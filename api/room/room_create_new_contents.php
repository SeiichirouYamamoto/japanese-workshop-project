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

        if (!isset($input['unique_code'])) respond_error('Value not found: unique_code', 400);
        if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);
        if (!isset($input['current_url'])) respond_error('Value not found: current_url', 400);

        $room_contents_code = trim((string)($input['unique_code'] ?? ''));
        if ($room_contents_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }
        $room_contents_code = escape_html($room_contents_code);

        $int_selected_language = (int)$input['int_selected_language'];

        $current_url = (string)$input['current_url'];
        $path = parse_url($current_url, PHP_URL_PATH);
        $page = basename(rtrim((string)$path, '/'));

        $allowed_pages = [
            'manage-rooms',
            'manage-room-lessons',
            'manage-room-lesson-steps',
            'manage-room-lesson-step-units',
            'manage-room-lesson-contents'
        ];

        if (!in_array($page, $allowed_pages, true)) {
            respond_error('invalid page', 400);
        }

        if ($page === 'manage-room-lesson-contents') {

            if (!isset($input['send_japanese_id'])) respond_error('Value not found: send_japanese_id', 400);

            $t_masta_japanese_root_id = (int)$input['send_japanese_id'];
            if ($t_masta_japanese_root_id <= 0) {
                respond_error('Invalid value: send_japanese_id', 400);
            }

            $step_unit_id = (int)fetch_lesson_step_unit_id_from_unique_code($room_contents_code, $int_selected_language);
            if ($step_unit_id <= 0) {
                respond_error('step_unit not found', 404);
            }

            $add_sort = count_next_sort($t_room_lesson_contents, 'step_unit_id', $step_unit_id, $int_selected_language);

            $arr_insertSQL = [
                ['step_unit_id', '?', $step_unit_id, 'PDO::PARAM_INT'],
                ['masta_japanese_root_id', '?', $t_masta_japanese_root_id, 'PDO::PARAM_INT'],
                ['priority', '?', $int_default_value_proxy_0, 'PDO::PARAM_INT'],
                ['sort', '?', $add_sort, 'PDO::PARAM_INT']
            ];

            list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_room_lesson_contents, $arr_insertSQL);
            handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

            respond_success(['int_selected_language' => $int_selected_language]);
        }

        if (!isset($input['send_array'])) respond_error('Value not found: send_array', 400);

        $send_array = $input['send_array'];

        if (!is_array($send_array)) {
            respond_error('Invalid value: send_array', 400);
        }

        if (empty($send_array)) {
            respond_error('send_array must not be empty', 400);
        }

        foreach ($send_array as $inputValue) {

            switch ($page) {

                case 'manage-rooms':

                    $generated = generate_unique_code($t_rooms, 'unique_code', 'id', $int_selected_language);
                    if ($generated === null) respond_error('Failed to generate unique code', 500);

                    $current_user = wp_get_current_user();
                    $current_user_id = (int)$current_user->ID;
                    if ($current_user_id <= 0) respond_error('Invalid user', 403);

                    $room_name = trim((string)$inputValue);
                    if ($room_name === '') respond_error('Invalid value in send_array', 400);

                    $add_sort = count_next_sort($t_rooms, 'room_owner_user_id', $current_user_id, $int_selected_language);

                    $pdo = connect_to_database();
                    if (empty($pdo)) {
                        respond_error('Database connection failed', 500);
                    }

                    try {

                        $pdo->beginTransaction();

                        $sql = 'INSERT INTO ' . $t_rooms . ' (unique_code, room_password, room_name, language_id, room_type, is_published, sort, room_owner_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(1, $generated, PDO::PARAM_STR);
                        $stmt->bindValue(2, $str_avoid_null_proxy, PDO::PARAM_STR);
                        $stmt->bindValue(3, escape_html($room_name), PDO::PARAM_STR);
                        $stmt->bindValue(4, $int_selected_language, PDO::PARAM_INT);
                        $stmt->bindValue(5, $int_masta_room_type_private_lesson, PDO::PARAM_INT);
                        $stmt->bindValue(6, FLAG_TRUE, PDO::PARAM_INT);
                        $stmt->bindValue(7, $add_sort, PDO::PARAM_INT);
                        $stmt->bindValue(8, $current_user_id, PDO::PARAM_INT);
                        $stmt->execute();

                        $room_id = (int)$pdo->lastInsertId();
                        if ($room_id <= 0) {
                            throw new Exception('Failed to create room');
                        }

                        $sql = 'INSERT INTO ' . $t_room_users . ' (room_id, user_id, confirmed) VALUES (?, ?, ?)';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(1, $room_id, PDO::PARAM_INT);
                        $stmt->bindValue(2, $current_user_id, PDO::PARAM_INT);
                        $stmt->bindValue(3, FLAG_TRUE, PDO::PARAM_INT);
                        $stmt->execute();

                        $pdo->commit();
                        $pdo = null;

                    } catch (Throwable $e) {

                        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        $pdo = null;

                        throw $e;
                    }

                    break;

                case 'manage-room-lessons':

                    $generated = generate_unique_code($t_room_lessons, 'unique_code', 'id', $int_selected_language);
                    if ($generated === null) respond_error('Failed to generate unique code', 500);

                    $room_id = (int)fetch_room_id_from_unique_code($room_contents_code, $int_selected_language);
                    if ($room_id <= 0) respond_error('room not found', 404);

                    $title = trim((string)$inputValue);
                    if ($title === '') respond_error('Invalid value in send_array', 400);

                    $add_sort = count_next_sort($t_room_lessons, 'room_id', $room_id, $int_selected_language);

                    $arr_insertSQL = [
                        ['unique_code', '?', $generated, 'PDO::PARAM_STR'],
                        ['room_id', '?', $room_id, 'PDO::PARAM_INT'],
                        ['teaching_material_lesson_id', '?', null, 'PDO::PARAM_NULL'],
                        ['title', '?', escape_html($title), 'PDO::PARAM_STR'],
                        ['learning_status', '?', $int_default_value_proxy_0, 'PDO::PARAM_INT'],
                        ['sort', '?', $add_sort, 'PDO::PARAM_INT']
                    ];

                    list($pdo_has_error, $insert_has_error, $e, $dummy) = execute_insert_data($t_room_lessons, $arr_insertSQL);
                    handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

                    break;

                case 'manage-room-lesson-steps':

                    $generated = generate_unique_code($t_room_lesson_steps, 'unique_code', 'id', $int_selected_language);
                    if ($generated === null) respond_error('Failed to generate unique code', 500);

                    $lesson_id = (int)fetch_lesson_id_from_unique_code($room_contents_code, $int_selected_language);
                    if ($lesson_id <= 0) respond_error('lesson not found', 404);

                    $step_name = trim((string)$inputValue);
                    if ($step_name === '') respond_error('Invalid value in send_array', 400);

                    $add_sort = count_next_sort($t_room_lesson_steps, 'lesson_id', $lesson_id, $int_selected_language);

                    $arr_insertSQL = [
                        ['unique_code', '?', $generated, 'PDO::PARAM_STR'],
                        ['lesson_id', '?', $lesson_id, 'PDO::PARAM_INT'],
                        ['step_name', '?', escape_html($step_name), 'PDO::PARAM_STR'],
                        ['sort', '?', $add_sort, 'PDO::PARAM_INT']
                    ];

                    list($pdo_has_error, $insert_has_error, $e, $dummy) = execute_insert_data($t_room_lesson_steps, $arr_insertSQL);
                    handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

                    break;

                case 'manage-room-lesson-step-units':

                    $generated = generate_unique_code($t_room_lesson_step_units, 'unique_code', 'id', $int_selected_language);
                    if ($generated === null) respond_error('Failed to generate unique code', 500);

                    $lesson_step_id = (int)fetch_lesson_step_id_from_unique_code($room_contents_code, $int_selected_language);
                    if ($lesson_step_id <= 0) respond_error('lesson_step not found', 404);

                    if (!is_numeric($inputValue)) respond_error('Invalid value in send_array', 400);

                    $unit_type = (int)$inputValue;
                    if ($unit_type <= 0) respond_error('Invalid value in send_array', 400);

                    $add_sort = count_next_sort($t_room_lesson_step_units, 'lesson_step_id', $lesson_step_id, $int_selected_language);

                    $arr_insertSQL = [
                        ['unique_code', '?', $generated, 'PDO::PARAM_STR'],
                        ['lesson_step_id', '?', $lesson_step_id, 'PDO::PARAM_INT'],
                        ['unit_type', '?', $unit_type, 'PDO::PARAM_INT'],
                        ['is_published', '?', FLAG_TRUE, 'PDO::PARAM_INT'],
                        ['sort', '?', $add_sort, 'PDO::PARAM_INT']
                    ];

                    list($pdo_has_error, $insert_has_error, $e, $dummy) = execute_insert_data($t_room_lesson_step_units, $arr_insertSQL);
                    handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

                    break;

                default:
                    respond_error('invalid page', 400);
            }
        }

        respond_success(['int_selected_language' => $int_selected_language]);

    } catch (Throwable $e) {
        respond_exception($e);
    }

