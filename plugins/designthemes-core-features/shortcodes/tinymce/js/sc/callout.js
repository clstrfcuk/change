scnShortcodeMeta = {
	attributes : [

			{
				label : "Type",
				id : "type",
				help : "",
				controlType : "select-control",
				selectValues : [ 'type1' ,'type2','type3','type4','type5'],
				defaultValue : 'type1',
				defaultText : 'type1 (Default)'
			},

			{
				label : "Button Label",
				id : "button_text",
				help : "The button label.",
			},
			{
				label : "Link",
				id : "link",
				help : "Optional link (e.g. http://google.com).",
			},
			{
				label : "Target",
				id : 'target',
				help : 'Setting the target to _blank will open your page in a new tab when the reader clicks on the button.',
				controlType : "select-control",
				selectValues : [ '_blank', '_new', '_parent', '_self', '_top' ],
				defaultValue : '_blank',
				defaultText : '_blank (Default)'
			},
			{
				label : "Font Awesome Icon",
				id : "icon",
				help : "",
			}],
	defaultContent : "<h4>Our Technological services has been improved vastly</h4><h5>Come Experience the real life situations of saving life</h5>",
	shortcode : "dt_sc_callout_box"

};