
document.body.addEventListener('keydown', function(e) {

	if(wisePanelWhiteboardBody === null)return;

	if(e.key === 'Control' || e.ctrlKey || e.metaKey){
		isCtrlPressed = true;
	}
	if(e.key === 'Alt' || e.altKey || e.key === 'AltGraph'){
		isAltPressed = true;
	}
	if(e.key === 'Shift' || e.shiftKey){
		isShiftPressed = true;
	}

	let isEnterKeyPressed = e.key === 'Enter' || e.keyCode === 13;
	let isBlankSpaceKeyPressed = e.key === '(blank space)' || e.keyCode === 32;
	let isTabKeyPressed = e.key === 'Tab' || e.keyCode === 9;

	let isAKeyPressed = e.key === 'a' || e.keyCode === 65;
	let isBKeyPressed = e.key === 'b' || e.keyCode === 66;
	let isCKeyPressed = e.key === 'c' || e.keyCode === 67;
	let isDKeyPressed = e.key === 'd' || e.keyCode === 68;
	let isEKeyPressed = e.key === 'e' || e.keyCode === 69;
	let isFKeyPressed = e.key === 'f' || e.keyCode === 70;
	let isGKeyPressed = e.key === 'g' || e.keyCode === 71;
	let isHKeyPressed = e.key === 'h' || e.keyCode === 72;
	let isIKeyPressed = e.key === 'i' || e.keyCode === 73;
	let isJKeyPressed = e.key === 'j' || e.keyCode === 74;
	let isKKeyPressed = e.key === 'k' || e.keyCode === 75;
	let isLKeyPressed = e.key === 'l' || e.keyCode === 76;
	let isMKeyPressed = e.key === 'm' || e.keyCode === 77;
	let isNKeyPressed = e.key === 'n' || e.keyCode === 78;
	let isOKeyPressed = e.key === 'o' || e.keyCode === 79;
	let isPKeyPressed = e.key === 'p' || e.keyCode === 80;
	let isQKeyPressed = e.key === 'q' || e.keyCode === 81;
	let isRKeyPressed = e.key === 'r' || e.keyCode === 82;
	let isSKeyPressed = e.key === 's' || e.keyCode === 83;
	let isTKeyPressed = e.key === 't' || e.keyCode === 84;
	let isUKeyPressed = e.key === 'u' || e.keyCode === 85;
	let isVKeyPressed = e.key === 'v' || e.keyCode === 86;
	let isWKeyPressed = e.key === 'w' || e.keyCode === 87;
	let isXKeyPressed = e.key === 'x' || e.keyCode === 88;
	let isYKeyPressed = e.key === 'y' || e.keyCode === 89;
	let isZKeyPressed = e.key === 'z' || e.keyCode === 90;

	let isZeroKeyPressed = e.key === '0' || e.keyCode === 96 || e.code === 'Numpad0';
	let isOneKeyPressed = e.key === '1' || e.keyCode === 97 || e.code === 'Numpad1';
	let isTwoKeyPressed = e.key === '2' || e.keyCode === 98 || e.code === 'Numpad2';
	let isThreeKeyPressed = e.key === '3' || e.keyCode === 99 || e.code === 'Numpad3';
	let isFourKeyPressed = e.key === '4' || e.keyCode === 100 || e.code === 'Numpad4';
	let isFiveKeyPressed = e.key === '5' || e.keyCode === 101 || e.code === 'Numpad5';
	let isSixKeyPressed = e.key === '6' || e.keyCode === 102 || e.code === 'Numpad6';
	let isSevenKeyPressed = e.key === '7' || e.keyCode === 103 || e.code === 'Numpad7';
	let isEightKeyPressed = e.key === '8' || e.keyCode === 104 || e.code === 'Numpad8';
	let isNineKeyPressed = e.key === '9' || e.keyCode === 105 || e.code === 'Numpad9';

	let isPlusKeyPressed = e.key === '+' || e.keyCode === 107 || e.code === 'NumpadAdd';
	let isMinusKeyPressed = e.key === '-' || e.keyCode === 109 || e.code === 'NumpadSubtract';

	let isArrowUpKeyPressed = e.key === 'ArrowUp' || e.keyCode === 38;
	let isArrowDownKeyPressed = e.key === 'ArrowDown' || e.keyCode === 40;

	let isDeleteKeyPressed = e.key === 'Delete' || e.keyCode === 46;
	let isBackspaceKeyPressed = e.key === 'Backspace' || e.keyCode === 8;
	let isSlashKeyPressed = e.key === '/' || e.keyCode === 111 || e.code === 'NumpadDivide';

	let doExecute = false;

	if (isTabKeyPressed) {
		let elms = document.querySelectorAll('.innerContainerTextArea');
		let focusedElm = document.activeElement;

		if (focusedElm.classList.contains('innerContainerTextArea')) {
			const currentIndex = Array.from(elms).indexOf(focusedElm);
			const nextIndex = (currentIndex + 1) % elms.length;
			elms[nextIndex].focus();
			e.preventDefault();
		}

		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if(isCompositionInProgress_wisePanelWhiteboardUiSearchInput){
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return;
	}

	if (isCtrlPressed && isSlashKeyPressed) {
		for(let i = INDEX_FIRST; i < currentSelectedMovableContainers.length; i++){
			let elm_currentSelected = currentSelectedMovableContainers[i];
			sliceElements(elm_currentSelected);
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isShiftPressed && isPlusKeyPressed) {
		changeEraserSize(true);
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isShiftPressed && isMinusKeyPressed) {
		changeEraserSize(false);
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isPlusKeyPressed) {
		changeWiseUiFontSize(true);
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isMinusKeyPressed) {
		changeWiseUiFontSize(false);
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isShiftPressed && isAltPressed && isMKeyPressed) {
	}

	if (
		isCtrlPressed &&
		isShiftPressed &&
		isZKeyPressed
	) {
		if (isActiveEditableElement()) {
			return true;
		}
		redo();
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isZKeyPressed) {
		if (isActiveEditableElement()) {
			return true;
		}
		undo();
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isYKeyPressed) {
		if (isActiveEditableElement()) {
			return true;
		}
		redo();
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	const focusedElement = document.activeElement;
	if (focusedElement) {
		if (focusedElement instanceof HTMLTextAreaElement) {
			if (isCtrlPressed && !isAKeyPressed && !isCKeyPressed && !isVKeyPressed && !isXKeyPressed) {
				e.preventDefault();
			}
			isCtrlPressed = false;
			isShiftPressed = false;
			isAltPressed = false;
			return;
		}
	}

	if (isCtrlPressed && isOKeyPressed) {
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isAKeyPressed) {
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isSKeyPressed) {
		if(wiseToolbarSelectorButton){
			executeWiseToolbarSelectorButton();
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}


	if(isArrowUpKeyPressed){
		doExecute = navigateSideMenuByArrowKey(true);
		if(doExecute){
			e.preventDefault();
			isCtrlPressed = false;
			isShiftPressed = false;
			isAltPressed = false;
			return false;
		}
	}
	if(isArrowDownKeyPressed){
		doExecute = navigateSideMenuByArrowKey(false);
		if(doExecute){
			e.preventDefault();
			isCtrlPressed = false;
			isShiftPressed = false;
			isAltPressed = false;
			return false;
		}
	}
	if (isCtrlPressed && isOneKeyPressed) {
		handleSelectDrawingColor('black');

		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isTwoKeyPressed) {
		handleSelectDrawingColor('red');

		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isThreeKeyPressed) {
		handleSelectDrawingColor('blue');
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isFourKeyPressed) {
		handleSelectDrawingColor('orange');
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}
	if (isCtrlPressed && isFiveKeyPressed) {
		handleSelectDrawingColor('green');
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isPKeyPressed) {
		togglePenColorShortcut();
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isLKeyPressed) {
		if(wiseToolbarLaserButton){
			executeWiseToolbarLaserButton();
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isDKeyPressed) {
		if(wiseMenuAddElementWordButton){
			executeWiseMenuAddElementWordButton();
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isFKeyPressed) {
		if(wiseMenuAddElementStickyNoteButton){
			executeWiseMenuAddElementStickyNoteButton();
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isGKeyPressed) {
	}

	if (
		(
			isCtrlPressed &&
			isShiftPressed &&
			isBlankSpaceKeyPressed
		) ||
		(
			isCtrlPressed &&
			isBlankSpaceKeyPressed
		)
	) {
		if(wiseMenuAddElementTextBoxButton){
			executeWiseMenuAddElementTextBoxButton();
		}
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isZeroKeyPressed) {
		isPenColorShortcutEnabled = FLAG_FALSE;
		currentWiseToolbarButton = 'wiseVerticalToolbarEraserButton';
		currentInteractionMode = 'eraserMode';
		changeWiseVerticalToolbarButton('wiseCanvasHandWriting');
		e.preventDefault();
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		return false;
	}

	if (isCtrlPressed && isShiftPressed && isAltPressed && isBackspaceKeyPressed) {
		if(wiseToolbarSelectorButton){
			executeWiseToolbarSelectorButton();
		}
		e.preventDefault();
		isShortcutActive = false;
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		resetHandwriting();
		return false;
	}

	if (isCtrlPressed && isShiftPressed && isAltPressed && isDeleteKeyPressed) {
		if(wiseToolbarSelectorButton){
			executeWiseToolbarSelectorButton();
		}
		e.preventDefault();
		isShortcutActive = false;
		isCtrlPressed = false;
		isShiftPressed = false;
		isAltPressed = false;
		deleteAllElements();
		return false;
	}
	if (isCtrlPressed && isShiftPressed && isAltPressed) {
		if(currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode'){
			if(
				wiseMenuErasersElementsEraserButton &&
				!wiseMenuErasersElementsEraserButton.classList.contains('wiseLeftVerticalToolbarButton-selected')
			){
				isShortcutActive = true;
				executeWiseMenuErasersElementsEraserButton();
				e.preventDefault();
				isCtrlPressed = false;
				isShiftPressed = false;
				isAltPressed = false;
				return false;
			}
		}
	}
	if ((isCtrlPressed && isShiftPressed) || (isCtrlPressed && isAltPressed)) {
		if(currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode'){
			if(wiseToolbarCreateLinkButton){
				if(
					!wiseToolbarCreateLinkButton.classList.contains('wiseLeftVerticalToolbarButton-selected') ||
					currentInteractionMode === 'createPriorityLinkMode'
				){
					isShortcutActive = true;
					openWiseMenuBarLinks(e);
					clearTimeout(toolbarLongPressTimer);
					e.preventDefault();
					isCtrlPressed = false;
					isShiftPressed = false;
					isAltPressed = false;
					return false;
				}
			}
		}
	}

}, false);

document.body.addEventListener('keyup', function(e) {

	if(e.key === 'Control' || e.ctrlKey || e.metaKey){
		isCtrlPressed = false;
	}
	if(e.key === 'Alt' || e.altKey || e.key === 'AltGraph'){
		isAltPressed = false;
	}
	if(e.key === 'Shift' || e.shiftKey){
		isShiftPressed = false;
	}

	if(isShortcutActive){
	isShortcutActive = false;
		if (!isCtrlPressed && !isAltPressed) {;
			if(wiseToolbarSelectorButton){
				executeWiseToolbarSelectorButton();
			}
			e.preventDefault();
			return false;
		}

		if (!isCtrlPressed && !isShiftPressed && !isAltPressed) {
			if(wiseToolbarSelectorButton){
				executeWiseToolbarSelectorButton();
			}
			e.preventDefault();
			return false;
		}
	}
}, false);



function isActiveEditableElement() {
	const activeEl = document.activeElement;
	const memoPadTextareas = document.querySelectorAll('.wisePanelMemoPadViewTextarea');
	const elms_contentEditable = document.querySelectorAll('[contenteditable="true"]');
	const elms_grammarInsightsDisplayAreaLiText = document.querySelectorAll('.grammarInsightsDisplayAreaLiText');
	return [...memoPadTextareas].includes(activeEl) ||
			[...elms_contentEditable].includes(activeEl) ||
			[...elms_grammarInsightsDisplayAreaLiText].includes(activeEl);
}

function togglePenColorShortcut(){

    if (isPenColorShortcutEnabled === FLAG_FALSE) {
        handleSelectDrawingColor('black');
        isPenColorShortcutEnabled = FLAG_TRUE;
    } else {
        handleSelectDrawingColor('red');
        isPenColorShortcutEnabled = FLAG_FALSE;
    }

}


function navigateSideMenuByArrowKey(isArrowUpKeyPressed){

	if(isAllowedListScroll){
	let liElements = wisePanelWhiteboardUiSelectableList.querySelectorAll('li'),
		count_liElements = liElements.length;

		if(count_liElements > LENGTH_EMPTY){
			if(!liElements[INDEX_FIRST].classList.contains('resultEmpty')){
				if(isArrowUpKeyPressed){
					if (selectedItemIndex > INDEX_FIRST) {
						liElements[selectedItemIndex].classList.remove('selectedLiItem');
						selectedItemIndex -= 1;
						liElements[selectedItemIndex].classList.add('selectedLiItem');
					}
				}
				else{
					if (selectedItemIndex < count_liElements - LAST_INDEX_OFFSET) {
						if (selectedItemIndex >= INDEX_FIRST) {
							liElements[selectedItemIndex].classList.remove('selectedLiItem');
						}
						selectedItemIndex += 1;
						liElements[selectedItemIndex].classList.add('selectedLiItem');
					}
				}
				if(selectedItemIndex >= INDEX_FIRST){
					const itemHeight = liElements[selectedItemIndex].offsetHeight,
						containerHeight = wisePanelWhiteboardUiSelectableList.parentNode.clientHeight,
						containerOffsetTop = wisePanelWhiteboardUiSelectableList.parentNode.offsetTop,
						itemOffsetTop = liElements[selectedItemIndex].offsetTop;

					let wisePanelWhiteboardUiSelectableListComputedStyle = window.getComputedStyle(wisePanelWhiteboardUiSelectableList),
						wisePanelWhiteboardUiSelectableListMarginTopValue = wisePanelWhiteboardUiSelectableListComputedStyle.getPropertyValue('margin-top'),
						wisePanelWhiteboardUiSelectableListMarginTopNumericValue = parseFloat(wisePanelWhiteboardUiSelectableListMarginTopValue);

					let currentOffsetTop = itemOffsetTop - containerOffsetTop;

					if (currentOffsetTop < wisePanelWhiteboardUiSelectableList.parentNode.scrollTop) {
						wisePanelWhiteboardUiSelectableList.parentNode.scrollTop = currentOffsetTop - wisePanelWhiteboardUiSelectableListMarginTopNumericValue;
					} else if (currentOffsetTop + itemHeight > wisePanelWhiteboardUiSelectableList.parentNode.scrollTop + containerHeight) {
						wisePanelWhiteboardUiSelectableList.parentNode.scrollTop = currentOffsetTop + itemHeight - containerHeight;
					}
				}
				return true;
			}
		}
	}
	return false;
}

function deleteAllElements(){

	let elms = document.querySelectorAll('.movableContainer');

	if(wiseToolbarSelectorButton !== null){
		executeWiseToolbarSelectorButton();
	}

	for(let i = INDEX_FIRST; i < elms.length; i++){
		deleteElement(elms[i]);
	}
	saveState(STATE_TITLE_DELETE_ALL_CONTAINERS[intSelectedLanguage]);
	return;
}
