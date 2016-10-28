jQuery(document).ready(function() {
	
	jQuery(window).load(function() {
		jQuery('.dt-quiz-timer').each(function(){
			if(jQuery(this).hasClass('dt-start')){
				jQuery(this).trigger('activate');
			}
		});
	});
	
	jQuery( 'body' ).delegate( '.dt_print_certificate', 'click', function(event){
		jQuery('.dt-sc-certificate-container').print();
		event.preventDefault();
	});
	
		
	jQuery( 'body' ).delegate( "#dt-start-quiz", 'click', function(){  
	
		var course_id = jQuery('#dt-quiz-attributes').attr('data-course_id'),
			lesson_id = jQuery('#dt-quiz-attributes').attr('data-lesson_id'),
			quiz_id = jQuery('#dt-quiz-attributes').attr('data-quiz_id'),
			user_id = jQuery('#dt-quiz-attributes').attr('data-user_id');
		
		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data:
			{
				action: 'dt_ajax_start_quiz',
				course_id: course_id,
				lesson_id: lesson_id,
				quiz_id: quiz_id,
				user_id: user_id
			},
			beforeSend: function(){
				jQuery('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				jQuery('#dt-quiz-questions-container').html('Something went wrong!');
			},
			success: function (response) {
				jQuery('#dt-quiz-questions-container').html(response);
				jQuery(window).on('beforeunload', function(){
					return object.onRefresh;
				});
						
				jQuery(window).on('unload',function(){
					jQuery('#dt-complete-quiz').trigger('click');
				});
				start_timer();
			},
			complete: function(){
				jQuery('#dt-sc-ajax-load-image').hide();
			} 
		});
		
	});
	
	jQuery( 'body' ).delegate( "#dt-complete-quiz", 'click', function(){  

		jQuery( window ).off( "beforeunload" );
		jQuery( window ).off( "unload" );
		
		var course_id = jQuery('#dt-quiz-attributes').attr('data-course_id'),
			lesson_id = jQuery('#dt-quiz-attributes').attr('data-lesson_id'),
			quiz_id = jQuery('#dt-quiz-attributes').attr('data-quiz_id'),
			user_id = jQuery('#dt-quiz-attributes').attr('data-user_id');

		jQuery.ajax({
			type: "POST",
			url: mytheme_urls.ajaxurl,
			data: jQuery('form[name=frmQuiz]').serialize() + '&action=dt_ajax_validate_quiz&course_id='+course_id+'&lesson_id='+lesson_id+'&quiz_id='+quiz_id+'&user_id='+user_id,
			beforeSend: function(){
				jQuery('#dt-sc-ajax-load-image').show();
			},
			error: function (xhr, status, error) {
				jQuery('#dt-quiz-questions-container').html('Something went wrong!');
			},
			success: function (response) {
				jQuery('#dt-quiz-questions-container').html(response);
			},
			complete: function(){
				jQuery('#dt-sc-ajax-load-image').hide();
			} 
		});

	});
	
	function start_timer() {
		var $quiztime = parseInt(jQuery('.dt-quiz-timer').attr('data-time'));
		var $quiztimer = jQuery('.dt-quiz-timer').find('.dt-timer');
		var $this = jQuery('.dt-quiz-timer');
		
		var $timercolors = {};
		$timercolors['cyan'] = { 'fgcolor' : '#81164e', 'bgcolor' : '#e2d6c1'};
		$timercolors['cyan-yellow'] = { 'fgcolor' : '#56bdc2', 'bgcolor' : '#e2d6c1'};
		$timercolors['dark-pink'] = { 'fgcolor' : '#f1ad26', 'bgcolor' : '#e2d6c1'};
		$timercolors['grayish-blue'] = { 'fgcolor' : '#fb6858', 'bgcolor' : '#e2d6c1'};
		$timercolors['grayish-green'] = { 'fgcolor' : '#e57988', 'bgcolor' : '#e2d6c1'};
		$timercolors['grayish-orange'] = { 'fgcolor' : '#87342e', 'bgcolor' : '#e2d6c1'};
		$timercolors['light-red'] = { 'fgcolor' : '#105268', 'bgcolor' : '#e2d6c1'};
		$timercolors['magenta'] = { 'fgcolor' : '#ca4f6c', 'bgcolor' : '#e2d6c1'};
		$timercolors['orange'] = { 'fgcolor' : '#838c48', 'bgcolor' : '#e2d6c1'};
		$timercolors['pink'] = { 'fgcolor' : '#453827', 'bgcolor' : '#e2d6c1'};
		$timercolors['white-avocado'] = { 'fgcolor' : '#72723e', 'bgcolor' : '#dddddd'};
		$timercolors['white-blue'] = { 'fgcolor' : '#478bca', 'bgcolor' : '#dddddd'};
		$timercolors['white-blueiris'] = { 'fgcolor' : '#595ca1', 'bgcolor' : '#dddddd'};
		$timercolors['white-blueturquoise'] = { 'fgcolor' : '#08bbb7', 'bgcolor' : '#dddddd'};
		$timercolors['white-brown'] = { 'fgcolor' : '#8f5a28', 'bgcolor' : '#dddddd'};
		$timercolors['white-burntsienna'] = { 'fgcolor' : '#d36b5e', 'bgcolor' : '#dddddd'};
		$timercolors['white-chillipepper'] = { 'fgcolor' : '#c10841', 'bgcolor' : '#dddddd'};
		$timercolors['white-eggplant'] = { 'fgcolor' : '#614051', 'bgcolor' : '#dddddd'};
		$timercolors['white-electricblue'] = { 'fgcolor' : '#536878', 'bgcolor' : '#dddddd'};
		$timercolors['white-graasgreen'] = { 'fgcolor' : '#81c77f', 'bgcolor' : '#dddddd'};
		$timercolors['white-gray'] = { 'fgcolor' : '#7d888e', 'bgcolor' : '#dddddd'};
		$timercolors['white-green'] = { 'fgcolor' : '#00a988', 'bgcolor' : '#dddddd'};
		$timercolors['white-lightred'] = { 'fgcolor' : '#d66060', 'bgcolor' : '#dddddd'};
		$timercolors['white-orange'] = { 'fgcolor' : '#f67f45', 'bgcolor' : '#dddddd'};
		$timercolors['white-palebrown'] = { 'fgcolor' : '#e472ae', 'bgcolor' : '#dddddd'};
		$timercolors['white-pink'] = { 'fgcolor' : '#e472ae', 'bgcolor' : '#dddddd'};
		$timercolors['white-radiantorchid'] = { 'fgcolor' : '#af71b0', 'bgcolor' : '#dddddd'};
		$timercolors['white-red'] = { 'fgcolor' : '#ef3a43', 'bgcolor' : '#dddddd'};
		$timercolors['white-skyblue'] = { 'fgcolor' : '#0facce', 'bgcolor' : '#dddddd'};
		$timercolors['white-yellow'] = { 'fgcolor' : '#eec005', 'bgcolor' : '#dddddd'};
		
		var $skin = (mytheme_urls.skin != '') ? mytheme_urls.skin : 'orange';
		
		$quiztimer.timer({
			'timer': $quiztime,
			'width' : 160 ,
			'height' : 160 ,
			'fgColor' : $timercolors[$skin].fgcolor,
			'bgColor' : $timercolors[$skin].bgcolor
		});
		var prevval = '';
		
		$quiztimer.on('change',function(){
			var $countdown= $this.find('.dt-countdown');
			var val = parseInt( $quiztimer.attr('data-timer'));
			
			if(val > 0){
				val--;
				$quiztimer.attr('data-timer',val);
				var $text='';
				if(val > 60){
					$text = Math.floor(val/60) + ':' + ((parseInt(val%60) < 10)?'0'+parseInt(val%60):parseInt(val%60)) + '';
				}else{
					$text = '00:'+ ((val < 10)?'0'+val:val);
				}
				$countdown.html($text);
			}else{
				$countdown.html(object.quizTimeout);
				jQuery('#dt-complete-quiz').trigger('click');
				$quiztimer.off();
			}  
			
		});
	}

});