

const whiteboardUiCreateNewWordJapaneseInput = document.getElementById('wisePanelWhiteboardUiCreateNewWordJapaneseInput');
const whiteboardUiCreateNewWordKanaInput = document.getElementById('wisePanelWhiteboardUiCreateNewWordKanaInput');
const whiteboardUiCreateNewInputs = [whiteboardUiCreateNewWordJapaneseInput,whiteboardUiCreateNewWordKanaInput];


const whiteboardUiCreateNewWord = document.getElementById('wisePanelWhiteboardUiCreateNewWord');


const whiteboardUiCreateNewWordConfirmButton = document.getElementById('wisePanelWhiteboardUiCreateNewWordConfirmButton');
const whiteboardUiCreateNewWordResultList = document.getElementById('wisePanelWhiteboardUiCreateNewWordResultList');
const whiteboardUiCreateNewWordSubClassificationSelect = document.getElementById('wisePanelWhiteboardUiCreateNewWordSubClassificationSelect');


if(whiteboardUiCreateNewWordConfirmButton !== null)
{whiteboardUiCreateNewWordConfirmButton.addEventListener('pointerup', function() {

	let isCheckValidity = false,
		isNull_elms = false;

	whiteboardUiCreateNewWordResultList.replaceChildren();

	let str_japanese = escapeHTML(whiteboardUiCreateNewWordJapaneseInput.value),
		str_kana = escapeKanaCharacters(whiteboardUiCreateNewWordKanaInput.value),
		str_input_word,
		arr_input_word = [str_japanese,str_kana],
		uniqueKey = whiteboardUiCreateNewWordSubClassificationSelect.selectedIndex,
		int_sub_classification_id = escapeNumber(whiteboardUiCreateNewWordSubClassificationSelect[uniqueKey].value),
		targetElm;


	for(let i = INDEX_FIRST; i < whiteboardUiCreateNewInputs.length; i++){
		targetElm = whiteboardUiCreateNewInputs[i];
		targetElm.style.borderColor = 'black';
		isCheckValidity = targetElm.checkValidity();

		str_input_word = arr_input_word[i];
		if(str_input_word.length === LENGTH_EMPTY){isCheckValidity = false};

		if(!isCheckValidity){
			isNull_elms = true;
			targetElm.style.borderColor = 'red';
		}
	}

	if(isNull_elms){

		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('wisePanelWhiteboardUiCreateNewWordResultLi');
			elm_addLi.textContent = MSG_ERROR_INPUT_CONTENT[intSelectedLanguage];

		whiteboardUiCreateNewWordResultList.appendChild(elm_addLi);
		return;
	}

	registerNewWord(str_japanese, str_kana, int_sub_classification_id);
	
	const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

	createNewWord(str_japanese, str_kana, int_sub_classification_id, point.x, point.y);

	advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, 'A');

	closeWisePanelUi(whiteboardPanel, 'target', whiteboardUiCreateNewWord);

}, false);}


if (whiteboardUiCreateNewWordJapaneseInput !== null) {
    whiteboardUiCreateNewWordJapaneseInput.addEventListener('keydown', function (e) {

        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
        }
    });
}

if (whiteboardUiCreateNewWordKanaInput !== null) {
    whiteboardUiCreateNewWordKanaInput.addEventListener('keydown', function (e) {

        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
        }
    });
}



async function registerNewWord(str_japanese, str_kana, int_sub_classification_id) {
	
	try {
	
		const payload = {
			str_japanese: str_japanese,
			str_kana: str_kana,
			int_sub_classification_id: int_sub_classification_id
		};
		
        await postJson(
            wiseCoreRegisterNewWordUrl,
            payload,
            10000
        );

    } catch (e) {

        if (e && typeof e.message === 'string' && e.message.includes('timeout')) {
            console.error('タイムアウトが発生しました。');
            return false;
        }

        alert(e.message || 'Error');
        return false;
    }
}