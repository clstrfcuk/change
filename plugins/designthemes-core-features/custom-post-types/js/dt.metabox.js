var dtMetabox = {
	dtInit : function() {
		dtMetabox.dtLayoutSelect();
		dtMetabox.dtCustomSwitch();
		dtMetabox.dtImageUploader();
		dtMetabox.dtImageHolder();
		dtMetabox.dtAddVideo();
		dtMetabox.dtCourses();
	},

	dtLayoutSelect : function() {
		jQuery(".dt-bpanel-layout-set").each(function() {
			jQuery(this).find("a").click(function(e) {

				var $parent = jQuery(this).parents(".dt-bpanel-layout-set");
				var $input = $parent.next(":input");

				if (jQuery(this).hasClass("selected")) {
					jQuery(this).removeClass("selected");
					$input.val("");
				} else {
					$parent.find("a.selected").removeClass("selected");
					$input.val(jQuery(this).attr("rel"));
					jQuery(this).addClass("selected");
				}

				e.preventDefault();
			});
		});
	},

	dtCustomSwitch : function() {
		jQuery("div.dt-checkbox-switch").each(function() {
			jQuery(this).click(function() {
				
				var $ele = '#' + jQuery(this).attr("data-for");

				jQuery(this).toggleClass('checkbox-switch-off checkbox-switch-on');

				if (jQuery(this).hasClass('checkbox-switch-on')) {
					jQuery($ele).attr("checked", "checked");
				} else {
					jQuery($ele).removeAttr("checked");
				}
			});
		});
	},
	
	dtImageUploader: function(){
		var file_frame = "";
		jQuery(".dt-open-media").live('click',function(e){
			e.preventDefault();
			
			// If the media frame already exists, reopen it.
		    if ( file_frame ) {
		      file_frame.open();
		      return;
		    }
		    
		    file_frame = wp.media.frames.file_frame = wp.media({
		    	multiple: true,
		    	title : "Upload / Select Media",
		    	button :{
		    		text : "Insert Image"
		    	}
		    });
		    
		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {
		    	// We set multiple to false so only get one image from the uploader
		        var attachments = file_frame.state().get('selection').toJSON();
		        var  holder = "";
		        jQuery.each( attachments,function(key,value){
					var full = value.sizes.full.url;
					var name = value.name;
					var thumbnail = "";
										 
					if(jQuery.type(value.sizes.thumbnail) != "undefined") {
					   thumbnail =  value.sizes.thumbnail.url;
					} else {
					   thumbnail =  full;
					}
					 
		        	 holder += "<li>" +
		        	 		"<img src='"+thumbnail+"'  alt=''/>" +
		        	 		"<span class='dt-image-name' >"+name+"</span>" +
		        	 		"<input type='hidden' class='dt-image-name' name='items_name[]' value='"+name+"' />" +
		        	 		"<input type='hidden' name='items[]' value='"+full+"' />" +
		        	 		"<input type='hidden' name='items_thumbnail[]' value='"+thumbnail+"' />" +
		        	 		"<span class='my_delete'></span>" +
		        	 		"</li>";
		        });
		        
		        jQuery("ul.dt-items-holder").append(holder);
		        
		    });
		    
		    // Finally, open the modal
		    file_frame.open();
		});
	},
	
	dtImageHolder: function() {
		
		jQuery('ul.dt-items-holder').sortable({
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			cancel: '.my_delete, input, textarea, label'
		});
		
		jQuery('body').delegate('span.my_delete','click', function(){
			jQuery(this).parent('li').remove();
		});
		
	},
	
	dtAddVideo : function() {
		
		jQuery(".dt-add-video").click(function(e){
			var $video =  "<li>" +
					"<span class='dt-video'></span>" +
					"<input type='text' name='items[]' value='http://vimeo.com/18439821'/>" +
					"<input type='hidden' class='dt-image-name' name='items_name[]' value='video' />" +
					"<input type='hidden' name='items_thumbnail[]' value='http://vimeo.com/18439821' />" +
					"<span class='my_delete'></span>" +
					"</li>";
			jQuery('ul.dt-items-holder').append($video);
			e.preventDefault();
		});
		
	},
	
	dtCourses : function() {
		
		jQuery("a.dt-add-multichoice-answer").click(function(e){
			
			var wrong_ans_cnt = jQuery("div.dt-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)+1;
			jQuery("div.dt-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val(wrong_ans_cnt);
			
			jQuery("div.dt-multichoice-answer-clone").find('#dt-multichoice-correct-answer').val(wrong_ans_cnt);
			
			jQuery("div.dt-multichoice-answer-clone").find('#dt-answer-holder').clone().appendTo( "#dt-multichoice-answers-container" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dt-remove-multichoice-answer','click', function(e){	
		
			jQuery(this).parents('#dt-answer-holder').remove();
			
			var wrong_ans_cnt = jQuery("div.dt-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)-1;
			jQuery("div.dt-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val(wrong_ans_cnt);
			
			var i = 0;
			jQuery('#dt-multichoice-answers-container #dt-multichoice-correct-answer').each(function() {
				jQuery(this).val(i);
				i++;
			});	
			
			e.preventDefault();
			
		});	
		
		jQuery("#dt-multichoice-answers-container").sortable({
			placeholder: 'sortable-placeholder',
			stop: function(event, ui) {
				var i = 0;
				jQuery(this).find('#dt-multichoice-correct-answer').each(function() {
					jQuery(this).val(i);
					i++;
				});	
			}
		});
		
		jQuery("a.dt-add-multicorrect-answer").click(function(e){
			
			var wrong_ans_cnt = jQuery("div.dt-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)+1;
			jQuery("div.dt-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val(wrong_ans_cnt);
			
			jQuery("div.dt-multicorrect-answer-clone").find('#dt-multicorrect-correct-answer').val(wrong_ans_cnt);
			
			jQuery("div.dt-multicorrect-answer-clone").find('#dt-answer-holder').clone().appendTo( "#dt-multicorrect-answers-container" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dt-remove-multicorrect-answer','click', function(e){	
		
			jQuery(this).parents('#dt-answer-holder').remove();
			
			var wrong_ans_cnt = jQuery("div.dt-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)-1;
			jQuery("div.dt-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val(wrong_ans_cnt);
			
			var i = 0;
			jQuery('#dt-multicorrect-answers-container #dt-multicorrect-correct-answer').each(function() {
				jQuery(this).val(i);
				i++;
			});	
			
			e.preventDefault();
			
		});	
		
		jQuery("#dt-multicorrect-answers-container").sortable({
			placeholder: 'sortable-placeholder',
			stop: function(event, ui) {
				var i = 0;
				jQuery('#dt-multicorrect-answers-container #dt-multicorrect-correct-answer').each(function() {
					jQuery(this).val(i);
					i++;
				});	
			}
		});
			
		jQuery('body').delegate('select#dt-question-type','change', function(e){	
			jQuery('.dt-answers').hide();
			jQuery('.dt-' + jQuery(this).val() + '-answers').show();
			e.preventDefault();
		});

		
		jQuery("a.dt-add-questions").click(function(e){
			
			var clone = jQuery("#dt-questions-to-clone").clone();
			
			clone.attr('id', 'dt-question-box').removeClass('hidden');
			clone.find('select').attr('id', 'dt-quiz-question').attr('name', 'dt-quiz-question[]').attr('class', 'dt-new-chosen-select');
			clone.find('input').attr('id', 'dt-quiz-question-grade').attr('name', 'dt-quiz-question-grade[]');
			
			clone.appendTo('#dt-quiz-questions-container');		
			
			jQuery(".dt-new-chosen-select").chosen({
				no_results_text: object.noResult,
			});
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dt-remove-question','click', function(e){	
		
			jQuery(this).parents('#dt-question-box').remove();
			jQuery( "#dt-quiz-question-grade" ).trigger( "change" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('#dt-quiz-question-grade', 'change', function(){
			 
			 var total = parseInt(0);
			 jQuery('#dt-quiz-questions-container #dt-question-box').each(function(){
				var ival = jQuery(this).find('#dt-quiz-question-grade').val();
				if(ival == 'NAN' || ival ==''){
					ival = parseInt(0);
				}
				total = parseInt(total) + parseInt(ival);
			 });
			 
			 jQuery("#dt-total-marks-container").find('span').html(total);
			 jQuery("#dt-total-marks-container").find('input[type="hidden"]').val(total);
			 
		});
		 
		jQuery("#dt-quiz-questions-container").sortable({ placeholder: 'sortable-placeholder' });
		
		jQuery("div.answer-switch").each(function(){
		  jQuery(this).click(function(){
			  var $ele = '#'+jQuery(this).attr("data-for");
			  var $quesid = jQuery(this).attr("data-quesid");
			  var $grade = jQuery(this).attr("data-grade");
			  var $marksobtained = jQuery('input#dt-marks-obtained').val();
			  var $totalmarks = jQuery('input#dt-total-marks').val();
			  var $marksobtained_percent = 0;
			  
			  jQuery(this).toggleClass('answer-switch-off answer-switch-on');
			  if(jQuery(this).hasClass('answer-switch-on')){
				  jQuery('tr#dt-row-'+$quesid+' #dt-grade-html').html($grade + ' / ' + $grade);
				  $marksobtained = parseInt($marksobtained)+parseInt($grade);
				  $marksobtained_percent = +(($marksobtained/$totalmarks)*100).toFixed(2);
				  
				  jQuery('input#dt-marks-obtained').val($marksobtained);
				  jQuery('input#dt-marks-obtained-percent').val($marksobtained_percent);
				  jQuery('#dt-marks-obtained-html').html('<label>'+$marksobtained+' ('+$marksobtained_percent+'%) </label>');
				  
				  jQuery(this).html('Right');
				  jQuery($ele).attr("checked","checked");
			  }else{
				  jQuery('tr#dt-row-'+$quesid+' #dt-grade-html').html('0 / ' + $grade);
				  $marksobtained = parseInt($marksobtained)-parseInt($grade);
				  $marksobtained_percent = +(($marksobtained/$totalmarks)*100).toFixed(2);
				  
				  jQuery('input#dt-marks-obtained').val($marksobtained);
				  jQuery('input#dt-marks-obtained-percent').val($marksobtained_percent);
				  jQuery('#dt-marks-obtained-html').html('<label>'+$marksobtained+' ('+$marksobtained_percent+'%) </label>');
	
				  jQuery(this).html('Wrong');
				  jQuery($ele).removeAttr("checked");
			  }
			  
		  });
		});
		
		jQuery("a#dt-reset-grade").click(function(e){
			
			if(confirm(objectL10n.resetGrade)){
				
				jQuery('#dt-marks-obtained-html').html('<label>0 (0%)</label>');
				jQuery('#dt-marks-obtained').val(0);
				jQuery('#dt-marks-obtained-percent').val(0);
				
				jQuery('#dt-grading-table td#dt-grade-html').each(function(){
					var grade = parseInt(jQuery(this).attr('data-grade'));
					jQuery(this).html('0 / ' + grade);
				});
				
				jQuery('#dt-grading-table td#dt-grade-field').each(function(){
					jQuery(this).find('.answer-switch').removeAttr('class').addClass('answer-switch answer-switch-off').html('Wrong');
					jQuery(this).find('input').removeAttr('checked').val(false);
				});
				
			}
			
			e.preventDefault();
			
		});
		
		jQuery("a#dt-auto-grade").click(function(e){
			
			var total_grade = 0, marks_obtained = 0;
			
			jQuery('#dt-grading-table tr:not(#dt-first-row)').each(function(){
				
				var correct_answer = jQuery(this).find('td#dt-correct-answer').html();
				var user_answer = jQuery(this).find('td#dt-user-answer').html();
				var grade = parseInt(jQuery(this).find('td#dt-grade-html').attr('data-grade'));
				
				total_grade = parseInt(total_grade)+grade;
				
				if(correct_answer.toLowerCase().replace(new RegExp(/\r?\n|\r|<br>| /g),"") == user_answer.toLowerCase().replace(new RegExp(/\r?\n|\r|<br>| /g),"")) {
					marks_obtained = parseInt(marks_obtained)+grade;
					jQuery(this).find('td#dt-grade-html').html(grade + ' / ' + grade);
					jQuery(this).find('td#dt-grade-field .answer-switch').removeAttr('class').addClass('answer-switch answer-switch-on').html('Right');
					jQuery(this).find('td#dt-grade-field input').attr('checked','checked').val(true);
				} else {
					jQuery(this).find('td#dt-grade-html').html('0 / ' + grade);
					jQuery(this).find('td#dt-grade-field .answer-switch').removeAttr('class').addClass('answer-switch answer-switch-off').html('Wrong');
					jQuery(this).find('td#dt-grade-field input').removeAttr('checked').val(false);
				}
				
			});
			
			var pass_percentage = parseInt(jQuery('#dt-pass-percentage').val());
			var marks_obtained_percent = +((marks_obtained/total_grade)*100).toFixed(2);
			
			jQuery('input#dt-marks-obtained').val(marks_obtained);
			jQuery('input#dt-marks-obtained-percent').val(marks_obtained_percent);
			jQuery('#dt-marks-obtained-html').html('<label>'+marks_obtained+' ('+marks_obtained_percent+'%) </label>');
			
			e.preventDefault();
			
		});
		
		jQuery( 'body' ).delegate( '#dt-setcom-teacher', 'change', function(){	
		
			var teacher_id = jQuery(this).val();
			if(teacher_id != '') {
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data:
					{
						action: 'dt_set_commission',
						teacher_id: teacher_id
					},
					success: function (response) {
						jQuery('#setcommission-container').html(response);
					},
				
				});
			} else {
				jQuery('#setcommission-container').html(jQuery('#teacher-alert').val());
			}
			
		});
		
		jQuery( 'body' ).delegate( '#dt-paycom-teacher', 'change', function(){
				
			var teacher_id = jQuery(this).val();
			if(teacher_id != '') {
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data:
					{
						action: 'dt_pay_commission',
						teacher_id: teacher_id
					},
					success: function (response) {
						jQuery('#paycommission-container').html(response);
						
					},
				
				});
			} else {
				jQuery('#paycommission-container').html(jQuery('#teacher-alert').val());
			}
						
		});
	
		jQuery( 'body' ).delegate( "div.dt-paycom-checkbox-switch", 'click', function(){  
			var $ele = '#'+jQuery(this).attr("data-for");
			var item_id = $ele.replace('#item-','');
			jQuery(this).toggleClass('checkbox-switch-off checkbox-switch-on');
			if(jQuery(this).hasClass('checkbox-switch-on')){
				jQuery($ele).attr("checked","checked");
				var topay = +jQuery('#hid-topay-'+item_id).val();
				var total = +jQuery('#total-amount').val();
				total = (total+topay).toFixed(1);
				jQuery('#total-amount').val(total);
			}else{
				jQuery($ele).removeAttr("checked");
				 var topay = +jQuery('#hid-topay-'+item_id).val();
				 var total = +jQuery('#total-amount').val();
				 total = (total-topay).toFixed(1);
				 jQuery('#total-amount').val(total);
			}
		});
		
		jQuery(".dt-chosen-select").chosen({
			no_results_text: object.noResult,
		});
		
		jQuery('body').delegate('#dt-marks-obtained', 'change', function(){
			 
			 var user_mark = jQuery(this).val();
			 var max_mark = jQuery('#dt-assignment-maximum-mark').val();
			 
			 var percentage = (parseInt(user_mark)/parseInt(max_mark))*100;
			 if(isNaN(percentage)) percentage = 0;
			 percentage = +percentage.toFixed(2);
			 
			 jQuery('#dt-marks-obtained-percentage-html').html('<label>'+percentage+'%</label>');
			 jQuery('#dt-marks-obtained-percent').val(percentage);
			 
		});
		
		jQuery( 'body' ).delegate( "#dt-statistics-courses-container .pagination a", 'click', function(){  
			
			var curr_page = jQuery(this).text();	
			if(jQuery(this).hasClass('dt-prev'))
				curr_page = parseInt(jQuery(this).attr('cpage'))-1;
			else if(jQuery(this).hasClass('dt-next'))
				curr_page = parseInt(jQuery(this).attr('cpage'))+1;
			else			
				curr_page = parseInt(curr_page);
				
				
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data:
				{
					action: 'dt_statistics_courses_pagination',
					curr_page: curr_page
				},
				beforeSend: function(){
					jQuery('#dt-sc-ajax-load-image').show();
				},
				error: function (xhr, status, error) {
					jQuery('#dt-statistics-courses-container').html('Something went wrong!');
				},
				success: function (response) {
					jQuery('#dt-statistics-courses-container').html(response);
				},
				complete: function(){
					jQuery('#dt-sc-ajax-load-image').hide();
				} 
			});
				
			
		});
		
		jQuery( 'body' ).delegate( "#dt-statistics-students-container .pagination a", 'click', function(){  
			
			var curr_page = jQuery(this).text();	
			if(jQuery(this).hasClass('dt-prev'))
				curr_page = parseInt(jQuery(this).attr('cpage'))-1;
			else if(jQuery(this).hasClass('dt-next'))
				curr_page = parseInt(jQuery(this).attr('cpage'))+1;
			else			
				curr_page = parseInt(curr_page);
				
				
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data:
				{
					action: 'dt_statistics_students_pagination',
					curr_page: curr_page
				},
				beforeSend: function(){
					jQuery('#dt-sc-ajax-load-image').show();
				},
				error: function (xhr, status, error) {
					jQuery('#dt-statistics-students-container').html('Something went wrong!');
				},
				success: function (response) {
					jQuery('#dt-statistics-students-container').html(response);
				},
				complete: function(){
					jQuery('#dt-sc-ajax-load-image').hide();
				} 
			});
				
			
		});
		
		jQuery( 'body' ).delegate( "#dt-statistics-teachers-container .pagination a", 'click', function(){  
			
			var curr_page = jQuery(this).text();	
			if(jQuery(this).hasClass('dt-prev'))
				curr_page = parseInt(jQuery(this).attr('cpage'))-1;
			else if(jQuery(this).hasClass('dt-next'))
				curr_page = parseInt(jQuery(this).attr('cpage'))+1;
			else			
				curr_page = parseInt(curr_page);
				
				
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data:
				{
					action: 'dt_statistics_teachers_pagination',
					curr_page: curr_page
				},
				beforeSend: function(){
					jQuery('#dt-sc-ajax-load-image').show();
				},
				error: function (xhr, status, error) {
					jQuery('#dt-statistics-teachers-container').html('Something went wrong!');
				},
				success: function (response) {
					jQuery('#dt-statistics-teachers-container').html(response);
				},
				complete: function(){
					jQuery('#dt-sc-ajax-load-image').hide();
				} 
			});
			
		});
		
		jQuery('.dt-graph-option').hide();
		jQuery('.dt-course-graph').show();
		
		jQuery('body').delegate('#dt-graph-type', 'change', function(){
			
			jQuery('#dt-include-zero-course, #dt-include-zero-teacher, #dt-include-zero-student').removeAttr('checked');
			
			jQuery('.dt-graph-option').hide();
			if(jQuery(this).val() == 'course') jQuery('.dt-course-graph').show();
			else if(jQuery(this).val() == 'teacher') jQuery('.dt-teacher-graph').show();
			else if(jQuery(this).val() == 'student') jQuery('.dt-student-graph').show();
			jQuery( "#dt-statistics-graph-options #dt-graph-generate" ).trigger( "click" );
			
		});
		
		jQuery( 'body' ).delegate( "#dt-statistics-graph-options #dt-graph-generate", 'click', function(){  
		
			if(jQuery('#dt-include-zero-course').attr('checked') == 'checked' || jQuery('#dt-include-zero-teacher').attr('checked') == 'checked' || jQuery('#dt-include-zero-student').attr('checked') == 'checked') var include_zero_sales = 1;
			else var include_zero_sales = 0;
			
			var	graph_type = jQuery('#dt-graph-type').val();

			if(graph_type == 'course') var selectedItems = jQuery('#dt-select-course').val();
			else if(graph_type == 'teacher') var selectedItems = jQuery('#dt-select-teacher').val();
			else if(graph_type == 'student') var selectedItems = jQuery('#dt-select-student').val();
			else  var selectedItems = '';
			
			
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data:
				{
					action: 'dt_statistics_statistics_graph_ajax',
					include_zero_sales: include_zero_sales,
					selectedItems: selectedItems,
					graph_type: graph_type
				},
				beforeSend: function(){
					jQuery('#dt-sc-ajax-load-image').show();
				},
				error: function (xhr, status, error) {
					jQuery('#dt-statistics-graph-container').html('Something went wrong!');
				},
				success: function (response) {
					
					if(response == 'NoData') {
						jQuery('#dt-statistics-graph-container').html(object.noGraph);
					} else {
						
						jQuery('#dt-statistics-graph-container').html(response);
						var dtChart = document.getElementById("dt-chart").getContext("2d");
						window.dtBar = new Chart(dtChart).Bar(dtChartData, {
								responsive : true,
							});
						
					}
						
				},
				complete: function(){
					jQuery('#dt-sc-ajax-load-image').hide();
				} 
			});
			
		});
		
		jQuery("a.dt-add-attachments").click(function(e){
			
			var clone = jQuery("#dt-attachments-clone").clone();
			
			clone.attr('id', 'dt-attachments-holder').attr('class', 'file-upload-container').find('input[type="text"]').attr('name', 'media-attachments[]');
			clone.appendTo('#dt-attachments-container');		
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dt-remove-attacment','click', function(e){	
		
			jQuery(this).parents('#dt-attachments-holder').remove();
			e.preventDefault();
			
		});	
		
		jQuery("#dt-attachments-container").sortable({ placeholder: 'sortable-placeholder' });
		
	}
	
};

jQuery(document).ready(function() {

	dtMetabox.dtInit();
	
});