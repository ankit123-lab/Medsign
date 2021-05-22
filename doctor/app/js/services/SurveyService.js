angular.module("medeasy")
        .service('SurveyService', function ($rootScope, $http) {
            /* Survey list function */
            this.getSurveyData = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_survey",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
						doctor_id: request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
			/* Survey Log function */
            this.setSurveyLog = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/save_survey_log",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
						doctor_id: request.doctor_id,
						survey_id: request.survey_id,
						log_type: request.log_type,
						survey_type_id: request.survey_type_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
			/* Survey Content function */
            this.getSurveyContent = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_survey_questions",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
						doctor_id: request.doctor_id,
						survey_id: request.survey_id,
						log_type: request.log_type
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
			/*Save Survey Content function */
            this.saveDoctorSurveyData = function(request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/save_doctor_survey_data",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
						doctor_id: request.doctor_id,
						clinic_id: request.clinic_id,
						survey_id: request.survey_id,
						questions: request.questions,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
		});