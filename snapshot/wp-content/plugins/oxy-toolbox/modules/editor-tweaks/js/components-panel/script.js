(function ($) {
	$('.otb-components-panel').show();

	if(window['oxy_toolbox_editor_tweaks_cpoptions'] !== undefined && oxy_toolbox_editor_tweaks_cpoptions['onclick']) {
		$('.otb-components-panel').addClass('clickable');
		$('.otb-components-panel').on('click', () => {
			$('.otb-components-panel').addClass('active');
		})

		$('.otb-components-panel').on('mouseleave', () => {
			$('.otb-components-panel').removeClass('active');
		})
	}

})(jQuery);