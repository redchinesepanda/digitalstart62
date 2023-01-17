<?php
/*
 * How to Fix Custom Fields Not Showing in WordPress
 */
add_filter('acf/settings/remove_wp_meta_box', '__return_false');

/*
 * Редирект после выхода из учётной записи
 */
add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

/*
 * Шорткод [ds62-filter-text], выводит указанное количество слов из указанного поля
 * [ds62-filter-text field='post_title' length='10']
 * [ds62-filter-text field='post_content' length='10']
 * [ds62-filter-text meta_field='ds62-stage-project' length='10' marker='&hellip;']
 * [ds62-filter-text meta_field='ds62-task-stage' length='10' marker='&hellip;']
 * [ds62-filter-text text='С другой стороны укрепление' length='10' marker='&hellip;']
 */
add_shortcode( 'ds62-filter-text', 'ds62_filter_text_shortcode' );
function ds62_filter_text_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_filter_text_shortcode<br />';
	//echo 'atts<br />';
	//print_r($atts);
	//$field = 'post_title';
	$text = 'Нет текста по заданным параметрам';
	$post = get_post();
	if (array_key_exists('field', $atts)) {
		$field = $atts['field'];
		$text = $post->$field;
	}
	if (array_key_exists('text', $atts)) {
		$text = $atts['text'];
	}
	if (array_key_exists('meta_field', $atts)) {
		$meta_key = $atts['meta_field'];
		/*$meta_value = get_post_meta($post->ID, $meta_key, true);
		if (!empty($meta_value)) {
			$text = $meta_value;
		}*/
		$acf_value = get_field($meta_key);
		if (array_key_exists('label', $acf_value)) {
			$text = $acf_value['label'];
		}
		//echo 'acf_value:<br />';
		//print_r($acf_value);
	}
	$length = 10;
	if (array_key_exists('length', $atts)) {
		$length = $atts['length'];
	}
	$marker = '';
	if (array_key_exists('marker', $atts)) {
		$marker = $atts['marker'];
	}
	$output = wp_trim_words($text , $length, $marker );
	//echo 'output: ' . $output . '^<br />';
	//echo '</pre>';
	return $output;
}

/*
 * How do I shorten the title lenghs with Elementor theme?
 */
add_filter( 'the_title', 'my_trim_words_by_post_type', 10, 2 );
function my_trim_words_by_post_type( $title, $post_id ) {
    //$post_type = get_post_type( $post_id );
    //if( 'product' == $post_type ) { 
    //    return $title;
    return wp_trim_words( $title, 10);
}

/*
 * Шорткод [ds62-icon], выводит иконку
 */
add_shortcode( 'ds62-icon', 'ds62_icon_shortcode' );
function ds62_icon_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_icon_shortcode<br />';
	$icon = '<i class="fas fa-check"></i>';
	$item_icon = get_field( 'ds62-project-icon' );
	if (!empty($item_icon)) {
		$icon = $item_icon;
	} else {
		$default_icons = array(
			'ds62-project' => '<i class="fas fa-project-diagram"></i>',
			'ds62-stage' => '<i class="fas fa-bookmark"></i>',
			'ds62-task' => '<i class="fas fa-thumbtack"></i>',
		);
		$default_icons_state = array(
			'avaible' => '<i class="fas fa-pause-circle"></i>',
			'progress' => '<i class="fas fa-play-circle"></i>',
			'check' => '<i class="fas fa-star-half-alt"></i>',
			'done' => '<i class="fas fa-star"></i>',
		);
		$post = get_post();
		$post_type = $post->post_type;
		if (array_key_exists($post_type, $default_icons)) {
			$icon = $default_icons[$post_type];
		}
		$post_meta = get_post_meta($post->ID, 'ds62-task-state', true);
		//echo 'post_meta: ' . $post_meta . '^<br />';
		if (array_key_exists($post_meta, $default_icons_state)) {
			$icon = $default_icons_state[$post_meta];
		}
	}
	//echo 'icon:<br />';
	//print_r($icon);
	//echo '</pre>';
	return $icon;
}

/*
 * Шорткод [ds62-icon-color], выводит название класса иконки, задающего цвет фона
 */
add_shortcode( 'ds62-icon-color', 'ds62_icon_color_shortcode' );
function ds62_icon_color_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_icon_color_shortcode<br />';
	//$post = get_post();
	$class = '';
	//$item_state = get_field( 'ds62-task-state', $post->ID );
	$post = get_post();
	$post_type = $post->post_type;
	$default_icons_type = array(
		'ds62-project' => '',
		'ds62-stage' => 'ds62-top-icon-stage',
		'ds62-task' => '',
	);
	if (array_key_exists($post_type, $default_icons_type)) {
		$class = $default_icons_type[$post_type];
	}
	$item_state = get_field( 'ds62-task-state');
	//echo 'item_state:<br />';
	//print_r($item_state);
	if (!empty($item_state)) {
		$default_icons_state = array(
			'avaible' => 'ds62-top-icon-avaible',
			'progress' => 'ds62-top-icon-progress',
			'check' => 'ds62-top-icon-check',
			'done' => 'ds62-top-icon-done',
		);
		if (array_key_exists($item_state['value'], $default_icons_state)) {
			$class = $default_icons_state[$item_state['value']];
		}
	}
	//echo 'class: ' . $class . '<br />';
	//echo '</pre>';
	return $class;
}

/*
 * Шорткод [ds62-expired-class], выводит класс ds62-expired, если дата окончания обьекта старше текущей
 */
add_shortcode( 'ds62-expired-class', 'ds62_expired_class_shortcode' );
function ds62_expired_class_shortcode( $atts ){
	//echo '<pre>';
	//echo 'ds62_expired_class_shortcode<br />';
	$result = '';
	$date_item_end = get_field( 'ds62-item-date-end' );
	/*if (!empty($date_item_end)) {
		//$date_end = $date_item_end;
		//echo 'date_item_end: ' . $date_item_end . '^<br />';
		$current_date = date("d/m/Y");
		//echo 'current_date:<br />';
		//print_r($current_date);
		//echo '<br />';
		$origin = new DateTime($date_item_end);
		$target = new DateTime($current_date);
		//$interval = $origin->diff($target);
		$interval = $target->diff($origin);
		//echo 'interval: ' . $interval->format('%R%a дней') . '^<br />';
		if ($origin <= $target) {
			$result = 'ds62-expired';
		}
	}*/
	//echo 'result: ' . $result . '^<br />';
	//echo '</pre>';
	return $result;
}

/*Шорткод, выводящий прогресс-бар в карточках с проектами и ГЗ*/

add_shortcode( 'ds62-progress-bar', 'ds62_progress_bar_shortcode' );
function ds62_progress_bar_shortcode( $atts ){
	$post = get_post();
	$post_type = $post->post_type;
	if ($post_type == 'ds62-project' || $post_type == 'ds62-stage') {
		$result = '<div class="ds62-progress-bar">
						<span class="ds62-progress-bar__title"></span>
						<div class="ds62-progress-bar__background">
							<div class="ds62-progress-bar__line"></div>
						</div>
					</div>';
		return $result;
	}else{
		return false;
	}	
}
?>