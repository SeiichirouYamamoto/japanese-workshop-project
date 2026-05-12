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
        if (!isset($input['isPreviousAsNumber'])) respond_error('Value not found: isPreviousAsNumber', 400);
        if (!isset($input['currentUrl'])) respond_error('Value not found: currentUrl', 400);
        if (!isset($input['id'])) respond_error('Value not found: id', 400);
        if (!isset($input['unique_code'])) respond_error('Value not found: unique_code', 400);

        $int_selected_language = intval($input['int_selected_language']);
        $isPreviousAsNumber = intval($input['isPreviousAsNumber']);

        $currentUrl = trim((string)($input['currentUrl'] ?? ''));
        if ($currentUrl === '') respond_error('Invalid value: currentUrl', 400);
        $currentUrl = escape_html($currentUrl);

        $target_id = intval($input['id']);
        if ($target_id <= 0) respond_error('Invalid value: id', 400);

        $unique_code = trim((string)($input['unique_code'] ?? ''));
        if ($unique_code === '') respond_error('Invalid value: unique_code', 400);
        $unique_code = escape_html($unique_code);

        $path = parse_url($currentUrl, PHP_URL_PATH);
        $page = basename(rtrim((string)$path, '/'));

        $target_table = '';
        $search_field = '';
        $search_value = 0;

        switch ($page) {
            case 'manage-rooms':
                $current_user = wp_get_current_user();
                $current_user_id = intval($current_user->ID);
                if ($current_user_id <= 0) respond_error('Invalid user', 403);

                $target_table = $t_rooms;
                $search_field = 'user_id';
                $search_value = $current_user_id;
                break;

            case 'manage-room-lessons':
                $target_table = $t_room_lessons;
                $search_field = 'room_id';
                $search_value = intval(fetch_room_id_from_unique_code($unique_code, $int_selected_language));
                break;

            case 'manage-room-lesson-steps':
                $target_table = $t_room_lesson_steps;
                $search_field = 'lesson_id';
                $search_value = intval(fetch_lesson_id_from_unique_code($unique_code, $int_selected_language));
                break;

            case 'manage-room-lesson-step-units':
                $target_table = $t_room_lesson_step_units;
                $search_field = 'lesson_step_id';
                $search_value = intval(fetch_lesson_step_id_from_unique_code($unique_code, $int_selected_language));
                break;

            case 'manage-room-lesson-contents':
                $target_table = $t_room_lesson_contents;
                $search_field = 'step_unit_id';
                $search_value = intval(fetch_lesson_step_unit_id_from_unique_code($unique_code, $int_selected_language));
                break;

            default:
                respond_error('Invalid page', 400);
        }

        if ($search_value <= 0) {
            respond_error('Target not found', 404);
        }

        $arr_strSQL_select = [
            [$target_table, 'id'],
            [$target_table, 'sort']
        ];

        $strSQL_from = ' FROM ' . $target_table;

        $arr_strSQL_where = [
            [
                [
                    [$target_table, $search_field, '=', $search_value, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$target_table, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $targets) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        if (empty($targets)) {
            respond_success([]);
        }

        resort_records($isPreviousAsNumber, $targets, $target_id, $target_table);

        respond_success(['int_selected_language' => $int_selected_language]);

    } catch (Throwable $e) {
        respond_exception($e);
    }

