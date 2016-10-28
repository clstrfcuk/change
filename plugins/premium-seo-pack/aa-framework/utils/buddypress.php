<?php
/*
* Define class pspBuddyPress
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspBuddyPress') != true) {
    class pspBuddyPress
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
		
		public $errors = '';
		public $components = array();
		public $admin_components_grouped = array();
		public $components_noadmin = array();
		public $tags_per_pages = array();


		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
			$this->the_plugin = $parent;
			$this->components = array(
				'groups' => array(
					'title' 	=> __('Group Directory', $this->the_plugin->localizationName),
					'std'		=> '',
					'tags'		=> '',
					'actions'	=> array(
						'home' => array(
							'title' 	=> __('Group Home', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'forum' => array(
							'title' 	=> __('Group Forum', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'forum-topic' => array(
							'title' 	=> __('Group Forum Topic', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'members' => array(
							'title' 	=> __('Group Members', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'my-groups' => array(
							'title' 	=> __('Profile Groups', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				),
				'activity' => array(
					'title' 	=> __('Activity Directory', $this->the_plugin->localizationName),
					'std'		=> '',
					'tags'		=> '',
					'actions'	=> array(
						'groups' => array(
							'title' 	=> __('Group Activity', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'activity' => array(
							'title' 	=> __('Activity Home', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'mentions' => array(
							'title' 	=> __('Activity Mentions', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'favorites' => array(
							'title' 	=> __('Activity Favorites', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'friends' => array(
							'title' 	=> __('Activity Friends', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'just-me' => array(
							'title' 	=> __('Profile Activity', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				),
				'members' => array(
					'title' 	=> __('Members Directory', $this->the_plugin->localizationName),
					'std'		=> '',
					'tags'		=> '',
					'actions'	=> array()
				),
				'profile' => array(
					'actions'	=> array(
						'public' => array(
							'title' 	=> __('Profile Home', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				),
				'blogs' => array(
					'title' 	=> __('Blogs Directory', $this->the_plugin->localizationName),
					'std'		=> '',
					'tags'		=> '',
					'actions'	=> array(
						'my-blogs' => array(
							'title' 	=> __('Profile Blogs', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				),
				'forums' => array(
					'actions'	=> array(
						'topics' => array(
							'title' 	=> __('Profile Forums', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'replies' => array(
							'title' 	=> __('Profile Forums Replies', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						),
						'favorites' => array(
							'title' 	=> __('Profile Forums Favorites', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				),
				'friends' => array(
					'actions'	=> array(
						'my-friends' => array(
							'title' 	=> __('Profile Friends', $this->the_plugin->localizationName),
							'std'		=> '',
							'tags'		=> ''
						)
					)
				)
			);
			$this->admin_components_grouped = array(
				'groups' 		=> array('groups', 'activity,groups', 'groups,home', 'groups,forum', 'groups,forum-topic', 'groups,members')

				,'activity' 	=> array('activity', 'activity,activity', 'activity,mentions', 'activity,favorites', 'activity,friends')

				,'members' 		=> array('members')

				,'profile' 		=> array('profile,public', 'activity,just-me', 'blogs,my-blogs', 'groups,my-groups', 'friends,my-friends', 'forums,topics', 'forums,replies', 'forums,favorites')

				,'blogs' 		=> array('blogs')
				
			);
			$this->components_noadmin = array(
				'register' => array(
					'title' 	=> __('Create an Account', $this->the_plugin->localizationName),
					'std'		=> ''
				),
				'activate' => array(
					'title' 	=> __('Activate your Account', $this->the_plugin->localizationName),
					'std'		=> ''
				),
				'search' => array(
					'title' 	=> __('Search', $this->the_plugin->localizationName),
					'std'		=> ''
				)
			);
			$this->tags_per_pages = array(
				'user'			=> array('activity,groups', 'activity,mentions', 'activity,favorites', 'activity,friends', 'activity,just-me', 'profile,public', 'blogs,my-blogs', 'friends,my-friends', 'groups,my-groups'),

				'group'			=> array('groups,home', 'groups,forum', 'groups,forum-topic', 'groups,members'),
				
				'activity'		=> array('activity,activity'),
				
				'forum'			=> array('groups,forum', 'groups,forum-topic'),
				'forum-topic'	=> array('groups,forum-topic')
			);
			$this->tags_list = array(
				'user'			=> array('user_login', 'user_nicename', 'user_registered_date', 'user_display_name', 'user_fullname'),

				'group'			=> array('group_name', 'group_desc'),
				
				'activity'		=> array('activity_content', 'user_login', 'user_nicename', 'user_display_name', 'user_fullname'),
				
				'forum'			=> array('forum_title', 'forum_date', 'forum_description', 'forum_short_description', 'forum_author', 'forum_author_username', 'forum_author_nickname', 'forum_author_description'),
				'forum-topic'	=> array('forum_title', 'forum_date', 'forum_description', 'forum_short_description', 'forum_author', 'forum_author_username', 'forum_author_nickname', 'forum_author_description', 'topic_title', 'topic_date', 'topic_description', 'topic_short_description', 'topic_author', 'topic_author_username', 'topic_author_nickname', 'topic_author_description')
			);
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
		
		
		public function build_components() {
			$components_slugs = array();

			global $bp;
			$conversion = array('xprofile' => 'profile');
        	if( is_array( $bp->active_components ) ){
            	$components = array_keys( $bp->active_components );
  
           		foreach( $components as $key ){
           			$keyy = $key;
           			if ( in_array($key, array_keys($conversion)) ) $keyy = $conversion["$key"];
  
                	if (isset($bp->$keyy)) {
                		$id = isset($bp->$keyy->id) ? $bp->$keyy->id : '';
						$slug = isset($bp->$keyy->slug) ? $bp->$keyy->slug : '';
  
						$slug2 = !empty($slug) ? $slug : $keyy;
						$components_slugs["$slug2"] = $keyy;
                	}
            	}
        	}
			return $components_slugs;
		}
		
		public function get_component_by_slug( $slug='' ) {
			$components = $this->build_components();
			if ( isset($components["$slug"]) && !empty($components["$slug"]) ) {
				return $components["$slug"];
			}
			return $slug;
		}
		
		public function is_component_active( $slug ) {
        	global $bp;
        
        	$component_name = $this->get_component_by_slug( $slug );
			$componentsList = array();
        
        	if( is_array( $bp->active_components ) ){
            	$components = array_keys( $bp->active_components );
            
           		foreach( $components as $key => $component ){
                	$componentsList[ $key ] = $this->get_component_by_slug( $component );
            	}
        	}
        
        	if( !empty($componentsList) && is_array($componentsList) )
            	if( in_array( $component_name, $componentsList ) ) return true;        
        	return false;
		}
		
		public function get_bp_pagetype( $page_type='' ) {
        	global $bp;

	        if( is_page() && $this->the_plugin->is_buddypress() && !empty($bp->current_component) ){
	            $slug = $bp->current_component;
		        $component = $this->get_component_by_slug( $slug );
		        $action = $bp->current_action;
     
	            if( !empty($component) ) {
	                if( !empty($action) ) {

  						$new_page_type = '';
		                if ( bp_is_group_forum_topic() ) {
		                    $new_page_type = 'bp_' . $component . '_' . $action . '-topic';                           
		                    
		                } else if ( !bp_is_component_front_page( 'activity' )
							&&  bp_is_activity_component() && is_numeric($action) /*&& $action != 'just-me'*/ ) {
		                	$new_page_type = 'bp_activity_activity';
							$action = 'activity';
		                } else {
		                    $new_page_type = 'bp_' . $component . '_' . $action;
		                }
						
						$page_type = $new_page_type;

						if ( $this->verifyPageSlugIsSet($component, $action) ) ;
						else $this->add_dynamic_pagetype($page_type);
	                } else {
	                	$new_page_type = 'bp_' . $component;
	                	$page_type = $new_page_type;
						if ( $this->verifyPageSlugIsSet($component, '') ) ;
						else $this->add_dynamic_pagetype($page_type);
	                }
	            }
        	}
			//var_dump('<pre>',$slug, $component, $action, $page_type,'</pre>');  
	        return apply_filters( 'premiumseo_seo_bp_pagetype', $page_type );
		}

		private function add_dynamic_pagetype( $page_type='' ) {
			if ( empty($page_type) ) return false;
  
			$bp_opt = get_option('psp_buddypress', true);
			$bp_opt = (array) ( maybe_unserialize($bp_opt) );
			if ( !isset($bp_opt['slugs']) )
				$bp_opt['slugs'] = array();
			if ( !in_array($page_type, $bp_opt['slugs']) )
				$bp_opt['slugs'][] = $page_type;
  
			update_option('psp_buddypress', $bp_opt);

			return true;
		}
		
		public function get_dynamic_pagetypes() {
			$ret = array();

			$bp_opt = get_option('psp_buddypress', true);
			$bp_opt = (array) ( maybe_unserialize($bp_opt) );
			if ( !isset($bp_opt['slugs']) ) $ret = array();
			else $ret = $bp_opt['slugs'];

			return $ret; 
		}

		public function get_bp_list_pagetypes( $arr_orig=array() ) {
			$new_arr = array();
  
			$arrList = array($this->components, $this->components_noadmin);
			foreach ( $arrList as $components ) {
				foreach ($components as $key => $val) {
					if ( isset($val['title']) )
						$new_arr[] = 'bp_' . $key;
	
					if ( isset($val['actions']) && !empty($val['actions']) && is_array($val['actions']) ) {
						foreach ($val['actions'] as $key2 => $val2) {
							$new_arr[] = 'bp_' . $key . '_' . $key2;
						}
					}
				}
			}
			
			$from_opt = $this->get_dynamic_pagetypes();
			if ( !empty($from_opt) ) {
				foreach ($from_opt as $key => $val)
					$new_arr[] = $val;
			}
  
			$arr = array_merge((array) $arr_orig, $new_arr);
			$arr = array_unique($arr);
			return apply_filters( 'premiumseo_seo_bp_list_pagetypes', $arr );
		}
		
		private function verifyPageSlugIsSet($component, $action='', $whereIs='all') {

			if ( in_array($whereIs, array('all', 'static', 'all,admin', 'all,noadmin', 'static,admin', 'static,noadmin')) ) {

				if ( in_array($whereIs, array('all,admin', 'static,admin')) )
					$arr = array($this->components);
				else if ( in_array($whereIs, array('all,noadmin', 'static,noadmin')) )
					$arr = array($this->components_noadmin);
				else
					$arr = array($this->components, $this->components_noadmin);

				foreach ($arr as $comp) {
					if ( isset($component) && !empty($component)
						&& isset($action) && !empty($action) ) {
		
						if ( isset($comp["$component"])
							&& isset($comp["$component"]['actions']["$action"])
							&& !empty($comp["$component"]['actions']["$action"]) ) {
							return true;
						}
					} else if ( isset($component) && !empty($component) ) {
		
			           	if ( isset($comp["$component"]) && isset($comp["$component"]['title']) ) {
			               	return true;
						}
					}
				}
			}

			if ( in_array($whereIs, array('all', 'dynamic')) ) {
				$new_key = $this->buildPageSlug($component, $action);
				$from_opt = $this->get_dynamic_pagetypes();
				if ( !empty($from_opt) ) {
					if ( in_array($new_key, $from_opt) ) return true;
				}
			}

			return false;
		}
		
		/**
		 * Build Admin Options
		 */
		public function build_admin_options() {
			$el = $this->admin_components_grouped;
			$el2 = $this->components;
			
			$ret = array(
				'elements' 	=> array()
				,'tabs'		=> array(
					'__tab1'	=> array(',bp_help_format_tags'),
					'__tab2'	=> array(),
					'__tab3'	=> array(),
					'__tab4'	=> array(),
					'__tab5'	=> array()
				)
				,'subtabs'	=> array(
					'__tab1'	=> array(
						'__subtab2' => array(
							__('Buddy Press', $psp->localizationName), 'bp_help_format_tags')),
					'__tab2'	=> array(
						'__subtab2' => array(
							__('Buddy Press', $psp->localizationName), '')),
					'__tab3'	=> array(
						'__subtab2' => array(
							__('Buddy Press', $psp->localizationName), '')),
					'__tab4'	=> array(
						'__subtab2' => array(
							__('Buddy Press', $psp->localizationName), '')),
					'__tab5'	=> array(
						'__subtab2' => array(
							__('Buddy Press', $psp->localizationName), ''))
				)
			);
  
			foreach ($el as $k => $v) {
				if ( !empty($v) ) {
					foreach ($v as $kk => $vv) {
						if ( empty($vv) ) continue 1;
						
						$part = array();						
						$part = explode(',', $vv);

						$key = 'bp_';
						$component = isset($part[0]) ? $part[0] : '';
						$action = isset($part[1]) ? $part[1] : '';
						
						$key .= implode('_', $part);
						
						if ( !empty($component) && !empty($action) )
							$theVal = $el2["$component"]['actions']["$action"];
						else if ( !empty($component) )
							$theVal = $el2["$component"];
						
						$metatags = array('title' => 'Title', 'desc' => 'Description', 'kw' => 'Keywords', 'robots' => 'Robots');
						$metatags_tabs = array('title' => '__tab2', 'desc' => '__tab3', 'kw' => '__tab4', 'robots' => '__tab5');
						$metatags_def = array('title' => "{title_default}", 'desc' => "{desc_default}", 'kw' => "{kw_default}", 'robots' => array());
						foreach ( array_keys($metatags) as $kk2 => $vv2) {
							
							$key2 = $key . '_' . $vv2;

							// type
							$type = 'text';
							if ( in_array($vv2, array('desc')) ) $type = 'textarea';
							else if ( in_array($vv2, array('robots')) ) $type = 'multiselect';
							
							// std
							$std = isset($theVal['std']) && !empty($theVal['std']) ? $theVal['std'] : $metatags_def["$vv2"];
							
							// title
							$title = $theVal['title'] . ' ' . __($metatags["$vv2"], $this->the_plugin->localizationNam);
							
							// description
							$desc = array();
							$desc[] = sprintf( __('Component: <u>%s</u> ; Action: <u>%s</u>', $this->the_plugin->localizationNam), $component, (!empty($action) ? $action : $action));
							$desc[] = '<br />Available here: (global availability) tags; {buddypress global availability}; (buddypress specific availability) tags: ';
							//if ( !empty($theVal['tags']) && is_array($theVal['tags']) )							//	$desc[] = '{' . implode('}, {', $theVal['tags']) . '}';
							$__desc = $this->field_admin_desc_tags(
								implode(',', $part), $vv2
							);
							$desc[] = '{' . implode('}, {', $__desc) . '}';
							$desc = implode('', $desc);
							
							$ret['elements']["$key2"] = array(
								'type' 			=> $type,
								'std' 			=> $std,
								'size' 			=> 'large',
								'force_width'	=> '400',
								'title' 		=> $title,
								'desc' 			=> $desc
							);
							
							if ( $type == 'multiselect' ) {
								$options = $this->__metaRobotsList();
								$ret['elements']["$key2"]['options'] = $options;
								$ret['elements']["$key2"]['std'] = array();
							}
							
							// tabs
							$__theTab = $metatags_tabs["$vv2"];
							//if ( !empty($ret['tabs']["$__theTab"][0]) )
								$ret['tabs']["$__theTab"][0] .= ',' . $key2;
							//else 
							//	$ret['tabs']["$__theTab"][0] .= $key2;
							
							// subtabs
							if ( !empty($ret['subtabs']["$__theTab"]["__subtab2"][1]) )
								$ret['subtabs']["$__theTab"]["__subtab2"][1] .= ',' . $key2;
							else 
								$ret['subtabs']["$__theTab"]["__subtab2"][1] .= $key2;
						}
					}
				}
			}
			return $ret;
		}

		private function __metaRobotsList() {
			return array(
				'noindex'	=> 'noindex', //support by: Google, Yahoo!, MSN / Live, Ask
				'nofollow'	=> 'nofollow', //support by: Google, Yahoo!, MSN / Live, Ask
				'noarchive'	=> 'noarchive', //support by: Google, Yahoo!, MSN / Live, Ask
				'noodp'		=> 'noodp' //support by: Google, Yahoo!, MSN / Live
			);
		}
		
		public function buildPageSlug($component, $action='') {
			$new_key = 'bp_';
			if ( isset($component) && !empty($component)
				&& isset($action) && !empty($action) ) {
				$new_key .= $component .'_' . $action;
				return $new_key;
			} else if ( isset($component) && !empty($component) ) {
				$new_key .= $component;
				return $new_key;
			}
			return '';
		}
		
		public function buildFromPageSlug( $page_type ) {
			$ret = array('component' => '', 'action' => '', 'grouped' => '');

			$page_type2 = str_replace('bp_', '', $page_type);
			$page_type2 = explode('_', $page_type2);
			if ( isset($page_type2[0]) ) $ret['component'] = $page_type2[0];
			if ( isset($page_type2[1]) ) $ret['action'] = $page_type2[1];
			$ret['grouped'] = implode(',', $page_type2);

			return $ret;
		}
		
		public function add_bp_settings($settings) {
			$page_type = $this->get_bp_pagetype();
			$ca = $this->buildFromPageSlug($page_type);
			$component = isset($ca['component']) ? $ca['component'] : '';
			$action = isset($ca['action']) ? $ca['action'] : '';
  
			$new_settings = array();
			if ( !$this->verifyPageSlugIsSet($component, $action, 'static,admin') ) {
				$fields = array('title' => "{title_default}", 'desc' => "{desc_default}", 'kw' => "{kw_default}", 'robots' => array());
				foreach ($fields as $field => $fieldval) {
					$new_settings["$page_type"."_".$field] = $fieldval;
				}
			}
   
			$settings = array_merge($settings, (array) $new_settings);
			return $settings;
		}
		
		public function get_tag_field_default($page_type) {
			$ret = array('title' => '', 'desc' => '', 'kw' => '', 'robots' => array());
			
			$ca = $this->buildFromPageSlug($page_type);
			$component = isset($ca['component']) ? $ca['component'] : '';
			$action = isset($ca['action']) ? $ca['action'] : '';
			$grouped = isset($ca['grouped']) ? $ca['grouped'] : '';
			
			if ( !$this->verifyPageSlugIsSet($component, $action, 'static') ) {

				$ret['title'] = '{component_name_h} - {action_h} | {site_title}';
				$ret['desc'] = '{component_name_h} - {action_h} | {site_description}';
				$ret['kw'] = '{component_name_h}, {action_h}';
			} else {

				$fields = array('title', 'desc', 'kw');
				$tags_per_pages = $this->tags_per_pages;
				
				// component&action title
				if ( !empty($component) && !empty($action) ) {

					$title = '';
					if ( isset($this->components["$component"]['actions']["$action"]['title']) )
						$title = $this->components["$component"]['actions']["$action"]['title'];
					else if ( isset($this->components_noadmin["$component"]['actions']["$action"]['title']) )
						$title = $this->components_noadmin["$component"]['actions']["$action"]['title'];
				} else if ( !empty($component) ) {

					$title = '';
					if ( isset($this->components["$component"]['title']) )
						$title = $this->components["$component"]['title'];
					else if ( isset($this->components_noadmin["$component"]['title']) )
						$title = $this->components_noadmin["$component"]['title'];
				}
				
				foreach ( $fields as $field ) {

					if ( !empty($title) && in_array($field, array('title', 'desc', 'kw')) )
						$ret["$field"][] = $title;
					
					foreach ($tags_per_pages as $variable => $pageList) {
						if ( in_array($grouped, $pageList) ) {

							if ( $variable=='group' ) {
								if ( in_array($field, array('title', 'kw')) )
									$ret["$field"][] = '{group_name}';
								else
									$ret["$field"][] = '{group_desc}';

							} else if ( $variable=='user' ) {
								$ret["$field"][] = '{user_display_name}';

							} else if ( $variable=='activity' ) {
								if ( in_array($field, array('title', 'kw')) )
									$ret["$field"][] = '{user_display_name}';
								else
									$ret["$field"][] = '{activity_content}';

							} else if ( in_array($variable, array('forum', 'forum-topic' )) ) {
								if ( $variable == 'forum' ) {
									if ( in_array($field, array('title', 'kw')) ) {
										$ret["$field"][] = '{forum_title}';
									}
									else {
										$ret["$field"][] = '{forum_short_description}';
									}
								}
								else if ( $variable == 'forum-topic' ) {
									if ( in_array($field, array('title', 'kw')) ) {
										$ret["$field"][] = '{topic_title}';
									}
									else {
										$ret["$field"][] = '{topic_short_description}';
									}
								}
							}
						}
					}
  
					if ( in_array($field, array('title', 'desc')) )
						$ret["$field"] = implode(' - ', $ret["$field"]);
					else
						$ret["$field"] = implode(', ', $ret["$field"]);

					if ( $field=='title' )
						$ret["$field"] .= ' | ' . '{site_title}';
					else if ( $field=='desc' )
						$ret["$field"] .= ' | ' . '{site_description}';
				}
			}
			
			return $ret;
		}

		public function field_admin_desc_tags($page_type, $field) {
			$ret = array('component_slug', 'component_name', 'component_name_h', 'action', 'action_h');
			$ret[] = "{$field}_default";
			
			foreach ( $this->tags_per_pages as $key => $val ) {
				if (in_array($page_type, $val) && isset($this->tags_list["$key"]))
					$ret = array_merge($ret, $this->tags_list["$key"]);
			}
			$ret = array_unique($ret);
			return $ret;
		}
    }
}

// Initialize the pspBuddyPress class
//$pspBuddyPress = new pspBuddyPress();
