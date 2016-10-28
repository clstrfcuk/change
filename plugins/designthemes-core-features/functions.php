<?php

/**
 * Disables BuddyPress' registration process and fallsback to WordPress' one.
 */
function dttheme_disable_bp_registration() {
  remove_action( 'bp_init',    'bp_core_wpsignup_redirect' );
  remove_action( 'bp_screens', 'bp_core_screen_signup' );
}
add_action( 'bp_loaded', 'dttheme_disable_bp_registration' );


// Creating new role as 'teacher' and configuring capabilities
$result = add_role(
    'teacher',
    __( 'Teacher', 'dt_themes' ),
    array(
		'read' => true,
		'edit_posts' => true,
		'publish_posts' => true,
		'edit_published_posts' => true,
		'delete_posts' => true,
		'delete_published_posts' => true,
		'upload_files' => true,	
    )
);


//1. Add a new form element...
add_action( 'register_form', 'dt_custom_registration_form' );
function dt_custom_registration_form() {

	$role = ( isset( $_POST['role'] ) ) ? trim( $_POST['role'] ) : 'subscriber';
	?>
	<p>
		<label for="role"><?php _e( 'Role', 'dt_themes' ) ?><br />
            <select name="role" id="role">
                <option value="subscriber" <?php selected( $role, 'subscriber' ); ?>><?php echo __('Subscriber', 'dt_themes'); ?></option>
                <option value="teacher" <?php selected( $role, 'teacher' ); ?>><?php echo __('Teacher', 'dt_themes'); ?></option>
				<?php 
                $status = dttheme_is_plugin_active('s2member/s2member.php');
                if($status) {
                ?>
                    <option value="s2member_level1" <?php selected( $role, 's2member_level1' ); ?>><?php echo __('Student', 'dt_themes'); ?></option>
				<?php
                }
                ?>
            </select>
        </label>
	</p>
	<?php
	
}

//2. Add validation. In this case, we make sure first_name is required.
add_filter( 'registration_errors', 'dt_registration_errors', 10, 3 );
function dt_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( ! isset( $_POST['role'] ) ) {
		$errors->add( 'role_error', __( '<strong>ERROR</strong>: You must select role.', 'dt_themes' ) );
	}
	
	if ( $_POST['role'] != 'subscriber' && $_POST['role'] != 'teacher' && $_POST['role'] != 's2member_level1' ) {
		$errors->add( 'role_error', __( '<strong>ERROR</strong>: Invalid role.', 'dt_themes' ) );
	}

	return $errors;
	
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'dt_update_user' );
function dt_update_user( $user_id ) {
	if ( isset( $_POST['role'] ) ) {
		 wp_update_user( array ('ID' => $user_id, 'role' => $_POST['role']) );
	}
}


add_action('user_register','dt_create_teacher_post');
function dt_create_teacher_post($user_id){
	
	if (!$user_id>0) return;
	
	$user = get_user_by('id', $user_id);
	
	if($user->roles[0] == 'teacher') {
	
		$teacher_post = array(
			'post_title' => $user->data->display_name,
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'dt_teachers',
			'post_author' => $user_id
		);
		
		$post_id = wp_insert_post( $teacher_post );
		
		update_post_meta ( $post_id, "_teacher_user_id",  $user_id );
	
	}

}

?>