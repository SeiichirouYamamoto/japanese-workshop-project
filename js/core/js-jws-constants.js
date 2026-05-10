const FLAG_FALSE = 0;
const FLAG_TRUE = 1;

const INDEX_FIRST = 0;
const INDEX_SECOND = 1;
const INDEX_THIRD = 2;
const INDEX_FOURTH = 3;
const INDEX_FIFTH = 4;
const INDEX_SIXTH = 5;
const INDEX_SEVENTH = 6;
const INDEX_EIGHTH = 7;
const INDEX_NINTH = 8;
const INDEX_TENTH = 9;

const STATUS_FIRST = 0;
const STATUS_SECOND = 1;
const STATUS_THIRD = 2;
const STATUS_FOURTH = 3;
const STATUS_FIFTH = 4;
const STATUS_SIXTH = 5;
const STATUS_SEVENTH = 6;
const STATUS_EIGHTH = 7;
const STATUS_NINTH = 8;
const STATUS_TENTH = 9;

const PRIORITY_FIRST = 0;
const COUNT_FIRST = 0;
const SORT_FIRST = 0;

const COUNT_EMPTY = 0;
const LENGTH_EMPTY = 0;
const LAST_INDEX_OFFSET = 1;

const SELECT_ALL = -1;
const USE_DEFAULT = -2;
const USE_CUSTOM = -2;

const FONT_SIZE_DEFAULT = 24;
// const FONT_SIZE_DEFAULT_MOBILE = 20;

const FONT_SIZE_MAX = 72;
const FONT_SIZE_MIN = 10;


const VERTICAL_TOOLBAR_BUTTON_SIZE = 50;

const BALANCE_X_MARGIN_LEFT_DEFAULT = 20;

const ANIMATION_SPEED = 0.75;

const CLICK_THRESHOLD = 300;

const MAX_HISTORY_COUNT = 256;

const INFLECTIONS_CHECKBOX_INDEX_DEFAULT = INDEX_FIRST;


const EXPORT_TYPE_HTML = STATUS_FIRST;
const EXPORT_TYPE_CSV = STATUS_SECOND;
const EXPORT_TYPE_PDF = STATUS_THIRD;


const SENTENCE_END = -1;
const PHRASE_CLAUSE_ID_NONE = -1;
const STRING_NONE = 'nothing';
const INT_NONE = -1;



const Z_INDEX_FRONT_BASELINE_MINUS_2 = 9988;
const Z_INDEX_FRONT_BASELINE_MINUS_1 = 9989;
const Z_INDEX_FRONT_BASELINE = 9990;
const Z_INDEX_FRONT_BASELINE_PLUS_1 = 9991;
const Z_INDEX_FRONT_BASELINE_PLUS_2 = 9992;
const Z_INDEX_FRONT_BASELINE_PLUS_3 = 9993;
const Z_INDEX_FRONT_BASELINE_PLUS_4 = 9994;
const Z_INDEX_FRONT_BASELINE_PLUS_5 = 9995;

const APPEARANCE_AREA_DEFAULT = 20;


const MINUTE_MS = 60_000;
const INACTIVE_THRESHOLD_MINUTES = 30;
const AUTO_WAIT_MODE = true;


const GRAMMAR_UNIQUE_CODE_DEFAULT = 'rhjRSwAvyrCZ';

const AVOID_NULL_PROXY_STRING = '---';

const KEY_UNIQUE_CODE = 'unique_code';
const KEY_GRAMMAR_UNIQUE_CODE = 'grammar_unique_code';
const KEY_SENTENCE_UNIQUE_CODE = 'sentence_unique_code';
const KEY_LABEL_UNIQUE_CODE = 'label_unique_code';


const TEXTAREA_CONTAINER_MIN_WIDTH = 50;
const CHART_TEXTAREA_MIN_WIDTH = 300;


const HIDE_DELAY_MS = 700;
const WAIT_DELAY_MS = 1000;
const POST_TYPEWRITER_PAUSE_MS = 1000;
const CLONE_CLASS = 'wise-scan-clone';


const CHART_TYPE_INFLECTIONS = STATUS_FIRST;
const CHART_TYPE_FREE = STATUS_SECOND;

const POST_JSON_TIMEOUT_MAX_RETRY = 3;

const SEARCH_SCOPE_WISE_CATEGORY = INDEX_FIRST;
const SEARCH_SCOPE_WISE_SUB_CATEGORY = INDEX_SECOND;
const SEARCH_SCOPE_LESSON_CONTENTS = INDEX_THIRD;
const SEARCH_SCOPE_MANAGE_LESSON_CONTENTS = INDEX_FOURTH;

const SEARCH_SCOPE_BOOKMARK_ROOM = INDEX_FIRST;
const SEARCH_SCOPE_BOOKMARK_INDIVIDUAL = INDEX_SECOND;

const MAX_BATCH_GRAMMAR_CREATE = 20; 

const WHITEBOARD_AUTOSAVE_MIN_INTERVAL_MS = 10000;

const BOARD_WIDTH  = 1600;
const BOARD_HEIGHT = 2400;

const MODE_LIST = [
	'lesson_contents_tree',
	'grammar_usages_tree',
	'grammar_knowledge_tree',
	'grammar_comparison_tree',
	'grammar_family_tree',
	'grammar_correspondence_tree',
	'grammar_alternative_tree'
];

const VISIBLE_OVERRIDE_KEYS = [
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


const MOVE_START_DISTANCE = 6;