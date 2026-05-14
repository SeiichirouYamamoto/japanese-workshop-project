
const panelOverlaySharedContentsUiSelectedContents = document.getElementById('panelOverlaySharedContentsUiSelectedContents');



if(panelOverlaySharedContentsUiHistorySelectAllButton !== null)
{panelOverlaySharedContentsUiHistorySelectAllButton.addEventListener('pointerup', function() {
	let elms_panelOverlaySharedContentsUiLiFromHistory = document.querySelectorAll('.panelOverlaySharedContentsUiLiFromHistory');
	if (elms_panelOverlaySharedContentsUiLiFromHistory.length === LENGTH_EMPTY) {
		return;
	}
	elms_panelOverlaySharedContentsUiLiFromHistory.forEach(function(elm) {
		addGrammarInsightsSelectedItem(elm);
	});
}, false);}


if(panelOverlaySharedContentsUiSelectedContentsCloseButton !== null)
{panelOverlaySharedContentsUiSelectedContentsCloseButton.addEventListener('pointerup', function() {
	switchPanelOverlaySharedContentsUiView(SHARED_CONTENTS_UI_VIEW.NONE);
}, false);}


if(panelOverlaySharedContentsUiCategorySelect !== null){
panelOverlaySharedContentsUiCategorySelect.addEventListener('change', async function (e){
	executeGrammarInsightsContentCategoryList();
}
, { passive: false });
}


if(panelOverlaySharedContentsUiCategorySelectAllButton !== null)
{panelOverlaySharedContentsUiCategorySelectAllButton.addEventListener('pointerup', function() {
	const elms = document.querySelectorAll('.panelOverlaySharedContentsUiLiFromCategory');
	if (elms.length === LENGTH_EMPTY) {
		return;
	}
	elms.forEach(function(elm) {
		addGrammarInsightsSelectedItem(elm);
	});
}, false);}


if(panelOverlaySharedContentsUiBookmarkSelect !== null){
panelOverlaySharedContentsUiBookmarkSelect.addEventListener('change', async function (e){
	executeGrammarInsightsContentBookmarkList();
}
, { passive: false });
}

if(panelOverlaySharedContentsUiBookmarkSelectAllButton !== null)
{panelOverlaySharedContentsUiBookmarkSelectAllButton.addEventListener('pointerup', function() {
	const elms = document.querySelectorAll('.panelOverlaySharedContentsUiLiFromBookmark');
	if (elms.length === LENGTH_EMPTY) {
		return;
	}
	elms.forEach(function(elm) {
		addGrammarInsightsSelectedItem(elm);
	});
}, false);}



if(panelOverlaySharedContentsUiSearchButton !== null)
{
	panelOverlaySharedContentsUiSearchButton.addEventListener('pointerup', function() {
		executeGrammarInsightsContentSearch();
	}, false);
}

if(panelOverlaySharedContentsUiSearchInput !== null)
{
	panelOverlaySharedContentsUiSearchInput.addEventListener('keydown', function (e) {

        if (e.key === 'Enter' || e.keyCode === 13) {
			e.preventDefault();
			executeGrammarInsightsContentSearch();
        }
    });
}




document.querySelectorAll(
    '.panelOverlaySharedContentsUiChangeModalNavButton'
).forEach(btn => {

    btn.addEventListener('pointerup', function () {

		if (this.disabled) return;

        const view = this.dataset.view;

        switch (view) {
            case 'CATEGORY':
                handleGrammarInsightsOpenCategory();
                break;

            case 'HISTORY':
                handleGrammarInsightsOpenHistory();
                break;

            case 'ADD':
                handleGrammarInsightsBackToAdd();
                break;

            case 'BOOKMARK':
                handleGrammarInsightsOpenBookmark();
                break;

            default:
                break;
        }

    }, false);

});

function handleGrammarInsightsOpenCategory() {

    if (!(panelOverlaySharedContentsUiFromCategory.classList.contains('panelOverlaySharedContentsUi-open'))) {

        panelOverlaySharedContentsUiCategorySelect.selectedIndex = INDEX_FIRST;

        const elm_targetUl = document.getElementById(
            'panelOverlaySharedContentsUiFromCategoryUl'
        );

        elm_targetUl.replaceChildren();

        switchPanelOverlaySharedContentsUiView([
            SHARED_CONTENTS_UI_VIEW.CATEGORY,
            SHARED_CONTENTS_UI_VIEW.SELECTED
        ]);

        return;
    }
}

function handleGrammarInsightsOpenHistory() {

    const elm_targetUl = panelOverlaySharedContentsUiHistoryList;
    const arr_classNaming_li = [
        'panelOverlaySharedContentsUiLi',
        'panelOverlaySharedContentsUiLiFromHistory'
    ];

    elm_targetUl.replaceChildren();

    if (!(panelOverlaySharedContentsUiFromHistory.classList.contains('panelOverlaySharedContentsUi-open'))) {

        switchPanelOverlaySharedContentsUiView([
            SHARED_CONTENTS_UI_VIEW.HISTORY,
            SHARED_CONTENTS_UI_VIEW.SELECTED
        ]);

        buildSearchResultListItems(
            sharedContentsSelectionItems,
            elm_targetUl,
            arr_classNaming_li
        );

        return;
    }
}


function handleGrammarInsightsOpenBookmark() {

	if (!(panelOverlaySharedContentsUiFromBookmark.classList.contains('panelOverlaySharedContentsUi-open'))) {

        panelOverlaySharedContentsUiBookmarkSelect.selectedIndex = INDEX_FIRST;

        const elm_targetUl = document.getElementById(
            'panelOverlaySharedContentsUiFromBookmarkUl'
        );

        elm_targetUl.replaceChildren();

        switchPanelOverlaySharedContentsUiView([
            SHARED_CONTENTS_UI_VIEW.BOOKMARK,
            SHARED_CONTENTS_UI_VIEW.SELECTED
        ]);

        return;
    }
}

document.querySelectorAll('.showGrammarInsightsButton').forEach(function(elm) {
	elm.addEventListener('pointerup', async function() {

		const selectedElms = getSharedContentsSelectionElements();
        if (!selectedElms) {
            return;
        }

		const panelId = grammarInsightsOpenButton.dataset.panelId;
		if (!panelId) {
			return;
		}

		switchPanelOverlaySharedContentsUiView(SHARED_CONTENTS_UI_VIEW.NONE);
		openWisePanelPositionSelectUi(panelId, true);

		await openGrammarInsightsPanelWithDiffCheck();
	
	}, false);
});

document.querySelectorAll('.showWisePanelGrammarExplanationButton').forEach(function(elm) {
	elm.addEventListener('pointerup', async function() {

		const selectedElms = getSharedContentsSelectionElements();
        if (!selectedElms) {
            return;
        }

		switchToGrammarExplanationPanel();

		await createMultipleGrammarExplanations(selectedElms);

	}, false);
});


function getSharedContentsSelectionElements() {
    const elms = document.querySelectorAll('.panelOverlaySharedContentsUiSelectedContentsListLi');

    if (elms.length === LENGTH_EMPTY) {
        return null;
    }

    elms.forEach(elm => {
        storeSharedContentsSelectionItem({
            japaneseId: escapeNumber(elm.dataset.japaneseId),
            grammarUniqueCode: escapeHTML(elm.dataset.grammarUniqueCode),
            japanese: escapeHTML(elm.dataset.japanese),
            kana: escapeHTML(elm.dataset.kana),
            categoryId: escapeNumber(elm.dataset.categoryId)
        });
    });

    return elms;
}

function storeSharedContentsSelectionItem(obj_grammarData) {
    const isDuplicate = sharedContentsSelectionItems.some(
        item => item.grammarUniqueCode === obj_grammarData.grammarUniqueCode
    );

    if (!isDuplicate) {
        sharedContentsSelectionItems.push(obj_grammarData);
    }
}


function switchPanelOverlaySharedContentsUiView(viewType)
{
    const targets = {
        add: panelOverlaySharedContentsUi,
        category: panelOverlaySharedContentsUiFromCategory,
        bookmark: panelOverlaySharedContentsUiFromBookmark,
        history: panelOverlaySharedContentsUiFromHistory,
        selected: panelOverlaySharedContentsUiSelectedContents
    };

    // まず全部閉じる
    Object.values(targets).forEach(elm => {
        if (elm) {
            elm.classList.remove('panelOverlaySharedContentsUi-open');
        }
    });

    // NONE / null / undefined のときはここで終了
    if (viewType === null || viewType === undefined || viewType === SHARED_CONTENTS_UI_VIEW.NONE) {
        return;
    }

    // 単体でも配列でも受ける
    const views = Array.isArray(viewType) ? viewType : [viewType];

	let openAddView = false;

    // 必要なものだけ開く
    views.forEach(v => {
        const elm = targets[v];
        if (elm) {
            elm.classList.add('panelOverlaySharedContentsUi-open');
        }

		if (v === SHARED_CONTENTS_UI_VIEW.ADD) {
            openAddView = true;
        }
    });

	if (openAddView && panelOverlaySharedContentsUiSearchInput) {
        requestAnimationFrame(() => {
            panelOverlaySharedContentsUiSearchInput.focus();
        });
    }
}
