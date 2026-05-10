





async function purgePhraseClauseContainer(elm_movableContainer){

	let arr_movableContainers = [];

	arr_phraseClauseContainer_info_collection = getPhraseClauseContainerInfoCollection(elm_movableContainer, false);
	arr_movableContainers = arr_phraseClauseContainer_info_collection['arr_movableContainers'];

	elm_movableContainer.classList.remove('phraseClauseContainer');
	delete elm_movableContainer.dataset.japanesePhraseClause;
	delete elm_movableContainer.dataset.kanaPhraseClause;

	let uniqueKey_targetContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey),
		idName_targetContainer = 'movableContainer'+uniqueKey_targetContainer,
		idName_targetBaseContainer = 'baseContainer'+uniqueKey_targetContainer,
		idName_targetInnerContainer = 'innerContainer'+uniqueKey_targetContainer,
		elm_targetBaseContainer = document.getElementById(idName_targetBaseContainer),
		elm_targetInnerContainer = document.getElementById(idName_targetInnerContainer),
		send_japanese_id = escapeNumber(elm_movableContainer.dataset.japaneseId),
		send_japanese_element_id = escapeNumber(elm_movableContainer.dataset.japaneseElementId),
		send_sub_classification_id = escapeNumber(elm_movableContainer.dataset.subClassificationId),
		send_form_id = escapeNumber(elm_movableContainer.dataset.formId),
		send_label_id = escapeNumber(elm_movableContainer.dataset.labelId),
		send_voice_id = escapeNumber(elm_movableContainer.dataset.voiceId),
		send_sub_classification = escapeHTML(elm_movableContainer.dataset.subClassification);

	contextMenuTargetContainer = elm_movableContainer;
	contextMenuTargetBaseContainer = elm_targetBaseContainer;
	contextMenuTargetInnerContainer = elm_targetInnerContainer;
	contextMenuTargetJapaneseId = send_japanese_id;
	contextMenuTargetJapaneseElementId = send_japanese_element_id;
	contextMenuTargetSubClassificationId = send_sub_classification_id;
	contextMenuTargetFormId = send_form_id;
	contextMenuTargetLabelId = send_label_id;
	contextMenuTargetVoiceId = send_voice_id;
	contextMenuTargetSubClassification = send_sub_classification;
	contextMenuTargetContainerIdName = idName_targetContainer;

	const inflection = await getInflection();
	if (!inflection) return;
	applyInflectionToWordContainer(inflection);

	let uniqueKey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey),
		elm_innerContainer = document.getElementById('innerContainer'+uniqueKey_movableContainer),
		str_japanese = escapeHTML(elm_movableContainer.dataset.japanese);

	elm_innerContainer.textContent = str_japanese;

	if('childElms' in arr_movableContainers){
		reviewPhraseClauseMaterialsRecursively(elm_movableContainer, arr_movableContainers);
	}
	return;
}



function reviewPhraseClauseMaterialsRecursively(elm_movableContainer_parentPhraseClause, arr_movableContainers){

	let idName_movableContainer = arr_movableContainers['idName'],
		elm_movableContainer = document.getElementById(idName_movableContainer),
		childElms,
		childElms_current;

	if(elm_movableContainer_parentPhraseClause !== elm_movableContainer){

		elm_movableContainer.classList.remove('itemOfPhraseClauseContainer');
		elm_movableContainer.classList.remove('itemOfPhraseClauseContainerTopElement');
		delete elm_movableContainer.dataset.phraseClauseId;

		let uniqueKey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey),
			elms_canvas = document.querySelectorAll('[data-left-link-id="'+uniqueKey_movableContainer+'"]'),
			elm_canvas;

		if(elms_canvas.length !== LENGTH_EMPTY){
			for(let i = INDEX_FIRST; i < elms_canvas.length; i++){
				elm_canvas = elms_canvas[i];
				elm_canvas.classList.remove('itemOfPhraseClauseContainer');
			}
		}
	}

	if('childElms' in arr_movableContainers){
		childElms = arr_movableContainers['childElms'];
		for(let i = INDEX_FIRST; i < childElms.length; i++){
			childElms_current = childElms[i];
			reviewPhraseClauseMaterialsRecursively(elm_movableContainer_parentPhraseClause, childElms_current);
		}
	}
	return;
}














function detectLinkContainerCollision(clientPoint) {

	const x = clientPoint.x;
	const y = clientPoint.y;

	const elms_linkContainer = document.querySelectorAll('.linkContainer');

	for (let i = INDEX_FIRST; i < elms_linkContainer.length; i++) {

		const elm_linkContainer = elms_linkContainer[i];

		if (!elm_linkContainer || elm_linkContainer === linkedMovableContainer) {
			continue;
		}

		const uniqueKey = escapeNumber(elm_linkContainer.dataset.uniqueKey);

		if (uniqueKey === drawLineStartKey) {
			continue;
		}

		if (linkMarkerSide === 'leftLinkMarker' && elm_linkContainer.dataset.linkMarkerType !== 'rightLinkMarker') {
			continue;
		}

		if (linkMarkerSide !== 'leftLinkMarker' && elm_linkContainer.dataset.linkMarkerType !== 'leftLinkMarker') {
			continue;
		}

		const rect = elm_linkContainer.getBoundingClientRect();

		const isHit =
			rect.left <= x &&
			x <= rect.right &&
			rect.top <= y &&
			y <= rect.bottom;

		if (isHit) {
			return uniqueKey;
		}
	}

	return '';
}

async function getWordContainerData(payload = null) {

    const finalPayload = payload ?? {
        send_japanese_id: contextMenuTargetJapaneseId,
        send_japanese_element_id: contextMenuTargetJapaneseElementId,
        send_sub_classification_id: contextMenuTargetSubClassificationId,
        send_form_id: contextMenuTargetFormId,
        send_label_id: contextMenuTargetLabelId,
        send_voice_id: contextMenuTargetVoiceId,
        str_japanese: '',
        str_kana: '',
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        wiseCoreGenerateWordContainerDataUrl,
        finalPayload,
        10000
    );

    return Array.isArray(result.data) ? result.data : [];
}

async function getInflection(payload = null) {
    const arr = await getWordContainerData(payload);
    return arr[INDEX_FIRST] ?? null;
}


function applyInflectionToWordContainer(inflection){

	let int_japanese_id = escapeNumber(inflection.japaneseId),
		int_japanese_element_id = escapeNumber(inflection.japaneseElementId),
		int_sub_classification_id = escapeNumber(inflection.subClassificationId),
		int_form_id = escapeNumber(inflection.formId),
		int_label_id = escapeNumber(inflection.labelId),
		int_voice_id = escapeNumber(inflection.voiceId),
		str_japanese = escapeHTML(inflection.japanese),
		str_kana = escapeHTML(inflection.kana);

	if(str_japanese.length === LENGTH_EMPTY){
		return;
	}

	politePlainFormHistory[int_japanese_element_id] = {
		int_label_id : int_label_id,
		str_japanese : str_japanese
	};

	maxZIndex += 1;

	contextMenuTargetContainer.dataset.japaneseId = int_japanese_id;
	contextMenuTargetContainer.dataset.japaneseElementId = int_japanese_element_id;
	contextMenuTargetContainer.dataset.subClassificationId = int_sub_classification_id;
	contextMenuTargetContainer.dataset.formId = int_form_id;
	contextMenuTargetContainer.dataset.labelId = int_label_id;
	contextMenuTargetContainer.dataset.voiceId = int_voice_id;
	contextMenuTargetContainer.dataset.japanese = str_japanese;
	contextMenuTargetContainer.dataset.kana = str_kana;

	if(contextMenuTargetFormId >= FORM_VOICE_POTENTIAL_VERB){
		contextMenuTargetContainer.dataset.subClassification = escapeHTML(inflection.form);
		contextMenuTargetSubClassification = escapeHTML(inflection.form);
	}

	contextMenuTargetJapaneseId = int_japanese_id;
	contextMenuTargetJapaneseElementId = int_japanese_element_id;
	contextMenuTargetSubClassificationId = int_sub_classification_id;
	contextMenuTargetFormId = int_form_id;
	contextMenuTargetLabelId = int_label_id;
	contextMenuTargetVoiceId = int_voice_id;

	if('japanesePhraseClause' in contextMenuTargetContainer.dataset){
		let str_japanesePhraseClause = escapeHTML(contextMenuTargetContainer.dataset.japanesePhraseClause);
		contextMenuTargetInnerContainer.textContent = str_japanesePhraseClause+str_japanese;
	}
	else{
		contextMenuTargetInnerContainer.textContent = str_japanese;

	}

	updateLinkContainerWidths();
	viewWordInformation(contextMenuTargetContainer);

	return;
}






function buildSentenceLinkInfoCollection(elms_movableContainer){

	let elm_movableContainer,
	bounds_movableContainer,
	idName_movableContainer,
	uniquekey_movableContainer,
	uniquekey_movableContainer_sentence_end,
	counter_sentence_end = COUNT_FIRST,
	int_japanese_id,
	int_sub_classification_id,
	int_form_id,
	int_link_id,
	int_link_type,
	str_japanese,
	str_kana,
	str_sub_classification,
	str_form,
	str_voice,
	str_phraseClauseType,
	int_phraseClauseId,
	str_japanesePhraseClause,
	str_kanaPhraseClause,
	isPermitted,
	doesNotHaveWordContainerClass = false,
	arr_link_id = [],
	arr_link_info_collection = [];

	for(let i = INDEX_FIRST; i < elms_movableContainer.length; i++){
		elm_movableContainer = elms_movableContainer[i];

		if(elm_movableContainer.classList.contains('itemOfPhraseClauseContainer')){
			elm_movableContainer.classList.remove('itemOfPhraseClauseContainer');
			bounds_movableContainer = elm_movableContainer.getBoundingClientRect();
			elm_movableContainer.classList.add('itemOfPhraseClauseContainer');
		}
		else{
			bounds_movableContainer = elm_movableContainer.getBoundingClientRect();
		}

		if(!elm_movableContainer.classList.contains('wordContainer')){
			doesNotHaveWordContainerClass = true;
		}

		idName_movableContainer = escapeHTML(elm_movableContainer.id);
		// 未定義id null変更
		uniquekey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey) || 0;
		int_japanese_id = escapeNumber(elm_movableContainer.dataset.japaneseId) || DEFAULT_JAPANESE_ID;
		int_japanese_element_id = escapeNumber(elm_movableContainer.dataset.japaneseElementId) || DEFAULT_JAPANESE_ELEMENT_ID;
		int_sub_classification_id = escapeNumber(elm_movableContainer.dataset.subClassificationId) || DEFAULT_SUB_CLASSIFICATION_ID;
		int_form_id = escapeNumber(elm_movableContainer.dataset.formId) || DEFAULT_FORM_ID;
		int_label_id = escapeNumber(elm_movableContainer.dataset.labelId) || DEFAULT_LABEL_ID;
		int_voice_id = escapeNumber(elm_movableContainer.dataset.voiceId) || DEFAULT_VOICE_ID;
		str_japanese = escapeHTML(elm_movableContainer.dataset.japanese) || DEFAULT_JAPANESE_TEXT;
		str_kana = escapeHTML(elm_movableContainer.dataset.kana) || str_japanese;
		str_subClassification = escapeHTML(elm_movableContainer.dataset.subClassification) || DEFAULT_SUB_CLASSIFICATION_TEXT;
		str_form = escapeHTML(elm_movableContainer.dataset.form) || DEFAULT_FORM_TEXT;
		str_voice = escapeHTML(elm_movableContainer.dataset.voice) || DEFAULT_VOICE_TEXT;

		if(elm_movableContainer.hasAttribute('data-link-id')){
			int_link_id = escapeNumber(elm_movableContainer.dataset.linkId);
			int_link_type = escapeNumber(elm_movableContainer.dataset.linkType);
		}
		else{
			int_link_id = SENTENCE_END;
			int_link_type = LINK_TYPE_NORMAL;
			uniquekey_movableContainer_sentence_end = uniquekey_movableContainer;
			++counter_sentence_end;
		}

		if(elm_movableContainer.classList.contains('phraseClauseContainer')){
			str_phraseClauseType = 'phraseClauseContainer';
			int_phraseClauseId = PHRASE_CLAUSE_ID_NONE;
			str_japanesePhraseClause = escapeHTML(elm_movableContainer.dataset.japanesePhraseClause);
			str_kanaPhraseClause = escapeHTML(elm_movableContainer.dataset.kanaPhraseClause);
		}
		else if(elm_movableContainer.classList.contains('itemOfPhraseClauseContainerTopElement')){
			str_phraseClauseType = 'itemOfPhraseClauseContainerTopElement';
			int_phraseClauseId = escapeNumber(elm_movableContainer.dataset.phraseClauseId);
			str_japanesePhraseClause = STRING_NONE;
			str_kanaPhraseClause = STRING_NONE;
		}
		else if(elm_movableContainer.classList.contains('itemOfPhraseClauseContainer')){
			str_phraseClauseType = 'itemOfPhraseClauseContainer';
			int_phraseClauseId = escapeNumber(elm_movableContainer.dataset.phraseClauseId);
			str_japanesePhraseClause = STRING_NONE;
			str_kanaPhraseClause = STRING_NONE;
		}
		else{
			str_phraseClauseType = 'movableContainer';
			int_phraseClauseId = PHRASE_CLAUSE_ID_NONE;
			str_japanesePhraseClause = STRING_NONE;
			str_kanaPhraseClause = STRING_NONE;
		}

		arr_link_id.push({
			idName : idName_movableContainer,
			uniqueKey : uniquekey_movableContainer,
			japaneseId : int_japanese_id,
			japaneseElementId : int_japanese_element_id,
			subClassificationId : int_sub_classification_id,
			formId : int_form_id,
			labelId : int_label_id,
			voiceId : int_voice_id,
			japanese : str_japanese,
			kana : str_kana,
			subClassification : str_subClassification,
			form : str_form,
			voice : str_voice,
			phraseClauseType : str_phraseClauseType,
			phraseClauseId : int_phraseClauseId,
			japanesePhraseClause : str_japanesePhraseClause,
			kanaPhraseClause : str_kanaPhraseClause,
			boundsTop : bounds_movableContainer.top,
			boundsLeft : bounds_movableContainer.left,
			linkId : int_link_id,
			linkType : int_link_type
		});
	}

	if(counter_sentence_end !== PERMIT_SENTENCE_END_LENGTH){
		arr_link_info_collection = {
			isPermitted : isPermitted = false,
			doesNotHaveWordContainerClass : doesNotHaveWordContainerClass,
			uniquekey_movableContainer_sentence_end : uniquekey_movableContainer_sentence_end,
			arr_link_id : arr_link_id
		}
		return arr_link_info_collection;
	}

	arr_link_info_collection = {
		isPermitted : isPermitted = true,
		doesNotHaveWordContainerClass : doesNotHaveWordContainerClass,
		uniquekey_movableContainer_sentence_end : uniquekey_movableContainer_sentence_end,
		arr_link_id : arr_link_id
	}
	return arr_link_info_collection;
}
