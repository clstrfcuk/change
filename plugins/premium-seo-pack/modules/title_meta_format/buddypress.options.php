<?php

global $psp;
if ( $psp->is_buddypress() ) {
	/*
	$__psp_mfo_bp = array(
					// tabs
					'tabs'	=> array(
						'__tab1'	=> array(',bp_help_format_tags'),
						'__tab2'	=> array(',bp_activity_title'),
						'__tab3'	=> array(',bp_activity_desc'),
						'__tab4'	=> array(',bp_activity_kw'),
						'__tab5'	=> array(',bp_activity_robots')
					)
					
					// subtabs
					,'subtabs'	=> array(
						'__tab1'	=> array(
							'__subtab2' => array(
								__('Buddy Press', 'psp'), 'bp_help_format_tags')),
						'__tab2'	=> array(
							'__subtab2' => array(
								__('Buddy Press', 'psp'), 'bp_activity_title')),
						'__tab3'	=> array(
							'__subtab2' => array(
								__('Buddy Press', 'psp'), 'bp_activity_desc')),
						'__tab4'	=> array(
							'__subtab2' => array(
								__('Buddy Press', 'psp'), 'bp_activity_kw')),
						'__tab5'	=> array(
							'__subtab2' => array(
								__('Buddy Press', 'psp'), 'bp_activity_robots'))
					)
					
					,'elements'	=> array(
						//=============================================================
						//== Buddy Press - help
						'bp_help_format_tags' => array(
							'type' 		=> 'message',
							
							'html' 		=> __('
								<h2>BP Basic Setup</h2>
								<p>You can set the custom page title using defined formats tags.</p>
								<h3>Available Format Tags</h3>
								<ul>
								</ul><br />
								<p>Info: when use {keywords}, if for a specific post|page {focus_keywords} is found then it is used, otherwise {keywords} remains active</p>
								', 'psp')
						),
						
						//=============================================================
						//== Buddy Press - title format
						'bp_activity_title' 	=> array(
							'type' 		=> 'text',
							'std' 		=> 'a{site_title}',
							'size' 		=> 'large',
							'force_width'=> '400',
							'title' 	=> __('Homepage Title Format:', 'psp'),
							'desc' 		=> __('Available here: (global availability) tags', 'psp')
						),
						
						//=============================================================
						//== meta description
						'bp_activity_desc' 	=> array(
							'type' 		=> 'textarea',
							'std' 		=> 'b{site_description}',
							'size' 		=> 'large',
							'force_width'=> '400',
							'title' 	=> __('Homepage Meta Description:', 'psp'),
							'desc' 		=> __('Available here: (global availability) tags', 'psp')
						),
						
						//=============================================================
						//== meta keywords
						'bp_activity_kw' 	=> array(
							'type' 		=> 'text',
							'std' 		=> 'c{keywords}',
							'size' 		=> 'large',
							'force_width'=> '400',
							'title' 	=> __('Homepage Meta Keywords:', 'psp'),
							'desc' 		=> __('Available here: (global availability) tags', 'psp')
						),
						
						//=============================================================
						//== meta robots
						'bp_activity_robots' 	=> array(
							'type' 		=> 'multiselect',
							'std' 		=> array(),
							'size' 		=> 'small',
							'force_width'=> '400',
							'title' 	=> __('Homepage Meta Robots:', 'psp'),
							'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
							'options'	=> $__metaRobotsList
						),
					)
	);
	*/
	
	$__psp_mfo_bp = $psp->buddypress_utils->build_admin_options();
	//=============================================================
	//== Buddy Press - help
	$__psp_mfo_bp['elements']['bp_help_format_tags'] = array(
							'type' 		=> 'message',
							
							'html' 		=> __('
								<h2>BuddyPress Basic Setup</h2>
								<p>You can set the custom page title using defined formats tags.</p>
								<h3>Available Format Tags</h3>
								<ul>
									<li><code>{component_slug}</code> : buddypress component slug (buddypress global availability)</li>
									<li><code>{component_name}</code> : buddypress component name (buddypress global availability)</li>
									<li><code>{component_name_h}</code> : buddypress component name human readable - word first letter is uppercase (buddypress global availability)</li>
									<li><code>{action}</code> : buddypress action (buddypress global availability)</li>
									<li><code>{action_h}</code> : buddypress action human readable - word first letter is uppercase (buddypress global availability)</li>
									<li><code>{group_name}</code> : buddypress group name (buddypress specific availability)</li>
									<li><code>{group_desc}</code> : buddypress group description (buddypress specific availability)</li>
									<li><code>{user_login}</code> : buddypress user login (buddypress specific availability)</li>
									<li><code>{user_nicename}</code> : buddypress user nicename (buddypress specific availability)</li>
									<li><code>{user_registered_date}</code> : buddypress user registration date (buddypress specific availability)</li>
									<li><code>{user_display_name}</code> : buddypress display name (buddypress specific availability)</li>
									<li><code>{user_fullname}</code> : buddypress full name (buddypress specific availability)</li>
									<li><code>{activity_content}</code> : buddypress activity content (buddypress specific availability)</li>
									<li><code>{forum_title}</code> : buddypress forum title (buddypress specific availability)</li>
									<li><code>{forum_date}</code> : buddypress forum date (buddypress specific availability)</li>
									<li><code>{forum_description}</code> : buddypress forum description (buddypress specific availability)</li>
									<li><code>{forum_short_description}</code> : buddypress forum short description (buddypress specific availability)</li>
									<li><code>{forum_author}</code> : buddypress forum author (buddypress specific availability)</li>
									<li><code>{forum_author_username}</code> : buddypress forum author username (buddypress specific availability)</li>
									<li><code>{forum_author_nickname}</code> : buddypress forum author nickname (buddypress specific availability)</li>
									<li><code>{forum_author_description}</code> : buddypress forum author description (buddypress specific availability)</li>
									<li><code>{topic_title}</code> : buddypress topic title (buddypress specific availability)</li>
									<li><code>{topic_date}</code> : buddypress topic date (buddypress specific availability)</li>
									<li><code>{topic_description}</code> : buddypress topic description (buddypress specific availability)</li>
									<li><code>{topic_short_description}</code> : buddypress topic short description (buddypress specific availability)</li>
									<li><code>{topic_author}</code> : buddypress topic author (buddypress specific availability)</li>
									<li><code>{topic_author_username}</code> : buddypress topic author username (buddypress specific availability)</li>
									<li><code>{topic_author_nickname}</code> : buddypress topic author nickname (buddypress specific availability)</li>
									<li><code>{topic_author_description}</code> : buddypress topic author description (buddypress specific availability)</li>
									<li><code>{title_default}</code> : buddypress title auto-generated by our plugin module (buddypress specific availability)</li>
									<li><code>{desc_default}</code> : buddypress description auto-generated by our plugin module (buddypress specific availability)</li>
									<li><code>{kw_default}</code> : buddypress keywords auto-generated by our plugin module (buddypress specific availability)</li>
								</ul><br />
								<!--<p>Info: when use {.._default}</p>-->
								', 'psp')
	);
//var_dump('<pre>', $__psp_mfo_bp, '</pre>'); die('debug...');  
}
?>
