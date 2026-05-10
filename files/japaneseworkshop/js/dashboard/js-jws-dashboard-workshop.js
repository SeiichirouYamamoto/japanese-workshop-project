
const workshopNextLessonButton = document.getElementById('workshopNextLessonButton');
const workshopOverlayGrammar = document.getElementById('workshopOverlayGrammar');


if (
    workshopNextLessonButton !== null
) {
    workshopNextLessonButton.addEventListener(
        'pointerup',
        handleWorkshopNextLessonClick,
        { passive: false }
    );
}


async function handleWorkshopLessonStart(btn) {

    const elmLoading = document.getElementById('workshopGrammarModalLoading');
    const elmContents = document.getElementById('workshopGrammarModalContents');

    if (elmLoading === null || elmContents === null) {
        return;
    }

    const roomUniqueCode = String(btn?.dataset?.roomUniqueCode || '').trim();
    const lessonIdRaw = String(btn?.dataset?.lessonId || '').trim();
    const teachingMaterialLessonIdRaw = String(btn?.dataset?.teachingMaterialLessonId || '').trim();

    const payload = {
        room_unique_code: roomUniqueCode,
        lesson_id: lessonIdRaw === '' ? null : Number(lessonIdRaw),
        teaching_material_lesson_id: teachingMaterialLessonIdRaw === '' ? null : Number(teachingMaterialLessonIdRaw),
        int_selected_language: intSelectedLanguage
    };

    /* --------------------
       loading ON
    -------------------- */
    elmContents.innerHTML = '';
    elmLoading.classList.remove('loading-hidden');

    try {

        const result = await postJson(
            dashboardGetGrammarListUrl,
            payload,
            10000
        );

        /* --------------------
           render html
        -------------------- */
        elmContents.innerHTML = result.data.html || '';

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error.message || 'Error');
        }

    } finally {

        /* --------------------
           loading OFF
        -------------------- */
        elmLoading.classList.add('loading-hidden');
    }
}


async function handleWorkshopNextLessonClick(e) {

    if (e) {
        e.preventDefault();
    }

    if (workshopNextLessonButton === null) {
        return;
    }

	let willReload = false;
	setElementDisabled(workshopNextLessonButton, true);

    const payload = {
        int_selected_language: intSelectedLanguage
    };

    try {

        const result = await postJson(
            dashboardRegisterNewLessonUrl,
            payload,
            10000
        );

        const data = (
            result &&
            typeof result === 'object' &&
            Object.prototype.hasOwnProperty.call(result, 'data')
        ) ? result.data : result;

        const isOk = (
            data &&
            data.success === true
        );

        if (!isOk) {
            alert(data?.message || result?.message || 'Error');
            return;
        }

		if (data.added === false) {
			if (typeof data.message === 'string' && data.message !== '') {
				alert(data.message);
			}
			return;
		}

		willReload = true;
        location.reload();

    } catch (error) {

        if (error?.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error?.message || 'Error');
        }

    } finally {
		if (!willReload) {
			setElementDisabled(workshopNextLessonButton, false);
		}
    }
}



clickHandlers['workshop:lesson:start'] = function (btn) {

    if (workshopOverlayGrammar === null) {
        return;
    }

    workshopOverlayGrammar.classList.add('overlay-on');

	handleWorkshopLessonStart(btn);
};
