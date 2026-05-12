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
		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language');
		}

		$int_layer_id = intval($input['int_layer_id']);
		$int_selected_language = intval($input['int_selected_language']);

		$arr_strSQL_select = [
			[$t_layer_elements, 'id'],
			[$t_layer_elements, 'registered_sentence_element_id as sentenceElementId'],
			[$t_layer_elements, 'form_id as ' . $str_snake_to_camel_form_id],
			[$t_layer_elements, 'voice_id as ' . $str_snake_to_camel_voice_id],
			[$t_layer_elements, 'is_highlighted as isHighlighted'],
			[$t_registered_sentence_elements, 'japanese']
		];

		$strSQL_from = " FROM
						$t_layer_elements
						INNER JOIN $t_registered_sentence_elements
						ON
						$t_layer_elements.registered_sentence_element_id = $t_registered_sentence_elements.id
						";

		$arr_strSQL_where = [
			[
				[
					[$t_layer_elements, 'layer_id', '=', $int_layer_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [
			[$t_registered_sentence_elements, 'sort', 'ASC']
		];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_layer_elements) = execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		$arr_form_list = fetch_arr_form_root_list([], $int_selected_language);
		$arr_voice_list = fetch_arr_voice_form_root_list([], $int_selected_language);

		array_unshift($arr_form_list, [
			'id' => 0,
			'masta_japanese_root_id' => 0,
			'title' => 'default'
		]);

		array_unshift(
			$arr_voice_list,
			[
				'id' => 0,
				'masta_japanese_root_id' => 0,
				'title' => 'default'
			],
			[
				'id' => $int_PoliteFormAffirmativeNotPastTense,
				'masta_japanese_root_id' => 0,
				'title' => 'no change'
			]
		);

		respond_success($arr_layer_elements, [
			'inflection' => $arr_form_list,
			'voice' => $arr_voice_list
		]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

