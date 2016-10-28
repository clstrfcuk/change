<?php
/*
* Define class pspRichSnippets
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSnippet_review') != true) {
    class pspSnippet_review extends pspRichSnippets
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

        	// html BOOK
        	$type   = ( !empty($eventtype) ? $eventtype : ucfirst( $this->shortcodeCfg['type'] ) );
        	//$ret[]	= '<div itemscope itemtype="http://schema.org/' . $type .'">';
        	$ret[]	= '<div itemscope itemtype="http://schema.org/Review">';

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
        	
	       	if ( !empty($author) ) {
        		$ret[]	= '<div itemprop="author" itemscope itemtype="http://schema.org/Person">' . __('Written by:', 'psp') . ' <span itemprop="name">' . $author . '</span></div>';
        	}
        	
        	if ( !empty($pubdate) ) {
        		$ret[]	= '<div class="bday"><meta itemprop="datePublished" content="' . $pubdate . '">' . __('Date Published:', 'psp') . ' ' . date('m/d/Y', strtotime($pubdate)) . '</div>';
        	}

        	if ( !empty($item_name) ) {
        		$ret[]	= '<div class="schema_review_name" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name">' . $item_name . '</span></div>';
        	}

        	if ( !empty($review) ) {
        		$ret[]	= '<div class="schema_review_body" itemprop="reviewBody">' . esc_textarea($review) . '</div>';
        	}
        	
        	if ( !empty($current_rating) ) {
        		$ret[]	= '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">';

        		// worst review scale
        		if ( !empty($worst_rating) ) {
        			$ret[]	= '<span itemprop="worstRating">' . $worst_rating . '</span> / ';
        		}

        		$ret[]	= '<span itemprop="ratingValue">' . $current_rating . '</span>';

        		// best review scale
        		if ( !empty($best_rating) ) {
        			$ret[]	= ' / <span itemprop="bestRating">' . $best_rating . '</span> ' . __('stars', 'psp') . '';
        		}

        		$ret[]	= '</div>';
        	}
        	
        	$ret[]	= '</div>';

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

// Initialize the pspSnippet_review class
$pspSnippet_review = new pspSnippet_review('review');