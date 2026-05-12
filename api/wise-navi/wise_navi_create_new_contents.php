<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_admin_level($user_level)) {
			respond_error('Forbidden', 403);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['unique_code'])) respond_error('Value not found: unique_code', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);
		if (!isset($input['current_url'])) respond_error('Value not found: current_url', 400);

		$room_contents_code = trim((string)($input['unique_code'] ?? ''));
		if ($room_contents_code === '') {
			respond_error('Invalid value: unique_code', 400);
		}
		$room_contents_code = escape_html($room_contents_code);

		$int_selected_language = (int)$input['int_selected_language'];

		$current_url = (string)($input['current_url'] ?? '');
		$path = parse_url($current_url, PHP_URL_PATH);
		$page = basename(rtrim((string)$path, '/'));

		$allowed_pages = [
			'manage-wise-navigations',
			'manage-wise-navigation-waypoints',
			'manage-wise-navigation-scripts',
			'manage-wise-navigation-items'
		];

		if (!in_array($page, $allowed_pages, true)) {
			respond_error('invalid page', 400);
		}

		if ($page === 'manage-wise-navigation-items') {

			if (!isset($input['send_layer_id'])) respond_error('Value not found: send_layer_id', 400);

			$send_layer_id = (int)$input['send_layer_id'];
			if ($send_layer_id <= 0) respond_error('Invalid value: send_layer_id', 400);

			$generated = generate_unique_code($t_wise_navigation_items, 'unique_code', 'id', $int_selected_language);
			if ($generated === null) respond_error('Failed to generate unique code', 500);

			$wise_navigation_script_id = (int)fetch_wise_navigation_script_id_from_unique_code($room_contents_code, $int_selected_language);
			if ($wise_navigation_script_id <= 0) respond_error('wise_navigation_script not found', 404);

			$add_sort = count_next_sort($t_wise_navigation_items, 'wise_navigation_script_id', $wise_navigation_script_id, $int_selected_language);

			$arr_insertSQL = [
				['unique_code', '?', $generated, 'PDO::PARAM_STR'],
				['wise_navigation_script_id', '?', $wise_navigation_script_id, 'PDO::PARAM_INT'],
				['layer_id', '?', $send_layer_id, 'PDO::PARAM_INT'],
				['is_new', '?', FLAG_TRUE, 'PDO::PARAM_INT'],
				['sort', '?', $add_sort, 'PDO::PARAM_INT']
			];

			list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_wise_navigation_items, $arr_insertSQL);
			handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

			respond_success(['int_selected_language' => $int_selected_language]);
		}

		if (!isset($input['send_array'])) respond_error('Value not found: send_array', 400);

		$send_array = $input['send_array'];

		if (!is_array($send_array)) {
			respond_error('Invalid value: send_array', 400);
		}

		if (empty($send_array)) {
			respond_error('send_array must not be empty', 400);
		}

		foreach ($send_array as $inputValue) {

			switch ($page) {

				case 'manage-wise-navigations':

					$generated = generate_unique_code($t_wise_navigations, 'unique_code', 'id', $int_selected_language);
					if ($generated === null) respond_error('Failed to generate unique code', 500);

					$registered_sentence_id = (int)fetch_registered_sentence_id_from_unique_code($room_contents_code, $int_selected_language);
					if ($registered_sentence_id <= 0) respond_error('registered_sentence not found', 404);

					$title = trim((string)$inputValue);
					if ($title === '') respond_error('Invalid value in send_array', 400);

					$add_sort = count_next_sort($t_wise_navigations, 'registered_sentence_id', $registered_sentence_id, $int_selected_language);

					$arr_insertSQL = [
						['unique_code', '?', $generated, 'PDO::PARAM_STR'],
						['registered_sentence_id', '?', $registered_sentence_id, 'PDO::PARAM_INT'],
						['title', '?', escape_html($title), 'PDO::PARAM_STR'],
						['is_published', '?', FLAG_TRUE, 'PDO::PARAM_INT'],
						['sort', '?', $add_sort, 'PDO::PARAM_INT']
					];

					list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_wise_navigations, $arr_insertSQL);
					handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

					break;

				case 'manage-wise-navigation-waypoints':

					$generated = generate_unique_code($t_wise_navigation_waypoints, 'unique_code', 'id', $int_selected_language);
					if ($generated === null) respond_error('Failed to generate unique code', 500);

					$wise_navigation_id = (int)fetch_wise_navigation_id_from_unique_code($room_contents_code, $int_selected_language);
					if ($wise_navigation_id <= 0) respond_error('wise_navigation not found', 404);

					$title = trim((string)$inputValue);
					if ($title === '') respond_error('Invalid value in send_array', 400);

					$add_sort = count_next_sort($t_wise_navigation_waypoints, 'wise_navigation_id', $wise_navigation_id, $int_selected_language);

					$arr_insertSQL = [
						['unique_code', '?', $generated, 'PDO::PARAM_STR'],
						['wise_navigation_id', '?', $wise_navigation_id, 'PDO::PARAM_INT'],
						['title', '?', escape_html($title), 'PDO::PARAM_STR'],
						['sort', '?', $add_sort, 'PDO::PARAM_INT']
					];

					list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_wise_navigation_waypoints, $arr_insertSQL);
					handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

					break;

				case 'manage-wise-navigation-scripts':

					$generated = generate_unique_code($t_wise_navigation_scripts, 'unique_code', 'id', $int_selected_language);
					if ($generated === null) respond_error('Failed to generate unique code', 500);

					$wise_navigation_waypoint_id = (int)fetch_wise_navigation_waypoint_id_from_unique_code($room_contents_code, $int_selected_language);
					if ($wise_navigation_waypoint_id <= 0) respond_error('wise_navigation_waypoint not found', 404);

					if (!is_numeric($inputValue)) respond_error('Invalid value in send_array', 400);

					$script_type_id = (int)$inputValue;
					if ($script_type_id <= 0) respond_error('Invalid value in send_array', 400);

					$add_sort = count_next_sort($t_wise_navigation_scripts, 'wise_navigation_waypoint_id', $wise_navigation_waypoint_id, $int_selected_language);

					$arr_insertSQL = [
						['unique_code', '?', $generated, 'PDO::PARAM_STR'],
						['wise_navigation_waypoint_id', '?', $wise_navigation_waypoint_id, 'PDO::PARAM_INT'],
						['script_type_id', '?', $script_type_id, 'PDO::PARAM_INT'],
						['message_japanese', '?', $str_avoid_null_proxy, 'PDO::PARAM_STR'],
						['message_chinese', '?', $str_avoid_null_proxy, 'PDO::PARAM_STR'],
						['sort', '?', $add_sort, 'PDO::PARAM_INT']
					];

					list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_wise_navigation_scripts, $arr_insertSQL);
					handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

					break;

				default:
					respond_error('invalid page', 400);
			}
		}

		respond_success(['int_selected_language' => $int_selected_language]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

