<?php
class DTCoreShortcodesDefination {
	
	function __construct() {
		
		/* Accordion Shortcode */
		add_shortcode ( "dt_sc_accordion_group", array ( $this, "dt_sc_accordion_group" ) );

		/* Button Shortcode */
		add_shortcode ( "dt_sc_button", array ( $this, "dt_sc_button" ) );

		/* BlockQuotes Shortcode */
		add_shortcode ( "dt_sc_blockquote", array ( $this, "dt_sc_blockquote" ) );

		/* Callout Box Shortcode */
		add_shortcode ( "dt_sc_callout_box", array ( $this, "dt_sc_callout_box" ));

		/* Columns Shortcode */
		add_shortcode ( "dt_sc_full_width", array ( $this, "dt_sc_columns" ) );
		
		add_shortcode ( "dt_sc_one_column", array ( $this, "dt_sc_columns" ) );
		
		add_shortcode ( "dt_sc_one_half", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_one_third", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_one_fourth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_one_fifth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_one_sixth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_two_sixth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_two_third", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_fourth", array ( $this, "dt_sc_columns") );

		add_shortcode ( "dt_sc_two_fifth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_fifth", array ( $this,"dt_sc_columns" ) );

		add_shortcode ( "dt_sc_four_fifth", array ( $this,"dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_sixth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_four_sixth", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_five_sixth", array ( $this, "dt_sc_columns" ) );

		/* Column with inner */
		add_shortcode ( "dt_sc_one_half_inner", array ( $this, "dt_sc_columns") );

		add_shortcode ( "dt_sc_one_third_inner", array ( $this, "dt_sc_columns") );

		add_shortcode ( "dt_sc_one_fourth_inner", array ( $this, "dt_sc_columns") );

		add_shortcode ( "dt_sc_one_fifth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_one_sixth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_two_sixth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_two_third_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_fourth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_two_fifth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_fifth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_four_four_inner", array (  $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_three_sixth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_four_sixth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_five_sixth_inner", array ( $this, "dt_sc_columns" ) );

		add_shortcode ( "dt_sc_four_fifth_inner", array (  $this, "dt_sc_columns" ) );

		/* Contact Information */
		#Address
		add_shortcode ( "dt_sc_address", array ( $this, "dt_sc_address") );
		
		#Phone
		add_shortcode ( "dt_sc_phone", array ( $this,"dt_sc_phone") );

		#Mobile
		add_shortcode ( "dt_sc_mobile", array ( $this, "dt_sc_mobile") );

		#Fax
		add_shortcode ( "dt_sc_fax", array ( $this, "dt_sc_fax" ) );
		
		#Email
		add_shortcode ( "dt_sc_email", array ( $this, "dt_sc_email" ) );

		#Web
		add_shortcode ( "dt_sc_web", array ( $this, "dt_sc_web") );
		/* Contact Information End */

		/* Clients Carousel Shortcode */
		add_shortcode ( "dt_sc_clients_carousel", array ( $this, "dt_sc_clients_carousel") );

		/* Donutchart Start */
		add_shortcode ( "dt_sc_donutchart_small", array ( $this,"dt_sc_donutchart") );
		
		add_shortcode ( "dt_sc_donutchart_medium", array ( $this, "dt_sc_donutchart") );

		add_shortcode ( "dt_sc_donutchart_large", array ( $this, "dt_sc_donutchart") );
		/* Donutchart End */
		
		/* Dividers */
		/* Clear Shortcode */
		add_shortcode ( "dt_sc_clear", array ( $this,"dt_sc_clear") );
		
		/* HR With Border */
		add_shortcode( "dt_sc_hr_border", array ( $this,"dt_sc_hr_border") );

		add_shortcode ( "dt_sc_hr", array ( $this, "dt_sc_dividers" ) );
		
		add_shortcode ( "dt_sc_hr_medium", array ( $this, "dt_sc_dividers" ) );
		
		add_shortcode ( "dt_sc_hr_large", array ( $this, "dt_sc_dividers" ) );
		
		add_shortcode ( "dt_sc_hr_invisible", array ( $this, "dt_sc_dividers" ) );
	
		add_shortcode ( "dt_sc_hr_invisible_small", array ( $this, "dt_sc_dividers" ) );

		add_shortcode ( "dt_sc_hr_invisible_medium", array ( $this,"dt_sc_dividers" ) );
		
		add_shortcode ( "dt_sc_hr_invisible_large", array ($this,"dt_sc_dividers" ) );
		/* Dividers End */
		
		/* Icon Boxes Shortcode */
		add_shortcode ( "dt_sc_icon_box", array ( $this,"dt_sc_icon_box" ) );
		/* Icon Boxes Shortcode End*/

		/* Icon Boxes Shortcode */
		add_shortcode ( "dt_sc_icon_box_colored", array ( $this, "dt_sc_icon_box_colored" ) );
		/* Icon Boxes Shortcode End*/
		
		/* Dropcap Shortcode */
		add_shortcode ( "dt_sc_dropcap", array ( $this, "dt_sc_dropcap" ) );
		
		/* Code Shortcode */
		add_shortcode ( "dt_sc_code", array ( $this, "dt_sc_code" ) );

		/* Ordered List Shortcode */
		add_shortcode ( "dt_sc_fancy_ol", array ( $this, "dt_sc_fancy_ol" ) );
		
		/* Unordered List Shortcode */
		add_shortcode ( "dt_sc_fancy_ul", array ( $this, "dt_sc_fancy_ul" ) );

		/* Pricing Table */
		add_shortcode ( "dt_sc_pricing_table", array ( $this, "dt_sc_pricing_table" ) );

		/* Pricing Table Item */
		add_shortcode ( "dt_sc_pricing_table_item", array ( $this, "dt_sc_pricing_table_item" ) );

		/* Progress Bar Shortcode */
		add_shortcode ( "dt_sc_progressbar", array ( $this, "dt_sc_progressbar" ) );

		/* Tabs */
		add_shortcode ( "dt_sc_tab", array ( $this, "dt_sc_tab" ) );

		add_shortcode ( "dt_sc_tabs_horizontal", array ( $this, "dt_sc_tabs_horizontal") );

		add_shortcode ( "dt_sc_tabs_vertical", array ( $this, "dt_sc_tabs_vertical" ) );

		/* Team Shortcode */
		add_shortcode ( "dt_sc_team", array ( $this, "dt_sc_team" ) );

		/* Testimonial Shortcode */
		add_shortcode ( "dt_sc_testimonial", array ( $this, "dt_sc_testimonial" ) );
		
		/* Testimonial Carousel Shortcode */
		add_shortcode ( "dt_sc_testimonial_carousel", array ( $this, "dt_sc_testimonial_carousel") );

		/* Title Shortcode */
		add_shortcode ( "dt_sc_h1", array ( $this, "dt_sc_title") );

		add_shortcode ( "dt_sc_h2", array ( $this, "dt_sc_title" ) );

		add_shortcode ( "dt_sc_h3", array ( $this, "dt_sc_title" ) );

		add_shortcode ( "dt_sc_h4", array ( $this, "dt_sc_title" ) );

		add_shortcode ( "dt_sc_h5", array ( $this, "dt_sc_title" ) );

		add_shortcode ( "dt_sc_h6", array ( $this, "dt_sc_title" ) );
		/* Title Shortcode End */

		/* Toggle Shortcode */
		add_shortcode ( "dt_sc_toggle", array ( $this, "dt_sc_toggle" ) );

		/* Toogle Framed Shortcode */
		add_shortcode ( "dt_sc_toggle_framed", array ( $this, "dt_sc_toggle_framed" ) );
		
		/* Titles Box Shortcode */
		add_shortcode ( "dt_sc_titled_box", array ( $this, "dt_sc_titled_box" ) );
		
		/* Tooltip Shortcode */
		add_shortcode ( "dt_sc_tooltip", array ( $this, "dt_sc_tooltip" ) );
		
		/* PullQuotes Shortcode */
		add_shortcode ( "dt_sc_pullquote", array ( $this, "dt_sc_pullquote" ) );

		/* Portfolio Shortcode */

		add_shortcode( "dt_sc_portfolio_item", array( $this, "dt_sc_portfolio_item" ));

		add_shortcode( "dt_sc_portfolios", array( $this, "dt_sc_portfolios" ));

		add_shortcode ( "dt_sc_infographic_bar", array ( $this, "dt_sc_infographic_bar" ) );

		/* Full width Shortcode*/
		add_shortcode("dt_sc_fullwidth_section", array ( $this, "dt_sc_fullwidth_section" ) );

		/* Full Width Video Shortcode */
		add_shortcode("dt_sc_fullwidth_video", array ( $this, "dt_sc_fullwidth_video" ));

		/* Animation */
		add_shortcode("dt_sc_animation", array ( $this, "dt_sc_animation" ) );

		/* Post And Recent Posts */
		add_shortcode("dt_sc_post", array ( $this, "dt_sc_post" ) );

		add_shortcode("dt_sc_recent_post", array ( $this, "dt_sc_recent_post" ) );
		
		/* Teachers Posts */
		add_shortcode("dt_sc_teacher", array ( $this, "dt_sc_teacher" ) );
		
		/* Sensei Featured Courses */	
		add_shortcode('dt_sc_courses_sensei', array ( $this, "dt_sc_courses_sensei" ));	
		
		/* Data counter */	
		add_shortcode('dt_sc_counter', array ( $this, "dt_sc_counter" ));	

		/* Events */	
		add_shortcode('dt_sc_events', array ( $this, "dt_sc_events" ));	

		/* Courses  */	
		add_shortcode('dt_sc_courses', array ( $this, "dt_sc_courses" ));	

		/* Courses Search */	
		add_shortcode('dt_sc_courses_search', array ( $this, "dt_sc_courses_search" ));	

		/* Timeline Section */	
		add_shortcode('dt_sc_timeline_section', array ( $this, "dt_sc_timeline_section" ));	

		/* Timeline */	
		add_shortcode('dt_sc_timeline', array ( $this, "dt_sc_timeline" ));	

		/* Timeline Item */	
		add_shortcode('dt_sc_timeline_item', array ( $this, "dt_sc_timeline_item" ));	

		/* Subscription Form */	
		add_shortcode('dt_sc_subscription_form', array ( $this, "dt_sc_subscription_form" ));	

		/* Subscribed Courses */	
		add_shortcode('dt_sc_subscribed_courses', array ( $this, "dt_sc_subscribed_courses" ));	
		
		/* Newsletter Section */	
		add_shortcode('dt_sc_newsletter_section', array ( $this, "dt_sc_newsletter_section" ));	
		
		/* Slider Search Section */	
		add_shortcode('dt_sc_slider_search', array ( $this, "dt_sc_slider_search" ));	
		
		/* Widget Shortcodes */
		add_shortcode ( "dt_sc_widgets", array ( $this, "dt_sc_widgets" ) );
		
		/* Do Shortcodes */
		add_shortcode ( "dt_sc_doshortcode", array ( $this, "dt_sc_doshortcode" ) );
	
		/* Resizeable Column */
		add_shortcode ( "dt_sc_resizable", array ( $this, "dt_sc_resizable" ) );

		add_shortcode ( "dt_sc_resizable_inner", array ( $this, "dt_sc_resizable" ) );
		
		/* Certificate Shortcode */
		add_shortcode ( "dt_sc_certificate", array ( $this, "dt_sc_certificate" ) );
	
		/* Certificate Template Shortcode */
		add_shortcode ( "dt_sc_certificate_template", array ( $this, "dt_sc_certificate_template" ) );
		
	}
	
	/**
	 *
	 * @param string $content        	
	 * @return string
	 */
	function dtShortcodeHelper($content = null) {
		$content = do_shortcode ( shortcode_unautop ( $content ) );
		$content = preg_replace ( '#^<\/p>|^<br \/>|<p>$#', '', $content );
		$content = preg_replace ( '#<br \/>#', '', $content );
		return trim ( $content );
	}
	
	/**
	 *
	 * @param string $dir        	
	 * @return multitype:
	 */
	function dtListImages($dir = null) {
		$images = array ();
		$icon_types = array ( 'jpg','jpeg','gif','png');
		
		if (is_dir ( $dir )) {
			$handle = opendir ( $dir );
			while ( false !== ($dirname = readdir ( $handle )) ) {
				
				if ($dirname != "." && $dirname != "..") {
					$parts = explode ( '.', $dirname );
					$ext = strtolower ( $parts [count ( $parts ) - 1] );
					
					if (in_array ( $ext, $icon_types )) {
						$option = $parts [count ( $parts ) - 2];
						$images [$dirname] = str_replace ( ' ', '', $option );
					}
				}
			}
			closedir ( $handle );
		}
		
		return $images;
	}
	
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_accordion_group($attrs, $content = null) {
		$out = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out = str_replace ( "<h5 class='dt-sc-toggle", "<h5 class='dt-sc-toggle-accordion ", $out );
		$out = "<div class='dt-sc-toggle-frame-set'>{$out}</div>";
		return $out;
	}


	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_button($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'size' => '', 'link' => '#', 'type' => '', 'target' => '', 'variation' => '', 'bgcolor' => '', 'textcolor' => '', 'class' =>'', 'timeline_button' => 'no'), $attrs ) );
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$size = ($size == 'xlarge') ? ' xlarge' : $size;
		$size = ($size == 'large') ? ' large' : $size;
		$size = ($size == 'medium') ? ' medium' : $size;
		$size = ($size == 'small') ? ' small' : $size;
		
		$target = empty($target) ? 'target="_blank"' : "target='{$target}' ";
		
		$variation = (($variation) && (empty ( $bgcolor ))) ? ' ' . $variation : '';
		
		$styles = array ();
		if ($bgcolor)
			$styles [] = 'background-color:' . $bgcolor . ';border-color:' . $bgcolor . ';';
		if ($textcolor)
			$styles [] = 'color:' . $textcolor . ';';
		$style = join ( '', array_unique ( $styles ) );
		$style = ! empty ( $style ) ? ' style="' . $style . '"' : '';

		$type = ( $type === "type2" ) ? "filled" : "";
		
		if($timeline_button == 'yes') {
			$btn_cls = "timeline-button";
		} else {
			$btn_cls = "dt-sc-button {$class} {$size} {$variation} {$type}";
		}
		
		if(preg_match('#^{{#', $link) === 1) {
			$link =  str_replace ( '{{', '[', $link );
			$link =  str_replace ( '}}', '/]', $link );
			$link = do_shortcode($link);
		}else{
			$link = esc_url ( $link );
		}		
		
		
		
		$out = "<a href='{$link}' {$target} class='{$btn_cls}' {$style}>{$content}</a>";
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_blockquote($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => "type1", 'align' => '', 'variation' => '', 'textcolor' => '', 'cite'=> '', 'role' =>''), $attrs ) );
		
		$class = array();
		if( preg_match( '/left|right|center/', trim( $align ) ) )
			$class[] = ' align' . $align;
		if( $variation)
			$class[] = ' ' . $variation;
		
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = ! empty ( $content ) ? "<q>{$content}</q>" : "";
		
		$cite = ! empty ( $cite ) ? '&ndash; ' .$cite : "";
		$role = ! empty ( $role ) ? '<br> <span>' . $role . '</span>' : "";

		$cite = !empty( $cite ) ? "<cite>$cite$role</cite>" : "";
		
		$style = ( $textcolor ) ? ' style="color:' . $textcolor . ';"' : '';
		$class = join( '', array_unique( $class ) );

		$out = "<blockquote class='{$type} {$class}' {$style}>{$content}{$cite}</blockquote>";
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_callout_box($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => "type1", 'link' => '#', 'button_text'=> __('Purchase Now','dt_themes'), 'icon' =>'', 'target' => '', 'class' => '' ), $attrs ) );

		$attribute = !empty($icon) ? "class='dt-sc-callout-box with-icon {$type} {$class}' " :" class='dt-sc-callout-box {$type} {$class}' ";

		$target = empty($target) ? 'target="_blank"' : "target='{$target}' ";
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		if(preg_match('#^{{#', $link) === 1) {
			$link =  str_replace ( '{{', '[', $link );
			$link =  str_replace ( '}}', '/]', $link );
			$link = do_shortcode($link);
		}else{
			$link = esc_url ( $link );
		}		
		
		$out = "<div {$attribute}>";
		$out .= ( !empty( $title ) ) ? "<h2>{$title}</h2>" : "";
		$out .= '<div class="column dt-sc-four-fifth first">';
		if( !empty( $icon ) ):
			$out .= '<div class="icon">';
			$out .= "<span class='fa {$icon}'></span>";
			$out .= '</div>';
		endif;
		$out .= $content;
		$out .= '</div>';
			
		$out .= '<div class="column dt-sc-one-fifth">';
		$out .= ( !empty($link) ) ? "<a href='{$link}' class='dt-sc-button small' {$target}>{$button_text}</a>" : "";
		$out .= '</div>';			
		$out .= "</div>";
		
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_columns($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'id' => '', 'class' => '', 'style' => '' , 'type' => '', 'animation' => '', 'animation_delay' => '' ), $attrs ) );
		
		$shortcodename = str_replace ( "_", "-", $shortcodename );
		$shortcodename = str_replace ( "-inner", "", $shortcodename );
		
		$danimation = !empty( $animation ) ? " data-animation='{$animation}' ": "";
		$ddelay = (!empty( $animation ) && !empty( $animation_delay )) ? " data-delay='{$animation_delay}' " : "";
		$danimate = !empty( $animation ) ? "animate": "";
		
		$id = ($id != '') ? " id = '{$id}'" : '';
		$style = !empty( $style ) ? " style='{$style}' ": "";
		$type = ( trim($type) === 'type2' ) ? "no-space" : "space";
		$first = (isset ( $attrs [0] ) && trim ( $attrs [0] == 'first' )) ? 'first' : '';
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out = "<div {$id} class='column {$shortcodename} {$class} {$type} {$danimate} {$first}' {$danimation} {$ddelay} {$style} >{$content}</div>";
		return $out;
	}

	/* Contact Information */
	
	/**
	 * Shortcode : Address
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_address($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'line1' => '', 'line2' => '', 'line3' => '', 'line4' => ''), $attrs ) );
				
		$out = '<div class="dt-sc-contact-info address">';
		$out .= "<div class='icon'><i class='fa fa-location-arrow'></i></div>";
		$out .= "<p>";
		$out .= ( !empty($line1) ) ? $line1 : "";
		$out .= ( !empty($line2) ) ? "<br>$line2" : "";
		$out .= ( !empty($line3) ) ? "<br>$line3" : "";
		$out .= ( !empty($line4) ) ? "<br>$line4" : "";
		$out .= "<p><span></span>";
		$out .= '</div>';
		
		return $out;
	}

	/**
	 * Shortcode : Phone
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_phone($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'phone' => ''), $attrs ) );

		$out = '<div class="dt-sc-contact-info">';
		$out .= "<div class='icon'><i class='fa fa-phone'></i></div>";
		$out .= ( !empty($phone) ) ?"<p>{$phone}</p>": "";
		$out .= "<span></span>";
		$out .= '</div>';
		
		return $out;
	}
	 
	/**
	 * Shortcode : Mobile
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_mobile($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'mobile' => ''), $attrs ) );
		
		$out = '<div class="dt-sc-contact-info">';
		$out .= "<div class='icon'><i class='fa fa-mobile-phone'></i></div>";
		$out .= ( !empty($mobile) ) ?"<p>{$mobile}</p>": "";
		$out .= "<span></span>";
		$out .= '</div>';
		
		return $out;
	}

	/**
	 * Shortcode : Fax
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_fax($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'fax' => ''), $attrs ) );

		$out = '<div class="dt-sc-contact-info">';
		$out .= "<div class='icon'><i class='fa fa-fax'></i></div>";
		$out .= ( !empty($fax) ) ? "<p>{$fax}</p>" : "";
		$out .= "<span></span>";
		$out .= '</div>';
		
		return $out;
	}

	/**
	 * Shortcode : Email id
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_email($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'emailid' => ''), $attrs ) );

		$out = '<div class="dt-sc-contact-info">';
		$out .= "<div class='icon'><i class='fa fa-envelope'></i></div>";
		$out .= ( !empty($emailid) ) ? "<p><a href='mailto:$emailid'>{$emailid}</a></p>" : "";
		$out .= "<span></span>";
		$out .= '</div>';
		return $out;
	}

	/**
	 * Shortcode : Website Url
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_web($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'url' => ''), $attrs ) );
		
		$out = '<div class="dt-sc-contact-info">';
		$out .= "<div class='icon'><i class='fa fa-globe'></i></div>";

		if( !empty( $url ) ) {
			$out .= "<p><a target='_blank' href='{$url}'>";
			$a = preg_replace('#^[^:/.]*[:/]+#i', '',urldecode( $url ));
			$out .=	preg_replace('!\bwww3?\..*?\b!', '', $a);
			$out .= "</a></p>";
		}
		$out .= "<span></span>";
		$out .= '</div>';
		
		return $out;
	}
	/* Contact Information End*/

	/* Client Carousel */
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_clients_carousel($attrs, $content = null) {
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = str_replace( '<ul>', "<ul class='dt-sc-partner-carousel'>", $content );
		
		
		$out = '<div class="dt-sc-partner-carousel-wrapper">';
		$out .= $content;
		$out .= '<div class="carousel-arrows">';
		$out .= '	<a href="" class="partner-prev"> </a>';
		$out .= '	<a href="" class="partner-next"> </a>';
		$out .= '</div>';
		$out .= '</div>';
		return $out;
	}

	/* Client Carousel End */
	
	/* Dividers */
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_clear($attrs, $content = null) {
		return '<div class="dt-sc-clear"></div>';
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_hr_border($attrs, $content = null) {
		return '<div class="dt-sc-hr-border"></div>';
	}


	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_donutchart($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'title' => '', 'bgcolor' => '', 'fgcolor' => '', 'percent' =>'30' ), $attrs ) );
		
		$size = "100";
		$size = ( "dt_sc_donutchart_medium" === $shortcodename ) ? "200" : $size;
		$size = ( "dt_sc_donutchart_large" === $shortcodename ) ? "300" : $size;
		
		$shortcodename = str_replace ( "_", "-", $shortcodename );
		$out = "<div class='{$shortcodename}'>";
		$out .= "<div class='dt-sc-donutchart' data-size='{$size}' data-percent='{$percent}' data-bgcolor='{$bgcolor}' data-fgcolor='$fgcolor'></div>";
		$out .= ( empty($title) ) ? $out : "<h5 class='dt-sc-donutchart-title'>{$title}</h5>";
		$out .= '</div>';
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_dividers($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'class' => '', 'top' => '' ), $attrs ) );
		
		if ("dt_sc_hr" === $shortcodename || "dt_sc_hr_medium" === $shortcodename || "dt_sc_hr_large" === $shortcodename) {
			
			$shortcodename = str_replace ( "_", "-", $shortcodename );
			
			$out = "<div class='{$shortcodename} {$class}'>";
			
			if ((isset ( $attrs [0] ) && trim ( $attrs [0] == 'top' ))) {
				
				$out = "<div class='{$shortcodename} top {$class}'>";
				$out .= "<a href='#top' class='scrollTop'><span class='fa fa-angle-up'></span>" . __ ( "top", 'dt_themes' ) . "</a>";
			}
			
			$out .= "</div>";
		} else {
			$shortcodename = str_replace ( "_", "-", $shortcodename );
			$out = "<div class='{$shortcodename}  {$class}'></div>";
		}
		return $out;
	}
	/* Dividers End*/
	
	/* Icon Boxes Shortcode */
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_icon_box($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'type' => '', 'fontawesome_icon' => '', 'stroke_icon' => '', 'custom_icon' => '', 'title' => '', 'link' => '', 'custom_bgcolor' => ''), $attrs ) );
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$type = trim($type);
		if($type == 'type13' && $custom_bgcolor != '') { $style = 'style="background-color:'.$custom_bgcolor.';"'; } else $style = '';
		
		$out =  "<div class='dt-sc-ico-content {$type}'>";
		if( !empty($fontawesome_icon) ){
			$out .= "<div class='custom-icon' {$style}> <span class='fa fa-{$fontawesome_icon}'> </span> </div>";
		} elseif( !empty($stroke_icon) ){
			$out .= "<div class='custom-icon' {$style}> <span class='icon $stroke_icon'> </span> </div>";
		}elseif( !empty($custom_icon) ){
			$out .= '<div class="custom-icon" '.$style.'><span><img src="'.$custom_icon.'" title="'.$title.'" alt="'.$title.'"></span></div>';
		}

		if(preg_match('#^{{#', $link) === 1) {
			$link =  str_replace ( '{{', '[', $link );
			$link =  str_replace ( '}}', '/]', $link );
			$link = do_shortcode($link);
		}else{
			$link = esc_url ( $link );
		}	

		$out .= empty( $title ) ? $out : "<h4><a href='{$link}' target='_blank'> {$title} </a></h4>";
		if($type != 'type13') $out .= $content;
		$out .= "</div>";
		return $out;
	}
	/* Icon Boxes Shortcode End*/

	/* Icon Boxes Colored Shortcode */
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_icon_box_colored($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'type' => '', 'fontawesome_icon' => '', 'custom_icon' => '', 'title' => '', 'bgcolor' => '' ), $attrs ) );
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$bgcolor = empty ( $bgcolor ) ? "" : " style='background:{$bgcolor};' ";
		
		$type = ( trim($type) === 'type1' ) ? "no-space" : "space";
		
		$out =  "<div class='dt-sc-colored-box {$type}' {$bgcolor}>";
		
		$icon = "";
		if( !empty($fontawesome_icon) ){
			$icon = "<span class='fa fa-{$fontawesome_icon}'> </span>";
		
		}elseif( !empty($custom_icon) ){
			$icon = "";	
		}
		
		$out .= "<h5>{$icon}{$title}</h5>";
		$out .= $content;
		$out .= "</div>";
		return $out;
	}
	/* Icon Boxes Colored Shortcode End*/


	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @param string $shortcodename        	
	 * @return string
	 */
	function dt_sc_dropcap($attrs, $content = null, $shortcodename = "") {
		extract ( shortcode_atts ( array ( 'type' => '', 'variation' => '', 'bgcolor' => '', 'textcolor' => '' ), $attrs ) );
		
		$type = str_replace ( " ", "-", $type );
		$type = "dt-sc-dropcap-".strtolower ( $type );
		
		$bgcolor = ( $type == 'dt-sc-dropcap-default') ? "" : $bgcolor;
		$variation = ( ( $variation ) && ( empty( $bgcolor ) ) ) ? ' ' . $variation : '';
		
		$styles = array();
		if($bgcolor) $styles[] = 'background-color:' . $bgcolor . ';';
		if($textcolor) $styles[] = 'color:' . $textcolor . ';border-color:' . $textcolor . ';';;
		$style = join('', array_unique( $styles ) );
		$style = !empty( $style ) ? ' style="' . $style . '"': '' ;
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$out = "<span class='dt-sc-dropcap $type {$variation}' {$style}>{$content}</span>";
		return $out;
	}
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_code($attrs, $content = null) {
		$array = array ( '[' => '&#91;', ']' => '&#93;', '/' => '&#47;', '<' => '&#60;', '>' => '&#62;', '<br />' => '&nbsp;');
		$content = strtr ( $content, $array );
		$out = "<pre>{$content}</pre>";
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return mixed
	 */
	function dt_sc_fancy_ol($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'style' => '', 'variation' => '', 'class' => '' ), $attrs ) );
		
		$style = ($style) ? trim ( $style ) : 'decimal';
		$class = ($class) ? trim ( $class ) : '';
		$variation = ($variation) ? ' ' . trim ( $variation ) : '';
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = str_replace ( '<ol>', "<ol class='dt-sc-fancy-list {$variation} {$class} {$style}'>", $content );
		$content = str_replace ( '<li>', '<li><span>', $content );
		$content = str_replace ( '</li>', '</span></li>', $content );
		return $content;
	}
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return mixed
	 */
	function dt_sc_fancy_ul($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'style' => '', 'variation' => '', 'class' => ''), $attrs ) );
		$style = ($style) ? trim ( $style ) : 'decimal';
		$class = ($class) ? trim ( $class ) : '';
		$variation = ($variation) ? ' ' . trim ( $variation ) : '';
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = str_replace ( '<ul>', "<ul class='dt-sc-fancy-list {$variation} {$class} {$style}'>", $content );
		return $content;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_pricing_table($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => 'type1' ), $attrs ) );
		
		$type = ( trim($type) === 'type1' ) ? "no-space" : "space";
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		return "<div class='dt-sc-pricing-table {$type}'>" . $content . '</div>';
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_pricing_table_item($attrs, $content = null) {
		extract ( shortcode_atts ( array (
				'heading' => __ ( "Heading", 'dt_themes' ),
				'per' => 'month',
				'price' => '',
				"button_link" => "#",
				"button_text" => __ ( "Buy Now", 'dt_themes' ),
				"button_size" => "small",
				'class' => '',
		), $attrs ) );
		
		$selected = (isset ( $attrs [0] ) && trim ( $attrs [0] == 'selected' )) ? 'selected' : '';
		
		if(preg_match('#^{{#', $button_link) === 1) {
			$button_link =  str_replace ( '{{', '[', $button_link );
			$button_link =  str_replace ( '}}', '/]', $button_link );
			$button_link = do_shortcode($button_link);
		}else{
			$button_link = esc_url ( $button_link );
		}
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = str_replace ( '<ul>', '<ul class="dt-sc-tb-content">', $content );
		$content = str_replace ( '<ol>', '<ul class="dt-sc-tb-content">', $content );
		$content = str_replace ( '</ol>', '</ul>', $content );
		$price = ! empty ( $price ) ? "<div class='dt-sc-price'> $price <span> $per</span> </div>" : "";
		
		$out = "<div class='dt-sc-pr-tb-col $selected $class'>";
		$out .= '	<div class="dt-sc-tb-header">';
		$out .= '		<div class="dt-sc-tb-title">';
		$out .= "			<h5>$heading</h5>";
		$out .= '		</div>';
		$out .= $price;
		$out .= '	</div>';
		$out .= $content;
		$out .= '<div class="dt-sc-buy-now">';
		$out .= do_shortcode ( "[dt_sc_button size='$button_size' link='$button_link']" . $button_text . "[/dt_sc_button]" );
		$out .= '</div>';
		$out .= '</div>';
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_progressbar($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => 'standard','color' => '','value' => '55'), $attrs ) );
		
		if( $type === 'standard' ){
			$type = "dt-sc-standard";
		}elseif( $type === 'progress-striped' ){
			$type = "dt-sc-progress-striped";
		}elseif( $type === 'progress-striped-active' ){
			$type = "dt-sc-progress-striped active";
		}

		
		$color = ! empty ( $color ) ? "style='background-color:$color;'" : "";

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = $content.' - '.$value."%";
		$value = "data-value='$value'";
		$out = "<div class='dt-sc-bar-text'>{$content}</div>";
		$out .= "<div class='dt-sc-progress $type'>";
		$out .= "<div class='dt-sc-bar' $color $value></div>";
		$out .= '</div>';
		return $out;
	}
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_tab($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'title' => '' ), $attrs ) );
		$out = '<li class="tab_head"><a href="#">' . $title . '</a></li><div class="tabs_content">' . DTCoreShortcodesDefination::dtShortcodeHelper ( $content ) . '</div>';
		return $out;
	}
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_tabs_horizontal($attrs, $content = null) {
		preg_match_all("/(.?)\[(dt_sc_tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/dt_sc_tab\])?(.?)/s", $content, $matches);

		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts( $matches[3][$i] );
		}

		$out = '<ul class="dt-sc-tabs-frame">';
			for($i = 0; $i < count($matches[0]); $i++) {
				$out .= '<li><a href="#">' . $matches[3][$i]['title'] . '</a></li>';
			}
		$out .= '</ul>';

		for($i = 0; $i < count($matches[0]); $i++) {
			$out .= '<div class="dt-sc-tabs-frame-content">' . DTCoreShortcodesDefination::dtShortcodeHelper($matches[5][$i]) . '</div>';
		}		
	return "<div class='dt-sc-tabs-container'>$out</div>";
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_tabs_vertical($attrs, $content = null) {
		preg_match_all("/(.?)\[(dt_sc_tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/dt_sc_tab\])?(.?)/s", $content, $matches);
		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts( $matches[3][$i] );
		}
		$out = "<ul class='dt-sc-tabs-vertical-frame'>";
		for($i = 0; $i < count($matches[0]); $i++) {
				$out .= '<li><a href="#">' . $matches[3][$i]['title'] . '<span></span></a></li>';
		}
		$out .= "</ul>";

		for($i = 0; $i < count($matches[0]); $i++) {
			$out .= '<div class="dt-sc-tabs-vertical-frame-content">' . DTCoreShortcodesDefination::dtShortcodeHelper($matches[5][$i]) . '</div>';
		}		
		return "<div class='dt-sc-tabs-vertical-container'>$out</div>";		
	}

	/**
	 *
	 * @param unknown $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_team($attrs, $content = null) {
		$dir_path = plugin_dir_path ( __FILE__ ) . "images/sociables/";
		$sociables_icons = DTCoreShortcodesDefination::dtListImages ( $dir_path );
		
		$sociables = array_values ( $sociables_icons );
		$attributes = array (
				'name' => '',
				'image' => 'http://placehold.it/300',
				'role' => '',
				'alt' => '',
				'title' => ''
		);
		
		foreach ( $sociables as $sociable ) {
			$attributes [$sociable] = '';
		}
		
		extract ( shortcode_atts ( $attributes, $attrs ) );
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$image = "<img src='{$image}' alt='{$alt}' title='{$title}' />";
		$name = empty ( $name ) ? "" : "<h5>{$name}</h5>";
		$role = empty ( $role ) ? "" : "<h6>{$role}</h6>";
		
		$s = "";
		$path = plugin_dir_url ( __FILE__ ) . "images/sociables/";
		foreach ( $sociables as $sociable ) {
			$img = array_search ( $sociable, $sociables_icons );
			$class = explode(".",$img);
			$class = $class[0];
			$s .= empty ( $$sociable ) ? "" : "<li class='{$class}'><a href='{$$sociable}' target='_blank'> <img src='{$path}hover/{$img}' alt='{$sociable}'/>  <img src='{$path}{$img}' alt='{$sociable}'/> </a></li>";
		}
		
		$s = ! empty ( $s ) ? "<div class='dt-sc-social-icons'><ul>$s</ul></div>" : "";

		$out = "<div class='dt-sc-team'>";
		$out .= "	<div class='image'>{$image}</div>";
		$out .= '	<div class="team-details">';
		$out .= 	$name.$role.$content.$s;
		$out .= '	</div>';
		$out .= '</div>';
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_testimonial($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'name' => '', 'role' => '', 'type' => '', 'enable_rating' => '', 'class' => '','image' => 'http://placehold.it/300'), $attrs ) );
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = ! empty ( $content ) ? '<q> "'.$content.'"</q>' : "";
		$name = ! empty ( $name ) ? " {$name} " : "";
		$role = ! empty ( $role ) ? "<span>{$role}</span>" : "";
		$cls = ! empty ( $class ) ? $class : '';
		
		if($type != '') $cls .= ' '.$type; else $cls .= '';
		
		$content = (! empty ( $content ) ) ? '<blockquote>'.$content.'</blockquote>' : "";
		if($type == 'type3' && $enable_rating == 'true') $content .= '<div class="testimonial-rating"></div>';
		$content.= "<div class='author-detail'>$name<span>$role</span></div>";
		
		if($type != 'type3')  {
			$image = "<img src='{$image}' alt='{$role}' title='{$name}' />";
			$image = "<div class='author'>{$image}</div>";
		} else {
			$image = '';
		}

		
		return "<div class='dt-sc-testimonial ".$cls."'>$image$content</div>";
	}
	
	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_testimonial_carousel($attrs, $content = null) {
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$content = str_replace( '<ul>', "<ul class='dt-sc-testimonial-carousel'>", $content );
		
		
		$out = '<div class="dt-sc-testimonial-carousel-wrapper">';
		$out .= $content;
		$out .= '<div class="carousel-arrows">';
		$out .= '	<a href="" class="testimonial-prev"> </a>';
		$out .= '	<a href="" class="testimonial-next"> </a>';
		$out .= '</div>';
		$out .= '</div>';
		return $out;
	}

	function dt_sc_title( $attrs,$content = null , $shortcodename = "" ){
		extract ( shortcode_atts ( array ( 'class' => '' ), $attrs ) );

		$shortcodename = str_replace ( "dt_sc_", "", $shortcodename );
		$out = "<{$shortcodename} class='border-title {$class}'>";
		$out .= DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out .= "<span></span>";
		$out .= "</{$shortcodename}>";
		return $out;	
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_toggle($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'title' => '' ), $attrs ) );
		
		$out = "<h5 class='dt-sc-toggle'><a href='#'>{$title}</a></h5>";
		$out .= '<div class="dt-sc-toggle-content" style="display: none;">';
		$out .= '<div class="block">';
		$out .= DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out .= '</div>';
		$out .= '</div>';
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_toggle_framed($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'title' => '' ), $attrs ) );
		
		$out = '<div class="dt-sc-toggle-frame">';
		$out .= "	<h5 class='dt-sc-toggle'><a href='#'>{$title}</a></h5>";
		$out .= '	<div class="dt-sc-toggle-content" style="display: none;">';
		$out .= '		<div class="block">';
		$out .= DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out .= '		</div>';
		$out .= '	</div>';
		$out .= '</div>';
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_titled_box($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'title' => '', 'icon' => '', 'type'	=> '', 'variation' => '', 'bgcolor' => '', 'textcolor' => ''), $attrs ) );
		
		$type = (empty($type)) ? 'dt-sc-titled-box' :"dt-sc-$type";
		$variation = ( ( $variation ) && ( empty( $bgcolor ) ) ) ? ' ' . $variation : '';
		$content = DTCoreShortcodesDefination::dtShortcodeHelper( $content );
		
		$styles = array();
		if($bgcolor) $styles[] = 'background-color:' . $bgcolor . ';border-color:' . $bgcolor . ';';
		if($textcolor) $styles[] = 'color:' . $textcolor . ';';
		$style = join('', array_unique( $styles ) );
		$style = !empty( $style ) ? ' style="' . $style . '"': '' ;
		
		if($type == 'dt-sc-titled-box') :
			$icon = ( empty($icon) ) ? "" : "<span class='fa {$icon} '></span>";
			$title = "<h6 class='{$type}-title' {$style}> {$icon} {$title}</h6>";
			$out = "<div class='{$type} {$variation}'>";
			$out .= $title;
			$out .=	"<div class='{$type}-content'>{$content}</div>";
			$out .= "</div>";
		else :
			$out = "<div class='{$type}'>{$content}</div>";
		endif;
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_tooltip($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => 'default', 'tooltip' => '', 'position' => 'top', 'href' => '', 'target' => '','bgcolor' => '','textcolor' => ''), $attrs ) );
		
		$class  = " class=' ";
		$class .=  ( $type == "boxed" ) ? "dt-sc-boxed-tooltip" : "";
		$class .= " dt-sc-tooltip-{$position}'";
		
		$href = " href='{$href}' ";
		$title = " title = '{$tooltip}' ";
		$target = empty($target) ? 'target="_blank"' : "target='{$target}' ";
		
		$styles = array();
		if($bgcolor) $styles[] = 'background-color:' . $bgcolor . ';border-color:' . $bgcolor . ';';
		if($textcolor) $styles[] = 'color:' . $textcolor . ';';
		$style = join('', array_unique( $styles ) );
		$style = !empty( $style ) ? ' style="' . $style . '"': '' ;
		$style = ( $type == "boxed" ) ? $style : "";
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper( $content );
		$out = "<a {$href} {$title} {$class} {$style} {$target}>{$content}</a>";
		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_pullquote($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'type' => 'pullquote1', 'align' => '', 'icon' => '', 'textcolor' => '', 'cite' => ''
		), $attrs ) );
		
		$class = array();
		if( isset($type) )
			$class[] = " dt-sc-{$type}";
			
		if( trim( $icon ) == 'yes' )
			$class[] = ' quotes';

		if( preg_match( '/left|right|center/', trim( $align ) ) )
			$class[] = ' align' . $align;
			
		$cite = ( $cite ) ? ' <cite>&ndash; ' . $cite .'</cite>' : '' ;
		
		$style = ( $textcolor ) ? ' style="color:' . $textcolor . ';"' : '';
		$class = join( '', array_unique( $class ) );
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		$out = "<span class='{$class}' {$style}> {$content} {$cite}</span>";
		
		return $out;
	}


	/**
	 * [dt_sc_portfolio_item description]
	 * @param  [type] $attrs         [description]
	 * @param  [type] $content       [description]
	 * @param  string $shortcodename [description]
	 * @return [type]                [description]
	 */
	function dt_sc_portfolio_item( $attrs, $content = null, $shortcodename= "" ){
		extract( shortcode_atts( array( 'id' => ''), $attrs ));
		$out = ""; 
		if( !empty( $id ) ){
			
			$post_thumbnail = 'portfolio-one-column';
			
			$tpl_default_settings = get_post_meta( get_the_ID(), '_tpl_default_settings', TRUE );
			$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
			$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
			
			if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $post_thumbnail .= '-single-sidebar';
			elseif($page_layout == 'both-sidebar') $post_thumbnail .= '-both-sidebar';		

			$p = get_post( $id );
			if( $p->post_type === "dt_portfolios" ):
				$permalink = get_permalink($id);
				$portfolio_item_meta = get_post_meta($id,'_portfolio_settings',TRUE);
				$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();

				$out .= "<div id='portfolio-{$id}' class='portfolio gallery'>";
				$out .= '<figure>';
							$popup = "http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio";
							if( array_key_exists('items_name', $portfolio_item_meta) ) {
								$item =  $portfolio_item_meta['items_name'][0];
								$popup = $portfolio_item_meta['items'][0];
								if( "video" === $item ) {
									$items = array_diff( $portfolio_item_meta['items_name'] , array("video") );
									if( !empty($items) ) {
										$out .= "<img src='".$portfolio_item_meta['items'][key($items)]."' width='1170' height='878' alt='".__('portfolio', 'dt_themes')."' />";
									} else {
										$out .= '<img src="http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio" alt="'.__('portfolio', 'dt_themes').'" width="1170" height="878"/>';
									}	
								} else {
									$attachment_id = dt_get_attachment_id_from_url($portfolio_item_meta['items'][0]);
									$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
									$out .= "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
								}	
							} else{
								$out .= "<img src='{$popup}'/>";
							}
					
							$out .= '<div class="image-overlay">';
							$out .= '	<div class="image-overlay-details">'; 
							$out .= "		<h5><a href='{$permalink}' >{$p->post_title}</a></h5>";
											if( array_key_exists("sub-title",$portfolio_item_meta) ):
												$out .= "<h6>{$portfolio_item_meta['sub-title']}</h6>";
											endif;	
							$out .= '		<div class="links">';
							$out .= '			<a href="'.$popup.'" data-gal="prettyPhoto[galleryitem]" class="zoom"><span class="fa fa-search"></span></a>';
							$out .= '			<a href="'.$permalink.'" class="link"><span class="fa fa-link"></span></a>';
							$out .= '		</div>';
							$out .= '	</div>';
							$out .= '	<a class="close-overlay hidden"> x </a>';
							$out .= '</div>';
					
				$out .= '</figure>';
				$out .= '</div>';
			else:
				$out .="<p>".__("There is no portfolio item with id :","dt_themes").$id."</p>";
			endif;

		} else {

			$out .="<p>".__("Please give portfolio post id","dt_themes")."</p>";
		}
		return $out;
	}


	function dt_sc_portfolios( $attrs, $content = null ){
		extract( shortcode_atts( array( 'category_id' => '','column'=>'3','count'=>'-1'), $attrs ));
		$out = "";
		$post_class = "";
		switch ( $column ) {
			case '2': 
				$post_class = " dt-sc-one-half";
				$post_thumbnail = 'portfolio-two-column';
				break;
			
			case '3':
				$post_class = " dt-sc-one-third ";
				$post_thumbnail = 'portfolio-three-column';
				break;

			case '4': 
				$post_class = " dt-sc-one-fourth ";
				$post_thumbnail = 'portfolio-four-column';
				break;
		}

		
		$tpl_default_settings = get_post_meta( get_the_ID(), '_tpl_default_settings', TRUE );
		$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
		
		if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $post_thumbnail .= '-single-sidebar';
		elseif($page_layout == 'both-sidebar') $post_thumbnail .= '-both-sidebar';		


		$category_id = explode(",", $category_id);
		if( is_array($category_id) && !empty($category_id) ){

			$args = array( 'orderby' => 'ID',
				'order' => 'ASC',
				'paged' => get_query_var( 'paged' ),
				'posts_per_page' => $count,
				'tax_query' => array( array( 'taxonomy'=>'portfolio_entries', 'field'=>'id', 'operator'=>'IN', 'terms'=>$category_id ) ) );

				query_posts($args);
				if( have_posts() ):
					while( have_posts() ):
						the_post();

						$the_id = get_the_ID();
						$permalink = get_permalink($the_id);
						$title = get_the_title($the_id);

						$portfolio_item_meta = get_post_meta($the_id,'_portfolio_settings',TRUE);
						$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();

						$out .= "<div id='portfolio-{$the_id}' class='portfolio column gallery no-space {$post_class}'>";
						$out .= '<figure>';
									$popup = "http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio";
									if( array_key_exists('items_name', $portfolio_item_meta) ) {
										$item =  $portfolio_item_meta['items_name'][0];
										$popup = $portfolio_item_meta['items'][0];
										if( "video" === $item ) {
											$items = array_diff( $portfolio_item_meta['items_name'] , array("video") );
											if( !empty($items) ) {
												$out .= "<img src='".$portfolio_item_meta['items'][key($items)]."' alt='".__('portfolio', 'dt_themes')."' width='1170' height='878' />";
											} else {
												$out .= '<img src="http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio" alt="'.__('portfolio', 'dt_themes').'" width="1170" height="878"/>';
											}	
										} else {
											$attachment_id = dt_get_attachment_id_from_url($portfolio_item_meta['items'][0]);
											$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
											$out .= "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
										}	
									} else{
										$out .= "<img src='{$popup}' alt='".__('portfolio', 'dt_themes')."' />";
									}
							
							$out .= '<div class="image-overlay">';
							$out .= '	<div class="image-overlay-details">'; 
							$out .= "		<h5><a href='{$permalink}' >{$title}</a></h5>";
											if( array_key_exists("sub-title",$portfolio_item_meta) ):
												$out .= "<h6>{$portfolio_item_meta['sub-title']}</h6>";
											endif;	
							$out .= '		<div class="links">';
							$out .= "			<a href='{$popup}' data-gal='prettyPhoto[gallery]' class='zoom'> <span class='fa fa-search'> </span> </a>";
							$out .= "			<a href='{$permalink}' class='link'> <span class='fa fa-link'> </span> </a>";
							$out .= '		</div>';
							$out .= '	</div>';
							$out .= '	<a class="close-overlay hidden"> x </a>';
							$out .= '</div>';
							
						$out .= '</figure>';
						$out .= '</div>';
					endwhile;
				endif;
				wp_reset_query();
		} else {
			$out = "<p>".__("No portfolios in given category","dt_themes")."</p>";
		}

		return $out;
	}

	/**
	 *
	 * @param array $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_infographic_bar($attrs, $content = null, $shortcodename ="") {
		extract ( shortcode_atts ( array ( 'type' => 'standard', 'icon' =>'', 'icon_size'=>'150', 'color' => '', 'value' => '55' ), $attrs ) );

		if( $type === 'standard' ){
			$type = "dt-sc-standard";
		}elseif( $type === 'progress-striped' ){
			$type = "dt-sc-progress-striped";
		}elseif( $type === 'progress-striped-active' ){
			$type = "dt-sc-progress-striped active";
		}
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$out = '<div class="dt-sc-infographic-bar">';
		
		if( !empty($icon) ){
		$out .= "<i class='fa {$icon}' style='font-size:{$icon_size}px; color:{$color};'> </i>";
		}
		$out .= '	<div class="info">';
		
		$out .= "		<div class='dt-sc-progress $type'>";
		$out .= "		 <div data-value={$value} style='background-color:{$color};' class='dt-sc-bar'></div>";
		$out .= '		</div>';
		
		$out .= "		<div class='dt-sc-bar-percentage'> <span> {$value}%  </span> </div>";
		$out .= "		<div class='dt-sc-bar-text'>$content</div>";
		$out .= '	</div>';
		
		$out .= '</div>';
		
		return $out;
	}

	function dt_sc_fullwidth_section($attrs, $content = null) {
		extract ( shortcode_atts ( array ( 'backgroundcolor' => '', 'backgroundimage' => '', 'backgroundrepeat' => '', 'backgroundposition' => '', 'paddingtop' => '', 'paddingbottom' => '', 'textcolor' =>'', 'opacity' => '', 'class' =>'', 'parallax' => 'no', 'disable_container' => 'false' ), $attrs ) );

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$styles = array ();
		$styles[] = !empty( $textcolor ) ? "color:{$textcolor};" : "";
		if( !empty( $opacity ) ) {
			$hex = str_replace ( "#", "", $backgroundcolor );
			if (strlen ( $hex ) == 3) :
				$r = hexdec ( substr ( $hex, 0, 1 ) . substr ( $hex, 0, 1 ) );
				$g = hexdec ( substr ( $hex, 1, 1 ) . substr ( $hex, 1, 1 ) );
				$b = hexdec ( substr ( $hex, 2, 1 ) . substr ( $hex, 2, 1 ) );
			else :
				$r = hexdec ( substr ( $hex, 0, 2 ) );
				$g = hexdec ( substr ( $hex, 2, 2 ) );
				$b = hexdec ( substr ( $hex, 4, 2 ) );
			endif;
			$rgb = array ( $r,$g,$b);
			$styles[] = "background-color:rgba($rgb[0],$rgb[1],$rgb[2],$opacity); ";
		} else {
			$styles[] = !empty( $backgroundcolor ) ? "background-color:{$backgroundcolor};" : "";
		}	

		$styles[] = !empty( $backgroundimage ) ? "background-image:url({$backgroundimage});" : "";
		$styles[] = !empty( $backgroundrepeat ) ? "background-repeat:{$backgroundrepeat};" : "";
		$styles[] = !empty( $backgroundposition ) ? "background-position:{$backgroundposition};" : "";
		$styles[] = !empty( $paddingtop ) ? "padding-top:{$paddingtop}px;" : "";
		$styles[] = !empty( $paddingbottom ) ? "padding-bottom:{$paddingbottom}px;" : "";

		$parallaxclass = '';
		if( $parallax === "yes") {
			$styles[] = "background-attachment:fixed; ";
			$parallaxclass = "dt-sc-parallax-section";
		}

		$styles = array_filter( $styles);
		$style = join ( '', array_unique ( $styles ) );
		$style = ! empty ( $style ) ? ' style="' . $style . '"' : '';
		
		$out = 	"<div class='fullwidth-section {$class} {$parallaxclass}' {$style}>";
			if($disable_container != 'true') $out .= '	<div class="container">';
				$out .= 	$content;
			if($disable_container != 'true') $out .= '	</div>';
		$out .= '</div>';
		return $out;
	}

	function dt_sc_fullwidth_video( $attrs, $content = null ) {
		extract ( shortcode_atts ( array ( 'mp4' => '', 'webm'=>'', 'ogv' => '', 'poster' => '', 'backgroundimage' => '', 'paddingtop' => '', 'paddingbottom' => '', 'class' =>''), $attrs ) );

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$styles = array ();
		$styles[] = !empty( $paddingtop ) ? "padding-top:{$paddingtop}px;" : "";
		$styles[] = !empty( $paddingbottom ) ? "padding-bottom:{$paddingbottom}px;" : "";
		$styles = array_filter( $styles);
		$style = join ( '', array_unique ( $styles ) );

		$backgroundimage = !empty( $backgroundimage )  ? "$backgroundimage" : "http://placehold.it/1920x400&text=DesignThemes";
		$style .= " background:url({$backgroundimage}) left top repeat; ";
		$style = ! empty ( $style ) ? ' style="' . $style . '"' : '';

		$poster = !empty( $poster )  ? " poster='{$poster}' " : "";

		$mp4 = !empty( $mp4 )  ? "<source src='{$mp4}' type='video/mp4'/>" : "";
		$webm = !empty( $webm )  ? "<source src='{$webm}' type='video/webm'/>" : "";
		$ogv = !empty( $ogv )  ? "<source src='{$ogv}' type='video/ogg'/>" : "";
		

		$out  = "<div class='dt-sc-fullwidth-video-section {$class}' {$style}>";
		$out .= '	<div class="dt-sc-video-container">';
		$out .= "	<div class='dt-sc-mobile-image-container' style='display:none;'></div>";
		$out .= "		<video autoplay loop class='dt-sc-video dt-sc-fillWidth' {$poster}>";
		$out .= 		$mp4.$webm.$ogv;
		$out .= '		</video>';
		$out .= '	</div>';
		$out .= '   <div class="dt-sc-video-content-wrapper">';		
		$out .= "		<div class='container'>{$content}</div>";
		$out .= '	</div>';
		$out .= '</div>';

		return $out;
	}

	function dt_sc_animation( $attrs, $content = null ){
		extract ( shortcode_atts ( array ( 'effect' => '','delay'=>''), $attrs ) );
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		return "<div class='animate' data-animation='{$effect}' data-delay='{$delay}'>{$content}</div>";
	}


	function dt_sc_post( $attrs, $content = null ) {
		extract(shortcode_atts(array( 'id'=>'1', 'read_more_text'=>__('Read More','dt_themes'),'excerpt_length'=>10,'columns'=>1), $attrs));

		$p = get_post($id,'ARRAY_A');
		$link = get_permalink($id);
		$format = get_post_format($id);
		$title = $p['post_title'];
		$author_id = $p['post_author'];
		$class = get_post_class("blog-entry no-border",$id);
		$class = implode(" ",$class);

		$tags = "";
		$terms = wp_get_post_tags($id);

		$post_meta = is_array(get_post_meta($id ,'_dt_post_settings',TRUE)) ? get_post_meta($id ,'_dt_post_settings',TRUE) : array();

		if( !empty($terms) ) {

			$tags .= '<p class="tags"><i class="fa fa-tags"> </i>';
			foreach( $terms as $term ) {
				$tags .= '<a href="'.get_term_link($term->slug, 'post_tag').'"> '.$term->name.'</a>,';
			}

			$tags = substr($tags,0,-1);
			$tags .= '</p> <span> | </span> ';
		}
		
		$thumbnail_sidebar = '';
		
		$tpl_default_settings = get_post_meta( get_the_ID(), '_tpl_default_settings', TRUE );
		$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
		
		if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $thumbnail_sidebar = '-single-sidebar';
		elseif($page_layout == 'both-sidebar') $thumbnail_sidebar = '-both-sidebar';		

		if($columns == 2) {
			if($thumbnail_sidebar == "-single-sidebar") $post_thumbnail = 'blog-two-column';
			else $post_thumbnail = 'blogcourse-two-column';
		} elseif($columns == 3)$post_thumbnail = 'blogcourse-three-column';
		else $post_thumbnail = 'blog-one-column';
		
		$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;
		
		$out  = '<article class="'.esc_attr($class).'">';
		$out .= '	<div class="blog-entry-inner">';

		$out .= '		<div class="entry-thumb">';
							if( $format === "image" || empty($format) ):
						$out .= '<a href="'.esc_url($link).'">';
							if( has_post_thumbnail( $id )) {
								$out .= get_the_post_thumbnail($id,$post_thumbnail);	
							}else{
								$out .= '<img src="http://placehold.it/1170x822&text=Image" alt="'.$title.'" />';
							}
						$out .= "</a>";
							elseif( $format === "gallery" && (array_key_exists("items", $post_meta)) ):
								$out .= "<ul class='entry-gallery-post-slider'>";
								foreach ( $post_meta['items'] as $item ) { 
									$attachment_id = dt_get_attachment_id_from_url($item);
									$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
									$out .= "<li><img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' /></li>";
								}
								$out .= "</ul>";
							elseif( $format === "video" && (array_key_exists('oembed-url', $post_meta) || array_key_exists('self-hosted-url', $post_meta)) ):
								if( array_key_exists('oembed-url', $post_meta) ):
									$out .= "<div class='dt-video-wrap'>".wp_oembed_get($post_meta['oembed-url']).'</div>';
								elseif( array_key_exists('self-hosted-url', $post_meta) ):
									$out .= "<div class='dt-video-wrap'>".wp_video_shortcode( array('src' => $post_meta['self-hosted-url']) ).'</div>';
								endif;
							elseif( $format === "audio" && (array_key_exists('oembed-url', $post_meta) || array_key_exists('self-hosted-url', $post_meta)) ):
								if( array_key_exists('oembed-url', $post_meta) ):
									$out .= wp_oembed_get($post_meta['oembed-url']);
								elseif( array_key_exists('self-hosted-url', $post_meta) ):
									$out .= wp_audio_shortcode( array('src' => $post_meta['self-hosted-url']) );
								endif;
							else:
						$out .= '<a href="'.esc_url($link).'">';
							if( has_post_thumbnail( $id )) {
								$out .= get_the_post_thumbnail($id,$post_thumbnail);	
							}else{
								$out .= '<img src="http://placehold.it/1170x822&text=Image" alt="'.$title.'" />';
							}
						$out .= "</a>";
							endif;
							
							$excerpt = explode(' ', do_shortcode($p['post_content']), $excerpt_length);
							$excerpt = array_filter($excerpt);

							if (!empty($excerpt)) {
								if (count($excerpt) >= $excerpt_length) {
									array_pop($excerpt);
									$excerpt = implode(" ", $excerpt).'...';
								} else {
									$excerpt = implode(" ", $excerpt);
								}
								$excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);
								$out .='<div class="entry-thumb-desc"><p>'.$excerpt.'</p></div>';							
							}
							
		$out .= '		</div> <!-- .entry-thumb -->';

		$out .= '		<div class="entry-details">';
							 if(is_sticky()):
								$out .= '<div class="featured-post"> <span class="fa fa-trophy"> </span> <span class="text">'.__('Featured','dt_themes').'</span></div>';
							 endif;
							 
		$out .= '			<div class="entry-meta">';
		$out .= '				<div class="date">';
		$out .= 					get_the_time('d M',$id);
		$out .='				</div>';
		$out .= "				<a href='{$link}' class='entry_format'></a>";
		$out .= '			</div>';
							 
		$out .= "			<div class='entry-title'><h4><a href='{$link}'>{$title}</a></h4></div>";

		$out .= '			<div class="entry-metadata">';
		$out .= "				<p class='author'><i class='fa fa-user'> </i> <a href='".get_author_posts_url($author_id)."'>".get_the_author_meta('display_name',$author_id)."</a></p><span> | </span>";
		$out .= 				$tags;
								$commtext = "";
								if((wp_count_comments($id)->approved) == 0)	$commtext = '0';
								else $commtext = wp_count_comments($id)->approved;
		$out .="				<p class='comments'><a href='{$link}/#respond' class='comments'><span class='fa fa-comments'> </span> {$commtext}</a></p>";
		$out .='			</div>';

		$out .= '		</div>';
		$out .= '	</div>';
		$out .= '</article>';

		return $out;
	}

	function dt_sc_recent_post( $attrs, $content = null ) {
		extract( shortcode_atts( array( 'categories'=>'', 'columns'=>'3','count'=>'3', 'read_more_text'=>__('Read More','dt_themes'),'excerpt_length'=>10), $attrs ));
		$out = "";
		$post_class = "";
		switch( $columns ) :
			case '2':
				$post_class = "column dt-sc-one-half";
			break;

			default:
			case '3':
				$post_class = "column dt-sc-one-third";
			break;
		endswitch;

		if(empty($categories)):
			$rposts = new WP_Query( array( 'posts_per_page' => $count, 'orderby' => 'date', 'post_type'=> 'post' ) );
		else:
			$rposts = new WP_Query( array( 'posts_per_page' => $count, 'orderby' => 'date', 'post_type'=> 'post', 'cat'=>$categories ) );
		endif;

		if ( $rposts->have_posts() ):

			$i = 1;

			while( $rposts->have_posts() ):
				$rposts->the_post();

				$the_id = get_the_ID();
				$permalink = get_permalink($the_id);
				$title = get_the_title($the_id);

				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;

				$format = get_post_format(  $the_id );

				$out .= "<div class='{$temp_class}'>";
				$sc = "[dt_sc_post id='{$the_id}' read_more_text='{$read_more_text}' excerpt_length='{$excerpt_length}' columns='{$columns}' /]";
				$out .= do_shortcode($sc);
				$out .= '</div>';
			endwhile;
			wp_reset_query();
		endif;
		return $out;
	}
	
	/**
	 *
	 * @param unknown $attrs        	
	 * @param string $content        	
	 * @return string
	 */
	function dt_sc_teacher($attrs, $content = null) {
				
		extract(shortcode_atts(array(
			'columns' => '',
			'limit' => '',
			'post_id' => ''
		), $attrs));
		
		$columns = !empty($columns) ? $columns : '4';
		$limit = !empty($limit) ? $limit : '-1';
		$col_class = $out = "";
		
		switch($columns):
			case '1':   $col_class = 'column dt-sc-one-column';   break;

			case '2':   $col_class = 'column dt-sc-one-half';   break;

			case '3':   $col_class = 'column dt-sc-one-third';   break;
			
			case '4':   $col_class = 'column dt-sc-one-fourth';   break;
			
			case '5':   $col_class = 'column dt-sc-one-fifth';   break;
			
			default:    $col_class = 'column dt-sc-one-fourth';   break;
		endswitch;
		
		if($post_id != '') {
			$post_id = explode(',', $post_id);
			$args = array('post_type' => 'dt_teachers', 'posts_per_page' => $limit, 'post__in' => $post_id, 'order' => 'ASC', 'orderby' => 'name');
		} else {
			$args = array('post_type' => 'dt_teachers', 'posts_per_page' => $limit);
		}
		
		$wp_query = new WP_Query($args);
		
		if($wp_query->have_posts()): $i = 1;
		 while($wp_query->have_posts()): $wp_query->the_post();
			
			$temp_class = ""; global $post;
			
			if($i == 1) $temp_class = $col_class." first"; else $temp_class = $col_class;
			if($i == $columns) $i = 1; else $i = $i + 1;
			
			$teacher_settings = get_post_meta ( $post->ID, '_teacher_settings', TRUE );
			
			$s = "";
			$path = plugin_dir_url ( __FILE__ ) . "images/sociables/";
			if(isset($teacher_settings['teacher-social'])) {
				foreach ( $teacher_settings['teacher-social'] as $sociable => $social_link ) {
					if($social_link != '') {
						$img = $sociable;
						$class = explode(".",$img);
						$class = $class[0];
						$s .= "<li class='{$class}'><a href='{$social_link}' target='_blank'> <img src='{$path}hover/{$img}' alt='{$class}'/>  <img src='{$path}{$img}' alt='{$class}'/> </a></li>";
					}
				}
			}
			
			$s = ! empty ( $s ) ? "<div class='dt-sc-social-icons'><ul>$s</ul></div>" : "";
				
			//FOR AJAX...
			$nonce = wp_create_nonce("dt_team_member_nonce");
			$link = admin_url('admin-ajax.php?ajax=true&amp;action=dttheme_team_member&amp;post_id='.$post->ID.'&amp;nonce='.$nonce);
						
			$out .= '<div class="'.$temp_class.'">';	
			$out .= "   <div class='dt-sc-team'>";
			$out .= "		<div class='image'>";
								if(get_the_post_thumbnail($post->ID, 'full') != ''):
									$out .= get_the_post_thumbnail($post->ID, 'full');
								else:
									$out .= '<img src="http://placehold.it/400x420" alt="member-image" />';
								endif;
			$out .= " 		</div>";
			$out .= '		<div class="team-details">';
			$out .= '			<h5><a href="'.$link.'" data-gal="prettyPhoto[pp_gal]">'.get_the_title().'</a></h5>';
								if($teacher_settings['role'] != '')
									$out .= "<h6>".$teacher_settings['role']."</h6>";
								if(isset($teacher_settings['show-social-share']) && $teacher_settings['show-social-share'] != '') $out .= $s;
			$out .= '		</div>';
			$out .= '   </div>';
			$out .= '</div>';	
				
		 endwhile;
		else: 
            $out .= '<h2>'.__('Nothing Found.', 'dt_themes').'</h2>';
            $out .= '<p>'.__('Apologies, but no results were found for the requested archive.', 'dt_themes').'</p>';
		endif;
		return $out;
	}
	
	
	function dt_sc_courses_sensei( $atts, $content = null ) {
		
		if(dttheme_is_plugin_active('woothemes-sensei/woothemes-sensei.php')) {
			
			extract(shortcode_atts(array(
				'limit'  => '-1',
				'course_type' => '', // featured, paid, recent
				'carousel' => '',
				'categories' => ''
			), $atts));
	
			global $woothemes_sensei;
			
			$out = $args = "";
			$article_class = "column dt-sc-one-half";
			$firstcnt = 2;
			
			if(empty($categories)) {
				$cats = get_categories('taxonomy=course-category&hide_empty=1');
				$cats = get_terms( array('course-category'), array('fields' => 'ids'));		
			} else {
				$cats = explode(',', $categories);
			}
		
			if($course_type == 'featured')
				$args = array('post_type' => 'course', 'posts_per_page' => $limit, 'tax_query' => array( array(
																										'taxonomy' => 'course-category',
																										'field' => 'id',
																										'terms' => $cats
																								)), 'meta_query' => array( array( 'key' => '_course_featured', 'value' => 'featured' ) ));
			elseif($course_type == 'paid')
				$args = array('post_type' => 'course', 'posts_per_page' => $limit, 'tax_query' => array( array(
																										'taxonomy' => 'course-category',
																										'field' => 'id',
																										'terms' => $cats
																								)), 'meta_query' => array( array( 'key' => '_course_woocommerce_product', 'value' => '-', 'compare' => '!=' ) ));
			else
				$args = array('post_type' => 'course', 'posts_per_page' => $limit, 'tax_query' => array( array( 
																										'taxonomy' => 'course-category',
																										'field' => 'id',
																										'terms' => $cats
																								)));
				
			$the_query = new WP_Query($args);
			
			if($the_query->have_posts()): 
			 while($the_query->have_posts()): $the_query->the_post();
			 
					$temp_class = '';
					
					if($carousel != 'true') {
						$firstcls = '';
						$no = $the_query->current_post+1;
						if(($no%$firstcnt) == 1){ $firstcls = ' first'; }
						$temp_class = 'class="'.$article_class.' '.$firstcls.'"';
					}
				
					$out .= '<li '.$temp_class.'><!-- Course Starts -->';
					$out .= '<article id="post-'.get_the_ID().'" class="'.implode(" ", get_post_class("dt-sc-course", get_the_ID())).'">';
					
						$out .= '<a href="'.get_permalink().'" >';
							$out .= '<div class="dt-sc-course-thumb">';
								if(has_post_thumbnail()):
									$image_url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full');
									$out .= '<img src="'.$image_url[0].'" alt="'.get_the_title().'" />';
								else:
									$out .= '<img src="http://placehold.it/1170x822&text=Image" alt="'.get_the_title().'" />';
								endif;
							$out .= '</div>';
						$out .= '</a>';
						
						$wooproductID = get_post_meta(get_the_ID(), '_course_woocommerce_product', true);
						if($wooproductID != '' && $wooproductID != '-' && function_exists('get_woocommerce_currency_symbol')) {
							$out .= '<a href="'.get_permalink($wooproductID).'" class="dt-sc-course-price">'.get_woocommerce_currency_symbol().get_post_meta($wooproductID, '_sale_price', true).'.00'.'</a>';
						} else {
							$out .= '<a href="'.wp_registration_url().'" class="dt-sc-course-price">'.__('Free', 'dt_themes').'</a>';
						}
						
						$out .= '<div class="dt-sc-course-details">';	
							$out .= '<h5><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h5>';
							$out .= '<p class="dt-sc-tags">'.get_the_term_list(get_the_ID(), 'course-category', ' ', ', ', ' ').'</p>';
							$out .= '<div class="dt-sc-course-meta">';
							$out .= '<span><i class="fa fa-book"> </i>'.$woothemes_sensei->post_types->course->course_lesson_count(get_the_ID()).'&nbsp;'.__('Lessons', 'dt_themes').'</span>';
							$out .=  '<span> <i class="fa fa-user"> </i><a href="'.get_author_posts_url(get_the_author_meta( 'ID' )).'" >'.get_the_author().'</a></span>';
							$out .= '</div>';
						$out .= '</div>';
					
					$out .= '</article>';
				$out .= '</li><!-- Course Ends -->';
			 endwhile;
			 
			else:
				$out .= '<h2>'.__('Nothing Found.', 'dt_themes').'</h2>';
				$out .= '<p>'.__('Apologies, but no results were found for the requested archive.', 'dt_themes').'</p>';
			endif;
			wp_reset_query();
			
			if($carousel == 'true') {
				return '<div class="dt-sc-coursesensei-carousel-wrapper"><ul class="dt-sc-coursesensei-carousel">'.$out.'</ul><div class="carousel-arrows"><a class="course-sensei-prev" href=""></a><a class="course-sensei-next" href=""></a></div></div>';
			} else {
				return '<ul class="dt-sc-course-list">'.$out.'</ul>';
			}
		
		} else {
			return '';
		}
		
	}
		
	function dt_sc_counter( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'title' => 'Title Comes Here',
			'number' => 2000,
		), $atts));

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );
		
		$out = '<div class="dt-sc-counter" data-counter="'.$number.'">
					<div class="dt-sc-counter-number"> '.$number.' </div>
					<h5> '.$title.' <span> </span></h5>
				</div>';
		
		return $out;
	}
	
	function dt_sc_events( $atts, $content = null ) {
		
		if(!function_exists('dt_events_list') && dttheme_is_plugin_active('the-events-calendar/the-events-calendar.php')) {
			
			extract(shortcode_atts(array(
				'limit'  => '-1',
				'carousel' => '',
				'category_ids' => '',
			), $atts));
	
			global $post; $out = '';
			$firstcnt = 2;
			
			if($category_ids != '') $terms = explode(',', $category_ids);
			
			$post_thumbnail = 'blogcourse-three-column';
			
			$tpl_default_settings = get_post_meta( get_the_ID(), '_tpl_default_settings', TRUE );
			$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
			$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
			
			if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $post_thumbnail .= '-single-sidebar';
			elseif($page_layout == 'both-sidebar') $post_thumbnail .= '-both-sidebar';		
			
			if($carousel == 'true') $html_tag = 'li';
			else $html_tag = 'div';
			
			if($category_ids != '')
				$all_events = tribe_get_events(array( 'eventDisplay'=>'all', 'posts_per_page'=> $limit, 'tax_query'=> array( array( 'taxonomy' => 'tribe_events_cat', 'field' => 'id', 'operator'=>'IN', 'terms' => $terms ) ) ));
			else
				$all_events = tribe_get_events(array( 'eventDisplay'=>'all', 'posts_per_page'=> $limit ));
			
			$cnt = 0;
			foreach($all_events as $post) {
			  setup_postdata($post);
			  
			  	$temp_class = $firstcls = '';
				if($carousel != 'true') {
					$no = $cnt + 1;
					if(($no%$firstcnt) == 1){ $firstcls = ' first'; }
				}
			  
				$out .= '<'.$html_tag.' class="dt-sc-one-half column '.$firstcls.'" id="post-'.get_the_ID().'">';
				
					$out .= '<div class="dt-sc-event-container">';
						$out .= '<div class="dt-sc-event-thumb">';
							$out .= '<a href="'.get_permalink().'" title="'.get_the_title().'">';
									if ( has_post_thumbnail()):
										$attr = array('title' => get_the_title()); $out .= get_the_post_thumbnail($post->ID, $post_thumbnail, $attr);
									else:
										$out .= '<img src="http://placehold.it/1170x895&text=Image" alt="'.get_the_title().'" />';
									endif;		
							$out .= '</a>';
							if(tribe_get_cost($post->ID) != '') {
								$currency_symbol = tribe_get_event_meta( $post->ID, '_EventCurrencySymbol', true );
								if(!$currency_symbol) $currency_symbol = tribe_get_option( 'defaultCurrencySymbol', '$' );
								$currency_position = tribe_get_event_meta( $post->ID, '_EventCurrencyPosition', true );
								$out .= '<span class="event-price">';
									if($currency_position == 'suffix')
										$out .= tribe_get_cost($post->ID).$currency_symbol;
									else
										$out .= $currency_symbol.tribe_get_cost($post->ID);
								$out .= '</span>';
							}
						$out .= '</div>';
					
						$out .= '<div class="dt-sc-event-content">';
							$out .= '<h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2>';				
							$out .= '<div class="dt-sc-event-meta">';
							$out .= '<p> <i class="fa fa-calendar-o"> </i>'.tribe_get_start_date($post->ID, false, 'M Y @ h a').' - '.tribe_get_end_date($post->ID, false, 'M Y @ h a').' </p>';
									$venue_id = tribe_get_venue_id( $post->ID );
									if ( isset($venue_id) && $venue_id > 0 ) {
										$url = esc_url( get_permalink( $venue_id ) );
										$venue_name = tribe_get_venue($post->ID);
										$out .= '<p> <i class="fa fa-map-marker"> </i>';
										$out .= '<a href="'.$url.'">'.$venue_name.'</a>';
										$out .= ', '.tribe_get_country($post->ID);
										if(tribe_get_map_link($post->ID) != '') {
											$out .= '<a href="'.tribe_get_map_link().'" title="'.$venue_name.'" target="_blank">'.__(' + Google Map ', 'dt_themes').'</a>';
										}
										$out .= '</p>';
									}
							$out .= '</div>';
							
						$out .= '</div>';
					$out .= '</div>';
					
				$out .= '</'.$html_tag.'>';
				
				$cnt++;
			}
			
			if($carousel == 'true') {
				return '<div class="dt-sc-events-carousel-wrapper"><ul class="dt-sc-events-carousel">'.$out.'</ul><div class="carousel-arrows"><a class="events-prev" href=""></a><a class="events-next" href=""></a></div></div>';
			} else {
				return $out;
			}
		
		} else {
			return '';
		}
		
	}
	
	function dt_sc_courses( $atts, $content = null ) {
		
			extract(shortcode_atts(array(
				'limit'  => '-1',
				'columns' => '',
				'course_type' => '', // featured,  recent
				'carousel' => '',
				'categories' => '',
				'layout_view' => 'grid'
			), $atts));
	
			global $woothemes_sensei;
			
			$out = $args = $thumbnail_sidebar = '';
			
			$tpl_default_settings = get_post_meta( get_the_ID(), '_tpl_default_settings', TRUE );
			$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
			$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
			
			if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $thumbnail_sidebar .= '-single-sidebar';
			elseif($page_layout == 'both-sidebar') $thumbnail_sidebar .= '-both-sidebar';		
			
			if($columns == '3') {
				$post_thumbnail = 'blogcourse-three-column';
			} else {
				if($page_layout == 'with-left-sidebar' || $page_layout == 'with-right-sidebar') $post_thumbnail = 'course-two-column';
				else $post_thumbnail = 'blogcourse-two-column';
			}
			
			$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;
			
			if( $layout_view == "list" ) {
				$layout_class = "course-list-view";
				if($columns == '2') {
					$article_class = "column dt-sc-one-half";
					$firstcnt = 2;
					$carousel_column = '2';
				} else {
					$article_class = "column dt-sc-full-width";
					$firstcnt = 1;
					$carousel_column = '1';
				}
			} elseif( $layout_view == "grid" ) {
				$layout_class = '';
				if($columns == '2') {
					$article_class = "column dt-sc-one-half";
					$firstcnt = 2;
					$carousel_column = '2';
				} else {
					$article_class = "column dt-sc-one-third";
					$firstcnt = 3;
					$carousel_column = '3';
				}
			} 
			
			if($carousel == 'true') {
				$html_tag = 'li';
			} else {
				$html_tag = 'div';
			}
			
			if(empty($categories) || $categories == 'null') {
				$cats = get_categories('taxonomy=course_category&hide_empty=1');
				$cats = get_terms( array('course_category'), array('fields' => 'ids'));		
			} else {
				$cats = explode(',', $categories);
			}
		
			if($course_type == 'featured')
				$args = array('post_type' => 'dt_courses', 'posts_per_page' => $limit, 'tax_query' => array( array(
																										'taxonomy' => 'course_category',
																										'field' => 'id',
																										'terms' => $cats
																								)), 'meta_query' => array( array( 'key' => 'featured-course', 'value' => 'true' ) ));
			else
				$args = array('post_type' => 'dt_courses', 'posts_per_page' => $limit, 'tax_query' => array( array( 
																										'taxonomy' => 'course_category',
																										'field' => 'id',
																										'terms' => $cats
																								)));
				
			$the_query = new WP_Query($args);
			
			if($the_query->have_posts()): 
			 while($the_query->have_posts()): $the_query->the_post();
			 
					$temp_class = '';
					
					if($carousel != 'true') {
						$firstcls = '';
						$no = $the_query->current_post+1;
						if(($no%$firstcnt) == 1){ $firstcls = ' first'; }
						$temp_class = 'class="'.$article_class.' '.$firstcls.'"';
					}
					if($carousel == 'true' && ($carousel_column == '2' || $carousel_column == '3')) {
						$temp_class = 'class="'.$article_class.'"';
					}
					
					$course_settings = get_post_meta(get_the_ID(), '_course_settings');
					
					$out .= '<'.$html_tag.' '.$temp_class.'>';
					
					$out .= '<article id="post-'.get_the_ID().'" class="'.implode(" ", get_post_class("dt-sc-custom-course-type {$layout_class}", get_the_ID())).'">';
																		
							$out .= '<div class="dt-sc-course-thumb">';
								$out .= '<a href="'.get_permalink().'" >';
								if(has_post_thumbnail()):
									$attachment_id = get_post_thumbnail_id(get_the_id());
									$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
									$out .= "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
								else:
									$out .= '<img src="http://placehold.it/1170x822&text=Image" alt="'.get_the_title().'" />';
								endif;
								$out .= '</a>';
								$out .= '<div class="dt-sc-course-overlay">
											<a title="'.get_the_title().'" href="'.get_permalink().'" class="dt-sc-button small white">'.__('View Course', 'dt_themes').'</a>
										</div>';
							$out .= '</div>';
						
						
                        $lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => get_the_ID() );
                        $lessons_array = get_pages( $lesson_args );
                        
						$count = $duration = 0;
						if(count($lessons_array) > 0) {
							foreach($lessons_array as $lesson) {
								$lesson_data = get_post_meta($lesson->ID, '_lesson_settings');
								if(isset($lesson_data[0]['lesson-duration'])) $duration = $duration + dttheme_wp_kses($lesson_data[0]['lesson-duration']);
								$count++;
							}
						}
                        
                        if($duration > 0) {
                            $hours = floor($duration/60); 
                            $mins = $duration % 60;
							if(strlen($mins) == 1) $mins = '0'.$mins;
							if(strlen($hours) == 1) $hours = '0'.$hours;
							if($hours == 0) {
								$duration = '00 : '.$mins;
							} else {
								$duration = $hours . ' : ' . $mins; 				
							}
                        }
						
						$out .= '<div class="dt-sc-course-details">';
						
							if( $layout_view == "list" ) {
								
								$out .= '<h5><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h5>';
								
								$starting_price = dttheme_wp_kses(get_post_meta(get_the_ID(), 'starting-price', true));
								if($starting_price != ''):
									$out .= '<span class="dt-sc-course-price"> <span class="amount"> ';
												if(dttheme_option('dt_course','currency-position') == 'after-price') 
													$out .= $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
												else
													$out .= dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
									$out .= '</span></span>';
								else:
									$out .= '<span class="dt-sc-course-price"> <span class="amount"> ';
												$out .= __('Free', 'dt_themes');
									$out .= '</span></span>';
								endif;
								
							} else {

								$starting_price = dttheme_wp_kses(get_post_meta(get_the_ID(), 'starting-price', true));
								if($starting_price != ''):
									$out .= '<span class="dt-sc-course-price"> <span class="amount"> ';
												if(dttheme_option('dt_course','currency-position') == 'after-price') 
													$out .= $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
												else
													$out .= dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
									$out .= '</span></span>';
								else:
									$out .= '<span class="dt-sc-course-price"> <span class="amount"> ';
												$out .= __('Free', 'dt_themes');
									$out .= '</span></span>';
								endif;
							
								$out .= '<h5><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h5>';
								
							}
													
							$out .= '<div class="dt-sc-course-meta">
										<p>'.get_the_term_list(get_the_ID(), 'course_category', ' ', ', ', ' ').'</p>
										<p>'.$count.'&nbsp;'.__('Lessons', 'dt_themes').'</p>
									</div>';

							if( $layout_view == "list" ) {
								$out .= '<div class="dt-sc-course-desc">'.get_the_excerpt().'</div>';
							}
							
							$out .= '<div class="dt-sc-course-data">
										<div class="dt-sc-course-duration">
											<i class="fa fa-clock-o"> </i>
											<span>'.$duration.'</span>
										</div>';
										if(function_exists('the_ratings') && !dttheme_option('general', 'disable-ratings-courses')) { 
											$out .= do_shortcode('[ratings id="'.get_the_ID().'"]');
										}
							$out .= '</div>';
						
						$out .= '</div>';
					
					$out .= '</article>';

					$out .= '</'.$html_tag.'>';

			 endwhile;
			 
			else:
				$out .= '<h2>'.__('Nothing Found.', 'dt_themes').'</h2>';
				$out .= '<p>'.__('Apologies, but no results were found for the requested archive.', 'dt_themes').'</p>';
			endif;
			wp_reset_query();
			
			if($carousel == 'true') {
				return '<div class="dt-sc-course-carousel-wrapper"><ul class="dt-sc-course-carousel" data-column="'.$carousel_column.'">'.$out.'</ul><div class="carousel-arrows"><a class="course-prev" href=""></a><a class="course-next" href=""></a></div></div>';
			} else {
				return $out;
			}
		
	}
	
	function dt_sc_courses_search( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'title'  => __('Search Courses', 'dt_themse'),
			'post_per_page'  => '-1',
		), $atts));

		global $post; $out = '';
		
		if(defined('ICL_LANGUAGE_CODE')) $icl_lang_code = ICL_LANGUAGE_CODE;
		else $icl_lang_code = '';
		
		$out .= '<div class="dt-sc-course-searchform-container">';
		$out .= '<div class="dt-sc-course-searchform">';
		$out .= '<header>';
		$out .= '<h2><span class="fa fa-search"> </span> '.$title.' </h2>';
		$out .= '</header>';
		$out .= '<div class="dt-sc-searchbox-container">';
				
				$out .= '<form name="frmcoursesearch" action="'.get_template_directory_uri().'/framework/courses_search_utils.php'.'" method="post">';

				$out .= '<div class="course-type-module">';
				$out .= '<label>'.__('Course Type','dt_themes').'</label>';
				$out .= '<select name="coursetype" id="dt-coursetype">';
				$out .= '<option value="0">'.__("Course Type","dt_themes").'</option>';
						$course_types = get_categories("taxonomy=course_category&hide_empty=1");
						foreach ( $course_types as $course_type ) {
							if ($course_type->category_parent == 0) {
								$id = esc_attr( $course_type->term_id );
								$title = esc_html( $course_type->name );
								$selected = isset($_REQUEST['coursetype']) ? $_REQUEST['coursetype'] : '';
								$out .= "<option value='{$id}' ".selected ( $selected, $id, false )." >{$title}</option>";
							}
						}        
				$out .= '</select>';
				$out .= '</div>';
				
				$out .= '<div class="sub-course-type-module">';
				$out .= '<label>'.__('Sub Course Type','dt_themes').'</label>';
				$out .= '<select name="subcoursetype" id="dt-subcoursetype">';
				$out .= '<option value="0">'.__("Sub Course Type","dt_themes").'</option>';
				$out .= '</select>';
				$out .= '</div>';
				
				$out .= '<div class="course-price-module">';
				$out .= '<label>'.__('Cost Type','dt_themes').'</label>';
				$out .= '<select name="costtype" id="dt-costtype">';
				$out .= '<option value="all">'.__("All","dt_themes").'</option>';
				$out .= '<option value="paid">'.__("Paid","dt_themes").'</option>';
				$out .= '<option value="free">'.__("Free","dt_themes").'</option>';				
				$out .= '</select>';
				$out .= '</div>';

				$out .= '<div class="search-text-module">';                        
				$out .= '<label>'.__("Search Text","dt_themes").'</label>';
						$searchtext = isset($_REQUEST['searchtext']) ? $_REQUEST['searchtext'] : '';
				$out .= '<input type="text" name="searchtext" value="'.$searchtext.'"/>';
				$out .= '</div>';
				
				$out .= '<div class="webinar-module">';
						$webinar = isset($_REQUEST['webinar']) ? $_REQUEST['webinar'] : 'off';                        
				$out .= '<label><input type="checkbox" name="webinar" '.checked( $webinar, 'on', false ).'/>';
				$out .= ''.__("Webinar","dt_themes").'</label>';
				$out .= '</div>';

				$out .= '<input type="submit" name="dt-course-search-submit" value="'.__("Search","dt_themes").'" />';
				$out .= '<input type="hidden" value="'.$post_per_page.'" name="postperpage">';
				$out .= '<input type="hidden" name="lang" value="'.$icl_lang_code.'"/>';
				$out .= '</form>';
				
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div id="dt-sc-ajax-load-image" class="search-ajax-load" style="display:none;"><img src="'.plugin_dir_url ( __FILE__ ) . "images/loading.png".'" alt="ajax-loader" /></div>';
		$out .= '</div>';
		$out .= '<div id="ajax_course_content" style="display:none;"></div>';
	
		return $out;
				
	}
	
	
	function dt_sc_subscription_form( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'image_url' => '',
			'slider' => '',
			'slider_id' => '',
			'title'  => __('Plan a Visit', 'dt_themse'),
			'submit_text' => __('Submit', 'dt_themes'),
			'success_msg' => __('Thanks for subscribing, we will contact you soon.', 'dt_themes'),
			'error_msg' => __('Mail not sent, please try again Later.', 'dt_themes'),
			'subject' => __('Subscription', 'dt_themes'),
			'admin_email' => get_bloginfo('admin_email'),
			'enable_planavisit' => 'true',
			'contact_label' => __('Inquiries', 'dt_themes'),
			'contact_number' => '',
			'course_type' => ''
		), $atts));


		if($admin_email == '') $admin_email = get_bloginfo('admin_email');
			
		$out = '';
		
		$out .= '<div class="column dt-sc-two-third no-space">  
					<div class="dt-sc-subscription-frm-image">';
					    
						if($image_url != '') {                      	
							$out .= '<img src="'.$image_url.'" alt="'.$title.'" title="'.$title.'">';
						} else if($slider != '' && $slider_id != '') {
							if($slider == 'LayerSlider') {
								$out .= do_shortcode("[layerslider id='{$slider_id}']");
							} elseif($slider == 'RevolutionSlider') {
								$out .= do_shortcode("[rev_slider $slider_id]");
							}
						}
						
					$out .= '</div>
				</div>
				<div class="column dt-sc-one-third no-space">
				
					<div class="dt-sc-subscription-frm-container">
						<h2> <i class="fa fa-clock-o"> </i> '.$title.' </h2>
						<div class="dt-sc-clear"></div>
						<form name="frmsubscription" action="'.get_template_directory_uri().'/framework/subscribe_mail.php" class="dt-sc-subscription-frm" method="post">
							<input type="text" placeholder="'.__('Full Name (required)', 'dt_themes').'" name="dtfullname" required>
							<input type="email" placeholder="'.__('Email (required)', 'dt_themes').'" name="dtemail" required>';
							
							if($course_type == 'sensei') $course_var = 'course'; else $course_var = 'dt_courses';
							$course_args = array('post_type' =>  $course_var, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'DESC', 'suppress_filters' => 0);
							$course_array = get_posts( $course_args );
							
							$out .= '<select id="dtcourse" name="dtcourse" required>';
							$out .= '<option value="">' . __( 'Preferred Courses', 'dt_themes' ) . '</option>';
							if ( count( $course_array ) > 0 ) {
								foreach ($course_array as $course_item){
									$out .= '<option value="' . esc_attr( $course_item->post_title ) . '" >' . esc_html( $course_item->post_title ) . '</option>';
								}
							}
							$out .= '</select>';
					
							if($enable_planavisit == 'true') {
								$out .= '<div class="dt-sc-check-box">
											<input type="checkbox" id="dtplanavisit" name="dtplanavisit"> <label class="checkbox-label"> <span> </span>'.__('Plan a Visit', 'dt_themes').' </label>
										</div>
										<input type="text" style="display: none;" id="dtdatetimepicker" name="dtdatetimepicker" placeholder="'.__('Date Time', 'dt_themes').'" >';
							}
					
						$out .= '<div id="ajax_subscribe_msg"></div>
								<input type="submit" class="dt-sc-button" value="'.$submit_text.'" name="btnsubscribe" id="btnsubscribe">
								<input type="hidden" value="'.$admin_email.'" name="hid_adminemail">
								<input type="hidden" value="'.$subject.'" name="hid_subject">
								<input type="hidden" value="'.$success_msg.'" name="hid_successmsg">
								<input type="hidden" value="'.$error_msg.'" name="hid_errormsg">
						</form>';
						
						if($contact_number != '') {
							$out .= '<div class="dt-sc-subscription-enquiry"> <i class="fa fa-phone"> </i> <span> '.$contact_label.' </span> '.$contact_number.' </div>';
						}
						
					$out .= '</div>                            
				
				</div>';
					
		return $out;
				
	}
	
	function dt_sc_timeline_section( $atts, $content = null ) {
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$out = '<div class="dt-sc-timeline-section">'.$content.'</div>';
					
		return $out;
				
	}
	
	function dt_sc_timeline( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'align'  => 'right',
			'class'  => ''
		), $atts));
		
		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$out = '<div class="dt-sc-timeline '.$align.' '.$class.'">
					<div class="dt-sc-one-half column first">
						'.$content.'
					</div>
				</div>';
					
		return $out;
				
	}

	function dt_sc_timeline_item( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'title'  => '',
			'subtitle'  => '',
			'fontawesome_icon' => '',
			'custom_icon' => '',
			'link' => '#'
		), $atts));

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$out = '<div class="dt-sc-timeline-content">
					<h2><a href="'.$link.'"> '.$title.' </a> </h2>
					<h4>'.$subtitle.'</h4>
					<p>';
					if( !empty($fontawesome_icon) ){
						$out .= '<i class="fa fa-'.$fontawesome_icon.'"> </i>';
					}elseif( !empty($custom_icon) ){
						$out .= '<img src="'.$custom_icon.'" alt="'.$title.'" title="'.$title.'">';
					}
					$out .= $content.'</p>
				</div>';
					
		return $out;
				
	}
		
	function dt_sc_subscribed_courses( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'hide_visit_count'  => ''
		), $atts));
		
		$out = '';
		
		if( dttheme_is_plugin_active('s2member/s2member.php') ) {
		
			$user_ccaps = get_user_field ("s2member_access_ccaps");
			$login_count = get_user_field ("s2member_login_counter");
			$display_name = get_user_field ("display_name");
			
			if($hide_visit_count != 'true') {
				$count_suffix = '';
				if($login_count == 1) $count_suffix = '<sup>st</sup>';
				elseif($login_count == 2) $count_suffix = '<sup>nd</sup>';
				elseif($login_count == 3) $count_suffix = '<sup>rd</sup>';
				elseif($login_count >= 4) $count_suffix = '<sup>th</sup>';
				
				$out .= '<h2 class="border-title">'.__('Hi '.$display_name.', this is your '.$login_count.$count_suffix.' visit', 'dt_themes').'<span> </span></h2>';
			}
			
			if(isset($user_ccaps) && !empty($user_ccaps)) {
				$out .= '<h3>'.__('Courses you have subscribed so far,', 'dt_themes').'</h3>';
				$out .=  '<div class="clear"> </div> <ol class="dt-sc-lessons-list dt-sc-user-subscribed-courses">';
				foreach($user_ccaps as $ccap) {
					$ccap = (int)str_replace('cid_', '', $ccap);
					$out .= '<li><h6><a href="'.get_permalink($ccap).'">'.get_the_title($ccap).'</a></h6></li>';
				}
				$out .= '</ol>';
			}
			
		}
		
		return $out;
				
				
	}


	function dt_sc_newsletter_section( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'title'  => '',
		), $atts));

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$out = '<section id="newsletter">
					<h2 class="border-title aligncenter">'.$title.'</h2>
					<h6> '.$content.' </h6>
					<form name="frmsubscribe" method="post" class="dt-sc-subscribe-frm">
						<input type="email" name="dt_sc_mc_emailid" required="" placeholder="'.__('Your Email Address', 'dt_themes').'" />
						<input type="hidden" name="dt_sc_mc_listid" value="'.stripslashes(dttheme_option('general','mailchimp-listid')).'" />
						<input type="submit" name="submit" class="dt-sc-button small" value="'.__('Subscribe', 'dt_themes').'" />
					</form>';
					if( isset( $_REQUEST['dt_sc_mc_emailid']) ):
						require_once(IAMD_FW."theme_widgets/mailchimp/MCAPI.class.php");
						$mcapi = new MCAPI( dttheme_wp_kses(dttheme_option('general','mailchimp-key')) );
						$list_id = dttheme_option('general','mailchimp-listid');
			
						if($mcapi->listSubscribe($list_id, $_REQUEST['dt_sc_mc_emailid']) ):
							$msg = '<span class="success-msg">'.__('Success! Check your inbox or spam folder for a message containing a confirmation link.', 'dt_themes').'</span>';
						else:
							$msg = '<span class="error-msg"><b>'.__('Error:', 'dt_themes').'</b>&nbsp; ' . $mcapi->errorMessage.'</span>';
						endif;
					endif;
					if ( isset ( $msg ) ) $out .= '<div class="dt_sc_mc_result">'.$msg.'</div>';
		$out .= '</section>';
					
		return $out;
				
	}
	
	
	function dt_sc_slider_search( $atts, $content = null ) {
		
		extract(shortcode_atts(array(
			'title'  => '',
			'button_title'  => '',
			'button_link'  => '#',
			'disable_search'  => 'false'
		), $atts));

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$search_text = empty($_REQUEST['s']) ? '' : get_search_query();
				
		if(dttheme_get_page_permalink_by_its_template('tpl-courses-search.php') != '') {	
			$action = dttheme_get_page_permalink_by_its_template('tpl-courses-search.php');
			$srch_str = 'searchtext';
		} else {
			$action = esc_url(home_url('/'));
			$srch_str = 's';
		}
		
		if(defined('ICL_LANGUAGE_CODE')) $icl_lang_code = ICL_LANGUAGE_CODE;
		else $icl_lang_code = '';
		
		$out = '<div class="slider-search">';
					if($disable_search != 'true') {
						$out .= '<form method="post" id="courses-search" class="courses-search" action="'.$action.'">                  	
									<input id="'.$srch_str.'" name="'.$srch_str.'" type="text" value="'.$search_text.'" placeholder="'.__('Search Course', 'dt_themes').'" />
									<input type="hidden" name="search-type" value="courses" />
									<input type="hidden" name="lang" value="'.$icl_lang_code.'"/>
									<input type="submit" value="">
								</form>';
					}
				if($title != '') 
					$out .= '<h4>'.$title.'</h4>';
				if($button_title != '')
					$out .= '<a href="'.$button_link.'" title="'.$button_title.'"> '.$button_title.' <span class="fa fa-angle-double-right"> </span> </a>';
		$out .= '</div>';
					
		return $out;
				
	}
	
	function dt_sc_widgets($attrs, $content = null) {
		extract ( shortcode_atts ( array (
				'widget_name' => '',
				'widget_wpname' => '',
				'widget_wpid' => ''
		), $attrs ) );
		
		if($widget_name != ''):	
		
			$widget_id = explode('-', $widget_wpid);
			$widget_id = $widget_id[0];
			
			foreach($attrs as $key=>$value):
				$instance[$key] = $value;			
			endforeach;
			
			$instance = array_filter($instance);
			
			//Event Widgets
			if($widget_name == 'TribeCountdownWidget') {
				$eventid = $instance['event_id'];	
				$eventid = explode('|', $eventid);
				$instance['event_ID'] = $eventid[0];
			}
			
			if($widget_name == 'TribeVenueWidget') {
				$venueid = $instance['venue_id'];	
				$instance['venue_ID'] = $venueid;
			}

			if(($widget_name == 'TribeEventsAdvancedListWidget' || $widget_name == 'TribeEventsMiniCalendarWidget') && isset($instance['selector'])) {
				$instance['filters'] = '{"tribe_events_cat":["'.$instance['selector'].'"]}';
			}
			
			if(class_exists('TribeEventsPro')) {
				wp_enqueue_style( 'widget-calendar-pro-style', TribeEventsPro::instance()->pluginUrl . 'resources/widget-calendar-full.css', array(), apply_filters( 'tribe_events_pro_css_version', TribeEventsPro::VERSION ) );
			}
			//Event Widgets End
			
			//Woocommerce Start
			if(substr($widget_name, 0, 2) == 'WC') $add_cls = 'woocommerce';
			else $add_cls = '';
			//Woocommerce End
			
			ob_start();
			the_widget($widget_name, $instance, 'before_widget=<aside id="'.$widget_wpid.'" class="widget '.$add_cls.' '.$widget_wpname.'">&after_widget=</aside>&before_title=<h3 class="widgettitle">&after_title=<span></span></h3>');
			$output = ob_get_contents();
			wp_cache_delete( $widget_id , 'widget' );
			ob_end_clean();
			
			return $output;
							
		endif;

	}

	function dt_sc_doshortcode($attrs, $content = null) {
		extract ( shortcode_atts ( array (
				'width' => '100',
				'animation' => '',
				'animation_delay' => ''
		), $attrs ) );

		$content = DTCoreShortcodesDefination::dtShortcodeHelper ( $content );

		$danimation = !empty( $animation ) ? " data-animation='{$animation}' ": "";
		$ddelay = ( !empty( $animation ) && !empty( $animation_delay )) ? " data-delay='{$animation_delay}' " : "";
		$danimate = !empty( $animation ) ? "animate": "";

		$first = (isset ( $attrs [0] ) && trim ( $attrs [0] == 'first' )) ? 'first' : '';

		$out = '<div class="column '.$danimate.' '.$first.'" style="width:'.$width.'%;" '.$danimation.' '.$ddelay.'>';
		$cont = do_shortcode($content);
		if(isset($cont))
			$out .= $cont;
		else
			$out .= $content;
		$out .= '</div>';
		return $out;
	}

	function dt_sc_resizable($attrs, $content = null) {		
		extract ( shortcode_atts ( array (
				'width' => '',
				'class' => '',
				'animation' => '',
				'animation_delay' => ''
		), $attrs ) );

		$danimation = !empty( $animation ) ? " data-animation='{$animation}' ": "";
		$ddelay = (!empty( $animation ) && !empty( $animation_delay )) ? " data-delay='{$animation_delay}' " : "";
		$danimate = !empty( $animation ) ? "animate": "";

		$style = (!empty( $width ) ) ? ' style="width:'.$width.'%;" ' : "";
	
		$first = (isset ( $attrs [0] ) && trim ( $attrs [0] == 'first' )) ? 'first' : '';
		$content = do_shortcode(DTCoreShortcodesDefination::dtShortcodeHelper ( $content ));
		$out = "<div class='column {$class} {$danimate} {$first}' {$danimation} {$ddelay} {$style}>{$content}</div>";
		return $out;
	}	
		
	function dt_sc_certificate($attrs, $content = null) {		
		extract ( shortcode_atts ( array (
				'item' => '',
		), $attrs ) );

		$out = '';
		
		$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : get_current_user_id();;
		$user_info = get_userdata($user_id);
		
		$certificate_id = isset($_REQUEST['certificate_id']) ? $_REQUEST['certificate_id'] : 0;
		$course_id = isset($_REQUEST['course_id']) ? $_REQUEST['course_id'] : 0;
		
		if($item == 'student_name') {
			$out .=  isset($user_info->display_name) ? '<div class="dt-sc-student-name">'.$user_info->display_name.'</div>' : '';
		} else if($item == 'course_name') {
			$out .= '<strong>'.get_the_title($course_id).'</strong>';
		} else if($item == 'student_percent') {

			$dt_gradings = array(
							'post_type'=>'dt_gradings',
							'meta_query'=>array(),
							'suppress_filters' => 0
						);
			
			$dt_gradings['meta_query'][] = array(
												'key'     => 'dt-course-id',
												'value'   => $course_id,
												'compare' => '=',
												'type'    => 'numeric'
											);
		
			$dt_gradings['meta_query'][] = array(
												'key'     => 'dt-user-id',
												'value'   => $user_id,
												'compare' => '=',
												'type'    => 'numeric'
											);
								
											
			$dt_grade_post = get_posts( $dt_gradings );
			$cnt = count($dt_grade_post);
			
			if(isset($dt_grade_post)) {
				$total_percent = 0;
				foreach($dt_grade_post as $dt_grade) {
					$grade_post_id = $dt_grade->ID;	
					$percent = get_post_meta ( $grade_post_id, "marks-obtained-percent",true); 
					$total_percent = $total_percent+$percent;
				}
			}
			
			if($cnt != 0) $out .=  '<strong>'.round($total_percent/$cnt, 2).__('%', 'dt_themes').'</strong>';

		} else if($item == 'student_email') {
			$out .=  isset($user_info->user_email) ? '<strong>'.$user_info->user_email.'</strong>' : '';
		} else if($item == 'date') {
			$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array(), 'orderby' => 'post_date', 'order' => 'DESC', 'suppress_filters' => 0 );
			$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
			$dtgradings['meta_query'][] = array( 'key' => 'dt-course-id', 'value' => $course_id, 'compare' => '=', 'type' => 'numeric' );
			$dtgradings_post = get_posts( $dtgradings );
			$dt = strtotime($dtgradings_post[0]->post_date);
			$out .= date('d M Y', $dt);
		}
		
		return $out;
	}
	
	function dt_sc_certificate_template($attrs, $content = null) {		
		extract ( shortcode_atts ( array (
				'type' => 'type1',
				'certificate_title' => '',
				'certificate_subtitle' => '',
				'certificate_bg_image' => '',
				'logo_topleft' => '',
				'logo_topright' => '',
				'logo_bottomcenter' => '',
				'authority_sign' => '',
				'authority_sign_name' => '',
				'show_certificate_issueddate' => 'yes',
		), $attrs ) );

		$content = do_shortcode(DTCoreShortcodesDefination::dtShortcodeHelper ( $content ));

		$out = '';
		
		$cert_bg = '';
		if($certificate_bg_image != '') {
			$cert_bg = 'style="background:url('.$certificate_bg_image.') center center no-repeat"';	
		}
		
		if($type == 'type1') {
			
			$out .= '<div class="dt-sc-course-certificate-wrapper">
						<div class="dt-sc-course-certificate" '.$cert_bg.'>                        
							
							<div class="dt-sc-cert-header">';
								if($logo_topleft != '') $out .= '<img src="'.$logo_topleft.'" alt="'.__('Certificate Logo Left', 'dt_themes').'" title="'.__('Certificate Logo Left', 'dt_themes').'" class="dt-sc-cert-comp-logo">';
								if($logo_topright != '') $out .= '<img src="'.$logo_topright.'" alt="'.__('Certificate Logo Right', 'dt_themes').'" title="'.__('Certificate Logo Right', 'dt_themes').'" class="dt-sc-cert-badge">';                     	
								if($certificate_title != '') $out .= '<h2> '.$certificate_title.' </h2>';
					$out .= '</div>
							
							<div class="dt-sc-cert-content">'.$content.'</div>
							
							<div class="dt-sc-cert-footer">';
								
								if($show_certificate_issueddate == 'yes') {
									$out .= '<div class="dt-sc-cert-date">
												<p> <span> '.do_shortcode('[dt_sc_certificate item="date" /]').' </span> <br>'. __('Date', 'dt_themes').' </p>
											</div>';
								}
								
								if($logo_bottomcenter != '') {
									$out .= '<div class="dt-sc-cert-logo">
												<img src="'.$logo_bottomcenter.'" alt="'.__('Certificate Logo Bottom', 'dt_themes').'" title="'.__('Certificate Logo Bottom', 'dt_themes').'">
											</div>';
								}
								
								$out .= '<div class="dt-sc-cert-sign">';
											if($authority_sign != '') $out .= '<img src="'.$authority_sign.'" alt=" '.__('Authorized Signature', 'dt_themes').'" title="'.__('Authorized Signature', 'dt_themes').'">';                           
											if($authority_sign_name != '') $out .= '<p> '.__('Authorized Signature - ', 'dt_themes').$authority_sign_name.' </p>';
								$out .= '</div>';
								
					$out .= '</div>
							
						</div>
					</div>';
		
		} else if($type == 'type2') {
			
             $out .= '<div class="dt-sc-course-certificate-wrapper type2">';
						if($logo_topleft != '') $out .= '<img src="'.$logo_topleft.'" alt="'.__('Certificate Logo Left', 'dt_themes').'" title="'.__('Certificate Logo Left', 'dt_themes').'" class="dt-sc-cert-badge">';

						$out .= '<div class="dt-sc-course-certificate" '.$cert_bg.'>                        
									<div class="dt-sc-cert-header">';
										if($certificate_title != '') $out .= '<h2> '.$certificate_title.' </h2>';
										if($certificate_subtitle != '') $out .= '<h3> '.$certificate_subtitle.' </h3>';
							$out .= '</div>
							
									<div class="dt-sc-cert-content">'.$content.'</div>
							
									<div class="dt-sc-cert-footer">';
									
										if($show_certificate_issueddate == 'yes') {
											$out .= '<div class="dt-sc-cert-date">
														<p> <span> '.do_shortcode('[dt_sc_certificate item="date" /]').' </span> <br>'. __('Date', 'dt_themes').' </p>
													</div>';
										}
		
										$out .= '<div class="dt-sc-cert-sign">';
											if($authority_sign != '') $out .= '<img src="'.$authority_sign.'" alt=" '.__('Authorized Signature', 'dt_themes').'" title="'.__('Authorized Signature', 'dt_themes').'">';                           
											if($authority_sign_name != '') $out .= '<p> '.__('Authorized Signature - ', 'dt_themes').$authority_sign_name.' </p>';
										$out .= '</div>
										
									</div>
									
								</div>
								
					</div>';
						
		} else if($type == 'type3') {
			
               $out .= '<div class="dt-sc-course-certificate-wrapper type3">
                            <div class="dt-sc-course-certificate" '.$cert_bg.'>                        
                                
                                <div class="dt-sc-cert-header">';
										if($certificate_title != '') $out .= '<h2> '.$certificate_title.' </h2>';
										if($certificate_subtitle != '') $out .= '<h3> '.$certificate_subtitle.' </h3>';
							$out .= '</div>
                                
                                <div class="dt-sc-cert-content">'.$content.'</div>
                                
								<div class="dt-sc-cert-footer">';
								
									if($show_certificate_issueddate == 'yes') {
										$out .= '<div class="dt-sc-cert-date">
													<p> <span> '.do_shortcode('[dt_sc_certificate item="date" /]').' </span> <br>'. __('Date', 'dt_themes').' </p>
												</div>';
									}
	
									$out .= '<div class="dt-sc-cert-sign">';
										if($authority_sign != '') $out .= '<img src="'.$authority_sign.'" alt=" '.__('Authorized Signature', 'dt_themes').'" title="'.__('Authorized Signature', 'dt_themes').'">';                           
										if($authority_sign_name != '') $out .= '<p> '.__('Authorized Signature - ', 'dt_themes').$authority_sign_name.' </p>';
									$out .= '</div>
									
								</div>
                                
                            </div>
                        </div>';
			
		}
		
		return $out;
	}
		
}
new DTCoreShortcodesDefination();?>