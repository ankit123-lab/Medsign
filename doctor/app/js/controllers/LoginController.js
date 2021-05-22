/*
 * Controller Name: LoginController
 * Use: This controller is used for login/register activity
 */
angular.module("app.login", [])
        .controller("LoginController", function ($scope, vcRecaptchaService, LoginService, ngToast, $location, AuthService, $state, CommonService, fileReader, ClinicService, UserService, RECAPTCHKEY, $localStorage, $rootScope, $timeout, $filter, SweetAlert, $interval) {
            $scope.is_show_captcha = false;
            $scope.response = null;
            $scope.widgetId = null;
            $scope.model = RECAPTCHKEY; //ReCaptch key
            $scope.password_type = "password";
			$scope.passwordShowHide = function () {
                if($scope.password_type == "password")
                    $scope.password_type = "text";
                else
                    $scope.password_type = "password";
            }
            $scope.setResponse = function (response) {
                //console.info('Response available');
                $scope.response = response;
            };
            $scope.setWidgetId = function (widgetId) {
                //console.info('Created widget ID: %s', widgetId);
                $scope.widgetId = widgetId;
            };

            $scope.cbExpiration = function() {
                //console.info('Captcha expired. Resetting response object');
                vcRecaptchaService.reload($scope.widgetId);
                $scope.response = null;
            };
			
            var tick = function () {
                if ($scope.resendWait <= $scope.currentTime) {
                    $scope.Tab.is_send_button_visible = true;
                    $scope.Tab.is_send_timer_visible = false;
                    return false;
                }
                $scope.resendWait -= 1000;
                $interval.cancel($scope.promise);
                $scope.promise = $interval(tick, 1000);
                $scope.Tab.is_send_timer_visible = true;
            };
            $scope.settick = function () {
                var date = new Date();
                date.setMinutes(0);
                date.setSeconds(0);
                date.setMilliseconds(0);
                $scope.currentTime = date.getTime();
                $scope.resendWait = $scope.currentTime + (60 * 1000);
            }
            $scope.myMax = -1;
            $scope.resendWait = 0;
            $scope.currentTime = 0;
            /* Tab constants */
            $scope.Tab = {
                is_register_open: false,
                is_login_open: true,
                is_login_with_email_open: true,
                is_login_with_otp: false,
                is_forgot_open: false,
                is_otp_verify_open: false,
                register_tab: 1,
                is_send_button_visible: false,
                is_send_timer_visible: true
            };
            $scope.Model = $scope.Model || {currentImg: ''};
            $scope.placeholder = 'Mobile Number / Email ID / Unique ID';
            $scope.button_text = 'Login';
            $scope.Tab.currentTab = $location.path();
            $scope.year_of_exp_open = true;
            /* Login params */
            $scope.login = {
                password: '',
                username: '',
                login_type: '',
                otp: '',
                phone_number: '',
                terms_condition : 0
            };
            $scope.progress = {
            };
            $scope.file = '';
            $scope.progress.width = '0';
            //state and country variable
            $scope.other = {};
            $scope.other.gender = 1;
            $scope.other.country = [];
            $scope.other.state = [];
            $scope.other.city = [];
            $scope.other.selected_country = '';
            $scope.other.selected_state = '';
            $scope.other.selected_city = '';
            $scope.other.selected_clinic_city = '';
            $scope.other.profile = '';
            $scope.other.speciality = [];
            $scope.other.edu_object = [
                {
                    edu_degree: '',
                    edu_college: '',
                    edu_year: '',
                    img_file_name: '',
                    img_file: '',
                    temp_img: ''
                }
            ];
            $scope.other.specialities_obj = [
                {
                    doctor_speciality_name: undefined,
                }
            ];
            $scope.other.registration_obj = [
                {
                    reg_detail: '',
                    reg_councel: '',
                    reg_year: '',
                    img_file: '',
                    temp_img: ''
                }
            ];
            $scope.other.specialization = [
                {
                    specialization_id: '',
                    img_file: '',
                    temp_img: ''
                }
            ];
            $scope.other.clinic_service = [
                {
                    text: ''
                }
            ];
            $scope.other.address = '';
            $scope.other.clinic_address = '';
            $scope.other.lat = undefined;
            $scope.other.lng = undefined;
            /* Login submit event */
            $scope.doLogin = function () {
                $scope.submitted = true;
                /* check form is valid */
                if ($scope.loginForm.$valid) {
                    $scope.submitted = false;
                    $scope.login.login_type = 2;
                    if ($scope.Tab.is_login_with_otp == true) {
                        $scope.login.login_type = 1;
                        $scope.login.password = '';
                    } else if ($scope.login.username.indexOf('@') > -1) {
                        $scope.login.login_type = 2;
                    } else if (/^\d+$/.test($scope.login.username)) {
                        $scope.login.login_type = 4;
                    } else {
                        $scope.login.login_type = 3;
                    }

                    /* Login api from login service */
                    LoginService.login($scope.login, function (response) {
                        $scope.submitted = false;
                        $scope.display_message = '';
                        if (response.status == true) {
                            if ($scope.login.login_type == 1 && response.authentication_flag == '1') {
                                $scope.display_message = response.message;
                                if (response.authentication_flag == '1') {
                                    SweetAlert.swal({
                                        title: response.message,
                                        text: "",
                                        type: "warning",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55",
                                        confirmButtonText: "Continue!",
                                        cancelButtonText: "Skip",
                                        closeOnConfirm: true
                                    },
                                            function (isConfirm) {
                                                if (isConfirm) {
                                                    $scope.login.username = '';
                                                    $scope.Tab.is_login_chk = false;
                                                    $scope.Tab.is_login_open = true;
                                                    $scope.Tab.is_login_with_otp = false;
                                                    $scope.myMax = -1;
                                                    $scope.placeholder = "Mobile Number / Email ID / Unique ID";
                                                    $scope.button_text = 'Login';
                                                    $scope.login.username = '';
                                                    $scope.login.password = '';
                                                    $scope.display_message = '';
                                                } else {
                                                    var phone_number = response.user_data.user_phone_number;
                                                    $scope.login.username = phone_number;
//                                                    var phone_number = $scope.login.username;
                                                    tick();
                                                    LoginService
                                                            .resendOtp(phone_number, function (response) {
                                                                SweetAlert.swal(response.message);
                                                                if (response.status) {
                                                                    $scope.Tab.is_otp_verify_open = true;
                                                                } else {
                                                                    ngToast.danger(response.message);
                                                                }
                                                            });
                                                }
                                            });
                                } else {
                                    $scope.Tab.is_otp_verify_open = true;
                                    $scope.Tab.is_login_open = false;
                                    tick();
                                }
                                ngToast.success({
                                    content: $scope.display_message,
                                    className: '',
                                    dismissOnTimeout: true,
                                    timeout: 5000
                                });
                            } else {
                                /* check two way authentication */
                                if (response.authentication_flag == '1' && response.is_number_verified == '1') {
                                    var phone_number = response.user_data.user_phone_number;
                                    $scope.login.username = phone_number;
                                    tick();
                                    LoginService
                                            .resendOtp(phone_number, function (response) {
                                                if (response.status) {
                                                    $scope.Tab.is_otp_verify_open = true;
                                                    ngToast.success({
                                                        content: response.message,
                                                        className: '',
                                                        dismissOnTimeout: true,
                                                        timeout: 5000
                                                    });
                                                } else {
                                                    ngToast.danger(response.message);
                                                }
                                            });

                                } else if (response.authentication_flag == '1') {
                                    tick();
                                    $scope.Tab.is_otp_verify_open = true;
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                } else {
									AuthService.login(response.user_data, response.access_token);
									if(response.user_data != undefined && response.user_data.advertisement_data != undefined){
										$rootScope.advertisement_data = response.user_data.advertisement_data;
										if(response.user_data.next_request_time != undefined)
											$rootScope.advertisement_next_request_time = response.user_data.next_request_time;
										$rootScope.setAdvertisementData();
									}
									$rootScope.current_doctor = '';
                                    if(response.user_data.user_type == 3 || response.user_data.user_type == 2){
                                        $timeout(function () {
                                            $rootScope.getSidebarMenu();
                                            $rootScope.app.is_not_valid = false;
                                            $rootScope.app.is_profile_complete = 1;
                                            $scope.getClinicService();
                                        }, 1000);
                                    }
                                    else{
                                        $timeout(function () {
											$rootScope.getSidebarMenu();
                                            UserService.getProfilePercentage(response.user_data.user_id, function (profileresponse) {
                                                if (profileresponse != 100) {
                                                    $rootScope.app.is_profile_complete = 2;
                                                    $rootScope.app.is_not_valid = true;
                                                    $state.go('app.complete_profile_view.personal');
                                                    $rootScope.error_message = $rootScope.incomplete_profile;
												} else {
                                                    $rootScope.app.is_profile_complete = 1;
                                                    $scope.getClinicService();
                                                }
                                            });
                                        }, 1000);
                                    }
                                }
                            }
                        } else {
                            if(response.status_code == 1001){ $scope.is_show_captcha = true; }
                            ngToast.danger(response.message);
                        }
                    });
                }
                else{
                    if($scope.is_show_captcha == true){
                        // vcRecaptchaService.reload($scope.widgetId);
                    }
                }
            };
            /* Change tab code for form visible hide */
            $scope.changeTab = function (type) {
				//$scope.login.username = '';
				//$scope.login.password = '';
                $scope.submitted = false;
                if (type == 2) {
                    $scope.Tab.is_register_open = true;
                    $scope.Tab.is_login_open = false;
                    $scope.Tab.is_forgot_open = false;
                } else if (type == 1) {
                    $scope.Tab.is_register_open = false;
                    $scope.Tab.is_login_open = true;
                    $scope.Tab.is_forgot_open = false;
                    $scope.Tab.is_otp_verify_open = false;
                    if (AuthService.isLoggedIn()) {
						$rootScope.getSidebarMenu();
                        UserService.getProfilePercentage($rootScope.currentUser.user_id, function (profileresponse) {
                            if (profileresponse != 100) {
                                $rootScope.app.is_not_valid = true;
                                $state.go('app.complete_profile_view.personal');
                                $rootScope.error_message = $rootScope.incomplete_profile;
                            } else {
                                $rootScope.app.is_profile_complete = 1;
                                $scope.getClinicService();
                            }
                        });
                    }
                } else if (type == 3) {
                    $scope.Tab.is_register_open = false;
                    $scope.Tab.is_login_open = false;
                    $scope.Tab.is_forgot_open = true;
                }
            }
            /* Check box event (login with otp) */
            $scope.loginWithOtpDiv = function () {
                if (!$scope.Tab.is_login_with_otp) {
                    $scope.myMax = 10;
                    $scope.placeholder = "Mobile Number";
                    $scope.button_text = "Send OTP";
                    $scope.login.username = '';
                    $scope.login.password = '';
                } else {
                    $scope.myMax = -1;
                    $scope.placeholder = "Mobile Number / Email ID / Unique ID";
                    $scope.button_text = 'Login';
                    $scope.login.username = '';
                    $scope.login.password = '';
                }
                $scope.Tab.is_login_with_otp = !($scope.Tab.is_login_with_otp);
            }
            /* Handle validation */
            $scope.handleValidation = (function () {
                var phone_regex = /^[0-9]{10}$/;
                var email_regex = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
                return {
                    test: function (value) {
                        if ($scope.Tab.is_login_with_otp) {
                            $scope.other.error = 2;
                            return phone_regex.test(value);
                        } else {
                            if (value.indexOf('@') > -1) {
                                $scope.other.error = 3;
                                return email_regex.test(value);
                            } else if (/^[0-9]*$/.test(value)) {
                                $scope.other.error = 2;
                                return phone_regex.test(value);
                            } else {
                                if (value.length != 8) {
                                    $scope.other.error = 4;
                                    return false;
                                }
                            }
                        }
                        /*  if (value.length < 6) {
                         $scope.other.error = 1;
                         return false;
                         } else if (value.indexOf('@') > -1) {
                         $scope.other.error = 3;
                         return email_regex.test(value);
                         } else if (value.length > 6) {
                         $scope.other.error = 2;
                         return phone_regex.test(value);
                         }
                         */
                        return true;
                    }
                };
            })();
			/* Handle validation */
            $scope.passwordValidation = (function () {
                var atleast_one_uppercase = /[A-Z]+/;
                var atleast_one_lowercase = /[a-z]+/;
                var atleast_one_special = /[!@#$%^&*()]+/;
                var atleast_one_digit = /[0-9]+/;
                return {
                    test: function (value) {
                        if (!atleast_one_uppercase.test(value)) {
                            $scope.other.password_error = $filter('translate')('PASSWORD_ERROR_UPPERCASE');
                            return atleast_one_uppercase.test(value);
                        }
                        if (!atleast_one_lowercase.test(value)) {
                            $scope.other.password_error = $filter('translate')('PASSWORD_ERROR_LOWERCASE');
                            return atleast_one_lowercase.test(value);
                        }
                        if (!atleast_one_special.test(value)) {
                            $scope.other.password_error = $filter('translate')('PASSWORD_ERROR_SPECIAL');
                            return atleast_one_special.test(value);
                        }
                        if (!atleast_one_digit.test(value)) {
                            $scope.other.password_error = $filter('translate')('PASSWORD_ERROR_DIGIT');
                            return atleast_one_digit.test(value);
                        }
                        return true;
                    }
                };
            })();
			/* Verify Otp code */
            $scope.checkOtp = function () {
                $scope.submitted = true;
                if ($scope.checkOtpForm.$valid) {
                    $scope.submitted = false;
                    $scope.login.phone_number = $scope.login.username;
                    LoginService
                            .checkOtp($scope.login, function (response) {
                                if (response.status == true) {
                                    $scope.changeRegisterTab(3, true);
                                    $scope.other.user_id = response.user_data.user_id;
                                    AuthService.login(response.user_data, response.access_token);
									if(response.user_data != undefined && response.user_data.advertisement_data != undefined){
										$rootScope.advertisement_data = response.user_data.advertisement_data;
										if(response.user_data.next_request_time != undefined)
											$rootScope.advertisement_next_request_time = response.user_data.next_request_time;
										$rootScope.setAdvertisementData();
									}
                                    $rootScope.current_doctor = '';
                                    if(response.user_data.user_type == 3 || response.user_data.user_type == 2){
                                        $timeout(function () {
                                            $rootScope.getSidebarMenu();
                                            $rootScope.app.is_not_valid = false;
                                            $rootScope.app.is_profile_complete = 1;
                                            $scope.getClinicService();
                                        }, 1000);
                                    }else{
										$timeout(function () {
											$rootScope.getSidebarMenu();
											UserService.getProfilePercentage(response.user_data.user_id, function (profileresponse) {
												if (profileresponse != 100) {
													$rootScope.app.is_profile_complete = 2;
													$rootScope.app.is_not_valid = true;
													$state.go('app.complete_profile_view.personal');
													$rootScope.error_message = $rootScope.incomplete_profile;
												} else {
													$rootScope.app.is_profile_complete = 1;
													$scope.getClinicService();
												}
											});
										}, 1000);
                                    }
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            };
            $scope.getClinicService = function () {
				ClinicService.getDoctorClinicsRole($rootScope.currentUser.user_id, function (response) {
                    if (!response.clinic_data[0]) {
                        $rootScope.app.is_clinic_complete = 2;
                        $rootScope.app.is_not_valid = true;
                        $rootScope.error_message = $rootScope.add_clinic_first;
                        $state.go('app.profile.clinic_view', {}, {reload: true});
                    } else if($rootScope.currentUser.user_status != undefined && $rootScope.currentUser.user_status == 3) {
						$rootScope.app.is_clinic_complete = 1;
						$rootScope.app.is_not_valid = true;
						$rootScope.error_message = $rootScope.account_pending_for_approval;
						$state.go('app.profile.my_profile_view');
					} else if(!$rootScope.currentUser.is_sub_active) {
                        $state.go('app.subscription');
                    } else {
                        $rootScope.app.is_clinic_complete = 1;
                        $rootScope.app.is_not_valid = false;
                        $state.go('app.dashboard.calendar');
                    }
                }, function (error) {
                    if (error.status == 403) {
                        $scope.getClinicService();
                    }
                });
            };
            /* Forgot password functionality */
            $scope.sendForgotPasswordMail = function () {
                $scope.submitted = true;
                if ($scope.forgotPasswordForm.$valid) {
                    LoginService
                            .forgotPassword($scope.login.username, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
            /* Change register tab */
            $scope.changeRegisterTab = function (type, flag) {
                $scope.login.otp = '';
                if ($scope.Tab.register_tab == 2 && type == 1) {
                } else if ($scope.Tab.register_tab == 4 && type == 3) {
                } else {
                    if (flag == false) {
                        return false;
                    }
                }
                $scope.submitted = false;
                if (type == 3 && $scope.Tab.register_tab != 4) {
                    //get country state city list
                    CommonService.getCountry('', function (response) {
                        if (response.status == true) {
                            $scope.other.country = response.data;
                            $scope.other.selected_country = $scope.other.country[0];
                            $scope.other.selected_clinic_country = $scope.other.country[0];
                        } else {
                            ngToast.danger(response.message);
                        }
                    });
                    $scope.getState('');
                    $scope.getQualification();
                    $scope.getSpecialization();
                    $scope.getCouncil();
                }
                $scope.Tab.register_tab = type;
                $scope.progress.width = (100 / 3) * (type - 1);
            }
            $scope.$watch(AuthService.isLoggedIn, function (isLoggedIn) {
                $scope.isLoggedIn = isLoggedIn;
                $scope.currentUser = AuthService.currentUser();
            });
            /* Register portion start */
            $scope.registerSendOtp = function () {
                $scope.submitted = true;
                $scope.login.temp_user_id = '';
                if ($scope.registerSendOtpForm.$valid) {
                    LoginService
                            .register($scope.login, function (response) {
                                if (response.user_data != undefined) {
                                    $scope.login.temp_user_id = response.user_data.temp_user_id;
                                }
                                if (response.status == true) {
                                    $scope.changeRegisterTab(2, true);
                                    $scope.Tab.is_send_button_visible = false;
                                    $scope.settick();
                                    tick();
                                    $scope.Tab.is_send_timer_visible = true;
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                } else {
									//ngToast.danger(response.message);
                                    SweetAlert.swal(response.message);
                                }
                            });
                } else {

                }
            }

            $scope.checkOtpRegister = function () {
                $scope.submitted = true;
                if ($scope.registerVerifyOtpForm.$valid) {
                    $scope.submitted = false;

                    LoginService
                            .verifyRegisterOtp($scope.login, function (response) {

                                if (response.status == true) {
                                    $scope.other.user_id = response.user_data.user_id;
                                    $scope.changeRegisterTab(3, true);
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    AuthService
                                            .login(response.user_data, response.access_token);
                                    $rootScope.current_doctor = '';
									//$rootScope.getSidebarMenu();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }

            /* Resend otp code */
            $scope.resend = function () {
                LoginService
                        .resendOtp($scope.login.username, function (response) {
                            if (response.status == true) {
                                $scope.Tab.is_send_button_visible = false;
                                $scope.settick();
                                $scope.Tab.is_send_timer_visible = true;

                                ngToast.success({
                                    content: response.message,
                                    className: '',
                                    dismissOnTimeout: true,
                                    timeout: 5000
                                });
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
            }
            
			/* Resend register otp code */
            $scope.registerResend = function () {
                LoginService
                        .registerResendOtp($scope.login.temp_user_id, function (response) {
                            if (response.status == true) {
                                $scope.Tab.is_send_button_visible = false;
                                $scope.settick();
                                $scope.Tab.is_send_timer_visible = true;
                                ngToast.success({
                                    content: response.message,
                                    className: '',
                                    dismissOnTimeout: true,
                                    timeout: 5000
                                });
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
            }

            /* Change order code */
            $scope.changeGender = function (gender) {
                $scope.other.gender = gender;
            }
            $scope.getCountry = function () {
                CommonService.getCountry('', function (response) {
                    if (response.status == true) {
                        $scope.other.country = response.data;
                        $scope.other.selected_country = $scope.other.country[0];
						//$scope.other.state = [];
                        $scope.other.city = [];
                    }
                });
            }

            $scope.getState = function (country) {
                $scope.other.selected_state = '';
                $scope.other.selected_city = '';
                $scope.other.selected_clinic_state = '';
                $scope.other.selected_clinic_city = '';
				CommonService.getState(country, false, function (response) {
					if (response.status == true) {
						$scope.other.state = response.data;
						$rootScope.states  = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
            }

            $scope.getCity = function (state_id) {
                $scope.other.selected_city = '';
                $scope.other.selected_clinic_city = '';
                CommonService.getCity(state_id, false, function (response) {
                    if (response.status == true) {
                        $scope.other.city = response.data;
                    } else {
                        ngToast.danger(response.message);
                    }
                });
            }
			
            $scope.getCountryForClinic = function () {
                CommonService.getCountry('', function (response) {

                    if (response.status == true) {
                        $scope.other.country = response.data;
                        $scope.other.city = [];
                        CommonService.getState($scope.other.selected_clinic_country, true, function (response) {

                            if (response.status == true) {
                                $scope.other.state = response.data;
                                CommonService.getCity($scope.other.selected_clinic_state, true, function (response) {

                                    if (response.status == true) {
                                        $scope.other.city = response.data;
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                });
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                    }

                });
            }
            var ctrl = this;
            ctrl.client = {name: '', id: ''};
            /* 
				$scope.dataSource = [{name: 'Oscar', id: 1000}, {name: 'Olgina', id: 2000}, {name: 'Oliver', id: 3000}, {name: 'Orlando', id: 4000}, {name: 'Osark', id: 5000}, {name: 'Osos', id: 5000}, {name: 'Oscarlos', id: 5000}];
			*/
            $scope.setClientData = function (item) {
                if (item) {
                    ctrl.client = item;
                }
            }
			
            $scope.getQualification = function () {
                CommonService.getQualification('', function (response) {
                    if (response.status == true) {
                        $scope.other.qualification = response.data;
                    }
                });
                CommonService.getColleges('', function (response) {
                    if (response.status == true) {
                        $scope.other.colleges = response.data;
						$rootScope.colleges = response.data;
                    }
                });
                CommonService.getLanguages('', function (response) {
                    if (response.status == true) {
                        $scope.other.languages = response.data;
                        $rootScope.languages = response.data;
                    }
                });
            }

            //sepcialisation code
            $scope.other.temp_specializations = [];
            $scope.getSpecialization = function () {
                CommonService.getSpecializationNew('', function (response) {
                    if (response.status == true) {
                        $scope.other.specializations = response.data;
                    }
                });
            }

            CommonService.getSpecializationNew({
                parent_id: '',
                flag: '1',
                search_specialization: ''
            }, function (response) {
                if (response.status == true) {
                    $scope.other.temp_specializations = [];
                    angular.forEach(response.data, function (value, key) {
                        this.push({"specialization_id": key, "specialization_title": value.specialization_title});
                    }, $scope.other.temp_specializations);

                }
            });
            $scope.other.speciality = [];

            $scope.getChildSpeciality = function (parent_id, form, old_id) {
                if (parent_id != undefined) {
                    CommonService.getSpecializationNew({
                        parent_id: parent_id,
                        flag: '',
                        search_specialization: ''
                    }, function (response) {
                        if (response.status == true) {
                            if (!$scope.other.speciality) {
                                $scope.other.speciality = [];
                            }
                            angular
                                    .forEach(response.data, function (value) {
                                        $scope.other.speciality.push(value.specialization_title);
                                        if (form) {
                                            form.speciality.$setValidity("required", true);
                                        }
                                    });
                            if (old_id == '') {
                                $scope.addEduObject(3);
                            }

                        }
                    });
                }

            }

            //sepcialisation code
            $scope.getCouncil = function () {
                CommonService.getCouncil('', function (response) {
                    if (response.status == true) {
                        $scope.other.councils = response.data;
                    }
                });
            }

            $scope.addEduObj = function () {
                var is_add = false;
                if ($scope.other.edu_object.length > 0) {
                    angular.forEach($scope.other.edu_object, function (value, key) {
                        if (value.edu_college == '' && value.edu_degree == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(1);
                    }
                }
            }

            $scope.addSpecialityObj = function () {
                var is_add = false;
                if ($scope.other.specialities_obj.length > 0) {
                    angular.forEach($scope.other.specialities_obj, function (value, key) {
                        if (value.doctor_speciality_name == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.addEduObject(5);
                    }
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

            $scope.isEduObjRequired = function (eduObj, key) {
                if (eduObj.length == 1)
                    return true;

                if (!eduObj[key].edu_degree)
                    return false;
                else
                    return true;
            }
            // add register document on on blur event on textbox
            $scope.addRegDocmentObj = function () {
                var is_add = false;
                if ($scope.other.registration_obj.length > 0) {
                    angular.forEach($scope.other.registration_obj, function (value, key) {
                        if (value.reg_detail == '' && value.reg_councel == '') {
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
                if (regDocumentObj.length == 1)
                    return true;

                if (!regDocumentObj[key].reg_detail)
                    return false;
                else
                    return true;
            }
            $scope.isSpecializationObjRequired = function (SpecializationObj, key) {
                if (SpecializationObj.length == 1)
                    return true;

                if (!SpecializationObj[key].specialization_id)
                    return false;
                else
                    return true;
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
            //dynamic html code
            $scope.addEduObject = function (type) {
                if (type == 1) {
                    $scope.other.edu_object.push({
                        edu_degree: '',
                        edu_college: '',
                        edu_year: '',
                        img_file_name: '',
                        img_file: '',
                        temp_img: ''
                    });
                    /* flag = true;
                     angular
                     .forEach($scope.other.edu_object, function (value) {
                     
                     if (value.edu_degree == '' || value.edu_degree == undefined ||
                     value.edu_college == '' || value.edu_college == undefined ||
                     value.edu_year == '' || value.edu_year == undefined ||
                     value.img_file == '' || value.img_file == undefined
                     ) {
                     
                     flag = false;
                     return;
                     }
                     });
                     if (flag) {
                     }*/
                } else if (type == 2) {
                    $scope.other.registration_obj.push({
                        reg_detail: '',
                        reg_councel: '',
                        reg_year: '',
                        img_file: '',
                        temp_img: ''
                    });
                } else if (type == 3) {
                    $scope.other.specialization.push({
                        specialization_id: '',
                        img_file: '',
                        temp_img: ''
                    });
                } else if (type == 5) {
                    $scope.other.specialities_obj.push({
                        doctor_speciality_name: undefined
                    });
                }
            }
			//dynamic html code
            $scope.removeEduObject = function (type, index) {
                if (type == 1) {
                    $scope.other.edu_object.splice(index, 1);
                } else if (type == 2) {
                    $scope.other.registration_obj.splice(index, 1);
                } else if (type == 3) {
                    $scope.other.specialization.splice(index, 1);
                } else if (type == 5) {
                    $scope.other.specialities_obj.splice(index, 1);
                }
            }
            $scope.removeCurrentImage = function (type, index) {
                if (type == 1) {
                    $scope.other.edu_object[index].temp_img = '';
                } else if (type == 2) {
                    $scope.other.registration_obj[index].temp_img = '';
                } else if (type == 3) {
                    $scope.other.specialization[index].temp_img = '';
                }
            }
            $scope.removeClinicImage = function (type) {
                if (type == 1) {
                    $scope.clinicOutsideImageSrc = '';
                } else if (type == 2) {
                    $scope.clinicWaitingImageSrc = '';
                } else if (type == 3) {
                    $scope.clinicReceptionImageSrc = '';
                } else if (type == 4) {
                    $scope.clinicAddressImageSrc = '';
                } else if (type == 5) {
                    $scope.clinicImageSrc = '';
                }
            }
            $scope.$on('gmPlacesAutocomplete::placeChanged', function () {
                $scope.other.is_copied = false;
                if ($scope.other.address != '' && $scope.other.address.getPlace() != undefined) {
                    var location = $scope.other.address.getPlace().geometry.location;
                    $scope.other.lat = location.lat();
                    $scope.other.lng = location.lng();
                }

                if ($scope.other.clinic_address != '' && $scope.other.clinic_address.getPlace() != undefined) {
                    var location = $scope.other.clinic_address.getPlace().geometry.location;
                    $scope.other.cliniclat = location.lat();
                    $scope.other.cliniclng = location.lng();
                }
                $scope.$apply();
            });
            $scope.blankLatLong = function () {
                $scope.other.lat = undefined;
                $scope.other.lng = undefined;
                $scope.other.cliniclat = undefined;
                $scope.other.cliniclng = undefined;
            }

            $scope.registerPersonalDetail = function () {
                $scope.submitted = true;
				
                /* if (angular.isObject($scope.other.address) && $scope.other.address.getPlace() == undefined) {
                    $scope.registerPersonalForm.user_address.$setValidity("required", false);
                } */

                if ($scope.registerPersonalForm.$valid) {
					$scope.other.specialization.splice($scope.other.specialization.length - 1, 1);
                    $scope.other.edu_object.splice($scope.other.edu_object.length - 1, 1);
                    $scope.other.registration_obj.splice($scope.other.registration_obj.length - 1, 1);
					if($scope.other != undefined && $scope.other.year_of_exp != undefined && $scope.other.year_of_exp != ''){
						var month = $scope.other.year_of_exp.getMonth() + 1;
						var day   = $scope.other.year_of_exp.getDate();
						var year  = $scope.other.year_of_exp.getFullYear();
						$scope.other.real_year_of_exp = year + "-" + ('0' + month).slice('-2clinicOutsideImageSrc') + "-" + ('0' + day).slice('-2');
                    }else{
						$scope.other.real_year_of_exp = '';
					}
					if ($scope.other.lat == undefined || $scope.other.lng == undefined) {
                        $scope.other.lat = '';
						$scope.other.lng = '';
						//ngToast.danger("Please enter valid address");
                        //return false;
                    }
                    if ($scope.imageSrc == undefined) {
                        $scope.imageSrc = '';
						//ngToast.danger("Please Select image");
                        //return false;
                    }
                    $scope.submitted = false;
                    $scope.other.email = $scope.login.username;
					
                    //language string
                    $scope.other.language_string = '';
					if($scope.other.language != undefined && $scope.other.language != ''){
						for (var i = 0; i < $scope.other.language.length; i++) {
							$scope.other.language_string += $scope.other.language[i].language_id;
							if (i != ($scope.other.language.length - 1)) {
								$scope.other.language_string += ',';
							}
						}
					}
					
                    $scope.other.specialitity_string = '';
                    for (var i = 0; i < $scope.other.specialities_obj.length; i++) {
                        if($scope.other.specialities_obj[i].doctor_speciality_name !=undefined && $scope.other.specialities_obj[i].doctor_speciality_name !="")
                            $scope.other.specialitity_string += $scope.other.specialities_obj[i].doctor_speciality_name;
                        if ($scope.other.specialities_obj[i+1] != undefined && $scope.other.specialities_obj[i+1].doctor_speciality_name != undefined && $scope.other.specialities_obj[i+1].doctor_speciality_name != '') {
                            $scope.other.specialitity_string += ',';
                        }
                    }
                    $rootScope.app.isLoader = true;
                    LoginService
                            .updateUserDetail($scope.other, function (response) {
                                if (response.status == true) {
                                    $localStorage.currentUser.user_first_name    = $scope.other.first_name;
                                    $localStorage.currentUser.user_last_name     = $scope.other.last_name;
                                    $localStorage.currentUser.address_country_id = ($scope.other.selected_country != undefined) ? $scope.other.selected_country.country_id : '';
                                    $localStorage.currentUser.address_city_id    = ($scope.other.selected_city != undefined && $scope.other.selected_city.city_id != undefined) ? $scope.other.selected_city.city_id : '';
                                    $localStorage.currentUser.address_state_id   = ($scope.other.selected_state != undefined && $scope.other.selected_state.state_id != undefined) ? $scope.other.selected_state.state_id : '';
                                    $localStorage.currentUser.address_latitude   = ($scope.other.lat != undefined) ? $scope.other.lat : '';
                                    $localStorage.currentUser.address_longitude  = ($scope.other.lng != undefined) ? $scope.other.lng : '';
                                    $localStorage.currentUser.address_pincode    = ($scope.other.zipcode !=  undefined) ? $scope.other.zipcode : '';
                                    $localStorage.currentUser.address_locality   = ($scope.other.locality != undefined) ? $scope.other.locality : '';
                                    var address_name = '';
                                    if($scope.other.address != undefined && angular.isObject($scope.other.address) && $scope.other.address.getPlace() != undefined) {
                                        address_name = $scope.other.address.getPlace().formatted_address;
                                    } else {
                                        address_name = ($scope.other.address != undefined) ? $scope.other.address : '';
                                    }
                                    $localStorage.currentUser.address_name       = address_name;
                                    $localStorage.currentUser.address1           = ($scope.other.address1!= undefined) ? $scope.other.address1 : '';
                                    //upload image code
                                    UserService.uploadUserImage($scope.other, function (response) {
                                                if (response.status)
                                                    $localStorage.currentUser.user_photo_filepath = (response.user_data != undefined && response.user_data.user_photo_filepath != undefined) ? response.user_data.user_photo_filepath : '';
                                            });
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.changeRegisterTab(4, true);
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                                $rootScope.app.isLoader = false;
                            });
                } else {
				   
                   SweetAlert.swal($rootScope.app.common_error);
                }
            };

            $scope.removeProfImage = function () {
                $scope.imageSrc = "";
            };

            $scope.getFileUser = function (file_type, file_obj, key) {
                fileReader.readAsDataUrl(file_obj, $scope)
                        .then(function (result) {
                            if (file_type == "profile") {
                                $scope.imageSrc = result;
                            } else if (file_type == "clinic_image") {
                                $scope.clinicImageSrc = result;
                            } else if (file_type == "clinic_outside") {
                                $scope.clinicOutsideImageSrc = result;
                            } else if (file_type == "clinic_waiting") {
                                $scope.clinicWaitingImageSrc = result;
                            } else if (file_type == "clinic_reception") {
                                $scope.clinicReceptionImageSrc = result;
                            } else if (file_type == "edu") {
                                $scope.other.edu_object[key].temp_img = result;
                            } else if (file_type == "reg") {
                                $scope.other.registration_obj[key].temp_img = result;
                            } else if (file_type == "sp") {
                                $scope.other.specialization[key].temp_img = result;
                            } else if (file_type == "clinic_address_proof") {
                                $scope.clinicAddressImageSrc = result;
                            }
                        });
            };
            $scope.$on("fileProgress", function (e, progress) {
                $scope.progress = progress.loaded / progress.total;
            });
            $scope.openFile = function (fileObj) {
                setTimeout(function () {
                    document.getElementById(fileObj).click();
                }, 0);
            };
            $scope.openFileObject = function (fileObj, key) {
                setTimeout(function () {
                    document.getElementById(fileObj + key).click();
                }, 0);
            };
            //dynamic html code
            $scope.addClinicService = function (type) {
                if (type == 1) {
                    $scope.other.clinic_service.push({
                        text: ''
                    });
                } else if (type == 2) {
                    $scope.other.registration_obj.push({
                        reg_detail: '',
                        reg_councel: '',
                        reg_year: ''
                    });
                } else if (type == 3) {
                    $scope.other.specialization.push({
                        sp_text: ''
                    });
                }
            }
            //dynamic html code
            $scope.removeEduObject = function (type, index) {
                if (type == 1) {
                    $scope.other.edu_object.splice(index, 1);
                } else if (type == 2) {
                    $scope.other.registration_obj.splice(index, 1);
                } else if (type == 3) {
                    $scope.other.specialization.splice(index, 1);
                } else if (type == 4) {
                    $scope.other.clinic_service.splice(index, 1);
                } else if (type == 5) {
                    $scope.other.specialities_obj.splice(index, 1);
                }
            }

            // time picker
            $scope.durationPicker = {
                date: '',
                timepickerOptions: {
                    readonlyInput: true,
                    showMeridian: false,
                    minuteStep: 5,
                },
                buttonBar: {
                    show: false,
                    now: {
                        show: false,
                    },
                    clear: {
                        show: true,
                        text: 'Clear'
                    }
                },
                open: false
            };
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
            $scope.year_of_exp_datepicker = {
                year_of_exp: new Date(),
                datepickerOptions: {
                    maxDate: new Date(),
                    mode: 'month',
                    minMode: 'month',
                    maxMode: 'year',
                }

            };
            $scope.openCalendar = function (e, picker) {
                $scope.durationPicker.open = true;
            };
            $scope.openEduCalendar = function ($event, picker) {
                picker.open = true;
            };
            $scope.hideSecondSession = function () {
                $scope.secondsession = false;
                $scope.other.clinic_start_time2 = null;
                $scope.other.clinic_end_time2 = null;
            };

            $scope.setPickerTime = [];
            $scope.openStartPicker = function ($event, index) {
                $scope.setPickerTime.length = 0;
                $scope.setPickerTime[index] = {'opened': true};
                var round_date = new Date();
                var minutes = round_date.getMinutes();
                var hours = round_date.getHours();
                var round_minutes = (parseInt((minutes + 7.5) / 15) * 15) % 60;
                var round_hours = minutes > 52 ? (hours === 23 ? 0 : ++hours) : hours;
                round_date.setHours(round_hours);
                round_date.setMinutes(round_minutes);
                if (index == 0) {
                    $scope.other.clinic_start_time = round_date;
                } else if (index == 1) {
                    $scope.other.clinic_end_time = round_date;
                } else if (index == 2) {
                    $scope.other.clinic_start_time2 = round_date;
                } else if (index == 3) {
                    $scope.other.clinic_end_time2 = round_date;
                }
            };

            $scope.addClinic = function (isFromClinic) {
					
				if (angular.isObject($scope.other.clinic_address) && $scope.other.clinic_address.getPlace() == undefined) {
                    $scope.clinicFormAddressRegister.clinic_address.$setValidity("required", false);
                }
				
                $scope.submitted = true;
                if ($scope.clinicFormAddressRegister.$valid) {
                    $scope.other.clinic_service.splice($scope.other.clinic_service.length - 1, 1);
                    if ($scope.compareTime($scope.other.clinic_start_time, $scope.other.clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                    if ($scope.other.clinic_start_time2 != '' && $scope.other.clinic_start_time2 != undefined) {
                        if ($scope.compareTime($scope.other.clinic_start_time2, $scope.other.clinic_end_time2) == false) {
                            ngToast.danger("Clinic start time should be smaller");
                            return false;
                        }
                        if ($scope.compareTime($scope.other.clinic_end_time, $scope.other.clinic_start_time2) == false) {
                            ngToast.danger("Please enter valid session timing");
                            return false;
                        }
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


                    if (angular.isDate($scope.other.clinic_start_time))
                        $scope.other.clinic_start_time = $scope.addZero($scope.other.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time.getMinutes());

                    if (angular.isDate($scope.other.clinic_start_time2))
                        $scope.other.clinic_start_time2 = $scope.addZero($scope.other.clinic_start_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_start_time2.getMinutes());

                    if (angular.isDate($scope.other.clinic_end_time))
                        $scope.other.clinic_end_time = $scope.addZero($scope.other.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time.getMinutes());

                    if (angular.isDate($scope.other.clinic_end_time2))
                        $scope.other.clinic_end_time2 = $scope.addZero($scope.other.clinic_end_time2.getHours()) + ":" + $scope.addZero($scope.other.clinic_end_time2.getMinutes());

                    if ($scope.other.clinic_email == undefined) {
                        $scope.other.clinic_email = "";
                    }
                    $scope.submitted = false;
						//var string = $scope.other.clinic_duration.split(':');
						//var hours = string[0];
						//var mints = (Number(hours) * 60) + Number(string[1]);
                    $scope.other.duration_mint = $scope.other.clinic_duration;
                    $rootScope.app.isLoader = true;
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
                                    if (isFromClinic) {
                                        $("#add_clinic_modal").hide();
                                        $state.go('app.profile.clinic_view', {}, {reload: true});
                                    } else {
                                        $rootScope.getSidebarMenu();
                                        $state.go('app.dashboard.calendar');
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $rootScope.app.isLoader = false;
                                $scope.submitted = false;
                            });
                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            }

            $scope.copyDoctorAddress = function () {
				if($scope.other.address != undefined && angular.isObject($scope.other.address) && $scope.other.address.getPlace() != undefined){
					$scope.other.clinic_address = $scope.other.address.getPlace().formatted_address;
					$scope.other.is_copied = true;
				}
                $scope.other.cliniclat 				 = ($scope.other.lat != undefined) ? $scope.other.lat : '';
                $scope.other.cliniclng 				 = ($scope.other.lng != undefined) ? $scope.other.lng : '';
                $scope.other.selected_clinic_country = ($scope.other.selected_country != undefined) ? $scope.other.selected_country : '';
                $scope.other.selected_clinic_state 	 = ($scope.other.selected_state != undefined) ? $scope.other.selected_state : '';
                $scope.other.selected_clinic_city    = ($scope.other.selected_city != undefined) ? $scope.other.selected_city : '';
                $scope.other.clinic_locality 		 = ($scope.other.locality != undefined) ? $scope.other.locality : '';
                $scope.other.clinic_zipcode 		 = ($scope.other.zipcode != undefined) ? $scope.other.zipcode : '';
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
					//timefrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
                timefrom.setHours((parseInt(temp[0]) + 24) % 24);
                timefrom.setMinutes(parseInt(temp[1]));
                var timeto = new Date();
                temp = end.split(":");
					//timeto.setHours((parseInt(temp[0]) - 1 + 24) % 24);
                timeto.setHours((parseInt(temp[0]) + 24) % 24);
                timeto.setMinutes(parseInt(temp[1]));

                if (timeto <= timefrom)
                    return false;
                return true;
            }
            $scope.createOption = function (term) {
                $scope.$apply(function () {
                    $scope.other.temp_specializations.push({
                        specialization_title: term
                    });
                });
                $timeout(function () {
                    return $scope.$apply(function () {
                        $scope.other.speciality.push($scope.other.temp_specializations[$scope.other.temp_specializations.length - 1].specialization_title);
                    });
                }, 100);
            }
            $scope.showFullImage = function (img_path) {
                $scope.Model.currentImg = img_path;
                $("#fullscreen_login_img_modal").show();
            }
            $scope.closeFullImgModal = function () {
                $("#fullscreen_login_img_modal").hide();
            }

            $scope.checkMobileKey = function (event) {
                if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
                    event.preventDefault();
                    return false;
                }
            }
            $scope.checkMobileKeyLogin = function (event) {
                if ($scope.Tab.is_login_with_otp) {
                    if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
                        event.preventDefault();
                        return false;
                    }
                }
            }
            $scope.checkSessionTiming = function () {
                if ($scope.other.clinic_start_time && $scope.other.clinic_end_time) {
                    if ($scope.compareTime($scope.other.clinic_start_time, $scope.other.clinic_end_time) == false) {
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
                /* logic to check duration is valid or not */
                if ($scope.other.clinic_start_time && $scope.other.clinic_end_time) {

                    var minutes = $scope.getDurationIntoMin($scope.other.clinic_start_time, $scope.other.clinic_end_time);
                    //now check another session timing
                    if ($scope.other.clinic_start_time2 && $scope.other.clinic_end_time2) {
                        minutes += $scope.getDurationIntoMin($scope.other.clinic_start_time2, $scope.other.clinic_end_time2);
                    }
                    if ($scope.other.clinic_duration) {
                        if (minutes < $scope.other.clinic_duration) {
                            ngToast.danger("Please select valid duration");
                            return false;
                        }
                    }
                }
            }
            $scope.addZero = function (i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
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

            $scope.getStaticPage = function () {
                $rootScope.sidebarMenu = [];
                $scope.data = [];
                CommonService.getStaticPage($scope.data, function (response) {});
            };
        });
/* Preview img code */
angular.module("app.login")
        .directive("ngFileSelectProfile", ['ngToast', function (ngToast) {
                return {
                    link: function ($scope, el, attr) {
                        el.bind("change", function (e) {
                            var file_obj = (e.srcElement || e.target).files[0];
                            var file_type = file_obj.name;
                            if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {

                                if (attr.obj == "profile") {
                                    $scope.other.profile_file = file_obj;
                                } else if (attr.obj == "clinic_image") {
                                    $scope.other.clinic_file = file_obj;
                                } else if (attr.obj == "clinic_outside") {
                                    $scope.other.clinic_outside_file = file_obj;
                                } else if (attr.obj == "clinic_waiting") {
                                    $scope.other.clinic_waiting_file = file_obj;
                                } else if (attr.obj == "clinic_reception") {
                                    $scope.other.clinic_reception_file = file_obj;
                                } else if (attr.obj == "edu") {
                                    $scope.other.edu_object[attr.key].img_file = file_obj;
                                    $scope.other.edu_object[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "reg") {
                                    $scope.other.registration_obj[attr.key].img_file = file_obj;
                                    $scope.other.registration_obj[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "sp") {
                                    $scope.other.specialization[attr.key].img_file = file_obj;
                                    $scope.other.specialization[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "clinic_address_proof") {
                                    $scope.other.clinic_address_proof_file = file_obj;
                                }
                                $scope.file = file_obj;
                                $scope.getFileUser(attr.obj, $scope.file, attr.key);
                            }
                        })
                    }
                }
            }]);