(function() {
	tinymce.create("tinymce.plugins.DTCoreShortcodePlugin", {

		init : function(d, e) {

			d.addCommand("scnOpenDialog", function(a, c) {
				scnSelectedShortcodeType = c.identifier;
				jQuery.get(e + "/dialog.php", function(b) {
					jQuery("#scn-dialog").remove();
					jQuery("body").append(b);
					jQuery("#scn-dialog").hide();
					var f = jQuery(window).width();
					b = jQuery(window).height();
					f = 720 < f ? 720 : f;
					f -= 80;
					b -= 84;
					tb_show("Insert Shortcode", "#TB_inline?width=" + f
							+ "&height=" + b + "&inlineId=scn-dialog");
					jQuery("#scn-options h3:first").text(
							"Customize the " + c.title + " Shortcode");
				});
			});
		},

		getInfo : function() {
			return {
				longname : 'DesignThemes Core Shortcodes',
				author : 'DesignThemes',
				authorurl : 'http://themeforest.net/user/designthemes',
				infourl : '',
				version : "1.0"
			};
		},

		createControl : function(btn, e) {

			var dummy_conent = "Lorem ipsum dolor sit amet, consectetur"
					+ " adipiscing elit. Morbi hendrerit elit turpis,"
					+ " a porttitor tellus sollicitudin at."
					+ " Class aptent taciti sociosqu ad litora "
					+ " torquent per conubia nostra,"
					+ " per inceptos himenaeos.",
					
			dummy_tabs = '<br>[dt_sc_tab title="Tab 1"]'
					+ "<br>" + dummy_conent + "<br>" + '[/dt_sc_tab]' + "<br>"
					+ '[dt_sc_tab title="Tab 2"]' + "<br>"
					+ dummy_conent + "<br>" + '[/dt_sc_tab]' + "<br>"
					+ '[dt_sc_tab title="Tab 3"]' + "<br>"
					+ dummy_conent + "<br>" + '[/dt_sc_tab]<br>';

			if ("designthemes_sc_button" === btn) {

				var a = this;
				var btn = e.createSplitButton("designthemes_sc_buttons", {
					title : "Insert Shortcode",
					image : DTCorePlugin.tinymce_folder+ "/images/dt-icon.png",
					icons : false
				});

				btn.onRenderMenu
						.add(function(c, b) {

							/* Accordion */
							c = b.addMenu({title : "Accordion"});
							a.addImmediate(c, "Default",
								"[dt_sc_accordion_group]"
								+'<br>[dt_sc_toggle title="Accordion 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Accordion 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Accordion 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+"<br>[/dt_sc_accordion_group]");
							 									
							a.addImmediate(c, "Framed",
								"[dt_sc_accordion_group]"
								+'<br>[dt_sc_toggle_framed title="Accordion 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Accordion 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Accordion 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+"<br>[/dt_sc_accordion_group]");
							
							a.addWithDialog(b, "Animation", "animation");	
							a.addWithDialog(b, "Button", "button");
							a.addWithDialog(b, "Blockquote", "blockquote");
							
							/* Callout Button */
							a.addWithDialog(b, "Callout Button", "callout");
							
							a.addWithDialog(b, "Column Layout", "column");
							
							a.addWithDialog(b, "Colored Box", "coloredbox");
							
							/* Contact Info */
							c = b.addMenu({title: "Contact Info"});
							a.addImmediate(c, "Address",'<br>[dt_sc_address line1="No: 58 A, East Madison St" line2="Baltimore, MD, USA" /]<br>');
							a.addImmediate(c, "Phone",'<br>[dt_sc_phone phone="+1 200 258 2145" /]<br>');
							a.addImmediate(c, "Mobile",'<br>[dt_sc_mobile mobile="+91 99941 49897" /]<br>');
							a.addImmediate(c, "Fax",'<br>[dt_sc_fax fax="+1 100 458 2345" /]<br>');
							a.addImmediate(c, "Email",'<br>[dt_sc_email emailid="yourname@somemail.com" /]<br>');
							a.addImmediate(c, "Web",'<br>[dt_sc_web url="http://www.google.com" /]<br>');
							
							/* Courses */
							c = b.addMenu({title: "Courses"});
							d = c.addMenu({title : "Custom Posts"});
							a.addImmediate(d, "List View",'[dt_sc_courses limit="-1" course_type="" carousel="false" categories="" layout_view="list" columns="1/2" /]');
							a.addImmediate(d, "Grid View",'[dt_sc_courses limit="-1" course_type="" carousel="false" categories="" layout_view="grid" columns="2/3" /]');
							a.addImmediate(d, "Courses Search",'[dt_sc_courses_search  title="Search Courses" post_per_page="-1" /]');
							
							a.addImmediate(c, "Sensei",'[dt_sc_courses_sensei limit="-1" course_type="" carousel="true" categories="" /]');

							/* Counter */
							a.addWithDialog(b, "Counter", "counter");
							
							/* Donutchart */
							c = b.addMenu({title: "Donut Chart"});
							a.addImmediate(c, "Small",'<br>[dt_sc_donutchart_small title="Lorem" bgcolor="#808080" fgcolor="#4bbcd7" percent="70" /]<br>');
							a.addImmediate(c, "Medium",'<br>[dt_sc_donutchart_medium title="Lorem" bgcolor="#808080" fgcolor="#7aa127" percent="65" /]<br>');
							a.addImmediate(c, "Large",'<br>[dt_sc_donutchart_large title="Lorem" bgcolor="#808080" fgcolor="#a23b6f" percent="50" /]<br>');
							
							/* Dropcap Shortcodes */
							a.addWithDialog(b, "Dropcap", "dropcap");

							/* Dividers Shortcodes */
							c = b.addMenu({title : "Dividers"});
							
							a.addImmediate(c,"Clear","<br>[dt_sc_clear]<br>");

							a.addImmediate(c, "Bordered Horizontal Rule","<br>[dt_sc_hr_border] <br>");
							
							a.addImmediate(c, "Horizontal Rule","<br>[dt_sc_hr] <br>");
							
							a.addImmediate(c, "Horizontal Rule Medium","<br>[dt_sc_hr_medium] <br>");
							
							a.addImmediate(c, "Horizontal Rule Large","<br>[dt_sc_hr_large] <br>");
							
							a.addImmediate(c, "Horizontal Rule with top link","<br>[dt_sc_hr top] <br>");
							
							a.addImmediate(c, "Whitespace","<br>[dt_sc_hr_invisible] <br>");
							
							a.addImmediate(c, "Whitespace Small","<br>[dt_sc_hr_invisible_small] <br>");

							a.addImmediate(c, "Whitespace Medium","<br>[dt_sc_hr_invisible_medium] <br>");
							
							a.addImmediate(c, "Whitespace Large","<br>[dt_sc_hr_invisible_large] <br>");


							/* Events */
							a.addImmediate(b, "Events", '<br>[dt_sc_events limit="-1" carousel="true" category_ids="" /]<br>');


							/* Full Width Section */
							a.addWithDialog(b,"Full Width Section","fullwidth");

							/* Full Width Section */
							a.addWithDialog(b,"Full Width Video","video");


							/* Icon Box */
							a.addWithDialog(b, "Icon Boxes", "iconbox");

							/* List Shortcodes */
							c = b.addMenu({title : "Lists"});
							a.addWithDialog(c, "Ordered List", "orderedlist");
							a.addWithDialog(c, "Unordered List","unorderedlist");

							/* Posts */
							c = b.addMenu({ title: "Post"});
							a.addImmediate(c, "Single Post",'<br>[dt_sc_post id="1" read_more_text="Read More" excerpt_length="10"/]');
							a.addImmediate(c, "Recent Posts",'<br>[dt_sc_recent_post count="3" columns="3" read_more_text="Read More" excerpt_length="10"/]');

							/*Pullquotes*/							
							a.addWithDialog(b, "Pullquote", "pullquote");

							/*Pricing Table*/
							a.addWithDialog(b, "Pricing Table", "pricingtable");
							
							/* Progressbar*/
							c = b.addMenu({title:"Progress Bar"});
							a.addImmediate(c, "Standard","<br>[dt_sc_progressbar value='85' type='standard' color='#9c59b6'] consectetur[/dt_sc_progressbar]<br>");
							a.addImmediate(c, "Stripe","<br>[dt_sc_progressbar value='75' type='progress-striped' color=''] consectetur[/dt_sc_progressbar]<br>");
							a.addImmediate(c, "Active","<br>[dt_sc_progressbar value='45' type='progress-striped-active'] consectetur[/dt_sc_progressbar]<br>");
							
							/* Info Graphics Progress bar*/
							a.addWithDialog(b, "Info Graphics Bar", "infographicbar");

							/* Timeline */
							a.addImmediate(b, "Timeline", '[dt_sc_timeline_section]<br>'
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

							/* Tab */
							c = b.addMenu({title : "Tabs"});
							a.addImmediate(c, "Horizontal","[dt_sc_tabs_horizontal]" + dummy_tabs+ "[/dt_sc_tabs_horizontal]");

							a.addImmediate(c, "Vertical","[dt_sc_tabs_vertical]" + dummy_tabs+ "[/dt_sc_tabs_vertical]");

							/* Title */
							c = b.addMenu({title : "Title"});
							a.addImmediate(c,"H1","[dt_sc_h1]Lorem ipsum dolor sit amet[/dt_sc_h1]");
							a.addImmediate(c,"H2","[dt_sc_h2]Lorem ipsum dolor sit amet[/dt_sc_h2]");
							a.addImmediate(c,"H3","[dt_sc_h3]Lorem ipsum dolor sit amet[/dt_sc_h3]");
							a.addImmediate(c,"H4","[dt_sc_h4]Lorem ipsum dolor sit amet[/dt_sc_h4]");
							a.addImmediate(c,"H5","[dt_sc_h5]Lorem ipsum dolor sit amet[/dt_sc_h5]");
							a.addImmediate(c,"H6","[dt_sc_h6]Lorem ipsum dolor sit amet[/dt_sc_h6]");

							a.addWithDialog(b, "Titled Box", "box");				

							/* Toggle */
							c = b.addMenu({title : "Toggle"});
							a.addImmediate(c, "Default",
								'[dt_sc_toggle title="Toggle 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Toggle 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]"
								+'<br>[dt_sc_toggle title="Toggle 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle]");
							a.addImmediate(c, "Framed",
								'[dt_sc_toggle_framed title="Toggle 1"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Toggle 2"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]"
								+'<br>[dt_sc_toggle_framed title="Toggle 3"]<br>'+ dummy_conent + "<br>[/dt_sc_toggle_framed]");
									
							/* Tooltip Shortcodes */
							a.addWithDialog(b, "Tooltip", "tooltip");							

							c = b.addMenu({title : "Others"});
							a.addImmediate(c,"Portfolio Item",'<br>[dt_sc_portfolio_item id=""/]<br>');
							a.addImmediate(c,"Portfolios From Category",'<br>[dt_sc_portfolios category_id="9,10" column="2/3/4/5/6" count="-1"/]<br>');
							a.addImmediate(c, "Team",'<br>[dt_sc_team name="DesignThemes" role="Chief Programmer" image="http://placehold.it/500" twitter="#" facebook="#" google="#" linkedin="#"]<br><p>Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.</p>[/dt_sc_team]<br>');
							a.addImmediate(c, "Teacher",'[dt_sc_teacher columns="1/2/3/4/5" limit="4" /]');
							
							var testimonal = '[dt_sc_testimonial name="John Doe" role="Cambridge Telcecom" type=" / type2 / type3" enable_rating="false / true" class=""]'+dummy_conent+'[/dt_sc_testimonial]';
							a.addImmediate(c, "Testimonial",'<br>'+testimonal+'<br>');
							a.addImmediate(c, "Testimonial Carousel",'<br>[dt_sc_testimonial_carousel]<br>'
										+'<ul>'
										+'<li>'+testimonal+'</li>'
										+'<li>'+testimonal+'</li>'
										+'<li>'+testimonal+'</li>'
										+'</ul>'
										+'<br>[/dt_sc_testimonial_carousel]<br>');

							a.addImmediate(c, "Clients Carousel",'<br>[dt_sc_clients_carousel]<br>'
										+'<ul>'
										+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 1" title="Client 1"/></a></li>'
										+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 2" title="Client 2"/></a></li>'
										+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 3" title="Client 3"/></a></li>'
										+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 4" title="Client 4"/></a></li>'
										+'<li><a href="#"><img src="http://placehold.it/163x116" alt="Client 5" title="Client 5"/></a></li>'
										+'</ul>'
										+'<br>[/dt_sc_clients_carousel]<br>');
										
							a.addImmediate(c, "Plan A Visit Form",'[dt_sc_subscription_form image_url="" slider="LayerSlider / RevolutionSlider" slider_id="" title="Plan a Visit" submit_text="Submit" success_msg="Thanks for subscribing, we will contact you soon."  '
								+' error_msg="Mail not sent, please try again Later." subject="Subscription" enable_planavisit="true" contact_label="Inquiries" contact_number="0123456789" course_type="sensei / cpt"]');
								
							a.addImmediate(c, "Users Subscribed Courses",'[dt_sc_subscribed_courses hide_visit_count="" /]');
							a.addImmediate(c, "Newsletter Section",'[dt_sc_newsletter_section title="Get in touch with us"]Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.[/dt_sc_newsletter_section]');
							a.addImmediate(c, "Slider Search Section",'[dt_sc_slider_search type="type1" title="Title Comes Here" button_title="" button_link="#" disable_search="false" /]');
							a.addImmediate(c, "Certificate",'[dt_sc_certificate_template certificate_title="" certificate_subtitle="" certificate_bg_image="" logo_topleft="" logo_topright="" logo_bottomcenter="" authority_sign="" authority_sign_name="" show_certificate_issueddate="yes"]This is to Certify that [dt_sc_certificate item="student_name" /] successfully completed the course with [dt_sc_certificate item="student_percent" /] on [dt_sc_certificate item="course_name" /][/dt_sc_certificate_template]');
										
						});
				return btn;
			}
		},

		addImmediate : function(d, e, a) {
			d.add({title : e,onclick : function() { tinyMCE.activeEditor.execCommand("mceInsertContent", false,a);}});
		},

		addWithDialog : function(d, e, a) {
			d.add({title : e,
				onclick : function() {
					tinyMCE.activeEditor.execCommand("scnOpenDialog", false, {
						title : e,
						identifier : a
					});
				}
			});
		}
	});

	// add DTCoreShortcodePlugin plugin
	tinymce.PluginManager.add("DTCoreShortcodePlugin",
			tinymce.plugins.DTCoreShortcodePlugin);
})();