/******************************************************
 *  COMMON
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]



/******************************************************
 *  SORTING
 *
 ******************************************************/
// [DOM]
// [EVENT]
// [FUNCTIONS]
const landingQuizzesOverlay = document.getElementById('landingPageQuizzesScreenOverlay');
const landingQuizzesScreen = document.getElementById('landingPageQuizzesScreen');
const landingSelectQuizConfirmButton = document.getElementById('selectQuizContainerConfirmButton');
const quizFeedbackExplanationList = document.getElementById('quizFeedbackScreenContentExplanationUl');
const quizFeedbackNextButton = document.getElementById('quizFeedbackScreenNextButton');
const quizFeedbackOverlay = document.getElementById('quizOverlayFeedback');




if(landingSelectQuizConfirmButton !== null)
{landingSelectQuizConfirmButton.addEventListener('pointerup', function() {

	const pageType = 'landing';
	const room_unique_code = 'default';
	buildQuizContentsSection(this, pageType, room_unique_code);
	
}, false);}




if(quizFeedbackNextButton !== null)
{quizFeedbackNextButton.addEventListener('pointerup', function() {

	if(escapeHTML(this.dataset.pageType) === 'quiz'){
		location.reload();
		return;
	}
	else if (escapeHTML(this.dataset.pageType) === 'landing') {
		let isConfirmed = window.confirm(MSG_QUIZ_OTHER_QUESTION_CONFIRM[intSelectedLanguage]);
		if(!isConfirmed)return;

		let int_quiz_type = escapeNumber(this.dataset.quizType);

		let url = '';
		switch (int_quiz_type) {
			case QUIZ_TYPE_JAPANESE_PARTICLE:
				url = pageJapaneseParticleQuizUrl;
				break;
			case QUIZ_TYPE_WORD_INFLECTION:
				url = pageWordInflectionQuizUrl;
				break;
			case QUIZ_TYPE_GRAMMAR:
				url = pageGrammarQuizUrl;
				break;
			case QUIZ_TYPE_PLAIN_FORM:
				url = pagePlainformQuizUrl;
				break;
			case QUIZ_TYPE_SORTING:
				url = pageSortingQuizUrl;
				break;
			default:
		}

		const levelData = this.dataset.level || '';
		const jcData = this.dataset.japaneseClassification || '';
		const infData = this.dataset.inflection || '';
		const params = new URLSearchParams();

		if (levelData) {
			const val = levelData.trim();
			if (val !== '') params.append('masteryLevel', val);
		}

		if (jcData) {
			jcData.split(',').forEach(v => {
				const val = v.trim();
				if (val !== '') params.append('arr_japanese_classification[]', val);
			});
		}

		if (infData) {
			infData.split(',').forEach(v => {
				const val = v.trim();
				if (val !== '') params.append('arr_inflection[]', val);
			});
		}

		if ([...params].length > 0) {
			url += '?' + params.toString();
		}

		window.open(url, '_blank', 'noopener');

	}
	else{
		const pageType = 'wise';
		const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
		buildQuizContentsSection(this, pageType, room_unique_code);
		closeWisePanelUi(quizPanel, 'target', quizUiFeedback);
	}

}, false);}






async function buildQuizContentsSection(btn, pageType, room_unique_code) {

	document.querySelectorAll('.sectionQuizContents').forEach((elm) => {
		elm.remove();
	});

	const studyTopicElement = document.getElementById('selectQuizContainerStudyTopic');
	const selectedOption = studyTopicElement.selectedOptions[0];
	const optgroup = selectedOption.closest('optgroup');
	const dataGroup = optgroup ? optgroup.dataset.group : null;
	const quizType = selectedOption.value;

	if (!quizType) {
		alert(MSG_NO_QUIZ_SELECTED[intSelectedLanguage] || 'No Selected');
		return;
	}

	setElementDisabled(btn, true);
	const original_label = btn.textContent;
	// btn.textContent = MSG_PAGE_TRANSITION[intSelectedLanguage];
    const shouldChangeLabel =
        !btn.classList.contains('wisePanelQuizHeaderButton');

    if (shouldChangeLabel) {
        btn.textContent = MSG_PAGE_TRANSITION[intSelectedLanguage];
    }

	let isAdvanceStageNum = FLAG_FALSE;
	if (pageType === 'wise') {
		isAdvanceStageNum = FLAG_TRUE;
	}

	const payload = {
		quiz_type: quizType,
		page_type: pageType,
		room_unique_code: room_unique_code,
		is_advance_stage_num: isAdvanceStageNum,
		int_selected_language: intSelectedLanguage
	};

	let attempt = 1;

	try {

		while (attempt <= POST_JSON_TIMEOUT_MAX_RETRY) {

			try {

				const result = await postJson(
					quizGetQuizContentsUrl,
					payload,
					10000
				);

				const data = result.data;
				if (!data || !data.html) {
					return;
				}

				const elm_quizContentsContainer = document.getElementById('quizContentsContainer');
				elm_quizContentsContainer.innerHTML = data.html;

				quizHistory.push({
					quizType: quizType,
					quizHistoryPrompt: data.quizHistoryPrompt
				});

				setTimeout(() => {
					if (dataGroup === 'sorting') {
						initializeGlobalQuizCorrectAnswers();
					} 

					applyFontSizeVariation(
						['wiseUiFontSizeTarget'],
						'wiseUiFontSizeTargetVariationDifference'
					);

					const elm_quizHeader = document.querySelector('.quizHeader');
					if (elm_quizHeader !== null) {
						elm_quizHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
					}

					if (pageType === 'wise') {
						// デバッグ クイズパネル化 処理を変更
						// wiseQuizzesScreen.classList.remove('hidden');
						// wiseSelectQuizScreen.classList.add('hidden');
					} else if (pageType === 'landing') {
						landingQuizzesScreen.classList.remove('hidden');
						landingQuizzesOverlay.classList.add('overlay-on');
					}
				}, 100);

				return;

			} catch (error) {

				const isTimeout = error.message && error.message.includes('タイムアウト');

				if (!isTimeout) {
					console.error('Error:', error.message || error);
					alert(error.message || 'Error');
					return;
				}

				console.error(`タイムアウトが発生しました。(attempt: ${attempt}/${POST_JSON_TIMEOUT_MAX_RETRY})`);

				if (attempt >= POST_JSON_TIMEOUT_MAX_RETRY) {
					alert(MSG_TIMEOUT_RETRY_GIVE_UP[intSelectedLanguage] || 'Please try again later.');
					return;
				}

				const ok = window.confirm(
					MSG_TIMEOUT_RETRY_CONFIRMATION[intSelectedLanguage] || 'Retry?'
				);

				if (!ok) {
					return;
				}

				attempt += 1;
			}
		}

	} finally {
		setElementDisabled(btn, false);
		btn.textContent = original_label;
	}
}


function showQuizFeedbackScreen(elm_button, isCorrect, int_quiz_type, str_correct_answer, str_your_answer, str_japanese, str_furigana){

	let elm_quizFeedbackScreenTitle = document.getElementById('quizFeedbackScreenTitle');
	if(isCorrect){
		elm_quizFeedbackScreenTitle.textContent = MSG_QUIZ_FEEDBACK_TITLE[INDEX_FIRST][intSelectedLanguage];
	}
	else{
		elm_quizFeedbackScreenTitle.textContent = MSG_QUIZ_FEEDBACK_TITLE[INDEX_SECOND][intSelectedLanguage];
	}

	let elm_quizFeedbackScreenContentMessageYourAnswer = document.getElementById('quizFeedbackScreenContentMessageYourAnswer');
	let elm_quizFeedbackScreenContentMessageCorrectAnswer = document.getElementById('quizFeedbackScreenContentMessageCorrectAnswer');
	let elm_quizFeedbackScreenContentMessageJapanese = document.getElementById('quizFeedbackScreenContentMessageJapanese');
	let elm_quizFeedbackScreenContentMessageFurigana = document.getElementById('quizFeedbackScreenContentMessageFurigana');
	
	elm_quizFeedbackScreenContentMessageYourAnswer.textContent = '';
	elm_quizFeedbackScreenContentMessageCorrectAnswer.textContent = '';
	elm_quizFeedbackScreenContentMessageJapanese.textContent = '';
	elm_quizFeedbackScreenContentMessageFurigana.textContent = '';
	
	elm_quizFeedbackScreenContentMessageYourAnswer.textContent = MSG_QUIZ_YOUR_ANSWER_LABEL[intSelectedLanguage]+str_your_answer;
	elm_quizFeedbackScreenContentMessageCorrectAnswer.textContent = MSG_QUIZ_CORRECT_ANSWER_LABEL[intSelectedLanguage]+str_correct_answer;
	elm_quizFeedbackScreenContentMessageJapanese.textContent = MSG_QUIZ_ORIGINAL_SENTENCE_LABEL[intSelectedLanguage]+str_japanese;
	elm_quizFeedbackScreenContentMessageFurigana.textContent = MSG_QUIZ_FURIGANA_LABEL[intSelectedLanguage]+str_furigana;

	const elm_quizFeedbackScreenSectionExplanationTitle = document.getElementById('quizFeedbackScreenSectionExplanationTitle');
	elm_quizFeedbackScreenSectionExplanationTitle.textContent = '';

	
	quizFeedbackExplanationList.replaceChildren();

	const elm_quizFeedbackScreenContentExplanationToGrammarButtonsContainer = document.getElementById('quizFeedbackScreenContentExplanationToGrammarButtonsContainer');
	elm_quizFeedbackScreenContentExplanationToGrammarButtonsContainer.replaceChildren();
	
	let str_explanation = elm_button.dataset.explanation;
	if (shouldShowInflectionProcess(int_quiz_type) && !isEmptyValue(str_explanation)) {
		elm_quizFeedbackScreenSectionExplanationTitle.textContent = MSG_INFLECTION_PROCESS[intSelectedLanguage];

		let arr_explanation = str_explanation.split(',');
		arr_explanation = arr_explanation.map(item => item.trim());
		arr_explanation.forEach(function(item, index, array) {
			let str_index = (index + 1).toString();
			let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('quizFeedbackScreenContentExplanationLi', 'quizScreenContainerContent', 'wiseUiFontSizeTarget');
			elm_addLi.textContent = str_index + '.' + escapeHTML(item);
			quizFeedbackExplanationList.appendChild(elm_addLi);
		});
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}

	const sourcePage = elm_button.dataset.quizSourcePage;

	if (sourcePage === 'wise') {
		openWisePanelUi(quizPanel, quizUiFeedback);
	}
	else {
		const elm_screen = document.getElementById('quizFeedbackScreen');
		elm_screen.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	
		quizFeedbackOverlay.classList.add('overlay-on');
	}
	
}

function shouldShowInflectionProcess(int_quiz_type) {
    return int_quiz_type === QUIZ_TYPE_WORD_INFLECTION ||
           int_quiz_type === QUIZ_TYPE_PLAIN_FORM;
}


function copyQuizContentsToClipboard(textToCopyQuestion, liElements, answersElement){
	let isConfirmed = window.confirm(MSG_COPY_QUIZ_CONFIRM[intSelectedLanguage]);
	if(isConfirmed) {

	let textToCopyChoices = '';

	liElements.forEach(function(li, index) {
		let liText = li.textContent || li.innerText;
		textToCopyChoices = textToCopyChoices+liText+'\n';
	});
	textToCopyChoices = textToCopyChoices+'\n';

	let textToCopyAnswer = MSG_COPY_QUIZ_ANSWER[intSelectedLanguage] + escapeHTML(answersElement.textContent);
	textToCopyAnswer = textToCopyAnswer+'\n\n\n';

	let textToCopyQuoteSource = MSG_COPY_QUIZ_QUOTE_SOURCE[intSelectedLanguage] + currentHomeUrl;

	let textToCopy = textToCopyQuestion+textToCopyChoices+textToCopyAnswer+textToCopyQuoteSource;

	setTimeout(() => {

		if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(textToCopy)
		.then(function() {
			alert(MSG_COPY_QUIZ_RESULT[intSelectedLanguage]);
		})
		.catch(function(err) {
			console.log('コピーに失敗しました。:'+err);
		});
		} else {
		let textArea = document.createElement('textarea');
		textArea.value = textToCopy;
		textArea.style.top = '0';
		textArea.style.left = '0';
		textArea.style.visibility = 'hidden';
		textArea.style.position = 'absolute';
		document.body.appendChild(textArea);
		textArea.select();
		document.execCommand("copy");
		document.body.removeChild(textArea);
		}
	}, 100);
	}
}



function buildGlobalQuizCorrectAnswerWordList(arr_registered_sentence_elements) {
	const wordSet = new Set();

	for (const key in arr_registered_sentence_elements) {
		const element = arr_registered_sentence_elements[key];

		if (element.phraseClauseType === 'phraseClauseContainer') {
			if (element.japanesePhraseClause && element.japanese) {
				wordSet.add(element.japanesePhraseClause + element.japanese);
			}
			if (element.kanaPhraseClause && element.kana) {
				wordSet.add(element.kanaPhraseClause + element.kana);
			}
		} else if (element.phraseClauseType === 'movableContainer') {
			if (element.japanese) wordSet.add(element.japanese);
			if (element.kana) wordSet.add(element.kana);
		}
	}

	correctAnswerWordList = Array.from(wordSet);
}





clickHandlers['quiz:sortingQuiz:usersManual:show'] = function (btn) {
	const targetId = btn.dataset.actionTarget;
	if (!targetId) return;

	const overlay = document.getElementById(targetId);
	if (!overlay) return;

	overlay.classList.add('overlay-on');
};

clickHandlers['quiz:sortingQuiz:restart'] = function (btn) {
    const isConfirmed = window.confirm(MSG_QUIZ_RESTART_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) return;

    document.querySelectorAll('.sortingQuizAnswersPieces')
        .forEach(element => element.remove());

    document.querySelectorAll('.sortingQuizPieceListContainerLi')
        .forEach(element => element.classList.remove('selected'));
};

// ======================================================
// 0) 共通ユーティリティ（delegate / overlay / dataset 等）
// ======================================================

function delegateEvent(eventType, selector, handler, options) {

    document.addEventListener(eventType, (e) => {

        const target = e.target.closest(selector);
        if (!target) {
            return;
        }

        handler(e, target);

    }, options);
}

function setOverlayOnById(idName) {
    const elm = document.getElementById(idName);
    if (elm) {
        elm.classList.add('overlay-on');
    }
}

function setOverlayOffById(idName) {
    const elm = document.getElementById(idName);
    if (elm) {
        elm.classList.remove('overlay-on');
    }
}

function closeAllQuizOverlays() {
    document.querySelectorAll('.quizOverlay').forEach(overlay => {
        overlay.classList.remove('overlay-on');
    });
}

function isAllSelectedBySelector(selector, selectedClassName) {
    let allSelected = true;
    document.querySelectorAll(selector).forEach(elm => {
        if (!elm.classList.contains(selectedClassName)) {
            allSelected = false;
        }
    });
    return allSelected;
}

function buildSortingQuizUserAnswerFromPieces() {

    const elms = document.querySelectorAll('.sortingQuizAnswersPieces');

    let str_userAnswerJapanese = '';
    let str_userAnswerKana = '';
    let hasUnexpectedWord = false;

    elms.forEach(elm => {

        const str_japaneseResult = escapeHTML(elm.dataset.japaneseResult);
        const str_kanaResult = escapeHTML(elm.dataset.kanaResult);

        str_userAnswerJapanese += str_japaneseResult;
        str_userAnswerKana += str_kanaResult;

        const isJapaneseIncluded = correctAnswerWordList.includes(str_japaneseResult);
        const isKanaIncluded = correctAnswerWordList.includes(str_kanaResult);

        if (!isJapaneseIncluded && !isKanaIncluded) {
            hasUnexpectedWord = true;
        }
    });

    return {
        userAnswerJapanese: str_userAnswerJapanese,
        userAnswerKana: str_userAnswerKana,
        hasUnexpectedWord: hasUnexpectedWord
    };
}

function showSortingQuizSuccess(userAnswerJapanese, userAnswerKana) {

    const expected = document.getElementById('quizSuccessScreenExpectedAnswerContent');
    const yourAnswer = document.getElementById('quizSuccessScreenYourAnswerContent');
    const furigana = document.getElementById('quizSuccessScreenYourAnswerFuriganaContent');

    if (expected) expected.textContent = correctAnswers[INDEX_FIRST];
    if (yourAnswer) yourAnswer.textContent = userAnswerJapanese;
    if (furigana) furigana.textContent = userAnswerKana;

    setOverlayOnById('quizOverlaySuccess');
}

function showSortingQuizFailure(hasUnexpectedWord) {

    const hint = document.getElementById('quizFailureScreenInflectionHint');
    if (hint) {
        if (hasUnexpectedWord) hint.classList.remove('hidden');
        else hint.classList.add('hidden');
    }

    setOverlayOnById('quizOverlayFailure');
}

function findClosestByClass(elm, className) {
    if (!elm || !elm.closest) {
        return null;
    }
    return elm.closest('.' + className);
}


function setQuizFeedbackNextButtonDatasets(btn, datasetSource) {

    const levelData = datasetSource.dataset.level || '';
    const jcData = datasetSource.dataset.japaneseClassification || '';
    const infData = datasetSource.dataset.inflection || '';

    if (levelData) btn.dataset.level = levelData;
    else delete btn.dataset.level;

    if (jcData) btn.dataset.japaneseClassification = jcData;
    else delete btn.dataset.japaneseClassification;

    if (infData) btn.dataset.inflection = infData;
    else delete btn.dataset.inflection;
}

function getCheckedNumberValues(selector) {
    return Array.from(document.querySelectorAll(selector))
        .map(input => Number(input.value));
}

// ======================================================
// 1) Sorting Quiz 系
// ======================================================

// 1-1) Confirm
delegateEvent('pointerup', '#sortingQuizMenuBarButtonConfirm', (e, btn) => {

    const allSelected = isAllSelectedBySelector('.sortingQuizPieceListContainerLi', 'selected');

    if (!allSelected) {
        alert(MSG_QUIZ_UNSELECTED_WORDS[intSelectedLanguage]);
        return;
    }

    const result = buildSortingQuizUserAnswerFromPieces();

    if (correctAnswers.includes(result.userAnswerJapanese)) {
        showSortingQuizSuccess(result.userAnswerJapanese, result.userAnswerKana);
        return;
    }

    showSortingQuizFailure(result.hasUnexpectedWord);
});

// 1-2) Finish quiz (close window)
delegateEvent('pointerup', '.sortingQuizMenuBarButtonFinishQuiz', () => {

    const isConfirmed = window.confirm(MSG_QUIZ_FINISH_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) {
        return;
    }

    try {
        if (window.opener) {
            window.close();
            setTimeout(() => {
                if (!window.closed) {
                    alert(MSG_QUIZ_FINISH_ERROR[intSelectedLanguage]);
                }
            }, 500);
        } else {
            alert(MSG_QUIZ_FINISH_ERROR[intSelectedLanguage]);
        }
    } catch (e) {
        alert(MSG_QUIZ_FINISH_ERROR[intSelectedLanguage]);
    }
});

// 1-3) Change size
delegateEvent('pointerup', '#sortingQuizMenuBarButtonChangeSizeBig', () => {
    changeWiseUiFontSize(true);
});

delegateEvent('pointerup', '#sortingQuizMenuBarButtonChangeSizeSmall', () => {
    changeWiseUiFontSize(false);
});

// 1-4) Piece inflection button (open inflection overlay)
delegateEvent('pointerup', '.sortingQuizPieceListContainerLiButtonsInflection', (e, btn) => {

    const li = findClosestByClass(btn, 'sortingQuizPieceListContainerLi');
    if (li) {

        contextMenuTargetContainer = li;
        contextMenuTargetJapaneseId = escapeNumber(li.dataset.japaneseId);
        contextMenuTargetJapaneseElementId = escapeNumber(li.dataset.japaneseElementId);
        contextMenuTargetSubClassificationId = escapeNumber(li.dataset.subClassificationId);
        contextMenuTargetFormId = escapeNumber(li.dataset.formId);
        contextMenuTargetLabelId = escapeNumber(li.dataset.labelId);
        contextMenuTargetVoiceId = escapeNumber(li.dataset.voiceId);
    }

    buildSortingQuizInflectionFormList();
    setOverlayOnById('quizOverlayInflection');
});

// 1-5) Piece kana toggle
delegateEvent('pointerup', '.sortingQuizPieceListContainerLiButtonsKana', (e, btn) => {

    const li = findClosestByClass(btn, 'sortingQuizPieceListContainerLi');
    if (!li) {
        return;
    }

    const elm = li.querySelector('.sortingQuizPieceListContainerLiJapanese');
    if (!elm) {
        return;
    }

    let str_result;

    if (li.classList.contains('KanaDisplay')) {
        str_result = escapeHTML(li.dataset.japaneseResult);
        li.classList.remove('KanaDisplay');
    } else {
        str_result = escapeHTML(li.dataset.kanaResult);
        li.classList.add('KanaDisplay');
    }

    elm.textContent = str_result;
});

// 1-6) Piece select -> add answer piece + animation
delegateEvent('pointerup', '.sortingQuizPieceListContainerLiJapanese', (event, elmJapanese) => {

    const li = elmJapanese.closest('.sortingQuizPieceListContainerLi');
    if (!li || li.classList.contains('selected')) {
        return;
    }

    li.classList.add('selected');

    const str_japanese = escapeHTML(li.dataset.japaneseResult);
    const str_kana = escapeHTML(li.dataset.kanaResult);
    const int_unique_key = escapeNumber(li.dataset.uniqueKey);

    const answerPiece = buildSortingQuizAnswerPiece(li, str_japanese, str_kana, int_unique_key);
    appendSortingQuizAnswerPiece(answerPiece);
	applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);
    animateSortingQuizAnswerPieceFly(event, str_japanese, answerPiece);
});

function buildSortingQuizAnswerPiece(sourceLi, str_japanese, str_kana, int_unique_key) {

    const answerPiece = document.createElement('div');
	answerPiece.classList.add('sortingQuizAnswersPieces', 'wiseUiFontSizeTarget');
    answerPiece.style.visibility = 'hidden';
    answerPiece.textContent = str_japanese;

    answerPiece.dataset.uniqueKey = int_unique_key;
    answerPiece.dataset.japaneseResult = str_japanese;
    answerPiece.dataset.kanaResult = str_kana;

    answerPiece.addEventListener('pointerup', () => {
        answerPiece.remove();
        sourceLi.classList.remove('selected');
    }, { passive: false });

    return answerPiece;
}

function appendSortingQuizAnswerPiece(answerPiece) {
    const quizZone = document.getElementById('sortingQuizZone');
    if (quizZone) {
        quizZone.appendChild(answerPiece);
    }
}

function animateSortingQuizAnswerPieceFly(e, text, answerPiece) {

    const point = getClientPoint(e);

    const animSpan = document.createElement('span');
    animSpan.textContent = text;
    animSpan.style.position = 'absolute';
    animSpan.style.zIndex = 1000;
    animSpan.classList.add('wiseUiFontSizeTarget');
    animSpan.style.left = '0px';
    animSpan.style.top = '0px';
    animSpan.style.visibility = 'hidden';
    animSpan.style.willChange = 'left, top';

    document.body.appendChild(animSpan);

    // 1) start 位置を確定
    animSpan.style.visibility = 'visible';

    const tempRect = animSpan.getBoundingClientRect();
    const spanWidth = tempRect.width;
    const spanHeight = tempRect.height;

    const startX = point.x + window.scrollX - spanWidth / 2;
    const startY = point.y + window.scrollY - spanHeight / 2;

    animSpan.style.left = `${startX}px`;
    animSpan.style.top = `${startY}px`;

    // answerPiece は hidden でも rect は取れます（display:none でなければOK）
    const rectEnd = answerPiece.getBoundingClientRect();
    const endX = rectEnd.left + window.scrollX;
    const endY = rectEnd.top + window.scrollY;

    // 2) 次フレームで transition を設定
    requestAnimationFrame(() => {

        animSpan.style.transition = 'left 0.5s ease, top 0.5s ease';

        // 3) さらに次フレームで最終位置へ（ここが重要）
        requestAnimationFrame(() => {

            animSpan.style.left = `${endX}px`;
            animSpan.style.top = `${endY}px`;

            animSpan.addEventListener('transitionend', () => {
                animSpan.remove();
                answerPiece.style.visibility = 'visible';
            }, { once: true });
        });
    });
}


// 1-7) Failure overlay buttons
delegateEvent('pointerup', '#quizFailureScreenButtonChallengeAgain', () => {
    setOverlayOffById('quizOverlayFailure');
});

delegateEvent('pointerup', '#quizFailureScreenButtonShowCorrectAnswer', () => {

    const correctAnswerContent = document.getElementById('quizFailureScreenCorrectAnswerContent');
    const messages = document.getElementById('quizFailureScreenSectionMessages');
    const buttons = document.getElementById('quizFailureScreenSectionButtons');
    const correctAnswerSection = document.getElementById('quizFailureScreenSectionCorrectAnswer');

    if (correctAnswerContent) correctAnswerContent.textContent = correctAnswers[INDEX_FIRST];
    if (correctAnswerSection) correctAnswerSection.classList.remove('hidden');
    if (messages) messages.classList.add('hidden');
    if (buttons) buttons.classList.add('hidden');
});

// 1-8) Users manual close
delegateEvent('pointerup', '#quizUsersManualScreenButtonClose', () => {
    setOverlayOffById('quizOverlayUsersManual');
});

// 1-9) select sorting quiz next
delegateEvent('pointerup', '#selectSortingQuizButtonNext', () => {
    location.reload();
});

// ======================================================
// 2) Quiz 共通系（ページ遷移 / 次の問題 / 設定 / 履歴 等）
// ======================================================

// 2-1) 次の問題（window close / reload / wise next）
delegateEvent('pointerup', '.quizScreenButtonNextQuestion', () => {
    window.close();
});

delegateEvent('pointerup', '.quizButtonNextQuestion', () => {
    location.reload();
});

delegateEvent('pointerup', '.wiseQuizButtonNextQuestion', (e, btn) => {
    const pageType = 'wise';
    const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
    buildQuizContentsSection(btn, pageType, room_unique_code);
});

// 2-2) 他クイズページへ
delegateEvent('pointerup', '.quizButtonToPage', (e, btn) => {

    const isConfirmed = window.confirm(MSG_QUIZ_OTHER_QUESTION_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) {
        return;
    }

    let url = resolveQuizPageUrl(btn);
    if (url === '') {
        return;
    }

    url = appendQuizPageQueryParams(url, btn);
    window.open(url, '_blank', 'noopener');
});

function resolveQuizPageUrl(btn) {

    switch (true) {
        case btn.classList.contains('japaneseParticleQuizButtonToPage'):
            return pageJapaneseParticleQuizUrl;
        case btn.classList.contains('wordInflectionQuizButtonToPage'):
            return pageWordInflectionQuizUrl;
        case btn.classList.contains('grammarQuizButtonToPage'):
            return pageGrammarQuizUrl;
        case btn.classList.contains('plainformQuizButtonToPage'):
            return pagePlainformQuizUrl;
        case btn.classList.contains('sortingQuizButtonToPage'):
            return pageSortingQuizUrl;
        default:
            return '';
    }
}

function appendQuizPageQueryParams(url, btn) {

    const levelData = btn.dataset.level || '';
    const jcData = btn.dataset.japaneseClassification || '';
    const infData = btn.dataset.inflection || '';

    const params = new URLSearchParams();

    if (levelData) {
        const val = levelData.trim();
        if (val !== '') params.append('masteryLevel', val);
    }

    if (jcData) {
        jcData.split(',').forEach(v => {
            const val = v.trim();
            if (val !== '') params.append('arr_japanese_classification[]', val);
        });
    }

    if (infData) {
        infData.split(',').forEach(v => {
            const val = v.trim();
            if (val !== '') params.append('arr_inflection[]', val);
        });
    }

    if ([...params].length > 0) {
        url += '?' + params.toString();
    }

    return url;
}

// 2-3) Settings overlay open/close + submit
delegateEvent('pointerup', '.quizSettingsScreenSubmit', async () => {

	const elmSelect = document.getElementById('quizSettingsScreenContentsSelectMasteryLevel');
	const masteryLevel = elmSelect ? elmSelect.value : '';

	const subCategoryValues = getCheckedNumberValues(
		"#quizSettingsScreenContentsContainerSubCategory input:checked"
	);

	const japaneseClassificationValues = getCheckedNumberValues(
		"#quizSettingsScreenLabelsContainerJapaneseClassification input:checked"
	);

	const inflectionValues = getCheckedNumberValues(
		"#quizSettingsScreenLabelsContainerWordInflection input:checked"
	);

	const payload = {
		int_mastery_level: masteryLevel,
		arr_sub_category: subCategoryValues,
		arr_japanese_classification: japaneseClassificationValues,
		arr_inflection: inflectionValues
	};

	let attempt = 1;

	while (attempt <= POST_JSON_TIMEOUT_MAX_RETRY) {

		try {

			await postJson(
				quizSaveQuizSettingsUrl,
				payload,
				10000
			);

			closeWisePanelUi(quizPanel, 'target', quizUiSettings);
			return;

		} catch (error) {

			const message = (error && typeof error.message === 'string') ? error.message : '';
			const isTimeout = message.includes('タイムアウト');

			if (!isTimeout) {
				console.error('Error:', message || error);
				alert(message || 'Error');
				return;
			}

			console.error(`タイムアウトが発生しました。(attempt: ${attempt}/${POST_JSON_TIMEOUT_MAX_RETRY})`);

			if (attempt >= POST_JSON_TIMEOUT_MAX_RETRY) {
				alert(MSG_TIMEOUT_RETRY_GIVE_UP[intSelectedLanguage] || 'Please try again later.');
				return;
			}

			const ok = window.confirm(
				MSG_TIMEOUT_RETRY_CONFIRMATION[intSelectedLanguage] || 'Retry?'
			);

			if (!ok) {
				return;
			}

			attempt += 1;
		}
	}
});


delegateEvent('pointerup', '.quizSettingsScreenSelectAll', (e, btn) => {
    const container = btn.closest('.quizSettingsScreenContentsContainer');
    if (container) {
        container.querySelectorAll(".quizSettingsScreenLabelsContainer input[type='checkbox']")
            .forEach(input => input.checked = true);
    }
});

delegateEvent('pointerup', '.quizSettingsScreenDeselectAll', (e, btn) => {
    const container = btn.closest('.quizSettingsScreenContentsContainer');
    if (container) {
        container.querySelectorAll(".quizSettingsScreenLabelsContainer input[type='checkbox']")
            .forEach(input => input.checked = false);
    }
});





// 2-6) Close all overlays
delegateEvent('pointerup', '.quizScreenButtonCloseScreenOverlay', () => {
    closeAllQuizOverlays();
});

// 2-7) Hint (stage advance)
delegateEvent('pointerup', '.quizButtonHint', (e, btn) => {

    const quizItem = findClosestByClass(btn, 'quizItem');
    if (!quizItem) {
        return;
    }

    let int_currentStageIndex = parseInt(quizItem.dataset.currentStageIndex, 10);

    const stages = quizItem.querySelectorAll('.quizStage');
    if (!stages || stages.length === 0) {
        return;
    }

    stages[int_currentStageIndex].classList.add('hidden');

    int_currentStageIndex += 1;

    if (stages[int_currentStageIndex]) {
        stages[int_currentStageIndex].classList.remove('hidden');
        quizItem.dataset.currentStageIndex = int_currentStageIndex;
    }
});

// ======================================================
// 3) Quiz “答え合わせ” 系（Choices / Input）
// ======================================================

delegateEvent('pointerup', '.quizCheckAnswerButtonChoices', (e, btn) => {

    const interaction = findClosestByClass(btn, 'quizInteraction');
    if (!interaction) {
        return;
    }

    const quizChoices = interaction.querySelector('.quizChoices');
    if (!quizChoices) {
        return;
    }

    const checkedOption = quizChoices.querySelector('input[type="radio"]:checked');
    if (!checkedOption) {
        alert(MSG_QUIZ_CHOICES_UNSELECTED[intSelectedLanguage]);
        return;
    }

    const int_quiz_type = escapeNumber(btn.dataset.quizType);
    quizFeedbackNextButton.dataset.quizType = int_quiz_type;

    setQuizFeedbackNextButtonDatasets(quizFeedbackNextButton, btn);

    const isCorrect = checkedOption.value === 'correct';

    const elm_correct_answer = quizChoices.querySelector('input[type="radio"][value="correct"]');
    const str_correct_answer = escapeHTML(elm_correct_answer.dataset.answer);
    const str_your_answer = escapeHTML(checkedOption.dataset.answer);
    const str_japanese = escapeHTML(btn.dataset.japanese);
    const str_furigana = escapeHTML(btn.dataset.furigana);

    showQuizFeedbackScreen(
        btn,
        isCorrect,
        int_quiz_type,
        str_correct_answer,
        str_your_answer,
        str_japanese,
        str_furigana
    );
});

delegateEvent('pointerup', '.quizCheckAnswerButtonInput', (e, btn) => {

    const interaction = findClosestByClass(btn, 'quizInteraction');
    if (!interaction) {
        return;
    }

    const quizInput = interaction.querySelector('.quizInput');
    if (!quizInput) {
        return;
    }

    const int_quiz_type = escapeNumber(btn.dataset.quizType);
    quizFeedbackNextButton.dataset.quizType = int_quiz_type;

    setQuizFeedbackNextButtonDatasets(quizFeedbackNextButton, btn);

    const str_correct_answer_japanese = escapeHTML(btn.dataset.correctAnswerJapanese);
    const str_correct_answer_kana = escapeHTML(btn.dataset.correctAnswerKana);
    const str_your_answer = escapeHTML(quizInput.value);
    const str_japanese = escapeHTML(btn.dataset.japanese);
    const str_furigana = escapeHTML(btn.dataset.furigana);

    if (isEmptyValue(str_your_answer)) {
        alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
        return;
    }

    let isCorrect = false;
    if (str_your_answer === str_correct_answer_japanese || str_your_answer === str_correct_answer_kana) {
        isCorrect = true;
    }

    showQuizFeedbackScreen(
        btn,
        isCorrect,
        int_quiz_type,
        str_correct_answer_japanese,
        str_your_answer,
        str_japanese,
        str_furigana
    );
});

// ======================================================
// 4) Quiz Inflection（overlay内のフォーム選択）
// ======================================================

delegateEvent('pointerup', '.quizInflectionScreenLi', async (e, li) => {

    const send_form_id = escapeNumber(li.dataset.formId);
    contextMenuTargetFormId = send_form_id;

    const inflection = await getInflection();
    if (!inflection) {
        return;
    }

    applyInflectionToSortingQuizTarget(inflection);
    setOverlayOffById('quizOverlayInflection');
});

function applyInflectionToSortingQuizTarget(inflection) {

    const int_japanese_id = escapeNumber(inflection.japaneseId);
    const int_japanese_element_id = escapeNumber(inflection.japaneseElementId);
    const int_sub_classification_id = escapeNumber(inflection.subClassificationId);
    const int_form_id = escapeNumber(inflection.formId);
    const int_label_id = escapeNumber(inflection.labelId);
    const int_voice_id = escapeNumber(inflection.voiceId);
    const str_japanese = escapeHTML(inflection.japanese);
    const str_kana = escapeHTML(inflection.kana);

    const str_japanesePhraseClause = escapeHTML(contextMenuTargetContainer.dataset.japanesePhraseClause);
    const str_kanaPhraseClause = escapeHTML(contextMenuTargetContainer.dataset.kanaPhraseClause);

    let str_japanese_result = '';
    let str_kana_result = '';

    if (contextMenuTargetContainer.dataset.japanesePhraseClause !== STRING_NONE) {
        str_japanese_result = str_japanesePhraseClause + str_japanese;
        str_kana_result = str_kanaPhraseClause + str_kana;
    } else {
        str_japanese_result = str_japanese;
        str_kana_result = str_kana;
    }

    const elm_target = contextMenuTargetContainer.querySelector('.sortingQuizPieceListContainerLiJapanese');
    if (elm_target) {
        elm_target.textContent = str_japanese_result;
    }

    contextMenuTargetContainer.dataset.japaneseId = int_japanese_id;
    contextMenuTargetContainer.dataset.japaneseElementId = int_japanese_element_id;
    contextMenuTargetContainer.dataset.subClassificationId = int_sub_classification_id;
    contextMenuTargetContainer.dataset.formId = int_form_id;
    contextMenuTargetContainer.dataset.labelId = int_label_id;
    contextMenuTargetContainer.dataset.voiceId = int_voice_id;
    contextMenuTargetContainer.dataset.japanese = str_japanese;
    contextMenuTargetContainer.dataset.kana = str_kana;
    contextMenuTargetContainer.dataset.japaneseResult = str_japanese_result;
    contextMenuTargetContainer.dataset.kanaResult = str_kana_result;
}

async function buildSortingQuizInflectionFormList() {

	if (
		contextMenuTargetContainer === null ||
		contextMenuTargetBaseContainer === null ||
		contextMenuTargetInnerContainer === null
	) {
		return $.Deferred().resolve([]).promise();
	}
	if (contextMenuTargetJapaneseId === DEFAULT_JAPANESE_ID) {
		return $.Deferred().resolve([]).promise();
	}

	let url = new URL(window.location.href),
	params = url.searchParams;
	const formList = params.getAll('formList[]').map(Number);

	const elm_quizInflectionScreenUl = document.getElementById('quizInflectionScreenUl');
	if (!elm_quizInflectionScreenUl) {
		return;
	}
	elm_quizInflectionScreenUl.replaceChildren();

	const elm_loading = document.getElementById('quizInflectionScreenLoading');
	if (elm_loading) {
		elm_loading.classList.remove('loading-hidden');
	}

	let attempt = 1;

	try {

		const payload = {
			send_japanese_id: contextMenuTargetJapaneseId,
			send_japanese_element_id: contextMenuTargetJapaneseElementId,
			send_sub_classification_id: contextMenuTargetSubClassificationId,
			send_label_id: contextMenuTargetLabelId,
			send_voice_id: contextMenuTargetVoiceId,
			formList: formList,
			int_selected_language: intSelectedLanguage
		};

		while (attempt <= POST_JSON_TIMEOUT_MAX_RETRY) {

			try {

				const result = await postJson(
					quizGetFormListUrl,
					payload,
					10000
				);

				const data = result.data;

				if (!data || !data.html) {
					return;
				}

				elm_quizInflectionScreenUl.innerHTML = data.html;

				applyFontSizeVariation(
					['wiseUiFontSizeTarget'],
					'wiseUiFontSizeTargetVariationDifference'
				);

				return;

			} catch (error) {

				const message = (error && typeof error.message === 'string') ? error.message : '';
				const isTimeout = message.includes('タイムアウト');

				if (!isTimeout) {
					console.error('Error:', message || error);
					alert(message || 'Error');
					return;
				}

				console.error(`タイムアウトが発生しました。(attempt: ${attempt}/${POST_JSON_TIMEOUT_MAX_RETRY})`);

				if (attempt >= POST_JSON_TIMEOUT_MAX_RETRY) {
					alert(MSG_TIMEOUT_RETRY_GIVE_UP[intSelectedLanguage] || 'Please try again later.');
					return;
				}

				const ok = window.confirm(
					MSG_TIMEOUT_RETRY_CONFIRMATION[intSelectedLanguage] || 'Retry?'
				);

				if (!ok) {
					return;
				}

				attempt += 1;
			}
		}

	} finally {

		if (elm_loading) {
			elm_loading.classList.add('loading-hidden');
		}
	}
}


function generateAnswerPatternsRecursively(arr_link_id, arr_seen, uniquekey_movableContainer, isFurigana) {

    const findedArray = arr_link_id.find(item => parseInt(item.uniqueKey) === uniquekey_movableContainer);
    const filteredArray = arr_link_id.filter(item => parseInt(item.linkId) === uniquekey_movableContainer);

    let uniquekey_movableContainer_current;
    let arr_answers_pattern_add = [];
    let arr_answers_pattern_combined = [];
    let str_quizPuzzleZone_answer_pattern_joined = '';
    let arr_answers_pattern_joined = [];
    let arr_answers_pattern_result = [];
    let str_japanese = escapeHTML(findedArray.japanese);
    let str_furigana = escapeHTML(findedArray.kana);

    if (filteredArray.length === LENGTH_EMPTY) {
        if (isFurigana) {
            return [[str_furigana]];
        }
        return [[str_japanese]];
    }

    // 1) 子要素を linkType でグルーピング
    const obj_groups = {}; // { linkType: [edge, ...] }

    for (let i = INDEX_FIRST; i < filteredArray.length; i++) {
        const t = escapeNumber(filteredArray[i].linkType);
        if (!(t in obj_groups)) {
            obj_groups[t] = [];
        }
        obj_groups[t].push(filteredArray[i]);
    }

    // 2) linkType 大きい順に処理
    const arr_types_desc = Object.keys(obj_groups)
        .map(v => parseInt(v))
        .sort((a, b) => b - a);

    // 3) グループごとに variant を作り、優先順で連結していく
    //    variant = [ child1候補配列, child2候補配列, ... ] の並び
    let arr_variants = [[]];

    for (let ti = INDEX_FIRST; ti < arr_types_desc.length; ti++) {

        const linkType = arr_types_desc[ti];
        const edges = obj_groups[linkType];

        // PRIORITY_SEQUENCE の「固定順」を安定させる（sort があれば sort、なければ boundsTop、なければ 0）
        if (linkType === LINK_TYPE_PRIORITY_SEQUENCE) {
            edges.sort((a, b) => {
                const aSort = ('sort' in a) ? escapeNumber(a.sort) : null;
                const bSort = ('sort' in b) ? escapeNumber(b.sort) : null;

                if (aSort !== null && bSort !== null) {
                    return aSort - bSort;
                }

                const aTop = ('boundsTop' in a) ? escapeFloatNumber(a.boundsTop) : 0;
                const bTop = ('boundsTop' in b) ? escapeFloatNumber(b.boundsTop) : 0;
                return aTop - bTop;
            });
        }

        // この linkType グループの「子ごとの候補配列の列」を作る
        let arr_answers_pattern_type = [];

        for (let i = INDEX_FIRST; i < edges.length; i++) {
            uniquekey_movableContainer_current = escapeNumber(edges[i].uniqueKey);

            arr_answers_pattern_add = generateAnswerPatternsRecursively(
                arr_link_id,
                arr_seen,
                uniquekey_movableContainer_current,
                isFurigana
            );

            // arr_answers_pattern_add は [ [候補... ] ] なので concat で列に積む
            arr_answers_pattern_type = arr_answers_pattern_type.concat(arr_answers_pattern_add);
        }

        // ★ここが本題：PRIORITY_SEQUENCE だけ permute しない
        let arr_group_variants;
        if (linkType === LINK_TYPE_PRIORITY_SEQUENCE) {
            arr_group_variants = [arr_answers_pattern_type]; // 固定（1通り）
        } else {
            // NORMAL と TO_PHRASE_CLAUSE は同じ扱い（permute）
            arr_group_variants = getPermutations(arr_answers_pattern_type);
        }

        // グループ間は「優先順を保持」して連結（順序保証）
        const arr_next_variants = [];
        for (let a = INDEX_FIRST; a < arr_variants.length; a++) {
            for (let b = INDEX_FIRST; b < arr_group_variants.length; b++) {
                arr_next_variants.push(arr_variants[a].concat(arr_group_variants[b]));
            }
        }
        arr_variants = arr_next_variants;
    }

    // 4) 従来処理：variant ごとに combinations を作り、文字列を join して親要素を末尾に付ける
    for (let i = INDEX_FIRST; i < arr_variants.length; i++) {

        arr_answers_pattern_combined = getCombinations(arr_variants[i]);

        for (let j = INDEX_FIRST; j < arr_answers_pattern_combined.length; j++) {

            str_quizPuzzleZone_answer_pattern_joined = arr_answers_pattern_combined[j].join('');

            if (isFurigana) {
                str_quizPuzzleZone_answer_pattern_joined = str_quizPuzzleZone_answer_pattern_joined + str_furigana;
            } else {
                str_quizPuzzleZone_answer_pattern_joined = str_quizPuzzleZone_answer_pattern_joined + str_japanese;
            }

            arr_answers_pattern_joined.push(str_quizPuzzleZone_answer_pattern_joined);
        }
    }

    arr_answers_pattern_result = [arr_answers_pattern_joined];
    return arr_answers_pattern_result;
}


function getPermutations(array) {
	const permutations = [];

	function swap(array, i, j) {
	const temp = array[i];
	array[i] = array[j];
	array[j] = temp;
	}

	function generate(n, array) {
	if (n === 1) {
		permutations.push([...array]);
	} else {
		for (let i = INDEX_FIRST; i < n; i++) {
		generate(n - 1, array);
			if (n % 2 === 0) {
				swap(array, i, n - 1);
			} else {
				swap(array, 0, n - 1);
			}
		}
	}
	}

	generate(array.length, array);

	return permutations;
}
function getCombinations(arrays) {
	if (arrays.length === LENGTH_EMPTY) {
		return [[]];
	}
	let result = [];
	let currentArray = arrays[INDEX_FIRST];
	let remainingArrays = arrays.slice(1);
	let remainingCombinations = getCombinations(remainingArrays);
	for (let i = INDEX_FIRST; i < currentArray.length; i++) {
		for (let j = INDEX_FIRST; j < remainingCombinations.length; j++) {
			result.push([currentArray[i]].concat(remainingCombinations[j]));
		}
	}
	return result;
}


function initSortingQuizFullScreenLayout(isOnload) {

    if (sortingQuizFullScreenSection === null) return;

    if (isOnload) {
        const elm_quizHeader = document.querySelector('.quizHeader');
        if (elm_quizHeader !== null) {
            setTimeout(() => {
                elm_quizHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 10);
        }
        applyFullscreenRequirement();

    }

    finalizeLayout();
}

function bindQuizLayoutEvents() {

    if (sortingQuizFullScreenSection === null) return;

    const onResize = throttle(() => {
        prepareLayoutOnResize();
        initSortingQuizFullScreenLayout(false);
    }, 150);

    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('orientationchange', onResize, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {

    const sectionQuizContents = document.querySelector('.sectionQuizContents');
    if (sectionQuizContents === null) return;

    applyFontSizeVariation(
        ['wiseUiFontSizeTarget'],
        'wiseUiFontSizeTargetVariationDifference'
    );

}, { passive: true });

document.addEventListener('DOMContentLoaded', () => {
    
    if (
        sortingQuizMainSection === null &&
        sortingQuizFullScreenSection === null
    ) return;

    initializeGlobalQuizCorrectAnswers();
    applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);

    prepareLayoutOnLoad();
    initSortingQuizFullScreenLayout(true);
    bindQuizLayoutEvents();

}, { passive: true });


async function initializeGlobalQuizCorrectAnswers() {

    const elm_btn = document.getElementById('sortingQuizMenuBarButtonConfirm');
    const send_sentence_unique_code = escapeHTML(elm_btn.dataset.sentenceUniqueCode);

    const arr_registered_sentence_elements = await getRegisteredSentenceElements(send_sentence_unique_code, false);

    if (arr_registered_sentence_elements.length === LENGTH_EMPTY) {
        return;
    }

    buildGlobalQuizCorrectAnswerWordList(arr_registered_sentence_elements);

    const findedArray = arr_registered_sentence_elements.find(
        item => parseInt(item.linkId, 10) === SENTENCE_END
    );

    if (!findedArray) return;

    const uniquekey_movableContainer_sentence_end = escapeNumber(findedArray.uniqueKey);
    let arr_seen = [];

    let arr_link_id = rebuildLinkIdsFromElements(arr_registered_sentence_elements);
    arr_link_id = rebuildLinkIdsToTopElement(arr_link_id);

    const arr_answers_pattern =
        generateAnswerPatternsRecursively(arr_link_id, arr_seen, uniquekey_movableContainer_sentence_end, false);
    correctAnswers = arr_answers_pattern[INDEX_FIRST];

    const arr_answers_pattern_furigana =
        generateAnswerPatternsRecursively(arr_link_id, arr_seen, uniquekey_movableContainer_sentence_end, true);
    correctAnswersFurigana = arr_answers_pattern_furigana[INDEX_FIRST];
}