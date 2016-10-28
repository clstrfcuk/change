//psp_local_seo_tc_button, psp_rich_snippets
(function($) {
	var pluginCmd = 'aafPopup',
	pluginPopupUrl = aafShortcodes.plugin_url + "tinymce.popup.php?{elemId}&{width}&{height}";

	var misc = {

		arrayHasOwnIndex: function(array, prop) {
			return array.hasOwnProperty(prop) && /^0$|^[1-9]\d*$/.test(prop) && prop <= 4294967294; // 2^32 - 2
		},
		
		hasOwnProperty: function(obj, prop) {
			var proto = obj.__proto__ || obj.constructor.prototype;
			return (prop in obj) &&
			(!(prop in proto) || proto[prop] !== obj[prop]);
		}

	};
	
    tinymce.PluginManager.add('aafShortcodes', function( editor, url ) {
    	// add command
    	if ( 1 ) {
			editor.addCommand(pluginCmd, function ( ui, atts ) {
 
				var title = atts.moduleTitle || 'default module title',
				elemId = 'module=' + atts.module + '&shortcode=' + atts.shortcode;
			
				// load popup window!
				var popupUrl = pluginPopupUrl;
				popupUrl = popupUrl.replace('{elemId}', elemId);

				var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
				W = W - 80; H = H - 84;
				popupUrl = popupUrl.replace('{width}', 'width=' + W);
				popupUrl = popupUrl.replace('{height}', 'height=' + H);

				tb_show(title, popupUrl);
			});
		}
			
    	// add buttons
		if ( 1 ) {
			var palias = aafShortcodes.plugin_alias;

			if ( !misc.hasOwnProperty(aafShortcodes, 'modules') )
				return null;

			var that = this, button, btnMenuRoot = [];
				
			for (var module in aafShortcodes.modules) {

				var btnName = palias+'_'+module, btnDetails = aafShortcodes.modules[module];
 
 				var btnMenu = [];
				var shc = btnDetails.shortcodes;
				for (var key in shc) {
					var shortcode = shc[key];
					
					atts = {
						'moduleTitle'	: btnDetails.button.title,
						'title'			: shortcode.title,
						'module'		: module,
						'shortcode'		: shortcode.name
					};
					btnMenu.push({
						text		: shortcode.title,
						value		: atts,
						onclick		: function(e) {
							var $this = this, atts = e.control._value; //$this.text()

							editor.execCommand(pluginCmd, false, atts);
						}
					});
				}
				
				btnMenuRoot.push({
					text		: btnDetails.button.title,
					value		: {},
					onclick		: function(e) {
					},
					menu		: btnMenu
				});
			}

        	editor.addButton(aafShortcodes.plugin_btn_name, {
            	title	: aafShortcodes.plugin_btn_title,
            	icon	: 'icon psp-btn-sh-icon',
            	image	: (btnDetails.folder_uri + btnDetails.button.icon),
            	type	: 'menubutton',
            	onclick : function(e) {},
            	onPostRender: function() {
            		button = this;
            	},
				menu: 	btnMenuRoot
        	});
		}
	});
})(jQuery);