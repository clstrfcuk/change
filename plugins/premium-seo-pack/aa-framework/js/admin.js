/*
    Document   :  aaFreamwork
    Created on :  August, 2013
    Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspFreamwork = (function ($) {
    "use strict";

	var t 			= null,
		ajaxBox 	= null,
		section		= 'dashboard',
		subsection	= '',
		sub_istab   = '',
		in_loading_section = null,
		topMenu 	= null;
		
	var upload_popup_parent = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
 
			t 			= $("div.wrapper-psp" ),
			ajaxBox 	= t.find('#psp-ajax-response'),
			topMenu 	= t.find('#psp-topMenu');
 
	        // plugin depedencies if default!
	        if ( $("li#psp-nav-depedencies").length > 0 ) {
	        	section = 'depedencies';
	        }

			triggers();
			fixLayoutHeight();
		});
	})();
	
	/*
	function addPreviewFooter()
	{
		var box = $("<div />");
		
		box.css({
			'position': 'absolute',
			'left': '0px',
			'top': ($(document).height() - 113) + 'px',
			'width': '100%',
			'height': '113px',
			'border-top': '2px solid #e87124',
			'background': "#fff",
			'z-index': 9999999
		});
		
		var logo = $("<img src='http://dev.premiumseopack.com/wp-content/plugins/premium-seo-pack/thumb_full.png' />");
		box.css({
			'position': 'absolute',
			'left': '30px',
			'top': '-30px'
		});
		
		box.append( logo );
		
		$("body").append(box);
	}*/
	
	function ajaxLoading(status)
	{
		var loading = $('<div id="psp-ajaxLoadingBox" class="psp-panel-widget">loading</div>');
		// append loading
		ajaxBox.html(loading);
	}
	
	function makeRequest( callback )
	{
		// fix for duble loading of js function
		if( in_loading_section == section ){
			return false;
		}
		in_loading_section = section;

		// do not exect the request if we are not into our ajax request pages
		if( ajaxBox.size() == 0 ) return false;

		ajaxLoading();
		var data = {
			'action' 		: 'pspLoadSection',
			'section' 		: section,
			'subsection'	: subsection
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'ok'){
				$("h1.psp-section-headline").html(response.headline);
				ajaxBox.html(response.html);
				
				makeTabs();
				makeActiveMenu();
				
				if( typeof pspDashboard != "undefined" ){
					pspDashboard.init();
				}
				
				// find new open
                var new_open = topMenu.find('li#psp-sub-nav-' + section + (subsection != '' && sub_istab == '' ? '--' + subsection : ''));
                var in_submenu = new_open.parent('.psp-sub-menu');
                
                // close current open menu
                var current_open = topMenu.find(">li.active");
                if( current_open != in_submenu.parent('li') ){
					current_open.find(".psp-sub-menu").slideUp(250);
					current_open.removeClass("active");
				}
				
				// open current menu
				in_submenu.find('.active').removeClass('active');
				new_open.addClass('active');
				
				// check if is into a submenu
				if( in_submenu.size() > 0 ){
					if( !in_submenu.parent('li').hasClass('active') ){
						in_submenu.slideDown(100);
					}
					in_submenu.parent('li').addClass('active');
				}
				
				if( section == 'dashboard' ){
					topMenu.find(".psp-sub-menu").slideUp(250);
					topMenu.find('.active').removeClass('active');
					
					topMenu.find('li#psp-nav-' + section).addClass('active');
				}
				
				// callback - subsection!
				if ( $.isArray(callback) && callback.length == 2 && $.isFunction( callback[0] ) )
					callback[0]( callback[1] );
					
				multiselect_left2right();
			}
		}, 'json');
	}

	function importSEOData($btn)
	{
		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');
		// replace the save button value with loading message
		$btn.val('import settings ...').removeClass('blue').addClass('gray');
		if(theForm.length > 0) {
			
			// serialiaze the form and send to saving data
			var data_nb = {
				'action' 	: 'pspimportSEOData',
				'options' 	: theForm.serialize(),
				'from'		: theForm.find('#from').val(),
				'subaction' : 'nbres'
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data_nb, function(response) {

				var nbrows = parseInt( response.nbrows );

				if(response.status == 'valid' && nbrows > 0 ) {
					
					statusBoxHtml.removeClass('psp-error').addClass('psp-success').html(response.html).fadeIn();
					
					importSEOData_loop($btn, 0, nbrows);
				} else {

					statusBoxHtml.delay(10000).fadeOut();
				}
			}, 'json');
		}
	}
	
	function importSEOData_loop($btn, step, nbrows) {

		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');

		if ( nbrows <= step ) {
		
			statusBoxHtml.delay(3000).fadeOut();

			// replace the save button value with default message
			$btn.val( value ).removeClass('gray').addClass('blue');

			setTimeout(function(){
				window.location.reload();
			}, 3000);
			return true;
		}

		// serialiaze the form and send to saving data
		var data = {
			'action' 	: 'pspimportSEOData',
			'options' 	: theForm.serialize(),
			'from'		: theForm.find('#from').val(),
			'step'		: step,
			'rowsperstep'	: 10
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {

			var __oldResHtml = statusBoxHtml.html(),
				__newResHtml = __oldResHtml + '<br />' + response.html;
			if(response.status == 'valid'){
				statusBoxHtml.removeClass('psp-error').addClass('psp-success').html(__newResHtml).fadeIn();
			}else{
				statusBoxHtml.removeClass('psp-success').addClass('psp-error').html(__newResHtml).fadeIn();
			}
				
			importSEOData_loop($btn, step + 10, nbrows);
		}, 'json');
	}
	
	function installDefaultOptions($btn)
	{
		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');
		// replace the save button value with loading message
		$btn.val('installing default settings ...').removeClass('blue').addClass('gray');
		if(theForm.length > 0) {
			// serialiaze the form and send to saving data
			var data = {
				'action' 	: 'pspInstallDefaultOptions',
				'options' 	: theForm.serialize()
			};
			
            /*$.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: JSON.stringify( data ),
                    contentType: "application/json; charset=utf-8",
                    dataType: 'json',
                    processData: false, // this is true by default
                    success: function(response) {
                        if(response.status == 'ok'){
                            statusBoxHtml.addClass('psp-success').html(response.html).fadeIn().delay(3000).fadeOut();
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000);
                        }else{
                            statusBoxHtml.addClass('psp-error').html(response.html).fadeIn().delay(13000).fadeOut();
                        }
                        // replace the save button value with default message
                        $btn.val( value ).removeClass('gray').addClass('blue');
                    }
            });*/
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if(response.status == 'ok'){
					statusBoxHtml.addClass('psp-success').html(response.html).fadeIn().delay(3000).fadeOut();
					setTimeout(function(){
						window.location.reload();
					}, 1000);
				}else{
					statusBoxHtml.addClass('psp-error').html(response.html).fadeIn().delay(13000).fadeOut();
				}
				// replace the save button value with default message
				$btn.val( value ).removeClass('gray').addClass('blue');
			}, 'json');
		}
	}
	
	function saveOptions($btn)
	{
		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div#psp-status-box');
		// replace the save button value with loading message
		$btn.val('saving setings ...').removeClass('green').addClass('gray');

		multiselect_left2right(true);

		if(theForm.length > 0) {
			// serialiaze the form and send to saving data
			var data = {
				'action' 		: 'pspSaveOptions',
				'options' 		: theForm.serialize(),
				'opt_nosave'	: ['last_status', 'profile_last_status']
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if(response.status == 'ok'){
					
					statusBoxHtml.addClass('psp-success').html(response.html).fadeIn().delay(3000).fadeOut();
					if(section == 'synchronization'){
						updateCron();
					}
					
					// special cases! - local seo
					if(section == 'local_seo'){ // refresh to view the saved slug!
						window.location.reload();
					}
				}
				// replace the save button value with default message
				$btn.val( value ).removeClass('gray').addClass('green');
			}, 'json');
		}
	}
	
	function moduleChangeStatus($btn)
	{
		var value = $btn.text(),
			the_status = $btn.hasClass('activate') ? 'true' : 'false';
		// replace the save button value with loading message
		$btn.text('saving setings ...');
		var data = {
			'action' 		: 'pspModuleChangeStatus',
			'module' 		: $btn.attr('rel'),
			'the_status' 	: the_status
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'ok'){
				window.location.reload();
			}
		}, 'json');
	}
	
	function updateCron()
	{
		var data = {
			'action' 		: 'pspSyncUpdate'
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {}, 'json');
	}
	
	function fixLayoutHeight()
	{
		var win 			= $(window),
			pspWrapper 	= $("#psp-wrapper"),
			minusHeight 	= 70,
			winHeight		= win.height();
		// show the freamwork wrapper and fix the height
		pspWrapper.css('height', parseInt(winHeight - minusHeight)).show();
		$("div#psp-ajax-response").css('min-height', parseInt(winHeight - minusHeight - 240)).show();

		$("#wpbody-content").css('padding-bottom', '40px');
		$("#wpfooter").css('border', 'none');
	}
	
	function activatePlugin( $that )
	{
		var requestData = {
			'ipc'	: $('#productKey').val(),
			'email'	: $('#yourEmail').val()
		};
		if(requestData.ipc == ""){
			alert('Please type your Item Purchase Code!');
			return false;
		}
		$that.replaceWith('Validating your IPC <em>( ' + ( requestData.ipc) + ' )</em>  and activating  Please be patient! (this action can take about <strong>10 seconds</strong>)');
		var data = {
			'action' 	: 'pspTryActivate',
			'ipc'		: requestData.ipc,
			'email'		: requestData.email
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'OK') {
				window.location.reload();
			}
			else{
				alert(response.msg);
				return false;
			}
		}, 'json');
	}

	function ajax_list()
	{

		var make_request = function( action, params, callback ){
			var loading = $("#psp-main-loading");
			loading.show();

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, {
				'action' 		: 'pspAjaxList',
				'ajax_id'		: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
				'sub_action'	: action,
				'params'		: params
			}, function(response) {

				if( response.status == 'valid' )
				{
					$("#psp-table-ajax-response").html( response.html );
					
					//SERP module case!
					var ajax_id = $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val();
					if ( 'pspSERPKeywords' == ajax_id )
						pspSERP.wait_time();

					loading.fadeOut('fast');
				}
			}, 'json');
		}

		$(".psp-table-ajax-list").on('change', 'select[name=psp-post-per-page]', function(e){
			e.preventDefault();

			make_request( 'post_per_page', {
				'post_per_page' : $(this).val()
			} );
		})

		.on('click', 'a.psp-jump-page', function(e){
			e.preventDefault();

			make_request( 'paged', {
				'paged' : $(this).attr('href').replace('#paged=', '')
			} );
		})
		
		.on('change', 'select[name=psp-filter-post_type]', function(e){
			e.preventDefault();

			make_request( 'post_type', {
				'post_type' : $(this).val()
			} );
		})

		.on('click', '.psp-post_status-list a', function(e){
			e.preventDefault();

			make_request( 'post_status', {
				'post_status' : $(this).attr('href').replace('#post_status=', '')
			} );
		})
		
		.on('click', 'input[name=psp-search-btn]', function(e){
			e.preventDefault();

			make_request( 'search', {
				'search_text' : $(this).parent().find('#psp-search-text').val()
			} );
		});
	}
	
	function googleAuthorizeApp()
	{
		$('body').on('click', ".psp-google-authorize-app", function(e){
			e.preventDefault();

			var $this = $(this),
				saveform = $this.data('saveform') || 'yes';
  
			var ajaxPms = {
				'action' 		: 'pspGoogleAuthorizeApp',
				'saveform'		: saveform
			};

			if ( typeof saveform != 'undefined' && saveform == 'yes' ) {
			var form = $this.parents('form').eq(0),
				client_id = form.find("#client_id").val(),
				client_secret = form.find("#client_secret").val();

			// Check if user has client ID and client secret key
			if( client_id == '' || client_secret == '' ){
				alert('Please add your Client ID / Secret for authorize your app.');
				return false;
			}

			ajaxPms.params = form.serialize()		
			}
  
			$.post(ajaxurl, ajaxPms, function(response) {
				if( response.status == 'valid' )
				{
					var newwindow = window.open( response.auth_url ,'Google Authorize App','height=400,width=550' );
				}
			}, 'json');

		});
	}
	
	function facebookAuthorizeApp()
	{
		$('body').on('click', ".psp-facebook-authorize-app", function(e){
			e.preventDefault();

			var $this = $(this),
				saveform = $this.data('saveform') || 'yes';
  
			var ajaxPms = {
				'action' 		: 'pspFacebookAuthorizeApp',
				'saveform'		: saveform
			};

			if ( typeof saveform != 'undefined' && saveform == 'yes' ) {
			var form = $this.parents('form').eq(0),
				client_id = form.find("#app_id").val(),
				client_secret = form.find("#app_secret").val();

			// Check if user has client ID and client secret key
			if( client_id == '' || client_secret == '' ){
				alert('Please add your Client ID / Secret for authorize your app.');
				return false;
			}

			ajaxPms.params = form.serialize()		
			}
  
			$.post(ajaxurl, ajaxPms, function(response) {
				if( response.status == 'valid' )
				{
					var newwindow = window.open( response.auth_url ,'Facebook Authorize App','height=400,width=550' );
				}
			}, 'json');

		});
	}
	
	function makeTabs()
	{
		// tabs
		$('ul.tabsHeader').each(function() {
			var child_tab = '', child_tab_s = '';

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');
			var $content_sub;

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $(this), __currentTab = $(this).find('li.tabsCurrent').attr('title');
			$active = $( $links.filter('[title="'+__currentTab+'"]')[0] || $links[0] );
			$active.addClass('active');

			// subtabs per tab!
			var __child_tab = makeTabs_subtabs( $active );
			child_tab = __child_tab.child_tab;
			if ( child_tab != '' ) child_tab_s = '.'+child_tab;
 
			$content = $( '.'+($active.attr('title')) );
			if ( child_tab != '' ) {
				$content_sub = $( '.'+($active.attr('title')) + child_tab_s );
			}

			// Hide the remaining content
			$links.not($active).each(function () {
				$( '.'+($(this).attr('title')) ).hide();
			});
			if ( child_tab != '' )
				$( '.'+($active.attr('title')) ).not( 'ul.subtabsHeader,'+child_tab_s ).hide();

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.removeClass('active');
				
				// subtabs per tab!
				var __child_tab = makeTabs_subtabs( $active );
				child_tab = __child_tab.child_tab;
				if ( child_tab != '' ) child_tab_s = '.'+child_tab;

				$content.hide();
				if ( child_tab != '' ) $content_sub.hide();

				// Update the variables with the new link and content
				__currentTab = $(this).attr('title');
				__tabsWrapper.find('li.tabsCurrent').attr('title', __currentTab);
				$active = $(this);
				
				// subtabs per tab!
				var __child_tab = makeTabs_subtabs( $active );
				child_tab = __child_tab.child_tab;
				if ( child_tab != '' ) child_tab_s = '.'+child_tab;

				$content = $( '.'+($(this).attr('title')) );
				if ( child_tab != '' )
					$content_sub = $( '.'+($(this).attr('title')) + child_tab_s );

				// Make the tab active.
				$active.addClass('active');
				if ( child_tab != '' ) $content_sub.show();
				else $content.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});
		
		// subtabs
		$('ul.subtabsHeader').each(function() {
			var parent_tab = $(this).data('parent'), parent_tab_s = '.'+parent_tab;

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active_sub, $content_sub, $links_sub = $(this).find('a');
 
			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $(this), __currentTab = $(this).find('li.tabsCurrent').attr('title');
			$active_sub = $( $links_sub.filter('[title="'+__currentTab+'"]')[0] || $links_sub[0] );
			$active_sub.addClass('active');
			$content_sub = $(parent_tab_s + '.'+($active_sub.attr('title')));
			
			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active_sub.removeClass('active');
				$content_sub.hide();

				// Update the variables with the new link and content
				__currentTab = $(this).attr('title');
				__tabsWrapper.find('li.tabsCurrent').attr('title', __currentTab);
				$active_sub = $(this);
				$content_sub = $( parent_tab_s + '.'+($(this).attr('title')) );

				// Make the tab active.
				$active_sub.addClass('active');
				$content_sub.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});
	}
	
	function makeTabs_subtabs( active_tab ) {
 
		var ret = { 'child_tab': "" };

		var $subtabsWrapper = $('ul.subtabsHeader').filter(function(i) {
			return ( $(this).data('parent') == active_tab.attr('title') );
		});

			$('ul.subtabsHeader').hide();
		if ( $subtabsWrapper.length > 0 ) {

			$subtabsWrapper.show();

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $links = $subtabsWrapper.find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $subtabsWrapper, __currentTab = $subtabsWrapper.find('li.tabsCurrent').attr('title');
			$active = $( $links.filter('[title="'+__currentTab+'"]')[0] || $links[0] );
			$active.addClass('active');

			ret.child_tab = $active.attr('title');
		}
		return ret;
	}

	function makeActiveMenu()
	{
		topMenu.find('.active').removeClass('active');

		// try to find the first child menu of current section
		var current_section = topMenu.find( '.psp-section-' + section + (subsection != '' && sub_istab == '' ? '--' + subsection : ''));
 
		// is submenu item, loop parent
		if( current_section.parent('ul').hasClass('psp-sub-menu') ){
			current_section = current_section.parent('ul').parent('li');
		}
		if( current_section.size() > 0 ){
			current_section.addClass('active');
		}
	}

	function send_to_editor()
	{
		if( window.send_to_editor != undefined ) {
			// store old send to editor function
			window.restore_send_to_editor = window.send_to_editor;	
		}

		window.send_to_editor = function(html){
			var thumb_id = $('img', html).attr('class').split('wp-image-');
			thumb_id = parseInt(thumb_id[1]);
			
			$.post(ajaxurl, {
				'action' : 'pspWPMediaUploadImage',
				'att_id' : thumb_id
			}, function(response) {
				if (response.status == 'valid') {
					
					var upload_box = upload_popup_parent.parents('.psp-upload-image-wp-box').eq(0);
					
					upload_box.find('input').val( thumb_id );
					
					var the_preview_box = upload_box.find('.upload_image_preview'),
						the_img = the_preview_box.find('img');
						
					the_img.attr('src', response.thumb );
					the_img.show();
					the_preview_box.show();
					upload_box.find('.psp-prev-buttons').show();
					upload_box.find(".upload_image_button_wp").hide();
				
				}
			}, 'json');
			
			tb_remove();
			
			if( window.restore_send_to_editor != undefined ) {
				// store old send to editor function
				window.restore_send_to_editor = window.send_to_editor;	
			}
		}
	}
	
	function removeWpUploadImage( $this )
	{
		var upload_box = $this.parents(".psp-upload-image-wp-box").eq(0);
		upload_box.find('input').val('');
		var the_preview_box = upload_box.find('.upload_image_preview'),
			the_img = the_preview_box.find('img');
			
		the_img.attr('src', '');
		the_img.hide();
		the_preview_box.hide();
		upload_box.find('.psp-prev-buttons').hide();
		upload_box.find(".upload_image_button_wp").fadeIn('fast');
	}
	
	function removeHelp()
	{
		$("#psp-help-container").remove();	
	}
	
	function showHelp( that )
	{
		removeHelp();
		var help_type = that.data('helptype');
        var html = $('<div class="psp-panel-widget" id="psp-help-container" />');
        html.append("<a href='#close' class='psp-button red' id='psp-close-help'>Close HELP</a>")
		if( help_type == 'remote' ){
			var url = that.data('url');
			var content_wrapper = $("#psp-content");
			
			html.append( '<iframe src="' + ( url ) + '" style="width:100%; height: 100%;border: 1px solid #d7d7d7;" frameborder="0"></iframe>' )
			
			content_wrapper.append(html);
		}
	}
	
	function multiselect_left2right( autselect ) {
		var $allListBtn = $('.multisel_l2r_btn');
		var autselect = autselect || false;
 
		if ( $allListBtn.length > 0 ) {
			$allListBtn.each(function(i, el) {
 
				var $this = $(el), $multisel_available = $this.prevAll('.psp-multiselect-available').find('select.multisel_l2r_available'), $multisel_selected = $this.prevAll('.psp-multiselect-selected').find('select.multisel_l2r_selected');
 
				if ( autselect ) {
					$multisel_selected.find('option').each(function() {
						$(this).prop('selected', true);
					});
					$multisel_available.find('option').each(function() {
						$(this).prop('selected', false);
					});
				} else {

				$this.on('click', '.moveright', function(e) {
					e.preventDefault();
					$multisel_available.find('option:selected').appendTo($multisel_selected);
				});
				$this.on('click', '.moverightall', function(e) {
					e.preventDefault();
					$multisel_available.find('option').appendTo($multisel_selected);
				});
				$this.on('click', '.moveleft', function(e) {
					e.preventDefault();
					$multisel_selected.find('option:selected').appendTo($multisel_available);
				});
				$this.on('click', '.moveleftall', function(e) {
					e.preventDefault();
					$multisel_selected.find('option').appendTo($multisel_available);
				});
				
				}
			});
		}
	}
	
	function hashChange()
	{
		if ( location.href.indexOf("psp#") != -1 ) {
			// Alerts every time the hash changes!
			if(location.hash != "") {
				section = location.hash.replace("#", '');
				
				var __tmp = section.indexOf('#');
				if ( __tmp == -1 ) {
				    subsection = '';
				} else { // found subsection block!
					subsection = section.substr( __tmp+1 );
					section = section.slice( 0, __tmp );
				}
	 
    			if ( subsection != '' ) {
    			    var __re = /tab:([0-9a-zA-Z_-]*)/gi; //new RegExp("tab:([0-9a-zA-Z_-]*)", "gi");
    			    if ( __re.test(subsection) ) {
                        var __match = subsection.match(__re); //__re.exec(subsection); //null;
                        sub_istab = typeof (__match[0]) != 'undefined' ? __match[0].replace('tab:', '') : '';

                        if ( sub_istab == '' ) return false;
                        makeRequest([
                            function (s) { 
                                $('.tabsHeader').find('a[title="'+s+'"]').click();
                            },
                            sub_istab
                        ]);
    			    } else {
        				makeRequest([
        					function (s) { scrollToElement( s ) },
        					'#'+subsection
        				]);
    				}
    			} else { 
    				makeRequest();
    			}
            }
			return false;
		}
		if ( location.href.indexOf("=psp") != -1 ) {
			makeRequest();
			return false;
		}
	}
	
	function triggers()
	{
		googleAuthorizeApp();
		facebookAuthorizeApp();
		
		$('body').on('click', '.upload_image_button_wp, .change_image_button_wp', function(e) {
			e.preventDefault();
			upload_popup_parent = $(this);
			var win = $(window);
			
			send_to_editor();
		
			tb_show('Select image', 'media-upload.php?type=image&amp;height=' + ( parseInt(win.height() / 1.2) ) + '&amp;width=610&amp;post_id=0&amp;from=aaframework&amp;TB_iframe=true');
		});
		
		$('body').on('click', '.remove_image_button_wp', function(e) {
			e.preventDefault();
			
			removeWpUploadImage( $(this) );
		});
		
		if ( typeof jQuery.fn.tipsy != "undefined" ) { // verify tipsy plugin is defined in jQuery namespace!
			$('a.aa-tooltip').tipsy({
				gravity: 'e'
			});
		}

		$(window).resize(function() {
			fixLayoutHeight();
		});
		$('body').on('click', '.psp_activate_product', function(e) {
			e.preventDefault();
			activatePlugin($(this));
		});
		$('body').on('click', '.psp-saveOptions', function(e) {
			e.preventDefault();
			saveOptions($(this));
		});
		$('body').on('click', '.psp-installDefaultOptions', function(e) {
			e.preventDefault();
			installDefaultOptions($(this));
		});
		$('body').on('click', '.psp-ImportSEO', function(e) {
			e.preventDefault();
			importSEOData($(this));
		});
		$("body").on('click', '#psp-module-manager a', function(e) {
			e.preventDefault();
			moduleChangeStatus($(this));
		});

		$('body').on('click', 'input#psp-item-check-all', function(){
			var that = $(this),
				checkboxes = $('#psp-list-table-posts input.psp-item-checkbox');

			if( that.is(':checked') ){
				checkboxes.prop('checked', true);
			}
			else{
				checkboxes.prop('checked', false);
			}
		});

		// Bind the hashchange event.
		$(window).on('hashchange', function(){
			hashChange();
		});
		hashChange();

		ajax_list();
		
		$("body").on('click', "a.psp-show-docs-shortcut", function(e){
        	e.preventDefault();
        	
        	$("a.psp-show-docs").click();
        });
        
		$("body").on('click', "a.psp-show-docs", function(e){
        	e.preventDefault();
        	 
        	showHelp( $(this) );
        });
        
         $("body").on('click', "a#psp-close-help", function(e){
        	e.preventDefault();
        	
        	removeHelp();
        });
        
        multiselect_left2right();
    }
    
    function scrollToElement(selector, time, verticalOffset) 
    {
    	time = typeof(time) != 'undefined' ? time : 1000;
    	verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;

    	var element = jQuery(selector);
    	if ( element.length <= 0 ) return false;

    	var offset = element.offset();
    	var offsetTop = parseInt( parseInt(offset.top) + parseInt(verticalOffset) );
    	$('html, body').animate({
    		scrollTop: offsetTop
    	}, time);
    }

    // UTF8 / UTF-8 related!
    function encode_utf8( s )
    {
    	return unescape( encodeURIComponent( s ) );
    }
    function substr_utf8_bytes(str, startInBytes, lengthInBytes) {

    	/* this function scans a multibyte string and returns a substring.
    	* arguments are start position and length, both defined in bytes.
    	*
    	* this is tricky, because javascript only allows character level
    	* and not byte level access on strings. Also, all strings are stored
    	* in utf-16 internally - so we need to convert characters to utf-8
    	* to detect their length in utf-8 encoding.
    	*
    	* the startInBytes and lengthInBytes parameters are based on byte
    	* positions in a utf-8 encoded string.
    	* in utf-8, for example:
    	*       "a" is 1 byte,
    	"ü" is 2 byte,
    	and  "你" is 3 byte.
    	*
    	* NOTE:
    	* according to ECMAScript 262 all strings are stored as a sequence
    	* of 16-bit characters. so we need a encode_utf8() function to safely
    	* detect the length our character would have in a utf8 representation.
    	*
    	* http://www.ecma-international.org/publications/files/ecma-st/ECMA-262.pdf
    	* see "4.3.16 String Value":
    	* > Although each value usually represents a single 16-bit unit of
    	* > UTF-16 text, the language does not place any restrictions or
    	* > requirements on the values except that they be 16-bit unsigned
    	* > integers.
    	*/

    	var resultStr = '';
    	var startInChars = 0;

    	// scan string forward to find index of first character
    	// (convert start position in byte to start position in characters)

    	var ch;
    	for (var bytePos = 0; bytePos < startInBytes; startInChars++) {

    		// get numeric code of character (is >128 for multibyte character)
    		// and increase "bytePos" for each byte of the character sequence

    		ch = str.charCodeAt(startInChars);
    		bytePos += (ch < 128) ? 1 : encode_utf8(str[startInChars]).length;
    	}

    	// now that we have the position of the starting character,
    	// we can built the resulting substring

    	// as we don't know the end position in chars yet, we start with a mix of
    	// chars and bytes. we decrease "end" by the byte count of each selected
    	// character to end up in the right position
    	var end = startInChars + lengthInBytes - 1;

    	for (var n = startInChars; startInChars <= end; n++) {
    		// get numeric code of character (is >128 for multibyte character)
    		// and decrease "end" for each byte of the character sequence
    		ch = str.charCodeAt(n);
    		end -= (ch < 128) ? 1 : encode_utf8(str[n]).length;

    		resultStr += str[n];
    	}

    	return resultStr;
    }

    // external usage
	return {
		'scrollToElement'			: scrollToElement,
		'substr_utf8_bytes' 		: substr_utf8_bytes,
		'makeTabs'					: makeTabs,
		'multiselect_left2right'	: multiselect_left2right
		//'addPreviewFooter'	: addPreviewFooter
    }
    
})(jQuery);
function pspPopUpClosed() {
    window.location.reload();
}