<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-GENERAL.PHP
// -----------------------------------------------------------------------------
// General email integration settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metabox
// =============================================================================

// Metabox
// =============================================================================

?>

<p>
  <?php _e( 'WordPress integration settings that function independently of your email provider.', '__x__' ); ?>
  <?php if ( empty( $master_list ) ) : ?>
    <strong><?php _e( 'It looks like you don\'t have any lists available. You\'ll need to connect your email provider and ensure a list exists.', '__x__' ); ?></strong> 
  <?php endif; ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_opt_in_new_users'; ?>">
        <strong><?php _e( 'Opt-In New Users', '__x__' ); ?></strong>
        <span><?php _e( 'Automatically subscribe newly registered users to a list.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <fieldset>
        <legend class="screen-reader-text"><span>input type="radio"</span></legend>
        <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[opt_in_new_users]" <?php echo checked( ( $opt_in_new_users == 'yes' ) ); ?> value="yes"> <span><?php _e( 'Yes', '__x__' ); ?></span></label><br>
        <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[opt_in_new_users]" <?php echo checked( ( $opt_in_new_users == 'no' ) ); ?> value="no"> <span><?php _e( 'No', '__x__' ); ?></span></label>
      </fieldset>
    </td>
  </tr>

  <tr style="display: <?php echo ( $opt_in_new_users == 'no' ) ? 'none' : 'table-row'; ?>;">
    <th>
      <label for="<?php echo $plugin_slug . '_opt_in_new_users_list'; ?>">
        <strong><?php _e( 'Opt-In New Users List', '__x__' ); ?></strong>
        <span><?php _e( 'Select the list that you would like your new users to be subscribed to.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" name="<?php echo $plugin_slug; ?>[opt_in_new_users_list]" id="<?php echo $plugin_slug . '_opt_in_new_users_list'; ?>">
        <?php if ( empty( $master_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <?php foreach ( $master_list as $list_item ) : ?>
            <?php $value = $list_item['provider'] . '_' . $list_item['id']; ?>
            <option value="<?php echo $value; ?>" <?php echo ( $value == $opt_in_new_users_list ) ? 'selected' : ''; ?>>
              <?php echo $list_item['name'] . ' (' . $list_item['provider_title'] . ')'; ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_mc_skip_double_opt_in'; ?>">
        <strong><?php _e( 'Default Form', '__x__' ); ?></strong>
        <span><?php _e( 'Select a form to be used if an ID is not specified in the shortcode.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" name="<?php echo $plugin_slug; ?>[default_form]" id="<?php echo $plugin_slug . '_default_form'; ?>"<?php if ( empty( $email_forms ) ) : ?> disabled<?php endif; ?>>
        <?php if ( empty( $email_forms ) ) : ?>
          <option><?php _e( 'No Forms Found', '__x__' ); ?></option>
        <?php else : foreach( $email_forms as $form_id => $form_title ) : ?>
          <option value="<?php echo $form_id; ?>" <?php echo ( $form_id == $default_form ) ? 'selected' : ''; ?>><?php echo $form_title; ?></option>
        <?php endforeach; endif; ?>
      </select>
    </td>
  </tr>

</table>