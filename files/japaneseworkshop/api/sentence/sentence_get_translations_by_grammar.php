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

        if (!isset($input['str_grammarUniqueCode'])) { respond_error('Value not found: str_grammarUniqueCode', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $unique_code = trim((string)($input['str_grammarUniqueCode'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: str_grammarUniqueCode', 400);
        }
        $unique_code = escape_html($unique_code);

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $arr_registered_sentence_foreign_language_answers = [];

        $t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($unique_code, $int_selected_language);
        if (intval($t_masta_japanese_root_id) === 0) {
            respond_success([]);
        }

        $arr_strSQL_select = [
            [$t_registered_sentence_translations,'answer']
        ];

        $strSQL_from = " FROM
                        $t_registered_sentences
                        INNER JOIN $t_registered_sentence_translations
                        ON
                        $t_registered_sentences.id = $t_registered_sentence_translations.registered_sentence_id
                        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_registered_sentences,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','And'],
                    [$t_registered_sentence_translations,'language_id','=',$int_selected_language,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_registered_sentences,'sort','ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_foreign_language_answers) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        $arr_registered_sentence_foreign_language_answers = array_column($arr_registered_sentence_foreign_language_answers, 'answer');

        if (empty($arr_registered_sentence_foreign_language_answers)) {
            respond_success([]);
        }

        respond_success($arr_registered_sentence_foreign_language_answers);

    } catch (Throwable $e) {
        respond_exception($e, 'registered_sentence_get_foreign_language_answers_unhandled');
    }

