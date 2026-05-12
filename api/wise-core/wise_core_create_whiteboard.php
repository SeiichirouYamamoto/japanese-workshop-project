<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		// ---------- Auth ----------
		$user_level = get_user_level();

		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		// ※ lesson_dates と同じ想定：教師のみ
		if (!is_teacher_level($user_level)) {
			respond_error('Forbidden', 403);
		}

		// ---------- Input ----------
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
		if (!isset($input['lesson_date_id'])) {
			respond_error('Value not found: lesson_date_id', 400);
		}

		$room_unique_code = trim((string)$input['room_unique_code']);
		$int_selected_language = (int)$input['int_selected_language'];
		$lesson_date_id = (int)$input['lesson_date_id'];

		if ($room_unique_code === '') {
			respond_error('Invalid room_unique_code', 400);
		}
		if ($lesson_date_id <= 0) {
			respond_error('Invalid lesson_date_id', 400);
		}

		// optional: board_title
		$board_title = null;
		if (array_key_exists('board_title', $input)) {
			if ($input['board_title'] === null) {
				$board_title = null;
			} else if (is_string($input['board_title'])) {
				$board_title = trim($input['board_title']);
				if ($board_title === '') {
					$board_title = null;
				}
				if ($board_title !== null && mb_strlen($board_title) > 100) {
					respond_error('Invalid board_title', 400);
				}
			} else {
				respond_error('Invalid board_title', 400);
			}
		}

		// optional: state (array|null)
		$state = [];
		if (array_key_exists('state', $input)) {
			if ($input['state'] === null) {
				$state = [];
			} else if (is_array($input['state'])) {
				$state = $input['state'];
			} else {
				respond_error('Invalid state', 400);
			}
		}

		// ---------- Room ----------
		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('Room not found', 404);
		}

		$can_access = ensure_user_can_access_room($room_id, $int_selected_language);
		if (!$can_access) {
			respond_error('Forbidden', 403);
		}

		// ---------- Create ----------
		execute_create_room_whiteboard_with_state(
			$room_id,
			$lesson_date_id,
			$board_title,
			$state
		);

	} catch (Throwable $e) {

		respond_exception($e);

	}