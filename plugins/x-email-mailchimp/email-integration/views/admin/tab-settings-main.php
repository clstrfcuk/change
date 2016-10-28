<?php

// =============================================================================
// VIEWS/ADMIN/TAB-SETTINGS-MAIN.PHP
// -----------------------------------------------------------------------------
// Main content used for general settings and provider settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Main Content
// =============================================================================

// Main Content
// =============================================================================

?>

<div id="post-body-content">

  <?php if ( isset( $meta_boxes ) && ! empty( $meta_boxes ) ) : ?>

    <div class="meta-box-sortables ui-sortable">

      <?php foreach ( $meta_boxes as $meta_box ) : ?>

        <div id="meta-box-settings" class="postbox">
          <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
          <h3 class="hndle"><span><?php echo $meta_box['title']; ?></span></h3>
          <div class="inside">
            <?php echo $meta_box['content']; ?>
          </div>
        </div>

      <?php endforeach; ?>

    </div>

  <?php endif; ?>

</div>