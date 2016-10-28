<!-- #buddypress-pages starts here-->
<div id="buddypress-pages" class="bpanel-content">

    <!-- .bpanel-main-content starts here-->
    <div class="bpanel-main-content">
        <ul class="sub-panel">
            <li><a href="#my-buddypress"><?php _e("BuddyPress Settings",'dt_themes');?></a></li>
        </ul>

        <!-- #my-buddypress starts here -->
        <div id="my-buddypress" class="tab-content">
            <div class="bpanel-box">
            <?php if( dttheme_is_plugin_active('buddypress/bp-loader.php') ):?>
                <!-- Members List Starts Here -->
                <div class="box-title">
                    <h3><?php _e('Members List','dt_themes');?></h3>
                </div>
                <div class="box-content">

                    <!-- Members Per Page -->
                    <div class="column one-third">
                        <label><?php _e('Members Per Page','dt_themes');?></label>
                    </div>
                    <div class="column two-third last">
                        <input name="mytheme[bp][members-per-page]" type="text" class="small" value="<?php echo trim(stripslashes(dttheme_option('bp','members-per-page')));?>" />
                        <p class="note"><?php _e('Number of members to show in members list page','dt_themes');?></p>
                    </div><!-- Members Per Page -->

                    <!-- Layout  -->
                    <h6><?php _e('Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Members Layout Style in Members list ","dt_themes");?> </p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                        <ul class="bpanel-post-layout bpanel-layout-set"><?php 
                            $posts_layout = array('one-half-column'=>__("Two Members per row.",'dt_themes'),'one-third-column' => __("Three Members per row.",'dt_themes'),'one-fourth-column' => __("Four Members per row","dt_themes"));
                            $v = dttheme_option('bp',"members-page-layout");
                            $v = !empty($v) ? $v : "one-third-column";

                            foreach($posts_layout as $key => $value):
                                $class = ( $key ==  $v ) ? " class='selected' " :"";
                                echo "<li><a href='#' rel='{$key}' {$class} title='{$value}'><img src='".IAMD_FW_URL."theme_options/images/columns/{$key}.png' alt='' /></a></li>";
                            endforeach;?>
                        </ul>
                        <input name="mytheme[bp][members-page-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Layout  -->

                </div>
                <!-- Members Lists Ends Here -->
            <?php else: ?>
                <div class="box-title">
                    <h3><?php _e('Warning','dt_themes');?></h3>
                </div>
                <div class="box-content">
                    <p class="note"><?php _e("You have to install and activate the BuddyPress plugin to use this module ..",'dt_themes');?></p>
                </div>
            <?php endif; ?>
            </div>
        </div><!-- #my-buddypress ends here -->    
    </div><!-- .bpanel-main-content ENDS here-->

</div><!-- #buddypress-pages ends here-->