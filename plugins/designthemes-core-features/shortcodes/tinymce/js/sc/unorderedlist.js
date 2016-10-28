scnShortcodeMeta = {
	attributes : [
			{
				label : "Style",
				id : "style",
				help : "Choose the style of list that you wish to use. Each one has a different icon.",
				controlType : "select-control",
				selectValues : [ 'arrow', 'rounded-arrow', 'double-arrow',
						'heart', 'trash', 'star', 'tick', 'rounded-tick',
						'cross', 'rounded-cross', 'rounded-question',
						'rounded-info', 'delete', 'warning', 'comment', 'edit',
						'share', 'plus', 'rounded-plus', 'minus',
						'rounded-minus', 'asterisk', 'cart', 'folder',
						'folder-open', 'desktop', 'tablet', 'mobile', 'reply',
						'quote', 'mail', 'external-link', 'adjust', 'pencil',
						'print', 'tag', 'thumbs-up', 'thumbs-down', 'time',
						'globe', 'pushpin', 'map-marker', 'link', 'paper-clip',
						'download', 'key', 'search', 'rss', 'twitter',
						'facebook', 'linkedin', 'google-plus' ],
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
	defaultContent : "<ul>" + "<li>Lorem ipsum dolor sit </li>"
			+ "<li>Praesent convallis nibh</li>"
			+ "<li>Nullam ac sapien sit</li>"
			+ "<li>Phasellus auctor augue</li></ul><br>",
	shortcode : "dt_sc_fancy_ul"
};