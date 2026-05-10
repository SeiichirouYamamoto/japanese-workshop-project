

const quizPanel = document.getElementById('wisePanelQuiz');
const quizUiFeedback = document.getElementById('wisePanelQuizUiFeedback');
const quizUiFeedbackCloseButton = document.getElementById('wisePanelQuizUiFeedbackCloseButton');

const quizUiSettings = document.getElementById('wisePanelQuizUiSettings');
const quizUiSettingsCloseButton = document.getElementById('wisePanelQuizUiSettingsCloseButton');

const quizUiHistory = document.getElementById('wisePanelQuizUiHistory');
const quizUiHistoryCloseButton = document.getElementById('wisePanelQuizUiHistoryCloseButton');

const quizPanelSelectQuizConfirmButton = document.getElementById('wisePanelQuizHeaderConfirmButton');
const quizPanelNextButton = document.getElementById('wisePanelQuizViewNextButton');
const quizPanelSettingsButton = document.getElementById('wisePanelQuizHeaderSettingsButton');
const quizPanelHistoryButton = document.getElementById('wisePanelQuizHeaderHistoryButton');


const quizPanelZoomInButton = document.getElementById('wisePanelQuizHeaderZoomInButton');
const quizPanelZoomOutButton = document.getElementById('wisePanelQuizHeaderZoomOutButton');


if(quizPanelSelectQuizConfirmButton !== null)
{quizPanelSelectQuizConfirmButton.addEventListener('pointerup', function() {

	const pageType = 'wise';
	const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
	buildQuizContentsSection(this, pageType, room_unique_code);
	
}, false);}


if(quizPanelNextButton !== null)
{quizPanelNextButton.addEventListener('pointerup', function() {

	const pageType = 'wise';
    const room_unique_code = escapeHTML(wiseSetupRoomSelect.value) || 'default';
    buildQuizContentsSection(this, pageType, room_unique_code);
	
}, false);}


if(quizPanelSettingsButton !== null)
{quizPanelSettingsButton.addEventListener('pointerup', function() {
	openWisePanelUi(quizPanel, quizUiSettings);
}, false);}


if(quizPanelHistoryButton !== null)
{quizPanelHistoryButton.addEventListener('pointerup', function() {

	const container = document.getElementById('quizHistoryScreenTableContainer');
    if (!container) {
        return;
    }

    container.innerHTML = '';
    container.appendChild(buildQuizHistoryTable());

	openWisePanelUi(quizPanel, quizUiHistory);
}, false);}


if(quizUiFeedbackCloseButton !== null)
{quizUiFeedbackCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(quizPanel, 'target', quizUiFeedback);
}, false);}

if(quizUiSettingsCloseButton !== null)
{quizUiSettingsCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(quizPanel, 'target', quizUiSettings);
}, false);}

if(quizUiHistoryCloseButton !== null)
{quizUiHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(quizPanel, 'target', quizUiHistory);
}, false);}

if(quizPanelZoomInButton !== null)
{quizPanelZoomInButton.addEventListener('pointerup', function() {
	changeWiseUiFontSize(true);
}, false);}

if(quizPanelZoomOutButton !== null)
{quizPanelZoomOutButton.addEventListener('pointerup', function() {
	changeWiseUiFontSize(false);
}, false);}



function buildQuizHistoryTable() {

    const table = document.createElement('table');
    table.style.width = '100%';
    table.setAttribute('border', '1');

    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr>
            <th>クイズの種類</th>
            <th>出題内容</th>
        </tr>
    `;
    table.appendChild(thead);

    const tbody = document.createElement('tbody');

    quizHistory.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.quizType}</td>
            <td>${item.quizHistoryPrompt}</td>
        `;
        tbody.appendChild(tr);
    });

    table.appendChild(tbody);

    return table;
}