<?php

// =============================================================================
// VIEWS/ADMIN/WIDGET-SUBSCRIBE.PHP
// -----------------------------------------------------------------------------
// Displays the Subscribe widget form in the WordPress admin.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Admin Widget Form
// =============================================================================

// Admin Widget Form
// =============================================================================

?>

<p>
  <label for="<?php echo $title_id; ?>"><?php _e( 'Title', '__x__' ); ?></label>
  <input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $title_name; ?>" type="text" value="<?php echo esc_attr( $title_value ); ?>">
</p>

<p>
  <label for="<?php echo $form_select_id; ?>"><?php _e( 'Choose a Form', '__x__' ); ?></label>
  <?php $no_forms = ( empty( $email_forms ) ); ?>
  <select class="select widefat" name="<?php echo $form_select_name; ?>" id="<?php echo $form_select_id; ?>" <?php if($no_forms) : ?>disabled<?php endif; ?>>
    <?php if ( $no_forms ) : ?>
      <option><?php _e( 'No Forms Found', '__x__' ); ?></option>
    <?php else : foreach( $email_forms as $form_id => $form_title ) : ?>
      <option value="<?php echo $form_id; ?>" <?php echo ( $form_id == $form_select_value ) ? 'selected' : ''; ?> ><?php echo $form_title; ?></option>
    <?php endforeach; endif; ?>
  </select>
</p>