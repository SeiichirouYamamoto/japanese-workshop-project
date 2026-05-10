
const lessonContentsDisplayArea = document.getElementById('wisePanelLessonContentsViewMainContentArea');
const lessonContentsLessonToolbar = document.getElementById('wisePanelLessonContentsViewLessonToolbar');
const lessonContentsLoading = document.getElementById('wisePanelLessonContentsViewLoading');
const lessonContentsOpenButton = document.getElementById('wisePanelLessonContentsViewOpener');

const lessonContentsUiSearchGrammarButton = document.getElementById('wisePanelLessonContentsViewLessonToolbarSearchGrammarButton');
const lessonContentsToolbarFunctionsButton = document.getElementById('wisePanelLessonContentsViewLessonToolbarFunctionsButton');
const lessonContentsToolbarSelectorButton = document.getElementById('wisePanelLessonContentsViewLessonToolbarSelectorButton');
const lessonContentsToolbarToolsButton = document.getElementById('wisePanelLessonContentsViewLessonToolbarToolsButton');
const lessonRangeStartSelect = document.getElementById('wisePanelLessonContentsViewLessonRangeSelectorStartLesson');
const lessonRangeEndSelect = document.getElementById('wisePanelLessonContentsViewLessonRangeSelectorEndLesson');
const lessonRangeConfirmButton = document.getElementById('wisePanelLessonContentsViewSelectRoomRangeConfirmButton');

const lessonContentsUiTools = document.getElementById('wisePanelLessonContentsUiTools');
const lessonContentsUiSearchGrammarCloseButton = document.getElementById('wisePanelLessonContentsUiSearchGrammarCloseButton');
const lessonContentsUiSearchGrammarSubmitButton = document.getElementById('wisePanelLessonContentsUiSearchGrammarSearchButton');
const lessonContentsAddToListButton = document.getElementById('wisePanelLessonContentsUiAddToListButton');
const lessonContentsDeselectButton = document.getElementById('wisePanelLessonContentsUiDeselectButton');
const lessonContentsShowExplanationButton = document.getElementById('wisePanelLessonContentsUiShowExplanationButton');
const lessonContentsShowInsightsButton = document.getElementById('wisePanelLessonContentsUiShowInsightsButton');
const lessonContentsShowMapLessonButton = document.getElementById('wisePanelLessonContentsUiShowWiseMapLessonButton');
const lessonContentsShowMapLessonStepButton = document.getElementById('wisePanelLessonContentsUiShowWiseMapLessonStepButton');

const lessonContentsUiFunctions = document.getElementById('wisePanelLessonContentsUiFunctions');
const lessonContentsGrammarScopeSelect = document.getElementById('wisePanelLessonContentsUiFunctionsContainerGrammarScopeSelect');
const lessonContentsTitleExampleDisplaySelect = document.getElementById('wisePanelLessonContentsUiFunctionsContainerTitleExampleDisplaySelect');

if(lessonContentsDeselectButton !== null)
{lessonContentsDeselectButton.addEventListener('pointerup', function() {
	deselectGrammarOutlineCheckboxes(lessonContentsPanelView);
	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);
}, false);}
if(lessonContentsAddToListButton !== null)
{lessonContentsAddToListButton.addEventListener('pointerup', function() {
	
	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
	collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect);

	switchPanelOverlaySharedContentsUiView([
		SHARED_CONTENTS_UI_VIEW.ADD,
		SHARED_CONTENTS_UI_VIEW.SELECTED
	]);
	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);
}, false);}

if(lessonContentsShowInsightsButton !== null)
{lessonContentsShowInsightsButton.addEventListener('pointerup', async function() {

	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
	collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect);

	const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
	if (lis.length === LENGTH_EMPTY) {
		alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
		return;
	}

	lis.forEach((elm) => {
		const obj_grammarData = {
			japaneseId: escapeNumber(elm.dataset.japaneseId),
			uniqueCode: escapeHTML(elm.dataset.uniqueCode),
			japanese: escapeHTML(elm.dataset.japanese),
			kana: escapeHTML(elm.dataset.kana),
			categoryId: escapeNumber(elm.dataset.categoryId)
		};
		storeSharedContentsSelectionItem(obj_grammarData);
	});

	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);
	
	const panelId = grammarInsightsOpenButton.dataset.panelId;
	if (!panelId) {
		return;
	}

	switchPanelOverlaySharedContentsUiView(SHARED_CONTENTS_UI_VIEW.NONE);
	openWisePanelPositionSelectUi(panelId, true);

	await openGrammarInsightsPanelWithDiffCheck();

}, false);}

if(lessonContentsShowExplanationButton !== null)
{lessonContentsShowExplanationButton.addEventListener('pointerup', async function() {

	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
	collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect);

	const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
	if (lis.length === LENGTH_EMPTY) {
		alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
		return;
	}

	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);
	
	switchToGrammarExplanationPanel();
	
	await createMultipleGrammarExplanations(lis);
	
}, false);}
if(lessonContentsShowMapLessonStepButton !== null)
{lessonContentsShowMapLessonStepButton.addEventListener('pointerup', async function() {

	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
	collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect);

	const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
	if (lis.length === LENGTH_EMPTY) {
		alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
		return;
	}

	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);

	let selectedIds = [];

	for (let elm of lis) {
		// 未定義id null変更
		const int_japanese_id = elm.dataset.japaneseId ? escapeNumber(elm.dataset.japaneseId) : 0;
		if (int_japanese_id > 0) {
			selectedIds.push(int_japanese_id);
		}
	}

	// デバッグ panel化 マップパネル
	// switchWiseCorePanelUiMode(WISE_CORE_PANEL.NONE);
	renderWiseMapUILessonStepFromIds(selectedIds);

}, false);}
if(lessonContentsShowMapLessonButton !== null)
{lessonContentsShowMapLessonButton.addEventListener('pointerup', async function() {

	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
	collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect);

	const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
	if (lis.length === LENGTH_EMPTY) {
		alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
		return;
	}

	updateLessonContentsUiState(lessonContentsToolbarSelectorButton);

	let selectedIds = [];

	for (let elm of lis) {
		// 未定義id null変更
		const int_japanese_id = elm.dataset.japaneseId ? escapeNumber(elm.dataset.japaneseId) : 0;
		if (int_japanese_id > 0) {
			selectedIds.push(int_japanese_id);
		}
	}
	
	// デバッグ panel化 マップパネル
	// switchWiseCorePanelUiMode(WISE_CORE_PANEL.NONE);
	renderWiseMapUILessonFromIds(selectedIds);

}, false);}


if(lessonContentsResetButton !== null){
lessonContentsResetButton.addEventListener('pointerup',
	async function (e){

		const checkedCheckboxes = lessonContentsPanelView.querySelectorAll('input[type="checkbox"].grammarOutlineGrammarCheckbox:checked');
		if (checkedCheckboxes.length > LENGTH_EMPTY && !window.confirm(MSG_GRAMMAR_SELECTED_CONFIRM[intSelectedLanguage])) {
			return;
		}

		await handleRoomSelectChangeForLessonRange();

	}
	, { passive: false });
}


if (lessonContentsGoToRoomManageButton !== null) {
	lessonContentsGoToRoomManageButton.addEventListener('pointerup', function () {

		if (wiseSetupRoomSelect === null) {
			return;
		}

		const uniqueCode = String(wiseSetupRoomSelect.value || '');

		if (uniqueCode === '') {
			alert('ルームが選択されていません。');
			return;
		}

		const url = pageManageRoomLessonsUrl + '/?unique_code=' + encodeURIComponent(uniqueCode);

		window.open(url, '_blank', 'noopener');

	}, false);
}

if(lessonContentsHandle !== null){
	lessonContentsHandle.addEventListener('touchmove', (e) => {
		preventDefaultOnTouchMove(e);
	}, { passive: false });
}


async function handleRoomSelectChangeForLessonRange() {

    const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';

    // 未選択
    if (room_unique_code === 'default') {
        alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
        return false;
    }

    prevIndex = wiseSetupRoomSelect.selectedIndex;

    resetLessonContents();

    setSelectLoading(lessonRangeStartSelect);
    setSelectLoading(lessonRangeEndSelect);

    setElementDisabled(lessonRangeStartSelect, true);
    setElementDisabled(lessonRangeEndSelect, true);

    try {

        const payload = {
            room_unique_code: room_unique_code,
            int_selected_language: intSelectedLanguage
        };

        const result = await postJson(
            roomGetLessonRangeUrl,
            payload,
            10000
        );

        const data = result.data;

        const list = Array.isArray(data)
            ? data
            : Array.isArray(data?.lessons)
                ? data.lessons
                : Array.isArray(data?.list)
                    ? data.list
                    : [];

        clearSelectOptions(lessonRangeStartSelect);
        clearSelectOptions(lessonRangeEndSelect);

        // 取得できたが空（＝範囲が作れない）
        if (list.length === LENGTH_EMPTY) {
			return true;
        }

        for (let i = INDEX_FIRST; i < list.length; i++) {
            let learningStatus = escapeNumber(list[i].learning_status);
            let str_title = escapeHTML(list[i].title);

            switch (learningStatus) {
                case LEARNING_STATUS_NOT_STARTED:
                    str_title = str_title + ' ---unlearned---';
                    break;
                case LEARNING_STATUS_LEARNING:
                    str_title = str_title + ' ---learning---';
                    break;
                default:
                    break;
            }

            const option = document.createElement('option');
            option.value = escapeNumber(list[i].id);
            option.textContent = str_title;
            option.dataset.sort = escapeNumber(list[i].sort);
            lessonRangeStartSelect.appendChild(option);

            const clonedOption = option.cloneNode(true);
            lessonRangeEndSelect.appendChild(clonedOption);
        }

        lessonRangeStartSelect.selectedIndex = INDEX_FIRST;
        lessonRangeEndSelect.selectedIndex = INDEX_FIRST;

        return true;

    } catch (error) {

        if (error.message && error.message.includes('HTTPエラー: 401')) {
            lessonContentsLessonToolbar.classList.remove('visible');
            lessonContentsLoading.classList.add('loading-hidden');
            alert('ログインが必要です');
            return false;
        }

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
            return false;
        }

        console.error('Error:', error.message || error);
        alert(error.message || 'Error');
        return false;

    } finally {
        setElementDisabled(lessonRangeStartSelect, false);
        setElementDisabled(lessonRangeEndSelect, false);
    }
}


if (lessonRangeConfirmButton !== null) {
    lessonRangeConfirmButton.addEventListener('pointerup', async function () {

        if (lessonRangeConfirmButton.disabled) {
            return;
        }

        const checkedCheckboxes = lessonContentsPanelView.querySelectorAll('input[type="checkbox"].grammarOutlineGrammarCheckbox:checked');
        if (checkedCheckboxes.length > LENGTH_EMPTY && !window.confirm('選択中の文法があります。処理を進めてよろしいですか？')) {
            return;
        }

        if (
            lessonRangeStartSelect.options.length === LENGTH_EMPTY ||
            lessonRangeEndSelect.options.length === LENGTH_EMPTY
        ) {
            return;
        }

        const startSelect = lessonRangeStartSelect;
        const endSelect = lessonRangeEndSelect;

        const int_start_id = escapeNumber(startSelect[startSelect.selectedIndex].value);
        const int_end_id = escapeNumber(endSelect[endSelect.selectedIndex].value);

        const int_start_sort = escapeNumber(startSelect[startSelect.selectedIndex].dataset.sort);
        const int_end_sort = escapeNumber(endSelect[endSelect.selectedIndex].dataset.sort);

        if (int_end_sort - int_start_sort < LENGTH_EMPTY) {
            return;
        }

		closeWisePanelUi(lessonContentsPanel, 'all');

        if (lessonContentsUiFunctions) {
            const selectElements = lessonContentsUiFunctions.querySelectorAll('select');
            selectElements.forEach(select => select.selectedIndex = INDEX_FIRST);
        }

        quizHistory = [];

        const room_unique_code = escapeHTML(wiseSetupRoomSelect.value || '');
		
		setElementDisabled(lessonRangeConfirmButton, true);
        lessonContentsDisplayArea.replaceChildren();
        lessonContentsLoading.classList.remove('loading-hidden');
		
        try {
	
			const payload = {
				room_unique_code: room_unique_code,
				int_start_id: int_start_id,
				int_end_id: int_end_id,
				int_start_sort: int_start_sort,
				int_end_sort: int_end_sort,
				int_selected_language: intSelectedLanguage
			};

            await processModesSequentiallyForDisplayLessonContents(payload);

        } catch (e) {
            console.error(e);
            alert(e?.message || 'Error');
        } finally {
            lessonContentsLoading.classList.add('loading-hidden');
			setElementDisabled(lessonRangeConfirmButton, false);
        }
    }, false);
}

function updateWisePanelTitlesWithRoomName() {
    const titles = document.querySelectorAll('.wisePanelTitle');
    if (titles.length === 0) {
        return;
    }

    if (!wiseSetupRoomSelect || wiseSetupRoomSelect.selectedIndex < 0) {
        return;
    }

    // selected option の表示テキスト = roomName
    const roomName = (wiseSetupRoomSelect
        .options[wiseSetupRoomSelect.selectedIndex]
        ?.textContent || ''
    ).trim();

    if (roomName === '') {
        return;
    }

    titles.forEach((elm) => {
        // 1. 初回のみ元の text を保存
        if (!elm.dataset.originalText) {
            elm.dataset.originalText = (elm.textContent || '').trim();
        }

        // 2. roomName + 's ○○'
        const original = elm.dataset.originalText;
        elm.textContent = `${roomName}'s ${original}`;
    });
}

function resetWisePanelTitles() {

    const titles = document.querySelectorAll('.wisePanelTitle');

    titles.forEach((elm) => {

        const original = elm.dataset.originalText;

        if (!original) {
            return;
        }

        elm.textContent = original;

    });

}

if(lessonContentsToolbarSelectorButton !== null)
{lessonContentsToolbarSelectorButton.addEventListener('pointerup', function() {
	updateLessonContentsUiState(this);
}, false);}

if(lessonContentsUiSearchGrammarButton !== null)
{lessonContentsUiSearchGrammarButton.addEventListener('pointerup', function() {
	updateLessonContentsUiState(this);
	openWisePanelUi(lessonContentsPanel, lessonContentsUiSearchGrammar);
	if(lessonContentsUiSearchGrammarInput !== document.activeElement){
		lessonContentsUiSearchGrammarInput.focus();
	}
}, false);}

if (lessonContentsToolbarToolsButton !== null) {
    lessonContentsToolbarToolsButton.addEventListener('pointerup', function() {
        updateLessonContentsUiState(this);
        openWisePanelUi(lessonContentsPanel, lessonContentsUiTools);

        requestAnimationFrame(() => {
            setLessonContentsUiFloats();
        });
    }, false);
}

if (lessonContentsToolbarFunctionsButton !== null) {
    lessonContentsToolbarFunctionsButton.addEventListener('pointerup', function() {
        updateLessonContentsUiState(this);
        openWisePanelUi(lessonContentsPanel, lessonContentsUiFunctions);

        requestAnimationFrame(() => {
            setLessonContentsUiFloats();
        });
    }, false);
}

if(lessonContentsGrammarScopeSelect !== null){
	lessonContentsGrammarScopeSelect.addEventListener('change',
	function (e){
		let int_selected_grammarScopeSelect = escapeNumber(lessonContentsGrammarScopeSelect[lessonContentsGrammarScopeSelect.selectedIndex].value);
		const elms_derivedGrammarLi = lessonContentsPanelView.querySelectorAll('.derivedGrammarLi');
		const elms_derivedGrammarsButtonOpener = lessonContentsPanelView.querySelectorAll('.derivedGrammarsButtonOpener');

		if(int_selected_grammarScopeSelect === SELECT_ALL){
			elms_derivedGrammarLi.forEach(element => {
				element.classList.remove('hiddenByGrammarScope');
				element.querySelectorAll('*').forEach(child => {
					child.classList.remove('hiddenByGrammarScope');
				});
			});
			elms_derivedGrammarsButtonOpener.forEach(element => {
				element.classList.remove('hiddenByGrammarScope');
				element.querySelectorAll('*').forEach(child => {
					child.classList.remove('hiddenByGrammarScope');
				});
			});
		}
		else{
			elms_derivedGrammarLi.forEach(element => {
				element.classList.add('hiddenByGrammarScope');
				element.querySelectorAll('*').forEach(child => {
					child.classList.add('hiddenByGrammarScope');
				});
			});
			elms_derivedGrammarsButtonOpener.forEach(element => {
				element.classList.add('hiddenByGrammarScope');
				element.querySelectorAll('*').forEach(child => {
					child.classList.add('hiddenByGrammarScope');
				});
			});       
		}
	}
	, { passive: false });
}

if(lessonContentsTitleExampleDisplaySelect !== null){
	lessonContentsTitleExampleDisplaySelect.addEventListener('change',
	function (e){

	toggleGrammarOutlineTitleExampleDisplay();
	return;
	}
	, { passive: false });
}


if(lessonContentsUiSearchGrammarCloseButton !== null)
	{lessonContentsUiSearchGrammarCloseButton.addEventListener('pointerup', function() {
		closeWisePanelUi(lessonContentsPanel, 'target', lessonContentsUiSearchGrammar);
	}, false);}


if(lessonContentsUiSearchGrammarSubmitButton !== null)
{lessonContentsUiSearchGrammarSubmitButton.addEventListener('pointerup', function() {

	const str_searchWord = escapeHTML(lessonContentsUiSearchGrammarInput.value);
	const elm_targetUl = document.getElementById('wisePanelLessonContentsUiSearchGrammarList');
	const elm_targetLoading = lessonContentsUiSearchGrammarLoading;
	const arr_classNaming_li = ['wisePanelLessonContentsUiSearchGrammarListLi'];
    const int_matching_type = MATCHING_TYPE_PARTIAL;
    const int_category_id = JP_CATEGORY_GRAMMAR;
	const int_sub_category_id = SELECT_ALL;
	const int_learningScope = LEARNING_SCOPE_ALREADY_LEARNED;

	searchContentList(
		SEARCH_SCOPE_LESSON_CONTENTS,
		str_searchWord,
		elm_targetUl,
		elm_targetLoading,
		arr_classNaming_li,
		int_matching_type,
		int_category_id,
		int_sub_category_id,
		int_learningScope
	);

}, false);}


/******************************************************
 *  
 *  
 ******************************************************/
function handleLessonContentsSearchGrammarListClick(e) {

    const li = getClosestLessonContentsSearchGrammarLi(e);
    if (!li) {
        return;
    }

    clearSearchTargets();

    const uniqueCode = getUniqueCodeFromLi(li);

    resetSearchGrammarClickStateIfNeeded(uniqueCode);

    const targetElements = getVisibleGrammarTargetElementsByUniqueCode(uniqueCode);

    const targetElement = resolveTargetElementByClickCount(targetElements);
    if (!targetElement) {
        return;
    }

    cleanupPreviousSearchGrammarTarget();

    applySearchGrammarTargetUI(targetElement);

    finalizeSearchGrammarTarget(targetElement);
}

document.addEventListener('pointerup', handleLessonContentsSearchGrammarListClick);


function getClosestLessonContentsSearchGrammarLi(e) {
    return e.target.closest('.wisePanelLessonContentsUiSearchGrammarListLi');
}

function clearSearchTargets() {
    const searchTargetElms = document.querySelectorAll('.search-target');
    searchTargetElms.forEach(elm => {
        elm.classList.remove('search-target');
    });
}

function getUniqueCodeFromLi(li) {
    return escapeHTML(li.dataset.uniqueCode);
}

function resetSearchGrammarClickStateIfNeeded(uniqueCode) {

    if (uniqueCode === wisePanelLessonContentsUiSearchGrammarListLiLastUniqueCode) {
        return;
    }

    wisePanelLessonContentsUiSearchGrammarListLiClickCount = COUNT_FIRST;
    wisePanelLessonContentsUiSearchGrammarListLiLastUniqueCode = uniqueCode;
}

function getVisibleGrammarTargetElementsByUniqueCode(uniqueCode) {

	const selector =
		'[data-lesson-contents-search-grammar-target="true"] ' +
		'button[data-unique-code="' + uniqueCode + '"]';

    const elements = Array.from(lessonContentsPanelView.querySelectorAll(selector));

    return elements.filter(elm => window.getComputedStyle(elm).display !== 'none');
}

function resolveTargetElementByClickCount(targetElements) {

    if (targetElements.length === 0) {
        wisePanelLessonContentsUiSearchGrammarListLiClickCount = COUNT_FIRST;
        return null;
    }

    if (wisePanelLessonContentsUiSearchGrammarListLiClickCount >= targetElements.length) {
        wisePanelLessonContentsUiSearchGrammarListLiClickCount = COUNT_FIRST;
    }

    return targetElements[wisePanelLessonContentsUiSearchGrammarListLiClickCount] || null;
}

function cleanupPreviousSearchGrammarTarget() {

    if (!wisePanelLessonContentsUiSearchGrammarLastTargetElement) {
        return;
    }

    let prevElement = wisePanelLessonContentsUiSearchGrammarLastTargetElement;

    while (prevElement) {

        prevElement.classList.remove('completed');

        cleanupGrammarOutlineOpenersIfNeeded(prevElement);

        if (prevElement.tagName.toLowerCase() === 'details') {
            prevElement.removeAttribute('open');
        }

        prevElement = prevElement.parentElement;
    }
}

function cleanupGrammarOutlineOpenersIfNeeded(elm) {

    if (
        !elm.classList.contains('divGrammarOutlineGrammarsUl') &&
        !elm.classList.contains('grammarOutlineUl')
    ) {
        return;
    }

    const siblings = Array.from(elm.parentElement.children);

    const grammarOutlineContainer =
        siblings.find(sibling => sibling.classList.contains('divGrammarOutlineContainer'));

    if (grammarOutlineContainer) {
        const opener =
            grammarOutlineContainer.querySelector('.grammarOutlineLabelButtonGrammarOutlineUlOpener');

        if (opener) {
            opener.classList.remove('completed');
            opener.textContent = '▶';
        }
    }

    if (elm.classList.contains('divGrammarOutlineGrammarsUl')) {

        const grammarOutlineLabelContainer =
            siblings.find(sibling => sibling.classList.contains('divGrammarOutlineLabelContainer'));

        if (grammarOutlineLabelContainer) {
            const opener =
                grammarOutlineLabelContainer.querySelector('.grammarOutlineLabelButtonGrammarsUlOpener');

            if (opener) {
                opener.classList.remove('completed');
                opener.textContent = '▶';
            }
        }
    }
}

function applySearchGrammarTargetUI(targetElement) {

    expandParentsAndMarkCompleted(targetElement);

    const labelElm = findSiblingLabelElement(targetElement);
    if (!labelElm) {
        return;
    }

    markAndScrollToLabel(labelElm);
}

function expandParentsAndMarkCompleted(targetElement) {

    let currentElement = targetElement;

    while (currentElement) {

        markGrammarOutlineParentsIfNeeded(currentElement);

        if (currentElement.tagName.toLowerCase() === 'details') {
            currentElement.setAttribute('open', '');
        }

        currentElement = currentElement.parentElement;
    }
}

function markGrammarOutlineParentsIfNeeded(elm) {

    if (
        !elm.classList.contains('divGrammarOutlineGrammarsUl') &&
        !elm.classList.contains('grammarOutlineUl')
    ) {
        return;
    }

    elm.classList.add('completed');

    const siblings = Array.from(elm.parentElement.children);

    const grammarOutlineContainer =
        siblings.find(sibling => sibling.classList.contains('divGrammarOutlineContainer'));

    if (grammarOutlineContainer) {
        const opener =
            grammarOutlineContainer.querySelector('.grammarOutlineLabelButtonGrammarOutlineUlOpener');

        if (opener) {
            opener.classList.add('completed');
            opener.textContent = '▼';
        }
    }

    if (elm.classList.contains('divGrammarOutlineGrammarsUl')) {

        const grammarOutlineLabelContainer =
            siblings.find(sibling => sibling.classList.contains('divGrammarOutlineLabelContainer'));

        if (grammarOutlineLabelContainer) {
            const opener =
                grammarOutlineLabelContainer.querySelector('.grammarOutlineLabelButtonGrammarsUlOpener');

            if (opener) {
                opener.classList.add('completed');
                opener.textContent = '▼';
            }
        }
    }
}

function findSiblingLabelElement(targetElement) {

    const parent = targetElement.parentElement;
    if (!parent) {
        return null;
    }

    return Array.from(parent.children).find(child =>
        child !== targetElement && (
            (child.classList.contains('divGrammarOutlineInputLabel') && !child.classList.contains('hidden')) ||
            child.classList.contains('divGrammarOutlineText')
        )
    ) || null;
}

function markAndScrollToLabel(labelElm) {

    labelElm.classList.add('search-target');
    labelElm.scrollIntoView({ behavior: 'smooth', block: 'center' });

    labelElm.classList.add('highlight');

    setTimeout(() => {
        labelElm.classList.remove('highlight');
    }, 1500);
}

function finalizeSearchGrammarTarget(targetElement) {
    wisePanelLessonContentsUiSearchGrammarLastTargetElement = targetElement;
    wisePanelLessonContentsUiSearchGrammarListLiClickCount += 1;
}


/******************************************************
 *  
 *  
 ******************************************************/
function handleGrammarOutlineCheckboxChange(e) {

    const checkbox = e.target.closest('.grammarOutlineCheckbox');
    if (!checkbox) {
        return;
    }

    const result = checkbox.checked === true;

    if (!result) {
        uncheckAncestorsUntilTopElement(checkbox, result);
    }

    if (checkbox.classList.contains('grammarOutlineTextCheckbox')) {
        applyResultToAllCheckboxesInCurrentLi(checkbox, result);
        return;
    }
}

document.addEventListener('change', handleGrammarOutlineCheckboxChange);


function uncheckAncestorsUntilTopElement(checkbox, result) {

    let currentNode = checkbox.parentNode;

    while (currentNode) {

        if (currentNode.classList && currentNode.classList.contains('grammarOutlineTopElement')) {
            break;
        }

        if (currentNode.classList && currentNode.classList.contains('grammarOutlineLi')) {
            const elm_div = currentNode.querySelector('.divGrammarOutlineContainer');
            if (elm_div) {
                const elm_checkBox = elm_div.querySelector('.grammarOutlineCheckbox');
                if (elm_checkBox) {
                    elm_checkBox.checked = result;
                }
            }
        }

        currentNode = currentNode.parentNode;
    }
}

function applyResultToAllCheckboxesInCurrentLi(checkbox, result) {

    const li = findClosestGrammarOutlineLi(checkbox);
    if (!li) {
        return;
    }

    const elms_checkBox = li.querySelectorAll('.grammarOutlineCheckbox');
    for (let i = INDEX_FIRST; i < elms_checkBox.length; i++) {
        elms_checkBox[i].checked = result;
    }
}

function findClosestGrammarOutlineLi(elm) {

    let currentNode = elm.parentNode;

    while (currentNode) {

        if (currentNode.classList && currentNode.classList.contains('grammarOutlineLi')) {
            return currentNode;
        }

        currentNode = currentNode.parentNode;
    }

    return null;
}


/******************************************************
 *  
 *  
 ******************************************************/
function handleGrammarOutlineLabelButtonOpenerClick(e) {

    const button = e.target.closest('.grammarOutlineLabelButtonOpener');
    if (!button) {
        return;
    }

    const targetSelector = getGrammarOutlineTargetSelector(button);

    const labelContainer = findClosestLabelContainer(button);
    if (!labelContainer) {
        return;
    }

    const li = findLi(button);
    if (!li) {
        return;
    }

    const targetElm = li.querySelector(targetSelector);
    if (!targetElm) {
        return;
    }

    toggleGrammarOutlineOpenState(button, targetElm);
}

document.addEventListener('pointerup', handleGrammarOutlineLabelButtonOpenerClick);


function getGrammarOutlineTargetSelector(button) {

    if (button.classList.contains('grammarOutlineLabelButtonGrammarsUlOpener')) {
        return '.divGrammarOutlineGrammarsUl';
    }

    return '.grammarOutlineUl';
}

function findClosestLabelContainer(elm) {

    let currentNode = elm;

    while (currentNode) {

        if (currentNode.classList && currentNode.classList.contains('divGrammarOutlineLabelContainer')) {
            return currentNode;
        }

        currentNode = currentNode.parentNode;
    }

    return null;
}

function toggleGrammarOutlineOpenState(button, targetElm) {

    if (button.classList.contains('completed')) {
        targetElm.classList.remove('completed');
        button.classList.remove('completed');
        button.textContent = '▶';
        return;
    }

    targetElm.classList.add('completed');
    button.classList.add('completed');
    button.textContent = '▼';
}


/******************************************************
 *  
 *  
 ******************************************************/
async function handleGrammarOutlineLabelButtonExplanationClick(e) {

    const button = e.target.closest('.grammarOutlineLabelButtonExplanation');
    if (!button) {
        return;
    }
	
    const grammarData = getGrammarDataFromExplanationButton(button);

    const panelType = detectGrammarExplanationPanelType(button);

    if (panelType === 'outside') {
        openGrammarViewInNewTab(grammarData.uniqueCode);
        return;
    }

    if (panelType === 'needsPanelSwitch') {
        switchToGrammarExplanationPanel();
    }

    await createGrammarExplanation(grammarData.uniqueCode);

    handleGrammarExplanationHistoryAndDisplay(grammarData);
}

document.addEventListener('pointerup', (e) => {
    void handleGrammarOutlineLabelButtonExplanationClick(e);
});


function getGrammarDataFromExplanationButton(button) {

    return {
        japaneseId: button.dataset.japaneseId ? escapeNumber(button.dataset.japaneseId) : 0,
        uniqueCode: button.dataset.uniqueCode ? escapeHTML(button.dataset.uniqueCode) : '',
        japanese: button.dataset.japanese ? escapeHTML(button.dataset.japanese) : '',
        kana: button.dataset.kana ? escapeHTML(button.dataset.kana) : '',
        categoryId: button.dataset.categoryId ? escapeNumber(button.dataset.categoryId) : 0
    };
}

function detectGrammarExplanationPanelType(button) {

    let currentNode = button;

    while (currentNode) {

        if (
            currentNode === grammarExplanationPanel ||
            currentNode === lessonContentsPanel ||
            currentNode === quizFeedbackScreen ||
            currentNode === wisePanelGrammarInsightsView
        ) {

            if (
                currentNode === lessonContentsPanel ||
                currentNode === quizFeedbackScreen ||
                currentNode === wisePanelGrammarInsightsView
            ) {
                return 'needsPanelSwitch';
            }

            return 'grammarExplanationPanel';
        }

        currentNode = currentNode.parentNode;
    }

    return 'outside';
}

function switchToGrammarExplanationPanel() {
	const panelId = grammarExplanationOpenButton.dataset.panelId;
	if (!panelId) {
		return;
	}

	switchPanelOverlaySharedContentsUiView(SHARED_CONTENTS_UI_VIEW.NONE);
	openWisePanelPositionSelectUi(panelId, true);
}

function handleGrammarExplanationHistoryAndDisplay(grammarData) {

    storeSharedContentsSelectionItem(grammarData);

    const idx = saveStateGrammarExplanation(grammarData, { behavior: 'append' });

    if (idx !== null) {
        displayCurrentStateGrammarExplanation(idx, true);
    }
}


/******************************************************
 *  
 *  
 ******************************************************/
function handleGrammarRelationHideButtonClick(e) {

    const button = e.target.closest('.sectionGrammarRelationHideButton');
    if (!button) {
        return;
    }

    const section = button.closest('.sectionGrammarRelation');
    if (!section) {
        return;
    }

    section.style.display = 'none';
}

document.addEventListener('pointerup', handleGrammarRelationHideButtonClick);


if (lessonContentsUiSearchGrammarInput !== null) {
    lessonContentsUiSearchGrammarInput.addEventListener('keydown', function (e) {

        if (e.key === 'Enter' || e.keyCode === 13) {
	
			const str_searchWord = escapeHTML(lessonContentsUiSearchGrammarInput.value);
			const elm_targetUl = document.getElementById('wisePanelLessonContentsUiSearchGrammarList');
			const elm_targetLoading = lessonContentsUiSearchGrammarLoading;
			const arr_classNaming_li = ['wisePanelLessonContentsUiSearchGrammarListLi'];
			const int_matching_type = MATCHING_TYPE_PARTIAL;
			const int_category_id = JP_CATEGORY_GRAMMAR;
			const int_sub_category_id = SELECT_ALL;
			const int_learningScope = LEARNING_SCOPE_ALREADY_LEARNED;

			searchContentList(
				SEARCH_SCOPE_LESSON_CONTENTS,
				str_searchWord,
				elm_targetUl,
				elm_targetLoading,
				arr_classNaming_li,
				int_matching_type,
				int_category_id,
				int_sub_category_id,
				int_learningScope,
			);

            e.preventDefault();
        }
    });
}


function updateLessonContentsUiState(elm){

	let elms_toggle = document.querySelectorAll('.wisePanelLessonContentsViewLessonToolbarButtonToggle'),
		elm_toggle;

	for(let i = INDEX_FIRST; i < elms_toggle.length; i++){
		elm_toggle = elms_toggle[i];
		if(elm_toggle === elm){
			elm_toggle.classList.add('wisePanelLessonContentsViewLessonToolbarButton-selected');
		}
		else{
			elm_toggle.classList.remove('wisePanelLessonContentsViewLessonToolbarButton-selected');
		}
	}

	let elms_searchTarget = lessonContentsPanelView.querySelectorAll('.search-target'),
	elm_searchTarget;
	
	for(let i = INDEX_FIRST; i < elms_searchTarget.length; i++){
		elm_searchTarget = elms_searchTarget[i];
		elm_searchTarget.classList.remove('search-target');
	}
	closeWisePanelUi(lessonContentsPanel, 'float');
}


function setFloatPosition(button, panel) {

    const rect = button.getBoundingClientRect();
    const panelWidth = panel.offsetWidth;
    const viewportWidth = window.innerWidth;

    let left = rect.left + (rect.width / 2) - (panelWidth / 2);

    if (left + panelWidth > viewportWidth) {
        left = rect.right - panelWidth;
    }

    if (left < 0) {
        left = 0;
    }

    panel.style.left = left + 'px';
    panel.style.top = rect.bottom + 'px';
}

function setLessonContentsUiFloats() {

    if (lessonContentsToolbarToolsButton !== null && lessonContentsUiTools !== null) {
        setFloatPosition(lessonContentsToolbarToolsButton, lessonContentsUiTools);
    }

    if (lessonContentsToolbarFunctionsButton !== null && lessonContentsUiFunctions !== null) {
        setFloatPosition(lessonContentsToolbarFunctionsButton, lessonContentsUiFunctions);
    }
}



function toggleGrammarOutlineTitleExampleDisplay(){

	let int_selected_titleExampleDisplaySelect = escapeNumber(lessonContentsTitleExampleDisplaySelect[lessonContentsTitleExampleDisplaySelect.selectedIndex].value);

	const divGrammarOutlineInputLabelTitles = document.querySelectorAll('.divGrammarOutlineInputLabelTitle');
	const divGrammarOutlineInputLabelExamples = document.querySelectorAll('.divGrammarOutlineInputLabelExample');
	
	if (int_selected_titleExampleDisplaySelect == STATUS_FIRST) {
		divGrammarOutlineInputLabelTitles.forEach(function(elm) {
			elm.classList.remove('hidden');
		});
		divGrammarOutlineInputLabelExamples.forEach(function(elm) {
			elm.classList.add('hidden');
		});
	} else {
		divGrammarOutlineInputLabelTitles.forEach(function(elm) {
			elm.classList.add('hidden');
		});
		divGrammarOutlineInputLabelExamples.forEach(function(elm) {
			elm.classList.remove('hidden');
		});
	}
}

function resetLessonContents(){

	clearSelectOptions(lessonRangeStartSelect);
	clearSelectOptions(lessonRangeEndSelect);

	lessonContentsDisplayArea.replaceChildren();
	lessonContentsLessonToolbar.classList.remove('visible');

	closeWisePanelUi(lessonContentsPanel, 'all');

}

async function displayLessonContentsInPanel(mode, send_data) {
	
	try {

		const payload = { ...send_data, str_mode: mode };

        const result = await postJson(
            roomDisplayLessonContentsUrl,
            payload,
            120000
        );

        const data = result.data;

        if (!data || !data.html) {
            return false;
        }

        lessonContentsLessonToolbar.classList.add('visible');
        lessonContentsDisplayArea.insertAdjacentHTML('beforeend', data.html);

        applyFontSizeVariation(
            GRAMMAR_FONT_SIZE_TARGETS,
            'sectionGrammarViewFontSizeVariationDifference'
        );
        toggleGrammarOutlineTitleExampleDisplay();

        return true;

    } catch (error) {
        if (error && typeof error.message === 'string' && error.message.includes('HTTPエラー: 401')) {
            // デバッグ loginUrl は、JWS_URLS などから取得して渡すのが理想
            // 例: createLoginPageLinkButton(lessonContentsDisplayArea, JWS_URLS.loginUrl);
            alert('ログインが必要です');
            return false;
        }

        if (error && typeof error.message === 'string' && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
            return false;
        }

        console.error('Error:', error.message || error);
        alert(error.message || 'Error');
        return false;
    }
}


function createLoginPageLinkButton(elm, str_link){
	let elm_linkButton = document.createElement('button');
	elm_linkButton.textContent = 'To Login Page';
	elm_linkButton.addEventListener('pointerup', function() {
	let url = escapeHTML(str_link);
	window.open(url, '_blank', 'noopener');
	}, false);
	elm.appendChild(elm_linkButton);
}



async function processModesSequentiallyForDisplayLessonContents(send_data) {
    try {
        for (const mode of MODE_LIST) {
            await displayLessonContentsInPanel(mode, send_data);
        }
    } finally {
        lessonContentsLoading.classList.add('loading-hidden');
		setElementDisabled(lessonRangeConfirmButton, false);
    }
}


function collectCheckedGrammarInsights(elm_targetUl, int_selected_grammarScopeSelect){

	elm_targetUl.replaceChildren();
	let checkedCheckboxes;
	
	checkedCheckboxes = Array.from(
		lessonContentsPanelView.querySelectorAll('input[type="checkbox"].grammarOutlineGrammarCheckbox:checked')
	).filter(elm => {
		return !elm.classList.contains('hiddenByGrammarScope');
	});

	checkedCheckboxes.forEach((elm) => {
		addGrammarInsightsSelectedItem(elm);
	});
}

function deselectGrammarOutlineCheckboxes(currentNode){
	const elms_checkBox = currentNode.querySelectorAll('.grammarOutlineCheckbox');
	if(elms_checkBox.length > LENGTH_EMPTY){
	for(let i = INDEX_FIRST; i < elms_checkBox.length; i++) {
		elms_checkBox[i].checked = false;
	}
	};
	return;
}
