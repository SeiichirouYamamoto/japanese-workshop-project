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
        if (!isset($input['lesson_date_id'])) {
            respond_error('Value not found: lesson_date_id', 400);
        }
        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        $room_unique_code = trim((string)$input['room_unique_code']);
        $lesson_date_id = (int)$input['lesson_date_id'];
        $int_selected_language = (int)$input['int_selected_language'];

        if ($room_unique_code === '') {
            respond_error('Invalid room_unique_code', 400);
        }
        if ($lesson_date_id <= 0) {
            respond_error('Invalid lesson_date_id', 400);
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
        global $t_room_memos;

        // lesson_date_id が room に属しているか確認（不正アクセス防止）
        $stmt = $pdo->prepare(
            'SELECT id
            FROM ' . $t_room_lesson_dates . '
            WHERE id = :lesson_date_id AND room_id = :room_id
            LIMIT 1'
        );
        $stmt->execute([
            ':lesson_date_id' => $lesson_date_id,
            ':room_id' => $room_id
        ]);
        $exists = (int)$stmt->fetchColumn();
        if ($exists <= 0) {
            respond_error('Lesson date not found', 404);
        }

        // いまは memo_order=0 固定
        $memo_order = 0;

        // まず取得
        $stmt = $pdo->prepare(
            'SELECT id, lesson_date_id, memo_order, memo_text
            FROM ' . $t_room_memos . '
            WHERE lesson_date_id = :lesson_date_id AND memo_order = :memo_order
            LIMIT 1'
        );
        $stmt->execute([
            ':lesson_date_id' => $lesson_date_id,
            ':memo_order' => $memo_order
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            respond_success([
                'memo_id' => (int)$row['id'],
                'lesson_date_id' => (int)$row['lesson_date_id'],
                'memo_order' => (int)$row['memo_order'],
                'memo_text' => (string)$row['memo_text']
            ]);
        }

        // 無ければ作成（競合対策：ユニーク制約がある場合を想定し、失敗時は再取得）
        $memo_text = '';

        try {

            $stmt = $pdo->prepare(
                'INSERT INTO ' . $t_room_memos . ' (lesson_date_id, memo_order, memo_text)
                VALUES (:lesson_date_id, :memo_order, :memo_text)'
            );
            $stmt->execute([
                ':lesson_date_id' => $lesson_date_id,
                ':memo_order' => $memo_order,
                ':memo_text' => $memo_text
            ]);

        } catch (PDOException $e) {

            // SQLSTATE 23000: integrity constraint violation（競合で既に作られていた等）
            if ((string)$e->getCode() !== '23000') {
                throw $e;
            }
        }

        // 再取得
        $stmt = $pdo->prepare(
            'SELECT id, lesson_date_id, memo_order, memo_text
            FROM ' . $t_room_memos . '
            WHERE lesson_date_id = :lesson_date_id AND memo_order = :memo_order
            LIMIT 1'
        );
        $stmt->execute([
            ':lesson_date_id' => $lesson_date_id,
            ':memo_order' => $memo_order
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            respond_error('Failed to get or create memo', 500);
        }

        respond_success([
            'memo_id' => (int)$row['id'],
            'lesson_date_id' => (int)$row['lesson_date_id'],
            'memo_order' => (int)$row['memo_order'],
            'memo_text' => (string)$row['memo_text']
        ]);

    } catch (Throwable $e) {

        respond_exception($e);
    }