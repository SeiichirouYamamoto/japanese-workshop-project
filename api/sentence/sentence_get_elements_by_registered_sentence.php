<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		// $user_id = jws_require_single_session();

		// if (!is_admin_level($user_level)) {
        //     respond_error('Forbidden', 403);
        // }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['searchCriteria'])) { respond_error('Value not found: searchCriteria', 400); }
        if (!isset($input['searchById'])) { respond_error('Value not found: searchById', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $searchById = intval($input['searchById'] ?? FLAG_FALSE);
        $searchById = ($searchById === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        if ($searchById === FLAG_TRUE) {

            $registered_sentence_id = intval($input['searchCriteria']);
            if ($registered_sentence_id <= 0) {
                respond_error('Invalid value: searchCriteria (registered_sentence_id)', 400);
            }

        } else {

            $searchCriteria = trim((string)($input['searchCriteria'] ?? ''));
            if ($searchCriteria === '') {
                respond_error('Invalid value: searchCriteria', 400);
            }
            $searchCriteria = escape_html($searchCriteria);

            $arr_strSQL_select = [
                [$t_registered_sentences,'id']
            ];

            $strSQL_from = ' FROM ' . $t_registered_sentences;

            $arr_strSQL_where = [
                [
                    [
                        ['BINARY ' . $t_registered_sentences,'unique_code','=',$searchCriteria,'PDO::PARAM_STR','']
                    ],
                    ''
                ]
            ];

            $arr_strSQL_order = [];

            $strSQL_option = '';

            list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all(
                $arr_strSQL_select,
                $strSQL_from,
                $arr_strSQL_where,
                $arr_strSQL_order,
                $strSQL_option
            );
            handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

            if (empty($arr_registered_sentence)) {
                respond_success([]);
            }

            $registered_sentence_id = intval($arr_registered_sentence[INDEX_FIRST]['id']);
            if ($registered_sentence_id <= 0) {
                respond_success([]);
            }
        }

        $arr_strSQL_select = [
            [$t_registered_sentence_elements, 'id'],
            [$t_registered_sentence_elements, 'registered_sentence_id'],
            [$t_registered_sentence_elements, 'id_name as idName'],
            [$t_registered_sentence_elements, 'unique_key as uniqueKey'],
            [$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
            [$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
            [$t_registered_sentence_elements, 'sub_classification_id as ' . $str_snake_to_camel_sub_classification_id],
            [$t_registered_sentence_elements, 'form_id as ' . $str_snake_to_camel_form_id],
            [$t_registered_sentence_elements, 'label_id as ' . $str_snake_to_camel_label_id],
            [$t_registered_sentence_elements, 'voice_id as ' . $str_snake_to_camel_voice_id],
            [$t_registered_sentence_elements, 'bounds_top as boundsTop'],
            [$t_registered_sentence_elements, 'bounds_left as boundsLeft'],
            [$t_registered_sentence_elements, 'link_id as ' . $str_snake_to_camel_link_id],
            [$t_registered_sentence_elements, 'link_type as linkType'],
            [$t_registered_sentence_elements, 'japanese'],
            [$t_registered_sentence_elements, 'kana'],
            [$t_registered_sentence_elements, 'sub_classification as subClassification'],
            [$t_registered_sentence_elements, 'phrase_clause_type as phraseClauseType'],
            [$t_registered_sentence_elements, 'phrase_clause_id as phraseClauseId'],
            [$t_registered_sentence_elements, 'japanese_phrase_clause as japanesePhraseClause'],
            [$t_registered_sentence_elements, 'kana_phrase_clause as kanaPhraseClause'],
            [$t_registered_sentence_elements, 'sort']
        ];

        $strSQL_from = ' FROM ' . $t_registered_sentence_elements;

        $arr_strSQL_where = [
            [
                [
                    [$t_registered_sentence_elements,'registered_sentence_id','=',$registered_sentence_id,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_registered_sentence_elements,'sort','ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        foreach ($arr_registered_sentence_elements as $key => $loop_registered_sentence_elements) {
            $t_masta_japanese_sub_classification_id = intval($loop_registered_sentence_elements[$str_snake_to_camel_sub_classification_id]);
            $t_masta_form_root_id = intval($loop_registered_sentence_elements[$str_snake_to_camel_form_id]);
            $int_voice_id = intval($loop_registered_sentence_elements[$str_snake_to_camel_voice_id]);

            $str_japanese_sub_classification = fetch_str_sub_classification_name_by_id($t_masta_japanese_sub_classification_id, $int_selected_language);
            $str_japanese_form = fetch_str_form_name_by_form_root_id($t_masta_form_root_id, $int_selected_language);
            $str_japanese_voice = get_str_voice_name_by_id($int_voice_id, $int_selected_language);

            $arr_registered_sentence_elements[$key][$str_snake_to_camel_sub_classification] = $str_japanese_sub_classification;
            $arr_registered_sentence_elements[$key][$str_snake_to_camel_form] = $str_japanese_form;
            $arr_registered_sentence_elements[$key][$str_snake_to_camel_voice] = $str_japanese_voice;
        }

        respond_success($arr_registered_sentence_elements);

    } catch (Throwable $e) {
        respond_exception($e, 'registered_sentence_get_elements_unhandled');
    }

