<?php

// =============================================================================
// VIEWS/ADMIN/EMAIL-FORMS-TAB.PHP
// -----------------------------------------------------------------------------
// Email Forms Table
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Email Forms Table
// =============================================================================

// Email Forms Table
// =============================================================================

?>

<div class="wrap x-plugin <?php echo $plugin_slug; ?>" id="<?php echo $plugin_slug; ?>-wrap">
  <?php $view->show( 'admin/navigation' ); ?>
  <?php echo $email_forms_table->get_messages(); ?>
  <h2><?php _e( 'Email Forms', '__x__' );?> <a href="<?php echo admin_url( 'post-new.php?post_type=x-email-forms' ); ?>" class="add-new-h2"><?php _e( 'Add New Form', '__x__' );?></a></h2>
  <?php echo $email_forms_table->render(); ?>
</div>