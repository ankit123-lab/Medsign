/*
 * Controller Name: ReportController
 * Use: This controller is used for Reports activity
 */
angular.module("app.dashboard.reports")
        .controller("ReportController", function ($scope, AuthService, ReportService, ALCOHOL, SMOKE, $timeout, $state, CommonService, $rootScope, $localStorage, ngToast, $location, uiCalendarConfig, $filter, PatientService, minutesToHourFilter, SweetAlert, fileReader, $interval, EncryptDecrypt) {
	        
	        if($rootScope.currentUser.sub_plan_setting.reports == undefined || $rootScope.currentUser.sub_plan_setting.reports == '0' || !$rootScope.currentUser.is_sub_active) {
                $state.go('app.subscription');
            }
            if (!$rootScope.languages || $rootScope.languages.length <= 0) {
                CommonService.getLanguages('', function (response) {
                    if (response.status == true) {
                        $rootScope.languages = response.data;
                    }
                });
            }
	        $scope.global_per_page = 10;
	        $scope.per_page = $scope.global_per_page;
	        $scope.popup_per_page = $scope.global_per_page;
        	$scope.init = function () {
				$scope.report_filters = {
					search_str : '',
					from_date : '',
					real_from_date : '',
					to_date : '',
					real_to_date : '',
					language_id : [],
					city_id : [],
					search_clinic_id : [],
					drug_generic_id : [],
					drug_id : [],
					search_sku : '',
					search_brands : '',
					search_indication : '',
					search_kco : '',
					report_from : "1",
					disease_id : [],
					clinical_notes_catalog_id : [],
					search_disease_id : [],
					search_analytics_id : [],
					is_show_table : false,
					is_no_data : true,
					per_page : $scope.per_page,
					popup_per_page : $scope.popup_per_page
				}
				if($location.path() == '/app/dashboard/reports/cancel_appointment') {
					$scope.from_date = {
		                datepickerOptions: {
		                    minDate: null,
		                    maxDate: null,
		                },
		                open: false
		            };

		            $scope.to_date = {
		                datepickerOptions: {
		                    minDate: null,
		                    maxDate: null,
		                },
		                open: false
		            };
				} else {
					$scope.from_date = {
		                datepickerOptions: {
		                    minDate: null,
		                    maxDate: new Date(),
		                },
		                open: false
		            };

		            $scope.to_date = {
		                datepickerOptions: {
		                    minDate: null,
		                    maxDate: new Date(),
		                },
		                open: false
		            };
	        	}
			}
			$scope.init();
			$scope.memberSummaryFilterReset = function () {
				$scope.init();
				$scope.getMemberSummary(1);
			}
			$scope.mobSummaryFilterReset = function () {
				$scope.init();
				$scope.getMobSummary(1);
			}
			$scope.lostPatientFilterReset = function () {
				$scope.init();
				var from_date = new Date();
	            var year = from_date.getFullYear();
	            var month = from_date.getMonth();
	            var day = from_date.getDate();
	            $scope.report_filters.from_date = new Date(year, month - 6, day);
	            $scope.report_filters.to_date = new Date();
				$scope.getLostPatient(1);
			}
			$scope.patientProgressFilterReset = function () {
				$scope.init();
				var from_date = new Date();
	            var year = from_date.getFullYear();
	            var month = from_date.getMonth();
	            var day = from_date.getDate();
	            $scope.report_filters.from_date = new Date(year, month - 6, day);
	            $scope.report_filters.to_date = new Date();
	            $scope.patients_progress = [];
	            $scope.report_filters.is_show_table = false;
			}
			$scope.invoiceSummaryFilterReset = function () {
				$scope.init();
				$scope.getInvoiceSummary(1);
			}
			$scope.cancelAppointmentFilterReset = function () {
				$scope.init();
				$scope.getCancelAppointment(1);
			}
			$scope.currentPage = 0;
			$scope.total_page = 0;
			$scope.total_rows = 0;
			$scope.last_rows = 0;
			$scope.filterRows = [
                {value: 10}, 
                {value: 25}, 
                {value: 50}, 
                {value: 100}
            ];
            $scope.currentPagePopup = 0;
			$scope.popup_total_page = 0;
			$scope.popup_total_rows = 0;
			$scope.popup_last_rows = 0;
			$scope.popupFilterRows = [
                {value: 10}, 
                {value: 25}, 
                {value: 50}, 
                {value: 100}
            ];
			$scope.getMemberSummary = function (number,is_clear) {
				if(is_clear != undefined && is_clear == 1) {
					$scope.init();
					$scope.report_filters.per_page = $scope.global_per_page;
				}
				$scope.per_page = $scope.report_filters.per_page;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}

                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_str: $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					language_id: $scope.report_filters.language_id,
					city_id: $scope.report_filters.city_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getMemberSummary(request, function (response) {
					if (response.status == true) {
                        $scope.member_summary = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.memberSummaryExport = function () {
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				var params_arr = {
					search_str : $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					language_id: $scope.report_filters.language_id,
					city_id: $scope.report_filters.city_id,
					doctor_id : (($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id),
					search_clinic_id: $scope.report_filters.search_clinic_id,
					access_token: $rootScope.currentUser.access_token,
					user_id: $rootScope.currentUser.user_id
				}
				var city_name = "";
				if($scope.report_filters.city_id.length > 0) {
					angular.forEach($scope.report_filters.city_id, function (value) {
                      var selectedCityObj = $filter('filter')($scope.patients_city, {'city_id':value},true);
                      if(selectedCityObj != undefined && selectedCityObj[0].city_name != undefined && selectedCityObj[0].city_name != '')
                      	 city_name += selectedCityObj[0].city_name + ', ';
                    });
				}
				params_arr.city_name = city_name;
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/member_summary/" + request_data;
			}

			$scope.getMobSummary = function (number, is_clear) {
				if(is_clear != undefined && is_clear == 1) {
					$scope.init();
					$scope.report_filters.per_page = $scope.global_per_page;
				}
				$scope.per_page = $scope.report_filters.per_page;
                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					drug_generic_id: $scope.report_filters.drug_generic_id,
					search_sku: $scope.report_filters.search_sku,
					search_brands: $scope.report_filters.search_brands,
					search_indication: $scope.report_filters.search_indication,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getMobSummary(request, function (response) {
					if (response.status == true) {
                        $scope.mob_summary = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.mobSummaryExport = function () {
				var params_arr = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					drug_generic_id: $scope.report_filters.drug_generic_id,
					search_sku: $scope.report_filters.search_sku,
					search_brands: $scope.report_filters.search_brands,
					search_indication: $scope.report_filters.search_indication,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				var drug_generic_name = "";
				if($scope.report_filters.drug_generic_id.length > 0) {
					angular.forEach($scope.report_filters.drug_generic_id, function (value) {
                      var selectedObj = $filter('filter')($scope.drug_generic, {'drug_generic_id':value},true);
                      if(selectedObj != undefined && selectedObj[0].drug_generic_title != undefined && selectedObj[0].drug_generic_title != '')
                      	 drug_generic_name += selectedObj[0].drug_generic_title + ', ';
                    });
				}
				params_arr.drug_generic_name = drug_generic_name;
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/mob_summary/" + request_data;
			}

			$scope.getLostPatient = function (number, is_clear) {
				if(is_clear != undefined && is_clear == 1) {
					$scope.init();
					$scope.report_filters.per_page = $scope.global_per_page;
					var from_date = new Date();
		            var year = from_date.getFullYear();
		            var month = from_date.getMonth();
		            var day = from_date.getDate();
		            $scope.report_filters.from_date = new Date(year, month - 6, day);
		            $scope.report_filters.to_date = new Date();
				}
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if($scope.report_filters.real_from_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if($scope.report_filters.real_to_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				$scope.per_page = $scope.report_filters.per_page;
                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					search_kco: $scope.report_filters.search_kco,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				$rootScope.app.isLoader = true;
				ReportService.getLostPatient(request, function (response) {
					if (response.status == true) {
                        $scope.lost_patient = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}
			$scope.getPatientDetails = function (disease_id, count, row_no) {
				$scope.patient_detail = {
					disease_id: disease_id,
					count : count,
					row_no : row_no
				}
				$scope.report_filters.popup_per_page = $scope.global_per_page;
				$scope.getLostPatientDetails(1);
			}
			$scope.getLostPatientDetails = function (number) {
				if($scope.patient_detail.count == 0)
					return false;
				$scope.currentPagePopup = number;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if($scope.report_filters.real_from_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if($scope.report_filters.real_to_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					disease_id: $scope.patient_detail.disease_id,
					row_no: $scope.patient_detail.row_no,
					page: $scope.currentPagePopup,
                    per_page: $scope.report_filters.popup_per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getLostPatientDetails(request, function (response) {
					if (response.status == true) {
                        $scope.lost_patient_details = response.data;
                        angular.forEach($scope.lost_patient_details, function (value, key) {
                        	$scope.lost_patient_details[key].alcohol = '';
                        	if (value.user_details_alcohol != '' && value.user_details_alcohol != '0' && value.user_details_alcohol != null) {
	                            $scope.lost_patient_details[key].alcohol = ALCOHOL[value.user_details_alcohol - 1].name;
	                        }
	                        $scope.lost_patient_details[key].surgeries = '';
	                        if (value.user_details_surgeries != '' && value.user_details_surgeries != null) {
	                            $scope.lost_patient_details[key].surgeries = EncryptDecrypt.my_decrypt(value.user_details_surgeries);
	                        }
	                        $scope.lost_patient_details[key].food_allergies = '';
	                        if (value.user_details_food_allergies != '' && value.user_details_food_allergies != null) {
	                            $scope.lost_patient_details[key].food_allergies = EncryptDecrypt.my_decrypt(value.user_details_food_allergies);
	                        }
	                        $scope.lost_patient_details[key].medicine_allergies = '';
	                        if (value.user_details_medicine_allergies != '' && value.user_details_medicine_allergies != null) {
	                            $scope.lost_patient_details[key].medicine_allergies = EncryptDecrypt.my_decrypt(value.user_details_medicine_allergies);
	                        }
	                        $scope.lost_patient_details[key].other_allergies = '';
	                        if (value.user_details_other_allergies != '' && value.user_details_other_allergies != null) {
	                            $scope.lost_patient_details[key].other_allergies = EncryptDecrypt.my_decrypt(value.user_details_other_allergies);
	                        }
	                        $scope.lost_patient_details[key].smoking_habbit = '';
	                        if (value.user_details_smoking_habbit != '' && value.user_details_smoking_habbit != 0 && value.user_details_smoking_habbit != null) {
	                            $scope.lost_patient_details[key].smoking_habbit = SMOKE[value.user_details_smoking_habbit - 1].name;
	                        }
                        });
                        $scope.popup_total_rows = response.count;
                        $scope.popup_total_page = Math.ceil(response.count/$scope.report_filters.popup_per_page);
                        $scope.popup_last_rows = $scope.currentPagePopup*$scope.report_filters.popup_per_page;
                        if($scope.popup_last_rows > $scope.popup_total_rows)
                        	$scope.popup_last_rows = $scope.popup_total_rows;
                        $("#modal_patient_details").modal("show");
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.lostPatientExport = function () {
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				var params_arr = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					search_kco: $scope.report_filters.search_kco,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				if($scope.report_filters.real_from_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if($scope.report_filters.real_to_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/lost_patient/" + request_data;
			}

			$scope.patients_progress = [];
			$scope.getPatientProgress = function (number) {
				var is_error = false;
				angular.forEach($scope.report_filters.search_disease_id, function (value, key) {
					if($scope.report_filters.search_analytics_id[key] != undefined && $scope.report_filters.search_analytics_id[key] != '') {
						$('.error_'+value).addClass('hide');
					} else {
						$('.error_'+value).removeClass('hide');
						is_error = true;
					}
				});
				if(is_error)
					return false;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if($scope.report_filters.real_from_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if($scope.report_filters.real_to_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				$scope.per_page = $scope.report_filters.per_page;
                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					report_from: $scope.report_filters.report_from,
					search_disease_id: $scope.report_filters.search_disease_id,
					search_analytics_id: $scope.report_filters.search_analytics_id,
					drug_id: $scope.report_filters.drug_id,
					drug_generic_id: $scope.report_filters.drug_generic_id,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getPatientProgress(request, function (response) {
					if (response.status == true) {
						if(response.data.length > 0)
							$scope.report_filters.is_no_data = false;
						else
							$scope.report_filters.is_no_data = true;

                        $scope.patients_progress = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.patientProgressExport = function () {
				var is_error = false;
				angular.forEach($scope.report_filters.search_disease_id, function (value, key) {
					if($scope.report_filters.search_analytics_id[key] != undefined && $scope.report_filters.search_analytics_id[key] != '') {
						$('.error_'+value).addClass('hide');
					} else {
						$('.error_'+value).removeClass('hide');
						is_error = true;
					}
				});
				if(is_error)
					return false;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if($scope.report_filters.real_from_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if($scope.report_filters.real_to_date == '') {
					ngToast.danger($rootScope.date_required);
					return false;
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var params_arr = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					report_from: $scope.report_filters.report_from,
					search_disease_id: $scope.report_filters.search_disease_id,
					search_analytics_id: $scope.report_filters.search_analytics_id,
					drug_id: $scope.report_filters.drug_id,
					drug_generic_id: $scope.report_filters.drug_generic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				var drug_generic_name = "";
				if($scope.report_filters.drug_generic_id.length > 0) {
					angular.forEach($scope.report_filters.drug_generic_id, function (value) {
                      var selectedObj = $filter('filter')($scope.drug_generic, {'drug_generic_id':value},true);
                      if(selectedObj != undefined && selectedObj[0].drug_generic_title != undefined && selectedObj[0].drug_generic_title != '')
                      	 drug_generic_name += selectedObj[0].drug_generic_title + ', ';
                    });
				}
				params_arr.drug_generic_name = drug_generic_name;
				var drug_name = "";
				if($scope.report_filters.drug_id.length > 0) {
					angular.forEach($scope.report_filters.drug_id, function (value) {
                      var selectedObj = $filter('filter')($scope.drug_data, {'drug_id':value},true);
                      if(selectedObj != undefined && selectedObj[0].drug_name != undefined && selectedObj[0].drug_name != '')
                      	 drug_name += selectedObj[0].drug_name + ', ';
                    });
				}
				params_arr.drug_name = drug_name;
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/patient_progress/" + request_data;
			}

			$scope.changeReport = function () {
				$scope.report_filters.disease_id = [];
				$scope.report_filters.clinical_notes_catalog_id = [];
				$scope.report_filters.search_analytics_id = [];
				$scope.report_filters.is_show_table = false;
				$scope.disease_list = [];
				$scope.patients_progress = [];
			}
			$scope.getNextPrev = function (val,report_no) {
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
				if(report_no == 1) {
					$scope.getMemberSummary(number);
				} else if(report_no == 2) {
					$scope.getMobSummary(number);
				} else if(report_no == 3) {
					$scope.getLostPatient(number);
				} else if(report_no == 4) {
					$scope.getPatientProgress(number);
				} else if(report_no == 5) {
					$scope.getInvoiceSummary(number);
				}
			}
			$scope.getNextPrevPopup = function (val,num) {
				if(val == 'next') {
					if($scope.currentPagePopup >= $scope.popup_total_page)
						return false;
					var number = $scope.currentPagePopup+1;
				}
				if(val == 'prev') {
					if($scope.currentPagePopup <= 1)
						return false;
					var number = $scope.currentPagePopup-1;
				}
				if(num == 1) {
					$scope.getLostPatientDetails(number);
				}
			}

			$scope.patients_city = {};
			$scope.getPatientsCity = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					clinic_id: $rootScope.current_clinic.clinic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getPatientsCity(request, function (response) {
					if (response.status == true) {
                        $scope.patients_city = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.kco_data = [];
			$scope.patient_analytics = [];
			$scope.getKCO = function () {
				var from_date = new Date();
	            var year = from_date.getFullYear();
	            var month = from_date.getMonth();
	            var day = from_date.getDate();
	            $scope.report_filters.from_date = new Date(year, month - 6, day);
	            $scope.report_filters.to_date = new Date();
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					clinic_id: $rootScope.current_clinic.clinic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getKCO(request, function (response) {
					if (response.status == true) {
                        $scope.kco_data = response.kco;
                        $scope.patient_analytics = response.patient_analytics;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.diagnoses_data = [];
			$scope.getDiagnoses = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					clinic_id: $rootScope.current_clinic.clinic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getDiagnoses(request, function (response) {
					if (response.status == true) {
                        $scope.diagnoses_data = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
			}
			$scope.disease_list = [];
			$scope.patientProgressNextStep = function() {
				$scope.report_filters.is_no_data = false;
				$scope.disease_list = [];
				$scope.report_filters.search_disease_id = [];
				if($scope.report_filters.report_from == "1") {
					if($scope.report_filters.disease_id.length > 0) {
						angular.forEach($scope.report_filters.disease_id, function (value) {
	                        var selectedObj = $filter('filter')($scope.kco_data, {'disease_id':value},true);
	                        if(selectedObj != undefined && selectedObj.length > 0)
	                      		$scope.disease_list.push(selectedObj[0]);
	                    });
					} else {
						$scope.disease_list = $scope.kco_data;
					}
				} else if($scope.report_filters.report_from == "2") {
					if($scope.report_filters.clinical_notes_catalog_id.length > 0) {
						angular.forEach($scope.report_filters.clinical_notes_catalog_id, function (value) {
	                        var selectedObj = $filter('filter')($scope.diagnoses_data, {'disease_id':value},true);
	                        if(selectedObj != undefined && selectedObj.length > 0)
	                      		$scope.disease_list.push(selectedObj[0]);
	                      		
	                    });
					} else {
						$scope.disease_list = $scope.diagnoses_data;
					}
				}
				angular.forEach($scope.disease_list, function (value, key) {
                  	$scope.report_filters.search_disease_id.push(value.disease_id);
                });
				$scope.report_filters.is_show_table = true;
			}

			$scope.drug_generic = [];
			$scope.getDrugGeneric = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					clinic_id: $rootScope.current_clinic.clinic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getDrugGeneric(request, function (response) {
					if (response.status == true) {
                        $scope.drug_generic = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.drug_data = [];
			$scope.getDrugs = function () {
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					clinic_id: $rootScope.current_clinic.clinic_id,
					drug_generic_id: $scope.report_filters.drug_generic_id,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getDrugs(request, function (response) {
					if (response.status == true) {
                        $scope.drug_data = response.data;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.getInvoiceSummary = function (number,is_clear) {
				if(is_clear != undefined && is_clear == 1) {
					$scope.init();
					$scope.report_filters.per_page = $scope.global_per_page;
				}
				$scope.per_page = $scope.report_filters.per_page;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}

                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					search_str: $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getInvoiceSummary(request, function (response) {
					if (response.status == true) {
                        $scope.invoice_summary = response.data;
                        $scope.invoice_sum = response.sum;
                        $scope.footer_sum = response.footer_sum;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.changeCurrentClinicForInvoiceSummary = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.getInvoiceSummary(1,1);
            };

			$scope.invoiceSummaryExport = function () {
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				var params_arr = {
					search_str : $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					doctor_id : (($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id),
					search_clinic_id: $scope.report_filters.search_clinic_id,
					access_token: $rootScope.currentUser.access_token,
					user_id: $rootScope.currentUser.user_id
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/invoice_summary/" + request_data;
			}

			$scope.viewInvoice = function (invoiceObj) {
				$scope.invoice_url = $rootScope.app.base_url + '/prints/invoice';
                $scope.invoice_url += "?appointment_id=" + invoiceObj.billing_appointment_id;
                $scope.invoice_url += "&doctor_id=";
				$scope.invoice_url += ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id;
                $scope.invoice_url += "&patient_id=" + invoiceObj.billing_user_id;
                $scope.invoice_url += "&billing_id=" + invoiceObj.billing_id;
                $scope.invoice_url += "&time=" + $.now();
                var invoice_url = btoa(encodeURI($scope.invoice_url));
				$scope.invoice_url = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + invoice_url;
				$("#modal_invoice_view").modal("show");
			}

			$scope.getCancelAppointment = function (number,is_clear) {
				if(is_clear != undefined && is_clear == 1) {
					$scope.init();
					$scope.report_filters.per_page = $scope.global_per_page;
				}
				$scope.per_page = $scope.report_filters.per_page;
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}

                $scope.currentPage = number;
				var request = {
					doctor_id: ($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id,
					search_clinic_id: $scope.report_filters.search_clinic_id,
					search_str: $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					page: $scope.currentPage,
                    per_page: $scope.report_filters.per_page,
					access_token: $rootScope.currentUser.access_token,
                    user_id: $rootScope.currentUser.user_id,
				}
				ReportService.getCancelAppointment(request, function (response) {
					if (response.status == true) {
                        $scope.cancel_appointment = response.data;
                        $scope.total_rows = response.count;
                        $scope.total_page = Math.ceil(response.count/$scope.report_filters.per_page);
                        $scope.last_rows = $scope.currentPage*$scope.report_filters.per_page;
                        if($scope.last_rows > $scope.total_rows)
                        	$scope.last_rows = $scope.total_rows;
					} else {
						ngToast.danger(response.message);
					}
				});
			}
			$scope.changeCurrentClinicForCancelAppointment = function (clinic) {
                $rootScope.current_clinic = clinic;
                $scope.getCancelAppointment(1,1);
            };

			$scope.cancelAppointmentExport = function () {
				$scope.report_filters.real_from_date = '';
				$scope.report_filters.real_to_date = '';
				if($scope.report_filters.from_date != undefined && $scope.report_filters.from_date != '') {
					var month = $scope.report_filters.from_date.getMonth() + 1;
	                var day = $scope.report_filters.from_date.getDate();
	                var year = $scope.report_filters.from_date.getFullYear();
	                $scope.report_filters.real_from_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var from_date = new Date(year, month, day);
				}
				if($scope.report_filters.to_date != undefined && $scope.report_filters.to_date != '') {
					var month = $scope.report_filters.to_date.getMonth() + 1;
	                var day = $scope.report_filters.to_date.getDate();
	                var year = $scope.report_filters.to_date.getFullYear();
	                $scope.report_filters.real_to_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
	                var to_date = new Date(year, month, day);
				}
				var params_arr = {
					search_str : $scope.report_filters.search_str,
					from_date: $scope.report_filters.real_from_date,
					to_date: $scope.report_filters.real_to_date,
					doctor_id : (($rootScope.currentUser.clinic_doctor_id) ? $rootScope.currentUser.clinic_doctor_id : $rootScope.currentUser.user_id),
					search_clinic_id: $scope.report_filters.search_clinic_id,
					access_token: $rootScope.currentUser.access_token,
					user_id: $rootScope.currentUser.user_id
				}
				if(from_date > to_date) {
					ngToast.danger("Invalid date");
					return false;
				}
				var request_data = window.btoa(JSON.stringify(params_arr));
				window.location.href = $rootScope.app.apiUrl + "/reports_download/cancel_appointment/" + request_data;
			}

			$("body").on("click", ".family-history", function(){
				if($(this).parent().find('.short-history').hasClass('hide')) {
                    $(this).parent().find('.short-history').removeClass('hide');
                    $(this).parent().find('.full-history').addClass('hide');
                } else {
                    $(this).parent().find('.short-history').addClass('hide');
                    $(this).parent().find('.full-history').removeClass('hide');
                }
			});
			$scope.getClassActive = function (route) {
                return ($location.path() === route) ? 'active' : '';
            }

});
angular.module("app.dashboard.reports").filter('range', function() {
  return function(input, total) {
    total = parseInt(total);
    for (var i=1; i<=total; i++) {
      input.push(i);
    }
    return input;
  };
});