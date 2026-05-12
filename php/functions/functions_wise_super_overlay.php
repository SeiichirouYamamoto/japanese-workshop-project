<?php

/**
 * Wise Overlay中身
 */
function build_html_wise_main_super_overlay_contents($int_selected_language){

    return [
        build_html_wise_start_overlay($int_selected_language, 'wait', 'SCANNING...'),
        build_html_wise_ui_lock_overlay(),
    ];
}


/**
 * Overlay bundle
 */
function build_html_wise_super_overlay_bundle($contents = '', $bundle_id = 'wiseSuperOverlayBundle'){

    return '
    <div id="'.$bundle_id.'" class="wiseSuperOverlayBundle">'.
        build_html_wise_join_contents($contents).
    '</div>';
}



/**
 * UI lock overlay
 */
function build_html_wise_ui_lock_overlay($overlay_id = 'wiseUiLockOverlay'){

    return '
    <div id="'.$overlay_id.'" class="wiseSuperOverlayScreen">' .
		build_html_loading_spinner('uiLockLoading') .
	'</div>';
}


function build_html_wise_start_overlay(
    $int_selected_language = INDEX_FIRST,
    $type = 'wait',
    $label_scan = 'SCANNING...',
    $options = []
){
	global
		$path_images_wise,
		$arr_wise_navigation_messages;
	
	$url_images_wise = get_home_url(
		get_main_site_id(),
		trailingslashit(ltrim($path_images_wise, '/'))
	);

    $type = ($type === 'opening') ? 'opening' : 'wait';
    $overlay_id = ($type === 'opening') ? 'wiseOpeningOverlay' : 'wiseWaitOverlay';
    $logo_id = ($type === 'opening') ? 'wiseOpeningOverlayLogo' : 'wiseWaitOverlayLogo';
    $extra_class = $options['extra_class'] ?? '';
    $logo_src = $url_images_wise . 'wise_logo.png';
    $user_opening_message_base = $options['user_opening_message'] ?? '';
    $topic_message = $options['topic_message'] ?? '';

    $str = '';
    $str_wiseNaviEventOverlay = '';

    $str .= '
    <div id="' . $overlay_id . '" class="wiseStartOverlay overlay-on wiseOverlay--' . $type . ' ' . $extra_class . '" data-overlay="' . $type . '">
        <img id="' . $logo_id . '" class="wiseStartOverlayLogo" src="' . jws_add_file_version($logo_src) . '" alt="W.I.S.E. Logo">';

    if ($type === 'opening') {
        if ($user_opening_message_base !== '') {

			if (!empty($topic_message)) {
				$str .= '
				<div id="wiseStartOverlayTopicMessage"
					class="wiseStartOverlayMessage wiseNaviMessage wiseNaviMessageTopic wiseUiFontSizeTarget is-typing wiseCyanGlowRing"
					data-original-text="' . escape_html($topic_message) . '">
					<span class="wiseTypingText" aria-live="polite" role="status">' . escape_html($topic_message) . '</span>
				</div>';
			}

            $arr_messages = $arr_wise_navigation_messages[$int_selected_language] ?? [];
            $arr_processed_messages = [];
            foreach ($arr_messages as $section_key => $section_messages) {
                $arr_processed_messages[$section_key] = [];
                foreach ($section_messages as $msg) {
                    $arr_processed_messages[$section_key][] = strtr($msg, [
                        '{sentence}' => $user_opening_message_base
                    ]);
                }
            }

            if (!empty($arr_processed_messages['opening'])) {
                $sort = INDEX_FIRST;
                foreach ($arr_processed_messages['opening'] as $msg) {
                    $str .= '
                    <div id="wiseStartOverlayOpeningMessage' . $sort . '"
                         class="wiseStartOverlayMessage wiseNaviMessage wiseNaviMessageOpening wiseUiFontSizeTarget"
                         data-sort="' . $sort . '"
                         data-original-text="' . escape_html($msg) . '"></div>';
                    $sort++;
                }
            }

            if (!empty($arr_processed_messages['ending'])) {
                $sort = INDEX_FIRST;
                foreach ($arr_processed_messages['ending'] as $msg) {
                    $str .= '
                    <div id="wiseStartOverlayWiseEndingMessage' . $sort . '"
                         class="wiseStartOverlayMessage wiseNaviMessage wiseNaviMessageEnding wiseUiFontSizeTarget"
                         data-sort="' . $sort . '"
                         data-original-text="' . escape_html($msg) . '"></div>';
                    $sort++;
                }
            }
        }

        $str_wiseNaviEventOverlay = '
        <div id="wiseNaviEventOverlay">
            <div id="wiseNaviNextLabel" class="white-text">NEXT &gt;&gt;</div>
        </div>';
    }

    $str .= '
    </div>
    <div id="wiseScanOverlay" class="wiseStartOverlay overlay-on" aria-hidden="true" inert>
        <div id="wiseScanline" class="wiseDecorativeItem" aria-hidden="true"></div>
        <div id="wiseScanLabel" aria-hidden="true">' . escape_html($label_scan) . '</div>
    </div>
    ';

    $str .= $str_wiseNaviEventOverlay;

    return $str;
}