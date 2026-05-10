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

		if (!isset($input['int_layer_id'])) {
			respond_error('Value not found: int_layer_id', 400);
		}

		$int_layer_id = intval($input['int_layer_id']);

		$arr_strSQL_select = [
			[$t_layers, 'id']
		];

		$strSQL_from = " FROM
						(
							$t_layers
							INNER JOIN $t_layer_elements
							ON
							$t_layers.id = $t_layer_elements.layer_id 
						)
						INNER JOIN $t_layer_element_overrides
						ON
						$t_layer_elements.id = $t_layer_element_overrides.layer_element_id 
						";

		$arr_strSQL_where = [
			[
				[
					[$t_layers, 'id', '=', $int_layer_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [];
		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_layer_element_overrides) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		respond_success(['hasOverride' => empty($arr_layer_element_overrides) ? FLAG_FALSE : FLAG_TRUE]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

