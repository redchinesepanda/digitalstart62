<?php
/*
 * Общие для всех состояний параметры
 */
/*
 * Добавление пересенной с адресом admin-ajax.php для файла javascript Ajax во фронтэнде
 */
add_action( 'wp_enqueue_scripts', 'myajax_data', 99 );
function myajax_data(){
	wp_localize_script( 'jquery', 'myajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);
}

/*
 * Подключение файлов с jscrollpane во фронтэнде
 */
 
add_action( 'wp_enqueue_scripts', 'jscroll_css' );
function jscroll_css(){
	wp_enqueue_style('ds62_jscroll_css', 'https://digitalstart62.ru/wp-content/themes/astra-child/css/jquery.jscrollpane.css');
}
 
add_action( 'wp_enqueue_scripts', 'my_action_javascript_scroll', 99 );
function my_action_javascript_scroll() {
    wp_register_script('ds62_scroll', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/jquery.jscrollpane.min.js', array(), null, true);
	wp_enqueue_script('ds62_scroll');
}

add_action( 'wp_enqueue_scripts', 'my_action_javascript_mousewheel_scroll', 99 );
function my_action_javascript_mousewheel_scroll() {
    wp_register_script('ds62_mousewheel_scroll', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/jquery.mousewheel.js', array(), null, true);
	wp_enqueue_script('ds62_mousewheel_scroll');
} 

/*
 * Подключение файлов с jquery.magnific-popup.js во фронтэнде
 */

add_action( 'wp_enqueue_scripts', 'magnificpopup_css' );
function magnificpopup_css(){
	wp_enqueue_style('ds62_magnificpopup_css', 'https://digitalstart62.ru/wp-content/themes/astra-child/css/magnific-popup.css');
}
 
add_action( 'wp_enqueue_scripts', 'my_action_javascript_magnificpopup', 99 );
function my_action_javascript_magnificpopup() {
    wp_register_script('ds62_magnificpopup', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/jquery.magnific-popup.js', array(), null, true);
	wp_enqueue_script('ds62_magnificpopup');
}



/*
 * Подключение файлов с jquery.mobile.custom.min.js во фронтэнде
 */

add_action( 'wp_enqueue_scripts', 'my_action_javascript_mobilecustom', 99 );
function my_action_javascript_mobilecustom() {
    wp_register_script('ds62_mobilecustom', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/jquery.mobile.custom.min.js', array(), null, true);
	wp_enqueue_script('ds62_mobilecustom');
}

/*
 * Подключение файла с javascript Ajax во фронтэнде
 */
 
add_action( 'wp_enqueue_scripts', 'my_action_javascript', 99 );
function my_action_javascript() {
    wp_register_script('ds62_ajax', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/ds62-ajax.js', array('jquery'), null, true);
	wp_enqueue_script('ds62_ajax');
}

/*
 * Подключение файла с javascript Ajax во фронтэнде
 */
 
add_action( 'wp_enqueue_scripts', 'my_action_js_live_comments', 99 );
function my_action_js_live_comments() {
    wp_register_script('ds62_live_comments', 'https://digitalstart62.ru/wp-content/themes/astra-child/js/ds62-live-comments.js', array('jquery'), null, true);
	wp_enqueue_script('ds62_live_comments');
}

function ds62_project_filter($user_id = 0) {
	$meta_conditions = array(
		'relation' => 'OR',
		array(
			'key' => 'ds62-project-curator',
			'value' => $user_id,
			'compare' => '=',
		),
		array(
			'key' => 'ds62-project-manager',
			'value' => $user_id,
			'compare' => '=',
		),
		array(
			'key' => 'ds62-project-members',
			'value' => $user_id,
			'type' => 'CHAR',
			'compare' => 'LIKE',
		),
	);
	return $meta_conditions;
}

/*
 * Состояние проекты
 */
/*
 * Модификация запроса для виджета проектов
 */
add_action( 'elementor/query/ds62-query-tasker-projects', function( $query ) {
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	if (
		!in_array( 'administrator', $user_roles )
	) {
		$user_id = $user->ID;
		$meta_conditions = ds62_project_filter($user_id);
		$query->set('meta_query', $meta_conditions);
	}
});

/*
 * Состояние этапы текущего проекта
 */
/*
 * Модификация запроса для виджета этапов текущего проекта
 */
add_action( 'elementor/query/ds62-query-column-stages-by-project', function( $query ) {
	$project_id = intval( $_POST['project_id'] );
	if (empty($project_id)) {
		global $post;
		if (!empty($post)) {
			//echo 'post: ' . $post->ID . '^<br />';
			$project_id = $post->ID;
		}
	}
	if (!empty($project_id)) {
		$query->set('meta_query', array(
			array(
				'key' => 'ds62-stage-project',
				'value' => $project_id,
				'compare' => '=',
			)
		));
	}
});

/*
 * Смена состояние выбранной задачи при получении на адрес admin-ajax.php параметра action = task_state_change
 */
add_action( 'wp_ajax_task_state_change', 'ds62_task_state_change' );
add_action( 'wp_ajax_nopriv_task_state_change', 'ds62_task_state_change' );
function ds62_task_state_change() {
	$messages = array('ds62_task_state_change');
	array_push($messages, '</pre>');
	array_push($messages, print_r($_POST, true));
	$task_id = 0;
	if (array_key_exists('task_id', $_POST)) {
		$task_id = $_POST['task_id'];
	}
	array_push($messages, 'task_id: ' . $task_id . '^');
	$task_state = 0;
	if (array_key_exists('task_state', $_POST)) {
		$states = array(
			'avaible',
			'progress',
			'check',
			'done',
		);
		if (in_array($_POST['task_state'], $states)) {
			$task_state = $_POST['task_state'];
		}
	}
	array_push($messages, 'task_state: ' . $task_state . '^');
	$result = 0;
	if (!empty($task_id) && !empty($task_state)) {
		$post = get_post($task_id);
		if (!empty($post)) {
			$result = update_post_meta( $task_id, 'ds62-task-state', $task_state );
		}
	}
	array_push($messages, 'result: ' . $result . '^');
	array_push($messages, '</pre>');
	//echo implode('<br />', $messages);
	wp_die($result);
}

/*
 * Вывод секции проектов выбранного этапа при получении на адрес admin-ajax.php параметра action = get_stages
 */
add_action( 'wp_ajax_get_stages', 'ds62_show_stages' );
add_action( 'wp_ajax_nopriv_get_stages', 'ds62_show_stages' );
function ds62_show_stages() {
	$project_id = intval( $_POST['project_id'] );
	if (!empty($project_id)) {
		echo do_shortcode('[elementor-template id="1780"]');
	}
	wp_die();
}

function get_conditions_meta($stage_id = 0, $state = 'avaible') {
	/*$conditions = array(
		'relation' => 'AND',
    	array(
    		'key' => 'ds62-task-stage', 
    		'value' => $stage_id,
    		'compare' => '=',
    	),
    	array(
    		'key' => 'ds62-task-state', 
    		'value' => $state,
    		'compare' => '=',
    	)
	);*/
	$conditions = array(
		'relation' => 'AND',
    	array(
    		'key' => 'ds62-task-stage', 
    		'value' => $stage_id,
    		'compare' => '=',
    	)
	);
	if ($state != 'all') {
		array_push(
			$conditions,
			array(
				'key' => 'ds62-task-state', 
				'value' => $state,
				'compare' => '=',
			)
		);
	}
	return $conditions;
}

function get_conditions_member() {
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$user_roles = (array) $user->roles;
	$conditions = array();
	if (in_array('ds_project_participant', $user_roles)) {
		$conditions = array(
			array(
				//'key' => 'ds62-project-members',
				'key' => 'ds62-task-members',
				'value' => $user_id,
				'type' => 'CHAR',
				'compare' => 'LIKE',
			),
		);
	}
	return $conditions;
}

/*
 * Состояние задачи all Все
 */
/*
 * Модификация запроса для виджета задач в состоянии Все текущего этапа
 */
add_action( 'elementor/query/ds62-query-column-tasks-by-stage-all', function( $query ) {
	//echo '<pre>';
	//echo 'ds62-query-column-tasks-by-stage-all<br />';
	$stage_id = intval( $_POST['stage_id'] );
	if (empty($stage_id)) {
		global $post;
		if (!empty($post)) {
			//echo 'post: ' . $post->ID . '^<br />';
			$stage_id = $post->ID;
		}
	}
	if (!empty($stage_id)) {
		//echo 'stage_id: ' .$stage_id .'^<br />';
		$state = 'all';
		$conditions_meta = get_conditions_meta($stage_id, $state);
		$conditions_member = get_conditions_member();
		$member_query_meta = array_merge($conditions_meta, $conditions_member);
		//echo 'member_query_meta:<br />';
		//print_r($member_query_meta);
		$query->set('meta_query', $member_query_meta);
		/*$query->set('meta_query', array(
			'relation' => 'AND',
    		array(
    			'key' => 'ds62-task-stage', 
    			'value' => $stage_id,
    			'compare' => '=',
    		),
    		array(
    			'key' => 'ds62-task-state', 
    			'value' => 'avaible',
    			'compare' => '=',
    		)
	    ));*/
	}
	//echo '</pre>';
});

/*
 * Состояние задачи avaible В очереди
 */
/*
 * Модификация запроса для виджета задач в состоянии В очереди текущего этапа
 */
add_action( 'elementor/query/ds62-query-column-tasks-by-stage-avaible', function( $query ) {
	//echo '<pre>';
	//echo 'ds62-query-column-tasks-by-stage-avaible<br />';
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		//echo 'stage_id: ' .$stage_id .'^<br />';
		$state = 'avaible';
		$conditions_meta = get_conditions_meta($stage_id, $state);
		$conditions_member = get_conditions_member();
		$member_query_meta = array_merge($conditions_meta, $conditions_member);
		//echo 'member_query_meta:<br />';
		//print_r($member_query_meta);
		$query->set('meta_query', $member_query_meta);
		/*$query->set('meta_query', array(
			'relation' => 'AND',
    		array(
    			'key' => 'ds62-task-stage', 
    			'value' => $stage_id,
    			'compare' => '=',
    		),
    		array(
    			'key' => 'ds62-task-state', 
    			'value' => 'avaible',
    			'compare' => '=',
    		)
	    ));*/
	}
	//echo '</pre>';
});

/*
 * Вывод секции задач в состоянии В очереди выбранного этапа при получении на адрес admin-ajax.php параметра action = get_tasks_avaible
 */
add_action( 'wp_ajax_get_tasks_avaible', 'ds62_show_tasks_avaible' );
add_action( 'wp_ajax_nopriv_get_tasks_avaible', 'ds62_show_tasks_avaible' );
function ds62_show_tasks_avaible() {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		echo do_shortcode('[elementor-template id="1861"]');
	}
	wp_die();
}

/*
 * Состояние задачи progress В работе
 */
/*
 * Модификация запроса для виджета задач в состоянии В работе текущего этапа
 */
add_action( 'elementor/query/ds62-query-column-tasks-by-stage-progress', function( $query ) {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		$state = 'progress';
		$conditions_meta = get_conditions_meta($stage_id, $state);
		$conditions_member = get_conditions_member();
		$member_query_meta = array_merge($conditions_meta, $conditions_member);
		$query->set('meta_query', $member_query_meta);
		/*$query->set('meta_query', array(
			'relation' => 'AND',
    		array(
    			'key' => 'ds62-task-stage', 
    			'value' => $stage_id,
    			'compare' => '=',
    		),
    		array(
    			'key' => 'ds62-task-state', 
    			'value' => 'progress',
    			'compare' => '=',
    		)
	    ));*/
	}
});

/*
 * Вывод секции задач в состоянии В работе выбранного этапа при получении на адрес admin-ajax.php параметра action = get_tasks_progress
 */
add_action( 'wp_ajax_get_tasks_progress', 'ds62_show_tasks_progress' );
add_action( 'wp_ajax_nopriv_get_tasks_progress', 'ds62_show_tasks_progress' );
function ds62_show_tasks_progress() {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		echo do_shortcode('[elementor-template id="1907"]');
	}
	wp_die();
}

/*
 * Состояние задачи check На проверку
 */
/*
 * Модификация запроса для виджета задач в состоянии На проерку текущего этапа
 */
add_action( 'elementor/query/ds62-query-column-tasks-by-stage-check', function( $query ) {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		$state = 'check';
		$conditions_meta = get_conditions_meta($stage_id, $state);
		$conditions_member = get_conditions_member();
		$member_query_meta = array_merge($conditions_meta, $conditions_member);
		$query->set('meta_query', $member_query_meta);
		/*$query->set('meta_query', array(
			'relation' => 'AND',
    		array(
    			'key' => 'ds62-task-stage',
    			'value' => $stage_id,
    			'compare' => '=',
    		),
    		array(
    			'key' => 'ds62-task-state', 
    			'value' => 'check',
    			'compare' => '=',
    		)
	    ));*/
	}
});

/*
 * Вывод секции задач в состоянии На проверку выбранного этапа при получении на адрес admin-ajax.php параметра action = get_tasks_check
 */
add_action( 'wp_ajax_get_tasks_check', 'ds62_show_tasks_check' );
add_action( 'wp_ajax_nopriv_get_tasks_check', 'ds62_show_tasks_check' );
function ds62_show_tasks_check() {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		echo do_shortcode('[elementor-template id="1912"]');
	}
	wp_die();
}

/*
 * Состояние задачи Done Готово
 */
/*
 * Модификация запроса для виджета задач в состоянии Готово текущего этапа
 */
add_action( 'elementor/query/ds62-query-column-tasks-by-stage-done', function( $query ) {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		$state = 'done';
		$conditions_meta = get_conditions_meta($stage_id, $state);
		$conditions_member = get_conditions_member();
		$member_query_meta = array_merge($conditions_meta, $conditions_member);
		$query->set('meta_query', $member_query_meta);
		/*$query->set('meta_query', array(
			'relation' => 'AND',
    		array(
    			'key' => 'ds62-task-stage', 
    			'value' => $stage_id,
    			'compare' => '=',
    		),
    		array(
    			'key' => 'ds62-task-state', 
    			'value' => 'done',
    			'compare' => '=',
    		)
	    ));*/
	}
});

/*
 * Вывод секции задач в состоянии Готово выбранного этапа при получении на адрес admin-ajax.php параметра action = get_tasks_done
 */
add_action( 'wp_ajax_get_tasks_done', 'ds62_show_tasks_done' );
add_action( 'wp_ajax_nopriv_get_tasks_done', 'ds62_show_tasks_done' );
function ds62_show_tasks_done() {
	$stage_id = intval( $_POST['stage_id'] );
	if (!empty($stage_id)) {
		echo do_shortcode('[elementor-template id="1915"]');
	}
	wp_die();
}

/*
 * Вывод секции задач в состоянии Готово выбранного этапа при получении на адрес admin-ajax.php параметра action = get_tasks_done
 */
add_action( 'wp_ajax_get_projects', 'ds62_show_projects' );
add_action( 'wp_ajax_nopriv_get_projects', 'ds62_show_projects' );
function ds62_show_projects() {
	echo do_shortcode('[elementor-template id="2222"]');
	wp_die();
}

/*
 * Вывод комментариев при получении на адрес admin-ajax.php параметра action = get_live_comments
 */
add_action( 'wp_ajax_get_live_comments', 'ds62_ajax_live_comments' );
add_action( 'wp_ajax_nopriv_get_live_comments', 'ds62_ajax_live_comments' );
function ds62_ajax_live_comments() {
	//echo '<pre>';
	$atts = array();
	//$atts['project_id'] = intval( $_POST['project_id'] ) ?? 2014;
	$atts['project_id'] = /*$_GET['project_id'] ??*/ intval( $_POST['project_id'] ) /*?? 2014*/;
	//$atts['pagenum'] = intval( $_POST['pagenum'] ) ?? 1;
	$atts['pagenum'] = /*$_GET['pagenum'] ??*/ intval( $_POST['pagenum'] ) /*?? 1*/;
	//echo 'atts:<br />';
	//print_r($atts);
	//echo '</pre>';
	//if (!empty($project_id)) {
	if (!empty($atts['project_id'])) {
		echo ds62_live_comments( $atts );
	}
	wp_die();
}

/*Ajax запрос на изменение количества статусов задач для глобальной задачи*/

add_action( 'wp_ajax_get_tasks_sbs', 'ds62_tasks_status_by_stage' );
add_action( 'wp_ajax_nopriv_get_tasks_sbs', 'ds62_tasks_status_by_stage' );
function ds62_tasks_status_by_stage() {
	
	$stage_id = intval($_POST['stage_id']);
	$project_id = intval($_POST['project_id']);
	
	function ds62_get_stage_status_by_tasks($id) {
		$states_for_stage = array(
			'avaible' => 0,
			'progress' => 0,
			'check' => 0,
			'done' => 0,
		);
		
		foreach ($states_for_stage as $state_key_for_stage => $state_value_for_stage) {
			$args_for_stage = array(
				'post_type' => 'ds62-task',
				'meta_query' => array(
					array(
						'key' => 'ds62-task-stage',
						'value'=> $id
					),
					array(
						'key' => 'ds62-task-state',
						'value' => $state_key_for_stage
					)
				)
			);
			$query_for_stage = new WP_Query($args_for_stage);
			$fontd_posts_for_stage = $query_for_stage->found_posts;
			if (!empty($fontd_posts_for_stage)) {
				$states_for_stage[$state_key_for_stage] = $fontd_posts_for_stage;
			}
		}
		
		return $states_for_stage;
	}
	
	$states_for_project = array(
		'avaible' => 0,
		'progress' => 0,
		'check' => 0,
		'done' => 0,
	);
	
	$args_for_project = array(
		'post_type' => 'ds62-stage',
		'meta_query' => array(
			array(
				'key' => 'ds62-stage-project',
				'value'=> $project_id
			)
		)
	);
	
	$query_for_project = new WP_Query();
	$stages_for_project = $query_for_project->query($args_for_project);
	$stage_state_for_project = array();
	
	foreach( $stages_for_project as $stage_for_project ){
		$stage_state_for_project = ds62_get_stage_status_by_tasks($stage_for_project->ID);
		foreach ($states_for_project as $state_key_for_project => $state_value_for_project) {
			$states_for_project[$state_key_for_project] = $state_value_for_project + $stage_state_for_project[$state_key_for_project];
		}
	}
	
	$result = array (
		'project_status' => $states_for_project,
		'stage_status' => ds62_get_stage_status_by_tasks($stage_id)
	);

	echo wp_json_encode($result);
	
	wp_die();
}

?>