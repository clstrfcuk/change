<?php

function dt_statistics_options() {
	
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dt_statistics_overview';
	
	dt_get_statistics_submenus($current);
	dt_get_statistics_tab($current);
	
}		

function dt_get_statistics_submenus($current){

    $tabs = array( 
				'dt_statistics_overview' => __('Overview', 'dt_themes'), 
				'dt_statistics_course' => __('Courses', 'dt_themes'), 
				'dt_statistics_students' => __('Students', 'dt_themes'), 
				'dt_statistics_teachers' => __('Teachers', 'dt_themes'), 
				'dt_statistics_graph' => __('Graph', 'dt_themes'), 
    		);
			
    echo '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ){
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$class.'" href="?page=dt-statistics-options&tab='.$key.'">'.$tab.'</a>';
		}
    echo '</h2>';
	

}

function dt_get_statistics_tab($current){
	
	switch($current){
		case 'dt_statistics_overview': 
			dt_get_statistics_overview();
		break;
		case 'dt_statistics_course':
			dt_get_statistics_courses();
		break;
		case 'dt_statistics_students': 
			dt_get_statistics_students();
		break;
		case 'dt_statistics_teachers': 
			dt_get_statistics_teachers();
		break;
		case 'dt_statistics_graph': 
			dt_get_statistics_graph();
		break;
		default:
			dt_get_statistics_overview();
		break;
	}
	
}

function dt_get_statistics_overview(){
	
	echo '<div class="dt-overallstatistics-container">';
	
	echo '<div class="column one-fourth">';
	echo '<table border="0" cellpadding="0" cellspacing="0" class="dt-statistics-overview">';
	
	$course_args = array('post_type' => 'dt_courses', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'DESC');
	$courses = get_posts( $course_args );
	
	echo '<tr>
			<td><strong>'.__('Total Courses ', 'dt_themes').'</strong></td>
			<td>'.count($courses).'</td>
		</tr>';
		
	$lesson_args = wp_count_posts('dt_lessons');
	$lesson_cnt = $lesson_args->publish;	
	
	echo '<tr>
			<td><strong>'.__('Total Lessons ', 'dt_themes').'</strong></td>
			<td>'.$lesson_cnt.'</td>
		</tr>';
	
	$teachers = get_users( array('role' => 'teacher') );
	
	echo '<tr>
			<td><strong>'.__('Total Teachers ', 'dt_themes').'</strong></td>
			<td>'.count($teachers).'</td>
		</tr>';
	
	$s2level1 = get_users(array('role' => 's2member_level1'));
	echo '<tr>
			<td><strong>'.S2MEMBER_LEVEL1_LABEL.'</strong></td>
			<td>'.count($s2level1).'</td>
		</tr>';

	$s2level2 = get_users(array('role' => 's2member_level2'));
	echo '<tr>
			<td><strong>'.S2MEMBER_LEVEL2_LABEL.'</strong></td>
			<td>'.count($s2level2).'</td>
		</tr>';

	$s2level3 = get_users(array('role' => 's2member_level3'));
	echo '<tr>
			<td><strong>'.S2MEMBER_LEVEL3_LABEL.'</strong></td>
			<td>'.count($s2level3).'</td>
		</tr>';

	$s2level4 = get_users(array('role' => 's2member_level4'));
	echo '<tr>
			<td><strong>'.S2MEMBER_LEVEL4_LABEL.'</strong></td>
			<td>'.count($s2level4).'</td>
		</tr>';
	
	$students = count($s2level1)+count($s2level2)+count($s2level3)+count($s2level4);
	
	echo '<tr>
			<td><strong>'.__('Total Students ', 'dt_themes').'</strong></td>
			<td>'.$students.'</td>
		</tr>';
	
	
	$total_course_subscribed = $total_course_completed = $total_course_certificates = $total_course_badges = 0;
	
	if(isset($courses) && !empty($courses)) {
		
		foreach($courses as $course) {
			
			$course_id = $course->ID;
			$course_data = get_post( $course_id );
			$student_cap = dt_get_course_capabilities_id($course_id);
			
			$course_subscribed = count($student_cap);
			$course_certificates = dt_get_course_certificates_count($course_id);
			$course_badges = dt_get_course_badges_count($course_id);
		
			$total_course_subscribed = $total_course_subscribed	+ $course_subscribed;
			$total_course_certificates = $total_course_certificates	+ $course_certificates;
			$total_course_badges = $total_course_badges	+ $course_badges;
				
		}
		
	}	
	
	echo '<tr>
			<td><strong>'.__('Total Subscribtions ', 'dt_themes').'</strong></td>
			<td>'.$total_course_subscribed.'</td>
		</tr>';
	echo '<tr>
			<td><strong>'.__('Total Certificates Issued ', 'dt_themes').'</strong></td>
			<td>'.$total_course_certificates.'</td>
		</tr>';
	echo '<tr>
			<td><strong>'.__('Total Badges Issued ', 'dt_themes').'</strong></td>
			<td>'.$total_course_badges.'</td>
		</tr>';
	
	
	echo '</table>';
	
	echo '</div>';
	
	echo '</div>';
}

function dt_get_statistics_courses(){
	
	echo '<div class="dt-overallstatistics-container">';
	
	if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != '') {
	
		$course_id = $_REQUEST['course_id'];
		$course_data = get_post($course_id);
		echo '<h3>'.$course_data->post_title.'</h3>';
		
		$student_cap = dt_get_course_capabilities_id($course_id);
		
		if(isset($student_cap) && !empty($student_cap)) {
			
			echo '<table border="0" cellpadding="0" cellspacing="0">';
			
			echo '<tr>
					<th>'.__('Student Name', 'dt_themes').'</th>
					<th>'.__('Status', 'dt_themes').'</th>
					<th>'.__('Percentage', 'dt_themes').'</th>
				</tr>';
			
			foreach($student_cap as $student_id) {
			
				$course_status = dt_get_users_course_status($course_id, $student_id);
				
				if($course_status) $status = _('Completed', 'dt_themes');
				else $status = _('In Progress', 'dt_themes');
				
				$course_percent = dt_get_course_percentage($course_id, $student_id);
				
				if($course_percent > 0) $course_percent =  $course_percent.'%';
				else $course_percent  = '';
				
				$student_info = get_userdata($student_id);
				
				echo '<tr>
						<td><strong>'.$student_info->display_name.'</strong></td>
						<td>'.$status.'</td>
						<td>'.$course_percent.'</td>
					</tr>';
					
			}
			
			echo '</table>';
			
		}
		
		echo '<a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_course').'" class="dt-statistics-button">'.__('All Courses', 'dt_themes').'</a>';
		
	} else {
	
		echo '<div id="dt-sc-ajax-load-image" style="display:none;"><img src="'.IAMD_BASE_URL."images/loading.png".'" alt="" /></div>';
		echo '<div id="dt-statistics-courses-container">';
		dt_get_statistics_courses_list(10, 1);
		echo '</div>';
	
	}
	
	echo '</div>';
	
}

function dt_get_statistics_students(){
	
	echo '<div class="dt-overallstatistics-container">';
	
	if(isset($_REQUEST['student_id']) && $_REQUEST['student_id'] != '') {
		
		$student_id = $_REQUEST['student_id'];
		$student_info = get_userdata($student_id);
		echo '<h3>'.$student_info->display_name.'</h3>';
		
		$student_level = get_user_field ("s2member_access_role", $student_id);
		if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') { $student_cap = dt_get_all_paid_courses(); }
		else { $student_cap = get_user_field ("s2member_access_ccaps", $student_id); $student_cap = dt_remove_cid($student_cap); }
		
		if(isset($student_cap) && !empty($student_cap)) {
			
			echo '<table border="0" cellpadding="0" cellspacing="0">';
			
			echo '<tr>
					<th>'.__('Course Name', 'dt_themes').'</th>
					<th>'.__('Status', 'dt_themes').'</th>
					<th>'.__('Percentage', 'dt_themes').'</th>
				</tr>';
			
			
			foreach($student_cap as $course_id) {
				
				$course_status = dt_get_users_course_status($course_id, $student_id);
				
				if($course_status) $status = __('Completed', 'dt_themes');
				else $status = __('In Progress', 'dt_themes');
				
				$course_percent = dt_get_course_percentage($course_id, $student_id);
				
				if($course_percent > 0) $course_percent = $course_percent.'%';
				else $course_percent  = '';
				
				$course_data = get_post($course_id);
				$course_title = $course_data->post_title;
				
				echo '<tr>
						<td><strong>'.$course_title.'</strong></td>
						<td>'.$status.'</td>
						<td>'.$course_percent.'</td>
					</tr>';

			}
			
			echo '</table>';
		
		}

		echo '<a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_students').'" class="dt-statistics-button">'.__('All Students', 'dt_themes').'</a>';		
		
	} else {
		
		echo '<div id="dt-sc-ajax-load-image" style="display:none;"><img src="'.IAMD_BASE_URL."images/loading.png".'" alt="" /></div>';
		echo '<div id="dt-statistics-students-container">';
		dt_get_statistics_students_list(10, 1);
		echo '</div>';
		
	}
	
	echo '</div>';
		
}

function dt_get_statistics_teachers(){
	
	echo '<div class="dt-overallstatistics-container">';
	
	if(isset($_REQUEST['teacher_id']) && $_REQUEST['teacher_id'] != '') {
		
		$teacher_id = $_REQUEST['teacher_id'];
		$teacher_info = get_userdata($teacher_id);
		echo '<h3>'.$teacher_info->display_name.'</h3>';
		
		$courses_args = array( 'post_type' => 'dt_courses', 'post_status' => 'publish', 'author' => $teacher_id);
		$courses = get_posts( $courses_args );
		
		if(isset($courses) && !empty($courses)) {
			
			echo '<table border="0" cellpadding="0" cellspacing="0">';
			
			echo '<tr>
					<th>'.__('Course Name', 'dt_themes').'</th>
					<th>'.__('Subscriptions', 'dt_themes').'</th>
					<th>'.__('Completed', 'dt_themes').'</th>
				</tr>';
			
			foreach($courses as $course) {
				
				$course_id = $course->ID;
				
				$student_cap = dt_get_course_capabilities_id($course_id);
				$course_subscribed = count($student_cap);
				
				$course_completed = dt_get_course_completed_list($course_id, 'count');
								
				echo '<tr>
						<td><a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_course&course_id='.$course_id).'">'.$course->post_title.'</a></td>
						<td>'.$course_subscribed.'</td>
						<td>'.$course_completed.'</td>
					</tr>';

			}
			
			echo '</table>';
		
		}
		
		echo '<a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_teachers').'"  class="dt-statistics-button">'.__('All Teachers', 'dt_themes').'</a>';
		
	} else {
		
		echo '<div id="dt-sc-ajax-load-image" style="display:none;"><img src="'.IAMD_BASE_URL."images/loading.png".'" alt="" /></div>';
		echo '<div id="dt-statistics-teachers-container">';
		dt_get_statistics_teachers_list(10, 1);
		echo '</div>';
	
	}
	
	echo '</div>';
	
}

function dt_get_statistics_graph(){
	
	echo '<div class="dt-overallstatistics-container">';
	
	echo '<div id="dt-statistics-graph-options">';
	
		echo '<select id="dt-graph-type" name="dt-graph-type" style="width:40%;">';
			echo '<option value="course">' . __('Course', 'dt_themes') . '</option>';
			echo '<option value="teacher">' . __('Teacher', 'dt_themes') . '</option>';
			echo '<option value="student">' . __('Student', 'dt_themes') . '</option>';
		echo '</select>';
		echo '<br /><br />';

		
		echo '<div class="dt-graph-option dt-course-graph">';	
		
			echo __('Include course with zero sales', 'dt_themes').'<input type="checkbox" name="dt-include-zero-course" id="dt-include-zero-course" value="1" class="dt-include-zero" />';
			echo '<br /><br />';
			
			$course_args = array('posts_per_page' => -1, 'post_type' => 'dt_courses', 'orderby' => 'title', 'order' => 'ASC');
			$courses = get_posts( $course_args );
	
			if(isset($courses) && !empty($courses)) {
			
				echo '<select id="dt-select-course" name="dt-select-course[]" style="width:40%;" multiple data-placeholder="'.__('Include Courses Manually...', 'dt_themes').'" class="dt-chosen-select">';
				echo '<option value=""></option>';
				
				foreach ($courses as $course){
					echo '<option value="' . esc_attr( $course->ID ) . '">' . esc_html( $course->post_title ) . '</option>';
				}
				
				echo '</select>';
				echo '<br /><br />';
			
			}
		
		echo '</div>';
		
		echo '<div class="dt-graph-option dt-teacher-graph">';
		
			echo __('Include teacher with zero submissions', 'dt_themes').'<input type="checkbox" name="dt-include-zero-teacher" id="dt-include-zero-teacher" value="1" class="dt-include-zero" />';
			echo '<br /><br />';
			
			$teachers = get_users( array('role' => 'teacher') );
			
			if(isset($teachers) && !empty($teachers)) {
				
				echo '<select id="dt-select-teacher" name="dt-select-teacher[]" style="width:40%;" multiple data-placeholder="'.__('Include Teachers Manually...', 'dt_themes').'" class="dt-chosen-select">';
				echo '<option value=""></option>';
				
				foreach($teachers as $teacher) {
					$teacher_id = $teacher->data->ID;
					$teacher_name = $teacher->data->display_name;
					echo '<option value="' . esc_attr( $teacher_id ) . '">' . esc_html( $teacher_name ) . '</option>';
				}
				
				echo '</select>';
				echo '<br /><br />';
				
			}
		
		echo '</div>';
		
		echo '<div class="dt-graph-option dt-student-graph">';		
		
			echo __('Include student with zero subsciptions', 'dt_themes').'<input type="checkbox" name="dt-include-zero-student" id="dt-include-zero-student" value="1" class="dt-include-zero" />';
			echo '<br /><br />';
			
			$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
			
			if(isset($students) && !empty($students)) {
			
				echo '<select id="dt-select-student" name="dt-select-student[]" style="width:40%;" multiple data-placeholder="'.__('Include Students Manually...', 'dt_themes').'" class="dt-chosen-select">';
				echo '<option value=""></option>';
				
				foreach ($students as $student){
					$student_id = $student->data->ID;
					$student_name = $student->data->display_name;
					echo '<option value="' . esc_attr( $student_id ) . '">' . esc_html( $student_name ) . '</option>';
				}
				
				echo '</select>';
				echo '<br /><br />';
			
			}
		
		echo '</div>';
		
		
		echo '<input type="button" name="dt-graph-generate" id="dt-graph-generate" value="'.__('Generate Graph', 'dt_themes').'" />';
		echo '<br /><br /><br />';
		
	echo '</div>';
	
	echo '<div id="dt-sc-ajax-load-image" style="display:none;"><img src="'.IAMD_BASE_URL."images/loading.png".'" alt="" /></div>';
	echo '<div id="dt-statistics-graph-container">';
	dt_get_statistics_graph_data('course', 0, '');
	echo '</div>';
	
	echo '</div>';
	
}

function dt_get_user_completed_course_count($student_id){
	
	$student_level = get_user_field ("s2member_access_role", $student_id);
	
	if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') { $student_cap = dt_get_all_paid_courses(); }
	else { $student_cap = get_user_field ("s2member_access_ccaps", $student_id); $student_cap = dt_remove_cid($student_cap); }

	$i = 0;
	foreach($student_cap as $course_id) {
		if(dt_get_users_course_status($course_id, $student_id))
			$i++;
	}
	
	return $i;

}

function dt_get_course_completed_list($course_id, $type){
	
	$students_list = array();
	
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	
	$i = 0;
	foreach($students as $student) {
		$student_id = $student->data->ID;
		if(dt_get_users_course_status($course_id, $student_id)) {
			$students_list[] = $student_id;
			$i++;
		}
	}
	
	if($type == 'count')
		return $i;
	else
		return $students_list;
		
}

function dt_get_course_certificates_count($course_id){
	
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	
	$enable_certificate = get_post_meta($course_id, 'enable-certificate', true);
	
	$i = 0;
	if(isset($enable_certificate) && $enable_certificate != '') {
	
		$certificate_percentage = dttheme_wp_kses(get_post_meta($course_id, 'certificate-percentage', true));
		foreach($students as $student) {
			
			$student_id = $student->data->ID;
			$course_percent = dt_get_course_percentage($course_id, $student_id);
			if($course_percent > 0 && $course_percent >= $certificate_percentage)
				$i++;
			
		}
	
	}
	
	return $i;

}

function dt_get_course_badges_count($course_id){
	
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	
	$enable_badge = get_post_meta($course_id, 'enable-badge', true);
	
	$i = 0;
	if(isset($enable_badge) && $enable_badge != '') {
	
		$badge_percentage = dttheme_wp_kses(get_post_meta($course_id, 'badge-percentage', true));
		
		foreach($students as $student) {
			$student_id = $student->data->ID;
			
			$course_percent = dt_get_course_percentage($course_id, $student_id);
			
			if($course_percent > 0 && $course_percent >= $badge_percentage)
				$i++;
			
		}
	
	}
	
	return $i;

}

function dt_get_statistics_courses_list($post_per_page, $curr_page) {
	
	$offset = (($curr_page-1)*$post_per_page);
	
	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col">'.__('Course', 'dt_themes').'</th>
				<th scope="col">'.__('Teacher', 'dt_themes').'</th>
				<th scope="col">'.__('Students Subscribed #', 'dt_themes').'</th>
				<th scope="col">'.__('Students Completed #', 'dt_themes').'</th>
				<th scope="col">'.__('Certificates #', 'dt_themes').'</th>
				<th scope="col">'.__('Badges #', 'dt_themes').'</th>
			  </tr>';

	$course_args = array('offset'=>$offset, 'paged' => $curr_page ,'posts_per_page' => $post_per_page, 'post_type' => 'dt_courses', 'orderby' => 'title', 'order' => 'DESC');
	$courses = get_posts( $course_args );

	$i = 0;
	if(isset($courses) && !empty($courses)) {
		
		foreach($courses as $course) {
			
			$course_id = $course->ID;
			
			$course_data = get_post( $course_id );
			$teacher_id = $course_data->post_author;
			$teacher_info = get_userdata($teacher_id);
			$teacher_name = $teacher_info->display_name;				
			
			$student_cap = dt_get_course_capabilities_id($course_id);
			$course_subscribed = count($student_cap);
			
			$course_completed = dt_get_course_completed_list($course_id, 'count');
			$course_certificates = dt_get_course_certificates_count($course_id);
			$course_badges = dt_get_course_badges_count($course_id);
		
			echo '<tr>
					<td>'.($i+1).'</td>
					<td><a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_course&course_id='.$course_id).'">'.$course->post_title.'</a></td>
					<td><a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_teachers&teacher_id='.$teacher_id).'">'.$teacher_name.'</a></td>
					<td>'.$course_subscribed.'</td>
					<td>'.$course_completed.'</td>
					<td>'.$course_certificates.'</td>
					<td>'.$course_badges.'</td>
				</tr>';
				
			$i++;	
			
		}
		
	}
	
	if($i == 0)
		echo '<tr><td colspan="5">'.__('No Records Found!', 'dt_themes').'</td></tr>';
	
	echo '</table>';
	
	$course_args = array('post_type' => 'dt_courses', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'DESC');
	$courses = get_posts( $course_args );

	echo dtthemes_ajax_pagination($post_per_page, $curr_page, count($courses), 0);
	
}

function dt_get_statistics_students_list($post_per_page, $curr_page) {
	
	$offset = (($curr_page-1)*$post_per_page);
	
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	$students = array_splice($students, $offset, $post_per_page);
	
	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col">'.__('Student', 'dt_themes').'</th>
				<th scope="col">'.__('Registered Date', 'dt_themes').'</th>
				<th scope="col">'.__('Subscribed Courses', 'dt_themes').'</th>
				<th scope="col">'.__('Completed Courses', 'dt_themes').'</th>
			  </tr>';
	
	$i = 0;
	if(isset($students) && !empty($students)) {
		
		foreach($students as $student) {
			
			$student_id = $student->data->ID;
			
			$student_level = get_user_field ("s2member_access_role", $student_id);
			
			if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') $student_cap = dt_get_all_paid_courses(); 
			else $student_cap = get_user_field ("s2member_access_ccaps", $student_id);
			
			$course_completed  = dt_get_user_completed_course_count($student_id);
			
			echo '<tr>
					<td>'.($i+1).'</td>
					<td><a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_students&student_id='.$student_id).'">'.$student->data->display_name.'</a></td>
					<td>'.$student->data->user_registered.'</td>
					<td>'.count($student_cap).'</td>
					<td>'.$course_completed.'</td>
				</tr>';
				
			$i++;	
			
		}
		
	}
	
	if($i == 0)
		echo '<tr><td colspan="5">'.__('No Records Found!', 'dt_themes').'</td></tr>';
	
	echo '</table>';
	
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, count($students), 0);

}

function dt_get_statistics_teachers_list($post_per_page, $curr_page) {
	
	$offset = (($curr_page-1)*$post_per_page);
	
	$teachers = get_users( array('offset' => $offset, 'number' => $post_per_page, 'role' => 'teacher') );
	
	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col">'.__('Teacher', 'dt_themes').'</th>
				<th scope="col">'.__('Registered Date', 'dt_themes').'</th>
				<th scope="col">'.__('Courses Submitted', 'dt_themes').'</th>
				<th scope="col">'.__('Assignments Submitted', 'dt_themes').'</th>
			  </tr>';
	
	$i = $total_courses_submitted = $total_assignments_submitted = 0;
	if(isset($teachers) && !empty($teachers)) {
		
		foreach($teachers as $teacher) {
			
			$total_courses = $total_assignments = 0;
			$teacher_id = $teacher->data->ID;
			
			$courses_args = array( 'post_type' => 'dt_courses', 'post_status' => 'publish', 'author' => $teacher_id);
			$courses = get_posts( $courses_args );
			$total_courses = count($courses);
			
			$assignments_args = array( 'post_type' => 'dt_assignments', 'post_status' => 'publish', 'author' => $teacher_id);
			$assignments = get_posts( $assignments_args );
			$total_assignments = count($assignments);
			
			echo '<tr>
					<td>'.($i+1).'</td>
					<td><a href="'.admin_url('admin.php?page=dt-statistics-options&tab=dt_statistics_teachers&teacher_id='.$teacher_id).'">'.$teacher->data->display_name.'</a></td>
					<td>'.$teacher->data->user_registered.'</td>
					<td>'.$total_courses.'</td>
					<td>'.$total_assignments.'</td>
				</tr>';
				
			$i++;	
			
		}
		
	}
	
	if($i == 0)
		echo '<tr><td colspan="5">'.__('No Records Found!', 'dt_themes').'</td></tr>';
	
	echo '</table>';
	
	$teachers = get_users( array('role' => 'teacher') );
	
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, count($teachers), 0);
	
}

function dt_get_statistics_graph_data($graph_type = 'course', $include_zero_sales = 0, $selectedItems = '') {
	
	$graph_title = $graph_data = $graph_data2 = '';
	
	if($graph_type == 'course') {
		
		if(isset($selectedItems) && !empty($selectedItems))
			$course_args = array('include' => $selectedItems, 'posts_per_page' => -1, 'post_type' => 'dt_courses', 'orderby' => 'title', 'order' => 'ASC');
		else
			$course_args = array('posts_per_page' => -1, 'post_type' => 'dt_courses', 'orderby' => 'title', 'order' => 'ASC');
			
		$courses = get_posts( $course_args );
	
		if(isset($courses) && !empty($courses)) {
			
			$course_title = $courses_subscribed = array();
			foreach($courses as $course) {
				
				$course_id = $course->ID;
				$student_cap = dt_get_course_capabilities_id($course_id);
				
				$subscription = count($student_cap);
				
				if($include_zero_sales == 1 || $subscription > 0) {
					$courses_subscribed[] = $subscription;
					$course_title[] = '"'.$course->post_title.'"';
				}
				
			}
			
			$graph_title = implode(',', $course_title);
			$graph_data = implode(',', $courses_subscribed);
			
			echo '<h3>'.__('Courses Vs Subscriptions (sales)', 'dt_themes').'</h3>';
			
			echo '<div class="dt-graph-marker">';
			echo '<div class="dt-graph-marker-box"> <div style="background-color:rgba(14,81,124,1); width:20px; height:20px;"></div></div> - '.__('Total Subscribtions (Sales)', 'dt_themes');
			echo '</div>';
			
		}
		
	} else if($graph_type == 'teacher') {
		
		if(isset($selectedItems) && !empty($selectedItems))
			$teachers = get_users( array('include' => $selectedItems, 'role' => 'teacher') );
		else
			$teachers = get_users( array('role' => 'teacher') );
		
		if(isset($teachers) && !empty($teachers)) {
			
			$teacher_names = $total_courses = $total_subscription = array();
			foreach($teachers as $teacher) {
				
				$teacher_id = $teacher->data->ID;
				$teacher_name = $teacher->data->display_name;
				
				$courses_args = array( 'post_type' => 'dt_courses', 'post_status' => 'publish', 'author' => $teacher_id);
				$courses = get_posts( $courses_args );
				$total_course = count($courses);
				
				
				
				if($include_zero_sales == 1 || $total_course > 0) {
					
					$teacher_names[] = '"'.$teacher_name.'"';
					$total_courses[] = $total_course;
					
					$total_course_subscribed = 0;
					if(isset($courses) && !empty($courses)) {
						foreach($courses as $course) {
							
							$course_id = $course->ID;
							
							$student_cap = dt_get_course_capabilities_id($course_id);
							$course_subscribed = count($student_cap);
							$total_course_subscribed = $total_course_subscribed + $course_subscribed; 
			
						}
					}
					
					$total_subscription[] = $total_course_subscribed;
					
				}
				
			}
			
			$graph_title = implode(',', $teacher_names);
			$graph_data = implode(',', $total_courses);
			$graph_data2 = implode(',', $total_subscription);
			
			echo '<h3>'.__('Teachers Vs Courses Submitted and Courses Total Subscriptions', 'dt_themes').'</h3>';
			
			echo '<div class="dt-graph-marker">';
			echo '<div class="dt-graph-marker-box"> <div style="background-color:rgba(14,81,124,1); width:20px; height:20px;"></div></div> - '.__('Total Courses Submitted', 'dt_themes');
			echo '</div>';
			echo '<div class="dt-graph-marker">';
			echo '<div class="dt-graph-marker-box"> <div style="background-color:rgba(51,167,227,1); width:20px; height:20px;"></div></div> - '.__('Total Subscribtions (Sales)', 'dt_themes');
			echo '</div>';
			
		}
		
	} else if($graph_type == 'student') {
		
		if(isset($selectedItems) && !empty($selectedItems))
			$students = array_merge(get_users(array('include' => $selectedItems, 'role' => 's2member_level1')), get_users(array('include' => $selectedItems, 'role' => 's2member_level2')), get_users(array('include' => $selectedItems, 'role' => 's2member_level3')), get_users(array('include' => $selectedItems, 'role' => 's2member_level4')));
		else
			$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));

		if(isset($students) && !empty($students)) {

			$student_names = $total_courses = $courses_completed = array();
			foreach($students as $student) {
				
				$student_id = $student->data->ID;
				$student_name = $student->data->display_name;
				
				$student_level = get_user_field ("s2member_access_role", $student_id);
				
				if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') $student_cap = dt_get_all_paid_courses(); 
				else $student_cap = get_user_field ("s2member_access_ccaps", $student_id);
				
				$total_course = count($student_cap);
				$course_completed  = dt_get_user_completed_course_count($student_id);
				
				if($include_zero_sales == 1 || $total_course > 0) {
					
					$student_names[] = '"'.$student_name.'"';
					$total_courses[] = $total_course;
					$courses_completed[] = $course_completed;
					
				}
				
			}
			
			$graph_title = implode(',', $student_names);
			$graph_data = implode(',', $total_courses);
			$graph_data2 = implode(',', $courses_completed);
			
			echo '<h3>'.__('Students Vs Courses Subscribed and Courses Completed', 'dt_themes').'</h3>';
			
			echo '<div class="dt-graph-marker">';
			echo '<div class="dt-graph-marker-box"> <div style="background-color:rgba(14,81,124,1); width:20px; height:20px;"></div></div> - '.__('Total Courses Subscribed', 'dt_themes');
			echo '</div>';
			
			echo '<div class="dt-graph-marker">';
			echo '<div class="dt-graph-marker-box"> <div style="background-color:rgba(51,167,227,1); width:20px; height:20px;"></div></div> - '.__('Courses Completed', 'dt_themes');
			echo '</div>';
			

		}
		
	}
		
	if($graph_title != '') {
		
		echo '<div style="width: 90%; height:80%" class="dt-chart-container"><canvas id="dt-chart"></canvas></div>';

		echo '<script>
		
				var dtChartData = {
					labels : ['.$graph_title.'],
					datasets : [
						{
							fillColor : "rgba(14,81,124,1)",
							strokeColor : "rgba(1,65,109,1)",
							highlightFill: "rgba(1,65,109,1)",
							highlightStroke: "rgba(1,56,93,1)",
							data : ['.$graph_data.']
						},';
						
				if($graph_data2 != '') {
				
					echo ' {
							fillColor : "rgba(51,167,227,1)",
							strokeColor : "rgba(43,138,189,1)",
							highlightFill: "rgba(43,138,189,1)",
							highlightStroke: "rgba(40,129,176,1)",
							data : ['.$graph_data2.']
						}';		

				}
						
		echo '		]
			
				}

				window.onload = function(){
					var dtChart = document.getElementById("dt-chart").getContext("2d");
					window.dtBar = new Chart(dtChart).Bar(dtChartData, {
							responsive : true,
						});
				}
				
			</script>';
			
	} else {
		echo 'NoData';	
	}
			
}

?>