<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

        if (!is_admin_level($user_level)) {
            respond_error('Forbidden', 403);
        }

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
            [$t_layers,'id'],
            // [$t_masta_japanese_root,'unique_code as ' . $str_snake_to_camel_grammar_unique_code],
            [$t_masta_japanese_sub_category,$arr_columns_masta_japanese_sub_category[$int_selected_language] . ' as subCategory'],
            [$t_layers,'layer_name'],
            [$t_layers,'sort']
        ];

        $strSQL_from = " FROM
                        (
                            $t_layers
                            LEFT JOIN $t_masta_japanese_root
                            ON
                            $t_layers.masta_japanese_root_id = $t_masta_japanese_root.id
                        )
                        LEFT JOIN $t_masta_japanese_sub_category
                        ON
                        $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
                        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_layers,'registered_sentence_id','=',$registered_sentence_id,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_layers,'sort','ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        respond_success($arr_layers);

    } catch (Throwable $e) {
        respond_exception($e, 'registered_sentence_get_layers_unhandled');
    }

