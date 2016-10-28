<?php
/**
 * Dummy module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

$__psp_video_include = array(
	'localhost'			=> 'Self Hosted'
	,'blip'				=> 'Blip.tv'
	,'dailymotion'		=> 'Dailymotion.com'
	,'dotsub'			=> 'Dotsub.com'
	,'flickr'			=> 'Flickr.com'
	,'metacafe'			=> 'Metacafe.com'
	,'screenr'			=> 'Screenr.com'
	,'veoh'				=> 'Veoh.com'
	,'viddler'			=> 'Viddler.com'
	,'vimeo'			=> 'Vimeo.com'
	,'vzaar'			=> 'Vzaar.com'
	,'youtube'			=> 'Youtube.com'
	,'wistia'			=> 'Wistia.com'
);

function psp_postTypes_priority( $istab = '', $is_subtab='' ) {
	global $psp;

	ob_start();

	$options = $psp->get_theoption('psp_sitemap');

	$standard_content = psp_standardContent_get();
	$custom_posttypes = psp_postTypes_get(false);
 
	$post_types = (array) $standard_content; //array_intersect( array('post', 'page'), $standard_content );		
	$post_types = array_merge( $post_types, array('taxonomy' => __('Custom Taxonomies', 'psp')), $custom_posttypes );
?>
<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label><?php _e('Priorities', 'psp'); ?>:</label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($post_types as $key => $value){
		$val = '';
		if( isset($options['priority']) && isset($options['priority'][$key]) ){
			$val = $options['priority'][$key];
		}
		$val = (string) $val;
		?>
		<label for="priority[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="priority[<?php echo $key;?>]" name="priority[<?php echo $key;?>]" style="width:60px;">
			<?php
			foreach (range(0, 1, 0.1) as $kk => $vv){
				$vv = (string) $vv;
				echo '<option value="' . ( $vv ) . '" ' . ( $val == $vv ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	} 
	?>
	</div>
	<p style="font-style: italic;"><?php _e('Because this value is relative to other pages on your site, assigning a high priority (or specifying the same priority for all URLs) will not help your site\'s search ranking. In addition, setting all pages to the same priority will have no effect.', 'psp'); ?></p>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
} 

function psp_postTypes_changefreq( $istab = '', $is_subtab='' ) {
	global $psp;

	ob_start();

	$options = $psp->get_theoption('psp_sitemap');

	$standard_content = psp_standardContent_get();
	$custom_posttypes = psp_postTypes_get(false);
 
	$post_types = (array) $standard_content; //array_intersect( array('post', 'page'), $standard_content );		
	$post_types = array_merge( $post_types, array('custom_taxonomies' => __('Custom Taxonomies', 'psp')), $custom_posttypes );
?>
<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label><?php _e('Frequencies', 'psp'); ?>:</label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($post_types as $key => $value){
		
		$val = '';
		if( isset($options['changefreq']) && isset($options['changefreq'][$key]) ){
			$val = $options['changefreq'][$key];
		}
		?>
		<label for="changefreq[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="changefreq[<?php echo $key;?>]" name="changefreq[<?php echo $key;?>]" style="width:120px;">
			<?php
			foreach (array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') as $kk => $vv){
				echo '<option value="' . ( $vv ) . '" ' . ( $val == $vv ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
	} 
	?>
	</div>
	<p style="font-style: italic;"><?php _e('Provides a hint about how frequently the page is likely to change.', 'psp'); ?></p>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function __pspNotifyEngine( $engine='google', $action='default', $istab = '', $is_subtab='' ) {
	global $psp;
	
	$req['action'] = $action;
	
	if ( $req['action'] == 'getStatus' ) {
		$notifyStatus = $psp->get_theoption('psp_sitemap_engine_notify');
		if ( $notifyStatus === false || !isset($notifyStatus["$engine"]) || !isset($notifyStatus["$engine"]["sitemap"]) )
			return '';
		return $notifyStatus["$engine"]["sitemap"]["msg_html"];
	}

	$html = array();
	
	$html[] = '<div class="psp-form-row psp-notify-engine-ping psp-notify-' . $engine . ' ' . ($istab!='' ? ' '.$istab : '') . ($is_subtab!='' ? ' '.$is_subtab : '') . '">';

	if ( $engine == 'google' ) {
		$html[] = '<div style="padding-bottom: 8px;">' . sprintf( __('Notify Google: you can check statistics on <a href="%s" target="_blank">Google Webmaster Tools</a>', 'psp'), 'http://www.google.com/webmasters/tools/' ). '</div>';
	} else if ( $engine == 'bing' ) {
		$html[] = '<div style="padding-bottom: 8px;">' . sprintf( __('Notify Bing: you can check statistics on <a href="%s" target="_blank">Bing Webmaster Tools</a>', 'psp'), 'http://www.bing.com/toolbox/webmaster' ). '</div>';
	}
	
	ob_start();
?>
		<label for="sitemap_type<?php echo '_'.$engine; ?>" style="display:inline;float:none;"><?php echo __('Select Sitemap', 'psp');?>:</label>
		&nbsp;
		<select id="sitemap_type<?php echo '_'.$engine; ?>" name="sitemap_type" style="width:160px;">
			<?php
			foreach (array('sitemap' => 'Sitemap.xml', 'sitemap_images' => 'Sitemap-Images.xml', 'sitemap_videos' => 'Sitemap-Videos.xml') as $kk => $vv){
				echo '<option value="' . ( $kk ) . '" ' . ( 0 ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
			} 
			?>
		</select>&nbsp;&nbsp;
<?php
	$selectSitemap = ob_get_contents();
	ob_end_clean();
	$html[] = $selectSitemap;
	
	$html[] = '<input type="button" class="psp-button blue" style="width: 160px;" id="psp-notify-' . $engine . '" value="' . ( __('Notify '.ucfirst($engine), 'psp') ) . '">
	<span style="margin:0px 0px 0px 10px" class="response">' . __pspNotifyEngine( $engine, 'getStatus' ) . '</span>';

	$html[] = '</div>';

	// view page button
	ob_start();
?>
	<script>
	(function($) {
		var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>',
		engine = '<?php echo $engine; ?>';

		$("body").on("click", "#psp-notify-"+engine, function(){

			$.post(ajaxurl, {
				'action' 		: 'pspAdminAjax',
				'sub_action'	: 'notify',
				'engine'		: engine,
				'sitemap_type'	: $('#sitemap_type_'+engine).val()
			}, function(response) {

				var $box = $('.psp-notify-'+engine), $res = $box.find('.response');
				$res.html( response.msg_html );
				if ( response.status == 'valid' )
					return true;
				return false;
			}, 'json');
		});
		
		$('#sitemap_type_'+engine).on('change', function (e) {
			e.preventDefault();

			$.post(ajaxurl, {
				'action' 		: 'pspAdminAjax',
				'sub_action'	: 'getStatus',
				'engine'		: engine,
				'sitemap_type'	: $('#sitemap_type_'+engine).val()
			}, function(response) {

				var $box = $('.psp-notify-'+engine), $res = $box.find('.response');
				$res.html( response.msg_html );
				if ( response.status == 'valid' )
					return true;
				return false;
			}, 'json');
		});
   	})(jQuery);
	</script>
<?php
	$__js = ob_get_contents();
	ob_end_clean();
	$html[] = $__js;

	return implode( "\n", $html );
}

function psp_standardContent_get() {
	global $psp;

	$post_types = array(
		'site'		    => __('Home', 'psp'),
		//'misc'          => __('Miscellaneous', 'psp'),
		'post'			=> __('Posts', 'psp'),
		'page'			=> __('Pages (static)', 'psp'),
		'category'		=> __('Categories', 'psp'),
		'post_tag'		=> __('Tag pages', 'psp'),
		'archive'		=> __('Archives', 'psp'),
		'author'		=> __('Author pages', 'psp'),
	);

	//unset($post_types['attachment'], $post_types['revision']);
	return $post_types;
}

function psp_postTypes_get( $builtin=true ) {
	global $psp;

	$pms = array(
		'public'   => true,
	);
	if ( $builtin === true || $builtin === false  ) {
		$pms = array_merge($pms, array(
			'_builtin' => $builtin, // exclude post, page, attachment
		));
	}
	$post_types = get_post_types($pms, 'objects');
	unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);

	$ret = array();
	foreach ( $post_types as $key => $post_type ) {
		$value = $post_type->label;
		$ret["$key"] = $value;
	}
	return $ret;
}

function psp_taxonomies_get( $builtin=true ) {
	global $psp;

    $pms = array(
        'public'   => true,
    );
    if ( $builtin === true || $builtin === false  ) {
        $pms = array_merge($pms, array(
            '_builtin' => $builtin, // exclude post_tag, category
        ));
    }
	$post_types = get_taxonomies($pms, 'objects');
	unset($post_types['post_format'], $post_types['nav_menu'], $post_types['link_category']);
	
	$ret = array();
	foreach ( $post_types as $key => $post_type ) {
		$value = $post_type->label;
		$ret["$key"] = $value;
	}
	return $ret;
}

function psp_categories_get() {
	global $psp;

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


global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'sitemap' => array(
				'title' 	=> __('Sitemap settings', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
                // tabs
                'tabs'  => array(
                    '__tab1'    => array(__('General', 'psp'), 'help_general, xmlsitemap_html, notify, notify_virtual_robots, global_items_per_page, stylesheet, lastmod_use_gmt, base_url, include_img'),
                    '__tab2'    => array(__('Execution variables', 'psp'), 'help_execution_variables, memory_limit, execution_time_limit, compress'),
                    '__tab3'    => array(__('Notify search engines', 'psp'), 'help_notify_search_engines, notify_google, notify_bing'),
                    '__tab4'    => array(__('Including', 'psp'), 'help_include, standard_content, post_types, taxonomies'),
                    '__tab5'    => array(__('Excluding', 'psp'), 'help_exclude, exclude_categories, exclude_posts_ids'),
                    '__tab6'    => array(__('Other settings', 'psp'), 'help_other_settings, author_roles, archive_type, taxonomies_zero_posts'),
                    '__tab7'    => array(__('Formatting', 'psp'), 'help_formatting, priority, changefreq'),
                    '__tab8'    => array(__('Video', 'psp'), 'help_video_sitemap, video_title_prefix, video_social_force, thumb_default, video_include, vzaar_domain, viddler_key, flickr_key')
                ),
                
				// create the box elements array
				'elements'	=> array(
				
                    /*'_header_exclude' => array(
                        'type'      => 'html',
                        'html'      => __(
                            '<div class="psp-form-row psp-ad-section-header">
                                <div>Excluding - only for posts | pages | custom post types sitemaps</div>
                            </div>', 'psp')
                    ),*/
                    
					// General
					'help_general' => array(
						'type' 		=> 'message',
						'status' 	=> 'info',
						'html' 		=> __('
							<h3 style="margin: 0px 0px 5px 0px;">General Settings</h3>
							<p>Settings available for the sitemap module!</p>
						', 'psp')
					),
					
					/*
					'logo' => array(
						'type' 			=> 'upload_image_wp',
						'size' 			=> 'large',
						'force_width'	=> '80',
						'preview_size'	=> 'large',	
						'value' 		=> __('Upload Image', 'psp'),
						'title' 		=> __('Logo', 'psp'),
						'desc' 			=> __('Upload your Logo using the native media uploader', 'psp'),
					),*/
					
					'xmlsitemap_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="psp-form-row __tab1 __subtab1">
								<label for="site-items">' . __('<a href="https://support.google.com/webmasters/answer/75712?hl=en&ref_topic=4581190" target="_blank">Index sitemap</a>:', 'psp') . '</label>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap.xml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap.xml') ) . '</a>
								</div>
								
								<label for="site-items">' . __('Images sitemap:', 'psp') . '</label>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-images.xml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-images.xml') ) . '</a>
								</div>
								
								<label for="site-items">' . __('Videos sitemap:', 'psp') . '</label>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-videos.xml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-videos.xml') ) . '</a>
								</div>
							</div>'
					),
					
					'notify' => array(
						'type' 		=> 'html',
						'html' 		=> __(
							'<div class="psp-form-row __tab1 __subtab1">
								<div>If you are using a custom robots.txt file, you must add the following Sitemap XML URLs:
									<ul style="margin-left: 20px;">
										<li><i>'. home_url('/sitemap.xml'). '</i></li>
										<li><i>'. home_url('/sitemap-images.xml'). '</i></li>
										<li><i>'. home_url('/sitemap-videos.xml'). '</i></li>
									</ul>
								</div>
								<div>If you are using Wordpress virtual robots.txt file, check bellow to include your Sitemap XML URL.</div>
							</div>', 'psp')
					),
					
					'notify_virtual_robots' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Add to virtual robots.txt: ', 'psp'),
						'desc' 		=> __('Add to Wordpress virtual robots.txt', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'global_items_per_page' => array(
						'type' 		=> 'text',
						'std' 		=> '5000',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> 'Items per page (global)',
						'desc' 		=> 'Global limit for items per page: default 5000 ( maximum allowed by google is 50 000 ). This setting is applied to all sitemaps ( eg: sitemaps like post.xml are split into post_part1.xml, post_part2.xml, etc. when limit reached. )',
					),
					
					'stylesheet' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '70',
						'title' 	=> 'Enable Stylesheet',
						'desc' 		=> 'enable default stylesheet for sitemaps',
						'options' 	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
                    'lastmod_use_gmt'    => array(
                        'type'      => 'select',
                        'std'       => 'no',
                        'size'      => 'large',
                        'force_width'=> '70',
                        'title'     => 'Last modified is GMT:',
                        'desc'      => 'if you choose NO => local timezone setting from Settings > General will be used',
                        'options'   => array(
                            'yes'   => __('YES', 'psp'),
                            'no'    => __('NO', 'psp')
                        )
                    ),
					
                    /*'base_url'  => array(
                        'type'      => 'text',
                        'std'       => '0',
                        'size'      => 'large',
                        'force_width'=> '150',
                        'title'     => __('base URL: ', 'psp'),
                        'desc'      => __('vverride the base url of the sitemap', 'psp')
                    ),*/

					/*'include_img' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Include Images:', 'psp'),
						'desc' 		=> __('Website posts, pages sitemap.xml file will also include images (the separate sitemap-images.xml file also remains and contains the posts/pages with theirs associated images)', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),*/
					
                    // Execution variables
                    'help_execution_variables' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Execution variables</h3>
                            <p>Settings available for the sitemap module!</p>
                        ', 'psp')
                    ),
					
					'memory_limit' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '256',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Memory limit: ', 'psp'),
						'desc' 		=> __('Increase the memory limit to: (in megabytes, eg: "124", "256", "512") - integer value.', 'psp')
					),
					'execution_time_limit' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '0',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Execution time limit: ', 'psp'),
						'desc' 		=> __('Increase the execution time limit to: (in seconds, eg: "60" or "0" for unlimited) - integer value.', 'psp')
					),
					'compress' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Compress: ', 'psp'),
						'desc' 		=> __('If you choose YES => automatically compress the sitemap if the requesting client supports it (gzip is used)', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
                    // Notify search engines
                    'help_notify_search_engines' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Notify search engines</h3>
                            <p>Settings available for the sitemap module!</p>
                        ', 'psp')
                    ),

					'notify_google' => array(
						'type' => 'html',
						'html' => __pspNotifyEngine( 'google', 'default', '__tab3', '' )
					),
					
					'notify_bing' => array(
						'type' => 'html',
						'html' => __pspNotifyEngine( 'bing', 'default', '__tab3', '' )
					),
					
                    // Include
                    'help_include' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Including</h3>
                            <p>Including - build individual sitemaps for your sitemap.xml index file</p>
                        ', 'psp')
                    ),

					'standard_content' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array('site', 'post', 'page', 'category', 'post_tag', 'archive', 'author'),
						'size' 		=> 'large',
						'rows_visible'	=> 8,
						'force_width'=> '300',
						'title' 	=> __('Select Standard Content', $psp->localizationName),
						'desc' 		=> __('(option for sitemap.xml index file) Choose standard content (sub-sitemaps) which you want to include in your sitemap.xml index file.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Standard Content list', $psp->localizationName),
							'right' => __('Your chosen standard content from list', $psp->localizationName),
						),
						'options' 	=> psp_standardContent_get()
					),

                    /*'post_types'  => array(
                        'type'      => 'multiselect',
                        'std'       => array('post','page'),
                        'size'      => 'small',
                        'force_width'=> '300',
                        'title'     => __('Post Types:', 'psp'),
                        'desc'      => __('Select post types which you want to include in your sitemap xml file.', 'psp'),
                        'options'   => psp_postTypes_get( 'all' )
                    ),*/
					'post_types' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array(),
						'size' 		=> 'large',
						'rows_visible'	=> 8,
						'force_width'=> '300',
						'title' 	=> __('Select Custom Post Types', $psp->localizationName),
						'desc' 		=> __('(option for sitemap.xml index file and sitemap-images.xml, sitemap-videos.xml files) Choose custom post types which you want to include in your sitemap xml file.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Custom Post Types list', $psp->localizationName),
							'right' => __('Your chosen custom post types from list', $psp->localizationName),
						),
						'options' 	=> psp_postTypes_get( false )
					),
					
					'taxonomies' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array(),
						'size' 		=> 'large',
						'rows_visible'	=> 12,
						'force_width'=> '300',
						'title' 	=> __('Select Custom Taxonomies Pages', $psp->localizationName),
						'desc' 		=> __('(option for sitemap.xml index file) Choose custom taxonomies pages which you want to include in your sitemap xml file.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Custom Taxonomies Pages list', $psp->localizationName),
							'right' => __('Your chosen custom taxonomies pages from list', $psp->localizationName),
						),
						'options' 	=> psp_taxonomies_get( false )
					),
					
                    // Exclude
                    'help_exclude' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Excluding</h3>
                            <p>Excluding - only for posts | pages | custom post types sitemaps</p>
                        ', 'psp')
                    ),
					
					'exclude_categories' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array(),
						'size' 		=> 'large',
						'rows_visible'	=> 12,
						'force_width'=> '300',
						'title' 	=> __('Select Exclude Categories', $psp->localizationName),
						'desc' 		=> __('(option for sitemap.xml index file and sitemap-posttype-[posttype].xml, sitemap-taxonomy-[taxonomy].xml files) Choose categories which you want to exclude in your sitemap xml file.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Categories list', $psp->localizationName),
							'right' => __('Your chosen exclude categories from list', $psp->localizationName),
						),
						'options' 	=> psp_categories_get()
					),
					
					'exclude_posts_ids' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'small',
						//'force_width'=> '400',
						'title' 	=> __('Exclude posts, pages, post types:', 'psp'),
						'desc' 		=> __('(option for sitemap-posttype-[posttype].xml files) Exclude posts, pages, post types ( list of IDs, separated by comma )', 'psp'),
						'height'	=> '200px',
						'width'		=> '100%'
					),
					
                    // Other settings
                    'help_other_settings' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Other settings</h3>
                            <p>Settings available for the sitemap module!</p>
                        ', 'psp')
                    ),

                    'author_roles'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array('administrator', 'editor', 'author', 'contributor', 'subscriber'),
                        'size'      => 'large',
                        'rows_visible'  => 5,
                        'force_width'=> '300',
                        'title'     => __('Select Author Roles', $psp->localizationName),
                        'desc'      => __('(option for sitemap-author.xml file) Choose author roles which you want to include in your sitemap-author.xml file.', $psp->localizationName),
                        'info'      => array(
                            'left' => __('All Author Roles list', $psp->localizationName),
                            'right' => __('Your chosen author roles from list', $psp->localizationName),
                        ),
                        'options'   => $psp->get_wp_user_roles()
                    ),
                    
                    'archive_type' => array(
                        'type'      => 'select',
                        'std'       => 'monthly',
                        'size'      => 'large',
                        'force_width'=> '120',
                        'title'     => __('Archive type: ', 'psp'),
                        'desc'      => __('(option for sitemap-archive.xml file) what type of archives you want in your sitemap-archive.xml file', 'psp'),
                        'options'   => array(
                            'monthly'   => __('monthly', 'psp'),
                            'yearly'    => __('yearly', 'psp')
                        )
                    ),
                    
                    'taxonomies_zero_posts' => array(
                        'type'      => 'select',
                        'std'       => 'no',
                        'size'      => 'large',
                        'force_width'=> '120',
                        'title'     => __('Include zero posts taxonomy terms: ', 'psp'),
                        'desc'      => __('(option for sitemap-taxonomy-[taxonomy].xml file) include taxonomy terms with zero posts', 'psp'),
                        'options'   => array(
                            'yes'   => __('YES', 'psp'),
                            'no'    => __('NO', 'psp')
                        )
                    ),
					
                    // Formatting
                    'help_formatting' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Formatting</h3>
                            <p>Settings available for the sitemap module!</p>
                        ', 'psp')
                    ),

					'priority' => array(
						'type' 		=> 'html',
						'html' 		=> psp_postTypes_priority('__tab7', '')
					),
					
					'changefreq' => array(
						'type' 		=> 'html',
						'html' 		=> psp_postTypes_changefreq('__tab7', '')
					),
					
					// Video Sitemap Settings
					'help_video_sitemap' => array(
						'type' 		=> 'message',
						'status' 	=> 'info',
						'html' 		=> __('
							<h3 style="margin: 0px 0px 5px 0px;">Video Sitemap Settings</h3>
							<p>Settings available for video sitemap!</p>
						', 'psp')
					),

					'video_title_prefix' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'Video',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Video Title Before Text: ', 'psp'),
						'desc' 		=> __('this text will be showed as prefix for video title in the schema.org content snippet (only if the text doesn\'t already exist in the title).', 'psp')
					),
					
					'video_social_force' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Social Tags Rewrite: ', 'psp'),
						'desc' 		=> __('rewrite the social meta tags (facebook) with the information from the video; if you have multiple videos in the post or page content, will use the first video found by our search algorithm and it may not be the first video in your post or page content', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'thumb_default' => array(
						'type' 			=> 'upload_image',
						'size' 			=> 'large',
						'force_width'	=> '80',
						'preview_size'	=> 'large',	
						'value' 		=> __('Upload Image', 'psp'),
						'title' 		=> __('Video Default Thumb', 'psp'),
						'desc' 			=> __('Upload your Video Default Thumb using the native media uploader', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						)
					),
					
					'video_include' 	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array_keys($__psp_video_include),
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('Select Video Providers:', 'psp'),
						'desc' 		=> __('select the video providers to include in your sitemap-videos.xml', 'psp'),
						'options' 	=> $__psp_video_include
					),
					
					'vzaar_domain' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'vzaar.com/videos',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Vzaar domain: ', 'psp'),
						'desc' 		=> __('enter vzaar domain.', 'psp')
					),
					
					'viddler_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Viddler key: ', 'psp'),
						'desc' 		=> __('enter viddler key.', 'psp')
					),
					
					'flickr_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Flickr key: ', 'psp'),
						'desc' 		=> __('enter flickr key.', 'psp')
					),
				)
			)
		)
	)
);