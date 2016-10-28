<?php
/*
* Define class pspSocialStats
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSocialStats') != true) {
    class pspSocialStats
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
		

		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Social_Stats/';
			$this->module = $this->the_plugin->cfg['modules']['Social_Stats'];
			
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_social' );
			
			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			$this->init();
			
			// social sharing
			if ( $this->the_plugin->is_admin !== true )
				$this->init_social_sharing();
        }
        

        /**
         * Head Filters & Init!
         *
         */
		public function init() {
		}
		
		/**
		 * Social Sharing
		 *
		 */
		public function init_social_sharing() {

			// social sharing module
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/social_sharing.php' );
			$ssh = new pspSocialSharing( $this->the_plugin );
		}
		
		
		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		if ( $this->the_plugin->capabilities_user_has_module('Social_Stats') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Social Stats', 'psp'),
		            __('Social Stats', 'psp'),
		            'read',
		           	$this->the_plugin->alias . "_Social_Stats",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}
		
		public function socialstats_scripts( $socialServices=array() )
		{
			if( count($socialServices) > 220 ){
				foreach ($socialServices as $key => $value){
					if( $value == 'twitter' ){
						echo '<script type="text/javascript" src="http://platform.twitter.com/widgets.js?' . ( time() ) . '"></script>';
					}
					elseif( $value == 'google' ){
						echo '<script type="text/javascript" src="http://apis.google.com/js/plusone.js?' . ( time() ) . '"></script>';
					}
					elseif( $value == 'digg' ){
					?>
						<script type="text/javascript">
							(function() {
							  var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
							  s.type = 'text/javascript';
							  s.async = true;
							  s.src = 'http://widgets.digg.com/buttons.js';
							  s1.parentNode.insertBefore(s, s1);
							})();
						</script>
					<?php
					}
					elseif( $value == 'linkedin' ){
						echo '<script type="text/javascript" src="http://platform.linkedin.com/in.js?' . ( time() ) . '"></script>';
					}

					elseif( $value == 'stumbleupon' ){
					?>
						<script type="text/javascript">
						  (function() {
						    var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
						    li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
						    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
						  })();
						</script>

					<?php
					}
				}
			}
			?>

		<?php
		}

		public function display_meta_box()
		{
			$this->printBoxInterface();
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}

		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			$socialServices = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_social', true ); 
			
			if( isset($socialServices['services']) ) {
				$socialServices = $socialServices['services'];
			}

			//if( count($socialServices) > 0 ) $this->socialstats_scripts($socialServices);
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('off_page_optimization|Social_Stats')->show_menu();
			?>
			
			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Content -->
			<div id="psp-content">
				
				<h1 class="psp-section-headline">
					<?php echo $this->module['Social_Stats']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['Social_Stats']['description'];?></span>
					<?php
					$has_help = isset($this->module['Social_Stats']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['Social_Stats']['help']['type']) && $this->module['Social_Stats']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['Social_Stats']['help']['url'] ) . '">HELP</a>';
						} 
					} 
					?>
				</h1>

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix" style="padding-top: 20px;">

						<!-- Content Area -->
						<div id="psp-content-area">
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<?php _e('Social Stats of your pages', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											$columns = array(
												'id'		=> array(
													'th'	=> __('ID', 'psp'),
													'td'	=> '%ID%',
													'width' => '40'
												),
	
												'title'		=> array(
													'th'	=> __('Title', 'psp'),
													'td'	=> '%title%',
													'align' => 'left'
												)
											);
											
											if( count($socialServices) > 0 ){
												foreach ($socialServices as $key => $value){
													if( $value == 'facebook' ){
														$columns['ss_facebook'] = array(
															'th'	=> __('Facebook', 'psp'),
															'td'	=> '%ss_facebook%',
															'width' => '80'
														);
													}
													
													if( $value == 'twitter' ){
														$columns['ss_twitter'] = array(
															'th'	=> __('Twitter', 'psp'),
															'td'	=> '%ss_twitter%',
															'width' => '80'
														);
													}
													
													if( $value == 'google' ){
														$columns['ss_google'] = array(
															'th'	=> __('Google +1', 'psp'),
															'td'	=> '%ss_google%',
															'width' => '80'
														);
													}
													
													if( $value == 'pinterest' ){
														$columns['ss_pinterest'] = array(
															'th'	=> __('Pinterest', 'psp'),
															'td'	=> '%ss_pinterest%',
															'width' => '80'
														);
													}
													
													if( $value == 'stumbleupon' ){
														$columns['ss_stumbleupon'] = array(
															'th'	=> __('Stumbleupon', 'psp'),
															'td'	=> '%ss_stumbleupon%',
															'width' => '80'
														);
													}
													
													if( $value == 'digg' ){
														$columns['ss_digg'] = array(
															'th'	=> __('Digg', 'psp'),
															'td'	=> '%ss_digg%',
															'width' => '80'
														);
													}
													
													if( $value == 'linkedin' ){
														$columns['ss_linkedin'] = array(
															'th'	=> __('Linkedin', 'psp'),
															'td'	=> '%ss_linkedin%',
															'width' => '80'
														);
													}
												}
											}
											
											$columns['date'] = array(
												'th'	=> __('Date', 'psp'),
												'td'	=> '%date%',
												'width' => '120'
											);
											
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspSocialStats',
													'show_header' 		=> true,
													'show_footer' 		=> false,
													'items_per_page' 	=> '10',
													'post_statuses' 	=> 'all',
													'columns'			=> $columns,
													'mass_actions'		=> false
												))
												->print_html();
								            ?>
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}
		
		/**
	    * Singleton pattern
	    *
	    * @return pspSocialStats Singleton instance
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

// Initialize the pspSocialStats class
//$pspSocialStats = new pspSocialStats();
$pspSocialStats = pspSocialStats::getInstance();