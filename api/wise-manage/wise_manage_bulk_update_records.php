<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

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

        if (!isset($input['current_url'])) { respond_error('Value not found: current_url', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['changes'])) { respond_error('Value not found: changes', 400); }

        $current_url = (string)($input['current_url'] ?? '');
		
        if ($current_url === '') {
            respond_error('Invalid value: current_url', 400);
        }


        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
			respond_error('Invalid value: int_selected_language', 400);
		}
			
		$changes = $input['changes'];
        if (!is_array($changes) || empty($changes)) {
            respond_success([]);
        }

        $table = '';
        $allowed_cols = [];
        $int_cols = [];

        $path = parse_url($current_url, PHP_URL_PATH);
        $page = basename(rtrim((string)$path, '/'));

        switch ($page) {

            case 'manage-rooms':
                $table = $t_rooms;
                $allowed_cols = ['room_name', 'room_password', 'language_id', 'room_type', 'is_published', 'sort'];
                $int_cols = ['language_id', 'room_type', 'is_published', 'sort'];
                break;

            case 'manage-room-lessons':
                $table = $t_room_lessons;
                $allowed_cols = ['title', 'teaching_material_lesson_id', 'learning_status', 'sort', 'is_published'];
                $int_cols = ['teaching_material_lesson_id', 'learning_status', 'sort', 'is_published'];
                break;

            case 'manage-room-lesson-steps':
                $table = $t_room_lesson_steps;
                $allowed_cols = ['step_name', 'sort', 'is_published'];
                $int_cols = ['sort', 'is_published'];
                break;

            case 'manage-room-lesson-step-units':
                $table = $t_room_lesson_step_units;
                $allowed_cols = ['unit_type', 'sort', 'is_published'];
                $int_cols = ['unit_type', 'sort', 'is_published'];
                break;

            case 'manage-room-lesson-contents':
                $table = $t_room_lesson_contents;
                $allowed_cols = ['masta_japanese_root_id', 'priority', 'sort', 'is_published'];
                $int_cols = ['masta_japanese_root_id', 'priority', 'sort', 'is_published'];
                break;

            case 'manage-wise-navigation-scripts':
                $table = $t_wise_navigation_scripts;
                $allowed_cols = ['script_type_id', 'message_japanese', 'message_chinese', 'sort', 'is_published'];
                $int_cols = ['script_type_id', 'sort', 'is_published'];
                break;

            case 'manage-wise-navigation-waypoints':
                $table = $t_wise_navigation_waypoints;
                $allowed_cols = ['title', 'sort', 'is_published', 'wise_navigation_id', 'waypoint_key'];
                $int_cols = ['sort', 'is_published', 'wise_navigation_id'];
                break;

            case 'manage-wise-navigations':
                $table = $t_wise_navigations;
                $allowed_cols = ['title', 'sort', 'is_published'];
                $int_cols = ['sort', 'is_published'];
                break;

            case 'requests':
				$query = (string)parse_url($current_url, PHP_URL_QUERY);
				parse_str($query, $qs);
				$room_unique_code = escape_html((string)($qs['unique_code'] ?? ''));

				if ($room_unique_code === '') {
					respond_error('Value not found: unique_code', 400);
				}

				$arr_rooms = fetch_arr_rooms_with_owner_from_unique_code($room_unique_code, $int_selected_language);
				if (empty($arr_rooms)) {
					respond_error('Room not found', 404);
				}

				$int_room_id = intval($arr_rooms[INDEX_FIRST]['id']);
				$owner_id = intval($arr_rooms[INDEX_FIRST]['room_owner_user_id']);

				$current_user = wp_get_current_user();
				$current_user_id = intval($current_user->ID);

				if ($current_user_id !== $owner_id) {
					respond_error('Forbidden', 403);
				}


				$arr_room_users = fetch_arr_room_users_from_room_id($int_room_id, $int_selected_language);
				$arr_allowed_room_user_ids = [];
				foreach ($arr_room_users as $ru) {
					$arr_allowed_room_user_ids[intval($ru['id'])] = true; // ★ t_room_users.id
				}

                $table = $t_room_users;
                $allowed_cols = ['confirmed'];
                $int_cols = ['confirmed'];
                break;

            default:
                respond_error('Unknown page', 400);
        }

        $by_id = [];

		$arr_allow_null_columns = [
			'teaching_material_lesson_id',
		];

        foreach ($changes as $c) {

            if (!is_array($c)) {
                continue;
            }

            $id = isset($c['id']) ? intval($c['id']) : 0;
            $col = isset($c['column']) ? (string)$c['column'] : '';

            $val = null;
            if (array_key_exists('value', $c)) {
                $val = $c['value'];
            }

            if ($id <= 0 || $col === '') {
                continue;
            }

			if ($page === 'requests') {
				if (!isset($arr_allowed_room_user_ids[$id])) {
					continue;
				}
			}

            if (!in_array($col, $allowed_cols, true)) {
                continue;
            }

            if (!isset($by_id[$id])) {
                $by_id[$id] = [];
            }

            $by_id[$id][$col] = $val;
        }

        if (empty($by_id)) {
            respond_success([]);
        }

        foreach ($by_id as $id => $cols) {

            $arr_updateSQL = [];

            foreach ($cols as $col => $val) {

				$is_int = in_array($col, $int_cols, true);

				if (
					$col === 'is_published' ||
					$col === 'confirmed'
				) {
					$val = ($val === '1' || $val === 1 || $val === true || $val === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;
				}

				$placeholder = ':update_' . $col;

				if (in_array($col, $arr_allow_null_columns, true)) {

					$is_null = ($val === null || $val === '' || $val === 'null');

					if ($is_null) {
						$value_for_sql = null;
						$paramType = 'PDO::PARAM_NULL';
					} else {
						$int_val = (int)$val;
						$value_for_sql = $int_val;
						$paramType = 'PDO::PARAM_INT';
					}

				} else {

					$paramType = $is_int ? 'PDO::PARAM_INT' : 'PDO::PARAM_STR';

					if ($is_int) {
						$value_for_sql = intval($val);
					} else {
						$value_for_sql = escape_html((string)$val);
					}
				}

				$arr_updateSQL[] = [$col, $placeholder, $value_for_sql, $paramType];
			}


            if (empty($arr_updateSQL)) {
                continue;
            }

            $arr_whereSQL = [
                ['id', ':where_id', intval($id), 'PDO::PARAM_INT', '']
            ];

            list($pdo_has_error, $update_has_error, $e) = execute_update_data($table, $arr_updateSQL, $arr_whereSQL);
            handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);
        }

        respond_success(['success' => true]);

    } catch (Throwable $e) {
        respond_exception($e, 'bulk_update_unhandled');
    }

