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

		if (!isset($input['room_unique_code'])) {
			respond_error('Value not found: room_unique_code', 400);
		}

		if (!isset($input['lesson_date_id'])) {
			respond_error('Value not found: lesson_date_id', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$room_unique_code = trim((string)$input['room_unique_code']);
		$lesson_date_id = (int)$input['lesson_date_id'];

		if ($room_unique_code === '') {
			respond_error('Invalid room_unique_code', 400);
		}

		if ($lesson_date_id <= 0) {
			respond_error('Invalid lesson_date_id', 400);
		}

		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('Room not found', 404);
		}

		$can_access = ensure_user_can_access_room(
			$room_id,
			$int_selected_language
		);

		if (!$can_access) {
			respond_error('Forbidden', 403);
		}

		// lesson_date 情報（room_id / lesson_date）を共通関数で取得
		$lesson_date_info = fetch_lesson_date_by_lesson_date_id($lesson_date_id, $int_selected_language);
		if ($lesson_date_info === null) {
			respond_error('Lesson date not found', 404);
		}

		// lesson_date_id が room に属しているか確認
		if ((int)$lesson_date_info['room_id'] !== $room_id) {
			respond_error('Forbidden', 403);
		}

		$lesson_date_str = (string)$lesson_date_info['lesson_date'];

		// 仮ラベル用 YYYY/MM/DD
		$lesson_date_for_label = $lesson_date_str;
		if ($lesson_date_str !== '') {
			$ts = strtotime($lesson_date_str);
			if ($ts !== false) {
				$lesson_date_for_label = date('Y/m/d', $ts);
			}
		}

		// whiteboards 取得
		$arr_whiteboards_raw = fetch_arr_room_whiteboards_by_lesson_date_id($lesson_date_id, $int_selected_language);

		$whiteboards = [];

		foreach ($arr_whiteboards_raw as $row) {

			$board_order = isset($row['board_order']) ? (int)$row['board_order'] : 1;
			$board_title = isset($row['board_title']) ? trim((string)$row['board_title']) : '';

			// board_title があればそれ、なければ 2026/02/27 #1 のように仮名称
			$label = $board_title !== ''
				? $board_title
				: (($lesson_date_for_label !== '' ? $lesson_date_for_label : 'Lesson') . ' #' . $board_order);

			$canvas_ops_gz = $row['canvas_ops_gz'] ?? null;

			$whiteboards[] = [
				'whiteboard_id' => (int)$row['id'],
				'lesson_date_id' => (int)$row['lesson_date_id'],

				'board_order' => $board_order,
				'board_title' => $row['board_title'] ?? null,

				// options表示用（JSが row.label を見ている前提）
				'label' => $label,

				'movable_snapshot_json' => $row['movable_snapshot_json'] ?? null,

				// blobはbase64で返す
				'canvas_ops_format' => (string)$row['canvas_ops_format'],
				'canvas_ops_gz_base64' => ($canvas_ops_gz !== null) ? base64_encode($canvas_ops_gz) : null,

				'background_image_path' => $row['background_image_path'] ?? null,
				'revision' => (int)$row['revision'],

				'created_at' => $row['created_at'] ?? null,
				'updated_at' => $row['updated_at'] ?? null
			];
		}

		respond_success($whiteboards);

	} catch (Throwable $e) {

		respond_exception($e);
	}