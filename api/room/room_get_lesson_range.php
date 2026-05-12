<?php

	header('Content-Type: application/json; charset=utf-8');
	
	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_teacher_level($user_level)) {
			respond_error('Forbidden', 403);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['room_unique_code'])) {
			respond_error('Value not found: room_unique_code', 400);
		}
		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$int_selected_language = intval($input['int_selected_language']);

		$room_unique_code = escape_html($input['room_unique_code']);
		$room_id = fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);

		$_SESSION['wise']['room_unique_code'] = $room_unique_code;

		$arr_strSQL_select = [
			[$t_room_lessons,'id'],
			[$t_room_lessons,'title'],
			[$t_room_lessons,'learning_status'],
			[$t_room_lessons,'sort']
		];

		$strSQL_from = ' FROM ' . $t_room_lessons;

		$arr_strSQL_where = [
			[
				[
					[$t_room_lessons,'room_id','=',$room_id,'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_strSQL_order = [
			[$t_room_lessons,'sort','ASC']
		];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		if (empty($arr_lessons)) {
			unset($_SESSION['arr_already_learned_list']);
			respond_success([]);
		}

		respond_success($arr_lessons);
	} catch (Throwable $e) {
		respond_exception($e);
	}

