<?php

/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_history_page(int $int_selected_language): string
{
    $arr_messages = [
        'title' => [
            '学習履歴',
            '學習紀錄',
            'History',
        ],
        'no_history' => [
            'まだ履歴がありません。',
            '尚無紀錄。',
            'No history yet.',
        ],
        'board_prefix' => [
            'board:',
            'board:',
            'board:',
        ],
        'memo_prefix' => [
            'memo:',
            'memo:',
            'memo:',
        ],
        'btn_open' => [
            '開く',
            '開啟',
            'Open',
        ],
		'board_modal_title' => [
            'ホワイトボード',
            '白板',
            'Whiteboard',
        ],
        'memo_modal_title' => [
            'レッスンメモ',
            '課堂筆記',
            'Lesson Memo',
        ],
    ];

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');
    $room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);

    $html = '';
    $html .= '<section class="jwsSection historySection">';
    $html .= '<div class="jwsContainer">';
    $html .= '<header>';
    $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
    $html .= '</header>';
    $html .= '<div class="jwsCardContainer historyCardContainer">';

    if ($room_id <= 0) {
        $html .= '<div class="jwsCard historyCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages['no_history'][$int_selected_language]) . '</div>';
        $html .= '</div>';
        $html .= '</div></div></section>';
        return $html;
    }

    // 1) lesson_dates 一覧（既存関数）
    $arr_lesson_dates = fetch_arr_room_lesson_dates_by_room_id($room_id, $int_selected_language);

    if (empty($arr_lesson_dates)) {
        $html .= '<div class="jwsCard historyCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages['no_history'][$int_selected_language]) . '</div>';
        $html .= '</div>';
        $html .= '</div></div></section>';
        return $html;
    }

    $has_any_output = false;

    // 2) lesson_date ごとにカード（lesson_date_id を使う）
    foreach ($arr_lesson_dates as $ld) {

        $lesson_date_id = (int)($ld['id'] ?? 0);
        $lesson_date = (string)($ld['lesson_date'] ?? '');

        if ($lesson_date_id <= 0 || $lesson_date === '') {
            continue;
        }

        // 3) 複数前提（LIMITなし）で一覧取得（既存関数）
        $arr_whiteboards = fetch_arr_room_whiteboards_by_lesson_date_id($lesson_date_id, $int_selected_language);
        $arr_memos = fetch_arr_room_memos_by_lesson_date_id($lesson_date_id, $int_selected_language);

        // 何も無い日はカードを出さない（必要なら「空」表示にも変更可能）
        if (empty($arr_whiteboards) && empty($arr_memos)) {
            continue;
        }

        $has_any_output = true;

        $html .= '<div class="jwsCard historyCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($lesson_date) . '</div>';

        // --- Whiteboards ---
        foreach ($arr_whiteboards as $wb) {

            $whiteboard_id = (int)($wb['id'] ?? 0);
            $board_order = (int)($wb['board_order'] ?? 0);
            $board_title = isset($wb['board_title']) ? (string)$wb['board_title'] : '';

            if ($whiteboard_id <= 0) {
                continue;
            }

            // title が空/null なら seq（board_order）を利用
            $fallback_seq = $board_order > 0 ? $board_order : 1;
            $label = $board_title !== '' ? $board_title : ('#' . $fallback_seq);

            // 4) p + button セットをコンテナに入れる
            $html .= '<div class="historyItemContainer whiteboardItemContainer">';
            $html .= '<div class="historyItemContainerTitle">' . escape_html($arr_messages['board_prefix'][$int_selected_language] . $label) . '</div>';
            $html .= '<button type="button"
                class="jwsAction lessonWhiteboardOpenButton"
                data-action="lesson_whiteboard:show_lesson_whiteboard_overlay"
                data-room-id="' . escape_html((string)$room_id) . '"
                data-lesson-date="' . escape_html($lesson_date) . '"
                data-whiteboard-seq="' . escape_html((string)$fallback_seq) . '"
                data-whiteboard-id="' . escape_html((string)$whiteboard_id) . '"
                aria-haspopup="dialog">';
            $html .= escape_html($arr_messages['btn_open'][$int_selected_language]);
            $html .= '</button>';
            $html .= '</div>';
        }

        // --- Memos ---
        foreach ($arr_memos as $memo) {

            $memo_id = (int)($memo['id'] ?? 0);
            $memo_order = (int)($memo['memo_order'] ?? 0);
            $memo_title = isset($memo['memo_title']) ? (string)$memo['memo_title'] : '';

            if ($memo_id <= 0) {
                continue;
            }

            // title が空/null なら seq（memo_order）を利用
            $fallback_seq = $memo_order > 0 ? $memo_order : 1;
            $label = $memo_title !== '' ? $memo_title : ('#' . $fallback_seq);

            // 4) p + button セットをコンテナに入れる
            $html .= '<div class="historyItemContainer memoItemContainer">';
            $html .= '<div class="historyItemContainerTitle">' . escape_html($arr_messages['memo_prefix'][$int_selected_language] . $label) . '</div>';
            $html .= '<button type="button"
                class="jwsAction lessonMemoOpenButton"
                data-action="lesson_memo:show_lesson_memo_overlay"
                data-room-id="' . escape_html((string)$room_id) . '"
                data-memo-date="' . escape_html($lesson_date) . '"
                data-lesson-seq="' . escape_html((string)$fallback_seq) . '"
                data-memo-id="' . escape_html((string)$memo_id) . '"
                aria-haspopup="dialog">';
            $html .= escape_html($arr_messages['btn_open'][$int_selected_language]);
            $html .= '</button>';
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    if (!$has_any_output) {
        $html .= '<div class="jwsCard historyCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages['no_history'][$int_selected_language]) . '</div>';
        $html .= '</div>';
    }

    $html .= '</div>'; // jwsCardContainer
    $html .= '</div>'; // jwsContainer
    $html .= '</section>';

	$html .= build_html_lesson_whiteboard_modal($int_selected_language, $arr_messages);
	$html .= build_html_lesson_memo_modal($int_selected_language, $arr_messages);

    return $html;
}

function build_html_lesson_whiteboard_modal(int $int_selected_language, array $arr_messages): string
{
    $str_html_overlay_close_button = build_html_overlay_close_button();
    $str_title = escape_html($arr_messages['board_modal_title'][$int_selected_language] ?? 'Whiteboard');

    $str_html_screen =
		'<div id="lessonWhiteboardModalScreen" class="workshopModal">' .
			$str_html_overlay_close_button .
			'<h2 id="lessonWhiteboardModalTitle">' . $str_title . '</h2>' .
			'<div id="lessonWhiteboardModalContentsContainer" class="modalScrollableContainerXY">' .
				build_html_loading_spinner('lessonWhiteboardModalLoading') .

				'<div id="lessonWhiteboardModalBoard" class="lessonWhiteboardModalBoard whiteboardModalResizeTarget">' .
					'<div id="lessonWhiteboardModalCanvasLayer" class="lessonWhiteboardModalCanvasLayer wiseDecorativeItem whiteboardModalResizeTarget">' .
						'<div id="lessonWhiteboardModalCanvasLinkedContainer" class="lessonWhiteboardModalCanvasLinkedContainer whiteboardModalResizeTarget"></div>' .
						'<canvas id="lessonWhiteboardModalCanvas" class="whiteboardModalResizeTarget"></canvas>' .
					'</div>' .

					'<div id="lessonWhiteboardModalElementsLayer" class="lessonWhiteboardModalElementsLayer wiseDecorativeItem whiteboardModalResizeTarget"></div>' .
					'<div id="lessonWhiteboardModalMarksLayer" class="lessonWhiteboardModalMarksLayer wiseDecorativeItem whiteboardModalResizeTarget"></div>' .
				'</div>' .
			'</div>' .
		'</div>';

    $str_html_overlay =
        '<div id="lessonWhiteboardOverlay" class="workshopOverlay">' .
            $str_html_screen .
        '</div>';

    return $str_html_overlay;
}