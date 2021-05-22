angular.module("medeasy")
        .service('ImportService', function ($rootScope, $http) {
            /* Import list function */
            this.getImportData = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_doctor_import_files",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
						doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };
			
            this.uploadDoctorImportFile = function (request, callback) {

                var form = $('#uploadImportFileForm')[0];

                var formData = new FormData(form);
                formData.append("user_id", $rootScope.currentUser.user_id);
                formData.append("access_token", $rootScope.currentUser.access_token);
                formData.append("doctor_id", request.doctor_id);
                formData.append("clinic_id", request.clinic_id);
                formData.append("device_type", 'web');
                // console.log(formData);
                //return false;
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/upload_doctor_import_file",
                    transformRequest: angular.identity,
                    data: formData,
                    enctype: 'multipart/form-data',
                    headers: {'Content-Type': undefined}
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    console.log(error);
                });
            };

            this.validateDoctorImportFile = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/validate_doctor_import_file",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        import_file_id: request.import_file_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };

            this.doctorNameSelection = function (request, callback) {
                // console.log(request);
                // return false;
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/doctor_name_selection",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        import_file_id: request.import_file_id,
                        selected_doctor_name: request.selected_doctor_name
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };

            this.importReadyForImport = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/ready_for_import",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        import_file_id: request.import_file_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };

            this.getImportLog = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_import_log",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id,
                        import_file_id: request.import_file_id,
                        search: request.search,
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };

            this.getImportFileType = function (request, callback) {
                $http({
                    method: 'POST',
                    url: $rootScope.app.apiUrl + "/get_import_file_type",
                    data: {
                        user_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        doctor_id: request.doctor_id,
                        clinic_id: request.clinic_id
                    }
                }).then(function successCallback(response) {
                    callback(response.data);
                }, function errorCallback(error) {
                    
                });
            };

		});