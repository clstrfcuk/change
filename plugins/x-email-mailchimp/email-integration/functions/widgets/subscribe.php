<?php

// =============================================================================
// FUNCTIONS/WIDGETS/SUBSCRIBE.PHP
// -----------------------------------------------------------------------------
// Creates a facade widget to wrap the [x_subscribe] shortcode.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Widget_X_Subscribe extends WP_Widget {

  protected $plugin;


  //
  // Register widget with WordPress.
  //

  function __construct() {

    parent::__construct(
      'x_email_form',
      __( 'Email Form', '__x__' ),
      array( 'description' => __( 'Add a subscription form.', '__x__' ), )
    );

  }


  //
  // Plugin Injection. This allows access to all our plugin data and methods.
  //

  public function set_plugin( $plugin ) {
    $this->plugin = $plugin;
  }


  //
  // Render for front-end.
  //

  public function widget( $args, $instance ) {

    //
    // 1. Open widget.
    // 2. Output title if set.
    // 3. Output form via shortcode.
    // 4. Close widget.
    //

    echo $args['before_widget']; // 1
    echo ( ! empty( $instance['title'] ) ) ? $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'] : ''; // 2
    echo do_shortcode( '[x_subscribe form="' . $instance['form_select'] . '"]' ); // 3
    echo $args['after_widget']; // 4

  }


  //
  // Admin form.
  //

  public function form( $instance ) {

    $view_data = array(
      'title_value'       => ( isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Subscribe', '__x__' ) ),
      'title_id'          => $this->get_field_id( 'title' ),
      'title_name'        => $this->get_field_name( 'title' ),
      'form_select_value' => ( isset( $instance[ 'form_select' ] ) ? $instance[ 'form_select' ] : '' ),
      'form_select_id'    => $this->get_field_id( 'form_select' ),
      'form_select_name'  => $this->get_field_name( 'form_select' ),
    );

    $this->plugin->view->show( 'admin/widget-subscribe', $view_data );

  }


  //
  // Save admin form.
  //

  public function update( $new_instance, $old_instance ) {

    $instance = array();
    $instance['title']       = ( ! empty( $new_instance['title'] )       ) ? strip_tags( $new_instance['title'] )       : '';
    $instance['form_select'] = ( ! empty( $new_instance['form_select'] ) ) ? strip_tags( $new_instance['form_select'] ) : '';

    return $instance;

  }

}