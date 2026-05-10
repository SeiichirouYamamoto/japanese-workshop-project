/******************************************************
 *  intSelectedLanguage
 *  
 ******************************************************/
const arr_select_language = ['jpn','cht'];

const str_pathname = window.location.pathname;
const arr_pathname = str_pathname.split('/');

let intSelectedLanguage;
if(arr_select_language.indexOf(arr_pathname[INDEX_SECOND]) !== -1){
	intSelectedLanguage = arr_select_language.indexOf(arr_pathname[INDEX_SECOND]);
}
else{
	intSelectedLanguage = INDEX_FIRST;
}


/******************************************************
 *  
 *  
 ******************************************************/

const LEARNING_STATUS_NOT_STARTED = STATUS_FIRST;
const LEARNING_STATUS_LEARNING = STATUS_SECOND;
const LEARNING_STATUS_LEARNED = STATUS_THIRD;

const ACTIVE_RECALL_LEARNED = STATUS_FIRST;
const ACTIVE_RECALL_IN_PROGRESS = STATUS_SECOND;
const ACTIVE_RECALL_DAY_1 = STATUS_THIRD;
const ACTIVE_RECALL_DAY_3 = STATUS_FOURTH;
const ACTIVE_RECALL_WEEK_1 = STATUS_FIFTH;
const ACTIVE_RECALL_MONTH_1 = STATUS_SIXTH;
const ACTIVE_RECALL_MASTERED = STATUS_SEVENTH;

const QUIZ_TYPE_JAPANESE_PARTICLE = STATUS_FIRST;
const QUIZ_TYPE_WORD_INFLECTION = STATUS_SECOND;
const QUIZ_TYPE_GRAMMAR = STATUS_THIRD;
const QUIZ_TYPE_PLAIN_FORM = STATUS_FOURTH;
const QUIZ_TYPE_SORTING = STATUS_FIFTH;

const LEARNING_SCOPE_SELECT_ALL = SELECT_ALL;
const LEARNING_SCOPE_ALREADY_LEARNED = STATUS_FIRST;

const MATCHING_TYPE_PARTIAL = STATUS_FIRST;
const MATCHING_TYPE_PERFECT = STATUS_SECOND;

// デバッグ 未定義idを1として利用している
const DEFAULT_JAPANESE_ID = 1;
const DEFAULT_JAPANESE_ELEMENT_ID = 1;
const DEFAULT_SUB_CLASSIFICATION_ID = 1;
const DEFAULT_FORM_ID = 9;
const DEFAULT_LABEL_ID = 1;
const DEFAULT_VOICE_ID = 1;
const DEFAULT_JAPANESE_TEXT = '';
const DEFAULT_KANA_TEXT = '';
const DEFAULT_SUB_CLASSIFICATION_TEXT = '';
const DEFAULT_FORM_TEXT = '';
const DEFAULT_VOICE_TEXT = '';
const PERMIT_SENTENCE_END_LENGTH = 1;



const LINK_TYPE_NORMAL = STATUS_FIRST;
const LINK_TYPE_TO_PHRASE_CLAUSE = STATUS_SECOND;
const LINK_TYPE_PRIORITY_SEQUENCE = STATUS_THIRD;



const FORM_STYLE_PLAIN = CONJ_DICTIONARY_FORM;
const FORM_STYLE_POLITE = CONJ_POLITE_AFFIRMATIVE_NON_PAST;
const OUTPUT_STYLE_WORD_CONTAINER = STATUS_FIRST;
const OUTPUT_STYLE_TEXTAREA_CONTAINER = STATUS_SECOND;
const ORDER_STYLE_ASCENDING = STATUS_FIRST;
const KANA_VISIBLE = FLAG_TRUE;
const KANA_HIDDEN = FLAG_FALSE;


const GRAMMAR_INSIGHTS_ATTR_POST_JSON = STATUS_FIRST;
const GRAMMAR_INSIGHTS_ATTR_LINKS = STATUS_SECOND;
const GRAMMAR_INSIGHTS_ATTR_BUTTONS = STATUS_THIRD;

const GRAMMAR_INSIGHTS_DISPLAY_TITLES = STATUS_FIRST;
const GRAMMAR_INSIGHTS_DISPLAY_EXAMPLES = STATUS_SECOND;
const GRAMMAR_INSIGHTS_DISPLAY_USER_INPUT = STATUS_THIRD;
const GRAMMAR_INSIGHTS_DISPLAY_SENTENCES = STATUS_FOURTH;
const GRAMMAR_INSIGHTS_DISPLAY_RANDOM_SENTENCES = STATUS_FIFTH;
const GRAMMAR_INSIGHTS_DISPLAY_ACTIVE_RECALL = STATUS_SIXTH;
const GRAMMAR_INSIGHTS_DISPLAY_DOWNLOAD_ITEMS = STATUS_SEVENTH;
const GRAMMAR_INSIGHTS_DISPLAY_CREATE_QUIZ_LINKS = STATUS_EIGHTH;
const GRAMMAR_INSIGHTS_DISPLAY_UPSERT_HOMEWORK = STATUS_NINTH;
/******************************************************
 *  
 *  
 ******************************************************/
const WISE_PANEL_LAYOUT = {
    NONE: 'none',
    SINGLE: 'single',
    SPLIT_LEFT_RIGHT: 'split-left-right',
    SPLIT_TOP_BOTTOM: 'split-top-bottom'
};

const WISE_TOOLBAR_PANEL_MAP = {
    whiteboard: 'wisePanelWhiteboard',
    grammarExplanation: 'wisePanelGrammarExplanation',
    memoPad: 'wisePanelMemoPad',
    lessonContents: 'wisePanelLessonContents',
    grammarInsights: 'wisePanelGrammarInsights',
    wiseSetup: 'wisePanelWiseSetup'
};

const WISE_PANEL_POSITION = {
    FULL: 'full',
    TOP: 'top',
    BOTTOM: 'bottom',
    LEFT: 'left',
    RIGHT: 'right'
};

const PAGES_INCLUDING_GRAMMAR = [
	pageRegisterSentenceUrl,
	pageEditRegisteredSentenceUrl,
	pageManageRoomLessonContentsUrl,
];

const INNER_CONTAINER_NAMES = ['wordInnerContainer', 'stickyNoteInnerContainer'];

const TEMPORARY_MOVABLE_CONTAINER_CLASSES = [
	'deleteTarget',
	'linkView',
	'linkContainer-displayed',
	'linkContainer-displayed-toSentence',
	'linkContainer-selected',
	'selectedMovableContainer',
	'selectedMenuContainer'
];

const GRAMMAR_FONT_SIZE_TARGETS = [
	'inputChoices',
	'inputChoicesWhite',
	'divGrammarOutlineButtonInput',
	'divGrammarOutlineText',
	'divGrammarOutlineInputLabel',
	'sampleSentenceListTextDiv',
	'grammarViewActionButton',
	'grammarViewWordListLi',
	'grammarViewDetailsSummarys',
	'grammarListDetailsSummarys',
	'grammarOutlineDetailsSummarys',
	'homeworkDetailsSummarys',
	'commonTextContent',
	'summarysContents',
	'originalTagChangers',
	'clickableInnerContainer',
	'sentenceLayerMenuLi',
	'homeworkLiText'
]

const SHARED_CONTENTS_UI_VIEW = {
    ADD: 'add',
    CATEGORY: 'category',
    BOOKMARK: 'bookmark',
    HISTORY: 'history',
    SELECTED: 'selected',
    NONE: null
};

const WISE_CORE_PANEL = {
    SETUP: 'wiseSetup',
    GRAMMAR_INSIGHTS: 'grammarInsights',
    GRAMMAR_EXPLANATION: 'grammarExplanation',
    LESSON_CONTENTS: 'lessonContents',
    NONE: null
};

const MOVABLE_MODE = {
    SELECT: 'select',
    MOVE: 'move',
    EDIT: 'edit',
    PENDING: 'pending'
};