<?php
/*
 * Elementor Custom Query Filter by ds62-task-status meta field
 */
add_action( 'elementor/query/ds62-task-query-avaible', function( $query ) {
	$query->set('meta_query', array(
		array(
			'key' => 'ds62-task-state', 
			'value' => 'avaible',
			'compare' => '=',
		)
	));
} );
add_action( 'elementor/query/ds62-task-query-progress', function( $query ) {

	$query->set('meta_query', array(
		array(
			'key' => 'ds62-task-state', 
			'value' => 'progress',
			'compare' => '=',
		)
	));
} );
add_action( 'elementor/query/ds62-task-query-check', function( $query ) {

	$query->set('meta_query', array(
		array(
			'key' => 'ds62-task-state', 
			'value' => 'check',
			'compare' => '=',
		)
	));
} );
add_action( 'elementor/query/ds62-task-query-done', function( $query ) {

	$query->set('meta_query', array(
		array(
			'key' => 'ds62-task-state', 
			'value' => 'done',
			'compare' => '=',
		)
	));
} );
add_action( 'elementor/query/ds62-query-single-stage-tasks', function( $query ) {
	$post_id = get_the_ID();
	$query->set('meta_query', array(
		array(
			'key' => 'ds62-task-stage', 
			'value' => $post_id,
			'compare' => '=',
		)
	));
} );
add_action( 'elementor/query/ds62-query-single-project-stages', function( $query ) {
	$post_id = get_the_ID();
	$query->set('meta_query', array(
		array(
			'key' => 'ds62-stage-project', 
			'value' => $post_id,
			'compare' => '=',
		)
	));
} );
?>