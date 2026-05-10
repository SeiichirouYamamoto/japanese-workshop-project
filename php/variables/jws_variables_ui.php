<?php

/******************************************************
 *  JWS mastery level
 *  
 ******************************************************/
$arr_mastery_level = [
    $int_mastery_level_select_all => ['全て', '全部'],
    $int_mastery_level_jws_beginner => ['入門', '入門'],
    $int_mastery_level_jws_basic => ['基礎', '基礎'],
    $int_mastery_level_jws_intermediate => ['初級', '初級'],
    $int_mastery_level_jws_advanced => ['中級', '中級'],
    $int_mastery_level_jws_expert => ['上級', '上級'],
    $int_mastery_level_jws_master => ['高級', '高級']
];


/******************************************************
 *  USER LEVEL CONTENTS
 *  
 ******************************************************/
$allow_grammar_view_content_section_capabilities_Free_Member = [
    $int_masta_japanese_attribute_id_sentencePattern,
    $int_masta_japanese_attribute_id_explanation,
    $int_masta_japanese_attribute_id_equivalentInForeignLanguage
];

$allow_grammar_view_content_section_capabilities_Basic_Student = array_merge($allow_grammar_view_content_section_capabilities_Free_Member, [
    $int_masta_japanese_attribute_id_headingFree,
    $int_masta_japanese_attribute_id_conversationExample,
    $int_masta_japanese_attribute_id_abbreviatedForm,
    $int_masta_japanese_attribute_id_howToFormThisPattern,
    $int_masta_japanese_attribute_id_correctnessComparison,
    $int_masta_japanese_attribute_id_conjugation,
    $int_masta_japanese_attribute_id_usableWords,
    $int_masta_japanese_attribute_id_unusableWords,
    $int_masta_japanese_attribute_id_howToUseThisPattern,
    $int_masta_japanese_attribute_id_structureOfThisPattern,
    $int_masta_japanese_attribute_id_problemStatement
]);

$allow_grammar_view_content_section_capabilities_Plus_Student = $allow_grammar_view_content_section_capabilities_Basic_Student;
$allow_grammar_view_content_section_capabilities_Premium_Student = $allow_grammar_view_content_section_capabilities_Plus_Student;

$allow_grammar_view_content_section_capabilities_VIP_Student = array_merge($allow_grammar_view_content_section_capabilities_Premium_Student, [
    $int_masta_japanese_attribute_id_link
]);

$allow_grammar_view_content_section_capabilities_Over_Teacher = array_merge($allow_grammar_view_content_section_capabilities_VIP_Student, [
    $int_masta_japanese_attribute_id_activeRecall,
    $int_masta_japanese_attribute_id_advancedKnowledge
]);

$allow_grammar_view_content_section_capabilities_default = [
	$int_Free_Member => $allow_grammar_view_content_section_capabilities_Free_Member,
	$int_Basic_Student => $allow_grammar_view_content_section_capabilities_Basic_Student,
	$int_Plus_Student => $allow_grammar_view_content_section_capabilities_Plus_Student,
	$int_Premium_Student => $allow_grammar_view_content_section_capabilities_Premium_Student,
	$int_VIP_Student => $allow_grammar_view_content_section_capabilities_VIP_Student,
	$int_Basic_Teacher => $allow_grammar_view_content_section_capabilities_Over_Teacher,
	$int_Plus_Teacher => $allow_grammar_view_content_section_capabilities_Over_Teacher,
	$int_Premium_Teacher => $allow_grammar_view_content_section_capabilities_Over_Teacher,
	$int_Operator => $allow_grammar_view_content_section_capabilities_Over_Teacher,
	$int_Administrator => $allow_grammar_view_content_section_capabilities_Over_Teacher,
	$int_Super_Administrator => $allow_grammar_view_content_section_capabilities_Over_Teacher
];

$allow_grammar_view_feature_capabilities_Base_Student = [
    'target_knowledge_visible' => $int_not_allow_visible_in_grammar_view,
    'prerequisite_knowledge_visible' => $int_not_allow_visible_in_grammar_view,
    'related_knowledge_visible' => $int_not_allow_visible_in_grammar_view,
    'slider_view_visible' => $int_not_allow_visible_in_grammar_view,
    'wise_map_focus_point_visible' => $int_not_allow_visible_in_grammar_view,
    'sample_sentence_list_visible' => $int_not_allow_visible_in_grammar_view,
    'sample_sentence_list_foreign_language_text_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_comparisons_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_correspondences_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_families_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_alternatives_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_outline_terminology_visible' => $int_not_allow_visible_in_grammar_view,
    'listed_location_visible' => $int_not_allow_visible_in_grammar_view,
    'user_input_data_visible' => $int_not_allow_visible_in_grammar_view,
    'grammar_set_buttons_visible' => $int_not_allow_visible_in_grammar_view,
    'recording_shorts' => $int_is_not_recording_shorts,
    'recording_video' => $int_is_not_recording_video,
    'link_to_register_sentence_visible' => $int_not_allow_visible_in_grammar_view,
    'link_to_grammar_view_for_administrators_visible' => $int_not_allow_visible_in_grammar_view
];

$allow_grammar_view_feature_capabilities_Free_Member = $allow_grammar_view_feature_capabilities_Base_Student;

$allow_grammar_view_feature_capabilities_Basic_Student = array_merge(
    $allow_grammar_view_feature_capabilities_Free_Member,
    [
        'sample_sentence_list_foreign_language_text_visible' => $int_allow_visible_in_grammar_view
    ]
);

$allow_grammar_view_feature_capabilities_Plus_Student = $allow_grammar_view_feature_capabilities_Basic_Student;

$allow_grammar_view_feature_capabilities_Premium_Student = array_merge(
    $allow_grammar_view_feature_capabilities_Plus_Student,
    [
		'user_input_data_visible' => $int_allow_visible_in_grammar_view,
        'listed_location_visible' => $int_allow_visible_in_grammar_view
    ]
);

$allow_grammar_view_feature_capabilities_VIP_Student = $allow_grammar_view_feature_capabilities_Premium_Student;

$allow_grammar_view_feature_capabilities_Over_Teacher = array_merge(
    $allow_grammar_view_feature_capabilities_VIP_Student,
    [
        'target_knowledge_visible' => $int_allow_visible_in_grammar_view,
        'prerequisite_knowledge_visible' => $int_allow_visible_in_grammar_view,
        'related_knowledge_visible' => $int_allow_visible_in_grammar_view,
        'wise_map_focus_point_visible' => $int_allow_visible_in_grammar_view,
        'sample_sentence_list_visible' => $int_allow_visible_in_grammar_view,
        'sample_sentence_list_foreign_language_text_visible' => $int_not_allow_visible_in_grammar_view,
		'grammar_comparisons_visible' => $int_allow_visible_in_grammar_view,
        'grammar_correspondences_visible' => $int_allow_visible_in_grammar_view,
        'grammar_families_visible' => $int_allow_visible_in_grammar_view,
        'grammar_alternatives_visible' => $int_allow_visible_in_grammar_view,
        'grammar_outline_terminology_visible' => $int_allow_visible_in_grammar_view,
        'grammar_set_buttons_visible' => $int_allow_visible_in_grammar_view
    ]
);

$allow_grammar_view_feature_capabilities_default = [
	$int_Free_Member => $allow_grammar_view_feature_capabilities_Free_Member,
	$int_Basic_Student => $allow_grammar_view_feature_capabilities_Basic_Student,
	$int_Plus_Student => $allow_grammar_view_feature_capabilities_Plus_Student,
	$int_Premium_Student => $allow_grammar_view_feature_capabilities_Premium_Student,
	$int_VIP_Student => $allow_grammar_view_feature_capabilities_VIP_Student,
	$int_Basic_Teacher => $allow_grammar_view_feature_capabilities_Over_Teacher,
	$int_Plus_Teacher => $allow_grammar_view_feature_capabilities_Over_Teacher,
	$int_Premium_Teacher => $allow_grammar_view_feature_capabilities_Over_Teacher,
	$int_Operator => $allow_grammar_view_feature_capabilities_Over_Teacher,
	$int_Administrator => $allow_grammar_view_feature_capabilities_Over_Teacher,
	$int_Super_Administrator => $allow_grammar_view_feature_capabilities_Over_Teacher
];

$arr_allow_visible_override_keys = [
    'target_knowledge_visible',
    'prerequisite_knowledge_visible',
    'related_knowledge_visible',
    'slider_view_visible',
    'wise_map_focus_point_visible',
    'sample_sentence_list_visible',
    'grammar_comparisons_visible',
    'grammar_correspondences_visible',
    'grammar_families_visible',
    'grammar_alternatives_visible',
    'grammar_outline_terminology_visible',
    'listed_location_visible',
    'user_input_data_visible',
    'grammar_set_buttons_visible',
    'recording_shorts',
    'recording_video'
];

$arr_section_grammar_relation = [
    'grammar_comparisons_visible' => [
        'sets' => $t_grammar_comparison_sets,
        'items' => $t_grammar_comparison_items,
        'class' => 'sectionGrammarComparisons'
    ],
    'grammar_correspondences_visible' => [
        'sets' => $t_grammar_correspondence_sets,
        'items' => $t_grammar_correspondence_items,
        'class' => 'sectionGrammarCorrespondences'
    ],
    'grammar_families_visible' => [
        'sets' => $t_grammar_family_sets,
        'items' => $t_grammar_family_items,
        'class' => 'sectionGrammarFamilies'
    ],
    'grammar_alternatives_visible' => [
        'sets' => $t_grammar_alternative_sets,
        'items' => $t_grammar_alternative_items,
        'class' => 'sectionGrammarAlternatives'
    ],
];



/******************************************************
 *  arr_select
 *  
 ******************************************************/
$arr_select_select_all = ['すべて','全部'];

$arr_select_japanese_category_id_search_grammar = [
	SELECT_ALL => $arr_select_select_all,
	$int_masta_japanese_category_id_grammar => ['文法','文法'],
	$int_masta_japanese_category_id_terminology => ['専門用語','專用語'],
	$int_masta_japanese_category_id_grammar_relation => ['文法比較','文法比較'],
	$int_masta_japanese_category_id_particle_with_label => ['助詞','助詞'],
];


$arr_select_japanese_category_id_search_lesson_contents = [
	SELECT_ALL => $arr_select_select_all,
	$int_masta_japanese_category_id_word => ['単語','單字'],
	$int_masta_japanese_category_id_grammar => ['文法','文法'],
	$int_masta_japanese_category_id_terminology => ['専門用語','專用語'],
	$int_masta_japanese_category_id_grammar_relation => ['文法比較','文法比較'],
];


$int_learning_scope_already_learned = STATUS_FIRST;
$arr_select_learning_scope = [
	SELECT_ALL => $arr_select_select_all,
	$int_learning_scope_already_learned => ['既習のみ','已經學習過']
];


$int_matching_type_partial_matching = STATUS_FIRST;
$int_matching_type_perfect_matching = STATUS_SECOND;
$arr_select_matching_type = [
	$int_matching_type_partial_matching => ['部分一致','部分一致'],
	$int_matching_type_perfect_matching => ['完全一致','完全一致']
];


$FORM_STYLE_PLAIN = $int_DictionaryForm;
$FORM_STYLE_POLITE = $int_PoliteFormAffirmativeNotPastTense;
$arr_select_form_style = [
	$FORM_STYLE_PLAIN => ['普通形','普通形'],
	$FORM_STYLE_POLITE => ['丁寧形','丁寧形']
];


$int_output_style_to_wordContainer = STATUS_FIRST;
$OUTPUT_STYLE_TEXTAREA_CONTAINER = STATUS_SECOND;
$arr_select_output_style = [
	$int_output_style_to_wordContainer => ['word','word'],
	$OUTPUT_STYLE_TEXTAREA_CONTAINER => ['textArea','textArea']
];


$ORDER_STYLE_ASCENDING = STATUS_FIRST;
$int_order_style_descending = STATUS_SECOND;
$int_order_style_random = STATUS_THIRD;
$arr_select_order_style = [
	$ORDER_STYLE_ASCENDING => ['昇順','升序'],
	$int_order_style_random => ['ランダム','random']
];


$int_kana_visible = FLAG_TRUE;
$int_kana_invisible = FLAG_FALSE;
$arr_select_kana_visible = [
	$int_kana_visible => ['表示','visible'],
	$int_kana_invisible => ['非表示','invisible']
];



/******************************************************
 *  learning_status
 *  
 ******************************************************/
$arr_learning_status = [
	$int_learning_status_not_started => [
		'title' => ['未習', '未學習'],
		'html_id_class' => 'notStarted'
	],
	$int_learning_status_learning => [
		'title' => ['学習中', '學習中'],
		'html_id_class' => 'learning'
	],
	$int_learning_status_learned => [
		'title' => ['既習', '已學習'],
		'html_id_class' => 'learned'
	],
];
/******************************************************
 *  active_recall
 *  
 ******************************************************/
$int_active_recall_not_started = -1;
$int_active_recall_learned = STATUS_FIRST;
$int_active_recall_in_progress = STATUS_SECOND;
$int_active_recall_d1 = STATUS_THIRD;
$int_active_recall_d3 = STATUS_FOURTH;
$int_active_recall_w1 = STATUS_FIFTH;
$int_active_recall_m1 = STATUS_SIXTH;
$int_active_recall_mastered = STATUS_SEVENTH;

$arr_active_recall_definitions = [
	$int_active_recall_learned => [
		'title' => ['Learned','Learned'],
		'html_id_class' => 'learned'
	],
	$int_active_recall_in_progress => [
		'title' => ['In Progress','In Progress'],
		'html_id_class' => 'inProgress'
	],
	$int_active_recall_d1 => [
		'title' => ['D1','D1'],
		'html_id_class' => 'activeRecallD1'
	],
	$int_active_recall_d3 => [
		'title' => ['D3','D3'],
		'html_id_class' => 'activeRecallD3'
	],
	$int_active_recall_w1 => [
		'title' => ['W1','W1'],
		'html_id_class' => 'activeRecallW1'
	],
	$int_active_recall_m1 => [
		'title' => ['M1','M1'],
		'html_id_class' => 'activeRecallM1'
	],
	$int_active_recall_mastered => [
		'title' => ['MTR','MTR'],
		'html_id_class' => 'activeRecallMastered'
	]
];



/******************************************************
 *  BOOKMARK
 *  
 ******************************************************/
$arr_bookmark_filter = [
	$bookmark_filter_active => [
		'title' => ['現在のブックマーク','目前的書籤'],
		'html_id_class' => 'reviewRequired'
	],
	$bookmark_filter_inactive => [
		'title' => ['過去のブックマーク','過去的書籤'],
		'html_id_class' => 'archived'
	],
];



/******************************************************
 *  grammar_outline
 *  
 ******************************************************/

$str_grammarOutlineLabelButtonUlOpener = ['▶','▶'];
$str_grammarOutlineLabelButtonUlOpenerCompleted = ['▼','▼'];
$str_grammarOutlineLabelButtonExplanation = ['説明ページへ','往說明頁'];
$str_grammarOutlineLabelButtonExplanationMarker = ['ℹ️','ℹ️'];
$str_grammarOutlinePreviewScreenTitle = ['Preview','Preview'];




/******************************************************
 *  grammar_insights
 *  
 ******************************************************/
$arr_str_grammar_insights_display_titles = ['Display Titles','Display Titles'];
$arr_str_grammar_insights_display_examples = ['Display Examples','Display Examples'];
$arr_str_grammar_insights_commons = ['Display Commons','Display Commons'];
$arr_str_grammar_insights_non_commons = ['Display Non Commons','Display Non Commons'];
$arr_str_grammar_insights_sentences = ['Display Sentences','Display Sentences'];
$arr_str_grammar_insights_random_sentences = ['Display Random Sentences','Display Random Sentences'];
$arr_str_grammar_insights_active_recall = ['Display Active Recall','Display Active Recall'];
$arr_str_grammar_insights_download_items = ['Download Items','Download Items'];
$arr_str_grammar_insights_create_quiz_links = ['Create Quiz Links','Create Quiz Links'];
$arr_str_grammar_insights_upsert_homework = ['Upsert Homework','Upsert Homework'];
$arr_str_grammar_insights_user_input_data = ['Display User Input Data','Display User Input Data'];



/******************************************************
 *  button_caption
 *  
 ******************************************************/
$arr_str_button_caption_submit = ['送信','提交'];
$arr_str_button_caption_next = ['Next','Next'];
$arr_str_button_caption_go = ['Go','Go'];
$arr_str_button_caption_exit = ['閉じる','關閉'];
$arr_str_button_caption_confirm = ['決定','確定'];
$arr_str_button_caption_show_answer = ['正解を見る','正確答案'];
$arr_str_button_caption_update = ['更新', '更新'];

$arr_str_button_caption_to_grammar = ['文法','文法'];
$arr_str_button_caption_to_register_sentence = ['to register_sentence','to register_sentence'];
$arr_str_button_caption_to_grammar_view_for_administrators = ['to grammar_view_for_administrators','to grammar_view_for_administrators'];



/******************************************************
 *  message
 *  
 ******************************************************/
$arr_str_mistake_connect_database = ['「データベース接続」 に失敗しました。', '「連接」 失敗了。'];
$arr_str_mistake_select_table = ['「SELECT」 に失敗しました。', '「SELECT」 失敗了。'];
$arr_str_mistake_insert_table = ['「INSERT」 に失敗しました。', '「INSERT」 失敗了。'];


$arr_str_apply_for_classroom_title = ['Classroom申請','Classroom申請'];
$arr_str_apply_for_classroom_application_submitted = [
	'Classroom申請を送信しました。承諾までお待ちください。このタブを閉じてください',
	'您的Classroom申請已送出。請等待老師核准。您可以關閉此分頁。'
];
$arr_str_apply_for_classroom_already_applied = ['すでに申請済みです。','已提出申請，請等待核准。'];
$arr_str_apply_for_classroom_application_errored = [
	'Classroomコードが見つかりませんでした。もう一度確認してください。',
	'找不到這個 Classroom 代碼。請再確認一次。'
];

$arr_str_notice_loop = [
	'エラーが発生しました。','エラーが発生しました。'
];

$arr_str_web_page_under_preparation = [
	'このページのコンテンツは まだ準備中です。','本頁內容仍在準備中。'
];



/******************************************************
 *  header
 *  
 ******************************************************/
$arr_str_grammar_usages_header = ['関連項目','相關項目'];
$arr_str_grammar_usages_header_target_knowledge = ['目標知識','目標知識'];
$arr_str_grammar_usages_header_prerequisite_knowledge = ['前提知識','前提知識'];
$arr_str_grammar_usages_header_related_knowledge = ['関連項目','相關項目'];
$arr_str_grammar_outline_teader_terminology = ['関連文法','相關文法'];



/******************************************************
 *  placeholder
 *  
 ******************************************************/
$arr_str_placeholder_room_unique_code = [
	'unique_codeを入力してください。',
	'請輸入unique_code。'
];
$arr_str_placeholder_room_name = [
	'新しく作成するroomの名前を入力してください。',
	'請輸入新room的名稱。'
];
$arr_str_placeholder_lesson_name = [
	'教科書のタイトルとlessonのナンバー(第一課・Lesson1など)を入力してください。',
	'請輸入教材的title及lesson number(第一課・Lesson1等)'
];
$arr_str_placeholder_lesson_step_name = [
	'lesson内の各種目標(名詞文を学ぶ・疑問文を学ぶなど)を入力してください。',
	'請輸入課程的各種目標（例如「學習名詞句子」或「學習問句」等）。'
];
$arr_str_placeholder_lesson_step_unit_name = [
	'ユニット名を選んでください',
	'請選擇單元名稱'
];
$arr_str_placeholder_wise_navigation_title = [
	'titleを入力してください。',
	'請輸入title。'
];



/******************************************************
 *  html
 *  
 ******************************************************/
$str_mark_cross = '×';
$str_mark_square = '□';
$str_mark_split = '⧉';


/******************************************************
 *  SENTENCES
 *  
 ******************************************************/
$arr_str_sampleSentenceListViewFuriganaButton = ['かな','かな'];
$arr_str_sampleSentenceListViewAnswerButton = ['Answer','Answer'];
$arr_str_sampleSentenceListToEditRegisteredSentence = ['例文編集','編輯例句'];
$arr_str_sampleSentenceListToCreateLayersButton = ['レイヤー作成','圖層創建'];
$arr_str_sampleSentenceListToWiseMapFocusPointButton = ['Map','Map'];
$arr_str_sampleSentenceListToManageWiseNavigationButton = ['Manage Navi','Manage Navi'];
$arr_str_sampleSentenceListToWiseNavigationButton = ['Select Navi','Select Navi'];

$arr_sample_sentence_list_tag = ['例文','例句'];
$arr_sample_sentence_list_contains_layers_tag = ['例文 (文章構造あり)','例句 (含有文章構造)'];
