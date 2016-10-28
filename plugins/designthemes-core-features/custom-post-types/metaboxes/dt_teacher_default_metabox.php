<?php
global $post;
$post_id = $post->ID;
$teacher_settings = get_post_meta ( $post->ID, '_teacher_settings', TRUE );
$teacher_settings = is_array ( $teacher_settings ) ? $teacher_settings : array (); ?>

<!-- Layout -->
<div id="page-layout" class="custom-box ">
	<div class="column one-sixth">
		<label><?php _e('Layout','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<ul class="bpanel-layout-set"><?php
			$layouts = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>	'right-sidebar', 'both-sidebar'=>'both-sidebar');
			
			$v =  array_key_exists("layout",$teacher_settings) ?  $teacher_settings['layout'] : 'content-full-width';
			foreach($layouts as $key => $value):
				$class = ($key == $v) ? " class='selected' " : "";
				echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
			endforeach; ?>
		</ul>
		<?php $v = array_key_exists("layout",$teacher_settings) ? $teacher_settings['layout'] : 'content-full-width';?>
		<input id="mytheme-teacher-layout" name="layout" type="hidden" value="<?php echo $v;?>" />
		<p class="note"> <?php _e("You can choose between a left, right or full width.",'dt_themes');?> </p>
	</div>
</div>
<!-- Layout End-->

<?php 
$sb_layout = array_key_exists("layout",$teacher_settings) ? $teacher_settings['layout'] : 'content-full-width';
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
                $switchclass = array_key_exists("disable-everywhere-sidebar-left",$teacher_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-left",$teacher_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-left" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-left" class="hidden" type="checkbox" name="disable-everywhere-sidebar-left" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Left End-->
    
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Left Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-left', $teacher_settings)):
                    $widgetareas =  array_unique($teacher_settings["widget-area-left"]);
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
                $switchclass = array_key_exists("disable-everywhere-sidebar-right",$teacher_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-right",$teacher_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-right" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-right" class="hidden" type="checkbox" name="disable-everywhere-sidebar-right" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Right End-->
        
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Right Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-right', $teacher_settings)):
                    $widgetareas =  array_unique($teacher_settings["widget-area-right"]);
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

<!-- Allow Comments -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Allow Comments','dt_themes');?></label>
	</div>
	<div class="column five-sixth last"><?php
	$switchclass = array_key_exists ( "comment", $teacher_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
	$checked = array_key_exists ( "comment", $teacher_settings ) ? ' checked="checked"' : '';
	
	?><div data-for="mytheme-teacher-comment" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
		<input id="mytheme-teacher-comment" class="hidden" type="checkbox" name="mytheme-teacher-comment" value="true" <?php echo $checked;?> />
		<p class="note"> <?php _e('YES! to allow comments for this member.','dt_themes');?> </p>
	</div>
</div>
<!-- Allow Comments End -->

<!-- Show Social Share -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Show Social Links','dt_themes');?></label>
	</div>
	<div class="column five-sixth last"><?php
	$switchclass = array_key_exists ( "show-social-share", $teacher_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
	$checked = array_key_exists ( "show-social-share", $teacher_settings ) ? ' checked="checked"' : '';
	?><div data-for="mytheme-social-share" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
		<input id="mytheme-social-share" class="hidden" type="checkbox" name="mytheme-social-share" value="true" <?php echo $checked;?> />
		<p class="note"> <?php _e('Would you like to show the social share for member.','dt_themes');?> </p>
	</div>
</div>
<!-- Show Social Share End -->

<!-- Social Profile -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Social Profile','dt_themes');?></label>
	</div>

	<div class="column five-sixth last">
			<?php 
			
				$d  = array_key_exists( "teacher-social", $teacher_settings ) ? $teacher_settings['teacher-social'] : array();
				
				$sociables_icons_path  = plugin_dir_path(__FILE__);
				$x =  explode ( "designthemes-core-features" , $sociables_icons_path );
				$sociables_icons_path = $x[0].'designthemes-core-features/shortcodes/images/sociables';
				$sociables = DTCoreShortcodesDefination::dtListImages($sociables_icons_path);
				
				$i = 1;
				foreach( $sociables as  $k => $sociable ):
					if(!strpos($sociable, '@')) {
					if( $i === 3 ){
						$class = "column one-third last";
						$i = 1;
					} else {
						$class = "column one-third";
						$i++;
					}

					$v = array_key_exists($k, $d) ? $d[$k] : "";?>
				<div class="<?php echo $class;?>">
					<label><?php echo ucwords( $sociable);?></label>
					<input type="text" name="<?php echo "social[{$k}]";?>" style="width:100%" value="<?php echo $v;?>">
				</div>
			<?php } endforeach;?>
	</div>
</div><!-- Social Profile End-->


<!-- Additional Fields -->
<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-fourth">
            <label><?php _e('Role','dt_themes');?></label>
        </div>
        <div class="column three-fourth last">
            <?php $v = array_key_exists("role",$teacher_settings) ?  $teacher_settings['role'] : '';?>
            <input id="role" name="_role" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
            <p class="note"> <?php _e("Put member role.",'dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
        <div class="column one-fourth">
            <label><?php _e('Website','dt_themes');?></label>
        </div>
        <div class="column three-fourth last">
            <?php $v = array_key_exists("url",$teacher_settings) ?  $teacher_settings['url'] : '';?>
            <input id="url" name="_url" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
            <p class="note"> <?php _e("Put member's website url.",'dt_themes');?> </p>
        </div>

	</div>
    
</div>

<div class="custom-box">

    <div class="column one-half">

        <div class="column one-fourth">
            <label><?php _e('Experience','dt_themes');?></label>
        </div>
        <div class="column three-fourth last">
            <?php $v = array_key_exists("exp",$teacher_settings) ?  $teacher_settings['exp'] : '';?>
            <input id="exp" name="_exp" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
            <p class="note"> <?php _e("Put member's experience.",'dt_themes');?> </p>
        </div>
    
    </div>

	<div class="column one-half last">
    
        <div class="column one-fourth">
            <label><?php _e('Specialist In','dt_themes');?></label>
        </div>
        <div class="column three-fourth last">
            <?php $v = array_key_exists("special",$teacher_settings) ?  $teacher_settings['special'] : '';?>
            <input id="special" name="_special" class="large" type="text" value="<?php echo $v;?>" style="width:100%;" />
            <p class="note"> <?php _e("Put member's specialist in.",'dt_themes');?> </p>
        </div>

	</div>
    
</div>
<!-- Additional Fields End -->