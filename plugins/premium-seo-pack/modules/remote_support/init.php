<?php
/*
* Define class pspRemoteSupport
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('pspRemoteSupport') != true) {
    class pspRemoteSupport
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
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/remote_support/';
			$this->module = $this->the_plugin->cfg['modules']['remote_support'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/remote_support/ajax.php' );
			new pspRemoteSupportAjax( $this->the_plugin );
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspRemoteSupport Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('remote_support') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('AA-Team Remote Support', 'psp'),
		            __('Remote Support', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_remote_support",
		            array($this, 'display_index_page')
		        );
    		}

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
			
			$remote_access = get_option( 'psp_remote_access', true );
			$login_token = get_option( 'psp_support_login_token', true );
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
		
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('general|remote_support')->show_menu();
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
					<?php echo $this->module['remote_support']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['remote_support']['description'];?></span>
					<?php
					$has_help = isset($this->module['remote_support']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['remote_support']['help']['type']) && $this->module['remote_support']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['remote_support']['help']['url'] ) . '">HELP</a>';
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
							
							<div class="psp-grid_4" id="psp-boxid-access">
							    <div class="psp-panel">
							        <div class="psp-panel-header">
							            <span class="psp-panel-title">
											Remote Support Details
										</span>
							        </div>
							        <div class="psp-panel-content">
							            <form id="psp_access_details" class="psp-form">
							                <div class="psp-form-row">
							                    <label for="protocol">Create WP Credential</label>
							                    <div class="psp-form-item large">
							                        <span class="formNote">This will automatically create a wordpress administrator account for AA-Team support team</span>
							                        
							                        <?php 
							                        $selected = 'yes';
													if( 
														!isset($remote_access['psp-create_wp_credential']) ||
														$remote_access['psp-create_wp_credential'] == 'no'
													){
														$selected = 'no';
													}
							                        ?>
							                        <select id="psp-create_wp_credential" name="psp-create_wp_credential" style="width:80px;">
							                            <option value="yes" <?php echo ($selected == 'yes' ? 'selected="selected"' : '');?>>Yes</option>
							                            <option value="no" <?php echo ($selected == 'no' ? 'selected="selected"' : '');?>>NO</option>
							                        </select>
							                        
							                        <div class="psp-wp-credential" <?php echo ( isset($remote_access['psp-create_wp_credential']) && trim($remote_access['psp-create_wp_credential']) == 'yes' ? 'style="display:block"' : 'style="display:none"' );?>>
							                        	<table class="psp-table" style="border-collapse: collapse;">
							                        		<tr>
							                        			<td width="160">Admin username:</td>
							                        			<td>aateam_support</td>
							                        		</tr>
							                        		<tr>
							                        			<td>Admin password:</td>
							                        			<td>
								                        			<?php  
									                        			$admin_password = isset($remote_access['psp-password']) ? $remote_access['psp-password'] : $this->generateRandomString(10);
								                        			?>
								                        			<input type="text" name="psp-password" id="psp-password" value="<?php echo $admin_password;?>" />
							                        			</td>
							                        		</tr>
							                        	</table>
							                        	<div class="psp-message psp-info"><i>(this details will be send automatically on your open ticket)</i></div>
							                        </div>
							                    </div>
							                </div>
							                <div class="psp-form-row">
							                    <label for="onsite_cart">Allow file remote access</label>
							                    <div class="psp-form-item large">
							                        <span class="formNote">This will automatically give access for AA-Team support team to your chosen server path</span>
							                        
							                        <?php 
							                        $selected = 'yes';
													if( 
														!isset($remote_access['psp-allow_file_remote']) ||
														$remote_access['psp-allow_file_remote'] == 'no'
													){
														$selected = 'no';
													}
							                        ?>
							                        <select id="psp-allow_file_remote" name="psp-allow_file_remote" style="width:80px;">
							                            <option value="yes" <?php echo ($selected == 'yes' ? 'selected="selected"' : '');?>>Yes</option>
							                            <option value="no" <?php echo ($selected == 'no' ? 'selected="selected"' : '');?>>NO</option>
							                        </select>
							                        
							                        <div class="psp-file-access-credential" <?php echo ( isset($remote_access['psp-allow_file_remote']) && trim($remote_access['psp-allow_file_remote']) == 'yes' ? 'style="display:block"' : 'style="display:none"' );?>>
							                        	<table class="psp-table" style="border-collapse: collapse;">
							                        		<tr>
							                        			<td width="120">Access key:</td>
							                        			<td>
							                        				<?php 
									                        			$access_key = isset($remote_access['psp-key']) ? $remote_access['psp-key'] : md5( $this->generateRandomString(12) );
								                        			?>
							                        				<input type="text" name="psp-key" id="psp-key" value="<?php echo $access_key;?>" />
							                        			</td>
							                        		</tr>
							                        		<tr>
							                        			<td width="120">Access path:</td>
							                        			<td>
							                        				<input type="text" name="psp-access_path" id="psp-access_path" value="<?php echo isset($remote_access['psp-access_path']) ? $remote_access['psp-access_path'] : ABSPATH;?>" />
							                        			</td>
							                        		</tr>
							                        	</table>
							                        	<div class="psp-message psp-info"><i>(this details will be send automatically on your open ticket)</i> </div>
							                        </div>
							                    </div>
							                </div>
							                <div style="display:none;" id="psp-status-box" class="psp-message"></div>
							                <div class="psp-button-row">
							                    <input type="submit" class="psp-button blue" value="Save Remote Access" style="float: left;" />
							                </div>
							            </form>
							        </div>
							    </div>
							</div>
							
							<div class="psp-grid_4" id="psp-boxid-logininfo">
	                        	<div class="psp-panel">
									<div class="psp-panel-content">
										<div class="psp-message psp-info">
											
											<?php
											if( !isset($login_token) || trim($login_token) == "" ){
											?>
												In order to contact AA-Team support team you need to login into support.aa-team.com
											<?php 
											}
											
											else{
											?>
												Test your token is still valid on AA-Team support website ...
												<script>
													pspRemoteSupport.checkAuth( '<?php echo $login_token;?>' );
												</script>
											<?php
											}
											?>
										</div>
				            		</div>
								</div>
							</div>
							
							<div class="psp-grid_2" id="psp-boxid-login" style="display:none">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											Login
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="psp-form-login">
											<div class="psp-form-row">
												<label class="psp-form-label" for="email">Email <span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="text" id="psp-email" name="psp-email" class="span12">
												</div>
											</div>
											<div class="psp-form-row">
												<label class="psp-form-label" for="password">Password <span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="password" id="psp-password" name="psp-password" class="span12">
												</div>
											</div>
											
											<div class="psp-form-row" style="height: 79px;">
												<input type="checkbox" id="psp-remember" name="psp-remember" style="float: left; position: relative; bottom: -12px;">
												<label for="psp-remember" class="psp-form-label" style="width: 120px;">&nbsp;Remember me</label>
											</div>
											
											<div class="psp-message psp-error" style="display:none;"></div>
	
											<div class="psp-button-row">
												<input type="submit" class="psp-button blue" value="Login" style="float: left;" />
											</div>
										</form>
				            		</div>
								</div>
							</div>
							
							<div class="psp-grid_2" id="psp-boxid-register" style="display:none">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											Register
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="psp-form-register">
											<div class="psp-message error" style="display:none;"></div>
											
											<div class="psp-form-row">
												<label class="psp-form-label">Your name <span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="text" id="psp-name-register" name="psp-name-register" class="span12">
												</div>
											</div>
											
											<div class="psp-form-row">
												<label class="psp-form-label">Your email <span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="text" id="psp-email-register" name="psp-email-register" class="span12">
												</div>
											</div>
											
											<div class="psp-form-row">
												<label class="psp-form-label">Create a password <span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="password" id="psp-password-register" name="psp-password-register" class="span6">
												</div>
											</div>
											
											<div class="psp-button-row">
												<input type="submit" class="psp-button blue" value="Register and login" style="float: left;" />
											</div>
										</form>
				            		</div>
								</div>
							</div>
							
							<div class="psp-grid_4" style="display: none;" id="psp-boxid-ticket">
							    <div class="psp-panel">
							        <div class="psp-panel-header">
							            <span class="psp-panel-title">
											Details about problem:
										</span>
							        </div>
							        <div class="psp-panel-content">
							            <form id="psp_add_ticket" class="psp-form">
							            	<input type="hidden" name="psp-token" id="psp-token" value="<?php echo $login_token;?>" />
							            	<input type="hidden" name="psp-site_url" id="psp-site_url" value="<?php echo admin_url();?>" />
							            	<input type="hidden" name="psp-wp_username" id="psp-wp_username" value="aateam_support" />
							            	<input type="hidden" name="psp-wp_password" id="psp-wp_password" value="" />
							            	
							            	<input type="hidden" name="psp-access_key" id="psp-access_key" value="" />
							            	<input type="hidden" name="psp-access_url" id="psp-access_url" value="<?php echo urlencode( str_replace("http://", "", $this->module_folder) . 'remote_tunnel.php');?>" />
							            	
							                
							                <div class="psp-form-row">
												<label class="psp-form-label">Ticket Subject<span class="required">*</span></label>
												<div class="psp-form-item large">
													<input type="text" id="ticket_subject" name="ticket_subject" class="span6">
												</div>
											</div>
											
							                <div class="psp-form-row">
						                        <?php
												wp_editor( 
													'', 
													'ticket_details', 
													array( 
														'media_buttons' => false,
														'textarea_rows' => 40,	
													) 
												); 
						                        ?>
							                </div>
							                <div style="display:none;" id="psp-status-box" class="psp-message psp-success"></div>
							                <div class="psp-button-row">
							                    <input type="submit" class="psp-button green" value="Open ticket on support.aa-team.com" style="float: left;" />
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

		private function generateRandomString($length = 6) 
		{
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%^*()';
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, strlen($characters) - 1)];
		    }
		    return $randomString;
		}
    }
}

// Initialize the pspRemoteSupport class
//$pspRemoteSupport = new pspRemoteSupport();
$pspRemoteSupport = pspRemoteSupport::getInstance();