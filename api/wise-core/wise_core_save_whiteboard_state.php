<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();

		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		// ※ lesson_dates と同じ想定：教師のみ
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

		if (!isset($input['lesson_date_id'])) {
			respond_error('Value not found: lesson_date_id', 400);
		}

		if (!isset($input['whiteboard_id'])) {
			respond_error('Value not found: whiteboard_id', 400);
		}

		if (!isset($input['state'])) {
			respond_error('Value not found: state', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$lesson_date_id = (int)$input['lesson_date_id'];
		$whiteboard_id = (int)$input['whiteboard_id'];
		$state = $input['state'];

		if ($lesson_date_id <= 0) {
			respond_error('Invalid lesson_date_id', 400);
		}

		if ($whiteboard_id <= 0) {
			respond_error('Invalid whiteboard_id', 400);
		}

		if (!is_array($state)) {
			respond_error('Invalid state', 400);
		}

		// lesson_date 情報（room_id / lesson_date）を共通関数で取得
		$lesson_date_info = fetch_lesson_date_by_lesson_date_id($lesson_date_id, $int_selected_language);
		if ($lesson_date_info === null) {
			respond_error('Lesson date not found', 404);
		}

		$room_id = (int)$lesson_date_info['room_id'];
		if ($room_id <= 0) {
			respond_error('Lesson date not found', 404);
		}

		$can_access = ensure_user_can_access_room(
			$room_id,
			$int_selected_language
		);

		if (!$can_access) {
			respond_error('Forbidden', 403);
		}

		/* ------------------------------
			保存データ構築
		------------------------------ */

		execute_update_room_whiteboard_state($whiteboard_id, $lesson_date_id, $state);


		respond_success([
			'whiteboard_id' => $whiteboard_id,
			'lesson_date_id' => $lesson_date_id
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}