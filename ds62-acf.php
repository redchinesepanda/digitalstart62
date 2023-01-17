<?php
/*
 * ACF вывод списка доступных кураторов в поле ds62-project-curator
 */
function acf_load_choices_curator($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_curator<br />';
	$post = get_post();
	$post_id = $post->ID;
	//$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
	$settings = array(
		'user_roles' => array(
			'ds_curator_of_the_project',
		),
		//'form_id' => $form_id,
		//'post_id' => $post_id,
	);
	if (in_array(get_post_type($post_id), array('ds62-project'))) {
		//echo 'post_id: ' . $post_id . '^<br />';
		//echo 'post->post_title: ' . $post->post_title . '^<br />';
		$settings['post_id'] = $post_id;
		$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
		//echo 'form_id: ' . $form_id . '^<br />';
		$settings['form_id'] = $form_id;
	}
	/* Текущий пост это форма добавления проекта */
	if ($post->post_name == 'add-project') {
		$settings['form_id'] = 139;
	}
	$users = wpuf_get_users($settings);
	//echo 'users<br />';
	//print_r($users);
	$settings = array(
		'posts' => $users,
		'field_id' => 'ID',
		'field_label' => 'display_name',
	);
	$choices = acf_get_options($settings);
	//echo 'choices:<br />';
	//print_r($choices);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-project-curator', 'acf_load_choices_curator');

/*
 * ACF вывод списка доступных руководителей в поле ds62-project-manager
 */
function acf_load_choices_manager($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_manager:<br />';
	$post = get_post();
	$post_id = $post->ID;
	//$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
	$settings = array(
		'user_roles' => array(
			'ds_project_manager',
		),
		//'form_id' => $form_id,
		//'post_id' => $post_id,
	);
	if (in_array(get_post_type($post_id), array('ds62-project'))) {
		//echo 'post_id: ' . $post_id . '^<br />';
		//echo 'post->post_title: ' . $post->post_title . '^<br />';
		$settings['post_id'] = $post_id;
		$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
		//echo 'form_id: ' . $form_id . '^<br />';
		$settings['form_id'] = $form_id;
	}
	/* Текущий пост это форма добавления проекта */
	if ($post->post_name == 'add-project') {
		$settings['form_id'] = 139;
	}
	$users = wpuf_get_users($settings);
	$settings = array(
		'posts' => $users,
		'field_id' => 'ID',
		'field_label' => 'display_name',
	);
	$choices = acf_get_options($settings);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-project-manager', 'acf_load_choices_manager');

/*
 * ACF вывод списка доступных участников в поля ds62-project-members и ds62-task-members
 */
function acf_load_choices_members($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_members:<br />';
	$settings = array(
		'user_roles' => array(
			'ds_project_manager',
			'ds_project_participant',
		),
	);
	$post = get_post();
	$post_id = $post->ID;
	/* Текущий пост проект или задача */
	if (in_array(get_post_type($post_id), array('ds62-project', 'ds62-task'))) {
		//echo 'post_id: ' . $post_id . '^<br />';
		//echo 'post->post_title: ' . $post->post_title . '^<br />';
		$settings['post_id'] = $post_id;
		$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
		//echo 'form_id: ' . $form_id . '^<br />';
		$settings['form_id'] = $form_id;
	}
	/* Текущий пост это форма добавления задачи */
	if ($post->post_name == 'add-task') {
		$settings['form_id'] = 123;
	}
	/*if (array_key_exists('project_id', $_GET)) {
		$project_id = $_GET['project_id'];
		echo 'project_id: ' . $project_id . '^<br />';
		$settings['project_id'] = $project_id;
	}*/
	if (array_key_exists('stage_id', $_GET)) {
		$project_id = $_GET['stage_id'];
		//echo 'stage_id: ' . $project_id . '^<br />';
		$settings['stage_id'] = $project_id;
	}
	
	//echo 'settings:<br />';
	//print_r($settings);
	$users = wpuf_get_users($settings);
	$settings = array(
		'posts' => $users,
		'field_id' => 'ID',
		'field_label' => 'display_name',
	);
	$choices = acf_get_options($settings);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-project-members', 'acf_load_choices_members');
add_filter('acf/load_field/name=ds62-task-members', 'acf_load_choices_members');

/*
 * ACF преобразование списка обьектов пользователя или поста в ассоциативный массив array('<id>' => '<value>',) для записи в поле
 */
function acf_get_options($settings) {
	$result = array();
	if(!empty($settings['posts'])) {
		$field_id = $settings['field_id'];
		$field_label = $settings['field_label'];
		foreach( $settings['posts'] as $post ){
			$atts = array(
				'text' => $post->$field_label,
				'length' => 10,
				'marker' => '&hellip;',
			);
			$label = ds62_filter_text_shortcode( $atts );
			$result[ $post->$field_id ] = esc_html( $label );
		}
	}
	return $result;
}

/*
 * ACF вывод списка доступных проектов в поле ds62-stage-project
 */
function acf_load_choices_projects($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_projects:<br />';
	$post = get_post();
	$post_id = $post->ID;
	//$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
	$settings = array(
		'post_type' => array(
			'ds62-project',
		),
	);
	if ($post->post_name == 'add-stage') {
		$settings['form_id'] = 21;
	}
	//echo 'settings:<br />';
	//print_r($settings);
	$posts = wpuf_get_posts($settings);
	$settings = array(
		'posts' => $posts ,
		'field_id' => 'ID',
		'field_label' => 'post_title',
	);
	//echo 'settings:<br />';
	//print_r($settings);
	$choices = acf_get_options($settings);
	//echo 'choices:<br />';
	//print_r($choices);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-stage-project', 'acf_load_choices_projects');

/*
 * ACF вывод списка доступных этапов в поле ds62-task-stage
 */
function acf_load_choices_stages($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_stages:<br />';
	$post = get_post();
	$post_id = $post->ID;
	$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
	$settings = array(
		'post_type' => array(
			'ds62-stage',
		),
	);
	/* Текущий пост это задача */
	if (in_array(get_post_type($post_id), array('ds62-task'))) {
		//echo 'post_id: ' . $post_id . '^<br />';
		//echo 'post->post_title: ' . $post->post_title . '^<br />';
		$settings['post_id'] = $post_id;
		$form_id = get_post_meta($post_id, '_wpuf_form_id', true);
		//echo 'form_id: ' . $form_id . '^<br />';
		$settings['form_id'] = $form_id;
	}
	/* Текущий пост это форма добавления задачи */
	if ($post->post_name == 'add-task') {
		$settings['form_id'] = 123;
	}
	/*if (array_key_exists('project_id', $_GET)) {
		$project_id = $_GET['project_id'];
		//echo 'project_id: ' . $project_id . '^<br />';
		$settings['project_id'] = $project_id;
	}*/
	if (array_key_exists('stage_id', $_GET)) {
		$stage_id = $_GET['stage_id'];
		//echo 'stage_id: ' . $stage_id . '^<br />';
		$settings['stage_id'] = $stage_id;
	}
	//echo '$settings:<br />';
	//print_r($settings);
	$posts = wpuf_get_posts($settings);
	//echo '$posts:<br />';
	//print_r($posts);
	$settings = array(
		'posts' => $posts ,
		'field_id' => 'ID',
		'field_label' => 'post_title',
	);
	$choices = acf_get_options($settings);
	//echo '$choices:<br />';
	//print_r($choices);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-task-stage', 'acf_load_choices_stages');

function acf_load_choices_states($field) {
	//echo '<pre>';
	//echo 'acf_load_choices_states:<br />';
	$choices = array(
		'avaible' => 'В очереди',
		'progress' => 'В работе',
		'check' => 'На проверку',
		//'done' => 'Завершенная',
	);
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	//echo 'user_roles:<br />';
	//print_r($user_roles);
	if (
		!in_array( 'ds_project_participant', $user_roles )
	) {
		$choices['done'] = 'Завершенная';
	}
	/*$post = get_post();
	$post_id = $post->ID;
	$post_type = $post->post_type;
	if (in_array($post_type, array('ds62-task'))) {
		$current_state ='!!!';
		//$current_state = get_field( 'ds62-task-state' );
		$current_state = get_post_meta($post_id, 'ds62-task-state', true );
		echo 'current_state: ' . $current_state .'^<br />';
		if (array_key_exists($current_state, $choices)) {
			unset($choices[$current_state]);
		}
	}*/
	//echo '$choices:<br />';
	//print_r($choices);
	$field['choices'] = $choices;
	//echo '</pre>';
	return $field;
}
add_filter('acf/load_field/name=ds62-task-state', 'acf_load_choices_states');

/*
 * Функция получает массив id автора и исполнителей задачяи
 */
function ds62_acf_notify_users($post_id = 0) {
	//echo 'ds62_acf_notify_users<br />';
	$ids = array();
	if (!empty($post_id)) {
		$task_author = get_post_field( 'post_author', $post_id );
		array_push($ids, $task_author);
		$task_members = get_post_meta( $post_id, 'ds62-task-members', true );
		$ids = array_merge($ids, (array) $task_members);
	}
	return $ids;
}





?>