<?php
/**
 * Shipping Methods Display
 *
 * @author 		WooThemes, ap
 * @package 	WGM/templates/woocommerce-german-market
 * @version     2.1
 */

global $woocommerce;

// If at least one shipping method is available
if ( $available_methods ) {

	// Prepare text labels with price for each shipping method
	foreach ( $available_methods as $method ) {
		$method->full_label = $method->label;

		if ( $method->cost > 0 ) {
			if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
				$method->full_label .= ': ' . wc_price( $method->cost );
				if ( $method->get_shipping_tax() > 0 && $woocommerce->cart->prices_include_tax ) {
					$method->full_label .= ' <small>' . $woocommerce->countries->ex_tax_or_vat() . '</small>';
				}
			} else {
				$method->full_label .= ': ' . wc_price( $method->cost + $method->get_shipping_tax() );
				if ( $method->get_shipping_tax() > 0 && ! $woocommerce->cart->prices_include_tax ) {
					$method->full_label .= ' <small>' . $woocommerce->countries->inc_tax_or_vat() . '</small>';
				}
			}
		} elseif ( $method->id !== 'free_shipping' ) {
			$method->full_label .= ' (' . __( 'Free', 'woocommerce' ) . ')';
		}
		$method->full_label = apply_filters( 'woocommerce_cart_shipping_method_full_label', $method->full_label, $method );
	}

	// Print a single available shipping method as plain text
	if ( 1 === count( $available_methods ) ) {

		echo wp_kses_post( $method->full_label ) . '<input type="hidden" name="shipping_method" id="shipping_method" value="' . esc_attr( $method->id ) . '" />';
 
	// Show selected method
	} else {
		$default_method = array_shift( array_keys( $available_methods ) );
		
		if( isset( $woocommerce->session->chosen_shipping_method ) )
			$choosen_shipping_method = $woocommerce->session->chosen_shipping_method;
		else 
			$choosen_shipping_method = $default_method;
		
		echo '<input type="hidden" name="shipping_method" id="shipping_method" value="'. esc_attr( $choosen_shipping_method ) .'">';
		
		if( isset( $available_methods[ $choosen_shipping_method ] ) )
			$shipping_class = $available_methods[ $choosen_shipping_method ];
		else 
			$shipping_class = $available_methods[ $default_method ]; // if method not regocnized, use the first one
		
		echo $shipping_class->full_label;	
	}

// No shipping methods are available
} else {

	if ( ! $woocommerce->customer->get_shipping_country() || ! $woocommerce->customer->get_shipping_state() || ! $woocommerce->customer->get_shipping_postcode() ) {
		echo '<p>' . __( 'Please fill in your details to see available shipping methods.', 'woocommerce' ) . '</p>';
	} else {
		echo '<p>' . __( 'Sorry, it seems that there are no available shipping methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';
	}

}
?>