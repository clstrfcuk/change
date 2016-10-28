<?php

// =============================================================================
// VIEWS/ADMIN/TAB-SETTINGS-SIDEBAR.PHP
// -----------------------------------------------------------------------------
// Save button and about items used for general settings and provider settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Sidebar
// =============================================================================

// Sidebar
// =============================================================================

?>

<div id="postbox-container-1" class="postbox-container">
  <div class="meta-box-sortables">

    <!--
    SAVE
    -->

    <div class="postbox">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Save', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Once you are satisfied with your settings, click the button below to save them.','__x__'); ?></p>
        <p class="cf"><input id="submit" class="button button-primary" type="submit" name="<?php echo $plugin_slug . '_submit';?>" value="Update"></p>
      </div>
    </div>

    <?php if ( isset( $about_items ) && ! empty( $about_items ) ) : ?>

      <!--
      ABOUT
      -->

      <div class="postbox">
        <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
        <h3 class="hndle"><span><?php _e( 'About', '__x__' ); ?></span></h3>
        <div class="inside">
          <dl class="accordion">

            <?php foreach ( $about_items as $item ) : ?>

              <dt class="toggle"><?php echo $item['title']; ?></dt>
              <dd class="panel">
                <div class="panel-inner">
                  <?php echo $item['content']; ?>
                </div>
              </dd>

            <?php endforeach; ?>

          </dl>
        </div>
      </div>

    <?php endif; ?>

  </div>
</div>