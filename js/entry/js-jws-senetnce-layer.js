/******************************************************
 *  COMMON
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]



/******************************************************
 *  CREATE
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]



/******************************************************
 *  UPDATE
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]



/******************************************************
 *  DELETE
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]



/******************************************************
 *  OVERRIDE
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]

const createLayersChangeLayoutButton = document.getElementById('sentenceLayerMenuChangeLayoutButtonCreateLayers');
const createNewLayerButton = document.getElementById('sentenceLayerMenuCreateNewLayerButton');
const createNewLayerInput = document.getElementById('sentenceLayerMenuCreateNewLayerInput');
const grammarIdApplyButton = document.getElementById('sentenceLayerUpdateScreenGrammarIdButton');
const grammarIdInput = document.getElementById('sentenceLayerUpdateScreenGrammarIdInput');
const grammarJapaneseContent = document.getElementById('sentenceLayerUpdateScreenGrammarJapaneseContent');
const sentenceLayerElementsDisplayList = document.getElementById('sentenceLayerUpdateScreenLayerElementsDisplayAreaUl');
const sentenceLayerNameInput = document.getElementById('sentenceLayerUpdateScreenLayerNameInput');
const sentenceLayerNameUpdateButton = document.getElementById('sentenceLayerUpdateScreenLayerNameUpdateButton');
const sentenceLayersMenuList = document.getElementById('sentenceLayerMenuUl');
const sentenceLayerUpdateCloseButton = document.getElementById('sentenceLayerUpdateScreenButtonClose');
const sentenceLayerUpdateLayerElements = document.getElementById('sentenceLayerUpdateScreenLayerElements');
const sentenceLayerUpdateOverlay = document.getElementById('sentenceLayerUpdateScreenOverlay');
const sentenceLayerUpdateSideMenu = document.getElementById('sentenceLayerUpdateScreenSideMenu');
const sentenceLayerUpdateSubmitButton = document.getElementById('sentenceLayerUpdateScreenButtonSubmit');
const overrideCreateButton = document.getElementById('sentenceLayerUpdateOverrideCreateButton');
const overrideDisplayTextInput = document.getElementById('sentenceLayerUpdateOverrideDisplayTextInput');
const overrideHighlightToggleButton = document.getElementById('sentenceLayerUpdateOverrideHighlightToggleButton');
const overrideIdSelect = document.getElementById('sentenceLayerUpdateOverrideOverrideIdSelect');
const overrideList = document.getElementById('sentenceLayerUpdateOverrideScreenlayerUpdateOverridesUl');
const overrideLoading = document.getElementById('sentenceLayerUpdateOverrideScreenDisplayAreaLoading');
const overrideOverlay = document.getElementById('sentenceLayerUpdateOverrideOverlay');
const overrideSortInput = document.getElementById('sentenceLayerUpdateOverrideSortInput');
const usedGrammarInflectionList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdInflectionUl');
const usedGrammarParticlesList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdParticlesUl');
const usedGrammarPredicateList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdPredicateUl');
const usedGrammarSelectedGrammarList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedGrammartUl');
const usedGrammarSelectedParticleList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedJapaneseParticleUl');
const usedGrammarSpecialTermsList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdSpecialTermsUl');
const usedGrammarUnselectedGrammarList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedGrammarUl');
const usedGrammarUnselectedParticleList = document.getElementById('sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedJapaneseParticleUl');

/******************************************************
 *  EVENT
 *
 ******************************************************/
if(createNewLayerButton !== null)
{createNewLayerButton.addEventListener('pointerup', function() {
	createNewLayer();
}, false);}


if(grammarIdInput !== null)
{grammarIdInput.addEventListener('input', function() {
	displayLayerUpdateScreenGrammarJapaneseContent();
}, false);}


if(grammarIdApplyButton !== null)
{grammarIdApplyButton.addEventListener('pointerup', function() {
	displayLayerUpdateScreenGrammarJapaneseContent();
}, false);}


if(overrideCreateButton !== null)
{overrideCreateButton.addEventListener('pointerup', async function() {
	await createNewOverride();
}, false);}


if (sentenceLayerNameUpdateButton !== null)
{sentenceLayerNameUpdateButton.addEventListener('pointerup', async function () {

	let int_layer_id = escapeNumber(sentenceLayerNameUpdateButton.dataset.layerId);
	
	try {
	
		const payload = {
			int_layer_id: int_layer_id,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			sentenceLayerUpdateLayerNameUrl,
			payload,
			60000
		);

		const data = result.data;

		sentenceLayerNameInput.value =
			escapeHTML(data.item_title);

	} catch (error) {
		console.error('Error:', error.message || error);
		alert(error.message || '通信エラーが発生しました');
	}

}, false);}


if(sentenceLayerUpdateSubmitButton !== null)
{sentenceLayerUpdateSubmitButton.addEventListener('pointerup', async function() {
	let int_layer_id = escapeNumber(sentenceLayerUpdateSubmitButton.dataset.layerId);
	let int_layerUpdateScreenGrammarId = toNullableInteger(grammarIdInput.value);
	let str_layerUpdateScreenLayerName = ((sentenceLayerNameInput.value !== null) && (sentenceLayerNameInput.value.length > LENGTH_EMPTY)) ? escapeHTML(sentenceLayerNameInput.value) : AVOID_NULL_PROXY_STRING;
	
	try {

		const payload = {
			int_layer_id: int_layer_id,
			int_layerUpdateScreenGrammarId: int_layerUpdateScreenGrammarId,
			str_layerUpdateScreenLayerName: str_layerUpdateScreenLayerName,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerUpdateLayerPropertyUrl,
			payload,
			10000
		);

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}

	sentenceLayerUpdateOverlay.classList.remove('overlay-on');
	reviewLayers(createLayersBody);

}, false);}


if (sentenceLayerUpdateCloseButton !== null)
{sentenceLayerUpdateCloseButton.addEventListener('pointerup', function () {

	let hasError = false;

	for (const container of document.querySelectorAll('.sentenceLayerElementInputContainer')) {
		const formSelect  = container.querySelector('.sentenceLayerElementFormIdSelect');
		const voiceSelect = container.querySelector('.sentenceLayerElementVoiceIdSelect');
		if (!formSelect || !voiceSelect) continue;

		const formVal  = Number(formSelect.value);
		const voiceVal = Number(voiceSelect.value);

		// デバッグ未定義id
		if ((formVal === 0) !== (voiceVal === 0)) {
			hasError = true;
			break;
		}
	}

	if (hasError) {
		alert('入力内容に誤りがあります。');
		return;
	}

	sentenceLayerUpdateOverlay.classList.remove('overlay-on');

}, false);}


if (createNewLayerInput !== null) {
    createNewLayerInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            createNewLayer();
            e.preventDefault();
        }
    });
}


if(createLayersChangeLayoutButton !== null)
{createLayersChangeLayoutButton.addEventListener('pointerup', function() {

	if(isEditingLayer){return;}

	if(isHorizontalLayout){
		isHorizontalLayout = false;
	}
	else{
		isHorizontalLayout = true;
	}

	createLayersDisplayArea.innerHTML = '';
	canvasLinkedContainer.innerHTML = '';

	targetBody = createLayersBody;
	targetContent = createLayersContent;
	targetContainersDisplayArea = createLayersDisplayArea;
	createWiseBodyClickableContainer(targetBody, targetContent, targetContainersDisplayArea);
}, false);}



/******************************************************
 *  FUNCTIONS
 *
 ******************************************************/


async function createNewLayer(){

	if (isEditingLayer || isCreatingLayer) return;

	let str_newLayersName = escapeHTML(createNewLayerInput.value);
	if(str_newLayersName.length === LENGTH_EMPTY){
		return;
	}
	// マジックナンバー
	let isConfirmed = window.confirm('新しいレイヤーを作成しますか？');
	if(isConfirmed) {

		isCreatingLayer = true;

		let url = new URL(window.location.href),
			params = url.searchParams,
			send_sentence_unique_code = escapeHTML(params.get(KEY_SENTENCE_UNIQUE_CODE));

		let searchCriteria = send_sentence_unique_code;
		let searchById = false;
		let searchByIdAsNumber = searchById ? FLAG_TRUE : FLAG_FALSE;
		
		try {
			
			const payload = {
				str_newLayersName: str_newLayersName,
				searchCriteria: searchCriteria,
				searchById: searchByIdAsNumber,
				int_selected_language: intSelectedLanguage
			};

			const result = await postJson(sentenceLayerCreateNewLayerUrl, payload, 10000);
			const data = result.data;

			if (!data || data.length === LENGTH_EMPTY) return;

			createNewLayerInput.value = '';
			reviewLayers(createLayersBody);

		} catch (error) {
			if (error.message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
			} else {
				console.error('Error:', error.message || error);
				alert(error.message || 'Error');
			}
			
		} finally {
			isCreatingLayer = false;
		}
	}
}


function parseHasOverrideResult(data) {
    if (data == null) return false;

    if (typeof data === 'object') {
        if ('hasOverride' in data) {
            return data.hasOverride === FLAG_TRUE || data.hasOverride === true || data.hasOverride === '1';
        }
        if ('exists' in data) {
            return data.exists === FLAG_TRUE || data.exists === true || data.exists === '1';
        }
        if ('override' in data) {
            return data.override === FLAG_TRUE || data.override === true || data.override === '1';
        }
        if ('count' in data) {
            return Number(data.count) > 0;
        }
    }

    if (typeof data === 'number') {
        return data > 0;
    }

    return false;
}


async function updateLayerElementProperty(payload, timeoutMs = 60000) {
    return await postJson(
        sentenceLayerUpdateLayerElementPropertyUrl,
        payload,
        timeoutMs
    );
}


async function showOverrideElements(int_layer_element_id) {

	if (overrideCreateButton) overrideCreateButton.dataset.layerElementId = int_layer_element_id;
	if (overrideList) overrideList.innerHTML = '';
	
	try {
	
		const payload = {
			id: parseInt(int_layer_element_id, 10),
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			sentenceLayerDisplayOverridesInformationUrl,
			payload,
			60000
		);

		const data = result.data;

		if (!data || !data.html) return;

		if (overrideList) {
			overrideList.innerHTML = data.html || '';
		}

	} catch (error) {
		console.error('Error:', error.message || error);
		alert('オーバーライド取得に失敗しました');
	}
}


async function createNewOverride() {
    
	// 未定義id
    const currentId = overrideCreateButton?.dataset.layerElementId || '';
	if (!currentId || currentId === '0') {
        console.warn('No element selected.');
        return;
    }


	const displayText = (overrideDisplayTextInput?.value || '').trim();

	const is_highlighted = Number(overrideHighlightToggleButton.checked);

	if (!overrideSortInput || overrideSortInput.value.trim() === '') {
		alert('Sort is required.');
		return;
	}
	const sortVal = parseInt(overrideSortInput.value, 10);
	if (Number.isNaN(sortVal) || sortVal < SORT_FIRST) {
		alert('Sort must be an integer (>= '+SORT_FIRST+').');
		return;
	}
	const overrideId = parseInt(overrideIdSelect?.value, 10);
	if (!overrideId || Number.isNaN(overrideId)) {
		alert('Please select an operation (override).');
		return;
	}

	overrideLoading.classList.remove('loading-hidden');
	setElementDisabled(overrideCreateButton, true);
	
	try {
	
		const payload = {
			int_layer_element_id: parseInt(currentId, 10),
			override_id: overrideId,
			display_text: displayText,
			is_highlighted: is_highlighted,
			sort: sortVal,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerCreateNewOverrideUrl,
			payload,
			60000
		);

		if (currentId) {
			await showOverrideElements(currentId);
		}
		if (overrideDisplayTextInput) {
			overrideDisplayTextInput.value = '';
		}
		if (overrideHighlightToggleButton) {
			overrideHighlightToggleButton.checked = false;
		}
		if (overrideSortInput) {
			overrideSortInput.value = SORT_FIRST;
		}
		if (overrideIdSelect) {
			overrideIdSelect.selectedIndex = INDEX_FIRST;
		}

	} catch (error) {
		console.error('Error:', error.message || error);
		alert('新しいオーバーライド作成に失敗しました');
	} finally {
		overrideLoading.classList.add('loading-hidden');
		setElementDisabled(overrideCreateButton, false);
	}
}









async function getLayerElements(int_layer_id) {

    const payload = {
        int_layer_id: int_layer_id,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        sentenceLayerGetElementsUrl,
        payload,
        10000
    );

    const data = result.data;
    if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
        return [];
    }

    return data;
}












function handleUsedGrammarListItemClick(e) {

    const li = e.target.closest('.sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedGrammartLi');
    if (!li) {
        return;
    }

    grammarIdInput.value = escapeNumber(li.dataset.grammarId);
    displayLayerUpdateScreenGrammarJapaneseContent();
}

document.addEventListener('pointerup', handleUsedGrammarListItemClick);


function initCreateLayersLayout(isOnload) {

    if (sectionCreateLayers === null) return;

    disableMobileGestures();
    hideMobileNavButtonOnLoad();

    if (isOnload) reviewLayers(createLayersBody);

    if (wiseToolbarContainer !== null) {
        resetToSelectMode();
        resizeWiseVerticalToolbars(createLayersBody);
        positionWiseMenuBars();
    }

    setWiseLayoutPosition(sectionCreateLayers);
	
    if (isOnload) {
		applyWiseCanvasLayout();
        createWiseBodyClickableContainer(
            createLayersBody,
            createLayersContent,
            createLayersDisplayArea
        );
    }

    finalizeLayout();
}

function bindCreateLayersLayoutEvents() {

    if (sectionCreateLayers === null) return;

    const onResize = throttle(() => {
        prepareLayoutOnResize();
        initCreateLayersLayout(false);
    }, 150);

    window.addEventListener('resize', onResize, { passive: true });
    // window.addEventListener('orientationchange', onResize, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {

    if (sectionCreateLayers === null) return;

    prepareLayoutOnLoad();
    initCreateLayersLayout(true);
    bindCreateLayersLayoutEvents();

}, { passive: true });



function createWiseBodyClickableContainer(target_elm_wiseBody, target_elm_wiseContent, target_elm_wiseContainersMainContentArea){

	let url = new URL(window.location.href),
		params = url.searchParams,
		send_sentence_unique_code = escapeHTML(params.get(KEY_SENTENCE_UNIQUE_CODE));

	linkContainerWidth = 0;

	createClickableContainers(send_sentence_unique_code, target_elm_wiseBody, target_elm_wiseContent, target_elm_wiseContainersMainContentArea);

}



/******************************************************
 * 
 ******************************************************/
async function createClickableContainers(
    send_sentence_unique_code,
    target_elm_wiseBody,
    target_elm_wiseContent,
    target_elm_wiseContainersMainContentArea
) {

    const arr_elements = await getRegisteredSentenceElements(send_sentence_unique_code, false);

    if (arr_elements.length === LENGTH_EMPTY) {
        return;
    }

	let arr_link_id = rebuildLinkIdsFromElements(arr_elements);
	arr_link_id = rebuildLinkIdsToTopElement(arr_link_id);

	let uniquekey_movableContainer_sentence_end,
		arr_seen = [];

	for(let i = INDEX_FIRST; i < arr_link_id.length; i++){
		let int_link_id_from_arr_link_id = arr_link_id[i]['linkId'];
		if(int_link_id_from_arr_link_id === SENTENCE_END){
			uniquekey_movableContainer_sentence_end = arr_link_id[i]['uniqueKey'];
		}
	}

	sentenceElementSortOrder = SORT_FIRST;
	let arr_group_of_words = collectSentenceElementsRecursively(arr_link_id, arr_seen, uniquekey_movableContainer_sentence_end, true);
	let arr_movableContainers = arr_group_of_words['arr_movableContainers'];
	let arr_link_id_add_sort = arr_group_of_words['arr_link_id_add_sort'];

	let arr_addClassName = [],
	int_add_x_first_margin = 50,
	int_add_x_interval = 30;


	if(isHorizontalLayout){
		let arr_link_id_horizontal_layout = buildHorizontalLinkLayout(arr_link_id_add_sort)
		arr_seen = [];
		arr_group_of_words = collectSentenceElementsRecursively(arr_link_id_horizontal_layout, arr_seen, uniquekey_movableContainer_sentence_end, true);
		arr_movableContainers = arr_group_of_words['arr_movableContainers'];
		arr_addClassName = ['clickableContainer', 'horizontalLayout'];
		target_elm_wiseContainersMainContentArea.classList.add('horizontalLayout');
		int_add_x_first_margin = 0;
		int_add_x_interval = 0;
	}
	else{
		target_elm_wiseContainersMainContentArea.classList.remove('horizontalLayout');
		arr_addClassName = ['clickableContainer'];
	}

	let int_depth = 0,
	int_weight_parent = 0,
	arr_balance = [];

	let int_ui_font_size = getWiseUiFontSizePx();
	let int_max_height = int_ui_font_size * 3.5;

	let { int_weight: int_weight_add, arr_balance: updated_arr_balance } = calculateVerticalBalanceRecursively(arr_movableContainers, arr_balance, int_max_height, int_depth, int_weight_parent);
	arr_balance = updated_arr_balance;

	if(arr_balance.length <= 1)return;
	arr_balance = arr_balance.reverse();

	let int_balance_x_interval,
		int_balance_x_total = balanceMarginLeft + int_add_x_first_margin;

	let int_clickableContainer_max_width;

	for(let i = INDEX_FIRST; i < arr_balance.length; i++){
		int_clickableContainer_max_width = 0;
		for(let j = INDEX_FIRST; j < arr_balance[i].length; j++){

			let int_sentence_element_id = escapeNumber(arr_balance[i][j]['sentenceElementId']),
				int_unique_key = escapeNumber(arr_balance[i][j]['uniqueKey']),
				int_japanese_id = escapeNumber(arr_balance[i][j]['japaneseId']),
				int_japanese_element_id = escapeNumber(arr_balance[i][j]['japaneseElementId']),
				int_sub_classification_id = escapeNumber(arr_balance[i][j]['subClassificationId']),
				int_form_id = escapeNumber(arr_balance[i][j]['formId']),
				int_label_id = escapeNumber(arr_balance[i][j]['labelId']),
				int_voice_id = escapeNumber(arr_balance[i][j]['voiceId']),
				str_japanese = escapeHTML(arr_balance[i][j]['japanese']),
				str_kana = escapeHTML(arr_balance[i][j]['kana']),
				str_sub_classification = escapeHTML(arr_balance[i][j]['subClassification']),
				str_form = escapeHTML(arr_balance[i][j]['form']),
				str_voice = escapeHTML(arr_balance[i][j]['voice']),
				int_link_id = escapeHTML(arr_balance[i][j]['linkId']),
				int_link_type = escapeNumber(arr_balance[i][j]['linkType']),
				int_vertical_order = escapeNumber(arr_balance[i][j]['verticalOrder']);

			elm_clickableContainer = document.createElement('div');
			elm_clickableContainer.setAttribute('id', 'clickableContainer'+int_unique_key);
			elm_clickableContainer.classList.add(...arr_addClassName);
			elm_clickableContainer.style.left = int_balance_x_total+'px';
			elm_clickableContainer.style.top = (escapeNumber(arr_balance[i][j]['top']) + 50)+'px';

			elm_clickableContainer.dataset.sentenceElementId = int_sentence_element_id;
			elm_clickableContainer.dataset.uniqueKey = int_unique_key;
			elm_clickableContainer.dataset.japaneseId = int_japanese_id;
			elm_clickableContainer.dataset.japaneseElementId = int_japanese_element_id;
			elm_clickableContainer.dataset.subClassificationId = int_sub_classification_id;
			elm_clickableContainer.dataset.formId = int_form_id;
			elm_clickableContainer.dataset.labelId = int_label_id;
			elm_clickableContainer.dataset.voiceId = int_voice_id;
			elm_clickableContainer.dataset.japanese = str_japanese;
			elm_clickableContainer.dataset.kana = str_kana;
			elm_clickableContainer.dataset.subClassification = str_sub_classification;
			elm_clickableContainer.dataset.form = str_form;
			elm_clickableContainer.dataset.voice = str_voice;
			elm_clickableContainer.dataset.linkType = int_link_type;
			elm_clickableContainer.dataset.horizontalOrder = i;
			elm_clickableContainer.dataset.verticalOrder = int_vertical_order;

			if(int_link_id !== SENTENCE_END){
			elm_clickableContainer.dataset.linkId = int_link_id;
			}

			elm_clickableLeftLinkMarker = document.createElement('div');
			elm_clickableLeftLinkMarker.setAttribute('id', 'leftLinkMarker'+int_unique_key);
			elm_clickableLeftLinkMarker.classList.add('leftLinkMarker', 'clickableLeftLinkMarker');
			elm_clickableContainer.appendChild(elm_clickableLeftLinkMarker);

			elm_clickableInnerContainer = document.createElement('div');
			elm_clickableInnerContainer.setAttribute('id', 'clickableInnerContainer'+int_unique_key);
			elm_clickableInnerContainer.classList.add('clickableInnerContainer', 'wiseUiFontSizeTarget');
			elm_clickableInnerContainer.textContent = str_japanese;

			elm_clickableBaseContainer = document.createElement('div');
			elm_clickableBaseContainer.setAttribute('id', 'clickableBaseContainer'+int_unique_key);
			elm_clickableBaseContainer.classList.add('clickableBaseContainer');

			elm_clickableBaseContainer.appendChild(elm_clickableInnerContainer);
			elm_clickableContainer.appendChild(elm_clickableBaseContainer);

			elm_clickableRightLinkMarker = document.createElement('div');
			elm_clickableRightLinkMarker.setAttribute('id', 'rightLinkMarker'+int_unique_key);
			elm_clickableRightLinkMarker.classList.add('rightLinkMarker', 'clickableRightLinkMarker');

			elm_clickableContainer.appendChild(elm_clickableRightLinkMarker);

			target_elm_wiseContainersMainContentArea.appendChild(elm_clickableContainer);

			if(int_clickableContainer_max_width<elm_clickableContainer.offsetWidth){
			int_clickableContainer_max_width=elm_clickableContainer.offsetWidth;
			}
		}
		int_balance_x_interval = int_clickableContainer_max_width+int_add_x_interval;
		int_balance_x_total = int_balance_x_total+int_balance_x_interval;
	}

	if(isHorizontalLayout){
		return;
	}else{
		let elms = document.querySelectorAll('.clickableContainer'),
		elm;

		for(let i = INDEX_FIRST; i < elms.length; i++){
			elm = elms[i];
			if(elm.hasAttribute('data-link-id')){
				createLinkedCanvas(canvasLinkedContainer, elm, target_elm_wiseBody);
			}
		}
		applyWiseCanvasLayout();
		return;
	}

}

function buildHorizontalLinkLayout(arr_link_id_add_sort){

	let arr_link_id_horizontal_layout = [...arr_link_id_add_sort].sort((a, b) => a.sort - b.sort);

	for (let i = INDEX_FIRST; i < arr_link_id_horizontal_layout.length - LAST_INDEX_OFFSET; i++) {
		const currentElement = arr_link_id_horizontal_layout[i];
		const nextElement = arr_link_id_horizontal_layout[i + 1];

		if (i < arr_link_id_horizontal_layout.length - LAST_INDEX_OFFSET) {
		const nextUniqueKey = nextElement.uniqueKey;
		currentElement.linkId = nextUniqueKey;
		}
	}
	return arr_link_id_horizontal_layout;
}




clickHandlers['override:update'] = async function (btn) {
	await updateLayerElementOverride(btn);
};
async function updateLayerElementOverride(btn) {
    const li = btn.closest('.sentenceLayerUpdateOverrideScreenLi');
    if (!li) {
        console.warn('parent li not found');
        return;
    }

    const layerElementOverrideId = parseIntStrict(
        btn.dataset.layerElementOverrideId || li.dataset.layerElementOverrideId
    );
    if (!layerElementOverrideId) {
        console.warn('invalid id');
        return;
    }

    const layerElementId = parseIntStrict(li.dataset.layerElementId);

    const opSelect = li.querySelector('.sentenceLayerUpdateOverrideScreenLiMastaOverrideId');
    const freeInput = li.querySelector('.sentenceLayerUpdateOverrideScreenLiFreeDisplayInput');
    const highlightToggle = li.querySelector('.sentenceLayerUpdateOverrideScreenLiHighlightToggleButton');
    const sortInput = li.querySelector('.sentenceLayerUpdateOverrideScreenLiSortInput');

    const override_id = parseIntStrict(opSelect?.value);
    if (!override_id) {
        alert('Operation is required.');
        return;
    }

    const display_text = (freeInput?.value || '').trim();

	const is_highlighted = Number(highlightToggle.checked);

    const sortVal = parseIntStrict(sortInput?.value);
    if (sortVal === null || sortVal < SORT_FIRST) {
        alert('Sort must be an integer (>= '+SORT_FIRST+').');
        return;
    }
	
	let isConfirmed = window.confirm(MSG_UPDATE_CONFIRM[intSelectedLanguage]);
	if(!isConfirmed) {return;}
	
	setElementDisabled(btn, true);
	
    try {
	
		const payload = {
			id: layerElementOverrideId,
			override_id: override_id,
			display_text: display_text,
			is_highlighted: is_highlighted,
			sort: sortVal,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerUpdateOverrideUrl,
			payload,
			60000
		);
        const currentId = layerElementId ?? parseIntStrict(overrideCreateButton?.dataset.layerElementId);
        if (currentId) {
            await showOverrideElements(currentId);
        }
        alert('completed');
    } catch (error) {
        console.error('Error:', error.message || error);
        alert('オーバーライド更新に失敗しました');
    } finally {
		setElementDisabled(btn, false);
	}
}


clickHandlers['override:delete'] = async function (btn) {
	await deleteLayerElementOverride(btn);
};
async function deleteLayerElementOverride(btn) {

	const li = btn.closest('.sentenceLayerUpdateOverrideScreenLi');
    if (!li) {
        console.warn('parent li not found');
        return;
    }
	
    const layerElementOverrideId = parseIntStrict(
        btn.dataset.layerElementOverrideId || li.dataset.layerElementOverrideId
    );
    if (!layerElementOverrideId) {
        console.warn('invalid id');
        return;
    }

	const layerElementId = parseIntStrict(li.dataset.layerElementId);
	
	let isConfirmed = window.confirm(MSG_DELETE_CONFIRM[intSelectedLanguage]);
	if(!isConfirmed) {return;}
	
	setElementDisabled(btn, true);
	
	try {
	
		const payload = {
			id: layerElementOverrideId,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerDeleteOverrideUrl,
			payload,
			60000
		);

		const currentId =
			layerElementId ?? parseIntStrict(overrideCreateButton?.dataset.layerElementId);

		if (currentId) {
			await showOverrideElements(currentId);
		}

		alert('completed');

	} catch (error) {
		console.error('Error:', error.message || error);
		alert('オーバーライド削除に失敗しました');

	} finally {
		setElementDisabled(btn, false);
	}
}
