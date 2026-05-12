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
			respond_error('Value not found: int_layer_id');
		}
		if (!array_key_exists('int_layerUpdateScreenGrammarId', $input)) {
			respond_error('Value not found: int_layerUpdateScreenGrammarId');
		}
		if (!isset($input['str_layerUpdateScreenLayerName'])) {
			respond_error('Value not found: str_layerUpdateScreenLayerName');
		}
		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language');
		}

		$int_layer_id = intval($input['int_layer_id']);

		$rawGrammarId = $input['int_layerUpdateScreenGrammarId'];
		if ($rawGrammarId === '' || $rawGrammarId === null || strtolower((string)$rawGrammarId) === 'null') {
			$grammarId = null;
			$grammarParam = 'PDO::PARAM_NULL';
		} else {
			if (is_numeric($rawGrammarId)) {
				$grammarId = (int)$rawGrammarId;
				$grammarParam = 'PDO::PARAM_INT';
			} else {
				respond_error('Invalid value: int_layerUpdateScreenGrammarId');
			}
		}

		$str_layerUpdateScreenLayerName = escape_html($input['str_layerUpdateScreenLayerName']);
		$int_selected_language = intval($input['int_selected_language']);

		$update_table = $t_layers;

		$arr_updateSQL = [
			['masta_japanese_root_id', ':update_masta_japanese_root_id', $grammarId, $grammarParam],
			['layer_name', ':update_layer_name', $str_layerUpdateScreenLayerName, 'PDO::PARAM_STR']
		];

		$arr_whereSQL = [
			['id', ':where_id', $int_layer_id, 'PDO::PARAM_INT', '']
		];

		list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);
		handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

		respond_success(['success' => true]);
		
	} catch (Throwable $e) {
		respond_exception($e);
	}

