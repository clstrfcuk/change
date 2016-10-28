// =============================================================================
// JS/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. General Settings Screen
//   02. Post Type Screen
//   03. Global Plugin Functionality
// =============================================================================

// General Settings Screen
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Show/hide new users opt-in list.
  //

  $row_new_users_list = $('#x_email_forms_opt_in_new_users_list').parents('tr');

  if ( $('input[name="x_email_forms[opt_in_new_users]"]:checked').val() === 'no' ) {
    $row_new_users_list.hide();
  }

  $('input[name="x_email_forms[opt_in_new_users]"]').change(function(){
    $row_new_users_list.toggle();
  });

});



// Post Type Screen
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Form: show/hide confirmation method.
  //

  $row_confirm_message  = $('#x_email_forms_confirmation_message').parents('tr');
  $row_confirm_redirect = $('#x_email_forms_confirmation_redirect').parents('tr');

  $('input[name="x_meta[x_email_forms_confirmation_type]"]').click(function() {
    if ( $(this).is(':checked') && $(this).val() === 'Message' ) {
      $row_confirm_message.show();
      $row_confirm_redirect.hide();
    } else {
      $row_confirm_message.hide();
      $row_confirm_redirect.show();
    }
  });

  $('input[name="x_meta[x_email_forms_confirmation_type]"]:checked').trigger('click');


  //
  // Form: show/hide title display.
  //

  $title_row = $('#x_email_forms_title').parents('tr');

  if ( $('input[name="x_meta[x_email_forms_show_title]"]:checked').val() === 'No' ) {
    $title_row.hide();
  }

  $('input[name="x_meta[x_email_forms_show_title]"]').change(function() {
    $title_row.toggle();
  });


  //
  // Form: show/hide name display.
  //

  $name_select    = $('#x_email_forms_name_display');
  $row_full_name  = $('#x_email_forms_full_name_placeholder').parents('tr');
  $row_first_name = $('#x_email_forms_first_name_placeholder').parents('tr');
  $row_last_name  = $('#x_email_forms_last_name_placeholder').parents('tr');

  toggle_name_placeholder_fields( $name_select.val() );

  $name_select.change(function() {
    toggle_name_placeholder_fields( $(this).val() );
  });

  function toggle_name_placeholder_fields( setting ) {
    switch( setting ) {
      case 'None':
        $row_first_name.hide();
        $row_last_name.hide();
        $row_full_name.hide();
        break;
      case 'First':
        $row_first_name.show();
        $row_last_name.hide();
        $row_full_name.hide();
        break;
      case 'Last':
        $row_first_name.hide();
        $row_last_name.show();
        $row_full_name.hide();
        break;
      case 'Full (Separate)':
        $row_first_name.show();
        $row_last_name.show();
        $row_full_name.hide();
        break;
      case 'Full (Combined)':
        $row_first_name.hide();
        $row_last_name.hide();
        $row_full_name.show();
        break;
    }
  }


  //
  // Form: show/hide label visibility.
  //

  $label_rows = $('#x_email_forms_email_label, #x_email_forms_first_name_label, #x_email_forms_last_name_label, #x_email_forms_full_name_label').parents('tr');

  if ( $('input[name="x_meta[x_email_forms_show_labels]"]:checked').val() === 'No' ) {
    $label_rows.hide();
  }

  $('input[name="x_meta[x_email_forms_show_labels]"]').change(function() {
    $label_rows.toggle();
  });


  //
  // Appearance (General): show/hide custom styling meta boxes.
  //

  $appearance_meta_boxes = $('#x-email-forms-appearance-form-container, #x-email-forms-appearance-form');

  if ( $('input[name="x_meta[x_email_forms_custom_styling]"]:checked').val() === 'No' ) {
    $appearance_meta_boxes.hide();
  }

  $('input[name="x_meta[x_email_forms_custom_styling]"]').change(function() {
    $appearance_meta_boxes.toggle();
  });


  //
  // Appearance (Form Container): show/hide background options.
  //

  $bg_select           = $('#x_email_forms_bg_option');
  $row_bg_color        = $('#x_email_forms_bg_color').parents('tr');
  $row_bg_pattern      = $('#x_email_forms_bg_pattern').parents('tr');
  $row_bg_image        = $('#x_email_forms_bg_image').parents('tr');
  $row_bg_parallax     = $('input[name="x_meta[x_email_forms_bg_parallax]"][value="Yes"]').parents('tr');
  $row_bg_video        = $('#x_email_forms_bg_video').parents('tr');
  $row_bg_video_poster = $('#x_email_forms_bg_video_poster').parents('tr');

  toggle_background_option_fields( $bg_select.val() );

  $bg_select.change(function() {
    toggle_background_option_fields( $(this).val() );
  });

  function toggle_background_option_fields( setting ) {
    switch( setting ) {
      case 'Transparent':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Color':
        $row_bg_color.show();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Pattern':
        $row_bg_color.hide();
        $row_bg_pattern.show();
        $row_bg_image.hide();
        $row_bg_parallax.show();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Image':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.show();
        $row_bg_parallax.show();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Video':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.show();
        $row_bg_video_poster.show();
        break;
    }
  }


  //
  // Appearance (Form): show/hide button colors.
  //

  $button_style_select     = $('#x_email_forms_button_style');
  $row_button_text         = $('#x_email_forms_button_text_color').parents('tr');
  $row_button_bg           = $('#x_email_forms_button_bg_color').parents('tr');
  $row_button_border       = $('#x_email_forms_button_border_color').parents('tr');
  $row_button_bottom       = $('#x_email_forms_button_bottom_color').parents('tr');
  $row_button_text_hover   = $('#x_email_forms_button_text_color_hover').parents('tr');
  $row_button_bg_hover     = $('#x_email_forms_button_bg_color_hover').parents('tr');
  $row_button_border_hover = $('#x_email_forms_button_border_color_hover').parents('tr');
  $row_button_bottom_hover = $('#x_email_forms_button_bottom_color_hover').parents('tr');

  toggle_button_color_fields( $button_style_select.val() );

  $button_style_select.change(function() {
    toggle_button_color_fields( $(this).val() );
  });

  function toggle_button_color_fields( setting ) {
    switch( setting ) {
      case '3D':
        $row_button_text.show();
        $row_button_bg.show();
        $row_button_border.show();
        $row_button_bottom.show();
        $row_button_text_hover.show();
        $row_button_bg_hover.show();
        $row_button_border_hover.show();
        $row_button_bottom_hover.show();
        break;
      case 'Flat':
        $row_button_text.show();
        $row_button_bg.show();
        $row_button_border.show();
        $row_button_bottom.hide();
        $row_button_text_hover.show();
        $row_button_bg_hover.show();
        $row_button_border_hover.show();
        $row_button_bottom_hover.hide();
        break;
      case 'Transparent':
        $row_button_text.show();
        $row_button_bg.hide();
        $row_button_border.show();
        $row_button_bottom.hide();
        $row_button_text_hover.show();
        $row_button_bg_hover.hide();
        $row_button_border_hover.show();
        $row_button_bottom_hover.hide();
        break;
    }
  }


  //
  // Strip ID from email form option values.
  //

  $('#x_email_forms_list option').each(function(i, el) {
    item    = $(el);
    val     = item.val();
    content = val.split('**', 2);
    item.text(content[1]);
    item.val(val);
  });

});



// Global Plugin Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Accordion.
  //

  $('.accordion > .toggle').click(function() {
    var $this = $(this);
    if ( $this.hasClass('active') ) {
      $this.removeClass('active').next().slideUp();
    } else {
      $('.accordion > .panel').slideUp();
      $this.siblings().removeClass('active');
      $this.addClass('active').next().slideDown();
      return false;
    }
  });


  //
  // Save button.
  //

  $('#submit').click(function() {
    $(this).addClass('saving').val('Updating');
  });


  //
  // Color picker.
  //

  $('.wp-color-picker').wpColorPicker();


  //
  // Datepicker.
  //

  $('.datepicker').datepicker();


  //
  // Meta box toggle.
  //

  postboxes.add_postbox_toggles(pagenow);

});