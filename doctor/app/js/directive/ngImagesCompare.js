angular.module("app.dashboard").directive('ngImagesCompare', function($window, $timeout) {
  return {
	restrict: 'AE',
    link: function(scope, element, attrs) {
		
		scope.$on("$destroy", function(){
			angular.element(element).off();
			angular.element(element[0]).off();
			//angular.element(element).unbind();
			angular.element(element).imagesCompare("destroy");
		});
		
		scope.$on("removePreImgCompObj", function(){
			//angular.element(element).off();
			//angular.element(element[0]).off();
			angular.element(element).unbind();
			angular.element(element).imagesCompare("destroy");
		});
		
		scope.$on("picsDownloaded",function(){
			$timeout(function(){
					//var imagesCompareElement = $(element[0]).imagesCompare(scope.$eval(attrs.ngImagesCompare));
					var imagesCompareElement = angular.element(element).imagesCompare(scope.$eval(attrs.ngImagesCompare));
					var imagesCompare = imagesCompareElement.data('imagesCompare');
					$('.image-compare-btn').on('click', function (event) {
						event.preventDefault();
						if (imagesCompare.getValue() >= 0 && imagesCompare.getValue() < 1) {
							imagesCompare.setValue(1, true);
						} else {
							imagesCompare.setValue(0, true);
						}
					});
					$(window).resize();
			},1000);
		});
		
		/* scope.$watch(function(){return scope.imageCompareObj.img1.path; }, function(obj) {
			if(obj != undefined && obj != ''){
				//angular.element(element[0]).unbind();
				//angular.element(element[0]).imagesCompare("destroy");
				console.log('dist');
				$timeout(function () {
					console.log('loadagain');
					//var imagesCompareElement = $(element[0]).imagesCompare(scope.$eval(attrs.ngImagesCompare));
					var imagesCompareElement = angular.element(element).imagesCompare(scope.$eval(attrs.ngImagesCompare));
					var imagesCompare = imagesCompareElement.data('imagesCompare');
					$('.image-compare-btn').on('click', function (event) {
						event.preventDefault();
						if (imagesCompare.getValue() >= 0 && imagesCompare.getValue() < 1) {
							imagesCompare.setValue(1, true);
						} else {
							imagesCompare.setValue(0, true);
						}
					});
					//return imagesCompareElement;
				});
			}
		}, true); */
    }
  }
});