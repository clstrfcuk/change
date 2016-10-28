// =============================================================================
// JS/SRC/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Plugin Specific Functionality
//   02. Global Plugin Functionality
// =============================================================================

// Plugin Specific Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Show/hide settings.
  //

  var $pluginEnable   = $('#x_video_lock_enable');
  var $pluginSettings = $('#meta-box-settings');

  $pluginEnable.change(function() {
    if ( $pluginEnable.is(':checked') ) {
      $pluginSettings.show();
    } else {
      $pluginSettings.hide();
    }
  });


  //
  // Show/hide individual settings.
  //

  //
  // Heading.
  //

  var $headingEnable   = $('#x_video_lock_heading_enable');
  var $headingSettings = $headingEnable.closest('tr').nextAll(':lt(2)');

  function headingSettingsAppearance() {
    if ( $headingEnable.is(':checked') ) {
      $headingSettings.show();
    } else {
      $headingSettings.hide();
    }
  }

  headingSettingsAppearance();

  $headingEnable.change(function() {
    headingSettingsAppearance();
  });


  //
  // Subheading.
  //

  var $subheadingEnable   = $('#x_video_lock_subheading_enable');
  var $subheadingSettings = $subheadingEnable.closest('tr').nextAll(':lt(2)');

  function subheadingSettingsAppearance() {
    if ( $subheadingEnable.is(':checked') ) {
      $subheadingSettings.show();
    } else {
      $subheadingSettings.hide();
    }
  }

  subheadingSettingsAppearance();

  $subheadingEnable.change(function() {
    subheadingSettingsAppearance();
  });


  //
  // Source.
  //

  var $source                   = $('input[name="x_video_lock_source"]');
  var $sourceSelfHostedSettings = $source.closest('tr').nextAll(':lt(4)');
  var $sourceThirdPartySettings = $source.closest('tr').nextAll(':eq(4)');

  function sourceSettingsAppearance() {
    if ( $('input[name="x_video_lock_source"]:checked').val() === 'self-hosted' ) {
      $sourceSelfHostedSettings.show();
      $sourceThirdPartySettings.hide();
    } else {
      $sourceSelfHostedSettings.hide();
      $sourceThirdPartySettings.show();
    }
  }

  sourceSettingsAppearance();

  $source.change(function() {
    sourceSettingsAppearance();
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