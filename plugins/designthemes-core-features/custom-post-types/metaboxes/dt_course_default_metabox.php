<?php
global $post;
$post_id = $post->ID;
$course_settings = get_post_meta ( $post->ID, '_course_settings', TRUE );
$course_settings = is_array ( $course_settings ) ? $course_settings : array (); 
?>

<!-- Layout -->
<div id="page-layout" class="custom-box ">
	<div class="column one-sixth">
		<label><?php _e('Layout','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<ul class="bpanel-layout-set"><?php
			$layouts = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>	'right-sidebar', 'both-sidebar'=>'both-sidebar');
			
			$v =  array_key_exists("layout",$course_settings) ?  $course_settings['layout'] : 'content-full-width';
			foreach($layouts as $key => $value):
				$class = ($key == $v) ? " class='selected' " : "";
				echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
			endforeach; ?>
		</ul>
		<?php $v = array_key_exists("layout",$course_settings) ? $course_settings['layout'] : 'content-full-width';?>
		<input id="mytheme-course-layout" name="layout" type="hidden" value="<?php echo $v;?>" />
		<p class="note"> <?php _e("You can choose between a left, right or full width.",'dt_themes');?> </p>
	</div>
</div>
<!-- Layout End-->

<?php 
$sb_layout = array_key_exists("layout",$course_settings) ? $course_settings['layout'] : 'content-full-width';
$sidebar_both = $sidebar_left = $sidebar_right = '';
if($sb_layout == 'content-full-width') {
	$sidebar_both = 'style="display:none;"'; 
} elseif($sb_layout == 'with-left-sidebar') {
	$sidebar_right = 'style="display:none;"'; 
} elseif($sb_layout == 'with-right-sidebar') {
	$sidebar_left = 'style="display:none;"'; 
} 
?>
<div id="widget-area-options" <?php echo $sidebar_both;?>>

    <div id="left-sidebar-container" class="page-left-sidebar" <?php echo $sidebar_left; ?>>
        <!-- 2. Every Where Sidebar Left Start -->
        <div id="page-commom-sidebar" class="sidebar-section custom-box">
            <div class="column one-sixth"><label><?php _e('Disable Every Where Sidebar Left','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php 
                $switchclass = array_key_exists("disable-everywhere-sidebar-left",$course_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-left",$course_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-left" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-left" class="hidden" type="checkbox" name="disable-everywhere-sidebar-left" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Left End-->
    
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Left Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-left', $course_settings)):
                    $widgetareas =  array_unique($course_settings["widget-area-left"]);
                    $widgetareas = array_filter($widgetareas);
                    foreach( $widgetareas as $widgetarea ){
                        echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-left",$widgetarea,"multidropdown","sidebars");
                        echo '</div>';
                    }
                    echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-left","","multidropdown","sidebars");
                    echo '</div>';                                
                else:
                    echo '<div class="multidropdown">';
                       echo dttheme_custom_widgetarea_list("widgetareas-left","","multidropdown","sidebars");
                    echo '</div>';                                
                endif;?>
            </div>
        </div><!-- Choose Widget Areas End -->
    </div>
    
    <div id="right-sidebar-container" class="page-right-sidebar" <?php echo $sidebar_right; ?>>
        <!-- 3. Every Where Sidebar Right Start -->
        <div id="page-commom-sidebar" class="sidebar-section custom-box page-right-sidebar">
            <div class="column one-sixth"><label><?php _e('Disable Every Where Sidebar Right','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php 
                $switchclass = array_key_exists("disable-everywhere-sidebar-right",$course_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-right",$course_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-right" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-right" class="hidden" type="checkbox" name="disable-everywhere-sidebar-right" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Right End-->
        
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Right Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-right', $course_settings)):
                    $widgetareas =  array_unique($course_settings["widget-area-right"]);
                    $widgetareas = array_filter($widgetareas);
                    foreach( $widgetareas as $widgetarea ){
                        echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-right",$widgetarea,"multidropdown","sidebars");
                        echo '</div>';
                    }
                    echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-right","","multidropdown","sidebars");
                    echo '</div>';                                
                else:
                    echo '<div class="multidropdown">';
                       echo dttheme_custom_widgetarea_list("widgetareas-right","","multidropdown","sidebars");
                    echo '</div>';                                
                endif;?>
            </div>
        </div><!-- Choose Widget Areas End -->
    </div>

</div>


<!-- Course Video -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Video','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php $course_video = get_post_meta ( $post_id, "course-video",true);?>
        <input id="course-video" name="course-video" class="large" type="text" value="<?php echo $course_video;?>" style="width:100%;" />
		<p class="note"> <?php _e("If you wish! You can add video here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Course Video End -->

<!-- Referrence URL -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Referrence URL','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php $v = array_key_exists("referrrence_url",$course_settings) ?  $course_settings['referrrence_url'] : '';?>
        <input id="referrrence_url" name="referrrence_url" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
		<p class="note"> <?php _e("You can add referrence url for your course here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Referrence URL End -->

<!-- Starting Price -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Price','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
        <?php $starting_price = get_post_meta ( $post_id, "starting-price",true);?>
        <input id="starting-price" name="starting-price" class="large" type="text" value="<?php echo $starting_price;?>" style="width:100%;" />
		<p class="note"> <?php _e("You can add price for your course here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Starting Price End -->


<div class="custom-box">
	<!-- Featured Course -->
	<div class="column one-half">
    
        <div class="column one-third"><?php _e( 'Featured Course','dt_themes');?></div>
        <div class="column two-third last">
            <div class="image-preview-container">
                <?php
                $current = get_post_meta ( $post_id, "featured-course",true);
                $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';	
                $checked = ( $current === "true") ? ' checked="checked" ' : '';
                ?>
                <div data-for="featured-course" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="featured-course" class="hidden" type="checkbox" name="featured-course" value="true" <?php echo $checked;?>/>
                <p class="note"> <?php _e('YES! to make this as featured course.','dt_themes');?> </p>
            </div>                    
        </div>
        
    </div>
    <!-- Featured Course End -->
    <!-- Show Social Share -->
    <div class="column one-half last">

        <div class="column one-third">
            <label><?php _e('Show Social Share','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php
            $switchclass = array_key_exists ( "show-social-share", $course_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = array_key_exists ( "show-social-share", $course_settings ) ? ' checked="checked"' : '';
            ?>
            <div data-for="mytheme-social-share" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="mytheme-social-share" class="hidden" type="checkbox" name="mytheme-social-share" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('Would you like to show the social share for this course.','dt_themes');?> </p>
        </div>

    </div>
    <!-- Show Social Share End -->
</div>



<div class="custom-box">
	<!-- Disable Staffs -->
	<div class="column one-half">
    
        <div class="column one-third">
            <label><?php _e('Disable Teachers','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php
            $switchclass = array_key_exists ( "disable-staffs", $course_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = array_key_exists ( "disable-staffs", $course_settings ) ? ' checked="checked"' : '';
            ?>
            <div data-for="mytheme-disable-staffs" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="mytheme-disable-staffs" class="hidden" type="checkbox" name="mytheme-disable-staffs" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('You can hide the staff details here','dt_themes');?> </p>
        </div>
        
    </div>
    <!-- Disable Staffs End -->
    <!-- Show Related Courses -->
    <div class="column one-half last">
    
        <div class="column one-third">
            <label><?php _e('Show Related Courses','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php
            $switchclass = array_key_exists ( "show-related-course", $course_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = array_key_exists ( "show-related-course", $course_settings ) ? ' checked="checked"' : '';
            ?>
            <div data-for="mytheme-related-course" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="mytheme-related-course" class="hidden" type="checkbox" name="mytheme-related-course" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('Would you like to show the related courses.','dt_themes');?> </p>
        </div>
        
    </div>
    <!-- Show Related Courses End -->
</div>

<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-third">
            <label><?php _e('Enable Certificate','dt_themes');?></label>
        </div>
        <div class="column two-third last">
        <?php
        $enable_certificate = get_post_meta ( $post->ID, 'enable-certificate', TRUE );
        $switchclass = ($enable_certificate == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($enable_certificate == true) ? ' checked="checked"' : '';
        ?>
        <div data-for="enable-certificate" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="enable-certificate" class="hidden" type="checkbox" name="enable-certificate" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('Would you like to enable certificate for this course?','dt_themes');?> </p>
        </div>

		<div class="hr_invisible"></div><div class="hr_invisible"></div>
        
        <div class="column one-third">
           <label><?php _e('Certificate Percentage (%)', 'dt_themes'); ?></label>
        </div>
        <div class="column two-third last">
            <?php $certificate_percentage = get_post_meta ( $post->ID, 'certificate-percentage', TRUE ); ?>
            <input type="text" id="certificate-percentage" name="certificate-percentage" value="<?php echo $certificate_percentage; ?>" class="large">
            <p class="note"> <?php _e('Add percentage required to gain this certificate.','dt_themes');?> </p>
        </div>

		<div class="hr_invisible"></div><div class="hr_invisible"></div>
        
        <div class="column one-third">
           <label><?php _e('Certificate Template','dt_themes');?></label>
        </div>
        <div class="column two-third last">
            <?php
            $certificate_template = get_post_meta ( $post->ID, 'certificate-template', TRUE );
            $certificates_args = array( 'post_type' => 'dt_certificates', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters'  => FALSE );
            $certificates_array = get_posts( $certificates_args );
    
            $out = '';
            $out .= '<select id="certificate-template" name="certificate-template" style="width:70%;" data-placeholder="'.__('Select Certificate Template...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
            $out .= '<option value="">' . __( 'None', 'dt_themes' ) . '</option>';
            if ( count( $certificates_array ) > 0 ) {
                foreach ($certificates_array as $certificate){
                    $out .= '<option value="' . esc_attr( $certificate->ID ) . '"' . selected( $certificate->ID, $certificate_template, false ) . '>' . esc_html( $certificate->post_title ) . '</option>' . "\n";
                }
            }
            $out .= '</select>' . "\n";
            echo $out;
            ?>
            <p class="note"> <?php _e('Choose certificate template here.','dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
        <div class="column one-third">
            <label><?php _e('Enable Badge','dt_themes');?></label>
        </div>
        <div class="column two-third last">
        <?php
        $enable_badge = get_post_meta ( $post->ID, 'enable-badge', TRUE );
        $switchclass = ($enable_badge == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($enable_badge == true) ? ' checked="checked"' : '';
        ?>
        <div data-for="enable-badge" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="enable-badge" class="hidden" type="checkbox" name="enable-badge" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('Would you like to enable badge for this course?','dt_themes');?> </p>
        </div>
    
    	<div class="hr_invisible"></div><div class="hr_invisible"></div>
        
        <div class="column one-third">
           <label><?php _e('Badge Percentage (%)', 'dt_themes'); ?></label>
        </div>
        <div class="column two-third last">
            <?php $badge_percentage = get_post_meta ( $post->ID, 'badge-percentage', TRUE ); ?>
            <input type="text" id="badge-percentage" name="badge-percentage" value="<?php echo $badge_percentage; ?>" class="large">
            <p class="note"> <?php _e('Add percentage required to gain this badge.','dt_themes');?> </p>
        </div>
    
    	<div class="hr_invisible"></div><div class="hr_invisible"></div>
        
        <div class="column one-third">
           <label><?php _e('Badge Image', 'dt_themes'); ?></label>
        </div>
        <div class="column two-third last">
            <div class="image-preview-container">
				<?php $badge_image = get_post_meta ( $post->ID, 'badge-image', TRUE ); ?>
                <input name="badge-image" type="text" class="uploadfield large" readonly value="<?php echo $badge_image;?>"/>
                <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview" />
                <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                <?php if( !empty($badge_image) ) dttheme_adminpanel_image_preview($badge_image );?>
            </div>
            <p class="note"> <?php _e('Choose badge_image for your course.','dt_themes');?> </p>
        </div>

	</div>
    
</div>

<!-- Course Lessons -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Lessons','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
    
		<?php
		$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'hierarchical' => 1,  'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $post_id );
		$lessons_array = get_pages( $lesson_args );
		
		if(isset($lessons_array) && count($lessons_array) > 0 ) {
			
			echo '<table border="0" cellpadding="0" width="920" cellspacing="10">
					<thead>
					  <tr>
						<th width="99" scope="col" align="left">'.__('S#', 'dt_themes').'</th>
						<th width="415" scope="col" align="left">'.__('Title', 'dt_themes').'</th>
						<th width="302" scope="col" align="left">'.__('Teacher', 'dt_themes').'</th>
						<th width="90" scope="col" align="left">'.__('Option', 'dt_themes').'</th>
					  </tr>
					 </thead>
					 <tbody>';
			
			$k = 1;
			
			foreach($lessons_array as $lesson) {
				
				$staff_name = '';	
				$lesson_teacher = get_post_meta ( $lesson->ID, "lesson-teacher",true);
				if($lesson_teacher != '') {
				$post_data = get_post($lesson_teacher);
				$staff_name = $post_data->post_title;
				}
				
				echo '<tr>
					<td>'.$k.'</td>
					<td>'.$lesson->post_title.'</td>
					<td>'.$staff_name.'</td>
					<td><a href="'. esc_url( get_edit_post_link( $lesson->ID ) ).'">'.__('Edit','dt_themes').'</a></td>
				  </tr>';
					
				$k++;
			}
			
			echo '</tbody></table>';
		} else {
			echo '<table border="0" cellpadding="0" width="920" cellspacing="10"><tr><td>'.__('No Lessons Found!','dt_themes').'<td><tr></table>';
		}
		
		?>
      <div class="clear"></div>
	</div>
</div><!-- Course Lessons End -->

<div class="custom-box">

	<div class="column one-sixth">
		<label><?php _e('Attachments','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
    
    	<a href="#" class="dt-add-attachments custom-button-style"><?php _e('Add', 'dt_themes'); ?></a>
        
        <div class="hr_invisible"></div>
        
        <div id="dt-attachments-container">
        
			<?php
			$media_attachments = get_post_meta ( $post_id, "media-attachments", true);
			
			if(isset($media_attachments) && !empty($media_attachments)) {
				foreach($media_attachments as $attachment) {
					if($attachment != '') {
						?>
							<div id="dt-attachments-holder" class="file-upload-container">
								<input name="media-attachments[]" type="text" class="uploadfield large" readonly value="<?php echo $attachment; ?>"/>
								<input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button multifile-upload show_preview" />
								<input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
								<span class="dt-remove-attacment">X</span>
							</div>
						<?php
					}
				}
			}
			?>        
        
        </div>
        <div id="dt-attachments-clone" class="hidden">
            <input name="" type="text" class="uploadfield large" readonly value=""/>
            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button multifile-upload show_preview" />
            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
            <span class="dt-remove-attacment">X</span>
            <div class="hr_invisible"></div>
        </div>

		<p class="note"> <?php _e("You can add any number of media attachments for this course.",'dt_themes');?> </p>
	</div>

</div>
