
function initLessonContentsLayout(isOnload) {

    if (manageLessonContentsBody === null) return;

    disableMobileGestures();
    hideMobileNavButtonOnLoad();

    resizeTopLevelItems('.manageRoomModalUiTopLevel');

    if (isOnload) {
        fetchAndRenderRoomLessonContents();
    }

    finalizeLayout();
}

function bindLessonContentsLayoutEvents() {

    if (manageLessonContentsBody === null) return;

    const onResize = throttle(() => {
        prepareLayoutOnResize();
        initLessonContentsLayout(false);
    }, 150);

    window.addEventListener('resize', onResize, { passive: true });
    // window.addEventListener('orientationchange', onResize, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {

    if (manageLessonContentsBody === null) return;

    prepareLayoutOnLoad();
    initLessonContentsLayout(true);
    bindLessonContentsLayoutEvents();

}, { passive: true });


function resizeTopLevelItems(classNaming){

	const elms = document.querySelectorAll(classNaming);

	const windowHeight = window.innerHeight;
	let elementHeight;
	let newTop;
	
	let windowWidth = window.innerWidth || 1;
	
	if (windowWidth <= 600){
		elementHeight = windowHeight;
		newTop = 0;
	}
	else{
		elementHeight = windowHeight * 0.9,
		newTop = (windowHeight - elementHeight) / 2;
	}

	elms.forEach(element => {
		element.style.height = elementHeight + 'px';
		element.style.top = newTop + 'px';
	});

}