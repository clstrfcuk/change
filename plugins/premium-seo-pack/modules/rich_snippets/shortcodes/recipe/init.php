<?php
/*
* Define class pspRichSnippets
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSnippet_recipe') != true) {
    class pspSnippet_recipe extends pspRichSnippets
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
        	$ret[]	= '<div itemscope itemtype="http://schema.org/Recipe">';

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

        	if ( !empty($yield) || !empty($prephours) || !empty($prepmins) || !empty($cookhours) || !empty($cookmins) ) {

        		$sc_build .= '<div>';

        		// PREPARATION: hours and minutes
        		if( !empty($prephours) && !empty($prepmins) ) {

        			$prephours_f = sprintf( _n('%d hour', '%d hours', $prephours, 'psp'), $prephours );
        			$prepmins_f  = sprintf( _n('%d minute', '%d minutes', $prepmins, 'psp'), $prepmins );

        			$ret[]	= '<p class="schema_preparation">';
        			$ret[]	= '<span class="schema_strong">' . __('Prep Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="prepTime" content="PT' . $prephours . 'H' . $prepmins . 'M">';
        			$ret[]	= sprintf( __('%s, %s', 'psp'), $prephours_f, $prepmins_f );
        			$ret[]	= '</p>';
        		}
        		// PREPARATION: no minutes
        		elseif( !empty($prephours) && empty($prepmins) ) {

        			$prephours_f = sprintf( _n('%d hour', '%d hours', $prephours, 'psp'), $prephours );

        			$ret[]	= '<p class="schema_preparation">';
        			$ret[]	= '<span class="schema_strong">' . __('Prep Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="prepTime" content="PT' . $prephours . 'H">';
        			$ret[]	= $prephours_f;
        			$ret[]	= '</p>';
        		}
        		// PREPARATION: no hours
        		elseif( !empty($prepmins) && empty($prephours) ) {

        			$prepmins_f = sprintf( _n('%d minute', '%d minutes', $prepmins, 'psp'), $prepmins );

        			$ret[]	= '<p class="schema_preparation">';
        			$ret[]	= '<span class="schema_strong">' . __('Prep Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="prepTime" content="PT' . $prepmins . 'M">';
        			$ret[]	= $prepmins_f;
        			$ret[]	= '</p>';
        		}

        		// COOK: hours and minutes
        		if( !empty($cookhours) && !empty($cookmins) ) {

        			$cookhours_f = sprintf( _n('%d hour', '%d hours', $cookhours, 'psp'), $cookhours );
        			$cookmins_f =  sprintf( _n('%d minute', '%d minutes', $cookmins, 'psp'), $cookmins );

        			$ret[]	= '<p class="schema_cook">';
        			$ret[]	= '<span class="schema_strong">' . __('Cook Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="cookTime" content="PT' . $cookhours . 'H' . $cookmins . 'M">';
        			$ret[]	= sprintf( _x('%s, %s', 'x hours, y minutes', 'psp'), $cookhours_f, $cookmins_f );
        			$ret[]	= '</p>';
        		}
        		// COOK: no minutes
        		elseif( !empty($cookhours) && empty($cookmins) ) {

        			$cookhours_f = sprintf( _n('%d hour', '%d hours', $cookhours, 'psp'), $cookhours );

        			$ret[]	= '<p class="schema_cook">';
        			$ret[]	= '<span class="schema_strong">' . __('Cook Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="cookTime" content="PT' . $cookhours . 'H">';
        			$ret[]	= $cookhours_f;
        			$ret[]	= '</p>';
        		}
        		// COOK: no hours
        		elseif( !empty($cookmins) && empty($cookhours) ) {

        			$cookmins_f =  sprintf( _n('%d minute', '%d minutes', $cookmins, 'psp'), $cookmins );

        			$ret[]	= '<p class="schema_cook">';
        			$ret[]	= '<span class="schema_strong">' . __('Cook Time:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="cookTime" content="PT' . $cookmins . 'M">';
        			$ret[]	= $cookmins_f;
        			$ret[]	= '</p>';
        		}

        		// YIELD
        		if( !empty($yield) ) {

        			$ret[]	= '<p class="schema_yield">';
        			$ret[]	= '<span class="schema_strong">' . __('Yield:', 'psp') . '</span> ';
        			$ret[]	= '<meta itemprop="recipeYield">';
        			$ret[]	= $yield;
        			$ret[]	= '</p>';
        		}

        		$ret[]	= '</div>';
        	}

        	if ( !empty($calories) || !empty($fatcount) || !empty($sugarcount) || !empty($saltcount) ) {

        		$ret[]	= '<div itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">';
        		$ret[]	= '<span class="schema_strong">' . __('Nutrition Information:', 'psp') . '</span><ul>';

        		if ( !empty($calories) ) {
        			$ret[]	= '<li><span itemprop="calories">'. sprintf( _n('%d calorie', '%d calories', $calories, 'psp'), $calories ) . '</span></li>';
        		}

        		if ( !empty($fatcount) ) {
        			$ret[]	= '<li><span itemprop="fatContent">' . sprintf( _n('%d gram of fat', '%d grams of fat', $fatcount, 'psp'), $fatcount ) . '</span></li>';
        		}

        		if ( !empty($sugarcount) ) {
        			$ret[]	= '<li><span itemprop="sugarContent">' . sprintf( _n('%d gram of sugar', '%d grams of sugar', $sugarcount, 'psp'), $sugarcount ) . '</span></li>';
        		}

        		if ( !empty($saltcount) ) {
        			$ret[]	= '<li><span itemprop="sodiumContent">' . sprintf( _n('%d milligram of sodium', '%d milligrams of sodium', $saltcount, 'psp'), $saltcount ) . '</span></li>';
        		}

        		$ret[]	= '</ul></div>';
        	}

        	if ( !empty($instructions) ) {
        		$ret[]	= '<div class="schema_instructions" itemprop="recipeInstructions">
						<span class="schema_strong">' . __('Instructions:', 'psp') . '</span><br />' . esc_attr($instructions) . '
					</div>';
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

// Initialize the pspSnippet_recipe class
$pspSnippet_recipe = new pspSnippet_recipe('recipe');