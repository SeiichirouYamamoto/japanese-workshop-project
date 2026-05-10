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

        if (!isset($input['currentTime'])) { respond_error('Value not found: currentTime', 400); }
        if (!isset($input['isActioned'])) { respond_error('Value not found: isActioned', 400); }
        if (!isset($input['isFirstInterval'])) { respond_error('Value not found: isFirstInterval', 400); }

        $current_user = wp_get_current_user();
        $current_user_id = intval($current_user->ID);

        if ($current_user_id <= 0) {
            respond_error('Login required', 401);
        }

        $currentTime = escape_html((string)($input['currentTime'] ?? ''));

        $isActioned = intval($input['isActioned'] ?? FLAG_FALSE);
        $isActioned = ($isActioned === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $isFirstInterval = intval($input['isFirstInterval'] ?? FLAG_FALSE);
        $isFirstInterval = ($isFirstInterval === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $remote_addr = escape_html((string)$remote_addr);

        $arr_insertSQL = [
            ['user_id', '?', $current_user_id, 'PDO::PARAM_INT'],
            ['session_current_time', '?', $currentTime, 'PDO::PARAM_STR'],
            ['action_taken', '?', $isActioned, 'PDO::PARAM_INT'],
            ['session_start', '?', $isFirstInterval, 'PDO::PARAM_INT'],
            ['ip', '?', $remote_addr, 'PDO::PARAM_STR'],
            ['confirmed', '?', FLAG_FALSE, 'PDO::PARAM_INT']
        ];

        list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data(
            $t_usage_historys,
            $arr_insertSQL
        );
        handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

        respond_success([]);

    } catch (Throwable $e) {
        respond_exception($e, 'usage_history_insert_unhandled');
    }

