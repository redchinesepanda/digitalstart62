<?php
/*
 * Шорткод [ds62-projects-total] для вывода общего количества проектов пользователя
 */
add_shortcode( 'ds62-projects-total', 'ds62_projects_total_shortcode' );
function ds62_projects_total_shortcode( $atts ) {
	$message = array('<pre>');
	array_push($message, 'ds62_projects_total_shortcode');
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	$args = array(
		'post_type' => 'ds62-project'
	);
	if (
		!in_array( 'administrator', $user_roles )
	) {
		$user_id = $user->ID;
		array_push($message, 'user_id: ' . $user_id);
		$meta_conditions = ds62_project_filter($user_id);
		$args['meta_query'] = $meta_conditions;
	}
	array_push($message, 'args: ' . print_r($args, true));
	$query = new WP_Query( $args );
	$total = $query->found_posts;
	array_push($message, 'total: ' . $total);
	array_push($message, '</pre>');
	//if( current_user_can('administrator') ){
	//	echo implode('<br />', $message);
	//}
	return $total;
};

/*
 * Шорткод [ds62-post-type-class] для заголовков формы
 */
add_shortcode( 'ds62-post-type-class', 'ds62_post_type_class_shortcode' );
function ds62_post_type_class_shortcode( $atts ){
	$message = array('<pre>');
	array_push($message, 'ds62_post_type_class_shortcode');
	$classes = array(
		'ds62-project',
		'ds62-stage',
		'ds62-task',
	);
	$result = 'ds62-post';
	$post_type = get_post_type();
	array_push($message, 'post_type: ' . $post_type);
	if (in_array($post_type, $classes)) {
		$result = $post_type;
	}
	array_push($message, '</pre>');
	//if( current_user_can('administrator') ){
	//	echo implode('<br />', $message);
	//}
	return $result;
}

/*
 * Шорткод [ds62-completed] для dsdjlf состояния
 */
add_shortcode( 'ds62-completed', 'ds62_completed_shortcode' );
function ds62_completed_shortcode( $atts ) {
	$message = array('<pre>');
	array_push($message, 'ds62_completed_shortcode');
	$post = get_post();
	$value = get_post_meta( $post->ID, 'ds62-item-state', true );
	array_push($message, 'post->ID: ' . $post->ID);
	array_push($message, 'value: ' . $value);
	$result = 'Нет';
	if ($value == true) {
		$result = 'Да';
	}
	array_push($message, '</pre>');
	//if( current_user_can('administrator') ){
	//	echo implode('<br />', $message);
	//}
	return $result;
}


/*
 * Шорткод [ds62-acf-state-form] для вывода формы смены состояния текущей задачи
 */
add_shortcode( 'ds62-acf-state-form', 'ds62_acf_state_form_shortcode' );
function ds62_acf_state_form_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_acf_state_form_shortcode<br />';
	$post = get_post();
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$state = '';
	if (
		in_array(get_post_type($post_id), array('ds62-task'))
	) {
		$state = get_post_meta($post_id, 'ds62-task-state', true );
	}
	//echo 'state: ' . $state . '^<br />';
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	//echo 'user_roles:<br />';
	//print_r($user_roles);
	//echo '</pre>';
	if (in_array($post_type, array('ds62-task'))) {
		$output = 'Вы не можете поменять состояние выполненной задачи';
		if (
			$state != 'done'
			|| !in_array( 'ds_project_participant', $user_roles )
		) {
			acf_form_head();
			acf_form(array(
				'id' => 'ds62-acf-state-form',
				'fields' => array('ds62-task-state'),
				'submit_value'  => 'Обновить состояние'
			));
			$output = '';
		}
	}
	return $output;
}


/*
 * Шорткод [ds62-form-head] для заголовков формы
 */
add_shortcode( 'ds62-form-head', 'ds62_form_head_shortcode' );
function ds62_form_head_shortcode( $atts ){
	acf_form_head();
	acf_enqueue_uploader();
	return '';
}

/*
 * Шорткод [ds62-custom-state-form] для вывода формы смены состояния текущей задачи
 */
add_shortcode( 'ds62-custom-state-form', 'ds62_custom_state_form_shortcode' );
function ds62_custom_state_form_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_custom_state_form_shortcode<br />';
	$post = get_post();
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$state = '';
	if (
		in_array(get_post_type($post_id), array('ds62-task'))
	) {
		$state = get_post_meta($post_id, 'ds62-task-state', true );
	}
	//echo 'state: ' . $state . '^<br />';
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	//echo 'user_roles:<br />';
	//print_r($user_roles);
	//echo '</pre>';
	if (in_array($post_type, array('ds62-task'))) {
		$output = 'Вы не можете поменять состояние выполненной задачи';
		if (
			$state != 'done'
			|| !in_array( 'ds_project_participant', $user_roles )
		) {
			acf_form_head();
			acf_form(array(
				'id' => 'ds62-acf-state-form',
				'fields' => array('ds62-task-state'),
				'submit_value'  => 'Обновить состояние',
				'return' => '/tasker/'
			));
			/*$htmlForm = "<div class='ds62-update-wrapper'>
							<div class='ds62-update-label'>
								<label for='ds62-update-selector'>Выберите состояние</label>
							</div>";
			$htmlForm .= 	"<div class='ds62-update-input'>
								<select class='ds62-update-selector' name='ds62-update-selector'>
									<option value='avaible'";
							if ($state == 'avaible') {
								$htmlForm .= 'selected="selected"';
							}
							$htmlForm .= 	">В очереди</option>
									<option value='progress'";
									if ($state == 'progress') {
								$htmlForm .= 'selected="selected"';
							}
							$htmlForm .= 	">В работе</option>
									<option value='check'";
									if ($state == 'check') {
								$htmlForm .= 'selected="selected"';
							}
							$htmlForm .= 	">На проверку</option>
									<option value='done'";
									if ($state == 'done') {
								$htmlForm .= 'selected="selected"';
							}
							$htmlForm .= 	">Завершенная</option>
								</select>
							</div>";
			$htmlForm .= 	"<div class='ds62-update-submit'>
								<input type='button' value='Обновить состояние'>
							</div>   
						</div>";
			echo $htmlForm;*/
			$output = '';
		}
	}
	return $output;
}

/*
 * Шорткод [ds62-acf-author-box] для вывода куратора проекта, менеджера глобальной задачи, первого исполнителя задачи
 */
add_shortcode( 'ds62-acf-author-box', 'ds62_acf_author_box_shortcode' );
function ds62_acf_author_box_shortcode( $atts ){
	//echo '<pre>';
	$output = 'Пользователь не найден';
	//echo 'ds62_acf_state_form_shortcode<br />';
	$post = get_post();
	$post_id = $post->ID;
	$post_type = $post->post_type;
	//echo 'post_type: ' . $post_type . '^<br />';
	$user_ids = array(0);
	if (in_array($post_type, array('ds62-project', 'ds62-stage')) ) {
		$field = 'ds62-project-curator';
		if (in_array($post_type, array('ds62-stage')) ) {
			$stage_name = 'ds62-stage-project';
			$post_id = wpuf_get_stage_meta($post_id, $stage_name);
			$field = 'ds62-project-manager';
		}
		$user_ids = wpuf_get_project_meta($post_id, $field);
	}
	if (in_array($post_type, array('ds62-task')) ) {
		$field = 'ds62-task-members';
		$user_ids = get_post_meta($post_id, $field, true );
	}
	switch($post_type) {
		case 'ds62-project':
			$user_role = '<div class="ds62-user-role">Куратор:</div>';
			break;
		case 'ds62-stage':
			$user_role = '<div class="ds62-user-role">Руководитель:</div>';
			break;
		case 'ds62-task':
			$user_role = '<div class="ds62-user-role">Исполнитель:</div>';
			break;
	}
	$user_id = $user_ids[0];
	//echo 'user_id: ' . $user_id . '<br />';
	if (!empty($user_id)) {
		$output = '<div class="ds62-author-box">';
		$user_data = get_userdata( $user_id );
		$user_display_name = '<div class="ds62-user-display-name">' . $user_data->display_name . '</div>';
		$output .= $user_display_name;
		//echo 'user_display_name: ' . $user_display_name . '^<br />';
		
		$output .= $user_role;
		$user_url = get_avatar_url( $user_id, "size=48");
		//echo 'user_url: ' . $user_url . '^<br />';
		$user_avatar = '<div class="ds62-user-avatar"><img alt="" src="'. $user_url .'"></div>';
		$output .= $user_avatar;
		$output .= '</div>';
		//echo 'user_data:<br />';
		//print_r($user_data);
	}
	//echo '</pre>';
	return $output;
}


/*
 * Шорткод [ds62-acf-notified] для удаления пользователя из списка уведомлений
 */
add_shortcode( 'ds62-acf-notified', 'ds62_acf_notified_shortcode' );
function ds62_acf_notified_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_acf_notified_shortcode<br />';
	$output = 'Нет новых обновлений';
	$user_id = get_current_user_id();
	//echo 'user_id: ' . $user_id . '^<br />';
	$post = get_post();
	$post_id = $post->ID;
	$notify_users = get_post_meta( $post_id, 'ds62-task-notify', true );
	//echo 'notify_users:<br />';
	//print_r($notify_users);
	if (!empty($notify_users)) {
		if (($key = array_search($user_id, $notify_users)) !== false) {
			unset($notify_users[$key]);
			update_post_meta($post_id, 'ds62-task-notify', $notify_users);
			$output = 'Ознакомлен с обновлениями';
		}
	}
	//echo '</pre>';
	return $output;
}

/*
 * Шорткод [ds62-acf-notification-class] для вывода класса уведомления если текущий пользователь в списке нотификации
 */
add_shortcode( 'ds62-acf-notification-class', 'ds62_acf_notification_class_shortcode' );
function ds62_acf_notification_class_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_acf_notification_class_shortcode<br />';
	$output = '';
	$user_id = get_current_user_id();
	//echo 'user_id: ' . $user_id . '^<br />';
	$post = get_post();
	$post_id = $post->ID;
	//echo 'post_id: ' . $post_id . '^<br />';
	/*if (in_array($post->post_type, array('ds62-task'))) {
		ds62_notify_meta($post_id);
	}*/
	$notify_users = get_post_meta( $post_id, 'ds62-task-notify', true );
	//echo 'notify_users:<br />';
	//print_r($notify_users);
	//$key = array_search($user_id, $notify_users);
	//echo 'key: ' . $key . '^<br />';
	//if ($key !== null) {
	if (in_array($user_id, $notify_users)) {
		$output = 'ds62-notification';
	}
	//echo 'output: ' . $output  . '^<br />';
	//echo '</pre>';
	return $output;
}

/*
 * Шорткод [ds62-acf-done-class] для вывода класса уведомления если текущий пользователь в списке нотификации
 */
add_shortcode( 'ds62-acf-done-class', 'ds62_acf_done_class_shortcode' );
function ds62_acf_done_class_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_acf_done_class_shortcode<br />';
	$output = '';
	//$user_id = get_current_user_id();
	//echo 'user_id: ' . $user_id . '^<br />';
	$post = get_post();
	$post_id = $post->ID;
	//echo 'post_id: ' . $post_id . '^<br />';
	/*if (in_array($post->post_type, array('ds62-task'))) {
		ds62_notify_meta($post_id);
	}*/
	$done = get_post_meta( $post_id, 'ds62-item-state', true );
	//echo 'notify_users:<br />';
	//print_r($notify_users);
	//$key = array_search($user_id, $notify_users);
	//echo 'key: ' . $key . '^<br />';
	//if ($key !== null) {
	if (!empty($done)) {
		$output = 'ds62-done';
	}
	//echo 'output: ' . $output  . '^<br />';
	//echo '</pre>';
	return $output;
}

/*
 * WPUF Шорткод [ds62-frontend-edit], выводящий ссылку для редактирования поста
 */
add_shortcode( 'ds62-frontend-edit', 'ds62_frontend_edit_shortcode' );
function ds62_frontend_edit_shortcode( $atts ){
	$post_id = get_the_ID();
	$url_params = array('pid' => $post_id);
	/*if (get_post_type($post_id) == 'ds62-task') {
		$project_id = wpuf_get_project_id($post_id);
		if (!empty($project_id)) {
			$url_params['project_id'] = $project_id;
		}
	}*/
	$url = add_query_arg($url_params, get_permalink( $post_id ) );
	$edit_page_url = apply_filters( 'wpuf_edit_post_link', $url );
	$output = wp_nonce_url( $edit_page_url, 'wpuf_edit' );
	return $output;
}

/*
 * WPUF Шорткод [ds62-frontend-logout], выводящий ссылку для выхода
 */
add_shortcode( 'ds62-frontend-logout', 'ds62_frontend_logout_shortcode' );
function ds62_frontend_logout_shortcode( $atts ){
	//$post_id = get_the_ID();
	//$url_params = array('action' => 'logout');
	/*if (get_post_type($post_id) == 'ds62-task') {
		$project_id = wpuf_get_project_id($post_id);
		if (!empty($project_id)) {
			$url_params['project_id'] = $project_id;
		}
	}*/
	//$url = add_query_arg($url_params, get_site_url() );
	//$edit_page_url = apply_filters( 'wpuf_edit_post_link', $url );
	//$output = wp_nonce_url( $edit_page_url, 'wpuf_edit' );
	$output = wp_logout_url();
	return $output;
}

/*
 * Шорткод [wpuf-get-project-state-json] WPUF получаем итоговый статус задачи в формате json. Результат: array('<состояние>' => <количество>)
 */
add_shortcode( 'wpuf-get-project-state-json', 'wpuf_get_project_state_json' );
function wpuf_get_project_state_json($args) {
    $state = array('Не является проектом');
    $post = get_post();
    $post_type = $post->post_type;
    if (in_array($post_type, array('ds62-project'))) {
        $post_id = $post->ID;
        $state = wpuf_get_project_state($post_id);
		return wp_json_encode($state);
    }else{
		return false;
	}
    
}

/*
 * Шорткод [wpuf-get-stage-state-json] WPUF получаем итоговый статус задачи в формате json. Результат: array('<состояние>' => <количество>)
 */
add_shortcode( 'wpuf-get-stage-state-json', 'wpuf_get_stage_state_json' );
function wpuf_get_stage_state_json($args) {
    $state = array('Не является глобальной задачей');
    $post = get_post();
    $post_type = $post->post_type;
    if (in_array($post_type, array('ds62-stage'))) {
        $post_id = $post->ID;
        $state = wpuf_get_stage_state($post_id);
		return wp_json_encode($state);
    } else {
		return false;
	}
    //return wp_json_encode($state);
}

/*
 * Шорткод [ds62-diagram] для вывода диаграммы куратора
 */
add_shortcode( 'ds62-diagram', 'ds62_diagram_shortcode' );
function ds62_diagram_shortcode( $atts ) {
	$message = array('<pre>');
	array_push($message, 'ds62_diagram_shortcode');
	$result = array();
	$meta_conditions = array();
	$post_types = array(
		'ds62-project',
		'ds62-stage',
		'ds62-task',
	);
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	//array_push($message, 'user_roles: ' . print_r($user_roles, true));
	$user_id = $user->ID;
	array_push($message, 'user_id: ' . $user_id);
	foreach ($post_types as $post_type) {
		array_push($message, 'post_type: ' . $post_type);
		$args = array(
			'post_type' => $post_type
		);
		if (!in_array('administrator', $user_roles)) {
			if (in_array($post_type, array('ds62-project'))) {
				$args['meta_query'] = ds62_project_filter($user_id);
			}
			if (in_array($post_type, array('ds62-stage'))) {
				$args['meta_query'] = array(
					array(
						'key' => 'ds62-stage-project',
						'value' => $post_type_ids,
						'compare' => 'IN',
					)
				);
			}
			if (in_array($post_type, array('ds62-task'))) {
				$args['meta_query'] = array(
					//'relation' => 'AND',
					array(
						'key' => 'ds62-task-stage',
						'value' => $post_type_ids,
						'compare' => 'IN',
					),
					//array(
					//	'key' => 'ds62-item-date-start',
					//)
				);
				
			}
		}
		if (in_array($post_type, array('ds62-task'))) {
			$args['meta_key'] = 'ds62-item-date-start';
			$args['orderby'] = 'meta_value';
			//$args['orderby'] = 'meta_value_num';
			//$args['order'] = 'DESC';
			$args['order'] = 'ASC';
		}
		array_push($message, 'args: ' . print_r($args, true));
		$query = new WP_Query();
		$posts = $query->query( $args );
		$post_type_ids = array();
		foreach( $posts as $post ){
			//echo esc_html( $post->post_title );
			array_push($message, 'post_title: ' . $post->post_title);
			array_push($message, 'ID: ' . $post->ID);
			array_push($message, 'ds62-item-date-start: ' .  get_post_meta($post->ID, 'ds62-item-date-start', true));
			array_push($post_type_ids, $post->ID);
			if (in_array($post_type, array('ds62-task'))) {
				$members = get_post_meta($post->ID, 'ds62-task-members', true);
				//array_push($message, 'members: ' . print_r($members, true));
				//$member_data = array();
				foreach ($members as $member) {
					$user_info = get_userdata($member);
					//$result[$member] = array(
					if (!array_key_exists($user_info->display_name, $result)) {
						$result[$user_info->display_name] = array(
							//'user_display_name' => $user_info->display_name,
							'user_id' => $member,
							'tasks' => array(),
						);
					}
					array_push(
					//$result[$member]['tasks'],
					$result[$user_info->display_name]['tasks'],
					array(
						'task_id' => $post->ID,
						'task_title' => $post->post_title,
						'task_state' => get_post_meta($post->ID, 'ds62-task-state', true),
						'task_date_start' => get_post_meta($post->ID, 'ds62-item-date-start', true),
						'task_date_end' => get_post_meta($post->ID, 'ds62-item-date-end', true),
					)
				);
				}
				//array_push($message, 'member_data: ' . print_r($member_data, true));
				
			}
		}
		array_push($message, 'post_type_ids ' . $post_type . ': ' .print_r($post_type_ids, true));
	}
	array_push($message, 'result: ' . print_r($result, true));
	array_push($message, '</pre>');
	//if( current_user_can('administrator') ){
	//	echo implode('<br />', $message);
	//}
	return json_encode($result);
};

/*
 * Шорткод [ds62-acf-task-state-class] для вывода класса задачи
 */
add_shortcode( 'ds62-acf-task-state-class', 'ds62_acf_task_state_class_shortcode' );
function ds62_acf_task_state_class_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_acf_task_state_class_shortcode<br />';
	$output = '';
	//$user_id = get_current_user_id();
	//echo 'user_id: ' . $user_id . '^<br />';
	$post = get_post();
	$post_id = $post->ID;
	//echo 'post_id: ' . $post_id . '^<br />';
	/*if (in_array($post->post_type, array('ds62-task'))) {
		ds62_notify_meta($post_id);
	}*/
	$state = get_post_meta( $post_id, 'ds62-task-state', true );
	//echo 'notify_users:<br />';
	//print_r($notify_users);
	//$key = array_search($user_id, $notify_users);
	//echo 'key: ' . $key . '^<br />';
	//if ($key !== null) {
	if (empty($state)) {
		$state = 'undefined';
	}
	$output = 'ds62-state-' . $state;
	//echo 'output: ' . $output  . '^<br />';
	//echo '</pre>';
	return $output;
}

?>