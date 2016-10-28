<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE-MAIN.PHP
// -----------------------------------------------------------------------------
// Plugin options page main content.
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
  <div class="meta-box-sortables ui-sortable">

    <!--
    ENABLE
    -->

    <div id="meta-box-enable" class="postbox">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Enable', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select the checkbox below to enable the plugin.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_smooth_scroll_enable">
                <strong><?php _e( 'Enable Smooth Scroll', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_smooth_scroll_enable" id="x_smooth_scroll_enable" value="1" <?php echo ( isset( $x_smooth_scroll_enable ) && checked( $x_smooth_scroll_enable, '1', false ) ) ? checked( $x_smooth_scroll_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_smooth_scroll_enable ) && $x_smooth_scroll_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_smooth_scroll_step">
                <strong><?php _e( 'Step', '__x__' ); ?></strong>
                <span><?php _e( 'The speed of the scrolling effect with a mouse wheel.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <input name="x_smooth_scroll_step" id="x_smooth_scroll_step" type="number" step="1" min="0" value="<?php echo ( isset( $x_smooth_scroll_step ) ) ? $x_smooth_scroll_step : 50; ?>" class="small-text">
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_smooth_scroll_speed">
                <strong><?php _e( 'Scroll Speed', '__x__' ); ?></strong>
                <span><?php _e( 'The speed of the scrolling effect.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <input name="x_smooth_scroll_speed" id="x_smooth_scroll_speed" type="number" step="1" min="0" value="<?php echo ( isset( $x_smooth_scroll_speed ) ) ? $x_smooth_scroll_speed : 100; ?>" class="small-text">
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>