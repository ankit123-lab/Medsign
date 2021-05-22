/*
 * Controller Name: ImportController
 * Use: This controller is used for Import activity
 */
angular.module("app.import")
        .controller("ImportController", function ($scope, AuthService, ImportService, $timeout, $state, CommonService, $rootScope, ngToast, $location, uiCalendarConfig, $filter, SettingService, minutesToHourFilter, SweetAlert, fileReader, $interval) {
			var current_date = new Date();
			current_date.setHours(00);
			current_date.setMinutes(00);
			current_date.setSeconds(00);
			$scope.import_data_list = [];
			$scope.import_file_type = [];
			$scope.doctor_name_list = [];
			$scope.import_file_id = '';
			$scope.log_status = '';
			$scope.doctor_select = {};
			$scope.log_data = [];
			$scope.status = false;
			$scope.is_import_hide = true;
			$scope.import_file_type_id = '';
			
			
			$scope.init = function(){};
			$scope.fileType = function (id) {
				$scope.import_file_type_id = id;
			}
			$scope.getImportData = function () {
				$scope.is_import_hide = true;
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
				}
				ImportService.getImportData(request, function (response) {
					if (response.status == true) {
						$scope.import_file_list = response.data;
						if(response.data.length==0)
							$scope.is_import_hide = false;
					}
				});
			}
			$scope.changeCurrentClinicForImport = function (clinic) {
				$rootScope.current_clinic = clinic;
				$scope.getImportData();
			}
			$scope.uploadDoctorImportFile = function () {
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
				}
				ImportService.uploadDoctorImportFile(request, function (response) {
					if (response.status == true) {
						$("#modal_upload_file").modal("hide");
						$scope.getImportData();
					} else {
						ngToast.danger(response.message);
					}
				});
			}

			$scope.importValidate = function (import_file_id) {
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					import_file_id : import_file_id
				}
				ImportService.validateDoctorImportFile(request, function (response) {
					if (response.status == true) {
						if(response.is_doctor_selection == true) {
							$scope.doctor_name_list = response.doctor_name_arr;
							$scope.doctor_select.import_file_id = response.import_file_id;
							$('#modal_doctor_selection').modal('show');
						} else {
							$scope.getImportData();
						}
					}
				});
			}

			$scope.doctor_name_selection = function (form) {
				if (form.$valid) {
					
					var request = {
						clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
						doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
						import_file_id: $scope.doctor_select.import_file_id,
						selected_doctor_name: $scope.doctor_select.selected_doctor_name
					}
					ImportService.doctorNameSelection(request, function (response) {
						if (response.status == true) {
							$("#modal_doctor_selection").modal("hide");
							$scope.getImportData();
						}
					});
				}
			}

			$scope.importReadyForImport = function (import_file_id) {
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					import_file_id : import_file_id
				}
				ImportService.importReadyForImport(request, function (response) {
					if (response.status == true) {
						$scope.getImportData();
					}
				});
			}

			$scope.get_import_log = function (import_file_id,status) {

				if(status == 1) {
					search = '"status_id":' + status;
				} else if(status == 3) {
					search = 'all_data_count_log';
				}
				$scope.log_status = status;
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					import_file_id : import_file_id,
					search : search
				}
				$scope.log_data = [];
				ImportService.getImportLog(request, function (response) {
					if (response.status == true) {
						$scope.log_data = response.data;
						$scope.status = status;
						$('#doctor_file_log').modal('show');
					}
				});
			}

			$scope.get_import_file_type = function () {
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
				}
				ImportService.getImportFileType(request, function (response) {
					if (response.status == true) {
						$scope.import_file_type = response.data;
					}
				});
			}
			
});