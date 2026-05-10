

/******************************************************
 *  WORD INFOMATION
 *
 ******************************************************/

const whiteboardUiWordInformation = document.getElementById('wisePanelWhiteboardUiWordInformation');

const whiteboardUiWordInformationExplanationContainer = document.getElementById('wisePanelWhiteboardUiWordInformationResultExplanationContainer');
const whiteboardUiWordInformationJapaneseContainer = document.getElementById('wisePanelWhiteboardUiWordInformationResultJapaneseContainer');
const whiteboardUiWordInformationKanaContainer = document.getElementById('wisePanelWhiteboardUiWordInformationResultKanaContainer');


async function viewWordInformation(elm_targetContainer){

	let elms_deleteTarget = document.querySelectorAll('.wisePanelWhiteboardUiWordInformationResultContents');
	for(let i = INDEX_FIRST; i < elms_deleteTarget.length; i++){
		let elm_deleteTarget = elms_deleteTarget[i];
		elm_deleteTarget.remove();
	}

	let str_japanese = escapeHTML(elm_targetContainer.dataset.japanese),
		str_kana = escapeHTML(elm_targetContainer.dataset.kana),
		str_japanesePhraseClause,
		str_kanaPhraseClause,
		int_japanese_id = escapeNumber(elm_targetContainer.dataset.japaneseId),
		int_sub_classification_id = escapeNumber(elm_targetContainer.dataset.subClassificationId),
		int_form_id = escapeNumber(elm_targetContainer.dataset.formId),
		int_voice_id = escapeNumber(elm_targetContainer.dataset.voiceId);

	let str_title = MSG_CHECK_GRAMMAR_EXPLANATION[intSelectedLanguage];

	if('japanesePhraseClause' in elm_targetContainer.dataset){
		str_japanesePhraseClause = escapeHTML(elm_targetContainer.dataset.japanesePhraseClause);
		str_kanaPhraseClause = escapeHTML(elm_targetContainer.dataset.kanaPhraseClause);
		str_japanese = str_japanesePhraseClause+str_japanese;
		str_kana = str_kanaPhraseClause+str_kana;
	}
	
	let payload = {
		send_japanese_id: int_japanese_id,
		send_sub_classification_id: int_sub_classification_id,
		send_form_id: int_form_id,
		send_voice_id: int_voice_id,
		int_selected_language: intSelectedLanguage
	};
	await fetchAndRenderExplanationButtonsForGrammar(payload, str_japanese, str_kana, str_title);
	await fetchAndRenderExplanationButtonsForInflection(payload);

}


async function fetchAndRenderExplanationButtonsForGrammar(payload, str_japanese, str_kana, str_title) {

    try {

        const result = await postJson(
            wiseCoreGetDataGrammarExplanationUrl,
            payload,
            10000
        );

        const data = (result && result.data !== undefined) ? result.data : result;
		let int_ui_font_size = getWiseUiFontSizePx();

        const elm_japanese = document.createElement('div');
        elm_japanese.classList.add(
            'wisePanelWhiteboardUiWordInformationResultContents',
            'wisePanelWhiteboardUiWordInformationResultContentsText',
            'wiseUiFontSizeTarget'
        );
        elm_japanese.textContent = str_japanese;
		
        elm_japanese.style.fontSize = (int_ui_font_size + 10) + 'px';
        whiteboardUiWordInformationJapaneseContainer.appendChild(elm_japanese);

        const elm_kana = document.createElement('div');
        elm_kana.classList.add(
            'wisePanelWhiteboardUiWordInformationResultContents',
            'wisePanelWhiteboardUiWordInformationResultContentsText',
            'wiseUiFontSizeTarget'
        );
        elm_kana.textContent = str_kana;
        elm_kana.style.fontSize = (int_ui_font_size + 10) + 'px';
        whiteboardUiWordInformationKanaContainer.appendChild(elm_kana);

        // phpが0を返している。これをnull変更（現状は 0 を「データなし」と扱う）
        if (data !== 0 && data !== null && data !== undefined) {

            const int_japanese_id = data.japaneseId ? escapeNumber(data.japaneseId) : 0;
            const str_grammarUniqueCode = data.uniqueCode ? escapeHTML(data.uniqueCode) : '';
            const str_japanese_from_api = data.japanese ? escapeHTML(data.japanese) : '';
            const str_kana_from_api = data.kana ? escapeHTML(data.kana) : '';
            const int_category_id = data.categoryId ? escapeNumber(data.categoryId) : 0;

            const arr_explanation = {
                str_grammarUniqueCode: str_grammarUniqueCode,
                str_title: str_title,
                appendChildTarget: whiteboardUiWordInformationExplanationContainer,
                arrClassButton: [
                    'wisePanelWhiteboardUiWordInformationResultContents',
                    'wisePanelWhiteboardUiWordInformationResultContentsViewExplanation'
                ]
            };

            const obj = {
                japaneseId: int_japanese_id,
                uniqueCode: str_grammarUniqueCode,
                japanese: str_japanese_from_api,
                kana: str_kana_from_api,
                categoryId: int_category_id
            };

            createGrammarViewJumpButton(arr_explanation, obj);
        }

    } catch (e) {

        alert(e.message || 'Error');
        return;
    }
}


async function fetchAndRenderExplanationButtonsForInflection(payload) {

    try {

        const result = await postJson(
            wiseCoreGetDataInflectionExplanationUrl,
            payload,
            10000
        );

        // postJson が { success, data } を返す場合と、旧形式を両対応
        const data = (result && result.data !== undefined) ? result.data : result;

        if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
            return;
        }

        const arr_searchTarget = ['form', 'voice'];

        arr_searchTarget.forEach(searchTarget => {

            for (let i = INDEX_FIRST; i < data.length; i++) {

                if (!data[i] || typeof data[i] !== 'object' || !(searchTarget in data[i])) {
                    continue;
                }

                const target = data[i][searchTarget];

                // PHPが0を返している。これをnull変更（現状は0を「なし」と扱う）
                if (target === 0 || target === null || target === undefined) {
                    continue;
                }

                const int_japanese_id = target.japaneseId ? escapeNumber(target.japaneseId) : 0;
                const str_grammarUniqueCode = target.uniqueCode ? escapeHTML(target.uniqueCode) : '';
                const str_japanese = target.japanese ? escapeHTML(target.japanese) : '';
                const str_kana = target.kana ? escapeHTML(target.kana) : '';
                const int_category_id = target.categoryId ? escapeNumber(target.categoryId) : 0;

                const arr_explanation = {
                    str_grammarUniqueCode: str_grammarUniqueCode,
                    str_title: str_japanese,
                    appendChildTarget: whiteboardUiWordInformationExplanationContainer,
                    arrClassButton: [
                        'wisePanelWhiteboardUiWordInformationResultContents',
                        'wisePanelWhiteboardUiWordInformationResultContentsViewExplanation'
                    ]
                };

                const obj = {
                    japaneseId: int_japanese_id,
                    uniqueCode: str_grammarUniqueCode,
                    japanese: str_japanese,
                    kana: str_kana,
                    categoryId: int_category_id
                };

                createGrammarViewJumpButton(arr_explanation, obj);
            }
        });

    } catch (e) {

        if (e && typeof e.message === 'string' && e.message.includes('timeout')) {
            console.error('タイムアウトが発生しました。');
            return;
        }

        alert(e.message || 'Error');
        return;
    }
}