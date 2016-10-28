jQuery.noConflict();

( function( $ ) {

	woocommerce_de = {

		init: function () {

			this.scale_unit_hint();
		},

		scale_unit_hint: function () {

			if ( $( '#woocommerce_attributes .toolbar' ).length > 1)
				$( '#woocommerce_attributes .toolbar' )
					.last()
					.append( '<small>Sie können weitere Maßeinheiten unter <a href="' + woocommerce_product_attributes_url + '">Produkte &rarr; Attribute</a> anlegen.</small>' );
		}
	};

	$( document ).ready( function( $ ) { woocommerce_de.init(); } );

} )( jQuery );