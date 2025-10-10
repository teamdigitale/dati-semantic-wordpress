(function($) {

	$('a')
	.filter('[href^="http"], [href^="//"]')
	.not('[href*="' + window.location.host + '"]')
	.attr({
		rel: 'noreferrer noopener',
		target: '_blank'
	})

}(jQuery));