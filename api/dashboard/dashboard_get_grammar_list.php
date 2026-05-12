<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
		if ($user_level === null) {
			respond_error('Login required', 401);
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

		$int_selected_language = (int)$input['int_selected_language'];
		$room_unique_code = trim((string)$input['room_unique_code']);


		$lesson_id = isset($input['lesson_id']) && $input['lesson_id'] !== null
			? (int)$input['lesson_id']
			: null;

		$teaching_material_lesson_id = isset($input['teaching_material_lesson_id']) && $input['teaching_material_lesson_id'] !== null
			? (int)$input['teaching_material_lesson_id']
			: null;

		if ($room_unique_code === '') {
			respond_error('Invalid room_unique_code', 400);
		}

		if ($lesson_id === null && $teaching_material_lesson_id === null) {
			respond_error('Value not found: lesson_id or teaching_material_lesson_id', 400);
		}

		$arr_bookmarks_data = get_data_bookmarks(
			$search_scope_current_user,
			$room_unique_code,
			$bookmark_filter_active,
			$int_selected_language
		);

		$arr_lessons = get_arr_lessons_for_workshop(
			$room_unique_code,
			$int_selected_language
		);

		if (empty($arr_lessons)) {
			respond_success([
				'success' => true,
				'html' => ''
			]);
		}

		$arr_lessons_filtered = [];

		$arr_rooms = fetch_arr_rooms_from_unique_code(
			$room_unique_code,
			$int_selected_language
		);

		$room_id = (int)($arr_rooms[INDEX_FIRST]['id'] ?? 0);

		foreach ($arr_lessons as $les) {

			$les_lesson_id = isset($les['lesson_id']) && $les['lesson_id'] !== null
				? (int)$les['lesson_id']
				: null;

			$les_teaching_material_lesson_id = isset($les['teaching_material_lesson_id']) && $les['teaching_material_lesson_id'] !== null
				? (int)$les['teaching_material_lesson_id']
				: null;

			if ($lesson_id !== null && $les_lesson_id === $lesson_id) {
				$arr_lessons_filtered[] = $les;
				continue;
			}

			if ($lesson_id === null && $teaching_material_lesson_id !== null && $les_teaching_material_lesson_id === $teaching_material_lesson_id) {
				$arr_lessons_filtered[] = $les;
				continue;
			}
		}

		if (empty($arr_lessons_filtered)) {
			respond_error('Lesson not found', 404);
		}

		$type = 'workshop_one_lesson';
		$contents_tree_flags = [];
		$arr_search_condition_for_category = [];

		$arr_lesson_content_information = get_data_lesson_content_information(
			$type,
			$room_id,
			$contents_tree_flags,
			$arr_lessons_filtered,
			$arr_bookmarks_data,
			$arr_search_condition_for_category,
			$int_selected_language
		);
		
		$html = (string)($arr_lesson_content_information['str_html'] ?? '');

		respond_success([
			'success' => true,
			'html' => $html
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
