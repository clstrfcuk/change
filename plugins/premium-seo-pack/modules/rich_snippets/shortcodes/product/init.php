<?php
/*
* Define class pspRichSnippets
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSnippet_product') != true) {
    class pspSnippet_product extends pspRichSnippets
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

        	// html PRODUCT
        	$type   = ( !empty($eventtype) ? $eventtype : ucfirst( $this->shortcodeCfg['type'] ) );
        	//$ret[]	= '<div itemscope itemtype="http://schema.org/' . $type .'">';
        	$ret[]	= '<div itemscope itemtype="http://schema.org/Product">';

        	if ( !empty($image) ) {

        		$imgalt = isset($name) ? $name : __($type.' Image', 'psp');
        		$ret[]	= '<img class="schema_image" itemprop="image" src="' . esc_url($image) . '" alt="' . $imgalt . '" />';
        	}

        	if ( !empty($name) && !empty($url) ) {
        		
        		$ret[]	= '<a class="schema_url" target="_blank" itemprop="url" href="' . esc_url($url) . '">';
        		$ret[]	= 	'<div class="schema_name" itemprop="name">' . $name . '</div>';
        		$ret[]	= '</a>';
        	} else if ( !empty($name) && empty($url) ) {
        		
        		$ret[]	= '<div class="schema_name" itemprop="name">' . $name . '</div>';
        	}

        	if ( !empty($description) ) {
        		$ret[]	= '<div class="schema_description" itemprop="description">' . esc_attr($description) . '</div>';
        	}

        	if ( !empty($brand) ) {
        		$ret[]	= '<div class="brand" itemprop="brand" itemscope itemtype="http://schema.org/Organization">
						<span class="desc_type">' . __('Brand:', 'psp') . '</span> <span itemprop="name">' . $brand . '</span>
					</div>';
        	}

        	if ( !empty($manufacturer) ) {
        		$ret[]	= '<div class="manufacturer" itemprop="manufacturer" itemscope itemtype="http://schema.org/Organization">
						<span class="desc_type">' . __('Manufacturer:', 'psp') . '</span> <span itemprop="name">' . $manufacturer . '</span>
					</div>';
        	}

        	if ( !empty($model) ) {
        		$ret[]	= '<div class="model"><span class="desc_type">' . __('Model:', 'psp') . '</span> <span itemprop="model">' . $model . '</span></div>';
        	}

        	if ( !empty($prod_id) ) {
        		$ret[]	= '<div class="prod_id"><span class="desc_type">' . __('Product ID:', 'psp') . '</span> <span itemprop="productID">' . $prod_id . '</span></div>';
        	}
        	
        	$__rating = array();
        	if ( !empty($current_rating) ) {
        		// worst review scale
        		if ( !empty($worst_rating) ) {
        			$__rating[]	= '<span itemprop="worstRating">' . $worst_rating . '</span> / ';
        		}

        		$__rating[]	= '<span itemprop="ratingValue">' . $current_rating . '</span>';

        		// best review scale
        		if ( !empty($best_rating) ) {
        			$__rating[]	= ' / <span itemprop="bestRating">' . $best_rating . '</span> ' . __('stars', 'psp') . '';
        		}
        	}
        	
        	// rating fields
        	if( !empty($avg_rating) && !empty($nb_reviews) ) {

	        	$ret[]	= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';

	        	$ret[]	= '<span itemprop="ratingCount">' . $avg_rating . '</span> ' . __('based on', 'psp') . ' ';
	        	$ret[]	= '<span itemprop="reviewCount">' . $nb_reviews . '</span> '. __('reviews', 'psp') . '';
	        	
	        	if ( !empty($__rating) )
	        		$ret[] = implode(PHP_EOL, $__rating);

	        	$ret[]	= '</div>';
        	} else if ( empty($nb_reviews) && !empty($avg_rating) ) {
        		
	        	$ret[]	= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
	        	$ret[]  = '<span itemprop="ratingCount">' . $avg_rating . '</span> ' . __('total ratings', 'psp');

	        	if ( !empty($__rating) )
	        		$ret[] = implode(PHP_EOL, $__rating);

	        	$ret[]  = '</div>';
        	} else if ( empty($avg_rating) && !empty($nb_reviews) ) {
        		
	        	$ret[]	= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
	        	$ret[]  = '<span itemprop="reviewCount">' . $nb_reviews . '</span> ' . __('total reviews', 'psp');
	        	
	        	if ( !empty($__rating) )
	        		$ret[] = implode(PHP_EOL, $__rating);

	        	$ret[]  = '</div>';
        	}

        	if ( !empty($price) && ( !empty($condition) || !empty($availability) ) ) {

        		$ret[]	= '<div class="offers" itemprop="offers" itemscope itemtype="http://schema.org/Offer">';
        		$ret[]	= 	'<span class="price" itemprop="price">' . $price . '</span>';
        		
        		$currency = !empty($currency) ? $currency : 'USD';
        		if ( !empty($currency) ) {
	        		$ret[]	= 	'<span class="price_currency" itemprop="priceCurrency">' . $currency . '</span>';
        		}

        		if ( !empty($condition) ) {
        			$ret[]	= 	'<link itemprop="itemCondition" href="http://schema.org/' . $condition . 'Condition" /> ' . $condition . '';
        		}
        		if ( !empty($availability) ) {
       				$ret[]	= 	'<span class="availability" itemprop="availability" content="' . $availability . '"> ' . $availability . '</span>';
       			}

        		$ret[]	= '</div>';
        	}
        	if ( ( empty($condition) && empty($availability) ) && !empty ($price) ) {
        		
        		$currency = !empty($currency) ? $currency : 'USD';

        		$ret[]	= '<div class="offers" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span class="price" itemprop="price">' . $price . '</span><span class="price_currency" itemprop="priceCurrency">' . $currency . '</span></div>';
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

// Initialize the pspSnippet_product class
$pspSnippet_product = new pspSnippet_product('product');