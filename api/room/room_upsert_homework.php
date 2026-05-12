<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		global $t_room_homeworks;

		$user_level = get_user_level();

		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_teacher_level($user_level)) {
			respond_error('Forbidden', 403);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}
		$int_selected_language = (int)$input['int_selected_language'];

		if (!isset($input['room_unique_code'])) {
			respond_error('Value not found: room_unique_code', 400);
		}
		$room_unique_code = escape_html((string)$input['room_unique_code']);

		$room_id = fetch_room_id_from_unique_code(
			$room_unique_code,
			$int_selected_language
		);

		if (empty($room_id) === true) {
			respond_error('Invalid room_unique_code', 404);
		}
		$room_id = (int)$room_id;

		$arr_grammar_information_raw = $input['arr_grammar_information'] ?? [];
		if (!is_array($arr_grammar_information_raw)) {
			respond_error('Invalid arr_grammar_information', 400);
		}

		// days_later => [type => [codes...]]
		// 空配列（= 宿題なし）も「その日を対象にする」ために保持します
		$arr_grammar_information = [];

		foreach ($arr_grammar_information_raw as $day => $types) {

			$days_later = (int)$day;

			if ($days_later < 0) {
				continue;
			}

			if (!is_array($types)) {
				continue;
			}

			// 「宿題がない日」でも、ここでキーだけ作っておく
			if (!isset($arr_grammar_information[$days_later])) {
				$arr_grammar_information[$days_later] = [];
			}

			foreach ($types as $type => $codes) {

				if (!is_array($codes)) {
					continue;
				}

				$escaped_codes = [];
				foreach ($codes as $code) {
					$code = trim((string)$code);
					if ($code !== '') {
						$escaped_codes[] = escape_html($code);
					}
				}

				if (!empty($escaped_codes)) {
					$arr_grammar_information[$days_later][(string)$type] = $escaped_codes;
				}
			}
		}

		$base_date = new DateTimeImmutable('today');

		// form_list_json は「受け取る」or「サーバ側で作る」どちらでも運用できるようにしておきます
		$arr_form_list_input = $input['form_list'] ?? null;

		if (is_array($arr_form_list_input)) {

			$arr_form_list_masta_japanese_root_ids = [];

			foreach ($arr_form_list_input as $v) {

				$int_v = (int)$v;

				if ($int_v > 0) {
					$arr_form_list_masta_japanese_root_ids[] = $int_v;
				}
			}

		} else {

			$arr_already_learned_list = [];

			if (isset($_SESSION['arr_already_learned_list']) && is_array($_SESSION['arr_already_learned_list'])) {
				$arr_already_learned_list = $_SESSION['arr_already_learned_list'];
			}

			$arr_form_list = fetch_arr_form_root_list($arr_already_learned_list, $int_selected_language);
			$arr_form_list_masta_japanese_root_ids = array_map('intval', array_column($arr_form_list, 'masta_japanese_root_id'));
		}

		$json_form_list = json_encode($arr_form_list_masta_japanese_root_ids, JSON_UNESCAPED_UNICODE);

		$arr_results = [];
		$has_inserted_homework = false;
		$arr_inserted_dates = [];

		foreach ($arr_grammar_information as $days_later => $content_by_type) {

			$target_date = $base_date->modify('+' . (int)$days_later . ' days')->format('Y-m-d');

			$has_homework = false;
			foreach ($content_by_type as $codes) {
				if (is_array($codes) && !empty($codes)) {
					$has_homework = true;
					break;
				}
			}

			$json_content = json_encode($content_by_type, JSON_UNESCAPED_UNICODE);

			// 既存チェック（room_id + target_date）
			$arr_strSQL_select = [
				[$t_room_homeworks, 'id']
			];

			$strSQL_from = ' FROM ' . $t_room_homeworks;

			$arr_strSQL_where = [
				[
					[
						[$t_room_homeworks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
						[$t_room_homeworks, 'target_date', '=', $target_date, 'PDO::PARAM_STR', '']
					],
					''
				]
			];

			$arr_strSQL_order = [];
			$strSQL_option = ' LIMIT 1';

			list($pdo_has_error, $select_has_error, $e, $arr_existing) = execute_select_and_fetch_all(
				$arr_strSQL_select,
				$strSQL_from,
				$arr_strSQL_where,
				$arr_strSQL_order,
				$strSQL_option
			);
			handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

			$is_exists = (empty($arr_existing) === false);
			$homework_id = $is_exists ? (int)$arr_existing[0]['id'] : 0;

			if ($has_homework === true && $is_exists === false) {

				// 宿題がある + target_dateのレコードがない → 新規作成
				$arr_insertSQL = [
					['room_id', '?', $room_id, 'PDO::PARAM_INT'],
					['target_date', '?', $target_date, 'PDO::PARAM_STR'],
					['content_json', '?', $json_content, 'PDO::PARAM_STR'],
					['form_list_json', '?', $json_form_list, 'PDO::PARAM_STR'],
					['created_by_user_id', '?', $user_id, 'PDO::PARAM_INT'],
					['updated_by_user_id', '?', $user_id, 'PDO::PARAM_INT']
				];

				list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_room_homeworks, $arr_insertSQL);
				handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

				$action = 'inserted';
				$has_inserted_homework = true;
				$arr_inserted_dates[] = $target_date;


			} elseif ($has_homework === true && $is_exists === true) {

				// 宿題がある + target_dateのレコードがある → 更新
				$update_table = $t_room_homeworks;

				$arr_updateSQL = [
					['content_json', ':update_content_json', $json_content, 'PDO::PARAM_STR'],
					['form_list_json', ':update_form_list_json', $json_form_list, 'PDO::PARAM_STR'],
					['updated_by_user_id', ':update_updated_by_user_id', $user_id, 'PDO::PARAM_INT']
				];

				$arr_whereSQL = [
					['id', ':where_id', $homework_id, 'PDO::PARAM_INT', '']
				];

				list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);
				handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

				$action = 'updated';

			} elseif ($has_homework === false && $is_exists === false) {

				// 宿題がない + target_dateのレコードがない → 何もしない
				$action = 'none';

			} else {

				// 宿題がない + target_dateのレコードがある → レコードを削除
				$str_deleteSQL = 'id = ?';
				$arr_values = [
					[$homework_id, 'PDO::PARAM_INT']
				];

				list($pdo_has_error, $delete_has_error, $e) = execute_delete_data(
					$t_room_homeworks,
					$str_deleteSQL,
					$arr_values
				);

				handle_database_error_and_respond($pdo_has_error, $delete_has_error, $e);

				$action = 'deleted';
			}

			$arr_results[] = [
				'days_later' => (int)$days_later,
				'target_date' => $target_date,
				'action' => $action
			];
		}


		if ($has_inserted_homework === true) {

            $arr_room_member_users = fetch_arr_room_member_users_with_email($room_id, $int_selected_language);

            $from_email = $str_mysite_mail_address_info;
            // $from_email = 'no-reply@localhost';

			$url_home_current = get_home_url(
				get_data_blog_id_from_selected_language($int_selected_language ?? null),
				'/'
			);


			$arr_messages_mail = [

				'homework_added_subject' => [
					'【Japanese Workshop】宿題が追加されました',     // 0: 日本語
					'【Japanese Workshop】已新增作業',               // 1: 繁体字
					'【Japanese Workshop】New homework added',       // 2: English
				],

				'homework_added_body_intro' => [
					'宿題が追加されました。',
					'已新增作業。',
					'New homework has been added.',
				],

				'homework_added_body_room' => [
					'Room',
					'教室',
					'Room',
				],

				'homework_added_body_date' => [
					'日付',
					'日期',
					'Date',
				],

				'homework_added_body_footer' => [
					'ログインしてご確認ください。',
					'請登入後確認。',
					'Please log in to check.',
				],
				'homework_added_body_link' => [
					'こちらからアクセスしてください',
					'請從這裡進入',
					'Access here',
				],
			];

			$subject = $arr_messages_mail['homework_added_subject'][$int_selected_language];

			$body  = '';
			$body .= $arr_messages_mail['homework_added_body_intro'][$int_selected_language] . "\n\n";
			$body .= $arr_messages_mail['homework_added_body_room'][$int_selected_language]
					. ': ' . $room_unique_code . "\n";
			$body .= $arr_messages_mail['homework_added_body_date'][$int_selected_language]
					. ': ' . implode(', ', $arr_inserted_dates) . "\n\n";
			$body .= $arr_messages_mail['homework_added_body_footer'][$int_selected_language] . "\n\n";

			$body .= $arr_messages_mail['homework_added_body_link'][$int_selected_language] . "\n";
			$body .= $url_home_current . "\n";

            foreach ($arr_room_member_users as $u) {

                $to_email = $u['user_email'] ?? '';
                notify_homework_added_mail($to_email, $subject, $body, $from_email);
            }
        }


		respond_success([
			'room_unique_code' => $room_unique_code,
			'room_id' => $room_id,
			'base_date' => $base_date->format('Y-m-d'),
			'results' => $arr_results
		]);

	} catch (Throwable $e) {
		respond_exception($e);
	}
