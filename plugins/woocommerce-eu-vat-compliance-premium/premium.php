<?php

/*
Purpose: Provide Premium functionality that extends the base version

- Adds date selectors to the reports page
- Adds multi-currency display to the post page

TODO:
Instead of removing the 'Premium' tab, instead, add the update connector. Or at least, add it somewhere.

*/

if (!defined('WC_EU_VAT_COMPLIANCE_DIR')) die('No direct access');

class WC_EU_VAT_Compliance_Premium {

	public $settings;

	public function __construct() {
// 		add_filter('wc_eu_vat_compliance_show_vat_paid', array($this, 'wc_eu_vat_compliance_show_vat_paid'), 10, 2);
		add_action('admin_init', array($this, 'admin_init'));

		add_filter('wc_eu_vat_compliance_csv_message', array($this, 'wc_eu_vat_compliance_csv_message'));

		add_filter('wc_eu_vat_compliance_cc_tabs', array($this, 'wc_eu_vat_compliance_cc_tabs'));

		// WooCommerce subscriptions
		add_action('woocommerce_subscriptions_renewal_order_created', array($this, 'woocommerce_subscriptions_renewal_order_created'), 20, 2);

		add_filter('wc_euvat_compliance_exchange_settings', array($this, 'wc_euvat_compliance_exchange_settings'));
		add_filter('wpo_wcpdf_raw_order_totals', array($this, 'wpo_wcpdf_raw_order_totals'), 10, 3);

// TODO
// 		add_action( 'woocommerce_settings_tax_options_end', array($this, 'woocommerce_settings_tax_options_end'));
// 		add_action( 'woocommerce_update_options_tax', array( $this, 'woocommerce_update_options_tax'));
// 		add_option('woocommerce_eu_vat_compliance_force_eu_tax_display', $this->default_vat_matches);

		# TODO
		# TODO: Also they'll need some kind of GeoIP solution - the existing message about what's lacking without one needs updating to include this.
		$this->settings = array(
			array(
				'name' 		=> __( 'Force VAT display on product pages for EU visitors', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( "EU regulations require EU customers to be shown prices inclusive of VAT. Select this option to use geo-location (based upon the visitor's IP address) to enforce this requirement.", 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_force_eu_tax_display',
				'type' 		=> 'checkbox'
			)
		);

	}

	public function woocommerce_subscriptions_renewal_order_created($renewal_order, $original_order) {

		if (is_checkout()) return;

		# Calculate the VAT
		$record = WooCommerce_EU_VAT_Compliance('WC_EU_VAT_Compliance_Record_Order_Country');
		$record->record_meta_vat_paid($renewal_order->id);

		# Duplicate the country info from the original order
		$country_info = get_post_meta($original_order->id, 'vat_compliance_country_info', true);

		update_post_meta($renewal_order->id, 'vat_compliance_country_info', apply_filters('wc_eu_vat_compliance_meta_country_info', $country_info));

		# Save the current currency conversion info
		$record->record_conversion_rates($renewal_order->id);

		# Duplicate the VAT number info from the original order
		$vat_number = get_post_meta($original_order->id, 'VAT Number', true);
		$valid = get_post_meta($original_order->id, 'Valid EU VAT Number', true);
		$validated = get_post_meta($original_order->id, 'VAT number validated', true);

		if ($vat_number) {
			if ($valid) {
				update_post_meta( $renewal_order->id, 'VAT Number', $vat_number );
				update_post_meta( $renewal_order->id, 'Valid EU VAT Number', 'true' );
			} else {
				update_post_meta( $renewal_order->id, 'VAT Number', $vat_number );
				update_post_meta( $renewal_order->id, 'Valid EU VAT Number', 'false' );
			}
			if ( $validated ) {
				update_post_meta( $renewal_order->id, 'VAT number validated', 'true' );
			}
		}

	}

	public function wc_euvat_compliance_exchange_settings($settings) {
		$settings[] = array(
			'title'   => __( 'Add exchange amount to invoices', 'wc_eu_vat_compliance' ),
			'desc'    => __( 'If you have the free WooCommerce PDF packing slips and invoices plugin, then this option will add the foreign currency VAT amount to your PDF invoices (for supported PDF invoicing plugins).', 'wc_eu_vat_compliance' ),
			'id'      => 'woocommerce_eu_vat_compliance_add_to_wcinvoicing_pdfs',
			'default' => 'no',
			'type'    => 'checkbox'
		);
		return $settings;
	}

	public function wc_eu_vat_compliance_csv_message ($x) {
		return '<a href="'.add_query_arg('downloadcsv', '1').'">'.__('Download all orders with VAT data in CSV format', 'wc_eu_vat_compliance').'</a>';
	}

	public function woocommerce_settings_tax_options_end() {
		woocommerce_admin_fields($this->settings);
	}

// 	public function woocommerce_update_options_tax() {
// 		if ( isset( $_POST[''] ) ) woocommerce_update_options($this->settings);
// 	}

	public function wpo_wcpdf_raw_order_totals($totals, $order) {

		if ('yes' !== get_option('woocommerce_eu_vat_compliance_add_to_wcinvoicing_pdfs')) return $totals;

		$compliance = WooCommerce_EU_VAT_Compliance();

		// if (OPTION_TO_SHOW_ON_PDF_INVOICE)

		$record_currencies = apply_filters('wc_eu_vat_vat_recording_currencies', get_option('woocommerce_eu_vat_compliance_vat_recording_currency'));
		if (empty($record_currencies)) $record_currencies = array();
		if (!is_array($record_currencies)) $record_currencies = array($record_currencies);
		if (count($record_currencies) == 0) return $totals;

		$show_in_currency = array_shift($record_currencies);

		$order_currency = method_exists($order, 'get_order_currency') ? $order->get_order_currency() : get_option('woocommerce_currency');

		if ($show_in_currency == $order_currency) return $totals;

		$order_time = strtotime($order->order_date);

		$post_id = (isset($order->post)) ? $order->post->ID : $order->id;
		$conversion_rates = get_post_meta($post_id, 'wceuvat_conversion_rates', true);

		if (empty($conversion_rates) || empty($conversion_rates['rates'][$show_in_currency])) return $totals;

		$conversion_currencies = array_keys($conversion_rates['rates']);

		$rate = $conversion_rates['rates'][$show_in_currency];

		$vat_strings = $compliance->get_vat_matches('regex');

		$vat_paid = $compliance->get_vat_paid($order, true, true);
		if (!is_array($vat_paid)) return $totals;

		// TODO: byrates - ?

// 		$items = $compliance->get_amount_in_conversion_currencies($vat_paid['items_total'], $conversion_currencies, $conversion_rates, $order_currency);
// 		$shipping = $compliance->get_amount_in_conversion_currencies($vat_paid['shipping_total'], $conversion_currencies, $conversion_rates, $order_currency);

		$print_total = $compliance->get_amount_in_conversion_currencies($vat_paid['total'], $conversion_currencies, $conversion_rates, $order_currency, '');

// 		$amount = $tax['tax_amount'];
// 		$amount_in_shown_currency = round($amount / $rate, 2);

		$currency_symbol = htmlentities(get_woocommerce_currency_symbol($show_in_currency));

		foreach ($totals as $key => $total) {
			if (!is_array($total) || !isset($total['label']) || !preg_match($vat_strings, $total['label'])) continue;

			if (false === strpos($totals[$key]['value'], '</span>')) {
// 				$totals[$key]['value'] .= " (".$currency_symbol." $amount_in_shown_currency)";
				$totals[$key]['value'] = $print_total;
			} else {
// 				$totals[$key]['value'] = str_replace('</span>', " ".$currency_symbol." $amount_in_shown_currency) </span>", $total['value']);
				$totals[$key]['value'] = str_replace('</span>', $print_total." </span>", $total['value']);
			}

		}
		return $totals;
	}

	public function admin_init() {

		// admin.php?page=wc_eu_vat_compliance_cc
		// admin.php?page=woocommerce_reports&tab=sales&chart=eu_vat_report
		// admin.php?page=wc-reports&tab=orders&report=eu_vat_report
		global $pagenow;
 		if ('admin.php' != $pagenow || empty($_REQUEST['page']) || (!($_REQUEST['page'] == 'woocommerce_reports' && 'chart' == $_REQUEST['eu_vat_report']) && !($_REQUEST['page'] == 'wc-reports' && !empty($_REQUEST['report']) && 'eu_vat_report' == $_REQUEST['report']) && $_REQUEST['page'] != 'wc_eu_vat_compliance_cc')) {
			// Do nothing - this code was moved from elsewhere, and re-writing the logic is pointless.
 		} else {
			// CSV download
			if (!empty($_GET['downloadcsv'])) {
				header('Content-type: text/csv');
				header('Content-Disposition: attachment; filename="eu-vat-compliance-report-'.date('Y-m-d_His', 86400+current_time('timestamp')).'.csv"');
				if (false == ($csv = fopen('php://output', 'w'))) {
					echo "Failed to open write stream\n";
					exit;
				}
				$columns = array(
					'Order number',
					'Date',
					'Date (GMT)',
					'Order status',
					'Order currency',
					'Order currency symbol',
					'Base currency',
					'Base currency symbol',
					'Billing country',
					'Billing country (ISO-3166-1)',
					'Shipping country',
					'Shipping country (ISO-3166-1)',
					'IP address',
					'IP address country',
					'IP address country  (ISO-3166-1)',
					'IP address country detected via',
					'Sales total (order currency)',
					'VAT paid (order currency)',
					'VAT paid (items, order currency)',
					'VAT paid (shipping, order currency)',
					'VAT paid (base currency)',
					'VAT paid (items, base currency)',
					'VAT paid (shipping, base currency)',
					'VAT number',
					'VAT number valid',
					'VAT number validated',
					'Sales total (base currency)',
					'Currency conversion rate',
					'Country used to calculate VAT (name)',
					'Country used to calculate VAT (ISO-3166-1)',
				);

				$compliance =  WooCommerce_EU_VAT_Compliance();

				$text_statuses = $compliance->order_status_to_text(true);
				$base_currency = get_option('woocommerce_currency');
				$base_currency_symbol = html_entity_decode(get_woocommerce_currency_symbol($base_currency));
				$currency_symbols = array($base_currency => $base_currency_symbol);
				$country_labels = array();

				$all_countries = $compliance->wc->countries->countries;

				if (!fputcsv($csv, $columns)) {
					echo "Write failure when writing header\n";
					exit;
				}
				$results = WooCommerce_EU_VAT_Compliance('WC_EU_VAT_Compliance_Reports')->get_report_results('1970-01-01', date('Y-m-d', 86400+current_time('timestamp')), true, true);

				$data_sources = $compliance->data_sources;

				if (!is_array($results)) {
					echo "Failure when collecting results.";
					exit;
				}

				// Keys are ignored; included here only to for ease of coding
				foreach ($results as $status => $orders) {
					if (!is_array($orders)) continue;

					$status_text = isset($text_statuses['wc-'.$status]) ? $text_statuses['wc-'.$status] : __('Unknown', 'wc_eu_vat_compliance').' ('.$status.')';

					foreach ($orders as $order_id => $order) {
						if (!is_array($order)|| empty($order['vat_paid'])) continue;

						$order_currency = isset($order['_order_currency']) ? $order['_order_currency'] : $base_currency;
						if (!isset($currency_symbols[$order_currency])) $currency_symbols[$order_currency] = html_entity_decode(get_woocommerce_currency_symbol($order_currency));

						$billing_country = $order['_billing_country'];
						$shipping_country = $order['_shipping_country'];

						if (!isset($country_labels[$billing_country])) {
							$country_labels[$billing_country] = isset($all_countries[$billing_country]) ? $all_countries[$billing_country] : __('Unknown', 'wc_eu_vat_compliance');
						}

						if (!isset($country_labels[$shipping_country])) {
							$country_labels[$shipping_country] = isset($all_countries[$shipping_country]) ? $all_countries[$shipping_country] : __('Unknown', 'wc_eu_vat_compliance');
						}

						$taxable_country = empty($order['taxable_country']) ? '??' : $order['taxable_country'];

						if (!isset($country_labels[$taxable_country])) {
							$country_labels[$taxable_country] = isset($all_countries[$taxable_country]) ? $all_countries[$taxable_country] : __('Unknown', 'wc_eu_vat_compliance');
						}

						$source =  (isset($order['vat_compliance_country_info']['source'])) ? $order['vat_compliance_country_info']['source'] : '??';
						$source_description = (isset($data_sources[$source])) ? $data_sources[$source] : __('Unknown', 'wc_eu_vat_compliance');

						$ip = (isset($order['vat_compliance_country_info']['meta']['ip'])) ? $order['vat_compliance_country_info']['meta']['ip'] : $order['_customer_ip_address'];

						$ip_country = (isset($order['vat_compliance_country_info']['data'])) ? $order['vat_compliance_country_info']['data'] : '??';

						if (!isset($country_labels[$ip_country])) {
							$country_labels[$ip_country] = isset($all_countries[$ip_country]) ? $all_countries[$ip_country] : __('Unknown', 'wc_eu_vat_compliance');
						}

						$data = array(
							'Order number' => $order_id,
							'Date' => $order['date'],
							'Date (GMT)' => $order['date_gmt'],
							'Order status' => $status_text,
							'Order currency' => $order_currency,
							'Order currency symbol' => $currency_symbols[$order_currency],
							'Base currency' => $base_currency,
							'Base currency symbol' => $base_currency_symbol,
							'Billing country' => $country_labels[$billing_country],
							'Billing country (ISO-3166-1)' => $shipping_country,
							'Shipping country' => $country_labels[$shipping_country],
							'Shipping country (ISO-3166-1)' => $billing_country,
							'IP address' => $ip,
							'IP address country' => $country_labels[$ip_country],
							'IP address country  (ISO-3166-1)' => $ip_country,
							'IP address country detected via' => $source_description,
							'Sales total (order currency)' => $order['_order_total'],
							'VAT paid (order currency)' => $order['vat_paid']['total'],
							'VAT paid (items, order currency)' => $order['vat_paid']['items_total'],
							'VAT paid (shipping, order currency)' => $order['vat_paid']['shipping_total'],
							'VAT paid (base currency)' => '??',
							'VAT paid (items, base currency)' => '??',
							'VAT paid (shipping, base currency)' => '??',
							'VAT number' => empty($order['vatno']) ? __('None supplied', 'wc_eu_vat_compliance') : $order['vatno'],
							'VAT number valid' => empty($order['vatno_valid']) ? __('n/a', 'wc_eu_vat_compliance') : ($order['vatno_valid'] ? __('Yes', 'wc_eu_vat_compliance') : __('No', 'wc_eu_vat_compliance')),
							'VAT number validated' => empty($order['vatno_validated']) ? __('n/a', 'wc_eu_vat_compliance') : ($order['vatno_validated'] ? __('Yes', 'wc_eu_vat_compliance') : __('No', 'wc_eu_vat_compliance')),
							'Sales total (base currency)' => '??',
							'Currency conversion rate' => '??',
							'Country used to calculate VAT (name)' => $country_labels[$taxable_country],
							'Country used to calculate VAT (ISO-3166-1)' => $taxable_country
						);

						if ($order_currency == $base_currency) {
							$data['VAT paid (base currency)'] = $data['VAT paid (order currency)'];
							$data['VAT paid (items, base currency)'] = $data['VAT paid (items, order currency)'];
							$data['VAT paid (shipping, base currency)'] = $data['VAT paid (shipping, order currency)'];
							$data['Sales total (base currency)'] = $order['_order_total'];
							$data['Currency conversion rate'] = 1;
						} else {
							$data['VAT paid (base currency)'] = $order['vat_paid']['total_base_currency'];
							$data['VAT paid (items, base currency)'] = $order['vat_paid']['items_total_base_currency'];
							$data['VAT paid (shipping, base currency)'] = $order['vat_paid']['shipping_total_base_currency'];
							$data['Sales total (base currency)'] = $order['_order_total_base_currency'];
							$data['Currency conversion rate'] = ($order['_order_total_base_currency'] > 0) ? round($order['_order_total_base_currency']/$order['_order_total_base_currency'], 4) : '??';
						}

						fputcsv($csv, $data);
					}
				}

				fclose($csv);
				exit;
			}
		}

		if (isset($_GET['wceuv-nodeactivate'])) return;
		if (!function_exists('is_plugin_active')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
		if (basename(dirname(__FILE__)) != 'woocommerce-eu-vat-compliance') {
			if (is_plugin_active('woocommerce-eu-vat-compliance/eu-vat-compliance.php')) {
				deactivate_plugins('woocommerce-eu-vat-compliance/eu-vat-compliance.php');
				$redirect_uri = $_SERVER['REQUEST_URI'];
				$redirect_uri .= (false === strpos($redirect_uri, '?')) ? '?' : '&';
				$redirect_uri .= 'wceuv-nodeactivate=1';
				wp_redirect($redirect_uri);
				exit;
				// Do nothing more this time to avoid duplication
				return;
			} elseif (is_dir(WP_PLUGIN_DIR.'/woocommerce-eu-vat-compliance') && current_user_can('delete_plugins')) {
				global $pagenow;
				# Exists, but not active - nag them
				if ('admin.php' == $pagenow && (!empty($_REQUEST['page']) && ('wc-settings' == $_REQUEST['page'] || 'woocommerce_settings' == $_REQUEST['page'])) || 'plugins.php' == $pagenow) add_action('all_admin_notices', array($this, 'deinstall_freeversion'));
			}
		}
	}

	public function wc_eu_vat_compliance_cc_tabs($tabs) {
		if (is_array($tabs)) unset($tabs['premium']);
		return $tabs;
	}

	public function deinstall_freeversion() {
		$del = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=woocommerce-eu-vat-compliance/eu-vat-compliance.php&amp;plugin_status=all&amp;paged=1&amp;s=', 'bulk-plugins') . '" title="' . esc_attr__('Delete plugin') . '" class="delete">' . __('You can delete the free version of the WooCommerce EU VAT Compliance plugin', 'wc_eu_vat_compliance') . '</a>';
		$this->show_admin_warning($del.' - '.__('all of its features are part of the premium version.', 'wc_eu_vat_compliance'));
	}

	private function show_admin_warning($message, $class = "updated") {
		echo '<div class="updraftmessage '.$class.'">'."<p>$message</p></div>";
	}

// 	public function wc_eu_vat_compliance_show_vat_paid($paid, $vat_paid) {
// 		if (is_array($vat_paid) && $vat_paid['base_currency'] != $vat_paid['currency']) {
// 			$paid .= ' ('.get_woocommerce_currency_symbol($vat_paid['base_currency']).' '.sprintf('%.02f', $vat_paid['total_base_currency']).')';
// 		}
// 		return $paid;
// 	}

}
