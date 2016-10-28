scnShortcodeMeta = {
	attributes : [
			{
				label : "Type",
				id : "type",
				help : "Select which type of box you would like to use.",
				controlType : "select-control",
				selectValues : [ 'titled-box', 'error-box', 'warning-box',
						'success-box', 'info-box' ],
				defaultValue : 'medium',
				defaultText : 'medium (Default)'
			},

			{
				label : "Title",
				id : "title",
				help : 'Type out the title to use with your titled box. The title will display above the content. (* Applicable for titled box only )',
				controlType : "text-control"
			},

			{
				label : "Fontawesome Icon",
				id : "icon",
				help : 'Type out the fontawesome icon class to use with your titled box. (* Applicable for titled box only )',
				controlType : "text-control"
			},

			{
				label : "Background Color",
				id : "bgcolor",
				help : 'Or you can also choose your own color to use as the background for your button',
				controlType : "color-control"
			},
			{
				label : 'Variation',
				id : 'variation',
				help : 'Choose one of our predefined color skins to use with your list.',
				controlType : "select-control",
				selectValues : [ '','avocado','black','blue','blueiris','blueturquoise','brown','burntsienna','chillipepper','eggplant','electricblue','graasgreen','gray','green','orange','palebrown','pink','radiantorchid','red','skyblue','yellow'],
				defaultValue : '',
				defaultText : 'Select'
			},
			{
				label : "Text Color",
				id : "textcolor",
				help : 'You can change the color of the text that appears on your button.',
				controlType : "color-control"
			} ],
	defaultContent : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra per inceptos himenaeos.,",
	shortcode : "dt_sc_titled_box"

};