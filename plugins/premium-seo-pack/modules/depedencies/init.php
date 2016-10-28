<?php
/*
* Define class pspDashboard
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspDashboard') != true) {
    class pspDashboard
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
		
		public $ga = null;
		public $ga_params = array();
		
		public $boxes = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/depedencies/';
			$this->module = $this->the_plugin->cfg['modules']['depedencies'];
			
			if (is_admin()) {
	            add_action( "admin_enqueue_scripts", array( &$this, 'admin_print_styles') );
				add_action( "admin_print_scripts", array( &$this, 'admin_load_scripts') );
			}
			   
			// load the ajax helper
			if ( $this->the_plugin->is_admin === true ) {
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/dashboard/ajax.php' );
				new pspDashboardAjax( $this->the_plugin );
			}
			
			if ( $this->the_plugin->is_admin === true ) {

			// add the boxes
			$this->addBox( 'website_preview', '', $this->website_preview(), array(
				'size' => 'grid_1'
			) );
			
			$this->addBox( 'plugin_depedencies', '', $this->plugin_depedencies(), array(
				'size' => 'grid_3'
			) );
			
			/*$this->addBox( 'dashboard_links', '', $this->links(), array(
				'size' => 'grid_4'
			) );
			
			$this->addBox( 'social', 'Social Statistics', $this->social(), array(
				'size' => 'grid_4'
			) );
			
			$this->addBox( 'audience_overview', 'Audience Overview', $this->audience_overview(), array(
				'size' => 'grid_4'
			) );*/

			$this->addBox( 'aateam_products', 'Other products by AA-Team:', $this->aateam_products(), array(
				'size' => 'grid_4'
			) );
			
			/*
			$this->addBox( 'technologies', 'Technologies', $this->technologies(), array(
				'size' => 'grid_4'
			) );
			*/
			
			$this->addBox( 'support', 'Need AA-Team Support?', $this->support() );
			
			}
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspDashboard Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
	    
		public function admin_print_styles()
		{
			wp_register_style( 'psp-DashboardBoxes', $this->module_folder . 'app.css', false, '1.0' );
        	wp_enqueue_style( 'psp-DashboardBoxes' );
		}
		
		public function admin_load_scripts()
		{
			wp_enqueue_script( 'psp-DashboardBoxes', $this->module_folder . 'app.class.js', array(), '1.0', true );
		}
		
		public function getBoxes()
		{
			$ret_boxes = array();
			if( count($this->boxes) > 0 ){
				foreach ($this->boxes as $key => $value) { 
					$ret_boxes[$key] = $value;
				}
			}
 
			return $ret_boxes;
		}
		
		private function formatAsFreamworkBox( $html_content='', $atts=array() )
		{
			return array(
				'size' 		=> isset($atts['size']) ? $atts['size'] : 'grid_4', // grid_1|grid_2|grid_3|grid_4
	            'header' 	=> isset($atts['header']) ? $atts['header'] : false, // true|false
	            'toggler' 	=> false, // true|false
	            'buttons' 	=> isset($atts['buttons']) ? $atts['buttons'] : false, // true|false
	            'style' 	=> isset($atts['style']) ? $atts['style'] : 'panel-widget', // panel|panel-widget
	            
	            // create the box elements array
	            'elements' => array(
	                array(
	                    'type' => 'html',
	                    'html' => $html_content
	                )
	            )
			);
		}
		
		private function addBox( $id='', $title='', $html='', $atts=array() )
		{ 
			// check if this box is not already in the list
			if( isset($id) && trim($id) != "" && !isset($this->boxes[$id]) ){
				
				$box = array();
				
				$box[] = '<div class="psp-dashboard-status-box">';
				if( isset($title) && trim($title) != "" ){
					$box[] = 	'<h1>' . ( $title ) . '</h1>';
				}
				$box[] = 	$html;
				$box[] = '</div>';
				
				$this->boxes[$id] = $this->formatAsFreamworkBox( implode("\n", $box), $atts );
				
			}
		}
		
		public function formatRow( $content=array() )
		{
			$html = array();
			
			$html[] = '<div class="psp-dashboard-status-box-row">';
			if( isset($content['title']) && trim($content['title']) != "" ){
				$html[] = 	'<h2>' . ( isset($content['title']) ? $content['title'] : 'Untitled' ) . '</h2>';
			}
			if( isset($content['ajax_content']) && $content['ajax_content'] == true ){
				$html[] = '<div class="psp-dashboard-status-box-content is_ajax_content">';
				$html[] = 	'{' . ( isset($content['id']) ? $content['id'] : 'error_id_missing' ) . '}';
				$html[] = '</div>';
			}
			else{
				$html[] = '<div class="psp-dashboard-status-box-content is_ajax_content">';
				$html[] = 	( isset($content['html']) && trim($content['html']) != "" ? $content['html'] : '!!! error_content_missing' );
				$html[] = '</div>';
			}
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function support()
		{
			$html = array();
			$html[] = '<a href="http://support.aa-team.com" target="_blank"><img src="' . ( $this->module_folder ) . 'assets/support_banner.jpg"></a>';
			
			return implode("\n", $html);
		}
		public function social()
		{
			$html = array();
			$html[] = $this->formatRow( array( 
				'id' 			=> 'social_impact',
				'title' 		=> '',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
 
			return implode("\n", $html);
		}
		
		public function audience_overview()
		{
			$html = array();
			$html[] = '<div class="psp-audience-graph" id="psp-audience-visits-graph" data-fromdate="' . ( date('Y-m-d', strtotime("-1 week")) ) . '" data-todate="' . ( date('Y-m-d') ) . '"></div>';

			return  implode("\n", $html);
		}
		
		public function website_preview()
		{
			$html = array();
			/*$html[] = '<div class="psp-website-preview">';
			$html[] = 	'<div class="psp-borwser-frame">';
			$html[] = 		'<img class="the-website-preview" src="' . ( 'http://api.snapito.com/web/ebcb5f2b3d62ccecb1ddc4caa5c5c20b01b040f4/mc?url=' . home_url() ) . '" width="800" height="640">';
			$html[] = 		'<img src="' . ( $this->module_folder ) . 'assets/browser.png" class="browser-preview">';
			$html[] = 	'</div>';*/
			$html[] = '<h3>Review of: <a href="' . ( home_url() ) . '">' . ( get_bloginfo('name') ) . '</a></h3>';
			$html[] = '<h4>Thank you for buying: <a target="_blank" href="http://codecanyon.net/item/plugin/' . ( get_option('psp_register_item_id') ) . '">' . ( get_option('psp_register_item_name') ) . '</a></h4>';
			$html[] = '<h4>Licence: <a target="_blank" href="http://codecanyon.net/licenses/regular_extended">' . ( get_option('psp_register_licence') ) . '</a></h4>';
			$html[] = '</div>';
			
			return  implode("\n", $html);
		}
		
		public function links()
		{
			$html = array();
			$html[] = '<ul class="psp-summary-links">';
			 
			// get all active modules
			foreach ($this->the_plugin->cfg['modules'] as $key => $value) {
 
				if( !in_array( $key, array_keys($this->the_plugin->cfg['activate_modules'])) ) continue;
				
				$module = $key;
				if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
				!$this->the_plugin->capabilities_user_has_module($module) ) {
					continue 1;
				}
				 
				$in_dashboard = isset($value[$key]['in_dashboard']) ? $value[$key]['in_dashboard'] : array();
				//var_dump('<pre>',$value[$key]['in_dashboard'], $key,'</pre>');  
				if( count($in_dashboard) > 0 ){
			
					$html[] = '
						<li>
							<a href="' . ( $in_dashboard['url'] ) . '">
								<img src="' . ( $value['folder_uri']  . $in_dashboard['icon'] ) . '">
								<span class="text">' . ( $value[$key]['menu']['title'] ) . '</span>
							</a>
						</li>';
				}
			}
			
			$html[] = '</ul>';
			
			return implode("\n", $html);
		}

		public function aateam_products()
		{
			$html = array();
			
			$html[] = '<ul class="psp-aa-products-tabs">';
			$html[] = 	'<li class="on">';
			$html[] = 		'<a href="javascript: void(0)" class="psp-aa-items-codecanyon">CodeCanyon</a>';
			$html[] = 	'</li>';
			$html[] = 	'<li>';
			$html[] = 		'<a href="javascript: void(0)" class="psp-aa-items-themeforest">ThemeForest</a>';
			$html[] = 	'</li>';
			$html[] = 	'<li>';
			$html[] = 		'<a href="javascript: void(0)" class="psp-aa-items-graphicriver">GraphicRiver</a>';
			$html[] = 	'</li>';
			$html[] = '</ul>';
			
			$html[] = $this->formatRow( array( 
				'id' 			=> 'aateam_products',
				'title' 		=> '',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
 
			return implode("\n", $html);
		}
		
		public function technologies()
		{
			$html = array();
			$html[] = $this->formatRow( array( 
				'id' 			=> 'server_ip',
				'title' 		=> 'Server IP',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
			
			$html[] = $this->formatRow( array( 
				'id' 			=> 'technologies',
				'title' 		=> 'Technologies',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
			
			$html[] = $this->formatRow( array( 
				'id' 			=> 'charset',
				'title' 		=> 'Charset',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
			 
 
			return implode("\n", $html);
		}

		/**
		 * Plugin Depedencies
		 */
		public function plugin_depedencies() {
			$this->pluginDepedencies = $this->the_plugin->pluginDepedencies;

			$depedenciesStatus = $this->pluginDepedencies->verifyDepedencies();
			return $depedenciesStatus['msg'];
		}
		
    }
}

// Initialize the pspDashboard class
$pspDashboard = new pspDashboard();
//$pspDashboard = pspDashboard::getInstance();