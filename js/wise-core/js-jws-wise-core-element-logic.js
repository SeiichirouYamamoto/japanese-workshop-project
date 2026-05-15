function createTextAreaContainer(str_text, point_x, point_y){

    let int_unique_key = maxUniqueKey;

    let elm_textAreaContainerTextArea = document.createElement('textarea');
    elm_textAreaContainerTextArea.setAttribute('id', 'textAreaContainerTextArea' + maxUniqueKey);
    elm_textAreaContainerTextArea.classList.add(
        'textAreaContainerTextArea',
        'innerContainerTextArea',
        'selectableContainer'
    );
    elm_textAreaContainerTextArea.cols = 10;
    elm_textAreaContainerTextArea.rows = 1;
    elm_textAreaContainerTextArea.style.fontSize = wiseMovableContainerFontSize + 'px';
    elm_textAreaContainerTextArea.dataset.uniqueKey = int_unique_key;

    if (str_text.length > LENGTH_EMPTY) {
        elm_textAreaContainerTextArea.value = escapeHTML(str_text);
    }

    let arr_names = [
        ['textAreaContainer'],
        ['textAreaBaseContainer'],
        'textAreaLinkContainer',
        'textAreaLeftLinkContainer',
        'textAreaRightLinkContainer',
        'textAreaLinkMarker',
        'textAreaLeftLinkMarker',
        'textAreaRightLinkMarker',
        'textAreaInnerContainer',
        'textAreaMenuContainer'
    ];

    let arr_dataset = {
        uniqueKey : int_unique_key
    };

    const lastCreatedMovableContainer = createNewContainer(
        arr_dataset,
        elm_textAreaContainerTextArea,
        maxUniqueKey,
        wisePanelWhiteboardViewMainContentArea,
        arr_names,
        point_x,
        point_y
    );

    maxUniqueKey += 1;

    return lastCreatedMovableContainer;
}

function resetSelectedElements(){
	let elms_selectedMovableContainer = document.querySelectorAll('.selectedMovableContainer');
	for(let i = INDEX_FIRST; i < elms_selectedMovableContainer.length; i++){
		let elm_selectedMovableContainer = elms_selectedMovableContainer[i];
		if(!(currentSelectedMovableContainers.includes(elm_selectedMovableContainer))){

			elm_selectedMovableContainer.classList.remove('selectedMovableContainer');

			let elm_selectedMenuContainer = elm_selectedMovableContainer.querySelector('.menuContainer');
			if (elm_selectedMenuContainer !== null) {
				elm_selectedMenuContainer.classList.remove('selectedMenuContainer');
			}

			let elm_selectedTextarea = elm_selectedMovableContainer.querySelector('textarea');
			if(elm_selectedTextarea !== null){
				recordTextareaValues(elm_selectedTextarea);
				elm_selectedTextarea.blur();
			}
		}
	}
}

function recordTextareaValues(elm_selectedTextarea){
	let str_textareaValue = escapeHTML(elm_selectedTextarea.value);

	const check_textareaValue = str_textareaValue.replace(/[\s\u3000]/g, '');
	if (check_textareaValue === '') {
		return;
	}

	let doHave = recordedTextareaValues.includes(str_textareaValue);

	if(!doHave){
		recordedTextareaValues.push(str_textareaValue);
		saveState(STATE_TITLE_EDIT_TEXTAREA[intSelectedLanguage]);
	}
}

function deleteElement(elm_targetContainer){

	let classList_targetContainer = elm_targetContainer.classList.value;
	if(classList_targetContainer.indexOf('movableContainer') !== -1){
		clearLinkIdData(elm_targetContainer);
		removeLinkedCanvases(elm_targetContainer);
	}

	elm_targetContainer.remove();
	const index = currentSelectedMovableContainers.indexOf(elm_targetContainer);
	if (index > -1) {
		currentSelectedMovableContainers.splice(index, 1);
	}
	
}

function removeLinkedCanvases(elm_movableContainer){

	let uniqueKey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey),
		elms_canvas = document.querySelectorAll('[data-left-link-id="'+uniqueKey_movableContainer+'"],[data-right-link-id="'+uniqueKey_movableContainer+'"]'),
		elm_canvas;

	if(elms_canvas.length === LENGTH_EMPTY)return;
	for(let i = INDEX_FIRST; i < elms_canvas.length; i++){
		elm_canvas = elms_canvas[i];
		elm_canvas.remove();
	}
}

function clearLinkIdData(elm){
	let uniqueKey_elm = escapeNumber(elm.dataset.uniqueKey),
		elms_movableContainer = document.querySelectorAll('[data-link-id="'+uniqueKey_elm+'"]'),
		elm_movableContainer;

	if(elms_movableContainer.length === LENGTH_EMPTY)return;
	for(let i = INDEX_FIRST; i < elms_movableContainer.length; i++){
		elm_movableContainer = elms_movableContainer[i];
		delete elm_movableContainer.dataset.linkId;
		delete elm_movableContainer.dataset.linkType;
	}
}


function changeEraserSize(doIncreaseEraserSize){

	if(doIncreaseEraserSize){
		currentEraserSize += 10;
	}else{
		if(currentEraserSize > 10){
			currentEraserSize -= 10;
		}
	}

	let screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	let screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;


	let elm_eraserSpan = document.getElementById('eraserSpan');
		elm_eraserSpan.style.width = currentEraserSize+'px';
		elm_eraserSpan.style.height = currentEraserSize+'px';
		elm_eraserSpan.style.left = screenWidth/2 + 'px';
		elm_eraserSpan.style.top = screenHeight/2 + 'px';
		elm_eraserSpan.style.visibility = 'visible';

	if (eraserSizeTimeoutID) {
		clearTimeout(eraserSizeTimeoutID);
	}

	eraserSizeTimeoutID = setTimeout(() => {
		elm_eraserSpan.style.visibility = 'hidden';
		eraserSizeTimeoutID = null;
	}, 500);

}

function updateLinkContainerWidths(){

	let elms = document.querySelectorAll('.movableContainer'),
		elm,
		uniqueKey_elm,
		elm_baseContainer,
		elm_leftLinkContainer,
		elm_rightLinkContainer,
		elm_baseContainer_width,
		elm_baseContainer_height;

	for(let i = INDEX_FIRST; i < elms.length; i++){
		elm = elms[i];
		uniqueKey_elm = escapeNumber(elm.dataset.uniqueKey);
		elm_baseContainer = elm.querySelector('.baseContainer');
		elm_leftLinkContainer = elm.querySelector('.leftLinkContainer');
		elm_rightLinkContainer = elm.querySelector('.rightLinkContainer');
		elm_width = escapeFloatNumber(elm.offsetWidth);
		elm_height = escapeFloatNumber(elm.offsetHeight);
		elm_baseContainer_width = escapeFloatNumber(elm_baseContainer.offsetWidth);
		elm_baseContainer_height = escapeFloatNumber(elm_baseContainer.offsetHeight);

		elm_leftLinkContainer.style.width = (elm_width/2)+'px';
		elm_rightLinkContainer.style.width = (elm_width/2)+'px';
		elm_leftLinkContainer.style.height = elm_height+'px';
		elm_rightLinkContainer.style.height = elm_height+'px';
	}
}

function updateAppearanceAreaGap(str_text){

	let elm_checkSizeSpan = document.createElement('span');
	elm_checkSizeSpan.setAttribute('id', 'checkSizeSpan');
	elm_checkSizeSpan.classList.add('wordContainer');
	elm_checkSizeSpan.textContent = escapeHTML(str_text);
	elm_checkSizeSpan.style.visibility = 'hidden';
	elm_checkSizeSpan.style.fontSize = wiseMovableContainerFontSize + 'px';
	document.body.appendChild(elm_checkSizeSpan);
	appearanceAreaGapWidth = (elm_checkSizeSpan.offsetWidth) + 20;
	appearanceAreaGapHeight = (elm_checkSizeSpan.offsetHeight) + 20;
	elm_checkSizeSpan.remove();
}


function sliceElements(elm_targetContainer) {

    let target_classNaming = null;

    switch (true) {
        case elm_targetContainer.classList.contains('textAreaContainer'):
            target_classNaming = '.textAreaContainerTextArea';
            break;
        case elm_targetContainer.classList.contains('stickyNoteContainer'):
            target_classNaming = '.stickyNoteContainerTextArea';
            break;
        default:
            return;
    }

    let elm_targetContainerTextArea = elm_targetContainer.querySelector(target_classNaming);
    if (elm_targetContainerTextArea === null) return;

    let elm_coordinateBase = wiseZoomStage;

    if (elm_coordinateBase === null) return;

    let bounds_targetContainer = elm_targetContainer.getBoundingClientRect();
    let localRect_targetContainer = convertClientRectToLocalRect(bounds_targetContainer, elm_coordinateBase);

    let appearanceArea_left = localRect_targetContainer.left;
    let appearanceArea_top = localRect_targetContainer.top;

    let str_targetContainerTextArea = escapeHTML(elm_targetContainerTextArea.value);

    let arr_targetContainerTextArea = str_targetContainerTextArea
        .split('\n')
        .map(item => item.trim())
        .filter(item => item !== '');

    if (arr_targetContainerTextArea.length === LENGTH_EMPTY) return;

    for (let i = INDEX_FIRST; i < arr_targetContainerTextArea.length; i++) {
        let str_text_i = arr_targetContainerTextArea[i];
        if (str_text_i.length > LENGTH_EMPTY) {
            let localRect_lastCreated = null;

            let arr_text = str_text_i
                .split('/')
                .map(item => item.trim())
                .filter(item => item !== '');

            for (let j = INDEX_FIRST; j < arr_text.length; j++) {
                let str_text_j = arr_text[j];
                if (str_text_j.length > LENGTH_EMPTY) {
                    let lastCreatedMovableContainer = null;

                    if (target_classNaming === '.textAreaContainerTextArea') {
                        lastCreatedMovableContainer = createTextAreaContainer(
							str_text_j,
							appearanceArea_left,
							appearanceArea_top
						);
                    }
                    else {
                        lastCreatedMovableContainer = createStickyNoteContainer(
                            escapeHTML(elm_targetContainer.dataset.color),
                            str_text_j,
                            appearanceArea_left,
                            appearanceArea_top
                        );
                    }

                    if (lastCreatedMovableContainer === null) continue;

                    let elm_movableContainer_lastCreated_innerContainerTextArea =
                        lastCreatedMovableContainer.querySelector('.innerContainerTextArea');

                    if (elm_movableContainer_lastCreated_innerContainerTextArea !== null) {
                        updateTextareaContainerSize(elm_movableContainer_lastCreated_innerContainerTextArea, str_text_j);
                    }

                    let bounds_lastCreated = lastCreatedMovableContainer.getBoundingClientRect();
                    localRect_lastCreated = convertClientRectToLocalRect(bounds_lastCreated, elm_coordinateBase);

                    appearanceArea_left = localRect_lastCreated.right;
                }
            }

            appearanceArea_left = localRect_targetContainer.left;

            if (localRect_lastCreated !== null) {
                appearanceArea_top = localRect_lastCreated.bottom;
            }
        }
    }

    deleteElement(elm_targetContainer);
    saveState(STATE_TITLE_DELETE_CONTAINER[intSelectedLanguage]);
	
}

function createWisePalette(targetElm, options = {}) {
	
    const {
        extractSelectors = ['p'],
        colors = ['White', 'Red', 'Blue', 'Green', 'Orange', 'Yellow'],
        colorClassMap = {
            White: 'stickyNoteBaseContainerColorWhite',
            Red: 'stickyNoteBaseContainerColorRed',
            Blue: 'stickyNoteBaseContainerColorBlue',
            Green: 'stickyNoteBaseContainerColorGreen',
            Orange: 'stickyNoteBaseContainerColorOrange',
            Yellow: 'stickyNoteBaseContainerColorYellow'
        },
        squareSize = 44
    } = options;

    const container = document.createElement('div');
    container.classList.add('wisePaletteContainer');
    container.style.display = 'flex';
    container.style.alignItems = 'center';
    container.style.flexWrap = 'wrap';
    container.style.gap = '8px';
    container.style.marginBottom = '8px';

    const button = document.createElement('button');
    button.type = 'button';
    button.classList.add('wisePaletteButton');
    button.textContent = 'Palette';

    const palette = document.createElement('div');
    palette.classList.add('wisePalette', 'hidden');
    palette.style.display = 'flex';
    palette.style.flexWrap = 'wrap';
    palette.style.gap = '6px';
    palette.style.alignItems = 'center';

    colors.forEach(function (name) {
        const sq = document.createElement('div');
        sq.classList.add('wisePaletteColorSquare', 'SelectColor' + name);
        sq.dataset.colorName = name;
        sq.style.width = squareSize + 'px';
        sq.style.height = squareSize + 'px';
        sq.style.cursor = 'pointer';
		sq.style.boxSizing = 'border-box';
		sq.style.border = '1px solid rgba(0,0,0,.2)';
		sq.style.borderRadius = '6px';

        sq.addEventListener('pointerup', function () {
            const classNaming_color = colorClassMap[name];

            const selector = extractSelectors.join(',');
            const list = Array.from(targetElm.querySelectorAll(selector))
                .filter(function (n) { return !container.contains(n); });

            const seen = new Set();
            const nodes = [];
            for (let i = 0; i < list.length; i++) {
                const el = list[i];
                if (!seen.has(el)) {
                    seen.add(el);
                    nodes.push(el);
                }
            }

            const texts = [];
            for (let i = 0; i < nodes.length; i++) {
                const t = (nodes[i].textContent || '').trim();
                const limit = (typeof LENGTH_EMPTY === 'number') ? LENGTH_EMPTY : 0;
                if (t.length > limit) texts.push(t);
            }

            const str_text = texts.join('\n');
			
			const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

			lastCreatedMovableContainer = createStickyNoteContainer(classNaming_color, str_text, point.x, point.y);
			updateTextareaContainerSize(lastCreatedMovableContainer.querySelector('.innerContainerTextArea'), str_text);

			advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, str_text);

        }, false);

        palette.appendChild(sq);
    });

    button.addEventListener('pointerup', function () {
        palette.classList.toggle('hidden');
    }, false);

    container.appendChild(button);
    container.appendChild(palette);
    return container;
}

function createStickyNoteContainer(classNaming_color, str_text, point_x, point_y){

	let int_unique_key = maxUniqueKey;

	let elm_stickyNoteContainerTextArea = document.createElement('textarea');
	elm_stickyNoteContainerTextArea.setAttribute('id', 'stickyNoteContainerTextArea'+maxUniqueKey);
	elm_stickyNoteContainerTextArea.classList.add(
		'stickyNoteContainerTextArea',
		'innerContainerTextArea',
		'selectableContainer'
	);
	elm_stickyNoteContainerTextArea.cols = 10;
	elm_stickyNoteContainerTextArea.rows = 1;
	elm_stickyNoteContainerTextArea.style.fontSize = wiseMovableContainerFontSize + 'px';
	elm_stickyNoteContainerTextArea.dataset.uniqueKey = int_unique_key;

	if(str_text.length > LENGTH_EMPTY){
		elm_stickyNoteContainerTextArea.value = escapeHTML(str_text);
	}

	let arr_dataset = {
		uniqueKey : int_unique_key,
		color : classNaming_color
	};

	let arr_names = [
		['stickyNoteContainer', classNaming_color],
		['stickyNoteBaseContainer', classNaming_color],
		'stickyNoteLinkContainer',
		'stickyNoteLeftLinkContainer',
		'stickyNoteRightLinkContainer',
		'stickyNoteLinkMarker',
		'stickyNoteLeftLinkMarker',
		'stickyNoteRightLinkMarker',
		'stickyNoteInnerContainer',
		'stickyNoteMenuContainer'
	];

	const lastCreatedMovableContainer = createNewContainer(arr_dataset, elm_stickyNoteContainerTextArea, maxUniqueKey, wisePanelWhiteboardViewMainContentArea, arr_names, point_x, point_y);

	maxZIndex += 1;
	maxUniqueKey += 1;

	return lastCreatedMovableContainer;

}

function getAppearanceBasePoint(
    elm,
    offsetX = APPEARANCE_AREA_DEFAULT,
    offsetY = APPEARANCE_AREA_DEFAULT
) {

    const targetElm = elm || wisePanelWhiteboardViewMainContentArea;

    const visiblePoint = getCreatePointFromVisibleArea(
        targetElm,
        offsetX,
        offsetY
    );

    if (wiseToolbarContainer === null) {
        return visiblePoint;
    }

    const toolbarWidth = wiseToolbarContainer.offsetWidth;

    return {
        x: visiblePoint.x + toolbarWidth + 10,
        y: visiblePoint.y
    };
}

function getAppearanceCreatePoint(
    elm,
    layoutState,
    offsetX = APPEARANCE_AREA_DEFAULT,
    offsetY = APPEARANCE_AREA_DEFAULT
) {

    const basePoint = getAppearanceBasePoint(elm, offsetX, offsetY);

    return {
        x: basePoint.x + layoutState.nextX,
        y: basePoint.y + layoutState.nextY
    };
}

function advanceAppearanceOrder(
    elm,
    layoutState,
    str_text,
    offsetX = APPEARANCE_AREA_DEFAULT,
    offsetY = APPEARANCE_AREA_DEFAULT
) {

    updateAppearanceAreaGap(str_text);

    const elementWidth = appearanceAreaGapWidth;
    const elementHeight = appearanceAreaGapHeight;

    const scrollElm = getScrollContainer(elm);
    const zoomScale = getWiseZoomScale();
    const visibleTopLeft = getVisibleLocalTopLeft(elm);
    const visibleWidth = (scrollElm ? scrollElm.clientWidth : window.innerWidth) / zoomScale;
    const visibleHeight = (scrollElm ? scrollElm.clientHeight : window.innerHeight) / zoomScale;

    const visibleRight = visibleTopLeft.x + visibleWidth;
    const visibleBottom = visibleTopLeft.y + visibleHeight;

    layoutState.currentColumnMaxWidth = Math.max(
        layoutState.currentColumnMaxWidth,
        elementWidth
    );

    layoutState.nextY = layoutState.nextY + elementHeight;
    layoutState.row += 1;

    const nextPoint = getAppearanceCreatePoint(elm, layoutState, offsetX, offsetY);

    if (nextPoint.y + elementHeight > visibleBottom) {
        layoutState.column += 1;
        layoutState.row = 0;
        layoutState.nextX = layoutState.nextX + layoutState.currentColumnMaxWidth;
        layoutState.nextY = 0;
        layoutState.currentColumnMaxWidth = 0;
    }

    const wrappedPoint = getAppearanceCreatePoint(elm, layoutState, offsetX, offsetY);

    if (wrappedPoint.x + elementWidth > visibleRight) {
        layoutState.row = 0;
        layoutState.column = 0;
        layoutState.nextX = 0;
        layoutState.nextY = 0;
        layoutState.currentColumnMaxWidth = 0;
    }
}

function resetAppearanceLayoutState(layoutState) {

    layoutState.row = 0;
    layoutState.column = 0;
    layoutState.nextX = 0;
    layoutState.nextY = 0;
    layoutState.currentColumnMaxWidth = 0;

}

function createNewWord(str_japanese, str_kana, int_sub_classification_id, point_x, point_y){

	let int_japanese_id = DEFAULT_JAPANESE_ID,
		int_japanese_element_id = DEFAULT_JAPANESE_ELEMENT_ID,
		int_form_id = DEFAULT_FORM_ID,
		int_unique_key = maxUniqueKey;

	let arr_dataset = {
		uniqueKey : int_unique_key,
		japaneseId : int_japanese_id,
		japaneseElementId : int_japanese_element_id,
		subClassificationId : int_sub_classification_id,
		formId : int_form_id,
		labelId : DEFAULT_LABEL_ID,
		voiceId : DEFAULT_VOICE_ID,
		japanese : str_japanese,
		kana : str_kana,
		subClassification : '',
		form : '',
		voice : ''
	};

	let arr_names = [
		['wordContainer'],
		['wordBaseContainer'],
		'wordLinkContainer',
		'wordLeftLinkContainer',
		'wordRightLinkContainer',
		'wordLinkMarker',
		'wordLeftLinkMarker',
		'wordRightLinkMarker',
		'wordInnerContainer',
		'wordMenuContainer'
	];
	
	const lastCreatedMovableContainer = createNewContainer(arr_dataset, str_japanese, int_unique_key, wisePanelWhiteboardViewMainContentArea, arr_names, point_x, point_y);

	maxUniqueKey += 1;

	return lastCreatedMovableContainer;

}

function createNewContainer(arr_dataset, add_content, int_unique_key, appendTarget, arr_names, appearanceArea_left, appearanceArea_top){

	let str_container_id = 'movableContainer'+int_unique_key,
		str_baseContainer_id = 'baseContainer'+int_unique_key,
		str_leftLinkContainer_id = 'leftLinkContainer'+int_unique_key,
		str_leftLinkMarker_id = 'leftLinkMarker'+int_unique_key,
		str_rightLinkContainer_id = 'rightLinkContainer'+int_unique_key,
		str_rightLinkMarker_id = 'rightLinkMarker'+int_unique_key,
		str_innerContainer_id = 'innerContainer'+int_unique_key,
		str_menuContainer_id = 'menuContainer'+int_unique_key;

	let elm_innerContainer = document.createElement('div');
		elm_innerContainer.setAttribute('id', str_innerContainer_id);
		elm_innerContainer.classList.add('innerContainer', arr_names[INDEX_NINTH], 'selectableContainer');
		elm_innerContainer.style.fontSize = wiseMovableContainerFontSize + 'px';
		elm_innerContainer.dataset.uniqueKey = int_unique_key;


	if(add_content instanceof Element){
		elm_innerContainer.appendChild(add_content);
	}
	else{
		elm_innerContainer.textContent = add_content;
	}

	let elm_baseContainer = document.createElement('div');
		elm_baseContainer.setAttribute('id', str_baseContainer_id);
		elm_baseContainer.classList.add('baseContainer', 'selectableContainer');
		elm_baseContainer.classList.add(...arr_names[INDEX_SECOND]);
		elm_baseContainer.dataset.uniqueKey = int_unique_key;
		elm_baseContainer.appendChild(elm_innerContainer);

	let elm_leftLinkMarker = document.createElement('div');
		elm_leftLinkMarker.setAttribute('id', str_leftLinkMarker_id);
		elm_leftLinkMarker.classList.add('linkMarker', 'leftLinkMarker', arr_names[INDEX_SIXTH], arr_names[INDEX_SEVENTH]);
		elm_leftLinkMarker.dataset.linkMarkerType = 'leftLinkMarker';
		elm_leftLinkMarker.dataset.uniqueKey = int_unique_key;

	let elm_leftLinkContainer = document.createElement('div');
		elm_leftLinkContainer.setAttribute('id', str_leftLinkContainer_id);
		elm_leftLinkContainer.classList.add('linkContainer', 'leftLinkContainer', 'selectableContainer', arr_names[INDEX_THIRD], arr_names[INDEX_FOURTH]);
		elm_leftLinkContainer.style.width = linkContainerWidth+'px';
		elm_leftLinkContainer.dataset.linkMarkerType = 'leftLinkMarker';
		elm_leftLinkContainer.dataset.uniqueKey = int_unique_key;

	let elm_rightLinkMarker = document.createElement('div');
		elm_rightLinkMarker.setAttribute('id', str_rightLinkMarker_id);
		elm_rightLinkMarker.classList.add('linkMarker', 'rightLinkMarker', arr_names[INDEX_SIXTH], arr_names[INDEX_EIGHTH]);
		elm_rightLinkMarker.dataset.linkMarkerType = 'rightLinkMarker';
		elm_rightLinkMarker.dataset.uniqueKey = int_unique_key;

	let elm_menuContainer = document.createElement('div');
		elm_menuContainer.setAttribute('id', str_menuContainer_id);
		elm_menuContainer.classList.add('menuContainer', arr_names[INDEX_TENTH]);
		elm_menuContainer.dataset.uniqueKey = int_unique_key;

	let elm_rightLinkContainer = document.createElement('div');
		elm_rightLinkContainer.setAttribute('id', str_rightLinkContainer_id);
		elm_rightLinkContainer.classList.add('linkContainer', 'rightLinkContainer', 'selectableContainer', arr_names[INDEX_THIRD], arr_names[INDEX_FIFTH]);
		elm_rightLinkContainer.style.width = linkContainerWidth+'px';
		elm_rightLinkContainer.dataset.linkMarkerType = 'rightLinkMarker';
		elm_rightLinkContainer.dataset.uniqueKey = int_unique_key;

	let elm_container = document.createElement('div');
		elm_container.setAttribute('id', str_container_id);
		elm_container.classList.add('movableContainer', 'selectableContainer');
		elm_container.classList.add(...arr_names[INDEX_FIRST]);
		elm_container.style.zIndex = maxZIndex;
		elm_container.style.left = appearanceArea_left+'px';
		elm_container.style.top = appearanceArea_top+'px';

	for (let key in arr_dataset) {
		elm_container.dataset[key] = arr_dataset[key];
	}

	elm_container.appendChild(elm_leftLinkMarker);
	elm_container.appendChild(elm_leftLinkContainer);
	elm_container.appendChild(elm_baseContainer);
	elm_container.appendChild(elm_rightLinkMarker);
	elm_container.appendChild(elm_menuContainer);
	elm_container.appendChild(elm_rightLinkContainer);

	appendTarget.appendChild(elm_container);

	maxZIndex += 1;

	saveState(STATE_TITLE_CREATE_MOVABLE_CONTAINER[intSelectedLanguage][INDEX_FIRST] + arr_names[INDEX_FIRST][INDEX_FIRST] + STATE_TITLE_CREATE_MOVABLE_CONTAINER[intSelectedLanguage][INDEX_SECOND]);
	updateLinkContainerWidths();

	return elm_container;
}


async function createWordContainers(payload = null, point_x, point_y) {

    const arrWordContainerData = await getWordContainerData(payload);

    if (!Array.isArray(arrWordContainerData) || arrWordContainerData.length === LENGTH_EMPTY) {
        return null;
    }

    for (let i = INDEX_FIRST; i < arrWordContainerData.length; i++) {

        const int_japanese_id = escapeNumber(arrWordContainerData[i].japaneseId);
        const int_japanese_element_id = escapeNumber(arrWordContainerData[i].japaneseElementId);
        const int_sub_classification_id = escapeNumber(arrWordContainerData[i].subClassificationId);
        const int_form_id = escapeNumber(arrWordContainerData[i].formId);
        const int_voice_id = escapeNumber(arrWordContainerData[i].voiceId);
        const int_label_id = escapeNumber(arrWordContainerData[i].labelId);

        const str_japanese = escapeHTML(arrWordContainerData[i].japanese);
        const str_kana = escapeHTML(arrWordContainerData[i].kana);
        const str_sub_classification = escapeHTML(arrWordContainerData[i].subClassification);
        const str_form = escapeHTML(arrWordContainerData[i].form);
        const str_voice = escapeHTML(arrWordContainerData[i].voice);

        const int_unique_key = maxUniqueKey;

        politePlainFormHistory[int_japanese_element_id] = {
            int_label_id: int_label_id,
            str_japanese: str_japanese
        };

        const arr_dataset = {
            uniqueKey: int_unique_key,
            japaneseId: int_japanese_id,
            japaneseElementId: int_japanese_element_id,
            subClassificationId: int_sub_classification_id,
            formId: int_form_id,
            labelId: int_label_id,
            voiceId: int_voice_id,
            japanese: str_japanese,
            kana: str_kana,
            subClassification: str_sub_classification,
            form: str_form,
            voice: str_voice
        };

        const arr_names = [
            ['wordContainer'],
            ['wordBaseContainer'],
            'wordLinkContainer',
            'wordLeftLinkContainer',
            'wordRightLinkContainer',
            'wordLinkMarker',
            'wordLeftLinkMarker',
            'wordRightLinkMarker',
            'wordInnerContainer',
            'wordMenuContainer'
        ];
		
        createNewContainer(
            arr_dataset,
            str_japanese,
            int_unique_key,
            wisePanelWhiteboardViewMainContentArea,
            arr_names,
            point_x,
            point_y
        );

        maxUniqueKey += 1;
    }
}

function createWordContainerFromRegisteredElement(element, doAlign) {

    const int_unique_key = maxUniqueKey;

    const arr_dataset = {
        uniqueKey: int_unique_key,
        japaneseId: escapeNumber(element.japaneseId),
        japaneseElementId: escapeNumber(element.japaneseElementId),
        subClassificationId: escapeNumber(element.subClassificationId),
        formId: escapeNumber(element.formId),
        labelId: escapeNumber(element.labelId),
        voiceId: escapeNumber(element.voiceId),
        japanese: escapeHTML(element.japanese),
        kana: escapeHTML(element.kana),
        subClassification: escapeHTML(element.subClassification ?? ''),
        form: escapeHTML(element.form ?? ''),
        voice: escapeHTML(element.voice ?? ''),
        idName: escapeHTML(element.idName ?? '')
    };

    const arr_names = [
        ['wordContainer'],
        ['wordBaseContainer'],
        'wordLinkContainer',
        'wordLeftLinkContainer',
        'wordRightLinkContainer',
        'wordLinkMarker',
        'wordLeftLinkMarker',
        'wordRightLinkMarker',
        'wordInnerContainer',
        'wordMenuContainer'
    ];

    const str_japanese = arr_dataset.japanese;

    const appearance_left = doAlign ? escapeFloatNumber(element.boundsLeft) : APPEARANCE_AREA_DEFAULT;
    const appearance_top = doAlign ? escapeFloatNumber(element.boundsTop) : APPEARANCE_AREA_DEFAULT;
	
    const elm_created = createNewContainer(
        arr_dataset,
        str_japanese,
        int_unique_key,
        wisePanelWhiteboardViewMainContentArea,
        arr_names,
        appearance_left,
        appearance_top
    );

    politePlainFormHistory[arr_dataset.japaneseElementId] = {
        int_label_id: arr_dataset.labelId,
        str_japanese: arr_dataset.japanese
    };

    maxUniqueKey += 1;

    return elm_created;
}


async function getRegisteredSentenceElements(searchCriteria, searchById) {

    const payload = {
        searchCriteria: searchCriteria,
        searchById: searchById ? FLAG_TRUE : FLAG_FALSE,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        sentenceGetElementsByRegisteredSentenceUrl,
        payload,
        10000
    );

    return Array.isArray(result.data) ? result.data : [];
}

async function reviewWordContainers(send_sentence_unique_code, doAlign) {

    const arr_registered_sentence_elements =
        await getRegisteredSentenceElements(send_sentence_unique_code, false);

    if (arr_registered_sentence_elements.length === LENGTH_EMPTY) {
        return [];
    }

    const arr_wordContainers = [];

    for (let i = INDEX_FIRST; i < arr_registered_sentence_elements.length; i++) {

        const element = arr_registered_sentence_elements[i];

        try {
            const elm_created = createWordContainerFromRegisteredElement(element, doAlign);
            if (elm_created !== null) {
                arr_wordContainers.push(elm_created);
            }
        } catch (e) {
            console.error('reviewWordContainers restore error:', e.message || e);
            continue;
        }
    }

    return arr_wordContainers;
}


function createPhraseClauseContainer(elm_movableContainer){

	let elm_movableContainer_parentPhraseClause = elm_movableContainer;

	let last_width = elm_movableContainer.offsetWidth,
		after_width,
		width_difference;

	let arr_phraseClauseContainer_info_collection = getPhraseClauseContainerInfoCollection(elm_movableContainer, false);
	let arr_link_id_add_sort = arr_phraseClauseContainer_info_collection['arr_link_id_add_sort'];

	if(arr_phraseClauseContainer_info_collection['doesNotHaveWordContainerClass']){
		alert(MSG_ERROR_INVALID_WORD_CONTAINER[intSelectedLanguage]);
		return;
	}

	let arr_movableContainers = arr_phraseClauseContainer_info_collection['arr_movableContainers'];
	let str_group_of_words_japanese = escapeHTML(arr_phraseClauseContainer_info_collection['str_group_of_words_japanese']),
		str_group_of_words_kana = escapeHTML(arr_phraseClauseContainer_info_collection['str_group_of_words_kana']),
		str_group_of_words_japanesePhraseClause = escapeHTML(arr_phraseClauseContainer_info_collection['str_group_of_words_japanesePhraseClause']),
		str_group_of_words_kanaPhraseClause = escapeHTML(arr_phraseClauseContainer_info_collection['str_group_of_words_kanaPhraseClause']);

	elm_movableContainer.classList.add('phraseClauseContainer');
	elm_movableContainer.dataset.japanesePhraseClause = str_group_of_words_japanesePhraseClause;
	elm_movableContainer.dataset.kanaPhraseClause = str_group_of_words_kanaPhraseClause;
	contextMenuTargetInnerContainer.textContent = str_group_of_words_japanese;

	after_width = elm_movableContainer.offsetWidth;
	width_difference = after_width-last_width;

	let currentPosition = parseInt(elm_movableContainer.style.left) || 0;
	let newPosition = currentPosition - width_difference;

	if(!elm_movableContainer.classList.contains('phraseClauseContainer')){
		elm_movableContainer.style.left = newPosition + 'px';
	}

	if('childElms' in arr_movableContainers){
		preparePhraseClauseMaterialCheckRecursively(elm_movableContainer_parentPhraseClause, arr_movableContainers);
		hidePhraseClauseMaterialsRecursively(elm_movableContainer_parentPhraseClause, arr_movableContainers);
	}

	markPhraseClauseTopElement(arr_link_id_add_sort);

	return;

}

function markPhraseClauseTopElement(arr_link_id_add_sort){
	const filteredArr = arr_link_id_add_sort.filter(item => item.hasOwnProperty("sort") && item.sort === SORT_FIRST);
	const result = filteredArr.length > LENGTH_EMPTY ? filteredArr[INDEX_FIRST].idName : null;

	const elm_targetContainer = document.getElementById(result);
	elm_targetContainer.classList.add('itemOfPhraseClauseContainerTopElement');

}

function hidePhraseClauseMaterialsRecursively(elm_movableContainer_parentPhraseClause, arr_movableContainers){

	let idName_movableContainer = arr_movableContainers['idName'],
		uniqueKey_movableContainer_parentPhraseClause = escapeNumber(elm_movableContainer_parentPhraseClause.dataset.uniqueKey),
		elm_movableContainer = document.getElementById(idName_movableContainer),
		childElms,
		childElms_current;

	if(elm_movableContainer_parentPhraseClause !== elm_movableContainer){

		elm_movableContainer.classList.add('itemOfPhraseClauseContainer');
		elm_movableContainer.dataset.phraseClauseId = uniqueKey_movableContainer_parentPhraseClause;

		let uniqueKey_movableContainer = escapeNumber(elm_movableContainer.dataset.uniqueKey),
			elms_canvas = document.querySelectorAll('[data-left-link-id="'+uniqueKey_movableContainer+'"]'),
			elm_canvas;

		if(elms_canvas.length !== LENGTH_EMPTY){
			for(let i = INDEX_FIRST; i < elms_canvas.length; i++){
				elm_canvas = elms_canvas[i];
				elm_canvas.classList.add('itemOfPhraseClauseContainer');
			}
		}
	}

	if('childElms' in arr_movableContainers){
		childElms = arr_movableContainers['childElms'];
		for(let i = INDEX_FIRST; i < childElms.length; i++){
			childElms_current = childElms[i];
			hidePhraseClauseMaterialsRecursively(elm_movableContainer_parentPhraseClause, childElms_current);
		}
	}
	return;
}

function preparePhraseClauseMaterialCheckRecursively(elm_movableContainer_parentPhraseClause, arr_movableContainers){

	let idName_movableContainer = arr_movableContainers['idName'],
		elm_movableContainer = document.getElementById(idName_movableContainer),
		childElms,
		childElms_current;

	if(elm_movableContainer_parentPhraseClause !== elm_movableContainer){
		if(elm_movableContainer.classList.contains('phraseClauseContainer')){
			purgePhraseClauseContainer(elm_movableContainer);
		}
	}

	if('childElms' in arr_movableContainers){
		childElms = arr_movableContainers['childElms'];
		for(let i = INDEX_FIRST; i < childElms.length; i++){
			childElms_current = childElms[i];
			preparePhraseClauseMaterialCheckRecursively(elm_movableContainer_parentPhraseClause, childElms_current);
		}
	}
	return;
}

async function recreateGrammarExplanation(grammar_unique_code, options = {}) {
    const {
        indexToDisplay = null,
        suppressHide = false,
        useLoading = true
    } = options;

    if (!grammar_unique_code) return;

    const selector = '.wisePanelGrammarExplanationViewMainContentAreaContents[data-unique-code="' + grammar_unique_code + '"]';
    const elements = document.querySelectorAll(selector);
    elements.forEach(elm => {
        if (elm.parentNode) {
            elm.parentNode.removeChild(elm);
        }
    });

    if (useLoading) {
        grammarExplanationLoading.classList.remove('loading-hidden');
    }

    try {
        await createGrammarExplanation(grammar_unique_code, { suppressHide });
        if (indexToDisplay !== null) {
            displayCurrentStateGrammarExplanation(indexToDisplay, true);
        }
    } finally {
        if (useLoading) {
            grammarExplanationLoading.classList.add('loading-hidden');
        }
    }
}

function resetGrammarInsightsDisplayArea(){
	document.querySelectorAll('.grammarInsightsDisplayArea').forEach(elm => {
		elm.classList.add('hidden');
		if(elm === grammarInsightsHomeworkArea){
			
			const elements = document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaLiResetTarget');
			elements.forEach(elm => {
				elm.remove();
			});
			
			document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainer').forEach(elm => {
				elm.classList.add('hidden');
				elm.classList.remove('selected');
			});
			
			document.querySelectorAll('.grammarInsightsHomeworkLinkDisplayAreaLiCheckbox').forEach(checkBox => {
				checkBox.checked = false;
			});

			const day1Button = document.querySelector('.grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector[data-day="1"]');
			if (day1Button) {
				switchGrammarInsightsHomeworkDay(day1Button);
			}
			
		}
		else if(elm !== grammarInsightsButtonsArea){
			elm.replaceChildren();
		}
	});
}

function renderWiseMapContentContainer(toggleElm, items, isForcusPoint, isNavi) {
    const container = document.createElement('div');
    container.classList.add('wiseMapUXWaypointContainer');

	if(isNavi){
		container.classList.add('newRendered');
	}
	else{
		const deleteBtn = document.createElement('button');
		deleteBtn.textContent = '✖';
		deleteBtn.classList.add('wiseMapDeleteButton');
		deleteBtn.addEventListener('pointerup', () => {
			container.remove();
			if (toggleElm) {
				toggleElm.checked = false;
			}
		});
		container.appendChild(deleteBtn);
	}

    const ul = document.createElement('ul');
    ul.classList.add('wiseMapUXWaypointContainerUl');

    items.forEach(item => {
        let grammarUniqueCode = item.item_unique_code;
        let str_title = item.item_title;
        let str_japanese = item.item_title;

        const btn = document.createElement('button');
        btn.name = 'wiseMapShowGrammarExplanation';
        btn.classList.add('wiseMapShowGrammarExplanationButton');
        btn.type = 'button';
        btn.textContent = '説明';

        const textSpan = document.createElement('span');
        textSpan.classList.add('wiseMapTextSpan', 'wiseMapTextEditable');
        textSpan.setAttribute('contenteditable', 'true');

        const li = document.createElement('li');
        li.classList.add('wiseUiFontSizeTarget');

		const controls = document.createElement('div');
		controls.classList.add('wiseMapControlsContainer');
		if (grammarUniqueCode) {
			controls.appendChild(btn);
		}

		if(!isNavi){
			li.insertBefore(controls, li.firstChild);
		}

        if (isForcusPoint) {
            str_japanese = item.item_japanese;
            li.classList.add('wiseMapSentence', 'wiseLearningHiContrast');
            textSpan.innerHTML = str_title;
            li.appendChild(textSpan);

            const paletteElm = createWisePalette(li, {
                extractSelectors: ['.wiseMapTextSpan']
            });

            controls.appendChild(paletteElm);

            ul.appendChild(li);

            if (wiseMapFocusMapArea &&
                typeof wiseMapFocusMapArea.innerHTML === 'string' &&
                wiseMapFocusMapArea.innerHTML.trim() !== ''
			) {

                const separatorLi = document.createElement('li');
                separatorLi.classList.add('wiseMapSeparator', 'wiseUiFontSizeTarget');
                separatorLi.textContent = '▲';
                // ul.appendChild(separatorLi);
            }
        } else {
            li.classList.add('wiseMapUXWaypointContainerLi');
            textSpan.textContent = str_japanese;
            li.appendChild(textSpan);
            ul.appendChild(li);
        }

        li.dataset.uniqueCode = grammarUniqueCode;
        li.dataset.japanese = str_japanese;

        btn.addEventListener('pointerup', async function () {
			
            switchToGrammarExplanationPanel();

            await createGrammarExplanation(grammarUniqueCode);

            const obj_grammarData = {
                grammarUniqueCode: grammarUniqueCode,
                japanese: str_japanese
            };

			handleGrammarExplanationHistoryAndDisplay(obj_grammarData);
			
        });
    });

    container.appendChild(ul);
    return container;
}

async function switchGrammarInsightsDisplayArea(){

    const int_wisePanelGrammarInsightsViewDropDownMenuSelect = escapeNumber(
        grammarInsightsDropdownSelect[grammarInsightsDropdownSelect.selectedIndex].value
    );

    let targetDisplayArea = null;

    switch (int_wisePanelGrammarInsightsViewDropDownMenuSelect) {

        case GRAMMAR_INSIGHTS_DISPLAY_TITLES:
            targetDisplayArea = grammarInsightsTitlesArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_EXAMPLES:
            targetDisplayArea = grammarInsightsExamplesArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_USER_INPUT:
            targetDisplayArea = grammarInsightsUserInputArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_SENTENCES:
            targetDisplayArea = grammarInsightsSentencesArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_RANDOM_SENTENCES:
            targetDisplayArea = grammarInsightsRandomSentencesArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_ACTIVE_RECALL:
            targetDisplayArea = grammarInsightsActiveRecallArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_DOWNLOAD_ITEMS:
            targetDisplayArea = grammarInsightsButtonsArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_CREATE_QUIZ_LINKS:
            targetDisplayArea = grammarInsightsQuizLinksArea;
            break;

        case GRAMMAR_INSIGHTS_DISPLAY_UPSERT_HOMEWORK:
            targetDisplayArea = grammarInsightsHomeworkArea;
            break;

        default:
            return;
    }

    await showGrammarInsightsDisplayArea(targetDisplayArea, false);
}






/******************************************************
 * 
 ******************************************************/

async function searchContentList(
	int_search_scope,
	str_searchWord,
	elm_targetUl,
	elm_targetLoading,
	arr_classNaming_li,
	int_matching_type = MATCHING_TYPE_PARTIAL,
	int_category_id = JP_CATEGORY_GRAMMAR,
	int_sub_category_id = SELECT_ALL,
	int_learningScope = LEARNING_SCOPE_SELECT_ALL
){

	elm_targetUl.replaceChildren();

	if(
		int_learningScope !== LEARNING_SCOPE_ALREADY_LEARNED
		&&
		str_searchWord.length === LENGTH_EMPTY
	){
		let elm_addLi = document.createElement('li');
		elm_addLi.classList.add('resultEmpty');
		elm_addLi.textContent = '検索結果 0件';
		elm_targetUl.appendChild(elm_addLi);
		return;
	}
	
	elm_targetLoading.classList.remove('loading-hidden');

	let payload = {
		int_search_scope: int_search_scope,
		search_word: str_searchWord,
		int_matching_type: int_matching_type,
		int_category_id: int_category_id,
		int_sub_category_id: int_sub_category_id,
		int_learningScope: int_learningScope,
		int_selected_language: intSelectedLanguage
	};

	try {
		const result = await postJson(
			wiseCoreSearchKnowledgeUrl,
			payload,
			10000
		);

		const data = result.data;

		if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
			const elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
			return;
		}

		buildSearchResultListItems(data, elm_targetUl, arr_classNaming_li);

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			alert(error.message || 'Error');
		}
	} finally {
		elm_targetLoading.classList.add('loading-hidden');
	}
}

async function searchContentListByBookmark(
	int_search_scope,
	elm_targetUl,
	elm_targetLoading,
	arr_classNaming_li,
	room_unique_code = 'default',
	int_bookmark_filter = SELECT_ALL,
){

	elm_targetUl.replaceChildren();

	let payload = {
		int_search_scope: int_search_scope,
		room_unique_code: room_unique_code,
		int_bookmark_filter: int_bookmark_filter,
		int_selected_language: intSelectedLanguage
	};

	elm_targetLoading.classList.remove('loading-hidden');

	try {
		const result = await postJson(
			wiseCoreSearchKnowledgeByBookmarkUrl,
			payload,
			10000
		);

		const data = result.data;

		if (!Array.isArray(data) || data.length === LENGTH_EMPTY) {
			const elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
			return;
		}

		buildSearchResultListItems(data, elm_targetUl, arr_classNaming_li);

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			alert(error.message || 'Error');
		}
	} finally {
		elm_targetLoading.classList.add('loading-hidden');
	}
}

function buildSearchResultListItems(json, elm_targetUl, arr_classNaming_li){
				
	for (let i = INDEX_FIRST; i < json.length; i ++) {

		if(json[i] === null){
			continue;
		}
		
		let classNaming_div = arr_classNaming_li[INDEX_FIRST]+'Div';
		let classNaming_button = arr_classNaming_li[INDEX_FIRST]+'Button';

		let int_japanese_id = escapeNumber(json[i].japaneseId);
		let str_unique_code = escapeHTML(json[i].grammarUniqueCode);
		let str_japanese = escapeHTML(json[i].japanese);
		let str_kana = escapeHTML(json[i].kana);
		let int_category_id = escapeNumber(json[i].categoryId);

		let elm_addLi = document.createElement('li');
		elm_addLi.classList.add('searchWordListLi');
		elm_addLi.classList.add(...arr_classNaming_li);
		elm_addLi.dataset.japaneseId = int_japanese_id;
		elm_addLi.dataset.uniqueCode = str_unique_code;
		elm_addLi.dataset.japanese = str_japanese;
		elm_addLi.dataset.kana = str_kana;
		elm_addLi.dataset.categoryId = int_category_id;
		elm_addLi.dataset.index = i;

		bindSearchResultItemBehavior(elm_addLi, arr_classNaming_li);
		
		let elm_addDiv = document.createElement('div');
		elm_addDiv.classList.add('searchWordListLiDiv');
		elm_addDiv.classList.add(classNaming_div);
		elm_addDiv.classList.add('display-on');
		
		// マジックナンバー
		switch(int_category_id){
			case JP_CATEGORY_WORD:
				elm_addDiv.textContent = str_japanese+' '+str_kana+' [単語]';
				break;

			case JP_CATEGORY_GRAMMAR:
				elm_addDiv.textContent = str_japanese+' '+str_kana+' [文法]';
				break;

			case JP_CATEGORY_TERMINOLOGY:
				elm_addDiv.textContent = str_japanese+' '+str_kana+' [専門用語]';
				break;

			default:
				elm_addDiv.textContent = str_japanese+' '+str_kana+' [文法]';
		}
		
		elm_addLi.appendChild(elm_addDiv);
		elm_targetUl.appendChild(elm_addLi);
		
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}
}

function bindSearchResultItemBehavior(elm_addLi, arr_classNaming_li) {

    switch (true) {

        case arr_classNaming_li.includes('panelOverlaySharedContentsUiLi'):
            elm_addLi.addEventListener('pointerup', function () {
                addGrammarInsightsSelectedItem(elm_addLi);
            }, false);
            break;

		case arr_classNaming_li.includes('manageRoomModalUiAddContentsLi'):
            elm_addLi.addEventListener('pointerup', async function () {
                await addRoomLessonContent(elm_addLi);
            }, false);
            break;

        default:
            break;
    }
}

async function addRoomLessonContent(elm_addLi) {

    const send_japanese_id = escapeNumber(elm_addLi.dataset.japaneseId);

    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const params = url.searchParams;
    const send_unique_code = escapeHTML(params.get(KEY_UNIQUE_CODE));

    if (send_unique_code === null || send_unique_code === '') {
        alert(MSG_ERROR_UNIQUE_CODE[intSelectedLanguage]);
        return;
    }

    try {

        const payload = {
            current_url: currentUrl,
            send_japanese_id: send_japanese_id,
            unique_code: send_unique_code,
            int_selected_language: intSelectedLanguage
        };

        await postJson(
            roomCreateNewContentsUrl,
            payload,
            10000
        );

        if (typeof fetchAndRenderRoomLessonContents === 'function') {
            fetchAndRenderRoomLessonContents();
        }

    } catch (error) {
        if (error && typeof error.message === 'string' && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert((error && error.message) || 'Error');
        }
    }
}

function setUiLock(on) {

    const overlay = document.getElementById('wiseUiLockOverlay');
    if (!overlay) return;

    overlay.classList.toggle('overlay-on', on);

    const spinner = overlay.querySelector('.loading-wrapper');
    if (!spinner) return;

    spinner.classList.toggle('loading-hidden', !on);
}


function changeWiseUiFontSize(doIncreaseFontSize){
	let str_targetClassName = 'wiseUiFontSizeTarget';
	let newVariationDifference = calculateNewFontSize('wiseUiFontSizeTargetVariationDifference', doIncreaseFontSize);
	const elms = document.querySelectorAll('.' + str_targetClassName);
	if (elms.length > LENGTH_EMPTY) {
		applyNewFontSize(elms, str_targetClassName, newVariationDifference);
	}
}


function resizePanelOverlaySharedContentsUi(){

	const topElms = document.querySelectorAll('panelOverlaySharedContentsUiTopLevel');

	const windowHeight = window.innerHeight;
	let elementHeight;
	let newTop;
	
	let windowWidth = window.innerWidth || 1;
	
	if (windowWidth <= 600){
		elementHeight = windowHeight;
		newTop = 0;
	}
	else{
		elementHeight = windowHeight * 0.9,
		newTop = (windowHeight - elementHeight) / 2;
	}

	topElms.forEach(element => {
		element.style.height = elementHeight + 'px';
		element.style.top = newTop + 'px';
	});

}