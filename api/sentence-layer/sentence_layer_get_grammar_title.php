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

		if (!array_key_exists('int_japanese_id', $input)) {
			respond_error('Value not found: int_japanese_id', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$t_masta_japanese_root_id = is_null($input['int_japanese_id']) ? null : (int)$input['int_japanese_id'];
		$int_selected_language = (int)$input['int_selected_language'];

		if ($t_masta_japanese_root_id === null || $t_masta_japanese_root_id <= 0) {
			respond_success([]);
		}

		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($t_masta_japanese_root_id, $int_selected_language);

		if (empty($arr_masta_japanese_root)) {
			respond_success([]);
		}

		$str_japanese = $arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]] ?? null;

		if ($str_japanese === null || $str_japanese === '') {
			respond_success([]);
		}

		respond_success(['japanese' => $str_japanese]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

