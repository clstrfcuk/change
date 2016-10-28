<!-- #sensei starts here-->
<div id="courses" class="bpanel-content">
  	<!-- .bpanel-main-content starts here-->
    <div class="bpanel-main-content">
        <ul class="sub-panel">
        	<li><a href="#default-course"><?php _e("Default Course",'dt_themes');?></a></li>
            <li><a href="#my-sensei"><?php _e("Sensei Course",'dt_themes');?></a></li>
        </ul>
        
        <!-- #default-course starts here --> 
        <div id="default-course" class="tab-content">

            <div class="bpanel-box">
                <div class="box-title">
                    <h3><?php _e('General','dt_themes');?></h3>
                </div>
                <div class="box-content">

                    <div class="column one-third"><label><?php _e('Currency Symbol','dt_themes');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][currency]" type="text" class="small" value="<?php echo trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','currency'))));?>" />
                        <p class="note"><?php _e('Please set default currency symbol which will be used in front end display','dt_themes');?></p>
                    </div>
                    
					<div class="hr_invisible"> </div>
 
                    <div class="column one-third"><label><?php _e('Currency Symbol Position','dt_themes');?></label></div>
                    <div class="column two-third last">
                        <?php $position_options = array('before-price' => 'Before Price', 'after-price' => 'After Price'); ?>
                        <select id="mytheme-currency-position" name="mytheme[dt_course][currency-position]">
                        <?php 
						$currency_position = dttheme_option('dt_course','currency-position') != '' ? dttheme_option('dt_course','currency-position') : 'before-price';
						foreach ($position_options as $key => $value):
							$selected = ( $currency_position == $key ) ? ' selected="selected" ' : '';
							echo "<option $selected value='$key'>$value</option>";
						 endforeach;
						 ?>
                        </select>                         
                        <p class="note"><?php _e('Please select curreny position to display in front end','dt_themes');?></p>
                    </div>
                    
					<div class="hr_invisible"> </div>
 
                    <div class="column one-third"><label><?php _e('Currency','dt_themes');?></label></div>
                    <div class="column two-third last">
                        <?php
						$currency_options = array('ADF', 'ADP', 'AED', 'AFA', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'AON', 'ARS', 'ATS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BEF', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BTN', 'BWP', 'BYR', 'BZD', 'CAD', 'CDF', 'CFP', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CSK', 'CUC', 'CUP', 'CVE', 'CYP', 'CZK', 'DEM', 'DJF', 'DKK', 'DOP', 'DZD', 'ECS', 'EEK', 'EGP', 'ESP', 'ETB', 'EUR', 'FIM', 'FJD', 'FKP', 'FRF', 'GBP', 'GEL', 'GHC', 'GHS', 'GIP', 'GMD', 'GNF', 'GRD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'IEP', 'ILS', 'INR', 'IQD', 'IRR', 'ISK', 'ITL', 'JMD', 'JOD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LTL', 'LUF', 'LVL', 'LYD', 'MAD', 'MDL', 'MGF', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MTL', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZM', 'MZN', 'NAD', 'NGN', 'NIO', 'NLG', 'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PTE', 'PYG', 'QAR', 'ROL', 'RON', 'RSD', 'RUB', 'SAR', 'SBD', 'SCR', 'SDD', 'SDG', 'SDP', 'SEK', 'SGD', 'SHP', 'SIT', 'SKK', 'SLL', 'SOS', 'SRD', 'SRG', 'STD', 'SVC', 'SYP', 'SZL', 'THB', 'TMM', 'TND', 'TOP', 'TRL', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGS', 'USD', 'UYU', 'UZS', 'VEF', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'YUN', 'ZAR', 'ZMK', 'ZWD');
						$currency_s2member = dttheme_option('dt_course','currency-s2member') != '' ? dttheme_option('dt_course','currency-s2member') : 'USD';
						 ?>
                        <select id="mytheme-currency-s2member" name="mytheme[dt_course][currency-s2member]">
                        <?php 
						foreach ($currency_options as $value):
							$selected = ( $value == $currency_s2member ) ? ' selected="selected" ' : '';
							echo "<option $selected value='$value'>$value</option>";
						 endforeach;
						 ?>
                        </select>                         
                        <p class="note"><?php _e('Please select your curreny symbol which will be used in s2member for payment.','dt_themes');?></p>
                    </div>
                    
                </div>
            </div>
          
            <div class="bpanel-box">
                <div class="box-title">
                    <h3><?php _e('s2Member Settings','dt_themes');?></h3>
                </div>
                <div class="box-content">

					<?php 
					if(dttheme_is_plugin_active('s2member/s2member.php')) {
						
						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["pro_gateways_seen"] == 1) {
							echo '<h6><strong>'.__('Choose Payment Types', 'dt_themes').'</strong></h6><div class="clear"></div>';
							$payments = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["pro_gateways_enabled"];
							$payments = array_merge($payments); 
							$payments_selected = dttheme_option('dt_course', 'payments-selected');
							$payments_selected = !empty($payments_selected) ? $payments_selected : array();
							foreach($payments as $payment_key => $payment) {
								$checked = (in_array(trim($payment), $payments_selected)) ? 'checked="checked"' : '';
								echo '<label><input type="checkbox" name="mytheme[dt_course][payments-selected][]" value="'.$payment.'" '.$checked.' />'.ucfirst($payment).'</label>';
							}
							echo '<div class="hr_invisible"></div><div class="hr_invisible"></div><h6><strong>'.__('Click Bank Settings', 'dt_themes').'</strong></h6><div class="clear"></div>';
							echo '<div class="column one-half">
									<label>'.__('Product Item No','dt_themes').'</label>
								  </div>
								  <div class="column one-half last">
									<input name="mytheme[dt_course][s2member-cb-productno]" type="text" class="medium" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-cb-productno')))).'" />
								  </div>
								  <div class="hr_invisible"></div>';
							echo '<div class="column one-half">
									<label>'.__('Order Form Template (Skin)','dt_themes').'</label>
								  </div>
								  <div class="column one-half last">
									<input name="mytheme[dt_course][s2member-cb-skin]" type="text" class="medium" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-cb-skin')))).'" />
								  </div>
								  <div class="hr_invisible"></div>';
							echo '<div class="column one-half">
									<label>'.__('PitchPlus Upsell Flow ID','dt_themes').'</label>
								  </div>
								  <div class="column one-half last">
									<input name="mytheme[dt_course][s2member-cb-flowid]" type="text" class="medium" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-cb-flowid')))).'" />
								  </div>
								  <div class="hr_invisible"></div><div class="hr_invisible"></div><div class="hr_invisible"></div>';
						}
						
						
						
						for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++) { 
	
							echo '<h6><strong>'.do_shortcode("[s2Get constant='S2MEMBER_LEVEL{$n}_LABEL'/]").' ('.__('s2Member Level', 'dt_themes').' '.$n.')</strong></h6><div class="clear"></div>';
							echo '<div class="column one-third">
									<label>'.__('Description','dt_themes').'</label>
								  </div>
								  <div class="column two-third last">
									<input name="mytheme[dt_course][s2member-'.$n.'-description]" type="text" class="large" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-'.$n.'-description')))).'" />
								  </div>
								  <div class="hr_invisible"></div>';
							echo '<div class="column one-third">
									<label>'.__('Period','dt_themes').'</label>
								  </div>
								  <div class="column two-third last">
									<input name="mytheme[dt_course][s2member-'.$n.'-period]" type="text" class="medium" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-'.$n.'-period')))).'" />
								  </div>
								  <div class="hr_invisible"></div>';
							echo '<div class="column one-third">
									<label>'.__('Term','dt_themes').'</label>
								  </div>
								  <div class="column two-third last">
									<select name="mytheme[dt_course][s2member-'.$n.'-term]">';
										$terms = array('', 'D' => 'Day(s)', 'W' => 'Week(s)', 'M' => 'Month(s)', 'Y' => 'Year(s)', 'L' => 'Lifetime');
										foreach($terms as $term_key => $term){
											$selected = selected(dttheme_option('dt_course','s2member-'.$n.'-term'), $term_key, false );
											echo '<option value="'.$term_key.'" '.$selected.'>'.$term.'</option>';
										}
								echo '</select>
								  </div>';
							
							if($n > 1) {	  
								echo '<div class="hr_invisible"></div>
									  <div class="column one-third">
										<label>'.__('Price','dt_themes').'</label>
									  </div>
									  <div class="column two-third last">
										<input name="mytheme[dt_course][s2member-'.$n.'-price]" type="text" class="medium" value="'.trim(stripslashes(dttheme_wp_kses(dttheme_option('dt_course','s2member-'.$n.'-price')))).'" />
									  </div>';
							}
							
							echo '<div class="hr_invisible"></div><div class="hr_invisible"></div>';
							
						}
						
						echo '<h6><strong>'.__('Notes:', 'dt_themes').'</strong></h6>';
						echo '<ul class="s2member-notes">';
							echo '<li><strong>'.__('s2Member Level 1', 'dt_themes').'</strong> - '.__('These are users who are going to purchase only particular course. Course price will be applied.', 'dt_themes').'</li>';
							echo '<li><strong>'.__('s2Member Level 2, s2Member Level 3, s2Member Level 4', 'dt_themes').'</strong> - '.__('Users will have access to all courses for specified period and term. Price have to be mentioned here.', 'dt_themes').'</li>';
							echo '<li><strong>'.__('Description', 'dt_themes').'</strong> - '.__('Will appear in the purchase page.', 'dt_themes').'</li>';
							echo '<li><strong>'.__('Period', 'dt_themes').'</strong> - '.__('Must be >= 1. (ex: 1 Week, 2 Months, 1 Month, 3 Days).', 'dt_themes').'</li>';
							echo '<li><strong>'.__('Term', 'dt_themes').'</strong> - '.__('Regular Term. Days, Weeks, Months, Years, Lifetime.', 'dt_themes').'</li>';
							echo '<li><strong>'.__('Price', 'dt_themes').'</strong> - '.__('Price for the package. Applicable only for s2Member Level 2, s2Member Level 3, s2Member Level 4.', 'dt_themes').'</li>';
						echo '</ul>';
					
					} else {
						echo __('Please activate s2Member plugin in order to continue!', 'dt_themes');	
					}
					
                    ?>
                    
                </div>
            </div>
          
            <div class="bpanel-box">
                <div class="box-title">
                    <h3><?php _e('Archive Page','dt_themes');?></h3>
                </div>
                <div class="box-content">


                    <!-- Layout -->
                    <h6><?php _e('Post Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Course Archive page layout ","dt_themes");?> </p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                        <ul class="bpanel-post-layout bpanel-layout-set">
                        <?php $posts_layout = array('one-half-column'=>__("Two products per row.",'dt_themes'),'one-third-column' => __("Three products per row.",'dt_themes'));
                                    $v = dttheme_option('dt_course',"archives-post-layout");
                                    $v = !empty($v) ? $v : "one-half-column";
                                  foreach($posts_layout as $key => $value):
                                    $class = ( $key ==  $v ) ? " class='selected' " :"";                                  
                                    echo "<li><a href='#' rel='{$key}' {$class} title='{$value}'><img src='".IAMD_FW_URL."theme_options/images/columns/{$key}.png' alt='' /></a></li>";
                                 endforeach;?>                        
                        </ul>
                        <input name="mytheme[dt_course][archives-post-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- .Layout End -->

					<div class="hr_invisible"> </div>
                    
                	<!-- Course Page Layout -->
                    <h6><?php _e('Page Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Course page layout","dt_themes");?></p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                    	<ul class="bpanel-post-layout bpanel-layout-set" id="archives-layout">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
							  $v = 	dttheme_option('dt_course',"archives-layout");
							  $v = !empty($v) ? $v : "content-full-width";
							  
                        foreach($layout as $key => $value):
                            $class = ( $key ==  $v ) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input name="mytheme[dt_course][archives-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Course Page Layout End-->
                    
					 <?php 
                     $sb_layout = dttheme_option('dt_course',"archives-layout");
                     $sidebar_both = $sidebar_left = $sidebar_right = '';
                     if($sb_layout == 'content-full-width') {
                        $sidebar_both = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-left-sidebar') {
                        $sidebar_right = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-right-sidebar') {
                        $sidebar_left = 'style="display:none;"'; 
                     } 
                     ?>
                    <div id="bpanel-widget-area-options" <?php echo 'class="archives-layout" '.$sidebar_both;?>>
                        
                        <div id="left-sidebar-container" class="bpanel-page-left-sidebar" <?php echo $sidebar_left; ?>>
                            <!-- 2. Every Where Sidebar Left Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Every Where Sidebar Left','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'dt_course','disable-everywhere-left-sidebar-for-course-archive'); ?>
                            </div><!-- Every Where Sidebar Left End-->
                        </div>
    
                        <div id="right-sidebar-container" class="bpanel-page-right-sidebar" <?php echo $sidebar_right; ?>>
                            <!-- 3. Every Where Sidebar Right Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Every Where Sidebar Right','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'dt_course','disable-everywhere-right-sidebar-for-course-archive'); ?>
                            </div><!-- Every Where Sidebar Right End-->
                        </div>
                        
                    </div>                    

                </div>
            </div>
            
            <div class="bpanel-box">
                <div class="box-title">
                    <h3><?php _e('Permalink Settings','dt_themes');?></h3>
                </div>
                <div class="box-content">

                    <div class="column one-third"><label><?php esc_html_e('Single Course slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][single-course-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','single-course-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. courses <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>

                    <div class="column one-third"><label><?php esc_html_e('Course Category slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][course-category-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','course-category-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. coursecategory <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>
                    
                    <div class="column one-third"><label><?php esc_html_e('Single Lesson slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][single-lesson-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','single-lesson-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. lessons <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>

                    <div class="column one-third"><label><?php esc_html_e('Lesson Category slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][lesson-category-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','lesson-category-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. lesson-complexity <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>

                    <div class="column one-third"><label><?php esc_html_e('Single Question slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][single-question-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','single-question-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. questions <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>

                    <div class="column one-third"><label><?php esc_html_e('Single Quiz slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][single-quiz-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','single-quiz-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. quizes <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    <div class="hr"></div>

                    <div class="column one-third"><label><?php esc_html_e('Single Assignment slug', 'priority');?></label></div>
                    <div class="column two-third last">
                        <input name="mytheme[dt_course][single-assignment-slug]" type="text" class="medium" value="<?php echo trim(stripslashes(dttheme_option('dt_course','single-assignment-slug')));?>" />
                        <p class="note"><?php esc_html_e('Do not use characters not allowed in links. Use, eg. assignments <br> <b>After change go to Settings > Permalinks and click Save changes.</b>', 'priority');?></p>
                    </div>
                    
                </div>
            </div>            
            
		</div><!-- #default-course ends here --> 
        
                
        <!-- #my-sensei starts here --> 
        <div id="my-sensei" class="tab-content">
        	<div class="bpanel-box">
			<?php  if( class_exists('WooThemes_Sensei') ) : ?>

				<!-- Course Page -->
            	<div class="box-title"><h3><?php _e('Course Detail','dt_themes');?></h3></div>
                <div class="box-content">
                	<!-- Course Detail Page Layout -->
                    <h6><?php _e('Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Course page layout","dt_themes");?></p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                    	<ul class="bpanel-post-layout bpanel-layout-set" id="course-layout">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
							  $v = 	dttheme_option('sensei',"course-layout");
							  $v = !empty($v) ? $v : "content-full-width";
							  
                        foreach($layout as $key => $value):
                            $class = ( $key ==  $v ) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input name="mytheme[sensei][course-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Course Detail Page Layout End-->
					 <?php 
                     $sb_layout = dttheme_option('sensei',"course-layout");
                     $sidebar_both = $sidebar_left = $sidebar_right = '';
                     if($sb_layout == 'content-full-width') {
                        $sidebar_both = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-left-sidebar') {
                        $sidebar_right = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-right-sidebar') {
                        $sidebar_left = 'style="display:none;"'; 
                     } 
                     ?>
                    <div id="bpanel-widget-area-options" <?php echo 'class="course-layout" '.$sidebar_both;?>>
                        
                        <div id="left-sidebar-container" class="bpanel-page-left-sidebar" <?php echo $sidebar_left; ?>>
                            <!-- 2. Every Where Sidebar Left Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Left','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-left-sidebar-for-course-layout'); ?>
                            </div><!-- Every Where Sidebar Left End-->
                        </div>
    
                        <div id="right-sidebar-container" class="bpanel-page-right-sidebar" <?php echo $sidebar_right; ?>>
                            <!-- 3. Every Where Sidebar Right Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Right','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-right-sidebar-for-course-layout'); ?>
                            </div><!-- Every Where Sidebar Right End-->
                        </div>
                        
                    </div>                    
                    
                </div><!-- Course Page -->

				<!-- Lesson Page -->
            	<div class="box-title"><h3><?php _e('Lesson Detail','dt_themes');?></h3></div>
                <div class="box-content">
                	<!-- Lesson Detail Page Layout -->
                    <h6><?php _e('Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Lesson page layout","dt_themes");?></p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                    	<ul class="bpanel-post-layout bpanel-layout-set" id="lesson-layout">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
							  $v = 	dttheme_option('sensei',"lesson-layout");
							  $v = !empty($v) ? $v : "content-full-width";
							  
                        foreach($layout as $key => $value):
                            $class = ( $key ==  $v ) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input name="mytheme[sensei][lesson-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Lesson Detail Page Layout End-->
					 <?php 
                     $sb_layout = dttheme_option('sensei',"lesson-layout");
                     $sidebar_both = $sidebar_left = $sidebar_right = '';
                     if($sb_layout == 'content-full-width') {
                        $sidebar_both = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-left-sidebar') {
                        $sidebar_right = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-right-sidebar') {
                        $sidebar_left = 'style="display:none;"'; 
                     } 
                     ?>
                    <div id="bpanel-widget-area-options" <?php echo 'class="lesson-layout" '.$sidebar_both;?>>
                        
                        <div id="left-sidebar-container" class="bpanel-page-left-sidebar" <?php echo $sidebar_left; ?>>
                            <!-- 2. Every Where Sidebar Left Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Left','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-left-sidebar-for-lesson-layout'); ?>
                            </div><!-- Every Where Sidebar Left End-->
                        </div>
    
                        <div id="right-sidebar-container" class="bpanel-page-right-sidebar" <?php echo $sidebar_right; ?>>
                            <!-- 3. Every Where Sidebar Right Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Right','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-right-sidebar-for-lesson-layout'); ?>
                            </div><!-- Every Where Sidebar Right End-->
                        </div>
                        
                    </div>                    
                    
                </div><!-- Lesson Page -->

				<!-- Quiz Page -->
            	<div class="box-title"><h3><?php _e('Quiz Detail','dt_themes');?></h3></div>
                <div class="box-content">
                	<!-- Quiz Detail Page Layout -->
                    <h6><?php _e('Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the Quiz page layout","dt_themes");?></p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                    	<ul class="bpanel-post-layout bpanel-layout-set" id="quiz-layout">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
							  $v = 	dttheme_option('sensei',"quiz-layout");
							  $v = !empty($v) ? $v : "content-full-width";
							  
                        foreach($layout as $key => $value):
                            $class = ( $key ==  $v ) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input name="mytheme[sensei][quiz-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Quiz Detail Page Layout End-->
					 <?php 
                     $sb_layout = dttheme_option('sensei',"quiz-layout");
                     $sidebar_both = $sidebar_left = $sidebar_right = '';
                     if($sb_layout == 'content-full-width') {
                        $sidebar_both = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-left-sidebar') {
                        $sidebar_right = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-right-sidebar') {
                        $sidebar_left = 'style="display:none;"'; 
                     } 
                     ?>
                    <div id="bpanel-widget-area-options" <?php echo 'class="quiz-layout" '.$sidebar_both;?>>
                        
                        <div id="left-sidebar-container" class="bpanel-page-left-sidebar" <?php echo $sidebar_left; ?>>
                            <!-- 2. Every Where Sidebar Left Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Left','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-left-sidebar-for-quiz-layout'); ?>
                            </div><!-- Every Where Sidebar Left End-->
                        </div>
    
                        <div id="right-sidebar-container" class="bpanel-page-right-sidebar" <?php echo $sidebar_right; ?>>
                            <!-- 3. Every Where Sidebar Right Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Right','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-right-sidebar-for-quiz-layout'); ?>
                            </div><!-- Every Where Sidebar Right End-->
                        </div>
                        
                    </div>                    
                    
                </div><!-- Quiz Page -->

				<!-- Course Category Page -->
            	<div class="box-title"><h3><?php _e('Archive Pages','dt_themes');?></h3></div>
                <div class="box-content">
                	<!-- Course Category Page Layout -->
                    <h6><?php _e('Layout','dt_themes');?></h6>
                    <p class="note no-margin"> <?php _e("Choose the archieve page layout Style","dt_themes");?></p>
                    <div class="hr_invisible"> </div>
                    <div class="bpanel-option-set">
                    	<ul class="bpanel-post-layout bpanel-layout-set" id="course-category-layout">
                        <?php $layout = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>'right-sidebar','both-sidebar'=>'both-sidebar');
							  $v = 	dttheme_option('sensei',"course-category-layout");
							  $v = !empty($v) ? $v : "content-full-width";
							  
                        foreach($layout as $key => $value):
                            $class = ( $key ==  $v ) ? " class='selected' " : "";
                            echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
                        endforeach; ?>
                        </ul>
                        <input name="mytheme[sensei][course-category-layout]" type="hidden" value="<?php echo $v;?>"/>
                    </div><!-- Course Category Page Layout End-->
					 <?php 
                     $sb_layout = dttheme_option('sensei',"course-category-layout");
                     $sidebar_both = $sidebar_left = $sidebar_right = '';
                     if($sb_layout == 'content-full-width') {
                        $sidebar_both = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-left-sidebar') {
                        $sidebar_right = 'style="display:none;"'; 
                     } elseif($sb_layout == 'with-right-sidebar') {
                        $sidebar_left = 'style="display:none;"'; 
                     } 
                     ?>
                    <div id="bpanel-widget-area-options" <?php echo 'class="course-category-layout" '.$sidebar_both;?>>
                        
                        <div id="left-sidebar-container" class="bpanel-page-left-sidebar" <?php echo $sidebar_left; ?>>
                            <!-- 2. Every Where Sidebar Left Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Left','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-left-sidebar-for-course-category-layout'); ?>
                            </div><!-- Every Where Sidebar Left End-->
                        </div>
    
                        <div id="right-sidebar-container" class="bpanel-page-right-sidebar" <?php echo $sidebar_right; ?>>
                            <!-- 3. Every Where Sidebar Right Start -->
                            <div id="page-commom-sidebar" class="bpanel-sidebar-section custom-box">
                                <h6><?php _e('Disable Sensei Course Every Where Sidebar Right','dt_themes');?></label></h6>
                                <?php dttheme_switch("",'sensei','disable-senseicourse-everywhere-right-sidebar-for-course-category-layout'); ?>
                            </div><!-- Every Where Sidebar Right End-->
                        </div>
                        
                    </div>                    
                    
                </div><!-- Course Category Page -->

<?php 	else: ?>
				<div class="box-title"><h3><?php _e('Warning','dt_themes');?></h3></div>
                <div class="box-content"><p class="note"><?php _e("You have to install and activate the Sensei plugin to use this module ..",'dt_themes');?></p></div>
<?php   endif;?>                
            </div><!--.bpanel-box End -->
        </div><!-- #my-sensei ends here -->        

    </div><!-- .bpanel-main-content ends here-->    
</div><!-- #sensei end-->