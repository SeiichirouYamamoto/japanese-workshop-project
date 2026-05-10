const WISE_PANEL_PLACEMENT_DRAG_START_DISTANCE = 0;

const wisePanelPlacementDragState = {
    isPointerDown: false,
    isDragging: false,
    pointerId: null,
    panelId: null,
    button: null,
    startX: 0,
    startY: 0
};

let wisePanelPlacementDragClone = null;
let wisePanelPlacementPreviewOverlay = null;
let wisePanelPlacementPreviewPosition = null;

const WISE_PANEL_PLACEMENT_TOP_BOTTOM_RATIO = 0.22;
const WISE_PANEL_PLACEMENT_CENTER_RATIO = 0.34;

function handleWisePanelPlacementPointerDown(e) {

    const button = e.target.closest('.wiseRightVerticalToolbarButton');

    if (!(button instanceof HTMLElement)) {
        return;
    }

    if (button.dataset.openPositionSelect !== '1') {
        return;
    }

    if (guardDisabledEvent(button, e)) {
        return;
    }

    const panelId = button.dataset.panelId;

    if (!panelId) {
        return;
    }

    const clientPoint = getClientPoint(e);

    wisePanelPlacementDragState.isPointerDown = true;
    wisePanelPlacementDragState.isDragging = false;
    wisePanelPlacementDragState.pointerId = e.pointerId;
    wisePanelPlacementDragState.panelId = panelId;
    wisePanelPlacementDragState.button = button;
    wisePanelPlacementDragState.startX = clientPoint.x;
    wisePanelPlacementDragState.startY = clientPoint.y;

    if (typeof button.setPointerCapture === 'function') {
        try {
            button.setPointerCapture(e.pointerId);
        } catch (error) {}
    }

    if (e.cancelable) {
        e.preventDefault();
    }
}

function handleWisePanelPlacementPointerMove(e) {

    if (!wisePanelPlacementDragState.isPointerDown) {
        return;
    }

    if (e.pointerId !== wisePanelPlacementDragState.pointerId) {
        return;
    }

    if (e.cancelable) {
        e.preventDefault();
    }

    e.stopImmediatePropagation();

    const clientPoint = getClientPoint(e);

    const distance = calculateMovableContainerDistance(
        wisePanelPlacementDragState.startX,
        wisePanelPlacementDragState.startY,
        clientPoint.x,
        clientPoint.y
    );

    if (distance < WISE_PANEL_PLACEMENT_DRAG_START_DISTANCE) {
        return;
    }

    if (!wisePanelPlacementDragState.isDragging) {
        startWisePanelPlacementDragUi(clientPoint.x, clientPoint.y);
    }

    wisePanelPlacementDragState.isDragging = true;

    moveWisePanelPlacementDragClone(clientPoint.x, clientPoint.y);

    const position = detectWisePanelPlacementPosition(clientPoint.x, clientPoint.y);
    updateWisePanelPlacementPreview(position);
}

function handleWisePanelPlacementPointerUp(e) {

    if (!wisePanelPlacementDragState.isPointerDown) {
        return;
    }

    if (e.pointerId !== wisePanelPlacementDragState.pointerId) {
        return;
    }

    releaseWisePanelPlacementPointerCapture(e);

    const wasDragging = wisePanelPlacementDragState.isDragging;
    const panelId = wisePanelPlacementDragState.panelId;
    const sourceButton = wisePanelPlacementDragState.button;
    const clientPoint = getClientPoint(e);
    const position = detectWisePanelPlacementPosition(clientPoint.x, clientPoint.y);

    cleanupWisePanelPlacementDrag();

    if (!wasDragging) {
        return;
    }

    if (e.cancelable) {
        e.preventDefault();
    }

    e.stopImmediatePropagation();

    if (position === null) {
        return;
    }

    executeWisePanelPositionSelect(panelId, position);

    if (sourceButton instanceof HTMLElement) {
        updateWiseToolbarCurrentButton(sourceButton);
    }

    closeWisePanelPositionSelectUi();
    closeAllWiseExpandableToolbars();
    resetToSelectMode();
}

function handleWisePanelPlacementPointerCancel(e) {

    if (!wisePanelPlacementDragState.isPointerDown) {
        return;
    }

    if (
        e.pointerId !== undefined &&
        e.pointerId !== wisePanelPlacementDragState.pointerId
    ) {
        return;
    }

    releaseWisePanelPlacementPointerCapture(e);
    cleanupWisePanelPlacementDrag();
}

function releaseWisePanelPlacementPointerCapture(e) {

    const button = wisePanelPlacementDragState.button;

    if (
        button instanceof HTMLElement &&
        typeof button.hasPointerCapture === 'function' &&
        typeof button.releasePointerCapture === 'function' &&
        e.pointerId !== undefined &&
        button.hasPointerCapture(e.pointerId)
    ) {
        button.releasePointerCapture(e.pointerId);
    }
}

function startWisePanelPlacementDragUi(pointX, pointY) {

    const button = wisePanelPlacementDragState.button;

    if (!(button instanceof HTMLElement)) {
        return;
    }

    button.classList.add('wisePanelPlacementDraggingSource');

    createWisePanelPlacementDragClone(button, pointX, pointY);
    createWisePanelPlacementPreviewOverlay();
}

function createWisePanelPlacementDragClone(button, pointX, pointY) {

    removeWisePanelPlacementDragClone();

    const clone = button.cloneNode(true);

    clone.classList.add('wisePanelPlacementDragClone');

    document.body.appendChild(clone);

    wisePanelPlacementDragClone = clone;

    moveWisePanelPlacementDragClone(pointX, pointY);
}

function moveWisePanelPlacementDragClone(pointX, pointY) {

    if (!(wisePanelPlacementDragClone instanceof HTMLElement)) {
        return;
    }

    wisePanelPlacementDragClone.style.transform =
        `translate3d(${pointX}px, ${pointY}px, 0) translate(-50%, -50%) scale(1.08)`;
}

function createWisePanelPlacementPreviewOverlay() {

    removeWisePanelPlacementPreviewOverlay();

    const layout = document.getElementById('wisePanelContainerLayout');

    if (!(layout instanceof HTMLElement)) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.classList.add('wisePanelPlacementPreviewOverlay');

    overlay.innerHTML = `
        <div class="wisePanelPlacementPreviewArea wisePanelPlacementPreviewAreaTop" data-position="${WISE_PANEL_POSITION.TOP}"></div>
        <div class="wisePanelPlacementPreviewArea wisePanelPlacementPreviewAreaBottom" data-position="${WISE_PANEL_POSITION.BOTTOM}"></div>
        <div class="wisePanelPlacementPreviewArea wisePanelPlacementPreviewAreaLeft" data-position="${WISE_PANEL_POSITION.LEFT}"></div>
        <div class="wisePanelPlacementPreviewArea wisePanelPlacementPreviewAreaRight" data-position="${WISE_PANEL_POSITION.RIGHT}"></div>
        <div class="wisePanelPlacementPreviewArea wisePanelPlacementPreviewAreaFull" data-position="${WISE_PANEL_POSITION.FULL}"></div>
    `;

    layout.appendChild(overlay);

    wisePanelPlacementPreviewOverlay = overlay;
}

function updateWisePanelPlacementPreview(position) {

    if (!(wisePanelPlacementPreviewOverlay instanceof HTMLElement)) {
        return;
    }

    if (wisePanelPlacementPreviewPosition === position) {
        return;
    }

    wisePanelPlacementPreviewPosition = position;

    const areas = wisePanelPlacementPreviewOverlay.querySelectorAll('.wisePanelPlacementPreviewArea');

    areas.forEach((area) => {
        area.classList.remove('active');
    });

    if (position === null) {
        return;
    }

    const target = wisePanelPlacementPreviewOverlay.querySelector(
        `.wisePanelPlacementPreviewArea[data-position="${position}"]`
    );

    if (target instanceof HTMLElement) {
        target.classList.add('active');
    }
}

function detectWisePanelPlacementPosition(pointX, pointY) {

    const layout = document.getElementById('wisePanelContainerLayout');

    if (!(layout instanceof HTMLElement)) {
        return null;
    }

    const rect = layout.getBoundingClientRect();

    const relativeX = pointX - rect.left;
    const relativeY = pointY - rect.top;

    const width = rect.width;
    const height = rect.height;

    if (
        relativeX < 0 ||
        relativeY < 0 ||
        relativeX > width ||
        relativeY > height
    ) {
        return null;
    }

    const topBottomHeight = height * 0.22;

    const centerWidth = width * 0.34;
    const centerHeight = height * 0.34;

    const centerLeft = (width - centerWidth) / 2;
    const centerRight = centerLeft + centerWidth;
    const centerTop = (height - centerHeight) / 2;
    const centerBottom = centerTop + centerHeight;

    if (
        relativeX >= centerLeft &&
        relativeX <= centerRight &&
        relativeY >= centerTop &&
        relativeY <= centerBottom
    ) {
        return WISE_PANEL_POSITION.FULL;
    }

    if (relativeY < topBottomHeight) {
        return WISE_PANEL_POSITION.TOP;
    }

    if (relativeY > height - topBottomHeight) {
        return WISE_PANEL_POSITION.BOTTOM;
    }

    if (relativeX < width / 2) {
        return WISE_PANEL_POSITION.LEFT;
    }

    return WISE_PANEL_POSITION.RIGHT;
}

function cleanupWisePanelPlacementDrag() {

    if (wisePanelPlacementDragState.button instanceof HTMLElement) {
        wisePanelPlacementDragState.button.classList.remove('wisePanelPlacementDraggingSource');
    }

    removeWisePanelPlacementDragClone();
    removeWisePanelPlacementPreviewOverlay();
    resetWisePanelPlacementDragState();
}

function removeWisePanelPlacementDragClone() {

    if (wisePanelPlacementDragClone instanceof HTMLElement) {
        wisePanelPlacementDragClone.remove();
    }

    wisePanelPlacementDragClone = null;
}

function removeWisePanelPlacementPreviewOverlay() {

    if (wisePanelPlacementPreviewOverlay instanceof HTMLElement) {
        wisePanelPlacementPreviewOverlay.remove();
    }

    wisePanelPlacementPreviewOverlay = null;
    wisePanelPlacementPreviewPosition = null;
}

function resetWisePanelPlacementDragState() {

    wisePanelPlacementDragState.isPointerDown = false;
    wisePanelPlacementDragState.isDragging = false;
    wisePanelPlacementDragState.pointerId = null;
    wisePanelPlacementDragState.panelId = null;
    wisePanelPlacementDragState.button = null;
    wisePanelPlacementDragState.startX = 0;
    wisePanelPlacementDragState.startY = 0;
}

document.addEventListener('pointerdown', handleWisePanelPlacementPointerDown, true);
document.addEventListener('pointermove', handleWisePanelPlacementPointerMove, { passive: false, capture: true });
document.addEventListener('pointerup', handleWisePanelPlacementPointerUp, true);
document.addEventListener('pointercancel', handleWisePanelPlacementPointerCancel, true);