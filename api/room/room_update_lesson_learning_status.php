<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		$int_selected_language = (int)($input['int_selected_language'] ?? $int_used_language_jpn);

		if (!isset($input['int_learning_status']) || $input['int_learning_status'] === '') {
			respond_error('Value not found: int_learning_status', 400);
		}

		if (!is_numeric($input['int_learning_status'])) {
			respond_error('Invalid value: int_learning_status', 400);
		}

		$int_learning_status = (int)$input['int_learning_status'];

		if (!isset($input['lesson_unique_code'])) {
			respond_error('Value not found: lesson_unique_code', 400);
		}

		$lesson_unique_code = trim((string)$input['lesson_unique_code']);
		if ($lesson_unique_code === '') {
			respond_error('Invalid value: lesson_unique_code', 400);
		}

		$lesson_unique_code = escape_html($lesson_unique_code);
		$int_lesson_id = (int)fetch_lesson_id_from_unique_code($lesson_unique_code, $int_selected_language);

		if ($int_lesson_id <= 0) {
			respond_error('lesson not found', 404);
		}

		$update_table = $t_room_lessons;
		$arr_updateSQL = [
			['learning_status', ':update_learning_status', $int_learning_status, 'PDO::PARAM_INT']
		];
		$arr_whereSQL = [
			['id', ':where_id', $int_lesson_id, 'PDO::PARAM_INT', '']
		];

		list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);
		handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

		respond_success(['success' => true]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

