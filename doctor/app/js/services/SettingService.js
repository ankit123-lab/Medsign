angular
        .module("medeasy")
        .service('SettingService', function ($rootScope, $http, $filter, $q) {
            this.resetPassword = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/reset_user_password",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        staff_user_id: request.staff_id,
                        password: sha1(request.password),
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            /* get all staff function */
            this.getStaff = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_staff",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        doctor_id: request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* add new staff function */
            this.addStaff = function (request, callback) {

                var gender = '';
                if (request.gender == 1) {
                    gender = 'male';
                } else if (request.gender == 2) {
                    gender = 'female';
                } else if (request.gender == 3) {
                    gender = 'undisclosed';
                }

                var formData = new FormData();
                formData.append("email", (!!request.email) ? request.email : '');
                formData.append("phone_number", request.mob_number);
                formData.append("first_name", request.fname);
                formData.append("last_name", request.lname);
                formData.append("clinic_id", request.clinic_id);
                formData.append("doctor_id", $rootScope.currentUser.user_id);
                formData.append("assign_clinic_id", request.assign_clinic_id);
                formData.append("staff_type", request.staff_type);

                formData.append("gender", gender);

                if (angular.isObject(request.address_name)) {
                    formData.append("address", request.address_name.getPlace().formatted_address);
                } else {
                    formData.append("address", request.address_name);
                }

                formData.append("address1", request.address_name_one);
                formData.append("city_id", request.address_city_id);
                formData.append("state_id", request.address_state_id);
                formData.append("country_id", request.address_country_id);
                formData.append("pincode", request.address_pincode);
                formData.append("latitude", request.address_latitude);
                formData.append("longitude", request.address_longitude);
                formData.append("locality", request.address_locality);
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("tfa", request.tfa);
                /* images */
                /* edu object */
                for (var i = 0; i < request.edu_object.length; i++) {
                    if(request.edu_object[i] != undefined && request.edu_object[i].img_file != undefined)
                        formData.append("education_images[" + i + "]", request.edu_object[i].img_file);
                    request.edu_object[i].doctor_qualification_completion_year = null;
                    if(request.edu_object[i] != undefined && request.edu_object[i].edu_year != undefined && request.edu_object[i].edu_year != '')
                        request.edu_object[i].doctor_qualification_completion_year = request.edu_object[i].edu_year.getFullYear();
                }
                formData.append("edu_qualification", JSON.stringify(request.edu_object));

                for (var i = 0; i < request.registration_obj.length; i++) {
                    if(request.registration_obj[i] != undefined && request.registration_obj[i].img_file != undefined)
                        formData.append("registration_images[" + i + "]", request.registration_obj[i].img_file);
                    
                    request.registration_obj[i].doctor_registration_year = null;
                    if(request.registration_obj[i] != undefined && request.registration_obj[i].reg_year != undefined && request.registration_obj[i].reg_year != '')
                        request.registration_obj[i].doctor_registration_year = request.registration_obj[i].reg_year.getFullYear();
                }
                formData.append("registration_details", JSON.stringify(request.registration_obj));
                
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_staff",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* upload user image function */
            this.uploadUserImage = function (request, callback) {

                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("photo", request.file);
                formData.append("other_user_id", request.staff_id);

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
            /* get staff detail */
            this.getStaffDetail = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_staff_detail",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        staff_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* edit staff code */
            this.editStaff = function (request, callback) {
                var gender = '';
                if (request.gender == 1) {
                    gender = 'male';
                } else if (request.gender == 2) {
                    gender = 'female';
                } else if (request.gender == 3) {
                    gender = 'undisclosed';
                }

                var formData = new FormData();
                formData.append("email", request.email);
                formData.append("phone_number", request.mob_number);
                formData.append("first_name", request.fname);
                formData.append("last_name", request.lname);
                formData.append("other_user_id", request.staff_id);
                formData.append("user_type", 2);
                formData.append("gender", gender);
                formData.append("doctor_id", $rootScope.currentUser.user_id);
                formData.append("assign_clinic_id", request.assign_clinic_id);
                formData.append("assign_old_clinic_id", request.assign_old_clinic_id);

                if (angular.isObject(request.address_name)) {
                    formData.append("address", request.address_name.getPlace().formatted_address);
                } else {
                    formData.append("address", request.address_name);
                }

                formData.append("address1", request.address_name_one);
                formData.append("city_id", request.address_city_id);
                formData.append("state_id", request.address_state_id);
                formData.append("country_id", request.address_country_id);
                formData.append("pincode", request.address_pincode);
                formData.append("latitude", request.address_latitude);
                formData.append("longitude", request.address_longitude);
                formData.append("locality", request.address_locality);
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append('tfa', request.tfa);
                formData.append('staff_type', request.staff_type);
                /* images */
                /* edu object */
                for (var i = 0; i < request.edu_object.length; i++) {
                    if(request.edu_object[i] != undefined && request.edu_object[i].img_file != undefined)
                        formData.append("education_images[" + i + "]", request.edu_object[i].img_file);

                    request.edu_object[i].doctor_qualification_completion_year = null;
                    if(request.edu_object[i] != undefined && request.edu_object[i].edu_year != undefined && request.edu_object[i].edu_year != '')
                        request.edu_object[i].doctor_qualification_completion_year = request.edu_object[i].edu_year.getFullYear();
                }
                formData.append("edu_qualification", JSON.stringify(request.edu_object));

                for (var i = 0; i < request.registration_obj.length; i++) {
                    if(request.registration_obj[i] != undefined && request.registration_obj[i].img_file != undefined)
                        formData.append("registration_images[" + i + "]", request.registration_obj[i].img_file);
                    
                    request.registration_obj[i].doctor_registration_year = null;
                    if(request.registration_obj[i] != undefined && request.registration_obj[i].reg_year != undefined && request.registration_obj[i].reg_year != '')
                        request.registration_obj[i].doctor_registration_year = request.registration_obj[i].reg_year.getFullYear();
                }
                formData.append("registration_details", JSON.stringify(request.registration_obj));
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_staff",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* get doctor availibility for setting module */
            this.getAvailibilitySetting = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_avialability",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        clinic_id_arr: request.clinic_id_arr,
                        doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* availibility status change code */
            this.changeStatusAvailibility = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/set_doctor_avialability_status",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        appointment_type: request.doctor_availability_appointment_type,
                        status: request.doctor_availability_status
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* Update Booking Status */
            this.updateBookingStatus = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_booking_status",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        setting_data: request.setting_data
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* set availibility code */
            this.setAvailibility = function (request, callback) {

                var data = {
                    user_id: $rootScope.currentUser.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    clinic_id: request.clinic_id,
                    doctor_id: $rootScope.currentUser.user_id,
                    appointment_type: request.doctor_availability_appointment_type,
                };
                var set_availibility = [];
                angular
                        .forEach(request, function (value, key) {
                            angular
                                    .forEach(value.days, function (innerValue, innerKey) {
                                        if (innerValue == true) {
                                            var temp = {
                                                session_1_start_time: value.doctor_availability_session_1_start_time,
                                                session_1_end_time: value.doctor_availability_session_1_end_time,
                                                session_2_start_time: value.doctor_availability_session_2_start_time,
                                                session_2_end_time: value.doctor_availability_session_2_end_time,
                                                day: innerKey
                                            };
                                            set_availibility.push(temp);
                                        }
                                    });
                        });
                data.set_availability = JSON.stringify(set_availibility);

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/set_doctor_avialability",
                    data: data
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* set availibility code */
            this.setClinicAvailibility = function (request, callback) {

                var data = {
                    user_id: $rootScope.currentUser.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    clinic_id: request.clinic_id,
                    doctor_id: $rootScope.currentUser.doctor_id,
                };
                var set_availibility = [];
                angular
                        .forEach(request.availibility, function (value, key) {
                            angular
                                    .forEach(value.days, function (innerValue, innerKey) {
                                        if (innerValue == true) {
                                            var temp = {
                                                session_1_start_time: value.clinic_availability_session_1_start_time,
                                                session_1_end_time: value.clinic_availability_session_1_end_time,
                                                session_2_start_time: value.clinic_availability_session_2_start_time,
                                                session_2_end_time: value.clinic_availability_session_2_end_time,
                                                day: innerKey
                                            };
                                            set_availibility.push(temp);
                                        }
                                    });
                        });
                data.set_availability = JSON.stringify(set_availibility);

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/set_clinic_availability",
                    data: data
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* get languages*/
            this.getDBLanguages = function (request, callback) {

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_language",
                    data: {
                        device_type: 'web'
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* get alert setting */
            this.getAlertSetting = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        setting_type: 3
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* set alert setting */
            this.setAlertSetting = function (request, callback) {


                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/set_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        language_id: 1,
                        setting_data_type: 1,
                        setting_type: 3,
                        data: JSON.stringify(request.data),
                        patient_data: (request.patient_data != undefined) ? JSON.stringify(request.patient_data) : '',

                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            /* change clinic duration */
            this.changeDuration = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/update_clinic_duration",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        clinic_id_arr: request.clinic_id_arr,
                        doctor_id: $rootScope.currentUser.user_id,
                        duration: request.duration,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            /* add tax into db */
            this.addNewTax = function (request, callback) {

                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_tax",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        tax_data: JSON.stringify(request),
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.getPaymentModeMaster = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_payment_mode_master",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getSocialMediaMaster = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_social_media_master",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getTelePaymentMode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_tele_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.add_tele_payment_mode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_tele_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                        doctor_payment_mode_id: request.doctor_payment_mode_id,
                        master_id: request.master_id,
                        upi_link: request.upi_link,
                        bank_name: (request.bank_name != undefined) ? request.bank_name : '',
                        bank_holder_name: (request.bank_holder_name != undefined) ? request.bank_holder_name : '',
                        ifsc_code: (request.ifsc_code != undefined) ? request.ifsc_code : '',
                        account_no: (request.account_no != undefined) ? request.account_no : '',
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.deleteTelePaymentMode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_tele_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        doctor_payment_mode_id: request.doctor_payment_mode_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* get tax from db */
            this.getTax = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_tax",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* edit tax */
            this.editTaxCode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_tax",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        tax_id: request.tax_id,
                        tax_name: request.tax_name,
                        tax_value: request.tax_value

                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.deleteStaffUser = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_staff_user",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        staff_user_id: request.staff_user_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.changeStatusStaffUser = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/change_status_staff_user",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        staff_user_id: request.staff_user_id,
                        status: request.status
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* deleteTax tax */
            this.deleteTax = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_tax",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        tax_id: request.tax_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* get payment modes*/
            this.getModes = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* get payment types*/
            this.getPaymentTypes = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_payment_type",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }


            /* add new mode */
            this.addNewMode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        payment_mode_data: JSON.stringify(request)
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* edit mode */
            this.editMode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        payment_mode_name: request.name,
                        payment_type: request.payment_type,
                        payment_vendor_fee: request.fee,
                        payment_mode_id: request.id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* deleteMode mode */
            this.deleteMode = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_payment_mode",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        payment_mode_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* addFeeService*/
            this.addFeeService = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_pricing",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        access_token: $rootScope.currentUser.access_token,
                        tax_id: request.tax_ids,
                        pricing_name: request.service_name,
                        pricing_cost: request.basic_cost,
                        pricing_instruction: request.instruction,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* addFeeService*/
            this.getFeesListDB = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_pricing",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        search: request.search
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* deleteFee*/
            this.deleteFee = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_pricing",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        pricing_id: request,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* editFee*/
            this.editFee = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_pricing",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        access_token: $rootScope.currentUser.access_token,
                        pricing_id: request.id,
                        tax_id: request.tax_ids,
                        pricing_name: request.service_name,
                        pricing_cost: request.basic_cost,
                        pricing_instruction: request.instruction,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* getClinicalNotesFromDB*/
            this.getClinicalNotesFromDB = function (request, flag, callback) {
                if (!flag) { flag = 2; }
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_clinical_notes",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search,
                        clinical_notes_type: request.clinical_notes_type,
                        flag: flag
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* deleteCatelogNote*/
            this.deleteCatelogNote = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_clinical_notes",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinical_notes_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* addClinicalNote*/
            this.addClinicalNote = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_clinical_notes",
                    async: false,
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinical_notes_title: request.title,
                        clinical_notes_type: request.clinical_notes_type,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            /* editNote*/
            this.editNote = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_clinical_notes",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinical_notes_title: request.edit_text_name,
                        clinical_notes_type: request.clinical_notes_type,
                        clinical_notes_id: request.id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* editNote*/
            this.searchSimilar = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_drugs",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        brand_name: request.brand_name,
                        drug_generic_id: request.drug_generic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            // var canceler = $q.defer();
            // var resolved = false;
            // var cancel = function() {
            //     canceler.resolve("http call aborted");
            // };
            this.searchSimilarGeneric = function (request, callback) {
                //  if (resolved) {
                //     cancel();
                // }
                // canceler = $q.defer();
                // resolved = true;
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/search_generic",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        generic_name: request
                    },
                    //timeout: canceler.promise
                }).then(function successCallback(response) {
                    resolved = false;
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            getDrugDetail
            this.getDrugDetail = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_drug_detail",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        drug_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

//            getGeneric
            this.getGeneric = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_drug_generic",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            getGeneric
            this.getBrandType = function (request, callback) {
                var drug_unit_is_display = '';
                if(request != undefined && request.drug_unit_is_display != undefined && request.drug_unit_is_display != '')
                    drug_unit_is_display = request.drug_unit_is_display;
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_drug_brand_type",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        drug_unit_is_display: drug_unit_is_display
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            getBrandFreq
            this.getBrandFreq = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_drug_frequency",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            getBrandList
            this.getBrandList = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_doctor_drug",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search,
                        page: request.page,
                        per_page: request.per_page
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            deleteBrand
            this.deleteBrand = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_drug",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        drug_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
//            addNewBrand
            this.addNewBrand = function (request, callback) {
				var final_request = [];
                angular
                        .forEach(request, function (value, key) {
                            var drug_drug_unit_value = value.drug_drug_unit_value;
                            if(value.drug_unit_name == 'Tablets' && value.defaultFreqOpen)
                                drug_drug_unit_value = '';
                            var temp_obj =
                                    {
                                        "drug_name": value.brand_name,
										"drug_unit_medicine_type": value.drug_unit_medicine_type,
                                        "drug_strength": value.drug_strength,
                                        "drug_generic_id": (value.drug_drug_generic_id!=undefined && value.drug_drug_generic_id!='') ? value.drug_drug_generic_id.join() : '',
                                        "drug_frequency_id": value.drug_frequency_id,
                                        "default1": (value.default1 != undefined && value.default1 != '') ? value.default1 : '0',
                                        "default2": (value.default2 != undefined && value.default2 != '') ? value.default2 : '0',
                                        "default3": (value.default3 != undefined && value.default3 != '') ? value.default3 : '0',
                                        "drug_unit_id": value.drug_unit_id,
                                        "drug_unit_value": drug_drug_unit_value,
                                        "drug_instruction": value.drug_instruction,
                                        "drug_intake": value.drug_intake,
                                        "drug_duration": value.drug_duration,
                                        "drug_duration_value": value.drug_duration_value
                                    };
                            final_request.push(temp_obj);
                        });

					$http({
						method: 'post',
						url: $rootScope.app.apiUrl + "/add_drug_by_doctor",
						data: {
							device_type: 'web',
							user_id: $rootScope.currentUser.user_id,
							doctor_id: $rootScope.currentUser.doctor_id,
							access_token: $rootScope.currentUser.access_token,
							drug_data: JSON.stringify(final_request)
						}
					}).then(function successCallback(response) {
						callback(response.data);
					}, function errorCallback(error) {
						console.log(error);
					});
            }

            this.getTemplate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_template",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: (request.search != undefined) ? request.search : '',
                        page: request.page,
                        per_page: request.per_page
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            };


            this.deleteTemplate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_template",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        template_id: request,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.addNewTemplate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_template",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getLabTestFromDB = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_lab_test",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.editTemplate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_template",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getTemplateDetail = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_template_detail",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        template_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* get share module */
            this.getShareSetting = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        setting_type: request.setting_type,
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* set share data */
            this.saveShareSetting = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/set_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        setting_data_type: request.setting_data_type,
                        setting_type: request.setting_type,
                        data: request.data,
                        rx_setting: (request.rx_setting != undefined) ? request.rx_setting : '',
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* set share data */
            this.saveStaffSetting = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/set_staff_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        setting_data_type: request.setting_data_type,
                        setting_type: request.setting_type,
                        data: request.data,
                        staff_id: request.staff_id,
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* get default kco (dieases) from db */
            this.getKCOTestFromDB = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_diseases",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* get default doctor listing from db */
            this.getDoctorListing = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/search_web_doctor",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* add fav*/
            this.addToFav = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/fav_doctor",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        fav_user_id: request.id,
                        is_fav: request.is_fav
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /* fav lising*/
            this.getFavListingDB = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_fav_doctor_listing",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getSetting = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_setting",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        setting_type: request.setting_type,
                        clinic_id: request.clinic_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getSidebarMenu = function (request, callback, errorFunction) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_sidebar_menu",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };

            /* Global Setting */
            this.getTermsCondition = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/static_page",
                    data: {
                        /* user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token, */
                        flag: 1,
                    }
                }).then(function successCallback(response)
                {
                    callback(response.data);
                }, function errorCallback(error) {
                    //errorFunction(error);
                });
            };

            /* change hour format */
            this.changeHourFormat = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/update_hour_format",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.getDoctorSetting = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_doctor_setting",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        setting_type: request.setting_type,
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            /* getClinicalNotesFromDB*/
            this.getInstructions = function (request, callback) {
                
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_instructions",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search,
                        type: request.type,
                        page: request.page,
                        per_page: request.per_page,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.getInvestigations = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_investigations",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: request.search,
                        page: request.page,
                        per_page: request.per_page
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* delete Diet Instructions*/
            this.deleteDietInstruction = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_diet_instructions",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        diet_instruction_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.deleteInvestigation = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_investigation",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        health_analytics_test_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            /* add Diet Instructions*/
            this.addDietInstructions = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_diet_instructions",
                    async: false,
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        diet_instruction: request.title,
                        translate_data: (request.translate_data != undefined) ? request.translate_data : '',
                        type: request.type,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            this.addEditInvestigation = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_edit_investigation",
                    async: false,
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        health_analytics_test_name: request.health_analytics_test_name,
                        health_analytics_test_id: request.health_analytics_test_id,
                        health_analytics_test_doctor_id: request.health_analytics_test_doctor_id,
                        instruction: request.instruction
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            this.getInvestigationInstructions = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_investigation_instructions",
                    async: false,
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        health_analytics_test_id: request,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            /* edit Diet Instruction*/
            this.editDietInstruction = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/edit_diet_instructions",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        diet_instruction: request.edit_text_name,
                        translate_data: (request.translate_data != undefined) ? request.translate_data : '',
                        diet_instruction_id: request.id,
                        type: request.type,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            this.getTranslate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_translate",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        note_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            this.getPatientGroups = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_patient_groups",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.deletePatientGroup = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_patient_group",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        patient_group_id: request.patient_group_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.searchPatientGroup = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/search_patient_group",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getPatientDiseases = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_disease_by_patient",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.add_patient_group = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/add_patient_group",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.getPatientGroupMembers = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_patient_group_members",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.getShareLink = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_share_link",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }

            this.createRegLink = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/create_reg_link",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                        registration_share_id: request.registration_share_id,
                        clinic_id: (request.clinic_id != undefined) ? request.clinic_id : '',
                        expiry_date: (request.link_expiry_date != undefined) ? request.link_expiry_date : '',
                        social_media_id: (request.social_media_id != undefined) ? request.social_media_id : '',
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            this.deleteShareLink = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/delete_share_link",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        registration_share_id: request.registration_share_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.shareQrCodeLink = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/share_qrcode_link",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.currentUser.user_id,
                        encrp_id: request.encrp_id,
                        mobile_no: request.mobile_no,
                        email: request.email,
                        patient_name: request.patient_name,
                        share_type: request.share_type,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }
            this.getPrescriptionTemplate = function (request, callback) {
                $http({
                    method: 'post',
                    url: $rootScope.app.apiUrl + "/get_prescription_template",
                    data: {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.doctor_id,
                        clinic_id: request.clinic_id,
                        template_type: request.template_type,
                        access_token: $rootScope.currentUser.access_token
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                }
                );
            }

            this.saveRxPrintSetup = function (request, callback, errorFunction) {
                var formData = new FormData();
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("doctor_id", $rootScope.current_doctor.user_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("data", JSON.stringify(request.data));
                formData.append("share_setting_data", JSON.stringify(request.share_setting_data));
                if(request.upload_signature_img != '')
                    formData.append("upload_signature_img", request.upload_signature_img);
                else if(request.signature_img != '')
                    formData.append("signature_img", request.signature_img);
                if(request.logo_img != '')
                    formData.append("logo_img", request.logo_img);
                if(request.watermark_img != '')
                    formData.append("watermark_img", request.watermark_img);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/set_prescription_print_setting",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

        });
