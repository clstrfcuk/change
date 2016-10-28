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
              <label for="x_google_analytics_enable">
                <strong><?php _e( 'Enable Google Analytics', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_google_analytics_enable" id="x_google_analytics_enable" value="1" <?php checked( ! isset( $x_google_analytics_enable ) ? '0' : $x_google_analytics_enable, '1', true ); ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_google_analytics_enable ) && $x_google_analytics_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_google_analytics_position">
                <strong><?php _e( 'Position', '__x__' ); ?></strong>
                <span><?php _e( 'Choose which section of your site you want your Google Analytics code to be output.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                <label class="radio-label"><input type="radio" class="radio" name="x_google_analytics_position" value="head" <?php echo ( isset( $x_google_analytics_position ) && checked( $x_google_analytics_position, 'head', false ) ) ? checked( $x_google_analytics_position, 'head', false ) : 'checked="checked"'; ?>> <span><?php _e( 'Head', '__x__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="x_google_analytics_position" value="footer" <?php echo ( isset( $x_google_analytics_position ) && checked( $x_google_analytics_position, 'footer', false ) ) ? checked( $x_google_analytics_position, 'footer', false ) : ''; ?>> <span><?php _e( 'Footer', '__x__' ); ?></span></label>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_google_analytics_code">
                <strong><?php _e( 'Analytics Code', '__x__' ); ?></strong>
                <span><?php _e( 'Input your Google Analytics code exactly as it is provided (i.e. with the &lt;script&gt; tags included).', '__x__' ); ?></span>
              </label>
            </th>
            <td><textarea name="x_google_analytics_code" id="x_google_analytics_code" class="code"><?php echo ( isset( $x_google_analytics_code ) ) ? esc_textarea( $x_google_analytics_code ) : ''; ?></textarea>
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>