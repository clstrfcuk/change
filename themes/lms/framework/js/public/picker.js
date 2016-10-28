jQuery(document).ready(function($){

  "use strict";
  var $picker_container = jQuery("div.dt-style-picker-wrapper"),
      $theme_url = mytheme_urls.theme_base_url,
      $fw_url = mytheme_urls.framework_base_url,
	  $rtl = mytheme_urls.isRTL,
      $patterns_url = $fw_url+"theme_options/images/patterns/";
  
  //Applying Cookies
  if($.cookie("lms_skin")!== null ){
  //if(  $.cookie("lms_skin")!== undefined ) {

    if( mytheme_urls.is_admin === '1' ) {
      $.cookie("lms_skin",mytheme_urls.skin, { path: '/' });
    }

    var $href = mytheme_urls.theme_base_url + 'skins/' + $.cookie("lms_skin")+"/style.css";

    $("link[id='skin-css']").attr("href",$href);
    $("ul.color-picker a[id='"+$.cookie("lms_skin")+"']").addClass("selected");
  }else{
	$("ul.color-picker a:first").addClass("selected");
  }

	if($rtl == true) {
  
		if ( $.cookie('lms-control-open') === '1' ) {
			$picker_container.animate({left: -230});
			$('a.style-picker-ico').addClass('control-open');
		} else {
			$picker_container.animate( { left: 0 } );
			$('a.style-picker-ico').removeClass('control-open');
		}
  
	} else {
  
		if ( $.cookie('lms-control-open') === '1' ) {
			$picker_container.animate({right: -230});
			$('a.style-picker-ico').addClass('control-open');
		} else {
			$picker_container.animate( { right: 0 } );
			$('a.style-picker-ico').removeClass('control-open');
		}
  
	}
	
	
  //1. Applying Layout & patterns
  if($.cookie("lms_layout") === "boxed"){
	  
    $("ul.layout-picker li a").removeAttr("class");
    $("ul.layout-picker li a[id='"+$.cookie("lms_layout")+"']").addClass("selected");

	  $("div#pattern-holder").removeAttr("style");
    $('body').addClass('boxed');
    if($.cookie("lms_pattern")) {
	    var $i = $.cookie("lms_pattern");
    	var $img = $patterns_url+$i;
        $('body').css('background-image', 'url('+$img+')');
	}
	
    
  }//Applying Cookies End
  
  //Picker On/Off
  $("a.style-picker-ico").click(function(e){
    var $this = $(this);	

	if($rtl == true) {
    
		if($this.hasClass('control-open')){
			$picker_container.animate({right: 0},function(){$this.removeClass('control-open');});
			$.cookie('lms-control-open', 1, { path: '/' });	
		}else{
			$picker_container.animate({right: -230},function(){$this.addClass('control-open');});
			$.cookie('lms-control-open', 0, { path: '/' });
		}
		
	} else {
		
		if($this.hasClass('control-open')){
			$picker_container.animate({left: 0},function(){$this.removeClass('control-open');});
			$.cookie('lms-control-open', 1, { path: '/' });	
		}else{
			$picker_container.animate({left: -230},function(){$this.addClass('control-open');});
			$.cookie('lms-control-open', 0, { path: '/' });
		}
		
	}
	
	e.preventDefault();
   });//Picker On/Off end

  //Layout Picker
  $("ul.layout-picker a").click(function(e){
    var $this = $(this);
    $("ul.layout-picker a").removeAttr("class");
    $this.addClass("selected");
    $.cookie("lms_layout", $this.attr("id"), { path: '/' });

    if( $.cookie("lms_layout") === "boxed") {
      $("body").addClass("boxed");
      $("div#pattern-holder").slideDown();
		if( $.cookie("lms_pattern") == null ){
			$("ul.pattern-picker a:first").addClass('selected');
			$.cookie("lms_pattern","pattern1.jpg",{ path: '/' });
			$('body').css('background-image', 'url('+$patterns_url+'pattern1.jpg)');
		} else {
			$img = $patterns_url+$.cookie("lms_pattern");
			$('body').css('background-image', 'url('+$img+')');
      }
    } else {
      $("body").removeAttr("style").removeClass("boxed");
      $("div#pattern-holder").slideUp();
      $("ul.pattern-picker a").removeAttr("class");
    }
    window.location.href = location.href;
    e.preventDefault();
  });//Layout Picker End

  //Pattern Picker
  $("ul.pattern-picker a").click(function(e){
    
    if($.cookie("lms_layout") === "boxed"){
      var $this = $(this);
      $("ul.pattern-picker a").removeAttr("class");
      $this.addClass("selected");
      $.cookie("lms_pattern", $this.attr("data-image"), { path: '/' });
      var $img = $patterns_url+$.cookie("lms_pattern");
      $('body').css('background-image', 'url('+$img+')');
    }
    e.preventDefault();
  });//Pattern Picker End

  //Color Picker
  $("ul.color-picker a").click(function(e){
    var $this = $(this);
    $("ul.color-picker a").removeAttr("class");
    $this.addClass("selected");
    $.cookie("lms_skin", $this.attr("id"), { path: '/' });
    var $href = mytheme_urls.theme_base_url + 'skins/' + $this.attr("id")+"/style.css";
    $("link[id='skin-css']").attr("href",$href);
    e.preventDefault();
  });//Color Picker End
});