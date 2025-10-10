(($) => { // the following code modifies the viewport hight calculations to accomodate the height of the admin bar
	
	$(document).ready(e => { 
		
		let adjustArtificialViewport = iframeScope.parentScope.adjustArtificialViewport;
		iframeScope.parentScope.adjustArtificialViewport = (artificialViewportWidth) => { 
		    adjustArtificialViewport(artificialViewportWidth);
		    let heightOffset = 103;
		    if (iframeScope.parentScope.viewportRullerShown) {
		        heightOffset += 16;
		    }

		    iframeScope.parentScope.artificialViewport.css('height', `calc( ${100/iframeScope.parentScope.viewportScale}vh - ${heightOffset/iframeScope.parentScope.viewportScale}px )`)
		    
		}
		iframeScope.parentScope.adjustArtificialViewport();
	});
})(angular.element)