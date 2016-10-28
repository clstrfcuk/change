<?php

require_once('load.wp.php'); // load wordpress
require_once('load.plugin.php'); // load plugin!

/*
* Define class aafShortcodes
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('aafShortcodesPopup') != true) {
    class aafShortcodesPopup
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		static protected $_instance;
		
		protected $module_folder = '';
		protected $module_folder_path = '';


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	$this->the_plugin = $psp;

			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'aa-framework/shortcodes/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'aa-framework/shortcodes/';
			
			$this->buildHtmlPage();
		}
		
		private function buildHtmlPage() {
			
			$req = array(
				'module'	=> isset($_GET['module']) ? trim($_GET['module']) : '',
				'shortcode'	=> isset($_GET['shortcode']) ? trim($_GET['shortcode']) : '',
			);
			
			$tryed_module = $this->the_plugin->cfg['modules'][ "{$req['module']}" ];
			if( isset($tryed_module) && count($tryed_module) > 0 ) {

				// Turn on output buffering
				ob_start();

				// shortcodes options
				$opt_file_path = $tryed_module['folder_path'] . 'options-shortcodes.php';
				if( is_file($opt_file_path) ) {
					require( $opt_file_path  );
				}
				$options = ob_get_clean(); //copy current buffer contents into variable and delete current output buffer

				if(trim($options) != "") {
					$options = json_decode($options, true);
				}
				
				if ( is_array($options) && !empty($options) > 0 ) {
					foreach ($options as $option){
						foreach ($option as $box_id => $box){
							if ( $box_id == $req['shortcode'] ) {

								$shortcode = $box['shortcode'];
								$exclude_empty = isset($box['exclude_empty_fields']) && ( $box['exclude_empty_fields'] || $box['exclude_empty_fields'] == 'true' ) ? 'yes' : 'no';
								$options = array( array( "$box_id" => $box ) );
								break 2;
							}
						}
					}
				}
			}
			//sh_title, sh_code
			
			$html = '';
			if ( !empty($shortcode) && is_array($options) && !empty($options) > 0 ) {
			
				// load the settings template class
				require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
				
				// Initalize the your aaInterfaceTemplates
				$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_plugin->cfg);
				
				// then build the html, and return it as string
				$html = $aaInterfaceTemplates->bildThePage( $options , $this->the_plugin->alias, array(), true);
				
				// fix some URI
				$html = str_replace('{plugin_folder_uri}', $tryed_module['folder_uri'], $html);
			}

			?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head></head>
			<body>
			
			<script type="text/javascript" src="<?php echo $this->module_folder;?>js/tinymce.popup.js" ></script>
			<link rel='stylesheet' href='<?php echo $this->module_folder;?>css/tinymce.popup.css' type='text/css' media='all' />
			<div class="psp-form psp-shortcode-pop-up">
			
				<?php 
				if ( !empty($html) ) {
					echo $html;
				?>
				
					<!-- buttons for each box -->
					<div class="psp-button-row">
			
						<?php echo '<input type="reset" value="' . __('Reset to default value', $psp->localizationName) . '" class="psp-button gray left" id="aff-reset-shortcode" />'; ?>
						<?php echo '<input type="submit" value="' . __('Insert shortcode', $psp->localizationName) . '" class="psp-button green psp-saveOptions" id="aaf-insert-shortcode" />'; ?>
			
					</div>
					
					<div id="aafShortcodeFormat" style="display:none;"><?php echo $shortcode; ?></div>
					<div id="aafShortcodeField" style="display:none;"></div>
					<div id="aafShortcodeAtts" style="display:none;" data-exclude_empty="<?php echo $exclude_empty; ?>">
					</div>
				
				<?php
				} else {

					echo '<div>' . __('Error occured!', $this->the_plugin->localizationName) . '</div>';
				}
				?>

			</div>

			</body>
			</html>
			<?php
		}


        /**
	    * Singleton pattern
	    *
	    * @return Singleton instance
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

// Initialize the aafShortcodesPopup class
$aafShortcodesPopup = new aafShortcodesPopup();