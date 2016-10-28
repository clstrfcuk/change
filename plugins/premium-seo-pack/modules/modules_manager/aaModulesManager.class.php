<?php
/*

* Define class Modules Manager List

* Make sure you skip down to the end of this file, as there are a few

* lines of code that are very important.

*/
!defined('ABSPATH') and exit;
if (class_exists('aaModulesManger') != true) {
	class aaModulesManger
	{
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*
		*/
		public $cfg = array();

		/*
		* Store some helpers config
		*/
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';

		private $settings = array();

		static protected $_instance;
		
		/**
	    * Singleton pattern
	    *
	    * @return pspLinkRedirect Singleton instance
	    */
		static public function getInstance()
		{
			if (!self::$_instance) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct() //public function __construct($cfg)
		{
			global $psp;

			$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/modules_manager/';
			$this->module = $this->the_plugin->cfg['modules']['modules_manager'];

			$this->settings = $this->the_plugin->getAllSettings( 'array', 'modules_manager' );
			
			$this->cfg = $this->the_plugin->cfg; //$this->cfg = $cfg;
		}
		
		public function printListInterface()
		{
			$html   = array();
			
			$html[] = '
			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text">' . __('Loading', 'psp') . '</div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>
			';

			$html[] = '<script type="text/javascript" src="' . $this->module_folder . 'app.class.js" ></script>';
			//$html[] = '<link rel="stylesheet" href="' . $this->module_folder . 'app.css" type="text/css" media="all" />';

			$html[] = '<table class="psp-table" id="' . ($this->cfg['default']['alias']) . '-module-manager" style="border-collapse: collapse;border-spacing: 0;">';
			$html[] = '<thead>
						<tr>
							<th width="10"><input type="checkbox" id="psp-item-check-all" checked></th>
							<th width="10">' . __('Icon', 'psp') . '</th>
							<th width="10">' . __('Version', 'psp') . '</th>
							<th width="350" align="left">' . __('Name', 'psp') . '</th>
							<th align="left">' . __('About', 'psp') . '</th>
						</tr>
					</thead>';
			$html[] = '<tbody>';
			$cc     = 0;
			foreach ($this->cfg['modules'] as $key => $value) {
				$module = $key;
				if ( !in_array($module, $this->cfg['core-modules'])
				&& !$this->the_plugin->capabilities_user_has_module($module) ) {
					continue 1;
				}
				
				$icon = '';
				if (is_file($value["folder_path"] . $value[$key]['menu']['icon'])) {
					$icon = $value["folder_uri"] . $value[$key]['menu']['icon'];
				}
				$html[] = '<tr class="' . ($cc % 2 ? 'odd' : 'even') . '">
                	<td align="center">';
				// activate / deactivate plugin button
				if ($value['status'] == true) {
					if (!in_array($key, $this->cfg['core-modules'])) {
						$html[] = '<input type="checkbox" class="psp-item-checkbox" name="psp-item-checkbox-' . ( $key ) . '" checked>';
					} else {
						$html[] = ""; // core module
					}
				} else {
					$html[] = '<input type="checkbox" class="psp-item-checkbox" name="psp-item-checkbox-' . ( $key ) . '">';
				}
				$html[] = '</td>
					<td align="center">' . (trim($icon) != "" ? '<img src="' . ($icon) . '" width="16" height="16" />' : '') . '</td>
					<td align="center">' . ($value[$key]['version']) . '</td>
					<td>';
				// activate / deactivate plugin button
				if ($value['status'] == true) {
					if (!in_array($key, $this->cfg['core-modules'])) {
						$html[] = '<a href="#deactivate" class="deactivate" rel="' . ($key) . '">Deactivate</a>';
					} else {
						$html[] = "<i>" . __("Core Modules, can't be deactivated!", 'psp') . "</i>";
					}
				} else {
					$html[] = '<a href="#activate" class="activate" rel="' . ($key) . '">' . __('Activate', 'psp') . '</a>';
				}
				$html[] = "&nbsp; | &nbsp;" . $value[$key]['menu']['title'];
				$html[] = '</td>
					<td>' . (isset($value[$key]['description']) ? $value[$key]['description'] : '') . '</td>
				</tr>';
				$cc++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';

			$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px; padding-bottom: 5px;">&nbsp;';
			$html[] = 	'<input type="button" value="' . __('Activate selected modules', 'psp') . '" id="psp-activate-selected" class="psp-button blue">';
			$html[] = 	'<input type="button" value="' . __('Deactivate selected modules', 'psp') . '" id="psp-deactivate-selected" class="psp-button blue">';
			$html[] = '</div>';

			return implode("\n", $html);
		}
	}
}
// Initalize the your aaModulesManger
//$aaModulesManger = new aaModulesManger($this->cfg, $module);
//$aaModulesManger = new aaModulesManger($this->cfg);