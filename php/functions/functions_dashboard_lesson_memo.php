<?php

function build_html_lesson_memos_page(int $int_selected_language): string
{
    $room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');

	$arr_messages = [
		'title' => [
			'レッスンメモ',
			'課堂備忘',
			'Lesson Memos',
		],
		'desc' => [
			'日付を選んでメモを開きます。',
			'請選擇日期以開啟備忘。',
			'Select a date to open the memo.',
		],
		'no_memos' => [
			'まだメモがありません。',
			'尚無備忘。',
			'No memos yet.',
		],
		'btn_open' => [
			'開く',
			'開啟',
			'Open',
		],
		'memo_modal_title' => [
			'レッスンメモ',
			'課堂備忘',
			'Lesson Memo',
		],
		'modal_close' => [
			'閉じる',
			'關閉',
			'Close',
		],
	];

    $html = '';

    if ($room_unique_code === '') {

        $html .= '<section class="jwsSection lessonMemosSection">';
        $html .= '<div class="jwsContainer">';

        $html .= '<header>';
        $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
        $html .= '</header>';

        $html .= '<p>' . escape_html($arr_messages['desc'][$int_selected_language]) . '</p>';

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    $room_id = (int)fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
    $arr_lesson_dates = fetch_arr_room_lesson_dates_by_room_id($room_id, $int_selected_language);

    $html .= '<section class="jwsSection lessonMemosSection">';
    $html .= '<div class="jwsContainer">';

    $html .= '<header>';
    $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
    $html .= '</header>';

    $html .= '<p>' . escape_html($arr_messages['desc'][$int_selected_language]) . '</p>';

    // ✅ カード生成を切り出し
    $html .= build_html_lesson_memo_cards($room_id, $arr_lesson_dates, $int_selected_language, $arr_messages);

    $html .= '</div>'; // jwsContainer
    $html .= '</section>';

    $html .= build_html_lesson_memo_modal($int_selected_language, $arr_messages);

    return $html;
}

function build_html_lesson_memo_cards(int $room_id, array $arr_lesson_dates, int $int_selected_language, array $arr_messages): string
{
    $html = '';

    $html .= '<div class="jwsCardContainer">';

    if (empty($arr_lesson_dates)) {

        $html .= '<div class="jwsCard lessonMemoCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages['no_memos'][$int_selected_language]) . '</div>';
        $html .= '</div>';

        $html .= '</div>';
        return $html;
    }

    foreach ($arr_lesson_dates as $row) {

        $memo_id = (int)($row['id'] ?? 0);
        $lesson_date = (string)($row['lesson_date'] ?? '');
        $lesson_seq = (int)($row['lesson_seq'] ?? 0);

        $lesson_date_safe = escape_html($lesson_date);

        $display_label = $lesson_date;
        if ($lesson_seq > 1) {
            $display_label .= ' #' . $lesson_seq;
        }

        $html .= '<div class="jwsCard lessonMemoCard">';

        $html .= '<div class="jwsCardTitle">' . escape_html($display_label) . '</div>';

        $html .= '<button type="button"
            class="jwsAction lessonMemoOpenButton"
			data-action="lesson_memo:show_lesson_memo_overlay"
            data-room-id="' . escape_html((string)$room_id) . '"
            data-memo-date="' . $lesson_date_safe . '"
            data-memo-seq="' . escape_html((string)$lesson_seq) . '"
            data-memo-id="' . escape_html((string)$memo_id) . '"
            aria-haspopup="dialog">';
        $html .= escape_html($arr_messages['btn_open'][$int_selected_language]);
        $html .= '</button>';

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

function build_html_lesson_memo_modal(int $int_selected_language, array $arr_messages): string
{
    $str_html_overlay_close_button = build_html_overlay_close_button();

    $str_title = escape_html($arr_messages['memo_modal_title'][$int_selected_language] ?? 'Lesson Memo');

    $str_html_screen =
        '<div id="lessonMemoModalScreen" class="workshopModal">' .
            $str_html_overlay_close_button .
            '<h2>' . $str_title . '</h2>' .
            '<div id="lessonMemoModalContentsContainer" class="modalScrollableContainer">' .
                build_html_loading_spinner('lessonMemoModalLoading') .
                '<div id="lessonMemoModalContents"></div>' .
            '</div>' .
        '</div>';

    $str_html_overlay =
        '<div id="lessonMemoOverlay" class="workshopOverlay">' .
            $str_html_screen .
        '</div>';

    return $str_html_overlay;
}

function build_html_lesson_memo_modal_contents(array $arr_lesson_memo, int $int_selected_language): string
{
    if (
		empty($arr_lesson_memo) ||
		empty(trim((string)($arr_lesson_memo[0]['memo_text'] ?? '')))
	) {

		$arr_message_empty = [
			'メモはまだありません',
			'尚未填寫備註',
			'No memo yet'
		];
		$message = $arr_message_empty[$int_selected_language] ?? 'No memo yet';
        return '<div class="lessonMemoEmpty">' . escape_html($message) . '</div>';
    }

    $row = $arr_lesson_memo[0];

    $memo_id = escape_html((string)($row['id'] ?? ''));
    $memo_text_raw = (string)($row['memo_text'] ?? '');
    $updated_at = escape_html((string)($row['updated_at'] ?? ''));

    // XSS対策でescapeした後、改行を <br> に変換
    $memo_text_html = nl2br(escape_html($memo_text_raw), false);

    $html =
        '<div id="lessonMemoModalContentsRoot" class="lessonMemoModalContentsRoot"' .
            ' data-memo-id="' . $memo_id . '"' .
            ' data-updated-at="' . $updated_at . '">' .
            '<div class="lessonMemoText">' . $memo_text_html . '</div>' .
        '</div>';

    return $html;
}
