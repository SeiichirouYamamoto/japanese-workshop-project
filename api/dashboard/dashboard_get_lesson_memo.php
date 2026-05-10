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
		if (!isset($input['memo_id'])) {
			respond_error('Value not found: memo_id', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$memo_id = (int)$input['memo_id'];

		if ($memo_id <= 0) {
			respond_error('Invalid memo_id', 400);
		}

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
		fetch memo
		-------------------- */
		$arr_lesson_memo = fetch_arr_lesson_memo_by_memo_id(
			$memo_id,
			$int_selected_language
		);

		/* ---- 配列じゃない → エラー ---- */
		if (!is_array($arr_lesson_memo)) {
			respond_error('Lesson memo not found', 404);
		}

		/* --------------------
		build html
		-------------------- */
		$html = build_html_lesson_memo_modal_contents(
			$arr_lesson_memo,
			$int_selected_language
		);

		respond_success([
			'html' => $html,
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
