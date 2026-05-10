const politePlainFormAnswerButtons = document.querySelectorAll('.wisePoliteFormPlainFormTableButtonsAnswers');
const politePlainFormAnswerContainers = document.querySelectorAll('.wisePoliteFormPlainFormTableAnswersDiv');
const politePlainFormExitButton = document.getElementById('wisePoliteFormPlainFormButtonExit');
const politePlainFormHintButtons = document.querySelectorAll('.wisePoliteFormPlainFormTableButtonsHints');
const politePlainFormOverlay = document.getElementById('wisePoliteFormPlainFormOverlay');
const politePlainForm = document.getElementById('wisePoliteFormPlainFormChart');


for(let i = INDEX_FIRST; i < politePlainFormHintButtons.length; i++){
	politePlainFormHintButtons[i].addEventListener('pointerup',
	async function (e){
	let int_form_id = escapeNumber(this.dataset.formId);

	let targetElm = document.querySelector(`.wisePoliteFormPlainFormTableTextarea[data-form-id="${int_form_id}"]`);
	
	contextMenuTargetFormId = int_form_id;

	const inflection = await getInflection();
	const inflection_process = inflection?.inflection_process ?? [];
	let str_inflection_process = '';


	for(let i = INDEX_FIRST; i < inflection_process.length; i++){
		let str_index = (i + 1).toString();
		if(i === INDEX_FIRST){
			str_inflection_process = str_index + '.' + escapeHTML(inflection_process[i]['explanation']);
		}
		else{
			str_inflection_process = str_inflection_process + '\n' + str_index + '.' + escapeHTML(inflection_process[i]['explanation']);
		}

	}

	targetElm.value = str_inflection_process;
	
	}
	, { passive: false });
}

for(let i = INDEX_FIRST; i < politePlainFormAnswerButtons.length; i++){
	politePlainFormAnswerButtons[i].addEventListener('pointerup',
	async function (e){


	// デバッグ パネル化 wordContainerのidはテーブルに移す

	let int_form_id = escapeNumber(this.dataset.formId);
	let targetElm = document.querySelector(`.wisePoliteFormPlainFormTableAnswersDiv[data-form-id="${int_form_id}"]`);
	
	contextMenuTargetFormId = int_form_id;
	
	const inflection = await getInflection();
	let str_japanese = escapeHTML(inflection?.japanese ?? '');

	
	// い形容詞の語形変化見直し
	switch (contextMenuTargetSubClassificationId) {
		case POS_ADJ_I:
		case POS_ADJ_I_ALT:
		case POS_AUX_VERB_I:
		switch (contextMenuTargetFormId) {
			case CONJ_POLITE_AFFIRMATIVE_NON_PAST:
			case CONJ_POLITE_NEGATIVE_NON_PAST:
			case CONJ_POLITE_AFFIRMATIVE_PAST:
			case CONJ_POLITE_NEGATIVE_PAST:
			str_japanese = str_japanese + 'です';
			break;
			default:
			str_japanese = str_japanese;
		}
		break;
		default:
		str_japanese = str_japanese;
	}

	targetElm.textContent = str_japanese;
	}
	, { passive: false });
}


if(politePlainFormExitButton !== null)
{politePlainFormExitButton.addEventListener('pointerup', function() {
	politePlainFormAnswerContainers
	politePlainFormAnswerContainers.forEach(function(elm) {
		elm.textContent = '';
	});
	politePlainFormOverlay.classList.remove('overlay-on');
}, false);}