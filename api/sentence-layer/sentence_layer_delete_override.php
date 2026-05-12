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

		if (!isset($input['int_selected_language'])) respond_error('Value not found: int_selected_language', 400);
		if (!isset($input['id'])) respond_error('Value not found: id', 400);

		$int_selected_language = (int)$input['int_selected_language'];
		$id = (int)$input['id'];

		// 未定義id
		if ($id <= 0) respond_error('invalid id', 400);

		$target_table = $t_layer_element_overrides;
		$str_deleteSQL = 'id = ?';
		$arr_values = [
			[$id, 'PDO::PARAM_INT']
		];
		list($pdo_has_error, $delete_has_error, $e) = execute_delete_data($target_table, $str_deleteSQL, $arr_values);
		handle_database_error_and_respond($pdo_has_error, $delete_has_error, $e);

		respond_success(['success' => true]);

	} catch (Throwable $e) {

		respond_exception($e, 'layer_element_override_delete_unhandled');
	}

