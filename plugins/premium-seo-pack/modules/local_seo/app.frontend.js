var pspGoogleMap = (function($) {
"use strict";

var gmap = {
	init: function( atts, el ) {
		var self = this;

		self.canvas = el;
		self.geocoder = '';
		self.map = '';
		self.marker = '';
		self.zoom = atts['zoom'] || 12;
		self.maptype = (function( maptype ) {
			switch (maptype) {
				case 'roadmap':
					return google.maps.MapTypeId.ROADMAP;
					break;
				case 'satellite':
					return google.maps.MapTypeId.SATELLITE;
					break;
				case 'hybrid':
					return google.maps.MapTypeId.HYBRID;
					break;
				case 'terrain':
					return google.maps.MapTypeId.TERRAIN;
					break;
				default:
					return google.maps.MapTypeId.ROADMAP;
					break;
			}
		})( atts['maptype'] || 'roadmap' );
		self.info = {
			title		: atts['title'],
			address		: atts['address']
		};
		self.latlng = {
			lat			: atts['lat'],
			lng			: atts['lng']
		}

		self.map_init();
	},

	map_view: function( callback, latlng ) {
		var self = this;

		var t;
		var startWhenVisible = function (){
			if ( jQuery( '#'+self.canvas ).is(':visible') ) {

				window.clearInterval(t);

				jQuery.isFunction( callback )
					callback.call( self, latlng );
				return true;
			}
			return false;
		};
		if ( !startWhenVisible() ) {
			// verify every 100 miliseconds till display!
			t = window.setInterval( function(){ startWhenVisible(); }, 100 );
		}
	},

	map_draw: function( latlng ) {
		var self = this;

		var mapOptions = {
			disableDefaultUI: true,
			zoom: self.zoom,
			center: latlng,
			mapTypeId: self.maptype
		}
		if ( !self.map )
			self.map = new google.maps.Map( document.getElementById( self.canvas ), mapOptions );
		else
			self.map.setCenter( latlng );

		if ( self.marker )
			self.marker.setMap(null);

		self.marker = new google.maps.Marker({
			//icon: icon,
			//html: '',
			map: self.map,
			position: latlng,
			title: self.info.title
		});

		self.tooltip( self.map, self.marker );
	},
	
	tooltip: function( map, marker ) {
		var self = this;

		var contentString =	'<div id="content" style="height:100%;"> \
									<div id="siteNotice"> \
									</div> \
									<h1 id="firstHeading" class="firstHeading"><strong>' + self.info.title + '</strong></h1> \
									<div id="bodyContent"><p>' + self.info.address + '</p> \
									</div> \
								</div>';
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		// infowindow will be open on map load
		infowindow.open( map, marker );

		// infowindow will be open on marker event
		google.maps.event.addListener(marker, "click", function() {
			infowindow.open( marker.getMap(), marker );
		});
		
		// re-center map on infowindow close
		google.maps.event.addListener(infowindow, 'closeclick', function() {
			map.setCenter( marker.getPosition() );
		});

		// idle event fired when the map becomes idle after panning or zooming
		/*google.maps.event.addListenerOnce(map, 'idle', function() {
			google.maps.event.trigger(marker,'click');
		});*/
		//google.maps.event.trigger(map, 'resize');
	},

	map_init: function() {
		var self = this;

		self.geocoder = new google.maps.Geocoder();

		var lat = self.latlng.lat, lng = self.latlng.lng;

		if ( jQuery.trim( lat ) != '' && jQuery.trim( lng ) != '' ) ;
		else
			return false;

		var latlng = new google.maps.LatLng( lat, lng );

		jQuery( '#'+self.canvas ).show();
		self.map_view( self.map_draw, latlng );
	}
	
};

$.fn.makeGmap = function() {

	var i = 0;
	return this.each(function() {
		i++;
		
		var $info = $(this).next('.psp-map-info');
		var atts = {
			id				: $info.find('.map-id').text(),
			zoom			: parseInt( $info.find('.map-zoom').text() ),
			maptype			: $info.find('.map-maptype').text(),
			title			: $info.find('.map-title').text(),
			address			: $info.find('.map-address').text(),
			lat				: $info.find('.map-lat').text(),
			lng				: $info.find('.map-lng').text()
		}
		var el = 'psp-map-canvas-' + atts['id'] + '-' + i;
		$(this).attr('id', el);
		gmap.init( atts, $(this).attr('id') );
	});

};
	
return {
	'gmap' 			: gmap
}

})(jQuery);