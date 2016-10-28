<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function __metaRobotsList() {
	return array(
		'noindex'	=> 'noindex', //support by: Google, Yahoo!, MSN / Live, Ask
		'nofollow'	=> 'nofollow', //support by: Google, Yahoo!, MSN / Live, Ask
		'noarchive'	=> 'noarchive', //support by: Google, Yahoo!, MSN / Live, Ask
		'noodp'		=> 'noodp' //support by: Google, Yahoo!, MSN / Live
	);
}
$__metaRobotsList = __metaRobotsList();


function psp_OpenGraphTypes( $istab = '', $is_subtab='' ) {
	global $psp;
	
	ob_start();

	$post_types = get_post_types(array(
		'public'   => true
	));
	//$post_types['attachment'] = __('Images', 'psp');
	//unset media - images | videos are treated as belonging to post, pages, custom post types
	unset($post_types['attachment'], $post_types['revision']);
	
	$options = $psp->get_theoption('psp_title_meta_format');
?>
<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label><?php _e('Default OpenGraph Type:', 'psp'); ?>	</label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($post_types as $key => $value){
		
		$val = '';
		if( isset($options['social_opengraph_default']) && isset($options['social_opengraph_default'][$key]) ){
			$val = $options['social_opengraph_default'][$key];
		}
		?>
		<label for="social_opengraph_default[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="social_opengraph_default[<?php echo $key;?>]" name="social_opengraph_default[<?php echo $key;?>]" style="width:120px;">
			<option value="none"><?php _e('None', 'psp'); ?></option>
			<?php
			$opengraph_defaults = array(
				'Internet' 	=> array(
					'article'				=> __('article', 'psp'),
					'blog'					=> __('Blog', 'psp'),
					'profile'				=> __('Profile', 'psp'),
					'website'				=> __('Website', 'psp')
				),
				'Products' 	=> array(
					'book'					=> __('Book', 'psp')
				),
				'Music' 	=> array(
					'music.album'			=> __('Album', 'psp'),
					'music.playlist'		=> __('Playlist', 'psp'),
					'music.radio_station'	=> __('Radio Station', 'psp'),
					'music.song'			=> __('Song', 'psp')
				),
				'Videos' => array(
					'video.movie'			=> __('Movie', 'psp'),
					'video.episode'			=> __('TV Episode', 'psp'),
					'video.tv_show'			=> __('TV Show', 'psp'),
					'video.other'			=> __('Video', 'psp')
				),
			);
			foreach ($opengraph_defaults as $k => $v){
				echo '<optgroup label="' . $k . '">';
				foreach ($v as $kk => $vv){
					echo 	'<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
				}
				echo '</optgroup>';
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

function psp_TwitterCardsTypes( $istab = '', $is_subtab='' ) {
	global $psp;
	
	ob_start();

	$post_types = get_post_types(array(
		'public'   => true
	));
	//$post_types['attachment'] = __('Images', 'psp');
	//unset media - images | videos are treated as belonging to post, pages, custom post types
	unset($post_types['attachment'], $post_types['revision']);
	
	$options = $psp->get_theoption('psp_title_meta_format');
?>
<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label><?php _e('Default Twitter Cards Type:', 'psp'); ?>	</label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
	<?php
	foreach ($post_types as $key => $value){
		
		$val = '';
		if( isset($options['psp_twc_cardstype_default']) && isset($options['psp_twc_cardstype_default'][$key]) ){
			$val = $options['psp_twc_cardstype_default'][$key];
		}
		?>
		<label for="psp_twc_cardstype_default[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="psp_twc_cardstype_default[<?php echo $key;?>]" name="psp_twc_cardstype_default[<?php echo $key;?>]" style="width:200px;">
			<option value="none"><?php _e('None', 'psp'); ?></option>
			<?php
			$opengraph_defaults = array(
				'summary'				=> __('Summary Card', 'psp'),
				'summary_large_image'		=> __('Summary Card with Large Image', 'psp'),
				'photo'					=> __('Photo Card', 'psp'),
				'gallery'				=> __('Gallery Card', 'psp'),
				'player'				=> __('Player Card', 'psp'),
				'product'				=> __('Product Card', 'psp')
			);
			foreach ($opengraph_defaults as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
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

function psp_TwitterCardsOptions( $istab = '', $is_subtab='', $type='' ) {
	global $psp;
	
	$options = $psp->get_theoption('psp_title_meta_format');

	ob_start();
?>
	<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>" id="<?php echo $type=='home' ? 'psp-twittercards-home-response' : 'psp-twittercards-app-response'; ?>" style="position:relative;"></div>
	<script>
// Initialization and events code for the app
pspTwitterCards_modoptions = (function ($) {
	"use strict";
	
	// public
	var debug_level = 0;
	var maincontainer = null;
	var loading = null;
	
	var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>',
	type = '<?php echo $type; ?>';
	
	var ajaxBox = ( type=='home' ? $('#psp-twittercards-home-response') : $('#psp-twittercards-app-response') );
	
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#psp-wrapper");
			loading = maincontainer.find("#main-loading");
	
			triggers();
		});
	})();
	
	function ajaxLoading()
	{
		var loading = $('<div id="psp-ajaxLoadingBox" class="psp-panel-widget">loading</div>');
		// append loading
		ajaxBox.html(loading);
	}
	
	function get_options( type ) {
			var __type = type || '';
			if ( $.trim(__type)=='' ) return false;
			
			ajaxLoading();

			var theTrigger = ( __type=='home' ? $('#psp_twc_home_type') : $('#psp_twc_site_app') ), theTriggerVal = theTrigger.val();
			var theResp = ajaxBox;

			if ( $.inArray(theTriggerVal, ['none', 'no']) > -1 ) {
				theResp.html('').hide();
				return false;
			}

			$.post(ajaxurl, {
				'action' 		: 'pspTwitterCards',
				'sub_action'		: 'getCardTypeOptions',
				'card_type'		: __type=='home' ? $('#psp_twc_home_type').val() : 'app',
				'page'			: __type=='home' ? 'home' : 'app'
			}, function(response) {

				if ( response.status == 'valid' ) {
					theResp.html( response.html ).show();
					pspFreamwork.makeTabs();
					
					$('#psp-twittercards-app-response').find('input#box_id, input#box_nonce').remove();
					$('#psp-twittercards-home-response').find('input#box_id, input#box_nonce').remove();
					return true;
				}
				return false;
			}, 'json');		
	}
	
	// triggers
	function triggers()
	{
		get_options( type );

		if ( type=='home' ) {
			$('#psp_twc_home_type').on('change', function (e) {
				e.preventDefault();
	
				get_options( type );
			});
		} else {
			$('#psp_twc_site_app').on('change', function (e) {
				e.preventDefault();
	
				get_options( type );
			});
		}
	}
	
	// external usage
	return {
	}
})(jQuery);
	</script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function psp_TwitterCardsImageFind($istab = '', $is_subtab='') {
	global $psp;
	
	ob_start();

	$options = $psp->get_theoption('psp_title_meta_format');
	$val = '';
	if ( isset($options['psp_twc_image_find']) ) {
		$val = $options['psp_twc_image_find'];
	}
?>
<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label><?php _e('How to choose Image:', 'psp'); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>
		<select id="psp_twc_image_find" name="psp_twc_image_find" style="width:350px;">
			<?php
			$image_find = array(
				'content'				=> __('Choose first image from the post | page content', 'psp'),
				'featured'				=> __('Featured image for the post | page', 'psp'),
				'customfield'				=> __('Choose a custom field for image', 'psp')
			);
			foreach ($image_find as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
			}
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
	</div>
	<div class="psp-form-item small" style="margin-top:5px;">
		<span class=""><?php echo __('Choose custom field:', 'psp'); ?></span>&nbsp;
		<input id="psp_twc_image_customfield" type="text" value="" name="psp_twc_image_customfield">
	</div>
</div>
	<script>
// Initialization and events code for the app
pspTwitterCards_image_find = (function ($) {
	"use strict";
	
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			triggers();
		});
	})();
	
	function custom_field(val) {
		var cf = $('#psp_twc_image_customfield'), cfp = cf.parent();
		
		if ( val =='customfield' ) {
			cfp.show();
		} else {
			cfp.hide();
		}
	}
	
	// triggers
	function triggers()
	{
		custom_field( $('#psp_twc_image_find').val() );
		
		$('#psp_twc_image_find').on('change', function (e) {
			e.preventDefault();
	
			custom_field( $(this).val() );
		});
	}
	
	// external usage
	return {
	}
})(jQuery);
	</script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

global $psp;
if ( $psp->is_buddypress() ) {
require_once('buddypress.options.php');
}
//echo json_encode(
$__psp_mfo = 
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'title_meta_format' => array(
				'title' 	=> __('Title & Meta Formats', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> false, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// tabs
				'tabs'	=> array(
					'__tab1'	=> array(__('Format Tags List', 'psp'), 'help_format_tags'),
					'__tab2'	=> array(__('Title Format', 'psp'), 'home_title,post_title,page_title,product_title,category_title,tag_title,taxonomy_title,archive_title,author_title,search_title,404_title,pagination_title,use_pagination_title'),
					'__tab3'	=> array(__('Meta Description', 'psp'), 'home_desc,post_desc,page_desc,product_desc,category_desc,tag_desc,taxonomy_desc,archive_desc,author_desc,pagination_desc,use_pagination_desc'),
					'__tab4'	=> array(__('Meta Keywords', 'psp'), 'home_kw,post_kw,page_kw,product_kw,category_kw,tag_kw,taxonomy_kw,archive_kw,author_kw,pagination_kw,use_pagination_kw'),
					'__tab5'	=> array(__('Meta Robots', 'psp'), 'home_robots,post_robots,page_robots,product_robots,category_robots,tag_robots,taxonomy_robots,archive_robots,author_robots,search_robots,404_robots,pagination_robots,use_pagination_robots'),
					'__tab6'	=> array(__('Social Meta', 'psp'), 'social_use_meta,social_include_extra,social_validation_type,social_site_title,social_default_img,social_home_title,social_home_desc,social_home_img,social_home_type,social_opengraph_default'),
					'__tab7'	=> array(__('Twitter Cards', 'psp'), 'psp_twc_use_meta,psp_twc_website_account,psp_twc_website_account_id,psp_twc_creator_account,psp_twc_creator_account_id,psp_twc_default_img,psp_twc_cardstype_default,psp_twc_home_app,psp_twc_home_type,psp_twc_site_app,help_psp_twc_post,help_psp_twc_home,help_psp_twc_app,psp_twc_image_find,psp_twc_thumb_sizes,psp_twc_thumb_crop')
				),
				
				// tabs
				'subtabs'	=> array(
					'__tab1'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'help_format_tags')),
					'__tab2'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_title,post_title,page_title,category_title,tag_title,taxonomy_title,archive_title,author_title,search_title,404_title,pagination_title,use_pagination_title')),
					'__tab3'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_desc,post_desc,page_desc,category_desc,tag_desc,taxonomy_desc,archive_desc,author_desc,pagination_desc,use_pagination_desc')),
					'__tab4'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_kw,post_kw,page_kw,category_kw,tag_kw,taxonomy_kw,archive_kw,author_kw,pagination_kw,use_pagination_kw')),
					'__tab5'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_robots,post_robots,page_robots,category_robots,tag_robots,taxonomy_robots,archive_robots,author_robots,search_robots,404_robots,pagination_robots,use_pagination_robots')),
					'__tab6'	=> array(
						'__subtab1' => array(
							__('General', 'psp'), 'social_use_meta,social_include_extra,social_validation_type,social_site_title,social_default_img,social_opengraph_default'),
						'__subtab2' => array(
							__('Homepage', 'psp'), 'social_home_title,social_home_desc,social_home_img,social_home_type')),
					'__tab7'	=> array(
						'__subtab1' => array(
							__('General', 'psp'), 'psp_twc_use_meta,psp_twc_website_account,psp_twc_website_account_id,psp_twc_creator_account,psp_twc_creator_account_id,psp_twc_default_img'),
						'__subtab2' => array(
							__('Posts, Pages', 'psp'), 'psp_twc_cardstype_default,help_psp_twc_post,psp_twc_image_find,psp_twc_thumb_sizes,psp_twc_thumb_crop'),
						'__subtab3' => array(
							__('Generic App Twitter Card', 'psp'), 'psp_twc_site_app,help_psp_twc_app'),
						'__subtab4' => array(
							__('Homepage', 'psp'), 'psp_twc_home_app,psp_twc_home_type,help_psp_twc_home'))
				),
				
				// create the box elements array
				'elements'	=> array(

					//=============================================================
					//== help
					'help_format_tags' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Basic Setup</h2>
							<p>You can set the custom page title using defined formats tags.</p>
							<h3>Available Format Tags</h3>
							<ul>
								<li><code>{site_title}</code> : the website\'s title (global availability)</li>
								<li><code>{site_description}</code> : the website\'s description (global availability)</li>
								<li><code>{current_date}</code> : current date (global availability)</li>
								<li><code>{current_time}</code> : current time (global availability)</li>
								<li><code>{current_day}</code> : current day (global availability)</li>
								<li><code>{current_year}</code> : current year (global availability)</li>
								<li><code>{current_month}</code> : current month (global availability)</li>
								<li><code>{current_week_day}</code> : current day of the week (global availability)</li>
								
								
								<li><code>{title}</code> : the page|post title (global availability)</li>
								<li><code>{id}</code> : the page|post id (specific availability)</li>
								<li><code>{date}</code> : the page|post date (specific availability)</li>
								<li><code>{description}</code> - the page|post full description (specific availability)</li>
								<li><code>{short_description}</code> - the page|post excerpt or if excerpt does not exist, 200 character maximum are retrieved from description (specific availability)</li>
								<li><code>{parent}</code> - the page|post parent title (specific availability)</li>
								<li><code>{author}</code> - the page|post author name (specific availability)</li>
								<li><code>{author_username}</code> - the page|post author username (specific availability)</li>
								<li><code>{author_nickname}</code> - the page|post author nickname (specific availability)</li>
								<li><code>{author_description}</code> - the page|post author biographical Info (specific availability)</li>
								<li><code>{categories}</code> : the post categories names list separated by comma (specific availability)</li>
								<li><code>{tags}</code> : the post tags names list separated by comma (specific availability)</li>
								<li><code>{terms}</code> : the post custom taxonomies terms names list separated by comma (specific availability)</li>
								<li><code>{category}</code> - the category name or the post first found category name (specific availability)</li>
								<li><code>{category_description}</code> - the category description or the post first found category description (specific availability)</li>
								<li><code>{tag}</code> - the tag name or the post first found tag name (specific availability)</li>
								<li><code>{tag_description}</code> - the tag description or the post first found tag description (specific availability)</li>
								<li><code>{term}</code> - the term name or the post first found custom taxonomy term name (specific availability)</li>
								<li><code>{term_description}</code> - the term description or the post first found custom taxonomy term description (specific availability)</li>
								<li><code>{search_keyword}</code> : the word(s) used for search (specific availability)</li>
								<li><code>{keywords}</code> : the post|page keywords already defined (specific availability)</li>
								<li><code>{focus_keywords}</code> : the post|page focus keywords already defined (specific availability)</li>
								<li><code>{totalpages}</code> - the total number of pages (if pagination is used), default value is 1 (specific availability)</li>
								<li><code>{pagenumber}</code> - the page number (if pagination is used), default value is 1 (specific availability)</li>
							</ul><br />
							', 'psp')
					),

// 							<p>Info: when use {keywords}, if for a specific post|page {focus_keywords} is found then it is used, otherwise {keywords} remains active</p>

					//=============================================================
					//== title format
					'home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_title'			=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'page_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'product_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'category_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {category} {category_description}'
					),
					'tag_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {tag} {tag_description}'
					),
					'taxonomy_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Custom Taxonomy Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {term} {term_description}'
					),
					'archive_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} ' . __('Archives', 'psp') . ' | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {date} ' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {author} {author_username} {author_nickname}'
					),
					'search_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> __('Search for ', 'psp') . '{search_keyword} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Search Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {search_keyword}'
					),
					'404_title'		=> array(
						'type' 		=> 'text',
						'std' 		=> __('404 Page Not Found |', 'psp') . ' {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('404 Page Not Found Title Format;', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'pagination_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} ' . __('- Page', 'psp') . ' {pagenumber} ' . __('of', 'psp') . ' {totalpages} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination Title Format:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {totalpages} {pagenumber}'
					),
					'use_pagination_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Title Format in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					//=============================================================
					//== meta description
					'home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_desc'			=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'page_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'product_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'category_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{category_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {category} {category_description}'
					),
					'tag_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{tag_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {tag} {tag_description}'
					),
					'taxonomy_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{term_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Custom Taxonomy Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {term} {term_description}'
					),
					'archive_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {date} ' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {author} {author_username} {author_nickname} {author_description}'
					),
					'pagination_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> __('Page {pagenumber}', 'psp'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination Meta Description:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {totalpages} {pagenumber}'
					),
					'use_pagination_desc' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Description in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					//=============================================================
					//== meta keywords
					'home_kw' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_kw'			=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'page_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'product_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}'
					),
					'category_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {category} {category_description}'
					),
					'tag_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {tag} {tag_description}'
					),
					'taxonomy_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Custom Taxonomy Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {term} {term_description}'
					),
					'archive_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {date} ' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {author} {author_username} {author_nickname}'
					),
					'pagination_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination Meta Keywords:', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:', 'psp') . ' {totalpages} {pagenumber}'
					),
					'use_pagination_kw' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Keywords in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					//=============================================================
					//== meta robots
					'home_robots' 	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Homepage Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'post_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Post Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'page_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Page Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'product_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Product Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'category_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Category Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'tag_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Tag Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'taxonomy_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('custom Taxonomy Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'archive_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Archives Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'author_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Author Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'search_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Search Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'404_robots'		=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('404 Page Not Found Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'pagination_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Pagination Meta Robots:', 'psp'),
						'desc' 		=> __('if you do not select "noindex" => "index" is by default active; if you do not select "nofollow" => "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'use_pagination_robots' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Robots in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					//=============================================================
					//== social tags
					
					'social_use_meta' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Social Meta Tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Facebook Open Graph Social Meta Tags in all your pages! If you choose No, you can still activate tags for a post or page in it\'s meta box.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'social_include_extra' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Include extra tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to include the following &lt;article:published_time&gt;, &lt;article:modified_time&gt;, &lt;article:author&gt; tags for your posts.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'social_validation_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Code Validation Type:', 'psp'),
						'desc' 		=> '',
						'options'	=> array(
							'opengraph' 	=> 'opengraph',
							'xhtml' 		=> 'xhtml',
							'html5'			=> 'html5'
						)
					),
					'social_site_title' => array(
						'type' 		=> 'text',
						'std' 		=> get_bloginfo('name'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Site Name:', 'psp'),
						'desc' 		=> __('&nbsp;', 'psp')
					),
					'social_default_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Default Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> __('Here you can specify an image URL or an image from your media library to use as a default image in the event that there is no image otherwise specified for a given webpage on your site.', 'psp'),
					),
					
					'social_home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> get_bloginfo('name'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Title:', 'psp'),
						'desc' 		=> '&nbsp;'
					),
					'social_home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> get_bloginfo('description'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Homepage Description:', 'psp'),
						'desc' 		=> '&nbsp;'
					),
					'social_home_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Homepage Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> '&nbsp;',
					),
					'social_home_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Homepage OpenGraph Type:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'blog'					=> __('Blog', 'psp'),
							'profile'				=> __('Profile', 'psp'),
							'website'				=> __('Website', 'psp')
						)
					),
					
					'social_opengraph_default' => array(
						'type' 		=> 'html',
						'html' 		=> psp_OpenGraphTypes( '__tab6', '__subtab1' )
					),
					
					//=============================================================
					//== twitter cards
					
					'psp_twc_use_meta' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Twitter Cards Meta Tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Twitter Cards Meta Tags in all your pages! If you choose No, you can still activate tags for a post or page in it\'s meta box.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),

					'psp_twc_website_account' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Website Twitter Account:', 'psp'),
						'desc' 		=> '(optional) <twitter:site> @username for the website used in the card footer.'
					),
					
					'psp_twc_website_account_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Website Twitter Account Id:', 'psp'),
						'desc' 		=> '(optional) <twitter:site:id> the website\'s Twitter user ID instead of @username. Note that user ids never change, while @usernames can be changed by the user.'
					),
					
					'psp_twc_creator_account' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Content Creator Twitter Account:', 'psp'),
						'desc' 		=> '(optional) <twitter:creator> @username for the content creator / author.'
					),
					
					'psp_twc_creator_account_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Content Creator Twitter Account Id:', 'psp'),
						'desc' 		=> '(optional) <twitter:creator:id> the Twitter user\'s ID for the content creator / author instead of @username.'
					),
					
					'psp_twc_default_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Default Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> __('Here you can specify an image URL or an image from your media library to use as a default image in the event that there is no image otherwise specified for a given webpage on your site.', 'psp'),
					),
					
					'help_psp_twc_post' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Posts | Page info - section</h2>
							<ul>
								<li>- For the following twitter card types (<strong>Summary Card, Summary Card with Large Image, Photo Card</strong>), if you don\'t complete the Title, Description, Image fields from the Post | Page Seo Setting box / Twitter Cards tab, they will be auto filled with information from the post or page title, content, image.</li>
								<li>- For the following twitter card types (<strong>Gallery Card, Player Card, Product Card</strong>), you need to complete the mandatory fields for the card type per every post or page which you want to be relationated with twitter - these fields cannot be auto filled.</li>
							</ul><br />
							', 'psp')
					),
					
					'psp_twc_cardstype_default' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsTypes( '__tab7', '__subtab2' )
					),
					
					'psp_twc_image_find' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsImageFind( '__tab7', '__subtab2' )
					),
					
					'psp_twc_thumb_sizes' => array(
						'type' 		=> 'select',
						'std' 		=> '120x120',
						'size' 		=> 'large',
						'force_width'  => '450',
						'title' 		=> __('Image Thumbnails sizes:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'none'		=> __('Don\'t make a thumbnail from the image', 'psp'),
							'435x375' => __('Web: height is 375px, width is 435px', 'psp'),
							'280x375' => __('Mobile (non-retina displays): height is 375px, width is 280px', 'psp'),
							'560x750' => __('Mobile (retina displays): height is 750px, width is 560px', 'psp'),
							'280x150' => __('Small: height is 150px, width is 280px', 'psp'),
							'120x120' => __('Smallest: height is 120px, width is 120px', 'psp')
						)
					),
					
					'psp_twc_thumb_crop' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'	=> '120',
						'title' 	=> __('Force Crop on card type Image?', 'psp'),
						'desc' 		=> __('Choose Yes if you want to force crop on your twitter card type chosen image.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'help_psp_twc_app' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Website Generic App Twitter Card Type - section</h2>
							<ul>
								<li>Choose if you want to add an app twitter card type to your website.</li>
							</ul><br />
							', 'psp')
					),
					
					'psp_twc_site_app' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Add Twitter App Card Type: ', 'psp'),
						'desc' 		=> __('Add Twitter App Card Type', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'psp_twc_site_app_options' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsOptions( '__tab7', '__subtab3', 'app' )
					),
					
					'help_psp_twc_home' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Homepage - section</h2>
							<ul>
								<li>- Choose the twitter card type for your homepage.</li>
								<li>- Also choose if you want to also add an app twitter card type for the homepage (the options from the above App Twitter Card Type section will be used)</li>
							</ul><br />
							', 'psp')
					),
					
					'psp_twc_home_app' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Homepage Add Twitter App Card Type: ', 'psp'),
						'desc' 		=> __('Add Twitter App Card Type to Homepage', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'psp_twc_home_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'none',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Homepage Twitter Card Type:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'none'					=> __('None', 'psp'),
							'summary'				=> __('Summary Card', 'psp'),
							'summary_large_image'		=> __('Summary Card with Large Image', 'psp'),
							'photo'					=> __('Photo Card', 'psp'),
							'gallery'				=> __('Gallery Card', 'psp'),
							'player'				=> __('Player Card', 'psp'),
							'product'				=> __('Product Card', 'psp')
						)
					),
					
					'psp_twc_home_options' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsOptions( '__tab7', '__subtab4', 'home' )
					),
					
					/*'psp_twc_home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> get_bloginfo('name'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Title:', 'psp'),
						'desc' 		=> 'Title should be concise and will be truncated at 70 characters.'
					),
					'psp_twc_home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> get_bloginfo('description'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Description:', 'psp'),
						'desc' 		=> 'A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. Description text will be truncated at the word to 200 characters.'
					),
					'psp_twc_home_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Homepage Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> 'Image must be less than 1MB in size.',
					),*/

				)
			)
		)
	)
//)
;

if ( $psp->is_buddypress() ) {
	
	// tabs
	if ( isset($__psp_mfo_bp['tabs']) && !empty($__psp_mfo_bp['tabs']) ) {
		foreach ( $__psp_mfo_bp['tabs'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				if ( count($val) == 1 ) {
					$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['tabs']["$key"][1] .= $val[0];					
				}
			}
		}
	}

	// subtabs
	if ( isset($__psp_mfo_bp['subtabs']) && !empty($__psp_mfo_bp['subtabs']) ) {
		foreach ( $__psp_mfo_bp['subtabs'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				foreach ( $val as $key2 => $val2 ) {
  
					$__is_tab = (bool) ( isset($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]) && !empty($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]) );
					$__is_subtab = (bool) ( isset($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"]) && !empty($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"]) );
 					
					if ( !$__is_subtab ) {
						if ( !$__is_tab )
							$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"] = array();

						$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"] = $val2;
					} else {
						$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"] = $val2;
					}
				}
			}
		}
	}
  
	// elements
	if ( isset($__psp_mfo_bp['elements']) && !empty($__psp_mfo_bp['elements']) ) {
		foreach ( $__psp_mfo_bp['elements'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['elements']["$key"] = $val;
			}
		}
	}
}

echo json_encode(
	$__psp_mfo
);