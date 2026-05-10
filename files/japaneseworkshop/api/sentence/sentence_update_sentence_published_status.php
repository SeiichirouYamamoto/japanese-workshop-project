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

        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['isPublished'])) { respond_error('Value not found: isPublished', 400); }
        if (!isset($input['send_sentence_unique_code'])) { respond_error('Value not found: send_sentence_unique_code', 400); }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $is_published = intval($input['isPublished'] ?? FLAG_FALSE);
        $is_published = ($is_published === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $unique_code = trim((string)($input['send_sentence_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_sentence_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        $int_registered_sentence_id = fetch_registered_sentence_id_from_unique_code($unique_code, $int_selected_language);
        $int_registered_sentence_id = intval($int_registered_sentence_id);

        if ($int_registered_sentence_id <= 0) {
            respond_success([]);
        }

        $update_table = $t_registered_sentences;

        $arr_updateSQL = [
            ['is_published', ':update_is_published', $is_published, 'PDO::PARAM_INT']
        ];

        $arr_whereSQL = [
            ['id', ':where_id', $int_registered_sentence_id, 'PDO::PARAM_INT', '']
        ];

        list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);
        handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

        respond_success([
            'isPublished' => $is_published
        ]);

    } catch (Throwable $e) {
        respond_exception($e, 'registered_sentence_update_publish_unhandled');
    }

