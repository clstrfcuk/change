<?php
/**
 * General Defaults
 *
 *  @author jj, ap
 */
Class WGM_Defaults {

	/**
	 * holds key for $lieferzeit_string
	 * default shipping of 48 if not defined
	 *
	 * @access	public
	 * @static
	 * @var		int
	 */
	public static $default_lieferzeit_id = 3;

	/**
	 * Currency
	 *
	 * @access	public
	 * @static
	 * @var string
	 */
	public static $woocommerce_de_currency	= 'EUR';

	/**
	 * Default Country
	 *
	 * @access	public
	 * @static
	 * @var string
	 */
	public static $woocommerce_de_default_country = 'DE';

	/**
	 * Default Pages
	 *
	 * @access	private
	 * @static
	 * @var		std object
	 */
	private static $default_pages = NULL;

	/**
	 * Default Options
	 *
	 * @access	private
	 * @static
	 * @var array
	 */
	private static $options = array();

	/**
	 * Constructor
	 */
	public function __construct () {

		// to be sure, that the slugs ar in option array when calling options
		WGM_Defaults::generate_default_page_objects();
	}

	/**
	 * General options
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_options
	 * @since	1.1.5
	 * @return	array
	 */
	public static function get_options() {
		if( ! empty( WGM_Defaults::$options ) ) {
			return WGM_Defaults::$options;
		} else {
			WGM_Defaults::generate_options();
			return WGM_Defaults::$options;
		}
	}

	/**
	 * General options
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_options
	 * @since	1.1.5
	 * @return	array
	 */
	public static function generate_options() {

		$options = array(
			'agb'															=> 'woocommerce_terms_page_id',
			'check'													 		=> 'woocommerce_check_page_id',
			'widerruf'												  		=> 'woocommerce_widerruf_page_id',
            'widerruf_fuer_digitale_medien'									=> 'woocommerce_widerruf_fuer_digitale_medien_page_id',
			'impressum'														=> 'woocommerce_impressum_page_id',
			'datenschutz'													=> 'woocommerce_datenschutz_page_id',
			'zahlungsarten'													=> 'woocommerce_zahlungsarten_page_id',
			'versandkosten'											 		=> 'woocommerce_versandkosten_page_id',
            'versandkosten__lieferung'									    => 'woocommerce_versandkosten__lieferung_page_id',
			'bestellvorgang'												=> 'woocommerce_bestellvorgang_page_id',
			'widerrufsfrist'												=> 'woocommerce_widerrufsfrist',
			'global_lieferzeit'										 		=> 'woocommerce_global_lieferzeit',
			'widerrufsadressdaten'									  		=> 'woocommerce_widerrufsadressdaten',
			'woocommerce_options_installed'							 		=> 'woocommerce_options_installed',
			'load_woocommerce-de_standard_css'						  		=> 'load_woocommerce-de_standard_css',
			'woocommerce_de_append_terms_to_mail'							=> 'woocommerce_de_append_terms_to_mail',
			'woocommerce_de_append_imprint_to_mail'							=> 'woocommerce_de_append_imprint_to_mail',
			'woocommerce_de_append_withdraw_to_mail'						=> 'woocommerce_de_append_withdraw_to_mail',
			'woocommerce_de_show_widerrufsbelehrung'						=> 'woocommerce_de_show_widerrufsbelehrung',
			'woocommerce_de_show_delivery_time_overview'					=> 'woocommerce_de_show_delivery_time_overview',
			'woocommerce_de_show_shipping_fee_overview'						=> 'woocommerce_de_show_shipping_fee_overview',
			'woocommerce_de_show_shipping_fee_overview_single'				=> 'woocommerce_de_show_shipping_fee_overview_single',
			'woocommerce_de_show_free_shipping'								=> 'woocommerce_de_show_free_shipping',
			'woocommerce_de_show_show_short_desc'							=> 'woocommerce_de_show_show_short_desc',
			'woocommerce_de_use_backend_footer_text_for_imprint_enabled'	=> 'woocommerce_de_use_backend_footer_text_for_imprint_enabled',
			'woocommerce_de_show_Widerrufsbelehrung'						=> 'woocommerce_de_show_Widerrufsbelehrung',
			'woocommerce_de_disclaimer_cart'								=> 'woocommerce_de_disclaimer_cart',
			'woocommerce_de_estimate_cart'									=> 'woocommerce_de_estimate_cart',
			'woocommerce_options_installed'									=> 'woocommerce_options_installed',
			'woocommerce_de_previous_installed'								=> 'woocommerce_de_previous_installed',
			'woocommerce_de_show_extra_cost_hint_eu'						=> 'woocommerce_de_show_extra_cost_hint_eu',
			'woocommerce_de_kleinunternehmerregelung'						=> 'woocommerce_de_kleinunternehmerregelung',
			'woocommerce_de_show_price_per_unit'							=> 'woocommerce_de_show_price_per_unit',
            'wgm_dual_shipping_option'						            	=> 'wgm_dual_shipping_option'
		);

		WGM_Defaults::$options = apply_filters( 'woocommerce_de_options', $options );
	}

	/**
	 * Default pages
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_default_pages
	 * @uses	Woocommerce_German_Market::get_textdomain()
	 * @since	1.1.5
	 * @return	array
	 */
	public static function get_default_pages() {

		// get textdomain
		$td = Woocommerce_German_Market::get_textdomain();

		// Declare the default Pages and create a translated object
		$pages = array(
				__( 'Impressum', $td ),
                sprintf( __( 'Versandkosten %s Lieferung', $td ), '&amp;'),
				__( 'Widerruf', $td ),
                __( 'Widerruf für digitale Medien', $td ),
				__( 'Datenschutz', $td ),
				__( 'Bestellvorgang', $td ),
				__( 'Zahlungsarten',  $td ),
				sprintf( __( 'Bestellung prüfen %s Bezahlen', $td ), '&rarr;'),
				__( 'AGB', $td ),
				__( 'AGB Österreich', $td ),
				__( 'Rücktrittsbelehrung Österreich', $td )
		);
		return apply_filters( 'woocommerce_de_default_pages', $pages );
	}

	/**
	 * Get the default page objects
	 *
	 * @static
	 * @access	public
	 * @return	Object  default pages objects
	 */
	public static function get_default_page_objects() {

		if( WGM_Defaults::$default_pages !== NULL ) {
			return WGM_Defaults::$default_pages;
		} else {
			WGM_Defaults::generate_default_page_objects();
			return WGM_Defaults::$default_pages;
		}
	}

    /**
     * Manual translate option names.
     * @param $option
     * @return mixed
     */
    public static function get_german_option_name( $option ) {

		$options = array(
			'imprint'			=> 'impressum',
			'shipping_costs'	=> 'versandkosten',
			'revocation'		=> 'widerruf',
			'privacy'			=> 'datenschutz',
			'order_transaction'	=> 'bestellvorgang',
			'method_of_payment'	=> 'zahlungsarten',
			'check_order_pay'	=> 'check'
		);

		if( isset( $options[ $option ] ) )
			return $options[ $option ];
		else
			return $option;
	}

	/**
	 * generate the default page objects
	 *
	 * @static
	 * @access	public
	 * @return	void
	 */
	private static function generate_default_page_objects() {

		// get default pages
		$pages = WGM_Defaults::get_default_pages();

        $default_pages = new stdClass();

        // because the terms slugs are different from the ids ( also english )
        $searches = array(
        	'agb',
        	'bestellung_pruefen_bezahlen'
		);

        $replaces = array(
        	'terms',
        	'check'
        );

        $drafts = array( 'AGB Österreich', 'Rücktrittsbelehrung Österreich' );

        foreach( $pages as $page ){

            $slug = WGM_Helper::get_page_slug( $page );

            @$default_pages->$slug->name      = $page;
            @$default_pages->$slug->slug      = $slug;
            @$default_pages->$slug->content   = $slug . '.html';
            @$default_pages->$slug->status    = in_array($page, $drafts) ? 'draft' : 'publish';

            $id_slug = str_replace( $searches, $replaces, $slug );

            // set the slug to the options
            WGM_Defaults::$options[ $slug ] = 'woocommerce_' . $id_slug . '_page_id';
        }
        WGM_Defaults::$default_pages = $default_pages;
	}

	/**
	 * Registers default delivery time strings
	 *
	 * @access	public
	 * @static
	 */
	public static function register_default_lieferzeiten_strings() {

		$option = 'wgm_default_lieferzeiten_registered';

		if ( !get_option( $option, false ) ) {

			add_option( $option, true );

			$td = Woocommerce_German_Market::get_textdomain();

			$defaults = array(
				__( 'Sofort lieferbar',						$td ),
				__( 'ca. 24 Stunden',						$td ),
				__( 'ca. 48 Stunden',						$td ),
				__( 'ca. 2-3 Werktage',						$td ),
				__( 'ca. 3-4 Werktage',						$td ),
				__( 'ca. 10 Werktage',						$td ),
				__( 'ca. 14 Werktage',						$td ),
				__( 'ca. 30 Werktage',						$td ),
				__( 'Derzeit nicht lieferbar',				$td ),
				__( 'keine Lieferzeit ( z.B.: Download )',	$td )
			);

			$defaults = apply_filters( 'woocommerce_de_default_delivery_times', $defaults );

			// Add terms
			foreach ($defaults as $key => $value) {
				wp_insert_term( $value, 'product_delivery_times' );
			}

		}
	}

	/**
	 * Lieferzeit strings
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_lieferzeit_strings
	 * @uses	Woocommerce_German_Market::get_textdomain()
	 * @since	1.1.5
	 * @return	array
	 */
	public static function get_lieferzeit_strings() {

		// Get the terms
		$terms = array_map(
			array( 'WGM_Defaults', 'wgm_return_translated_terms' ),
			get_terms( 'product_delivery_times', array(
				'orderby' => 'id',
				'hide_empty' => 0
			)
		));

		if( ! in_array( __( 'Nutze den Standard', Woocommerce_German_Market::get_textdomain() ), $terms ) )
			$term = array_unshift( $terms ,  __( 'Nutze den Standard', Woocommerce_German_Market::get_textdomain() ) );

		return apply_filters( 'woocommerce_de_lieferzeit_strings', $terms );
	}

	public static function get_term_lieferzeiten_strings(){
		$terms = get_terms( 'product_delivery_times', array( 'orderby' => 'id', 'hide_empty' => 0 ) );
		$out = array();

		foreach( $terms as $term )
			$out[ $term->term_id ] = __( $term->name, Woocommerce_German_Market::get_textdomain() );

		return $out;
	}

	/**
	 * Function for array_map
	 *
	 * @access	public
	 * @static
	 * @param 	string $term
	 * @uses	Woocommerce_German_Market::get_textdomain(), __
	 * @since	2.1.2
	 * @return	string
	 */
	public static function wgm_return_translated_terms( $term ) {

		// get textdomain
		$td = Woocommerce_German_Market::get_textdomain();
		return __( $term->name, $td );
	}


	/**
	 * Get the default tax rates
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_default_tax_rates
	 * @uses	Woocommerce_German_Market::get_textdomain()
	 * @since	1.1.5
	 * @return	array
	 */
	public static function get_default_tax_rates() {

		// get textdomain
		$td = Woocommerce_German_Market::get_textdomain();

		$default_de_tax_rates = array(
						array( 'countries' => array( 'DE' => array( '*' ) ),
								 'rate'	 => number_format( 19.0, 4 ),
								 'shipping'  => 'yes',
								 'class'	=> '',
								 'label'	 => __( 'MwSt.', $td ),
								 'compound'  => 'no'
								 ),
						array( 'countries' => array( 'DE' => array( '*' ) ),
								 'rate'	 => number_format( 7.0, 4 ),
								 'shipping'  => 'yes',
								 'class'	=> 'reduced-rate',
								 'label'	 => __( 'MwSt.', $td ),
								 'compound'  => 'no'
								 )
						);

		return apply_filters( 'woocommerce_de_default_tax_rates',  $default_de_tax_rates );
	}

	/**
	 * Get default product attributes
	 *
	 * @access	public
	 * @static
	 * @hook	woocommerce_de_default_procuct_attributes
	 * @uses	Woocommerce_German_Market::get_textdomain()
	 * @since	1.1.5
	 * @return	array
	 */
	public static function get_default_procuct_attributes() {

		// get textdomain
		$td = Woocommerce_German_Market::get_textdomain();

		$scale_units = array(
							  array( 'tag-name' => 'kg', 'tag-slug' => 'kg','description' => __( 'Kilogramm', $td ) ),
							  array( 'tag-name' => 'g',  'tag-slug' => 'g', 'description' => __( 'Gramm', $td ) ),
							  array( 'tag-name' => 'mg', 'tag-slug' => 'mg','description' => __( 'Milligramm', $td ) ),
							  array( 'tag-name' => 'L',  'tag-slug' => 'L', 'description' => __( 'Liter', $td ) ),
							  array( 'tag-name' => 'ml', 'tag-slug' => 'ml','description' => __( 'Milliliter', $td ) ),
							  array( 'tag-name' => 'm',  'tag-slug' => 'm', 'description' => __( 'Meter', $td ) ),
							  array( 'tag-name' => 'cm', 'tag-slug' => 'cm','description' => __( 'Zentimeter', $td ) ),
		);


		$default_product_attributes	= array(
										array(  'attribute_name'	=> mb_strtolower( __( 'Maßeinheit', $td ) ),
												'attribute_label'	=> __( 'Maßeinheit', $td ),
												'attribute_type'	=> 'select',
												'elements'			=> $scale_units,
										)
									);

		return apply_filters( 'woocommerce_de_default_product_attributes',  $default_product_attributes );
	}
}
?>