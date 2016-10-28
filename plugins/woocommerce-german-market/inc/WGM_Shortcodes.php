<?php
/**
 * Shortcodes
 * 
 * @author jj,ap
 */
Class WGM_Shortcodes {
	
	/**
	* Shortcode for the amount of days to withdraw to include in Disclaimer page
	* 
	* @access	public
	* @static	
	* @uses		get_option
	* @return	string days and singular/plural of day
	*/
	public static function add_shortcode_disclaimer_deadline() {

		$frist = get_option( WGM_Helper::get_wgm_option( 'widerrufsfrist' ) );
		$returnvalue  = $frist . ' ';
		$returnvalue .=  ( intval( $frist ) >= 2 ) ? __( 'Tage', Woocommerce_German_Market::get_textdomain() ) :  __( 'Tag', Woocommerce_German_Market::get_textdomain() );
		return $returnvalue;
	}

	/**
	* withdraw address shortcode for the disclaimer page
	*
	* @access	public
	* @uses		get_option
	* @static
	* @return 	string withdraw address
	*/
	public static function add_shortcode_disclaimer_address_data() {
		return nl2br( get_option( WGM_Helper::get_wgm_option( 'widerrufsadressdaten' ) ) );
	}
	
	/**
	* Shortcode for the second checkout page in the german Version
	* 
	* @access	public
	* @static
	* @return	string template conents
	*/
	public static function add_shortcode_check() {

		ob_start();

        WGM_Template::load_template( 'second-checkout2.php' );
			
		$tpl = ob_get_contents();
		ob_end_clean();

		return $tpl;
	}
}

?>