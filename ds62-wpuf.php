<?php
/*
 * WPUF, ACF генерация фильтра для хапроса постов
 */
function wpuf_generate_post_args($settings) {
	//echo '<pre>';
	//echo 'wpuf_generate_post_args<br />';
	//echo 'settings<br />';
	//print_r($settings);
	$post_args = array(
		'post_type' => $settings['post_type'],
		'orderby' => array( 'title' => 'ASC' ),
		'posts_per_page' => -1
	);
	/*if (array_key_exists('project_id', $settings)) {
		$post_args['meta_query'] = array(
			array(
				'key' => 'ds62-stage-project', 
				'value' => $settings['project_id'],
				'compare' => '=',
			)
		);
	}*/
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	/* Форма: любая, выбираем: этап, пользователь: любой */ 
	if (
		in_array('ds62-stage', $settings['post_type'])
		//&& !in_array( 'administrator', $user_roles )
	) {
		//echo 'Форма: любая, выбираем: этап, пользователь: любой<br />';
		$project_id = 0;
		if (array_key_exists('post_id', $settings)) {
			//echo 'array_key_exists post_id<br />';
			$project_id = wpuf_get_project_id($settings['post_id']);
		}
		if (array_key_exists('stage_id', $settings)) {
			//echo '$settings[stage_id]: ' . $settings['stage_id'] . '^<br />';
			$project_id = wpuf_get_stage_meta($settings['stage_id'], 'ds62-stage-project');
		}
		//echo 'project_id: ' . $project_id . '^<br />';
		$post_args['meta_query'] = array(
			array(
				'key' => 'ds62-stage-project', 
				'value' => $project_id,
				'compare' => '=',
			)
		);
	}
	/* форма: любая, выбираем: проект, пользователь: не администратор */
	if (
		in_array('ds62-project', $settings['post_type'])
		&& !in_array( 'administrator', $user_roles )
	) {
		//echo 'форма: любая, выбираем: проект, пользователь: не администратор<br />';
		$user_id = $user->ID;
		$meta_conditions = ds62_project_filter($user_id);
		//echo 'meta_conditions<br />';
		//print_r($meta_conditions);
		$post_args['meta_query'] = $meta_conditions;
	}
	//echo 'post_args<br />';
	//print_r($post_args);
	//echo '</pre>';
	return $post_args;
}

/*
 * WPUF, ACF получение постов по фильтру
 */
function wpuf_get_posts($settings) {
	$post_args = wpuf_generate_post_args($settings);
	$my_posts = new WP_Query;
	$posts = $my_posts->query($post_args);
	return $posts;
}



/*
 * WPUF, ACF получение id связанного этапа по id поста
 */
function wpuf_get_stage_id($post_id) {
	$result = 0;
	if (!empty($post_id)) {
		$stage_id = get_post_meta($post_id, 'ds62-task-stage', true );
		if (!empty($stage_id)) {
			$result = $stage_id;
		}
	}
	return $result;
}

/*
 * WPUF получение значения заданного мета-поля этапа по id текущего поста
 */
function wpuf_get_stage_meta($stage_id = null, $meta_name = 'ds62-project-members') {
	$result = array();
	if (!empty($stage_id)) {
		$stage_meta_value = get_post_meta($stage_id, $meta_name, true );
		if (!empty($stage_meta_value)) {
			$result = $stage_meta_value;
		}
	}
	return $result;
}

/*
 * WPUF, ACF получение id связанного проекта по id поста
 */
function wpuf_get_project_id($post_id) {
	$result = 0;
	if (!empty($post_id)) {
		$stage_id = wpuf_get_stage_id($post_id);
		//echo 'stage_id: ' . $stage_id . '^<br />';
		if (!empty($stage_id)) {
			$project_id = get_post_meta($stage_id, 'ds62-stage-project', true );
			if (!empty($project_id)) {
				$result = $project_id;
			}
		}
	}
	return $result;
}

/*
 * WPUF получение значения заданного мета-поля проекта по id текущего поста
 */
function wpuf_get_project_meta($project_id = null, $meta_name = 'ds62-project-members') {
	$result = array();
	if (!empty($project_id)) {
		$project_meta_value = get_post_meta($project_id, $meta_name, true );
		if (!empty($project_meta_value)) {
			$result = array_merge($result, (array) $project_meta_value);
		}
	}
	return $result;
}

/*
 * WPUF, ACF генерация фильтра для хапроса пользователей
 */
function wpuf_generate_user_args($settings) {
	//echo '<pre>';
	//echo 'wpuf_generate_user_args<br />';
	//echo 'settings:<br />';
	//print_r($settings);
	$user_args = array(
		'role__in' => $settings['user_roles'],
	);
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	
	/* форма: проект, выбираем: куратор, пользователь: куратор */
	if (
		$settings['form_id'] == 139
		&& in_array( 'ds_curator_of_the_project', $settings['user_roles'] )
		&& in_array( 'ds_curator_of_the_project', $user_roles )
		//&& $settings['field_name'] == 'ds62-project-curator'
	) {
		//echo 'форма: проект, выбираем: куратор, пользователь: куратор<br />';
		$user_args['include'] = (array) $user->ID;
	}
	
	/* форма: проект, выбираем: руководитель, пользователь: руководитель */
	if (
		$settings['form_id'] == 139
		&& in_array( 'ds_project_manager', $settings['user_roles'] )
		&& !in_array( 'ds_project_participant', $settings['user_roles'] )
		&& in_array( 'ds_project_manager', $user_roles )
		//&& $settings['field_name'] == 'ds62-project-manager'
	) {
		//echo 'форма: проект, выбираем: руководитель, пользователь: руководитель<br />';
		$user_args['include'] = (array) $user->ID;
	}
	
	/* форма: задача, выбираем участников, пользователь: любой */
	if (
		$settings['form_id'] == 123
		&& in_array('ds_project_participant', $settings['user_roles'])
	) {
		//echo 'форма: задача, выбираем участников, пользователь: любой<br />';
		$project_id = 0;
		$project_members = array();
		if (array_key_exists('post_id', $settings)) {
			$project_id = wpuf_get_project_id($settings['post_id']);
		}
		if (array_key_exists('stage_id', $settings)) {
			$stage_id = $settings['stage_id'];
			$stage_project_id = wpuf_get_stage_meta($stage_id, 'ds62-stage-project');
			// echo 'stage_project_id: ' . $stage_project_id . '^<br />';
			if(!empty($stage_project_id)) {
				$project_id = $stage_project_id;
			}
		}
		if (!empty($project_id)) {
			$project_members = wpuf_get_project_meta($project_id, 'ds62-project-members');
		}
		/*test begin*/
		//$user_args['include'] = array_merge((array) 0, (array) $project_members);
		$user_args['include'] = (array) $project_members;
		/*test end*/
	}
	//echo 'user_args:<br />';
	//print_r($user_args);
	//echo '</pre>';
	return $user_args;
}

/*
 * WPUF, ACF получение пользователей по фильтру
 */
function wpuf_get_users($settings) {
	$user_args = wpuf_generate_user_args($settings);
	$users = get_users($user_args);
	return $users;
}



/*
 * WPUF получаем итоговый статус проекта. Результат: array('<состояние>' => <количество>)
 */
function wpuf_get_project_state($project_id) {
	//echo '<pre>';
	//echo 'wpuf_get_project_state<br />';
	$states = array(
		'avaible' => 0,
		'progress' => 0,
		'check' => 0,
		'done' => 0,
	);
	$args = array(
		'post_type' => 'ds62-stage',
		'meta_query' => array(
			array(
				'key' => 'ds62-stage-project',
				'value'=> $project_id
			)
		)
	);
	$query = new WP_Query();
	$stages = $query->query($args);
	$stage_state = array();
	foreach( $stages as $stage ){
		$stage_state = wpuf_get_stage_state($stage->ID);
		foreach ($states as $state_key => $state_value) {
			$states[$state_key] = $state_value + $stage_state[$state_key];
		}
		/*$states = array_map(
			function () {
				return array_sum(func_get_args());
			},
			$states,
			$stage_state
		);*/
		
		//echo 'states + ' . $stage->ID . '^<br />';
		//print_r($states);
	}
	//echo '</pre>';
	return $states;
}

/*function ds62_chart($state) {
	echo '<pre>';
	echo 'ds62_chart<br />';
	$diagram_attrs = array(
		'avaible' => array(
			'color' => '#FFC325',
			'x' => 0,
			'y' => 0
		),
		'progress' => array(
			'color' => '#FF9A25',
			'x' => 0,
			'y' => 0
			
		),
		'check' => array(
			'color' => '#5BA00C',
			'x' => 0,
			'y' => 0
			
		),
		'done' => array(
			'color' => '#2AA4DF',
			'x' => 0,
			'y' => 0
			
		),
	);
	$height = 115;
	$width = 115;
	$radius = 110;
	$state_sum = array_sum($state);
	echo 'state_sum: ' . $state_sum . '^<br />';
	$cost = 360 / $state_sum;
	echo 'cost: ' . $cost . '^<br />';
	$result = '<svg style="
		width: 230px;
		height: 230px;">';
	$result .= '<circle fill="#3B4FE4" cx="115" cy="115" r="110"></circle>';
	$angle = 0;
	foreach($diagram_attrs as $diagram_attr_key => $diagram_attr_value) {
		$current_angle = $state[$diagram_attr_key] * $cost;
		if (!empty($current_angle)) {
		$angle += $current_angle;
		echo $diagram_attr_key . ' angle: ' . $angle .'^<br />';
		$diagram_attrs[$diagram_attr_key]['x'] = $height + $radius * cos($angle);
		echo 'x: ' . $diagram_attrs[$diagram_attr_key]['x'] . '^<br />';
		$diagram_attrs[$diagram_attr_key]['y'] = $width + $radius * sin($angle);
		echo 'y: ' . $diagram_attrs[$diagram_attr_key]['y'] . '^<br />';
		$result .= '<path fill="'
			. $diagram_attrs[$diagram_attr_key]['color']
			. '" d="M115,115 L115,5 A110,110 1 0,1 '
			. $diagram_attrs[$diagram_attr_key]['x']
			. ','
			. $diagram_attrs[$diagram_attr_key]['y']
			. ' z"></path>';
		}
	}
	$result .= '</svg>';
	echo '</pre>';
	return $result;
}*/



/*
 * WPUF получаем итоговый статус задачи. Результат: array('<состояние>' => <количество>)
 */
function wpuf_get_stage_state($stage_id) {
	//echo '<pre>';
	//echo 'wpuf_get_stage_state<br />';
	$states = array(
		'avaible' => 0,
		'progress' => 0,
		'check' => 0,
		'done' => 0,
	);
	foreach ($states as $state_key => $state_value) {
		$args = array(
			'post_type' => 'ds62-task',
			'meta_query' => array(
				array(
					'key' => 'ds62-task-stage',
					'value'=> $stage_id
				),
				array(
					'key' => 'ds62-task-state',
					'value' => $state_key
				)
			)
		);
		//echo 'args:<br />';
		//print_r($args);
		$query = new WP_Query($args);
		//echo 'query:<br />';
		//print_r($query);
		$fontd_posts = $query->found_posts;
		//echo 'found_posts ' . $state_key . ': ' . $fontd_posts .'^<br />';
		if (!empty($fontd_posts)) {
			$states[$state_key] = $fontd_posts;
		}
	}
	//echo 'states:<br />';
	//print_r($states);
	//echo '</pre>';
	return $states;
}

/*
 * WPUF вывод полей группы полей Настройки проекта ACF в форме проекта
 */
function wpuf_add_project_acf( $form_id, $post_id, $form_settings ) {
	acf_form_head();
	$acf_form_options = array(
		'html_submit_button'  => '',
		'field_groups' => array(
			'group_60ace4967141a',
			'group_60c08a0f89dc4',
			//'group_60c0a9d1bc7dc'
		),
	);
	if (!empty($post_id)) {
		$project_state = wpuf_get_project_state($post_id);
		$state_sum = array_sum($project_state);
		//echo 'state_sum: ' . $state_sum . '^<br />';
		if (
			!empty($state_sum)
			&& $project_state['done'] == $state_sum)
		{
			array_push($acf_form_options['field_groups'], 'group_60c0a9d1bc7dc');
		} else {
			echo '<span class="item-state-label">Состояние: в работе</span>';
		}
	}
	acf_form($acf_form_options);
}
add_action('ds62_project_curator_select', 'wpuf_add_project_acf', 10, 3 );

/*
 * WPUF вывод полей ACF в форме этапа
 */
function wpuf_add_stage_acf( $form_id, $post_id, $form_settings ) {
	//echo '<pre>';
	//echo 'wpuf_add_stage_acf<br />';
	acf_form_head();
	$acf_form_options = array(
		'html_submit_button'  => '',
		'field_groups' => array(
			'group_60a746211e83c',
			'group_60c08a0f89dc4'
		),
	);
	if (!empty($post_id)) {
		$stage_state = wpuf_get_stage_state($post_id);
		//echo ds62_chart($stage_state);
		//echo 'stage_state<br />';
		//print_r($stage_state);
		$state_sum = array_sum($stage_state);
		//echo 'state_sum: ' . $state_sum . '^<br />';
		if (
			!empty($state_sum)
			&& $stage_state['done'] == $state_sum)
		{
			array_push($acf_form_options['field_groups'], 'group_60c0a9d1bc7dc');
		} else {
			echo '<span class="item-state-label">Состояние: в работе</span>';
		}
	}
	//echo '</pre>';
	acf_form($acf_form_options);
}
add_action('ds62_stage_project_select', 'wpuf_add_stage_acf', 10, 3 );

/*
 * WPUF вывод полей ACF в форме задачи
 */
function wpuf_add_task_acf( $form_id, $post_id, $form_settings ) {
	acf_form_head();
	$acf_form_options = array(
		'html_submit_button'  => '',
		'field_groups' => array(
			'group_60ab4ae46f93e',
			'group_60c08a0f89dc4'
		),
	);
	acf_form($acf_form_options);
}
add_action('ds62_task_members_select', 'wpuf_add_task_acf', 10, 3 );

/*
 * Форма WPUF, прием и обработка полей ACF после добавления поста или редактирования
 */
function wpuf_update_custom_fields( $post_id ) {
	$groups = acf_get_field_groups(array('post_id' => $post_id));
	foreach ($groups as $group) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {
			if (array_key_exists($field['key'], $_POST['acf'])) {
				update_field($field['key'], $_POST['acf'][$field['key']], $post_id);
			}
		}
	}
}
add_action( 'wpuf_add_post_after_insert', 'wpuf_update_custom_fields' );
add_action( 'wpuf_edit_post_after_update', 'wpuf_update_custom_fields' );
?>