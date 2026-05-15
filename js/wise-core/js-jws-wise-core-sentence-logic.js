
async function registerSentence(){

	let arr_group_of_words = buildSentenceWordGroup();

	if(arr_group_of_words.length === LENGTH_EMPTY)return;

	let arr_link_id_add_sort = arr_group_of_words['arr_link_id_add_sort'];

	arr_link_id_add_sort.sort((a, b) => a.uniqueKey - b.uniqueKey);

	let arr_link_id_add_sort_uniqueKey;

	for(let i = INDEX_FIRST; i < arr_link_id_add_sort.length; i++){
		arr_link_id_add_sort_uniqueKey = arr_link_id_add_sort[i].uniqueKey;
		if(i !== arr_link_id_add_sort_uniqueKey){
			arr_link_id_add_sort[i].uniqueKey = i;
			arr_link_id_add_sort[i].idName = 'movableContainer'+i;
			for(let j = INDEX_FIRST; j < arr_link_id_add_sort.length; j++){
				if(arr_link_id_add_sort[j].linkId == arr_link_id_add_sort_uniqueKey){
					arr_link_id_add_sort[j].linkId = i;
				}
				if(arr_link_id_add_sort[j].phraseClauseId == arr_link_id_add_sort_uniqueKey){
					arr_link_id_add_sort[j].phraseClauseId = i;
				}
			}
		}
	}

	const url = new URL(window.location.href);
	const params = url.searchParams;
	const send_grammar_unique_code = escapeHTML(
		params.get(KEY_GRAMMAR_UNIQUE_CODE)
	);

	try {
	
		const payload = {
			arr_link_id_add_sort: arr_link_id_add_sort,
			send_grammar_unique_code: send_grammar_unique_code,
			str_group_of_words_japanese: escapeHTML(arr_group_of_words['str_group_of_words_japanese']),
			str_group_of_words_kana: escapeHTML(arr_group_of_words['str_group_of_words_kana']),
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceRegisterSentenceUrl,
			payload,
			10000
		);

		alert(escapeHTML(arr_group_of_words['str_group_of_words_japanese']));

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}
}

async function reviewLayers(targetBody) {

    let url = new URL(window.location.href),
        params = url.searchParams,
        send_sentence_unique_code = escapeHTML(params.get(KEY_SENTENCE_UNIQUE_CODE) || '');

	sentenceLayersMenuList.innerHTML = '';


    let arr_layers = [];

    try {
        arr_layers = await getLayersByRegisteredSentence(send_sentence_unique_code, false);
    } catch (error) {
        console.error('Error:', error.message || error);
        alert(error.message || 'Error');
        return;
    }

    if (!Array.isArray(arr_layers) || arr_layers.length === LENGTH_EMPTY) {
        return;
    }

    if (targetBody !== createLayersBody) {
        let newElement = { id: 0, layer_name: 'Default', sort: 0 };
        arr_layers.unshift(newElement);
    }

    for (let i = INDEX_FIRST; i < arr_layers.length; i++) {

        const int_layer_id = escapeNumber(arr_layers[i]['id']);
        const str_layer_name = escapeHTML(arr_layers[i]['layer_name']);
        const str_button_text = escapeHTML(arr_layers[i]['subCategory']);

        let elm_sentenceLayerNamesContainer = document.createElement('div');
        elm_sentenceLayerNamesContainer.classList.add('sentenceLayerNamesContainer');

        let elm_sentenceLayerNamesSpan = document.createElement('span');
        elm_sentenceLayerNamesSpan.classList.add('sentenceLayerNamesSpan');
        elm_sentenceLayerNamesSpan.textContent = str_layer_name;

        elm_sentenceLayerNamesContainer.appendChild(elm_sentenceLayerNamesSpan);

        let elm_li = document.createElement('li');
        elm_li.dataset.layerId = int_layer_id;

        elm_li.classList.add('sentenceLayerMenuLi');

        let elm_layerEditStartButton = document.createElement('button');
        elm_layerEditStartButton.classList.add('sentenceLayerEditStartButton', 'sentenceLayerEditButton');
        // マジックナンバー
        elm_layerEditStartButton.textContent = '編集開始';

        let elm_layerEditFinishButton = document.createElement('button');
        elm_layerEditFinishButton.classList.add('sentenceLayerEditFinishButton', 'sentenceLayerEditButton', 'hidden');
        // マジックナンバー
        elm_layerEditFinishButton.textContent = '編集終了';

        let elm_layerEditElementsInfoButton = document.createElement('button');
        elm_layerEditElementsInfoButton.classList.add('sentenceLayerEditElementsInfoButton');
        // マジックナンバー
        elm_layerEditElementsInfoButton.textContent = 'Elms Info';
        elm_layerEditElementsInfoButton.addEventListener('pointerup', function () {
            if (isEditingLayer) { return; }
            displayLayerElementsInformation(int_layer_id);
        }, false);

        let elm_layerEditLayerInfoButton = document.createElement('button');
        elm_layerEditLayerInfoButton.classList.add('sentenceLayerEditLayerInfoButton');
        // マジックナンバー
        elm_layerEditLayerInfoButton.textContent = 'Layer Info';
        elm_layerEditLayerInfoButton.addEventListener('pointerup', function () {
            if (isEditingLayer) { return; }
            displayLayerInformation(int_layer_id);
        }, false);

        let elm_sentenceLayerDeleteLayerButton = document.createElement('button');
        elm_sentenceLayerDeleteLayerButton.classList.add('sentenceLayerDeleteLayerButton');
        // マジックナンバー
        elm_sentenceLayerDeleteLayerButton.textContent = 'Del';
        elm_sentenceLayerDeleteLayerButton.addEventListener('pointerup', function () {
            if (isEditingLayer) { return; }
            deleteLayer(int_layer_id);
        }, false);

        let elm_editButtonsContainer = document.createElement('div');
        elm_editButtonsContainer.classList.add('editButtonsContainer');

        elm_editButtonsContainer.appendChild(elm_layerEditStartButton);
        elm_editButtonsContainer.appendChild(elm_layerEditFinishButton);
        elm_editButtonsContainer.appendChild(elm_layerEditElementsInfoButton);
        elm_editButtonsContainer.appendChild(elm_layerEditLayerInfoButton);
        elm_editButtonsContainer.appendChild(elm_sentenceLayerDeleteLayerButton);

        let elm_sentenceLayerSortButtonsContainer = document.createElement('div');
        elm_sentenceLayerSortButtonsContainer.classList.add('sentenceLayerSortButtonsContainer');

        let elm_sentenceLayerSortPreviousButton = document.createElement('button');
        elm_sentenceLayerSortPreviousButton.classList.add('sentenceLayerSortPreviousButton', 'sentenceLayerSortButton');
        // マジックナンバー
        elm_sentenceLayerSortPreviousButton.textContent = '△';

        elm_sentenceLayerSortPreviousButton.addEventListener('pointerup', function () {
            if (isEditingLayer) { return; }
            resortLayers(true, int_layer_id, send_sentence_unique_code);
        }, false);

        let elm_sentenceLayerSortNextButton = document.createElement('button');
        elm_sentenceLayerSortNextButton.classList.add('sentenceLayerSortNextButton', 'sentenceLayerSortButton');
        // マジックナンバー
        elm_sentenceLayerSortNextButton.textContent = '▽';

        elm_sentenceLayerSortNextButton.addEventListener('pointerup', function () {
            if (isEditingLayer) { return; }
            resortLayers(false, int_layer_id, send_sentence_unique_code);
        }, false);

        elm_sentenceLayerSortButtonsContainer.appendChild(elm_sentenceLayerSortPreviousButton);
        elm_sentenceLayerSortButtonsContainer.appendChild(elm_sentenceLayerSortNextButton);
        elm_editButtonsContainer.appendChild(elm_sentenceLayerSortButtonsContainer);

        const targets = [
            elm_layerEditStartButton,
            elm_layerEditFinishButton,
            elm_layerEditElementsInfoButton,
            elm_layerEditLayerInfoButton,
            elm_sentenceLayerDeleteLayerButton,
            elm_sentenceLayerSortPreviousButton,
            elm_sentenceLayerSortNextButton
        ];

        elm_layerEditStartButton.addEventListener('pointerup', function () {
            startEditLayer(elm_li, elm_layerEditFinishButton, targets, int_layer_id);
        }, false);
        elm_layerEditFinishButton.addEventListener('pointerup', function () {
            finishEditLayer(elm_li, elm_layerEditFinishButton, targets, int_layer_id);
        }, false);

        elm_li.appendChild(elm_editButtonsContainer);
        elm_li.appendChild(elm_sentenceLayerNamesContainer);

        sentenceLayersMenuList.appendChild(elm_li);
    }
}

async function startEditLayer(elm_li, target, targets, int_layer_id) {
    if (isEditingLayer) {
        return;
    }
    isEditingLayer = true;

    const allLis = document.querySelectorAll('.sentenceLayerMenuLi');
    allLis.forEach(li => {
        if (li === elm_li) return;
        if (!li.classList.contains('sentenceLayerMenuLocked')) {
            li.classList.add('sentenceLayerMenuLocked');
        }
    });

    updateHiddenStates(target, targets, true);

    try {
        const arr_layer_elements = await getLayerElements(int_layer_id);

        let arr_sentenceElementId = [];

        if (Array.isArray(arr_layer_elements) && arr_layer_elements.length !== LENGTH_EMPTY) {
            const oneColumnArray = arr_layer_elements.map(item => item.sentenceElementId);
            arr_sentenceElementId = oneColumnArray.map(elm => parseInt(elm, 10));
        }

        const elms_clickableContainer = document.querySelectorAll('.clickableContainer');

        for (let i = INDEX_FIRST; i < elms_clickableContainer.length; i++) {
            const elm_clickableContainer = elms_clickableContainer[i];

            elm_clickableContainer.classList.add('isEditingLayerClickableContainer');

            if (arr_sentenceElementId.length > LENGTH_EMPTY) {
                if (elm_clickableContainer.hasAttribute('data-sentence-element-id')) {
                    const dataSentenceElementId = elm_clickableContainer.getAttribute('data-sentence-element-id');
                    if (arr_sentenceElementId.includes(parseInt(dataSentenceElementId, 10))) {
                        elm_clickableContainer.classList.add('selected');
                    }
                }
            }
        }

    } catch (error) {
        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            console.error('Error:', error.message || error);
            alert(error.message || 'Error');
        }

        // 失敗時に編集状態を解除するなら（任意）
        // isEditingLayer = false;
        // updateHiddenStates(target, targets, false);
        // allLis.forEach(li => li.classList.remove('sentenceLayerMenuLocked'));
    }
}

function updateHiddenStates(target, targets, isEditing) {
    targets.forEach(function (el) {
        if (!el) return;
        if (el === target) {
            el.classList.toggle('hidden', !isEditing);
        } else {
            el.classList.toggle('hidden', isEditing);
        }
    });
}

async function getLayersByRegisteredSentence(searchCriteria, searchById) {

    const payload = {
        searchCriteria: searchCriteria,
        searchById: searchById ? FLAG_TRUE : FLAG_FALSE,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        sentenceLayerGetLayersByRegisteredSentenceUrl,
        payload,
        10000
    );

    return Array.isArray(result.data) ? result.data : [];
}

async function finishEditLayer(elm_li, target, targets, int_layer_id){
	if(!isEditingLayer){
		return;
	}
	isEditingLayer = false;

	const allLis = document.querySelectorAll('.sentenceLayerMenuLi');
	allLis.forEach(li => {
		if (li === elm_li) return;
		if (li.classList.contains('sentenceLayerMenuLocked')) {
			li.classList.remove('sentenceLayerMenuLocked');
		}
	});
	
	updateHiddenStates(target, targets, false);

	let arr_selected = [];

	let elms_clickableContainer = document.querySelectorAll('.clickableContainer'),
		elm_clickableContainer;

	for(let i = INDEX_FIRST; i < elms_clickableContainer.length; i++){
		elm_clickableContainer = elms_clickableContainer[i];
		elm_clickableContainer.classList.remove('isEditingLayerClickableContainer');

		let int_sentence_element_id = escapeNumber(elm_clickableContainer.dataset.sentenceElementId)

		if(elm_clickableContainer.classList.contains('selected')){
			arr_selected.push({
			sentenceElementId : int_sentence_element_id,
				selected : FLAG_TRUE
			});
			elm_clickableContainer.classList.remove('selected');
		}
		else{
			arr_selected.push({
				sentenceElementId : int_sentence_element_id,
				selected : FLAG_FALSE
			});
		}
	}

	const url = new URL(window.location.href);
	const params = url.searchParams;
	const send_sentence_unique_code = escapeHTML(
		params.get(KEY_SENTENCE_UNIQUE_CODE)
	);

	const searchCriteria = send_sentence_unique_code;
	const searchById = false;
	const searchByIdAsNumber = searchById ? FLAG_TRUE : FLAG_FALSE;
	
	try {
	
		const payload = {
			arr_selected: arr_selected,
			int_layer_id: int_layer_id,
			searchCriteria: searchCriteria,
			searchById: searchByIdAsNumber,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerUpdateLayerElementsUrl,
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


}

async function resortLayers(isPrevious, int_layer_id, send_sentence_unique_code){

	let isPreviousAsNumber = isPrevious ? FLAG_TRUE : FLAG_FALSE;
	
	try {

		const payload = {
			is_previous_as_number: isPreviousAsNumber,
			int_layer_id: int_layer_id,
			send_sentence_unique_code: send_sentence_unique_code,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerResortLayersByRegisteredSentenceUrl,
			payload,
			10000
		);

		await reviewLayers(createLayersBody);

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}

}

async function deleteLayer(int_layer_id) {
    const isConfirmed = window.confirm(MSG_DELETE_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) return;
	
	try {
	
		const payload = {
			int_layer_id: int_layer_id,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceLayerDeleteLayerUrl,
			payload,
			10000
		);

		reviewLayers(createLayersBody);
		return;

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
		return;
	}
}

async function displayLayerElementsInformation(int_layer_id) {
    const elm_targetUl = sentenceLayerElementsDisplayList;
    if (!elm_targetUl) return;

    elm_targetUl.innerHTML = '';

    sentenceLayerUpdateScreen.classList.add('hidden');
    sentenceLayerUpdateSideMenu.classList.add('hidden');
    sentenceLayerUpdateLayerElements.classList.remove('hidden');
    sentenceLayerUpdateOverlay.classList.add('overlay-on');

    try {

        const payload = {
            int_layer_id: int_layer_id,
            int_selected_language: intSelectedLanguage
        };

        const result = await postJson(
            sentenceLayerDisplayElementsInformationUrl,
            payload,
            60000
        );

        const data = result.data;
        const inflection = Array.isArray(result.inflection) ? result.inflection : [];
        const voice = Array.isArray(result.voice) ? result.voice : [];

        if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
            const li = document.createElement('li');
            li.classList.add('resultEmpty');
            li.textContent = '検索結果 0件';
            elm_targetUl.appendChild(li);
            return;
        }

        for (let i = INDEX_FIRST; i < data.length; i++) {
            const int_layer_element_id = escapeNumber(data[i].id);
            const int_formId = parseInt(data[i].formId, 10);
            const int_voiceId = parseInt(data[i].voiceId, 10);
            const is_highlighted = parseInt(data[i].isHighlighted, 10);
            const str_japanese = escapeHTML(data[i].japanese);

            const li = document.createElement('li');
            li.classList.add('sentenceLayerUpdateScreenLayerElementsDisplayAreaLi');
            li.dataset.layerElementId = int_layer_element_id;

            // nameContainer
            const nameContainer = document.createElement('div');
            nameContainer.classList.add('sentenceLayerElementNamesContainer');

            const nameSpan = document.createElement('span');
            nameSpan.classList.add('sentenceLayerElementNamesSpan');
            nameSpan.style.fontSize = '21px';
            nameSpan.textContent = str_japanese;
            nameContainer.appendChild(nameSpan);

            // toggleContainer
            const label = document.createElement('label');
            label.classList.add('sentenceLayerElementToggleLabel');
            label.setAttribute('for', `sentenceLayerElementToggleButton${i}`);

            const chk = document.createElement('input');
            chk.classList.add('sentenceLayerElementToggleButton');
            chk.type = 'checkbox';
            chk.id = `sentenceLayerElementToggleButton${i}`;
            chk.checked = is_highlighted === FLAG_TRUE;

            chk.addEventListener('change', async () => {
                const newValue = chk.checked ? FLAG_TRUE : FLAG_FALSE;

                if (newValue === FLAG_TRUE) {
                    const proceed = await confirmLayerUpdateIfNoOverride(int_layer_id);
                    if (!proceed) {
                        chk.checked = !chk.checked;
                        return;
                    }
                }

                try {
                    await updateLayerElementProperty({
                        target: 'highlight',
                        int_layer_element_id: int_layer_element_id,
                        is_highlighted_new: newValue
                    });
                } catch (error) {
                    console.error('更新エラー:', error.message || error);
                    alert(error.message || '更新に失敗しました');
                    chk.checked = !chk.checked;
                }
            });

            const toggleContainer = document.createElement('div');
            toggleContainer.classList.add('sentenceLayerElementToggleButtonContainer');
            toggleContainer.appendChild(chk);
            toggleContainer.appendChild(label);

            // inputs（Form/Voice）
            const formSelect = document.createElement('select');
            formSelect.classList.add('sentenceLayerElementFormIdSelect');
            formSelect.id = `formIdSelect${i}`;

            inflection.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.id;
                option.textContent = opt.title;
                if (opt.id === int_formId) option.selected = true;
                formSelect.appendChild(option);
            });

            formSelect.dataset.prevValue = String(int_formId);

            formSelect.addEventListener('change', async () => {
                const prev = formSelect.dataset.prevValue;
                const next = formSelect.value;

                try {
                    await updateLayerElementProperty({
                        target: 'formId',
                        int_layer_element_id: int_layer_element_id,
                        formId: next
                    });
                    formSelect.dataset.prevValue = next;
                } catch (error) {
                    console.error('更新エラー:', error.message || error);
                    alert(error.message || '更新に失敗しました');
                    formSelect.value = prev;
                }
            });

            const formLabel = document.createElement('label');
            formLabel.setAttribute('for', `formIdSelect${i}`);
            formLabel.textContent = 'Form: ';
            formLabel.classList.add('sentenceLayerElementInputLabel');

            const formPair = document.createElement('div');
            formPair.classList.add('sentenceLayerElementInputPair');
            formPair.appendChild(formLabel);
            formPair.appendChild(formSelect);

            const voiceSelect = document.createElement('select');
            voiceSelect.classList.add('sentenceLayerElementVoiceIdSelect');
            voiceSelect.id = `voiceIdSelect${i}`;

            voice.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.id;
                option.textContent = opt.title;
                if (opt.id === int_voiceId) option.selected = true;
                voiceSelect.appendChild(option);
            });

            voiceSelect.dataset.prevValue = String(int_voiceId);

            voiceSelect.addEventListener('change', async () => {
                const prev = voiceSelect.dataset.prevValue;
                const next = voiceSelect.value;

                try {
                    await updateLayerElementProperty({
                        target: 'voiceId',
                        int_layer_element_id: int_layer_element_id,
                        voiceId: next
                    });
                    voiceSelect.dataset.prevValue = next;
                } catch (error) {
                    console.error('更新エラー:', error.message || error);
                    alert(error.message || '更新に失敗しました');
                    voiceSelect.value = prev;
                }
            });

            const voiceLabel = document.createElement('label');
            voiceLabel.setAttribute('for', `voiceIdSelect${i}`);
            voiceLabel.textContent = 'Voice: ';
            voiceLabel.classList.add('sentenceLayerElementInputLabel');

            const voicePair = document.createElement('div');
            voicePair.classList.add('sentenceLayerElementInputPair');
            voicePair.appendChild(voiceLabel);
            voicePair.appendChild(voiceSelect);

            const inputs = document.createElement('div');
            inputs.classList.add('sentenceLayerElementInputContainer');
            inputs.appendChild(formPair);
            inputs.appendChild(voicePair);

            // editLayerElementsContainer（toggle + inputs）
            const editLayerElementsContainer = document.createElement('div');
            editLayerElementsContainer.classList.add('sentenceLayerElementEditLayerElementsContainer');
            editLayerElementsContainer.appendChild(toggleContainer);
            editLayerElementsContainer.appendChild(inputs);

            // overrideContainer（ボタン配置）
            const overrideContainer = document.createElement('div');
            overrideContainer.classList.add('overrideContainer');

            const overrideBtn = document.createElement('button');
            overrideBtn.type = 'button';
            overrideBtn.classList.add('openOverrideEditorButton');
            overrideBtn.textContent = 'Override…';
            overrideBtn.addEventListener('pointerup', async () => {
                await showOverrideElements(int_layer_element_id);
                overrideOverlay.classList.add('overlay-on');
            });
            overrideContainer.appendChild(overrideBtn);

            // editContainer（editLayerElementsContainer ＋ overrideContainer）
            const editContainer = document.createElement('div');
            editContainer.classList.add('sentenceLayerElementEditContainer');
            editContainer.appendChild(editLayerElementsContainer);
            editContainer.appendChild(overrideContainer);

            // 最終構造：nameContainer → editContainer
            li.appendChild(nameContainer);
            li.appendChild(editContainer);

            elm_targetUl.appendChild(li);
        }

    } catch (error) {
        console.error('データ取得エラー:', error.message || error);
        alert(error.message || 'データ取得中にエラーが発生しました');
    }
}

async function confirmLayerUpdateIfNoOverride(int_layer_id, timeoutMs = 60000) {

    try {

		const payload = {
            int_layer_id: int_layer_id
        };

        const result = await postJson(
            sentenceLayerEnsureHasOverrideUrl,
            payload,
            timeoutMs
        );
        const hasOverride = parseHasOverrideResult(result.data);
        if (hasOverride) {
            return true;
        }
        return window.confirm('この要素には t_override のレコードがありません。更新してもよろしいですか？');
    } catch (error) {
        console.error('確認エラー:', error.message || error);
        alert(error.message || '更新前確認に失敗しました');
        return false;
    }
}

async function displayLayerInformation(int_layer_id) {

    usedGrammarSelectedGrammarList.innerHTML = '';
    usedGrammarSelectedParticleList.innerHTML = '';
    usedGrammarUnselectedGrammarList.innerHTML = '';
    usedGrammarUnselectedParticleList.innerHTML = '';
    usedGrammarPredicateList.innerHTML = '';
    usedGrammarParticlesList.innerHTML = '';
    usedGrammarInflectionList.innerHTML = '';
    usedGrammarSpecialTermsList.innerHTML = '';

    sentenceLayerUpdateScreen.classList.remove('hidden');
    sentenceLayerUpdateSideMenu.classList.remove('hidden');
    sentenceLayerUpdateLayerElements.classList.add('hidden');
    sentenceLayerUpdateOverlay.classList.add('overlay-on');

    try {
        const arr_layer_information = await getLayerInformation(int_layer_id);
		const gId = arr_layer_information[INDEX_FIRST]['grammarId'];
		grammarIdInput.value = (gId === null ? '' : String(Number(gId)));
        sentenceLayerNameInput.value = escapeHTML(arr_layer_information[INDEX_FIRST]['layerName']);
        sentenceLayerNameUpdateButton.dataset.layerId = escapeNumber(int_layer_id);
        sentenceLayerUpdateSubmitButton.dataset.layerId = escapeNumber(int_layer_id);
        displayLayerUpdateScreenGrammarJapaneseContent();
    } catch (e) {
        console.error('getLayerInformation失敗:', e.message);
        return;
    }

    const arr_elements = [];
    const elms_clickableContainer = document.querySelectorAll('.clickableContainer');
    for (let i = INDEX_FIRST; i < elms_clickableContainer.length; i++) {
        const elm_clickableContainer = elms_clickableContainer[i];
        const int_sentence_element_id = escapeNumber(elm_clickableContainer.dataset.sentenceElementId);
        arr_elements.push(int_sentence_element_id);
    }

    try {
        const arr_layer_grammars = await getRegisteredGrammarsInLayer(int_layer_id, arr_elements);

        for (let i = INDEX_FIRST; i < arr_layer_grammars.length; i++) {
            const item = arr_layer_grammars[i];
            const str_type = item['type'];
            let targetUl;

            switch (str_type) {
                case 'current':
                case 'others': {
                    const arrGrammar = item['arrGrammar'];
                    const arrJapaneseParticle = item['arrJapaneseParticle'];

                    if (str_type === 'current') {
                        targetUl = usedGrammarSelectedGrammarList;
                        buildUsedGrammarSelectedListItems(arrGrammar, targetUl);

                        targetUl = usedGrammarSelectedParticleList;
                        buildUsedGrammarSelectedListItems(arrJapaneseParticle, targetUl);
                    } else {
                        targetUl = usedGrammarUnselectedGrammarList;
                        buildUsedGrammarSelectedListItems(arrGrammar, targetUl);

                        targetUl = usedGrammarUnselectedParticleList;
                        buildUsedGrammarSelectedListItems(arrJapaneseParticle, targetUl);
                    }
                    break;
                }
                case 'predicate': {
                    const arrPredicate = item['arrPredicate'];
                    targetUl = usedGrammarPredicateList;
                    buildUsedGrammarSelectedListItems(arrPredicate, targetUl);
                    break;
                }
                case 'particles': {
                    const arrParticles = item['arrParticles'];
                    targetUl = usedGrammarParticlesList;
                    buildUsedGrammarSelectedListItems(arrParticles, targetUl);
                    break;
                }
                case 'inflection': {
                    const arrInflection = item['arrInflection'];
                    targetUl = usedGrammarInflectionList;
                    buildUsedGrammarSelectedListItems(arrInflection, targetUl);
                    break;
                }
                case 'specialTerms': {
                    const arrSpecialTerms = item['arrSpecialTerms'];
                    targetUl = usedGrammarSpecialTermsList;
                    buildUsedGrammarSelectedListItems(arrSpecialTerms, targetUl);
                    break;
                }
                default:
                    break;
            }
        }
    } catch (e) {
        console.error('getRegisteredGrammarsInLayer失敗:', e.message);
        return;
    }
}

async function getLayerInformation(int_layer_id) {

	const payload = {
		int_layer_id: int_layer_id,
		int_selected_language: intSelectedLanguage
	};

	const result = await postJson(
        sentenceLayerGetLayerInformationUrl,
        payload
    );

    return Array.isArray(result.data) ? result.data : [];
}

async function getRegisteredGrammarsInLayer(int_layer_id, arr_elements) {

	const payload = {
		int_layer_id: int_layer_id,
		arr_elements: arr_elements,
		int_selected_language: intSelectedLanguage
	};

    const result = await postJson(
        sentenceLayerGetRegisteredGrammarsUrl,
        payload
    );

    return Array.isArray(result.data) ? result.data : [];
}

function buildUsedGrammarSelectedListItems(array, targetUl){
    setDetailsVisibilityByArray(array, targetUl);

    if (targetUl) targetUl.textContent = '';

    if (!Array.isArray(array) || array.length === LENGTH_EMPTY) return;

    for (let s = INDEX_FIRST; s < array.length; s++) {
        const item = array[s];
        const elmLi = document.createElement('li');
        elmLi.classList.add('sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedGrammartLi');
        elmLi.style.fontSize = '21px';
        elmLi.dataset.grammarId = escapeNumber(item['id']);
        elmLi.dataset.japanese = escapeHTML(item['japanese']);
        elmLi.textContent = escapeHTML(item['japanese']);

        const elmSpan = document.createElement('span');
        elmSpan.style.fontSize = '16px';
        elmSpan.style.color = 'blue';
        elmSpan.textContent = escapeHTML(item['rootExample']);

        elmLi.appendChild(elmSpan);
        targetUl.appendChild(elmLi);
    }
}

function setDetailsVisibilityByArray(array, targetUl){
    if (!targetUl) return;
    let details = targetUl.closest('details');
    if (!details) {
        const summary = targetUl.closest('summary');
        if (summary && summary.parentElement && summary.parentElement.tagName.toLowerCase() === 'details') {
            details = summary.parentElement;
        }
    }
    if (!details) return;

    details.removeAttribute('open');

    const hasItems = Array.isArray(array) && array.length > LENGTH_EMPTY;
    if (hasItems) {
        details.style.removeProperty('display');
    } else {
        details.style.display = 'none';
    }
}

async function displayLayerUpdateScreenGrammarJapaneseContent() {
	
    const int_japanese_id = toNullableInteger(grammarIdInput.value);

	try {

		const payload = {
			int_japanese_id: int_japanese_id,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			sentenceLayerGetGrammarTitleUrl,
			payload,
			10000
		);

		const data = result.data;

		const japanese = data?.japanese ?? null;

		grammarJapaneseContent.textContent =
			japanese === null ? 'Null' : escapeHTML(japanese);

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}

}

async function searchAlreadyRegisteredSentences() {
	try {
		const arr_registered_sentence = await getAlreadyRegisteredSentences();
		renderAlreadyRegisteredSentences(arr_registered_sentence);
		return;
	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
		return;
	}
}

async function getAlreadyRegisteredSentences() {

	const urlObj = new URL(window.location.href);
	const params = urlObj.searchParams;

	const send_grammar_unique_code = escapeHTML(params.get(KEY_GRAMMAR_UNIQUE_CODE) || '');

	const payload = {
		send_grammar_unique_code: send_grammar_unique_code,
		int_selected_language: intSelectedLanguage
	};

	const result = await postJson(
		sentenceGetRegisteredByGrammarUrl,
		payload,
		10000
	);

	return Array.isArray(result.data) ? result.data : [];
}

function renderAlreadyRegisteredSentences(arr_registered_sentence){

	let url = new URL(window.location.href),
	params = url.searchParams,
	send_grammar_unique_code = escapeHTML(params.get(KEY_GRAMMAR_UNIQUE_CODE));

	let elm_addLi,
		elm_targetUl;

	elm_targetUl = document.getElementById('wisePanelWhiteboardUiRegisteredItemsList');
	elm_targetUl.replaceChildren();

	if (arr_registered_sentence.length === LENGTH_EMPTY) {
		elm_addLi = document.createElement('li');
		elm_addLi.classList.add('resultEmpty');
		elm_addLi.textContent = '検索結果 0件';
		elm_targetUl.appendChild(elm_addLi);
		return;
	}

	for (let i = INDEX_FIRST; i < arr_registered_sentence.length; i ++) {

		let int_registered_sentence_id = escapeNumber(arr_registered_sentence[i]['registered_sentence_id']),
			str_sentence = escapeHTML(arr_registered_sentence[i].sentence),
			sentenceUniqueCode = escapeHTML(arr_registered_sentence[i].sentenceUniqueCode);

		elm_addLi = document.createElement('li');
		elm_addLi.classList.add('wiseWhiteboardUiCallAlreadyregisteredSentenceListLi', 'wiseUiFontSizeTarget');
		elm_addLi.dataset.sentenceUniqueCode = sentenceUniqueCode;

		let elm_registeredSentenceNamesContainer = document.createElement('div');
			elm_registeredSentenceNamesContainer.classList.add('registeredSentenceNamesContainer');

		let elm_registeredSentenceNamesSpan = document.createElement('span');
			elm_registeredSentenceNamesSpan.classList.add('registeredSentenceNamesSpan');
			elm_registeredSentenceNamesSpan.textContent = str_sentence;

		elm_registeredSentenceNamesContainer.appendChild(elm_registeredSentenceNamesSpan);

		let elm_registeredSentenceSortButtonsContainer = document.createElement('div');
		elm_registeredSentenceSortButtonsContainer.classList.add('registeredSentenceSortButtonsContainer');


		let elm_registeredSentencePreviousButton = document.createElement('button');
		elm_registeredSentencePreviousButton.classList.add('registeredSentenceSortPreviousButton', 'registeredSentenceSortButton');
		// マジックナンバー
		elm_registeredSentencePreviousButton.textContent = '△';

		elm_registeredSentencePreviousButton.addEventListener('pointerup', function(e) {
			e.stopPropagation();
			resortRegisteredSentences(true, send_grammar_unique_code, int_registered_sentence_id);
		}, false);


		let elm_registeredSentenceNextButton = document.createElement('button');
		elm_registeredSentenceNextButton.classList.add('registeredSentenceSortNextButton', 'registeredSentenceSortButton');
		// マジックナンバー
		elm_registeredSentenceNextButton.textContent = '▽';

		elm_registeredSentenceNextButton.addEventListener('pointerup', function(e) {
			e.stopPropagation();
			resortRegisteredSentences(false, send_grammar_unique_code, int_registered_sentence_id);
		}, false);

		
		let elm_registeredSentenceLinkToCreateLayersButton = document.createElement('button');
		elm_registeredSentenceLinkToCreateLayersButton.classList.add('registeredSentenceLinkToCreateLayersButton');
		// マジックナンバー
		elm_registeredSentenceLinkToCreateLayersButton.textContent = 'Create Layers';
		
		elm_registeredSentenceLinkToCreateLayersButton.addEventListener('pointerup', function(e) {
			e.stopPropagation();
			let url = pageCreateLayersUrl;
			let urlWithParams = `${url}/?${KEY_SENTENCE_UNIQUE_CODE}=${encodeURIComponent(sentenceUniqueCode)}`;
			window.open(urlWithParams, '_blank', 'noopener');
		}, false);
		

		elm_registeredSentenceSortButtonsContainer.appendChild(elm_registeredSentencePreviousButton);
		elm_registeredSentenceSortButtonsContainer.appendChild(elm_registeredSentenceNextButton);
		elm_registeredSentenceSortButtonsContainer.appendChild(elm_registeredSentenceLinkToCreateLayersButton);
		
		let elm_registeredSentenceToggleButtonContainer = document.createElement('div');
		elm_registeredSentenceToggleButtonContainer.classList.add('registeredSentenceToggleButtonContainer');

		let elm_addLabel = document.createElement('label');
		elm_addLabel.classList.add('registeredSentenceToggleLabel');
		elm_addLabel.setAttribute('for', `registeredSentenceToggleButton${i}`);

		let elm_addLabelsInput = document.createElement('input');
		elm_addLabelsInput.classList.add('registeredSentenceToggleButton');
		elm_addLabelsInput.type = 'checkbox';
		elm_addLabelsInput.id = `registeredSentenceToggleButton${i}`;
		elm_addLabelsInput.name = `registeredSentenceToggleButton${i}`;
		elm_addLabelsInput.value = `registeredSentenceToggleButton${i}`;
		elm_addLabelsInput.dataset.sentenceUniqueCode = sentenceUniqueCode;

		if(parseInt(arr_registered_sentence[i]['isPublished']) === 1){
			elm_addLabelsInput.checked = true;
		}
		else{
			elm_addLabelsInput.checked = false;
		}

		// elm_addLabelsInput.addEventListener('click', function(e) {
		// 	e.stopPropagation();
		// }, false);
        elm_addLabelsInput.addEventListener('pointerup', function(e) {
            e.stopPropagation();
        }, false);
		elm_addLabelsInput.addEventListener('change', async function() {
			let isPublished;
			if (elm_addLabelsInput.checked) {
				isPublished = FLAG_TRUE;
			} else {
				isPublished = FLAG_FALSE;
			}
			let payload = {
				isPublished: isPublished,
				send_sentence_unique_code: sentenceUniqueCode,
				int_selected_language: intSelectedLanguage
			};
			try {
				await postJson(
					sentenceUpdateSentencePublishedStatusUrl,
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

		});

		elm_registeredSentenceToggleButtonContainer.appendChild(elm_addLabelsInput);
		elm_registeredSentenceToggleButtonContainer.appendChild(elm_addLabel);

		elm_addLi.appendChild(elm_registeredSentenceNamesContainer);
		elm_addLi.appendChild(elm_registeredSentenceSortButtonsContainer);
		elm_addLi.appendChild(elm_registeredSentenceToggleButtonContainer);

		elm_targetUl.appendChild(elm_addLi);
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}
}


async function resortRegisteredSentences(isPrevious, send_grammar_unique_code, int_registered_sentence_id){

	let isPreviousAsNumber = isPrevious ? FLAG_TRUE : FLAG_FALSE;
	
	try {

		const payload = {
			isPreviousAsNumber: isPreviousAsNumber,
			send_grammar_unique_code: send_grammar_unique_code,
			int_registered_sentence_id: int_registered_sentence_id,
			int_selected_language: intSelectedLanguage
		};

		await postJson(
			sentenceResortRegisteredSentenceUrl,
			payload,
			10000
		);

		await searchAlreadyRegisteredSentences();

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}

}