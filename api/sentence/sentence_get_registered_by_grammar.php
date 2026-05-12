<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_member_level($user_level)) {
            respond_error('Forbidden', 403);
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['send_grammar_unique_code'])) { respond_error('Value not found: send_grammar_unique_code', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $unique_code = trim((string)($input['send_grammar_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_grammar_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        // 必要ならここで切替できます（現状は false 固定）
        $isRequestForeignLanguage = false;

        $t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($unique_code, $int_selected_language);

        // 未定義id（0）なら空配列を返して終了（元仕様維持）
        if (intval($t_masta_japanese_root_id) === 0) {
            respond_success([]);
        }

        $arr_strSQL_select = [
            [$t_registered_sentences,'id as registered_sentence_id'],
            [$t_registered_sentences,'unique_code as ' . $str_snake_to_camel_unique_code],
            [$t_registered_sentences,'masta_japanese_root_id as japanese_id'],
            [$t_registered_sentences,'sentence'],
            [$t_registered_sentences,'is_published as isPublished'],
            [$t_registered_sentences,'sort']
        ];

        $strSQL_from = ' FROM ' . $t_registered_sentences;

        $arr_strSQL_where = [
            [
                [
                    [$t_registered_sentences,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_registered_sentences,'sort','ASC']
        ];

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

        if ($isRequestForeignLanguage) {
            foreach ($arr_registered_sentence as $key => $loop_registered_sentence) {
                $t_registered_sentence_id = intval($loop_registered_sentence['registered_sentence_id']);
                $str_one_answer = fetch_str_registered_sentence_answer_by_id($t_registered_sentence_id, $int_selected_language);

                if (!empty($str_one_answer)) {
                    $arr_registered_sentence[$key]['sentence'] = $str_one_answer;
                }
            }
        }

        respond_success($arr_registered_sentence);

    } catch (Throwable $e) {
        respond_exception($e, 'grammar_get_registered_sentences_unhandled');
    }

