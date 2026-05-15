const grammarExplanationUiHistoryCloseButton = document.getElementById('wisePanelGrammarExplanationUiHistoryCloseButton');
const downloadGrammarPdfOneColumnButton = document.getElementById('downloadGrammarViewPDFOneColumn');
const downloadGrammarPdfTwoColumnsButton = document.getElementById('downloadGrammarViewPDFTwoColumns');
const generateExampleChartButton = document.getElementById('generateExampleChart');
const grammarExplanationBackButton = document.getElementById('wisePanelGrammarExplanationViewBackButton');
const grammarExplanationDisplayArea = document.getElementById('wisePanelGrammarExplanationViewMainContentArea');
const grammarExplanationMainContentContainer = document.getElementById('wisePanelGrammarExplanationViewMainContentContainer');
const grammarExplanationForwardButton = document.getElementById('wisePanelGrammarExplanationViewForwardButton');
const grammarExplanationHistoryList = document.getElementById('wisePanelGrammarExplanationUiHistoryList');
const grammarExplanationLoading = document.getElementById('wisePanelGrammarExplanationViewLoading');
const grammarExplanationOpenButton = document.getElementById('wisePanelGrammarExplanationViewOpener');
const grammarExplanationReloadButton = document.getElementById('wisePanelGrammarExplanationViewReloadButton');
const reloadGrammarExplanationButton = document.getElementById('reloadGrammarExplanation');
const showGrammarHistoryButton = document.getElementById('showGrammarExplanationHistory');

if(grammarExplanationUiHistoryCloseButton !== null)
{grammarExplanationUiHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(grammarExplanationPanel, 'target', grammarExplanationUiHistory);
}, false);}

if(grammarExplanationReloadButton !== null)
{grammarExplanationReloadButton.addEventListener('pointerup', function() {
	displayCurrentStateGrammarExplanation(currentGrammarExplanationIndex, true);
}, false);}

if(grammarExplanationHandle !== null){
	grammarExplanationHandle.addEventListener('touchmove', (e) => {
		preventDefaultOnTouchMove(e);
	}, { passive: false });
}

if(grammarExplanationBackButton !== null)
{grammarExplanationBackButton.addEventListener('pointerup', async function() {
	if(grammarExplanationHistory.length === LENGTH_EMPTY)return;
	if (currentGrammarExplanationIndex > INDEX_FIRST) {
		currentGrammarExplanationIndex--;
		displayCurrentStateGrammarExplanation(currentGrammarExplanationIndex, false);
		resetAppearanceLayoutState(appearanceLayoutState);
	}
}, false);}
if(grammarExplanationForwardButton !== null)
{grammarExplanationForwardButton.addEventListener('pointerup', async function() {
	if (currentGrammarExplanationIndex < grammarExplanationHistory.length - LAST_INDEX_OFFSET) {
		currentGrammarExplanationIndex++;
		displayCurrentStateGrammarExplanation(currentGrammarExplanationIndex, true);
		resetAppearanceLayoutState(appearanceLayoutState);
	}
}, false);}

if (reloadGrammarExplanationButton !== null) {
    reloadGrammarExplanationButton.addEventListener('pointerup', async function() {
        if (reloadGrammarExplanationButton._lock) return;
        reloadGrammarExplanationButton._lock = true;

        try {
            const currentData = grammarExplanationHistory[currentGrammarExplanationIndex];
            if (!currentData || !currentData.grammarUniqueCode) return;

            const targetCode = currentData.grammarUniqueCode;

            await recreateGrammarExplanation(targetCode, {
                indexToDisplay: currentGrammarExplanationIndex,
                suppressHide: false,
                useLoading: true
            });

        } catch (e) {
            console.error(e);
            alert(e.message || 'Error');
        } finally {
            reloadGrammarExplanationButton._lock = false;
        }
    }, false);
}

if(generateExampleChartButton !== null)
{generateExampleChartButton.addEventListener('pointerup', function() {
	if(grammarExplanationHistory.length === LENGTH_EMPTY)return;
	let str_grammarUniqueCode = grammarExplanationHistory[currentGrammarExplanationIndex].grammarUniqueCode;
	createChartForRegisteredSentences(str_grammarUniqueCode);
}, false);}


if(showGrammarHistoryButton !== null)
{showGrammarHistoryButton.addEventListener('pointerup', function() {

	let elm_targetUl = grammarExplanationHistoryList;
	let arr_classNaming_li = [
		'wisePanelGrammarExplanationUiHistoryLi',
		'wisePanelGrammarExplanationUiHistoryLiFromHistory'
	];
	
	elm_targetUl.replaceChildren();
	buildSearchResultListItems(grammarExplanationHistory, elm_targetUl, arr_classNaming_li);
	openWisePanelUi(grammarExplanationPanel, grammarExplanationUiHistory);

}, false);}


if(downloadGrammarPdfOneColumnButton !== null)
{downloadGrammarPdfOneColumnButton.addEventListener('pointerup', async function() {

	let url = new URL(window.location.href),
	params = url.searchParams,
	send_grammar_unique_code = escapeHTML(params.get(KEY_GRAMMAR_UNIQUE_CODE));

	const targetsObj = {
	main : ['mainSelectedLanguage'],
	column : ['descriptionSelectedLanguage']
	};
	const scrollPosition = window.scrollY;
	try {
		await downloadGrammarView(true, true, true, send_grammar_unique_code, targetsObj);
	} catch (error) {
		console.error('Error during download:', error);
	}
	window.scrollTo(0, scrollPosition);

}, false);}

if(downloadGrammarPdfTwoColumnsButton !== null)
{downloadGrammarPdfTwoColumnsButton.addEventListener('pointerup', async function() {

	let url = new URL(window.location.href),
	params = url.searchParams,
	send_grammar_unique_code = escapeHTML(params.get(KEY_GRAMMAR_UNIQUE_CODE));

	const targetsObj = {
	main : ['mainDefault','mainSelectedLanguage'],
	column : ['descriptionDefault','descriptionSelectedLanguage']
	};
	const scrollPosition = window.scrollY;
	try {
		await downloadGrammarView(true, true, true, send_grammar_unique_code, targetsObj);
	} catch (error) {
		console.error('Error during download:', error);
	}
	window.scrollTo(0, scrollPosition);

}, false);}


function handleGrammarExplanationHistoryPointerUp(e) {

    const li = e.target.closest('.wisePanelGrammarExplanationUiHistoryLi');
    if (!li) {
        return;
    }

    const index = escapeNumber(li.dataset.index);
    displayCurrentStateGrammarExplanation(index, true);
}

document.addEventListener('pointerup', handleGrammarExplanationHistoryPointerUp);



if(linkToGrammarView !== null)
{linkToGrammarView.addEventListener('pointerup', function() {
	if(grammarExplanationHistory.length === LENGTH_EMPTY)return;
	let url = pageGrammarViewForTeachersUrl;
	let str_grammarUniqueCode = grammarExplanationHistory[currentGrammarExplanationIndex].grammarUniqueCode;
	let urlWithParams = `${url}/?${KEY_GRAMMAR_UNIQUE_CODE}=${encodeURIComponent(str_grammarUniqueCode)}`;
	window.open(urlWithParams, '_blank', 'noopener');
}, false);}


async function createMultipleGrammarExplanations(elms_list) {
    if (createMultipleGrammarExplanations._lock) return false;

    if (!elms_list || elms_list.length === COUNT_EMPTY) return false;
    if (elms_list.length > MAX_BATCH_GRAMMAR_CREATE) {
        alert(MSG_EXCEED_MAX_BATCH_CREATE_GRAMMAR[intSelectedLanguage]);
        return false;
    }

    createMultipleGrammarExplanations._lock = true;
	setElementDisabled(grammarExplanationBackButton, true);
	setElementDisabled(grammarExplanationForwardButton, true);

    try {
        const list = Array.from(elms_list);
        currentGrammarExplanationIndex = Math.max(
            grammarExplanationHistory.length - LAST_INDEX_OFFSET,
            LENGTH_EMPTY
        );
        const baseIndex = grammarExplanationHistory.length;

        const firstElm = list[INDEX_FIRST];
        const firstData = {
            japaneseId: firstElm.dataset.japaneseId ? escapeNumber(firstElm.dataset.japaneseId) : 0,
            grammarUniqueCode: firstElm.dataset.grammarUniqueCode ? escapeHTML(firstElm.dataset.grammarUniqueCode) : '',
            japanese: firstElm.dataset.japanese ? escapeHTML(firstElm.dataset.japanese) : '',
            kana: firstElm.dataset.kana ? escapeHTML(firstElm.dataset.kana) : '',
            categoryId: firstElm.dataset.categoryId ? escapeNumber(firstElm.dataset.categoryId) : 0
        };

        await createGrammarExplanation(firstData.grammarUniqueCode, { suppressHide: false });
        const firstIdx = saveStateGrammarExplanation(firstData, { behavior: 'append' });
        await new Promise(requestAnimationFrame);
        if (firstIdx !== null) displayCurrentStateGrammarExplanation(firstIdx, true);
        storeSharedContentsSelectionItem(firstData);

        const rest = list.slice(1).map(elm => ({
            japaneseId: elm.dataset.japaneseId ? escapeNumber(elm.dataset.japaneseId) : 0,
            grammarUniqueCode: elm.dataset.grammarUniqueCode ? escapeHTML(elm.dataset.grammarUniqueCode) : '',
            japanese: elm.dataset.japanese ? escapeHTML(elm.dataset.japanese) : '',
            kana: elm.dataset.kana ? escapeHTML(elm.dataset.kana) : '',
            categoryId: elm.dataset.categoryId ? escapeNumber(elm.dataset.categoryId) : 0,
            elm
        }));

        const promises = rest.map(d =>
            createGrammarExplanation(d.grammarUniqueCode, { suppressHide: true })
                .then(() => ({ ok: true, data: d }))
                .catch(e => ({ ok: false, data: d, e }))
        );

        const settled = await Promise.allSettled(promises);
        for (let i = 0; i < settled.length; i++) {
            const r = settled[i];
            if (r.status === 'fulfilled' && r.value.ok) {
                const d = r.value.data;
                saveStateGrammarExplanation({
                    japaneseId: d.japaneseId,
                    grammarUniqueCode: d.grammarUniqueCode,
                    japanese: d.japanese,
                    kana: d.kana,
                    categoryId: d.categoryId
                }, { behavior: 'append' });
                storeSharedContentsSelectionItem(d);
            } else if (r.status === 'fulfilled' && !r.value.ok) {
                console.error('createGrammarExplanation failed:', r.value.data.grammarUniqueCode, r.value.e);
            } else if (r.status === 'rejected') {
                console.error('createGrammarExplanation rejected:', r.reason);
            }
        }

        for (const d of rest) {
            if (d.elm.classList.contains('searchGrammarListLiInput')) d.elm.checked = false;
        }

        if (grammarExplanationHistory.length > baseIndex) {
            displayCurrentStateGrammarExplanation(baseIndex, true);
        }
		
        currentGrammarExplanationIndex = baseIndex;
		return true;

    } finally {
        createMultipleGrammarExplanations._lock = false;		
		setElementDisabled(grammarExplanationBackButton, false);
		setElementDisabled(grammarExplanationForwardButton, false);
    }
}


async function createGrammarExplanation(str_grammarUniqueCode, options = {}) {
    const { suppressHide = false } = options;

    str_grammarUniqueCode = escapeHTML(str_grammarUniqueCode);

    const elements = document.querySelectorAll('.wisePanelGrammarExplanationViewMainContentAreaContents');
    const found = Array.from(elements).some(el => el.dataset.grammarUniqueCode === str_grammarUniqueCode);
    if (found) return;

    if (!suppressHide) {
        elements.forEach(el => { el.style.display = 'none'; });
        grammarExplanationLoading.classList.remove('loading-hidden');
    }

    try {
        const urlParams = new URLSearchParams(window.location.search);

		const arr_targets_visible_from_urlParams = {};

		VISIBLE_OVERRIDE_KEYS.forEach(key => {
			const value = urlParams.get(key);
			if (value !== null) {
				arr_targets_visible_from_urlParams[key] = escapeHTML(value);
			}
		});

		const payload = {
			grammar_unique_code: str_grammarUniqueCode,
			arr_targets_visible_from_urlParams: arr_targets_visible_from_urlParams,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			wiseCoreBuildGrammarViewHtmlUrl,
			payload,
			60000
		);

		
		const data = result.data ?? {};
		const html = data.html ?? data.HTML ?? data.content ?? '';

		if (!html) {
			if (!suppressHide) {
				grammarExplanationLoading.classList.add('loading-hidden');
			}
			return;
		}

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const targetHeader = doc.getElementById('grammarViewHeader');
		// const targetHeader = doc.querySelector('.grammarViewHeaderRow');

        const naming_targetElements = [
            'sectionUserInputData',
            'sectionPrerequisiteKnowledge',
            'sectionTargetKnowledge',
            'sectionGrammarView',
            'sectionGrammarRelationAlreadyLearned',
            'sectionGrammarRelation',
            'sectionRelatedKnowledge',
            'sectionListedLocation',
            'sectionGrammarOutlineTerminology'
        ];

        const addElement = document.createElement('div');
        addElement.classList.add('wisePanelGrammarExplanationViewMainContentAreaContents');
        addElement.style.display = 'none';
        addElement.style.zIndex = 2;
        addElement.dataset.grammarUniqueCode = str_grammarUniqueCode;

        const addContainer = document.createElement('div');
        addContainer.classList.add('wisePanelGrammarExplanationViewMainContentAreaContentsContainer');
        if (targetHeader) addContainer.appendChild(targetHeader);
        addContainer.style.marginBottom = (grammarExplanationMainContentContainer.offsetHeight * 0.8) + 'px';

        naming_targetElements.forEach(naming_targetElement => {
            const targetElements = doc.querySelectorAll('.' + naming_targetElement);
            if (targetElements.length > LENGTH_EMPTY) {
                targetElements.forEach(targetElement => {
                    targetElement.querySelectorAll('a').forEach(anchor => {
                        anchor.parentNode.removeChild(anchor);
                    });
                    targetElement.querySelectorAll('.sampleSentenceListLiButtonsContainer').forEach(container => {
                        container.querySelectorAll('button[data-action="grammar:navigate"], button[data-action="navi:navigate"]').forEach(btn => btn.parentNode.removeChild(btn));
                    });
                    targetElement.querySelectorAll('input').forEach(inputElement => {
                        if (!inputElement.classList.contains('allowDisplayGrammarExplanationDisplayArea')) {
                            inputElement.removeAttribute('onclick');
                        }
                    });

                    bindDetailsToggleCommon(
                        targetElement,
                        'details.grammarViewDetails, details.grammarOutlineDetails',
                        {
                            scrollMode: 'summaryOffset',
                            containerClass: 'wisePanelGrammarExplanationViewMainContentAreaContents',
                            highlightClass: 'highlight',
                            highlightDurationMs: 1500
                        }
                    );
                    bindDetailsToggleCommon(
                        targetElement,
                        '.expandButtonDetails, .summarysContents',
                        {
                            scrollMode: 'rectBased',
                            containerClass: 'wisePanelGrammarExplanationViewMainContentAreaContents',
                            highlightClass: 'highlight',
                            highlightDurationMs: 1500,
                            addClassName: 'inGrammarExplanation',
                            guardClassName: 'expandButtonDetails'
                        }
                    );

                    targetElement.querySelectorAll('.grammarViewFrame').forEach(elm => {
                        const paletteElm = createWisePalette(elm, {
                            extractSelectors: ['.grammarViewTextContent']
                        });
                        elm.insertBefore(paletteElm, elm.firstChild);
                    });

                    addContainer.appendChild(targetElement);
                });
            }
        });

        addElement.appendChild(addContainer);
        grammarExplanationDisplayArea.appendChild(addElement);

        applyFontSizeVariation(
            GRAMMAR_FONT_SIZE_TARGETS,
            'sectionGrammarViewFontSizeVariationDifference'
        );

    } catch (e) {
        alert(e.message || 'Error');
        throw e;
    } finally {
        if (!suppressHide) {
            grammarExplanationLoading.classList.add('loading-hidden');
        }
    }
}

function bindDetailsToggleCommon(root, detailsSelector, options = {}) {
    const {
        scrollMode = 'summaryOffset',
        highlightClass = 'highlight',
        highlightDurationMs = 1500,
        containerClass = 'wisePanelGrammarExplanationViewMainContentAreaContents',
        addClassName = '',
        guardClassName = ''
    } = options;

    const detailsList = root.querySelectorAll(detailsSelector);

    detailsList.forEach(function(detailsElement) {
        if (addClassName) {
            detailsElement.classList.add(addClassName);
        }

        if (guardClassName && !detailsElement.classList.contains(guardClassName)) {
            return;
        }

        if (detailsElement.dataset.toggleHandlerBound === '1') {
            return;
        }
        detailsElement.dataset.toggleHandlerBound = '1';

        detailsElement.addEventListener('toggle', function() {
            const allSummary = root.querySelectorAll('summary');
            allSummary.forEach(function(s) {
                s.classList.remove(highlightClass);
            });

            if (!detailsElement.open) {
                return;
            }

            const summaryElement = detailsElement.querySelector('summary');
            if (!summaryElement) {
                return;
            }

            let currentNode = detailsElement;
            while (currentNode) {
                if (currentNode.classList && currentNode.classList.contains(containerClass)) {
                    break;
                }
                currentNode = currentNode.parentNode;
                if (!currentNode) {
                    break;
                }
            }

            if (!currentNode) {
                summaryElement.classList.add(highlightClass);
                setTimeout(() => {
                    summaryElement.classList.remove(highlightClass);
                }, highlightDurationMs);
                return;
            }

            let targetTop = 0;
            if (scrollMode === 'summaryOffset') {
                const summaryElementScrollPosition = summaryElement.offsetTop - 20;
                targetTop = summaryElementScrollPosition - currentNode.offsetTop;
            } else {
                const detailsRect = detailsElement.getBoundingClientRect();
                const containerRect = currentNode.getBoundingClientRect();
                const containerScrollTop = currentNode.scrollTop;
                targetTop = (containerScrollTop + (detailsRect.top - 20)) - containerRect.top;
            }

            currentNode.scrollTo({
                top: targetTop,
                behavior: 'smooth'
            });
            summaryElement.classList.add(highlightClass);
            setTimeout(() => {
                summaryElement.classList.remove(highlightClass);
            }, highlightDurationMs);
        }, false);
    });
}

function openGrammarViewInNewTab(str_grammarUniqueCode){

	str_grammarUniqueCode = escapeHTML(str_grammarUniqueCode);

	let currentPath = window.location.pathname.split("?")[INDEX_FIRST];
	if (!currentPath.endsWith("/")) {
		currentPath += "/";
	}

	let url = '';
	if (currentPath.endsWith('for-teachers/')) {
		url = pageGrammarViewForTeachersUrl;
	} else {
		url = pageGrammarPointUrl;
	}

	let urlWithParams = `${url}/?${KEY_GRAMMAR_UNIQUE_CODE}=${encodeURIComponent(str_grammarUniqueCode)}`;
	window.open(urlWithParams, '_blank', 'noopener');
}


function saveStateGrammarExplanation(obj, options = {}) {
    const { behavior = 'append' } = options;

    const grammarUniqueCode = obj.grammarUniqueCode;
    const len = grammarExplanationHistory.length;
    const lastIndex = len - LAST_INDEX_OFFSET;
    const lastCode = len > LENGTH_EMPTY ? grammarExplanationHistory[lastIndex].grammarUniqueCode : null;

    if (lastCode === grammarUniqueCode) return null;

    if (behavior === 'advance') {
        const cutIndex = Math.min(Math.max(currentGrammarExplanationIndex + LAST_INDEX_OFFSET, INDEX_FIRST), len);
        grammarExplanationHistory.splice(cutIndex);
        grammarExplanationHistory.push(obj);
        return grammarExplanationHistory.length - LAST_INDEX_OFFSET;
    }

    if (behavior === 'append') {
        const existingIndex = grammarExplanationHistory.findIndex(it => it.grammarUniqueCode === grammarUniqueCode);
        if (existingIndex >= INDEX_FIRST) return existingIndex;
        grammarExplanationHistory.push(obj);
        return grammarExplanationHistory.length - LAST_INDEX_OFFSET;
    }

    return null;
}

function displayCurrentStateGrammarExplanation(targetIndex, doIncrease){

	let result = null;
	let changedIndex = targetIndex;

	if (doIncrease) {
		for (let i = targetIndex; i < grammarExplanationHistory.length; i++) {
			if (i in grammarExplanationHistory) {
				result = grammarExplanationHistory[i].grammarUniqueCode;
				changedIndex = i;
				break;
			}
		}
	} else {
		for (let i = targetIndex; i >= INDEX_FIRST; i--) {
			if (i in grammarExplanationHistory) {
				result = grammarExplanationHistory[i].grammarUniqueCode;
				changedIndex = i;
				break;
			}
		}
	}

	if (result) {
		currentGrammarExplanationIndex = changedIndex;
		let matchFound = false;
		const elements = document.querySelectorAll('.wisePanelGrammarExplanationViewMainContentAreaContents');
		elements.forEach(element => {
			if (element.dataset.grammarUniqueCode === result) {
				if(matchFound){
					element.style.display = 'none';
				}
				else{
					element.style.display = 'block';
					matchFound = true;
				}
			} else {
				element.style.display = 'none';
			}
		});
	}
}







