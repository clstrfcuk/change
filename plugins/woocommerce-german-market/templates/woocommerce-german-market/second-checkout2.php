<?php
/**
 * Second Checkout Form
 * Merged from form-checkout.php review-order.php
 *
 * @author 		WooThemes, ap
 * @package 	WGM/templates/woocommerce-german-market
 * @version     2.1
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'WGM_CHECKOUT', TRUE );

global $woocommerce;

// Get checkout object
$checkout = $woocommerce->checkout();

// define as checkout process, to ensure correct price calculation
if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) define( 'WOOCOMMERCE_CHECKOUT', TRUE );


if ( ! isset( $_SESSION[ 'first_checkout_post_array' ] ) || ! $_SESSION[ 'first_checkout_post_array' ] ) {

	$message = __( 'Es wurden keine Daten übergeben. Prüfen Sie den Warenkorb.', Woocommerce_German_Market::get_textdomain() );
	echo '<p class="error">'. $message .'</a></p>';
	return;

} else {

	$message = __( 'Bitte prüfen Sie alle Daten. Schließen Sie den Vorgang dann ab.', Woocommerce_German_Market::get_textdomain() );
}

wc_add_notice( $message );

wc_print_notices();

//do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout wgm-second-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">

			<div class="col-1">

				<?php WGM_Template::second_checkout_form_billing(); ?>

			</div>

			<?php if( WGM_Template::should_be_shipping_to_shippingadress() ): ?>
				<div class="col-2">
					<?php WGM_Template::second_checkout_form_shipping(); ?>
				</div>
			<?php endif; ?>

		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' );

		$out = WGM_Template::checkout_readonly_field(
			'order_comments',
			array(
				'type'        => 'textarea',
				'class'       => array( 'notes' ),
				'name'        => 'order_comments',
				'label'       => __( 'Order Notes', 'woocommerce' ),
				'placeholder' => __( 'Notes about your order.', 'woocommerce' )
			)
		);

		if( $out ) {
			echo '<div class="wgm-second-checkout-user-note">';

			echo '<h3>' . __( 'Notizen/Kommentare', Woocommerce_German_Market::get_textdomain() ) . '</h3>';

			if ( is_array( $out ) ) {
				echo '<p>' . $out[0] . '</p>';
				$hidden_fields[] = $out[1];
			}

			echo '</div>';

			if ( is_array( $out ) ) {
				// print the hidden fields
				echo implode( '', $hidden_fields );
			}
		}

		if ( isset( $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] )  ) :
		?>
			<h3><?php _e( 'Zahlungsmethode', Woocommerce_German_Market::get_textdomain() ) ?></h3>
			<?php
			$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
			$gateway = $available_gateways[ $_SESSION[ 'first_checkout_post_array' ][ 'payment_method' ] ];

			if( method_exists( $gateway, 'get_icon' ) ) {
				$icon = $gateway->get_icon();
			} else {
				$icon = "";
			}
			?>

			<label for="payment_method"> <?php echo apply_filters( 'woocommerce_gateway_icon', $icon, $gateway->id ); ?></label>
				<h4 id="payment_method">
				<?php echo $gateway->title; ?>
				</h4>
				<span class="wgm-break"></span>
		<?php
		endif;


		$last_hint = get_option( 'woocommerce_de_last_checkout_hints' );
		if( $last_hint && trim( $last_hint ) != '' ) :

		?>

		<div class="checkout_hints">
			<h3>
				<?php echo __( 'Hinweis', Woocommerce_German_Market::get_textdomain() ); ?>
			</h3>

			<?php echo $last_hint; ?>
		</div>
		<span class="wgm-break"></span>
		<?php

		endif;
	endif;
	?>
		<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
<?php
// copied from woocommerce core ( woocommerce-ajax.php ), important to update values
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['shipping_method']) ) $_SESSION['_chosen_shipping_method'] = $_SESSION[ 'first_checkout_post_array' ][ 'shipping_method' ];
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['country'] ) ) $woocommerce->customer->set_country( $_SESSION[ 'first_checkout_post_array' ]['country'] );
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['state']) ) $woocommerce->customer->set_state( $_SESSION[ 'first_checkout_post_array' ]['state'] );
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['postcode'] ) ) $woocommerce->customer->set_postcode( $_SESSION[ 'first_checkout_post_array' ]['postcode'] );

if ( isset( $_SESSION[ 'first_checkout_post_array' ]['s_country'] ) ) $woocommerce->customer->set_shipping_country( $_SESSION[ 'first_checkout_post_array' ]['s_country'] );
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['s_state']) ) $woocommerce->customer->set_shipping_state( $_SESSION[ 'first_checkout_post_array' ]['s_state'] );
if ( isset( $_SESSION[ 'first_checkout_post_array' ]['s_postcode'] ) ) $woocommerce->customer->set_shipping_postcode( $_SESSION[ 'first_checkout_post_array' ]['s_postcode'] );

$woocommerce->cart->calculate_totals();
?>
<!-- Begin Order Review Template -->

<?php
/**
 * Copied Review order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

$available_methods = $woocommerce->shipping->get_shipping_methods();
?>
<div id="order_review">

	<table class="shop_table">
		<thead>
			<tr>
				<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
				<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="cart-subtotal">
				<th><?php _e( 'Cart Subtotal', 'woocommerce' ); ?></th>
				<td><?php echo $woocommerce->cart->get_cart_subtotal(); ?></td>
			</tr>

			<?php if ( $woocommerce->cart->needs_shipping() && $woocommerce->cart->show_shipping() ) :

                do_action('woocommerce_review_order_before_shipping'); ?>
				<tr>
					<th><?php _e( 'Shipping', 'woocommerce' ); ?> </th>
					<td>

					<?php
						$packages = WC()->shipping->get_packages();

						foreach ( $packages as $i => $package ):
							foreach($package['rates'] as $key => $method):
								if( WC()->session->chosen_shipping_methods[ $i ] == $key):
							?>
									<span for="shipping_method_<?php echo $i; ?>_<?php echo sanitize_title( $method->id ); ?>"><?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) ); ?></span>

							<?php
								endif;
							endforeach;
						endforeach;
					?>
					</td>
				</tr>
			<?php do_action('woocommerce_review_order_after_shipping'); ?>

			<?php endif; ?>

			<?php foreach ( $woocommerce->cart->get_fees() as $fee ) : ?>
                <tr class="fee">
                    <th><?php echo esc_html( $fee->name ); ?></th>
                    <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                </tr>
			<?php endforeach; ?>

			<?php
				// Show the tax row if showing prices exlcusive of tax only
				if ( $woocommerce->cart->tax_display_cart == 'excl' ) {

					$taxes = $woocommerce->cart->get_taxes();

					if ( sizeof( $taxes ) > 0 ) {

						$has_compound_tax = false;

						foreach ( $taxes as $key => $tax ) {
							if ( WC_Tax::is_compound( $key ) ) {
								$has_compound_tax = true;
								continue;
							}
							?>
							<tr class="tax-rate tax-rate-<?php echo $key; ?>">
								<th><?php echo WC_Tax::get_rate_label( $key ); ?></th>
								<td><?php echo wc_price( $tax ); ?></td>
							</tr>
							<?php
						}

						if ( $has_compound_tax ) {
							?>
							<tr class="order-subtotal">
								<th><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
								<td><?php echo $woocommerce->cart->get_cart_subtotal( true ); ?></td>
							</tr>
							<?php
						}

						foreach ( $taxes as $key => $tax ) {
							if ( ! WC_Tax::is_compound( $key ) )
								continue;
							?>
							<tr class="tax-rate tax-rate-<?php echo $key; ?>">
								<th><?php echo WC_Tax::get_rate_label( $key ); ?></th>
								<td><?php echo wc_price( $tax ); ?></td>
							</tr>
							<?php
						}

					} elseif ( $woocommerce->cart->get_cart_tax() ) {
						?>
						<tr class="tax">
							<th><?php _e( 'Tax', 'woocommerce' ); ?></th>
							<td><?php echo $woocommerce->cart->get_cart_tax(); ?></td>
						</tr>
						<?php
					}
				}
			?>

			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<tr class="total">
				<th><strong><?php _e( 'Order Total', 'woocommerce' ); ?></strong></th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

		</tfoot>
		<tbody>
			<?php
				do_action( 'woocommerce_review_order_before_cart_contents' );

				if (sizeof($woocommerce->cart->get_cart())>0) :
					foreach ($woocommerce->cart->get_cart() as $item_id => $values) :
						$_product = $values['data'];
						if ($_product->exists() && $values['quantity']>0) :
							echo '
								<tr class="' . esc_attr( apply_filters('woocommerce_cart_table_item_class', 'checkout_table_item', $values, $item_id ) ) . '">
									<td class="product-name">' . $_product->get_title() . ' <strong class="product-quantity">&times; ' . $values['quantity'] . '</strong>' . $woocommerce->cart->get_item_data( $values );
									if ( get_option( 'woocommerce_de_show_show_short_desc' ) == 'on' ) {
										echo '<span class="wgm-break"></span> <span class="product-desc">'  . strip_tags( apply_filters( 'woocommerce_short_description', $_product->post->post_excerpt ) ) . '</span>';
									}

                                $prerequisites = get_post_meta( $_product->id, 'product_function_desc_textarea', true );

                                $is_digital = false;
                                if( method_exists( $_product, 'is_virtual' ) && method_exists( $_product, 'is_downloadable' ) ) {
                                    $is_digital = ( $_product->is_virtual() || $_product->is_downloadable() );
                                }

                                if( $is_digital && $prerequisites != false ){
                                    echo '<span class="wgm-break"></span><span class="wgm-product-prerequisites">' . $prerequisites . '</span>';
                                }

									echo '</td>
									<td class="product-total">' . apply_filters( 'woocommerce_cart_item_subtotal', $woocommerce->cart->get_product_subtotal( $_product, $values['quantity'] ), $values, $item_id ) . '</td>
								</tr>';
						endif;
					endforeach;
				endif;

				do_action( 'woocommerce_review_order_after_cart_contents' );
			?>
		</tbody>
	</table>

	<div class="form-row place-order wgm-place-order">

		<!-- End Order Review Template -->

		<input type="submit" class="button wgm-go-back-button" name="woocommerce_checkout_update_totals" id="place_order_back" value="<?php _e( 'Zurück', Woocommerce_German_Market::get_textdomain() ) ?>" />
		<input type="submit" class="button alt checkout-button wgm-place-order" name="woocommerce_checkout_place_order" id="place_order" value="<?php echo apply_filters( 'woocommerce_de_buy_button_text', __( 'Kaufen', Woocommerce_German_Market::get_textdomain() ) ); ?>" />
		<?php
			//$woocommerce->nonce_field( 'process_checkout' );
			// correct the referer
			$_SESSION[ 'first_checkout_post_array' ][ '_wp_http_referer' ] = $_SERVER['REQUEST_URI'];
			$_SESSION[ 'first_checkout_post_array' ][ 'widerruf' ] = '1';
			WGM_Template::print_hidden_fields( $_SESSION[ 'first_checkout_post_array' ], array_keys($_SESSION[ 'first_checkout_post_array' ] ) );
		?>

	</div>

	<div class="clear"></div>

</div>
</form>