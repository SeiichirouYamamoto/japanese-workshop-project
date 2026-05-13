<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/

function build_html_homework_page(int $int_selected_language): string
{
    $arr_messages = [
        'title' => [
            '宿題',
            '作業',
            'Homework',
        ],
        'desc' => [
            '日付を選んで宿題を開きます。',
            '請選擇日期以開啟作業。',
            'Select a date to open the homework.',
        ],
        'no_homeworks' => [
            'まだ宿題がありません。',
            '尚無作業。',
            'No homework yet.',
        ],
        'btn_open' => [
            '開く',
            '開啟',
            'Open',
        ],
        'homework_modal_title' => [
            '宿題',
            '作業',
            'Homework',
        ],
        'err_room_not_selected' => [
            'ルームが選択されていません。ルームを選択してからもう一度お試しください。',
            '尚未選擇教室。請先選擇教室後再試一次。',
            'No room is selected. Please select a room and try again.',
        ],
        'err_room_not_found' => [
            'ルーム情報の取得に失敗しました。再読み込みしてもう一度お試しください。',
            '無法取得教室資訊。請重新整理後再試一次。',
            'Failed to load room information. Please reload and try again.',
        ],
    ];

    $html = '';

    // ※ lesson_memo と同じ流儀：セッションから room_unique_code を取得
    $room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');

    // エラー：room_unique_code が無い
    if ($room_unique_code === '') {

        $html .= '<section class="jwsSection homeworkSection">';
        $html .= '<div class="jwsContainer">';

        $html .= '<header>';
        $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
        $html .= '</header>';

        $html .= '<p>' . escape_html($arr_messages['err_room_not_selected'][$int_selected_language]) . '</p>';

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    $room_id = (int)fetch_room_id_from_unique_code(
        $room_unique_code,
        $int_selected_language
    );

    // エラー：room_id が取れない（存在しない / 取得失敗）
    if ($room_id <= 0) {

        $html .= '<section class="jwsSection homeworkSection">';
        $html .= '<div class="jwsContainer">';

        $html .= '<header>';
        $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
        $html .= '</header>';

        $html .= '<p>' . escape_html($arr_messages['err_room_not_found'][$int_selected_language]) . '</p>';

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    // 正常：コンテンツ作成
    $arr_homework_dates = fetch_arr_room_homework_dates_by_room_id(
        $room_id,
        $int_selected_language
    );

    $html .= '<section class="jwsSection homeworkSection">';
    $html .= '<div class="jwsContainer">';

    $html .= '<header>';
    $html .= '<h1>' . escape_html($arr_messages['title'][$int_selected_language]) . '</h1>';
    $html .= '</header>';

    $html .= '<p>' . escape_html($arr_messages['desc'][$int_selected_language]) . '</p>';

    $html .= build_html_homework_cards(
        $room_id,
        $arr_homework_dates,
        $int_selected_language,
        $arr_messages
    );

    $html .= '</div>';
    $html .= '</section>';

    $html .= build_html_homework_modal($int_selected_language, $arr_messages);

    return $html;
}


/******************************************************
 *  CARDS
 *
 ******************************************************/
function build_html_homework_cards(int $room_id, array $arr_homework_dates, int $int_selected_language, array $arr_messages): string
{
    $html = '';

    $html .= '<div class="jwsCardContainer homeworkCardContainer">';

    if (empty($arr_homework_dates)) {

        $html .= '<div class="jwsCard homeworkCard">';
        $html .= '<div class="jwsCardTitle">' . escape_html($arr_messages['no_homeworks'][$int_selected_language]) . '</div>';
        $html .= '</div>';

        $html .= '</div>';
        return $html;
    }

    foreach ($arr_homework_dates as $row) {

        $homework_id = (int)($row['id'] ?? 0);
        $target_date = (string)($row['target_date'] ?? '');

        if ($target_date === '') {
            continue;
        }

        $target_date_safe = escape_html($target_date);

        $html .= '<div class="jwsCard homeworkCard">';

        $html .= '<div class="jwsCardTitle">' . $target_date_safe . '</div>';

        // JSは後で：data-* だけ用意
        $html .= '<button type="button"
            class="jwsAction homeworkOpenButton"
            data-action="homework:show_homework_overlay"
            data-room-id="' . escape_html((string)$room_id) . '"
            data-homework-id="' . escape_html((string)$homework_id) . '"
            data-target-date="' . $target_date_safe . '"
            aria-haspopup="dialog">';
        $html .= escape_html($arr_messages['btn_open'][$int_selected_language]);
        $html .= '</button>';

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}


/******************************************************
 *  MODAL / OVERLAY
 *
 ******************************************************/
function build_html_homework_modal(int $int_selected_language, array $arr_messages): string
{
    $str_html_overlay_close_button = build_html_overlay_close_button();

    $str_title = escape_html($arr_messages['homework_modal_title'][$int_selected_language] ?? 'Homework');

    $str_html_screen =
        '<div id="homeworkModalScreen" class="workshopModal">' .
            $str_html_overlay_close_button .
            '<h2 id="homeworkModalTitle">' . $str_title . '</h2>' .
            '<div id="homeworkModalContentsContainer" class="modalScrollableContainer">' .
                build_html_loading_spinner('homeworkModalLoading') .
                '<div id="homeworkModalContents"></div>' .
            '</div>' .
        '</div>';

    $str_html_overlay =
        '<div id="homeworkOverlay" class="workshopOverlay">' .
            $str_html_screen .
        '</div>';

    return $str_html_overlay;
}


/******************************************************
 *  DB
 *
 ******************************************************/
function fetch_arr_room_homework_dates_by_room_id(int $room_id, int $int_selected_language): array
{
    global $t_room_homeworks;

    if ($room_id <= 0) {
        return [];
    }

    $arr_strSQL_select = [
        [$t_room_homeworks, 'id'],
        [$t_room_homeworks, 'target_date'],
        [$t_room_homeworks, 'updated_at'],
    ];

    $strSQL_from = ' FROM ' . $t_room_homeworks;

    $arr_strSQL_where = [
        [
            [
                [$t_room_homeworks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_room_homeworks, 'target_date', 'DESC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (!is_array($arr_rows)) {
        return [];
    }

    return $arr_rows;
}


/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_homework_modal_contents(array $arr_homework, int $int_selected_language): string
{
    global
        $arr_form_list_default,
        $str_homework_method_inputData,
        $str_homework_method_activeRecall,
        $str_homework_method_registeredSentences,
        $str_homework_method_registeredSentencesAdvanced,
        $str_homework_method_randomSentences,
        $str_homework_method_randomSentencesAdvanced,
        $str_homework_method_japaneseParticleQuiz,
        $str_homework_method_sortingQuiz,
        $str_homework_method_preparation,
        $str_homework_method_inflection,
        $str_homework_method_plainform,
        $int_quiz_japaneseParticleQuiz,
        $int_quiz_sortingQuiz,
        $path_grammar_point;

    $str_html = '';

    $room_id = (int)($arr_homework['room_id'] ?? 0);
    $target_date = (string)($arr_homework['target_date'] ?? '');

    if ($room_id <= 0 || $target_date === '') {
        return $str_html;
    }

    $json_form_list = (string)($arr_homework['form_list_json'] ?? '');
    $json_form_list = html_entity_decode($json_form_list, ENT_QUOTES, 'UTF-8');
    $json_form_list = trim($json_form_list);
    $json_form_list = preg_replace('/^\xEF\xBB\xBF/', '', $json_form_list);

    $arr_form_list = json_decode($json_form_list, true);
    if (!is_array($arr_form_list)) {
        $arr_form_list = [];
    }

    foreach ($arr_form_list_default as $default_id) {
        if (!in_array($default_id, $arr_form_list)) {
            $arr_form_list[] = $default_id;
        }
    }

    $json_form_list = json_encode($arr_form_list, JSON_UNESCAPED_UNICODE);
    $data_form_list = escape_html($json_form_list, ENT_QUOTES, 'UTF-8');

    $json_status_grouped = (string)($arr_homework['content_json'] ?? '');
    $json_status_grouped = html_entity_decode($json_status_grouped, ENT_QUOTES, 'UTF-8');
    $json_status_grouped = trim($json_status_grouped);
    $json_status_grouped = preg_replace('/^\xEF\xBB\xBF/', '', $json_status_grouped);

    $arr_status_grouped = json_decode($json_status_grouped, true);
    if (!is_array($arr_status_grouped) || empty($arr_status_grouped)) {
        return $str_html;
    }

    $header = '<h1>' . escape_html($target_date) . '</h1>';
    $str_html_contents = '';
    $str_html_content_div_class = 'homeworkContentContainer';

    foreach ($arr_status_grouped as $type => $items) {

        if (!is_array($items) || empty($items)) {
            continue;
        }

        $str_html_content_title = '';
        $str_html_content = '';

        switch ($type) {

            case $str_homework_method_inputData:
                $str_html_content_title = 'Your Input Data';
                $str_html_content = build_html_homework_input_data($room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_activeRecall:
                $str_html_content_title = 'Active Recall';
                $str_html_content = build_html_homework_active_recall($room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_registeredSentences:
                $str_html_content_title = 'Check Sentences';
                $isAdvanceStage = false;
                $str_html_content = build_html_homework_registered_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_registeredSentencesAdvanced:
                $str_html_content_title = 'Check Sentences Advanced';
                $isAdvanceStage = true;
                $str_html_content = build_html_homework_registered_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_randomSentences:
                $str_html_content_title = 'Random Sentences';
                $isAdvanceStage = false;
                $str_html_content = build_html_homework_random_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_randomSentencesAdvanced:
                $str_html_content_title = 'Random Sentences Advanced';
                $isAdvanceStage = true;
                $str_html_content = build_html_homework_random_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language);
                break;

            case $str_homework_method_japaneseParticleQuiz:
                $str_html_content_title = 'Japanese Particle Quiz';
                $str_html_content = build_html_homework_quiz_link_from_grammar_unique_codes($int_quiz_japaneseParticleQuiz, $items, $int_selected_language);
                break;

            case $str_homework_method_sortingQuiz:
                $str_html_content_title = 'Sorting Quiz';
                $str_html_content = build_html_homework_quiz_link_from_grammar_unique_codes($int_quiz_sortingQuiz, $items, $int_selected_language);
                break;

            case $str_homework_method_preparation:
                $str_html_content_title = 'Preparation';
                $str_html_content = build_html_homework_grammar_view_links($path_grammar_point, $items, $int_selected_language);
                break;

            case $str_homework_method_inflection:
                $str_html_content_title = 'Check Inflections';
                $str_html_content = build_html_homework_word_inflection_quiz_link($items, $int_selected_language);
                break;

            case $str_homework_method_plainform:
                $str_html_content_title = 'Check Plainforms';
                $str_html_content = build_html_homework_plainform_quiz_link($items, $int_selected_language);
                break;

            default:
                continue 2;
        }

        if (trim($str_html_content) === '') {
            continue;
        }

        $str_html_content_header = '<h3>' . $str_html_content_title . '</h3>';
        $str_html_content_div = '<div class="' . $str_html_content_div_class . '">' . $str_html_content_header . $str_html_content . '</div>';
        $str_html_contents .= $str_html_content_div;
    }

    $str_html_section = '<section class="homeworkContentsSection">' . $header . $str_html_contents . '</section>';
    $str_html .= $str_html_section;

    return $str_html;
}

function build_html_homework_input_data($room_id, $data_form_list, $items, $int_selected_language){

	global
		$t_room_user_input_data,
		$arr_columns_masta_japanese_root;

	$str_html_inputData = '';
	$arr_target = [];

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = escape_html($item_grammar_unique_code);
		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_from_unique_code($item_grammar_unique_code, $int_selected_language);

		if(empty($arr_masta_japanese_root)){
			continue;
		};

		$t_masta_japanese_root_id = $arr_masta_japanese_root[INDEX_FIRST]['id'];
		$heading = escape_html_with_nl2br($arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]]);	

		$arr_strSQL_select = [
			[$t_room_user_input_data,'input_data as foreignLanguageText']
		];
		
		$strSQL_from = " FROM $t_room_user_input_data";
	
		$arr_strSQL_where = [
			[
				[
					[$t_room_user_input_data,'room_id','=',$room_id,'PDO::PARAM_INT','And'],
					[$t_room_user_input_data,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
				],
				''
			]
		];
	
		$arr_strSQL_order = [
			[$t_room_user_input_data,'sort','ASC']
		];
	
		$strSQL_option = '';
	
		list($pdo_has_error, $select_has_error, $e, $arr_room_user_input_data) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		if ($pdo_has_error == FLAG_TRUE || $select_has_error == FLAG_TRUE || $e !== null) {
			continue;
		}
	
		if(!empty($arr_room_user_input_data)){
			$arr_target[] = [
				'add_button' => false,
				'advance_stage' => false,
				'heading' => $heading,
				'array' => $arr_room_user_input_data
			];
		}
	}

	$str_html_details = build_html_homework_details_sections($arr_target, $data_form_list, $int_selected_language);
	$str_html_inputData = $str_html_details;

	return $str_html_inputData;
}


function build_html_homework_active_recall($room_id, $data_form_list, $items, $int_selected_language){

	global
		$arr_columns_masta_japanese_root;

	$str_html_activeRecall = '';
	$arr_target = [];

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = escape_html($item_grammar_unique_code);
		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_from_unique_code($item_grammar_unique_code, $int_selected_language);

		if(empty($arr_masta_japanese_root)){
			continue;
		};

		$t_masta_japanese_root_id = $arr_masta_japanese_root[INDEX_FIRST]['id'];
		$heading = escape_html_with_nl2br($arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]]);
			
		$arr_active_recall = fetch_arr_active_recall($t_masta_japanese_root_id, $int_selected_language);
	
		if(!empty($arr_active_recall)){
			$arr_target[] = [
				'add_button' => false,
				'advance_stage' => false,
				'heading' => $heading,
				'array' => $arr_active_recall
			];
		}
	}

	$str_html_details = build_html_homework_details_sections($arr_target, $data_form_list, $int_selected_language);
	$str_html_activeRecall = $str_html_details;

	return $str_html_activeRecall;
}


function build_html_homework_registered_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language){

	global
		$arr_columns_masta_japanese_root,
		$t_registered_sentences;

	$str_html_registeredSentence = '';
	$arr_target = [];

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = escape_html($item_grammar_unique_code);
		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_from_unique_code($item_grammar_unique_code, $int_selected_language);

		if(empty($arr_masta_japanese_root)){
			continue;
		};

		$t_masta_japanese_root_id = $arr_masta_japanese_root[INDEX_FIRST]['id'];
		$heading = escape_html_with_nl2br($arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]]);

		$arr_search_condition_t_masta_japanese_root_id = [
			[
				[
					[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','And'],
					[$t_registered_sentences,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
				],
				''
			]
		];
		$arr_registered_sentences = get_arr_registered_sentences_with_multilingual_text($arr_search_condition_t_masta_japanese_root_id, $int_selected_language);
	
		if(!empty($arr_registered_sentences)){
			$arr_target[] = [
				'add_button' => true,
				'advance_stage' => $isAdvanceStage,
				'heading' => $heading,
				'array' => $arr_registered_sentences
			];
		}
	}

	$str_html_details = build_html_homework_details_sections($arr_target, $data_form_list, $int_selected_language);
	$str_html_registeredSentence = $str_html_details;

	return $str_html_registeredSentence;
}


function build_html_homework_random_sentences($isAdvanceStage, $room_id, $data_form_list, $items, $int_selected_language){

	global
		$t_registered_sentences;

	$str_html_randomSentences = '';
	$arr_target = [];
	$arr_search_condition_base = [];

	foreach ($items as $index => $item_grammar_unique_code) {
		$t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($item_grammar_unique_code, $int_selected_language);
		// 未定義id
		if($t_masta_japanese_root_id === 0){
			continue;
		};
		$condition = [$t_registered_sentences,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','Or'];
		if ($index === count($items) - 1) {
			$condition[INDEX_SIXTH] = '';
		}
		$arr_search_condition_base[] = $condition;
	}
	$arr_search_condition_grammar_unique_code = [
		[
			[
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			$arr_search_condition_base,
			''
		]
	];

	// マジックナンバー
	$heading = 'Random Sentences';
	$arr_registered_sentences = get_arr_registered_sentences_with_multilingual_text($arr_search_condition_grammar_unique_code, $int_selected_language);

	if(!empty($arr_registered_sentences)){
		$arr_target[] = [
			'add_button' => true,
			'advance_stage' => $isAdvanceStage,
			'heading' => $heading,
			'array' => $arr_registered_sentences
		];
	}

	$str_html_details = build_html_homework_details_sections($arr_target, $data_form_list, $int_selected_language);
	$str_html_randomSentences = $str_html_details;

	return $str_html_randomSentences;
}


function build_html_homework_grammar_view_links($str_path, $items, $int_selected_language){

	global
        $arr_columns_masta_japanese_root;

	$str_html_a = '';
	$str_li = '';
	
	$url_base = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($str_path, '/'))
	);

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = (string)$item_grammar_unique_code;

		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_from_unique_code(
			$item_grammar_unique_code,
			$int_selected_language
		);

		if(empty($arr_masta_japanese_root)){
			continue;
		}

		$str_url = add_query_arg(
			'grammar_unique_code',
			$item_grammar_unique_code,
			$url_base
		);
		$str_japanese = escape_html(
			$arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]]
		);

		$str_a = '<a class="grammarOutlineCreateAreaAddContents grammarInsightsDisplayAreaLiText wiseUiFontSizeTarget" href="' . esc_url($str_url) . '" target="_blank">'.$str_japanese.'</a>';
	
		$str_li_text_container = '
		<div class="homeworkLiTextsContainer">
			<div class="homeworkLiText">'.$str_a.'</div>
		</div>';
	
		$str_li_content = '<div class="homeworkLiContent">'.$str_li_text_container.'</div>';
		$str_li_add = '<li class="homeworkLi">'.$str_li_content.'</li>';
		$str_li .= $str_li_add;
	}

	$str_list = '<ul class="homeworkUl">'.$str_li.'</ul>';
	$str_html_a = $str_list;

	return $str_html_a;
}


function build_html_homework_quiz_link_from_grammar_unique_codes($int_quiz_type, $items, $int_selected_language){

	global
        $arr_quiz_data;

	$str_html_a = '';
	
	$url_base = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($arr_quiz_data[$int_quiz_type]['quiz_path'], '/'))
	);

	$params = [
		'createFromArray' => 1,
		'arr_grammar_unique_code' => array_map('strval', $items),
	];

	$str_url = add_query_arg($params, $url_base);

	$str_a = '<a class="grammarOutlineCreateAreaAddContents grammarInsightsDisplayAreaLiText wiseUiFontSizeTarget" href="'
		. esc_url($str_url)
		. '" target="_blank" rel="noopener noreferrer">'
		. escape_html($arr_quiz_data[$int_quiz_type]['quiz_title'][$int_selected_language])
		. '</a>';

	$str_li_text_container = '
	<div class="homeworkLiTextsContainer">
		<div class="homeworkLiText">'.$str_a.'</div>
	</div>';

	$str_li_content = '<div class="homeworkLiContent">'.$str_li_text_container.'</div>';
	$str_li = '<li class="homeworkLi">'.$str_li_content.'</li>';
	$str_list = '<ul class="homeworkUl">'.$str_li.'</ul>';
	$str_html_a = $str_list;

	return $str_html_a;
}


function build_html_homework_word_inflection_quiz_link($items, $int_selected_language){

	global
		$arr_quiz_data,
		$int_quiz_wordInflectionQuiz;

	$str_html_a = '';
	
	$url_base = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($arr_quiz_data[$int_quiz_wordInflectionQuiz]['quiz_path'], '/'))
	);
	
	$arr_inflection = [];

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = (string)$item_grammar_unique_code;

		$int_inflection = (int)fetch_masta_form_root_id_from_unique_code(
			$item_grammar_unique_code,
			$int_selected_language
		);

		if ($int_inflection <= 0) {
			continue;
		}

		$arr_inflection[] = $int_inflection;
	}

	$str_url = add_query_arg(
		[
			'createFromArray' => 1,
			'arr_inflection' => $arr_inflection,
		],
		$url_base
	);

	$str_a = '<a class="grammarOutlineCreateAreaAddContents grammarInsightsDisplayAreaLiText wiseUiFontSizeTarget" href="' . esc_url($str_url) . '" target="_blank" rel="noopener noreferrer">'
		. escape_html($arr_quiz_data[$int_quiz_wordInflectionQuiz]['quiz_title'][$int_selected_language])
		. '</a>';

	$str_li_text_container = '
	<div class="homeworkLiTextsContainer">
		<div class="homeworkLiText">'.$str_a.'</div>
	</div>';
	
	$str_li_content = '<div class="homeworkLiContent">'.$str_li_text_container.'</div>';
	$str_li = '<li class="homeworkLi">'.$str_li_content.'</li>';
	$str_list = '<ul class="homeworkUl">'.$str_li.'</ul>';
	$str_html_a = $str_list;

	return $str_html_a;
}


function build_html_homework_plainform_quiz_link($items, $int_selected_language){

	global
		$arr_quiz_data,
		$int_quiz_plainformQuiz;

	$str_html_a = '';
		
	$url_base = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($arr_quiz_data[$int_quiz_plainformQuiz]['quiz_path'], '/'))
	);

	$arr_japanese_classification = [];

	foreach ($items as $item_grammar_unique_code) {

		$item_grammar_unique_code = (string)$item_grammar_unique_code;

		$int_japanese_classification = (int)fetch_masta_japanese_classification_id_from_unique_code(
			$item_grammar_unique_code,
			$int_selected_language
		);

		if ($int_japanese_classification <= 0) {
			continue;
		}

		$arr_japanese_classification[] = $int_japanese_classification;
	}

	$str_url = add_query_arg(
		[
			'createFromArray' => 1,
			'arr_japanese_classification' => $arr_japanese_classification,
		],
		$url_base
	);

	$str_a = '<a class="grammarOutlineCreateAreaAddContents grammarInsightsDisplayAreaLiText wiseUiFontSizeTarget" href="'
		. esc_url($str_url)
		. '" target="_blank" rel="noopener noreferrer">'
		. escape_html($arr_quiz_data[$int_quiz_plainformQuiz]['quiz_title'][$int_selected_language])
		. '</a>';

	$str_li_text_container = '
	<div class="homeworkLiTextsContainer">
		<div class="homeworkLiText">'.$str_a.'</div>
	</div>';
	
	$str_li_content = '<div class="homeworkLiContent">'.$str_li_text_container.'</div>';
	$str_li = '<li class="homeworkLi">'.$str_li_content.'</li>';
	$str_list = '<ul class="homeworkUl">'.$str_li.'</ul>';
	$str_html_a = $str_list;

	return $str_html_a;
}


function build_html_homework_details_sections($arr_target, $data_form_list, $int_selected_language)
{
    global
        $str_snake_to_camel_unique_code;

    $str_html_details = '';

    if (empty($arr_target)) {
        return $str_html_details;
    }

    $user_level = get_user_level();
    $is_admin = is_admin_level($user_level);
    $li_contenteditable = $is_admin ? ' contenteditable="true"' : '';

    foreach ($arr_target as $target) {

        $add_button = (bool) ($target['add_button'] ?? false);
        $advance_stage = (bool) ($target['advance_stage'] ?? false);
        $heading = (string) ($target['heading'] ?? '');
        $rows = (array) ($target['array'] ?? []);

        if (empty($rows)) {
            continue;
        }

        $str_list = '';

        // -----------------------------
        // New design (ONLY for example sentences list)
        // -----------------------------
        if ($add_button) {

            $buttons = [];

            $data_action = 'homework:navigate:sorting-quiz-fullscreen';
            $label_button = 'Sorting Quiz';

            if ($advance_stage) {
                $data_action = 'homework:navigate:sorting-quiz-fullscreen-advance-stage';
                $label_button = 'Advance Stage';
            }

            $buttons[] = [
                'key' => 'sortingQuiz',
                'button_text' => $label_button,
                'class' => 'homeworkLiButton homeworkContentsSectionButton',
                'action' => $data_action
            ];

            if ($is_admin) {

                $buttons[] = [
                    'key' => 'answer',
                    'button_text' => 'Answer',
                    'class' => 'homeworkLiButton homeworkContentsSectionButton',
                    'action' => 'homework:show',
                    'action_target' => 'answer'
                ];

                $buttons[] = [
                    'key' => 'furigana',
                    'button_text' => 'Kana',
                    'class' => 'homeworkLiButton homeworkContentsSectionButton hidden',
                    'action' => 'homework:show',
                    'action_target' => 'furigana'
                ];
            }

            $ui = [
                'list_tag' => 'ul',
                'list_class' => 'homeworkUl',
                'li_class' => 'homeworkLi',
                'li_content_class' => 'homeworkLiContent',
                'text_container_class' => 'homeworkLiTextsContainer',
                'text_class' => 'homeworkLiText',
                'buttons_container_class' => 'homeworkLiButtonsContainer',
                'contenteditable' => $is_admin,
                'data_form_list' => (string) $data_form_list,
                'unique_code_data_attr' => 'data-sentence-unique-code',
                'apply_text_for_output' => true
            ];

            $str_list = build_html_registered_sentences_ul(
                $rows,
                $buttons,
                $int_selected_language,
                $user_level,
                $str_snake_to_camel_unique_code,
                'foreign',
                $ui
            );
        }
        // -----------------------------
        // Old design (for input_data / active_recall etc.)
        // -----------------------------
        else {

            $str_li = '';

            foreach ($rows as $item) {

                $foreign = (string) ($item['foreignLanguageText'] ?? '');
                $clean_text = apply_text_for_output($foreign);

                if (trim($clean_text) === '') {
                    continue;
                }

                $str_li_text_container = '
                <div class="homeworkLiTextsContainer">
                    <div class="homeworkLiText"' . $li_contenteditable . '>' . $clean_text . '</div>
                </div>';

                $str_li_content = '<div class="homeworkLiContent">' . $str_li_text_container . '</div>';
                $str_li_add = '<li class="homeworkLi">' . $str_li_content . '</li>';
                $str_li .= $str_li_add;
            }

            if ($str_li !== '') {
                $str_list = '<ol class="homeworkOl">' . $str_li . '</ol>';
            }
        }

        if ($str_list === '') {
            continue;
        }

        $details_class = 'homeworkDetails animationSlideIn';
        $summary_class = 'homeworkDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
        $details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';

        $str_html_details .= build_html_details_contents(
            $str_list,
            $heading,
            $details_class,
            $summary_class,
            $details_div_class
        );
    }

    return $str_html_details;
}
