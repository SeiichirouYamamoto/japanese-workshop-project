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
        if (!isset($input['is_previous_as_number'])) { respond_error('Value not found: is_previous_as_number', 400); }
        if (!isset($input['send_sentence_unique_code'])) { respond_error('Value not found: send_sentence_unique_code', 400); }
        if (!isset($input['int_layer_id'])) { respond_error('Value not found: int_layer_id', 400); }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $is_previous_as_number = intval($input['is_previous_as_number'] ?? FLAG_FALSE);
        $is_previous_as_number = ($is_previous_as_number === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $unique_code = trim((string)($input['send_sentence_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_sentence_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        $int_layer_id = intval($input['int_layer_id'] ?? $int_id_default);
        if ($int_layer_id <= 0) {
            respond_error('Invalid value: int_layer_id', 400);
        }

        $t_registered_sentence_id = fetch_registered_sentence_id_from_unique_code($unique_code, $int_selected_language);
        $t_registered_sentence_id = intval($t_registered_sentence_id);
        if ($t_registered_sentence_id <= 0) {
            respond_success([]);
        }

        $arr_strSQL_select = [
            [$t_layers,'id'],
            [$t_layers,'sort']
        ];

        $strSQL_from = ' FROM ' . $t_layers;

        $arr_strSQL_where = [
            [
                [
                    [$t_layers,'registered_sentence_id','=',$t_registered_sentence_id,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_layers,'sort','ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $targets) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        if (empty($targets)) {
            respond_success([]);
        }

        resort_records($is_previous_as_number, $targets, $int_layer_id, $t_layers);

		respond_success(['success' => true]);

    } catch (Throwable $e) {
        respond_exception($e, 'layers_resort_unhandled');
    }

