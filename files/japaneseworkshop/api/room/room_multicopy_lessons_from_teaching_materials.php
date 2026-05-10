<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_teacher_level($user_level)) {
			respond_error('Forbidden', 403);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['int_start_id'])) respond_error('Value not found: int_start_id', 400);
		if (!isset($input['int_end_id'])) respond_error('Value not found: int_end_id', 400);
		if (!isset($input['send_unique_code'])) respond_error('Value not found: send_unique_code', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);

		$int_start_id = (int)$input['int_start_id'];
		$int_end_id = (int)$input['int_end_id'];
		$int_selected_language = (int)$input['int_selected_language'];

		if ($int_start_id <= 0) respond_error('Invalid value: int_start_id', 400);
		if ($int_end_id <= 0) respond_error('Invalid value: int_end_id', 400);

		$unique_code = trim((string)($input['send_unique_code'] ?? ''));
		if ($unique_code === '') respond_error('Invalid value: send_unique_code', 400);
		$unique_code = escape_html($unique_code);

		$room_id = (int)fetch_room_id_from_unique_code($unique_code, $int_selected_language);
		if ($room_id <= 0) respond_error('room not found', 404);

		$t_teaching_material_sets_id = (int)validate_teaching_material_range($int_start_id, $int_end_id, $int_selected_language);
		if ($t_teaching_material_sets_id <= 0) respond_error('Invalid range', 400);

		$pdo = connect_to_database();
		if (empty($pdo)) {
			respond_error('Database connection failed', 500);
		}

		$pdo->beginTransaction();

		$sql = "
			SELECT
				s." . $arr_columns_masta_teaching_material_sets[$int_selected_language] . " AS set_title,
				l." . $arr_columns_masta_teaching_material_levels[$int_selected_language] . " AS level_title,
				le." . $arr_columns_masta_teaching_material_lessons[$int_selected_language] . " AS lesson_title,
				le.id AS lesson_id
			FROM $t_teaching_material_sets s
			INNER JOIN $t_teaching_material_levels l
				ON s.id = l.set_id
			INNER JOIN $t_teaching_material_lessons le
				ON l.id = le.level_id
			WHERE s.id = ?
			ORDER BY s.sort ASC, l.sort ASC, le.sort ASC
		";

		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(1, $t_teaching_material_sets_id, PDO::PARAM_INT);
		$stmt->execute();
		$arr_teaching_material_lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (empty($arr_teaching_material_lessons)) {
			$pdo->rollBack();
			$pdo = null;
			respond_success([]);
		}

		$stmt_insert = $pdo->prepare("
			INSERT INTO $t_room_lessons
				(unique_code, room_id, teaching_material_lesson_id, title, learning_status, sort)
			VALUES
				(?, ?, ?, ?, ?, ?)
		");

		$collecting = false;
		$inserted = 0;
		$next_sort = null;

		foreach ($arr_teaching_material_lessons as $row) {

			$int_teaching_material_lesson_id = (int)($row['lesson_id'] ?? 0);
			if ($int_teaching_material_lesson_id <= 0) {
				continue;
			}

			if ($int_teaching_material_lesson_id === $int_start_id) {
				$collecting = true;
				$next_sort = count_next_sort($t_room_lessons, 'room_id', $room_id, $int_selected_language);
				$next_sort = (int)$next_sort;
			}

			if ($collecting) {

				$title_raw = (string)($row['set_title'] ?? '') . ' ' . (string)($row['level_title'] ?? '') . ' ' . (string)($row['lesson_title'] ?? '');
				$title_raw = trim($title_raw);
				if ($title_raw === '') {
					$title_raw = $str_avoid_null_proxy;
				}

				$generated = generate_unique_code($t_room_lessons, 'unique_code', 'id', $int_selected_language);
				if ($generated === null) {
					throw new Exception('Failed to generate unique code');
				}

				$add_sort = $next_sort;
        		$next_sort++;

				$stmt_insert->bindValue(1, $generated, PDO::PARAM_STR);
				$stmt_insert->bindValue(2, $room_id, PDO::PARAM_INT);
				$stmt_insert->bindValue(3, $int_teaching_material_lesson_id, PDO::PARAM_INT);
				$stmt_insert->bindValue(4, escape_html_with_nl2br($title_raw), PDO::PARAM_STR);
				$stmt_insert->bindValue(5, $int_default_value_proxy_0, PDO::PARAM_INT);
				$stmt_insert->bindValue(6, $add_sort, PDO::PARAM_INT);
				$stmt_insert->execute();

				$inserted++;
			}

			if ($int_teaching_material_lesson_id === $int_end_id) {
				break;
			}
		}

		if (!$collecting) {
			throw new Exception('start_id not found in lessons');
		}

		$pdo->commit();
		$pdo = null;

		respond_success([
			'int_selected_language' => $int_selected_language,
			'inserted' => $inserted
		]);

	} catch (Throwable $e) {

		if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
			$pdo->rollBack();
		}

		respond_exception($e);
	}

