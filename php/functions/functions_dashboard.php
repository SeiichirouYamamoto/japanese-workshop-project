<?php


/******************************************************
 *  PAGE
 *  
 ******************************************************/
function build_html_grammar_view_for_administrators_page($unique_code, $int_selected_language){
	
	global
		$t_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$int_used_language_jpn,
		$arr_columns_masta_japanese_root;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id'],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM $t_masta_japanese_root";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_root,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	
	
	// デバッグ tempo
	debug('a',$arr_masta_japanese_root);
	return;
	// デバッグ tempo
}


function build_html_dashboard_page($int_selected_language)
{
	
	global
		$path_workshop,
		$path_quizzes,
		$path_history,
		$path_your_bookmarks,
		$path_room_bookmarks,
		$path_bookmark_activity,
		$path_homework,
		$path_lesson_memos,
		$path_account,
		$path_membership,
		$path_contact,
		$workshop_trial_unique_code,
		$workshop_no_room_unique_code,
		$int_Free_Member,
		$int_Basic_Student,
		$int_Plus_Student,
		$int_Premium_Student,
		$int_Basic_Teacher;

	$user_level = get_user_level();

	if(session_status() !== PHP_SESSION_ACTIVE){
		session_start();
	}

	if (!isset($_SESSION['dashboard']) || !is_array($_SESSION['dashboard'])) {
		$_SESSION['dashboard'] = [];
	}

	if (!isset($_SESSION['dashboard']['room_unique_code'])) {
		$_SESSION['dashboard']['room_unique_code'] = '';
	}

    $arr_messages = [

        'section_current' => [
            'Current lessons',
            '目前學習',
            'Current lessons',
        ],
        'section_study' => [
            'Study',
            '學習',
            'Study',
        ],
        'section_bookmark' => [
            'Bookmarks',
            '書籤',
            'Bookmarks',
        ],
        'section_homework' => [
            'Homework',
            '作業',
            'Homework',
        ],
		'section_lesson_memos' => [
			'Lesson memos',
			'課堂備忘',
			'Lesson memos',
		],
        'section_settings' => [
            'Settings',
            '設定',
            'Settings',
        ],

        'card_workshop' => [
            'Workshop',
            'Workshop',
            'Workshop',
        ],
        'card_quizzes' => [
            'Quizzes',
            'Quizzes',
            'Quizzes',
        ],
		'card_history' => [
			'History',
			'學習紀錄',
			'History',
		],
        'card_your_bookmarks' => [
			'Your bookmarks',
			'Your bookmarks',
			'Your bookmarks',
		],
		'card_room_bookmarks' => [
			'Class bookmarks',
			'Class bookmarks',
			'Class bookmarks',
		],
        'card_bookmark_activity' => [
            'Activity',
            'Activity',
            'Activity',
        ],
        'card_homework' => [
            'Homework',
            '作業',
            'Homework',
        ],
		'card_lesson_memos' => [
			"Lesson memos",
			'課堂備忘',
			"Lesson memos",
		],
        'card_account' => [
            'Account',
            '帳戶',
            'Account',
        ],
        'card_membership' => [
			'有料会員',
			'付費會員',
			'Paid Membership',
		],
        'card_contact' => [
            'Contact',
            '聯絡',
            'Contact',
        ],
        'card_room' => [
            'Class',
            '教室',
            'Class',
        ],

        'desc_workshop' => [
            'レッスンを選んで学習を進めます。',
            '選擇課程並進行學習。',
            'Choose lessons and continue learning.',
        ],
        'desc_quizzes' => [
            'クイズで理解度を確認します。',
            '透過測驗確認理解程度。',
            'Check your understanding with quizzes.',
        ],
		'desc_history' => [
			'これまでの学習履歴を確認します。',
			'查看你的學習紀錄。',
			'View your learning history.',
		],
        'desc_your_bookmarks' => [
			'自分が保存したブックマークを開きます。',
			'開啟你自己保存的書籤。',
			'Open your personal bookmarks.',
		],
		'desc_room_bookmarks' => [
			'教室共有のブックマークを開きます。',
			'開啟教室共享書籤。',
			'Open class shared bookmarks.',
		],
        'desc_bookmark_activity' => [
            '削除した（復元可能な）ブックマークの一覧を開きます。',
            '開啟已刪除（可復原）的書籤列表。',
            'Open deleted (restorable) bookmarks.',
        ],
        'desc_homework' => [
            '宿題を確認します。',
            '查看作業。',
            'Check your homework.',
        ],
		'desc_lesson_memos' => [
			'レッスンメモは History に移動しました。今後は History からご確認ください。',
			'課堂備忘已移動至 History。請改從 History 查看。',
			'Lesson memos have moved to History. Please access them from there.',
		],
        'desc_room' => [
            '学習するルームを変更します。',
            '變更學習教室。',
            'Change your learning room.',
        ],
        'desc_account' => [
			'アカウント情報を管理します。',
			'管理帳號資訊。',
			'Manage your account information.',
		],
        'desc_membership' => [
			'有料会員プランの内容を確認・変更します。',
			'查看並變更付費會員方案。',
			'View and manage your paid membership plans.',
		],
        'desc_contact' => [
			'ご不明な点はお問い合わせください。',
			'如有疑問，請聯絡我們。',
			'If you have any questions, please contact us.',
		],
        'desc_coming_soon' => [
            '現在準備中です。',
            '目前正在準備中。',
            'Coming soon.',
        ],

        'btn_open' => [
            '開く',
            '開啟',
            'Open',
        ],
        'btn_coming_soon' => [
            '準備中',
            '準備中',
            'Coming soon',
        ],
        'btn_change' => [
            '変更',
            '變更',
            'Change',
        ],
    ];

    $arr_sections = [
        [
            'section_class' => 'studySection',
            'title_key' => 'section_study',
            'items' => [
                [
                    'label_key' => 'card_workshop',
                    'desc_key' => 'desc_workshop',
                    'button_key' => 'btn_open',
                    'href' => $path_workshop,
                    'is_enabled' => true,
                    'element_type' => 'link',
                ],
                [
                    'label_key' => 'card_quizzes',
                    'desc_key' => 'desc_quizzes',
                    'button_key' => 'btn_open',
                    'href' => $path_quizzes,
                    'is_enabled' => true,
                    'element_type' => 'link',
                ],
				[
					'label_key' => 'card_history',
					'desc_key' => 'desc_history',
					'button_key' => 'btn_open',
					'href' => $path_history,
					'is_enabled' => true,
					'element_type' => 'link',
					'card_id' => 'dashboardHistoryCard',
				],
            ],
        ],
        [
			'section_class' => 'bookmarkSection',
			'title_key' => 'section_bookmark',
			'items' => [
				[
					'label_key' => 'card_your_bookmarks',
					'desc_key' => 'desc_your_bookmarks',
					'button_key' => 'btn_open',
                    'href' => $path_your_bookmarks,
                    'is_enabled' => ((int)$user_level < (int)$int_Basic_Teacher),
                    'element_type' => 'link',
				],
				[
					'label_key' => 'card_room_bookmarks',
					'desc_key' => 'desc_room_bookmarks',
					'button_key' => 'btn_open',
                    'href' => $path_room_bookmarks,
                    'is_enabled' => true,
                    'element_type' => 'link',
					'requires_room_mode' => true,
				],
				[
					'label_key' => 'card_bookmark_activity',
					'desc_key' => 'desc_bookmark_activity',
					'button_key' => 'btn_open',
                    'href' => $path_bookmark_activity,
                    'is_enabled' => ((int)$user_level < (int)$int_Basic_Teacher),
                    'element_type' => 'link',
				],
			],
		],
        [
            'section_class' => 'homeworkSection',
            'title_key' => 'section_homework',
            'items' => [
                [
					'label_key' => 'card_homework',
					'desc_key' => 'desc_homework',
					'button_key' => 'btn_open',
					'href' => $path_homework,
					'is_enabled' => true,
					'element_type' => 'link',
				],
            ],
        ],
        [
            'section_class' => 'lessonMemosSection',
            'title_key' => 'section_lesson_memos',
            'items' => [
				[
					'label_key' => 'card_lesson_memos',
					'desc_key' => 'desc_lesson_memos',
					'button_key' => 'btn_open',
					'is_enabled' => true,
					'element_type' => 'button',
					'action' => 'dashboard:scroll_to_history',
				],
            ],
        ],
        [
            'section_class' => 'settingSection',
            'title_key' => 'section_settings',
            'items' => [
                [
                    'label_key' => 'card_room',
                    'desc_key' => 'desc_room',
                    'button_key' => 'btn_change',
                    'is_enabled' => true,
                    'element_type' => 'button',
                    'action' => 'dashboard:show_room_select_overlay',
                    'requires_multi_rooms' => true,
                ],
                [
					'label_key' => 'card_account',
					'desc_key' => 'desc_account',
					'button_key' => 'btn_open',
					'href' => $path_account,
					'is_enabled' => true,
					'element_type' => 'link',
				],
				[
					'label_key' => 'card_membership',
					'desc_key' => 'desc_membership',
					'button_key' => 'btn_open',
					'href' => $path_membership,
					'is_enabled' => ((int)$user_level < (int)$int_Premium_Student),
					'element_type' => 'link',
				],
                [
					'label_key' => 'card_contact',
					'desc_key' => 'desc_contact',
					'button_key' => 'btn_open',
					'href' => $path_contact,
					'is_enabled' => true,
					'element_type' => 'link',
				],
            ],
        ],
    ];

	$arr_sections = get_arr_dashboard_sections_by_user_level(
		$arr_sections,
		(int)$user_level,
		(int)$int_Free_Member,
		(int)$int_Basic_Student,
		(int)$int_Plus_Student,
		(int)$int_Premium_Student
	);

    $html = '';

    /*
     * 1. 学習中のレッスンへのリンク section
     */

	if ((int)$user_level === (int)$int_Free_Member) {
		$_SESSION['dashboard']['room_unique_code'] = (string)$workshop_trial_unique_code;
		$room_context = get_data_room_context_for_fixed_room_unique_code((string)$workshop_trial_unique_code);
	} elseif (
		(int)$user_level === (int)$int_Basic_Student ||
		(int)$user_level === (int)$int_Plus_Student
	) {
		$_SESSION['dashboard']['room_unique_code'] = (string)$workshop_no_room_unique_code;
		$room_context = get_data_room_context_for_fixed_room_unique_code((string)$workshop_no_room_unique_code);
	} else {
		if (
			isset($_SESSION['dashboard']['room_unique_code']) &&
			(
				$_SESSION['dashboard']['room_unique_code'] === (string)$workshop_trial_unique_code ||
				$_SESSION['dashboard']['room_unique_code'] === (string)$workshop_no_room_unique_code
			)
		) {
			$_SESSION['dashboard']['room_unique_code'] = '';
		}
		$room_context = get_data_room_context_for_dashboard((int)$int_selected_language);
	}

	$room_unique_code = escape_html((string)$room_context['room_unique_code']);
	$arr_current_lessons = get_arr_lessons_for_dashboard_current((int)$int_selected_language);

    if (
		(int)$user_level >= (int)$int_Basic_Student &&
		$room_unique_code !== '' &&
		!empty($arr_current_lessons)
	) {

        $html .= '<section class="jwsSection currentLessonSection">';
        $html .= '<div class="jwsContainer">';

        $html .= '<header>';
        $html .= '<h1>' . escape_html($arr_messages['section_current'][$int_selected_language]) . '</h1>';
        $html .= '</header>';

        $html .= '<div class="jwsCardContainer">';
        $html .= build_html_workshop_lesson_cards(
            $arr_current_lessons,
            $room_unique_code,
            $int_selected_language,
			true
        );
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</section>';
    }

    /*
     * 2〜6. その他セクション
     */
    foreach ($arr_sections as $section) {

        $html .= '<section class="jwsSection ' . escape_html($section['section_class']) . '">';
        $html .= '<div class="jwsContainer">';

        $html .= '<header>';
        $html .= '<h1>' . escape_html($arr_messages[$section['title_key']][$int_selected_language]) . '</h1>';
        $html .= '</header>';

        $html .= '<div class="jwsCardContainer">';

        foreach ($section['items'] as $item) {

			if (!empty($item['requires_multi_rooms'])) {

				$has_multiple_rooms = (
					isset($room_context['room_count']) &&
					(int)$room_context['room_count'] >= 2
				);

				if (!$has_multiple_rooms) {
					continue; // カードごと出力しない
				}
			}

			// デバッグ 検証中 ここから
			if (!$item['is_enabled']) {
				continue;
			}
			// デバッグ 検証中 ここまで

			if (!empty($item['requires_room_mode'])) {

				$is_fixed_room = (
					isset($room_context['room_unique_code']) &&
					(
						(string)$room_context['room_unique_code'] === (string)$workshop_trial_unique_code ||
						(string)$room_context['room_unique_code'] === (string)$workshop_no_room_unique_code
					)
				);

				if ($is_fixed_room) {
					continue;
				}
			}

            $card_id = isset($item['card_id']) ? (string)$item['card_id'] : '';
			$card_id_attr = $card_id !== '' ? ' id="' . escape_html($card_id) . '"' : '';

			$html .= '<div class="jwsCard"' . $card_id_attr . '>';

            $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages[$item['label_key']][$int_selected_language]) . '</div>';
            $html .= '<p>' . escape_html($arr_messages[$item['desc_key']][$int_selected_language]) . '</p>';

            if ($item['is_enabled']) {

                $element_type = isset($item['element_type']) ? (string)$item['element_type'] : 'link';

                if ($element_type === 'button') {

                    $action = isset($item['action']) ? (string)$item['action'] : '';

                    $html .= '<button type="button" class="jwsAction" data-action="' . escape_html($action) . '">';
                    $html .= escape_html($arr_messages[$item['button_key']][$int_selected_language]);
                    $html .= '</button>';

                } else {

					$path = isset($item['href']) ? (string)$item['href'] : '';

					if ($path === '' || $path === '#') {
						$href = '#';
					} else {
						$href = get_home_url(
							get_data_blog_id_from_selected_language($int_selected_language ?? null),
							trailingslashit(ltrim($path, '/'))
						);
					}

                    $html .= '<a href="' . escape_html($href) . '" class="jwsAction" target="_blank" rel="noopener noreferrer">';
                    $html .= escape_html($arr_messages[$item['button_key']][$int_selected_language]);
                    $html .= '</a>';
                }

            } else {

                $html .= '<span class="jwsAction is-disabled" aria-disabled="true">';
                $html .= escape_html($arr_messages[$item['button_key']][$int_selected_language]);
                $html .= '</span>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</section>';
    }

    $html .= build_html_room_select_modal((int)$int_selected_language, $room_context);
	$html .= build_html_workshop_grammar_modal($int_selected_language);

    return $html;
}

function get_arr_dashboard_sections_by_user_level(
    array $arr_sections,
    int $user_level,
    int $int_Free_Member,
    int $int_Basic_Student,
    int $int_Plus_Student,
    int $int_Premium_Student
): array {

    if ($user_level === $int_Free_Member) {
        $allowed = [
            'section_study',
            'section_settings'
        ];

        return array_values(array_filter($arr_sections, function (array $section) use ($allowed): bool {
            return in_array((string)($section['title_key'] ?? ''), $allowed, true);
        }));
    }

    if (
		$user_level === $int_Basic_Student ||
		$user_level === $int_Plus_Student
	) {
        $allowed = [
            'section_study',
            'section_bookmark',
            'section_settings'
        ];

        return array_values(array_filter($arr_sections, function (array $section) use ($allowed): bool {
            return in_array((string)($section['title_key'] ?? ''), $allowed, true);
        }));
    }

    if ($user_level >= $int_Premium_Student) {
        return $arr_sections;
    }

    return $arr_sections;
}




/******************************************************
 *  HTML
 *  
 ******************************************************/


function build_html_room_select_modal($int_selected_language, $room_context){
	
	global
		$path_apply_for_classroom;

	$arr_messages = [
		'title' => [
			'ルームを選択',
			'選擇教室',
			'Select Room',
		],
		'placeholder' => [
			'ルームを選んでください',
			'請選擇教室',
			'Please select a room',
		],
		'btn_confirm' => [
			'確定',
			'確認',
			'Confirm',
		],
		'btn_close' => [
			'閉じる',
			'關閉',
			'Close',
		],
		'no_room_title' => [
			'参加できるルームがありません',
			'目前沒有可加入的教室',
			'No rooms available',
		],
		'no_room_desc' => [
			'クラスルーム申請を行ってください。',
			'請先申請加入教室。',
			'Please apply for a classroom first.',
		],
		'btn_apply' => [
			'クラスルーム申請へ',
			'前往申請教室',
			'Apply for classroom',
		],
		'arr_pending_title' => [
			'申請は送信済みです',
			'申請已送出',
			'Application submitted',
		],
		'arr_pending_desc' => [
			'承認されるまでお待ちください。承認後、ダッシュボードでルームを選択できます。',
			'請等待審核通過。通過後可在儀表板選擇教室。',
			'Please wait for approval. After approval, you can select a room from the dashboard.',
		],
		'btn_reload' => [
			'ページを再読み込み',
			'重新整理頁面',
			'Reload page',
		],

	];

	$arr_rooms = $room_context['arr_rooms'];
	$room_count = (int)$room_context['room_count'];
	$has_session_room = (bool)$room_context['has_session_room'];
	$is_in_one_room = (bool)$room_context['is_in_one_room'];
	$is_pending = !empty($room_context['is_pending']);


	$overlay_id = 'jwsRoomSelectOverlay';
	$overlay_class = 'workshopOverlay';
	$modal_id = 'jwsRoomSelectModal';
	$modal_class = 'workshopModal';
	$select_id = 'jwsRoomSelectSelect';
	$btn_confirm_id = 'jwsRoomSelectConfirmButton';

	$url_apply_for_classroom = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_apply_for_classroom, '/'))
	);

	if (
		!$has_session_room &&
		$room_count !== 1
	) {
		$overlay_class .= ' overlay-on';
	}

	$html = '';

	$html .= '<div id="' . escape_html($overlay_id) . '" class="' . escape_html($overlay_class) . '">';
	$html .= '<div id="'.escape_html($modal_id).'" class="' . escape_html($modal_class) . '" role="dialog" aria-modal="true">';
	$html .= '<div class="jwsModalInner">';
	$html .= '<h2 class="jwsModalTitle">'.escape_html($arr_messages['title'][$int_selected_language]).'</h2>';

	if (empty($arr_rooms)) {

		if ($is_pending) {

			$html .= '<p class="jwsModalText">' . escape_html($arr_messages['arr_pending_title'][$int_selected_language]) . '</p>';
			$html .= '<p class="jwsModalText">' . escape_html($arr_messages['arr_pending_desc'][$int_selected_language]) . '</p>';

			$html .= '<div class="jwsModalButtons">';
			$html .= '<a class="jwsAction" href="' . escape_html(add_query_arg(null, null)) . '">'
				. escape_html($arr_messages['btn_reload'][$int_selected_language]) . '</a>';
			$html .= '</div>';

		} else {

			$html .= '<p class="jwsModalText">' . escape_html($arr_messages['no_room_title'][$int_selected_language]) . '</p>';
			$html .= '<p class="jwsModalText">' . escape_html($arr_messages['no_room_desc'][$int_selected_language]) . '</p>';

			$html .= '<div class="jwsModalButtons">';
			$html .= '<a class="jwsAction" href="' . escape_html($url_apply_for_classroom) . '">'
				. escape_html($arr_messages['btn_apply'][$int_selected_language]) . '</a>';
			$html .= '</div>';
		}

		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	$html .= '<select id="'.escape_html($select_id).'" name="room_unique_code" class="jwsSelect">';
	$html .= '<option value="">'.escape_html($arr_messages['placeholder'][$int_selected_language]).'</option>';

	foreach($arr_rooms as $row){

		$room_unique_code = (string)($row['unique_code'] ?? '');
		$room_name = (string)($row['room_name'] ?? '');

		if($room_unique_code === ''){
			continue;
		}

		$html .= '<option value="'.escape_html($room_unique_code).'">'.escape_html($room_name).'</option>';
	}

	$html .= '</select>';

	$html .= '<div class="jwsModalButtons">';
	$html .= '<button type="button" id="'.escape_html($btn_confirm_id).'" class="jwsAction">'.escape_html($arr_messages['btn_confirm'][$int_selected_language]).'</button>';
	$html .= '</div>';

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}


function build_html_workshop_lesson_cards(
	array $arr_lessons,
	string $room_unique_code,
	int $int_selected_language,
	bool $do_display_card_status
): string
{
    global
        $arr_learning_status,
        $int_learning_status_not_started,
        $arr_columns_masta_teaching_material_lessons,
        $arr_columns_masta_teaching_material_lesson_objectives;

    $arr_open_labels = ['開く', '查看'];

    $lesson_title_column =
        $arr_columns_masta_teaching_material_lessons[$int_selected_language]
        ?? null;

    $lesson_objective_column =
        $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]
        ?? null;

    $html = '';

    foreach ($arr_lessons as $les) {

        /* --------------------
           ids
        -------------------- */
        $card_id = (int)($les['id'] ?? 0);

        $lesson_id = isset($les['lesson_id']) && $les['lesson_id'] !== null
            ? (int)$les['lesson_id']
            : null;

        $teaching_material_lesson_id = isset($les['teaching_material_lesson_id']) && $les['teaching_material_lesson_id'] !== null
            ? (int)$les['teaching_material_lesson_id']
            : null;

        $lesson_id_attr = $lesson_id !== null ? (string)$lesson_id : '';
        $teaching_material_lesson_id_attr = $teaching_material_lesson_id !== null ? (string)$teaching_material_lesson_id : '';

        $lesson_sort = (int)($les['lesson_sort'] ?? 0);

        /* --------------------
           learning status
        -------------------- */
        $learning_status = isset($les['learning_status'])
            ? (int)$les['learning_status']
            : $int_learning_status_not_started;

        $status_info =
            $arr_learning_status[$learning_status]
            ?? $arr_learning_status[$int_learning_status_not_started];

        $status_label =
            $status_info['title'][$int_selected_language]
            ?? $status_info['title'][INDEX_FIRST];

        $status_class = $status_info['html_id_class'] ?? '';
		$html_card_status = '';
		if($do_display_card_status){
			$html_card_status = 
			'<div class="workshopLessonCardStatus wiseDecorativeItem ' . escape_html($status_class) . '">' .
				escape_html($status_label) .
			'</div>';
		}

        /* --------------------
           title / objective
        -------------------- */
        $lesson_title = 'Lesson ' . ($lesson_sort + 1);
        if ($lesson_title_column !== null && isset($les[$lesson_title_column])) {
            $lesson_title = trim((string)$les[$lesson_title_column]);
        }

        $objective = '';
        if ($lesson_objective_column !== null && isset($les[$lesson_objective_column])) {
            $objective = trim((string)$les[$lesson_objective_column]);
        }

        /* --------------------
           grammar（今回は保留）
        -------------------- */
        $grammar_titles = $les['grammar_titles'] ?? [];
        $grammar_titles = is_array($grammar_titles) ? $grammar_titles : [];

        $show = array_slice($grammar_titles, 0, 3);
        $has_more = count($grammar_titles) > 3;

        $li = [];
        foreach ($show as $g) {
            $li[] =
                '<li class="workshopLessonCardGrammarLi">▸ ' .
                escape_html((string)$g) .
                '</li>';
        }

        if ($has_more) {
            $li[] = '<li class="workshopLessonCardGrammarLi etc">etc...</li>';
        }

        /* --------------------
           render card
        -------------------- */

        $html .=
            '<article class="workshopLessonCard" data-card-id="' . $card_id . '">' .

                '<div class="workshopLessonCardTopRow wiseDecorativeItem">' .
                    $html_card_status .

					// 設定ボタンは 現在使用用途がないためコメントアウト
                    // '<button type="button"
                    //     class="workshopLessonCardSettingButton"
                    //     data-action="workshop:lesson:openSetting"
                    //     aria-label="設定">' .
                    //     '<span class="workshopLessonCardSettingIcon">⋮</span>' .
                    // '</button>' .
                '</div>' .

                '<header class="workshopLessonCardHeader">' .
                    '<div class="workshopLessonCardTitleMain">' .
                        escape_html($lesson_title) .
                    '</div>' .
                '</header>' .

                '<div class="workshopLessonCardBody">' .

                    (
                        $objective !== ''
                            ? '<div class="workshopLessonCardObjective">' .
                                escape_html($objective) .
                              '</div>'
                            : ''
                    ) .

                    (
                        !empty($li)
                            ? '<ul class="workshopLessonCardGrammarUl">' .
                                implode('', $li) .
                              '</ul>'
                            : ''
                    ) .

                '</div>' .

                '<div class="workshopLessonCardFooter">' .
                    '<button type="button"
                        class="jwsAction workshopLessonCardOpenButton"
                        data-action="workshop:lesson:start"
                        data-room-unique-code="' . escape_html($room_unique_code) . '"
                        data-lesson-id="' . escape_html($lesson_id_attr) . '"
                        data-teaching-material-lesson-id="' . escape_html($teaching_material_lesson_id_attr) . '">' .
                        escape_html(
                            $arr_open_labels[$int_selected_language]
                            ?? $arr_open_labels[INDEX_FIRST]
                        ) .
                    '</button>' .
                '</div>' .

            '</article>';
    }

    return $html;
}


function build_html_workshop_grammar_modal(int $int_selected_language): string
{
    $str_html_overlay_close_button = build_html_overlay_close_button();

    $str_title = escape_html(
        $int_selected_language === INDEX_FIRST
            ? '文法一覧'
            : '文法列表'
    );

    $str_html_screen =
        '<div id="workshopGrammarModalScreen" class="workshopModal">' .
            $str_html_overlay_close_button .
            '<h2>' . $str_title . '</h2>' .
            '<div id="workshopGrammarModalContentsContainer" class="modalScrollableContainer">' .
				build_html_loading_spinner('workshopGrammarModalLoading') .
                '<div id="workshopGrammarModalContents"></div>' .
            '</div>' .
        '</div>';

    $str_html_overlay =
        '<div id="workshopOverlayGrammar" class="workshopOverlay">' .
            $str_html_screen .
        '</div>';

    return $str_html_overlay;
}



/******************************************************
 *  TOOLS
 *  
 ******************************************************/
function fetch_arr_confirmed_rooms_for_current_user($int_selected_language){

	global
		$t_room_users,
		$t_rooms;

	$current_user = wp_get_current_user();
	$current_user_id = (int)$current_user->ID;

	$arr_strSQL_select = [
		[$t_rooms, 'id'],
		[$t_rooms, 'unique_code'],
		[$t_rooms, 'room_name'],
		[$t_rooms, 'sort'],
	];

	$strSQL_from = " FROM
					$t_rooms
					INNER JOIN $t_room_users
					ON
					$t_rooms.id = $t_room_users.room_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_users, 'user_id', '=', $current_user_id, 'PDO::PARAM_INT', 'And'],
				[$t_room_users, 'confirmed', '=', FLAG_TRUE, 'PDO::PARAM_INT', 'And'],
				[$t_rooms, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_rooms, 'sort', 'ASC'],
		[$t_rooms, 'id', 'ASC'],
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_confirmed_rooms) = execute_select_and_fetch_all(
		$arr_strSQL_select,
		$strSQL_from,
		$arr_strSQL_where,
		$arr_strSQL_order,
		$strSQL_option
	);

	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return $arr_confirmed_rooms;
}

function fetch_arr_pending_rooms_for_current_user($int_selected_language){

	global
		$t_room_users,
		$t_rooms;

	$current_user = wp_get_current_user();
	$current_user_id = (int)$current_user->ID;

	$arr_strSQL_select = [
		[$t_rooms, 'id'],
		[$t_rooms, 'unique_code'],
		[$t_rooms, 'room_name'],
		[$t_rooms, 'sort'],
	];

	$strSQL_from = " FROM
					$t_rooms
					INNER JOIN $t_room_users
					ON
					$t_rooms.id = $t_room_users.room_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_users, 'user_id', '=', $current_user_id, 'PDO::PARAM_INT', 'And'],
				[$t_room_users, 'confirmed', '=', FLAG_FALSE, 'PDO::PARAM_INT', 'And'],
				[$t_rooms, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_rooms, 'sort', 'ASC'],
		[$t_rooms, 'id', 'ASC'],
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_pending_rooms) = execute_select_and_fetch_all(
		$arr_strSQL_select,
		$strSQL_from,
		$arr_strSQL_where,
		$arr_strSQL_order,
		$strSQL_option
	);

	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return $arr_pending_rooms;
}


function get_arr_lessons_for_workshop($room_unique_code, $int_selected_language) {

    global
        $int_masta_teaching_material_set_id_jws_workshop,
        $t_teaching_material_lessons,
        $t_teaching_material_levels,
        $t_room_lessons,
        $t_user_lessons,
        $arr_columns_masta_teaching_material_lessons,
        $arr_columns_masta_teaching_material_lesson_objectives,
        $arr_columns_masta_teaching_material_levels;

	$user_level = get_user_level();
    $mode = get_data_workshop_mode($room_unique_code, $user_level);

    if ($mode === 'trial') {
        // 既存 trial のSQLをそのまま（room_id不要）
        // 戻り配列の room_id は 0 か null にするのが自然です
        $room_id = 0;

        $arr_strSQL_select = [
            [$t_teaching_material_lessons, 'id as teaching_material_lesson_id'],
            [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language] . ' as title'],
            [$t_teaching_material_lessons, 'sort as lesson_sort'],
            [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language]],
            [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]],
            [$t_teaching_material_lessons, 'level_id'],
            [$t_teaching_material_levels, $arr_columns_masta_teaching_material_levels[$int_selected_language]],
            [$t_teaching_material_levels, 'sort as level_sort']
        ];

        $strSQL_from = "
            FROM $t_teaching_material_lessons
            LEFT JOIN $t_teaching_material_levels
                ON $t_teaching_material_lessons.level_id = $t_teaching_material_levels.id
        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_teaching_material_lessons, 'is_trial', '=', FLAG_TRUE, 'PDO::PARAM_INT', 'And'],
                    [$t_teaching_material_levels, 'set_id', '=', $int_masta_teaching_material_set_id_jws_workshop, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_teaching_material_levels, 'sort', 'ASC'],
            [$t_teaching_material_lessons, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

        $arr_lessons = [];
        foreach ($rows as $i => $r) {
            $arr_lessons[] = [
                'id' => (int)$r['teaching_material_lesson_id'],
                'room_id' => $room_id,
                'lesson_id' => null,
                'teaching_material_lesson_id' => (int)$r['teaching_material_lesson_id'],
                'title' => $r['title'] ?? ($r[$arr_columns_masta_teaching_material_lessons[$int_selected_language]] ?? ('Lesson ' . ($i + 1))),
                'lesson_sort' => isset($r['lesson_sort']) ? (int)$r['lesson_sort'] : ($i + 1),
                $arr_columns_masta_teaching_material_lessons[$int_selected_language] => $r[$arr_columns_masta_teaching_material_lessons[$int_selected_language]] ?? '',
                $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language] => $r[$arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]] ?? '',
                'level_id' => $r['level_id'] ?? null,
                $arr_columns_masta_teaching_material_levels[$int_selected_language] => $r[$arr_columns_masta_teaching_material_levels[$int_selected_language]] ?? '',
                'level_sort' => isset($r['level_sort']) ? (int)$r['level_sort'] : PHP_INT_MAX
            ];
        }

        return $arr_lessons;
    }

    if ($mode === 'basic' || $mode === 'plus') {

		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;

        $room_id = 0;

        $arr_strSQL_select = [
            [$t_user_lessons, 'id'],
            [$t_user_lessons, 'user_id'],
            [$t_user_lessons, 'id as lesson_id'],
            [$t_user_lessons, 'teaching_material_lesson_id'],
            [$t_user_lessons, 'learning_status'],
            [$t_user_lessons, 'title'],
            [$t_user_lessons, 'sort as lesson_sort'],
            [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language] . ' as tm_title'],
            [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]],
            [$t_teaching_material_lessons, 'level_id'],
            [$t_teaching_material_levels, $arr_columns_masta_teaching_material_levels[$int_selected_language]],
            [$t_teaching_material_levels, 'sort as level_sort']
        ];

        $strSQL_from = "
            FROM $t_user_lessons
            LEFT JOIN $t_teaching_material_lessons
                ON $t_user_lessons.teaching_material_lesson_id = $t_teaching_material_lessons.id
            LEFT JOIN $t_teaching_material_levels
                ON $t_teaching_material_lessons.level_id = $t_teaching_material_levels.id
        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_user_lessons, 'user_id', '=', $current_user_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [
            [$t_teaching_material_levels, 'sort', 'ASC'],
            [$t_user_lessons, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

        $arr_lessons = [];
        foreach ($rows as $i => $r) {
            $arr_lessons[] = [
                'id' => (int)$r['id'],
                'room_id' => $room_id,
                'lesson_id' => (int)$r['lesson_id'],
                'teaching_material_lesson_id' => (int)$r['teaching_material_lesson_id'],
                'learning_status' => isset($r['learning_status']) ? (int)$r['learning_status'] : null,
                'title' => $r['title'] ?? ($r['tm_title'] ?? ('Lesson ' . ($i + 1))),
                'lesson_sort' => isset($r['lesson_sort']) ? (int)$r['lesson_sort'] : ($i + 1),
                $arr_columns_masta_teaching_material_lessons[$int_selected_language] => $r['tm_title'] ?? '',
                $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language] => $r[$arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]] ?? '',
                'level_id' => $r['level_id'] ?? null,
                $arr_columns_masta_teaching_material_levels[$int_selected_language] => $r[$arr_columns_masta_teaching_material_levels[$int_selected_language]] ?? '',
                'level_sort' => isset($r['level_sort']) ? (int)$r['level_sort'] : PHP_INT_MAX
            ];
        }

        return $arr_lessons;
    }

    $arr_rooms = fetch_arr_rooms_from_unique_code($room_unique_code, $int_selected_language);

    $room_id = (int)($arr_rooms[INDEX_FIRST]['id'] ?? 0);
    if ($room_id <= 0) {
        return [];
    }

    $arr_strSQL_select = [
        [$t_room_lessons, 'id'],
        [$t_room_lessons, 'room_id'],
        [$t_room_lessons, 'id as lesson_id'],
        [$t_room_lessons, 'teaching_material_lesson_id'],
        [$t_room_lessons, 'learning_status'],
        [$t_room_lessons, 'title'],
        [$t_room_lessons, 'sort as lesson_sort'],
        [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language]],
        [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]],
        [$t_teaching_material_lessons, 'level_id'],
        [$t_teaching_material_levels, $arr_columns_masta_teaching_material_levels[$int_selected_language]],
        [$t_teaching_material_levels, 'sort as level_sort']
    ];

    $strSQL_from = "
        FROM $t_room_lessons
        LEFT JOIN $t_teaching_material_lessons
            ON $t_room_lessons.teaching_material_lesson_id = $t_teaching_material_lessons.id
        LEFT JOIN $t_teaching_material_levels
            ON $t_teaching_material_lessons.level_id = $t_teaching_material_levels.id
    ";

    $arr_strSQL_where = [
        [
            [
                [$t_room_lessons, 'room_id', '=', $room_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_teaching_material_levels, 'sort', 'ASC'],
        [$t_room_lessons, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    return $arr_lessons;
}


function get_arr_lessons_for_dashboard_current(int $int_selected_language): array
{
    global
        $int_learning_status_not_started,
        $int_learning_status_learning;

    $room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');
    if ($room_unique_code === '') {
        return [];
    }

    $arr_lessons = get_arr_lessons_for_workshop(
        $room_unique_code,
        $int_selected_language
    );

    if (empty($arr_lessons)) {
        return [];
    }

    $arr_learning = array_values(array_filter(
        $arr_lessons,
        function ($lesson) use ($int_learning_status_learning) {
            return (int)($lesson['learning_status'] ?? 0) === $int_learning_status_learning;
        }
    ));

    if (!empty($arr_learning)) {
        return array_slice($arr_learning, 0, 1);
    }

    $arr_not_started = array_values(array_filter(
        $arr_lessons,
        function ($lesson) use ($int_learning_status_not_started) {
            return (int)($lesson['learning_status'] ?? 0) === $int_learning_status_not_started;
        }
    ));

    return array_slice($arr_not_started, 0, 1);
}

function get_data_room_context_for_dashboard($int_selected_language)
{
    $arr_rooms = fetch_arr_confirmed_rooms_for_current_user($int_selected_language);
    $room_count = is_array($arr_rooms) ? count($arr_rooms) : 0;

    $arr_pending_rooms = [];
    $is_pending = false;

    if ($room_count === 0) {
        $arr_pending_rooms = fetch_arr_pending_rooms_for_current_user($int_selected_language);
        $is_pending = !empty($arr_pending_rooms);
    }

    $has_session_room = (
        isset($_SESSION['dashboard']['room_unique_code']) &&
        $_SESSION['dashboard']['room_unique_code'] !== ''
    );

    $is_in_one_room = ($room_count === 1);

    if (!$has_session_room && $is_in_one_room) {
        $room_unique_code = (string)($arr_rooms[0]['unique_code'] ?? '');
        if ($room_unique_code !== '') {
            $_SESSION['dashboard']['room_unique_code'] = $room_unique_code;
            $has_session_room = true;
        }
    }

    $room_unique_code = (string)($_SESSION['dashboard']['room_unique_code'] ?? '');

    return [
        'arr_rooms' => $arr_rooms,
        'room_count' => $room_count,
        'has_session_room' => $has_session_room,
        'is_in_one_room' => $is_in_one_room,
        'room_unique_code' => $room_unique_code,
        'is_pending' => $is_pending,
        'arr_pending_rooms' => $arr_pending_rooms,
    ];
}

function get_data_room_context_for_fixed_room_unique_code(string $room_unique_code): array
{
    return [
        'arr_rooms' => [],
        'room_count' => 1,
        'has_session_room' => true,
        'is_in_one_room' => true,
        'room_unique_code' => $room_unique_code
    ];
}
