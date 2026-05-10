const MSG_PREMIUM_MEMBERS_ONLY = ['こちらは有料会員専用です。','僅限付費會員 – 限制僅供付費會員使用。'];

const MSG_QUIZ_RESTART_CONFIRM = ['最初からやり直しますか？','要重新開始嗎？'];
const MSG_QUIZ_FINISH_CONFIRM = ['終了しますか','要結束嗎？'];
const MSG_QUIZ_FINISH_ERROR = ['ブラウザを閉じることができませんでした。ご自身でブラウザバックしてください','無法自動關閉瀏覽器，請手動返回上一頁。'];
const MSG_QUIZ_NEXT_QUESTION_CONFIRM = ['次のクイズに挑戦しますか？','要挑戰下一個問題嗎？'];
const MSG_QUIZ_OTHER_QUESTION_CONFIRM = ['他のクイズに挑戦しますか？','要挑戰其他問題嗎？'];
const MSG_QUIZ_UNSELECTED_WORDS = ['未選択の単語があります。','還有未選擇的單字。'];
const MSG_QUIZ_CHECK_ANSWER_CONFIRM = ['答えを見ますか？','確認正確答案？'];
const MSG_QUIZ_CHOICES_UNSELECTED = ['解答を選択してください','請選擇一個答案'];
const MSG_QUIZ_FEEDBACK_TITLE = [
	['正解!','答對了!'],
	['残念','好可惜']
];
const MSG_QUIZ_YOUR_ANSWER_LABEL = ['「あなたの回答」: ','「您的回答」: '];
const MSG_QUIZ_CORRECT_ANSWER_LABEL = ['「正解」: ','「正確答案」: '];
const MSG_QUIZ_ORIGINAL_SENTENCE_LABEL = ['「原文」: ','「原文」: '];
const MSG_QUIZ_FURIGANA_LABEL = ['「ふりがな」: ','「注音」: '];

const MSG_INFLECTION_PROCESS = ['活用のプロセス','詞形變化過程'];

const MSG_TIMEOUT_RETRY_CONFIRMATION = [
	'通信に時間がかかっています。もう一度読み込みますか？',
	'連線時間過長。要再試一次嗎？'
];

const MSG_TIMEOUT_RETRY_GIVE_UP = [
	'通信が不安定な可能性があります。時間をおいてもう一度お試しください。',
	'連線可能不穩定，請稍後再試。'
];

const MSG_RESET_CONFIRM = ['リセットしますか？','要重置嗎？'];
const MSG_UPLOAD_CONFIRM = ['アップロードしますか？','要上載嗎？'];
const MSG_UPLOAD_COMPLETED = ['アップロードが完了しました','上載完成了'];
const MSG_DOWNLOAD_CONFIRM = ['ダウンロードしますか？','要下載嗎？'];
const MSG_NO_DOWNLOAD = ['ダウンロード対象がありません','沒有下載DATA'];
const MSG_DESELECT_CONFIRM = ['選択解除しますか？','取消選擇？'];
const MSG_UPDATE_CONFIRM = ['更新しますか？','更新？'];
const MSG_NO_UPDATE = ['更新対象がありません','沒有更新DATA'];
const MSG_DELETE_CONFIRM = ['削除しますか？','刪除？'];
const MSG_REGISTER_CONFIRM = ['登録しますか?','登記？'];
const MSG_PAGE_TRANSITION = ['ページ遷移中です…','正在跳轉頁面…'];
const MSG_ERROR_INPUT_CONTENT = ['入力内容に誤りがあります。','輸入內容有錯誤。'];
const MSG_ERROR_UNIQUE_CODE = ['コードに誤りがあります。','code有錯誤。'];
const MSG_ERROR_SENTENCE_STRUCTURE = ['文章の構成に間違いがあります。','句子的結構有錯誤。'];
const MSG_ERROR_INVALID_WORD_CONTAINER = ['wordContainer以外の要素があります。','wordContainer以外の要素があります。'];
const MSG_CHECK_GRAMMAR_EXPLANATION = ['文法を確認する','確認文法'];
const MSG_LINK_GRAMMAR_VIEW_TEACHER = ['説明','説明'];
const MSG_CONTEXT_MENU_PHRASE_CLAUSE = [
	['まとめる','組合'],
	['分解する','分解']
];
const MSG_IRREVERSIBLE_UPDATE_CONFIRM = [
    'この処理を行うと、元の状態へは戻せなくなります。\nステータスを更新しますか？',
    '此操作無法復原。\n是否要更新狀態？'
];

const MSG_SELECT_QUIZ_SECTION = ['勉強したい内容を選択してください。','請選擇您想要學習的內容。'];

const MSG_NO_INPUT_STRING = ['入力されていません','沒有輸入'];
const MSG_NO_SELECTION_ARRAY = ['選択されていません','沒有選擇'];

const MSG_COPY_QUIZ_PARTICLE_QUESTION = ['Question：[ ★ ] は同じ助詞です。助詞は何ですか？','問題：共通的助詞是什麼?'];
const MSG_COPY_QUIZ_GRAMMAR_QUESTION = ['Question：','問題：'];

const MSG_COPY_QUIZ_ANSWER = ['正解：','正確回答：'];
const MSG_COPY_QUIZ_QUOTE_SOURCE = ['引用：','引用自：'];
const MSG_COPY_QUIZ_CONFIRM = ['コピーしますか?','複製?'];
const MSG_COPY_QUIZ_RESULT = ['問題文がコピーされました。','複製成功了。'];

const MSG_EXPLANATION_SCREEN_HEADER = ['の作り方','的變化方法'];

const MSG_CHOICE_FROM_LIST = ['リストから選んでください','請從清單中選擇'];

const MSG_NO_ROOM_SELECTED = ['roomが未選択です','沒選擇room'];
const MSG_NO_QUIZ_SELECTED = ['Quizが未選択です','沒選擇Quiz'];

const MSG_PAGE_LEAVE_CONFIRMATION = ['このページを離れてもよろしいですか。','您確定要離開此頁面嗎？'];

const MSG_EXCEED_MAX_BATCH_CREATE_GRAMMAR = [
    `同時に新規作成できる文法説明は${MAX_BATCH_GRAMMAR_CREATE}件までです。\n新規作成は停止いたしました。`,
    `一次最多只能同時建立 ${MAX_BATCH_GRAMMAR_CREATE} 筆文法說明。\n已停止新增作業。`
];

const MSG_SESSION_CONFLICT = [
    '別の端末でログインが検出されました。この端末ではこの操作を続行できません。引き続きこの端末をご利用になる場合は、再ログインをお願いいたします。',
    '偵測到您在其他裝置登入。本裝置無法繼續此操作。如要繼續使用本裝置，請重新登入。'
];


const STATE_TITLE_ONLOAD = ['初期状態','初始狀態'];
const STATE_TITLE_RESIZE = ['画面をリサイズ','調整螢幕大小'];
const STATE_TITLE_DRAW_HANDWRITTEN_LINE = ['手書き線線を描画','畫手寫線'];
const STATE_TITLE_DELETE_HANDWRITTEN_LINES = ['手書き線を削除','刪除手寫線'];
const STATE_TITLE_DELETE_ALL_HANDWRITTEN_LINES = ['手書き線を全削除','全刪除手寫線'];
const STATE_TITLE_DRAW_LINK_LINE = ['Link線を描画','畫Link線'];
const STATE_TITLE_GET_REGISTERED_SENTENCES = ['文法から例文を取得','從文法中取得例句'];
const STATE_TITLE_CREATE_URL_LINK = ['URLを作成','造出URL'];
const STATE_TITLE_CREATE_MOVABLE_CONTAINER = [
	['新しい','を作成'],
	['造出新的','']
];
const STATE_TITLE_INFLECTION = ['語形変化','詞形變化'];
const STATE_TITLE_EDIT_TEXTAREA = ['テキスト編集','編輯文字'];
const STATE_TITLE_MOVE_CONTAINER = ['containerを移動','移動container'];
const STATE_TITLE_COMBINE_CONTAINER = ['containerを合わせる','組合container'];
const STATE_TITLE_DIVIDE_CONTAINER = ['containerを分解','分解container'];
const STATE_TITLE_ALIGN_CONTAINER = ['containerを整列','對齊container'];
const STATE_TITLE_DELETE_ALL_CONTAINERS = ['containerを全削除','全刪除container'];
const STATE_TITLE_DELETE_CONTAINER = ['containerを削除','刪除container'];
const STATE_TITLE_DELETE_LINK_LINE = ['リンクを削除','刪除連結'];
const STATE_TITLE_CHANGE_LABEL = ['ラベルを変更','變更表記'];

const MSG_WHITEBOARD_CONTINUE_CONFIRM = [
    '現在のホワイトボードの状態は、新しいホワイトボードにそのまま利用されます。',
    '目前白板的內容將直接延續到新的白板。'
];

const MSG_WHITEBOARD_RESTORE_CONFIRM = [
    '現在のホワイトボードの描画状態は破棄され、選択したホワイトボードを復元します。よろしいですか？',
    '目前白板的繪製內容將被捨棄，並恢復所選的白板。是否繼續？'
];

const MSG_LESSON_RECORD_END_CONFIRM = [
    '授業としての記録を終了します。現在のホワイトボードの状態を保存し、画面をリセットします。よろしいですか？',
    '將結束本次課堂記錄。當前白板狀態將被儲存，畫面將重置。是否繼續？'
];

const MSG_GRAMMAR_SELECTED_CONFIRM = [
    '選択中の文法があります。処理を進めてよろしいですか？',
    '目前有已選取的文法項目。是否繼續處理？'
];


function applyFontSizeVariation(targetArray, str_variationDifference){

	let currentVariationDifference = localStorage.getItem(str_variationDifference);
	if (!currentVariationDifference) {
		currentVariationDifference = 0;
	}
	else{
		currentVariationDifference = Number(currentVariationDifference);
	}
	localStorage.setItem(str_variationDifference, currentVariationDifference);

	for(let i = INDEX_FIRST; i < targetArray.length; i++){
		let str_targetClassName = targetArray[i];
		let elms = document.querySelectorAll('.' + str_targetClassName);
		if (elms.length > LENGTH_EMPTY) {
			let originalFontSize = localStorage.getItem(str_targetClassName);
			if (!originalFontSize) {
				let firstElm = elms[INDEX_FIRST];
				let computedStyle = window.getComputedStyle(firstElm);
				let fontSize = computedStyle.getPropertyValue('font-size');
				originalFontSize = parseFloat(fontSize);
				localStorage.setItem(str_targetClassName, originalFontSize);
			}
			else{
				originalFontSize = parseFloat(originalFontSize);
			}
				
			let newFontSize = originalFontSize + currentVariationDifference;
			
			if(newFontSize < FONT_SIZE_MIN || newFontSize > FONT_SIZE_MAX)continue;
				
			elms.forEach(function(elm) {
			elm.style.fontSize = newFontSize + 'px';
			});
		} 
	}
}

function calculateNewFontSize(str_variationDifference, doIncreaseFontSize){

    let currentVariationDifference = localStorage.getItem(str_variationDifference);

    if (!currentVariationDifference) {
        currentVariationDifference = 0;
    } else {
        currentVariationDifference = Number(currentVariationDifference);
    }

    let newVariationDifference;

    if (doIncreaseFontSize) {
        newVariationDifference = currentVariationDifference + 1;
    } else {
        newVariationDifference = currentVariationDifference - 1;
    }

    localStorage.setItem(str_variationDifference, newVariationDifference);

    return newVariationDifference;
}

function applyNewFontSize(elms, str_targetClassName, newVariationDifference){

    if (elms.length === LENGTH_EMPTY) return;

    let originalFontSize = localStorage.getItem(str_targetClassName);

    if (!originalFontSize) {
        let firstElm = elms[INDEX_FIRST];
        let computedStyle = window.getComputedStyle(firstElm);
        let fontSize = computedStyle.getPropertyValue('font-size');

        originalFontSize = parseFloat(fontSize);
        localStorage.setItem(str_targetClassName, originalFontSize);
    } else {
        originalFontSize = parseFloat(originalFontSize);
    }

    let newFontSize = originalFontSize + newVariationDifference;

    if (newFontSize < FONT_SIZE_MIN || newFontSize > FONT_SIZE_MAX) return;

    elms.forEach(function(elm) {
        elm.style.fontSize = newFontSize + 'px';
    });
}

function getWiseUiFontSizePx() {

    const elm = document.createElement('div');
    elm.classList.add('wiseUiFontSizeTarget');
    elm.style.position = 'absolute';
    elm.style.visibility = 'hidden';
    elm.style.pointerEvents = 'none';
    elm.textContent = 'A';

    document.body.appendChild(elm);

    const fontSize = parseFloat(window.getComputedStyle(elm).fontSize);

    document.body.removeChild(elm);

    return Number.isFinite(fontSize) ? fontSize : 16;
}

function checkCollision(elm, point_x, point_y){

	if(!elm){
        return false;
    }

	let bounds = elm.getBoundingClientRect();

	if(bounds.left <= point_x &&
		point_x <= bounds.right &&
		bounds.top <= point_y &&
		point_y <= bounds.bottom
	){
		return true;
	}
	else{
		return false;
	}
}