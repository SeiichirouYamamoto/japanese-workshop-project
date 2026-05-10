function handleFrameInSliderButtonClick(e) {

    const prevButton = e.target.closest('.frameInSliderPrevButton');
    if (prevButton) {
        handleFrameInSliderClick(false, prevButton);
        return;
    }

    const nextButton = e.target.closest('.frameInSliderNextButton');
    if (nextButton) {
        handleFrameInSliderClick(true, nextButton);
        return;
    }
}

document.addEventListener('pointerup', handleFrameInSliderButtonClick);


function handleFrameInSliderClick(isNext, button) {
    const container = button.closest(".frameInSliderButtonsContainer");
    if (!container) return;

    let current = Number(container.dataset.currentIndex) || 0;
    const max = Number(container.dataset.maxIndex) || 0;
    const nextIndex = isNext ? current + 1 : current - 1;

    if (nextIndex < INDEX_FIRST || nextIndex > max) {
        updateButtonsVisibility(container);
        return;
    }

    navigateFrameInSlider(isNext, button);
    container.dataset.currentIndex = nextIndex;
    updateButtonsVisibility(container);
}

function navigateFrameInSlider(isNext, currentNode){
	while (currentNode) {
	if (currentNode.classList && currentNode.classList.contains('detailsDiv')) {
		break;
	}
	currentNode = currentNode.parentNode;
	if (!currentNode) {
		break;
	}
	}
	if (currentNode) {
	const elm_currentVisible = currentNode.querySelector('.frameInSlider.visible');
	if (elm_currentVisible) {
		const elms_frame =  currentNode.querySelectorAll('.frameInSlider');
		const currentIndex = Array.from(elms_frame).indexOf(elm_currentVisible);
		const nextIndex = isNext ? currentIndex + 1 : currentIndex - 1;
		if (nextIndex < INDEX_FIRST || nextIndex >= elms_frame.length) {
			return;
		}
			
		elm_currentVisible.classList.remove("visible");
		elm_currentVisible.classList.add("vanishing");
		
		let summaryElement = null;
		while (currentNode) {
			if (currentNode.classList && currentNode.classList.contains('wisePanelGrammarExplanationViewMainContentAreaContents')) {
				break;
			}
			if (currentNode.tagName && currentNode.tagName.toLowerCase() === 'details') {
				summaryElement = currentNode.querySelector('summary');
			}
			currentNode = currentNode.parentNode;
			if (!currentNode) {
				break;
			}
		}
		if (currentNode && summaryElement) {
			const summaryElementScrollPosition = summaryElement.offsetTop - 20;
			currentNode.scrollTo({
				top: summaryElementScrollPosition - currentNode.offsetTop,
				behavior: 'smooth',
			});
		}

		const elm_visibleTarget = elms_frame[nextIndex];

		setTimeout(() => {
		elm_currentVisible.classList.remove("vanishing");
		elm_visibleTarget.classList.add('visible');
		elm_visibleTarget.style.animation = "slideIn 0.7s ease";
		}, 700);
	}
	
	return;
	}
}


clickHandlers['grammar:show'] = async function (btn) {
    const targetType = btn.dataset.actionTarget;
    switch (targetType) {
        case 'answer':
            toggleSampleSentenceListCompletion(btn, 'sampleSentenceListAnswerSpan', 'answer');
			let elm_li = findLi(btn);
			if (elm_li) {
				const furiganaBtn = elm_li.querySelector('button.grammarViewActionButton[data-key="furigana"]');
				if (furiganaBtn && furiganaBtn.classList.contains('hidden')) {
					furiganaBtn.classList.remove('hidden');
				}
			}

            break;
        case 'furigana':
            toggleSampleSentenceListCompletion(btn, 'sampleSentenceListFuriganaSpan', 'furigana');
            break;
        case 'wiseMapFocusPoint':
            if (typeof wiseMapOverlay !== 'undefined' && wiseMapOverlay !== null) {
				// デバッグ panel化 マップパネル
				// switchWiseCorePanelUiMode(WISE_CORE_PANEL.NONE);
                renderWiseMapUIFocusPoint(btn.dataset.uniqueCode);
            }
            break;
        default:
            console.warn('未対応のgrammar:showターゲット:', targetType);
    }
};


function toggleSampleSentenceListCompletion(elm, classNaming, datasetNaming) {
    toggleTextCompletion(elm, {
        datasetNaming: datasetNaming,
        targetSelector: '.sampleSentenceListTextDiv',
        spanClassNaming: classNaming,
        baseSpanClass: 'sampleSentenceListSpan',
        spanClasses: [],
        animationClass: 'animationSlideIn'
    });
}



clickHandlers['grammar:navigate'] = function (btn) {

    const pagePath = btn.dataset.pagePath;
    const uniqueCode = btn.dataset.uniqueCode;

    if (!pagePath) {
        console.warn('pagePathが指定されていません');
        return;
    }
    if (!uniqueCode) {
        console.warn('uniqueCodeが指定されていません');
        return;
    }

    let baseUrl = currentHomeUrl + pagePath;
    let urlWithParams = `${baseUrl}/?${KEY_SENTENCE_UNIQUE_CODE}=${encodeURIComponent(uniqueCode)}`;

    window.open(urlWithParams, '_blank', 'noopener');
	
};