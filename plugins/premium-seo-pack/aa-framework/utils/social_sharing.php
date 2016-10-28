<?php
/*
* Define class pspSocialSharing
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSocialSharing') != true) {
	class pspSocialSharing
	{
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*/
		public $the_plugin = null;
		private $plugin_settings = array();
		
		protected $module_folder = '';
		protected $module_folder_path = '';

		static protected $_instance;

		private $socialNetworks = array();
		private $toolbarTypes = array();
		private $pageTypes = array();
		private $shareInfo;
		
		private static $isTest = false;


		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct( $parent )
		{
			$this->the_plugin = $parent;
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_socialsharing' );
			
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Social_Stats/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Social_Stats/';

			$this->socialNetworks();
			
			$this->init();
		}
		
		/**
		 * Frontend load
		 *
		 */
		public function init() {
			$isEnabled = $this->is_toolbar_enabled();

			// at least 1 toolbar is enabled!
			if ( !is_admin() && $isEnabled['isEnabled'] ) {
				
				add_action( "wp_enqueue_scripts", array($this, 'the_styles') );
				add_action( "wp_enqueue_scripts", array($this, 'the_scripts') );
				
				add_action( "wp_head", array($this, 'the_header') );
				add_action( "wp_footer", array($this, 'the_footer') );
			}
		}
		
		public function the_styles() {
			if( !wp_style_is('psp_socialshare_css') ) {
				wp_enqueue_style( 'psp_socialshare_css' , $this->module_folder . '/social_sharing.css' );
			}
		}
		public function the_scripts() {
			if( !wp_script_is('jquery') ) { // first, check to see if it is already loaded
				wp_enqueue_script( 'jquery' , 'https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js' );
			}
			if( !wp_script_is('psp_socialshare_js') ) {
				wp_enqueue_script( 'psp_socialshare_js' , $this->module_folder . '/social_sharing.js', array(
					'jquery'
				) );
				wp_localize_script( 'psp_socialshare_js', 'pspSocialSharing_ajaxurl', admin_url('admin-ajax.php') );
			}
		}
		public function the_header() {
			$isEnabled = $this->is_toolbar_enabled();

			// the content toolbars inserted in the post content
			if ( $isEnabled['isContent'] ) {
				add_filter( 'the_content', array($this, 'update_the_content'), 15 );
			}
			return ;
		}
		public function the_footer() {
			$isEnabled = $this->is_toolbar_enabled();

			// the floating toolbar inserted in wp footer
			if ( $isEnabled['isFloating'] && $this->is_page_allowed('floating') ) {
				$theToolbar = $this->getToolbar('floating');
				if ( !empty($theToolbar) ) echo $theToolbar;
			}
			
			// build html with 3 toolbars options which will be read and executed in javascript file!
			echo $this->setToolbarsOptions();
			
			echo $this->setToolbarsBackground();
			return ;
		}
		public function update_the_content($content) {
			$isEnabled = $this->is_toolbar_enabled();

			// horizontal content toolbar
			if ( $isEnabled['content_horizontal'] && $this->is_page_allowed('content_horizontal') ) {
				$content = $this->getToolbar('content_horizontal', $content);
			}
			
			// vertical content toolbar - ( after horizontal toolbar - so the top markes is set right!)
			if ( $isEnabled['content_vertical'] && $this->is_page_allowed('content_vertical') ) {
				$content = $this->getToolbar('content_vertical', $content);
			}
			
			return $content;
		}

		/**
	    	* Singleton pattern
	    	*
	   	* @return pspFileEdit Singleton instance
	   	*/
		static public function getInstance()
		{
			if (!self::$_instance) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/**
		* Social Sharing
		*/
		public function socialNetworks() {
			
			$this->toolbarTypes = array(
				'none'				=> __('None', $this->the_plugin->localizationName),
				'floating'			=> __('Floating Toolbar', $this->the_plugin->localizationName),
				'content_horizontal'		=> __('Content Top / Bottom Toolbar', $this->the_plugin->localizationName),
				'content_vertical'		=> __('Content Left / Right Toolbar', $this->the_plugin->localizationName)
			);
			
			$this->pageTypes = array(
				'home' 		=> __('Homepage', $this->the_plugin->localizationName),
				'front_page' 		=> __('Posts Front Page', $this->the_plugin->localizationName),
				'single' 		=> __('Posts', $this->the_plugin->localizationName),
				'page' 			=> __('Pages', $this->the_plugin->localizationName),
				'category' 		=> __('Category Pages', $this->the_plugin->localizationName),
				'tag' 			=> __('Tag Pages', $this->the_plugin->localizationName),
				'archive' 		=> __('Archive Pages', $this->the_plugin->localizationName)
			);

			$this->socialNetworks = array(
				'print' 			=> array('title' => __('Print', $this->the_plugin->localizationName)),
				'email' 			=> array('title' => __('Email', $this->the_plugin->localizationName)),
				// 'more' 			=> array('title' => __('More', $this->the_plugin->localizationName)),
				'facebook' 		=> array('title' => __('Facebook', $this->the_plugin->localizationName)),
				'twitter' 		=> array('title' => __('Twitter', $this->the_plugin->localizationName)),
				'plusone' 		=> array('title' => __('Plusone', $this->the_plugin->localizationName)),
				'linkedin' 		=> array('title' => __('Linkedin', $this->the_plugin->localizationName)),
				'stumbleupon' 	=> array('title' => __('Stumble Upon', $this->the_plugin->localizationName)),
				// 'digg' 			=> array('title' => __('Digg', $this->the_plugin->localizationName)),
				'delicious' 		=> array('title' => __('Delicious', $this->the_plugin->localizationName)),
				'pinterest' 		=> array('title' => __('Pinterest', $this->the_plugin->localizationName)),
				// 'xing' 			=> array('title' => __('Xing', $this->the_plugin->localizationName)),
				'buffer' 		=> array('title' => __('Buffer', $this->the_plugin->localizationName)), // @js errors
				'flattr' 			=> array('title' => __('Flattr', $this->the_plugin->localizationName)),
				// 'tumblr' 		=> array('title' => __('Tumblr', $this->the_plugin->localizationName)),
				'reddit' 		=> array('title' => __('Reddit', $this->the_plugin->localizationName))
			);
		}
		
		
		/**
		 * Admin
		 *
		 */

		public function set_toolbar_options( $defaults=array(), $pms=array() ) {
			if( !is_array($defaults) ) $defaults = array();

			extract($pms);

			$toolbarTitle = $this->toolbarTypes["$toolbar"];

			$options = array(
				array(
					/* define the form_sizes  box */
					'socialsharing' => array(
						'title' 		=> $toolbarTitle,
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 		=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							$toolbar.'-enabled' => array(
								'type' 		=> 'select',
								'std' 		=> 'no',
								'size' 		=> 'large',
								'force_width'  => '120',
								'title' 		=> __('Enabled:', $this->the_plugin->localizationName),
								'desc' 		=> 'choose yes if you want to enable this toolbar type',
								'options'	=> array(
									'no'			=> __('No', $this->the_plugin->localizationName),
									'yes'			=> __('Yes', $this->the_plugin->localizationName)
								)
							)

							/*,$toolbar.'-contact' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_contact( $toolbar, $defaults )
							)*/

							,$toolbar.'-design' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_optdesign( $toolbar, $defaults )
							)

							,$toolbar.'-position' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_position( $toolbar.'-position', isset($defaults[$toolbar.'-position']) ? $defaults[$toolbar.'-position'] : array() )
							)

							,$toolbar.'-margin' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_margin( $toolbar.'-margin', isset($defaults[$toolbar.'-margin']) ? $defaults[$toolbar.'-margin'] : array() )
							)

							,$toolbar.'-pages' 	=> array(
								'type' 		=> 'multiselect',
								'std' 		=> array('homepage', 'post'),
								'size' 		=> 'small',
								'force_width'  => '250',
								'title' 		=> __('Toolbar showing areas:', $this->the_plugin->localizationName),
								'desc' 		=> __('areas where you want the social share toolbar to appear', $this->the_plugin->localizationName),
								'options' 	=> $this->pageTypes
							)
							
							,$toolbar.'-exclude-categ' 	=> array(
								'type' 		=> 'multiselect',
								'std' 		=> array(),
								'size' 		=> 'small',
								'force_width'  => '250',
								'title' 		=> __('Exclude toolbar on categories:', $this->the_plugin->localizationName),
								'desc' 		=> __('categories where you don\'t want the social share toolbar to appear (also all posts belonging to these categories will not have the toolbar)', $this->the_plugin->localizationName),
								'options' 	=> $this->tbExcludeCategs_opt()
							)
							
							,$toolbar.'-exclude-post' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_exclude( $toolbar.'-exclude-post', isset($defaults[$toolbar.'-exclude-post']) ? $defaults[$toolbar.'-exclude-post'] : array() )
							)

							,$toolbar.'-opt' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_opt( $toolbar, $defaults )
							)
							
							,$toolbar.'-buttons' => array(
								'type' 		=> 'html',
								'html' 		=> $this->set_toolbar_buttons( $toolbar, $defaults )
							)

						)
					)
				)
			);

			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box) {
						//if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$elm_id];
								}
							}
						//}
					}
				}

				// than update the options for returning
				$options = array( $option );
			}

			return $options;
		}
		
		public function build_toolbar_options($pms=array()) {
			
			extract($pms);
			
			// load the settings template class
			require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_plugin->cfg);
			
			$options = array();
			$options = $this->plugin_settings;
			
			// then build the html, and return it as string
			$html_options = $aaInterfaceTemplates->bildThePage( $this->set_toolbar_options( $options, $pms ) , $this->the_plugin->alias, array(), false);
			return $html_options;
		}
		
		/**
		 * Custom methods
		 */
		private function set_toolbar_position( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
		?>
<div class="psp-form-row">
	<label><?php _e('Position: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($psp_socialsharing_position as $key => $value){

		$__toolbar = str_replace('-position', '', $field_name);
		//if ( $__toolbar == 'content_horizontal' && $key == 'vertical' ) continue 1;

		$val = '0';
		if( isset($db_meta_name[$key]) && isset($db_meta_name[$key]) ) {
			$val =$db_meta_name[$key];
		}
		?>
		<label for="<?php echo $field_name.'['.$key.']'; ?>" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $key));?>:</label>
		&nbsp;
		<select id="<?php echo $field_name.'['.$key.']'; ?>" name="<?php echo $field_name.'['.$key.']'; ?>" style="width:120px;">
			<?php
			foreach ($value as $kk => $vv){

				if ( $__toolbar == 'content_horizontal' && $key == 'vertical' && $kk == 'center' ) continue 1;
				if ( $__toolbar == 'content_vertical' && $key == 'horizontal' && $kk == 'center' ) continue 1;
				echo '<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	} 
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		private function set_toolbar_margin( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
		?>
<div class="psp-form-row">
	<label><?php _e('Margin: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($psp_socialsharing_margin as $key => $value){
		
		$__toolbar = str_replace('-margin', '', $field_name);
		//if ( $__toolbar == 'content_horizontal' && $key == 'vertical' ) continue 1;

		$val = '';
		if( isset($db_meta_name[$key]) && isset($db_meta_name[$key]) ) {
			$val =$db_meta_name[$key];
		}
		?>
		<label for="<?php echo $field_name.'['.$key.']'; ?>" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $key));?>:</label>
		&nbsp;
		<input type='text' class='' id='<?php echo $field_name.'['.$key.']'; ?>' name='<?php echo $field_name.'['.$key.']'; ?>' value='<?php echo $val; ?>' style="width:100px;">&nbsp;<?php _e('px', $this->the_plugin->localizationName); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	} 
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		private function set_toolbar_opt( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
			
			$__optArr = array( 
				'btnsize' 		=> $psp_socialsharing_opt['btnsize'],
				'viewcount' 	=> $psp_socialsharing_opt['viewcount'],
				'withmore'		=> $psp_socialsharing_opt['withmore']
			);
			$__optArrDetails = array(
				'btnsize' 	=> array('title' => __('Buttons size', $this->the_plugin->localizationName)),
				'viewcount' 	=> array('title' => __('View count', $this->the_plugin->localizationName)),
				'withmore'		=> array('title' => __('With More button', $this->the_plugin->localizationName)),
			);
		?>
<div class="psp-form-row">
	<label><?php _e('Buttons options: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($__optArr as $key => $value){
		
		$__theKey = $field_name.'-'.$key;

		$val = '';
		if( isset($db_meta_name[$__theKey]) && isset($db_meta_name[$__theKey]) ) {
			$val =$db_meta_name[$__theKey];
		}
		?>
		<label for="<?php echo $__theKey; ?>" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $__optArrDetails[$key]['title']));?>:</label>
		&nbsp;
		<select id="<?php echo $__theKey; ?>" name="<?php echo $__theKey; ?>" style="width:120px;">
			<?php
			foreach ($value as $kk => $vv){
				echo '<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	} 
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		private function set_toolbar_exclude( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
		?>
<div class="psp-form-row">
	<label><?php _e('Include/Exclude toolbar on Post, Pages: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($psp_socialsharing_exclude as $key => $value){
		
		$__toolbar = str_replace('-exclude-post', '', $field_name);

		$val = '';
		if( isset($db_meta_name[$key]) && isset($db_meta_name[$key]) ) {
			$val =$db_meta_name[$key];
		}
		?>
		<?php /*<label for="<?php echo $field_name.'['.$key.']'; ?>" style="display:inline-block;"><?php echo $value['title'];?>:</label>*/ ?>
		<div class="psp-form-item large" style="display:inline-block; width:49%; margin-left:0;">
			<span class="formNote" style="width: 100%;"><?php echo $value['desc']; ?></span>
			<textarea class='' id='<?php echo $field_name.'['.$key.']'; ?>' name='<?php echo $field_name.'['.$key.']'; ?>' style=""><?php echo $val; ?></textarea>
		</div>
		<?php
	} 
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		private function set_toolbar_optdesign( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
			
			$__optArr = $psp_socialsharing_design;
			
			$__optArrDetails = array(
				'make_floating' 	=> array('title' => __('Make it floating', $this->the_plugin->localizationName)),
				'background_color'	=> array('title' => __('Background color', $this->the_plugin->localizationName)),
				'floating_beyond_content'	=> array('title' => __('Floating beyond the end of the post content', $this->the_plugin->localizationName))
			);
		?>
<div class="psp-form-row">
	<label><?php _e('Design: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($__optArr as $key => $value){
		
		$__theKey = $field_name.'-'.$key;
		
		if ($field_name != 'content_vertical' && in_array($key, array('make_floating', 'floating_beyond_content')) ) continue 1;

		$val = '';
		if( isset($db_meta_name[$__theKey]) && isset($db_meta_name[$__theKey]) ) {
			$val =$db_meta_name[$__theKey];
		}
		
		if ( $key == 'background_color' ) {
		?>
		<label for="<?php echo $__theKey; ?>" style="display:inline;float:none;"><?php echo $__optArrDetails[$key]['title']; ?>:</label>
		&nbsp;
		<input type='text' class='socialshare-color-picker' id='<?php echo $__theKey; ?>' name='<?php echo $__theKey; ?>' value='<?php echo $val; ?>' data-background_color="<?php echo $val; ?>" style="width:100px;">&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
		} else {
		?>
		<label for="<?php echo $__theKey; ?>" style="display:inline;float:none;"><?php echo $__optArrDetails[$key]['title']; ?>:</label>
		&nbsp;
		<select id="<?php echo $__theKey; ?>" name="<?php echo $__theKey; ?>" style="width:120px;">
			<?php
			foreach ($value as $kk => $vv){
				echo '<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
		}
	} 
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		private function set_toolbar_buttons( $field_name, $db_meta_name ) {
			ob_start();
			
			$__theKey = $field_name.'-'.'buttons';
			$selectedBtn = array();
			if ( isset($db_meta_name[$__theKey]) && !empty($db_meta_name[$__theKey]) ) {
				$selectedBtn = explode(',', $db_meta_name[$__theKey]);
			}
			$selectedBtn = (array) $selectedBtn;
			
			$availableBtn = array_keys( $this->socialNetworks );
			$availableBtn = array_diff( $availableBtn, $selectedBtn );
		?>
<div class="psp-form-row">
	<label><?php _e('Toolbar buttons: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>

	<input type="hidden" id="btn-selected-list" name="<?php echo $__theKey; ?>" value="" />
	
	<div class="btn-wrapper">
		<span class="title"><?php _e('Available buttons', $this->the_plugin->localizationName); ?></span>
		<ul class="btn-available btn-sortable">
		<?php
			if ( !empty($availableBtn) ) {
				foreach ( $availableBtn as $k => $v ) {
		?>
				<li class="block social-btn <?php echo $v; ?>" data-btn="<?php echo $v; ?>"><a class="icon"><span class="title"><?php echo $this->socialNetworks["$v"]['title']; ?></span></a><span class="delete"><?php _e('x', $this->the_plugin->localizationName); ?></span></li>
		<?php	
				}
			}
		?>
		</ul>
	</div>
	
	<div class="btn-wrapper">
		<span class="title"><?php _e('Selected buttons', $this->the_plugin->localizationName); ?></span>
		<ul class="btn-selected btn-sortable">
		<?php
			if ( !empty($selectedBtn) ) {
				foreach ( $selectedBtn as $k => $v ) {
		?>
				<li class="block social-btn <?php echo $v; ?>" data-btn="<?php echo $v; ?>"><a class="icon"><span class="title"><?php echo $this->socialNetworks["$v"]['title']; ?></span></a><span class="delete"><?php _e('x', $this->the_plugin->localizationName); ?></span></li>
		<?php	
				}
			}
		?>
		</ul>
	</div>

	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		private function set_toolbar_contact( $field_name, $db_meta_name ) {
			ob_start();

			require($this->module_folder_path . 'lists.inc.php');

			$__optArr = $psp_socialsharing_opt['contact'];
		?>
<div class="psp-form-row">
	<label><?php _e('Info details: ', $this->the_plugin->localizationName); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($__optArr as $key => $value) {
		
		$__theKey = $field_name.'-'.$key;
		
		$val = '';
		if ( isset($value['std']) && !empty($value['std']) ) {
			$val = $value['std'];
		}
		if( isset($db_meta_name[$__theKey]) && isset($db_meta_name[$__theKey]) ) {
			$val =$db_meta_name[$__theKey];
		}
		?>
		<label for="<?php echo $__theKey; ?>" style="display:inline;float:none;"><?php echo $value['title']; ?>:</label>
		&nbsp;
		<input type='text' class='' id='<?php echo $__theKey; ?>' name='<?php echo $__theKey; ?>' value='<?php echo $val; ?>' style="width:100px;">&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	}
	?>
	</div>
</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		
		/**
		 * Frontend methods
		 */
		// toolbar is enabled
		private function is_toolbar_enabled() {
			$opt = $this->plugin_settings;
			
			$ret = array(
				'floating'				=> false,
				'content_horizontal'	=> false,
				'content_vertical'		=> false,
				
				'isFloating'			=> false,
				'isContent'				=> false,
				'isEnabled'				=> false
			);
			
			$isEnabled = false; $isFloating = false; $isContent = false;
			foreach ($this->toolbarTypes as $k=>$v) {
				$status = $this->get_property( $k . '-enabled', 'string' );
				if ( $status=='yes' ) {

					$ret["$k"] = true;
					if ( $k == 'floating' ) $isFloating = true;
					if ( in_array($k, array('content_horizontal', 'content_vertical')) ) $isContent = true;
					$isEnabled = true;
				}
			}
			
			return array_merge($ret, array(
				'isEnabled'			=> $isEnabled,
				'isFloating'		=> $isFloating,
				'isContent'			=> $isContent
			));
		}

		// toolbar is allowed on page type
		private function is_page_allowed( $toolbarType='floating' ) {
			if ( is_admin() || is_feed() ) return false;

			$allowedPages = $this->get_property( $toolbarType . '-pages', 'array' );
			
			// loop through all page types!
			if ( is_home() ) {
				return ( in_array('home', $allowedPages) ? true : false );
			}
			else if ( is_front_page() ) {
				return ( in_array('front_page', $allowedPages) ? true : false );
			}
			else if ( is_single() ) {
				return ( in_array('single', $allowedPages) && !$this->is_exclude_item($toolbarType) ? true : false );
			}
			else if ( is_page() ) {
				return ( in_array('page', $allowedPages) && !$this->is_exclude_item($toolbarType) ? true : false );
			}
			else if ( is_attachment() ) {
				return ( in_array('attachment', $allowedPages) ? true : false );
			}
			else if ( is_category() ) {
				return ( in_array('category', $allowedPages) && !$this->is_exclude_item($toolbarType) ? true : false );
			}
			else if ( is_tag() ) {
				return ( in_array('tag', $allowedPages) ? true : false );
			}
			else if ( is_tax() ) {
				return ( in_array('tax', $allowedPages) ? true : false );
			}
			else if ( is_archive() ) {
				return ( in_array('archive', $allowedPages) ? true : false );
			}
			else if ( is_author() ) {
				return ( in_array('author', $allowedPages) ? true : false );
			}
			else if ( is_search() ) {
				return ( in_array('search', $allowedPages) ? true : false );
			}
			else if ( is_404() ) {
				return ( in_array('404', $allowedPages) ? true : false );
			}
			return false;
		}
		
		// get toolbar
		private function getToolbar($toolbarType, $content=false) {

			global $post;

			$toolbar = $this->buildToolbar($toolbarType, $post);
			if ( empty($toolbar) ) return ($content!==false ? $content : '');

			$ret = $toolbar;
			if ( $toolbarType == 'floating' ) {
				return $ret;
			}
			if ( $content!==false ) {
				// horizontal toolbar - chose position (top - above content or bottom - bellow content)
				if ( $toolbarType == 'content_horizontal' ) {
					$position = $this->get_property( $toolbarType . '-position', 'array' );
					$vertical = isset($position['vertical']) && in_array($position['vertical'], array('top', 'bottom')) ? $position['vertical'] : 'top';

					if ( $vertical == 'top' )
						$ret = $toolbar . $content;
					if ( $vertical == 'bottom' )
						$ret = $content . $toolbar;
				}
				// vertical toolbar - always bellow content - moved by js
				else if ( $toolbarType == 'content_vertical' ) {
					$__mark_top = '<span class="psp-social-content-mark-top"></span>';
					$__mark_bottom = '<span class="psp-social-content-mark-bottom"></span>';
					$ret = ( $__mark_top . $content . $__mark_bottom ) . $toolbar;
				}
			}
			return $ret;
		}
		
		// build toolbar!
		private function buildToolbar($toolbarType, $post=null) {
			$__btnUrl = $this->module_folder;
			
			$post_id = 0;
			if ( !is_null($post) && is_object($post) && isset($post->ID) ) {
				$post_id = $post->ID;
			}
    
			if ( $toolbarType == 'floating' ) {
				global $wp_query;
				$post = $wp_query->get_queried_object();
			}
			
			$this->shareInfo = $this->getPostInfo($post, $toolbarType);

			$cssExtra = array(); $__params = ' data-itemid="' . $post_id . '" data-url="' . $this->shareInfo->url . '" ';
			switch ($toolbarType) {
				case 'floating':
					$__tbType = 'box-floating';
					break;
					
				case 'content_horizontal':
					$__tbType = 'box-panel';
					break;
					
				case 'content_vertical':
					$__tbType = 'box-panel-vertical';
					break;
			}
			
			if ( $this->get_property( $toolbarType . '-viewcount', 'string', 'no') == 'yes' ) $cssExtra[] = 'viewcount';
			if ( $this->get_property( $toolbarType . '-btnsize', 'string', 'normal') == 'large' ) $cssExtra[] = 'large';
			
			if ( self::$isTest ) {
				$buttons = '
					<div class="social-btn"><img src="' . $__btnUrl . 'buttons-test/btn_1.png" width="65" height="23" /></div>
					<div class="social-btn"><img src="' . $__btnUrl . 'buttons-test/btn_2.png" width="59" height="23" /></div>
					<div class="social-btn"><img src="' . $__btnUrl . 'buttons-test/btn_3.png" width="59" height="22" /></div>
					<div class="social-btn"><img src="' . $__btnUrl . 'buttons-test/btn_4.png" width="59" height="22" /></div>
					<div class="social-btn"><img src="' . $__btnUrl . 'buttons-test/btn_5.png" width="59" height="19" /></div>
				';
			} else {
				$buttons = $this->getButtons($toolbarType, $post);
				if ( empty($buttons) ) return '';
				$buttons_list = implode('', $buttons);
			}

			/*$buttonsList = $this->get_property( $toolbarType . '-buttons', 'string' );
			$toolbarPms = array(
				'type'		=> $toolbarType,
				'itemid'	=> $post_id,
				'position' 	=> $this->get_property( $toolbarType . '-position', 'array', array(
					'horizontal' 	=> 'left',
    					'vertical' 	=> 'top'
    				) ),
				'margin' 	=> $this->get_property( $toolbarType . '-margin', 'array', array(
					'horizontal' 	=> 0,
    					'vertical' 	=> 0
    				) ),
    				'viewcount'	=> $this->get_property( $toolbarType . '-viewcount', 'string', 'no'),
    				'btnsize'	=> $this->get_property( $toolbarType . '-btnsize', 'string', 'normal'),
    				'buttons'	=> $buttonsList
			);
			$toolbarPmsJson = json_encode($toolbarPms);*/

			$ret = '
					<!-- start/ Premium SEO pack - Wordpress Plugin / Social Sharing Toolbar -->
					<div class="psp-sshare-wrapper ' . $__tbType . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '" ' . $__params . '>
						<div class="psp-socialbox-content">'
						. $buttons_list
						. '</div>
					</div>'
					/*. '<script type="text/javascript">
					jQuery(document).ready(function() {
					//<![CDATA[
						// pspSocialSharing.setAjaxUrl( "' . admin_url('admin-ajax.php') . '" );
						var pspSocialSharing_pms = ' . $toolbarPmsJson . ';
						pspSocialSharing.build_toolbar( pspSocialSharing_pms );
					//]]>
					});
					</script>'*/
					. '<!-- end/ Premium SEO pack - Wordpress Plugin / Social Sharing Toolbar -->
			';
			return $ret;
		}
		
		private function getButtons($toolbarType, $post=null) {
			$ret = array();
			$buttonsList = $this->get_property( $toolbarType . '-buttons', 'string' );
			$buttonsList = (array) explode(',', $buttonsList);
			if ( empty($buttonsList) ) return $ret;
			
			// social sharing module
			$pms = array(
				'toolbarType'		=> $toolbarType,
				'post'				=> $post
			);
			require_once( 'social_sharing_btn.php' );
			$sharingButtons = new pspSocialSharingButtons( $this->the_plugin, $pms );
			
			$shareInfo = $this->shareInfo;
			
			if ( $toolbarType=='floating' ) $sharingButtons->setPostInfo( null, $shareInfo );
			else $sharingButtons->setPostInfo( $post, $shareInfo );
			
			// more buttons list
			$buttons_more = array_keys( $this->socialNetworks );
			$buttons_more = array_diff( $buttons_more, $buttonsList );
			// has more button
			$withmore = $this->get_property( $toolbarType . '-withmore', 'string' );
			$withmore = ($withmore == 'yes' && count($buttons_more) > 0) ? true : false;
			
			// built more buttons list
			$btnMore = array();
			if ( $withmore ) { foreach ($buttons_more as $k=>$v) {
				$__func = $v . '_btn';
				if ( is_callable(array($sharingButtons, $__func), true) && method_exists($sharingButtons, $__func) ) {
					$btnMore[] = $sharingButtons->$__func();
				}
			} }
  
			$btn = array();
			if ( $withmore && !empty($btnMore) ) $buttonsList[] = 'more';
			foreach ($buttonsList as $k=>$v) {
				$__func = $v . '_btn';
				if ( is_callable(array($sharingButtons, $__func), true) && method_exists($sharingButtons, $__func) ) {
					if ( $v == 'more' )
						$btn[] = $sharingButtons->$__func( $btnMore );
					else
						$btn[] = $sharingButtons->$__func();
				}
			}
			return $btn;
		}
		
		private function setToolbarsOptions() {
			$isEnabled = $this->is_toolbar_enabled();
			
			$tblList = array();
			if ( $isEnabled['floating'] ) $tblList['floating'] = array();
			if ( $isEnabled['content_horizontal'] ) $tblList['content_horizontal'] = array();
			if ( $isEnabled['content_vertical'] ) $tblList['content_vertical'] = array();
			
			if ( !empty($tblList) ) {
				foreach ($tblList as $k=>$v) {

					$toolbarType = $k;
					$buttonsList = $this->get_property( $toolbarType . '-buttons', 'string' );
					$toolbarPms = array(
						'currentToolbar'	=> '',
						'type'			=> $toolbarType,
						'itemid'		=> 0,
						'position' 		=> $this->get_property( $toolbarType . '-position', 'array', array(
							'horizontal' 		=> 'left',
		    				'vertical' 			=> 'top'
		    				) 
						),
						'margin' 		=> $this->get_property( $toolbarType . '-margin', 'array', array(
							'horizontal' 		=> 0,
	    					'vertical' 		=> 0
		    				)
						),
		    			'viewcount'		=> $this->get_property( $toolbarType . '-viewcount', 'string', 'no'),
		    			'btnsize'		=> $this->get_property( $toolbarType . '-btnsize', 'string', 'normal'),
		    			'buttons'		=> $buttonsList
					);
					if ( $toolbarType == 'content_vertical' ) {
						$toolbarPms['make_floating'] = $this->get_property( $toolbarType . '-make_floating', 'string', 'no');
						$toolbarPms['floating_beyond_content'] = $this->get_property( $toolbarType . '-floating_beyond_content', 'string', 'no');
					}
					$toolbarPms['is_admin_bar_showing'] = is_admin_bar_showing() ? 'yes' : 'no';
					$tblList["$toolbarType"] = $toolbarPms;
				}
			}
			$tblList = json_encode($tblList);
			$tblList = htmlentities($tblList);
			
			return '<div id="psp-sshare-toolbars-options" style="display: none;" data-options="' . $tblList . '"></div>';
		}
		
		public function getPostInfo($post, $toolbarType='') {

			$isPremium = false;
			if ( $this->the_plugin->is_plugin_active( 'premium-seo-pack/plugin.php' ) ) {
				$__moduleIsActive = get_option('psp_module_title_meta_format');
				if ( isset($__moduleIsActive) && $__moduleIsActive=='true' )
					$isPremium = true;
			}
		
			if ( !$isPremium ) {
				$urlroot = get_bloginfo('url');

				if ( is_singular() || $toolbarType!='floating' ) {
					$post_id = $post->ID;
					$url = get_permalink($post->ID);
					$title = get_the_title($post->ID);
				}
				else if ( is_category() || is_tag() || is_tax() ) {
					$post_id = $post->term_id;
				}
				if ( is_home() || is_front_page() ) {
					$url = home_url( '/' );
				}
				
				if ( !isset($url) || empty($url) ) {
					$url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
				}
				if ( !isset($title) || empty($title) ) {
					$title = wp_title('', false);
					$title = trim($title);
				}
			
				$shareInfo = (object) array(
					'urlroot'		=> $urlroot,
					'url'			=> $url,
					'title'			=> $title
				);
				return $shareInfo;
			}
 
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/title_meta_format/init.php');
			$info = new pspTitleMetaFormat();

			$info->setPostInfo( $post );
			// $infoFb = new pspSocialTags(); // facebook
			// $infoTw = new pspSocialTwitterCards(); // twitter cards
			
			$shareInfo = (object) array(
				'info'			=> $info,
				'infoFb'		=> isset($infoFb) ? $infoFb : array(),
				'infoTw'		=> isset($infoTw) ? $infoTw : array()
			);

			$urlroot = get_bloginfo('url');
			$url = $shareInfo->info->get_the_url();
			$title = $shareInfo->info->get_the_title();

			if ( $toolbarType=='floating' ) {
				$url = $shareInfo->info->the_url();
				$title = $shareInfo->info->the_title('');
			}

			// $info = $shareInfo->infoFb->opengraph_tags(true);
			// $title = isset($info['og:title']) && !empty($info['og:title']) ? $info['og:title'] : $title;
			// $info = $shareInfo->infoTw->twitter_cards_tags(true);
			// $title = isset($info['twitter:title']) && !empty($info['twitter:title']) ? $info['twitter:title'] : $title;
			
			$shareInfo = (object) array(
				'urlroot'		=> $urlroot,
				'url'			=> $url,
				'title'			=> $title
			);
			return $shareInfo;
		}

		public function setToolbarsBackground() {
			$isEnabled = $this->is_toolbar_enabled();
			
			$tblList = array();
			$toolbars = array(
				'floating' 					=> 'box-floating',
				'content_horizontal'		=> 'box-panel',
				'content_vertical'			=> 'box-panel-vertical'
			);
			foreach ( $toolbars as $toolbarType => $cssValue ) {
				if ( $isEnabled["$toolbarType"] ) {
					$bkcolor = $this->get_property( $toolbarType . '-background_color', 'string' );
					if ( !empty($bkcolor) ) {
						$bkcolor = str_replace('#', '', $bkcolor);
						$tblList["$toolbarType"] = '.psp-sshare-wrapper.' . $cssValue . ' { background-color: #' . $bkcolor . '; }';
					}
				}
			}
			
			if ( !empty($tblList) ) {
				$tblList = implode(PHP_EOL, $tblList);
			
				$tblList = PHP_EOL . "<!-- start/ " . ($this->the_plugin->details['plugin_name']) . "/ Social Sharing -->" . PHP_EOL
				. '<style type="text/css">' . PHP_EOL
				. $tblList
				. PHP_EOL . '</style>'
				. PHP_EOL . "<!-- end/ " . ($this->the_plugin->details['plugin_name']) . "/ Social Sharing -->" . PHP_EOL;
				return $tblList;
			}
			return ''; 
		}

		/**
		 * get COUNT
		 */
		public function getSocialsData( $website_url='', $itemid=0, $force_refresh_cache=false )
		{
			$cache_life_time = 60 * 10; // in seconds
			$the_db_cache = get_post_meta( $itemid, 'psp_socialsharing_count', true);
			
			// check if cache NOT expires 
			if( isset($the_db_cache['_cache_date']) && ( time() <= ( $the_db_cache['_cache_date'] + $cache_life_time ) ) && $force_refresh_cache == false ) {
				$the_db_cache['facebook'] = 0;
				if ( isset($the_db_cache['facebook']['share_count']) )
					$the_db_cache['facebook'] = $the_db_cache['facebook']['share_count'];
				return $the_db_cache;
			}
			
			$db_cache = array();
			$db_cache['_cache_date'] = time();
			
			// Facebook
			$fql  = "SELECT url, normalized_url, share_count, like_count, comment_count, ";
			$fql .= "total_count, commentsbox_count, comments_fbid, click_count FROM ";
			$fql .= "link_stat WHERE url = '{$website_url}'";
			$apiQuery = "https://api.facebook.com/method/fql.query?format=json&query=" . urlencode($fql);
			$fb_data = $this->getRemote( $apiQuery );
			$fb_data = $fb_data[0];
			
			// Twitter
			$apiQuery = "http://urls.api.twitter.com/1/urls/count.json?url=" . $website_url;
			$tw_data = $this->getRemote( $apiQuery );
			
			// LinkedIn
			$apiQuery = "http://www.linkedin.com/countserv/count/share?format=json&url=" . $website_url;
			$ln_data = $this->getRemote( $apiQuery );
			
			// Pinterest
			$apiQuery = "http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . $website_url;
			$pn_data = $this->getRemote( $apiQuery );
			
			// StumbledUpon
			$apiQuery = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=" . $website_url;
			$st_data = $this->getRemote( $apiQuery );
			
			// Delicious
			$apiQuery = "http://feeds.delicious.com/v2/json/urlinfo/data?url=" . $website_url;
			$de_data = $this->getRemote( $apiQuery ); 
			$de_data = $de_data[0];
			
			// Google Plus
			$apiQuery = "https://plusone.google.com/_/+1/fastbutton?bsv&size=tall&hl=it&url=" . $website_url;			
			$go_data = $this->getRemote( $apiQuery, false ); 
			
			if ( isset($go_data) && !empty($go_data) ) {
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
				if ( !empty($this->the_plugin->charset) )
					$html = pspphpQuery::newDocumentHTML( $go_data, $this->the_plugin->charset );
				else
					$html = pspphpQuery::newDocumentHTML( $go_data );
				$go_data = $html->find("#aggregateCount")->text();
			}
			
			// Buffer
			$apiQuery = "https://api.bufferapp.com/1/links/shares.json?url=" . $website_url;
			$buffer_data = $this->getRemote( $apiQuery );
			
			// Reddit
			$apiQuery = "http://www.reddit.com/api/info.json?url=" . $website_url;
			$reddit_data = $this->getRemote( $apiQuery );
			if ( isset($reddit_data['data']['children'][0]['data']) )
				$reddit_data = $reddit_data['data']['children'][0]['data'];
			else $reddit_data = array('score' => 0);
			
			// Flattr
			$apiQuery = "https://api.flattr.com/rest/v2/things/lookup/?url=" . $website_url;
			$flattr_data = $this->getRemote( $apiQuery );
			if ( isset($flattr_data['message']) && $flattr_data['message'] != 'found' ) 
				$flattr_data['flattrs'] = 0;
			
			// Tumblr
			// api: http://www.tumblr.com/docs/en/api/v2#blog-likes
			// @info: needs an api key!
			
			// Digg
			// no valid api found!
			
			// Xing
			// api: https://dev.xing.com/docs
			// @info: needs an api key! - can't find the number of likes/bookmarks!
			
			// store for feature cache
			$db_cache['facebook'] = array(
				'share_count' => isset($fb_data['share_count']) ? $fb_data['share_count'] : 0,
				'like_count' => isset($fb_data['like_count']) ? $fb_data['like_count'] : 0,
				'comment_count' => isset($fb_data['comment_count']) ? $fb_data['comment_count'] : 0,
				'click_count' => isset($fb_data['click_count']) ? $fb_data['click_count'] : 0
			);
			
			$db_cache['twitter'] = isset($tw_data['count']) ? $tw_data['count'] : 0;
			$db_cache['linkedin'] = isset($ln_data['count']) ? $ln_data['count'] : 0;
			$db_cache['pinterest'] = isset($pn_data['count']) ? $pn_data['count'] : 0;
			$db_cache['stumbleupon'] = isset($st_data['result']['views']) ? $st_data['result']['views'] : 0;
			$db_cache['delicious'] = isset($de_data['total_posts']) ? $de_data['total_posts'] : 0;
			$db_cache['plusone'] = isset($go_data) ? $go_data : 0;
			$db_cache['buffer'] = isset($buffer_data['shares']) ? $buffer_data['shares'] : 0;
			$db_cache['reddit'] = isset($reddit_data['score']) ? $reddit_data['score'] : 0;
			$db_cache['flattr'] = isset($flattr_data['flattrs']) ? $flattr_data['flattrs'] : 0;
			
			if ( !empty($db_cache) ) {
				foreach ($db_cache as $k => $v) {
					if ( $k == 'plusone' ) ;
					else if ( $k == 'facebook') {
						foreach ($v as $key => $value) {
							$db_cache["$k"]["$key"] = (int) $value;							
						}
					} else {
						$db_cache["$k"] = (int) $v;
					}
				}
			}
			
			// create a DB cache of this
			update_post_meta( $itemid, 'psp_socialsharing_count', $db_cache );
			
			$db_cache['facebook'] = $db_cache['facebook']['share_count'];
			return $db_cache; 
		}

		private function getRemote( $the_url, $parse_as_json=true )
		{ 
			$response = wp_remote_get( $the_url, array('user-agent' => "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0", 'timeout' => 10) ); 
			// If there's error
            if ( is_wp_error( $response ) ){
            	return array(
					'status' => 'invalid'
				);
            }
        	$body = wp_remote_retrieve_body( $response );
			
			if( $parse_as_json == true ){
				// trick for pinterest
				if( preg_match('/receiveCount/i', $body)){
					$body = str_replace("receiveCount(", "", $body);
					$body = str_replace(")", "", $body);
				}
				
	        	return json_decode( $body, true );
			}
			
			return $body;
		}
		
		
		/**
		 * Toolbar exclude
		 */
		public function tbExcludeCategs_opt() {
			$args = array(
				'orderby' => 'name',
				'parent' => 0
			);
			$categories = get_categories( $args );
			if ( empty($categories) || !is_array($categories)) return array();
			
			$ret = array();
			foreach ( $categories as $category ) {
				$key = $category->term_id;
				$value = $category->name;
				$ret["$key"] = $value;
			}
			return $ret;
		}
		
		public function is_exclude_item( $toolbarType ) {
			
			$__excludePost = $this->get_property( $toolbarType . '-exclude-post', 'array' );
			$exclude = array(
				'categ'			=> $this->get_property( $toolbarType . '-exclude-categ', 'array' ),
				'post_include'	=> isset($__excludePost['include']) && !empty($__excludePost['include']) ? array_map('trim', explode(',', $__excludePost['include'])) : array(),
				'post_exclude'	=> isset($__excludePost['exclude']) && !empty($__excludePost['exclude']) ? array_map('trim', explode(',', $__excludePost['exclude'])) : array()
			);
    
			if ( is_category() ) {

				$categ = get_category(get_query_var('cat'),false);
				$categ_id = $categ->term_id;
				if ( in_array($categ_id, $exclude['categ']) ) return true;

			} else if ( is_single() || is_page() ) {

				global $post;
				$post_id = $post->ID;
  
				// verify post in posts IDs list
				if ( !empty($exclude['post_include']) ) {
					if ( !in_array($post_id, $exclude['post_include']) ) return true;
					return false;
				}
				if ( !empty($exclude['post_exclude']) ) {
					if ( in_array($post_id, $exclude['post_exclude']) ) return true;
				}

				// verify post in category
				$categories = get_the_category($post_id);
				if ( $categories ){
					foreach ($categories as $category) {
						// if ( $category->name == 'uncategorized' || $category->slug == 'uncategorized' ) continue 1;
						if ( in_array($category->term_id, $exclude['categ']) ) return true;
					}
				}
			}
			return false;
		}


		/**
		 * UTILS
		 */
		private function get_property( $key, $type='string', $default='' ) {
			$opt = $this->plugin_settings;
			switch ($type) {
				case 'string' :
					$prop = isset($opt["$key"]) ? $opt["$key"] : ( !empty($default) ? $default : '' );
					break;
					
				case 'array' :
					$prop = isset($opt["$key"]) && is_array($opt["$key"]) ? $opt["$key"] : ( !empty($default) ? $default : array() );
					break;
			}
			return $prop;
		}
		
		public function formatCount( $value ) {
			if ( is_string($value) ) return $value;

			$ret = (int) $value;
			$len = strlen( (string) $value);
			if ( $len >= 5 && $len <= 6 ) {
				$ret = '+' . floor( $value / 1000 ) . 'k';
			} else if ( $len >=7 && $len <= 9 ) {
				$ret = '+' . floor( $value / 1000000 ) . 'm';
			}
			return $ret;
		}
	}
}

// Initialize the pspSocialSharing class
//$pspSocialSharing = new pspSocialSharing();
