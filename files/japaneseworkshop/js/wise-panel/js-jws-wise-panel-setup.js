const wiseSetupPanel = document.getElementById('wisePanelWiseSetupView');
const wiseSetupRecordLessonToggle = document.getElementById('wisePanelWiseSetupViewRecordLessonToggleInput');

const wiseSetupSelectRoomArea = document.getElementById('wisePanelWiseSetupViewDropDownSelectRoomArea');
const wiseSetupSelectLessonDateArea = document.getElementById('wisePanelWiseSetupViewDropDownSelectLessonDateArea');
const wiseSetupSelectWhiteboardArea = document.getElementById('wisePanelWiseSetupViewDropDownSelectWhiteboardArea');

const wiseSetupRoomSelect = document.getElementById('wisePanelWiseSetupViewSelectRoom');
const wiseSetupLessonDateSelect = document.getElementById('wisePanelWiseSetupViewSelectLessonDate');
const wiseSetupWhiteboardSelect = document.getElementById('wisePanelWiseSetupViewSelectWhiteboard');

const wiseSetupRoomConfirmButton = document.getElementById('wisePanelWiseSetupViewSelectRoomConfirmButton');
const wiseSetupLessonDateConfirmButton = document.getElementById('wisePanelWiseSetupViewSelectLessonDateConfirmButton');
const wiseSetupWhiteboardConfirmButton = document.getElementById('wisePanelWiseSetupViewSelectWhiteboardConfirmButton');

const wiseSetupCurrentLessonInfo = document.getElementById('wisePanelWiseSetupViewCurrentLessonInfo');
const wiseSetupCurrentWhiteboardInfo = document.getElementById('wisePanelWiseSetupViewCurrentWhiteboardInfo');

if (wiseSetupRecordLessonToggle !== null) {

    wiseSetupRecordLessonToggle.addEventListener('change', async function () {

        setUiLock(true);

        try {

            // ONにした
            if (this.checked) {

                if (wiseSetupRoomSelect) {
                    setWiseSetupSelectUiState(wiseSetupRoomSelect, wiseSetupRoomConfirmButton, true);
                    setSelectLoading(wiseSetupRoomSelect, 'Loading rooms...');
                }

                // UI：room選択を表示、日付は一旦隠す（room確定後に出す）
                if (wiseSetupSelectRoomArea) wiseSetupSelectRoomArea.classList.remove('hidden');
                if (wiseSetupSelectLessonDateArea) wiseSetupSelectLessonDateArea.classList.add('hidden');
                if (wiseSetupSelectWhiteboardArea) wiseSetupSelectWhiteboardArea.classList.add('hidden');

                try {

                    const payload = {
                        int_selected_language: intSelectedLanguage
                    };

                    const result = await postJson(
                        roomGetLessonSelectOptionsUrl,
                        payload,
                        10000
                    );

                    const data = result.data;

                    if (wiseSetupRoomSelect && typeof data === 'string') {
                        wiseSetupRoomSelect.innerHTML = data;
                    }

                    setWiseSetupSelectUiState(wiseSetupRoomSelect, wiseSetupRoomConfirmButton, false);

                } catch (error) {

                    // 失敗したらONを取り消して元に戻す
                    this.checked = false;

                    if (wiseSetupSelectRoomArea) wiseSetupSelectRoomArea.classList.add('hidden');
                    if (wiseSetupSelectLessonDateArea) wiseSetupSelectLessonDateArea.classList.add('hidden');
                    if (wiseSetupSelectWhiteboardArea) wiseSetupSelectWhiteboardArea.classList.add('hidden');

                    if (error.message && error.message.includes('タイムアウト')) {
                        console.error('タイムアウトが発生しました。');
                        alert('タイムアウトが発生しました。');
                    } else {
                        console.error('Error:', error.message || error);
                        alert(error.message || 'Error');
                    }
                }

                return;
            }

            // OFFにした（しようとした）
            const ok = window.confirm(MSG_LESSON_RECORD_END_CONFIRM[intSelectedLanguage]);

            if (!ok) {
                // キャンセルならONに戻す
                this.checked = true;
                return;
            }

            try {
                await sendWhiteboardStateIfNeeded(true);
            } catch (e) {
                console.error('final save failed:', e);
            }

            resetWhiteboardMemory();
            resetGrammarExplanationMemory();
            unlockWhiteboardSetupUi();

            // OKならOFF確定：UIも初期状態へ
            if (wiseSetupSelectRoomArea) wiseSetupSelectRoomArea.classList.add('hidden');
            if (wiseSetupSelectLessonDateArea) wiseSetupSelectLessonDateArea.classList.add('hidden');
            if (wiseSetupSelectWhiteboardArea) wiseSetupSelectWhiteboardArea.classList.add('hidden');

            // ここで必要なら、選択状態や内部stateもクリア
            wiseSetupRoomSelect.value = '';
            wiseSetupLessonDateSelect.value = '';
            updateCurrentLessonInfo('');
            updateCurrentWhiteboardInfo('');
            resetLessonContents();
            setRoomToolsState(false);
            resetWisePanelTitles();
            resetSharedContentsSelection();

        } finally {

            setUiLock(false);

        }

    }, { passive: false });
}

if (wiseSetupRoomConfirmButton !== null) {
	prevIndex = wiseSetupRoomSelect.selectedIndex;
	setRoomToolsState(false);
	wiseSetupRoomConfirmButton.addEventListener('pointerup', async function () {

		setUiLock(true);

		try {

			const checkedCheckboxes = lessonContentsPanelView.querySelectorAll(
				'input[type="checkbox"].grammarOutlineGrammarCheckbox:checked'
			);

			if (
				checkedCheckboxes.length > LENGTH_EMPTY &&
				!window.confirm(MSG_GRAMMAR_SELECTED_CONFIRM[intSelectedLanguage])
			) {
				wiseSetupRoomSelect.selectedIndex = prevIndex;
				return;
			}

			setRoomToolsState(false);

			const ok = await handleRoomSelectChangeForLessonRange();

			if (ok) {
				if (wiseSetupSelectLessonDateArea) {
					wiseSetupSelectLessonDateArea.classList.remove('hidden');
					if (wiseSetupSelectWhiteboardArea) wiseSetupSelectWhiteboardArea.classList.add('hidden');
					updateCurrentLessonInfo('');
					updateCurrentWhiteboardInfo('');
					await updateRoomLessonDateSelect(false);
				}
				updateWisePanelTitlesWithRoomName();
			}

		} finally {

			setUiLock(false);

		}

	}, false);
}


/* ------------------------------
    Lesson Date Select (WISE setup)
    memo pad の date select と同構造
------------------------------ */

if (wiseSetupLessonDateSelect) {
    wiseSetupLessonDateSelect.addEventListener('change', function () {
        const v = String(wiseSetupLessonDateSelect.value || '');
        if (v === '') {
            // 必要なら、ここで lesson contents の表示などをクリアする
            // resetLessonContents();
        }
    });
}

if (wiseSetupLessonDateConfirmButton) {
    wiseSetupLessonDateConfirmButton.addEventListener('pointerup', async function () {
        await handleLessonDateConfirmClick();
    }, false);
}

async function handleLessonDateConfirmClick() {

	setUiLock(true);

    try {

        if (!wiseSetupLessonDateSelect) {
            return;
        }

        const selectedValue = String(wiseSetupLessonDateSelect.value || '');
        if (selectedValue === '') {
            return;
        }

        const room_unique_code =
            escapeHTML(wiseSetupRoomSelect?.value) || 'default';

        if (selectedValue === 'create_new') {

            const created =
                await createRoomLessonDate(room_unique_code);

            if (!created || !created.lesson_date_id) {
                return;
            }

            await updateRoomLessonDateSelect(false);

            wiseSetupLessonDateSelect.value =
                String(created.lesson_date_id);

            const label =
                typeof created.label === 'string' && created.label !== ''
                    ? created.label
                    : getSelectedLessonDateLabel();

            updateCurrentLessonInfo(label);

            await syncMemoPadDateSelectToLessonDates(
                room_unique_code,
                created.lesson_date_id
            );

            if (wiseSetupSelectWhiteboardArea) {
                wiseSetupSelectWhiteboardArea.classList.remove('hidden');
                await updateRoomWhiteboardSelect(false);
                updateCurrentWhiteboardInfo('');
            }

            return;
        }

        updateCurrentLessonInfo(getSelectedLessonDateLabel());

        await syncMemoPadDateSelectToLessonDates(
            room_unique_code,
            selectedValue
        );

        if (wiseSetupSelectWhiteboardArea) {
            wiseSetupSelectWhiteboardArea.classList.remove('hidden');
            await updateRoomWhiteboardSelect(false);
            updateCurrentWhiteboardInfo('');
        }

    } finally {

        setUiLock(false);

    }
}

/* ------------------------------
    Whiteboard Select (WISE setup)
    lesson date と同構造
------------------------------ */

if (wiseSetupWhiteboardSelect) {
    wiseSetupWhiteboardSelect.addEventListener('change', function () {
        const v = String(wiseSetupWhiteboardSelect.value || '');
        if (v === '') {
            // 必要なら、ここで whiteboard 表示や内部状態をクリア
            // resetWhiteboard();
        }
    });
}

if (wiseSetupWhiteboardConfirmButton) {
    wiseSetupWhiteboardConfirmButton.addEventListener('pointerup', async function () {
        await handleWhiteboardConfirmClick();
    }, false);
}







/* ------------------------------
    lesson_date 関数群
------------------------------ */
function getSelectedLessonDateLabel() {

    if (!wiseSetupLessonDateSelect) {
        return '';
    }

    const opt =
        wiseSetupLessonDateSelect.options[wiseSetupLessonDateSelect.selectedIndex];

    return opt ? String(opt.textContent || '') : '';
}

/**
 * disabled=true なら options を初期化して終了
 * disabled=false なら APIから dates を取得して options を構築
 */
async function updateRoomLessonDateSelect(disabled) {

    if (!wiseSetupLessonDateSelect) {
        return;
    }

    if (disabled) {
        setWiseSetupSelectUiState(
            wiseSetupLessonDateSelect,
            wiseSetupLessonDateConfirmButton,
            true
        );
        clearSelectOptionsExceptFixed(wiseSetupLessonDateSelect);
        beginSelectEmpty(wiseSetupLessonDateSelect, '---');
        return;
    }

    setWiseSetupSelectUiState(
        wiseSetupLessonDateSelect,
        wiseSetupLessonDateConfirmButton,
        true
    );
    beginSelectLoading(wiseSetupLessonDateSelect, 'Loading lesson dates...');

    const room_unique_code =
        escapeHTML(wiseSetupRoomSelect?.value) || 'default';

    const lessonDates = await fetchRoomLessonDates(room_unique_code);

    // options 再構築（内部で endSelectUi + clearExceptFixed 済み）
    createRoomLessonDateOptions(wiseSetupLessonDateSelect, Array.isArray(lessonDates) ? lessonDates : []);

    if (!Array.isArray(lessonDates) || lessonDates.length === 0) {
        beginSelectEmpty(wiseSetupLessonDateSelect, 'No lesson dates');
    }

    setWiseSetupSelectUiState(
        wiseSetupLessonDateSelect,
        wiseSetupLessonDateConfirmButton,
        false
    );
}

/**
 * API: 指定 room の lesson_dates を配列で返す
 * 期待する返却形式:
 *  [
 *    { lesson_date_id: 123, label: '2026-02-26 (1)' },
 *    ...
 *  ]
 */
async function fetchRoomLessonDates(room_unique_code) {

    const payload = {
        room_unique_code: room_unique_code,
        int_selected_language: intSelectedLanguage
    };

    try {

        const result = await postJson(wiseCoreGetLessonDatesUrl, payload);

        const isOk = (
            result &&
            result.status === 'success' &&
            Array.isArray(result.data)
        );

        if (!isOk) {
            console.error(result?.data?.message || result?.message || 'fetchRoomLessonDates Error');
            return [];
        }

        return result.data;

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            alert('fetchRoomLessonDates タイムアウト');
        } else {
            alert(error.message || 'fetchRoomLessonDates Error');
        }

        return [];
    }
}


/**
 * dates を select に流し込む
 * memo pad と同じく「fixed option 以外を消してから追加」
 */
function createRoomLessonDateOptions(selectElm, dates) {

    if (!selectElm) {
        return;
    }

    // fixedのラベル復元 + ui option削除（loading/empty を残さない）
    endSelectUi(selectElm);

    // fixed以外は削除（従来どおり）
    Array.from(selectElm.options).forEach(option => {
        if (option.dataset.fixed !== '1') {
            option.remove();
        }
    });

    if (!Array.isArray(dates) || dates.length === 0) {
        return;
    }

    for (const row of dates) {

        const lesson_date_id = row?.lesson_date_id;
        if (lesson_date_id === undefined || lesson_date_id === null) {
            continue;
        }

        const option = document.createElement('option');
        option.value = String(lesson_date_id);
        option.textContent = String(row?.label || '');

        selectElm.appendChild(option);
    }
}

function updateCurrentLessonInfo(text) {

    if (!wiseSetupCurrentLessonInfo) {
        return;
    }

    const emptyText =
        String(wiseSetupCurrentLessonInfo.dataset.emptyText || '---');

    const v = String(text || '').trim();
    wiseSetupCurrentLessonInfo.textContent =
        v !== '' ? v : emptyText;
}

async function createRoomLessonDate(room_unique_code) {

    const payload = {
        room_unique_code: room_unique_code,
        int_selected_language: intSelectedLanguage
    };

    try {

        const result =
            await postJson(wiseCoreCreateLessonDateUrl, payload);

        const isOk = (
            result &&
            result.status === 'success' &&
            result.data
        );

        if (!isOk) {
            console.error(
                result?.data?.message ||
                result?.message ||
                'createRoomLessonDate Error'
            );
            return null;
        }

        return result.data;

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            alert('createRoomLessonDate タイムアウト');
        } else {
            alert(error.message || 'createRoomLessonDate Error');
        }

        return null;
    }
}

/* ------------------------------
    whiteboard 関数群
------------------------------ */
function getSelectedWhiteboardLabel() {

    if (!wiseSetupWhiteboardSelect) {
        return '';
    }

    const opt =
        wiseSetupWhiteboardSelect.options[wiseSetupWhiteboardSelect.selectedIndex];

    return opt ? String(opt.textContent || '') : '';
}

function updateCurrentWhiteboardInfo(text) {

    if (!wiseSetupCurrentWhiteboardInfo) {
        return;
    }

    const emptyText =
        String(wiseSetupCurrentWhiteboardInfo.dataset.emptyText || '---');

    const v = String(text || '').trim();
    wiseSetupCurrentWhiteboardInfo.textContent =
        v !== '' ? v : emptyText;
}

/**
 * disabled=true なら options を初期化して終了
 * disabled=false なら APIから whiteboards を取得して options を構築
 */
async function updateRoomWhiteboardSelect(disabled) {

    if (!wiseSetupWhiteboardSelect) {
        return;
    }

    if (disabled) {
        setWiseSetupSelectUiState(
            wiseSetupWhiteboardSelect,
            wiseSetupWhiteboardConfirmButton,
            true
        );
        clearSelectOptionsExceptFixed(wiseSetupWhiteboardSelect);
        beginSelectEmpty(wiseSetupWhiteboardSelect, '---');
        return;
    }

    const room_unique_code =
        escapeHTML(wiseSetupRoomSelect.value) || 'default';

    const lesson_date_id =
        String(wiseSetupLessonDateSelect.value || '');

    if (lesson_date_id === '' || lesson_date_id === 'create_new') {
        setWiseSetupSelectUiState(
            wiseSetupWhiteboardSelect,
            wiseSetupWhiteboardConfirmButton,
            false
        );
        clearSelectOptionsExceptFixed(wiseSetupWhiteboardSelect);
        beginSelectEmpty(wiseSetupWhiteboardSelect, 'Select lesson date first');
        return;
    }

    setWiseSetupSelectUiState(
        wiseSetupWhiteboardSelect,
        wiseSetupWhiteboardConfirmButton,
        true
    );
    beginSelectLoading(wiseSetupWhiteboardSelect, 'Loading whiteboards...');

    const whiteboards = await fetchRoomWhiteboards(room_unique_code, lesson_date_id);

    // options 再構築（内部で endSelectUi + clearExceptFixed 済み）
    createRoomWhiteboardOptions(wiseSetupWhiteboardSelect, Array.isArray(whiteboards) ? whiteboards : []);

    if (!Array.isArray(whiteboards) || whiteboards.length === 0) {
        beginSelectEmpty(wiseSetupWhiteboardSelect, 'No whiteboards');
    }

    setWiseSetupSelectUiState(
        wiseSetupWhiteboardSelect,
        wiseSetupWhiteboardConfirmButton,
        false
    );
}
/**
 * API: 指定 room + lesson_date の whiteboards を配列で返す
 * 期待する返却形式:
 *  [
 *    { whiteboard_id: 10, label: 'Board 1' },
 *    ...
 *  ]
 */
async function fetchRoomWhiteboards(room_unique_code, lesson_date_id) {

    const payload = {
        room_unique_code: room_unique_code,
        lesson_date_id: Number(lesson_date_id),
        int_selected_language: intSelectedLanguage
    };

    try {

        // ※URL定数名は環境に合わせてください
        const result = await postJson(wiseCoreGetWhiteboardsUrl, payload);

        const isOk = (
            result &&
            result.status === 'success' &&
            Array.isArray(result.data)
        );

        if (!isOk) {
            console.error(result?.data?.message || result?.message || 'fetchRoomWhiteboards Error');
            return [];
        }

        return result.data;

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            alert('fetchRoomWhiteboards タイムアウト');
        } else {
            alert(error.message || 'fetchRoomWhiteboards Error');
        }

        return [];
    }
}


/**
 * whiteboards を select に流し込む
 * 「fixed option 以外を消してから追加」
 */
function createRoomWhiteboardOptions(selectElm, whiteboards) {

    if (!selectElm) {
        return;
    }

    // fixedのラベル復元 + ui option削除（loading/empty を残さない）
    endSelectUi(selectElm);

    // fixed以外は削除（従来どおり）
    Array.from(selectElm.options).forEach(option => {
        if (option.dataset.fixed !== '1') {
            option.remove();
        }
    });

    if (!Array.isArray(whiteboards) || whiteboards.length === 0) {
        return;
    }

    for (const row of whiteboards) {

        const whiteboard_id = row?.whiteboard_id;
        if (whiteboard_id === undefined || whiteboard_id === null) {
            continue;
        }

        const option = document.createElement('option');
        option.value = String(whiteboard_id);
        option.textContent = String(row?.label || '');

        selectElm.appendChild(option);
    }
}

async function createRoomWhiteboard(room_unique_code, lesson_date_id, state) {

    const payload = {
        room_unique_code: room_unique_code,
        // ご指定の payload（lesson_id, state, int_selected_language）に合わせます
        lesson_date_id: Number(lesson_date_id),
        state: state ?? null,
        int_selected_language: intSelectedLanguage
    };

    try {

        const result = await postJson(wiseCoreCreateWhiteboardUrl, payload);

        const isOk = (
            result &&
            result.status === 'success' &&
            result.data
        );

        if (!isOk) {
            console.error(
                result?.data?.message ||
                result?.message ||
                'createRoomWhiteboard Error'
            );
            return null;
        }

        return result.data;

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            alert('createRoomWhiteboard タイムアウト');
        } else {
            alert(error.message || 'createRoomWhiteboard Error');
        }

        return null;
    }
}


async function handleWhiteboardConfirmClick() {

    if (!wiseSetupWhiteboardSelect) {
        return;
    }

    const selectedValue = String(wiseSetupWhiteboardSelect.value || '');
    if (selectedValue === '') {
        return;
    }

    const room_unique_code = escapeHTML(wiseSetupRoomSelect?.value) || 'default';
    const lesson_date_id = String(wiseSetupLessonDateSelect?.value || '');

    if (lesson_date_id === '' || lesson_date_id === 'create_new') {
        return;
    }

    setWiseSetupSelectUiState(
        wiseSetupWhiteboardSelect,
        wiseSetupWhiteboardConfirmButton,
        true
    );

    setUiLock(true);

    try {

        // 1) create_new：維持（＝現ボードはそのまま）→ DBに新規レコードを作る
        if (selectedValue === 'create_new') {

            alert(MSG_WHITEBOARD_CONTINUE_CONFIRM[intSelectedLanguage]);

			const state = operationHistory[currentHistoryIndex];

            const created = await createRoomWhiteboard(
                room_unique_code,
                lesson_date_id,
                state
            );

            if (!created || !created.whiteboard_id) {
                return;
            }

            await updateRoomWhiteboardSelect(false);

            wiseSetupWhiteboardSelect.value = String(created.whiteboard_id);

            const label =
                typeof created.label === 'string' && created.label !== ''
                    ? created.label
                    : getSelectedWhiteboardLabel();

            updateCurrentWhiteboardInfo(label);
			setRoomToolsState(true);
			lockWhiteboardSetupUi();
			
            return;
        }

        // 2) 既存：破棄して復元（キャンセル可）
        const ok = window.confirm(
            MSG_WHITEBOARD_RESTORE_CONFIRM[intSelectedLanguage]
        );
        if (!ok) {
            return;
        }

        updateCurrentWhiteboardInfo(getSelectedWhiteboardLabel());

        const whiteboardId = Number(selectedValue);
        if (!Number.isFinite(whiteboardId) || whiteboardId <= 0) {
            return;
        }

        await switchToWhiteboard(whiteboardId);
		setRoomToolsState(true);
		lockWhiteboardSetupUi();

    } finally {

        setUiLock(false);
		
    }
}

async function loadWhiteboardDataById(whiteboardId) {

    const payload = {
        whiteboard_id: Number(whiteboardId),
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        wiseCoreGetWhiteboardStateUrl,
        payload,
        10000
    );

    if (!result || result.status !== 'success') {
        throw new Error(result?.data?.message || result?.message || 'whiteboard load failed');
    }

    return result.data;
}

function resetWhiteboardMemory() {

    // history（restoreStateFromHistory が参照）
    operationHistory = [];
    currentHistoryIndex = -1;

    // stroke
    strokeHistory = [];
    strokeUndone = [];

    // 画面クリア（サイズは restore 側で復元される）
    if (wiseCanvasOriginal && wiseCanvasOriginalContext) {
        wiseCanvasOriginalContext.setTransform(1, 0, 0, 1, 0, 0);
        wiseCanvasOriginalContext.clearRect(0, 0, wiseCanvasOriginal.width, wiseCanvasOriginal.height);
    }

    if (wiseCanvasHandWriting && wiseCanvasHandWritingContext) {
        wiseCanvasHandWritingContext.setTransform(1, 0, 0, 1, 0, 0);
        wiseCanvasHandWritingContext.clearRect(0, 0, wiseCanvasHandWriting.width, wiseCanvasHandWriting.height);
    }

    // movable / link canvas を全削除（restore が作り直す）
    document.querySelectorAll('.movableContainer').forEach(elm => {
        if (wisePanelWhiteboardViewMainContentArea && elm.parentNode === wisePanelWhiteboardViewMainContentArea) {
            wisePanelWhiteboardViewMainContentArea.removeChild(elm);
        }
    });

    document.querySelectorAll('.wiseCanvasLinked').forEach(elm => {
        if (canvasLinkedContainer && elm.parentNode === canvasLinkedContainer) {
            canvasLinkedContainer.removeChild(elm);
        }
    });

    // dirty フラグもリセットしたいなら（任意）
    isWhiteboardDirty = false;
}

function resetGrammarExplanationMemory() {

    grammarExplanationHistory = [];
    currentGrammarExplanationIndex = -1;

}

function resetSharedContentsSelection() {

    const ul = document.getElementById(
        'panelOverlaySharedContentsUiSelectedContentsListUl'
    );

    if (!ul) {
        return;
    }

    ul.replaceChildren();

}

function renderWhiteboard(savedData) {

    // savedData が state そのものの場合と、{ state } の場合があり得るので吸収
    const state = (savedData && savedData.state) ? savedData.state : savedData;
    if (!state) {
        return;
    }

    operationHistory = [state];
    currentHistoryIndex = 0;

	syncMaxUniqueKeyFromState(state);

    restoreStateFromHistory();
}

async function switchToWhiteboard(whiteboardId) {

    resetWhiteboardMemory();

    const savedData = await loadWhiteboardDataById(whiteboardId);

    renderWhiteboard(savedData);
}

/* ------------------------------
    関数群
------------------------------ */
async function syncMemoPadDateSelectToLessonDates(room_unique_code, lesson_date_id) {

    // MemoPad 側 UI が存在しない場合は何もしない
    if (typeof memoPadDateSelect === 'undefined' || !memoPadDateSelect) {
        return;
    }

    // MemoPad 側の options を lesson_dates で更新
    if (typeof updateRoomMemoDateSelect === 'function') {
        await updateRoomMemoDateSelect(false);
    }

    // 今確定した lesson_date を MemoPad 側にも反映
    // memoPadDateSelect.value = String(lesson_date_id || '');
}


function setRoomToolsState(inRoom) {

    const disabled = !inRoom;
    const addable = inRoom;

    setRoomToolsStateGrammarExplanation(addable);
    setRoomToolsStateGrammarInsights(disabled);
    setRoomToolsStateMemoPad(disabled);
	setRoomToolsStateLessonContents(disabled);
}


function setRoomToolsStateGrammarExplanation(addable) {

    grammarExplanationPanel.dataset.addable = addable ? '1' : '0';
}


function setRoomToolsStateGrammarInsights(disabled) {

	document.querySelectorAll('.showGrammarInsightsButton').forEach(elm => {
		setElementDisabled(elm, disabled);
		elm.classList.toggle('disabled', disabled);
		elm.setAttribute('aria-disabled', disabled.toString());
	});

	setElementDisabled(grammarInsightsOpenButton, disabled);
	grammarInsightsOpenButton.classList.toggle('disabled', disabled);

	setElementDisabled(panelOverlaySharedContentsUiChangeModalButtonBookmark, disabled);
}


async function setRoomToolsStateMemoPad(disabled) {

    // MemoPad Open Button
	setElementDisabled(memoPadOpenButton, disabled);
    memoPadOpenButton.classList.toggle('disabled', disabled);
    memoPadOpenButton.setAttribute('aria-disabled', disabled.toString());

    // MemoPad Panel
    memoPadPanel.hidden = disabled;

	if (disabled) {
        clearMemoPadTextareas();
    }
	await updateRoomMemoDateSelect(disabled);
}

function setRoomToolsStateLessonContents(disabled) {

    if (typeof lessonContentsOpenButton !== 'undefined' && lessonContentsOpenButton) {
        setElementDisabled(lessonContentsOpenButton, disabled);
        lessonContentsOpenButton.classList.toggle('disabled', disabled);
    }

    if (typeof lessonContentsPanel !== 'undefined' && lessonContentsPanel) {
        lessonContentsPanel.hidden = disabled;
    }

}

function lockWhiteboardSetupUi() {

    setElementDisabled(wiseSetupRoomSelect, true);
    setElementDisabled(wiseSetupRoomConfirmButton, true);

    setElementDisabled(wiseSetupLessonDateSelect, true);
    setElementDisabled(wiseSetupLessonDateConfirmButton, true);

    setElementDisabled(wiseSetupWhiteboardSelect, true);
    setElementDisabled(wiseSetupWhiteboardConfirmButton, true);
}

function unlockWhiteboardSetupUi() {

    setElementDisabled(wiseSetupRoomSelect, false);
    setElementDisabled(wiseSetupRoomConfirmButton, false);

    setElementDisabled(wiseSetupLessonDateSelect, false);
    setElementDisabled(wiseSetupLessonDateConfirmButton, false);

    setElementDisabled(wiseSetupWhiteboardSelect, false);
    setElementDisabled(wiseSetupWhiteboardConfirmButton, false);
}



const channel = new BroadcastChannel('jws_wise_single_tab');
const tabId = crypto.randomUUID();

let hasExistingWiseTab = false;
let isActiveWiseTab = false;

channel.onmessage = (event) => {

    const data = event.data ?? {};

    if (data.type === 'wise_presence_request' && data.tabId !== tabId && isActiveWiseTab) {
        channel.postMessage({
            type: 'wise_presence_response',
            tabId: tabId
        });
    }

    if (data.type === 'wise_presence_response' && data.tabId !== tabId) {
        hasExistingWiseTab = true;
    }
};

function checkWiseTabConflict() {

    hasExistingWiseTab = false;

    channel.postMessage({
        type: 'wise_presence_request',
        tabId: tabId
    });

    setTimeout(() => {

        if (hasExistingWiseTab) {
            document.body.innerHTML = '<p>W.I.S.E. はすでに別タブで開かれています。</p>';
            return;
        }

        isActiveWiseTab = true;

    }, 300);
}

document.addEventListener('DOMContentLoaded', () => {

    const path = window.location.pathname.replace(/\/+$/, '');

    if (path.endsWith('/wise') || path === '/wise') {
        checkWiseTabConflict();
    }

}, { passive: true });
