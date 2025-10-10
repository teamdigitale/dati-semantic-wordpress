(($) => {
	$(document).ready(() => {

	    let calculateDepth = iframeScope.calculateDepth;

	    iframeScope.calculateDepth = function(component, parent) {
	        let result = calculateDepth(component, parent);

	        if(result === false && component['name']) {

	            if(iframeScope.componentsTemplates.hasOwnProperty(component['name'])) {
	                if(iframeScope.componentsTemplates[component['name']]['nestable'] && iframeScope.componentsTemplates[component['name']]['nestable'] == 'true') {
	                    return parseInt(parent.depth)+1;
	                }
	            }

	        }
	        return result;
	    }
	});

})(jQuery)