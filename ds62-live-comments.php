<?php
function live_comment_add($post_id = 0, $message = '') {
	if (
		!empty($post_id) &&
		!empty($message)
	) {
		$data = [
			'comment_post_ID'      => $post_id,
			'comment_content'      => $message,
			'comment_type'         => 'comment',
			'comment_parent'       => 0,
			'user_id'              => get_current_user_id(),
			'comment_date'         => null, // получим current_time('mysql')
			'comment_approved'     => 1,
		];
		$comment_id = wp_insert_comment( wp_slash($data) );
		if ($comment_id) {
			ds62_notify_comment($comment_id, true);
		}
	}
}

function live_comment_post_add( $post_id, $post, $update ) {
	if (
		in_array($post->post_type, array('ds62-project', 'ds62-stage', 'ds62-task'))
		&& !$update
	) {
		$post_title = $post->post_title;
		$post_type_object = get_post_type_object($post->post_type);
		$post_type_label = $post_type_object->labels->singular_name;
		$message = 'Был создан новый ' . $post_type_label . ': ' . $post_title;
		live_comment_add($post_id, $message);
		/*if (in_array($post->post_type, array('ds62-task'))) {
			ds62_notify_meta($post_id);
		}*/
	}
}
add_action( 'wp_insert_post', 'live_comment_post_add', 10, 3 );
//add_action( 'new_ds62-project', 'live_comment_post_add', 10, 3 );
//add_action( 'new_ds62-stage', 'live_comment_post_add', 10, 3 );
//add_action( 'new_ds62-task', 'live_comment_post_add', 10, 3 );

function live_comment_post_edit( $post_id, $post_after, $post_before ) {
	if (in_array($post_after->post_type, array('ds62-project', 'ds62-stage', 'ds62-task'))) {
		$post_title = $post_after->post_title;
		$post_type_object = get_post_type_object($post_after->post_type);
		$post_type_label = $post_type_object->labels->singular_name;
		$message = 'Был обновлен ' . $post_type_label . ': ' . $post_title;
		live_comment_add($post_id, $message);
		/*if (in_array($post->post_type, array('ds62-task'))) {
			ds62_notify_meta($post_id);
		}*/
	}
}
add_action( 'post_updated', 'live_comment_post_edit', 10 , 3 );

function wpse16835_after_post_meta( $meta_id, $post_id, $meta_key, $meta_value )
{
	$message = '';
	//$message .= 'meta_id: ' . $meta_id . '^<br />';
	//$message .= 'meta_key: ' . $meta_key . '^<br />';
	//$message .= 'meta_value: ' . $meta_value . '^<br />';
	//$message .= 'post_id: ' . $post_id . '^<br />';
	$meta_names = array(
		'ds62-task-state',
		'ds62-item-state',
	);
	if (in_array($meta_key, $meta_names)) {
		$states = array(
			'avaible'  => 'В очереди',
			'progress' => 'В работе',
			'check' => 'На проверку',
			'done' => 'Завершенная',
			'0' => 'В работе',
			'1' => 'Завершенная',
		);
		
		//$message .= 'meta_name: ' . $meta_name . '^<br />';
		
		$post = get_post($post_id);
		$post_title = $post->post_title;
		$post_type_object = get_post_type_object($post->post_type);
		$post_type_label = $post_type_object->labels->singular_name;
		$post_title = $post->post_title;
		//$state = get_field($meta_key, $post_id);
		$state = $meta_value;
		//$message .= 'state: ' . print_r($state, true) . '^<br />';
		if (array_key_exists($state, $states)) {
			$state = $states[$state];
		}
		//$message .= 'state: ' . print_r($state, true) . '^<br />';
		
		//$message .= 'state label: ' . $state['label'] . '^<br />';
		//$message .= 'state value: ' . $state['value'] . '^<br />';
		
		$message .= 'Новое состояние ' . $post_type_label . ' ' . $post_title . ': ' . $state;
		//$message = 'Новое состояние ' . $post_type_label . ' ' . $post_title . ': ' . $meta_value;
		live_comment_add($post_id, $message);
		/*if (in_array($post->post_type, array('ds62-task'))) {
			ds62_notify_meta($post_id);
		}*/
	}
}
add_action( 'added_post_meta', 'wpse16835_after_post_meta', 10, 4 );
add_action( 'updated_post_meta', 'wpse16835_after_post_meta', 10, 4 );

/**
 * Query comments for multiple post IDs.
 */
class T5_Multipost_Comments {
    /**
     * Post IDs, eg. array ( 1, 2, 40 )
     * @var array
     */
    protected static $post_ids = array ();

	public static function get_pagination( $comment_args = array (), $post_ids = array (), $paginattion_args = array() )
    {
        if ( array () !== $post_ids )
        {
            self::$post_ids = $post_ids;
            add_filter( 'comments_clauses', array ( __CLASS__, 'filter_where_clause' ) );
        }
		$query    = new WP_Comment_Query;
		$comments = $query->query($comment_args);
		$max_pages = (int) $query->max_num_pages;
		
		$paginattion_args['total'] = $max_pages;
		$paginate_links = paginate_links($paginattion_args);
        return array(
			'comments' => $comments,
			'pagination' => $paginate_links,
		);
    }
	
    /**
     * Called like get_comments.
     *
     * @param  array $args
     * @param  array $post_ids
     * @return array
     */
    public static function get( $args = array (), $post_ids = array () )
    {
        if ( array () !== $post_ids )
        {
            self::$post_ids = $post_ids;
            add_filter( 'comments_clauses', array ( __CLASS__, 'filter_where_clause' ) );
        }
        return get_comments( $args );
    }

    /**
     * Filter the comment query
     *
     * @param array $q Query parts, see WP_Comment_Query::query()
     *
     * @return array
     */
    public static function filter_where_clause( $q )
    {
        $ids       = implode( ', ', self::$post_ids );
        $_where_in = " AND comment_post_ID IN ( $ids )";

        if ( FALSE !== strpos( $q['where'], ' AND comment_post_ID =' ) )
        {
            $q['where'] = preg_replace(
                '~ AND comment_post_ID = \d+~',
                $_where_in,
                $q['where']
            );
        }
        else
        {
            $q['where'] .= $_where_in;
        }

        remove_filter( 'comments_clauses', array ( __CLASS__, 'filter_where_clause' ) );
        return $q;
    }
}

function ds62_live_comments_render($comments = null) {
	$result = array('<div style="margin: 32px 15px 0" class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Пока нет комментариев</span></div>');
	if (!empty($comments)) {
		$result = array();
		array_push($result, '<div class="ds62-comments">');
		foreach ($comments as $comment) {
			array_push($result, '<div class="ds62-comment">');
			array_push($result, '<div class="ds62-comment-author">');
			$user_info = get_userdata($comment->user_id);
			array_push($result, $user_info->display_name);
			array_push($result, '</div>');
			array_push($result, '<div class="ds62-comment-content">');
			array_push($result, $comment->comment_content);
			array_push($result, '</div>');
			array_push($result, '<div class="ds62-comment-date">');
			array_push($result, $comment->comment_date);
			array_push($result, '</div>');
			array_push($result, '</div>');
		}
		array_push($result, '</div>');
	}
	return implode('', $result);
}

function ds62_get_stage_tasks($stage_id) {
	$args = array(
		'post_type' => 'ds62-task',
		'meta_query' => array(
			array(
				'key' => 'ds62-task-stage',
				'value' => $stage_id,
				'compare' => '=',
			)
		)
	);
	//$query = new WP_Query($args);
	$posts = get_posts($args);
	//echo '<pre>';
	//echo 'posts:<br />';
	//print_r($posts);
	//echo '</pre>';
	return $posts;
}

function ds62_get_project_stages($project_id) {
	$args = array(
		'post_type' => 'ds62-stage',
		'meta_query' => array(
			array(
				'key' => 'ds62-stage-project',
				'value' => $project_id,
				'compare' => '=',
			)
		)
	);
	//$query = new WP_Query($args);
	$posts = get_posts($args);
	//echo '<pre>';
	//echo 'posts:<br />';
	//print_r($posts);
	//echo '</pre>';
	return $posts;
}

/*
 * Live comments Шорткод [ds62-live-comments], выводящий коментарии из нескольких постов
 */
//add_shortcode( 'ds62-live-comments', 'ds62_live_comments' );
function ds62_live_comments( $atts ){
	//echo '<pre>';
	//echo 'ds62_live_comments<br />';
	$project_id = $atts['project_id']/* ?? 2014*/;
	//$project_id = $atts['project_id'] ?? $_GET['project_id'] ?? 2014;
	//if (array_key_exists('project_id', $_GET)) {
	//	$project_id = $_GET['project_id'];
	//}
	$items = array($project_id);
	$stages = ds62_get_project_stages($project_id);
	foreach ($stages as $stage) {
		array_push($items, $stage->ID);
		$tasks = ds62_get_stage_tasks($stage->ID);
		foreach ($tasks as $task) {
			array_push($items, $task->ID);
		}
	}
	$per_page = 5;
	//$pagenum = $_GET['pagenum'] ?? 1;
	//$pagenum = $atts['pagenum'] ?? $_GET['pagenum'] ?? 1;
	$pagenum = $atts['pagenum'] ?? 1;
	$offset = ($pagenum - 1) * $per_page;
	$paged_url_patt = home_url( preg_replace( '/[?&].*/', '', $_SERVER['REQUEST_URI'] ) ) .'?pagenum=%#%';
	//echo 'paged_url_patt: ' . $paged_url_patt . '^<br />';
	
	$comment_args = array(
		'orderby' => 'comment_date',
		'order' => 'DESC',
		'offset'  => $offset,
		'number'  => $per_page,
		'no_found_rows'  => false,
	);
	//$multi_comments = T5_Multipost_Comments::get(
	//	$comment_args,
	//	$items
	//);
	$paginattion_args = array(
		'base'    => $paged_url_patt,
		'current' => $pagenum,
	);
	$multi_comments_pagination = T5_Multipost_Comments::get_pagination(
		$comment_args,
		$items,
		$paginattion_args
	);

	//$output = '<pre>' . htmlspecialchars( print_r( $multi_comments, true ) ) . '</pre>';
	//$output = ds62_live_comments_render($multi_comments);
	//$output = 'multi_comments:<br />' . ds62_live_comments_render($multi_comments) . 
	//	'multi_comments_pagination comments:<br />' . ds62_live_comments_render($multi_comments_pagination['comments']) .
	//	'multi_comments_pagination pagination:<br />' . print_r($multi_comments_pagination['pagination'], true);
	$output =
		ds62_live_comments_render($multi_comments_pagination['comments']) .
		$multi_comments_pagination['pagination'];
	//echo 'output<br />';
	//print_r($output);
	//echo '</pre>';
	return $output;
}

/*
 * Записывает всех причастных пользователей в мета-поле поста для уведомления
 */
function ds62_notify_meta($post_id)	{
	//echo 'ds62_notify_meta<br />';
	$ids = ds62_acf_notify_users($post_id);
	//echo 'ids:<br />';
	//print_r($ids);
	if (!empty($ids)) {
		update_post_meta($post_id, 'ds62-task-notify', $ids);
	}
	//return true;
}
/*
 * Срабатывает сразу после добавления комментария в базу данных, чтобы добавить уведомления о новых комментариях к посту
 */
add_action( 'comment_post', 'ds62_notify_comment', 10, 3 );
//function ds62_notify_comment( $comment_ID, $comment_approved, $commentdata ) {
function ds62_notify_comment( $comment_ID, $comment_approved) {
	//echo '<pre>';
	//echo 'ds62_notify_comment<br />';
	$comment = get_comment( $comment_ID );
	$post_id = $comment->comment_post_ID;
	//echo 'post_id: ' . $post_id . '^<br />';
	$post = get_post( $post_id );
	$post_type = $post->post_type;
	//echo 'post_type: ' . $post_type . '^<br />';
	//$post_id = $post->comment_post_ID;
	//$post = get_post($post_id);
	if (in_array($post_type, array('ds62-task'))) {
		ds62_notify_meta($post_id);
		$notify_users = get_post_meta( $post_id, 'ds62-task-notify', true );
		//echo 'notify_users:<br />';
		//print_r($notify_users);
	}
	/*$comment_content = $comment->comment_content . ' ' . 'ds62_notify_comment';
	$commentarr = [
		'comment_ID'      => $comment_ID,
		'comment_content' => $comment_content,
	];
	wp_update_comment( $commentarr );	*/
	//echo '</pre>';
	//die('end ds62_notify_comment');
	//return true;
}

/*add_action( 'comment_form', 'ds62_comment_form_test' );
function ds62_comment_form_test( $post_id ){
	echo '<pre>';
	echo 'ds62_comment_form_test<br />';
	$ids = ds62_acf_notify_users($post_id);
	echo 'ids:<br />';
	print_r($ids);
	echo '</pre>';
}*/
?>