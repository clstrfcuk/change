<?php get_header(); ?>

<!-- ** Primary Section ** -->
<section id="primary" class="content-full-width">

    <?php 
    
	if(is_user_logged_in()) {
		
		if( have_posts() ): while( have_posts() ): the_post();
		$post_id = get_the_ID(); 
		$dt_assignment_course = get_post_meta ($post_id, "dt-assignment-course",true);
		
		if(isset($dt_assignment_course) && $dt_assignment_course != '') {
		
		?>
		
		<article id="post-<?php the_ID(); ?>" <?php post_class('dt-sc-assignment-single'); ?>>
			
			<?php
			$assignment_subtitle = dttheme_wp_kses(get_post_meta ( $post_id, "assignment-subtitle",true));
			$assignment_maximum_mark = get_post_meta ( $post_id, "assignment-maximum-mark",true);
			$assignment_enable_textarea = get_post_meta ( $post_id, "assignment-enable-textarea",true);
			$assignment_enable_attachment = get_post_meta ( $post_id, "assignment-enable-attachment",true);
			?>
				
            <h2><?php the_title(); ?></h2>
            <h5><?php echo $assignment_subtitle; ?></h5>
            <?php the_content(); ?>
            
            <?php
			
			if(isset( $_POST['dt_submit_assignment']) && wp_verify_nonce($_POST['dt_submit_assignment_noonce'], 'dt_submit_assignment_noonce')) {
				
				echo '<div class="dt-sc-hr-invisible-small"></div>';
				
				if(dt_submit_assignemnt()) {
					echo '<div class="dt-sc-info-box">'.__('Your assignment submitted successfully!', 'dt_themes').'</div>';
				}
				
				echo '<a class="dt-sc-button small filled dt-sc-resubmit-assignment" href="'.get_permalink($post_id).'">'.__('Resubmit Assignment','dt_themes').'</a>';
			
			} else {
				
				$user_id = get_current_user_id();
			
				$dt_gradings = array(
								'post_type'=>'dt_gradings',
								'meta_query'=>array()
							);
				
				$dt_gradings['meta_query'][] = array(
													'key'     => 'dt-user-id',
													'value'   => $user_id,
													'compare' => '=',
													'type'    => 'numeric'
												);
			
				$dt_gradings['meta_query'][] = array(
													'key'     => 'dt-assignment-id',
													'value'   => get_the_ID(),
													'compare' => '=',
													'type'    => 'numeric'
												);
												
				$dt_grade_post = get_posts( $dt_gradings );

				if(isset($dt_grade_post)) {
					$grade_post_id = isset($dt_grade_post[0]) ? $dt_grade_post[0]->ID : 0;
					$grade = get_post_meta ( $grade_post_id, "graded",true);
				} else {
					$grade = '';	
				}
				
				if($grade == '') {
					
					echo '<div class="dt-sc-hr-invisible"></div>';
					
					echo '<div class="dt-sc-assignment-upload">';
					
						echo '<h4 class="border-title">'.__('Upload Assignment : ', 'dt_themes').'<span> </span></h4>';
						
						$assignment_attachment_type = get_post_meta ( $post_id, "assignment-attachment-type",true);
						if(isset($assignment_attachment_type) && $assignment_attachment_type != '') {
							echo '<div class="dt-sc-assignment-file-types">';
								echo '<h6>'.__('Allowed File Types : ', 'dt_themes').'</h6>';
								echo '<ul class="assignment-file-types">';
								foreach($assignment_attachment_type as $assignment) {
									echo '<li><span>.</span>'.$assignment.'</li>';	
								}
								echo '</ul>';
							echo '</div>';
						}
						$assignment_attachment_size = get_post_meta ( $post_id, "assignment-attachment-size",true);
						if(isset($assignment_attachment_size) && $assignment_attachment_size != '') {
							echo '<div class="dt-sc-assignment-file-size">';
								echo '<h6>'.__('Maximum File Upload Size : ', 'dt_themes').'<span>'.$assignment_attachment_size.__('MB', 'dt_themes').'</span></h6>';
							echo '</div>';
						}
						
						echo '<form method="post" class="frmAssignment" name="frmAssignment" action="'.get_permalink($post_id).'" enctype="multipart/form-data">';
						
							if(isset($assignment_enable_textarea) && $assignment_enable_textarea != '') {
								echo '<h6>'.__('Notes :', 'dt_themes').'</h6>';
								echo '<textarea id="dt-assignemnt-textarea" name="dt-assignemnt-textarea"></textarea>';
							}
						
							if(isset($assignment_enable_attachment) && $assignment_enable_attachment != '') {
								echo '<div class="upload-assignment">';
									echo '<h6>'.__('Upload Assignment :', 'dt_themes').'</h6>';
									echo '<input id="dt-assignemnt-attachment" name="dt-assignemnt-attachment" type="file">';
								echo '</div>';
							}
							
							echo '<input type="hidden" name="dt_submit_assignment_noonce" id="dt_submit_assignment_noonce" value="'.wp_create_nonce('dt_submit_assignment_noonce').'" />';
							echo '<input type="submit" name="dt_submit_assignment" class="dt_submit_assignment" id="dt_submit_assignment" value="'.__('Submit Assignment','dt_themes').'" />';
						
						echo '</form>';
					
					echo '</div>';
					
				}
				
				
				if(isset($dt_grade_post)) {
					
					echo '<div class="dt-sc-hr-invisible"></div>';
				
					echo '<h4 class="border-title">'.__('Your Submission', 'dt_themes').'<span> </span></h4>';
					
					echo '<div class="dt-sc-clear"></div>';
					
					if($grade != '') {
						
						$marks_obtained = get_post_meta ( $grade_post_id, "marks-obtained", true); 
						$assignment_maximum_mark = get_post_meta ( $post_id, "assignment-maximum-mark", true); 
						echo '<h6 class="dt-sc-assignment-score">'.sprintf( __('You have scored %1$s out of %2$s', 'dt_themes'), $marks_obtained, $assignment_maximum_mark ).'</h6>';
						
					}
					
					echo '<div class="dt-sc-clear"></div>';
					
					$dt_assignment_notes = get_post_meta ( $grade_post_id, "dt-assignment-notes", true); 
					$dt_attachment_id = get_post_meta ( $grade_post_id, "dt-attachment-id", true);
					$dt_attachment_name = get_post_meta ( $grade_post_id, "dt-attachment-name", true);
						
						
				   echo '<ul class="dt-sc-assignment-submission">';
				   		
						echo '<li>';
				   		
							echo '<div class="column dt-sc-one-fifth first">';
								echo __('Notes', 'dt_themes');
							echo '</div>';
							echo '<div class="column dt-sc-four-fifth">';
								if(isset($dt_assignment_notes) && $dt_assignment_notes != '') {
									echo nl2br($dt_assignment_notes);
								}
							echo '</div>';
						
						echo '</li>';
						
						echo '<li>';
						
							echo '<div class="column dt-sc-one-fifth first">';
								echo __('Attachments', 'dt_themes');
							echo '</div>';
							echo '<div class="column dt-sc-four-fifth">';
								if(isset($dt_attachment_id) && $dt_attachment_id != '') {
									echo $dt_attachment_name.'<br />';
									echo '<a href="'.wp_get_attachment_url( $dt_attachment_id ).'" target="_blank">'.__('View Attachment', 'dt_themes').'</a>';
								} else {
									echo __('No attachments found!', 'dt_themes');
								}
							echo '</div>';
						
						echo '</li>';
					
					 echo '</ul>';
					
				}
			
			}
			
			if(isset($dt_assignment_course) && $dt_assignment_course != '') {
				echo '<a href="'.get_permalink($dt_assignment_course).'" class="dt-sc-button small back-to-course">'.__('Back to ', 'dt_themes').get_the_title($dt_assignment_course).'</a>';
			}
			
			?>
					
		</article>
			
		<?php
		} else {
			echo '<div class="dt-sc-warning-box">'.__('This assignment not yet assigned for any course, please contact your teacher for futher instructions!', 'dt_themes').'</div>';	
		}
		
		endwhile; endif;
	
	} else {
		echo '<div class="dt-sc-warning-box">'.__('Please login to get access to this assignment', 'dt_themes').'</div>';
	}
	
    ?>       

</section><!-- ** Primary Section End ** -->
    
<?php get_footer(); ?>