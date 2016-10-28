<?php
/**
 * Class WGM_Tax
 *
 * This class contains helper functions to calculate the tax and some formatting functions
 *
 * @author  ChriCo
 */
class WGM_Tax {

	/**
	 * Returns true if the current Shop has activated the "kur"-option (*K*lein*u*nternehmer*r*egelung).
	 *
	 * @author  ChriCo
	 *
	 * @issue   #418
	 * @return  bool true|false
	 */
	public static function is_kur(){
		return ( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) === 'on' );
	}

	/**
	 * Returns the formatted split tax html
	 *
	 * @param   array $rates
	 * @param   string $type
	 *
	 * @return  string $html
	 */
	public static function get_split_tax_html( $rates, $type ){

		$html   = '';
		$msg    = WGM_Tax::get_excl_incl_tax_string( $type );

		foreach( $rates[ 'rates' ] as $item ) {

			$decimal_length = WGM_Helper::get_decimal_length( $item['rate'] );
			$formatted_rate = number_format_i18n( (float)$item['rate'], $decimal_length );

			$html .= sprintf(
				'<br class="wgm-break" /><span class="wgm-tax product-tax">%1$s %2$s%%: %3$s</span>',
				$msg,
				$formatted_rate,
				wc_price( $item[ 'sum' ] )
			);
		}

		return apply_filters( 'wgm_get_split_tax_html', $html, $rates, $type );

	}

	/**
	 * Returns the tax string for excl/incl tax
	 *
	 * @author  ChriCo
	 *
	 * @param   string $type
	 * @return  string $msg
	 */
	public static function get_excl_incl_tax_string( $type ){
		if ( (string)$type === 'excl' ) {
			$msg = sprintf( '%s ', __( 'zusätzliche MwSt.', Woocommerce_German_Market::get_textdomain() ) );
		}
		else {
			$msg = sprintf( '%s ', __( 'enthaltene MwSt.', Woocommerce_German_Market::get_textdomain() ) );
		}
		return apply_filters( 'wgm_get_excl_incl_tax_string', $msg, $type );
	}

	/**
	 * Calculating the tax based on default rate and reduced rate
	 *
	 * @param   int $price
	 * @param   WC_Cart|WC_Order|null $cart_or_order
	 *
	 * @return  array $rates array(
	 *                          'sum'   => Integer,
	 *                          'rates  => array(
	 *                              rate_id => array(
	 *                                  'sum'       => Integer
	 *                                  'rate'      => String
	 *                                  'rate_id'   => Integer
	 *                              ),
	 *                              ...
	 *                          )
	 */
	public static function calculate_split_rate( $price, $cart_or_order = null ){
		$count = array();

		$line_items = array();
		if ( $cart_or_order === null ) {
			$line_items =  WC()->cart->get_cart();
		} else if ( is_a( $cart_or_order, 'WC_Cart' ) ) {
			$line_items = $cart_or_order->get_cart();
		} else if ( is_a( $cart_or_order, 'WC_Order' ) ) {
			$line_items = $cart_or_order->get_items();
		}

		$total = 0;
		foreach ( $line_items as $item ) {

			$product_id   = absint( $item[ 'product_id' ] );
			$variation_id = absint( $item[ 'variation_id' ] );

			if ( WGM_Helper::is_digital( $product_id ) ) {
				continue;
			}

			if ( $variation_id !== 0 ) {
				$id = $variation_id;
			} else {
				$id = $product_id;
			}

			$_product = wc_get_product( $id );

			// If the Costumer object is not available, we're most likely in an order
			if ( is_a( $cart_or_order, 'WC_Order' ) ) {
				$country    = $cart_or_order->billing_country;
				$state      = $cart_or_order->billing_state;
			} else {
				list( $country, $state, $postcode, $city ) = WC()->customer->get_taxable_address();
			}

			$tax_rate_args = array(
				'country' 	=>  $country,
				'state'       => $state,
				'tax_class'   => $_product->get_tax_class()
			);
			$tax = WC_Tax::find_rates( $tax_rate_args );

			$current_tax= current( $tax );
			$rate_id    = key( $tax );

			/**
			 * wir müssen "line_total" benutzen, denn das ist der tatsächlich Betrag nach Abzug
			 * von Rabatten/Gutscheinen auf "line_subtotal"
			 * @issue 392
			 */
			if ( array_key_exists( $rate_id, $count ) ) {
				$count[ $rate_id ][ 'total' ] += $item[ 'line_total' ];
			} else {
				$count[ $rate_id ][ 'total' ]   = $item[ 'line_total' ];
				$count[ $rate_id ][ 'rate' ]    = $current_tax[ 'rate' ];
			}

			$total += $item[ 'line_total' ];
		}

		$out = array(
			'sum'   => 0,
		    'rates' => array()
		);

		foreach ( $count as $rate_id => $item ) {

			if ( $total > 0 ) {
				$sum = ( ( $price / $total * $item[ 'total' ] ) / 100 ) * $item[ 'rate' ];
			} else {
				$sum = 0;
			}

			$out[ 'rates' ][ $rate_id ] = array(
				'sum'       => $sum,
				'rate'      => $item[ 'rate' ],
				'rate_id'   => $rate_id
			);

			$out[ 'sum' ] += $sum;
		}

		return $out;
	}

	/**
	 * Calculating the split tax on ajax callback in backend on "update tax"/"update sum"
	 *
	 * @wp-hook	woocommerce_saved_order_items
	 *
	 * @param	int $order_id
	 * @return	void
	 */
	public static function re_calculate_tax_on_save_order_items( $order_id ){

		$order = new WC_Order( $order_id );

		// get all shipping items and remove them from order
		$all_shippings = $order->get_items( 'shipping' );
		$order->remove_order_items( 'shipping' );

		$shipping_taxes = array();

		// loop through all shipping items and create new ones with the split tax
		foreach( $all_shippings as $shipping ){

			// calculating the split tax
			$taxes = WGM_Tax::calculate_split_rate( $shipping[ 'cost' ], $order );

			$new_shipping = new WC_Shipping_Flat_Rate( );
			$new_shipping->label    = $shipping[ 'name' ];
			$new_shipping->id       = $shipping[ 'method_id' ];
			$new_shipping->cost     = $shipping[ 'cost' ];
			$new_shipping->taxes    = array();
			foreach ( $taxes[ 'rates' ] as $tax_id => $tax ) {
				$new_shipping->taxes[ $tax_id ] = $tax['sum'];

				if ( ! array_key_exists( $tax_id, $shipping_taxes ) ) {
					$shipping_taxes[ $tax_id ] = 0;
				}
				$shipping_taxes[ $tax_id ] += $tax[ 'sum' ];

			}

			// assign new shipping item to order
			$order->add_shipping( $new_shipping );
		}
		// re-calculate the shipping costs
		$order->calculate_shipping();

		// remove all taxes
		$order->remove_order_items( 'tax' );

		// get all line_items and loop through them to fetch the taxes
		$line_items = $order->get_items( 'line_item' );
		$line_taxes = array();
		foreach( $line_items as $item ){

			// no line tax data is given
			if( empty( $item['line_tax_data'] ) ) {
				continue;
			}

			$taxes = maybe_unserialize( $item['line_tax_data'] );
			if ( !is_array( $taxes ) ) {
				continue;
			}

			// loop through all total taxes (subtotal-discount)
			foreach( $taxes[ 'total' ] as $rate_id => $tax_sum ){
				if( !array_key_exists( $rate_id, $line_taxes ) ){
					$line_taxes[ $rate_id ] = 0;
				}
				$line_taxes[ $rate_id ] += $tax_sum;
			}

		}

		// looping through all line_taxes and shipping taxes and saving the new tax sum
		// we don't add the fee-tax, because the fee-tax is added by another filter on display
		foreach( array_keys( $line_taxes + $shipping_taxes ) as $rate_id ) {

			$line_tax = 0;
			if( array_key_exists( $rate_id, $line_taxes ) ){
				$line_tax = $line_taxes[ $rate_id ];
			}

			$shipping_tax = 0;
			if( array_key_exists( $rate_id, $shipping_taxes ) ) {
				$shipping_tax = $shipping_taxes[ $rate_id ];
			}

			$order->add_tax(
				$rate_id,
				$line_tax,
				$shipping_tax
			);
		}

	}
}