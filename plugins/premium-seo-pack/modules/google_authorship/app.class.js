/*
Document   :  Google Authorship
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspGoogleAuthorship = (function ($) {
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
	
	function fixMetaBoxLayout()
	{
		//meta boxes
		var meta_box 		= $(".psp-meta-box-container"),
			meta_box_width 	= $(".psp-meta-box-container").width() - 100;
  
		$("#profile-page #psp-meta-box-preload").hide();
		$("#profile-page .psp-meta-box-container").fadeIn('fast');

		/*$("#profile-page").on('click', '.psp-tab-menu a', function(e){
			e.preventDefault();

			var that 	= $(this),
				open 	= $("#profile-page .psp-tab-menu a.open"),
				href 	= that.attr('href').replace('#', '');

			$("#profile-page .psp-meta-box-container").hide();

			$("#profile-page #psp-tab-div-id-" + href ).show();

			// close current opened tab
			var rel_open = open.attr('href').replace('#', '');

			$("#profile-page #psp-tab-div-id-" + rel_open ).hide();

			$("#profile-page #psp-meta-box-preload").show();

			$("#profile-page #psp-meta-box-preload").hide();
			$("#profile-page .psp-meta-box-container").fadeIn('fast');

			open.removeClass('open');
			that.addClass('open');
		});*/
	}
	
	function triggers()
	{
		fixMetaBoxLayout();
	}

	// external usage
	return {
    	}
})(jQuery);
