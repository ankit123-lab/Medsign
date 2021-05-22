/*
 * Controller Name: SettingController
 * Use: This controller is used for Setting activity
 * Created Date : 20th March,2018
 */
angular.module("app.dashboard.setting",['ng.ckeditor'])
        .controller("SettingController", function ($scope, $timeout, $rootScope, ngToast, SettingService, ROLE, fileReader, APPOINTMENT_TYPE, DAYS, uiCalendarConfig, SweetAlert, PatientService,CommonService,ClinicService,$location,$filter,$uibModal) {
            $scope.staff_list = [];
            $scope.role_types = ROLE;
            $scope.current_staff_tab = 1;
            $scope.submitted = false;
            $scope.reception = {};
            $scope.doctor_availibility_data = [];
            $scope.APPOINTMENT_TYPE = APPOINTMENT_TYPE;
            $scope.DAYS = DAYS;
            $scope.current_tax_tab = 2;
            $scope.tax = {};
            $scope.tele_payment_data = {};
            $scope.taxes = [];
            $scope.taxObj = [{}];
            $scope.modeObj = [{}];
            $scope.payment_modes = [];
            $scope.payment_types = [];
            $scope.current_mode = {};
            $scope.is_add_receptionist = true;
            $scope.is_add_assistant = true;
            $scope.is_add_clinic = true;
            $scope.current_fee = {
                tax_id: '',
                basic_cost: '',
                final_amount: '0.00',
                tax_ids: '',
                instruction: ''
            };
            $scope.reset_login = {
                password: '',
                cpassword : '',
                staff_id : '',
            };
            $scope.staff_password_type = {
                password: 'password',
                cpassword : 'password'
            };
            $scope.test = {};
            $scope.test.search = '';
            $scope.fees_list = [];
            $scope.add_instruction = false;
            $scope.current_catelog_tab = 1;
            $scope.clinicalPlaceholder = [
                "Complaints",
                "Observation",
                "Diagnoses",
                "Notes"
            ];
            $scope.DosageDropdown = [
                {
                    text: '0',
                },
                {
                    text: '½',
                },
                {
                    text: '1',
                },
                {
                    text: '1 ½',
                },
                {
                    text: '2',
                },
                {
                    text: '3',
                },
                {
                    text: '4',
                },
                {
                    text: '5',
                },
                {
                    text: '6',
                },
                {
                    text: '7',
                },
                {
                    text: '8',
                },
                {
                    text: '9',
                }
            ];
            $scope.currentClinicalPlaceholder = $scope.clinicalPlaceholder[0];
            $scope.clinical_notes = [];
            $scope.current_clinic_note = {};
            $scope.current_clinic_note.addOpen = false;
            $scope.current_clinic_note.text_name = '';

            $scope.diet_instructions = [];
            $scope.current_diet_instructions = {};
            $scope.current_diet_instructions.addOpen = false;
            $scope.current_diet_instructions.text_name = '';
            $scope.current_rx_instructions = {};
            $scope.current_rx_instructions.addOpen = false;
            $scope.current_rx_instructions.text_name = '';

            $scope.brandList = [{
                    brand_name: '',
                    isOpen: false,
                    default1: '0',
                    default2: '0',
                    default3: '0',
                },
            ];
            $scope.patientGender = [{
                    value: 'all',
                    label: 'All'
                },
                {
                    value: 'male',
                    label: 'Male'
                },
                {
                    value: 'female',
                    label: 'Female'
                },
                {
                    value: 'undisclosed',
                    label: 'Undisclosed'
                },
                {
                    value: 'other',
                    label: 'Other'
                }
            ];
            $scope.patientAgeGroup = [{
                    value: '7',
                    label: 'All'
                },
                {
                    value: '1',
                    label: '0-5'
                },
                {
                    value: '2',
                    label: '5-14'
                },
                {
                    value: '3',
                    label: '15-25'
                },
                {
                    value: '4',
                    label: '26-40'
                },
                {
                    value: '5',
                    label: '40-60'
                },
                {
                    value: '6',
                    label: 'Above 60'
                }
            ];
            $scope.drugList = [];
            $scope.brandGenericList = [];
            $scope.brandTypeList = [];
            $scope.brandFreqList = [];
            /* $scope.brandDurationTypeList = [{
                    id: '1',
                    name: 'Days'
                },
                {
                    id: '2',
                    name: "Weeks"
                },
                {
                    id: '3',
                    name: 'Months'
                }
            ]; */
            /* $scope.brandIntakeList = [
                {
                    id: '1',
                    name: 'Before Food',
                },
                {
                    id: '2',
                    name: 'After Food',
                }, {
                    id: '3',
                    name: 'Along with Food',
                },
                {
                    id: '4',
                    name: 'Empty Stomach',
                }
            ]; */
            $scope.brand_search = {};
            $scope.brand_search.search = '';
            $scope.alert = {
                data: [{
                        data: [{
                                communication_setting_sms_status: '2',
                                communication_setting_email_status: '2',
                                communication_setting_push_status: '2',
                            },
                            {
                                communication_setting_sms_status: '2',
                                communication_setting_email_status: '2',
                                communication_setting_push_status: '2',
                            },
                            {
                                communication_setting_sms_status: '2',
                                communication_setting_email_status: '2',
                                communication_setting_push_status: '2',
                            },
                        ]
                    },
                    {
                        data: [{
                                communication_setting_sms_status: '2',
                                communication_setting_email_status: '2',
                                communication_setting_push_status: '2',
                            }
                        ]
                    }]
            };

            $scope.testChief = [{
                }];
            $scope.testObservation = [{
                }];
            $scope.testDiagnosis = [{
                }];
            $scope.testNotes = [{
                }];
            $scope.testLabs = [{
                }];
            $scope.template = {
                lab_instruction: '',
                template_diagnoses: '',
                template_name: '',
            };
            $scope.template_list = [];
            $scope.template_tab = {};
            $scope.security_data = [];
            $scope.share_data = [];
            $scope.fav_doctors = [];
            $scope.fav_doctor_search = {
                search: '',
                id: ''
            };
            $scope.cPasswordShowHide = function(val){
                if(val == 1) {
                    if($scope.staff_password_type.password == "password")
                        $scope.staff_password_type.password = "text";
                    else
                        $scope.staff_password_type.password = "password";
                } else if(val == 2) {
                    if($scope.staff_password_type.cpassword == "password")
                        $scope.staff_password_type.cpassword = "text";
                    else
                        $scope.staff_password_type.cpassword = "password";
                }
            }
            $scope.resetPasswordReceptionist = function(form){
                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .resetPassword($scope.reset_login, function (response) {
                                if (response.status == true) {
                                    $("#modal_reset_password").modal('hide');
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                        
                                }else{
                                    ngToast.danger({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                }
                        }
                    );
                }
            }
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
            $scope.other.country = [];
            CommonService.getCountry('', function (response) {
                if (response.status == true) {
                    $scope.other.country = response.data;
                    $scope.other.selected_clinic_country = $scope.other.country[0].country_id;
                    $scope.getState($scope.other.selected_clinic_country);
                    $scope.other.city = [];
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
            $scope.changeGender = function (gender) {
                $scope.reception.gender = gender;
            }
            $scope.$on('gmPlacesAutocomplete::placeChanged', function () {
                $scope.other.is_copied = false;
                if ($scope.reception.address_name != '' && $scope.reception.address_name != undefined && $scope.reception.address_name.getPlace() != undefined) {
                    var location = $scope.reception.address_name.getPlace().geometry.location;
                    $scope.reception.address_latitude = location.lat();
                    $scope.reception.address_longitude = location.lng();
                }
                if ($scope.add_clinic_data.clinic_address != '' && $scope.add_clinic_data.clinic_address != undefined && $scope.add_clinic_data.clinic_address.getPlace() != undefined) {
                    var location = $scope.add_clinic_data.clinic_address.getPlace().geometry.location;
                    $scope.add_clinic_data.cliniclat = location.lat();
                    $scope.add_clinic_data.cliniclng = location.lng();
                } 
                $scope.$apply();
            });

            /* clinic drop down code (User controller for edit clinic) */
            $scope.changeCurrentClinic = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.$broadcast('fromSettingEditClinic');
            }
			
            $scope.$on('pingBack', function (e, data) {
            });
			
            /* clinic drop down code (For clinic staff) */
            $scope.changeCurrentClinicForStaff = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getStaffList($rootScope.current_clinic.clinic_id);
            };

            /* Get Staff listing */
            $scope.getStaffList = function (clinic_id) {
                $scope.current_staff_tab = 1;
                var request = {
                    clinic_id: clinic_id,
                    doctor_id: $rootScope.currentUser.doctor_id
                };
                SettingService.getStaff(request, function (response) {
                    if (response.status) {
                        $scope.staff_list = response.data;
                        $scope.is_add_receptionist = response.is_add_receptionist;
                        $scope.is_add_assistant = response.is_add_assistant;
                    } else {

                    }
                });
            }
            $scope.isImgEdited = '';
			
            /* change staff tab code */
            $scope.changeStaffTab = function (tab) {
                $scope.current_staff_tab = tab;
                $scope.reception.reception_img_temp = undefined;
                $scope.reception = {};
                $scope.reception.gender = 1;
                $scope.submitted = false;
                $scope.isImgEdited = '';
            }

            $scope.removeStaffImage = function (type) {
                if (type == 1) {
                    $scope.reception.reception_img_temp = '';
                } else if (type == 2) {
                    $scope.isImgEdited = '';
                    $scope.reception.reception_img_temp = '';
                }
            }

            /* add assistant and reception code */
            $scope.addReceptionist = function (form) {
                $scope.submitted = true;
                
                if (form.$valid) {
                    $rootScope.app.isLoader = true;
                    $scope.reception.clinic_id = $rootScope.current_clinic.clinic_id;
                    $scope.reception.staff_type = $scope.current_staff_tab;
                    if($scope.doctor.edu_object[$scope.doctor.edu_object.length - 1] != undefined && $scope.doctor.edu_object[$scope.doctor.edu_object.length - 1].doctor_qualification_degree != undefined && $scope.doctor.edu_object[$scope.doctor.edu_object.length - 1].doctor_qualification_degree == '')
                        $scope.doctor.edu_object.splice($scope.doctor.edu_object.length - 1, 1);
                    if($scope.doctor.edu_object.length > 0){
                        for (var j = 0; j < $scope.doctor.edu_object.length; j++) {
                            var selectedEduObj = $filter('filter')($scope.other.qualification, {'qualification_name':$scope.doctor.edu_object[j].doctor_qualification_degree},true);
                            if(selectedEduObj != undefined && selectedEduObj.length > 0){
                                $scope.doctor.edu_object[j].doctor_qualification_qualification_id = selectedEduObj[0].qualification_id;
                            }
                        }
                    }
                    $scope.reception.edu_object = $scope.doctor.edu_object;
                    if($scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1] != undefined && $scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1].doctor_council_registration_number != undefined && $scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1].doctor_council_registration_number == '')
                        $scope.doctor.registration_obj.splice($scope.doctor.registration_obj.length - 1, 1);
                    $scope.reception.registration_obj = $scope.doctor.registration_obj;
                    SettingService
                            .addStaff($scope.reception, function (response) {
                                if (response.status) {
                                    
                                    /* if(!!$scope.reception.tfa && $scope.reception.tfa == 1){
                                        var temp = [{"id":1,"name":"2 Factor authentication","status":$scope.reception.tfa}];
                                        var request1 = {
                                            data: JSON.stringify(temp),
                                            setting_type: 2,
                                            setting_data_type: 1,
                                            clinic_id: '',
                                            staff_id : response.staff_id
                                        };
                                        SettingService
                                                .saveStaffSetting(request1, function (response1) {});
                                    } */
                                    

                                    $rootScope.app.isLoader = true;
                                    if($scope.reception.file){
                                        var request = {
                                            file: $scope.reception.file,
                                            staff_id: response.staff_id,
                                        };
                                        SettingService
                                            .uploadUserImage(request, function (img_response) {
                                                $rootScope.app.isLoader = false;
                                                $("#modal_add_staff").modal('hide');

                                                ngToast.success({
                                                    content: response.message,
                                                    className: '',
                                                    dismissOnTimeout: true,
                                                    timeout: 5000
                                                });
                                                $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                            });
                                    }
                                    else{
                                        $("#modal_add_staff").modal('hide');
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                    }
                                    

                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                            });
                }
            }

            $scope.openFile = function (fileObj) {
                setTimeout(function () {
                    document.getElementById(fileObj).value = '';
                    document.getElementById(fileObj).click();
                }, 0);
            }

            $scope.getFile = function (obj_type, file, key) {
                $scope.reception.reception_file = file;
                fileReader.readAsDataUrl(file, $scope)
                        .then(function (result) {
                            if (obj_type == "receptionist") {
                                $scope.reception.reception_img_temp = result;
                                $scope.isImgEdited = '1';
                            } else if (obj_type == "edu") {
                                $scope.doctor.edu_object[key].doctor_qualification_image_full_path = result;
                                $scope.doctor.edu_object[key].cancel_image = '1';
                            } else if (obj_type == "reg") {
                                $scope.doctor.registration_obj[key].doctor_registration_image_filepath = result;
                                $scope.doctor.registration_obj[key].cancel_image = '1';
                            } else if (obj_type == "clinic_image") {
                                $scope.clinicImageSrc = result;
                                $scope.clinicImageSrcEdited = '1';
                            } else if (obj_type == "clinic_outside") {
                                $scope.clinicOutsideImageSrc = result;
                                $scope.clinicOutsideImageSrcEdited = '1';
                            } else if (obj_type == "clinic_waiting") {
                                $scope.clinicWaitingImageSrc = result;
                                $scope.clinicWaitingImageSrcEdited = '1';
                            } else if (obj_type == "clinic_reception") {
                                $scope.clinicReceptionImageSrc = result;
                                $scope.clinicReceptionImageSrcEdited = '1';
                            } else if (obj_type == "clinic_address_proof") {
                                $scope.clinicAddressImageSrc = result;
                                $scope.clinicAddressImageSrcEdited = '1';
                            } else if (obj_type == "rx_print_logo") {
                                $scope.rx_img.temp_img = result;
                                $scope.previewRxPrint();
                            } else if (obj_type == "rx_watermark_img") {
                                $scope.rx_img.watermark_temp_img = result;
                                $scope.previewRxPrint();
                            } else if (obj_type == "rx_print_signature") {
                                $scope.rx_img.imageSignSrc = result;
                                $scope.previewRxPrint();
                            }
                        });
            };


            /* get staff detail code */
            $scope.getStaffDetail = function (staff_id, user_role) {
                $scope.isImgEdited = '';
                $scope.getQualification();
                SettingService
                        .getStaffDetail(staff_id, function (response) {
                            if (response.status == true) {
                                $scope.reception = {
                                    role: user_role,
                                    staff_id: response.user_data.user_id,
                                    assign_clinic_id: response.user_data.clinic_id.split(","),
                                    assign_old_clinic_id: response.user_data.clinic_id,
                                    fname: response.user_data.user_first_name,
                                    lname: response.user_data.user_last_name,
                                    email: response.user_data.user_email,
                                    mob_number: response.user_data.user_phone_number,
                                    reception_img_temp: response.user_data.user_photo_filepath,
                                    address_name_one : response.user_data.address_name_one,
                                    address_name : response.user_data.address_name,
                                    address_latitude : response.user_data.address_latitude,
                                    address_longitude : response.user_data.address_longitude,
                                    address_country_id : response.user_data.address_country_id,
                                    address_state_id : response.user_data.address_state_id,
                                    address_city_id : response.user_data.address_city_id,
                                    address_locality : response.user_data.address_locality,
                                    address_pincode : response.user_data.address_pincode,
                                    reception_img_temp : response.user_data.user_photo_filepath,
                                    doctor_clinic_mapping_role_id : response.user_data.doctor_clinic_mapping_role_id,
                                    tfa : response.user_data.tfa
                                };
                                if($scope.reception.address_state_id){
                                    $scope.getCity($scope.reception.address_state_id);
                                }
                                if(response.user_data.user_gender == 'male')
                                    $scope.reception.gender = 1;
                                else if(response.user_data.user_gender == 'female')
                                    $scope.reception.gender = 2;
                                else
                                    $scope.reception.gender = 3;

                                if (response.doctor_edu_data.length > 0) {
                                    $scope.doctor.edu_object = response.doctor_edu_data;
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
                                if (response.doctor_reg_data.length > 0) {
                                    $scope.doctor.registration_obj = response.doctor_reg_data;
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

                                $("#modal_edit_staff").modal("show");
                            }
                        });
            }

            /* edit staff code */
            $scope.editReceptionist = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    $rootScope.app.isLoader = true;
                    $scope.reception.clinic_id = $rootScope.current_clinic.clinic_id;
                    $scope.reception.staff_type = $scope.reception.doctor_clinic_mapping_role_id;
                    if($scope.doctor.edu_object[$scope.doctor.edu_object.length - 1] != undefined && $scope.doctor.edu_object[$scope.doctor.edu_object.length - 1].doctor_qualification_degree != undefined && $scope.doctor.edu_object[$scope.doctor.edu_object.length - 1].doctor_qualification_degree == '')
                        $scope.doctor.edu_object.splice($scope.doctor.edu_object.length - 1, 1);

                    if($scope.doctor.edu_object.length > 0){
                        for (var j = 0; j < $scope.doctor.edu_object.length; j++) {
                            var selectedEduObj = $filter('filter')($scope.other.qualification, {'qualification_name':$scope.doctor.edu_object[j].doctor_qualification_degree},true);
                            if(selectedEduObj != undefined && selectedEduObj.length > 0){
                                $scope.doctor.edu_object[j].doctor_qualification_qualification_id = selectedEduObj[0].qualification_id;
                            }
                        }
                    }
                    $scope.reception.edu_object = $scope.doctor.edu_object;
                    if($scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1] != undefined && $scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1].doctor_council_registration_number != undefined && $scope.doctor.registration_obj[$scope.doctor.registration_obj.length - 1].doctor_council_registration_number == '')
                        $scope.doctor.registration_obj.splice($scope.doctor.registration_obj.length - 1, 1);
                    $scope.reception.registration_obj = $scope.doctor.registration_obj;
                    SettingService
                            .editStaff($scope.reception, function (response) {
                                if (response.status) {
                                    $rootScope.app.isLoader = true;

                                    if ($scope.reception.file) {
                                        var request = {
                                            file: $scope.reception.file,
                                            staff_id: $scope.reception.staff_id,
                                        };
                                        SettingService
                                                .uploadUserImage(request, function (img_response) {

                                                    $("#modal_edit_staff").modal('hide');
                                                    ngToast.success({
                                                        content: response.message,
                                                        className: '',
                                                        dismissOnTimeout: true,
                                                        timeout: 5000
                                                    });
                                                    $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                                });

                                    } else {
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                        $("#modal_edit_staff").modal('hide');
                                    }
                                    $scope.isImgEdited = '';
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.submitted = false;
                            });
                }
            }

            $scope.openAddStaffModal = function () {
                $scope.submitted = false;
                $scope.reception = {
                    gender : 1
                };
                $scope.current_staff_tab = 3;
                $scope.getQualification();
            }

            $scope.getQualification = function() {
                $scope.doctor = {};
                $scope.doctor.edu_object = [
                    {
                        doctor_qualification_qualification_id: '',
                        doctor_qualification_degree: '',
                        doctor_qualification_college: '',
                        edu_year: '',
                        img_file_name: '',
                        img_file: '',
                        temp_img: ''
                    }
                ];

                $scope.doctor.registration_obj = [
                    {
                        doctor_registration_image_filepath: '',
                        doctor_registration_council_id: '',
                        doctor_council_registration_number: '',
                        reg_year: '',
                        img_file: '',
                        temp_img: '',
                        cancel_image: ''
                    }
                ];
                

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
                CommonService.getCouncil('', function (response) {
                    if (response.status == true) {
                        $scope.other.councils = response.data;
                    }
                });
            }

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
            $scope.addEduObject = function (type) {
                if (type == 1) {
                    $scope.doctor.edu_object.push({
                        doctor_qualification_qualification_id: '',
                        doctor_qualification_degree: '',
                        doctor_qualification_college: '',
                        doctor_qualification_completion_year: '',
                        doctor_qualification_image_full_path: '',
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
                        reg_year: '',
                        img_file: '',
                        temp_img: '',
                        cancel_image: ''
                    });

                }
            }
            //dynamic html code
            $scope.removeEduObject = function (type, index) {
                if (type == 1) {
                    $scope.doctor.edu_object.splice(index, 1);
                } else if (type == 2) {
                    $scope.doctor.registration_obj.splice(index, 1);
                }
            }
            $scope.removeImage = function (type, index) {
                if (type == 2) {
                    $scope.doctor.edu_object[index].doctor_qualification_image_full_path = "";
                    $scope.doctor.edu_object[index].cancel_image = "";
                } else if (type == 3) {
                    $scope.doctor.registration_obj[index].doctor_registration_image_filepath = "";
                    $scope.doctor.registration_obj[index].cancel_image = "";
                }
            }
            $scope.openFileObject = function (fileObj, key) {
                setTimeout(function () {
                    document.getElementById(fileObj + key).click();
                }, 0);
            };
            $scope.showFullImage = function (img_path) {
                $scope.Model.currentImg = img_path;
                $("#fullscreen_img_modal").show();

            }

            $scope.closeFullImgModal = function () {
                $("#fullscreen_img_modal").hide();
            }
            
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

            $scope.deleteStaff = function (staff_user_id) {
                SweetAlert.swal(
                    {
                        title: $rootScope.app.clinic_staff_delete_alert,
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes!",
                        cancelButtonText: "No",
                        closeOnConfirm: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            var request = {
                                            staff_user_id : staff_user_id,
                                        };
                            SettingService
                                .deleteStaffUser(request, function (response) {
                                    if(response.status === true){
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                    }else{
                                        ngToast.danger({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                    }
                            });
                        }
                    }
                );
            }

            $scope.changeStatusStaff = function (staff_user_id,status) {
                SweetAlert.swal(
                    {
                        title: $rootScope.app.clinic_staff_status_alert,
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes!",
                        cancelButtonText: "No",
                        closeOnConfirm: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            var request = {
                                            staff_user_id : staff_user_id,
                                            status : status
                                        };
                            SettingService
                                .changeStatusStaffUser(request, function (response) {
                                    if(response.status === true){
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getStaffList($rootScope.current_clinic.clinic_id);
                                    }else{
                                        ngToast.danger({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                    }
                            });
                        }
                    }
                );
            }

            /* calendar setting module (availibility) */
            $scope.getDoctorAvailibility = function (clinic_ids) {
                if(clinic_ids != undefined && clinic_ids.length > 0) {
                    var clinic_id_arr = clinic_ids;
                } else {
                    var clinic_id_arr = [];
                    angular.forEach($scope.clinic_data, function (value, key) {
                        clinic_id_arr.push(value.clinic_id);
                    });
                }
                var request = {
                    "clinic_id": $rootScope.current_clinic.clinic_id,
                    "clinic_id_arr": clinic_id_arr
                };
                SettingService
                        .getAvailibilitySetting(request, function (response) {
                            if (response.status) {
                                $scope.doctor_availibility_data = response.data;
                                $scope.clinic_availibility_data = response.clinic_availability;
								if($scope.flg_update_current_clinic_availibility != undefined && $scope.flg_update_current_clinic_availibility == true){
                                    $rootScope.clinic_availability_data = response.clinic_availability;
									$rootScope.current_clinic_availability = response.clinic_availability[$rootScope.current_clinic.clinic_id];
									$rootScope.flg_to_force_calendar_refresh = true;
									$scope.flg_update_current_clinic_availibility = false;
									if (response.clinic_data != undefined && response.clinic_data[0]) {
										$rootScope.clinic_data = angular.copy(response.clinic_data);
                                        var clinicSearchObj = $filter('filter')(response.clinic_data, {'clinic_id':$rootScope.current_clinic.clinic_id},true);
										$rootScope.current_clinic = angular.copy(clinicSearchObj[0]);
									}
                                    $rootScope.calendarMinTime = response.other_data.minTime;
                                    $rootScope.calendarMaxTime = response.other_data.maxTime;
                                    $rootScope.calendarMinDuration = response.other_data.minDuration;
                                    $rootScope.timeSlots = response.other_data.timeSlots;
								}
                            } else {
                                $scope.doctor_availibility_data = [{}];
                                $scope.clinic_availibility_data = '';
                            }
                        });
            }

            /* change availibility as per clinic change */
            $scope.changeCurrentClinicForAvailibiliy = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getDoctorAvailibility();
            }

            /* avalibility edit code */
            $scope.getDetailAvailibility = function (availibility_data, clinic_id, appointment_type) {
                $scope.submitted = false;
                $scope.availibility = {
                    doctor_availability_appointment_type: appointment_type,
                    data:availibility_data[clinic_id]
                }

                var flags = [];
                var newAvailibility = [];
                var index;
                for (index = 0; index < $scope.availibility.data.length; index++) {
                    if (!flags[
                            $scope.availibility.data[index].doctor_availability_session_1_start_time + "_" +
                            $scope.availibility.data[index].doctor_availability_session_1_end_time + "_" +
                            $scope.availibility.data[index].doctor_availability_session_2_start_time + "_" +
                            $scope.availibility.data[index].doctor_availability_session_2_end_time + "_"
                    ]) {
                        flags[
                                $scope.availibility.data[index].doctor_availability_session_1_start_time + "_" +
                                $scope.availibility.data[index].doctor_availability_session_1_end_time + "_" +
                                $scope.availibility.data[index].doctor_availability_session_2_start_time + "_" +
                                $scope.availibility.data[index].doctor_availability_session_2_end_time + "_"
                        ] = true;
                        $scope.availibility.data[index].days = [];
                        $scope.availibility.data[index].days[$scope.availibility.data[index].doctor_availability_week_day] = true;

                        var new_date = new Date();
                        new_date.setHours($scope.availibility.data[index].doctor_availability_session_1_start_time.slice(0, -6));
                        new_date.setMinutes($scope.availibility.data[index].doctor_availability_session_1_start_time.slice(3, -3));
                        new_date.setSeconds(00);
                        $scope.availibility.data[index].doctor_availability_session_1_new_start_time = new_date;

                        var new_date = new Date();
                        new_date.setHours($scope.availibility.data[index].doctor_availability_session_1_end_time.slice(0, -6));
                        new_date.setMinutes($scope.availibility.data[index].doctor_availability_session_1_end_time.slice(3, -3));
                        new_date.setSeconds(00);
                        $scope.availibility.data[index].doctor_availability_session_1_new_end_time = new_date;

                        if ($scope.availibility.data[index].doctor_availability_session_2_start_time) {
                            var new_date = new Date();
                            new_date.setHours($scope.availibility.data[index].doctor_availability_session_2_start_time.slice(0, -6));
                            new_date.setMinutes($scope.availibility.data[index].doctor_availability_session_2_start_time.slice(3, -3));
                            new_date.setSeconds(00);
                            $scope.availibility.data[index].doctor_availability_session_2_new_start_time = new_date;

                            var new_date = new Date();
                            new_date.setHours($scope.availibility.data[index].doctor_availability_session_2_end_time.slice(0, -6));
                            new_date.setMinutes($scope.availibility.data[index].doctor_availability_session_2_end_time.slice(3, -3));
                            new_date.setSeconds(00);
                            $scope.availibility.data[index].doctor_availability_session_2_new_end_time = new_date;

                        }

                        newAvailibility.push($scope.availibility.data[index]);
                    } else {
                        newAvailibility[newAvailibility.length - 1].days[$scope.availibility.data[index].doctor_availability_week_day] = true;
                    }
                }
                if (newAvailibility.length == 0) {
                    newAvailibility = [{}];
                }
                $scope.availibility = newAvailibility;
                $scope.availibility.doctor_availability_appointment_type = appointment_type;
                $scope.availibility.clinic_id = clinic_id;

                $("#edit_doctor_timing").modal("show");
            }
            /* avalibility edit code */
            $scope.getDetailClinicAvailibility = function (availibility_data,clinic_id) {

                $scope.submitted = false;
                $scope.availibility = availibility_data;

                var flags = [];
                var newAvailibility = [];
                var index;
                for (index = 0; index < $scope.availibility.length; index++) {
                    if (!flags[
                            $scope.availibility[index].clinic_availability_session_1_start_time + "_" +
                            $scope.availibility[index].clinic_availability_session_1_end_time + "_" +
                            $scope.availibility[index].clinic_availability_session_2_start_time + "_" +
                            $scope.availibility[index].clinic_availability_session_2_end_time + "_"
                    ]) {
                        flags[
                                $scope.availibility[index].clinic_availability_session_1_start_time + "_" +
                                $scope.availibility[index].clinic_availability_session_1_end_time + "_" +
                                $scope.availibility[index].clinic_availability_session_2_start_time + "_" +
                                $scope.availibility[index].clinic_availability_session_2_end_time + "_"
                        ] = true;
                        $scope.availibility[index].days = [];
                        $scope.availibility[index].days[$scope.availibility[index].clinic_availability_week_day] = true;

                        var new_date = new Date();
                        new_date.setHours($scope.availibility[index].clinic_availability_session_1_start_time.slice(0, -6));
                        new_date.setMinutes($scope.availibility[index].clinic_availability_session_1_start_time.slice(3, -3));
                        new_date.setSeconds(00);
                        $scope.availibility[index].clinic_availability_session_1_new_start_time = new_date;

                        var new_date = new Date();
                        new_date.setHours($scope.availibility[index].clinic_availability_session_1_end_time.slice(0, -6));
                        new_date.setMinutes($scope.availibility[index].clinic_availability_session_1_end_time.slice(3, -3));
                        new_date.setSeconds(00);
                        $scope.availibility[index].clinic_availability_session_1_new_end_time = new_date;

                        if ($scope.availibility[index].clinic_availability_session_2_start_time) {
                            var new_date = new Date();
                            new_date.setHours($scope.availibility[index].clinic_availability_session_2_start_time.slice(0, -6));
                            new_date.setMinutes($scope.availibility[index].clinic_availability_session_2_start_time.slice(3, -3));
                            new_date.setSeconds(00);
                            $scope.availibility[index].clinic_availability_session_2_new_start_time = new_date;

                            var new_date = new Date();
                            new_date.setHours($scope.availibility[index].clinic_availability_session_2_end_time.slice(0, -6));
                            new_date.setMinutes($scope.availibility[index].clinic_availability_session_2_end_time.slice(3, -3));
                            new_date.setSeconds(00);
                            $scope.availibility[index].clinic_availability_session_2_new_end_time = new_date;

                        }

                        newAvailibility.push($scope.availibility[index]);
                    } else {
                        newAvailibility[newAvailibility.length - 1].days[$scope.availibility[index].clinic_availability_week_day] = true;
                    }
                }
                if (newAvailibility.length == 0) {
                    newAvailibility = [{}];
                }
                $scope.availibility = newAvailibility;
                $scope.clinic_time_edit_clinic_id = clinic_id;
                $scope.edit_time_clinic_name = '';
                var clinicSearchObj = $filter('filter')($scope.clinic_data, {'clinic_id':clinic_id},true);
                if(clinicSearchObj != undefined && clinicSearchObj[0] != undefined && clinicSearchObj[0].clinic_name != undefined)
                    $scope.edit_time_clinic_name = clinicSearchObj[0].clinic_name;

                $("#edit_clinic_timing").modal("show");
            }

            /* add more timing code */
            $scope.addMoreTiming = function (type) {
                if ($scope.availibility.length >= 7) {
                    return false;
                }
                if (type == 1) {
                    $("#edit_doctor_timing").modal('handleUpdate');
					if($('#edit_doctor_timing'))
                        $('#edit_doctor_timing .modal-backdrop').height($("#edit_doctor_timing .modal-backdrop.in").height() + 300);
                } else {
                    $("#edit_clinic_timing").modal('handleUpdate');
					if($('#edit_clinic_timing'))
                        $('#edit_clinic_timing .modal-backdrop').height($("#edit_clinic_timing .modal-backdrop.in").height() + 300);
                }
                $scope.availibility.push({});
            }
			
            $scope.removeTiming = function (index) {
                $scope.availibility.splice(index, 1);
            }
            $scope.$on('refreshClinicList', function (e, clinic_details) {
                var clinic_id_arr = [];
                angular.forEach($scope.clinic_data, function (value, key) {
                    clinic_id_arr.push(value.clinic_id);
                });
                clinic_id_arr.push(clinic_details.clinic_id);
                $rootScope.clinic_data = [];
                $scope.getClinics();
                $scope.getDoctorAvailibility(clinic_id_arr);
            });
            $scope.updateBookingStatus = function (clinicData) {
                var setting_data = {
                    doctor_visit: clinicData.online_in_clinic, 
                    tele_consultation: clinicData.online_tele_clinic, 
                }
                var request = {
                    setting_data: setting_data,
                    clinic_id: clinicData.clinic_id
                };
                SettingService
                        .updateBookingStatus(request, function (response) {
                            if (response.status) {
                                $scope.getTimeslot();
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
            /* change availibility status */
            $scope.changeAvailibilityStatus = function (availibility_data,clinic_id,index) {
                var request = {
                    doctor_availability_status: availibility_data.data[clinic_id][0].doctor_availability_status,
                    doctor_availability_appointment_type: availibility_data.doctor_availability_appointment_type,
                    clinic_id: clinic_id
                };
                if(availibility_data.data[clinic_id][0].doctor_availability_status == "2") {
                    if(availibility_data.doctor_availability_appointment_type == "1") {
                        $scope.clinic_data[index].online_in_clinic = "2"
                    }
                    if(availibility_data.doctor_availability_appointment_type == "5") {
                        $scope.clinic_data[index].online_tele_clinic = "2"
                    }
                    console.log($scope.clinic_data[index]);
                }
                SettingService
                        .changeStatusAvailibility(request, function (response) {
                            if (response.status) {
                                $scope.getTimeslot();
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

            /* validation only one selected from seven */
            $scope.checkAnotherChecked = function (current_checked_obj, check_day) {
                angular
                        .forEach($scope.availibility, function (value, key) {
                            if (value.days) {
                                if (value.days[check_day] && value != current_checked_obj) {
                                    value.days[check_day] = false;
                                }
                            }
                        });
            }

            $scope.datepicker1 = [];
            $scope.datepicker2 = [];
            $scope.datepicker3 = [];
            $scope.datepicker4 = [];

            $scope.openPicker = function ($event, object, key, type) {
                $event.preventDefault();
                $event.stopPropagation();
                $scope.datepicker1.length = 0;
                $scope.datepicker2.length = 0;
                $scope.datepicker3.length = 0;
                $scope.datepicker4.length = 0;

                if (type == 1) {
                    $scope.datepicker1[key] = {'opened': true};
                } else if (type == 2) {
                    $scope.datepicker2[key] = {'opened': true};
                } else if (type == 3) {
                    $scope.datepicker3[key] = {'opened': true};
                } else if (type == 4) {
                    $scope.datepicker4[key] = {'opened': true};
                }

                var round_date = new Date();
                var minutes = round_date.getMinutes();
                var hours = round_date.getHours();
                var round_minutes = (parseInt((minutes + 7.5) / 15) * 15) % 60;
                var round_hours = minutes > 52 ? (hours === 23 ? 0 : ++hours) : hours;
                round_date.setHours(round_hours);
                round_date.setMinutes(round_minutes);

                if (type == 1) {
                    if (object.clinic_availability_session_1_new_start_time == "" ||
                            object.clinic_availability_session_1_new_start_time == undefined
                            ) {
                        object.clinic_availability_session_1_new_start_time = round_date;
                    }
                }
                if (type == 2) {
                    if (object.clinic_availability_session_1_new_end_time == "" ||
                            object.clinic_availability_session_1_new_end_time == undefined
                            ) {
                        object.clinic_availability_session_1_new_end_time = round_date;
                    }
                }

                if (type == 3) {
                    if (object.clinic_availability_session_2_new_start_time == "" ||
                            object.clinic_availability_session_2_new_start_time == undefined
                            ) {
                        object.clinic_availability_session_2_new_start_time = round_date;
                    }
                }

                if (type == 4) {
                    if (object.clinic_availability_session_2_new_end_time == "" ||
                            object.clinic_availability_session_2_new_end_time == undefined
                            ) {
                        object.clinic_availability_session_2_new_end_time = round_date;
                    }
                }

            }


            $scope.doctordatepicker1 = [];
            $scope.doctordatepicker2 = [];
            $scope.doctordatepicker3 = [];
            $scope.doctordatepicker4 = [];

            $scope.openDoctorPicker = function ($event, object, key, type) {
                $event.preventDefault();
                $event.stopPropagation();
                $scope.doctordatepicker1.length = 0;
                $scope.doctordatepicker2.length = 0;
                $scope.doctordatepicker3.length = 0;
                $scope.doctordatepicker4.length = 0;

                if (type == 1) {
                    $scope.doctordatepicker1[key] = {'opened': true};
                } else if (type == 2) {
                    $scope.doctordatepicker2[key] = {'opened': true};
                } else if (type == 3) {
                    $scope.doctordatepicker3[key] = {'opened': true};
                } else if (type == 4) {
                    $scope.doctordatepicker4[key] = {'opened': true};
                }

                var round_date = new Date();
                var minutes = round_date.getMinutes();
                var hours = round_date.getHours();
                var round_minutes = (parseInt((minutes + 7.5) / 15) * 15) % 60;
                var round_hours = minutes > 52 ? (hours === 23 ? 0 : ++hours) : hours;
                round_date.setHours(round_hours);
                round_date.setMinutes(round_minutes);

                if (type == 1) {
                    if (object.doctor_availability_session_1_new_start_time == "" ||
                            object.doctor_availability_session_1_new_start_time == undefined
                            ) {
                        object.doctor_availability_session_1_new_start_time = round_date;
                    }
                }
                if (type == 2) {
                    if (object.doctor_availability_session_1_new_end_time == "" ||
                            object.doctor_availability_session_1_new_end_time == undefined
                            ) {
                        object.doctor_availability_session_1_new_end_time = round_date;
                    }
                }

                if (type == 3) {
                    if (object.doctor_availability_session_2_new_start_time == "" ||
                            object.doctor_availability_session_2_new_start_time == undefined
                            ) {
                        object.doctor_availability_session_2_new_start_time = round_date;
                    }
                }

                if (type == 4) {
                    if (object.doctor_availability_session_2_new_end_time == "" ||
                            object.doctor_availability_session_2_new_end_time == undefined
                            ) {
                        object.doctor_availability_session_2_new_end_time = round_date;
                    }
                }

            }

            /* edit availibility code */
            $scope.editAvailibility = function (form) {

                $scope.submitted = true;
                var flag = false;
                var valid = true;
                if (form.$valid) {

                    angular
                            .forEach($scope.availibility, function (value, key) {
                                if (valid) {
                                    flag = false;
                                    value.doctor_availability_session_1_start_time = $scope.addZero(value.doctor_availability_session_1_new_start_time.getHours()) + ":" + $scope.addZero(value.doctor_availability_session_1_new_start_time.getMinutes());
                                    value.doctor_availability_session_1_end_time = $scope.addZero(value.doctor_availability_session_1_new_end_time.getHours()) + ":" + $scope.addZero(value.doctor_availability_session_1_new_end_time.getMinutes());

                                    if ($scope.compareTime(value.doctor_availability_session_1_start_time, value.doctor_availability_session_1_end_time) == false) {
                                        ngToast.danger("Start time should be less than end time");
                                        return false;
                                    }
                                    if (value.doctor_availability_session_2_new_start_time) {
                                        value.doctor_availability_session_2_start_time = $scope.addZero(value.doctor_availability_session_2_new_start_time.getHours()) + ":" + $scope.addZero(value.doctor_availability_session_2_new_start_time.getMinutes());
                                        value.doctor_availability_session_2_end_time = $scope.addZero(value.doctor_availability_session_2_new_end_time.getHours()) + ":" + $scope.addZero(value.doctor_availability_session_2_new_end_time.getMinutes());

                                        if ($scope.compareTime(value.doctor_availability_session_1_end_time, value.doctor_availability_session_2_start_time) == false) {
                                            ngToast.danger("Please enter valid session timing");
                                            valid = false;
                                            return false;
                                        }
                                        if ($scope.compareTime(value.doctor_availability_session_2_start_time, value.doctor_availability_session_2_end_time) == false) {
                                            ngToast.danger("Start time should be less than end time");
                                            valid = false;
                                            return false;
                                        }
                                    } else {
                                        value.doctor_availability_session_2_start_time = '';
                                        value.doctor_availability_session_2_end_time = '';
                                    }
                                    angular
                                            .forEach(value.days, function (innerValue, innerKey) {
                                                if (innerValue == true) {
                                                    flag = true;
                                                }
                                            });

                                    valid = true;
                                }
                            });

                    if (!flag && valid) {
                        ngToast.danger("Please select atleast one day");
                        valid = false;
                        return false;
                    }
                    if (!valid) {
                        return false;
                    }
                    /* give alert for final warning */
                    SweetAlert.swal({
                        title: $rootScope.app.doctor_practice_edit_alert,
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes!",
                        cancelButtonText: "No",
                        closeOnConfirm: true},
                            function (isConfirm) {

                                if (isConfirm) {
                                    SettingService
                                            .setAvailibility($scope.availibility, function (response) {
                                                if (response.status) {
                                                    $scope.getTimeslot();
                                                    ngToast.success({
                                                        content: response.message,
                                                        className: '',
                                                        dismissOnTimeout: true,
                                                        timeout: 5000
                                                    });
                                                    $("#edit_doctor_timing").modal("hide");
                                                    $scope.getDoctorAvailibility();
                                                } else {
                                                    ngToast.danger(response.message);
                                                }
                                            });
                                }
                            });

                }
            }
            /* edit availibility code */
            $scope.editClinicAvailibility = function (form) {
                $scope.submitted = true;
                var flag = false;
                var valid = true;
                if (form.$valid) {
                    angular
                            .forEach($scope.availibility, function (value, key) {
                                if (valid) {
                                    flag = false;
                                    value.clinic_availability_session_1_start_time = $scope.addZero(value.clinic_availability_session_1_new_start_time.getHours()) + ":" + $scope.addZero(value.clinic_availability_session_1_new_start_time.getMinutes());
                                    value.clinic_availability_session_1_end_time = $scope.addZero(value.clinic_availability_session_1_new_end_time.getHours()) + ":" + $scope.addZero(value.clinic_availability_session_1_new_end_time.getMinutes());

                                    if ($scope.compareTime(value.clinic_availability_session_1_start_time, value.clinic_availability_session_1_end_time) == false) {
                                        ngToast.danger("Start time should be less than end time");
                                        return false;
                                    }
                                    if (value.clinic_availability_session_2_new_start_time) {
                                        value.clinic_availability_session_2_start_time = $scope.addZero(value.clinic_availability_session_2_new_start_time.getHours()) + ":" + $scope.addZero(value.clinic_availability_session_2_new_start_time.getMinutes());
                                        value.clinic_availability_session_2_end_time = $scope.addZero(value.clinic_availability_session_2_new_end_time.getHours()) + ":" + $scope.addZero(value.clinic_availability_session_2_new_end_time.getMinutes());

                                        if ($scope.compareTime(value.clinic_availability_session_1_end_time, value.clinic_availability_session_2_start_time) == false) {
                                            ngToast.danger("Please enter valid session timing");
                                            valid = false;
                                            return false;
                                        }
                                        if ($scope.compareTime(value.clinic_availability_session_2_start_time, value.clinic_availability_session_2_end_time) == false) {
                                            ngToast.danger("Start time should be less than end time");
                                            valid = false;
                                            return false;
                                        }
                                    } else {
                                        value.clinic_availability_session_2_start_time = '';
                                        value.clinic_availability_session_2_end_time = '';
                                    }
                                    angular
                                            .forEach(value.days, function (innerValue, innerKey) {
                                                if (innerValue == true) {
                                                    flag = true;
                                                }
                                            });

                                    valid = true;
                                }
                            });

                    if (!flag && valid) {
                        ngToast.danger("Please select atleast one day");
                        valid = false;
                        return false;
                    }
                    if (!valid) {
                        return false;
                    }
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
                                    var request = {
                                        availibility: $scope.availibility,
                                        clinic_id: $scope.clinic_time_edit_clinic_id
                                    }
                                    SettingService
                                            .setClinicAvailibility(request, function (response) {
                                                if (response.status) {
                                                    ngToast.success({
                                                        content: response.message,
                                                        className: '',
                                                        dismissOnTimeout: true,
                                                        timeout: 5000
                                                    });
                                                    $("#edit_clinic_timing").modal("hide");
                                                    $scope.flg_update_current_clinic_availibility = true;
													$scope.getDoctorAvailibility();
                                                } else {
                                                    ngToast.danger(response.message);
                                                }
                                            });
                                }
                            });

                }
            }

            $scope.checkDaysValidation = function () {
                angular
                        .forEach($scope.edit_availibility.days, function (value, key) {
                            if (value == true) {
                                return false;
                            }
                        });
                return true;
            }


            /* alert module */
            $scope.getLanguages = function () {
                if (!$rootScope.languages || $rootScope.languages.length <= 0) {
                    SettingService
                            .getDBLanguages('', function (response) {
                                if (response.status) {
                                    $rootScope.languages = response.data;
                                }
                            });
                }
            }

            /* get alert setting*/
            $scope.getDoctorAlertSetting = function () {
                var request = {
                    "clinic_id": $rootScope.current_clinic.clinic_id
                };
                SettingService
                        .getAlertSetting(request, function (response) {

                            $scope.alert.patient_data = JSON.parse(response.patient_setting_data.setting_data);
                            if (response.status) {
                                $scope.alert.data = JSON.parse(response.data.setting_data);
                            } else {
                                $scope.alert.data = [
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
                                    {"id": 14, "name": "payment_notification_status", "status": "2"}];
                            }
                        });
            }

            /* save alert form */
            $scope.saveAlert = function () {
                $scope.alert.clinic_id = $rootScope.current_clinic.clinic_id;

                SettingService
                        .setAlertSetting($scope.alert, function (response) {

                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $scope.getDoctorAlertSetting();
                            }
                        });
            }

            /*change clinic for alert module */
            $scope.changeCurrentClinicForAlert = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getDoctorAlertSetting();

            }

            /* change clinic duration */
            $scope.changeClinicDuration = function (key) {
                var clinic_id_arr = [];
                angular.forEach($rootScope.clinic_data, function (value, key) {
                    clinic_id_arr.push(value.clinic_id);
                });
                var request = {
                    clinic_id: $scope.clinic_data[key].clinic_id,
                    clinic_id_arr: clinic_id_arr,
                    duration: $scope.clinic_data[key].doctor_clinic_mapping_duration
                    // duration: $rootScope.current_clinic.doctor_clinic_mapping_duration,
                };
                SettingService
                        .changeDuration(request, function (response) {

                            if (response.status) {
                                if($rootScope.current_clinic.clinic_id == $scope.clinic_data[key].clinic_id) {
                                    $rootScope.current_clinic.doctor_clinic_mapping_duration = $scope.clinic_data[key].doctor_clinic_mapping_duration
                                    if ($rootScope.current_clinic) {
                                        $scope.appointment.duration = $rootScope.current_clinic.doctor_clinic_mapping_duration;
                                    }
                                }
                                $scope.flg_update_current_clinic_availibility = true;
                                $scope.getDoctorAvailibility();
                                // $scope.getTimeslot();
                                // $scope.setCalendar();
                            }
                        });
            }

            /*Share link module*/
            $scope.social_media_master_data = [];
            $scope.getShareLink = function () {
                SettingService
                        .getShareLink('', function (response) {
                            if (response.status) {
                                $scope.share_link_data = response.data;
                            } else {
                                $scope.share_link_data = [];
                            }

                        });
            }
            /*END Share link module*/
            /* Tax setting module start  */
            $scope.payment_mode_master_data = [];
            $scope.getTelePaymentMode = function () {
                SettingService
                        .getTelePaymentMode('', function (response) {
                            if (response.status) {
                                $scope.tele_payment_mode = response.data;
                                angular.forEach($scope.tele_payment_mode, function(value, key){
                                    if(value.doctor_payment_mode_master_id == 5) {
                                        var bank_details = JSON.parse(value.doctor_payment_mode_upi_link);
                                        $scope.tele_payment_mode[key].account_no = bank_details.account_no;
                                        $scope.tele_payment_mode[key].bank_holder_name = bank_details.bank_holder_name;
                                        $scope.tele_payment_mode[key].bank_name = bank_details.bank_name;
                                        $scope.tele_payment_mode[key].ifsc_code = bank_details.ifsc_code;
                                    }
                                });
                            } else {
                                $scope.tele_payment_mode = [];
                            }

                        });
            }
            $scope.getPaymentModeMaster = function () {
                SettingService
                    .getPaymentModeMaster('', function (response) {
                        if (response.status) {
                            $scope.payment_mode_master_data = response.data;
                        } else {
                            $scope.payment_mode_master_data = [];
                        }
                    });
            }
            $scope.getSocialMediaMaster = function () {
                SettingService
                    .getSocialMediaMaster('', function (response) {
                        if (response.status) {
                            $scope.social_media_master_data = response.data;
                        } else {
                            $scope.social_media_master_data = [];
                        }
                    });
            }
            var expiry_date_maxdate = new Date();
            var year = expiry_date_maxdate.getFullYear();
            var month = expiry_date_maxdate.getMonth();
            var day = expiry_date_maxdate.getDate();
            var expiry_date_maxdate = new Date(year, month + 12, day)
            var expiry_date_mindate = new Date(year, month, day);
            $scope.share_link_expiry_date_picker = {
                datepickerOptions: {
                    minDate: expiry_date_mindate,
                    maxDate: expiry_date_maxdate
                },
                open: false
            };
            $scope.addShareLinkPopup = function () {
                $scope.add_edit_share_link = {
                    registration_share_id: ''
                };
                $scope.submitted = false;
            }
            $scope.editShareLinkPopup = function (key) {
                if($scope.share_link_data[key] != undefined) {
                    $scope.add_edit_share_link = {
                        registration_share_id: $scope.share_link_data[key].registration_share_id,
                        social_media_id: $scope.share_link_data[key].registration_share_social_media_id,
                        clinic_id: $scope.share_link_data[key].registration_share_clinic_id,
                        expiry_date: new Date($scope.share_link_data[key].registration_share_expiry_date)
                    };
                    $("#modal_create_reg_link").modal("show");
                    $scope.submitted = false;
                }
            }
            /* function for determine active class of setting menu */
            $scope.getClassActiveSettingMenu = function (route) {
                return ($location.path() === route) ? 'active' : '';
            }
            $scope.printShareLinkPopup = function (key) {
                if($scope.share_link_data[key] != undefined) {
                    $scope.print_url_qrcode = $rootScope.app.base_url + 'prints/share_link_qr';
                    $scope.print_url_qrcode += "?share_id=" + $scope.share_link_data[key].registration_share_id;
                    $scope.print_url_qrcode += "&time=" + $.now();
                    var print_url_qrcode = btoa(encodeURI($scope.print_url_qrcode));
                    $scope.print_url_qrcode = $rootScope.app.base_url + "qrcode/" + print_url_qrcode;
                    $("#modal_print_url_qrcode").modal("show");
                    $scope.qr_code_share = {
                        encrp_id: $scope.share_link_data[key].encrp_id,
                        patient_name: '',
                        email: '',
                        mobile_no: '',
                        mobile_is_required: false,
                        email_is_required: false
                    }
                    $scope.submitted = false;
                }
            }
            $scope.clearPrintShareLink = function () {
                $scope.print_url_qrcode = '';
            }
            $scope.shareQRcodeLink = function (form, share_type) {
                if(share_type == 'email')
                    $scope.qr_code_share.email_is_required = true;
                else
                    $scope.qr_code_share.email_is_required = false;
                if(share_type == 'sms' || share_type == 'whatsapp')
                    $scope.qr_code_share.mobile_is_required = true;
                else
                    $scope.qr_code_share.mobile_is_required = false;
                $scope.submitted = true;
                if (form.$valid) {
                    var request = {
                        encrp_id: $scope.qr_code_share.encrp_id,
                        mobile_no: $scope.qr_code_share.mobile_no,
                        email: $scope.qr_code_share.email,
                        patient_name: $scope.qr_code_share.patient_name,
                        share_type: share_type
                    };
                    SettingService
                        .shareQrCodeLink(request, function (response) {
                        if (response.status) {
                            ngToast.success({content: response.message});
                            $("#modal_print_url_qrcode").modal("hide");
                            $scope.clearPrintShareLink();
                            $scope.submitted = false;
                        } else {
                            ngToast.danger(response.message);
                        }
                    }, function (error) {
                        $rootScope.handleError(error);
                    });
                }
            }
            $scope.deleteShareLink = function (id) {
                var request = {
                    registration_share_id : id
                };
                $rootScope.app.isLoader = true;
                SweetAlert.swal({
                    title: "Are you sure want to delete this share link?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteShareLink(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getShareLink();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }
            $scope.create_reg_link = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    if($scope.add_edit_share_link.expiry_date != undefined && $scope.add_edit_share_link.expiry_date != ''){
                        var expiry_date = $scope.add_edit_share_link.expiry_date;
                        var month = expiry_date.getMonth() + 1;
                        var day = expiry_date.getDate();
                        var year = expiry_date.getFullYear();
                        $scope.add_edit_share_link.link_expiry_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    }
                    SettingService
                        .createRegLink($scope.add_edit_share_link, function (response) {
                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $scope.getShareLink();
                                $("#modal_create_reg_link").modal("hide");
                                $scope.submitted = false;
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                }
            }
            $scope.changeCurrentClinicForPaymentMode = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getTelePaymentMode();
            }
            $scope.addTelePaymentModePopup = function () {
                $scope.tele_payment_data = {
                    doctor_payment_mode_id : '',
                    master_id : '',
                    upi_link : ''
                };
                $scope.submitted = false;
            }
            $scope.editTelePaymentMode = function (key) {
                $scope.tele_payment_data = {
                    doctor_payment_mode_id : $scope.tele_payment_mode[key].doctor_payment_mode_id,
                    master_id : $scope.tele_payment_mode[key].doctor_payment_mode_master_id,
                    upi_link : $scope.tele_payment_mode[key].doctor_payment_mode_upi_link
                };
                if($scope.tele_payment_data.master_id == 5) {
                    var bank_details = JSON.parse($scope.tele_payment_mode[key].doctor_payment_mode_upi_link);
                    $scope.tele_payment_data.account_no = bank_details.account_no;
                    $scope.tele_payment_data.bank_holder_name = bank_details.bank_holder_name;
                    $scope.tele_payment_data.bank_name = bank_details.bank_name;
                    $scope.tele_payment_data.ifsc_code = bank_details.ifsc_code;
                    $scope.tele_payment_data.upi_link = "";
                } else {
                    $scope.tele_payment_data.upi_link = $scope.tele_payment_mode[key].doctor_payment_mode_upi_link;
                }
                $scope.submitted = false;
                $("#modal_add_tele_payment_mode").modal("show");
            }
            $scope.add_tele_payment_mode = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                        .add_tele_payment_mode($scope.tele_payment_data, function (response) {
                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $scope.getTelePaymentMode();
                                $("#modal_add_tele_payment_mode").modal("hide");
                                $scope.submitted = false;
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                }
            }
            $scope.deleteTelePaymentMode = function (id) {
                var request = {
                    doctor_payment_mode_id : id
                };
                $rootScope.app.isLoader = true;
                SweetAlert.swal({
                    title: "Are you sure want to delete this payment mode?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteTelePaymentMode(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getTelePaymentMode();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }

            $scope.getPatientGroups = function () {
                SettingService
                        .getPatientGroups('', function (response) {
                            if (response.status) {
                                $scope.patient_groups_list = response.data;
                            } else {
                                $scope.patient_groups_list = [];
                            }

                        });
            }
            $scope.currentPage = 0;
            $scope.per_page = 10;
            $scope.is_search_patient_group = false;
            $scope.searchPatientGroup = function (number, is_clear) {
                if(is_clear != undefined && is_clear == 1)
                    $scope.patient_group_data.user_ids = [];
                $scope.currentPage = number;
                var request = {
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    patient_gender: $scope.patient_group_data.patient_gender,
                    patient_age_group: $scope.patient_group_data.patient_age_group,
                    patient_disease_ids: $scope.patient_group_data.patient_disease_ids,
                    page: $scope.currentPage,
                    per_page: $scope.per_page,
                }
                $scope.submitted = false;
                $scope.patient_group_data.is_search_required = false;
                if((request.patient_gender == undefined || request.patient_gender == '') && (request.patient_age_group == undefined || request.patient_age_group == '') && (request.patient_disease_ids == undefined || request.patient_disease_ids.length == 0)) {
                    $scope.submitted = true;
                    $scope.patient_group_data.is_search_required = true;
                    return false;
                }
                SettingService
                    .searchPatientGroup(request, function (response) {
                        if (response.status) {
                            $scope.patient_group_data.selected_all_pt = false;
                            angular.forEach(response.data, function(val,k) { 
                                if($scope.patient_group_data.user_ids[val.user_id] == false)
                                    $scope.patient_group_data.selected_all_pt = false;

                                if($scope.patient_group_data.user_ids[val.user_id] == undefined)
                                    $scope.patient_group_data.user_ids[val.user_id] = false; 
                            });
                            $scope.search_patient_groups = response.data;
                            $scope.total_rows = response.count;
                            $scope.total_page = Math.ceil(response.count/$scope.per_page);
                            $scope.last_rows = $scope.currentPage*$scope.per_page;
                            if($scope.last_rows > $scope.total_rows)
                                $scope.last_rows = $scope.total_rows;
                            $rootScope.updateBackDropModalHeight('add_patient_group_modal');
                        } else {
                            $scope.search_patient_groups = [];
                        }
                        $scope.is_search_patient_group = true;
                    });
            }
            $scope.selectAllPatients = function () {
                if($scope.patient_group_data.select_all_patients)
                    $scope.patient_group_data.is_read_only = true;
                else
                    $scope.patient_group_data.is_read_only = false;
            }
            $scope.getNextPrev = function (val) {
                if(val == 'next') {
                    if($scope.currentPage >= $scope.total_page)
                        return false;
                    var number = $scope.currentPage+1;
                }
                if(val == 'prev') {
                    if($scope.currentPage <= 1)
                        return false;
                    var number = $scope.currentPage-1;
                }
                if($scope.is_search_patient_group)
                    $scope.searchPatientGroup(number);
                else
                    $scope.getPatientGroupMembers(number);
            }
            $scope.addPatientGroupModePopup = function () {
                $scope.patient_group_data = {
                    patient_gender: 'all',
                    patient_age_group: '7',
                    patient_group_id: '',
                    patient_group_name: '',
                    select_all_patients: false,
                    auto_added_patients: false,
                    is_read_only: false,
                    user_ids: [],
                    patient_disease_ids: []
                }
                $scope.search_patient_groups = [];
                $scope.is_search_patient_group = false;
            }
            $scope.togglePtSelectAll = function() {
                var toggleStatus = $scope.patient_group_data.selected_all_pt ? true : false;
                angular.forEach($scope.search_patient_groups, function(val,key){ 
                    $scope.patient_group_data.user_ids[val.user_id] = toggleStatus; 
                });

            }
            $scope.groupMemberToggled = function() {
                $scope.patient_group_data.selected_all_pt = $scope.patient_group_data.user_ids.every(function(itm, id){ 
                    return itm; 
                });
            }
            $scope.someSelected = function () {
                var selected = [];
                angular.forEach($scope.patient_group_data.user_ids, function (value, key) {
                    if(value)
                        selected.push(1);
                });
                if(selected.length > 0 || $scope.patient_group_data.select_all_patients || $scope.patient_group_data.auto_added_patients)
                    return true;
                else
                    return false;
            }
            $scope.editPatientGroup = function (key) {
                $scope.is_search_patient_group = false;
                var group_disease_id = $scope.patient_groups_list[key].patient_group_disease_id;
                if(group_disease_id != null && group_disease_id != '')
                    group_disease_id = group_disease_id.split(",");
                else
                    group_disease_id = [];

                $scope.patient_group_data = {
                    patient_gender: $scope.patient_groups_list[key].patient_group_gender,
                    patient_age_group: $scope.patient_groups_list[key].patient_group_age,
                    patient_group_id: $scope.patient_groups_list[key].patient_group_id,
                    patient_group_name: $scope.patient_groups_list[key].patient_group_title,
                    select_all_patients: ($scope.patient_groups_list[key].patient_group_all_added == "1") ? true : false,
                    auto_added_patients: ($scope.patient_groups_list[key].patient_group_auto_added == "1") ? true : false,
                    user_ids: [],
                    patient_disease_ids: group_disease_id
                }
                $scope.getPatientGroupMembers(1);
                $scope.submitted = false;
                $("#modal_add_patient_group").modal("show");
            }
            $scope.getPatientGroupMembers = function (number) {
                $scope.currentPage = number;
                var request = {
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    patient_group_id: $scope.patient_group_data.patient_group_id,
                    page: $scope.currentPage,
                    per_page: $scope.per_page
                }
                SettingService
                    .getPatientGroupMembers(request, function (response) {
                        if (response.status) {
                            $scope.patient_group_data.selected_all_pt = true;
                            angular.forEach(response.data, function(val,k) { 
                                if($scope.patient_group_data.user_ids[val.user_id] == false)
                                    $scope.patient_group_data.selected_all_pt = false;

                                if($scope.patient_group_data.user_ids[val.user_id] == undefined)
                                    $scope.patient_group_data.user_ids[val.user_id] = true; 
                            });
                            $scope.search_patient_groups = response.data;
                            $scope.total_rows = response.count;
                            $scope.total_page = Math.ceil(response.count/$scope.per_page);
                            $scope.last_rows = $scope.currentPage*$scope.per_page;
                            if($scope.last_rows > $scope.total_rows)
                                $scope.last_rows = $scope.total_rows;
                            $rootScope.updateBackDropModalHeight('add_patient_group_modal');
                        } else {
                            $scope.search_patient_groups = [];
                        }
                    });
            }
            $scope.add_patient_group = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    $scope.patient_group_data.is_search_required = false;
                    if(($scope.patient_group_data.patient_gender == undefined || $scope.patient_group_data.patient_gender == '') &&  ($scope.patient_group_data.patient_age_group == undefined || $scope.patient_group_data.patient_age_group == '') && ( $scope.patient_group_data.patient_disease_ids == undefined ||  $scope.patient_group_data.patient_disease_ids.length == 0)) {
                        $scope.submitted = true;
                        $scope.patient_group_data.is_search_required = true;
                        return false;
                    }
                    var member_ids = [];
                    angular.forEach($scope.patient_group_data.user_ids, function (selected, id) {
                        if (selected) {
                           member_ids.push(id);
                        }
                    });
                   var request = {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: ($rootScope.currentUser.doctor_id) ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                        patient_group_id: $scope.patient_group_data.patient_group_id,
                        patient_group_title: $scope.patient_group_data.patient_group_name,
                        patient_group_gender: $scope.patient_group_data.patient_gender,
                        patient_age_group: $scope.patient_group_data.patient_age_group,
                        patient_disease_ids: $scope.patient_group_data.patient_disease_ids,
                        member_ids: member_ids,
                        select_all_patients: $scope.patient_group_data.select_all_patients,
                        auto_added_patients: $scope.patient_group_data.auto_added_patients,
                    }
                    SettingService
                        .add_patient_group(request, function (response) {
                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $scope.getPatientGroups();
                                $("#modal_add_patient_group").modal("hide");
                                $scope.submitted = false;
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                }
            }
            $scope.patient_diseases = undefined;
            $scope.getPatientDiseases = function () {
                var request = {
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.currentUser.user_id,
                    access_token: $rootScope.currentUser.access_token
                }
                SettingService
                    .getPatientDiseases(request, function (response) {
                        if (response.status) {
                            $scope.patient_diseases = response.data;
                            
                        } else {
                            $scope.patient_diseases = undefined;
                        }
                    });
            }
            $scope.deletePatientGroup = function (id) {
                var request = {
                    patient_group_id : id
                };
                $rootScope.app.isLoader = true;
                SweetAlert.swal({
                    title: "Are you sure want to delete this patient group?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deletePatientGroup(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getPatientGroups();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }
            /* get tax setting */
            $scope.getTaxSetting = function () {
                $scope.current_tax_tab = 1;
                SettingService
                        .getTax('', function (response) {
                            if (response.status) {
                                $scope.taxes = response.data;
                            } else {
                                $scope.taxes = [];
                            }
                        });
            }

            $scope.changeCurrentClinicForBilling = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getTaxSetting();
            }

            $scope.addTax = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    $scope.taxObj.splice($scope.taxObj.length - 1, 1);
                    SettingService.addNewTax($scope.taxObj, function (response) {
                        if (response.status) {
                            ngToast.success({
                                content: response.message
                            });
                            $scope.taxObj = [{}];
                            $scope.submitted = false;
                            $("#modal_add_tax").modal("hide");
                            $scope.getTaxSetting()
                        } else {
                            ngToast.danger(response.message);
                        }
                    });

                } else {
                    SweetAlert.swal($rootScope.app.common_error);
                }
            }
            $scope.addTaxObj = function (key, form) {
                //$scope.submitted = true;
                var is_add = false;
                if ($scope.taxObj.length > 0) {
                    angular.forEach($scope.taxObj, function (value, key) {
                        if (value.tax_name == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.taxObj.push({
                            tax_name: '',
                            tax_value: ''
                        });
                    }
                }
            }
            $scope.isTaxObjRequired = function (key) {
                if ($scope.taxObj.length == 1) {
                    return true;
                }
                if (!$scope.taxObj[key].tax_name && !$scope.taxObj[key].tax_value)
                {
                    return false;
                } else {
                    return true;
                }
            }
            $scope.removeTaxObj = function (key) {
                $scope.taxObj.splice(key, 1);
                /* $scope.tax.tax_id = $scope.taxObj[key].tax_id;
                 SettingService
                 .deleteTax($scope.tax, function (response) {
                 if (response.status == true) {
                 $scope.taxObj.splice(key, 1);
                 }
                 $rootScope.app.isLoader = false;
                 });
                 */
            }
            /* get payments modes api */
            $scope.getPaymentModes = function () {
                $scope.current_tax_tab = 2;
                SettingService
                        .getModes('', function (response) {

                            if (response.status) {
                                $scope.payment_modes = response.data;
                            } else {
                                $scope.payment_modes = [];
                            }
                        });
            }

            /* change tax tab code */
            $scope.changeTaxTab = function (type) {
                $scope.current_tax_tab = type;
                if (type == 1 && $scope.taxes.length == 0) {
                    $scope.getTaxSetting();
                }
            }
            $scope.cancleTaxBtn = function () {
                $scope.tax = {};
                $scope.submitted = false;
                $scope.taxObj = [{}];
                $scope.getTaxSetting();
            }

            /* tac detail */
            $scope.taxDetail = function (id) {
                $scope.tax.tax_name = $scope.taxes[id].tax_name;
                $scope.tax.tax_value = $scope.taxes[id].tax_value;
                $scope.tax.tax_id = $scope.taxes[id].tax_id;

                $("#modal_edit_tax").modal("show");
            }

            /* tax edit code */
            $scope.editTax = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .editTaxCode($scope.tax, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $("#modal_edit_tax").modal("hide");
                                    $scope.submitted = false;
                                    $scope.tax = {};
                                    $scope.getTaxSetting();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
            /* tax delete code */
            $scope.deleteTax = function (id) {
                $scope.tax.tax_id = $scope.taxes[id].tax_id;
                $rootScope.app.isLoader = true;
                SweetAlert.swal({
                    title: "Are you sure want to delete this tax?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteTax($scope.tax, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getTaxSetting();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }
            $scope.addPaymentModeObj = function () {
                var is_add = false;
                if ($scope.modeObj.length > 0) {
                    angular.forEach($scope.modeObj, function (value, key) {
                        if (value.name == '') {
                            is_add = true;
                        }
                    });
                    if (is_add == false) {
                        $scope.modeObj.push({
                            name: '',
                            payment_type: '',
                            fee: ''
                        });
                    }
                }
            }
            $scope.isPaymentModeObjRequired = function (key) {
                if ($scope.modeObj.length == 1) {
                    return true;
                }
                if (!$scope.modeObj[key].name && !$scope.modeObj[key].payment_type && !$scope.modeObj[key].fee)
                {
                    return false;
                } else {
                    return true;
                }
            }
            $scope.removePaymentModeObj = function (key) {
                $scope.modeObj.splice(key, 1);
                /* $scope.tax.tax_id = $scope.taxObj[key].tax_id;
                 SettingService
                 .deleteTax($scope.tax, function (response) {
                 if (response.status == true) {
                 $scope.taxObj.splice(key, 1);
                 }
                 $rootScope.app.isLoader = false;
                 });
                 */
            }
            /* add mode start */
            $scope.addMode = function (form) {
                $scope.submitted = true;

                if (form.$valid) {
                    $scope.modeObj.splice($scope.modeObj.length - 1, 1);
                    SettingService
                            .addNewMode($scope.modeObj, function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $scope.getPaymentModes();
                                    $scope.modeObj = [{}];
                                    $("#modal_add_mode").modal("hide");
                                    $scope.submitted = false;
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
            $scope.addPaymentMode = function (key, form) {
                $scope.submitted = true;

                if (form.$valid) {
                    SettingService
                            .addNewMode($scope.modeObj[key], function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $scope.modeObj[key].payment_mode_id = response.inserted_id;
                                    $scope.modeObj.push({});
                                    $scope.current_mode = {};
                                    $scope.submitted = false;
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
            $scope.removeModeObj = function (key) {
                SettingService
                        .deleteMode($scope.modeObj[key].payment_mode_id, function (response) {
                            if (response.status == true) {
                                ngToast.success({
                                    content: response.message,
                                    timeout: 5000
                                });
                                $scope.submitted = false;
                                $scope.modeObj.splice(key, 1);
                            }
                            $rootScope.app.isLoader = false;
                        });
            }

            /* get defualt payment types */
            $scope.getPaymentTypes = function () {
                $scope.current_catelog_tab = 1;
                $scope.currentClinicalPlaceholder = $scope.clinicalPlaceholder[0];
                SettingService
                        .getPaymentTypes('', function (response) {
                            if (response.status) {
                                $scope.payment_types = response.data;
                            }
                        });
            }
            $scope.cancleModeBtn = function () {
                $scope.current_mode = {};
                $scope.modeObj = [{}];
                $scope.getPaymentModes();
            }
            /* mode detail code */
            $scope.detailMode = function (key) {
                $scope.current_mode.name = $scope.payment_modes[key].payment_mode_name;
                $scope.current_mode.fee = $scope.payment_modes[key].payment_mode_vendor_fee;
                $scope.current_mode.payment_type = $scope.payment_modes[key].payment_type_id;
                $scope.current_mode.id = $scope.payment_modes[key].payment_mode_id;

                $("#modal_edit_mode").modal("show");
            }
            $scope.editMode = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .editMode($scope.current_mode, function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $scope.getPaymentModes();
                                    $("#modal_edit_mode").modal("hide");
                                    $scope.submitted = false;
                                    $scope.current_mode = {};
                                } else {
                                    ngToast.danger(response.message);
                                }
                            });
                }
            }
            /* delete mode code */
            $scope.deleteMode = function (mode_id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this payment mode?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteMode(mode_id, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getPaymentModes();
                                        }
                                        $rootScope.app.isLoader = false;
                                    });
                        });
            }
            /* Tax setting module end  */


            /* fee strcuture module start */
            $scope.addFee = function (form) {
                $scope.submitted = true;
                if (form.$valid) {

                    SettingService
                            .addFeeService($scope.current_fee, function (response) {

                                if (response.status == true) {
                                    $scope.current_fee = {
                                        instruction: ''
                                    };
                                    $scope.submitted = false;
                                    $scope.add_instruction = false;
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });

                                    $("#modal_add_fee_details").modal("hide");
                                    $scope.getFeesList();
                                } else {
                                    ngToast.danger(response.message);
                                }

                            });
                }
            }
            $scope.calculateFees = function () {

                var basic_cost = Number($scope.current_fee.basic_cost);
                var taxes = $scope.current_fee.tax_id;
                $scope.current_fee.tax_ids = '';
                var final_amount = Number(basic_cost);

                if (!isNaN(basic_cost)) {
                    angular
                            .forEach(taxes, function (value, key) {
                                var tax_value = (basic_cost * Number(value.tax_value)) / 100;
                                final_amount += tax_value;
                                $scope.current_fee.tax_ids += value.tax_id + ',';
                            });
                }
                $scope.current_fee.tax_ids = $scope.current_fee.tax_ids.substring(0, $scope.current_fee.tax_ids.length - 1);
                if (!isNaN(final_amount)) {
                    $scope.current_fee.final_amount = Number(final_amount).toFixed(2);
                } else {
                    $scope.current_fee.final_amount = '';
                }
            };
            $scope.calculateFeesForEdit = function () {

                var basic_cost = Number($scope.current_fee.basic_cost);
                var taxes = $scope.current_fee.tax_id;

                var final_amount = Number(basic_cost);
                $scope.current_fee.tax_ids = '';
                if (!isNaN(basic_cost)) {
                    angular
                            .forEach(taxes, function (value, key) {

                                angular
                                        .forEach($scope.taxes, function (innerValue, innerKey) {
                                            if (innerValue.tax_id == value) {
                                                var tax_value = (basic_cost * Number(innerValue.tax_value)) / 100;
                                                final_amount += tax_value;
                                                $scope.current_fee.tax_ids += innerValue.tax_id + ',';
                                            }
                                        });


                            });
                }
                $scope.current_fee.tax_ids = $scope.current_fee.tax_ids.substring(0, $scope.current_fee.tax_ids.length - 1);
                if (!isNaN(basic_cost)) {
                    $scope.current_fee.final_amount = Number(final_amount).toFixed(2);
                } else {
                    $scope.current_fee.final_amount = '';
                }
            };
            /* get fees list */
            $scope.getFeesList = function () {
                var request = {
                    search: ''
                }
                SettingService
                        .getFeesListDB(request, function (response) {

                            if (response.status) {
                                $scope.fees_list = response.data;
                                angular
                                        .forEach($scope.fees_list, function (value, key) {
                                            $scope.fees_list[key].tax_value_arr = value.tax_value.split(',');
                                            $scope.fees_list[key].tax_name_arr = value.tax_name.split(',');
                                            var final_amount = Number($scope.fees_list[key].pricing_catalog_cost);
                                            angular
                                                    .forEach($scope.fees_list[key].tax_value_arr, function (innervalue, innerkey) {
                                                        var tax_value = (Number($scope.fees_list[key].pricing_catalog_cost) * Number(innervalue)) / 100;
                                                        final_amount += tax_value;

                                                    });
                                            $scope.fees_list[key].final_amount = Number(final_amount).toFixed(2);
                                        });
                            } else {
                                $scope.fees_list = [];
                            }
                        });
            }
            $scope.changeCurrentClinicForFee = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getFeesList();
            }
            /* delete fee code */
            $scope.deleteFee = function (feeId) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteFee(feeId, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getFeesList();
                                        }
                                        $rootScope.app.isLoader = false;
                                    });
                        });
            }
            /* detail fee code */
            $scope.detailFee = function (key) {


                $scope.current_fee.basic_cost = $scope.fees_list[key].pricing_catalog_cost;
                $scope.current_fee.service_name = $scope.fees_list[key].pricing_catalog_name;
                $scope.current_fee.id = $scope.fees_list[key].pricing_catalog_id;
                $scope.current_fee.instruction = $scope.fees_list[key].pricing_catalog_instructions;
                if ($scope.current_fee.instruction != '') {
                    $scope.add_instruction = true;
                } else {
                    $scope.add_instruction = false;
                }
                var current_tax_id_arr = $scope.fees_list[key].tax_id.split(',');
                var tax_id_object = [];
                var final_amount = Number($scope.current_fee.basic_cost);
                $scope.current_fee.tax_ids = '';
                angular
                        .forEach(current_tax_id_arr, function (value, innerkey) {
                            tax_id_object.push(value);
                            var tax_value = (Number($scope.current_fee.basic_cost) * Number($scope.fees_list[key].tax_value_arr[innerkey])) / 100;
                            final_amount += tax_value;
                            $scope.current_fee.tax_ids += value + ',';
                        });
                $("#modal_edit_fee_details").modal('show');
                $scope.current_fee.tax_id = tax_id_object;
                $scope.current_fee.final_amount = final_amount;

                $("#tax_id1").trigger('chosen:updated');
            }

            /* edit fee code */
            $scope.editFee = function (form) {

                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .editFee($scope.current_fee, function (response) {

                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $("#modal_edit_fee_details").modal('hide');
                                    $scope.current_fee = {
                                        instruction: ''
                                    };
                                    $scope.add_instruction = false;
                                    $scope.getFeesList();
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $rootScope.app.isLoader = false;
                            })
                }
            }
            $scope.clearFeeObj = function () {
                $scope.current_fee = {
                    instruction: ''
                };
                $scope.submitted = false;
                $scope.add_instruction = false;
            }
            /* fee strcuture module end */

            /* clinical catelog module start */
            $scope.changeClinicalTab = function (type) {
                $scope.current_catelog_tab = type;
                $scope.currentClinicalPlaceholder = $scope.clinicalPlaceholder[type - 1];
                $scope.getClinicalNotes();
            }

            /* get clinical notes */
            $scope.getClinicalNotes = function () {

                var request = {
                    clinical_notes_type: $scope.current_catelog_tab,
                    search: $scope.current_clinic_note.text_name
                };
                SettingService
                        .getClinicalNotesFromDB(request, 2, function (response) {
                            $scope.clinical_notes = response.data;
                        });

            }

            $scope.changeCurrentClinicForClinicalNote = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getClinicalNotes();
            }

            /* delete clinical note code */
            $scope.deleteCatelog = function (id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this note?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteCatelogNote(id, function (response) {
                                        if (response.status) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getClinicalNotes();
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                    });
                        });



            }
            $scope.closeAddNoteForm = function () {
                $scope.current_clinic_note.addOpen = false;
                $scope.current_clinic_note.text_name_add = '';
                $scope.submitted = false;
            }
            //add clinical note 
            $scope.addClinicalNote = function () {
                $scope.submitted = true;

                var request = {
                    title: $scope.current_clinic_note.text_name_add,
                    clinical_notes_type: $scope.current_catelog_tab
                };
                if ($scope.current_clinic_note.text_name_add) {
                    SettingService
                            .addClinicalNote(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_clinic_note.text_name_add = '';
                                    $scope.current_clinic_note.addOpen = false;
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getClinicalNotes();
                            });
                } else {

                }

            }
            $scope.clearSearchName = function () {
                $scope.current_clinic_note.text_name = '';
                $scope.getClinicalNotes();
                $scope.current_clinic_note.addOpen = false;
            }

            $scope.detailCatelog = function (key) {
                $scope.current_clinic_note.edit_text_name = $scope.clinical_notes[key].clinical_notes_catalog_title;
                $scope.current_clinic_note.id = $scope.clinical_notes[key].clinical_notes_catalog_id;
                $scope.current_clinic_note.clinical_notes_type = $scope.current_catelog_tab;
                $("#modal_edit_note").modal("show");
            }

            /* edit clinical note code */
            $scope.editClinicalNote = function (form) {

                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .editNote($scope.current_clinic_note, function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_clinic_note.edit_text_name = '';
                                    $scope.submitted = false;
                                    $("#modal_edit_note").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getClinicalNotes();
                            });
                }
            }
            /* clinical catelog module start */
            /* Diet Instructions module start */
            /* get Diet Instructions */
            $scope.prescriptionTab = 1;
            $scope.investigations = [];
            $scope.investigations_data = {};
            $scope.investigations_data.search = '';
            $scope.addMoreInvInstruction = function () {
                $scope.investigations_data.instruction.push({'instruction' : ''});
            }
            $scope.removeInvInstruction = function (index) {
                $scope.investigations_data.instruction.splice(index, 1);
            }
            $scope.addInvestigation = function () {
                $scope.investigations_data.health_analytics_test_id = '';
                $scope.investigations_data.health_analytics_test_name = '';
                $scope.investigations_data.health_analytics_test_doctor_id = '';
                $scope.investigations_data.instruction = [];
                $scope.investigations_data.instruction.push({'instruction' : ''});
                $("#modal_investigation").modal("show");
            }
            $scope.editInvestigation = function (key) {
                SettingService
                    .getInvestigationInstructions($scope.investigations[key].health_analytics_test_id, function (response) {
                        $scope.diet_instructions = response.data;
                        $scope.investigations_data.health_analytics_test_id = $scope.investigations[key].health_analytics_test_id;
                        $scope.investigations_data.health_analytics_test_name = $scope.investigations[key].health_analytics_test_name;
                        $scope.investigations_data.health_analytics_test_doctor_id = $scope.investigations[key].health_analytics_test_doctor_id;
                        $scope.investigations_data.instruction = [];
                        if(response.data.length > 0)
                            $scope.investigations_data.instruction = response.data;
                        else
                            $scope.investigations_data.instruction.push({'instruction' : ''});
                        $("#modal_investigation").modal("show");
                    });
            }
            $scope.changePrescriptionTab = function (tab) {
                $scope.prescriptionTab = tab;
                if(tab==1) {
                    $scope.getDietInstructions(1);
                    $scope.getLanguages();
                } else if(tab==2) {
                    $scope.getInvestigations(1);
                } else if(tab==3) {
                    $scope.getRxInstructions(1);
                    $scope.getLanguages();
                }
            }
            $scope.translate_data = [];
            $scope.AddLangauge = function () {
                $scope.translate_data.push({});
                $("#modal_edit_rx_instructions .modal-backdrop.in").height($("#modal_edit_rx_instructions .modal-backdrop.in").height() + 100);
            }
            $scope.removeLangauge = function (key) {
                $scope.translate_data.splice(key, 1);
            }
            $scope.notePaste = function (e, key) {
                var strng = e.target.value;
                $scope.translate_data[key].note = strng.substring(0, e.target.selectionStart) + e.originalEvent.clipboardData.getData('text/plain') + strng.substring(e.target.selectionEnd, strng.length);
            }
            $scope.getDietInstructions = function (number) {
                $scope.getLanguages();
                $scope.prescriptionTab = 1;
                $scope.currentPage = number;
                var request = {
                    search: $scope.current_diet_instructions.text_name,
                    page: $scope.currentPage,
                    per_page: $scope.per_page,
                    type: 1
                };
                SettingService
                        .getInstructions(request, function (response) {
                            $scope.diet_instructions = response.data;
                            $scope.total_rows = response.count;
                            $scope.total_page = Math.ceil(response.count/$scope.per_page);
                            $scope.last_rows = $scope.currentPage*$scope.per_page;
                            if($scope.last_rows > $scope.total_rows)
                                $scope.last_rows = $scope.total_rows;
                            });

            }
            $scope.getRxInstructions = function (number) {
                $scope.currentPage = number;
                var request = {
                    search: $scope.current_rx_instructions.text_name,
                    page: $scope.currentPage,
                    per_page: $scope.per_page,
                    type: 2
                };
                SettingService
                        .getInstructions(request, function (response) {
                            $scope.rx_instructions = response.data;
                            $scope.total_rows = response.count;
                            $scope.total_page = Math.ceil(response.count/$scope.per_page);
                            $scope.last_rows = $scope.currentPage*$scope.per_page;
                            if($scope.last_rows > $scope.total_rows)
                                $scope.last_rows = $scope.total_rows;
                            });

            }
            $scope.getInvestigations = function (number) {
                $scope.currentPage = number;
                var request = {
                    search: $scope.investigations_data.search,
                    page: $scope.currentPage,
                    per_page: $scope.per_page,
                };
                SettingService
                    .getInvestigations(request, function (response) {
                        $scope.investigations = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.per_page;
                        if($scope.last_rows > $scope.total_rows)
                            $scope.last_rows = $scope.total_rows;
                    });

            }
            $scope.changeCurrentClinicForDietInstruction = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getDietInstructions(1);
            }
            /* delete Diet Instruction code */
            $scope.deleteDietInstruction = function (id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this Diet/Specific Instruction?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: true},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteDietInstruction(id, function (response) {
                                        if (response.status) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            $scope.getDietInstructions(1);
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                    });
                        });
            }
            $scope.deleteRxInstruction = function (id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this note (Freq./ Inst.)?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: true},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteDietInstruction(id, function (response) {
                                        if (response.status) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            $scope.getRxInstructions(1);
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                    });
                        });
            }
            $scope.deleteInvestigation = function (id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this investigation?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: true},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteInvestigation(id, function (response) {
                                        if (response.status) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            $scope.getInvestigations(1);
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                    });
                        });
            }
            $scope.getNextInvPrev = function (val) {
                if(val == 'next') {
                    if($scope.currentPage >= $scope.total_page)
                        return false;
                    var number = $scope.currentPage+1;
                }
                if(val == 'prev') {
                    if($scope.currentPage <= 1)
                        return false;
                    var number = $scope.currentPage-1;
                }
                if($scope.prescriptionTab == 1){
                    $scope.getDietInstructions(number);
                } else if($scope.prescriptionTab == 2){
                    $scope.getInvestigations(number);
                } else if($scope.prescriptionTab == 3){
                    $scope.getRxInstructions(number);
                }
            }
            $scope.closeDietInstructionForm = function () {
                $scope.current_diet_instructions.addOpen = false;
                $scope.current_diet_instructions.text_name_add = '';
                $scope.submitted = false;
            }
            $scope.closeRxInstructionForm = function () {
                $scope.current_rx_instructions.addOpen = false;
                $scope.current_rx_instructions.text_name_add = '';
                $scope.submitted = false;
            }
            $scope.addRxInstructionPopup = function () {
                $scope.submitted = false;
                $scope.current_rx_instructions.edit_text_name = '';
                $scope.current_rx_instructions.id = '';
                $scope.translate_data = [];
                $("#modal_edit_rx_instructions").modal("show");
            }
            $scope.addDietInstructionPopup = function () {
                $scope.submitted = false;
                $scope.current_diet_instructions.edit_text_name = '';
                $scope.current_diet_instructions.id = '';
                $scope.translate_data = [];
                $("#modal_diet_instructions").modal("show");
            }
            
            $scope.addEditInvestigation = function (form) {
                $scope.submitted = true;
                var request = {
                    health_analytics_test_name: $scope.investigations_data.health_analytics_test_name,
                    health_analytics_test_id: $scope.investigations_data.health_analytics_test_id,
                    health_analytics_test_doctor_id: $scope.investigations_data.health_analytics_test_doctor_id,
                    instruction: $scope.investigations_data.instruction
                };
                if (form.$valid) {
                    SettingService
                        .addEditInvestigation(request, function (response) {
                            $scope.submitted = false;
                            if (response.status) {
                                ngToast.success({
                                    content: response.message,
                                    timeout: 5000
                                });
                                $scope.getInvestigations(1);
                                $("#modal_investigation").modal("hide");
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
                }
            }
            $scope.clearSearchDietInstructionName = function () {
                $scope.current_diet_instructions.text_name = '';
                $scope.getDietInstructions(1);
                $scope.current_diet_instructions.addOpen = false;
            }
            $scope.clearSearchRxInstructionName = function () {
                $scope.current_rx_instructions.text_name = '';
                $scope.getRxInstructions(1);
                $scope.current_rx_instructions.addOpen = false;
            }

            $scope.clearSearchInvestigation = function () {
                $scope.investigations_data.search = '';
                $scope.getInvestigations(1);
            }

            $scope.detailDietInstruction = function (key) {
                $scope.current_diet_instructions.edit_text_name = $scope.diet_instructions[key].diet_instruction;
                $scope.current_diet_instructions.id = $scope.diet_instructions[key].id;
                SettingService
                    .getTranslate($scope.diet_instructions[key].id, function (response) {
                        if (response.status) {
                            $scope.translate_data = [];
                            angular.forEach(response.data, function (value) {
                                $scope.translate_data.push({
                                    'language_id': value.translation_lang_id,
                                    'note': value.translation_text
                                });
                            });
                            $("#modal_diet_instructions").modal("show");
                        }
                    });
            }
            $scope.detailRxInstruction = function (key) {
                SettingService
                    .getTranslate($scope.rx_instructions[key].id, function (response) {
                        if (response.status) {
                            $scope.current_rx_instructions.edit_text_name = $scope.rx_instructions[key].diet_instruction;
                            $scope.current_rx_instructions.id = $scope.rx_instructions[key].id;
                            $scope.translate_data = [];
                            angular.forEach(response.data, function (value) {
                                $scope.translate_data.push({
                                    'language_id': value.translation_lang_id,
                                    'note': value.translation_text
                                });
                            });
                            $("#modal_edit_rx_instructions").modal("show");
                        }
                    });
            }

            $scope.addEditRxInstruction = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    if($scope.current_rx_instructions.id != '') {
                        $scope.current_rx_instructions.type = 2;
                        $scope.current_rx_instructions.translate_data = $scope.translate_data;
                        SettingService
                            .editDietInstruction($scope.current_rx_instructions, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_rx_instructions.edit_text_name = '';
                                    $scope.submitted = false;
                                    $("#modal_edit_rx_instructions").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getRxInstructions(1);
                            });
                    } else {
                        var request = {
                            title: $scope.current_rx_instructions.edit_text_name,
                            translate_data: $scope.translate_data,
                            type: 2
                        };
                        SettingService
                            .addDietInstructions(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_rx_instructions.edit_text_name = '';
                                    $scope.submitted = false;
                                    $("#modal_edit_rx_instructions").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getRxInstructions(1);
                            });
                    }
                }
            }
            /* Diet Instructions module End */
            $scope.addEditDietInstruction = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    if($scope.current_diet_instructions.id != '') {
                        $scope.current_diet_instructions.type = 1;
                        $scope.current_diet_instructions.translate_data = $scope.translate_data;
                        SettingService
                            .editDietInstruction($scope.current_diet_instructions, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_diet_instructions.edit_text_name = '';
                                    $scope.submitted = false;
                                    $("#modal_diet_instructions").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getDietInstructions(1);
                            });
                    } else {
                        var request = {
                            title: $scope.current_diet_instructions.edit_text_name,
                            translate_data: $scope.translate_data,
                            type: 1
                        };
                        SettingService
                            .addDietInstructions(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.current_diet_instructions.edit_text_name = '';
                                    $scope.submitted = false;
                                    $("#modal_diet_instructions").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getDietInstructions(1);
                            });
                    }
                }
            }
            /* Brand module start */

            /* get brand names */
            $scope.getBrands = function (number) {
                $scope.currentPage = number;
                var request = {
                    search: $scope.brand_search.search,
                    page: $scope.currentPage,
                    per_page: $scope.per_page
                };
                SettingService
                        .getBrandList(request, function (response) {
                            if(response.data != undefined && response.data.length > 0) {
                                $scope.drugList = response.data;
                                $scope.total_rows = response.count;
                                $scope.total_page = Math.ceil(response.count/$scope.per_page);
                                $scope.last_rows = $scope.currentPage*$scope.per_page;
                                if($scope.last_rows > $scope.total_rows)
                                    $scope.last_rows = $scope.total_rows;
                            } else {
                                $scope.drugList = [];
                            }
                        });
            }
            $scope.getNextPrevBrand = function (val) {
                if(val == 'next') {
                    if($scope.currentPage >= $scope.total_page)
                        return false;
                    var number = $scope.currentPage+1;
                }
                if(val == 'prev') {
                    if($scope.currentPage <= 1)
                        return false;
                    var number = $scope.currentPage-1;
                }
                $scope.getBrands(number);
            }
            $scope.changeCurrentClinicForBrand = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getBrands(1);
            }
            $scope.addMoreBrand = function () {
                $scope.brandList.push({
                    brand_name: '',
                    isOpen: false,
                    default1: '0',
                    default2: '0',
                    default3: '0',
                });
                if($("#modal_add_template").hasClass("in")){
                    $("#modal_add_template").modal('handleUpdate');
                    $("#modal_add_template .modal-backdrop.in").height($("#modal_add_template .modal-backdrop.in").height() + 500);
                }
                if($("#modal_edit_template").hasClass("in")){
                    $("#modal_edit_template").modal('handleUpdate');
                    $("#modal_edit_template .modal-backdrop.in").height($("#modal_edit_template .modal-backdrop.in").height() + 500);
                }
                $("#modal_add_brand").modal('handleUpdate');
                $("#modal_add_brand .modal-backdrop.in").height($("#modal_add_brand .modal-backdrop.in").height() + 500);
            }

            $scope.removeBrandBtn = function () {
                $scope.brandList = [{
                        isOpen: false,
                    }];
                $scope.submitted = false;
            }
            $scope.removeLastBrandObj = function (key) {
                $scope.brandList.splice(key, 1);
            }
            $scope.openSimilarBrand = function (key) {
                $scope.brandList[key].isOpen = true;
                $("#modal_add_brand .modal-backdrop.in").height($("#modal_add_brand .modal-backdrop.in").height() + 50);
            }

            /* add new brand code */
            $scope.addBrand = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    SettingService
                            .addNewBrand($scope.brandList, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.brandList = [{}];
                                    $scope.submitted = false;
                                    $("#modal_add_brand").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                                $scope.getBrands(1);
                            });
                }
            }
            $scope.searchSimilarBrand = function (key) {
                $scope.brandList[key].drug_strength = '';
                $scope.brandList[key].drug_duration = '';
                $scope.brandList[key].drug_duration_value = '';
                $scope.brandList[key].drug_intake = '';
                $scope.brandList[key].drug_instruction = '';
                $scope.brandList[key].drug_drug_generic_id = '';
                $scope.brandList[key].drug_unit_id = '';
                $scope.brandList[key].drug_unit_medicine_type = '';
                $scope.brandList[key].drug_drug_unit_value = '';
                $scope.brandList[key].drug_frequency_id = '';
                $scope.brandList[key].drug_unit_name = '';
                $scope.brandList[key].similar_brand_id = '';
                $("#addCustomBrandTooltip").tooltip({
                    html: true,
                    position: {
                        my: "left top-50"
                        // at: "right"
                    }
                }).tooltip("open");
                var request = {
                    brand_name: $scope.brandList[key].similar_brand,
                    drug_generic_id: ''
                };
                if(request.brand_name == undefined || request.brand_name.length < 2)
                    return false;
                SettingService
                        .searchSimilar(request, function (response) {
                            if (response.status == true) {
                                $scope.search_similar_brand_result = response.data;
                            }
                        });
            }
            $scope.setFocusHere = function (el) {
                $scope.$broadcast(el);
            }
            $scope.setClientData = function (item, type, key) {

                if (type == 8) {
                    $scope.testChief[key].search = item.value;
                    $scope.testChief[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testChief.push({
                    });
                    $scope.focusIndex = Number(key) + 1;
                } else if (type == 9) {
                    $scope.testObservation[key].search = item.value;
                    $scope.testObservation[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testObservation.push({
                    });
                    $scope.focusIndex = Number(key) + 1;
                } else if (type == 10) {
                    $scope.testDiagnosis[key].search = item.value;
                    $scope.testDiagnosis[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testDiagnosis.push({
                    });
                    $scope.focusIndex = Number(key) + 1;
                } else if (type == 11) {
                    $scope.testNotes[key].search = item.value;
                    $scope.testNotes[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testNotes.push({
                    });
                    $scope.focusIndex = Number(key) + 1;
                } else if (type == 7) {

                    //call detail drug code
                    SettingService
                            .getDrugDetail(item.drug_id, function (response) {
                                if (response.status) {
                                    $scope.brandList[key].drug_strength = response.data[0].drug_strength;
                                    response.data[0].drug_duration = "1";
                                    if (response.data[0].drug_duration != 0) {
                                        $scope.brandList[key].drug_duration = response.data[0].drug_duration;
                                    }
                                    $scope.brandList[key].drug_duration_value = response.data[0].drug_duration_value;
                                    if(response.data[0].drug_intake != "0")
                                        $scope.brandList[key].drug_intake = response.data[0].drug_intake;
                                    $scope.brandList[key].drug_instruction = response.data[0].drug_instruction;
                                    if (response.data[0].drug_drug_generic_id) {
                                        $scope.brandList[key].drug_drug_generic_id = response.data[0].drug_drug_generic_id.split(',');
                                    }
                                    $scope.brandList[key].drug_frequency_value = response.data[0].drug_frequency_value;
                                    $scope.brandList[key].drug_unit_id = response.data[0].drug_unit_id;
                                    $scope.brandList[key].drug_unit_medicine_type = response.data[0].drug_unit_medicine_type;
                                    $scope.brandList[key].drug_drug_unit_value = response.data[0].drug_drug_unit_value;
                                    $scope.brandList[key].drug_frequency_id = response.data[0].drug_frequency_id;
                                    $scope.brandList[key].drug_unit_name = response.data[0].drug_unit_name;
                                    $scope.brandList[key].isOpen = true;
                                    $scope.brandList[key].similar_brand_id = item.drug_id;
                                    $scope.brandList[key].dosage = response.data[0].drug_drug_unit_value;
                                    $scope.changeCustomValue(key);
                                    $("#modal_add_brand .modal-backdrop.in").height($("#modal_add_brand .modal-backdrop.in").height() + 50);
                                }
                            });
                } else if (type == 12) {

                    /* get child tests */
                    var parent_id = 0;
                    if (item.health_analytics_test_id) {
                        parent_id = item.health_analytics_test_id;
                    }
                    var request = {
                        search: '',
                        parent_id: parent_id
                    }
                    PatientService
                            .getHealthTest(request, false, function (response) {
                                if (response.data) {
                                    $scope.testLabs.splice(key, 1);
                                    angular
                                            .forEach(response.data, function (value) {
                                                $scope.testLabs.push({
                                                    search: value.health_analytics_test_name,
                                                    id: value.health_analytics_test_id
                                                });
                                            });
                                } else {
                                    $scope.testLabs[key].search = item.health_analytics_test_name;
                                    $scope.testLabs[key].id = item.health_analytics_test_id;
                                }
                                $scope.testLabs.push({
                                    search: ''
                                });
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                    $scope.focusIndex = Number(key) + 1;
                } else if (type == 14) {
                    $scope.fav_doctor_search.id = item.user_id;
                }
            }

            $scope.addCustomBrand = function (key) {
                // $scope.brandList[key].drug_strength = '';
                // $scope.brandList[key].drug_duration_value = response.data[0].drug_duration_value;
                // $scope.brandList[key].drug_intake = response.data[0].drug_intake;
                // $scope.brandList[key].drug_instruction = response.data[0].drug_instruction;
                // if (response.data[0].drug_drug_generic_id) {
                //     $scope.brandList[key].drug_drug_generic_id = response.data[0].drug_drug_generic_id.split(',');
                // }
                // $scope.brandList[key].drug_unit_id = response.data[0].drug_unit_id;
                // $scope.brandList[key].drug_unit_medicine_type = response.data[0].drug_unit_medicine_type;
                // $scope.brandList[key].drug_drug_unit_value = response.data[0].drug_drug_unit_value;
                // $scope.brandList[key].drug_frequency_id = response.data[0].drug_frequency_id;
                // $scope.brandList[key].drug_unit_name = response.data[0].drug_unit_name;
                $scope.brandList[key].isOpen = true;
                $scope.brandList[key].similar_brand_id = 0;
                $scope.brandList[key].default1 = '0';
                $scope.brandList[key].default2 = '0';
                $scope.brandList[key].default3 = '0';
                $scope.brandList[key].brand_name = $scope.brandList[key].similar_brand;
                // $scope.brandList[key].dosage = response.data[0].drug_drug_unit_value;
                // $scope.changeCustomValue(key);
            }

            $scope.setFrequency = function (key) {
                if($scope.brandList[key].default1 != '0' && $scope.brandList[key].default2 == '0' && $scope.brandList[key].default3 == '0') {
                    $scope.brandList[key].drug_frequency_id = '1';
                } else if($scope.brandList[key].default1 == '0' && $scope.brandList[key].default2 != '0' && $scope.brandList[key].default3 == '0') {
                    $scope.brandList[key].drug_frequency_id = '2';
                } else if($scope.brandList[key].default1 == '0' && $scope.brandList[key].default2 == '0' && $scope.brandList[key].default3 != '0') {
                    $scope.brandList[key].drug_frequency_id = '3';
                } else if($scope.brandList[key].default1 != '0' && $scope.brandList[key].default2 == '0' && $scope.brandList[key].default3 != '0') {
                    $scope.brandList[key].drug_frequency_id = '4';
                } else if($scope.brandList[key].default1 != '0' && $scope.brandList[key].default2 != '0' && $scope.brandList[key].default3 == '0') {
                    $scope.brandList[key].drug_frequency_id = '4';
                } else if($scope.brandList[key].default1 == '0' && $scope.brandList[key].default2 != '0' && $scope.brandList[key].default3 != '0') {
                    $scope.brandList[key].drug_frequency_id = '4';
                } else if($scope.brandList[key].default1 != '0' && $scope.brandList[key].default2 != '0' && $scope.brandList[key].default3 != '0') {
                    $scope.brandList[key].drug_frequency_id = '5';
                } else {
                    $scope.brandList[key].drug_frequency_id = '';
                }
            }

            $scope.getInitAllDataForBrand = function () {
                if ($scope.brandGenericList.length == 0) {
                    SettingService
                            .getGeneric('', function (response) {
                                $scope.brandGenericList = response.data;
                            });
                }
                if ($scope.brandTypeList.length == 0) {
                    SettingService
                            .getBrandType('', function (response) {
                                $scope.brandTypeList = response.data;
                            });
                }
                if ($scope.brandFreqList.length == 0) {
                    SettingService
                            .getBrandFreq('', function (response) {
                                $scope.brandFreqList = response.data;
                            });
                }
            }

            $scope.changeBrandType = function (key) {
                angular
                        .forEach($scope.brandTypeList, function (value, innerkey) {
                            if (value.drug_unit_id == $scope.brandList[key].drug_unit_id) {
                                $scope.brandList[key].drug_unit_name = value.drug_unit_name;
                                $scope.brandList[key].drug_unit_medicine_type = value.medicine_type;
                            }
                        })
            }

            /* delete brand code */
            $scope.deleteBrand = function (brand_id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this brand?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteBrand(brand_id, function (response) {
                                        if (response.status) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal(response.message);
                                        } else {
                                            ngToast.danger(response.message);
                                        }
                                        $rootScope.app.isLoader = false;
                                        $scope.getBrands(1);
                                    });
                        });


            }
            /* Brand module end */


            $scope.appendNote = function (key, event, testNotes, type) {

                if (event.keyCode == 13 && testNotes[testNotes.length - 1].search != '' && testNotes[testNotes.length - 1].search != undefined) {
                    testNotes.push({
                    });
                    $scope.focusIndex = Number(key) + 1;
                    return;
                } else {
                    if ((testNotes[key].search == undefined || testNotes[key].search.length <= 0) && key != 0) {
                        testNotes.splice(testNotes.length - 1, 1);
                        $scope.focusIndex = Number(key) - 1;
                        return;
                    }
                }
                if (type == 12) {
                    if (!testNotes[key].id) {
                        PatientService
                                .getHealthTest(testNotes[key], true, function (response) {
                                    $scope.search_labs = response.data;
                                });
                    }
                    return;
                }
                testNotes[key].autoID = '';
                testNotes[key].clinical_notes_type = type;
                if(testNotes[key].search != undefined && type == 3){
                    if(testNotes[key].search.length > 2) {
                        SettingService
                            .getClinicalNotesFromDB(testNotes[key], 1, function (response) {
                                $scope.search_notes = response.data;
                        });
                    }
                } else {
                    SettingService
                        .getClinicalNotesFromDB(testNotes[key], 1, function (response) {
                            $scope.search_notes = response.data;
                    });
                }
            }

            $scope.closeTemplateModal = function () {
                $scope.template_tab.addInvetigateTab = false;
                $scope.template_tab.addRXTab = false;
                $scope.template_tab.addClinicalNoteTab = false;
                $scope.testChief = [{
                    }];
                $scope.testObservation = [{
                    }];
                $scope.testDiagnosis = [{
                    }];
                $scope.testNotes = [{
                    }];
                $scope.brandList = [{
                    }];
                $scope.testLabs = [{
                    }];
                $scope.template.lab_instruction = '';
                $scope.submitted = false;
                $scope.template.template_name = '';
                $scope.template.template_diagnoses = '';
            }
            $scope.removeLabInvestigationObj = function (key) {
                $scope.testLabs.splice(key, 1);
                if ($scope.testLabs.length == 0) {
                    $scope.testLabs = [{}];
                }
            }

            $scope.addTemplate = function (form) {
                $scope.submitted = true;
                if (!$scope.template_tab.addInvetigateTab || !$scope.template_tab.addRXTab || !$scope.template_tab.addClinicalNoteTab) {
                    $scope.template.tab_required = true;
                    return false;
                } else {
                    $scope.template.tab_required = false;
                }

                var request = {
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    diagnosis_name: $scope.template.template_diagnoses,
                    template_name: $scope.template.template_name,
                };
                if (form.$valid) {
                    var final_notes_array = [];
                    angular
                            .forEach($scope.testChief, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testObservation, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testDiagnosis, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testNotes, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });

                    request.clinical_notes = JSON.stringify(final_notes_array);
                    var brandData = [];
                    angular
                            .forEach($scope.brandList, function (value, key) {
                                if (value.similar_brand_id) {
                                    var intake_inst = '';
                                    if (value.intake_instruction) {
                                        intake_inst = value.intake_instruction;
                                    }
                                    var dosage = '';
                                    if (value.dosage) {
                                        dosage = value.dosage;
                                    }
                                    if((value.drug_unit_name=='Tablets' || value.drug_unit_name =='IU') && value.defaultFreqOpen) {
                                        dosage = '';
                                    } else if(value.drug_unit_name=='As Directed') {
                                        dosage = '';
                                    }
                                    var feq_instruction = '';
                                    if (value.freq_instruction) {
                                        feq_instruction = value.freq_instruction;
                                    }
                                    var drug_inst = '';
                                    if (value.drug_instruction) {
                                        drug_inst = value.drug_instruction;
                                    }
                                    var custom_value = '';
                                    if (value.default1 || value.default2 || value.default3) {
                                        if (!value.default1) {
                                            value.default1 = '0';
                                        }
                                        if (!value.default2) {
                                            value.default2 = '0';
                                        }
                                        if (!value.default3) {
                                            value.default3 = '0';
                                        }
                                        custom_value = value.default1 + '-' + value.default2 + '-' + value.default3;
                                    }
                                    var tempBrand = {
                                        "drug_id": value.similar_brand_id,
                                        "drug_intake_instruction": intake_inst,
                                        "drug_custom_frequency": custom_value,
                                        "drug_dosage": dosage,
                                        "drug_frequency_instruction": feq_instruction,
                                        "drug_duration_value": value.drug_duration_value,
                                        "drug_duration": value.drug_duration,
                                        "drug_intake": value.drug_intake,
                                        "drug_frequency": value.drug_frequency_id,
                                        "drug_diet_instruction": drug_inst,
                                    };
                                    brandData.push(tempBrand);
                                }
                            });
                    request.drug_data = JSON.stringify(brandData);
                    request.investigation_instruction = '';
                    var temp = {};
                    angular
                            .forEach($scope.testLabs, function (value, key) {
                                if (value.search && key != ($scope.testLabs.length - 1)) {

                                    if (!value.lab_instruction) {
                                        value.lab_instruction = '';
                                    }
                                    temp[value.search] = value.lab_instruction;

                                }
                            });
                    request.investigation_name = JSON.stringify(temp);
                    if (final_notes_array.length == 0) {
                        $scope.template.is_blank_clinical_notes = true;
                        return;
                    } else {
                        $scope.template.is_blank_clinical_notes = false;
                    }

                    SettingService
                            .addNewTemplate(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $rootScope.app.isLoader = false;
                                    $scope.submitted = false;
                                    $("#modal_add_template").modal("hide");
                                    $scope.testChief = [{
                                        }];
                                    $scope.testObservation = [{
                                        }];
                                    $scope.testDiagnosis = [{
                                        }];
                                    $scope.testNotes = [{
                                        }];
                                    $scope.testLabs = [{
                                        }];
                                    $scope.template = {
                                        lab_instruction: '',
                                        template_diagnoses: '',
                                        template_name: '',
                                    };
                                    $scope.brandList = [{
                                        }];
                                } else {
                                    ngToast.danger(response.message);
                                }

                                $scope.getTemplateList(1);
                            });
                }
            }
            $scope.clearCustomValues = function(key) {
                if($scope.brandList[key].drug_unit_name=='Tablets' || $scope.brandList[key].drug_unit_name=='IU') {
                    $scope.brandList[key].dosage = '0';
                }
            }

            $scope.openClinicalTab = function () {
				if($("#modal_add_template")){
					$("#modal_add_template").modal('handleUpdate');
					$("#modal_add_template .modal-backdrop.in").height($("#modal_add_template .modal-backdrop.in").height() + 500);
				}
            }

            /* get template listing */
            $scope.getTemplateList = function (number) {
                $scope.currentPage = number;
                var request = {
                    page: $scope.currentPage,
                    per_page: $scope.per_page
                };
                SettingService
                        .getTemplate(request, function (response) {
                            $scope.template_list = response.data;
                            if(response.status) {
                                $scope.total_rows = response.count;
                                $scope.total_page = Math.ceil(response.count/$scope.per_page);
                                $scope.last_rows = $scope.currentPage*$scope.per_page;
                                if($scope.last_rows > $scope.total_rows)
                                    $scope.last_rows = $scope.total_rows;
                            }
                            angular
                                    .forEach($scope.template_list, function (value, key) {

                                        var investigation_array = [];
                                        var keys = [];
                                        if (value.template_investigation_name) {
                                            investigation_array = JSON.parse(value.template_investigation_name);
                                            for (var k in investigation_array) {
                                                keys.push(k);
                                            }
                                        }
                                        value.template_investigation_name = keys.join(',');
                                    });
                        });
            }
            $scope.getNextPrevTemp = function (val) {
                if(val == 'next') {
                    if($scope.currentPage >= $scope.total_page)
                        return false;
                    var number = $scope.currentPage+1;
                }
                if(val == 'prev') {
                    if($scope.currentPage <= 1)
                        return false;
                    var number = $scope.currentPage-1;
                }
                $scope.getTemplateList(number);
            }
            $scope.changeCurrentClinicForTemplate = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getTemplateList(1);
            }
            /* delete temp code */
            $scope.deleteTemplate = function (template_id) {
                SweetAlert.swal({
                    title: "Are you sure want to delete this template?",
                    text: "Your will not be able to recover this.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            SettingService
                                    .deleteTemplate(template_id, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getTemplateList(1);
                                        }
                                        $rootScope.app.isLoader = false;
                                    });
                        });
            }

            $scope.checkFormSubmit = function (e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            }
            /* edit template code */
            $scope.editTemplateDetail = function (template_id) {
                SettingService
                        .getTemplateDetail(template_id, function (response) {

                            var temp_obj = response.data;
                            $scope.template.template_diagnoses = temp_obj.template_diagnosis_name;
                            $scope.template.template_name = temp_obj.template_template_name;
                            $scope.template.template_id = template_id;
                            var testChief = [];
                            var testObservation = [];
                            var testDiagnosis = [];
                            var testNotes = [];
                            angular
                                    .forEach(temp_obj.clinical_notes, function (value, key) {
                                        if (value.clinical_notes_catalog_type == 1) {
                                            testChief.push({
                                                search: value.clinical_notes_catalog_title,
                                                autoID: value.clinical_notes_catalog_id,
                                                clinical_notes_type: 1
                                            });
                                        } else if (value.clinical_notes_catalog_type == 2) {
                                            testObservation.push({
                                                search: value.clinical_notes_catalog_title,
                                                autoID: value.clinical_notes_catalog_id,
                                                clinical_notes_type: 2
                                            });
                                        } else if (value.clinical_notes_catalog_type == 3) {
                                            testDiagnosis.push({
                                                search: value.clinical_notes_catalog_title,
                                                autoID: value.clinical_notes_catalog_id,
                                                clinical_notes_type: 3
                                            });
                                        } else if (value.clinical_notes_catalog_type == 4) {
                                            testNotes.push({
                                                search: value.clinical_notes_catalog_title,
                                                autoID: value.clinical_notes_catalog_id,
                                                clinical_notes_type: 4
                                            });
                                        }
                                    });
                            if (testChief.length != 0) {
                                $scope.testChief = testChief;
                            }
                            if (testObservation.length != 0) {
                                $scope.testObservation = testObservation;
                            }
                            if (testDiagnosis.length != 0) {
                                $scope.testDiagnosis = testDiagnosis;
                            }
                            if (testNotes.length != 0) {
                                $scope.testNotes = testNotes;
                            }
                            $scope.template_tab.addClinicalNoteTab = true;
                            $scope.template_tab.addInvetigateTab = true;
                            $scope.template_tab.addRXTab = true;
                            var investigation_array = [];
                            if (temp_obj.template_investigation_name) {
                                investigation_array = JSON.parse(temp_obj.template_investigation_name);
                            }

                            if (investigation_array) {
                                $scope.testLabs = [];
                                angular
                                        .forEach(investigation_array, function (labvalue, labkey) {
                                            var isOpenInst = false;
                                            if (labvalue) {
                                                isOpenInst = true;
                                            }
                                            $scope.testLabs.push({
                                                search: labkey,
                                                lab_instruction: labvalue,
                                                isOpenInst: isOpenInst
                                            });
                                        });
                                $scope.testLabs.push({
                                    search: ''
                                });
                            }
                            var drug_obj = [];
                            angular
                                    .forEach(temp_obj.drug_data, function (value, key) {
                                        var custom_array = value.custom_frequency.split('-');
                                        var default1 = '';
                                        var default2 = '';
                                        var default3 = '';

                                        var freq = value.frequency;
                                        if (freq == 1) {
                                            default1 = '1';
                                            default2 = '0';
                                            default3 = '0';
                                        } else if (freq == 2) {
                                            default1 = '0';
                                            default2 = '1';
                                            default3 = '0';
                                        } else if (freq == 3) {
                                            default1 = '0';
                                            default2 = '0';
                                            default3 = '1';
                                        } else if (freq == 4) {
                                            default1 = '1';
                                            default2 = '0';
                                            default3 = '1';
                                        } else if (freq == 5) {
                                            default1 = '1';
                                            default2 = '1';
                                            default3 = '1';
                                        } else {
                                            default1 = '';
                                            default2 = '';
                                            default3 = '';
                                        }
                                        if(value.custom_frequency != undefined && value.custom_frequency !='') {
                                            var custom_array = value.custom_frequency.split('-');
                                            if (custom_array.length == 3) {
                                                default1 = custom_array[0];
                                                default2 = custom_array[1];
                                                default3 = custom_array[2];
                                            }
                                        }
                                        var freq_open = false;
                                        if (value.frequency_instruction) {
                                            freq_open = true;
                                        }
                                        var intake_open = false;
                                        if (value.intake_instruction) {
                                            intake_open = true;
                                        }
                                        var defaultFreq = false;
                                        if((value.drug_unit_name=='Tablets' || value.drug_unit_name=='IU') && value.dosage =='') {
                                            defaultFreq = true;
                                        }

                                        drug_obj.push({
                                            similar_brand_id: value.drug_id,
                                            similar_brand: value.drug_name,
                                            drug_drug_generic_id: value.generic_id.split(','),
                                            dosage: value.dosage,
                                            drug_frequency_id: value.frequency,
                                            default1: default1,
                                            default2: default2,
                                            default3: default3,
                                            freq_instruction: value.frequency_instruction,
                                            drug_intake: value.intake,
                                            intake_instruction: value.intake_instruction,
                                            drug_duration_value: value.duration_value,
                                            drug_instruction: value.diet_instruction,
                                            defaultFreqOpen: defaultFreq,
                                            isFreqInst: freq_open,
                                            isIntakeInst: intake_open,
                                            drug_unit_name: value.drug_unit_name,
                                            drug_duration: value.duration,
                                        });


                                    });
                            if (drug_obj.length != 0) {
                                $scope.brandList = drug_obj;
                            }
                            $("#modal_edit_template").modal("show");
                            $("#modal_edit_template").modal('handleUpdate');
                            $("#modal_edit_template .modal-backdrop.in").height($("#modal_edit_template .modal-backdrop.in").height() + 1000);
                        });
            }

            $scope.editTemplate = function (form) {
                $scope.submitted = true;
                if (!$scope.template_tab.addInvetigateTab || !$scope.template_tab.addRXTab || !$scope.template_tab.addClinicalNoteTab) {
                    $scope.template.tab_required = true;
                    return false;
                } else {
                    $scope.template.tab_required = false;
                }
                var request = {
                    device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                    template_id: request,
                    diagnosis_name: $scope.template.template_diagnoses,
                    template_name: $scope.template.template_name,
                };
                if (form.$valid) {
                    var final_notes_array = [];
                    angular
                            .forEach($scope.testChief, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testObservation, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testDiagnosis, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });
                    angular
                            .forEach($scope.testNotes, function (value, key) {
                                if (value.search) {
                                    final_notes_array.push(value);
                                }
                            });

                    request.clinical_notes = JSON.stringify(final_notes_array);

                    var brandData = [];
                    angular
                            .forEach($scope.brandList, function (value, key) {
                                if (value.similar_brand_id) {

                                    var intake_inst = '';
                                    if (value.intake_instruction) {
                                        intake_inst = value.intake_instruction;
                                    }
                                    var dosage = '';
                                    if (value.dosage) {
                                        dosage = value.dosage;
                                    }
                                    if((value.drug_unit_name=='Tablets' || value.drug_unit_name =='IU') && value.defaultFreqOpen) {
                                        dosage = '';
                                    } else if(value.drug_unit_name=='As Directed') {
                                        dosage = '';
                                    }
                                    var feq_instruction = '';
                                    if (value.freq_instruction) {
                                        feq_instruction = value.freq_instruction;
                                    }
                                    var drug_inst = '';
                                    if (value.drug_instruction) {
                                        drug_inst = value.drug_instruction;
                                    }
                                    var custom_value = '';
                                    if (value.default1 || value.default2 || value.default3) {
                                        if (!value.default1) {
                                            value.default1 = '0';
                                        }
                                        if (!value.default2) {
                                            value.default2 = '0';
                                        }
                                        if (!value.default3) {
                                            value.default3 = '0';
                                        }
                                        custom_value = value.default1 + '-' + value.default2 + '-' + value.default3;
                                    }
                                    var tempBrand = {
                                        "drug_id": value.similar_brand_id,
                                        "drug_intake_instruction": intake_inst,
                                        "drug_custom_frequency": custom_value,
                                        "drug_dosage": dosage,
                                        "drug_frequency_instruction": feq_instruction,
                                        "drug_duration_value": value.drug_duration_value,
                                        "drug_duration": value.drug_duration,
                                        "drug_intake": value.drug_intake,
                                        "drug_frequency": value.drug_frequency_id,
                                        "drug_diet_instruction": drug_inst,
                                    };
                                    brandData.push(tempBrand);
                                }
                            });
                    request.drug_data = JSON.stringify(brandData);
                    request.investigation_instruction = '';
                    var temp = {};
                    angular
                            .forEach($scope.testLabs, function (value, key) {
                                if (value.search && key != ($scope.testLabs.length - 1)) {

                                    if (!value.lab_instruction) {
                                        value.lab_instruction = '';
                                    }
                                    temp[value.search] = value.lab_instruction;

                                }
                            });
                    request.investigation_name = JSON.stringify(temp);
                    request.template_id = $scope.template.template_id;

                    if (final_notes_array.length == 0) {
                        $scope.template.is_blank_clinical_notes = true;
                        return;
                    } else {
                        $scope.template.is_blank_clinical_notes = false;
                    }

                    SettingService
                            .editTemplate(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $rootScope.app.isLoader = false;
                                    $scope.submitted = false;
                                    $("#modal_edit_template").modal("hide");
                                    $scope.testChief = [{
                                        }];
                                    $scope.testObservation = [{
                                        }];
                                    $scope.testDiagnosis = [{
                                        }];
                                    $scope.testNotes = [{
                                        }];
                                    $scope.testLabs = [{
                                        }];
                                    $scope.template = {
                                        lab_instruction: '',
                                        template_diagnoses: '',
                                        template_name: '',
                                    };
                                    $scope.brandList = [{
                                        }];
                                } else {
                                    ngToast.danger(response.message);
                                }

                                $scope.getTemplateList(1);
                            });

                }
            }

            $scope.closeRXTab = function (type) {
                if (type == 2) {
                    $scope.brandList = [{
                            brand_name: '',
                            isOpen: false,
                            default1: '',
                            default2: '',
                            default3: '',
                        },
                    ];
                } else if (type == 3) {
                    $scope.testLabs = [{
                        }];
                    $scope.template.lab_instruction = '';
                } else if (type == 1) {
                    $scope.testChief = [{
                        }];
                    $scope.testObservation = [{
                        }];
                    $scope.testDiagnosis = [{
                        }];
                    $scope.testNotes = [{
                        }];
                }
            }

            /* share moduel start */
            $scope.getDoctorShareSetting = function () {
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    setting_type: 1
                };

                SettingService
                        .getShareSetting(request, function (response) {
                            if (response.data) {
                                $scope.share_data = JSON.parse(response.data.setting_data);
                            }
                            if ($scope.share_data.length == 0) {
                                $scope.share_data = [{
                                        id: 1,
                                        name: 'Vital',
                                        status: 2,
                                    }, {
                                        id: 2,
                                        name: 'Clinical Notes',
                                        status: 2,
                                    }, {
                                        id: 3,
                                        name: 'Rx',
                                        status: 2,
                                    }, {
                                        id: 4,
                                        name: 'Order Investigations',
                                        status: 2,
                                    }, {
                                        id: 5,
                                        name: 'Completed Procedures',
                                        status: 2,
                                    }, {
                                        id: 6,
                                        name: 'Invoice',
                                        status: 2,
                                    }, {
                                        id: 7,
                                        name: 'Report',
                                        status: 2,
                                    }];
                            }
                        });


            }
            $scope.changeCurrentClinicForShare = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getDoctorShareSetting();
            }

            $scope.saveShare = function () {

                var request = {
                    data: JSON.stringify($scope.share_data),
                    setting_type: 1,
                    setting_data_type: 1,
                    clinic_id: $rootScope.current_clinic.clinic_id
                };

                SettingService
                        .saveShareSetting(request, function (response) {
                            ngToast.success({
                                content: response.message,
                                timeout: 5000
                            });
                        });
            }
            /* share moduel end */

            /* security module start */
            $scope.saveDataSecurity = function () {
                var request = {
                    data: JSON.stringify($scope.security_data),
                    rx_setting: JSON.stringify($scope.rx_setting),
                    setting_type: 2,
                    setting_data_type: 1,
                    clinic_id: ''
                };
                SettingService
                        .saveShareSetting(request, function (response) {
                            ngToast.success({
                                content: response.message,
                                timeout: 5000
                            });
                        });
            }
            $scope.rx_setting = [];
            $scope.getDoctorDataSecurity = function () {
                var request = {
                    setting_type: [2,6],
                    setting_data_type: 1,
                    clinic_id: ''
                };
                SettingService
                        .getShareSetting(request, function (response) {
                            if (response.data) {
                                var selectedObj = $filter('filter')(response.data, {'setting_type':"2"},true);
                                if(selectedObj != undefined && selectedObj.length > 0){
                                    $scope.security_data = JSON.parse(selectedObj[0].setting_data);
                                } else {
                                    $scope.security_data = [{
                                        id: 1,
                                        name: '2 Factor authentication',
                                        status: 2,
                                    }];
                                }
                                var selectedObj = $filter('filter')(response.data, {'setting_type':"6"},true);
                                if(selectedObj != undefined && selectedObj.length > 0){
                                    $scope.rx_setting = JSON.parse(selectedObj[0].setting_data);
                                } else {
                                    $scope.rx_setting = [{
                                        id: 1,
                                        name: 'Enable Rx Entry',
                                        status: "1",
                                    }];
                                    $scope.rx_setting.push({
                                        id: 2,
                                        name: 'Enable Upload Rx',
                                        status: "2",
                                    });
                                }
                            }
                        });
            }

            /*change clinic for alert module */
            $scope.changeCurrentClinicForDataSecurity = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getDoctorDataSecurity();
            }

            /* fav doctor listing */
            $scope.getFavListing = function () {
                var request = {
                    search: ''
                }
                SettingService
                        .getFavListingDB(request, function (response) {

                            if (response.status) {
                                $scope.fav_doctors = response.data;
                            }
                        });
            }
            $scope.changeCurrentClinicForFavDoctor = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getFavListing();
            }

            /* search doctor */
            $scope.search_doctors = [];
            $scope.getDoctorListing = function ($event) {
                if ($event.keyCode != 13) {
                    $scope.fav_doctor_search.id = '';

                    SettingService
                            .getDoctorListing($scope.fav_doctor_search.search, function (response) {
                                $scope.search_doctors = response.data;
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            /* add fav doctor code */
            $scope.addToFavDoctor = function (is_fav, user_id) {
                var id = '';
                if (user_id) {
                    id = user_id;
                } else {
                    id = $scope.fav_doctor_search.id;
                }

                if (id) {
                    var request = {
                        id: id,
                        is_fav: is_fav
                    }
                    SettingService
                            .addToFav(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $scope.getFavListing();
                                    $scope.fav_doctor_search.search = '';
                                    $scope.fav_doctor_search.id = '';
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            })
                } else {
                    ngToast.danger("Please select doctor from searchbox");
                }
            }

            $scope.changeCustomValue = function (key) {
                var freq = $scope.brandList[key].drug_frequency_id;
                $scope.brandList[key].default1 = '0';
                $scope.brandList[key].default2 = '0';
                $scope.brandList[key].default3 = '0';
                if (freq == 1) {
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '0';
                } else if (freq == 2) {
                    $scope.brandList[key].default1 = '0';
                    $scope.brandList[key].default2 = '1';
                    $scope.brandList[key].default3 = '0';
                } else if (freq == 3) {
                    $scope.brandList[key].default1 = '0';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 4) {
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 5) {
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '1';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 11) {
                    $scope.brandList[key].default1 = '0';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '1';
                } else {
                    if (freq == 7)
                        $scope.brandList[key].drug_duration = 2;
                    else if (freq == 8)
                        $scope.brandList[key].drug_duration = 3;
                    if(freq == 6 || freq == 7 || freq == 8 || freq == 9 || freq == 10) {
                        $scope.brandList[key].default1 = '';
                        $scope.brandList[key].default2 = '';
                        $scope.brandList[key].default3 = '';
                    }
                }
                if($scope.brandList[key].drug_frequency_value != undefined && $scope.brandList[key].drug_frequency_value !='') {
                    var custom_array = $scope.brandList[key].drug_frequency_value.split('-');
                    if (custom_array.length == 3) {
                        $scope.brandList[key].default1 = custom_array[0];
                        $scope.brandList[key].default2 = custom_array[1];
                        $scope.brandList[key].default3 = custom_array[2];
                    }
                }
            }

            /* security module end */

            $scope.validate_percentage = function (key, form, type) {

                var check_value = '';
                if (type == 1) {
                    check_value = $scope.taxObj[key].tax_value;
                }
                if (type == 2) {
                    check_value = $scope.tax.tax_value;
                }
                if (type == 3) {
                    check_value = $scope.modeObj[key].fee;
                }
                if (type == 4) {
                    check_value = $scope.current_mode.fee;
                }
                if (check_value > 100 && (type == 1 || type == 2)) {
                    form.taxvalue.$setValidity("pattern", false);
                }
                if (check_value > 100 && (type == 3 || type == 4)) {
                    form.fee.$setValidity("pattern", false);
                }
            };

            /* change hour format */
            $scope.changeHourFormat = function () {
                var request = {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: $rootScope.current_doctor.user_id,
                        hour_format: $rootScope.currentUser.hour_format,
                    };
                SettingService
                        .changeHourFormat(request, function (response) {
                            if (response.status) {
                                $scope.startPickerInit();
                            }
                        });
            }

            /* add new clinic */
            $scope.add_clinic_data = {};
            
            $scope.addClinicPopup = function () {
                $scope.submitted = false;
                $scope.add_clinic_data = {};
                $scope.add_clinic_data.clinic_service = [{
                    text: ''
                }];
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
                        if ($scope.add_clinic_data.clinic_start_time == "" ||
                                $scope.add_clinic_data.clinic_start_time == undefined
                                ) {
                            $scope.add_clinic_data.clinic_start_time = round_date;
                        }
                    }
                    if (key == 1) {
                        if ($scope.add_clinic_data.clinic_end_time == "" ||
                                $scope.add_clinic_data.clinic_end_time == undefined
                                ) {
                            $scope.add_clinic_data.clinic_end_time = round_date;
                        }
                    }
                }

                if (type == 2) {
                    $scope.clinicdatepicker2.length = 0;
                    $scope.clinicdatepicker2[key] = {'opened': true};
                    if (key == 2) {
                        if ($scope.add_clinic_data.clinic_start_time2 == "" ||
                                $scope.add_clinic_data.clinic_start_time2 == undefined
                                ) {
                            $scope.add_clinic_data.clinic_start_time2 = round_date;
                        }
                    }
                    if (key == 3) {
                        if ($scope.add_clinic_data.clinic_end_time2 == "" ||
                                $scope.add_clinic_data.clinic_end_time2 == undefined
                                ) {
                            $scope.add_clinic_data.clinic_end_time2 = round_date;
                        }
                    }

                }
            };
            $scope.checkSessionTiming = function () {

                if ($scope.add_clinic_data.clinic_start_time && $scope.add_clinic_data.clinic_end_time) {
                    if (angular.isDate($scope.add_clinic_data.clinic_start_time))
                        $scope.add_clinic_data.temp_clinic_start_time = $scope.addZero($scope.add_clinic_data.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_start_time.getMinutes());
                    else
                        $scope.add_clinic_data.temp_clinic_start_time = $scope.add_clinic_data.clinic_start_time;

                    if (angular.isDate($scope.add_clinic_data.clinic_end_time))
                        $scope.add_clinic_data.temp_clinic_end_time = $scope.addZero($scope.add_clinic_data.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_end_time.getMinutes());
                    else
                        $scope.add_clinic_data.temp_clinic_end_time = $scope.add_clinic_data.clinic_end_time;

                    // $scope.add_clinic_data.temp_clinic_start_time = $scope.addZero($scope.add_clinic_data.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_start_time.getMinutes());
                    // $scope.add_clinic_data.temp_clinic_end_time = $scope.addZero($scope.add_clinic_data.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_end_time.getMinutes());

                    if ($scope.compareTime($scope.add_clinic_data.temp_clinic_start_time, $scope.add_clinic_data.temp_clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                }
                if ($scope.add_clinic_data.clinic_start_time2 != '' && $scope.add_clinic_data.clinic_start_time2 != undefined && $scope.add_clinic_data.clinic_end_time2 != '' && $scope.add_clinic_data.clinic_end_time2 != undefined) {
                    if ($scope.compareTime($scope.add_clinic_data.clinic_start_time2, $scope.add_clinic_data.clinic_end_time2) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                    if ($scope.compareTime($scope.add_clinic_data.clinic_end_time, $scope.add_clinic_data.clinic_start_time2) == false) {
                        ngToast.danger("Please enter valid session timing");
                        return false;
                    }
                }
                if ($scope.add_clinic_data.clinic_start_time && $scope.add_clinic_data.clinic_end_time) {
                    var minutes = $scope.getDurationIntoMin($scope.add_clinic_data.clinic_start_time, $scope.add_clinic_data.clinic_end_time);
                    //now check another session timing
                    if ($scope.add_clinic_data.clinic_start_time2 != '' && $scope.add_clinic_data.clinic_start_time2 != undefined) {
                        minutes += $scope.getDurationIntoMin($scope.add_clinic_data.clinic_start_time2, $scope.add_clinic_data.clinic_end_time2);
                    }
                    if (minutes > 0 && $scope.add_clinic_data.clinic_duration) {
                        if (minutes < $scope.add_clinic_data.clinic_duration) {
                            ngToast.danger("Please select valid duration");
                            return false;
                        }
                    }
                }
                return true;

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
            $scope.isClientServiceRequired = function (clientServiceObj, key) {
                if (clientServiceObj.length == 1)
                    return true;

                if (!clientServiceObj[key].text)
                    return false;
                else
                    return true;

            }
            $scope.addNewClinic = function (addClinicForm) {
                $scope.submitted = true;
                if (addClinicForm.$valid) {
                    if (!$scope.checkSessionTiming()) {
                        return false;
                    }
                    $scope.add_clinic_data.is_from_usercontroller = true;
                    if ($scope.compareTime($scope.add_clinic_data.clinic_start_time, $scope.add_clinic_data.clinic_end_time) == false) {
                        ngToast.danger("Clinic start time should be smaller");
                        return false;
                    }
                    if ($scope.add_clinic_data.clinic_start_time2 != '' && $scope.add_clinic_data.clinic_start_time2 != undefined && $scope.add_clinic_data.clinic_end_time2 != '' && $scope.add_clinic_data.clinic_end_time2 != undefined) {
                        if ($scope.compareTime($scope.add_clinic_data.clinic_start_time2, $scope.add_clinic_data.clinic_end_time2) == false) {
                            ngToast.danger("Clinic start time should be smaller");
                            return false;
                        }
                        if ($scope.compareTime($scope.add_clinic_data.clinic_end_time, $scope.add_clinic_data.clinic_start_time2) == false) {
                            ngToast.danger("Please enter valid session timing");
                            return false;
                        }
                    }
                    $scope.submitted = false;
                    $scope.add_clinic_data.duration_mint = $scope.add_clinic_data.clinic_duration;
                    $rootScope.app.isLoader = true;

                    if (angular.isDate($scope.add_clinic_data.clinic_start_time))
                        $scope.add_clinic_data.clinic_start_time = $scope.addZero($scope.add_clinic_data.clinic_start_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_start_time.getMinutes());

                    if (angular.isDate($scope.add_clinic_data.clinic_start_time2))
                        $scope.add_clinic_data.clinic_start_time2 = $scope.addZero($scope.add_clinic_data.clinic_start_time2.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_start_time2.getMinutes());

                    if (angular.isDate($scope.add_clinic_data.clinic_end_time))
                        $scope.add_clinic_data.clinic_end_time = $scope.addZero($scope.add_clinic_data.clinic_end_time.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_end_time.getMinutes());

                    if (angular.isDate($scope.add_clinic_data.clinic_end_time2))
                        $scope.add_clinic_data.clinic_end_time2 = $scope.addZero($scope.add_clinic_data.clinic_end_time2.getHours()) + ":" + $scope.addZero($scope.add_clinic_data.clinic_end_time2.getMinutes());

                    
                    if($scope.add_clinic_data.clinic_email == undefined){
                        $scope.add_clinic_data.clinic_email = "";
                    }
                    
                    ClinicService
                            .addNewClinic($scope.add_clinic_data, function (response) {
                                if (response.status == true) {
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $("#add_clinic_modal").modal("hide");
                                    $rootScope.clinic_data = [];
                                    $scope.getClinics();
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
            /*END Add new clinic*/

            /*Rx Prints Settings*/
            $scope.change_print_setting_tab = function(tab) {
                $scope.print_setting_tab = tab;
            }
            $scope.changeCurrentClinicForPrintouts = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.changeClinicCalendarUpdate(clinic);
                $scope.getPrescriptionSetting();
            }
            $scope.getRxPrintDefaultSetting = function(templateType) {
                var defaultSetting = [{
                    template_id: "1",
                    appointment_type: "1",
                    left_space: 1,
                    right_space: 1,
                    header_space: 0.8,
                    footer_space: 0.8,
                    header_title: '',
                    header_left_text: '',
                    header_left_check: false,
                    header_right_check: false,
                    header_title_check: false,
                    footer_content_check: false,
                    left_signature_check: false,
                    right_signature_check: false,
                    header_right_text: '',
                    logo_position: 'left',
                    logo_width: 50, //50*3=150px default width
                    sign_position: 'right',
                    orientation: 'P',
                    page_type: 'A4',
                    font_family: 'Arial',
                    font_size_1: 14,
                    font_size_2: 12,
                    font_size_3: 10,
                    footer_left_signature: '',
                    footer_right_signature: '',
                    footer_content: '',
                    logo_img_path: '',
                    watermark_check: false,
                    watermark_img_path: '',
                    watermark_opacity: 20, // percentage
                },{
                    template_id: "2",
                    appointment_type: "5",
                    left_space: 1,
                    right_space: 1,
                    header_space: 0.8,
                    footer_space: 0.8,
                    header_title: '',
                    header_left_text: '',
                    header_left_check: false,
                    header_right_check: false,
                    header_title_check: false,
                    footer_content_check: false,
                    left_signature_check: false,
                    right_signature_check: false,
                    header_right_text: '',
                    logo_position: 'left',
                    logo_width: 50, //50*3=150px default width
                    sign_position: 'right',
                    orientation: 'P',
                    page_type: 'A4',
                    font_family: 'Arial',
                    font_size_1: 14,
                    font_size_2: 12,
                    font_size_3: 10,
                    footer_left_signature: '',
                    footer_right_signature: '',
                    footer_content: '',
                    logo_img_path: '',
                    watermark_check: false,
                    watermark_img_path: '',
                    watermark_opacity: 20, // percentage
                }];
                var selectedFilterObj = $filter('filter')(defaultSetting, {'appointment_type':templateType},true);
                if(selectedFilterObj != undefined && selectedFilterObj[0] != undefined) {
                    return selectedFilterObj[0];
                }
            }
            $scope.getPrescriptionSetting = function() {
                $scope.print_setting_tab = 1;
                $scope.rxPrint = {template_type: "1"};
                $scope.rx_settings = $scope.getRxPrintDefaultSetting($scope.rxPrint.template_type);
                $scope.getPrescriptionTemplate();
            }
            $scope.changeRxTemplate = function() {
                $scope.rx_settings = $scope.getRxPrintDefaultSetting($scope.rxPrint.template_type);
                $scope.getPrescriptionTemplate();
            }
            $scope.editorTextPreview = function () {
                $scope.rx_settings.header_title = $scope.ckEditorData.textInput;
                $scope.previewRxPrint();
                $("#ckEditorModal").modal("hide");
            }
            $scope.open_rx_ck_editor = function () {
                if(!$scope.rx_settings.header_title_check)
                    return false;
                var config = {}; 
                config.toolbarGroups = [
                    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                    { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                    { name: 'forms', groups: [ 'forms' ] },
                    '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                    { name: 'links', groups: [ 'links' ] },
                    { name: 'insert', groups: [ 'insert' ] },
                    '/',
                    { name: 'styles', groups: [ 'styles' ] },
                    { name: 'colors', groups: [ 'colors' ] },
                    { name: 'tools', groups: [ 'tools' ] },
                    { name: 'others', groups: [ 'others' ] },
                    { name: 'about', groups: [ 'about' ] }
                ];
                config.removeButtons = 'Styles,Format,Font,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Strike,Subscript,Superscript,CopyFormatting,RemoveFormat,Outdent,Indent,Blockquote,BidiLtr,BidiRtl,Language,Link,Unlink,Anchor,Image,Flash,Smiley,SpecialChar,PageBreak,Iframe,About,HorizontalRule,ShowBlocks';
                $scope.ckEditorData = {
                    load: true,
                    config: config,
                    textInput: $scope.rx_settings.header_title
                }
                $("#ckEditorModal").modal("show");
            }
            $scope.getPrescriptionTemplate = function() {
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    template_type: $scope.rxPrint.template_type
                };
                $scope.current_template = {};
                $scope.rx_img = {
                    imageSignSrc: '',
                    watermark_temp_img: '',
                    temp_img: ''
                };
                $scope.rx_print_preview_zoom = 100;
                $scope.fontFamilyArr = [];
                $scope.isRenderSlider = false;
                SettingService
                    .getPrescriptionTemplate(request, function (response) {
                        $scope.prescription_template = response.data;
                        $scope.doctorAlldetails = response.doctorAlldetails;
                        $scope.shareSettingData = JSON.parse(response.shareSettingData.setting_data);
                        if($scope.doctorAlldetails.setting_data != "" && $scope.doctorAlldetails.setting_data != null) {
                            
                            var settingDataArr = JSON.parse($scope.doctorAlldetails.setting_data);
                            if(settingDataArr.template_id != undefined && settingDataArr.template_id != '')
                                $scope.rx_settings.template_id = settingDataArr.template_id;
                            if(settingDataArr.left_space != undefined)
                                $scope.rx_settings.left_space = settingDataArr.left_space;
                            if(settingDataArr.right_space != undefined)
                                $scope.rx_settings.right_space = settingDataArr.right_space;
                            if(settingDataArr.header_space != undefined)
                                $scope.rx_settings.header_space = settingDataArr.header_space;
                            if(settingDataArr.footer_space != undefined)
                                $scope.rx_settings.footer_space = settingDataArr.footer_space;
                            if(settingDataArr.header_title != undefined)
                                $scope.rx_settings.header_title = settingDataArr.header_title;
                            if(settingDataArr.header_left_text != undefined)
                                $scope.rx_settings.header_left_text = settingDataArr.header_left_text;
                            if(settingDataArr.header_left_check != undefined)
                                $scope.rx_settings.header_left_check = settingDataArr.header_left_check;
                            if(settingDataArr.header_right_check != undefined)
                                $scope.rx_settings.header_right_check = settingDataArr.header_right_check;
                            if(settingDataArr.header_title_check != undefined)
                                $scope.rx_settings.header_title_check = settingDataArr.header_title_check;
                            if(settingDataArr.footer_content_check != undefined)
                                $scope.rx_settings.footer_content_check = settingDataArr.footer_content_check;
                            if(settingDataArr.left_signature_check != undefined)
                                $scope.rx_settings.left_signature_check = settingDataArr.left_signature_check;
                            if(settingDataArr.right_signature_check != undefined)
                                $scope.rx_settings.right_signature_check = settingDataArr.right_signature_check;
                            if(settingDataArr.header_right_text != undefined)
                                $scope.rx_settings.header_right_text = settingDataArr.header_right_text;
                            if(settingDataArr.logo_position != undefined)
                                $scope.rx_settings.logo_position = settingDataArr.logo_position;
                            if(settingDataArr.logo_width != undefined)
                                $scope.rx_settings.logo_width = settingDataArr.logo_width;
                            if(settingDataArr.sign_position != undefined)
                                $scope.rx_settings.sign_position = settingDataArr.sign_position;
                            if(settingDataArr.orientation != undefined)
                                $scope.rx_settings.orientation = settingDataArr.orientation;
                            if(settingDataArr.page_type != undefined)
                                $scope.rx_settings.page_type = settingDataArr.page_type;
                            if(settingDataArr.font_family != undefined)
                                $scope.rx_settings.font_family = settingDataArr.font_family;
                            if(settingDataArr.font_size_1 != undefined)
                                $scope.rx_settings.font_size_1 = settingDataArr.font_size_1;
                            if(settingDataArr.font_size_2 != undefined)
                                $scope.rx_settings.font_size_2 = settingDataArr.font_size_2;
                            if(settingDataArr.font_size_3 != undefined)
                                $scope.rx_settings.font_size_3 = settingDataArr.font_size_3;
                            if(settingDataArr.footer_left_signature != undefined)
                                $scope.rx_settings.footer_left_signature = settingDataArr.footer_left_signature;
                            if(settingDataArr.footer_right_signature != undefined)
                                $scope.rx_settings.footer_right_signature = settingDataArr.footer_right_signature;
                            if(settingDataArr.footer_content != undefined)
                                $scope.rx_settings.footer_content = settingDataArr.footer_content;
                            if(settingDataArr.logo_img_path != undefined)
                                $scope.rx_settings.logo_img_path = settingDataArr.logo_img_path;
                            if(settingDataArr.watermark_check != undefined)
                                $scope.rx_settings.watermark_check = settingDataArr.watermark_check;
                            if(settingDataArr.watermark_img_path != undefined)
                                $scope.rx_settings.watermark_img_path = settingDataArr.watermark_img_path;
                            if(settingDataArr.watermark_opacity != undefined)
                                $scope.rx_settings.watermark_opacity = settingDataArr.watermark_opacity;
                            if(settingDataArr.hide_header_footer != undefined && settingDataArr.hide_header_footer) {
                                $scope.rx_settings.header_left_check = true;
                                $scope.rx_settings.header_right_check = true;
                                $scope.rx_settings.footer_content_check = true;
                            }
                        }
                        $scope.isRenderSlider = true;
                        $scope.previewRxPrint();
                });
            }
            $scope.resetRxPrintSetting = function() {
                SweetAlert.swal({
                    title: "Are you sure want to reset this settings?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $scope.rx_settings = $scope.getRxPrintDefaultSetting($scope.rxPrint.template_type);
                        $timeout(function () {
                            document.getElementById("saveSetting").click();
                        }, 100);
                    }
                });
            }
            $scope.saveRxPrintSetup = function(form) {
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    data: $scope.rx_settings,
                    signature_img: $scope.rx_img.imageSignSrc,
                    share_setting_data: $scope.shareSettingData,
                    logo_img: ($scope.rx_img.logo_img != undefined) ? $scope.rx_img.logo_img : '',
                    upload_signature_img: ($scope.rx_img.signature_img != undefined) ? $scope.rx_img.signature_img : '',
                    watermark_img: ($scope.rx_img.watermark_img != undefined) ? $scope.rx_img.watermark_img : ''
                };
                if (form.$valid) {
                    SettingService
                        .saveRxPrintSetup(request, function (response) {
                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $scope.getPrescriptionTemplate();
                            } else {
                                ngToast.danger(response.message);
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        })
                } else {
                    console.log("Error");
                }
            }
            $scope.rxPreviewZoom = function(sign) {
                if(sign == '+')
                    $scope.rx_print_preview_zoom = $scope.rx_print_preview_zoom + 10;
                else
                    $scope.rx_print_preview_zoom = $scope.rx_print_preview_zoom - 10;
            }
            $scope.headerLeftText = function() {
                if($scope.rx_settings.header_left_check && ($scope.rx_settings.header_left_text == undefined || $scope.rx_settings.header_left_text == '')) {
                    var headerLeftText = $scope.doctorAlldetails.doctor_name+"\n";
                    headerLeftText += 'Reg. No. ' + $scope.doctorAlldetails.doctor_regno+"\n";
                    headerLeftText += $scope.doctorAlldetails.doctor_detail_speciality+"\n";
                    headerLeftText += $scope.doctorAlldetails.doctor_qualification_degree;
                    $scope.rx_settings.header_left_text = headerLeftText;
                    $scope.previewRxPrint();
                }
            }
            $scope.headerRightText = function() {
                if($scope.rx_settings.header_right_check && ($scope.rx_settings.header_right_text == undefined || $scope.rx_settings.header_right_text == '')) {
                    var headerRightText = $scope.doctorAlldetails.clinic_name+"\n";
                    headerRightText += $scope.doctorAlldetails.clinic_address+"\n";
                    headerRightText += $scope.doctorAlldetails.clinic_contact_number+"\n";
                    if($scope.doctorAlldetails.clinic_email != "")
                        headerRightText += $scope.doctorAlldetails.clinic_email;
                    $scope.rx_settings.header_right_text = headerRightText;
                    $scope.previewRxPrint();
                }
            }
            $scope.previewRxPrint = function() {
                var selectedFilterObj = $filter('filter')($scope.prescription_template, {'template_id':$scope.rx_settings.template_id},true);
                if(selectedFilterObj != undefined && selectedFilterObj[0] != undefined) {
                    $scope.current_template = angular.copy(selectedFilterObj[0]);
                    var templateMeta = JSON.parse($scope.current_template.template_meta);
                    $scope.fontFamilyArr = templateMeta.font_family;
                } else {
                    return;
                }
                /*Header title update*/
                if($scope.rx_settings.header_title != undefined && $scope.rx_settings.header_title != '' && $scope.rx_settings.header_title_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_title}', $scope.rx_settings.header_title);
                } else if($scope.rx_settings.header_title_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_title}', '');
                } else {
                    $scope.rx_settings.header_title = '';
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_title}', '');
                }
                /*END Header title update*/
                /*Left text update*/
                if($scope.rx_settings.header_left_text != undefined && $scope.rx_settings.header_left_text != '' && $scope.rx_settings.header_left_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_left}', $scope.rx_settings.header_left_text);
                } else if($scope.rx_settings.header_left_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_left}', '');
                } else {
                    $scope.rx_settings.header_left_text = '';
                    var headerLeftText = $scope.doctorAlldetails.doctor_name+"\n";
                    headerLeftText += 'Reg. No. ' + $scope.doctorAlldetails.doctor_regno+"\n";
                    headerLeftText += $scope.doctorAlldetails.doctor_detail_speciality+"\n";
                    headerLeftText += $scope.doctorAlldetails.doctor_qualification_degree;
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_left}', headerLeftText);
                }
                /*END Left text update*/
                /*Right text update*/
                if($scope.rx_settings.header_right_text != undefined && $scope.rx_settings.header_right_text != '' && $scope.rx_settings.header_right_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_right}', $scope.rx_settings.header_right_text);
                } else if($scope.rx_settings.header_right_check) {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_right}', '');
                } else {
                    $scope.rx_settings.header_right_text = '';
                    var headerRightText = $scope.doctorAlldetails.clinic_name+"\n";
                    headerRightText += $scope.doctorAlldetails.clinic_address+"\n";
                    headerRightText += $scope.doctorAlldetails.clinic_contact_number+"";
                    if($scope.doctorAlldetails.clinic_email != "")
                        headerRightText += "," + $scope.doctorAlldetails.clinic_email;
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{header_right}', headerRightText);
                }
                /*END Right text update*/
                /*if($scope.rx_settings.teleconsultation_check) {
                    if($scope.rx_settings.teleconsultation_text == '')
                        $scope.rx_settings.teleconsultation_text = 'The prescription is given on telephonic consultation.';
                } else {
                    $scope.rx_settings.teleconsultation_text = '';
                }*/
                /*Footer content update*/
                if($scope.rx_settings.footer_content != undefined && $scope.rx_settings.footer_content != '' && $scope.rx_settings.footer_content_check) {
                    $scope.current_template.template_footer = $scope.current_template.template_footer.replace('{footer_content}', $scope.rx_settings.footer_content);
                } else if($scope.rx_settings.footer_content_check) {
                    $scope.current_template.template_footer = $scope.current_template.template_footer.replace('{footer_content}', '');
                } else {
                    $scope.rx_settings.footer_content = '';
                    $scope.current_template.template_footer = $scope.current_template.template_footer.replace('{footer_content}', '');
                }
                /*END Footer content update*/
                /*Footer signature content update*/
                if(!$scope.rx_settings.left_signature_check){
                    $scope.rx_settings.footer_left_signature = '';
                }
                if(!$scope.rx_settings.right_signature_check){
                    $scope.rx_settings.footer_right_signature = '';
                }
                if($scope.rx_img.imageSignSrc != undefined && $scope.rx_img.imageSignSrc != ''){
                    $scope.signatureImgPreview = $scope.rx_img.imageSignSrc;
                } else if($scope.doctorAlldetails.user_sign_thumb_filepath != '') {
                    $scope.signatureImgPreview = $scope.doctorAlldetails.user_sign_thumb_filepath;
                } else {
                    $scope.signatureImgPreview = '';
                }
                /*END Footer signature content update*/
                if($scope.rx_img.watermark_temp_img != undefined && $scope.rx_img.watermark_temp_img != ''){
                    $scope.watermarkImgPreview = $scope.rx_img.watermark_temp_img;
                } else if($scope.doctorAlldetails.watermark_img_thumb_path != '') {
                    $scope.watermarkImgPreview = $scope.doctorAlldetails.watermark_img_thumb_path;
                } else {
                    $scope.watermarkImgPreview = '';
                }
                
                if($scope.rx_img.temp_img != undefined && $scope.rx_img.temp_img != "") {
                    var logoImg = $scope.rx_img.temp_img;
                } else if($scope.doctorAlldetails.logo_img_thumb_path != '') {
                    var logoImg = $scope.doctorAlldetails.logo_img_thumb_path;
                } else {
                    var logoImg = '';
                }
                if(logoImg != undefined && logoImg != "") {
                    if($scope.rx_settings.logo_position=='left'){
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{left_logo}', '<img src="'+logoImg+'" style="width: '+$scope.rx_settings.logo_width*3+'px;">');
                    } else {
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{left_logo}', '');
                    }
                    if($scope.rx_settings.logo_position=='right'){
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{right_logo}', '<img src="'+logoImg+'" style="width: '+$scope.rx_settings.logo_width*3+'px;">');
                    } else {
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{right_logo}', '');
                    }
                    if($scope.rx_settings.logo_position=='center'){
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{center_logo}', '<img src="'+logoImg+'" style="width: '+$scope.rx_settings.logo_width*3+'px;">');
                    } else {
                        $scope.current_template.template_header = $scope.current_template.template_header.replace('{center_logo}', '');
                    }
                } else {
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{left_logo}', '');
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{center_logo}', '');
                    $scope.current_template.template_header = $scope.current_template.template_header.replace('{right_logo}', '');
                }
                if($scope.rx_settings.font_size_2 == undefined || $scope.rx_settings.font_size_2 == ""){
                    $scope.rx_settings.font_size_2 = 0;
                }
                if($scope.rx_settings.font_size_3 == undefined || $scope.rx_settings.font_size_3 == ""){
                    $scope.rx_settings.font_size_3 = 0;
                }
                /*Update font size 2*/
                var style_update = 'font-size:'+$scope.rx_settings.font_size_2+'px;font-family:'+$scope.rx_settings.font_family+';';
                $scope.current_template.template_header = $scope.current_template.template_header.replaceAll('{font_size_2}', style_update);

                /*Update font size 2*/
                var style3_update = 'font-size:'+$scope.rx_settings.font_size_3+'px;font-family:'+$scope.rx_settings.font_family+';';
                $scope.current_template.template_footer = $scope.current_template.template_footer.replaceAll('{font_size_3}', style3_update);

                if($scope.rx_settings.left_space != undefined && $scope.rx_settings.left_space > 0){
                    $scope.left_padding = 37.795*$scope.rx_settings.left_space;
                } else {
                    $scope.left_padding = 0;
                }
                if($scope.rx_settings.right_space != undefined && $scope.rx_settings.right_space > 0){
                    $scope.right_padding = 37.795*$scope.rx_settings.right_space;
                } else {
                    $scope.right_padding = 0;
                }
                if($scope.rx_settings.header_space != undefined && $scope.rx_settings.header_space > 0){
                    $scope.header_padding = 37.795*$scope.rx_settings.header_space;
                } else {
                    $scope.header_padding = 0;
                }
                if($scope.rx_settings.footer_space != undefined && $scope.rx_settings.footer_space > 0){
                    $scope.footer_padding = 37.795*$scope.rx_settings.footer_space;
                } else {
                    $scope.footer_padding = 0;
                }
            }
            $scope.logoSizePlus = function() {
                if($scope.rx_settings.logo_width < 100) {
                    $scope.rx_settings.logo_width++;
                    $scope.previewRxPrint();
                }
            }
            $scope.logoSizeMinus = function() {
                if($scope.rx_settings.logo_width > 0) {
                    $scope.rx_settings.logo_width--;
                    $scope.previewRxPrint();
                }
            }
            $scope.removeRxLogoImg = function() {
                $scope.rx_img.temp_img = '';
                $scope.rx_img.logo_img = '';
                $scope.previewRxPrint();
            }
            $scope.removeRxWatermarkImg = function() {
                $scope.rx_img.watermark_temp_img = '';
                $scope.rx_img.watermark_img = '';
                $scope.previewRxPrint();
            }
            $scope.removeRxSign = function() {
                $scope.rx_img.imageSignSrc = '';
                $scope.rx_img.signature_img = '';
                $scope.previewRxPrint();
            }
            $scope.highlightOverFontSize1 = function () {
                $scope.is_highlight1 = true;
            }
            $scope.highlightOutFontSize1 = function () {
                $scope.is_highlight1 = false;
            }
            $scope.highlightOverFontSize2 = function () {
                $scope.is_highlight2 = true;
            }
            $scope.highlightOutFontSize2 = function () {
                $scope.is_highlight2 = false;
            }
            $scope.highlightOverFontSize3 = function () {
                $scope.is_highlight3 = true;
            }
            $scope.highlightOutFontSize3 = function () {
                $scope.is_highlight3 = false;
            }
            $scope.rxPreviewToolTip = function (event) {
                $(event.target).tooltip({
                    html: true,
                    placement: "right",
                }).tooltip("open");
            };
            $scope.signaturePopup = function () {
                $scope.rx_img.imageSignSrc = '';
                $scope.rx_img.signature_img = '';
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
            /*END Rx Prints Settings*/
        });

/* Preview img code */
angular.module("app.dashboard.setting")
        .directive("ngFileSelectStaff", ['ngToast', function (ngToast) {
                return {
                    link: function ($scope, el, attr) {
                        el.bind("change", function (e) {
                            var file_obj = (e.srcElement || e.target).files[0];
                            var file_type = file_obj.name;

                            if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {

                                if (attr.obj == "receptionist") {
                                    $scope.reception.file = file_obj;
                                } else if (attr.obj == "edu") {
                                    $scope.doctor.edu_object[attr.key].img_file = file_obj;
                                    $scope.doctor.edu_object[attr.key].img_file_name = file_obj.name;
                                } else if (attr.obj == "reg") {
                                    $scope.doctor.registration_obj[attr.key].img_file = file_obj;
                                    $scope.doctor.registration_obj[attr.key].img_file_name = file_obj.name;
                                }
                                $scope.file = file_obj;

                                $scope.getFile(attr.obj, $scope.file, attr.key);
                            }
                        })
                    }
                }

            }]);
angular.module("app.dashboard.setting")
    .directive("sliderHorizontal", ['ngToast', function (ngToast) {
        return {
            restrict: 'A',
            link: function ($scope, element, attr) {
                var defaultVal = attr.setdata;
                element.slider({
                    orientation: "Horizontal",
                    range: "min",
                    min: 0,
                    max: 100,
                    value: defaultVal,
                    slide: function(event, ui) {
                        $scope.rx_settings.watermark_opacity = ui.value;
                        $("#opacity_txt").html(ui.value);
                        var g = parseInt(ui.value <= 50 ? 255 : 255 - ((ui.value-50)*(255/50)));
                        var r = parseInt(ui.value >= 50 ? 255 : 255 - ((50-ui.value)*(255/50)));
                        $(".ui-widget-header").css("background-color", "rgb(255,165,0)");  
                    }
                });
                $("#opacity_txt").html(element.slider("value"));
            }
        }
    }]);
angular.module("app.dashboard.setting")
    .directive("ngSelectFile", ['ngToast', function (ngToast) {
        return {
            link: function ($scope, el, attr) {
                el.bind("change", function (e) {
                    var file_obj = (e.srcElement || e.target).files[0];
                    var file_type = file_obj.name;
                    if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {
                        if (attr.obj == "rx_print_logo") {
                            $scope.rx_img.logo_img = file_obj;
                        } else if (attr.obj == "rx_watermark_img") {
                            $scope.rx_img.watermark_img = file_obj;
                        } else if (attr.obj == "rx_print_signature") {
                            $scope.rx_img.signature_img = file_obj;
                        }
                        $scope.file = file_obj;
                        $scope.getFile(attr.obj, $scope.file, attr.key);
                    }
                })
            }
        }
    }]);
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
angular.module("app.dashboard.setting")
        .directive("ngFileSelectClinic", ['ngToast', function (ngToast) {
                return {
                    link: function ($scope, el, attr) {
                        el.bind("change", function (e) {
                            var file_obj = (e.srcElement || e.target).files[0];
                            var file_type = file_obj.name;

                            if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {

                                if (attr.obj == "clinic_image") {
                                    $scope.add_clinic_data.clinic_file = file_obj;
                                } else if (attr.obj == "clinic_outside") {
                                    $scope.add_clinic_data.clinic_outside_file = file_obj;
                                } else if (attr.obj == "clinic_waiting") {
                                    $scope.add_clinic_data.clinic_waiting_file = file_obj;
                                } else if (attr.obj == "clinic_reception") {
                                    $scope.add_clinic_data.clinic_reception_file = file_obj;
                                } else if (attr.obj == "clinic_address_proof") {
                                    $scope.add_clinic_data.clinic_address_proof_file = file_obj;
                                }
                                $scope.file = file_obj;

                                $scope.getFile(attr.obj, $scope.file, attr.key);
                            }
                        })
                    }
                }

            }]);