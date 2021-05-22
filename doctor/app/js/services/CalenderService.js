angular
        .module("medeasy")
        .service('CalenderService', function ($rootScope, $http, $filter) {
            this.addBlockCalenderEntry = function (block_data, callback) {
				var cUrl = 'add_block_calendar';
				if(block_data.calender_block_id != undefined && block_data.calender_block_id != ''){
					cUrl = 'edit_block_calendar';
				}else{
					block_data.calender_block_id = '';
				}
				
				$http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/"+cUrl,
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: block_data.doctor_id,
						clinic_id: block_data.clinic_id,
                        block_type: block_data.type,
                        start_date: block_data.real_start_date,
                        end_date: block_data.real_end_date,
                        start_time: block_data.start_time,
                        end_time: block_data.end_time,
                        blockslot_date: block_data.real_blockslot_date,
                        details: block_data.comment,
                        cancel_appointment: block_data.cancel_appointment,
						calender_block_id: block_data.calender_block_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

			/* get block calendar details */
			this.getBlockCalendarSlot = function (request, callback) {
				$http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_block_calendar_slot",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
			};
			
			/* delete block calendar details */
			this.deleteBlockCalenderEntry = function (request, callback) {
				$http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_block_calendar",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
						calender_block_id: request.calender_block_id 
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
			};
			
            /* get appointment list */
            this.getAppointmentList = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_appointments_list",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        start_date: request.start,
                        end_date: request.end,
                        clinic_id_arr: request.clinic_id_arr,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };

            /* add new appointment flow */
            this.addNewAppointment = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/confirm_appointment",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        date: request.real_appointment_date,
                        start_time: request.from_time.start_time,
                        end_time: request.from_time.end_time,
                        doctor_availability_id: request.from_time.doctor_availability_id,
                        appointment_type: request.appointment_type,
                        is_new_patient: request.is_new_patient,
                        otp: request.otp,
                        device_type: 'web',
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            }

            /* Appointments Book Send Otp flow */
            this.appointmentsBookSendOtp = function (request, callback) {
                var is_resend_otp = '';
                if(request.is_resend_otp != undefined) {
                    is_resend_otp = request.is_resend_otp;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/appointments_book_send_otp",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        patient_phone_number: request.patient_mob,
                        is_resend_otp : is_resend_otp,                        
                        device_type: 'web',
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            }

            this.cancelAppointment = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/cancel_appointment",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        patient_id: request.patient_id,
                        appointment_id: request.cancel_appointment_id,
                        access_token: $rootScope.currentUser.access_token,
                        device_type: 'web',
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {

                });
            };

            this.editExistingAppointment = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/reschedule_appointment",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.current_doctor.user_id,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        appointment_type: request.appointment_type,
                        date: request.real_appointment_date,
                        start_time: request.from_time.start_time,
                        end_time: request.from_time.end_time,
                        doctor_availability_id: request.from_time.doctor_availability_id,
                        device_type: 'web',
                        clinic_id: request.clinic_id,
                        user_type: 2
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            }

            this.getTimeSlots = function (request, callback) {
                var appointment_type = 1;
                if (request.appointment_type) {
                    appointment_type = request.appointment_type;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_availability",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        device_type: 'web',
                        appointment_type: appointment_type
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    //console.log(error);
                });
            };
        });