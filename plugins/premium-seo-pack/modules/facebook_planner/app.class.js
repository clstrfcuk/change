/*
Document   :  On Page Optimization
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspFacebookPage = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var IDs = [];
    var loaded_page = 0;
    
    var maincontainer_tasks = null;
    var mainloading_tasks = null;
    
    var langmsg = {};

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#psp-wrapper #psp_facebook_share-options");
			loading = maincontainer.find("#main-loading");
			
			maincontainer_tasks = $("#psp-wrapper");
			mainloading_tasks = maincontainer_tasks.find("#psp-main-loading");

			triggers();
		});
	})();

	function fixMetaBoxLayout()
	{
		//meta boxes
		var meta_box 		= $(".psp-meta-box-container .psp-seo-status-container"),
			meta_box_width 	= $(".psp-meta-box-container").width() - 100,
			row				= meta_box.find(".psp-seo-rule-row");

		row.width(meta_box_width - 40);
		row.find(".right-col").width( meta_box_width - 180 );
		row.find(".message-box").width(meta_box_width - 45);
		row.find(".right-col .message-box").width( meta_box_width - 180 );


		$("#psp_facebook_share-options #psp-meta-box-preload").hide();
		$("#psp_facebook_share-options .psp-meta-box-container").fadeIn('fast');

		$("#psp_facebook_share-options").on('click', '.psp-tab-menu a', function(e){
			e.preventDefault();

			var that 	= $(this),
				open 	= $("#psp_facebook_share-options .psp-tab-menu a.open"),
				href 	= that.attr('href').replace('#', '');

			$("#psp_facebook_share-options .psp-meta-box-container").hide();

			$("#psp_facebook_share-options #psp-tab-div-id-" + href ).show();

			// close current opened tab
			var rel_open = open.attr('href').replace('#', '');

			$("#psp_facebook_share-options #psp-tab-div-id-" + rel_open ).hide();

			$("#psp_facebook_share-options #psp-meta-box-preload").show();

			$("#psp_facebook_share-options #psp-meta-box-preload").hide();
			$("#psp_facebook_share-options .psp-meta-box-container").fadeIn('fast');

			open.removeClass('open');
			that.addClass('open');
		});
	}
	
	function fb_planner_post(atts) {

		var fb_planner_post = {

			init: function(atts) {
				this.atts = $.extend(this.atts, atts);
				this.trigger();
			},

			autocomplete_fields: function() {
				var self = this;

				var titleValue = jQuery('#titlewrap').find('input#title').val(),
				imageValue = jQuery('#psp_wplannerfb_image').val(),
				featuredImg = jQuery('a#set-post-thumbnail').find('img.attachment-post-thumbnail').attr('src');

				if(tinymce.activeEditor) {
					if(!tinymce.activeEditor.isHidden()) {
						tinymce.activeEditor.save();
					}
				}

				var descValue = jQuery('#content').val();
				descValue = descValue.replace(/(<([^>]+)>)/ig,""); // remove <> codes
				descValue = descValue.replace(/(\[([^\]]+)\])/ig,""); // remove [] shortcodes
				descValue = descValue.replace(/(\s\s+)/ig,""); // remove multiple spaces
				descValue = descValue.substr(0, 10000);

				//if( titleValue != jQuery('#psp_wplannerfb_title').val() ) {
				if( jQuery.trim( jQuery('#psp_wplannerfb_title').val() ) == '' )
					jQuery('#psp_wplannerfb_title').val( titleValue );
				//if( jQuery.trim( jQuery('#psp_wplannerfb_caption').val() ) == '' )
				//	jQuery('#psp_wplannerfb_caption').val( titleValue );				

				//if( descValue != jQuery('#psp_wplannerfb_description').val() ) {
				if( jQuery.trim( jQuery('#psp_wplannerfb_description').val() ) == '' )
					jQuery('#psp_wplannerfb_description').val( descValue );
			},

			defaultValues: function() {
				if ( jQuery('#psp_wplannerfb_permalink_value').val() != '' ) {
					jQuery('#psp_wplannerfb_permalink_value').show();
				} else {
					jQuery('#psp_wplannerfb_permalink_value').hide();
				}
			},

			trigger: function() {
				var self = this;
				jQuery('#psp-wplannerfb-auto-complete').click(function() {
					self.autocomplete_fields();
				});
				self.defaultValues();
			}
		};
		
		fb_planner_post.init(atts);

		atts.action = atts.action || '';
		if ( atts.action != '' ) {
			if ( atts.action == 'autocomplete' )
				fb_planner_post.autocomplete_fields();
		}
	}
	
	function fb_postnow(atts) {
		
		var atts = atts;

		var postNowBtn = jQuery('#psp_post_planner_postNowFBbtn');
		postNowBtn.click(function() {
			// Auto-Complete fields with data from above (title, permalink, content) if empty
			if( jQuery('#psp_wplannerfb_title').val() == '' ||
				//jQuery('#psp_wplannerfb_permalink').val() == '' ||
				jQuery('#psp_wplannerfb_description').val() == ''
			) {
				var c = confirm(langmsg.mandatory);

				if(c == true) {
					fb_planner_post({'action': 'autocomplete'});
				}else{
					alert(langmsg.publish_cancel);
					return false;
				}
			}


			var postTo = '',
			postMe = jQuery('#psp_wplannerfb_now_post_to_me'),
			postPageGroup = jQuery('#psp_wplannerfb_now_post_to_page'),
			postTOFbNow = jQuery('#psp_postTOFbNow');

			postTOFbNow.show();
			postNowBtn.hide();

			var postToProfile = '';
			var postToPageGroup = '';
			if( postMe.attr('checked') == 'checked' ) {
				postToProfile = 'on';
			}
			if( postPageGroup.attr('checked') == 'checked' ) {
				postToPageGroup = jQuery('#psp_wplannerfb_now_post_to_page_group').val();
			}

			var data = {
				action: 'psp_publish_fb_now',
				postId: atts.post_id,
				postTo: {'profile' : postToProfile, 'page_group' : postToPageGroup},
				privacy: jQuery('#psp_wplannerfb_now_post_privacy').val(),
				psp_wplannerfb_message: jQuery('#psp_wplannerfb_message').val(),
				psp_wplannerfb_title: jQuery('#psp_wplannerfb_title').val(),
				psp_wplannerfb_permalink: jQuery("input[name=psp_wplannerfb_permalink]:checked").val(),
				psp_wplannerfb_permalink_value: jQuery('#psp_wplannerfb_permalink_value').val(),
				psp_wplannerfb_caption: jQuery('#psp_wplannerfb_caption').val(),
				psp_wplannerfb_description: jQuery('#psp_wplannerfb_description').val(),
				psp_wplannerfb_image: jQuery('input[name=psp_wplannerfb_image]').val(),
				psp_wplannerfb_useimage: jQuery('select[name=psp_wplannerfb_useimage]').val()
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if(jQuery.trim(response) == 'OK'){
					postTOFbNow.hide();
					alert( langmsg.publish_success );
					postNowBtn.show();
				}else{
					alert( langmsg.publish_error );
					postNowBtn.show();
				}
			});
			return false;
		});
	}
	
	function fb_scheduler( atts ) {
		
		var atts = atts;
		
		// Check for mandatory empty fields AND Auto-Complete fields with data from post/page (title, permalink, content) if empty
		jQuery('body').on('click', '#psp_wplannerfb_date_hour', function() {

			if( jQuery('#psp_wplannerfb_title').val() == '' ||
			//jQuery('#psp_wplannerfb_permalink').val() == '' ||
			jQuery('#psp_wplannerfb_description').val() == '')
			{
				fb_planner_post({'action': 'autocomplete'});
				alert(langmsg.mandatory2);
			}
		});

		// Auto-Check repeat interval input
		var $repeating_interval = jQuery('#psp_wplannerfb_repeating_interval');
		$repeating_interval.keyup(function(){
			$t = jQuery(this),
			val = $t.val();

			if(val != parseInt(val) || parseInt(val) < 1){
				$t.val(parseInt(val));
			}
		})
		
	}
	
	function delete_bulk_rows() {
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
			alert('You didn\'t select any rows!');
			return false;
		}
		
		mainloading_tasks.fadeIn('fast');

		jQuery.post(ajaxurl, {
			'action' 		: 'psp_do_bulk_delete_rows',
			'id'			: ids,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading_tasks.fadeOut('fast');				
				//refresh page!
				window.location.reload();
				return false;
			}
			mainloading_tasks.fadeOut('fast');
			alert('Problems occured while trying to delete the selected rows!');
		}, 'json');
	}
	
	function triggers()
	{
		fixMetaBoxLayout();
		
		maincontainer_tasks.on('click', '#psp-do_bulk_delete_facebook_planner_rows', function(e){
			e.preventDefault();

			if (confirm('Are you sure you want to delete the selected rows?'))
				delete_bulk_rows();
		});
	}
	
	function setLangMsg( atts ) {
		langmsg = $.extend(langmsg, atts);
	}
	
	// external usage
	return {
		'setLangMsg'		: setLangMsg,
		'fb_planner_post'	: fb_planner_post,
		'fb_scheduler'		: fb_scheduler,
		'fb_postnow'		: fb_postnow

    }
})(jQuery);