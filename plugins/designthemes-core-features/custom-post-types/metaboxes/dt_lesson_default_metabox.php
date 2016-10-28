<?php
global $post;
$post_id = $post->ID;
$lesson_settings = get_post_meta ( $post->ID, '_lesson_settings', TRUE );
$lesson_settings = is_array ( $lesson_settings ) ? $lesson_settings : array ();

?>
 
<!-- Layout -->
<div id="page-layout" class="custom-box ">
	<div class="column one-sixth">
		<label><?php _e('Layout','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<ul class="bpanel-layout-set"><?php
			$layouts = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>	'right-sidebar', 'both-sidebar'=>'both-sidebar');
			
			$v =  array_key_exists("layout",$lesson_settings) ?  $lesson_settings['layout'] : 'content-full-width';
			foreach($layouts as $key => $value):
				$class = ($key == $v) ? " class='selected' " : "";
				echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
			endforeach; ?>
		</ul>
		<?php $v = array_key_exists("layout",$lesson_settings) ? $lesson_settings['layout'] : 'content-full-width';?>
		<input id="mytheme-lesson-layout" name="layout" type="hidden" value="<?php echo $v;?>" />
		<p class="note"> <?php _e("You can choose between a left, right or full width.",'dt_themes');?> </p>
	</div>
</div>
<!-- Layout End-->

<?php 
$sb_layout = array_key_exists("layout",$lesson_settings) ? $lesson_settings['layout'] : 'content-full-width';
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
                $switchclass = array_key_exists("disable-everywhere-sidebar-left",$lesson_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-left",$lesson_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-left" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-left" class="hidden" type="checkbox" name="disable-everywhere-sidebar-left" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Left End-->
    
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Left Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-left', $lesson_settings)):
                    $widgetareas =  array_unique($lesson_settings["widget-area-left"]);
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
                $switchclass = array_key_exists("disable-everywhere-sidebar-right",$lesson_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-right",$lesson_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-right" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-right" class="hidden" type="checkbox" name="disable-everywhere-sidebar-right" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Right End-->
        
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Right Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-right', $lesson_settings)):
                    $widgetareas =  array_unique($lesson_settings["widget-area-right"]);
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


<!-- Lesson Course -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Lesson Course','dt_themes');?></label>
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
        
        $dt_lesson_course = get_post_meta ( $post_id, "dt_lesson_course",true);
        
        $out = '';
        $out .= '<select id="dt-lesson-course" name="dt-lesson-course" style="width:100%;" data-placeholder="'.__('Select Course...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
        $out .= '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $posts_array ) > 0 ) {
            foreach ($posts_array as $post_item){
                $out .= '<option value="' . esc_attr( $post_item->ID ) . '"' . selected( $post_item->ID, $dt_lesson_course, false ) . '>' . esc_html( $post_item->post_title ) . '</option>' . "\n";
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
		<p class="note"> <?php _e("Assign course to this lesson here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Lesson Course End -->


<!-- Teacher -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Teacher','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php
        $post_args = array(	'post_type' 		=> 'dt_teachers',
                            'numberposts' 		=> -1,
                            'orderby'         	=> 'title',
                            'order'           	=> 'ASC',
							'suppress_filters'  => FALSE
                            );
        $posts_array = get_posts( $post_args );
        
		$lesson_teacher = get_post_meta ( $post_id, "lesson-teacher",true);
        
        $out = '';
        $out .= '<select id="lesson-teacher" name="lesson-teacher" style="width:100%;" data-placeholder="'.__('Select Teacher...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
        $out .= '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $posts_array ) > 0 ) {
            foreach ($posts_array as $post_item){
                $out .= '<option value="' . esc_attr( $post_item->ID ) . '"' . selected( $post_item->ID, $lesson_teacher, false ) . '>' . esc_html( $post_item->post_title ) . '</option>' . "\n";
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
		<p class="note"> <?php _e("Assign teacher to this lesson.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Teacher End -->

<!-- Lesson Video -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Video','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
    	<?php $lesson_video = get_post_meta ( $post_id, "lesson-video",true);?>
        <input id="lesson-video" name="lesson-video" class="large" type="text" value="<?php echo $lesson_video;?>" style="width:100%;" />
		<p class="note"> <?php _e("If you wish! You can add video here.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Lesson Video End -->

<!-- Lesson Duration -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Lesson Duration In Minutes','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php $v = array_key_exists("lesson-duration",$lesson_settings) ?  $lesson_settings['lesson-duration'] : '';?>
        <input id="lesson-duration" name="lesson-duration" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
		<p class="note"> <?php _e("Add duration of this lesson in minutes.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Lesson Duration End -->

<!-- Private Lesson -->
<div class="custom-box">
    <div class="column one-sixth">
		<label><?php _e( 'Private Lesson','dt_themes');?></label>
    </div>
    <div class="column five-sixth last">
        <div class="image-preview-container">
			<?php
			$switchclass = array_key_exists("private-lesson",$lesson_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
			$checked = array_key_exists("private-lesson",$lesson_settings) ? ' checked="checked"' : '';
			?>

			<div data-for="private-lesson" class="checkbox-switch <?php echo $switchclass;?>"></div>
			<input id="private-lesson" class="hidden" type="checkbox" name="private-lesson" value="true" <?php echo $checked;?>/>
			<p class="note"> <?php _e('YES! to mark this lesson as private. This lesson is visible only to the users who have purchased the course this lesson belongs to.','dt_themes');?> </p>
        </div>                    
    </div>
</div><!-- Private Lesson End -->

<!-- Quizes -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Quiz','dt_themes');?></label>
	</div>
	<div class="column five-sixth last">
		<?php
        $post_args = array(	'post_type' 		=> 'dt_quizes',
                            'numberposts' 		=> -1,
                            'orderby'         	=> 'date',
							'post_status'       => 'any',
                            'order'           	=> 'DESC',
							'suppress_filters'  => FALSE
                            );
        $posts_array = get_posts( $post_args );
        
        $dt_lesson_quiz = get_post_meta ( $post_id, "lesson-quiz",true);
        
        $out = '';
        $out .= '<select id="dt-lesson-quiz" name="dt-lesson-quiz" style="width:100%;" data-placeholder="'.__('Select Quiz...', 'dt_themes').'" class="dt-chosen-select">' . "\n";
        $out .= '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $posts_array ) > 0 ) {
            foreach ($posts_array as $post_item){
				if(dt_check_quiz_assigned_status($post_item->ID, $post_id)) {
					$out .= '<option value="' . esc_attr( $post_item->ID ) . '"' . selected( $post_item->ID, $dt_lesson_quiz, false ) . '>' . esc_html( $post_item->post_title ) . '</option>' . "\n";
				}
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
		<p class="note"> <?php _e("Assign quiz for this lesson. Once quiz assigned it cannot be used for another lesson.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Lesson Course End -->
