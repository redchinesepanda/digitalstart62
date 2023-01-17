<?php
/*
 * How to allow registered users to change their user role through frontend?
 */
add_shortcode( 'ds62-upgrade-to-manager', 'ds62_upgrade_to_manager' );
function ds62_upgrade_to_manager($args) {
	//echo '<pre>';
	//echo 'ds62_upgrade_to_manager<br />';
    $output = '';
    if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user(); // getting & setting the current user 
		$current_roles = ( array ) $current_user->roles;
		if (
			in_array('administrator', $current_roles)
			|| in_array('ds_curator_of_the_project', $current_roles)
		) {
			$user_id = 0;
			$role = '';
			if (array_key_exists('user_id', $args)) {
				$user_id = $args['user_id'];
			}
			if (
				array_key_exists('role', $_POST)
				&& array_key_exists('user_id', $_POST)
			) {
				$role = sanitize_key( $_POST['role'] );
				$user_id = sanitize_key( $_POST['user_id'] );
			}
			//echo 'user_id: ' . $user_id . '^<br />';
			$user = get_user_by('ID', $args['user_id'] );
			$user_roles = $user->roles;
			//echo 'user_roles:<br />';
			//print_r($user_roles);
			if (in_array('ds_project_participant', $user_roles)) {
				if (!empty($role)) {
					//echo 'role: ' . $role . '^<br />';
					$user->set_role( $role );
					$output .= 'Пользователь повышен до менеджера';
				} else {
					$form = '
						<form method="post" action="">
							<input type="hidden" name="user_id" value="' . $user_id . '" />
							<input type="hidden" name="role" value="ds_project_manager" />
							<input type="submit" value="Повысить до руководителя" />
						</form>
					';
					$output .= $form;
				}
			} else {
				$output .= 'Пользователь не является участником';
			}
		}
	}
	//echo '</pre>';
    return $output;
}

/*
 * WPUF user listing show rank up button
 */
add_action( 'wpuf_user_profile_before_content', 'wpuf_user_profile_test' );
function wpuf_user_profile_test(){
	//echo '<pre>';
	//echo 'wpuf_user_profile_test<br />';
	$user_id = isset( $_GET['user_id'] ) ? intval( wp_unslash( $_GET['user_id'] ) ) : '';
	$args = array('user_id' => $user_id);
	echo ds62_upgrade_to_manager($args);
	//echo 'user_id: ' . $user_id . '<br />';
	//echo '</pre>';
	//return $args;
}
?>