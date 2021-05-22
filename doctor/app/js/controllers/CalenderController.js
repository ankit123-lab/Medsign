/*
 * Controller Name: CalenderController
 * Use: This controller is used for calender menu activity
 */
angular.module("app.dashboard")
        .controller("CalenderController", function ($scope, AuthService, $rootScope, ngToast, CalenderService, SweetAlert, uiCalendarConfig, $filter) {
            var current_date = new Date();
            current_date.setHours(00);
            current_date.setMinutes(00);
            current_date.setSeconds(00);
            current_date.setMilliseconds(000);
			var year  = current_date.getFullYear();
            var month = current_date.getMonth();
            var day   = current_date.getDate();
            var common_calender_start_date = new Date(year, month, day);
			
            $scope.init = function () {
				$scope.block = {
                    calender_block_id: '',
					doctor_id: ($rootScope.current_doctor != undefined) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					clinic_id: ($rootScope.current_clinic != undefined) ? $rootScope.current_clinic.clinic_id : '',
                    type: '1',
                    start_date: '',
                    end_date: '',
					real_start_date: '',
                    real_end_date: '',
                    start_time: '',
                    end_time: '',
                    blockslot_date: '',
					real_blockslot_date: '',
					comment: '',
                    cancel_appointment: 2
                };
                $scope.submitted = false;
				$rootScope.isShowBlockCalForm = false;
            }

            $scope.init();
            $scope.addUpdateBlockCalenderSlot = function() {
				$scope.submitted = true;
                if ($scope.blockCalenderForm.$valid) {
                	if ($scope.block.type == "1") {
                        $scope.block.start_time = '';
                        $scope.block.end_time = '';
                        $scope.block.blockslot_date = '';
						$scope.block.real_blockslot_date = '';
						
						//$scope.block.start_date = $scope.block.start_date.getFullYear()+'-'+$scope.block.start_date.getMonth()+'-'+$scope.block.start_date.getDate();
						//$scope.block.end_date = $scope.block.end_date.getFullYear()+'-'+$scope.block.end_date.getMonth()+'-'+$scope.block.end_date.getDate();
						
						var month = $scope.block.start_date.getMonth() + 1;
						var day   = $scope.block.start_date.getDate();
						var year  = $scope.block.start_date.getFullYear();
						$scope.block.real_start_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');

						var month = $scope.block.end_date.getMonth() + 1;
						var day   = $scope.block.end_date.getDate();
						var year  = $scope.block.end_date.getFullYear();
						$scope.block.real_end_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
						
                    } else {
                        $scope.block.start_date = '';
                        $scope.block.end_date = '';
						$scope.block.real_start_date = '';
                        $scope.block.real_end_date = '';
						
						//$scope.block.blockslot_date = $scope.block.blockslot_date.getFullYear()+'-'+$scope.block.blockslot_date.getMonth()+'-'+$scope.block.blockslot_date.getDate();

						var month = $scope.block.blockslot_date.getMonth() + 1;
						var day   = $scope.block.blockslot_date.getDate();
						var year  = $scope.block.blockslot_date.getFullYear();
						$scope.block.real_blockslot_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');

						$scope.block.start_time = $scope.addZero($scope.block.start_time.getHours()) + ":" + $scope.addZero($scope.block.start_time.getMinutes());
						$scope.block.end_time = $scope.addZero($scope.block.end_time.getHours()) + ":" + $scope.addZero($scope.block.end_time.getMinutes());
                    }
					
					$scope.block.clinic_id = ($rootScope.current_clinic != undefined) ? $rootScope.current_clinic.clinic_id : '';
                    $scope.submitted = false;
                    CalenderService
                            .addBlockCalenderEntry($scope.block, function(response){
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.init();
                                    $("#modal_blockcalendar").modal('hide');
                                } else {
                                    if (response.have_appointment == 1) {
                                        SweetAlert.swal({
												title: response.message,
												text: "You will not be able to recover these appointments !",
												type: "warning",
												showCancelButton: true,
												confirmButtonColor: "#DD6B55",
												confirmButtonText: "Yes, delete it !",
												closeOnConfirm: false
											},
											function(isConfirm){                                                
                                                    if (!isConfirm)
															return;
														
                                                    $scope.block.cancel_appointment = 1;
                                                    CalenderService
                                                            .addBlockCalenderEntry($scope.block, function (response) {
                                                                if (response.status == true) {
                                                                    ngToast.success({
                                                                        content: response.message,
                                                                        timeout: 5000
                                                                    });
                                                                    SweetAlert.swal("Block calendar added.");
                                                                    $scope.init();
                                                                    $("#modal_blockcalendar").modal("hide");
                                                                    uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
                                                                }
                                                            });
                                            }
										);
                                    } else {
                                        ngToast.danger(response.message);
										if($('#modal_blockcalendar'))
											$('#modal_blockcalendar .modal-backdrop').height($("#modal_blockcalendar .modal-backdrop.in").height() + 300);
                                    }
                                }
                            });
                }else{
					if($('#modal_blockcalendar'))
						$('#modal_blockcalendar .modal-backdrop').height($("#modal_blockcalendar .modal-backdrop.in").height() + 300);
				}
            }
	
			$scope.addNewInit = function(){
				$scope.init();
				$rootScope.isShowBlockCalForm = true;
			}
			
			$scope.editBlockCalenderSlot = function(blockCalId){
				if(blockCalId == undefined || blockCalId == '') return;
				var selectedBlockDetails = $filter('filter')($scope.block_calender_data, {'calender_block_id':blockCalId},true);
				if(selectedBlockDetails.length <= 0){ return; }else{
					selectedBlockDetails = selectedBlockDetails[0];
				}
				$scope.block = {
                    //doctor_id: 			$rootScope.currentUser.user_id,
					doctor_id: 				($rootScope.current_doctor != undefined) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					clinic_id: 				($rootScope.current_clinic != undefined) ? $rootScope.current_clinic.clinic_id : '',
                    type: 				selectedBlockDetails.calender_block_duration_type,
                    start_date: 		selectedBlockDetails.calender_block_from_date,
                    end_date: 			selectedBlockDetails.calender_block_to_date,
                    start_time: 		selectedBlockDetails.calender_block_start_time,
                    end_time: 			selectedBlockDetails.calender_block_end_time,
                    blockslot_date: 	selectedBlockDetails.calender_block_from_date,
					calender_block_id:  selectedBlockDetails.calender_block_id,
					comment: 			selectedBlockDetails.calender_block_title,
                    cancel_appointment: 2
                };
				if ($scope.block.type == "2") {
					$scope.block.blockslot_date = new Date($scope.block.blockslot_date);
					if($scope.block.start_time != undefined && $scope.block.start_time != ''){
						var new_date = new Date();
						new_date.setHours($scope.block.start_time.slice(0, -6));
						new_date.setMinutes($scope.block.start_time.slice(3, -3));
						new_date.setSeconds(00);
						$scope.block.start_time = new_date;
					}
					if($scope.block.end_time != undefined && $scope.block.end_time != ''){
						var new_date = new Date();
						new_date.setHours($scope.block.end_time.slice(0, -6));
						new_date.setMinutes($scope.block.end_time.slice(3, -3));
						new_date.setSeconds(00);
						$scope.block.end_time = new_date;
					}
				} else {
					$scope.block.start_date = new Date($scope.block.start_date);
					$scope.block.end_date   = new Date($scope.block.end_date);
				}
				$rootScope.isShowBlockCalForm = true;
			}
			$scope.familyHistoryShow = function (event) {
                
            }
			$scope.deleteBlockCalenderSlot = function(blockCalId){
				if(blockCalId == undefined || blockCalId == '') return;
				var selectedBlockDetails = $filter('filter')($scope.block_calender_data, {'calender_block_id':blockCalId},true);
				if(selectedBlockDetails.length <= 0){ return;}else{
					selectedBlockDetails = selectedBlockDetails[0];
				}
				$scope.block = {
                    //doctor_id: 	$rootScope.currentUser.user_id,
					doctor_id:	($rootScope.current_doctor != undefined) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					clinic_id: 	($rootScope.current_clinic != undefined) ? $rootScope.current_clinic.clinic_id : '',
                    type: 		selectedBlockDetails.calender_block_duration_type,
                    start_date: selectedBlockDetails.calender_block_from_date,
                    end_date: 	selectedBlockDetails.calender_block_to_date,
                    start_time: selectedBlockDetails.calender_block_start_time,
                    end_time: 	selectedBlockDetails.calender_block_end_time,
                    blockslot_date: 	selectedBlockDetails.calender_block_from_date,
					calender_block_id:  selectedBlockDetails.calender_block_id,
					comment: 	selectedBlockDetails.calender_block_title,
                    cancel_appointment: 2
                };
				SweetAlert.swal({
						title: 'Block calendar',
						text: "Are you sure you want to remove this block calander details? After this your calendar will be available for appointment booking.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, Remove it!",
						closeOnConfirm: false
					},
					function(isConfirm){                                                   
							if (!isConfirm){
								$scope.init();
								return;
							}				
							$scope.block.cancel_appointment = 1;
							CalenderService
									.deleteBlockCalenderEntry($scope.block, function (response) {
										if (response.status == true) {
											ngToast.success({
												content: response.message,
												timeout: 5000
											});
											SweetAlert.swal("Block calendar details removed.");
											$scope.init();
											$("#modal_blockcalendar").modal("hide");
											uiCalendarConfig.calendars['myCalendar'].fullCalendar('refetchEvents');
										}
									});
					}
				);
			}
			
			$scope.common_calender = {
				datepickerOptions: {
                    minDate: common_calender_start_date
                },
                timepickerOptions: {
                    readonlyInput: false,
                    //showMeridian: true,
					showMeridian: ($rootScope.currentUser.hour_format == '1') ? true : false,
                    minuteStep: 5
                },
				open: false
            };
            $scope.enddate_calender = {
                datepickerOptions: {
                    minDate: common_calender_start_date
                },
                timepickerOptions: {
                    readonlyInput: false,
                    showMeridian: true,
                },
				open: false
            };
            $scope.enddtimepicker_calender = {
                timepickerOptions: {
                    readonlyInput: false,
                    //showMeridian: true,
                    //min: null
					showMeridian: ($rootScope.currentUser.hour_format == '1') ? true : false,
                    minuteStep: 5
                },
            };

            var unwatchMinMaxValues = $scope.$watch(function () {
				if($scope.block != undefined) 
					return [$scope.block.start_date, $scope.block.start_time];
				else
					return [];
            }, function () {
				if($scope.block == undefined)
					return ;
				
				if($scope.block.calender_block_id != undefined && $scope.block.calender_block_id != ''){
					//$scope.block.end_date = '';
					//$scope.block.end_time = '';
					$scope.enddate_calender.datepickerOptions.minDate = $scope.block.start_date;
					$scope.enddtimepicker_calender.timepickerOptions.min = $scope.block.start_time;
				}else{
					$scope.block.end_date = '';
					// $scope.block.end_time = '';
					$scope.enddate_calender.datepickerOptions.minDate = $scope.block.start_date;
					$scope.enddtimepicker_calender.timepickerOptions.min = $scope.block.start_time;
				}
            }, true);

            // destroy watcher
            $scope.$on('$destroy', function () {
                unwatchMinMaxValues();
            });
        });