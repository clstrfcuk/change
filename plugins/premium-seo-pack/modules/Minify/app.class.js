/*
Document   :  Minify
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspMinify = (function ($) {
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
	
	function triggers() {
	}

	// external usage
	return {
    }
})(jQuery);
