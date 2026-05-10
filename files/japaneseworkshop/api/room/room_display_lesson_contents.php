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
		if (!isset($input['str_mode'])) respond_error('Value not found: str_mode', 400);
		if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);

		$int_selected_language = (int)$input['int_selected_language'];
		$str_mode = trim((string)($input['str_mode'] ?? ''));
		$room_unique_code = trim((string)($input['room_unique_code'] ?? ''));

		if ($str_mode === '') respond_error('Invalid value: str_mode', 400);
		if ($room_unique_code === '') respond_error('Invalid value: room_unique_code', 400);

		$room_unique_code = escape_html($room_unique_code);

		$int_start_sort = (int)($input['int_start_sort'] ?? SORT_FIRST);
		$int_end_sort = (int)($input['int_end_sort'] ?? SORT_FIRST);

		if ($int_start_sort < SORT_FIRST) respond_error('Invalid value: int_start_sort', 400);
		if ($int_end_sort < SORT_FIRST) respond_error('Invalid value: int_end_sort', 400);

		$allowed_modes = [
			'lesson_contents_tree',
			'grammar_comparison_tree',
			'grammar_family_tree',
			'grammar_correspondence_tree',
			'grammar_alternative_tree',
			'grammar_usages_tree',
			'grammar_knowledge_tree'
		];

		if (!in_array($str_mode, $allowed_modes, true)) {
			respond_error('Invalid value: str_mode', 400);
		}

		$room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('room not found', 404);
		}

		$arr_bookmarks_data = get_data_bookmarks(
			$search_scope_room_owner_user,
			$room_unique_code,
			$bookmark_filter_active,
			$int_selected_language
		);

		$contents_tree_flags = [
			'doDisplayGrammarOutlineGrammars' => true,
			'doDisplayGrammarOutlineCheckbox' => true,
			'doDisplayGrammarOutlineLabelButtonsExplanation' => true,
			'openOutline' => false
		];

		$str_contents = '';
		$str_header_title = '';
		$str_section_id = '';

		switch ($str_mode) {

			case 'lesson_contents_tree':

				$arr_strSQL_select = [
					[$t_room_lessons, 'id'],
					[$t_room_lessons, 'room_id'],
					[$t_room_lessons, 'teaching_material_lesson_id'],
					[$t_room_lessons, 'title'],
					[$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language]],
					[$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]]
				];

				$strSQL_from = "
					FROM $t_room_lessons
					LEFT JOIN $t_teaching_material_lessons
					ON $t_room_lessons.teaching_material_lesson_id = $t_teaching_material_lessons.id
				";

				$arr_strSQL_where = [
					[
						[
							[$t_room_lessons, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
							[$t_room_lessons, 'sort', $str_sql_where_between, $int_start_sort, $int_end_sort, 'PDO::PARAM_INT', '']
						],
						''
					]
				];

				$arr_strSQL_order = [
					[$t_room_lessons, 'sort', 'ASC']
				];

				$strSQL_option = '';

				list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
				handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

				$str_contents = build_html_lesson_contents_tree($room_id, $contents_tree_flags, $arr_lessons, $arr_bookmarks_data, $int_selected_language);
				$str_header_title = 'Lessons';
				$str_section_id = 'sectionLessonContents';

				break;

			case 'grammar_comparison_tree':

				$tree_title = 'Comparisons';
				$requireAllLearned = true;

				$str_contents = build_html_grammar_tree_common($contents_tree_flags, $arr_bookmarks_data, $int_selected_language, $t_grammar_comparison_sets, $t_grammar_comparison_items, $tree_title, $requireAllLearned);
				$str_header_title = $tree_title;
				$str_section_id = 'sectionGrammarOutlinesGrammar' . $tree_title;

				break;

			case 'grammar_family_tree':

				$tree_title = 'Families';
				$requireAllLearned = false;

				$str_contents = build_html_grammar_tree_common($contents_tree_flags, $arr_bookmarks_data, $int_selected_language, $t_grammar_family_sets, $t_grammar_family_items, $tree_title, $requireAllLearned);
				$str_header_title = $tree_title;
				$str_section_id = 'sectionGrammarOutlinesGrammar' . $tree_title;

				break;

			case 'grammar_correspondence_tree':

				$tree_title = 'Correspondences';
				$requireAllLearned = true;

				$str_contents = build_html_grammar_tree_common($contents_tree_flags, $arr_bookmarks_data, $int_selected_language, $t_grammar_correspondence_sets, $t_grammar_correspondence_items, $tree_title, $requireAllLearned);
				$str_header_title = $tree_title;
				$str_section_id = 'sectionGrammarOutlinesGrammar' . $tree_title;

				break;

			case 'grammar_alternative_tree':

				$tree_title = 'Alternatives';
				$requireAllLearned = true;

				$str_contents = build_html_grammar_tree_common($contents_tree_flags, $arr_bookmarks_data, $int_selected_language, $t_grammar_alternative_sets, $t_grammar_alternative_items, $tree_title, $requireAllLearned);
				$str_header_title = $tree_title;
				$str_section_id = 'sectionGrammarOutlinesGrammar' . $tree_title;

				break;

			case 'grammar_usages_tree':

				$arr_allow_display = (isset($_SESSION['arr_already_learned_list']) && is_array($_SESSION['arr_already_learned_list'])) ? $_SESSION['arr_already_learned_list'] : [];
				$draw_details = true;

				$arr_targets = get_arr_tree_usage_category_ids_by_status($int_selected_language, $int_masta_grammar_usage_status_usage);

				$str_contents = build_html_grammar_usages_tree($contents_tree_flags, $arr_targets, $arr_allow_display, $arr_bookmarks_data, $draw_details, $int_selected_language);
				$str_header_title = 'Usages';
				$str_section_id = 'sectionGrammarUsages';

				break;

			case 'grammar_knowledge_tree':

				$arr_allow_display = (isset($_SESSION['arr_already_learned_list']) && is_array($_SESSION['arr_already_learned_list'])) ? $_SESSION['arr_already_learned_list'] : [];
				$draw_details = true;

				$arr_targets = get_arr_tree_usage_category_ids_by_status($int_selected_language, $int_masta_grammar_usage_status_knowledge);

				$str_contents = build_html_grammar_usages_tree($contents_tree_flags, $arr_targets, $arr_allow_display, $arr_bookmarks_data, $draw_details, $int_selected_language);
				$str_header_title = 'Knowledge';
				$str_section_id = 'sectionGrammarUsages';

				break;
		}

		if ($str_contents !== '') {
			$str_contents = '<h1>' . $str_header_title . '</h1>' . $str_contents;
		}

		$str_room_lesson_contents = 
		'<section id="' . $str_section_id . '" class="sectionStandard" data-lesson-contents-search-grammar-target="true">' . $str_contents . '</section>';

		respond_success(['html' => $str_room_lesson_contents]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

