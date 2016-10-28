<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Cash on Delivery Gateway
 *
 * Provides a Cash on Delivery Payment Gateway.
 *
 * @class 		WC_Gateway_COD
 * @extends		WC_Payment_Gateway
 * @version		2.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		Patrick Garman, Julian Jöris ( changes for WGM )
 */
class WGM_Gateway_Cash_on_delivery_v2 extends WC_Payment_Gateway {

    /**
     * Init Payment Gateway
     */
    function __construct() {
		$this->id           = 'cash_on_delivery';
		$this->icon         = apply_filters( 'woocommerce_cod_icon', '' );
		$this->method_title = __( 'Cash on Delivery', 'woocommerce' );
		$this->has_fields   = false;

		// Load the settings
		$this->init_form_fields();
		$this->init_settings();

		// Get settings
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->instructions       = $this->get_option( 'instructions' );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
	    $this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes' ? true : false;

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_cod', array( $this, 'thankyou' ) );
	}


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	function admin_options() {
		?>
		<h3><?php _e('Cash on Delivery','woocommerce'); ?></h3>
		<p><?php _e('Have your customers pay with cash (or by other means) upon delivery.', 'woocommerce' ); ?></p>
		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table> <?php
	}


	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {
		global $woocommerce;

		$shipping_methods = array();

		if ( is_admin() )
			foreach ( $woocommerce->shipping->load_shipping_methods() as $method ) {
				$shipping_methods[ $method->id ] = $method->get_title();
			}

		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable COD', 'woocommerce' ),
				'label' => __( 'Enable Cash on Delivery', 'woocommerce' ),
				'type' => 'checkbox',
				'description' => '',
				'default' => 'no'
			),
			'title' => array(
				'title' => __( 'Title', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Payment method title that the customer will see on your website.', 'woocommerce' ),
				'default' => __( 'Cash on Delivery', 'woocommerce' ),
				'desc_tip'      => true,
			),
			'description' => array(
				'title' => __( 'Description', 'woocommerce' ),
				'type' => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your website.', 'woocommerce' ),
				'default' => __( 'Pay with cash upon delivery.', 'woocommerce' ),
			),
			'instructions' => array(
				'title' => __( 'Instructions', 'woocommerce' ),
				'type' => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'woocommerce' ),
				'default' => __( 'Pay with cash upon delivery.', 'woocommerce' )
			),
			'woocommerce_cash_on_delivery_fee' => array(
				'title' => __( 'Gebühr', Woocommerce_German_Market::get_textdomain() ),
				'type' => 'text',
				'css'  => 'width:50px;',
				'description' =>  ' '. get_option( 'woocommerce_currency' ) . ' ' . __( 'Gebühr für das Zahlen per Nachnahme.', Woocommerce_German_Market::get_textdomain() ),
				'default' => ''
			),
			'enable_for_methods' => array(
				'title' 		=> __( 'Enable for shipping methods', 'woocommerce' ),
				'type' 			=> 'multiselect',
				'class'			=> 'chosen_select',
				'css'			=> 'width: 450px;',
				'default' 		=> '',
				'description' 	=> __( 'If COD is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'woocommerce' ),
				'options'		=> $shipping_methods,
				'desc_tip'      => true,
			),
			'enable_for_virtual' => array(
				'title'             => __( 'Enable for virtual orders', 'woocommerce' ),
				'label'             => __( 'Enable COD if the order is virtual', 'woocommerce' ),
				'type'              => 'checkbox',
				'default'           => 'yes'
			)
	   );
	}

	/**
	 * Check If The Gateway Is Available For Use
	 *
	 * @access public
	 * @return bool
	 */
	function is_available() {
		global $woocommerce;

		if ( ! $this->enable_for_virtual ) {
			if ( WC()->cart && ! WC()->cart->needs_shipping() ) {
				return false;
			}

			if ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
				$order_id = absint( get_query_var( 'order-pay' ) );
				$order    = wc_get_order( $order_id );

				// Test if order needs shipping.
				$needs_shipping = false;

				if ( 0 < sizeof( $order->get_items() ) ) {
					foreach ( $order->get_items() as $item ) {
						$_product = $order->get_product_from_item( $item );

						if ( $_product->needs_shipping() ) {
							$needs_shipping = true;
							break;
						}
					}
				}

				$needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

				if ( $needs_shipping ) {
					return false;
				}
			}
		}

		if ( ! empty( $this->enable_for_methods ) ) {

			if ( is_wc_endpoint_url( get_option( 'woocommerce_checkout_pay_endpoint' ) ) ) {

                $order_id = absint( get_query_var( 'order-pay' ) );
				$order = new WC_Order( $order_id );

				if ( ! $order->shipping_method )
					return false;

				$chosen_method = $order->shipping_method;

			} elseif ( empty( $woocommerce->session->chosen_shipping_methods ) ) {
				return false;
			} else {
				$chosen_method = $woocommerce->session->chosen_shipping_methods;
			}

			$found = false;


			foreach ( $this->enable_for_methods as $method_id ) {
				if ( ( is_string( $chosen_method ) && strpos( $chosen_method, $method_id ) === 0 ) ||
					 ( is_array( $chosen_method ) && in_array( $method_id, $chosen_method ) ) ) {
					$found = true;
					break;
				}
			}

			if ( ! $found )
				return false;
		}

		return parent::is_available();
	}


	/**
	 * Process the payment and return the result
	 *
	 * @access public
	 * @param int $order_id
	 * @return array
	 */
	function process_payment ($order_id) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status('on-hold', __( 'Payment to be made upon delivery.', 'woocommerce' ));

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$woocommerce->cart->empty_cart();

		if( isset( $_SESSION['_chosen_payment_method']) )
			unset( $_SESSION['_chosen_payment_method'] );
		if( isset( $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] ) )
			unset( $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] );

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $order->get_checkout_order_received_url()
		);
	}


	/**
	 * Output for the order received page.
	 *
	 * @access public
	 * @return void
	 */
	function thankyou() {
		echo $this->instructions != '' ? wpautop( $this->instructions ) : '';
	}

}