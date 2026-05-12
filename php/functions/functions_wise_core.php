<?php


/**
 * WISE メインページ
 */
function build_html_wise_page($int_selected_language){

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
        $str_wiseRightVerticalToolbarButton_id_whiteboard => true,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup => true,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation => true,
        $str_wiseRightVerticalToolbarButton_id_memoPad => true,
        $str_wiseRightVerticalToolbarButton_id_chart => true,
        $str_wiseRightVerticalToolbarButton_id_quiz => true,
        $str_wiseRightVerticalToolbarButton_id_map => true,
        $str_wiseRightVerticalToolbarButton_id_imageViewer => true,
        $str_wiseRightVerticalToolbarButton_id_lessonContents => true,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights => true,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi => true,
    ];

    // panelは固定
    $arr_panels = [
        build_html_wise_whiteboard_panel($int_selected_language),
        build_html_wise_grammar_explanation_panel($int_selected_language),
        build_html_wise_memo_pad_panel($int_selected_language),
        build_html_wise_lesson_contents_panel($int_selected_language),
        build_html_wise_grammar_insights_panel($int_selected_language),
        build_html_wise_setup_panel($int_selected_language),
        build_html_wise_chart_panel($int_selected_language),
        build_html_wise_image_viewer_panel($int_selected_language),
        build_html_wise_quiz_panel($int_selected_language),
    ];

    $str_panel_container_layer = build_html_wise_panel_container_layer($arr_panels);

    $str_panel_overlay_layer = build_html_wise_panel_overlay_layer(
        build_html_wise_panel_overlay_bundle(
            build_html_wise_main_panel_overlay_contents($int_selected_language)
        )
    );

    $str_hud_layer = build_html_wise_hud_layer(
        build_html_wise_main_hud_contents($arr_visible_right_buttons, $int_selected_language)
    );

    $str_super_overlay_layer = build_html_wise_super_overlay_layer(
        build_html_wise_super_overlay_bundle(
            build_html_wise_main_super_overlay_contents($int_selected_language)
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
 *  INFLECTION APPLY
 *  
 ******************************************************/
function apply_word_inflection($arr_inflected_label, $t_masta_japanese_root_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, $int_masta_japanese_label_id, $doAllowInflection, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$int_V1KU,
		$int_V3Z,
		$int_AI,
		$int_AI2,
		$int_AuxVI,
		$str_stages_of_inflection_get_baseform_japanese,
		$str_stages_of_inflection_get_baseform_kana,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id,
		$str_snake_to_camel_voice_id,
		$int_id_default,
		$int_ANA,
		$int_N,
		$int_NAdV,
		$int_masta_japanese_label_id_AuxVNA_desu,
		$int_AuxVNA,
		$int_ANAToTaru,
		$int_AdV,
		$int_PNA,
		$int_C,
		$int_JapaneseParticle,
		$int_Interjection,
		$int_Prefix,
		$int_Suffix,
		$int_CounterWord,
		$int_Idiom,
		$int_Symbol,
		$int_Num;

	$arr_inflected_label[$str_inflected_label_inflection_process] = [];

	switch($t_masta_japanese_sub_classification_id){
		case $int_V1KU <= $t_masta_japanese_sub_classification_id && $t_masta_japanese_sub_classification_id <= $int_V3Z:
			$arr_inflected_label = apply_word_inflection_verb($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, $int_selected_language);
			break;

		case $int_AI:
		case $int_AI2:
		case $int_AuxVI:
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];
			$arr_inflected_label = apply_word_inflection_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
			$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
			$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_id_default;
			break;

		case $int_ANA:
		case $int_N:
		case $int_NAdV:
			if($doAllowInflection){
				$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
				$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];
				if(intval($int_masta_japanese_label_id) === $int_masta_japanese_label_id_AuxVNA_desu){
					$arr_inflected_label[$str_snake_to_camel_japanese] = '';
					$arr_inflected_label[$str_snake_to_camel_kana] = '';
				}
				$arr_inflected_label = apply_word_inflection_na($arr_inflected_label, $t_masta_japanese_root_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_masta_japanese_label_id, $int_selected_language);
				$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
				$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
				$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_id_default;
			}
			else{
				$arr_inflected_label = $arr_inflected_label;
				$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
				$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
				$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_id_default;
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
				$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
				$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];
			}
			break;

		case $int_AuxVNA:
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];
			if(intval($int_masta_japanese_label_id) === $int_masta_japanese_label_id_AuxVNA_desu){
				$arr_inflected_label[$str_snake_to_camel_japanese] = '';
				$arr_inflected_label[$str_snake_to_camel_kana] = '';
			}
			$arr_inflected_label = apply_word_inflection_na($arr_inflected_label, $t_masta_japanese_root_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_masta_japanese_label_id, $int_selected_language);
			$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
			$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
			$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_id_default;
			break;

		case $int_ANAToTaru:
		case $int_AdV:
		case $int_PNA:
		case $int_C:
		case $int_JapaneseParticle:
		case $int_Interjection:
		case $int_Prefix:
		case $int_Suffix:
		case $int_CounterWord:
		case $int_Idiom:
		case $int_Symbol:
		case $int_Num:
			$arr_inflected_label = $arr_inflected_label;
			$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
			$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
			$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_id_default;
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
			$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];
			break;

		default:
			return [];
	}
	return $arr_inflected_label;
}


function apply_word_inflection_verb($arr_inflected_label, $t_masta_japanese_sub_classification_id_original, $t_masta_form_root_id, $int_voice_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$verb_polite_form,
		$verb_plain_form,
		$int_PotentialVerb,
		$int_CausativePassiveVerbShortened,
		$str_stages_of_inflection_get_baseform_japanese,
		$str_stages_of_inflection_get_baseform_kana,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id,
		$str_snake_to_camel_voice_id,
		$int_PoliteFormAffirmativeNotPastTense,
		$int_PoliteFormNegativeNotPastTense,
		$int_PoliteFormAffirmativePastTense,
		$int_PoliteFormNegativePastTense,
		$int_OriginalForm,
		$int_NaiForm,
		$int_TaForm,
		$int_NakattaForm,
		$int_DictionaryForm,
		$int_TeForm,
		$int_NakuteForm,
		$int_NaideForm,
		$int_TeFormShortened,
		$arr_how_to_make_teform_shortened,
		$int_BaForm,
		$int_NakerebaForm,
		$int_baform_shortened,
		$int_NakerebaFormShortenedNakya,
		$int_NakerebaFormShortenedNakerya,
		$int_TaraForm,
		$int_NakattaraForm,
		$int_TariForm,
		$int_NakattariForm,
		$int_MasuForm,
		$int_ImperfectiveForm,
		$int_ImperfectiveForm_se,
		$int_VolitionalForm,
		$int_VolitionalFormShortened,
		$int_ImperativeForm,
		$int_ProhibitionForm,
		$int_StemForm,
		$int_NaiFormStemForm,
		$int_inflection_add_part_shi,
		$int_inflection_add_part_ta,
		$int_inflection_add_part_te,
		$int_inflection_add_part_Normal,
		$int_inflection_add_part_shortened;

	$int_inflection_from = $verb_polite_form;

	$arr_stages_of_inflection = [];
	$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;

	if($int_PotentialVerb <= $t_masta_form_root_id && $t_masta_form_root_id <= $int_CausativePassiveVerbShortened){
		$arr_inflected_label = generate_arr_inflection_stages_for_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id_original, $t_masta_form_root_id, $int_selected_language);
		$arr_inflected_label = apply_word_inflection_voice($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id_original, $t_masta_form_root_id, $int_selected_language);
		$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id_original;
		return $arr_inflected_label;
	}

	if($int_PotentialVerb <= $int_voice_id && $int_voice_id <= $int_CausativePassiveVerbShortened){
		$arr_inflected_label = apply_word_inflection_voice($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id_original, $int_voice_id, $int_selected_language);
		$t_masta_japanese_sub_classification_id = $arr_inflected_label[$str_snake_to_camel_sub_classification_id];
		$arr_inflected_label[$str_inflected_label_inflection_process] = [];
		$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_inflected_label[$str_snake_to_camel_japanese];
		$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_inflected_label[$str_snake_to_camel_kana];

		$int_inflection_from = $verb_plain_form;
	}
	else{
		$t_masta_japanese_sub_classification_id = $t_masta_japanese_sub_classification_id_original;
		$arr_inflected_label = generate_arr_inflection_stages_for_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
	}

	$str_japanese = '';
	$str_kana = '';
	$str_last_part = '';

	switch($t_masta_form_root_id){
		case $int_PoliteFormAffirmativeNotPastTense :

			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ます';
			break;

		case $int_PoliteFormNegativeNotPastTense :

			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ません';
			break;

		case $int_PoliteFormAffirmativePastTense :

			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ました';
			break;

		case $int_PoliteFormNegativePastTense :

			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ませんでした';
			break;

		case $int_OriginalForm :

			$arr_inflected_label = generate_word_inflection_dictionaryform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NaiForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ない';
			break;

		case $int_TaForm :

			$arr_inflected_label = generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_ta, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NakattaForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なかった';
			break;

		case $int_DictionaryForm :

			$arr_inflected_label = generate_word_inflection_dictionaryform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_TeForm :

			$arr_inflected_label = generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_te, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NakuteForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なくて';
			break;

		case $int_NaideForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ないで';
			break;

		case $int_TeFormShortened :

			$arr_inflected_label = generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_te, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
			$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;
			$arr_how_to_make_form = $arr_how_to_make_teform_shortened[$int_inflection_from];
			$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
			$str_last_part = '';
			break;

		case $int_BaForm :

			$arr_inflected_label = generate_word_inflection_baform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NakerebaForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なければ';
			break;

		case $int_baform_shortened :

			$arr_inflected_label = generate_word_inflection_baform_shortened($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NakerebaFormShortenedNakya :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なきゃ';
			break;

		case $int_NakerebaFormShortenedNakerya :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なけりゃ';
			break;

		case $int_TaraForm :

			$arr_inflected_label = generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_ta, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ら';
			break;

		case $int_NakattaraForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なかったら';
			break;

		case $int_TariForm :

			$arr_inflected_label = generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_ta, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'り';
			break;

		case $int_NakattariForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'なかったり';
			break;

		case $int_MasuForm :

			if($int_inflection_from == $verb_polite_form){
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			}
			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_ImperfectiveForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_ImperfectiveForm_se :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_se, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_VolitionalForm :

			$arr_inflected_label = generate_word_inflection_volitionalform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_Normal, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_VolitionalFormShortened :

			$arr_inflected_label = generate_word_inflection_volitionalform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shortened, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_ImperativeForm :

			$arr_inflected_label = generate_word_inflection_imperativeform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_ProhibitionForm :

			$arr_inflected_label = generate_word_inflection_dictionaryform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'な';
			break;

		case $int_StemForm :

			if($int_inflection_from == $verb_polite_form){
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			}
			$arr_inflected_label = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NaiFormStemForm :

			$arr_inflected_label = generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_inflection_add_part_shi, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'な';
			break;

		default:
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id_original;
	$arr_inflected_label[$str_snake_to_camel_form_id] = $t_masta_form_root_id;
	$arr_inflected_label[$str_snake_to_camel_voice_id] = $int_voice_id;

	switch($t_masta_form_root_id){
		case $int_PoliteFormAffirmativeNotPastTense :
		case $int_PoliteFormNegativeNotPastTense :
		case $int_PoliteFormAffirmativePastTense :
		case $int_PoliteFormNegativePastTense :
			if($int_inflection_from == $verb_plain_form){
				$arr_inflected_label = apply_inflection_stage_add_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
			}
			else{
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
				$arr_inflected_label = apply_inflection_stage_change_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
			}
			break;

		default:
			$arr_inflected_label = apply_inflection_stage_add_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
			break;
	}

	return $arr_inflected_label;
}


function apply_word_inflection_voice($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id,
		$str_snake_to_camel_voice_id,
		$int_PotentialVerb,
		$int_PassiveVerb,
		$int_HonorificVerb,
		$int_CausativeVerb,
		$int_CausativePassiveVerb,
		$int_CausativeVerbShortened,
		$int_CausativePassiveVerbShortened,
		$int_V2E,
		$int_V1SA,
		$int_DictionaryForm,
		$int_inflection_add_part_CausativeVerb,
		$int_inflection_add_part_CausativePassiveVerb;

	switch($t_masta_form_root_id){
		case $int_PotentialVerb :

			$arr_inflected_label = generate_word_inflection_potentialverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V2E;
			break;

		case $int_PassiveVerb :
		case $int_HonorificVerb :

			$arr_inflected_label = generate_word_inflection_passive_honorificverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V2E;
			break;

		case $int_CausativeVerb :

			$arr_inflected_label = generate_word_inflection_causativeverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_inflection_add_part_CausativeVerb, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V2E;
			break;

		case $int_CausativePassiveVerb :

			$arr_inflected_label = generate_word_inflection_causativeverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_inflection_add_part_CausativePassiveVerb, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V2E;
			break;

		case $int_CausativeVerbShortened :

			$arr_inflected_label = generate_word_inflection_causativeverb_shortened($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_inflection_add_part_CausativeVerb, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V1SA;
			break;

		case $int_CausativePassiveVerbShortened :

			$arr_inflected_label = generate_word_inflection_causativeverb_shortened($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_inflection_add_part_CausativePassiveVerb, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			$t_masta_japanese_sub_classification_id = $int_V2E;
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_sub_classification_id] = $t_masta_japanese_sub_classification_id;
	$arr_inflected_label[$str_snake_to_camel_form_id] = $int_DictionaryForm;
	$arr_inflected_label[$str_snake_to_camel_voice_id] = $t_masta_form_root_id;

	return $arr_inflected_label;
}



function apply_word_inflection_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_inflected_label_inflection_process,
		$int_PoliteFormAffirmativeNotPastTense,
		$int_PoliteFormNegativeNotPastTense,
		$int_PoliteFormAffirmativePastTense,
		$int_PoliteFormNegativePastTense,
		$int_OriginalForm,
		$int_NaiForm,
		$int_TaForm,
		$int_NakattaForm,
		$int_DictionaryForm,
		$int_TeForm,
		$int_NakuteForm,
		$int_BaForm,
		$int_NakerebaForm,
		$int_baform_shortened,
		$int_NakerebaFormShortenedNakya,
		$int_NakerebaFormShortenedNakerya,
		$int_TaraForm,
		$int_NakattaraForm,
		$int_TariForm,
		$int_NakattariForm,
		$int_StemForm,
		$int_NaiFormStemForm,
		$int_AdverbForm;

	$str_last_part = '';

	switch($t_masta_form_root_id){
		case $int_PoliteFormAffirmativeNotPastTense :

			$arr_inflected_label = $arr_inflected_label;
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_PoliteFormNegativeNotPastTense :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くない';
			break;

		case $int_PoliteFormAffirmativePastTense :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'かった';
			break;

		case $int_PoliteFormNegativePastTense :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなかった';
			break;

		case $int_OriginalForm :

			$arr_inflected_label = $arr_inflected_label;
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			$str_last_part = '';
			break;

		case $int_NaiForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くない';
			break;

		case $int_TaForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'かった';
			break;

		case $int_NakattaForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなかった';
			break;

		case $int_DictionaryForm :

			$arr_inflected_label = $arr_inflected_label;
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			$str_last_part = '';
			break;

		case $int_TeForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くて';
			break;

		case $int_NakuteForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなくて';
			break;

		case $int_BaForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'ければ';
			break;

		case $int_NakerebaForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなければ';
			break;

		case $int_baform_shortened :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'けりゃ';
			break;

		case $int_NakerebaFormShortenedNakya :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなきゃ';
			break;

		case $int_NakerebaFormShortenedNakerya :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなけりゃ';
			break;

		case $int_TaraForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'かったら';
			break;

		case $int_NakattaraForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなかったら';
			break;

		case $int_TariForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'かったり';
			break;

		case $int_NakattariForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くなかったり';
			break;


		case $int_StemForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = '';
			break;

		case $int_NaiFormStemForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'くな';
			break;

		case $int_AdverbForm :

			$arr_inflected_label = generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_last_part = 'く';
			break;

		default:
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_last_part;
	$arr_inflected_label = apply_inflection_stage_add_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
	return $arr_inflected_label;
}


function apply_word_inflection_na($arr_inflected_label, $t_masta_japanese_root_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_masta_japanese_label_id, $int_selected_language){

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_PoliteFormAffirmativeNotPastTense,
		$int_PoliteFormNegativeNotPastTense,
		$int_PoliteFormAffirmativePastTense,
		$int_PoliteFormNegativePastTense,
		$int_OriginalForm,
		$int_NaiForm,
		$int_TaForm,
		$int_NakattaForm,
		$int_DictionaryForm,
		$int_TeForm,
		$int_NakuteForm,
		$int_TaraForm,
		$int_NakattaraForm,
		$int_TariForm,
		$int_NakattariForm,
		$int_StemForm,
		$int_NaiFormStemForm,
		$int_AdverbForm,
		$int_AdnominalModificationFormNO,
		$int_AdnominalModificationFormNA,
		$int_masta_japanese_label_id_AuxVNA_desu;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	$str_last_part = '';

	switch($t_masta_form_root_id){
		case $int_PoliteFormAffirmativeNotPastTense :

			$str_last_part = 'です';
			break;

		case $int_PoliteFormNegativeNotPastTense :

			$str_last_part = 'じゃありません';
			break;

		case $int_PoliteFormAffirmativePastTense :

			$str_last_part = 'でした';
			break;

		case $int_PoliteFormNegativePastTense :

			$str_last_part = 'じゃありませんでした';
			break;

		case $int_OriginalForm :

			$str_last_part = 'だ';
			break;

		case $int_NaiForm :

			$str_last_part = 'じゃない';
			break;

		case $int_TaForm :

			$str_last_part = 'だった';
			break;

		case $int_NakattaForm :

			$str_last_part = 'じゃなかった';
			break;

		case $int_DictionaryForm :

			$str_last_part = '';
			break;

		case $int_TeForm :

			$str_last_part = 'で';
			break;

		case $int_NakuteForm :

			$str_last_part = 'じゃなくて';
			break;

		case $int_TaraForm :

			$str_last_part = 'だったら';
			break;

		case $int_NakattaraForm :

			$str_last_part = 'じゃなかったら';
			break;

		case $int_TariForm :

			$str_last_part = 'だったり';
			break;

		case $int_NakattariForm :

			$str_last_part = 'じゃなかったり';
			break;

		case $int_StemForm :

			$str_last_part = 'だ';
			break;

		case $int_NaiFormStemForm :

			$str_last_part = 'じゃな';
			break;

		case $int_AdverbForm :

			$str_last_part = 'に';
			break;

		case $int_AdnominalModificationFormNO :

			$str_last_part = 'の';
			break;

		case $int_AdnominalModificationFormNA :

			$str_last_part = 'な';
			break;

		default:
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_last_part;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_last_part;

	if(intval($int_masta_japanese_label_id) === $int_masta_japanese_label_id_AuxVNA_desu){
		$arr_inflected_label = apply_inflection_stage_change_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
	}
	else{
		$arr_inflected_label = apply_inflection_stage_add_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language);
	}

	return $arr_inflected_label;
}



/******************************************************
 *  INFLECTION STAGES
 *  
 ******************************************************/
function generate_arr_inflection_stages_for_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_stages_of_inflection_get_baseform_japanese,
		$str_stages_of_inflection_get_baseform_kana,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$arr_how_to_make_masuform;

	$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];

	$arr_stages_of_inflection_change_to_masuform = generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language);

	$arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese] = $arr_stages_of_inflection_change_to_masuform[$str_snake_to_camel_japanese].'ます';
	$arr_inflected_label[$str_stages_of_inflection_get_baseform_kana] = $arr_stages_of_inflection_change_to_masuform[$str_snake_to_camel_kana].'ます';

	$arr_stages_of_inflection[INDEX_FIRST] = [
		'word'=>$arr_stages_of_inflection_change_to_masuform[$str_snake_to_camel_japanese],
		'explanation'=>$arr_how_to_make_masuform[$int_inflection_from][$int_selected_language]
	];

	$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;
	return $arr_inflected_label;
}


function generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language){

	global
		$verb_plain_form,
		$str_inflected_label_inflection_process,
		$arr_how_to_make_masuform;


	if($int_inflection_from == $verb_plain_form){
		$arr_stages_of_inflection[INDEX_FIRST] = [
			'word'=>$str_japanese,
			'explanation'=>$arr_how_to_make_masuform[$int_inflection_from][$int_selected_language]
		];
		$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;
	}
	return $arr_inflected_label;
}


function apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language){

	global
	    $str_inflected_label_inflection_process,
	    $str_snake_to_camel_japanese;

	if(empty($arr_how_to_make_form)){return $arr_inflected_label;}

	$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];

	$arr_stages_of_inflection[] = [
		'word'=>$arr_inflected_label[$str_snake_to_camel_japanese],
		'explanation'=>$arr_how_to_make_form[$int_selected_language]
	];
	$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;
	return $arr_inflected_label;
}


function apply_inflection_stage_add_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$arr_how_to_make_add_last_part;

	if(empty($str_last_part)){return $arr_inflected_label;}

	$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];

	$arr_stages_of_inflection[] = [
		'word'=>$arr_inflected_label[$str_snake_to_camel_japanese],
		'explanation'=>$arr_how_to_make_add_last_part[$int_selected_language][INDEX_FIRST].$str_last_part.$arr_how_to_make_add_last_part[$int_selected_language][INDEX_SECOND]
	];
	$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;
	return $arr_inflected_label;
}


function apply_inflection_stage_change_last_part_explanation($arr_inflected_label, $str_last_part, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$arr_how_to_make_change_last_part;

	if(empty($str_last_part)){return $arr_inflected_label;}

	$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];

	$arr_stages_of_inflection[] = [
		'word'=>$arr_inflected_label[$str_snake_to_camel_japanese],
		'explanation'=>$arr_how_to_make_change_last_part[$int_selected_language][INDEX_FIRST].$str_last_part.$arr_how_to_make_change_last_part[$int_selected_language][INDEX_SECOND]
	];
	$arr_inflected_label[$str_inflected_label_inflection_process] = $arr_stages_of_inflection;
	return $arr_inflected_label;
}


/******************************************************
 *  INFLECTION GENERATE
 *  
 ******************************************************/

// 動詞辞書形を作る
function generate_word_inflection_dictionaryform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_u,
		$verb_plain_form,
		$arr_how_to_make_dictionaryform_V2,
		$arr_how_to_make_dictionaryform_V3K,
		$arr_how_to_make_dictionaryform_V3S,
		$arr_how_to_make_dictionaryform_V3Z;

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$arr_how_to_make_form = $arr_how_to_make_dictionaryform_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$arr_how_to_make_form = $arr_how_to_make_dictionaryform_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		//サ変・−スル
		case $int_V3S:
		case $int_V3S2:
			$arr_how_to_make_form = $arr_how_to_make_dictionaryform_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$arr_how_to_make_form = $arr_how_to_make_dictionaryform_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_u, $int_selected_language);
			if($int_inflection_from == $verb_plain_form){
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			}
			return $arr_inflected_label;
	}

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞ます形を作る
function generate_word_inflection_masuform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V1RA2,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_i,
		$verb_polite_form,
		$arr_how_to_make_masuform_V2,
		$arr_how_to_make_masuform_V1RA2,
		$arr_how_to_make_masuform_V3K,
		$arr_how_to_make_masuform_V3S,
		$arr_how_to_make_masuform_V3Z;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_how_to_make_form = $arr_how_to_make_masuform_V2[$int_inflection_from];
			break;

		//五段・ラ行特殊(くださる)
		case $int_V1RA2:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$str_japanese = $str_japanese.'い';
			$str_kana = $str_kana.'い';
			$arr_how_to_make_form = $arr_how_to_make_masuform_V1RA2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来';
			$str_kana = $str_kana.'き';
			$arr_how_to_make_form = $arr_how_to_make_masuform_V3K[$int_inflection_from];
			break;

		//第三類(する)
		//サ変・−スル
		case $int_V3S:
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'し';
			$str_kana = $str_kana.'し';
			$arr_how_to_make_form = $arr_how_to_make_masuform_V3S[$int_inflection_from];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じ';
			$str_kana = $str_kana.'じ';
			$arr_how_to_make_form = $arr_how_to_make_masuform_V3Z[$int_inflection_from];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_i, $int_selected_language);
			if($int_inflection_from == $verb_polite_form){

				$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];
				$length_stages_of_inflection = count($arr_stages_of_inflection)-1;
				$arr_inflected_label[$str_inflected_label_inflection_process][$length_stages_of_inflection]['explanation'] = $arr_how_to_make_masuform_V2[$int_inflection_from][$int_selected_language];
			}
			return $arr_inflected_label;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
	return $arr_inflected_label;
}


// 動詞未然形を作る
function generate_word_inflection_imperfectiveform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_shi_or_se, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V1ARU,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_inflection_add_part_shi,
		$int_stem_a,
		$arr_how_to_make_masuform_V2,
		$arr_how_to_make_imperfectiveform_V1ARU,
		$arr_how_to_make_imperfectiveform_V3K,
		$arr_how_to_make_imperfectiveform_V3S,
		$arr_how_to_make_imperfectiveform_V3SE,
		$arr_how_to_make_imperfectiveform_V3S2,
		$arr_how_to_make_imperfectiveform_V3Z;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_how_to_make_form = $arr_how_to_make_masuform_V2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第一類(ある)
		case $int_V1ARU:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V1ARU[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来';
			$str_kana = $str_kana.'こ';
			$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_shi_or_se === $int_inflection_add_part_shi){
				$str_japanese = $str_japanese.'し';
				$str_kana = $str_kana.'し';
				$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V3S[$int_inflection_from];
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			}else{
				$str_japanese = $str_japanese.'せ';
				$str_kana = $str_kana.'せ';
				$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V3SE[$int_inflection_from];
				$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			}
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'さ';
			$str_kana = $str_kana.'さ';
			$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じ';
			$str_kana = $str_kana.'じ';
			$arr_how_to_make_form = $arr_how_to_make_imperfectiveform_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_a, $int_selected_language);
			return $arr_inflected_label;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞て形た形を作る
function generate_word_inflection_teform_taform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_te_or_ta, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_inflection_add_part_te,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_V1WA,
		$int_V1TA,
		$int_V1RA,
		$int_V1RA2,
		$int_V1ARU,
		$int_V1NA,
		$int_V1BA,
		$int_V1MA,
		$int_V1KU,
		$int_V1GU,
		$int_V1SA,
		$int_V1KU2,
		$arr_how_to_make_teform_V2,
		$arr_how_to_make_teform_V3K,
		$arr_how_to_make_teform_V3S,
		$arr_how_to_make_teform_V3Z,
		$arr_how_to_make_teform_V1Sokuonbin,
		$arr_how_to_make_teform_V1Hatsuonbin,
		$arr_how_to_make_teform_V1IonbinKI,
		$arr_how_to_make_teform_V1IonbinGI,
		$arr_how_to_make_teform_V1S,
		$arr_how_to_make_teform_V1IKU,
		$arr_how_to_make_taform_V2,
		$arr_how_to_make_taform_V3K,
		$arr_how_to_make_taform_V3S,
		$arr_how_to_make_taform_V3Z,
		$arr_how_to_make_taform_V1Sokuonbin,
		$arr_how_to_make_taform_V1Hatsuonbin,
		$arr_how_to_make_taform_V1IonbinKI,
		$arr_how_to_make_taform_V1IonbinGI,
		$arr_how_to_make_taform_V1S,
		$arr_how_to_make_taform_V1IKU;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'て';
				$str_kana = $str_kana.'て';
				$arr_how_to_make_form = $arr_how_to_make_teform_V2[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'た';
				$str_kana = $str_kana.'た';
				$arr_how_to_make_form = $arr_how_to_make_taform_V2[$int_inflection_from];
			}
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'来て';
				$str_kana = $str_kana.'きて';
				$arr_how_to_make_form = $arr_how_to_make_teform_V3K[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'来た';
				$str_kana = $str_kana.'きた';
				$arr_how_to_make_form = $arr_how_to_make_taform_V3K[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		//サ変・−スル
		case $int_V3S:
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'して';
				$str_kana = $str_kana.'して';
				$arr_how_to_make_form = $arr_how_to_make_teform_V3S[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'した';
				$str_kana = $str_kana.'した';
				$arr_how_to_make_form = $arr_how_to_make_taform_V3S[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'じて';
				$str_kana = $str_kana.'じて';
				$arr_how_to_make_form = $arr_how_to_make_teform_V3Z[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'じた';
				$str_kana = $str_kana.'じた';
				$arr_how_to_make_form = $arr_how_to_make_taform_V3Z[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		case $int_V1WA :
		case $int_V1TA :
		case $int_V1RA :
		case $int_V1RA2 :
		case $int_V1ARU :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'って';
				$str_kana = $str_kana.'って';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1Sokuonbin[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'った';
				$str_kana = $str_kana.'った';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1Sokuonbin[$int_inflection_from];
			}
			break;

		case $int_V1NA :
		case $int_V1BA :
		case $int_V1MA :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'んで';
				$str_kana = $str_kana.'んで';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1Hatsuonbin[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'んだ';
				$str_kana = $str_kana.'んだ';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1Hatsuonbin[$int_inflection_from];
			}
			break;

		case $int_V1KU :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'いて';
				$str_kana = $str_kana.'いて';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1IonbinKI[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'いた';
				$str_kana = $str_kana.'いた';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1IonbinKI[$int_inflection_from];
			}
			break;

		case $int_V1GU :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'いで';
				$str_kana = $str_kana.'いで';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1IonbinGI[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'いだ';
				$str_kana = $str_kana.'いだ';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1IonbinGI[$int_inflection_from];
			}
			break;

		case $int_V1SA :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'して';
				$str_kana = $str_kana.'して';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1S[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'した';
				$str_kana = $str_kana.'した';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1S[$int_inflection_from];
			}
			break;

		case $int_V1KU2 :
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			if($int_te_or_ta === $int_inflection_add_part_te){
				$str_japanese = $str_japanese.'って';
				$str_kana = $str_kana.'って';
				$arr_how_to_make_form = $arr_how_to_make_teform_V1IKU[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'った';
				$str_kana = $str_kana.'った';
				$arr_how_to_make_form = $arr_how_to_make_taform_V1IKU[$int_inflection_from];
			}
			// $arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞ば形を作る
function generate_word_inflection_baform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_e,
		$arr_how_to_make_baform_V2,
		$arr_how_to_make_baform_V3K,
		$arr_how_to_make_baform_V3S,
		$arr_how_to_make_baform_V3Z,
		$arr_how_to_make_baform_V1;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'れば';
			$str_kana = $str_kana.'れば';
			$arr_how_to_make_form = $arr_how_to_make_baform_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_last_part = '';
			$str_japanese = $str_japanese.'来れば';
			$str_kana = $str_kana.'くれば';
			$arr_how_to_make_form = $arr_how_to_make_baform_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		//サ変・−スル
		case $int_V3S:
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'すれば';
			$str_kana = $str_kana.'すれば';
			$arr_how_to_make_form = $arr_how_to_make_baform_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'ずれば';
			$str_kana = $str_kana.'ずれば';
			$arr_how_to_make_form = $arr_how_to_make_baform_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_e, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_japanese = $str_japanese.'ば';
			$str_kana = $str_kana.'ば';
			$arr_how_to_make_form = $arr_how_to_make_baform_V1[$int_inflection_from];
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞ば形短縮形を作る
function generate_word_inflection_baform_shortened($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_i,
		$arr_how_to_make_baform_shortened_V2,
		$arr_how_to_make_baform_shortened_V3K,
		$arr_how_to_make_baform_shortened_V3S,
		$arr_how_to_make_baform_shortened_V3S2,
		$arr_how_to_make_baform_shortened_V3Z,
		$arr_how_to_make_baform_shortened_V1;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'りゃ';
			$str_kana = $str_kana.'りゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来りゃ';
			$str_kana = $str_kana.'くりゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'すりゃ';
			$str_kana = $str_kana.'すりゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'しゃ';
			$str_kana = $str_kana.'しゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じりゃ';
			$str_kana = $str_kana.'じりゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_i, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_japanese = $str_japanese.'ゃ';
			$str_kana = $str_kana.'ゃ';
			$arr_how_to_make_form = $arr_how_to_make_baform_shortened_V1[$int_inflection_from];
			break;
	}


	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞意向形を作る
function generate_word_inflection_volitionalform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_Normal_or_shortened, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_o,
		$int_inflection_add_part_shortened,
		$arr_how_to_make_volitionalform_V2,
		$arr_how_to_make_volitionalform_V3K,
		$arr_how_to_make_volitionalform_V3S,
		$arr_how_to_make_volitionalform_V3S2,
		$arr_how_to_make_volitionalform_V3Z,
		$arr_how_to_make_volitionalform_V1,
		$arr_how_to_make_volitionalform_shortened;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'よう';
			$str_kana = $str_kana.'よう';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来よう';
			$str_kana = $str_kana.'こよう';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'しよう';
			$str_kana = $str_kana.'しよう';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'そう';
			$str_kana = $str_kana.'そう';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じよう';
			$str_kana = $str_kana.'じよう';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_o, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			if($int_Normal_or_shortened === $int_inflection_add_part_shortened){
				$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
				$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;
				$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
				return $arr_inflected_label;
			}
			$str_japanese = $str_japanese.'う';
			$str_kana = $str_kana.'う';
			$arr_how_to_make_form = $arr_how_to_make_volitionalform_V1[$int_inflection_from];
			break;
	}


	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);


	if($int_Normal_or_shortened === $int_inflection_add_part_shortened){
		$str_japanese = mb_substr($str_japanese, 0, -1);
		$str_kana = mb_substr($str_kana, 0, -1);
		$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
		$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;
		$arr_how_to_make_form = $arr_how_to_make_volitionalform_shortened[$int_inflection_from];
		$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
	}

	return $arr_inflected_label;
}


// 動詞命令形を作る
function generate_word_inflection_imperativeform($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_e,
		$arr_how_to_make_imperativeform_V2,
		$arr_how_to_make_imperativeform_V3K,
		$arr_how_to_make_imperativeform_V3S,
		$arr_how_to_make_imperativeform_V3S2,
		$arr_how_to_make_imperativeform_V3Z;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'ろ';
			$str_kana = $str_kana.'ろ';
			$arr_how_to_make_form = $arr_how_to_make_imperativeform_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来い';
			$str_kana = $str_kana.'こい';
			$arr_how_to_make_form = $arr_how_to_make_imperativeform_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'しろ';
			$str_kana = $str_kana.'しろ';
			$arr_how_to_make_form = $arr_how_to_make_imperativeform_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'せ';
			$str_kana = $str_kana.'せ';
			$arr_how_to_make_form = $arr_how_to_make_imperativeform_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じろ';
			$str_kana = $str_kana.'じろ';
			$arr_how_to_make_form = $arr_how_to_make_imperativeform_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_e, $int_selected_language);
			return $arr_inflected_label;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 可能動詞を作る
function generate_word_inflection_potentialverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_e,
		$arr_how_to_make_potentialverb_V2,
		$arr_how_to_make_potentialverb_V3K,
		$arr_how_to_make_potentialverb_V3S,
		$arr_how_to_make_potentialverb_V3S2,
		$arr_how_to_make_potentialverb_V3Z,
		$arr_how_to_make_potentialverb_V1;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	$arr_stages_of_inflection = $arr_inflected_label[$str_inflected_label_inflection_process];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'られる';
			$str_kana = $str_kana.'られる';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来られる';
			$str_kana = $str_kana.'こられる';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'できる';
			$str_kana = $str_kana.'できる';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'せる';
			$str_kana = $str_kana.'せる';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じられる';
			$str_kana = $str_kana.'じられる';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_e, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_japanese = $str_japanese.'る';
			$str_kana = $str_kana.'る';
			$arr_how_to_make_form = $arr_how_to_make_potentialverb_V1[$int_inflection_from];
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 受身尊敬動詞を作る
function generate_word_inflection_passive_honorificverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_a,
		$arr_how_to_make_passive_honorificverb_V2,
		$arr_how_to_make_passive_honorificverb_V3K,
		$arr_how_to_make_passive_honorificverb_V3S,
		$arr_how_to_make_passive_honorificverb_V3S2,
		$arr_how_to_make_passive_honorificverb_V3Z,
		$arr_how_to_make_passive_honorificverb_V1;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			$str_japanese = $str_japanese.'られる';
			$str_kana = $str_kana.'られる';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V2[$int_inflection_from];
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'来られる';
			$str_kana = $str_kana.'こられる';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V3K[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'される';
			$str_kana = $str_kana.'される';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V3S[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'される';
			$str_kana = $str_kana.'される';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V3S2[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			$str_japanese = $str_japanese.'じられる';
			$str_kana = $str_kana.'じられる';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V3Z[$int_inflection_from];
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_a, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			$str_japanese = $str_japanese.'れる';
			$str_kana = $str_kana.'れる';
			$arr_how_to_make_form = $arr_how_to_make_passive_honorificverb_V1[$int_inflection_from];
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 使役動詞を作る
function generate_word_inflection_causativeverb($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_CausativeVerb_or_CausativePassiveVerb, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_a,
		$int_inflection_add_part_CausativeVerb,
		$arr_how_to_make_causativeverb_V2,
		$arr_how_to_make_causativeverb_V3K,
		$arr_how_to_make_causativeverb_V3S,
		$arr_how_to_make_causativeverb_V3S2,
		$arr_how_to_make_causativeverb_V3Z,
		$arr_how_to_make_causativeverb_V1,
		$arr_how_to_make_causativepassiveverb_V2,
		$arr_how_to_make_causativepassiveverb_V3K,
		$arr_how_to_make_causativepassiveverb_V3S,
		$arr_how_to_make_causativepassiveverb_V3S2,
		$arr_how_to_make_causativepassiveverb_V3Z,
		$arr_how_to_make_causativepassiveverb_V1;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'させる';
				$str_kana = $str_kana.'させる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V2[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'させられる';
				$str_kana = $str_kana.'させられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V2[$int_inflection_from];
			}
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'来させる';
				$str_kana = $str_kana.'こさせる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V3K[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'来させられる';
				$str_kana = $str_kana.'こさせられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V3K[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'させる';
				$str_kana = $str_kana.'させる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V3S[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'させられる';
				$str_kana = $str_kana.'させられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V3S[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'させる';
				$str_kana = $str_kana.'させる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V3S2[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'させられる';
				$str_kana = $str_kana.'させられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V3S2[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'じさせる';
				$str_kana = $str_kana.'じさせる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V3Z[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'じさせられる';
				$str_kana = $str_kana.'じさせられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V3Z[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_a, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'せる';
				$str_kana = $str_kana.'せる';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_V1[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'せられる';
				$str_kana = $str_kana.'せられる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_V1[$int_inflection_from];
			}
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 使役動詞短縮形を作る
function generate_word_inflection_causativeverb_shortened($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_CausativeVerb_or_CausativePassiveVerb, $int_selected_language){

	global
		$str_inflected_label_inflection_process,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_V2I,
		$int_V2E,
		$int_V3K,
		$int_V3S,
		$int_V3S2,
		$int_V3Z,
		$int_stem_a,
		$int_inflection_add_part_CausativeVerb,
		$arr_how_to_make_causativeverb_shortened_V2,
		$arr_how_to_make_causativeverb_shortened_V3K,
		$arr_how_to_make_causativeverb_shortened_V3S,
		$arr_how_to_make_causativeverb_shortened_V3S2,
		$arr_how_to_make_causativeverb_shortened_V3Z,
		$arr_how_to_make_causativeverb_shortened_V1,
		$arr_how_to_make_causativepassiveverb_shortened_V2,
		$arr_how_to_make_causativepassiveverb_shortened_V3K,
		$arr_how_to_make_causativepassiveverb_shortened_V3S,
		$arr_how_to_make_causativepassiveverb_shortened_V3S2,
		$arr_how_to_make_causativepassiveverb_shortened_V3Z,
		$arr_how_to_make_causativepassiveverb_shortened_V1;


	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	switch($t_masta_japanese_sub_classification_id){

		// 第2類動詞
		case $int_V2I:
		case $int_V2E:
			$str_japanese = mb_substr($str_japanese, 0, -1);
			$str_kana = mb_substr($str_kana, 0, -1);
			$arr_inflected_label = generate_arr_inflection_stages_for_stem_V2($int_inflection_from, $arr_inflected_label, $str_japanese, $str_kana, $int_selected_language);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'さす';
				$str_kana = $str_kana.'さす';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V2[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'さされる';
				$str_kana = $str_kana.'さされる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V2[$int_inflection_from];
			}
			break;

		//第三類(来る)
		case $int_V3K:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'来さす';
				$str_kana = $str_kana.'こさす';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V3K[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'来さされる';
				$str_kana = $str_kana.'こさされる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V3K[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//第三類(する)
		case $int_V3S:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'さす';
				$str_kana = $str_kana.'さす';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V3S[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'さされる';
				$str_kana = $str_kana.'さされる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V3S[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−スル
		case $int_V3S2:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'さす';
				$str_kana = $str_kana.'さす';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V3S2[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'さされる';
				$str_kana = $str_kana.'さされる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V3S2[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		//サ変・−ズル
		case $int_V3Z:
			$str_japanese = mb_substr($str_japanese, 0, -2);
			$str_kana = mb_substr($str_kana, 0, -2);
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'じさす';
				$str_kana = $str_kana.'じさす';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V3Z[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'じさされる';
				$str_kana = $str_kana.'じさされる';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V3Z[$int_inflection_from];
			}
			$arr_inflected_label[$str_inflected_label_inflection_process] = [];
			break;

		// 第1類動詞
		default:
			$arr_inflected_label = generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_a, $int_selected_language);
			$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
			$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			if($int_CausativeVerb_or_CausativePassiveVerb === $int_inflection_add_part_CausativeVerb){
				$str_japanese = $str_japanese.'す';
				$str_kana = $str_kana.'す';
				$arr_how_to_make_form = $arr_how_to_make_causativeverb_shortened_V1[$int_inflection_from];
			}else{
				$str_japanese = $str_japanese.'される';
				$str_kana = $str_kana.'される';
				$arr_how_to_make_form = $arr_how_to_make_causativepassiveverb_shortened_V1[$int_inflection_from];
			}
			break;
	}

	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// 動詞語幹を作る
function generate_word_inflection_stem($int_inflection_from, $arr_inflected_label, $t_masta_japanese_sub_classification_id, $int_stem_type, $int_selected_language){

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$arr_how_to_make_verb_stem,
		$arr_inflection_ending;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	$str_japanese = mb_substr($str_japanese, 0, -1);
	$str_kana = mb_substr($str_kana, 0, -1);
	$arr_how_to_make_form = $arr_how_to_make_verb_stem[$int_inflection_from][$int_stem_type];


	$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$arr_inflection_ending[$t_masta_japanese_sub_classification_id][$int_stem_type];
	$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$arr_inflection_ending[$t_masta_japanese_sub_classification_id][$int_stem_type];
	$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

	return $arr_inflected_label;
}


// い形容詞語幹を作る
function generate_word_inflection_stem_ia($arr_inflected_label, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_selected_language){

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$int_AI2,
		$str_inflection_IA_ending_cc,
		$str_inflection_IA_ending_hy,
		$arr_how_to_make_stem_ia,
		$arr_how_to_make_stem_ia_i_to_yo;

	$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
	$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];

	if($t_masta_japanese_sub_classification_id == $int_AI2){

		$str_japanese = mb_substr($str_japanese, 0, -1);
		$str_japanese_last = mb_substr($str_japanese, -1);

		$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
		$arr_how_to_make_form = $arr_how_to_make_stem_ia;
		$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);

		$str_japanese = mb_substr($str_japanese, 0, -1);
		$str_kana = mb_substr($str_kana, 0, -2);


		if($str_japanese_last == $str_inflection_IA_ending_cc){
			$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_inflection_IA_ending_cc;
			$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_inflection_IA_ending_hy;
		}
		else{
			$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese.$str_inflection_IA_ending_hy;
			$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana.$str_inflection_IA_ending_hy;
			$arr_how_to_make_form = $arr_how_to_make_stem_ia_i_to_yo;
			$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
		}

	}
	else{
		$str_japanese = mb_substr($str_japanese, 0, -1);
		$str_kana = mb_substr($str_kana, 0, -1);

		$arr_inflected_label[$str_snake_to_camel_japanese] = $str_japanese;
		$arr_inflected_label[$str_snake_to_camel_kana] = $str_kana;

		$arr_how_to_make_form = $arr_how_to_make_stem_ia;
		$arr_inflected_label = apply_inflection_stage_add_form_explanation($arr_inflected_label, $arr_how_to_make_form, $int_selected_language);
	}

	return $arr_inflected_label;
}



/******************************************************
 *  GET
 *  FETCH
 *  
 ******************************************************/

function get_arr_japanese_from_labels($arr_search_condition, $int_selected_language){

	global
		$t_japanese_labels,
		$t_masta_japanese_label,
		$str_column_label_japanese,
		$str_column_label_kana;

	$arr_strSQL_select = [
		[$t_japanese_labels,'id'],
		[$t_japanese_labels,'japanese_element_id'],
		[$t_masta_japanese_label,$str_column_label_japanese],
		[$t_masta_japanese_label,$str_column_label_kana]
	];

	$strSQL_from = " FROM
					$t_japanese_labels
					INNER JOIN $t_masta_japanese_label
					ON
					$t_japanese_labels.masta_japanese_label_id = $t_masta_japanese_label.id
					";

	if(empty($arr_search_condition)){
		$arr_strSQL_where = $arr_search_condition;
	}
	else{
		$arr_strSQL_where = [
			[
				$arr_search_condition,
				''
			]
		];
	}

	$arr_strSQL_order = [
		[$t_japanese_labels,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_japanese_labels) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_japanese_labels;
}


function get_arr_japanese_from_labels_with_content($arr_search_condition, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_japanese_elements,
		$t_masta_japanese_sub_category,
		$t_japanese_labels,
		$t_masta_japanese_label,
		$t_masta_japanese_sub_classification,
		$t_masta_japanese_classification,
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_japanese_polite,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_kana_polite,
		$str_snake_to_camel_category_id,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_sub_classification,
		$str_snake_to_camel_classification,
		$str_t_masta_japanese_root_1,
		$str_t_masta_japanese_root_2,
		$int_used_language_jpn;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,'root_japanese_polite as '.$str_snake_to_camel_japanese_polite],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese_root,'root_kana_polite as '.$str_snake_to_camel_kana_polite],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id],
		[$t_japanese_elements,'masta_japanese_sub_classification_id as '.$str_snake_to_camel_sub_classification_id],
		[$str_t_masta_japanese_root_1,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_sub_classification],
		[$str_t_masta_japanese_root_2,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_classification]
	];

	$strSQL_from = " FROM
					(
						(
							(
								(
									(
										$t_masta_japanese_root
										INNER JOIN $t_japanese_elements
										ON
										$t_masta_japanese_root.id = $t_japanese_elements.masta_japanese_root_id
									)
									INNER JOIN $t_masta_japanese_sub_category
									ON
									$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
								)
								INNER JOIN $t_japanese_labels
								ON
								$t_japanese_elements.id = $t_japanese_labels.japanese_element_id
							)
							INNER JOIN $t_masta_japanese_label
							ON
							$t_japanese_labels.masta_japanese_label_id = $t_masta_japanese_label.id
						)
						INNER JOIN $t_masta_japanese_sub_classification
						ON
						$t_japanese_elements.masta_japanese_sub_classification_id = $t_masta_japanese_sub_classification.id
						INNER JOIN $t_masta_japanese_classification
						ON
						$t_masta_japanese_sub_classification.classification_id = $t_masta_japanese_classification.id
					)
					INNER JOIN $t_masta_japanese_root AS $str_t_masta_japanese_root_1 ON $t_masta_japanese_sub_classification.masta_japanese_root_id = $str_t_masta_japanese_root_1.id
					INNER JOIN $t_masta_japanese_root AS $str_t_masta_japanese_root_2 ON $t_masta_japanese_classification.masta_japanese_root_id = $str_t_masta_japanese_root_2.id
					";

	if(empty($arr_search_condition)){
		$arr_strSQL_where = $arr_search_condition;
	}
	else{
		$arr_strSQL_where = [
			[
				$arr_search_condition,
				''
			]
		];
	}

	$arr_strSQL_order = [
		[$t_masta_japanese_root,'root_kana','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_japanese_labels) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_japanese_labels)){
		 return [];
	}

	$arr_japanese_labels = array_column($arr_japanese_labels, null, $str_snake_to_camel_japanese_id);

	return $arr_japanese_labels;
}


function get_arr_japanese_from_search_criteria($arr_strSQL_where, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$str_snake_to_camel_parent_id,
		$str_snake_to_camel_parent_sort,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_category_id,
		$str_snake_to_camel_level,
		$str_snake_to_camel_sort,
		$int_used_language_jpn;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,'masta_id as '.$str_snake_to_camel_parent_id],
		[$t_masta_japanese,'sort as '.$str_snake_to_camel_parent_sort],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id],
		[$t_masta_japanese_root,'jws_level as '.$str_snake_to_camel_level],
		[$t_masta_japanese_root,'sort as '.$str_snake_to_camel_sort]
	];

	$strSQL_from = " FROM
					(
						$t_masta_japanese_root
						INNER JOIN $t_masta_japanese
						ON
						$t_masta_japanese_root.masta_id = $t_masta_japanese.id
					)
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";

	$arr_strSQL_where = $arr_strSQL_where;

	$arr_strSQL_order = [
		[$t_masta_japanese_root,'jws_level','ASC'],
		[$t_masta_japanese_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_root)){
		return [];
	}

	$arr_masta_japanese_root = array_column($arr_masta_japanese_root, null, $str_snake_to_camel_japanese_id);

	$targets = [
        $str_snake_to_camel_japanese,
        $arr_columns_masta_japanese_root[$int_used_language_jpn],
        $arr_columns_masta_japanese_root[$int_selected_language],
        'root_example',
        $str_snake_to_camel_kana
    ];

    foreach ($arr_masta_japanese_root as &$row) {
        foreach ($targets as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null) {
                $row[$key] = apply_remove_original_tags($row[$key]);
            }
        }
    }
    unset($row);

	return $arr_masta_japanese_root;
}


function get_arr_japanese_from_search_criteria_with_labels($arr_search_condition, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese,
		$t_masta_japanese_sub_category,
		$t_japanese_elements,
		$t_japanese_labels,
		$t_masta_japanese_sub_classification,
		$t_masta_japanese_classification,
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$str_snake_to_camel_parent_id,
		$str_snake_to_camel_parent_sort,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_japanese_polite,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_kana_polite,
		$str_snake_to_camel_category_id,
		$str_snake_to_camel_level,
		$str_snake_to_camel_sort,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_sub_classification,
		$str_snake_to_camel_classification,
		$str_t_masta_japanese_root_2,
		$str_t_masta_japanese_root_3,
		$int_used_language_jpn;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,'masta_id as '.$str_snake_to_camel_parent_id],
		[$t_masta_japanese,'sort as '.$str_snake_to_camel_parent_sort],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,'root_japanese_polite as '.$str_snake_to_camel_japanese_polite],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese_root,'root_kana_polite as '.$str_snake_to_camel_kana_polite],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id],
		[$t_masta_japanese_root,'jws_level as '.$str_snake_to_camel_level],
		[$t_masta_japanese_root,'sort as '.$str_snake_to_camel_sort],
		[$t_japanese_elements,'masta_japanese_sub_classification_id as '.$str_snake_to_camel_sub_classification_id],
		[$str_t_masta_japanese_root_2,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_sub_classification],
		[$str_t_masta_japanese_root_3,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_classification]
	];

	$strSQL_from = " FROM
					(
						(
							(
								(
									$t_masta_japanese_root
									INNER JOIN $t_masta_japanese
									ON
									$t_masta_japanese_root.masta_id = $t_masta_japanese.id
								)
								INNER JOIN $t_masta_japanese_sub_category
								ON
								$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
							)
							INNER JOIN $t_japanese_elements
							ON
							$t_masta_japanese_root.id = $t_japanese_elements.masta_japanese_root_id
							INNER JOIN $t_japanese_labels
							ON
							$t_japanese_elements.id = $t_japanese_labels.japanese_element_id
						)
						INNER JOIN $t_masta_japanese_sub_classification
						ON
						$t_japanese_elements.masta_japanese_sub_classification_id = $t_masta_japanese_sub_classification.id
						INNER JOIN $t_masta_japanese_classification
						ON
						$t_masta_japanese_sub_classification.classification_id = $t_masta_japanese_classification.id
					)
					INNER JOIN $t_masta_japanese_root AS $str_t_masta_japanese_root_2 ON $t_masta_japanese_sub_classification.masta_japanese_root_id = $str_t_masta_japanese_root_2.id
					INNER JOIN $t_masta_japanese_root AS $str_t_masta_japanese_root_3 ON $t_masta_japanese_classification.masta_japanese_root_id = $str_t_masta_japanese_root_3.id
					";

	if(empty($arr_search_condition)){
		$arr_strSQL_where = $arr_search_condition;
	}
	else{
		$arr_strSQL_where = [
			[
				$arr_search_condition,
				''
			]
		];
	}

	$arr_strSQL_order = [
				[$t_masta_japanese_root,'jws_level','ASC'],
				[$t_masta_japanese_root,'sort','ASC']
			];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_root)){
		return [];
	}

	$arr_masta_japanese_root = array_column($arr_masta_japanese_root, null, $str_snake_to_camel_japanese_id);

	return $arr_masta_japanese_root;
}


function get_arr_matched_contents_from_categories($originalArray, $arr_search_condition, $int_selected_language){

	global
	    $t_masta_japanese_sub_category;

	$arr_matched_contents = [];

	$removedArray = $originalArray;
	if (isset($removedArray[SELECT_ALL])) {
		unset($removedArray[SELECT_ALL]);
	}
	foreach($removedArray as $key => $loop_removedArray){
		$arr_search_condition_current = $arr_search_condition;
		$arr_search_condition_current[] = [$t_masta_japanese_sub_category,'category_id','=',escape_html($key),'PDO::PARAM_INT',''];
		$arr_strSQL_where = [
			[
				$arr_search_condition_current,
				''
			]
		];
		$arr_matched_contents_add = get_arr_japanese_from_search_criteria($arr_strSQL_where, $int_selected_language);
		$arr_matched_contents = !empty($arr_matched_contents) ? $arr_matched_contents + $arr_matched_contents_add : $arr_matched_contents_add;
	}
	return $arr_matched_contents;
}


function get_arr_matched_contents_from_category_id($category_id, $arr_search_condition, $int_selected_language){

	global
	    $t_masta_japanese_sub_category;

	$arr_matched_contents = [];

	$arr_search_condition[] = [$t_masta_japanese_sub_category,'category_id','=',$category_id,'PDO::PARAM_INT',''];
	$arr_strSQL_where = [
		[
			$arr_search_condition,
			''
		]
	];
	$arr_matched_contents = get_arr_japanese_from_search_criteria($arr_strSQL_where, $int_selected_language);

	return $arr_matched_contents;
}


function fetch_str_sub_classification_name_by_id($t_masta_japanese_sub_classification_id, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_classification,
		$arr_columns_masta_japanese_root,
		$arr_columns_masta_japanese_sub_classification,
		$arr_masta_japanese_sub_classification;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$arr_columns_masta_japanese_sub_classification[$int_selected_language]]
	];

	$strSQL_from = " FROM
					$t_masta_japanese_sub_classification
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_japanese_sub_classification.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_classification,'id','=',$t_masta_japanese_sub_classification_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_sub_classification) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_sub_classification)){
		return '';
	}
	$str_japanese_sub_classification = $arr_masta_japanese_sub_classification[INDEX_FIRST][$arr_columns_masta_japanese_sub_classification[$int_selected_language]];
	return $str_japanese_sub_classification;
}


function fetch_str_form_name_by_form_root_id($t_masta_form_root_id, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_form,
		$t_masta_form_root,
		$arr_columns_masta_japanese_root,
		$arr_masta_form_root;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_form_root,'id','=',$t_masta_form_root_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		return '';
	}
	$str_japanese_form = $arr_masta_form_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]];
	return $str_japanese_form;
}


function get_str_voice_name_by_id($int_voice_id, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_form,
		$t_masta_form_root,
		$arr_columns_masta_japanese_root,
		$int_PotentialVerb,
		$arr_masta_form_root;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_form_root,'id','=',$int_voice_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		return '';
	}
	if($int_voice_id < $int_PotentialVerb){
		return '';
	}
	$str_japanese_voice = $arr_masta_form_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]];
	return $str_japanese_voice;
}
