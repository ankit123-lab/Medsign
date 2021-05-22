/*
 * Controller Name: DashboardController
 * Use: This controller is used for doctor user related activity
 */
angular.module("app.profile")
        .controller("UserController", function ($scope, AuthService, $state, UserService, CommonService, $rootScope, ngToast, $location, ClinicService, fileReader, LoginService, $rootScope, $localStorage, $timeout, $filter, SettingService, SweetAlert, $interval, angularLoad, $uibModal) {

			$scope.init = function () {
                $scope.years_of_picker = {
                    date: new Date(),
                    datepickerOptions: {
                        mode: 'year',
                        minMode: 'year',
                        maxMode: 'year',
                        maxDate: new Date()
                    },
                    open: false
                };
                $scope.user_detail = {};
                $scope.other = {
                    qualification: [],
                    specializations: [],
                    councils: [],
                };
                $scope.user = {
                    first_name: '',
                    last_name: '',
                    doctor_photo: '',
                    doctor_phone_number: '',
                    doctor_email: '',
                    doctor_specialisation: '',
                    doctor_qualification_degree: '',
                    doctor_timing_start: '',
                    doctor_timing_end: '',
                    doctor_timing_start2: '',
                    doctor_timing_end2: '',
                    clinic: {
                        clinic_id: '',
                        clinic_name: '',
                        clinic_address: '',
                        clinic_email: '',
                        clinic_phone_number: ''
                    },
                    profile_per: 0,
                };
                $scope.Model = $scope.Model || {profile_per: 0};
                $scope.Model = $scope.Model || {currentImg: ''};
                $scope.Model = $scope.Model || {number: ''};
                $scope.Model = $scope.Model || {clinic_number: ''};

                $scope.doctor = {
                    gender: 1,
                    user_email: $localStorage.currentUser.user_email,
                    user_phone_number:   $localStorage.currentUser.user_phone_number,
						edu_object: 	 [{}],
						specialization:  [{doctor_specialization_specialization_text: ''}],
						registration_obj:[{}],
						award_obj: 		 [{}],
						speciality:		 [{}]
                };
				
                $scope.other = {};
                $scope.other.clinic_service = [{
                        text: ''
                    }];
                $scope.other.country = [];
                CommonService.getCountry('', function (response) {

                    if (response.status == true) {
                        $scope.other.country = response.data;
                        $scope.other.city = [];
                        $scope.other.selected_clinic_country = $scope.other.country[0].country_id;
                        $scope.getState($scope.other.selected_clinic_country);
                    }
                });
                $scope.submitted = false;
                $scope.clinicImageSrc = '';
                $scope.clinicOutsideImageSrc = '';
                $scope.clinicReceptionImageSrc = '';
                $scope.clinicWaitingImageSrc = '';

                $scope.clinicImageSrcEdited = '';
                $scope.clinicOutsideImageSrcEdited = '';
                $scope.clinicReceptionImageSrcEdited = '';
                $scope.clinicWaitingImageSrcEdited = '';
                $scope.clinicAddressImageSrcEdited = '';
                $scope.receptionist = [];
                $scope.show_doctor = true;
                $scope.show_receptionist = false;
            }
            $scope.other = {};
            $scope.other.temp_specializations = [];
            $scope.createOption = function (term) {

                $scope.$apply(function () {
                    $scope.other.temp_specializations_child.push({
                        specialization_title: term
                    });


                });
                $timeout(function () {
                    return $scope.$apply(function () {
                        $scope.doctor.speciality.push($scope.other.temp_specializations_child[$scope.other.temp_specializations_child.length - 1].specialization_title);
                    });
                }, 100);

            }
            $scope.getSpecialization = function () {

                CommonService.getSpecialization('', function (response) {
                    if (response.status == true) {
                        $scope.other.specializations = response.data;
                        $scope.other.temp_specializations = [];
                        angular.forEach(response.data, function (value, key) {
                            this.push({"specialization_title": value.specialization_title});
                        }, $scope.other.temp_specializations);
                    }
                });
            }

            $scope.init();
            CommonService.getQualification('', function (response) {
                if (response.status == true) {
                    $scope.other.qualification = response.data;
                }
            });
            CommonService.getSpecializationNew({
                parent_id: '',
                flag: '',
                search_specialization: ''
            }, function (response) {
                if (response.status == true) {
                    $scope.other.specializations = response.data;
                    $scope.other.temp_specializations = [];
                    angular.forEach(response.data, function (value, key) {
                        this.push({"specialization_id": key, "specialization_title": value.specialization_title});
                    }, $scope.other.temp_specializations);

                }
            });
            CommonService.getSpecializationNew({
                parent_id: '',
                flag: '1',
                search_specialization: ''
            }, function (response) {
                if (response.status == true) {
                    $scope.other.temp_specializations_child = [];
                    angular.forEach(response.data, function (value, key) {
                        this.push({"specialization_id": key, "specialization_title": value.specialization_title});
                    }, $scope.other.temp_specializations_child);

                }
            });

            $scope.getChildSpeciality = function (old_id, parent_id) {

                if (parent_id != undefined) {
                    CommonService.getSpecializationNew({
                        parent_id: parent_id,
                        flag: '',
                        search_specialization: ''
                    }, function (response) {
                        if (response.status == true) {
                            if (!$scope.doctor.speciality) {
                                $scope.doctor.speciality = [];
                            }
                            angular
                                    .forEach(response.data, function (value) {
                                        $scope.doctor.speciality.push(value.specialization_title);
                                    });

                            if (old_id == '') {
                                $scope.addEduObject(3);
                            }

                        }
                    });
                }
            }
            CommonService.getCouncil('', function (response) {
                if (response.status == true) {
                    $scope.other.councils = response.data;
                }
            });
            CommonService.getCountry('', function (response) {
                if (response.status == true) {
                    $scope.other.country = response.data;
                    $scope.other.city = [];
                    $scope.other.selected_clinic_country = $scope.other.country[0].country_id;
                    $scope.getState($scope.other.selected_clinic_country);
                }
            });
            CommonService.getColleges('', function (response) {
                if (response.status == true) {
                    $scope.other.colleges = response.data;
                }
            });
            $scope.getCountry = function () {
                CommonService.getCountry('', function (response) {

                    if (response.status == true) {
                        $scope.other.country = response.data;
                        $scope.other.selected_clinic_country = $scope.other.country[0].country_id;
                        $scope.getState($scope.other.selected_clinic_country);
                        $scope.other.city = [];
                    }
                });
            }
            CommonService.getLanguages('', function (response) {
                if (response.status == true) {
                    $scope.other.languages = response.data;
                }
            });
            $scope.getPercentage = function () {
				UserService
                        .getProfilePercentage($localStorage.currentUser.user_id, function (response) {
                            $scope.Model.profile_per = response;
                            if (response != 100) {
                                $rootScope.app.is_not_valid = true;
                                $rootScope.error_message = $rootScope.incomplete_profile;
                                $rootScope.app.is_profile_complete = 2;
							} else if($rootScope.currentUser != undefined && $rootScope.currentUser.user_status != undefined && $rootScope.currentUser.user_status == 3) {
								$rootScope.app.is_not_valid = true;
                                $rootScope.error_message = $rootScope.account_pending_for_approval;
                                $rootScope.app.is_profile_complete = 1;
							} else if($localStorage.currentUser != undefined && $localStorage.currentUser.user_status != undefined && $localStorage.currentUser.user_status == 3) {
								$rootScope.app.is_not_valid = true;
                                $rootScope.error_message = $rootScope.account_pending_for_approval;
                                $rootScope.app.is_profile_complete = 1;
							} else {
                                $rootScope.app.is_not_valid = false;
                                $rootScope.app.is_profile_complete = 1;
                                $scope.getClinicService();
                            }
                        });
            }
            $scope.getClinicService = function () {
                ClinicService.getDoctorClinicsRole($rootScope.currentUser.user_id, function (response) {
                    if (!response.clinic_data[0]) {
                        $rootScope.app.is_clinic_complete = 2;
                        $rootScope.app.is_not_valid = true;
                        $rootScope.error_message = $rootScope.add_clinic_first;
                        $state.go('app.profile.clinic_view', {}, {reload: true});
                    }
                }, function (error) {
                    if (error.status == 403) {
                        $scope.getClinicService();
                    }
                });
            };
            $scope.logout = function () {
                AuthService.logout();
                $state.go('app.login');
            }
            /* function for determine active class of my profile tab */
            $scope.getClassActive = function (route) {
                return ($location.path().substr(0, route.length) === route) ? 'active' : '';
            }

            /* get user detail function */
            $scope.getDoctorProfile = function (user_id) {
                if (!user_id) {
                    user_id = $rootScope.currentUser.user_id;
                }

                if ($rootScope.app.is_not_valid == true && $rootScope.app.is_profile_complete == 2) {
                    $state.go('app.complete_profile_view.personal');
                }
                $scope.show_doctor = true;
                $scope.show_receptionist = false;
                UserService
                        .getDoctorDetail(user_id, function (response) {

                            if (response.status == true) {
                                var doctor_data = response.doctor_data;

                                $scope.user = {
                                    user_id: user_id,
                                    first_name: doctor_data.doctor_first_name,
                                    last_name: doctor_data.doctor_last_name,
                                    doctor_photo: doctor_data.doctor_photo,
                                    doctor_phone_number: doctor_data.doctor_phone_number,
                                    doctor_email: doctor_data.doctor_email,
                                    doctor_specialisation: doctor_data.doctor_specialisation,
                                    doctor_qualification_degree: doctor_data.doctor_qualification,
                                    doctor_timing_start: doctor_data.doctor_timing_start,
                                    doctor_timing_end: doctor_data.doctor_timing_end,
                                    doctor_timing_start2: doctor_data.doctor_timing_start2,
                                    doctor_timing_end2: doctor_data.doctor_timing_end2

                                };
                                if (response.clinic_data[0] != undefined) {
                                    $scope.user.clinic = {
                                        clinic_id: response.clinic_data[0].clinic_id,
                                        clinic_name: response.clinic_data[0].clinic_name,
                                        clinic_address: response.clinic_data[0].clinic_address,
                                        clinic_address1: response.clinic_data[0].clinic_address1,
                                        clinic_email: response.clinic_data[0].clinic_email,
                                        clinic_phone_number: response.clinic_data[0].clinic_phone_number
                                    };
                                }

                            }
                        });

            };

            /* get logged in user clinic list */
            $scope.getUsersClinicList = function () {
                //get clinic
                ClinicService
                        .getDoctorClinics($rootScope.currentUser.user_id, function (response) {
                            $scope.is_add_clinic = true;
                            if (response.status == true) {
                                $scope.clinic_data = response.clinic_data;
                                $scope.is_add_clinic = response.is_add_clinic;
                                if ($scope.clinic_data.length == 0) {
                                    $rootScope.app.is_not_valid = true;
                                    $rootScope.app.is_clinic_complete = 2;
                                    $rootScope.error_message = $rootScope.add_clinic_first;
								} else if($rootScope.currentUser != undefined && $rootScope.currentUser.user_status != undefined && $rootScope.currentUser.user_status == 3) {
									$rootScope.app.is_not_valid = true;
									$rootScope.error_message = $rootScope.account_pending_for_approval;
                                } else {
                                    $rootScope.app.is_not_valid = false;
                                }
                            }
                        });
            }

            $scope.clinicdatepicker1 = [];
            $scope.clinicdatepicker2 = [];
            $scope.openClinicPicker = function ($event, key, type) {

                $event.preventDefault();
                $event.stopPropagation();
                var round_date = new Date();
                var minutes = round_date.getMinutes();
                var hours = round_date.getHours();
                var round_minutes = (parseInt((minutes + 7.5) / 15) * 15) % 60;
                var round_hours = minutes > 52 ? (hours === 23 ? 0 : ++hours) : hours;
                round_date.setHours(round_hours);
                round_date.setMinutes(round_minutes);

                if (type == 1) {
                    $scope.clinicdatepicker1.length = 0;
                    $scope.clinicdatepicker1[key] = {'opened': true};
                    if (key == 0) {
                        if ($scope.other.clinic_start_time == "" ||
                                $scope.other.clinic_start_time == undefined
                                ) {
                            $scope.other.clinic_start_time = round_date;
                        }
                    }
                    if (key == 1) {
                        if ($scope.other.clinic_end_time == "" ||
                                $scope.other.clinic_end_time == undefined
                                ) {
                            $scope.other.clinic_end_time = round_date;
                        }
                    }
                }

                if (type == 2) {
                    $scope.clinicdatepicker2.length = 0;
                    $scope.clinicdatepicker2[key] = {'opened': true};
                    if (key == 2) {
                        if ($scope.other.clinic_start_time2 == "" ||
                                $scope.other.clinic_start_time2 == undefined
                                ) {
                            $scope.other.clinic_start_time2 = round_date;
                        }
                    }
                    if (key == 3) {
                        if ($scope.other.clinic_end_time2 == "" ||
                                $scope.other.clinic_end_time2 == undefined
                                ) {
                            $scope.other.clinic_end_time2 = round_date;
                        }
                    }

                }
            };

            /* get clinic details for edit */
            $scope.getClinicDetailForEdit = function (clinic_id) {

                ClinicService
                        .getClinicDetail(clinic_id, function (response) {
                            if (response.status == true) {

                                $scope.other = response.data;
                                $scope.other.clinic_id = response.data.clinic_id;
                                $scope.other.clinic_address_id = response.data.address_id;
                                $scope.other.clinic_address = response.data.address_name;
                                $scope.other.clinic_address1 = response.data.address_name_one;
                                $scope.other.clinic_number = response.data.clinic_contact_number;
                                $scope.other.clinic_locality = response.data.address_locality;
                                $scope.other.clinic_zipcode = response.data.address_pincode;
                                $scope.other.clinic_duration = response.data.doctor_clinic_mapping_duration;
                                $scope.other.clinicCharge = response.data.doctor_clinic_mapping_fees;
                                $scope.other.clinicTeleCharge = response.data.doctor_clinic_mapping_tele_fees;

                                $scope.clinicAddressImageSrc = '';
                                $scope.other.clinic_phone_verified = response.data.clinic_phone_verified;


                                if ($scope.other.clinicCharge) {
                                    $scope.other.clinicCharge = Math.round($scope.other.clinicCharge * 100) / 100;
                                }
                                $scope.other.clinic_start_time = response.data.doctor_clinic_doctor_session_1_start_time;
                                $scope.other.clinic_end_time = response.data.doctor_clinic_doctor_session_1_end_time;
                                $scope.other.clinic_start_time2 = response.data.doctor_clinic_doctor_session_2_start_time;
                                $scope.other.clinic_end_time2 = response.data.doctor_clinic_doctor_session_2_end_time;
                                $scope.clinicImageSrc = response.data.clinic_filepath;
                                $scope.other.is_copied = true;

                                var new_date = new Date();
                                new_date.setHours($scope.other.clinic_start_time.slice(0, -6));
                                new_date.setMinutes($scope.other.clinic_start_time.slice(3, -3));
                                new_date.setSeconds(00);
                                $scope.other.clinic_start_time = new_date;

                                var new_date = new Date();
                                new_date.setHours($scope.other.clinic_end_time.slice(0, -6));
                                new_date.setMinutes($scope.other.clinic_end_time.slice(3, -3));
                                new_date.setSeconds(00);
                                $scope.other.clinic_end_time = new_date;


                                $scope.secondsession = false;
                                $scope.other.secondsession = false;
                                if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != '00:00:00') {

                                    $scope.other.secondsession = true;

                                    var new_date = new Date();
                                    new_date.setHours($scope.other.clinic_start_time2.slice(0, -6));
                                    new_date.setMinutes($scope.other.clinic_start_time2.slice(3, -3));
                                    new_date.setSeconds(00);
                                    $scope.other.clinic_start_time2 = new_date;
                                    var new_date = new Date();
                                    new_date.setHours($scope.other.clinic_end_time2.slice(0, -6));
                                    new_date.setMinutes($scope.other.clinic_end_time2.slice(3, -3));
                                    new_date.setSeconds(00);
                                    $scope.other.clinic_end_time2 = new_date;
                                }
                                $scope.other.cliniclat = response.data.address_latitude;
                                $scope.other.cliniclng = response.data.address_longitude;
                                $scope.getCountry();
                                $timeout(function () {
                                    $scope.getState(response.data.address_country_id);
                                }, 1500);
                                $timeout(function () {
                                    $scope.getCity(response.data.address_state_id);
                                }, 2000);

                                $timeout(function () {
                                    $scope.other.selected_clinic_country = response.data.address_country_id;
                                    $scope.other.selected_clinic_state = response.data.address_state_id;
                                    $scope.other.selected_clinic_city = response.data.address_city_id;
                                }, 2000);
                                $scope.clinicOutsideImageSrc = '';
                                $scope.other.clinicOutsideImageSrcId = '';
                                $scope.clinicWaitingImageSrc = '';
                                $scope.other.clinicWaitingImageSrcId = '';
                                $scope.clinicReceptionImageSrc = '';
                                $scope.other.clinicReceptionImageSrcId = '';
                                $scope.clinicAddressImageSrc = '';
                                $scope.other.clinicAddressImageSrcId = '';
                                if (response.data.images.length > 0) {
                                    angular
                                            .forEach(response.data.images, function (value, key) {
                                                if (value.clinic_photo_type == 1) {
                                                    $scope.clinicOutsideImageSrc = value.clinic_photo_filepath;
                                                    $scope.other.clinicOutsideImageSrcId = value.clinic_photo_id;
                                                } else if (value.clinic_photo_type == 2) {
                                                    $scope.clinicWaitingImageSrc = value.clinic_photo_filepath;
                                                    $scope.other.clinicWaitingImageSrcId = value.clinic_photo_id;
                                                } else if (value.clinic_photo_type == 3) {
                                                    $scope.clinicReceptionImageSrc = value.clinic_photo_filepath;
                                                    $scope.other.clinicReceptionImageSrcId = value.clinic_photo_id;
                                                } else if (value.clinic_photo_type == 4) {
                                                    $scope.clinicAddressImageSrc = value.clinic_photo_filepath;
                                                    $scope.other.clinicAddressImageSrcId = value.clinic_photo_id;
                                                }

                                            });

                                }

                                $scope.other.clinic_service = [];
                                var service_array = response.data.clinic_services.split(',');

                                angular
                                        .forEach(service_array, function (value) {
                                            $scope.other.clinic_service.push({text: value});
                                        });

                                if (service_array.length > 0) {
                                    $scope.addClinicService(1);
                                }

                            } else {
                                ngToast.danger(response.message);
                            }
                        });
            };

            //1=clinic 2=doctor
            $rootScope.updateEmailVerification = function (flag) {
                $scope.$apply(function () {
                    if (flag == 2) {
                        $scope.doctor.user_email_verified = 1;
                        $rootScope.currentUser.user_email_verified = 1;
                        var hideElement = angular.element(document.querySelector('.verify_unverify_email'));
                        hideElement.addClass('hide');
                    } else {
                        $scope.other.clinic_phone_verified = 1;
                        var hideElement = angular.element(document.querySelector('.verify_unverify_email'));
                        hideElement.addClass('hide');
                    }
                });
            };


            /* get whole detail of doctor */
            $scope.getDoctorWholeDetail = function () {
                UserService
                        .getDoctorWholeDetail($rootScope.currentUser.user_id, function (response) {
                            if (response.status == true) {
                                if (response.data != '') {
                                    $scope.doctor = response.data;
                                    if ($scope.doctor.address_country_id == '' || $scope.doctor.address_country_id == undefined) {
                                        $scope.doctor.address_country_id = $scope.other.country[0].country_id;
                                    }
                                    $scope.getState($scope.doctor.address_country_id);
                                    $scope.getCity($scope.doctor.address_state_id);
									$scope.doctor.language = $scope.doctor.doctor_detail_language_id.split(',');
                                    $scope.doctor.gender = '';
                                    if ($scope.doctor.user_gender == 'male') {
                                        $scope.doctor.gender = 1;
                                    } else if ($scope.doctor.user_gender == 'female') {
                                        $scope.doctor.gender = 2;
                                    } else {
                                        $scope.doctor.gender = 3;
                                    }
                                    if (!$scope.doctor.user_first_name) {
                                        $rootScope.app.is_not_valid = true;
                                        $rootScope.error_message = $rootScope.incomplete_profile;
                                    } else {
                                        //$rootScope.app.is_not_valid = false;
                                        $rootScope.currentUser.user_first_name = $scope.doctor.user_first_name;
                                    }
                                    if ($scope.doctor.doctor_detail_year_of_experience) {
                                        var temp_date = $scope.doctor.doctor_detail_year_of_experience + 'T00:00:00';
                                        var date = new Date(temp_date);
                                        $scope.doctor.doctor_detail_year_of_experience = date;
                                    }
                                } else {
                                    $scope.doctor.address_country_id = $scope.other.country[0].country_id;
                                    $scope.getState($scope.doctor.address_country_id);
                                }
                            }
                        });
            }
            $scope.login = {terms_condition: 0}
            $scope.acceptTerms = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    var request = {
                        is_term_accept: $scope.login.terms_condition
                    }
                    UserService.updateTermCondition(request, function (response) {
                        if (response.status == true) {
                            $scope.currentUser.doctor_detail_is_term_accepted = 1;
                            $("#term_condition_modal").modal("hide");
                        }
                    });
                }
            }
            $scope.getState = function (country) {
                if (!$scope.other.state || $scope.other.state.length <= 0) {
                    CommonService.getState(country, true, function (response) {

                        if (response.status == true) {
                            $scope.other.state = response.data;
                        } else {
                            ngToast.danger(response.message);
                        }
                    });
                }
            }

            $scope.getCity = function (state_id) {
                CommonService.getCity(state_id, true, function (response) {
                    if (response.status == true) {
                        $scope.other.city = response.data;
                    } else {
                        $scope.other.city = [];
                        ngToast.danger(response.message);
                    }
                });
            }


            $scope.getDoctorStaffList = function () {
                $scope.other.doctor_list = [
                    {
                        "doctor_id": 1,
                        "doctor_name": $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name,
                    }
                ];

                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id
                };
                SettingService.getStaff(request, function (response) {
                    if (response.status) {
                        $scope.other.staff_rec_list = [];
                        $scope.other.staff_assistant_list = [];
                        angular
                                .forEach(response.data, function (value, key) {
                                    if (value.user_role == 2) {
                                        $scope.other.staff_assistant_list.push(value);
                                    } else if (value.user_role == 3) {
                                        $scope.other.staff_rec_list.push(value);
                                    }
                                });
                    }
                });
            }

            /* Change order code */
            $scope.changeGender = function (gender) {
                $scope.doctor.gender = gender;
            }

            /* get user edu details */
            $scope.getEduDetails = function () {

                UserService
                        .getDoctorEduDetail($rootScope.currentUser.user_id, function (response) {
                            if (response.status == true) {
                                $scope.doctor.specialization_dummy = [{}];
                                if (response.edu_data.length > 0) {
                                    $scope.doctor.edu_object = response.edu_data;
                                    angular.forEach($scope.doctor.edu_object, function (value, key) {
                                        if($scope.doctor.edu_object[key].doctor_qualification_completion_year == '0000' || $scope.doctor.edu_object[key].doctor_qualification_completion_year == "") {
                                            $scope.doctor.edu_object[key].edu_year = '';
                                        } else {
                                            var temp_date = $scope.doctor.edu_object[key].doctor_qualification_completion_year + '-01-01T00:00:00';
                                            var date = new Date(temp_date);
                                            $scope.doctor.edu_object[key].edu_year = date;
                                        }
                                    });
                                    $scope.addEduObject(1);

                                }

                                $scope.doctor.specialities_obj = [];
                                if (response.speciality_data) {
                                    $scope.doctor.speciality = [];
                                    $scope.other.temp_specializations = [];
                                    $scope.doctor.sp_data = response.speciality_data.split(',');
                                    angular
                                            .forEach($scope.doctor.sp_data, function (value, key) {
                                                $scope.doctor.speciality.push(value);
                                                $scope.doctor.specialities_obj.push({'doctor_speciality_name': value});
                                                var flag = false;
                                                angular.forEach($scope.other.temp_specializations_child, function (innerValue, innerKey) {
                                                    if (innerValue.specialization_title == value) {
                                                        flag = true;
                                                    }
                                                });
                                                if (!flag) {
                                                    $scope.other.temp_specializations_child.push({
                                                        specialization_title: value
                                                    });
                                                }
                                            });
                                }
                                $scope.addEduObject(5);
                            }

                        }, function (error) {
                            if (error.status == 403) {
                                $scope.getEduDetails();
                            }
                        });
            }

            $scope.removeImage = function (type, index) {
                if (type == 1) {
                    $scope.doctor.specialization[index].doctor_specialization_image_full_path = '';
                    $scope.doctor.specialization[index].cancel_image = "";
                } else if (type == 2) {
                    $scope.doctor.edu_object[index].doctor_qualification_image_full_path = "";
                    $scope.doctor.edu_object[index].image_thumb_path = "";
                    $scope.doctor.edu_object[index].cancel_image = "";
                } else if (type == 3) {
                    $scope.doctor.registration_obj[index].doctor_registration_image_filepath = "";
                    $scope.doctor.registration_obj[index].cancel_image = "";
                } else if (type == 4) {
                    $scope.doctor.award_obj[index].doctor_award_image_fullpath = "";
                    $scope.doctor.award_obj[index].cancel_image = "";
                }
            }

            /* get registration details */
            $scope.getRegDetails = function () {
                if (!$scope.other.councils || $scope.other.councils.length == 0) {
                    CommonService.getCouncil('', function (response) {
                        if (response.status == true) {
                            $scope.other.councils = response.data;
                        }
                    });
                }
                UserService
                        .getDoctorRegDetail($rootScope.currentUser.user_id, function (response) {

                            if (response.status == true) {
                                if (response.reg_data.length > 0) {
                                    $scope.doctor.registration_obj = response.reg_data;
                                    angular.forEach($scope.doctor.registration_obj, function (value, key) {
                                        if($scope.doctor.registration_obj[key].doctor_registration_year == '0000' || $scope.doctor.registration_obj[key].doctor_registration_year == "") {
                                            $scope.doctor.registration_obj[key].reg_year = '';
                                        } else {
                                            var temp_date = $scope.doctor.registration_obj[key].doctor_registration_year + '-01-01T00:00:00';
                                            var date = new Date(temp_date);
                                            $scope.doctor.registration_obj[key].reg_year = date;
                                        }
                                    });
                                    $scope.addEduObject(2);
                                }
                            }
                        }, function (error) {
                            if (error.status == 403) {
                                $scope.getRegDetails();
                            }
                        });
            }


            $scope.openFile = function (fileObj) {
                setTimeout(function () {
                    document.getElementById(fileObj).click();
                }, 0);
            };


            $scope.getFile = function (file_type, file_obj, key) {

                fileReader.readAsDataUrl(file_obj, $scope)
                        .then(function (result) {

                            if (file_type == "profile") {
                                $scope.imageSrc = result;
                                $scope.doctor.user_photo_filepath = result;
                            } else if (file_type == "clinic_image") {
                                $scope.clinicImageSrc = result;
                                $scope.clinicImageSrcEdited = '1';
                            } else if (file_type == "clinic_outside") {
                                $scope.clinicOutsideImageSrc = result;
                                $scope.clinicOutsideImageSrcEdited = '1';
                            } else if (file_type == "clinic_waiting") {
                                $scope.clinicWaitingImageSrc = result;
                                $scope.clinicWaitingImageSrcEdited = '1';
                            } else if (file_type == "clinic_reception") {
                                $scope.clinicReceptionImageSrc = result;
                                $scope.clinicReceptionImageSrcEdited = '1';
                            } else if (file_type == "edu") {
                                $scope.doctor.edu_object[key].doctor_qualification_image_full_path = result;
                                $scope.doctor.edu_object[key].image_thumb_path = result;
                                $scope.doctor.edu_object[key].cancel_image = '1';
                            } else if (file_type == "reg") {
                                $scope.doctor.registration_obj[key].doctor_registration_image_filepath = result;
                                $scope.doctor.registration_obj[key].image_thumb_path = result;
                                $scope.doctor.registration_obj[key].cancel_image = '1';
                            } else if (file_type == "sp") {
                                $scope.doctor.specialization[key].doctor_specialization_image_full_path = result;
                                $scope.doctor.specialization[key].cancel_image = '1';
                            } else if (file_type == "award") {
                                $scope.doctor.award_obj[key].doctor_award_image_fullpath = result;
                                $scope.doctor.award_obj[key].image_thumb_path = result;
                                $scope.doctor.award_obj[key].cancel_image = '1';
                            } else if (file_type == "clinic_address_proof") {
                                $scope.clinicAddressImageSrc = result;
                                $scope.clinicAddressImageSrcEdited = '1';
                            }

                        });
            };

            $scope.removeProfile = function (type) {
                if (type == 1) {
                    $scope.imageSrc = '';
                    $scope.doctor.user_photo_filepath = '';
                }
            };
            $scope.signature = {imageSignSrc:''}
            $scope.removeSign = function () {
                $scope.signature.imageSignSrc = '';
                $scope.doctor.user_sign_filepath = '';
            };

            $scope.removeClinicImage = function (type) {
                if (type == 1) {
                    $scope.clinicImageSrc = '';
                    $scope.clinicImageSrcEdited = '';
                } else if (type == 2) {
                    $scope.clinicOutsideImageSrc = '';
                    $scope.clinicOutsideImageSrcEdited = '';
                } else if (type == 3) {
                    $scope.clinicWaitingImageSrc = '';
                    $scope.clinicWaitingImageSrcEdited = '';
                } else if (type == 4) {
                    $scope.clinicReceptionImageSrc = '';
                    $scope.clinicReceptionImageSrcEdited = '';
                } else if (type == 5) {
                    $scope.clinicAddressImageSrc = '';
                    $scope.clinicAddressImageSrcEdited = '';
                }
            };

            $scope.updatePersonalDetail = function (isRedirect) {
                $scope.submitted = true;
                if ($scope.updatePersonalForm.$valid) {
					if ($scope.doctor.address_latitude == undefined || $scope.doctor.address_longitude == undefined) {
                        //ngToast.danger("Please enter valid address");
                        //return false;
                    }
                    $scope.submitted = false;
                    //language string
					if($scope.doctor != undefined && $scope.doctor.language != undefined)
						$scope.doctor.language_string = $scope.doctor.language.join(',');
					else
						$scope.doctor.language_string = '';
					$rootScope.app.isLoader = true;
					
					if($scope.doctor.address_country_id != undefined && $scope.other.country != undefined){
						var selectedCountryObj = $filter('filter')($scope.other.country, {'country_id':$scope.doctor.address_country_id},true);
						if(selectedCountryObj != undefined && selectedCountryObj.length > 0){
							$scope.doctor.address_country_id_text = selectedCountryObj[0].country_name;
						}
					}
					
					if($scope.doctor.address_state_id != undefined && $scope.other.state != undefined){
						var selectedStateObj = $filter('filter')($scope.other.state, {'state_id':$scope.doctor.address_state_id},true);
						if(selectedStateObj != undefined && selectedStateObj.length > 0){
							$scope.doctor.address_state_id_text = selectedStateObj[0].state_name;
						}
					}
					
					if($scope.doctor.address_city_id != undefined && $scope.other.city != undefined){
						var selectedCityObj = $filter('filter')($scope.other.city, {'city_id':$scope.doctor.address_city_id},true);
						if(selectedCityObj != undefined && selectedCityObj.length > 0){
							$scope.doctor.address_city_id_text = selectedCityObj[0].city_name;
						}
					}
					
					UserService
                            .updateUserPersonalDetail($scope.doctor, function (response) {
                                if (response.status == true) {
                                    $scope.getDoctorWholeDetail();
									$localStorage.currentUser.user_first_name    = $scope.doctor.user_first_name;
                                    $localStorage.currentUser.user_last_name 	 = $scope.doctor.user_last_name;
                                    $localStorage.currentUser.address_country_id = $scope.doctor.address_country_id;
                                    $localStorage.currentUser.address_city_id 	 = $scope.doctor.address_city_id;
                                    $localStorage.currentUser.address_state_id 	 = $scope.doctor.address_state_id;
                                    $localStorage.currentUser.address_latitude   = $scope.doctor.address_latitude;
                                    $localStorage.currentUser.address_longitude  = $scope.doctor.address_longitude;
                                    $localStorage.currentUser.address_pincode    = $scope.doctor.address_pincode;
                                    $localStorage.currentUser.address_locality   = $scope.doctor.address_locality;
                                    $localStorage.currentUser.address_name       = $scope.doctor.address_name;
                                    if (angular.isObject($scope.doctor.address_name)) {
                                        $localStorage.currentUser.address_name   = $scope.doctor.address_name.getPlace().formatted_address;
                                    }
                                    //upload image code
                                    if ($scope.imageSrc != undefined && $scope.imageSrc != ''){
                                        $rootScope.app.isLoader = true;
                                        UserService
                                                .uploadUserImage($scope.doctor, function (response){
                                                    $rootScope.app.isLoader = false;
                                                    $localStorage.currentUser.user_photo_filepath = response.user_data.user_photo_filepath_thumb;
                                                    $scope.doctor.user_photo_filepath = response.user_data.user_photo_filepath;
                                                    $scope.doctor.user_photo_filepath_thumb = response.user_data.user_photo_filepath_thumb;
                                                });
                                    }
                                    //upload image code
                                    if ($scope.signature.imageSignSrc != undefined && $scope.signature.imageSignSrc != ''){
                                        $rootScope.app.isLoader = true;
                                        UserService
                                                .uploadSignImage($scope.signature, function (response){
                                                    $rootScope.app.isLoader = false;
                                                    $scope.doctor.user_sign_filepath = response.user_data.user_sign_filepath;
                                                    $scope.doctor.user_sign_filepath_thumb = response.user_data.user_sign_filepath_thumb;
                                                    $scope.signature.imageSignSrc = '';
                                                });
                                    }
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.getPercentage();
                                    $scope.imageSrc = '';
                                    if (isRedirect) {
                                        $state.go('app.complete_profile_view.edu');
                                    }
                                    if (response.data.phone_number_updated == 1) {
                                        $scope.Model.number = $scope.doctor.user_phone_number;
                                        $("#otp_modal").modal("show");
                                        $scope.settick();
                                        tick();
                                    }
                                } else {
                                    var temp_date = $scope.doctor.doctor_detail_year_of_experience + 'T00:00:00';
                                    var date = new Date(temp_date);
                                    $scope.doctor.doctor_detail_year_of_experience = date;
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                                $rootScope.app.isLoader = false;
                            });
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            };

            $scope.updateEduDetail = function (isRedirect) {
                $scope.submitted = true;
                if ($scope.updateEduForm.$valid) {
                    $scope.doctor.specialitity_string = '';
                    for (var i = 0; i < $scope.doctor.specialities_obj.length; i++) {
                        if($scope.doctor.specialities_obj[i].doctor_speciality_name !=undefined && $scope.doctor.specialities_obj[i].doctor_speciality_name !="")
                            $scope.doctor.specialitity_string += $scope.doctor.specialities_obj[i].doctor_speciality_name;
                        if ($scope.doctor.specialities_obj[i+1] != undefined && $scope.doctor.specialities_obj[i+1].doctor_speciality_name != undefined && $scope.doctor.specialities_obj[i+1].doctor_speciality_name != '') {
                            $scope.doctor.specialitity_string += ',';
                        }
                    }
					$scope.doctor.edu_object.splice($scope.doctor.edu_object.length - 1, 1);
                    if($scope.doctor.edu_object.length > 0){
                        for (var j = 0; j < $scope.doctor.edu_object.length; j++) {
                            if($scope.doctor.edu_object[j] != undefined && $scope.doctor.edu_object[j].doctor_qualification_degree != undefined){
                                var selectedObj = $filter('filter')($scope.other.qualification, {'qualification_name':$scope.doctor.edu_object[j].doctor_qualification_degree},true);
                                if(selectedObj != undefined && selectedObj.length > 0){
                                    $scope.doctor.edu_object[j].doctor_qualification_qualification_id = selectedObj[0].qualification_id;
                                }
                            }
                        }
                    }
					$scope.submitted = false;
                    $rootScope.app.isLoader = true;
                    UserService
							.updateUserEduDetail($scope.doctor, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.getPercentage();
                                    if (isRedirect) {
                                        $state.go("app.complete_profile_view.reg");
                                    }
                                    $scope.getEduDetails();
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                                $rootScope.app.isLoader = false;
                            });
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            }
            $scope.updateRegDetail = function (isRedirect) {
                $scope.submitted = true;
                if ($scope.updateRegForm.$valid) {
					$scope.submitted = false;
                    $rootScope.app.isLoader = true;
                    $scope.doctor.registration_obj.splice($scope.doctor.registration_obj.length - 1, 1);
					if($scope.doctor.registration_obj[0]!=undefined && $scope.doctor.registration_obj[0].doctor_council_registration_number !=undefined){
						UserService
                            .updateUserRegDetail($scope.doctor, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.getPercentage();
                                    $scope.getRegDetails();
                                    if (isRedirect) {
                                        $state.go("app.complete_profile_view.award");
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                                $rootScope.app.isLoader = false;
                            });
					}else{
						$scope.submitted = false;
                        $rootScope.app.isLoader = false;
						SweetAlert.swal($rootScope.app.common_error);
					}
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            }
            $scope.updateAwardDetails = function (isRedirect) {
                $scope.submitted = true;
                if ($scope.awardForm.$valid) {
                    $scope.submitted = false;
                    $rootScope.app.isLoader = true;
					if($scope.doctor.award_obj.length > 1){
						$scope.doctor.award_obj.splice($scope.doctor.award_obj.length - 1, 1);
					}
					if($scope.doctor.award_obj[0]!=undefined && $scope.doctor.award_obj[0].doctor_award_name!=undefined && $scope.doctor.award_obj[0].award_year!=undefined){
						 UserService
                            .updateDoctorAwardDetail($scope.doctor, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.getPercentage();
                                    $scope.getAwardDetails();
                                    if (isRedirect) {
                                        $state.go("app.profile.my_profile_view");
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                                $rootScope.app.isLoader = false;
                            });
					}else{
						if (isRedirect){
							$state.go("app.profile.my_profile_view");
						}else{ 
							SweetAlert.swal($rootScope.app.common_error);
							$scope.submitted = false;
							$rootScope.app.isLoader = false;
						}
					}
                } else {
					if (isRedirect){
						$state.go("app.profile.my_profile_view");
					}else{ 
						SweetAlert.swal($rootScope.app.common_error);
						$scope.submitted = false;
						$rootScope.app.isLoader = false;
					}
                }
            }

            $scope.$on('gmPlacesAutocomplete::placeChanged', function () {
                $scope.other.is_copied = false;
                if ($scope.doctor.address_name != '' && $scope.doctor.address_name != undefined && $scope.doctor.address_name.getPlace() != undefined) {
                    var location = $scope.doctor.address_name.getPlace().geometry.location;
                    $scope.doctor.address_latitude = location.lat();
                    $scope.doctor.address_longitude = location.lng();
                }
                if ($scope.other.clinic_address != '' && $scope.other.clinic_address != undefined && $scope.other.clinic_address.getPlace() != undefined) {
                    var location = $scope.other.clinic_address.getPlace().geometry.location;
                    $scope.other.cliniclat = location.lat();
                    $scope.other.cliniclng = location.lng();
                }

                $scope.$apply();
            });
            // add edu object on blur on textbox
            $scope.addEduObj = function () {
                var is_add = false;
                if ($scope.doctor.edu_object.length > 0) {
                    angular.forEach($scope.doctor.edu_object, function (value, key) {
                        if (value.doctor_qualification_college == '' && value.doctor_qualification_degree == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(1);
                    }
                }
            }
            // set required field on edu object
            $scope.isEduObjRequired = function (eduObj, key) {

                if (eduObj.length == 1) {
                    return true;
                }
                if (!eduObj[key].doctor_qualification_degree)
                {
                    return false;
                } else {
                    return true;
                }
            }
            $scope.isSpecialityObjRequired = function (specialityObj, key) {
                if (specialityObj.length == 1) {
                    return true;
                }
                if (specialityObj[key].doctor_speciality_name != undefined || specialityObj[key].doctor_speciality_name != '')
                {
                    return false;
                } else {
                    return true;
                }
            }
            // add register document on on blur event on textbox
            $scope.addRegDocmentObj = function () {
                var is_add = false;
                if ($scope.doctor.registration_obj.length > 0) {
                    angular.forEach($scope.doctor.registration_obj, function (value, key) {
                        if (value.doctor_council_registration_number == '' && value.doctor_registration_council_id == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(2);
                    }
                }
            }
            // set required field
            $scope.isRegDocmentObjRequired = function (regDocumentObj, key) {
                if (regDocumentObj.length == 1) {
                    return true;
                }
                if (!regDocumentObj[key].doctor_council_registration_number)
                {
                    return false;
                } else {
                    return true;
                }
            }
            // add register document on on blur event on textbox
            $scope.addAwardObj = function () {
                var is_add = false;
                if ($scope.doctor.award_obj.length > 0) {
                    angular.forEach($scope.doctor.award_obj, function (value, key) {
                        if (value.doctor_award_name == '' && value.doctor_award_year == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(4);
                    }
                }
            }

            $scope.isAwardObjRequired = function (awardObj, key) {
                if (awardObj.length == 1) {
                    return true;
                }
                if (!awardObj[key].doctor_award_name && !awardObj[key].doctor_award_year)
                {
                    return false;
                } else {
                    return true;
                }
            }

            $scope.addSpecialityObj = function () {
                var is_add = false;
                if ($scope.doctor.specialities_obj.length > 0) {
                    angular.forEach($scope.doctor.specialities_obj, function (value, key) {
                        if (value.doctor_speciality_name == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(5);
                    }
                }
            }
            //dynamic html code
            $scope.addEduObject = function (type) {
                if (type == 1) {
                    $scope.doctor.edu_object.push({
                        doctor_qualification_degree: '',
                        doctor_qualification_college: '',
                        doctor_qualification_completion_year: '',
                        doctor_qualification_image_full_path: '',
                        image_thumb_path: '',
                        img_file_name: '',
                        img_file: '',
                        temp_img: undefined,
                        cancel_image: ''
                    });
                } else if (type == 2) {
                    $scope.doctor.registration_obj.push({
                        doctor_registration_image_filepath: '',
                        doctor_registration_council_id: '',
                        doctor_council_registration_number: '',
                        doctor_registration_year: '',
                        img_file: '',
                        temp_img: '',
                        cancel_image: ''
                    });

                } else if (type == 3) {
                    $scope.doctor.specialization.push({
                        doctor_specialization_specialization_id: '',
                        doctor_specialization_image_full_path: '',
                        img_file: '',
                        temp_img: undefined,
                        cancel_image: ''
                    });

                } else if (type == 4) {
                    $scope.doctor.award_obj.push({
                        doctor_award_name: '',
                        doctor_award_year: '',
                        doctor_award_image_fullpath: '',
                        img_file: '',
                        temp_img: undefined,
                        cancel_image: ''
                    });
                } else if (type == 5) {
                    $scope.doctor.specialities_obj.push({
                        doctor_speciality_name: undefined
                    });
                }
            }
            //dynamic html code
            $scope.removeEduObject = function (type, index) {
                if (type == 1) {
                    $scope.doctor.edu_object.splice(index, 1);
                } else if (type == 2) {
                    $scope.doctor.registration_obj.splice(index, 1);
                } else if (type == 3) {
                    $scope.doctor.specialization.splice(index, 1);
                } else if (type == 4) {
                    $scope.doctor.award_obj.splice(index, 1);
                } else if (type == 5) {
                    $scope.doctor.specialities_obj.splice(index, 1);
                }
            }

            $scope.openFileObject = function (fileObj, key) {
                setTimeout(function () {
                    document.getElementById(fileObj + key).click();
                }, 0);
            };

            $scope.edu_year_picker = {
                date: new Date(),
                datepickerOptions: {
                    mode: 'year',
                    minMode: 'year',
                    maxMode: 'year',
                    maxDate: new Date()
                },
                open: false
            };


            /* get award details */
            $scope.getAwardDetails = function () {
                UserService
                        .getDoctorAwardDetails($localStorage.currentUser.user_id, function (response) {
                            if (response.award_data.length > 0) {
                                $scope.doctor.award_obj = response.award_data;
                                angular.forEach($scope.doctor.award_obj, function (value, key) {
                                    var temp_date = $scope.doctor.award_obj[key].doctor_award_year + '-01-01T00:00:00';
                                    var date = new Date(temp_date);
                                    $scope.doctor.award_obj[key].award_year = date;
                                });
                                $scope.addEduObject(4);
                            }

                        }, function (error) {
                            if (error.status == 403) {
                                $scope.getAwardDetails();
                            }
                        });
            }

            $scope.showFullImage = function (img_path) {
                $scope.Model.currentImg = img_path;
                $("#fullscreen_img_modal").show();

            }

            $scope.closeFullImgModal = function () {
                $("#fullscreen_img_modal").hide();
            }

            $scope.hideSecondSession = function () {

                $scope.secondsession = false;
                $scope.other.secondsession = false;
                $scope.other.clinic_start_time2 = '';
                $scope.other.clinic_end_time2 = '';
            }
            $scope.startPicker = {
                timepickerOptions: {
                    readonlyInput: false,
                    showMeridian: false,
                    minuteStep: 5,
                },
                buttonBar: {
                    show: true,
                    now: {
                        show: false,
                        text: 'Now',
                        cls: 'btn-sm btn-default'
                    },
                    today: {
                        show: true,
                        text: 'Today',
                        cls: 'btn-sm btn-default'
                    },
                    clear: {
                        show: true,
                        text: 'Clear',
                        cls: 'btn-sm btn-default'
                    },
                    date: {
                        show: true,
                        text: 'Date',
                        cls: 'btn-sm btn-default'
                    },
                    time: {
                        show: true,
                        text: 'Time',
                        cls: 'btn-sm btn-default'
                    },
                    close: {
                        show: true,
                        text: 'OK',
                        cls: 'btn-sm btn-default'
                    },
                    cancel: {
                        show: false,
                        text: 'Cancel',
                        cls: 'btn-sm btn-default'
                    }
                }

            };

            $scope.getStaffProfile = function (staff) {
                $scope.show_doctor = false;
                $scope.show_receptionist = true;
                $scope.receptionist = [];
                if (staff) {
                    $scope.receptionist = staff;
                }
            }
            /* add new clinic */
            $scope.addClinic = function (isFromClinic) {
                $scope.submitted = true;
                if ($scope.clinicFormAddressRegister.$valid) {
                    if (!$scope.checkSessionTiming()) {
                        return false;
                    }

                    $scope.other.is_from_usercontroller = true;
                    // if ($scope.other.cliniclat == undefined || $scope.other.cliniclng == undefined) {
                    //     ngToast.danger("Enter valid clinic address");
                    //     return false;
                    // }
                    if ($scope.compareTime($scope.other.clinic_start_time, $scope.other.clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }

                    //                    var clinic_start_duration = $filter('timeToMinutes')($scope.other.clinic_start_time);
                    //                    var clinic_end_duration = $filter('timeToMinutes')($scope.other.clinic_end_time);
                    //                    var clinic_duration = clinic_end_duration - clinic_start_duration;
                    //                    if (clinic_duration < $scope.other.clinic_duration) {
                    //                        ngToast.danger("Please select duration as per clinic timing");
                    //                        return false;
                    //                    }
                    if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined && $scope.other.clinic_end_time2 != '' && $scope.other.clinic_end_time2 != undefined) {
                        if ($scope.compareTime($scope.other.clinic_start_time2, $scope.other.clinic_end_time2) == false) {
                            ngToast.danger("Clinic start time should be smaller");
                            return false;
                        }
                        if ($scope.compareTime($scope.other.clinic_end_time, $scope.other.clinic_start_time2) == false) {
                            ngToast.danger("Please enter valid session timing");
                            return false;
                        }
                    }
                    /*if (
                            $scope.clinicImageSrc == undefined ||
                            $scope.clinicWaitingImageSrc == undefined ||
                            $scope.clinicReceptionImageSrc == undefined ||
                            $scope.clinicAddressImageSrc == undefined
                            ) {
                        ngToast.danger("Please Select image");
                        return false;
                    }*/

                    $scope.submitted = false;
                    $scope.other.duration_mint = $scope.other.clinic_duration;
                    $rootScope.app.isLoader = true;

                    if (angular.isDate($scope.other.clinic_start_time))
                        $scope.other.clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());

                    if (angular.isDate($scope.other.clinic_start_time2))
                        $scope.other.clinic_start_time2 = $scope.addZero($scope.other.clinic_start_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time2.getMinutes());

                    if (angular.isDate($scope.other.clinic_end_time))
                        $scope.other.clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());

                    if (angular.isDate($scope.other.clinic_end_time2))
                        $scope.other.clinic_end_time2 = $scope.addZero($scope.other.clinic_end_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time2.getMinutes());

                    
                    if($scope.other.clinic_email == undefined){
                        $scope.other.clinic_email = "";
                    }
                    
                    ClinicService
                            .addNewClinic($scope.other, function (response) {
                                if (response.status == true) {
                                    //upload image code
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $rootScope.app.is_not_valid = false;
                                    $rootScope.app.is_clinic_complete = 1;
                                    if($scope.other.is_add_from_calendar != undefined && $scope.other.is_add_from_calendar) {
                                        $("#add_clinic_modal").modal("hide");
                                        $scope.$emit('refreshClinicList',response.data);
                                    } else {
                                        if (isFromClinic) {
                                            $("#add_clinic_modal").hide();
    										$('body,html').removeClass('modal-open');
                                            $state.go('app.profile.clinic_view', {}, {reload: true});
                                            $rootScope.getSidebarMenu();
                                        } else {
                                            $state.go('app.dashboard');
                                            $rootScope.getSidebarMenu();
                                        }
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                    if($('#add_clinic_modal'))
                                        $('#add_clinic_modal .modal-backdrop').height($("#add_clinic_modal .modal-backdrop.in").height() + 300);
                                }
                                $rootScope.app.isLoader = false;
                                $scope.submitted = false;
                            });
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                    if($('#add_clinic_modal'))
                        $('#add_clinic_modal .modal-backdrop').height($("#add_clinic_modal .modal-backdrop.in").height() + 300);
                }
            }


            //dynamic html code
            $scope.addClinicService = function (type) {
                if (type == 1) {
                    $scope.other.clinic_service.push({
                        text: ''
                    });
                }
            }
            //dynamic html code
            $scope.removeClinicservice = function (type, index) {
                if (type == 1) {
                    $scope.other.clinic_service.splice(index, 1);
                }
            }
            $scope.copyDoctorAddressFromClinicPage = function () {
                var address = $rootScope.currentUser.address_name;
                if (!address) {
                    UserService
                            .getDoctorWholeDetail($rootScope.currentUser.user_id, function (response) {
                                if (response.status == true) {
                                    if (response.data != '') {
                                        $rootScope.currentUser.address_name       = response.data.address_name;
                                        $rootScope.currentUser.address_latitude   = response.data.address_latitude;
                                        $rootScope.currentUser.address_longitude  = response.data.address_longitude;
                                        $rootScope.currentUser.address_country_id = response.data.address_country_id;
                                        $rootScope.currentUser.address_state_id   = response.data.address_state_id;
                                        $rootScope.currentUser.address_city_id    = response.data.address_city_id;
                                        $rootScope.currentUser.address_locality   = response.data.address_locality;
                                        $rootScope.currentUser.address_pincode    = response.data.address_pincode;
                                        $scope.other.clinic_address 			  = $rootScope.currentUser.address_name;
										if(response.data.address_name != '')
											$scope.other.is_copied = true;
										
                                        $scope.other.cliniclat = $rootScope.currentUser.address_latitude;
                                        $scope.other.cliniclng = $rootScope.currentUser.address_longitude;
                                        $scope.other.selected_clinic_country = $rootScope.currentUser.address_country_id;
                                        $scope.other.selected_clinic_state 	 = $rootScope.currentUser.address_state_id;
                                        if($scope.other.selected_clinic_state != ''){
											CommonService.getCity($scope.other.selected_clinic_state, true, function (response) {
												if (response.status == true) {
													$scope.other.city = response.data;
												}
											});
										}
                                        if ($scope.other.selected_clinic_country != undefined && $scope.other.selected_clinic_country != '' && $scope.other.state.length <= 0) {
                                            CommonService.getState($scope.other.selected_clinic_country, true, function (response) {
                                                if (response.status == true) {
                                                    $scope.other.state = response.data;
                                                }
                                            });
                                        }
                                        
                                        $scope.other.selected_clinic_city = $rootScope.currentUser.address_city_id;
                                        $scope.other.clinic_locality = $rootScope.currentUser.address_locality;
                                        $scope.other.clinic_zipcode = $rootScope.currentUser.address_pincode;
                                    }
                                }
                            });
                } else {
                    $scope.other.clinic_address = address;
                    $scope.other.is_copied = true;
                    $scope.other.cliniclat = $rootScope.currentUser.address_latitude;
                    $scope.other.cliniclng = $rootScope.currentUser.address_longitude;
                    $scope.other.selected_clinic_country = $rootScope.currentUser.address_country_id;
                    $scope.other.selected_clinic_state = $rootScope.currentUser.address_state_id;

                    CommonService.getCity($scope.other.selected_clinic_state, true, function (response) {
                        if (response.status == true) {
                            $scope.other.city = response.data;
                        } else {
                            ngToast.danger(response.message);
                        }
                    });
                    if ($scope.other.state.length <= 0) {
                        CommonService.getState($scope.other.selected_clinic_country, true, function (response) {

                            if (response.status == true) {
                                $scope.other.state = response.data;
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                    }
                    $scope.other.is_copied = true;
                    $scope.other.selected_clinic_city = $rootScope.currentUser.address_city_id;
                    $scope.other.clinic_locality = $rootScope.currentUser.address_locality;
                    $scope.other.clinic_zipcode = $rootScope.currentUser.address_pincode;
                }
            }
            $scope.addZero = function (i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
            $scope.addClientServiceObj = function () {
                var is_add = false;
                if ($scope.other.clinic_service.length > 0) {
                    angular.forEach($scope.other.clinic_service, function (value, key) {
                        if (value.text == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addClinicService(1);
                    }
                }
            }
            $scope.isClientServiceRequired = function (clientServiceObj, key) {
                if (clientServiceObj.length == 1)
                    return true;

                if (!clientServiceObj[key].text)
                    return false;
                else
                    return true;

            }
            /* add new clinic */
            $scope.editClinic = function (editForm, isFromSetting) {
                $scope.submitted = true;
                if (editForm.$valid) {
                    /*if (!$scope.checkSessionTimingEdit()) {
                        return false;
                    }*/
                    $scope.other.clinic_service.splice($scope.other.clinic_service.length - 1, 1);
                    $scope.other.is_from_usercontroller = true;
                    if ($scope.other.cliniclat == undefined || $scope.other.cliniclng == undefined) {
                        ngToast.danger("Enter valid clinic address");
                        return false;
                    }

                    if (angular.isDate($scope.other.clinic_start_time))
                        $scope.other.temp_clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());
                    else
                        $scope.other.temp_clinic_start_time = $scope.other.clinic_start_time;

                    if (angular.isDate($scope.other.clinic_end_time))
                        $scope.other.temp_clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());
                    else
                        $scope.other.temp_clinic_end_time = $scope.other.clinic_end_time;

                    /*if ($scope.compareTime($scope.other.temp_clinic_start_time, $scope.other.temp_clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }*/
                    // var clinic_start_duration = $filter('timeToMinutes')($scope.other.temp_clinic_start_time);
                    // var clinic_end_duration = $filter('timeToMinutes')($scope.other.temp_clinic_end_time);
                    // var clinic_duration = clinic_end_duration - clinic_start_duration;
                    // if (clinic_duration < $scope.other.clinic_duration) {
                    //     ngToast.danger("Please select duration as per clinic timing");
                    //     return false;
                    // }
                    $scope.other.temp_clinic_start_time2 = '';
                    $scope.other.temp_clinic_end_time2 = '';

                    if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined
                            && $scope.other.clinic_start_time2 != '00:00:00'
                            ) {
                        $scope.other.temp_clinic_start_time2 = $scope.addZero($scope.other.clinic_start_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time2.getMinutes());
                        $scope.other.temp_clinic_end_time2 = $scope.addZero($scope.other.clinic_end_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time2.getMinutes());
                        /*if ($scope.compareTime($scope.other.temp_clinic_start_time2, $scope.other.temp_clinic_end_time2) == false) {
                            ngToast.danger("Clinic start time should be smaller");
                            return false;
                        }
                        if ($scope.compareTime($scope.other.temp_clinic_end_time, $scope.other.temp_clinic_start_time2) == false) {
                            ngToast.danger("Please enter valid session timing");
                            return false;
                        }*/
                    }
                    /* if (
                            $scope.clinicImageSrc == undefined ||
                            $scope.clinicWaitingImageSrc == undefined ||
                            $scope.clinicReceptionImageSrc == undefined ||
                            $scope.clinicAddressImageSrc == undefined
                            ) {
                        ngToast.danger("Please Select image");
                        return false;
                    } */
                    /* give alert for final warning */
                    SweetAlert.swal({
                        title: $rootScope.app.clinic_edit_alert,
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes!",
                        cancelButtonText: "No",
                        closeOnConfirm: true},
                            function (isConfirm) {
                                if (isConfirm) {
                                    $scope.submitted = false;
                                    $scope.other.duration_mint = $scope.other.clinic_duration;
                                    $rootScope.app.isLoader = true;
                                    ClinicService
                                            .editClinic($scope.other, function (response) {
                                                if (response.status == true) {
                                                    if ($rootScope.current_clinic) {
                                                        $rootScope.current_clinic.clinic_name = $scope.other.clinic_name;
                                                        $rootScope.current_clinic.doctor_clinic_mapping_duration = $scope.other.clinic_duration;
                                                        if ($scope.appointment) {
                                                            $scope.appointment.duration = $rootScope.current_clinic.doctor_clinic_mapping_duration;
                                                        }
                                                    }
                                                    //$scope.getTimeslot();
                                                    //upload image code
                                                    ngToast.success({
                                                        content: response.message,
                                                        className: '',
                                                        dismissOnTimeout: true,
                                                        timeout: 5000
                                                    });
													
													$('body,html').removeClass('modal-open');
													$("#edit_clinic_modal").hide();
													
													if (response.data.phone_number_updated == 1) {
                                                        $scope.Model.clinic_number = $scope.other.clinic_number;
                                                        $("#clinic_otp_modal").modal("show");
                                                    } else {
                                                        if (isFromSetting != true) {
                                                            $state.go('app.profile.clinic_view', {}, {reload: true});
                                                        } else {
                                                            $state.go('app.dashboard.setting.clinic_details', {}, {reload: true});
                                                        }
                                                    }
                                                } else {
                                                    ngToast.danger(response.message);
                                                }
                                                $rootScope.app.isLoader = false;
                                                $scope.submitted = false;
                                            });
                                }
                            });
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            }
            $scope.closeClinicOtpModal = function () {
                $scope.submitted = false;
                $state.go('app.profile.clinic_view', {}, {reload: true});
            }
            $scope.checkSessionTiming = function () {

                if ($scope.other.clinic_start_time && $scope.other.clinic_end_time) {
                    if (angular.isDate($scope.other.clinic_start_time))
                        $scope.other.temp_clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());
                    else
                        $scope.other.temp_clinic_start_time = $scope.other.clinic_start_time;

                    if (angular.isDate($scope.other.clinic_end_time))
                        $scope.other.temp_clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());
                    else
                        $scope.other.temp_clinic_end_time = $scope.other.clinic_end_time;

                    // $scope.other.temp_clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());
                    // $scope.other.temp_clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());

                    if ($scope.compareTime($scope.other.temp_clinic_start_time, $scope.other.temp_clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                }
                if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined && $scope.other.clinic_end_time2 != '' && $scope.other.clinic_end_time2 != undefined) {
                    if ($scope.compareTime($scope.other.clinic_start_time2, $scope.other.clinic_end_time2) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                    if ($scope.compareTime($scope.other.clinic_end_time, $scope.other.clinic_start_time2) == false) {
                        ngToast.danger("Please enter valid session timing");
                        return false;
                    }
                }
                if ($scope.other.clinic_start_time && $scope.other.clinic_end_time) {
                    var minutes = $scope.getDurationIntoMin($scope.other.clinic_start_time, $scope.other.clinic_end_time);
                    //now check another session timing
                    if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined) {
                        minutes += $scope.getDurationIntoMin($scope.other.clinic_start_time2, $scope.other.clinic_end_time2);
                    }
                    if (minutes > 0 && $scope.other.clinic_duration) {
                        if (minutes < $scope.other.clinic_duration) {
                            ngToast.danger("Please select valid duration");
                            return false;
                        }
                    }
                }
                return true;

            }
            $scope.checkSessionTimingEdit = function () {
                if ($scope.other.clinic_start_time && $scope.other.clinic_end_time) {
                    $scope.other.temp_clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());
                    $scope.other.temp_clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());

                    if ($scope.compareTime($scope.other.temp_clinic_start_time, $scope.other.temp_clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                }
                if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined
                        && $scope.other.clinic_start_time2 != '00:00:00'
                        ) {
                    $scope.other.temp_clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());
                    $scope.other.temp_clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());

                    $scope.other.temp_clinic_start_time2 = $scope.addZero($scope.other.clinic_start_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time2.getMinutes());
                    if ($scope.other.clinic_end_time2) {
                        $scope.other.temp_clinic_end_time2 = $scope.addZero($scope.other.clinic_end_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time2.getMinutes());

                        if ($scope.compareTime($scope.other.temp_clinic_start_time2, $scope.other.temp_clinic_end_time2) == false) {
                            ngToast.danger("Clinic start time should be smaller");
                            return false;
                        }

                        if ($scope.compareTime($scope.other.temp_clinic_end_time, $scope.other.temp_clinic_start_time2) == false) {
                            ngToast.danger("Please enter valid session timing");
                            return false;
                        }
                    }
                }

                /* logic to check duration is valid or not */
                if ($scope.other.temp_clinic_start_time && $scope.other.temp_clinic_end_time) {

                    var minutes = $scope.getDurationIntoMin($scope.other.temp_clinic_start_time, $scope.other.temp_clinic_end_time);
                    //now check another session timing
                    if ($scope.other.temp_clinic_start_time2 && $scope.other.temp_clinic_end_time2) {
                        minutes += $scope.getDurationIntoMin($scope.other.temp_clinic_start_time2, $scope.other.temp_clinic_end_time2);
                    }

                    if (minutes > 0 && $scope.other.clinic_duration) {
                        if (minutes < $scope.other.clinic_duration) {
                            ngToast.danger("Please select valid duration");
                            return false;
                        }
                    }
                }
                return true;
            }
            $scope.getDurationIntoMin = function (start_obj, end_obj) {
                if (angular.isDate(start_obj)) {
                    start_obj = start_obj.getHours() + ':' + start_obj.getMinutes();
                }
                if (angular.isDate(end_obj)) {
                    end_obj = end_obj.getHours() + ':' + end_obj.getMinutes();
                }

                var timefrom = new Date();
                var temp = start_obj.split(":");
                timefrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
                timefrom.setMinutes(parseInt(temp[1]));
                timefrom.setSeconds(0);

                var timeto = new Date();
                temp = end_obj.split(":");
                timeto.setHours((parseInt(temp[0]) - 1 + 24) % 24);
                timeto.setMinutes(parseInt(temp[1]));
                timeto.setSeconds(0);
                var diffMillies = timeto.getTime() - timefrom.getTime();
                var minutes = Number(diffMillies / 60000);
                return minutes;
            }

            $scope.compareTime = function (start, end) {
                if (start == undefined || end == undefined) {
                    return false;
                }

                if (angular.isDate(start)) {
                    start = start.getHours() + ':' + start.getMinutes();
                }
                if (angular.isDate(end)) {
                    end = end.getHours() + ':' + end.getMinutes();
                }
                var timefrom = new Date();
                var temp = start.split(":");

                timefrom.setHours((parseInt(temp[0]) + 24) % 24);
                timefrom.setMinutes(parseInt(temp[1]));

                var timeto = new Date();
                temp = end.split(":");

                timeto.setHours((parseInt(temp[0]) + 24) % 24);
                timeto.setMinutes(parseInt(temp[1]));

                if (timeto <= timefrom)
                    return false;
                return true;
            }

            $scope.clearEditedData = function () {
                $scope.clinicImageSrc = '';
                $scope.clinicOutsideImageSrc = '';
                $scope.clinicReceptionImageSrc = '';
                $scope.clinicWaitingImageSrc = '';
                $scope.clinicAddressImageSrc = '';
            }
            $scope.$on('fromSettingEditClinic', function (e) {
                $scope.$emit("pingBack", $scope.getClinicDetailForEdit($rootScope.current_clinic.clinic_id));
            });
            $scope.checkMobileKey = function (event) {
                if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
                    event.preventDefault();
                    return false;
                }
            }

            /* clinic drop down code (For clinic staff) */
            $scope.changeCurrentClinicForStaff = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.getDoctorStaffList($rootScope.current_clinic.clinic_id);
            };

            $rootScope.is_send_button_visible = false;
            $rootScope.is_send_timer_visible = true;
            $rootScope.currentTime = 0;

            var tick = function () {
                if ($rootScope.resendWaiting <= $rootScope.currentTime) {
                    $rootScope.is_send_button_visible = true;
                    $rootScope.is_send_timer_visible = false;
                    return false;
                }
                $rootScope.resendWaiting -= 1000;
                $interval.cancel($scope.promise);
                $scope.promise = $interval( function(){ tick(); }, 1000, true);
                $rootScope.is_send_timer_visible = true;
            };

            $scope.settick = function () {
                var date = new Date();
                date.setMinutes(0);
                date.setSeconds(0);
                date.setMilliseconds(0);
                $rootScope.currentTime = date.getTime();
                $rootScope.resendWaiting = $rootScope.currentTime + (60 * 1000);
            };

            $scope.resendUpdateMobileOtp = function () {
                var request = {
                    phone_number: $scope.Model.number
                };
                UserService
                        .resendOTPDoctor(request, function (response) {
                            if (response.status) {
                                ngToast.success(response.message);
                                $rootScope.is_send_timer_visible = true;
                                $rootScope.is_send_button_visible = false;
                                $scope.settick();
                                tick();
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
            };

            /* user number update code */
            $scope.verifyOtpForDoctor = function (form) {

                $scope.submitted = true;
                if (form.$valid) {
                    var request = {
                        number: $scope.Model.number,
                        otp: $scope.doctor.otp,
                    };
                    if ($scope.doctor.user_phone_number != $scope.Model.number) {
                        request.phone_number = $scope.Model.number;
                        UserService
                                .checkOtpNumberUpdated(request, function (response) {
                                    if (response.status) {
                                        $scope.submitted = false;
                                        $("#otp_modal").modal("hide");
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $state.go('app.complete_profile_view.personal', {}, {reload: true});
                                        $scope.doctor.otp = '';
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    } else {

                        UserService
                                .checkOtpForUpdateProfile(request, function (response) {
                                    if (response.status) {
                                        $scope.submitted = false;
                                        $scope.getDoctorWholeDetail();
                                        $("#otp_modal").modal("hide");
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.doctor.otp = '';
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }
            }

            $scope.resetNumber = function (type) {
                if (type == 1) {
                    $scope.doctor.otp = "";
                    $("#otp_modal").modal("hide");
                }
            }

            /* clinic number update code */
            $scope.verifyOtpForClinic = function (form, isFromSetting) {
                $scope.submitted = true;
                if (form.$valid) {
                    var request = {
                        number: $scope.Model.clinic_number,
                        otp: $scope.other.clinic_otp,
                        clinic_id: $scope.other.clinic_id,
                    };

                    UserService
                            .checkOtpForUpdateClinicNumber(request, function (response) {
                                if (response.status) {
                                    $scope.submitted = false;

                                    $("#clinic_otp_modal").modal("hide");
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $scope.other.clinic_otp = '';
                                    if (isFromSetting != true) {
                                        $state.go('app.profile.clinic_view', {}, {reload: true});
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.resendOtpForClinicVerification = function (type) {
                if (type == 1) {
                    var request = {
                        number: $scope.other.clinic_number,
                        otp: $scope.other.clinic_otp,
                        clinic_id: $scope.other.clinic_id,
                    };
                    UserService
                            .resendOTPForClinic(request, function (response) {
                                if (response.status) {
                                    $scope.Model.clinic_number = $scope.other.clinic_number;
                                    $scope.submitted = false;

                                    $("#clinic_otp_modal").modal("show");
                                    ngToast.success({
                                        content: response.message
                                    });
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                } else {
                    var request = {
                        email: $scope.other.clinic_email,
                        otp: $scope.other.clinic_otp,
                        clinic_id: $scope.other.clinic_id,
                    };
                    UserService
                            .resendEmailLink(request, function (response) {
                                if (response.status) {
                                    $scope.submitted = false;

                                    ngToast.success({
                                        content: response.message
                                    });
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.resendMailForDoctor = function (type) {
                if (type == 1) {
                    var request = {
                        email: $scope.doctor.user_email
                    };
                    UserService
                            .resendEmailDoctor(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                } else {
                                    $scope.doctor.user_email_verified = 1;
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                } else if (type == 3) {
                    if ($scope.doctor.user_updated_email == '') {
                        ngToast.danger("Requested Email ID not found");
                        return false;
                    }
                    var request = {
                        email: $scope.doctor.user_updated_email,
                        type: 3
                    };
                    UserService
                            .resendEmailDoctor(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    //$scope.doctor.user_updated_email = '';
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });

                } else {

                    var request = {
                        phone_number: $scope.doctor.user_phone_number
                    };
                    UserService
                            .resendOTPDoctor(request, function (response) {
                                if (response.status) {
                                    $scope.submitted = false;

                                    $("#otp_modal").modal("show");
                                    ngToast.success({
                                        content: response.message
                                    });
                                } else {
                                    $scope.doctor.user_phone_verified = 1;
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                }
            }

			// $scope.notify = [
			//     {
			//         app_sms_status: 2,
			//         app_email_status: 2,
			//         can_sms_status: 2,
			//         can_email_status: 2,
			//         instant_status: 2,
			//         call_status: 2,
			//     }];
			// $scope.notify = [
			//     {
			//         id: 1,
			//         name: 'app_sms_status',
			//         status: '2'
			//     }, {
			//         id: 2,
			//         name: 'app_email_status',
			//         status: '2'
			//     }, {
			//         id: 3,
			//         name: 'can_sms_status',
			//         status: '2'
			//     }, {
			//         id: 4,
			//         name: 'can_email_status',
			//         status: '2'
			//     }, {
			//         id: 5,
			//         name: 'instant_status',
			//         status: '2'
			//     }, {
			//         id: 6,
			//         name: 'call_btn_status',
			//         status: '2'
			//     }
			//
			// ];
            $scope.notify = [
                {"id": 1, "name": "app_sms_status", "status": "2"},
                {"id": 2, "name": "app_email_status", "status": "2"},
                {"id": 3, "name": "can_sms_status", "status": "2"},
                {"id": 4, "name": "can_email_status", "status": "2"},
                {"id": 5, "name": "instant_status", "status": "2"},
                {"id": 6, "name": "call_btn_status", "status": "2"},
                {"id": 7, "name": "reschedule_sms_status", "status": "2"},
                {"id": 8, "name": "reschedule_email_status", "status": "2"},
                {"id": 9, "name": "app_notification_status", "status": "2"},
                {"id": 10, "name": "cancel_notification_status", "status": "2"},
                {"id": 11, "name": "reschedule_notification_status", "status": "2"},
                {"id": 12, "name": "payment_sms_status", "status": "2"},
                {"id": 13, "name": "payment_email_status", "status": "2"},
                {"id": 14, "name": "payment_notification_status", "status": "2"}

            ];
            /* save notification status */
            $scope.saveNotificationStatus = function () {
                var request = {
                    clinic_id: '',
                    setting_data_type: 1,
                    setting_type: 3,
                    data: JSON.stringify($scope.notify)
                };
                SettingService
                        .saveShareSetting(request, function (response) {
                            if (response.status) {
                                ngToast.success({content: response.message});
                            } else {
                                ngToast.danger(response.message);
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });

            }
            $scope.getSettingData = function () {
                var request = {
                    clinic_id: '',
                    setting_type: 3
                };
                SettingService
                        .getSetting(request, function (response) {
                            if (response.data) {
                                $scope.notify = JSON.parse(response.data.setting_data);
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }

            $scope.signaturePopup = function () {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'app/views/profile/signature.html?' + $rootScope.getVer(2),
                    controller: 'ModalSignCtrl',
                    size: 'sm',
                    backdrop: 'static',
                    keyboard: false,
                    resolve: {
                        items: function () {
                            return '';
                        }
                    }
                });
            }


        });

angular.module('app.profile').controller('ModalSignCtrl', function ($scope, $rootScope, $filter, $uibModalInstance, items, SMOKE, ALCOHOL, EncryptDecrypt, SweetAlert, PatientService, CommonService, ngToast,angularLoad) {
    $rootScope.updateBackDropModalHeight('user-signature-modal');
    angularLoad.loadScript('app/plugins/signature_pad/signature_pad.umd.js?' + $rootScope.getVer(2)).then(function() {
        angularLoad.loadScript('app/plugins/signature_pad/app.js?' + new Date().getTime());
    }).catch(function() {
        console.log('Signature lib error. Please contact support.');
    });
    $scope.close = function () {
        $uibModalInstance.close();
    };
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});
/* Preview img code */
angular.module("app.profile")
        .directive("ngFileSelect", ['ngToast', function (ngToast) {
                return {
                    link: function ($scope, el, attr) {
                        el.bind("change", function (e) {

                            var file_obj = (e.srcElement || e.target).files[0];
                            var file_type = file_obj.name;

                            if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {

                                if (attr.obj == "profile") {
                                    $scope.doctor.profile_file = file_obj;
                                } else if (attr.obj == "clinic_image") {
                                    $scope.other.clinic_file = file_obj;
                                } else if (attr.obj == "clinic_outside") {
                                    $scope.other.clinic_outside_file = file_obj;
                                } else if (attr.obj == "clinic_waiting") {
                                    $scope.other.clinic_waiting_file = file_obj;
                                } else if (attr.obj == "clinic_reception") {
                                    $scope.other.clinic_reception_file = file_obj;
                                } else if (attr.obj == "edu") {
                                    $scope.doctor.edu_object[attr.key].img_file = file_obj;
                                    $scope.doctor.edu_object[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "reg") {
                                    $scope.doctor.registration_obj[attr.key].img_file = file_obj;
                                    $scope.doctor.registration_obj[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "sp") {
                                    $scope.doctor.specialization[attr.key].img_file = file_obj;
                                    $scope.doctor.specialization[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "award") {
                                    $scope.doctor.award_obj[attr.key].img_file = file_obj;
                                    $scope.doctor.award_obj[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "clinic_address_proof") {
                                    $scope.other.clinic_address_proof_file = file_obj;
                                }

                                $scope.file = file_obj;
                                $scope.getFile(attr.obj, $scope.file, attr.key);
                            }
                        })
                    }
                }

            }]);
