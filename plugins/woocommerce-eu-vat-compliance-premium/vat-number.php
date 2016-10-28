<?php

/*
This class:
* Takes the customer's EU VAT number, if they are registered, and validates it
* If it validates, then they are exempted from VAT, depending on the shop owner's settings
* If the customer's IP address country and their shipping tax country mis-match, then they are (optionally) asked to self-certify their country (or, optionally, asked anyway, or never asked)

Some of the code in this class was licensed under the GNU GPL from WooThemes
*/

if (!defined('WC_EU_VAT_COMPLIANCE_DIR')) die('No direct access');

// Could use the VIES service directly: https://github.com/herdani/vat-validation/

if (class_exists('WC_EU_VAT_Compliance_VAT_Number')) return;
class WC_EU_VAT_Compliance_VAT_Number {

	public $checkout_title;
	public $checkout_message;
	public $vat_number               = '';
	public $is_eu_vat_number         = false;
	public $validated                = false;
	public $country;
	public $validation_api_url       = 'http://isvat.appspot.com/';
//  	public $validation_api_url       = 'http://woo-vat-validator.herokuapp.com/v1/validate/';
	public $european_union_countries; //= array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'IM', 'MC' );

	public $settings;

	/**
		* __construct function.
		*
		* @access public
		* @return void
		*/
	public function __construct() {

		$this->checkout_title   = get_option( 'woocommerce_eu_vat_compliance_checkout_title' );
		$this->checkout_message = get_option( 'woocommerce_eu_vat_compliance_checkout_message' );

		// Init settings
		$this->settings = array(
			array(
				'name' 		=> __( 'Invoice footer text (B2B)', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( "Text to prepend to the footer of your PDF invoice for transactions with validated VAT numbers (for supported PDF invoicing plugins). If you include {vat_number}, then it will be replaced with the validated number.", 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_pdf_footer_b2b',
				'type' 		=> 'textarea',
				'css'		=> 'width:100%; height: 100px;'
			),

			array(
				'name' 		=> __( 'Allow VAT numbers to be entered', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'N.B. Some of the options further down the page are irrelevant, if you choose "Never" here.', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_allow_exemption',
				'type' 		=> 'radio',
				'options'		=> array(
					'permit' => __('Permit: customers with valid EU VAT numbers can enter them, and become VAT-exempt.'),
					'never' => __('Never: VAT will always be charged whenever VAT-able items are present.', 'wc_eu_vat_compliance'),
					'require' => __('Require: customers without a valid EU VAT number will not be permitted to check out on any order.'),
				),
				'default' => 'permit'
			),

			array(
				'name' 		=> __( 'EU VAT Title', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'The title that appears at checkout above the VAT Number box.', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_checkout_title',
				'type' 		=> 'text'
			),
			array(
				'name' 		=> __( 'EU VAT Message', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'The message that appears at checkout above the VAT Number box.', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_checkout_message',
				'type' 		=> 'textarea',
				'css'		=> 'width:100%; height: 100px;'
			),
			array(
				'name' 		=> __( 'Show field for base country', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'Show the VAT field even when the customer is in your base country', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_show_in_base',
				'type' 		=> 'checkbox'
			),
			array(
				'name' 		=> __( 'Deduct VAT for base country', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'Deduct the VAT even when the customer is in your base country and has a valid number', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_deduct_in_base',
				'type' 		=> 'checkbox'
			),
			array(
				'name' 		=> __( 'Store non-valid numbers', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( 'Enable this option to store numbers which don\'t pass validation, rather than reject them. Tax will not be made exempt.', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_store_invalid_numbers',
				'type' 		=> 'checkbox'
			),
			array(
				'name' 		=> __( 'Resolving conflicting VAT data', 'wc_eu_vat_compliance' ),
				'desc' 		=> __( "This option determines what will happen if the customer's apparent country (according to their IP address) conflicts with the country used to determine their taxes (i.e. shipping or billing country, depending upon the setting further up this page).", "wc_eu_vat_compliance").' '.__('Which is the correct option for you depends upon the particular evidence your local taxman is happy with to verify the country of purchase.', 'wc_eu_vat_compliance' ),
				'id' 		=> 'woocommerce_eu_vat_compliance_conflict_resolution',
				'type' 		=> 'radio',
				'options'		=> array(
					'sometimesask' => __('Require the customer to indicate the correct country when there is a conflict', 'wc_eu_vat_compliance'),
					'alwaysask' => __('Always show the customer the VAT country, even if there was no conflict', 'wc_eu_vat_compliance'),
					'neverask' => __('Always use the WooCommerce tax settings to indicate the country (never ask the customer)', 'wc_eu_vat_compliance'),
				),
				'default' => 'sometimesask'
			),
		);

		// Default options
		add_option( 'woocommerce_eu_vat_compliance_checkout_title', __( "EU VAT Number (if any)", 'wc_eu_vat_compliance' ) );
		add_option( 'woocommerce_eu_vat_compliance_checkout_message', __( "If you are in the EU and have a valid EU VAT registration number please enter it below.", 'wc_eu_vat_compliance' ) );
		add_option( 'woocommerce_eu_vat_compliance_show_in_base', 'yes' );
		add_option( 'woocommerce_eu_vat_compliance_deduct_in_base', 'no' );
		add_option( 'woocommerce_eu_vat_compliance_store_invalid_numbers', 'no' );
		add_option( 'woocommerce_eu_vat_compliance_conflict_resolution', 'sometimesask' );
		add_option('woocommerce_eu_vat_compliance_allow_exemption', 'permit');

		// Admin
		add_action( 'woocommerce_settings_tax_options_end', array( $this, 'admin_settings' ) );
		add_action( 'woocommerce_update_options_tax', array( $this, 'save_admin_settings' ) );
		// Actions/Filters
		add_action( 'woocommerce_after_order_notes', array( $this, 'vat_number_field' ) );

		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'ajax_update_checkout_totals' ) ); // Check during ajax update totals
		add_action( 'woocommerce_checkout_process', array( $this, 'process_checkout' ) ); // Check during checkout process

		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'update_user_meta' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );

		add_action('woocommerce_init', array($this, 'woocommerce_init'), 11);

		// PDF invoice integration
		if (!defined('WC_EU_VAT_Compliance_Disable_Append_To_Billing') || !WC_EU_VAT_Compliance_Disable_Append_To_Billing) {
			add_action('woocommerce_order_formatted_billing_address', array($this, 'woocommerce_order_formatted_billing_address'), 10, 2);
			add_action('woocommerce_formatted_address_replacements', array($this, 'woocommerce_formatted_address_replacements'), 10, 2);
			add_action('woocommerce_localisation_address_formats', array($this, 'woocommerce_localisation_address_formats'), 10, 1);
			add_action('wpo_wcpdf_billing_address', array($this, 'wpo_wcpdf_billing_address'));
		}

		add_filter('wc_euvat_compliance_wpo_wcpdf_footer', array($this, 'wc_euvat_compliance_wpo_wcpdf_footer'), 10, 8);

		// Email meta
		add_filter('woocommerce_email_order_meta_keys', array( $this, 'order_meta_keys' ) );

		global $pagenow;
		if ('admin.php' == $pagenow && !empty($_REQUEST['page']) && ('woocommerce_settings' == $_REQUEST['page'] || 'wc-settings' == $_REQUEST['page']) && !empty($_REQUEST['tab']) && 'tax' == $_REQUEST['tab'] && empty($_REQUEST['section'])) {
			add_action('admin_footer', array($this, 'admin_footer'));
		}

	}

	public function wc_euvat_compliance_wpo_wcpdf_footer($footer, $orig_footer, $text, $vat_paid, $vat_number, $valid_eu_vat_number, $vat_number_validated, $order) {

		if (!is_array($vat_paid) || !isset($vat_paid['total'])) return $footer;

		$add = get_option('woocommerce_eu_vat_compliance_pdf_footer_b2b');
		if (empty($add)) $add = '';

		if ($vat_number && $valid_eu_vat_number && $vat_number_validated && empty ($vat_paid['total'])) {
			$new_footer = wpautop( wptexturize( str_replace('{vat_number}', $vat_number, $add) ) ) . $orig_footer;
			return $new_footer;
		}

		return $footer;
	}

	public function woocommerce_init() {
		$this->european_union_countries = WooCommerce_EU_VAT_Compliance()->get_european_union_vat_countries();
	}

	// These functions, which add the VAT # to the billing address, are based on the work of Diego Zanella
	public function woocommerce_formatted_address_replacements($replacements, $values) {
		$vat_number = (!empty($values['vat_number'])) ? $values['vat_number'] : '';
		if (empty($vat_number)) {
			$replacements['{vat_number}'] = '';
		} else {
			$replacements['{vat_number}'] = __('VAT #:', 'wc_eu_vat_compliance') . ' ' . $vat_number;
		}
		return $replacements;
	}

	// Alters the address formats and adds new tokens, such as the VAT number.
	public function woocommerce_localisation_address_formats($formats) {
		foreach($formats as $format_idx => $address_format) {
			$formats[$format_idx] .= "\n{vat_number}";
		}
		return $formats;
	}

	public function woocommerce_order_formatted_billing_address($address_parts, $order) {
		$post_id = (isset($order->post)) ? $order->post->ID : $order->id;
		$valid_eu_vat_number = get_post_meta($post_id, 'Valid EU VAT Number', true);
		$vat_number_validated = get_post_meta($post_id, 'VAT number validated', true);
		$vat_number = get_post_meta($post_id, 'VAT Number', true);
		if ($valid_eu_vat_number && $vat_number) {
			$address_parts['vat_number'] = $vat_number;
			// Probably not best to state "validated", as the position of the VAT # by the address may be taken to mean that it's been validated that this VAT number belongs to *this* address/entity.
// 			if ($vat_number_validated) $address_parts['vat_number'] .= ' ('.__('validated', 'wc_eu_vat_compliance').')';
// 		} else {
// 			$address_parts['vat_number'] = __('none', 'wc_eu_vat_compliance');
		}
		return $address_parts;
	}

	public function wpo_wcpdf_billing_address($formatted_billing_address) {
		$vat_number_label = __('VAT #:', 'wc_eu_vat_compliance');
		$formatted_billing_address = str_replace(
			$vat_number_label,
			'<span class="vat_number_label">'.$vat_number_label.'</span>',
			$formatted_billing_address
		);
		return $formatted_billing_address;
	}

	// Before WC 2.2
	public function woocommerce_form_field_radio($field, $key, $args, $value) {
		if (empty($args['id'])) $args['id'] = $key;
		$required = '';

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required  . '</label>';

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $option_key => $option_text ) {
				$field .= '<input type="radio" class="input-radio" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
				$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label>';
			}
		}
		return $field;
	}

	// Only fires on the correct page
	public function admin_footer() {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#woocommerce_tax_based_on').after('<br><em><?php echo esc_attr(__('N.B. The final country used may be modified according to your EU VAT settings, further down this page.', 'wc_eu_vat_compliance'));?></em>');
				});
			</script>
		<?php
	}

	/*-----------------------------------------------------------------------------------*/
	/* Class Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
		* admin_settings function.
		*
		* @access public
		* @return void
		*/
	public function admin_settings() {
		woocommerce_admin_fields( $this->settings );
	}

	/**
		* save_admin_settings function.
		*
		* @access public
		* @return void
		*/
	public function save_admin_settings() {
		if ( isset( $_POST['woocommerce_eu_vat_compliance_conflict_resolution'] ) ) woocommerce_update_options( $this->settings );
	}



	/**
		* vat_number_field function.
		*
		* @access public
		* @param mixed $woocommerce_checkout
		* @return void
		*/
	public function vat_number_field( $woocommerce_checkout ) {

		$compliance = WooCommerce_EU_VAT_Compliance();

		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ('shipping' == $tax_based_on && !$compliance->wc->cart->needs_shipping()) $tax_based_on = 'billing';

		echo <<<ENDHERE
<style type="text/css">
#vat_self_certify_field label { float: left; margin-left: 4px; }
#vat_self_certify_field input { float: left; clear: left; }
</style>
ENDHERE;

		echo '<div id="woocommerce_eu_vat_compliance">';
		echo '<div id="woocommerce_eu_vat_compliance_vat_number">';
		echo '<h3>' . wptexturize( __( $this->checkout_title, 'wc_eu_vat_compliance' ) ) . '</h3>';
		echo wpautop( '<small>' . wptexturize( __( $this->checkout_message, 'wc_eu_vat_compliance' ) ) . '</small>');

		$exemption_setting = $this->exemption_setting();

		if ('never' == $exemption_setting) {
			echo apply_filters('wc_eu_vat_nob2b_message', '<p class="form-row" id="vat_number">'.__('This shop does not allow the entry of EU VAT numbers for VAT exemption.', 'wc_eu_vat_compliance').'</p>');
		} else {
			woocommerce_form_field( 'vat_number', array(
				'type' 		=> 'text',
				'class' 		=> array( 'vat-number update_totals_on_change address-field form-row-wide' ),
				'label' 		=> __( 'VAT Number', 'wc_eu_vat_compliance' ),
				'placeholder' 	=> __( 'VAT Number', 'wc_eu_vat_compliance' ),
			) );
		}

		// close div#woocommerce_eu_vat_compliance_vat_number
		echo '</div>';

		echo '<div id="woocommerce_eu_vat_compliance_self_certify"';

		$conflict_mode = get_option('woocommerce_eu_vat_compliance_conflict_resolution', 'sometimesask');

		$country_info = $compliance->get_visitor_country_info();
		$country_code = (empty($country_info['data'])) ? '??' : $country_info['data'];
		$countries = $compliance->wc->countries->countries;
		$country_name = isset($countries[$country_code]) ? $countries[$country_code] : __('Unknown', 'wc_eu_vat_compliance');

		if ($conflict_mode == 'alwaysask' || $conflict_mode == 'sometimesask') {

			// Options: 1) Always show 2) Never show (rely no Woo) 3) Show only in case of conflict

			$opts_array = array(
				'accessing' => apply_filters('wc_eu_vat_certify_apparent_access_country', sprintf(__('%s (detected)', 'wc_eu_vat_compliance'), $country_name), $country_name)
			);

			switch ( $tax_based_on ) {
				case 'billing' :
				$opts_array['billing'] = __('Billing country', 'wc_eu_vat_compliance');
				break;
				case 'shipping' :
				$opts_array['shipping'] = __('Shipping country', 'wc_eu_vat_compliance');
				break;
			}

			$certify_msg = apply_filters('wc_eu_vat_certify_message', __('For the correct calculation of VAT, please confirm your country of residence:', 'wc_eu_vat_compliance'));

			// Default needs to be $tax_based_on
			if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2', '<')) {
				add_filter('woocommerce_form_field_radio', array($this, 'woocommerce_form_field_radio'), 10, 4);
			}

			woocommerce_form_field( 'vat_self_certify', apply_filters('wc_eu_vat_certify_form_field', array(
				'type'	=> 'radio',
				'class'	=> array( 'update_totals_on_change address-field form-row-wide' ),
				'label'	=> $certify_msg,
				'placeholder'	=> $certify_msg,
				'options'	=> $opts_array,
				'default' => $tax_based_on
			)) );

		}

		# Close div#woocommerce_eu_vat_compliance_self_certify
		echo '</div>';

		echo '</div>';

		$inline_js = "";

		if ( get_option( 'woocommerce_eu_vat_compliance_show_in_base' ) == 'yes' ) {
			$check_countries = "var check_countries = new Array(\"" . implode( '","', $this->european_union_countries ) . "\");";
		} else {
			$check_countries = "var check_countries = new Array(\"".implode( '","', array_diff( $this->european_union_countries, array( $compliance->wc->countries->get_base_country() ) ) )."\");";
		}

		$inline_js_base = "var wc_eu_vat_ip_country_code = '$country_code';\nvar wc_eu_vat_conflict_mode = '$conflict_mode';
			" . $check_countries . "

			function check_vat_number_display() {
				var showit = true;
				// Get the country to check against
				var effective_vat_country = get_effective_vat_country();
				if (effective_vat_country && jQuery.inArray( effective_vat_country, check_countries ) >= 0) {
					jQuery('#woocommerce_eu_vat_compliance_vat_number').fadeIn();
				} else {
					jQuery('#woocommerce_eu_vat_compliance_vat_number').fadeOut();
					jQuery('#woocommerce_eu_vat_compliance_vat_number input').val('');
				}
			}

			jQuery('#woocommerce_eu_vat_compliance input:radio').change(function() {
				check_vat_number_display();
				jQuery('body').trigger('update_checkout');
			});

		";

		$show_condition = ($conflict_mode == 'sometimesask') ? 'wc_eu_vat_ip_country_code != country' : '1 == 1';

		switch ( $tax_based_on ) {
			case 'billing' :
				$inline_js = "
					function get_effective_vat_country() {
						var country = jQuery('select#billing_country').val();
						if ('neverask' == wc_eu_vat_conflict_mode || wc_eu_vat_ip_country_code == country) {return country;}
						// User is asked to resolve the conflict
						var how_to_resolve = jQuery('#woocommerce_eu_vat_compliance_self_certify input[name=vat_self_certify]:checked').val();
						if ('accessing' == how_to_resolve) {
							return wc_eu_vat_ip_country_code;
						} else if ('none' == how_to_resolve) {
							return country;
						} else if ('billing' == how_to_resolve || 'shipping' == how_to_resolve) {
							return country;
						} else {
							return country;
						}
					}

					function check_self_compliance_display() {
						var country = jQuery('select#billing_country').val();
						var country_name = jQuery('select#billing_country option:selected').text();

						// Display if <alwaysask> or (<sometimes> and <country conflicts>)

						if ('alwaysask' == wc_eu_vat_conflict_mode || 'sometimesask' == wc_eu_vat_conflict_mode) {
							jQuery('label[for=\"vat_self_certify_billing\"]').html(country_name+' (".__('billing country', 'wc_eu_vat_compliance').")');
							if ('alwaysask' == wc_eu_vat_conflict_mode || (wc_eu_vat_ip_country_code != country)) {
								jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', false);
								jQuery('#woocommerce_eu_vat_compliance_self_certify').show();
								if (wc_eu_vat_ip_country_code == country) {
									jQuery('label[for=\"vat_self_certify_accessing\"]').hide();
									jQuery('#vat_self_certify_accessing').hide();
									if (jQuery('#vat_self_certify_accessing').prop('checked')) {
										jQuery('#vat_self_certify_billing').attr('checked', 'checked');
									}
								} else {
									jQuery('label[for=\"vat_self_certify_accessing\"]').show();
									jQuery('#vat_self_certify_accessing').show();
								}
							} else {
								jQuery('#woocommerce_eu_vat_compliance_self_certify').hide();
								jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', true);
							}
						} else {
							jQuery('#woocommerce_eu_vat_compliance_self_certify').hide();
							jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', true);
						}
					}

					jQuery('form.checkout, form#order_review').on('change', 'select#billing_country', function() {

						// Should the VAT number box be shown?
						check_vat_number_display();

						// Should the self-certification box be shown?
						check_self_compliance_display();

					});
					jQuery('select#billing_country').change();
				";
			break;

			case 'shipping' :
				// N.B. The ship-to-different / ship-to-billing change on different WC versions
				$inline_js = "

					function get_effective_vat_country() {
						if ( jQuery( '#ship-to-different-address input' ).is( ':checked' ) || (jQuery( '#shiptobilling input' ).length > 0 && ! jQuery( '#shiptobilling input' ).is( ':checked' ))) {
							var country = jQuery('select#shipping_country').val();
						} else {
							var country = jQuery('select#billing_country').val();
						}
						if ('neverask' == wc_eu_vat_conflict_mode || wc_eu_vat_ip_country_code == country) {return country;}
						// User is asked to resolve the conflict
						var how_to_resolve = jQuery('#woocommerce_eu_vat_compliance_self_certify input[name=vat_self_certify]:checked').val();
						if ('accessing' == how_to_resolve) {
							return wc_eu_vat_ip_country_code;
						} else if ('none' == how_to_resolve) {
							return country;
						} else if ('billing' == how_to_resolve || 'shipping' == how_to_resolve) {
							return country;
						} else {
							return country;
						}
					}

					function check_self_compliance_display() {

						if ( jQuery( '#ship-to-different-address input' ).is( ':checked' ) || (jQuery( '#shiptobilling input' ).length > 0 && ! jQuery( '#shiptobilling input' ).is( ':checked' ))) {
							var country = jQuery('select#shipping_country').val();
							var country_name = jQuery('select#shipping_country option:selected').text();
						} else {
							var country = jQuery('select#billing_country').val();
							var country_name = jQuery('select#billing_country option:selected').text();
						}

						// Display if <alwaysask> or (<sometimes> and <country conflicts>)

						if ('alwaysask' == wc_eu_vat_conflict_mode || 'sometimesask' == wc_eu_vat_conflict_mode) {
							jQuery('label[for=\"vat_self_certify_shipping\"]').html(country_name+' (".__('shipping country', 'wc_eu_vat_compliance').")');
							if ('alwaysask' == wc_eu_vat_conflict_mode || (wc_eu_vat_ip_country_code != country)) {
								jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', false);
								jQuery('#woocommerce_eu_vat_compliance_self_certify').show();
								if (wc_eu_vat_ip_country_code == country) {
									jQuery('label[for=\"vat_self_certify_accessing\"]').hide();
									jQuery('#vat_self_certify_accessing').hide();
									if (jQuery('#vat_self_certify_accessing').prop('checked')) {
										jQuery('#vat_self_certify_shipping').attr('checked', 'checked');
									}
								} else {
									jQuery('label[for=\"vat_self_certify_accessing\"]').show();
									jQuery('#vat_self_certify_accessing').show();
								}
							} else {
								jQuery('#woocommerce_eu_vat_compliance_self_certify').hide();
								jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', true);
							}
						} else {
							jQuery('#woocommerce_eu_vat_compliance_self_certify').hide();
							jQuery('#woocommerce_eu_vat_compliance_self_certify input[type=\"radio\"]').prop('disabled', true);
						}
					}

					jQuery('form.checkout, form#order_review').on('change', 'select#billing_country, select#shipping_country, input#ship-to-different-address-checkbox, input#shiptobilling-checkbox', function() {
						// Should the VAT number box be shown?
						check_vat_number_display();

						// Should the self-certification box be shown?
						check_self_compliance_display();
					});
					jQuery('select#billing_country').change();
				";
			break;
		}

		if ( $inline_js ) {
			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( $inline_js_base.$inline_js );
			} else {
				$compliance->wc->add_inline_js( $inline_js_base.$inline_js );
			}
		}
	}

	public function exemption_setting() {
		$setting = get_option('woocommerce_eu_vat_compliance_allow_exemption', 'permit');
		return ('never' == $setting || 'require' == $setting) ? $setting : 'permit';
	}

	/**
		* ajax_update_checkout_totals function.
		*
		* @access public
		* @param mixed $form_data
		* @return void
		*/
	public function ajax_update_checkout_totals( $form_data ) {
		
		$compliance = WooCommerce_EU_VAT_Compliance();

		parse_str( $form_data );

		if ( empty( $billing_country ) && empty( $shipping_country ) ) {
			return;
		}
		if (empty($billing_state)) $billing_state = '';
		if (empty($shipping_state)) $shipping_state = '';
		if (empty($billing_country)) $billing_country = '';
		if (empty($shipping_country)) $shipping_country = '';

		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ('shipping' == $tax_based_on && !$compliance->wc->cart->needs_shipping()) $tax_based_on = 'billing';

		if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.1', '<')) {
			if ('shipping' == $tax_based_on && !empty($shiptobilling)) $tax_based_on = 'billing';
		} else {
			if ('shipping' == $tax_based_on && empty($ship_to_different_address)) $tax_based_on = 'billing';
		}

		switch ( $tax_based_on ) {
			case 'billing' :
			case 'base' :
				$country = ! empty( $billing_country ) ? $billing_country : '';
				$state = ! empty( $billing_state ) ? $billing_state : '';
			break;
			case 'shipping' :
				$country = ! empty( $shipping_country ) ? $shipping_country : $billing_country;
				$state = ! empty( $shipping_state ) ? $shipping_state : '';
			break;
		}

		$country_info = $compliance->get_visitor_country_info();
		$wc_eu_vat_ip_country_code = (empty($country_info['data'])) ? '??' : $country_info['data'];

		// Now over-ride, if set
		if (!empty($vat_self_certify)) {
			if ('shipping' == $vat_self_certify && 'billing' == $tax_based_on) {
				// Case where no separate shipping address has been set; this has already been handled, above
			} else {
				list($changed, $country, $state) = $this->detect_self_certify_override($vat_self_certify, $country, $wc_eu_vat_ip_country_code, $billing_country, $shipping_country, $state, $billing_state, $shipping_state);
				if ($changed) {
	// 				WooCommerce_EU_VAT_Compliance()->wc->customer->set_country( $country );
	// 				$_POST['country'] = $country;
				}
			}
		}

		$compliance->wc->session->set('eu_vat_country', $country);
		$compliance->wc->session->set('eu_vat_state', $state);

		$exemption_setting = $this->exemption_setting();

		if ('never' == $exemption_setting) $vat_number = null;

		if ( ! empty( $vat_number ) && ! empty( $country ) ) {
			// The country here has already been over-ridden, if necessary
			$this->country 		= sanitize_text_field( $country );
			$this->vat_number 	= sanitize_text_field( $vat_number );

			if ( $this->check_vat_number_validity() ) {

				// Check base and billing is in the EU
				if ( in_array( $compliance->wc->countries->get_base_country(), $this->european_union_countries ) && in_array( $this->country, $this->european_union_countries ) ) {

					if ( $compliance->wc->countries->get_base_country() == $this->country && get_option( 'woocommerce_eu_vat_compliance_deduct_in_base' ) == 'yes' ) {

						$compliance->wc->customer->set_is_vat_exempt( true );
						return;

					} elseif ( $compliance->wc->countries->get_base_country() != $this->country ) {

						$compliance->wc->customer->set_is_vat_exempt( true );
						return;

					}

				}

			}

		}

		$compliance->wc->customer->set_is_vat_exempt( false );

	}

	private function detect_self_certify_override($vat_self_certify, $country, $wc_eu_vat_ip_country_code, $billing_country, $shipping_country, $state = '', $billing_state = '', $shipping_state = '') {
		$changed = false;
		if ('shipping' == $vat_self_certify && !empty($shipping_country)) {
			$country = $shipping_country;
			$state = $shipping_state;
			$changed = true;
		} elseif ('billing' ==  $vat_self_certify && !empty($billing_country)) {
			$country = $billing_country;
			$changed = true;
		} elseif ('accessing' == $vat_self_certify && !empty($wc_eu_vat_ip_country_code)) {
			$country = $wc_eu_vat_ip_country_code;
			$changed = true;
		}

		return array($changed, $country, $state);
	}

	private function get_customer_vat_country($country) {
		$new_country = WooCommerce_EU_VAT_Compliance()->wc->session->get('eu_vat_country');
		return (empty($new_country)) ? $country : $new_country;
	}

	/**
		* process_checkout function.
		*
		* @access public
		* @return void
		*/
	public function process_checkout() {

		$compliance = WooCommerce_EU_VAT_Compliance();

		$exemption_setting = $this->exemption_setting();

		// get_country() may not return the actual country we wish to use for VAT
		$this->country 		= $this->get_customer_vat_country($compliance->wc->customer->get_country());
		$this->vat_number 	= (isset( $_POST['vat_number'] ) && 'never' != $exemption_setting) ? sanitize_text_field( $_POST['vat_number'] ) : '';

		if ( $this->check_vat_number_validity( true ) ) {

			// Check base and billing is in the EU
			if ( in_array( $compliance->wc->countries->get_base_country(), $this->european_union_countries ) && in_array( $this->country, $this->european_union_countries ) ) {

				if ( $compliance->wc->countries->get_base_country() == $this->country && get_option( 'woocommerce_eu_vat_compliance_deduct_in_base' ) == 'yes' ) {

					$compliance->wc->customer->set_is_vat_exempt( true );
					return;

				} elseif ( $compliance->wc->countries->get_base_country() != $this->country ) {

					$compliance->wc->customer->set_is_vat_exempt( true );
					return;

				}
			}
		}

		// Require valid EU VAT number
		if ( in_array( $this->country, $this->european_union_countries ) && 'require' == $exemption_setting && !$this->is_eu_vat_number && !$this->error_has_been_shown) {
			$error = apply_filters('wc_eu_vat_certify_vat_no_required', __( 'This store requires all orders to be accompanied by a valid EU VAT number', 'wc_eu_vat_compliance' ));
			$this->error_has_been_shown = true;
			$compliance->add_wc_error($error);
		}

		if (!$this->error_has_been_shown && 'neverask' != get_option('woocommerce_eu_vat_compliance_conflict_resolution', 'sometimesask') && !empty($_POST['vat_self_certify']) && 'none' == $_POST['vat_self_certify']) {
			$error = apply_filters('wc_eu_vat_certify_must_certify', __( 'You must certify your country of residence in order to complete your order.', 'wc_eu_vat_compliance' ));
			$this->error_has_been_shown = true;
			$compliance->add_wc_error($error);
		}

	}

	/**
		* check_vat_number_validity function.
		*
		* @access public
		* @return void
		*/
	public function check_vat_number_validity( $on_checkout = false ) {

		$this->is_eu_vat_number = false;
		$this->validated        = false;
		$this->error_has_been_shown = false;
		$compliance = WooCommerce_EU_VAT_Compliance();

		// Check vars
		if ( ! $this->country || ! $this->vat_number ) {
			return false;
		}

		// Check country
		if ( ! in_array( $this->country, $this->european_union_countries ) ) {
			$error = sprintf( __( 'You cannot use a VAT number since your country (%s) is outside of the EU.', 'wc_eu_vat_compliance' ), $this->country );

			$this->error_has_been_shown = true;
			$compliance->add_wc_error($error);

			$this->vat_number = '';
			return false;
		}

		// Format the number
		$this->vat_number = strtoupper( str_replace( array( ' ', '-', '_', '.' ), '', $this->vat_number ) );

		// Remove country code if set at the begining
		$first_chars = substr( $this->vat_number, 0, 2 );

		if ( in_array( $first_chars, array_merge( $this->european_union_countries, array( 'EL' ) ) ) )
			$this->vat_number = substr( $this->vat_number, 2 );

		if ( ! $this->vat_number )
			return false;

		$vat_prefix = $this->get_vat_number_prefix( $this->country );

		// Check cache
		$cached_result = get_transient( 'vat_number_' . $vat_prefix . $this->vat_number );

		if ( false === $cached_result ) {

			$response = wp_remote_get( $this->validation_api_url . $vat_prefix . '/' . $this->vat_number . '/' );

			if ( is_wp_error( $response ) || empty( $response['body'] ) ) {

				$this->is_eu_vat_number = true;
				$this->validated        = false;

				// There was an error with the API so let the number pass to prevent the order being cancelled
				return true;

			} else {

				if ( $response['body'] == "true" ) {

					$this->is_eu_vat_number = true;
					$this->validated        = true;

					set_transient( 'vat_number_' . $vat_prefix . $this->vat_number, 1, 7 * DAY_IN_SECONDS );

					return true;

				} elseif ( strstr( $response['body'], 'SERVER_BUSY' ) ) {

					$this->is_eu_vat_number = true;
					$this->validated        = false;

					// The API was busy - let it through
					return true;

				} else {

					if ( get_option( 'woocommerce_eu_vat_compliance_store_invalid_numbers' ) != 'yes' ) {

						$error = sprintf( __( 'You have entered an invalid VAT number (%s) for your country.', 'wc_eu_vat_compliance' ), $this->vat_number );

						if ( $on_checkout ) {
							$this->error_has_been_shown = true;
							$compliance->add_wc_error($error);
						}

					}

					set_transient( 'vat_number_' . $vat_prefix . $this->vat_number, 0, 7 * DAY_IN_SECONDS );

					return false;
				}
			}

		} elseif ( $cached_result ) {

			$this->is_eu_vat_number = true;
			$this->validated        = true;

			return true;

		} else {

			if ( get_option( 'woocommerce_eu_vat_compliance_store_invalid_numbers' ) != 'yes' ) {

				$error = sprintf( __( 'You have entered an invalid VAT number (%s) for your country.', 'wc_eu_vat_compliance' ), $this->vat_number );

				if ( $on_checkout ) {
					$this->error_has_been_shown = true;
					$compliance->add_wc_error($error);
				}

			}

			return false;
		}
	}

	/**
		* update_user_meta function.
		*
		* @access public
		* @param mixed $user_id
		* @return void
		*/
	public function update_user_meta( $user_id ) {
		if ( $this->vat_number ) {
			update_user_meta( $user_id, $this->vat_number, true );
		}
	}

	/**
		* update_order_meta function.
		*
		* @access public
		* @param mixed $order_id
		* @return void
		*/
	public function update_order_meta( $order_id ) {
		if ( $this->vat_number ) {
			if ( $this->is_eu_vat_number ) {
				update_post_meta( $order_id, 'VAT Number', $this->get_vat_number_prefix( $this->country ) . ' ' . $this->vat_number );
				update_post_meta( $order_id, 'Valid EU VAT Number', 'true' );
			} else {
				update_post_meta( $order_id, 'VAT Number', $this->vat_number );
				update_post_meta( $order_id, 'Valid EU VAT Number', 'false' );
			}
			if ( $this->validated ) {
				update_post_meta( $order_id, 'VAT number validated', 'true' );
			}
		}
	}

	/**
		* Return the vat number prefix
		*
		* @param  string $country
		* @return string
		*/
	public function get_vat_number_prefix( $country ) {
		$vat_prefix = $country;

		// Deal with exceptions
		switch ( $country ) {
			case 'GR' :
				$vat_prefix = 'EL';
			break;
			case 'IM' :
				$vat_prefix = 'GB';
			break;
			case 'MC' :
				$vat_prefix = 'FR';
			break;
		}

		return $vat_prefix;
	}

	/**
		* order_meta_keys function.
		*
		* @access public
		* @param mixed $keys
		* @return void
		*/
	public function order_meta_keys( $keys ) {
		$keys[] = 'VAT Number';
		return $keys;
	}

}
