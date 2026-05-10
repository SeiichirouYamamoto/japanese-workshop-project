<?php

/******************************************************
 *  BODY
 *
 ******************************************************/
function build_html_wise_lesson_contents_body($int_selected_language)
{
    global
        $path_images_verticalToolbarContainer,
        $arr_str_button_caption_confirm;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
		build_html_wise_panel_close_button('wisePanelLessonContentsViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelLessonContentsViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelLessonContentsViewSplitButton'),
    ];

    $buttons_right = [
        '<button id="lessonContentsResetButton">Reset</button>',
        '<button id="lessonContentsGoToRoomManageButton">Room Page</button>',
        '<button class="grammarViewZoomIn grammarViewZoomButton">' . $html_zoom_in_icon . '</button>',
        '<button class="grammarViewZoomOut grammarViewZoomButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        'lesson_contents',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelLessonContentsViewHeader',
    );

    $url_images_verticalToolbarContainer = get_home_url(
        get_main_site_id(),
        trailingslashit(ltrim($path_images_verticalToolbarContainer, '/'))
    );

    $html_toolbar_contents = '
        <div id="wisePanelLessonContentsViewLessonRangeSelectorContainer">
            <div id="wisePanelLessonContentsViewLessonRangeSelector">
                <select id="wisePanelLessonContentsViewLessonRangeSelectorStartLesson" name="wisePanelLessonContentsViewLessonRangeSelectorStartLesson"></select>
                <select id="wisePanelLessonContentsViewLessonRangeSelectorEndLesson" name="wisePanelLessonContentsViewLessonRangeSelectorEndLesson"></select>
            </div>
            <button id="wisePanelLessonContentsViewSelectRoomRangeConfirmButton">' . $arr_str_button_caption_confirm[$int_selected_language] . '</button>
        </div>

        <div id="wisePanelLessonContentsViewLessonToolbar">
            <div id="wisePanelLessonContentsViewLessonToolbarSelectorButton" class="wisePanelLessonContentsViewLessonToolbarButton wisePanelLessonContentsViewLessonToolbarButtonToggle wisePanelLessonContentsViewLessonToolbarButton-selected">
                <img id="wisePanelLessonContentsViewLessonToolbarSelectorButtonImg" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png') . '" alt="セレクト" title="セレクト">
            </div>
            <div id="wisePanelLessonContentsViewLessonToolbarSearchGrammarButton" class="wisePanelLessonContentsViewLessonToolbarButton wisePanelLessonContentsViewLessonToolbarButtonToggle">
                <img id="wisePanelLessonContentsViewLessonToolbarSearchGrammarButtonImg" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSearchGrammarButton.png') . '" alt="文法" title="文法">
            </div>
            <div id="wisePanelLessonContentsViewLessonToolbarToolsButton" class="wisePanelLessonContentsViewLessonToolbarButton wisePanelLessonContentsViewLessonToolbarButtonToggle">
                <img id="wisePanelLessonContentsViewLessonToolbarToolsButtonImg" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarToolsButton.png') . '" alt="ツール" title="ツール">
            </div>
            <div id="wisePanelLessonContentsViewLessonToolbarFunctionsButton" class="wisePanelLessonContentsViewLessonToolbarButton wisePanelLessonContentsViewLessonToolbarButtonToggle">
                <img id="wisePanelLessonContentsViewLessonToolbarFunctionsButtonImg" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarFunctionsButton.png') . '" alt="ファンクション" title="ファンクション">
            </div>
        </div>
    ';

    $html_toolbar = build_html_wise_panel_toolbar(
        $html_toolbar_contents,
        'wisePanelLessonContentsViewToolbar'
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '',
        'wisePanelLessonContentsViewMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = build_html_loading_spinner('wisePanelLessonContentsViewLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelLessonContentsViewMainContentContainer'
    );

    $html_handle = build_html_wise_panel_view_handle(
        '',
        'wisePanelLessonContentsViewHandle',
    );

    return build_html_wise_panel_view(
        'wisePanelLessonContentsView',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        $html_handle
    );
}

/******************************************************
 *  UI
 *
 ******************************************************/
function build_html_wise_lesson_contents_ui($int_selected_language)
{
    $html = '';
    $html .= build_html_wise_lesson_contents_tools_ui($int_selected_language);
    $html .= build_html_wise_lesson_contents_functions_ui($int_selected_language);
    $html .= build_html_wise_lesson_contents_search_grammar_ui($int_selected_language);

    return $html;
}

/******************************************************
 *  FLOAT UI : TOOLS
 *
 ******************************************************/
function build_html_wise_lesson_contents_tools_ui($int_selected_language)
{
    $html_contents = '
        <div id="wisePanelLessonContentsUiToolsContainer" class="wisePanelLessonContentsUiFloatContainer">
            <button id="wisePanelLessonContentsUiDeselectButton" class="wisePanelLessonContentsUiButton">Deselect</button>
            <button id="wisePanelLessonContentsUiAddToListButton" class="wisePanelLessonContentsUiButton">Add To List</button>
            <button id="wisePanelLessonContentsUiShowInsightsButton" class="wisePanelLessonContentsUiButton">Show Insights</button>
            <button id="wisePanelLessonContentsUiShowExplanationButton" class="wisePanelLessonContentsUiButton">Show Explanation</button>
            <button id="wisePanelLessonContentsUiShowWiseMapLessonStepButton" class="wisePanelLessonContentsUiButton">Show Usages Map</button>
            <button id="wisePanelLessonContentsUiShowWiseMapLessonButton" class="wisePanelLessonContentsUiButton">Show Tasks Map</button>
        </div>
    ';

    return build_html_wise_panel_ui_item(
        'wisePanelLessonContentsUiTools',
        '',
        '',
        '',
        $html_contents,
        'float'
    );
}

/******************************************************
 *  FLOAT UI : FUNCTIONS
 *
 ******************************************************/
function build_html_wise_lesson_contents_functions_ui($int_selected_language)
{
    $arr_labels_by_language = [
        [
            SELECT_ALL   => '必修 + 応用',
            STATUS_FIRST => '必修のみ',
        ],
        [
            SELECT_ALL   => '必修 + 進階',
            STATUS_FIRST => '僅限必修',
        ],
        [
            SELECT_ALL   => 'Required + Advanced',
            STATUS_FIRST => 'Required Only',
        ],
    ];

    $arr_labels = $arr_labels_by_language[$int_selected_language] ?? $arr_labels_by_language[0];

    $html_contents = '
        <div id="wisePanelLessonContentsUiFunctionsContainer" class="wisePanelLessonContentsUiFloatContainer">
            <select id="wisePanelLessonContentsUiFunctionsContainerGrammarScopeSelect" name="wisePanelLessonContentsUiFunctionsContainerGrammarScopeSelect">
                <option value="' . SELECT_ALL . '">' . $arr_labels[SELECT_ALL] . '</option>
                <option value="' . STATUS_FIRST . '">' . $arr_labels[STATUS_FIRST] . '</option>
            </select>
            <select id="wisePanelLessonContentsUiFunctionsContainerTitleExampleDisplaySelect" name="wisePanelLessonContentsUiFunctionsContainerTitleExampleDisplaySelect">
                <option value="' . STATUS_FIRST . '">Titles</option>
                <option value="' . STATUS_SECOND . '">Examples</option>
            </select>
        </div>
    ';

    return build_html_wise_panel_ui_item(
        'wisePanelLessonContentsUiFunctions',
        '',
        '',
        '',
        $html_contents,
        'float'
    );
}

/******************************************************
 *  SIDE UI : SEARCH GRAMMAR
 *
 ******************************************************/
function build_html_wise_lesson_contents_search_grammar_ui($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    // ===== buttons =====
    $buttons_left = [
        '<button id="wisePanelLessonContentsUiSearchGrammarCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    // ===== header =====
    $html_header = build_html_wise_panel_header(
        '文法検索',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelLessonContentsUiSearchGrammarHeader'
    );

    // ===== toolbar =====
    $html_toolbar = '';

    // ===== main content area =====
    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelLessonContentsUiSearchGrammarSearchArea" class="wisePanelLessonContentsUiSearchGrammarSearchArea">
            <form id="wisePanelLessonContentsUiSearchGrammarSearchForm" class="wisePanelLessonContentsUiSearchGrammarSearchForm">
                <input type="text" id="wisePanelLessonContentsUiSearchGrammarSearchInput" class="wisePanelLessonContentsUiSearchGrammarSearchInput wisePanelLessonContentsUiTextInputArea" placeholder="検索ワードを入力">
            </form>
            <button id="wisePanelLessonContentsUiSearchGrammarSearchButton" class="wisePanelLessonContentsUiSearchGrammarSearchButton">検索</button>
        </div>
        <ul id="wisePanelLessonContentsUiSearchGrammarList" class="wisePanelUiList"></ul>
        ',
        'wisePanelLessonContentsUiSearchGrammarMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    // ===== loading =====
    $html_loading = build_html_loading_spinner('wisePanelLessonContentsUiSearchGrammarLoading');

    // ===== container =====
    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelLessonContentsUiSearchGrammarMainContentContainer'
    );

    // ===== ui =====
    return build_html_wise_panel_ui_item(
        'wisePanelLessonContentsUiSearchGrammar',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'side'
    );
}

/******************************************************
 *  Item
 *  
 ******************************************************/
function build_html_lesson_contents_tree($room_id, $contents_tree_flags, $arr_lessons, $arr_bookmarks_data, $int_selected_language){

	$str_html = '';
	$arr_masta_japanese_root_ids = [];

	$contents_tree_flags['doDisplayDerivedGrammars'] = false;
	$contents_tree_flags['displayInGrammarView'] = false;

	$type = 'lesson_contents_tree';
	$arr_search_condition_for_category = [];
	$arr_lesson_content_information = get_data_lesson_content_information($type, $room_id, $contents_tree_flags, $arr_lessons, $arr_bookmarks_data, $arr_search_condition_for_category, $int_selected_language);

	$arr_masta_japanese_root_ids = $arr_lesson_content_information['arr_masta_japanese_root_ids'];
	$str_html = $arr_lesson_content_information['str_html'];

	$arr_already_learned_list = get_arr_already_learned_list($arr_masta_japanese_root_ids, $int_selected_language);
	$_SESSION['arr_already_learned_list'] = $arr_already_learned_list;

	return $str_html;
}


function build_html_grammar_tree_common($contents_tree_flags, $arr_bookmarks_data, $int_selected_language, $sets_table, $items_table, $tree_title, $requireAllLearned = true) {

    $str_html = '';
    $str_explanation_button = '';

    $contents_tree_flags['doDisplayDerivedGrammars'] = false;
    $contents_tree_flags['displayInGrammarView'] = false;

    $arr_already_learned_list = [];
    if (isset($_SESSION['arr_already_learned_list'])) {
        $arr_already_learned_list = $_SESSION['arr_already_learned_list'];
    }
    if (empty($arr_already_learned_list)) {
        return $str_html;
    }

    $arr_matched = get_arr_grammar_relation_sets_for_tree($sets_table, $items_table, $int_selected_language, $requireAllLearned);
    if (empty($arr_matched)) {
        return $str_html;
    }

    $str_li = '';

    foreach ($arr_matched as $key => $loop_matched) {

        $title = $loop_matched['title'];
        $items = $loop_matched['array'];

        list($str_div_grammar_outline_text, $str_explanation_button) = build_html_explanation_button($contents_tree_flags['doDisplayGrammarOutlineLabelButtonsExplanation'], $key, $int_selected_language);

        $contents_tree_flags['doDisplayGrammarOutlineUlOpener'] = false;
        $str_html_container = build_html_grammar_outline_container($contents_tree_flags, $arr_bookmarks_data, $items, $str_explanation_button, $title, $int_selected_language);

        $str_container = '<div class="divGrammarOutlineContainer">'.$str_html_container.'</div>';
        $str_li_add = '<li class="grammarOutlineLi">'.$str_container.''.'</li>';

        $str_li = $str_li.$str_li_add;
    }

    if ($str_li === '') {
        return $str_html;
    }

    $str_tree_title = $tree_title;

    $str_ul = '<ul class="grammarOutlineUl">'.$str_li.'</ul>';

    if (!empty($str_li)) {
        $contents_tree_flags['doDisplayGrammarOutlineUlOpener'] = true;
    } else {
        $contents_tree_flags['doDisplayGrammarOutlineUlOpener'] = false;
    }

    $str_html_container_for_title = build_html_grammar_outline_container($contents_tree_flags, $arr_bookmarks_data, [], '', $str_tree_title, $int_selected_language);

    $str_container_for_for_title = '<div class="divGrammarOutlineContainer">'.$str_html_container_for_title.'</div>';
    $str_li_result = '<li class="grammarOutlineLi">'.$str_container_for_for_title.$str_ul.'</li>';
    $str_ul_result = '<ul class="grammarOutlineUlTopElement">'.$str_li_result.'</ul>';

    $str_html_tree = '
    <div class="grammarOutline grammarOutlineTopElement">'.
        $str_ul_result.'
    </div>';

    $details_class = 'grammarOutlineDetails animationSlideIn';
    $summary_class = 'grammarOutlineDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
    $details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';

    $str_html_heading = $str_tree_title;
    $str_html_heading = escape_html_with_nl2br($str_html_heading);
    $str_html_tree = build_html_details_contents($str_html_tree, $str_html_heading, $details_class, $summary_class, $details_div_class);

	$str_html = '<div class="divFrame">' . $str_html_tree . '</div>';

    return $str_html;
}


function build_html_lesson_contents_tree_one_lesson(
    $lesson_title,
    $steps_grouped,
    $arr_bookmarks_data,
    $contents_tree_flags,
    $int_selected_language
) {

    $str_explanation_button = '';
    $str_li = '';

    foreach ($steps_grouped as $step_name => $step) {
        $units = $step['units'] ?? [];
        usort($units, fn($a, $b) => $a['sort'] <=> $b['sort']);

        $str_unit_lis = '';
        foreach ($units as $u) {
            $items = $u['items'] ?? [];
            usort($items, fn($a, $b) => $a['lesson_content_sort'] <=> $b['lesson_content_sort']);

            $flags = $contents_tree_flags;
            $flags['doDisplayGrammarOutlineUlOpener'] = false;

            $box = build_html_grammar_outline_container(
                $flags,
                $arr_bookmarks_data,
                $items,
                $str_explanation_button,
                $u['unit_type_text'] ?? '',
                $int_selected_language
            );
            $str_unit_lis .= '<li class="grammarOutlineLi"><div class="divGrammarOutlineContainer">' . $box . '</div></li>';
        }

        if ($str_unit_lis !== '') {
            $flags = $contents_tree_flags;
            $flags['doDisplayGrammarOutlineUlOpener'] = true;

            $title = build_html_grammar_outline_container($flags, $arr_bookmarks_data, [], '', $step_name, $int_selected_language);
            $str_li .= '<li class="grammarOutlineLi"><div class="divGrammarOutlineContainer">' . $title . '</div><ul class="grammarOutlineUl">' . $str_unit_lis . '</ul></li>';
        }
    }

    if ($str_li === '') {
        return '';
    }

    $flags = $contents_tree_flags;
    $flags['doDisplayGrammarOutlineUlOpener'] = true;

    $title = build_html_grammar_outline_container($flags, $arr_bookmarks_data, [], '', $lesson_title, $int_selected_language);
    return '<li class="grammarOutlineLi"><div class="divGrammarOutlineContainer">' . $title . '</div><ul class="grammarOutlineUl">' . $str_li . '</ul></li>';
}


function build_html_workshop_lesson_contents(
    $lesson_title,
    $steps_grouped,
    $arr_bookmarks_data,
    $int_selected_language,
	bool $withDetails = true
) {
	
	global
		$int_masta_japanese_category_id_grammar,
		$str_snake_to_camel_japanese,
		$arr_columns_masta_japanese_sub_category,
		$str_snake_to_camel_unique_code,
		$path_grammar_point,
		$workshop_trial_unique_code;

		
	$room_unique_code = $arr_bookmarks_data['room_unique_code'] ?? '';
	$map_grammar_unique_code = $arr_bookmarks_data['map_grammar_unique_code'] ?? [];


    $purpose_ul = '';
	
	$url_grammar_point = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_grammar_point, '/'))
	);

    foreach ($steps_grouped as $step_name => $step) {
        $units = $step['units'] ?? [];
        usort($units, fn($a, $b) => $a['sort'] <=> $b['sort']);

        $lis = '';
        foreach ($units as $u) {
            $items = $u['items'] ?? [];
            usort($items, fn($a, $b) => $a['lesson_content_sort'] <=> $b['lesson_content_sort']);

            foreach ($items as $it) {
                $label = (($it['category_id'] ?? 0) !== $int_masta_japanese_category_id_grammar)
                    ? escape_html_with_nl2br($it[$str_snake_to_camel_japanese] ?? '') . ' : ' . escape_html_with_nl2br($it[$arr_columns_masta_japanese_sub_category[$int_selected_language]] ?? '')
                    : escape_html_with_nl2br($it[$str_snake_to_camel_japanese] ?? '');

                $example = (($it['category_id'] ?? 0) === $int_masta_japanese_category_id_grammar)
                    ? apply_original_tags_to_html(escape_html_with_nl2br($it['root_example'] ?? ''))
                    : '';

                $grammar_unique_code = (string)($it[$str_snake_to_camel_unique_code] ?? '');
				$href = ($grammar_unique_code !== '')
					? add_query_arg(
						'grammar_unique_code',
						$grammar_unique_code,
						$url_grammar_point
					)
					: '#';

                $label_html = build_html_anchor(
                    '',
                    $label,
                    $href,
                    'workshopLessonsLiLabel',
                    true,
                    $int_selected_language
                );

				$html_bookmark_star = '';

                if (
					$room_unique_code !== $workshop_trial_unique_code &&
					$grammar_unique_code !== ''
				) {
					

					$unique_id = uniqid();
					$is_bookmarked =
						isset($map_grammar_unique_code[$grammar_unique_code]) &&
						empty($map_grammar_unique_code[$grammar_unique_code]['deleted_at']);

					$html_bookmark_star = build_html_bookmark_star(
						$unique_id,
						$grammar_unique_code,
						$is_bookmarked,
						$room_unique_code
					);
                }

                $lis .=
                    '<li class="workshopLessonsLi" data-unique-code="' . escape_html($grammar_unique_code) . '">' .
						$html_bookmark_star .
                        '<div class="workshopLessonsLiLabelContainer">' .
                            '<div class="workshopLessonsLiTitle">' . $label_html . '</div>' .
                            '<div class="workshopLessonsLiExample">' . $example . '</div>' .
                        '</div>' .
                    '</li>';
            }
        }

        if ($lis !== '') {
            $purpose_ul .=
                '<div class="workshopLessonsItem">' .
                    '<h3 class="workshopLessonsStepName">' . escape_html_with_nl2br($step_name) . '</h3>' .
                    '<ul class="workshopLessonsUl">' . $lis . '</ul>' .
                '</div>';
        }
    }

    if ($purpose_ul === '') {
        return '';
    }

	if($withDetails){

		$purpose_heading = apply_text_for_output($lesson_title);
	
		$details_class = 'workshopLessonsDetails';
		$summary_class = 'workshopLessonsDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
		$details_div_class = 'detailsDiv workshopLessonsDetailsDiv detailsDivAddMarginBottom';
	
		$purpose_blocks = build_html_details_contents($purpose_ul, $purpose_heading, $details_class, $summary_class, $details_div_class);
		$html = '<div class="divFrame">' . $purpose_blocks . '</div>';
	
		return $html;

	}
	else{

		$html = $purpose_ul;
		return $html;

	}
}



/******************************************************
 *  ITEM
 *  
 ******************************************************/
function get_data_lesson_content_information(
	$type,
	$room_id,
	$contents_tree_flags,
	$arr_lessons,
	$arr_bookmarks_data,
	$arr_search_condition_for_category,
	$int_selected_language
) {

	global
		$arr_columns_masta_teaching_material_lesson_objectives,
		$arr_columns_masta_teaching_material_lessons,
		$arr_columns_masta_step_unit_types,
		$arr_columns_masta_teaching_material_levels,
		$str_homework_method_inputData,
		$str_homework_method_activeRecall,
		$str_homework_method_registeredSentences,
		$str_workshopLessonLevelSelectAll,
		$str_workshopLessonLevelSelectHeader;

	list($group_tm, $group_room) = get_arr_lesson_contents_grouped_for_ui($arr_lessons, $arr_search_condition_for_category, $int_selected_language);
	$arr_masta_japanese_root_ids = fetch_arr_masta_japanese_root_ids_by_lessons($arr_lessons, $int_selected_language);

	$lesson_cards = [];
	$arr_masta_japanese_root = [];

	foreach ($arr_lessons as $lesson) {
		$room_lesson_id = (int)$lesson['id'];
		$tm_lesson_id = (int)$lesson['teaching_material_lesson_id'];

		$objective = $lesson[$arr_columns_masta_teaching_material_lesson_objectives[$int_selected_language]] ?? null;
		$lesson_title = ($objective === null || $objective === '---')
			? $lesson['title']
			: $lesson[$arr_columns_masta_teaching_material_lessons[$int_selected_language]] . ': ' . $objective;

		$steps_grouped = [];

		if (isset($group_tm[$tm_lesson_id])) {
			foreach ($group_tm[$tm_lesson_id] as $step_id => $step) {
				$name = $step['step_name'];
				$steps_grouped[$name]['sort'] = $step['sort'];
				foreach ($step['units'] as $unit_id => $u) {
					$steps_grouped[$name]['units'][] = [
						'sort' => $u['sort'],
						'items' => $u['items'],
						'unit_type_text' => $u[$arr_columns_masta_step_unit_types[$int_selected_language]] ?? ''
					];
				}
			}
		}

		if (isset($group_room[$room_lesson_id])) {
			foreach ($group_room[$room_lesson_id] as $step_id => $step) {
				$name = $step['step_name'] . ($type === 'lesson_contents_tree' ? ':Original Contents' : '');
				$steps_grouped[$name]['sort'] = $step['sort'] + 10000;
				foreach ($step['units'] as $unit_id => $u) {
					$steps_grouped[$name]['units'][] = [
						'sort' => $u['sort'],
						'items' => $u['items'],
						'unit_type_text' => $u[$arr_columns_masta_step_unit_types[$int_selected_language]] ?? ''
					];
				}
			}
		}

		if (empty($steps_grouped)) {
			continue;
		}

		uasort($steps_grouped, fn($a, $b) => $a['sort'] <=> $b['sort']);

		switch ($type) {
			case 'lesson_contents_tree': {
				$card_html = build_html_lesson_contents_tree_one_lesson(
					$lesson_title,
					$steps_grouped,
					$arr_bookmarks_data,
					$contents_tree_flags,
					$int_selected_language
				);
				$lesson_cards[] = [
					'level_id' => $lesson['level_id'] ?? null,
					'level_label' => $lesson[$arr_columns_masta_teaching_material_levels[$int_selected_language]] ?? null,
					'level_sort' => isset($lesson['level_sort']) ? (int)$lesson['level_sort'] : PHP_INT_MAX,
					'lesson_sort' => isset($lesson['lesson_sort']) ? (int)$lesson['lesson_sort'] : PHP_INT_MAX,
					'html' => $card_html
				];
				break;
			}
			case 'workshop_lessons': {
				$card_html = build_html_workshop_lesson_contents(
					$lesson_title,
					$steps_grouped,
					$arr_bookmarks_data,
					$int_selected_language,
					true
				);
				$lesson_cards[] = [
					'level_id' => $lesson['level_id'] ?? null,
					'level_label' => $lesson[$arr_columns_masta_teaching_material_levels[$int_selected_language]] ?? null,
					'level_sort' => isset($lesson['level_sort']) ? (int)$lesson['level_sort'] : PHP_INT_MAX,
					'lesson_sort' => isset($lesson['lesson_sort']) ? (int)$lesson['lesson_sort'] : PHP_INT_MAX,
					'html' => $card_html
				];
				break;
			}
			case 'workshop_one_lesson': {
				$card_html = build_html_workshop_lesson_contents(
					$lesson_title,
					$steps_grouped,
					$arr_bookmarks_data,
					$int_selected_language,
					false
				);
				$lesson_cards[] = [
					'level_id' => $lesson['level_id'] ?? null,
					'level_label' => $lesson[$arr_columns_masta_teaching_material_levels[$int_selected_language]] ?? null,
					'level_sort' => isset($lesson['level_sort']) ? (int)$lesson['level_sort'] : PHP_INT_MAX,
					'lesson_sort' => isset($lesson['lesson_sort']) ? (int)$lesson['lesson_sort'] : PHP_INT_MAX,
					'html' => $card_html
				];
				break;
			}
			case 'masta_japanese_root_ids_for_quiz': {
				break;
			}
			case 'bookmarks': {
				$int_learning_status = (int)$lesson['learning_status'];
				foreach ($steps_grouped as $s) {
					$units = $s['units'] ?? [];
					foreach ($units as $u) {
						$items = $u['items'] ?? [];
						usort($items, fn($a, $b) => $a['lesson_content_sort'] <=> $b['lesson_content_sort']);
						foreach ($items as &$item) {
							$root_id = (int)$item['masta_japanese_root_id'];
							$arr_input = fetch_arr_room_user_input_data($room_id, $root_id, $int_selected_language);
							$arr_recall = fetch_arr_active_recall($root_id, $int_selected_language);
							$arr_reg = fetch_arr_registered_sentence_by_root_id($root_id, $int_selected_language);
							$item[$str_homework_method_inputData] = empty($arr_input) ? FLAG_FALSE : FLAG_TRUE;
							$item[$str_homework_method_activeRecall] = empty($arr_recall) ? FLAG_FALSE : FLAG_TRUE;
							$item[$str_homework_method_registeredSentences] = empty($arr_reg) ? FLAG_FALSE : FLAG_TRUE;
							$item['learning_status'] = $int_learning_status;
							$arr_masta_japanese_root[] = $item;
						}
						unset($item);
					}
				}
				break;
			}
		}
	}

	$str_html = '';

	if ($type === 'lesson_contents_tree') {
		$cards_concatenated = implode('', array_column($lesson_cards, 'html'));
		if ($cards_concatenated !== '') {
			$heading = escape_html_with_nl2br('Lesson Contents');
			$details_class = 'grammarOutlineDetails animationSlideIn';
			$summary_class = 'grammarOutlineDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
			$details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';

			$flags = $contents_tree_flags;
			$flags['doDisplayGrammarOutlineUlOpener'] = true;

			$title = build_html_grammar_outline_container($flags, $arr_bookmarks_data, [], '', $heading, $int_selected_language);
			$str_html = '<ul class="grammarOutlineUlTopElement"><li class="grammarOutlineLi"><div class="divGrammarOutlineContainer">' . $title . '</div><ul class="grammarOutlineUl">' . $cards_concatenated . '</ul></li></ul>';
			$str_html = '<div class="grammarOutline grammarOutlineTopElement">' . $str_html . '</div>';
			$str_html = build_html_details_contents($str_html, $heading, $details_class, $summary_class, $details_div_class);
			$str_html = '<div class="divFrame">' . $str_html . '</div>';
		}
	} elseif ($type === 'workshop_lessons') {
		$groups = [];
		$level_min_sort = [];

		foreach ($lesson_cards as $c) {
			$has_level = !is_null($c['level_id']) && !is_null($c['level_label']) && $c['level_label'] !== '';
			$key = $has_level ? ('level_' . (int)$c['level_id']) : 'original';
			$label = $has_level ? $c['level_label'] : 'Original Contents';
			$lvl_sort = $c['level_sort'] ?? PHP_INT_MAX;
			$les_sort = $c['lesson_sort'] ?? PHP_INT_MAX;

			if (!isset($groups[$key])) {
				$groups[$key] = ['label' => $label, 'level_sort' => $lvl_sort, 'items' => []];
				$level_min_sort[$key] = $lvl_sort;
			} else {
				if ($lvl_sort < $level_min_sort[$key]) {
					$level_min_sort[$key] = $lvl_sort;
					$groups[$key]['level_sort'] = $lvl_sort;
				}
				if ($groups[$key]['label'] === '' && $label !== '') {
					$groups[$key]['label'] = $label;
				}
			}

			if ($c['html'] !== '') {
				$groups[$key]['items'][] = ['lesson_sort' => $les_sort, 'html' => $c['html']];
			}
		}

		if (!empty($groups)) {
			$ordered_keys = array_keys($groups);
			usort($ordered_keys, function ($ka, $kb) use ($groups) {
				return ($groups[$ka]['level_sort'] <=> $groups[$kb]['level_sort']);
			});

			$default_level_key = $ordered_keys[0] ?? null;

			$opts = [];
			$opts[] = '<option value="' . SELECT_ALL . '">' . escape_html_with_nl2br($str_workshopLessonLevelSelectAll[$int_selected_language]) . '</option>';
			foreach ($ordered_keys as $k) {
				$label = $groups[$k]['label'] !== '' ? $groups[$k]['label'] : 'Original Contents';
				$selected = ($k === $default_level_key) ? ' selected' : '';
				$opts[] = '<option value="' . escape_html_with_nl2br($k) . '"' . $selected . '>' . escape_html_with_nl2br($label) . '</option>';
			}

			$select_html =
				'<div class="workshopLessonLevelSelector">' .
					'<h2>' . escape_html_with_nl2br($str_workshopLessonLevelSelectHeader[$int_selected_language]) . '</h2>' .
					'<select id="workshopLessonLevelSelect" class="selectWorkshopLessonLevel">' .
						implode('', $opts) .
					'</select>' .
				'</div>';

			$parts = [];
			foreach ($ordered_keys as $k) {
				$label = $groups[$k]['label'] !== '' ? $groups[$k]['label'] : 'Original Contents';
				$items = $groups[$k]['items'];

				usort($items, function ($a, $b) {
					return ($a['lesson_sort'] <=> $b['lesson_sort']);
				});

				$h2 = '<h2>' . escape_html_with_nl2br($label) . '</h2>';
				$group_html = $h2 . implode('', array_column($items, 'html'));
				$maybe_hidden = ($k === $default_level_key) ? '' : ' hidden';
				$group_html = '<section class="sectionWorkshopLesson sectionStandard' . $maybe_hidden . '" data-level-key="' . escape_html_with_nl2br($k) . '">' . $group_html . '</section>';
				$parts[] = $group_html;
			}

			$groups_html = implode('', $parts);

			$str_html =
				'<section class="sectionWorkshopLessons">' .
					$select_html .
					$groups_html .
				'</section>';
		}
	} elseif ($type === 'workshop_one_lesson') {
		$str_html = '';
		foreach($lesson_cards as $card){
			$str_html .= $card['html'];
		}
		$str_html =
			'<section class="sectionWorkshopLesson sectionStandard">' .
				$str_html .
			'</section>';
	} elseif ($type === 'masta_japanese_root_ids_for_quiz') {
		$str_html = '';
	} elseif ($type === 'bookmarks') {
		$str_html = '';
	}

	return [
		'arr_masta_japanese_root' => $arr_masta_japanese_root,
		'arr_masta_japanese_root_ids' => $arr_masta_japanese_root_ids,
		'str_html' => $str_html
	];
}


function get_arr_lesson_contents_grouped_for_ui($arr_lessons, $arr_search_condition_for_category, $int_selected_language) {

	global
		$t_teaching_material_lesson_steps,
		$t_teaching_material_lesson_step_units,
		$str_sql_where_is_in,
		$arr_columns_masta_teaching_material_lesson_steps,
		$arr_columns_masta_step_unit_types,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$arr_columns_masta_japanese_sub_category,
		$int_used_language_jpn,
		$str_column_root_kana,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_category_id,
		$t_masta_japanese_sub_category,
		$t_teaching_material_lesson_contents,
		$t_masta_step_unit_type,
		$t_room_lesson_steps,
		$t_room_lesson_step_units,
		$t_room_lesson_contents;

    $tm_lesson_ids = array_values(array_unique(array_map(fn($r) => (int)$r['teaching_material_lesson_id'], $arr_lessons)));
    $room_lesson_ids = array_values(array_unique(array_map(fn($r) => (int)$r['id'], $arr_lessons)));

    $where_tm = [
        [$t_teaching_material_lesson_steps, 'lesson_id', $str_sql_where_is_in, $tm_lesson_ids, 'PDO::PARAM_INT', 'And'],
        [$t_teaching_material_lesson_step_units, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
    ];

    $where_room = [
        [$t_room_lesson_steps, 'lesson_id', $str_sql_where_is_in, $room_lesson_ids, 'PDO::PARAM_INT', 'And'],
        [$t_room_lesson_step_units, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
    ];

    
    $arr_strSQL_select_teaching_material_lesson_contents = [
		[$t_teaching_material_lesson_steps,'lesson_id as teaching_material_lesson_id'],
		[$t_teaching_material_lesson_steps,'id as teaching_material_step_id'],
		[$t_teaching_material_lesson_steps,$arr_columns_masta_teaching_material_lesson_steps[$int_selected_language].' as step_name'],
		[$t_teaching_material_lesson_step_units,'id as step_unit_id'],
		[$t_masta_step_unit_type, $arr_columns_masta_step_unit_types[$int_selected_language]],
		[$t_teaching_material_lesson_step_units,'sort as step_unit_sort'],
		[$t_masta_japanese_root,'id as masta_japanese_root_id'],
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese_sub_category,'id as sub_category_id'],
		[$t_masta_japanese_sub_category,'category_id'],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id],
		[$t_masta_japanese_sub_category,$arr_columns_masta_japanese_sub_category[$int_selected_language]],
		[$t_teaching_material_lesson_step_units,'is_published'],
		[$t_teaching_material_lesson_steps,'sort as step_sort'],
		[$t_teaching_material_lesson_contents,'sort as lesson_content_sort']
	];

	$strSQL_from_teaching_material_lesson_contents = "
		FROM $t_masta_japanese_root
		INNER JOIN $t_masta_japanese_sub_category
			ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
		INNER JOIN $t_teaching_material_lesson_contents
			ON $t_masta_japanese_root.id = $t_teaching_material_lesson_contents.masta_japanese_root_id
		INNER JOIN $t_teaching_material_lesson_step_units
			ON $t_teaching_material_lesson_contents.step_unit_id = $t_teaching_material_lesson_step_units.id
		INNER JOIN $t_masta_step_unit_type
			ON $t_teaching_material_lesson_step_units.unit_type = $t_masta_step_unit_type.id
		INNER JOIN $t_teaching_material_lesson_steps
			ON $t_teaching_material_lesson_step_units.lesson_step_id = $t_teaching_material_lesson_steps.id
	";

	$arr_strSQL_where_teaching_material_lesson_contents = [
		[
			$where_tm,
			''
		]
	];

	if(!empty($arr_search_condition_for_category)){
		$arr_strSQL_where_teaching_material_lesson_contents = [
			[
				$arr_search_condition_for_category,
				'And'
			],
			[
				$where_tm,
				''
			]
		];
	}
	
	$arr_strSQL_order_teaching_material_lesson_contents = [
		[$t_teaching_material_lesson_steps,'sort','ASC'],
		[$t_teaching_material_lesson_step_units,'sort','ASC'],
		[$t_teaching_material_lesson_contents,'sort','ASC']
	];

	$strSQL_option_teaching_material_lesson_contents = '';

	list($pdo_has_error, $select_has_error, $e, $arr_teaching_material_lesson_contents) = execute_select_and_fetch_all($arr_strSQL_select_teaching_material_lesson_contents, $strSQL_from_teaching_material_lesson_contents, $arr_strSQL_where_teaching_material_lesson_contents, $arr_strSQL_order_teaching_material_lesson_contents, $strSQL_option_teaching_material_lesson_contents);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);


	$arr_grouped_contents_teaching_material = [];
	foreach ($arr_teaching_material_lesson_contents as $content) {
		$lesson_id = $content['teaching_material_lesson_id'];
		$step_id = $content['teaching_material_step_id'];
		$step_unit_id = $content['step_unit_id'];
		$step_sort = $content['step_sort'];
		$step_unit_sort = $content['step_unit_sort'];

		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['step_name'] = $content['step_name'];
		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['sort'] = $step_sort;
		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['units'][$step_unit_id][$arr_columns_masta_step_unit_types[$int_selected_language]] = $content[$arr_columns_masta_step_unit_types[$int_selected_language]];
		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['units'][$step_unit_id]['sort'] = $step_unit_sort;
		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['units'][$step_unit_id]['is_published'] = $content['is_published'];
		$arr_grouped_contents_teaching_material[$lesson_id][$step_id]['units'][$step_unit_id]['items'][] = $content;
	}
	

	$arr_strSQL_select_room_lesson_contents = [
		[$t_room_lesson_steps,'lesson_id as room_lesson_id'],
		[$t_room_lesson_steps,'id as room_step_id'],
		[$t_room_lesson_steps,'step_name'],
		[$t_room_lesson_step_units,'id as step_unit_id'],
		[$t_masta_step_unit_type, $arr_columns_masta_step_unit_types[$int_selected_language]],
		[$t_room_lesson_step_units,'sort as step_unit_sort'],
		[$t_masta_japanese_root,'id as masta_japanese_root_id'],
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese_sub_category,'id as sub_category_id'],
		[$t_masta_japanese_sub_category,'category_id'],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id],
		[$t_room_lesson_step_units,'is_published'],
		[$t_room_lesson_steps,'sort as step_sort'],
		[$t_room_lesson_contents,'sort as lesson_content_sort']
	];

	$strSQL_from_room_lesson_contents = "
		FROM $t_masta_japanese_root
		INNER JOIN $t_masta_japanese_sub_category
			ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
		INNER JOIN $t_room_lesson_contents
			ON $t_masta_japanese_root.id = $t_room_lesson_contents.masta_japanese_root_id
		INNER JOIN $t_room_lesson_step_units
			ON $t_room_lesson_contents.step_unit_id = $t_room_lesson_step_units.id
		INNER JOIN $t_masta_step_unit_type
			ON $t_room_lesson_step_units.unit_type = $t_masta_step_unit_type.id
		INNER JOIN $t_room_lesson_steps
			ON $t_room_lesson_step_units.lesson_step_id = $t_room_lesson_steps.id
	";


	$arr_strSQL_where_room_lesson_contents = [
		[
			$where_room,
			''
		]
	];
	
	if(!empty($arr_search_condition_for_category)){
		$arr_strSQL_where_room_lesson_contents = [
			[
				$arr_search_condition_for_category,
				'And'
			],
			[
				$where_room,
				''
			]
		];
	}
	
	$arr_strSQL_order_room_lesson_contents = [
		[$t_room_lesson_steps,'sort','ASC'],
		[$t_room_lesson_contents,'sort','ASC']
	];

	$strSQL_option_room_lesson_contents = '';

	list($pdo_has_error, $select_has_error, $e, $arr_room_lesson_contents) = execute_select_and_fetch_all($arr_strSQL_select_room_lesson_contents, $strSQL_from_room_lesson_contents, $arr_strSQL_where_room_lesson_contents, $arr_strSQL_order_room_lesson_contents, $strSQL_option_room_lesson_contents);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$arr_grouped_contents_room_lesson = [];
	foreach ($arr_room_lesson_contents as $content) {
		$room_lesson_id = $content['room_lesson_id'];
		$step_id = $content['room_step_id'];
		$step_unit_id = $content['step_unit_id'];
		$step_sort = $content['step_sort'];
		$step_unit_sort = $content['step_unit_sort'];

		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['step_name'] = $content['step_name'];
		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['sort'] = $step_sort;
		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['units'][$step_unit_id][$arr_columns_masta_step_unit_types[$int_selected_language]] = $content[$arr_columns_masta_step_unit_types[$int_selected_language]];
		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['units'][$step_unit_id]['sort'] = $step_unit_sort;
		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['units'][$step_unit_id]['is_published'] = $content['is_published'];
		$arr_grouped_contents_room_lesson[$room_lesson_id][$step_id]['units'][$step_unit_id]['items'][] = $content;
	}
    return [$arr_grouped_contents_teaching_material, $arr_grouped_contents_room_lesson];
}



/******************************************************
 *  USAGE
 *  
 ******************************************************/

function build_html_grammar_usages_tree($contents_tree_flags, $arr_tree_targets, $arr_allow_display, $arr_bookmarks_data, $draw_details, $int_selected_language){

	global
		$t_grammar_usage_categories,
		$arr_columns_grammar_usage_categories,
		$t_grammar_usage_parents,
		$int_masta_grammar_usage_tier_root,
		$t_masta_japanese_root,
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline;


	$str_grammar_usages_tree = '';

	$arr_search_condition = [];
	$str_order_condition = 'CASE id';
	$totalTargets = count($arr_tree_targets);

	foreach($arr_tree_targets as $index => $target_id){
		if ($index === $totalTargets - 1) {
			$last_index = $index + 1;
			$arr_search_condition[] = [$t_grammar_usage_categories,'id','=',$target_id,'PDO::PARAM_INT',''];
			$str_order_condition = $str_order_condition." WHEN $target_id THEN $index";
			$str_order_condition = $str_order_condition." ELSE $last_index END";
		} else {
			$arr_search_condition[] = [$t_grammar_usage_categories,'id','=',$target_id,'PDO::PARAM_INT','Or'];
			$str_order_condition = $str_order_condition." WHEN $target_id THEN $index";
		}
	}

	$arr_strSQL_select = [
		[$t_grammar_usage_categories,'id'],
		[$t_grammar_usage_categories,$arr_columns_grammar_usage_categories[$int_selected_language]]
	];

	$strSQL_from = ' FROM ' .$t_grammar_usage_categories;

	$arr_strSQL_where = [
		[
			$arr_search_condition,
			''
		]
	];

	$arr_strSQL_order = [
		['',$str_order_condition,'']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_grammar_usage_categories) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$relations = fetch_all_root_parent_child_relations($int_selected_language);
	$map_parent_to_children = [];
	foreach ($relations as $row) {
		$map_parent_to_children[$row['masta_japanese_root_id_parent']][] = $row;
	}

	foreach($arr_grammar_usage_categories as $loop_grammar_usage_categories){

		$arr_strSQL_select = [
			[$t_grammar_usage_parents,'usage_category_id'],
			[$t_grammar_usage_parents,'masta_japanese_root_id']
		];

		$strSQL_from = ' FROM ' .$t_grammar_usage_parents;

		$arr_strSQL_where = [
			[
				[
					[$t_grammar_usage_parents,'usage_category_id','=',$loop_grammar_usage_categories['id'],'PDO::PARAM_INT','And'],
					[$t_grammar_usage_parents,'tier','=',$int_masta_grammar_usage_tier_root,'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_strSQL_order = [];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_grammar_usage_parents) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

		if(empty($arr_grammar_usage_parents)){
			continue;
		}

		$str_grammar_outline = '';

		$contents_tree_flags['doDisplayDerivedGrammars'] = true;
		$contents_tree_flags['displayInGrammarView'] = true;
		$t_masta_japanese_root_id = $arr_grammar_usage_parents[INDEX_FIRST]['masta_japanese_root_id'];
		$root_id = $t_masta_japanese_root_id;
		$i = INDEX_FIRST;
		$seen = [];
		$seen_target_table = $t_masta_japanese_root;
		$did_build_grammar_outline = false;
		$arr_belongs = [
			$int_grammar_outline_status,
			$int_hidden_in_grammar_outline
		];

		list($seen, $str_grammar_outline, $did_build_grammar_outline) = recursive_build_html_grammar_outline($contents_tree_flags, $i, $seen, $seen_target_table, $did_build_grammar_outline, $root_id, $t_masta_japanese_root_id, $arr_allow_display, $arr_bookmarks_data, $arr_belongs, $map_parent_to_children, $int_selected_language);

		if(!$did_build_grammar_outline){
			continue;
		}

		$str_grammar_outline = '
		<div class="grammarOutline grammarOutlineTopElement">'.
			$str_grammar_outline.'
		</div>';
		
		if($draw_details){
			$details_class = 'grammarOutlineDetails animationSlideIn';
			$summary_class = 'grammarOutlineDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
			$details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';
	
			$str_grammar_outline_heading = $loop_grammar_usage_categories[$arr_columns_grammar_usage_categories[$int_selected_language]];
			$str_grammar_outline_heading = escape_html_with_nl2br($str_grammar_outline_heading);
			$str_grammar_outline = build_html_details_contents($str_grammar_outline, $str_grammar_outline_heading, $details_class, $summary_class, $details_div_class);
	
			$str_grammar_outline = '<div class="divFrame">' . $str_grammar_outline . '</div>';
		}

		$str_grammar_usages_tree = $str_grammar_usages_tree.$str_grammar_outline;

	}
	return $str_grammar_usages_tree;
}


function fetch_arr_grammar_usage_parents_by_attribute($t_masta_japanese_root_id, $attribute_id, $search_target, $int_selected_language){

	global
		$t_grammar_usage_children,
		$t_grammar_usage_parents;


	$arr_grammar_usage_parents = [];

	$arr_strSQL_select = [
		[$t_grammar_usage_parents,'usage_category_id'],
		[$t_grammar_usage_parents,'masta_japanese_root_id'],
		[$t_grammar_usage_children,$search_target]
	];

	$strSQL_from = " FROM
					$t_grammar_usage_children
					INNER JOIN $t_grammar_usage_parents
					ON
					$t_grammar_usage_parents.masta_japanese_root_id = $t_grammar_usage_children.parent_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_grammar_usage_children,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','And'],
				[$t_grammar_usage_children,$search_target,'=',$attribute_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_grammar_usage_parents) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_grammar_usage_parents;
}


function fetch_arr_grammar_usage_children_by_attribute($parent_root_id, $attribute_id, $map_parent_to_children, $int_selected_language) {
    $children = $map_parent_to_children[$parent_root_id] ?? [];
    $result = [];
    foreach ($children as $row) {
        if ($row['grammar_outline_status'] == $attribute_id) {
            $result[] = [
                'masta_japanese_root_id' => $row['masta_japanese_root_id_child'],
                'grammar_outline_status' => $row['grammar_outline_status'],
                'sort' => $row['sort'] ?? SORT_FIRST
            ];
        }
    }
    usort($result, fn($a, $b) => $a['sort'] <=> $b['sort']);
    return $result;
}