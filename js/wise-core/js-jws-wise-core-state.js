function saveState(str_title, options = {}) {

    const mergeKey = String(options.mergeKey || '');
    const mergeMs = Number(options.mergeMs || 0);
    const now = Date.now();

    // スナップショット作成（既存処理そのまま）
    let movableElementsSnapshot = [];

	let elms_movableContainer =
		wisePanelWhiteboardViewMainContentArea.querySelectorAll(
			'.movableContainer:not([data-ignore-history="1"])'
		);

    elms_movableContainer.forEach(parentElement => {
        movableElementsSnapshot.push(recordElement(parentElement));
    });

    const strokeHistorySnapshot = (typeof structuredClone === 'function')
        ? structuredClone(strokeHistory)
        : JSON.parse(JSON.stringify(strokeHistory));

    const newHistoryData = {
        title: str_title,
        movableElementsSnapshot: movableElementsSnapshot,
        strokeHistory: strokeHistorySnapshot,
        canvasMeta: {
            width: wiseCanvasHandWriting.width,
            height: wiseCanvasHandWriting.height
        },

        // ★ 追加：マージ判定用
        _meta: {
            mergeKey: mergeKey,
            ts: now
        }
    };

    // ★ ここがポイント：直近履歴が同一mergeKeyかつmergeMs以内なら「上書き」
    const last = operationHistory[currentHistoryIndex];
    const canMerge = (
        mergeKey !== '' &&
        last &&
        last._meta &&
        last._meta.mergeKey === mergeKey &&
        Number.isFinite(last._meta.ts) &&
        (now - last._meta.ts) <= mergeMs
    );

    if (canMerge) {
        operationHistory[currentHistoryIndex] = newHistoryData;
        markWhiteboardDirty();
        return;
    }

    // ---- ここから下は従来の push 処理（元のまま） ----
    if (operationHistory.length >= MAX_HISTORY_COUNT) {
        operationHistory.shift();
        currentHistoryIndex--;
    }

    currentHistoryIndex++;
    operationHistory.splice(currentHistoryIndex);
    operationHistory.push(newHistoryData);

    markWhiteboardDirty();
}

function undo() {
	if(operationHistory.length === LENGTH_EMPTY)return;
	if (currentHistoryIndex > INDEX_FIRST) {
		currentHistoryIndex--;
		restoreStateFromHistory();
	}
}

function redo() {
	if (currentHistoryIndex < operationHistory.length - LAST_INDEX_OFFSET) {
		currentHistoryIndex++;
		restoreStateFromHistory();
	}
}

function restoreStateFromHistory() {

    const state = operationHistory[currentHistoryIndex];
    if (!state) return;

    if (state.canvasMeta) {
        const w = state.canvasMeta.width;
        const h = state.canvasMeta.height;

        if (Number.isFinite(w) && Number.isFinite(h)) {
            if (wiseCanvasHandWriting.width !== w) wiseCanvasHandWriting.width = w;
            if (wiseCanvasHandWriting.height !== h) wiseCanvasHandWriting.height = h;

            if (wiseCanvasOriginal.width !== w) wiseCanvasOriginal.width = w;
            if (wiseCanvasOriginal.height !== h) wiseCanvasOriginal.height = h;
        }
    }

    wiseCanvasOriginalContext.setTransform(1, 0, 0, 1, 0, 0);
    wiseCanvasHandWritingContext.setTransform(1, 0, 0, 1, 0, 0);

    wiseCanvasOriginalContext.clearRect(0, 0, wiseCanvasOriginal.width, wiseCanvasOriginal.height);
    wiseCanvasHandWritingContext.clearRect(0, 0, wiseCanvasHandWriting.width, wiseCanvasHandWriting.height);

    const history = Array.isArray(state.strokeHistory) ? state.strokeHistory : [];

    strokeHistory = (typeof structuredClone === 'function')
        ? structuredClone(history)
        : JSON.parse(JSON.stringify(history));

    redrawFromStrokeHistory(
        wiseCanvasHandWritingContext,
        wiseCanvasHandWriting,
        strokeHistory
    );

    const movableElementsSnapshot = Array.isArray(state.movableElementsSnapshot)
        ? state.movableElementsSnapshot
        : [];

    document.querySelectorAll('.movableContainer').forEach(elm => {
        wisePanelWhiteboardViewMainContentArea.removeChild(elm);
    });

    document.querySelectorAll('.wiseCanvasLinked').forEach(elm => {
        canvasLinkedContainer.removeChild(elm);
    });

    const restoredMovableContainers = [];

    movableElementsSnapshot.forEach(snapshot => {
        const elm_restored = recreateElement(snapshot);
        wisePanelWhiteboardViewMainContentArea.appendChild(elm_restored);
        restoredMovableContainers.push(elm_restored);
    });

    requestAnimationFrame(() => {
        restoredMovableContainers.forEach(elm_movableContainer => {

            if (!(elm_movableContainer instanceof HTMLElement)) {
                return;
            }

            const elm_textarea = elm_movableContainer.querySelector('.innerContainerTextArea');

            if (elm_textarea instanceof HTMLTextAreaElement) {
                updateTextareaContainerSize(elm_textarea, elm_textarea.value || '');
            }
        });

        recreateLinkedCanvases();
    });
}

function recreateLinkedCanvases(){
	const movableElements = document.querySelectorAll('.movableContainer');
	movableElements.forEach(element => {
		const linkId = element.getAttribute('data-link-id');
		if (linkId) {
			createLinkedCanvas(canvasLinkedContainer, element, wiseCanvasContainer);
		}
	});
}

function recordElement(element) {

	const elementClasses = element.classList;
	const filteredClasses = Array.from(elementClasses).filter(className => !TEMPORARY_MOVABLE_CONTAINER_CLASSES.includes(className));

	const snapshot = {
		id: element.id,
		tag: element.tagName,
		class: filteredClasses.join(' '),
		style: element.getAttribute("style"),
		data: {},
		children: []
	};

	const dataAttributes = element.getAttributeNames().filter(attr => attr.startsWith("data-"));
	dataAttributes.forEach(attr => {
		snapshot.data[attr] = element.getAttribute(attr);
	});

	if (INNER_CONTAINER_NAMES.some(className => element.classList.contains(className))) {
		snapshot.textContent = element.textContent;
	}
	if (element.classList.contains('innerContainerTextArea')) {
		snapshot.textContent = element.value;
	}

	element.childNodes.forEach(child => {
		if (child.nodeType === Node.ELEMENT_NODE) {
			snapshot.children.push(recordElement(child));
		}
	});

	return snapshot;
}

function recreateElement(snapshot) {
	const newElement = document.createElement(snapshot.tag);
	newElement.id = escapeHTML(snapshot.id);
	newElement.className = escapeHTML(snapshot.class);
	if(snapshot.style !== null){
		newElement.style = escapeHtmlAttribute(snapshot.style);
	}

	for (const attr in snapshot.data) {
		const escapedValue = escapeHTML(snapshot.data[attr]);
		newElement.setAttribute(attr, escapedValue);
	}

	if (snapshot.textContent !== undefined) {

		if (INNER_CONTAINER_NAMES.some(className => snapshot.class.includes(className))) {
			newElement.textContent = escapeHTML(snapshot.textContent);
		}
		if (snapshot.class.includes('innerContainerTextArea')) {
			newElement.value = escapeHTML(snapshot.textContent);
		}
	}

	snapshot.children.forEach(childSnapshot => {
		const newChildElement = recreateElement(childSnapshot);
		newElement.appendChild(newChildElement);
	});

	return newElement;
}



function markWhiteboardDirty() {
    isWhiteboardDirty = true;
}

function sendWhiteboardAutosaveTick() {

    if (!isActiveTab) return;
    if (!isWhiteboardDirty) return;

    const now = Date.now();
    if ((now - lastWhiteboardSentAt) < WHITEBOARD_AUTOSAVE_MIN_INTERVAL_MS) return;

    sendWhiteboardStateIfNeeded(false).catch(e => {
        console.error('sendWhiteboardStateIfNeeded error:', e);
    });
}

async function sendWhiteboardStateIfNeeded(force = false) {

    if (!isActiveTab) return;

    if (!force) {
        if (!wiseSetupRecordLessonToggle || !wiseSetupRecordLessonToggle.checked) {
            return;
        }
    }

    const state = operationHistory[currentHistoryIndex];
    if (!state) return;

    if (!wiseSetupLessonDateSelect || !wiseSetupWhiteboardSelect) {
        return;
    }

    const lesson_date_id = Number(wiseSetupLessonDateSelect.value || 0);
    const whiteboard_id = Number(wiseSetupWhiteboardSelect.value || 0);

    if (lesson_date_id <= 0 || whiteboard_id <= 0) {
        return;
    }

    const payload = {
        lesson_date_id: lesson_date_id,
        whiteboard_id: whiteboard_id,
        state: state,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        wiseCoreSaveWhiteboardStateUrl,
        payload,
        10000
    );

    if (!result || result.status !== 'success') {
        throw new Error('whiteboard save failed');
    }

    lastWhiteboardSentAt = Date.now();
    isWhiteboardDirty = false;
}

function nowAsNumber() {
    return new Date().getTime();
}

function setupAutosave(getValue, getMeta, saveFn, options = {}) {

    if (typeof getValue !== 'function') {
        return {
            markDirty: function () {},
            debouncedSave: function () {},
            flushSave: async function () {},
            tick: function () {},
            teardown: function () {}
        };
    }

    const debounceMs = Number(options.debounceMs ?? 3000);
    const intervalMs = Number(options.intervalMs ?? 0);
    const minLength = Number(options.minLength ?? 0);

    const onStatus = typeof options.onStatus === 'function' ? options.onStatus : null;
    const isSame = typeof options.isSame === 'function'
        ? options.isSame
        : function (a, b) { return a === b; };

    const makePayload = typeof options.makePayload === 'function'
        ? options.makePayload
        : function (value, meta) { return { ...meta, value: value }; };

    let lastSavedValue = getValue();
    let isSaving = false;
    let dirty = false;
    let pendingAfterSave = false;
    let intervalId = null;

    function setStatus(status, detail = '') {
        if (onStatus) {
            onStatus(status, detail);
        }
    }

    function markDirty(reason = 'mark_dirty') {
        dirty = true;
        setStatus('dirty', reason);
    }

    async function flushSave(reason) {

        const currentValue = getValue();

        if (typeof currentValue === 'string' && currentValue.length < minLength) {
            dirty = false;
            return;
        }

        if (!dirty && isSame(currentValue, lastSavedValue)) {
            return;
        }

        if (isSame(currentValue, lastSavedValue)) {
            dirty = false;
            return;
        }

        dirty = true;

        if (isSaving) {
            pendingAfterSave = true;
            return;
        }

        isSaving = true;
        setStatus('saving', reason);

        try {
            const meta = getMeta ? getMeta() : {};
            const payload = makePayload(currentValue, meta);

            const result = await saveFn(payload);

            lastSavedValue = currentValue;
            dirty = false;

            setStatus('saved', reason);

            if (pendingAfterSave) {
                pendingAfterSave = false;
                await flushSave('pending_after_save');
            }

            return result;
        } catch (e) {
            dirty = true;
            setStatus('error', reason);
            throw e;
        } finally {
            isSaving = false;
        }
    }

    const debouncedSave = createDebounce(() => {
        flushSave('debounce').catch(() => {});
    }, debounceMs);

    function runAutosaveTick() {
        if (!dirty) {
            return;
        }
        flushSave('tick').catch(() => {});
    }

    function onVisibilityChange() {
        if (document.visibilityState === 'hidden') {
            flushSave('visibility_hidden').catch(() => {});
        }
    }

    function onPageHide() {
        if (!dirty) {
            return;
        }
        flushSave('pagehide').catch(() => {});
    }

    document.addEventListener('visibilitychange', onVisibilityChange);
    window.addEventListener('pagehide', onPageHide);

    if (intervalMs > 0) {
        intervalId = window.setInterval(() => {
            runAutosaveTick();
        }, intervalMs);
    }

    setStatus('ready', 'init');

    return {
        markDirty,
        debouncedSave,
        flushSave,
        tick: runAutosaveTick,
        teardown: function () {
            document.removeEventListener('visibilitychange', onVisibilityChange);
            window.removeEventListener('pagehide', onPageHide);

            if (intervalId !== null) {
                window.clearInterval(intervalId);
                intervalId = null;
            }
        }
    };
}

function makeWhiteboardSignature(state) {
    const strokeCount = Array.isArray(state?.strokeHistory) ? state.strokeHistory.length : 0;
    const movableCount = Array.isArray(state?.movableElementsSnapshot) ? state.movableElementsSnapshot.length : 0;
    return String(state?.title ?? '') + '|' + String(strokeCount) + '|' + String(movableCount);
}

function setupStateAutosave(getState, getMeta, saveFn, options = {}) {

    return setupAutosave(
        () => getState(),
        getMeta,
        saveFn,
        {
            ...options,
            makePayload: function (value, meta) {
                return {
                    ...meta,
                    state: value
                };
            },
            isSame: function (a, b) {
                return makeWhiteboardSignature(a) === makeWhiteboardSignature(b);
            }
        }
    );
}