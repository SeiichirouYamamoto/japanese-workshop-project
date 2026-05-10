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

        if (!isset($input['sessionEndTime'])) { respond_error('Value not found: sessionEndTime', 400); }
        if (!isset($input['send_grammar_unique_code'])) { respond_error('Value not found: send_grammar_unique_code', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['recorded_textarea_values']) || !is_array($input['recorded_textarea_values'])) {
            respond_error('Value not found: recorded_textarea_values', 400);
        }

        $current_user = wp_get_current_user();
        $current_user_id = intval($current_user->ID);

        if ($current_user_id <= 0) {
            respond_error('Login required', 401);
        }

        $sessionEndTime = escape_html((string)($input['sessionEndTime'] ?? ''));

        $unique_code = trim((string)($input['send_grammar_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_grammar_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($unique_code, $int_selected_language);
        $t_masta_japanese_root_id = intval($t_masta_japanese_root_id);

        $arr_textarea_values = array_map('escape_html', $input['recorded_textarea_values'] ?? []);

        foreach ($arr_textarea_values as $loop_textarea_value) {

            if ($loop_textarea_value === '') {
                continue;
            }

            $arr_insertSQL = [
                ['user_id', '?', $current_user_id, 'PDO::PARAM_INT'],
                ['session_end_time', '?', $sessionEndTime, 'PDO::PARAM_STR'],
                ['textarea_value', '?', $loop_textarea_value, 'PDO::PARAM_STR'],
                ['masta_japanese_root_id', '?', $t_masta_japanese_root_id, 'PDO::PARAM_INT']
            ];

            list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data(
                $t_usage_textarea_values,
                $arr_insertSQL
            );

            handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);
        }

        respond_success([]);

    } catch (Throwable $e) {
        respond_exception($e, 'usage_textarea_values_insert_unhandled');
    }

