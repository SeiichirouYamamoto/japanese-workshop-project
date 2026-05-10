const WISE_CHART_PANEL_MODE = {
    CHART: 'wisePanelChartModeChart',
    POLITE_PLAIN_TABLE: 'wisePanelChartModePolitePlainTable'
};

const WISE_CHART_PANEL_CONTENT = {
    CHART: 'wisePanelChartChartView',
    POLITE_PLAIN_TABLE: 'wisePanelPolitePlainTableView',
    WORD_X: 'wisePanelChartWordsXView',
    WORD_Y: 'wisePanelChartWordsYView',
    INFLECTION: 'wisePanelChartInflectionsView'
};

const WISE_CHART_SUBMIT_MODE = {
    CREATE: 'wisePanelChartViewSubmitButton',
    RESUBMIT: 'wisePanelChartViewReSubmitButton'
};

const wiseChartPanelState = {
    mode: WISE_CHART_PANEL_MODE.CHART,
    currentMain: WISE_CHART_PANEL_CONTENT.CHART,
    currentX: null,
    currentY: null,
	submitMode: WISE_CHART_SUBMIT_MODE.CREATE
};



function switchChartPanelView(visibleIds = []) {

    const targets = document.querySelectorAll('.wiseChartPanelContent');

    targets.forEach(el => {
        if (visibleIds.includes(el.id)) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

function renderWiseChartPanel() {

    const {
        mode,
        currentMain,
        currentX,
        currentY,
        submitMode
    } = wiseChartPanelState;

    const visibleIds = [];

    if (mode) {
        visibleIds.push(mode);
    }

    if (currentMain) {
        visibleIds.push(currentMain);
    }

    if (currentX) {
        visibleIds.push(currentX);
    }

    if (currentY) {
        visibleIds.push(currentY);
    }

    switchChartPanelView(visibleIds);
	updateWiseChartActionControls();
}

function initWisePanelChart() {

	resetWiseChartScreenValues();
    // state初期化
    wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.CHART;
    wiseChartPanelState.currentMain = null;
    wiseChartPanelState.currentX = WISE_CHART_PANEL_CONTENT.WORD_X;
    wiseChartPanelState.currentY = WISE_CHART_PANEL_CONTENT.WORD_Y;
    wiseChartPanelState.submitMode = WISE_CHART_SUBMIT_MODE.CREATE;

	chartDropdownSelect.selectedIndex = CHART_TYPE_FREE;

    // 初回描画
    renderWiseChartPanel();

}

function updateWiseChartActionControls() {

    const {
        submitMode,
        mode
    } = wiseChartPanelState;

    const isCreate = submitMode === WISE_CHART_SUBMIT_MODE.CREATE;
    const isResubmit = submitMode === WISE_CHART_SUBMIT_MODE.RESUBMIT;

    // submit系
    if (chartSubmitButton) {
        chartSubmitButton.classList.toggle('hidden', !isCreate);
    }

    if (chartResubmitButton) {
        chartResubmitButton.classList.toggle('hidden', !isResubmit);
    }

    if (chartDropdownSelect) {
        chartDropdownSelect.classList.toggle('hidden', isResubmit);
    }

    // 👇 モード切り替えボタン
    const isChartMode = mode === WISE_CHART_PANEL_MODE.CHART;
    const isPoliteMode = mode === WISE_CHART_PANEL_MODE.POLITE_PLAIN_TABLE;

    if (chartModeButton) {
        chartModeButton.classList.toggle('hidden', !isPoliteMode);
    }

    if (politePlainModeButton) {
        politePlainModeButton.classList.toggle('hidden', !isChartMode);
    }

	if (chartHistoryButton) {
		chartHistoryButton.classList.toggle('hidden', !isChartMode);
	}

	if (chartDownloadButton) {
		chartDownloadButton.classList.toggle('hidden', !isChartMode);
	}
}
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー

const chartOpenButton = document.getElementById('wisePanelChartViewOpener');

const chartPanel = document.getElementById('wisePanelChart');
const chartUiHistory = document.getElementById('wisePanelChartUiHistory');
const chartUiHistoryCloseButton = document.getElementById('wisePanelChartUiHistoryCloseButton');

const chartModeButton = document.getElementById('wisePanelChartViewModeChartButton');
const politePlainModeButton = document.getElementById('wisePanelChartViewModePolitePlainTableButton');
const chartHistoryButton = document.getElementById('wisePanelChartViewHistoryOpenButton');
const chartDownloadButton = document.getElementById('wisePanelChartViewDownloadButton');

const chartZoomInButton = document.getElementById('wisePanelChartViewZoomInButton');
const chartZoomOutButton = document.getElementById('wisePanelChartViewZoomOutButton');


const chartSubmitButton = document.getElementById('wisePanelChartViewSubmitButton');
const chartResubmitButton = document.getElementById('wisePanelChartViewReSubmitButton');


const chartDropdownSelect = document.getElementById('wisePanelChartDropDownMenuSelect');
const chartInflectionsPanel = document.getElementById('wisePanelChartInflectionsView');
const chartWordsXContainer = document.getElementById('wisePanelChartWordsXView');
const chartWordsXTextarea = document.getElementById('wisePanelChartWordsXTextarea');
const chartWordsYContainer = document.getElementById('wisePanelChartWordsYView');
const chartWordsYTextarea = document.getElementById('wisePanelChartWordsYTextarea');
const chartInflectionCheckboxes = document.querySelectorAll('.wisePanelChartInflectionsCheckbox');


const chartCanvas = document.getElementById('wisePanelChartChartView');
const chartDisplayArea = document.getElementById('wisePanelChartChartDisplayArea');
const chartAddRowsColumnsButton = document.getElementById('wisePanelChartViewAddRowsAndColumnsButton');



const chartResetButtons = document.querySelectorAll('.chartResetButton');

chartResetButtons.forEach(element => {
    element.addEventListener('pointerup', () => {
		if (element.id === 'wisePanelChartViewResetButtonChart') {
			saveChartSnapshot();
		}
        resetWiseChartScreen();
    });
});

const tableResetButton = document.getElementById('wisePanelChartTableResetButton');
if(tableResetButton !== null)
{tableResetButton.addEventListener('pointerup', function() {
	
	let isConfirmed = window.confirm(MSG_RESET_CONFIRM[intSelectedLanguage]);
	if(!isConfirmed)return;

	const textareas = document.querySelectorAll('.wisePoliteFormPlainFormTableTextarea');

	textareas.forEach(el => {
		el.value = '';
	});

}, false);}


if(chartDropdownSelect !== null){
	chartDropdownSelect.addEventListener('change',
	function (e){
		changeWiseChartScreens();		
	}
	, { passive: false });
}

if(chartModeButton !== null)
{chartModeButton.addEventListener('pointerup', function() {
	
	initWisePanelChart();

}, false);}

if(politePlainModeButton !== null)
{politePlainModeButton.addEventListener('pointerup', function() {

	// デバッグ パネル化 wordContainerから開けるようになったら削除
	const buttons = document.querySelectorAll('.wisePoliteFormPlainFormTableButtons');
	buttons.forEach(button => {
		button.classList.add('hidden');
	});

	wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.POLITE_PLAIN_TABLE;
    wiseChartPanelState.currentMain = WISE_CHART_PANEL_CONTENT.POLITE_PLAIN_TABLE;
    wiseChartPanelState.currentX = null;
    wiseChartPanelState.currentY = null;
    wiseChartPanelState.submitMode = null;
	
    renderWiseChartPanel();

}, false);}


if(chartHistoryButton !== null)
{chartHistoryButton.addEventListener('pointerup', function() {
	openWisePanelUi(chartPanel, chartUiHistory);
	renderChartHistory();
}, false);}

if(chartUiHistoryCloseButton !== null)
{chartUiHistoryCloseButton.addEventListener('pointerup', function() {
	closeWisePanelUi(chartPanel, 'target', chartUiHistory);
}, false);}


if(chartDownloadButton !== null)
{chartDownloadButton.addEventListener('pointerup', function() {
	exportChart(EXPORT_TYPE_HTML);
}, false);}


if(chartZoomInButton !== null)
{chartZoomInButton.addEventListener('pointerup', function() {
	changeWiseUiFontSize(true);
}, false);}

if(chartZoomOutButton !== null)
{chartZoomOutButton.addEventListener('pointerup', function() {
	changeWiseUiFontSize(false);
}, false);}


if(chartAddRowsColumnsButton !== null)
{chartAddRowsColumnsButton.addEventListener('pointerup', function() {
	reopenWiseChartScreens();
}, false);}


for(let i = INDEX_FIRST; i < chartInflectionCheckboxes.length; i++){
	chartInflectionCheckboxes[i].addEventListener('change',
	function (e){
	if (this.checked) {
		++inflectionsCheckboxIndex;
			this.dataset.sort = inflectionsCheckboxIndex;
	}
	}
	, { passive: false });
}


if(chartSubmitButton !== null)
{chartSubmitButton.addEventListener('pointerup', function() {
	let data = [];
	renderWiseChartScreen(data);
	return;
}, false);}

if(chartResubmitButton !== null)
{chartResubmitButton.addEventListener('pointerup', function() {
	let arr_history = chatHistory[chatHistory.length - LAST_INDEX_OFFSET],
		data = arr_history['data'];
	renderWiseChartScreen(data);
	return;
}, false);}



function resetWiseChartScreen(){

	let isConfirmed = window.confirm(MSG_RESET_CONFIRM[intSelectedLanguage]);
	if(!isConfirmed)return;

	resetWiseChartScreenValues();
	changeWiseChartScreens();

}

function resetWiseChartScreenValues(){

	chartWordsXTextarea.value = '';
	chartWordsYTextarea.value = '';

	inflectionsCheckboxIndex = INFLECTIONS_CHECKBOX_INDEX_DEFAULT
	chartInflectionCheckboxes.forEach(element => {
		element.checked = false;
		element.dataset.sort = INFLECTIONS_CHECKBOX_INDEX_DEFAULT;
	});
	chartInflectionsPanel.scrollTop = 0;
}

function openWiseChartScreens(str_x, str_y, buttonState = WISE_CHART_SUBMIT_MODE.CREATE){

	wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.CHART;
    wiseChartPanelState.currentMain = null;
    wiseChartPanelState.currentX = WISE_CHART_PANEL_CONTENT.WORD_X;
    wiseChartPanelState.currentY = WISE_CHART_PANEL_CONTENT.WORD_Y;
    wiseChartPanelState.submitMode = buttonState;

	chartDropdownSelect.selectedIndex = CHART_TYPE_FREE;
	
    renderWiseChartPanel();

	chartWordsXTextarea.value = str_x;
	chartWordsYTextarea.value = str_y;
}

function reopenWiseChartScreens(){
	
	saveChartSnapshot();
	let arr_history = chatHistory[chatHistory.length - LAST_INDEX_OFFSET],
	headers = arr_history['headers'],
	labels = arr_history['labels'],
	data = arr_history['data'],
	str_x = '',
	str_y = '';

	for(let i = INDEX_FIRST; i < headers.length; i++) {
		if(i === INDEX_FIRST){
			str_x = escapeHTML(headers[i]);
		}
		else{
			str_x = str_x + '\n' + escapeHTML(headers[i]);
		}
		}
		for(let i = INDEX_FIRST; i < labels.length; i++) {
		if(i === INDEX_FIRST){
			str_y = escapeHTML(labels[i]);
		}
		else{
			str_y = str_y + '\n' + escapeHTML(labels[i]);
		}
	}
	
	openWiseChartScreens(str_x, str_y, WISE_CHART_SUBMIT_MODE.RESUBMIT);
}

function changeWiseChartScreens(){

	let int_selected = escapeNumber(chartDropdownSelect[chartDropdownSelect.selectedIndex].value);

	wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.CHART;
	wiseChartPanelState.currentMain = null;
	
	if(int_selected === CHART_TYPE_INFLECTIONS){
		wiseChartPanelState.currentX = WISE_CHART_PANEL_CONTENT.INFLECTION;
	}
	else{
		wiseChartPanelState.currentX = WISE_CHART_PANEL_CONTENT.WORD_X;
	}
	
	wiseChartPanelState.currentY = WISE_CHART_PANEL_CONTENT.WORD_Y;
	wiseChartPanelState.submitMode = WISE_CHART_SUBMIT_MODE.CREATE;
	renderWiseChartPanel();
}


function renderWiseChartScreen(data){

	let str_wordsY = escapeHTML(chartWordsYTextarea.value);
	if(str_wordsY.length === LENGTH_EMPTY){
		alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
		return;
	}

	let arr_wordsY = str_wordsY.split(/\n/);
	arr_wordsY = arr_wordsY.filter(function(element) {
		return element !== "" && element !== null && element !== undefined && element.length > LENGTH_EMPTY;
	});
	
	if(arr_wordsY.length === LENGTH_EMPTY){
		alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
		return;
	}

	let arr_wordsX = [];

	let int_selected = escapeNumber(chartDropdownSelect[chartDropdownSelect.selectedIndex].value);
	if(int_selected === CHART_TYPE_INFLECTIONS){

		let elms_checked = chartInflectionsPanel.querySelectorAll('input[type="checkbox"]:checked');
		if(elms_checked.length === LENGTH_EMPTY){
			alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
			return;
		}
		let arr_formId = [];
		
		elms_checked.forEach(function(elm) {
			arr_formId.push({
			word : escapeHTML(elm.dataset.word),
			sort : escapeNumber(elm.dataset.sort)
			});
		});
		arr_formId.sort((a, b) => a.sort - b.sort);
		arr_wordsX = arr_formId.map(item => item.word);
	}
	else{
	
		let str_wordsX = escapeHTML(chartWordsXTextarea.value);
		if(str_wordsX.length === LENGTH_EMPTY){
			alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
			return;
		}

		arr_wordsX = str_wordsX.split(/\n/);
		arr_wordsX = arr_wordsX.filter(function(element) {
			return element !== "" && element !== null && element !== undefined && element.length > LENGTH_EMPTY;
		});
		if(arr_wordsX.length === LENGTH_EMPTY){
			alert(MSG_NO_INPUT_STRING[intSelectedLanguage]);
			return;
		}

	}


	wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.CHART;
    wiseChartPanelState.currentMain = WISE_CHART_PANEL_CONTENT.CHART;
    wiseChartPanelState.currentX = null;
    wiseChartPanelState.currentY = null;
    wiseChartPanelState.submitMode = WISE_CHART_SUBMIT_MODE.CREATE;
    renderWiseChartPanel();

	chartDisplayArea.replaceChildren();

	let table = buildChartTable(arr_wordsX, arr_wordsY, data);
	chartDisplayArea.appendChild(table);

	applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);

	let elms_wisePanelChartTextarea = table.querySelectorAll('.wisePanelChartTextarea');
	elms_wisePanelChartTextarea.forEach(elm => {
		let text = escapeHTML(elm.value);
		updateTextareaSize(elm, text);
	});
}



function buildChartTable(headers, labels, data){

	let table = document.createElement('table');
		table.id = 'wiseChartScreenChartTable';

	let headerRow = table.insertRow();
	headerRow.insertCell();
	for (let i = INDEX_FIRST; i < headers.length; i++) {
	let cell = headerRow.insertCell();
	cell.classList.add('wiseChartScreenChartHeaderX', 'wiseUiFontSizeTarget');
	cell.contentEditable = 'true';
	cell.textContent = escapeHTML(headers[i]);
	}

	for (let i = INDEX_FIRST; i < labels.length; i++) {
	let row = table.insertRow();
	let headerCell = row.insertCell();
	headerCell.classList.add('wiseChartScreenChartHeaderY', 'wiseUiFontSizeTarget');
	headerCell.contentEditable = 'true';
	headerCell.textContent = escapeHTML(labels[i]);

	for (let j = INDEX_FIRST; j < headers.length; j++) {
		let cell = row.insertCell();
		let inputBox = document.createElement('textarea');
		inputBox.classList.add('wisePanelChartTextarea', 'wiseUiFontSizeTarget');
		inputBox.type = 'text';
		if (data[i] && data[i][j] !== undefined && data[i][j] !== null && data[i][j] !== '') {
		inputBox.value = escapeHTML(data[i][j]);
		}
		inputBox.addEventListener('input',
		function (e){
			let elm = this;
			let text = escapeHTML(elm.value);
			updateTextareaSize(elm, text);
		}
		, { passive: false });
		cell.appendChild(inputBox);
	}
	}
	return table;
}



function saveChartSnapshot(){
	
	let chartInfo = getChartInfo();
	chatHistory.push(chartInfo);

}
function getChartInfo(){

	let table = document.getElementById('wiseChartScreenChartTable');
	let title = '';
	let headers = [];
	let labels = [];
	let data = [];

	if(table === null)return;

	const skipLabelIndex = 1;

	let headerRow = table.rows[INDEX_FIRST];
	for (let i = skipLabelIndex; i < headerRow.cells.length; i++) {
		if(i === skipLabelIndex){
			title = headerRow.cells[i].textContent;
		}
		else{
			title = title + ' ' + headerRow.cells[i].textContent;
		}
		headers.push(headerRow.cells[i].textContent);
	}

	for (let i = skipLabelIndex; i < table.rows.length; i++) {
		let row = table.rows[i];
		let rowData = {};
		title = title + ' ' + row.cells[INDEX_FIRST].textContent;
		rowData.label = row.cells[INDEX_FIRST].textContent;
		labels.push(rowData.label);
		rowData.values = [];
		for (let j = 1; j < row.cells.length; j++) {
			let cellValue = row.cells[j].querySelector('textarea').value;
			rowData.values.push(cellValue);
		}
		data.push(rowData.values);
	}

	return {
		title : title,
		headers : headers,
		labels : labels,
		data : data
	};
}





function exportChart(int_method) {

	let tableID = 'wiseChartScreenChartTable';
	let tableOriginal = document.getElementById(tableID);

	if(!tableOriginal){
		alert(MSG_NO_DOWNLOAD[intSelectedLanguage]);
		return;
	}

	
	let isConfirmed = window.confirm(MSG_DOWNLOAD_CONFIRM[intSelectedLanguage]);
	if(!isConfirmed)return;


	let currentDate = new Date();
	let blob;

	switch (int_method) {
	case EXPORT_TYPE_HTML:
	case EXPORT_TYPE_PDF:

		let tableNew = document.createElement('table');
		tableNew.style.borderCollapse = 'collapse';
		tableNew.style.border = '1px solid black';

		tableOriginal.querySelectorAll('tr').forEach(function(row) {
		
			let newRow = document.createElement('tr');
			
			row.querySelectorAll('td').forEach(function(cell) {
			
				let newCell = document.createElement('td');
				newCell.style.fontSize = '31px';
				newCell.style.border = '1px solid black';
				newCell.style.minWidth = '200px';
				newCell.style.padding = '5px';
				newCell.style.margin = '0';
				newCell.style.textAlign = 'center';
				
				let text = cell.querySelector('textarea') ? cell.querySelector('textarea').value : cell.textContent.trim();
				text = escapeHTML(text);
				text = text.replace(/\n/g, '<br>');
				
				newCell.innerHTML = text;
				newRow.appendChild(newCell);
			});
			
			tableNew.appendChild(newRow);
		});

		let mainTitle = '';
		let htmlString = `
				<!DOCTYPE html>
				<html lang="ja">
					<head>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, initial-scale=1.0">
						<title>${mainTitle}</title>
					</head>
					<body>
						${tableNew.outerHTML}
					</body>
				</html>
				`;

		switch (int_method) {
		case EXPORT_TYPE_HTML:
			const filenameHTML = 'chart' + formatDate(currentDate) + '.html';
			const filenameZIP = 'chart' + formatDate(currentDate) + '.zip';
			downloadContentsAsZip(filenameHTML, filenameZIP, htmlString);
			// filename = 'chart' + formatDate(currentDate) + '.html';
			// blob = new Blob([htmlString], { type: 'text/html;charset=utf-8' });
			// downloadContents(blob, filename);
			break;
		case EXPORT_TYPE_PDF:
			const filenamePDF = 'chart' + formatDate(currentDate) + '.pdf';
			downloadPdf(tableNew, filenamePDF);
			break;
		}
		tableNew.remove();
		break;

	case EXPORT_TYPE_CSV:
		let csvString = '';
		let csv = [];
		let rows = document.getElementById(tableID).querySelectorAll("tr");

		csv.push('\uFEFF');
	
		for (let i = INDEX_FIRST; i < rows.length; i++) {
		let row = [], cols = rows[i].querySelectorAll("td, th");
		for (let j = INDEX_FIRST; j < cols.length; j++) {
			let text;
			if (cols[j].querySelector("textarea")) {
			text = cols[j].querySelector("textarea").value;
			} else {
			text = cols[j].innerText;
			}
			text = escapeHTML(text);
			if (/\n/.test(text)) {
			text = '"' + text.replace(/"/g, '""').replace(/\n/g, '\r\n') + '"';
			}
			row.push(text);
		}
		csv.push(row.join(","));
		}
		csvString = csv.join("\n");

		const filenameCSV = 'chart' + formatDate(currentDate) + '.csv';
		blob = new Blob([csvString], { type: "text/csv;charset=utf-8" });
		downloadContents(blob, filenameCSV);
		break;
	
	default:
		return;
	}
}


async function createChartForRegisteredSentences(str_grammarUniqueCode) {

	try {

		const payload = {
			str_grammarUniqueCode: str_grammarUniqueCode,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			sentenceGetTranslationsByGrammar,
			payload,
			10000
		);

		const data = Array.isArray(result.data) ? result.data : [];

		if (data.length === LENGTH_EMPTY) {
			return;
		}

		let str_y = '';

		for (let i = INDEX_FIRST; i < data.length; i++) {
			str_y += escapeHTML(data[i]);
			if (i < data.length - LAST_INDEX_OFFSET) {
				str_y += '\n';
			}
		}

		const str_x = '日本語';

		const panelId = chartOpenButton.dataset.panelId;
		if (!panelId) {
			return;
		}

		openWiseChartScreens(str_x, str_y, WISE_CHART_SUBMIT_MODE.CREATE);
		openWisePanelPositionSelectUi(panelId, true);

		return;

	} catch (error) {
		if (error.message && error.message.includes('タイムアウト')) {
			console.error('タイムアウトが発生しました。');
		} else {
			console.error('Error:', error.message || error);
			alert(error.message || 'Error');
		}
		return;
	}
}


function renderChartHistory(){

	let elm_targetUl;

	elm_targetUl = document.getElementById('wisePanelChartUiHistoryList');
	elm_targetUl.replaceChildren();

	if (chatHistory.length === LENGTH_EMPTY) {
		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('resultEmpty');
			elm_addLi.textContent = '検索結果 0件';
			elm_targetUl.appendChild(elm_addLi);
		return;
	}

	for (let i = INDEX_FIRST; i < chatHistory.length; i ++) {

		let str_title = escapeHTML(chatHistory[i]['title']);

		let elm_addLi = document.createElement('li');
			elm_addLi.classList.add('wisePanelWhiteboardUiChartHistoryLi', 'wiseUiFontSizeTarget');
			elm_addLi.dataset.index = i;
			elm_addLi.textContent = str_title;

			elm_addLi.addEventListener('pointerup', function() {
				openWiseChartFromHistory(chatHistory[i]);
			}, false);

		elm_targetUl.appendChild(elm_addLi);
		applyFontSizeVariation(
			['wiseUiFontSizeTarget'],
			'wiseUiFontSizeTargetVariationDifference'
		);
	}
}

function openWiseChartFromHistory(array){
	
	wiseChartPanelState.mode = WISE_CHART_PANEL_MODE.CHART;
    wiseChartPanelState.currentMain = WISE_CHART_PANEL_CONTENT.CHART;
    wiseChartPanelState.currentX = null;
    wiseChartPanelState.currentY = null;
    wiseChartPanelState.submitMode = WISE_CHART_SUBMIT_MODE.CREATE;
    renderWiseChartPanel();

	chartDisplayArea.replaceChildren();

	let headers = array['headers'],
		labels = array['labels'],
		data = array['data'];
	
	let table = buildChartTable(headers, labels, data);
	chartDisplayArea.appendChild(table);

	applyFontSizeVariation(
		['wiseUiFontSizeTarget'],
		'wiseUiFontSizeTargetVariationDifference'
	);

	let elms_wisePanelChartTextarea = table.querySelectorAll('.wisePanelChartTextarea');

	elms_wisePanelChartTextarea.forEach(elm => {
		let text = escapeHTML(elm.value);
		updateTextareaSize(elm, text);
	});
}