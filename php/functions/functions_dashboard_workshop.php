<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/



/******************************************************
 *  HTML
 *  
 ******************************************************/

function build_html_workshop_section(int $int_selected_language): string
{

    global
        $arr_columns_masta_teaching_material_levels;

    $str_html = '';

    /* --------------------
       messages (language)
    -------------------- */
    $arr_messages = [
        'no_room' => [
            'ルームを選択してください。',
            '請選擇教室。',
        ],
        'no_lessons' => [
            '表示できるレッスンがありません。',
            '沒有可顯示的課程。',
        ],
    ];

    $msg_no_room = $arr_messages['no_room'][$int_selected_language]
        ?? $arr_messages['no_room'][0];

    $msg_no_lessons = $arr_messages['no_lessons'][$int_selected_language]
        ?? $arr_messages['no_lessons'][0];

    /* --------------------
       room check
    -------------------- */
	if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
	
    $room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');
    if ($room_unique_code === '') {
        return
            '<div class="workshopLessonsEmpty">' .
                escape_html($msg_no_room) .
            '</div>';
    }

    /* --------------------
       mode
    -------------------- */
    $user_level = get_user_level();
    $mode = get_data_workshop_mode($room_unique_code, $user_level);

    $do_add_next_button = ($mode === 'basic' || $mode === 'plus');
    $do_display_card_status = ($mode !== 'trial');

    /* --------------------
       lessons
    -------------------- */
    $arr_lessons = get_arr_lessons_for_workshop(
        $room_unique_code,
        $int_selected_language
    );

    $lesson_count = is_array($arr_lessons) ? count($arr_lessons) : 0;

    $html_next_lesson_button = '';
    if ($do_add_next_button) {

		$current_user = wp_get_current_user();
		$current_user_id = (int)($current_user->ID ?? 0);

		if ($current_user_id > 0) {

			$data_next = get_data_next_workshop_lesson_for_user(
				$current_user_id,
				$int_selected_language
			);

			if ($data_next !== null) {
				$html_next_lesson_button = build_html_workshop_next_lesson_button(
					$lesson_count,
					$int_selected_language
				);
			}
		}
	}

    if ($lesson_count === 0) {
        return
            '<div class="workshopLessonsEmpty">' .
                escape_html($msg_no_lessons) .
            '</div>' .
            $html_next_lesson_button;
    }

    /* --------------------
       group by level
    -------------------- */
    $col_level_title = $arr_columns_masta_teaching_material_levels[$int_selected_language] ?? '';

    $arr_by_level = [];
    foreach ($arr_lessons as $les) {

        $level_id = (int)($les['level_id'] ?? 0);

        if (!isset($arr_by_level[$level_id])) {
            $arr_by_level[$level_id] = [
                'level_id' => $level_id,
                'level_sort' => (int)($les['level_sort'] ?? PHP_INT_MAX),
                'level_title' => ($col_level_title !== '' ? (string)($les[$col_level_title] ?? '') : ''),
                'lessons' => []
            ];
        }

        $arr_by_level[$level_id]['lessons'][] = $les;
    }

    uasort($arr_by_level, function ($a, $b) {
        return ((int)$a['level_sort']) <=> ((int)$b['level_sort']);
    });

    /* --------------------
       render
    -------------------- */
    foreach ($arr_by_level as $lv) {

        usort($lv['lessons'], function ($a, $b) {
            return ((int)($a['lesson_sort'] ?? 0)) <=> ((int)$b['lesson_sort']);
        });

        $level_title = escape_html(
            $lv['level_title'] !== ''
                ? $lv['level_title']
                : ('Level ' . (int)$lv['level_id'])
        );

        $str_html .=
            '<section class="workshopLevelSection" data-level-id="' . (int)$lv['level_id'] . '">' .
                '<div class="workshopLevelHeader">' .
                    '<h3 class="workshopLevelTitle">' . $level_title . '</h3>' .
                '</div>' .
                '<div class="workshopLessonSliderContainer wiseDecorativeItem">' .
                    '<div class="workshopLessonSlider" data-room-unique-code="' . escape_html($room_unique_code) . '">' .
                        build_html_workshop_lesson_cards(
                            $lv['lessons'],
                            $room_unique_code,
                            $int_selected_language,
							$do_display_card_status
                        ) .
                    '</div>' .
                '</div>' .
            '</section>';
    }

    $str_html .= build_html_workshop_grammar_modal($int_selected_language);
    $str_html .= $html_next_lesson_button;

    return $str_html;
}


function build_html_workshop_next_lesson_button(
    int $lesson_count,
    int $int_selected_language
): string
{
    $arr_labels = [
        'start' => [
            'レッスンを始める',
            '開始課程',
        ],
        'next' => [
            '次のレッスンへ',
            '下一課',
        ],
    ];

    $key = ($lesson_count === 0) ? 'start' : 'next';

    $label = $arr_labels[$key][$int_selected_language]
        ?? $arr_labels[$key][0];

    return
        '<div class="workshopNextLessonButtonArea">' .
            '<button type="button"' .
                ' id="workshopNextLessonButton"' .
                ' class="workshopNextLessonButton">' .
                escape_html($label) .
            '</button>' .
        '</div>';
}


/* =========================================================
   Functions (1–3)
========================================================= */

function fetch_arr_workshop_master_lessons(int $int_selected_language): array
{
    global
        $t_teaching_material_lessons,
        $t_teaching_material_levels,
        $arr_columns_masta_teaching_material_lessons,
        $arr_columns_masta_teaching_material_levels,
        $int_masta_teaching_material_set_id_jws_workshop;

    $col_lesson_title = $arr_columns_masta_teaching_material_lessons[$int_selected_language] ?? '';
    $col_level_title  = $arr_columns_masta_teaching_material_levels[$int_selected_language] ?? '';

    if ($col_lesson_title === '' || $col_level_title === '') {
        throw new RuntimeException('Invalid language columns');
    }

    $arr_strSQL_select = [
        [$t_teaching_material_lessons, 'id as teaching_material_lesson_id'],
        [$t_teaching_material_lessons, $col_lesson_title . ' as title'],
        [$t_teaching_material_lessons, 'sort as lesson_sort'],
        [$t_teaching_material_lessons, 'level_id'],
        [$t_teaching_material_levels, 'sort as level_sort'],
        [$t_teaching_material_levels, $col_level_title . ' as level_title'],
    ];

    $strSQL_from = "
        FROM $t_teaching_material_lessons
        LEFT JOIN $t_teaching_material_levels
            ON $t_teaching_material_lessons.level_id = $t_teaching_material_levels.id
    ";

    $arr_strSQL_where = [
        [
            [
                [$t_teaching_material_levels, 'set_id', '=', $int_masta_teaching_material_set_id_jws_workshop, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_teaching_material_levels, 'sort', 'ASC'],
        [$t_teaching_material_lessons, 'sort', 'ASC']
    ];

    list($pdo_has_error, $select_has_error, $e, $rows) =
        execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            ''
        );

    if ($pdo_has_error || $select_has_error) {
        throw new RuntimeException((string)$e);
    }

    return is_array($rows) ? $rows : [];
}

function fetch_arr_user_lessons_by_user_id(int $user_id): array
{
    global $t_user_lessons;

    $arr_strSQL_select = [
        [$t_user_lessons, 'teaching_material_lesson_id'],
        [$t_user_lessons, 'sort'],
    ];

    $strSQL_from = " FROM $t_user_lessons ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_lessons, 'user_id', '=', $user_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

	$arr_strSQL_order = [];

    list($pdo_has_error, $select_has_error, $e, $rows) =
        execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            ''
        );

    if ($pdo_has_error || $select_has_error) {
        throw new RuntimeException((string)$e);
    }

    return is_array($rows) ? $rows : [];
}

function get_data_next_workshop_lesson_for_user(int $user_id, int $int_selected_language): ?array
{
    $arr_master = fetch_arr_workshop_master_lessons($int_selected_language);
    if (empty($arr_master)) {
        return null;
    }

    $arr_user = fetch_arr_user_lessons_by_user_id($user_id);

    $registered = [];
    $max_sort = 0;

    foreach ($arr_user as $r) {
        $tm_id = (int)($r['teaching_material_lesson_id'] ?? 0);
        if ($tm_id > 0) {
            $registered[$tm_id] = true;
        }
        $max_sort = max($max_sort, (int)($r['sort'] ?? 0));
    }

    foreach ($arr_master as $m) {
        $lesson_id = (int)($m['teaching_material_lesson_id'] ?? 0);
        if ($lesson_id <= 0) {
            continue;
        }

        if (!isset($registered[$lesson_id])) {
            return [
                'teaching_material_lesson_id' => $lesson_id,
                'title' => (string)($m['title'] ?? ''),
                'next_sort' => $max_sort + 1,
                'arr_registered_lesson_ids' => array_keys($registered),
            ];
        }
    }

    return null;
}

/* =========================================================
   Insert
========================================================= */
function register_next_lesson_and_mark_previous_learned(
    int $user_id,
    int $teaching_material_lesson_id,
    string $title,
    int $sort,
    array $arr_registered_lesson_ids,
    int $int_selected_language
): bool
{
    global
        $t_user_lessons,
        $int_learning_status_learning,
        $int_learning_status_learned;

    $pdo = connect_to_database();
    if (empty($pdo)) {
        throw new RuntimeException('DB connection failed');
    }

    $pdo->beginTransaction();

    try {

        /* --------------------
           update existing lessons -> learned
        -------------------- */
        $arr_registered_lesson_ids = array_values(array_filter(
            $arr_registered_lesson_ids,
            function ($v) {
                return is_numeric($v) && (int)$v > 0;
            }
        ));

        if (!empty($arr_registered_lesson_ids)) {

            $placeholders = implode(',', array_fill(0, count($arr_registered_lesson_ids), '?'));

            $sql_update = "
                UPDATE $t_user_lessons
                SET learning_status = ?
                WHERE user_id = ?
                    AND teaching_material_lesson_id IN ($placeholders)
                    AND learning_status <> ?
            ";

            $stmt_update = $pdo->prepare($sql_update);

            $bind = [];
            $bind[] = (int)$int_learning_status_learned;
            $bind[] = (int)$user_id;

            foreach ($arr_registered_lesson_ids as $id) {
                $bind[] = (int)$id;
            }

            $bind[] = (int)$int_learning_status_learned;

            $stmt_update->execute($bind);
        }

        /* --------------------
           already exists check (safety)
        -------------------- */
        $sql_exists = "
            SELECT id
            FROM $t_user_lessons
            WHERE user_id = ?
                AND teaching_material_lesson_id = ?
            LIMIT 1
        ";

        $stmt_exists = $pdo->prepare($sql_exists);
        $stmt_exists->execute([(int)$user_id, (int)$teaching_material_lesson_id]);

        $row = $stmt_exists->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            $pdo->commit();
            return false;
        }

        /* --------------------
           generate unique_code (same logic, same PDO)
        -------------------- */
        $unique_code = generate_unique_code_with_pdo(
            $pdo,
            $t_user_lessons,
            'unique_code',
            'id',
            $int_selected_language
        );

        if ($unique_code === null) {
            throw new RuntimeException('Failed to generate unique_code');
        }

        /* --------------------
           insert new lesson
        -------------------- */
        $sql_insert = "
            INSERT INTO $t_user_lessons
                (unique_code, user_id, teaching_material_lesson_id, title, learning_status, sort)
            VALUES
                (?, ?, ?, ?, ?, ?)
        ";

        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([
            (string)$unique_code,
            (int)$user_id,
            (int)$teaching_material_lesson_id,
            (string)$title,
            (int)$int_learning_status_learning,
            (int)$sort
        ]);

        $pdo->commit();
        return true;

    } catch (PDOException $exception) {

        $pdo->rollBack();
        throw new RuntimeException($exception->getMessage());
    }
}

/**
 * generate_unique_code() と同じ思想で、同PDOで衝突チェックする版
 * - generate_random_string() を利用（既存の共通関数を使用）
 */
function generate_unique_code_with_pdo(
    PDO $pdo,
    string $target_table,
    string $target_column,
    string $select_column,
    int $int_selected_language,
    int $max_attempts = 32
): ?string
{
    for ($i = 0; $i < $max_attempts; $i++) {

        $candidate = generate_random_string();
        if ($candidate === null || $candidate === '') {
            continue;
        }

        $sql = "
            SELECT $select_column
            FROM $target_table
            WHERE BINARY $target_column = ?
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([(string)$candidate]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($row)) {
            return (string)$candidate;
        }
    }

    return null;
}
