scnShortcodeMeta = {
	attributes : [
		{
			label : "Background Color",
			id : "backgroundcolor",
			help : '',
			controlType : "color-control"
		},

		{
			label : "Background Image",
			id : "backgroundimage",
			help : "",
		},
		{
			label : "Background Opacity",
			id : "opacity",
			help : "Add opacity for background ( 0- 1 ) ",
		},

		{
			label : "Parallax Effect",
			id : 'parallax',
			help : 'Enable parallax effect for background',
			controlType : "select-control",
			selectValues : [ 'no', 'yes', ],
			defaultValue : 'no',
			defaultText : 'no (Default)'
		},


		{
			label : "Background Repeat",
			id : 'backgroundrepeat',
			help : '',
			controlType : "select-control",
			selectValues : [ 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ],
			defaultValue : 'no-repeat',
			defaultText : 'no-repeat (Default)'
		},
		{
			label : "Background Position",
			id : 'backgroundposition',
			help : '',
			controlType : "select-control",
			selectValues : [ 'left top', 'left center', 'left bottom', 'right top', 'right center','right bottom', 'center top', 'center center','center bottom'],
			defaultValue : 'left top',
			defaultText : 'left top (Default)'
		},
		{
			label : "Padding Top",
			id : "paddingtop",
			help : "In pixels",
		},
		{
			label : "Padding Bottom",
			id : "paddingbottom",
			help : "In pixels",
		},
		{
			label : "Text Color",
			id : "textcolor",
			help : 'You can change the color of the text.',
			controlType : "color-control"
		},
		{
			label : "Disable Container",
			id : 'disable_container',
			help : 'Yes to disable container class.',
			controlType : "select-control",
			selectValues : [ 'false', 'true', ],
			defaultValue : 'false',
			defaultText : 'false (Default)'
		},
		{
			label : "CSS Class",
			id : "class",
			help : "Add additional CSS Class",
		}				
	],
	defaultContent : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras cursus sollicitudin nunc nec rhoncus.",
	shortcode : "dt_sc_fullwidth_section"
};