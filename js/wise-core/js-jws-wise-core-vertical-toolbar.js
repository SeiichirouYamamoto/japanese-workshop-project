const wiseMenuAddElement = document.getElementById('wiseMenuBarAddElement');
const wiseMenuColorButtons = document.querySelectorAll('.wiseMenuBarButtonSelectColor');
const wiseMenuDrawingColors = document.getElementById('wiseMenuBarDrawingColors');
const wiseMenuErasers = document.getElementById('wiseMenuBarErasers');
const wiseMenuLinks = document.getElementById('wiseMenuBarLinks');
const wiseMenuLinksToSentenceButton = document.getElementById('wiseMenuBarLinksToSentence');
const wiseMenuTools = document.getElementById('wiseMenuBarTools');
const wiseMenuToolsCallAlreadyRegisteredButton = document.getElementById('wiseMenuBarToolsCallAlreadyRegistered');
const wiseMenuToolsCreateNewButton = document.getElementById('wiseMenuBarToolsCreateNew');
const wiseMenuToolsDownloadButton = document.getElementById('wiseMenuBarToolsDownloadWise');
const wiseMenuToolsHistoryListButton = document.getElementById('wiseMenuBarToolsHistorysList');
const wiseMenuToolsRegisterSentenceButton = document.getElementById('wiseMenuBarToolsRegisterSentence');
const wiseMenuToolsReRegisterSentenceButton = document.getElementById('wiseMenuBarToolsReRegisterSentence');
const wiseMenuToolsSettingsButton = document.getElementById('wiseMenuBarToolsSettingsMenu');
const wiseMenuToolsStartEditingButton = document.getElementById('wiseMenuBarToolsStartEditing');
const wiseToolbarAddElementMenuButton = document.getElementById('wiseVerticalToolbarMenuListOpenerAddElement');
const wiseToolbarDrawingButton = document.getElementById('wiseVerticalToolbarDrawingButton');
const wiseToolbarEraserButton = document.getElementById('wiseVerticalToolbarEraserButton');
const wiseToolbarToolsMenuButton = document.getElementById('wiseVerticalToolbarMenuListOpenerTools');

const grammarInsightsOpenButton = document.getElementById('wisePanelGrammarInsightsViewOpener');

const panelOverlaySharedContentsUiFromCategory = document.getElementById('panelOverlaySharedContentsUiFromCategory');
const panelOverlaySharedContentsUiCategorySelect = document.getElementById('panelOverlaySharedContentsUiFromCategorySelectId');
const panelOverlaySharedContentsUiCategorySelectAllButton = document.getElementById('panelOverlaySharedContentsUiFromCategorySelectAllButton');
const panelOverlaySharedContentsUiCategoryLoading = document.getElementById('panelOverlaySharedContentsUiLoadingAddContentsFromCategory');

const panelOverlaySharedContentsUiFromBookmark = document.getElementById('panelOverlaySharedContentsUiFromBookmark');
const panelOverlaySharedContentsUiBookmarkSelect = document.getElementById('panelOverlaySharedContentsUiFromBookmarkSelectId');
const panelOverlaySharedContentsUiBookmarkSelectAllButton = document.getElementById('panelOverlaySharedContentsUiFromBookmarkSelectAllButton');
const panelOverlaySharedContentsUiBookmarkLoading = document.getElementById('panelOverlaySharedContentsUiLoadingAddContentsFromBookmark');

const panelOverlaySharedContentsUiFromHistory = document.getElementById('panelOverlaySharedContentsUiFromHistory');
const panelOverlaySharedContentsUiHistoryList = document.getElementById('panelOverlaySharedContentsUiFromHistoryUl');
const panelOverlaySharedContentsUiHistorySelectAllButton = document.getElementById('panelOverlaySharedContentsUiFromHistorySelectAllButton');

const panelOverlaySharedContentsUiList = document.getElementById('panelOverlaySharedContentsUiUl');
const panelOverlaySharedContentsUiLoading = document.getElementById('panelOverlaySharedContentsUiLoadingAddContents');
const panelOverlaySharedContentsUiSearchInput = document.getElementById('panelOverlaySharedContentsUiSearchInput');
const sharedContentsUiOpenButton = document.getElementById('panelOverlaySharedContentsUiOpener');


if(sharedContentsUiOpenButton !== null)
{sharedContentsUiOpenButton.addEventListener('pointerup', function() {
	executeSharedContentsUiOpenButton();
	closeAllWiseExpandableToolbars();
}, false);}



if(wiseToolbarDrawingButton !== null)
{wiseToolbarDrawingButton.addEventListener('pointerup', function() {
	currentWiseToolbarButton = 'wiseVerticalToolbarDrawingButton';
	currentInteractionMode = 'selectMode';
	changeWiseVerticalToolbarButton('');
	openWiseMenuBar(wiseMenuDrawingColors);
}, false);}

if (wiseToolbarEraserButton !== null) {

    if (wiseMenuErasers !== null) {

        let isEraserLongPress = false;

        wiseToolbarEraserButton.addEventListener('mousedown', (e) => {
            startWiseEraserButtonPress(e);
        });

        wiseToolbarEraserButton.addEventListener('mouseup', (e) => {
            finishWiseEraserButtonPress(e);
        });

        wiseToolbarEraserButton.addEventListener('mouseout', (e) => {
            cancelWiseEraserButtonPress(e);
        });

        wiseToolbarEraserButton.addEventListener('touchstart', (e) => {
            startWiseEraserButtonPress(e);
        }, { passive: false });

        wiseToolbarEraserButton.addEventListener('touchend', (e) => {
            finishWiseEraserButtonPress(e);
        }, { passive: false });

        wiseToolbarEraserButton.addEventListener('touchcancel', (e) => {
            cancelWiseEraserButtonPress(e);
        }, { passive: false });

        function startWiseEraserButtonPress(e) {

            e.preventDefault();

            isEraserLongPress = false;

            currentWiseToolbarButton = 'wiseVerticalToolbarEraserButton';
            currentInteractionMode = 'eraserMode';
            changeWiseVerticalToolbarButton('wiseCanvasHandWriting');

            toolbarLongPressTimer = setTimeout(() => {

                isEraserLongPress = true;
				openWiseMenuBar(wiseMenuErasers);

            }, 500);
        }

        function finishWiseEraserButtonPress(e) {

            e.preventDefault();
            clearTimeout(toolbarLongPressTimer);

            if (isEraserLongPress) {
                return;
            }

            closeAllWiseExpandableToolbars();
        }

        function cancelWiseEraserButtonPress(e) {

            e.preventDefault();
            clearTimeout(toolbarLongPressTimer);

        }

    } else {

        wiseToolbarEraserButton.addEventListener('pointerup', function() {

            currentWiseToolbarButton = 'wiseVerticalToolbarEraserButton';
            currentInteractionMode = 'eraserMode';
            changeWiseVerticalToolbarButton('wiseCanvasHandWriting');
            closeAllWiseExpandableToolbars();

        }, false);

    }
}

if(wiseToolbarSelectorButton !== null)
{wiseToolbarSelectorButton.addEventListener('pointerup', function() {
	executeWiseToolbarSelectorButton();
	closeAllWiseExpandableToolbars();
}, false);}

if(wiseToolbarCreateLinkButton !== null)
{
	if(wiseMenuLinks !== null){

		wiseToolbarCreateLinkButton.addEventListener('mousedown', (e) => {
			openWiseMenuBarLinks(e);
		});
		wiseToolbarCreateLinkButton.addEventListener('mouseup', () => {
			clearTimeout(toolbarLongPressTimer);
		});
		wiseToolbarCreateLinkButton.addEventListener('mouseout', () => {
			clearTimeout(toolbarLongPressTimer);
		});

		wiseToolbarCreateLinkButton.addEventListener('touchstart', (e) => {
			openWiseMenuBarLinks(e);
		}, { passive: false });
		wiseToolbarCreateLinkButton.addEventListener('touchend', () => {
			clearTimeout(toolbarLongPressTimer);
		}, { passive: true });
		wiseToolbarCreateLinkButton.addEventListener('touchcancel', () => {
			clearTimeout(toolbarLongPressTimer);
		}, { passive: true });

	}
	else{
		wiseToolbarCreateLinkButton.addEventListener('pointerup', function() {
			currentWiseToolbarButton = 'wiseVerticalToolbarCreateLinkButton';
			currentInteractionMode = 'createLinkMode';
			changeWiseVerticalToolbarButton('');
		}, false);
	}
}

if(wiseToolbarAddElementMenuButton !== null)
{wiseToolbarAddElementMenuButton.addEventListener('pointerup', function() {
	currentWiseToolbarButton = 'wiseVerticalToolbarMenuListOpenerAddElement';
	currentInteractionMode = 'selectMode';
	changeWiseVerticalToolbarButton('');
	openWiseMenuBar(wiseMenuAddElement);
}, false);}

if(wiseToolbarToolsMenuButton !== null)
{wiseToolbarToolsMenuButton.addEventListener('pointerup', function() {
	currentWiseToolbarButton = 'wiseVerticalToolbarMenuListOpenerTools';
	currentInteractionMode = 'selectMode';
	changeWiseVerticalToolbarButton('');
	openWiseMenuBar(wiseMenuTools);
}, false);}

if(wiseToolbarLaserButton !== null)
{wiseToolbarLaserButton.addEventListener('pointerup', function() {
	executeWiseToolbarLaserButton();
	closeAllWiseExpandableToolbars();
}, false);}


if (wiseMenuToolsStartEditingButton !== null) {
wiseMenuToolsStartEditingButton.addEventListener('pointerup', async function () {

	if (isStartedEditing) return;

	isStartedEditing = true;
	wiseMenuToolsStartEditingButton.classList.add('hidden');
	wiseMenuToolsReRegisterSentenceButton.classList.remove('hidden');

	const urlObj = new URL(window.location.href);
	const params = urlObj.searchParams;
	const send_sentence_unique_code = escapeHTML(params.get(KEY_SENTENCE_UNIQUE_CODE) || '');

	try {
		const arr_wordContainers = await reviewWordContainers(send_sentence_unique_code, true);
		// 必要なら arr_wordContainers を使う
	} catch (error) {
		console.error('Error:', error.message || error);
		alert(error.message || 'Error');
	}

}, false);}

if(wiseMenuToolsReRegisterSentenceButton !== null)
{wiseMenuToolsReRegisterSentenceButton.addEventListener('pointerup', function() {
	let isConfirmed = window.confirm(MSG_REGISTER_CONFIRM[intSelectedLanguage]);
	if(isConfirmed) {
		updateRegisteredSentence();
	}
}, false);}

async function updateRegisteredSentence(){

	let arr_group_of_words = buildSentenceWordGroup();

	if(arr_group_of_words.length === LENGTH_EMPTY)return;

	let arr_link_id_add_sort = arr_group_of_words['arr_link_id_add_sort'];

	arr_link_id_add_sort.sort((a, b) => a.uniqueKey - b.uniqueKey);

	let arr_link_id_add_sort_uniqueKey;

	for(let i = INDEX_FIRST; i < arr_link_id_add_sort.length; i++){
		arr_link_id_add_sort_uniqueKey = arr_link_id_add_sort[i].uniqueKey;
		if(i !== arr_link_id_add_sort_uniqueKey){
			arr_link_id_add_sort[i].uniqueKey = i;
			arr_link_id_add_sort[i].idName = 'movableContainer'+i;
			for(let j = INDEX_FIRST; j < arr_link_id_add_sort.length; j++){
				if(arr_link_id_add_sort[j].linkId == arr_link_id_add_sort_uniqueKey){
					arr_link_id_add_sort[j].linkId = i;
				}
				if(arr_link_id_add_sort[j].phraseClauseId == arr_link_id_add_sort_uniqueKey){
					arr_link_id_add_sort[j].phraseClauseId = i;
				}
			}
		}
	}

	const url = new URL(window.location.href);
	const params = url.searchParams;
	const send_sentence_unique_code = escapeHTML(
		params.get(KEY_SENTENCE_UNIQUE_CODE)
	);
		
	try {

		const payload = {
			arr_link_id_add_sort: arr_link_id_add_sort,
			send_sentence_unique_code: send_sentence_unique_code,
			str_group_of_words_japanese: escapeHTML(arr_group_of_words['str_group_of_words_japanese']),
			str_group_of_words_kana: escapeHTML(arr_group_of_words['str_group_of_words_kana']),
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceUpdateRegisteredSentenceUrl,
			payload,
			10000
		);

		alert(escapeHTML(arr_group_of_words['str_group_of_words_japanese']));

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}



}

if (wiseMenuAddElementTextBoxButton) {

    // 先に発生する pointerdown を止める（重要）
    wiseMenuAddElementTextBoxButton.addEventListener('pointerdown', (e) => {
        e.preventDefault();
        e.stopPropagation();
    }, { passive: false });

    // いつもの処理は pointerup でOK
    wiseMenuAddElementTextBoxButton.addEventListener('pointerup', (e) => {
        e.preventDefault();
        e.stopPropagation();
        executeWiseMenuAddElementTextBoxButton();
		closeAllWiseExpandableToolbars();
    }, { passive: false });
}

if(wiseMenuAddElementStickyNoteButton !== null)
{wiseMenuAddElementStickyNoteButton.addEventListener('pointerup', function() {
	executeWiseMenuAddElementStickyNoteButton();
	closeAllWiseExpandableToolbars();
}, false);}

if(wiseMenuAddElementWordButton !== null)
{wiseMenuAddElementWordButton.addEventListener('pointerup', function() {
	executeWiseMenuAddElementWordButton();
	closeAllWiseExpandableToolbars();
}, false);}

if(wiseMenuToolsSettingsButton !== null)
{wiseMenuToolsSettingsButton.addEventListener('pointerup', function() {
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiSettings);
	closeAllWiseExpandableToolbars();
}, false);}

if(wiseMenuToolsDownloadButton !== null)
{wiseMenuToolsDownloadButton.addEventListener('pointerup', function() {
	resetToSelectMode();
	downloadWiseCanvasImage();
}, false);}


function downloadWiseCanvasImage(){

	let currentDate = new Date();
	const filenamePNG = 'wise' + formatDate(currentDate) + '.png';

	let elms_textarea = wisePanelWhiteboardViewMainContentArea.querySelectorAll('textarea');
	elms_textarea.forEach(elm => {
		let textareaComputedStyle = getComputedStyle(elm),
		textValue = escapeHTML(elm.value),
		fontSize = textareaComputedStyle.fontSize,
		width,
		height,
		left,
		top,
		zIndex,
		backgroundColor,
		parent = elm.parentElement;

		while (parent) {
			if (parent.classList.contains('baseContainer')) {
				const baseContainerComputedStyle = getComputedStyle(parent);
				backgroundColor = baseContainerComputedStyle.backgroundColor;
			}
			if (parent.classList.contains('movableContainer')) {
				const movableContainerComputedStyle = getComputedStyle(parent);
				if(textValue.length === LENGTH_EMPTY){
					width = movableContainerComputedStyle.width;
					height = movableContainerComputedStyle.height;
				}
				left = movableContainerComputedStyle.left;
				top = movableContainerComputedStyle.top;
				zIndex = movableContainerComputedStyle.zIndex;
				parent.classList.add('html2canvasHiddenTarget');
			}
			parent = parent.parentElement;
		}

		const createNew = document.createElement('div');
		createNew.classList.add('html2canvasCreateNew');
		createNew.style.position = 'absolute';
		createNew.style.fontSize = fontSize;
		// createNew.style.lineHeight = lineHeight;
		// createNew.style.overflow = 'hidden';
		createNew.style.marginTop = '0';
		createNew.style.paddingTop = '0';
		createNew.style.width = width;
		createNew.style.height = height;
		createNew.style.left = left;
		createNew.style.top = top;
		createNew.style.zIndex = zIndex;
		createNew.style.backgroundColor = backgroundColor;
		createNew.style.display = 'block';
		createNew.innerHTML = textValue.replace(/\n/g, '<br>');
		wisePanelWhiteboardViewMainContentArea.appendChild(createNew);
	});

	wiseBannerAdContainer.classList.add('html2canvasHiddenTarget');

	html2canvas(sectionWise,
	{
		scrollX: 0,
		scrollY: 0,
		scale: 2
	}
	).then(function(canvas) {
		var img = canvas.toDataURL('image/png');
		var link = document.createElement('a');
		link.href = img;
		link.download = filenamePNG;
		link.click();
	});

	let elms_hiddenTarget = document.querySelectorAll('.html2canvasHiddenTarget');
	elms_hiddenTarget.forEach(elm => {
		elm.classList.remove('html2canvasHiddenTarget');
	});

	let elms_deleteTarget = document.querySelectorAll('.html2canvasCreateNew');
	elms_deleteTarget.forEach(elm => {
		elm.remove();
	});
	return;
}


if (wiseMenuToolsCallAlreadyRegisteredButton !== null) {
wiseMenuToolsCallAlreadyRegisteredButton.addEventListener('pointerup', async function () {
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiRegisteredItems);

	await searchAlreadyRegisteredSentences();

}, false);}

if(wiseMenuToolsHistoryListButton !== null)
{wiseMenuToolsHistoryListButton.addEventListener('pointerup', function() {
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiHistory);
	closeAllWiseExpandableToolbars();
}, false);}

if(wiseMenuToolsCreateNewButton !== null)
{wiseMenuToolsCreateNewButton.addEventListener('pointerup', function() {
	let targetElm;
	for(let i = INDEX_FIRST; i < whiteboardUiCreateNewInputs.length; i++){
		targetElm = whiteboardUiCreateNewInputs[i];
		targetElm.style.borderColor = 'black';
	}
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiCreateNewWord);
	whiteboardUiCreateNewWordJapaneseInput.focus();
}, false);}

if(wiseMenuToolsRegisterSentenceButton !== null)
{wiseMenuToolsRegisterSentenceButton.addEventListener('pointerup', function() {
	let isConfirmed = window.confirm(MSG_REGISTER_CONFIRM[intSelectedLanguage]);
	if(isConfirmed) {
		registerSentence();
	}
}, false);}

if(wiseMenuLinksToSentenceButton !== null)
{wiseMenuLinksToSentenceButton.addEventListener('pointerup', function() {
	currentInteractionMode = 'createPriorityLinkMode';
	changeWiseVerticalToolbarButton('');
}, false);}



for (let i = INDEX_FIRST; i < wiseMenuColorButtons.length; i++) {

	wiseMenuColorButtons[i].addEventListener('pointerdown', (e) => {
        e.preventDefault();
        e.stopPropagation();
    }, { passive: false });

    wiseMenuColorButtons[i].addEventListener('pointerup', function (e) {
        const colorName = e.currentTarget.dataset.drawingColor;
        handleSelectDrawingColor(colorName);
		closeAllWiseExpandableToolbars();
    }, { passive: false });

}

function setHandWritingColorByName(colorName){
    switch (colorName) {
        case 'black':
        case 'red':
        case 'blue':
        case 'orange':
        case 'green':
            currentHandWritingColor = colorName;
            return true;

        default:
            return false;
    }
}

function enterHandWritingMode(){
    currentWiseToolbarButton = 'wiseVerticalToolbarDrawingButton';
    currentInteractionMode = 'drawingMode';
    changeWiseVerticalToolbarButton('wiseCanvasHandWriting');
}

function handleSelectDrawingColor(colorName){
    const isOk = setHandWritingColorByName(colorName);
    if (!isOk) {
        resetToSelectMode();
        return;
    }

    enterHandWritingMode();
}


if(wiseMenuErasersElementsEraserButton !== null)
{wiseMenuErasersElementsEraserButton.addEventListener('pointerup', function() {
	executeWiseMenuErasersElementsEraserButton();
	closeAllWiseExpandableToolbars();
}, false);}


function getWiseToolbarResizeInfo(toolbarContainer, targetHeight) {

    if (toolbarContainer === null) {
        return null;
    }

    const buttons = toolbarContainer.querySelectorAll('.wiseVerticalToolbarButton');

    if (buttons.length === LENGTH_EMPTY) {
        return {
            toolbarContainer: toolbarContainer,
            buttons: buttons,
            buttonCount: 0,
            recommendedButtonSize: VERTICAL_TOOLBAR_BUTTON_SIZE
        };
    }

    const toolbarStyle = window.getComputedStyle(toolbarContainer);
    const bottom = parseFloat(toolbarStyle.getPropertyValue('bottom')) || 0;

    const availableHeight = targetHeight - bottom;

    let recommendedButtonSize = Math.floor(availableHeight / (buttons.length * 1.2));

    if (recommendedButtonSize > VERTICAL_TOOLBAR_BUTTON_SIZE) {
        recommendedButtonSize = VERTICAL_TOOLBAR_BUTTON_SIZE;
    }

    return {
        toolbarContainer: toolbarContainer,
        buttons: buttons,
        buttonCount: buttons.length,
        recommendedButtonSize: recommendedButtonSize
    };
}

function getWiseToolbarExpandedHeight(toolbarContainer) {

    if (toolbarContainer === null) {
        return 0;
    }

    const clone = toolbarContainer.cloneNode(true);

    clone.classList.remove('is-collapsed');
    clone.classList.add('is-open');

    clone.style.position = 'absolute';
    clone.style.visibility = 'hidden';
    clone.style.pointerEvents = 'none';
    clone.style.left = '-9999px';
    clone.style.top = '0';
    clone.style.bottom = 'auto';

    document.body.appendChild(clone);

    const height = clone.offsetHeight;

    clone.remove();

    return height;
}

function getSharedWiseToolbarButtonSize(leftInfo, rightInfo) {

    if (leftInfo === null && rightInfo === null) {
        return VERTICAL_TOOLBAR_BUTTON_SIZE;
    }

    if (leftInfo !== null && rightInfo === null) {
        return leftInfo.recommendedButtonSize;
    }

    if (leftInfo === null && rightInfo !== null) {
        return rightInfo.recommendedButtonSize;
    }

    return Math.min(
        leftInfo.recommendedButtonSize,
        rightInfo.recommendedButtonSize
    );
}

function applyWiseToolbarButtonSize(toolbarContainer, buttonSize) {

    if (toolbarContainer === null) {
        return;
    }

    const buttons = toolbarContainer.querySelectorAll('.wiseVerticalToolbarButton');
    const buttonsArea = toolbarContainer.querySelector('.wiseToolbarButtonsArea');

    const gapSize = Math.floor(buttonSize / 6);

    buttons.forEach(button => {
        button.style.width = buttonSize + 'px';
        button.style.height = buttonSize + 'px';
    });

    if (buttonsArea !== null) {
        buttonsArea.style.gap = gapSize + 'px';
    }

}

function resizeWiseVerticalToolbars(targetBody) {

    if (isResizing) return;
    isResizing = true;

    try {
		
        let windowWidth = window.innerWidth || 1;
        let windowHeight = window.innerHeight || 1;

		const vv = window.visualViewport;

		if (vv) {
			windowWidth = vv.width;
			windowHeight = vv.height;
		}

        let wiseVerticalToolbarTargetHeight;

        if (windowWidth <= 600) {
            wiseVerticalToolbarTargetHeight = windowHeight / 2;
        } else {
            wiseVerticalToolbarTargetHeight = windowHeight;
        }

        const leftInfo = getWiseToolbarResizeInfo(
            wiseToolbarContainer,
            wiseVerticalToolbarTargetHeight
        );

        const rightInfo = getWiseToolbarResizeInfo(
            wiseRightToolbarContainer,
            wiseVerticalToolbarTargetHeight
        );

        let sharedButtonSize = getSharedWiseToolbarButtonSize(leftInfo, rightInfo);

        if (sharedButtonSize < 24) {
            sharedButtonSize = 24;
        }

        applyWiseToolbarButtonSize(wiseToolbarContainer, sharedButtonSize);
        applyWiseToolbarButtonSize(wiseRightToolbarContainer, sharedButtonSize);

        const leftToolbarWidth = wiseToolbarContainer !== null
            ? wiseToolbarContainer.offsetWidth
            : 0;

        balanceMarginLeft = BALANCE_X_MARGIN_LEFT_DEFAULT + leftToolbarWidth;
    } finally {
        isResizing = false;
    }
}


function positionWiseMenuBars(){

	if(wiseToolbarContainer === null)return;

	if(wiseToolbarDrawingButton !== null){
		let bounds_wiseVerticalToolbarDrawingButton = wiseToolbarDrawingButton.getBoundingClientRect();
		wiseMenuDrawingColors.style.left = bounds_wiseVerticalToolbarDrawingButton.right+'px';
		wiseMenuDrawingColors.style.top = bounds_wiseVerticalToolbarDrawingButton.top+'px';
	}

	if(wiseToolbarEraserButton !== null){
		if(wiseMenuErasers !== null){
		let bounds_wiseVerticalToolbarEraserButton = wiseToolbarEraserButton.getBoundingClientRect();
		wiseMenuErasers.style.left = bounds_wiseVerticalToolbarEraserButton.right+'px';
		wiseMenuErasers.style.top = bounds_wiseVerticalToolbarEraserButton.top+'px';
		}
	}

	if(wiseToolbarCreateLinkButton !== null){
		if(wiseMenuLinks !== null){
		let bounds_wiseVerticalToolbarCreateLinkButton = wiseToolbarCreateLinkButton.getBoundingClientRect();
		wiseMenuLinks.style.left = bounds_wiseVerticalToolbarCreateLinkButton.right+'px';
		wiseMenuLinks.style.top = bounds_wiseVerticalToolbarCreateLinkButton.top+'px';
		}
	}

	if(wiseToolbarAddElementMenuButton !== null){
		let bounds_wiseVerticalToolbarMenuListOpenerAddElement = wiseToolbarAddElementMenuButton.getBoundingClientRect();
		wiseMenuAddElement.style.left = bounds_wiseVerticalToolbarMenuListOpenerAddElement.right+'px';
		wiseMenuAddElement.style.top = bounds_wiseVerticalToolbarMenuListOpenerAddElement.top+'px';
	}

	if(wiseToolbarToolsMenuButton !== null){
		let bounds_wiseVerticalToolbarMenuListOpenerTools = wiseToolbarToolsMenuButton.getBoundingClientRect();
		wiseMenuTools.style.left = bounds_wiseVerticalToolbarMenuListOpenerTools.right+'px';
		wiseMenuTools.style.top = bounds_wiseVerticalToolbarMenuListOpenerTools.top+'px';
	}

}





// 追加
function executeWiseToolbarSelectorButton(){
    resetToSelectMode();
}
function executeWiseToolbarLaserButton(){
	laserLastPosition.x = null;
	laserLastPosition.y = null;
	currentWiseToolbarButton = 'wiseVerticalToolbarLaserButton';
	currentInteractionMode = 'laserMode';
	changeWiseVerticalToolbarButton('globalCanvas');
}
function executeWiseMenuAddElementWordButton(){
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiWordSearch);
	if(addWordSearchInput !== document.activeElement){
		addWordSearchInput.focus();
	}
}
function executeWiseMenuAddElementStickyNoteButton(){
	resetToSelectMode();
	openWisePanelUi(whiteboardPanel, whiteboardUiCreateStickyNote);
}

function executeWiseMenuAddElementTextBoxButton() {
    currentWiseToolbarButton = 'wiseVerticalToolbarMenuListOpenerAddElement';
    currentInteractionMode = 'createTextBoxMode';
    changeWiseVerticalToolbarButton('');
}

function executeWiseMenuErasersElementsEraserButton(){
	
	currentWiseToolbarButton = 'wiseVerticalToolbarSelectorButton';
	currentInteractionMode = 'elementsEraserMode';
	changeWiseVerticalToolbarButton('');

	let elms_movableContainer = document.querySelectorAll('.movableContainer');

	for(let i = INDEX_FIRST; i < elms_movableContainer.length; i++){

		elms_movableContainer[i].classList.add('deleteTarget');

		let elms_baseContainer = elms_movableContainer[i].querySelectorAll('.baseContainer');

		for(let j = INDEX_FIRST; j < elms_baseContainer.length; j++){
			elms_baseContainer[j].classList.add('deleteTarget');
		}
	}
	redrawLinkLines();
}

function executeSharedContentsUiOpenButton(){
	resetToSelectMode();
	switchPanelOverlaySharedContentsUiView([
		SHARED_CONTENTS_UI_VIEW.ADD,
		SHARED_CONTENTS_UI_VIEW.SELECTED
	]);
}