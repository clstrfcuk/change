/*
Document   :  Dashboard
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspDashboard = (function ($) {
    "use strict";

    // public
    var debug = false;
    var maincontainer = null;

	// init function, autoload
	function init()
	{
		maincontainer = $("#psp-ajax-response");
		triggers();
		fix_frame_preview();
	};
	
	function fix_frame_preview()
	{
		var preview = maincontainer.find(".psp-website-preview .browser-preview");
		maincontainer.find(".psp-website-preview .the-website-preview").load(function(){
			maincontainer.find(".psp-borwser-frame").height( preview.height() );
		});
	}
	
	function loadAudience()
	{
		var graph = $("#psp-audience-visits-graph"); 
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGoogleAPIRequest',
			'sub_action' 	: 'getAudience',
			'from_date'		: graph.data('fromdate'),
			'to_date'		: graph.data('todate'),
			'debug'			: debug
		}, function(response) {
			
			if( typeof response.getAudience !== 'undefined' ){
				var data = response.getAudience.data.rows; 
				var opts = {
					series: {
						lines: { show: true },
						points: { show: true }
					},
					tooltip: true,
					tooltipOpts: {
						defaultTheme: true,
						content: "%x<br />%s: %y",
						xDateFormat: "%d/%m/%y"
					},
					xaxis: {
						mode: "time",
						timeformat: "%d/%m/%y"
					},
					grid: {
						hoverable: true,
						clickable: true,
						borderWidth: null
					}
				};
		
				var datasets = [
					{ data: data.newVisits, label: "% New Visits", color: "#E15656" },
					{ data: data.visits, label: "Visits", color: "#61A5E4" },
					{ data: data.avgTimeOnPage, label: "Avg. Visit Duration", color: "#37aa37" },
					{ data: data.visitBounceRate, label: "Bounce Rate", color: "#A6D037" },
					{ data: data.pageviews, label: "Pageviews", color: "#ad6dd6"},
					{ data: data.uniquePageviews, label: "Unique Visitors", color: "#a91c83" }
				];
				
				var plot = $.plot(graph, datasets, opts);
				
				// remove the loading
				graph.css('background-image', 'none');
			}else{
				graph.parents('.psp-panel-widget').eq(0).remove();
			}
			
		}, 'json');
		
	}
	
	function tooltip()
	{
		var xOffset = -30,
			yOffset = -300,
			winW 	= $(window).width();
		
		$(".psp-aa-products-container ul li a").hover(function(e){
			
			var that = $(this),
				preview = that.data('preview');

			$("body").append("<p id='psp-aa-preview'>"+ ( '<img src="' + ( preview ) + '" >' ) +"</p>");
			
			var new_left = e.pageX + yOffset;
			
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			$("#psp-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px")
				.fadeIn("fast");
	    },
		function(){
			this.title = this.t;
			$("#psp-aa-preview").remove();
	    });
		
	
		$(".psp-aa-products-container ul li a").mousemove(function(e){
			
			var new_left = e.pageX + yOffset;
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			
			$("#psp-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px");
		});
	}
	
	function boxLoadAjaxContent( box )
	{
		var allAjaxActions = [];
		box.find('.is_ajax_content').each(function(key, value){
			
			var alias = $(value).text().replace( /\n/g, '').replace("{", "").replace("}", "");
			$(value).attr('id', 'psp-row-alias-' + alias);
			allAjaxActions.push( alias );
		}); 
  
		jQuery.post(ajaxurl, {
			'action' 		: 'pspDashboardRequest',
			'sub_actions'	: allAjaxActions.join(","),
			'debug'			: debug
		}, function(response) {
			
			$.each(response, function(key, value){
				if( value.status == 'valid' ){
					var row = box.find( "#psp-row-alias-" + key );
					row.html(value.html);
					
					row.removeClass('is_ajax_content');
					
					tooltip();
				} 
			});
			
		}, 'json');
	}
	
	function triggers()
	{
		maincontainer.find(">div").each( function(e){
			var that = $(this);
			
			// check if box has ajax content
			if( that.find('.is_ajax_content').size() > 0 ){
				boxLoadAjaxContent(that);
			}
		});
		
		$("#psp-audience-visits-graph").each(function(){
			loadAudience();
		});
		
		$(".psp-aa-products-tabs").on('click', "li:not(.on) a", function(e){
			e.preventDefault();
			
			var that = $(this),
				alias = that.attr('class').split("items-"),
				alias = alias[1];
			
			$('.psp-aa-products-container').hide();
			$("#aa-prod-" + alias).show();
			
			$(".psp-aa-products-tabs").find("li.on").removeClass('on');
			that.parent('li').addClass('on');
		});
	}

	// external usage
	return {
		"init": init
    }
})(jQuery);
