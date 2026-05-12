<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}
		if (!isset($input['grammar_unique_code'])) {
			respond_error('Value not found: grammar_unique_code', 400);
		}
		if (!isset($input['is_bookmarked'])) {
			respond_error('Value not found: is_bookmarked', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];

		$room_unique_code = isset($input['room_unique_code'])
			? trim((string)$input['room_unique_code'])
			: '';

		$grammar_unique_code = trim((string)$input['grammar_unique_code']);

		$is_bookmarked = (int)$input['is_bookmarked'];
		$is_bookmarked = ($is_bookmarked === 1) ? 1 : 0;

		if ($grammar_unique_code === '') {
			respond_error('Invalid grammar_unique_code', 400);
		}

		$current_user = wp_get_current_user();
		$current_user_id = (int)$current_user->ID;

		if ($current_user_id <= 0) {
			respond_error('Login required', 401);
		}

		// room は任意。未指定なら個人用として room_id = 0
		$room_id = 0;

		if ($user_level >= $int_Basic_Teacher) {
			if (
				$room_unique_code !== '' &&
				$room_unique_code !== $workshop_no_room_unique_code
			) {

				$room_id = (int)fetch_room_id_from_unique_code(
					$room_unique_code,
					$int_selected_language
				);

				if ($room_id <= 0) {
					respond_error('Room not found', 404);
				}

				$can_access = ensure_user_can_access_room(
					$room_id,
					$int_selected_language
				);

				if (!$can_access) {
					respond_error('Forbidden', 403);
				}
			}
		}

		$masta_japanese_root_id = (int)fetch_masta_japanese_root_id_from_unique_code(
			$grammar_unique_code,
			$int_selected_language
		);

		if ($masta_japanese_root_id <= 0) {
			respond_error('Grammar not found', 404);
		}

		// 既存レコード（user_id + room_id + root_id）確認
		$arr_strSQL_select = [
			[$t_user_bookmarks, 'id'],
			[$t_user_bookmarks, 'deleted_at']
		];

		$strSQL_from = ' FROM ' . $t_user_bookmarks;

		$arr_strSQL_where = [
			[
				[
					[$t_user_bookmarks, 'user_id', '=', $current_user_id, 'PDO::PARAM_INT', 'And'],
					[$t_user_bookmarks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
					[$t_user_bookmarks, 'masta_japanese_root_id', '=', $masta_japanese_root_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [];
		$strSQL_option = 'LIMIT 1';

		list($pdo_has_error, $select_has_error, $e, $arr_existing) = execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);

		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		$exists = !empty($arr_existing);
		$bookmark_id = $exists ? (int)($arr_existing[INDEX_FIRST]['id'] ?? 0) : 0;

		if ($is_bookmarked === 1) {

			if (!$exists) {

				// INSERT（移行期 status は仮で 0）
				$arr_insertSQL = [
					['user_id', '?', $current_user_id, 'PDO::PARAM_INT'],
					['room_id', '?', $room_id, 'PDO::PARAM_INT'],
					['masta_japanese_root_id', '?', $masta_japanese_root_id, 'PDO::PARAM_INT'],
					['status', '?', 0, 'PDO::PARAM_INT'],
					['deleted_at', '?', null, 'PDO::PARAM_NULL']
				];

				list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data(
					$t_user_bookmarks,
					$arr_insertSQL
				);

				handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

			} else {

				// UPDATE（復活：deleted_at = NULL）
				$arr_updateSQL = [
					['deleted_at', ':update_deleted_at', null, 'PDO::PARAM_NULL']
				];

				$arr_whereSQL = [
					['id', ':where_id', $bookmark_id, 'PDO::PARAM_INT', '']
				];

				list($pdo_has_error, $update_has_error, $e) = execute_update_data(
					$t_user_bookmarks,
					$arr_updateSQL,
					$arr_whereSQL
				);

				handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);
			}

		} else {

			// is_bookmarked === 0（削除は冪等にしておく）
			if ($exists && $bookmark_id > 0) {

				$now = current_time('mysql');

				$arr_updateSQL = [
					['deleted_at', ':update_deleted_at', $now, 'PDO::PARAM_STR']
				];

				$arr_whereSQL = [
					['id', ':where_id', $bookmark_id, 'PDO::PARAM_INT', '']
				];

				list($pdo_has_error, $update_has_error, $e) = execute_update_data(
					$t_user_bookmarks,
					$arr_updateSQL,
					$arr_whereSQL
				);

				handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);
			}
		}

		respond_success([
			'success' => true,
			'is_bookmarked' => ($is_bookmarked === 1),
			'room_id' => $room_id,
			'masta_japanese_root_id' => $masta_japanese_root_id
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
