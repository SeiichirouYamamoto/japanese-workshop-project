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

		if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);

		$int_selected_language = (int)$input['int_selected_language'];

		$room_unique_code = trim((string)($input['room_unique_code'] ?? ''));
		if ($room_unique_code === '') respond_error('Invalid value: room_unique_code', 400);
		$room_unique_code = escape_html($room_unique_code);

		$arr_grammar_unique_code = $input['arr_grammar_unique_code'] ?? [];
		if (
			!is_array($arr_grammar_unique_code) ||
			empty($arr_grammar_unique_code)
		) {
			respond_error(
				'Invalid or empty: arr_grammar_unique_code',
				400
			);
		}

		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('room not found', 404);
		}
		
		$result = get_arr_homework_items($arr_grammar_unique_code, $room_id, $int_selected_language);
		respond_success($result);

	} catch (Throwable $e) {
		respond_exception($e);
	}

