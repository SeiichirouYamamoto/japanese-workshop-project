const wiseMapFocusMapLoading = document.getElementById('wiseMapScreenMapContainerLoadingFocus');
const wiseMapFocusScreenLoading = document.getElementById('wiseMapFocusPointScreenLoading');
const wiseMapFocusTasksList = document.getElementById('wiseMapScreenTasksListDisplayAreaUlFocus');
const wiseMapFocusUiList = document.getElementById('wiseMapScreenUiDisplayAreaUlFocus');
const wiseMapFocusUsagesList = document.getElementById('wiseMapScreenUsagesListDisplayAreaUlFocus');
const wiseMapLessonGoalLoading = document.getElementById('wiseMapLessonGoalScreenLoading');
const wiseMapLessonMapArea = document.getElementById('wiseMapScreenMapDisplayAreaLesson');
const wiseMapLessonNodeArea = document.getElementById('wiseMapScreenNodeDisplayAreaLesson');
const wiseMapLessonStepGoalLoading = document.getElementById('wiseMapLessonStepGoalScreenLoading');
const wiseMapLessonStepMapArea = document.getElementById('wiseMapScreenMapDisplayAreaLessonStep');
const wiseMapLessonStepNodeArea = document.getElementById('wiseMapScreenNodeDisplayAreaLessonStep');
const wiseMapLessonStepUiList = document.getElementById('wiseMapScreenUiDisplayAreaUlLessonStep');
const wiseMapLessonUiList = document.getElementById('wiseMapScreenUiDisplayAreaUlLesson');
const wiseMapToggleTasksButton = document.getElementById('wiseMapToggleTasksListButton');
const wiseMapToggleUiButton = document.getElementById('wiseMapToggleUIButton');
const wiseMapToggleUsagesButton = document.getElementById('wiseMapToggleUsagesListButton');




let waypointItemsMapForUsages = {};
let waypointItemsMapForTasks = {};






if(wiseMapToggleUiButton !== null)
{wiseMapToggleUiButton.addEventListener('pointerup', function() {
	const targetClass = '.wiseMapScreenUiContainer';
	switchWiseMapPanelVisibility(this, targetClass);
}, false);}


if(wiseMapToggleUsagesButton !== null)
{wiseMapToggleUsagesButton.addEventListener('pointerup', function() {
	const targetClass = '.wiseMapScreenUsagesListContainer';
	switchWiseMapPanelVisibility(this, targetClass);
}, false);}


if(wiseMapToggleTasksButton !== null)
{wiseMapToggleTasksButton.addEventListener('pointerup', function() {
	const targetClass = '.wiseMapScreenTasksListContainer';
	switchWiseMapPanelVisibility(this, targetClass);
}, false);}







function switchWiseMapPanelVisibility(btn, targetClass){
    const targets = document.querySelectorAll(targetClass);
    const state = btn.getAttribute('data-state');
    const isOpen = state === 'open';

    targets.forEach(elm => {
        if(isOpen){
            elm.classList.add('hidden');
        } else {
            elm.classList.remove('hidden');
        }
    });

    btn.setAttribute('data-state', isOpen ? 'close' : 'open');
}


async function renderWiseMapUILessonFromIds(selectedIds) {
    if (!Array.isArray(selectedIds) || selectedIds.length === LENGTH_EMPTY) {
        console.error('selectedIds が空、または配列ではありません');
        return;
    }
    if (!wiseMapOverlay) {
        console.error('wiseMapOverlay が存在しません');
        return;
    }

    wiseMapLessonGoalLoading.classList.remove('loading-hidden');
    switchWiseMapSection(wiseMapLessonGoalScreen);
    wiseMapLessonMapArea.innerHTML = '';
    wiseMapLessonNodeArea.innerHTML = '';
    wiseMapLessonUiList.innerHTML = '';
    wiseMapOverlay.classList.add('overlay-on');

    try {

		const payload = {
			map_type: 'lesson_goal_from_ids',
			target_ids: selectedIds,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
            wiseMapRenderUiFromIdsUrl,
            payload,
            60000
        );

		const data = result.data;

        if (!data) {
            console.error('data が空です');
            return;
        }

        const { waypoints_html, goal_html, waypoint_data } = data;

        waypointItemsMapForTasks = {};
        (waypoint_data || []).forEach(wp => {
            waypointItemsMapForTasks[wp.waypoint_unique_code] = wp.items;
        });

        if (wiseMapLessonUiList && waypoints_html) {
            wiseMapLessonUiList.innerHTML = waypoints_html;
        } else {
            console.warn('UI display area not found or no waypoints_html provided');
        }

        if (wiseMapLessonNodeArea && goal_html) {
            wiseMapLessonNodeArea.innerHTML = goal_html;
        } else {
            console.warn('Node display area not found or no goal_html provided');
        }

        // applyFontSizeVariation(
		// 	['wiseUiFontSizeTarget'],
		// 	'wiseUiFontSizeTargetVariationDifference'
		// );

    } catch (error) {
        console.error('Error:', error.message || error);
        alert(error.message || '通信エラーが発生しました');
    } finally {
        wiseMapLessonGoalLoading.classList.add('loading-hidden');
    }
}


async function renderWiseMapUILessonStepFromIds(selectedIds) {
    if (!Array.isArray(selectedIds) || selectedIds.length === LENGTH_EMPTY) {
        console.error('selectedIds が空、または配列ではありません');
        return;
    }
    if (!wiseMapOverlay) {
        console.error('wiseMapOverlay が存在しません');
        return;
    }

    wiseMapLessonStepGoalLoading.classList.remove('loading-hidden');
    switchWiseMapSection(wiseMapLessonStepGoalScreen);
    wiseMapLessonStepMapArea.innerHTML = '';
    wiseMapLessonStepNodeArea.innerHTML = '';
    wiseMapLessonStepUiList.innerHTML = '';
    wiseMapOverlay.classList.add('overlay-on');

    try {
		
		const payload = {
			map_type: 'lesson_step_goal_from_ids',
			target_ids: selectedIds,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
            wiseMapRenderUiFromIdsUrl,
            payload,
            60000
        );

		const data = result.data;

        if (!data) {
            console.error('data が空です');
            return;
        }

        const { waypoints_html, goal_html, waypoint_data } = data;

        waypointItemsMapForUsages = {};
        (waypoint_data || []).forEach(wp => {
            waypointItemsMapForUsages[wp.waypoint_unique_code] = wp.items;
        });

        if (wiseMapLessonStepUiList && waypoints_html) {
            wiseMapLessonStepUiList.innerHTML = waypoints_html;
        } else {
            console.warn('UI display area not found or no waypoints_html provided');
        }

        if (wiseMapLessonStepNodeArea && goal_html) {
            wiseMapLessonStepNodeArea.innerHTML = goal_html;
        } else {
            console.warn('Node display area not found or no goal_html provided');
        }

		// applyFontSizeVariation(
		// 	['wiseUiFontSizeTarget'],
		// 	'wiseUiFontSizeTargetVariationDifference'
		// );

    } catch (error) {
        console.error('Error:', error.message || error);
        alert(error.message || '通信エラーが発生しました');
    } finally {
        wiseMapLessonStepGoalLoading.classList.add('loading-hidden');
    }
}


async function renderWiseMapUIFocusPoint(uniqueCode) {
    if (!uniqueCode) {
        console.error('uniqueCodeが指定されていません');
        return;
    }
    if (!wiseMapOverlay) {
        console.error('wiseMapOverlay が存在しません');
        return;
    }

    wiseMapFocusScreenLoading.classList.remove('loading-hidden');

    switchWiseMapSection(wiseMapFocusScreen);
    wiseMapFocusMapArea.innerHTML = '';
    wiseMapFocusNodeArea.innerHTML = '';
    wiseMapFocusUiList.innerHTML = '';
    wiseMapOverlay.classList.add('overlay-on');

    try {

		const payload = {
			map_type: 'focus_point',
			unique_code: uniqueCode,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
            wiseMapRenderUiFromUniqueCodeUrl,
            payload,
            60000
        );

		const data = result.data;

		renderFocusPointListsFromData(data || {});

        const { waypoints_html, goal_html } = data || {};

        if (wiseMapFocusUiList && waypoints_html) {
            wiseMapFocusUiList.innerHTML = waypoints_html;
        } else {
            console.warn('UI display area not found or no waypoints_html provided');
        }

        if (wiseMapFocusNodeArea && goal_html) {
            wiseMapFocusNodeArea.innerHTML = goal_html;
        } else {
            console.warn('Node display area not found or no goal_html provided');
        }

		// applyFontSizeVariation(
		// 	['wiseUiFontSizeTarget'],
		// 	'wiseUiFontSizeTargetVariationDifference'
		// );
		
    } catch (error) {
        console.error('Error:', error.message || error);
    } finally {
        wiseMapFocusScreenLoading.classList.add('loading-hidden');
    }
}


async function renderWiseMapContentLesson(toggleElm, items) {

	if (items.length === LENGTH_EMPTY) {
        console.log('No active waypoints. Stopping request.');
        return;
    }
	
	const container = renderWiseMapContentContainer(toggleElm, items, false, false);
	await animateMount(container, {
		mode: 'prepend',
		parentEl: wiseMapLessonMapArea,
		duration: 900,
		easing: 'cubic-bezier(.22,.61,.36,1)',
		effect: 'gap',
		gapRatio: 0.6
	});

	// applyFontSizeVariation(
	// 	['wiseUiFontSizeTarget'],
	// 	'wiseUiFontSizeTargetVariationDifference'
	// );
}


async function renderWiseMapContentLessonStep(toggleElm, items) {

	if (items.length === LENGTH_EMPTY) {
        console.log('No active waypoints. Stopping request.');
        return;
    }
	
    const container = renderWiseMapContentContainer(toggleElm, items, false, false);
	await animateMount(container, {
		mode: 'prepend',
		parentEl: wiseMapLessonStepMapArea,
		duration: 900,
		easing: 'cubic-bezier(.22,.61,.36,1)',
		effect: 'gap',
		gapRatio: 0.6
	});

	// applyFontSizeVariation(
	// 	['wiseUiFontSizeTarget'],
	// 	'wiseUiFontSizeTargetVariationDifference'
	// );

}







async function renderWiseMapContentFocusPoint(toggleElm, activeUniqueCodes, goalUniqueCode, addedUniqueCodes) {
    if (!Array.isArray(activeUniqueCodes) || activeUniqueCodes.length === LENGTH_EMPTY) {
        wiseMapFocusMapLoading.classList.add('loading-hidden');
        return;
    }

    let loadingShown = false;
    const delay = 1000;
    const loadingTimer = setTimeout(() => {
        wiseMapFocusMapLoading.classList.remove('loading-hidden');
        loadingShown = true;
    }, delay);

    try {

		const payload = {
			goal_unique_code: goalUniqueCode,
			waypoints: activeUniqueCodes,
			added_unique_codes: addedUniqueCodes,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
            wiseMapRenderContentFocusPointMapUrl,
            payload,
            60000
        );

		const data = result.data;

        clearTimeout(loadingTimer);
        if (loadingShown) {
            wiseMapFocusMapLoading.classList.add('loading-hidden');
        }

        if (!data || !data.item_title) return;

        const items = toArrayMaybe(data);
        const container = renderWiseMapContentContainer(toggleElm, items, true, false);
		await animateMount(container, {
			mode: 'prepend',
			parentEl: wiseMapFocusMapArea,
			duration: 900,
			easing: 'cubic-bezier(.22,.61,.36,1)',
			effect: 'gap',
			gapRatio: 0.6
		});

		// applyFontSizeVariation(
		// 	['wiseUiFontSizeTarget'],
		// 	'wiseUiFontSizeTargetVariationDifference'
		// );

    } catch (error) {
        clearTimeout(loadingTimer);
        wiseMapFocusMapLoading.classList.add('loading-hidden');
        console.error('Error:', error.message || error);
        alert(error.message || '通信エラーが発生しました');
    } finally {
        clearTimeout(loadingTimer);
        wiseMapFocusMapLoading.classList.add('loading-hidden');
    }
}


function renderFocusPointListsFromData(data) {
    const usages = Array.isArray(data?.usages) ? data.usages : [];
    const tasks  = Array.isArray(data?.tasks)  ? data.tasks  : [];

    if (wiseMapFocusUsagesList) {
        renderWiseMapList('usages', usages, wiseMapFocusUsagesList);
    }
    if (wiseMapFocusTasksList) {
        renderWiseMapList('tasks', tasks, wiseMapFocusTasksList);
    }
}


function renderWiseMapList(type, route, targetUl) {
    if (!targetUl) return;
    targetUl.innerHTML = '';
    for (const wp of route) {
        const li = document.createElement('li');
        li.className = 'wiseMapListItem';

        const str_grammarUniqueCode = String(wp.waypoint_unique_code || '');
        const str_japanese = String(wp.waypoint_title || wp.waypoint_unique_code || '');

        if (type === 'usages') {
            const btn = document.createElement('button');
            btn.className = 'wiseMapListItemButton';
            btn.type = 'button';
            btn.textContent = 'Open';
            btn.addEventListener('pointerup', async function () {
                
				switchToGrammarExplanationPanel();
				
                await createGrammarExplanation(str_grammarUniqueCode);

				const obj_grammarData = {
					uniqueCode: str_grammarUniqueCode,
					japanese: str_japanese
				};

                handleGrammarExplanationHistoryAndDisplay(obj_grammarData);
            });
            li.appendChild(btn);
        }

        const title = document.createElement('span');
        title.className = 'wiseMapListItemTitle';
        title.textContent = str_japanese;

        li.appendChild(title);
        targetUl.appendChild(li);
    }
}


function switchWiseMapSection(targetElement) {

	const sections = document.querySelectorAll('.wiseMapScreenSection');

	sections.forEach(section => {
		if (section === targetElement) {
			section.classList.remove('hidden');
		} else {
			section.classList.add('hidden');
		}
	});
}



clickHandlers['map:show'] = function (btn) {

    const targetId = btn.dataset.actionTarget;
    const targetElement = document.getElementById(targetId);

    if (targetElement) {
        switchWiseMapSection(targetElement);
    } else {
        console.warn('map:show の対象セクションが存在しません: ', targetId);
    }

};

changeHandlers['map:focusPoint:toggle'] = function (target) {
    if (!(target instanceof HTMLInputElement)) return;
    if (!target.checked) return;

    const allToggles = wiseMapFocusScreen.querySelectorAll(
        '.waypointToggleButton'
    );

    const activeUniqueCodes = [];
    let goalUniqueCode = null;
    const addedUniqueCodes = [target.dataset.waypointUniqueCode];

    allToggles.forEach(toggle => {
        if (!goalUniqueCode) {
            goalUniqueCode = toggle.dataset.goalUniqueCode;
        }
        if (toggle.checked) {
            activeUniqueCodes.push(toggle.dataset.waypointUniqueCode);
        }
    });

    renderWiseMapContentFocusPoint(
        target,
        activeUniqueCodes,
        goalUniqueCode,
        addedUniqueCodes
    );
};

changeHandlers['map:lessonStep:toggle'] = function (target) {
    if (!(target instanceof HTMLInputElement)) return;
    if (!target.checked) return;

    const waypointUniqueCode = target.dataset.waypointUniqueCode;
    const items = waypointItemsMapForUsages[waypointUniqueCode] || [];

    renderWiseMapContentLessonStep(target, items);
};

changeHandlers['map:lesson:toggle'] = function (target) {
    if (!(target instanceof HTMLInputElement)) return;
    if (!target.checked) return;

    const waypointUniqueCode = target.dataset.waypointUniqueCode;
    const items = waypointItemsMapForTasks[waypointUniqueCode] || [];

    renderWiseMapContentLesson(target, items);
};