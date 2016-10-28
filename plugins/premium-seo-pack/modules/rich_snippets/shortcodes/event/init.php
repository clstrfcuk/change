<?php
/*
* Define class pspRichSnippets
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSnippet_event') != true) {
    class pspSnippet_event extends pspRichSnippets
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		protected $module_folder = '';
		protected $module_folder_path = '';
		
		static protected $_instance;


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $shortcode=null )
        {
        	global $psp;

        	// access parent class!
        	$this->shortcode_cfg( $shortcode, array(
        		'type'			=> $shortcode,
        		'execute'		=> true
        	) );
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/rich_snippets/shortcodes/'.$this->shortcode.'/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/shortcodes/'.$this->shortcode.'/';
			
			$this->init();
        }
        
        
        public function init() {
        	
        	$shortcode = $this->the_plugin->alias . '_rs_' . $this->shortcode;
        	add_shortcode( $shortcode, array( $this, 'gethtml') );
        }
        
        public function gethtml( $atts = array(), $content = null ) {
        	$ret = array();

        	// the attributes
        	extract( $this->shortcode_atts($atts, $content) );

        	// html EVENT
        	$type   = ( !empty($eventtype) ? $eventtype : ucfirst( $this->shortcodeCfg['type'] ) );
        	$ret[]	= '<div itemscope itemtype="http://schema.org/' . $type .'">';

        	if ( !empty($image) ) {

        		$imgalt = isset($name) ? $name : __($type.' Image', 'psp');
        		$ret[]	= '<img class="schema_image" itemprop="image" src="' . esc_url($image) . '" alt="' . $imgalt . '" />';
        	}

        	if ( !empty($name) && !empty($url) ) {

        		$ret[]	= '<a class="schema_url" target="_blank" itemprop="url" href="' . esc_url($url) . '">';
        		$ret[]	= 	'<div class="schema_name" itemprop="name">' . $name . '</div>';
        		$ret[]	= '</a>';
        	} else 	if ( !empty($name) && empty($url) ) {

        		$ret[]	= '<div class="schema_name" itemprop="name">' . $name . '</div>';
        	}

        	if ( !empty($description) ) {
        		$ret[]	= '<div class="schema_description" itemprop="description">' . esc_attr($description) . '</div>';
        	}

        	if ( !empty($startdate) && !empty($starttime) ) {

	        	$meta_datetime = $startdate . 'T' . date('G:i', strtotime( $startdate . $starttime ));
    	    	$ret[]	= '<div><meta itemprop="startDate" content="' . $meta_datetime . '">' . __('Starts:', 'psp') . ' ' . date('m/d/Y', strtotime($startdate)) . ' ' . $starttime . '</div>';
        	} else if ( empty($starttime) && !empty($startdate) ) {
        		
        		$ret[]	= '<div><meta itemprop="startDate" content="' . $startdate . '">' . __('Starts:', 'psp') . ' ' . date('m/d/Y', strtotime($startdate)) . '</div>';
        	}

        	if ( !empty($enddate) ) {
        		
        		$ret[]	= '<div><meta itemprop="endDate" content="' . $enddate . ':00.000">' . __('Ends:', 'psp') . ' ' . date('m/d/Y', strtotime($enddate)) . '</div>';
        	}

        	if ( !empty($duration) ) {

        		$hour_format	= date('G', strtotime($duration));
        		$mins_format	= date('i', strtotime($duration));

        		$hours		= ( !empty($hour_format) && $hour_format > 0 ? $hour_format . ' ' . __('hours:', 'psp') : '' );
        		$minutes	= ( !empty($mins_format) && $mins_format > 0 ? ' ' . __('and', 'psp') . ' ' . $mins_format . ' ' . __('minutes', 'psp') : '' );

        		$ret[]	= '<div><meta itemprop="duration" content="0000-00-00T' . $duration . '">' . __('Duration:', 'psp') . ' ' . $hours . $minutes . '</div>';
        	}

        	$ret[]	= '</div>';

        	// POSTAL ADDRESS
        	if(	!empty($street) || !empty($pobox) || !empty($city) || !empty($state)
        		|| !empty($postalcode) || !empty($country) ) {
        		
        			$ret[]	= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
        	}

        	if ( !empty($street) ) {
        		
        		$ret[]	= '<div class="street" itemprop="streetAddress">' . $street . '</div>';
        	}

        	if ( !empty($pobox) ) {
        	
        		$ret[]	= '<div class="pobox">' . __('P.O. Box:', 'psp') . ' <span itemprop="postOfficeBoxNumber">' . $pobox . '</span></div>';
        	}

        	if ( !empty($city) && !empty($state) ) {

        		$ret[]	= '<div class="city_state">';
        		$ret[]	= 	'<span class="locale" itemprop="addressLocality">' . $city . '</span>,';
        		$ret[]	= 	'<span class="region" itemprop="addressRegion"> ' . $state . '</span>';
        		$ret[]	= '</div>';
        	} else if ( empty($state) && !empty($city) ) {
        	
        		$ret[]	= '<div class="city_state"><span class="locale" itemprop="addressLocality">' . $city . '</span></div>';
        	} else if ( empty($city) && !empty($state) ) {
        		
        		$ret[]	= '<div class="city_state"><span class="region" itemprop="addressRegion">' . $state . '</span></div>';
        	}

        	if ( !empty($postalcode) ) {
        		
        		$ret[]	= '<div class="postalcode" itemprop="postalCode">' . $postalcode . '</div>';
        	}

        	if ( !empty($country) ) {
        		
        		$ret[]	= '<div class="country" itemprop="addressCountry">' . $country . '</div>';
        	}

        	if(	!empty($street) || !empty($pobox) || !empty($city) || !empty($state)
        		|| !empty($postalcode) || !empty($country) ) {
        	
        			$ret[]	= '</div>';
        	}
        	// end POSTAL ADDRESS

        	// geo location & place
        	$ret[] = 		'<div itemscope itemtype="http://schema.org/Place">';
            if ( !empty($place_name) ) {
                $ret[]  = '<div class="schema_name" itemprop="name">' . $place_name . '</div>';
            }
        	if ( $map_latitude!='' && $map_longitude!='' ) {

        		$ret[] = 		'<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        		if ( isset($map_latitude) && !empty($map_latitude) )
        			$ret[] = 		'<meta itemprop="latitude" content="' . $map_latitude . '" />';
        		if ( isset($map_longitude) && !empty($map_longitude) )
        			$ret[] = 		'<meta itemprop="longitude" content="' . $map_longitude . '" />';
        		$ret[] = 		'</div>';
        	}
        	$ret[] = 		'</div>'; // end Place

			// build Full html!
        	return $this->shortcode_execute( $ret, $atts, $content );
        }
        
		
		/**
	    * Singleton pattern
	    *
	    * @return pspSnippet_event Singleton instance
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

// Initialize the pspSnippet_event class
$pspSnippet_event = new pspSnippet_event('event');