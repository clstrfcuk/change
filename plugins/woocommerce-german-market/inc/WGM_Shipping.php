<?php


class WGM_Shipping {

	/**
	 * Recalculate the split tax for the shipping-methods
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_package_rates
	 *
	 * @param   array $rates
	 * @return  array $rates
	 */
	public static function add_taxes_to_package_rates( $rates ) {

		if ( WGM_Tax::is_kur() ) {
			return $rates;
		}

		// looping through all packages to calculate the new taxes
		foreach ( $rates as $key => $rate ) {

			// no costs defined and taxes are empty?
			if ( $rate->cost == 0 && empty( $rate->taxes ) ) {
				continue;
			}

			// getting the correct calculated taxes for the package
			$new_rates = WGM_Tax::calculate_split_rate( $rate->cost );

			// reset the taxes for new assignment
			$rate->taxes = array();
			foreach ( $new_rates[ 'rates' ] as $rate_id => $item ) {
				$rate->taxes[ $rate_id ] = $item['sum'];
			}

			// re-assign the rate to the package
			$rates[ $key ] = $rate;
		}

		return $rates;
	}

	/**
	 * Remove the taxes when "kur" (*K*lein*u*nternehmer*r*egelung) is enabled
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_get_shipping_tax
	 *
	 * @param   int $taxes
	 * @return  int $taxes
	 */
	public static function remove_kur_shipping_tax( $taxes ){
		if( WGM_Tax::is_kur() ) {

			$taxes = 0;
		}
		return $taxes;
	}

	/**
	 * Adding the taxes to shipping method
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_cart_shipping_method_full_label
	 *
	 * @param   string $label
	 * @param   stdClass $method
	 *
	 * @return  string $label
	 */
	public static function add_shipping_tax_notice( $label, $method ){

		if( WGM_Tax::is_kur() ){
			return $label;
		}

		$label = $method->label;
		$rates = WGM_Tax::calculate_split_rate( $method->cost );

		if ( $method->cost > 0 ) {
			if ( WC()->cart->tax_display_cart == 'excl' ) {
				$label .= ': ' . wc_price( $method->cost );
			}
			else {
				$label .= ': ' . wc_price( $method->cost + $rates[ 'sum' ] );
			}

			// append the split taxes to shipping-string
			$label .= WGM_Tax::get_split_tax_html( $rates, WC()->cart->tax_display_cart );



		}
		else if ( $method->id !== 'free_shipping' ) {
			$label .= ' (' . __( 'Free', 'woocommerce' ) . ')';
		}

		return apply_filters( 'wgm_cart_shipping_method_full_label', $label, $method, $rates );

	}

	/**
	 * Adding taxes to shipping to output
	 *
	 * @wp-hook woocommerce_order_shipping_to_display
	 *
	 * @param   string $shipping
	 * @param   WC_Order $order
	 *
	 * @return  string $shipping
	 */
	public static function shipping_tax_for_thankyou( $shipping, WC_Order $order ) {

		if( WGM_Tax::is_kur() ){
			return $shipping;
		}

		if ( $order->order_shipping > 0 ) {

			$rates = WGM_Tax::calculate_split_rate( $order->order_shipping, $order );

			$wc_price_args = array(
				'currency' => $order->get_order_currency()
			);

			$shipping = $order->get_shipping_method() . ': ';

			if ( $order->tax_display_cart === 'excl' ) {
				// Show shipping excluding tax
				$shipping .= wc_price( $order->order_shipping, $wc_price_args );
			}
			else {
				// Show shipping including tax
				$shipping .= wc_price( $order->order_shipping + $rates[ 'sum' ], $wc_price_args );
			}

			$shipping .= WGM_Tax::get_split_tax_html( $rates, $order->tax_display_cart );

		}
		else if ( $order->get_shipping_method() ) {
			$shipping = $order->get_shipping_method();
		}
		else {
			$shipping = __( 'Free!', 'woocommerce' );
		}

		return apply_filters( 'wgm_order_shipping_to_display', $shipping, $order );
	}
}