<?php
/**
 * Payment Gateways
 * 
 * @author jj, ap
 */
Class WGM_Gateways {
	
	public static $payment_gateway_fees = array();
	
	/**
	* Set the fee for a payment gateway to calculate the totals
	*
	* @param string $gateway_id
	* @param float $fee
	*/
	public static function set_gateway_fee( $gateway_id, $fee ) {
		WGM_Gateways::$payment_gateway_fees [ $gateway_id ] = $fee;
	}

	/**
	* get the fee for a payment gateway to calculate the totals
	*
	* @access	public
	* @static
	* @param	string $gateway_id
	* @return	string gateway fee, or 0 if not exists
	*/
	public static function get_gateway_fee( $gateway_id ) {

		if( isset( WGM_Gateways::$payment_gateway_fees [ $gateway_id ] ) && is_numeric( WGM_Gateways::$payment_gateway_fees [ $gateway_id ] ) )
			return WGM_Gateways::$payment_gateway_fees [ $gateway_id ];
		else
			return 0;
	}


    public static function get_gateway_fees(){
        return self::$payment_gateway_fees;
    }
	
	/**
	* Determines if the gateway fees should be displayed
	* true, if payment_method exists in session, $_POST or order
	*
	* @access public
	* @author jj
	* @static
	* @param string $fee_option
	* @param string $gateway_id
	* @return bool TRUE if gateway fee exists, else false
	*/
	public static function gateway_fee_exists( $fee_option, $gateway_id ) {

		global $order, $page_id;
		
		$in_first_checkout_session = ( ( isset( $_SESSION[ '_chosen_payment_method' ] ) && $_SESSION[ '_chosen_payment_method' ] == $gateway_id )  );
		$in_checkout               = ( !isset( $_SESSION[ '_chosen_payment_method' ] ) && $page_id == get_option( 'woocommerce_checkout_page_id' ) && get_option( 'woocommerce_default_gateway' ) == $gateway_id );
		$has_fee                   = ( 0 != WGM_Gateways::get_gateway_fee( $fee_option ) );
		$in_post                   = ( isset( $_POST[ 'payment_method' ] )  && $_POST[ 'payment_method' ] == $gateway_id );
		$in_session                = ( isset( $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] ) && $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] == $gateway_id );
		$in_order                  = isset ( $order->payment_method ) && $order->payment_method == $gateway_id;
		
		return ( $has_fee && ( $in_post || $in_session || $in_order || $in_first_checkout_session || $in_checkout ) );
	}
}
?>