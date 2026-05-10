

/******************************************************
 *  SETTINGS
 *
 ******************************************************/
const whiteboardUiSettings = document.getElementById('wisePanelWhiteboardUiSettings');

const whiteboardUiSettingsEraserSizeIncreaseItem = document.getElementById('wisePanelWhiteboardUiSettingsLiChangeEraserSizeBig');
const whiteboardUiSettingsEraserSizeDecreaseItem = document.getElementById('wisePanelWhiteboardUiSettingsLiChangeEraserSizeSmall');
const whiteboardUiSettingsResizeEventItem = document.getElementById('wisePanelWhiteboardUiSettingsLiResizeEvent');
const whiteboardUiSettingsWiseWaitModeToggle = document.getElementById('wisePanelWhiteboardUiSettingsLiWiseWaitMode');


if(whiteboardUiSettingsEraserSizeIncreaseItem !== null)
{whiteboardUiSettingsEraserSizeIncreaseItem.addEventListener('pointerup', function() {
	changeEraserSize(true);
}, false);}

if(whiteboardUiSettingsEraserSizeDecreaseItem !== null)
{whiteboardUiSettingsEraserSizeDecreaseItem.addEventListener('pointerup', function() {
	changeEraserSize(false);
}, false);}

if(whiteboardUiSettingsResizeEventItem !== null)
{whiteboardUiSettingsResizeEventItem.addEventListener('pointerup', function() {
	prepareLayoutOnResize();
	initWiseLayout(false);
}, false);}

if(whiteboardUiSettingsWiseWaitModeToggle !== null)
{whiteboardUiSettingsWiseWaitModeToggle.addEventListener('pointerup', function() {
	showWiseWaitOverlay();
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiSettings);
}, false);}
