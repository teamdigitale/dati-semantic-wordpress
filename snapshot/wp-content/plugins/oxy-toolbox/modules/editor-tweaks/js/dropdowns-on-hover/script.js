(function($) {
	$('document').ready(function() {
		let addButton = $('.oxygen-add-button', parent.document);

		addButton.on('mouseover', (e) => {
			iframeScope.parentScope.actionTabs['componentBrowser'] = false;
			iframeScope.parentScope.switchActionTab('componentBrowser')
		})
		
	})
})(jQuery);
  