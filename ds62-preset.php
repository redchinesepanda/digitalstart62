<?php
add_action( 'added_post_meta', 'ds62_added_meta_type_action', 10, 4 );
function ds62_added_meta_type_action( $mid, $object_id, $meta_key, $_meta_value ){
	$message = array('<pre>');
	array_push($message, 'ds62_added_meta_type_action');
	$type = 'default';
	$preset_permission = false;
	$ds62_preset = array();
	array_push($message, 'meta_key: ' . $meta_key);
	if ($meta_key == 'ds62-project-type') {
		$ds62_preset = array(
			'stage1' => array(
				'title' => 'Этап 1. Валидация идеи',
				'post_type' => 'ds62-stage',
				'meta_key' => 'ds62-stage-project',
				'items' => array(
					'task1' => array(
						'title' => 'Задача 1. Уточнение идеи проекта',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task2' => array(
						'title' => 'Задача 2. Анализ целевой аудитории',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task3' => array(
						'title' => 'Задача 3. SCORE-анализ проблем потребителя',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task4' => array(
						'title' => 'Задача 4. Конкурентный анализ и скоринг решений конкурентов по функциям продукта',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task5' => array(
						'title' => 'Задача 5. Формирование ценностного предложения',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task6' => array(
						'title' => 'Задача 7. Оценка рынка',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task7' => array(
						'title' => 'Задача 8. Оценка уровня технологического развития',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
				),
			),
			'stage2' => array(
				'title' => 'Этап 2. Оценка',
				'post_type' => 'ds62-stage',
				'meta_key' => 'ds62-stage-project',
				'items' => array(
					'task1' => array(
						'title' => 'Задача 1. Оценка команды и компетенций',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task2' => array(
						'title' => 'Задача 2. Расчет экономики проекта',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task3' => array(
						'title' => 'Задача 3. Расчет инвестиционного потенциала',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
				),
			),
			'stage3' => array(),
			'stage4' => array(
				'title' => 'Этап 4. Финал',
				'post_type' => 'ds62-stage',
				'meta_key' => 'ds62-stage-project',
				'items' => array(
					'task1' => array(
						'title' => 'Задача 1. Подготовка предложения для инвестора',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
				),
			),
		);
		array_push($message, '_meta_value: ' . $_meta_value);
		if ($_meta_value == 'ds62-social') {
			$type = 'ds62-social';
			$ds62_preset['stage3'] = array(
				'title' => 'Этап 3. Бизнес-Моделирование',
				'post_type' => 'ds62-stage',
				'meta_key' => 'ds62-stage-project',
				'items' => array(
					'task1' => array(
						'title' => 'Задача 1. Выявление ключевых партнеров',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task2' => array(
						'title' => 'Задача 2. Определение ключевых ресурсов',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task3' => array(
						'title' => 'Задача 3. Проектирование каналов сбыта',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task4' => array(
						'title' => 'Задача 4. Оценка расходов',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task5' => array(
						'title' => 'Задача 5. Формирование бизнес-модели по канве Остервальдера',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
				),
			);
			$preset_permission = true;
		}
		if ($_meta_value == 'ds62-tech') {
			$type = 'ds62-tech';
			$ds62_preset['stage3'] = array(
				'title' => 'Этап 3. Технико-экономическое обоснование',
				'post_type' => 'ds62-stage',
				'meta_key' => 'ds62-stage-project',
				'items' => array(
					'task1' => array(
						'title' => 'Задача 1. Анализ технологических решений',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task2' => array(
						'title' => 'Задача 2. Подготовка финансового плана',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task3' => array(
						'title' => 'Задача 3. Формирование концепции маркетинга',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
					'task4' => array(
						'title' => 'Задача 4. Подготовка технико-экономического обоснования по проекту',
						'post_type' => 'ds62-task',
						'meta_key' => 'ds62-task-stage',
					),
				),
			);
			$preset_permission = true;
		}
	}
	array_push($message, 'preset_permission: ' . ($preset_permission ? 'true' : 'false'));
	array_push($message, 'ds62_preset: ' . print_r($ds62_preset, true));
	if ($preset_permission) {
		foreach ($ds62_preset as $ds62_parent_key => $ds62_parent_value) {
			array_push($message, 'ds62_parent_key: ' . $ds62_parent_key);
			$post_array = array(
				'post_content' => 'Описание для ' . $ds62_parent_value['title'],
				'post_status' => 'publish',
				'post_title' => $ds62_parent_value['title'],
				'post_type' => $ds62_parent_value['post_type'],
				'meta_input' => [ $ds62_parent_value['meta_key'] => $object_id ],
			);
			array_push($message, 'post_array: ' . print_r($post_array, true));
			$parent_id = wp_insert_post($post_array);
			//$parent_id = $ds62_parent_key;
			if (!empty($parent_id)) {
				$ds62_preset[$ds62_parent_key]['ID'] = $parent_id;
				if (!empty($ds62_parent_value['items'])) {
					foreach ($ds62_parent_value['items'] as $ds62_item_key => $ds62_item_value) {
						array_push($message, 'ds62_item_key: ' . $ds62_item_key);
						$post_array = array(
							'post_content' => 'Описание для ' . $ds62_item_value['title'],
							'post_status' => 'publish',
							'post_title' => $ds62_item_value['title'],
							'post_type' => $ds62_item_value['post_type'],
							'meta_input' => [
								$ds62_item_value['meta_key'] => $ds62_preset[$ds62_parent_key]['ID'],
								'ds62-task-state' => 'avaible',
							],
						);
						array_push($message, 'post_array: ' . print_r($post_array, true));
						$item_id = wp_insert_post($post_array);
						//$item_id = $ds62_item_key;
						if (!empty($item_id)) {
							$ds62_preset[$ds62_parent_key]['items'][$ds62_item_key]['ID'] = $item_id;
						}
					}
				}
			}
		}
	}
	array_push($message, 'ds62_preset: ' . print_r($ds62_preset, true));
	array_push($message, '</pre>');
	//if ($meta_key == 'ds62-project-type') {
	//	$post_array = array(
	//		'ID' => $object_id,
	//		'post_content' => implode('<br />', $message),
	//	);
	//	wp_update_post($post_array);
	//}
	//if ( current_user_can('editor') || current_user_can('administrator') ) {
	//	echo implode('<br />', $message);
	//}
}

/*
 * Проверка пресета при получении на адрес admin-ajax.php параметра action = check_preset
 */
add_action( 'wp_ajax_check_preset', 'ds62_check_preset' );
add_action( 'wp_ajax_nopriv_check_preset', 'ds62_check_preset' );
function ds62_check_preset() {
	$message = array('<pre>');
	array_push($message, 'ds62_check_preset');
	ds62_added_meta_type_action(43526, 4114, 'ds62-project-type', 'ds62-social');
	array_push($message, '</pre>');
	if ( current_user_can('editor') || current_user_can('administrator') ) {
		echo implode('<br />', $message);
	}
	wp_die();
}
?>