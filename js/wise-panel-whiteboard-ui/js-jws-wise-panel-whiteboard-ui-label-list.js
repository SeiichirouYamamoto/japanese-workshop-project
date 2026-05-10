

/******************************************************
 *  LABEL LIST
 *
 ******************************************************/

const whiteboardUiLabelList = document.getElementById('wisePanelWhiteboardUiLabelList');

document.addEventListener('pointerup', async (e) => {

    const li = e.target.closest('.whiteboardUiLabelListLi');
    if (!li) {
        return;
    }

    const send_sub_classification_id = escapeNumber(li.dataset.subClassificationId);
    const send_label_id = escapeNumber(li.dataset.labelId);

    contextMenuTargetSubClassificationId = send_sub_classification_id;
    contextMenuTargetLabelId = send_label_id;

    const inflection = await getInflection();
    if (!inflection) {
        return;
    }

    applyInflectionToWordContainer(inflection);

    saveState(STATE_TITLE_CHANGE_LABEL[intSelectedLanguage] + ' ' + escapeHTML(inflection.japanese));
});