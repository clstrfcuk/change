            <!-- **Header** -->
            <header id="header" class="header2">
            
                <div class="container">
                    <!-- **Logo - Start** -->
                    <div id="logo">
						<?php
                        if( dttheme_option('general', 'logo') ):
                            $url = dttheme_option('general', 'logo-url');
                            $url = !empty( $url ) ? $url : IAMD_BASE_URL."images/logo.png";
                            
                            $retina_url = dttheme_option('general','retina-logo-url');
                            $retina_url = !empty($retina_url) ? $retina_url : IAMD_BASE_URL."images/logo@2x.png";
                            
                            $width = dttheme_option('general','retina-logo-width');
                            $width = !empty($width) ? $width."px;" : "98px";
                            
                            $height = dttheme_option('general','retina-logo-height');
                            $height = !empty($height) ? $height."px;" : "99px";?>
                            <a href="<?php echo home_url();?>" title="<?php echo dttheme_blog_title();?>">
                                <img class="normal_logo" src="<?php echo esc_url($url);?>" alt="<?php echo dttheme_blog_title(); ?>" title="<?php echo dttheme_blog_title(); ?>" />
                                <img class="retina_logo" src="<?php echo esc_url($retina_url);?>" alt="<?php echo dttheme_blog_title();?>" title="<?php echo dttheme_blog_title(); ?>" style="width:<?php echo esc_attr($width);?>; height:<?php echo esc_attr($height);?>;"/>
                            </a><?php
                        else:?>
                            <h2><a href="<?php echo home_url();?>" title="<?php echo dttheme_blog_title();?>"><?php echo do_shortcode(get_option('blogname')); ?></a></h2><?php
                        endif;?>
                    </div><!-- **Logo - End** -->
            
                    <div class="header-register">                    	
                        <ul>
							<?php
                            if(!is_user_logged_in()): 
                                $login = dttheme_get_page_permalink_by_its_template('tpl-login.php'); 
                                $login = is_null($login) ? home_url()."/wp-login.php" : $login;
								?>
                                <li><a href="<?php echo $login; ?>" title="<?php _e('Login / Register Now', 'dt_themes');?>"><i class="fa fa-user"></i><?php echo __('Login', 'dt_themes').'<span> | </span>'.__('Register', 'dt_themes'); ?></a></li>
                            <?php
                            else:
                                $current_user = wp_get_current_user();
                                $welcome = dttheme_get_page_permalink_by_its_template('tpl-welcome.php');
                                if( dttheme_is_plugin_active('s2member/s2member.php') ):
                                    $login_welcome_page = get_option('ws_plugin__s2member_cache');
                                    $login_welcome_page = $login_welcome_page['login_welcome_page'];
                                    $page = $login_welcome_page['page'];
                                    if( !empty($page) ):
                                        $link = $login_welcome_page['link'];
                                        $title =  get_the_title($page);
                                        echo '<li><a href="'.$link.'">'.get_avatar( $current_user->ID, 30).'<span>'.__('Welcome, ', 'dt_themes').'&nbsp;'.$current_user->display_name.' | </span>'.'</a></li>';
									elseif(!is_null($welcome)):
										echo '<li><a href="'.$welcome.'">'.get_avatar( $current_user->ID, 30).'<span>'.__('Welcome, ', 'dt_themes').'&nbsp;'.$current_user->display_name.' | </span>'.'</a></li>';
                                    endif;
                                elseif(!is_null($welcome)):
                                    echo '<li><a href="'.$welcome.'">'.get_avatar( $current_user->ID, 30).'<span>'.__('Welcome, ', 'dt_themes').'&nbsp;'.$current_user->display_name.' | </span>'.'</a></li>';
                                else:
                                    echo '<li><a href="#">'.get_avatar( $current_user->ID, 30).'<span>'.__('Welcome, ', 'dt_themes').'&nbsp;'.$current_user->display_name.' | </span>'.'</a></li>';
                                endif;	
                                                            
                                echo '<li><a href="'.wp_logout_url(get_permalink()).'" title="'.__('Logout', 'dt_themes').'">'.__('Logout', 'dt_themes').'</a></li>';
                            endif;
							
							if(class_exists('WooCommerce') && dttheme_option('appearance', 'enable-header-cart')) {
								global $woocommerce;
								$cart_url = $woocommerce->cart->get_cart_url();
								echo '<li class="dt-sc-cart"><a href="'.$cart_url.'"><i class="fa fa-shopping-cart"></i></a></li>';
							}
                            ?>
                        </ul>
                    </div>                    
            
                    <!-- **Navigation** -->
                    <div id="primary-menu">
                    <nav id="main-menu">
                        <div class="dt-menu-toggle" id="dt-menu-toggle">
                            <?php _e('Menu','dt_themes');?>
                            <span class="dt-menu-toggle-icon"></span>
                        </div>
                    
						<?php 
						$primaryMenu = NULL;
						if( is_page_template('tpl-landingpage.php') ) {
							
							global $post;
							$lp_title = $post->post_title;
							$lp_name = str_replace(' ', '-', trim($post->post_title));
							
							if (function_exists('wp_nav_menu')) :
								$primaryMenu = wp_nav_menu(array(
											'theme_location'=>'landingpage_menu',
											'menu_id'=>'',
											'menu_class'=>'menu',
											'fallback_cb'=>'dttheme_default_navigation',
											'echo'=>false,
											'container'=>false,
											'items_wrap'      => '<ul id="%1$s" class="group %2$s"><li class="menu-item current-menu-item"><a href="#'.$lp_name.'"><i class="fa fa-home"></i>'.$lp_title.'</a></li>%3$s</ul>',
											'walker' => new DTFrontEndMenuWalker()
										));
							endif;
						
						} else {
							
							if (function_exists('wp_nav_menu')) :
								$primaryMenu = wp_nav_menu(array(
											'theme_location'=>'header_menu',
											'menu_id'=>'',
											'menu_class'=>'menu',
											'fallback_cb'=>'dttheme_default_navigation',
											'echo'=>false,
											'container'=>false,
											'walker' => new DTFrontEndMenuWalker()
										));
							endif;
							
						}
						if(!empty($primaryMenu)) echo $primaryMenu;
						?>
                    </nav><!-- **Navigation - End** -->
                    </div>
                                        
                </div>    
            </header><!-- **Header - End** -->