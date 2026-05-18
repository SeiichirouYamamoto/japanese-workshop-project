
const zoomWhiteboardIn = document.getElementById('zoomWhiteboardIn');
const zoomWhiteboardOut = document.getElementById('zoomWhiteboardOut');
const zoomWhiteboardReset = document.getElementById('zoomWhiteboardReset');


if(zoomWhiteboardIn !== null)
{zoomWhiteboardIn.addEventListener('pointerup', function() {
	zoomInWhiteboard();
}, false);}

if(zoomWhiteboardOut !== null)
{zoomWhiteboardOut.addEventListener('pointerup', function() {
	zoomOutWhiteboard();
}, false);}

if(zoomWhiteboardReset !== null)
{zoomWhiteboardReset.addEventListener('pointerup', function() {
	resetZoomWhiteboard();
}, false);}



function bindWiseExpandableToolbarEvents() {

    const currentButtons = document.querySelectorAll('.wiseToolbarCurrentButton');

    currentButtons.forEach(currentButton => {

        currentButton.addEventListener('pointerup', function(event) {

            event.preventDefault();
            event.stopPropagation();

            handleWiseExpandableToolbarButton(currentButton);

        }, false);

    });

    const toolbarButtons = document.querySelectorAll('.wiseToolbarButtonsArea .wiseVerticalToolbarButton');

    toolbarButtons.forEach(button => {

        button.addEventListener('pointerup', function() {

            updateWiseToolbarCurrentButton(button);

        }, false);

    });

}

function handleWiseExpandableToolbarButton(button) {

    if (button === null) {
        return false;
    }

    const toolbar = button.closest('.wiseExpandableToolbar');

    if (toolbar === null) {
        return false;
    }

    if (toolbar.classList.contains('is-open')) {
        closeWiseExpandableToolbar(toolbar);
		closeWiseMenuBars();
        return true;
    }

    if (toolbar.classList.contains('is-collapsed')) {
        openWiseExpandableToolbar(toolbar);
        return true;
    }

    return false;

}

function setWiseToolbarButtonDelays(toolbar, direction = 'open') {

    const buttons = toolbar.querySelectorAll('.wiseVerticalToolbarButton');
    const step = 0.04;

    buttons.forEach((button, index) => {

        let delay;

        if (direction === 'open') {
            delay = (buttons.length - 1 - index) * step;
        } else {
			delay = index * step;
        }

        button.style.transitionDelay = `${delay}s`;

    });

}

function openWiseExpandableToolbar(toolbar) {

    if (toolbar === null) {
        return;
    }

    closeOtherWiseExpandableToolbars(toolbar);
	closeWiseMenuBars();

    toolbar.classList.remove('is-collapsed');
    toolbar.classList.add('is-open');

	setWiseToolbarButtonDelays(toolbar, 'open');

	requestAnimationFrame(() => {
        positionWiseMenuBars();
    });

}


function closeWiseExpandableToolbar(toolbar) {

    if (toolbar === null) {
        return;
    }

	setWiseToolbarButtonDelays(toolbar, 'close');

    toolbar.classList.remove('is-open');
    toolbar.classList.add('is-collapsed');

}

function closeOtherWiseExpandableToolbars(activeToolbar) {

    const toolbars = document.querySelectorAll('.wiseExpandableToolbar');

    toolbars.forEach(toolbar => {

        if (toolbar !== activeToolbar) {
            closeWiseExpandableToolbar(toolbar);
        }

    });

}

function closeAllWiseExpandableToolbars() {

    const toolbars = document.querySelectorAll('.wiseExpandableToolbar');

    toolbars.forEach(toolbar => {
        closeWiseExpandableToolbar(toolbar);
    });

}

function closeWiseExpandableToolbarAfterConfirmedAction() {

    closeAllWiseExpandableToolbars();

}


function updateWiseToolbarCurrentButton(selectedButton) {

    if (selectedButton === null) {
        return;
    }

    const toolbar = selectedButton.closest('.wiseExpandableToolbar');

    if (toolbar === null) {
        return;
    }

    const currentButton = toolbar.querySelector('.wiseToolbarCurrentButton');
    const currentImg = currentButton?.querySelector('img');
    const selectedImg = selectedButton.querySelector('img');

    if (currentImg === null || selectedImg === null) {
        return;
    }

    currentImg.src = selectedImg.src;
    currentImg.alt = selectedImg.alt;
    currentImg.title = selectedImg.title;

}

function openAllWiseToolbars() {

    const toolbars = document.querySelectorAll('.wiseExpandableToolbar');

    toolbars.forEach(toolbar => {
        toolbar.classList.remove('is-collapsed');
        toolbar.classList.add('is-open');
    });

}