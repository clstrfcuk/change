jQuery( function($) {

  var Progress, Importer, progress, standard, expanded;

  Progress = function() {
    this.$el = $( this.markup );
    this.$message = this.$el.find('.progress-content-title');
    this.$bar = this.$el.find('.progress-content-bar-inner');
  }

  $.extend( Progress.prototype, {

    markup: '<div class="progress-outer">'
      + '<div class="progress-inner">'
        + '<div class="progress-content">'
          + '<div class="progress-content-title"></div>'
          + '<div class="progress-content-bar-outer">'
            + '<div class="progress-content-bar-inner"></div>'
          + '</div>'
        + '</div>'
        + '<div class="progress-complete">'
        +  '<div class="progress-complete-icon dashicons dashicons-yes"></div>'
        +  '<div class="progress-complete-title">'+ xDemoContent.complete + '</div>'
        + '</div>'
      + '</div>'
    + '</div>',

    start: function() {
      this.message( xDemoContent.start );
      $('.x-addons-demo-content').prepend( this.$el );
      this.setProgress(0);
    },

    message: function( message ) {
      this.$message.html( message )
    },

    setProgress: function( ratio ) {
      ratio = ratio * 0.9 + 0.1;
      this.$bar.css( 'width', Math.round( ratio * 100) + '%' );
    },

    simulateProgress: function() {
      this.message( xDemoContent.simulated );
      this.$bar.animate( { width: '80%' }, 250 );
    },

    complete: function() {

      this.message( '&nbsp;' );

      this.$bar.animate( { width: '100%' }, 250 );

      setTimeout(function(){
        this.$el.addClass('complete');
        this.close();
      }.bind(this), 400 )

    },

    fail: function( message ) {
      this.message( message );
      this.close();
    },

    close: function() {

      this.$el.delay(1500).fadeOut(250);

      setTimeout(function(){
        this.$el.detach().removeClass('complete').show();
        this.setProgress(0);
        this.message('');
      }.bind(this), 2000 );

    }

  })

  Importer = function() { }

  $.extend( Importer.prototype, {

    init: function( data, expanded ) {

      this.data = data;
      this.data.action = ( expanded ) ? 'x_demo_importer' : 'x_demo_content_setup';
      this.data.attempts = 1;

      if (!expanded)
        return this.runStandard();


      this.data.session = 's_' + Math.round( new Date().getTime() + ( Math.random() * 100 ) );
      progress.start();
      $('#x-addons-demo-content-submit').prop( 'disabled', true );
      this.acknowledge( { data: {}, first: true } );

    },

    runStandard: function() {

      progress.start();
      progress.simulateProgress();
      $('#x-addons-demo-content-submit').prop( 'disabled', true );

      //

      this.standardProcess( function( response ){

        if ( response.success === false )
          return this.failure( response.data.message, response );

        progress.complete();
        $('#x-addons-demo-content-submit').prop( 'disabled', false );

      }.bind(this) );

    },

    standardProcess: function( success ) {

      jQuery.post( ajaxurl, this.data, success ).fail( function(data) {

        progress.message( this.timeOutMessage( this.data.attempts++ ) );
        if ( this.data.attempts >= 25 )
          return this.failure( xDemoContent.failure, data );

        this.standardProcess( success ); // repeat

      }.bind(this) );

    },

    acknowledge: function( response ) {

      if ( !response.data && !response.first ) {
        progress.message( this.timeOutMessage( this.data.attempts++ ) );
        if ( this.data.attempts > 25 )
          return this.failure( xDemoContent.failure, response );
      } else {

        this.data.attempts = 0;

        if ( response.success == false )
          return this.failure( response.data.message, response.data );

        if ( response.data.message )
          progress.message( response.data.message );

        if ( response.data.debug_message && xDemoContent.debug )
          console.log( 'X Demo Debug', response.data.debug_message, response.data.debug || null );

        if ( response.data.completion && response.data.completion === true ) {
          if ( response.data.debug && xDemoContent.debug )
            console.log( response.data.debug );

          return this.complete();
        }

        if ( response.data.completion ) {
          progress.setProgress( response.data.completion.ratio );
        }

      }

      setTimeout( function() {
        jQuery.post( ajaxurl, this.data ).always( this.acknowledge.bind(this) );
      }.bind(this), 40 * this.data.attempts ); // slow down if timeouts start

    },

    complete: function() {
      progress.complete();
      $('#x-addons-demo-content-submit').prop( 'disabled', false );
    },

    failure: function( message, debug ) {
      progress.fail( message );
      $('#x-addons-demo-content-submit').prop( 'disabled', false );
      console.error( 'X Demo Importer failure', debug || {});
    },

    timeOutMessage: function( attempts ) {
      if (attempts > 20)
        return xDemoContent.timeout3;
      if (attempts > 10)
        return xDemoContent.timeout2;
      return xDemoContent.timeout1;
    }

  } );

  progress = new Progress();
  importer = new Importer();

  $('.x-demo-select').on('click change',function() {

    var $content, $selected, mode, buttonText;
    $('.x-addons-postbox .content').removeClass('selected');

    $content = $(this).parent().parent();
    $selected = $(this).find('option:selected');
    $content.find('.demo-content-link').attr('href', $selected.data('demo-url') );
    $content.addClass('selected');

    mode = ( $(this).attr('name') == 'standard-demo' ) ? 'standard' : 'expanded';

    buttonText = ( mode == 'standard' ) ? xDemoContent.buttonStandard : xDemoContent.buttonExpanded;

    $('#x-addons-demo-content-submit').val( buttonText.replace( '%s', $selected.html() ) );
    $('input[name="demo_type"]').val( mode );

  });

  $('form#x-demo-content-form').on('submit', function( e ) {

    var data, mode;
    e.preventDefault();

    //
    // Prepare Data
    //

    mode = ( $(this).find('input[name="demo_type"]').val() );
    data = { 'demo': $(this).find('select[name="'+ mode +'-demo"]').val() };

    if ( mode == 'standard' ) {
      data.standard_posts           = $(this).find('input[name="standard-demo-posts"]:checked').val() || 'no';
      data.standard_portfolio_items = $(this).find('input[name="standard-demo-portfolio-items"]:checked').val() || 'no';
    }

console.log(data);

    //
    // Confirm, then proceed to initiate AJAX request.
    //

    xAdminConfirm( 'error', xDemoContent.confirm, function(){
      importer.init( data, ( mode == 'expanded' ) );
    } );

  });

});