/******************************************************
 *  STICKY NOTE
 *
 ******************************************************/

const whiteboardUiCreateStickyNote = document.getElementById('wisePanelWhiteboardUiCreateStickyNote');

const stickyNoteColorButtons = document.querySelectorAll('.wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor');

for(let i = INDEX_FIRST; i < stickyNoteColorButtons.length; i++) {
	stickyNoteColorButtons[i].addEventListener('pointerup',
		function (e){
			let dataName_SelectColor = stickyNoteColorButtons[i].dataset.drawingColor,
				classNaming_color;
			switch (dataName_SelectColor){
				case 'white':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'White';
					break;

				case 'red':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'Red';
					break;

				case 'blue':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'Blue';
					break;

				case 'orange':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'Orange';
					break;

				case 'green':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'Green';
					break;

				case 'yellow':
					classNaming_color = 'stickyNoteBaseContainerColor' + 'Yellow';
					break;

				default:
					return;

			}

			const point = getAppearanceCreatePoint(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState);

			createStickyNoteContainer(classNaming_color, '', point.x, point.y);

			advanceAppearanceOrder(wisePanelWhiteboardViewMainContentArea, appearanceLayoutState, 'A');
			
		}
	, { passive: false });
}