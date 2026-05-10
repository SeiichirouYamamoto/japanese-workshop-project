
/******************************************************
 *  HISTORY
 *
 ******************************************************/


const whiteboardUiHistory = document.getElementById('wisePanelWhiteboardUiHistory');

const whiteboardUiCreatedWordHistory = document.getElementById('wisePanelWhiteboardUiCreatedWordHistory');
const whiteboardUiActionHistory = document.getElementById('wisePanelWhiteboardUiActionHistory');
const whiteboardUiChartHistory = document.getElementById('wisePanelWhiteboardUiChartHistory');


const whiteboardUiHistoryCreateWordItem = document.getElementById('wisePanelWhiteboardUiHistoryLiCallCreateWordHistory');
if(whiteboardUiHistoryCreateWordItem !== null)
{whiteboardUiHistoryCreateWordItem.addEventListener('pointerup', function() {
	resetToSelectMode();
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiHistory);
	openWisePanelUi(whiteboardPanel, whiteboardUiCreatedWordHistory);
	renderCreatedWordHistory();
}, false);}





const whiteboardUiHistoryActionItem = document.getElementById('wisePanelWhiteboardUiHistoryLiCallActionHistory');
if(whiteboardUiHistoryActionItem !== null)
{whiteboardUiHistoryActionItem.addEventListener('pointerup', function() {
	resetToSelectMode();
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiHistory);
	openWisePanelUi(whiteboardPanel, whiteboardUiActionHistory);
	renderActionHistory();
}, false);}


function renderCreatedWordHistory(){

	let elm_targetUl;

	elm_targetUl = document.getElementById('wisePanelWhiteboardUiCreatedWordHistoryList');
	elm_targetUl.replaceChildren();

	if (wordListHistory.length === LENGTH_EMPTY) {
		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
		return;
	}

	for (let i = INDEX_FIRST; i < wordListHistory.length; i ++) {

		let int_japanese_id = escapeNumber(wordListHistory[i]['japaneseId']),
			int_sub_classification_id = escapeNumber(wordListHistory[i]['subClassificationId']),
			str_japanese = escapeHTML(wordListHistory[i]['japanese']),
			str_kana = escapeHTML(wordListHistory[i]['kana']),
			str_sub_classification = escapeHTML(wordListHistory[i]['subClassification']),
			int_category_id = escapeNumber(wordListHistory[i]['categoryId']);

		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('whiteboardUiCreatedWordHistoryLi', 'wiseUiFontSizeTarget');
			elm_addLi.dataset.japaneseId = int_japanese_id;
			elm_addLi.dataset.japaneseElementId = DEFAULT_JAPANESE_ELEMENT_ID;
			elm_addLi.dataset.subClassificationId = int_sub_classification_id;
			elm_addLi.dataset.japanese = str_japanese;
			elm_addLi.dataset.kana = str_kana;
			elm_addLi.dataset.subClassification = str_sub_classification;
			elm_addLi.dataset.categoryId = int_category_id;
			elm_addLi.textContent = str_japanese+' '+str_kana+' '+str_sub_classification;

		elm_targetUl.appendChild(elm_addLi);
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}
}





function renderActionHistory(){
	
	let elm_targetUl;

	elm_targetUl = document.getElementById('wisePanelWhiteboardUiActionHistoryList');
	elm_targetUl.replaceChildren();

	if (operationHistory.length === LENGTH_EMPTY) {
		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
		return;
	}

	for (let i = INDEX_FIRST; i < operationHistory.length; i ++) {

		let str_title = escapeHTML(operationHistory[i]['title']);

		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('wisePanelWhiteboardUiActionHistoryLi', 'wiseUiFontSizeTarget');
			elm_addLi.dataset.index = i;
			elm_addLi.textContent = str_title;

			elm_addLi.addEventListener('pointerup', function() {
			currentHistoryIndex = i;
			restoreStateFromHistory();
			}, false);

		elm_targetUl.appendChild(elm_addLi);
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}

}

