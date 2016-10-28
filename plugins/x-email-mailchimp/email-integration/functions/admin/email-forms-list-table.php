<?php

// =============================================================================
// FUNCTIONS/ADMIN/EMAIL-FORMS-LIST-TABLE.PHP
// -----------------------------------------------------------------------------
// Build the WP_List_Table we need to display our custom post type controls.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Include WP_List_Table Class
//   02. List Table
// =============================================================================

// Include WP_List_Table Class
// =============================================================================

if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



// List Table
// =============================================================================

class X_Email_Forms_List_Table extends WP_List_Table {

  //
  // Properties.
  //

  protected $args;
  protected $messages;


  //
  // Construct.
  //

  function __construct( $args = array() ) {

    $this->messages = array();

    $this->args = wp_parse_args( $args, array(

      //
      // Should always be overriden.
      //

      'name'      => 'generic',
      'post_type' => 'generic',
      'singular'  => 'generic',
      'plural'    => 'generics',
      'labels'    => array(
        'singular' => __( 'Item','__x__' ),
        'plural'   => __( 'Items','__x__' ),
        'no_items' => __( 'No Items', '__x__' )
      ),


      //
      // Override optionally.
      //

      'use_cb_column'    => true,
      'use_trash'        => true,
      'per_page_default' => 15,
      'columns'          => array(
        'title' => __( 'Title', '__x__' ),
        'date'  => __( 'Date', '__x__' )
      ),
      'bulk_actions'     => array()

    ) );

    parent::__construct( array(
      'singular' => $this->args['singular'],
      'plural'   => $this->args['plural'],
      'ajax'     => false
    ) );


    //
    // Setup up some reusable column defaults.
    //

    add_filter( 'x_' . $this->args['name'] . '_list_table_column_title', array( $this, 'default_column_title' ), 10, 2 );
    add_filter( 'x_' . $this->args['name'] . '_list_table_column_date', array( $this, 'default_column_date' ), 10, 2 );

  }


  //
  // Prepare items.
  //

  function prepare_items() {

    //
    // Hook to do something before items are prepared.
    //

    do_action( 'x_' . $this->args['name'] . '_list_table_before_prepare_items', $this );

    if ( $this->args['use_trash'] ) {
      $this->handle_trash();
    }


    //
    // Prepare columns.
    //

    $columns               = $this->get_columns();
    $hidden                = array();
    $sortable              = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );


    //
    // Build query
    //

    $per_page = $this->get_items_per_page( 'edit_' . $this->args['post_type'] . '_per_page', $this->args['per_page_default'] );

    $args = array(
      'post_type'      => $this->args['post_type'],
      'posts_per_page' => $per_page,
      'offset'         => ( $this->get_pagenum() - 1 ) * $per_page
    );

    if ( ! empty( $_REQUEST['post_status'] ) && in_array( $_REQUEST['post_status'], get_post_stati() ) ) {
      $args['post_status'] = $_REQUEST['post_status'];
    }

    if ( ! empty( $_REQUEST['s'] ) ) {
      $args['s'] = $_REQUEST['s'];
    }

    if ( ! empty( $_REQUEST['orderby'] ) ) {
      $orderby = $_REQUEST['orderby'];
      if ( in_array( $orderby, array_keys( $this->args['columns'] ) ) ) {
        $args['orderby'] = $orderby;
      }
    }

    if ( ! empty( $_REQUEST['order'] ) ) {
      $order = strtoupper( $_REQUEST['order'] );
      if ( in_array( $order, array( 'ASC', 'DESC') ) ) {
        $args['order'] = $order;
      }
    }


    //
    // Execute query.
    //

    $prepare_items_query = new WP_Query( $args );

    $items = array();

    if ( $prepare_items_query ) {
      foreach ( $prepare_items_query->posts as $post ){
        $items[] = $post;
      }
    }

    $this->items = $items;


    //
    // Setup pagination.
    //

    $total_items = ($prepare_items_query) ? $prepare_items_query->found_posts : 0;
    $total_pages = ceil( $total_items / $per_page );

    $this->set_pagination_args( array(
      'total_items' => $total_items,
      'total_pages' => $total_pages,
      'per_page' => $per_page ) );
  }


  //
  // Get columns.
  //

  function get_columns(){

    $columns = array();

    if ( $this->args['use_cb_column'] ) {
      $columns['cb'] = '<input type="checkbox" />';
    }

    $columns = array_merge( $columns, $this->args['columns'] );

    return $columns;

  }


  //
  // Get bulk actions.
  //

  function get_bulk_actions() {

    $bulk_actions = $this->args['bulk_actions'];

    if ( $this->args['use_trash'] ) {
      if ( isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash' ) {
        $bulk_actions['untrash'] = __( 'Restore from Trash', '__x__' );
        $bulk_actions['delete']  = __( 'Delete Permanently', '__x__' );
      } else {
        $bulk_actions['trash'] = __( 'Move to Trash', '__x__' );
      }
    }

    return $bulk_actions;

  }


  //
  // Column checkbox.
  //

  function column_cb( $item ) {
    return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID );
  }


  //
  // Allow this table to be extensible beyond the standard columns.
  //

  function column_default( $item, $column_name ){
    $result = '';
    return apply_filters( 'x_' . $this->args['name'] . '_list_table_column_' . $column_name, $result, $item );
  }


  //
  // Default column title.
  //

  function default_column_title( $result, $item ) {

    //
    // Setup actions.
    //

    $actions = array();
    $params  = array( 'post' => $item->ID );


    //
    // Add edit action.
    //

    if ( $item->post_status != 'trash' ) {
      $edit_url        = admin_url( 'post.php?post=' . $item->ID . '&action=edit' );
      $actions['edit'] = sprintf(__( '<a href="%s">Edit</a>', '__x__'), $edit_url );
    }


    //
    // Add trash action.
    //

    if ( $this->args['use_trash'] ) {

      if ( $item->post_status == 'trash' ) {

        $untrash_params = array(
          '_untrash_nonce' => wp_create_nonce( 'untrash_' . $item->ID ),
          'action'         => 'untrash'
        );

        $actions['untrash'] = sprintf( __( '<a href="%s">Restore</a>', '__x__' ),
                                add_query_arg( array_merge( $params, $untrash_params ), $this->get_admin_url() )
                              );

        $delete_params = array(
          '_delete_nonce' => wp_create_nonce( 'delete_' . $item->ID ),
          'action'        => 'delete'
        );

        $actions['delete'] = sprintf( __( '<a href="%s">Delete Permanently</a>', '__x__' ),
                               add_query_arg( array_merge( $params, $delete_params ), $this->get_admin_url() )
                             );

      } else {

        $trash_params = array(
          '_trash_nonce' => wp_create_nonce( 'trash_' . $item->ID ),
          'action'       => 'trash'
        );

        $actions['trash'] = sprintf( __( '<a href="%s">Trash</a>', '__x__' ),
                              add_query_arg( array_merge( $params, $trash_params), $this->get_admin_url() )
                            );

      }

    }

    $title = apply_filters( 'the_title', $item->post_title );

    ob_start();

    echo '<strong>';

    if ( $edit_url == '' ) {
      echo $title;
    } else {
      echo '<a class="row-title" href="' . $edit_url . '" title="' . __( 'Edit', '__x__' ) . '">' . $title . '</a>';
    }

    _post_states( $item );

    echo '</strong>';
    echo $this->row_actions( apply_filters( 'x_' . $this->args['name'] . '_list_table_title_actions', $actions, $params ) );

    return ob_get_clean();

  }


  //
  // Default column date.
  //

  function default_column_date ( $result, $post ) {

    if ( '0000-00-00 00:00:00' == $post->post_date ) {

      $t_time    = $h_time = __( 'Unpublished', '__x__' );
      $time_diff = 0;

    } else {

      $t_time    = get_the_time( __( 'Y/m/d g:i:s A' ) );
      $m_time    = $post->post_date;
      $time      = get_post_time( 'G', true, $post );
      $time_diff = time() - $time;

      if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
        $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
      } else {
        $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
      }

    }

    $formatted_time = apply_filters( 'x_list_table_date_column_time', $h_time, $post, $this->args['name'] );

    if ( 'publish' == $post->post_status ) {
      $status = __( 'Published', '__x__' );
    } elseif ( 'future' == $post->post_status ) {
      if ( $time_diff > 0 ) {
        $status = '<strong class="attention">' . __( 'Missed schedule', '__x__' ) . '</strong>';
      } else {
        $status = __( 'Scheduled', '__x__' );
      }
    } else {
      $status = __( 'Last Modified', '__x__' );
    }

    return '<abbr title="' . $t_time . '">' . $formatted_time . '</abbr><br />' . $status;

  }


  //
  // Handle trash.
  //

  function handle_trash() {

    //
    // Trashing.
    //

    if ( isset( $_REQUEST['action'] ) && 'trash' == $_REQUEST['action'] ) {

      //
      // Single trash.
      //

      if ( isset( $_REQUEST['post'] ) && isset( $_REQUEST['_trash_nonce'] ) ) {
        if ( wp_verify_nonce( $_REQUEST['_trash_nonce'], 'trash_' . $_REQUEST['post'] ) ) {
          if ( ! wp_trash_post( $_REQUEST['post'] ) ) {
            wp_die( __( 'Error deleting item.', '__x__' ) );
          }
          $this->add_message( sprintf( __('%s moved to the trash.', '__x__'), $this->args['labels']['singular'] ) );
        } else {
          wp_die( __( 'You do not have permission to delete this item.', '__x__' ) );
        }
      }


      //
      // Bulk trash.
      //

      if ( isset( $_REQUEST['email_form'] ) && is_array( $_REQUEST['email_form'] ) ) {
        if ( check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
          foreach ( $_REQUEST['email_form'] as $post_id ) {
            if ( ! wp_trash_post( $post_id ) ) {
              wp_die( __( 'Error deleting items.', '__x__' ) );
            }
          }
          $this->add_message( sprintf( __( '%s %s moved to the trash.', '__x__' ), count( $_REQUEST['email_form'] ), $this->args['labels']['plural'] ) );
        } else {
          wp_die( __( 'You do not have permission to delete these items.', '__x__' ) );
        }
      }

    }


    //
    // Untrashing
    //

    if ( isset( $_REQUEST['action'] ) && 'untrash' == $_REQUEST['action'] ) {

      //
      // Single untrash.
      //

      if ( isset( $_REQUEST['post'] ) && isset( $_REQUEST['_untrash_nonce'] ) ) {
        if ( wp_verify_nonce( $_REQUEST['_untrash_nonce'], 'untrash_' . $_REQUEST['post'] ) ) {
          if ( ! wp_untrash_post( $_REQUEST['post'] ) ) {
            wp_die( __( 'Error in restoring from Trash.', '__x__' ) );
          } else {
            $this->add_message( sprintf( __( '%s restored from trash.', '__x__' ), $this->args['labels']['singular'] ) );
          }
        } else {
          wp_die( __( 'You do not have permission to restore this item.', '__x__' ) );
        }
      }


      //
      // Bulk untrash.
      //

      if ( isset( $_REQUEST['email_form'] ) && is_array( $_REQUEST['email_form'] ) ) {
        if ( check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
          foreach ( $_REQUEST['email_form'] as $post_id ) {
            if ( ! wp_untrash_post( $post_id ) ) {
              wp_die( __( 'Error in restoring from Trash.', '__x__' ) );
            }
          }
          $this->add_message( sprintf( __( '%s %s restored from the trash.', '__x__' ), count( $_REQUEST['email_form'] ), $this->args['labels']['plural'] ) );
        } else {
          wp_die( __( 'You do not have permission to delete these items.', '__x__' ) );
        }
      }

    }


    //
    // Deletion.
    //

    if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] ) {

      //
      // Single delete.
      //

      if ( isset( $_REQUEST['post'] ) && isset( $_REQUEST['_delete_nonce'] ) ) {
        if ( wp_verify_nonce( $_REQUEST['_delete_nonce'], 'delete_' . $_REQUEST['post'] ) ) {
          if ( ! wp_delete_post( $_REQUEST['post'], true ) ) {
            wp_die( __( 'Error deleting item.', '__x__' ) );
          }
          $this->add_message( sprintf( __('%s permanently deleted', '__x__' ), $this->args['labels']['singular'] ) );
        } else {
          wp_die( __( 'You do not have permission to delete this item.', '__x__' ) );
        }
      }


      //
      // Bulk delete.
      //

      if ( isset( $_REQUEST['email_form'] ) && is_array( $_REQUEST['email_form'] ) ) {
        if ( check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
          foreach ( $_REQUEST['email_form'] as $post_id ) {
            if ( ! wp_delete_post( $post_id, true ) ) {
              wp_die( __( 'Error deleting items.', '__x__' ) );
            }
          }
          $this->add_message( sprintf( __( '%s %s permanently deleted.', '__x__' ), count( $_REQUEST['email_form'] ), $this->args['labels']['plural'] ) );
        } else {
          wp_die( __( 'You do not have permission to delete these items.', '__x__' ) );
        }
      }

    }

  }


  //
  // No items.
  //

  function no_items() {
    echo $this->args['labels']['no_items'];
  }


  //
  // Get views.
  //

  function get_views() {

    GLOBAL $locked_post_status;

    $post_type = $this->args['post_type'];

    if ( ! empty( $locked_post_status ) ) {
      return array();
    }

    $status_links     = array();
    $num_posts        = wp_count_posts( $post_type, 'readable' );
    $class            = '';
    $allposts         = '';
    $avail_post_stati = get_available_post_statuses( $post_type );
    $total_posts      = array_sum( (array) $num_posts );


    //
    // Subtract post types that are not included in the admin all list.
    //

    foreach ( get_post_stati( array( 'show_in_admin_all_list' => false ) ) as $state ) {
      $total_posts -= $num_posts->$state;
    }

    $class               = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
    $all_url             = remove_query_arg( 'post_status', $this->get_admin_url() );
    $status_links['all'] = "<a href='$all_url'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

    foreach ( get_post_stati( array( 'show_in_admin_status_list' => true ), 'objects' ) as $status ) {

      $class       = '';
      $status_name = $status->name;

      if ( ! in_array( $status_name, $avail_post_stati ) ) {
        continue;
      }

      if ( empty( $num_posts->$status_name ) ) {
        continue;
      }

      if ( isset( $_REQUEST['post_status'] ) && $status_name == $_REQUEST['post_status'] ) {
        $class = ' class="current"';
      }

      $status_url                 = add_query_arg( array( 'post_status' => $status_name ), $this->get_admin_url() );
      $status_links[$status_name] = "<a href='$status_url' $class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';

    }

    return $status_links;

  }


  //
  // Get admin URL.
  //

  function get_admin_url() {

    $params = array();

    if ( isset( $_REQUEST['page'] ) ) {
      $params['page'] = $_REQUEST['page'];
    }

    if ( isset( $_REQUEST['tab'] ) ) {
      $params['tab'] = $_REQUEST['tab'];
    }

    return add_query_arg( $params, admin_url( 'admin.php' ) );

  }


  //
  // Messaging API.
  //

  function add_message( $message ) {
    array_push( $this->messages, $message );
  }

  function get_messages() {
    return ( ! empty( $this->messages ) ) ? '<div id="message" class="updated"><p>' . join( ' ', $this->messages ) . '</p></div>' : '';
  }


  //
  // Display table with required markup.
  //

  function render() {

    echo '<form id="forms-filter" method="get">';
      echo $this->views();
      echo '<input type="hidden" name="page" value="' . $_REQUEST['page'] . '" />';
      $this->display();
    echo '</form>';

  }

}