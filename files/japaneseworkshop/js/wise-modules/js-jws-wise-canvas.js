

if(wiseCanvasOriginal !== null){
	wiseCanvasOriginal.addEventListener('touchmove', (e) => {
		if (
			currentInteractionMode === 'drawingMode' ||
			currentInteractionMode === 'eraserMode' ||
			currentInteractionMode === 'laserMode'
		) {
			preventDefaultOnTouchMove(e);
		}
	}, { passive: false });
}

if(wiseCanvasHandWriting!==null)
{
	wiseCanvasOriginalContext = wiseCanvasOriginal.getContext('2d',{
		willReadFrequently: true
	});
	wiseCanvasHandWritingContext = wiseCanvasHandWriting.getContext('2d',{
		willReadFrequently: true
	});

	let elm_penCursorSpan = document.createElement('span');
	elm_penCursorSpan.setAttribute('id', 'penCursorSpan');
	elm_penCursorSpan.className = 'wiseDecorativeItem';
	elm_penCursorSpan.textContent = '';
	elm_penCursorSpan.style.left = '0px';
	elm_penCursorSpan.style.top = '0px';
	elm_penCursorSpan.style.visibility = 'hidden';
	wiseCanvasContainer.prepend(elm_penCursorSpan);

	let elm_eraserSpan = document.createElement('span');
	elm_eraserSpan.setAttribute('id', 'eraserSpan');
	elm_eraserSpan.textContent = '';
	elm_eraserSpan.style.left = '0px';
	elm_eraserSpan.style.width = currentEraserSize+'px';
	elm_eraserSpan.style.height = currentEraserSize+'px';
	elm_eraserSpan.style.overflow = 'hidden';
	elm_eraserSpan.style.visibility = 'hidden';
	wiseCanvasContainer.prepend(elm_eraserSpan);

	wiseCanvasHandWriting.addEventListener('mousedown', (e) => {
		const clientPoint = getClientPoint(e);
		const localPoint = getLocalPoint(clientPoint, wiseCanvasHandWriting);

		startHandwriting(e, localPoint.x, localPoint.y);
		updateDrawingCursor(localPoint.x, localPoint.y);
		drawHandwriting(e, localPoint.x, localPoint.y);
	});

	wiseCanvasHandWriting.addEventListener('mouseup', endHandwriting);
	wiseCanvasHandWriting.addEventListener('mouseout', endHandwriting);

	wiseCanvasHandWriting.addEventListener('mousemove', (e) => {
		if (currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode') {
			return;
		}

		const clientPoint = getClientPoint(e);
		const localPoint = getLocalPoint(clientPoint, wiseCanvasHandWriting);

		updateDrawingCursor(localPoint.x, localPoint.y);
		drawHandwriting(e, localPoint.x, localPoint.y);
	});

	wiseCanvasHandWriting.addEventListener('touchstart', (e) => {
		if (sortingQuizFullScreenSection === null && sectionCreateLayers === null) {
			preventDefaultOnTouchMove(e);
		}

		const clientPoint = getClientPoint(e);
		const localPoint = getLocalPoint(clientPoint, wiseCanvasHandWriting);

		startHandwriting(e, localPoint.x, localPoint.y);
		updateDrawingCursor(localPoint.x, localPoint.y);
		drawHandwriting(e, localPoint.x, localPoint.y);
	}, { passive: false });

	wiseCanvasHandWriting.addEventListener('touchcancel', endHandwriting);
	wiseCanvasHandWriting.addEventListener('touchend', endHandwriting);

	wiseCanvasHandWriting.addEventListener('touchmove', (e) => {
		if (currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode') {
			return;
		}

		if (sortingQuizFullScreenSection === null && sectionCreateLayers === null) {
			preventDefaultOnTouchMove(e);
		}

		const clientPoint = getClientPoint(e);
		const localPoint = getLocalPoint(clientPoint, wiseCanvasHandWriting);

		updateDrawingCursor(localPoint.x, localPoint.y);
		drawHandwriting(e, localPoint.x, localPoint.y);
	}, { passive: false });

	if (wiseMenuErasersHandWritingResetButton !== null) {
		wiseMenuErasersHandWritingResetButton.addEventListener('pointerup', function(e) {
			e.preventDefault();

			resetHandwriting();
			closeAllWiseExpandableToolbars();
		}, false);
	}
}


function createLinkedCanvas(canvasContainer, elm_container_leftLink, elm_sizeTarget){

	let elm_canvas_new,
		uniqueKey_container_leftLink = escapeNumber(elm_container_leftLink.dataset.uniqueKey),
		uniqueKey_container_rightLink = escapeNumber(elm_container_leftLink.dataset.linkId),
		int_linkType = escapeNumber(elm_container_leftLink.dataset.linkType),
		elm_linkMarker_drawStart = document.getElementById('leftLinkMarker'+uniqueKey_container_rightLink),
		elm_linkMarker_drawEnd = document.getElementById('rightLinkMarker'+uniqueKey_container_leftLink);

	elm_canvas_new = document.createElement('canvas');
	elm_canvas_new.setAttribute('id', 'wiseCanvas' + maxCanvasId);
	elm_canvas_new.classList.add('wiseCanvas', 'wiseCanvasLinked');
	elm_canvas_new.width = elm_sizeTarget.clientWidth;
	elm_canvas_new.height = elm_sizeTarget.clientHeight;
	elm_canvas_new.dataset.leftLinkId = uniqueKey_container_leftLink;
	elm_canvas_new.dataset.rightLinkId = uniqueKey_container_rightLink;
	elm_canvas_new.dataset.linkType = int_linkType;


	if(elm_container_leftLink.classList.contains('itemOfPhraseClauseContainer')){
		elm_canvas_new.classList.add('itemOfPhraseClauseContainer');
	}

	++maxCanvasId;

	canvasContainer.prepend(elm_canvas_new);

	drawLinkLine(elm_canvas_new, elm_linkMarker_drawStart, elm_linkMarker_drawEnd);

}


function applyWiseCanvasLayout(){

    let elms_wiseCanvas = document.querySelectorAll('.wiseCanvas');
    if(elms_wiseCanvas.length === LENGTH_EMPTY) return;

    // コンテナサイズ固定
    wiseCanvasContainer.style.width  = BOARD_WIDTH  + 'px';
    wiseCanvasContainer.style.height = BOARD_HEIGHT + 'px';

	if(wisePanelWhiteboardViewMainContentArea !== null){
		wisePanelWhiteboardViewMainContentArea.style.width  = BOARD_WIDTH  + 'px';
		wisePanelWhiteboardViewMainContentArea.style.height = BOARD_HEIGHT + 'px';
	}

    for(let i = 0; i < elms_wiseCanvas.length; i++){
        elms_wiseCanvas[i].width  = BOARD_WIDTH;
        elms_wiseCanvas[i].height = BOARD_HEIGHT;
    }

	ensureBoardMarksLayer(BOARD_WIDTH, BOARD_HEIGHT);

    redrawLinkLines();
}

function ensureBoardMarksLayer(boardWidth, boardHeight) {

    if (!wisePanelWhiteboardBody || !wiseBoardMarksLayer) {
        return;
    }

    wiseBoardMarksLayer.style.width = boardWidth + 'px';
    wiseBoardMarksLayer.style.height = boardHeight + 'px';

    // 既存トンボをクリア
    wiseBoardMarksLayer.innerHTML = '';

    // 端にピッタリだと邪魔になりやすいので、少し内側に入れる（好みで調整）
    const inset = 12;

    const points = [
        { key: 'tl', x: inset, y: inset },
        { key: 'tr', x: boardWidth - inset, y: inset },
        { key: 'bl', x: inset, y: boardHeight - inset },
        { key: 'br', x: boardWidth - inset, y: boardHeight - inset },

        { key: 'c',  x: boardWidth / 2, y: boardHeight / 2 },

        { key: 'tm', x: boardWidth / 2, y: inset },
        { key: 'bm', x: boardWidth / 2, y: boardHeight - inset },
        { key: 'lm', x: inset, y: boardHeight / 2 },
        { key: 'rm', x: boardWidth - inset, y: boardHeight / 2 }
    ];

    for (let i = 0; i < points.length; i++) {
        const p = points[i];
        const mark = document.createElement('span');
        mark.className = 'wiseBoardMark wiseDecorativeItem';
        mark.dataset.key = p.key;
        mark.style.left = p.x + 'px';
        mark.style.top = p.y + 'px';
        wiseBoardMarksLayer.appendChild(mark);
    }

}

function drawLinkLine(elm_canvas, elm_linkMarker_drawStart, elm_linkMarker_drawEnd) {

    let isItemOfPhraseClauseContainer = false;

    if (elm_canvas.classList.contains('itemOfPhraseClauseContainer')) {
        isItemOfPhraseClauseContainer = true;
        elm_canvas.classList.remove('itemOfPhraseClauseContainer');
    }

    const bounds_linkMarker_drawStart = elm_linkMarker_drawStart.getBoundingClientRect();
    const bounds_linkMarker_drawEnd = elm_linkMarker_drawEnd.getBoundingClientRect();

    const bounds_drawingPoint_drawStart = calculateDrawingPoint(bounds_linkMarker_drawStart);
    const bounds_drawingPoint_drawEnd = calculateDrawingPoint(bounds_linkMarker_drawEnd);

    const startPoint = convertClientPointToCanvasPoint(
        bounds_drawingPoint_drawStart.x,
        bounds_drawingPoint_drawStart.y,
        elm_canvas
    );

    const endPoint = convertClientPointToCanvasPoint(
        bounds_drawingPoint_drawEnd.x,
        bounds_drawingPoint_drawEnd.y,
        elm_canvas
    );

    const context_canvas = elm_canvas.getContext('2d');

    let point_x_drawStart_result = startPoint.x;
    let point_y_drawStart_result = startPoint.y;
    let point_x_drawEnd_result = endPoint.x;
    let point_y_drawEnd_result = endPoint.y;

    const elm_movableContainer_drawStart = elm_linkMarker_drawStart.parentElement;
    const elm_movableContainer_drawEnd = elm_linkMarker_drawEnd.parentElement;

    const seg1Start = [startPoint.x, startPoint.y];
    const seg1End = [endPoint.x, endPoint.y];

    if (elm_movableContainer_drawStart !== null && elm_movableContainer_drawEnd !== null) {

        const bounds_movableContainer_drawStart = elm_movableContainer_drawStart.getBoundingClientRect();
        const bounds_movableContainer_drawEnd = elm_movableContainer_drawEnd.getBoundingClientRect();

        const rectStart = convertClientRectToCanvasRect(bounds_movableContainer_drawStart, elm_canvas);
        const rectEnd = convertClientRectToCanvasRect(bounds_movableContainer_drawEnd, elm_canvas);

        const arr_bounds = [rectStart, rectEnd];
        const arr_seg1 = [seg1Start, seg1End];
        const arr_draw_points = [];

        for (let i = INDEX_FIRST; i < arr_bounds.length; i++) {

            const target_bounds = arr_bounds[i];
            const target_left = target_bounds.left;
            const target_right = target_bounds.right;
            const target_top = target_bounds.top;
            const target_bottom = target_bounds.bottom;

            const arr_seg2 = [
                [[target_left, target_top], [target_right, target_top]],
                [[target_right, target_top], [target_right, target_bottom]],
                [[target_right, target_bottom], [target_left, target_bottom]],
                [[target_left, target_bottom], [target_left, target_top]]
            ];

            let intersection = null;

            for (let j = INDEX_FIRST; j < arr_seg2.length; j++) {

                const seg2Start = arr_seg2[j][INDEX_FIRST];
                const seg2End = arr_seg2[j][INDEX_SECOND];

                intersection = areSegmentsIntersecting(seg1Start, seg1End, seg2Start, seg2End);

                if (intersection !== null) {
                    break;
                }
            }

            if (intersection !== null) {
                arr_draw_points.push(intersection);
            } else {
                arr_draw_points.push(arr_seg1[i]);
            }
        }

        point_x_drawStart_result = arr_draw_points[INDEX_FIRST][INDEX_FIRST];
        point_y_drawStart_result = arr_draw_points[INDEX_FIRST][INDEX_SECOND];
        point_x_drawEnd_result = arr_draw_points[INDEX_SECOND][INDEX_FIRST];
        point_y_drawEnd_result = arr_draw_points[INDEX_SECOND][INDEX_SECOND];
    }

    context_canvas.clearRect(0, 0, elm_canvas.width, elm_canvas.height);
    context_canvas.beginPath();
    context_canvas.moveTo(point_x_drawStart_result, point_y_drawStart_result);
    context_canvas.lineTo(point_x_drawEnd_result, point_y_drawEnd_result);

    if (currentInteractionMode === 'elementsEraserMode') {
        context_canvas.strokeStyle = 'red';
        context_canvas.lineWidth = 10;

        if (context_canvas.isPointInStroke(linkLineEraserPointX, linkLineEraserPointY)) {
            if (!(touchedLinkLines.includes(elm_canvas))) {
                touchedLinkLines.push(elm_canvas);
            }
        }
    } else {
        if (escapeNumber(elm_canvas.dataset.linkType) === LINK_TYPE_NORMAL) {
            context_canvas.strokeStyle = 'black';
        } else if (escapeNumber(elm_canvas.dataset.linkType) === LINK_TYPE_TO_PHRASE_CLAUSE) {
            context_canvas.strokeStyle = 'blue';
        } else {
            context_canvas.strokeStyle = 'red';
        }
        context_canvas.lineWidth = 2;
    }

    context_canvas.stroke();

    if (isItemOfPhraseClauseContainer) {
        elm_canvas.classList.add('itemOfPhraseClauseContainer');
    }

    return;
}

function areSegmentsIntersecting(seg1Start, seg1End, seg2Start, seg2End) {
    const X = 0;
    const Y = 1;

    const COLINEAR = 0;
    const CLOCKWISE = 1;
    const COUNTERCLOCKWISE = 2;
    const EPS = 1e-10;

    function orientation(p, q, r) {
        const val = (q[Y] - p[Y]) * (r[X] - q[X]) - (q[X] - p[X]) * (r[Y] - q[Y]);
        if (Math.abs(val) < EPS) return COLINEAR;
        return val > COLINEAR ? CLOCKWISE : COUNTERCLOCKWISE;
    }

    function onSegment(p, q, r) {
        return (
            q[X] <= Math.max(p[X], r[X]) + EPS &&
            q[X] >= Math.min(p[X], r[X]) - EPS &&
            q[Y] <= Math.max(p[Y], r[Y]) + EPS &&
            q[Y] >= Math.min(p[Y], r[Y]) - EPS
        );
    }

    const o1 = orientation(seg1Start, seg1End, seg2Start);
    const o2 = orientation(seg1Start, seg1End, seg2End);
    const o3 = orientation(seg2Start, seg2End, seg1Start);
    const o4 = orientation(seg2Start, seg2End, seg1End);

    if (o1 !== o2 && o3 !== o4) {
        const a1 = seg1End[Y] - seg1Start[Y];
        const b1 = seg1Start[X] - seg1End[X];
        const c1 = a1 * seg1Start[X] + b1 * seg1Start[Y];

        const a2 = seg2End[Y] - seg2Start[Y];
        const b2 = seg2Start[X] - seg2End[X];
        const c2 = a2 * seg2Start[X] + b2 * seg2Start[Y];

        const det = a1 * b2 - a2 * b1;
        if (Math.abs(det) < EPS) {
            return null;
        }

        const x = (b2 * c1 - b1 * c2) / det;
        const y = (a1 * c2 - a2 * c1) / det;

        const p = [x, y];
        if (onSegment(seg1Start, p, seg1End) && onSegment(seg2Start, p, seg2End)) {
            return p;
        }
        return null;
    }

    if (o1 === COLINEAR && onSegment(seg1Start, seg2Start, seg1End)) return [seg2Start[X], seg2Start[Y]];
    if (o2 === COLINEAR && onSegment(seg1Start, seg2End, seg1End))   return [seg2End[X],   seg2End[Y]];
    if (o3 === COLINEAR && onSegment(seg2Start, seg1Start, seg2End)) return [seg1Start[X], seg1Start[Y]];
    if (o4 === COLINEAR && onSegment(seg2Start, seg1End, seg2End))   return [seg1End[X],   seg1End[Y]];

    return null;
}


function redrawLinkLines(){

	let elms_canvas = document.querySelectorAll('.wiseCanvasLinked');

	if(elms_canvas.length === LENGTH_EMPTY)return;
	
	for(let i = INDEX_FIRST; i < elms_canvas.length; i++){
		let elm_canvas = elms_canvas[i];
		let uniqueKey_container_leftLink = escapeNumber(elm_canvas.dataset.leftLinkId);
		let uniqueKey_container_rightLink = escapeNumber(elm_canvas.dataset.rightLinkId);
		let elm_linkMarker_drawStart = document.getElementById('leftLinkMarker'+uniqueKey_container_rightLink);
		let elm_linkMarker_drawEnd = document.getElementById('rightLinkMarker'+uniqueKey_container_leftLink);
		drawLinkLine(elm_canvas, elm_linkMarker_drawStart, elm_linkMarker_drawEnd);
	}
}


function drawHandwritingLine(elm_wiseCanvas, x, y, color, linesize) {

    const startX = handWritingStartPosition.x;
    const startY = handWritingStartPosition.y;

    const end = getStraightLineEndPoint(x, y);

    let context_canvas = elm_wiseCanvas.getContext('2d');

    if (elm_wiseCanvas === wiseCanvasOriginal) {
        context_canvas.clearRect(0, 0, elm_wiseCanvas.width, elm_wiseCanvas.height);
    }

    context_canvas.beginPath();
    context_canvas.moveTo(startX, startY);
    context_canvas.lineTo(end.x, end.y);

    context_canvas.strokeStyle = color;
    context_canvas.lineWidth = linesize;
    context_canvas.stroke();

    return end;
}


function getStraightLineEndPoint(x, y) {

    let endX = x;
    let endY = y;

    if (isStarted_Handwriting_withCtrlKey) {

        const dx = Math.abs(endX - handWritingStartPosition.x);
        const dy = Math.abs(endY - handWritingStartPosition.y);

        if (dx > dy) {
            endY = handWritingStartPosition.y;
        } else {
            endX = handWritingStartPosition.x;
        }
    }

    return { x: endX, y: endY };
}

function drawHandwriting(e, x, y) {

    if (!doingHandwriting) return;

    const touches = e.touches;
    if (touches && touches.length >= 2) {
        resetHandwritingState();
        return;
    }

    // ★直線モード：handwrite には描かない。プレビューだけ。
    if (isStarted_Handwriting_withShiftKey) {
        drawHandwritingLine(wiseCanvasOriginal, x, y, currentHandWritingColor, 2);
        return;
    }

    // ★通常モード：線分ごとに描く（新仕様の改善点は維持）
    if (currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode') return;

    const fromX = (handWritingLastPosition.x === null) ? x : handWritingLastPosition.x;
    const fromY = (handWritingLastPosition.y === null) ? y : handWritingLastPosition.y;

    wiseCanvasHandWritingContext.beginPath();
    wiseCanvasHandWritingContext.moveTo(fromX, fromY);
    wiseCanvasHandWritingContext.lineTo(x, y);
    wiseCanvasHandWritingContext.stroke();

    addStrokePoint(x, y);

    handWritingLastPosition.x = x;
    handWritingLastPosition.y = y;
}



function startHandwriting(e, x, y) {

	if(currentInteractionMode !== 'drawingMode'){
		if(currentInteractionMode !== 'eraserMode')return;
	}

	const touches = e.touches;
	if(touches){
		if (touches.length >= 2){
			resetHandwritingState();
		}
	}

	let isCtrlPressed = e.key === 'Control' || e.ctrlKey || e.metaKey;
	let isShiftPressed = e.key === 'Shift' || e.shiftKey;
	let isAltPressed = e.key === 'Alt' || e.altKey;
	
	if(isCtrlPressed){
		isStarted_Handwriting_withCtrlKey = true;
	}
	if(isShiftPressed || isAltPressed){
		isStarted_Handwriting_withShiftKey = true;
		handWritingStartPosition = { x: x, y: y };
	}

	wiseCanvasHandWritingContext.beginPath();

	if(currentInteractionMode === 'drawingMode'){

		wiseCanvasHandWritingContext.globalCompositeOperation = 'source-over';
		wiseCanvasHandWritingContext.lineWidth = 1;

	}else{

		if(isStarted_Handwriting_withShiftKey){
			isStarted_Handwriting_withCtrlKey = false;
			isStarted_Handwriting_withShiftKey = false;
			return;
		}
		wiseCanvasHandWritingContext.globalCompositeOperation = 'destination-out';
		wiseCanvasHandWritingContext.lineWidth = currentEraserSize;

		let elm_eraserSpan = document.getElementById('eraserSpan');
		elm_eraserSpan.style.visibility = 'visible';

	}
	wiseCanvasHandWritingContext.lineCap = 'round';
	wiseCanvasHandWritingContext.strokeStyle = currentHandWritingColor;

	const tool = (currentInteractionMode === 'eraserMode') ? 'eraser' : 'pen';
	const size = (tool === 'eraser') ? currentEraserSize : 1; // drawingMode は lineWidth = 1 を使っているので合わせる
	const color = (tool === 'eraser') ? null : currentHandWritingColor;

	if (!isStarted_Handwriting_withShiftKey) {
		startStroke(tool, x, y, { color: color, size: size });
	}

	doingHandwriting = true;

}

function endHandwriting(e) {

    if (currentInteractionMode !== 'drawingMode' && currentInteractionMode !== 'eraserMode') {
        return;
    }

	hideDrawingCursor();

    const clientPoint = getClientPoint(e);
    const localPoint = getLocalPoint(clientPoint, wiseCanvasHandWriting);

    if (isStarted_Handwriting_withShiftKey) {
        wiseCanvasOriginalContext.clearRect(0, 0, wiseCanvasOriginal.width, wiseCanvasOriginal.height);

		const tool = (currentInteractionMode === 'eraserMode') ? 'eraser' : 'pen';
		const size = (tool === 'eraser') ? currentEraserSize : 2;
		const color = (tool === 'eraser') ? null : currentHandWritingColor;

        const end = drawHandwritingLine(wiseCanvasHandWriting, localPoint.x, localPoint.y, color, size);

		startStroke(tool, handWritingStartPosition.x, handWritingStartPosition.y, { color: color, size: size });
		addStrokePoint(end.x, end.y);
    } else {
        drawHandwriting(e, localPoint.x, localPoint.y);
    }

    resetHandwritingState();
}


function resetHandwritingState(){

    isStarted_Handwriting_withCtrlKey = false;
    isStarted_Handwriting_withShiftKey = false;

    wiseCanvasHandWritingContext.closePath();

    doingHandwriting = false;

    handWritingStartPosition = { x: null, y: null };
    handWritingLastPosition.x = null;
    handWritingLastPosition.y = null;

	const didChange =
		currentStroke &&
		currentStroke.points &&
		currentStroke.points.length >= 1;

	if (!didChange) {
		currentStroke = null;
		return;
	}

	if (currentStroke.points.length === 1) {
		const p = currentStroke.points[0];
		currentStroke.points.push({
			x: p.x,
			y: p.y,
			t: Date.now()
		});
	}

	endStroke();

	let str_title = '';
	if (currentInteractionMode === 'eraserMode') {
		str_title = STATE_TITLE_DELETE_HANDWRITTEN_LINES[intSelectedLanguage];
	} else {
		str_title = STATE_TITLE_DRAW_HANDWRITTEN_LINE[intSelectedLanguage];
	}

	saveState(str_title);
}



function resetHandwriting() {

	resetToSelectMode();

	isConfirmed = true;

	if(isConfirmed){
		wiseCanvasHandWritingContext.clearRect(0, 0, wiseCanvasHandWriting.width, wiseCanvasHandWriting.height);
		strokeHistory = [];
		saveState(STATE_TITLE_DELETE_ALL_HANDWRITTEN_LINES[intSelectedLanguage]);
	}
	return;
}

// function compareImageData(imageData1, imageData2) {

// 	if (imageData1 === null || imageData1 === undefined || imageData2 === null || imageData2 === undefined) {
// 	return false;
// 	}


// 	let data1 = imageData1.data;
// 	let data2 = imageData2.data;

// 	for (let i = INDEX_FIRST; i < data1.length; i++) {
// 	if (data1[i] !== data2[i]) {
// 		return true;
// 	}
// 	}
// 	return false;
// }










function startStroke(tool, x, y, options = {}) {
    currentStroke = {
        tool: tool,
        color: (tool === 'eraser') ? null : (options.color || '#000000'),
        size: Number.isFinite(options.size) ? options.size : 3,
        points: [{ x: x, y: y, t: Date.now() }]
    };
}

function addStrokePoint(x, y) {

    if (!currentStroke) return;

    // 点が細かすぎるとデータが肥大化するので間引き（距離で）
    const pts = currentStroke.points;
    const last = pts[pts.length - 1];

    const dx = x - last.x;
    const dy = y - last.y;
    const dist2 = dx * dx + dy * dy;
    const threshold = 0.5;
	if (dist2 < threshold * threshold) return;

    pts.push({ x: x, y: y, t: Date.now() });
}

function endStroke() {

    if (!currentStroke) return false;

    // 1点しかない場合でも「点」として残したければ残す
    strokeHistory.push(currentStroke);

    // 何か描いたら redo スタックは破棄
    strokeUndone = [];

    currentStroke = null;

    return true;
}

function drawStroke(ctx, stroke) {

    const pts = stroke.points || [];
    if (pts.length < 2) return;

    ctx.save();
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.lineWidth = stroke.size || 2;

    if (stroke.tool === 'eraser') {
        ctx.globalCompositeOperation = 'destination-out';
        ctx.strokeStyle = 'rgba(0,0,0,1)';
    } else {
        ctx.globalCompositeOperation = 'source-over';
        ctx.strokeStyle = stroke.color || '#000000';
    }

    // ★ライブ描画寄せ（線分ごと）
    for (let i = 1; i < pts.length; i++) {
        ctx.beginPath();
        ctx.moveTo(pts[i - 1].x, pts[i - 1].y);
        ctx.lineTo(pts[i].x, pts[i].y);
        ctx.stroke();
    }

    ctx.restore();
}

function redrawFromStrokeHistory(ctx, canvas, history) {

	ctx.setTransform(1,0,0,1,0,0);
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (!Array.isArray(history)) return;

    for (let i = 0; i < history.length; i++) {
        drawStroke(ctx, history[i]);
    }
}


function updateDrawingCursor(x, y) {

    const elm_penCursorSpan = document.getElementById('penCursorSpan');
    const elm_eraserSpan = document.getElementById('eraserSpan');

    if (currentInteractionMode === 'drawingMode') {

        document.body.classList.add('cursor-hidden');

        if (elm_penCursorSpan !== null) {
            elm_penCursorSpan.style.visibility = 'visible';
            elm_penCursorSpan.style.left = x + 'px';
            elm_penCursorSpan.style.top = y + 'px';
        }

        if (elm_eraserSpan !== null) {
            elm_eraserSpan.style.visibility = 'hidden';
        }

        return;
    }

    if (currentInteractionMode === 'eraserMode') {

        document.body.classList.add('cursor-hidden');

        if (elm_penCursorSpan !== null) {
            elm_penCursorSpan.style.visibility = 'hidden';
        }

        if (elm_eraserSpan !== null) {
            elm_eraserSpan.style.visibility = 'visible';
            elm_eraserSpan.style.left = x + 'px';
            elm_eraserSpan.style.top = y + 'px';
        }

        return;
    }

    document.body.classList.remove('cursor-hidden');

    if (elm_penCursorSpan !== null) {
        elm_penCursorSpan.style.visibility = 'hidden';
    }

    if (elm_eraserSpan !== null) {
        elm_eraserSpan.style.visibility = 'hidden';
    }
}

function hideDrawingCursor() {

    const elm_penCursorSpan = document.getElementById('penCursorSpan');
    const elm_eraserSpan = document.getElementById('eraserSpan');

    document.body.classList.remove('cursor-hidden');

    if (elm_penCursorSpan !== null) {
        elm_penCursorSpan.style.visibility = 'hidden';
    }

    if (elm_eraserSpan !== null) {
        elm_eraserSpan.style.visibility = 'hidden';
    }
}