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

	tinymce.create('tinymce.plugins.aafShortcodes', {
 
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
  
			ed.addCommand(pluginCmd, function ( ui, atts ) {

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
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
 
			var palias = aafShortcodes.plugin_alias;

			var regex = new RegExp("^"+palias+'_', "gi");
			if ( regex.test( n ) ) {

				if ( !misc.hasOwnProperty(aafShortcodes, 'modules') )
					return null;

				var modulesList = aafShortcodes.modules,
				module = n.replace(palias+'_', '');
				
				if ( !misc.hasOwnProperty(modulesList, module) )
					return null;

				var that = this;
				var btnName = n, btnDetails = modulesList[module];

				var btn = cm.createSplitButton( btnName, {
					title: btnDetails.button.title,
					image: (btnDetails.folder_uri + btnDetails.button.icon),
					icons: false,
					onclick: function () {
						jQuery("#content_"+n+"_open").closest('td').trigger('click');
						return false;
					}
				} );

				btn.onRenderMenu.add(function (c, m) {

					var shc = btnDetails.shortcodes;
					for (var key in shc) {
						var shortcode = shc[key];
						that.addDropdownItem( m, {
							'moduleTitle'	: btnDetails.button.title,
							'title'			: shortcode.title,
							'module'		: module,
							'shortcode'		: shortcode.name
						} );
					}
				});
				return btn;
			}

			return null;
		},

		/**
		 * Custom method: add an menu element to the module button
		 *
		 */
		addDropdownItem: function ( ed, atts ) {
			ed.add({
				title: atts.title,
				onclick: function () {
					tinyMCE.activeEditor.execCommand(pluginCmd, false, atts);
				}
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname 	: 'Shortcodes Buttons',
				author 		: 'Andrei Dinca, AA-Team',
				authorurl 	: 'http://codecanyon.net/user/AA-Team',
				infourl 	: 'http://codecanyon.net/user/AA-Team',
				version 	: "0.1"
			};
		}

	});
	// Register plugin
	tinymce.PluginManager.add( 'aafShortcodes', tinymce.plugins.aafShortcodes );
})(jQuery);