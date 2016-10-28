/*
Document   :  404 Monitor
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
psp404Monitor = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var mainloading = null;
    var lightbox = null;
    var loaded_page = 0;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#psp-wrapper");
			mainloading = maincontainer.find("#psp-main-loading");
			lightbox = $("#psp-lightbox-overlay");

			triggers();
		});
	})();
	
	function showAddNewLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-add-redirect')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-details')
			.css({'display': 'table'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
		});
	}
	
	function showUpdateLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-add-redirect')
			.css({'display': 'table'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-details')
			.css({'display': 'none'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
		});
	}

	function getDetails( id, sub_action )
	{
		mainloading.fadeIn('fast');
			
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGet404MonitorRequest',
			'id'			: id,
			'sub_action'	: sub_action,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading.fadeOut('fast');

				lightbox.find("#psp-lightbox-seo-report-response").html( response.data );
				showAddNewLink();
			}
			mainloading.fadeOut('fast');
			return false;

		}, 'json');
	}
	
	function getUpdateData() {
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked'), __urls = [];
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
			
			var that = $(this),
				row = that.parents('tr').eq(0);
			__urls[k] = row.find('td').eq(3).text();
		});
		ids = ids.join(',');
		if (ids.length<=0) {
			alert('You didn\'t select any rows!');
			return false;
		}
		__urls = __urls.join('<br />');

		var $form = $('.psp-update-link-form'),
		itemid = ids, url_redirect = $form.find('input#new_url_redirect2').val();

		$form.find('input#upd-itemid').val( itemid ); //hidden field to indentify used rows for update!
		$form.find('input#new_url_redirect2').val( url_redirect );
		$form.find('#old_url_list').html( __urls );
		
		showUpdateLink();
	}

	function updateToBuilder( itemid, subaction )
	{
		subaction = subaction || '';
		
		var $form = $('.psp-update-link-form'),
		url_redirect = $form.find('input#new_url_redirect2').val();
		
		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "psp404MonitorToRedirect" });
    	data_save.push({ name: "subaction", value: subaction });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: itemid });
			
		lightbox.fadeOut('fast');
		mainloading.fadeIn('fast');
		
		jQuery.post(ajaxurl, data_save, function(response) {
			if( response.status == 'valid' ){
				mainloading.fadeOut('fast');
				window.location.reload();
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}
	
	function delete_404_rows() {
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
			alert('You didn\'t select any rows!');
			return false;
		}
		
		mainloading.fadeIn('fast');

		jQuery.post(ajaxurl, {
			'action' 		: 'psp_do_bulk_delete_404_rows',
			'id'			: ids,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading.fadeOut('fast');				
				//refresh page!
				window.location.reload();
				return false;
			}
			mainloading.fadeOut('fast');
			alert('Problems occured while trying to delete the selected rows!');
		}, 'json');
	}

	function triggers()
	{
		maincontainer.on('click', 'a.psp-btn-referrers-lightbox, a.psp-btn-user_agents-lightbox', function(e){
			e.preventDefault();

			var that 	= $(this),
				itemID	= that.data('itemid');

			getDetails( itemID, that.attr('href').replace("#", '') );
		});
		
		// update row info
		maincontainer.on('click', "#psp-do_add_new_link", function(e){
			e.preventDefault();
			getUpdateData();
		});
		$('body').on('click', ".psp-update-link-form input#psp-submit-to-builder2", function(e){
			e.preventDefault();

			var $form = $('.psp-update-link-form'),
			itemid = $form.find('input#upd-itemid').val(),
			url_redirect = $form.find('input#new_url_redirect2').val();
	
			//maybe some validation!
			if ($.trim(url_redirect)=='') {
				alert('You didn\'t complete the necessary fields!');
				return false;
			}
			updateToBuilder( itemid );
		});
		
		maincontainer.on('click', '#psp-do_bulk_delete_404_rows', function(e){
			e.preventDefault();

			if (confirm('Are you sure you want to delete the selected rows?'))
				delete_404_rows();
		});
		
		//all checkboxes are checked by default!
		$('.psp-form .psp-table input.psp-item-checkbox').attr('checked', 'checked');
	}

	// external usage
	return {
    }
})(jQuery);
