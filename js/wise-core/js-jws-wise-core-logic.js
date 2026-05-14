

function showWiseOverlay(el, { interactive = true } = {}) {
    if (!el) return;
    el.classList.add('overlay-on');
    if (interactive) {
        el.removeAttribute('aria-hidden');
        el.inert = false;
    } else {
        el.setAttribute('aria-hidden', 'true');
        el.inert = true;
    }
}

function calculateTextareaSize(elm, text){

    text = escapeTextareaMeasurementText(text);

    let fontSize = parseFloat(getComputedStyle(elm).fontSize);
	if (!Number.isFinite(fontSize)) {
        fontSize = wiseMovableContainerFontSize;
    }

    const span = document.createElement('span');
    span.style.fontSize = fontSize + 'px';
    span.style.whiteSpace = 'pre';
    span.style.left = '0px';
    span.style.top = '0px';
    span.style.position = 'absolute';
    span.style.visibility = 'hidden';
    span.style.lineHeight = '1';
    span.textContent = text;

    document.body.appendChild(span);

    const width = span.offsetWidth + 10;
    const height = span.offsetHeight;

    document.body.removeChild(span);

    return { width, height };
}

function updateTextareaContainerSize(elm, text){

	updateTextareaSize(elm, text);

	let send_text = text.replace(/\s/g, '');

	let uniqueKey_targetContainer = escapeNumber(elm.dataset.uniqueKey),
		idName_targetContainer = 'movableContainer' + uniqueKey_targetContainer,
		elm_targetContainer = document.getElementById(idName_targetContainer);

	if(send_text.length === LENGTH_EMPTY){
		delete elm_targetContainer.dataset.japanese;
		elm.classList.add('emptyText');
	}
	else{
		elm_targetContainer.dataset.japanese = send_text;
		elm.classList.remove('emptyText');
	}

	updateLinkContainerWidths();
	ensureMovableContainerInsideBounds(elm_targetContainer);
	redrawLinkLines();
}

function ensureMovableContainerInsideBounds(elm_targetContainer) {

    if (!(elm_targetContainer instanceof HTMLElement)) {
        return;
    }

    const parent = wisePanelWhiteboardViewMainContentArea;

    if (!(parent instanceof HTMLElement)) {
        return;
    }

    const zoomScale = getWiseZoomScale();

    const parentWidth = parent.clientWidth;
    const parentHeight = parent.clientHeight;

    const left = parseFloat(elm_targetContainer.style.left) || 0;
    const top = parseFloat(elm_targetContainer.style.top) || 0;

    const rect = elm_targetContainer.getBoundingClientRect();

    const width = rect.width / zoomScale;
    const height = rect.height / zoomScale;

    let nextLeft = left;
    let nextTop = top;

    if (nextLeft + width > parentWidth) {
        nextLeft = parentWidth - width;
    }

    if (nextTop + height > parentHeight) {
        nextTop = parentHeight - height;
    }

    if (nextLeft < 0) {
        nextLeft = 0;
    }

    if (nextTop < 0) {
        nextTop = 0;
    }

    elm_targetContainer.style.left = nextLeft + 'px';
    elm_targetContainer.style.top = nextTop + 'px';
}

function updateTextareaSize(elm, text) {

    const size = calculateTextareaSize(elm, text);

    if (elm.classList.contains('plainformQuizTableTextarea')) {
        return;
    }

    if (
        elm.classList.contains('grammarInsightsDisplayAreaLiTextAreaUserInput') ||
        elm.classList.contains('wiseUpdateUserInputDataTextarea')
    ) {
        elm.style.height = 'auto';
        elm.style.height = elm.scrollHeight + 'px';
        return;
    }

    let minWidth = null;

    if (elm.classList.contains('innerContainerTextArea')) {
        minWidth = TEXTAREA_CONTAINER_MIN_WIDTH;
    } else if (elm.classList.contains('wisePanelChartTextarea')) {
        minWidth = CHART_TEXTAREA_MIN_WIDTH;
    } else {
        return;
    }

    if (size.width < minWidth) {
        elm.style.width = minWidth + 'px';
    } else if (size.width > textAreaContainerMaxWidthCurrent) {
        elm.style.width = textAreaContainerMaxWidthCurrent + 'px';
    } else {
        elm.style.width = size.width + 'px';
    }

    const computed = getComputedStyle(elm);

    let lineHeight = parseFloat(computed.lineHeight);
    if (!Number.isFinite(lineHeight)) {
        const fontSize = parseFloat(computed.fontSize) || 16;
        lineHeight = fontSize;
    }

    const paddingTop = parseFloat(computed.paddingTop) || 0;
    const paddingBottom = parseFloat(computed.paddingBottom) || 0;
    const borderTop = parseFloat(computed.borderTopWidth) || 0;
    const borderBottom = parseFloat(computed.borderBottomWidth) || 0;

    const value = String(text ?? '');
    const lineCount = value === '' ? 1 : value.split(/\r?\n/).length;

	let textareaMaxHeight = 300;

	if (
		typeof BOARD_HEIGHT !== 'undefined' &&
		Number.isFinite(BOARD_HEIGHT)
	) {

		textareaMaxHeight = Math.max(
			BOARD_HEIGHT - 100,
			1
		);

	}

	elm.style.height = 'auto';

	if (lineCount <= 1) {

		elm.style.height = (
			lineHeight +
			paddingTop +
			paddingBottom +
			borderTop +
			borderBottom
		) + 'px';

		elm.classList.remove('innerContainerTextAreaFadeout');

	}
	else {

		const nextHeight = Math.min(
			elm.scrollHeight,
			textareaMaxHeight
		);

		elm.style.height = nextHeight + 'px';

		if (elm.scrollHeight > textareaMaxHeight) {
			elm.classList.add('innerContainerTextAreaFadeout');
		}
		else {
			elm.classList.remove('innerContainerTextAreaFadeout');
		}

	}

}

function changeWiseVerticalToolbarButton(wiseCanvasType) {

    clearSelection();
    currentSelectedMovableContainers = [];
    resetSelectedElements();

    let elms_movableContainer = document.querySelectorAll('.movableContainer'),
        elm_movableContainer,
        elms_linkContainer,
        elm_linkContainer,
        elms_baseContainer,
        elm_baseContainer;

    for (let i = INDEX_FIRST; i < elms_movableContainer.length; i++) {
        elm_movableContainer = elms_movableContainer[i];
        elm_movableContainer.classList.remove('deleteTarget');

        if (currentWiseToolbarButton === 'wiseVerticalToolbarCreateLinkButton') {
            elm_movableContainer.classList.add('linkView');
        } else {
            elm_movableContainer.classList.remove('linkView');
        }

        elms_linkContainer = elm_movableContainer.querySelectorAll('.linkContainer');
        for (let j = INDEX_FIRST; j < elms_linkContainer.length; j++) {
            elm_linkContainer = elms_linkContainer[j];

            if (currentWiseToolbarButton === 'wiseVerticalToolbarCreateLinkButton') {
                if (currentInteractionMode === 'createLinkMode') {
                    elm_linkContainer.classList.add('linkContainer-displayed');
                    elm_linkContainer.classList.remove('linkContainer-displayed-toSentence');
                } else {
                    elm_linkContainer.classList.add('linkContainer-displayed-toSentence');
                    elm_linkContainer.classList.remove('linkContainer-displayed');
                }
            } else {
                elm_linkContainer.classList.remove('linkContainer-displayed');
                elm_linkContainer.classList.remove('linkContainer-displayed-toSentence');
            }
        }

        elms_baseContainer = elm_movableContainer.querySelectorAll('.baseContainer');
        for (let j = INDEX_FIRST; j < elms_baseContainer.length; j++) {
            elm_baseContainer = elms_baseContainer[j];
            elm_baseContainer.classList.remove('deleteTarget');
        }
    }

    wiseCanvasOriginal.style.pointerEvents = 'none';
    wiseCanvasHandWriting.style.pointerEvents = 'none';

    if (globalCanvas !== null) {
        globalCanvas.style.pointerEvents = 'none';
    }

    switch (wiseCanvasType) {
        case 'wiseCanvasOriginal':
            wiseCanvasOriginal.style.pointerEvents = 'auto';
            break;

        case 'wiseCanvasHandWriting':
            wiseCanvasHandWriting.style.pointerEvents = 'auto';
            break;

        case 'globalCanvas':
            if (globalCanvas !== null) {
                globalCanvas.style.pointerEvents = 'auto';
            }
            break;

        default:
            break;
    }

    let buttons = document.querySelectorAll('.wiseLeftVerticalToolbarButton');

    for (let i = INDEX_FIRST; i < buttons.length; i++) {
        let button = buttons[i];
        if (button.id === currentWiseToolbarButton) {
            button.classList.add('wiseLeftVerticalToolbarButton-selected');
			updateWiseToolbarCurrentButton(button);
        } else {
            button.classList.remove('wiseLeftVerticalToolbarButton-selected');
        }
    }

    closeWiseMenuBars();
    redrawLinkLines();
}

function openWiseMenuBar(menubar){
	if(!menubar)return;
	menubar.classList.toggle('wiseMenuBar-open');
	requestAnimationFrame(() => {
        positionWiseMenuBars();
    });
}

function closeWiseMenuBars(){
	let elms_wiseMenuBar = document.querySelectorAll('.wiseMenuBar'),
		elm_wiseMenuBar;
	for(let i = INDEX_FIRST; i < elms_wiseMenuBar.length; i++){
		elm_wiseMenuBar = elms_wiseMenuBar[i];
		elm_wiseMenuBar.classList.remove('wiseMenuBar-open');
	}
}

function clearSelection() {
	if (window.getSelection) {
		let selection = window.getSelection();
		if (selection.rangeCount > COUNT_EMPTY) {
			selection.removeAllRanges();
		}
	}
}


function createGrammarViewJumpButton(arr_explanation, obj){

	const button = document.createElement('button');
	button.classList.add(...arr_explanation.arrClassButton);
	button.textContent = arr_explanation.str_title;
	button.dataset.uniqueCode = arr_explanation.str_grammarUniqueCode;

	if (arr_explanation.str_grammarUniqueCode.length !== LENGTH_EMPTY) {
		button.addEventListener('pointerup', async function() {
			if(
				grammarExplanationDisplayArea !== null
			){
				currentGrammarExplanationIndex = grammarExplanationHistory.length-1;
				
				switchToGrammarExplanationPanel();

				await createGrammarExplanation(arr_explanation.str_grammarUniqueCode);
				const idx = saveStateGrammarExplanation(obj, { behavior: 'append' });
				if (idx !== null) {
					displayCurrentStateGrammarExplanation(idx, true);
				}
			}
			else{
				openGrammarViewInNewTab(arr_explanation.str_grammarUniqueCode);
			}
		}, false);
	}
	arr_explanation.appendChildTarget.appendChild(button);
}

function calculateVerticalBalanceRecursively(arr_movableContainers, arr_balance, int_movableContainer_max_height, int_depth, int_weight_parent){

	let childElms,
		childElms_current;

	let int_depth_current = int_depth,
		int_weight_parent_current = int_weight_parent,
		int_weight = 0;

	if('childElms' in arr_movableContainers){
		++int_depth;
		childElms = arr_movableContainers['childElms'];
		for(let i = INDEX_FIRST; i < childElms.length; i++){
			childElms_current = childElms[i];
			let { int_weight: int_weight_add, arr_balance: updated_arr_balance } = calculateVerticalBalanceRecursively(childElms_current, arr_balance, int_movableContainer_max_height, int_depth, int_weight_parent);
			int_weight += int_weight_add;
			int_weight_parent += int_weight_add;
			arr_balance = updated_arr_balance;
		}
	}
	else{
		int_weight = 1;
	}

	let arr_breadth,
		arr_breadth_add;

	if(int_depth_current in arr_balance){
		arr_breadth = arr_balance[int_depth_current];
	}else{
		arr_breadth = [];
	}

	// デバッグ 未定義idを1として利用している？
	arr_breadth_add = {
		sentenceElementId : arr_movableContainers['sentenceElementId'] !== null ? arr_movableContainers['sentenceElementId'] : 0,
		idName : arr_movableContainers['idName'],
		uniqueKey : arr_movableContainers['uniqueKey'],
		japaneseId : arr_movableContainers['japaneseId'],
		japaneseElementId : arr_movableContainers['japaneseElementId'],
		subClassificationId : arr_movableContainers['subClassificationId'],
		formId : arr_movableContainers['formId'],
		labelId : arr_movableContainers['labelId'],
		voiceId : arr_movableContainers['voiceId'],
		linkId : arr_movableContainers['linkId'],
		linkType : arr_movableContainers['linkType'],
		japanese : arr_movableContainers['japanese'],
		kana : arr_movableContainers['kana'],
		subClassification : arr_movableContainers['subClassification'],
		form : arr_movableContainers['form'],
		voice : arr_movableContainers['voice'],
		top : balanceMarginTop+((int_movableContainer_max_height)*(((int_weight+1)/2)-1+int_weight_parent_current)),
		verticalOrder : (((int_weight+1)/2)-1+int_weight_parent_current),
		int_weight : int_weight
	};
	arr_breadth.push(arr_breadth_add);
	arr_balance[int_depth_current] = arr_breadth;

	return { int_weight: int_weight, arr_balance: arr_balance };
}

function getPhraseClauseContainerInfoCollection(elm_movableContainer, doSelectAllWordContainers){

	let elms_movableContainer = document.querySelectorAll('.movableContainer'),
		arr_link_id = [],
		arr_link_info_collection = [],
		arr_seen = [],
		doesNotHaveWordContainerClass;

	arr_link_info_collection = buildSentenceLinkInfoCollection(elms_movableContainer);
	arr_link_id = arr_link_info_collection['arr_link_id'];
	doesNotHaveWordContainerClass = arr_link_info_collection['doesNotHaveWordContainerClass'];

	let uniqueKey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey);

	sentenceElementSortOrder = SORT_FIRST;

	let arr_group_of_words = collectSentenceElementsRecursively(arr_link_id, arr_seen, uniqueKey_movableContainer, doSelectAllWordContainers);

	arr_group_of_words['doesNotHaveWordContainerClass'] = doesNotHaveWordContainerClass;

	return arr_group_of_words;
}


/******************************************************
 * collectSentenceElementsRecursively
 * applySortOrderToLinkIds
 ******************************************************/
function collectSentenceElementsRecursively(arr_link_id, arr_seen, uniqueKey_movableContainer, doSelectAllWordContainers, depth = 0){

	uniqueKey_movableContainer = escapeNumber(uniqueKey_movableContainer);

	let doHave = arr_seen.includes(uniqueKey_movableContainer),
		arr_group_of_words = {},
		arr_movableContainers_add = [];

	if(doHave){
		return arr_group_of_words = {
			is_empty: true,
			str_group_of_words_japanese : '',
			str_group_of_words_kana : '',
			str_group_of_words_japanesePhraseClause : '',
			str_group_of_words_kanaPhraseClause : '',
			arr_movableContainers : {},
			arr_link_id_add_sort : arr_link_id
		};
	}

	arr_seen.push(uniqueKey_movableContainer);

	let matchingItem = arr_link_id.find(item => item.uniqueKey === uniqueKey_movableContainer);

	if(!matchingItem){
		return arr_group_of_words = {
			is_empty: true,
			str_group_of_words_japanese : '',
			str_group_of_words_kana : '',
			str_group_of_words_japanesePhraseClause : '',
			str_group_of_words_kanaPhraseClause : '',
			arr_movableContainers : {},
			arr_link_id_add_sort : arr_link_id
		};
	}

	// 未定義id null変更
	let int_sentence_element_id = escapeNumber(matchingItem['sentenceElementId'] !== null ? matchingItem['sentenceElementId'] : 0),
		str_japanese = escapeHTML(matchingItem['japanese']),
		str_kana = escapeHTML(matchingItem['kana']),
		int_japanese_id = escapeNumber(matchingItem['japaneseId']),
		int_japanese_element_id = escapeNumber(matchingItem['japaneseElementId']),
		int_sub_classification_id = escapeNumber(matchingItem['subClassificationId']),
		int_form_id = escapeNumber(matchingItem['formId']),
		int_label_id = escapeNumber(matchingItem['labelId']),
		int_voice_id = escapeNumber(matchingItem['voiceId']),
		str_sub_classification = escapeHTML(matchingItem['subClassification']),
		str_form = escapeHTML(matchingItem['form']),
		str_voice = escapeHTML(matchingItem['voice']),
		str_phraseClauseType = escapeHTML(matchingItem['phraseClauseType']),
		int_phraseClauseId = escapeNumber(matchingItem['phraseClauseId']),
		str_japanesePhraseClause = escapeHTML(matchingItem['japanesePhraseClause']),
		str_kanaPhraseClause = escapeHTML(matchingItem['kanaPhraseClause']),
		int_boundsTop = escapeFloatNumber(matchingItem['boundsTop']),
		int_boundsLeft = escapeFloatNumber(matchingItem['boundsLeft']),
		int_link_id = escapeNumber(matchingItem['linkId']),
		int_link_type = escapeNumber(matchingItem['linkType']);

	let idName_movableContainer = 'movableContainer'+uniqueKey_movableContainer,
		uniqueKey_movableContainer_targetKey,
		arr_uniqueKey_movableContainer = [];


	for(let i = INDEX_FIRST; i < arr_link_id.length; i++){
		if(arr_link_id[i]['linkId'] !== uniqueKey_movableContainer)continue;
		if(!doSelectAllWordContainers && arr_link_id[i]['linkType'] !== LINK_TYPE_NORMAL)continue;
		arr_uniqueKey_movableContainer.push(arr_link_id[i]);
	}


	if(arr_uniqueKey_movableContainer.length === LENGTH_EMPTY){
		let arr_link_id_add_sort = applySortOrderToLinkIds(arr_link_id, uniqueKey_movableContainer);

		let arr_movableContainers = {
			sentenceElementId : int_sentence_element_id,
			idName : idName_movableContainer,
			uniqueKey : uniqueKey_movableContainer,
			japaneseId : int_japanese_id,
			japaneseElementId : int_japanese_element_id,
			subClassificationId : int_sub_classification_id,
			formId : int_form_id,
			labelId : int_label_id,
			voiceId : int_voice_id,
			japanese : str_japanese,
			kana : str_kana,
			subClassification : str_sub_classification,
			form : str_form,
			voice : str_voice,
			phraseClauseType : str_phraseClauseType,
			phraseClauseId : int_phraseClauseId,
			japanesePhraseClause : str_japanesePhraseClause,
			kanaPhraseClause : str_kanaPhraseClause,
			boundsTop : int_boundsTop,
			boundsLeft : int_boundsLeft,
			linkId : int_link_id,
			linkType : int_link_type
		};

		arr_group_of_words = {
			is_empty: false,
			str_group_of_words_japanese : str_japanese,
			str_group_of_words_kana : str_kana,
			str_group_of_words_japanesePhraseClause : str_japanese,
			str_group_of_words_kanaPhraseClause : str_kana,
			arr_movableContainers : arr_movableContainers,
			arr_link_id_add_sort : arr_link_id_add_sort
		};
		return arr_group_of_words;
	}

	let str_japanese_merge = '',
		str_kana_merge = '',
		str_japanesePhraseClause_merge = '',
		str_kanaPhraseClause_merge = '';

	arr_uniqueKey_movableContainer.sort((a, b) => {
		if (a.linkType !== b.linkType) {
			return b.linkType - a.linkType;
		}
		return a.boundsTop - b.boundsTop;
	});

	for(let i = INDEX_FIRST; i < arr_uniqueKey_movableContainer.length; i++){

		uniqueKey_movableContainer_targetKey = escapeNumber(arr_uniqueKey_movableContainer[i]['uniqueKey']);

		let arr_group_of_words_add = collectSentenceElementsRecursively(arr_link_id, arr_seen, uniqueKey_movableContainer_targetKey, doSelectAllWordContainers, depth+1);

		arr_link_id = arr_group_of_words_add.arr_link_id_add_sort;
		if(arr_group_of_words_add.is_empty){
			continue;
		}

		str_japanese_merge = str_japanese_merge+arr_group_of_words_add['str_group_of_words_japanese'];
		str_kana_merge = str_kana_merge+arr_group_of_words_add['str_group_of_words_kana'];
		str_japanesePhraseClause_merge = str_japanesePhraseClause_merge+arr_group_of_words_add['str_group_of_words_japanesePhraseClause'];
		str_kanaPhraseClause_merge = str_kanaPhraseClause_merge+arr_group_of_words_add['str_group_of_words_kanaPhraseClause'];
		let arr_movableContainers_chileElms = arr_group_of_words_add['arr_movableContainers']
		arr_movableContainers_add[i] = arr_movableContainers_chileElms;
	}

	let arr_movableContainers = {
		sentenceElementId : int_sentence_element_id,
		idName : idName_movableContainer,
		uniqueKey : uniqueKey_movableContainer,
		japaneseId : int_japanese_id,
		japaneseElementId : int_japanese_element_id,
		subClassificationId : int_sub_classification_id,
		formId : int_form_id,
		labelId : int_label_id,
		voiceId : int_voice_id,
		japanese : str_japanese,
		kana : str_kana,
		subClassification : str_sub_classification,
		form : str_form,
		voice : str_voice,
		phraseClauseType : str_phraseClauseType,
		phraseClauseId : int_phraseClauseId,
		japanesePhraseClause : str_japanesePhraseClause,
		kanaPhraseClause : str_kanaPhraseClause,
		boundsTop : int_boundsTop,
		boundsLeft : int_boundsLeft,
		linkId : int_link_id,
		linkType : int_link_type,
		childElms : arr_movableContainers_add
	};

	str_japanese_merge = str_japanese_merge+str_japanese;
	str_kana_merge = str_kana_merge+str_kana;

	if(depth === 0){
		str_japanesePhraseClause_merge = str_japanesePhraseClause_merge;
		str_kanaPhraseClause_merge = str_kanaPhraseClause_merge;
	}
	else{
		str_japanesePhraseClause_merge = str_japanesePhraseClause_merge+str_japanese;
		str_kanaPhraseClause_merge = str_kanaPhraseClause_merge+str_kana;
	}

	let arr_link_id_add_sort = applySortOrderToLinkIds(arr_link_id, uniqueKey_movableContainer);

	return arr_group_of_words = {
		is_empty: false,
		str_group_of_words_japanese : str_japanese_merge,
		str_group_of_words_kana : str_kana_merge,
		str_group_of_words_japanesePhraseClause : str_japanesePhraseClause_merge,
		str_group_of_words_kanaPhraseClause : str_kanaPhraseClause_merge,
		arr_movableContainers : arr_movableContainers,
		arr_link_id_add_sort : arr_link_id_add_sort
	};
}

function applySortOrderToLinkIds(arr_link_id, uniqueKey_movableContainer){
	for(let i = INDEX_FIRST; i < arr_link_id.length; i++){
		if(arr_link_id[i]['uniqueKey'] === uniqueKey_movableContainer){
			arr_link_id[i]['sort'] = sentenceElementSortOrder;
			++sentenceElementSortOrder;
		}
	}
	return arr_link_id;
}

function rebuildLinkIdsFromElements(arr_elements){

	let arr_link_id = [];

	for(let i = INDEX_FIRST; i < arr_elements.length; i++){

		// 未定義id null変更
		const int_sentence_element_id = escapeNumber(
			arr_elements[i].id !== null ? arr_elements[i].id : 0
		);
		const idName_movableContainer = escapeHTML(arr_elements[i].idName);
		const uniqueKey_movableContainer = escapeNumber(arr_elements[i].uniqueKey);
		const int_japanese_id = escapeNumber(arr_elements[i].japaneseId);
		const int_japanese_element_id = escapeNumber(arr_elements[i].japaneseElementId);
		const int_sub_classification_id = escapeNumber(arr_elements[i].subClassificationId);
		const int_form_id = escapeNumber(arr_elements[i].formId);
		const int_label_id = escapeNumber(arr_elements[i].labelId);
		const int_voice_id = escapeNumber(arr_elements[i].voiceId);
		const int_top = Number(arr_elements[i].boundsTop);
		const int_left = Number(arr_elements[i].boundsLeft);
		const int_link_id = escapeNumber(arr_elements[i].linkId);
		const int_link_type = escapeNumber(arr_elements[i].linkType);
		const str_japanese = escapeHTML(arr_elements[i].japanese);
		const str_kana = escapeHTML(arr_elements[i].kana);
		const str_subClassification = escapeHTML(arr_elements[i].subClassification);
		const str_phraseClauseType = escapeHTML(arr_elements[i].phraseClauseType);
		const int_phraseClauseId = escapeNumber(arr_elements[i].phraseClauseId);
		const str_japanesePhraseClause = escapeHTML(arr_elements[i].japanesePhraseClause);
		const str_kanaPhraseClause = escapeHTML(arr_elements[i].kanaPhraseClause);
		const str_form = escapeHTML(arr_elements[i].form);
		const str_voice = escapeHTML(arr_elements[i].voice);


		if(arr_elements[i].phraseClauseType === 'phraseClauseContainer'){
			arr_link_id[i] = {
				sentenceElementId : int_sentence_element_id,
				idName : idName_movableContainer,
				uniqueKey : uniqueKey_movableContainer,
				japaneseId : int_japanese_id,
				japaneseElementId : int_japanese_element_id,
				subClassificationId : int_sub_classification_id,
				formId : int_form_id,
				labelId : int_label_id,
				voiceId : int_voice_id,
				boundsTop : int_top,
				boundsLeft : int_left,
				linkId : int_link_id,
				linkType : int_link_type,
				japanese : str_japanese,
				kana : str_kana,
				subClassification : str_subClassification,
				phraseClauseType : str_phraseClauseType,
				phraseClauseId : int_phraseClauseId,
				japanesePhraseClause : str_japanesePhraseClause,
				kanaPhraseClause : str_kanaPhraseClause,
				form : str_form,
				voice : str_voice
			};
		}
		else{
			arr_link_id[i] = {
				sentenceElementId : int_sentence_element_id,
				idName : idName_movableContainer,
				uniqueKey : uniqueKey_movableContainer,
				japaneseId : int_japanese_id,
				japaneseElementId : int_japanese_element_id,
				subClassificationId : int_sub_classification_id,
				formId : int_form_id,
				labelId : int_label_id,
				voiceId : int_voice_id,
				boundsTop : int_top,
				boundsLeft : int_left,
				linkId : int_link_id,
				linkType : int_link_type,
				japanese : str_japanese,
				kana : str_kana,
				subClassification : str_subClassification,
				phraseClauseType : str_phraseClauseType,
				phraseClauseId : int_phraseClauseId,
				form : str_form,
				voice : str_voice
			};
		}
	}
	return arr_link_id;
}

function rebuildLinkIdsToTopElement(arr_link_id){

	let arr_phraseClauseContainer = arr_link_id.filter(item => item.phraseClauseType === 'phraseClauseContainer').map(item => item.uniqueKey);

	if(arr_phraseClauseContainer.length === LENGTH_EMPTY)return arr_link_id;

	for (let i = INDEX_FIRST; i < arr_phraseClauseContainer.length; i++) {
		let currentUniqueKey = arr_phraseClauseContainer[i];
		let modifiedLinkId;
		for (let j = INDEX_FIRST; j < arr_link_id.length; j++) {
			if(arr_link_id[j].phraseClauseId === currentUniqueKey && arr_link_id[j].phraseClauseType === 'itemOfPhraseClauseContainerTopElement'){
				modifiedLinkId = arr_link_id[j].uniqueKey;
				break;
			}
		}
		for (let j = INDEX_FIRST; j < arr_link_id.length; j++) {
			if(arr_link_id[j].linkId === currentUniqueKey && arr_link_id[j].linkType !== LINK_TYPE_NORMAL){
				arr_link_id[j].linkId = modifiedLinkId;
			}
		}
	}
	return arr_link_id;
}



/******************************************************
 * 
 ******************************************************/
function buildSentenceWordGroup(){

	let elms_movableContainer = document.querySelectorAll('.movableContainer'),
		uniquekey_movableContainer_sentence_end,
		isPermitted,
		doesNotHaveWordContainerClass,
		arr_link_id = [],
		arr_link_info_collection = [],
		arr_seen = [],
		arr_group_of_words = [],
		arr_group_of_words_info_collection = [];

	if(elms_movableContainer.length <= 1)return arr_group_of_words_info_collection;

	arr_link_info_collection = buildSentenceLinkInfoCollection(elms_movableContainer);
	isPermitted = arr_link_info_collection['isPermitted'];
	doesNotHaveWordContainerClass = arr_link_info_collection['doesNotHaveWordContainerClass'];

	if(!isPermitted){
		alert(MSG_ERROR_SENTENCE_STRUCTURE[intSelectedLanguage]);
		return arr_group_of_words_info_collection;
	}
	if(doesNotHaveWordContainerClass){
		alert(MSG_ERROR_INVALID_WORD_CONTAINER[intSelectedLanguage]);
		return arr_group_of_words_info_collection;
	}

	arr_link_id = arr_link_info_collection['arr_link_id'];
	uniquekey_movableContainer_sentence_end = arr_link_info_collection['uniquekey_movableContainer_sentence_end'];

	sentenceElementSortOrder = SORT_FIRST;
	arr_group_of_words = collectSentenceElementsRecursively(arr_link_id, arr_seen, uniquekey_movableContainer_sentence_end, true);

	return arr_group_of_words;
}

function openWiseMenuBarLinks(e){
	currentWiseToolbarButton = 'wiseVerticalToolbarCreateLinkButton';
	currentInteractionMode = 'createLinkMode';
	changeWiseVerticalToolbarButton('');
	e.preventDefault();
	toolbarLongPressTimer = setTimeout(() => {
		openWiseMenuBar(wiseMenuLinks);
	}, 500);
}

clickHandlers['overlay:close'] = function (btn, e) {
	e.preventDefault();
	e.stopPropagation();
	e.stopImmediatePropagation();
    handleOverlayCloseClick(btn);
};

function buildRegisterTextareaValuesPayload() {

    const sessionEndTime = Date.now();

    const url = new URL(window.location.href);
    const currentURL = window.location.href;
    const params = url.searchParams;

    let send_grammar_unique_code = 0;

    if (currentURL.includes('register-sentence')) {
        send_grammar_unique_code = escapeHTML(
            params.get(KEY_GRAMMAR_UNIQUE_CODE)
        );
    }

    return {
        sessionEndTime: escapeNumber(sessionEndTime),
        recorded_textarea_values: recordedTextareaValues,
        send_grammar_unique_code: send_grammar_unique_code,
        int_selected_language: intSelectedLanguage
    };
}

function registerTextareaValuesByPostJson() {

    const payload = buildRegisterTextareaValuesPayload();

    return postJson(
        wiseCoreRegisterTextareaValuesUrl,
        payload,
        10000
    ).catch(e => {

        if (e && typeof e.message === 'string' && e.message.includes('timeout')) {
            console.error('タイムアウトが発生しました。');
            return;
        }

        console.error('registerTextareaValuesByPostJson error:', e);
    });
}

function registerTextareaValuesByBeacon() {

    try {
        const payload = buildRegisterTextareaValuesPayload();
        const blob = new Blob(
            [JSON.stringify(payload)],
            { type: 'application/json' }
        );

        navigator.sendBeacon(
            wiseCoreRegisterTextareaValuesUrl,
            blob
        );

    } catch (e) {
        console.error('registerTextareaValuesByBeacon error:', e);
    }
}

async function createLabelList(){

	elm_targetUl = document.getElementById('wisePanelWhiteboardUiLabelListList');
	elm_targetUl.replaceChildren();
	
    try {
	
		const payload = {
			send_japanese_id: contextMenuTargetJapaneseId,
			send_japanese_element_id: contextMenuTargetJapaneseElementId,
			send_sub_classification_id: contextMenuTargetSubClassificationId,
			send_form_id: contextMenuTargetFormId,
			send_voice_id: contextMenuTargetVoiceId,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
            wiseCoreGenerateLabelDataUrl,
            payload,
            10000
        );

        const data = (result && result.data !== undefined) ? result.data : result;

        if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
            return;
        }

        for (let i = INDEX_FIRST; i < data.length; i++) {

            const int_sub_classification_id = escapeNumber(data[i].subClassificationId);
            const int_label_id = escapeNumber(data[i].id);
            const str_japanese = escapeHTML(data[i].japanese);

            const elm_addLi = document.createElement('li');
            elm_addLi.classList.add('whiteboardUiLabelListLi', 'wiseUiFontSizeTarget');
            elm_addLi.dataset.subClassificationId = int_sub_classification_id;
            elm_addLi.dataset.labelId = int_label_id;
            elm_addLi.textContent = str_japanese;

            elm_targetUl.appendChild(elm_addLi);
        }

        applyFontSizeVariation(
            ['wiseUiFontSizeTarget'],
            'wiseUiFontSizeTargetVariationDifference'
        );

    } catch (e) {

        if (e && typeof e.message === 'string' && e.message.includes('timeout')) {
            console.error('タイムアウトが発生しました。');
            return;
        }

        alert(e.message || 'Error');
        return;
    }
}

function alignElements(elm_movableContainer){

    let arr_movableContainers = [];

    arr_phraseClauseContainer_info_collection = getPhraseClauseContainerInfoCollection(elm_movableContainer, true);
    arr_movableContainers = arr_phraseClauseContainer_info_collection['arr_movableContainers'];

    let int_depth = 0,
        int_weight_parent = 0,
        arr_balance = [];

    let int_movableContainer_max_height = getMaxMovableContainerHeight(),
        int_movableContainer_max_width;

    int_movableContainer_max_height = int_movableContainer_max_height + 10;

    let { int_weight: int_weight_add, arr_balance: updated_arr_balance } =
        calculateVerticalBalanceRecursively(arr_movableContainers, arr_balance, int_movableContainer_max_height, int_depth, int_weight_parent);

    arr_balance = updated_arr_balance;

    if(arr_balance.length <= 1) return;
    arr_balance = arr_balance.reverse();

    // ★ボード幅を使う
    const boardWidth = wisePanelWhiteboardViewMainContentArea.getBoundingClientRect().width;

    // ★スクロール分（実際のスクロール要素に合わせてください）
    const scrollElm = wisePanelWhiteboardBody;
    const scrollLeft = scrollElm ? scrollElm.scrollLeft : 0;
    const scrollTop  = scrollElm ? scrollElm.scrollTop  : 0;

    let int_balance_x_interval = 0,
        int_balance_x_total = scrollLeft + balanceMarginLeft,
        elm_movableContainer_align_target;

    let int_balance_x_interval_maxsize = (boardWidth - balanceMarginLeft - 100) / (arr_balance.length - 1);

    for(let i = INDEX_FIRST; i < arr_balance.length; i++){

        int_movableContainer_max_width = 0;

        for(let j = INDEX_FIRST; j < arr_balance[i].length; j++){
            elm_movableContainer_align_target = document.getElementById(arr_balance[i][j]['idName']);
            if(int_movableContainer_max_width < elm_movableContainer_align_target.offsetWidth){
                int_movableContainer_max_width = elm_movableContainer_align_target.offsetWidth;
            }
        }

        for(let j = INDEX_FIRST; j < arr_balance[i].length; j++){
            elm_movableContainer_align_target = document.getElementById(arr_balance[i][j]['idName']);

            // ★topも「見えている領域」を基準にする
            elm_movableContainer_align_target.style.top = (escapeNumber(arr_balance[i][j]['top']) + scrollTop) + 'px';
            elm_movableContainer_align_target.style.left = int_balance_x_total + 'px';
        }

        int_balance_x_interval = int_movableContainer_max_width + 30;
        if(int_balance_x_interval_maxsize < int_balance_x_interval){
            int_balance_x_interval = int_balance_x_interval_maxsize;
        }
        int_balance_x_total = int_balance_x_total + int_balance_x_interval;
    }
}

function getMaxMovableContainerHeight(){

	let elms = document.querySelectorAll('.movableContainer')
	int_movableContainer_max_width = 0,
	int_movableContainer_max_height = 0;

	for(let i = INDEX_FIRST; i < elms.length; i++){
		const elm = elms[i];
		if(int_movableContainer_max_height<elm.offsetHeight){
			int_movableContainer_max_height=elm.offsetHeight;
		}
	}
	return int_movableContainer_max_height;
};

function resetToSelectMode(){
	currentWiseToolbarButton = 'wiseVerticalToolbarSelectorButton';
	currentInteractionMode = 'selectMode';
	changeWiseVerticalToolbarButton('');
}


function lockPageScroll() {

    const de = document.documentElement;
    const body = document.body;

    de.classList.add('wise-lock-scroll');
    body.classList.add('wise-lock-scroll');
}






function initWiseLayout(isOnload) {

    if (sectionWise === null) return;

    if (wiseToolbarContainer !== null) {
        resetToSelectMode();
        resizeWiseVerticalToolbars(sectionWise);
    }

    setWiseLessonContentsItems();
    setWiseLayoutPosition(sectionWise);
    resizeGlobalCanvas();

    if (wiseBannerAdContainer !== null) {
        isActioned = true;
        adjustBannerAdSize();
        if (isOnload) sendBannerAdActivity();
    }

    if (isOnload) {
		disableMobileGestures();
		hideMobileNavButtonOnLoad();
	    applyFullscreenRequirement();
        applyWiseCanvasLayout();
        registerTextareaUnloadHandler();
        saveState(STATE_TITLE_ONLOAD[intSelectedLanguage]);
        applyFontSizeVariation(
            ['wiseUiFontSizeTarget'],
            'wiseUiFontSizeTargetVariationDifference'
        );
    } else {
        saveState(STATE_TITLE_RESIZE[intSelectedLanguage]);
        undo();
    }

    finalizeLayout();
    setZoomScale(getWiseZoomScale());
}

function applyFullscreenRequirement(targetSelector = '.wise-require-fullscreen') {
    const targets = document.querySelectorAll(targetSelector);

    targets.forEach(el => {
        let parent = el.parentElement;

        while (parent && parent !== document.body && parent !== document.documentElement) {
            parent.classList.add('wise-fullscreen-parent');
            parent = parent.parentElement;
        }

        el.classList.add('wise-fullscreen-child');
    });
}

function setZoomScale(newScale) {

    newScale = Number(newScale);

    if (!Number.isFinite(newScale)) {
        return;
    }

    if (newScale < zoomScaleMin) {
        newScale = zoomScaleMin;
    }

    if (newScale > zoomScaleMax) {
        newScale = zoomScaleMax;
    }

    whiteboardState.zoomScale = newScale;

    redrawWhiteboardByZoom();
}

function redrawWhiteboardByZoom() {

    applyZoomToWhiteboard();
    redrawLinkLines();
}

function applyZoomToWhiteboard() {

    if (!wiseZoomStage) {
        return;
    }

    const zoomScale = getWiseZoomScale();

    wiseZoomStage.style.transform =
        'scale(' + zoomScale + ')';

    wiseZoomStage.style.transformOrigin =
        'top left';
}

function getWiseZoomScale() {

    const zoomScale = whiteboardState?.zoomScale || 1;

    if (!Number.isFinite(zoomScale) || zoomScale <= 0) {
        return 1;
    }

    return zoomScale;
}

function zoomInWhiteboard() {
    setZoomScale(getWiseZoomScale() + 0.1);
}

function zoomOutWhiteboard() {
    setZoomScale(getWiseZoomScale() - 0.1);
}

function resetZoomWhiteboard() {
    setZoomScale(zoomScaleDefault);
}