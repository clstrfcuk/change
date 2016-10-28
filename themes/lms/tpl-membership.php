<?php /*Template Name: Membership Template*/?>
<?php get_header();

	$tpl_default_settings = get_post_meta( $post->ID, '_tpl_default_settings', TRUE );
	$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();

	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";

	$show_sidebar = $show_left_sidebar = $show_right_sidebar =  false;
	$sidebar_class = $thumbnail_sidebar = $post_thumbnail = "";

	switch ( $page_layout ) {
		case 'with-left-sidebar':
			$page_layout = "page-with-sidebar with-left-sidebar";
			$show_sidebar = $show_left_sidebar = true;
			$sidebar_class = "secondary-has-left-sidebar";
			$thumbnail_sidebar = "-single-sidebar";
		break;

		case 'with-right-sidebar':
			$page_layout = "page-with-sidebar with-right-sidebar";
			$show_sidebar = $show_right_sidebar	= true;
			$sidebar_class = "secondary-has-right-sidebar";
			$thumbnail_sidebar = "-single-sidebar";
		break;

		case 'both-sidebar':
			$page_layout = "page-with-sidebar page-with-both-sidebar";
			$show_sidebar = $show_right_sidebar	= $show_left_sidebar = true;
			$sidebar_class = "secondary-has-both-sidebar";
			$thumbnail_sidebar = "-both-sidebar";
		break;

		case 'content-full-width':
		default:
			$page_layout = "content-full-width";
			$thumbnail_sidebar = "";
		break;
	}

	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
		<?php
		if( have_posts() ):
			while( have_posts() ):
				the_post();
				get_template_part( 'framework/loops/content', 'page' );
			endwhile;
		endif;
		
		if(dttheme_is_plugin_active('s2member/s2member.php')) {
		
			$terms_arr = array('D' => 'Day(s)', 'W' => 'Week(s)', 'M' => 'Month(s)', 'Y' => 'Year(s)', 'L' => 'Lifetime');
	
			if(isset($_REQUEST['paymenttype']) && $_REQUEST['paymenttype'] != '') {
				
				$paymenttype = $_REQUEST['paymenttype'];
				$level = $_REQUEST['level'];
				
				if(dttheme_option('dt_course','s2member-'.$level.'-description') != '') $description = dttheme_option('dt_course','s2member-'.$level.'-description');
				else $description = '';
				
				if(dttheme_option('dt_course','s2member-'.$level.'-period') != '') $period = dttheme_option('dt_course','s2member-'.$level.'-period');
				else $period = '';
				
				if(dttheme_option('dt_course','s2member-'.$level.'-term') != '') $term = dttheme_option('dt_course','s2member-'.$level.'-term');
				else $term = '';
				
				if(dttheme_option('dt_course','s2member-'.$level.'-price') != '') $price = dttheme_option('dt_course','s2member-'.$level.'-price');
				else $price = '';
				
				if($period != '' && $term != '' && $price != '') {	
					
					if($term == 'L') $period_label = ''; else $period_label = $period.' ';	
					$time = $period_label.$terms_arr[$term];
					
					if(dttheme_option('dt_course','currency-position') == 'after-price') $price_label = $price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
					else $price_label = dttheme_wp_kses(dttheme_option('dt_course','currency')).$price; 
						
					if(dttheme_option('dt_course','currency-s2member') != '') $currency = dttheme_option('dt_course','currency-s2member');
					else $currency = 'USD';
					
					$time_label = '<span>'.$time.' - '.$price_label.'</span>';
					echo '<div class="membership-description"><h4><strong>'.do_shortcode('[s2Get constant="S2MEMBER_LEVEL'.$level.'_LABEL"/]').'</strong> ('.__('s2Member Level', 'dt_themes').' '.$level.')</h4>
						  <h5>'.sprintf( __('You are about to purchase this level, which have access to all courses for %1$s', 'dt_themes'), $time_label  ).'</h5></div>';					
	
					$s2member_payment_url = '';
					
					if($paymenttype == 'stripe') {
	
						$s2member_payment_url = do_shortcode('[s2Member-Pro-Stripe-Form level="'.$level.'" ccaps="" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
	
					} else if($paymenttype == 'authnet') {
	
						$s2member_payment_url = do_shortcode('[s2Member-Pro-AuthNet-Form level="'.$level.'" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
	
					} else if($paymenttype == 'clickbank') {
	
						$cb_productno = dttheme_option('dt_course','s2member-cb-productno');
						$cb_skin = dttheme_option('dt_course','s2member-cb-skin');
						$cb_flowid = dttheme_option('dt_course','s2member-cb-flowid');
						
						$s2member_payment_url = do_shortcode('[s2Member-Pro-ClickBank-Button cbp="'.$cb_productno.'" cbskin="'.$cb_skin.'" cbfid="'.$cb_flowid.'" cbur="" cbf="auto" level="'.$level.'" ccaps="" desc="'.$description.'" custom="'.$_SERVER["HTTP_HOST"].'" rp="'.$period.'" rt="'.$term.'" rr="0" image="default" output="anchor" /]');
	
					} else if($paymenttype == 'paypal') {
	
						$s2member_payment_url = do_shortcode('[s2Member-Pro-PayPal-Form level="'.$level.'" ccaps="" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="2" image="" output="url"/]');
	
					} else if($paymenttype == 'paypal-default') {
	
						$s2member_payment_url = do_shortcode('[s2Member-PayPal-Button level="'.$level.'" ccaps="" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="2" image="" output="url"/]');		
	
					}
					
					echo do_shortcode($s2member_payment_url);
					
				}
				
				
			} else {
				
				$payment_icon = array('stripe' => 'fa-cc-stripe', 'authnet' => 'fa-cc-visa', 'clickbank' => 'fa-bank', 'paypal' => 'fa-cc-paypal');
		
				$courseid = isset($_REQUEST['courseid']) ? $_REQUEST['courseid'] : '';
	
				if($courseid != '') {
	
					$course_title = get_the_title($courseid);
					
					if(!current_user_can('access_s2member_ccap_cid_'.$courseid)) {
					
												
						$description = (dttheme_option('dt_course','s2member-1-description') != '') ? dttheme_option('dt_course','s2member-1-description') : __('You are about to purchase the Course : ', 'dt_themes').$course_title;
						$period = (dttheme_option('dt_course','s2member-1-period') != '') ? dttheme_option('dt_course','s2member-1-period') : 1;
						$term = (dttheme_option('dt_course','s2member-1-term') != '') ? dttheme_option('dt_course','s2member-1-term') : 'L';
						
						if($term == 'L') $period_label = ''; else $period_label = $period.' ';	
						$time = $period_label.$terms_arr[$term];
						
						$price = dttheme_wp_kses(get_post_meta($courseid, 'starting-price', true));
						if(dttheme_option('dt_course','currency-position') == 'after-price') $price_label = $price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
						else $price_label = dttheme_wp_kses(dttheme_option('dt_course','currency')).$price; 
							
						if(dttheme_option('dt_course','currency-s2member') != '') $currency = dttheme_option('dt_course','currency-s2member');
						else $currency = 'USD';
									
						$s2member_stripe_url = $s2member_authnet_url = $s2member_clickbank_url = $s2member_paypal_pro_url = $s2member_paypal_url = '';
						
						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["pro_gateways_seen"] == 1) {
							
							$payments_selected = dttheme_option('dt_course', 'payments-selected');
							$payments_selected = !empty($payments_selected) ? $payments_selected : array();
							
							if(!empty($payments_selected)) {
								
								if(in_array('stripe', $payments_selected)) {
									$s2member_stripe_url = do_shortcode('[s2Member-Pro-Stripe-Form level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
								} 
								
								if(in_array('authnet', $payments_selected)) {
									$s2member_authnet_url = do_shortcode('[s2Member-Pro-AuthNet-Form level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
								} 
								
								if(in_array('clickbank', $payments_selected)) {
									$cb_productno = dttheme_option('dt_course','s2member-cb-productno');
									$cb_skin = dttheme_option('dt_course','s2member-cb-skin');
									$cb_flowid = dttheme_option('dt_course','s2member-cb-flowid');
									
									$s2member_clickbank_url = do_shortcode('[s2Member-Pro-ClickBank-Button cbp="'.$cb_productno.'" cbskin="'.$cb_skin.'" cbfid="'.$cb_flowid.'" cbur="" cbf="auto" level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" custom="'.$_SERVER["HTTP_HOST"].'" rp="'.$period.'" rt="'.$term.'" rr="0" image="default" output="anchor" /]');
								} 
								
								if(in_array('paypal', $payments_selected)) {
									$s2member_paypal_pro_url = do_shortcode('[s2Member-Pro-PayPal-Form level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="2" image="" output="url"/]');
								}
								
							} else {
								
								$s2member_paypal_url = do_shortcode('[s2Member-PayPal-Button level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="1" image="" output="url"/]');
									
							}
							
						} else {
							
							$s2member_paypal_url = do_shortcode('[s2Member-PayPal-Button level="1" ccaps="cid_'.$courseid.'" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="1" image="" output="url"/]');
								
						}
						
						$time_label = '<span>'.$time.' - '.$price_label.'</span>';
						echo '<div class="membership-description"><h4><strong>'.do_shortcode('[s2Get constant="S2MEMBER_LEVEL1_LABEL"/]').'</strong> ('.__('s2Member Level 1', 'dt_themes').') </h4>
							  <h5>'.sprintf( __('Purchase course %1$s for %2$s', 'dt_themes'), '<strong>'.$course_title.'</strong>', $time_label  ).'</h5></div>';					
						
						if($s2member_stripe_url != '') {
							echo '<div class="dt-sc-toggle-frame pro-payments">
									<h5 class="dt-sc-toggle">
										<a href="#"><i class="fa '.$payment_icon['stripe'].'"></i>'.__('Pay Using Stripe', 'dt_themes').'</a>
									</h5>
									<div style="display: none;" class="dt-sc-toggle-content">
										<div class="block">
											'.$s2member_stripe_url.'
										</div>
									</div>
								</div>';
						}
						
						if($s2member_authnet_url != '') {
							echo '<div class="dt-sc-toggle-frame pro-payments">
									<h5 class="dt-sc-toggle">
										<a href="#"><i class="fa '.$payment_icon['authnet'].'"></i>'.__('Pay Using Auth Net', 'dt_themes').'</a>
									</h5>
									<div style="display: none;" class="dt-sc-toggle-content">
										<div class="block">
											'.$s2member_authnet_url.'
										</div>
									</div>
								</div>';
						}
						
						if($s2member_clickbank_url != '') {
							echo '<div class="dt-sc-toggle-frame pro-payments">
									<h5 class="dt-sc-toggle">
										<a href="#"><i class="fa '.$payment_icon['clickbank'].'"></i>'.__('Pay Using Click Bank', 'dt_themes').'</a>
									</h5>
									<div style="display: none;" class="dt-sc-toggle-content">
										<div class="block">
											'.$s2member_clickbank_url.'
										</div>
									</div>
								</div>';
						}
						
						if($s2member_paypal_pro_url != '') {	
							echo '<div class="dt-sc-toggle-frame pro-payments">
									<h5 class="dt-sc-toggle">
										<a href="#"><i class="fa '.$payment_icon['paypal'].'"></i>'.__('Pay Using PayPal', 'dt_themes').'</a>
									</h5>
									<div style="display: none;" class="dt-sc-toggle-content">
										<div class="block">
											'.$s2member_paypal_pro_url.'
										</div>
									</div>
								</div>';
						} else {
							echo '<div class="dt-sc-toggle-frame pro-payments">
									<h5 class="dt-sc-toggle">
										<a href="#"><i class="fa fa-paypal"></i>'.__('Pay Using PayPal', 'dt_themes').'</a>
									</h5>
									<div style="display: none;" class="dt-sc-toggle-content">
										<div class="block">
											<a target="_self" class="dt-sc-button small" href="'.$s2member_paypal_url.'"><i class="fa fa-shopping-cart"></i> '.$price_label.' - '.__('Purchase Now','dt_themes').'</a>
										</div>
									</div>
								</div>';
						}
						
						echo '<div class="dt-sc-hr-invisible"></div><div class="dt-sc-hr-invisible"></div>';
							
					} else {
						echo '<div class="dt-sc-info-box">'.sprintf( __('You have purchased %1$s course already!', 'dt_themes'), '<strong>'.$course_title.'</strong>' ).'</div><div class="dt-sc-hr-invisible"></div>';
					}
	
				}
				
				$membership_page_link = dttheme_get_page_permalink_by_its_template('tpl-membership.php');
				
				for ($n = 2; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++) { 
				
					if(dttheme_option('dt_course','s2member-'.$n.'-description') != '') $description = dttheme_option('dt_course','s2member-'.$n.'-description');
					else $description = '';
					
					if(dttheme_option('dt_course','s2member-'.$n.'-period') != '') $period = dttheme_option('dt_course','s2member-'.$n.'-period');
					else $period = '';
					
					if(dttheme_option('dt_course','s2member-'.$n.'-term') != '') $term = dttheme_option('dt_course','s2member-'.$n.'-term');
					else $term = '';
					
					if(dttheme_option('dt_course','s2member-'.$n.'-price') != '') $price = dttheme_option('dt_course','s2member-'.$n.'-price');
					else $price = '';
					
					if($period != '' && $term != '' && $price != '') {
						
						if($term == 'L') $period_label = ''; else $period_label = $period.' ';	
						$time = $period_label.$terms_arr[$term];
						
						if(dttheme_option('dt_course','currency-position') == 'after-price') $price_label = $price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
						else $price_label = dttheme_wp_kses(dttheme_option('dt_course','currency')).$price; 
	
						if(dttheme_option('dt_course','currency-s2member') != '') $currency = dttheme_option('dt_course','currency-s2member');
						else $currency = 'USD';
	
						$s2member_paypal_url = do_shortcode('[s2Member-PayPal-Button level="'.$n.'" ccaps="" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="1" image="" output="url"/]');	
						
						$payment_urls = '';
						
						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["pro_gateways_seen"] == 1) {
						
							$payments_selected = dttheme_option('dt_course', 'payments-selected');
							$payments_selected = !empty($payments_selected) ? $payments_selected : array();
							
							if(!empty($payments_selected)) {
							
								foreach ($payments_selected as $payment_type) {
									
									if($payment_type == 'clickbank') {
										$cb_productno = dttheme_option('dt_course','s2member-cb-productno');
										$cb_skin = dttheme_option('dt_course','s2member-cb-skin');
										$cb_flowid = dttheme_option('dt_course','s2member-cb-flowid');
									} else {
										$cb_productno = $cb_skin = $cb_flowid = '';
									}
								
									$payment_urls .= '<a href="'.$membership_page_link.'?paymenttype='.$payment_type.'&level='.$n.'" style="margin-left:10px;" class="dt-sc-button small"><i class="fa '.$payment_icon[$payment_type].'"></i> '.__('Pay Using ', 'dt_themes').ucfirst($payment_type).'</a>'."&nbsp;&nbsp;";
								
								}
							
							} else {
							
								$payment_urls .= '<a target="_self" class="dt-sc-button small" href="'.$s2member_paypal_url.'"><i class="fa fa-paypal"></i> '.$price_label.' - '.__('Purchase Now','dt_themes').'</a>';
							
							}
								
						} else {
							
							$payment_urls .= '<a target="_self" class="dt-sc-button small" href="'.$s2member_paypal_url.'"><i class="fa fa-paypal"></i> '.$price_label.' - '.__('Purchase Now','dt_themes').'</a>';
								
						}
						
						if($payment_urls != '') {
							
							$time_period = '<span>'.$time.' - '.$price_label.'</span>';
							echo '<div class="dt-sc-callout-box membership-box type5">
									<div class="column dt-sc-one-column">
										<h4><strong>'.do_shortcode('[s2Get constant="S2MEMBER_LEVEL'.$n.'_LABEL"/]').'</strong> ('.__('s2Member Level', 'dt_themes').' '.$n.')</h4>
										<h5>'.sprintf( __('Purchase this level to have access to all courses for %1$s', 'dt_themes'), $time_period ).'</h5>
									</div>
									<div class="dt-sc-hr-invisible-small"></div>
									<div class="column">
										'.$payment_urls.'
									</div>
								</div>';
								
						}
					
					}
		
				}
			
			
			}
			
		} else {
			echo '<div class="dt-sc-info-box">'.__('s2Member plugin is not active please activate to continue!', 'dt_themes').'</div>';
		}
		
		?>

	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>