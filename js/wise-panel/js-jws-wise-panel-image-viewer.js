/******************************************************
 *  Image Viewer : Elements
 ******************************************************/

const wisePanelImageViewerOpenFileButton =
    document.getElementById('wisePanelImageViewerViewOpenFileButton');

const wisePanelImageViewerFileInput =
    document.getElementById('wisePanelImageViewerFileInput');

const wisePanelImageViewerImageContainer =
    document.getElementById('wisePanelImageViewerImageContainer');

const wisePanelImageViewerPdfContainer =
    document.getElementById('wisePanelImageViewerPdfContainer');

const wisePanelImageViewerImageCanvas =
    document.getElementById('wisePanelImageViewerImageCanvas');

const wisePanelImageViewerTextLayer =
    document.getElementById('wisePanelImageViewerTextLayer');


const zoomInBtn = document.getElementById('wisePanelImageViewerViewZoomInButton');
const zoomOutBtn = document.getElementById('wisePanelImageViewerViewZoomOutButton');

if (zoomInBtn) {
    zoomInBtn.addEventListener('pointerup', zoomInImageViewer);
}

if (zoomOutBtn) {
    zoomOutBtn.addEventListener('pointerup', zoomOutImageViewer);
}

/******************************************************
 *  Event Bind
 ******************************************************/

if (wisePanelImageViewerOpenFileButton && wisePanelImageViewerFileInput) {
    wisePanelImageViewerOpenFileButton.addEventListener('pointerup', function () {
        wisePanelImageViewerFileInput.click();
    });
}

if (wisePanelImageViewerFileInput) {
    wisePanelImageViewerFileInput.addEventListener('change', function (e) {

        const file = e.target.files[0];

        if (!file) {
            return;
        }

        handleWisePanelImageViewerFile(file);

        // 同じファイルを再選択できるようにリセット
        wisePanelImageViewerFileInput.value = '';
    });
}


/******************************************************
 *  File Handler
 ******************************************************/

function handleWisePanelImageViewerFile(file) {

    if (file.type.startsWith('image/')) {
        loadWisePanelImageViewerImage(file);
        return;
    }

    if (file.type === 'application/pdf') {
        loadWisePanelImageViewerPdf(file);
        return;
    }

    alert('対応していないファイル形式です。');
}


/******************************************************
 *  Mode Switch
 ******************************************************/

function switchWisePanelImageViewerMode(mode) {

    if (!wisePanelImageViewerImageContainer || !wisePanelImageViewerPdfContainer) {
        return;
    }

    wisePanelImageViewerImageContainer.classList.add('hidden');
    wisePanelImageViewerPdfContainer.classList.add('hidden');

    if (mode === 'image') {
        wisePanelImageViewerImageContainer.classList.remove('hidden');
    }

    if (mode === 'pdf') {
        wisePanelImageViewerPdfContainer.classList.remove('hidden');
    }
}


/******************************************************
 *  Image Loader
 ******************************************************/

function loadWisePanelImageViewerImage(file) {

    if (!wisePanelImageViewerImageCanvas) {
        return;
    }

    switchWisePanelImageViewerMode('image');

    const ctx = wisePanelImageViewerImageCanvas.getContext('2d');
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = function () {

        wisePanelImageViewerImageCanvas.width = img.naturalWidth;
        wisePanelImageViewerImageCanvas.height = img.naturalHeight;

        ctx.clearRect(
            0,
            0,
            wisePanelImageViewerImageCanvas.width,
            wisePanelImageViewerImageCanvas.height
        );

        ctx.drawImage(img, 0, 0);

        URL.revokeObjectURL(objectUrl);
    };

    img.src = objectUrl;
}


/******************************************************
 *  PDF Loader
 ******************************************************/

let wisePanelImageViewerPdfDoc = null;

async function loadWisePanelImageViewerPdf(file) {

	if (!wisePanelImageViewerPdfContainer) {
		return;
	}

    if (typeof pdfjsLib === 'undefined') {
        alert('PDF.js が読み込まれていません。');
        return;
    }

    switchWisePanelImageViewerMode('pdf');

    const objectUrl = URL.createObjectURL(file);

    try {
        wisePanelImageViewerPdfDoc =
            await pdfjsLib.getDocument(objectUrl).promise;

		await renderWisePanelImageViewerPdfAllPages();

    } finally {
        URL.revokeObjectURL(objectUrl);
    }
}


async function renderWisePanelImageViewerPdfAllPages() {

    if (!wisePanelImageViewerPdfDoc || !wisePanelImageViewerPdfContainer) {
        return;
    }

    wisePanelImageViewerPdfContainer.innerHTML = '';

    for (let pageNumber = 1; pageNumber <= wisePanelImageViewerPdfDoc.numPages; pageNumber++) {
        await renderWisePanelImageViewerPdfPage(pageNumber);
    }
}

async function renderWisePanelImageViewerPdfPage(pageNumber) {

    if (!wisePanelImageViewerPdfDoc || !wisePanelImageViewerPdfContainer) {
        return;
    }

    const page = await wisePanelImageViewerPdfDoc.getPage(pageNumber);

    const displayScale = 1;
    const outputScale = window.devicePixelRatio || 1;

    const viewport = page.getViewport({ scale: displayScale });

    const pageContainer = document.createElement('div');
    pageContainer.classList.add('wisePanelImageViewerPdfPage');

    const canvas = document.createElement('canvas');
    canvas.classList.add('wisePanelImageViewerPdfCanvas');

    const textLayer = document.createElement('div');
    textLayer.classList.add('wisePanelImageViewerTextLayer');

    const ctx = canvas.getContext('2d');

    canvas.width = Math.floor(viewport.width * outputScale);
    canvas.height = Math.floor(viewport.height * outputScale);

    canvas.style.width = viewport.width + 'px';
    canvas.style.height = viewport.height + 'px';

    pageContainer.style.width = viewport.width + 'px';
    pageContainer.style.height = viewport.height + 'px';

    textLayer.style.width = viewport.width + 'px';
    textLayer.style.height = viewport.height + 'px';

    textLayer.style.setProperty('--scale-factor', viewport.scale);

    const transform =
        outputScale !== 1
            ? [outputScale, 0, 0, outputScale, 0, 0]
            : null;

    pageContainer.appendChild(canvas);
    pageContainer.appendChild(textLayer);

    wisePanelImageViewerPdfContainer.appendChild(pageContainer);

    await page.render({
        canvasContext: ctx,
        viewport: viewport,
        transform: transform
    }).promise;

    await renderWisePanelImageViewerTextLayer(page, viewport, textLayer);
}

async function renderWisePanelImageViewerTextLayer(page, viewport, textLayer) {

    if (!textLayer) {
        return;
    }

    textLayer.innerHTML = '';

    const textContent = await page.getTextContent();

    if (typeof pdfjsLib.renderTextLayer !== 'function') {
        console.warn('pdfjsLib.renderTextLayer is not available.');
        return;
    }

    await pdfjsLib.renderTextLayer({
        textContentSource: textContent,
        container: textLayer,
        viewport: viewport,
        textDivs: []
    }).promise;
}
/******************************************************
 *  Image Viewer : State
 ******************************************************/

const imageViewerState = {
    zoomScale: zoomScaleDefault
};


/******************************************************
 *  Zoom Controls
 ******************************************************/

function zoomInImageViewer() {
    setImageViewerZoomScale(imageViewerState.zoomScale + 0.1);
}

function zoomOutImageViewer() {
    setImageViewerZoomScale(imageViewerState.zoomScale - 0.1);
}

function resetZoomImageViewer() {
    setImageViewerZoomScale(zoomScaleDefault);
}


/******************************************************
 *  Core Zoom
 ******************************************************/

function setImageViewerZoomScale(newScale) {

    if (typeof newScale !== 'number' || Number.isNaN(newScale)) {
        return;
    }

    if (newScale < zoomScaleMin) {
        newScale = zoomScaleMin;
    }

    if (newScale > zoomScaleMax) {
        newScale = zoomScaleMax;
    }

    imageViewerState.zoomScale = newScale;

    applyImageViewerZoom();
}


/******************************************************
 *  Apply Zoom
 ******************************************************/

function applyImageViewerZoom() {

    const zoomStage = document.getElementById('wisePanelImageViewerZoomStage');

    if (!zoomStage) {
        return;
    }

    const scale = imageViewerState.zoomScale;

    zoomStage.style.transform = 'scale(' + scale + ')';
}