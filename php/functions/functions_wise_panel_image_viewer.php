<?php

/******************************************************
 *  image_viewer_panel
 ******************************************************/

function build_html_wise_image_viewer_body($int_selected_language)
{
    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        build_html_wise_panel_close_button('wisePanelImageViewerViewCloseButton'),
        build_html_wise_panel_expand_button('wisePanelImageViewerViewExpandButton'),
        build_html_wise_panel_split_button('wisePanelImageViewerViewSplitButton'),
    ];

    $buttons_right = [
        '<button id="wisePanelImageViewerViewOpenFileButton" class="wisePanelImageViewerHeaderButton" type="button">Open</button>',
        '<button id="wisePanelImageViewerViewZoomInButton" class="wisePanelImageViewerHeaderButton wisePanelZoomButton" type="button">' . $html_zoom_in_icon . '</button>',
        '<button id="wisePanelImageViewerViewZoomOutButton" class="wisePanelImageViewerHeaderButton wisePanelZoomButton" type="button">' . $html_zoom_out_icon . '</button>',
    ];

    $html_header = build_html_wise_panel_header(
        'Image Viewer',
        $buttons_left,
        $buttons_right,
        '',
        'wisePanelImageViewerHeader',
        'wisePanelHeader wisePanelImageViewerHeader',
        [],
        'wisePanelTitle'
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        build_html_wise_image_viewer_main_content(),
        'wisePanelImageViewerViewMainContentArea',
        'wisePanelMainContentArea wisePanelImageViewerViewMainContentArea'
    );

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        build_html_loading_spinner('wisePanelImageViewerViewLoading'),
        'wisePanelImageViewerViewMainContentContainer',
        'wisePanelMainContentContainer wisePanelImageViewerViewMainContentContainer'
    );

    return build_html_wise_panel_view(
        'wisePanelImageViewerView',
        $html_header,
        '',
        $html_main_content_container,
        '',
        'wisePanelView wisePanelImageViewerView',
        'wisePanelViewContents wisePanelImageViewerViewContents'
    );
}

function build_html_wise_image_viewer_main_content()
{
    return
    '<input'
        . ' id="wisePanelImageViewerFileInput"'
        . ' class="wisePanelImageViewerFileInput"'
        . ' type="file"'
        . ' accept="image/*,application/pdf"'
        . ' hidden'
    . '>'.

    '<div id="wisePanelImageViewerContainer" class="wisePanelImageViewerContainer">'.

        '<div id="wisePanelImageViewerZoomStage" class="wisePanelImageViewerZoomStage">'.

            '<div id="wisePanelImageViewerImageContainer" class="wisePanelImageViewerContentContainer hidden">'.
                '<canvas'
                    . ' id="wisePanelImageViewerImageCanvas"'
                    . ' class="wisePanelImageViewerImageCanvas"'
                . '></canvas>'.
            '</div>'.

            '<div id="wisePanelImageViewerPdfContainer" class="wisePanelImageViewerContentContainer hidden">'.
            '</div>'.

        '</div>'.

    '</div>';
}