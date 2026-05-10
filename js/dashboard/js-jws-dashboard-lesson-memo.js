
const lessonMemoOverlay = document.getElementById('lessonMemoOverlay');


async function handleShowLessonMemoOverlay(btn) {

    const elmOverlay = document.getElementById('lessonMemoOverlay');
    const elmLoading = document.getElementById('lessonMemoModalLoading');
    const elmContents = document.getElementById('lessonMemoModalContents');

    if (elmOverlay === null || elmLoading === null || elmContents === null) {
        return;
    }

    const memoIdRaw = String(btn?.dataset?.memoId || '').trim();
    if (memoIdRaw === '') {
        return;
    }

    const payload = {
        memo_id: Number(memoIdRaw),
        int_selected_language: intSelectedLanguage
    };

    elmOverlay.classList.add('overlay-on');
    elmContents.innerHTML = '';
    elmLoading.classList.remove('loading-hidden');

    try {

        const result = await postJson(
            dashboardGetLessonMemoUrl,
            payload,
            10000
        );

        elmContents.innerHTML = result.data.html || '';

    } finally {

        elmLoading.classList.add('loading-hidden');
    }
}


function getCurrentLessonMemoMeta() {

    const root = document.getElementById('lessonMemoModalContentsRoot');
    if (root === null) {
        return null;
    }

    const memoId = Number(root.dataset.memoId || 0);
    const updatedAt = String(root.dataset.updatedAt || '');

    if (memoId <= 0 || updatedAt === '') {
        return null;
    }

    return {
        memo_id: memoId,
        updated_at: updatedAt
    };
}


clickHandlers['lesson_memo:show_lesson_memo_overlay'] = function (btn) {

    if (lessonMemoOverlay === null) {
        return;
    }

    lessonMemoOverlay.classList.add('overlay-on');

    handleShowLessonMemoOverlay(btn);
};

setInterval(async () => {

	if (
		lessonMemoOverlay === null ||
		!lessonMemoOverlay.classList.contains('overlay-on')
	) {
		return;
	}

    const memoMeta = getCurrentLessonMemoMeta();
    if (memoMeta === null) {
        return;
    }

    const payload = {
        memo_id: memoMeta.memo_id,
        updated_at: memoMeta.updated_at,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(
        dashboardCheckLessonMemoUpdatedUrl,
        payload
    );

    if (result.data.has_updated) {
        const elmContents = document.getElementById('lessonMemoModalContents');
        if (elmContents === null) {
            return;
        }

        elmContents.innerHTML = result.data.html || '';

    }

}, 10000);

