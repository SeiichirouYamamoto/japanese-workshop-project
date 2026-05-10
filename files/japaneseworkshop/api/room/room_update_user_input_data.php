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
        if (!isset($input['room_unique_code'])) respond_error('Value not found: room_unique_code', 400);
        if (!isset($input['arr_user_input_update'])) respond_error('Value not found: arr_user_input_update', 400);

        $int_selected_language = intval($input['int_selected_language']);

        $room_unique_code = trim((string)($input['room_unique_code'] ?? ''));
        if ($room_unique_code === '') {
            respond_error('Invalid value: room_unique_code', 400);
        }
        $room_unique_code = escape_html($room_unique_code);

        $room_id = intval(fetch_room_id_from_unique_code($room_unique_code, $int_selected_language));
        if ($room_id <= 0) {
            respond_error('Room not found', 404);
        }

        $arr_user_input_update = $input['arr_user_input_update'];
        if (!is_array($arr_user_input_update) || empty($arr_user_input_update)) {
            respond_error('Invalid value: arr_user_input_update', 400);
        }

        // ★ここだけ注意：テーブルのPK列名が id でない場合は変更してください
        // 例）user_input_data_id の場合: $pk_column = 'user_input_data_id';
        $pk_column = 'id';

        $updated_ids = [];

        foreach ($arr_user_input_update as $row) {

            if (!is_array($row)) {
                continue;
            }

            $userInputDataId = intval($row['userInputDataId'] ?? 0);
            if ($userInputDataId <= 0) {
                continue;
            }

            // JSは key が value
            $value_raw = (string)($row['value'] ?? '');
            $value_raw = trim($value_raw);

            // 空更新を禁止したいなら、ここで弾けます
            // if ($value_raw === '') continue;

            $value = escape_html($value_raw);

            $update_table = $t_room_user_input_data;

            $arr_updateSQL = [
                ['input_data', ':update_input_data', $value, 'PDO::PARAM_STR'],
            ];

            $arr_whereSQL = [
                [$pk_column, ':where_id', $userInputDataId, 'PDO::PARAM_INT', ' And '],
                ['room_id', ':where_room_id', $room_id, 'PDO::PARAM_INT', ''],
            ];

            list($pdo_has_error, $update_has_error, $e) = execute_update_data(
                $update_table,
                $arr_updateSQL,
                $arr_whereSQL
            );

            handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

            $updated_ids[] = $userInputDataId;
        }

        if (empty($updated_ids)) {
            respond_error('No rows updated', 400);
        }

        respond_success([
            'updated_ids' => $updated_ids
        ]);

    } catch (Throwable $e) {
        respond_exception($e);
    }

