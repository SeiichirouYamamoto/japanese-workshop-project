<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		/* --------------------
		login check（必須）
		-------------------- */
		$user_level = get_user_level();
		if ($user_level === null) {
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
		if (!isset($input['homework_id'])) {
			respond_error('Value not found: homework_id', 400);
		}
		if (!isset($input['target_date'])) {
			respond_error('Value not found: target_date', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$homework_id = (int)$input['homework_id'];
		$target_date = escape_html((string)$input['target_date']);

		if ($homework_id <= 0) {
			respond_error('Invalid homework_id', 400);
		}

		$dt = DateTimeImmutable::createFromFormat('Y-m-d', $target_date);
		if ($dt === false) {
			respond_error('Invalid target_date (expected YYYY-MM-DD)', 400);
		}
		$target_date = $dt->format('Y-m-d');

		/* --------------------
		room (session) -> access check
		-------------------- */
		$room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');
		if ($room_unique_code === '') {
			respond_error('Room not selected', 400);
		}

		$room_id = fetch_room_id_from_unique_code(
			$room_unique_code,
			$int_selected_language
		);

		if ((int)$room_id <= 0) {
			respond_error('Room not found', 404);
		}

		$can_access = ensure_user_can_access_room(
			(int)$room_id,
			(int)$int_selected_language
		);

		if (!$can_access) {
			respond_error('Forbidden', 403);
		}

		/* --------------------
		fetch homework
		-------------------- */
		$arr_homework = fetch_arr_room_homework_by_homework_id_and_target_date(
			(int)$room_id,
			$homework_id,
			$target_date,
			$int_selected_language
		);

		if (!is_array($arr_homework) || empty($arr_homework)) {
			respond_error('Homework not found', 404);
		}

		/* --------------------
		build html
		-------------------- */
		$html = build_html_homework_modal_contents(
			$arr_homework,
			$int_selected_language
		);

		respond_success([
			'html' => $html,
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
