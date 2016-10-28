(function($){
	"use strict";
	
	var $fw_url = mytheme_urls.framework_base_url,
		$patterns_url = $fw_url+"theme_options/images/patterns/",
		$bg_color = "",
		$bg_opacity = "";

	function hex2rgb(hex, opacity) {
		var h=hex.replace('#', '');
        h =  h.match(new RegExp('(.{'+h.length/3+'})', 'g'));

        for(var i=0; i<h.length; i++)
            h[i] = parseInt(h[i].length==1? h[i]+h[i]:h[i], 16);

        if (typeof opacity != 'undefined')  h.push(opacity);

        return 'rgba('+h.join(',')+')';
	}


	wp.customize("dt_skin",function( value ){
		value.bind(function(to){
			var $href = $("link[id='skin-css']").attr("href");
			    $href = $href.substr(0,$href.lastIndexOf("/"));
			    $href = $href.substr(0,$href.lastIndexOf("/"))+"/"+to+"/style.css";
			    $("link[id='skin-css']").attr("href",$href);
		});
	});

	wp.customize("dt_layout", function( value ){
		value.bind(function(to){
			if( "boxed" === to ) {
				$("body").addClass("boxed");
				$('body').css('background-image', 'url('+$patterns_url+mytheme_urls.layout_pattern+')');
			} else {
				 $("body").removeAttr("style").removeClass("boxed").css({'background-image':'none','background-color':'#F3F3F3'});
			}
		});
	});

	wp.customize("dt_boxed_layout_bg",function( value ){
		value.bind(function(to){
			var	$image = $patterns_url+to;
			$('body').css('background-image', 'url('+$image+')');
		});
	});

	wp.customize("dt_boxed_layout_bg_color", function( value ){
		$bg_color = value.get();
		value.bind(function(to){
			$bg_color = to;
			$('body').css('background-color',hex2rgb( $bg_color, $bg_opacity));
		});
	});

	wp.customize("dt_boxed_layout_bg_opacity", function( value ){

		$bg_opacity = value.get();

		value.bind(function(to){
			$bg_opacity = to;
			$('body').css('background-color',hex2rgb( $bg_color, $bg_opacity));
		});
	});

// Menu Settings
	//Menu Font
	wp.customize("dt_menu_font_type",function( value ){
		value.bind(function(to){
			if( to === "standard" ) {
				$("link#menu-font").remove();
			}
		});
	});//Menu Font End

	//Menu Google Font
	wp.customize("dt_menu_font",function( value ){
		value.bind(function(to){
			var $font = mytheme_customize_fonts.fonts[to];
			var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
			$('<link id="menu-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
			$("#main-menu > ul.menu > li > a").css("font-family",$font);
		});
	});//Menu Google Font End

	//Menu Standard Font
	wp.customize("dt_menu_standard_font", function( value){
		value.bind(function(to){
			$("nav#main-menu ul li a, .mobile-menu").css("font-family",to);
		});
	});//Menu Standard Font

	//Menu Standard Font Style
	wp.customize("dt_menu_standard_font_style", function( value){
		value.bind(function(to){
			if( to === "Normal" || to === "Italic" ) {
				$("#main-menu ul.menu li a").css("font-style",to);
				$("#main-menu ul.menu li a").css("font-weight","normal");
			}else if( to === "Bold Italic"){
				$("#main-menu ul.menu li a").css("font-style","italic");
				$("#main-menu ul.menu li a").css("font-weight","bold");
			}else{
				$("#main-menu ul.menu li a").css("font-weight","bold");
				$("#main-menu ul.menu li a").css("font-style","normal");
			}
		});
	});//Menu Standard Font Style

	//Menu Font Size
	wp.customize("dt_menu_font_size",function( value) {
		value.bind(function(to){
			$("#main-menu > ul.menu > li > a").css("font-size",to+"px");
			var $submenu_font = to-2;
			$("#main-menu ul.sub-menu li a, #main-menu ul li.menu-item-simple-parent ul li a").css("font-size",$submenu_font+"px");
		});
	});//Menu Font Size

	//Menu Font Primary Color
	wp.customize("dt_menu_primary_color",function( value) {
		value.bind(function(to){
			$("#main-menu ul.menu li a").css("color",to);
		});
	});
// Menu Settings End

// Body Settings
	//Body Font Type
	wp.customize("dt_body_font_type",function( value ){
		value.bind(function(to){
			if( to === "standard" ) {
				$("link#body-font").remove();
			}
		});
	});//Body Font Type End

	//Body Standard Font
	wp.customize("dt_body_standard_font", function( value){
		value.bind(function(to){
			$("body").css("font-family",to);
		});
	});//Body Standard Font	

	//Body Standard Font Style
	wp.customize("dt_body_standard_font_style", function( value){
		value.bind(function(to){
			if( to === "Normal" || to === "Italic" ) {
				$("body").css("font-style",to);
				$("body").css("font-weight","normal");
			}else if( to === "Bold Italic"){
				$("body").css("font-style","italic");
				$("body").css("font-weight","bold");
			}else{
				$("body").css("font-weight","bold");
				$("body").css("font-style","normal");
			}
		});
	});//Body Standard Font Style	

	//Body Google Font
	wp.customize("dt_body_font",function( value ){
		value.bind(function(to){
			var $font = mytheme_customize_fonts.fonts[to];
			var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
			$('<link id="body-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
			$("body").css("font-family",$font);
		});
	});//Menu Google Font End

	//Body Font Size
	wp.customize("dt_body_font_size",function( value) {
		value.bind(function(to){
			$("body").css("font-size",to+"px");
		});
	});//Body Font Size

	//Body Font Color
	wp.customize("dt_body_font_color",function( value) {
		value.bind(function(to){
			$("body").css("color",to);
		});
	});	

	//Body Anchor Font Color
	wp.customize("dt_body_primary_color",function( value) {
		value.bind(function(to){
			$("body a").css("color",to);
		});
	});	
// Body Font Settings End

//Footer Settings
	//Footer Title Font Type
	wp.customize("dt_footer_title_font_type",function( value ){
		value.bind(function(to){
			if( to === "standard" ) {
				$("link#footer-title-font").remove();
			}
		});
	});//Footer Font Type End

	//Footer Title Standard Font
	wp.customize("dt_footer_title_standard_font", function( value){
		value.bind(function(to){
			$("#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a").css("font-family",to);
		});
	});//Footer Title Standard Font

	//Footer Title Standard Font Style
	wp.customize("dt_footer_title_standard_font_style", function( value){
		value.bind(function(to){
			var $sel = "#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a";
			if( to === "Normal" || to === "Italic" ) {
				$($sel).css("font-style",to);
				$($sel).css("font-weight","normal");
			}else if( to === "Bold Italic"){
				$($sel).css("font-style","italic");
				$($sel).css("font-weight","bold");
			}else{
				$($sel).css("font-weight","bold");
				$($sel).css("font-style","normal");
			}
		});
	});//Footer Title Standard Font Style	

	//Footer Title Font Size
	wp.customize("dt_footer_title_font_size",function( value) {
		value.bind(function(to){
			$("#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a").css("font-size",to+"px");
		});
	});//Footer Title Font Size

	//Footer Title Font Color
	wp.customize("dt_footer_title_font_color",function( value) {
		value.bind(function(to){
			$("#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a").css("color",to);
		});
	});//Footer Title Font Color

	//Footer Title Google Font
	wp.customize("dt_footer_title_font",function( value ){
		value.bind(function(to){
			var $font = mytheme_customize_fonts.fonts[to];
			var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
			$('<link id="footer-title-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
			$("#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a").css("font-family",$font);
		});
	});//Footer Title Google Font End

	//Footer Content Font Type
	wp.customize("dt_footer_content_font_type",function( value ){
		value.bind(function(to){
			if( to === "standard" ) {
				$("link#footer-content-font").remove();
			}
		});
	});

	//Footer Content Standard Font
	wp.customize("dt_footer_content_standard_font", function( value){
		value.bind(function(to){
			$("#footer .widget.widget_recent_entries .entry-metadata .author, #footer .widget.widget_recent_entries .entry-meta .date, #footer label, #footer .widget ul li, #footer .widget ul li:hover, .copyright, #footer .widget.widget_recent_entries .entry-metadata .tags, #footer .categories ").css("font-family",to);
		});
	});//Footer Content Standard Font

	//Footer content Standard Font Style
	wp.customize("dt_footer_content_standard_font_style", function( value){
		value.bind(function(to){
			var $sel = "#footer .widget.widget_recent_entries .entry-metadata .author, #footer .widget.widget_recent_entries .entry-meta .date, #footer label, #footer .widget ul li, #footer .widget ul li:hover, .copyright, #footer .widget.widget_recent_entries .entry-metadata .tags, #footer .categories ";
			if( to === "Normal" || to === "Italic" ) {
				$($sel).css("font-style",to);
				$($sel).css("font-weight","normal");
			}else if( to === "Bold Italic"){
				$($sel).css("font-style","italic");
				$($sel).css("font-weight","bold");
			}else{
				$($sel).css("font-weight","bold");
				$($sel).css("font-style","normal");
			}
		});
	});//Footer Title Standard Font Style	

	//Footer Content Font Size
	wp.customize("dt_footer_content_font_size",function( value) {
		value.bind(function(to){
			$("#footer .widget.widget_recent_entries .entry-metadata .author, #footer .widget.widget_recent_entries .entry-meta .date, #footer label, #footer .widget ul li, #footer .widget ul li:hover, .copyright, #footer .widget.widget_recent_entries .entry-metadata .tags, #footer .categories ").css("font-size",to+"px");
		});
	});//Footer content Font Size

	//Footer Content Google Font
	wp.customize("dt_footer_content_font",function( value ){
		value.bind(function(to){
			var $font = mytheme_customize_fonts.fonts[to];
			var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
			$('<link id="footer-content-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
			$("#footer .widget.widget_recent_entries .entry-metadata .author, #footer .widget.widget_recent_entries .entry-meta .date, #footer label, #footer .widget ul li, #footer .widget ul li:hover, .copyright, #footer .widget.widget_recent_entries .entry-metadata .tags, #footer .categories ").css("font-family",$font);
		});
	});//Footer Content Google Font End

	//Footer Content Font Color
	wp.customize("dt_footer_content_font_color",function( value) {
		value.bind(function(to){
			$("#footer .widget.widget_recent_entries .entry-metadata .author, #footer .widget.widget_recent_entries .entry-meta .date, #footer label, #footer .widget ul li, #footer .widget ul li:hover, .copyright, #footer .widget.widget_recent_entries .entry-metadata .tags, #footer .categories ").css("color",to);
		});
	});	

	//Footer content Primary Color
	wp.customize("dt_footer_primary_color",function( value) {
		value.bind(function(to){
			$("#footer ul li a, #footer .widget_categories ul li a, #footer .widget.widget_recent_entries .entry-metadata .tags a, #footer .categories a, .copyright a ").css("color",to);
		});
	});	
// Footer Settings End

//Typography Settings
	//H1
		wp.customize("dt_h1_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h1-font").remove();
				}
			});
		});

		wp.customize("dt_h1_standard_font", function( value){
			value.bind(function(to){
				$("h1").css("font-family",to);
			});
		});

		wp.customize("dt_h1_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h1 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});

		wp.customize("dt_h1_font_size",function( value) {
			value.bind(function(to){
				$("h1").css("font-size",to+"px");
			});
		});

		wp.customize("dt_h1_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h1-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h1").css("font-family",$font);
			});
		});

		wp.customize("dt_h1_font_color",function( value) {
			value.bind(function(to){
				$("h1").css("color",to);
			});
		});	
	//H1 End

	//H2
		wp.customize("dt_h2_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h2-font").remove();
				}
			});
		});

		wp.customize("dt_h2_standard_font", function( value){
			value.bind(function(to){
				$("h2").css("font-family",to);
			});
		});

		wp.customize("dt_h2_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h2 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});

		wp.customize("dt_h2_font_size",function( value) {
			value.bind(function(to){
				$("h2").css("font-size",to+"px");
			});
		});

		wp.customize("dt_h2_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h2-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h2").css("font-family",$font);
			});
		});

		wp.customize("dt_h2_font_color",function( value) {
			value.bind(function(to){
				$("h2").css("color",to);
			});
		});	
	//H2 End
	
	//H3
		wp.customize("dt_h3_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h3-font").remove();
				}
			});
		});

		wp.customize("dt_h3_standard_font", function( value){
			value.bind(function(to){
				$("h3").css("font-family",to);
			});
		});

		wp.customize("dt_h3_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h3 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});

		wp.customize("dt_h3_font_size",function( value) {
			value.bind(function(to){
				$("h3").css("font-size",to+"px");
			});
		});

		wp.customize("dt_h3_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h3-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h3").css("font-family",$font);
			});
		});

		wp.customize("dt_h3_font_color",function( value) {
			value.bind(function(to){
				$("h3").css("color",to);
			});
		});	
	//H3 End
	
	//H4
		wp.customize("dt_h4_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h4-font").remove();
				}
			});
		});
		
		wp.customize("dt_h4_standard_font", function( value){
			value.bind(function(to){
				$("h4").css("font-family",to);
			});
		});
		
		wp.customize("dt_h4_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h4 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});
		
		wp.customize("dt_h4_font_size",function( value) {
			value.bind(function(to){
				$("h4").css("font-size",to+"px");
			});
		});
		
		wp.customize("dt_h4_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h4-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h4").css("font-family",$font);
			});
		});
		
		wp.customize("dt_h4_font_color",function( value) {
			value.bind(function(to){
				$("h4").css("color",to);
			});
		});	
	//H4 End

	//H5
		wp.customize("dt_h5_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h5-font").remove();
				}
			});
		});
		
		wp.customize("dt_h5_standard_font", function( value){
			value.bind(function(to){
				$("h5").css("font-family",to);
			});
		});
		
		wp.customize("dt_h5_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h5 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});
		
		wp.customize("dt_h5_font_size",function( value) {
			value.bind(function(to){
				$("h5").css("font-size",to+"px");
			});
		});
		
		wp.customize("dt_h5_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h5-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h5").css("font-family",$font);
			});
		});
		
		wp.customize("dt_h5_font_color",function( value) {
			value.bind(function(to){
				$("h5").css("color",to);
			});
		});	
	//H5 End

	//H6
		wp.customize("dt_h6_font_type",function( value ){
			value.bind(function(to){
				if( to === "standard" ) {
					$("link#h6-font").remove();
				}
			});
		});
		
		wp.customize("dt_h6_standard_font", function( value){
			value.bind(function(to){
				$("h6").css("font-family",to);
			});
		});
		
		wp.customize("dt_h6_standard_font_style", function( value){
			value.bind(function(to){
				var $sel = "h6 ";
				if( to === "Normal" || to === "Italic" ) {
					$($sel).css("font-style",to);
					$($sel).css("font-weight","normal");
				}else if( to === "Bold Italic"){
					$($sel).css("font-style","italic");
					$($sel).css("font-weight","bold");
				}else{
					$($sel).css("font-weight","bold");
					$($sel).css("font-style","normal");
				}
			});
		});
		
		wp.customize("dt_h6_font_size",function( value) {
			value.bind(function(to){
				$("h6").css("font-size",to+"px");
			});
		});
		
		wp.customize("dt_h6_font",function( value ){
			value.bind(function(to){
				var $font = mytheme_customize_fonts.fonts[to];
				var $url = "https://fonts.googleapis.com/css?family="+( $font.replace(" ","+"));
				$('<link id="h6-font" type="text/css" media="all" href="'+ $url +'" rel="stylesheet">' ).appendTo( $( 'head' ) );
				$("h6").css("font-family",$font);
			});
		});
		
		wp.customize("dt_h6_font_color",function( value) {
			value.bind(function(to){
				$("h6").css("color",to);
			});
		});	
	//H6 End
//Typography Settings End
	
}(jQuery));