/******************************************************
 *  FUNCTIONS
 *
 ******************************************************/
async function handleBookmarkToggle(btn) {

    if (!btn || !btn.classList.contains('bookmarkStar')) {
        return;
    }

    const grammarUniqueCode = btn.dataset.grammarUniqueCode || '';
    const roomUniqueCode = btn.dataset.roomUniqueCode || '';

    if (grammarUniqueCode === '' || roomUniqueCode === '') {
        return;
    }

    const isActiveNow = btn.getAttribute('aria-pressed') === 'true';
    const isActiveNext = !isActiveNow;

    if (btn.dataset.isProcessing === '1') {
        return;
    }
    btn.dataset.isProcessing = '1';

    // === UIを先に反映（楽観的更新）===
    btn.setAttribute('aria-pressed', isActiveNext ? 'true' : 'false');
    btn.classList.toggle('isActive', isActiveNext);

	setElementDisabled(btn, true);

    try {

        const payload = {
            room_unique_code: roomUniqueCode,
            grammar_unique_code: grammarUniqueCode,
            is_bookmarked: isActiveNext ? 1 : 0,
            int_selected_language: intSelectedLanguage
        };

        const result = await postJson(
            wiseCoreRegisterBookmarkUrl,
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
            throw new Error(result?.data?.message || result?.message || 'Error');
        }

    } catch (error) {

        // === 失敗時は元に戻す ===
        btn.setAttribute('aria-pressed', isActiveNow ? 'true' : 'false');
        btn.classList.toggle('isActive', isActiveNow);

        if (error.message && error.message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(error.message || 'Error');
        }

    } finally {

		setElementDisabled(btn, false);
        btn.dataset.isProcessing = '0';
    }
}


/******************************************************
 *  clickHandlers
 *
 ******************************************************/
clickHandlers['bookmark:toggle'] = function (btn) {

    handleBookmarkToggle(btn);
};
