<?php

/**
 * Class with Template Snippet functions, Template Helper Functions
 * Output filtering Funktions of WooCommerce hooks
 *
 * @author jj, ap
 */
class WGM_Template {

    /**
     * Overloading Woocommerce with German Market templates
     * @param string $template
     * @param string $template_name
     * @param string $template_path
     * @access public
     * @static
     * @author ap
     * @return string the template
     */
    public static function add_woocommerce_de_templates( $template, $template_name, $template_path ){
		global $woocommerce;

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '../templates' . DIRECTORY_SEPARATOR . '/woocommerce' . DIRECTORY_SEPARATOR;

		// Only load our templates if they are nonexistent in the theme
		if( file_exists( $path . $template_name ) && ! locate_template( array( WC()->template_path() . $template_name ) ) ) {
			$template = $path . $template_name;
		}

		return $template;
	}

	/**
     * Overloading WGM Teplates with templates form the theme if existent
	 * @param $template_name tempalte name
	 * @param string $template_path path the templates in theme folder
	 * @param string $default_path path to templates in plugin folder
	 * @return mixed found template
	 * @author ap
	 * @since 2.3
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( !$template_path ) $template_path = 'woocommerce-german-market' . DIRECTORY_SEPARATOR;
		if ( !$default_path ) $default_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '../templates' . DIRECTORY_SEPARATOR . 'woocommerce-german-market' . DIRECTORY_SEPARATOR;

		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		if ( ! $template )
			$template = $default_path . $template_name;


		return apply_filters('wgm_locate_template', $template, $template_name, $template_path);
	}

	/**
	 * @param $template_name template name
	 * @param array $args variables for template scope
	 * @author ap
	 * @since 2.3
	 */
	public static function load_template( $template_name, array $args = array() ) {
		$tmpl = WGM_Template::locate_template( $template_name );

		extract( $args );
		include $tmpl;
	}

    public static function add_mwst_rate_to_product_item_init(){
        add_filter( 'woocommerce_cart_item_subtotal', array( 'WGM_Template', 'add_mwst_rate_to_product_item' ), 10 ,3 );
    }

    /**
     * adds german vat tax rate to every product
     * @since 1.1.5beta
     * @access public
     * @hook woocommerce_checkout_item_subtotal
     * @author ap
     * @param float $amount
     * @param array $item
     * @param int $item_id
     * @return string
     */
    public static function add_mwst_rate_to_product_item( $amount, $item, $item_id ) {
		global $woocommerce;


		$_product = get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );

		if( ! $_product->is_taxable() )
			return $amount;

		$_tax = new WC_Tax();

        if( ! is_object(  WC()->customer ) ){
            $order = wc_get_order( get_the_ID() );
            $addr = $order->get_billing_address();
            $country = $addr[ 'country' ];
            $state = $addr[ 'state' ];

        } else {
            list( $country, $state, $postcode, $city ) = WC()->customer->get_taxable_address();
        }

		$t = $_tax->find_rates( array(
			'country' 	=>  $country,
			'state' 	=> $state,
			'tax_class' => $_product->tax_class
		) );

		$tax = array_shift( $t );

		if( get_option('woocommerce_tax_display_cart') == 'excl' || get_option('woocommerce_tax_display_cart', '' ) == '' ){
            $incl_excl = __( 'zusätzliche MwSt.', Woocommerce_German_Market::get_textdomain() );
        }
		elseif( get_option('woocommerce_tax_display_cart') == 'incl' ){
            $incl_excl = __( 'enthaltene MwSt.', Woocommerce_German_Market::get_textdomain() );
        }

		$decimal_length = WGM_Helper::get_decimal_length( $tax[ 'rate' ] );
		$formatted_rate = number_format_i18n( (float)$tax[ 'rate' ], $decimal_length );

		$tax_string = apply_filters(
			'wgm_format_vat_output',
            sprintf( '%1$s %2$s (%3$s%%)', $incl_excl, wc_price( $item[ 'line_subtotal_tax' ] ), $formatted_rate ),
			$item[ 'line_tax' ],
			$tax[ 'rate' ],
			$incl_excl
		);

		$template = '%s <span class="product-tax"> %s </span>';

		$item = sprintf( $template, $amount, $tax_string );

		$item = apply_filters( 'wgm_additional_tax_notice', $item, $amount, $tax, $incl_excl );

		return $item;
	}


    /**
     * adds german mwst tax rate to every product in line in order-details.php
     * @since	1.1.5beta
     * @access	public
     * @author ap
     * @hook 	woocommerce_checkout_item_subtotal
     * @param float $subtotal
     * @param array $item
     * @param Post $order_obj
     * @return string
     */
    public static function add_mwst_rate_to_product_order_item( $subtotal, $item, $order_obj ) {
		global $woocommerce;

		// Little hack for WGM_Email (see WGM_Email::email_de_footer)

		if( ! defined( 'WGM_MAIL' ) )
			define( 'WGM_MAIL', true );

		$_product = $order_obj->get_product_from_item( $item );

		if( empty( $_product ) || ! $_product->is_taxable() )
			return $subtotal;

		$_tax = new WC_Tax();

	    $country = $order_obj->billing_country;
	    $state = $order_obj->billing_state;

        $t = $_tax->find_rates( array(
				'country' 	=>  $country,
				'state' 	=> $state,
				'tax_class' => $_product->tax_class
			) );

        $tax_sum = WC_Tax::calc_tax( $item[ 'line_subtotal' ], $t, false );

		$tax = array_shift( $t );

		$incl_excl = '';
		if( get_option('woocommerce_tax_display_cart') == 'excl' || get_option('woocommerce_tax_display_cart', '' ) == '' ){
            $incl_excl = __( 'zusätzliche MwSt.', Woocommerce_German_Market::get_textdomain() );
        }
		elseif( get_option('woocommerce_tax_display_cart') == 'incl' ) {
            $subtotal = $item[ 'line_subtotal' ] + array_sum(  $tax_sum );
			$subtotal = wc_price( $subtotal );
            $incl_excl = __( 'enthaltene MwSt.', Woocommerce_German_Market::get_textdomain() );
        }

		$decimal_length = WGM_Helper::get_decimal_length( $tax[ 'rate' ] );
		$formatted_rate = number_format_i18n( (float)$tax[ 'rate' ], $decimal_length );

		$tax_string = apply_filters(
				'wgm_format_vat_output',
				sprintf( '%1$s (%2$s%%)', wc_price( array_sum( $tax_sum ) ), $formatted_rate ),
                array_sum( $tax_sum ),
				$tax[ 'rate' ],
				$incl_excl
			);

		$template = '%s <span class="product-tax"> %s %s </span>';

		$item = sprintf( $template, $subtotal, $tax_string ,$incl_excl );
		$item = apply_filters( 'wgm_additional_tax_notice', $item, $subtotal, $tax, $incl_excl );

		return $item;
	}

	/**
	 * adds mwst to variation price
     * @uthor jj, ap
     * @access public
     * @static
     * @return void
	 */
	public static function add_mwst_rate_to_variation_product_price(){

		global $product;

		$show_shipping_fee = ( 'on' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_shipping_fee_overview_single' ) ) );

        do_action( 'wgm_before_tax_display_variation' );
		WGM_Template::text_including_tax( $product );
        do_action( 'wgm_after_tax_display_variation' );

        if( $show_shipping_fee && WGM_Helper::is_digital() ) :
            do_action( 'wgm_before_variation_shipping_fee' );
            ?>
			<div class="woocommerce_de_versandkosten">

				<?php if ( get_option( 'woocommerce_de_show_free_shipping' ) == 'on' ):
					_e( 'versandkostenfrei', Woocommerce_German_Market::get_textdomain() );
				else: ?>
					<?php _e( 'zzgl.', Woocommerce_German_Market::get_textdomain() ); ?>
					<a class="versandkosten" href="<?php echo get_permalink( get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) ) ); ?>">
						<?php _e( 'Versand', Woocommerce_German_Market::get_textdomain() ); ?>
					</a>
				<?php
                    do_action( 'wgm_after_variation_shipping_fee' );

                endif; ?>


			</div>
		<?php endif;
	}

    /**
     * Updates and shows tax for variations. Used for Ajax request
     * @author ap, dw
     * @access public
     * @static
     * @return void
     */
    public static function update_mwst_rate_to_variation_product_price() {
		ob_start();

        $show_shipping_fee = ( 'on' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_shipping_fee_overview_single' ) ) );
		$product = get_product( $_POST[ 'variation_id' ] );

		self::text_including_tax( $product );

        $is_digital = false;
        if( method_exists( $product, 'is_virtual' ) && method_exists( $product, 'is_downloadable' ) ) {
            $is_digital = ( $product->is_virtual() || $product->is_downloadable() );
        }

        if( $show_shipping_fee && ! $is_digital ) :
            do_action( 'wgm_before_variation_shipping_fee' );
            ?>
            <div class="woocommerce_de_versandkosten">

                <?php if ( get_option( 'woocommerce_de_show_free_shipping' ) == 'on' ):
                    _e( 'versandkostenfrei', Woocommerce_German_Market::get_textdomain() );
                else: ?>
                    <?php _e( 'zzgl.', Woocommerce_German_Market::get_textdomain() ); ?>
                    <a class="versandkosten" href="<?php echo get_permalink( get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) ) ); ?>">
                        <?php _e( 'Versand', Woocommerce_German_Market::get_textdomain() ); ?>
                    </a>
                    <?php
                    do_action( 'wgm_after_variation_shipping_fee' );

                endif; ?>


            </div>
        <?php endif;


		echo ob_get_clean();

		exit;
	}


	/**
	* add filter for mwst rate to the label on cart
	*
	* @access public
	* @static
	* @return void
	* @uses add_filter
	*/
	public static function add_mwst_rate_to_cart_totals(){

		// add filter to enable rate at the mwst label
		add_filter( 'woocommerce_rate_label', array( 'WGM_Template', 'add_rate_to_label' ), 10, 2 );
	}


	/**
	* remove filter for mwst rate to the label on cart
	*
	* @access public
	* @static
	* @return void
	* @uses add_filter
	*/
	public static function remove_mwst_rate_to_cart_totals(){

		// remove filter from: this->add_mwst_rate_to_cart_totals
		remove_filter( 'woocommerce_rate_label', array( 'WGM_Template', 'add_rate_to_label' ), 10, 2 );
	}


    /**
     * Adds tax rate (percentage) to tax label (tax rate name) in cart and checkout.
     *
     * @wp-hook woocommerce_rate_label
     *
     * @param   string $rate_name
     * @param   string $key
     *
     * @return  string $new_rate_name
     */
    public static function add_rate_to_label( $rate_name, $key ) {
		global $wpdb;

	    $sql    = "SELECT tax_rate FROM " .  $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_id = %s";
	    $query  = $wpdb->prepare( $sql, $key );
		$rate   = $wpdb->get_var( $query );

	    $new_rate_name  = $rate_name;

		if ( ! empty( $rate ) ) {
			$decimal_length = WGM_Helper::get_decimal_length( $rate );
			$rate           = number_format_i18n( (float)$rate, $decimal_length );
			$new_rate_name .= ' ' . $rate . '%';
		}

		return $new_rate_name;
	}


	/**
	* print checkout button below the cart contents
	*
	* @author jj, ap
	* @uses globals $woocommerce
	* @access public
	* @hook woocommerce_after_cart_totals
	* @static
	* @return void
	*/
	public static function woocommerce_after_cart_totals() {

		global $woocommerce;

		$checkout_url = $woocommerce->cart->get_checkout_url();
		$checkout_text_string = __( 'Proceed to Checkout', 'woocommerce' );

		$html  = '<div class="wc-proceed-to-checkout wgm-proceed-to-checkout">';
		$html .= '<a href="' . esc_url( $checkout_url ) . '" class="checkout-button button alt wc-forward">';
		$html .= $checkout_text_string;
		$html .= '</a></div>';

		echo apply_filters( 'woocommerce_after_cart_totals_html', $html, $checkout_url, $checkout_text_string );
	}

    /**
     * Retrives price per unit data
     * @param Product $_product
     * @access public
     * @static
     * @author ap
     * @return array
     */
    public static function get_price_per_unit_data( $_product ){

		if( $_product->is_on_sale() ) {
			$price_per_unit = str_replace( ',', '.', get_post_meta( get_the_ID(), '_sale_price_per_unit', TRUE ) );
			$unit = get_post_meta( get_the_ID(), '_unit_sale_price_per_unit', TRUE );
			$mult = get_post_meta( get_the_ID(), '_unit_sale_price_per_unit_mult', TRUE );
		} else {
			$price_per_unit = str_replace( ',', '.', get_post_meta( get_the_ID(), '_regular_price_per_unit', TRUE ) );
			$unit = get_post_meta( get_the_ID(), '_unit_regular_price_per_unit', TRUE );
			$mult = get_post_meta( get_the_ID(), '_unit_regular_price_per_unit_mult', TRUE );
		}

		if ( $price_per_unit && $unit && $mult ) {
			return compact( 'price_per_unit', 'unit', 'mult' );
		} else {
			return Array();
		}

	}

	/**
	* print tax hint after prices in loop
	*
	* @uses globals $product, remove_action
	* @access public
	* @hook woocommerce_after_shop_loop_item_title
	* @static
	* @author jj, ap
	* @return void
	*/
        public static function woocommerce_de_price_with_tax_hint_loop() {

		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

		global $product;

		$price_per_unit_data = WGM_Template::get_price_per_unit_data( $product );

		$show_shipping_fee = ( 'on' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_shipping_fee_overview' ) ) );
		$suppress_data_shipping = maybe_unserialize( get_post_meta( get_the_ID(), '_suppress_shipping_notice', TRUE ) );

		if( ! empty( $suppress_data_shipping ) && 'on' === $suppress_data_shipping )
			$show_shipping_fee = FALSE;

        $classes = ' ' . apply_filters( 'wgm_loop_price_class', '' );
		if ($price_html = $product->get_price_html() ) : ?>
			<div class="price <?php echo $classes;?>" >
				<?php
                do_action( 'wgm_before_loop_price' );
                echo $price_html;
                do_action( 'wgm_after_loop_price' );

                do_action( 'wgm_before_tax_display_loop' );
				WGM_Template::text_including_tax( $product );
                do_action( 'wgm_after_tax_display_loop' );

				if( ! empty( $price_per_unit_data ) && ( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_price_per_unit' ) ) == 'on' ) ) {

					do_action( 'wgm_before_price_per_unit_loop' );

                    echo apply_filters( 'wmg_price_per_unit_loop', sprintf( '<span class="price-per-unit price-per-unit-loop">%s / %s %s</span>',
                                    wc_price( str_replace( ',', '.', $price_per_unit_data[ 'price_per_unit' ] ) ),
                                    $price_per_unit_data[ 'mult'],
                                    $price_per_unit_data[ 'unit' ] ),

                                    wc_price( str_replace( ',', '.', $price_per_unit_data[ 'price_per_unit' ] ) ),
                                    $price_per_unit_data[ 'mult'],
                                    $price_per_unit_data[ 'unit' ] );

						do_action( 'wgm_after_price_per_unit_loop' );
					}

                    $is_digital = false;
                    if( method_exists( $product, 'is_virtual' ) && method_exists( $product, 'is_downloadable' ) ) {
                        $is_digital = ( $product->is_virtual() || $product->is_downloadable() );
                    }

					if( $show_shipping_fee && ! $is_digital ) :
                        do_action( 'wgm_before_shipping_fee_loop');
                        ?>

						<div class="woocommerce_de_versandkosten">

							<?php if ( get_option( 'woocommerce_de_show_free_shipping' ) == 'on' ):
								_e( 'versandkostenfrei', Woocommerce_German_Market::get_textdomain() );
							else: ?>
								<a class="versandkosten" href="<?php echo get_permalink( get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) ) ); ?>">
									<?php _e( 'zzgl.', Woocommerce_German_Market::get_textdomain() ); ?>
									<?php _e( 'Versand', Woocommerce_German_Market::get_textdomain() ); ?>
								</a>
							<?php endif; ?>

						</div>
                        <?php
                        do_action( 'wgm_after_shipping_fee_loop' );
                    endif; ?>
			</div>

		<?php endif;

	}


	/**
	*  print tax hint after prices in single
	*
	* @author jj, ap
	* @hook woocommerce_single_product_summary
	* @uses remove_action, get_post_meta, get_the_ID, get_woocommerce_currency_symbol
	* @access public
	* @static
	* @return void
	*/
	public static function woocommerce_de_price_with_tax_hint_single() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );

		global $product;

		$price_per_unit_data = WGM_Template::get_price_per_unit_data( $product );

		$show_shipping_fee = ('on' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_shipping_fee_overview_single' ) ) );

		$suppress_data_shipping = maybe_unserialize( get_post_meta( get_the_ID(), '_suppress_shipping_notice', TRUE ) );

		if( ! empty( $suppress_data_shipping ) && 'on' === $suppress_data_shipping )
			$show_shipping_fee = FALSE;

        $classes = ' ' . apply_filters( 'wgm_single_price_class', '' );
		do_action( 'wgm_before_single_price' );
        ?>
		<div class="price <?php echo $classes; ?>">
			<?php
            do_action( 'wgm_before_single_price_html' );
            echo $product->get_price_html();
            do_action( 'wgm_after_single_price_html' );
            ?>

			<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
			<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
			<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

			<?php

			do_action( 'wgm_before_tax_display_single' );
			WGM_Template::text_including_tax( $product );
            do_action( 'wgm_after_tax_display_single' );

			if( ! empty( $price_per_unit_data ) ) {

				do_action( 'wgm_before_price_per_unit_single' );

				echo apply_filters( 'wmg_price_per_unit_single', sprintf( '<span class="price-per-unit price-per-unit-single">%s / %s %s</span>',
																		wc_price( $price_per_unit_data[ 'price_per_unit' ] ),
																		$price_per_unit_data[ 'mult'],
																		$price_per_unit_data[ 'unit' ] ),
																		wc_price( $price_per_unit_data[ 'price_per_unit' ] ),
																		$price_per_unit_data[ 'mult'],
																		$price_per_unit_data[ 'unit' ] );

				do_action( 'wgm_after_price_per_unit_single' );
			}

			if( $show_shipping_fee && ! $product->is_virtual() && !$product->is_downloadable()  ) :
                do_action( 'wgm_before_shipping_fee_single');
						?>
						<div class="woocommerce_de_versandkosten">

							<?php if ( get_option( 'woocommerce_de_show_free_shipping' ) == 'on' ):
								_e( 'versandkostenfrei', Woocommerce_German_Market::get_textdomain() );
							else: ?>
								<?php _e( 'zzgl.', Woocommerce_German_Market::get_textdomain() ); ?>
								<a class="versandkosten" href="<?php echo get_permalink( get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) ) ); ?>">
									<?php _e( 'Versand', Woocommerce_German_Market::get_textdomain() ); ?>
								</a>
							<?php endif; ?>

						</div>

				<?php
                do_action( 'wgm_after_shipping_fee_single');
            endif;
			?>
		</div>
		<?php
	}

	/**
	 * Output the Shippingform
	 * @access public
	 * @static
	 * @author jj, ap
	 * @return string the Shipping Form
	 */
	public static function second_checkout_form_shipping() {

		$woocommerce_checkout = WC()->checkout();

        if( $_SESSION[ 'first_checkout_post_array '][ 'ship_to_different_address' ] != '1' ) return;

		if ( WGM_Template::should_be_shipping_to_shippingadress() ) :

			echo '<h3>' . __( 'Shipping Address', 'woocommerce' ) . '</h3>';

			echo'<table class="review_order_shipping">';
			$hidden_fields = array();

			foreach ( $woocommerce_checkout->checkout_fields[ 'shipping' ] as $key => $field ) :
				$out = WGM_Template::checkout_readonly_field( $key, $field );
				if ( is_array( $out ) ) {
					echo $out[0];
					$hidden_fields[] = $out[1];
				}
			endforeach;

			echo'</table>';
		endif;
	}


    /**
     * @access public
     * @static
     * @author ap
     * @since 2.3.5
     */
    public static function shipping_address_check(){
        $_SESSION[ 'first_checkout_post_array '][ 'ship_to_different_address' ] =  WC()->checkout()->get_value( 'ship_to_different_address' );
    }

	/**
	 * @access public
	 * @static
	 * @author ap
	 * @return bool
	 */
	public static function should_be_shipping_to_shippingadress(){
		global $woocommerce;

		if ( $woocommerce->cart->needs_shipping() && ! WGM_Helper::ship_to_billing() || get_option('woocommerce_require_shipping_address') == 'yes' )
			return true;
		return false;
	}

	/**
	* Remove shipping from standard checkout form
	* @access public
	* @static
	* @author jj
	* @return void
	* @hook woocommerce_checkout_shipping
	*/
	public static function second_checkout_form_shipping_remove() {

		global $woocommerce;
		$woocommerce_checkout = $woocommerce->checkout();

		if ( isset( $_POST )  && isset( $_POST[ 'login' ] ) ) {
			// remove the normal checkout form
			remove_action( 'woocommerce_checkout_shipping', array ( $woocommerce_checkout, 'checkout_form_shipping' ) );
		}
	}


	/**
	* Output the billing information form
	* @access public
	* @static
	* @author jj, ap
	* @return string The billing information
	*/
	public static function second_checkout_form_billing() {

		global $woocommerce;
		// Get checkout object
		$checkout = $woocommerce->checkout();

		if ( WGM_Helper::ship_to_billing() )
			echo '<h3>'. __( 'Billing &amp; Shipping', 'woocommerce' ). '</h3>';
		else
			echo '<h3>'. __( 'Billing Address', 'woocommerce' ).'</h3>';

		echo '<table class="review_order_billing">';
		$hidden_fields = array();

		// Billing Details
		foreach ( $checkout->checkout_fields[ 'billing' ]  as $key => $field ) {
			$out = WGM_Template::checkout_readonly_field( $key, $field );
			if ( is_array( $out ) ) {
				echo $out[ 0 ];
				$hidden_fields[] = $out[ 1 ];
			}
		}

		echo '</table>';

		// print the hidden fields
		echo implode( '', $hidden_fields );
	}


	/**
	* remove the billing in second checkout
	* @access public
	* @static
	* @author jj, ap
	* @return void
	* @hook woocommerce_checkout_billing
	*/
	public static function second_checkout_form_billing_remove() {

		global $woocommerce;

		// Get checkout object
		$checkout = $woocommerce->checkout();

		if ( isset( $_POST )  && isset( $_POST[ 'login' ] ) ) {
			// remove the normal checkout form
			remove_action( 'woocommerce_checkout_billing', array( $checkout , 'checkout_form_billing' ) );
		}

	}

	/**
	* print hidden fields for given post array
	* determined by given field array
	*
	* @param array $post_array
	* @param array $fields_array
	* @static
	* @author jj
	* @return void
	*/
	public static function print_hidden_fields( $post_array, $fields_array ) {
		foreach( $fields_array as $field )
            if( ! is_array( $post_array[ $field ] ) )
			    echo '<input type="hidden" name="'. $field .'" value="'. $post_array[ $field ] .'" />';
            else
                echo '<input type="hidden" name="'. $field .'" value="'. current( $post_array[ $field ] ) .'" />';

	}


	/**
	* Change the button text, if on checkout page
	*
	* @param string $button_text
	* @static
	* @author jj, ap
	* @return void
	* @hook woocommerce_order_button_text
	*/
	public static function change_order_button_text( $button_text ) {

		// @todo do not touch button, when on pay for order page
		// @todo when refreshing payments,  session is expired, because cart is empty, see woocommerce-ajax.php
		if ( isset( $_SESSION[ 'woocommerce_de_in_first_checkout' ] ) )
			return __( 'weiter', Woocommerce_German_Market::get_textdomain() );

		return $button_text;
	}


	/**
	* Adds the Shipping Time to the Product title
	*
	* @param string $item_name
	* @param string $product
	* @static
	* @author jj, ap
	* @return void
	* @hook woocommerce_order_product_title
	*/
	public static function add_shipping_time_to_product_title( $item_name, $item ) {

        $product = get_product( ( $item[ 'variation_id' ] != 0 ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] );

		$shipping_time = get_post_meta( $product->id, '_lieferzeit', TRUE );

		if ( is_numeric( $shipping_time ) && (int) $shipping_time !== -1 )
			$lieferzeit = ( int ) $shipping_time;
		else
			$lieferzeit = ( int ) @get_option( WGM_Helper::get_wgm_option( 'global_lieferzeit' ) );

		$deliverytime_term = get_term( $lieferzeit, 'product_delivery_times' );

		$shipping_time_output = '';
		$lieferzeit_string    = '';

		if( ! empty( $deliverytime_term ) ) {
			$lieferzeit_string = $deliverytime_term->name;
        }

        if( $lieferzeit_string == __( 'Nutze den Standard', Woocommerce_German_Market::get_textdomain() ) || $lieferzeit_string == '' ) {
		    $deliverytime_term = get_term( get_option( WGM_Helper::get_wgm_option( 'global_lieferzeit' ) ), 'product_delivery_times' );

            if( ! is_null( $deliverytime_term ) ) {
                $lieferzeit_string = $deliverytime_term->name;
            } else {
                $lieferzeit_string = __( 'Keine Standardlieferzeit gesetzt!', Woocommerce_German_Market::get_textdomain() );
            }
        }

			$shipping_time_output = ', ' . __( 'Lieferzeit:', Woocommerce_German_Market::get_textdomain() ) . ' ' . $lieferzeit_string;

		$shipping_time_output = apply_filters( 'wgm_shipping_time_product_string', $shipping_time_output, $lieferzeit_string, $product );

		return $item_name . ' ( ' . __( 'jeweils', Woocommerce_German_Market::get_textdomain() ) .  ' ' . wc_price( $product->get_price() ) . $shipping_time_output . ' ) ';
	}


	/**
	* add fax to billing fields
	*
	* @access public
	* @static
	* @author jj
	* @return void
	* @param array	 $billing_fields
	* @return array
	* @hook woocommerce_billing_fields
	*/
	public static function billing_fields( $billing_fields ) {

		$billing_fields[ 'billing_fax' ] = array(
			'type'=> 'phone',
			'name'=>'fax',
			'label' => __( 'Fax', 'woocommerce' ),
			'required' => false,
			'class' => array( 'form-row-last' )
		);

		return WGM_Template::set_field_type( $billing_fields, 'state', 'required' ) ;
	}



	/**
	* add fax number to shipping fields
	* @access public
	* @static
	* @author jj
	* @return void
	* @param array $shipping_fields
	* @return array
	* @hook woocommerce_shipping_fields
	*/
	public static function shipping_fields( $shipping_fields ) {

		$shipping_fields[ 'shipping_fax' ] = array(
			'type'=> 'phone',
			'name'=>'fax',
			'label' => __( 'Fax', 'woocommerce' ),
			'required' => false,
			'class' => array( 'form-row-last' )
		);

		return WGM_Template::set_field_type( $shipping_fields, 'state', 'required' ) ;
	}


	/**
	* show the delivery time in overview
	* @access public
	* @static
	* @author ap
	* @return void
	* @return mixed
	* @hook woocommerce_after_shop_loop_item
	*/
	public static function woocommerce_de_after_shop_loop_item() {
		// if this function is defined in template, use it (like in the original woocommerce)
		if ( ! function_exists( 'woocommerce_de_after_shop_loop_item' ) )
			WGM_Template::add_template_loop_shop(  'on' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_delivery_time_overview' ) ) );
		else
			woocommerce_de_after_shop_loop_item();
	}


	/**
	* Interupt checkout process after validation, to use the checkout site again, to fullfill
	* the german second checkout verify obligation
	*
	* @access public
	* @static
	* @author ap
	* @return void
	* @param array $posted $_POST array at hook position
	* @hook woocommerce_after_checkout_validation
	*/
	public static function do_de_checkout_after_validation ( $posted ) {

        $has_virutal = false;
        $cart = WC()->cart->get_cart();
        $dcount = 0;

        foreach( $cart as $item ){
            if( empty( $item[ 'variation_id' ] ) )
                $product = get_product( $item['product_id'] );
            else
                $product = get_product( $item[ 'variation_id' ] );


            $is_digital = false;
            if( method_exists( $product, 'is_virtual' ) && method_exists( $product, 'is_downloadable' ) ) {
                $is_digital = ( $product->is_virtual() || $product->is_downloadable() );
            }

            if( $is_digital ){
                $has_virutal = true;
                $dcount++;
            }
        }


        $only_digital = false;
        if( $dcount == count( $cart ) )
            $only_digital = true;

        $_SESSION[ 'WGM_CHECKOUT' ][ 'only_digital' ] = $only_digital;
        $_SESSION[ 'WGM_CHECKOUT' ][ 'has_digital' ] = $has_virutal;

		$error_count = wc_notice_count();
		// check widerruf
        if( ! $only_digital ){
		    if ( !isset( $_POST[ 'woocommerce_checkout_update_totals' ] ) && empty( $_POST[ 'widerruf' ] ) && get_option( 'woocommerce_widerruf_page_id' ) > 0 && get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_Widerrufsbelehrung' ) ) == 'on' ) {
                wc_add_notice( __( 'Die Zustimmung zur Widerrufsbelehrung ist erforderlich', Woocommerce_German_Market::get_textdomain() ), 'error' );
			    $error_count ++;
		    }
        }

        if( $has_virutal ){

            if ( !isset( $_POST[ 'woocommerce_checkout_update_totals' ] ) && empty( $_POST[ 'widerruf-digital' ] ) ) {
                wc_add_notice( __( 'Die Zustimmung zur Widerrufsbelehrung für digitale Inhalte ist erfoderlich', Woocommerce_German_Market::get_textdomain() ), 'error' );
                $error_count ++;
            }

            if ( !isset( $_POST[ 'woocommerce_checkout_update_totals' ] ) && empty( $_POST[ 'widerruf-digital-acknowledgement' ] ) ) {
                wc_add_notice( __( 'Die Zustimmung zur Aufgabe des Widerrufsrechts ist erfoderlich.', Woocommerce_German_Market::get_textdomain() ), 'error' );
                $error_count ++;
            }
        }

		if ( $error_count != 0 )
			return;

		if ( isset( $_SESSION[ 'woocommerce_de_in_first_checkout' ] ) ) {

			// reset woocommerce_de_in_first_checkout
			unset ( $_SESSION[ 'woocommerce_de_in_first_checkout' ] );

			// save the $_POST variables into session, to save them during redirect
			$_SESSION[ 'first_checkout_post_array' ] = $_POST;

			if ( is_ajax() ) {
					@ob_clean();

					echo json_encode( array(
								'result'   => 'success',
								'redirect' => WGM_Helper::get_check_url()
								) );
					exit;
			} else {
				wp_safe_redirect( WGM_Helper::get_check_url() );
				exit;
			}

		} else {
			if( isset( $_SESSION[ 'first_checkout_post_array' ] ) )
				unset( $_SESSION[ 'first_checkout_post_array' ] );
		}
	}


	/**
	* add the german disclaimer to checkout
	* @access public
	* @static
	* @author ap
	* @return string review order
	* @hook woocommerce_review_order_after_submit
	*/
	public static function add_review_order() {

        $has_virutal = false;
        $cart = WC()->cart->get_cart();
        $dcount = 0;

        foreach( $cart as $item ){
            if( empty( $item[ 'variation_id' ] ) )
                $product = get_product( $item['product_id'] );
            else
                $product = get_product( $item[ 'variation_id' ] );

            if( $product->is_virtual() || $product->is_downloadable() ){
                $has_virutal = true;
                $dcount++;
            }
        }

        $only_digital = false;
        if( $dcount == count( $cart ) )
            $only_digital = true;


		$review_order = '';

        if(! $only_digital ){
            if( get_option( WGM_Helper::get_wgm_option(  'woocommerce_de_show_Widerrufsbelehrung' ) ) == 'on' ) {
                $checked = isset( $_POST[ 'widerruf' ] ) ? checked( $_POST[ 'widerruf' ], '1', FALSE ) : '';
                $review_order = '
				<p class="form-row terms">
					<label for="widerruf" class="checkbox">'
                    . apply_filters( 'wgm_checkout_revocation_checkbox_text', __( 'Gelesen und zur Kenntnis genommen: ', Woocommerce_German_Market::get_textdomain() ) )
                    . ' <a href="' . get_permalink( get_option( WGM_Helper::get_wgm_option( 'widerruf' ) ) ) . '" target="_blank">' .
                    __( 'Widerrufsbelehrung', Woocommerce_German_Market::get_textdomain() ) .
                    '</a>
                </label>
                <input type="checkbox" class="input-checkbox" ' . $checked . ' name="widerruf" id="widerruf" />
				</p>';
            }
        }

        if( $has_virutal ) {
            $checked = isset( $_POST[ 'widerruf_digital' ] ) ? checked( $_POST[ 'widerruf_digital' ], '1', FALSE ) : '';
            $review_order .= '
				<p class="form-row terms">
					<label for="widerruf-digital" class="checkbox">'
                . apply_filters( 'wgm_checkout_revocation_digital_checkbox_text', __( 'Gelesen und zur Kenntnis genommen: ', Woocommerce_German_Market::get_textdomain() ) )
                . ' <a href="' . get_permalink( get_option( WGM_Helper::get_wgm_option( 'widerruf_fuer_digitale_medien' ) ) ) . '" target="_blank">' .
                apply_filters( 'wgm_checkout_revocation_digital_keyword_checkbox_text', __( 'Widerrufsbelehrung für <span class="wgm-virtual-notice">digitale</span> Inhalte', Woocommerce_German_Market::get_textdomain() ) ) .
                '</a>
            </label>
            <input type="checkbox" class="input-checkbox" ' . $checked . ' name="widerruf-digital" id="widerruf-digital" />
				</p>';

        $review_order .= '<p class="form-row terms">
					<label for="widerruf-digital-acknowledgement" class="checkbox">'.
                    apply_filters( 'wgm_checkout_digital_revocation_text', __( 'Für <span class="wgm-virtual-notice">digitale</span> Inhalte: Ich stimme ausdrücklich zu, dass Sie vor Ablauf der Widerrufsfrist mit der
                    Ausführung des Vertrages beginnen. Mir ist bekannt, dass ich durch diese Zustimmung mit Beginn der
                    Ausführung des Vertrages mein Widerrufsrecht verliere.', Woocommerce_German_Market::get_textdomain() ) ) . '</label>
            <input type="checkbox" class="input-checkbox" ' . $checked . ' name="widerruf-digital-acknowledgement" id="widerruf-digital-acknowledgement" />
				</p>';

            add_action( 'woocommerce_review_order_after_payment', array( 'WGM_Template', 'digital_items_notice' ) );
        }

		echo apply_filters( 'woocommerce_de_review_order_after_submit' , $review_order );
	}

    public static function digital_items_notice(){
        echo '<span class="wgm-digital-checkout-notice">' . __( 'Hinweis: Digitale Inhalte, sind solche, die nicht auf einem körperlichen Datenträger geliefert werden (Softwaredownloads, E-Books, etc.)', Woocommerce_German_Market::get_textdomain() ) . '</span>';
    }

	/**
	* add the hidden field and set the woocommerce_de_in_first_checkout
	* @access public
	* @static
	* @author ap
	* @hook woocommerce_review_order_before_submit
	*/
	public static function add_wgm_checkout_session() {
		// set update_totals, to generate the second checkout site
		if ( ! isset( $_SESSION[ 'woocommerce_de_in_first_checkout' ] ) ) {
			$_SESSION[ 'woocommerce_de_in_first_checkout' ] = TRUE;
		}
	}


	/**
	* Show shipping time and shipping costs
	* @access public
	* @static
	* @author jj, ap
	* @return void
	* @param bool $show_delivery_time default TRUE
	* @hook woocommerce_single_product_summary
	*/
	public static function add_template_loop_shop ( $show_delivery_time = TRUE ) {
		// fix wp problem, add_filter passes empty string as first argument as default


		if( '' === $show_delivery_time )
			$show_delivery_time = TRUE;

		$data = get_post_meta( get_the_ID(), '_lieferzeit', TRUE );
		$data_delivery_time = get_post_meta( get_the_ID(), '_no_delivery_time_string', TRUE );


		$lieferzeit_string_array = WGM_Defaults::get_lieferzeit_strings();

		// If data = default value or "use the default" or is not set
		if ( (int) $data == -1 || empty( $data ) )
			$lieferzeit = get_option( WGM_Helper::get_wgm_option( 'global_lieferzeit' ) );
		else
			$lieferzeit = $data;

		$deliverytime_term = get_term( $lieferzeit, 'product_delivery_times' );


		if( is_wp_error( $deliverytime_term ) || ! isset( $deliverytime_term ) )
			$lieferzeit_string = __( 'Keine Angabe', Woocommerce_German_Market::get_textdomain() );
		else
			$lieferzeit_string = $deliverytime_term->name;

		if( $lieferzeit_string == __( 'Nutze den Standard', Woocommerce_German_Market::get_textdomain() ) ) {
			$deliverytime_term = get_term( get_option( WGM_Helper::get_wgm_option( 'global_lieferzeit' ) ), 'product_delivery_times' );
			$lieferzeit_string = $deliverytime_term->name;
		}


		if( $show_delivery_time || ! empty( $data_delivery_time ) && get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_delivery_time_overview' ) ) == 'on' ) {
			?>
			<div class="shipping_de shipping_de_string">
				<small>
					<?php if( $show_delivery_time || ! empty( $data_delivery_time ) ) :

						$lieferzeit_output = apply_filters( 'wgm_deliverytime_loop', __( 'Lieferzeit:', Woocommerce_German_Market::get_textdomain() ) . ' ' . $lieferzeit_string,  $lieferzeit_string );
					?>
						<span><?php echo $lieferzeit_output ?></span>

					<?php endif; ?>
				</small>
			</div>
		<?php
		}
	} // end function

    /**
    *  add shipping costs and and discalmer to cart before the buttons
    * @access public
    * @static
    * @author jj, ap
    * @return void
    * @hook woocommerce_widget_shopping_cart_before_buttons
    */
	public static function add_shopping_cart() {

    	if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_disclaimer_cart' ) ) == 'off' )
        		return;
        ?>

        <p class="jde_hint">
       			<?php echo WGM_Template::disclaimer_line(); ?>
       		</p>
		<?php
    }

	/**
	* add shipping costs and and discalmer to cart
	* @access public
	* @static
	* @author jj, ap
	* @return void
	* @hook woocommerce_cart_contents
	*/
	public static function add_shop_table_cart() {

		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_disclaimer_cart' ) ) == 'off' )
			return;

		?>
		<tr class="jde_hint">
			<td colspan="<?php echo apply_filters( 'wgm_colspan_add_shop_table_cart', 7 ); ?>" class="actions">
				<?php echo WGM_Template::disclaimer_line(); ?>
			</td>
		</tr>
		<?php
	}


	/**
	* admin field string template
	*
	* @access public
	* @static
	* @param string $value
	* @return void
	* @hook woocommerce_admin_field_string
	*/
	public static function add_admin_field_string_template( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo $value[ 'name' ]; ?>
			</th>
			<td class="forminp">
				<?php echo esc_attr( $value[ 'desc' ] ); ?>
			</td>
		</tr>
		<?php
	}


	/**
	* print including tax for products
	*
	* @access public
	* @static
	* @author jj, ap
	* @param Product $product
	* @return void
	*/
	public static function text_including_tax( $product ) {

		$tax_print_include_enabled = apply_filters( 'woocommerce_de_print_including_tax', TRUE );

        $is_taxable = false;
        if( method_exists( $product, 'is_taxable' ) ){
            $is_taxable = $product->is_taxable();
        }

		$classes = apply_filters( 'wgm_tax_display_text_classes', '' ); ?>

		<span class="woocommerce-de_price_taxrate <?php echo $classes; ?>">
		<?php

		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) == 'on' ) {

            do_action( 'wgm_before_variation_kleinunternehmerreglung_notice' ); ?>

			<div class="wgm-kleinunternehmerregelung"><?php echo apply_filters( 'woocommerce_de_small_business_regulation_text', __( 'Umsatzsteuerbefreit nach §19 UstG', Woocommerce_German_Market::get_textdomain() ) ); ?></div>

			<?php
            do_action( 'wgm_after_variation_kleinunternehmerreglung_notice' );

		} elseif ( $is_taxable ) {

			$location = wc_get_customer_default_location();

			$tax_rate_args = array(
				'country' 	=>  $location[ 'country' ],
				'state'       => $location[ 'state' ],
				'tax_class'   => $product->get_tax_class()
			);

			$tax_rates = WC_Tax::find_rates( $tax_rate_args );

			$include_string = (  'incl' == get_option( 'woocommerce_tax_display_shop' ) ) ? __( 'inkl.', Woocommerce_German_Market::get_textdomain() ) : __( 'zzgl.', Woocommerce_German_Market::get_textdomain() );

			foreach( $tax_rates as $rate ) {

				if ( $tax_print_include_enabled ) {

					$decimal_length = WGM_Helper::get_decimal_length( $rate[ 'rate' ] );
					$formatted_rate = number_format_i18n( (float)$rate[ 'rate' ], $decimal_length );

		            $tax_display = apply_filters(
		            	'wgm_tax_text',
		            	sprintf( '%1$s %2$s%% %3$s', $include_string, $formatted_rate, $rate[ 'label' ] ),
		                $product,
		                $include_string,
		                $rate
		            );

					echo $tax_display;

		        } else {

					_e( 'MwSt. entfällt', Woocommerce_German_Market::get_textdomain() );
				}
			}
		}
		?>
		</span>
	<?php
	}


	/**
	* Outputs readonly checkout fields
	*
	* @author jj
	* @access public
	* @static
	* @param string key key of field
	* @param array args	contains a list of args for showing the field, merged with defaults (below
	* @return void
	*/
	public static function checkout_readonly_field( $key, $args = array() ) {

		global $woocommerce;
		$woocommerce_checkout = $woocommerce->checkout();

		$defaults = array(
			'type' => 'input',
			'name' => '',
			'label' => '',
			'placeholder' => '',
			'required' => false,
			'class' => array(),
			'label_class' => array(),
			'rel' => '',
			'return' => TRUE
		);

		$args = wp_parse_args( $args, $defaults );

		$field = '';

		if( ! isset( $_SESSION[ 'first_checkout_post_array' ][ $key ] ) )
			return FALSE;

		$value =  $_SESSION[ 'first_checkout_post_array' ][ $key ];

		if( empty( $value ) )
			return FALSE;

		switch ( $args[ 'type' ] ) {
			case "textarea" :
				$field = '<span class="wgm-field-label">' . $args[ 'label' ] . '</span>' . '<span class="wgm-break"></span>' . $value;
				$hidden = sprintf( '<input type="hidden" name="%s" value="%s" /> ', $key, $value );
			break;
			default :
				$field = '<tr><td><span class="wgm-field-label">' . $args[ 'label' ] . '</span></td><td>' . $value  . '</td></tr>';
				$hidden = sprintf( '<input type="hidden" name="%s" value="%s" /> ', $key, $value );
			break;
		}

		if ( $args[ 'return' ] )
			return array( $field, $hidden );
		else
			printf( '%s,%s\n', $field, $hidden );
	}

	/**
	 * Get last checkout Hints
	 *
	 * Likely not used anymore!
	 * @static
	 * @author jj
	 * @return string
	 */
	public static function get_last_checkout_hints( ){
		return WGM_Template::checkout_readonly_field( 'woocommerce_de_last_checkout_hints' );
	}



	/**
	* Set a field of an fields array to a specific value, by passing the fieldtype
	*
	* @static
	* @param array $fields
	* @param string $field_type
	* @param string $changefield
	* @param string $value
	* @return array $fields
	*
	*/

    /**
     * Set a field of an fields array to a specific value, by passing the fieldtype
     * @param array $fields
     * @param string $field_type
     * @param string $changefield
     * @param mixed $value
     * @access public
     * @static
     * @author jj, ap
     * @return array $fields
     */
    public static function set_field_type( $fields, $field_type, $changefield, $value = FALSE ) {

		foreach ( $fields as $key => $field ) {
			if ( isset( $field[ 'type' ] ) && $field_type == $field[ 'type' ] )
				$fields[ $key ][ $changefield ] = $value;
		}

		return $fields;
	}


	/**
	* returns shipping costs and withdraw disclaimer as html with links
	*
	* @access public
	* @static
	* @author jj, ap
	* @uses get_option
	* @return string html
	*/
	public static function disclaimer_line(){
		$versandkosten = '';
		$widerrufsrecht = '';
        $zahlungsarten ='';

		$versandkosten_url  = get_permalink( get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) ) );
		$versandkosten      = '<a class="wgm-versandkosten" href="' . $versandkosten_url . '" target="_blank">' . __( 'Versandkosten', Woocommerce_German_Market::get_textdomain() ) . '</a>';
		$versandkosten_text = sprintf( ' ' . __( 'Informationen zu den %1$s,', Woocommerce_German_Market::get_textdomain() ), $versandkosten );

		$widerrufsrecht_url = get_permalink( get_option( WGM_Helper::get_wgm_option( 'widerruf' ) ) );
		$widerrufsrecht     = '<a class="wgm-widerruf" href="' . $widerrufsrecht_url . '" target="_blank">' . __( 'Widerrufsrecht', Woocommerce_German_Market::get_textdomain() ) . '</a>';
		$widerrufsrecht_text= sprintf( ' ' . __( 'Einzelheiten zum %1$s', Woocommerce_German_Market::get_textdomain() ), $widerrufsrecht );

        $zahlungsarten_url = get_permalink( get_option( WGM_Helper::get_wgm_option( 'zahlungsarten' ) ) );
        $zahlungsarten     = '<a class="wgm-zahlungsarten" href="' . $zahlungsarten_url . '"  target="_blank">' . __( 'Zahlungsarten', Woocommerce_German_Market::get_textdomain() ) . '</a>';
        $zahlungsarten_text= sprintf( ' ' . __( 'und den %s', Woocommerce_German_Market::get_textdomain() ), $zahlungsarten );

		$disclaimer_line_prefix = __( 'Hier finden Sie' , Woocommerce_German_Market::get_textdomain() );

		$html  = '';
		$html .= $disclaimer_line_prefix;

		if ( 'on' === get_option(  WGM_Helper::get_wgm_option( 'woocommerce_de_show_shipping_fee_overview_single' ) ) )
			$html .= $versandkosten_text;

		$html .= $widerrufsrecht_text;

        $html .= $zahlungsarten_text;

		return apply_filters(
			'wgm_disclaimer_line',
			$html,
			$versandkosten_url,
			$versandkosten_text,
			$versandkosten,
			$widerrufsrecht,
			$widerrufsrecht_url,
			$widerrufsrecht_text,
            $zahlungsarten,
            $zahlungsarten_url,
            $zahlungsarten_text,
			$disclaimer_line_prefix
		);
	}


	/**
	* get string from texttemplate directory, if filename is given, else it returns the parameter
	*
	* @access	public
	* @static
	* @author	et, ap
	* @param	string name template filename
	* @param	array params
	* @return	void
	*/
	public static function include_template( $name, $args = array() ) {
		_deprecated_function( 'WGM_Template::include_template', "v2.3", "WGM_Template::load_template" );
		WGM_Template::load_template( $name, $args );
	}


	/**
	* get string from texttemplate directory, if filename is given, else it returns the parameter
	*
	* @access public
	* @param string name template filename
    * @author jj, ap
	* @return string
	*/
	public static function get_text_template( $name ) {

		$path = dirname( __FILE__ ) . '/../text-templates/' . $name;
		if ( file_exists( $path )  ) {
			return file_get_contents( $path );
		} else {
			return $name;
		}
	}

	/**
	 * Adds payment information to the mails
	 * @param WC_Order $order The Woocommerce order object
	 * @access public
	 * @author ap
	 * @since 2.0
	 * @hook woocommerce_email_after_order_table
	 */
	public static function add_paymentmethod_to_mails( $order ){
		$html = '<h3>' . __( 'Zahlungsart', Woocommerce_German_Market::get_textdomain() ) . ': ' . $order->payment_method_title . '</h3>';
		echo apply_filters( 'wgm_add_paymentmethod_to_mails_html', $html, $order );
	}


    /**
     * adds the product short description to the checkout
     * @param string $title
     * @param string $item
     * @return string
     * @author ap
     * @access public
     * @static
     * @hook woocommerce_checkout_item_quantity
     */
    public static function add_product_short_desc_to_checkout_title( $title, $item ){
        if ( get_option( 'woocommerce_de_show_show_short_desc' ) !== 'on' )
			return $title;


		$product_short_desc = $item[ 'data' ]->post->post_excerpt;
		$html = '<span class="wgm-break"></span><span class="product-desc">'  . $product_short_desc . '</span>';
		$title .= apply_filters( 'wgm_add_product_short_desc_to_checkout_title', $html, $title, $item, $product_short_desc );

		return $title;
	}

    /**
     * adds the product short description to the oder listing
     * @access public
     * @static
     * @author ap
     * @param string $title
     * @param string $item
     * @return string
     * @hook woocommerce_checkout_item_quantity
     */
    public static function add_product_short_desc_to_order_title( $title, $item ){

		if ( get_option( 'woocommerce_de_show_show_short_desc' ) !== 'on' )
			return $title;

		$_product = get_product( $item[ 'item_meta'][ '_product_id' ][0] );
		$product_short_desc = $_product->post->post_excerpt;
		$html = '<span class="wgm-break"></span> <span class="product-desc">'  . $product_short_desc . '</span>';

		$title .= apply_filters( 'wgm_add_product_short_desc_to_order_title_html', $html, $title, $item, $product_short_desc );

		return $title;
	}

    public static function add_product_short_desc_to_order_title_mail( $item ){
        if ( get_option( 'woocommerce_de_show_show_short_desc' ) !== 'on' )
			return;

		$_product = get_product( $item[ 'item_meta'][ '_product_id' ][0] );
		$product_short_desc = $_product->post->post_excerpt;

        $prerequisites = get_post_meta( $item[ 'product_id'], 'product_function_desc_textarea', true );

		$html = '<span class="wgm-break"></span> <span class="product-desc">'  . $product_short_desc . '</span>';

        if( ! empty( $item[ 'variation_id' ] ) )
            $id = $item[ 'variation_id' ];
        else
            $id = $item[ 'product_id' ];

        if( WGM_Helper::is_digital( $id ) && $prerequisites != false ){
            $html .= ' <span class="wgm-break"></span><span class="wgm-product-prerequisites">' . $prerequisites . '</span>';
        }

		echo apply_filters( 'wgm_add_product_short_desc_to_order_title_html', $html, $item[ 'name' ], $item, $product_short_desc );
	}


    public static function add_product_function_desc( $title, $item ){

        $prerequisites = get_post_meta( $item[ 'product_id'], 'product_function_desc_textarea', true );

        if( ! empty( $item[ 'variation_id' ] ) )
            $id = $item[ 'variation_id' ];
        else
            $id = $item[ 'product_id' ];

        if( WGM_Helper::is_digital( $id ) && $prerequisites != false ){

            $html = ' <span class="wgm-break"></span><span class="wgm-product-prerequisites">' . $prerequisites . '</span>';
            $title .= apply_filters( 'wgm_add_product_prerequisites_to_order_title_html', $html, $title, $item, $prerequisites );
        }

        return $title;
    }


    public static function add_digital_product_prerequisits(){
        $_product = get_product();

        $prerequisites = get_post_meta( $_product->id, 'product_function_desc_textarea', true );

        if( WGM_Helper::is_digital( $_product->id ) && $prerequisites != false ){

            do_action( 'wgm_before_product_prerequists' );

            $html = '';

            if( $_product->product_type == 'variable' ){

                $notice = '<span class="wgm-digital-variation-notice">';
                $notice .= apply_filters( 'wgm_variation_prerequists_notice_label', __( 'Folgende Ausführungen dieses Produkts sind digital:',
                        Woocommerce_German_Market::get_textdomain() )  );
                $notice .= '</span>';

                $html .= apply_filters( 'wgm_digital_variation_notice_html', $notice );

                $list = '<ul class="wgm-digital-attribute-list">';

                $child_filter = array();

                foreach( $_product->get_children() as $child ){

                    $c_product = get_product( $child );

                    if( $c_product->virtual == 'yes' || $c_product->downloadable == 'yes' ){
                        $child_filter[] = $c_product;

                        $list .= '<li>';

                        $data = array_values( $c_product->variation_data );

                        for( $i=0; $i<=count( $data ) -1; $i++ ){
                            if( empty( $data[ $i ] ) ) continue;
                            $list .= $data[ $i ];

                            if( isset( $data[ $i+1 ] ) && !empty( $data[ $i + 1 ] ) )
                                $list .= apply_filters( 'wgm_digital_variation_notice_attribute_separator',  ' & ' );
                            }
                        }

                        $list .= '</li>';
                }

                $list .= '</ul>';

                $html .= apply_filters( 'wgm_digital_variation_notice_attribues_list', $list, $child_filter );

                $html .= apply_filters(
                			'wgm_variation_prerequists_label',
                			sprintf( '<span class="wgm-product-prerequisites-label">%s</span>', __( 'Für digitale Ausführungen dieses Produkts gelten folgende Voraussetzungen:', Woocommerce_German_Market::get_textdomain() ) )
                			);
            }

            $prerequisites_html = '<div class="wgm-product-prerequisites">' . $prerequisites . '</div>';
            $html .= apply_filters( 'wgm_add_product_prerequisites', $prerequisites_html, $prerequisites );

            echo $html;

            do_action( 'wgm_after_product_prerequists' );
        }
    }


	/**
	* add the extra cost for non eu countries to the product description
	*
	* @access public
	* @static
	* @return void
	* @author ap
	* @hook woocommerce_single_product_summary
	*/
	public static function show_extra_costs_eu(){
		if ( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_show_extra_cost_hint_eu' ) ) !== 'on' )
			return;

		echo apply_filters(
			'wgm_show_extra_costs_eu_html',
			__( '<small class="wgm-extra-costs-eu">Bei Lieferungen in das Nicht-EU-Ausland fallen zusätzliche Zölle, Steuern und Gebühren an</small>', Woocommerce_German_Market::get_textdomain() )
		);
	}

    /**
     * hides the shipping fee if the free shipping limit is reached
     * @author ap
     * @access public
     * @static
     * @param array $available_methods
     * @return array
     * @hook woocommerce_available_shipping_methods
     */
    public static function hide_standard_shipping_when_free_is_available( $available_methods ) {
		if( isset( $available_methods['free_shipping'] ) && isset( $available_methods['flat_rate'] ) && get_option( WGM_Helper::get_wgm_option( 'wgm_dual_shipping_option' ), 'on' ) === 'off' ) {
			unset( $available_methods['flat_rate'] );
		}

		return $available_methods;
	}

	/**
	* removes the deliverytime metabox from the products edit view
	*
	* @access public
	* @static
	* @return void
	* @author ap
	* @hook admin_menu
	*/
	public static function remove_lieferzeit_taxonomy_metabox(){
		remove_meta_box( 'product_delivery_timesdiv', 'product', 'side' );
	}


	public static function kur_notice() {
		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) == 'on' ){
			echo apply_filters(
				'wgm_kur_notice_html',
				'<tr><div class="wgm-kur-notice">' .
				apply_filters(
					'woocommerce_de_small_business_regulation_text',
					__( 'Umsatzsteuerbefreit nach §19 UstG', Woocommerce_German_Market::get_textdomain() )
				) .
				'</div></tr>'
			);
		}
	}

	public static function kur_review_order_notice() {
		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) == 'on' ){
			echo apply_filters(
				'wgm_kur_review_order_notice_html',
				'<tr>
				<td></td>
				<td>
				<div class="wgm-kur-notice-review">' .
				apply_filters(
					'woocommerce_de_small_business_regulation_text',
					__( 'Umsatzsteuerbefreit nach §19 UstG', Woocommerce_German_Market::get_textdomain() )
				) .
				'</div>
				</td>
				</tr>'
			);
		}
	}

	public static function kur_review_order_item( $total_rows ){
		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) !== 'on' )
			return $total_rows;

		$html = ' <small>' .
			apply_filters( 'woocommerce_de_small_business_regulation_text',
				__( 'Umsatzsteuerbefreit nach §19 UstG', Woocommerce_German_Market::get_textdomain() ) ) . '</small>';

		$total_rows['order_total']['value'] .= apply_filters( 'wgm_kur_review_order_item_html', $html );

		return $total_rows;
	}


    public static function add_virtual_product_notice( $name, $_product ){

        if( ! empty( $_product->variation_id ) )
            $id = $_product->variation_id;
        else
            $id = $_product->id;

        if( WGM_Helper::is_digital( $id ) ){
            $digital_keyword = apply_filters( 'wgm_product_name_virtual_notice_keyword', '[Digital]' );

            return apply_filters(
                'wgm_product_name_virtual_notice',
                sprintf( '%s <span class="wgm-virtual-notice">%s</span>', $name, $digital_keyword ),
                $name, $digital_keyword
            );
        }

        return $name;
    }

	/**
	 * Add the "[Digital]" to product name
	 *
	 * @author  ChriCo
	 *
	 * @wp-hook woocommerce_order_get_items
	 *
	 * @param   array $items
	 * @return  array $items
	 */
    public static function filter_order_item_name( $items ){

        $keyword= apply_filters( 'wgm_product_name_virtual_notice_keyword', '[Digital]' );
	    $html   = sprintf( '<span class="wgm-virtual-notice">%s</span>', $keyword );

        foreach( $items as $key => $item ){
            $search = apply_filters(
                'wgm_product_name_virtual_notice',
                $html,
                $item[ 'name' ],
                $keyword
            );

            if( strpos( $item[ 'name' ], $search ) !== FALSE ){
                $item[ 'name' ] = str_replace( $search, $keyword, $item[ 'name' ] );
            }

	        /**
	         * re-assign the value
	         * @issue #421
	         */
	        $items[ $key ] = $item;

        }

        return $items;
    }


}

?>