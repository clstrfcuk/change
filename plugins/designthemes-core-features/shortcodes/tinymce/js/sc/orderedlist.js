scnShortcodeMeta = {
	attributes : [
			{
				label : "Style",
				id : "style",
				help : "Choose the style of list that you wish to use. Each one has a different icon.",
				controlType : "select-control",
				selectValues : ['decimal', 'decimal-leading-zero','lower-alpha', 'lower-roman', 'upper-alpha','upper-roman' ],
				defaultValue : 'decimal',
				defaultText : 'decimal (Default)'

			},
			{
				label : 'Variation',
				id : 'variation',
				help : 'Choose one of our predefined color skins to use with your list.',
				controlType : "select-control",
				selectValues : [ '','avocado','black','blue','blueiris','blueturquoise','brown','burntsienna','chillipepper','eggplant','electricblue','graasgreen','gray','green','orange','palebrown','pink','radiantorchid','red','skyblue','yellow'],
				defaultValue : '',
				defaultText : 'Select'
			}, ],
	defaultContent : "<ol>" + "<li>Lorem ipsum dolor sit </li>"
			+ "<li>Praesent convallis nibh</li>"
			+ "<li>Nullam ac sapien sit</li>"
			+ "<li>Phasellus auctor augue</li></ol><br>",
	shortcode : "dt_sc_fancy_ol"
};