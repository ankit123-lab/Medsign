angular
        .module("medeasy")
        .service('ClinicService', function ($localStorage, AuthService, $rootScope, $http) {

            /* add new clinic function */
            this.addNewClinic = function (request, callback) {
                var clinic_address = '';
                if (request.is_copied == true) {
                    clinic_address = request.clinic_address;
                } else {
					if(angular.isObject(request.clinic_address)){
                        clinic_address = (request.clinic_address != undefined && request.clinic_address != '' && request.clinic_address.getPlace() != undefined) ? request.clinic_address.getPlace().formatted_address : '';
                    } else {
                        clinic_address = (request.clinic_address != undefined && request.clinic_address != '') ? request.clinic_address:'';
                    }
                }
                var city_id = request.selected_clinic_city.city_id;
                var state_id = request.selected_clinic_state.state_id;
                var country_id = request.selected_clinic_country.country_id;
                if (request.is_from_usercontroller) {
                    city_id = request.selected_clinic_city;
                    state_id = request.selected_clinic_state;
                    country_id = request.selected_clinic_country;
                }
                var session_time2 = '';
                if (request.clinic_start_time2) {
                    session_time2 = request.clinic_start_time2 + ',' + request.clinic_end_time2;
                }

                var formData = new FormData();
                formData.append("doctor_id", $rootScope.currentUser.user_id);
                formData.append("clinic_name", request.clinic_name);
                formData.append("clinic_number", request.clinic_number);
                formData.append("clinic_address", clinic_address);
                formData.append("clinic_address1", request.clinic_address1);
                formData.append("clinic_address_latlong", ((request.cliniclat != undefined) ? request.cliniclat : '') + "," + ((request.cliniclng != undefined) ? request.cliniclng : ''));
                formData.append("clinic_email", request.clinic_email);
                formData.append("clinic_locality", (request.clinic_locality != undefined) ? request.clinic_locality : '');
                formData.append("clinic_city", city_id);
                formData.append("clinic_state", state_id);
                formData.append("clinic_country", country_id);
                formData.append("clinic_zipcode", request.clinic_zipcode);
                formData.append("doctor_clinic_mapping_duration", request.duration_mint);
                formData.append("clinic_consultation_charges", (request.clinicCharge != undefined) ? request.clinicCharge : '');
                formData.append("clinic_tele_consultation_charges", (request.clinicTeleCharge != undefined) ? request.clinicTeleCharge : '');
                formData.append("clinic_session_time_1", request.clinic_start_time + ',' + request.clinic_end_time);
                formData.append("clinic_session_time_2", session_time2);
                formData.append("clinic_availability_type", 1);
                formData.append("access_token", $localStorage.currentUser.access_token);
                formData.append("user_id", $localStorage.currentUser.user_id);
				if(request.clinic_file != undefined && request.clinic_file != '')
					formData.append("clinic_logo_image", request.clinic_file);
				if(request.clinic_outside_file != undefined && request.clinic_outside_file != '')
					formData.append("clinic_out_side_area_image", request.clinic_outside_file);
                if(request.clinic_waiting_file != undefined && request.clinic_waiting_file != '')
					formData.append("clinic_waiting_area_image", request.clinic_waiting_file);
				if(request.clinic_reception_file != undefined && request.clinic_reception_file != '')
					formData.append("clinic_reception_area_image", request.clinic_reception_file);
                if(request.clinic_address_proof_file != undefined && request.clinic_address_proof_file != '')
					formData.append("clinic_address_image", request.clinic_address_proof_file);
				
                var new_clinic_services = '';
                for (var i = 0; i < request.clinic_service.length; i++) {
                    new_clinic_services += request.clinic_service[i].text;
                    if (i != (request.clinic_service.length - 1)) {
                        new_clinic_services += ',';
                    }
                }
                formData.append("clinic_services", new_clinic_services);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_clinic",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            /* edit clinic function */
            this.editClinic = function (request, callback) {

                var clinic_address = '';
                if (request.is_copied == true) {
                    clinic_address = request.clinic_address;
                } else {
					if(request.clinic_address != undefined && angular.isObject(request.clinic_address) && request.clinic_address.getPlace() != undefined){
						clinic_address = request.clinic_address.getPlace().formatted_address;
                    } else {
                        clinic_address = (request.clinic_address != undefined && request.clinic_address != '') ? request.clinic_address:'';
                    }
                }
                var city_id = '';
                var state_id = '';
                var country_id = '';
                if (request.is_from_usercontroller) {
                    city_id = request.selected_clinic_city;
                    state_id = request.selected_clinic_state;
                    country_id = request.selected_clinic_country;
                } else {
                    city_id = request.selected_clinic_city.city_id;
                    state_id = request.selected_clinic_state.state_id;
                    country_id = request.selected_clinic_country.country_id;
                }
                var session_time2 = '';
                if (request.clinic_start_time2) {
                    session_time2 = request.temp_clinic_start_time2 + ',' + request.temp_clinic_end_time2;
                }

                var formData = new FormData();
                formData.append("doctor_id", $rootScope.currentUser.user_id);
                formData.append("clinic_name", request.clinic_name);
                formData.append("clinic_number", request.clinic_number);
                formData.append("clinic_address_id", request.clinic_address_id);
                formData.append("clinic_address1", request.clinic_address1);
                formData.append("clinic_address", clinic_address);
                formData.append("clinic_address_latlong", request.cliniclat + "," + request.cliniclng);
                formData.append("clinic_email", request.clinic_email);
                formData.append("clinic_locality", request.clinic_locality);
                formData.append("clinic_city", city_id);
                formData.append("clinic_state", state_id);
                formData.append("clinic_country", country_id);
                formData.append("clinic_zipcode", request.clinic_zipcode);
                formData.append("doctor_clinic_mapping_duration", request.duration_mint);
                formData.append("clinic_consultation_charges", (request.clinicCharge != undefined) ? request.clinicCharge : '');
                formData.append("clinic_tele_consultation_charges", (request.clinicTeleCharge != undefined) ? request.clinicTeleCharge : '');
                formData.append("clinic_session_time_1", request.temp_clinic_start_time + ',' + request.temp_clinic_end_time);
                formData.append("clinic_session_time_2", session_time2);
                formData.append("clinic_availability_type", 1);
                formData.append("access_token", $localStorage.currentUser.access_token);
                formData.append("user_id", $localStorage.currentUser.user_id);
				formData.append("clinic_id", request.clinic_id);
				if(request.clinic_file != undefined && request.clinic_file != '')
					formData.append("clinic_logo_image", request.clinic_file);
				if(request.clinic_outside_file != undefined && request.clinic_outside_file != undefined)
					formData.append("clinic_out_side_area_image", request.clinic_outside_file);
				if(request.clinicOutsideImageSrcId != undefined && request.clinicOutsideImageSrcId != undefined)
					formData.append("clinic_out_side_area_image_id", request.clinicOutsideImageSrcId);
				if(request.clinic_waiting_file != undefined && request.clinic_waiting_file!='')
					formData.append("clinic_waiting_area_image", request.clinic_waiting_file);
				if(request.clinicWaitingImageSrcId != undefined && request.clinicWaitingImageSrcId != '')
					formData.append("clinic_waiting_area_image_id", request.clinicWaitingImageSrcId);
				if(request.clinic_reception_file != undefined && request.clinic_reception_file != '')
					formData.append("clinic_reception_area_image", request.clinic_reception_file);
                if(request.clinicReceptionImageSrcId != undefined && request.clinicReceptionImageSrcId != '')
					formData.append("clinic_reception_area_image_id", request.clinicReceptionImageSrcId);
				if(request.clinic_address_proof_file != undefined && request.clinic_address_proof_file != '')
					formData.append("clinic_address_image", request.clinic_address_proof_file);
				if(request.clinicAddressImageSrcId != undefined && request.clinicAddressImageSrcId!='')
					formData.append("clinic_address_image_id", request.clinicAddressImageSrcId);
				
				var new_clinic_services = '';
                for (var i = 0; i < request.clinic_service.length; i++) {
                    new_clinic_services += request.clinic_service[i].text;
                    if (i != (request.clinic_service.length - 1)) {
                        new_clinic_services += ',';
                    }
                }

                formData.append("clinic_services", new_clinic_services);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_clinic",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
			
            /* get created by clinics */
            this.getDoctorClinics = function (user_id, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctors_clinics",
                    data: {
                        "user_id": $rootScope.currentUser.user_id,
                        "doctor_id": user_id,
                        "access_token": AuthService.currentUser().access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            this.getDoctorClinicsRole = function (user_id, callback, errorFunction) {

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_clinic_list",
                    data: {
                        "user_id": $rootScope.currentUser.user_id,
                        "doctor_id": user_id,
                        "access_token": AuthService.currentUser().access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };

            this.getClinicDetail = function (clinic_id, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_clinic_detail",
                    data: {
                        "user_id": $rootScope.currentUser.user_id,
                        "clinic_id": clinic_id,
                        "access_token": AuthService.currentUser().access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
        });