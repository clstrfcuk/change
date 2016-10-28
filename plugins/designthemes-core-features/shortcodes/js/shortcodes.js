jQuery(document).ready(function() {


	//Accordion & Toggle
	jQuery('.dt-sc-toggle').toggle(function(){ jQuery(this).addClass('active'); },function(){ jQuery(this).removeClass('active'); });
	jQuery('.dt-sc-toggle').click(function(){ jQuery(this).next('.dt-sc-toggle-content').slideToggle(); });
	
	jQuery('.dt-sc-toggle-frame-set').each(function(){
		var $this = jQuery(this),
		    $toggle = $this.find('.dt-sc-toggle-accordion');
			
			$toggle.click(function(){
				if( jQuery(this).next().is(':hidden') ) {
					$this.find('.dt-sc-toggle-accordion').removeClass('active').next().slideUp();
					jQuery(this).toggleClass('active').next().slideDown();
				}
				return false;
			});
			
			//Activate First Item always
			$this.find('.dt-sc-toggle-accordion:first').addClass("active");
			$this.find('.dt-sc-toggle-accordion:first').next().slideDown();
  	});//Accordion & Toggle

	//Tooltip
	 if(jQuery(".dt-sc-tooltip-bottom").length){
		 jQuery(".dt-sc-tooltip-bottom").each(function(){	jQuery(this).tipTip({maxWidth: "auto"}); });
	 }
	 
	 if(jQuery(".dt-sc-tooltip-top").length){
		 jQuery(".dt-sc-tooltip-top").each(function(){ jQuery(this).tipTip({maxWidth: "auto",defaultPosition: "top"}); });
	 }
	 
	 if(jQuery(".dt-sc-tooltip-left").length){
		 jQuery(".dt-sc-tooltip-left").each(function(){ jQuery(this).tipTip({maxWidth: "auto",defaultPosition: "left"}); });
	 }
	 
	 if(jQuery(".dt-sc-tooltip-right").length){
		 jQuery(".dt-sc-tooltip-right").each(function(){ jQuery(this).tipTip({maxWidth: "auto",defaultPosition: "right"}); });
	 }//Tooltip End	

	
	//Plan a visit options switch
	jQuery('#dtplanavisit').click(function () {
		if(jQuery('#dtplanavisit').attr('checked')) {
			jQuery('#dtdatetimepicker').slideDown();
		} else {
			jQuery('#dtdatetimepicker').slideUp();
		}
	});
	
	//Date Time Picker
	jQuery('#dtdatetimepicker').datetimepicker({
		dateFormat : 'dd/mm/yy',
		timeFormat : 'hh:mm TT',
		minDate : 0
	});
	
	//SUBSCRIPTION BOX VALIDATION & MAIL SENDING....
	jQuery('form[name="frmsubscription"]').submit(function () {
		
		var This = jQuery(this);
		
			var action = jQuery(This).attr('action');

			var data_value = unescape(jQuery(This).serialize());
			jQuery.ajax({
				 type: "POST",
				 url:action,
				 data: data_value,
				 error: function (xhr, status, error) {
					 confirm('The page save failed.');
				   },
				  success: function (response) {
					jQuery('#ajax_subscribe_msg').html(response);
				 }
			});

		return false;
    });

	//LOAD SEARCH RESULT IN AJAX....
	jQuery('form[name="frmcoursesearch"]').submit(function () {
		
		var This = jQuery(this);
		
			var action = jQuery(This).attr('action');

			var data_value = jQuery(This).serialize();
			jQuery.ajax({
				type: "POST",
				url:action,
				data: data_value,
				
				beforeSend: function(){
					jQuery('#dt-sc-ajax-load-image').show();
				},
				error: function (xhr, status, error) {
					jQuery('#ajax_course_content').html('Something went wrong!');
				},
				success: function (response) {
					jQuery('#ajax_course_content').html(response);
					jQuery('#ajax_course_content').show();
				},
				complete: function(){
					jQuery('#dt-sc-ajax-load-image').hide();
				} 
			
			});

		return false;
    });
	
	jQuery( 'body' ).delegate( '#ajax_course_content .pagination a', 'click', function(){	
		
		var curr_page = jQuery(this).text(),
			postperpage = jQuery('#dt-course-search-datas').attr('data-postperpage'),
			costtype = jQuery('#dt-course-search-datas').attr('data-costtype'),
			searchtext = jQuery('#dt-course-search-datas').attr('data-searchtext'),
			coursetype = jQuery('#dt-course-search-datas').attr('data-coursetype'),
			subcoursetype = jQuery('#dt-course-search-datas').attr('data-subcoursetype'),
			webinar = jQuery('#dt-course-search-datas').attr('data-webinar');
			
		if(jQuery(this).hasClass('dt-prev'))
			curr_page = parseInt(jQuery(this).attr('cpage'))-1;
		else if(jQuery(this).hasClass('dt-next'))
			curr_page = parseInt(jQuery(this).attr('cpage'))+1;
			
		if(curr_page == 1) var offset = 0;
		else if(curr_page > 1) var offset = ((curr_page-1)*postperpage);
		
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.framework_base_url + 'courses_search_utils.php',
			data:
			{
				postperpage: postperpage,
				offset: offset,
				curr_page: curr_page,
				costtype: costtype,
				searchtext: searchtext,
				coursetype: coursetype,
				subcoursetype: subcoursetype,
				webinar: webinar,
			},
			beforeSend: function(){
				jQuery('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				jQuery('#ajax_course_content').html('Something went wrong!');
			},
			success: function (response) {
				jQuery('#ajax_course_content').html(response);
			},
			complete: function(){
				jQuery('#dt-sc-ajax-load-image').hide();
			} 
		
		});

		return false;
    });
	
	jQuery( 'body' ).delegate( '#dt-coursetype', 'change', function(){	
			var cat_id = jQuery('select#dt-coursetype').val();
			
			jQuery.ajax({
				type: "POST",
				url: mytheme_urls.ajaxurl,
				data:
				{
					action: 'get_course_subcategories',
					cat_id: cat_id
				},
				success: function (response) {
					jQuery('select#dt-subcoursetype').replaceWith(response);
				},
			
			});

		return false;
	});
	
	//Parallax Sections...
	jQuery('.dt-sc-parallax-section').each(function(){
		jQuery(this).bind('inview', function (event, visible) {
			if(visible == true) {
				jQuery(this).parallax("50%", 0.3);
			} else {
				jQuery(this).css('background-position','');
			}
		});
	});
	
	/* Progress Bar */
	 animateSkillBars();
	 animateSection();
	 jQuery(window).scroll(function(){ 
	 	animateSkillBars();
	 	animateSection();
	 });

	function animateSection() {
		var applyViewPort = ( jQuery("html").hasClass('csstransforms') ) ? ":in-viewport" : "";

		jQuery('.animate'+applyViewPort).each(function(){
			var $this = jQuery(this),
	 	 		$animation = ( $this.data("animation") !== undefined ) ? $this.data("animation") : "slideUp";
	 	 	var	$delay = ( $this.data("delay") !== undefined ) ? $this.data("delay") : 300;

	 	 	if( !$this.hasClass($animation) ){
	 	 		setTimeout(function() { $this.addClass($animation);	},$delay);
	 	 	}
	 	 });
	}
	 
	 function animateSkillBars(){
		 var applyViewPort = ( jQuery("html").hasClass('csstransforms') ) ? ":in-viewport" : "";
		 
		 jQuery('.dt-sc-progress'+applyViewPort).each(function(){
			 var progressBar = jQuery(this),
			 	 progressValue = progressBar.find('.dt-sc-bar').attr('data-value');
				 
				if (!progressBar.hasClass('animated')) {
					 	progressBar.addClass('animated');
						progressBar.find('.dt-sc-bar').animate({
							width: progressValue + "%"
							},600,function(){ 
								progressBar.find('.dt-sc-bar-text').fadeIn(400);
						});
				}
    	 });
  	}/* Progress Bar End */

  //Divider Scroll to top
  jQuery("a.scrollTop").each(function(){
    jQuery(this).click(function(e){
      jQuery("html, body").animate({ scrollTop: 0 }, 600);
      e.preventDefault();
    });
  });//Divider Scroll to top end

  // Tabs Shortcodes
  
  "use strict";
  if(jQuery('ul.dt-sc-tabs').length > 0) {
    jQuery('ul.dt-sc-tabs').jtabs('> .dt-sc-tabs-content');
  }
  
  if(jQuery('ul.dt-sc-tabs-frame').length > 0){
    jQuery('ul.dt-sc-tabs-frame').jtabs('> .dt-sc-tabs-frame-content');
  }
  
  if(jQuery('.dt-sc-tabs-vertical-frame').length > 0){
    
    jQuery('.dt-sc-tabs-vertical-frame').jtabs('> .dt-sc-tabs-vertical-frame-content');
    
    jQuery('.dt-sc-tabs-vertical-frame').each(function(){
      jQuery(this).find("li:first").addClass('first').addClass('current');
      jQuery(this).find("li:last").addClass('last');
    });
    
    jQuery('.dt-sc-tabs-vertical-frame li').click(function(){
      jQuery(this).parent().children().removeClass('current');
      jQuery(this).addClass('current');
    });
    
  }/*Tabs Shortcode Ends*/
  
  	//Donutchart
  	jQuery(".dt-sc-donutchart").each(function(){
		var $this = jQuery(this);
	 	var $bgColor =  ( $this.data("bgcolor") !== undefined ) ? $this.data("bgcolor") : "#5D18D6";
	 	var $fgColor =  ( $this.data("fgcolor") !== undefined ) ? $this.data("fgcolor") : "#000000";
	 	var $size = ( $this.data("size") !== undefined ) ? $this.data("size") : "100";
	 
	 	$this.donutchart({'size': $size, 'fgColor': $fgColor, 'bgColor': $bgColor , 'donutwidth' : 5 });
	 	$this.donutchart('animate');
	});//Donutchart Shortcode Ends   
		  
});


jQuery(window).load(function() {
	
	jQuery( 'form[name="frmcoursesearch"]' ).trigger( "submit" );
	
	if (navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
	 jQuery(".dt-sc-fullwidth-video-section").each(function(){
		jQuery(this).find(".dt-sc-mobile-image-container").show();
		jQuery(this).find(".dt-sc-video").remove();
	 });
	}
	
	//Testimonial Carousel
	if( jQuery('.dt-sc-testimonial-carousel').length ) {
	  jQuery('.dt-sc-testimonial-carousel').each(function(){
		  var pagger = jQuery(this).parents(".dt-sc-testimonial-carousel-wrapper").find("div.carousel-arrows"),
			  next = pagger.find("a.testimonial-next"),
			  prev = pagger.find("a.testimonial-prev") ;
				
		  jQuery(this).carouFredSel({
			  responsive:true,
			  auto:false,
			  width:'100%',
			  height: 'variable',
			  scroll:1,
			  items:{ 
				width:600,
				height: 'variable',
				visible: {min: 1,max: 2} 
			  },
			  prev:prev,
			  next:next
		  });
	  });
	}

	// Clients Carousel
	if( jQuery('.dt-sc-partner-carousel').length) {
		jQuery('.dt-sc-partner-carousel').each(function(){
			  var pagger = jQuery(this).parents(".dt-sc-partner-carousel-wrapper").find("div.carousel-arrows"),
			      next = pagger.find("a.partner-next"),
				  prev = pagger.find("a.partner-prev");

			jQuery(this).carouFredSel({
				  responsive:true,
				  auto:false,
				  width:'100%',
				  height: 'variable',
				  scroll:1,
				  items:{ 
				  	width:220,
				  	height: 'variable',
				  	visible: {min: 1,max: 5} 
				  },
				  prev:prev,
				  next:next
			});

		});
	}// Clients Carousel End	
	
	   /*Course Sensei Carousel*/
	  if(jQuery(".dt-sc-coursesensei-carousel").length) {
		
		jQuery(".dt-sc-coursesensei-carousel").each(function(){
			  
		  var pagger = jQuery(this).parents(".dt-sc-coursesensei-carousel-wrapper").find("div.carousel-arrows"),
			  next = pagger.find("a.course-sensei-next"),
			  prev = pagger.find("a.course-sensei-prev");

			jQuery(this).carouFredSel({
			  responsive: true,
			  auto: false,
			  width: '100%',
			  height: 387,
			  prev: prev,
			  next: next,
			  scroll: 1,				
			  items: {
				width:340,
				height: 'variable',
				visible: {
				  min: 1,
				  max: 3
				}
			  }				
			});	
		
		});
		
	  }

	   /*Course  Carousel*/	
	  if(jQuery(".dt-sc-course-carousel").length) {
		jQuery(".dt-sc-course-carousel").each(function(){
		  var $item = jQuery(this),
			  $column  = $item.attr("data-column");

		  if($column == 1) $item_width = '1170';
		  else if($column == 2) $item_width = '600';
		  else $item_width = '340';
		  
		  var pagger = jQuery(this).parents(".dt-sc-course-carousel-wrapper").find("div.carousel-arrows"),
			  next = pagger.find("a.course-next"),
			  prev = pagger.find("a.course-prev");
		  
		  $item.carouFredSel({
			  responsive: true,
			  auto: false,
			  width: '100%',
			  height: 464,
			  prev: prev,
			  next: next,
			  scroll: 1,				
			  items: {
				width:$item_width,
				height: 464,
				visible: {
				  min: 1,
				  max: 3
				}
			  }				
		  });
		  
		});
	  }
	 
	   /*Staff  Carousel*/	
	  if(jQuery(".dt-sc-staff-carousel").length) {
		jQuery(".dt-sc-staff-carousel").each(function(){
		  var $item = jQuery(this);

		  $item_width = '270';
		  
		  var pagger = jQuery(this).parents(".dt-sc-staff-carousel-wrapper").find("div.carousel-arrows"),
			  next = pagger.find("a.staff-next"),
			  prev = pagger.find("a.staff-prev");
		  
		  $item.carouFredSel({
			  responsive: true,
			  auto: false,
			  width: '100%',
			  height: 'variable',
			  prev: prev,
			  next: next,
			  scroll: 1,				
			  items: {
				width:$item_width,
				height: 'variable',
				visible: {
				  min: 1,
				  max: 4
				}
			  }				
		  });
		  
		});
	  }
	 
	  
	   /*Events  Carousel*/
		if(jQuery(".dt-sc-events-carousel").length) {
			jQuery(".dt-sc-events-carousel").each(function(){
				
			  var pagger = jQuery(this).parents(".dt-sc-events-carousel-wrapper").find("div.carousel-arrows"),
				  next = pagger.find("a.events-next"),
				  prev = pagger.find("a.events-prev");
				
			   jQuery(this).carouFredSel({
				 responsive: true,
				 auto: false,
				 width: '100%',
				 height: 'variable',
				 prev: prev,
				 next: next,
				 scroll: 1,                                
				 items: {
					   width:600,
					   height: 'variable',
					   visible: {
						 min: 1,
						 max: 2
					   }
				 }                                
			   });  
		    });                     
		 }	
	
});


(function ($) {
    "use strict";
	
    $(".dt-sc-counter").each(function () {
        $(this).bind('inview', function (event, visible) {
            var $this = $(this),
			$counter = ($this.data("counter") !== undefined) ? $this.data("counter") : "3000";

            if (visible === true && !$this.hasClass('dc-already-counted')) {
				$this.find('.dt-sc-counter-number').countTo({
					from: 0,
					to: $counter,
					speed: 6000,
					refreshInterval: 100
				});
				$this.addClass('dc-already-counted');
            }
			
        });
    });

})(jQuery);