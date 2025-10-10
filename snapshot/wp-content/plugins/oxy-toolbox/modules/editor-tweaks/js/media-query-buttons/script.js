(function($) {
	$('document').ready(function() {

		$( ".oxygen-media-query-dropdown li", parent.document ).each(function( i ) {

			$(this).attr("aria-label", $(this).find('span').text().trim() );
			// $(this).attr("data-balloon-pos", "down");
			$(this).attr("data-balloon-pos", "down-left");
			$(this).find('span').empty();

		});

	})
})(angular.element);
  