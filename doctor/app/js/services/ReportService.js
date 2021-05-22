angular.module("medeasy")
        .service('ReportService', function ($rootScope, $http, $filter, SMOKE, ALCOHOL, APPOINTMENT_TYPE, bmiFilter, kgToPoundFilter, EncryptDecrypt) {
            
            this.getMemberSummary = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/member_summary",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getMobSummary = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/mob_summary",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getLostPatient = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/lost_patient",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getPatientProgress = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/patient_progress",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getPatientsCity = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/patients_city",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getDrugGeneric = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/drug_generic",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getDrugs = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_report_drugs",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getKCO = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_kco",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getDiagnoses = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_diagnoses",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getInvoiceSummary = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/invoice_summary",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getCancelAppointment = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/cancel_appointment_list",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            this.getLostPatientDetails = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_lost_patient_details",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

		});