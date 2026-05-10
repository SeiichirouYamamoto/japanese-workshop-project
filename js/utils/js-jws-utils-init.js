/**
 * Call safely if the function exists.
 */
function callIfFunction(fn, ...args) {

    if (typeof fn === 'function') return fn(...args);

    return undefined;
}

/**
 * Resolve UIReady safely (supports UIReady.resolveOnce()).
 */
function resolveUIReadyOnce() {

    if (typeof UIReady !== 'undefined' && UIReady !== null) {
        if (typeof UIReady.resolveOnce === 'function') {
            UIReady.resolveOnce();
            return true;
        }
    }

    return false;
}

/**
 * Common pre-layout tasks for onload (safe no-op if functions are missing).
 * - hide page-top button
 * - set viewport height
 */


function hideMobileNavButtonOnLoad(){
	let mobileNavButton = document.querySelector('.vk-mobile-nav-menu-btn');
	if (mobileNavButton !== null) {
		mobileNavButton.style.display = 'none';
	}
}


function hidePageTopButtonOnLoad(){
	let pageTopButton = document.querySelector('#page_top');
	if (pageTopButton !== null) {
		pageTopButton.style.display = 'none';
	}
}

function applyWiseViewportRect() {

    const elements = document.querySelectorAll('.wise-require-fullscreen');
    const vv = window.visualViewport;
    const htmlRect = document.documentElement.getBoundingClientRect();

    let width = window.innerWidth;
    let height = window.innerHeight;
    let top = 0;
    let left = 0;

    // if (vv) {

    //     // visualViewport の「見えている矩形」を
    //     // layout viewport 基準で安全に補正する
    //     const rawTop = vv.offsetTop - htmlRect.top;
    //     const rawLeft = vv.offsetLeft - htmlRect.left;

    //     const visibleTop = Math.max(0, Math.round(rawTop));
    //     const visibleLeft = Math.max(0, Math.round(rawLeft));

    //     const visibleBottom = Math.min(
    //         window.innerHeight,
    //         Math.round(rawTop + vv.height)
    //     );

    //     const visibleRight = Math.min(
    //         window.innerWidth,
    //         Math.round(rawLeft + vv.width)
    //     );

    //     width = Math.max(0, visibleRight - visibleLeft);
    //     height = Math.max(0, visibleBottom - visibleTop);
    //     top = visibleTop;
    //     left = visibleLeft;
    // }

    ensureWiseVh(height);

    elements.forEach(elm => {
        elm.style.left = left + 'px';
        elm.style.top = top + 'px';
        elm.style.width = width + 'px';
        elm.style.height = height + 'px';
        elm.style.right = 'auto';
        elm.style.bottom = 'auto';
    });
}

function prepareLayoutOnLoad() {

    callIfFunction(hidePageTopButtonOnLoad);
	
    if (sectionWise === null) return;

    runAfterStableLayout(() => {
        callIfFunction(applyWiseViewportRect);
        lockPageScroll();
    });
}

/**
 * Common pre-layout tasks for resize/orientationchange (safe no-op).
 * Keep minimal: viewport height recalculation only.
 */

function prepareLayoutOnResize() {
    callIfFunction(applyWiseViewportRect);
}

function runAfterStableLayout(callback) {

    let lastRect = null;
    let stableCount = 0;

    function check() {

        const vv = window.visualViewport;

        const rect = vv
            ? {
                width: Math.round(vv.width),
                height: Math.round(vv.height),
                top: Math.round(vv.offsetTop),
                left: Math.round(vv.offsetLeft)
            }
            : {
                width: Math.round(window.innerWidth),
                height: Math.round(window.innerHeight),
                top: 0,
                left: 0
            };

        const same =
            lastRect &&
            rect.width === lastRect.width &&
            rect.height === lastRect.height &&
            rect.top === lastRect.top &&
            rect.left === lastRect.left;

        if (same) {
            stableCount++;
        } else {
            stableCount = 0;
            lastRect = rect;
        }

        if (stableCount >= 2) {
            callback();
        } else {
            requestAnimationFrame(check);
        }
    }

    check();
}

/**
 * Disable mobile gestures that conflict with your UI (safe no-op).
 * - pull-to-refresh
 * - pinch-zoom
 */
function disableMobileGestures() {

    callIfFunction(disablePullDownRefresh);
    callIfFunction(disablePinchZoom);
}

function disablePullDownRefresh() {
    
    document.addEventListener('touchstart', function (e) {
        cancelPullDownRefreshStartY = e.touches[INDEX_FIRST].clientY;
    }, { passive: true });

    document.addEventListener('touchmove', function (e) {
        const touchedElement = e.target;
        const currentY = e.touches[INDEX_FIRST].clientY;
        const isPullingDown = currentY > cancelPullDownRefreshStartY;

        if (isPullingDown && !isScrollableElement(touchedElement) && e.cancelable) {
            e.preventDefault();
        }
    }, { passive: false });
}

function isScrollableElement(elm) {
	while (elm && elm !== document.body) {
		const style = window.getComputedStyle(elm);
		const overflowY = style.overflowY;

		if (
			(overflowY === 'auto' || overflowY === 'scroll') &&
			elm.scrollHeight > elm.clientHeight
		) {
			return true;
		}
		elm = elm.parentElement;
	}
	return false;
}

function disablePinchZoom(){
	document.addEventListener('dblclick', function (e) {
		e.preventDefault();
	}, { passive: false });
	document.addEventListener('gesturestart', function (e) {
		e.preventDefault();
	}, { passive: false });
	document.addEventListener('gesturechange', function (e) {
		e.preventDefault();
	}, { passive: false });
	document.addEventListener('gestureend', function (e) {
		e.preventDefault();
	}, { passive: false });	
}

/**
 * Finalize layout after resizing process.
 * Wait until global isResizing becomes false, then resolve UIReady once.
 *
 * @param {Object} options
 * @param {number} options.timeoutMs Default 8000
 * @param {boolean} options.resolveEvenIfWaitMissing Default true
 */
function finalizeLayout(options = {}) {

    const timeoutMs = Number.isFinite(options.timeoutMs) ? options.timeoutMs : 8000;
    const resolveEvenIfWaitMissing = options.resolveEvenIfWaitMissing !== false;

    const canWait =
        typeof waitUntil === 'function' &&
        typeof isResizing !== 'undefined';

    if (!canWait) {
        if (resolveEvenIfWaitMissing) resolveUIReadyOnce();
        return;
    }

    waitUntil(() => !isResizing, { timeoutMs: timeoutMs })
        .then(() => {
            resolveUIReadyOnce();
        })
        .catch(() => {
            resolveUIReadyOnce();
        });
}

/**
 * Optional helper: throttle layout refresh calls for resize storms.
 * Use in entries if needed.
 *
 * @param {Function} fn
 * @param {number} waitMs
 * @returns {Function}
 */
function throttle(fn, waitMs = 150) {

    let lastTime = 0;
    let timerId = null;

    return function (...args) {

        const now = Date.now();
        const remaining = waitMs - (now - lastTime);

        if (remaining <= 0) {
            lastTime = now;
            fn.apply(this, args);
            return;
        }

        if (timerId !== null) return;

        timerId = setTimeout(() => {
            timerId = null;
            lastTime = Date.now();
            fn.apply(this, args);
        }, remaining);
    };
}
