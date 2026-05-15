


let movableMode = MOVABLE_MODE.SELECT;
let activeMovableContainer = null;
let activePointerId = null;
let activePendingTextarea = null;

/******************************************************
 * touch mouse start
 * 
 ******************************************************/

function handleBodyPointerStart(e) {

    if (e.target.closest('.menuContainer, .wiseExpandableToolbar, .wiseMenuBar')) {
        return;
    }

    if (e.target.tagName === 'IMG') {
        e.preventDefault();
    }

    if (handleRightClickActions(e)) {
        return;
    }

    if (handleLaserStartOnGlobalCanvas(e)) {
        return;
    }

    if (wisePanelWhiteboardViewMainContentArea === null) {
        return;
    }

    const targetElement = e.target;

    if (handleLaserStartOnWiseCanvas(e)) {
        return;
    }

    if (handleWisePanelSplitterPointerStart(targetElement, e)) {
        return;
    }

    if (handleWisePanelViewHandlePointerStart(targetElement, e)) {
        return;
    }

    handleResetSelectionIfNeeded(targetElement, e);

    cacheWiseLocalPointBase(wisePanelWhiteboardViewMainContentArea);

    const clientPoint = getClientPoint(e);
    const localPoint = getCachedLocalPoint(clientPoint);

    if (handleElementsEraser(clientPoint.x, clientPoint.y)) {
        return;
    }

    if (handleCreateTextBoxMode(localPoint.x, localPoint.y)) {
        return;
    }

    if (handleCloseContextMenuIfOpened(clientPoint.x, clientPoint.y)) {
        return;
    }
	
}

document.body.addEventListener('pointerdown', handleBodyPointerStart, { passive: false });


const DRAG_SCROLL_EDGE_SIZE = 40;
const DRAG_SCROLL_HOVER_DELAY = 1000;
const DRAG_SCROLL_STEP = 12;
const DRAG_SCROLL_LOCK_CLASS = 'wisePanelDragScrollLock';

const dragAutoScrollState = {
    edge: null,
    enteredAt: 0,
    isActive: false,
    rafId: 0
};

let lastMoveClientPointX = 0;
let lastMoveClientPointY = 0;


function tryCopyLinkOnRightClick(e) {

	const target = e.target;

	if (target.tagName.toLowerCase() !== 'a') {
		return false;
	}

	if (!target.classList.contains('grammarOutlineCreateAreaAddContents')) {
		return false;
	}

	const linkText = target.href;

	navigator.clipboard.writeText(linkText)
		.then(() => {
			alert('リンクのテキストがクリップボードにコピーされました！');
		})
		.catch((err) => {
			console.error('クリップボードへのコピーに失敗しました:', err);
		});

	return true;
}

function tryToggleWiseToolbarOnRightClick(e) {

	if (wiseToolbarContainer === null) {
		return false;
	}

	if (currentWiseToolbarButton === 'wiseVerticalToolbarLaserButton') {
		executeWiseToolbarSelectorButton();
		return true;
	}

	if (currentWiseToolbarButton === 'wiseVerticalToolbarSelectorButton') {

		const target = e.target;
		const isEditable =
			target.tagName === 'INPUT' ||
			target.tagName === 'TEXTAREA' ||
			target.isContentEditable;

		if (isEditable) {
			e.preventDefault();
			target.blur();
			window.getSelection()?.removeAllRanges();
		}

		executeWiseToolbarLaserButton();
		return true;
	}

	return false;
}

function tryToggleGlobalToolbarOnRightClick(e) {

	if (globalToolbarContainer === null || globalToolbarLaserButton === null) {
		return false;
	}

	if (currentGlobalToolbarButton === 'globalVerticalToolbarLaserButton') {
		switchGlobalVerticalToolbarButton(globalToolbarSelectorButton);
		return true;
	}

	if (currentGlobalToolbarButton === 'globalVerticalToolbarSelectorButton') {

		const target = e.target;
		const isEditable =
			target.tagName === 'INPUT' ||
			target.tagName === 'TEXTAREA' ||
			target.isContentEditable;

		if (isEditable) {
			e.preventDefault();
			target.blur();
			window.getSelection()?.removeAllRanges();
		}

		switchGlobalVerticalToolbarButton(globalToolbarLaserButton);
		return true;
	}

	return false;
}

function handleRightClickActions(e) {

	if (e.button !== 2) {
		return false;
	}

	if (tryCopyLinkOnRightClick(e)) {
		return true;
	}

	if (tryToggleWiseToolbarOnRightClick(e)) {
		return true;
	}

	if (tryToggleGlobalToolbarOnRightClick(e)) {
		return true;
	}

	return false;
}

function handleLaserStartOnGlobalCanvas(e) {

	if (currentGlobalToolbarButton !== 'globalVerticalToolbarLaserButton') {
		return false;
	}

	const clientPoint = getClientPoint(e);

	isDrawingLaserOnGlobalCanvas = true;
	startLaserDrawing(clientPoint.x, clientPoint.y, global_context_canvas_globalCanvas);

	return true;
}

function handleLaserStartOnWiseCanvas(e) {

    if (currentInteractionMode !== 'laserMode') {
        return false;
    }

    let elm_canvas = null;
    let elm_context = null;

    if (globalCanvas !== null) {
        elm_canvas = globalCanvas;
        elm_context = global_context_canvas_globalCanvas;
        isDrawingLaserOnGlobalCanvas = true;
    } else {
        elm_canvas = wiseCanvasOriginal;
        elm_context = wiseCanvasOriginalContext;
        isDrawingLaser = true;
    }

    const clientPoint = getClientPoint(e);

    startLaserDrawing(clientPoint.x, clientPoint.y, elm_context);

    return true;
}

function handleResetSelectionIfNeeded(targetElement, e) {
	
	if (e.ctrlKey || e.metaKey) {
        return;
    }

	if (targetElement.closest('.menuContainer')) {
		return;
	}

	const clickedMovable = targetElement.closest('.wiseContainersMainContentArea .movableContainer');
	const clickedSelectable = targetElement.closest('.selectableContainer');

	if (clickedMovable || clickedSelectable) {
		return;
	}

	currentSelectedMovableContainers = [];
	resetSelectedElements();
}

function handleElementsEraser(x, y) {

	if (currentInteractionMode !== 'elementsEraserMode') {
		return false;
	}

	linkLineEraserPointX = x;
	linkLineEraserPointY = y;
	touchedLinkLines = [];

	redrawLinkLines();

	for (let i = INDEX_FIRST; i < touchedLinkLines.length; i++) {
		const elm_canvas = touchedLinkLines[i];
		const int_left_link_id = escapeNumber(elm_canvas.dataset.leftLinkId);
		const elm_movableContainer = document.getElementById('movableContainer' + int_left_link_id);

		elm_canvas.remove();

		delete elm_movableContainer.dataset.linkId;
		delete elm_movableContainer.dataset.linkType;
	}

	linkLineEraserPointX = 0;
	linkLineEraserPointY = 0;

	if (touchedLinkLines.length) {
		saveState(STATE_TITLE_DELETE_LINK_LINE[intSelectedLanguage]);
	}

	touchedLinkLines = [];

	return true;
}

function handleCreateTextBoxMode(x, y) {

    if (currentInteractionMode !== 'createTextBoxMode') {
        return false;
    }

    const didCollide = checkCollision(wiseToolbarContainer, x, y);
    if (didCollide) {
        return true;
    }

    const lastCreatedMovableContainer = createTextAreaContainer('', x, y);
    resetToSelectMode();

    const textarea = lastCreatedMovableContainer.querySelector('.textAreaContainerTextArea');

    if (textarea !== null) {
        textarea.classList.add('emptyText');

        setTimeout(function () {
            enterTextareaEditMode(textarea, lastCreatedMovableContainer);
        }, 10);
    } else {
        selectMovableElement(lastCreatedMovableContainer);
    }

    return true;
}

function handleCloseContextMenuIfOpened(x, y) {

	if (!isOpened_contextMenu) {
		return false;
	}

	const isInsideContextMenu =
		contextMenuLeft <= x &&
		x <= contextMenuRight &&
		contextMenuTop <= y &&
		y <= contextMenuBottom;

	if (isInsideContextMenu) {
		return true;
	}

	wiseContextMenu.classList.add('hidden');
	isOpened_contextMenu = false;

	return true;
}

/******************************************************
 * touch mouse end
 * 
 ******************************************************/
async function handleBodyPointerEnd(e) {
    
    if (handleEndGlobalLaser(e)) {
        return;
    }

    if (handleWisePanelSplitterPointerEnd(e)) {
        return;
    }

    if (handleWisePanelViewHandlePointerEnd(e)) {
        return;
    }

    if (wisePanelWhiteboardViewMainContentArea === null) {
        return;
    }

    const timeDifference = setTouchEndTimeAndGetDifference(e);

    if (handleEndDrawingLink(e)) {
        return;
    }

    if (handleEndWiseLaser(e)) {
        return;
    }

    if (handleEndHandWriting(e)) {
        return;
    }
}

document.body.addEventListener('pointerup', handleBodyPointerEnd);



function isWhiteboardPanelSplitMode() {

    if (typeof wisePanelState === 'undefined') {
        return false;
    }

    if (typeof WISE_PANEL_LAYOUT === 'undefined') {
        return false;
    }

    return wisePanelState.layout !== WISE_PANEL_LAYOUT.SINGLE;
}

function setWhiteboardDragScrollLock(isLocked) {

    if (!(wisePanelWhiteboardBody instanceof HTMLElement)) {
        return;
    }

    if (isLocked) {
        wisePanelWhiteboardBody.classList.add(DRAG_SCROLL_LOCK_CLASS);
        return;
    }

    wisePanelWhiteboardBody.classList.remove(DRAG_SCROLL_LOCK_CLASS);
}

function resetDragAutoScrollState() {

    dragAutoScrollState.edge = null;
    dragAutoScrollState.enteredAt = 0;
    dragAutoScrollState.isActive = false;

    if (dragAutoScrollState.rafId) {
        cancelAnimationFrame(dragAutoScrollState.rafId);
        dragAutoScrollState.rafId = 0;
    }
}

function stopDragAutoScroll() {
    resetDragAutoScrollState();
}

function detectWhiteboardScrollEdge(clientX, clientY) {

    if (!(wisePanelWhiteboardBody instanceof HTMLElement)) {
        return null;
    }

    const rect = wisePanelWhiteboardBody.getBoundingClientRect();

    if (
        clientX < rect.left ||
        clientX > rect.right ||
        clientY < rect.top ||
        clientY > rect.bottom
    ) {
        return null;
    }

    const nearTop = clientY <= rect.top + DRAG_SCROLL_EDGE_SIZE;
    const nearBottom = clientY >= rect.bottom - DRAG_SCROLL_EDGE_SIZE;
    const nearLeft = clientX <= rect.left + DRAG_SCROLL_EDGE_SIZE;
    const nearRight = clientX >= rect.right - DRAG_SCROLL_EDGE_SIZE;

    if (nearTop) {
        return 'top';
    }

    if (nearBottom) {
        return 'bottom';
    }

    if (nearLeft) {
        return 'left';
    }

    if (nearRight) {
        return 'right';
    }

    return null;
}

function performDragAutoScrollStep() {

    dragAutoScrollState.rafId = 0;

    if (!dragAutoScrollState.isActive) {
        return;
    }

    if (!(wisePanelWhiteboardBody instanceof HTMLElement)) {
        stopDragAutoScroll();
        return;
    }

    const edge = dragAutoScrollState.edge;
    if (edge === null) {
        stopDragAutoScroll();
        return;
    }

    const elm = wisePanelWhiteboardBody;

    if (edge === 'top') {
        elm.scrollTop -= DRAG_SCROLL_STEP;
    }
    else if (edge === 'bottom') {
        elm.scrollTop += DRAG_SCROLL_STEP;
    }
    else if (edge === 'left') {
        elm.scrollLeft -= DRAG_SCROLL_STEP;
    }
    else if (edge === 'right') {
        elm.scrollLeft += DRAG_SCROLL_STEP;
    }

    dragAutoScrollState.rafId = requestAnimationFrame(performDragAutoScrollStep);
}

function startDragAutoScroll() {

    if (dragAutoScrollState.isActive) {
        return;
    }

    dragAutoScrollState.isActive = true;

    if (!dragAutoScrollState.rafId) {
        dragAutoScrollState.rafId = requestAnimationFrame(performDragAutoScrollStep);
    }
}

function updateDragAutoScrollOnPointerMove(clientX, clientY) {

    if (!isMoving) {
        stopDragAutoScroll();
        return;
    }

    if (!isWhiteboardPanelSplitMode()) {
        stopDragAutoScroll();
        return;
    }

    const edge = detectWhiteboardScrollEdge(clientX, clientY);

    if (edge === null) {
        stopDragAutoScroll();
        return;
    }

    const now = Date.now();

    if (dragAutoScrollState.edge !== edge) {
        dragAutoScrollState.edge = edge;
        dragAutoScrollState.enteredAt = now;
        dragAutoScrollState.isActive = false;

        if (dragAutoScrollState.rafId) {
            cancelAnimationFrame(dragAutoScrollState.rafId);
            dragAutoScrollState.rafId = 0;
        }
        return;
    }

    if (!dragAutoScrollState.isActive) {
        const elapsed = now - dragAutoScrollState.enteredAt;

        if (elapsed >= DRAG_SCROLL_HOVER_DELAY) {
            startDragAutoScroll();
        }
    }
}

function setTouchEndTimeAndGetDifference(e) {
	touchEndTimestamp = e.timeStamp;
	return touchEndTimestamp - touchStartTimestamp;
}

function handleEndDrawingLink(e) {

	if (!isDrawing) {
		return false;
	}

	isDrawing = false;

	wiseCanvasOriginalContext.clearRect(0, 0, wiseCanvasOriginal.width, wiseCanvasOriginal.height);

	const clientPoint = getClientPoint(e);
	const isPermitted = createLink(clientPoint);

	if (isPermitted) {
		saveState(STATE_TITLE_DRAW_LINK_LINE[intSelectedLanguage]);
	}

	return true;
}

async function handleDropToPanelsIfNeeded(pointEndX, pointEndY) {

	const movingTargets = getMovingTargetContainers();
    const targets = getDroppableMovingContainers();

    if (targets.length === 0) {
        return false;
    }

    const dropPanel = detectDropPanel(pointEndX, pointEndY);

    if (dropPanel === 'memo') {
        const isExecuted = addValueToMemoPad(targets);

        if (isExecuted) {
            restoreMovingContainers(movingTargets);
            saveState(STATE_TITLE_DELETE_CONTAINER[intSelectedLanguage]);

            if (targets.includes(movingMovableContainer)) {
                movingMovableContainer = null;
            }

            return true;
        }

        restoreMovingContainers(movingTargets);
        return true;
    }

    if (dropPanel === 'grammar') {
        removeDragPreviewClones();
        stopDragAutoScroll();

        for (const target of movingTargets) {
            target.classList.remove('drag-source-hidden');
            target.style.transform = '';
        }

        for (const target of targets) {
            target.classList.add('hidden');
        }

        await new Promise(requestAnimationFrame);

        const isExecuted = await addValueToGrammarExplanation(targets);

        if (isExecuted) {
            restoreMovingContainers(movingTargets);
            saveState(STATE_TITLE_DELETE_CONTAINER[intSelectedLanguage]);

            if (targets.includes(movingMovableContainer)) {
                movingMovableContainer = null;
            }

            return true;
        }

        restoreMovingContainers(movingTargets);
        return true;
    }

    if (dropPanel === 'whiteboard') {
        return false;
    }

    restoreMovingContainers(movingTargets);
    return true;
}

function getDroppableMovingContainers() {

    const selectedContainers = Array.isArray(currentSelectedMovableContainers)
        ? currentSelectedMovableContainers
        : [];

    const targets = selectedContainers.filter(container =>
        container &&
        (
            container.classList.contains('textAreaContainer') ||
            container.classList.contains('stickyNoteContainer')
        )
    );

    if (targets.length > 0) {
        return targets;
    }

    if (
        movingMovableContainer &&
        (
            movingMovableContainer.classList.contains('textAreaContainer') ||
            movingMovableContainer.classList.contains('stickyNoteContainer')
        )
    ) {
        return [movingMovableContainer];
    }

    return [];
}

function restoreMovingContainers(targets) {

    if (!Array.isArray(targets)) {
        targets = [targets];
    }

    for (const target of targets) {

        if (!target) continue;

        target.classList.remove('hidden');
        target.classList.remove('drag-source-hidden');

        target.style.transform = '';

        if ('dragStartLeft' in target.dataset) {
            target.style.left = target.dataset.dragStartLeft;
        }

        if ('dragStartTop' in target.dataset) {
            target.style.top = target.dataset.dragStartTop;
        }

        delete target.dataset.dragStartLeft;
        delete target.dataset.dragStartTop;

    }
}

async function handleEndMoving(e, timeDifference) {

    if (!isMoving) {
        return false;
    }

    const clientPoint = getClientPoint(e);
    const localPoint = getLocalPoint(clientPoint, wisePanelWhiteboardViewMainContentArea);

    try {
        const dropped = await handleDropToPanelsIfNeeded(clientPoint.x, clientPoint.y);

        redrawLinkLines();

        if (dropped) {
            cleanupMovingState();
            return true;
        }

        const distance = calculateMovableContainerDistance(
            moveStartPointX,
            moveStartPointY,
            localPoint.x,
            localPoint.y
        );

        moveStartPointX = 0;
        moveStartPointY = 0;

        if (timeDifference > CLICK_THRESHOLD || distance > 50) {
            saveState(STATE_TITLE_MOVE_CONTAINER[intSelectedLanguage]);
        }

        cleanupMovingState();
        return true;

    } catch (error) {
        cleanupMovingState();
        redrawLinkLines();
        throw error;
    }
}

function calculateMovableContainerDistance(x1, y1, x2, y2) {
	const dx = x2 - x1;
	const dy = y2 - y1;
	const distance = Math.sqrt(dx * dx + dy * dy);
	return distance;
}

function handleEndWiseLaser(e) {

	if (!isDrawingLaser) {
		return false;
	}

	const clientPoint = getClientPoint(e);

	isDrawingLaser = false;
	endLaserDrawing(clientPoint.x, clientPoint.y, wiseCanvasOriginal, wiseCanvasOriginalContext, 500);

	return true;
}

function handleEndHandWriting(e) {

	if (currentInteractionMode !== 'drawingMode') {
		return false;
	}

	endHandwriting(e);

	return true;
}

function handleEndGlobalLaser(e) {

	if (!isDrawingLaserOnGlobalCanvas) {
		return false;
	}

	const clientPoint = getClientPoint(e);

	isDrawingLaserOnGlobalCanvas = false;
	endLaserDrawing(clientPoint.x, clientPoint.y, globalCanvas, global_context_canvas_globalCanvas, 500);

	return true;
}


/******************************************************
 * touch mouse cancel
 * 
 ******************************************************/
function handleBodyPointerCancel(e) {

    if (e.type === 'mouseout' && !isMouseLeavingWindow(e)) {
        return;
    }

    if (handleWisePanelSplitterPointerEnd(e)) {
        return;
    }

    if (handleWisePanelViewHandlePointerEnd(e)) {
        return;
    }

    if (handleCancelMoving()) {
        return;
    }

    if (handleCancelWiseLaser(e)) {
        return;
    }

    if (handleCancelGlobalLaser(e)) {
        return;
    }
}

document.body.addEventListener('pointercancel', handleBodyPointerCancel);


function handleCancelWiseLaser(e) {

	if (!isDrawingLaser) {
		return false;
	}

	const clientPoint = getClientPoint(e);

	isDrawingLaser = false;
	endLaserDrawing(clientPoint.x, clientPoint.y, wiseCanvasOriginal, wiseCanvasOriginalContext, 500);

	return true;
}

function handleCancelGlobalLaser(e) {

	if (!isDrawingLaserOnGlobalCanvas) {
		return false;
	}

	const clientPoint = getClientPoint(e);

	isDrawingLaserOnGlobalCanvas = false;
	endLaserDrawing(clientPoint.x, clientPoint.y, globalCanvas, global_context_canvas_globalCanvas, 500);

	return true;
}

function isMouseLeavingWindow(e) {
	return e.relatedTarget === null;
}


function handleCancelMoving() {

    if (!isMoving) {
        return false;
    }

    restoreMovingContainers(getMovingTargetContainers());

    cleanupMovingState();
    redrawLinkLines();

    return true;
}

function cancelAllMovablePointerState() {
    handleCancelMoving();
    resetMovablePointerState();
}

window.addEventListener('blur', function () {
    cancelAllMovablePointerState();
});

document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        cancelAllMovablePointerState();
    }
});

/******************************************************
 * touch mouse move
 * 
 ******************************************************/
function handleWisePointerMovePrimary(e) {

    if (
        typeof handleWisePanelSplitterPointerMove === 'function' &&
        handleWisePanelSplitterPointerMove(e)
    ) {
        return;
    }

    if (
        typeof handleWisePanelViewHandlePointerMove === 'function' &&
        handleWisePanelViewHandlePointerMove(e)
    ) {
        return;
    }

    if (!isInWisePointerMoveScope(e)) {
        return;
    }

    handleSwipeGuardTouchMove(e);

    const clientPoint = getClientPoint(e);
    const localPoint = getCachedLocalPoint(clientPoint);

    if (handleDrawLinkLineOnPointerMove(e, clientPoint.x, clientPoint.y)) {
        return;
    }

    if (handleDrawWiseLaserOnPointerMove(clientPoint.x, clientPoint.y)) {
        return;
    }
}

document.addEventListener('pointermove', handleWisePointerMovePrimary, { passive: false });

function isInWisePointerMoveScope(e) {
	return !!e.target.closest('#sectionWise');
}

function preventDefaultIfTouchMove(e) {
	if (shouldPreventTouchScroll(e) && e.cancelable) {
		e.preventDefault();
	}
}

function shouldPreventTouchScroll(e) {

    if (e.pointerType !== 'touch') {
        return false;
    }

    if (!isInWisePointerMoveScope(e)) {
        return false;
    }

    if (
        isMoving ||
        isDrawing ||
        isChangingSideMenuWidth ||
        isDrawingLaser ||
        isDrawingLaserOnGlobalCanvas
    ) {
        return true;
    }

    return false;
}

function handleMoveMovableContainerOnPointerMove(e, x, y) {

    if (!isMoving) {
        return false;
    }

    preventDefaultIfTouchMove(e);
    preventDefaultIfInnerTextArea(e);

    lockSelectionDuringPanelDragIfNeeded();

    const clientPoint = getClientPoint(e);

    lastMovePointX = x;
    lastMovePointY = y;
    lastMoveClientPointX = clientPoint.x;
    lastMoveClientPointY = clientPoint.y;

    moveModifierState.ctrl = e.ctrlKey;
    moveModifierState.meta = e.metaKey;
    moveModifierState.shift = e.shiftKey;

    if (!isMoveFramePending) {
        isMoveFramePending = true;
        requestAnimationFrame(processMoveFrame);
    }

    return true;
}

function processMoveFrame() {

    isMoveFramePending = false;

    if (!isMoving || !movingMovableContainer) {
        return;
    }

    const x = lastMovePointX;
    const y = lastMovePointY;

    moveSelectedMovableContainers(x, y);

    updateDragAutoScrollOnPointerMove(lastMoveClientPointX, lastMoveClientPointY);
    updateDragPreviewStateOnPointerMove(lastMoveClientPointX, lastMoveClientPointY, x, y);
    redrawLinkLines();
}

function preventDefaultIfInnerTextArea(e) {

    const targetElement = e.target;
    if (!targetElement.classList.contains('innerContainerTextArea')) {
        return;
    }

    if (!movingMovableContainer) {
        return;
    }

    if (!movingMovableContainer.classList.contains('wiseSelectionLock')) {
        return;
    }
    e.preventDefault();
}




function cacheSelectedDragStartPositions() {

    const boundsContainer = wisePanelWhiteboardViewMainContentArea.getBoundingClientRect();

    for (let i = INDEX_FIRST; i < currentSelectedMovableContainers.length; i++) {

        const elm = currentSelectedMovableContainers[i];
        if (!elm) continue;

        if (!('selectedDragStartLeftPx' in elm.dataset)) {

            const rect = elm.getBoundingClientRect();

            const left = parseFloat(elm.style.left);
            const top = parseFloat(elm.style.top);

            elm.dataset.selectedDragStartLeftPx = Number.isFinite(left) ? left : (rect.left - boundsContainer.left);
            elm.dataset.selectedDragStartTopPx = Number.isFinite(top) ? top : (rect.top - boundsContainer.top);
        }
    }
}

function clearSelectedDragStartPositions() {

    for (let i = INDEX_FIRST; i < currentSelectedMovableContainers.length; i++) {

        const elm = currentSelectedMovableContainers[i];
        if (!elm) continue;

        delete elm.dataset.selectedDragStartLeftPx;
        delete elm.dataset.selectedDragStartTopPx;
    }
}



function clampMovingDistance(distanceX, distanceY) {

    const parent = wisePanelWhiteboardViewMainContentArea;

    if (!(parent instanceof HTMLElement)) {
        return { x: distanceX, y: distanceY };
    }

    const zoomScale = getWiseZoomScale();

    const parentWidth = parent.clientWidth;
    const parentHeight = parent.clientHeight;

    let minX = -Infinity;
    let maxX = Infinity;
    let minY = -Infinity;
    let maxY = Infinity;

    for (let i = 0; i < currentSelectedMovableContainers.length; i++) {
        const elm = currentSelectedMovableContainers[i];
        if (!elm) continue;

        const startLeft = parseFloat(elm.dataset.selectedDragStartLeftPx) || 0;
        const startTop = parseFloat(elm.dataset.selectedDragStartTopPx) || 0;

        const rect = elm.getBoundingClientRect();
        const width = rect.width / zoomScale;
        const height = rect.height / zoomScale;

        minX = Math.max(minX, -startLeft);
        maxX = Math.min(maxX, parentWidth - (startLeft + width));

        minY = Math.max(minY, -startTop);
        maxY = Math.min(maxY, parentHeight - (startTop + height));
    }

    return {
        x: Math.min(Math.max(distanceX, minX), maxX),
        y: Math.min(Math.max(distanceY, minY), maxY)
    };
}
function moveSelectedMovableContainers(x, y) {

    const currentScrollLeft = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollLeft : 0;
    const currentScrollTop = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollTop : 0;

    const scrollDiffX = currentScrollLeft - dragStartScrollLeft;
    const scrollDiffY = currentScrollTop - dragStartScrollTop;

    let distanceX = (x - moveStartPointX) + scrollDiffX;
    let distanceY = (y - moveStartPointY) + scrollDiffY;

    cacheSelectedDragStartPositions();

    const clampedDistance = clampMovingDistance(distanceX, distanceY);

    distanceX = clampedDistance.x;
    distanceY = clampedDistance.y;

    for (let i = 0; i < currentSelectedMovableContainers.length; i++) {
        const elm = currentSelectedMovableContainers[i];

        const startLeft = parseFloat(elm.dataset.selectedDragStartLeftPx) || 0;
        const startTop = parseFloat(elm.dataset.selectedDragStartTopPx) || 0;

        elm.style.left = (startLeft + distanceX) + 'px';
        elm.style.top = (startTop + distanceY) + 'px';
    }
}

function handleDrawLinkLineOnPointerMove(e, x, y) {

	if (!isDrawing) {
		return false;
	}

	const clientPoint = getClientPoint(e);

	highlightValidLinkTarget(clientPoint);
	drawUnsettledLinkLine(x, y, movableContainerLinkMarker);

	return true;
}

function handleDrawWiseLaserOnPointerMove(x, y) {

	if (!isDrawingLaser) {
		return false;
	}

	drawLaser(x, y, wiseCanvasOriginalContext);

	return true;
}

function handleSwipeGuardTouchMove(e) {

	const target = e.target.closest('.menuContainer, .linkContainer');
	if (!target) {
		return;
	}

	preventDefaultOnTouchMove(e);
}

/******************************************************
 * items
 * 
 ******************************************************/
function handleLinkContainerPointerStart(e) {

	const linkContainer = e.target.closest('.linkContainer');
	if (!linkContainer) {
		return;
	}

	if (isDrawing) {
		return;
	}

	linkedMovableContainer = linkContainer;

	drawLineStartKey = escapeNumber(linkContainer.dataset.uniqueKey);
	linkMarkerSide = escapeHTML(linkContainer.dataset.linkMarkerType);

	if (linkMarkerSide === 'leftLinkMarker') {
		movableContainerLinkMarker = document.getElementById('leftLinkMarker' + drawLineStartKey);
	} else {
		movableContainerLinkMarker = document.getElementById('rightLinkMarker' + drawLineStartKey);
	}

	if (
		linkedMovableContainer.classList.contains('phraseClauseContainer') &&
		linkMarkerSide === 'leftLinkMarker'
	) {
		return;
	}

	isDrawing = true;

	maxZIndex += 1;
	linkedMovableContainer.style.zIndex = maxZIndex;
}

document.addEventListener('pointerdown', handleLinkContainerPointerStart);


function handleMovableContainerPointerDown(e) {

    if (e.target.closest('.linkContainer')) {
        return;
    }

    if (e.target.closest('.menuContainer')) {
        return;
    }
	
    const movableContainer = e.target.closest('.wiseContainersMainContentArea .movableContainer');
    if (!movableContainer) {
        return;
    }

    if (currentInteractionMode === 'elementsEraserMode') {
        handleMovableContainerEraserStart(e, movableContainer);
        return;
    }

    if (isMoving || isDrawing) {
        return;
    }

    touchStartTimestamp = e.timeStamp;

    const isMultiSelect = e.ctrlKey || e.metaKey;
    const textarea = e.target.closest('textarea');
    const isTextarea = !!textarea;
    const isSelected = movableContainer.classList.contains('selectedMovableContainer');

    activeMovableContainer = movableContainer;
    activePointerId = e.pointerId;
    activePendingTextarea = null;

    if (typeof movableContainer.setPointerCapture === 'function') {
        try {
            movableContainer.setPointerCapture(e.pointerId);
        } catch (error) {}
    }

    const clientPoint = getClientPoint(e);
    const localPoint = getLocalPoint(clientPoint, wisePanelWhiteboardViewMainContentArea);

    pressPointX = localPoint.x;
    pressPointY = localPoint.y;

    if (isMultiSelect) {

        toggleMovableContainerSelection(movableContainer);
        movableMode = MOVABLE_MODE.SELECT;

        e.preventDefault();
        return;
    }

    if (!isSelected) {
        currentSelectedMovableContainers = [];
        resetSelectedElements();
        selectMovableElement(movableContainer);
        movableMode = MOVABLE_MODE.SELECT;

        e.preventDefault();
        return;
    }

    if (isTextarea) {

        const isFocusedTextarea =
            document.activeElement === textarea;

        if (isFocusedTextarea) {

            currentSelectedMovableContainers = [];
            resetSelectedElements();
            selectMovableElement(movableContainer);

            movableMode = MOVABLE_MODE.EDIT;
            return;
        }

        movableMode = MOVABLE_MODE.PENDING;
        activePendingTextarea = textarea;

        if (e.cancelable) {
            e.preventDefault();
        }

        return;
    }

    movableMode = MOVABLE_MODE.MOVE;
    e.preventDefault();
}

function handleMovableContainerPointerMove(e) {

    if (activePointerId !== null && e.pointerId !== activePointerId) {
        return;
    }

    if (!activeMovableContainer) {
        return;
    }

    if (movableMode === MOVABLE_MODE.EDIT) {
        return;
    }

    if (
        movableMode !== MOVABLE_MODE.MOVE &&
        movableMode !== MOVABLE_MODE.PENDING
    ) {
        return;
    }

    const clientPoint = getClientPoint(e);
    const localPoint = getLocalPoint(clientPoint, wisePanelWhiteboardViewMainContentArea);

    const distance = calculateMovableContainerDistance(
        pressPointX,
        pressPointY,
        localPoint.x,
        localPoint.y
    );

    if (!isMoving) {
        if (distance < MOVE_START_DISTANCE) {
            return;
        }

        movableMode = MOVABLE_MODE.MOVE;

        if (e.cancelable) {
            e.preventDefault();
        }

        prepareMovableContainerMove(
            e,
            activeMovableContainer,
            localPoint,
            clientPoint
        );
    }

    handleMoveMovableContainerOnPointerMove(e, localPoint.x, localPoint.y);
}

let lastTextareaTapTime = 0;
let lastTextareaTapTarget = null;
const TEXTAREA_EDIT_DOUBLE_TAP_MS = 350;

async function handleMovableContainerPointerUp(e) {

    if (activePointerId !== null && e.pointerId !== activePointerId) {
        return;
    }

    try {
        if (isMoving) {
            await handleEndMoving(e, e.timeStamp - touchStartTimestamp);
            return;
        }

        if (movableMode === MOVABLE_MODE.PENDING) {

            currentSelectedMovableContainers = [];
            resetSelectedElements();
            selectMovableElement(activeMovableContainer);

            const now = e.timeStamp;
            const isDoubleTap =
                activePendingTextarea === lastTextareaTapTarget &&
                now - lastTextareaTapTime <= TEXTAREA_EDIT_DOUBLE_TAP_MS;

            if (isDoubleTap) {
                enterTextareaEditMode(activePendingTextarea, activeMovableContainer);
                lastTextareaTapTime = 0;
                lastTextareaTapTarget = null;
                return;
            }

            lastTextareaTapTime = now;
            lastTextareaTapTarget = activePendingTextarea;

            return;
        }

    } finally {
        resetMovablePointerState();
    }
}

function handleMovableContainerPointerCancel(e) {

    if (activePointerId !== null && e.pointerId !== activePointerId) {
        return;
    }

    handleCancelMoving();
    resetMovablePointerState();
}

function resetMovablePointerState() {
    movableMode = MOVABLE_MODE.SELECT;
    activeMovableContainer = null;
    activePointerId = null;
    activePendingTextarea = null;
    pressPointX = 0;
    pressPointY = 0;
}

function toggleMovableContainerSelection(movableContainer) {

    if (movableContainer.classList.contains('selectedMovableContainer')) {
        unselectMovableElement(movableContainer);
        return;
    }

    selectMovableElement(movableContainer);
}

function unselectMovableElement(movableContainer) {

    movableContainer.classList.remove('selectedMovableContainer');

    const menuContainer = movableContainer.querySelector('.menuContainer');
    if (menuContainer) {
        menuContainer.classList.remove('selectedMenuContainer');
    }

    currentSelectedMovableContainers = currentSelectedMovableContainers.filter((elm) => {
        return elm !== movableContainer;
    });
}

function setTextareaEditable(textarea, isEditable) {

    if (!(textarea instanceof HTMLTextAreaElement)) {
        return;
    }

    if (isEditable) {
        textarea.removeAttribute('readonly');
        return;
    }

    textarea.setAttribute('readonly', 'readonly');
}

function selectMovableElement(elm_targetContainer) {

    if (currentWiseToolbarButton === 'wiseVerticalToolbarCreateLinkButton') return;

    const elm_targetMenuContainer = elm_targetContainer.querySelector('.menuContainer');

    elm_targetContainer.classList.add('selectedMovableContainer');

    if (elm_targetMenuContainer) {
        elm_targetMenuContainer.classList.add('selectedMenuContainer');
    }

    const textarea = elm_targetContainer.querySelector('textarea');
    if (textarea) {
        setTextareaEditable(textarea, false);
    }

    if (!currentSelectedMovableContainers.includes(elm_targetContainer)) {
        currentSelectedMovableContainers.push(elm_targetContainer);
    }
}

function enterTextareaEditMode(textarea, movableContainer) {

    currentSelectedMovableContainers = [];
    resetSelectedElements();
    selectMovableElement(movableContainer);

    setTextareaEditable(textarea, true);

    movableMode = MOVABLE_MODE.EDIT;

    textarea.focus();
}




function handleMovableContainerEraserStart(e, movableContainer) {

    if (movableContainer.classList.contains('itemOfPhraseClauseContainer')) {
        return;
    }

    e.preventDefault();

    if (movableContainer.classList.contains('phraseClauseContainer')) {
        purgePhraseClauseContainer(movableContainer);
    }

    deleteElement(movableContainer);
    saveState(STATE_TITLE_DELETE_CONTAINER[intSelectedLanguage]);
}

document.addEventListener('pointerdown', handleMovableContainerPointerDown);
document.addEventListener('pointermove', handleMovableContainerPointerMove, { passive: false });
document.addEventListener('pointerup', handleMovableContainerPointerUp);
document.addEventListener('pointercancel', handleMovableContainerPointerCancel);

function handleCreateLayersClickableContainerStart(e) {

	const target = e.target.closest('#wiseBodyCreateLayers .clickableContainer');
	if (!target) {
		return;
	}

	if (!isEditingLayer) {
		return;
	}

	target.classList.toggle('selected');
}

document.addEventListener('pointerdown', handleCreateLayersClickableContainerStart, { passive: false });

function handleMenuContainerPointerStart(e) {

	const menuContainer = getClosestMenuContainer(e);
	if (menuContainer === null) {
		return;
	}

	if (shouldIgnoreMenuContainerPointerStart()) {
		return;
	}

	const menuContext = buildMenuContextFromMenuContainer(menuContainer);
	if (menuContext === null) {
		return;
	}

	hideAllContextMenuItems();

	const menuType = resolveMenuType(menuContext.classListValue);
	if (menuType === null) {
		return;
	}

	const menuConfig = buildMenuConfig(menuType, menuContext.targetContainerElm);
	if (menuConfig === null) {
		return;
	}

	showContextMenuItems(menuConfig.selectorName, menuConfig.doAllowInflection);

	saveContextMenuTargets(menuContext, menuConfig);

	updatePhraseClauseMenuText(menuContext.targetContainerElm);

	openContextMenu();

	positionContextMenu(menuConfig.boundsWiseBody, menuContext.targetContainerElm);

	updateContextMenuBounds();

	e.preventDefault();
    e.stopPropagation();
}

document.addEventListener('pointerdown', handleMenuContainerPointerStart);



function handleInnerContainerCompositionStart(e) {

	const target = e.target.closest('.innerContainerTextArea');
	if (!target) {
		return;
	}

	isComposing = true;
}

document.addEventListener('compositionstart', handleInnerContainerCompositionStart);


function handleInnerContainerCompositionEnd(e) {

	const target = e.target.closest('.innerContainerTextArea');
	if (!target) {
		return;
	}

	isComposing = false;
	saveState(STATE_TITLE_EDIT_TEXTAREA[intSelectedLanguage]);
}

document.addEventListener('compositionend', handleInnerContainerCompositionEnd);


function handleInnerContainerTextAreaInput(e) {

	const elm = e.target.closest('.innerContainerTextArea');
	if (!elm) {
		return;
	}

	const text = escapeHTML(elm.value);
	updateTextareaContainerSize(elm, text);

	if (!isComposing) {
		saveState(STATE_TITLE_EDIT_TEXTAREA[intSelectedLanguage]);
	}
}

document.addEventListener('input', handleInnerContainerTextAreaInput);


function handleUserInputDataTextAreaInput(e) {

	const elm = e.target.closest('.grammarInsightsDisplayAreaLiTextAreaUserInput, .wiseUpdateUserInputDataTextarea');
	if (!elm) {
		return;
	}

	const text = escapeHTML(elm.value);
	updateTextareaSize(elm, text);
}

document.addEventListener('input', handleUserInputDataTextAreaInput);





function getClosestMenuContainer(e) {
	return e.target.closest('.menuContainer');
}

function buildMenuConfig(menuType, targetContainerElm) {

	const boundsWiseBody = wisePanelWhiteboardViewMainContentArea.getBoundingClientRect();

	if (menuType === 'word') {
		const subClassificationId = escapeNumber(targetContainerElm.dataset.subClassificationId);
		const doAllowInflection = isAllowInflectionSubClassificationId(subClassificationId);

		return {
			selectorName: '.wiseContextLiWord',
			useDefaultValue: false,
			boundsWiseBody: boundsWiseBody,
			doAllowInflection: doAllowInflection
		};
	}

	if (menuType === 'stickyNote') {
		return {
			selectorName: '.wiseContextLiStickyNote',
			useDefaultValue: true,
			boundsWiseBody: boundsWiseBody,
			doAllowInflection: false
		};
	}

	if (menuType === 'textArea') {
		return {
			selectorName: '.wiseContextLiTextArea',
			useDefaultValue: true,
			boundsWiseBody: boundsWiseBody,
			doAllowInflection: false
		};
	}

	return null;
}

function isAllowInflectionSubClassificationId(subClassificationId) {

	const arr_allow_inflection = [
		POS_V1_KU, POS_V1_GU, POS_V1_SA, POS_V1_TA, POS_V1_NA,
		POS_V1_BA, POS_V1_MA, POS_V1_RA, POS_V1_WA, POS_V1_KU_ALT,
		POS_V1_RA_ALT, POS_V1_ARU, POS_V2_I, POS_V2_E,
		POS_V3_K, POS_V3_S, POS_V3_S_ALT, POS_V3_Z,
		POS_ADJ_I, POS_ADJ_I_ALT, POS_AUX_VERB_I, POS_AUX_VERB_NA
	];

	return arr_allow_inflection.includes(subClassificationId);
}



function buildMenuContextFromMenuContainer(menuContainer) {

	const classListValue = menuContainer.classList.value;
	const uniqueKey = escapeNumber(menuContainer.dataset.uniqueKey);

	const idNameTargetContainer = 'movableContainer' + uniqueKey;
	const idNameTargetBaseContainer = 'baseContainer' + uniqueKey;
	const idNameTargetInnerContainer = 'innerContainer' + uniqueKey;

	const targetContainerElm = document.getElementById(idNameTargetContainer);
	const targetBaseContainerElm = document.getElementById(idNameTargetBaseContainer);
	const targetInnerContainerElm = document.getElementById(idNameTargetInnerContainer);

	if (targetContainerElm === null || targetBaseContainerElm === null || targetInnerContainerElm === null) {
		return null;
	}

	return {
		classListValue: classListValue,
		uniqueKey: uniqueKey,
		idNameTargetContainer: idNameTargetContainer,
		idNameTargetBaseContainer: idNameTargetBaseContainer,
		idNameTargetInnerContainer: idNameTargetInnerContainer,
		targetContainerElm: targetContainerElm,
		targetBaseContainerElm: targetBaseContainerElm,
		targetInnerContainerElm: targetInnerContainerElm
	};
}

function hideAllContextMenuItems() {
	for (let i = INDEX_FIRST; i < wiseContextItems.length; i++) {
		wiseContextItems[i].style.display = 'none';
	}
}

function openContextMenu() {
	wiseContextMenu.classList.remove('hidden');
	isOpened_contextMenu = true;
}

function positionContextMenu(boundsWiseBody, targetContainerElm) {

	const bounds_targetContainer = targetContainerElm.getBoundingClientRect();
	const area_targetContainer_right = bounds_targetContainer.right;
	const area_targetContainer_top = bounds_targetContainer.top;

	const bounds_wiseContextMenu = wiseContextMenu.getBoundingClientRect();

	if (boundsWiseBody.width <= area_targetContainer_right + bounds_wiseContextMenu.width) {
		wiseContextMenu.style.left = (bounds_targetContainer.left - bounds_wiseContextMenu.width) + 'px';
	} else {
		wiseContextMenu.style.left = area_targetContainer_right + 'px';
	}

	if (boundsWiseBody.height <= area_targetContainer_top + bounds_wiseContextMenu.height) {
		wiseContextMenu.style.top = (boundsWiseBody.bottom - bounds_wiseContextMenu.height - 10) + 'px';
	} else {
		wiseContextMenu.style.top = area_targetContainer_top + 'px';
	}
}


function resolveMenuType(classListValue) {

	if (classListValue.indexOf('wordMenuContainer') !== -1) {
		return 'word';
	}

	if (classListValue.indexOf('stickyNoteMenuContainer') !== -1) {
		return 'stickyNote';
	}

	if (classListValue.indexOf('textAreaMenuContainer') !== -1) {
		return 'textArea';
	}

	return null;
}

function saveContextMenuTargets(menuContext, menuConfig) {

	const values = getContextMenuTargetValues(menuContext.targetContainerElm, menuConfig.useDefaultValue);

	contextMenuTargetContainer = menuContext.targetContainerElm;
	contextMenuTargetBaseContainer = menuContext.targetBaseContainerElm;
	contextMenuTargetInnerContainer = menuContext.targetInnerContainerElm;

	contextMenuTargetJapaneseId = values.japaneseId;
	contextMenuTargetJapaneseElementId = values.japaneseElementId;
	contextMenuTargetSubClassificationId = values.subClassificationId;
	contextMenuTargetFormId = values.formId;
	contextMenuTargetLabelId = values.labelId;
	contextMenuTargetVoiceId = values.voiceId;
	contextMenuTargetSubClassification = values.subClassificationText;

	contextMenuTargetContainerIdName = menuContext.idNameTargetContainer;
}

function getContextMenuTargetValues(targetContainerElm, useDefaultValue) {

	if (useDefaultValue) {
		return {
			japaneseId: DEFAULT_JAPANESE_ID,
			japaneseElementId: DEFAULT_JAPANESE_ELEMENT_ID,
			subClassificationId: DEFAULT_SUB_CLASSIFICATION_ID,
			formId: DEFAULT_FORM_ID,
			labelId: DEFAULT_LABEL_ID,
			voiceId: DEFAULT_VOICE_ID,
			subClassificationText: DEFAULT_SUB_CLASSIFICATION_TEXT
		};
	}

	return {
		japaneseId: escapeNumber(targetContainerElm.dataset.japaneseId),
		japaneseElementId: escapeNumber(targetContainerElm.dataset.japaneseElementId),
		subClassificationId: escapeNumber(targetContainerElm.dataset.subClassificationId),
		formId: escapeNumber(targetContainerElm.dataset.formId),
		labelId: escapeNumber(targetContainerElm.dataset.labelId),
		voiceId: escapeNumber(targetContainerElm.dataset.voiceId),
		subClassificationText: escapeHTML(targetContainerElm.dataset.subClassification)
	};
}


function shouldIgnoreMenuContainerPointerStart() {
	return currentInteractionMode === 'elementsEraserMode';
}

function showContextMenuItems(selectorName, doAllowInflection) {

	const elms_targetWiseContextLi = document.querySelectorAll(selectorName);

	for (let i = INDEX_FIRST; i < elms_targetWiseContextLi.length; i++) {
		const elm_targetWiseContextLi = elms_targetWiseContextLi[i];

		if (elm_targetWiseContextLi.id === 'wiseContextMenuInflection' && !doAllowInflection) {
			elm_targetWiseContextLi.style.display = 'none';
		} else {
			elm_targetWiseContextLi.style.display = 'block';
		}
	}
}

function updateContextMenuBounds() {

	const bounds = wiseContextMenu.getBoundingClientRect();

	contextMenuLeft = bounds.left;
	contextMenuRight = bounds.right;
	contextMenuTop = bounds.top;
	contextMenuBottom = bounds.bottom;
}

function updatePhraseClauseMenuText(targetContainerElm) {

	const elm = document.getElementById('wiseContextMenuCreatePhraseClause');
	if (elm === null) {
		return;
	}

	if (targetContainerElm.classList.contains('phraseClauseContainer')) {
		elm.textContent = MSG_CONTEXT_MENU_PHRASE_CLAUSE[INDEX_SECOND][intSelectedLanguage];
	} else {
		elm.textContent = MSG_CONTEXT_MENU_PHRASE_CLAUSE[INDEX_FIRST][intSelectedLanguage];
	}
}


function normalizeTargetContainers(targetContainers) {

    if (Array.isArray(targetContainers)) {
        return targetContainers.filter(targetContainer => targetContainer);
    }

    if (targetContainers) {
        return [targetContainers];
    }

    return [];
}

function getTextAreaValuesFromContainers(targetContainers) {

    const containers = normalizeTargetContainers(targetContainers);
    const values = [];

    for (const container of containers) {
        const elm_textarea = container.querySelector('textarea');

        if (elm_textarea === null) continue;
        if (elm_textarea.value.length === LENGTH_EMPTY) continue;

        values.push(elm_textarea.value);
    }

    return values;
}

async function addValueToGrammarExplanation(targetContainers) {

    if (isAddingGrammarExplanation) return false;
    if (grammarExplanationPanel.dataset.addable !== '1') return false;
    if (grammarExplanationHistory.length === LENGTH_EMPTY) return false;

    const textareaValues = getTextAreaValuesFromContainers(targetContainers);

    if (textareaValues.length === LENGTH_EMPTY) return false;

    const isConfirmed = window.confirm(MSG_UPLOAD_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) return false;

    isAddingGrammarExplanation = true;

    try {
        const int_grammarExplanationIndex_saved = currentGrammarExplanationIndex;

        const str_textareaValue = escapeHTML(textareaValues.join('\n\n'));
        const grammar_unique_code = escapeHTML(grammarExplanationHistory[currentGrammarExplanationIndex].grammarUniqueCode);
        const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';

        const payload = {
            str_textareaValue: str_textareaValue,
            grammar_unique_code: grammar_unique_code,
            room_unique_code: room_unique_code,
            int_selected_language: intSelectedLanguage
        };

        await postJson(
            roomUploadUserInputDataUrl,
            payload,
            10000
        );

        await recreateGrammarExplanation(grammar_unique_code, {
            indexToDisplay: int_grammarExplanationIndex_saved,
            suppressHide: false,
            useLoading: true
        });

        return true;

    } catch (error) {
        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error.message || 'Error');
        }

        return false;

    } finally {
        isAddingGrammarExplanation = false;
    }
}

function addValueToMemoPad(targetContainers) {

    const textareaValues = getTextAreaValuesFromContainers(targetContainers);

    if (textareaValues.length === LENGTH_EMPTY) return false;

    const str_textareaValue = escapeHTML(textareaValues.join('\n\n'));
    const memoId = Number(currentMemoState.memoId ?? 0);
    const lessonDateId = Number(currentMemoState.lessonDateId ?? 0);

    let elm_memoPad = null;

    if (memoId > 0) {
        elm_memoPad = memoPadTextareaContainer.querySelector(
            `textarea[data-memo-id="${memoId}"]`
        );
    }

    if (elm_memoPad === null && lessonDateId > 0) {
        elm_memoPad = memoPadTextareaContainer.querySelector(
            `textarea[data-memo-date-id="${lessonDateId}"]`
        );
    }

    if (elm_memoPad === null) return false;

    if (elm_memoPad.value.length > LENGTH_EMPTY) {
        elm_memoPad.value += `\n\n${str_textareaValue}`;
    } else {
        elm_memoPad.value = str_textareaValue;
    }

    elm_memoPad.dispatchEvent(new Event('input', { bubbles: true }));

    return true;
}

function highlightValidLinkTarget(clientPoint) {

	let arr_permission_link = checkLinkPermission(clientPoint);

	if (arr_permission_link.length === LENGTH_EMPTY) return;

	let uniqueKey_drawLine_end = arr_permission_link['uniqueKey_drawLine_end'],
		idName_linkContainer_end;

	if (linkMarkerSide === 'leftLinkMarker') {
		idName_linkContainer_end = 'rightLinkContainer' + uniqueKey_drawLine_end;
	}
	else {
		idName_linkContainer_end = 'leftLinkContainer' + uniqueKey_drawLine_end;
	}

	let elm_linkContainer_link_end = document.getElementById(idName_linkContainer_end);

	if (elm_linkContainer_link_end !== null) {
		elm_linkContainer_link_end.classList.add('linkContainer-selected');
	}
}


function createLink(clientPoint) {

	let arr_permission_link = checkLinkPermission(clientPoint);

	if (arr_permission_link.length === LENGTH_EMPTY) return false;

	let elm_movableContainer_leftLink = arr_permission_link['elm_movableContainer_leftLink'],
		elm_movableContainer_rightLink = arr_permission_link['elm_movableContainer_rightLink'],
		int_link_id = arr_permission_link['int_link_id'];

	elm_movableContainer_leftLink.dataset.linkId = int_link_id;

	if (currentInteractionMode === 'createPriorityLinkMode') {
		elm_movableContainer_leftLink.dataset.linkType = LINK_TYPE_PRIORITY_SEQUENCE;
	}
	else if (elm_movableContainer_rightLink.classList.contains('phraseClauseContainer')) {
		elm_movableContainer_leftLink.dataset.linkType = LINK_TYPE_TO_PHRASE_CLAUSE;
	}
	else {
		elm_movableContainer_leftLink.dataset.linkType = LINK_TYPE_NORMAL;
	}

	createLinkedCanvas(canvasLinkedContainer, elm_movableContainer_leftLink, wiseCanvasContainer);
	return true;
}


function checkLinkPermission(clientPoint) {

	let arr_permission_link = [];

	let elms_linkContainer = document.querySelectorAll('.linkContainer'),
		elm_linkContainer;

	for (let i = INDEX_FIRST; i < elms_linkContainer.length; i++) {
		elm_linkContainer = elms_linkContainer[i];
		elm_linkContainer.classList.remove('linkContainer-selected');
	}

	let uniqueKey_drawLine_end = detectLinkContainerCollision(clientPoint);

	if (uniqueKey_drawLine_end === '') return arr_permission_link;

	let idName_movableContainer_leftLink,
		idName_movableContainer_rightLink,
		elm_movableContainer_leftLink,
		elm_movableContainer_rightLink,
		int_link_id;

	if (linkMarkerSide === 'leftLinkMarker') {
		idName_movableContainer_leftLink = 'movableContainer' + uniqueKey_drawLine_end;
		idName_movableContainer_rightLink = 'movableContainer' + drawLineStartKey;
		int_link_id = drawLineStartKey;
	}
	else {
		idName_movableContainer_leftLink = 'movableContainer' + drawLineStartKey;
		idName_movableContainer_rightLink = 'movableContainer' + uniqueKey_drawLine_end;
		int_link_id = uniqueKey_drawLine_end;
	}

	elm_movableContainer_leftLink = document.getElementById(idName_movableContainer_leftLink);
	elm_movableContainer_rightLink = document.getElementById(idName_movableContainer_rightLink);

	if ('linkId' in elm_movableContainer_leftLink.dataset) {
		return arr_permission_link;
	}

	const int_link_id_leftLink = elm_movableContainer_leftLink.getAttribute('data-link-id'),
		uniquekey_leftLink = elm_movableContainer_leftLink.getAttribute('data-unique-key'),
		int_link_id_rightLink = elm_movableContainer_rightLink.getAttribute('data-link-id'),
		uniquekey_rightLink = elm_movableContainer_rightLink.getAttribute('data-unique-key');

	if (
		(int_link_id_leftLink === uniquekey_rightLink) ||
		(int_link_id_rightLink === uniquekey_leftLink)
	) {
		return arr_permission_link;
	}

	arr_permission_link = {
		elm_movableContainer_leftLink: elm_movableContainer_leftLink,
		elm_movableContainer_rightLink: elm_movableContainer_rightLink,
		int_link_id: int_link_id,
		uniqueKey_drawLine_end: uniqueKey_drawLine_end
	};

	return arr_permission_link;
}


function drawUnsettledLinkLine(point_move_x, point_move_y, elm) {

    const bounds_linkMarker_drawStart = elm.getBoundingClientRect();
    const bounds_drawingPoint_drawStart = calculateDrawingPoint(bounds_linkMarker_drawStart);

    const startPoint = convertClientPointToCanvasPoint(
        bounds_drawingPoint_drawStart.x,
        bounds_drawingPoint_drawStart.y,
        wiseCanvasOriginal
    );

    const endPoint = convertClientPointToCanvasPoint(
        point_move_x,
        point_move_y,
        wiseCanvasOriginal
    );

    wiseCanvasOriginalContext.clearRect(0, 0, wiseCanvasOriginal.width, wiseCanvasOriginal.height);
    wiseCanvasOriginalContext.beginPath();
    wiseCanvasOriginalContext.moveTo(startPoint.x, startPoint.y);
    wiseCanvasOriginalContext.lineTo(endPoint.x, endPoint.y);

    wiseCanvasOriginalContext.strokeStyle = 'green';
    wiseCanvasOriginalContext.lineWidth = 2;
    wiseCanvasOriginalContext.stroke();
}







function handleGlobalCanvasPointerMove(e) {

	if (!isDrawingLaserOnGlobalCanvas) {
		return;
	}

	const clientPoint = getClientPoint(e);
	const localPoint = getLocalPoint(clientPoint, globalCanvas);

	updateGlobalDrawingCursor(clientPoint.x, clientPoint.y);
	drawLaser(clientPoint.x, clientPoint.y, global_context_canvas_globalCanvas);
}

if(globalCanvas !== null){
	globalCanvas.addEventListener('mousemove', handleGlobalCanvasPointerMove);
	globalCanvas.addEventListener('touchmove', handleGlobalCanvasPointerMove, { passive: false });
}

function drawLaser(x, y, elm_context) {

    if (
        currentWiseToolbarButton === 'wiseVerticalToolbarLaserButton' ||
        currentGlobalToolbarButton === 'globalVerticalToolbarLaserButton'
    ) {

        if (laserLastPosition.x === null || laserLastPosition.y === null) {
            elm_context.moveTo(x, y);
        } else {
            elm_context.moveTo(laserLastPosition.x, laserLastPosition.y);
        }

        elm_context.lineTo(x, y);
        elm_context.stroke();

        laserLastPosition.x = x;
        laserLastPosition.y = y;
    }
}

function startLaserDrawing(x, y, elm_context) {

	if(
		currentWiseToolbarButton === 'wiseVerticalToolbarLaserButton' || 
		currentGlobalToolbarButton === 'globalVerticalToolbarLaserButton'
	)
	{
		elm_context.beginPath();
		elm_context.globalCompositeOperation = 'source-over';
		elm_context.lineWidth = 3;

		elm_context.lineCap = 'round';
		elm_context.strokeStyle = 'red';
		elm_context.globalAlpha = 0.2;

		drawLaser(x, y, elm_context);
	}
}

function endLaserDrawing(x, y, elm_canvas, elm_context, time) {

	if(
		currentWiseToolbarButton === 'wiseVerticalToolbarLaserButton' || 
		currentGlobalToolbarButton === 'globalVerticalToolbarLaserButton'
	)
	{
		drawLaser(x, y, elm_context);
		hideGlobalDrawingCursor();
		
		elm_context.globalAlpha = 1;
		elm_context.closePath();
		
		laserLastPosition.x = null;
		laserLastPosition.y = null;

		setTimeout(() => {
			elm_context.clearRect(0, 0, elm_canvas.width, elm_canvas.height);
		}, time);
	}
}

function removeDragOriginPreviewClones() {

    for (const clone of dragOriginPreviewClones) {
        if (clone) {
            clone.remove();
        }
    }

    dragOriginPreviewClones = [];
}

function removeDragCurrentPreviewClones() {

    for (const clone of dragCurrentPreviewClones) {
        if (clone) {
            clone.remove();
        }
    }

    dragCurrentPreviewClones = [];
}

function removeDragPreviewClones() {
    removeDragOriginPreviewClones();
    removeDragCurrentPreviewClones();
}

function getMovingTargetContainers() {

    if (
        Array.isArray(currentSelectedMovableContainers) &&
        currentSelectedMovableContainers.length > 0
    ) {
        return currentSelectedMovableContainers.filter(container => container);
    }

    if (movingMovableContainer) {
        return [movingMovableContainer];
    }

    return [];
}

function prepareMovableContainerMove(e, movableContainer, localPoint, clientPoint) {

    movingMovableContainer = movableContainer;

    const movingTargets = getMovingTargetContainers();

    const hasPanelDropTarget = movingTargets.some(target =>
        target.classList.contains('textAreaContainer') ||
        target.classList.contains('stickyNoteContainer')
    );

    if (hasPanelDropTarget && isWhiteboardPanelSplitMode()) {
        setWhiteboardDragScrollLock(true);
    }

    if (isOpened_contextMenu) {
        wiseContextMenu.classList.add('hidden');
        isOpened_contextMenu = false;
    }

    isMoving = true;

    const currentLeft = parseFloat(movableContainer.style.left) || 0;
    const currentTop = parseFloat(movableContainer.style.top) || 0;

    grabOffsetX = localPoint.x - currentLeft;
    grabOffsetY = localPoint.y - currentTop;

    lastMovePointX = localPoint.x;
    lastMovePointY = localPoint.y;

    lastMoveClientPointX = clientPoint.x;
    lastMoveClientPointY = clientPoint.y;

    moveStartPointX = localPoint.x;
    moveStartPointY = localPoint.y;

    dragStartScrollLeft = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollLeft : 0;
    dragStartScrollTop = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollTop : 0;

    for (const target of movingTargets) {
        target.dataset.dragStartLeft = target.style.left || '';
        target.dataset.dragStartTop = target.style.top || '';
    }

    movableContainerBoundsFromBody = movingMovableContainer.getBoundingClientRect();

    maxZIndex += 1;

    for (const target of movingTargets) {
        target.style.zIndex = maxZIndex;
    }

    if (hasPanelDropTarget) {
        cacheDragPreviewStartRects(movingTargets);
        createDragOriginPreviewClones(movingTargets);
        createDragCurrentPreviewClones(movingTargets);
    }
}


function createSingleDragPreviewClone(sourceElm, className, zIndex) {

    const contentClone = sourceElm.cloneNode(true);
    const rect = sourceElm.getBoundingClientRect();
    const zoomScale = getWiseZoomScale();

    if (contentClone.id) {
        contentClone.removeAttribute('id');
    }

    contentClone.querySelectorAll('[id]').forEach((elm) => {
        elm.removeAttribute('id');
    });

    contentClone.classList.remove(
        'selectedMovableContainer',
        'drag-source-hidden',
        'hidden'
    );

    const wrapper = document.createElement('div');

    wrapper.dataset.ignoreHistory = '1';
    wrapper.classList.add(className);

    wrapper.style.position = 'fixed';
    wrapper.style.left = rect.left + 'px';
    wrapper.style.top = rect.top + 'px';
    wrapper.style.width = rect.width + 'px';
    wrapper.style.height = rect.height + 'px';

    wrapper.style.boxSizing = 'border-box';
    wrapper.style.pointerEvents = 'none';
    wrapper.style.opacity = '0';
    wrapper.style.zIndex = zIndex;

    contentClone.style.position = 'absolute';
    contentClone.style.left = '0px';
    contentClone.style.top = '0px';

    contentClone.style.width = (rect.width / zoomScale) + 'px';
    contentClone.style.height = (rect.height / zoomScale) + 'px';

    contentClone.style.transformOrigin = 'top left';
    contentClone.style.transform = `scale(${zoomScale})`;
    contentClone.style.boxSizing = 'border-box';
    contentClone.style.zIndex = '1';

    const cloneBaseContainer = contentClone.querySelector('.baseContainer');
    const sourceBaseContainer = sourceElm.querySelector('.baseContainer');

    if (cloneBaseContainer && sourceBaseContainer) {
        const baseRect = sourceBaseContainer.getBoundingClientRect();

        cloneBaseContainer.style.width = (baseRect.width / zoomScale) + 'px';
        cloneBaseContainer.style.height = (baseRect.height / zoomScale) + 'px';
        cloneBaseContainer.style.boxSizing = 'border-box';
    }

    const cloneInnerTextarea = contentClone.querySelector('.innerContainerTextArea');
    const sourceInnerTextarea = sourceElm.querySelector('.innerContainerTextArea');

    if (cloneInnerTextarea && sourceInnerTextarea) {
        const textareaRect = sourceInnerTextarea.getBoundingClientRect();

        cloneInnerTextarea.style.width = (textareaRect.width / zoomScale) + 'px';
        cloneInnerTextarea.style.height = (textareaRect.height / zoomScale) + 'px';
        cloneInnerTextarea.style.boxSizing = 'border-box';
    }

    wrapper.appendChild(contentClone);

    return wrapper;
}

function cacheDragPreviewStartRects(targets) {

    targets = normalizeTargetContainers(targets);

    for (const target of targets) {
        const rect = target.getBoundingClientRect();

        target.dataset.dragPreviewStartClientLeft = rect.left;
        target.dataset.dragPreviewStartClientTop = rect.top;
        target.dataset.dragPreviewStartClientWidth = rect.width;
        target.dataset.dragPreviewStartClientHeight = rect.height;
    }
}

function clearDragPreviewStartRects(targets) {

    targets = normalizeTargetContainers(targets);

    for (const target of targets) {
        delete target.dataset.dragPreviewStartClientLeft;
        delete target.dataset.dragPreviewStartClientTop;
        delete target.dataset.dragPreviewStartClientWidth;
        delete target.dataset.dragPreviewStartClientHeight;
    }
}

function createDragOriginPreviewClones(targets) {

    removeDragOriginPreviewClones();

    targets = normalizeTargetContainers(targets);

    for (const target of targets) {
        const clone = createSingleDragPreviewClone(
            target,
            'drag-origin-preview-clone',
            '9998'
        );

        document.body.appendChild(clone);
        dragOriginPreviewClones.push(clone);
    }
}

function createDragCurrentPreviewClones(targets) {

    removeDragCurrentPreviewClones();

    targets = normalizeTargetContainers(targets);

    const host = wiseSuperOverlayBundle || wiseBaseLayerSuperOverlay || document.body;

    for (const target of targets) {
        const clone = createSingleDragPreviewClone(
            target,
            'drag-current-preview-clone',
            '9999'
        );

        host.appendChild(clone);
        dragCurrentPreviewClones.push(clone);
    }
}

function updateDragPreviewStateOnPointerMove(clientX, clientY, pointX, pointY) {

    const movingTargets = getMovingTargetContainers();

    if (movingTargets.length === 0) {
        return;
    }

    const dropPanel = detectDropPanel(clientX, clientY);
    currentDropPanel = dropPanel;

    updateDragCurrentPreviewPositionsByPointer(movingTargets, pointX, pointY);

    if (dropPanel === 'whiteboard') {
        for (const target of movingTargets) {
            target.classList.remove('drag-source-hidden');
            target.style.transform = '';
        }

        for (const clone of dragOriginPreviewClones) {
            clone.style.opacity = '0';
        }

        for (const clone of dragCurrentPreviewClones) {
            clone.style.opacity = '0';
            clone.classList.remove('can-drop', 'cannot-drop');
        }

        return;
    }

    for (const target of movingTargets) {
        target.classList.add('drag-source-hidden');
        target.style.transform = 'translateZ(0)';
    }

    for (const clone of dragOriginPreviewClones) {
        clone.style.opacity = '0.25';
    }

    for (const clone of dragCurrentPreviewClones) {
        clone.style.opacity = '0.9';
        clone.classList.remove('can-drop', 'cannot-drop');

        if (dropPanel === 'grammar' || dropPanel === 'memo') {
            clone.classList.add('can-drop');
        } else {
            clone.classList.add('cannot-drop');
        }
    }
}

function updateDragCurrentPreviewPositionsByPointer(targets, pointX, pointY) {

    targets = normalizeTargetContainers(targets);

    const currentScrollLeft = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollLeft : 0;
    const currentScrollTop = wisePanelWhiteboardBody instanceof HTMLElement ? wisePanelWhiteboardBody.scrollTop : 0;

    const scrollDiffX = currentScrollLeft - dragStartScrollLeft;
    const scrollDiffY = currentScrollTop - dragStartScrollTop;

    const distanceX = (pointX - moveStartPointX) + scrollDiffX;
    const distanceY = (pointY - moveStartPointY) + scrollDiffY;

    const zoomScale = getWiseZoomScale();

    for (let i = 0; i < targets.length; i++) {
        const target = targets[i];
        const clone = dragCurrentPreviewClones[i];

        if (!target || !clone) continue;

        const startLeft = parseFloat(target.dataset.dragPreviewStartClientLeft) || 0;
        const startTop = parseFloat(target.dataset.dragPreviewStartClientTop) || 0;
        const startWidth = parseFloat(target.dataset.dragPreviewStartClientWidth) || 0;
        const startHeight = parseFloat(target.dataset.dragPreviewStartClientHeight) || 0;

        clone.style.left = (startLeft + distanceX * zoomScale) + 'px';
        clone.style.top = (startTop + distanceY * zoomScale) + 'px';
        clone.style.width = startWidth + 'px';
        clone.style.height = startHeight + 'px';
    }
}

function detectDropPanel(pointX, pointY) {

    if (checkCollision(grammarExplanationPanel, pointX, pointY)) {
        if (grammarExplanationPanel.dataset.addable === '1') {
            return 'grammar';
        }
    }

    if (checkCollision(memoPadPanel, pointX, pointY)) {
        return 'memo';
    }

    if (checkCollision(whiteboardPanel, pointX, pointY)) {
        return 'whiteboard';
    }

    return 'outside';
}

function cleanupMovingState() {

    const movingTargets = getMovingTargetContainers();

    clearSelectedDragStartPositions();
    clearDragPreviewStartRects(movingTargets);
    removeDragPreviewClones();
    stopDragAutoScroll();
    setWhiteboardDragScrollLock(false);

    for (const target of movingTargets) {
        setMovableContainerSelectionLock(target, false);
        target.classList.remove('drag-source-hidden');
        target.style.transform = '';
    }

    dragStartScrollLeft = 0;
    dragStartScrollTop = 0;

    movingMovableContainer = null;
    isMoving = false;
}

function setMovableContainerSelectionLock(elm, isLocked) {

    if (!(elm instanceof HTMLElement)) {
        return;
    }

    if (isLocked) {
        elm.classList.add('wiseSelectionLock');
    } else {
        elm.classList.remove('wiseSelectionLock');
    }

    const textareas = elm.querySelectorAll('textarea');
    for (let i = 0; i < textareas.length; i++) {
        if (isLocked) {
            textareas[i].classList.add('wiseSelectionLock');
        } else {
            textareas[i].classList.remove('wiseSelectionLock');
        }
    }
}

function clearBrowserSelection() {

    const selection = window.getSelection();
    if (selection) {
        selection.removeAllRanges();
    }
}

function lockSelectionDuringPanelDragIfNeeded() {

    const movingTargets = getMovingTargetContainers();

    const panelDropTargets = movingTargets.filter(target =>
        target.classList.contains('textAreaContainer') ||
        target.classList.contains('stickyNoteContainer')
    );

    if (panelDropTargets.length === 0) {
        return;
    }

    clearBrowserSelection();

    for (const target of panelDropTargets) {
        if (!target.classList.contains('wiseSelectionLock')) {
            setMovableContainerSelectionLock(target, true);
        }
    }
}

function handleInnerContainerTextAreaFocusIn(e) {

    const textarea = e.target.closest('.innerContainerTextArea');
    if (!textarea) {
        return;
    }

    const movableContainer = textarea.closest('.movableContainer');
    if (!movableContainer) {
        return;
    }

    if (textarea.hasAttribute('readonly')) {
        textarea.blur();
        return;
    }

    movableContainer.classList.add('editingMovableContainer');
}

document.addEventListener('focusin', handleInnerContainerTextAreaFocusIn);

function handleInnerContainerTextAreaFocusOut(e) {

    const textarea = e.target.closest('.innerContainerTextArea');
    if (!textarea) {
        return;
    }

    const movableContainer = textarea.closest('.movableContainer');
    if (!movableContainer) {
        return;
    }

    movableContainer.classList.remove('editingMovableContainer');
}

document.addEventListener('focusout', handleInnerContainerTextAreaFocusOut);
