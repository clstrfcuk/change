<?php
/**
 * Plugin Name:     WooCommerce German Market
 * Description:     Woocommerce Plugin für Features und Rechtssicherheit für den deutschsprachigen Raum
 * Author:          Inpsyde GmbH
 * Version:         2.4.11
 * Licence:         GPLv3
 * Author URI:      http://inpsyde.com
 * Text Domain:     WooCommerce-German-Market
 * Domain Path:     /languages
 * Upgrade Check:   none
 * Last Change:     23.02.2015 10:35
 */

class Woocommerce_German_Market {

    /**
     * Plugin version
     * @var string
     */
    static public $version = "2.4.11";

    /**
     * Singleton object holder
     * @var mixed
     */
    static private $instance = NULL;

    /**
     * @var mixed
     */
    static public $plugin_name = NULL;

    /**
     * @var mixed
     */
    static public $textdomain = NULL;

    /**
     * @var mixed
     */
    static public $plugin_base_name = NULL;

    /**
     * @var mixed
     */
    static public $plugin_url = NULL;

    /**
     * @var string
     */
    static public $plugin_filename = __FILE__;


    /**
     * Plugin constructor. Init basic plugin behaviour and register hooks.
     */
    public function __construct () {

		// start the session. Woocommerce has its own session management since 2.0
		// Perhaps we can use it in the future, and remove PHP Sessions completely
		Woocommerce_German_Market::start_session_if_not_exist();

		// set the textdomain variable for Auto Updater
		self::$textdomain = self::get_textdomain();

		// The Plugins Name
		self::$plugin_name = $this->get_plugin_header( 'Name' );

		// The Plugins Basename
		self::$plugin_base_name = plugin_basename( __FILE__ );

		// Load the textdomain
		$this->load_plugin_textdomain();

		add_filter( 'init',														array( 'Woocommerce_German_Market', 'load_styles' ) );
		add_filter( 'init',														array( 'Woocommerce_German_Market', 'init' ) );

		add_filter( 'wp_enqueue_scripts',										array( 'Woocommerce_German_Market', 'enqueue_frontend_scripts' ), 15 );
		add_filter( 'admin_enqueue_scripts',									array( 'Woocommerce_German_Market', 'enqueue_admin_scripts' ), 15 );

		add_filter( 'admin_notices',											array( 'WGM_Installation', 'install_notice' ) );
		add_filter( 'admin_notices',											array( 'WGM_Installation', 'upgrade_deliverytimes_notice' ) );

		add_filter( 'woocommerce_admin_field_string',							array( 'WGM_Template', 'add_admin_field_string_template' ), 10, 1 );
		add_filter( 'woocommerce_cart_contents',								array( 'WGM_Template', 'add_shop_table_cart' ) );
		add_filter( 'woocommerce_widget_shopping_cart_before_buttons',			array( 'WGM_Template', 'add_shopping_cart' ) );
		add_filter( 'woocommerce_review_order_before_submit',					array( 'WGM_Template', 'add_wgm_checkout_session' ) );
		add_filter( 'woocommerce_review_order_before_submit',					array( 'WGM_Template', 'add_review_order' ) );
		add_filter( 'woocommerce_after_shop_loop_item',						 	array( 'WGM_Template', 'woocommerce_de_after_shop_loop_item' ) );
		add_filter( 'woocommerce_billing_fields',								array( 'WGM_Template', 'billing_fields' ) );
		add_filter( 'woocommerce_shipping_fields',								array( 'WGM_Template', 'shipping_fields' ) );
		add_filter( 'woocommerce_single_product_summary',						array( 'WGM_Template', 'add_template_loop_shop' ), 11 );
		add_filter( 'woocommerce_order_item_name',							    array( 'WGM_Template', 'add_shipping_time_to_product_title' ), 10, 2 );
		add_filter( 'woocommerce_order_button_text',							array( 'WGM_Template', 'change_order_button_text' ), 1, 1 );
		add_filter( 'woocommerce_after_checkout_validation',					array( 'WGM_Template', 'do_de_checkout_after_validation' ), 1, 1 );
		add_filter( 'woocommerce_checkout_billing',								array( 'WGM_Template', 'second_checkout_form_billing_remove' ), 5 );
		add_filter( 'woocommerce_checkout_shipping',							array( 'WGM_Template', 'second_checkout_form_shipping_remove' ), 5 );
		add_filter( 'admin_menu',												array( 'WGM_Template', 'remove_lieferzeit_taxonomy_metabox' ), 1 );
		add_filter( 'woocommerce_after_cart_totals',							array( 'WGM_Template', 'kur_notice' ), 1 );
		add_filter( 'woocommerce_review_order_after_order_total',				array( 'WGM_Template', 'kur_review_order_notice' ), 1 );
		add_filter( 'woocommerce_get_order_item_totals',						array( 'WGM_Template', 'kur_review_order_item' ), 10, 1 );

        add_filter( 'woocommerce_checkout_process',                             array( 'WGM_Template', 'shipping_address_check' ), 10, 1 );
        add_filter( 'woocommerce_product_title',                                array( 'WGM_Template', 'add_virtual_product_notice' ), 1, 2 );

		add_filter( 'woocommerce_single_product_summary',						array( 'WGM_Template', 'woocommerce_de_price_with_tax_hint_single' ) , 7 );
		add_filter( 'woocommerce_after_shop_loop_item_title',					array( 'WGM_Template', 'woocommerce_de_price_with_tax_hint_loop' ) , 5 );
		add_filter( 'woocommerce_after_cart_totals',							array( 'WGM_Template', 'woocommerce_after_cart_totals' ) );
		add_filter( 'woocommerce_locate_template',								array( 'WGM_Template', 'add_woocommerce_de_templates' ),9 ,3 );
		add_filter( 'woocommerce_checkout_init', 						        array( 'WGM_Template', 'add_mwst_rate_to_product_item_init' ) );
		add_filter( 'woocommerce_order_formatted_line_subtotal',				array( 'WGM_Template', 'add_mwst_rate_to_product_order_item' ), 10 ,3 );
		add_filter( 'woocommerce_email_after_order_table',						array( 'WGM_Template', 'add_paymentmethod_to_mails' ), 10, 2 );
        add_filter( 'woocommerce_order_item_quantity_html',					    array( 'WGM_Template', 'add_product_short_desc_to_order_title' ), 10, 2 );
        add_filter( 'woocommerce_order_item_quantity_html',					    array( 'WGM_Template', 'add_product_function_desc' ), 11, 2 );
        add_filter( 'wgm_email_after_item_name',					            array( 'WGM_Template', 'add_product_short_desc_to_order_title_mail' ), 11, 2 );
        add_filter( 'woocommerce_checkout_cart_item_quantity',					array( 'WGM_Template', 'add_product_short_desc_to_checkout_title' ), 10, 2 );
        add_filter( 'woocommerce_checkout_cart_item_quantity',					array( 'WGM_Template', 'add_product_function_desc' ), 11, 2 );
        add_filter( 'woocommerce_single_product_summary',						array( 'WGM_Template', 'show_extra_costs_eu' ) , 11 );
        add_filter( 'woocommerce_single_product_summary',						array( 'WGM_Template', 'add_digital_product_prerequisits' ) , 20 );


		add_filter( 'woocommerce_package_rates', 					            array( 'WGM_Template', 'hide_standard_shipping_when_free_is_available' ) , 10 );
		add_filter( 'wgm_after_single_variation', 					            array( 'WGM_Template', 'add_mwst_rate_to_variation_product_price' ) , 10 );
		add_action( 'woocommerce_cart_totals_before_order_total',				array( 'WGM_Template', 'add_mwst_rate_to_cart_totals' ) );
		add_action( 'woocommerce_review_order_before_order_total',				array( 'WGM_Template', 'add_mwst_rate_to_cart_totals' ) );
		add_action( 'woocommerce_cart_totals_after_order_total',				array( 'WGM_Template', 'remove_mwst_rate_to_cart_totals' ) );
		add_action( 'woocommerce_review_order_after_order_total',				array( 'WGM_Template', 'remove_mwst_rate_to_cart_totals' ) );

        add_action( 'woocommerce_order_get_items',				                array( 'WGM_Template', 'filter_order_item_name' ), 10, 2 );

		add_action( 'wp_ajax_update_variation', 								array( 'WGM_Template', 'update_mwst_rate_to_variation_product_price' ), 10 );
		add_action( 'wp_ajax_nopriv_update_variation', 							array( 'WGM_Template', 'update_mwst_rate_to_variation_product_price' ), 10 );


		add_filter( 'woocommerce_order_actions_start',                          array( 'WGM_Helper', 'deactivate_pay_for_order_button' ) );
		add_filter( 'body_class',												array( 'WGM_Helper', 'add_checkout_body_classes' ) );

		add_filter( 'woocommerce_payment_gateways',				                array( 'WGM_Cash_On_Delivery', 'remove_standard_cod' ), 1 );
		add_filter( 'woocommerce_payment_gateways',								array( 'WGM_Cash_On_Delivery', 'add_cash_on_delivery_gateway' ) );


		add_filter( 'woocommerce_settings_tabs_preferences_de',				 	array( 'WGM_Settings', 'add_settings_tab_content' ) );
		add_filter( 'woocommerce_update_options_preferences_de',				array( 'WGM_Settings', 'update_settings_tab_content' ) );
		add_filter( 'woocommerce_product_write_panel_tabs',					 	array( 'WGM_Settings', 'add_product_write_panel_tabs' ) );
		add_filter( 'woocommerce_product_write_panels',						 	array( 'WGM_Settings', 'add_product_write_panels' ) );
		add_filter( 'woocommerce_process_product_meta',							array( 'WGM_Settings', 'add_process_product_meta' ), 10, 2 );
		add_filter( 'woocommerce_email_settings',								array( 'WGM_Settings', 'imprint_email_settings' ) );
		add_filter( 'woocommerce_settings_tabs_array',							array( 'WGM_Settings', 'add_setting_tab' ) );
		add_filter( 'woocommerce_register_taxonomy',							array( 'WGM_Settings', 'register_taxonomies' ) );

		add_filter( 'woocommerce_unforce_ssl_checkout',							array( 'WGM_Settings', 'unforce_ssl_checkout' ) );

		add_filter( 'woocommerce_register_taxonomy', 							array( 'WGM_Defaults', 'register_default_lieferzeiten_strings' ) );

		add_shortcode( 'woocommerce_de_disclaimer_deadline',					array( 'WGM_Shortcodes', 'add_shortcode_disclaimer_deadline' ) );
		add_shortcode( 'woocommerce_de_disclaimer_address_data',				array( 'WGM_Shortcodes', 'add_shortcode_disclaimer_address_data' ) );
		add_shortcode( 'woocommerce_de_check',									array( 'WGM_Shortcodes', 'add_shortcode_check' ) );

		add_filter( 'woocommerce_email_footer',									array( 'WGM_Email', 'email_de_footer' ), 5 );

		// deactivate "pay for order"
		remove_shortcode( 'woocommerce_pay' );

		// remove taxonomy from quickedit
		add_filter( 'default_content',											array( 'Woocommerce_German_Market', 'remove_taxonomy_from_quickedit' ) );

		add_filter( 'wgm_shipping_time_product_string', 						array( 'WGM_Helper', 'filter_deliverytimes' ), 10, 2  );

        add_filter( 'woocommerce_countries_ex_tax_or_vat', 						array( 'WGM_Helper', 'remove_woo_vat_notice' ), 10, 2  );
        add_filter( 'woocommerce_countries_inc_tax_or_vat', 					array( 'WGM_Helper', 'remove_woo_vat_notice' ), 10, 2  );

		add_filter( 'manage_edit-product_delivery_times_columns', 				array( 'WGM_Helper', 'remove_deliverytime_postcount_columns' ), 10, 2 );

	    add_filter( 'woocommerce_cart_totals_fee_html',                         array( 'WGM_Fee', 'show_gateway_fees_tax' ), 10, 2 );
	    add_filter( 'woocommerce_cart_calculate_fees',                          array( 'WGM_Fee', 'add_fee_to_gateway_page' ), 10, 1 );
	    add_filter( 'woocommerce_cart_calculate_fees',                          array( 'WGM_Fee', 'add_taxes_to_fee' ), 10, 1 );
	    add_filter( 'woocommerce_get_order_item_totals',                        array( 'WGM_Fee', 'add_tax_string_to_fee_order_item' ), 10, 2 );
	    add_filter( 'woocommerce_calculated_total',                             array( 'WGM_Fee', 'add_fee_taxes_to_total_sum' ), 10, 2 );
	    add_filter( 'woocommerce_order_tax_totals',                             array( 'WGM_Fee', 'add_fee_to_order_tax_totals' ), 10, 2 );
	    add_filter( 'woocommerce_cart_get_taxes',                               array( 'WGM_Fee', 'add_fee_to_cart_tax_totals' ), 10, 2 );
	    add_action( 'woocommerce_saved_order_items',                            array( 'WGM_Fee', 're_calculate_tax_on_save_order_items' ) );


        add_filter( 'woocommerce_chosen_method_shipping_rate',                  array( 'WGM_Helper', '_hack' ), 10, 2 );

        add_filter( 'woocommerce_cart_shipping_method_full_label',              array( 'WGM_Shipping', 'add_shipping_tax_notice' ), 10, 2);
        add_filter( 'woocommerce_order_shipping_to_display',                    array( 'WGM_Shipping', 'shipping_tax_for_thankyou' ), 10, 2);
	    add_filter( 'woocommerce_package_rates',                                array( 'WGM_Shipping', 'add_taxes_to_package_rates' ), 10 );
		add_filter( 'woocommerce_get_shipping_tax',                             array( 'WGM_Shipping', 'remove_kur_shipping_tax' ), 10 );
	    add_action( 'woocommerce_saved_order_items',                            array( 'WGM_Tax', 're_calculate_tax_on_save_order_items' ) );

        add_filter( 'woocommerce_paypal_args',                                  array( 'WGM_Helper', 'paypal_fix' ), 10, 2);

	    add_filter( 'woocommerce_review_order_before_payment',                  array( 'WGM_Helper', 'change_payment_gateway_order_button_text' ) );
	    add_filter( 'woocommerce_cart_needs_shipping',                          array( 'WGM_Helper', 'virtual_needs_shipping' ) );

	    add_filter( 'pre_set_transient_woocommerce_cache_excluded_uris',        array('Woocommerce_German_Market', 'exclude_checkout_from_cache') );


	    // require Auto Updater
		require_once 'pro/class-Woocommerce_German_Market_Auto_Update.php';

	    $plugin_data = new stdClass();
	    $plugin_data->plugin_slug      = 'woocommerce-german-market';
	    $plugin_data->plugin_name      = self::$plugin_name;
	    $plugin_data->plugin_base_name = self::$plugin_base_name;
	    $plugin_data->plugin_url       = self::$plugin_url;
	    $plugin_data->version          = self::$version;
	    $autoupdate = Woocommerce_German_Market_Auto_Update::get_instance();
	    $autoupdate->setup( $plugin_data );

	}

	/**
	* Creates an Instance of this Class
	*
	* @access public
	* @since 0.0.1
	* @return Woocommerce_German_Market
	*/
	public static function get_instance() {

		if ( NULL === self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Load the localization
	 *
	 * @since	0.5
	 * @access	public
	 * @uses	load_plugin_textdomain, plugin_basename
	 * @return	void
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( self::get_textdomain(), FALSE, dirname( plugin_basename( __FILE__ ) ) . self::get_textdomain_path() );
	}

	/**
	* Get a value of the plugin header
	*
	* @access	protected
	* @param	string $value
	* @uses		get_plugin_data, ABSPATH
	* @return	string The plugin header value
	*/
	protected function get_plugin_header( $value = 'TextDomain' ) {

		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[ $value ];

		return $plugin_value;
	}

	/**
	* registers the css styles
	*
	* @static
	* @uses		get_option, wp_register_style, wp_enqueue_style, plugins_url
	* @access	public
	* @return	void
	*/
	public static function load_styles() {

		// Admin styles
		if ( is_admin() ) {

			// load activation css
			if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_options_installed' ) ) !== 1 )  {
				wp_register_style( 'woocommerce-de-activation-style',
                    plugins_url( '/css/activation.css', plugin_basename( __FILE__ ) ) );
				wp_enqueue_style( 'woocommerce-de-activation-style' );
			}

		// Frontend styles
		} else {

			if ( get_option( 'load_woocommerce-de_standard_css' ) === 'on' ) {
				wp_register_style( 'woocommerce-de_frontend_styles',
                    plugins_url( '/css/frontend.css', plugin_basename( __FILE__ ) ), array(), '1.0' );

				wp_enqueue_style( 'woocommerce-de_frontend_styles' );
			}
		}
	}

	/**
	* enqueue admin scripts and pass variables into the global scope
	*
	* @static
	* @uses		wp_enqueue_script, wp_localize_script, plugin_dir_url
	* @access 	public
	* @return	void
	*/
	public static function enqueue_admin_scripts() {
		wp_enqueue_script( 'woocommerce_de_admin',
            plugins_url( '/js/WooCommerce-German-Market-Admin.js', plugin_basename( __FILE__ ) ), array( 'jquery', 'woocommerce_admin' ) );

        wp_localize_script( 'woocommerce_de_admin', 'woocommerce_product_attributes_url', admin_url() . 'edit-tags.php?taxonomy=pa_masseinheit&post_type=product' );
	}

	/**
	* enqueue frontend scripts and pass variables into the global scope
	*
	* @static
	* @uses		wp_enqueue_script, get_option, wp_localize_script, wp_get_referer, plugin_dir_url
	* @access 	public
	* @return	void
	*/
	public static function enqueue_frontend_scripts() {
		global $page_id;

		wp_enqueue_script( 'woocommerce_de_frontend',
            plugins_url( '/js/WooCommerce-German-Market-Frontend.js', plugin_basename( __FILE__ ) ), array( 'jquery', 'woocommerce' ) );

		if( $page_id == get_option( 'woocommerce_checkout_page_id' ) && strstr( WGM_Helper::get_check_url(), wp_get_referer() ) )
			wp_localize_script( 'woocommerce_de_frontend', 'woocommerce_remove_updated_totals', '1' );
		else
			wp_localize_script( 'woocommerce_de_frontend', 'woocommerce_remove_updated_totals', '0' );

        if( defined( 'ICL_LANGUAGE_CODE' ) ){
            wp_localize_script( 'woocommerce_de_frontend', 'wgm_wpml_ajax_language', ICL_LANGUAGE_CODE );
        }
	}

	/**
	* get the textdomain
	*
	* @static
	* @access	public
	* @return	string textdomain
	*/
	public static function get_textdomain() {
		if( is_null( self::$textdomain ) )
			self::$textdomain = self::get_plugin_data( 'TextDomain' );

		return self::$textdomain;
	}

	/**
	 * get the textdomain
	 *
	 * @access	public
	 * @return	string Domain Path
	 */
	public static function get_textdomain_path() {
		return self::get_plugin_data( 'DomainPath' );
	}

	/**
	* return plugin comment data
	*
	* @uses   get_plugin_data
	* @access public
	* @since  0.0.1
	* @param  $value string, default = 'Version'
	*		Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	* @return string
	*/
	private static function get_plugin_data( $value = 'Version' ) {

		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugin_data  = get_plugin_data ( __FILE__ );
		$plugin_value = $plugin_data[ $value ];

		return $plugin_value;
	}

	/**
	 * Check if current WooCommerce Version is equal or above 2.1
	 *
	 * @since	2.3
	 * @author	ap
	 * @access	public
	 * @global	$woocommerce
	 * @static
	 * @return	boolean is above or not
	 */
	public static function is_wc_2_1() {

		global $woocommerce;

		return version_compare( @$woocommerce->version , '2.1', '>=' );

	}

    /**
     * Returns plugin version
     * @since 2.3.1
     * @author ap
     * @access public
     * @static
     * @return string
     */
    public static function get_version(){
        return self::$version;
    }

	/**
	 * Starts the session, if not already started
	 *
	 * @since	1.1.5beta
	 * @author	jj
	 * @access	public
	 * @static
	 * @return	void
	 */
	public static function start_session_if_not_exist() {

		if ( session_id() === '' )
			@session_start();
	}

    /**
     * Check if the current site is wgm checkout
     * @author ap
     * @access public
     * @static
     * @return boolean
     */
	public static function is_wgm_checkout(){
		return defined( 'WGM_CHECKOUT' );
	}


    /**
     * Called when plugin is initialized
     * @author ap
     * @access public
     * @static
     */
	public static function init(){
		WGM_Helper::check_kleinunternehmerregelung();
		WGM_Installation::upgrade_deliverytimes();

        WGM_Installation::upgrade_system();
	}

	/**
	 * Remove the Taxonomy from Quickedit by using a filter that is used in get_default_post_to_edit ( which is called by $wp_list_table->inline_edit() on edit.php )
	 *
	 * @access public
	 * @since  0.0.1
	 * @param $post_content
	 * @return Woocommerce_German_Market
	 */
	public static function remove_taxonomy_from_quickedit( $post_content ) {

		$screen = get_current_screen();
		// only if screen is edit-product
		if ( $screen -> id == 'edit-product' ) {
			global $wp_taxonomies;
			$wp_taxonomies [ 'product_delivery_times' ]->show_ui = FALSE;
		}

		// return the $post_content because we don't need to alter it
		return $post_content;
	}

	/**
	 * Exclude second checkout page from woocommrece cache
	 * @param array $page_uris
	 * @access public
	 * @since 2.4.10
	 * @author ap, cb
	 * @wp-hook pre_set_transient_woocommerce_cache_excluded_uris
	 *
	 * @return array $page_uris
	 */
	public static function exclude_checkout_from_cache( $page_uris ) {
		$wgm_checkout_2     = absint( get_option( 'woocommerce_check_page_id' ) );
		$wgm_checkout_uri   = 'p=' . $wgm_checkout_2;

		if ( ! in_array( $wgm_checkout_uri , $page_uris ) ) {
			$page_uris[] = $wgm_checkout_uri ;
		}

		$page = get_post( $wgm_checkout_2 );
		if ( $page === null ) {
			return $page_uris;
		}

		$wgm_checkout_uri  ='/' . $page->post_name;

	     if (  ! in_array( $wgm_checkout_uri , $page_uris ) ) {
		    $page_uris[] = $wgm_checkout_uri ;
	    }

	    return $page_uris;
	}

} // end class

if ( class_exists( 'Woocommerce_German_Market' ) ) {

	add_action( 'plugins_loaded', array( 'Woocommerce_German_Market', 'get_instance' ) );

	// load modulues, and register classes
	// necessary, to have the install routines for (de)activation hooks present before plugins_loaded
	// see http://codex.wordpress.org/Function_Reference/register_activation_hook#Notes

	require_once 'inc/WGM_Loader.php';
	WGM_Loader::register();

	register_activation_hook( 	__FILE__, 		array( 'WGM_Installation', 'on_activate' ) );
	register_uninstall_hook( 	__FILE__,		array( 'WGM_Installation', 'on_uninstall' ) );
}

?>
