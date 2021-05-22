angular
        .module("medeasy")
        .service('UserService', function ($localStorage, AuthService, $rootScope, $http) {

            /* upload user image function */
            this.uploadUserImage = function (request, callback) {
				if(request == undefined || request.profile_file == undefined || request.profile_file == ''){
					callback({status:true});
					return true;
				}
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("photo", request.profile_file);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/upload_image",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* upload user image function */
            this.uploadSignImage = function (request, callback) {
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("sign_photo", request.imageSignSrc);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/upload_sign_image",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* get user detail */
            this.getDoctorDetail = function (user_id, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/doctor_detail",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* get user detail */
            this.getDoctorWholeDetail = function (user_id, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_whole_details",
                    data: {
                        user_id: user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* get edu detail */
            this.getDoctorEduDetail = function (user_id, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_edu_details",
                    data: {
                        user_id: user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get registration detail */
            this.getDoctorRegDetail = function (user_id, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_reg_details",
                    data: {
                        user_id: user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* update user's personal basic detail */
            this.updateUserPersonalDetail = function (userdata, callback) {
                var gender = '';
                if (userdata.gender == 1) {
                    gender = 'male';
                } else if (userdata.gender == 2) {
                    gender = 'female';
                } else if (userdata.gender == 3) {
                    gender = 'undisclosed';
                }
                var formData = new FormData();
                formData.append("is_update_profile",true);
                formData.append("user_id", 		$rootScope.currentUser.user_id);
                formData.append("user_type", 	2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("first_name", 	userdata.user_first_name);
                formData.append("last_name", 	userdata.user_last_name);
                formData.append("email", 		userdata.user_email);
                formData.append("gender", 		gender);
                if (userdata.address_name != undefined && angular.isObject(userdata.address_name)) {
                    formData.append("address",(userdata.address_name.getPlace() != undefined) ? userdata.address_name.getPlace().formatted_address : '');
                    $localStorage.currentUser.address_name = (userdata.address_name.getPlace() != undefined) ? userdata.address_name.getPlace().formatted_address : '';
                } else {
					userdata.address_name = (userdata.address_name!=undefined) ? userdata.address_name : '';
                    formData.append("address", userdata.address_name);
                    $localStorage.currentUser.address_name = userdata.address_name;
                }
                formData.append("address1",     (userdata.address_name_one != undefined) ? userdata.address_name_one : '');
                formData.append("city_id",      (userdata.address_city_id != undefined) ? userdata.address_city_id : '');
                formData.append("state_id",     (userdata.address_state_id != undefined) ? userdata.address_state_id : '');
                formData.append("country_id",   (userdata.address_country_id != undefined) ? userdata.address_country_id : '');
				
				formData.append("city_id_text",      (userdata.address_city_id_text != undefined) ? userdata.address_city_id_text : '');
				formData.append("state_id_text",     (userdata.address_state_id_text != undefined) ? userdata.address_state_id_text : '');
				formData.append("country_id_text",   (userdata.address_country_id_text != undefined) ? userdata.address_country_id_text : '');

                formData.append("pincode", 	    (userdata.address_pincode != undefined) ? userdata.address_pincode : '');
                formData.append("latitude",     (userdata.address_latitude != undefined) ? userdata.address_latitude : '');
                formData.append("longitude",    (userdata.address_longitude != undefined) ? userdata.address_longitude : '');
                formData.append("language",     (userdata.language_string != undefined) ? userdata.language_string : '');
                formData.append("phone_number", (userdata.user_phone_number != undefined) ? userdata.user_phone_number : '');
                formData.append("locality",     (userdata.address_locality != undefined) ? userdata.address_locality : '');
                if (userdata.doctor_detail_year_of_experience!= undefined && userdata.doctor_detail_year_of_experience) {
                    var month 	= userdata.doctor_detail_year_of_experience.getMonth() + 1;
                    var day 	= userdata.doctor_detail_year_of_experience.getDate();
                    var year 	= userdata.doctor_detail_year_of_experience.getFullYear();
                    userdata.doctor_detail_year_of_experience = year + "-" + ('0' + month).slice('-2clinicOutsideImageSrc') + "-" + ('0' + day).slice('-2');
                    formData.append("year_of_exp", userdata.doctor_detail_year_of_experience);
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_profile_doctor',
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* update edu details of doctor */
            this.updateUserEduDetail = function (userdata, callback) {

                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("speciality", userdata.specialitity_string);

                /* images */
                /* edu object */
                for (var i = 0; i < userdata.edu_object.length; i++) {
                    if(userdata.edu_object[i].edu_year != undefined && userdata.edu_object[i].edu_year != '') {
                         var month = userdata.edu_object[i].edu_year.getMonth() + 1;
                         var day   = userdata.edu_object[i].edu_year.getDate();
                         var year  = userdata.edu_object[i].edu_year.getFullYear();
                        userdata.edu_object[i].doctor_qualification_completion_year = year + "/" + ('0' + month).slice('-2') + "/" + ('0' + day).slice('-2');
                    } else {
                        userdata.edu_object[i].doctor_qualification_completion_year = '';
                    }
					if(userdata.edu_object[i] != undefined && userdata.edu_object[i].img_file != undefined)
						formData.append("education_images[" + i + "]", userdata.edu_object[i].img_file);
                }
                formData.append("education_qualification", JSON.stringify(userdata.edu_object));
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_doctor_other_details',
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* update registration details of doctor */
            this.updateUserRegDetail = function (userdata, callback) {
				var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
				
                /* images */
                /* registration object */
                for (var i = 0; i < userdata.registration_obj.length; i++) {
					if(userdata.registration_obj[i] != undefined) {
                        if(userdata.registration_obj[i].reg_year != undefined && userdata.registration_obj[i].reg_year != '') {
    						var month = userdata.registration_obj[i].reg_year.getMonth() + 1;
    						var day = userdata.registration_obj[i].reg_year.getDate();
    						var year = userdata.registration_obj[i].reg_year.getFullYear();
    						userdata.registration_obj[i].doctor_registration_year = year + "/" + ('0' + month).slice('-2') + "/" + ('0' + day).slice('-2');
                        } else {
                            userdata.registration_obj[i].doctor_registration_year = '';
                        }
						if(userdata.registration_obj[i] != undefined && userdata.registration_obj[i].img_file != undefined && userdata.registration_obj[i].img_file!= ''){
							formData.append("registration_images[" + i + "]", userdata.registration_obj[i].img_file);
						}
					}else{
						userdata.registration_obj.splice(i, 1);
					}
                }
                formData.append("registration_details", JSON.stringify(userdata.registration_obj));
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_doctor_other_reg_details',
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* update award details of doctor */
            this.updateDoctorAwardDetail = function (userdata, callback) {
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                /* images */
                /* registration object */
                for (var i = 0; i < userdata.award_obj.length; i++) {
					if(userdata.award_obj[i] != undefined && userdata.award_obj[i].award_year != undefined){
						var month = userdata.award_obj[i].award_year.getMonth() + 1;
						var day   = userdata.award_obj[i].award_year.getDate();
						var year  = userdata.award_obj[i].award_year.getFullYear();
						userdata.award_obj[i].doctor_award_year = year + "/" + ('0' + month).slice('-2') + "/" + ('0' + day).slice('-2');					
							if(userdata.award_obj[i] != undefined && userdata.award_obj[i].img_file != undefined && userdata.award_obj[i].img_file!=''){
								formData.append("award_images[" + i + "]", userdata.award_obj[i].img_file);
							}
					}else{
						userdata.award_obj.splice(i, 1);
					}
                }
                formData.append("award_details", JSON.stringify(userdata.award_obj));
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/update_doctor_other_award_details',
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.getDoctorList = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/get_doctor_list',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        clinic_id: request.clinic_id
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            this.getDoctorAwardDetails = function (user_id, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/get_doctor_award_details',
                    data: {
                        user_id: user_id,
                        access_token: AuthService.currentUser().access_token
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.getProfilePercentage = function (user_id, callback) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/get_profile_per',
                    data: {
                        user_id: user_id,
                        access_token: AuthService.currentUser().access_token
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.checkOtpForUpdateProfile = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/verify_update_number_otp',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        other_user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        country_code: '+91',
                        phone_number: request.number,
                        otp: request.otp
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.checkOtpForUpdateClinicNumber = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/verify_otp_for_clinic',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        country_code: '+91',
                        phone_number: request.number,
                        otp: request.otp,
                        clinic_id: request.clinic_id,
                        device_token: ''
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };

            this.resendOTPForClinic = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/resend_otp_for_clinic',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        country_code: '+91',
                        phone_number: request.number,
                        clinic_id: request.clinic_id
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.resendEmailDoctor = function (request, callback, errorFunction) {
                var request_data = {
                    user_id: $rootScope.currentUser.user_id,
                    access_token: AuthService.currentUser().access_token,
                    user_type: 2,
                    email: request.email
                }
                if (!!request.type && request.type == 3) {
                    request_data['other_user_id'] = $rootScope.currentUser.user_id;
                    request_data['is_email_updated'] = 1;
                }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/resend_email_verify_link',
                    data: request_data

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.resendOTPDoctor = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/resend_otp',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        phone_number: request.phone_number,
                        country_code: '+91',
                        is_number_updated: 1,
                        other_user_id: $rootScope.currentUser.user_id
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.checkOtpForResend = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/verify_otp',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        phone_number: request.phone_number,
                        country_code: '+91',
                        otp: request.otp,
                        device_token: ''
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };

            this.checkOtpNumberUpdated = function (request, callback, errorFunction) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + '/verify_update_number_otp',
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        other_user_id: $rootScope.currentUser.user_id,
                        access_token: AuthService.currentUser().access_token,
                        user_type: 2,
                        phone_number: request.phone_number,
                        country_code: '+91',
                        otp: request.otp,
                        device_token: ''
                    }

                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };

            this.resendEmailLink = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/resend_email_link_for_clinic",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        //doctor_id: $rootScope.currentUser.user_id,
						doctor_id: ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id != undefined) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                        clinic_id: request.clinic_id,
                        email: request.email,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.updateTermCondition = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_terms_condtion_flag",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id != undefined) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                        is_term_accept: request.is_term_accept,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
        });
