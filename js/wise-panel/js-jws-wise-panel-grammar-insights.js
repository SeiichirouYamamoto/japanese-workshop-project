const grammarInsightsActiveRecallArea = document.getElementById('grammarInsightsActiveRecallDisplayArea');
const grammarInsightsButtonsArea = document.getElementById('grammarInsightsButtonsDisplayArea');
const grammarInsightsUpsertHomeworkButton = document.getElementById('grammarInsightsHomeworkLinkDisplayAreaRightContainerUpsertHomework');
const grammarInsightsDownloadHtmlOneColumnButton = document.getElementById('grammarInsightsButtonsDisplayAreaDownloadHTMLsOneColumnButton');
const grammarInsightsDownloadHtmlTwoColumnsButton = document.getElementById('grammarInsightsButtonsDisplayAreaDownloadHTMLsTwoColumnsButton');
const grammarInsightsDownloadHtmlTwoColumnsNoZipButton = document.getElementById('grammarInsightsButtonsDisplayAreaDownloadHTMLsTwoColumnsNoZipButton');
const grammarInsightsDownloadPdfOneColumnButton = document.getElementById('grammarInsightsButtonsDisplayAreaDownloadPDFsOneColumnButton');
const grammarInsightsDownloadPdfTwoColumnsButton = document.getElementById('grammarInsightsButtonsDisplayAreaDownloadPDFsTwoColumnsButton');
const grammarInsightsDropdownSelect = document.getElementById('wisePanelGrammarInsightsViewDropDownMenuSelect');
const grammarInsightsExamplesArea = document.getElementById('grammarInsightsExamplesDisplayArea');
const grammarInsightsHomeworkArea = document.getElementById('grammarInsightsHomeworkLinkDisplayArea');
const grammarInsightsHomeworkLeftList = document.getElementById('grammarInsightsHomeworkLinkDisplayAreaLeftContainerUl');
const grammarInsightsHomeworkLeftLoading = document.getElementById('grammarInsightsHomeworkLinkDisplayAreaLeftContainerLoading');
const grammarInsightsHomeworkLinkContainer = document.getElementById('grammarInsightsHomeworkLinkDisplayAreaAContainer');
const grammarInsightsQuizLinksArea = document.getElementById('grammarInsightsQuizLinksDisplayArea');
const grammarInsightsRandomSentencesArea = document.getElementById('grammarInsightsRandomSentencesDisplayArea');
const grammarInsightsReSearchButton = document.getElementById('wisePanelGrammarInsightsViewButtonsContainerReSearchButton');

const grammarInsightsSentencesArea = document.getElementById('grammarInsightsSentencesDisplayArea');
const grammarInsightsTitlesArea = document.getElementById('grammarInsightsTitlesDisplayArea');
const grammarInsightsUserInputArea = document.getElementById('grammarInsightsUserInputDataDisplayArea');

const wiseUpdateUserInputDataScreenUpdateButton = document.getElementById('wiseUpdateUserInputDataScreenUpdateButton');


if(grammarInsightsReSearchButton !== null)
{grammarInsightsReSearchButton.addEventListener('pointerup', function() {
	switchPanelOverlaySharedContentsUiView([
		SHARED_CONTENTS_UI_VIEW.ADD,
		SHARED_CONTENTS_UI_VIEW.SELECTED
	]);
}, false);}


if(grammarInsightsDropdownSelect !== null)
{grammarInsightsDropdownSelect.addEventListener('change', async function() {
	await switchGrammarInsightsDisplayArea();
}, false);}



function handleGrammarInsightsBackToAdd() {

    switchPanelOverlaySharedContentsUiView([
        SHARED_CONTENTS_UI_VIEW.ADD,
        SHARED_CONTENTS_UI_VIEW.SELECTED
    ]);
}



if (grammarInsightsUpsertHomeworkButton !== null)
{
    grammarInsightsUpsertHomeworkButton.addEventListener('pointerup', async function () {

        grammarInsightsHomeworkLinkContainer.replaceChildren();

        let arr_grammar_information = {};

        const dayContainers = document.querySelectorAll('#grammarInsightsHomeworkLinkDisplayAreaRightContainer .grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainer');

        dayContainers.forEach(dayContainer => {
            const day = dayContainer.dataset.day;
            if (!arr_grammar_information[day]) {
                arr_grammar_information[day] = {};
            }

            const taskTypeContainers = dayContainer.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer');

            taskTypeContainers.forEach(taskTypeContainer => {
                const type = taskTypeContainer.dataset.type;
                if (!arr_grammar_information[day][type]) {
                    arr_grammar_information[day][type] = [];
                }

                const ul = taskTypeContainer.querySelector('.grammarInsightsHomeworkLinkDisplayAreaRightContainerUl');
                if (!ul) return;

                const lis = ul.querySelectorAll('li');
                lis.forEach(li => {
                    if (li.classList.contains('grammarInsightsHomeworkLinkDisplayAreaLiWithInput')) {
                        const checkbox = li.querySelector('.grammarInsightsHomeworkLinkDisplayAreaLiCheckbox');
                        if (checkbox && checkbox.checked) {
                            const uniqueCode = li.dataset.uniqueCode;
                            if (uniqueCode) {
                                arr_grammar_information[day][type].push(uniqueCode);
                            }
                        }
                    } else {
                        const uniqueCode = li.dataset.uniqueCode;
                        if (uniqueCode) {
                            arr_grammar_information[day][type].push(uniqueCode);
                        }
                    }
                });
            });
        });

        const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';

        try {

            setElementDisabled(grammarInsightsUpsertHomeworkButton, true);
            setUiLock(true);

            const payload = {
                arr_grammar_information: arr_grammar_information,
                room_unique_code: room_unique_code,
                int_selected_language: intSelectedLanguage
            };

            await postJson(
                roomUpsertHomeworkUrl,
                payload,
                10000
            );

            alert('Upserted Homework!');

        } catch (error) {
            if (error.message && error.message.includes('タイムアウト')) {
                console.error('タイムアウトが発生しました。');
                alert('タイムアウトが発生しました。');
            } else {
                console.error('Error:', error.message || error);
                alert(error.message || 'Error');
            }
        } finally {
            setElementDisabled(grammarInsightsUpsertHomeworkButton, false);
            setUiLock(false);
        }

    }, false);
}


if(grammarInsightsDownloadPdfOneColumnButton !== null)
{grammarInsightsDownloadPdfOneColumnButton.addEventListener('pointerup',
	async function (e){
	const targetsObj = {
		main : ['mainSelectedLanguage'],
		column : ['descriptionSelectedLanguage']
	};
	const isPDF = true;
	const isZIP = true;
	startGrammarInsightsDownload(isPDF, isZIP, targetsObj);
	return;
	}
	, { passive: false });
}


if(grammarInsightsDownloadPdfTwoColumnsButton !== null)
{grammarInsightsDownloadPdfTwoColumnsButton.addEventListener('pointerup',
	async function (e){
	const targetsObj = {
		main : ['mainDefault','mainSelectedLanguage'],
		column : ['descriptionDefault','descriptionSelectedLanguage']
	};
	const isPDF = true;    
	const isZIP = true;
	startGrammarInsightsDownload(isPDF, isZIP, targetsObj);
	return;
	}
	, { passive: false });
}


if(grammarInsightsDownloadHtmlOneColumnButton !== null)
{grammarInsightsDownloadHtmlOneColumnButton.addEventListener('pointerup',
	async function (e){
	const targetsObj = {
		main : ['mainSelectedLanguage'],
		column : ['descriptionSelectedLanguage']
	};
	const isPDF = false;
	const isZIP = true;
	startGrammarInsightsDownload(isPDF, isZIP, targetsObj);
	return;
	}
	, { passive: false });
}


if(grammarInsightsDownloadHtmlTwoColumnsButton !== null)
{grammarInsightsDownloadHtmlTwoColumnsButton.addEventListener('pointerup',
	async function (e){
	const targetsObj = {
		main : ['mainDefault','mainSelectedLanguage'],
		column : ['descriptionDefault','descriptionSelectedLanguage']
	};
	const isPDF = false;
	const isZIP = true;
	startGrammarInsightsDownload(isPDF, isZIP, targetsObj);
	return;
	}
	, { passive: false });
}


if(grammarInsightsDownloadHtmlTwoColumnsNoZipButton !== null)
{grammarInsightsDownloadHtmlTwoColumnsNoZipButton.addEventListener('pointerup',
	async function (e){
	const targetsObj = {
		main : ['mainDefault','mainSelectedLanguage'],
		column : ['descriptionDefault','descriptionSelectedLanguage']
	};
	const isPDF = false;
	const isZIP = false;
	startGrammarInsightsDownload(isPDF, isZIP, targetsObj);
	return;
	}
	, { passive: false });
}

document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector').forEach(button => {
	button.addEventListener('pointerup', () => {
		switchGrammarInsightsHomeworkDay(button);
	});
});


document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer').forEach(container => {
	container.addEventListener('pointerup', () => {
		switchGrammarInsightsHomeworkType(container);
	});
});



function addGrammarInsightsSelectedItem(elm){

	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiSelectedContentsListUl');
	const classNaming_li = 'panelOverlaySharedContentsUiSelectedContentsListLi';

	const int_japanese_id = escapeNumber(elm.dataset.japaneseId);
	const str_unique_code = escapeHTML(elm.dataset.uniqueCode);
	const str_japanese = escapeHTML(elm.dataset.japanese);
	const int_category_id = escapeNumber(elm.dataset.categoryId);

	const existing = document.querySelectorAll(`.panelOverlaySharedContentsUiSelectedContentsListLi[data-japanese-id="${int_japanese_id}"]`);

	if (existing.length === LENGTH_EMPTY) {	
		const elm_addLi = document.createElement('li');
		elm_addLi.classList.add(classNaming_li);
		elm_addLi.dataset.japaneseId = int_japanese_id;
		elm_addLi.dataset.uniqueCode = str_unique_code;
		elm_addLi.dataset.japanese = str_japanese;
		elm_addLi.dataset.categoryId = int_category_id;
		elm_addLi.textContent = str_japanese;
		elm_addLi.addEventListener('pointerup', function () {
            deleteElement(elm_addLi);
        }, false);
		elm_targetUl.appendChild(elm_addLi);
	}
}



async function showGrammarInsightsDisplayArea(targetDisplayArea, forceRebuild = false) {

    document.querySelectorAll('.grammarInsightsDisplayArea').forEach(elm => {
        elm.classList.add('hidden');
    });

    if (!targetDisplayArea) {
        return;
    }

    let shouldCreate = false;

    if (forceRebuild === true) {
        shouldCreate = true;
    } else {
        if (targetDisplayArea === grammarInsightsHomeworkArea) {
            if (!grammarInsightsHomeworkLeftList.hasChildNodes()) {
                shouldCreate = true;
            }
        } else {
            if (!targetDisplayArea.hasChildNodes()) {
                shouldCreate = true;
            }
        }
    }

    if (shouldCreate) {
        await buildGrammarInsightsDisplayContents();
    }

    targetDisplayArea.classList.remove('hidden');
}


async function buildGrammarInsightsDisplayContents(){

    const elmLoading = document.getElementById('grammarInsightsLoading');

    if (elmLoading !== null) {
        elmLoading.classList.remove('loading-hidden');
    }

    try {

        const int_wisePanelGrammarInsightsViewDropDownMenuSelect = escapeNumber(
            grammarInsightsDropdownSelect[grammarInsightsDropdownSelect.selectedIndex].value
        );

        const int_wisePanelGrammarInsightsViewDropDownMenuSelectAttribute = escapeNumber(
            grammarInsightsDropdownSelect[grammarInsightsDropdownSelect.selectedIndex].dataset.attribute
        );

        const elms_panelOverlaySharedContentsUiSelectedContentsListLi = document.querySelectorAll(
            '.panelOverlaySharedContentsUiSelectedContentsListLi'
        );

        if (elms_panelOverlaySharedContentsUiSelectedContentsListLi.length === LENGTH_EMPTY) {
            return;
        }

        const seenIds = new Set();
        const arr_grammar_unique_code = [];

        elms_panelOverlaySharedContentsUiSelectedContentsListLi.forEach(elm => {

            const id = elm.getAttribute('data-japanese-id');

            if (seenIds.has(id)) {
                elm.remove();
                return;
            }

            seenIds.add(id);
            arr_grammar_unique_code.push(escapeHTML(elm.dataset.uniqueCode));
        });

        switch (int_wisePanelGrammarInsightsViewDropDownMenuSelectAttribute) {

            case GRAMMAR_INSIGHTS_ATTR_POST_JSON:

                await fetchAndRenderGrammarInsights(
                    arr_grammar_unique_code,
                    int_wisePanelGrammarInsightsViewDropDownMenuSelect
                );
                break;

            case GRAMMAR_INSIGHTS_ATTR_LINKS:

                switch (int_wisePanelGrammarInsightsViewDropDownMenuSelect) {

                    case GRAMMAR_INSIGHTS_DISPLAY_CREATE_QUIZ_LINKS:

                        buildGrammarInsightsQuizLinks(
                            elms_panelOverlaySharedContentsUiSelectedContentsListLi,
                            int_wisePanelGrammarInsightsViewDropDownMenuSelect
                        );
                        break;

                    case GRAMMAR_INSIGHTS_DISPLAY_UPSERT_HOMEWORK:

                        await buildGrammarInsightsHomeworkLeftList();
                        break;

                    default:
                        return;
                }
                break;

            case GRAMMAR_INSIGHTS_ATTR_BUTTONS:

                await showGrammarInsightsDisplayArea(grammarInsightsButtonsArea, false);
                break;

            default:
                return;
        }

    } finally {

        if (elmLoading !== null) {
            elmLoading.classList.add('loading-hidden');
        }
    }
}


async function fetchAndRenderGrammarInsights(arr_grammar_unique_code, int_wisePanelGrammarInsightsViewDropDownMenuSelect){

	const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
	
	try {
	
		const payload = {
			arr_grammar_unique_code: arr_grammar_unique_code,
			room_unique_code: room_unique_code,
			value: int_wisePanelGrammarInsightsViewDropDownMenuSelect,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			roomGetGrammarInsightsUrl,
			payload,
			10000
		);

		const data = result.data;


		let targetDisplayArea = null;
		switch (int_wisePanelGrammarInsightsViewDropDownMenuSelect) {

			case GRAMMAR_INSIGHTS_DISPLAY_TITLES:
				targetDisplayArea = grammarInsightsTitlesArea;
				break;

			case GRAMMAR_INSIGHTS_DISPLAY_EXAMPLES:
				targetDisplayArea = grammarInsightsExamplesArea;
				break;

			case GRAMMAR_INSIGHTS_DISPLAY_USER_INPUT:
				targetDisplayArea = grammarInsightsUserInputArea;
				break;

			case GRAMMAR_INSIGHTS_DISPLAY_SENTENCES:
				targetDisplayArea = grammarInsightsSentencesArea;
				break;

			case GRAMMAR_INSIGHTS_DISPLAY_RANDOM_SENTENCES:
				targetDisplayArea = grammarInsightsRandomSentencesArea;
				break;

			case GRAMMAR_INSIGHTS_DISPLAY_ACTIVE_RECALL:
				targetDisplayArea = grammarInsightsActiveRecallArea;
				break;

			default:
				return;
		}

		targetDisplayArea.replaceChildren();

		for (let i = INDEX_FIRST; i < data.length; i++) {

			if (data[i] === null) {
				continue;
			}

			const elm_addDiv = document.createElement('div');
			elm_addDiv.classList.add('grammarInsightsDisplayAreaDiv');

			const elm_header = document.createElement('h2');
			elm_header.classList.add('grammarInsightsDisplayAreaHeader');
			elm_header.textContent = escapeHTML(data[i].title);

			const elm_addUl = document.createElement('ul');
			elm_addUl.classList.add('grammarInsightsDisplayAreaUl');

			let str_copyContents = '';

			let showCopyButton = false;
			let showChartButtonOneGrammar = false;
			let showChartButtonMultiGrammars = false;

			const arr = Array.isArray(data[i].array) ? data[i].array : [];

			for (let j = INDEX_FIRST; j < arr.length; j++) {

				let str_addCopyContents = '';
				let elm_addLi;

				switch (int_wisePanelGrammarInsightsViewDropDownMenuSelect) {

					case GRAMMAR_INSIGHTS_DISPLAY_TITLES: {
						const { elm_addLi: li, copyText } = buildGrammarInsightListItem({
							jsonItem: arr[j],
							isExample: false,
							includeButton: true,
							addUniqueCodeToLi: false,
							showRadioContainer: true,
							i,
							j
						});
						elm_addLi = li;
						str_addCopyContents = copyText;

						showCopyButton = true;
						showChartButtonOneGrammar = false;
						showChartButtonMultiGrammars = false;
						break;
					}

					case GRAMMAR_INSIGHTS_DISPLAY_EXAMPLES: {
						const { elm_addLi: li, copyText } = buildGrammarInsightListItem({
							jsonItem: arr[j],
							isExample: true,
							includeButton: true,
							addUniqueCodeToLi: false,
							showRadioContainer: true,
							i,
							j
						});
						elm_addLi = li;
						str_addCopyContents = copyText;

						showCopyButton = true;
						showChartButtonOneGrammar = false;
						showChartButtonMultiGrammars = false;
						break;
					}

					case GRAMMAR_INSIGHTS_DISPLAY_SENTENCES:
					case GRAMMAR_INSIGHTS_DISPLAY_RANDOM_SENTENCES: {
						const { elm_addLi: li, copyText } = buildGrammarInsightSentenceItem({
							jsonItem: arr[j]
						});

						elm_addLi = li;
						str_addCopyContents = copyText;

						showCopyButton = true;
						showChartButtonOneGrammar = true;
						showChartButtonMultiGrammars =
							int_wisePanelGrammarInsightsViewDropDownMenuSelect === GRAMMAR_INSIGHTS_DISPLAY_RANDOM_SENTENCES;
						break;
					}

					default: {
						const { elm_addLi: li, copyText } = buildGrammarInsightDefaultItem({
							jsonItem: arr[j]
						});
						elm_addLi = li;
						str_addCopyContents = copyText;

						showCopyButton = false;
						showChartButtonOneGrammar = false;
						showChartButtonMultiGrammars = false;
					}
				}

				if (j < arr.length - LAST_INDEX_OFFSET) {
					str_addCopyContents += '\n';
				}

				str_copyContents += str_addCopyContents;
				elm_addUl.appendChild(elm_addLi);
			}

			const { elm_addDivButtonsContainer, elm_addUl: updatedUl } = buildGrammarInsightsActionButtons({
				str_copyContents,
				showCopyButton,
				showChartButtonOneGrammar,
				showChartButtonMultiGrammars,
				int_wisePanelGrammarInsightsViewDropDownMenuSelect,
				jsonItem: data[i],
				elm_addUl,
				i
			});

			elm_addDiv.appendChild(elm_header);
			elm_addDiv.appendChild(elm_addDivButtonsContainer);
			elm_addDiv.appendChild(updatedUl);

			targetDisplayArea.appendChild(elm_addDiv);

			applyFontSizeVariation(
				['wiseUiFontSizeTarget'],
				'wiseUiFontSizeTargetVariationDifference'
			);
		}

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
	}

}

function buildGrammarInsightListItem({
	jsonItem,
	isExample = false,
	includeButton = true,
	addUniqueCodeToLi = false,
	showRadioContainer = true,
	i,
	j
}) {

	const elm_addLi = document.createElement('li');
	elm_addLi.classList.add('grammarInsightsDisplayAreaLi', 'flexDirectionColumn');

	if (addUniqueCodeToLi) {
		elm_addLi.dataset.uniqueCode = escapeHTML(jsonItem['grammarUniqueCode']);
	}

	const elm_addLiContainer = document.createElement('div');
	elm_addLiContainer.classList.add('grammarInsightsDisplayAreaLiContainer');

	if (includeButton) {
		const elm_addLiButton = document.createElement('button');
		elm_addLiButton.classList.add('grammarOutlineLabelButtonExplanation');
		elm_addLiButton.textContent = 'ℹ️';
		elm_addLiButton.dataset.japaneseId = escapeHTML(jsonItem['japaneseId']);
		elm_addLiButton.dataset.uniqueCode = escapeHTML(jsonItem['grammarUniqueCode']);
		elm_addLiButton.dataset.japanese = escapeHTML(jsonItem['japanese']);
		elm_addLiButton.dataset.kana = escapeHTML(jsonItem['kana']);
		elm_addLiButton.dataset.categoryId = escapeHTML(jsonItem['categoryId']);
		elm_addLiContainer.appendChild(elm_addLiButton);
	}

	const elm_addLiText = document.createElement('div');
	elm_addLiText.classList.add('grammarInsightsDisplayAreaLiText', 'wiseUiFontSizeTarget');
	elm_addLiText.textContent = escapeHTML(isExample ? jsonItem['rootExample'] : jsonItem['rootText']);
	elm_addLiContainer.appendChild(elm_addLiText);

	elm_addLi.appendChild(elm_addLiContainer);

	if(showRadioContainer){
		const elm_addLiHtml = document.createElement('div');
		elm_addLiHtml.innerHTML = jsonItem['html'];
		elm_addLi.appendChild(elm_addLiHtml);
	}

	return {
		elm_addLi,
		copyText: escapeHTML(isExample ? jsonItem['rootExample'] : jsonItem['rootText'])
	};
}

function buildGrammarInsightSentenceItem({jsonItem}){

	const elm_addLi = document.createElement('li');
	elm_addLi.classList.add('grammarInsightsDisplayAreaLi');

	let sentenceUniqueCode = escapeHTML(jsonItem['sentenceUniqueCode']);
	

	let elm_addLiDivButtonsContainer = document.createElement('div');
	elm_addLiDivButtonsContainer.classList.add('grammarInsightsDisplayAreaLiDivButtonsContainer');

	let elm_addButtonSortingQuiz = document.createElement('button');
	elm_addButtonSortingQuiz.classList.add('grammarInsightsDisplayAreaLiButton');
	elm_addButtonSortingQuiz.dataset.uniqueCode = sentenceUniqueCode;
	// マジックナンバー
	elm_addButtonSortingQuiz.textContent = 'Sorting Quiz';
	elm_addButtonSortingQuiz.addEventListener('pointerup',
		function (e){
			let url = pageSortingQuizFullscreenUrl;
			let urlWithParams = `${url}/?${KEY_SENTENCE_UNIQUE_CODE}=${encodeURIComponent(sentenceUniqueCode)}&advance_stage=1`;
			window.open(urlWithParams, '_blank', 'noopener');
		}
	, { passive: false });
	elm_addLiDivButtonsContainer.appendChild(elm_addButtonSortingQuiz);

	
	let elm_addButtonDisplayJapaneseText = document.createElement('button');
	elm_addButtonDisplayJapaneseText.classList.add('grammarInsightsDisplayAreaLiButton');
	// マジックナンバー
	elm_addButtonDisplayJapaneseText.textContent = 'Answer';
	elm_addButtonDisplayJapaneseText.addEventListener('pointerup',
		function (e){
			const li = this.closest('.grammarInsightsDisplayAreaLi');
			if (!li) {
				return;
			}

			const target = li.querySelector('.grammarInsightsDisplayAreaLiTextJapaneseText');
			if (!target) {
				return;
			}

			target.classList.toggle('hidden');
		}
	, { passive: false });
	elm_addLiDivButtonsContainer.appendChild(elm_addButtonDisplayJapaneseText);
	
	elm_addLi.appendChild(elm_addLiDivButtonsContainer);
	
	let elm_addLiDivTextsContainer = document.createElement('div');
	elm_addLiDivTextsContainer.classList.add('grammarInsightsDisplayAreaLiDivTextsContainer');

	let elm_addLiTextForeignLanguageText = document.createElement('div');
	elm_addLiTextForeignLanguageText.classList.add('grammarInsightsDisplayAreaLiText','grammarInsightsDisplayAreaLiTextForeignLanguageText', 'wiseUiFontSizeTarget');
	elm_addLiTextForeignLanguageText.textContent = escapeHTML(jsonItem['foreignLanguageText']);
	elm_addLiTextForeignLanguageText.setAttribute('contenteditable', 'true');
	elm_addLiDivTextsContainer.appendChild(elm_addLiTextForeignLanguageText);

	let elm_addLiTextJapaneseText = document.createElement('div');
	elm_addLiTextJapaneseText.classList.add('grammarInsightsDisplayAreaLiText','grammarInsightsDisplayAreaLiTextJapaneseText', 'wiseUiFontSizeTarget', 'hidden', 'animationSlideIn');
	elm_addLiTextJapaneseText.textContent = escapeHTML(jsonItem['japaneseText']);
	elm_addLiTextJapaneseText.setAttribute('contenteditable', 'true');
	elm_addLiDivTextsContainer.appendChild(elm_addLiTextJapaneseText);

	elm_addLi.appendChild(elm_addLiDivTextsContainer);

	return {
		elm_addLi,
		copyText: escapeHTML(jsonItem['foreignLanguageText'])
	};
}

function buildGrammarInsightDefaultItem({jsonItem}){

	const elm_addLi = document.createElement('li');
	elm_addLi.classList.add('grammarInsightsDisplayAreaLiTextsContaier', 'flexDirectionColumn');

	const lines = escapeHTML(jsonItem['foreignLanguageText']).split(/\r?\n/);

	lines.forEach((line, index) => {
		if (isEmptyValue(line.trim())) return;
		const lineDiv = document.createElement('div');
		lineDiv.classList.add('grammarInsightsDisplayAreaLiText', 'wiseUiFontSizeTarget');
		lineDiv.setAttribute('contenteditable', 'true');
		lineDiv.textContent = line;
		elm_addLi.appendChild(lineDiv);
	});

	return {
		elm_addLi,
		copyText: ''
	};
}

function buildGrammarInsightsActionButtons({
	str_copyContents,
	showCopyButton,
	showChartButtonOneGrammar,
	showChartButtonMultiGrammars,
	int_wisePanelGrammarInsightsViewDropDownMenuSelect,
	jsonItem,
	elm_addUl,
	i
}) {
	const elm_addDivButtonsContainer = document.createElement('div');
	elm_addDivButtonsContainer.classList.add('grammarInsightsDisplayAreaButtonsContainer');

	if (!isEmptyValue(str_copyContents) && showCopyButton) {
		const copyButton = document.createElement('button');
		copyButton.classList.add('grammarInsightsDisplayAreaButtonCopyContents', 'grammarInsightsDisplayAreaButton');
		copyButton.textContent = 'Copy';

		copyButton.addEventListener('pointerup', function () {
			let currentNode = this.parentNode;
			while (currentNode && !currentNode.classList.contains('grammarInsightsDisplayAreaDiv')) {
				currentNode = currentNode.parentNode;
			}

			let contentsToCopy = str_copyContents;
			if (currentNode) {
				const headerElement = currentNode.querySelector('.grammarInsightsDisplayAreaHeader');
				if (headerElement) {
					contentsToCopy = headerElement.textContent.trim() + '\n\n' + contentsToCopy;
				}
			}

			const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

			lastCreatedMovableContainer = createTextAreaContainer(contentsToCopy, point.x, point.y);
			updateTextareaContainerSize(lastCreatedMovableContainer.querySelector('.innerContainerTextArea'), contentsToCopy);

			advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, contentsToCopy);
			
		}, { passive: false });

		elm_addDivButtonsContainer.appendChild(copyButton);
	}

	if (!isEmptyValue(str_copyContents) && showChartButtonMultiGrammars) {
		const chartButton = document.createElement('button');
		chartButton.classList.add('grammarInsightsDisplayAreaButtonToChart', 'grammarInsightsDisplayAreaButton');
		chartButton.textContent = 'Chart';
		chartButton.addEventListener('pointerup', () => {
			let str_x = '日本語', str_y = str_copyContents;

			const panelId = chartOpenButton.dataset.panelId;
			if (!panelId) {
				return;
			}

			openWiseChartScreens(str_x, str_y, WISE_CHART_SUBMIT_MODE.CREATE);
			openWisePanelPositionSelectUi(panelId, true);
		}, { passive: false });
		elm_addDivButtonsContainer.appendChild(chartButton);
	}

	const str_grammarUniqueCode = escapeHTML(jsonItem['grammarUniqueCode']);

	if (!isEmptyValue(str_grammarUniqueCode)) {
		if (showChartButtonOneGrammar) {
			const chartButton = document.createElement('button');
			chartButton.classList.add('grammarInsightsDisplayAreaButtonToChart', 'grammarInsightsDisplayAreaButton');
			chartButton.textContent = 'Chart';
			chartButton.addEventListener('pointerup', () => {
				createChartForRegisteredSentences(str_grammarUniqueCode);
			}, { passive: false });
			elm_addDivButtonsContainer.appendChild(chartButton);
		}
	}

	const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';

	if (int_wisePanelGrammarInsightsViewDropDownMenuSelect === GRAMMAR_INSIGHTS_DISPLAY_USER_INPUT) {
		const elm_addLi = document.createElement('li');
		elm_addLi.classList.add('grammarInsightsDisplayAreaLi');

		const textarea = document.createElement('textarea');
		textarea.classList.add('grammarInsightsDisplayAreaLiText', 'grammarInsightsDisplayAreaLiTextAreaUserInput', 'wiseUiFontSizeTarget');
		textarea.style.width = '100%';
		textarea.style.marginTop = '10px';
		textarea.style.resize = 'none';
		textarea.style.overflow = 'hidden';
		textarea.rows = 1;

		const sendButton = document.createElement('button');
		sendButton.textContent = '送信';
		sendButton.style.marginTop = '5px';
		sendButton.style.display = 'block';

		
		sendButton.addEventListener('pointerup', async function () {
		const inputValue = textarea.value.trim();
		if (inputValue === '') {
			alert('入力が空です。');
			return;
		}

		try {

			setElementDisabled(sendButton, true);
			setUiLock(true);

			const payload = {
				str_textareaValue: inputValue,
				grammar_unique_code: str_grammarUniqueCode,
				room_unique_code: room_unique_code,
				int_selected_language: intSelectedLanguage
			};

			await postJson(
				roomUploadUserInputDataUrl,
				payload,
				10000
			);

			let grammarDiv = sendButton.closest('.grammarInsightsDisplayAreaDiv');
			let container = grammarDiv.querySelector('.grammarInsightsDisplayAreaLiTextsContaier');

			if (!container) {
				container = document.createElement('div');
				container.classList.add('grammarInsightsDisplayAreaLiTextsContaier', 'flexDirectionColumn');
				elm_addUl.insertBefore(container, elm_addUl.firstChild);
			}

			const lines = escapeHTML(inputValue).split(/\r?\n/);
			lines.forEach(line => {
				if (isEmptyValue(line.trim())) return;

				const lineDiv = document.createElement('div');
				lineDiv.classList.add('grammarInsightsDisplayAreaLiText', 'wiseUiFontSizeTarget');
				lineDiv.textContent = line;
				container.appendChild(lineDiv);
			});

			textarea.value = '';

			applyFontSizeVariation(
				['wiseUiFontSizeTarget'],
				'wiseUiFontSizeTargetVariationDifference'
			);

		} catch (error) {
			if (error.message && error.message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
				alert('タイムアウトが発生しました。');
			} else {
				console.error('Error:', error.message || error);
				alert(error.message || '送信に失敗しました。');
			}
		} finally {
			setElementDisabled(sendButton, false);
			setUiLock(false);
		}
	});

		const showOverlayButton = document.createElement('button');
		showOverlayButton.textContent = '編集';
		showOverlayButton.style.marginTop = '5px';
		showOverlayButton.style.display = 'block';

		showOverlayButton.addEventListener('pointerup', async function () {
			const overlay = document.getElementById('wiseUpdateUserInputDataScreenOverlay');
			if (!overlay) {
				console.error('wiseUpdateUserInputDataScreenOverlay が見つかりません。');
				return;
			}
			overlay.classList.add('overlay-on');

			const payload = {
				arr_grammar_unique_code: [str_grammarUniqueCode],
				room_unique_code: room_unique_code,
				value: GRAMMAR_INSIGHTS_DISPLAY_USER_INPUT,
				int_selected_language: intSelectedLanguage
			};

			const result = await postJson(
				roomGetGrammarInsightsUrl,
				payload,
				10000
			);

			const data = result.data;

			const ul = document.getElementById('wiseUpdateUserInputDataList');
			if (!ul) {
				console.error('wiseUpdateUserInputDataList が見つかりません。');
				return;
			}

			ul.replaceChildren();

			const grammar = Array.isArray(data)
				? data.find(item => item?.grammarUniqueCode === str_grammarUniqueCode)
				: null;

			const arr = Array.isArray(grammar?.array) ? grammar.array : [];

			arr.forEach(item => {

				const userInputDataId = item.userInputDataId ?? 0;
				const textValue = item.inputData ?? '';

				const li = document.createElement('li');
				li.classList.add('wiseUpdateUserInputDataListItem');
				li.dataset.userInputDataId = String(userInputDataId);

				const textarea = document.createElement('textarea');
				textarea.classList.add('wiseUpdateUserInputDataTextarea');
				textarea.style.width = '100%';
				textarea.style.resize = 'none';
				textarea.style.overflow = 'hidden';
				textarea.rows = 1;

				textarea.dataset.userInputDataId = String(userInputDataId);
				textarea.dataset.edited = 'false';

				textarea.value = textValue;

				textarea.addEventListener('input', function () {
					if (this.dataset.edited !== 'true') {
						this.dataset.edited = 'true';
					}
				});

				li.appendChild(textarea);
				ul.appendChild(li);
				updateTextareaSize(textarea, textValue);
			});

		});


		elm_addLi.appendChild(textarea);
		elm_addLi.appendChild(sendButton);
		elm_addLi.appendChild(showOverlayButton);
		elm_addUl.appendChild(elm_addLi);
	}

	return {
		elm_addDivButtonsContainer,
		elm_addUl
	};
}


async function buildGrammarInsightsHomeworkLeftList() {

    grammarInsightsHomeworkLeftList.replaceChildren();
    grammarInsightsHomeworkLeftLoading.classList.remove('loading-hidden');

    const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';

    try {

        if (isEmptyValue(room_unique_code) || room_unique_code === 'default') {
            return;
        }

        const arr_grammar_unique_code = [];
        const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
        lis.forEach(li => {
            const str_grammarUniqueCode = escapeHTML(li.dataset.uniqueCode);
            arr_grammar_unique_code.push(str_grammarUniqueCode);
        });

        const payload = {
            room_unique_code: room_unique_code,
            arr_grammar_unique_code: arr_grammar_unique_code,
            int_selected_language: intSelectedLanguage,
        };

        const result = await postJson(
            roomGetGrammarInsightsHomeworkItemsUrl,
            payload,
            10000
        );

        const data = result.data;

        if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
            const elm_addLi = document.createElement('li');
            elm_addLi.classList.add('resultEmpty');
            elm_addLi.textContent = '検索結果 0件';
            grammarInsightsHomeworkLeftList.appendChild(elm_addLi);
            return;
        }

        insertGrammarInsightsHomeworkLeftListItems(data);
        updateGrammarInsightsHomeworkVisibility();

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            console.error('Error:', error.message || error);
            alert(error.message || 'Error');
        }

    } finally {

        grammarInsightsHomeworkLeftLoading.classList.add('loading-hidden');
    }
}


function insertGrammarInsightsHomeworkLeftListItems(arrLiInfo) {

	arrLiInfo.forEach(info => {

		let str_japanese = escapeHTML(info.japanese);
		let str_uniqueCode = escapeHTML(info.uniqueCode);
		let inputData = escapeNumber(info.inputData);
		let activeRecall = escapeNumber(info.activeRecall);
		let registeredSentences = escapeNumber(info.registeredSentences);

		let elm_addLi = document.createElement('li');
		elm_addLi.classList.add('grammarInsightsHomeworkLinkDisplayAreaLeftContainerLi', 'grammarInsightsHomeworkLinkDisplayAreaLiResetTarget', 'grammarInsightsHomeworkLinkDisplayAreaLi');
		elm_addLi.textContent = str_japanese;
		elm_addLi.dataset.inputData = inputData;
		elm_addLi.dataset.activeRecall = activeRecall;
		elm_addLi.dataset.registeredSentences = registeredSentences;
		elm_addLi.addEventListener('pointerup',function (e){
			
			if (this.classList.contains('homeworkHidden')) return;

			const selectedUl = document.querySelector('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer.selected .grammarInsightsHomeworkLinkDisplayAreaRightContainerUl');

			if (!selectedUl || ['inflection', 'plainform'].includes(selectedUl.dataset.type)) {
				return;
			}
			
			let existing = selectedUl.querySelectorAll(`.grammarInsightsHomeworkLinkDisplayAreaRightContainerLi[data-unique-code="${str_uniqueCode}"]`);
			
			if (existing.length === LENGTH_EMPTY) {	
				let elm_addLi_for_rightContainer = document.createElement('li');
				elm_addLi_for_rightContainer.classList.add('grammarInsightsHomeworkLinkDisplayAreaRightContainerLi', 'grammarInsightsHomeworkLinkDisplayAreaLiResetTarget', 'grammarInsightsHomeworkLinkDisplayAreaLi');
				elm_addLi_for_rightContainer.dataset.uniqueCode = str_uniqueCode;
				elm_addLi_for_rightContainer.textContent = str_japanese;

				elm_addLi_for_rightContainer.addEventListener('pointerup',function (e){
					e.preventDefault();
					this.remove();
				}
				, { passive: false });

				selectedUl.appendChild(elm_addLi_for_rightContainer);
			}

		}
		, { passive: false });

		grammarInsightsHomeworkLeftList.appendChild(elm_addLi);
	});
}

function updateGrammarInsightsHomeworkVisibility() {
	const selectedContainer = document.querySelector('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer.selected');
	if (!selectedContainer) return;

	const type = selectedContainer.dataset.type;
	const elms = document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaLeftContainerLi');

	elms.forEach(elm => {
		elm.classList.remove('homeworkHidden');

		if (type === 'inputData' && Number(elm.dataset.inputData) === FLAG_FALSE) {
			elm.classList.add('homeworkHidden');
		}

		if (type === 'activeRecall' && Number(elm.dataset.activeRecall) === FLAG_FALSE) {
			elm.classList.add('homeworkHidden');
		}

		if (
			(
				type === 'registeredSentences' ||
				type === 'registeredSentencesAdvanced' ||
				type === 'randomSentences' ||
				type === 'randomSentencesAdvanced' ||
				type === 'japaneseParticleQuiz' ||
				type === 'sortingQuiz'
			)
			&&
			Number(elm.dataset.registeredSentences) === FLAG_FALSE
		) {
			elm.classList.add('homeworkHidden');
		}
	});
}


function buildGrammarInsightsQuizLinks(elms_panelOverlaySharedContentsUiSelectedContentsListLi, int_wisePanelGrammarInsightsViewDropDownMenuSelect){
	
	grammarInsightsQuizLinksArea.replaceChildren();

	let params = '';

	for (let i = INDEX_FIRST; i < elms_panelOverlaySharedContentsUiSelectedContentsListLi.length; i++) {
		const elm = elms_panelOverlaySharedContentsUiSelectedContentsListLi[i];
		const grammarUniqueCode = escapeHTML(elm.dataset.uniqueCode);
		params = params + '&arr_grammar_unique_code[]=' + grammarUniqueCode;
	}

	let elm_quizContainer = document.createElement('div');
	elm_quizContainer.classList.add('grammarOutlineCreateAreaQuizContainer');

	if(params.length >= 2048){
		elm_quizContainer = document.createElement('div');
		// マジックナンバー
		elm_quizContainer.textContent = '選択された文法が多すぎます';
	}
	else{

		const quizUrlMap = {
			'japanese-particle-quiz': pageJapaneseParticleQuizUrl,
			'word-inflection-quiz': pageWordInflectionQuizUrl,
			'grammar-quiz': pageGrammarQuizUrl,
			'plainform-quiz': pagePlainformQuizUrl,
			'sorting-quiz': pageSortingQuizUrl
		};

		Object.entries(quizUrlMap).forEach(([quizKey, url]) => {
			const baseUrl = url;
			const urlWithParams = baseUrl + '/?createFromArray=1' + params;

			const elm_button = document.createElement('a');
			elm_button.classList.add('grammarOutlineCreateAreaAddContents', 'grammarInsightsDisplayAreaLiText', 'wiseUiFontSizeTarget');
			elm_button.href = urlWithParams;
			elm_button.target = '_blank';
			elm_button.rel = 'noopener noreferrer';
			elm_button.textContent = quizKey;

			elm_quizContainer.appendChild(elm_button);
		});

	}

	grammarInsightsQuizLinksArea.appendChild(elm_quizContainer);
	applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);
	
}


function switchGrammarInsightsHomeworkDay(button) {
	const selectedDay = button.dataset.day;

	document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector').forEach(btn => {
		btn.classList.remove('selected');
	});
	button.classList.add('selected');

	document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainer').forEach(container => {
		container.classList.add('hidden');
		container.classList.remove('selected');
	});

	document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer').forEach(container => {
		container.classList.remove('selected');
	});

	const target = document.querySelector(`.grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainer[data-day="${selectedDay}"]`);
	if (target) {
		target.classList.remove('hidden');
		target.classList.add('selected');
		target.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer[data-type="inputData"]').forEach(container => {
			container.classList.add('selected');
		});
	}
	updateGrammarInsightsHomeworkVisibility();
}


function switchGrammarInsightsHomeworkType(container) {
	const selectedType = container.dataset.type;

	document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer').forEach(ctn => {
		ctn.classList.remove('selected');
	});
	container.classList.add('selected');
	updateGrammarInsightsHomeworkVisibility();
}






function executeGrammarInsightsContentSearch() {

	const str_searchWord = escapeHTML(panelOverlaySharedContentsUiSearchInput.value);
	const elm_targetUl = panelOverlaySharedContentsUiList;
	const elm_targetLoading = panelOverlaySharedContentsUiLoading;
	const arr_classNaming_li = ['panelOverlaySharedContentsUiLi'];
	const int_matching_type = MATCHING_TYPE_PARTIAL;
	const int_category_id = SELECT_ALL;
	const int_sub_category_id = SELECT_ALL;
	const int_learningScope = LEARNING_SCOPE_SELECT_ALL;

	searchContentList(
		SEARCH_SCOPE_WISE_CATEGORY,
		str_searchWord,
		elm_targetUl,
		elm_targetLoading,
		arr_classNaming_li,
		int_matching_type,
		int_category_id,
		int_sub_category_id,
		int_learningScope
	);
}

function executeGrammarInsightsContentCategoryList() {
	
	const str_searchWord = '';
	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiFromCategoryUl');
	const elm_targetLoading = panelOverlaySharedContentsUiCategoryLoading;
	const arr_classNaming_li = [
		'panelOverlaySharedContentsUiLi',
		'panelOverlaySharedContentsUiLiFromCategory'
	];
	const int_matching_type = MATCHING_TYPE_PARTIAL;
	const int_category_id = SELECT_ALL;
	const int_sub_category_id = escapeNumber(panelOverlaySharedContentsUiCategorySelect[panelOverlaySharedContentsUiCategorySelect.selectedIndex].value);
	const int_learningScope = LEARNING_SCOPE_ALREADY_LEARNED;

	searchContentList(
		SEARCH_SCOPE_WISE_SUB_CATEGORY,
		str_searchWord,
		elm_targetUl,
		elm_targetLoading,
		arr_classNaming_li,
		int_matching_type,
		int_category_id,
		int_sub_category_id,
		int_learningScope
	);
}

function executeGrammarInsightsContentBookmarkList() {
	
	const elm_targetUl = document.getElementById('panelOverlaySharedContentsUiFromBookmarkUl');
	const elm_targetLoading = panelOverlaySharedContentsUiBookmarkLoading;
	const arr_classNaming_li = [
		'panelOverlaySharedContentsUiLi',
		'panelOverlaySharedContentsUiLiFromBookmark'
	];
	const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
	const int_bookmark_filter = escapeNumber(panelOverlaySharedContentsUiBookmarkSelect[panelOverlaySharedContentsUiBookmarkSelect.selectedIndex].value);

	searchContentListByBookmark(
		SEARCH_SCOPE_BOOKMARK_ROOM,
		elm_targetUl,
		elm_targetLoading,
		arr_classNaming_li,
		room_unique_code,
		int_bookmark_filter,
	);
}


async function startGrammarInsightsDownload(isPDF, isZIP, targetsObj){
	let elms_panelOverlaySharedContentsUiSelectedContentsListLi = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');
	if (elms_panelOverlaySharedContentsUiSelectedContentsListLi.length === LENGTH_EMPTY) {
		alert(MSG_NO_SELECTION_ARRAY[intSelectedLanguage]);
		return;
	}

	let isConfirmed = window.confirm(MSG_DOWNLOAD_CONFIRM[intSelectedLanguage]);
	if(isConfirmed) {
		const scrollPosition = window.scrollY;
		for (let i = INDEX_FIRST; i < elms_panelOverlaySharedContentsUiSelectedContentsListLi.length; i++) {
			const elm = elms_panelOverlaySharedContentsUiSelectedContentsListLi[i];
			const int_japanese_id = escapeNumber(elm.dataset.japaneseId);
			try {
				await downloadGrammarView(isPDF, isZIP, false, int_japanese_id, targetsObj);
			} catch (error) {
				console.error('Error during download:', error);
			}
		}
		window.scrollTo(0, scrollPosition);
	}
}


if(wiseUpdateUserInputDataScreenUpdateButton !== null)
{
	
	wiseUpdateUserInputDataScreenUpdateButton.addEventListener('pointerup', async function () {
	
		const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
	
		try {
	
			setElementDisabled(wiseUpdateUserInputDataScreenUpdateButton, true);
	
			if (isEmptyValue(room_unique_code) || room_unique_code === 'default') {
				alert('room を選択してください。');
				return;
			}
	
			const textareas = document.querySelectorAll('.wiseUpdateUserInputDataTextarea');
			const arr_user_input_update = [];
	
			textareas.forEach(textarea => {
	
				if (textarea.dataset.edited !== 'true') {
					return;
				}
	
				const userInputDataId = Number(textarea.dataset.userInputDataId ?? 0);
				const value = textarea.value ?? '';
	
				if (userInputDataId < 1) {
					return;
				}
	
				arr_user_input_update.push({
					userInputDataId: userInputDataId,
					value: value,
				});
			});
	
			if (arr_user_input_update.length === 0) {
				alert('変更がありません。');
				return;
			}
	
			const payload = {
				room_unique_code: room_unique_code,
				arr_user_input_update: arr_user_input_update,
				int_selected_language: intSelectedLanguage,
			};
	
			const result = await postJson(
				roomUpdateUserInputDataUrl,
				payload,
				10000
			);
	
			const data = result.data;
	
			await showGrammarInsightsDisplayArea(grammarInsightsUserInputArea, true);
	
			textareas.forEach(textarea => {
				if (textarea.dataset.edited === 'true') {
					textarea.dataset.edited = 'false';
				}
			});
	
			alert('Success!');
	
		} catch (error) {
	
			if (error.message && error.message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
			} else {
				console.error('Error:', error.message || error);
				alert(error.message || 'Error');
			}
	
		} finally {
			setElementDisabled(wiseUpdateUserInputDataScreenUpdateButton, false);
		}
	});
}


function getCurrentGrammarInsightsSnapshot() {

    const lis = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');

    const selectedUniqueCodes = Array.from(lis).map(li => {
        return escapeHTML(li.dataset.uniqueCode);
    });

    return {
        roomUniqueCode: escapeHTML(wiseSetupRoomSelect?.value || 'default'),
        selectedUniqueCodes: selectedUniqueCodes
    };
}

function isSameGrammarInsightsSnapshot(a, b) {

    if (a === null || b === null) {
        return false;
    }

    if (a.roomUniqueCode !== b.roomUniqueCode) {
        return false;
    }

    if (a.selectedUniqueCodes.length !== b.selectedUniqueCodes.length) {
        return false;
    }

    for (let i = 0; i < a.selectedUniqueCodes.length; i++) {
        if (a.selectedUniqueCodes[i] !== b.selectedUniqueCodes[i]) {
            return false;
        }
    }

    return true;
}

async function openGrammarInsightsPanelWithDiffCheck() {

    const currentSnapshot = getCurrentGrammarInsightsSnapshot();

    const hasSelection = currentSnapshot.selectedUniqueCodes.length > 0;

    if (!hasSelection) {

        switchPanelOverlaySharedContentsUiView([
            SHARED_CONTENTS_UI_VIEW.ADD,
            SHARED_CONTENTS_UI_VIEW.SELECTED
        ]);

        return;
    }

    const isSame = isSameGrammarInsightsSnapshot(
        grammarInsightsDisplaySnapshot,
        currentSnapshot
    );

    if (!isSame) {
        resetGrammarInsightsDisplayArea();
        grammarInsightsDisplaySnapshot = currentSnapshot;
    }

    await switchGrammarInsightsDisplayArea();
}