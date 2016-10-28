<?php

// =============================================================================
// SHORTCODE-X-SUBSCRIBE.PHP
// -----------------------------------------------------------------------------
// Shortcode output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Form Output
//   02. Shortcode Setup
//   03. Shortcode Output
// =============================================================================

// Form Output
// =============================================================================

//
// Confirmation data.
//

if ( $confirmation_type == 'Message' ) {
  $confirmation_data = 'data-x-email-message="' . $confirmation_message . '"';
} else if ( $confirmation_type == 'Redirect' ) {
  $confirmation_data = 'data-x-email-redirect="' . $confirmation_redirect . '"';
}


//
// Button style.
//

if ( $custom_styling == 'Yes' ) {
  switch ( $button_style ) {
    case '3D' :
      $style = ' x-btn x-btn-real';
      break;
    case 'Flat' :
      $style = ' x-btn x-btn-flat';
      break;
    case 'Transparent' :
      $style = ' x-btn x-btn-transparent';
      break;
  }
} else {
  $style = '';
}


//
// Button shape.
//

if ( $custom_styling == 'Yes' ) {
  switch ( $button_shape ) {
    case 'Square' :
      $shape = ' x-btn-square';
      break;
    case 'Rounded' :
      $shape = ' x-btn-rounded';
      break;
    case 'Pill' :
      $shape = ' x-btn-pill';
      break;
  }
} else {
  $shape = '';
}


//
// Button colors.
//

switch ( $button_style ) {
  case '3D' :
    $button_colors       = 'color: ' . $button_text_color . '; border-color: ' . $button_border_color . '; background-color: ' . $button_bg_color . '; box-shadow: 0 0.25em 0 0 ' . $button_bottom_color . ', 0 4px 9px rgba(0, 0, 0, 0.75);';
    $button_colors_hover = 'color: ' . $button_text_color_hover . '; border-color: ' . $button_border_color_hover . '; background-color: ' . $button_bg_color_hover . '; box-shadow: 0 0.25em 0 0 ' . $button_bottom_color_hover . ', 0 4px 9px rgba(0, 0, 0, 0.75);';
    break;
  case 'Flat' :
    $button_colors       = 'color: ' . $button_text_color . '; border-color: ' . $button_border_color . '; background-color: ' . $button_bg_color . ';';
    $button_colors_hover = 'color: ' . $button_text_color_hover . '; border-color: ' . $button_border_color_hover . '; background-color: ' . $button_bg_color_hover . ';';
    break;
  case 'Transparent' :
    $button_colors       = 'color: ' . $button_text_color . '; border-color: ' . $button_border_color . ';';
    $button_colors_hover = 'color: ' . $button_text_color_hover . '; border-color: ' . $button_border_color_hover . ';';
    break;
}



ob_start();

?>

<form method="post" id="x-subscribe-form-<?php echo $form_id; ?>" class="x-subscribe-form x-subscribe-form-<?php echo $form_id; ?> center-block mvn" data-x-email-confirm="<?php echo $confirmation_type; ?>" <?php echo $confirmation_data; ?> style="max-width: <?php echo $max_width; ?>; font-size: <?php echo $font_size; ?>;">

  <?php if ( $custom_styling == 'Yes' ) { ?>

    <style scoped>
      <?php if ( $show_title ) : ?>
        .x-subscribe-form-<?php echo $form_id; ?> h1 {
          color: <?php echo $title_color; ?>;
        }
      <?php endif; ?>

      <?php if ( $show_labels ) : ?>
        .x-subscribe-form-<?php echo $form_id; ?> label {
          color: <?php echo $label_color; ?>;
        }
      <?php endif; ?>

      .x-subscribe-form-<?php echo $form_id; ?> .submit {
        <?php echo $button_colors; ?>
      }

      .x-subscribe-form-<?php echo $form_id; ?> .submit:hover {
        <?php echo $button_colors_hover; ?>
      }
    </style>

  <?php } ?>

  <input type="hidden" name="x_subscribe_form[id]" value="<?php echo $form_id; ?>">

  <?php if ( $show_title == 'Yes' ) : ?>
    <h1><?php echo $title; ?></h1>
  <?php endif; ?>

  <?php if ( strpos( $name_display, 'first' ) !== false ) : ?>
    <fieldset>
      <?php if ( $show_labels ) : ?>
        <label for="x_subscribe_form_first_name">
          <span>
            <?php echo $first_name_label; ?>
            <?php echo ( $name_required ) ? '<span class="required">*</span>' : ''; ?>
          </span>
        </label>
      <?php endif; ?>
      <input type="text" name="x_subscribe_form[first-name]" id="x_subscribe_form_first_name" placeholder="<?php echo $first_name_placeholder; ?>"<?php echo ( $name_required ) ? ' required' : ''; ?>>
    </fieldset>
  <?php endif; ?>

  <?php if ( strpos( $name_display, 'last' ) !== false ) : ?>
    <fieldset>
      <?php if ( $show_labels ) : ?>
        <label for="x_subscribe_form_last_name">
          <span>
            <?php echo $last_name_label; ?>
            <?php echo ( $name_required ) ? '<span class="required">*</span>' : ''; ?>
          </span>
        </label>
      <?php endif; ?>
      <input type="text" name="x_subscribe_form[last-name]" id="x_subscribe_form_last_name" placeholder="<?php echo $last_name_placeholder; ?>"<?php echo ( $name_required ) ? ' required' : ''; ?>>
    </fieldset>
  <?php endif; ?>

  <?php if ( strpos( $name_display, 'full' ) !== false ) : ?>
    <fieldset>
      <?php if ( $show_labels ) : ?>
        <label for="x_subscribe_form_full_name">
          <span>
            <?php echo $full_name_label; ?>
            <?php echo ( $name_required ) ? '<span class="required">*</span>' : ''; ?>
          </span>
        </label>
      <?php endif; ?>
      <input type="text" name="x_subscribe_form[full-name]" id="x_subscribe_form_full_name" placeholder="<?php echo $full_name_placeholder; ?>"<?php echo ( $name_required ) ? ' required' : ''; ?>>
    </fieldset>
  <?php endif; ?>

  <fieldset>
    <?php if ( $show_labels ) : ?>
      <label for="x_subscribe_form_email">
        <span>
          <?php echo $email_label; ?>
          <span class="required">*</span>
        </span>
      </label>
    <?php endif; ?>
    <input type="email" name="x_subscribe_form[email]" id="x_subscribe_form_email" placeholder="<?php echo $email_placeholder; ?>" required>
  </fieldset>

  <fieldset>
    <input type="submit" name="x_subscribe_form[submit]" class="submit<?php echo $style . $shape; ?>" value="<?php echo $submit_label; ?>">
  </fieldset>

</form>

<?php

$form = ob_get_contents(); ob_end_clean();



// Shortcode Setup
// =============================================================================

//
// Class output.
//

if ( $class != '' ) {
  $class = 'class="' . $class . '"';
} else {
  $class = '';
}


//
// If "Custom Styling" is set to "No," then simply output the form in an
// "invisible" [x_section] shortcode, which simply acts as a container.
// Otherwise, allow users to tap into [x_section] options via the plugin.
//

if ( $custom_styling == 'No' ) {

  $shortcode = do_shortcode( '[x_section ' . $class . ' padding_top="0" padding_bottom="0"][x_row][x_column type="1/1"]' . $form . '[/x_column][/x_row][/x_section]' );

} else if ( $custom_styling == 'Yes' ) {

  //
  // Margin.
  //

  if ( $remove_margin == 'Yes' ) {
    $margin = 'margin: 0;';
  } else {
    $margin = '';
  }


  //
  // Border.
  //

  switch ( $border ) {
    case 'None' :
      $border = '';
      break;
    case 'Top' :
      $border = ' border-top: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'Left' :
      $border = ' border-left: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'Right' :
      $border = ' border-right: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'Bottom' :
      $border = ' border-bottom: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'Vertical' :
      $border = ' border-top: 1px solid rgba(0, 0, 0, 0.075); border-bottom: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'Horizontal' :
      $border = ' border-left: 1px solid rgba(0, 0, 0, 0.075); border-right: 1px solid rgba(0, 0, 0, 0.075);';
      break;
    case 'All' :
      $border = ' border: 1px solid rgba(0, 0, 0, 0.075);';
      break;
  }


  //
  // Padding.
  //

  $padding = ' padding: ' . $padding . ';';


  //
  // Background.
  //

  switch ( $bg_option ) {
    case 'Transparent' :
      $bg = ' bg_color="transparent"';
      break;
    case 'Color' :
      $bg = ' bg_color="' . $bg_color . '"';
      break;
    case 'Pattern' :
      $parallax = ( $bg_parallax == 'Yes' ) ? ' parallax="true"' : '';
      $bg       = ' bg_pattern="' . $bg_pattern . '"' . $parallax;
      break;
    case 'Image' :
      $parallax = ( $bg_parallax == 'Yes' ) ? ' parallax="true"' : '';
      $bg       = ' bg_image="' . $bg_image . '"' . $parallax;
      break;
    case 'Video' :
      $bg = ' bg_video="' . $bg_video . '" bg_video_poster="' . $bg_video_poster . '"';
      break;
  }


  //
  // Inner container.
  //

  if ( $inner_container == 'Yes' ) {
    $inner = ' inner_container="true"';
  } else {
    $inner = '';
  }

  $shortcode = do_shortcode( '[x_section ' . $class . ' style="' . $margin . $border . $padding . '"' . $bg . '][x_row ' . $inner . '][x_column type="1/1"]' . $form . '[/x_column][/x_row][/x_section]' );

}



// Shortcode Output
// =============================================================================

echo $shortcode;