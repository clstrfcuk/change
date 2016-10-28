/*
Document   :  Social Stats
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspLinkRedirect = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var mainloading = null;
    var lightbox = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#psp-wrapper");
			mainloading = $("#psp-main-loading");
			lightbox = $("#psp-lightbox-overlay");

			triggers();
		});
	})();
	
	function setFlagAdd(val) {
		localStorage.setItem('add_flag', val);
	}
	function getFlagAdd() {
		var myValue = localStorage.getItem( 'add_flag' );
    	if (myValue)
        	return myValue;
        return 0;
	}
	
	function showAddNewLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-title-upd')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-title-add')
			.css({'display': 'table'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
		});
	}
	
	function showUpdateLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-title-upd')
			.css({'display': 'table'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-title-add')
			.css({'display': 'none'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
		});
	}
	
	function addToBuilder( $form )
	{
		lightbox.fadeOut('fast');
		mainloading.fadeIn('fast');
		
		var url = $form.find('#new_url'), 
			url_val = url.val(),
			url_redirect = $form.find('#new_url_redirect'), url_redirect_val = url_redirect.val();
			
			
		if (!url_val.match("^http?://")) {
			url.val("http://" + url_val);
			url.val( url.val().replace("http://https://", "https://") );
		}
		if (!url_redirect_val.match("^http?://")) {
			url_redirect.val("http://" + url_redirect_val);
			url_redirect.val( url_redirect.val().replace("http://https://", "https://") );
		}

		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspAddToRedirect" });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: 0 });

		jQuery.post(ajaxurl, data_save, function(response) {
			if( response.status == 'valid' ) {
				setFlagAdd(1);
				mainloading.fadeOut('fast');
				window.location.reload();
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}
	
	function getUpdateData( itemid ) {
		mainloading.fadeIn('fast');

		jQuery.post(ajaxurl, {
			'action' 		: 'pspGetUpdateDataRedirect',
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading.fadeOut('fast');

				setUpdateForm( response.data );
				showUpdateLink();
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}

	function setUpdateForm( data ) {
		var $form = $('.psp-update-link-form'),
		itemid = data.id, url = data.url, url_redirect = data.url_redirect;

		$form.find('input#upd-itemid').val( itemid ); //hidden field to indentify used row for update!
		$form.find('input#new_url2').val( url );
		$form.find('input#new_url_redirect2').val( url_redirect );
	}
	
	function updateToBuilder( itemid, subaction )
	{
		subaction = subaction || '';
		
		var $form = $('.psp-update-link-form');
		
		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspUpdateToRedirect" });
    	data_save.push({ name: "subaction", value: subaction });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: itemid });
			
		lightbox.fadeOut('fast');
		mainloading.fadeIn('fast');
		
		jQuery.post(ajaxurl, data_save, function(response) {
			if( response.status == 'valid' ){
				setFlagAdd(1);
				mainloading.fadeOut('fast');
				window.location.reload();
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}
	
	function deleteFromBuilder( itemid )
	{
		lightbox.fadeOut('fast');
		mainloading.fadeIn('fast');
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoveFromRedirect',
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				setFlagAdd(1);
				mainloading.fadeOut('fast');
				window.location.reload();
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
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
		
		mainloading.fadeIn('fast');

		jQuery.post(ajaxurl, {
			'action' 		: 'pspLinkRedirect_do_bulk_delete_rows',
			'id'			: ids,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading.fadeOut('fast');
				setFlagAdd(1);
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
		// add form lightbox
		if (getFlagAdd()==0) ;//showAddNewLink();
		setFlagAdd(0);

		maincontainer.on("click", '#psp-do_add_new_link', function(e){
			e.preventDefault();
			showAddNewLink();
		});
		
		// add row
		$('body').on('click', ".psp-add-link-form input#psp-submit-to-builder", function(e){
			e.preventDefault();
			
			var $form = $('.psp-add-link-form'),
			url = $form.find('#new_url').val(),
			url_redirect = $form.find('#new_url_redirect').val();
			
			//maybe some validation!
			if ($.trim(url)=='' || $.trim(url_redirect)=='') {
				alert('You didn\'t complete the necessary fields!');
				return false;
			}
			
			addToBuilder( $form );
		});
		
		// delete row		
		$('body').on('click', ".psp-do_item_delete", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid'),
				url = row.find('td').eq(3).find('input').val(),
				url_redirect = row.find('td').eq(4).find('input').val();

			//row.find('code').eq(0).text()
			if(confirm('Delete (' + url + ', ' + url_redirect  + ') pair from redirect? This action can\t be rollback!' )){
				deleteFromBuilder( id );
			}
		});
		
		// update row info
		$('body').on('click', ".psp-do_item_update", function(e){
			e.preventDefault();

			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid');

			getUpdateData( id );
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
		
		maincontainer.on('click', '#psp-do_bulk_delete_rows', function(e){
			e.preventDefault();

			if (confirm('Are you sure you want to delete the selected rows?'))
				delete_bulk_rows();
		});
		
		//all checkboxes are checked by default!
		$('.psp-form .psp-table input.psp-item-checkbox').attr('checked', 'checked');
		
	}

	// external usage
	return {
    }
})(jQuery);
