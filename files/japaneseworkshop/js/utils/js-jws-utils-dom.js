
function createTableFromHTML(htmlString) {
	const tableWrapper = document.createElement('div');
	tableWrapper.innerHTML = htmlString;
	return tableWrapper.firstChild;
}


function findLi(element) {
	if (element.tagName.toLowerCase() === 'li') {
		return element;
	}
	if (element.parentNode) {
		return findLi(element.parentNode);
	}
	return null;
}

function updateButtonsVisibility(container) {
    const current = Number(container.dataset.currentIndex) || 0;
    const max = Number(container.dataset.maxIndex) || 0;

    const prev = container.querySelector(".frameInSliderPrevButton");
    const next = container.querySelector(".frameInSliderNextButton");

	setElementDisabled(prev, current <= INDEX_FIRST);
	setElementDisabled(next, current >= max);
}

function isElementVisible(elm) {
	while (elm) {
		const style = window.getComputedStyle(elm);
		if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
			return false;
		}
		elm = elm.parentElement;
	}
	return true;
}

function restoreSelectValueIfExists(selectEl, targetValue) {
    if (!selectEl || targetValue === undefined || targetValue === null) return false;

    const value = String(targetValue);

    for (let i = 0; i < selectEl.options.length; i++) {
        if (String(selectEl.options[i].value) === value) {
            selectEl.selectedIndex = i;
            return true;
        }
    }

    return false;
}
