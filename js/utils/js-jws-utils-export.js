
function downloadContents(blob, filename){
	if (navigator.msSaveBlob) {
	navigator.msSaveBlob(blob, filename);
	} else {
	let link = document.createElement("a");
	if (link.download !== undefined) { 
		let url = URL.createObjectURL(blob);
		link.href = url;
		link.download = filename;
		link.style.visibility = "hidden";
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
		setTimeout(() => URL.revokeObjectURL(url), 1000);
	} else {
		alert("Your browser does not support the download attribute.");
	}
	}
}

function downloadContentsAsZip(filenameHTML, filenameZIP, htmlString){
	const zip = new JSZip();
	zip.file(filenameHTML, htmlString);
	zip.generateAsync({ type: "blob" }).then(function (content) {
		const link = document.createElement('a');
		link.href = URL.createObjectURL(content);
		link.download = filenameZIP;
		link.click();
		link.remove();
		setTimeout(() => URL.revokeObjectURL(link.href), 1000);
	});
}

function downloadPdf(entryBody, filename){
	const pageHeightMm = 297; // A4ページの高さ (mm)
	const pageWidthMm = 210;  // A4ページの幅 (mm)
	const topMarginMm = 10;   // 上マージン (mm)
	const bottomMarginMm = 10; // 下マージン (mm)

	const mmToPx = 3.7795275591;
	const pageHeightPx = pageHeightMm * mmToPx;
	const pageWidthPx = pageWidthMm * mmToPx;
	const topMarginPx = topMarginMm * mmToPx;
	const bottomMarginPx = bottomMarginMm * mmToPx;

	const usablePageHeightPx = pageHeightPx - topMarginPx - bottomMarginPx;

	let currentHeight = 0;
	const elements = Array.from(entryBody.children);

	elements.forEach((element) => {
		const rect = element.getBoundingClientRect();
		currentHeight += rect.height;

		if (currentHeight > usablePageHeightPx) {
			element.classList.add('pageBreakTarget');
			currentHeight = rect.height;
		}
	});

	const topMarginInches = topMarginMm / 25.4;
	const bottomMarginInches = bottomMarginMm / 25.4;

	const options = {
		margin: [topMarginInches, 0.5, bottomMarginInches, 0.5],
		filename: filename,
		pagebreak: { mode: 'avoid-all', before: '.pageBreakTarget' },
		image: { type: 'jpeg', quality: 0.98 },
		html2canvas: { scale: 2 },
		jsPDF: { format: 'a4', orientation: 'portrait' }
	};

	window.scrollTo(0, 0);

	html2pdf()
		.from(entryBody)
		.set(options)
		.save()
		.then(() => {
			console.log('PDF generated and saved.');
		})
		.catch((error) => {
			console.error('Error generating PDF:', error);
		});
	return;
}
