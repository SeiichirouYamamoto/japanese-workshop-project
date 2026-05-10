
if (wiseWaitOverlay) {
    wiseWaitOverlay.addEventListener('pointerup', function(e) {
        e.preventDefault();
        e.stopPropagation();
        startWiseWaitToScanTransition(wiseWaitOverlay);
    }, false);
}


if (wiseWaitOverlayLogo) {
    wiseWaitOverlayLogo.addEventListener('pointerup', function(e) {
        e.preventDefault();
        e.stopPropagation();
        startWiseWaitToScanTransition(wiseWaitOverlay);
    }, false);
}


if (wiseScanOverlay) {
    wiseScanOverlay.addEventListener('pointerup', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeWiseScanOverlay();
    }, false);
}


function hideWiseOverlay(el) {
    if (!el) return;
    el.classList.remove('overlay-on', 'scan-start', 'scan-finish');
    el.setAttribute('aria-hidden', 'true');
    el.inert = true;
    document.querySelectorAll('.wiseScanTarget.wise-scan-glow')
        .forEach(n => n.classList.remove('wise-scan-glow'));
    if (typeof currentScanTargets !== 'undefined') currentScanTargets = [];
}

function closeWiseScanOverlay() {

    wiseScanOverlay.classList.remove('scan-start');
    wiseScanOverlay.classList.add('scan-finish');

	isWiseScanRunning = false;
	closeAllWiseExpandableToolbars();
    setTimeout(() => {
		gatherScanClonesToToolbar(() => {
			removeAllScanClones?.(currentScanTargets);
			hideWiseOverlay(wiseScanOverlay);
			isWiseScanRunning = false;
			wise_navi_status = 'scanned';
			showWiseNaviNextLabel('black-text');
		});
	}, HIDE_DELAY_MS);

}

async function startWiseWaitToScanTransition(targetOverlay) {
    if (!targetOverlay || !wiseScanOverlay) return;

	if (targetOverlay === wiseOpeningOverlay) {
		wise_navi_status = 'scanning';
		hideWiseNaviNextLabel();
    }

    try { await UIReady.promise; } catch {}
    try { await waitUntil(() => !isResizing, { timeoutMs: 5000 }); } catch {}

    if (targetOverlay === wiseOpeningOverlay) {
        try { await renderWiseMapForNavigation({ showOverlay: false }); } catch {}
    }

    hideWiseOverlay(targetOverlay);
    wiseScanOverlay.removeAttribute('aria-hidden');
    wiseScanOverlay.inert = false;

    if (wiseScanline) {
        wiseScanline.style.animation = 'none';
        void wiseScanline.offsetWidth;
        wiseScanline.style.animation = '';
    }

    // if (window.soundFX) window.soundFX.startScanHold({ minFreq: SCAN_MIN_FREQ, maxFreq: SCAN_MAX_FREQ, rampMs: SCAN_MAX_RAMP_MS, vibHz: 5, vibDepth: 12, vol: 0.55, subLevel: 0.8 });

    wiseScanOverlay.classList.add('scan-start');

    if (targetOverlay === wiseOpeningOverlay) {
        runWiseScanAndCloseForNavi();
    } else {
        runWiseScanAndClose();
    }
}

function spawnOrPulseScanClone(el, { soft = false } = {}) {
    const r = el.getBoundingClientRect();
    if (!soft) {
        const area = r.width * r.height;
        const maxDim = Math.max(r.width, r.height);
        if (area > 180000 || maxDim > 600) soft = true;
    }
    let clone = cloneMap.get(el);
    if (!clone) {
        clone = el.cloneNode(true);
        sanitizeScanCloneTree(clone);
        clone.classList.add(CLONE_CLASS);
        clone.style.position = 'fixed';
        clone.style.left = r.left + 'px';
        clone.style.top = r.top + 'px';
        clone.style.width = r.width + 'px';
        clone.style.height = r.height + 'px';
        clone.style.transform = 'none';
        clone.style.filter = 'none';
        clone.style.zIndex = '10051';
        clone.style.pointerEvents = 'none';
        clone.style.opacity = getComputedStyle(el).opacity;
        wiseScanOverlay.appendChild(clone);
        cloneMap.set(el, clone);
    } else {
        clone.style.left = r.left + 'px';
        clone.style.top = r.top + 'px';
        clone.style.width = r.width + 'px';
        clone.style.height = r.height + 'px';
        clone.style.opacity = getComputedStyle(el).opacity;
    }
    clone.classList.remove('wise-scan-glow', 'wise-scan-glow-soft');
    clone.classList.add(soft ? 'wise-scan-glow-soft' : 'wise-scan-glow');
    clone.classList.remove('wise-scan-glow');
    void clone.offsetWidth;
    clone.classList.add(soft ? 'wise-scan-glow-soft' : 'wise-scan-glow');
}

function gatherScanClonesToToolbar(callback) {

    const leftTarget = document.getElementById('wiseVerticalToolbarCurrentButton');
    const rightTarget = document.getElementById('wiseRightVerticalToolbarCurrentButton');

    const clones = Array.from(
		document.querySelectorAll('.' + CLONE_CLASS + '.wiseScanActor')
	);

    if (clones.length === 0) {
        callback?.();
        return;
    }

    let finishedCount = 0;

    clones.forEach(clone => {

        // --- ここが分岐ポイント ---
        const isRight = clone.classList.contains('wiseRightVerticalToolbarButton');
        const target = isRight ? rightTarget : leftTarget;

        if (!target) {
            finishedCount++;
            return;
        }

        const targetRect = target.getBoundingClientRect();
        const cloneRect = clone.getBoundingClientRect();

        const targetX = targetRect.left + targetRect.width / 2;
        const targetY = targetRect.top + targetRect.height / 2;

        const cloneX = cloneRect.left + cloneRect.width / 2;
        const cloneY = cloneRect.top + cloneRect.height / 2;

        const moveX = targetX - cloneX;
        const moveY = targetY - cloneY;

		const moveTime = 800;

		clone.style.transition = `transform ${moveTime}ms cubic-bezier(0.22, 1, 0.36, 1), opacity ${moveTime}ms ease`;
        clone.style.transform = `translate(${moveX}px, ${moveY}px) scale(0.2)`;
        clone.style.opacity = '0';

        clone.addEventListener('transitionend', () => {
            finishedCount++;
            if (finishedCount >= clones.length) {
                callback?.();
            }
        }, { once: true });

    });
}

function removeAllScanClones(targets) {
    document.querySelectorAll('.' + CLONE_CLASS).forEach(n => n.remove());
    if (Array.isArray(targets)) targets.forEach(el => cloneMap.delete(el));
}

function runWiseScanAndClose() {
    if (!wiseScanOverlay) return;
    if (isWiseScanRunning) return;
    isWiseScanRunning = true;

    const targets = Array.from(document.querySelectorAll('.wiseScanTarget'));
    currentScanTargets = targets.slice(0);
    let prevLineY = null;

    function runWiseScanOverlayFrame() {
        if (!wiseScanOverlay.classList.contains('scan-start')) return;
        if (!wiseScanline) return;
        const r = wiseScanline.getBoundingClientRect();
        const lineY = r.top + r.height / 2;
        if (prevLineY === null) {
            prevLineY = lineY;
            requestAnimationFrame(runWiseScanOverlayFrame);
            return;
        }
        targets.forEach(el => {
            if (!el.offsetParent) return;
            const rect = el.getBoundingClientRect();
            const cy = rect.top + rect.height / 1.5;
            if (prevLineY >= cy && lineY < cy) {
                spawnOrPulseScanClone(el, { soft: el.matches('.wiseScanTargetSoft, [data-wise-glow="soft"]') });
                // window.soundFX?.hit();
            }
        });
        prevLineY = lineY;
        requestAnimationFrame(runWiseScanOverlayFrame);
    }

    wiseScanOverlay.classList.add('scan-start');
    requestAnimationFrame(runWiseScanOverlayFrame);

    if (wiseScanline) {
        const onScanEnd = () => {
			closeWiseScanOverlay();
        };
        wiseScanline.addEventListener('animationend', onScanEnd, { once: true });
    } else {
		closeWiseScanOverlay();
    }
}

function sanitizeScanCloneTree(root){
    root.setAttribute('aria-hidden', 'true');
    root.inert = true;
    const ATTRS_TO_STRIP = ['id','for','aria-labelledby','aria-describedby','aria-controls','aria-owns','aria-activedescendant'];
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT, null);
    let node = root;
    while (node){
        ATTRS_TO_STRIP.forEach(attr => {
            if (node.hasAttribute && node.hasAttribute(attr)){
                const val = node.getAttribute(attr);
                node.setAttribute('data-scan-src-' + attr, val);
                node.removeAttribute(attr);
            }
        });
        if (node.tabIndex !== undefined) node.tabIndex = -1;
        node = walker.nextNode();
    }
}


function showWiseWaitOverlay() {
	
    showWiseOverlay(wiseWaitOverlay, { interactive: true });
    showWiseOverlay(wiseScanOverlay, { interactive: false });

	setTimeout(() => {
		openAllWiseToolbars();
	}, 300);

    wiseScanOverlay?.classList.remove('scan-start', 'scan-finish');
    if (wiseScanline) {
        wiseScanline.style.animation = 'none';
        void wiseScanline.offsetWidth;
        wiseScanline.style.animation = '';
    }
}


if (wiseWaitOverlay !== null) {
    showWiseWaitOverlay();
}
