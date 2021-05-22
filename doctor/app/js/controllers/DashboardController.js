/*
 * Controller Name: DashboardController
 * Use: This controller is used for dashboard activity
 */
angular.module("app.dashboard")
        .controller("DashboardController", function ($scope, AuthService, LoginService, $timeout, $state, CommonService, $rootScope, ngToast, ClinicService, PatientService, $location, UserService, uiCalendarConfig, CalenderService, $filter,
                SettingService, minutesToHourFilter, SweetAlert, fileReader, $interval, SMOKE, ALCOHOL,$uibModal, $log) {
            
            var today_date = new Date();
            var year = today_date.getFullYear();
            var month = today_date.getMonth();
            var day = today_date.getDate();
            var current_date = new Date(year, month, day);

			$scope.block_calender_data = [];
			$rootScope.calOverlayLoader = true;
			$rootScope.flg_to_force_calendar_refresh = false;
            $scope.isOTPScreenOn = false;
            $scope.add_patient_popup = function() {
                $scope.patient_data = {user_id: ''};
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'app/views/patient/modal/add_patient_modal.html?' + $rootScope.getVer(2),
                    controller: 'ModalAddPatientCtrl',
                    size: 'lg',
                    backdrop: 'static',
                    keyboard: false,
                    resolve: {
                        items: function () {
                            return $scope.patient_data;
                        }
                    }
                });
            }
            $scope.$on('editPatientData', function (e,patient_id) {
                $scope.edit_patient_detail_popup(patient_id);
            });
            
            $scope.edit_patient_detail_popup = function(patient_id) {
                var request = {
                        user_id: $rootScope.currentUser.user_id,
                        patient_id: patient_id,
                        access_token: $rootScope.currentUser.access_token
                    };
                PatientService
                    .getPatientUserDetails(request, function (response) {
                        if (response.status == true) {
                            $scope.patient_data = response.data;
                            var modalInstance = $uibModal.open({
                                    animation: true,
                                    templateUrl: 'app/views/patient/modal/add_patient_modal.html?' + $rootScope.getVer(2),
                                    controller: 'ModalAddPatientCtrl',
                                    size: 'lg',
                                    backdrop: 'static',
                                    keyboard: false,
                                    resolve: {
                                        items: function () {
                                            return $scope.patient_data;
                                        }
                                    }
                                });
                            modalInstance.result.then(function (patient_data) {
                                $scope.$broadcast('updatePatientData', patient_data);
                            }, function () {
                              
                            });
                        }
                    });
            }
            //set video src
            $rootScope.help_video_url = 'app/videos/video.mp4';
            $rootScope.showHelpVideos = function(id) {
                $rootScope.isShowHelpVideo = true;
                var selectVideoObj = $filter('filter')($rootScope.videos_list, {'me_video_id':id},true);
                $rootScope.video_title = selectVideoObj[0].me_video_title;
                $rootScope.help_video_url = $rootScope.app.apiUrl + '/help/av/' + selectVideoObj[0].key;
                // $(".help-video-body video")[0].load();
            };
            $rootScope.backToVideoList = function() {
                $rootScope.isShowHelpVideo = false;
            };
            $scope.startPickerInit = function () {
                $scope.startPicker = {
                    timepickerOptions: {
                        readonlyInput: false,
                        showMeridian: ($rootScope.currentUser.hour_format == '1') ? true : false,
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
            };
			$scope.startPickerInit();
            $scope.init = function () {
                $rootScope.local_support = {};
                $rootScope.global_support = {};
                $scope.submitted = '';
                $scope.issue = {};
                $scope.issue.issue_text = '';
                $scope.issue.issue_email = '';
				if ($rootScope.currentUser == undefined) {
                    $rootScope.currentUser = AuthService.currentUser();
                }
                $rootScope.clinic_data = [];
                $rootScope.clinic_availability_data = [];
				$scope.search_patient_keyword = '';
                $scope.search_result = [];
                $scope.date_filter = {
                    start: $filter('date')(new Date(), "yyyy-MM-dd"),
                    end:   $filter('date')(new Date(), "yyyy-MM-dd")
                };
                $timeout(function () {
                    $scope.appointment = {
                        doctor_name: $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name,
                        appointment_date: current_date,
                        appointment_type: "1",
                        duration: ''
                    };
                }, 1000);
                $scope.other = {};
                $scope.other.availibility_slots = [];
            };
            /*
            $rootScope.$watch(AuthService.currentUser, function (currentUser) {
                $rootScope.currentUser = currentUser;
                if (currentUser.doctor_detail_is_term_accepted != 1)
                {
                    $rootScope.getGlobalSettings();
                    $timeout(function () {
                        $("#term_condition_modal").modal("show");
                    }, 500);
                }
            });
            */
			
            var appointment_maxdate = new Date();
            var year = appointment_maxdate.getFullYear();
            var month = appointment_maxdate.getMonth();
            var day = appointment_maxdate.getDate();
            var appointment_maxdate = new Date(year, month + 6, day)
            var appointment_mindate = new Date(year, month, day);
            $scope.appointment_picker = {
                datepickerOptions: {
                    minDate: appointment_mindate,
                    maxDate: appointment_maxdate
                }
            };
			$scope.startEventRendaring = false;
            $scope.events = [];
            $scope.init();
            $scope.event_data = [];
            $scope.getClinics = function () {
				
                $timeout(function () {
                    if ($rootScope.currentUser != undefined) {
                        if ($rootScope.clinic_data.length <= 0) {
                            ClinicService
                                    .getDoctorClinicsRole($rootScope.currentUser.user_id, function (response) {
                                        if (response.status == true) {
                                            $rootScope.clinic_data = response.clinic_data;
                                            $rootScope.clinic_availability_data = response.clinic_availability;
                                            if (response.clinic_data[0]) {
                                                if($rootScope.current_clinic != undefined && $rootScope.current_clinic.clinic_id != undefined) {
                                                    $rootScope.current_clinic.clinic_id
                                                    var currentClinicDataObj = $filter('filter')(response.clinic_data, { 'clinic_id' : $rootScope.current_clinic.clinic_id}, true);
                                                    if(currentClinicDataObj.length > 0) {
                                                        $rootScope.current_clinic = currentClinicDataObj[0];
                                                        $scope.appointment.duration = currentClinicDataObj[0].doctor_clinic_mapping_duration;
                                                    } else {
                                                        $rootScope.current_clinic = response.clinic_data[0];
                                                        $scope.appointment.duration = response.clinic_data[0].doctor_clinic_mapping_duration;
                                                    }
                                                } else {
                                                    $rootScope.current_clinic = response.clinic_data[0];
    												$scope.appointment.duration = response.clinic_data[0].doctor_clinic_mapping_duration;
                                                }
                                                $rootScope.calendarMinTime = response.other_data.minTime;
                                                $rootScope.calendarMaxTime = response.other_data.maxTime;
                                                $rootScope.calendarMinDuration = response.other_data.minDuration;
                                                $rootScope.timeSlots = response.other_data.timeSlots;
												$rootScope.current_clinic_availability = response.clinic_availability[$rootScope.current_clinic.clinic_id];
                                                
												/* get doctorlist */
												var set_calender_availability = true;
                                                $scope.getDoctorList(set_calender_availability);
												
												/* [THIS CODE MOVED TO THE getDoctorList]
												$scope.setCalendar();
												var request = {
                                                    clinic_id: $rootScope.current_clinic.clinic_id,
                                                    date: $filter('date')(new Date(), "yyyy-MM-dd"),
													doctor_id: $rootScope.current_doctor.user_id
                                                };
                                                $scope.other.availibility_slots = [];
                                                CalenderService
                                                        .getTimeSlots(request, function (response) {
                                                            angular.forEach(response.data, function (value) {
                                                                if (value.is_available) {
                                                                    $scope.other.availibility_slots.push(value)
                                                                }
                                                            });
                                                        }); 
												*/
														
                                                $rootScope.app.is_not_valid = false;
												
                                                if (!$rootScope.currentUser.user_first_name) {
                                                    $rootScope.app.is_not_valid = true;
                                                    $rootScope.error_message = $rootScope.incomplete_profile;
                                                    $state.go('app.complete_profile_view.personal');
                                                }
                                                if ($rootScope.currentUser.user_email_verified != 1) {
                                                    $rootScope.app.is_not_valid = false;
                                                    $rootScope.error_message = $rootScope.unverified_email;
                                                    //$state.go('app.complete_profile_view.personal');
                                                }
												if($rootScope.currentUser.user_status != undefined && $rootScope.currentUser.user_status == 3) {
													$rootScope.app.is_not_valid = true;
													$rootScope.error_message = $rootScope.account_pending_for_approval;
													$state.go('app.profile.my_profile_view');
												}
                                                if(!$rootScope.currentUser.is_sub_active) {
                                                    $state.go('app.subscription');
                                                }
                                            } else {
                                                $scope.setCalendar(2);
												$scope.startStopRefetchEvent();

												$rootScope.app.is_not_valid = true;
                                                if ($rootScope.app.is_profile_complete == 2) {
                                                    $rootScope.error_message = $rootScope.incomplete_profile;
                                                    $state.go('app.complete_profile_view.personal');
                                                } else {
                                                    $rootScope.error_message = $rootScope.add_clinic_first;
                                                    $state.go('app.profile.clinic_view');
                                                }
                                            }
                                        }
                                    }, function (error) {
                                        if (error.status == 403) {
                                            $scope.getClinicService();
                                        }
                                    });
                        }
                    }
                }, 1500);
				/* Check forcefully update the calendar setting */
				    if ($rootScope.currentUser != undefined && $rootScope.clinic_data.length > 0) {
						if($rootScope.flg_to_force_calendar_refresh != undefined && $rootScope.flg_to_force_calendar_refresh == true){
							
							$rootScope.flg_to_force_calendar_refresh = false;
							//$scope.startEventRendaring = false;
							//if(uiCalendarConfig.calendars['myCalendar']){
							//	uiCalendarConfig.calendars['myCalendar'].fullCalendar('destroy');
							//	delete $scope.uiConfig;
							//}
							//$timeout(function () {
								$scope.setCalendar(7);
								//$scope.startStopRefetchEvent();
								$rootScope.calOverlayLoader = false;
								/* Set user calender to the current date today  */
								if(uiCalendarConfig != undefined && uiCalendarConfig.calendars['myCalendar'] != undefined && uiCalendarConfig.calendars['myCalendar'].fullCalendar != undefined){
									$scope.calBtnTodayClick();
								}
							//}, 1500);
						}
					}
            };

            //$scope.appointment.doctor_name = $rootScope.current_doctor.user_first_name + " " + $rootScope.current_doctor.user_last_name;
            $scope.getDoctorList = function(set_calender_availability) {
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                };
                UserService
                        .getDoctorList(request, function (response) {
                            if (response.status == true) {
                                $rootScope.doctor_list = response.data;
                                if ($rootScope.doctor_list != undefined && $rootScope.doctor_list.length > 0) {
									angular.forEach($rootScope.doctor_list, function (value) {
										value.full_name = value.user_first_name + " " + value.user_last_name;
										value.is_checked = false;
									});
									
                                    $rootScope.doctor_list[0].is_checked = true;
                                    $rootScope.current_doctor = $rootScope.doctor_list[0];
                                    $scope.date_filter.doctor_id = $rootScope.current_doctor.user_id;
									
									if($rootScope.advertisement_data == undefined && $rootScope.flg_to_set_advertisement_data == false){
										$rootScope.getAdvertisementData();
									}
									
									if(set_calender_availability == true){
										$scope.setCalendar(3);
										//$scope.startStopRefetchEvent();
										$rootScope.calOverlayLoader = false;
										
										var request = {
											clinicns_id: $rootScope.current_clinic.clinic_id,
											date: $filter('date')(new Date(), "yyyy-MM-dd"),
											doctor_id: $rootScope.current_doctor.user_id
										};
										$scope.other.availibility_slots = [];
										CalenderService.getTimeSlots(request, function (response) {
											angular.forEach(response.data, function (value){
												if (value.is_available) {
                                                    value.start_time_label = $filter('timeFormat')(value.start_time, $rootScope.currentUser.hour_format);
													$scope.other.availibility_slots.push(value)
												}
											});
										});
										
										/* Set user calender to the current date today  */
										if(uiCalendarConfig != undefined && uiCalendarConfig.calendars['myCalendar'] != undefined && uiCalendarConfig.calendars['myCalendar'].fullCalendar != undefined){
											$scope.calBtnTodayClick();
										}
										/* setTimeout(function(){
											$scope.startEventRendaring = true;
											uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
											uiCalendarConfig.calendars['myCalendar'].fullCalendar('render');
										},300); */
									}
									
                                    if(uiCalendarConfig.calendars['myCalendar'] && (set_calender_availability == undefined || set_calender_availability == false)){
										uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                                    }
                                }
                            }
                        });
            }
			
            $scope.changeCurrentDoctor = function (doctorObj) {
                if (doctorObj.is_checked) {
                    $rootScope.current_doctor = doctorObj;
                    uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                }
            }
			
            $scope.logout = function () {
				if($rootScope.addvertTimer != undefined)
					$interval.cancel($rootScope.addvertTimer);
				if($rootScope.mainAddvertTimer != undefined)
					$interval.cancel($rootScope.mainAddvertTimer);
				if($rootScope.addvertTimerRefresh != undefined)
					$timeout.cancel($rootScope.addvertTimerRefresh);
				
				$timeout(function(){
					if($rootScope.advertisement_data != undefined)
						delete $rootScope.advertisement_data;
					if($rootScope.advertisement_next_request_time != undefined)
						delete $rootScope.advertisement_next_request_time;
					if($rootScope.currentad != undefined)
						delete $rootScope.currentad;
					if($rootScope.currentAdvtData != undefined)
						delete $rootScope.currentAdvtData;
				});
				
                AuthService.logout();
                $rootScope.app.is_not_valid = false;
                $rootScope.error_message = '';
                $state.go('app.login');
                $rootScope.app.is_clinic_complete = 1;
                $rootScope.app.is_profile_complete = 1;
            };

			$scope.getBlockCalendarSlot = function (){
				var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    doctor_id: $rootScope.current_doctor.user_id
                };
                CalenderService.getBlockCalendarSlot(request, function (response) {
					if(response != undefined && response.data != undefined && response.data.length > 0){
						$scope.block_calender_data = response.data;
						$rootScope.isShowBlockCalForm = false;
					}else{
						$rootScope.isShowBlockCalForm = true;
						$scope.block_calender_data = [];
					}
                });
			}
			
            $scope.getSupportContact = function () {
				CommonService
					.get_support_contact($rootScope.currentUser.address_country_id, function (response) {
						if (response.status == true) {
							$rootScope.local_support = response.local;
                            $rootScope.support_other_detail = response.other_detail;
							$rootScope.global_support = response.global;
						}
					});
            }
			
            $scope.help_files = [];
            $scope.addHelpForm = function () {
                $scope.submitted = true;
                if ($scope.issue.issue_text != '' && $scope.issue.issue_email != '') {
                    CommonService
                            .addIssue($scope.issue, $scope.help_files, function (response) {
                                if (response.status = true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $("#help_modal").modal('hide');
                                $scope.help_files = [];
                                $scope.submitted = false;
                                $scope.issue = {
                                    issue_email: $rootScope.currentUser.user_email
                                };
                            });
                }
            }

            $scope.resetHelpForm = function () {
                $scope.issue.issue_text = "";
                $scope.help_files = [];
            };

            $scope.getWhatsNewData = function () {
                CommonService
                        .getWhatsDataFromDB('', function (response) {
                            if (response.status == true) {
                                $scope.whatsdata = response.data;
                            }
                        });
            };

            /* clinic drop down code */
            $scope.changeCurrentClinic = function (clinic) {
                $rootScope.current_clinic = clinic;
                $rootScope.current_clinic_availability = $rootScope.clinic_availability_data[$rootScope.current_clinic.clinic_id];
                $scope.appointment.duration = clinic.doctor_clinic_mapping_duration;
                $scope.setCalendar(4);
				$scope.startStopRefetchEvent();
				
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    date: $filter('date')(new Date(), "yyyy-MM-dd"),
                    doctor_id: $rootScope.current_doctor.user_id
                };
                $scope.other.availibility_slots = [];
                CalenderService
                        .getTimeSlots(request, function (response) {
                            angular.forEach(response.data, function (value) {
                                if (value.is_available) {
                                    value.start_time_label = $filter('timeFormat')(value.start_time, $rootScope.currentUser.hour_format);
                                    $scope.other.availibility_slots.push(value)
                                }
                            });
                        });
            }

            $scope.changeClinicCalendarUpdate = function (clinic) {
                $rootScope.current_clinic_availability = $rootScope.clinic_availability_data[$rootScope.current_clinic.clinic_id];
                $scope.appointment.duration = clinic.doctor_clinic_mapping_duration;
                $scope.setCalendar(4);
                $scope.startStopRefetchEvent();                
            }            

            /* global search */
            $scope.globalSearch = function () {
                $rootScope.app.isLoader = false;
                var objPatientSearch = {
                    keyword: $scope.search_patient_keyword,
                    is_patient_from_gdb: false
                };
                PatientService
                        .searchPatient(objPatientSearch, function (response) {
                            $scope.search_result = response.data;
                        });
            }
            
			/* patient search in appointment form */
            $scope.patientSearch = function (event) {
                if (event && event.keyCode == 13) {
                    return false;
                }
                if ($scope.Model.patient_obj) {
                    
                    $scope.appointment.patient_fname = $scope.Model.patient_obj.user_name;
                    $scope.appointment.patient_mob = $scope.Model.patient_obj.user_phone_number;
                    $scope.appointment.patient_email = $scope.Model.patient_obj.user_email;
                    $scope.appointment.patient_id = $scope.Model.patient_obj.user_id;
                    $scope.appointment.patient_search = $scope.Model.patient_obj.label;
                    $scope.Model.patient_obj = '';
                    $scope.appointment.appointment_type = "1";
                    $scope.getTimeslot();
                } else {
                    $scope.appointment.patient_fname = '';
                    $scope.appointment.patient_mob = '';
                    $scope.appointment.patient_email = '';
                    $scope.appointment.patient_id = '';
                    $rootScope.app.isLoader = false;
                    var is_patient_from_gdb = false;
                    if($scope.appointment.is_patient_from_gdb != undefined) {
                        is_patient_from_gdb = $scope.appointment.is_patient_from_gdb;
                    }
                    var objPatientSearch = {
                        keyword: $scope.appointment.patient_search,
                        is_patient_from_gdb: is_patient_from_gdb
                    };
                    PatientService
                            .searchPatient(objPatientSearch, function (response) {
								
                                if (response.status == true) {
                                    $scope.search_patient_result = response.data;
                                }
                            });

                }
            }
           
		   /* automatic fullfill data */
            $scope.setClientData = function (item) {
				//$scope.Model.addAppointmentOpen = false;
                $scope.appointment.patient_fname = item.user_name;
                $scope.appointment.patient_mob = item.user_phone_number;
                if(item.user_phone_number == '' && item.parent_user_phone_number != undefined && item.parent_user_phone_number != '') {
                    $scope.appointment.patient_mob = item.parent_user_phone_number;
                }
                $scope.appointment.patient_email = item.user_email;
                $scope.appointment.patient_id = item.user_id;
                $scope.appointment.appointment_id = item.appointment_id;

                if(item.is_new_patient != undefined)
                    $scope.appointment.is_new_patient = item.is_new_patient;

                if (!$scope.Model.addAppointmentOpen) {
                    if (!$scope.appointment.appointment_id) {

                        $scope.Model.patient_obj = item;
                        $state.go('app.dashboard.calendar');
                        $scope.Model.addAppointmentOpen = true;
                        $scope.openAppointment();
                    } else {
                        if($rootScope.current_clinic != undefined && item.appointment_clinic_id != $rootScope.current_clinic.clinic_id) {
                            var clinicSearchObj = $filter('filter')($rootScope.clinic_data, {'clinic_id':item.appointment_clinic_id},true);
                            $rootScope.current_clinic = clinicSearchObj[0];
                            $rootScope.current_clinic_availability = $rootScope.clinic_availability_data[$rootScope.current_clinic.clinic_id];
                            if(clinicSearchObj[0] !=undefined && clinicSearchObj[0].doctor_clinic_mapping_duration != undefined)
                                $scope.appointment.duration = clinicSearchObj[0].doctor_clinic_mapping_duration;
                            $scope.setCalendar(4);
                            $scope.startStopRefetchEvent();
                        }
                        $rootScope.patient_obj = item;
                        $scope.search_patient_keyword = '';
                        $state.go('app.dashboard.patient', {}, {reload: true});
                    }
                }
            }

            /* function for determine active class of dashboard menu */
            $scope.getClassActive = function (route) {
                return ($location.path().substr(0, route.length) === route) ? 'active' : '';
            }

            $rootScope.doctor_list = [];
            /*
             $timeout(function () {
				 UserService.getDoctorList($rootScope.currentUser.user_id, function (response) {
					 if (response.status == true) {
						$rootScope.doctor_list = response.data;
					 }
				 });
             }, 1500);
             */
			 
            $scope.selectAllDoctor = function () {
                var all_selected = $scope.select_all;
                if (all_selected) {
                    var result = $rootScope.doctor_list.map(function (el) {
                        var o = Object.assign({}, el);
                        o.is_checked = true;
                        return o;
                    });
                    $rootScope.doctor_list = result;
                }
            };
			
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            $scope.changeTo = 'Hindi';

            /* alert on eventClick */
            $scope.alertOnEventClick = function (event, jsEvent, view) {
				var cursorPosition = (jsEvent.pageX) + $(window).scrollLeft();
				
				var target = $(jsEvent.target);
                if (target.parents(".outer_div").length <= 0) {
                    $(".outer_div").html('');
                    $(".outer_div").parents(".fc-time-grid-event").css("z-index", '1');
                    $(".outer_div").parents(".fc-event-container").css("z-index", '1');
                    $(".outer_div").parents(".fc-content-col").css("z-index", '1');
                    $(".outer_div.hovercls_" + event.appointment_id).parents('.fc-time-grid-event').css("z-index", '2000');
                    $(".outer_div.hovercls_" + event.appointment_id).parents(".fc-event-container").css("z-index", '2000');
                    $(".fc-row.fc-week.fc-widget-content").css("z-index", '0');
                    $(".outer_div.hovercls_" + event.appointment_id).parents(".fc-row.fc-week.fc-widget-content").css("z-index", '2000');
                    $(".outer_div.hovercls_" + event.appointment_id).parents(".fc-content-col").css("z-index", '2000');

                    PatientService.getPatientModalHTML(event, function (response) {
                        $(".outer_div.hovercls_" + event.appointment_id).html(response);
						
						//var view_port_ht = $('.fc-view-container').height();
						//var curr_app_view_popup_ht = $('.hovercls_'+ event.appointment_id).offset().top + 350;
						
						//if(view_port_ht < curr_app_view_popup_ht){
						//	$('.inner_div_1').css({top:'-350px'});
						//	$('.inner_div_1.left_arrow_left:after').css({top:'350px'});
						//}
						
						if(event.appointment_date){
							var app_date = $filter('date')(new Date(event.appointment_date), "EEE").toString();
							var app_day = app_date.substr(0,3);
							if (view.name == 'month' && ['Fri','Sat','Sun'].indexOf(app_day) != -1){
								$(".inner_div_1").removeClass("inner_div_1_left left_arrow_left");
								$(".inner_div_1").addClass("inner_div_1_right left_arrow_right");
							}else if (view.name == 'agendaWeek' && ['Thu','Fri','Sat','Sun'].indexOf(app_day) != -1){
								$(".inner_div_1").removeClass("inner_div_1_left left_arrow_left");
								$(".inner_div_1").addClass("inner_div_1_right left_arrow_right");
							}else if (cursorPosition >= 1000 && view.name != 'agendaDay') {
								$(".inner_div_1").removeClass("inner_div_1_left left_arrow_left");
								$(".inner_div_1").addClass("inner_div_1_right left_arrow_right");
							}
						}
						
						if($.fullscreen != undefined && $.fullscreen.isFullScreen()){
						}else{
							setTimeout(function () {
								$('html, body').animate({
									scrollTop: $(".outer_div.hovercls_" + event.appointment_id).offset().top - 150
								}, 700);
							}, 200);
						}
                    });
                }
				
                if (target.hasClass("close_icon")) {
                    $(".outer_div").html('');
                }

                if (target.hasClass("delete_appointment") && $rootScope.checkPermission($rootScope.APPOINTMENT_MODULE, $rootScope.DELETE)) {
                    SweetAlert.swal({
                        title: "Are you sure want to cancel appointment ?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            var cancelRequest = {
                                'cancel_appointment_id': event.appointment_id,
                                'doctor_id': $rootScope.current_doctor.user_id,
                                'patient_id': event.user_id
                            }

                            CalenderService
                                    .cancelAppointment(cancelRequest, function (response) {
                                        if (response.status) {
                                            ngToast.success(response.message);
                                            $rootScope.gTrack('delete_appointment');
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                    });

                            $(".outer_div").html('');
                            $scope.getDoctorList();
                        }
                    });
                }

                if (target.hasClass("edit_appointment") && $rootScope.checkPermission($rootScope.APPOINTMENT_MODULE, $rootScope.EDIT)) {
					if($rootScope.current_doctor != undefined && $rootScope.current_doctor.full_name != undefined && $rootScope.current_doctor.full_name != ''){
						$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.current_doctor.full_name;
					}else{
						$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name;
					}
					$scope.editAppointmentOpen = true;
                    $scope.appointment.patient_fname = event.title;
                    $scope.appointment.patient_email = event.user_email;
                    $scope.appointment.patient_mob = event.user_phone_number;
                    $scope.appointment.patient_id = event.user_id;
                    $scope.appointment.appointment_id = event.appointment_id;
                    $scope.appointment.clinic_id = event.appointment_clinic_id;
                    $scope.appointment.appointment_type = event.appointment_type;
                    $scope.appointment.appointment_date = new Date(event.appointment_date);
                    $scope.appointment.from_time = event.appointment_from_time;
                    $scope.appointment.end_time = event.appointment_to_time;

                    var new_date = new Date();
                    new_date.setHours($scope.appointment.from_time.slice(0, -6));
                    new_date.setMinutes($scope.appointment.from_time.slice(3, -3));
                    new_date.setSeconds(00);
                    var request = {
                        'clinic_id': $scope.appointment.clinic_id,
                        'date': event.appointment_date,
                        'appointment_type': event.appointment_type,
                        'doctor_id': $rootScope.current_doctor.user_id
                    };
                    $scope.other.availibility_slots = [];
                    CalenderService
                            .getTimeSlots(request, function (response) {
                                angular.forEach(response.data, function (value) {
                                    if (value.is_available || (value.start_time + ':00') == event.appointment_from_time) {
                                        value.start_time_label = $filter('timeFormat')(value.start_time, $rootScope.currentUser.hour_format);
                                        $scope.other.availibility_slots.push(value)
                                    }
                                });
                                angular.forEach($scope.other.availibility_slots, function (value) {
                                    if ((value.start_time + ':00') == event.appointment_from_time) {
                                        $scope.appointment.from_time = value;
                                    }
                                });
                            });

                    var new_date = new Date();
                    new_date.setHours($scope.appointment.end_time.slice(0, -6));
                    new_date.setMinutes($scope.appointment.end_time.slice(3, -3));
                    new_date.setSeconds(00);
                    $scope.appointment.end_time = new_date;
                    $(".outer_div").html('');
                }
				
                if (target.hasClass("user_redirect")) {
                    $rootScope.current_dashboard_patient_obj = {};
                    $rootScope.current_dashboard_patient_obj.user_id = event.user_id;
                    $rootScope.current_dashboard_patient_obj.appointment_clinic_id = event.appointment_clinic_id;
                    $rootScope.current_dashboard_patient_obj.appointment_id = event.appointment_id;
                    $rootScope.current_dashboard_patient_obj.appointment_date = event.appointment_date;
                    if(event.appointment_clinic_id != $rootScope.current_clinic.clinic_id) {
                        var clinicSearchObj = $filter('filter')($rootScope.clinic_data, {'clinic_id':event.appointment_clinic_id},true);
                        $rootScope.current_clinic = clinicSearchObj[0];
                        $rootScope.current_clinic_availability = $rootScope.clinic_availability_data[$rootScope.current_clinic.clinic_id];
                        if(clinicSearchObj[0] !=undefined && clinicSearchObj[0].doctor_clinic_mapping_duration != undefined)
                            $scope.appointment.duration = clinicSearchObj[0].doctor_clinic_mapping_duration;
                        $scope.setCalendar(4);
                        $scope.startStopRefetchEvent();
                    }
                    $state.go('app.dashboard.patient');
                }
                if (target.hasClass("family-history")) {
                    if(target.parent().find('.short-history').hasClass('hide')) {
                        target.parent().find('.short-history').removeClass('hide');
                        target.parent().find('.full-history').addClass('hide');
                    } else {
                        target.parent().find('.short-history').addClass('hide');
                        target.parent().find('.full-history').removeClass('hide');
                    }
                }
            };
			
            /* alert on Drop */
            $scope.alertOnDrop = function (event, delta, revertFunc, jsEvent, ui, view) {
                $scope.alertMessage = ('Event Dropped to make dayDelta ' + delta);
            };
            /* alert on Resize */
            $scope.alertOnResize = function (event, delta, revertFunc, jsEvent, ui, view) {
                $scope.alertMessage = ('Event Resized to make dayDelta ' + delta);
            };
            /* add and removes an event source of choice */
            $scope.addRemoveEventSource = function (sources, source) {
                var canAdd = 0;
                angular.forEach(sources, function (value, key) {
                    if (sources[key] === source) {
                        sources.splice(key, 1);
                        canAdd = 1;
                    }
                });
                if (canAdd === 0) {
                    sources.push(source);
                }
            };

			$scope.startStopRefetchEvent = function(){
				$scope.startEventRendaring = false;
				setTimeout(function(){
					$scope.startEventRendaring = true;
					if(uiCalendarConfig.calendars['myCalendar']){
						uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
						uiCalendarConfig.calendars['myCalendar'].fullCalendar('render');
					}
					$rootScope.calOverlayLoader = false;
				},300);
			}
			
            /* Change View */
            $scope.changeView = function (view, calendar) {
				//uiCalendarConfig.calendars['myCalendar'].fullCalendar('changeView', view);
				$scope.currentCalView = view;
				$scope.setCalendar(5);
				if(view == 'agendaDay'){
					$scope.calBtnTodayClick();
				}else{
					$scope.startStopRefetchEvent();
				}
            };
			
			$scope.calBtnPrevClick = function(){
				$scope.startEventRendaring = false;
				uiCalendarConfig.calendars['myCalendar'].fullCalendar('prev');
				setTimeout(function(){
					if ($scope.currentCalView == 'agendaDay') {
						if($rootScope.calendar_current_day != undefined){
							var cur_date = $rootScope.calendar_current_day.substr(0,3);
							if ($scope.dayNamesShort.indexOf(cur_date) != -1 && $rootScope.current_clinic_availability) {
								var current_clinic_availability_day = $scope.dayNamesShort.indexOf(cur_date) + 1;
								var current_clinic_availability = $filter('filter')($rootScope.current_clinic_availability, { 'clinic_availability_week_day' : current_clinic_availability_day.toString() });
								if(current_clinic_availability.length > 0){
									var current_clinic = current_clinic_availability[0];
									var minTime = $rootScope.calendarMinTime;
									var maxTime = $rootScope.calendarMaxTime;
									if (current_clinic.clinic_availability_session_2_start_time) {
										// maxTime = current_clinic.clinic_availability_session_2_end_time;
									}							
									uiCalendarConfig.calendars['myCalendar'].fullCalendar('option',{'minTime': minTime,'maxTime': maxTime});
								}
							}
						}
					}
					$scope.startEventRendaring = true;
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('render');
				},300);
			}
			$scope.calBtnNextClick = function(){
				$scope.startEventRendaring = false;
				uiCalendarConfig.calendars['myCalendar'].fullCalendar('next');
				setTimeout(function(){
					if ($scope.currentCalView == 'agendaDay') {
						if($rootScope.calendar_current_day != undefined){
							var cur_date = $rootScope.calendar_current_day.substr(0,3);
							if ($scope.dayNamesShort.indexOf(cur_date) != -1 && $rootScope.current_clinic_availability) {
								var current_clinic_availability_day = $scope.dayNamesShort.indexOf(cur_date) + 1;
								var current_clinic_availability = $filter('filter')($rootScope.current_clinic_availability, { 'clinic_availability_week_day' : current_clinic_availability_day.toString() });
								if(current_clinic_availability.length > 0){
									var current_clinic = current_clinic_availability[0];
									var minTime = $rootScope.calendarMinTime;
									var maxTime = $rootScope.calendarMaxTime;
									if (current_clinic.clinic_availability_session_2_start_time) {
										// maxTime = current_clinic.clinic_availability_session_2_end_time;
									}							
									uiCalendarConfig.calendars['myCalendar'].fullCalendar('option',{'minTime': minTime,'maxTime': maxTime});
									/* 
										uiCalendarConfig.calendars['myCalendar'].fullCalendar('option', 'minTime', minTime);
										uiCalendarConfig.calendars['myCalendar'].fullCalendar('option', 'maxTime', maxTime); 
									*/
								}
							}
						}
					}
					$scope.startEventRendaring = true;
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('render');
				},300);
			}
			$scope.calBtnTodayClick = function(){
				$scope.startEventRendaring = false;
				uiCalendarConfig.calendars['myCalendar'].fullCalendar('today');
				setTimeout(function(){
					if ($scope.currentCalView == 'agendaDay') {
						if($rootScope.calendar_current_day != undefined){
							var cur_date = $rootScope.calendar_current_day.substr(0,3);
							if ($scope.dayNamesShort.indexOf(cur_date) != -1 && $rootScope.current_clinic_availability) {
								var current_clinic_availability_day = $scope.dayNamesShort.indexOf(cur_date) + 1;
								var current_clinic_availability = $filter('filter')($rootScope.current_clinic_availability, { 'clinic_availability_week_day' : current_clinic_availability_day.toString() });
								if(current_clinic_availability.length > 0){
									var current_clinic = current_clinic_availability[0];
									var minTime = $rootScope.calendarMinTime;
									var maxTime = $rootScope.calendarMaxTime;
									if (current_clinic.clinic_availability_session_2_start_time) {
										// maxTime = current_clinic.clinic_availability_session_2_end_time;
									}							
									uiCalendarConfig.calendars['myCalendar'].fullCalendar('option',{'minTime': minTime,'maxTime': maxTime});
								}
							}
						}
					}
					$scope.startEventRendaring = true;
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
					uiCalendarConfig.calendars['myCalendar'].fullCalendar('render');
				},300);
			}
			
			//with this you can handle the events that generated when we change the view i.e. Month, Week and Day
			/* $scope.changeView = function(view,calendar) {
				currentView = view;
				calendar.fullCalendar('changeView',view);
				$scope.$apply(function(){
				$scope.alertMessage = ('You are looking at '+ currentView);
				});
			}; */;
			
            /* Change View */
            $scope.renderCalender = function (calendar) {
                if (uiCalendarConfig.calendars[calendar]) {
                    uiCalendarConfig.calendars[calendar].fullCalendar('render');
                }
            };

            $scope.dayClickTest = function (date, allDay, jsEvent, view) {
				
                /*
                 $scope.appointment.doctor_name = $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name;
                 $scope.appointment.appointment_date = date._d;
                 $scope.getTimeslot();
                 $scope.Model.addAppointmentOpen = true;
                 */
            };

            /* event source that pulls from google.com */
            $scope.eventSource = function (start, end, timezone, callback) {
				$rootScope.calendar_current_day = $filter('date')(start, "EEE").toString();
				$scope.date_filter = {
                    start: $filter('date')(start, "yyyy-MM-dd"),
                    end: $filter('date')(end, "yyyy-MM-dd")
                };
				
                if ($rootScope.current_clinic) {
                    $scope.date_filter.clinic_id = $rootScope.current_clinic.clinic_id;
                    if ($rootScope.current_doctor) {
                        $scope.date_filter.doctor_id = $rootScope.current_doctor.user_id;
                    } else {
                        $scope.events = [];
                        callback($scope.events);
                        return;
                        //$scope.date_filter.doctor_id = $rootScope.currentUser.user_id;
                    }
                }
                $scope.events = [];
                if (!$scope.date_filter.doctor_id) {
                    $scope.events = [];
                    callback($scope.events);
                    return;
                }
				
				if(!$scope.startEventRendaring){
					$scope.events = [];
                    callback($scope.events);
                    return;
				}
				var clinic_id_arr = [];
                angular.forEach($rootScope.clinic_data, function (value, key) {
                    clinic_id_arr.push(value.clinic_id);
                });
                $scope.date_filter.clinic_id_arr = clinic_id_arr;
				CalenderService
                        .getAppointmentList($scope.date_filter, function (response){
                            if (response.status == true) {
                                $scope.events = [];
                                angular
                                        .forEach(response.data, function (value) {
                                            var temp_start_date = value.appointment_date + "T" + value.appointment_from_time;
                                            var temp_end_date = value.appointment_date + "T" + value.appointment_to_time;
                                            //var start = $filter('date')(value.appointment_date + "T" + value.appointment_from_time, "yyyy-MM-dd H:m:s");
                                            //var today = $filter('date')(new Date(), "yyyy-MM-dd H:m:s");
                                            // var is_past = false;
                                            // if (start < today){
                                            //     is_past = true;
                                            // }
                                            $scope.events.push({
                                                title: '' + $filter('trimString')(value.user_first_name + " " + value.user_last_name, 20),
                                                start: temp_start_date,
                                                end: temp_end_date,
                                                textEscape: false,
                                                cache: false,
                                                user_id: value.user_id,
                                                user_email: value.user_email,
                                                user_phone_number: value.user_phone_number,
                                                appointment_id: value.appointment_id,
                                                appointment_type: value.appointment_type,
                                                appointment_date: value.appointment_date,
                                                appointment_from_time: value.appointment_from_time,
                                                appointment_clinic_id: value.appointment_clinic_id,
                                                clinic_color_code: value.clinic_color_code,
                                                clinic_name: value.clinic_name,
                                                appointment_to_time: value.appointment_to_time,
                                                appointment_time: value.appointment_time,
                                                is_past: value.is_past
                                            });
                                        });
                            }
                            callback($scope.events);
                        });
            };
			
			$scope.dayNamesShort = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
			$scope.setCalendar = function(id) {
                var duration = 60;
                var minTime = "00:00:00";
                var maxTime = "24:00:00";
                var skip_slot_min = '';
                var skip_slot_max = '';
				var businessHoursList = [];
                if ($rootScope.current_clinic) {
					duration = $rootScope.calendarMinDuration;
                    minTime = $rootScope.calendarMinTime;
                    maxTime = $rootScope.calendarMaxTime;
                    if ($rootScope.current_clinic.doctor_clinic_doctor_session_2_start_time) {
                        skip_slot_min = $rootScope.current_clinic.doctor_clinic_doctor_session_2_start_time;
                        skip_slot_max = maxTime;
                        // maxTime = $rootScope.current_clinic.doctor_clinic_doctor_session_2_end_time;
                    }

					if($rootScope.current_clinic_availability){
						angular.forEach($rootScope.clinic_availability_data, function (clinic_availability) {
                            angular.forEach(clinic_availability, function (value) {
    							if (value.clinic_availability_week_day != undefined) {
    								var cDay = parseInt(value.clinic_availability_week_day);
    								if(cDay == 7){ cDay = 0; }
    								businessHoursList.push({start: value.clinic_availability_session_1_start_time, end: value.clinic_availability_session_1_end_time, dow: [cDay]});
    								if (value.clinic_availability_session_2_start_time) {
    									businessHoursList.push({start: value.clinic_availability_session_2_start_time, end: value.clinic_availability_session_2_end_time, dow: [cDay]});
    								}
    							}
    						});
                        });
					}
                }
				//businessHoursList = [];
				if($scope.currentCalView == undefined)
					$scope.currentCalView = 'agendaDay';
				if($scope.currentCalView == 'agendaDay')
                    businessHoursList = [];
				$scope.uiConfig = {
                    calendar: {
                        customButtons: {
                            myCustomButton: {
                                text: 'Calendar',
                            }
                        },
                        timeFormat: 'HH:mm',
                        stick: true,
                        minTime: minTime,
                        maxTime: maxTime,
                        firstDay: 1,
                        editable: false,
                        allDaySlot: false,
                        startEditable: false,
                        aspectRatio: 2,
                        slotDuration: minutesToHourFilter(duration),
                        slotLabelInterval: minutesToHourFilter(duration),
                        slotLabelFormat: ($rootScope.currentUser.hour_format == '1') ? 'hh:mm A' : 'HH:mm',
                        defaultView: $scope.currentCalView,
                        contentHeight: 'auto',
						schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
						businessHours:businessHoursList,
                        views: {
                            week: {
                                columnFormat: "ddd DD/MM",
                            }
                        },
                        header: {
                            left: '',
                            center: 'today prev,title,next', // agendaDay,agendaWeek,month
                            right: ''
                        },
						customButtons: {
							prev: {
								text:'',
								icon: "fc-icon fc-icon-left-single-arrow",
								click: $scope.calBtnPrevClick
							},
							next: {
								text:'',
								icon: "fc-icon fc-icon-right-single-arrow",
								click: $scope.calBtnNextClick
							},
							today: {
								text:'Today',
								icon: "",
								click: $scope.calBtnTodayClick
							}
						},
                        eventClick: $scope.alertOnEventClick,
                        dayClick: $scope.dayClickTest,
                        disableDragging: true,
                        viewRender: function (view, element) {
							if ($rootScope.calendar_current_day == undefined) {
								$rootScope.calendar_current_day = $filter('date')(new Date(), "EEE").toString();
							}
							var cur_date = $rootScope.calendar_current_day.substr(0,3);
							var cur_view_date = view.calendar.currentDate.toString().substr(0,3);
							if (view.name == 'agendaDay') {
                                $(element).find(".fc-axis.fc-widget-header").html('Time');
                                $(".fc-today-button").html('Today');
                            } else if (view.name == 'agendaWeek') {
                                $(".fc-today-button").html('This Week');
                            } else if (view.name == 'month') {
                                $(".fc-today-button").html('This Month');
                            }
                            $(".fc-today-button").css("width", "auto");
                            $(".fc-today-button").css("min-width", "100px");
                            $(".fc-today-button").css("text-transform", "none");
                            // $(".fc-today-button").css("font-family", "gotham_book");
							if (view.name == 'agendaDay' && $rootScope.clinic_data.length == 1) {
								if ($scope.dayNamesShort.indexOf(cur_date) != -1 && $rootScope.current_clinic_availability) {
									var current_clinic_availability_day = $scope.dayNamesShort.indexOf(cur_date) + 1;
									var current_clinic_availability = $filter('filter')($rootScope.current_clinic_availability, { 'clinic_availability_week_day' : current_clinic_availability_day.toString() });
									if(current_clinic_availability.length > 0){
										var current_clinic = current_clinic_availability[0];
										var currMinTime = current_clinic.clinic_availability_session_1_start_time;
                                        var minTime = current_clinic.clinic_availability_session_1_start_time;
										if(minTime != currMinTime && current_clinic.clinic_availability_session_2_start_time)
											$scope.removeNonBusinessHoursSlots(minTime, currMinTime, true, false);
										
										currMaxTime = current_clinic.clinic_availability_session_1_end_time;
										if (current_clinic.clinic_availability_session_2_start_time) {
											skip_slot_min = current_clinic.clinic_availability_session_2_start_time;
											skip_slot_max = currMaxTime;
											currMaxTime = current_clinic.clinic_availability_session_2_end_time;
										} else {
                                            skip_slot_max = '';
                                            skip_slot_min = '';
                                        }
									} else {
                                        $('.fc-body').find('.fc-slats table tbody').html('<tr class="doc_not_available"><td class="fc-axis fc-time fc-widget-content" style="width: 32px;"></td><td class="fc-widget-content">Clinic Not Available</td></tr>');
                                    }
									if (skip_slot_min && skip_slot_max){
										$scope.removeNonBusinessHoursSlots(skip_slot_max, skip_slot_min, false, true);
									}
									if(maxTime != currMaxTime)
										$scope.removeNonBusinessHoursSlots(currMaxTime, maxTime, false, false);
									$('.fc-business-container').remove();
								}
                            } else if(view.name == 'agendaDay' && $rootScope.clinic_data.length > 1) {
                                var current_clinic_availability_day = $scope.dayNamesShort.indexOf(cur_date) + 1;
                                var current_day = $scope.dayNamesShort.indexOf(cur_date) + 1;                                
                                if($rootScope.timeSlots != undefined && $rootScope.timeSlots[current_day] != undefined)
                                    $scope.removeNonBusinessSlots($rootScope.timeSlots[current_day]);
                            }
                            if (view.name == 'agendaWeek') {
								
							}
                        },
                        eventRender: function (event, element) {
                            	element.find('.fc-time').addClass("hide");
								var appointment_type_img = '';
								if (event.appointment_type == 1) {
									appointment_type_img = 'doctor_visit.png';
								} else if (event.appointment_type == 2) {
									appointment_type_img = 'home_service.png';
								} else if (event.appointment_type == 3) {
									appointment_type_img = 'online_chat.png';
								} else if (event.appointment_type == 4) {
									appointment_type_img = 'call_doctor.png';
								} else if (event.appointment_type == 5) {
									appointment_type_img = 'video_call.png';
								}
                                var clinic_color = '';
                                if(event.clinic_color_code != undefined && event.clinic_color_code != '') {
                                    clinic_color = '<span class="clinic_status" style="background:#' + event.clinic_color_code + '">&nbsp;</span>';
                                }
                                // console.log(clinic_color);
								var content = clinic_color + '<span>' + event.title + '</span>' + '<img class="doctor_visit" src="app/images/' + appointment_type_img + '">';
								if ($scope.currentCalView != 'agendaWeek') {
									content += '<br><div class="fc-time">' +element.find('.fc-time').html() +'</div>';
								}
								element.find('.fc-title').html(content);
								element.append('<div class="outerdiv outer_div hovercls_' + event.appointment_id + '" id="outer_div"></div>');
                        },
                    }
                };
            }
            $scope.removeNonBusinessSlots = function(businessSlots) {
                $(".fc-slats table tbody tr").each(function() {
                    var slot_time = $(this).attr("data-time");
                    if(!businessSlots.includes(slot_time))
                        $(this).remove();
                });
            }
            //$scope.setCalendar(1);
			$scope.addMinutes = function(time, minsToAdd) {
                function D(J){ return (J<10? '0':'') + J;};
                var piece = time.split(':');
                var mins = piece[0]*60 + +piece[1] + minsToAdd;
                return D(mins%(24*60)/60 | 0) + ':' + D(mins%60);  
            }
			$scope.removeNonBusinessHoursSlots = function(skip_slot_max, skip_slot_min, is_remove_first, is_show_msg){
                var break_start_time = skip_slot_max;
                var break_end_time = skip_slot_min;
                if($(".fc-slats").find("[data-time='" + skip_slot_max + "']").length == 0) {
                    var is_got_slot = false;
                    for (var i = 0; i < 60; i++) {
                        if(is_got_slot == false) {
                            var new_time = $scope.addMinutes(skip_slot_max, +5);
                            skip_slot_max = new_time + ":00";
                        }
                        if(is_got_slot == false && $(".fc-slats").find("[data-time='" + skip_slot_max + "']").length > 0) {
                            is_got_slot = true;
                        }                        
                    }
                }
                if($(".fc-slats").find("[data-time='" + skip_slot_min + "']").length == 0) {
                    var is_got_slot = false;
                    for (var i = 0; i < 60; i++) {
                        if(is_got_slot == false) {
                            var new_time = $scope.addMinutes(skip_slot_min, +5);
                            skip_slot_min = new_time + ":00";
                        }
                        if(is_got_slot == false && $(".fc-slats").find("[data-time='" + skip_slot_min + "']").length > 0) {
                            is_got_slot = true;
                        }                        
                    }
                }
				var start = $(".fc-slats").find("[data-time='" + skip_slot_max + "']");
				var end = $(".fc-slats").find("[data-time='" + skip_slot_min + "']");
				var start = $(".fc-slats").find("[data-time='" + skip_slot_max + "']");
				var end = $(".fc-slats").find("[data-time='" + skip_slot_min + "']");
				$(start).nextUntil(end, 'tr').remove();
				if(!is_remove_first && is_show_msg){
					$(start).addClass('doc_not_available');
					$(start).find("td:eq(1)").text('');
                    if($rootScope.currentUser.hour_format == '1') {
					   $(start).find("td:eq(1)").text('Clinic Not Available - ' + $filter('timeTo24')(break_start_time) + ' To '+$filter('timeTo24')(break_end_time));
                    } else {
                        var break_start_time_arr = break_start_time.split(":");
                        var break_end_time_arr = break_end_time.split(":");
                        $(start).find("td:eq(1)").text('Clinic Not Available - ' + break_start_time_arr[0] + ':' + break_start_time_arr[1] + ' To ' + break_end_time_arr[0] + ':' + break_end_time_arr[1]);
                    }
				}
				if(is_remove_first){
					$(start).remove();
				}
			}
			
            $scope.changeLang = function () {
                if ($scope.changeTo === 'Hindi') {
                    $scope.uiConfig.calendar.dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    $scope.uiConfig.calendar.dayNamesShort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                    $scope.changeTo = 'English';
                } else {
                    $scope.uiConfig.calendar.dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    $scope.uiConfig.calendar.dayNamesShort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                    $scope.changeTo = 'Hindi';
                }
            };
			
            /* event sources array*/
            $scope.eventSources = [$scope.events, $scope.eventSource];
			
            $scope.addAppointmentClose = function () {
                $scope.Model.addAppointmentOpen = false;
                $scope.Model.patient_obj = '';
                $scope.editAppointmentOpen = false;
                $scope.submitted = false;
                $scope.isOTPScreenOn = false;
                var today_appoint_date = new Date();
                var year = today_appoint_date.getFullYear();
                var month = today_appoint_date.getMonth();
                var day = today_appoint_date.getDate();
                var appoint_today_date = new Date(year, month, day);
                $scope.appointment = {
                    doctor_name: $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name,
                    appointment_date: appoint_today_date,
                    appointment_type: "1",
                    duration: $rootScope.current_clinic.doctor_clinic_mapping_duration,
                };
            }
			
            $scope.resend_appointment_book_otp = function () {
                $scope.appointment.is_resend_otp = true;
                $('.addAppointmentBtn').click();
            };

            $scope.backToAppointmentScreen = function () {
                $scope.isOTPScreenOn = false;
            };
            
            /* add appointemnt form submit */
            $scope.addAppointment = function (addAppointmentForm, isFromPatientController) {
                $scope.submitted = true;
                if (addAppointmentForm.$valid) {
                    if ($scope.other.availibility_slots.length == 0) {
                        ngToast.danger("Please add clinic details first");
                        return false;
                    }

					$scope.appointment.appointment_date = addAppointmentForm.appointment_date.$modelValue;					
                    var month = $scope.appointment.appointment_date.getMonth() + 1;
                    var day   = $scope.appointment.appointment_date.getDate();
                    var year  = $scope.appointment.appointment_date.getFullYear();
                    $scope.appointment.real_appointment_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    // $scope.appointment.clinic_id  = $rootScope.current_clinic.clinic_id;
                    $scope.appointment.doctor_id  = $rootScope.current_doctor.user_id;
					$scope.appointment.from_time  = addAppointmentForm.from_time.$modelValue;
					$scope.appointment.patient_id = addAppointmentForm.patient_id.$modelValue;
                        if(($scope.appointment.is_new_patient!= undefined && $scope.appointment.is_new_patient == 'Yes' && $scope.isOTPScreenOn != true) || ($scope.appointment.is_resend_otp != undefined && $scope.appointment.is_resend_otp == true)){
                            CalenderService
                                    .appointmentsBookSendOtp($scope.appointment, function (response) {
                                        if (response.status == true) {
                                            $scope.isOTPScreenOn = true;
                                            $scope.appointment.is_resend_otp = '';
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                            });

                        } else {
                            if($scope.isOTPScreenOn == true) {
                                $scope.appointment.is_new_patient = 'Yes';
                                $scope.appointment.otp = addAppointmentForm.send_appointment_book_otp.$modelValue;

                            }

                            CalenderService
                                    .addNewAppointment($scope.appointment, function (response) {
                                        
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                className: '',
                                                dismissOnTimeout: true,
                                                timeout: 5000
                                            });
                                            $rootScope.gTrack('add_appointment');
                                            $scope.Model.addAppointmentOpen = false;
                                            if (isFromPatientController) {
                                                $scope.$broadcast('refreshDates');
                                                uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                                                $scope.other.availibility_slots = [];
                                                $scope.appointment.doctor_id = "";
                                            } else {
                                                $scope.addAppointmentClose();
                                                uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                                            }
                                            $scope.getDoctorList();
                                            $scope.isOTPScreenOn = false;
                                            $scope.appointment.is_resend_otp = '';
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                            });
                    }
                }
            }

            $scope.addZero = function (i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
            /* edit appointemnt form submit */
            $scope.editAppointment = function (editAppointmentForm) {

                $scope.submitted = true;
                if (editAppointmentForm.$valid) {

                    var month = $scope.appointment.appointment_date.getMonth() + 1;
                    var day = $scope.appointment.appointment_date.getDate();
                    var year = $scope.appointment.appointment_date.getFullYear();

                    $scope.appointment.real_appointment_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');

                    // $scope.appointment.clinic_id = $rootScope.current_clinic.clinic_id;

                    CalenderService
                            .editExistingAppointment($scope.appointment, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });

                                    uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                                    $scope.Model.addAppointmentOpen = false;
                                    $scope.addAppointmentClose();
                                    $rootScope.gTrack('edit_appointment');
                                } else {
                                    ngToast.danger(response.message);
                                }

                            });
                }
            }
            $scope.Model = $scope.Model || {addAppointmentOpen: ''};
            $scope.Model = $scope.Model || {patient_obj: ''};
            $scope.openAppointment = function () {
                $scope.Model.addAppointmentOpen = true;
				if($rootScope.current_doctor != undefined && $rootScope.current_doctor.full_name != undefined && $rootScope.current_doctor.full_name != ''){
					$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.current_doctor.full_name;
				}else{
					$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name;
				}
                $scope.appointment.appointment_type = "1";
                $scope.appointment.clinic_id = $rootScope.current_clinic.clinic_id;
                $(".outer_div").html('');
                setTimeout(function () {
                    $('html, body').animate({
                        scrollTop: $("#add_appointment").offset().top
                    }, 700);
                }, 100);
            }

            $scope.compareTime = function (start, end) {
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
            };

            $scope.getTimeslot = function () {
                if($scope.appointment.clinic_id == undefined){
                    $scope.appointment.clinic_id = $rootScope.current_clinic.clinic_id;
                }

                if($scope.appointment.appointment_date != undefined && $scope.appointment.clinic_id != undefined) {
                    var month = $scope.appointment.appointment_date.getMonth() + 1;
                    var day = $scope.appointment.appointment_date.getDate();
                    var year = $scope.appointment.appointment_date.getFullYear();
                    $scope.appointment.real_appointment_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    var clinicSearchObj = $filter('filter')($rootScope.clinic_data, {'clinic_id':$scope.appointment.clinic_id},true);
                    if(clinicSearchObj[0] !=undefined && clinicSearchObj[0].doctor_clinic_mapping_duration != undefined)
                        $scope.appointment.duration = clinicSearchObj[0].doctor_clinic_mapping_duration;
                    var request = {
                        'clinic_id': $scope.appointment.clinic_id,
                        'date': $scope.appointment.real_appointment_date,
                        appointment_type: $scope.appointment.appointment_type,
                        doctor_id: $rootScope.current_doctor.user_id
                    };
                    $scope.other.availibility_slots = [];
                    CalenderService
                            .getTimeSlots(request, function (response) {
                                $scope.appointmentTypeArr = response.appointmentTypeArr;
                                // console.log($scope.appointmentTypeArr);
                                if($scope.appointmentTypeArr.length == 1)
                                    $scope.appointment.appointment_type = $scope.appointmentTypeArr[0];
                                angular.forEach(response.data, function (value) {
                                    if (value.is_available) {
                                        value.start_time_label = $filter('timeFormat')(value.start_time, $rootScope.currentUser.hour_format);
                                        $scope.other.availibility_slots.push(value)
                                    }
                                });
                        });
                }
            }

            $scope.checkMobileKey = function (event) {
                if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
                    event.preventDefault();
                    return false;
                }
            }

            $scope.checkOtpLogin = function (form) {
                $scope.submitted = true;
                var request = {
                    phone_number: $rootScope.phone_number,
                    otp: $scope.login.otp,
                };
                if (form.$valid) {
                    LoginService
                            .checkOtp(request, function (response) {
                                if (response.status == true) {
                                    AuthService
                                            .login(response.user_data, response.access_token);
                                    $scope.login.otp = '';
                                    $("#autologout_modal").modal("hide");
                                    $rootScope.getSidebarMenu();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
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

            /* Resend otp code */
            $scope.resend = function () {
                LoginService
                        .resendOtp($rootScope.phone_number, function (response) {
                            if (response.status == true) {
                                ngToast.success({
                                    content: response.message,
                                    className: '',
                                    dismissOnTimeout: true,
                                    timeout: 5000
                                });
                                $rootScope.is_send_button_visible = false;
                                $rootScope.is_send_timer_visible = true;
                                $scope.settick();
                                tick();
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
            };

            $rootScope.is_send_button_visible = true;
            $rootScope.is_send_timer_visible = false;
            $rootScope.currentTime = 0;
            var tick = function () {
                if ($rootScope.resendWaiting <= $rootScope.currentTime) {
                    $rootScope.is_send_button_visible = true;
                    $rootScope.is_send_timer_visible = false;
                    return false;
                }
                $rootScope.resendWaiting -= 1000;
                $interval.cancel($scope.promise);
                $scope.promise = $interval(function () {
                    tick();
                }, 1000, true);
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

            $scope.showAlertForDev = function () {
                SweetAlert.swal($rootScope.app.devMsg);
            }

            $scope.openHelpFile = function (fileObj) {
                setTimeout(function () {
                    document.getElementById(fileObj).click();
                }, 0);
            }
            $scope.getFileHelpFiles = function (file_obj, key) {
                var total_files = $scope.help_files.length + file_obj.length;
                if (total_files > 5) {
                    //SweetAlert.swal("Maximum 5 images are allowed");
                    return false;
                }
                var is_file = false;
                angular.forEach($scope.help_files, function (file_obj1, key) {
                    if (file_obj1.name == file_obj[0].name) {
                        is_file = true;
                    }
                });
                if (is_file == false) {
                    var files = file_obj;
                    angular.forEach(files, function (file_obj, key) {
                        $scope.help_files.push(file_obj);
                        var file_type = file_obj.name;
                        if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {
                            fileReader.readAsDataUrl(file_obj, $scope)
                                    .then(function (result) {
                                        file_obj.temp_img = result;
                                    });
                        }

                    });
                }
            }
            $scope.removeImage = function (key) {
                $scope.help_files.splice(key, 1);
            }
            $scope.refreshCalendarByBtn = function () {
                //uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                $scope.getDoctorList();
            }
            $scope.openToolTip = function (type) {
                $(".tooltip_custom_" + type).tooltip({
                    html: true,
                    title: 'hello',
                    placement: "left",
                }).tooltip("open");
            };
        });
		
angular.module("app.dashboard").directive("ngFileSelectHelp", function () {
	return {
		link: function ($scope, el, attr, ctrl) {
			el.bind("change", function (e) {
				var file_obj_array = (e.srcElement || e.target).files;
				$scope.getFileHelpFiles(file_obj_array, attr.pass);

			})
		}
	}
});
angular.module("app.dashboard")
    .directive("ngFileSelectIdProof", function () {
        return {
            // controller: 'ModalAddPatientCtrl',
            // bindToController: true,
            link: function ($scope, el, attr, ctrl) {
                el.bind("change", function (e) {
                    var file_obj = (e.srcElement || e.target).files[0];
                    var file_type = file_obj.name;
                    if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {
                        $scope.id_proof_file = file_obj;
                        $scope.getFileIdProof(file_obj);
                    }
                })
            }
        }
    });
angular.module('app.dashboard').controller('ModalViewIdProofImg', function ($scope, $rootScope, $filter, $uibModalInstance, items) {
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    $scope.id_proof_img_path = items.img_path;
    $rootScope.updateBackDropModalHeight('view_id_proof_img');
});
angular.module('app.dashboard').filter('filterInArray', function($filter){
    return function(list, arrayFilter, element){
        if(arrayFilter){
            return $filter("filter")(list, function(listItem){
                return arrayFilter.indexOf(listItem[element]) != -1;
            });
        }
    };
});
angular.module('app.dashboard').controller('ModalAddPatientCtrl', function ($scope, $rootScope, $filter, $uibModalInstance, $uibModal, items, SMOKE, ALCOHOL, EncryptDecrypt, SweetAlert, PatientService, CommonService, ngToast, fileReader) {
    $rootScope.updateBackDropModalHeight('add_patient_modal');
    $scope.getFileIdProof = function (file_obj) {
        fileReader.readAsDataUrl(file_obj, $scope)
            .then(function (result) {
                $scope.patient.id_proof_img_temp = result;
            });
    }
    $scope.removeIdProofImage = function () {
        $scope.patient.id_proof_img_temp = '';
    }
    $scope.Model = $scope.Model || {currentImg: ''} || {currentPdf: ''};
    $scope.showFullImage = function (img_path) {
        $scope.Model.currentImg = img_path;
        var items = {img_path: img_path}
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'app/views/patient/modal/view_id_proof_img.html?' + $rootScope.getVer(2),
            controller: 'ModalViewIdProofImg',
            size: 'lg',
            backdrop: 'static',
            keyboard: false,
            resolve: {
                items: function () {
                    return items;
                }
            }
        });
    }
    var past_mindate = new Date();
    var year = past_mindate.getFullYear();
    var month = past_mindate.getMonth();
    var day = past_mindate.getDate();
    var past_mindate = new Date(year - 150, month, day);
    var today_maxdate = new Date(year, month, day);
    today_maxdate.setHours(23);
    today_maxdate.setMinutes(59);
    today_maxdate.setSeconds(59);
    $scope.date_of_birth = {
        datepickerOptions: {
            maxDate: today_maxdate,
            minDate: past_mindate,
        },
        open: false
    };
    $scope.family_his_date = {
        datepickerOptions: {
            maxDate: today_maxdate,
            minDate: past_mindate,
        },
        open: false
    };
    $scope.medical_conditions = [];
    $scope.id_proof_list = ['Aadhar card', 'Passport', 'Driving License', 'Pan Card'];
    $scope.get_medical_conditions = function() {
        if($scope.medical_conditions.length > 0)
            return false;
        var request = {};
        PatientService
            .getMedicalConditions(request, function (response) {
                if (response.status == true) {
                    $scope.medical_conditions = response.medical_condition_data;
                }
            });
    }
    $scope.$on('gmPlacesAutocomplete::placeChanged', function () {
        $scope.patient.lat = '';
        $scope.patient.lng = '';
        if (angular.isObject($scope.patient.patient_address)) {
            if ($scope.patient.patient_address !=undefined && $scope.patient.patient_address != '' && $scope.patient.patient_address.getPlace() != undefined) {
                var location = $scope.patient.patient_address.getPlace().geometry.location;
                $scope.patient.lat = location.lat();
                $scope.patient.lng = location.lng();
                $scope.$apply();
            }
        }
    });
    
    if(items.user_id != '') {
        $scope.patient = {
            patient_id: items.user_id,
            user_patient_id: items.user_patient_id,
            patient_first_name: items.user_first_name,
            patient_last_name: items.user_last_name,
            patient_mob_number: items.user_phone_number,
            patient_email: items.user_email,
            country_id: items.address_country_id,
            patient_bdate: new Date(items.user_details_dob),
            patient_height: items.user_details_height,
            patient_blood_group: items.user_details_blood_group,
            patient_address1: items.address_name_one,
            patient_state: items.address_state_id,
            patient_city: items.address_city_id,
            locality: items.address_locality,
            patient_picode: items.address_pincode,
            patient_weight: $filter('PoundToKG')(items.user_details_weight),
            lat: items.address_latitude,
            lng: items.address_longitude,
            patient_img_temp: '',
            patient_img: items.user_photo_filepath,
            patient_img_thumb: items.user_photo_filepath_thumb,
            share_status: (items.user_details_agree_medical_share == "1") ? true : false,
            refby: items.refer_other_doctor_id,
            refer_user_id: items.refer_user_id,
            emergency_contact_name: items.user_details_emergency_contact_person,
            emergency_contact_number: items.user_details_emergency_contact_number,
            smoking_habits: items.user_details_smoking_habbit,
            alcohol: items.user_details_alcohol,
            food_preference: items.user_details_food_preference,
            marital_status: items.user_details_marital_status,
            occupation: items.user_details_occupation,
            id_proof_type: items.user_details_id_proof_type,
            id_proof_detail: items.user_details_id_proof_detail,
            id_proof_image: items.user_details_id_proof_image,
            id_proof_image_thumb: items.user_details_id_proof_image_thumb,
            search_patient_keyword: '',
            refer_other_doctor_id: items.refer_other_doctor_id,
            patient_smoke_habits: SMOKE,
            patient_alcohol: ALCOHOL
        }
        var current_date = new Date();
        var yearNow = current_date.getFullYear();
        var yearDob = $scope.patient.patient_bdate.getFullYear();
        $scope.patient.patient_age = yearNow - yearDob;
        if(items.refer_doctor_name != null && items.refer_doctor_name != '')
            $scope.patient.search_patient_keyword = $rootScope.docPrefix + items.refer_doctor_name;
        
        if(items.user_gender == 'male')
            $scope.patient.gender = 1;
        else if(items.user_gender == 'female')
            $scope.patient.gender = 2;
        else if(items.user_gender == 'other')
            $scope.patient.gender = 3;
        else
            $scope.patient.gender = '';
        setTimeout( function () {
            $scope.patient.patient_address = items.address_name;
        },500);
    } else {
        $scope.patient = {
            patient_id: '',
            patient_first_name: '',
            patient_last_name: '',
            patient_mob_number: '',
            patient_type: "1",
            patient_email: '',
            country_id: '',
            gender: '',
            patient_bdate: '',
            patient_height: '',
            patient_blood_group: '',
            patient_address1: '',
            patient_address: '',
            patient_state: '',
            patient_city: '',
            patient_weight: '',
            locality: '',
            patient_picode: '',
            lat: '',
            lng: '',
            patient_img_temp: '',
            patient_img: '',
            patient_img_thumb: '',
            share_status: false,
            refby: '',
            emergency_contact_name: '',
            emergency_contact_number: '',
            smoking_habits: '',
            alcohol: '',
            food_preference: '',
            marital_status: '',
            occupation: '',
            search_patient_keyword: '',
            refer_other_doctor_id: '',
            refer_user_id: '',
            patient_smoke_habits: SMOKE,
            patient_alcohol: ALCOHOL
        }
    }
    $scope.patient.id_proof_img_temp = '';
    $scope.patient.caretaker_data = [];
    if(items.patient_caretaker_data != undefined && items.patient_caretaker_data.length > 0) {
        $scope.patient.patient_type = "2";
        $scope.patient.caretaker_data = items.patient_caretaker_data;
        angular.forEach($scope.patient.caretaker_data, function (val,key) {
            $scope.patient.caretaker_data[key].mapping_relation = parseInt(val.mapping_relation);
        });
    } else {
        $scope.patient.patient_type = "1";
    }
    $scope.patient.is_email_read_only = false;
    if(items.user_email != undefined && items.user_email != '' && items.user_email != null)
        $scope.patient.is_email_read_only = true;
    $scope.patient.is_mobile_read_only = false;
    if(items.user_phone_number != undefined && items.user_phone_number != '' && items.user_phone_number != null)
        $scope.patient.is_mobile_read_only = true;
    $scope.getCountry = function () {
        CommonService.getCountry('', function (response) {
            if (response.status == true) {
                $scope.patient_country = response.data;
            }
        });
    }
    if($scope.patient_country == undefined)
        $scope.getCountry();
    
    $scope.patient.cities = [];
    $scope.getPatientCity = function (state_id) {
        $scope.patient.cities = [];
        if(state_id > 0) {
            CommonService.getCity(state_id, true, function (response) {
                if (response.status == true) {
                    $scope.patient.cities = response.data;
                } else {
                    ngToast.danger(response.message);
                }
            });
        }
    }
    $scope.globalSearchForRefferedBy = function () {
        $rootScope.app.isLoader = false;
        PatientService
            .searchDoctor($scope.patient.search_patient_keyword, function (response) {
                if (response.status == true) {
                    $scope.search_result = response.data;
                }
            });
    }
    $scope.checkMobileKey = function (event) {
        if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
            event.preventDefault();
            return false;
        }
    }
    $scope.searchCaretaker = function () {
        $rootScope.app.isLoader = false;
        $scope.patient.caretaker_user_id = '';
        $scope.patient.caretaker_first_name = '';
        $scope.patient.caretaker_last_name = '';
        if($scope.patient.caretaker_mobile != undefined && $scope.patient.caretaker_mobile.length > 2) {
            PatientService
                .searchCaretaker($scope.patient.caretaker_mobile, function (response) {
                    if (response.status == true) {
                        $scope.search_caretaker_result = response.data;
                        if($scope.patient.caretaker_mobile.length == 10) {
                            $scope.patient.caretaker_user_id = $scope.search_caretaker_result[0].user_id;
                            $scope.patient.caretaker_first_name = $scope.search_caretaker_result[0].user_first_name;
                            $scope.patient.caretaker_last_name = $scope.search_caretaker_result[0].user_last_name;
                            $scope.patient.caretaker_mobile = $scope.search_caretaker_result[0].user_phone_number;
                            $scope.patient.is_invalid_caretaker_number = false;
                        }
                    } else {
                        if($scope.patient.caretaker_mobile.length == 10) {
                            $scope.patient.caretaker_user_id = '0';
                        }
                    }
                });
        }
    }
    $scope.patient.is_resend_otp = false;
    $scope.sendCaretakerOTP = function () {
        if($scope.patient.caretaker_mobile.length == 10) {
            var request = {
                    doctor_id: $rootScope.current_doctor.user_id,
                    mobile_no: $scope.patient.caretaker_mobile,
                    is_resend_otp: $scope.patient.is_resend_otp,
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    access_token: $rootScope.currentUser.access_token
                };
            PatientService
                .sendCaretakerOTP(request, function (response) {
                    if (response.status == true) {
                        $scope.patient.is_resend_otp = true;
                        ngToast.success({
                            content: response.message
                        });
                    } else {
                        ngToast.danger(response.message);
                    }
                });
        }
    }
    if($scope.patient.patient_state != undefined && $scope.patient.patient_state)
        $scope.getPatientCity($scope.patient.patient_state);

    $scope.get_medical_conditions();
    $scope.patient.current_patient_details_tab = 4;
    if(items.user_details_languages_known != undefined && items.user_details_languages_known != '' && items.user_details_languages_known != null) {
        var lang_id = items.user_details_languages_known.split(",");
        if(lang_id[0] != undefined)
            $scope.patient.patient_languages = lang_id[0];
    }
    if(items.user_details_chronic_diseases != undefined && items.user_details_chronic_diseases != '' && items.user_details_chronic_diseases != null) {
        var chronic_diseases = EncryptDecrypt.my_decrypt(items.user_details_chronic_diseases);
        $scope.patient.chronic_diseases_data = chronic_diseases.split(",");
    } else {
        $scope.patient.chronic_diseases_data = [];
    }

    if(items.user_details_food_allergies != undefined && items.user_details_food_allergies != '' && items.user_details_food_allergies != null) {
        var food_allergies = EncryptDecrypt.my_decrypt(items.user_details_food_allergies);
        $scope.patient.food_allergies_arr = [];
        angular.forEach(food_allergies.split(","), function (val) {
            $scope.patient.food_allergies_arr.push({value:val});
        });
    } else {
        $scope.patient.food_allergies_arr = [{value:''}];
    }
    if(items.user_details_medicine_allergies != undefined && items.user_details_medicine_allergies != '' && items.user_details_medicine_allergies != null) {
        var medicine_allergies = EncryptDecrypt.my_decrypt(items.user_details_medicine_allergies);
        $scope.patient.medicine_allergies_arr = [];
        angular.forEach(medicine_allergies.split(","), function (val) {
            $scope.patient.medicine_allergies_arr.push({value:val});
        });
    } else {
        $scope.patient.medicine_allergies_arr = [{value:''}];
    }
    if(items.user_details_other_allergies != undefined && items.user_details_other_allergies != '' && items.user_details_other_allergies != null) {
        var others_allergies = EncryptDecrypt.my_decrypt(items.user_details_other_allergies);
        $scope.patient.others_allergies_arr = [];
        angular.forEach(others_allergies.split(","), function (val) {
            $scope.patient.others_allergies_arr.push({value:val});
        });
    } else {
        $scope.patient.others_allergies_arr = [{value:''}];
    }
    if(items.user_details_injuries != undefined && items.user_details_injuries != '' && items.user_details_injuries != null) {
        var patient_injuries = EncryptDecrypt.my_decrypt(items.user_details_injuries);
        $scope.patient.patient_injuries_arr = [];
        angular.forEach(patient_injuries.split(","), function (val) {
            $scope.patient.patient_injuries_arr.push({value:val});
        });
    } else {
        $scope.patient.patient_injuries_arr = [{value:''}];
    }
    if(items.user_details_surgeries != undefined && items.user_details_surgeries != '' && items.user_details_surgeries != null) {
        var patient_surgeries = EncryptDecrypt.my_decrypt(items.user_details_surgeries);
        $scope.patient.patient_surgeries_arr = [];
        angular.forEach(patient_surgeries.split(","), function (val) {
            $scope.patient.patient_surgeries_arr.push({value:val});
        });
    } else {
        $scope.patient.patient_surgeries_arr = [{value:''}];
    }
    $scope.patient.patient_family_history = [];
    if(items.patient_family_history != undefined && items.patient_family_history.length > 0) {
        angular.forEach(items.patient_family_history, function (val,key) {
            $scope.patient.patient_family_history.push({
                relation: parseInt(val.family_medical_history_relation),
                medical_condition: val.family_medical_history_medical_condition_id.split(","),
                history_date: new Date(val.family_medical_history_date),
                comments: val.family_medical_history_comment
            });
        });
    } else {
        $scope.patient.patient_family_history = [{relation:'',medical_condition:'',history_date:'',comments:''}];
    }
    var user_details_activity_days = [];
    if(items.user_details_activity_days != undefined && items.user_details_activity_days != null && items.user_details_activity_days !='') {
        user_details_activity_days = items.user_details_activity_days.split(",");
    }
    var user_details_activity_hours = [];
    if(items.user_details_activity_hours != undefined && items.user_details_activity_hours != null && items.user_details_activity_hours !='') {
        user_details_activity_hours = items.user_details_activity_hours.split(",");
    }
    var user_details_activity_level = [];
    if(items.user_details_activity_level != undefined && items.user_details_activity_level != null && items.user_details_activity_level !='') {
        user_details_activity_level = items.user_details_activity_level.split(",");
    }
    $scope.patient.patient_activity_levels = [];
    if(user_details_activity_level != undefined && user_details_activity_level.length > 0) {
        angular.forEach(user_details_activity_level, function (val,key) {
            $scope.patient.patient_activity_levels.push({
                activity_levels: val.trim(), 
                activity_days: (user_details_activity_days[key] != undefined) ? user_details_activity_days[key] : '', 
                activity_hours: (user_details_activity_hours[key] != undefined) ? user_details_activity_hours[key] : ''
            });
        });
    } else {
        $scope.patient.patient_activity_levels = [{activity_levels:'', activity_days: '', activity_hours: ''}];
    }
    $scope.blankLatLong = function () {
        $scope.patient.lat = '';
        $scope.patient.lng = '';
    }
    $scope.heightValidation = function (form, type) {
        if (type == 1) {
            if ($scope.patient.patient_height) {
                var height = $scope.patient.patient_height;
                if (height <= 0 || height > 333) {
                    form.height.$setValidity("pattern", false);
                }
            }
        } else {
            if ($scope.patient.patient_weight) {
                var weight = $scope.patient.patient_weight;
                if (weight <= 0 || weight > 200) {
                    form.weight.$setValidity("pattern", false);
                }
            }
        }
    }
    $scope.updatePatientBirthDate = function () {
        if($scope.patient.patient_age != undefined && $scope.patient.patient_age != '') {
            var current_date = new Date();
            var year = current_date.getFullYear();
            var month = 6;
            var day = 1;
            var birth_date = new Date(year-$scope.patient.patient_age, month, day);
            $scope.patient.patient_bdate = birth_date;
        } else {
            $scope.patient.patient_bdate = '';
        }
    }
    $scope.removeProfile = function (type) {
        if (type == 1) {
            $scope.patient.patient_img_temp = '';
        }
    };
    $scope.changeGender = function (gender, is_caregiver) {
        if(is_caregiver) {
            $scope.patient.caretaker_gender = gender;
        } else {
            $scope.patient.gender = gender;
            $scope.patient.share_status = false;
        }
    }
    $scope.is_required_id_type = function () {
        if($scope.patient.id_proof_detail != undefined && $scope.patient.id_proof_detail != '') {
            return true;
        } else {
            return false;
        }
    }
    $scope.patienDontDisclose = function () {
        $scope.patient.gender = '';
    }
    $scope.changePatientDetailsTab = function (tab) {
        $scope.patient.current_patient_details_tab = tab;
        $rootScope.updateBackDropModalHeight('add_patient_modal');
    }
    $scope.addEditPatient = function () {
        $scope.submitted = true;
        $scope.patient.is_invalid_caretaker_number = false;
        if($scope.patient.caretaker_mobile !=undefined && $scope.patient.caretaker_mobile.length != 10) {
            $scope.patient.is_invalid_caretaker_number = true;
            $scope.patient.current_patient_details_tab = 4;
            return false;
        }
        var food_allergies = '';
        angular.forEach($scope.patient.food_allergies_arr, function (val, key) {
            if(val.value != ''){
                if(key > 0)
                    food_allergies += ',';
                food_allergies += val.value;
            }
        });
        $scope.patient.food_allergies = '';
        if(food_allergies != '')
            $scope.patient.food_allergies = EncryptDecrypt.my_encrypt(food_allergies);

        var medicine_allergies = '';
        angular.forEach($scope.patient.medicine_allergies_arr, function (val, key) {
            if(val.value != ''){
                if(key > 0)
                    medicine_allergies += ',';
                medicine_allergies += val.value;
            }
        });
        $scope.patient.medicine_allergies = '';
        if(medicine_allergies != '')
            $scope.patient.medicine_allergies = EncryptDecrypt.my_encrypt(medicine_allergies);

        var other_allergies = '';
        angular.forEach($scope.patient.others_allergies_arr, function (val, key) {
            if(val.value != ''){
                if(key > 0)
                    other_allergies += ',';
                other_allergies += val.value;
            }
        });
        $scope.patient.other_allergies = '';
        if(other_allergies != '')
            $scope.patient.other_allergies = EncryptDecrypt.my_encrypt(other_allergies);

        var patient_injuries = '';
        angular.forEach($scope.patient.patient_injuries_arr, function (val, key) {
            if(val.value != ''){
                if(key > 0)
                    patient_injuries += ',';
                patient_injuries += val.value;
            }
        });
        $scope.patient.injuries = '';
        if(patient_injuries != '')
            $scope.patient.injuries = EncryptDecrypt.my_encrypt(patient_injuries);

        var patient_surgeries = '';
        angular.forEach($scope.patient.patient_surgeries_arr, function (val, key) {
            if(val.value != ''){
                if(key > 0)
                    patient_surgeries += ',';
                patient_surgeries += val.value;
            }
        });
        $scope.patient.surgeries = '';
        if(patient_surgeries != '')
            $scope.patient.surgeries = EncryptDecrypt.my_encrypt(patient_surgeries);

        var chronic_diseases = '';
        angular.forEach($scope.patient.chronic_diseases_data, function (val, key) {
            if(val != '') {
                if(key > 0)
                    chronic_diseases += ',';
                chronic_diseases += val;
            }
        });
        $scope.patient.chronic_diseases = '';
        if(chronic_diseases != '')
        $scope.patient.chronic_diseases = EncryptDecrypt.my_encrypt(chronic_diseases);
        if($scope.patient.country_id != undefined && $scope.patient_country != undefined){
            var selectedCountryObj = $filter('filter')($scope.patient_country, {'country_id':$scope.patient.country_id},true);
            if(selectedCountryObj != undefined && selectedCountryObj.length > 0){
                $scope.patient.country_id_text = selectedCountryObj[0].country_name;
            }
        }
        if($scope.patient.patient_state != undefined && $rootScope.states != undefined){
            var selectedStateObj = $filter('filter')($rootScope.states, {'state_id':$scope.patient.patient_state},true);
            if(selectedStateObj != undefined && selectedStateObj.length > 0){
                $scope.patient.state_id_text = selectedStateObj[0].state_name;
            }
        }
        if($scope.patient.patient_city != undefined && $scope.patient.cities != undefined){
            var selectedCityObj = $filter('filter')($scope.patient.cities, {'city_id':$scope.patient.patient_city},true);
            if(selectedCityObj != undefined && selectedCityObj.length > 0){
                $scope.patient.city_id_text = selectedCityObj[0].city_name;
            }
        }
        if ($scope.patientRegisterForm.$valid) {
            $rootScope.gTrack('add_new_patient');
            var month = $scope.patient.patient_bdate.getMonth() + 1; //months from 1-12
            var day = $scope.patient.patient_bdate.getDate();
            var year = $scope.patient.patient_bdate.getFullYear();
            $scope.patient.patient_bdate_new = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
            
            if ($scope.patient.patient_img_temp == '' || $scope.patient.patient_img_temp == undefined) {
                $scope.patient.patient_img_temp = '';
            }
            if ($scope.patient.refby == '' || $scope.patient.refby == undefined) {
                $scope.patient.refby = $scope.patient.search_patient_keyword;
            }
            $rootScope.app.isLoader = true;
            if ($scope.patient.occupation != undefined && $scope.patient.occupation == 'Others') {
                $scope.patient.occupation = $scope.patient.occupation_other;
            }
            PatientService
                    .addPatient($scope.patient, function (response) {
                        if (response.status == true) {
                            $scope.patient_id = response.patient_id;
                            var patient_msg = response.message;
                            $rootScope.app.isLoader = true;
                            //upload profile pic
                            PatientService
                                    .upload_profile($scope.file, $scope.patient_id, $scope.id_proof_file, function (response) {
                                        $rootScope.app.isLoader = false;
                                        ngToast.success({
                                            content: patient_msg,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        if($scope.patient != undefined && $scope.patient.patient_id != undefined && $scope.patient.patient_id != '') {
                                            var patient_data = {
                                                patient_id : $scope.patient.patient_id,
                                                user_first_name : $scope.patient.patient_first_name,
                                                user_last_name : $scope.patient.patient_last_name,
                                                user_photo_filepath_thumb : (response.user_data != undefined && response.user_data.user_photo_filepath_thumb != undefined) ? response.user_data.user_photo_filepath_thumb : ''
                                            };
                                            $scope.close(patient_data);
                                        } else {
                                            $scope.cancel();
                                        }
                                    });
                        } else {
                            $rootScope.app.isLoader = false;
                            ngToast.danger(response.message);
                        }
                    });
        } else {
            var errors = [];
            var first_tab_fields = ["weight", "height", "pincode", "emergency_contact_number", "id_proof_type", "id_proof_detail"];
            var second_tab_fields = ["innerFamilyHistoryForm", "innerInjuriesAllergiesForm", "innerSurgeriesAllergiesForm"];
            var third_tab_fields = ["innerActivityLevelsForm"];
            var forth_tab_fields = ["fname","lname","mobile","date","email","gender","caretaker_mobile","caretakerfname","caretakerlname","caregiver_email","caretaker_relation","caretaker_gender","caretaker_age","user_patient_id"];
            var fifth_tab_fields = ["innerFoodAllergiesForm", "innerMedicineAllergiesForm", "innerOthersAllergiesForm"];
            for (var key in $scope.patientRegisterForm.$error) {
                for (var index = 0; index < $scope.patientRegisterForm.$error[key].length; index++) {
                    errors.push($scope.patientRegisterForm.$error[key][index].$name);
                }
            }
            angular.forEach(fifth_tab_fields, function (value) {
                if(errors.indexOf(value) > -1) {
                    $scope.patient.current_patient_details_tab = 5;
                }
            });
            angular.forEach(second_tab_fields, function (value) {
                if(errors.indexOf(value) > -1) {
                    $scope.patient.current_patient_details_tab = 2;
                }
            });
            angular.forEach(third_tab_fields, function (value) {
                if(errors.indexOf(value) > -1) {
                    $scope.patient.current_patient_details_tab = 3;
                }
            });
            angular.forEach(first_tab_fields, function (value) {
                if(errors.indexOf(value) > -1) {
                    $scope.patient.current_patient_details_tab = 1;
                }
            });
            angular.forEach(forth_tab_fields, function (value) {
                if(errors.indexOf(value) > -1) {
                    $scope.patient.current_patient_details_tab = 4;
                }
            });
        }
    }
    $scope.addMoreFoodAllergy = function(isAdd, key) {
        if(isAdd){
            $scope.patient.food_allergies_arr.push({value:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.food_allergies_arr.splice(key, 1);
        }
    }
    $scope.addMoreMedicineAllergy = function(isAdd, key) {
        if(isAdd){
            $scope.patient.medicine_allergies_arr.push({value:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.medicine_allergies_arr.splice(key, 1);
        }
    }
    $scope.addMoreOthersAllergy = function(isAdd, key) {
        if(isAdd){
            $scope.patient.others_allergies_arr.push({value:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.others_allergies_arr.splice(key, 1);
        }
    }
    $scope.addMoreFamilyHistory = function(isAdd, key) {
        if(isAdd){
            $scope.patient.patient_family_history.push({relation:'',medical_condition:'',history_date:'',comments:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.patient_family_history.splice(key, 1);
        }
    }
    $scope.addMoreActivityLevel = function(isAdd, key) {
        if(isAdd){
            $scope.patient.patient_activity_levels.push({activity_levels:'', activity_days: '', activity_hours: ''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.patient_activity_levels.splice(key, 1);
        }
    }
    $scope.addMoreInjuries = function(isAdd, key) {
        if(isAdd){
            $scope.patient.patient_injuries_arr.push({value:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.patient_injuries_arr.splice(key, 1);
        }
    }
    $scope.addMoreSurgeries = function(isAdd, key) {
        if(isAdd){
            $scope.patient.patient_surgeries_arr.push({value:''});
            $rootScope.updateBackDropModalHeight('add_patient_modal');
        } else {
            $scope.patient.patient_surgeries_arr.splice(key, 1);
        }
    }
    $scope.close = function (patient_data) {
        $uibModalInstance.close(patient_data);
    };
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    $scope.isFamHisRequired = function (famHisObj, key) {
        if (!famHisObj[key].relation && !famHisObj[key].medical_condition && !famHisObj[key].history_date && !famHisObj[key].comments) {
            return false;
        } else {
            return true;
        }
    }
    $scope.isActLevRequired = function (actLevObj, key) {
        if (!actLevObj[key].activity_levels && !actLevObj[key].activity_days && !actLevObj[key].activity_hours) {
            return false;
        } else {
            return true;
        }
    }
    $scope.openFileObject = function (fileObj) {
        setTimeout(function () {
            document.getElementById(fileObj).click();
        }, 0);
    };
});