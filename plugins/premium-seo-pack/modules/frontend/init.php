<?php
/*
* Define class pspFrontend
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspFrontend') != true) {
    class pspFrontend
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';
		
		static protected $_instance;
		
		//custom attributes
		private $plugin_settings = array();


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/frontend/';
			$this->module = $this->the_plugin->cfg['modules']['frontend'];

			$this->init();
        }
        
        /**
         * Head Filters & Init!
         *
         */
        
		public function init() {
			//header meta tags & related!
			add_action( 'wp_head', array( &$this, 'make_head' ), 1 );
			
			//footer related!
			add_action( 'wp_footer', array( &$this, 'make_footer' ), 1 );
			
			//wp virtual robots.txt
			add_action('do_robots', array( &$this, 'do_virtual_robots' ), 100, 0);
		}
		
		public function make_head() {
			global $wp_query;
			
			if ( !has_action('premiumseo_head') )
				return true;

			$__wp_query = null;
	
			if ( !$wp_query->is_main_query() ) {
				$__wp_query = $wp_query;
				wp_reset_query();
			}
 
			echo PHP_EOL . "<!-- start/ " . ($this->the_plugin->details['plugin_name']) . " -->" . PHP_EOL;

			do_action( 'premiumseo_head' );

			echo "<!-- end/ " . ($this->the_plugin->details['plugin_name']) . " -->" . PHP_EOL.PHP_EOL;
	
			if ( !empty($__wp_query) ) {
				$GLOBALS['wp_query'] = $__wp_query;
				unset( $__wp_query );
			}
	
			return true;
		}
		
		public function make_footer() {
			global $wp_query;
			
			if ( !has_action('premiumseo_footer') )
				return true;

			$__wp_query = null;
	
			if ( !$wp_query->is_main_query() ) {
				$__wp_query = $wp_query;
				wp_reset_query();
			}
 
			echo PHP_EOL . "<!-- start/ " . ($this->the_plugin->details['plugin_name']) . " -->" . PHP_EOL;

			do_action( 'premiumseo_footer' );

			echo "<!-- end/ " . ($this->the_plugin->details['plugin_name']) . " -->" . PHP_EOL.PHP_EOL;
	
			if ( !empty($__wp_query) ) {
				$GLOBALS['wp_query'] = $__wp_query;
				unset( $__wp_query );
			}
	
			return true;
		}
		
		public function do_virtual_robots() {
			if ( !$this->the_plugin->verify_module_status( 'sitemap' ) ) //module is inactive
				return false;
			
			$sitemapUrl = home_url('/sitemap-index.xml');
			$sitemapUrl_images = home_url('/sitemap-images.xml');
			$sitemapUrl_videos = home_url('/sitemap-videos.xml');

			$option = $this->the_plugin->get_theoption('psp_sitemap');
			if ( $option === false || !isset($option['notify_virtual_robots']) ) return false;

			if ( $option['notify_virtual_robots'] == 'yes' ) {
				echo  PHP_EOL . "sitemap: " . $sitemapUrl;
				echo  PHP_EOL . "sitemap: " . $sitemapUrl_images;
				echo  PHP_EOL . "sitemap: " . $sitemapUrl_videos . PHP_EOL;
			}
			return false;
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

// Initialize the pspSEOImages class
//$pspFrontend = new pspFrontend();
$pspFrontend = pspFrontend::getInstance();