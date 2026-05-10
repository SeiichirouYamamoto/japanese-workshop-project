
/******************************************************
 * navigate   : ページ遷移
 * open/close : UI表示
 * set/reset  : 状態操作
 * build      : 構築
 * get/fetch  : 取得
 * is/should  : 判定
 * handle     : イベント専用
 ******************************************************/

const grammarViewZoomInButtons = document.querySelectorAll('.grammarViewZoomIn');
const grammarViewZoomOutButtons = document.querySelectorAll('.grammarViewZoomOut');

for(let i = INDEX_FIRST; i < grammarViewZoomInButtons.length; i++){
	grammarViewZoomInButtons[i].addEventListener('pointerup',
	function (e){
		applyGrammarViewFontSizeChange(true);
	}
	, { passive: false });
}

for(let i = INDEX_FIRST; i < grammarViewZoomOutButtons.length; i++){
	grammarViewZoomOutButtons[i].addEventListener('pointerup',
	function (e){
		applyGrammarViewFontSizeChange(false);
	}
	, { passive: false });
}



function applyGrammarViewFontSizeChange(doIncreaseFontSize){

	let newVariationDifference = calculateNewFontSize('sectionGrammarViewFontSizeVariationDifference', doIncreaseFontSize);
	let arr_changed = [];
	for(let i = INDEX_FIRST; i < GRAMMAR_FONT_SIZE_TARGETS.length; i++){
		let str_targetClassName = GRAMMAR_FONT_SIZE_TARGETS[i];
		let elms = document.querySelectorAll('.' + str_targetClassName);
		if (elms.length > LENGTH_EMPTY) {
			applyNewFontSize(elms, str_targetClassName, newVariationDifference);
			elms.forEach(function(elm) {
				if (!arr_changed.includes(elm)) {
					arr_changed.push(elm);
				}
			});
		} 
	}
}

document.addEventListener('contextmenu', function(e) {
	e.preventDefault();
	e.stopPropagation();
});

document.addEventListener('pointerup', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    if (clickHandlers[action]) {
        clickHandlers[action](btn, e);
    }
});

document.addEventListener('change', function (e) {
    const target = e.target;
    const action = target.dataset.action;
    if (!action) return;

    if (changeHandlers[action]) {
        changeHandlers[action](target);
    }
});

const UIReady = (() => {
    let _resolve, _isResolved = false;
    const promise = new Promise(r => { _resolve = r; });
    return {
        promise,
        resolveOnce() { if (!_isResolved) { _isResolved = true; _resolve(); } },
        get isResolved() { return _isResolved; }
    };
})();














function initGrammarViewLayout() {

    if (grammarViewZoomInButtons === null) return;

    applyFontSizeVariation(
        GRAMMAR_FONT_SIZE_TARGETS,
        'sectionGrammarViewFontSizeVariationDifference'
    );

    finalizeLayout();
}

document.addEventListener('DOMContentLoaded', () => {

    const sectionGrammarViews = document.querySelectorAll('.sectionGrammarView');

    if (sectionGrammarViews.length === 0) return;

    prepareLayoutOnLoad();
    initGrammarViewLayout();

}, { passive: true });