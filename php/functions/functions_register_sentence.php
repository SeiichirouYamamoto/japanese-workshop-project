<?php

/******************************************************
 *  REGISTER SENTENCE PAGE
 *
 *  PAGE
 *  ├ panel_container_layer
 *  │  ├ whiteboard_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  ├ grammar_explanation_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  └ memo_pad_panel
 *  │     ├ panel_body
 *  │     └ panel_ui
 *  ├ global_overlay_layer
 *  └ hud_layer
 ******************************************************/

/**
 * Register Sentence 用 Whiteboard panel UI
 * Whiteboard 上で使う補助UIだけ残す
 */
function build_html_register_sentence_whiteboard_panel_ui_contents($int_selected_language){

    return [
        build_html_wise_whiteboard_ui_zoom(),

        build_html_wise_whiteboard_ui_context_menu($int_selected_language),

        build_html_wise_whiteboard_ui_form_list($int_selected_language),
        build_html_wise_whiteboard_ui_settings($int_selected_language),
        build_html_wise_whiteboard_ui_label_list($int_selected_language),

        build_html_wise_whiteboard_ui_create_sticky_note($int_selected_language),

        build_html_wise_whiteboard_ui_word_information($int_selected_language),
        build_html_wise_whiteboard_ui_word_search_set($int_selected_language),
        build_html_wise_whiteboard_ui_create_new_word($int_selected_language),
        build_html_wise_whiteboard_ui_registered_items($int_selected_language),

        build_html_wise_whiteboard_ui_history($int_selected_language),
        build_html_wise_whiteboard_ui_created_word_history($int_selected_language),
        build_html_wise_whiteboard_ui_chart_history($int_selected_language),
        build_html_wise_whiteboard_ui_action_history($int_selected_language),

    ];
}


/**
 * Register Sentence 用 panel body
 */
function build_html_register_sentence_whiteboard_panel_body($int_selected_language){

	return build_html_wise_whiteboard_body($int_selected_language);

}

/**
 * Register Sentence 用 panel
 */
function build_html_register_sentence_whiteboard_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelWhiteboard',
        'wisePanel-whiteboard',
        build_html_register_sentence_whiteboard_panel_body($int_selected_language),
        build_html_register_sentence_whiteboard_panel_ui_contents($int_selected_language)
    );
}




/**
 * Register Sentence 用 panel overlay 無し
 */

/**
 * Register Sentence 用 HUD
 */
function build_html_register_sentence_hud_contents($arr_target_menus, $int_selected_language){

    return [
        build_html_wise_vertical_toolbar(true, $int_selected_language),
    ];
}

/**
 * Register Sentence 用 overlay
 */
function build_html_register_sentence_super_overlay_contents($int_selected_language){

    return [
        build_html_wise_ui_lock_overlay()
    ];
}

/**
 * Register Sentence 用 PAGE
 */
function build_html_register_sentence_page($int_selected_language){

    global
        $str_wiseRightVerticalToolbarButton_id_whiteboard,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation,
        $str_wiseRightVerticalToolbarButton_id_memoPad,
        $str_wiseRightVerticalToolbarButton_id_chart,
        $str_wiseRightVerticalToolbarButton_id_quiz,
        $str_wiseRightVerticalToolbarButton_id_map,
        $str_wiseRightVerticalToolbarButton_id_imageViewer,
        $str_wiseRightVerticalToolbarButton_id_lessonContents,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi;

    $arr_visible_right_buttons = [
        $str_wiseRightVerticalToolbarButton_id_whiteboard => false,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup => false,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation => false,
        $str_wiseRightVerticalToolbarButton_id_memoPad => false,
        $str_wiseRightVerticalToolbarButton_id_chart => false,
        $str_wiseRightVerticalToolbarButton_id_quiz => false,
        $str_wiseRightVerticalToolbarButton_id_map => false,
        $str_wiseRightVerticalToolbarButton_id_imageViewer => false,
        $str_wiseRightVerticalToolbarButton_id_lessonContents => false,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights => false,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi => false,
    ];

    $arr_panels = [
		build_html_register_sentence_whiteboard_panel($int_selected_language)
	];

    $str_panel_container_layer = build_html_wise_panel_container_layer(
        $arr_panels
    );

    $str_panel_overlay_layer = '';

    $str_hud_layer = build_html_wise_hud_layer(
        build_html_register_sentence_hud_contents($arr_visible_right_buttons, $int_selected_language)
    );

    $str_super_overlay_layer = build_html_wise_super_overlay_layer(
        build_html_wise_super_overlay_bundle(
            build_html_register_sentence_super_overlay_contents($int_selected_language)
        )
    );

    return build_html_wise_page_shell(
        'sectionWise',
        [
			$str_panel_container_layer,
			$str_panel_overlay_layer,
			$str_hud_layer,
			$str_super_overlay_layer
		]
    );
}


/******************************************************
 *  EDIT REGISTERED SENTENCE PAGE
 ******************************************************/


/**
 * Edit Registered Sentence 用 Whiteboard panel UI
 */
function build_html_edit_registered_sentence_whiteboard_panel_ui_contents($int_selected_language){

    return [
        build_html_wise_whiteboard_ui_zoom(),

        build_html_wise_whiteboard_ui_context_menu($int_selected_language),

        build_html_wise_whiteboard_ui_form_list($int_selected_language),
        build_html_wise_whiteboard_ui_settings($int_selected_language),
        build_html_wise_whiteboard_ui_label_list($int_selected_language),

        build_html_wise_whiteboard_ui_create_sticky_note($int_selected_language),

        build_html_wise_whiteboard_ui_word_information($int_selected_language),
        build_html_wise_whiteboard_ui_word_search_set($int_selected_language),
        build_html_wise_whiteboard_ui_create_new_word($int_selected_language),
        build_html_wise_whiteboard_ui_registered_items($int_selected_language),

        build_html_wise_whiteboard_ui_history($int_selected_language),
        build_html_wise_whiteboard_ui_created_word_history($int_selected_language),
        build_html_wise_whiteboard_ui_chart_history($int_selected_language),
        build_html_wise_whiteboard_ui_action_history($int_selected_language),

    ];
}


/**
 * Edit Registered Sentence 用 panel body
 */
function build_html_edit_registered_sentence_whiteboard_panel_body($int_selected_language){

	return build_html_wise_whiteboard_body($int_selected_language);
	
}


/**
 * Edit Registered Sentence 用 panel
 */
function build_html_edit_registered_sentence_whiteboard_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelWhiteboard',
        'wisePanel-whiteboard',
        build_html_edit_registered_sentence_whiteboard_panel_body($int_selected_language),
        build_html_edit_registered_sentence_whiteboard_panel_ui_contents($int_selected_language)
    );
}


/**
 * Edit Registered Sentence 用 panel overlay 無し
 */


/**
 * Edit Registered Sentence 用 HUD
 */
function build_html_edit_registered_sentence_hud_contents($arr_target_menus, $int_selected_language){

    return [
        build_html_wise_vertical_toolbar_for_editing($int_selected_language),
    ];
}

/**
 * Edit Registered Sentence 用 overlay
 */
function build_html_edit_registered_sentence_super_overlay_contents($int_selected_language){

    return [
        build_html_wise_ui_lock_overlay()
    ];
}


/**
 * Edit Registered Sentence 用 PAGE
 */

function build_html_edit_registered_sentence_page($int_selected_language){

    global
        $str_wiseRightVerticalToolbarButton_id_whiteboard,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation,
        $str_wiseRightVerticalToolbarButton_id_memoPad,
        $str_wiseRightVerticalToolbarButton_id_chart,
        $str_wiseRightVerticalToolbarButton_id_quiz,
        $str_wiseRightVerticalToolbarButton_id_map,
        $str_wiseRightVerticalToolbarButton_id_imageViewer,
        $str_wiseRightVerticalToolbarButton_id_lessonContents,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi;

    $arr_visible_right_buttons = [
        $str_wiseRightVerticalToolbarButton_id_whiteboard => false,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup => false,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation => false,
        $str_wiseRightVerticalToolbarButton_id_memoPad => false,
        $str_wiseRightVerticalToolbarButton_id_chart => false,
        $str_wiseRightVerticalToolbarButton_id_quiz => false,
        $str_wiseRightVerticalToolbarButton_id_map => false,
        $str_wiseRightVerticalToolbarButton_id_imageViewer => false,
        $str_wiseRightVerticalToolbarButton_id_lessonContents => false,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights => false,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi => false,
    ];

    $arr_panels = [
        build_html_edit_registered_sentence_whiteboard_panel($int_selected_language)
    ];

    $str_panel_container_layer = build_html_wise_panel_container_layer(
        $arr_panels
    );

    $str_panel_overlay_layer = '';

    $str_hud_layer = build_html_wise_hud_layer(
        build_html_edit_registered_sentence_hud_contents($arr_visible_right_buttons, $int_selected_language)
    );

    $str_super_overlay_layer = build_html_wise_super_overlay_layer(
        build_html_wise_super_overlay_bundle(
            build_html_edit_registered_sentence_super_overlay_contents($int_selected_language)
        )
    );

    return build_html_wise_page_shell(
        'sectionWise',
        [
			$str_panel_container_layer,
			$str_panel_overlay_layer,
			$str_hud_layer,
			$str_super_overlay_layer
		]
    );
}