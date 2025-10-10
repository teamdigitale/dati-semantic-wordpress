(($) => {
	$(document).ready(() => {

	    if(jQuery("body").attr("ng-controller")) {
            
            var $scope = angular.element("body").scope();

                $scope.insertPosterShortcodeToImage = function(text) {

                    var id = $scope.component.active.id;            
                    text=text.replace(/\"/ig, "'");
                    $scope.setOptionModel('oxy-pro-media-player_poster_image_urls', text, id);
            
                }

                $scope.insertPosterShortcodeToCustomImage = function(text) {

                    var id = $scope.component.active.id;
                    text=text.replace(/\"/ig, "'");
                    $scope.setOptionModel('oxy-pro-media-player_poster_image_custom_urls', text, id);
            
                }

            }

	});

})(jQuery)