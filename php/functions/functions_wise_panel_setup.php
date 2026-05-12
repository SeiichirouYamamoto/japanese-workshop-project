<?php

function build_html_wise_setup_body($int_selected_language)
{
    global
        $arr_str_button_caption_confirm;

    $arr_str_record_lesson_toggle = ['授業として記録する', '記錄為課程'];
    $arr_str_select_room_title = ['ルーム選択', '選擇房間'];
    $arr_str_select_lesson_date_title = ['日付選択', '選擇日期'];

    $arr_str_select_whiteboard_title = ['ホワイトボード選択', '選擇白板'];
    $arr_str_whiteboard_select_empty = ['---', '---'];
    $arr_str_whiteboard_select_create_new = ['Create New', 'Create New'];

    $arr_str_lesson_date_select_empty = ['---', '---'];
    $arr_str_lesson_date_select_create_new = ['Create New', 'Create New'];

    $arr_str_current_lesson_info_title = ['現在利用中のレッスン', '目前使用中的課程'];
    $arr_str_current_lesson_info_empty = ['未選択', '未選擇'];

    $arr_str_current_whiteboard_info_title = ['現在利用中のホワイトボード', '目前使用中的白板'];
    $arr_str_current_whiteboard_info_empty = ['未選択', '未選擇'];

    $buttons_left = [
		build_html_wise_panel_close_button('wisePanelWiseSetupViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelWiseSetupViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelWiseSetupViewSplitButton'),
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        'WISE setup',
        $buttons_left,
        [],
		$header_content,
        '',
    );

    $contents = '';

    $contents .= '
    <div id="wisePanelWiseSetupViewRecordLessonToggleArea" class="wiseSetupSection">
        <div class="wiseSetupSectionTitle">' . $arr_str_record_lesson_toggle[$int_selected_language] . '</div>

        <div class="wiseSetupSectionContent">
            <div class="wiseSetupToggleButtonContainer">
                <input
                    id="wisePanelWiseSetupViewRecordLessonToggleInput"
                    type="checkbox"
                    class="wiseSetupToggleButton"
                >
                <label
                    for="wisePanelWiseSetupViewRecordLessonToggleInput"
                    class="wiseSetupToggleLabel"
                ></label>
            </div>
        </div>
    </div>
    ';

    $contents .= '
    <div id="wisePanelWiseSetupViewDropDownSelectRoomArea" class="wiseSetupSection hidden">
        <div class="wiseSetupSectionTitle">' . $arr_str_select_room_title[$int_selected_language] . '</div>
        <div class="wiseSetupSectionContent">
            <select id="wisePanelWiseSetupViewSelectRoom"></select>
            <button id="wisePanelWiseSetupViewSelectRoomConfirmButton">' . $arr_str_button_caption_confirm[$int_selected_language] . '</button>
        </div>
    </div>
    ';

    $contents .= '
    <div id="wisePanelWiseSetupViewDropDownSelectLessonDateArea" class="wiseSetupSection hidden">
        <div class="wiseSetupSectionTitle">' . $arr_str_select_lesson_date_title[$int_selected_language] . '</div>
        <div class="wiseSetupSectionContent">
            <select id="wisePanelWiseSetupViewSelectLessonDate">
                <option value="" data-fixed="1">' . $arr_str_lesson_date_select_empty[$int_selected_language] . '</option>
                <option value="create_new" data-fixed="1">' . $arr_str_lesson_date_select_create_new[$int_selected_language] . '</option>
            </select>
            <button id="wisePanelWiseSetupViewSelectLessonDateConfirmButton">' . $arr_str_button_caption_confirm[$int_selected_language] . '</button>
        </div>
    </div>
    ';

    $contents .= '
    <div id="wisePanelWiseSetupViewDropDownSelectWhiteboardArea" class="wiseSetupSection hidden">
        <div class="wiseSetupSectionTitle">' . $arr_str_select_whiteboard_title[$int_selected_language] . '</div>
        <div class="wiseSetupSectionContent">
            <select id="wisePanelWiseSetupViewSelectWhiteboard">
                <option value="" data-fixed="1">' . $arr_str_whiteboard_select_empty[$int_selected_language] . '</option>
                <option value="create_new" data-fixed="1">' . $arr_str_whiteboard_select_create_new[$int_selected_language] . '</option>
            </select>
            <button id="wisePanelWiseSetupViewSelectWhiteboardConfirmButton">' . $arr_str_button_caption_confirm[$int_selected_language] . '</button>
        </div>
    </div>
    ';

    $contents .= '
    <div class="wiseSetupCurrentLessonInfoArea" style="margin-top: 8px;">
        <div class="wiseSetupSectionTitle">' . $arr_str_current_lesson_info_title[$int_selected_language] . '</div>
        <div class="wiseSetupSectionContent">
            <div
                id="wisePanelWiseSetupViewCurrentLessonInfo"
                class="wisePanelWiseSetupViewCurrentLessonInfo"
                aria-live="polite"
                data-empty-text="' . $arr_str_current_lesson_info_empty[$int_selected_language] . '"
            >' . $arr_str_current_lesson_info_empty[$int_selected_language] . '</div>
        </div>
    </div>
    ';

    $contents .= '
    <div class="wiseSetupCurrentWhiteboardInfoArea" style="margin-top: 8px;">
        <div class="wiseSetupSectionTitle">' . $arr_str_current_whiteboard_info_title[$int_selected_language] . '</div>
        <div class="wiseSetupSectionContent">
            <div
                id="wisePanelWiseSetupViewCurrentWhiteboardInfo"
                class="wisePanelWiseSetupViewCurrentWhiteboardInfo"
                aria-live="polite"
                data-empty-text="' . $arr_str_current_whiteboard_info_empty[$int_selected_language] . '"
            >' . $arr_str_current_whiteboard_info_empty[$int_selected_language] . '</div>
        </div>
    </div>
    ';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        $contents,
        'wisePanelWiseSetupViewMainContentArea',
        'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = build_html_loading_spinner('wisePanelWiseSetupViewLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWiseSetupViewMainContentContainer'
    );

    return build_html_wise_panel_view(
        'wisePanelWiseSetupView',
        $html_header,
        '',
        $html_main_content_container,
        '',
        'wisePanelView',
        'wisePanelViewContents'
    );
}