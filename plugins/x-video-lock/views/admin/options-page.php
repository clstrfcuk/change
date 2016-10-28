<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE.PHP
// -----------------------------------------------------------------------------
// Plugin options page.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Permissions Check
//   02. Require Options
//   03. Options Page Output
// =============================================================================

// Permissions Check
// =============================================================================

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( 'You do not have sufficient permissions to access this page.' );
}



// Require Options
// =============================================================================

require( X_VIDEO_LOCK_PATH . '/functions/options.php' );



// Options Page Output
// =============================================================================

//
// Setup array of all pages and posts.
//

$x_video_lock_list_entries_args   = array( 'posts_per_page' => -1 );
$x_video_lock_list_entries_merge  = array_merge( get_pages( $x_video_lock_list_entries_args ), get_posts( $x_video_lock_list_entries_args ) );
$x_video_lock_list_entries_master = array();

foreach ( $x_video_lock_list_entries_merge as $post ) {
  $x_video_lock_list_entries_master[$post->ID] = $post->post_title;
}

asort( $x_video_lock_list_entries_master );


//
// Check if variables are set to prevent notices. Variables are set after the
// first submission of data.
//

$x_video_lock_entries_include = ( isset( $x_video_lock_entries_include ) ) ? $x_video_lock_entries_include : array();

?>

<div class="wrap x-plugin x-video-lock">
  <h2><?php _e( 'Video Lock', '__x__' ); ?></h2>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <form name="x_video_lock_form" method="post" action="">
        <input name="x_video_lock_form_submitted" type="hidden" value="submitted">

        <?php require( 'options-page-main.php' ); ?>
        <?php require( 'options-page-sidebar.php' ); ?>

      </form>
    </div>
    <br class="clear">
  </div>
</div>