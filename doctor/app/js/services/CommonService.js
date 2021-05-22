angular
        .module("medeasy")
        .service('CommonService', function ($rootScope, $http) {
            /* country function */
            this.getCountry = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_country_code",
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
            /* State function */
            this.getState = function (request, flag, callback) {
				if($rootScope.states != undefined && $rootScope.states.length > 0){
					callback({'status':true,'data':angular.copy($rootScope.states)});
				} else {
					var country_id = '';
					if (flag) {
						country_id = request;
					} else {
						country_id = request.country_id;
					}
					$http({
						method: 'POST',
						data: {
							country_id: country_id
						},
						url: $rootScope.app.apiUrl + "/get_states",
					}).then(function successCallback(response) {
						callback(response.data);
					}, function errorCallback(error) {
						//console.log(error);
					});
				}
            };
            /* City function */
            this.getCity = function (request, flag, callback) {
                var state_id = '';
                if (flag) {
                    state_id = request;
                } else {
                    state_id = request.state_id;
                }
				if(state_id != undefined && state_id != ''){
					$http({
						method: 'POST',
						data: {
							state_id: state_id
						},
						url: $rootScope.app.apiUrl + "/get_cities",
					}).then(function successCallback(response) {
						callback(response.data);
					}, function errorCallback(error) {
						//console.log(error);
					});
				}else{
					callback({status:true, data:[]});
				}
            };
            /* Qualification function */
            this.getQualification = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_qualifications",
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
            /* Colleges list function */
            this.getColleges = function (request, callback) {
				if($rootScope.colleges != undefined && $rootScope.colleges.length > 0){
					callback({'status':true,'data':angular.copy($rootScope.colleges)});
				} else {
					$http({
						method: 'POST',
						url: $rootScope.app.apiUrl + "/get_colleges",
					}).then(function successCallback(response) {
						callback(response.data);
					}, function errorCallback(error) {
						//console.log(error);
					});
				}
            };
			
            /* Languages list function */
            this.getLanguages = function (request, callback) {
				if($rootScope.languages != undefined && $rootScope.languages.length > 0){
					callback({'status':true,'data':angular.copy($rootScope.languages)});
				} else {
					$http({
						method: 'POST',
						url: $rootScope.app.apiUrl + "/get_language",
					}).then(function successCallback(response) {
						callback(response.data);
					}, function errorCallback(error) {
						//console.log(error);
					});
				}
            };
            /* Specialisation list function */
            this.getSpecialization = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_specialization",
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
            /* Council list function */
            this.getCouncil = function (request, callback) {

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_councils"

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            /* get support contact */
            this.get_support_contact = function (request, callback) {
                if ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id != undefined && $rootScope.current_doctor.user_id) {
                    var doctor_id = $rootScope.current_doctor.user_id;
                } else {
                    var doctor_id = $rootScope.currentUser.user_id;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_support_contact",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        country: 101
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    $rootScope.app.isLoader = false;
                });
            };

            /* report issue code */
            this.addIssue = function (issue, file_obj, callback) {
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("issue", issue.issue_text);
                formData.append("issue_email", issue.issue_email);

                /* images */
                /* edu object */
                for (var i = 0; i < file_obj.length; i++) {
                    formData.append("attachment[" + i + "]", file_obj[i]);
                }

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_issue",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    $rootScope.app.isLoader = false;
                });
            }

            /* whats new page data from db */
            this.getWhatsDataFromDB = function (data, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_whats_new_data",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        country_id: data
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    $rootScope.app.isLoader = false;
                });
            }
            /* static page code */
            this.getStaticPage = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/static_page",
                    data: {
                        "flag": 1
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
            /* new specialisation code */
            /* Specialisation list function */
            this.getSpecializationNew = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_specialization",
                    data: {
                        parent_id: request.parent_id,
                        flag: request.flag,
                        search_specialization: request.search_specialization
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
			/* Advertisement list function */
            this.getAdvertisementData = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_advertisement",
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
            this.getVideoList = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/get_video',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        page_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
		});