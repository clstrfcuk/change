<?php
/**
 * Helper Functions
 *
 * @author jj, ap
 */
class WGM_Helper {

	/**
	 * Returns the decimal length of any scalar value.
	 *
	 * @param   int|float|string|bool $value
	 * @return  int
	 */
	public static function get_decimal_length( $value ) {

		if ( ! is_scalar( $value ) ) {
			return 0;
		}

		$value = (float) $value;

		$value = strrchr( $value, "." );
		$value = substr( $value, 1 );
		$value = strlen( $value );

		return $value;
	}

    /**
     * Replaces umlauts etc.
     *
     * @param string $name
     * @return mixed
     */
    public static function get_page_slug( $name ){
        return str_replace( array( ' ', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß', '&rarr;', '__', '&amp;' ) , array( '_', 'ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss', '_', '', ''), strtolower( $name ) );
    }

	/**
	* get the page_id from db by name of page
	*
	* @access	public
	* @static
	* @param	string $page_name
	* @return	int page_id the page id
	*/
	public static function get_page_id( $page_name ) {
		global $wpdb;

		$page_id = $wpdb->get_var( 'SELECT
										ID
									FROM
										' . $wpdb->posts . '
									WHERE
										post_name = "' . $page_name . '"
									AND
										post_status = "publish"
									AND
										post_type = "page"'
								 );

		return (int) $page_id;
	}

	/**
	* Gets the url to the check page and then to checkout form the core plugin
	*
	* @access	public
	* @uses		get_option, get_permalink, is_ssl
	* @static
	* @return	string link to checkout page
	*/
	public static function get_check_url() {

		$check_page_id = get_option( WGM_Helper::get_wgm_option( 'check' ) );

        //WPML Support
        if( function_exists( 'icl_object_id' ) ) {
            $check_page_id = icl_object_id( $check_page_id, 'page', true );
        }

        $permalink     = get_permalink( $check_page_id );


		if ( is_ssl() )
			$permalink = str_replace( 'http:', 'https:', $permalink );

		return $permalink;
	}

	/**
	* gets the checkout page_id
	*
	* @access	public
	* @uses		get_option
	* @static
	* @return 	int checkout poge id
	*/
	public static function get_checkout_redirect_page_id() {
		return get_option( WGM_Helper::get_wgm_option( 'check' ) );
	}

	/**
	* get checkout page id via filter
	*
	* @param int $checkout_redirect_page_id checkout poge id
	* @return int checkout poge id
	*/
	public static function change_checkout_redirect_page_id ( $checkout_redirect_page_id ) {
		return apply_filters( 'woocommerce_de_get_checkout_redirect_page_id', WGM_Helper::get_checkout_redirect_page_id() );
	}

	/**
	* get the default pages
	*
	* @access public
	* @static
	* @author jj, ap
	* @return array default pages
	*/
	public static function get_default_pages() {

		// get data from current user for add pages with his ID
		$user_data = wp_get_current_user();

        foreach( WGM_Defaults::get_default_page_objects() as $page ){

            $default_pages[ $page->slug ] = array(
                                    'post_status'       => $page->status,
                                    'post_type'         => 'page',
                                    'post_author'       => (int) $user_data->data->ID,
                                    'post_name'         => $page->slug,
                                    'post_title'        => $page->name,
                                    'post_content'      => apply_filters( 'woocommerce_de_' . $page->slug . '_content', WGM_Template::get_text_template( $page->content ) ),
                                    'comment_status'    => 'closed'
                                );
        }

		return $default_pages;
	}

    /**
     * Deactivates Pay for order button
     *
     * @param  int $post_id The Post ID
     * @return void
     */
    public static function deactivate_pay_for_order_button( $post_id ) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".order_actions .button[name=invoice]").parent().hide();
		});
		</script>
		<?php
	}

	/**
	 * Determines wether to show shipping address or not
	 * @author jj, ap
	 * @static
	 * @global $woocommerce
	 * @return bool should show shipping or not
	 */
	public static function ship_to_billing() {

		global $woocommerce;

		if( ( isset( $_SESSION[ 'first_checkout_post_array' ][ 'shiptobilling' ] ) &&
			'1' == $_SESSION[ 'first_checkout_post_array' ][ 'shiptobilling' ] ) ||
			$woocommerce->cart->ship_to_billing_address_only() )
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Get an Option name for WooCommerce German Market
	 *
	 * @since	1.1.5
	 * @static
	 * @access	public
	 * @param	string $option_index
	 * @return	mixed string of option, when not exist FALSE
	 */
	public static function get_wgm_option( $option_index ) {

		// geht the default option array
		$options = WGM_Defaults::get_options();

		if( isset( $options[ $option_index ] ) )
			return  $options[ $option_index ];
		else
			return FALSE;
	}

	/**
	* update option if not exists
	*
	* @access	public
	* @static
	* @uses		update_option, get_option
	* @param 	string $option
	* @param 	string $value
	* @return	void
	*/
	public static function update_option_if_not_exist( $option, $value ) {
		if( ! get_option( $option ) )
			update_option( $option, $value );
	}

	/**
	* inserts a given element before key into given array
	*
	* @access public
	* @author jj, ap
	* @param array $array
	* @param string $key
	* @param string $element
	* @return array items
	*/
	public function insert_array_before_key( $array, $key, $element ) {

		if( in_array( key( $element ), array_keys( $array ) ) )
			return $array;

		$position = array_search( $key ,array_keys( $array ) );
		$before   = array_slice( $array, 0, $position );
		$after    = array_slice( $array, $position );

		return array_merge( $before, $element, $after );
	}

    /**
     * Adds bodyclass to second checkout
     * @param array $classes
     * @return array
     * @author ap
     */
    public static function add_checkout_body_classes( $classes) {

		global $woocommerce;

		$classes = ( array ) $classes;

		// id of the second checkout page
		$check_page_id = absint( get_option( WGM_Helper::get_wgm_option( 'check' ) ) );

		// current page id
		$current_id =  @get_the_ID();

		if( ! empty( $woocommerce ) && is_object( $woocommerce ) && $current_id == $check_page_id ) {
			$classes[] = 'woocommerce';
            $classes[] = 'woocommerce-checkout';
            $classes[] = 'woocommerce-page';
            $classes[] = 'wgm-second-checkout';
		}

		return $classes;
	}

    /**
     * Enforced certain settings for the small business regulation setting.
     * @author ap
     * @return void
     */
    public static function check_kleinunternehmerregelung(){

		if( get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_kleinunternehmerregelung' ) ) == 'on' ){
			// Enforce that all prices do not include tax
			update_option( 'woocommerce_prices_include_tax', 'no' );
			// Don't calc the taxes
			update_option( 'woocommerce_calc_taxes', 'no' );
		}
	}


    /**
     * Filters and replaces deliveryimes
     * @param string $string
     * @param string $deliverytime
     * @author ap
     * @return string
     */
    public static function filter_deliverytimes( $string, $deliverytime ){
		if( $deliverytime == __( 'Sofort lieferbar', Woocommerce_German_Market::get_textdomain() ) )
			$string = ', ' . __( 'Lieferzeit:', Woocommerce_German_Market::get_textdomain() ) . ' ' . $deliverytime;
		return $string;
	}

    /**
     * Removes postcount on deliverytimes backend page
     * @author ap
     * @param array $cols
     * @return array
     */
    public static function remove_deliverytime_postcount_columns( $cols ){
		unset( $cols['posts'] );
		return $cols;
	}


    public static function remove_woo_vat_notice(){
        return '';
    }


    public static function is_digital( $product_id = 0 ){

        //When the product_id is an array get the first entry as the product id
        if( is_array($product_id) )
            $product_id = current($product_id);

        if( $product_id == 0 ){
            $product = get_product();
        } else {
            $product = get_product( $product_id );
        }

        if( ! isset( $product->product_type ) || empty( $product->product_type ) )
            return false;

        if( $product->product_type == 'variable' ){
            $is_digital = false;

            foreach( $product->get_children() as $child ){
                $p = get_product( $child );

                if( $p->is_virtual() || $p->is_downloadable() ){
                    $is_digital = true;
                }
            }

            return $is_digital;
        }


        if( $product->product_type == 'variation' ){
            return ( $product->virtual == 'yes' || $product->downloadable == 'yes' );
        }

        if( method_exists( $product, 'is_virtual' ) && method_exists( $product, 'is_downloadable' ) ) {
            return ( $product->is_virtual() || $product->is_downloadable() );
        }
    }

    public static function paypal_fix( $args ){
        $args[ 'return' ] = urldecode( $args[ 'return' ] );
        $args[ 'cancel_return' ] = urldecode( $args[ 'cancel_return' ] );

        return $args;
    }

    public static function _hack( $rate, $shipping ){

	    if( WGM_Tax::is_kur() ){
		    return $rate;
	    }


	    $tax = array_values( WGM_Helper::calculate_additinal_services( $rate->cost ) );

        foreach ( $tax as $key => $val ){
            $rate->taxes[ $key ] = $val;
        }

        return $rate;
    }

	public static function change_payment_gateway_order_button_text(){
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		foreach( $available_gateways as $gateway ){
			$gateway->order_button_text = __( 'weiter', Woocommerce_German_Market::get_textdomain() );
		}
	}

	/**
	 * Disable shipping for virtual products
	 * @param bool $need_shipping
	 * @access public
	 * @since 2.4.10
	 * @author ap
	 * @wp-hook woocommerce_cart_needs_shipping
	 * @return bool $need_shipping
	 */
	public static function virtual_needs_shipping( $need_shipping ){

		$cart = WC()->cart->get_cart();
		$dcount = 0;

		foreach( $cart as $item ) {
			if( empty( $item[ 'variation_id' ] ) )
				$product = get_product( $item['product_id'] );
			else
				$product = get_product( $item[ 'variation_id' ] );

			if( $product->is_virtual() || $product->is_downloadable() ){
				$dcount++;
			}
		}

		if( $dcount == count( $cart ) ){
			return false;
		} else {
			return $need_shipping;
		}
	}
}
?>