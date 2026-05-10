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

		if (!isset($input['whiteboard_id'])) {
			respond_error('Value not found: whiteboard_id', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$whiteboard_id = (int)$input['whiteboard_id'];

		if ($whiteboard_id <= 0) {
			respond_error('Invalid whiteboard_id', 400);
		}

		global $t_room_whiteboards;

		$pdo = connect_to_database();
		if (empty($pdo)) {
			respond_error('Database connection failed', 500);
		}

		$sql = '
			SELECT
				id,
				lesson_date_id,
				board_order,
				board_title,
				movable_snapshot_json,
				canvas_ops_gz,
				canvas_ops_format,
				background_image_path,
				revision,
				created_at,
				updated_at
			FROM ' . $t_room_whiteboards . '
			WHERE id = :whiteboard_id
			LIMIT 1
		';

		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':whiteboard_id', $whiteboard_id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			respond_error('Whiteboard not found', 404);
		}

		$lesson_date_id = isset($row['lesson_date_id']) ? (int)$row['lesson_date_id'] : 0;

		if ($lesson_date_id <= 0) {
			respond_error('Whiteboard not found', 404);
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
			decode: movable_snapshot_json
		------------------------------ */

		$movable_snapshot_json = $row['movable_snapshot_json'] ?? null;
		$movable_pack = null;

		if (is_string($movable_snapshot_json) && $movable_snapshot_json !== '') {
			$decoded = json_decode($movable_snapshot_json, true);
			if (is_array($decoded)) {
				$movable_pack = $decoded;
			}
		}

		$movable_snapshot = [];
		$canvas_meta_from_movable = null;

		if (is_array($movable_pack)) {
			if (isset($movable_pack['movableElementsSnapshot']) && is_array($movable_pack['movableElementsSnapshot'])) {
				$movable_snapshot = $movable_pack['movableElementsSnapshot'];
			}

			if (isset($movable_pack['canvasMeta']) && is_array($movable_pack['canvasMeta'])) {
				$canvas_meta_from_movable = $movable_pack['canvasMeta'];
			}
		}

		/* ------------------------------
			decode: canvas_ops_gz
		------------------------------ */

		$stroke_history = [];
		$canvas_meta_from_ops = null;
		$title = null;

		$canvas_ops_format = isset($row['canvas_ops_format']) ? (string)$row['canvas_ops_format'] : '';
		$canvas_ops_gz = $row['canvas_ops_gz'] ?? null;

		if ($canvas_ops_gz !== null) {

			// MySQL(LONGBLOB) は PDO で resource になるケースがあるので吸収
			if (is_resource($canvas_ops_gz)) {
				$canvas_ops_gz = stream_get_contents($canvas_ops_gz);
			}

			if (is_string($canvas_ops_gz) && $canvas_ops_gz !== '') {

				if ($canvas_ops_format === '' || $canvas_ops_format === 'ops_gzip_json_v1') {

					$ops_json = gzdecode($canvas_ops_gz);

					if ($ops_json !== false && is_string($ops_json) && $ops_json !== '') {
						$ops = json_decode($ops_json, true);

						if (is_array($ops)) {

							if (isset($ops['strokeHistory']) && is_array($ops['strokeHistory'])) {
								$stroke_history = $ops['strokeHistory'];
							}

							if (isset($ops['canvasMeta']) && is_array($ops['canvasMeta'])) {
								$canvas_meta_from_ops = $ops['canvasMeta'];
							}

							if (isset($ops['title']) && is_string($ops['title'])) {
								$title = $ops['title'];
							}
						}
					}

				} else {

					// 未対応フォーマット
					respond_error('Unsupported canvas_ops_format', 500);

				}
			}
		}

		/* ------------------------------
			build state (JS restore 用)
		------------------------------ */

		$canvas_meta = $canvas_meta_from_ops;
		if ($canvas_meta === null) {
			$canvas_meta = $canvas_meta_from_movable;
		}

		$state = [
			'movableElementsSnapshot' => $movable_snapshot,
			'strokeHistory' => $stroke_history,
			'canvasMeta' => $canvas_meta,
			'title' => $title
		];

		// 返却は JS が renderWhiteboard() で吸収できる形式
		respond_success([
			'whiteboard_id' => (int)$row['id'],
			'lesson_date_id' => $lesson_date_id,
			'board_order' => isset($row['board_order']) ? (int)$row['board_order'] : 1,
			'board_title' => $row['board_title'] ?? null,
			'background_image_path' => $row['background_image_path'] ?? null,
			'revision' => isset($row['revision']) ? (int)$row['revision'] : 1,
			'created_at' => $row['created_at'] ?? null,
			'updated_at' => $row['updated_at'] ?? null,
			'state' => $state
		]);

	} catch (Throwable $e) {

		respond_exception($e);

	}