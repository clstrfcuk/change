<?php
/*
* Define class pspMisc
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLocalSEOSitemapShortcodes') != true) {
    class pspLocalSEOSitemapShortcodes extends pspLocalSEO
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		static protected $_instance;
		
		/**
		*
		* @var XMLWriter
		*/
		private $writer;
		private $domain;
		private $path;
		private $filename = 'sitemap';
		private $current_item = 0;
		private $current_sitemap = 0;

		const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
		const SCHEMA_GEO = 'http://www.google.com/geo/schemas/sitemap/1.0';
		const ITEM_PER_SITEMAP = 100000;
		const SEPERATOR = '-';
		const INDEX_SUFFIX = 'index';
		
		const KML_SCHEMA = 'http://www.opengis.net/kml/2.2';
		const KML_SCHEMA_ATOM = 'http://www.w3.org/2005/Atom';
		
		
		private static $sitemap_type = 'xml';

		

		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
			parent::__construct();
			
			if ( $this->the_plugin->is_admin !== true ) {
				$this->detect_if_sitemap_page();
			}

			add_action( 'init', array( $this, 'init_shortcodes' ) );
        }
        
        
        /**
         * Sitemap
         */
        
        private function detect_if_sitemap_page()
        {
        	$siteurl = get_option('siteurl');
        	$parts = parse_url($siteurl);
        	$path = isset($parts['path']) ? $parts['path'] : '';

        	$uri = $_SERVER['REQUEST_URI'];
        	$path_len = strlen($path);
        	if(strlen($uri) > $path_len && substr($uri,0,$path_len) == $path)
        	{
        		$request = substr($uri,$path_len);
        		$parts = parse_url($request);

        		switch($parts['path'])
        		{
        			case '/sitemap-locations.xml':
        				self::$sitemap_type = 'xml';
        				$this->print_sitemap();
        				break;

        			case '/sitemap-locations.kml':
        				self::$sitemap_type = 'kml';
        				$this->print_sitemap();
        				break;

        			default:
        				break;
        		}
        	}
        }
        
		//get all locations!
		private function get_items( $id = 'all' ) {
			global $wpdb;

			$sqlClause = '';
			if ( (string) $id != 'all' && (int) $id > 0 )
				$sqlClause = " AND a.ID = '$id' ";
			
        	$sql = "SELECT a.ID, a.post_type, a.post_title, a.post_content, a.post_name, b.meta_key, b.meta_value
	            FROM " . $wpdb->prefix . "posts as a
	            LEFT JOIN " . $wpdb->prefix . "postmeta as b
	            ON b.post_id = a.ID
	            WHERE 1=1 " . $sqlClause . " AND a.post_status = 'publish' AND a.post_password = ''
	            AND a.post_type = 'psp_locations'
	            AND (b.meta_key = 'psp_locations_meta' AND !ISNULL(b.meta_value) AND b.meta_value != '')
	            ORDER BY a.post_title ASC
	            LIMIT 1000;";
			
			$res = $wpdb->get_results( $sql );
			return $res;
		}
		
		private function get_item_location( $value = '' ) {

			if ( empty($value) ) return '';

			$meta_value = $value;
			$meta_value = maybe_unserialize($meta_value);
			$meta_value = $meta_value['location'];
			return $meta_value;
		}
        
		//print xml sitemap!
		private function print_sitemap()
		{
			$siteurl = get_option('siteurl');
			$site_parts = parse_url($siteurl);
			//$this->setDomain( $site_parts['scheme'] . '://' . $site_parts['host'] );
			$this->setDomain( $siteurl );
			$this->setPath( '/' );
			$this->setFilename( 'sitemap_index' );
			
			$items = $this->get_items();

			if( count($items) > 0 ) {
	
				$this->text_xml_header();
				$this->addItem();
	
				if ( self::$sitemap_type == 'xml' ) ;
				else if ( self::$sitemap_type == 'kml' ) {

					foreach ($items as $key => $value) {
		
						$this->addItem( $value );
					}
				}

				$this->endSitemap();
			}
			die;
		}
        
		/**
		* Change the header to text/xml
		*
		*/
		private function text_xml_header() 
		{
			header('Cache-Control: no-cache, must-revalidate, max-age=0');
			header('Pragma: no-cache');
			header('X-Robots-Tag: noindex, follow');
			header('Content-Type: text/xml');
		}
	
		/**
		* Returns root path of the website
		*
		* @return string
		*/
		private function getDomain() {
			return $this->domain;
		}
		
		/**
		* Sets root path of the website, starting with http:// or https://
		*
		* @param string $domain
		*/
		public function setDomain($domain) {
			$this->domain = $domain;
			return $this;
		}
		
		/**
		 * Returns XMLWriter object instance
		 *
		 * @return XMLWriter
		 */
		private function getWriter() {
			return $this->writer;
		}
	
		/**
		 * Assigns XMLWriter object instance
		 *
		 * @param XMLWriter $writer 
		 */
		private function setWriter(XMLWriter $writer) {
			$this->writer = $writer;
		}
	
		/**
		 * Returns path of sitemaps
		 * 
		 * @return string
		 */
		private function getPath() {
			return $this->path;
		}
	
		/**
		 * Sets paths of sitemaps
		 * 
		 * @param string $path
		 * @return Sitemap
		 */
		public function setPath($path) {
			$this->path = $path;
			return $this;
		}
	
		/**
		 * Returns filename of sitemap file
		 * 
		 * @return string
		 */
		private function getFilename() {
			return $this->filename;
		}
	
		/**
		 * Sets filename of sitemap file
		 * 
		 * @param string $filename
		 * @return Sitemap
		 */
		public function setFilename($filename) {
			$this->filename = $filename;
			return $this;
		}
	
		/**
		 * Returns current item count
		 *
		 * @return int
		 */
		private function getCurrentItem() {
			return $this->current_item;
		}
	
		/**
		 * Increases item counter
		 * 
		 */
		private function incCurrentItem() {
			$this->current_item = $this->current_item + 1;
		}
	
		/**
		 * Returns current sitemap file count
		 *
		 * @return int
		 */
		private function getCurrentSitemap() {
			return $this->current_sitemap;
		}
	
		/**
		 * Increases sitemap file count
		 * 
		 */
		private function incCurrentSitemap() {
			$this->current_sitemap = $this->current_sitemap + 1;
		}
	
		/**
		 * Prepares sitemap XML document
		 * 
		 */
		private function startSitemap() 
		{
			$this->setWriter(new XMLWriter());
			$this->getWriter()->openURI('php://output');
			$this->getWriter()->startDocument('1.0', 'UTF-8');
			$this->getWriter()->setIndent(true);
			$this->getWriter()->writeComment( 'Sitemap generated using: ' . ( $this->the_plugin->details['plugin_name'] ) );
			$this->getWriter()->writeComment( 'Generated-on=' . ( date("F j, Y, g:i a") ) );
			
			if ( self::$sitemap_type == 'xml' ) {

				$this->getWriter()->startElement('urlset');
				$this->getWriter()->writeAttribute('xmlns', self::SCHEMA);
				$this->getWriter()->writeAttribute('xmlns:geo', self::SCHEMA_GEO);
				
				$this->getWriter()->startElement('url');
				
				$this->getWriter()->writeElement('loc', home_url('/sitemap-locations.kml'));
				
				$this->getWriter()->startElement('geo:geo');
				$this->getWriter()->writeElement('geo:format', 'kml');
				$this->getWriter()->endElement(); // end geo
				
				$this->getWriter()->endElement(); // end url
			} else {
				
				$this->getWriter()->startElement('kml');
				$this->getWriter()->writeAttribute('xmlns', self::KML_SCHEMA);
				$this->getWriter()->writeAttribute('xmlns:atom', self::KML_SCHEMA_ATOM);
				
				$this->getWriter()->startElement('Document');
				
				$this->getWriter()->writeElement('name', get_bloginfo('name'));
				
				$this->getWriter()->startElement('atom:author');
				$this->getWriter()->writeElement('atom:name', get_bloginfo('name'));
				$this->getWriter()->endElement(); // end atom author

				$this->getWriter()->startElement('atom:link');
				$this->getWriter()->writeAttribute('rel', 'related');
				$this->getWriter()->writeAttribute('href', home_url());
				$this->getWriter()->endElement(); // end atom link
			}
		}
	
		/**
		 * Adds an item to sitemap
		 * 
		 */
		public function addItem( $value = '' ) {
			if ( empty($value) ) $value = new stdClass();

			if (($this->getCurrentItem() % self::ITEM_PER_SITEMAP) == 0) {
				if ($this->getWriter() instanceof XMLWriter) {
					$this->endSitemap();
				}
				$this->startSitemap();
				$this->incCurrentSitemap();
			}
			$this->incCurrentItem();

			if ( !isset($value->ID) || empty($value->ID) ) return $this;

			$this->getWriter()->startElement('Placemark');
			$this->getWriter()->writeAttribute('id', $value->ID );
			
			$meta_value = $this->get_item_location( $value->meta_value );
			
			$this->cdataElement( 'name', $meta_value['bname'] );
			$this->cdataElement( 'address', $this->format_address( $meta_value ) );
			$this->cdataElement( 'description', $this->build_description( $meta_value ) );
			
			$this->getWriter()->startElement('Point');
			$this->getWriter()->writeElement('coordinates', ($meta_value['map_latitude'].','.$meta_value['map_longitude'].',0'));
			$this->getWriter()->endElement();

			$this->getWriter()->endElement();

			return $this;
		}
		
		private function cdataElement( $key='', $val='', $forceEmpty=true ) {
			if ( !$forceEmpty ) return false;

			$val = '<![CDATA[' . $val . ']]>';
			$this->getWriter()->writeElement( $key, $val );
		}
	
        /**
		 * Finalizes tags of sitemap XML document.
		 *
	 	*/
        private function endSitemap() {
        	if ( self::$sitemap_type == 'xml' ) ;
        	else {
				$this->getWriter()->endElement(); // end Document
        	}

        	$this->getWriter()->endElement();
        	$this->getWriter()->endDocument();
        }
        
        private function build_address( $value=array() ) {

			$address_format = '{street} {city}, {state} {zipcode} {country}';
			$address = $this->format_address( $value, false, $address_format );
        	return $address;
        }
        
        private function format_address( $value=array(), $is_schema = false, $format = false, $atts = array() ) {

        	if ( !isset($this->settings['address_format']) || empty($this->settings['address_format']) )
        		$address_format = '{street} {city}, {state} {zipcode} {country}';
        	else
	        	$address_format = $this->settings['address_format'];
        	
        	if ( $format !== false )
        		$address_format = $format;
        	
        	$ret = $address_format;
        	
        	
        	// schema.org version!
        	if ( $is_schema === true ) {

        		$emptyValues = array();
        		foreach ( $value as $a => $b )
        			if ( !isset($b) || empty($b) )
        				$emptyValues[ "$a" ] = '';

        		// merge arrays to prevent overwrite empty values!
        		$value = array_merge( array(
        			'address'			=> '<span itemprop="streetAddress">'.$value['address'].'</span>',
        			'city'				=> '<span itemprop="addressLocality">'.$value['city'].'</span>',
        			'state'				=> '<span itemprop="addressRegion">'.$value['state'].'</span>',
        			'zipcode'			=> '<span itemprop="postalCode">'.$value['zipcode'].'</span>',
        			'country'			=> '<span itemprop="addressCountry">'.$value['country'].'</span>'
        			//,'unit'			=> '<span itemprop="unit">'.$value['unit'].'</span>'
        		), $emptyValues );
        	}
        	
        	// verify show attribute in shortcode!
        	if ( is_array($atts) && !empty($atts) ) {
        		foreach ( array('street', 'city', 'state', 'zipcode', 'country') as $field ) {

        			$attribute = 'show_'.$field;
        			if ( isset($atts[ "$attribute" ]) && $atts[ "$attribute" ] === false ) {
        				$value[ $field ] = '';
        			}
        		}
        	}
        	
			$ret = preg_replace('/{street}/iu', $value['address'], $ret);
			$ret = preg_replace('/{city}/iu', $value['city'], $ret);
			$ret = preg_replace('/{state}/iu', $value['state'], $ret);
			$ret = preg_replace('/{zipcode}/iu', $value['zipcode'], $ret);
			$ret = preg_replace('/{country}/iu', $value['country'], $ret);
			//$ret = preg_replace('/{unit}/iu', $value['unit'], $ret);
			
			$ret = preg_replace('/^(\s|,)+/iu', '', $ret); // left trim empty space & ,
			$ret = preg_replace('/(\s|,)+$/iu', '', $ret); // right trim empty space & ,
			return $ret;
        }
        
        private function build_description( $value=array() ) {
        	$ret = array();

        	require($this->module_folder_path . 'lists.inc.php');
        	
        	// business type parent
        	$btypeParent = $value['btype'];
        	foreach ( $psp_business_type_list as $k => $v ) {
        		foreach ( $v as $kk => $vv ) {
        			
        			if ( $value['btype'] == $vv ) {
        				$btypeParent = $k;
        				break 2;
        			}
        		}
        	}
        	
			$ret[] = '
				<div itemscope itemtype="http://schema.org/LocalBusiness">
					<div itemscope itemtype="'.$btypeParent.'">
						<div itemscope itemtype="'.$value['btype'].'">
			';

			// business base info
			if ( isset($value['logo_image']) && !empty($value['logo_image']) )
				$ret[] = 	'<span itemscope itemtype="http://schema.org/ImageObject" itemprop="logo"><img src="'.$value['logo_image'].'" itemprop="image" /></span>';
			if ( isset($value['building_image']) && !empty($value['building_image']) )
				$ret[] = 	'<span itemscope itemtype="http://schema.org/ImageObject" itemprop="photo"><img src="'.$value['building_image'].'" itemprop="image" /></span>';
			if ( isset($value['url']) && !empty($value['url']) ) {
				$ret[] = 	'<a href="'.$value['url'].'" itemprop="url">';
				if ( isset($value['bname']) && !empty($value['bname']) )
					$ret[] = 	'<span itemprop="name">'.$value['bname'].'</span>';
				$ret[] =	'</a>';
			} else {
				if ( isset($value['bname']) && !empty($value['bname']) )
					$ret[] = '<span itemprop="name">'.$value['bname'].'</span>';
			}
			if ( isset($value['description']) && !empty($value['description']) )
				$ret[] = 	'<span itemprop="description">'.$value['description'].'</span>';
			
			// business postal address
			$ret[] = 		'<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

			if ( ( $format_address = $this->format_address( $value, true ) ) !== false )
				$ret[] = $format_address;

			// business contact
			if ( isset($value['phone']) && !empty($value['phone']) )
				$ret[] = 		'<span itemprop="telephone">'.$value['phone'].'</span>';
			if ( isset($value['phone_alt']) && !empty($value['phone_alt']) )
				$ret[] = 		'<span itemprop="telephone">'.$value['phone_alt'].'</span>';
			if ( isset($value['fax']) && !empty($value['fax']) )
				$ret[] = 		'<span itemprop="faxNumber">'.$value['fax'].'</span>';
			if ( isset($value['email']) && !empty($value['email']) )
				$ret[] = 		'<a href="mailto:'.$value['email'].'" itemprop="email">'.$value['email'].'</a>';
				
			$ret[] =		'</div>'; // end PostalAddress

			$ret[] = 		'<div itemscope itemtype="http://schema.org/Place">';
			
			// business geo location
			if ( $value['map_latitude']!='' && $value['map_longitude']!='' ) {

				$ret[] = 		'<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
				if ( isset($value['map_latitude']) && !empty($value['map_latitude']) )
					$ret[] = 		'<meta itemprop="latitude" content="'.$value['map_latitude'].'" />';
				if ( isset($value['map_longitude']) && !empty($value['map_longitude']) )
					$ret[] = 		'<meta itemprop="longitude" content="'.$value['map_longitude'].'" />';
				$ret[] = 		'</div>';
			}
			
			// opening hours
			$ret[] = $this->build_opening_hours( $value );

			$ret[] = 		'</div>'; // end Place

			// payment
			if ( isset($value['payment_forms']) && !empty($value['payment_forms']) )
				$ret[] =	'<span itemprop="paymentAccepted">'.$value['payment_forms'].'</span>';
			if ( isset($value['payment_currency']) && !empty($value['payment_currency']) )
				$ret[] =	'<span itemprop="currenciesAccepted">'.$value['payment_currency'].'</span>';
			if ( isset($value['payment_price_range']) && !empty($value['payment_price_range']) )
				$ret[] =	'<span itemprop="priceRange">'.$value['payment_price_range'].'</span>';
			
			$ret[] = 	'
						</div>
					</div>
				</div>
			';
			
			$ret = implode('', $ret);
        	return $ret;
        }
        
        private function build_opening_hours( $value=array() ) {
        	
        	require($this->module_folder_path . 'lists.inc.php');

			$ret = array();

			if ( count($value['oh']) > 0 ) {
				
				//$ret[] = '<div itemprop="http://purl.org/goodrelations/v1#hasOpeningHoursSpecification" itemscope itemtype="http://purl.org/goodrelations/v1#OpeningHoursSpecification">';
				$ret[] = '<div itemprop="openingHoursSpecification" itemscope itemtype="http://schema.org/OpeningHoursSpecification">';
				if ( isset($value['oh_heading']) && !empty($value['oh_heading']) )
					$ret[] = $value['oh_heading'];
				
				foreach ( $value['oh'] as $k => $val ) {

					$__formated = $val;
					foreach ( $val as $kk => $vv ) {

						if ( $kk == 'day' ) continue 1;
						$__formated[ $kk ] = (int) $vv < 10 ? '0'.$vv : $vv;
					}

					$__from 	= $val['from_hour'] . ':' . $val['from_min'];
					$__to 		= $val['to_hour'] . ':' . $val['to_min'];
					
					$__from2 	= $__formated['from_hour'] . ':' . $__formated['from_min'] . ':00';
					$__to2 		= $__formated['to_hour'] . ':' . $__formated['to_min'] . ':00';
					
					// purl.org/goodrelations version!
					//$ret[] = '<link itemprop="hasOpeningHoursDayOfWeek" href="http://purl.org/goodrelations/v1#'.$psp_days_list[ "{$val['day']}" ].'" />';
			     	//$ret[] = '<meta itemprop="opens" content="'.$__from2.'">'.$__from.' - <meta itemprop="closes" content="'.$__to2.'">'.$__to;
			     	
			     	// schema.org version!
			     	$ret[] = '<link itemprop="dayOfWeek" href="http://purl.org/goodrelations/v1#' . ( isset($psp_days_list[ "{$val['day']}" ]) ? $psp_days_list[ "{$val['day']}" ] : '' ) . '" />' . ( isset($psp_days_list[ "{$val['day']}" ]) ? $psp_days_list[ "{$val['day']}" ] : '' );
			     	$ret[] = '<meta itemprop="opens" content="'.$__from2.'">'.$__from.' - <meta itemprop="closes" content="'.$__to2.'">'.$__to;
				}

				$ret[] = '</div>';
			}

			$ret = implode('', $ret);
        	return $ret;
        }
        
        private function build_geomap( $value=array(), $atts=array() ) {

        	$address = $this->build_address( $value );
        	$__address = str_replace( ' ', '+', $address );
        	
        	if ( !isset($atts['zoom']) || empty($atts['zoom']) )
        		$atts['zoom'] = '12';
        	if ( !isset($atts['width']) || empty($atts['width']) )
        		$atts['width'] = '720';
        	if ( !isset($atts['height']) || empty($atts['height']) )
        		$atts['height'] = '240';
        	if ( !isset($atts['maptype']) || empty($atts['maptype']) )
        		$atts['maptype'] = 'roadmap';
        	if ( !isset($atts['type']) || empty($atts['type']) )
        		$atts['type'] = 'static';
            
        	$type = $atts['type'];
        	if ( $type == 'static' ) { // static map
    			$ret = '
		    		<a href="http://mapof.it/{address}" title="{title}">
		    			<img src="http://maps.googleapis.com/maps/api/staticmap?center={address}&amp;zoom={zoom}&amp;size={width}x{height}&amp;maptype={maptype}&amp;sensor=false&amp;markers={address}" alt="{title}" width="{width}" height="{height}">
		    		</a>';
        	}
			else if ( $type == 'dynamic' ) { // dynamic javascript map
				ob_start();
				
				if( !wp_style_is('psp_gmap_css') ) {
					wp_enqueue_style( 'psp_gmap_css' , $this->module_folder . '/app.frontend.css' );
				}
				if( !wp_script_is('jquery') ) { // first, check to see if it is already loaded
					wp_enqueue_script( 'jquery' , 'https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js' );
				}
				if( !wp_script_is('psp_googlemap') ) {
					wp_enqueue_script( 'psp_googlemap' , $this->get_geo_uri_js() );
				}
				if( !wp_script_is('psp_custom_gmap') ) {
					wp_enqueue_script( 'psp_custom_gmap' , $this->module_folder . '/app.frontend.js', array( 
						'jquery',
						'psp_googlemap'
					) );
				}
				if( wp_script_is('jquery') && wp_script_is('psp_googlemap') && wp_script_is('psp_custom_gmap') ) {
				?>
				<script type="text/javascript">
				(function () {
					var t;
					var startWhenVisible = function () {
						if ( typeof pspGoogleMap === 'undefined' ) {

							return false;
						} else {
		
							window.clearInterval(t);
		
							//jQuery(document).ready(function(){
							jQuery('.psp-map-canvas').makeGmap();
							//});
	
							return true;
						}
					};
					if ( !startWhenVisible() ) {
						// verify every 100 miliseconds till display!
						t = window.setInterval( function(){ startWhenVisible(); }, 100 );
					}
				})();
				</script>
				<?php
				}
				?>
				<div class="psp-map-canvas" style="display: none; width: <?php echo $atts['width']; ?>px; height: <?php echo $atts['height']; ?>px;"></div>
				<div class="psp-map-info" style="display:none;">
					<span class="map-id"><?php echo $atts['id']; ?></span>
					<span class="map-zoom"><?php echo $atts['zoom']; ?></span>
					<span class="map-maptype"><?php echo $atts['maptype']; ?></span>
					<span class="map-title"><?php echo $value['map_name']; ?></span>
					<span class="map-address"><?php echo $address; ?></span>
					<span class="map-lat"><?php echo $value['map_latitude']; ?></span>
					<span class="map-lng"><?php echo $value['map_longitude']; ?></span>
				</div>
				<?php
				$ret = ob_get_contents();
				ob_end_clean();
			}
    		else if ( $type == 'qrcode' ) { // qr code map
    			$ret = '
			    	<iframe width="{width}" height="{height}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://chart.apis.google.com/chart?cht=qr&amp;chs={width}x{height}&amp;chl=http://mapof.it/{address}"></iframe>';
    		}
    		    		
    		$ret = preg_replace('/{address}/iu', $__address, $ret);
    		$ret = preg_replace('/{zoom}/iu', $atts['zoom'], $ret);
    		$ret = preg_replace('/{width}/iu', $atts['width'], $ret);
    		$ret = preg_replace('/{height}/iu', $atts['height'], $ret);
    		$ret = preg_replace('/{maptype}/iu', $atts['maptype'], $ret);
    		$ret = preg_replace('/{title}/iu', $value['map_name'], $ret);
    		
    		return $ret;
        }


        /**
         * Shortcodes
         */
        public function init_shortcodes() {

			// define shortcodes lists
			add_shortcode( 'psp_business', array( $this, 'sh_business') );
			add_shortcode( 'psp_address', array( $this, 'sh_address') );
			add_shortcode( 'psp_contact', array( $this, 'sh_contact') );
			add_shortcode( 'psp_opening_hours', array( $this, 'sh_opening_hours') );
			add_shortcode( 'psp_payment', array( $this, 'sh_payment') );
			add_shortcode( 'psp_gmap', array( $this, 'sh_gmap') );
			add_shortcode( 'psp_full', array( $this, 'sh_full') );
        }
        
        public function shortcode_header( $shortcode = null, $execute = true, $id = null ) {

        	if ( $execute !== true ) return '';

        	$ret = array();
        	
        	$qId = !is_null($id) && (int) $id > 0 ? " data-itemid='psp-$shortcode-$id' " : '';
        	
			$ret[] = '
				<!--begin psp local seo shortcode ' . ($qId != '' ? '-' . $qId : '') . '-->
				<div itemscope itemtype="http://schema.org/LocalBusiness">
			';
			$ret = implode('', $ret);
        	return $ret;
        }
        
        public function shortcode_footer( $shortcode = null, $execute = true, $id = null ) {

        	if ( $execute !== true ) return '';

        	$ret = array();
        	
			$ret[] = 	'
				</div>
				<!--end psp local seo shortcode-->
			';
			$ret = implode('', $ret);
        	return $ret;
        }

        // [psp_business id=all show_name=true show_desc=true show_img_logo=true show_img_building=true]
        public function sh_business( $atts, $content = null ) {

			extract( $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_name' 			=> true,
				'show_desc' 			=> true,
				'show_img_logo' 		=> true,
				'show_img_building' 	=> true
			), $atts ) ) );

			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'business', true ) ) != '' ) $ret[] = $header;
			
			if( !wp_style_is('psp_gmap_css') ) {
				wp_enqueue_style( 'psp_gmap_css' , $this->module_folder . '/app.frontend.css' );
			}
				
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );
					
					$ret[] = '<div class="psp-loc-business">';

					if ( ( $header = $this->shortcode_header( 'business', true, $itemid ) ) != '' ) $ret[] = $header;
					
					// business base info
					if ( $show_img_logo && isset($value['logo_image']) && !empty($value['logo_image']) )
						$ret[] = 	'<span itemscope itemtype="http://schema.org/ImageObject" itemprop="logo"><img src="'.$value['logo_image'].'" itemprop="image" class="psp-company-logo" /></span>';
					if ( $show_name && isset($value['url']) && !empty($value['url']) ) {
						$ret[] = 	'<a href="'.$value['url'].'" itemprop="url">';
						if ( $show_name && isset($value['bname']) && !empty($value['bname']) )
							$ret[] = 	'<span itemprop="name">'.$value['bname'].'</span>';
						$ret[] =	'</a>';
					} else {
						if ( $show_name && isset($value['bname']) && !empty($value['bname']) )
							$ret[] = '<span itemprop="name">'.$value['bname'].'</span>';
					}
					if ( $show_desc && isset($value['description']) && !empty($value['description']) )
						$ret[] = 	'<span itemprop="description">'.$value['description'].'</span>';
					
					if ( $show_img_building && isset($value['building_image']) && !empty($value['building_image']) )
						$ret[] = 	'<span itemscope itemtype="http://schema.org/ImageObject" itemprop="photo"><img src="'.$value['building_image'].'" itemprop="image" /></span>';
						
					if ( ( $footer = $this->shortcode_footer( 'business', true ) ) != '' ) $ret[] = $footer;
					
					$ret[] = '</div>';
				}
			}
			
			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'business', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
        }

        // [psp_address id=all show_street=true show_city=true show_state=true show_zipcode=true show_country=true]
		public function sh_address( $atts, $content = null ) {
			
			$atts = $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_street' 			=> true,
				'show_city' 			=> true,
				'show_state' 			=> true,
				'show_zipcode' 			=> true,
				'show_country' 			=> true
			), $atts ) );
			extract( $atts );
			
			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'address', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'address', true, $itemid ) ) != '' ) $ret[] = $header;

					// business postal address
					$ret[] = 		'<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
		
					$ret[] = $this->format_address( $value, true, false, $atts );
						
					$ret[] =		'</div>';
						
					if ( ( $footer = $this->shortcode_footer( 'address', true ) ) != '' ) $ret[] = $footer;
				}
			}

			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'address', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}

		// [psp_contact id=all show_phone=true show_altphone=true show_fax=true show_email=true]
		public function sh_contact( $atts, $content = null ) {
			
			extract( $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_phone' 			=> true,
				'show_altphone' 		=> true,
				'show_fax' 				=> true,
				'show_email' 			=> true
			), $atts ) ) );

			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'contact', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'contact', true, $itemid ) ) != '' ) $ret[] = $header;

					// business postal address
					$ret[] = 		'<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

					// business contact
					if ( $show_phone && isset($value['phone']) && !empty($value['phone']) )
						$ret[] = 		'<span itemprop="telephone">'.$value['phone'].'</span>';
					if ( $show_altphone && isset($value['phone_alt']) && !empty($value['phone_alt']) )
						$ret[] = 		'<span itemprop="telephone">'.$value['phone_alt'].'</span>';
					if ( $show_fax && isset($value['fax']) && !empty($value['fax']) )
						$ret[] = 		'<span itemprop="faxNumber">'.$value['fax'].'</span>';
					if ( $show_email && isset($value['email']) && !empty($value['email']) )
						$ret[] = 		'<a href="mailto:'.$value['email'].'" itemprop="email">'.$value['email'].'</a>';

					$ret[] =		'</div>';
						
					if ( ( $footer = $this->shortcode_footer( 'contact', true ) ) != '' ) $ret[] = $footer;
				}
			}

			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'contact', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}

		// [psp_opening_hours id=all show_head=true]
		public function sh_opening_hours( $atts, $content = null ) {
			
			require($this->module_folder_path . 'lists.inc.php');
			
			extract( $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_head'				=> true
			), $atts ) ) );

			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'opening_hours', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'opening_hours', true, $itemid ) ) != '' ) $ret[] = $header;

					if ( count($value['oh']) > 0 ) {
						
						// opening hours
						$ret[] = '<div itemscope itemtype="http://schema.org/Place">';
						$ret[] = 	'<div itemprop="openingHoursSpecification" itemscope itemtype="http://schema.org/OpeningHoursSpecification">';
						if ( $show_head && isset($value['oh_heading']) && !empty($value['oh_heading']) )
							$ret[] = $value['oh_heading'];

						foreach ( $value['oh'] as $k => $val ) {
		
							$__formated = $val;
							foreach ( $val as $kk => $vv ) {
		
								if ( $kk == 'day' ) continue 1;
								$__formated[ $kk ] = (int) $vv < 10 ? '0'.$vv : $vv;
							}
		
							$__from 	= $val['from_hour'] . ':' . $val['from_min'];
							$__to 		= $val['to_hour'] . ':' . $val['to_min'];
							
							$__from2 	= $__formated['from_hour'] . ':' . $__formated['from_min'] . ':00';
							$__to2 		= $__formated['to_hour'] . ':' . $__formated['to_min'] . ':00';
							
					     	// schema.org version!
					     	$ret[] = '<link itemprop="dayOfWeek" href="http://purl.org/goodrelations/v1#' . ( isset($psp_days_list[ "{$val['day']}" ]) ? $psp_days_list[ "{$val['day']}" ] : '' ) . '" />' . ( isset($psp_days_list[ "{$val['day']}" ]) ? $psp_days_list[ "{$val['day']}" ] : '' );
					     	$ret[] = '<meta itemprop="opens" content="'.$__from2.'">'.$__from.' - <meta itemprop="closes" content="'.$__to2.'">'.$__to;
						}
	
						$ret[] = 	'</div>';
						$ret[] = '</div>';
					
					}

					if ( ( $footer = $this->shortcode_footer( 'opening_hours', true ) ) != '' ) $ret[] = $footer;
				}
			}

			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'opening_hours', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}

		// [psp_payment id=all show_payment=true show_currencies=true show_pricerange=true]
		public function sh_payment( $atts, $content = null ) {
			
			extract( $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_payment'			=> true,
				'show_currencies'		=> true,
				'show_pricerange'		=> true
			), $atts ) ) );

			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'payment', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'payment', true, $itemid ) ) != '' ) $ret[] = $header;

					// payment
					if ( $show_payment && isset($value['payment_forms']) && !empty($value['payment_forms']) )
						$ret[] =	'<span itemprop="paymentAccepted">'.$value['payment_forms'].'</span>';
					if ( $show_currencies && isset($value['payment_currency']) && !empty($value['payment_currency']) )
						$ret[] =	'<span itemprop="currenciesAccepted">'.$value['payment_currency'].'</span>';
					if ( $show_pricerange && isset($value['payment_price_range']) && !empty($value['payment_price_range']) )
						$ret[] =	'<span itemprop="priceRange">'.$value['payment_price_range'].'</span>';
						
					if ( ( $footer = $this->shortcode_footer( 'payment', true ) ) != '' ) $ret[] = $footer;
				}
			}

			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'payment', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}

		// [psp_gmap id=all width=320 height=240 zoom=12 maptype="roadmap"]
		// maptype values: roadmap | satellite | terrain | hybrid
		// type values: static | dynamic
		public function sh_gmap( $atts, $content = null ) {
			
			$atts = $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'width' 				=> '720',
				'height' 				=> 240,
				'zoom' 					=> 12,
				'maptype' 				=> 'roadmap',
				'type'					=> 'static'
			), $atts ) );
			extract( $atts );

			$ret = array();
			if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'gmap', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'gmap', true, $itemid ) ) != '' ) $ret[] = $header;

					$ret[] = 		'<div itemscope itemtype="http://schema.org/Place">';
					
					// business geo location
					if ( $value['map_latitude']!='' && $value['map_longitude']!='' ) {
		
						$ret[] = 		'<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
						if ( isset($value['map_latitude']) && !empty($value['map_latitude']) )
							$ret[] = 		'<meta itemprop="latitude" content="'.$value['map_latitude'].'" />';
						if ( isset($value['map_longitude']) && !empty($value['map_longitude']) )
							$ret[] = 		'<meta itemprop="longitude" content="'.$value['map_longitude'].'" />';
						$ret[] = 		'</div>';
					}
					
					// business google map
					$ret[] = '<div>';
					$ret[] = $this->build_geomap( $value, array_merge( $atts, array('id' => $itemid ) ) );
					$ret[] = '</div>';
					
					$ret[] = 		'</div>'; // end Place
			
					if ( ( $footer = $this->shortcode_footer( 'gmap', true ) ) != '' ) $ret[] = $footer;
				}
			}

			if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'gmap', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}

		// [psp_full id=all show_business=true show_address=true show_contact=true show_opening_hours=true show_payment=true show_gmap=true]
		public function sh_full( $atts, $content = null ) {
			
			extract( $this->safeBoolean( shortcode_atts( array(
				'id' 					=> 'all',
				'show_business' 		=> true,
				'show_address' 			=> true,
				'show_contact' 			=> true,
				'show_opening_hours' 	=> true,
				'show_payment' 			=> true,
				'show_gmap' 			=> true
			), $atts ) ) );

			$ret = array();
			//if ( (string) $id == 'all' && ( $header = $this->shortcode_header( 'full', true ) ) != '' ) $ret[] = $header;
			
			// body
			$items = $this->get_items( $id );
			if( count($items) > 0 ) {
				foreach ($items as $key => $value) {

					$itemid = $value->ID;
					$value = $this->get_item_location( $value->meta_value );

					if ( ( $header = $this->shortcode_header( 'full', true, $itemid ) ) != '' ) $ret[] = $header;

					if ( $show_business )
						$ret[] = $this->sh_business( array('id' => $itemid), $content );
						
					if ( $show_address )
						$ret[] = $this->sh_address( array('id' => $itemid), $content );

					if ( $show_contact )
						$ret[] = $this->sh_contact( array('id' => $itemid), $content );
						
					if ( $show_opening_hours )
						$ret[] = $this->sh_opening_hours( array('id' => $itemid), $content );
						
					if ( $show_payment )
						$ret[] = $this->sh_payment( array('id' => $itemid), $content );
						
					if ( $show_gmap )
						$ret[] = $this->sh_gmap( array('id' => $itemid), $content );
			
					if ( ( $footer = $this->shortcode_footer( 'full', true ) ) != '' ) $ret[] = $footer;
				}
			}

			//if ( (string) $id == 'all' && ( $footer = $this->shortcode_footer( 'full', true ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
		}


		private function safeBoolean( $atts = array() ) {
			
			if ( !is_array($atts) || empty($atts) ) return array();

			foreach ( $atts as $key => $value ) {
				
				if ( preg_match('/^show_/i', $key) > 0 ) {

					$atts[ "$key" ] = (bool) $value;
					if ( $value === true || $value === 'true' )
						$atts[ "$key" ] = true;
					if ( $value === false || $value === 'false' )
						$atts[ "$key" ] = false;
				}
			}
			return $atts;
		}

		/**
	    * Singleton pattern
	    *
	    * @return pspSEOImages Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
    }
}

// Initialize the pspLocalSEOSitemapShortcodes class
$pspLocalSEOSitemapShortcodes = new pspLocalSEOSitemapShortcodes();