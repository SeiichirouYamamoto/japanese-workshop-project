const wisePanelPositionSelectCancelButton = document.getElementById('wisePanelPositionSelectCancelButton');

const WISE_PANEL_DEFAULT_RATIO = 0.5;
const WISE_PANEL_MIN_RATIO = 0.1;
const WISE_PANEL_MAX_RATIO = 0.9;

const wisePanelState = {
    layout: WISE_PANEL_LAYOUT.SINGLE,
    mainPanelId: WISE_TOOLBAR_PANEL_MAP.whiteboard,
    subPanelId: null,
    splitRatioX: WISE_PANEL_DEFAULT_RATIO,
    splitRatioY: WISE_PANEL_DEFAULT_RATIO
};

const wisePanelSplitterDragState = {
    isDragging: false,
    pointerId: null,
	element: null
};

function renderWisePanels() {
    const layout = document.getElementById('wisePanelContainerLayout');

    if (layout === null) {
        return;
    }

    layout.dataset.layout = wisePanelState.layout;

    const panels = layout.querySelectorAll('.wisePanel');
    panels.forEach((panel) => {
        panel.classList.remove('active');
        panel.classList.remove('wisePanelMain');
        panel.classList.remove('wisePanelSub');
        panel.style.flexBasis = '';
        panel.style.width = '';
        panel.style.height = '';
        panel.style.order = '';
    });

    let mainPanel = null;
    let subPanel = null;

    if (wisePanelState.mainPanelId !== null) {
        mainPanel = document.getElementById(wisePanelState.mainPanelId);
        if (mainPanel !== null) {
            mainPanel.classList.add('active');
            mainPanel.classList.add('wisePanelMain');
        }
    }

    if (wisePanelState.subPanelId !== null) {
        subPanel = document.getElementById(wisePanelState.subPanelId);
        if (subPanel !== null) {
            subPanel.classList.add('active');
            subPanel.classList.add('wisePanelSub');
        }
    }

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT && mainPanel && subPanel) {
        const mainRatio = wisePanelState.splitRatioX;
        const subRatio = 1 - mainRatio;

        mainPanel.style.order = '1';
        subPanel.style.order = '2';

        mainPanel.style.flexBasis = (mainRatio * 100) + '%';
        subPanel.style.flexBasis = (subRatio * 100) + '%';
    }

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_TOP_BOTTOM && mainPanel && subPanel) {
        const mainRatio = wisePanelState.splitRatioY;
        const subRatio = 1 - mainRatio;

        mainPanel.style.order = '1';
        subPanel.style.order = '2';

        mainPanel.style.flexBasis = (mainRatio * 100) + '%';
        subPanel.style.flexBasis = (subRatio * 100) + '%';
    }

    updateWisePanelSplitterPosition();
}

function setWisePanelSingle(panelId) {
    if (!panelId) {
        return;
    }

    applyWisePanelState({
        layout: WISE_PANEL_LAYOUT.SINGLE,
        mainPanelId: panelId,
        subPanelId: null,
        splitRatioX: wisePanelState.splitRatioX,
        splitRatioY: wisePanelState.splitRatioY
    });
}

function setWisePanelSplitLeftRight(mainPanelId, subPanelId, splitRatioX = WISE_PANEL_DEFAULT_RATIO) {
    if (!mainPanelId || !subPanelId) {
        return;
    }

    applyWisePanelState({
        layout: WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT,
        mainPanelId,
        subPanelId,
        splitRatioX,
        splitRatioY: wisePanelState.splitRatioY
    });
}

function setWisePanelSplitTopBottom(mainPanelId, subPanelId, splitRatioY = WISE_PANEL_DEFAULT_RATIO) {
    if (!mainPanelId || !subPanelId) {
        return;
    }

    applyWisePanelState({
        layout: WISE_PANEL_LAYOUT.SPLIT_TOP_BOTTOM,
        mainPanelId,
        subPanelId,
        splitRatioX: wisePanelState.splitRatioX,
        splitRatioY
    });
}

function handleWisePanelPositionSelectButtonClick(event) {

    const button = event.currentTarget;

    if (!(button instanceof HTMLElement)) {
        return;
    }

    const ui = document.getElementById('wisePanelPositionSelectOverlay');

    if (ui === null) {
        return;
    }

    const targetPanelId = ui.dataset.targetPanelId;
    const position = button.dataset.position;

    if (!targetPanelId || !position) {
        closeWisePanelPositionSelectUi();
        return;
    }

    executeWisePanelPositionSelect(targetPanelId, position);
    closeWisePanelPositionSelectUi();
}

function openWisePanelPositionSelectUi(panelId, isForce = false) {

    if (!panelId) {
        return;
    }

    const ui = document.getElementById('wisePanelPositionSelectOverlay');

    if (ui === null) {
        return;
    }

    switchPanelOverlaySharedContentsUiView(SHARED_CONTENTS_UI_VIEW.NONE);

    ui.dataset.targetPanelId = panelId;
    ui.classList.remove('hidden');

    if (wisePanelPositionSelectCancelButton !== null) {
        if (isForce === true) {
            wisePanelPositionSelectCancelButton.classList.add('hidden');
        } else {
            wisePanelPositionSelectCancelButton.classList.remove('hidden');
        }
    }
}

function closeWisePanelPositionSelectUi() {

    const ui = document.getElementById('wisePanelPositionSelectOverlay');

    if (ui === null) {
        return;
    }

    ui.dataset.targetPanelId = '';
    ui.classList.add('hidden');
}

function executeWisePanelPositionSelect(targetPanelId, position) {

    if (!targetPanelId || !position) {
        return;
    }

    const currentMain = wisePanelState.mainPanelId;
    const currentSub = wisePanelState.subPanelId;

    switch (position) {
        case WISE_PANEL_POSITION.FULL:
            setWisePanelSingle(targetPanelId);
            break;

        case WISE_PANEL_POSITION.LEFT: {
            const nextSubPanelId =
                currentSub && currentSub !== targetPanelId
                    ? currentSub
                    : currentMain && currentMain !== targetPanelId
                        ? currentMain
                        : null;

            if (nextSubPanelId) {
                setWisePanelSplitLeftRight(
                    targetPanelId,
                    nextSubPanelId,
                    wisePanelState.splitRatioX
                );
            }
            else {
                setWisePanelSingle(targetPanelId);
            }
            break;
        }

        case WISE_PANEL_POSITION.RIGHT: {
            const nextMainPanelId =
                currentMain && currentMain !== targetPanelId
                    ? currentMain
                    : currentSub && currentSub !== targetPanelId
                        ? currentSub
                        : null;

            if (nextMainPanelId) {
                setWisePanelSplitLeftRight(
                    nextMainPanelId,
                    targetPanelId,
                    wisePanelState.splitRatioX
                );
            }
            else {
                setWisePanelSingle(targetPanelId);
            }
            break;
        }

        case WISE_PANEL_POSITION.TOP: {
            const nextSubPanelId =
                currentSub && currentSub !== targetPanelId
                    ? currentSub
                    : currentMain && currentMain !== targetPanelId
                        ? currentMain
                        : null;

            if (nextSubPanelId) {
                setWisePanelSplitTopBottom(
                    targetPanelId,
                    nextSubPanelId,
                    wisePanelState.splitRatioY
                );
            }
            else {
                setWisePanelSingle(targetPanelId);
            }
            break;
        }

        case WISE_PANEL_POSITION.BOTTOM: {
            const nextMainPanelId =
                currentMain && currentMain !== targetPanelId
                    ? currentMain
                    : currentSub && currentSub !== targetPanelId
                        ? currentSub
                        : null;

            if (nextMainPanelId) {
                setWisePanelSplitTopBottom(
                    nextMainPanelId,
                    targetPanelId,
                    wisePanelState.splitRatioY
                );
            }
            else {
                setWisePanelSingle(targetPanelId);
            }
            break;
        }

        default:
            return;
    }
}

function normalizeWisePanelState(panelState) {

    if (!panelState) {
        return {
            layout: WISE_PANEL_LAYOUT.SINGLE,
            mainPanelId: null,
            subPanelId: null,
            splitRatioX: WISE_PANEL_DEFAULT_RATIO,
            splitRatioY: WISE_PANEL_DEFAULT_RATIO
        };
    }

    let {
        layout,
        mainPanelId,
        subPanelId,
        splitRatioX,
        splitRatioY
    } = panelState;

    if (!mainPanelId && subPanelId) {
        mainPanelId = subPanelId;
        subPanelId = null;
        layout = WISE_PANEL_LAYOUT.SINGLE;
    }

    if (mainPanelId === subPanelId) {
        subPanelId = null;
        layout = WISE_PANEL_LAYOUT.SINGLE;
    }

    if (!mainPanelId) {
        subPanelId = null;
        layout = WISE_PANEL_LAYOUT.SINGLE;
    }

    if (!subPanelId && layout !== WISE_PANEL_LAYOUT.SINGLE) {
        layout = WISE_PANEL_LAYOUT.SINGLE;
    }

    if (subPanelId && layout === WISE_PANEL_LAYOUT.SINGLE) {
        layout = WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT;
    }

    splitRatioX = clampWisePanelRatio(splitRatioX);
    splitRatioY = clampWisePanelRatio(splitRatioY);

    return {
        layout,
        mainPanelId,
        subPanelId,
        splitRatioX,
        splitRatioY
    };
}

function applyWisePanelState(nextState) {

    const normalizedState = normalizeWisePanelState(nextState);

    wisePanelState.layout = normalizedState.layout;
    wisePanelState.mainPanelId = normalizedState.mainPanelId;
    wisePanelState.subPanelId = normalizedState.subPanelId;
    wisePanelState.splitRatioX = normalizedState.splitRatioX;
    wisePanelState.splitRatioY = normalizedState.splitRatioY;

    renderWisePanels();
}

function bindWisePanelPositionSelectEvents() {

    const ui = document.getElementById('wisePanelPositionSelectOverlay');

    if (ui === null) {
        return;
    }

    const positionButtons = ui.querySelectorAll('.wisePanelPositionSelectOption');
    

    positionButtons.forEach((button) => {
        button.addEventListener('pointerup', handleWisePanelPositionSelectButtonClick, false);
    });

    if (wisePanelPositionSelectCancelButton !== null) {
        wisePanelPositionSelectCancelButton.addEventListener('pointerup', function () {
            closeWisePanelPositionSelectUi();
        }, false);
    }
}

function bindWiseRightToolbarEvents() {

    if (wiseRightToolbarContainer === null) {
        return;
    }

    const buttons = wiseRightToolbarContainer.querySelectorAll('[data-open-position-select="1"]');

    buttons.forEach((button) => {
        button.addEventListener('pointerup', function (e) {

			if (guardDisabledEvent(button, e)) {
				return;
			}

            const panelId = button.dataset.panelId;

            if (!panelId) {
                return;
            }

            openWisePanelPositionSelectUi(panelId, false);
			closeAllWiseExpandableToolbars();
            resetToSelectMode();

        }, false);
    });

	initWisePanelChart();
}

function clampWisePanelRatio(value) {

    const ratio = Number(value);

    if (!Number.isFinite(ratio)) {
        return WISE_PANEL_DEFAULT_RATIO;
    }

    if (ratio < WISE_PANEL_MIN_RATIO) {
        return WISE_PANEL_MIN_RATIO;
    }

    if (ratio > WISE_PANEL_MAX_RATIO) {
        return WISE_PANEL_MAX_RATIO;
    }

    return ratio;
}

function updateWisePanelSplitterPosition() {
    const layout = document.getElementById('wisePanelContainerLayout');
    const splitter = document.getElementById('wisePanelLayoutSplitter');

    if (layout === null || splitter === null) {
        return;
    }

    splitter.style.left = '';
    splitter.style.top = '';

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT) {
        splitter.dataset.direction = 'vertical';
        splitter.style.left = (wisePanelState.splitRatioX * 100) + '%';
        return;
    }

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_TOP_BOTTOM) {
        splitter.dataset.direction = 'horizontal';
        splitter.style.top = (wisePanelState.splitRatioY * 100) + '%';
        return;
    }

    splitter.dataset.direction = '';
}



function handleWisePanelSplitterPointerStart(targetElement, e) {
    const splitter = targetElement.closest('#wisePanelLayoutSplitter');
    const layout = document.getElementById('wisePanelContainerLayout');

    if (!(splitter instanceof HTMLElement) || layout === null) {
        return false;
    }

    if (
        wisePanelState.layout !== WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT &&
        wisePanelState.layout !== WISE_PANEL_LAYOUT.SPLIT_TOP_BOTTOM
    ) {
        return false;
    }

    wisePanelSplitterDragState.isDragging = true;
    wisePanelSplitterDragState.pointerId = e.pointerId ?? 'mouse-touch-fallback';
	wisePanelSplitterDragState.element = splitter;

    splitter.classList.add('wisePanelSplitterDragging');

    if (typeof splitter.setPointerCapture === 'function' && e.pointerId !== undefined) {
        splitter.setPointerCapture(e.pointerId);
    }

    e.preventDefault();
    return true;
}

function handleWisePanelSplitterPointerMove(e) {
    const layout = document.getElementById('wisePanelContainerLayout');

    if (layout === null) {
        return false;
    }

    if (!wisePanelSplitterDragState.isDragging) {
        return false;
    }

    if (e.pointerId !== undefined && wisePanelSplitterDragState.pointerId !== e.pointerId) {
        return false;
    }

    const clientPoint = getClientPoint(e);
    const rect = layout.getBoundingClientRect();

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_LEFT_RIGHT) {
        const offsetX = clientPoint.x - rect.left;
        wisePanelState.splitRatioX = clampWisePanelRatio(offsetX / rect.width);
        renderWisePanels();
        return true;
    }

    if (wisePanelState.layout === WISE_PANEL_LAYOUT.SPLIT_TOP_BOTTOM) {
        const offsetY = clientPoint.y - rect.top;
        wisePanelState.splitRatioY = clampWisePanelRatio(offsetY / rect.height);
        renderWisePanels();
        return true;
    }

    return false;
}

function handleWisePanelSplitterPointerEnd(e) {

    if (!wisePanelSplitterDragState.isDragging) {
        return false;
    }

    if (e.pointerId !== undefined && wisePanelSplitterDragState.pointerId !== e.pointerId) {
        return false;
    }

    const splitter = wisePanelSplitterDragState.element;

    if (
        splitter instanceof HTMLElement &&
        typeof splitter.hasPointerCapture === 'function' &&
        typeof splitter.releasePointerCapture === 'function' &&
        e.pointerId !== undefined &&
        splitter.hasPointerCapture(e.pointerId)
    ) {
        splitter.releasePointerCapture(e.pointerId);
    }

    if (splitter instanceof HTMLElement) {
        splitter.classList.remove('wisePanelSplitterDragging');
    }

    wisePanelSplitterDragState.isDragging = false;
    wisePanelSplitterDragState.pointerId = null;
    wisePanelSplitterDragState.element = null;

    return true;
}

function closeWisePanel(panelId) {
    if (!panelId) {
        return;
    }

	
    if (panelId === WISE_TOOLBAR_PANEL_MAP.grammarExplanation) {
        resetAppearanceLayoutState(appearanceLayoutState);
    }

    const defaultPanelId = WISE_TOOLBAR_PANEL_MAP.whiteboard;

    if (wisePanelState.mainPanelId === panelId) {
        if (wisePanelState.subPanelId) {
            setWisePanelSingle(wisePanelState.subPanelId);
            return;
        }

        setWisePanelSingle(defaultPanelId);
        return;
    }

    if (wisePanelState.subPanelId === panelId) {
        if (wisePanelState.mainPanelId) {
            setWisePanelSingle(wisePanelState.mainPanelId);
            return;
        }

        setWisePanelSingle(defaultPanelId);
        return;
    }

    setWisePanelSingle(defaultPanelId);
}

function bindWisePanelCloseEvents() {
    const closeButtons = document.querySelectorAll('.wisePanelViewCloseButton');

    closeButtons.forEach((button) => {
        button.addEventListener('pointerup', function () {
            const panel = button.closest('.wisePanel');

            if (!(panel instanceof HTMLElement)) {
                closeWisePanel(WISE_TOOLBAR_PANEL_MAP.whiteboard);
                return;
            }

            closeWisePanel(panel.id);
        }, false);
    });
}


function bindWiseResizeEvents() {

    if (sectionWise === null) return;

    const onResizeLikeChange = throttle(() => {

        if (shouldSkipRelayout()) {
            shouldBlurAfterRelayout = true;
            waitAndRelayout();
            return;
        }

        runRelayout();

    }, 150);

    window.addEventListener('resize', onResizeLikeChange, { passive: true });
}

function waitAndRelayout() {

    clearTimeout(resizeRelayoutTimer);

    resizeRelayoutTimer = setTimeout(() => {

        if (shouldSkipRelayout()) {
            waitAndRelayout();
            return;
        }

        runRelayout();

    }, 200);
}

function runRelayout() {
    prepareLayoutOnResize();
    initWiseLayout(false);

    if (shouldBlurAfterRelayout) {
        blurActiveElement();
        shouldBlurAfterRelayout = false;
    }
}

function blurActiveElement() {
    const activeElm = document.activeElement;

    if (activeElm && typeof activeElm.blur === 'function') {
        activeElm.blur();
    }
}

function bindWisePanelExpandEvents() {
    const expandButtons = document.querySelectorAll('.wisePanelViewExpandButton');

    expandButtons.forEach((button) => {
        button.addEventListener('pointerup', function () {

            const panel = button.closest('.wisePanel');

            // パネルが取れない場合は白板を全画面
            if (!(panel instanceof HTMLElement)) {
                setWisePanelSingle(WISE_TOOLBAR_PANEL_MAP.whiteboard);
                return;
            }

            // 対象パネルを全画面化
            setWisePanelSingle(panel.id);

        }, false);
    });
}

function bindWisePanelSplitEvents() {
    const splitButtons = document.querySelectorAll('.wisePanelViewSplitButton');

    splitButtons.forEach((button) => {
        button.addEventListener('pointerup', function () {

            if (wisePanelState.layout !== WISE_PANEL_LAYOUT.SINGLE) {
                return;
            }

            const panel = button.closest('.wisePanel');

            if (!(panel instanceof HTMLElement)) {
                return;
            }

            if (panel.id === WISE_TOOLBAR_PANEL_MAP.whiteboard) {
                return;
            }

            setWisePanelSplitLeftRight(
                WISE_TOOLBAR_PANEL_MAP.whiteboard,
                panel.id,
                wisePanelState.splitRatioX
            );

        }, false);
    });
}

const WISE_PANEL_SIDE_OPEN_CLASS = 'wisePanel-sideUiOpen';
const WISE_PANEL_BODY_SIDE_CLASS = 'wisePanelBody-sideMode';
const WISE_PANEL_UI_AREA_SIDE_CLASS = 'wisePanelUiArea-sideMode';

function openWisePanelUi(panel, ui) {

    if (!(ui instanceof HTMLElement) || !(panel instanceof HTMLElement)) {
        return;
    }

    const uiType = ui.dataset.panelUiType || 'float';
    const panelBody = panel.querySelector('.wisePanelBody');
    const panelUiArea = panel.querySelector('.wisePanelUiArea');

    ui.classList.remove('hidden');

    if (uiType === 'float') {
        return;
    }

    if (uiType === 'side') {
        const sideUis = panel.querySelectorAll('.wisePanelUi-side');

        sideUis.forEach((sideUi) => {
            if (sideUi !== ui) {
                sideUi.classList.add('hidden');
            }
        });

        panel.classList.add(WISE_PANEL_SIDE_OPEN_CLASS);

        if (panelBody instanceof HTMLElement) {
            panelBody.classList.add(WISE_PANEL_BODY_SIDE_CLASS);
        }

        if (panelUiArea instanceof HTMLElement) {
            panelUiArea.classList.add(WISE_PANEL_UI_AREA_SIDE_CLASS);
        }

        renderWisePanelInnerSplit(panel.id);
    }
}

function closeWisePanelUi(panel, mode = 'all', targetUi = null) {

    if (!(panel instanceof HTMLElement)) {
        return;
    }

    const panelBody = panel.querySelector('.wisePanelBody');
    const panelUiArea = panel.querySelector('.wisePanelUiArea');

    let targets = [];

    switch (mode) {
        case 'target':
            if (targetUi instanceof HTMLElement) {
                targets = [targetUi];
            }
            break;

        case 'side':
            targets = Array.from(panel.querySelectorAll('.wisePanelUi-side'));
            break;

        case 'float':
            targets = Array.from(panel.querySelectorAll('.wisePanelUi-float'));
            break;

        case 'overlay':
            targets = Array.from(panel.querySelectorAll('.wisePanelUi-overlay'));
            break;

        case 'all':
        default:
            targets = Array.from(panel.querySelectorAll('.wisePanelUi'));
            break;
    }

    targets.forEach((ui) => {
        ui.classList.add('hidden');
    });

    const visibleSideUi = panel.querySelector('.wisePanelUi-side:not(.hidden)');

    if (!visibleSideUi) {
        panel.classList.remove(WISE_PANEL_SIDE_OPEN_CLASS);

        if (panelBody instanceof HTMLElement) {
            panelBody.classList.remove(WISE_PANEL_BODY_SIDE_CLASS);
        }

        if (panelUiArea instanceof HTMLElement) {
            panelUiArea.classList.remove(WISE_PANEL_UI_AREA_SIDE_CLASS);
        }
    }

    renderWisePanelInnerSplit(panel.id);
}

const WISE_PANEL_INNER_DEFAULT_RATIO = 0.7;
const WISE_PANEL_INNER_MIN_RATIO = 0.2;
const WISE_PANEL_INNER_MAX_RATIO = 0.8;

const wisePanelInnerSplitState = {
    wisePanelLessonContents: {
        ratio: WISE_PANEL_INNER_DEFAULT_RATIO
    },
    wisePanelGrammarExplanation: {
        ratio: WISE_PANEL_INNER_DEFAULT_RATIO
    }
};

const wisePanelInnerSplitterDragState = {
    isDragging: false,
    pointerId: null,
    panelId: null,
    element: null
};

function clampWisePanelInnerRatio(value) {
    const ratio = Number(value);

    if (!Number.isFinite(ratio)) {
        return WISE_PANEL_INNER_DEFAULT_RATIO;
    }

    if (ratio < WISE_PANEL_INNER_MIN_RATIO) {
        return WISE_PANEL_INNER_MIN_RATIO;
    }

    if (ratio > WISE_PANEL_INNER_MAX_RATIO) {
        return WISE_PANEL_INNER_MAX_RATIO;
    }

    return ratio;
}

function getWisePanelInnerState(panelId) {
    if (!panelId || !wisePanelInnerSplitState[panelId]) {
        return null;
    }

    return wisePanelInnerSplitState[panelId];
}

function renderWisePanelInnerSplit(panelId) {
    const panel = document.getElementById(panelId);

    if (!(panel instanceof HTMLElement)) {
        return;
    }

    const panelBody = panel.querySelector('.wisePanelBody');
    const panelUiArea = panel.querySelector('.wisePanelUiArea');
    const innerState = getWisePanelInnerState(panelId);

    if (!(panelBody instanceof HTMLElement) || !(panelUiArea instanceof HTMLElement) || innerState === null) {
        return;
    }

    if (!panel.classList.contains(WISE_PANEL_SIDE_OPEN_CLASS)) {
        panelBody.style.flexBasis = '';
        panelUiArea.style.flexBasis = '';
        return;
    }

    const bodyRatio = clampWisePanelInnerRatio(innerState.ratio);
    const uiRatio = 1 - bodyRatio;

    panelBody.style.flexBasis = (bodyRatio * 100) + '%';
    panelUiArea.style.flexBasis = (uiRatio * 100) + '%';
}

function renderAllWisePanelInnerSplits() {
    Object.keys(wisePanelInnerSplitState).forEach((panelId) => {
        renderWisePanelInnerSplit(panelId);
    });
}

function getWisePanelIdFromViewHandle(target) {
    const handle = target.closest('#wisePanelLessonContentsViewHandle, #wisePanelGrammarExplanationViewHandle');

    if (!(handle instanceof HTMLElement)) {
        return null;
    }

    if (handle.id === 'wisePanelLessonContentsViewHandle') {
        return 'wisePanelLessonContents';
    }

    if (handle.id === 'wisePanelGrammarExplanationViewHandle') {
        return 'wisePanelGrammarExplanation';
    }

    return null;
}


function handleWisePanelViewHandlePointerStart(targetElement, e) {
    const handle = targetElement.closest('.wisePanelViewHandle');

    if (!(handle instanceof HTMLElement)) {
        return false;
    }

    const panelId = getWisePanelIdFromViewHandle(handle);

    if (!panelId) {
        return false;
    }

    const panel = document.getElementById(panelId);

    if (!(panel instanceof HTMLElement)) {
        return false;
    }

    if (!panel.classList.contains(WISE_PANEL_SIDE_OPEN_CLASS)) {
        return false;
    }

    wisePanelInnerSplitterDragState.isDragging = true;
    wisePanelInnerSplitterDragState.pointerId = e.pointerId ?? 'mouse-touch-fallback';
    wisePanelInnerSplitterDragState.panelId = panelId;
    wisePanelInnerSplitterDragState.element = handle;

    handle.classList.add('wisePanelViewHandleDragging');

    if (typeof handle.setPointerCapture === 'function' && e.pointerId !== undefined) {
        handle.setPointerCapture(e.pointerId);
    }

    e.preventDefault();
    return true;
}

function handleWisePanelViewHandlePointerMove(e) {
    if (!wisePanelInnerSplitterDragState.isDragging) {
        return false;
    }

    if (
        e.pointerId !== undefined &&
        wisePanelInnerSplitterDragState.pointerId !== e.pointerId
    ) {
        return false;
    }

    const panelId = wisePanelInnerSplitterDragState.panelId;
    const panel = document.getElementById(panelId);
    const innerState = getWisePanelInnerState(panelId);

    if (!(panel instanceof HTMLElement) || innerState === null) {
        return false;
    }

    const rect = panel.getBoundingClientRect();
    const clientPoint = getClientPoint(e);

    const ratioFromLeft = (clientPoint.x - rect.left) / rect.width;

    innerState.ratio = clampWisePanelInnerRatio(1 - ratioFromLeft);
    renderWisePanelInnerSplit(panelId);

    return true;
}

function handleWisePanelViewHandlePointerEnd(e) {
    if (!wisePanelInnerSplitterDragState.isDragging) {
        return false;
    }

    if (
        e.pointerId !== undefined &&
        wisePanelInnerSplitterDragState.pointerId !== e.pointerId
    ) {
        return false;
    }

    const handle = wisePanelInnerSplitterDragState.element;

    if (
        handle instanceof HTMLElement &&
        typeof handle.hasPointerCapture === 'function' &&
        typeof handle.releasePointerCapture === 'function' &&
        e.pointerId !== undefined &&
        handle.hasPointerCapture(e.pointerId)
    ) {
        handle.releasePointerCapture(e.pointerId);
    }

    if (handle instanceof HTMLElement) {
        handle.classList.remove('wisePanelViewHandleDragging');
    }

    wisePanelInnerSplitterDragState.isDragging = false;
    wisePanelInnerSplitterDragState.pointerId = null;
    wisePanelInnerSplitterDragState.panelId = null;
    wisePanelInnerSplitterDragState.element = null;

    return true;
}