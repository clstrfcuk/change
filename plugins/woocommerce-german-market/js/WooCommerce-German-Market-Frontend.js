jQuery.noConflict();

( function( $ ) {

	woocommerce_de = {

		init: function () {
            this.setupAjax();
			this.remove_totals();
			this.register_payment_update();
			this.on_update_variation();
		},

        setupAjax: function(){
            if( typeof wgm_wpml_ajax_language !== 'undefined' ){
                $.ajaxSetup( { data: {'lang': wgm_wpml_ajax_language } } );
            }
        },

		remove_totals: function () {

			if ( woocommerce_remove_updated_totals == 1 )
				$( '.woocommerce_message' ).remove();
		},

		register_payment_update: function () {

			$( "input[name='payment_method']" ).on( 'change', function () {
				$( 'body' ).trigger( 'update_checkout' );
			});
		},

		// not used anymore with WooCommerce 2.3.5, left here for legacy purposes
		remove_first_checkout_button: function () {

			$( '.checkout-button' ).each( function( index, value ) {

				var $value = $( value );

				if ( ! $value.hasClass( 'second-checkout-button' ) )
					$value.hide();
			});
		},

		on_update_variation: function() {
			$( 'body.single-product' ).on( 'found_variation', '.variations_form', _update_variation );
			$( 'body.single-product' ).on( 'check_variations', '.variations_form', function() {
				if ( $( this ).find( 'option:selected' ).val() === '' ) {
					$( '.price' ).first().show();
					$( '.woocommerce-de_price_taxrate', '.single_variation_wrap' ).hide();
				}
			});
		}
	};

	var blocking = false;
	function _update_variation() {

		// Block request if another is still active
		if ( blocking ) {
			return false;
		}

		blocking = true;

		var variation_id = $( 'input[name=variation_id]' ).val();

		if ( variation_id === '' ) {
			return false;
		}

		// Create and show loading
		var taxrate = $( '.woocommerce-de_price_taxrate').toggleClass( 'wgm-ajax-animation' );
		_loading();
		var loading = window.setInterval( _loading, 150 );

		$.ajax({
			url: wc_cart_fragments_params.ajax_url,
			method: 'POST',
			data: {
				action: 'update_variation',
				variation_id: variation_id
			}
		})
		.then( _set_variation_tax )
		.done( function() {
			if ( $( '.amount', '.single_variation' ).length === 0 ) {
				$( '.price' ).first().show();
				$( '.woocommerce-de_price_taxrate', '.single_variation_wrap' ).hide();
                $( '.woocommerce_de_versandkosten', '.single_variation_wrap' ).hide();
			} else {
				$( '.price' ).first().hide();
				$( '.woocommerce-de_price_taxrate', '.single_variation_wrap' ).show();
                $( '.woocommerce_de_versandkosten', '.single_variation_wrap' ).show();
			}
		})
		.done( function() {
			// Clear poll and release block
			clearInterval( loading );
			blocking = false;
		});
	}

	function _set_variation_tax( tax_template ) {
        $( '.woocommerce_de_versandkosten').remove();
		$( '.woocommerce-de_price_taxrate' )
			.replaceWith( tax_template);

	}

	function _loading() {
		var taxrate = $( '.woocommerce-de_price_taxrate').not( '.wgm-kleinunternehmerregelung + .woocommerce-de_price_taxrate' );

		switch ( taxrate.html() ) {
			case '.':
				taxrate.html( '..' );
				break;
			case '..':
				taxrate.html( '...' );
				break;
			case '...':
				taxrate.html( '.' );
				break;
			default:
				taxrate.html( '.' );
		}
	}

	$( document ).ready( function( $ ) { woocommerce_de.init(); } );

} )( jQuery );