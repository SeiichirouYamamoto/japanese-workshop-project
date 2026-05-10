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

		$room_unique_code = trim((string)$input['room_unique_code']);
		$int_selected_language = (int)$input['int_selected_language'];

		if ($room_unique_code === '') {
			respond_error('Invalid room_unique_code', 400);
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

		// ---------- DB ----------
		$pdo = connect_to_database();
		if (!($pdo instanceof PDO)) {
			respond_error('Database connection failed', 500);
		}

		global $t_room_lesson_dates;

		// 日付＝今日（必要なら後で「授業日指定」対応可能）
		$lesson_date = date('Y-m-d');

		$pdo->beginTransaction();

		// 同日複数作成に備えた seq（room_id + lesson_date の max + 1）
		$stmt = $pdo->prepare(
			'SELECT COALESCE(MAX(lesson_seq), 0) AS max_seq
			FROM ' . $t_room_lesson_dates . '
			WHERE room_id = :room_id AND lesson_date = :lesson_date'
		);
		$stmt->execute([
			':room_id' => $room_id,
			':lesson_date' => $lesson_date
		]);
		$max_seq = (int)$stmt->fetchColumn();
		$lesson_seq = $max_seq + 1;

		// t_room_lesson_dates INSERT
		$stmt = $pdo->prepare(
			'INSERT INTO ' . $t_room_lesson_dates . ' (room_id, lesson_date, lesson_seq)
			VALUES (:room_id, :lesson_date, :lesson_seq)'
		);
		$stmt->execute([
			':room_id' => $room_id,
			':lesson_date' => $lesson_date,
			':lesson_seq' => $lesson_seq
		]);

		$lesson_date_id = (int)$pdo->lastInsertId();
		if ($lesson_date_id <= 0) {
			$pdo->rollBack();
			respond_error('Failed to create lesson date', 500);
		}

		$pdo->commit();

		// 表示用 label（get_lesson_dates 側の仕様に合わせて調整OK）
		$label = $lesson_date;
		if ($lesson_seq > 1) {
			$label .= ' (' . $lesson_seq . ')';
		}

		// JS側は result.data をそのまま使う前提
		respond_success([
			'lesson_date_id' => $lesson_date_id,
			'lesson_date' => $lesson_date,
			'lesson_seq' => $lesson_seq,
			'label' => $label
		]);

	} catch (Throwable $e) {

		if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
			$pdo->rollBack();
		}

		respond_exception($e);
	}