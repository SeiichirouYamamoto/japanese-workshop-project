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

        if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);
        if (!isset($input['grammar_unique_code'])) respond_error('Value not found: grammar_unique_code', 400);
        if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);
        if (!isset($input['str_textareaValue'])) respond_error('Value not found: str_textareaValue', 400);

        $int_selected_language = intval($input['int_selected_language']);


        $grammar_unique_code = trim((string)($input['grammar_unique_code'] ?? ''));
        if ($grammar_unique_code === '') {
            respond_error('Invalid value: grammar_unique_code', 400);
        }
        $grammar_unique_code = escape_html($grammar_unique_code);

        $t_masta_japanese_root_id = intval(fetch_masta_japanese_root_id_from_unique_code($grammar_unique_code, $int_selected_language));
        if ($t_masta_japanese_root_id <= 0) {
            respond_error('Grammar not found', 404);
        }

		
        $room_unique_code = trim((string)($input['room_unique_code'] ?? ''));
        if ($room_unique_code === '') {
            respond_error('Invalid value: room_unique_code', 400);
        }
        $room_unique_code = escape_html($room_unique_code);
		
        $room_id = intval(fetch_room_id_from_unique_code($room_unique_code, $int_selected_language));
        if ($room_id <= 0) {
            respond_error('Room not found', 404);
        }


        $str_textareaValue_raw = trim((string)($input['str_textareaValue'] ?? ''));
        if ($str_textareaValue_raw === '') {
            respond_error('Invalid value: str_textareaValue', 400);
        }
        $str_textareaValue = escape_html($str_textareaValue_raw);

		
        $arr_strSQL_select = [
            ['', 'MAX(sort) as max_sort']
        ];
        $strSQL_from = ' FROM ' . $t_room_user_input_data;
        $arr_strSQL_where = [
            [
                [
                    [$t_room_user_input_data, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
                    [$t_room_user_input_data, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];
        $arr_strSQL_order = [];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_check_max) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        $max_sort = null;
        if (!empty($arr_check_max) && array_key_exists('max_sort', $arr_check_max[INDEX_FIRST])) {
            $max_sort = $arr_check_max[INDEX_FIRST]['max_sort'];
        }

        $add_sort = ($max_sort === null) ? SORT_FIRST : intval($max_sort) + 1;

        $arr_insertSQL = [
            ['room_id', '?', $room_id, 'PDO::PARAM_INT'],
            ['masta_japanese_root_id', '?', $t_masta_japanese_root_id, 'PDO::PARAM_INT'],
            ['input_data', '?', $str_textareaValue, 'PDO::PARAM_STR'],
            ['sort', '?', $add_sort, 'PDO::PARAM_INT']
        ];

        list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_room_user_input_data, $arr_insertSQL);
        handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

        if (empty($last_insert_id)) {
            respond_error('Failed to insert', 500);
        }

        respond_success(['input_data' => $str_textareaValue]);

    } catch (Throwable $e) {
        respond_exception($e);
    }

