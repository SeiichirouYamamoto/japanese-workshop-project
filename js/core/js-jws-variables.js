


/******************************************************
 *  State
 *  
 ******************************************************/

let appearanceLayoutState = {
    row: 0,
    column: 0,
    nextX: 0,
    nextY: 0,
    currentColumnMaxWidth: 0
};

let moveModifierState = {
    ctrl: false,
    meta: false,
    shift: false
};

let pointerLocalCache = {
    active: false,
    rectLeft: 0,
    rectTop: 0,
    zoomScale: 1
};


/******************************************************
 *  VALUE
 *  
 ******************************************************/
let pressPointX;
let pressPointY;
let moveStartPointX;
let moveStartPointY;
let lastMovePointX;
let lastMovePointY;
let grabOffsetX = 0;
let grabOffsetY = 0;
let cancelPullDownRefreshStartY = 0;


let dragStartScrollLeft = 0;
let dragStartScrollTop = 0;

let lastTouchTime = 0;

let wiseCanvasOriginalContext;
let laserLastPosition = { x: null, y: null };

let wiseCanvasHandWritingContext;
let handWritingStartPosition = { x: null, y: null };
let handWritingLastPosition = { x: null, y: null };

let movableContainerBoundsFromBody;

let drawLineStartKey;
let linkMarkerSide;


let maxZIndex = 1000;
let maxUniqueKey = 0;
let maxCanvasId = 0;


let wiseMovableContainerFontSize = FONT_SIZE_DEFAULT;
let grammarExplanationFontSize = FONT_SIZE_DEFAULT;


let linkContainerWidth = 21;


let currentEraserSize = 70;

let isPenColorShortcutEnabled = FLAG_FALSE;

let appearanceAreaGapWidth = 0, appearanceAreaGapHeight = 0;


let balanceMarginLeft = BALANCE_X_MARGIN_LEFT_DEFAULT;
let balanceMarginTop = 20;


let linkLineEraserPointX = 0;
let linkLineEraserPointY = 0;
let touchedLinkLines = [];


let correctAnswers = [];
let correctAnswersFurigana = [];
let correctAnswerWordList = [];

let sentenceElementSortOrder = SORT_FIRST;



let wordSpanCreateCount = COUNT_FIRST;

let touchStartTimestamp = 0,
touchEndTimestamp = 0;

let wisePanelLessonContentsUiSearchGrammarListLiClickCount = COUNT_FIRST;
let wisePanelLessonContentsUiSearchGrammarListLiLastUniqueCode = null;
let wisePanelLessonContentsUiSearchGrammarLastTargetElement = null;


/******************************************************
 *  ELM
 *  
 ******************************************************/
let changingSideMenuElement = null;

let wisePanelWhiteboardUiSelectableList;
let selectedItemIndex = -1;


let movingMovableContainer;
let linkedMovableContainer;
let movableContainerLinkMarker;

let dragOriginPreviewClones = [];
let dragCurrentPreviewClones = [];
let currentDropPanel = 'whiteboard';


let contextMenuTargetContainer;
let contextMenuTargetBaseContainer;
let contextMenuTargetInnerContainer;
let contextMenuTargetJapaneseId;
let contextMenuTargetJapaneseElementId;
let contextMenuTargetSubClassificationId;
let contextMenuTargetFormId;
let contextMenuTargetLabelId;
let contextMenuTargetVoiceId;
let contextMenuTargetSubClassification;
let contextMenuTargetContainerIdName;

/******************************************************
 *  HISTORY
 *  
 ******************************************************/
let drawLaserHistory = [];

let currentSelectedMovableContainers = [];

let operationHistory = [];
let currentHistoryIndex = -1;

let strokeHistory = [];          // 確定したストローク（永続化対象）
let currentStroke = null;        // 描画中の1ストローク
let strokeUndone = [];           // undo/redo用（任意）


let grammarExplanationHistory = [];
let currentGrammarExplanationIndex = -1;

let sharedContentsSelectionItems = [];
let chatHistory = [];
let quizHistory = [];

let wordListHistory =[];
let politePlainFormHistory ={};

let currentWiseToolbarButton = 'wiseVerticalToolbarSelectorButton';
let currentInteractionMode = 'selectMode';
let currentHandWritingColor = 'black';
let currentGlobalToolbarButton = 'globalVerticalToolbarSelectorButton';

let lastSessionStartTime = null;

let toolbarLongPressTimer;

let recordedTextareaValues = [];

let actionCount = COUNT_FIRST;

let eraserSizeTimeoutID;

let inflectionsCheckboxIndex = INFLECTIONS_CHECKBOX_INDEX_DEFAULT;

let globalTargetSection = null;
let globalTargetBody = null;
let globalTargetContent = null;

let contextMenuLeft = 0;
let contextMenuRight = 0;
let contextMenuTop = 0;
let contextMenuBottom = 0;

let textAreaContainerMaxWidthDefault;

let textAreaContainerMaxWidthCurrent = textAreaContainerMaxWidthDefault;


const clickHandlers = {};
const changeHandlers = {};

let prevIndex = null;


const zoomScaleDefault = 1;
const zoomScaleMax = 3;
const zoomScaleMin = 0.2;

const whiteboardState = {
    zoomScale: zoomScaleDefault
};

let grammarInsightsDisplaySnapshot = null;
/******************************************************
 *  DOM
 *  
 ******************************************************/
const globalToolbarContainer = document.getElementById('globalVerticalToolbarContainer');
const globalToolbarSelectorButton = document.getElementById('globalVerticalToolbarSelectorButton');
const globalToolbarLaserButton = document.getElementById('globalVerticalToolbarLaserButton');
const globalToolbarOpenWiseButton = document.getElementById('globalVerticalToolbarOpenWiseButton');
const globalToolbarManageRoomsButton = document.getElementById('globalVerticalToolbarManageRoomsButton');
const globalCanvas = document.getElementById('globalCanvas');


const wiseBaseLayerSuperOverlay = document.getElementById('wiseBaseLayerSuperOverlay');
const wiseSuperOverlayBundle = document.getElementById('wiseSuperOverlayBundle');

const sectionWise = document.getElementById('sectionWise');
const whiteboardPanel = document.getElementById('wisePanelWhiteboard');
const wisePanelWhiteboardBody = document.getElementById('wisePanelWhiteboardBody');
const wisePanelWhiteboardViewMainContentArea = document.getElementById('wisePanelWhiteboardViewMainContentArea');
const wiseBoardMarksLayer = document.getElementById('wiseBoardMarksLayer');
const wiseContextMenu = document.getElementById('wiseContextMenu');
const wiseZoomStage = document.getElementById('wiseZoomStage');



const wiseCanvasContainer = document.getElementById('wiseCanvasContainer');
const canvasLinkedContainer = document.getElementById('canvasLinkedContainer');
const wiseCanvasOriginal = document.getElementById('wiseCanvasOriginal');
const wiseCanvasHandWriting = document.getElementById('wiseCanvasHandWriting');


const createLayersBody = document.getElementById('wiseBodyCreateLayers');
const createLayersContent = document.getElementById('wiseContentCreateLayers');
const createLayersDisplayArea = document.getElementById('wiseContainersMainContentAreaCreateLayers');
const sentenceLayerUpdateScreen = document.getElementById('sentenceLayerUpdateScreen');
const sectionCreateLayers = document.getElementById('sectionWiseCreateLayers');


const quizBody = document.getElementById('wiseBodyQuiz');
const quizFeedbackScreen = document.getElementById('quizFeedbackScreen');
const sortingQuizFullScreenSection = document.getElementById('sectionSortingQuizFullScreen');

const grammarExplanationPanel = document.getElementById('wisePanelGrammarExplanation');
const grammarExplanationPanelView = document.getElementById('wisePanelGrammarExplanationView');
const grammarExplanationUiHistory = document.getElementById('wisePanelGrammarExplanationUiHistory');
const grammarExplanationCloseButton = document.getElementById('wisePanelGrammarExplanationViewCloseButton');
const grammarExplanationHandle = document.getElementById('wisePanelGrammarExplanationViewHandle');
const linkToGrammarView = document.getElementById('linkToGrammarView');


const panelOverlaySharedContentsUiSearchButton = document.getElementById('panelOverlaySharedContentsUiSearchButton');
const panelOverlaySharedContentsUiSelectedContentsCloseButton = document.getElementById('panelOverlaySharedContentsUiSelectedContentsCloseButton');
const panelOverlaySharedContentsUi = document.getElementById('panelOverlaySharedContentsUi');
const panelOverlaySharedContentsUiShowInsightsButton = document.getElementById('panelOverlaySharedContentsUiSelectedContentsShowInsightsButton');
const panelOverlaySharedContentsUiChangeModalButtonBookmark = document.getElementById('panelOverlaySharedContentsUiChangeModalButtonBookmark');


const wisePanelGrammarInsightsView = document.getElementById('wisePanelGrammarInsightsView');
const grammarInsightsDropdownMenuArea = document.getElementById('wisePanelGrammarInsightsViewDropDownMenuArea');
const grammarInsightsShowExplanationButton = document.getElementById('wisePanelGrammarInsightsViewButtonsContainerShowExplanationButton');


const lessonContentsPanel = document.getElementById('wisePanelLessonContents');
const lessonContentsPanelView = document.getElementById('wisePanelLessonContentsView');
const lessonContentsHandle = document.getElementById('wisePanelLessonContentsViewHandle');
const lessonContentsResetButton = document.getElementById('lessonContentsResetButton');
const lessonContentsGoToRoomManageButton = document.getElementById('lessonContentsGoToRoomManageButton');
const lessonContentsUiSearchGrammar = document.getElementById('wisePanelLessonContentsUiSearchGrammar');
const lessonContentsUiSearchGrammarInput = document.getElementById('wisePanelLessonContentsUiSearchGrammarSearchInput');
const lessonContentsUiSearchGrammarLoading = document.getElementById('wisePanelLessonContentsUiSearchGrammarLoading');


const manageLessonContentsBody = document.getElementById('manageLessonContentsBody');
const manageSectionUpdateAllButton = document.getElementById('manageSectionUpdateAllButton');




const wiseMapButtonsContainer = document.getElementById('wiseMapScreenButtonsContainer');
const wiseMapFocusMapArea = document.getElementById('wiseMapScreenMapDisplayAreaFocus');
const wiseMapFocusNodeArea = document.getElementById('wiseMapScreenNodeDisplayAreaFocus');
const wiseMapFocusScreen = document.getElementById('wiseMapFocusPointScreen');
const wiseMapLessonGoalScreen = document.getElementById('wiseMapLessonGoalScreen');
const wiseMapLessonStepGoalScreen = document.getElementById('wiseMapLessonStepGoalScreen');
const wiseMapOverlay = document.getElementById('wiseMapScreenOverlay');


const naviItemsBody = document.getElementById('naviItemsBody');
const wiseMapMessageScreen = document.getElementById('wiseMapMessageScreen');
const wiseNaviEventOverlay = document.getElementById('wiseNaviEventOverlay');
const wiseNaviNextLabel = document.getElementById('wiseNaviNextLabel');
const wiseOpeningOverlay = document.getElementById('wiseOpeningOverlay');
const wiseOpeningOverlayLogo = document.getElementById('wiseOpeningOverlayLogo');
const wiseScanline = document.getElementById('wiseScanline');
const wiseScanOverlay = document.getElementById('wiseScanOverlay');
const wiseWaitOverlay = document.getElementById('wiseWaitOverlay');
const wiseWaitOverlayLogo = document.getElementById('wiseWaitOverlayLogo');


const memoPadOpenButton = document.getElementById('wisePanelMemoPadViewOpener');

const memoPadCloseButton = document.getElementById('wisePanelMemoPadViewCloseButton');
const memoPadPanel = document.getElementById('wisePanelMemoPadView');




const wiseMenuAddElementStickyNoteButton = document.getElementById('wiseMenuBarAddElementStickyNote');
const wiseMenuAddElementTextBoxButton = document.getElementById('wiseMenuBarAddElementTextBox');
const wiseMenuAddElementWordButton = document.getElementById('wiseMenuBarAddElementWord');
const wiseMenuErasersElementsEraserButton = document.getElementById('wiseMenuBarErasersElementsEraser');
const wiseMenuErasersHandWritingResetButton = document.getElementById('wiseMenuBarErasersHandWritingReset');
const wiseToolbarContainer = document.getElementById('wiseVerticalToolbarContainer');
const wiseRightToolbarContainer = document.getElementById('wiseRightVerticalToolbarContainer');
const wiseToolbarCreateLinkButton = document.getElementById('wiseVerticalToolbarCreateLinkButton');
const wiseToolbarLaserButton = document.getElementById('wiseVerticalToolbarLaserButton');
const wiseToolbarSelectorButton = document.getElementById('wiseVerticalToolbarSelectorButton');









const wiseBannerAdContainer = document.getElementById('wiseBannerAdvertisementContainer');



