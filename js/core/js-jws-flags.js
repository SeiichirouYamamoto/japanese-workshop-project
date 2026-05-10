
let isMoving = false;
let isDrawing = false;
let isTouched = false;
let isOpened_contextMenu = false;
let doHide = true;
let isTextareaInputing;
let isDrawingLaser = false;
let isEditingLayer = false;
let isCreatingLayer = false;

let isMoveFramePending = false;
let isDrawingLaserOnGlobalCanvas = false;

let isCompositionInProgress_wisePanelWhiteboardUiSearchInput = false;
let isAllowedListScroll = false;


let isStarted_Handwriting = false;
let isStarted_Handwriting_withShiftKey = false;
let isStarted_Handwriting_withCtrlKey = false;
let doingHandwriting = false;

let isCtrlPressed = false;
let isAltPressed = false;
let isShiftPressed = false;
let isShortcutActive = false;


let isActioned = false;
let isFirstInterval = true;
let isActiveTab = true;

let isResizing = false;

let isWiseScanRunning = false;

let isStartedEditing = false;

let isComposing = false;

let isHorizontalLayout = true;

let isWhiteboardDirty = false;
let lastWhiteboardSentAt = 0;

let resizeRelayoutTimer = null;
let shouldBlurAfterRelayout = false;


let isAddingGrammarExplanation = false;