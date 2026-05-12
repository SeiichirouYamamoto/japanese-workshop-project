<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../_bootstrap.php';

try {

    // ---------- Auth ----------
    $user_level = get_user_level();

    if ($user_level === null) {
        respond_error('Login required', 401);
    }

    $user_id = jws_require_single_session();

    if (!is_teacher_level($user_level)) {
        respond_error('Forbidden', 403);
    }

    // ---------- Input ----------
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (!is_array($input)) {
        respond_error('Invalid JSON', 400);
    }

    if (!isset($input['lesson_date_id'])) {
        respond_error('Value not found: lesson_date_id', 400);
    }
    if (!isset($input['memo_id'])) {
        respond_error('Value not found: memo_id', 400);
    }
    if (!isset($input['memo_text'])) {
        respond_error('Value not found: memo_text', 400);
    }
    if (!isset($input['int_selected_language'])) {
        respond_error('Value not found: int_selected_language', 400);
    }

    $lesson_date_id = (int)$input['lesson_date_id'];
    $memo_id = (int)$input['memo_id'];
    $memo_text = (string)$input['memo_text'];
    $int_selected_language = (int)$input['int_selected_language'];

    if ($lesson_date_id <= 0) {
        respond_error('Invalid lesson_date_id', 400);
    }

    // 1API 1UPDATE（更新専用）
    if ($memo_id <= 0) {
        respond_error('Invalid memo_id', 400);
    }

    // ---------- Resolve room_id & room_unique_code (JOIN) ----------
    global $t_room_lesson_dates;
    global $t_rooms;

    $arr_strSQL_select = [
        [$t_room_lesson_dates, 'room_id'],
        [$t_rooms, 'unique_code']
    ];

    $strSQL_from = ' FROM ' . $t_room_lesson_dates . '
        INNER JOIN ' . $t_rooms . '
            ON ' . $t_room_lesson_dates . '.room_id = ' . $t_rooms . '.id';

    $arr_strSQL_where = [
        [
            [
                [$t_room_lesson_dates, 'id', '=', $lesson_date_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    if (empty($arr_rows)) {
        respond_error('Memo date not found', 404);
    }

    $room_id = (int)$arr_rows[0]['room_id'];
    $room_unique_code = trim((string)$arr_rows[0]['unique_code']);

    if ($room_id <= 0 || $room_unique_code === '') {
        respond_error('Memo date not found', 404);
    }
	
	$can_access = ensure_user_can_access_room(
		$room_id,
		$int_selected_language
	);

	if (!$can_access) {
		respond_error('Forbidden', 403);
	}

    // ---------- UPDATE ----------
    global $t_room_memos;

    $update_table = $t_room_memos;

    $arr_updateSQL = [
        ['memo_text', ':update_memo_text', $memo_text, 'PDO::PARAM_STR']
    ];

    // 現状 memo_order = 0 固定（将来複数化したら、ここを可変にする）
    $arr_whereSQL = [
        ['id', ':where_id', $memo_id, 'PDO::PARAM_INT', ' AND '],
        ['lesson_date_id', ':where_lesson_date_id', $lesson_date_id, 'PDO::PARAM_INT', ' AND '],
        ['memo_order', ':where_memo_order', 0, 'PDO::PARAM_INT', '']
    ];

    list($pdo_has_error, $update_has_error, $e) = execute_update_data(
        $update_table,
        $arr_updateSQL,
        $arr_whereSQL
    );
    handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

    // JS側が data.memo_id を見るので返す
    respond_success([
        'memo_id' => $memo_id,
        'lesson_date_id' => $lesson_date_id
    ]);

} catch (Throwable $e) {

    respond_exception($e);
}
