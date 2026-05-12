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
		if (!isset($input['updated_at'])) {
			respond_error('Value not found: updated_at', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$memo_id = (int)$input['memo_id'];
		$client_updated_at = (string)$input['updated_at'];

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

		if ($room_id === null) {
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
		fetch memo (for updated_at)
		-------------------- */
		$arr_lesson_memo = fetch_arr_lesson_memo_by_memo_id(
			$memo_id,
			$int_selected_language
		);

		if (!is_array($arr_lesson_memo) || empty($arr_lesson_memo)) {
			respond_error('Lesson memo not found', 404);
		}

		/*
			updated_at の取り出し方は、返却構造に合わせて吸収します。
			- 単一メモ（連想配列）でも
			- 複数行（配列 of 連想配列）でも対応
		*/
		$db_updated_at = '';

		if (isset($arr_lesson_memo['updated_at'])) {
			$db_updated_at = (string)$arr_lesson_memo['updated_at'];
		} else if (isset($arr_lesson_memo[0]) && is_array($arr_lesson_memo[0])) {

			$first_row = $arr_lesson_memo[0];

			if (isset($first_row['updated_at'])) {
				$db_updated_at = (string)$first_row['updated_at'];
			} else if (isset($first_row['memo_updated_at'])) {
				$db_updated_at = (string)$first_row['memo_updated_at'];
			}
		}

		if ($db_updated_at === '') {
			respond_error('Value not found: updated_at (db)', 500);
		}

		/* --------------------
		compare updated_at
		-------------------- */
		$has_updated = ($client_updated_at !== $db_updated_at);

		if(!$has_updated){
			respond_success([
				'has_updated' => $has_updated,
				'updated_at' => $db_updated_at
			]);
		}

		$html = build_html_lesson_memo_modal_contents(
			$arr_lesson_memo,
			$int_selected_language
		);

		respond_success([
			'has_updated' => $has_updated,
			'updated_at' => $db_updated_at,
			'html' => $html,
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
