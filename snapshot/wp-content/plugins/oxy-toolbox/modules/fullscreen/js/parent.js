let oxy_fullscreen_src = document.querySelector('#ct-artificial-viewport').getAttribute('data-src');
document.querySelector('#ct-artificial-viewport').setAttribute('data-src', '');
(function($) {
	
   	$('document').ready(function() {
   		
		window['oxy_fullscreen'] = window.open(oxy_fullscreen_src);
		window['oxy_fullscreen'].parent = window;

	});

})(jQuery);