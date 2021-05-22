angular
        .module("app.login")
        .service('LoginService', function ($localStorage, $rootScope, $http) {

            /* Login function */
            this.login = function (request, callback) {
                var data = {};
                $.sha1 = sha1;
                var device_token = '';
                if ($localStorage.pushToken) {
                    device_token = $localStorage.pushToken;
                }
                if (request.login_type == 1) {
                    data = {
                        login_with: request.login_type,
                        phone_number: request.username,
                        password: '',
                        user_type: 2,
                        device_token: device_token
                    }
                } else if (request.login_type == 2) {
                    data = {
                        login_with: request.login_type,
                        email: request.username,
                        password: sha1(request.password),
                        user_type: 2,
                        csrf_test_name: '',
                        device_token: device_token
                    }
                } else if (request.login_type == 3) {
                    data = {
                        login_with: request.login_type,
                        unique_id: request.username,
                        password: sha1(request.password),
                        user_type: 2,
                        device_token: device_token
                    }
                } else if (request.login_type == 4) {
                    data = {
                        login_with: request.login_type,
                        phone_number: request.username,
                        password: sha1(request.password),
                        user_type: 2,
                        device_token: device_token
                    }
                }

                $http({
                    method: 'POST',
                    data: data,
                    url: $rootScope.app.apiUrl + "/login",
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.login1 = function (request, callback) {
                var data = {};
                $.sha1 = sha1;
                if (request.login_type == 1) {
                    data = {
                        login_with: request.login_type,
                        phone_number: request.username,
                        password: '',
                        user_type: 2
                    }
                } else if (request.login_type == 2) {
                    data = {
                        login_with: request.login_type,
                        email: request.username,
                        password: sha1(request.password),
                        user_type: 2,
                        csrf_test_name: '6055b0deb431e1160c7fa2533d0fa683'
                    }
                } else if (request.login_type == 3) {
                    data = {
                        login_with: request.login_type,
                        unique_id: request.username,
                        password: sha1(request.password),
                        user_type: 2
                    }
                } else if (request.login_type == 4) {
                    data = {
                        login_with: request.login_type,
                        phone_number: request.username,
                        password: sha1(request.password),
                        user_type: 2
                    }
                }
                $http({
                    method: 'POST',
                    data: data,
                    url: $rootScope.app.apiUrl + "/login",
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* Verify OTP function */
            this.checkOtp = function (request, callback) {
                var device_token = '';
                if ($localStorage.pushToken) {
                    device_token = $localStorage.pushToken;
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/verify_otp',
                    data: {
                        country_code: '+91',
                        phone_number: request.phone_number,
                        otp: request.otp,
                        is_login: 1,
                        device_type: 'web',
                        user_type: 2,
                        device_token: device_token
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function  errorCallback(error) {
                    console.log(error);
                });
            };


            /* Verify OTP function for register time only */
            this.verifyRegisterOtp = function (request, callback) {
                var device_token = '';
                if ($localStorage.pushToken) {
                    device_token = $localStorage.pushToken;
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/register_verify_otp',
                    data: {
                        registered_id: request.temp_user_id,
                        otp: request.otp,
                        device_type: 'web',
                        user_type: 2,
                        device_token: device_token
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function  errorCallback(error) {
                    console.log(error);
                });
            };
            
            /* resendotp function for the register time only  */
            this.registerResendOtp = function (request, callback) {
                var device_token = '';
                if ($localStorage.pushToken) {
                    device_token = $localStorage.pushToken;
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/register_resend_otp',
                    data: {
                        registered_id: request,
                        device_type: 'web',
                        user_type: 2,
                        device_token: device_token
                    }
                }).then(function succCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* Forgot password api code */
            this.forgotPassword = function (username, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/forgot_password',
                    data: {
                        email: username,
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* Register code */
            this.register = function (request, callback) {
                $.sha1 = sha1;
                var device_token = '';
                if ($localStorage.pushToken) {
                    device_token = $localStorage.pushToken;
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/register',
                    data: {
                        email: request.username,
                        country_code: '+91',
                        phone_number: request.phone_number,
                        password: sha1(request.password),
                        is_term_accept : request.terms_condition,
                        user_type: 2,
                        language: 1,
                        device_type: 'web',
                        device_token: device_token
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallBack(error) {
                    console.log(error);
                });
            };

            /* Resend OTP code */
            this.resendOtp = function (phone_number, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/resend_otp',
                    data: {
                        country_code: '+91',
                        phone_number: phone_number,
                        device_type: 'web',
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            
			/* Update user profile code */
            this.updateUserDetail = function (userdata, callback) {
                if (userdata.gender == 1) {
                    userdata.gender = 'male';
                } else if (userdata.gender == 2) {
                    userdata.gender = 'female';
                } else if (userdata.gender == 3) {
                    userdata.gender = 'undisclosed';
                }
                var user_address = '';
                if(userdata.address != undefined && angular.isObject(userdata.address) && userdata.address.getPlace() != undefined) {
                    user_address = userdata.address.getPlace().formatted_address;
                } else {
                    user_address = (userdata.address != undefined) ? userdata.address : '';
                }
                var formData = new FormData();
                formData.append("user_id", 		userdata.user_id);
                formData.append("user_type", 	2);
                formData.append("access_token", $localStorage.currentUser.access_token);
                formData.append("first_name", 	userdata.first_name);
                formData.append("last_name", 	userdata.last_name);
                formData.append("email", 		userdata.email);
                formData.append("gender", 		userdata.gender);
                formData.append("address", 		user_address);
                formData.append("address1", 	(userdata.address1 != undefined) ? userdata.address1 : '');
                formData.append("city_id", 		(userdata.selected_city != undefined && userdata.selected_city.city_id != undefined) ? userdata.selected_city.city_id : '');
                formData.append("state_id", 	(userdata.selected_state != undefined && userdata.selected_state.state_id != undefined) ? userdata.selected_state.state_id : '');
                formData.append("country_id", 	(userdata.selected_country != undefined && userdata.selected_country.country_id != undefined) ? userdata.selected_country.country_id : '');
                formData.append("pincode", 		(userdata.zipcode != undefined) ? userdata.zipcode : '');
                formData.append("latitude", 	(userdata.lat!= undefined) ? userdata.lat : '');
                formData.append("longitude", 	(userdata.lng!= undefined) ? userdata.lng : '');
                formData.append("language", 	(userdata.language_string != undefined) ? userdata.language_string : '');
                formData.append("year_of_exp",  (userdata.real_year_of_exp != undefined) ? userdata.real_year_of_exp : '');
                formData.append("speciality", 	(userdata.specialitity_string != undefined) ? userdata.specialitity_string : '');
                formData.append("locality", 	(userdata.locality != undefined) ? userdata.locality : '');

                /* images */
                /* edu object */
                for (var i = 0; i < userdata.edu_object.length; i++) {
					if(userdata.edu_object[i] != undefined && userdata.edu_object[i].img_file != undefined)
						formData.append("education_images[" + i + "]", userdata.edu_object[i].img_file);
					if(userdata.edu_object[i] != undefined && userdata.edu_object[i].edu_year != undefined && userdata.edu_object[i].edu_year != '') {
					   userdata.edu_object[i].edu_year_temp = userdata.edu_object[i].edu_year.getFullYear();
                    } else {
                        userdata.edu_object[i].edu_year_temp = '';
                    }
                }
                /* registration object */
                for (var i = 0; i < userdata.registration_obj.length; i++) {
					if(userdata.registration_obj[i] != undefined && userdata.registration_obj[i].img_file != undefined)
						formData.append("registration_images[" + i + "]", userdata.registration_obj[i].img_file);
                    if(userdata.registration_obj[i] != undefined && userdata.registration_obj[i].reg_year_temp != undefined && userdata.registration_obj[i].reg_year_temp != '') {
					   userdata.registration_obj[i].reg_year_temp = userdata.registration_obj[i].reg_year.getFullYear();
                    } else {
                        userdata.registration_obj[i].reg_year_temp = '';
                    }
                }
                formData.append("education_qualification", JSON.stringify(userdata.edu_object));
                formData.append("registration_details", JSON.stringify(userdata.registration_obj));
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_profile_doctor',
                    transformRequest: angular.identity,
                    data: 	 formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
			
            this.updateDeviceToken = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_device_token',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        device_token: request.device_token,
                        device_type: request.device_type
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

        });