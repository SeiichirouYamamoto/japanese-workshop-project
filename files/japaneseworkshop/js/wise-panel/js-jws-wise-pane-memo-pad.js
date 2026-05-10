
const memoPadDateSelect = document.getElementById('wisePanelMemoPadViewDateSelect');
const memoPadConfirmButton = document.getElementById('wisePanelMemoPadViewConfirmButton');
const memoPadTextareaContainer = document.getElementById('wisePanelMemoPadViewTextareaContainer');


let memoAutosaveTeardown = null;
let currentMemoState = {
    memoId: 0,
    lessonDateId: 0
};

function setCurrentMemoState(textarea) {
    currentMemoState.memoId = Number(textarea?.dataset.memoId ?? 0);
    currentMemoState.lessonDateId = Number(textarea?.dataset.memoDateId ?? 0);
}




if (memoPadDateSelect) {
    memoPadDateSelect.addEventListener('change', function () {
        const v = String(memoPadDateSelect.value || '');
        if (v === '') {
            clearMemoPadTextareas();
        }
    });
}


if (memoPadConfirmButton) {
    memoPadConfirmButton.addEventListener('pointerup', async function () {
        await handleMemoPadConfirmClick();
    });
}


function clearMemoPadTextareas() {

    if (!memoPadTextareaContainer) {
        return;
    }

    memoPadTextareaContainer
        .querySelectorAll('textarea')
        .forEach(elm => elm.remove());
}

function clearMemoPadTextareasVisibility() {
    const textareas = document.querySelectorAll(
        '.wisePanelMemoPadViewTextarea'
    );

    textareas.forEach(textarea => {
        textarea.classList.remove('visible');
    });
}


async function updateRoomMemoDateSelect(disabled) {

    if (disabled) {
        createRoomMemoDateOptions(memoPadDateSelect, []);
        return;
    }

    const room_unique_code =
        escapeHTML(wiseSetupRoomSelect.value) || 'default';

    const memoDates = await fetchRoomLessonDates(room_unique_code);
    createRoomMemoDateOptions(memoPadDateSelect, memoDates);
}




function createRoomMemoDateOptions(selectElm, dates) {

    if (!selectElm) {
        return;
    }

    Array.from(selectElm.options).forEach(option => {
        if (option.dataset.fixed !== '1') {
            option.remove();
        }
    });

    for (const row of dates) {
        const option = document.createElement('option');
        option.value = String(row.lesson_date_id);
        option.textContent = row.label;
        selectElm.appendChild(option);
    }
}

async function handleMemoPadConfirmClick() {

    const elmLoading = document.getElementById('wisePanelMemoPadViewLoading');
	if (elmLoading === null) {
		return;
	}

	clearMemoPadTextareasVisibility();
	elmLoading.classList.remove('loading-hidden');

    try {

        if (!memoPadDateSelect) {
            return;
        }

        const selectedValue = String(memoPadDateSelect.value || '');
        if (selectedValue === '') {
            return;
        }

        const room_unique_code =
            escapeHTML(wiseSetupRoomSelect.value) || 'default';

        const memo =
            await fetchOrCreateRoomMemo(
                room_unique_code,
                selectedValue
            );

        applyMemoToTextarea(memo);

    } finally {

        /* --------------------
            loading OFF
        -------------------- */
        elmLoading.classList.add('loading-hidden');
    }
}



async function fetchOrCreateRoomMemo(room_unique_code, lesson_date_id) {

    const payload = {
        room_unique_code: room_unique_code,
        lesson_date_id: lesson_date_id,
        int_selected_language: intSelectedLanguage
    };

    try {

        const result = await postJson(wiseCoreGetOrCreateMemoUrl, payload);

        const isOk = (
            result &&
            result.status === 'success' &&
            result.data
        );

        if (!isOk) {
            console.error(result?.data?.message || result?.message || 'fetchOrCreateRoomMemo Error');
            return null;
        }

        return result.data;

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            alert('fetchOrCreateRoomMemo タイムアウト');
        } else {
            alert(error.message);
        }

        return null;
    }
}


function getOrCreateMemoTextarea(memo) {

    if (!memoPadTextareaContainer || !memo) {
        return null;
    }

    const memoDateId = memo.lesson_date_id !== undefined ? String(memo.lesson_date_id) : '';
    const memoId = memo.memo_id !== undefined ? String(memo.memo_id) : '';

    if (memoDateId === '') {
        return null;
    }

    // まず lesson_date_id で探す（現段階はこれが一意）
    let textarea = memoPadTextareaContainer.querySelector(
        `textarea[data-memo-date-id="${CSS.escape(memoDateId)}"]`
    );

    if (textarea) {
        // 既存があれば memo_id を更新しておく（API側で作成された場合など）
        if (memoId !== '') {
            textarea.dataset.memoId = memoId;
        }
        return textarea;
    }

    // 無ければ作る
    textarea = document.createElement('textarea');
	textarea.classList.add('wisePanelMemoPadViewTextarea', 'wiseUiFontSizeTarget');
    textarea.dataset.memoDateId = memoDateId;
	textarea.placeholder = 'memo';

    if (memoId !== '') {
        textarea.dataset.memoId = memoId;
    }

    // 将来複数化したときのため
    if (memo.memo_order !== undefined && memo.memo_order !== null) {
        textarea.dataset.memoOrder = String(memo.memo_order);
    }

    // UI要件に合わせて必要なら付与
    textarea.setAttribute('aria-label', 'Memo textarea');

    memoPadTextareaContainer.appendChild(textarea);

	applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);

    return textarea;
}

function showMemoPadTextarea(elm_targetTextarea) {

    // 全 textarea を非表示
    clearMemoPadTextareasVisibility();

    if (!elm_targetTextarea) {
		currentMemoState.memoId = 0;
        currentMemoState.lessonDateId = 0;

        if (typeof memoAutosaveTeardown === 'function') {
			memoAutosaveTeardown();
			memoAutosaveTeardown = null;
		}
        return;
    }

    elm_targetTextarea.classList.add('visible');
	setCurrentMemoState(elm_targetTextarea);

    // 旧 autosave を解除
    if (typeof memoAutosaveTeardown === 'function') {
		memoAutosaveTeardown();
		memoAutosaveTeardown = null;
	}

    // textarea が表すメモ情報
    function getMeta() {
        return {
            lesson_date_id: Number(elm_targetTextarea.dataset.memoDateId ?? 0),
            memo_id: Number(elm_targetTextarea.dataset.memoId ?? 0),
            int_selected_language: intSelectedLanguage
        };
    }

    // 保存処理（postJson は data を返す前提）
    async function saveFn(payload) {

        const result = await postJson(wiseCoreUpdateMemoUrl, payload);

		if (!result || result.status !== 'success' || !result.data) {
			throw new Error(result?.data?.message || result?.message || 'update memo failed');
		}

		const data = result.data;

        // 初回 insert 時に memo_id が返ったら反映
        if (data?.memo_id != null) {
            elm_targetTextarea.dataset.memoId = String(data.memo_id);
        }

        return data;
    }

    // 状態フック（必要になったら実装）
    function onStatus(status, reason) {
        // status: 'ready' | 'dirty' | 'saving' | 'saved' | 'error'
    }

    const autosave = setupMemoAutosave(
		elm_targetTextarea,
		getMeta,
		saveFn,
		{
			debounceMs: 3000,
			intervalMs: 60000,
			minLength: 0,
			onStatus: onStatus
		}
	);

	memoAutosaveTeardown = autosave.teardown;
}


function applyMemoToTextarea(memo) {

    if (!memoPadTextareaContainer) {
        return;
    }

    const textarea = getOrCreateMemoTextarea(memo);
    if (!textarea) {
        return;
    }

    textarea.value =
        memo && typeof memo.memo_text === 'string'
            ? memo.memo_text
            : '';

    // ★ デフォルト挙動：この memo だけ表示
    showMemoPadTextarea(textarea);
}

function setupMemoAutosave(textarea, getMeta, saveFn, options = {}) {

    if (!textarea) {
        return { teardown: function () {} };
    }

    const autosave = setupAutosave(
        () => String(textarea.value ?? ''),
        getMeta,
        saveFn,
        {
            ...options,
            makePayload: function (value, meta) {
                return {
                    ...meta,
                    memo_text: value
                };
            },
            isSame: function (a, b) { return a === b; }
        }
    );

    function onInput() {
        autosave.markDirty('input');
        autosave.debouncedSave();
    }

    function onBlur() {
        autosave.flushSave('blur').catch(() => {});
    }

    textarea.addEventListener('input', onInput);
    textarea.addEventListener('blur', onBlur);

    const originalTeardown = autosave.teardown;
    autosave.teardown = function () {
        textarea.removeEventListener('input', onInput);
        textarea.removeEventListener('blur', onBlur);
        originalTeardown();
    };

    return autosave;
}