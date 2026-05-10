let global_context_canvas_globalCanvas;


if(globalCanvas !== null){
	globalCanvas.addEventListener('touchmove', (e) => {
		preventDefaultOnTouchMove(e);
	}, { passive: false });
	global_context_canvas_globalCanvas = globalCanvas.getContext('2d',{
		willReadFrequently: true
	});

	let elm_globalPenCursorSpan = document.createElement('span');
    elm_globalPenCursorSpan.setAttribute('id', 'globalPenCursorSpan');
	elm_globalPenCursorSpan.className = 'wiseDecorativeItem';
    elm_globalPenCursorSpan.textContent = '';
    elm_globalPenCursorSpan.style.left = '0px';
    elm_globalPenCursorSpan.style.top = '0px';
    elm_globalPenCursorSpan.style.visibility = 'hidden';
    document.body.appendChild(elm_globalPenCursorSpan);

	if (globalToolbarSelectorButton !== null) {
		switchGlobalVerticalToolbarButton(globalToolbarSelectorButton);
	}

	resizeGlobalCanvas();
}


function resizeGlobalCanvas() {

    if (globalCanvas === null) {
        return;
    }

    let width = window.innerWidth;
    let height = window.innerHeight;

	const vv = window.visualViewport;

	if (vv) {
        width = vv.width;
        height = vv.height;
    }

	width = Math.round(width);
	height = Math.round(height);

    ensureWiseVh(height);

    globalCanvas.width = width;
	globalCanvas.height = height;
}

if(globalToolbarSelectorButton !== null)
{globalToolbarSelectorButton.addEventListener('pointerup', function() {
	switchGlobalVerticalToolbarButton(this);
}, false);}


if(globalToolbarLaserButton !== null)
{globalToolbarLaserButton.addEventListener('pointerup', function() {
	switchGlobalVerticalToolbarButton(this);
}, false);}


if(globalToolbarOpenWiseButton !== null)
{globalToolbarOpenWiseButton.addEventListener('pointerup', function() {
	window.open(pageWiseUrl, "_blank");
}, false);}


if(globalToolbarManageRoomsButton !== null)
{globalToolbarManageRoomsButton.addEventListener('pointerup', function() {
	window.open(pageManageRoomsUrl, '_blank', 'noopener');
}, false);}




function switchGlobalVerticalToolbarButton(elm){

	if(elm === globalToolbarLaserButton){
		currentGlobalToolbarButton = 'globalVerticalToolbarLaserButton';
		globalCanvas.style.pointerEvents = 'auto';
	}
	else{
		currentGlobalToolbarButton = 'globalVerticalToolbarSelectorButton';
		globalCanvas.style.pointerEvents = 'none';
	}

	let elms_toggle = document.querySelectorAll('.globalVerticalToolbarButtonToggle'),
		elm_toggle;

	for(let i = INDEX_FIRST; i < elms_toggle.length; i++){
		elm_toggle = elms_toggle[i];
		if(elm_toggle === elm){
			elm_toggle.classList.add('globalVerticalToolbarButton-selected');
		}
		else{
			elm_toggle.classList.remove('globalVerticalToolbarButton-selected');
		}
	}

	let elms_searchTarget = document.querySelectorAll('.search-target'),
		elm_searchTarget;

	for(let i = INDEX_FIRST; i < elms_searchTarget.length; i++){
		elm_searchTarget = elms_searchTarget[i];
		elm_searchTarget.classList.remove('search-target');
	}
	closeGlobalVerticalToolbarMenus();
}


function closeGlobalVerticalToolbarMenus(){
	let elms_globalVerticalToolbarMenuBar = document.querySelectorAll('.globalVerticalToolbarMenuBar'),
		elm_globalVerticalToolbarMenuBar;
	for(let i = INDEX_FIRST; i < elms_globalVerticalToolbarMenuBar.length; i++){
	elm_globalVerticalToolbarMenuBar = elms_globalVerticalToolbarMenuBar[i];
	elm_globalVerticalToolbarMenuBar.classList.remove('globalVerticalToolbarMenuBar-open');
	}
}

function updateGlobalDrawingCursor(x, y) {

    const elm_globalPenCursorSpan = document.getElementById('globalPenCursorSpan');

    if (elm_globalPenCursorSpan === null) {
        return;
    }

    if (
        currentWiseToolbarButton === 'wiseVerticalToolbarLaserButton' ||
        currentGlobalToolbarButton === 'globalVerticalToolbarLaserButton'
    ){
        document.body.classList.add('cursor-hidden');

        elm_globalPenCursorSpan.style.visibility = 'visible';
        elm_globalPenCursorSpan.style.left = x + 'px';
        elm_globalPenCursorSpan.style.top = y + 'px';

        return;
    }

    document.body.classList.remove('cursor-hidden');
    elm_globalPenCursorSpan.style.visibility = 'hidden';
}

function hideGlobalDrawingCursor() {

    const elm_globalPenCursorSpan = document.getElementById('globalPenCursorSpan');

    document.body.classList.remove('cursor-hidden');

    if (elm_globalPenCursorSpan !== null) {
        elm_globalPenCursorSpan.style.visibility = 'hidden';
    }
}