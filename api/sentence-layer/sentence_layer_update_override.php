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
		if (!isset($input['override_id'])) respond_error('Value not found: override_id', 400);
		if (!isset($input['display_text'])) respond_error('Value not found: display_text', 400);
		if (!isset($input['is_highlighted'])) respond_error('Value not found: is_highlighted', 400);
		if (!isset($input['sort'])) respond_error('Value not found: sort', 400);

		$int_selected_language = (int)$input['int_selected_language'];
		$id = (int)$input['id'];
		$masta_override_id = (int)$input['override_id'];
		$display_text = trim((string)$input['display_text']);
		$is_highlighted = ((int)($input['is_highlighted'] ?? FLAG_FALSE) === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;
		$sort = (int)$input['sort'];

		if ($id <= 0) respond_error('invalid id', 400);
		if ($masta_override_id <= 0) respond_error('invalid override_id', 400);
		if ($sort < 0) respond_error('sort must be >= 0', 400);
		if (mb_strlen($display_text) > 255) respond_error('display_text is too long', 400);

		$arr_strSQL_select = [
			[$t_layer_element_overrides, 'id'],
			[$t_layer_element_overrides, 'layer_element_id']
		];
		$strSQL_from = ' FROM ' . $t_layer_element_overrides;
		$arr_strSQL_where = [
			[
				[
					[$t_layer_element_overrides, 'id', '=', $id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];
		$arr_strSQL_order = [];
		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_existing) = execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		if (empty($arr_existing)) respond_error('layer_element_override not found', 404);

		$arr_strSQL_select = [
			[$t_masta_override, 'id'],
			[$t_masta_override, 'operation_id'],
			[$t_masta_override_operation, 'operation']
		];
		$strSQL_from = "
			FROM $t_masta_override
			INNER JOIN $t_masta_override_operation
				ON $t_masta_override.operation_id = $t_masta_override_operation.id
		";
		$arr_strSQL_where = [
			[
				[
					[$t_masta_override, 'id', '=', $masta_override_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];
		$arr_strSQL_order = [];
		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_override_m) = execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		if (empty($arr_override_m)) respond_error('masta_override not found', 404);

		$update_table = $t_layer_element_overrides;
		$arr_updateSQL = [
			['masta_override_id', ':update_masta_override_id', $masta_override_id, 'PDO::PARAM_INT'],
			['display_text', ':update_display_text', $display_text, 'PDO::PARAM_STR'],
			['is_highlighted', ':update_is_highlighted', $is_highlighted, 'PDO::PARAM_INT'],
			['sort', ':update_sort', $sort, 'PDO::PARAM_INT']
		];
		$arr_whereSQL = [
			['id', ':where_id', $id, 'PDO::PARAM_INT', '']
		];

		list($pdo_has_error, $update_has_error, $e) = execute_update_data(
			$update_table,
			$arr_updateSQL,
			$arr_whereSQL
		);
		handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

		respond_success(['id' => $id]);

	} catch (Throwable $e) {
		respond_exception($e, 'layer_element_override_update_unhandled');
	}

