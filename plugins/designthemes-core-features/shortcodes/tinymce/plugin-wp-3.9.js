(function(){
	var dummy_conent = "Lorem ipsum dolor sit amet, consectetur"
				+ " adipiscing elit. Morbi hendrerit elit turpis,"
				+ " a porttitor tellus sollicitudin at."
				+ " Class aptent taciti sociosqu ad litora "
				+ " torquent per conubia nostra,"
				+ " per inceptos himenaeos.";

	var dummy_tabs = '<br>[dt_sc_tab title="Tab 1"]'
					+ "<br>" + dummy_conent + "<br>" + '[/dt_sc_tab]' + "<br>"
					+ '[dt_sc_tab title="Tab 2"]' + "<br>"
					+ dummy_conent + "<br>" + '[/dt_sc_tab]' + "<br>"
					+ '[dt_sc_tab title="Tab 3"]' + "<br>"
					+ dummy_conent + "<br>" + '[/dt_sc_tab]<br>';

	var testimonal = '[dt_sc_testimonial name="John Doe" role="Cambridge Telcecom" type=" / type2 / type3" enable_rating="false / true" class=""]'+dummy_conent+'[/dt_sc_testimonial]';

	// Add Shortcode button
	tinymce.PluginManager.add("DTCoreShortcodePlugin",function( editor , url ) { 

		editor.on('init', function() {
			editor.addCommand("scnOpenDialog", function(obj) {
				scnSelectedShortcodeType = obj.identifier;
				jQuery.get(url + "/dialog.php", function(b) {
					jQuery("#scn-dialog").remove();
					jQuery("body").append(b);
					jQuery("#scn-dialog").hide();
					var f = jQuery(window).width();
					b = jQuery(window).height();
					f = 720 < f ? 720 : f;
					f -= 80;
					b -= 84;
					tb_show("Insert Shortcode", "#TB_inline?width=800"+ "&height=400&inlineId=scn-dialog");
					jQuery("#scn-options h3:first").text("Customize the " + obj.title + " Shortcode");
				});
			});
		});

		editor.addButton('designthemes_sc_button',{
			title: "DT Shortcode",
			icon: "dt-icon",
			type: "menubutton",
			menu:[
				{ text: 'Accordion',
					menu : [
						{ text: 'Default', onclick: function(e){
							e.stopPropagation();
							var content = "[dt_sc_accordion_group]"
								+'<br>[dt_sc_toggle title="Accordion 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Accordion 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Accordion 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+"<br>[/dt_sc_accordion_group]";
								editor.insertContent(content);
							}
						},

						{ text: 'Framed', onclick: function(e){
							e.stopPropagation();
							var content = "[dt_sc_accordion_group]"
								+'<br>[dt_sc_toggle_framed title="Accordion 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Accordion 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Accordion 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+"<br>[/dt_sc_accordion_group]";
							editor.insertContent(content);
							}
						}
					]
				},

				{ text: 'Animation' , onclick: function() {

						editor.windowManager.open({

							title : "Add Animation",
							body : [

								{ type: 'listbox', name:'effect', label:'Choose Effect',values:[
									{ text: 'Flash', value : 'flash' },							{ text: 'Shake', value : 'shake' },							{ text: 'Bounce', value : 'bounce' },
									{ text: 'Tada', value : 'tada' },							{ text: 'Swing', value : 'swing'},							{ text: 'Wobble', value : 'wobble' },
									{ text: 'Pulse', value : 'pulse' },							{ text: 'Flip', value : 'flip' },							{ text: 'Flip In X Axis', value : 'flipInX' },
									{ text: 'Flip Out X Axis', value : 'flipOutX' },			{ text: 'Flip In Y Axis', value : 'flipInY' },				{ text: 'Flip Out Y Axis', value : 'flipOutY' },
									{ text: 'fadeIn', value:'fadeIn'},							{ text: 'fadeInUp', value:'fadeInUp'},						{ text: 'fadeInDown', value:'fadeInDown'},
									{ text: 'fadeInLeft', value:'fadeInLeftfadeInLeft'},		{ text: 'fadeInRight', value:'fadeInRight'},				{ text: 'fadeInUpBig', value:'fadeInUpBig'},
									{ text: 'fadeInDownBig', value:'fadeInDownBig'},			{ text: 'fadeInLeftBig', value:'fadeInLeftBig'},			{ text: 'fadeInRightBig', value:'fadeInRightBig'},
									{ text: 'fadeOut', value:'fadeOut'},						{ text: 'fadeOutUp', value:'fadeOutUp'},					{ text: 'fadeOutDown', value:'fadeOutDown'},
									{ text: 'fadeOutLeft', value:'fadeOutLeft'},				{ text: 'fadeOutRight', value:'fadeOutRight'},				{ text: 'fadeOutUpBig', value:'fadeOutUpBig'},
									{ text: 'fadeOutDownBig', value:'fadeOutDownBig'},			{ text: 'fadeOutLeftBig', value:'fadeOutLeftBig'},			{ text: 'fadeOutRightBig', value:'fadeOutRightBig'},
									{ text: 'bounceIn', value:'bounceIn'},						{ text: 'bounceInUp', value:'bounceInUp'},					{ text: 'bounceInDown', value:'bounceInDown'},
									{ text: 'bounceInLeft', value:'bounceInLeft'},				{ text: 'bounceInRight', value:'bounceInRight'},			{ text: 'bounceOut', value:'bounceOut'},
									{ text: 'bounceOutUp', value:'bounceOutUp'},				{ text: 'bounceOutDown', value:'bounceOutDown'},			{ text: 'bounceOutLeft', value:'bounceOutLeft'},
									{ text: 'bounceOutRight', value:'bounceOutRight'},			{ text:'rotateIn', value:'rotateIn'},						{ text:'rotateInUpLeft', value:'rotateInUpLeft'},		
									{ text:'rotateInDownLeft', value:'rotateInDownLeft'},		{ text:'rotateInUpRight', value:'rotateInUpRight'},			{ text:'rotateInDownRight', value:'rotateInDownRight'},		
									{ text:'rotateOut', value:'rotateOut'},						{ text:'rotateOutUpLeft', value:'rotateOutUpLeft'},			{ text:'rotateOutDownLeft', value:'rotateOutDownLeft'},		
									{ text:'rotateOutUpRight', value:'rotateOutUpRight'},		{ text:'rotateOutDownRight', value:'rotateOutDownRight'},	{ text:'hinge', value:'hinge'},		
									{ text:'rollIn', value:'rollIn'},							{ text:'rollOut', value:'rollOut'},							{ text:'lightSpeedIn', value:'lightSpeedIn'},		
									{ text:'lightSpeedOut', value:'lightSpeedOut'},				{ text:'slideDown', value:'slideDown'},						{ text:'slideUp', value:'slideUp'},		
									{ text:'slideLeft', value:'slideLeft'},						{ text:'slideRight', value:'slideRight'},					{ text:'slideExpandUp', value:'slideExpandUp'},
									{ text:'expandUp', value:'expandUp'},						{ text:'expandOpen', value:'expandOpen'},					{ text:'bigEntrance', value:'bigEntrance'},		
									{ text:'hatch', value:'hatch'},								{ text:'floating', value:'floating'},						{ text:'tossing', value:'tossing'},		
									{ text:'pullUp', value:'pullUp'},							{ text:'pullDown', value:'pullDown'},						{ text:'stretchLeft', value:'stretchLeft'},
									{ text:'stretchRight', value:'stretchRight'}],
								},

								{ type: 'textbox', name:'delay',label : "Time Delay"},
							],
							onsubmit: function(e) {
								editor.insertContent('[dt_sc_animation effect="'+e.data.effect +'" delay="'+ e.data.delay+'"] Add Content to Animate [/dt_sc_animation]');
							}
						});	}
				},

				{ text: 'Button',
					onclick: function(e) {
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "button"});
					}
				},

				{ text: 'Block Quote',
					onclick: function(e) {
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "blockquote"});
					}
				},

				{ text: 'Call Out Button',
					onclick: function() {

						editor.windowManager.open({
							title: "Call Out Buttons",
							body:[

								{
									type: 'listbox',
									name: 'type',
									label:'Type',
									values:[
										{ text: 'Type 1', value: 'type1' },
										{ text: 'Type 2', value: 'type2' },
										{ text: 'Type 3', value: 'type3' },
										{ text: 'Type 4', value: 'type4' },
										{ text: 'Type 5', value: 'type5' },
									],
								},

								{ type:'textbox', name:'button_text', label: 'Button Label'},

								{ type: 'textbox', name: 'link', label: 'Button link', value: '#' },

								{ type: 'textbox', name: 'icon', label: 'Font Awesome Icon', value: 'fa-heart' },

								{
									type: 'listbox',
									name: 'target',
									label:'Target',
									values:[
										{ text: 'Blank', value: '_blank' },
										{ text: 'New', value: '_new' },
										{ text: 'Parent', value: '_parent' },
										{ text: 'Self', value: '_self' },
										{ text: 'Top', value: '_top' },
									],
								},
							],
							onsubmit: function(e){
								editor.insertContent('[dt_sc_callout_box type="'+e.data.type+'" icon="'+e.data.icon+'" link="'+e.data.link+'" target="'+e.data.target+'" button_text="'+e.data.button_text+'"]<h4>Our Technological services has been improved vastly</h4><h5>Come Experience the real life situations of saving life</h5>[/dt_sc_callout_box]');
							}
						});
					}
				},

				{ text: 'Column Layout', 
					onclick: function(e) {
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "column"});
					}
				},

				{ text: 'Colored Box',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "coloredbox"});
					}
				},

				{ text : 'Contact Info', menu :[

					{ text: 'Address', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_address line1="No: 58 A, East Madison St" line2="Baltimore, MD, USA"/]');
					}},

					{ text: 'Phone', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_phone phone="+1 200 258 2145"/]');
					}},

					{ text: 'Mobile', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_mobile mobile="+91 99941 49897"/]');
					}},

					{ text: 'Fax', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_fax fax="+1 100 458 2345"/]');
					}},

					{ text: 'Email', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_email emailid="yourname@somemail.com"/]');
					}},

					{ text: 'Web', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_web url="http://www.google.com"/]');
					}},
				] },
				
				{
					text: 'Courses', 
					menu: [
						{
							text: 'Custom Posts',
							menu: [
								{
									text: 'List View',
									onclick: function(){
										editor.selection.setContent('[dt_sc_courses limit="-1" course_type="" carousel="false" categories="" layout_view="list" columns="1/2" /]');
									}
								},
								{
									text: 'Grid View',
									onclick: function(){
										editor.selection.setContent('[dt_sc_courses limit="-1" course_type="" carousel="false" categories="" layout_view="grid" columns="2/3" /]');
									}
								},
								{
									text: 'Search',
									onclick: function(){
										editor.selection.setContent('[dt_sc_courses_search  title="Search Courses" post_per_page="-1" /]');
									}
								},
							]						
						},
						{
							text: 'Sensei',
							onclick: function(){
								editor.selection.setContent('[dt_sc_courses_sensei limit="-1" course_type="" carousel="true" categories="" /]');
							}
						},
					]						
				},
				
				{ text: 'Counter',
					onclick: function(e) {
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "counter"});
					}
				},

				{ text : 'Donut Chart', menu:[

					{ text: 'Small', onclick: function(e) {
						e.stopPropagation();
						editor.insertContent('[dt_sc_donutchart_small title="Lorem" bgcolor="#808080" fgcolor="#4bbcd7" percent="70"/]');
					} },


					{ text: 'Medium', onclick: function(e) {
						e.stopPropagation();
						editor.insertContent('[dt_sc_donutchart_medium title="Lorem" bgcolor="#808080" fgcolor="#7aa127" percent="65"/]');
					} },


					{ text: 'Large', onclick: function(e) {
						e.stopPropagation();
						editor.insertContent('[dt_sc_donutchart_large title="Lorem" bgcolor="#808080" fgcolor="#a23b6f" percent="50"/]');
					} },
				] },

				{ text: 'Drop Cap',
					onclick: function( e ){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "dropcap"});
					}
				},
				
				{ text : 'Dividers', menu:[

					{ text: 'Clear', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_clear]');
					}},

					{ text: 'Bordered Horizontal Rule', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_border]');
					}},

					{ text: 'Horizontal Rule', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr]');
					}},

					{ text: 'Horizontal Rule Medium', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_medium]');
					}},

					{ text: 'Horizontal Rule Large', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_large]');
					}},

					{ text: 'Horizontal Rule with top link', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr top]');
					}},

					{ text: 'Whitespace', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_invisible]');
					}},

					{ text: 'Whitespace Small', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_invisible_small]');
					}},

					{ text: 'Whitespace Medium', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_invisible_medium]');
					}},

					{ text: 'Whitespace Large', onclick: function(e){
						e.stopPropagation();
						editor.insertContent('[dt_sc_hr_invisible_large]');
					}},
				] },

				{
					text: 'Events',
					onclick: function(){
						editor.selection.setContent('<br>[dt_sc_events limit="-1" carousel="true" category_ids="" /]<br>');
					}
				},

				{ text: 'Full Width Section', 
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "fullwidth"});
					}
				},

				{ text: 'Full Width Video',
					onclick: function(){

						editor.windowManager.open({
							title: "Add Full Width  Video Section",
							body:[
								{ type:'textbox', label:'MP4', name: 'mp4'},
								{ type:'textbox', label:'WEBM', name: 'webm'},
								{ type:'textbox', label:'OGV', name: 'ogv'},
								{ type:'textbox', label:'Poster Image', name: 'poster'},
								{ type:'textbox', name:'backgroundimage', label: 'Background Image'},
								{ type:'textbox', name:'paddingtop', label: 'Padding Top( in px)'},
								{ type:'textbox', name:'paddingbottom', label: 'Padding Bottom( in px)'},
								{ type:'textbox', name:'class', label: 'CSS Class'},
							],
							onsubmit: function(e){
								editor.insertContent('[dt_sc_fullwidth_video mp4="'+e.data.mp4+'" webm="'+e.data.we+'" ogv="'+e.data.ogv+'" poster="'+e.data.po+'" backgroundimage="'+e.data.backgroundimage+'" paddingtop="'+e.data.paddingtop+'" paddingbottom="'+e.data.paddingbottom+'" class="'+e.data.class+'"][/dt_sc_fullwidth_video]');
							}
						});
					}
				}, 

				{ text: 'Icon Boxes', 
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "iconbox"});
					}
				},

				{ text: 'Lists',
					menu :[
						{
							text: 'Ordered List',
							onclick : function() {
								editor.windowManager.open({
									title: "Add New Stylish Ordered List",
									body: [
										{
											type: 'listbox',
											name: 'style',
											label:'Style',
											values:[
												{ text: 'Decimal', value: 'decimal' }, { text: 'Decimals With Leading Zero', value: 'decimal-leading-zero' }, { text:'Lower Alpha', value:'lower-alpha'},
												{ text:'Lower Roman', value:'lower-roman'}, { text:'Upper Alpha', value:'upper-alpha'},{ text:'Upper Roman', value:'upper-roman'}
											],
										},

										{
											type: 'listbox',
											name: 'variation',
											label: 'Variation',

											values:[
												{ text: 'Default', value: '' },		{ text: 'Avocado', value: 'avocado' },	{ text: 'Black', value: 'black' }, { text: 'Blue', value: 'blue' },
												{ text: 'Blueiris', value: 'blueiris' },		{ text: 'Blueturquoise', value: 'blueturquoise' },	{ text: 'Brown', value: 'brown' }, { text: 'Burntsienna', value: 'burntsienna' },
												{ text: 'Chillipepper', value: 'chillipepper' },		{ text: 'Emerald', value: 'emerald' },	{ text: 'Eggplant', value: 'eggplant' }, { text: 'Electric blue', value: 'electricblue' },
												{ text: 'Graas green', value: 'graasgreen' },		{ text: 'Gray', value: 'gray' },	{ text: 'Green', value: 'green' }, { text: 'Orange', value: 'orange' },
												{ text: 'Pale brown', value: 'palebrown' },		{ text: 'Pink', value: 'pink' },	{ text: 'radiantorchid', value: 'radiantorchid' }, { text: 'Red', value: 'red' },
												{ text: 'Sky Blue', value: 'skyblue' },		{ text: 'Yellow', value: 'yellow' },
											],
										},
									],
									onsubmit: function(e){
										var defaultContent =  "<ol>"
												+ "<li>Lorem ipsum dolor sit </li>"
												+ "<li>Praesent convallis nibh</li>"
												+ "<li>Nullam ac sapien sit</li>"
												+ "<li>Phasellus auctor augue</li></ol>";

										editor.insertContent('[dt_sc_fancy_ol style="'+e.data.style+'" variation="'+e.data.variation+'"]'+ defaultContent+'[/dt_sc_fancy_ol]');
									}
								});
							}
						},

						{
							text: 'Unordered List',
							onclick : function(){
								editor.windowManager.open({
									title: "Add New Stylish Unordered List",
									body: [

										{
											type: 'listbox',
											name: 'style',
											label:'Style',
											values:[
												{ text: 'Arrow', value: 'arrow' },					{ text: 'Rounded Arrow', value: 'rounded-arrow' },			{ text: 'Double Arrow', value: 'double-arrow' },
												{ text: 'Heart', value: 'heart' },					{ text: 'Trash', value: 'trash' },							{ text: 'Star', value: 'star' },
												{ text: 'Tick', value: 'tick' },					{ text: 'Rounded Tick', value: 'rounded-tick' },			{ text: 'Cross', value: 'cross' },
												{ text: 'Rounded Cross', value: 'rounded-cross' },	{ text: 'Rounded Question', value: 'rounded-question' },	{ text: 'Rounded Info', value: 'rounded-info' },
												{ text: 'Delete', value: 'delete' },				{ text: 'Warning', value: 'warning' },						{ text: 'Comment', value: 'comment' },
												{ text: 'Edit', value: 'edit' },					{ text: 'Share', value: 'share' },							{ text: 'Plus', value: 'plus' },
												{ text: 'Rounded Plus', value: 'Rounded Plus' },	{ text: 'Minus', value: 'minus' },							{ text: 'Rounded minus', value: 'rounded-minus' },
												{ text: 'Asterisk', value: 'asterisk' },			{ text: 'Cart', value: 'cart' },							{ text: 'Folder', value: 'folder' },
												{ text: 'Folder Open', value: 'folder-open' },		{ text: 'Desktop', value: 'desktop' },						{ text: 'Tablet', value: 'tablet' },
												{ text: 'Mobile', value: 'mobile' },				{ text: 'Reply', value: 'reply' },							{ text: 'Quote', value: 'quote' },
												{ text: 'Mail', value: 'mail' },					{ text: 'External Link', value: 'external-link' },			{ text: 'Adjust', value: 'adjust' },

												{ text: 'Pencil', value: 'pencil' },				{ text: 'Print', value: 'print' },							{ text: 'Tag', value: 'tag' },
												{ text: 'Thumbs Up', value: 'thumbs-up' },			{ text: 'Thumbs Down', value: 'thumbs-down' },				{ text: 'Time', value: 'time' },
												{ text: 'Globe', value: 'globe' },					{ text: 'Pushpin', value: 'pushpin' },						{ text: 'Map Marker', value: 'map-marker' },
												{ text: 'Link', value: 'link' },					{ text: 'Paper Clip', value: 'paper-clip' },				{ text: 'Download', value: 'download' },
												{ text: 'Key', value: 'key' },						{ text: 'Search', value: 'search' },						{ text: 'Rss', value: 'rss' },
												{ text: 'Twitter', value: 'twitter' },				{ text: 'Facebook', value: 'facebook' },					{ text: 'Linkedin', value: 'linkedin' },
												{ text:'Google Plus', value:'google-plus'}
											],
										},

										{
											type: 'listbox',
											name: 'variation',
											label: 'Variation',

											values:[
												{ text: 'Default', value: '' },		{ text: 'Avocado', value: 'avocado' },	{ text: 'Black', value: 'black' }, { text: 'Blue', value: 'blue' },
												{ text: 'Blueiris', value: 'blueiris' },		{ text: 'Blueturquoise', value: 'blueturquoise' },	{ text: 'Brown', value: 'brown' }, { text: 'Burntsienna', value: 'burntsienna' },
												{ text: 'Chillipepper', value: 'chillipepper' },		{ text: 'Emerald', value: 'emerald' },	{ text: 'Eggplant', value: 'eggplant' }, { text: 'Electric blue', value: 'electricblue' },
												{ text: 'Graas green', value: 'graasgreen' },		{ text: 'Gray', value: 'gray' },	{ text: 'Green', value: 'green' }, { text: 'Orange', value: 'orange' },
												{ text: 'Pale brown', value: 'palebrown' },		{ text: 'Pink', value: 'pink' },	{ text: 'radiantorchid', value: 'radiantorchid' }, { text: 'Red', value: 'red' },
												{ text: 'Sky Blue', value: 'skyblue' },		{ text: 'Yellow', value: 'yellow' },
											],
										},
									],
									onsubmit: function(e){
										var defaultContent =  "<ul>"
												+ "<li>Lorem ipsum dolor sit </li>"
												+ "<li>Praesent convallis nibh</li>"
												+ "<li>Nullam ac sapien sit</li>"
												+ "<li>Phasellus auctor augue</li></ul>";

										editor.insertContent('[dt_sc_fancy_ul style="'+e.data.style+'" variation="'+e.data.variation+'"]'+ defaultContent+'[/dt_sc_fancy_ul]');
									}
								});
							}
						},
					]
				},

				{ text: 'Post',
					menu:[
						{ text: 'Single Post',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('<br>[dt_sc_post id="1" read_more_text="Read More" excerpt_length="10"/]');
							}
						},
						{ text: 'Recent Posts',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('<br>[dt_sc_recent_post count="3" columns="3" read_more_text="Read More" excerpt_length="10"/]');
							}
						}
					]
				},

				{ text:'Pull Quote',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "pullquote"});
					}
				},

				{ text:'Pricing Table',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "pricingtable"});
					}
				},

				{ text: 'Progress Bar',
					menu:[

						{ text:'Active', 
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_progressbar value="45" type="progress-striped-active"] consectetur [/dt_sc_progressbar]');
							}
						},

						{ text:'Standard',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_progressbar value="85" type="standard" color="#9c59b6"] consectetur [/dt_sc_progressbar]');
							}
						},

						{ text:'Stripe',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_progressbar value="75" type="progress-striped" color=""] consectetur[/dt_sc_progressbar]');
							}
						},
					]
				},

				{ text:'Info Graphics Bar',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "infographicbar"});
					}
				},

				{
					text: 'Timeline',
					onclick: function(){
						editor.selection.setContent('[dt_sc_timeline_section]<br>'
							+'[dt_sc_timeline align="left" class=""]<br>'
							+'[dt_sc_timeline_item fontawesome_icon="home" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]<br>'
							+'Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.<br>'
							+'[/dt_sc_timeline_item]<br>'
							+'[/dt_sc_timeline]<br>'
							+'[dt_sc_timeline align="right" class=""]<br>'
							+'[dt_sc_timeline_item fontawesome_icon="eye" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]<br>'
							+'Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.<br>'
							+'[/dt_sc_timeline_item]<br>'
							+'[/dt_sc_timeline]<br>'
							+'[dt_sc_timeline align="left" class=""]<br>'
							+'[dt_sc_timeline_item fontawesome_icon="cogs" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]<br>'
							+'Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.<br>'
							+'[/dt_sc_timeline_item]<br>'
							+'[/dt_sc_timeline]<br>'
							+'[dt_sc_timeline align="right" class=""]<br>'
							+'[dt_sc_timeline_item fontawesome_icon="institution" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]<br>'
							+'Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.<br>'
							+'[/dt_sc_timeline_item]<br>'
							+'[/dt_sc_timeline]<br>'
							+'[/dt_sc_timeline_section]'
						);

					}
				},

				{ text: 'Tabs',
					menu:[
						{ text:'Horizontal',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent("[dt_sc_tabs_horizontal]" + dummy_tabs + "[/dt_sc_tabs_horizontal]");
							}
						},
						{ text:'Vertical',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent("[dt_sc_tabs_vertical]" + dummy_tabs+ "[/dt_sc_tabs_vertical]");
							}
						}
					]
				},

				{ text: 'Title',
					menu:[
						{
							text:'H1',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h1]Lorem ipsum dolor sit amet[/dt_sc_h1]');
							}
						},

						{
							text:'H2',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h2]Lorem ipsum dolor sit amet[/dt_sc_h2]');
							}
						},
						{
							text:'H3',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h3]Lorem ipsum dolor sit amet[/dt_sc_h3]');
							}
						},
						{
							text:'H4',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h4]Lorem ipsum dolor sit amet[/dt_sc_h4]');
							}
						},
						{
							text:'H5',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h5]Lorem ipsum dolor sit amet[/dt_sc_h5]');
							}
						},
						{
							text:'H6',
							onclick:function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_h6]Lorem ipsum dolor sit amet[/dt_sc_h6]');
							}
						},
					]
				},

				{ text:'Title Box',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "box"});
					}
				},

				{ text: 'Toggle',
					menu:[
						{
							text: 'Default',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_toggle title="Toggle 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
										+'<br>[dt_sc_toggle title="Toggle 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
										+'<br>[dt_sc_toggle title="Toggle 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]");
							}
						},

						{
							text: 'Framed',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_toggle_framed title="Toggle 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
										+'<br>[dt_sc_toggle_framed title="Toggle 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
										+'<br>[dt_sc_toggle_framed title="Toggle 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]");
							}
						},
					]
				},

				{ text:'Tool tip',
					onclick: function(e){
						e.stopPropagation();
						tinyMCE.activeEditor.execCommand("scnOpenDialog", {title: this.text() ,identifier: "tooltip"});
					}
				},

				{ text:'Others',
					menu:[

						{ text:'Portfolio Item',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_portfolio_item id=""/]');
							}
						},

						{ text:'Portfolios From Category',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_portfolios category_id="9,10" column="2/3/4/5/6" count="-1"/]');
							}
						},

						{ text:'Team',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_team name="DesignThemes" role="Chief Programmer" image="http://placehold.it/500" twitter="#" facebook="#" google="#" linkedin="#"]<br><p>Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.</p>[/dt_sc_team]');
							}
						},

						{ text:'Teacher',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_teacher columns="1/2/3/4/5" limit="4" /]');
							}
						},						

						{ text:'Testimonial',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent(testimonal);
							}
						},

						{ text:'Testimonial Carousel',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_testimonial_carousel]<br>'
									+'<ul>'
									+'<li>'+testimonal+'</li>'
									+'<li>'+testimonal+'</li>'
									+'<li>'+testimonal+'</li>'
									+'</ul>'
									+'<br>[/dt_sc_testimonial_carousel]');
							}
						},

						{ text:'Clients Carousel',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_clients_carousel]<br>'
									+'<ul>'
									+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 1" title="Client 1"/></a></li>'
									+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 2" title="Client 2"/></a></li>'
									+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 3" title="Client 3"/></a></li>'
									+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 4" title="Client 4"/></a></li>'
									+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 5" title="Client 5"/></a></li>'
									+'</ul>'
									+'<br>[/dt_sc_clients_carousel]');
							}
						},
						
						{ text:'Plan A Visit Form',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_subscription_form image_url="" slider="LayerSlider / RevolutionSlider" slider_id="" title="Plan a Visit" submit_text="Submit" success_msg="Thanks for subscribing, we will contact you soon."  '
								+' error_msg="Mail not sent, please try again Later." subject="Subscription" enable_planavisit="true" contact_label="Inquiries" contact_number="0123456789" course_type="sensei / cpt"]');
							}
						},
						
						{ text:'Users Subscribed Courses',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_subscribed_courses hide_visit_count="" /]');
							}
						},

						{ text:'Newsletter Section',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_newsletter_section title="Get in touch with us"]Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.[/dt_sc_newsletter_section]');
							}
						},

						{ text:'Slider Search Section',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_slider_search title="Title Comes Here" button_title="" button_link="#" disable_search="false" /]');
							}
						},

						{ text:'Certificate',
							onclick: function(e){
								e.stopPropagation();
								editor.insertContent('[dt_sc_certificate_template type="type1" certificate_title="" certificate_subtitle="" certificate_bg_image="" logo_topleft="" logo_topright="" logo_bottomcenter="" authority_sign="" authority_sign_name="" show_certificate_issueddate="yes"]This is to Certify that [dt_sc_certificate item="student_name" /] successfully completed the course with [dt_sc_certificate item="student_percent" /] on [dt_sc_certificate item="course_name" /][/dt_sc_certificate_template]');
							}
						},

					]
				}
			]
		});
	});// Add Shortcode button End
})();