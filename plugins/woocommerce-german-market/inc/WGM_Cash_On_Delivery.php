<?php
/**
 * Cash On Delivery Woocommerce_de Version
 * Removes the Builtin Version of Woocommerce COD Gateway
 * 
 * @author jj, ap
 */
class WGM_Cash_On_Delivery {
	
	/**
	 * Remove standard cash on delivery
	 *
	 * @uses globals $woocommerce
	 * @access public
	 */
	public static function remove_standard_cod( $available_gateways ) {

        foreach( $available_gateways as $c => $gateway ){
            if( $gateway == 'WC_Gateway_COD' ){
                unset( $available_gateways[$c] );
            }
        }

        return $available_gateways;
	}


	/**
	* add the german COD gateway
	*
	* 
	* @static	
	* @access	public
	* @uses		get_option
	* @param 	array $gateways
	* @return	array gateways
	*/
	public static function add_cash_on_delivery_gateway( $gateways ) {
		
		global $woocommerce;

		require_once dirname( __FILE__ ) . '/../gateways/WGM_Gateway_Cash_on_delivery_v2.php';
		$gateways[] = 'WGM_Gateway_Cash_on_delivery_v2';
		
		
		// set the fee for the cash on delivery payment method
		$cod_settings = get_option( 'woocommerce_cash_on_delivery_settings' );
		WGM_Gateways::set_gateway_fee( 'cash_on_delivery' , $cod_settings[ 'woocommerce_cash_on_delivery_fee' ] );

		return $gateways;
	}	
}
?>