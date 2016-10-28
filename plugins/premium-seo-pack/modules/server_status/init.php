<?php
/*
* Define class pspServerStatus
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('pspServerStatus') != true) {
    class pspServerStatus
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
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/server_status/';
			$this->module = $this->the_plugin->cfg['modules']['server_status'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/server_status/ajax.php' );
			new pspServerStatusAjax( $this->the_plugin );
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspServerStatus Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
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
    		add_submenu_page(
    			$this->the_plugin->alias,
    			$this->the_plugin->alias . " " . __('Check System status', 'psp'),
	            __('System Status', 'psp'),
	            'manage_options',
	            $this->the_plugin->alias . "_server_status",
	            array($this, 'display_index_page')
	        );

			return $this;
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
			global $wpdb;
			
			// Google Analytics
			if ( $this->the_plugin->verify_module_status( 'Google_Analytics' ) ) { //module is active
    			$analytics_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_google_analytics' );
    			$analytics_mandatoryFields = array(
    				'client_id'			=> false,
    				'client_secret'		=> false,
    				'redirect_uri'		=> false
    			);
        
    			// get the module init file
    			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Google_Analytics/init.php' );
    			// Initialize the pspGoogleAnalytics class
    			$pspGoogleAnalytics = new pspGoogleAnalytics();
			} // end Google Analytics

			
			// Google SERP
			if ( $this->the_plugin->verify_module_status( 'serp' ) ) { //module is active
    			$serp_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_serp' );
    			$serp_mandatoryFields = array(
    				'developer_key'			=> false,
    				'custom_search_id'		=> false,
    				'google_country'		=> false
    			);
    			
    			// get the module init file
    			// require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/serp/init.php' );
    			// Initialize the pspSERP class
    			// $pspSERP = new pspSERP($this->cfg, ( isset($module) ? $module : array()) );
			} // end Google SERP

			
			// Google Pagespeed
			if ( $this->the_plugin->verify_module_status( 'google_pagespeed' ) ) { //module is active
    			$pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_pagespeed' );
    			$pagespeed_mandatoryFields = array(
    				'developer_key'			=> false,
    				'google_language'		=> false
    			);
    			
    			// get the module init file
    			// require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/google_pagespeed/ajax.php' );
    			// Initialize the pspPageSpeedInsightsAjax class
    			// $pspPagespeed = new pspPageSpeedInsightsAjax($this->the_plugin);
			} // end Google Pagespeed
			
			
			// Facebook
			if ( $this->the_plugin->verify_module_status( 'facebook_planner' ) ) { //module is active
    			$facebook_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_facebook_planner' );
    			$facebook_mandatoryFields = array(
    				'app_id'			=> false,
    				'app_secret'		=> false,
    				'language'			=> false,
    				'redirect_uri'		=> false
    			);
    			
    			// get the module init file
    			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/init.php' );
    			// Initialize the pspFacebook_Planner class
    			$pspFacebook_Planner = new pspFacebook_Planner();
			} // end Facebook
			

            // Tiny Compress
            if ( $this->the_plugin->verify_module_status( 'tiny_compress' ) ) { //module is active
                $tinycompress_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_tiny_compress' );
                $tinycompress_mandatoryFields = array(
                    'tiny_key'         => false,
                    'image_sizes'      => false,
                );
                
                // get the module init file
                require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/tiny_compress/init.php' );
                // Initialize the pspTinyCompress class
                $pspTinyCompress = new pspTinyCompress();
            } // end Tiny Compress

			$plugin_data = get_plugin_data( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'plugin.php' );  
?>
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('general|server_status')->show_menu();
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
					<?php echo $this->module['server_status']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['server_status']['description'];?></span>
					<?php
					$has_help = isset($this->module['server_status']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['server_status']['help']['type']) && $this->module['server_status']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['server_status']['help']['url'] ) . '">HELP</a>';
						} 
					} 
					?>
				</h1>
				
				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix" style="padding-top: 5px;">

						<!-- Content Area -->
						<div id="psp-content-area">
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
									<div class="psp-panel-content">
										<table class="psp-table" cellspacing="0">
											
											<thead>
												<tr>
													<th colspan="2"><?php _e( 'Modules', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
									         	<tr>
									         		<td><?php _e( 'Active Modules','psp' ); ?>:</td>
									         		<td><div class="psp-loading-ajax-details" data-action="active_modules"></div></td>
									         	</tr>
											</tbody>
											
<?php
// Google Analytics module
if ( $this->the_plugin->verify_module_status( 'Google_Analytics' ) ) { //module is inactive
?>
				
											<thead>
												<tr>
													<th colspan="2"><a id="sect-google_analytics" name="sect-google_analytics"></a><?php _e( 'Module Google Analytics:', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
												<tr>
									                <td width="190"><?php _e( 'Your client id','psp' ); ?>:</td>
									                <td>
<?php
if ( isset($analytics_settings['client_id']) && !empty($analytics_settings['client_id']) ) {
	$analytics_mandatoryFields['client_id'] = true;
	echo $analytics_settings['client_id'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
													</td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Your client secret','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($analytics_settings['client_secret']) && !empty($analytics_settings['client_secret']) ) {
	$analytics_mandatoryFields['client_secret'] = true;
	echo $analytics_settings['client_secret'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Redirect URI','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($analytics_settings['redirect_uri']) && !empty($analytics_settings['redirect_uri']) ) {
	$analytics_mandatoryFields['redirect_uri'] = true;
	echo $analytics_settings['redirect_uri'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Profile ID','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($analytics_settings['profile_id']) && !empty($analytics_settings['profile_id']) ) {
	echo $analytics_settings['profile_id'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Authorize','psp' ); ?>:</td>
									                <td>
<div class="psp-begin-test-container noheight">
<?php
$mandatoryValid = true;
foreach ($analytics_mandatoryFields as $k=>$v) {
	if ( !$v ) {
		$mandatoryValid = false;
		break;
	}
}
if ( $mandatoryValid ) {
	if ( $pspGoogleAnalytics->makeoAuthLogin() ) {
?>
	<div class="psp-message psp-success">
		<a href="#google-analytics/authorize" class="psp-button blue pspStressTest inline psp-google-authorize-app" data-saveform="no">Re-Authorize app</a>
	&nbsp;(<?php _e( 'app is authorized','psp' ); ?>)
	</div>
<?php
	} else {
?>
	<div class="psp-message psp-info">
		<a href="#google-analytics/authorize" class="psp-button blue pspStressTest inline psp-google-authorize-app" data-saveform="no">Authorize app</a>
		<span style="margin-left: 10px;">(<?php _e( 'app is not authorized yet','psp' ); ?>)</span>
	</div>
<?php
	}
} else {
?>
	<div class="psp-message psp-error">
		<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can authorize the app!','psp' ); ?>
	</div>
<?php
}
?>
</div>
									                </td>
									            </tr>
															
									            <tr>
									            	<td style="vertical-align: middle;">Verify:</td>
									                <td>
														<div class="psp-verify-products-test">
															<div class="psp-test-timeline">
																<div class="psp-one_step stepid-step1 nbsteps4">
																	<div class="psp-step-status psp-loading-inprogress"></div>
																	<span class="psp-step-name">Step 1</span>
																</div>
																<div class="psp-one_step stepid-step2 nbsteps4">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 2</span>
																</div>
																<div class="psp-one_step stepid-step3 nbsteps4">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 3</span>
																</div>
																<div class="psp-one_step stepid-step4 nbsteps4">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 4</span>
																</div>
																<div style="clear:both;"></div>
															</div>
															<table class="psp-table psp-logs" cellspacing="0">
																<tr class="logbox-step1">
																	<td width="50">Step 1:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Set mandatory fields: client id, client secret, redirect uri', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step2">
																	<td width="50">Step 2:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Authorize app on Google APIs Console:', 'psp' ); ?>
																			<a target="_blank" href="https://code.google.com/apis/console/">https://code.google.com/apis/console/</a>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step3">
																	<td width="50">Step 3:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Get profile ID', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step4">
																	<td width="50">Step 4:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Make a test request from Google Analytics', 'psp' ); ?>
																			<?php
$today = date( 'Y-m-d' );
$from_date 	= date( 'Y-m-d', strtotime( "-1 week", strtotime( $today ) ) );
$to_date 	= date( 'Y-m-d', strtotime( $today ) );
echo " (from $from_date to $to_date)";
																			?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
															</table>
															<div class="psp-begin-test-container">
																<a href="#google-analytics/verify" class="psp-button blue pspStressTest verify" data-module="google_analytics">Verify</a>
															</div>
														</div>
													</td>
									            </tr>
											</tbody>
<?php
} // end Google Analytics module!
?>

<?php
// Google SERP module
if ( $this->the_plugin->verify_module_status( 'serp' ) ) { //module is inactive
?>
				
											<thead>
												<tr>
													<th colspan="2"><a id="sect-google_serp" name="sect-google_serp"></a><?php _e( 'Module Google SERP:', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
												<tr>
									                <td width="190"><?php _e( 'Google Developer Key','psp' ); ?>:</td>
									                <td>
<?php
if ( isset($serp_settings['developer_key']) && !empty($serp_settings['developer_key']) ) {
	$serp_mandatoryFields['developer_key'] = true;
	echo $serp_settings['developer_key'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
													</td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Custom Search Engine ID','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($serp_settings['custom_search_id']) && !empty($serp_settings['custom_search_id']) ) {
	$serp_mandatoryFields['custom_search_id'] = true;
	echo $serp_settings['custom_search_id'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Google location','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($serp_settings['google_country']) && !empty($serp_settings['google_country']) ) {
	$serp_mandatoryFields['google_country'] = true;
	echo 'google.'.$serp_settings['google_country'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Status','psp' ); ?>:</td>
									                <td>
<div class="psp-begin-test-container noheight">
<?php
$mandatoryValid = true;
foreach ($serp_mandatoryFields as $k=>$v) {
	if ( !$v ) {
		$mandatoryValid = false;
		break;
	}
}
if ( $mandatoryValid ) {
?>
	<div class="psp-message psp-success">
		<?php _e( 'all mandatory module settings are set!','psp' ); ?>
	</div>
<?php
} else {
?>
	<div class="psp-message psp-error">
		<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a serp request!','psp' ); ?>
	</div>
<?php
}
?>
</div>
									                </td>
									            </tr>
															
									            <tr>
									            	<td style="vertical-align: middle;">Verify:</td>
									                <td>
														<div class="psp-verify-products-test">
															<div class="psp-test-timeline">
																<div class="psp-one_step stepid-step1 nbsteps2">
																	<div class="psp-step-status psp-loading-inprogress"></div>
																	<span class="psp-step-name">Step 1</span>
																</div>
																<div class="psp-one_step stepid-step2 nbsteps2">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 2</span>
																</div>
																<div style="clear:both;"></div>
															</div>
															<table class="psp-table psp-logs" cellspacing="0">
																<tr class="logbox-step1">
																	<td width="50">Step 1:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Set mandatory fields: google developer key, custom search engine id, google location', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step2">
																	<td width="50">Step 2:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Make a test request from Google SERP', 'psp' ); ?>
																			<?php
$serp_keyword 	= 'test';
$serp_link		= 'www.test.com';
echo " (keyword: $serp_keyword , url: $serp_link)";
																			?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
															</table>
															<div class="psp-begin-test-container">
																<a href="#google-serp/verify" class="psp-button blue pspStressTest verify" data-module="serp">Verify</a>
															</div>
														</div>
													</td>
									            </tr>
											</tbody>
<?php
} // end Google SERP module!
?>

<?php
// Google Pagespeed module
if ( $this->the_plugin->verify_module_status( 'google_pagespeed' ) ) { //module is inactive
?>
				
											<thead>
												<tr>
													<th colspan="2">											<a id="sect-google_pagespeed" name="sect-google_pagespeed"></a><?php _e( 'Module Google Pagespeed:', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
												<tr>
									                <td width="190"><?php _e( 'Google Developer Key','psp' ); ?>:</td>
									                <td>
<?php
if ( isset($pagespeed_settings['developer_key']) && !empty($pagespeed_settings['developer_key']) ) {
	$pagespeed_mandatoryFields['developer_key'] = true;
	echo $pagespeed_settings['developer_key'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#google_pagespeed"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
													</td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Google language','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($pagespeed_settings['google_language']) && !empty($pagespeed_settings['google_language']) ) {
	$pagespeed_mandatoryFields['google_language'] = true;
	echo $pagespeed_settings['google_language'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#google_pagespeed"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Status','psp' ); ?>:</td>
									                <td>
<div class="psp-begin-test-container noheight">
<?php
$mandatoryValid = true;
foreach ($pagespeed_mandatoryFields as $k=>$v) {
	if ( !$v ) {
		$mandatoryValid = false;
		break;
	}
}
if ( $mandatoryValid ) {
?>
	<div class="psp-message psp-success">
		<?php _e( 'all mandatory module settings are set!', 'psp' ); ?>
	</div>
<?php
} else {
?>
	<div class="psp-message psp-error">
		<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a serp request!','psp' ); ?>
	</div>
<?php
}
?>
</div>
									                </td>
									            </tr>
															
									            <tr>
									            	<td style="vertical-align: middle;">Verify:</td>
									                <td>
														<div class="psp-verify-products-test">
															<div class="psp-test-timeline">
																<div class="psp-one_step stepid-step1 nbsteps2">
																	<div class="psp-step-status psp-loading-inprogress"></div>
																	<span class="psp-step-name">Step 1</span>
																</div>
																<div class="psp-one_step stepid-step2 nbsteps2">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 2</span>
																</div>
																<div style="clear:both;"></div>
															</div>
															<table class="psp-table psp-logs" cellspacing="0">
																<tr class="logbox-step1">
																	<td width="50">Step 1:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Set mandatory fields: google developer key, google language', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step2">
																	<td width="50">Step 2:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Make a test request from Google Pagespeed', 'psp' ); ?>
																			<?php
$serp_link		= 'www.test.com';
echo " (url: $serp_link)";
																			?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
															</table>
															<div class="psp-begin-test-container">
																<a href="#google-pagespeed/verify" class="psp-button blue pspStressTest verify" data-module="pagespeed">Verify</a>
															</div>
														</div>
													</td>
									            </tr>
											</tbody>
<?php
} // end Google Pagespeed module!
?>

<?php
// Facebook Planner module
if ( $this->the_plugin->verify_module_status( 'facebook_planner' ) ) { //module is inactive
?>
				
											<thead>
												<tr>
													<th colspan="2"><a id="sect-facebook_planner" name="sect-facebook_planner"></a><?php _e( 'Module Facebook:', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
												<tr>
									                <td width="190"><?php _e( 'Facebook App ID','psp' ); ?>:</td>
									                <td>
<?php
if ( isset($facebook_settings['app_id']) && !empty($facebook_settings['app_id']) ) {
	$facebook_mandatoryFields['app_id'] = true;
	echo $facebook_settings['app_id'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
													</td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Facebook App Secret','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($facebook_settings['app_secret']) && !empty($facebook_settings['app_secret']) ) {
	$facebook_mandatoryFields['app_secret'] = true;
	echo $facebook_settings['app_secret'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Redirect URI','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($facebook_settings['redirect_uri']) && !empty($facebook_settings['redirect_uri']) ) {
	$facebook_mandatoryFields['redirect_uri'] = true;
	echo $facebook_settings['redirect_uri'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Facebook Language','psp' ); ?>:</td>
									                <td>
<?php 
if ( isset($facebook_settings['language']) && !empty($facebook_settings['language']) ) {
	$facebook_mandatoryFields['language'] = true;
	echo $facebook_settings['language'];
} else {
?>
<div class="psp-begin-test-container">
	<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
	}
?>
									                </td>
									            </tr>
												<tr>
									                <td width="190"><?php _e( 'Authorize','psp' ); ?>:</td>
									                <td>
<div class="psp-begin-test-container noheight">
<?php
$mandatoryValid = true;
foreach ($facebook_mandatoryFields as $k=>$v) {
	if ( !$v ) {
		$mandatoryValid = false;
		break;
	}
}
if ( $mandatoryValid ) {
	if ( $pspFacebook_Planner->makeoAuthLogin() ) {
?>
	<div class="psp-message psp-success">
		<a href="#facebook-planner/authorize" class="psp-button blue pspStressTest inline psp-facebook-authorize-app" data-saveform="no">Re-Authorize app</a>
	&nbsp;(<?php _e( 'app is authorized for: ','psp' ); ?><a target="_blank" href="<?php echo $facebook_settings['auth_foruser_link']; ?>"><?php echo $facebook_settings['auth_foruser_name']; ?></a>)
	</div>
<?php
	} else {
?>
	<div class="psp-message psp-info">
		<a href="#facebook-planner/authorize" class="psp-button blue pspStressTest inline psp-facebook-authorize-app" data-saveform="no">Authorize app</a>
		<span style="margin-left: 10px;">(<?php _e( 'app is not authorized yet','psp' ); ?>)</span>
	</div>
<?php
	}
} else {
?>
	<div class="psp-message psp-error">
		<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can authorize the app!','psp' ); ?>
	</div>
<?php
}
?>
</div>
									                </td>
									            </tr>
															
									            <tr>
									            	<td style="vertical-align: middle;">Verify:</td>
									                <td>
														<div class="psp-verify-products-test">
															<div class="psp-test-timeline">
																<div class="psp-one_step stepid-step1 nbsteps2">
																	<div class="psp-step-status psp-loading-inprogress"></div>
																	<span class="psp-step-name">Step 1</span>
																</div>
																<div class="psp-one_step stepid-step2 nbsteps2">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 2</span>
																</div>
<?php
/*
?>
																<!--<div class="psp-one_step stepid-step3 nbsteps3">
																	<div class="psp-step-status"></div>
																	<span class="psp-step-name">Step 3</span>
																</div>-->
<?php
*/
?>
																<div style="clear:both;"></div>
															</div>
															<table class="psp-table psp-logs" cellspacing="0">
																<tr class="logbox-step1">
																	<td width="50">Step 1:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Set mandatory fields: app id, app secret, redirect uri, language', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
																<tr class="logbox-step2">
																	<td width="50">Step 2:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Authorize app on Facebook Developers:', 'psp' ); ?>
																			<a target="_blank" href="http://developers.facebook.com/">http://developers.facebook.com/</a>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>
<?php
/*
?>
																<!--<tr class="logbox-step3">
																	<td width="50">Step 3:</td>
																	<td>
																		<div class="psp-log-title">
																			<?php _e( 'Get profile ID', 'psp' ); ?>
																			<a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
																		</div>
																		
																		<textarea class="psp-log-details"></textarea>
																	</td>
																</tr>-->
<?php
*/
?>
															</table>
															<div class="psp-begin-test-container">
																<a href="#facebook-planner/verify" class="psp-button blue pspStressTest verify" data-module="facebook_planner">Verify</a>
															</div>
														</div>
													</td>
									            </tr>
											</tbody>
<?php
} // end Facebook Planner module!
?>

<?php
// Tiny Compress module
if ( $this->the_plugin->verify_module_status( 'tiny_compress' ) ) { //module is inactive
?>
                
                                            <thead>
                                                <tr>
                                                    <th colspan="2">                                            <a id="sect-tiny_compress" name="sect-tiny_compress"></a><?php _e( 'Module Tiny Compress:', 'psp' ); ?></th>
                                                </tr>
                                            </thead>
                                    
                                            <tbody>
                                                <tr>
                                                    <td width="190"><?php _e( 'Tiny Compress API Key','psp' ); ?>:</td>
                                                    <td>
<?php
if ( isset($tinycompress_settings['tiny_key']) && !empty($tinycompress_settings['tiny_key']) ) {
    $tinycompress_mandatoryFields['tiny_key'] = true;
    echo $tinycompress_settings['tiny_key'];
} else {
?>
<div class="psp-begin-test-container">
    <a href="<?php echo admin_url("admin.php?page=psp#tiny_compress"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
    }
?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="190"><?php _e( 'Selected Image Sizes','psp' ); ?>:</td>
                                                    <td>
<?php 
if ( isset($tinycompress_settings['image_sizes']) && !empty($tinycompress_settings['image_sizes']) ) {
    $tinycompress_mandatoryFields['image_sizes'] = true;
    echo implode(', ', (array) $tinycompress_settings['image_sizes']);
} else {
?>
<div class="psp-begin-test-container">
    <a href="<?php echo admin_url("admin.php?page=psp#tiny_compress"); ?>" class="psp-button blue pspStressTest">Update module settings</a>
</div>
<?php
    }
?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="190"><?php _e( 'Status','psp' ); ?>:</td>
                                                    <td>
<div class="psp-begin-test-container noheight">
<?php
$mandatoryValid = true;
foreach ($tinycompress_mandatoryFields as $k=>$v) {
    if ( !$v ) {
        $mandatoryValid = false;
        break;
    }
}
if ( $mandatoryValid ) {
?>
    <div class="psp-message psp-success">
        <?php _e( 'all mandatory module settings are set!', 'psp' ); ?>
    </div>
<?php
} else {
?>
    <div class="psp-message psp-error">
        <?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a tiny compress request!','psp' ); ?>
    </div>
<?php
}
?>
</div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?php _e('Monthly limit','psp' ); ?>:</td>
                                                    <td>
                                                        <?php
                                                            $compress_limits = $pspTinyCompress->get_compress_limits();
                                                        ?>
                                                        <div class="psp-message psp-<?php echo $compress_limits['status'] == 'valid' ? 'success' : 'error'; ?>">
                                                            <p><?php echo $compress_limits['msg']; ?></p>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="vertical-align: middle;">Verify connection:</td>
                                                    <td>
                                                        <div class="psp-verify-products-test">
                                                            <div class="psp-test-timeline">
                                                                <div class="psp-one_step stepid-step1 nbsteps2">
                                                                    <div class="psp-step-status psp-loading-inprogress"></div>
                                                                    <span class="psp-step-name">Step 1</span>
                                                                </div>
                                                                <div class="psp-one_step stepid-step2 nbsteps2">
                                                                    <div class="psp-step-status"></div>
                                                                    <span class="psp-step-name">Step 2</span>
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            </div>
                                                            <table class="psp-table psp-logs" cellspacing="0">
                                                                <tr class="logbox-step1">
                                                                    <td width="50">Step 1:</td>
                                                                    <td>
                                                                        <div class="psp-log-title">
                                                                            <?php _e( 'Set mandatory fields: tiny api key, image sizes', 'psp' ); ?>
                                                                            <a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
                                                                        </div>
                                                                        
                                                                        <textarea class="psp-log-details"></textarea>
                                                                    </td>
                                                                </tr>
                                                                <tr class="logbox-step2">
                                                                    <td width="50">Step 2:</td>
                                                                    <td>
                                                                        <div class="psp-log-title">
                                                                            <?php _e( 'Connection status to TinyPNG.com API', 'psp' ); //Make a test request from Tiny API ?>
                                                                            <a href="#" class="psp-button gray"><?php _e( 'View details +', 'psp' ); ?></a>
                                                                        </div>
                                                                        
                                                                        <textarea class="psp-log-details"></textarea>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <div class="psp-begin-test-container">
                                                                <a href="#tiny-compress/verify" class="psp-button blue pspStressTest verify" data-module="tinycompress">Verify</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
<?php
} // end Tiny Compress module!
?>

                                            <?php /*<thead>
                                                <tr>
                                                    <th colspan="2"><?php _e( 'Module Media Smushit:', 'psp' ); ?></th>
                                                </tr>
                                            </thead>
                                    
                                            <tbody>
                                                <tr>
                                                    <td><?php _e('Smushit.com Connection Status','psp' ); ?>:</td>
                                                    <td><div class="psp-loading-ajax-details" data-action="smushit_remote_get"></div></td>
                                                </tr>
                                            </tbody>*/ ?>
											
											<thead>
												<tr>
													<th colspan="2"><?php _e( 'Environment', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
												<tr>
									                <td width="190"><?php _e( 'Home URL','psp' ); ?>:</td>
									                <td><?php echo home_url(); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'psp Version','psp' ); ?>:</td>
									                <td><?php echo $plugin_data['Version'];?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'WP Version','psp' ); ?>:</td>
									                <td><?php if ( is_multisite() ) echo 'WPMU'; else echo 'WP'; ?> <?php bloginfo('version'); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'Web Server Info','psp' ); ?>:</td>
									                <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] );  ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'PHP Version','psp' ); ?>:</td>
									                <td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'MySQL Version','psp' ); ?>:</td>
									                <td><?php if ( function_exists( 'mysql_get_server_info' ) ) echo esc_html( mysql_get_server_info() ); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'WP Memory Limit','psp' ); ?>:</td>
									                <td><div class="psp-loading-ajax-details" data-action="check_memory_limit"></div></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'WP Debug Mode','psp' ); ?>:</td>
									                <td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo __( 'Yes', 'psp' ); else echo __( 'No', 'psp' ); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e( 'WP Max Upload Size','psp' ); ?>:</td>
									                <td><?php echo size_format( wp_max_upload_size() ); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e('PHP Post Max Size','psp' ); ?>:</td>
									                <td><?php if ( function_exists( 'ini_get' ) ) echo size_format( $this->woocommerce_let_to_num( ini_get('post_max_size') ) ); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e('PHP Time Limit','psp' ); ?>:</td>
									                <td><?php if ( function_exists( 'ini_get' ) ) echo ini_get('max_execution_time'); ?></td>
									            </tr>
									            <tr>
									                <td><?php _e('WP Remote GET','psp' ); ?>:</td>
									                <td><div class="psp-loading-ajax-details" data-action="remote_get"></div></td>
									            </tr>
									            <tr>
									                <td><?php _e('SOAP Client','psp' ); ?>:</td>
									                <td><div class="psp-loading-ajax-details" data-action="check_soap"></div></td>
									            </tr>
											</tbody>
									
											<thead>
												<tr>
													<th colspan="2"><?php _e( 'Plugins', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
									         	<tr>
									         		<td><?php _e( 'Installed Plugins','psp' ); ?>:</td>
									         		<td><div class="psp-loading-ajax-details" data-action="active_plugins"></div></td>
									         	</tr>
											</tbody>
									
											<thead>
												<tr>
													<th colspan="2"><?php _e( 'Settings', 'psp' ); ?></th>
												</tr>
											</thead>
									
											<tbody>
									
									            <tr>
									                <td><?php _e( 'Force SSL','psp' ); ?>:</td>
													<td><?php echo get_option( 'woocommerce_force_ssl_checkout' ) === 'yes' ? __( 'Yes', 'psp' ) : __( 'No', 'psp' ); ?></td>
									            </tr>
											</tbody>
											
											
											<!--tfoot>
												<tr>
													<th colspan="2">
														<a href="#" class="psp-button blue psp-export-logs">Export status log as file</a>
													</th>
												</tr>
											</tfoot-->
										</table>
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

		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requesto to 404 table
		*/
		public function ajax_request()
		{
			global $wpdb;
			$request = array(
				'id' 			=> isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0
			);
			
			$asin = get_post_meta($request['id'], '_amzASIN', true);
			
			$sync = new wwcAmazonSyncronize( $this->the_plugin );
			$sync->updateTheProduct( $asin, $request['id'] );
		}
		
		
		/**
		 * UTILS
		 */
		function woocommerce_let_to_num($size) {
			if ( function_exists('woocomerce_let_to_num') )
				return function_exists('woocomerce_let_to_num');

			$l = substr($size, -1);
			$ret = substr($size, 0, -1);
			switch( strtoupper( $l ) ) {
				case 'P' :
					$ret *= 1024;
				case 'T' :
					$ret *= 1024;
				case 'G' :
					$ret *= 1024;
				case 'M' :
					$ret *= 1024;
				case 'K' :
					$ret *= 1024;
			}
			return $ret;
		}
	}
}
// Initialize the pspServerStatus class
//$pspServerStatus = new pspServerStatus();
$pspServerStatus = pspServerStatus::getInstance();