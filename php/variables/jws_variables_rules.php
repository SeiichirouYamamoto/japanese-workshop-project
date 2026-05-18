<?php

/******************************************************
 *  多言語サイト化ルール 
 *  
 ******************************************************/
$int_used_language_jpn = INDEX_FIRST;
$int_used_language_cht = INDEX_SECOND;
$int_used_language_eng = INDEX_THIRD;



/******************************************************
 *  USER LEVEL 
 *  
 ******************************************************/

// === 無料会員レベル（0） ===
$int_Free_Member = 0; // W.I.S.E. Workshop-Trial 会員登録のみ

// === 学生レベル（1〜4） ===
$int_Basic_Student = 1; // W.I.S.E. Workshop-Basic 月210元 年2400元 ROM専用 noRoom N5-N4相当のコンテンツ
$int_Plus_Student = 2; // W.I.S.E. Workshop-Plus 月525元 年6000元 ROM + テキストベースの質問可能 noRoom N5-N4相当のコンテンツ
$int_Premium_Student = 3; // W.I.S.E. Workshop-Premium 月1050元 年12000元 (1hの無料レッスン付き) + 以降ワンレッスン1000元/1h inRoom N5-N1学生さんに合わせる
$int_VIP_Student = 4; // 「例外」 inRoom

// === 教師レベル（5〜7） ===
$int_Basic_Teacher = 5; // 教師見習い・補助
$int_Plus_Teacher = 6; // 認定教師（指導担当可能）
$int_Premium_Teacher = 7; // 上級教師／コーチ（教材開発・育成対応可）

// === 管理者レベル（8〜10） ===
$int_Operator = 8; // 運営スタッフ（限定管理権限）
$int_Administrator = 9; // 一般管理者（会員管理・設定可）
$int_Super_Administrator = 10; // 最高責任者（全権限）



/******************************************************
 *  Prices
 *  
 ******************************************************/
$arr_membership_prices = [

    $int_Basic_Student => [
        'monthly' => [
            '1,050円',
            'NT$210',
            '$6.99',
        ],
        'yearly' => [
            '12,000円',
            'NT$2400',
            '$79.99',
        ],
    ],

    $int_Plus_Student => [
        'monthly' => [
            '2,600円',
            'NT$525',
            '$17.99',
        ],
        'yearly' => [
            '30,000円',
            'NT$6000',
            '$199.99',
        ],
    ],

    $int_Premium_Student => [
        'monthly' => [
            '5,200円',
            'NT$1050',
            '$34.99',
        ],
        'yearly' => [
            '60,000円',
            'NT$12000',
            '$399.99',
        ],
        'lesson_fee' => [
            'regular' => [
                '5,000円 / 1時間',
                'NT$1,000 / 小時',
                'USD $35 / hour',
            ],
            'benefit' => [
                '毎月1時間の授業を無料提供（当月のみ有効）',
                '每月提供 1 小時免費課程（僅限當月）',
                '1 free lesson hour per month (valid for the month only)',
            ],
        ],
    ],

];


/******************************************************
 *  DEFAULT
 *  
 ******************************************************/
$int_default_value_proxy_0 = 0;
$str_avoid_null_proxy = '---';



/******************************************************
 *  learning_status
 *  
 ******************************************************/
$int_learning_status_not_started = STATUS_FIRST;
$int_learning_status_learning = STATUS_SECOND;
$int_learning_status_learned = STATUS_THIRD;



/******************************************************
 *  php js connection
 *  
 ******************************************************/
$str_snake_to_camel_japanese_id = 'japaneseId';
$str_snake_to_camel_japanese_element_id = 'japaneseElementId';
$str_snake_to_camel_sub_classification_id = 'subClassificationId';
$str_snake_to_camel_form_id = 'formId';
$str_snake_to_camel_label_id = 'labelId';
$str_snake_to_camel_voice_id = 'voiceId';
$str_snake_to_camel_link_id = 'linkId';
$str_snake_to_camel_category_id = 'categoryId';

$str_snake_to_camel_japanese = 'japanese';
$str_snake_to_camel_japanese_polite = 'japanesePolite';
$str_snake_to_camel_kana = 'kana';
$str_snake_to_camel_kana_polite = 'kanaPolite';
$str_snake_to_camel_classification = 'classification';
$str_snake_to_camel_sub_classification = 'subClassification';
$str_snake_to_camel_form = 'form';
$str_snake_to_camel_voice = 'voice';
$str_snake_to_camel_level = 'level';
$str_snake_to_camel_sort = 'sort';
$str_snake_to_camel_parent_id = 'parentId';
$str_snake_to_camel_parent_sort = 'parentSort';
$str_snake_to_camel_root_example = 'rootExample';


/******************************************************
 *  php js connection unique_code
 *  
******************************************************/

$str_snake_to_camel_unique_code = 'uniqueCode';

$str_snake_to_camel_grammar_unique_code = 'grammarUniqueCode';
$str_snake_to_camel_japanese_element_unique_code = 'japaneseElementUniqueCode';
$str_snake_to_camel_japanese_label_unique_code = 'japaneseLabelUniqueCode';

$str_snake_to_camel_layer_unique_code = 'layerUniqueCode';
$str_snake_to_camel_layer_element_unique_code = 'layerElementUniqueCode';
$str_snake_to_camel_sentence_unique_code = 'sentenceUniqueCode';

$str_snake_to_camel_room_unique_code = 'roomUniqueCode';
$str_snake_to_camel_room_lesson_unique_code = 'roomLessonUniqueCode';
$str_snake_to_camel_room_lesson_step_unique_code = 'roomLessonStepUniqueCode';
$str_snake_to_camel_room_lesson_step_unit_unique_code = 'roomLessonStepUnitUniqueCode';
$str_snake_to_camel_user_lesson_unique_code = 'userLessonUniqueCode';

$str_snake_to_camel_wise_navigation_unique_code = 'wiseNavigationUniqueCode';
$str_snake_to_camel_wise_navigation_waypoint_unique_code = 'wiseNavigationWaypointUniqueCode';
$str_snake_to_camel_wise_navigation_script_unique_code = 'wiseNavigationScriptUniqueCode';
$str_snake_to_camel_wise_navigation_item_unique_code = 'wiseNavigationItemUniqueCode';


/******************************************************
 *  grammar_insights
 *  
 ******************************************************/
$int_grammar_insights_display_titles = STATUS_FIRST;
$int_grammar_insights_display_examples = STATUS_SECOND;
$int_grammar_insights_user_input_data = STATUS_THIRD;
$int_grammar_insights_sentences = STATUS_FOURTH;
$int_grammar_insights_random_sentences = STATUS_FIFTH;
$int_grammar_insights_active_recall = STATUS_SIXTH;
$int_grammar_insights_download_items = STATUS_SEVENTH;
$int_grammar_insights_create_quiz_links = STATUS_EIGHTH;
$int_grammar_insights_upsert_homework = STATUS_NINTH;

$int_grammar_insights_attribute_postJson = STATUS_FIRST;
$int_grammar_insights_attribute_links = STATUS_SECOND;
$int_grammar_insights_attribute_buttons = STATUS_THIRD;



/******************************************************
 *  FLAG 
 *  
 ******************************************************/
$int_allow_visible_in_grammar_view = FLAG_TRUE;
$int_not_allow_visible_in_grammar_view = FLAG_FALSE;

$int_is_recording_shorts = FLAG_TRUE;
$int_is_not_recording_shorts = FLAG_FALSE;

$int_is_recording_video = FLAG_TRUE;
$int_is_not_recording_video = FLAG_FALSE;




/******************************************************
 *  globalVerticalToolbar 
 *  
 ******************************************************/
$str_globalVerticalToolbarSelectorButton_id = 'globalVerticalToolbarSelectorButton';
$str_globalVerticalToolbarOpenWiseButton_id = 'globalVerticalToolbarOpenWiseButton';
$str_globalVerticalToolbarManageRoomsButton_id = 'globalVerticalToolbarManageRoomsButton';
$str_globalVerticalToolbarLaserButton_id = 'globalVerticalToolbarLaserButton';

$int_phrase_clause_id_target = -1;
$str_phrase_clause_container = 'phraseClauseContainer';


/******************************************************
 *  INT 
 *  
 ******************************************************/
$str_option_value_default = 'default';
$str_option_value_array = 'array';
$str_option_value_select_all = 'Select All';


/******************************************************
 *  INT 
 *  
 ******************************************************/
$int_grammar_navigation_max_length = 100;
$int_unique_code_max_length = 12;


/******************************************************
 *  SEARCH_SCOPE 
 *  
 ******************************************************/
$search_scope_wise_category = INDEX_FIRST;
$search_scope_wise_sub_category = INDEX_SECOND;
$search_scope_lesson_contents = INDEX_THIRD;
$search_scope_manage_lesson_contents = INDEX_FOURTH;


$search_scope_current_user = INDEX_FIRST;
$search_scope_room_members = INDEX_SECOND;
$search_scope_room_owner_user = INDEX_THIRD;
/******************************************************
 *  BOOKMARK 
 *  
 ******************************************************/
$bookmark_filter_active = INDEX_FIRST;
$bookmark_filter_inactive = INDEX_SECOND;