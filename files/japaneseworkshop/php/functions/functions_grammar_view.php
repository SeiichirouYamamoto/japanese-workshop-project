<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/
function build_html_grammar_view_page($grammar_unique_code, $room_unique_code, $user_level, $arr_targets_visible, $int_selected_language){

	global
		$int_allow_visible_in_grammar_view,
		$int_not_allow_visible_in_grammar_view,
		$arr_str_web_page_under_preparation,
		$arr_section_grammar_relation,
		$int_masta_japanese_category_id_grammar,
		$int_masta_japanese_category_id_question,
		$search_scope_current_user,
		$int_Free_Member,
		$int_Basic_Student,
		$bookmark_filter_active;

	$str_html = '';

	$t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($grammar_unique_code, $int_selected_language);
	// 未定義id
	if($t_masta_japanese_root_id === 0){
		return $str_html;
	};

	$is_bookmarked = false;

	if (
		$room_unique_code !== '' &&
		$room_unique_code !== $workshop_trial_unique_code &&
		$grammar_unique_code !== ''
	) {
		$arr_bookmarks_data = get_data_bookmarks(
			$search_scope_current_user,
			$room_unique_code,
			$bookmark_filter_active,
			$int_selected_language
		);

		$map_grammar_unique_code = $arr_bookmarks_data['map_grammar_unique_code'] ?? [];

		$is_bookmarked =
			isset($map_grammar_unique_code[$grammar_unique_code]) &&
			empty($map_grammar_unique_code[$grammar_unique_code]['deleted_at']);
	}


	$str_html_header = build_html_grammar_view_header(
		$t_masta_japanese_root_id,
		$grammar_unique_code,
		$room_unique_code,
		(bool)$is_bookmarked,
		$int_selected_language
	);

	$str_html .= $str_html_header;

	if (
		($arr_targets_visible['user_input_data_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_section_user_input_data = build_html_grammar_view_user_input_data_section($room_unique_code, $t_masta_japanese_root_id, $int_selected_language);
		$str_html .= $str_section_user_input_data;
	}
	
	if (
		($arr_targets_visible['prerequisite_knowledge_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_section_prerequisite_knowledge = build_html_grammar_view_prerequisite_knowledge_section($t_masta_japanese_root_id, $int_selected_language);
		$str_html .= $str_section_prerequisite_knowledge;
	}

	if (
		($arr_targets_visible['target_knowledge_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_section_target_knowledge = build_html_grammar_view_target_knowledge_section($t_masta_japanese_root_id, $int_selected_language);
		$str_html .= $str_section_target_knowledge;
	}
	
	$str_html_root = '';
	$str_html_root = build_html_grammar_view_root($t_masta_japanese_root_id, $arr_targets_visible, $int_selected_language);
	
	$arr_result = [
		'str_wise_map_focus_point' => '',
		'str_sample_sentence_list' => '',
		'str_sample_sentence_list_foreign_language_text' => ''
	];

	if (
		($arr_targets_visible['sample_sentence_list_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
		||
		($arr_targets_visible['sample_sentence_list_foreign_language_text_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$arr_result = get_arr_sample_sentence_list(
			$arr_targets_visible,
			$t_masta_japanese_root_id,
			$int_selected_language
		);
	}

	$str_wise_map_focus_point = $arr_result['str_wise_map_focus_point'];
	$str_sample_sentence_list = $arr_result['str_sample_sentence_list'];
	$str_sample_sentence_list_foreign_language_text = $arr_result['str_sample_sentence_list_foreign_language_text'];

	if (
		($arr_targets_visible['sample_sentence_list_foreign_language_text_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_sample_sentence_list = $str_sample_sentence_list_foreign_language_text;
	}

	if(!empty($str_html_root.$str_wise_map_focus_point.$str_sample_sentence_list)){

		$arr_str_html_root_header = [
			'文法説明',
			'文法説明'
		];
		$str_html_root_header = '<h3>' . $arr_str_html_root_header[$int_selected_language] . '</h3>';
		$str_html_root_div = '<div class="divGrammarViewMaindiv">' . $str_html_root . '</div>';
		$str_html_wise_map_focus_point_div = '<div class="divGrammarViewSampleSentenceListdiv">' . $str_wise_map_focus_point . '</div>';
		$str_html_sample_sentence_list_div = '<div class="divGrammarViewSampleSentenceListdiv">' . $str_sample_sentence_list . '</div>';

		$str_html_root_section = '<section class="sectionGrammarView sectionStandard">' . $str_html_root_header . $str_html_root_div . $str_html_wise_map_focus_point_div . $str_html_sample_sentence_list_div . '</section>';

		$str_html .= $str_html_root_section;
	}
	else{
		$str_html .= $arr_str_web_page_under_preparation[$int_selected_language];
	}

	foreach ($arr_section_grammar_relation as $visible_key => $cfg) {
		if (
			($arr_targets_visible[$visible_key] ?? $int_not_allow_visible_in_grammar_view)
				=== $int_allow_visible_in_grammar_view
		) {
			$str_html .= build_html_section_grammar_relation(
				$t_masta_japanese_root_id,
				$arr_targets_visible,
				$int_selected_language,
				$cfg['sets'],
				$cfg['items'],
				$cfg['class']
			);
		}
	}

	if (
		($arr_targets_visible['related_knowledge_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_section_related_knowledge = build_html_grammar_view_related_knowledge_section($t_masta_japanese_root_id, $int_selected_language);
		$str_html .= $str_section_related_knowledge;
	}
	
	if (
		($arr_targets_visible['listed_location_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_listed_location = build_html_grammar_view_listed_location_section($t_masta_japanese_root_id, $room_unique_code, $int_selected_language);
		$str_html .= $str_listed_location;
	}

	$category_id = fetch_category_id_from_masta_japanese_root_id($t_masta_japanese_root_id, $int_selected_language);
	
	if(
		intval($category_id) === $int_masta_japanese_category_id_grammar ||
		intval($category_id) === $int_masta_japanese_category_id_question
	){
		if (
			($arr_targets_visible['link_to_register_sentence_visible'] ?? $int_not_allow_visible_in_grammar_view)
				=== $int_allow_visible_in_grammar_view
		) {
			$str_link_to_register_sentence = build_html_section_link_to_register_sentence($t_masta_japanese_root_id, $grammar_unique_code, $int_selected_language);
			$str_html .= $str_link_to_register_sentence;
		}
	}
	else {
		if (
			($arr_targets_visible['grammar_outline_terminology_visible'] ?? $int_not_allow_visible_in_grammar_view)
				=== $int_allow_visible_in_grammar_view
		) {
			$arr_bookmarks_data = [];
			$str_section_grammar_outline_terminology = build_html_grammar_view_terminology_outline_section($arr_bookmarks_data, $t_masta_japanese_root_id, $int_selected_language);
			$str_html .= $str_section_grammar_outline_terminology;
		}
	}

	if (
		($arr_targets_visible['link_to_grammar_view_for_administrators_visible'] ?? $int_not_allow_visible_in_grammar_view)
			=== $int_allow_visible_in_grammar_view
	) {
		$str_link_to_grammar_view_for_administrators = build_html_section_link_to_grammar_view_for_administrators($t_masta_japanese_root_id, $grammar_unique_code, $int_selected_language);
		$str_html .= $str_link_to_grammar_view_for_administrators;
	}

    if ($user_level === null) {
		$str_html .= build_html_grammar_view_create_account_section($int_selected_language);
	}

	if ( $user_level === null || $user_level === $int_Free_Member ) {
        $str_html .= build_html_grammar_view_locked_contents_section_for_upgrade_membership(
            $t_masta_japanese_root_id,
            $int_Free_Member,
            $int_Basic_Student,
            $int_selected_language
        );
    }

	return $str_html;
}
/******************************************************
 *  LINK
 *  
 ******************************************************/
function build_html_section_link_to_grammar_view_for_administrators($t_masta_japanese_root_id, $unique_code, $int_selected_language){

	global
		$arr_str_button_caption_to_grammar_view_for_administrators,
		$path_grammar_view_for_administrators,
		$str_rgb_blue;

	$str_html = '';

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return $str_html;
	}

	$button_id = '';
	$button_text = $arr_str_button_caption_to_grammar_view_for_administrators[$int_selected_language];

	$url_grammar_view_for_administrators = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_grammar_view_for_administrators, '/'))
	);

	$div_class = 'divChoices';
	$form_class = '';
	$input_class = 'inputChoices allowDisplayGrammarExplanationDisplayArea';
	$hidden_class = '';
	$button_background_color = "rgb($str_rgb_blue);";
	$new_tab = false;
	$send_method = 'GET';

	$arr_request_contents = [
		[
			'class' => $hidden_class,
			'name' => 'grammar_unique_code',
			'value' => $unique_code
		]
	];

	$str_html = build_html_choice($button_id, $button_text, $arr_request_contents, $url_grammar_view_for_administrators, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language);

	$str_html = '<h3>'.$arr_str_button_caption_to_grammar_view_for_administrators[$int_selected_language].'</h3>'.$str_html;
	$str_html = '<section class="sectionLinkToGrammarViewForAdministrators sectionStandard">' . $str_html . '</section>';

	return $str_html;
}


function build_html_section_link_to_register_sentence($t_masta_japanese_root_id, $unique_code, $int_selected_language){

	global
		$str_sql_where_is_in,
		$t_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$arr_columns_masta_japanese_root,
		$arr_str_button_caption_to_register_sentence,
		$path_register_sentence,
		$str_rgb_blue,
		$int_used_language_jpn,
		$str_column_root_kana,
		$t_masta_japanese_sub_category;

	$str_html = '';

	$user_level = get_user_level();
	if(!is_admin_level($user_level)){
		return $str_html;
	}
	
	$url_register_sentence = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_register_sentence, '/'))
	);

	$button_id = '';
	$button_text = $arr_str_button_caption_to_register_sentence[$int_selected_language];

	$div_class = 'divChoices';
	$form_class = '';
	$input_class = 'inputChoices allowDisplayGrammarExplanationDisplayArea';
	$hidden_class = '';
	$button_background_color = "rgb($str_rgb_blue);";
	$new_tab = true;
	$send_method = 'GET';

	$arr_request_contents = [
		[
			'class' => $hidden_class,
			'name' => 'grammar_unique_code',
			'value' => $unique_code
		]
	];

	$str_html = build_html_choice($button_id, $button_text, $arr_request_contents, $url_register_sentence, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language);

	$str_html = '<h3>'.$arr_str_button_caption_to_register_sentence[$int_selected_language].'</h3>'.$str_html;
	$str_html = '<section class="sectionLinkToRegisterSentence sectionStandard">' . $str_html . '</section>';

	return $str_html;

}
/******************************************************
 *  GRAMMAR VIEW
 *  
 ******************************************************/

function build_html_grammar_view_root($t_masta_japanese_root_id, $arr_targets_visible, $int_selected_language){

	global
		$t_masta_japanese_section,
		$t_masta_japanese_attribute,
		$arr_columns_masta_japanese_section,
		$arr_columns_masta_japanese_attribute,
		$int_is_recording_shorts,
		$int_is_recording_video,
		$int_used_language_jpn,
		$int_masta_japanese_attribute_id_equivalentInForeignLanguage,
		$int_masta_japanese_attribute_id_example;

    $str_html = '';
    $recording_shorts = $arr_targets_visible['recording_shorts'];
    $recording_video = $arr_targets_visible['recording_video'];
    $allow_grammar_view_content_section_capabilities = $arr_targets_visible['allow_grammar_view_content_section_capabilities'] ?? [];

    $arr_strSQL_select = [
        [$t_masta_japanese_section, 'id'],
        [$t_masta_japanese_section, $arr_columns_masta_japanese_section[$int_selected_language]],
        [$t_masta_japanese_attribute, 'id as attribute_id'],
        [$t_masta_japanese_attribute, $arr_columns_masta_japanese_attribute[$int_selected_language]]
    ];

    $strSQL_from = " FROM
                    $t_masta_japanese_section
                    INNER JOIN $t_masta_japanese_attribute
                    ON
                    $t_masta_japanese_section.attribute_id = $t_masta_japanese_attribute.id
                    ";

    $arr_strSQL_where = generate_arr_recording_where_conditions(
        $t_masta_japanese_section,
        'root_id',
        $t_masta_japanese_root_id,
        $recording_shorts,
        $recording_video,
        $int_is_recording_shorts,
        $int_is_recording_video
    );

    $arr_strSQL_order = [
        [$t_masta_japanese_section, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_section) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_masta_japanese_section)) {
        $str_html = '';
        return $str_html;
    }

    foreach ($arr_masta_japanese_section as $loop_masta_japanese_section) {
        $t_masta_japanese_section_id = $loop_masta_japanese_section['id'];
        $t_masta_japanese_section_class = $loop_masta_japanese_section[$arr_columns_masta_japanese_section[$int_selected_language]];
        $t_masta_japanese_section_attribute_id = intval($loop_masta_japanese_section['attribute_id']);
        $t_masta_japanese_section_attribute = $loop_masta_japanese_section[$arr_columns_masta_japanese_attribute[$int_selected_language]];

        if (!in_array($t_masta_japanese_section_attribute_id, $allow_grammar_view_content_section_capabilities, true)) {
            continue;
        }
        if (
            (
                $int_selected_language === $int_used_language_jpn &&
                $t_masta_japanese_section_attribute_id === $int_masta_japanese_attribute_id_equivalentInForeignLanguage
            ) ||
            $t_masta_japanese_section_attribute_id === $int_masta_japanese_attribute_id_example
        ) {
            continue;
        }

        $str_grammar_view_section = build_html_grammar_view_section(
            $t_masta_japanese_section_id,
            $t_masta_japanese_section_class,
            $t_masta_japanese_section_attribute_id,
            $t_masta_japanese_section_attribute,
            $arr_targets_visible,
            $int_selected_language
        );

        $str_html .= $str_grammar_view_section;
    }

    return $str_html;
}


function build_html_grammar_view_section($t_masta_japanese_section_id, $t_masta_japanese_section_class, $t_masta_japanese_section_attribute_id, $t_masta_japanese_section_attribute, $arr_targets_visible, $int_selected_language){
	
	global
		$t_masta_japanese_main,
		$t_masta_japanese_attribute,
		$t_masta_japanese_description,
		$arr_columns_masta_japanese_main,
		$arr_columns_masta_japanese_attribute,
		$arr_columns_masta_japanese_description,
		$int_used_language_jpn,
		$int_is_recording_shorts,
		$int_is_recording_video,
		$int_allow_visible_in_grammar_view,
		$int_masta_japanese_attribute_id_explanation,
		$int_masta_japanese_attribute_id_japaneseTranslation,
		$int_masta_japanese_attribute_id_answerMethod,
		$int_masta_japanese_attribute_id_answerCorrectness,
		$int_masta_japanese_attribute_id_answerPositive,
		$int_masta_japanese_attribute_id_answerNegative,
		$int_masta_japanese_attribute_id_link,
		$int_masta_japanese_attribute_id_headingFree,
		$int_masta_japanese_attribute_id_correctnessComparison,
		$path_images_japanese_main;

	$url_images_japanese_main = get_home_url(
		get_main_site_id(),
		trailingslashit(ltrim($path_images_japanese_main, '/'))
	);

    $str_html = '';
    $slider_view_visible = $arr_targets_visible['slider_view_visible'];
    $recording_shorts = $arr_targets_visible['recording_shorts'];
    $recording_video = $arr_targets_visible['recording_video'];

    $arr_strSQL_select = [
        [$t_masta_japanese_main, 'id'],
        [$t_masta_japanese_main, 'attribute_id'],
        [$t_masta_japanese_main, $arr_columns_masta_japanese_main[$int_used_language_jpn]],
        [$t_masta_japanese_main, $arr_columns_masta_japanese_main[$int_selected_language]],
        [$t_masta_japanese_attribute, $arr_columns_masta_japanese_attribute[$int_selected_language]]
    ];

    $strSQL_from = " FROM
                    $t_masta_japanese_main
                    INNER JOIN $t_masta_japanese_attribute
                    ON
                    $t_masta_japanese_main.attribute_id = $t_masta_japanese_attribute.id
                    ";

    $arr_strSQL_where = generate_arr_recording_where_conditions(
        $t_masta_japanese_main,
        'masta_japanese_section_id',
        $t_masta_japanese_section_id,
        $recording_shorts,
        $recording_video,
        $int_is_recording_shorts,
        $int_is_recording_video
    );

    $arr_strSQL_order = [
        [$t_masta_japanese_main, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_japanese_main) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_japanese_main)) {
        return $str_html;
    }

    foreach ($arr_japanese_main as $index => $loop_japanese_main) {
        $t_masta_japanese_main_id = $loop_japanese_main['id'];
        $t_masta_japanese_main_attribute_id = $loop_japanese_main['attribute_id'];
        $t_masta_japanese_main_attribute = $loop_japanese_main[$arr_columns_masta_japanese_attribute[$int_selected_language]];

        $add_contents = '';
        $add_contents = apply_text_for_output($loop_japanese_main[$arr_columns_masta_japanese_main[$int_selected_language]]);

        $add_contents_heading = '';
        $class = 'frameDeepBlue';
        
        if ($slider_view_visible === $int_allow_visible_in_grammar_view) {
            $class = 'frameDeepBlue frameInSlider';
            if ($index === INDEX_FIRST) {
                $class = 'frameDeepBlue frameInSlider visible';
            }
        }

        $add_contents_p_tag = '<p class="grammarViewTextContent commonTextContent">';
        $user_level = get_user_level();
        if (is_operator_level($user_level) && !empty($add_contents)) {
            $add_contents_p_tag = '<p class="grammarViewTextContent commonTextContent" contenteditable="true" spellcheck="false">';
        }

        switch (intval($t_masta_japanese_main_attribute_id)) {
            case $int_masta_japanese_attribute_id_explanation:
                $add_contents_heading = $t_masta_japanese_main_attribute;
                $class = 'frame ' . $class;
                $add_contents = $add_contents_p_tag . $add_contents . '</p>';
                break;

            case $int_masta_japanese_attribute_id_japaneseTranslation:
                $class = 'frameNonTitle ' . $class;
                $add_contents = '<span class="underLine">' . $add_contents . '</span>';
                $add_contents = $add_contents_p_tag . $add_contents . '</p>';
                break;

            case $int_masta_japanese_attribute_id_answerMethod:
            case $int_masta_japanese_attribute_id_answerCorrectness:
            case $int_masta_japanese_attribute_id_answerPositive:
            case $int_masta_japanese_attribute_id_answerNegative:
                $class = 'frameNonTitle ' . $class;
                $add_contents = 'Q: ' . $add_contents;
                $add_contents = $add_contents_p_tag . $add_contents . '</p>';
                break;

            case $int_masta_japanese_attribute_id_link:
                $class = 'frameNonTitle ' . $class;
                $add_contents = build_html_links_for_main(intval($t_masta_japanese_main_id), $int_selected_language);
                break;

            default:
                $class = 'frameNonTitle ' . $class;
                $add_contents = $add_contents_p_tag . $add_contents . '</p>';
        }

        $arr_strSQL_select = [
            [$t_masta_japanese_description, 'attribute_id'],
            [$t_masta_japanese_description, $arr_columns_masta_japanese_description[$int_selected_language]],
            [$t_masta_japanese_attribute, $arr_columns_masta_japanese_attribute[$int_selected_language]]
        ];

        $strSQL_from = " FROM
                        $t_masta_japanese_description
                        INNER JOIN $t_masta_japanese_attribute
                        ON
                        $t_masta_japanese_description.attribute_id = $t_masta_japanese_attribute.id
                        ";

        $arr_strSQL_where = generate_arr_recording_where_conditions(
            $t_masta_japanese_description,
            'masta_japanese_main_id',
            $t_masta_japanese_main_id,
            $recording_shorts,
            $recording_video,
            $int_is_recording_shorts,
            $int_is_recording_video
        );

        $arr_strSQL_order = [
            [$t_masta_japanese_description, 'sort', 'ASC']
        ];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_japanese_column) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

        if (!empty($arr_japanese_column)) {
            if (!($int_selected_language == $int_used_language_jpn && 
            $t_masta_japanese_main_attribute_id == $int_masta_japanese_attribute_id_japaneseTranslation)) {
                $add_contents = build_html_expand_details_from_attributes($arr_japanese_column, $add_contents, $arr_columns_masta_japanese_description[$int_selected_language], $t_masta_japanese_main_attribute, $int_selected_language);
            }
        }
        
        $str_div_class = '<div class="divContent grammarViewDivContent">';

        $add_contents = $str_div_class . $add_contents . '</div>';

        $arr_image_urls = get_arr_images_for_main(intval($t_masta_japanese_main_id), $url_images_japanese_main);

        $images_html_before = '';
        $images_html_after = '';

        if (!empty($arr_image_urls['before'])) {
            foreach ($arr_image_urls['before'] as $u) {
                $images_html_before .= '<div class="japaneseMainImageContainer"><img class="japaneseMainImage" src="' 
                . esc_url(jws_add_file_version($u)) . '" alt="" loading="lazy" decoding="async"></div>';
            }
        }

        if (!empty($arr_image_urls['after'])) {
            foreach ($arr_image_urls['after'] as $u) {
                $images_html_after .= '<div class="japaneseMainImageContainer"><img class="japaneseMainImage" src="' 
                . esc_url(jws_add_file_version($u)) . '" alt="" loading="lazy" decoding="async"></div>';
            }
        }

        if (!empty($images_html_before) || !empty($images_html_after)) {
            $add_contents = $images_html_before . $add_contents . $images_html_after;
        }

        $class .= ' grammarViewFrame';

        $caution_class = 'frameTitle cautionDeepBlue';

        $add_contents = build_html_div_frame($class, $add_contents_heading, $add_contents, $caution_class);

        $str_html .= $add_contents;
    }

    if ($slider_view_visible === $int_allow_visible_in_grammar_view) {
        if ($slider_view_visible === $int_allow_visible_in_grammar_view) {
            if (count($arr_japanese_main) > 1) {
                $data_max = count($arr_japanese_main) - 1;
                $str_button_container = '
                <div class="frameInSliderButtonsContainer" data-current-index="0" data-max-index="' . $data_max . '">
                    <button class="frameInSliderPrevButton" disabled>Prev</button>
                    <button class="frameInSliderNextButton">Next</button>
                </div>';
                $str_html = $str_button_container . $str_html;
            }
        }
    }

    $details_class = 'grammarViewDetails animationSlideIn';
    $summary_class = 'grammarViewDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
    $details_div_class = 'detailsDiv grammarViewDetailsDiv detailsDivAddMarginBottom animationSlideIn';

    if (!empty($t_masta_japanese_section_class)) {
        if (
            intval($t_masta_japanese_section_attribute_id) === $int_masta_japanese_attribute_id_headingFree ||
            intval($t_masta_japanese_section_attribute_id) === $int_masta_japanese_attribute_id_correctnessComparison
        ) {
            $str_heading = $t_masta_japanese_section_class;
        }
        else {
            $str_heading = $t_masta_japanese_section_attribute.'---'.$t_masta_japanese_section_class;
        }
    }
    else {
        $str_heading = $t_masta_japanese_section_attribute;
    }

    $str_heading = apply_text_for_output($str_heading);
    $str_html = build_html_details_contents($str_html, $str_heading, $details_class, $summary_class, $details_div_class);

    $str_html = '<div class="divFrame">' . $str_html . '</div>';

    return $str_html;
}

function build_html_grammar_view_create_account_section(int $int_selected_language): string
{
    $arr_header = [
        'アカウント作成',
        '建立帳號',
    ];

    $arr_message = [
		'<p>アカウント登録はお済みですか？</p>',
		'<p>您已完成帳號註冊了嗎？</p>',
	];

    $str_header = '<h3 class="grammarViewCreateAccountLinkTitle">' . $arr_header[$int_selected_language] . '</h3>';
    $str_message = $arr_message[$int_selected_language];

    $str_link = build_html_create_account_link(
        'grammarViewCreateAccountLink',
        $int_selected_language
    );

    $str_html = '<section class="sectionGrammarView sectionStandard sectionGrammarViewCreateAccountLink">'
        . $str_header
        . $str_message
        . $str_link
        . '</section>';

    return $str_html;
}


function build_html_grammar_view_locked_contents_section_for_upgrade_membership($t_masta_japanese_root_id, $current_level, $next_level, $int_selected_language)
{
    global
        $allow_grammar_view_content_section_capabilities_default,
        $t_masta_japanese_section,
        $arr_columns_masta_japanese_section,
        $t_masta_japanese_attribute,
        $arr_columns_masta_japanese_attribute,
        $str_sql_where_is_in;

    if (!isset($allow_grammar_view_content_section_capabilities_default[$current_level], $allow_grammar_view_content_section_capabilities_default[$next_level])) {
        return '';
    }

    $current = $allow_grammar_view_content_section_capabilities_default[$current_level];
    $next = $allow_grammar_view_content_section_capabilities_default[$next_level];

    $array = array_values(array_diff($next, $current));
    if (empty($array)) {
        return '';
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_section, 'id'],
        [$t_masta_japanese_section, $arr_columns_masta_japanese_section[$int_selected_language]],
        [$t_masta_japanese_attribute, 'id as attribute_id'],
        [$t_masta_japanese_attribute, $arr_columns_masta_japanese_attribute[$int_selected_language]],
    ];

    $strSQL_from = " FROM
                $t_masta_japanese_section
                INNER JOIN $t_masta_japanese_attribute
                ON
                $t_masta_japanese_section.attribute_id = $t_masta_japanese_attribute.id
                ";

    $arr_strSQL_where = [
        [
            [
                [$t_masta_japanese_section, 'root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', 'And'],
                [$t_masta_japanese_section, 'attribute_id', $str_sql_where_is_in, $array, 'PDO::PARAM_INT', ''],
            ],
            '',
        ],
    ];

    $arr_strSQL_order = [
        [$t_masta_japanese_section, 'sort', 'ASC'],
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_section) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_masta_japanese_section)) {
        return '';
    }

    $str_list = '<ul class="grammarViewLockedContentsList">';

    foreach ($arr_masta_japanese_section as $row) {
        $section_title = $row[$arr_columns_masta_japanese_section[$int_selected_language]] ?? null;
        $attribute_title = $row[$arr_columns_masta_japanese_attribute[$int_selected_language]] ?? null;

        $label = $attribute_title;

        if (is_string($section_title)) {
            $label = $section_title;
        }

        $label = apply_remove_original_tags($label);
        $label = escape_html_with_nl2br($label);

        $str_list .= '<li>' . $label . '</li>';
    }

    $str_list .= '</ul>';

    $arr_str_paid_member_only_header = [
        '有料会員専用',
        '有料會員專用',
    ];

	$arr_locked_message = [
		'<p>以下のコンテンツは有料会員専用です。</p>',
		'<p>以下內容僅限付費會員專用。</p>',
	];

    $str_header = '<h3 class="grammarViewLockedContentsTitle">' . $arr_str_paid_member_only_header[$int_selected_language] . '</h3>';

	$str_message = $arr_locked_message[$int_selected_language];

    $str_upgrade_link = build_html_about_membership_link('grammarViewUpgradeMembershipLink', $int_selected_language);

	$str_html = '<section class="sectionGrammarView sectionStandard sectionGrammarViewLockedContents">'
		. $str_header
		. $str_message
		. $str_list
		. $str_upgrade_link
		. '</section>';

    return $str_html;
}



function build_html_grammar_view_target_knowledge_section($t_masta_japanese_root_id, $int_selected_language){
	
	global
		$int_grammar_view_status_as_target_knowledge,
		$arr_str_grammar_usages_header_target_knowledge;

	$str_html = '';

	$arr_root_ids = [];
	$arr_knowledge = [
		$int_grammar_view_status_as_target_knowledge
	];

	$arr_root_ids = get_arr_root_ids_for_grammar_outline_to_parent($t_masta_japanese_root_id, $arr_knowledge, $int_selected_language);

	if(empty($arr_root_ids)){
		return '';
	}

	$str_grammar_outline_contents = build_html_grammar_outline_contents($arr_root_ids, $int_selected_language);

	$str_header = '<h3>' . $arr_str_grammar_usages_header_target_knowledge[$int_selected_language] . '</h3>';

	$str_html = $str_header.$str_grammar_outline_contents;
	
	$str_html = '<section class="sectionTargetKnowledge sectionStandard">' . $str_html . '</section>';


	return $str_html;
}


function build_html_grammar_view_prerequisite_knowledge_section($t_masta_japanese_root_id, $int_selected_language){
	
	global
		$int_grammar_view_status_as_prerequisite_knowledge,
		$arr_str_grammar_usages_header_prerequisite_knowledge;

	$str_html = '';

	$arr_root_ids = [];
	$arr_knowledge = [
		$int_grammar_view_status_as_prerequisite_knowledge
	];

	$arr_root_ids = get_arr_root_ids_for_grammar_outline_to_parent($t_masta_japanese_root_id, $arr_knowledge, $int_selected_language);

	if(empty($arr_root_ids)){
		return '';
	}

	$str_grammar_outline_contents = build_html_grammar_outline_contents($arr_root_ids, $int_selected_language);

	$str_header = '<h3>' . $arr_str_grammar_usages_header_prerequisite_knowledge[$int_selected_language] . '</h3>';

	$str_html = $str_header . $str_grammar_outline_contents;

	$str_html = '<section class="sectionPrerequisiteKnowledge sectionStandard">' . $str_html . '</section>';


	return $str_html;
}


function build_html_grammar_view_related_knowledge_section($t_masta_japanese_root_id, $int_selected_language){
	
	global
		$int_grammar_view_status_as_related_knowledge,
		$arr_str_grammar_usages_header_related_knowledge;

	$str_html = '';

	$arr_root_ids = [];
	$arr_knowledge = [
		$int_grammar_view_status_as_related_knowledge
	];

	$arr_root_ids = get_arr_root_ids_for_grammar_outline_to_parent($t_masta_japanese_root_id, $arr_knowledge, $int_selected_language);

	if(empty($arr_root_ids)){
		return '';
	}

	$str_grammar_outline_contents = build_html_grammar_outline_contents($arr_root_ids, $int_selected_language);

	$str_header = '<h3>' . $arr_str_grammar_usages_header_related_knowledge[$int_selected_language] . '</h3>';

	$str_html = $str_header . $str_grammar_outline_contents;

	$str_html = '<section class="sectionRelatedKnowledge sectionStandard">' . $str_html . '</section>';


	return $str_html;
}


function build_html_grammar_view_terminology_outline_section($arr_bookmarks_data, $t_masta_japanese_root_id, $int_selected_language){
	
	global
		$t_masta_japanese_root,
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline,
		$arr_str_grammar_outline_teader_terminology;

	$str_html = '';
	
	$str_grammar_outline = '';

	$contents_tree_flags = [
		'doDisplayGrammarOutlineGrammars' => true,
		'doDisplayGrammarOutlineCheckbox' => false,
		'doDisplayGrammarOutlineLabelButtonsExplanation' => true,
		'doDisplayDerivedGrammars' => true,
		'displayInGrammarView' => true,
		'openOutline' => true
	];

	$root_id = $t_masta_japanese_root_id;
	$i = INDEX_FIRST;
	$seen = [];
	$seen_target_table = $t_masta_japanese_root;
	$did_build_grammar_outline = false;
	if (isset($_SESSION['arr_already_learned_list'])) {
		$arr_allow_display = $_SESSION['arr_already_learned_list'];
	} else {
		$arr_allow_display = get_arr_temp_already_learned_list($int_selected_language);
	}
	$arr_belongs = [
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline
	];
	$relations = fetch_all_root_parent_child_relations($int_selected_language);
	$map_parent_to_children = [];
	foreach ($relations as $row) {
		$map_parent_to_children[$row['masta_japanese_root_id_parent']][] = $row;
	}
	
	list($seen, $str_grammar_outline, $did_build_grammar_outline) = recursive_build_html_grammar_outline($contents_tree_flags, $i, $seen, $seen_target_table, $did_build_grammar_outline, $root_id, $t_masta_japanese_root_id, $arr_allow_display, $arr_bookmarks_data, $arr_belongs, $map_parent_to_children, $int_selected_language);

	if(!$did_build_grammar_outline){
		return '';
	}

	$str_grammar_outline = '
	<div class="grammarOutline grammarOutlineTopElement">'.
		$str_grammar_outline.'
	</div>';

	$details_class = 'grammarOutlineDetails animationSlideIn';
	$summary_class = 'grammarOutlineDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
	$details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';

	$str_grammar_outline_heading = $arr_str_grammar_outline_teader_terminology[$int_selected_language];
	$str_grammar_outline_heading = escape_html_with_nl2br($str_grammar_outline_heading);
	$str_grammar_outline = build_html_details_contents($str_grammar_outline, $str_grammar_outline_heading, $details_class, $summary_class, $details_div_class);

	$str_grammar_outline = '<div class="divFrame">' . $str_grammar_outline . '</div>';

	$str_header = '<h3>' . $arr_str_grammar_outline_teader_terminology[$int_selected_language] . '</h3>';

	$str_html = $str_header . $str_grammar_outline;

	$str_html = '<section class="sectionGrammarOutlineTerminology sectionStandard">' . $str_html . '</section>';

	return $str_html;
}


function build_html_grammar_view_user_input_data_section($room_unique_code, $t_masta_japanese_root_id, $int_selected_language){
	
	$str_html = '';

	if($room_unique_code === '') {
		return $str_html;
	}
	$room_id = fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
	
	$arr_room_user_input_data = fetch_arr_room_user_input_data($room_id, $t_masta_japanese_root_id, $int_selected_language);

	if(empty($arr_room_user_input_data)){return $str_html;}

	$str_details = '';

	foreach($arr_room_user_input_data as $index => $loop_room_user_input_data){
		$add_contents = escape_html_with_nl2br($loop_room_user_input_data['input_data']);
		$add_contents_p_tag = '<p class="grammarViewTextContent commonTextContent">';
		$user_level = get_user_level();
		if(is_operator_level($user_level) && !empty($add_contents)){
			$add_contents_p_tag ='<p class="grammarViewTextContent commonTextContent" contenteditable="true" spellcheck="false">';
		}
		$add_contents = $add_contents_p_tag . $add_contents . '</p>';
		$str_div_class = '<div class="divContent grammarViewDivContent">';

		$class = 'frameNonTitle frameDeepBlue grammarViewFrame';
		$caution_class = 'frameTitle cautionDeepBlue';
		$add_contents = build_html_div_frame($class, '', $add_contents, $caution_class);
		$str_details = $str_details . $add_contents;
	}

	$details_class = 'grammarViewDetails animationSlideIn';
	$summary_class = 'grammarViewDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
	$details_div_class = 'detailsDiv grammarViewDetailsDiv detailsDivAddMarginBottom animationSlideIn';

	$arr_str_user_input_data_details_header = ['一覧','一覧'];
	$str_details_heading = $arr_str_user_input_data_details_header[$int_selected_language];
	$str_details_heading = escape_html_with_nl2br($str_details_heading);
	
	$str_details = build_html_details_contents($str_details, $str_details_heading, $details_class, $summary_class, $details_div_class);

	$str_details = '<div class="divFrame">' . $str_details . '</div>';

	$arr_str_user_input_data_header = [
		'user_input_data',
		'user_input_data'
	];

	$str_header = '<h3>' . $arr_str_user_input_data_header[$int_selected_language] . '</h3>';

	$str_html = $str_header . $str_details;

	$str_html = '<section class="sectionUserInputData sectionStandard">' . $str_html . '</section>';

	return $str_html;
}


function build_html_grammar_view_listed_location_section($t_masta_japanese_root_id, $room_unique_code, $int_selected_language){

    global
        $t_teaching_material_sets,
        $t_teaching_material_levels,
        $t_teaching_material_lessons,
        $t_teaching_material_lesson_steps,
        $t_teaching_material_lesson_step_units,
        $t_teaching_material_lesson_contents,
        $arr_columns_masta_teaching_material_sets,
        $arr_columns_masta_teaching_material_levels,
        $arr_columns_masta_teaching_material_lessons;
		
    $arr_str_details_heading = ['一覧', '一覧'];
    $arr_str_header = ['掲載場所', 'location'];

    $str_html = '';

    if (intval($t_masta_japanese_root_id) === 0) {
        return $str_html;
    }

    // --- DB: location取得（set / level / lesson まで） ---
    $arr_strSQL_select = [
        [$t_teaching_material_sets, $arr_columns_masta_teaching_material_sets[$int_selected_language] . ' as set_title'],
        [$t_teaching_material_levels, $arr_columns_masta_teaching_material_levels[$int_selected_language] . ' as level_title'],
        [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language] . ' as lesson_title'],

        [$t_teaching_material_sets, 'sort as set_sort'],
        [$t_teaching_material_levels, 'sort as level_sort'],
        [$t_teaching_material_lessons, 'sort as lesson_sort']
    ];

    $strSQL_from = "
        FROM $t_teaching_material_lesson_contents
        LEFT JOIN $t_teaching_material_lesson_step_units
            ON $t_teaching_material_lesson_contents.step_unit_id = $t_teaching_material_lesson_step_units.id
        LEFT JOIN $t_teaching_material_lesson_steps
            ON $t_teaching_material_lesson_step_units.lesson_step_id = $t_teaching_material_lesson_steps.id
        LEFT JOIN $t_teaching_material_lessons
            ON $t_teaching_material_lesson_steps.lesson_id = $t_teaching_material_lessons.id
        LEFT JOIN $t_teaching_material_levels
            ON $t_teaching_material_lessons.level_id = $t_teaching_material_levels.id
        LEFT JOIN $t_teaching_material_sets
            ON $t_teaching_material_levels.set_id = $t_teaching_material_sets.id
    ";

    $arr_strSQL_where = [
        [
            [
                [$t_teaching_material_lesson_contents, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', 'And'],
				[$t_teaching_material_sets, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_teaching_material_sets, 'sort', 'ASC'],
        [$t_teaching_material_levels, 'sort', 'ASC'],
        [$t_teaching_material_lessons, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_locations) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_locations)) {
        return $str_html;
    }

    // --- 重複排除（set / level / lesson 組み合わせ） ---
    $map_unique = [];
    foreach ($arr_locations as $loop) {

        $set_title = trim(strval($loop['set_title'] ?? ''));
        $level_title = trim(strval($loop['level_title'] ?? ''));
        $lesson_title = trim(strval($loop['lesson_title'] ?? ''));

        if ($set_title === '' && $level_title === '' && $lesson_title === '') {
            continue;
        }

        $key = $set_title . '|' . $level_title . '|' . $lesson_title;
        if (isset($map_unique[$key])) {
            continue;
        }

        $map_unique[$key] = [
            'set_title' => $set_title,
            'level_title' => $level_title,
            'lesson_title' => $lesson_title
        ];
    }

    if (empty($map_unique)) {
        return $str_html;
    }

    // --- HTML組み立て（user_input_data と同じ作法） ---
    $str_details = '';

    foreach ($map_unique as $row) {

        $set_title = escape_html_with_nl2br($row['set_title']);
        $level_title = escape_html_with_nl2br($row['level_title']);
        $lesson_title = escape_html_with_nl2br($row['lesson_title']);

        $line = $set_title;
        if ($level_title !== '') {
            $line .= ' : ' . $level_title;
        }
        if ($lesson_title !== '') {
            $line .= ' : ' . $lesson_title;
        }

        $add_contents = '<p class="grammarViewTextContent commonTextContent">' . $line . '</p>';

        $class = 'frameNonTitle frameDeepBlue grammarViewFrame';
        $caution_class = 'frameTitle cautionDeepBlue';
        $add_contents = build_html_div_frame($class, '', $add_contents, $caution_class);

        $str_details .= $add_contents;
    }

    $details_class = 'grammarViewDetails animationSlideIn';
    $summary_class = 'grammarViewDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
    $details_div_class = 'detailsDiv grammarViewDetailsDiv detailsDivAddMarginBottom animationSlideIn';

    $str_details_heading = escape_html_with_nl2br($arr_str_details_heading[$int_selected_language]);

    $str_details = build_html_details_contents($str_details, $str_details_heading, $details_class, $summary_class, $details_div_class);
    $str_details = '<div class="divFrame">' . $str_details . '</div>';

    $str_header = '<h3>' . $arr_str_header[$int_selected_language] . '</h3>';

    $str_html = $str_header . $str_details;
    $str_html = '<section class="sectionListedLocation sectionStandard">' . $str_html . '</section>';

    return $str_html;
}

/******************************************************
 *  ITEM
 *  
 ******************************************************/
function build_html_grammar_view_header(
    int $t_masta_japanese_root_id,
    string $grammar_unique_code,
    string $room_unique_code,
    bool $is_bookmarked,
    int $int_selected_language
){
    global
        $arr_columns_masta_japanese_root,
        $workshop_trial_unique_code;

    $str_contents_header = '';
    $arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($t_masta_japanese_root_id, $int_selected_language);

    if (empty($arr_masta_japanese_root)) {
        return $str_contents_header;
    }

    $t_masta_japanese_root_title = apply_text_for_output($arr_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]]);

    $user_level = get_user_level();

    $str_h1_open = '<h1 id="grammarViewHeader">';
    if (is_admin_level($user_level)) {
        $str_h1_open = '<h1 id="grammarViewHeader" contenteditable="true" spellcheck="false">';
    }

    $str_title = $str_h1_open . $t_masta_japanese_root_title . '</h1>';

    $str_star = '';
    if (
        $room_unique_code !== '' &&
        $room_unique_code !== $workshop_trial_unique_code &&
        $grammar_unique_code !== ''
    ) {
        $unique_id = uniqid();
        $str_star = build_html_bookmark_star(
            $unique_id,
            $grammar_unique_code,
            $is_bookmarked,
            $room_unique_code
        );
    }

    $str_contents_header =
        '<div class="grammarViewHeaderRow">' .
            $str_title .
            $str_star .
        '</div>';

    return $str_contents_header;
}



/**
 * buttons cfg から <button ...> 群を生成します（GrammarView / SampleSentenceList 用）
 *
 * $ctx = [
 *     'id' => (int),
 *     'row' => (array),
 *     'unique_code' => (string),
 *     'user_level' => (int),
 *     'int_selected_language' => (int),
 *     'mode' => 'jp'|'foreign'
 * ]
 *
 * 仕様（現行互換）:
 * - role があれば user_level 未満は出さない
 * - condition === 'ifLayersExist' の場合 layers_exist を満たさなければ出さない
 * - key === 'answer':
 *     - mode === 'jp' なら fetch_str_registered_sentence_answer_by_id が空なら出さない
 *       data-answer に答え（翻訳）を入れる
 *     - mode === 'foreign' なら data-answer に日本語 sentence を入れる（空なら出さない）
 * - key === 'furigana': data-furigana を付与
 */
function build_html_action_buttons_from_config(array $buttons, array $ctx): string
{
    $html = '';

    $id = (int) ($ctx['id'] ?? 0);
    $row = (array) ($ctx['row'] ?? []);
    $unique_code = (string) ($ctx['unique_code'] ?? '');
    $user_level = (int) ($ctx['user_level'] ?? 0);
    $int_selected_language = (int) ($ctx['int_selected_language'] ?? 0);
    $mode = (string) ($ctx['mode'] ?? 'jp');

    foreach ($buttons as $cfg) {

        if (isset($cfg['role']) && $user_level < (int) $cfg['role']) {
            continue;
        }

        if (isset($cfg['condition']) && $cfg['condition'] === 'ifLayersExist') {
            if (!layers_exist($id, $int_selected_language)) {
                continue;
            }
        }

        $key = (string) ($cfg['key'] ?? '');
        $action = (string) ($cfg['action'] ?? '');
        $class = trim('grammarViewActionButton ' . (string) ($cfg['class'] ?? ''));
        $button_text = (string) ($cfg['button_text'] ?? '');

        $data = 'data-key="' . escape_html($key) . '"';
        $data .= ' data-action="' . escape_html($action) . '"';
        $data .= ' data-unique-code="' . escape_html($unique_code) . '"';

        if (!empty($cfg['action_target'])) {
            $data .= ' data-action-target="' . escape_html((string) $cfg['action_target']) . '"';
        }

        if (!empty($cfg['page_path'])) {
            $data .= ' data-page-path="' . escape_html((string) $cfg['page_path']) . '"';
        }

        // === Sorting Quiz ===
        if ($key === 'sortingQuiz' || $key === 'sorting_quiz') {

            $data_form_list = (string) ($ctx['data_form_list'] ?? ($row['data_form_list'] ?? ''));

            if ($data_form_list !== '') {
                $data .= ' data-form-list="' . escape_html($data_form_list) . '"';
            }
        }

        // === Answer ===
        if ($key === 'answer') {

            if ($mode === 'jp') {

                $ans = (string) ($row['foreignLanguageText'] ?? '');

                // 互換 fallback（古いrows用）
                if ($ans === '') {
                    $ans = (string) fetch_str_registered_sentence_answer_by_id($id, $int_selected_language);
                }

                if ($ans === '') {
                    continue;
                }

                $data .= ' data-answer="' . escape_html($ans) . '"';

            } else {

                $jp = escape_html((string) ($row['sentence'] ?? ''));
                if ($jp === '') {
                    continue;
                }

                $data .= ' data-answer="' . $jp . '"';
            }
        }

        // === Furigana ===
        if ($key === 'furigana') {
            $data .= ' data-furigana="' . escape_html((string) ($row['sentence_kana'] ?? '')) . '"';
        }

        $html .= '<button class="' . escape_html($class) . '" ' . $data . '>' . escape_html($button_text) . '</button>';
    }

    return $html;
}




/**
 * rows + buttons cfg から SampleSentenceList の <ul> を生成します

 * $mode:
 * - 'jp'      : sentence を表示
 * - 'foreign' : fetch_str_registered_sentence_answer_by_id を表示（空ならその行はスキップ）
 */
function build_html_registered_sentences_ul(
    array $rows,
    array $buttons,
    int $int_selected_language,
    int $user_level,
    string $unique_code_key,
    string $mode,
    array $ui = []
): string {

    if (empty($rows)) {
        return '';
    }

    $list_tag = (string) ($ui['list_tag'] ?? 'ul');
    if ($list_tag !== 'ul' && $list_tag !== 'ol') {
        $list_tag = 'ul';
    }

    $list_class = (string) ($ui['list_class'] ?? 'sampleSentenceListUl');
    $li_class = (string) ($ui['li_class'] ?? 'sampleSentenceListLi');

    $li_content_class = (string) ($ui['li_content_class'] ?? '');
    $text_container_class = (string) ($ui['text_container_class'] ?? '');
    $text_class = (string) ($ui['text_class'] ?? 'sampleSentenceListTextDiv');
    $buttons_container_class = (string) ($ui['buttons_container_class'] ?? 'sampleSentenceListLiButtonsContainer');

    $contenteditable = (bool) ($ui['contenteditable'] ?? false);
    $data_form_list = (string) ($ui['data_form_list'] ?? '');

    $apply_text_for_output = (bool) ($ui['apply_text_for_output'] ?? false);

    $lis = '';

    foreach ($rows as $r) {

        $id = (int) ($r['id'] ?? 0);
        $unique_code_raw = (string) ($r[$unique_code_key] ?? '');
        $unique_code = escape_html($unique_code_raw);

        if ($unique_code === '') {
            continue;
        }

        if ($mode === 'jp') {

            $raw = (string) ($r['sentence'] ?? ($r['japaneseText'] ?? ''));
            if ($raw === '') {
                continue;
            }

        } else {

            $raw = (string) ($r['foreignLanguageText'] ?? '');
            if ($raw === '') {
                continue;
            }
        }

        if ($apply_text_for_output) {
            $raw = (string) apply_text_for_output($raw);
        }

        $text = escape_html($raw);

        if ($text === '') {
            continue;
        }

        $btns = build_html_action_buttons_from_config($buttons, [
            'id' => $id,
            'row' => $r,
            'unique_code' => $unique_code_raw,
            'user_level' => $user_level,
            'int_selected_language' => $int_selected_language,
            'mode' => $mode,
            'data_form_list' => $data_form_list
        ]);

        // --- Text HTML ---
        if ($text_class === 'sampleSentenceListTextDiv') {

            $div_text = '<div class="' . escape_html($text_class) . '" data-register-sentence-unique-code="' . $unique_code . '">' . $text . '</div>';

        } else {

            // homework想定: class="homeworkLiText" contenteditable="true"
            $editable_attr = $contenteditable ? ' contenteditable="true"' : '';
            $div_text = '<div class="' . escape_html($text_class) . '"' . $editable_attr . '>' . $text . '</div>';
        }

        if ($text_container_class !== '') {
            $div_text = '<div class="' . escape_html($text_container_class) . '">' . $div_text . '</div>';
        }

        // --- Buttons HTML ---
        $div_btns = '';
        if ($btns !== '') {
            $div_btns = '<div class="' . escape_html($buttons_container_class) . '">' . $btns . '</div>';
        }

        // --- Combine ---
        if ($li_content_class !== '') {
            $inner = '<div class="' . escape_html($li_content_class) . '">' . $div_btns . $div_text . '</div>';
        } else {
            $inner = $div_text . $div_btns;
        }

        $lis .= '<li class="' . escape_html($li_class) . '">' . $inner . '</li>';
    }

    if ($lis === '') {
        return '';
    }

    return '<' . $list_tag . ' class="' . escape_html($list_class) . '">' . $lis . '</' . $list_tag . '>';
}



function get_arr_sample_sentence_list($arr_targets_visible, $t_masta_japanese_root_id, $int_selected_language)
{
    global
        $int_is_not_recording_shorts,
        $int_is_recording_shorts,
        $int_is_not_recording_video,
        $int_is_recording_video,
        $arr_str_sampleSentenceListViewAnswerButton,
        $arr_str_sampleSentenceListViewFuriganaButton,
        $arr_str_sampleSentenceListToEditRegisteredSentence,
        $path_edit_registered_sentence,
        $int_Administrator,
        $arr_str_sampleSentenceListToCreateLayersButton,
        $path_create_layers,
        $arr_str_sampleSentenceListToWiseMapFocusPointButton,
        $arr_str_sampleSentenceListToManageWiseNavigationButton,
        $path_manage_wise_navigations,
        $arr_str_sampleSentenceListToWiseNavigationButton,
        $path_wise_select_navigation,
        $int_Basic_Teacher,
        $t_registered_sentences,
        $str_snake_to_camel_unique_code,
        $int_allow_visible_in_grammar_view,
        $sql_exists_registered_sentences,
        $str_sql_where_is_exists,
        $str_sql_where_is_not_exists,
        $arr_wise_map_focus_point_tag,
        $arr_sample_sentence_list_tag,
        $int_not_allow_visible_in_grammar_view;

    $result = [
        'str_wise_map_focus_point' => '',
        'str_sample_sentence_list' => '',
        'str_sample_sentence_list_foreign_language_text' => ''
    ];

    $user_level = get_user_level();

    $recording_shorts = isset($_GET['recording_shorts'])
        ? intval($_GET['recording_shorts'])
        : $int_is_not_recording_shorts;

    $recording_video = isset($_GET['recording_video'])
        ? intval($_GET['recording_video'])
        : $int_is_not_recording_video;

    $pub_cond = [
        [$t_registered_sentences, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
    ];

    if ($recording_shorts === $int_is_recording_shorts) {
        array_unshift($pub_cond, [$t_registered_sentences, 'is_published_shorts', '=', FLAG_TRUE, 'PDO::PARAM_INT', 'And']);
    } elseif ($recording_video === $int_is_recording_video) {
        array_unshift($pub_cond, [$t_registered_sentences, 'is_published_video', '=', FLAG_TRUE, 'PDO::PARAM_INT', 'And']);
    }

    $base_buttons = [
        [
            'key' => 'answer',
            'button_text' => $arr_str_sampleSentenceListViewAnswerButton[$int_selected_language],
            'class' => 'sampleSentenceListViewAnswerButton',
            'action' => 'grammar:show',
            'action_target' => 'answer'
        ],
        [
            'key' => 'furigana',
            'button_text' => $arr_str_sampleSentenceListViewFuriganaButton[$int_selected_language],
            'class' => 'sampleSentenceListViewFuriganaButton hidden',
            'action' => 'grammar:show',
            'action_target' => 'furigana'
        ],
        [
            'key' => 'editRegisteredSentence',
            'button_text' => $arr_str_sampleSentenceListToEditRegisteredSentence[$int_selected_language],
            'class' => 'sampleSentenceListToEditRegisteredSentence',
            'action' => 'grammar:navigate',
            'page_path' => $path_edit_registered_sentence,
            'role' => $int_Administrator
        ],
        [
            'key' => 'createLayers',
            'button_text' => $arr_str_sampleSentenceListToCreateLayersButton[$int_selected_language],
            'class' => 'sampleSentenceListToCreateLayersInput',
            'action' => 'grammar:navigate',
            'page_path' => $path_create_layers,
            'role' => $int_Administrator
        ]
    ];

    $wise_buttons = [
        [
            'key' => 'wiseMapFocusPoint',
            'button_text' => $arr_str_sampleSentenceListToWiseMapFocusPointButton[$int_selected_language],
            'class' => 'sampleSentenceListToWiseMapFocusPointButton',
            'action' => 'grammar:show',
            'action_target' => 'wiseMapFocusPoint',
            'role' => $int_Basic_Teacher
        ],
        [
            'key' => 'manageWiseNavigation',
            'button_text' => $arr_str_sampleSentenceListToManageWiseNavigationButton[$int_selected_language],
            'class' => 'sampleSentenceListToManageWiseNavigationButton',
            'action' => 'navi:navigate',
            'page_path' => $path_manage_wise_navigations,
            'role' => $int_Administrator
        ],
        [
            'key' => 'wiseNavigation',
            'button_text' => $arr_str_sampleSentenceListToWiseNavigationButton[$int_selected_language],
            'class' => 'sampleSentenceListToWiseNavigationButton',
            'action' => 'navi:navigate',
            'page_path' => $path_wise_select_navigation,
            'role' => $int_Administrator
        ]
    ];

    $wrap_expand = function($inner_html, $summary_text) {
        if ($inner_html === '') {
            return '';
        }
        $details_class = 'grammarViewDetails animationSlideIn';
        $summary_class = 'grammarViewDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
        $details_div_class = 'detailsDiv animationSlideIn';
        $expanded = build_html_details_contents($inner_html, $summary_text, $details_class, $summary_class, $details_div_class);
        return '<div class="divFrame">' . $expanded . '</div>';
    };

    if (
        ($arr_targets_visible['wise_map_focus_point_visible'] ?? $int_not_allow_visible_in_grammar_view)
            === $int_allow_visible_in_grammar_view
    ) {

        $where_focus_inner = [
            [$t_registered_sentences, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', 'And'],
            ['', $sql_exists_registered_sentences, $str_sql_where_is_exists, '', '', '']
        ];

        $where_focus = [
            [$pub_cond, 'And'],
            [$where_focus_inner, '']
        ];

        $rows_focus = get_arr_registered_sentences_with_multilingual_text($where_focus, $int_selected_language);

        $ul_focus = build_html_registered_sentences_ul(
            $rows_focus,
            array_merge($base_buttons, $wise_buttons),
            $int_selected_language,
            $user_level,
            $str_snake_to_camel_unique_code,
            'jp'
        );

        $result['str_wise_map_focus_point'] = $wrap_expand($ul_focus, $arr_wise_map_focus_point_tag[$int_selected_language]);

        $where_list_inner = [
            [$t_registered_sentences, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', 'And'],
            ['', $sql_exists_registered_sentences, $str_sql_where_is_not_exists, '', '', '']
        ];

        $where_list = [
            [$pub_cond, 'And'],
            [$where_list_inner, '']
        ];

        $rows_list = get_arr_registered_sentences_with_multilingual_text($where_list, $int_selected_language);

        $ul_list_jp = build_html_registered_sentences_ul(
            $rows_list,
            $base_buttons,
            $int_selected_language,
            $user_level,
            $str_snake_to_camel_unique_code,
            'jp'
        );

        $ul_list_foreign = build_html_registered_sentences_ul(
            $rows_list,
            $base_buttons,
            $int_selected_language,
            $user_level,
            $str_snake_to_camel_unique_code,
            'foreign'
        );

        $result['str_sample_sentence_list'] = $wrap_expand($ul_list_jp, $arr_sample_sentence_list_tag[$int_selected_language]);
        $result['str_sample_sentence_list_foreign_language_text'] = $wrap_expand($ul_list_foreign, $arr_sample_sentence_list_tag[$int_selected_language]);

        return $result;
    }

    $where_all_inner = [
        [$t_registered_sentences, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
    ];

    $where_all = [
        [$pub_cond, 'And'],
        [$where_all_inner, '']
    ];

    $rows_all = get_arr_registered_sentences_with_multilingual_text($where_all, $int_selected_language);

    $ul_all_jp = build_html_registered_sentences_ul(
        $rows_all,
        $base_buttons,
        $int_selected_language,
        $user_level,
        $str_snake_to_camel_unique_code,
        'jp'
    );

    $ul_all_foreign = build_html_registered_sentences_ul(
        $rows_all,
        $base_buttons,
        $int_selected_language,
        $user_level,
        $str_snake_to_camel_unique_code,
        'foreign'
    );

    $result['str_sample_sentence_list'] = $wrap_expand($ul_all_jp, $arr_sample_sentence_list_tag[$int_selected_language]);
    $result['str_sample_sentence_list_foreign_language_text'] = $wrap_expand($ul_all_foreign, $arr_sample_sentence_list_tag[$int_selected_language]);
    $result['str_wise_map_focus_point'] = '';

    return $result;
}


function get_arr_images_for_main(int $masta_japanese_main_id, string $url_images_japanese_main): array
{

	global
		$t_japanese_main_images;

    $arr_strSQL_select_img = [
        [$t_japanese_main_images, 'image_code'],
        [$t_japanese_main_images, 'image_position'],
        [$t_japanese_main_images, 'sort']
    ];

    $strSQL_from_img = " FROM $t_japanese_main_images ";

    $arr_strSQL_where_img = [
        [
            [
                [$t_japanese_main_images, 'masta_japanese_main_id', '=', $masta_japanese_main_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order_img = [
        [$t_japanese_main_images, 'image_position', 'ASC'],
        [$t_japanese_main_images, 'sort', 'ASC']
    ];

    $strSQL_option_img = '';

    list($pdo_err, $sel_err, $e, $arr_imgs) = execute_select_and_fetch_all(
        $arr_strSQL_select_img,
        $strSQL_from_img,
        $arr_strSQL_where_img,
        $arr_strSQL_order_img,
        $strSQL_option_img
    );

    $urls = [
        'before' => [],
        'after'  => []
    ];

    foreach ($arr_imgs as $imgs) {
        $imgs_uc = escape_html($imgs['image_code']);
        $url = $url_images_japanese_main . $imgs_uc . '.webp';

        if ($imgs['image_position'] == 1 || $imgs['image_position'] === 'before') {
            $urls['before'][] = $url;
        } elseif ($imgs['image_position'] == 2 || $imgs['image_position'] === 'after') {
            $urls['after'][] = $url;
        }
    }

    return $urls;
}


function build_html_links_for_main(int $masta_japanese_main_id, int $int_selected_language): string {

	global
		$t_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$t_masta_japanese_sub_category,
		$t_japanese_main_links,
		$str_grammarOutlineLabelButtonExplanation,
		$str_grammarOutlineLabelButtonExplanationMarker;

    $arr_strSQL_select_link = [
        [$t_masta_japanese_root, 'id'],
        [$t_masta_japanese_root, 'unique_code as ' . $str_snake_to_camel_unique_code],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
        [$t_masta_japanese_root, $str_column_root_kana],
        [$t_masta_japanese_sub_category, 'category_id'],
        [$t_japanese_main_links, 'sort']
    ];

    $strSQL_from_link = " FROM 
	$t_japanese_main_links
	INNER JOIN $t_masta_japanese_root ON $t_japanese_main_links.masta_japanese_root_id = $t_masta_japanese_root.id
	INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
	";

    $arr_strSQL_where_link = [
        [
			[
				[$t_japanese_main_links, 'masta_japanese_main_id', '=', $masta_japanese_main_id, 'PDO::PARAM_INT', '']
			],
			''
		]
    ];

    $arr_strSQL_order_link = [
        [$t_japanese_main_links, 'sort', 'ASC']
    ];
	$strSQL_option_link = '';


    list($pdo_err, $sel_err, $e, $arr_links) = execute_select_and_fetch_all(
        $arr_strSQL_select_link,
        $strSQL_from_link,
        $arr_strSQL_where_link,
        $arr_strSQL_order_link,
        $strSQL_option_link
    );

    $html = '<ul class="grammarViewContentLinksUl">';
	foreach ($arr_links as $link) {
		$set_japanese_root_id = $link['id'];
		$str_unique_code = $link[$str_snake_to_camel_unique_code];
		$str_title = $link[$arr_columns_masta_japanese_root[$int_selected_language]];
		$str_kana = $link[$str_column_root_kana];
		$int_category_id = $link['category_id'];

		$str_explanation_button = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation"'
			. ' data-japanese-id="' . $set_japanese_root_id . '"'
			. ' data-unique-code="' . $str_unique_code . '"'
			. ' data-japanese="' . $str_title . '"'
			. ' data-kana="' . $str_kana . '"'
			. ' data-category-id="' . $int_category_id . '"'
			. ' title="' . $str_grammarOutlineLabelButtonExplanation[$int_selected_language] . '">'
			. $str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language]
			. '</button>';

		$html .= '<li class="grammarViewContentLinksLi">' . $str_explanation_button . ' ' . $str_title . '</li>';
	}
	$html .= '</ul>';

	return $html;
}


function get_arr_root_ids_for_grammar_outline_to_parent($t_masta_japanese_root_id, $arr_knowledge, $int_selected_language){
	
	global
		$int_to_parent;

	$arr_root_ids = [];

	foreach($arr_knowledge as $loop_knowledge){

		$arr_root_ids_in_parents = fetch_arr_grammar_usage_parents_by_attribute($t_masta_japanese_root_id, $loop_knowledge, 'grammar_view_status', $int_selected_language);
	
		if(empty($arr_root_ids_in_parents)){
			continue;
		}

		$flag_parent_or_child = $int_to_parent;
		$arr_seen = [];
		$arr_seen = get_arr_root_parent_child_relations($flag_parent_or_child, $arr_root_ids_in_parents, $int_selected_language);
		$arr_root_ids = array_merge($arr_root_ids,$arr_seen);
	}

	if(empty($arr_root_ids)){
		return $arr_root_ids;
	}

	$arr_root_ids = array_unique($arr_root_ids);
	$arr_root_ids = array_values($arr_root_ids);
	
	array_unshift($arr_root_ids, $t_masta_japanese_root_id);

	return $arr_root_ids;

}


function build_html_grammar_outline_contents($arr_root_ids, $int_selected_language){

	global
		$t_grammar_usage_parents,
		$int_masta_grammar_usage_tier_root;

	$str_html = '';    
	$arr_tree_targets = [];

	$arr_tree_target_usage_category_ids = get_arr_tree_target_usage_category_ids($int_selected_language);

	foreach($arr_tree_target_usage_category_ids as $loop_tree_target_usage_category_ids){

		$arr_strSQL_select = [
			[$t_grammar_usage_parents,'usage_category_id'],
			[$t_grammar_usage_parents,'masta_japanese_root_id']
		];

		$strSQL_from = ' FROM ' .$t_grammar_usage_parents;

		$arr_strSQL_where = [
			[
				[
					[$t_grammar_usage_parents,'usage_category_id','=',$loop_tree_target_usage_category_ids,'PDO::PARAM_INT','And'],
					[$t_grammar_usage_parents,'tier','=',$int_masta_grammar_usage_tier_root,'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_strSQL_order = [];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_grammar_usage_parents) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

		$arr_grammar_usage_parents_masta_japanese_root_id = $arr_grammar_usage_parents[INDEX_FIRST]['masta_japanese_root_id'];

		if(in_array($arr_grammar_usage_parents_masta_japanese_root_id, $arr_root_ids)){
			$arr_tree_targets[] = $loop_tree_target_usage_category_ids;
		}
		
	}

	$arr_bookmarks_data = [];
	$arr_allow_display = $arr_root_ids;

	$contents_tree_flags = [
		'doDisplayGrammarOutlineGrammars' => false,
		'doDisplayGrammarOutlineCheckbox' => false,
		'doDisplayGrammarOutlineLabelButtonsExplanation' => true,
		'openOutline' => true
	];

	$draw_details = true;

	$str_html = build_html_grammar_usages_tree($contents_tree_flags, $arr_tree_targets, $arr_allow_display, $arr_bookmarks_data, $draw_details, $int_selected_language);
	
	return $str_html;
}


