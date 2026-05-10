
/******************************************************
 *  ADD WORD
 *
 ******************************************************/
const whiteboardUiWordSearch = document.getElementById('wisePanelWhiteboardUiWordSearch');
const whiteboardUiWordSearchOptions = document.getElementById('wisePanelWhiteboardUiWordSearchOptions');
const addWordFormStyleSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectFormStyle');
const addWordJapaneseClassificationSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectJapaneseClassification');
const addWordKanaVisibleSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectKanaVisible');
const addWordKnowledgeAreaSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectKnowledgeArea');
const addWordList = document.getElementById('wisePanelWhiteboardUiWordSearchList');
const addWordMasteryLevelSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectMasteryLevel');
const addWordOptionsCloseButton = document.getElementById('wisePanelWhiteboardUiWordSearchOptionsCloseButton');
const addWordOrderStyleSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectOrderStyle');
const addWordOutputStyleSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectOutputStyle');
const addWordSearchInput = document.getElementById('wisePanelWhiteboardUiWordSearchSearchInput');

const addWordCreateButton = document.getElementById('wisePanelWhiteboardUiWordSearchCreateButton');
const addWordLoading = document.getElementById('wisePanelWhiteboardUiWordSearchLoading');
const addWordMatchingTypeSelect = document.getElementById('wisePanelWhiteboardUiWordSearchSelectMatchingType');
const whiteboardUiWordSearchOpenOptionsButton = document.getElementById('wisePanelWhiteboardUiWordSearchOpenOptionsButton');
const addWordOptionsResetButton = document.getElementById('wisePanelWhiteboardUiWordSearchOptionsResetButton');
const addWordSearchButton = document.getElementById('wisePanelWhiteboardUiWordSearchSearchButton');


if(addWordOutputStyleSelect !== null){
	addWordOutputStyleSelect.addEventListener('change',
	function (e){
		let int_selected = escapeNumber(addWordOutputStyleSelect[addWordOutputStyleSelect.selectedIndex].value);

		let displayTargetClass = '';
		let hiddenTargetClass = '';

		if(int_selected === OUTPUT_STYLE_WORD_CONTAINER){
			addWordCreateButton.classList.add('hidden');
			displayTargetClass = '.searchWordListLiDiv';
        	hiddenTargetClass = '.searchWordListLiLabel';
		}
		else{
			addWordCreateButton.classList.remove('hidden');
			displayTargetClass = '.searchWordListLiLabel';
        	hiddenTargetClass = '.searchWordListLiDiv';
		}

		document.querySelectorAll(`.whiteboardUiWordListLi ${displayTargetClass}:not(.display-on)`)
			.forEach(el => el.classList.add('display-on'));

		document.querySelectorAll(`.whiteboardUiWordListLi ${hiddenTargetClass}.display-on`)
			.forEach(el => el.classList.remove('display-on'));
	}
	, { passive: false });
}

if(addWordOptionsResetButton !== null)
	{addWordOptionsResetButton.addEventListener('pointerup', function() {
	let currentNode = this;
	while (currentNode) {
		if (currentNode.classList && currentNode.classList.contains('wisePanelWhiteboardUi')) {
			break;
		}
		currentNode = currentNode.parentNode;
		if (!currentNode) {
			break;
		}
	}
	if (currentNode) {
		var selectElements = currentNode.querySelectorAll('select');
		selectElements.forEach(function(select) {
			select.selectedIndex = INDEX_FIRST;
		});
	}
}, false);}

if(addWordSearchButton !== null)
{addWordSearchButton.addEventListener('pointerup', function() {
	
	let elms_checked = addWordList.querySelectorAll('input[type="checkbox"]:checked');
	if(elms_checked.length > LENGTH_EMPTY)return;

	resetSelectedLiItemRetainingMenu();
	let str_searchWord = escapeHTML(addWordSearchInput.value),
	elm_targetUl = addWordList,
	elm_targetLoading = addWordLoading,
	arr_classNaming_li = ['whiteboardUiWordListLi', 'wiseUiFontSizeTarget'];
	searchWordFromList(str_searchWord, elm_targetUl, arr_classNaming_li, elm_targetLoading, addWordMatchingTypeSelect);
}, false);}

if(addWordCreateButton !== null)
{addWordCreateButton.addEventListener('pointerup', function() {
	let int_output_style = escapeNumber(addWordOutputStyleSelect[addWordOutputStyleSelect.selectedIndex].value);

	if(int_output_style === OUTPUT_STYLE_WORD_CONTAINER)return;

	let int_form_style;

	if (addWordFormStyleSelect === null) {
		int_form_style = FORM_STYLE_PLAIN;
	} else {
		int_form_style = escapeNumber(addWordFormStyleSelect[addWordFormStyleSelect.selectedIndex].value);
	}

	let elms_checked = addWordList.querySelectorAll('input[type="checkbox"]:checked');
	if(elms_checked.length === LENGTH_EMPTY)return;

	let arr_japanese = [];
	elms_checked.forEach(function(elm) {
		let str_japanese;
		if(int_form_style === FORM_STYLE_PLAIN){
			str_japanese = escapeHTML(elm.dataset.japanese);
		}
		else{
			str_japanese = escapeHTML(elm.dataset.japanesePolite);
		}
		arr_japanese.push({
			japanese : str_japanese
		});
		elm.checked = false;
		});
	if(arr_japanese.length === LENGTH_EMPTY)return;

	let str_japanese = '';

	arr_japanese.forEach((item, index) => {
		str_japanese += item.japanese;
		if (index < arr_japanese.length - LAST_INDEX_OFFSET) {
			str_japanese += '\n';
		}
	});

	const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

	lastCreatedMovableContainer = createTextAreaContainer(str_japanese, point.x, point.y);
	updateTextareaContainerSize(lastCreatedMovableContainer.querySelector('.innerContainerTextArea'), str_japanese);

	advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, str_japanese);
	
}, false);}

if(whiteboardUiWordSearchOpenOptionsButton !== null)
{whiteboardUiWordSearchOpenOptionsButton.addEventListener('pointerup', function() {
	openWisePanelUi(whiteboardPanel, whiteboardUiWordSearchOptions);
}, false);}


if (addWordSearchInput !== null) {
    addWordSearchInput.addEventListener('keydown', function (e) {

        if (e.key === 'Enter' || e.keyCode === 13) {

            const isClicked = applySelectedLiItemAction();
            if (isClicked) {
                e.preventDefault();
                return;
            }

            resetSelectedLiItemRetainingMenu();

            const str_searchWord = escapeHTML(addWordSearchInput.value);
            const elm_targetUl = addWordList;
            const elm_targetLoading = addWordLoading;
            const arr_classNaming_li = ['whiteboardUiWordListLi', 'wiseUiFontSizeTarget'];

            searchWordFromList(
                str_searchWord,
                elm_targetUl,
                arr_classNaming_li,
                elm_targetLoading,
                addWordMatchingTypeSelect
            );

            e.preventDefault();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {

    // 1. 存在確認
    if (!addWordFormStyleSelect || !addWordOutputStyleSelect) {
        return;
    }

    // 2. 現在のpath取得
    const currentPath = window.location.pathname;

    // 3. register-sentenceか判定
    if (currentPath.includes('register-sentence')) {

        // register-sentence の場合
        addWordFormStyleSelect.value = String(FORM_STYLE_PLAIN);
        addWordOutputStyleSelect.value = String(OUTPUT_STYLE_WORD_CONTAINER);
		addWordCreateButton.classList.add('hidden');

    } else {

        // それ以外
        addWordFormStyleSelect.value = String(FORM_STYLE_POLITE);
        addWordOutputStyleSelect.value = String(OUTPUT_STYLE_TEXTAREA_CONTAINER);
		addWordCreateButton.classList.remove('hidden');

    }

});


async function searchWordFromList(str_searchWord, elm_targetUl, arr_classNaming_li, elm_targetLoading, elm_targetSelect){

	let elm_addLi;

	let classNaming_div = arr_classNaming_li[INDEX_FIRST] + 'Div';
	let classNaming_label = arr_classNaming_li[INDEX_FIRST] + 'Labels';
	let idNaming_labelsInput = classNaming_label + 'Input';
	let classNaming_labelsInput = classNaming_label + 'Inputs';
	let classNaming_labelsText = classNaming_label + 'Texts';

	elm_targetUl.replaceChildren();

	let int_form_style;
	let int_output_style;
	let int_order_style;
	let int_kana_visible;
	let int_matching_type = escapeNumber(
		elm_targetSelect[elm_targetSelect.selectedIndex].value
	);
	let int_learningScope;
	let int_masta_japanese_classification_id;
	let int_mastery_level;


	if(elm_targetUl === addWordList){
		int_form_style = escapeNumber(addWordFormStyleSelect[addWordFormStyleSelect.selectedIndex].value);
		int_output_style = escapeNumber(addWordOutputStyleSelect[addWordOutputStyleSelect.selectedIndex].value);
		int_order_style = escapeNumber(addWordOrderStyleSelect[addWordOrderStyleSelect.selectedIndex].value);
		int_kana_visible = escapeNumber(addWordKanaVisibleSelect[addWordKanaVisibleSelect.selectedIndex].value);
		int_learningScope = escapeNumber(addWordKnowledgeAreaSelect[addWordKnowledgeAreaSelect.selectedIndex].value);
		int_masta_japanese_classification_id = escapeNumber(addWordJapaneseClassificationSelect[addWordJapaneseClassificationSelect.selectedIndex].value);
		int_mastery_level = escapeNumber(addWordMasteryLevelSelect[addWordMasteryLevelSelect.selectedIndex].value);
	}
	else{
		int_form_style = FORM_STYLE_PLAIN;
		int_output_style = OUTPUT_STYLE_WORD_CONTAINER;
		int_order_style = ORDER_STYLE_ASCENDING;
		int_kana_visible = KANA_VISIBLE;
		int_learningScope = LEARNING_SCOPE_SELECT_ALL;
		int_masta_japanese_classification_id = SELECT_ALL;
		int_mastery_level = SELECT_ALL;
	}

	if(str_searchWord.length === LENGTH_EMPTY){
		if(
			int_masta_japanese_classification_id === SELECT_ALL &&
			int_mastery_level === SELECT_ALL
		){
			elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
			return;
		}
	}

	let currentURL = window.location.href,
		isIncludesGrammar = false;

	for (let i = INDEX_FIRST; i < PAGES_INCLUDING_GRAMMAR.length; i++) {
		if (currentURL.startsWith(PAGES_INCLUDING_GRAMMAR[i])) {
			isIncludesGrammar = true;
		}
	}
	let isIncludesGrammarAsNumber = isIncludesGrammar ? FLAG_TRUE : FLAG_FALSE;

	elm_targetLoading.classList.remove('loading-hidden');

	let payload = {
		search_word: str_searchWord,
		int_order_style: int_order_style,
		int_matching_type: int_matching_type,
		int_learningScope: int_learningScope,
		int_masta_japanese_classification_id: int_masta_japanese_classification_id,
		int_mastery_level: int_mastery_level,
		isIncludesGrammarAsNumber: isIncludesGrammarAsNumber,
		int_selected_language: intSelectedLanguage
	};

	try {

        const result = await postJson(
            wiseCoreSearchWordUrl,
            payload,
            10000
        );

        const data = (result && result.data !== undefined)
            ? result.data
            : result;

        if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
            const elm_addLi = document.createElement('li');
            elm_addLi.classList.add('resultEmpty');
            elm_addLi.textContent = '検索結果 0件';
            elm_targetUl.appendChild(elm_addLi);
            elm_targetLoading.classList.add('loading-hidden');
            return;
        }

        if (data.length > 3000) {
            const elm_addLi = document.createElement('li');
            elm_addLi.classList.add('resultEmpty');
            elm_addLi.textContent = '検索対象が多すぎます。';
            elm_targetUl.appendChild(elm_addLi);
            elm_targetLoading.classList.add('loading-hidden');
            return;
        }

        for (let i = INDEX_FIRST; i < data.length; i++) {

            if (data[i] === null) continue;

            let int_japanese_id = escapeNumber(data[i].japaneseId);
            let int_sub_classification_id = escapeNumber(data[i].subClassificationId);
            let str_japanese = escapeHTML(data[i].japanese);
            let str_japanese_polite = escapeHTML(data[i].japanesePolite);
            let str_kana = escapeHTML(data[i].kana);
            let str_kana_polite = escapeHTML(data[i].kanaPolite);
            let str_sub_classification = escapeHTML(data[i].subClassification);
            let str_classification = escapeHTML(data[i].classification);
            let int_category_id = escapeNumber(data[i].categoryId);

            const elm_addLi = document.createElement('li');
            elm_addLi.classList.add('searchWordListLi', ...arr_classNaming_li);
            elm_addLi.dataset.japaneseId = int_japanese_id;
            elm_addLi.dataset.subClassificationId = int_sub_classification_id;
            elm_addLi.dataset.japanese = str_japanese;
            elm_addLi.dataset.japanesePolite = str_japanese_polite;
            elm_addLi.dataset.kana = str_kana;
            elm_addLi.dataset.kanaPolite = str_kana_polite;
            elm_addLi.dataset.subClassification = str_sub_classification;
            elm_addLi.dataset.classification = str_classification;
            elm_addLi.dataset.categoryId = int_category_id;

            const elm_addDiv = document.createElement('div');
            elm_addDiv.classList.add('searchWordListLiDiv', classNaming_div);

            const elm_addLabel = document.createElement('label');
            elm_addLabel.classList.add('searchWordListLiLabel', classNaming_label);
            elm_addLabel.setAttribute('for', idNaming_labelsInput + i);

            const elm_addLabelsInput = document.createElement('input');
            elm_addLabelsInput.classList.add('searchWordListLiInput', classNaming_labelsInput);
            elm_addLabelsInput.type = 'checkbox';
            elm_addLabelsInput.id = idNaming_labelsInput + i;
            elm_addLabelsInput.name = idNaming_labelsInput + i;
            elm_addLabelsInput.value = idNaming_labelsInput + i;
            elm_addLabelsInput.dataset.japanese = str_japanese;
            elm_addLabelsInput.dataset.japanesePolite = str_japanese_polite;

            const elm_addLabelsDiv = document.createElement('div');
            elm_addLabelsDiv.classList.add(classNaming_labelsText);

            if (int_form_style !== FORM_STYLE_PLAIN) {
                str_japanese = str_japanese_polite;
                str_kana = str_kana_polite;
            }

            if (int_output_style === OUTPUT_STYLE_WORD_CONTAINER) {
                elm_addDiv.classList.add('display-on');
            } else {
                elm_addLabel.classList.add('display-on');
            }

            if (int_kana_visible === KANA_HIDDEN) {
                str_kana = '';
            }

            if (int_category_id === JP_CATEGORY_GRAMMAR) {
                elm_addDiv.textContent = `${str_japanese} ${str_kana} [文法]`;
                elm_addLabelsDiv.textContent = `${str_japanese} ${str_kana} [文法]`;
            } else {
                elm_addDiv.textContent = `${str_japanese} ${str_kana} [${str_classification}]`;
                elm_addLabelsDiv.textContent = `${str_japanese} ${str_kana} [${str_classification}]`;
            }

            elm_addLabel.appendChild(elm_addLabelsInput);
            elm_addLabel.appendChild(elm_addLabelsDiv);

            elm_addLi.appendChild(elm_addDiv);
            elm_addLi.appendChild(elm_addLabel);
            elm_targetUl.appendChild(elm_addLi);
        }

        applyFontSizeVariation(
            ['wiseUiFontSizeTarget'],
            'wiseUiFontSizeTargetVariationDifference'
        );

        elm_targetLoading.classList.add('loading-hidden');

    } catch (e) {

        alert(e.message || 'Error');
        elm_targetLoading.classList.add('loading-hidden');
        return;
    }
};
