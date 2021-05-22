angular
        .module("medeasy")
        .service('PatientService', function ($rootScope, $http, $filter, SMOKE, ALCOHOL, APPOINTMENT_TYPE, bmiFilter, kgToPoundFilter, EncryptDecrypt) {

            /* add new patient function */
            this.addPatient = function (request, callback) {
                if (request.gender == 1) {
                    request.gender = 'male';
                } else if (request.gender == 2) {
                    request.gender = 'female';
                } else if (request.gender == 3) {
                    request.gender = 'other';
                } else {
                    request.gender = 'undisclosed';
                }
                if (request.caretaker_gender != undefined && request.caretaker_gender == 1) {
                    request.caretaker_gender = 'male';
                } else if (request.caretaker_gender != undefined && request.caretaker_gender == 2) {
                    request.caretaker_gender = 'female';
                }
                var formData = new FormData();
                formData.append("email", (request.patient_email != undefined) ? request.patient_email : '');
                formData.append("country_code", '+91');
                formData.append("phone_number", request.patient_mob_number);
                formData.append("first_name", request.patient_first_name);
                formData.append("last_name", request.patient_last_name);
                formData.append("caretaker_mobile", (request.caretaker_mobile != undefined) ? request.caretaker_mobile : '');
                formData.append("user_patient_id", (request.user_patient_id != undefined) ? request.user_patient_id : '');
                formData.append("caretaker_user_id", (request.caretaker_user_id != undefined) ? request.caretaker_user_id : '');
                formData.append("caretaker_first_name", (request.caretaker_first_name != undefined) ? request.caretaker_first_name : '');
                formData.append("caretaker_last_name", (request.caretaker_last_name != undefined) ? request.caretaker_last_name : '');
                formData.append("caretaker_gender", (request.caretaker_gender != undefined) ? request.caretaker_gender : '');
                formData.append("caregiver_patient_email", (request.caregiver_patient_email != undefined) ? request.caregiver_patient_email : '');
                formData.append("caretaker_age", (request.caretaker_age != undefined) ? request.caretaker_age : '');
                formData.append("patient_type", request.patient_type);
                formData.append("caretaker_relation", (request.caretaker_relation != undefined) ? request.caretaker_relation : '');
                formData.append("gender", request.gender);
                formData.append("dob", (request.patient_bdate_new));
                formData.append("languages", (request.patient_languages != undefined) ? request.patient_languages : '');
                formData.append("blood_group", (request.patient_blood_group != undefined) ? request.patient_blood_group : '');
                formData.append("user_type", 1);
                formData.append("weight", (request.patient_weight != undefined && request.patient_weight!='') ? $filter('kgToPound')(request.patient_weight) : 0);
                formData.append("height", (request.patient_height != undefined) ? request.patient_height : 0);
                formData.append("occupation", (request.occupation != undefined && request.occupation != '') ? request.occupation : '');
                formData.append("id_proof_type", (request.id_proof_type != undefined && request.id_proof_type != '') ? request.id_proof_type : '');
                formData.append("id_proof_detail", (request.id_proof_detail != undefined && request.id_proof_detail != '') ? request.id_proof_detail : '');
                if(angular.isObject(request.patient_address)) {
                    formData.append("address", (request.patient_address != undefined && request.patient_address != '' && request.patient_address.getPlace() != undefined) ? (request.patient_address.getPlace().formatted_address):'');
                } else {
                    formData.append("address", (request.patient_address != undefined && request.patient_address != '') ? request.patient_address:'');
                }
                formData.append("address1", (request.patient_address1 != undefined) ? request.patient_address1 : '');
                formData.append("city_id", (request.patient_city != undefined && request.patient_city != '') ? request.patient_city : '');
                formData.append("state_id",(request.patient_state != undefined && request.patient_state != '') ? request.patient_state : '');
                formData.append("country_id", 101);
                formData.append("pincode",  (request.patient_picode != undefined) ? request.patient_picode : '');
                formData.append("locality",  (request.locality != undefined) ? request.locality : '');
                formData.append("latitude", (request.lat!=undefined) ? request.lat : '');
                formData.append("longitude",(request.lng!=undefined) ? request.lng : '');
                formData.append("share_status", (request.share_status) ? request.share_status : '');
                formData.append("referred_by", (request.refby) ? request.refby : '');
                formData.append("user_id", $rootScope.currentUser.user_id);
                if($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id != undefined && $rootScope.current_doctor.user_id > 0)
                    formData.append("doctor_id", $rootScope.current_doctor.user_id);
                else
                    formData.append("doctor_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("emergency_contact_name", request.emergency_contact_name);
                formData.append("emergency_contact_number", request.emergency_contact_number);
                formData.append("marital_status", request.marital_status);
                formData.append("food_allergies", request.food_allergies);
                formData.append("medicine_allergies", request.medicine_allergies);
                formData.append("other_allergies", request.other_allergies);
                var patient_family_history = [];
                if(request.patient_family_history != undefined && request.patient_family_history.length > 0) {
                    angular.forEach(request.patient_family_history, function (value, key) {
                        if(value.history_date != undefined && value.history_date != '') {
                            var month = value.history_date.getMonth() + 1;
                            var day = value.history_date.getDate();
                            var year = value.history_date.getFullYear();
                            var history_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                            patient_family_history.push({
                                'medical_condition': value.medical_condition,
                                'relation': value.relation,
                                'history_date': history_date,
                                'comments': value.comments
                            });
                        }
                    });
                }
                formData.append("patient_family_history", JSON.stringify(patient_family_history));
                formData.append("patient_activity_levels", JSON.stringify(request.patient_activity_levels));
                formData.append("chronic_diseases", request.chronic_diseases);
                formData.append("surgeries", request.surgeries);
                formData.append("injuries", request.injuries);
                formData.append("smoking_habits", request.smoking_habits);
                formData.append("alcohol", request.alcohol);
                formData.append("food_preference", request.food_preference);
                formData.append("patient_id", request.patient_id);
                formData.append("country_id_text",(request.country_id_text!=undefined) ? request.country_id_text : '');
                formData.append("state_id_text",(request.state_id_text!=undefined) ? request.state_id_text : '');
                formData.append("city_id_text",(request.city_id_text!=undefined) ? request.city_id_text : '');
                formData.append("refer_other_doctor_id",(request.refer_other_doctor_id!=undefined) ? request.refer_other_doctor_id : '');
                formData.append("refer_user_id",(request.refer_user_id!=undefined) ? request.refer_user_id : '');
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_patient",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
 
            /* patient upload profile */
            this.upload_profile = function (file, patient_id, id_proof_file, callback) {
				if((file == undefined || file == '') && (id_proof_file == undefined || id_proof_file == '')){
					callback({status:true});
					return true;
				}
				var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("other_user_id", patient_id);
                if(file != undefined || file != ''){
                    formData.append("photo", file);
                } else {
                    formData.append("photo", '');
                }
                if(id_proof_file != undefined || id_proof_file != ''){
                    formData.append("id_proof_file", id_proof_file);
                } else {
                    formData.append("id_proof_file", '');
                }
                formData.append("access_token", $rootScope.currentUser.access_token);
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

			this.updatePatientDob = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_patient_dob",
                    data: {
                        access_token:$rootScope.currentUser.access_token,
                        user_type: 	 2,
                        user_id: 	 $rootScope.currentUser.user_id,
                        doctor_id: 	 request.doctor_id,
						clinic_id: 	 request.clinic_id,
						patient_id:  request.patient_id,
						old_dob:     request.old_dob,
						updated_user_dob: request.updated_user_dob
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
			
			/********** Sunil Service Code ******************/
			
			this.getVideoCallToken = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_video_conf_token",
                    data: {
						user_id: 	 $rootScope.currentUser.user_id,
                        access_token:$rootScope.currentUser.access_token,                       
                        doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
						appointment_id: request.appointment_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
			
			
			this.generateURL = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/generate_video_url_patient",
                    data: {
                        user_id: 	 $rootScope.currentUser.user_id,
						access_token:$rootScope.currentUser.access_token, 
						doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
						appointment_id: request.appointment_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
			
			this.endVideoCall = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/end_video_conf_call",
                    data: {
                        user_id: 	 $rootScope.currentUser.user_id,
						access_token:$rootScope.currentUser.access_token, 
						doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
						appointment_id: request.appointment_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
            this.updateConnectionId = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_connection_id",
                    data: {
                        user_id:     $rootScope.currentUser.user_id,
                        access_token:$rootScope.currentUser.access_token, 
                        doctor_id: request.doctor_id,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        connection_id: request.connection_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            };
			
			/********** End Code *********************/
            
			/* patient search  */
            this.searchPatient = function (objPatientSearch, callback) {
				if(objPatientSearch.keyword == undefined || objPatientSearch.keyword == '' || objPatientSearch.keyword.length < 3)
					return false;
			
                var doctor_id = '';
                if ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id != undefined && $rootScope.current_doctor.user_id) {
                    doctor_id = $rootScope.current_doctor.user_id;
                } else {
                    doctor_id = $rootScope.currentUser.user_id;
                }
    			if(doctor_id == '')
                    return false;
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/search_patient",
                    data: {
                        search_text: objPatientSearch.keyword,
                        is_patient_from_gdb: objPatientSearch.is_patient_from_gdb,
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* doctor search  */
            this.searchDoctor = function (keyword, callback) {
                if(keyword == undefined || keyword == '')
                    return false;
            
                var doctor_id = '';
                if ($rootScope.current_doctor.user_id) {
                    doctor_id = $rootScope.current_doctor.user_id;
                }
                
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/search_doctor_list",
                    data: {
                        search: keyword,
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /* patient detail  */
            this.getPatientDetail = function (patient_id, callback) {


                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_user_details",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        other_user_id: patient_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            /*  html modal for patient detail*/
            this.getPatientModalHTML = function (event, callback) {
                var patient_modal = '';
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_appointed_patient_details",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        other_user_id: event.user_id
                    }
                }).then(function (response) {
                    if (response.data.status == true) {
                        var user_data = response.data.user_data;
                        var user_photo = user_data.user_photo_filepath;
                        if (user_photo == '') {
                            user_photo = "app/images/placeholder_user.png";
                        }
                        var surgery = '';
                        if (user_data.user_details_surgeries) {
                            surgery = EncryptDecrypt.my_decrypt(user_data.user_details_surgeries);
                        }
                        var kco_array_patient = user_data.kco.split('],');
                        var kco_string = [];
                        if (kco_array_patient) {
                            angular
                                    .forEach(kco_array_patient, function (value, key) {
                                        value = value.replace('[', '');
                                        value = value.replace(']', '');
                                        value = value.replace(/"/g, '');
                                        if (value) {
                                            kco_string += value;
                                            if (key != (kco_array_patient.length - 1)) {
                                                kco_string += ",";
                                            }
                                        }

                                    });
                        }
                        //user_data.kco_string = $filter('trimString')(kco_string, 20);
						user_data.kco_string = kco_string; 
                        var age = $filter('ageFilter')(user_data.user_details_dob);
                        var clinic_name_html = '';
                        if(event.clinic_name != undefined)
                            clinic_name_html = '<div class="row common_row text-center" style="color:#'+event.clinic_color_code+'"><h4 class="no_margin gotham_medium pop-data-name">'+event.clinic_name+'</h4></div>';
                        patient_modal = '<div class="outer_div outerdiv" id="outer_div" >' +
                                '                                            <div  class="inner_div_1 inner_div_1_left left_arrow_left left-arrow display_person_data1 pop-data" id="patient_data" style="z-index:20000" >' +
                                '                                                <a  ng-click="closeModal()" >' +
                                '                                                    <img src="app/images/popup_remove.png" alt="user_image" class="icon_image_right close_icon" id="close_icon" />' +
                                '                                                </a>' + clinic_name_html +
                                '                                                <div class="row common_row">' +
                                '                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 patient_detail_appointment">' +
                                '                                                        <img src="' + user_photo + '" alt="user_image"/>' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">' +
                                '                                                        <h4 class="no_margin gotham_medium user_redirect cursor_pointer pop-data-name">' + user_data.user_first_name + ' ' + user_data.user_last_name + '</h4>                             ' +
                                '                                                        <p class="col-lg-6 padding_0"><span class="title">Gender :</span> <span class="gotham_medium">' + user_data.user_gender + '</span></p>' +
                                '                                                        <p class="col-lg-6 padding_0"><span class="title">Age :</span> <span class="gotham_medium">' + age + '</span></p>' +
                                '                                                        <div class="clearfix"></div>' +
                                '                                                        <p><span class="title">Mobile no :</span> <span class="gotham_medium">' + user_data.user_phone_number + '</span></p>' +
                                '                                                        <p><span class="title">Email :</span> <span class="gotham_medium cursor_pointer" data-toggle="tooltip" title="' + user_data.user_email + '" data-placement="left" >' + $filter('trimString')(user_data.user_email, 30) + '</span></p>                            ' +
                                '                                                    </div>' +
                                '                                                </div>';
								if(user_data.user_details_emergency_contact_person != undefined && user_data.user_details_emergency_contact_person != '' && user_data.user_details_emergency_contact_number != undefined && user_data.user_details_emergency_contact_number != ''){
									patient_modal += '<div class="row common_row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><p>Emergency contact : <span class="gotham_medium">' + user_data.user_details_emergency_contact_person + '</span>, <span class="gotham_medium">' + user_data.user_details_emergency_contact_number + '</span></p></div></div>';
								}
								
								patient_modal += '<div class="row common_row">' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">' +
                                '                                                        <p>Surgeries : <span class="gotham_medium">' + surgery + '</span></p>' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">' +
                                '                                                        <div>' +
                                '                                                            <span class="title">Family history : </span>';
								angular.forEach(user_data.family_medical_history_data, function (family) {
                                    var relation = '';
                                    var selectObj = $filter('filter')($rootScope.familyRelation, {id: parseInt(family.family_medical_history_relation)},true);
                                    if (selectObj != undefined && selectObj[0] != undefined && selectObj[0].name != undefined) {
                                        relation = selectObj[0].name;
                                    }
                                    patient_modal += '<p class="cursor_pointer" data-toggle="tooltip" title="' + family.family_medical_history_medical_condition_id + '" data-placement="left" ><span class="gotham_medium family-history short-history">' + relation + ' - ' + $filter('trimString')(family.family_medical_history_medical_condition_id, 10) + '</span><span class="gotham_medium family-history full-history hide">' + relation + ' - ' + family.family_medical_history_medical_condition_id + '</span> <span class="family-history" style="font-size:14px !important;">(' + $filter('date')(new Date(family.family_medical_history_date), "dd-MMM-yyyy") + '),</span></p>';
                                });
                        var alcohole = '';
                        if (user_data.user_details_alcohol != '' && user_data.user_details_alcohol != '0') {
                            alcohole = ALCOHOL[user_data.user_details_alcohol - 1].name;
                        }

                        var smoking = '';
                        if (user_data.user_details_smoking_habbit != '' && user_data.user_details_smoking_habbit != 0) {
                            smoking = SMOKE[user_data.user_details_smoking_habbit - 1].name;
                        }
                        var start_time = event.appointment_from_time.slice(0, -3);
                        var end_time = event.appointment_to_time.slice(0, -3);
                        var start_hour = start_time.slice(0, -3);
                        var start_minutes = start_time.slice(-2);
                        var end_hour = end_time.slice(0, -3);
                        var end_minutes = end_time.slice(-2);
                        var startDate = new Date(0, 0, 0, start_hour, start_minutes);
                        var endDate = new Date(0, 0, 0, end_hour, end_minutes);
                        var millis = endDate - startDate;
                        var minutes = millis / 1000 / 60;
                        var duration = minutes + " mins";
                        if (minutes >= 60) {
                            duration = (minutes / 60).toFixed(2);
                            duration += " Hour(s)";
                        }
                        var bmi = $filter('bmi')(user_data.user_details_height, user_data.user_details_weight);

                        var food_alergy = '';
                        if (user_data.user_details_food_allergies) {
                            food_alergy = EncryptDecrypt.my_decrypt(user_data.user_details_food_allergies);
                        }
                        var medicine_alergy = '';
                        if (user_data.user_details_medicine_allergies) {
                            medicine_alergy = EncryptDecrypt.my_decrypt(user_data.user_details_medicine_allergies);
                        }
                        var other_alergy = '';
                        if (user_data.user_details_other_allergies) {
                            other_alergy = EncryptDecrypt.my_decrypt(user_data.user_details_other_allergies);
                        }

                        patient_modal += ' </div>' +
                                '                                                    </div>' +
                                '                                                </div>' +
                                '                                                <div class="hr"></div>' +
                                '                                                <div class="row common_row">' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">' +
                                '                                                        <span class="title">Blood group : </span>' +
                                '                                                        <p><span class="gotham_medium">' + user_data.user_details_blood_group + '</span></p>                            ' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col_right_border">' +
                                '                                                        <span class="title">Weight : </span>' +
                                '                                                        <p><span class="gotham_medium">' + $filter('PoundToKG')(user_data.user_details_weight) + '</span></p>                            ' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col_right_border">' +
                                '                                                        <span class="title">BMI : </span>';
                        if (bmi) {
                            patient_modal += '<p><span class="gotham_medium">' + Math.round(bmi) + '</span></p> ';
                        } else {
                            patient_modal += '<p><span class="gotham_medium"></span></p> ';
                        }
                        patient_modal += '' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' +
                                '                                                        <span class="title">Alcohol : </span>' +
                                '                                                        <p><span class="gotham_medium">' + alcohole + '</span></p>                            ' +
                                '                                                    </div>' +
                                '                                                </div>' +
                                '                                                <div class="hr"></div>' +
                                '                                                <div class="row common_row">' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">' +
                                '                                                        <span class="title">Smoking : </span>' +
                                '                                                        <p><span class="gotham_medium">' + smoking + '</span></p>    ' +
                                '                                                    </div>';
                                // 25 =
                                if($rootScope.checkPermission($rootScope.PATIENT_KCO,$rootScope.VIEW))
                                {
                                    patient_modal += '' +
                                '                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 ">' +
                                '                                                        <span class="title">K/C/O : </span>' +
                                '                                                        <p><span class="gotham_medium">' + user_data.kco_string + '</span></p>    ' +
                                '                                                    </div>';
                                }
                                var caretaker_html = '';
                                if(user_data.caretaker_data.length > 0) {
                                    caretaker_html += '<div class="hr"></div><div class="row common_row">'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">'+                                                        
                                            '<b>Caregiver</b>'+
                                        '</div>'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">'+
                                            '<b>Mobile No</b>'+
                                        '</div>'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">'+
                                            '<b>Relation</b>'+
                                        '</div>'+
                                    '</div>';
                                }
                                angular.forEach(user_data.caretaker_data, function (caretaker) {
                                    caretaker_html += '<div class="row">'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">'+caretaker.user_first_name + ' ' + caretaker.user_last_name +'</div>'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">'+caretaker.user_phone_number+'</div>'+
                                        '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">'+caretaker.relation+'</div>'+
                                    '</div>';
                                });
                                patient_modal += '' +
                                '                                                </div>' +
                                '                                                <div class="hr"></div>' +
                                '                                                <div class="row common_row">' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">' +
                                '                                                        <span class="title">Food Allergy : </span> ' +
                                '                                                        <p><span class="gotham_medium">' + food_alergy + '</span></p>   ' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col_right_border">' +
                                '                                                        <span class="title">Medicine Allergy : </span>' +
                                '                                                        <p><span class="gotham_medium">' + medicine_alergy + '</span></p>    ' +
                                '                                                    </div>' +
                                '                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">' +
                                '                                                        <span class="title">Other Allergy : </span>' +
                                '                                                        <p><span class="gotham_medium">' + other_alergy + '</span></p>    ' +
                                '                                                    </div>' +
                                '                                                </div>' + caretaker_html +
                                '                                                <div class="row common_row background_color_3" style="padding: 11px 0px;">' +
                                '                                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">' +
                                '                                                        <h3 class="margin_0_auto title1">Appointment details</h3>' +
                                '                                                    </div>';
                        if (!event.is_past) {
                            patient_modal += '                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' +
                                    '                                                        <img src="app/images/edit_icon.png" alt="edit" class="edit_appointment" id="edit_icon"/>' +
                                    '                                                        <img src="app/images/delete_icon.png" alt="edit" class="delete_appointment" id="delete_icon"/>' +
                                    '                                                    </div>';
                        }
						
						if($rootScope.current_doctor != undefined && $rootScope.current_doctor.full_name != undefined && $rootScope.current_doctor.full_name != ''){
							var doctor_name = $rootScope.docPrefix + $rootScope.current_doctor.full_name;
						}else{
							var doctor_name = $rootScope.docPrefix + $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name;
						}

                        var appointmentTypeName = $filter('filter')(APPOINTMENT_TYPE, {id: event.appointment_type});
                        appointmentTypeName = (appointmentTypeName != undefined) ? appointmentTypeName[0].name : APPOINTMENT_TYPE[event.appointment_type - 1].name;

                        patient_modal += '                                      </div>' +
                                '                                                <div class="row common_row">   ' +
                                '                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">' +
                                '                                                        <p>Appointment with : <span class="gotham_medium">' + doctor_name + '</span></p>' +
                                '                                                        <p>Appointment on : <span class="gotham_medium">' + event.appointment_time + ' for ' + duration + '</span></p>' +
                                '                                                        <p>Appointment type : <span class="gotham_medium">' + appointmentTypeName + '</span></p>' +
                                '                                                    </div>' +
                                '                                                </div>                    ' +
                                '                                            </div>' +
                                '                                        </div>';


                    }
                    callback(patient_modal);
                }, function (error) {
                    console.log(error);
                });

            }

            /* get patient list */
            this.getPatientFilterList = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_patient_list",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: request.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        patient_list_type: request.filter,
                        page: request.page,
                        per_page: request.per_page,
                        is_pagination: true
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get patient detail */
            this.getPatientDetail = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_patient_detail",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: request.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.appointment_clinic_id,
                        patient_id: request.user_id,
                        appointment_id: request.appointment_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* add new vital code */
            this.addNewVital = function (request, callback, errorFunction) {
                var spo = '';
                if (request.sp2o) {
                    spo = request.sp2o;
                }
                var weight = '';
                if (request.weight) {
                    weight = request.weight;
                }
                var blood_pressure_systolic = '';
                if (request.blood_pressure_systolic) {
                    blood_pressure_systolic = request.blood_pressure_systolic;
                }
                var blood_pressure_diastolic = '';
                if (request.blood_pressure_diastolic) {
                    blood_pressure_diastolic = request.blood_pressure_diastolic;
                }
                var pulse = '';
                if (request.pulse) {
                    pulse = request.pulse;
                }
                var temp = '';
                if (request.temperature) {
                    temp = request.temperature;
                }
                var resp = '';
                if (request.resp) {
                    resp = request.resp;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_vital_for_patient",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        patient_id: request.patient_id,
                        date: request.date,
                        sp2o: spo,
                        weight: weight,
                        blood_pressure_systolic: blood_pressure_systolic,
                        blood_pressure_diastolic: blood_pressure_diastolic,
                        blood_pressure_type: request.blood_pressure_type,
                        pulse: pulse,
                        temperature: temp,
                        temperature_type: request.temperature_type,
                        temperature_taken: request.temperature_taken,
                        resp: resp,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* edit new vital code */
            this.editVital = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_vital_for_patient",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        patient_id: request.patient_id,
                        vital_id: request.id,
                        date: request.date,
                        sp2o: request.sp2o,
                        weight: request.weight,
                        blood_pressure_systolic: request.blood_pressure_systolic,
                        blood_pressure_diastolic: request.blood_pressure_diastolic,
                        blood_pressure_type: request.blood_pressure_type,
                        pulse: request.pulse,
                        temperature: request.temperature,
                        temperature_type: request.temperature_type,
                        temperature_taken: request.temperature_taken,
                        resp: request.resp,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get appointment dates*/
            this.getAppointmentDates = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_appointment_date",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: request.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        clinic_id: request.clinic_id,
                        patient_id: request.user_id,
                        page: request.page,
                        per_page: request.per_page,
                        follow_up_flag: request.follow_up_flag,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get report types */
            this.getReportTypes = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_reports_types",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* add report into db */
            this.addReportToDB = function (request, callback, errorFunction) {
                var formData = new FormData();

                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("doctor_id", $rootScope.current_doctor.user_id);
                formData.append("patient_id", request.patient_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("appointment_id", request.appointment_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("report_type_id", request.report_type_id);
                formData.append("date", request.date);
                formData.append("report_name", request.report_name);
                formData.append("images", request.img);

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_report",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
//                $http({
//                    method: 'POST',
//                    url: $rootScope.app.apiUrl + "/add_report",
//                    data: {
//                        user_id: $rootScope.currentUser.user_id,
//                        doctor_id: $rootScope.currentUser.user_id,
//                        access_token: $rootScope.currentUser.access_token,
//                        user_type: 2,
//                        patient_id: request.patient_id,
//                        appointment_id: request.appointment_id,
//                        clinic_id: request.clinic_id,
//                        report_type_id: request.report_type_id,
//                        date: request.date,
//                        report_name: request.report_name
//                    }
//                }).then(function successCallback(response) {
//                    callback(response.data);
//                }, function errorCallback(error) {
//                    errorFunction(error);
//                });
            }

            this.deleteReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        report_id: request.report_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add clinical notes for patient into db */
            this.addClinicalNotesPatient = function (request, callback, errorFunction) {
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("doctor_id", $rootScope.current_doctor.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("user_type", 2);
                formData.append("patient_id", request.patient_id);
                formData.append("appointment_id", request.appointment_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("date", request.date);
                formData.append("kco", request.kco);
                formData.append("complaints", request.complaints);
                formData.append("observation", request.observation);
                formData.append("diagnose", request.diagnose);
                formData.append("notes", request.notes);
                formData.append("image", request.image);
                formData.append("image_type", request.image_type);

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_clinic_notes",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* add patient prescription */
            this.addPrescription = function (request, callback, errorFunction) {

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_prescription",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        drug_request_json: request.drug_request_json,
                        diet_instruction: request.diet_instruction,
                        next_follow_up: request.next_follow_up,
                        is_capture_compliance: request.is_capture_compliance,
                        diet_instruction_id: request.diet_instruction_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add patient prescription */
            this.editPrescription = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_prescription",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        drug_id: request.brandList.similar_brand_id,
                        drug_name: request.brandList.similar_brand,
                        generic_id: request.brandList.drug_drug_generic_id.join(','),
                        unit_id: request.brandList.drug_unit_id,
                        unit_value: request.brandList.drug_unit_value,
                        frequency_id: request.brandList.drug_frequency_id,
                        frequency_value: request.custom_value,
                        dosage: request.brandList.dosage,
                        frequency_instruction: request.brandList.freq_instruction,
                        intake: request.brandList.drug_intake,
                        intake_instruction: request.brandList.intake_instruction,
                        duration: request.brandList.drug_duration,
                        duration_value: request.brandList.drug_duration_value,
                        diet_instruction: request.brandList.drug_instruction,
                        next_follow_up: request.next_follow_up,
                        prescription_id: request.id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add investigation report */
            this.addInvestigationReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_investigation",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        lab_test_name: request.lab_test_name,
						lab_test_name_other: request.lab_test_name_other,
                        instruction: request.instruction

                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add investigation report */
            this.editInvestigationReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_investigation",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        lab_test_name: request.lab_test_name,
						lab_test_name_other: request.lab_test_name_other,
                        instruction: request.instruction,
                        investigation_id: request.id

                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add procedure report */
            this.addProcedureReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_procedure",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        procedure: request.procedure,
                        procedure_note: request.procedure_note

                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* edit procedure report */
            this.editProcedureReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_procedure",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        procedure: request.procedure,
                        procedure_note: request.procedure_note,
                        procedure_id: request.id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /*get patient whole report  */
            this.getPatientWholeReport = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_report_detail",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: request.doctor_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        key: request.key,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /*get patient whole report  */
            this.deleteRX = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_prescription",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        clinic_id: request.clinic_id,
                        prescription_id: request.id,
                        patient_id: request.patient_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* delete img code */
            this.deleteClinicalImage = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_clinic_notes_image",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        clinic_id: request.clinic_id,
                        clinic_notes_image_id: request.clinic_notes_image_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* getHealthTest code */
            this.getHealthTest = function (request, flag, callback, errorFunction) {
                if (!request.search) {
                    request.search = '';
                }
                if (!request.parent_id) {
                    request.parent_id = 0;
                }
                var flagValue = '';
                if (flag) {
                    flagValue = 1;
                }
				var healthAnalyticsTestTypeValue = '';
                if (request.health_analytics_test_type != undefined) {
                    healthAnalyticsTestTypeValue = request.health_analytics_test_type;
                }
                if($rootScope.currentUser.sub_plan_setting != undefined && $rootScope.currentUser.sub_plan_setting.medsign_speciality_id != undefined) {
                    var medsign_speciality_id = $rootScope.currentUser.sub_plan_setting.medsign_speciality_id;
                } else {
                    var medsign_speciality_id = '';
                }
				$http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_health_anlaytics_test",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        search: request.search,
                        parent_id: request.parent_id,
                        medsign_speciality_id: medsign_speciality_id,
                        flag: flagValue,
						health_analytics_test_type: healthAnalyticsTestTypeValue
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.getAllHealthTest = function (request, flag, callback, errorFunction) {
                if (!request.search) {
                    request.search = '';
                }
                if (!request.parent_id) {
                    request.parent_id = 0;
                }
                var flagValue = '';
                if (flag) {
                    flagValue = 1;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_all_health_anlaytics_test",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        search: request.search,
                        parent_id: request.parent_id,
                        flag: flagValue,
						resource_type: request.resourceType
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* getHealthTest code */
            this.getHealthTestForPatient = function (request, flag, callback, errorFunction) {
                if (!request.search) {
                    request.search = '';
                }
                if (!request.parent_id) {
                    request.parent_id = 0;
                }
                var flagValue = '';
                if (flag) {
                    flagValue = 1;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_health_anlaytics",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        search: request.search,
                        parent_id: request.parent_id,
                        flag: flagValue,
                        patient_id: request.patient_id,
                        medsign_speciality_id: (request.medsign_speciality_id != undefined) ? request.medsign_speciality_id : ''
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* add new health value */
            this.addAnalyticsValue = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_health_analytics",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        parent_id: request.parent_id,
                        appointment_id: request.appointment_id,
                        date: request.date,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id,
                        health_analytics_data: request.health_analytics_data,
                        health_analytics_test: request.health_analytics_test,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* edit health value */
            this.editAnalyticsValue = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/edit_health_analytics",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        parent_id: request.parent_id,
                        appointment_id: request.appointment_id,
                        date: request.date,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id,
                        health_analytics_data: request.health_analytics_data,
                        health_analytics_test: request.health_analytics_test,
                        health_analytics_id: request.health_analytics_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* add new refreal doctor value */
            this.addReferalDoctor = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_refer",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        other_doctor_id: request.other_doctor_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* copy followup template */
            this.copyFollowUpTemplate = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_followup_data",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        old_appointment_id: request.old_appointment_id,
                        new_appointment_id: request.new_appointment_id,
                        old_date: request.old_date,
                        new_date: request.new_date,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* delete old data after selecting template*/
            this.deleteOldData = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/change_template",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* graph table code*/
            this.getTableGraphDataVital = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_vital",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        flag: request.flag,
                        page: request.page,
                        per_page: request.per_page,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* graph table code*/
            this.getTableGraphDataNotes = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_clinical_notes_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        flag: 1,
                        page: request.page,
                        per_page: request.per_page,
                        clinical_report_id: request.report_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* graph table code*/
            this.getTableGraphDataHealthAnalytics = function (request, callback, errorFunction) {
                var objRequest = {
                        user_id: 	$rootScope.currentUser.user_id,
                        doctor_id:  $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        flag: 1,
                        page: request.page,
                        per_page: request.per_page
                    };
					
				if(request.appointment_id != undefined)
					objRequest.appointment_id = request.appointment_id;
				
				if(request.patient_previous_health_analytic != undefined)
					objRequest.patient_previous_health_analytic = request.patient_previous_health_analytic;

				$http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_health_analytics_report",
                    data: objRequest 
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getPatientHealthAnalytics = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_health_analytics",
                    data: {
                        user_id: 	  $rootScope.currentUser.user_id,
                        doctor_id: 	  $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 	  2,
                        patient_id:   request.patient_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getTableGraphDataReports = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        flag: 1,
						rptTypeId: (request.rptTypeId != undefined) ? request.rptTypeId : '',
                        search: (request.search != undefined) ? request.search : '',
                        page: (request.page != undefined) ? request.page : '',
                        per_page: (request.per_page != undefined) ? request.per_page : ''
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getReportDetail = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_report_detail",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        report_id: request.report_id,
                        report_type_id: request.report_type_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getTableGraphDataProc = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_procedure_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        report_id: request.report_id,
                        clinic_id: request.clinic_id,
                        page: request.page,
                        per_page: request.per_page,
                        flag: request.flag,
                        procedure_report_id: request.procedure_report_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getTableGraphDataInvestigation = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_investigation_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        report_id: request.report_id,
                        clinic_id: request.clinic_id,
                        page: request.page,
                        per_page: request.per_page,
                        flag: request.flag,
                        lab_report_id: request.lab_report_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getTableGraphDataRX = function (request, callback, errorFunction) {
                var is_all_data = "";
                if(request.is_all_data != undefined)
                    is_all_data = request.is_all_data;
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_prescription",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        patient_id: request.patient_id,
                        clinic_id: request.clinic_id,
                        page: request.page,
                        per_page: request.per_page,
                        flag: request.flag,
                        is_all_data: is_all_data,
                        date: request.date
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getTableGraphPatientCompliance = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_reminder_chart",
                    data: {
                        access_token: "medeasy",
                        user_type: 2,
                        user_id: request.patient_id,
                        date: request.date
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.saveNewTemplate = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/save_template_based_appointment",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        template_name: request.template_name,
                        appointment_id: request.appointment_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getIXList = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_lab_test",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        search: ''
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.copyRX = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/clone_prescription",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        patient_id: request.patient_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        date: request.date,
                        prescription_id: request.prescription_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getRelatedDrugData = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_related_drugs",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        drug_id: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getGenericDetailsFromDB = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_drug_generic_detail",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        generic_id: request.generic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.searchProc = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_procedure",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        search: request
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.acceptPayment = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_billing",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        appointment_id: request.appointment_id,
						doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id,
                        payment_json: request.payment_json,
                        total_discount: request.total_discount,
                        total_tax: request.total_tax,
                        grand_total: request.grand_total,
                        mode_of_payment_id: request.mode_of_payment_id,
                        total_payable: request.total_payable,
                        advance_amount: request.advance_amount,
                        paid_amount: request.paid_amount,
						invoice_no: (request.invoice_no != undefined) ? request.invoice_no : '',
                        billing_id: (request.billing_id != undefined) ? request.billing_id : '',
                        invoice_date: (request.invoice_date != undefined) ? request.invoice_date : '',
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getAppointmentBillingDetail = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_billing",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 		2,
                        user_id: 		$rootScope.currentUser.user_id,
                        appointment_id: request.appointment_id,
                        clinic_id: 		request.clinic_id,
                        patient_id: 	request.patient_id,
                        billing_id:     request.billing_id,
						doctor_id: 		request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getInvoiceList = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_invoice_list",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type:      2,
                        user_id:        $rootScope.currentUser.user_id,
                        appointment_id: request.appointment_id,
                        clinic_id:      request.clinic_id,
                        patient_id:     request.patient_id,
                        doctor_id:      request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.deleteInvoice = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_invoice",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type:      2,
                        user_id:        $rootScope.currentUser.user_id,
                        billing_id:     request.billing_id,
                        doctor_id:      request.doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.sharePDFMail = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/share_record",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id,
                        with_vitalsign: request.with_vitalsign,
                        with_clinicnote: request.with_clinicnote,
                        with_only_diagnosis: request.with_only_diagnosis,
                        with_patient_lab_orders: request.with_patient_lab_orders,
                        with_prescription: request.with_prescription,
                        with_generic: request.with_generic,
                        with_files: request.with_files,
                        with_treatment: request.with_treatment,
                        with_procedure: request.with_procedure,
                        with_signature: request.with_signature,
                        email: request.email,
                        mobile_no: request.mobile_no,
                        language_id: request.language_id,
                        share_type: request.share_type,
                        with_anatomy_diagram: request.with_anatomy_diagram,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.sharePDFMailInvoice = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/share_invoice",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: request.doctor_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id,
                        billing_id: request.billing_id,
                        email: request.email,
                        mobile_no: request.mobile_no,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.deleteVital = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_vital",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        appointment_id: request.appointment_id,
                        clinic_id: request.clinic_id,
                        patient_id: request.patient_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.updateInvoiceNoSetting = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_invoice_no_setting",
                    data: {
                        access_token:$rootScope.currentUser.access_token,
                        user_type: 	 2,
                        user_id: 	 $rootScope.currentUser.user_id,
                        doctor_id: 	 request.doctor_id,
						clinic_id: 	 request.clinic_id,
						inv_prefix:  request.inv_prefix,
						inv_counter: request.inv_counter,
						invoice_autoincrement: request.invoice_autoincrement,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.managePatientPreviousHealthAnalytic = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/manage_previous_patient_health_analytics",
                    data: {
                        user_id: 	           $rootScope.currentUser.user_id,
                        doctor_id: 	  		   $rootScope.current_doctor.user_id,
                        access_token: 		   $rootScope.currentUser.access_token,
                        user_type: 	  		   2,
                        appointment_id: 	   request.appointment_id,
                        clinic_id:  		   request.clinic_id,
                        patient_id: 		   request.patient_id,
                        health_analytics_data: request.health_analytics_data
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.savePrintSetup = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/set_prescription_print_setting",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        clinic_id: request.clinic_id,
						data: request.pdf_url_data
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.getPrintSetup = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_prescription_print_setting",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 		2,
                        user_id: 		$rootScope.currentUser.user_id,
                        clinic_id: 		request.clinic_id,
                        doctor_id: 		$rootScope.current_doctor.user_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
			}
            this.getDaigramsData = function (request, callback, errorFunction) {
                if($rootScope.currentUser.sub_plan_setting != undefined && $rootScope.currentUser.sub_plan_setting.medsign_speciality_id != undefined) {
                    var medsign_speciality_id = $rootScope.currentUser.sub_plan_setting.medsign_speciality_id;
                } else {
                    var medsign_speciality_id = '';
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_diagrams",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        category_id: request.category,
                        search: request.search,
                        sub_category_id: request.sub_category,
                        medsign_speciality_id: medsign_speciality_id,
                        page: request.page,
                        per_page: request.per_page,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getDiagramCategory = function (request, callback, errorFunction) {
                if($rootScope.currentUser.sub_plan_setting != undefined && $rootScope.currentUser.sub_plan_setting.medsign_speciality_id != undefined) {
                    var medsign_speciality_id = $rootScope.currentUser.sub_plan_setting.medsign_speciality_id;
                } else {
                    var medsign_speciality_id = '';
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_diagrams_category",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_id: $rootScope.currentUser.user_id,
                        medsign_speciality_id: medsign_speciality_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getDiagramSubCategory = function (request, callback, errorFunction) {
                if($rootScope.currentUser.sub_plan_setting != undefined && $rootScope.currentUser.sub_plan_setting.medsign_speciality_id != undefined) {
                    var medsign_speciality_id = $rootScope.currentUser.sub_plan_setting.medsign_speciality_id;
                } else {
                    var medsign_speciality_id = '';
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_diagrams_sub_category",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        medsign_speciality_id: medsign_speciality_id,
                        category_id: request.category_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.updateAppointmentStatus = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/update_appointment_status",
                    data: {
                        access_token: $rootScope.currentUser.access_token,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        flag: request.flag,
                        appointment_id: request.appointment_id,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
			this.upload_drs_art_imgs = function (file, request, callback) {
				if(file == undefined || file == ''){
					callback({status:true});
					return true;
				}
				var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("patient_id", request.patient_id);
                formData.append("doctor_id", request.doctor_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("appointment_id", request.appointment_id);
                formData.append("appointment_date", request.appointment_date);
                formData.append("diagrams_title", request.diagrams_title);
                formData.append("photo", file);
                formData.append("access_token", $rootScope.currentUser.access_token);
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/doctor_art",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.permanentDeleteReport = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/permanent_delete_report",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id : $rootScope.current_doctor.user_id,
                        report_id : request.report_id
                    },
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            /* add Previous vital code */
            this.addPreviousVital = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/add_previous_vitals",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get Previous vital code */
            this.getPreviousVital = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_previous_vitals",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }

            /* send refer opt to patient code */
            this.referRxSendOTP = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/refer_rx_send_otp",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* send refer opt to patient code */
            this.referRxVerifyOtp = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/refer_rx_verify_otp",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /* get patient rx data code */
            this.referPatientRxData = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_rx_data",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            this.getMedicalConditions = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_medical_condition",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token
                    },
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.getPatientUserDetails = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_patient_user_details",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.getPrescriptionPdf = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_prescription_pdf",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            /* upload rx */
            this.uploadRx = function (request, callback, errorFunction) {
                var formData = new FormData();
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("doctor_id", $rootScope.current_doctor.user_id);
                formData.append("patient_id", request.patient_id);
                formData.append("user_type", 2);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("appointment_id", request.appointment_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("date", request.date);
                formData.append("rx_upload_name", request.rx_upload_name);
                formData.append("images", request.img);

                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/upload_rx",
                    transformRequest: angular.identity,
                    data: formData,
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            }
            /*get uploaded rx*/
            this.getUploadedRx = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_uploaded_rx",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.deleteRxUploaded = function (request, callback, errorFunction) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/delete_rx_uploaded",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        user_type: 2,
                        rx_upload_id: request.rx_upload_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    errorFunction(error);
                });
            }
            /*search diet instruction*/
            this.searchGlobalInstruction = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/search_instruction",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.changeAppointmentType = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/change_appointment_type",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.daigramsAddToPrescription = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/daigrams_add_to_prescription",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.getDocumentFromShare = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_document_from_share",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.searchInvestigationInstructions = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/search_investigation_instructions",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.saveUAS7Data = function (request, callback) {
                var wheal_count = [];
                var pruritus = [];
                var uas7_date = [];
                var is_valid = false;
                for(var i=1; i <= 7; i++) {
                    if($("#wheal_count_"+i).val() > 0 || $("#pruritus_count_"+i).val() > 0)
                        is_valid = true;
                    wheal_count.push($("#wheal_count_"+i).val());
                    pruritus.push($("#pruritus_count_"+i).val());
                    uas7_date.push($("#uas7_date_"+i).val());
                }
                if(!is_valid)
                    return false;
                request.wheal_count = wheal_count;
                request.pruritus = pruritus;
                request.uas7_date = uas7_date;
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/save_uas7_data",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.getUAS7Parameters = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_uas7_parameters",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.getMyProcData = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_my_procedure_report",
                    data: request,
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.searchCaretaker = function (keyword, callback) {
                if(keyword == undefined || keyword == '')
                    return false;
                            var doctor_id = '';
                if ($rootScope.current_doctor.user_id) {
                    doctor_id = $rootScope.current_doctor.user_id;
                }
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/search_caretaker_list",
                    data: {
                        search: keyword,
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: doctor_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
            this.sendCaretakerOTP = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/send_caretaker_otp",
                    data: request
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };
		});