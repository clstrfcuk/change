<!-- #general -->
<div id="general" class="bpanel-content">

    <!-- .bpanel-main-content -->
    <div class="bpanel-main-content">
        <ul class="sub-panel"> 
            <li><a href="#my-general"><?php _e("General",'dt_themes');?></a></li>
            <li><a href="#my-sociable"><?php _e("Sociable",'dt_themes');?></a></li>
            <li><a href="#my-global"><?php _e("Global",'dt_themes');?></a></li>
        </ul>
        
        <!-- #my-general-->
        <div id="my-general" class="tab-content">
        
            <!-- .bpanel-box -->
            <div class="bpanel-box">
                    <!-- Logo -->
                    <div class="box-title"><h3><?php _e('Logo','dt_themes');?></h3></div>
                    <div class="box-content">
                    
                    
                    	<div class="column three-fifth">
                            <div class="bpanel-option-set">
                                <?php $logo = array(
                                        'id'=>		'logo',
                                        'options'=>		array(
                                                            'true'	=> __('Custom Image Logo','dt_themes'),
                                                             ''=> 	__('Display Site Title <small><a href="options-general.php">(click here to edit site title)</a></small>','dt_themes')
                                                            )
                                        );
                                             
                                        $output = "";
                                        $i = 0;
                                        foreach($logo['options'] as $key => $option):
                                            $checked = ( $key ==  dttheme_option('general',$logo['id']) ) ? ' checked="checked"' : '';
                                            $output .= "<label><input type='radio' name='mytheme[general][$logo[id]]' value='{$key}'  $checked />$option</label>";
                                            if($i == 0):
                                                $output .='<div class="clear"></div>';
                                            endif;
                                        $i++;
                                        endforeach;
                                        echo $output;?>
                          </div><!-- .bpanel-option-set end-->
                      
                        </div>
                        
                        <div class="column two-fifth last">
                            <p class="note"><?php _e('You can choose whether you wish to display a custom logo or your site title.','dt_themes');?></p>
                        </div>  
                        
                        <div class="hr"> </div>

                        <h6><?php _e('Logo','dt_themes');?></h6>  
                        <div class="image-preview-container">
                            <input id="mytheme-logo" name="mytheme[general][logo-url]" type="text" class="uploadfield" readonly="readonly"
                                    value="<?php echo  dttheme_option('general','logo-url');?>" />
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','logo-url'),false,'logo.png');?>
                        </div>
                        
                        <p class="note"> <?php _e('Upload a logo for your theme, or specify the image address of your online logo.','dt_themes');?> </p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Retina Logo','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-retina-logo" type="text" name="mytheme[general][retina-logo-url]" class="uploadfield" readonly="readonly" 
                            value="<?php echo dttheme_option('general','retina-logo-url');?>"/>
                          <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview" />
                          <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                          <?php dttheme_adminpanel_image_preview(dttheme_option('general','retina-logo-url'),false,'logo@2x.png');?>
                        </div>

                        <p class="note"><?php _e('Upload a retina logo for your theme, or specify the image address of your online logo.','dt_themes');?></p>
                        
                        <div class="clear"></div>
                        
                        <div class="one-half-content">
                        	<h6><?php _e('Width','dt_themes');?></h6>
                            <input type="text" class="medium" name="mytheme[general][retina-logo-width]" value="<?php echo dttheme_option('general','retina-logo-width');?>" />
							<?php _e('px','dt_themes');?>
                        </div>    

                        <div class="one-half-content last">
                        	<h6><?php _e('Height','dt_themes');?></h6>
                            <input type="text" class="medium" name="mytheme[general][retina-logo-height]" value="<?php echo dttheme_option('general','retina-logo-height');?>"/>
                            <?php _e('px','dt_themes');?>
                        </div>    
                        <p class="note"><?php _e('If retina logo is uploaded, enter the standard logo width and height in above respective boxes.','dt_themes');?></p>
                        <div class="clear"></div>
                          

                        <div class="hr"></div>
                        <div class="clear"></div>
                        
                        <h6><?php _e('Footer Logo','dt_themes');?></h6>  
                        <div class="image-preview-container">
                            <input id="mytheme-footer-logo" name="mytheme[general][footer-logo-url]" type="text" class="uploadfield" readonly="readonly"
                                    value="<?php echo  dttheme_option('general','footer-logo-url');?>" />
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','footer-logo-url'),false,'footer-logo.png');?>
                        </div>
                        
                        <p class="note"> <?php _e('Upload a footer logo for your theme, or specify the image address of your online footer logo.','dt_themes');?> </p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        
                        <h6><?php _e('Footer Retina Logo','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-retina-footer-logo" type="text" name="mytheme[general][retina-footer-logo-url]" class="uploadfield" readonly="readonly" 
                            value="<?php echo dttheme_option('general','retina-footer-logo-url');?>"/>
                          <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview" />
                          <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                          <?php dttheme_adminpanel_image_preview(dttheme_option('general','retina-footer-logo-url'),false,'footer-logo@2x.png');?>
                        </div>

                        <p class="note"><?php _e('Upload a retina footer logo for your theme, or specify the image address of your online footer logo.','dt_themes');?></p>
                        
                        <div class="clear"></div>
                        
                        <div class="one-half-content">
                        	<h6><?php _e('Width','dt_themes');?></h6>
                            <input type="text" class="medium" name="mytheme[general][retina-footer-logo-width]" 
                            	value="<?php echo dttheme_option('general','retina-footer-logo-width');?>" /><?php _e('px','dt_themes');?>
                        </div>    

                        <div class="one-half-content last">
                        	<h6><?php _e('Height','dt_themes');?></h6>
                            <input type="text" class="medium" name="mytheme[general][retina-footer-logo-height]" 
                            	value="<?php echo dttheme_option('general','retina-footer-logo-height');?>"/><?php _e('px','dt_themes');?>
                        </div>    
                        <p class="note"><?php _e('If footer retina logo is uploaded, enter the standard footer logo width and height in above respective boxes.','dt_themes');?></p>
                        
                    </div> <!-- Logo End -->

                    <!-- Favicon -->
                    <div class="box-title">
                        <h3><?php _e('Favicon','dt_themes');?></h3>
                    </div>
                    <div class="box-content">
                    	<h6> <?php _e('Enable Favicon','dt_themes');?> </h6> 
                        
                        <div class="column one-fifth">
                        <?php $checked = ( "true" ==  dttheme_option('general','enable-favicon') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','enable-favicon') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="enable-favicon" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="enable-favicon" name="mytheme[general][enable-favicon]" type="checkbox" 
                            value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last"></div>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Custom Favicon','dt_themes');?></h6>
                          <div class="image-preview-container">
                            <input id="mytheme-favicon" name="mytheme[general][favicon-url]" type="text" class="uploadfield"  readonly="readonly"
                                value="<?php echo  dttheme_option('general','favicon-url');?>" />
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','favicon-url'),false,'favicon.png');?>
                          </div>
                          <p class="note"> <?php _e('Upload a favicon for your theme, or specify the oneline URL for favicon','dt_themes');?>  </p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Apple iPhone Icon','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-apple-icon" name="mytheme[general][apple-favicon]" type="text" class="uploadfield" readonly="readonly"
                            value="<?php echo dttheme_option('general','apple-favicon');?>"/>
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','apple-favicon'),false,'apple-touch-icon.png');?>
                        </div>
                        <p class="note"> <?php _e('Upload your custom iPhone icon (57px by 57px), or specify the oneline URL for favicon','dt_themes');?>  </p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Apple iPhone Retina Icon','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-apple-icon" name="mytheme[general][apple-retina-favicon]" type="text" class="uploadfield" readonly="readonly"
                            value="<?php echo dttheme_option('general','apple-retina-favicon');?>"/>
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','apple-retina-favicon'),false,'apple-touch-icon-114x114.png');?>
                        </div>
                        <p class="note"><?php _e('Upload your custom iPhone retina icon (114px by 114px), or specify the oneline URL for favicon','dt_themes');?></p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Apple iPad Icon','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-apple-icon" name="mytheme[general][apple-ipad-favicon]" type="text" class="uploadfield" readonly="readonly"
                            value="<?php echo dttheme_option('general','apple-ipad-favicon');?>"/>
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','apple-ipad-favicon'),false,'apple-touch-icon-72x72.png');?>
                        </div>
                        <p class="note"> <?php _e('Upload your custom iPad icon (72px by 72px), or specify the oneline URL for favicon','dt_themes');?>  </p>

                        <div class="hr"></div>
                        <div class="clear"></div>
                        <h6><?php _e('Apple iPad Retina Icon','dt_themes');?></h6>
                        <div class="image-preview-container">
                          <input id="mytheme-apple-icon" name="mytheme[general][apple-ipad-retina-favicon]" type="text" class="uploadfield" readonly="readonly"
                            value="<?php echo dttheme_option('general','apple-ipad-retina-favicon');?>"/>
                            <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button" />
                            <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset" />
                            <?php dttheme_adminpanel_image_preview(dttheme_option('general','apple-ipad-retina-favicon'),false,'apple-touch-icon-144x144.png');?>
                        </div>
                        <p class="note"><?php _e('Upload your custom iPad retina icon (144px by 144px), or specify the oneline URL for favicon','dt_themes');?></p>

                    </div> <!-- Favicon End -->

                    <!-- Others -->
                    <div class="box-title"><h3><?php _e('Others', 'dt_themes');?></h3></div>
                    <div class="box-content">

                      <h6><?php _e('Enable Retina Support','dt_themes');?></h6>
                      <div class="column one-fifth">
                        <?php $checked = ( "true" ==  dttheme_option('general','enable-retina') ) ? ' checked="checked"' : ''; ?>
                        <?php $switchclass = ( "true" ==  dttheme_option('general','enable-retina') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                        <div data-for="mytheme-enable-retina" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-enable-retina" name="mytheme[general][enable-retina]" type="checkbox" value="true" <?php echo $checked;?>/>
                      </div>
                      <div class="column four-fifth last">
                        <p class="note"><?php _e('YES! to enable retina support, and the retina.js library would automatically try and find with @2x.png extension ','dt_themes');?></p>
                      </div>
                      <div class="clear"> </div>
                      <div class="hr"></div>

                      <h6> <?php _e('Enable Sticky Navigation','dt_themes'); ?></h6>
                    
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','enable-sticky-nav') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','enable-sticky-nav') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-enable-sticky-nav" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-enable-sticky-nav" name="mytheme[general][enable-sticky-nav]" type="checkbox" 
                            value="true" <?php echo $checked;?> />
                        </div>
                        
                        <div class="column four-fifth last">
                            <p class="note"><?php _e('YES! to enable sticky navigation.','dt_themes');?> </p>
                        </div>
                        
                        <div class="clear"> </div>
                        <div class="hr"></div>

                      <h6> <?php _e('Enable Sticky Navigation For Landing Page','dt_themes'); ?></h6>
                    
                      <div class="column one-fifth">
                          <?php $checked = ( "true" ==  dttheme_option('general','enable-landingpage-sticky-nav') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','enable-landingpage-sticky-nav') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-enable-landingpage-sticky-nav" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-enable-landingpage-sticky-nav" name="mytheme[general][enable-landingpage-sticky-nav]" type="checkbox" 
                            value="true" <?php echo $checked;?> />
                        </div>
                        
                        <div class="column four-fifth last">
                            <p class="note"><?php _e('YES! to enable sticky navigation in landing page.','dt_themes');?> </p>
                        </div>
                        
                        <div class="clear"> </div>
                        <div class="hr"></div>


                      <h6> <?php _e('Globally Disable Comments on Pages','dt_themes');?> </h6>
                      <div class="column one-fifth">
                      	<?php $checked = ( "true" ==  dttheme_option('general','disable-page-comment') ) ? ' checked="checked"' : ''; ?>
                        <?php $switchclass = ( "true" ==  dttheme_option('general','disable-page-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                        <div data-for="mytheme-global-page-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-global-page-comment" name="mytheme[general][disable-page-comment]" type="checkbox" value="true" <?php echo $checked;?> />
                      </div>
                      <div class="column four-fifth last">
                      	<p class="note no-margin"><?php 
							_e('YES! to disable comments on all the pages. This will globally override your "Allow comments" option under your page "Discussion" settings. ','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>
                      
                      
                        
                      <h6><?php _e('Globally Disable Comments on Posts','dt_themes');?></h6>
                      <div class="column one-fifth">
                      	<?php $checked = ( "true" ==  dttheme_option('general','global-post-comment') ) ? ' checked="checked"' : ''; ?>
                        <?php $switchclass = ( "true" ==  dttheme_option('general','global-post-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                        <div data-for="mytheme-global-post-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-global-post-comment" name="mytheme[general][global-post-comment]" type="checkbox" value="true" <?php echo $checked;?> />
                      </div>
                      <div class="column four-fifth last">
                      	<p class="note no-margin"><?php 
							_e('YES! to disable comments on all the posts. This will globally override your "Allow comments" option under your post "Discussion" settings. ','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>
                      

                      <h6><?php _e('Globally Disable Comments on Portfolios','dt_themes');?></h6>
                      <div class="column one-fifth">
                      	<?php $checked = ( "true" ==  dttheme_option('general','disable-portfolio-comment') ) ? ' checked="checked"' : ''; ?>
                        <?php $switchclass = ( "true" ==  dttheme_option('general','disable-portfolio-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                        <div data-for="mytheme-global-portfolio-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-global-portfolio-comment" name="mytheme[general][disable-portfolio-comment]" type="checkbox" value="true" <?php echo $checked;?> />
                      </div>
                      
                      <div class="column four-fifth last">
                      	<p class="note"><?php _e('Enable/ Disable comments on portfolio pages.','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>


                      <h6><?php _e('Globally Disable Comments on Teachers','dt_themes');?></h6>
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','disable-teacher-comment') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','disable-teacher-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-global-teacher-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-global-teacher-comment" name="mytheme[general][disable-teacher-comment]" type="checkbox" 
                            value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last">
                        	<p class="note"><?php _e('Enable/ Disable comments on teacher pages.','dt_themes');?> </p>
                        </div>
                        <div class="hr"></div>
                        
                        
                      <h6><?php _e('Globally Disable Comments on Courses','dt_themes');?></h6>
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','disable-courses-comment') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','disable-courses-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-global-courses-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-global-courses-comment" name="mytheme[general][disable-courses-comment]" type="checkbox" 
                            value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last">
                        	<p class="note"><?php _e('Enable/ Disable comments on course pages.','dt_themes');?> </p>
                        </div>
                        <div class="hr"></div>

                      <h6><?php _e('Globally Disable Comments on Lessons','dt_themes');?></h6>
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','disable-lessons-comment') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','disable-lessons-comment') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-global-lessons-comment" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-global-lessons-comment" name="mytheme[general][disable-lessons-comment]" type="checkbox" value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last">
                        	<p class="note"><?php _e('Enable/ Disable comments on lessons pages.','dt_themes');?> </p>
                        </div>
                        <div class="hr"></div>

                      <h6><?php _e('Globally Disable Ratings on Courses','dt_themes');?></h6>
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','disable-ratings-courses') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','disable-ratings-courses') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-global-ratings-courses" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-global-ratings-courses" name="mytheme[general][disable-ratings-courses]" type="checkbox" value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last">
                        	<p class="note"><?php _e('Enable/ Disable ratings on courses pages.','dt_themes');?> </p>
                        </div>
                        <div class="hr"></div>
                        
                      <h6><?php _e('Globally Disable Breadcrumb','dt_themes');?></h6>
                      <div class="column one-fifth">
                        	<?php $checked = ( "true" ==  dttheme_option('general','disable-breadcrumb-globally') ) ? ' checked="checked"' : ''; ?>
                            <?php $switchclass = ( "true" ==  dttheme_option('general','disable-breadcrumb-globally') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                            <div data-for="mytheme-disable-breadcrumb-globally" class="checkbox-switch <?php echo $switchclass;?>"></div>
                            <input class="hidden" id="mytheme-disable-breadcrumb-globally" name="mytheme[general][disable-breadcrumb-globally]" type="checkbox" value="true" <?php echo $checked;?> />
                        </div>
                        <div class="column four-fifth last">
                        	<p class="note"><?php _e('Enable/ Disable breadcrumbs for all pages here.','dt_themes');?> </p>
                        </div>
                        <div class="hr"></div>

                      <!-- Breadcrumb -->
                      <h6><?php _e('Breadcrumb Delimiter','dt_themes');?></h6>
                      <select id="mytheme-breadcrumb-delimiter" name="mytheme[general][breadcrumb-delimiter]"><?php 
                        $breadcrumb_icons = array('default','fa-angle-double-right','fa-sort','fa-angle-right','fa-caret-right','fa-arrow-right','fa-chevron-right','fa-plus','fa-hand-o-right','fa-arrow-circle-right');
                            foreach($breadcrumb_icons as $breadcrumb_icon):
                                $s = selected(dttheme_option('general','breadcrumb-delimiter'),$breadcrumb_icon,false);
                                echo "<option $s >$breadcrumb_icon</option>";
                            endforeach;?></select>
                            <p class="note"><?php _e('This is the symbol that will appear in between your breadcrumbs','dt_themes');?></p>
                      <div class="hr"></div>
                      <!-- Breadcrumb -->

                      <!-- Breadcrumb Search Box -->
                      <h6><?php _e('Disable Breadcrumb Search Box','dt_themes');?></h6>
                      <div class="column one-fifth">
                        <?php $switchclass = ( "on" ==  dttheme_option('general','disable-breadcrumb-searchbox') ) ? 'checkbox-switch-on' :'checkbox-switch-off';?>
                        <div data-for="mytheme-disable-breadcrumb-searchbox" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-disable-breadcrumb-searchbox" name="mytheme[general][disable-breadcrumb-searchbox]" type="checkbox"<?php checked(dttheme_option('general','disable-breadcrumb-searchbox'),'on');?>/>
                      </div>
                      <div class="column four-fifth last">
                        <p class="note"><?php _e('Check if you want to disable search box in the breadcrumb :)','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>
                      <!-- Breadcrumb Search Box -->

                      <!-- Browser Scroll -->
                      <h6><?php _e('Disable browser custom scroll','dt_themes');?></h6>
                      <div class="column one-fifth">
                        <?php $switchclass = ( "on" ==  dttheme_option('general','disable-custom-scroll') ) ? 'checkbox-switch-on' :'checkbox-switch-off';?>
                        <div data-for="mytheme-browesr-scroll-disable" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-browesr-scroll-disable" name="mytheme[general][disable-custom-scroll]" type="checkbox"<?php checked(dttheme_option('general','disable-custom-scroll'),'on');?>/>
                      </div>
                      <div class="column four-fifth last">
                        <p class="note"><?php _e('Check if you do not want disable the browser custom scrollbar :)','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>
                      <!-- Browser Scroll -->
                      
                      <h6><?php _e('Disable Style Picker','dt_themes');?></h6>
                      <div class="column one-fifth">
                      	<?php $switchclass = ( "on" ==  dttheme_option('general','disable-picker') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                        <div data-for="mytheme-global-disable-picker" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-global-disable-picker" name="mytheme[general][disable-picker]" type="checkbox" <?php checked(dttheme_option('general','disable-picker'),'on');?>/>                      </div>
                      <div class="column four-fifth last">
                      	<p class="note"><?php _e('YES! to hide the front end style picker','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>

                      <!-- Disable placeholder images -->
                      <h6><?php _e('Globally disable placeholder images','dt_themes');?></h6>
                      <div class="column one-fifth">
                        <?php $switchclass = ( "on" ==  dttheme_option('general','disable-placeholder-images') ) ? 'checkbox-switch-on' :'checkbox-switch-off';?>
                        <div data-for="mytheme-disable-placeholder-images" class="checkbox-switch <?php echo $switchclass;?>"></div>
                        <input class="hidden" id="mytheme-disable-placeholder-images" name="mytheme[general][disable-placeholder-images]" type="checkbox" <?php checked(dttheme_option('general','disable-placeholder-images'),'on');?>/>
                      </div>
                      <div class="column four-fifth last">
                        <p class="note"><?php _e('Check if you do not want to diaplay placeholder images','dt_themes');?> </p>
                      </div>
                      <div class="hr"></div>
                      <!-- Disable placeholder images -->

                      <h6><?php _e('Phone Number','dt_themes');?></h6>
                      <div class="column one-half">
                        <input id="mytheme-google-font-subset" name="mytheme[general][h4-phoneno]" type="text" value="<?php echo dttheme_option('general','h4-phoneno');?>"/>
                      </div>
                      
                      <div class="column one-half last">
                        <p class="note no-margin"><?php _e('Specify phone number which is shown at top of header style 3','dt_themes'); ?></p>
                      </div>

                      <h6><?php _e('Email id','dt_themes');?></h6>
                      <div class="column one-half">
                        <input id="mytheme-google-font-subset" name="mytheme[general][h4-emailid]" type="text" value="<?php echo dttheme_option('general','h4-emailid');?>"/>
                      </div>
                      
                      <div class="column one-half last">
                        <p class="note no-margin"><?php _e('Specify email id which is shown at top of header style 3','dt_themes'); ?></p>
                      </div>
                      <div class="hr"></div>
                      <h6><?php _e('Google Font Subset','dt_themes');?></h6>
                      <div class="column one-half">
                      	<input id="mytheme-google-font-subset" name="mytheme[general][google-font-subset]" type="text" value="<?php echo dttheme_option('general','google-font-subset');?>"/>
                      </div>
                      
                      <div class="column one-half last">
                      	<p class="note no-margin"><?php _e('Specify which subsets should be downloaded. Multiple subsets should be separated with coma','dt_themes'); ?></p>
                      </div>
                      
                      <div class="clear"> </div>
                      <div class="hr-invisible-small"> </div>
                      <p class="note"><?php _e('Some of the fonts in the Google Font Directory supports multiple scripts (like Latin and Cyrillic for example). In order to specify which subsets should be downloaded the subset parameter should be appended to the URL. For a complete list of available fonts and font subsets please see','dt_themes'); 
							echo " <a href='http://www.google.com/webfonts'>Google Web Fonts</a>";?> </p>
                      <div class="hr"></div>
                      <div class="clear"> </div>
                      
                      <h6><?php _e('Mailchimp API KEY','dt_themes');?></h6>
                      <div class="column one-half">
                      	<input id="mytheme-mailchimp-key" name="mytheme[general][mailchimp-key]" type="text" value="<?php echo dttheme_option('general','mailchimp-key'); ?>" />
                      </div>
                      
                      <div class="column one-half last">
                      	<p class="note no-margin"><?php _e('Paste your mailchimp api key that will be used by the mailchimp widget.','dt_themes'); ?></p>
                      </div>
 
                      <div class="hr"></div>
                      <div class="clear"> </div>

                      <h6><?php _e('Select Mailchimp List','dt_themes');?></h6>
                      <div class="column one-half">

                        <?php
						$list_id = (dttheme_option('general','mailchimp-listid') != '') ? dttheme_option('general','mailchimp-listid') : '';
						require_once(IAMD_FW."theme_widgets/mailchimp/MCAPI.class.php");
						$mcapi = new MCAPI( dttheme_option('general','mailchimp-key') );
						$lists = $mcapi->lists();
						?>

                        <select id="mytheme-mailchimp-listid" name="mytheme[general][mailchimp-listid]">
                        <?php foreach ($lists['data'] as $key => $value):
                                $id = $value['id'];
                                $name = $value['name'];
                                $selected = ( $list_id == $id ) ? ' selected="selected" ' : '';
                                echo "<option $selected value='$id'>$name</option>";
                             endforeach;?>
                        </select>                         
                        
                      </div>
                      <div class="column one-half last">
                      	<p class="note no-margin"><?php _e('Select your mailchip list id, which will be used in newsletter shortcode.','dt_themes'); ?></p>
                      </div>
                        
                    </div>                                            
                    
            </div><!-- .bpanel-box end-->
        </div><!--#my-general end-->
        
        
        <!-- #my-sociable-->
        <div id="my-sociable" class="tab-content">
            <!-- .bpanel-box -->
            <div class="bpanel-box">
            
                <div class="box-title">
                	<h3><?php _e('Sociable','dt_themes');?></h3>
                </div><!-- .box-title -->

                <div class="box-content">
                    <div class="bpanel-option-set">
                         <h6><?php _e('Show Sociable','dt_themes');?></h6>
                         
                         <div class="column one-fifth">
							 <?php $switchclass = ( "on" ==  dttheme_option('general','show-sociables') ) ? 'checkbox-switch-on' :'checkbox-switch-off'; ?>
                             <div data-for="mytheme-show-sociables" class="checkbox-switch <?php echo $switchclass;?>"></div>
                             <input class="hidden" id="mytheme-show-sociables" name="mytheme[general][show-sociables]" type="checkbox" 
                             <?php checked(dttheme_option('general','show-sociables'),'on');?>/>
                         </div>
                         
                         <input type="button" value="<?php _e('Add New Sociable','dt_themes');?>" class="black mytheme_add_item" />
                         
                         <div class="column four-fifth last">
                             <p class="note"> <?php _e('Manage Social Icons here which will be displayed in Header Type 4','dt_themes');?> </p>
                         </div>
                         
                         <div class="hr_invisible"></div>
                    </div>
                    
                    <div class="bpanel-option-set">
                        <ul class="menu-to-edit disable-sorting">
                        <?php $socials = dttheme_option('social');
        						      if(is_array($socials)): 
        							  	$keys = array_keys($socials);

        								$i=0;
        						 	  foreach($socials as $s):?>
                          <li id="<?php echo $keys[$i];?>">
                            <div class="item-bar">
                              <span class="item-title"><?php
                               if( array_key_exists('icon', $s)) {
									$n = $s['icon']; 
									$n = explode('-',$n);

									$k = 0; $title = '';
									foreach($n as $x) {
										if($k != 0) $title .= ' '.ucfirst($x); 	
										$k++;
									}
									echo $title;
							   }
								?></span>

                                <span class="item-control"><a class="item-edit"><?php _e('Edit','dt_themes');?></a></span>
                            </div>

                            <div class="item-content" style="display: none;">
                              <span><label><?php _e('Sociable Icon','dt_themes');?></label>
                              <?php echo dttheme_sociables_selection($keys[$i],$s['icon']);?></span>
                              <span>
                                <label><?php _e('Sociable Link','dt_themes');?></label>
                                <input type="text" class="social-link" name="mytheme[social][<?php echo $keys[$i];?>][link]" value="<?php echo $s['link']?>"/>
                              </span>

                              <div class="remove-cancel-links">
                                <span class="remove-item"><?php _e('Remove','dt_themes');?></span>
                                <span class="meta-sep"> | </span>
                                <span class="cancel-item"><?php _e('Cancel','dt_themes');?></span>
                              </div>
                            </div>
                          </li>
                        <?php $i++;endforeach; endif;?> 
                        </ul>
                        
                        <ul class="sample-to-edit" style="display:none;">
                        	<!-- Social Item -->
                            <li>
                            	<!-- .item-bar -->
                            	<div class="item-bar">
                                	<span class="item-title"><?php _e('Sociable','dt_themes');?></span>
                                    <span class="item-control"><a class="item-edit"><?php _e('Edit','dt_themes');?></a></span>
                                </div><!-- .item-bar -->
                                <!-- .item-content -->
                                <div class="item-content">                                
                                	<span><label><?php _e('Sociable Icon','dt_themes');?></label><?php echo dttheme_sociables_selection();?></span>
                                    <span><label><?php _e('Sociable Link','dt_themes');?></label><input type="text" class="social-link" /></span>
                                    
                                    <div class="remove-cancel-links">
                                        <span class="remove-item"><?php _e('Remove','dt_themes');?></span>
                                        <span class="meta-sep"> | </span>
                                        <span class="cancel-item"><?php _e('Cancel','dt_themes');?></span>
                                    </div>
                                </div><!-- .item-content end -->
                            </li><!-- Social Item End-->
                        </ul>
                        
                    </div>
                </div> <!-- .box-content -->    
            </div><!-- .bpanel-box end -->
        </div><!--#my-sociable end-->
        
       
        <!-- #my-global-->
        <div id="my-global" class="tab-content">
            <div class="bpanel-box">
                <div class="box-title">
                	<h3><?php _e('Global Settings','dt_themes');?></h3>
                </div>
                <div class="box-content">
                
                    <div class="bpanel-option-set">
                        <h6><?php _e('Force to Enable Global Page Layout','dt_themes');?></h6>
                        <?php dttheme_switch("",'general','force-enable-global-layout');?>
                        <p class="note"> <?php _e('Enable or Disable global page layout ("Disaplay Everywhere" sidebar) for all pages, posts, archive pages.','dt_themes');?>  </p>
                    </div>
                    <div class="clear"> </div>
                    <div class="hr"> </div>
                
                    <div class="bpanel-option-set">
                        <ul class="bpanel-post-layout bpanel-layout-set" id="dt-global-page">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
                        foreach($layout as $key => $value):
                            $class = ( $key ==  dttheme_option('general',"global-page-layout")) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input id="mytheme[general][global-page-layout]" name="mytheme[general][global-page-layout]" type="hidden" value="<?php echo dttheme_option('general',"global-page-layout");?>"/>
                    </div>
                        
                </div> 
            </div>
        </div>
        <!--#my-global end-->
        
        
   </div><!-- .bpanel-main-content end-->
</div><!-- #general end