<?php

/******************************************************
 *  Dashboard Bookmark Pages (Manage)
 *
 *  Pages:
 *      1) your_bookmarks
 *      2) room_bookmarks
 *      3) bookmark_activity
 *
 *  Notes:
 *  - No tab navigation
 *  - Grammar title is plain text (NOT <a>)
 *  - Grammar link is a button (opens in new tab)
 *  - Uses global $path_grammar_point
 ******************************************************/


/******************************************************
 *  PAGE
 ******************************************************/
function build_html_your_bookmarks_page(int $int_selected_language): string
{
    global
        $search_scope_current_user,
        $bookmark_filter_active;

    $room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');

    $data = get_data_bookmarks(
        (int)$search_scope_current_user,
        $room_unique_code,
        (int)$bookmark_filter_active,
        (int)$int_selected_language
    );

    return build_html_bookmark_manage_page_common(
        'your_bookmarks',
        $data,
        (int)$int_selected_language
    );
}


function build_html_room_bookmarks_page(int $int_selected_language): string
{
    global
		$search_scope_current_user,
        $search_scope_room_owner_user,
        $bookmark_filter_active;

    $room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');

    if ($room_unique_code === '') {
        return build_html_bookmark_manage_page_no_room_selected((int)$int_selected_language);
    }

    // 1) 教室共有の一覧（表示する文法一覧）
    $data_room = get_data_bookmarks(
        (int)$search_scope_room_owner_user,
        $room_unique_code,
        (int)$bookmark_filter_active,
        (int)$int_selected_language
    );

    // 2) 自分のスター状態（同じ room_unique_code を渡しておく）
    $data_user = get_data_bookmarks(
        (int)$search_scope_current_user,
        $room_unique_code,
        (int)$bookmark_filter_active,
        (int)$int_selected_language
    );

    // 3) 合成：一覧は room、スター状態は user
    $data_room['map_grammar_unique_code'] = $data_user['map_grammar_unique_code'] ?? [];

    return build_html_bookmark_manage_page_common(
        'room_bookmarks',
        $data_room,
        (int)$int_selected_language
    );
}



function build_html_bookmark_activity_page(int $int_selected_language): string
{
    global
        $search_scope_current_user,
        $bookmark_filter_inactive;

    $room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');

    $data = get_data_bookmarks(
        (int)$search_scope_current_user,
        $room_unique_code,
        (int)$bookmark_filter_inactive,
        (int)$int_selected_language
    );

    return build_html_bookmark_manage_page_common(
        'bookmark_activity',
        $data,
        (int)$int_selected_language
    );
}


/******************************************************
 *  PAGE COMMON
 ******************************************************/
function build_html_bookmark_manage_page_common(
    string $page_key,
    array $data,
    int $int_selected_language
): string {

    $arr_messages = get_arr_messages_dashboard_bookmark_manage();

    $title = (string)($arr_messages['page_title'][$page_key][$int_selected_language] ?? 'Bookmarks');
    $desc  = (string)($arr_messages['page_desc'][$page_key][$int_selected_language] ?? '');

    $items = $data['arr_user_bookmarks'] ?? [];
    $map_grammar_unique_code = $data['map_grammar_unique_code'] ?? [];
    $room_unique_code = (string)($data['room_unique_code'] ?? '');

	if ($page_key === 'room_bookmarks' && !empty($items)) {
		$items = apply_deduplicate_bookmark_items_by_grammar_unique_code($items);
	}


    $html = '';
    $html .= '<section class="jwsSection bookmarkManageSection">';
    $html .= '<div class="jwsContainer">';

    $html .= '<header>';
    $html .= '<h1>' . escape_html($title) . '</h1>';
    if ($desc !== '') {
        $html .= '<p>' . escape_html($desc) . '</p>';
    }
    $html .= '</header>';

    if (empty($items)) {
        $html .= build_html_bookmark_manage_empty_state($page_key, $int_selected_language);
        $html .= '</div></section>';
        return $html;
    }

    $html .= '<div class="jwsCardContainer bookmarkManageCardContainer">';
    $html .= build_html_bookmark_manage_cards(
        $items,
        $map_grammar_unique_code,
        $room_unique_code,
        $int_selected_language
    );
    $html .= '</div>';

    $html .= '</div></section>';

    return $html;
}


function build_html_bookmark_manage_page_no_room_selected(int $int_selected_language): string
{
    $arr_messages = get_arr_messages_dashboard_bookmark_manage();

    $title = (string)($arr_messages['page_title']['room_bookmarks'][$int_selected_language] ?? '');
    $message = (string)($arr_messages['room_required'][$int_selected_language] ?? '');

    return
        '<section class="jwsSection bookmarkManageSection">
            <div class="jwsContainer">
                <header>
                    <h1>' . escape_html($title) . '</h1>
                    <p>' . escape_html($message) . '</p>
                </header>
            </div>
        </section>';
}


/******************************************************
 *  CARDS
 ******************************************************/
function build_html_bookmark_manage_cards(
    array $items,
    array $map_grammar_unique_code,
    string $room_unique_code,
    int $int_selected_language
): string {

    global
        $path_grammar_point,
        $workshop_trial_unique_code,
        $str_snake_to_camel_japanese,
        $str_snake_to_camel_root_example;

    $arr_messages = get_arr_messages_dashboard_bookmark_manage();
    $label_open = (string)($arr_messages['btn_open_grammar'][$int_selected_language] ?? '');

    $cards = '';

	$url_grammar_point = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_grammar_point, '/'))
	);

    foreach ($items as $it) {

        $grammar_unique_code = (string)($it['unique_code'] ?? '');
        if ($grammar_unique_code === '') {
            continue;
        }

        $title = escape_html_with_nl2br($it[$str_snake_to_camel_japanese] ?? '');

        $example_raw = (string)($it[$str_snake_to_camel_root_example] ?? '');
        $example = apply_original_tags_to_html(
            escape_html_with_nl2br($example_raw)
        );

		$url_open = add_query_arg(
			'grammar_unique_code',
			$grammar_unique_code,
			$url_grammar_point
		);

        $is_bookmarked =
			isset($map_grammar_unique_code[$grammar_unique_code]) &&
			empty($map_grammar_unique_code[$grammar_unique_code]['deleted_at']);


        $unique_id = (string)($it['id'] ?? '');
        if ($unique_id === '') {
            $unique_id = uniqid();
        }

        $html_bookmark_star = '';
        if (
            $room_unique_code !== '' &&
            $room_unique_code !== $workshop_trial_unique_code
        ) {
            $html_bookmark_star = build_html_bookmark_star(
                $unique_id,
                $grammar_unique_code,
                (bool)$is_bookmarked,
                $room_unique_code
            );
        }

        $cards .= '<div class="jwsCard bookmarkManageCard" data-unique-code="' . escape_html($grammar_unique_code) . '">';

		
		/* Header : ★ → Title */
		$cards .= '<div class="bookmarkManageCardHeader">';
		$cards .= '<div class="bookmarkManageCardStar">' . $html_bookmark_star . '</div>';

		$cards .= '<div class="bookmarkManageCardTitleArea">';
		$cards .= '<div class="bookmarkManageCardTitle">' . $title . '</div>';
		$cards .= '</div>'; // bookmarkManageCardTitleArea

		$cards .= '</div>'; // bookmarkManageCardHeader

		/* Example */
		if ($example !== '') {
			$cards .= '<div class="bookmarkManageCardExample">' . $example . '</div>';
		}

		/* Saved in: （Example の後） */
		// $context_name = (string)($it['bookmark_context_name'] ?? '');
		// if ($context_name !== '') {
		// 	$cards .= '<div class="bookmarkManageCardMeta">';
		// 	$cards .= escape_html('Saved in: ' . $context_name);
		// 	$cards .= '</div>';
		// }


        /* Footer : Open grammar (new tab) */
        $cards .= '<div class="bookmarkManageCardFooter">';
        $cards .= '<a class="jwsAction bookmarkManageCardOpenLink"'
            . ' href="' . escape_html($url_open) . '"'
            . ' target="_blank"'
            . ' rel="noopener noreferrer">';
        $cards .= escape_html($label_open);
        $cards .= '</a>';
        $cards .= '</div>';

        $cards .= '</div>';
    }

    return $cards;
}


/******************************************************
 *  EMPTY
 ******************************************************/
function build_html_bookmark_manage_empty_state(string $page_key, int $int_selected_language): string
{
    $arr_messages = get_arr_messages_dashboard_bookmark_manage();

    $message = (string)($arr_messages['empty'][$page_key][$int_selected_language] ?? '');

    return '<div class="jwsCard bookmarkManageEmptyCard"><p>' . escape_html($message) . '</p></div>';
}


/******************************************************
 *  MESSAGES
 ******************************************************/
function get_arr_messages_dashboard_bookmark_manage(): array
{
    return [

        'page_title' => [
            'your_bookmarks' => [
                'Your bookmarks',
                '我的書籤',
                'Your bookmarks',
            ],
            'room_bookmarks' => [
                'Room bookmarks',
                '教室書籤',
                'Room bookmarks',
            ],
            'bookmark_activity' => [
                'History',
                '履歷',
                'History',
            ],
        ],

        'page_desc' => [
            'your_bookmarks' => [
                '登録済みの文法を管理できます。',
                '管理已加入的文法。',
                'Manage your saved grammar.',
            ],
            'room_bookmarks' => [
                '教室共有の文法を管理できます。',
                '管理教室共享的文法。',
                'Manage room shared grammar.',
            ],
            'bookmark_activity' => [
                '削除したブックマークの履歴です。',
                '已刪除書籤的履歷。',
                'Deleted bookmark history.',
            ],
        ],

        'btn_open_grammar' => [
            '文法を見る',
            '查看文法',
            'Open grammar',
        ],

        'room_required' => [
            'ルームが選択されていません。',
            '尚未選擇教室。',
            'No room selected.',
        ],

        'empty' => [
            'your_bookmarks' => [
                'ブックマークがありません。',
                '目前沒有書籤。',
                'No bookmarks yet.',
            ],
            'room_bookmarks' => [
                '教室共有のブックマークがありません。',
                '目前沒有教室共享書籤。',
                'No room bookmarks yet.',
            ],
            'bookmark_activity' => [
                '履歴がありません。',
                '目前沒有刪除履歷。',
                'No history yet.',
            ],
        ],
    ];
}

function apply_deduplicate_bookmark_items_by_grammar_unique_code(array $items): array
{
    $map_unique = [];

    foreach ($items as $row) {

        $grammar_unique_code = (string)($row['unique_code'] ?? '');
        if ($grammar_unique_code === '') {
            continue;
        }

        if (isset($map_unique[$grammar_unique_code])) {
            continue;
        }

        $map_unique[$grammar_unique_code] = $row;
    }

    return array_values($map_unique);
}
