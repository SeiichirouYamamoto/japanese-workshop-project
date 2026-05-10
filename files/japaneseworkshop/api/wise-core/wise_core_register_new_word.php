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

        if (!isset($input['str_japanese'])) { respond_error('Value not found: str_japanese', 400); }
        if (!isset($input['str_kana'])) { respond_error('Value not found: str_kana', 400); }
        if (!isset($input['int_sub_classification_id'])) { respond_error('Value not found: int_sub_classification_id', 400); }

        $str_japanese = escape_html((string)($input['str_japanese'] ?? ''));
        $str_kana = escape_html((string)($input['str_kana'] ?? ''));
        $int_sub_classification_id = intval($input['int_sub_classification_id'] ?? $int_id_default);

        if ($int_sub_classification_id === intval($int_Num)) {
            respond_success(['subClassificationId' => $int_sub_classification_id]);
        }

        $arr_insertSQL = [
            ['japanese', '?', $str_japanese, 'PDO::PARAM_STR'],
            ['kana', '?', $str_kana, 'PDO::PARAM_STR'],
            ['sub_classification_id', '?', $int_sub_classification_id, 'PDO::PARAM_INT']
        ];

        list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data(
            $t_get_new_word_create_new_word,
            $arr_insertSQL
        );
        handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

        respond_success(['id' => intval($last_insert_id)]);

    } catch (Throwable $e) {
        respond_exception($e, 'create_new_word_unhandled');
    }

