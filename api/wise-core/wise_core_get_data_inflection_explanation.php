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

        if (!isset($input['send_japanese_id'])) { respond_error('Value not found: send_japanese_id', 400); }
        if (!isset($input['send_sub_classification_id'])) { respond_error('Value not found: send_sub_classification_id', 400); }
        if (!isset($input['send_form_id'])) { respond_error('Value not found: send_form_id', 400); }
        if (!isset($input['send_voice_id'])) { respond_error('Value not found: send_voice_id', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $arr_inflection_explanation = [];

        $t_masta_japanese_root_id = intval($input['send_japanese_id'] ?? $int_id_default);
        $t_masta_japanese_sub_classification_id = intval($input['send_sub_classification_id'] ?? $int_id_default);
        $t_masta_form_root_id = intval($input['send_form_id'] ?? $int_id_default);
        $int_voice_id = intval($input['send_voice_id'] ?? $int_id_default);
        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);

        $arr_strSQL_select = [
            [$t_masta_japanese_sub_classification, 'classification_id']
        ];

        $strSQL_from = ' FROM ' . $t_masta_japanese_sub_classification;

        $arr_strSQL_where = [
            [
                [
                    [$t_masta_japanese_sub_classification, 'id', '=', $t_masta_japanese_sub_classification_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [];

    	$strSQL_option = '';
        list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_sub_classification) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        if (empty($arr_masta_japanese_sub_classification)) {
            respond_success([]);
        }

        $classification_id = intval($arr_masta_japanese_sub_classification[INDEX_FIRST]['classification_id'] ?? 0);
        if ($classification_id <= 0) {
            respond_success([]);
        }

        $arr_search_target = [
            $str_snake_to_camel_voice => $int_voice_id,
            $str_snake_to_camel_form => $t_masta_form_root_id
        ];

        foreach ($arr_search_target as $key => $loop_search_target) {

            $arr_strSQL_select = [
                [$t_masta_japanese_root, 'id as ' . $str_snake_to_camel_japanese_id],
                [$t_masta_japanese_root, 'unique_code as ' . $str_snake_to_camel_grammar_unique_code],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn]],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as ' . $str_snake_to_camel_japanese],
                [$t_masta_japanese_root, $str_column_root_kana . ' as ' . $str_snake_to_camel_kana],
                [$t_masta_japanese_sub_category, 'category_id as ' . $str_snake_to_camel_category_id]
            ];

            $strSQL_from = " FROM
                            (
                                $t_masta_inflection
                                INNER JOIN $t_masta_japanese_root
                                ON
                                $t_masta_inflection.masta_japanese_root_id = $t_masta_japanese_root.id
                            )
                            INNER JOIN $t_masta_japanese_sub_category
                            ON
                            $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
                            ";

            $arr_strSQL_where = [
                [
                    [
                        [$t_masta_inflection, 'masta_form_root_id', '=', $loop_search_target, 'PDO::PARAM_INT', 'And'],
                        [$t_masta_inflection, 'masta_japanese_classification_id', '=', $classification_id, 'PDO::PARAM_INT', '']
                    ],
                    ''
                ]
            ];

            $arr_strSQL_order = [
                [$t_masta_inflection, 'sort', 'ASC']
            ];

            $strSQL_option = '';

            list($pdo_has_error, $select_has_error, $e, $arr_masta_inflection) = execute_select_and_fetch_all(
                $arr_strSQL_select,
                $strSQL_from,
                $arr_strSQL_where,
                $arr_strSQL_order,
                $strSQL_option
            );
            handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

            if (empty($arr_masta_inflection)) {
                $arr_inflection_explanation[] = [$key => 0];
            } else {
                $arr_inflection_explanation[] = [$key => $arr_masta_inflection[INDEX_FIRST]];
            }
        }

        respond_success($arr_inflection_explanation);

    } catch (Throwable $e) {
        respond_exception($e, 'inflection_explanation_unhandled');
    }

