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

        if (!isset($input['unique_code'])) {
            respond_error('Value not found: unique_code', 400);
        }
        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        $unique_code = escape_html((string)$input['unique_code']);
        $int_selected_language = intval($input['int_selected_language']);

        if ($unique_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $wise_navigation_script_id = fetch_wise_navigation_script_id_from_unique_code(
            $unique_code,
            $int_selected_language
        );

		// デバッグ 未定義id
        if ($wise_navigation_script_id === null) {
            respond_error('Target not found', 404);
        }

        $arr_strSQL_select = [
            [$t_wise_navigation_items, 'id as itemId'],
            [$t_wise_navigation_items, 'layer_id as layerId'],
            [$t_layers, 'layer_name as layerName'],
            [$t_wise_navigation_items, 'sort']
        ];

        $strSQL_from = " FROM
            (
                $t_wise_navigation_items
                INNER JOIN $t_layers
                ON $t_wise_navigation_items.layer_id = $t_layers.id
            )";

        $arr_strSQL_where = [
            [
                [
                    [$t_wise_navigation_items, 'wise_navigation_script_id', '=', $wise_navigation_script_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_wise_navigation_items, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_items) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );

        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        respond_success($arr_items);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_navigation_items_fetch_unhandled');
    }

