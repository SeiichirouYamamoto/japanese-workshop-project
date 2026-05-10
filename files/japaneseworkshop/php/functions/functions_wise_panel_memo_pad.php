<?php

/******************************************************
 *  BODY
 *
 ******************************************************/

function build_html_wise_memo_pad_body($int_selected_language) {

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
		build_html_wise_panel_close_button('wisePanelMemoPadViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelMemoPadViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelMemoPadViewSplitButton'),
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        'memo',
        $buttons_left,
        $buttons_right,
        $header_content
    );

    $html_toolbar_contents = ''
    . '<div class="wisePanelMemoPadViewDateRow">'
        . '<select id="wisePanelMemoPadViewDateSelect" class="wisePanelMemoPadViewDateSelect" aria-label="Memo date select">'
            . '<option value="" data-fixed="1">---</option>'
        . '</select>'

        . '<button'
            . ' id="wisePanelMemoPadViewConfirmButton"'
            . ' type="button"'
            . ' class="wisePanelMemoPadViewConfirmButton"'
        . '>'
            . 'Confirm'
        . '</button>'
    . '</div>'

    . '<div id="wisePanelMemoPadViewStatus" class="wisePanelMemoPadViewStatus" aria-live="polite"></div>';

    $html_toolbar = build_html_wise_panel_toolbar(
        $html_toolbar_contents,
        'wisePanelMemoPadViewToolbar',
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '',
        'wisePanelMemoPadViewTextareaContainer',
        'wisePanelMemoPadViewTextareaContainer'
    );

    $html_main_content_container = build_html_wise_panel_main_content_container(
		$html_main_content_area,
		build_html_loading_spinner('wisePanelMemoPadViewLoading'),
		'wisePanelMemoPadViewMainContainer'
	);

    return build_html_wise_panel_view(
        'wisePanelMemoPadView',
        $html_header,
        $html_toolbar,
        $html_main_content_container
    );
}