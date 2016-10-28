<?php
/*Template Name: Login Template */
get_header();

	$tpl_default_settings = get_post_meta( $post->ID, '_tpl_default_settings', TRUE );
	$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();

	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
	
	$show_sidebar = $show_left_sidebar = $show_right_sidebar =  false;
	$sidebar_class = "";

	switch ( $page_layout ) {
		case 'with-left-sidebar':
			$page_layout = "page-with-sidebar with-left-sidebar";
			$show_sidebar = $show_left_sidebar = true;
			$sidebar_class = "secondary-has-left-sidebar";
		break;

		case 'with-right-sidebar':
			$page_layout = "page-with-sidebar with-right-sidebar";
			$show_sidebar = $show_right_sidebar	= true;
			$sidebar_class = "secondary-has-right-sidebar";
		break;

		case 'both-sidebar':
			$page_layout = "page-with-sidebar page-with-both-sidebar";
			$show_sidebar = $show_right_sidebar	= $show_left_sidebar = true;
			$sidebar_class = "secondary-has-both-sidebar";
		break;

		case 'content-full-width':
		default:
			$page_layout = "content-full-width";
		break;
	}

	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;
	
	?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
		<!-- Login Module -->
		<?php if( is_user_logged_in() ) {
				$link = dttheme_get_page_permalink_by_its_template('tpl-welcome.php');
				if ( !empty( $link )) {
					return wp_redirect( $link );
				}
			} else { ?>

				<!-- Login Form -->
				<div class="column dt-sc-one-half first">

					<div class="dt-sc-border-title"> <h2><span><?php _e('Login Form','dt_themes');?></span> </h2></div>

					<p> <strong><?php _e('Already a Member? Log in here.','dt_themes');?></strong> </p>
					<form name="loginform" id="loginform" action="<?php echo home_url();?>/wp-login.php" method="post">

						<p>
							<label><?php _e('Username','dt_themes');?><span class="required"> * </span></label>
							<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" required="required" />
						</p>

						<p>
							<label><?php _e('Password','dt_themes');?><span class="required"> * </span> </label>
							<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" required="required" />
						</p>

						<p class="forgetmenot">
							<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> <?php _e('Remember Me','dt_themes');?></label>
						</p>

						<p class="submit alignleft">
							<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php _e('Log In','dt_themes');?>" tabindex="100" />
						</p>

						<?php $link = dttheme_get_page_permalink_by_its_template('tpl-welcome.php');
							  $link = !empty( $link ) ? $link : home_url();	?>
							  <input type="hidden" name="redirect_to" value="<?php echo $link;?>" />
					</form>

					<p class="tpl-forget-pwd"><a href="<?php echo home_url(); ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?','dt_themes');?></a></p>
				</div><!-- Login Form End -->

				<!-- Registration Form -->
				<div class="column dt-sc-one-half">
                    <div class="dt-sc-border-title"> <h2><span><?php _e('Register Form','dt_themes');?></span> </h2></div>
                    
					<p> <strong><?php _e('Do not have an account? Register here','dt_themes');?></strong> </p>

					<form name="loginform" id="loginform" action="<?php echo home_url(); ?>/wp-login.php?action=register" method="post">
						<p>	
							<label><?php _e('Username','dt_themes');?><span class="required"> * </span> </label> 
							<input type="text" name="user_login"  class="input" value="" size="20" required="required" />
						</p>
						<p>
							<label><?php _e('Email Id','dt_themes');?><span class="required"> * </span> </label> 
							<input type="email" name="user_email"  class="input" value="" size="20" required="required" />
						</p>
						<p>
							<label><?php _e('Role','dt_themes');?><span class="required"> * </span> </label> 
                            <select name="role" id="role">
                                <option value="subscriber"><?php echo __('Subscriber', 'dt_themes'); ?></option>
                                <option value="teacher"><?php echo __('Teacher', 'dt_themes'); ?></option>
                                <?php 
								$status = dttheme_is_plugin_active('s2member/s2member.php');
								if($status) {
								?>
                                    <option value="s2member_level1"><?php echo __('Student', 'dt_themes'); ?></option>
                                <?php
								}
								?>
                            </select>
						</p>
						<p class="submit alignleft"><input type="submit" class="button-primary" value="<?php _e('Register','dt_themes');?>" /></p>
					</form>
				</div><!-- Registration Form End -->
				<div class="clear"></div>
		<?php }?>

		<!-- Login Module End-->

		<?php
		if( have_posts() ):
			while( have_posts() ):
				the_post();
				get_template_part( 'framework/loops/content', 'page' );
			endwhile;
		endif;?>
	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
    
<?php get_footer(); ?>