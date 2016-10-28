<?php
/**
 * Backend Settings
 *
 * @author jj, ap
 */
Class WGM_Settings {


	/**
	 * Register taxonomies
	 *
	 * @access public
	 * @author dw
	 * @static
	 * @return void
	 * @hook woocommerce_register_taxonomy
	 *
	 */
	public static function register_taxonomies() {

		// Register delivery times
		register_taxonomy( 'product_delivery_times',
			array('product', 'product_variation'),
			array(
				'hierarchical' 			=> true,
				'update_count_callback' => '_update_post_term_count',
				'label' 				=> __( 'Lieferzeiten', Woocommerce_German_Market::get_textdomain() ),
				'labels' => array(
						'name' 				=> __( 'Lieferzeiten', Woocommerce_German_Market::get_textdomain() ),
						'singular_name' 	=> __( 'Lieferzeit', Woocommerce_German_Market::get_textdomain() ),
						'menu_name'			=> _x( 'Lieferzeiten', 'Admin menu name', Woocommerce_German_Market::get_textdomain() ),
						'search_items' 		=> __( 'Lieferzeiten durchsuchen', Woocommerce_German_Market::get_textdomain() ),
						'all_items' 		=> __( 'Alle Lieferzeiten', Woocommerce_German_Market::get_textdomain() ),
						'parent_item' 		=> __( 'Übergeordnete Lieferzeit', Woocommerce_German_Market::get_textdomain() ),
						'parent_item_colon' => __( 'Übergeordnete Lieferzeit:', Woocommerce_German_Market::get_textdomain() ),
						'edit_item' 		=> __( 'Lieferzeit bearbeiten', Woocommerce_German_Market::get_textdomain() ),
						'update_item' 		=> __( 'Lieferzeit aktualisieren', Woocommerce_German_Market::get_textdomain() ),
						'add_new_item' 		=> __( 'Lieferzeit hinzufügen', Woocommerce_German_Market::get_textdomain() ),
						'new_item_name' 	=> __( 'Lieferzeitname hinzufügen', Woocommerce_German_Market::get_textdomain() )
					),
				'public'				=> false,
				'show_ui' 				=> true,
				'show_in_nav_menus' 	=> false,
				'query_var' 			=> is_admin(),
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
				),
				'rewrite' 				=> false,
			)
		);
	}

	/**
	* let the user dertermine, if he wants to use the imprint from the
	* page or use the custom text
	*
	* @access public
	* @param array
	*/
	public static function imprint_email_settings( $settings_array ) {

		foreach( $settings_array as $position => $item ) {
			if( 'woocommerce_email_footer_text' == $item[ 'id' ] ) {
				$settings_array[ $position ][ 'desc' ] = $settings_array[ $position ][ 'desc' ] . '<br />' .
					__( 'Geben Sie bitte hier den Text für das Impressum an, wenn Sie nicht den Text von der Webseite verwenden wollen.', Woocommerce_German_Market::get_textdomain() );

				$imprint_checkbox =  array(
					'name'      => __( 'E-Mail Footer Text verwenden', Woocommerce_German_Market::get_textdomain() ),
					'desc' 		=> __( 'Den E-Mail-Footer statt des Textes auf der Impressumsseite verwenden', Woocommerce_German_Market::get_textdomain() ),
					'id' 		=> WGM_Helper::get_wgm_option( 'woocommerce_de_use_backend_footer_text_for_imprint_enabled' ),
					'type' 		=> 'checkbox'
				);
				array_splice( $settings_array, $position, 0, array( $imprint_checkbox ) );
				break;
			}
		}

		return $settings_array;
	}

	/**
	 * Add the settings tab
	 *
	 * @author jj
	 * @access public
	 * @static
	 * @param array $tabs
	 * @return array $tabs
	 */
	public static function add_setting_tab( $tabs ) {

		$tabs[ 'preferences_de' ] = __( 'Einstellungen DE', Woocommerce_German_Market::get_textdomain() );
		return $tabs;
	}

	/**
	* gets the content of the DE preferences tab
	*
	* @access private
	* @static
	* @author fb,jj, ap
	* @return array preferences array for woocommerce backend
	*/
	private static function settings_de_content() {

		// set array for options de
		$options_settings_de = array();
		$tip_default_text = __( 'Bitte erstellen Sie die Seite über den Menüpunkt "Seiten" von WordPress, wenn das Plugin sie nicht anlegen konnte, und wählen Sie diese hier aus. Sie enthält alle notwendigen Informationen zum Vorgang.', Woocommerce_German_Market::get_textdomain() );

		$lieferzeit_strings = WGM_Defaults::get_term_lieferzeiten_strings();

		$options_settings_pages = array(
			array(
				'name' => __( 'Pflicht-Seiten', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id' => 'de_pages'
			),
			array(
				'name' => __( 'Impressum', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für das Impressum an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'impressum' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page',
			),

			array(
				'name' => __( 'Versandkosten & Lieferung', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für die Versandkosten & Lieferung an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page'
			),

			array(
				'name' => __( 'Widerruf', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für das Widerrufsrecht an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'widerruf' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page'
			),

            array(
                'name' => __( 'Widerruf für digitale Medien', Woocommerce_German_Market::get_textdomain() ),
                'desc' => __( 'Geben Sie eine Seite für das Widerrufsrecht für digitale Medien an', Woocommerce_German_Market::get_textdomain() ),
                'tip'  => $tip_default_text,
                'id'   => WGM_Helper::get_wgm_option( 'widerruf_fuer_digitale_medien' ),
                'css'  => 'min-width:50px;',
                'type' => 'single_select_page'
            ),

			array(
				'name' => __( 'Datenschutz', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für den Datenschutz an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'datenschutz' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page'
			),

			array(
				'name' => __( 'Bestellvorgang', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie ein Seite für den Bestellvorgang an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'bestellvorgang' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page'
			),

			array(
				'name' => __( 'Zahlungsarten', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für die Zahlungsarten an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => $tip_default_text,
				'id'   => WGM_Helper::get_wgm_option( 'zahlungsarten' ),
				'css'  => 'min-width:50px;',
				'type' => 'single_select_page'
			),

			array(
				'name' => __( 'Bestellung prüfen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie eine Seite für das Prüfen der Bestellung an. Diese Seite muss den Shortcode [woocommerce_de_check] und nutzt die Seite \'Zur Kasse\' Seite (Checkout) als Eltern-Seite.', Woocommerce_German_Market::get_textdomain() ),
				'tip' => $tip_default_text,
				'id' => WGM_Helper::get_wgm_option( 'check' ),
				'css' => 'min-width:50px;',
				'type' => 'single_select_page'
			),

			array( 'type' => 'sectionend', 'id' => 'de_pages' ),
			array(
				'name' => __( 'Angaben zur Widerrufsseite', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'de_widerruf'
			),

			array(
				'name' => __( 'Widerrufsfrist', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie die Widerrufsfrist in Tagen an', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Geben Sie die Widerrufsfrist in Tagen an. Standard sind 14 Tage', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'widerrufsfrist' ),
				'css'  => 'min-width:50px;',
				'type' => 'text',
				'default'  => '14'
			),

			array(
				'name' => __( 'Widerruf Addressdaten', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Geben Sie Namen/Firma und ladungsfähige Anschrift des Widerrufsadressaten an.', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Namen/Firma und ladungsfähige Anschrift des Widerrufsadressaten. (Zusätzlich können angegeben werden Telefaxnummer, E-Mail-Adresse und/oder, wenn der Verbraucher eine Bestätigung seiner Widerrufserklärung an den Unternehmer erhält, auch eine Internetadresse.', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'widerrufsadressdaten' ),
				'css'  => 'width:50%; height: 75px;',
				'type' => 'textarea',
				'default'  => ''
			),
			array( 'type' => 'sectionend', 'id' => 'de_widerruf' ),

			array(
				'name' => __( 'Lieferzeiten', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'de_lieferzeiten'
			),
			array(
				'name' => __( 'Lieferzeit', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Standardlieferzeit für neue Produkte', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Dieser Wert kann am Produkt individuell überschrieben werden', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'global_lieferzeit' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 7,
				'options' => $lieferzeit_strings
			),
			array( 'type' => 'sectionend', 'id' => 'de_lieferzeiten' ),


			array(
				'name' => __( 'Kleinunternehmerregelung', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'de_kleinunternehmerregelung'
			),

			array(
				'name' => __( 'Kleinunternehmerregelung', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Kleinunternehmerregelung gemäß § 19 UStG', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Kleinunternehmerregelung gemäß § 19 UStG', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'off',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
			),

			array( 'type' => 'sectionend', 'id' => 'de_kleinunternehmerregelung' ),


			array(
				'name' => __( 'Anzeige', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'de_frontent_view',
			),
			array(
				'name' => __( 'Lieferzeiten in Übersicht', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Zeige die Lieferzeiten in der Übersicht unter jedem Produkt', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Zeige die Lieferzeiten in der Übersicht unter jedem Produkt', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_delivery_time_overview' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'off',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
				)
			),

			array(
				'name' => __( 'Versandkostenfrei', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Zeige "versandkostenfrei" anstelle von "zzgl. Versand"', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Zeige "versandkostenfrei" anstelle von "zzgl. Versand"', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_free_shipping' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'off',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
				),

			array(
				'name' => __( 'Produktkurzbeschreibungen anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Anzeige der Produktkurzbeschreibung im Checkout und Bestellübersicht', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Anzeige der Produktkurzbeschreibung im Checkout und Bestellübersicht', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_show_short_desc' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'off',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
				),

			array(
				'name' => __( 'Preis pro Einheit in der Übersicht anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Globale Einstellung zur Anzeige der Preise pro Einheit in der Übersicht', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Globale Einstellung zur Anzeige der Preise pro Einheit in der Übersicht', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_price_per_unit' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'on',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
				),

            array(
                'name' => __( 'Versandpauschale und kostenloser Versand gleichzeitig verwendbar', Woocommerce_German_Market::get_textdomain() ),
                'desc' => __( 'Versandkostenpauschale ist wenn aktiviert trotz kostenlosen Versand für den Kunden auswählbar.', Woocommerce_German_Market::get_textdomain() ),
                'tip'  => __( 'Versandkostenpauschale ist wenn aktiviert trotz kostenlosen Versand für den Kunden auswählbar.', Woocommerce_German_Market::get_textdomain() ),
                'id'   => WGM_Helper::get_wgm_option( 'wgm_dual_shipping_option' ),
                'css'  => 'min-width:50px;',
                'type' => 'select',
                'default'  => 'off',
                'options' => array(
                    'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
                    'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
                )
            ),

			array(
				'name' => __( 'Zusatzkostenhinweise für nicht EU Ausland anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Zeige einen Zusatzkostenhinweis für Versand in nicht EU Länder unter jedem Produkt', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Zeige einen Zusatzkostenhinweis für Versand in nicht EU Länder unter jedem Produkt', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_extra_cost_hint_eu' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'off',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
			),

			array(
				'name' => __( 'Widerrufsbelehrung anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Die Widerrufsbelehrung auf der 1. Checkoutseite anzeigen?', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Die Widerrufsbelehrung auf der 1. Checkoutseite anzeigen?', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_show_Widerrufsbelehrung' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'on',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
				),

			array(
				'name' => __( 'Versandkosten und Widerrufsrecht Hinweis im Warenkorb anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Anzeige des Hinweises in der Warenkorb Tabelle', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Anzeige des Hinweises in der Warenkorb Tabelle', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_disclaimer_cart' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'on',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
			),

			array(
				'name' => __( 'Hinweis zur Steuer- und Versandkostenschätzung im Warenkorb anzeigen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Anzeige unter dem Gesamtwert und über dem "Weiter" Button im Warenkorb', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Anzeige unter dem Gesamtwert und über dem "Weiter" Button im Warenkorb', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'woocommerce_de_estimate_cart' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'on',
				'options' => array(
					'on'  => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
					)
				),

			array( 'type' => 'sectionend', 'id' => 'de_frontent_view' ),
		); // end array

		$options_settings_de = array_merge( $options_settings_de, $options_settings_pages );

		$options_settings_de[] = array( 'type' => 'sectionend', 'id' => 'de_lieferzeiten' );

		$options_settings_hints = array(

			array(
				'name' => __( 'Hinweise', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'de_hints'
			),

			array(
				'name' => __( 'Hinweistext auf letzter Kassenseite', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Hier können Sie Ihre Hinweise auf der letzten Kassenseite eintragen wie zum Beispiel eventuell anfallende Kosten durch Zölle oder Nachnahmegebühren.', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'Hier können Sie Ihre Hinweise auf der letzten Kassenseite eintragen wie zum Beispiel eventuell anfallende Kosten durch Zölle oder Nachnahmegebühren.', Woocommerce_German_Market::get_textdomain() ),
				'id'   => 'woocommerce_de_last_checkout_hints',
				'css'  => 'width:100%; height: 75px;',
				'type' => 'textarea',
				'default'  => '',
				'args' => ''
			),

			array(
				'name' => __( 'Impressum', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Hinterlegen Sie eine Seite Impressum mit den Daten: Name der Firma, Geschäftsführer bzw. Inhaber, Adresse, Telefonnummer, Faxnummer falls vorhanden, Webseitenadresse, E-Mail-Adresse, Zuständiges Finanzamt, Steuernummer, Umsatzsteuer-Id Nummer, Zuständiges Gericht, HRB Nummer (falls vorhanden), Kontoinhaber, Kontonummer, BLZ, Bankname, SWIFT, IBAN.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
				),

			array(
				'name' => __( 'Widerrufsbelehrung', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Falls die von der mitgelieferten Widerrufsbelehrung abgewichen werden sollte können Sie mit folgenden Shortcodes auf bestimmte Werte zugreifen: [woocommerce_de_disclaimer_deadline] für die Widerrufsfrist und [woocommerce_de_disclaimer_address_data] für Namen/Firma und ladungsfähige Anschrift des Widerrufsadressaten. (Zusätzlich können angegeben werden Telefaxnummer, E-Mail-Adresse und/oder, wenn der Verbraucher eine Bestätigung seiner Widerrufserklärung an den Unternehmer erhält, auch eine Internetadresse.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
			),

			array(
				'name' => __( 'Zusatzinhalte', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Prüfen Sie, ob die Seiten Impressum, Versandkosten, Widerruf, AGB, Datenschutz, Bestellvorgang und Zahlungsarten vorhanden sind und pflegen Sie die Inhalte, in dem Sie die Platzhalter ersetzen. (Das Plugin versucht beim Aktivieren diese Seiten anzulegen.) Diese Seiten informieren den Besucher zu den jeweiligen Themen und sollten entsprechend verlinkt sein. Sollten die Seiten mit ihren Mustertexten nicht angelegt sein, so können Sie die Mustertexte auf <a href="http://xxx.de/mustertexte/">xxx</a> einsehen und nutzen.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
			),

			array(
				'name' => __( 'Bestimmte Berufsgruppen', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Bitte beachten: Für bestimmte Berufsgruppen, beispielsweise Apotheker und Ärzte, Architekten, Rechtsanwälte und Optiker, gelten weitere Pflichtangaben. Je nach Berufsgruppe kann dies variieren und sollte zwingend geprüft werden. Diese Seiten informieren den Besucher zu den jeweiligen Themen und sollten entsprechend verlinkt sein. Sollten die Seiten mit ihren Mustertexten nicht angelegt sein, so können Sie die Mustertexte auf <a href="http://xxx.de/mustertexte/">xxx</a> einsehen und nutzen.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
			),

			array(
				'name' => __( 'Links im Footer', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Hinterlegen Sie die Seiten mit Ihren Links zum Datenschutz, Bestellvorgang, Zahlungsarten, Impressum, Lieferung, Widerruf und AGB im Footer des Frontends.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
			),

			array(
				'name' => __( 'Links im Bestellvorgang', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Links im Bestellvorgang müssen eindeutig als sprechende Links gekennzeichnet werden, sie müssen sehr gut als Links erkennbar sein. z.B. mit Hilfe einer Unterstreichung.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string'
			),

			array(
				'name' => __( 'Lieferzeiten', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'Zu jeder Kategorie und jedem Produkt können eigenen Lieferzeiten hinterlegt werden. Alternativ wird der gesetzte Standard gezogen. Werden neue Kategorien angelegt, dann können Sie über diese Einstellungen die Lieferzeiten aktuallisieren.', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'string',
			),

			array(
				'name' => __( 'Standard-CSS', Woocommerce_German_Market::get_textdomain() ),
				'desc' => __( 'L&auml;dt das Standard-CSS', Woocommerce_German_Market::get_textdomain() ),
				'tip'  => __( 'L&auml;dt das Standard-CSS', Woocommerce_German_Market::get_textdomain() ),
				'id'   => WGM_Helper::get_wgm_option( 'load_woocommerce-de_standard_css' ),
				'css'  => 'min-width:50px;',
				'type' => 'select',
				'default'  => 'on',
				'options' => array(
					'on' => __( 'Ja', Woocommerce_German_Market::get_textdomain() ),
					'off' => __( 'Nein', Woocommerce_German_Market::get_textdomain() ),
				)
			),

			array( 'type' => 'sectionend', 'id' => 'de_hints' ),
		);

		return array_merge( $options_settings_de, $options_settings_hints );
	}

	/**
	* Generate the settings pages in the Backend
	*
	* @access public
	* @static
	* @uses woocommerce_admin_fields
	* @return void
	*/
	public static function add_settings_tab_content() {

		// fill the tab with content with the standard method
		woocommerce_admin_fields( WGM_Settings::settings_de_content() );
	}

	/**
	* Update the settings
	*
	* @access public
	* @static
	* @uses woocommerce_update_options
	* @return void
	*/
	public static function update_settings_tab_content() {

		// update the settings with the standard method
		woocommerce_update_options( WGM_Settings::settings_de_content()  );
	}

	/**
	* Save delivery time
	*
	* @access public
	* @author jj, ap
	* @uses update_post_meta
	* @param int $post_id
	* @param array $post
	* @return void
	*/
	public static function add_process_product_meta( $post_id, $post ) {

		if ( ! isset( $_POST[ 'lieferzeit' ] ) )
			$_POST[ 'lieferzeit' ] = 0;
		update_post_meta( $post_id, '_lieferzeit', stripslashes( (int) $_POST[ 'lieferzeit' ] ) );

		if ( ! isset( $_POST[ '_suppress_shipping_notice' ] ) )
			$_POST[ '_suppress_shipping_notice' ] = '';
		update_post_meta( $post_id, '_suppress_shipping_notice', stripslashes( $_POST[ '_suppress_shipping_notice' ] ) );

		if ( ! isset( $_POST[ '_unit_regular_price_per_unit' ] ) )
			$_POST[ '_unit_regular_price_per_unit' ] = '';
		update_post_meta( $post_id, '_unit_regular_price_per_unit', stripslashes( $_POST[ '_unit_regular_price_per_unit' ] ) );

		if ( ! isset( $_POST[ '_unit_regular_price_per_unit_mult' ] ) )
			$_POST[ '_unit_regular_price_per_unit_mult' ] = '';
		update_post_meta( $post_id, '_unit_regular_price_per_unit_mult', stripslashes( $_POST[ '_unit_regular_price_per_unit_mult' ] ) );

		if ( ! isset( $_POST[ '_no_delivery_time_string' ] ) )
			$_POST[ '_no_delivery_time_string' ] = '';
		update_post_meta( $post_id, '_no_delivery_time_string', stripslashes( $_POST[ '_no_delivery_time_string' ] ) );

		if ( ! isset( $_POST[ '_regular_price_per_unit' ] ) )
			$_POST[ '_regular_price_per_unit' ] = '';
		update_post_meta( $post_id, '_regular_price_per_unit', stripslashes( $_POST[ '_regular_price_per_unit' ] ) );

		if ( ! isset( $_POST[ '_unit_sale_price_per_unit' ] ) )
			$_POST[ '_unit_sale_price_per_unit' ] = '';
		update_post_meta( $post_id, '_unit_sale_price_per_unit', stripslashes( $_POST[ '_unit_sale_price_per_unit' ] ) );

		if ( ! isset( $_POST[ '_unit_sale_price_per_unit_mult' ] ) )
			$_POST[ '_unit_sale_price_per_unit_mult' ] = '';
		update_post_meta( $post_id, '_unit_sale_price_per_unit_mult', stripslashes( $_POST[ '_unit_sale_price_per_unit_mult' ] ) );

		if ( ! isset( $_POST[ '_sale_price_per_unit' ] ) )
			$_POST[ '_sale_price_per_unit' ] = '';
		update_post_meta( $post_id, '_sale_price_per_unit', stripslashes( $_POST[ '_sale_price_per_unit' ] ) );

        if ( ! isset( $_POST[ 'product_function_desc_textarea' ] ) )
			$_POST[ 'product_function_desc_textarea' ] = '';
		update_post_meta( $post_id, 'product_function_desc_textarea', stripslashes( $_POST[ 'product_function_desc_textarea' ] ) );
	}

	/**
	* add delivery time link to products
	*
	* @access	public
	* @author	jj, ap
	* @uses		apply_filters
	* @static
	* @return	void
	*/
	public static function add_product_write_panel_tabs () {
        $product_write_panel_tabs = '';

        $_product = get_product();


        if( $_product->is_virtual() || $_product->is_downloadable() ){
            $product_write_panel_tabs .= '<li class="advanced_tab advanced_options	"><a href="#product_function_desc">' . __( 'Voraussetzungen', Woocommerce_German_Market::get_textdomain() ) . '</a></li>';
        }

        if( $_product->product_type = 'product_type' ){
            foreach( $_product->get_children() as $child ){
                $p = get_product( $child );

                if( $p->is_virtual() || $p->is_downloadable() ){
                    $product_write_panel_tabs .= '<li class="advanced_tab advanced_options	"><a href="#product_function_desc">' . __( 'Voraussetzungen', Woocommerce_German_Market::get_textdomain() ) . '</a></li>';
                    break;
                }
            }
        }

		$product_write_panel_tabs .= '<li class="advanced_tab advanced_options	"><a href="#delivery_time">' . __( 'Lieferzeit', Woocommerce_German_Market::get_textdomain() ) . '</a></li>';
		$product_write_panel_tabs .= '<li class="advanced_tab advanced_options"><a href="#shipping_options">' . __( 'Versandoptionen', Woocommerce_German_Market::get_textdomain() ) . '</a></li>';
		$product_write_panel_tabs .= '<li class="advanced_tab advanced_options"><a href="#price_per_unit_options">' . __( 'Preis pro Einheit', Woocommerce_German_Market::get_textdomain() ) . '</a></li>';
		echo apply_filters( 'woocommerce_de_product_write_panel_tabs' , $product_write_panel_tabs );
	}

	/**
	* add delivery time control and shipping control to products
	*
	* @access public
	* @author jj, ap
	* @uses maybe_unserialize, get_the_ID, get_post_meta, selected, woocommerce_wp_text_input, get_woocommerce_currency_symbol
	* @static
	* @return void
	*/
	public static function add_product_write_panels() {

		$data = maybe_unserialize( get_post_meta( get_the_ID(), '_lieferzeit', TRUE ) );
		$data_shipping = maybe_unserialize( get_post_meta( get_the_ID(), '_suppress_shipping_notice', TRUE ) );

		$terms = get_terms( 'product_delivery_times', array( 'orderby' => 'id', 'hide_empty' => 0 ) );

        $_product = get_product();

        if ( is_numeric( $data ) )
            $lieferzeit = (int) $data;
        else
            $lieferzeit = get_option( WGM_Helper::get_wgm_option( 'global_lieferzeit' ) );

        $is_digital = false;

        if( method_exists( $_product, 'is_virtual' ) && method_exists( $_product, 'is_downloadable' ) ) {
            $is_digital = ( $_product->is_virtual() || $_product->is_downloadable() );
        }

        if( $_product->product_type = 'variable' ){
            foreach( $_product->get_children() as $child ){
                $p = get_product( $child );

                if( $p->is_virtual() || $p->is_downloadable() ){
                   $is_digital = true;
                }
            }
        }

        if( $is_digital ): ?>

            <div id="product_function_desc" class="panel woocommerce_options_panel" style="display: block; ">
                <?php
                $field = array(
                    'label' => __( 'Voraussetzungen', Woocommerce_German_Market::get_textdomain() ),
                    'id' => 'product_function_desc_textarea',
                );

                woocommerce_wp_textarea_input( $field );
                ?>
            </div>


        <?php endif; ?>


        <div id="shipping_options" class="panel woocommerce_options_panel" style="display: block; ">
            <p class="form-field show_if_simple show_if_variable" style="display:block">
                <label for="_suppress_shipping_notice"><?php _e( 'kein Versandkostenhinweis', Woocommerce_German_Market::get_textdomain() ); ?></label>
                <input type="checkbox" class="checkbox" name="_suppress_shipping_notice" value="on" <?php checked( $data_shipping, 'on' ); ?>/>
                <span class="description"><?php _e( 'Den Versandkostenhinweis nur für dieses Produkt im Frontend und Mails nicht anzeigen (z.B.: bei Downloads )', Woocommerce_German_Market::get_textdomain() );?></span>
            </p>
        </div>


		<div id="delivery_time" class="panel woocommerce_options_panel" style="display: block; ">
			<p class="form-field">
				<label for="lieferzeit"><?php _e( 'Lieferzeit:', Woocommerce_German_Market::get_textdomain() ); ?></label>
				<select name="lieferzeit" id="lieferzeit_product_panel">
					<option value="-1"><?php _e( 'Bitte auswählen', Woocommerce_German_Market::get_textdomain() ); ?></option>
					<?php
					foreach ( $terms as $i  )  {
						echo '<option value="' . $i->term_id . '"' . selected( $i->term_id, $lieferzeit, FALSE ) . '>';
						echo $i->name . '</option>';
					}
					?>
				</select>
			</p>
		</div>
		<div id="price_per_unit_options" class="panel woocommerce_options_panel" style="display: block; ">
			<?php
			$smalltax = '<br /><small> ' . __( 'inkl. MwSt',  Woocommerce_German_Market::get_textdomain() ) . ' </small>';
			$regular_price_per_unit_selection = array( 'id' => '_unit_regular_price_per_unit' );

			$mult_field = '<span style="float: left;">&nbsp;&#47; &nbsp;</span> <input type="text" style="width: 40px;" name="_unit_regular_price_per_unit_mult" value="'.  get_post_meta( get_the_ID(), '_unit_regular_price_per_unit_mult', true ) .'" />';

			// Price
			WGM_Settings::extended_woocommerce_text_input(
				array(
					'id'          						=> '_regular_price_per_unit',
					'label'      						=> __( 'Regulärer Preis',  Woocommerce_German_Market::get_textdomain() ) . ' (' . get_woocommerce_currency_symbol() . ')' . $smalltax,
					'between_input_and_desscription' 	=> $mult_field . WGM_Settings::select_scale_units( $regular_price_per_unit_selection )
				)
			);

			$sale_price_per_unit_selection = array( 'id' => '_unit_sale_price_per_unit' );

			$mult_field = '<span style="float: left;">&nbsp;&#47; &nbsp;</span> <input type="text" style="width: 40px;" name="_unit_sale_price_per_unit_mult" value="'.  get_post_meta( get_the_ID(), '_unit_sale_price_per_unit_mult', true ) .'" />';


			// Special Price
			WGM_Settings::extended_woocommerce_text_input(
				array(
					'id'          						=> '_sale_price_per_unit',
					'label'       						=> __( 'Angebotspreis',  Woocommerce_German_Market::get_textdomain() ) . ' (' . get_woocommerce_currency_symbol() . ')' . $smalltax ,
					'between_input_and_desscription' 	=> $mult_field . WGM_Settings::select_scale_units( $sale_price_per_unit_selection )
				)
			);
			?>
		</div>
		<?php
	}

	/**
	 * Prints a woocommerce settigs html text field.
	 * Copied from woocommerce core, extended to field after it (select box for scale units)
	 *
	 * @since	1.1.5beta
	 * @static
	 * @global	$thepostid, $post, $woocommerce
	 * @access	public
	 * @param 	array $field
	 * @return	void
	 */
	public static function extended_woocommerce_text_input( $field ) {

		global $thepostid, $post, $woocommerce;

		$thepostid 					= empty( $thepostid ) ? $post->ID : $thepostid;
		$field[ 'placeholder' ] 	= isset( $field[ 'placeholder' ] ) ? $field[ 'placeholder' ] : '';
		$field[ 'class' ]			= isset( $field[ 'class' ] ) ? $field[ 'class' ] : 'short';
		$field[ 'wrapper_class' ]	= isset( $field[ 'wrapper_class' ] ) ? $field[ 'wrapper_class' ] : '';
		$field[ 'value' ]			= isset( $field[ 'value' ] ) ? $field[ 'value' ] : get_post_meta( $thepostid, $field[ 'id' ], true );
		$field[ 'name' ]			= isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field[ 'id' ];
		$field[ 'type' ]			= isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $field[ 'custom_attributes' ] ) && is_array( $field[ 'custom_attributes' ] ) )
			foreach ( $field[ 'custom_attributes' ] as $attribute => $value )
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field[ 'wrapper_class' ] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

		if ( ! empty( $field[ 'between_input_and_desscription' ] ) ) {
			echo '<span>' . $field[ 'between_input_and_desscription' ] . '</span>';
		}

		if ( ! empty( $field['description'] ) ) {

			if ( isset( $field['desc_tip'] ) ) {
				echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}

		}
		echo '</p>';
	}

	/**
	* Make a select field for scale_units
	*
	* @access	public
	* @param	array $field
	* @uses		get_post_meta, get_terms, selected
	* @global	$thepostid, $post, $woocommerce
	* @static
	* @return	string html
	*/
	public static function select_scale_units( $field ) {

		global $thepostid, $post, $woocommerce;

		if ( ! $thepostid )
			$thepostid = $post->ID;

		if ( ! isset( $field[ 'class' ] ) )
			$field[ 'class' ] = 'select short';

		if ( ! isset( $field[ 'value' ] ) )
			$field[ 'value' ] = get_post_meta( $thepostid, $field[ 'id' ], true );

		$default_product_attributes = WGM_Defaults::get_default_procuct_attributes();

		$attribute_taxonomy_name = wc_attribute_taxonomy_name( $default_product_attributes[ 0 ][ 'attribute_name' ] );

		$string = '<select name="' . $field[ 'id' ] . '">';

		$terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );

		foreach ( $terms as  $value ) {
			$string .= '<option value="'. $value->name .'" ';
			$string .= selected( $field[ 'value' ], $value->name, FALSE );
			$string .=  '>'. $value->description . '</option>';
		}

		$string .= '</select>';
		return $string;
	}

	/**
	* If desired, force SSL for own checkout sites too
	*
	* @access	public
	* @global	$post
	* @static
	* @return	bool
	*/
	public static function unforce_ssl_checkout() {
		global $post;

		return has_shortcode( $post->post_content, 'woocommerce_de_check' );
	}
}
?>