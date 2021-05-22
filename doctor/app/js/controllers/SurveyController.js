/*
 * Controller Name: SurveyController
 * Use: This controller is used for Survey activity
 */
angular.module("app.survey")
        .controller("SurveyController", function ($scope, AuthService, SurveyService, $timeout, $state, CommonService, $rootScope, ngToast, $location, uiCalendarConfig, $filter, SettingService, minutesToHourFilter, SweetAlert, fileReader, $interval) {
			var current_date = new Date();
			current_date.setHours(00);
			current_date.setMinutes(00);
			current_date.setSeconds(00);
			$scope.survey_data_list = [];
			$scope.active_survey = [];
			$scope.current_survey_url = '';
			$scope.surveyFormSubmit = false;
			
			$scope.init = function(){};
			$scope.getSurveyData = function () {
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
				}
				SurveyService.getSurveyData(request, function (response) {
					if (response.status == true) {
						$scope.survey_data_list = response.survey_data;
					}
				});
			}
			$scope.setSurveyDocVideoUrls = function(surveyId,curSurveyObj){
				$scope.current_survey_title = curSurveyObj.title;
				if(curSurveyObj.survey_file_path != undefined && curSurveyObj.survey_file_path != ''){
					var pdf_url = btoa(encodeURI($rootScope.app.base_url + 'pdf_preview/web/view_pdf.php?file_url=' + btoa(encodeURI(curSurveyObj.survey_file_path))));
					$scope.current_survey_url = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + pdf_url; 
					//$scope.current_survey_url = curSurveyObj.survey_file_path;
				}else if(curSurveyObj.survey_videourl != undefined && curSurveyObj.survey_videourl != ''){
					$scope.current_survey_url = curSurveyObj.survey_videourl;
				}
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					log_type: 2,
					survey_type_id: curSurveyObj.survey_type_id,
					survey_id: surveyId
				}
				SurveyService.setSurveyLog(request, function (response) {
					if (response.status == true) {
						//$scope.survey_data_list = response.survey_data;
					}
				});
			}
			$scope.unSetSurveyDocVideoUrls = function(){
				$timeout(function(){
					$scope.current_survey_url = '';
					$('#survey_content_modal').find("iframe").attr("src", "");
				});
			}
			$scope.getSurveyContent = function(curSurveyObj){
				$scope.active_survey = curSurveyObj;
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					log_type: 1,
					survey_id: $scope.active_survey.survey_id
				}
				SurveyService.getSurveyContent(request, function (response) {
					if (response.status == true) {
						$scope.active_survey = response.survey_data;
						$scope.active_survey.is_consent_accept = false;
					}
				});
			}
			$scope.unSetActiveSurveyDetails = function(){
				$timeout(function(){
					$scope.active_survey = [];
					$scope.surveyFormSubmit = false;
				});
			}
			$scope.setSurveyConsentAccept = function(surveyId){
				var request = {
					clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
					doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
					log_type: 3,
					survey_id: surveyId,
					survey_type_id:''
				}
				SurveyService.setSurveyLog(request, function (response) {
					if (response.status == true) {
						$scope.active_survey.is_consent_accept = true;
						$scope.surveyFormSubmit = false;
						$timeout(function(){
							if($('#take_survey_modal'))
								$('#take_survey_modal .modal-backdrop').height($("#take_survey_modal .modal-backdrop.in").height() + 500);
						},100);
					}
				});
			}
			$scope.saveSurveyData = function(){
					$scope.surveyFormSubmit = true;
					if ($scope.surveyForm.$valid) {
						SweetAlert.swal({
								title: "Are you sure want to submit your feedback on this survey ?",
								text: "Your will not be able to recover this.",
								type: "warning",
								showCancelButton: true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, Submit !",
								closeOnConfirm: false
							},
							function (isConfirm) {
									if (!isConfirm) {
										$rootScope.app.isLoader = false;
										return;
									}
									$rootScope.app.isLoader = true;
									if($scope.active_survey != undefined && $scope.active_survey.answerData != undefined && $scope.active_survey.answerData.length > 0){
										var answerData = [];
										angular.forEach($scope.active_survey.answerData, function (valObj, key) {
											if(valObj.survey_type == 2){ // Checkbox type 
												var allAnswerOpts = [];
												angular.forEach(valObj.options, function (subValObj, subKey) {
													if(subValObj.status == true)
														allAnswerOpts.push(subValObj.option_txt);
												});
												answerData.push({'question_id':valObj.question_id,'options':allAnswerOpts});
											}else if(valObj.survey_type == 1){ //Radio Type
												answerData.push({'question_id':valObj.question_id,'options':[valObj.answer]});
											}
										});
										var request = {
											clinic_id: ($rootScope.current_clinic && $rootScope.current_clinic.clinic_id) ? $rootScope.current_clinic.clinic_id : '',
											doctor_id: ($rootScope.current_doctor && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
											survey_id: $scope.active_survey.survey_id,
											questions: answerData
										}
										SurveyService.saveDoctorSurveyData(request, function (response) {
											$rootScope.app.isLoader = true;
											if (response.status == true) {
												ngToast.success({
														content: response.message,
														className: '',
														dismissOnTimeout: true,
														timeout: 5000
												});
												$scope.getSurveyData(); //update listing
												$('body,html').removeClass('modal-open');
                                                $("#take_survey_modal").modal("hide");
												swal.close();
												//$("#take_survey_modal").hide();
												return;
											}
										});
									}
							}
						);
					}else{
						if($('#take_survey_modal'))
							$('#take_survey_modal .modal-backdrop').height($("#take_survey_modal .modal-backdrop.in").height() + 100);
					}
			}
			$scope.validateChkGrpSelected = function(object) {
				return Object.keys(object).some(function (key) {
					return object[key].status;
				});
			}
});