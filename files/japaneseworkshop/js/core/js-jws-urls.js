/******************************************************
 *  HOME
 *  
 ******************************************************/
const mainHomeUrl = JWS_URLS.mainHomeUrl; // https://site.com/
const currentHomeUrl = JWS_URLS.currentHomeUrl; // https://site.com/lang/
const networkHomeUrl = JWS_URLS.networkHomeUrl; // https://site.com/
const themeUrl = JWS_URLS.themeUrl; // https://site.com/themes/



const apiUrl = themeUrl + 'api/'
/******************************************************
 *  WISE CORE
 *  
 ******************************************************/
const wiseCoreUrl = apiUrl + 'wise-core/'
const wiseCoreBuildFormListHtmlUrl = wiseCoreUrl + 'wise_core_build_form_list_html.php';
const wiseCoreBuildGrammarViewHtmlUrl = wiseCoreUrl + 'wise_core_build_grammar_view_html.php';
const wiseCoreGetDataGrammarViewUrl = wiseCoreUrl + 'wise_core_get_data_grammar_view.php';
const wiseCoreGetDataGrammarExplanationUrl = wiseCoreUrl + 'wise_core_get_data_grammar_explanation.php';
const wiseCoreGetDataInflectionExplanationUrl = wiseCoreUrl + 'wise_core_get_data_inflection_explanation.php';
const wiseCoreGenerateWordContainerDataUrl = wiseCoreUrl + 'wise_core_generate_word_container_data.php';
const wiseCoreGenerateLabelDataUrl = wiseCoreUrl + 'wise_core_generate_label_data.php';
const wiseCoreRegisterBookmarkUrl = wiseCoreUrl + 'wise_core_register_bookmark.php';
const wiseCoreRegisterNewWordUrl = wiseCoreUrl + 'wise_core_register_new_word.php';
const wiseCoreRegisterSessionTimeUrl = wiseCoreUrl + 'wise_core_register_session_time.php';
const wiseCoreRegisterTextareaValuesUrl = wiseCoreUrl + 'wise_core_register_textarea_values.php';
const wiseCoreSearchKnowledgeUrl = wiseCoreUrl + 'wise_core_search_knowledge.php';
const wiseCoreSearchKnowledgeByBookmarkUrl = wiseCoreUrl + 'wise_core_search_knowledge_by_bookmark.php';
const wiseCoreSearchWordUrl = wiseCoreUrl + 'wise_core_search_word.php';

const wiseCoreGetLessonDatesUrl = wiseCoreUrl + 'wise_core_get_lesson_dates.php';
const wiseCoreCreateLessonDateUrl = wiseCoreUrl + 'wise_core_create_lesson_date.php';

const wiseCoreGetWhiteboardsUrl = wiseCoreUrl + 'wise_core_get_whiteboards.php';
const wiseCoreCreateWhiteboardUrl = wiseCoreUrl + 'wise_core_create_whiteboard.php';
const wiseCoreGetWhiteboardStateUrl = wiseCoreUrl + 'wise_core_get_whiteboard_state.php';
const wiseCoreSaveWhiteboardStateUrl = wiseCoreUrl + 'wise_core_save_whiteboard_state.php';

const wiseCoreGetOrCreateMemoUrl = wiseCoreUrl + 'wise_core_get_or_create_memo.php';
const wiseCoreUpdateMemoUrl = wiseCoreUrl + 'wise_core_update_memo.php';



/******************************************************
 *  REGISTERED SENTENCES
 *  
 ******************************************************/
const sentenceUrl = apiUrl + 'sentence/'
const sentenceRegisterSentenceUrl = sentenceUrl + 'sentence_register_sentence.php';
const sentenceUpdateRegisteredSentenceUrl = sentenceUrl + 'sentence_update_registered_sentence.php';
const sentenceUpdateSentencePublishedStatusUrl = sentenceUrl + 'sentence_update_sentence_published_status.php';
const sentenceGetRegisteredByGrammarUrl = sentenceUrl + 'sentence_get_registered_by_grammar.php';
const sentenceGetElementsByRegisteredSentenceUrl = sentenceUrl + 'sentence_get_elements_by_registered_sentence.php';
const sentenceGetTranslationsByGrammar = sentenceUrl + 'sentence_get_translations_by_grammar.php';
const sentenceResortRegisteredSentenceUrl = sentenceUrl + 'sentence_resort_registered_sentences.php';



/******************************************************
 *  ROOM
 *  
 ******************************************************/
const roomUrl = apiUrl + 'room/'
const roomDisplayLessonContentsUrl = roomUrl + 'room_display_lesson_contents.php';
const roomUpsertHomeworkUrl = roomUrl + 'room_upsert_homework.php';
const roomCreateNewContentsUrl = roomUrl + 'room_create_new_contents.php';
const roomDeleteContentsUrl = roomUrl + 'room_delete_contents.php';
const roomGetGrammarInsightsUrl = roomUrl + 'room_get_grammar_insights.php';
const roomGetGrammarInsightsHomeworkItemsUrl = roomUrl + 'room_get_grammar_insights_homework_items.php';
const roomGetLessonContentsUrl = roomUrl + 'room_get_lesson_contents.php';
const roomGetLessonRangeUrl = roomUrl + 'room_get_lesson_range.php';
const roomGetLessonSelectOptionsUrl = roomUrl + 'room_get_lesson_select_options.php';
const roomGetTeachingMaterialsUrl = roomUrl + 'room_get_teaching_materials.php';
const roomMulticopyLessonsFromTeachingMaterialsUrl = roomUrl + 'room_multicopy_lessons_from_teaching_materials.php';
const roomResortContentsUrl = roomUrl + 'room_resort_contents.php';
const roomUpdateLessonLearningStatusUrl = roomUrl + 'room_update_lesson_learning_status.php';
const roomUploadUserInputDataUrl = roomUrl + 'room_upload_user_input_data.php';
const roomUpdateUserInputDataUrl = roomUrl + 'room_update_user_input_data.php';



/******************************************************
 *  LAYER
 *  
 ******************************************************/
const sentenceLayerUrl = apiUrl + 'sentence-layer/'
const sentenceLayerCreateNewLayerUrl = sentenceLayerUrl + 'sentence_layer_create_new_layer.php';
const sentenceLayerCreateNewOverrideUrl = sentenceLayerUrl + 'sentence_layer_create_new_override.php';
const sentenceLayerDeleteLayerUrl = sentenceLayerUrl + 'sentence_layer_delete_layer.php';
const sentenceLayerDeleteOverrideUrl = sentenceLayerUrl + 'sentence_layer_delete_override.php';
const sentenceLayerDisplayElementsInformationUrl = sentenceLayerUrl + 'sentence_layer_display_elements_infomation.php';
const sentenceLayerDisplayOverridesInformationUrl = sentenceLayerUrl + 'sentence_layer_display_overrides_infomation.php';
const sentenceLayerEnsureHasOverrideUrl = sentenceLayerUrl + 'sentence_layer_ensure_has_override.php';
const sentenceLayerGetElementsUrl = sentenceLayerUrl + 'sentence_layer_get_elements.php';
const sentenceLayerGetGrammarTitleUrl = sentenceLayerUrl + 'sentence_layer_get_grammar_title.php';
const sentenceLayerGetLayerInformationUrl = sentenceLayerUrl + 'sentence_layer_get_layer_information.php';
const sentenceLayerGetLayersByRegisteredSentenceUrl = sentenceLayerUrl + 'sentence_layer_get_layers_by_registered_sentence.php';
const sentenceLayerGetRegisteredGrammarsUrl = sentenceLayerUrl + 'sentence_layer_get_registered_grammars.php';
const sentenceLayerResortLayersByRegisteredSentenceUrl = sentenceLayerUrl + 'sentence_layer_resort_layers_by_registered_sentence.php';
const sentenceLayerUpdateLayerElementPropertyUrl = sentenceLayerUrl + 'sentence_layer_update_layer_element_property.php';
const sentenceLayerUpdateLayerElementsUrl = sentenceLayerUrl + 'sentence_layer_update_layer_elements.php';
const sentenceLayerUpdateLayerNameUrl = sentenceLayerUrl + 'sentence_layer_update_layer_name.php';
const sentenceLayerUpdateLayerPropertyUrl = sentenceLayerUrl + 'sentence_layer_update_layer_property.php';
const sentenceLayerUpdateOverrideUrl = sentenceLayerUrl + 'sentence_layer_update_override.php';




/******************************************************
 *  QUIZ
 *  
******************************************************/
const quizUrl = apiUrl + 'quiz/'
const quizGetFormListUrl = quizUrl + 'quiz_get_form_list.php';
const quizGetLayerInformationUrl = quizUrl + 'quiz_get_layer_information.php';
const quizGetQuizContentsUrl = quizUrl + 'quiz_get_quiz_contents.php';
const quizSaveQuizSettingsUrl = quizUrl + 'quiz_save_quiz_settings.php';


/******************************************************
 *  WISE MAP
 *  
******************************************************/
const wiseMapUrl = apiUrl + 'wise-map/'
const wiseMapRenderUiFromIdsUrl = wiseMapUrl + 'wise_map_render_ui_from_ids.php';
const wiseMapRenderUiFromUniqueCodeUrl = wiseMapUrl + 'wise_map_render_ui_from_unique_code.php';
const wiseMapRenderContentFocusPointMapUrl = wiseMapUrl + 'wise_map_render_content_focus_point_map.php';



/******************************************************
 *  WISE NAVI
 *  
******************************************************/
const wiseNaviUrl = apiUrl + 'wise-navi/'
const wiseNaviRenderGoalUrl = wiseNaviUrl + 'wise_navi_render_goal.php';
const wiseNaviGetItemsUrl = wiseNaviUrl + 'wise_navi_get_items.php';
const wiseNaviGetMessagesUrl = wiseNaviUrl + 'wise_navi_get_messages.php';
const wiseNaviGetScriptsLengthUrl = wiseNaviUrl + 'wise_navi_get_scripts_length.php';
const wiseNaviGetSummaryItemsUrl = wiseNaviUrl + 'wise_navi_get_summary_items.php';
const wiseNaviRenderContentUrl = wiseNaviUrl + 'wise_navi_render_content.php';
const wiseNaviCreateNewContentsUrl = wiseNaviUrl + 'wise_navi_create_new_contents.php';
const wiseNaviDeleteContentsUrl = wiseNaviUrl + 'wise_navi_delete_contents.php';
const wiseNaviResortContentsUrl = wiseNaviUrl + 'wise_navi_resort_contents.php';



/******************************************************
 *  WISE MANAGE
 *  
******************************************************/
const wiseManageUrl = apiUrl + 'wise-manage/'
const wiseManageBulkUpdateRecords = wiseManageUrl + 'wise_manage_bulk_update_records.php';



/******************************************************
 *  DASHBOARD
 *  
******************************************************/
const dashboardUrl = apiUrl + 'dashboard/'
const dashboardSetCurrentRoomUrl = dashboardUrl + 'dashboard_set_current_room.php';
const dashboardRegisterNewLessonUrl = dashboardUrl + 'dashboard_register_new_lesson.php';
const dashboardGetGrammarListUrl = dashboardUrl + 'dashboard_get_grammar_list.php';
const dashboardGetHomeworkUrl = dashboardUrl + 'dashboard_get_homework.php';
const dashboardGetLessonMemoUrl = dashboardUrl + 'dashboard_get_lesson_memo.php';
const dashboardCheckLessonMemoUpdatedUrl = dashboardUrl + 'dashboard_check_lesson_memo_updated.php';



/******************************************************
 *  PAGE
 *  
 ******************************************************/
const pageWiseUrl = currentHomeUrl + 'wise';
const pageRegisterSentenceUrl = currentHomeUrl + 'wise/register-sentence';
const pageEditRegisteredSentenceUrl = currentHomeUrl + 'wise/edit-registered-sentence';
const pageCreateLayersUrl = currentHomeUrl + 'wise/create-layers';
const pageManageRoomsUrl = currentHomeUrl + 'teaching-rooms/manage-rooms';
const pageManageRoomLessonsUrl = currentHomeUrl + 'teaching-rooms/manage-rooms/manage-room-lessons';
const pageManageRoomLessonContentsUrl = currentHomeUrl + 'teaching-rooms/manage-rooms/manage-room-lesson-contents';
const pageJapaneseParticleQuizUrl = currentHomeUrl + 'dashboard/study/quizzes/japanese-particle-quiz';
const pageWordInflectionQuizUrl = currentHomeUrl + 'dashboard/study/quizzes/word-inflection-quiz';
const pageGrammarQuizUrl = currentHomeUrl + 'dashboard/study/quizzes/grammar-quiz';
const pagePlainformQuizUrl = currentHomeUrl + 'dashboard/study/quizzes/plainform-quiz';
const pageSortingQuizUrl = currentHomeUrl + 'dashboard/study/quizzes/sorting-quiz';
const pageSortingQuizFullscreenUrl = currentHomeUrl + 'dashboard/study/quizzes/sorting-quiz-fullscreen';
const pageGrammarViewForTeachersUrl = currentHomeUrl + 'wise/grammar-dashboard/grammar-view-for-teachers';
const pageGrammarPointUrl = currentHomeUrl + 'dashboard/study/grammar-point';
const pageDashboardWorkshopUrl = currentHomeUrl + 'dashboard/study/workshop';
const pageDashboardWorkshopLessonsUrl = currentHomeUrl + 'dashboard/study/workshop/lessons';

