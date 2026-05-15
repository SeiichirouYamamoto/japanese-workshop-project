/******************************************************
 *  CLOSE
 *
 ******************************************************/

const whiteboardUiCreateStickyNoteCloseButton = document.getElementById('wisePanelWhiteboardUiCreateStickyNoteCloseButton');
if(whiteboardUiCreateStickyNoteCloseButton !== null)
{whiteboardUiCreateStickyNoteCloseButton.addEventListener('pointerup', function() {
	resetAppearanceLayoutState(appearanceLayoutState);
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiCreateStickyNote);
}, false);}
	
const whiteboardUiLabelListCloseButton = document.getElementById('wisePanelWhiteboardUiLabelListCloseButton');
if(whiteboardUiLabelListCloseButton !== null)
{whiteboardUiLabelListCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiLabelList);
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordInformation);
}, false);}


const whiteboardUiSettingsCloseButton = document.getElementById('wisePanelWhiteboardUiSettingsCloseButton');
if(whiteboardUiSettingsCloseButton !== null)
{whiteboardUiSettingsCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiSettings);
}, false);}

const whiteboardUiFormListCloseButton = document.getElementById('wisePanelWhiteboardUiFormListCloseButton');
if(whiteboardUiFormListCloseButton !== null)
{whiteboardUiFormListCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiFormList);
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordInformation);
}, false);}
	
const whiteboardUiWordInformationCloseButton = document.getElementById('wisePanelWhiteboardUiWordInformationCloseButton');
if(whiteboardUiWordInformationCloseButton !== null)
{whiteboardUiWordInformationCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordInformation);
}, false);}

const whiteboardUiRegisteredItemsCloseButton = document.getElementById('wisePanelWhiteboardUiRegisteredItemsCloseButton');
if(whiteboardUiRegisteredItemsCloseButton !== null)
{whiteboardUiRegisteredItemsCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiRegisteredItems);
}, false);}

const whiteboardUiHistoryCloseButton = document.getElementById('wisePanelWhiteboardUiHistoryCloseButton');
if(whiteboardUiHistoryCloseButton !== null)
{whiteboardUiHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiHistory);
}, false);}

const whiteboardUiCreatedWordHistoryCloseButton = document.getElementById('wisePanelWhiteboardUiCreatedWordHistoryCloseButton');
if(whiteboardUiCreatedWordHistoryCloseButton !== null)
{whiteboardUiCreatedWordHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiCreatedWordHistory);
}, false);}

const whiteboardUiChartHistoryCloseButton = document.getElementById('wisePanelWhiteboardUiChartHistoryCloseButton');
if(whiteboardUiChartHistoryCloseButton !== null)
{whiteboardUiChartHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiChartHistory);
}, false);}

const whiteboardUiActionHistoryCloseButton = document.getElementById('wisePanelWhiteboardUiActionHistoryCloseButton');
if(whiteboardUiActionHistoryCloseButton !== null)
{whiteboardUiActionHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiActionHistory);
}, false);}

const whiteboardUiCreateNewWordCloseButton = document.getElementById('wisePanelWhiteboardUiCreateNewWordCloseButton');
if(whiteboardUiCreateNewWordCloseButton !== null)
{whiteboardUiCreateNewWordCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiCreateNewWord);
}, false);}

const whiteboardUiWordSearchCloseButton = document.getElementById('wisePanelWhiteboardUiWordSearchCloseButton');
if(whiteboardUiWordSearchCloseButton !== null)
{whiteboardUiWordSearchCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordSearchOptions);
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordSearch);
}, false);}

const whiteboardUiWordSearchOptionsCloseButton = document.getElementById('wisePanelWhiteboardUiWordSearchOptionsCloseButton');
if(whiteboardUiWordSearchOptionsCloseButton !== null)
{whiteboardUiWordSearchOptionsCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiWordSearchOptions);
}, false);}



/******************************************************
 *  INCREASE
 *
 ******************************************************/
const wiseUiFontSizeIncreaseButtons = document.querySelectorAll('.wiseUiFontSizeIncreaseButton');
const wiseUiFontSizeDecreaseButtons = document.querySelectorAll('.wiseUiFontSizeDecreaseButton');


for(let i = INDEX_FIRST; i < wiseUiFontSizeIncreaseButtons.length; i++) {
	wiseUiFontSizeIncreaseButtons[i].addEventListener('pointerup',
		function (e){
			changeWiseUiFontSize(true);
		}
	, { passive: false });
}
for(let i = INDEX_FIRST; i < wiseUiFontSizeDecreaseButtons.length; i++) {
	wiseUiFontSizeDecreaseButtons[i].addEventListener('pointerup',
		function (e){
			changeWiseUiFontSize(false);
		}
	, { passive: false });
}



/******************************************************
 *  SEARCH INPUTS
 *
 ******************************************************/

const whiteboardUiSearchInputs = document.querySelectorAll('.wisePanelWhiteboardUiSearchInput');

for(let i = INDEX_FIRST; i < whiteboardUiSearchInputs.length; i++) {
	whiteboardUiSearchInputs[i].addEventListener('focus',
		function (e){
			let currentNode = e.target;
			findWhiteboardUiSelectableList(currentNode);
			if(wisePanelWhiteboardUiSelectableList.length <= selectedItemIndex){
				selectedItemIndex = -1;
			}
			isAllowedListScroll = true;
		}
	, { passive: false });
	whiteboardUiSearchInputs[i].addEventListener('blur',
		function (e){
			resetSelectedLiItem();
		}
	, { passive: false });

	whiteboardUiSearchInputs[i].addEventListener('compositionstart', function(e) {
		isCompositionInProgress_wisePanelWhiteboardUiSearchInput = true;
	});

	whiteboardUiSearchInputs[i].addEventListener('compositionend', function(e) {
		isCompositionInProgress_wisePanelWhiteboardUiSearchInput = false;
	});

	whiteboardUiSearchInputs[i].addEventListener('input', function(e) {
		resetSelectedLiItemRetainingMenu();
	});

}





document.addEventListener('pointerup', (e) => {

    const elm = e.target.closest('.whiteboardUiWordListLiLabel');
    if (!elm) {
        return;
    }

    e.stopPropagation();
});

document.addEventListener('pointerup', async (e) => {

    const li = e.target.closest('.wiseWhiteboardUiCallAlreadyregisteredSentenceListLi');
    if (!li) {
        return;
    }

    const send_sentence_unique_code = escapeHTML(li.dataset.sentenceUniqueCode || '');

    try {
        const arr_wordContainers = await reviewWordContainers(send_sentence_unique_code, false);
        // 必要なら arr_wordContainers を使う
        void arr_wordContainers;
    } catch (error) {
        console.error('Error:', error && error.message ? error.message : error);
        alert((error && error.message) || 'Error');
    }
});


function resetSelectedLiItem(){
	wisePanelWhiteboardUiSelectableList = null;
	isAllowedListScroll = false;
	selectedItemIndex = -1;

	let elms = document.querySelectorAll('.selectedLiItem');

	for(let i = INDEX_FIRST; i < elms.length; i++){
		elms[i].classList.remove('selectedLiItem');
	}
}


function resetSelectedLiItemRetainingMenu(){

	selectedItemIndex = -1;

	let elms = document.querySelectorAll('.selectedLiItem');

	for(let i = INDEX_FIRST; i < elms.length; i++){
		elms[i].classList.remove('selectedLiItem');
	}
}


function findWhiteboardUiSelectableList(currentNode){
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
		const elm_wisePanelWhiteboardUiSelectableList = currentNode.querySelector('.wisePanelWhiteboardUiSelectableList');
		if (elm_wisePanelWhiteboardUiSelectableList) {
			wisePanelWhiteboardUiSelectableList = elm_wisePanelWhiteboardUiSelectableList;
		}
	}
}



function setSelectedLiItem(currentItem){

	if(currentItem.classList.contains('resultEmpty'))return;

	resetSelectedLiItemRetainingMenu();
	findWhiteboardUiSelectableList(currentItem);
	if(!wisePanelWhiteboardUiSelectableList)return;

	let liElements = wisePanelWhiteboardUiSelectableList.querySelectorAll('li');
	let index = Array.from(liElements).indexOf(currentItem);
	isAllowedListScroll = true;
	selectedItemIndex = index;
	liElements[selectedItemIndex].classList.add('selectedLiItem');
}


function applySelectedLiItemAction() {

    if (isCompositionInProgress_wisePanelWhiteboardUiSearchInput || !isAllowedListScroll) {
        return false;
    }

    const liElements = wisePanelWhiteboardUiSelectableList.querySelectorAll('li');
    const count_liElements = liElements.length;

    let int_output_style;
    if (addWordOutputStyleSelect === null) {
        int_output_style = OUTPUT_STYLE_WORD_CONTAINER;
    } else {
        int_output_style = escapeNumber(addWordOutputStyleSelect[addWordOutputStyleSelect.selectedIndex].value);
    }

    if (count_liElements <= COUNT_EMPTY) {
        return false;
    }

    if (liElements[INDEX_FIRST].classList.contains('resultEmpty')) {
        return false;
    }

    if (selectedItemIndex === -1) {
        return false;
    }

    if (int_output_style !== OUTPUT_STYLE_WORD_CONTAINER) {
        return false;
    }

    const liElement = liElements[selectedItemIndex];
    if (!liElement) {
        return false;
    }

    executeWhiteboardUiWordListItemAction(liElement);
    return true;
}


function buildGrammarViewArtifacts(json, targetsObj) {
    const entryBody = document.createElement('div');
    entryBody.classList.add('entryBody');
    entryBody.style.width = '100%';
    entryBody.style.height = '100%';
    entryBody.style.padding = '0';
    entryBody.style.margin = '0';

    let mainTitle;

    for (let i = INDEX_FIRST; i < json.length; i++) {
        let arr_targetsMain = targetsObj.main;
        let arr_targetsColumn = targetsObj.column;

        const rootDefaults = Array.from(new Set(json[i].map(item => item.rootDefault)));
        const firstTitle = rootDefaults[INDEX_FIRST];
        if (i === INDEX_FIRST) {
            mainTitle = firstTitle;
        }
        const rootElm = document.createElement('h1');
        rootElm.classList.add('rootHeaders');
        rootElm.style.fontSize = '40px';
        rootElm.style.width = 'auto';
        rootElm.textContent = firstTitle;

        const uniqueSectionIds = Array.from(new Set(json[i].map(item => item.sectionId)));
        const uniqueSectionItems = uniqueSectionIds.map(sectionId => {
            const itemsWithSectionId = json[i].filter(item => item.sectionId === sectionId);
            const firstItem = itemsWithSectionId[INDEX_FIRST];
            return {
                sectionId: sectionId,
                sectionSelectedLanguage: firstItem.sectionSelectedLanguage,
                sectionAttribute: firstItem.sectionAttribute
            };
        });

        const classItemsContainer = document.createElement('div');
        classItemsContainer.classList.add('classItemsContainer');
        classItemsContainer.style.width = '80%';
        classItemsContainer.style.display = 'flex';
        classItemsContainer.style.flexDirection = 'column';
        classItemsContainer.style.justifyContent = 'center';
        classItemsContainer.style.alignItems = 'center';

        uniqueSectionItems.forEach(targetSectionItems => {
            const uniqueMainIds = Array.from(new Set(
                json[i]
                    .filter(item => item.sectionId === targetSectionItems.sectionId)
                    .map(item => item.mainId)
            ));

            const uniqueMainItems = uniqueMainIds.map(mainId => {
                const itemsWithMainId = json[i].filter(item => item.mainId === mainId);
                const firstItem = itemsWithMainId[INDEX_FIRST];
                let result = {
                    mainId: mainId,
                    mainAttribute: firstItem.mainAttribute
                };
                arr_targetsMain.forEach(key => {
                    result[key] = firstItem[key];
                });
                return result;
            });

            const mainItemsContainer = document.createElement('div');
            mainItemsContainer.classList.add('mainItemsContainer');
            mainItemsContainer.style.width = '100%';
            mainItemsContainer.style.display = 'flex';
            mainItemsContainer.style.flexDirection = 'column';
            mainItemsContainer.style.marginBottom = '80px';

            uniqueMainItems.forEach(targetMainItems => {
                const uniqueDescriptionIds = Array.from(new Set(
                    json[i]
                        .filter(item => item.mainId === targetMainItems.mainId)
                        .map(item => item.descriptionId)
                ));

                const uniqueColumnItems = uniqueDescriptionIds.map(descriptionId => {
                    const itemsWithDescriptionId = json[i].filter(item => item.descriptionId === descriptionId);
                    const firstItem = itemsWithDescriptionId[INDEX_FIRST];
                    let result = {
                        descriptionId: descriptionId,
                        descriptionAttribute: firstItem.descriptionAttribute
                    };
                    arr_targetsColumn.forEach(key => {
                        result[key] = firstItem[key];
                    });
                    return result;
                });

                const columnItemsContainer = document.createElement('div');
                columnItemsContainer.classList.add('columnItemsContainer');
                columnItemsContainer.style.width = '80%';
                columnItemsContainer.style.margin = 'auto';
                columnItemsContainer.style.display = 'flex';
                columnItemsContainer.style.flexDirection = 'column';

                let count = COUNT_FIRST;

                for (const targetColumnItems of uniqueColumnItems) {
                    const columnItemsLRContainer = document.createElement('div');
                    columnItemsLRContainer.classList.add('columnItemsLRContainer');
                    columnItemsLRContainer.style.display = 'flex';
                    columnItemsLRContainer.style.width = 'auto';
                    if (targetColumnItems.descriptionId === '') {
                        continue;
                    }
                    arr_targetsColumn.forEach(key => {
                        let columnText = '';
                        if (targetColumnItems[key] === '') {
                            columnText = targetColumnItems.descriptionAttribute;
                        } else if (targetColumnItems.descriptionAttribute === AVOID_NULL_PROXY_STRING) {
                            columnText = targetColumnItems[key];
                        } else {
                            columnText = targetColumnItems.descriptionAttribute + ': ' + targetColumnItems[key];
                        }
                        const columnElm = document.createElement('div');
                        columnElm.classList.add('columnContents');
                        columnElm.style.fontSize = '20px';
                        columnElm.style.padding = '5px 0px 15px 15px';
                        columnElm.style.flex = '1';
                        columnElm.style.whiteSpace = 'pre-wrap';
                        columnElm.textContent = columnText;
                        columnItemsLRContainer.appendChild(columnElm);
                    });
                    columnItemsContainer.appendChild(columnItemsLRContainer);
                    ++count;
                }

                const mainItemsLRContainer = document.createElement('div');
                mainItemsLRContainer.classList.add('mainItemsLRContainer');
                mainItemsLRContainer.style.display = 'flex';
                mainItemsLRContainer.style.width = 'auto';

                arr_targetsMain.forEach(key => {
                    const mainElm = document.createElement('div');
                    mainElm.classList.add('mainHeaders');
                    mainElm.style.fontSize = '26px';
                    mainElm.style.width = 'auto';
                    mainElm.style.padding = '15px';
                    mainElm.style.textAlign = 'left';
                    mainElm.style.flex = '1';
                    mainElm.style.whiteSpace = 'pre-wrap';
                    mainElm.textContent = targetMainItems[key];
                    mainItemsLRContainer.appendChild(mainElm);
                });

                const mainItemContainer = document.createElement('div');
                mainItemContainer.classList.add('mainItemContainer');
                mainItemContainer.style.width = '100%';
                mainItemContainer.style.marginBottom = '40px';
                mainItemContainer.style.display = 'flex';
                mainItemContainer.style.flexDirection = 'column';

                if (count > COUNT_EMPTY) {
                    const columnItemsLRContainer = document.createElement('div');
                    columnItemsLRContainer.classList.add('columnItemsLRContainer');
                    columnItemsLRContainer.style.display = 'flex';
                    columnItemsLRContainer.style.width = 'auto';
                    arr_targetsMain.forEach(key => {
                        const columnHeader = document.createElement('div');
                        columnHeader.classList.add('columnHeaders');
                        columnHeader.style.fontSize = '22px';
                        columnHeader.style.width = 'auto';
                        columnHeader.style.padding = '5px 0 15px 0';
                        columnHeader.style.flex = '1';
                        columnHeader.style.textAlign = 'center';
                        columnHeader.textContent = '「' + targetMainItems.mainAttribute + '」';
                        columnItemsLRContainer.appendChild(columnHeader);
                        columnItemsContainer.style.border = '1px solid rgb(64, 212, 229)';
                        columnItemsContainer.style.borderRadius = '1px';
                    });
                    const firstChild = columnItemsContainer.firstChild;
                    columnItemsContainer.insertBefore(columnItemsLRContainer, firstChild);
                }

                mainItemContainer.appendChild(mainItemsLRContainer);
                mainItemContainer.appendChild(columnItemsContainer);

                mainItemsContainer.appendChild(mainItemContainer);
            });

            const classElm = document.createElement('h2');
            classElm.classList.add('classHeaders');
            classElm.style.fontSize = '32px';
            classElm.style.width = '100%';
            classElm.style.textAlign = 'center';
            classElm.style.borderBottom = '3px solid rgb(64, 212, 229)';
            classElm.style.paddingBottom = '5px';
            if (targetSectionItems.sectionSelectedLanguage === '') {
                classElm.textContent = targetSectionItems.sectionAttribute;
            } else {
                classElm.textContent = targetSectionItems.sectionSelectedLanguage;
            }

            const classItemContainer = document.createElement('div');
            classItemContainer.classList.add('classItemContainer');
            classItemContainer.style.width = '100%';
            classItemContainer.style.display = 'flex';
            classItemContainer.style.flexDirection = 'column';
            classItemContainer.appendChild(classElm);
            classItemContainer.appendChild(mainItemsContainer);

            classItemsContainer.appendChild(classItemContainer);
        });

        const grammarViewContainer = document.createElement('div');
        grammarViewContainer.classList.add('grammarViewContainer');
        grammarViewContainer.style.width = '100%';
        grammarViewContainer.style.height = '100%';
        grammarViewContainer.style.padding = '15px';
        grammarViewContainer.style.boxSizing = 'border-box';
        grammarViewContainer.style.display = 'flex';
        grammarViewContainer.style.flexDirection = 'column';
        grammarViewContainer.style.justifyContent = 'center';
        grammarViewContainer.style.alignItems = 'center';
        grammarViewContainer.appendChild(rootElm);
        grammarViewContainer.appendChild(classItemsContainer);

        entryBody.appendChild(grammarViewContainer);
    }

    const htmlString = `
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${mainTitle}</title>
  </head>
  <body>
    ${entryBody.outerHTML}
  </body>
</html>`.trim();

    const environmentDependentCharacters = /[\u24B6-\u24CF\uFF21-\uFF3A\uFF41-\uFF5A]/g;
    const replacedMainTitle = String(mainTitle || '').replace(environmentDependentCharacters, '～');

    return { entryBody, mainTitle, htmlString, replacedMainTitle };
}

async function downloadGrammarView(isPDF, isZIP, idUniqueCode, send_value, targetsObj) {

    try {
        const payload = {
            is_unique_code: idUniqueCode ? FLAG_TRUE : FLAG_FALSE,
            send_value: send_value,
            int_selected_language: intSelectedLanguage
        };

        const result = await postJson(
            wiseCoreGetDataGrammarViewUrl,
            payload,
            10000
        );

        const data = result.data;

        if (!data || (Array.isArray(data) && data.length === LENGTH_EMPTY)) {
            return;
        }

        const { entryBody, mainTitle, htmlString, replacedMainTitle } =
            buildGrammarViewArtifacts(data, targetsObj);

        if (isPDF) {
            const filenamePDF = replacedMainTitle + '.pdf';
            downloadPdf(entryBody, filenamePDF);
            entryBody.remove();
            return;
        }

        const filenameHTML = replacedMainTitle + '.html';
        const filenameZIP = replacedMainTitle + '.zip';

        if (isZIP) {
            downloadContentsAsZip(filenameHTML, filenameZIP, htmlString);
        } else {
            const blob = new Blob([htmlString], { type: 'text/html;charset=utf-8' });
            downloadContents(blob, filenameHTML);
        }

        entryBody.remove();
        return;

    } catch (e) {
        const msg = e?.message || 'Error';
        if (String(msg).includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(msg);
        }
        throw e;
    }
}



document.addEventListener('pointerup', async (e) => {
	const li = e.target.closest('.whiteboardUiWordListLi, .whiteboardUiCreatedWordHistoryLi');
    if (!li) {
        return;
    }

    executeWhiteboardUiWordListItemAction(li);
});


async function executeWhiteboardUiWordListItemAction(li) {

    if (li.classList.contains('whiteboardUiWordListLi')) {

        const div = li.querySelector('.searchWordListLiDiv');
        const label = li.querySelector('.searchWordListLiLabel');

        const is_div_mode = !!div && div.classList.contains('display-on');
        const is_label_mode = !!label && label.classList.contains('display-on');

        if (!is_div_mode || is_label_mode) {
            return;
        }
    }

    setSelectedLiItem(li);

    const send_japanese_id = escapeNumber(li.dataset.japaneseId);
    const send_sub_classification_id = escapeNumber(li.dataset.subClassificationId);

    let send_form_style;
    if (addWordFormStyleSelect === null) {
        send_form_style = FORM_STYLE_PLAIN;
    } else {
        send_form_style = escapeNumber(addWordFormStyleSelect[addWordFormStyleSelect.selectedIndex].value);
    }

    const send_japanese = escapeHTML(li.dataset.japanese);
    const send_kana = escapeHTML(li.dataset.kana);
    const send_sub_classification = escapeHTML(li.dataset.subClassification);
    const int_category_id = escapeNumber(li.dataset.categoryId);

    if (send_japanese_id === DEFAULT_JAPANESE_ID && send_sub_classification_id === DEFAULT_SUB_CLASSIFICATION_ID) {

		const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

		createNewWord(send_japanese, send_kana, DEFAULT_SUB_CLASSIFICATION_ID, point.x, point.y);

		advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, 'A');
		
        return;
    }

    if (li.classList.contains('whiteboardUiWordListLi')) {
        wordListHistory.push({
            japaneseId: send_japanese_id,
            subClassificationId: send_sub_classification_id,
            japanese: send_japanese,
            kana: send_kana,
            subClassification: send_sub_classification,
            categoryId: int_category_id
        });
    }

    const send_data = {
        send_japanese_id: send_japanese_id,
        send_japanese_element_id: DEFAULT_JAPANESE_ELEMENT_ID,
        send_sub_classification_id: INT_NONE,
        send_form_id: send_form_style,
        send_label_id: INT_NONE,
        send_voice_id: INT_NONE,
        str_japanese: send_japanese,
        str_kana: send_kana,
        int_selected_language: intSelectedLanguage
    };

    try {
		const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);
        await createWordContainers(send_data, point.x, point.y);
		advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, 'A');
    } catch (err) {
        console.error('createWordContainers error:', err && err.message ? err.message : err);
        return;
    }

    const elm_wisePanelWhiteboardUiSearchInput = findWhiteboardUiSearchInput(li);
    if (elm_wisePanelWhiteboardUiSearchInput && canAutoFocusSearchInput()) {
        elm_wisePanelWhiteboardUiSearchInput.focus();
    }
}


function findWhiteboardUiSearchInput(currentNode){
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
		const elm_wisePanelWhiteboardUiSearchInput = currentNode.querySelector('.wisePanelWhiteboardUiSearchInput');
		if (elm_wisePanelWhiteboardUiSearchInput) {
			return elm_wisePanelWhiteboardUiSearchInput;
		}
	}
	return null;
}


function canAutoFocusSearchInput(){
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		return false;
	}
	return true;
}