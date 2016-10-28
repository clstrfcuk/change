<?php
global $post;
$post_id = $post->ID;

?>

<!-- Subtitle -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Subtitle', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $assignment_subtitle = get_post_meta ( $post_id, "assignment-subtitle",true); ?>
        <input id="assignment-subtitle" name="assignment-subtitle" class="large" type="text" value="<?php echo $assignment_subtitle;?>" style="width:50%;" />
        <p class="note"> <?php _e('Add sutitle for your assignment.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Subtitle End -->

<!-- Maximum Mark -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Maximum Mark', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $assignment_maximum_mark = get_post_meta ( $post_id, "assignment-maximum-mark",true); ?>
        <input id="assignment-maximum-mark" name="assignment-maximum-mark" class="large" type="number" value="<?php echo $assignment_maximum_mark; ?>" style="width:10%;" />
        <p class="note"> <?php _e('Maximum mark for assignment.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Maximum Mark End -->

<!-- Enable Text Area -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Enable Text Area', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php
        $assignment_enable_textarea = get_post_meta ( $post_id, "assignment-enable-textarea",true);
        $switchclass = ($assignment_enable_textarea != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($assignment_enable_textarea != '') ? ' checked="checked"' : '';
        ?>
        <div data-for="assignment-enable-textarea" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="assignment-enable-textarea" class="hidden" type="checkbox" name="assignment-enable-textarea" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('If you wish you can enable text area for assignment.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Enable Text Area End -->

<!-- Enable File Upload -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Enable Attachment', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php
        $assignment_enable_attachment = get_post_meta ( $post_id, "assignment-enable-attachment",true);
        $switchclass = ($assignment_enable_attachment != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($assignment_enable_attachment != '') ? ' checked="checked"' : '';
        ?>
        <div data-for="assignment-enable-attachment" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="assignment-enable-attachment" class="hidden" type="checkbox" name="assignment-enable-attachment" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('If you wish you can enable attachment for assignment.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Enable File Upload End -->

<!-- Attachment Types -->
<div class="custom-box">

	<div class="column one-sixth">
    
       <label><?php _e('Attachment Types','dt_themes');?></label>

	</div>
	<div class="column five-sixth last">
    
        <?php
		$assignment_attachment_type = get_post_meta ( $post_id, "assignment-attachment-type",true);
		
		$attachment_types = dt_allowed_filetypes();

        $out = '';
        $out .= '<select id="assignment-attachment-type" name="assignment-attachment-type[]" multiple style="width:70%;" data-placeholder="'.__('Select Attachment Type...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
        $out .= '<option value=""></option>';
        if ( count( $attachment_types ) > 0 ) {
            foreach ($attachment_types as $attachment_type){
				if($assignment_attachment_type != '' && in_array($attachment_type, $assignment_attachment_type)) $str = 'selected="selected"'; else $str = '';
                $out .= '<option value="' . esc_attr( $attachment_type ) . '"' . $str . '>' . strtoupper( $attachment_type ) . '</option>' . "\n";
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
        <p class="note"> <?php _e('Choose attachment types here.','dt_themes');?> </p>

	</div>
    
</div>
<!-- Attachment Types End -->

<!-- Attachment Size -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Attachment Size (MB)', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $assignment_attachment_size = get_post_meta ( $post_id, "assignment-attachment-size",true); ?>
        <input id="assignment-attachment-size" name="assignment-attachment-size" class="large" type="number" value="<?php echo $assignment_attachment_size; ?>" style="width:10%;" />
        <p class="note"> <?php _e('Set maximum size for attachment. Set it less than <strong>'.dt_get_upload_size().'MB</strong>. If you like to have more than <strong>'.dt_get_upload_size().'MB</strong>, than you have to make changes in php.ini file. ','dt_themes');?> </p>
    </div>
    
</div>
<!-- Attachment Size End -->

<!-- Include In Course Evaluation -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Include In Course Evaluation', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php
        $assignment_course_evaluation = get_post_meta ( $post_id, "assignment-course-evaluation",true);
        $switchclass = ($assignment_course_evaluation != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($assignment_course_evaluation != '') ? ' checked="checked"' : '';
        ?>
        <div data-for="assignment-course-evaluation" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="assignment-course-evaluation" class="hidden" type="checkbox" name="assignment-course-evaluation" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('If you wish you can include this assignment in course evaluation.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Include In Course Evaluation End -->

<!-- Assignment Course -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Assignment Course','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php
        $post_args = array(	'post_type' 		=> 'dt_courses',
                            'numberposts' 		=> -1,
                            'orderby'         	=> 'title',
                            'order'           	=> 'DESC',
							'suppress_filters'  => FALSE
                            );
        $posts_array = get_posts( $post_args );
        
        $dt_assignment_course = get_post_meta ( $post_id, "dt-assignment-course",true);
        
        $out = '';
        $out .= '<select id="dt-assignment-course" name="dt-assignment-course" style="width:100%;" data-placeholder="'.__('Select Assignment Course...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
        $out .= '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $posts_array ) > 0 ) {
            foreach ($posts_array as $post_item){
                $out .= '<option value="' . esc_attr( $post_item->ID ) . '"' . selected( $post_item->ID, $dt_assignment_course, false ) . '>' . esc_html( $post_item->post_title ) . '</option>' . "\n";
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
		<p class="note"> <?php _e("Assign course to this assignment here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Assignment Course End -->