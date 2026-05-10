function formatDate(date) {
	let year = date.getFullYear();
	let month = (date.getMonth() + 1).toString().padStart(2, '0');
	let day = date.getDate().toString().padStart(2, '0');
	let hours = date.getHours().toString().padStart(2, '0');
	let minutes = date.getMinutes().toString().padStart(2, '0');
	let seconds = date.getSeconds().toString().padStart(2, '0');
	return year + month + day + '_' + hours + minutes + seconds;
}

function isEven(number) {
	return number % 2 === 0;
}

function joinUrl(base, path) {
    return base.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
}

function toArrayMaybe(v){
  if (v == null) return [];
  return Array.isArray(v) ? v : [v];
}

function ensureNumber(value, fallback = 0) {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
}

function sortMemosByOrder(memos) {
    return [...memos].sort((a, b) => {
        const ao = ensureNumber(a.memo_order, 0);
        const bo = ensureNumber(b.memo_order, 0);
        if (ao !== bo) {
            return ao - bo;
        }
        return ensureNumber(a.memo_id, 0) - ensureNumber(b.memo_id, 0);
    });
}

function createDebounce(fn, waitMs) {
    let timerId = null;

    return function (...args) {
        if (timerId !== null) {
            window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(() => {
            timerId = null;
            fn(...args);
        }, waitMs);
    };
}

function toggleTextCompletion(elm, opt) {

    const datasetNaming = opt.datasetNaming;
    const targetSelector = opt.targetSelector;
    const spanClasses = opt.spanClasses || [];
    const baseSpanClass = opt.baseSpanClass || '';
    const animationClass = opt.animationClass || '';
    const completedClass = opt.completedClass || 'completed';
    const useTextContent = opt.useTextContent !== false; // default true
    const beforeAppend = opt.beforeAppend || null;

    const raw = elm?.dataset?.[datasetNaming] ?? '';
    const str_target = escapeHTML(raw);

    const elm_li = findLi(elm);
    if (!elm_li || !str_target) {
        return;
    }

    const existing = elm_li.querySelector('.' + opt.spanClassNaming);
    if (elm.classList.contains(completedClass)) {

        if (existing) {
            existing.remove();
        }

        elm.classList.remove(completedClass);
        return;
    }

    const targetElm = elm_li.querySelector(targetSelector);
    if (!targetElm) {
        return;
    }

    const elm_span = document.createElement('span');

    const classes = [];
    if (baseSpanClass) {
        classes.push(baseSpanClass);
    }
    if (opt.spanClassNaming) {
        classes.push(opt.spanClassNaming);
    }
    classes.push(...spanClasses);
    if (animationClass) {
        classes.push(animationClass);
    }

    elm_span.classList.add(...classes);

    if (useTextContent) {
        elm_span.textContent = str_target;
    } else {
        elm_span.innerHTML = str_target;
    }

    if (typeof beforeAppend === 'function') {
        beforeAppend({ elm, elm_li, targetElm, elm_span });
    }

    targetElm.appendChild(elm_span);
    elm.classList.add(completedClass);
}

function setElementDisabled(elm, isDisabled) {

    if (!elm) {
        return;
    }

    elm.disabled = isDisabled;
    elm.classList.toggle('is-disabled', isDisabled);
}

function clearSelectOptions(selectElm) {

    if (!selectElm) {
        return;
    }

    while (selectElm.options.length > LENGTH_EMPTY) {
        selectElm.remove(INDEX_FIRST);
    }
}

function setSelectLoading(selectElm, message = 'Now Loading...') {

    if (!selectElm) {
        return;
    }

    clearSelectOptions(selectElm);

    const option = document.createElement('option');
    option.value = '';
    option.textContent = message;
    setElementDisabled(option, true);
    option.selected = true;

    selectElm.appendChild(option);
}

/* ------------------------------
    fixed / ui option helpers
------------------------------ */
function findFixedOption(selectElm) {

    if (!selectElm) {
        return null;
    }

    const options = Array.from(selectElm.options);
    return options.find(option => option.dataset.fixed === '1') || null;
}

function setOptionLabelTemporarily(optionElm, tempLabel) {

    if (!optionElm) {
        return;
    }

    if (optionElm.dataset.originalLabel === undefined) {
        optionElm.dataset.originalLabel = optionElm.textContent || '';
    }

    optionElm.textContent = tempLabel;
}

function restoreOptionLabel(optionElm) {

    if (!optionElm) {
        return;
    }

    if (optionElm.dataset.originalLabel !== undefined) {
        optionElm.textContent = optionElm.dataset.originalLabel;
        delete optionElm.dataset.originalLabel;
    }
}

function ensureUiOption(selectElm, uiType, label) {

    // uiType: 'loading' | 'empty'
    if (!selectElm) {
        return null;
    }

    let uiOption = Array.from(selectElm.options).find(option => option.dataset.ui === uiType) || null;

    if (!uiOption) {
        uiOption = document.createElement('option');
        uiOption.value = '';
        uiOption.dataset.ui = uiType;
        setElementDisabled(uiOption, true);
        selectElm.appendChild(uiOption);
    }

    uiOption.textContent = label;
    uiOption.selected = true;

    return uiOption;
}

function removeUiOptions(selectElm) {

    if (!selectElm) {
        return;
    }

    Array.from(selectElm.options).forEach(option => {
        if (option.dataset.ui === 'loading' || option.dataset.ui === 'empty') {
            option.remove();
        }
    });
}

/**
 * fixed以外を削除（fixedは保持）
 * ※ createRoomLessonDateOptions / createRoomWhiteboardOptions で使う想定
 */
function clearSelectOptionsExceptFixed(selectElm) {

    if (!selectElm) {
        return;
    }

    Array.from(selectElm.options).forEach(option => {
        if (option.dataset.fixed !== '1') {
            option.remove();
        }
    });
}

/**
 * Loading開始
 * - fixedがある: fixedのlabelだけ差し替え（originalLabel保存）
 * - fixedがない: loading optionを追加
 */
function beginSelectLoading(selectElm, message = 'Now Loading...') {

    if (!selectElm) {
        return;
    }

    removeUiOptions(selectElm);

    const fixedOption = findFixedOption(selectElm);

    if (fixedOption) {
        setOptionLabelTemporarily(fixedOption, message);
        fixedOption.selected = true;
        return;
    }

    ensureUiOption(selectElm, 'loading', message);
}

/**
 * Empty開始
 * - fixedがある: fixedのlabelだけ差し替え（originalLabel保存）
 * - fixedがない: empty optionを追加
 */
function beginSelectEmpty(selectElm, message = 'No data') {

    if (!selectElm) {
        return;
    }

    removeUiOptions(selectElm);

    const fixedOption = findFixedOption(selectElm);

    if (fixedOption) {
        setOptionLabelTemporarily(fixedOption, message);
        fixedOption.selected = true;
        return;
    }

    ensureUiOption(selectElm, 'empty', message);
}

/**
 * Loading/Empty終了（復元）
 * - fixedがある: label復元
 * - fixedがない: ui option削除
 */
function endSelectUi(selectElm) {

    if (!selectElm) {
        return;
    }

    const fixedOption = findFixedOption(selectElm);

    if (fixedOption) {
        restoreOptionLabel(fixedOption);
    }

    removeUiOptions(selectElm);
}

/* ------------------------------
    select + confirm button UI state
------------------------------ */
function setWiseSetupSelectUiState(selectElm, confirmButtonElm, disabled) {

    setElementDisabled(selectElm, disabled);
    setElementDisabled(confirmButtonElm, disabled);

    if (selectElm) {
        selectElm.classList.toggle('is-disabled', disabled);
        selectElm.setAttribute('aria-disabled', disabled.toString());
    }

    if (confirmButtonElm) {
        confirmButtonElm.classList.toggle('is-disabled', disabled);
        confirmButtonElm.setAttribute('aria-disabled', disabled.toString());
    }
}

function syncMaxUniqueKeyFromState(state) {

    if (!state || !Array.isArray(state.movableElementsSnapshot)) {
        maxUniqueKey = 1;
        return;
    }

    let maxFound = 0;

    function traverse(snapshot) {

        if (!snapshot || typeof snapshot !== 'object') return;

        // data-unique-key を見る
        if (snapshot.data && snapshot.data['data-unique-key']) {
            const v = Number(snapshot.data['data-unique-key']);
            if (Number.isFinite(v) && v > maxFound) {
                maxFound = v;
            }
        }

        // id からも保険で拾う
        if (snapshot.id) {
            const m = String(snapshot.id).match(/^movableContainer(\d+)$/);
            if (m) {
                const v = Number(m[1]);
                if (Number.isFinite(v) && v > maxFound) {
                    maxFound = v;
                }
            }
        }

        if (Array.isArray(snapshot.children)) {
            snapshot.children.forEach(traverse);
        }
    }

    state.movableElementsSnapshot.forEach(traverse);

    maxUniqueKey = maxFound + 1;
}
