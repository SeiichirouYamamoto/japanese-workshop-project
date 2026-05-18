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

        $t_masta_japanese_root_id = intval($input['send_japanese_id'] ?? $int_id_default);
        $t_masta_japanese_sub_classification_id = intval($input['send_sub_classification_id'] ?? $int_id_default);
        $t_masta_form_root_id = intval($input['send_form_id'] ?? $int_id_default);
        $int_voice_id = intval($input['send_voice_id'] ?? $int_id_default);
        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);

        $arr_strSQL_select = [
            [$t_masta_japanese_section, 'id'],
            [$t_masta_japanese_section, $arr_columns_masta_japanese_section[$int_selected_language]]
        ];

        $strSQL_from = ' FROM ' . $t_masta_japanese_section;

        $arr_strSQL_where = [
            [
                [
                    [$t_masta_japanese_section, 'root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_masta_japanese_section, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_section) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        if (empty($arr_masta_japanese_section)) {
            respond_success($int_default_value_proxy_0);
        }

        $arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($t_masta_japanese_root_id, $int_selected_language);

        $result = [
            $str_snake_to_camel_japanese_id => $arr_masta_japanese_root['id'],
            $str_snake_to_camel_grammar_unique_code => $arr_masta_japanese_root[$str_snake_to_camel_grammar_unique_code],
            $str_snake_to_camel_japanese => $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]],
            $str_snake_to_camel_kana => $arr_masta_japanese_root[$str_column_root_kana],
            $str_snake_to_camel_category_id => $arr_masta_japanese_root['category_id']
        ];

        respond_success($result);

    } catch (Throwable $e) {
        respond_exception($e, 'get_masta_japanese_root_unhandled');
    }

