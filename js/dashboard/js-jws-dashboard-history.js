const lessonWhiteboardOverlay = document.getElementById('lessonWhiteboardOverlay');
const lessonWhiteboardModalCanvasLinkedContainer = document.getElementById('lessonWhiteboardModalCanvasLinkedContainer');

async function handleShowLessonWhiteboardOverlay(btn) {

    const elmOverlay = lessonWhiteboardOverlay;
    const elmLoading = document.getElementById('lessonWhiteboardModalLoading');

    const elmTitle = document.getElementById('lessonWhiteboardModalTitle');
    const elmBoard = document.getElementById('lessonWhiteboardModalBoard');
    const elmCanvas = document.getElementById('lessonWhiteboardModalCanvas');
    const elmElementsLayer = document.getElementById('lessonWhiteboardModalElementsLayer');

    if (
        elmOverlay === null ||
        elmLoading === null ||
        elmBoard === null ||
        elmCanvas === null ||
        elmElementsLayer === null
    ) {
        return;
    }

	resizeWhiteboardModalTargets();

    const whiteboardIdRaw = String(btn?.dataset?.whiteboardId || '').trim();
    if (whiteboardIdRaw === '') {
        return;
    }

    elmOverlay.classList.add('overlay-on');

    // 初期化
    if (elmTitle) {
        elmTitle.textContent = '';
    }
    elmElementsLayer.innerHTML = '';
    const ctx = elmCanvas.getContext('2d');
    if (ctx) {
        ctx.clearRect(0, 0, elmCanvas.width, elmCanvas.height);
    }

    elmLoading.classList.remove('loading-hidden');

    try {

        const data = await loadWhiteboardDataById(Number(whiteboardIdRaw));
        const state = data.state;

        // title
        if (elmTitle) {
            elmTitle.textContent = 'whiteboard';
        }

        // canvasMeta
        const width = Number(state?.canvasMeta?.width ?? BOARD_WIDTH);
        const height = Number(state?.canvasMeta?.height ?? BOARD_HEIGHT);

        elmCanvas.width = width;
        elmCanvas.height = height;

        // board / elements layer size
        elmBoard.style.width = `${width}px`;
        elmBoard.style.height = `${height}px`;
        elmElementsLayer.style.width = `${width}px`;
        elmElementsLayer.style.height = `${height}px`;

        // movableElementsSnapshot 復元
        renderMovableElementsSnapshot(elmElementsLayer, state?.movableElementsSnapshot);

        // strokeHistory 再描画
        redrawStrokeHistory(elmCanvas, state?.strokeHistory);

		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				buildLessonModalLinkLines();
			});
		});

    } finally {

        elmLoading.classList.add('loading-hidden');
    }
}

function buildElementFromSnapshot(node) {
    if (!node || !node.tag) {
        return null;
    }

    const el = document.createElement(node.tag);

    if (node.id) {
        el.id = node.id;
    }
    if (node.class) {
        el.className = node.class;
    }
    if (node.style) {
        el.setAttribute('style', node.style);
    }

    if (node.data && typeof node.data === 'object') {
        for (const [key, value] of Object.entries(node.data)) {
            if (value === null || value === undefined) {
                continue;
            }
            el.setAttribute(key, String(value));
        }
    }

    if (node.tag === 'TEXTAREA') {
        el.value = node.textContent ?? '';
    } else if (typeof node.textContent === 'string' && node.textContent.length > 0) {
        el.textContent = node.textContent;
    }

    if (Array.isArray(node.children)) {
        for (const child of node.children) {
            const childEl = buildElementFromSnapshot(child);
            if (childEl) {
                el.appendChild(childEl);
            }
        }
    }

    return el;
}

// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function renderMovableElementsSnapshot(containerEl, snapshotList) {
    containerEl.innerHTML = '';

    if (!Array.isArray(snapshotList)) {
        return;
    }

    const restored = [];

    for (const node of snapshotList) {
        const el = buildElementFromSnapshot(node);
        if (!el) {
            continue;
        }

        containerEl.appendChild(el);
        restored.push(el);
    }

    requestAnimationFrame(() => {
        restored.forEach((el) => {
            resizeMovableContainerByTextOwner(el);
        });
    });
}


function resizeMovableContainerByTextOwner(rootEl) {
    if (!(rootEl instanceof HTMLElement)) {
        return;
    }

    const textOwner = findTextOwner(rootEl);
    if (!(textOwner instanceof HTMLElement)) {
        return;
    }

    resizeTextOwner(textOwner);
    resizeParentsFromTextOwner(rootEl, textOwner);
}


function findTextOwner(rootEl) {
    if (!(rootEl instanceof HTMLElement)) {
        return null;
    }

    return rootEl.querySelector('[data-text-owner="1"], textarea, input, [contenteditable="true"]');
}


function resizeTextOwner(el) {
    if (el instanceof HTMLTextAreaElement) {
        resizeTextarea(el);
        return;
    }

    if (el instanceof HTMLInputElement) {
        resizeByMirror(el, el.value || ' ');
        return;
    }

    resizeByMirror(el, el.textContent || ' ');
}


function resizeTextarea(textarea) {
    resizeByMirror(textarea, textarea.value || ' ');
}


function resizeByMirror(el, text) {
    if (!(el instanceof HTMLElement)) {
        return;
    }

    const style = window.getComputedStyle(el);
    const mirror = document.createElement('div');

    mirror.style.position = 'absolute';
    mirror.style.left = '-99999px';
    mirror.style.top = '-99999px';
    mirror.style.visibility = 'hidden';
    mirror.style.pointerEvents = 'none';
    mirror.style.whiteSpace = 'pre';
    mirror.style.display = 'inline-block';

    mirror.style.font = style.font;
    mirror.style.letterSpacing = style.letterSpacing;
    mirror.style.wordSpacing = style.wordSpacing;
    mirror.style.lineHeight = style.lineHeight;
    mirror.style.padding = style.padding;
    mirror.style.border = style.border;
    mirror.style.boxSizing = style.boxSizing;

    mirror.textContent = text.endsWith('\n') ? text + ' ' : text;

    document.body.appendChild(mirror);

    const rect = mirror.getBoundingClientRect();
    const fontSize = parseFloat(style.fontSize) || 16;

    el.style.width = Math.ceil(rect.width + Math.max(2, fontSize * 0.2)) + 'px';
    el.style.height = Math.ceil(rect.height + Math.max(2, fontSize * 0.1)) + 'px';

    document.body.removeChild(mirror);
}


function resizeParentsFromTextOwner(rootEl, textOwner) {
    const inner = textOwner.closest('.innerContainer');
    const base = textOwner.closest('.baseContainer');
    const leftLink = rootEl.querySelector('.leftLinkContainer');
    const rightLink = rootEl.querySelector('.rightLinkContainer');

    let width = Math.ceil(textOwner.offsetWidth);
    let height = Math.ceil(textOwner.offsetHeight);

    if (inner instanceof HTMLElement) {
        inner.style.width = width + 'px';
        inner.style.height = height + 'px';
        width = inner.offsetWidth;
        height = inner.offsetHeight;
    }

    if (base instanceof HTMLElement) {
        base.style.width = width + 'px';
        base.style.height = height + 'px';
        width = base.offsetWidth;
        height = base.offsetHeight;
    }

    if (leftLink instanceof HTMLElement) {
        leftLink.style.width = Math.ceil(width / 2) + 'px';
        leftLink.style.height = height + 'px';
    }

    if (rightLink instanceof HTMLElement) {
        rightLink.style.width = Math.ceil(width / 2) + 'px';
        rightLink.style.height = height + 'px';
    }

    rootEl.style.width = width + 'px';
    rootEl.style.height = height + 'px';
}
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


function redrawStrokeHistory(canvasEl, strokeHistory) {
    const ctx = canvasEl.getContext('2d');
    if (!ctx) {
        return;
    }

    // 念のため初期化
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.globalCompositeOperation = 'source-over';
    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);

    if (!Array.isArray(strokeHistory)) {
        return;
    }

    for (const stroke of strokeHistory) {
        const points = Array.isArray(stroke.points) ? stroke.points : [];
        if (points.length < 2) {
            continue;
        }

        const tool = String(stroke.tool ?? 'pen');

        ctx.save();
        ctx.lineWidth = Number(stroke.size ?? 1);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (tool === 'eraser') {
            ctx.globalCompositeOperation = 'destination-out';
            // 消しゴムは色不要だが、ブラウザ差異回避で不透明色を入れておくと安全です
            ctx.strokeStyle = '#000000';
        } else {
            ctx.globalCompositeOperation = 'source-over';
            ctx.strokeStyle = String(stroke.color ?? 'black');
        }

        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for (let i = 1; i < points.length; i++) {
            ctx.lineTo(points[i].x, points[i].y);
        }
        ctx.stroke();
        ctx.restore();
    }
}


function buildLessonModalLinkLines() {
    const elms = document.querySelectorAll('#lessonWhiteboardModalElementsLayer .movableContainer');

    for (const elm of elms) {
        if (elm.hasAttribute('data-link-id')) {
            createLinkedCanvas(lessonWhiteboardModalCanvasLinkedContainer, elm, lessonWhiteboardModalCanvasLinkedContainer);
        }
    }

    redrawLinkLines();
}

clickHandlers['lesson_whiteboard:show_lesson_whiteboard_overlay'] = function (btn) {

    if (lessonWhiteboardOverlay === null) {
        return;
    }

    lessonWhiteboardOverlay.classList.add('overlay-on');

    handleShowLessonWhiteboardOverlay(btn);
};


function resizeWhiteboardModalTargets()
{
    const targets = document.querySelectorAll('.whiteboardModalResizeTarget');

    targets.forEach((elm) => {

        // divなど表示サイズ
        if (elm.tagName !== 'CANVAS') {
            elm.style.width = BOARD_WIDTH + 'px';
            elm.style.height = BOARD_HEIGHT + 'px';
            return;
        }

        // canvasは内部座標も同期（最重要）
        elm.style.width = BOARD_WIDTH + 'px';
        elm.style.height = BOARD_HEIGHT + 'px';
        elm.width = BOARD_WIDTH;
        elm.height = BOARD_HEIGHT;
    });
}