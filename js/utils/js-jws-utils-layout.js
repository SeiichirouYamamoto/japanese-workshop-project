
function getClientPoint(e) {
    if (e.type.startsWith('touch')) {
        const touch = e.changedTouches[INDEX_FIRST] || e.touches[INDEX_FIRST];
        return { x: touch.clientX, y: touch.clientY };
    }
    return { x: e.clientX, y: e.clientY };
}

function getLocalPoint(clientPoint, elm) {

    const rect = elm.getBoundingClientRect();
    const zoomScale = getWiseZoomScale();

    return {
        x: (clientPoint.x - rect.left) / zoomScale,
        y: (clientPoint.y - rect.top) / zoomScale
    };
}

function getScrollContainer(elm) {
    let cur = elm;
    while (cur && cur !== document.body) {
        const style = window.getComputedStyle(cur);
        const overflowY = style.overflowY;
        const overflowX = style.overflowX;

        const canScrollY = (overflowY === 'auto' || overflowY === 'scroll') && cur.scrollHeight > cur.clientHeight;
        const canScrollX = (overflowX === 'auto' || overflowX === 'scroll') && cur.scrollWidth > cur.clientWidth;

        if (canScrollX || canScrollY) {
            return cur;
        }
        cur = cur.parentElement;
    }
    return document.scrollingElement || document.documentElement;
}

function getVisibleLocalTopLeft(elm) {

    const scrollElm = getScrollContainer(elm);
    const zoomScale = getWiseZoomScale();

    return {
        x: (scrollElm ? scrollElm.scrollLeft : 0) / zoomScale,
        y: (scrollElm ? scrollElm.scrollTop : 0) / zoomScale
    };
}

function getCreatePointFromVisibleArea(elm, offsetX = APPEARANCE_AREA_DEFAULT, offsetY = APPEARANCE_AREA_DEFAULT) {

    const visibleTopLeft = getVisibleLocalTopLeft(elm);

    return {
        x: visibleTopLeft.x + offsetX,
        y: visibleTopLeft.y + offsetY
    };
}


function convertClientPointToCanvasPoint(clientX, clientY, canvas) {

    const rect = canvas.getBoundingClientRect();

    return {
        x: (clientX - rect.left) * (canvas.width / rect.width),
        y: (clientY - rect.top) * (canvas.height / rect.height)
    };
}

function convertClientRectToCanvasRect(rect, canvas) {

    const canvasRect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / canvasRect.width;
    const scaleY = canvas.height / canvasRect.height;

    return {
        left: (rect.left - canvasRect.left) * scaleX,
        right: (rect.right - canvasRect.left) * scaleX,
        top: (rect.top - canvasRect.top) * scaleY,
        bottom: (rect.bottom - canvasRect.top) * scaleY
    };
}

function convertClientRectToLocalRect(rect, elm) {

    const baseRect = elm.getBoundingClientRect();
    const zoomScale = getWiseZoomScale();

    return {
        left: (rect.left - baseRect.left) / zoomScale,
        right: (rect.right - baseRect.left) / zoomScale,
        top: (rect.top - baseRect.top) / zoomScale,
        bottom: (rect.bottom - baseRect.top) / zoomScale,
        width: rect.width / zoomScale,
        height: rect.height / zoomScale
    };
}


function cacheWiseLocalPointBase(elm) {
    const rect = elm.getBoundingClientRect();

    pointerLocalCache.active = true;
    pointerLocalCache.rectLeft = rect.left;
    pointerLocalCache.rectTop = rect.top;
    pointerLocalCache.zoomScale = getWiseZoomScale();
}

function getCachedLocalPoint(clientPoint) {
    const zoomScale = pointerLocalCache.zoomScale || 1;

    return {
        x: (clientPoint.x - pointerLocalCache.rectLeft) / zoomScale,
        y: (clientPoint.y - pointerLocalCache.rectTop) / zoomScale
    };
}


function setWiseLayoutPosition(targetSection){
	targetSection.style.left = "0px";
	targetSection.style.top = "0px";
	let bounds_targetSection = targetSection.getBoundingClientRect();
	let offsetLeft = bounds_targetSection.left;
	let offsetTop = bounds_targetSection.top;
	targetSection.style.left = -offsetLeft + "px";
	targetSection.style.top = -offsetTop + "px";
}

async function animateMount(content, {
    mode = 'prepend',
    parentEl = null,
    targetEl = null,
    duration = 800,
    easing = 'cubic-bezier(.22,.61,.36,1)',
    effect = 'gap',
    gapRatio = 0.6,
    itemSelector = '.wiseMapUXWaypointContainer',
    floatClass = 'wiseMapFloatIn',
    lockHeightDuring = false,
    keepLocked = false,
    sound = 'popup'
} = {}) {
    const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const asNode = (htmlOrNode) => {
        if (htmlOrNode instanceof HTMLElement) return htmlOrNode;
        const wrap = document.createElement('div');
        wrap.innerHTML = String(htmlOrNode);
        return mode === 'replace' ? wrap : (wrap.firstElementChild || wrap);
    };

    // const playSound = () => {
    //     if (!sound) return;
    //     if (window.soundFX && typeof window.soundFX[sound] === 'function') {
    //         window.soundFX[sound]();
    //     }
    // };

    if (mode === 'replace') {
        if (!targetEl) return;

        const nextWrap = asNode(content);
        nextWrap.classList?.add(floatClass);

        const prevStyle = { height: targetEl.style.height, overflow: targetEl.style.overflow };
        const prevH = targetEl.getBoundingClientRect().height;

        if (lockHeightDuring || keepLocked) {
            targetEl.style.height = prevH + 'px';
            targetEl.style.overflow = 'hidden';
        }

        targetEl.innerHTML = '';
        targetEl.appendChild(nextWrap);

        if (reduce) {
            nextWrap.classList?.remove(floatClass);
            if (!keepLocked) {
                targetEl.style.height = prevStyle.height;
                targetEl.style.overflow = prevStyle.overflow;
            }
            return;
        }

        nextWrap.getBoundingClientRect();
        // playSound();

        nextWrap.style.transition = `transform ${duration}ms ${easing}, opacity ${duration}ms ${easing}, filter ${duration}ms ${easing}`;
        nextWrap.style.transform = 'scale(1)';
        nextWrap.style.opacity = '1';
        nextWrap.style.filter = 'none';

        await wait(duration + 40);

        nextWrap.classList?.remove(floatClass);
        nextWrap.style.transition = '';
        nextWrap.style.transform = '';
        nextWrap.style.opacity = '';
        nextWrap.style.filter = '';

        if (!keepLocked) {
            targetEl.style.height = prevStyle.height;
            targetEl.style.overflow = prevStyle.overflow;
        }

        return;
    }

    if (!parentEl) return;

    const node = asNode(content);

    if (reduce) {
        // playSound();
        mode === 'prepend' ? parentEl.prepend(node) : parentEl.append(node);
        return;
    }

    if (effect === 'gap') {
        const probe = node.cloneNode(true);
        probe.style.position = 'absolute';
        probe.style.visibility = 'hidden';
        probe.style.pointerEvents = 'none';

        mode === 'prepend' ? parentEl.prepend(probe) : parentEl.append(probe);

        const targetH = probe.getBoundingClientRect().height || 1;
        probe.remove();

        const ph = document.createElement('div');
        ph.className = 'wiseMapGapPlaceholder';
        ph.style.height = '0px';
        ph.style.overflow = 'hidden';

        const gapDur = Math.max(1, Math.floor(duration * gapRatio));
        ph.style.transition = `height ${gapDur}ms ${easing}`;

        mode === 'prepend' ? parentEl.prepend(ph) : parentEl.append(ph);
        ph.getBoundingClientRect();
        ph.style.height = targetH + 'px';

        await wait(gapDur + 20);

        node.classList.add(floatClass);
        // playSound();
        ph.replaceWith(node);
        node.getBoundingClientRect();

        const floatDur = Math.max(1, Math.floor(duration * (1 - gapRatio)));
        node.style.transition = `transform ${floatDur}ms ${easing}, opacity ${floatDur}ms ${easing}, filter ${floatDur}ms ${easing}`;
        node.style.transform = 'scale(1)';
        node.style.opacity = '1';
        node.style.filter = 'none';

        await wait(floatDur + 50);

        node.classList.remove(floatClass);
        node.style.transition = '';
        node.style.transform = '';
        node.style.opacity = '';
        node.style.filter = '';

        return;
    }

    if (effect === 'translate') {
        const existing = Array.from(parentEl.querySelectorAll(itemSelector));
        const firstTops = existing.map(el => el.getBoundingClientRect().top);

        node.classList.add(floatClass);
        // playSound();

        mode === 'prepend' ? parentEl.prepend(node) : parentEl.append(node);
        node.getBoundingClientRect();

        const lastTops = existing.map(el => el.getBoundingClientRect().top);
        existing.forEach((el, i) => {
            const dy = firstTops[i] - lastTops[i];
            el.style.transition = 'none';
            el.style.transform = `translateY(${dy}px)`;
        });

        node.style.transition = 'none';
        node.getBoundingClientRect();

        existing.forEach(el => {
            el.style.transition = `transform ${duration}ms ${easing}`;
            el.style.transform = 'translateY(0)';
        });

        node.style.transition = `transform ${duration}ms ${easing}, opacity ${duration}ms ${easing}, filter ${duration}ms ${easing}`;
        node.style.transform = 'scale(1)';
        node.style.opacity = '1';
        node.style.filter = 'none';

        await wait(duration + 50);

        existing.forEach(el => {
            el.style.transition = '';
            el.style.transform = '';
        });

        node.classList.remove(floatClass);
        node.style.transition = '';
        node.style.transform = '';
        node.style.opacity = '';
        node.style.filter = '';

        return;
    }

    node.classList.add(floatClass);
    // playSound();

    mode === 'prepend' ? parentEl.prepend(node) : parentEl.append(node);
    node.getBoundingClientRect();

    node.style.transition = `transform ${duration}ms ${easing}, opacity ${duration}ms ${easing}, filter ${duration}ms ${easing}`;
    node.style.transform = 'scale(1)';
    node.style.opacity = '1';
    node.style.filter = 'none';

    await wait(duration + 40);

    node.classList.remove(floatClass);
    node.style.transition = '';
    node.style.transform = '';
    node.style.opacity = '';
    node.style.filter = '';
}

function calculateDrawingPoint(bounds_array){

	let area_left = Number(bounds_array.left),
		area_top = Number(bounds_array.top),
		area_width_center = Number(bounds_array.width)/2,
		area_height_center = Number(bounds_array.height)/2,
		point_draw_X = area_left+area_width_center,
		point_draw_Y = area_top+area_height_center,
		bounds_drawingPoint = {};

	bounds_drawingPoint = {
		x: point_draw_X,
		y: point_draw_Y
	};

	return bounds_drawingPoint;
}

function ensureWiseVh(height) {

    const root = document.documentElement;
    root.style.setProperty('--wise-vh', height + 'px');
}


function isEditableElementFocused() {

    const elm = document.activeElement;
    if (!elm) return false;

    return (
        elm.tagName === 'TEXTAREA' ||
        elm.tagName === 'INPUT' ||
        elm.isContentEditable === true
    );
}

function isSoftwareKeyboardOpen() {

    if (!window.visualViewport) return false;

    return window.visualViewport.height < window.innerHeight * 0.85;
}

function shouldSkipRelayout() {

    const editing = isEditableElementFocused();
    const keyboardOpen = isSoftwareKeyboardOpen();

    return editing && keyboardOpen;
}

