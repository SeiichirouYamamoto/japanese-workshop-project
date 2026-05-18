const lessonAddContentsAttributeSelect = document.getElementById('manageRoomModalUiAddContentsSelectAttribute');
const lessonAddContentsList = document.getElementById('manageRoomModalUiAddContentsUl');
const lessonAddContentsLoading = document.getElementById('manageRoomModalUiLoadingAddContents');
const lessonAddContentsMatchingTypeSelect = document.getElementById('manageRoomModalUiAddContentsSelectMatchingType');
const lessonAddContentsSearchButton = document.getElementById('manageRoomModalUiAddContentsSearchButton');
const lessonAddContentsSearchInput = document.getElementById('manageRoomModalUiAddContentsSearchInput');
const multiCopyLessonsButton = document.getElementById('multicopyLessonsFromTeachingMaterialsCopyButton');
const multiCopyLessonsEndIdInput = document.getElementById('multicopyLessonsFromTeachingMaterialsEndId');
const multiCopyLessonsStartIdInput = document.getElementById('multicopyLessonsFromTeachingMaterialsStartId');
const roomContentsCreateButton = document.getElementById('roomContentsCreateNewButton');




document.querySelectorAll('.learningStatusRadioButton').forEach(element => {
	element.addEventListener('change', async function() {
		
		let learningStatus = escapeNumber(this.value);
		let roomLessonUniqueCode = escapeHTML(this.dataset.roomLessonUniqueCode);

		try {
	
			const payload = {
				int_learning_status: learningStatus,
				lesson_unique_code: roomLessonUniqueCode,
				int_selected_language: intSelectedLanguage
			};

			await postJson(
				roomUpdateLessonLearningStatusUrl,
				payload,
				10000
			);

		} catch (error) {
			if (error.message && error.message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
			} else {
				console.error('Error:', error.message || error);
				alert(error.message || 'Error');
			}
		}

	});
});


if (manageSectionUpdateAllButton !== null) {
manageSectionUpdateAllButton.addEventListener('pointerup', async function () {
	let isConfirmed = window.confirm(MSG_UPDATE_CONFIRM[intSelectedLanguage]);
	if (!isConfirmed) return;

	const section = this.closest('.editSectionContainer');
	if (!section) return;

	const editable = section.querySelectorAll('.editableElement');
	const changes = [];

	editable.forEach(el => {
		const id = el.dataset.id;
		const column = el.dataset.column;
		const original = (el.dataset.original ?? '');
		let current;

		if (el.type === 'checkbox') {
			current = el.checked ? '1' : '0';
		} else {
			current = el.value;
		}

		if (!id || !column) return;
		if (current !== original) {
			changes.push({
				id: Number(id),
				column: column,
				value: current
			});
		}
	});

	if (changes.length === LENGTH_EMPTY) {
		alert(MSG_NO_UPDATE[intSelectedLanguage] || 'No changes');
		return;
	}

	try {

		const currentUrl = window.location.href;
	
		const payload = {
			current_url: currentUrl,
			int_selected_language: intSelectedLanguage,
			changes: changes
		};

		const result = await postJson(
			wiseManageBulkUpdateRecords,
			payload,
			15000
		);

		const data = result.data;

		if (!data) return;

		if (data.success === true) {
			location.reload();
			return;
		}

		throw new Error('Unexpected response');
	} catch (e) {
		if (e && typeof e.message === 'string' && e.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			alert((e && e.message) || 'Error');
		}
	}

}, false);}

if (roomContentsCreateButton !== null) {
    roomContentsCreateButton.addEventListener('pointerup', async function() {
        let isConfirmed = window.confirm(MSG_REGISTER_CONFIRM[intSelectedLanguage]);

        if (isConfirmed) {

            let inputValue = escapeHTML(document.querySelector('.roomContentsCreateNewData').value);
            let currentUrl = window.location.href;
            let url = new URL(currentUrl);
            let params = url.searchParams;
            let uniqueCodeParam = this.dataset.uniqueCodeParam || KEY_UNIQUE_CODE;
            let send_unique_code = escapeHTML(params.get(uniqueCodeParam)) || 0;

            let send_array = inputValue.split(/\n/);
            send_array = send_array.filter(item => item !== "");

            if (send_array.length === LENGTH_EMPTY) return;

            try {

                const payload = {
                    current_url: currentUrl,
                    send_array: send_array,
                    unique_code: send_unique_code,
                    int_selected_language: intSelectedLanguage
                };

                await postJson(
                    roomCreateNewContentsUrl,
                    payload,
                    10000
                );

                location.reload();
                return;

            } catch (error) {
                if (error && typeof error.message === 'string' && error.message.includes('タイムアウト')) {
                    console.error('タイムアウトが発生しました。');
                } else {
                    alert((error && error.message) || 'Error');
                }

                return;
            }
        }
    }, false);
}


if(lessonAddContentsSearchButton !== null)
{
	lessonAddContentsSearchButton.addEventListener('pointerup', function() {
		executeLessonContentSearch();
	}, false);
}


if(lessonAddContentsSearchInput !== null)
{
	lessonAddContentsSearchInput.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' || e.keyCode === 13) {
			e.preventDefault();
			executeLessonContentSearch();
		}
	}, false);
}


if(multiCopyLessonsButton !== null)
{multiCopyLessonsButton.addEventListener('pointerup', async function() {
	let isConfirmed = window.confirm(MSG_REGISTER_CONFIRM[intSelectedLanguage]);
	if(isConfirmed) {

		let int_start_id = escapeNumber(multiCopyLessonsStartIdInput[multiCopyLessonsStartIdInput.selectedIndex].value);
		let int_end_id = escapeNumber(multiCopyLessonsEndIdInput[multiCopyLessonsEndIdInput.selectedIndex].value);
		
		// 未定義id null変更
		let currentUrl = window.location.href;
		let url = new URL(currentUrl),
		params = url.searchParams,
		send_unique_code = escapeHTML(params.get(KEY_ROOM_UNIQUE_CODE)) || 0;
		
		try {
			
			const payload = {
				int_start_id: int_start_id,
				int_end_id: int_end_id,
				send_unique_code: send_unique_code,
				int_selected_language: intSelectedLanguage
			};

			const result = await postJson(
				roomMulticopyLessonsFromTeachingMaterialsUrl,
				payload,
				10000
			);

			location.reload();

		} catch (error) {
			if (error.message && error.message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
			} else {
				console.error('Error:', error.message || error);
				alert(error.message || 'Error');
			}
		}

	}
}, false);}





document.addEventListener('pointerup', function (event) {

    const elmDeleteButton = event.target.closest('.roomContentsDeleteButton');
    if (!elmDeleteButton) return;

    event.stopPropagation();

    handleRoomLessonContentDelete(elmDeleteButton);

});


function handleRoomLessonContentDelete(elmDeleteButton) {

    const isConfirmed = window.confirm(MSG_DELETE_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) return;

    const id = escapeNumber(elmDeleteButton.dataset.id);

    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const params = url.searchParams;
    const unique_code = params.get(KEY_ROOM_LESSON_STEP_UNIT_UNIQUE_CODE) || 0;

    const payload = {
        currentUrl: currentUrl,
        id: id,
        unique_code: unique_code,
        int_selected_language: intSelectedLanguage
    };

    executeManagePageDeletion(roomDeleteContentsUrl, payload, {
        onSuccess: () => {
            if (currentUrl.includes('manage-room-lesson-contents')) {
                if (typeof fetchAndRenderRoomLessonContents === 'function') {
                    fetchAndRenderRoomLessonContents();
                } else {
                    location.reload();
                }
            } else {
                location.reload();
            }
        }
    });
}


async function fetchAndRenderRoomLessonContents() {
    try {
        const lessonContents = await fetchRoomLessonContentList();
        renderRoomLessonContentList(lessonContents);
    } catch (error) {
        console.error('Failed to search lesson contents:', error.message || error);
    }
}


async function fetchRoomLessonContentList() {
    const urlObj = new URL(window.location.href);
    const params = urlObj.searchParams;
    const uniqueCode = params.get(KEY_ROOM_LESSON_STEP_UNIT_UNIQUE_CODE) || 0;

    const payload = {
        unique_code: uniqueCode,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        roomGetLessonContentsUrl,
        payload,
        10000
    );

    const data = result.data;

    if (!Array.isArray(data) || data.length === 0) {
        return [];
    }

    return data;
}


function renderRoomLessonContentList(arrLessonContents) {

    const elmTargetUl = document.getElementById('manageRoomModalUiSelectedContentsUl');
    const classNamingLi = 'manageRoomModalUiSelectedContentsLi';

    if (elmTargetUl === null) return;

    elmTargetUl.replaceChildren();

    for (let i = INDEX_FIRST; i < arrLessonContents.length; i++) {

        const targetArray = arrLessonContents[i];
        if (targetArray === null) continue;

        const intLessonContentsId = escapeNumber(targetArray.lessonContentId);
        const intJapaneseId = escapeNumber(targetArray.japaneseId);
        const strJapanese = escapeHTML(targetArray.japanese);
        const intSort = escapeHTML(targetArray.sort);

        const elmLi = document.createElement('li');
        elmLi.classList.add(classNamingLi);
        elmLi.dataset.id = intLessonContentsId;
        elmLi.dataset.japaneseId = intJapaneseId;
        elmLi.dataset.japanese = strJapanese;
        elmLi.dataset.sort = intSort;

        const elmDeleteButton = document.createElement('button');
        elmDeleteButton.classList.add('roomContentsDeleteButton');
        elmDeleteButton.dataset.id = intLessonContentsId;
        elmDeleteButton.textContent = 'del';

        const elmPreviousButton = document.createElement('button');
        elmPreviousButton.classList.add('roomContentsEditContentsSortPreviousButton', 'roomContentsEditContentsSortButton');
        elmPreviousButton.dataset.id = intLessonContentsId;
        elmPreviousButton.textContent = '△';

        const elmNextButton = document.createElement('button');
        elmNextButton.classList.add('roomContentsEditContentsSortNextButton', 'roomContentsEditContentsSortButton');
        elmNextButton.dataset.id = intLessonContentsId;
        elmNextButton.textContent = '▽';

        const elmNamesSpan = document.createElement('span');
        elmNamesSpan.classList.add('roomContentsNamesSpan');
        elmNamesSpan.textContent = strJapanese;

        elmLi.appendChild(elmDeleteButton);
        elmLi.appendChild(elmPreviousButton);
        elmLi.appendChild(elmNextButton);
        elmLi.appendChild(elmNamesSpan);

        elmTargetUl.appendChild(elmLi);
    }
}


async function executeManagePageDeletion(url, payload, options = {}) {
	
    const onSuccess = typeof options.onSuccess === 'function' ? options.onSuccess : null;

	try {
		const result = await postJson(
			url,
			payload,
			10000
		);
		
		const data = result.data;

		if (onSuccess) {
			onSuccess(data);
		} else {
			location.reload();
		}

	} catch (error) {
		if (error && typeof error.message === 'string' && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			alert((error && error.message) || 'Error');
		}
	}

}

document.addEventListener('pointerup', async function (event) {

    const elmPreviousButton = event.target.closest('.roomContentsEditContentsSortPreviousButton');
    if (elmPreviousButton) {
        event.stopPropagation();
        await reorderRoomItem(true, elmPreviousButton);
        return;
    }

    const elmNextButton = event.target.closest('.roomContentsEditContentsSortNextButton');
    if (elmNextButton) {
        event.stopPropagation();
        await reorderRoomItem(false, elmNextButton);
        return;
    }

});


async function reorderRoomItem(isPrevious, elm) {

    const isPreviousAsNumber = isPrevious ? FLAG_TRUE : FLAG_FALSE;

    const id = escapeNumber(elm.dataset.id);

    const currentUrl = window.location.href;

    const url = new URL(currentUrl);

    const pathname = url.pathname;

    const params = url.searchParams;

    const {
        type: pageType,
        uniqueCode: unique_code
    } = getRoomResortPageInfo(params, pathname);

    const payload = {
        isPreviousAsNumber: isPreviousAsNumber,
        currentUrl: currentUrl,
        pageType: pageType,
        id: id,
        unique_code: unique_code,
        int_selected_language: intSelectedLanguage
    };

    await executeRoomLessonContentResort(
        roomResortContentsUrl,
        payload,
        {
            onSuccess: () => {

                if (pageType === 'contents') {

                    if (typeof fetchAndRenderRoomLessonContents === 'function') {
                        fetchAndRenderRoomLessonContents();
                    } else {
                        location.reload();
                    }

                } else {

                    location.reload();

                }
            }
        }
    );
}

function getRoomResortPageInfo(params, pathname) {

    if (pathname.endsWith('/manage-room-lesson-contents/')) {
        return {
            type: 'contents',
            uniqueCode: params.get(KEY_ROOM_LESSON_STEP_UNIT_UNIQUE_CODE) || 0
        };
    }

    if (pathname.endsWith('/manage-room-lesson-step-units/')) {
        return {
            type: 'step_units',
            uniqueCode: params.get(KEY_ROOM_LESSON_STEP_UNIQUE_CODE) || 0
        };
    }

    if (pathname.endsWith('/manage-room-lesson-steps/')) {
        return {
            type: 'steps',
            uniqueCode: params.get(KEY_ROOM_LESSON_UNIQUE_CODE) || 0
        };
    }

    if (pathname.endsWith('/manage-room-lessons/')) {
        return {
            type: 'lessons',
            uniqueCode: params.get(KEY_ROOM_UNIQUE_CODE) || 0
        };
    }

    if (pathname.endsWith('/manage-rooms/')) {
        return {
            type: 'rooms',
            uniqueCode: params.get(KEY_ROOM_UNIQUE_CODE) || 0
        };
    }

    return {
        type: '',
        uniqueCode: 0
    };
}


async function executeRoomLessonContentResort(url, payload, options = {}) {
    const onSuccess = typeof options.onSuccess === 'function' ? options.onSuccess : null;

    try {
		await postJson(
			url,
			payload,
			10000
		);

		if (onSuccess) {
			onSuccess();
		} else {
			location.reload();
		}
		return;

	} catch (error) {
		if (error && typeof error.message === 'string' && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			alert((error && error.message) || 'Error');
		}
		return;
	}
}


function executeLessonContentSearch() {

    const str_searchWord = escapeHTML(lessonAddContentsSearchInput.value);
    const elm_targetUl = lessonAddContentsList;
    const elm_targetLoading = lessonAddContentsLoading;
    const elm_targetMatchingType = lessonAddContentsMatchingTypeSelect;

    const int_matching_type = escapeNumber(
        elm_targetMatchingType[elm_targetMatchingType.selectedIndex].value
    );

    const arr_classNaming_li = [
        'manageRoomModalUiAddContentsLi',
        'wiseUiFontSizeTarget'
    ];

    const int_category_id = escapeNumber(
        lessonAddContentsAttributeSelect[lessonAddContentsAttributeSelect.selectedIndex].value
    );
	const int_sub_category_id = SELECT_ALL;

	const int_learningScope = LEARNING_SCOPE_SELECT_ALL;

    searchContentList(
		SEARCH_SCOPE_MANAGE_LESSON_CONTENTS,
        str_searchWord,
        elm_targetUl,
        elm_targetLoading,
        arr_classNaming_li,
        int_matching_type,
        int_category_id,
        int_sub_category_id,
        int_learningScope
    );
}

