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

		if (!isset($input['str_newLayersName'])) {
			respond_error('Value not found: str_newLayersName');
		}
		if (!isset($input['searchCriteria'])) {
			respond_error('Value not found: searchCriteria');
		}
		if (!isset($input['searchById'])) {
			respond_error('Value not found: searchById');
		}
		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language');
		}
		
		$str_newLayersName = escape_html($input['str_newLayersName']);
		$searchCriteria = escape_html($input['searchCriteria']);
		$searchById = escape_html($input['searchById']);
		$int_selected_language = intval($input['int_selected_language']);

		if($searchById == FLAG_TRUE){
			$registered_sentence_id = intval($searchCriteria);
		}
		else{
			$arr_strSQL_select = [
				[$t_registered_sentences,'id']
			];

			$strSQL_from = ' FROM ' .$t_registered_sentences;

			$arr_strSQL_where = [
				['BINARY '.$t_registered_sentences,'unique_code','=',$searchCriteria,'PDO::PARAM_STR','']
			];
			$arr_strSQL_where = [
				[
					$arr_strSQL_where,
					''
				]
			];

			$arr_strSQL_order = [];

			$strSQL_option = '';

			list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
			handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

			if(empty($arr_registered_sentence)){
				respond_success([]);
			}
			$registered_sentence_id = $arr_registered_sentence[INDEX_FIRST]['id'];
		}

		$add_sort = count_next_sort(
			$t_layers,
			'registered_sentence_id',
			$registered_sentence_id,
			$int_selected_language
		);

		$generated = generate_unique_code($t_layers, 'unique_code', 'id', $int_selected_language);

		if ($generated === null) {
			respond_error('Failed to generate unique code', 500);
		}

		$arr_insertSQL = [
			['unique_code','?',$generated,'PDO::PARAM_STR'],
			['registered_sentence_id','?',$registered_sentence_id,'PDO::PARAM_INT'],
			['masta_japanese_root_id','?',null,'PDO::PARAM_NULL'],
			['layer_name','?',$str_newLayersName,'PDO::PARAM_STR'],
			['sort','?',$add_sort,'PDO::PARAM_INT']
		];

		list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_layers, $arr_insertSQL);
		handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

		respond_success(
			[$add_sort],
			[
				'layer_id' => intval($last_insert_id),
				'unique_code' => $generated
			]
		);

	} catch (Throwable $e) {
		respond_exception($e);
	}
