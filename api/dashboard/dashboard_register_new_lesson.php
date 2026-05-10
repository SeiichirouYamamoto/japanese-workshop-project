<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		/* --------------------
		login check
		-------------------- */
		$user_level = get_user_level();
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$current_user = wp_get_current_user();
		$current_user_id = (int)($current_user->ID ?? 0);
		if ($current_user_id <= 0) {
			respond_error('Login required', 401);
		}

		/* --------------------
		input
		-------------------- */
		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];

		/* --------------------
		room (session)
		-------------------- */
		$room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');
		if ($room_unique_code === '') {
			respond_error('Room not selected', 400);
		}

		/* --------------------
		mode check (basic / plus only)
		-------------------- */
		$mode = (string)get_data_workshop_mode(
			$room_unique_code,
			$user_level
		);

		if (!($mode === 'basic' || $mode === 'plus')) {
			respond_error('Forbidden', 403);
		}

		/* --------------------
		no-more-lessons messages
		-------------------- */
		$arr_no_more_lesson_messages = [
			'表示できるレッスンがありません。',
			'沒有可顯示的課程。',
		];

		$msg_no_more_lessons =
			$arr_no_more_lesson_messages[$int_selected_language]
			?? $arr_no_more_lesson_messages[0];

		/* --------------------
		1–3. get next lesson
		-------------------- */
		$data_next = get_data_next_workshop_lesson_for_user(
			$current_user_id,
			$int_selected_language
		);

		if ($data_next === null) {
			respond_success([
				'success' => true,
				'added' => false,
				'message' => $msg_no_more_lessons,
			]);
		}

		/* --------------------
		4. insert (execute_insert_data)
		-------------------- */

		$added = register_next_lesson_and_mark_previous_learned(
			$current_user_id,
			(int)$data_next['teaching_material_lesson_id'],
			(string)$data_next['title'],
			(int)$data_next['next_sort'],
			(array)($data_next['arr_registered_lesson_ids'] ?? []),
			$int_selected_language
		);

		if ($added === false) {
			respond_success([
				'success' => true,
				'added' => false,
				'message' => $msg_no_more_lessons,
			]);
		}

		respond_success([
			'success' => true,
			'added' => $added,
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
