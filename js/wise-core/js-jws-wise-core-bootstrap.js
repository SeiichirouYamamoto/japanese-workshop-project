
const wiseContextItems = document.querySelectorAll('.wiseContextLi');

if(wisePanelWhiteboardViewMainContentArea !== null){
	textAreaContainerMaxWidthDefault = (wisePanelWhiteboardViewMainContentArea.scrollWidth) * 0.8;
}
else{
	textAreaContainerMaxWidthDefault = 0;
}


for(let i = INDEX_FIRST; i < wiseContextItems.length; i++) {
	wiseContextItems[i].addEventListener('pointerup',
		function (e){
			let elm_targetContainer = contextMenuTargetContainer;
			switch (this.id){
				case 'wiseContextMenuDelete':
					let isConfirmed = true;
					if(isConfirmed) {
						deleteElement(elm_targetContainer);
						saveState(STATE_TITLE_DELETE_CONTAINER[intSelectedLanguage]);
					}
					break;

				case 'wiseContextMenuInflection':
					buildFormList();
					openWisePanelUi(whiteboardPanel, whiteboardUiFormList);
					viewWordInformation(elm_targetContainer);
					openWisePanelUi(whiteboardPanel, whiteboardUiWordInformation);
					break;

				case 'wiseContextMenuChangeLabel':
					openWisePanelUi(whiteboardPanel, whiteboardUiLabelList);
					createLabelList();
					viewWordInformation(elm_targetContainer);
					openWisePanelUi(whiteboardPanel, whiteboardUiWordInformation);
					break;

				case 'wiseContextMenuWordInformation':
					openWisePanelUi(whiteboardPanel, whiteboardUiWordInformation);
					viewWordInformation(elm_targetContainer);
					break;

				case 'wiseContextMenuPoliteFormPlainFormTable':

					const buttons = politePlainForm.querySelectorAll('.wisePoliteFormPlainFormTableButtons');
					buttons.forEach(button => {
						button.classList.remove('hidden');
					});

					// デバッグ パネル化
					// politePlainFormOverlay.classList.add('overlay-on');
					break;

				case 'wiseContextMenuCreatePhraseClause':
					if(elm_targetContainer.classList.contains('phraseClauseContainer')){

						purgePhraseClauseContainer(elm_targetContainer);
						updateLinkContainerWidths();
						alignElements(elm_targetContainer);
						redrawLinkLines();
						saveState(STATE_TITLE_DIVIDE_CONTAINER[intSelectedLanguage]);
						
					}
					else{
						if(elm_targetContainer.classList.contains('wordContainer')){
	
							createPhraseClauseContainer(elm_targetContainer);
							updateLinkContainerWidths();
							redrawLinkLines();
							saveState(STATE_TITLE_COMBINE_CONTAINER[intSelectedLanguage]);

						}
					}
					break;

				case 'wiseContextMenuAlignElements':
					alignElements(elm_targetContainer);
					redrawLinkLines();
					saveState(STATE_TITLE_ALIGN_CONTAINER[intSelectedLanguage]);
					break;

				case 'wiseContextMenuSliceElements':
					sliceElements(elm_targetContainer);
					break;

				default:

			}
			wiseContextMenu.classList.add('hidden');
			isOpened_contextMenu = false;
		}
	, { passive: false });
}


document.addEventListener('DOMContentLoaded', () => {

    if (sectionWise === null) return;

    prepareLayoutOnLoad();

    initWiseLayout(true);
    renderWisePanels();
	
    bindWiseRightToolbarEvents();
	bindWiseExpandableToolbarEvents();	

    bindWisePanelPositionSelectEvents();
    bindWiseResizeEvents();
    bindWisePanelCloseEvents();
    bindWisePanelExpandEvents();
    bindWisePanelSplitEvents();

}, { passive: true });



window.addEventListener('load', () => {

    if (sectionWise === null) return;

    runAfterStableLayout(() => {
		callIfFunction(prepareLayoutOnResize);
		initWiseLayout(false);
	});

}, { passive: true });


function setWiseLessonContentsItems(){
	
	if (wisePanelGrammarInsightsView !== null) {

		const baseHeight =
			panelOverlaySharedContentsUiSearchButton.offsetHeight;

		const buttons = document.querySelectorAll(
			'.panelOverlaySharedContentsUiChangeModalButton'
		);

		buttons.forEach(btn => {
			btn.style.height = baseHeight + 'px';
		});

		resizePanelOverlaySharedContentsUi();
	}
}

function registerTextareaUnloadHandler() {

    window.addEventListener('beforeunload', function (e) {

        registerTextareaValuesByBeacon();

        const confirmationMessage = MSG_PAGE_LEAVE_CONFIRMATION[intSelectedLanguage];
        e.returnValue = confirmationMessage;
        return confirmationMessage;
    });
}


function adjustBannerAdSize(){

	const screenWidth = window.innerWidth;

	if(wiseBannerAdContainer !== null){
	if(screenWidth <= 767){
		wiseBannerAdContainer.style.width = '80%';
	}else{
		wiseBannerAdContainer.style.width = '728px';
	}
	}
}


if (wiseBannerAdContainer !== null) {
    registerUserActivityListeners();
    startInactivityChecker();
}


function registerUserActivityListeners() {
    const setActive = () => { isActioned = true; };
    const setInactive = () => { 
        isActioned = false; 
        isActiveTab = false; 
    };
    const setFocus = () => { 
        isActioned = true; 
        isActiveTab = true; 
    };

    const userEvents = ["mousemove", "keydown", "click", "touchstart", "touchmove"];
    userEvents.forEach(event => {
        document.addEventListener(event, setActive);
    });

    window.addEventListener("blur", setInactive);
    window.addEventListener("focus", setFocus);
}

function startInactivityChecker() {
    return setInterval(() => {

        if (wiseBannerAdContainer !== null) {
            sendBannerAdActivity();
        }

        if (typeof sendWhiteboardAutosaveTick === 'function') {
            sendWhiteboardAutosaveTick();
        }

        if (wiseWaitOverlay !== null) {
            if (AUTO_WAIT_MODE) {
                if (!isActioned && !wiseWaitOverlay.classList.contains('overlay-on')) {
                    actionCount += 1;
                } else {
                    actionCount = COUNT_FIRST;
                }

                if (actionCount >= INACTIVE_THRESHOLD_MINUTES) {
                    showWiseWaitOverlay();
                    actionCount = COUNT_FIRST;
                }
            }
        }

        isActioned = false;
    }, MINUTE_MS);
}

function sendBannerAdActivity() {

    if (!isActiveTab) return;

    const currentTime = new Date().getTime();
    const isActionedAsNumber = isActioned ? FLAG_TRUE : FLAG_FALSE;
    const isFirstIntervalAsNumber = isFirstInterval ? FLAG_TRUE : FLAG_FALSE;

    const payload = {
        currentTime: escapeNumber(currentTime),
        isActioned: isActionedAsNumber,
        isFirstInterval: isFirstIntervalAsNumber
    };

    postJson(wiseCoreRegisterSessionTimeUrl, payload, 10000).catch(e => {
        if (e && typeof e.message === 'string' && e.message.includes('timeout')) {
            console.error('タイムアウトが発生しました。');
            return;
        }
        console.error('sendBannerAdActivity error:', e);
    });

    isFirstInterval = false;
}



