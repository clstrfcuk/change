<?php

/**
 * Class WGM_Fee
 *
 * This class will fix the taxes for fee "Nachnahme" in "review-order"
 * and after creating the order
 *
 * @author  ChriCo
 */
class WGM_Fee {

	/**
	 * Calculating the split tax for fee on ajax callback in backend on "update tax"/"update sum"
	 *
	 * @wp-hook	woocommerce_saved_order_items
	 *
	 * @param	int $order_id
	 * @return	void
	 */
	public static function re_calculate_tax_on_save_order_items( $order_id ) {

		$order = new WC_Order( $order_id );

		// getting all fees and remove them from order
		$all_fees = $order->get_fees();
		$order->remove_order_items( 'fee' );

		// loop through all shipping fees and create new ones with the split tax
		foreach ( $all_fees as $fee ) {

			$taxes = WGM_Tax::calculate_split_rate( $fee[ 'line_total' ], $order );

			$new_fee            = new stdClass();
			$new_fee->name      = $fee[ 'name' ];
			$new_fee->type      = $fee[ 'type' ];
			$new_fee->taxable   = TRUE;
			$new_fee->tax_class = $fee[ 'tax_class' ];
			$new_fee->amount    = $fee[ 'line_total' ];
			$new_fee->tax       = $taxes[ 'sum' ];

			foreach ( $taxes[ 'rates' ] as $tax_id => $tax ) {
				$new_fee->taxes[ $tax_id ]    = $tax[ 'sum' ];
				$new_fee->tax_data[ $tax_id ] = $tax[ 'sum' ];
			}

			// adding the new fee to order
			$order->add_fee( $new_fee );
		}
	}

	/**
	 * Adding Fee to gateway-Page second-checkout to display the taxes
	 *
	 * @wp-hook woocommerce_cart_calculate_fees
	 *
	 * @param   WC_Cart $cart
	 * @return  void
	 */
	public static function add_fee_to_gateway_page( WC_Cart $cart ){
		$avail = WC()->payment_gateways->get_available_payment_gateways();

		foreach( WGM_Gateways::get_gateway_fees() as $gateway => $fee ){

			if( isset($_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ]) &&
			    $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] == $gateway
			){

				if( !array_key_exists( $gateway, $avail ) ){
					continue;
				}

				$g = $avail[ $gateway ];

				$fee = str_replace( ',', '.', $fee );

				$title = __( $g->title , Woocommerce_German_Market::get_textdomain() );

				$tax_class_hack = 'wgm_' . $fee;

				if( get_option('woocommerce_tax_display_cart') == 'excl' || get_option('woocommerce_tax_display_cart', '' ) == '' ) {
					$cart->add_fee( $title, $fee, false, $tax_class_hack );
				}
				else if( get_option('woocommerce_tax_display_cart') == 'incl' ) {
					$cart->add_fee( $title, $fee, false, $tax_class_hack );
				}
			}
		}
	}

	/**
	 * Adds the Fee with tax-string to review-order- and cart-totals-Template
	 *
	 * @wp-hook woocommerce_cart_totals_fee_html
	 *
	 * @param   string $fee_html
	 * @param   stdClass $fee
	 *
	 * @return  string $fee_html
	 */
	public static function show_gateway_fees_tax( $fee_html, $fee ){

		if( WGM_Tax::is_kur() ){
			return $fee_html;
		}

		if( ! empty( $fee->tax_class ) && substr( $fee->tax_class, 0, 4 ) == 'wgm_' ) {
			$amount = substr( $fee->tax_class, 4 );
		}
		else {
			$amount = $fee->amount;
		}

		$rates  = WGM_Tax::calculate_split_rate( $amount );

		$fee_html .= WGM_Tax::get_split_tax_html( $rates, WC()->cart->tax_display_cart );

		return apply_filters( 'wgm_show_gateway_fees_tax', $fee_html, $fee );
	}

	/**
	 * Adding the correct split taxes to the fee-object.
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_cart_calculate_fees
	 *
	 * @param   WC_Cart $cart   copied reference of the cart to manipulate the fee-tax
	 * @return  void
	 */
	public static function add_taxes_to_fee( WC_Cart $cart ){

		if( WGM_Tax::is_kur() ){
			return;
		}

		// loop through all fees to add the correct tax
		foreach( $cart->fees as $k => $fee ) {

			// getting the split taxes for the fee
			$taxes = WGM_Tax::calculate_split_rate( $fee->amount, $cart );

			// calculating the tax-sum and adding the tax-positions to the fee
			foreach( $taxes[ 'rates' ] as $tax_id => $tax ){
				$fee->taxes[ $tax_id ] = $tax['sum'];
				$fee->tax_data[ $tax_id ] = $tax['sum'];
			}

			// attaching the tax_sum to the fee
			$fee->tax = $taxes[ 'sum' ];

			// re-assign the fee to the cart
			$cart->fees[ $k ] = $fee;
		}

	}

	/**
	 * Adding the split taxes to fee order_item which is called
	 * in get_order_item_totals() for thankyou-page, email-template, ..
	 *
	 * @author  Chrico
	 *
	 * @wp-hook woocommerce_get_order_item_totals
	 *
	 * @param   array $items    contains all order items for display
	 * @param   WC_Order $order contains the complete order-Object
	 *
	 * @return  array $items
	 */
	public static function add_tax_string_to_fee_order_item( $items, WC_Order $order ) {

		if( WGM_Tax::is_kur() ){
			return $items;
		}


		// looping through all fees to fix the text-string which is in "value"
		foreach( $order->get_fees() as $key => $fee ) {

			// in $items the fee is saved with {fee_$key)
			$search_key         = 'fee_' . $key;

			if ( !array_key_exists( $search_key, $items ) ) {
				continue;
			}

			$taxes = WGM_Tax::calculate_split_rate( $fee[ 'line_total' ], $order );

			// append the tax-messages to the value
			$items[ $search_key ][ 'value' ] .= WGM_Tax::get_split_tax_html( $taxes, $order->tax_display_cart );

		}

		return $items;
	}

	/**
	 * Adds the fee-taxes to the total sum on cart, review-order and second-checkout
	 * WooCommerce only calculates: cart_contents_total + tax_total + shipping_tax_total + shipping_total - discount_total + fee_total
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_calculated_total
	 *
	 * @param   int $total
	 * @param   WC_Cart $cart
	 *
	 * @return  int $total
	 */
	public static function add_fee_taxes_to_total_sum( $total, WC_Cart $cart ) {

		if( WGM_Tax::is_kur() ){
			return $total;
		}

		foreach( $cart->get_fees() as $fee ){
			$total = $total + $fee->tax;
		}
		return $total;
	}

	/**
	 * Adding the Fee taxes to the cart total taxes string (incl./excl. taxes).
	 * The key of the taxes is the {rate_id} (unique id of database-column)
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_cart_get_taxes
	 *
	 * @param   array $taxes
	 * @param   WC_Cart $cart
	 *
	 * @return  array $taxes
	 */
	public static function add_fee_to_cart_tax_totals( $taxes, WC_Cart $cart ){

		if( WGM_Tax::is_kur() ){
			return $taxes;
		}

		// looping through all fees in cart
		foreach ( $cart->get_fees() as $fee ) {
			if ( ! empty( $fee->tax_data ) ) {
				// if tax is not empty, loop through all taxes and add them to taxes array
				foreach ( $fee->tax_data as $rate_id => $tax ) {
					if ( !array_key_exists( $rate_id, $taxes ) ) {
						$taxes[ $rate_id ] = 0;
					}
					$taxes[ $rate_id ] += $tax;
				}
			}
		}

		return $taxes;
	}


	/**
	 * Adds the fee taxes to the tax_totals-array.
	 * The key of $tax_totals is the unique WC_Tax::get_rate_code( $rate_id );
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_order_tax_totals
	 *
	 * @param   array $tax_totals
	 * @param   WC_Order $order
	 *
	 * @return  array $tax_totals
	 */
	public static function add_fee_to_order_tax_totals( $tax_totals, WC_Order $order ){

		if( WGM_Tax::is_kur() ){
			return $tax_totals;
		}

		// looping through all existing fees
		foreach( $order->get_fees() as $fee ) {

			$taxes = WGM_Tax::calculate_split_rate( $fee['line_total'], $order );

			// looping through all found taxes
			foreach( $taxes[ 'rates' ] as $rate_id => $item ) {

				// getting the unique rate_code
				$rate_code = WC_Tax::get_rate_code( $rate_id );

				if ( !array_key_exists( $rate_code, $tax_totals ) ) {
					continue;
				}

				// add the new amount to the current amount
				$new_amount                         = $tax_totals[ $rate_code ]->amount + $item[ 'sum' ];
				$tax_totals[ $rate_code ]->amount   = $new_amount;

				// create the new formatted amount
				$tax_totals[ $rate_code ]->formatted_amount = wc_price(
					wc_round_tax_total( $new_amount ),
					array('currency' => $order->get_order_currency() )
				);

			}
		}

		return $tax_totals;
	}

}