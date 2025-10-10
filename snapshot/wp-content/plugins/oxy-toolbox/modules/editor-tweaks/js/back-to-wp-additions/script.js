(function($) {
	$(".oxygen-back-to-wp-menu .oxygen-toolbar-button-dropdown").append(
	  '<a href="' +
	  oxy_toolbox_editor_tweaks_admin_urls.templates +
		'" class="oxygen-toolbar-button-dropdown-option">Templates</a><a href="' +
		oxy_toolbox_editor_tweaks_admin_urls.pages +
		'" class="oxygen-toolbar-button-dropdown-option">Pages</a>'
	);
})(jQuery);
  