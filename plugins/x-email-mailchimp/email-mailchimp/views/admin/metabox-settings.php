<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-SETTINGS.PHP
// -----------------------------------------------------------------------------
// Provider email integration settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metabox
// =============================================================================

// Metabox
// =============================================================================

?>

<?php if ( $mc_api_key ) : ?>
  <p><?php _e( 'Your site is now connected to your MailChimp account!', '__x__' ); ?></p>
<?php else : ?>
  <p><?php _e( 'Your site is not yet linked to your MailChimp account.', '__x__' ); ?></p>
<?php endif; ?>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_mc_api_key'; ?>">
        <strong><?php _e( 'API key', '__x__' ); ?></strong>
        <span>
        <?php if ( $mc_api_key ) : ?>
          <?php _e( 'Your API key is valid.', '__x__' ); ?>
        <?php else : ?>
          <?php _e( 'Enter your MailChimp API key.', '__x__' ); ?>
        <?php endif; ?>
        </span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text<?php echo ( $mc_api_key ) ? ' x-input-success' : ''; ?>" name="<?php echo $plugin_slug; ?>[mc_api_key]" id="<?php echo $plugin_slug . '_mc_api_key'; ?>" value="<?php echo esc_attr( $mc_api_key ); ?>">
    </td>
  </tr>

  <?php if ( $mc_api_key ) : ?>

    <?php if ( apply_filters( 'x_email_mailchimp_skip_double_opt_in', false ) ) : ?>

      <tr>
        <th>
          <label for="<?php echo $plugin_slug . '_mc_skip_double_opt_in'; ?>">
            <strong><?php _e( 'Skip Double Opt-In', '__x__' ); ?></strong>
            <span><?php _e( 'You can skip the double opt-in process if you wish. This only applies to forms generated in this plugin.', '__x__' ); ?></span>
          </label>
        </th>
        <td>
           <fieldset>
            <legend class="screen-reader-text"><span>input type="radio"</span></legend>
            <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[mc_skip_double_opt_in]" id="<?php echo $plugin_slug . '_mc_skip_double_opt_in_yes'; ?>" <?php echo checked( ( $mc_skip_double_opt_in =='yes' ) ); ?> value="yes"> <span><?php _e( 'Yes', '__x__' ); ?></span></label><br>
            <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[mc_skip_double_opt_in]" id="<?php echo $plugin_slug . '_mc_skip_double_opt_in_no'; ?>" <?php echo checked( ( $mc_skip_double_opt_in =='no') ) ; ?> value="no"> <span><?php _e( 'No (Recommended)', '__x__' ); ?></span></label>
          </fieldset>
        </td>
      </tr>

      <tr style="display: <?php echo ( $mc_skip_double_opt_in == 'no' ) ? 'none' : 'table-row'; ?>;">
        <th>
          <label for="<?php echo $plugin_slug . '_mc_send_welcome'; ?>">
            <strong><?php _e( 'Send Confirmation', '__x__' ); ?></strong>
            <span><?php _e( 'When skipping double opt-in, you can choose whether or not to send the confirmation message.', '__x__' ); ?></span>
          </label>
        </th>
        <td>
           <fieldset>
            <legend class="screen-reader-text"><span>input type="radio"</span></legend>
            <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[mc_send_welcome]" id="<?php echo $plugin_slug . '_mc_send_welcome_yes'; ?>" <?php echo checked( ( $mc_send_welcome =='yes' ) ); ?> value="yes"> <span><?php _e( 'Yes', '__x__' ); ?></span></label><br>
            <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[mc_send_welcome]" id="<?php echo $plugin_slug . '_mc_send_welcome_no'; ?>" <?php echo checked( ( $mc_send_welcome =='no') ) ; ?> value="no"> <span><?php _e( 'No', '__x__' ); ?></span></label>
          </fieldset>
        </td>
      </tr>

    <?php endif; ?>

  <?php endif; ?>

</table>