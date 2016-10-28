<?php

// =============================================================================
// VIEWS/ADMIN/TAB-SETTINGS.PHP
// -----------------------------------------------------------------------------
// Tab used for general settings and provider settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Settings Tab
// =============================================================================

// Settings Tab
// =============================================================================

?>

<div class="wrap x-plugin <?php echo $plugin_slug; ?>" id="<?php echo $plugin_slug; ?>-wrap">
  <?php $view->show( 'admin/navigation' ); ?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <form name="<?php echo $plugin_slug; ?>_form" method="post" action="">
        <input name="<?php echo $plugin_slug; ?>_form_submitted" type="hidden" value="submitted">

        <?php

        $view->show( 'admin/tab-settings-main' );
        $view->show( 'admin/tab-settings-sidebar' );

        ?>

      </form>
    </div>
    <br class="clear">
  </div>
</div>