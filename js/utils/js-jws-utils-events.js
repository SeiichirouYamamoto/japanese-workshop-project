
function preventDefaultOnTouchMove(e){
	if(e.cancelable){
		e.preventDefault();
	}
}

function handleOverlayCloseClick(elm){
	let overlay = elm.closest('.overlay-on');
	if (overlay) {
		overlay.classList.remove('overlay-on');
	}
}

function guardDisabledEvent(elm, e) {

    if (!elm) {
        return true;
    }

    const isDisabled =
        elm.disabled === true ||
        elm.getAttribute('disabled') !== null ||
        elm.getAttribute('aria-disabled') === 'true' ||
        elm.classList.contains('disabled');

    if (isDisabled) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        return true;
    }

    return false;
}