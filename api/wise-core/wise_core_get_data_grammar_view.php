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

        if (!isset($input['is_unique_code'])) {
            respond_error('Value not found: is_unique_code', 422);
        }
        if (!isset($input['send_value'])) {
            respond_error('Value not found: send_value', 422);
        }
        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 422);
        }

        $int_selected_language = intval($input['int_selected_language']);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 422);
        }

        $is_unique_code = intval($input['is_unique_code']);
        $send_value = escape_html((string)$input['send_value']);

        if ($is_unique_code === FLAG_TRUE) {
            $target_id = fetch_masta_japanese_root_id_from_unique_code($send_value, $int_selected_language);
        } else {
            $target_id = intval($send_value);
        }

        if ($target_id === 0) {
            respond_success([]);
        }

        $arr_masta_japanese_root_ids = [$target_id];
        $arr_contents = [];

        $arr_grammar_comparison_sets =
            get_arr_grammar_relation_sets_for_section(
                $t_grammar_comparison_sets,
                $t_grammar_comparison_items,
                $target_id,
                $int_selected_language
            );

        foreach ($arr_grammar_comparison_sets as $loop_set) {
            if (!empty($loop_set['alreadyLearned'])) {
                $arr_masta_japanese_root_ids[] = intval($loop_set['masta_japanese_root_id']);
            }
        }

        $t_masta_japanese_attribute_1 = $t_masta_japanese_attribute . '_1';
        $t_masta_japanese_attribute_2 = $t_masta_japanese_attribute . '_2';
        $t_masta_japanese_attribute_3 = $t_masta_japanese_attribute . '_3';

        foreach ($arr_masta_japanese_root_ids as $t_masta_japanese_root_id) {

            $arr_strSQL_select = [
                [$t_masta_japanese_root, 'id as rootId'],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn] . ' as rootDefault'],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as rootSelectedLanguage'],
                [$t_masta_japanese_section, 'id as sectionId'],
                [$t_masta_japanese_section, $arr_columns_masta_japanese_section[$int_used_language_jpn] . ' as sectionDefault'],
                [$t_masta_japanese_section, $arr_columns_masta_japanese_section[$int_selected_language] . ' as sectionSelectedLanguage'],
                [$t_masta_japanese_main, 'id as mainId'],
                [$t_masta_japanese_main, $arr_columns_masta_japanese_main[$int_used_language_jpn] . ' as mainDefault'],
                [$t_masta_japanese_main, $arr_columns_masta_japanese_main[$int_selected_language] . ' as mainSelectedLanguage'],
                [$t_masta_japanese_description, 'id as descriptionId'],
                [$t_masta_japanese_description, $arr_columns_masta_japanese_description[$int_used_language_jpn] . ' as descriptionDefault'],
                [$t_masta_japanese_description, $arr_columns_masta_japanese_description[$int_selected_language] . ' as descriptionSelectedLanguage'],
                [$t_masta_japanese_attribute_1, $arr_columns_masta_japanese_attribute[$int_selected_language] . ' as sectionAttribute'],
                [$t_masta_japanese_attribute_2, $arr_columns_masta_japanese_attribute[$int_selected_language] . ' as mainAttribute'],
                [$t_masta_japanese_attribute_3, $arr_columns_masta_japanese_attribute[$int_selected_language] . ' as descriptionAttribute']
            ];

            $strSQL_from = "
                FROM
                (
                    (
                        (
                            $t_masta_japanese_root
                            LEFT JOIN $t_masta_japanese_section
                                ON $t_masta_japanese_root.id = $t_masta_japanese_section.root_id
                        )
                        LEFT JOIN $t_masta_japanese_main
                            ON $t_masta_japanese_section.id = $t_masta_japanese_main.masta_japanese_section_id
                    )
                    LEFT JOIN $t_masta_japanese_description
                        ON $t_masta_japanese_main.id = $t_masta_japanese_description.masta_japanese_main_id
                )
                LEFT JOIN $t_masta_japanese_attribute AS $t_masta_japanese_attribute_1
                    ON $t_masta_japanese_section.attribute_id = $t_masta_japanese_attribute_1.id
                LEFT JOIN $t_masta_japanese_attribute AS $t_masta_japanese_attribute_2
                    ON $t_masta_japanese_main.attribute_id = $t_masta_japanese_attribute_2.id
                LEFT JOIN $t_masta_japanese_attribute AS $t_masta_japanese_attribute_3
                    ON $t_masta_japanese_description.attribute_id = $t_masta_japanese_attribute_3.id
            ";

            $arr_strSQL_where = [
                [
                    [
                        [$t_masta_japanese_root, 'id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', 'And'],
                        [$t_masta_japanese_section, 'attribute_id', '<>', $int_masta_japanese_attribute_id_example, 'PDO::PARAM_INT', '']
                    ],
                    ''
                ]
            ];

            $arr_strSQL_order = [
                [$t_masta_japanese_section, 'sort', 'ASC'],
                [$t_masta_japanese_main, 'sort', 'ASC'],
                [$t_masta_japanese_description, 'sort', 'ASC']
            ];

            list($pdo_has_error, $select_has_error, $e, $arr_grammar_view) =
                execute_select_and_fetch_all(
                    $arr_strSQL_select,
                    $strSQL_from,
                    $arr_strSQL_where,
                    $arr_strSQL_order,
                    ''
                );

            handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

            if (empty($arr_grammar_view)) {
                continue;
            }

            $arr_grammar_view = array_map('apply_remove_original_tags', $arr_grammar_view);
            $arr_strSQL_select = [
                [$t_registered_sentences, 'id'],
                [$t_registered_sentences, 'sentence']
            ];
            $strSQL_from = ' FROM ' . $t_registered_sentences;

            $arr_strSQL_where = [
                [$t_registered_sentences, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
            ];
            $arr_strSQL_where = [
                [
                    [
                        [$t_registered_sentences, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
                    ],
                    'And'
                ],
                [
                    $arr_strSQL_where,
                    ''
                ]
            ];

            $arr_strSQL_order = [
                [$t_registered_sentences, 'sort', 'ASC']
            ];
            $strSQL_option = '';

            list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);

            handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

            $i = -1;
            foreach ($arr_registered_sentence as $loop_registered_sentence) {
                $sample_sentence = $loop_registered_sentence['sentence'];
                $int_registered_sentence_id = $loop_registered_sentence['id'];
                $str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);
                $answer = !empty($str_quiz_translation) ? $str_quiz_translation : '';

                $arr_grammar_view[] = [
                    'rootId' => $t_masta_japanese_root_id,
                    'rootDefault' => $arr_grammar_view[INDEX_FIRST]['rootDefault'],
                    'rootSelectedLanguage' => $arr_grammar_view[INDEX_FIRST]['rootSelectedLanguage'],
                    'sectionId' => -1,
                    'sectionDefault' => $arr_sample_sentence_list_tag[$int_used_language_jpn],
                    'sectionSelectedLanguage' => $arr_sample_sentence_list_tag[$int_selected_language],
                    'mainId' => $i,
                    'mainDefault' => $sample_sentence,
                    'mainSelectedLanguage' => $answer,
                    'descriptionId' => '',
                    'descriptionDefault' => '',
                    'descriptionSelectedLanguage' => '',
                    'sectionAttribute' => $arr_sample_sentence_list_tag[$int_selected_language],
                    'mainAttribute' => $str_avoid_null_proxy,
                    'descriptionAttribute' => $str_avoid_null_proxy
                ];
                --$i;
            }

            $arr_contents[] = $arr_grammar_view;
        }

        respond_success($arr_contents);

    } catch (Throwable $e) {
        respond_exception($e, 'grammar_section_view_unhandled');
    }

