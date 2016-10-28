/*
Document   :  Shortcodes
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
	
// Initialization and events code for the app
(function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    
    var shortcodeFormat = null;
    var shortcode = null;
    var atts = {};


    // init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function() {
			maincontainer = $(".psp-form");
			shortcodeFormat = $('#aafShortcodeFormat').text();
			shortcode = shortcodeFormat;
			
			atts.exclude_empty = $('#aafShortcodeAtts').data('exclude_empty');
			
			triggers();
		});
	})();
	
	function tb_resize()
	{
		var	tb_content = $('#TB_ajaxContent'),
		tb_window = $('#TB_window'),
		box_content = $('div.psp-form');
		
		var W = box_content.outerWidth(), H = box_content.outerHeight(),
		windowW = tb_window.outerWidth(), windowH = tb_window.outerHeight();

		tb_window.css({
			'height'		: parseInt( H + 50 ) + 'px',
			'width'			: W + 'px',
			'margin-left'	: '-' + parseInt( W / 2 ) + 'px' // center horizontal
		});
 
		tb_content.addClass('tb_content');
		tb_content.css({
			'height'		: parseInt( windowH - 150 ) + 'px',
			'width'			: parseInt( windowW - 40 ) + 'px'
		});
		
		/*var dbgW = window.getComputedStyle(tb_window.get(0), null), dbgW2 = {
			'width' 		: dbgW.getPropertyValue( 'width' ),
			'height'		: dbgW.getPropertyValue( 'height' ),
			'margin-left'	: dbgW.getPropertyValue( 'margin-left' )
		},
		dbgC = window.getComputedStyle(tb_content.get(0), null), dbgC2 = {
			'width' 		: dbgC.getPropertyValue( 'width' ),
			'height'		: dbgC.getPropertyValue( 'height' )
		};
		console.log( dbgW2, dbgC2  );*/
	}
	
	function buildShortcode() {

		var fieldsToExclude = [];
		fieldsToExclude.push( 'box_id' );
		fieldsToExclude.push( 'box_nonce' );

		shortcode = shortcodeFormat;

		var $form = $('div.psp-form').find('form.psp-form'),
		data_save = $form.serializeArray();

		var dynamicAtts = [], multipleVals = {};
		for (var key in data_save) {

			var val = data_save[key], theName = val.name, theValue = val.value;

			// exclude form special fields - not for shortcode!
			if ( $.inArray( theName, fieldsToExclude ) != -1 ) {
				continue;
			}
			
			// multiple selected values
			var isMultiple = new RegExp("\[\]$", "gi");
			if ( /\[\]$/gi.test( theName ) ) {

				var __tmp = theName.replace('[]', '');
				if ( !$.isArray(multipleVals[__tmp]) ) {
					multipleVals[__tmp] = [];
				}
				multipleVals[__tmp].push( theValue );
				continue;
			}

			if ( atts.exclude_empty == 'yes' && $.trim( theValue ) != '' && $.trim( theValue ) != 'none' ) {
				dynamicAtts.push( theName + '="' + theValue + '"' );
			} else {
				var regexp = new RegExp("{" + theName + "}", "gi");
				shortcode = shortcode.replace( regexp, theValue );
			}
		}
		
		var mv = '';
		for (var key in multipleVals) {
			if ( hasOwnProperty(multipleVals, key) ) {

				mv = multipleVals[key].join(';;');

				if ( atts.exclude_empty == 'yes' ) {
					dynamicAtts.push( key + '="' + mv + '"' );
				} else {
					var regexp = new RegExp("{" + key + "}", "gi");
					shortcode = shortcode.replace( regexp, mv );
				}
			}
		}

		//multipleVals
		if ( atts.exclude_empty == 'yes' ) {
			shortcode = shortcode.replace( '{atts}', dynamicAtts.join(' ' ) );
		}

		$('#aafShortcodeField').text( shortcode );
	}

	function triggers()
	{
   		// thickbox window
   		tb_resize();
   		/*$(window).resize(function() {
   			tb_resize();
   		});*/
    		
		// reset form to default values button
		$('#aff-reset-shortcode').on('click', function (e) {
			e.preventDefault();
			
			var $form = $('div.psp-form').find('form.psp-form').get(0);
			$form.reset();
		});
		
		// insert code button
		$('#aaf-insert-shortcode').on('click', function (e) {
			e.preventDefault();

			buildShortcode(); // build shortcode based on form elements!

			if ( window.tinyMCE && $.trim( shortcode ) != '' ) {

				var code = $('#aafShortcodeField').text();

				/* get the TinyMCE version to account for API diffs */
				var tmce_ver=window.tinyMCE.majorVersion;
				if ( tmce_ver>="4" ) {
					window.tinyMCE.execCommand('mceInsertContent', false, code);
				} else {
					window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, code);
				}
				tb_remove();
			}
		});
	}
	
	function hasOwnProperty(obj, prop) {
		var proto = obj.__proto__ || obj.constructor.prototype;
		return (prop in obj) &&
		(!(prop in proto) || proto[prop] !== obj[prop]);
	}
	
	// external usage
	return {
    }
})(jQuery);