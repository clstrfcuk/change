<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		pspAdminMenu
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('pspAdminMenu') != true) {
	class pspAdminMenu {
		
		/*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $the_menu = array();
		private $current_menu = '';
		private $ln = '';
		
		private $menu_depedencies = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
            global $psp;
        	$this->the_plugin = $psp;
			$this->ln = $this->the_plugin->localizationName;
			
			// update the menu tree
			$this->the_menu_tree();
			
			return $this;
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
		
		private function the_menu_tree()
		{
			if ( isset($this->the_plugin->cfg['modules']['depedencies']['folder_uri'])
				&& !empty($this->the_plugin->cfg['modules']['depedencies']['folder_uri']) ) {
				$this->menu_depedencies['depedencies'] = array( 
					'title' => __( 'Plugin depedencies', 'psp' ),
					'url' => admin_url("admin.php?page=psp"),
					'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
					'menu_icon' => 'images/16_dashboard.png'
				);
				
				$this->clean_menu();			
				$this->capabilities();
				return true;
			}

			$this->the_menu['dashboard'] = array( 
				'title' => __( 'Dashboard', 'psp' ),
				'url' => admin_url("admin.php?page=psp#dashboard"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_dashboard.png'
			);
			
			$this->the_menu['monitoring'] = array( 
				'title' => __( 'Monitorize', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_monitorize.png',
				'submenu' => array(
					'Google_Analytics' => array(
						'title' => __( 'Google Analytics', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Google_Analytics"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Google_Analytics']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
						'submenu' => array(
							'Google_Analytics_View' => array(
								'title' => __( 'Google Analytics', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Google_Analytics")
							),
							'Google_Analytics_Settings' => array(
								'title' => __( 'Google Analytics Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Google_Analytics")
							),
						)
					),
					
					'serp' => array(
						'title' => __( 'SERP Tracking', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_SERP"),
						'folder_uri' => $this->the_plugin->cfg['modules']['serp']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
						'submenu' => array(
							'SERP_View' => array(
								'title' => __( 'SERP Tracking', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_SERP")
							),
							'SERP_Settings' => array(
								'title' => __( 'SERP Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#serp")
							),
						)
					),
					
					'google_pagespeed' => array(
						'title' => __( 'PageSpeed Insights', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_PageSpeedInsights"),
						'folder_uri' => $this->the_plugin->cfg['modules']['google_pagespeed']['folder_uri'],
						'menu_icon' => 'assets/16_pagespeed.png',
						'submenu' => array(
							'google_pagespeed_View' => array(
								'title' => __( 'PageSpeed Insights', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_PageSpeedInsights")
							),
							'google_pagespeed_Settings' => array(
								'title' => __( 'PageSpeed Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#google_pagespeed")
							),
						)
					),
					
					'monitor_404' => array(
						'title' => __( '404 Monitor', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_mass404Monitor"),
						'folder_uri' => $this->the_plugin->cfg['modules']['monitor_404']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
					),
				)
			);
			
			$this->the_menu['on_page_optimization'] = array( 
				'title' => __( 'ON Page Optimization', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_onpg.png',
				'submenu' => array(
					/*'on_page_optimization' => array(
						'title' => __( 'Mass Optimization', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massOptimization"),
						'folder_uri' => $this->the_plugin->cfg['modules']['on_page_optimization']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),*/
					
					'on_page_optimization' => array(
						'title' => __( 'Mass Optimization', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massOptimization"),
						'folder_uri' => $this->the_plugin->cfg['modules']['on_page_optimization']['folder_uri'],
						'menu_icon' => 'assets/16_onpageoptimization.png',
						'submenu' => array(
							'on_page_optimization_View' => array(
								'title' => __( 'Mass Optimization', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massOptimization")
							),
							'on_page_optimization_Settings' => array(
								'title' => __( 'Mass Optimization Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#on_page_optimization")
							),
						)
					),
					
					'title_meta_format' => array(
						'title' => __( 'Title & Meta Format', 'psp' ),
						'url' => admin_url("admin.php?page=psp#title_meta_format"),
						'folder_uri' => $this->the_plugin->cfg['modules']['title_meta_format']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'sitemap' => array(
						'title' => __( 'Sitemap', 'psp' ),
						'url' => admin_url("admin.php?page=psp#sitemap"),
						'folder_uri' => $this->the_plugin->cfg['modules']['sitemap']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'misc' => array(
						'title' => __( 'SEO Slug Optimizer', 'psp' ),
						'url' => admin_url("admin.php?page=psp#misc"),
						'folder_uri' => $this->the_plugin->cfg['modules']['misc']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'seo_friendly_images' => array(
						'title' => __( 'SEO Friendly Images', 'psp' ),
						'url' => admin_url("admin.php?page=psp#seo_friendly_images"),
						'folder_uri' => $this->the_plugin->cfg['modules']['seo_friendly_images']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'local_seo' => array(
						'title' => __( 'Local SEO', 'psp' ),
						'url' => admin_url("admin.php?page=psp#local_seo"),
						'folder_uri' => $this->the_plugin->cfg['modules']['local_seo']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'google_authorship' => array(
						'title' => __( 'Google Authorship', 'psp' ),
						'url' => admin_url("admin.php?page=psp#google_authorship"),
						'folder_uri' => $this->the_plugin->cfg['modules']['google_authorship']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					)
				)
			);
			
			$this->the_menu['off_page_optimization'] = array( 
				'title' => __( 'OFF Page Optimization', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_offpg.png',
				'submenu' => array(
					'Backlink_Builder' => array(
						'title' => __( 'Backlink Builder', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Backlink_Builder"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Backlink_Builder']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'Social_Stats' => array(
						'title' => __( 'Social Stats', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Social_Stats"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Social_Stats']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
						'submenu' => array(
							'Social_Stats_View' => array(
								'title' => __( 'Social Stats', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Social_Stats")
							),
							'Google_Analytics_Settings' => array(
								'title' => __( 'Social Stats Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Social_Stats")
							),
						)
					),
					
					'Link_Builder' => array(
						'title' => __( 'Link Builder', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Link_Builder"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Link_Builder']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'Link_Redirect' => array(
						'title' => __( '301 Link Redirect', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Link_Redirect"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Link_Redirect']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
				)
			);
			
			$this->the_menu['advanced_setup'] = array( 
				'title' => __( 'Advanced Setup', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_tools.png',
				'submenu' => array(
					'file_edit' => array(
						'title' => __( 'Files Edit', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massFileEdit"),
						'folder_uri' => $this->the_plugin->cfg['modules']['file_edit']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),

					'W3C_HTMLValidator' => array(
						'title' => __( 'W3C Validator', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_HTMLValidator"),
						'folder_uri' => $this->the_plugin->cfg['modules']['W3C_HTMLValidator']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),

					'misc--insert-code' => array(
						'title' => __( 'SEO Insert Code', 'psp' ),
						'url' => admin_url("admin.php?page=psp#misc#insert-code"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_code.png'
					),

					/*'rich_snippets' => array(
						'title' => __( 'Rich Snippets', 'psp' ),
						'url' => admin_url("admin.php?page=psp#rich_snippets"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'assets/menu_icon.png'
					),

					'facebook_planner' => array(
						'title' => __( 'Facebook Planner', 'psp' ),
						'url' => admin_url("admin.php?page=psp#facebook_planner"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_code.png'
					),*/
					
					'facebook_planner' => array(
						'title' => __( 'Facebook Planner', 'psp' ),
						'url' => admin_url("admin.php?page=psp#facebook_planner"),
						'folder_uri' => $this->the_plugin->cfg['modules']['facebook_planner']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
						'submenu' => array(
							'Smushit_View' => array(
								'title' => __( 'FB Planner', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_facebook_planner")
							),
							'Smushit_Settings' => array(
								'title' => __( 'FB Planner Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#facebook_planner")
							),
						)
					),

					/*'smushit' => array(
						'title' => __( 'Media Smushit', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_smushit"),
						'folder_uri' => $this->the_plugin->cfg['modules']['smushit']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png',
						'submenu' => array(
							'Smushit_View' => array(
								'title' => __( 'Media Smushit', 'psp' ),
								'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_smushit")
							),
							'Smushit_Settings' => array(
								'title' => __( 'Smushit Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#smushit")
							),
						)
					),*/
					
                    'tiny_compress' => array(
                        'title' => __( 'Tiny Compress', 'psp' ),
                        'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_tiny_compress"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['tiny_compress']['folder_uri'],
                        'menu_icon' => 'assets/menu_icon.png',
                        'submenu' => array(
                            'Tiny_Compress_View' => array(
                                'title' => __( 'Tiny Compress', 'psp' ),
                                'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_tiny_compress")
                            ),
                            'Tiny_Compress_Settings' => array(
                                'title' => __( 'Tiny Compress Settings', 'psp' ),
                                'url' => admin_url("admin.php?page=psp#tiny_compress")
                            ),
                        )
                    ),
					
                    'Minify' => array(
                        'title' => __( 'Minify', 'psp' ),
                        'url' => admin_url("admin.php?page=psp#Minify"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['Minify']['folder_uri'],
                        'menu_icon' => 'assets/menu_icon.png'
                    ),

				)
			);
			
			$this->the_menu['general'] = array( 
				'title' => __( 'Plugin Settings', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_generalsettings.png',
				'submenu' => array(
					'modules_manager' => array(
						'title' => __( 'Modules Manager', 'psp' ),
						'url' => admin_url("admin.php?page=psp#modules_manager"),
						'folder_uri' => $this->the_plugin->cfg['modules']['modules_manager']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'capabilities' => array(
						'title' => __( 'Capabilities', 'psp' ),
						'url' => admin_url("admin.php?page=psp_capabilities"),
						'folder_uri' => $this->the_plugin->cfg['modules']['capabilities']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'setup_backup' => array(
						'title' => __( 'Setup / Backup', 'psp' ),
						'url' => admin_url("admin.php?page=psp#setup_backup"),
						'folder_uri' => $this->the_plugin->cfg['modules']['setup_backup']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'server_status' => array(
						'title' => __( 'Server status', 'psp' ),
						'url' => admin_url("admin.php?page=psp_server_status"),
						'folder_uri' => $this->the_plugin->cfg['modules']['server_status']['folder_uri'],
						'menu_icon' => 'assets/menu_icon.png'
					),
					
					'remote_support' => array(
						'title' => __( 'Remote Support', 'psp' ),
						'url' => admin_url("admin.php?page=psp_remote_support"),
						'folder_uri' => $this->the_plugin->cfg['modules']['remote_support']['folder_uri'],
						'menu_icon' => 'assets/16_support.png'
					),
				)
			);
			
			$this->clean_menu();			
			$this->capabilities();
		}
		
		public function capabilities() {
			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && !empty($v['submenu']) ) {
					foreach ($v['submenu'] as $sk=>$sv) { // submenu
						$module = $sk;
						if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
						!$this->the_plugin->capabilities_user_has_module($module) )
							unset($this->the_menu["$k"]['submenu']["$sk"]);
					}
				} else {
					$module = $k;
					if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
					!$this->the_plugin->capabilities_user_has_module($module) )
						unset($this->the_menu["$k"]);
				}
			}
			
			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && empty($v['submenu']) ) {
					unset($this->the_menu["$k"]);
				}
			}
		}

		public function clean_menu() {
			foreach ($this->the_menu as $key => $value) {
				if( isset($value['submenu']) ){
					foreach ($value['submenu'] as $kk2 => $vv2) {
						$kk2orig = $kk2;
						// fix to support same module multiple times in menu
						$kk2 = substr( $kk2, 0, (($t = strpos($kk2, '--'))!==false ? $t : strlen($kk2)) );
  
						if( ($kk2 != 'synchronization_log')
							&& !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) {
							unset($this->the_menu["$key"]['submenu']["$kk2orig"]);
						}
					}
				}
			}

			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && empty($v['submenu']) ) {
					unset($this->the_menu["$k"]);
				}
			}
		}

		public function show_menu( $pluginPage='' )
		{
			$plugin_data = $this->the_plugin->details;
 
			$html = array();
			// id="psp-nav-dashboard" 
			$html[] = '<div id="psp-header">';
			$html[] = 	'<div id="psp-header-bottom">';
			$html[] = 		'<div id="psp-topMenu">';
			$html[] = 			'<a href="http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437?rel=AA-Team" target="_blank" class="psp-product-logo">
									<img src="' . ( $this->the_plugin->cfg['paths']['plugin_dir_url'] ) . 'thumb.png" alt="">
									<h2>Premium SEO Pack</h2>
									<h3>' . ( $plugin_data['version'] ) . '</h3>
									
									<span class="psp-rate-now"></span>
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/rate-now.png" class="psp-rate-img">
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/star.gif" class="psp-rate-gif">
									<strong>Don’t forget to rate us!</strong>
								</a>';
								
			if ( $pluginPage == 'depedencies' ) {
				$menu = $this->menu_depedencies;
				$this->current_menu = array(
					0 => 'depedencies',
					1 => 'depedencies'
				);
			} else {
				$menu = $this->the_menu;
			}

			$html[] = 			'<ul>';
								foreach ($menu as $key => $value) {
									$iconImg = '<img src="' . ( $value['folder_uri'] . $value['menu_icon'] ) . '" />';
									$html[] = '<li id="psp-nav-' . ( $key ) . '" class="psp-section-' . ( $key ) . ' ' . ( isset($this->current_menu[0]) && ( $key == $this->current_menu[0] ) ? 'active' : '' ) . '">';
									
									if( $value['url'] == "#" ){
										$value['url'] = 'javascript: void(0)';
									}
									$html[] = 	'<a href="' . ( $value['url'] ) . '">' . ( $iconImg ) . '' . ( $value['title'] ) . '</a>';
									if( isset($value['submenu']) ){
										$html[] = 	'<ul class="psp-sub-menu">';
										foreach ($value['submenu'] as $kk2 => $vv2) {
											// if( ($kk2 != 'synchronization_log') && isset($this->the_plugin->cfg['activate_modules']) && is_array($this->the_plugin->cfg['activate_modules']) && !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) continue;
		
											$iconImg = '<img src="' . ( $vv2['folder_uri'] . $vv2['menu_icon'] ) . '" />';
											$html[] = '<li class="psp-section-' . ( $kk2 ) . '  ' . ( isset($this->current_menu[1]) && $kk2 == $this->current_menu[1] ? 'active' : '' ) . '" id="psp-sub-nav-' . ( $kk2 ) . '">';
											$html[] = 	$iconImg;
											$html[] = 	'<a href="' . ( $vv2['url'] ) . '">' . ( $vv2['title'] ) . '</a>'; 
											
											if( isset($vv2['submenu']) ){
												$html[] = 	'<ul class="psp-sub-sub-menu">';
												foreach ($vv2['submenu'] as $kk3 => $vv3) {
													$html[] = '<li id="psp-sub-sub-nav-' . ( $kk3 ) . '">';
													$html[] = 	'<a href="' . ( $vv3['url'] ) . '">' . ( $vv3['title'] ) . '</a>';
													$html[] = '</li>';
												}
												$html[] = 	'</ul>';
											}
											$html[] = '</li>';
										}
										$html[] = 	'</ul>';
									}
									$html[] = '</li>';
								}
			$html[] = 			'</ul>';
			$html[] = 		'</div>';
			$html[] = 	'</div>';
			
			$html[] = 	'<a href="http://codecanyon.net/user/AA-Team/portfolio?ref=AA-Team" class="psp-aateam-logo">AA-Team</a>';
			
			$html[] = '</div>';
			
			$html[] = '<script>
			(function($) {
				var pspMenu = $("#psp-topMenu");

				pspMenu.on("click", "a", function(e){
					
					var that = $(this),
						href = that.attr("href");

					if( href == "javascript: void(0)" ){
						var current_open = pspMenu.find("li.active");
						current_open.find(".psp-sub-menu").slideUp(350);
						current_open.removeClass("active");
					}
					
					that.parent("li").eq(0).find(".psp-sub-menu").slideDown(350);
					that.parent("li").eq(0).addClass("active");
				});
			})(jQuery);
			
			</script>';
			
			echo implode("\n", $html);
		}

		public function make_active( $section='' )
		{
			$this->current_menu = explode("|", $section);
			return $this;
		}
	}
}