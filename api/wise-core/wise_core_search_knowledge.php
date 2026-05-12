<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		// Teacher permission required
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

		// Required
		if (!isset($input['int_search_scope'])) respond_error('Value not found: int_search_scope', 400);
		if (!isset($input['search_word'])) respond_error('Value not found: search_word', 400);
		if (!isset($input['int_category_id'])) respond_error('Value not found: int_category_id', 400);
		if (!isset($input['int_sub_category_id'])) respond_error('Value not found: int_sub_category_id', 400);
		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);

		// Optional
		// int_matching_type, int_learningScope

		$int_search_scope = intval($input['int_search_scope']);

		$search_word = trim((string)($input['search_word'] ?? ''));
		$search_word = escape_html($search_word);

		$int_category_id = intval($input['int_category_id'] ?? SELECT_ALL);
		$int_sub_category_id = intval($input['int_sub_category_id'] ?? SELECT_ALL);
		$int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);

		$int_matching_type = intval($input['int_matching_type'] ?? $int_matching_type_partial_matching);
		$int_learningScope = intval($input['int_learningScope'] ?? SELECT_ALL);

		// ------------------------------------------------------------
		// scope に応じて「許可IDリスト」「条件テーブル」「条件カラム」「対象ID」を決める
		// ------------------------------------------------------------
		$arr_allowed_ids = [];
		$str_condition_table = $t_masta_japanese_sub_category;
		$str_condition_column = 'category_id';
		$int_target_id = SELECT_ALL;

		switch ($int_search_scope) {

			case $search_scope_wise_category:
				if (!isset($arr_select_japanese_category_id_search_grammar) || !is_array($arr_select_japanese_category_id_search_grammar)) {
					respond_error('Category config missing: arr_select_japanese_category_id_search_grammar', 500);
				}
				$arr_allowed_ids = array_keys($arr_select_japanese_category_id_search_grammar);
				$str_condition_table = $t_masta_japanese_sub_category;
				$str_condition_column = 'category_id';
				$int_target_id = $int_category_id;
				break;

			case $search_scope_wise_sub_category:
				$arr_masta_japanese_sub_category = fetch_arr_masta_japanese_sub_categories_for_grammar($int_selected_language);
				if (!is_array($arr_masta_japanese_sub_category)) {
					$arr_masta_japanese_sub_category = [];
				}

				$arr_allowed_ids = array_column($arr_masta_japanese_sub_category, 'id');

				$str_condition_table = $t_masta_japanese_sub_category;
				$str_condition_column = 'id';
				$int_target_id = $int_sub_category_id;
				break;

			case $search_scope_lesson_contents:
				
				if (!isset($arr_select_japanese_category_id_search_grammar) || !is_array($arr_select_japanese_category_id_search_grammar)) {
					respond_error('Category config missing: arr_select_japanese_category_id_search_grammar', 500);
				}
				$arr_allowed_ids = array_keys($arr_select_japanese_category_id_search_grammar);
				$str_condition_table = $t_masta_japanese_sub_category;
				$str_condition_column = 'category_id';
				$int_target_id = $int_category_id;
				break;

			case $search_scope_manage_lesson_contents:
				if (!isset($arr_select_japanese_category_id_search_lesson_contents) || !is_array($arr_select_japanese_category_id_search_lesson_contents)) {
					respond_error('Category config missing: arr_select_japanese_category_id_search_lesson_contents', 500);
				}
				$arr_allowed_ids = array_keys($arr_select_japanese_category_id_search_lesson_contents);
				$str_condition_table = $t_masta_japanese_sub_category;
				$str_condition_column = 'category_id';
				$int_target_id = $int_category_id;
				break;

			default:
				respond_error('Invalid int_search_scope', 400);
		}

		// SELECT_ALL を除外し、数値として正規化
		$arr_allowed_ids = array_values(array_filter(
			array_map('intval', $arr_allowed_ids),
			function ($v) {
				return $v !== SELECT_ALL;
			}
		));

		// ------------------------------------------------------------
		// scope条件（カテゴリ/サブカテゴリ）を構築
		// ------------------------------------------------------------
		$arr_category_condition = [];

		if ($int_target_id === SELECT_ALL) {

			$lastIndex = count($arr_allowed_ids) - 1;
			if ($lastIndex < 0) {
				respond_success([]);
			}

			foreach ($arr_allowed_ids as $i => $id) {

				$logic = ($i === $lastIndex) ? '' : 'Or';

				$arr_category_condition[] = [
					$str_condition_table,
					$str_condition_column,
					'=',
					intval($id),
					'PDO::PARAM_INT',
					$logic
				];
			}

		} else {

			if (!in_array($int_target_id, $arr_allowed_ids, true)) {
				respond_error('Invalid scope target id', 400);
			}

			$arr_category_condition[] = [
				$str_condition_table,
				$str_condition_column,
				'=',
				intval($int_target_id),
				'PDO::PARAM_INT',
				''
			];
		}

		// ------------------------------------------------------------
		// Search (empty search_word must return category list)
		// ------------------------------------------------------------
		$arr_matched = [];

		if ($search_word === '') {

			$arr_strSQL_where = [
				[
					$arr_category_condition,
					''
				]
			];

			$arr_matched = fetch_arr_masta_japanese_root_by_search_conditions($arr_strSQL_where, $int_selected_language);

		} else {

			$keyword = mb_convert_kana($search_word, 'sKVc');
			$arr_keyword = preg_split('/[\s]+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);

			if ($int_matching_type === $int_matching_type_perfect_matching) {

				$arr_strSQL_where = [
					[
						[
							[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn], '=', $search_word, 'PDO::PARAM_STR', '']
						],
						'And'
					],
					[
						$arr_category_condition,
						''
					]
				];
				$arr_japanese = fetch_arr_masta_japanese_root_by_search_conditions($arr_strSQL_where, $int_selected_language);

				$arr_strSQL_where = [
					[
						[
							[$t_masta_japanese_root, $str_column_root_kana, '=', $search_word, 'PDO::PARAM_STR', '']
						],
						'And'
					],
					[
						$arr_category_condition,
						''
					]
				];
				$arr_kana = fetch_arr_masta_japanese_root_by_search_conditions($arr_strSQL_where, $int_selected_language);

				$arr_matched = array_merge($arr_japanese, $arr_kana);

			} else {

				if (empty($arr_keyword)) {
					respond_success([]);
				}

				$lastIndex_keywords = count($arr_keyword) - 1;
				if ($lastIndex_keywords < 0) {
					respond_success([]);
				}

				$arr_search_condition = [];
				foreach ($arr_keyword as $i => $loop_keyword) {

					$logic = ($i === $lastIndex_keywords) ? '' : 'And';

					$arr_search_condition[] = [
						'BINARY ' . $t_masta_japanese_root,
						'search_criteria',
						'like',
						'%' . $loop_keyword . '%',
						'PDO::PARAM_STR',
						$logic
					];
				}

				$arr_strSQL_where = [
					[
						$arr_search_condition,
						'And'
					],
					[
						$arr_category_condition,
						''
					]
				];

				$arr_matched = fetch_arr_masta_japanese_root_by_search_conditions($arr_strSQL_where, $int_selected_language);
			}
		}

		// Knowledge filter (kept as design)
		$arr_already_learned_list = $_SESSION['arr_already_learned_list'] ?? [];
		if (!is_array($arr_already_learned_list)) {
			$arr_already_learned_list = [];
		}

		if (
			($int_learningScope === intval($int_learning_scope_already_learned)) &&
			!empty($arr_already_learned_list)
		) {
			$arr_matched = array_filter(
				$arr_matched,
				function ($item) use ($arr_already_learned_list) {
					return in_array(
						intval($item['japaneseId']),
						$arr_already_learned_list,
						true
					);
				}
			);
		}

		if (!empty($arr_matched)) {
			usort($arr_matched, 'customSort');
		}

		respond_success($arr_matched);

	} catch (Throwable $e) {
		respond_exception($e);
	}
