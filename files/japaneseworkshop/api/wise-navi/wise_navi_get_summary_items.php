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

        $t_wise_navigation_id = fetch_wise_navigation_id_from_unique_code($unique_code, $int_selected_language);
        if (!is_numeric($t_wise_navigation_id) || intval($t_wise_navigation_id) <= 0) {
            respond_error('Invalid navigation unique_code', 404);
        }
        $t_wise_navigation_id = intval($t_wise_navigation_id);

        $message_explanations = [
            $int_masta_script_type_id_message_explanation_for_particle,
            $int_masta_script_type_id_message_explanation_for_grammar,
            $int_masta_script_type_id_message_explanation_for_inflection
        ];

        $arr_strSQL_select = [
            [$t_masta_japanese_root, 'id'],
            [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]]
        ];

        $strSQL_from = "
            FROM $t_wise_navigations
            INNER JOIN $t_wise_navigation_waypoints
                ON $t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id
            INNER JOIN $t_wise_navigation_scripts
                ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
            INNER JOIN $t_wise_navigation_items
                ON $t_wise_navigation_scripts.id = $t_wise_navigation_items.wise_navigation_script_id
            LEFT JOIN $t_layers
                ON $t_layers.id = $t_wise_navigation_items.layer_id
            LEFT JOIN $t_masta_japanese_root
                ON $t_masta_japanese_root.id = $t_layers.masta_japanese_root_id
        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_wise_navigations, 'id', '=', $t_wise_navigation_id, 'PDO::PARAM_INT', 'And'],
                    [$t_wise_navigation_scripts, 'script_type_id', $str_sql_where_is_in, $message_explanations, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_wise_navigations, 'sort', 'ASC'],
            [$t_wise_navigation_waypoints, 'sort', 'ASC'],
            [$t_wise_navigation_scripts, 'sort', 'ASC'],
            [$t_wise_navigation_items, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );

        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        $col_title = $arr_columns_masta_japanese_root[$int_selected_language];
        $seen = [];
        $items = [];

        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $r) {
                $root_id = intval($r['id'] ?? 0);
                if ($root_id <= 0) {
                    continue;
                }
                if (isset($seen[$root_id])) {
                    continue;
                }
                $seen[$root_id] = 1;

                $title = isset($r[$col_title]) ? (string)$r[$col_title] : '';
                if ($title === '') {
                    continue;
                }

                $items[] = ['item_title' => $title];
            }
        }

        respond_success(['items' => $items]);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_navigation_message_explanations_unhandled');
    }

