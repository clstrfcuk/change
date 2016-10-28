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
              <label for="x_video_lock_enable">
                <strong><?php _e( 'Enable Video Lock', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_enable" id="x_video_lock_enable" value="1" <?php echo ( isset( $x_video_lock_enable ) && checked( $x_video_lock_enable, '1', false ) ) ? checked( $x_video_lock_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_video_lock_enable ) && $x_video_lock_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_video_lock_delay">
                <strong><?php _e( 'Delay (s)', '__x__' ); ?></strong>
                <span><?php _e( 'Amount of time to pass (in seconds) before Video Lock should appear on the screen.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_delay" id="x_video_lock_delay" type="number" step="1" min="0" value="<?php echo ( isset( $x_video_lock_delay ) ) ? $x_video_lock_delay : '10'; ?>" class="small-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_width">
                <strong><?php _e( 'Width (px)', '__x__' ); ?></strong>
                <span><?php _e( 'Valid inputs are between 450 and 850 in increments of 10.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_width" id="x_video_lock_width" type="number" step="10" min="450" max="850" value="<?php echo ( isset( $x_video_lock_width ) ) ? $x_video_lock_width : '750'; ?>" class="small-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_heading_enable">
                <strong><?php _e( 'Enable Heading', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the Video Lock heading.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_heading_enable" id="x_video_lock_heading_enable" value="1" <?php echo ( isset( $x_video_lock_heading_enable ) && checked( $x_video_lock_heading_enable, '1', false ) ) ? checked( $x_video_lock_heading_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_heading">
                <strong><?php _e( 'Heading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in your desired heading.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_heading" id="x_video_lock_heading" type="text" value="<?php echo ( isset( $x_video_lock_heading ) ) ? $x_video_lock_heading : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_heading_color">
                <strong><?php _e( 'Heading Color', '__x__' ); ?></strong>
                <span><?php _e( 'Select your heading color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_heading_color" id="x_video_lock_heading_color" type="text" value="<?php echo ( isset( $x_video_lock_heading_color ) ) ? $x_video_lock_heading_color : '#272727'; ?>" class="wp-color-picker" data-default-color="#272727"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_subheading_enable">
                <strong><?php _e( 'Enable Subheading', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the Video Lock subheading.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_subheading_enable" id="x_video_lock_subheading_enable" value="1" <?php echo ( isset( $x_video_lock_subheading_enable ) && checked( $x_video_lock_subheading_enable, '1', false ) ) ? checked( $x_video_lock_subheading_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_subheading">
                <strong><?php _e( 'Subheading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in your desired subheading.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_subheading" id="x_video_lock_subheading" type="text" value="<?php echo ( isset( $x_video_lock_subheading ) ) ? $x_video_lock_subheading : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_subheading_color">
                <strong><?php _e( 'Subheading Color', '__x__' ); ?></strong>
                <span><?php _e( 'Select your subheading color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_subheading_color" id="x_video_lock_subheading_color" type="text" value="<?php echo ( isset( $x_video_lock_subheading_color ) ) ? $x_video_lock_subheading_color : '#999999'; ?>" class="wp-color-picker" data-default-color="#999999"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_source">
                <strong><?php _e( 'Source', '__x__' ); ?></strong>
                <span><?php _e( 'You may either self-host your video embed from a third party service.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_source" value="self-hosted" <?php echo ( isset( $x_video_lock_source ) && checked( $x_video_lock_source, 'self-hosted', false ) ) ? checked( $x_video_lock_source, 'self-hosted', false ) : 'checked="checked"'; ?>> <span><?php _e( 'Self-hosted', '__x__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_source" value="third-party" <?php echo ( isset( $x_video_lock_source ) && checked( $x_video_lock_source, 'third-party', false ) ) ? checked( $x_video_lock_source, 'third-party', false ) : ''; ?>> <span><?php _e( 'Third Party', '__x__' ); ?></span></label>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_video">
                <strong><?php _e( 'Video', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in the URL to your self-hosted video in mp4 format.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_video" id="x_video_lock_video" type="text" value="<?php echo ( isset( $x_video_lock_video ) ) ? $x_video_lock_video : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_video_poster">
                <strong><?php _e( 'Video Poster', '__x__' ); ?></strong>
                <span><?php _e( 'Select a poster image to be used as an initial image if autoplay is disabled.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_video_poster" id="x_video_lock_video_poster" type="text" value="<?php echo ( isset( $x_video_lock_video_poster ) ) ? $x_video_lock_video_poster : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_video_autoplay_enable">
                <strong><?php _e( 'Enable Video Autoplay', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable video autoplay.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_video_autoplay_enable" id="x_video_lock_video_autoplay_enable" value="1" <?php echo ( isset( $x_video_lock_video_autoplay_enable ) && checked( $x_video_lock_video_autoplay_enable, '1', false ) ) ? checked( $x_video_lock_video_autoplay_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_video_controls_disable">
                <strong><?php _e( 'Disable Video Controls', '__x__' ); ?></strong>
                <span><?php _e( 'Select to disable video controls.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_video_controls_disable" id="x_video_lock_video_controls_disable" value="1" <?php echo ( isset( $x_video_lock_video_controls_disable ) && checked( $x_video_lock_video_controls_disable, '1', false ) ) ? checked( $x_video_lock_video_controls_disable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_embed">
                <strong><?php _e( 'Embed Code', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in the embed code from your video provider. Only iframes are allowed in this input.', '__x__' ); ?></span>
              </label>
            </th>
            <td><textarea name="x_video_lock_embed" id="x_video_lock_embed" class="code"><?php echo ( isset( $x_video_lock_embed ) ) ? esc_textarea( $x_video_lock_embed ) : ''; ?></textarea>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_button_text">
                <strong><?php _e( 'Button Text', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in the text for your button.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_button_text" id="x_video_lock_button_text" type="text" value="<?php echo ( isset( $x_video_lock_button_text ) ) ? $x_video_lock_button_text : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_button_link">
                <strong><?php _e( 'Button Link', '__x__' ); ?></strong>
                <span><?php _e( 'Enter in the URL for where you would like your button to go.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_button_link" id="x_video_lock_button_link" type="text" value="<?php echo ( isset( $x_video_lock_button_link ) ) ? $x_video_lock_button_link : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_button_style">
                <strong><?php _e( 'Button Style', '__x__' ); ?></strong>
                <span><?php _e( 'Choose between your global button style or three pre-made marketing buttons.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_button_style" value="global" <?php echo ( isset( $x_video_lock_button_style ) && checked( $x_video_lock_button_style, 'global', false ) ) ? checked( $x_video_lock_button_style, 'global', false ) : 'checked="checked"'; ?>> <span><?php _e( 'Global', '__x__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_button_style" value="marketing-red" <?php echo ( isset( $x_video_lock_button_style ) && checked( $x_video_lock_button_style, 'marketing-red', false ) ) ? checked( $x_video_lock_button_style, 'marketing-red', false ) : ''; ?>> <span><?php _e( 'Marketing &ndash; Red', '__x__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_button_style" value="marketing-yellow" <?php echo ( isset( $x_video_lock_button_style ) && checked( $x_video_lock_button_style, 'marketing-yellow', false ) ) ? checked( $x_video_lock_button_style, 'marketing-yellow', false ) : ''; ?>> <span><?php _e( 'Marketing &ndash; Yellow', '__x__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="x_video_lock_button_style" value="marketing-green" <?php echo ( isset( $x_video_lock_button_style ) && checked( $x_video_lock_button_style, 'marketing-green', false ) ) ? checked( $x_video_lock_button_style, 'marketing-green', false ) : ''; ?>> <span><?php _e( 'Marketing &ndash; Green', '__x__' ); ?></span></label>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_button_delay">
                <strong><?php _e( 'Button Delay (s)', '__x__' ); ?></strong>
                <span><?php _e( 'Amount of time to pass (in seconds) after Video Lock appears before the button should appear.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_video_lock_button_delay" id="x_video_lock_button_delay" type="number" step="1" min="0" value="<?php echo ( isset( $x_video_lock_button_delay ) ) ? $x_video_lock_button_delay : '5'; ?>" class="small-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_close_enable">
                <strong><?php _e( 'Enable Close Button', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the Video Lock close button, allowing users to dismiss the plugin once it appears.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_video_lock_close_enable" id="x_video_lock_close_enable" value="1" <?php echo ( isset( $x_video_lock_close_enable ) && checked( $x_video_lock_close_enable, '1', false ) ) ? checked( $x_video_lock_close_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_video_lock_entries_include">
                <strong><?php _e( 'Include', '__x__' ); ?></strong>
                <span><?php _e( 'Select the pages or posts that you want Video Lock to appear on.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <select name="x_video_lock_entries_include[]" id="x_video_lock_entries_include" multiple="multiple">
                <?php
                foreach ( $x_video_lock_list_entries_master as $key => $value ) {
                  if ( in_array( $key, $x_video_lock_entries_include ) ) {
                    $selected = ' selected="selected"';
                  } else {
                    $selected = '';
                  }
                  echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                }
                ?>
              </select>
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>