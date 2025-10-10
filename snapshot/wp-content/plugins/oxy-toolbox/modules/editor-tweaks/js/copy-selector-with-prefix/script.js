(($) => {
	$(document).ready(() => {
		let panelContainer = $(".oxygen-media-query-and-selector-wrapper", parent.document);
		
		let container = $('<div>');
		container.css({
			'padding-top': '8px',
			'margin-left': '6px',
			position: 'relative'
		})
		let copyButton = $('<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="copy" class="svg-inline--fa fa-copy fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M433.941 65.941l-51.882-51.882A48 48 0 0 0 348.118 0H176c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h224c26.51 0 48-21.49 48-48v-48h80c26.51 0 48-21.49 48-48V99.882a48 48 0 0 0-14.059-33.941zM266 464H54a6 6 0 0 1-6-6V150a6 6 0 0 1 6-6h74v224c0 26.51 21.49 48 48 48h96v42a6 6 0 0 1-6 6zm128-96H182a6 6 0 0 1-6-6V54a6 6 0 0 1 6-6h106v88c0 13.255 10.745 24 24 24h88v202a6 6 0 0 1-6 6zm6-256h-64V48h9.632c1.591 0 3.117.632 4.243 1.757l48.368 48.368a6 6 0 0 1 1.757 4.243V112z"></path></svg>');
		let text = $("<input type='text'/>");
		text.css({
			position: 'absolute',
			opacity: 0
		});

		let tooltip = $("<span style='position: absolute; font-size: 12px; white-space: nowrap; background: #000; padding: 4px 7px; border-radius: 6px; line-height: 1; top: -22px; left: -59px; box-shadow: 0 0 6px 2px rgb(0 0 0 / 50%))'>Selector copied to Clipboard</span>");

		let classesDropdown = $(".oxygen-classes-dropdown", parent.document);
		let statesDropdown = $(".oxygen-states-dropdown", parent.document);

		panelContainer.append(container);
		container.append(copyButton);
		
		classesDropdown.css({right: '-28px'});
		statesDropdown.css({right: '-28px'});

		copyButton.css({
		    width: '14px',
		    opacity: '0.8',
		    cursor: 'pointer'
		})

		copyButton.on('click', () => {
			container.append(text);
			let selector = '';

			if(iframeScope.isEditing('id')) {
				selector = '#'+iframeScope.component.options[iframeScope.component.active.id].selector
			} else if(iframeScope.isEditing('class')) {
				selector = '.'+iframeScope.currentClass;
			} else if(iframeScope.isEditing('custom-selector')) {
				selector = iframeScope.selectorToEdit
			}

			text.val(selector);
			text.get(0).select();
			text.get(0).setSelectionRange(0, 99999); /* For mobile devices */
			parent.document.execCommand("copy");
			text.remove();
			tooltip.css('opacity', 0);
			container.append(tooltip);
			tooltip.animate({opacity: 1}, 500, () => {
				setTimeout(() => {
					tooltip.animate({opacity: 0}, 500, () => {
						tooltip.remove();
					});
				}, 500)
			})
		})
	})
})(jQuery)