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

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$int_selected_language = intval($input['int_selected_language']);
		
		$str_myLessonsForTeacherDropDownSelectRoomAreaOptions = build_html_lesson_room_select_options_for_teacher($int_selected_language);

		respond_success($str_myLessonsForTeacherDropDownSelectRoomAreaOptions);
	} catch (Throwable $e) {
		respond_exception($e);
	}

