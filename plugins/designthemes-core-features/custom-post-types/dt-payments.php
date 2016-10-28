<?php

function dt_payment_options() {
	
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dt_set_commission';
	
	dt_get_payment_submenus($current);
	dt_get_payment_tab($current);
	
}		

function dt_get_payment_submenus($current){

    $tabs = array( 
				'dt_set_commission' => __('Set Commissions', 'dt_themes'), 
				'dt_pay_commission' => __('Pay Commissions', 'dt_themes'), 
    		);
			
    echo '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ){
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$class.'" href="?page=dt-payment-options&tab='.$key.'">'.$tab.'</a>';
	
		}
    echo '</h2>';

}

function dt_get_payment_tab($current){
	
	if(isset($_POST['dt_save']))
		dt_payment_save_settings($current);

	switch($current){
		case 'dt_set_commission': 
			dt_get_commission_settings();
		break;
		case 'dt_pay_commission':
			dt_get_payment_settings();
		break;
		default:
			dt_get_commission_settings();
		break;
	}
	
}

function dt_payment_save_settings($current){
	if ( !empty($_POST) && check_admin_referer('dt_payment_settings','_wpnonce') ){
		$payment_settings = array();
		$payment_settings = get_option('dt_settings');

		unset($_POST['_wpnonce']);
		unset($_POST['_wp_http_referer']);
		unset($_POST['dt_save']);
		switch($current){
			case 'dt_set_commission':
				$payment_settings['set-commission']['commission'][$_POST['dt-setcom-teacher']] = $_POST;
			break;
			case 'dt_pay_commission':
				
				$teacher_info = get_userdata($_POST['dt-paycom-teacher']);
				$teacher_name = $teacher_info->display_name;
							
				$title = __('Commissions / '.$teacher_name.' / '.date('Y-m-d'), 'dt_themes');
				
				$items_all = dt_decode_array($_POST['item_data_all']);
				$selected_items = isset($_POST['item']) ? $_POST['item'] : '';
				
				$new_items = array();
				
				if($selected_items != '') {
					
					$j = 0;
					foreach($items_all as $key => $item) {
						if(isset($selected_items[$j]) && $selected_items[$j] == 'true') {
							
							$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
							
							foreach($students as $student) {
								$new_ccaps = '';
								
								$student_level = get_user_field ("s2member_access_role", $student->data->ID);
								
								if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') { $all_ccaps = dt_get_all_paid_courses(); }
								else { $student_cap = get_user_field ("s2member_access_ccaps", $student->data->ID); $all_ccaps = dt_remove_cid($student_cap); }
								
								if(in_array($item['course_id'], $all_ccaps)) {
									$prev_ccaps = get_user_meta($student->data->ID, 'commission_ccaps', true);
									$prev_ccaps = isset($prev_ccaps) ? $prev_ccaps : '';
									if($prev_ccaps != '')
										$new_ccaps = $prev_ccaps.','.$item['course_id'];
									else
										$new_ccaps = $item['course_id'];
										
									update_user_meta($student->data->ID, 'commission_ccaps', $new_ccaps);
								}
								
							}
							
							$new_items[] = $item;
						}
						$j++;
					}
					
					$payment_post = array(
						'post_title' => $title,
						'post_status' => 'publish',
						'post_type' => 'dt_payments',
					);
					
					$payment_post_id = wp_insert_post( $payment_post );
					
					update_post_meta ( $payment_post_id, 'payment-data',  $new_items );
					
				}
				
			break;
		}
		update_option('dt_settings',$payment_settings);
	}
}

function dt_get_commission_settings(){
	
	$payment_settings = get_option('dt_settings');
	
	$teachers = get_users( array('role'=> 'teacher') );
	
	echo '<div class="dt-overallstatistics-container">';
	
	echo '<input type="hidden" name="current-tab" id="current-tab" value="set-commission" />';
	echo '<input type="hidden" name="teacher-alert" id="teacher-alert" value="'.__('Please select teaacher!', 'dt_themes').'" />';
	
	echo '<form name="frmSetCommission" method="post">';

	echo '<div class="dt-payment-option-container">';
	echo '<label>'.__('Teacher', 'dt_themes').'</label>';
    echo '<select id="dt-setcom-teacher" name="dt-setcom-teacher" style="width:50%;" data-placeholder="'.__('Select Teacher...', 'dt_themes').'" class="dt-chosen-select">';
	echo '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $teachers ) > 0 ) {
            foreach ($teachers as $teacher){
				$teacher_id = $teacher->data->ID;
                echo '<option value="' . esc_attr( $teacher_id ) . '"' . selected( $teacher_id, $_POST['dt-setcom-teacher'], false ) . '>' . esc_html( $teacher->data->display_name ) . '</option>';
            }
        }
    echo '</select>';
	echo '</div>';
	  
	echo '<div id="setcommission-container">';
	
	if(isset($_POST['dt-setcom-teacher']))
		dt_set_commission($_POST['dt-setcom-teacher']);
	
	echo '</div>';
	
	echo '</form>';
	
	echo '</div>';
	
}

function dt_get_payment_settings(){

	echo '<div class="dt-overallstatistics-container">';
	
	echo '<input type="hidden" name="current-tab" id="current-tab" value="pay-commission" />';
	echo '<input type="hidden" name="teacher-alert" id="teacher-alert" value="'.__('Please select teaacher!', 'dt_themes').'" />';

	echo '<form name="frmPayCommission" method="post">';

	$teachers = get_users( array('role'=> 'teacher') );
	
	echo '<div class="dt-payment-option-container">';
	echo '<label>'.__('Teacher', 'dt_themes').'</label>';
    echo '<select id="dt-paycom-teacher" name="dt-paycom-teacher" style="width:50%;" data-placeholder="'.__('Select Teacher...', 'dt_themes').'" class="dt-chosen-select">';
	echo '<option value="">'.__('None', 'dt_themes').'</option>';
        if ( count( $teachers ) > 0 ) {
            foreach ($teachers as $teacher){
				$teacher_id = $teacher->data->ID;
                echo '<option value="' . esc_attr( $teacher_id ) . '"' . selected( $teacher_id, $_POST['dt-paycom-teacher'], false ) . '>' . esc_html( $teacher->data->display_name ) . '</option>';
            }
        }
    echo '</select>';
	echo '</div>';

	echo '<div id="paycommission-container">';
	
	if(isset($_POST['dt-paycom-teacher']))
		dt_pay_commission($_POST['dt-paycom-teacher']);
	
	echo '</div>';
	
	echo '</form>';
	
	echo '</div>';
		
}

?>