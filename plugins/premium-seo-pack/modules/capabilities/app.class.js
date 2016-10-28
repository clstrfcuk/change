/*
Document   :  Modules Manager
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspModulesManager = (function ($) {
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
	
	function get_user_modules() {
		mainloading.fadeIn('fast');

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, {
			'action' 		: 'pspCapabilities_changeUser',
			'user_role'		: $('select[name=psp-filter-user-roles]').val(),
			'debug_level'		: debug_level
		}, function(response) {

			if( response.status == 'valid' )
			{
				mainloading.fadeOut('fast');
				$("#psp-table-ajax-response").html( response.html );
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}
	
	function save_changes() {
		mainloading.fadeIn('fast');
		
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
		}

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, {
			'action' 		: 'pspCapabilities_saveChanges',
			'user_role'		: $('select[name=psp-filter-user-roles]').val(),
			'modules'		: ids,
			'debug_level'		: debug_level
		}, function(response) {

			if( response.status == 'valid' )
			{
				mainloading.fadeOut('fast');
				$("#psp-table-ajax-response").html( response.html );
			}
			mainloading.fadeOut('fast');
			return false;
		}, 'json');
	}
	
	function triggers()
	{
		maincontainer.on('change', 'select[name=psp-filter-user-roles]', function(e){
			e.preventDefault();

			get_user_modules();
		});
		
		maincontainer.on('click', 'input#psp-save-changes', function(e) {
			e.preventDefault();
			
			save_changes();
		});
	}

	// external usage
	return {
    	}
})(jQuery);
