<?php 
	$dt_bp_data = dttheme_option("bp");
	$dt_bp_data = is_array( $dt_bp_data ) ? $dt_bp_data  : array();
	$dt_per_page = array_key_exists("members-per-page", $dt_bp_data) ? dttheme_wp_kses($dt_bp_data["members-per-page"]) : 10;
	$dt_per_page - intval($dt_per_page);?>

<?php do_action( 'bp_before_members_loop' ); ?>
<?php $columns = $post_class = "";
	$members_page_layout = dttheme_option('bp',"members-page-layout");
	switch($members_page_layout) {
		case "one-half-column":		$columns = 2; $post_class = "column dt-sc-one-half"; 	break;
	  	case "one-third-column":	$columns = 3; $post_class = "column dt-sc-one-third";	break;
	  	case "one-fourth-column":	$columns = 4; $post_class = "column dt-sc-one-fourth";	break;				
	  	default:					$columns = 4; $post_class = "column dt-sc-one-fourth";	break;
	}?>
<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) .'&per_page='.$dt_per_page ) ) :  $i = 1;?>
	<?php do_action( 'bp_before_directory_members_list' ); ?>
		<?php while ( bp_members() ) : 
				bp_the_member();

				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;
 ?>
				<div class="<?php echo $temp_class;?>">
					<div class="members-list">
						<div class="item-avatar"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(array('width'=>'90','height'=>'90')); ?></a></div>
						<div class="item">
							<div class="item-title"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
							<div class="item-meta"><?php bp_member_last_active(); ?></div>
							<div class="item-body"><?php echo get_the_author_meta('description',bp_get_member_user_id());?></div>
						</div>
						<a href="<?php bp_member_permalink(); ?>" class="dt-sc-button small"><?php _e(' View Profile','dt_themes');?></a>
					</div>	
				</div>
		<?php endwhile;?>
	<?php do_action( 'bp_after_directory_members_list' ); ?>
	<?php bp_member_hidden_fields(); ?>

	<!-- Pagination -->
	<div class="pagination">
		<?php bp_members_pagination_links(); ?>
	</div><!-- End Pagination -->

<?php else: ?>
	<div id="message" class="alert-box">
		<?php _e( "Sorry, no members were found.", 'dt_themes'); ?>
	</div>
<?php endif; ?>
<?php do_action( 'bp_after_members_loop' ); ?>