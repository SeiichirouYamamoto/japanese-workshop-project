
/******************************************************
 *  FORM LIST
 *
 ******************************************************/

const whiteboardUiFormList = document.getElementById('wisePanelWhiteboardUiFormList');
const whiteboardUiFormListContainer = document.getElementById('wisePanelWhiteboardUiFormListContainer');

async function buildFormList(){
	
	whiteboardUiFormListUl.replaceChildren();
	
	let payload = {
		int_selected_language: intSelectedLanguage
	};

	try {
		const result = await postJson(
			wiseCoreBuildFormListHtmlUrl,
			payload,
			10000
		);

		const data = result.data;

		if (!data || typeof data.html !== 'string') {
			return;
		}

		whiteboardUiFormListUl.innerHTML = data.html;
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
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


const whiteboardUiFormListUl = document.getElementById('wisePanelWhiteboardUiFormListUl');

document.addEventListener('pointerup', async (e) => {

    const li = e.target.closest('.wisePanelWhiteboardUiFormListLi');
    if (!li) {
        return;
    }

    const send_form_id = escapeNumber(li.dataset.formId);
    contextMenuTargetFormId = send_form_id;


    const inflection = await getInflection();
    if (!inflection) {
        return;
    }

    applyInflectionToWordContainer(inflection);
    saveState(STATE_TITLE_INFLECTION[intSelectedLanguage] + ' ' + escapeHTML(inflection.japanese));

});
