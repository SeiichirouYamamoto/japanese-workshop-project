/******************************************************
 *  DOM
 *
 ******************************************************/
const jwsRoomSelectOverlay = document.getElementById('jwsRoomSelectOverlay');
const jwsRoomSelectSelect = document.getElementById('jwsRoomSelectSelect');
const jwsRoomSelectConfirmButton = document.getElementById('jwsRoomSelectConfirmButton');


/******************************************************
 *  EVENT
 *
 ******************************************************/
if (
    jwsRoomSelectConfirmButton !== null &&
    jwsRoomSelectSelect !== null &&
    jwsRoomSelectOverlay !== null
) {
    jwsRoomSelectConfirmButton.addEventListener(
        'pointerup',
        handleJwsRoomSelectConfirm,
        { passive: false }
    );
}



/******************************************************
 *  FUNCTIONS
 *
 ******************************************************/
async function handleJwsRoomSelectConfirm(e) {

    if (
        jwsRoomSelectOverlay === null ||
        jwsRoomSelectSelect === null ||
        jwsRoomSelectConfirmButton === null
    ) {
        return;
    }

    const roomUniqueCode = String(jwsRoomSelectSelect.value || '').trim();

    if (roomUniqueCode === '') {
        alert('ルームを選択してください。');
        return;
    }

	let willReload = false;
	setElementDisabled(jwsRoomSelectConfirmButton, true);

    const payload = {
        room_unique_code: roomUniqueCode,
        int_selected_language: intSelectedLanguage
    };

    try {

        const result = await postJson(
            dashboardSetCurrentRoomUrl,
            payload,
            10000
        );

        const isOk = (
            result &&
            result.status === 'success' &&
            result.data &&
            result.data.success === true
        );

        if (!isOk) {
            alert(result?.data?.message || result?.message || 'Error');
            return;
        }

		willReload = true;
		location.reload();

    } catch (error) {

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error.message || 'Error');
        }

    } finally {

		if (!willReload) {
			setElementDisabled(jwsRoomSelectConfirmButton, false);
		}
    }
}

function highlightHistoryCard(elm)
{
    elm.classList.remove('history-blink');
    void elm.offsetWidth; // 再トリガー
    elm.classList.add('history-blink');
}

/******************************************************
 *  clickHandlers
 *
 ******************************************************/
clickHandlers['dashboard:show_room_select_overlay'] = function (btn) {

    if (jwsRoomSelectOverlay === null) {
        return;
    }

    jwsRoomSelectOverlay.classList.add('overlay-on');
};

clickHandlers['dashboard:scroll_to_history'] = function () {

    const target = document.getElementById('dashboardHistoryCard');
    if (!target) {
        return;
    }

    const headerOffset = 80; // 固定ヘッダー高さに合わせて調整
    const rect = target.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const targetY = rect.top + scrollTop - headerOffset;

    window.scrollTo({
        top: targetY,
        behavior: 'smooth'
    });

    highlightHistoryCard(target);
};