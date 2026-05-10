
function escapeHTML(str) {
	if (str === null || str === undefined) {
		return '';
	}
	if (typeof str !== 'string' || !str) {
		if(str === '' || str.length === LENGTH_EMPTY){
			return '';
		}
		else{
			return escapeNumber(str);
		}
	}
	str = str.replace(/&/g, '&amp;');
	str = str.replace(/</g, '&lt;');
	str = str.replace(/>/g, '&gt;');
	str = str.replace(/"/g, '&quot;');
	str = str.replace(/'/g, '&#39;');
	return str;
}


function escapeNumber(str) {
	if (str === null || str === undefined) {
		return 0;
	}
	if (typeof str === 'number') {
		return str;
	}
	if (!isNaN(str) && str !== '') {
		str = parseInt(str, 10);
		return str;
	} else {
		return 0;
	}
}


function escapeFloatNumber(str) {
	if (str === null || str === undefined) {
		return 0;
	}
	if(typeof(str) === 'number')return str;
	str = str.replace(/[^0-9.]/g, '');
	return str;
}


function escapeKanaCharacters(str) {
	str = str.replace(/[^ぁ-んー0-9a-zA-Z]/gi, '');
	return str;
}


function escapeHtmlAttribute(unsafe) {
	return unsafe.replace(/[&<"'\/]/g, match => {
		switch (match) {
			case "&":
			return "&amp;";
			case "<":
			return "&lt;";
			case '"':
			return "&quot;";
			case "'":
			return "&#039;";
			case "/":
			return "&#x2F;";
			default:
			return match;
		}
	});
}


function escapeTextareaMeasurementText(str) {
	if (typeof str !== 'string' || !str) {
		return '';
	}
	str = str.replace(/&amp;/g, '€');
	str = str.replace(/&lt;/g, '€');
	str = str.replace(/&gt;/g, '€');
	str = str.replace(/&quot;/g, '€');
	str = str.replace(/&#39;/g, '€');
	return str;
}


function isEmptyValue(value) {
	return (
		value == null ||
		value === false ||
		value === "" ||
		value === 0 ||
		value === "0" ||
		(Array.isArray(value) && value.length === LENGTH_EMPTY) ||
		(typeof value === "object" && Object.keys(value).length === LENGTH_EMPTY)
	);
}


function toNullableInteger(v) {
    if (v === null || v === undefined) return null;
    if (typeof v === 'string') {
        const trimmed = v.trim();
        if (trimmed === '' || trimmed.toLowerCase() === 'null') return null;
        if (!isNaN(trimmed)) return parseInt(trimmed, 10);
        return null;
    }
    if (typeof v === 'number') return Number.isFinite(v) ? v : null;
    return null;
}


function parseIntStrict(v) {
	const n = parseInt(v, 10);
	return Number.isNaN(n) ? null : n;
}
