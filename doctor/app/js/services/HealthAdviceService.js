angular.module("medeasy")
        .service('HealthAdviceService', function ($rootScope, $http) {
            this.getHealthAdviceGroups = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_health_advice_groups",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };
            this.getHealthAdvice = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_health_advice",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };
            this.addPatientHealthAdvice = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_patient_health_advice",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };
			
		});