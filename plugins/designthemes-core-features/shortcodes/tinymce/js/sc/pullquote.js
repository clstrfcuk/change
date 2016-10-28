scnShortcodeMeta = {
	attributes : [

			{
				label : "Type",
				id : "type",
				help : "",
				controlType : "select-control",
				selectValues : [ 'pullquote1' ,'pullquote2' ,'pullquote3','pullquote4','pullquote5','pullquote6'],
				defaultValue : 'pullquote1',
				defaultText : 'pullquote1'
			},

			{
				label : "Align",
				id : "align",
				help : "Set the alignment for your quote here.Your quote will float along the center, left or right hand sides depending on your choice.",
				controlType : "select-control",
				selectValues : [ 'left' ,'right' ,'center'],
				defaultValue : 'left',
				defaultText : 'left'
			},

			{
				label : "Quote Icon",
				id : "icon",
				help : "choose yes if you wish to have icons displayed with your quote.",
				controlType : "select-control",
				selectValues : [ 'yes' ,'no'],
				defaultValue : 'yes',
				defaultText : 'yes'
			},
			

			{
				label : "Custom Text Color",
				id : "textcolor",
				help : 'Or you can also choose your own color to use as the text color',
				controlType : "color-control"
			},
			{
				label : "Cite Name",
				id : "cite",
				help : 'This is the name of the author. It will display at the end of the quote.',
				controlType : "text-control"
			}],
	defaultContent : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac luctus ligula. Phasellus a ligula blandit",
	shortcode : "dt_sc_pullquote"
};