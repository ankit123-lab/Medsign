angular.module("medeasy")
    .service('TeleconsultationService', function ($rootScope, $http, $filter, SMOKE, ALCOHOL, APPOINTMENT_TYPE, bmiFilter, kgToPoundFilter, EncryptDecrypt) {
    	this.getTeleconsultationDate = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_teleconsultation_date",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getTeleconsultationList = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_teleconsultation_list",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getTeleGlobalData = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_tele_global_data",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.createPaymentCreditsOrder = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/create_payment_minutes_order",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.paymentCreditsCapture = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/payment_minutes_capture",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
});