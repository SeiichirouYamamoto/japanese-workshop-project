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

		if (!isset($input['int_search_scope'])) respond_error('Value not found: int_search_scope', 400);
		if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);
		if (!isset($input['int_bookmark_filter'])) respond_error('Value not found: int_bookmark_filter', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);

		
		$int_search_scope = intval($input['int_search_scope']);
		$int_bookmark_filter = (int)$input['int_bookmark_filter'];
		$int_selected_language = (int)$input['int_selected_language'];

		$room_unique_code = trim((string)($input['room_unique_code'] ?? ''));
		if ($room_unique_code === '') respond_error('Invalid value: room_unique_code', 400);
		$room_unique_code = escape_html($room_unique_code);


		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('room not found', 404);
		}

		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_by_bookmarks(
			$search_scope_room_owner_user,
			$room_id,
			$int_bookmark_filter,
			$int_selected_language
		);

		// arr_masta_japanese_rootをユニーク化
		$arr_unique_by_id = [];

		foreach ($arr_masta_japanese_root as $row) {

			if (isset($row[$str_snake_to_camel_japanese_id]) === false) {
				continue;
			}

			$int_japanese_id = (int)$row[$str_snake_to_camel_japanese_id];

			if ($int_japanese_id <= 0) {
				continue;
			}

			if (isset($arr_unique_by_id[$int_japanese_id]) === true) {
				continue;
			}

			$arr_unique_by_id[$int_japanese_id] = $row;
		}

		// $arr_masta_japanese_root = $arr_unique_by_id;
		$arr_masta_japanese_root = array_values($arr_unique_by_id);

		respond_success($arr_masta_japanese_root);

	} catch (Throwable $e) {
		respond_exception($e);
	}

