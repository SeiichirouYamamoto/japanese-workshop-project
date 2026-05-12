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

        if (!isset($input['search_word'])) { respond_error('Value not found: search_word', 400); }
        if (!isset($input['int_order_style'])) { respond_error('Value not found: int_order_style', 400); }
        if (!isset($input['int_matching_type'])) { respond_error('Value not found: int_matching_type', 400); }
        if (!isset($input['int_learningScope'])) { respond_error('Value not found: int_learningScope', 400); }
        if (!isset($input['int_masta_japanese_classification_id'])) { respond_error('Value not found: int_masta_japanese_classification_id', 400); }
        if (!isset($input['int_mastery_level'])) { respond_error('Value not found: int_mastery_level', 400); }
        if (!isset($input['isIncludesGrammarAsNumber'])) { respond_error('Value not found: isIncludesGrammarAsNumber', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $search_word = escape_html((string)($input['search_word'] ?? ''));
        $int_order_style = intval($input['int_order_style']);
        $int_matching_type = intval($input['int_matching_type']);
        $int_learningScope = intval($input['int_learningScope']);
        $int_masta_japanese_classification_id_id = intval($input['int_masta_japanese_classification_id']);
        $int_mastery_level = intval($input['int_mastery_level']);
        $isIncludesGrammarAsNumber = intval($input['isIncludesGrammarAsNumber']);
        $isIncludesGrammarAsNumber = ($isIncludesGrammarAsNumber === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $arr_already_learned_list = $_SESSION['arr_already_learned_list'] ?? [];

        $arr_search_condition_original = [];

        if ($int_masta_japanese_classification_id_id !== SELECT_ALL) {
            $arr_search_condition_original[] = [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', 'And'];
            $arr_search_condition_original[] = [$t_masta_japanese_sub_classification, 'classification_id', '=', $int_masta_japanese_classification_id_id, 'PDO::PARAM_INT', 'And'];
        }

        if ($int_mastery_level !== $int_mastery_level_select_all) {
            $arr_search_condition_original[] = [$t_masta_japanese_root, 'jws_level', '=', $int_mastery_level, 'PDO::PARAM_INT', 'And'];
        }

        $arr_japanese_labels = [];

        if ($search_word === '') {

            $arr_search_condition = $arr_search_condition_original;

            if (!empty($arr_search_condition)) {
                $lastKey = array_key_last($arr_search_condition);
                $arr_search_condition[$lastKey][count($arr_search_condition[$lastKey]) - 1] = '';
            }

            $arr_japanese_labels = get_arr_japanese_from_labels_with_content(
                $arr_search_condition,
                $int_selected_language
            );

        } else {

            $keyword = mb_convert_kana($search_word, 's');
            $arr_keyword = preg_split('/[\s]+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);

            if (empty($arr_keyword)) {
                respond_success([]);
            }

            if ($int_matching_type === $int_matching_type_perfect_matching) {

                $str_search_condition = $arr_keyword[INDEX_FIRST];

                if ($isIncludesGrammarAsNumber === FLAG_TRUE) {
                    $arr_search_condition = array_merge(
                        $arr_search_condition_original,
                        [
                            ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '=', $str_search_condition, 'PDO::PARAM_STR', '']
                        ]
                    );
                } else {
                    $arr_search_condition = array_merge(
                        $arr_search_condition_original,
                        [
                            ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '=', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                            [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', '']
                        ]
                    );
                }

                $arr_japanese_labels = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

            } else {

                if (count($arr_keyword) === 1) {

                    $str_search_condition = $arr_keyword[INDEX_FIRST];

                    $arr_japanese_labels_perfect_matching = [];
                    $arr_japanese_labels_prefix_matching = [];
                    $arr_japanese_labels_partial_matching = [];
                    $arr_japanese_labels_search_criteria = [];

                    if ($isIncludesGrammarAsNumber === FLAG_TRUE) {

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '=', $str_search_condition, 'PDO::PARAM_STR', '']
                            ]
                        );
                        $arr_japanese_labels_perfect_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '<>', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'like', $str_search_condition . '%', 'PDO::PARAM_STR', '']
                            ]
                        );
                        $arr_japanese_labels_prefix_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '<>', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'not like', $str_search_condition . '%', 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'like', '%' . $str_search_condition . '%', 'PDO::PARAM_STR', '']
                            ]
                        );
                        $arr_japanese_labels_partial_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_root, 'search_criteria', 'like', '%' . $str_search_condition . '%', 'PDO::PARAM_STR', '']
                            ]
                        );
                        $arr_japanese_labels_search_criteria = get_arr_japanese_from_search_criteria_with_labels($arr_search_condition, $int_selected_language);

                    } else {

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '=', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                                [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', '']
                            ]
                        );
                        $arr_japanese_labels_perfect_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '<>', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'like', $str_search_condition . '%', 'PDO::PARAM_STR', 'And'],
                                [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', '']
                            ]
                        );
                        $arr_japanese_labels_prefix_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_search_condition = array_merge(
                            $arr_search_condition_original,
                            [
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, '<>', $str_search_condition, 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'not like', $str_search_condition . '%', 'PDO::PARAM_STR', 'And'],
                                ['BINARY '.$t_masta_japanese_label, $str_column_label_japanese, 'like', '%' . $str_search_condition . '%', 'PDO::PARAM_STR', 'And'],
                                [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', '']
                            ]
                        );
                        $arr_japanese_labels_partial_matching = get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language);

                        $arr_japanese_labels_search_criteria = [];
                    }

                    $arr_japanese_labels =
                        $arr_japanese_labels_perfect_matching
                        + $arr_japanese_labels_prefix_matching
                        + $arr_japanese_labels_partial_matching
                        + $arr_japanese_labels_search_criteria;

                } else {

                    $arr_search_condition = $arr_search_condition_original;

                    if ($isIncludesGrammarAsNumber !== FLAG_TRUE) {
                        $arr_search_condition[] = [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_word, 'PDO::PARAM_INT', 'And'];
                    }

                    foreach ($arr_keyword as $index => $loop_keyword) {
                        $operator = ($index === array_key_last($arr_keyword)) ? '' : 'And';
                        $arr_search_condition[] = ['BINARY '.$t_masta_japanese_root, 'search_criteria', 'like', '%' . $loop_keyword . '%', 'PDO::PARAM_STR', $operator];
                    }

                    $arr_japanese_labels = get_arr_japanese_from_search_criteria_with_labels(
                        $arr_search_condition,
                        $int_selected_language
                    );
                }
            }
        }

        if ($int_learningScope === $int_learning_scope_already_learned && !empty($arr_already_learned_list)) {
            $arr_japanese_labels = array_intersect_key($arr_japanese_labels, array_flip($arr_already_learned_list));
            $arr_japanese_labels = array_values($arr_japanese_labels);
            usort($arr_japanese_labels, 'customSortKana');
        } else {
            $arr_japanese_labels = array_values($arr_japanese_labels);
        }

        if ($int_masta_japanese_classification_id_id === $int_masta_japanese_classification_id_number) {
            usort($arr_japanese_labels, 'customSortLabels');
        }

        if ($int_order_style === $int_order_style_random) {
            shuffle($arr_japanese_labels);
        }

        respond_success($arr_japanese_labels);

    } catch (Throwable $e) {
        respond_exception($e, 'search_word_unhandled');
    }

