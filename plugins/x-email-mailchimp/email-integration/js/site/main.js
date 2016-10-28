// =============================================================================
// JS/SITE/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin site scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Handle Form Submissions
// =============================================================================

// Handle Form Submissions
// =============================================================================

jQuery(document).ready(function($) {

  function confirm_subscription( form ) {

    var confirm_type = form.data('x-email-confirm');

    if ( confirm_type === 'Message' ) {
      make_alert( form.data('x-email-message'), 'x-alert-success' ).appendTo(form);
    }

    if ( confirm_type === 'Redirect' ) {
      window.location.href = form.data('x-email-redirect');
    }

  }

  function make_alert( content, class_name ) {

    return $('<div class="x-subscribe-form-alert-wrap">' +
               '<div class="x-alert ' + ( class_name || 'x-alert-danger' ) + ' fade in man">' +
                 '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                 content +
               '</div>' +
             '</div>').alert();

  }

  $('.x-subscribe-form').submit(function(e) {

    //
    // Prevent default behavior.
    //

    e.preventDefault();


    //
    // Make note of our form.
    //

    $form = $(this);


    //
    // Prevent extra submissions.
    //

    $form.find('input[type="submit"]').prop('disabled', true).addClass('btn-muted');


    //
    // Craft data for AJAX request.
    //

    postdata = {
      action : 'x_subscribe',
      data   : {
        form_id       : $(this).find('input[name="x_subscribe_form[id]"]').val() || '',
        first_name    : $(this).find('input[name="x_subscribe_form[first-name]"]').val() || '',
        last_name     : $(this).find('input[name="x_subscribe_form[last-name]"]').val() || '',
        full_name     : $(this).find('input[name="x_subscribe_form[full-name]"]').val() || '',
        email_address : $(this).find('input[name="x_subscribe_form[email]"]').val() || '',
      }
    };


    //
    // Submit form.
    //

    $.post(x_email_forms.ajaxurl, postdata, function(response) {
      data = $.parseJSON(response);
      if ( data.error ) {
        console.log(data.log_message);
        make_alert(data.message).appendTo($form);
      } else {
        console.log('subscribed');
        confirm_subscription($form);
      }
    });

  });

});