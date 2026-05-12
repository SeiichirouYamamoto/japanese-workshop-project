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

		if (!isset($input['unique_code'])) respond_error('Value not found: unique_code', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);

		$unique_code = trim((string)($input['unique_code'] ?? ''));
		if ($unique_code === '') respond_error('Invalid value: unique_code', 400);
		$unique_code = escape_html($unique_code);

		$int_selected_language = (int)$input['int_selected_language'];

		$step_unit_id = (int)fetch_lesson_step_unit_id_from_unique_code($unique_code, $int_selected_language);
		if ($step_unit_id <= 0) respond_error('step unit not found', 404);

		$current_user = wp_get_current_user();
		$current_user_id = (int)$current_user->ID;
		if ($current_user_id <= 0) respond_error('Invalid user', 403);

		$arr_strSQL_select = [
			[$t_room_lesson_contents, 'id as lessonContentId'],
			[$t_room_lesson_contents, 'masta_japanese_root_id as ' . $str_snake_to_camel_japanese_id],
			[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as japanese'],
			[$t_room_lesson_contents, 'sort']
		];

		$strSQL_from = " FROM
			(
				(
					(
						(
							$t_rooms
							INNER JOIN $t_room_lessons
							ON $t_rooms.id = $t_room_lessons.room_id
						)
						INNER JOIN $t_room_lesson_steps
						ON $t_room_lessons.id = $t_room_lesson_steps.lesson_id
					)
					INNER JOIN $t_room_lesson_step_units
					ON $t_room_lesson_steps.id = $t_room_lesson_step_units.lesson_step_id
				)
				INNER JOIN $t_room_lesson_contents
				ON $t_room_lesson_step_units.id = $t_room_lesson_contents.step_unit_id
			)
			INNER JOIN $t_masta_japanese_root
			ON $t_room_lesson_contents.masta_japanese_root_id = $t_masta_japanese_root.id";

		$arr_strSQL_where = [
			[
				[
					[$t_room_lesson_step_units, 'id', '=', $step_unit_id, 'PDO::PARAM_INT', 'And'],
					[$t_rooms, 'room_owner_user_id', '=', $current_user_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [
			[$t_room_lesson_contents, 'sort', 'ASC']
		];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_lesson_contents) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		respond_success($arr_lesson_contents);

	} catch (Throwable $e) {
		respond_exception($e);
	}

