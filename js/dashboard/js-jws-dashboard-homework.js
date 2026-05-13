const homeworkOverlay = document.getElementById('homeworkOverlay');

async function handleShowHomeworkOverlay(btn) {

    const elmOverlay = document.getElementById('homeworkOverlay');
    const elmLoading = document.getElementById('homeworkModalLoading');
    const elmContents = document.getElementById('homeworkModalContents');

    if (elmOverlay === null || elmLoading === null || elmContents === null) {
        return;
    }

    const targetDate = String(btn?.dataset?.targetDate || '').trim();
    const homeworkId = String(btn?.dataset?.homeworkId || '').trim();

    if (targetDate === '' || homeworkId === '') {
        return;
    }

    const payload = {
        homework_id: homeworkId,
        target_date: targetDate,
        int_selected_language: intSelectedLanguage
    };

    /* --------------------
       overlay ON + loading ON
    -------------------- */
    elmOverlay.classList.add('overlay-on');
    elmContents.innerHTML = '';
    elmLoading.classList.remove('loading-hidden');

    try {

        const result = await postJson(
            dashboardGetHomeworkUrl,
            payload,
            10000
        );
		
        elmContents.innerHTML = result.data.html || '';

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error.message || 'Error');
        }

    } finally {

        /* --------------------
           loading OFF
        -------------------- */
        elmLoading.classList.add('loading-hidden');
    }
}


function navigateToSortingQuizFullscreenFromHomework(isAdvanceStage, elm) {

    const formListStr = elm.dataset.formList;

    let formList;
    try {
        formList = JSON.parse(formListStr);
    } catch (e) {
        console.error('JSONのパースに失敗しました:', e);
        return;
    }
    const queryParams = formList.map(id => `formList[]=${escapeNumber(id)}`).join('&');

    const sentenceUniqueCode = escapeHTML(elm.dataset.sentenceUniqueCode);
    const url = pageSortingQuizFullscreenUrl;

    let urlWithParams = `${url}/?${KEY_SENTENCE_UNIQUE_CODE}=${encodeURIComponent(sentenceUniqueCode)}&${queryParams}`;
    if (isAdvanceStage) {
        urlWithParams += '&advance_stage=1';
    }
    window.open(urlWithParams, '_blank', 'noopener');
}

function toggleHomeworkCompletion(elm, classNaming, datasetNaming) {
    toggleTextCompletion(elm, {
        datasetNaming: datasetNaming,
        targetSelector: '.homeworkLiText',
        spanClassNaming: classNaming,
        baseSpanClass: 'homeworkSpan',
        spanClasses: [],
        animationClass: 'animationSlideIn',
        beforeAppend: ({ elm_span }) => {
            // 下に出したいなら改行を入れる（必要なら）
            elm_span.insertAdjacentHTML('afterbegin', '<br>');
        }
    });
}


clickHandlers['homework:show_homework_overlay'] = function (btn) {

    const elmOverlay = document.getElementById('homeworkOverlay');

    if (elmOverlay === null) {
        return;
    }

    elmOverlay.classList.add('overlay-on');

    handleShowHomeworkOverlay(btn);
};


clickHandlers['homework:navigate:sorting-quiz-fullscreen'] = function (btn) {
    navigateToSortingQuizFullscreenFromHomework(false, btn);
};

clickHandlers['homework:navigate:sorting-quiz-fullscreen-advance-stage'] = function (btn) {
    navigateToSortingQuizFullscreenFromHomework(true, btn);
};

clickHandlers['homework:show'] = async function (btn) {
    const targetType = btn.dataset.actionTarget;

    switch (targetType) {
        case 'answer':
            toggleHomeworkCompletion(btn, 'homeworkAnswerSpan', 'answer');
            {
                const li = findLi(btn);
                if (li) {
                    const kanaBtn = li.querySelector('button.grammarViewActionButton[data-key="furigana"]');
                    if (kanaBtn && kanaBtn.classList.contains('hidden')) {
                        kanaBtn.classList.remove('hidden');
                    }
                }
            }

            break;

        case 'furigana':
            toggleHomeworkCompletion(btn, 'homeworkKanaSpan', 'furigana');
            break;

        default:
            console.warn('未対応のhomework:showターゲット:', targetType);
    }
};