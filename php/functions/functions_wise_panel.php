<?php


/******************************************************
 *  PAGE
 *
 *  第一層
 *  Panel Container
 *
 *  第二層
 *  Panel Overlay
 *
 *  第三層
 *  HUD
 * 
 *  第四層
 *  Super Overlay
 *
 *  WISE BASE
 *  ├ panel_container_layer
 *  │  ├ whiteboard_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  ├ grammar_explanation_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  ├ lesson_contents_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  ├ memo_pad_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  ├ wise_setup_panel
 *  │  │  ├ panel_body
 *  │  │  └ panel_ui
 *  │  └ panel_splitter
 *  ├ panel_overlay_layer
 *  ├ hud_layer
 *  └ super_overlay_layer

 ******************************************************/

/******************************************************
 *  PANEL INTERNAL OUTLINE
 *
 *  PANEL
 *  ├ panel_body
 *  │  └ panel_view
 *  │    └ wisePanel[PanelName]View
 *  │       └ wisePanel[PanelName]ViewContents
 *  │          ├ wisePanel[PanelName]Header
 *  │          ├ wisePanel[PanelName]Toolbar
 *  │          ├ wisePanel[PanelName]MainContentContainer
 *  │          │  ├ wisePanel[PanelName]MainContentArea
 *  │          │  └ wisePanel[PanelName]Loading
 *  │          └ wisePanel[PanelName]ViewHandle 
 *  │ 
 *  └ panel_ui_area
 *    ├ panel_ui  side 
 *    │  └ wisePanel[PanelName]Ui
 *    │     └ wisePanel[PanelName]UiContents
 *    │        ├ wisePanel[PanelName]Header
 *    │        ├ wisePanel[PanelName]Toolbar
 *    │        └ wisePanel[PanelName]MainContentContainer
 *    │           ├ wisePanel[PanelName]MainContentArea
 *    │           └ wisePanel[PanelName]Loading
 *    │ 
 *    └ panel_ui  float 
 *       └ wisePanel[PanelName]Ui
 *          └ wisePanel[PanelName]UiContents 
 *             └ wisePanel[PanelName][UiName]
 ******************************************************/

/******************************************************
 *  POINTER EVENTS DESIGN SYSTEM
 *
 * wiseMainBaseLayer       メインコンテンツを入れるレイヤー イベントを受ける 
 * wisePassThroughLayer    親はイベントを通す 
 * wiseHitItem             クリック可能な要素 
 * wiseDecorativeItem      見た目専用（クリック不可） 
 * panel_ui_area           ボディの上に行くので、イベントを通す
 ******************************************************/

/**
 * 汎用レイヤー
 */

function build_html_wise_base_layer(
    $layer_id,
    $layer_class,
    $contents = '',
    $is_pass_through = false
){

    $pass_class = $is_pass_through ? ' wisePassThroughLayer' : '';

    return '
    <div id="'.$layer_id.'" class="wiseBaseLayer '.$layer_class.$pass_class.'">'.
        build_html_wise_join_contents($contents).
    '</div>';
}

/**
 * 第一層 Panel Container
 */
function build_html_wise_panel_container_layer(
    $contents = '',
    $layer_id = 'wiseBaseLayerPanelContainer'
){

    return build_html_wise_base_layer(
        $layer_id,
        'wiseMainBaseLayer wiseBaseLayer-panel-container',
        '
        <div id="wisePanelContainerLayout" class="wisePanelContainerLayout" data-layout="single">'.
            build_html_wise_join_contents([
                build_html_wise_join_contents($contents),
                build_html_wise_panel_splitter()
            ]).
        '</div>',
        false
    );
}


/**
 * 第二層 Panel Overlay
 */
function build_html_wise_panel_overlay_layer($contents = '', $layer_id = 'wiseBaseLayerPanelOverlay'){

    return build_html_wise_base_layer(
        $layer_id,
        'wiseBaseLayer-panel-overlay',
        $contents,
        true
    );
}


/**
 * 第三層 HUD
 */
function build_html_wise_hud_layer($contents = '', $layer_id = 'wiseBaseLayerHud'){

    return build_html_wise_base_layer(
        $layer_id,
        'wiseBaseLayer-hud',
        $contents,
        true
    );
}

/**
 * 第四層 Super Overlay
 */
function build_html_wise_super_overlay_layer($contents = '', $layer_id = 'wiseBaseLayerSuperOverlay'){

    return build_html_wise_base_layer(
        $layer_id,
        'wiseBaseLayer-super-overlay',
        $contents,
        true
    );
}


/**
 * section 全体骨格
 */
function build_html_wise_page_shell(
    $section_id,
    $layers = []
){

    return '
    <section id="'.$section_id.'" class="wise-require-fullscreen">'.
        build_html_wise_join_contents($layers).
    '</section>';
}

/**
 * 汎用 Panel Body
 */
function build_html_wise_panel_body($contents = '', $area_id = ''){

    return '
    <div id="'.$area_id.'" class="wisePanelBody">'.
        build_html_wise_join_contents($contents).
    '</div>';
}


/**
 * 汎用 Panel UI
 */
function build_html_wise_panel_ui_area($contents = '', $area_id = ''){

    return '
    <div id="'.$area_id.'" class="wisePanelUiArea wisePointerNone">'.
        build_html_wise_join_contents($contents).
    '</div>';
}


/**
 * 汎用 Panel
 */
function build_html_wise_panel($panel_id, $panel_class, $body_contents = '', $ui_contents = ''){

    return '
    <div id="'.$panel_id.'" class="wisePanel '.$panel_class.'">'.
        build_html_wise_join_contents([
            build_html_wise_panel_body($body_contents, $panel_id.'Body'),
            build_html_wise_panel_ui_area($ui_contents, $panel_id.'UiArea')
        ]).
    '</div>';
}

/**
 * 汎用 Panel Splitter
 */
function build_html_wise_panel_splitter(
    $splitter_id = 'wisePanelLayoutSplitter',
    $splitter_class = 'wisePanelLayoutSplitter'
){

    return '
    <div id="'.$splitter_id.'" class="'.$splitter_class.'" data-direction="" aria-hidden="true"></div>';
}


/**
 * whiteboard_panel
 */
function build_html_wise_whiteboard_panel_body($int_selected_language){

	return build_html_wise_whiteboard_body($int_selected_language);

}

function build_html_wise_whiteboard_panel_ui_contents($int_selected_language){

	return build_html_wise_whiteboard_ui($int_selected_language);

}

function build_html_wise_whiteboard_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelWhiteboard',
        'wisePanel-whiteboard',
        build_html_wise_whiteboard_panel_body($int_selected_language),
        build_html_wise_whiteboard_panel_ui_contents($int_selected_language)
    );
}

/**
 * grammar_explanation_panel
 */
function build_html_wise_grammar_explanation_panel_body($int_selected_language){

    return build_html_wise_grammar_explanation_body(true, $int_selected_language);
}

function build_html_wise_grammar_explanation_panel_ui_contents($int_selected_language){

    return build_html_wise_grammar_explanation_ui( $int_selected_language);
}

function build_html_wise_grammar_explanation_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelGrammarExplanation',
        'wisePanel-grammarExplanation',
        build_html_wise_grammar_explanation_panel_body($int_selected_language),
        build_html_wise_grammar_explanation_panel_ui_contents($int_selected_language)
    );
}

/**
 * lesson_contents_panel
 */
function build_html_wise_lesson_contents_panel_body($int_selected_language){

    return build_html_wise_lesson_contents_body($int_selected_language);
}

function build_html_wise_lesson_contents_panel_ui_contents($int_selected_language){

    return build_html_wise_lesson_contents_ui($int_selected_language);
}

function build_html_wise_lesson_contents_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelLessonContents',
        'wisePanel-lessonContents',
        build_html_wise_lesson_contents_panel_body($int_selected_language),
        build_html_wise_lesson_contents_panel_ui_contents($int_selected_language)
    );
}

/**
 * grammar_insights_panel
 */
function build_html_wise_grammar_insights_panel_body($int_selected_language){

    return build_html_wise_grammar_insights_body($int_selected_language);
}

function build_html_wise_grammar_insights_panel_ui_contents($int_selected_language){

    return build_html_wise_grammar_insights_ui($int_selected_language);
}

function build_html_wise_grammar_insights_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelGrammarInsights',
        'wisePanel-grammarInsights',
        build_html_wise_grammar_insights_panel_body($int_selected_language),
        build_html_wise_grammar_insights_panel_ui_contents($int_selected_language)
    );
}

/**
 * memo_pad_panel
 */
function build_html_wise_memo_pad_panel_body($int_selected_language){

    return build_html_wise_memo_pad_body($int_selected_language);
}

function build_html_wise_memo_pad_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelMemoPad',
        'wisePanel-memoPad',
        build_html_wise_memo_pad_panel_body($int_selected_language),
        ''
    );
}

/**
 * wise_setup_panel
 */
function build_html_wise_setup_panel_body($int_selected_language){

    return build_html_wise_setup_body($int_selected_language);
}

function build_html_wise_setup_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelWiseSetup',
        'wisePanel-wiseSetup',
        build_html_wise_setup_panel_body($int_selected_language),
        ''
    );
}

/**
 * chart_panel
 */

function build_html_wise_chart_panel_body($int_selected_language)
{
    return build_html_wise_chart_body($int_selected_language);
}

function build_html_wise_chart_panel_ui_contents($int_selected_language)
{
    return build_html_wise_chart_ui($int_selected_language);
}

function build_html_wise_chart_panel($int_selected_language)
{
    return build_html_wise_panel(
        'wisePanelChart',
        'wisePanel-chart',
        build_html_wise_chart_panel_body($int_selected_language),
        build_html_wise_chart_panel_ui_contents($int_selected_language)
    );
}


/**
 * image_viewer
 */
function build_html_wise_image_viewer_panel_body($int_selected_language){

    return build_html_wise_image_viewer_body($int_selected_language);
}

function build_html_wise_image_viewer_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelImageViewer',
        'wisePanel-imageViewer',
        build_html_wise_image_viewer_panel_body($int_selected_language),
        ''
    );
}

/**
 * quiz_panel
 */
function build_html_wise_quiz_panel_body($int_selected_language)
{
    return build_html_wise_quiz_panel_view_main($int_selected_language);
}

function build_html_wise_quiz_panel_ui_contents($int_selected_language)
{

	global $int_mastery_level_jws_beginner;

	$int_mastery_level = $int_mastery_level_jws_beginner;
	$arr_japanese_classification = [];
	$arr_sub_category = [];
	$arr_inflection = [];

	if (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
	}

	if (isset($_SESSION['quiz_settings_sub_category'])) {
		$arr_sub_category = $_SESSION['quiz_settings_sub_category'];
	}

	if (isset($_SESSION['quiz_settings_japanese_classification'])) {
		$arr_japanese_classification = $_SESSION['quiz_settings_japanese_classification'];
	}

	if (isset($_SESSION['quiz_settings_inflection'])) {
		$arr_inflection = $_SESSION['quiz_settings_inflection'];
	}

    return build_html_wise_join_contents([
        build_html_wise_quiz_panel_feedback_overlay_ui($int_selected_language),
		build_html_wise_quiz_panel_settings_overlay_ui(
			$int_mastery_level,
			$arr_sub_category,
			$arr_japanese_classification,
			$arr_inflection,
			$int_selected_language
		),
		build_html_wise_quiz_panel_history_overlay_ui($int_selected_language),
    ]);
}

function build_html_wise_quiz_panel($int_selected_language)
{
    return build_html_wise_panel(
        'wisePanelQuiz',
        'wisePanel-quiz',
        build_html_wise_quiz_panel_body($int_selected_language),
        build_html_wise_quiz_panel_ui_contents($int_selected_language)
    );
}

/******************************************************
 *  Wrapper
 *  
 ******************************************************/

function build_html_wise_panel_toolbar(
    $contents = '',
    $id = '',
    $class = 'wisePanelToolbar',
    $options = []
) {
    $html = '';

    $html .= '<div' . build_html_attributes($id, $class, $options) . '>';
    $html .= $contents;
    $html .= '</div>';

    return $html;
}


function build_html_wise_panel_main_content_area(
    $contents = '',
    $id = '',
    $class = 'wisePanelMainContentArea',
    $options = []
) {
    $html = '';

    $html .= '<div' . build_html_attributes($id, $class, $options) . '>';
    $html .= $contents;
    $html .= '</div>';

    return $html;
}


function build_html_wise_panel_main_content_container(
    $html_main_content_area = '',
    $html_loading = '',
    $id = '',
    $class = 'wisePanelMainContentContainer',
    $options = []
) {
    $html = '';

    $html .= '<div' . build_html_attributes($id, $class, $options) . '>';

        $html .= $html_main_content_area;
        $html .= $html_loading;

    $html .= '</div>';

    return $html;
}



function build_html_wise_panel_header(
    $str_title,
    $buttons_left = [],
    $buttons_right = [],
    $header_content = '',   // ← 追加
    $id = '',
    $class = 'wisePanelHeader',
    $options = [],
    $title_class = 'wisePanelTitle'
) {
    $html = '';

    $html .= '<div' . build_html_attributes($id, $class, $options) . '>';

        // left
        $html .= '<div class="wisePanelHeaderLeft">';
        foreach ($buttons_left as $btn) {
            $html .= $btn;
        }
        $html .= '</div>';

        // title
        $html .= '<div class="' . escape_html($title_class) . '">';
        $html .= escape_html($str_title);
        $html .= '</div>';

        // header_content
        if ($header_content !== '') {
            $html .= '<div class="wisePanelHeaderContent">';
            $html .= $header_content;
            $html .= '</div>';
        }

        // right
        $html .= '<div class="wisePanelHeaderRight">';
        foreach ($buttons_right as $btn) {
            $html .= $btn;
        }
        $html .= '</div>';

    $html .= '</div>';

    return $html;
}


/******************************************************
 *  View Wrapper
 *  
 ******************************************************/
function build_html_wise_panel_view_handle(
    $contents = '',
    $id = '',
    $class = 'wisePanelViewHandle',
    $options = []
) {
    $html = '';

    $html .= '<div' . build_html_attributes($id, $class, $options) . '>';
    $html .= $contents;
    $html .= '</div>';

    return $html;
}


function build_html_wise_panel_view(
    $view_id,
    $html_header = '',
    $html_toolbar = '',
    $html_main_content_container = '',
    $html_handle = '',
    $view_class = 'wisePanelView',
    $view_contents_class = 'wisePanelViewContents'
) {
    $html = '';

    $html .= '<div id="' . escape_html($view_id) . '" class="' . escape_html($view_class) . '">';
        $html .= '<div class="' . escape_html($view_contents_class) . '">';

            if ($html_header !== '') {
                $html .= $html_header;
            }

            if ($html_toolbar !== '') {
                $html .= $html_toolbar;
            }

            if ($html_main_content_container !== '') {
                $html .= $html_main_content_container;
            }

            if ($html_handle !== '') {
                $html .= $html_handle;
            }

        $html .= '</div>';
    $html .= '</div>';

    return $html;
}


/******************************************************
 *  Ui Wrapper
 *  
 ******************************************************/
function build_html_wise_panel_ui_item(
    $ui_id,
    $html_header = '',
    $html_toolbar = '',
    $html_main_content_container = '',
    $html_contents = '',
    $ui_type = 'float',
    $is_hidden = true,
    $ui_class = 'wisePanelUi wiseHitItem',
    $ui_contents_class = 'wisePanelUiContents'
) {
    $allowed_types = ['side', 'float', 'overlay'];

    if (!in_array($ui_type, $allowed_types, true)) {
        $ui_type = 'float';
    }

    $classes = [
        $ui_class,
        'wisePanelUi-' . $ui_type,
    ];

    if ($is_hidden) {
        $classes[] = 'hidden';
    }

    $html = '';

    $html .= '<div id="' . escape_html($ui_id) . '" 
        class="' . escape_html(implode(' ', array_filter($classes))) . '" 
        data-panel-ui-type="' . escape_html($ui_type) . '">';

        $contents_class_attr = '';
        if ($ui_contents_class !== '') {
            $contents_class_attr = ' class="' . escape_html($ui_contents_class) . '"';
        }

        $html .= '<div' . $contents_class_attr . '>';

            if ($html_header !== '') {
                $html .= $html_header;
            }

            if ($html_toolbar !== '') {
                $html .= $html_toolbar;
            }

            if ($html_main_content_container !== '') {
                $html .= $html_main_content_container;
            }

            if ($html_contents !== '') {
                $html .= $html_contents;
            }

        $html .= '</div>';
    $html .= '</div>';

    return $html;
}
/******************************************************
 *  Panel UI Overlay Contents
 ******************************************************/

function build_html_wise_panel_ui_overlay_contents(
    $screen_contents = '',
    $close_button = '',
    $modal_id = '',
    $screen_id = '',
    $modal_class = 'wisePanelUiOverlayModal',
    $screen_class = 'wisePanelUiOverlayScreen'
) {
    $html = '';

    $modal_attr = build_html_attributes($modal_id, $modal_class);
    $screen_attr = build_html_attributes($screen_id, $screen_class);

    $html .= '<div' . $modal_attr . '>';

        if ($close_button !== '') {
            $html .= $close_button;
        }

        $html .= '<div' . $screen_attr . '>';
            $html .= build_html_wise_join_contents($screen_contents);
        $html .= '</div>';

    $html .= '</div>';

    return $html;
}
/******************************************************
 *  button 作成
 *  
 ******************************************************/
function build_html_attr($name, $value)
{
    if (empty($value)) {
        return '';
    }

    return ' ' . $name . '="' . $value . '"';
}

function build_html_attr_class($class)
{
    if (empty($class)) {
        return '';
    }

    if (is_array($class)) {
        $class = array_filter($class);
        $class = implode(' ', $class);
    }

    if ($class === '') {
        return '';
    }

    return ' class="' . $class . '"';
}

function build_html_wise_panel_close_button($id = '', $class = 'wisePanelViewCloseButton', $options = [])
{
    global $str_mark_cross;
	
    $attr = build_html_attributes($id, $class, $options);

    return '<button' . $attr . '>' . $str_mark_cross . '</button>';
}

function build_html_wise_panel_expand_button($id = '', $class = 'wisePanelViewExpandButton', $options = [])
{
    global $str_mark_square;

	$attr = build_html_attributes($id, $class, $options);

	return '<button' . $attr . '>' . $str_mark_square . '</button>';

}

function build_html_wise_panel_split_button($id = '', $class = 'wisePanelViewSplitButton', $options = [])
{
    global $str_mark_split;

	$attr = build_html_attributes($id, $class, $options);

	return '<button' . $attr . '>' . $str_mark_split . '</button>';

}

function build_html_wise_panel_nav_button($id = '', $class = 'wisePanelViewNavigationButton', $label = '', $options = [])
{
    $attr = build_html_attributes($id, $class, $options);

    return '<button' . $attr . '>' . $label . '</button>';
}


function build_html_wise_panel_ui_overlay_close_button($id = '', $class = 'wisePanelUiOverlayCloseButton', $options = []
) {
    global $str_mark_cross;

    $attr = build_html_attributes($id, $class, $options);

    return '<button' . $attr . '>' . $str_mark_cross . '</button>';
}
