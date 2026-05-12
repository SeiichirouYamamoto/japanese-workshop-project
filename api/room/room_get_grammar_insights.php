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

		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);
		if (!isset($input['arr_grammar_unique_code'])) respond_error('Value not found: arr_grammar_unique_code', 400);
		if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);
		if (!isset($input['value'])) respond_error('Value not found: value', 400);

		$int_selected_language = (int)$input['int_selected_language'];
		$value = (int)$input['value'];

		$room_unique_code = trim((string)($input['room_unique_code'] ?? ''));
		if ($room_unique_code === '') respond_error('Invalid value: room_unique_code', 400);
		$room_unique_code = escape_html($room_unique_code);

		$arr_grammar_unique_code = $input['arr_grammar_unique_code'];
		if (!is_array($arr_grammar_unique_code)) {
			respond_error('Invalid value: arr_grammar_unique_code', 400);
		}
		$arr_grammar_unique_code = array_map('escape_html', $arr_grammar_unique_code);

		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('room not found', 404);
		}

		$result_array = [];
		$items = [];

		$map_grammar_unique_code = [];

		$bookmarks_data = get_data_bookmarks(
			$search_scope_room_owner_user,
			$room_unique_code,
			$bookmark_filter_active,
			$int_selected_language
		);

		$map_grammar_unique_code = $bookmarks_data['map_grammar_unique_code'] ?? [];

		foreach ($arr_grammar_unique_code as $grammar_unique_code) {

			$grammar_unique_code = trim((string)$grammar_unique_code);
			if ($grammar_unique_code === '') {
				continue;
			}

			$t_masta_japanese_root_id = (int)fetch_masta_japanese_root_id_from_unique_code($grammar_unique_code, $int_selected_language);
			if ($t_masta_japanese_root_id <= 0) {
				continue;
			}

			$arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($t_masta_japanese_root_id, $int_selected_language);
			if (empty($arr_masta_japanese_root)) {
				continue;
			}

			switch ($value) {

				case $int_grammar_insights_display_titles:
				case $int_grammar_insights_display_examples:
				case $int_grammar_insights_upsert_homework:

					$is_bookmarked =
						isset($map_grammar_unique_code[$grammar_unique_code]) &&
						empty($map_grammar_unique_code[$grammar_unique_code]['deleted_at']);

					$star_html = build_html_bookmark_star(
						uniqid(),                 // DOM用
						$grammar_unique_code,
						(bool)$is_bookmarked,
						$room_unique_code
					);

					$matched_array = [];
					$matched_array[] = [
						$str_snake_to_camel_japanese_id => $arr_masta_japanese_root['id'],
						'grammarUniqueCode' => $grammar_unique_code,
						$str_snake_to_camel_japanese => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
						$str_snake_to_camel_kana => $arr_masta_japanese_root[$str_column_root_kana],
						$str_snake_to_camel_category_id => $arr_masta_japanese_root['category_id'],
						'rootText' => apply_remove_original_tags($arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]]),
						$str_snake_to_camel_root_example => apply_remove_original_tags($arr_masta_japanese_root['root_example']),
						'html' => $star_html,
					];

					$items[] = [
						$str_snake_to_camel_japanese_id => $t_masta_japanese_root_id,
						$str_snake_to_camel_unique_code => $arr_masta_japanese_root[$str_snake_to_camel_unique_code],
						'title' => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
						'array' => $matched_array
					];

					break;

				case $int_grammar_insights_active_recall:

					$arr_active_recall = fetch_arr_active_recall($t_masta_japanese_root_id, $int_selected_language);

					if (!empty($arr_active_recall)) {

						foreach ($arr_active_recall as $key => $active_recall_items) {
							$arr_active_recall[$key]['japaneseText'] = apply_remove_original_tags($active_recall_items['japaneseText']);
							$arr_active_recall[$key]['foreignLanguageText'] = apply_remove_original_tags($active_recall_items['foreignLanguageText']);
						}

						$items[] = [
							$str_snake_to_camel_japanese_id => $t_masta_japanese_root_id,
							$str_snake_to_camel_unique_code => $arr_masta_japanese_root[$str_snake_to_camel_unique_code],
							'title' => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
							'array' => $arr_active_recall
						];
					}

					break;

				case $int_grammar_insights_sentences:
				case $int_grammar_insights_random_sentences:

					$arr_search_condition_t_masta_japanese_root_id = [
						[
							[
								[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','And'],
								[$t_registered_sentences, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
							],
							''
						]
					];

					$arr_target = get_arr_registered_sentences_with_multilingual_text($arr_search_condition_t_masta_japanese_root_id, $int_selected_language);

					if (empty($arr_target)) {
						continue 2;
					}

					$items[] = [
						$str_snake_to_camel_japanese_id => $t_masta_japanese_root_id,
						$str_snake_to_camel_unique_code => $arr_masta_japanese_root[$str_snake_to_camel_unique_code],
						'title' => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
						'array' => $arr_target
					];

					break;

				case $int_grammar_insights_user_input_data:

					$arr_strSQL_select = [
						[$t_room_user_input_data, 'id as userInputDataId'],
						[$t_room_user_input_data, 'input_data as inputData']
					];

					$strSQL_from = " FROM $t_room_user_input_data";

					$arr_strSQL_where = [
						[
							[
								[$t_room_user_input_data, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
								[$t_room_user_input_data, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
							],
							''
						]
					];

					$arr_strSQL_order = [
						[$t_room_user_input_data, 'sort', 'ASC']
					];

					$strSQL_option = '';

					list($pdo_has_error, $select_has_error, $e, $arr_room_user_input_data) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
					handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

					if (!empty($arr_room_user_input_data)) {
						foreach ($arr_room_user_input_data as $key => $loop_room_user_input_data) {
							$arr_room_user_input_data[$key]['japaneseText'] = $loop_room_user_input_data['inputData'];
							$arr_room_user_input_data[$key]['foreignLanguageText'] = $loop_room_user_input_data['inputData'];
						}
					}

					$items[] = [
						$str_snake_to_camel_japanese_id => $t_masta_japanese_root_id,
						$str_snake_to_camel_unique_code => $arr_masta_japanese_root[$str_snake_to_camel_unique_code],
						'title' => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
						'array' => $arr_room_user_input_data
					];

					break;

				default:
			}
		}

		if (empty($items)) {
			respond_success([]);
		}

		switch ($value) {

			case $int_grammar_insights_display_titles:
			case $int_grammar_insights_display_examples:
			case $int_grammar_insights_upsert_homework:

				$merged_array = [];
				foreach ($items as $item) {
					if (isset($item['array']) && is_array($item['array'])) {
						$merged_array = array_merge($merged_array, $item['array']);
					}
				}

				$result_array[] = [
					$str_snake_to_camel_japanese_id => -2,
					$str_snake_to_camel_unique_code => '',
					'title' => '一覧',
					'array' => $merged_array
				];

				break;

			case $int_grammar_insights_random_sentences:

				$merged_array = [];
				foreach ($items as $item) {
					if (isset($item['array']) && is_array($item['array'])) {
						$merged_array = array_merge($merged_array, $item['array']);
					}
				}

				shuffle($merged_array);

				$result_array[] = [
					$str_snake_to_camel_japanese_id => -2,
					$str_snake_to_camel_unique_code => '',
					'title' => '例文ランダム',
					'array' => $merged_array
				];

				break;

			case $int_grammar_insights_user_input_data:
			case $int_grammar_insights_active_recall:
			case $int_grammar_insights_sentences:

				$result_array = $items;

				break;

			default:
				$result_array = [];
		}

		respond_success($result_array);

	} catch (Throwable $e) {
		respond_exception($e);
	}

