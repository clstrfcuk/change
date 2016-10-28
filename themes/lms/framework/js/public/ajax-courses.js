jQuery(document).ready(function($){
	  
	$( 'body' ).delegate( '#courses-type', 'change', function(){	
			
		$('a.course-price').removeClass('active');
		$('a.course-price').first().addClass('active');
			
		var postid = $(this).attr('data-postid'),
			view_type = $('#dt-course-datas').attr('data-view_type'),
			price_type = '',
			courses_type = $(this).val(),
			offset = 0,
			curr_page = 1;

		loadCourses(postid, view_type, price_type, courses_type, offset, curr_page);

		return false;
		
	});
  
	$( 'body' ).delegate( '.course-price', 'click', function(){	
			
		$('#courses-type').val('all');	
		$('a.course-price').removeClass('active');
		$(this).addClass('active');
		
		var postid = $(this).attr('data-postid'),
			view_type = $('#dt-course-datas').attr('data-view_type'),
			price_type = $(this).attr('data-price_type'),
			courses_type = '',
			offset = 0,
			curr_page = 1;

		loadCourses(postid, view_type, price_type, courses_type, offset, curr_page);
			
		return false;
		
	});
	
	
	$( 'body' ).delegate( '.course-layout', 'click', function(){	
			
		$('a.course-layout').removeClass('active');
		$(this).addClass('active');
		
		var postid = $(this).attr('data-postid'),
			view_type = $(this).attr('data-view_type'),
			price_type = $('#dt-course-datas').attr('data-price_type'),
			courses_type = $('#dt-course-datas').attr('data-courses_type'),
			offset = $('#dt-course-datas').attr('data-offset'),
			curr_page = $('#dt-course-datas').attr('data-curr_page');

		loadCourses(postid, view_type, price_type, courses_type, offset, curr_page);
			
		return false;
		
	});

	$( 'body' ).delegate( '#ajax_tpl_course_content .pagination a', 'click', function(){	
			
		var postid = $('#dt-course-datas').attr('data-postid'),
			view_type = $('#dt-course-datas').attr('data-view_type'),
			price_type = $('#dt-course-datas').attr('data-price_type'),
			courses_type = $('#dt-course-datas').attr('data-courses_type'),
			postperpage = $('#dt-course-datas').attr('data-postperpage'),
			curr_page = $(this).text();
			
		if($(this).hasClass('dt-prev'))
			curr_page = parseInt($(this).attr('cpage'))-1;
		else if($(this).hasClass('dt-next'))
			curr_page = parseInt($(this).attr('cpage'))+1;
			
		if(curr_page == 1) var offset = 0;
		else if(curr_page > 1) var offset = ((curr_page-1)*postperpage);

		loadCourses(postid, view_type, price_type, courses_type, offset, curr_page);
			
		return false;
			
	});
	
	
	function loadCourses(postid, view_type, price_type, courses_type, offset, curr_page) {
	
		if (jQuery('body').hasClass('post-type-archive-dt_courses')) {
			var course_page_type = 'archive';
		} else if (jQuery('body').hasClass('tax-course_category')) {
			var course_page_type = 'tax-archive';
		} else if (jQuery('body').hasClass('page-template page-template-tpl-courses-php')) {
			var course_page_type = 'template';
		}
		
		$.ajax({
			type: "POST",
			url: mytheme_urls.url + '/wp-content/themes/' + mytheme_urls.themeName + '/framework/courses_utils.php',
			data:
			{
				post_id: postid,
				view_type: view_type,
				price_type: price_type,
				courses_type: courses_type,
				offset: offset,
				curr_page: curr_page,
				course_page_type: course_page_type,
				lang: mytheme_urls.lang
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#ajax_tpl_course_content').html('Something went wrong!');
			},
			success: function (response) {
				$('#ajax_tpl_course_content').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});
	
	}
	
	
	// Dashboard 
	
	$( 'body' ).delegate( '#dt-sc-dashboard-user-courses .pagination a', 'click', function(){
			
		var curr_page = $(this).text();	
		if($(this).hasClass('dt-prev'))
			curr_page = parseInt($(this).attr('cpage'))-1;
		else if($(this).hasClass('dt-next'))
			curr_page = parseInt($(this).attr('cpage'))+1;
		else			
			curr_page = parseInt(curr_page);
			
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data:
			{
				action: 'dt_dashboard_user_courses',
				curr_page: curr_page
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#dt-sc-dashboard-user-courses').html('Something went wrong!');
			},
			success: function (response) {
				$('#dt-sc-dashboard-user-courses').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});

		return false;
		
	});
	
	$( 'body' ).delegate( '#dt-sc-dashboard-teacher-courses .pagination a', 'click', function(){
			
		var curr_page = $(this).text();	
		if($(this).hasClass('dt-prev'))
			curr_page = parseInt($(this).attr('cpage'))-1;
		else if($(this).hasClass('dt-next'))
			curr_page = parseInt($(this).attr('cpage'))+1;
		else			
			curr_page = parseInt(curr_page);
			
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data:
			{
				action: 'dt_dashboard_teacher_courses',
				curr_page: curr_page
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#dt-sc-dashboard-teacher-courses').html('Something went wrong!');
			},
			success: function (response) {
				$('#dt-sc-dashboard-teacher-courses').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});

		return false;
		
	});
	
	$( 'body' ).delegate( '#dt-sc-dashboard-user-assignments .pagination a', 'click', function(){
			
		var curr_page = $(this).text();	
		if($(this).hasClass('dt-prev'))
			curr_page = parseInt($(this).attr('cpage'))-1;
		else if($(this).hasClass('dt-next'))
			curr_page = parseInt($(this).attr('cpage'))+1;
		else			
			curr_page = parseInt(curr_page);
			
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data:
			{
				action: 'dt_dashboard_user_assignments',
				curr_page: curr_page
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#dt-sc-dashboard-user-assignments').html('Something went wrong!');
			},
			success: function (response) {
				$('#dt-sc-dashboard-user-assignments').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});

		return false;
		
	});
	
	$( 'body' ).delegate( '#dt-sc-dashboard-teacher-assignments .pagination a', 'click', function(){
			
		var curr_page = $(this).text();	
		if($(this).hasClass('dt-prev'))
			curr_page = parseInt($(this).attr('cpage'))-1;
		else if($(this).hasClass('dt-next'))
			curr_page = parseInt($(this).attr('cpage'))+1;
		else			
			curr_page = parseInt(curr_page);
			
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data:
			{
				action: 'dt_dashboard_teacher_assignments',
				curr_page: curr_page
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#dt-sc-dashboard-teacher-assignments').html('Something went wrong!');
			},
			success: function (response) {
				$('#dt-sc-dashboard-teacher-assignments').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});

		return false;
		
	});
	
	
	// Payments
	
	$( 'body' ).delegate( '.ajax-payment', 'click', function(){	
		
		var paymenttype = $(this).attr('data-paymenttype'),
			level = $(this).attr('data-level'),
			description = $(this).attr('data-description'),
			currency = $(this).attr('data-currency'),
			price = $(this).attr('data-price'),
			period = $(this).attr('data-period'),
			term = $(this).attr('data-term'),
			cbproductno = $(this).attr('data-cbproductno'),
			cbskin = $(this).attr('data-cbskin'),
			cbflowid = $(this).attr('data-cbflowid');
			
		$.ajax({
			type: "POST",
			url: mytheme_urls.url + '/wp-content/themes/' + mytheme_urls.themeName + '/framework/payment_utils.php',
			data:
			{
				paymenttype: paymenttype,
				level: level,
				description: description,
				currency: currency,
				price: price,
				period: period,
				term: term,
				cbproductno: cbproductno,
				cbskin: cbskin,
				cbflowid: cbflowid
			},
			beforeSend: function(){
				$('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				$('#payment-ajax-content').html('Something went wrong!');
			},
			success: function (response) {
				$('#payment-ajax-content').html(response);
			},
			complete: function(){
				$('#dt-sc-ajax-load-image').hide();
			} 
		});
			
		return false;
			
	});

  
});