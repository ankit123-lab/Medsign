angular.module("medeasy")
    .service('CommunicationService', function ($rootScope, $http, $filter, SMOKE, ALCOHOL, APPOINTMENT_TYPE, bmiFilter, kgToPoundFilter, EncryptDecrypt) {
    	this.getPatients = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_patients",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getPatientGroups = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_commu_patient_groups",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.addCommunication = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/add_communication",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getCommunicationList = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_communication_list",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getCommunicationDate = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_communication_date",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getSmsTemplate = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_sms_template",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.testTemplateSms = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/test_template_sms",
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
                url: $rootScope.app.apiUrl + "/create_payment_credits_order",
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
                url: $rootScope.app.apiUrl + "/payment_credits_capture",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
        this.getAutoAddedGroupsMember = function (request, callback) {
            $http({
                method: 'POST',
                url: $rootScope.app.apiUrl + "/get_auto_added_groups_member",
                data: request
            }).then(function successCallback(response) {
                callback(response.data);
            }, function errorCallback(error) {
                //console.log(error);
            });
        }
});