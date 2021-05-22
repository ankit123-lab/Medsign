/*
 * Controller Name: PatienrController
 * Use: This controller is used for patient activity
 */
angular.module("app.dashboard")
        .controller("PatientController", function ($scope, CommonService, PatientService, ngToast,
                fileReader, $rootScope, SMOKE, ALCOHOL, $filter, APPOINTMENT_TYPE, SettingService, SweetAlert, $timeout, $sce, $parse, EncryptDecrypt,$uibModal,$localStorage,$state){

        	$scope.objCurrentCalculator = {};
			$scope.imageCompareObj = {'img1':{},'img2':{},'doc1':{},'doc2':{}};
			$scope.objReportsCompareSel = [];
			$scope.allCompareObjDisabled = false;
			$scope.isDocumentCompare = false;
			$scope.is_invoice_import_data = false;
			$scope.is_appoinment_import_data = false;
			$scope.is_appoinment_done = 0;
			$scope.active_vital_name = 'WT';
			$scope.active_analytics_id = '';
			$scope.chart_heading = 'Weight(kg)';
			$scope.analytics_chart_heading = '';
			$scope.analytics_chart_short_title = '';
			$scope.analytics_min = '';
			$scope.analytics_max = '';
			$scope.diagramsModelShow = false;
			$scope.drug_generic_id = '';
			$scope.edit_patient_detail_popup = function() {
				$scope.$emit('editPatientData', $scope.current_patient.user_id);
			}
			$scope.rxUploadObj = [];
			var rx_upload_date = new Date();
            var rx_year = rx_upload_date.getFullYear();
            var rx_month = rx_upload_date.getMonth();
            var rx_day = rx_upload_date.getDate();
            $scope.rx_upload_pre = [];
            $scope.rx_upload_pre.edit = false;
            $scope.report_upload_pre = [];
            $scope.report_upload_pre.edit = false;
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
                    text: "1 ½",
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
            $scope.health_advice_popup = function() {
            	$scope.health_data = {
            		doctor_id: $rootScope.current_doctor.user_id,
            		patient_id: $scope.current_patient.user_id
            	};
	            var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'app/views/patient/modal/health_advice.html?' + $rootScope.getVer(2),
                    controller: 'HealthAdviceController',
                    size: 'lg',
                    backdrop: 'static',
                    keyboard: false,
                    resolve: {
                        items: function () {
                            return $scope.health_data;
                        }
                    }
                });
	            modalInstance.result.then(function (health_data) {
	                	
	            }, function () {
	              
	            });
            }
			/* Line chart configurations */
			$scope.renderLineChart = function(vital_data, chart_name){
				$scope.isVitalChartActive = false;
				var chart_labels = [];
				var chart_data = [];
				var systolic_chart_data = [];
				var diastolic_chart_data = [];
				var chart_title = '';
				var chart_value = 0;
				var yAxesMin = 0;
				var yAxesMax = 200;
				$scope.active_vital_name = chart_name;
				angular.forEach(vital_data, function (value, key) {
					if(chart_name == 'WT')	{
						chart_title = 'Weight(kg)';
						chart_value = $filter('PoundToKG')(value.vital_report_weight);
						yAxesMax = 200;
					} else if(chart_name == 'PR') {
						chart_title = 'Pulse Rate/Min';
						chart_value = value.vital_report_pulse;
						yAxesMax = 500;
					} else if(chart_name == 'RR') {
						chart_title = 'Resp.Rate/Min';
						chart_value = value.vital_report_resp_rate;
						yAxesMax = 70;
					} else if(chart_name == 'SpO2') {
						chart_title = 'Sp02(%)';
						chart_value = value.vital_report_spo2;
						yAxesMax = 100;
					} else if(chart_name == 'BP') {
						chart_title = 'Blood Pressure(mm Hg)';
						chart_value = value.vital_report_bloodpressure_systolic;
						yAxesMax = 300;
					} else if(chart_name == 'TEMP') {
						chart_title = 'Temperature';
						chart_value = value.vital_report_temperature;
						yAxesMax = 110;
					}
					if(chart_value > 0) {
						if(chart_name == 'BP') {
							systolic_chart_data.push(value.vital_report_bloodpressure_systolic);
							diastolic_chart_data.push(value.vital_report_bloodpressure_diastolic);
						} else {
							chart_data.push(chart_value);
						}
						chart_labels.push($filter('date')(new Date(value.vital_report_date), "dd/MM/yyyy"));
					}

				});
				if(chart_labels.length > 0)
					$scope.isVitalChartActive = true;
				$scope.chart_heading = chart_title;
				$scope.labels = chart_labels;
				var is_bp = false;
				if(chart_name == 'BP') {
					$scope.series = ['Systolic', 'Diastolic'];
					$scope.colors = ['#30aca5','#558bb7'];
					$scope.data = [systolic_chart_data,diastolic_chart_data];
					is_bp = true;
				} else {
					$scope.series = [chart_title];
					$scope.colors = ['#30aca5'];
					$scope.data = [chart_data];
				}
				$scope.onClick = function (points, evt) {
					
				};
				$scope.onHover = function (points) {
					if (points.length > 0) {
						
					} else {
						
					}
				};
				$scope.datasetOverride = [{ yAxisID: 'y-axis-1' }];
				$scope.options = {
					scales: {
						yAxes: [
							{
								id: 'y-axis-1',
								type: 'linear',
								display: true,
								position: 'left',
								ticks: {min: yAxesMin, max:yAxesMax,fontColor: "#000"}
							}
						],
						xAxes : [{ticks: {fontColor: "#000"}}]
					},

					elements: {
                        line: {
                                fill: false
                        	}
                    },
					legend: {
				      display: is_bp,
				      position: 'bottom'
				    }
				};
				
			}

			/* Health Analytics Line chart configurations */
			$scope.add_remove_dataset = 0;
			$scope.analytics_max_dataset = 0;
			$scope.analytics_dataset_zoom_percent = 10;
			$scope.isShowHealthAnalyticPagination = true;
			$scope.renderHealthAnalyticsLineChart = function(health_analytics_data, analytics_id){
				$scope.graph_analytics_id = analytics_id;
				$scope.isHealthAnalyticsChartActive = false;
				$scope.isShowHealthAnalyticPagination = true;
				var healthAnalabels = [];
				var analyticsValue = [];
				var analyticsChartSeries = '';
				var analyticsChartvalue = 0;
				var yAxesMin = 0;
				var yAxesMax = 200;
				angular.forEach(health_analytics_data, function (value, key) {
					var health_analytics_report_data_list = value.health_analytics_report_data.split('$@@$');
					for(var i=0; i < health_analytics_report_data_list.length; i++){
						var selectedHealthObj = $filter('filter')(JSON.parse(health_analytics_report_data_list[i]), {'id':analytics_id},true);
						if(selectedHealthObj != undefined && selectedHealthObj.length > 0 && selectedHealthObj[0].value != undefined && selectedHealthObj[0].value > 0) {
							healthAnalabels.push($filter('date')(new Date(value.health_analytics_report_date), "dd/MM/yyyy"));
							analyticsChartValue = selectedHealthObj[0].value;
							$scope.analytics_chart_heading = selectedHealthObj[0].name;
							$scope.analytics_chart_short_title = selectedHealthObj[0].precise_name;
							analyticsChartSeries = selectedHealthObj[0].precise_name;
							analyticsValue.push(parseInt(analyticsChartValue));
						}	
					}
				});
				
				if(healthAnalabels.length > 0)
					$scope.isHealthAnalyticsChartActive = true;

				if($scope.patient_health_data != undefined && $scope.patient_health_data.length > 0) {
					var selectedPatientHealthDataObj = $filter('filter')($scope.patient_health_data, {'patient_analytics_analytics_id':analytics_id},true);
					if(selectedPatientHealthDataObj != undefined && selectedPatientHealthDataObj.length > 0 && selectedPatientHealthDataObj[0].health_analytics_test_validation != undefined) {
						var health_analytics_test_validation_arr = JSON.parse(selectedPatientHealthDataObj[0].health_analytics_test_validation);
						var gender = $scope.current_patient.user_gender;
						$scope.analytics_min = '';
						$scope.analytics_max = '';
						if(health_analytics_test_validation_arr.normal_range != undefined && health_analytics_test_validation_arr.normal_range.min != undefined && health_analytics_test_validation_arr.normal_range.max != undefined){
							$scope.analytics_min = health_analytics_test_validation_arr.normal_range.min;
							$scope.analytics_max = health_analytics_test_validation_arr.normal_range.max;
							var chartRangeAnotationObj = [{
							  	id: 'box1',
							    type: 'box',
							    yScaleID: 'y-axis-1',
							    yMin: $scope.analytics_min,
							    yMax: $scope.analytics_max,
							    backgroundColor: 'rgba(48, 172, 165, 0.3)',
							    borderColor: 'rgba(48, 172, 165, 0.3)',
							}];
						}else if(health_analytics_test_validation_arr.all_range != undefined){
							var chartRangeAnotationObj = [];
							var cntr = 1;
							angular.forEach(health_analytics_test_validation_arr.all_range, function (value, key) {
								chartRangeAnotationObj.push({
								  	id: 'box'+cntr,
								    type: 'box',
								    yScaleID: 'y-axis-1',
								    yMin: value.min,
								    yMax: value.max,
								    backgroundColor: value.backgroundColor,
								    borderColor: value.borderColor
								});
								chartRangeAnotationObj.push({
									type: 'line',
									drawTime: 'afterDatasetsDraw',
									id: 'a-line-'+cntr,
									mode: 'horizontal',
									scaleID: 'y-axis-1',
									value: value.min,
									/*endValue: 26,*/
									borderColor: 'grey',
									borderWidth: 2,
									borderDash: [2, 2],
									borderDashOffset: 5,
									label: {
										backgroundColor: 'rgba(0,0,0,0.4)',
										fontFamily: "sans-serif",
										fontSize: 12,
										fontStyle: "bold",
										fontColor: "#fff",
										xPadding: 6,
										yPadding: 6,
										cornerRadius: 6,
										position: "center",	
										xAdjust: 0,
										yAdjust: 0,
										enabled: true,
										content: value.dataLabel,
										rotation: 90
									}
								});
								cntr++;
							});
						}else{
							var chartRangeAnotationObj = [];
						}
						// console.log(chartRangeAnotationObj);
						if(health_analytics_test_validation_arr.validation.max != undefined) {
							yAxesMax = health_analytics_test_validation_arr.validation.max;
						}
						if (health_analytics_test_validation_arr.validation.gender != undefined && health_analytics_test_validation_arr.validation.gender.length > 0 && gender != '') {
							if(gender == 'female' && health_analytics_test_validation_arr.validation.gender.female.max != undefined) {
								var yAxesMax = health_analytics_test_validation_arr.validation.gender.female.max;
							} else if(gender == 'male' && health_analytics_test_validation_arr.validation.gender.male.max != undefined) {
								var yAxesMax = health_analytics_test_validation_arr.validation.gender.male.max;
							}
						}
					}
				}
				$scope.chartRangeAnotationObj = chartRangeAnotationObj;
				$scope.analytics_max_dataset = yAxesMax;
				Array.prototype.max = function() {
				  return Math.max.apply(null, this);
				};
				if(analyticsValue.length > 0);
					yAxesMax = analyticsValue.max();
				if(yAxesMax > 100)
					yAxesMax = Math.ceil((yAxesMax)/100)*100;
				else
					yAxesMax = Math.ceil((yAxesMax)/10)*10;
				if($scope.analytics_max_dataset > (parseInt(yAxesMax)+$scope.add_remove_dataset))
					yAxesMax = parseInt(yAxesMax)+$scope.add_remove_dataset;
				else
					yAxesMax = $scope.analytics_max_dataset;

				$scope.yAxesMaxDatasetValue = yAxesMax;
				$scope.labels = healthAnalabels;
				$scope.colors = ['#30aca5'];
				$scope.series = [analyticsChartSeries];
				$scope.data = [
					analyticsValue
				];
				$scope.onClick = function (points, evt) {
					
				};
				$scope.onHover = function (points) {
					if (points.length > 0) {
						
					} else {
						
					}
				};
				$scope.datasetOverride = [{ yAxisID: 'y-axis-1' }];
				$scope.options = {
					scales: {
						yAxes: [
							{
								id: 'y-axis-1',
								type: 'linear',
								display: true,
								scaleLabel: {
									display: true,
									labelString: $scope.analytics_chart_short_title
								},
								position: 'left',
								ticks: {min: 0, max:parseInt(yAxesMax),fontColor: "#000"}
							}
						],
						xAxes : [{ticks: {fontColor: "#000"}}]
					},
					elements: {
                        line: {
                                fill: false
                        	}
                    },
                     annotation: {
					      drawTime: "afterDatasetsDraw",
					      annotations: chartRangeAnotationObj
					    }
				};
			}
			/* Compliance Line chart configurations */
			$scope.complianceLineChart = function(drug_data){
				var chartData = [];
				var chartLabels = [];
				var chartSeries = [];
				var chartColors = [];
				var chartDate = [];
				angular.forEach(drug_data, function (value, key) {
					var compData = [];
					chartSeries.push(value.drug_name);
					chartColors.push('#' + value.drug_color_code);
					angular.forEach(value.reminder_record_date, function (value1, key1) {
						if(value1.take_count != undefined) {
							compData.push(parseInt(value1.take_count));
						} else {
							compData.push(0);
						}
					});
					chartData[key] = compData;
					chartDate = Object.keys(value.reminder_record_date);
				});
				angular.forEach(chartDate, function (value, key) {
					chartLabels.push($filter('date')(new Date(value), "dd/MM/yyyy"));
				});
				$scope.labels = chartLabels;
				$scope.series = chartSeries;
				$scope.colors = chartColors;
				$scope.data = chartData;
				$scope.onClick = function (points, evt) {
					
				};
				$scope.onHover = function (points) {
					if (points.length > 0) {
						
					} else {
						
					}
				};
				$scope.datasetOverride = [{ yAxisID: 'y-axis-1' }];
				$scope.options = {
					scales: {
						yAxes: [
							{
								id: 'y-axis-1',
								type: 'linear',
								display: true,
								position: 'left',
								ticks: {fontColor: "#000"}
							}
						],
						xAxes : [{ticks: {fontColor: "#000"}}]
					},
					legend: {
				      display: true,
				      position: 'bottom',
				      labels: {
			                fontColor: '#000'
			            }
				    },
					elements: {
                        line: {
                                fill: false
                        	}
                    }
				};
				
			}

			$scope.show_vital_chart = function(chart_name) {
				$scope.renderLineChart($scope.vital_table_data,chart_name);
			}
			$scope.uas7_daily_data = [];
			$scope.uas7_weekly_data = [];
			$scope.graphType = 1;
			$scope.show_analytic_chart = function(analytics_id) {
				$scope.active_analytics_id = analytics_id;
				$scope.add_remove_dataset = 0;
				if(analytics_id == 308) {
					var request = {
                        patient_id: $scope.current_patient.user_id,
                        device_type: 'web',
                    	user_id: $rootScope.currentUser.user_id,
                    	access_token: $rootScope.currentUser.access_token
                    };
                    PatientService
                        .getUAS7Parameters(request, function (response) {
                        	$scope.uas7_daily_data = response.uas7_daily_data;
							$scope.uas7_weekly_data = response.uas7_weekly_data;
                            $scope.graphType = 1;
                            $scope.renderUAS7LineChart(response.uas7_daily_data, $scope.graphType);
                        }, function (error) {
                            
                        });
				} else {
					$scope.renderHealthAnalyticsLineChart($scope.patient_health_analysis_data,analytics_id);
				}
			}
			$scope.healthAnalyticTab = function(tab) {
				if($scope.active_analytics_id == 308 && tab == 2) {
					$scope.isShowHealthAnalyticPagination = false;
				} else {
					$scope.isShowHealthAnalyticPagination = true;
				}
				if(tab == 2)
					$scope.show_analytic_chart($scope.active_analytics_id);
			}
			$scope.uas7Graph = function(tab) {
				$scope.graphType = tab;
				if(tab==1) { //1=Daily Graph
					$scope.renderUAS7LineChart($scope.uas7_daily_data,$scope.graphType);
				} else { //1=Weekly Graph
					$scope.renderUAS7LineChart($scope.uas7_weekly_data,$scope.graphType);
				}
			}
			$scope.renderUAS7LineChart = function(UAS7ChartData, graphType) {
				$scope.isShowHealthAnalyticPagination = false;
				$scope.isHealthAnalyticsChartActive = false;
				$scope.analytics_chart_heading = "";
				$scope.analytics_chart_short_title = "";
				var analyticsChartSeries = "";
				var selectedPatientHealthDataObj = $filter('filter')($scope.patient_health_data, {'patient_analytics_analytics_id':"308"},true);
				if(selectedPatientHealthDataObj != undefined && selectedPatientHealthDataObj.length > 0 && selectedPatientHealthDataObj[0].health_analytics_test_validation != undefined) {
					var health_analytics_test_validation_arr = JSON.parse(selectedPatientHealthDataObj[0].health_analytics_test_validation);
					$scope.analytics_chart_heading = selectedPatientHealthDataObj[0].patient_analytics_name;
					$scope.analytics_chart_short_title = selectedPatientHealthDataObj[0].patient_analytics_name_precise;
					analyticsChartSeries = selectedPatientHealthDataObj[0].patient_analytics_name_precise;
				}
				if($scope.graphType == 1) {
					analyticsChartSeries = "UAS";
					$scope.analytics_chart_heading = "Urticaria Activity Score (UAS)";
				}
				var healthAnalabels = [];
				var analyticsValue = [];
				$scope.analytics_min = '';
				$scope.analytics_max = '';
				var yAxesMin = 0;
				var yAxesMax = 6;
				if($scope.graphType == 2)
					yAxesMax = 42;
				angular.forEach(UAS7ChartData, function (value, key) {
					healthAnalabels.push(value.patient_diary_date);
					analyticsValue.push(parseInt(value.uas7_score));
				});
				
				if(healthAnalabels.length > 0)
					$scope.isHealthAnalyticsChartActive = true;
				var chartRangeAnotationObj = [];
				if(graphType == 2) {
					var cntr = 1;
					angular.forEach(health_analytics_test_validation_arr.all_range, function (value, key) {
						chartRangeAnotationObj.push({
						  	id: 'box'+cntr,
						    type: 'box',
						    yScaleID: 'y-axis-1',
						    yMin: value.min,
						    yMax: value.max,
						    backgroundColor: value.backgroundColor,
						    borderColor: value.borderColor
						});
						chartRangeAnotationObj.push({
							type: 'line',
							drawTime: 'afterDatasetsDraw',
							id: 'a-line-'+cntr,
							mode: 'horizontal',
							scaleID: 'y-axis-1',
							value: value.min,
							/*endValue: 26,*/
							borderColor: 'grey',
							borderWidth: 2,
							borderDash: [2, 2],
							borderDashOffset: 5,
							label: {
								backgroundColor: 'rgba(0,0,0,0.4)',
								fontFamily: "sans-serif",
								fontSize: 12,
								fontStyle: "bold",
								fontColor: "#fff",
								xPadding: 6,
								yPadding: 6,
								cornerRadius: 6,
								position: "center",	
								xAdjust: 0,
								yAdjust: 0,
								enabled: true,
								content: value.dataLabel,
								rotation: 90
							}
						});
						cntr++;
					});
				}
				$scope.chartRangeAnotationObj = chartRangeAnotationObj;
				$scope.analytics_max_dataset = yAxesMax;
				Array.prototype.max = function() {
				  return Math.max.apply(null, this);
				};
				if(analyticsValue.length > 0);
					yAxesMax = analyticsValue.max();
				if(yAxesMax > 100)
					yAxesMax = Math.ceil((yAxesMax)/100)*100;
				else
					yAxesMax = Math.ceil((yAxesMax)/10)*10;
				if($scope.analytics_max_dataset > (parseInt(yAxesMax)+$scope.add_remove_dataset))
					yAxesMax = parseInt(yAxesMax)+$scope.add_remove_dataset;
				else
					yAxesMax = $scope.analytics_max_dataset;

				$scope.yAxesMaxDatasetValue = yAxesMax;
				$scope.labels = healthAnalabels;
				$scope.colors = ['#30aca5'];
				$scope.series = [analyticsChartSeries];
				$scope.data = [
					analyticsValue
				];
				$scope.onClick = function (points, evt) {
					
				};
				$scope.onHover = function (points) {
					if (points.length > 0) {
						
					} else {
						
					}
				};
				$scope.datasetOverride = [{ yAxisID: 'y-axis-1' }];
				$scope.options = {
					scales: {
						yAxes: [
							{
								id: 'y-axis-1',
								type: 'linear',
								display: true,
								scaleLabel: {
									display: true,
									labelString: analyticsChartSeries
								},
								position: 'left',
								ticks: {min: 0, max:parseInt(yAxesMax),fontColor: "#000"}
							}
						],
						xAxes : [{ticks: {fontColor: "#000"}}]
					},
					elements: {
                        line: {
                                fill: false
                        	}
                    },
                     annotation: {
					      drawTime: "afterDatasetsDraw",
					      annotations: chartRangeAnotationObj
					    }
				};
			}

			$scope.analytic_chart_remove_dataset = function() {
				$scope.add_remove_dataset = $scope.add_remove_dataset - ($scope.analytics_max_dataset*$scope.analytics_dataset_zoom_percent/100);
				if(($scope.yAxesMaxDatasetValue) < 0){
					$scope.add_remove_dataset = $scope.add_remove_dataset + ($scope.analytics_max_dataset*$scope.analytics_dataset_zoom_percent/100);
					return false;
				}
				if($scope.active_analytics_id == 308) {
					if($scope.graphType == 1) { //1=Daily Graph
						$scope.renderUAS7LineChart($scope.uas7_daily_data,$scope.graphType);
					} else { //1=Weekly Graph
						$scope.renderUAS7LineChart($scope.uas7_weekly_data,$scope.graphType);
					}
				} else {
					$scope.renderHealthAnalyticsLineChart($scope.patient_health_analysis_data,$scope.graph_analytics_id);
				}
			}
			$scope.analytic_chart_add_dataset = function() {
				$scope.add_remove_dataset = $scope.add_remove_dataset + ($scope.analytics_max_dataset*$scope.analytics_dataset_zoom_percent/100);
				if($scope.analytics_max_dataset <= ($scope.yAxesMaxDatasetValue)){
					$scope.add_remove_dataset = $scope.add_remove_dataset - ($scope.analytics_max_dataset*$scope.analytics_dataset_zoom_percent/100);
					return false;
				}
				if($scope.active_analytics_id == 308) {
					if($scope.graphType == 1) { //1=Daily Graph
						$scope.renderUAS7LineChart($scope.uas7_daily_data,$scope.graphType);
					} else { //1=Weekly Graph
						$scope.renderUAS7LineChart($scope.uas7_weekly_data,$scope.graphType);
					}
				} else {
					$scope.renderHealthAnalyticsLineChart($scope.patient_health_analysis_data,$scope.graph_analytics_id);
				}
			}
			
			$scope.openDaigramsModal = function() {
            	$scope.diagramsModelShow = true;
            	$rootScope.gTrack('patient_tools');
            	$timeout(function () {
	            	$("#modal_diagrams").modal("show");
				}, 100);
            }

			/* Previous patient health analytic code START */
			$scope.isShowPatientPreviousHealthAnalyticForm = true;
            $scope.fullScreenPageLimit = 5;
			var healthAnalysisCurrentDefaultDateObj = new Date();
			var healthAnalysisDefaultDate = new Date(healthAnalysisCurrentDefaultDateObj.getFullYear(), healthAnalysisCurrentDefaultDateObj.getMonth(), healthAnalysisCurrentDefaultDateObj.getDate() - 1);
			$scope.resetPphad = function(){
				$scope.pphadsubmitted = false;
				$scope.patientPreviousHealthAnalyticData = [{search: '', healthAnalysis: {name:'', value:'', date: healthAnalysisDefaultDate}, isOpen: false}];
				$scope.pphad_deleted_obj = [];
			}
			
			$scope.export = function(element_id){
				return false;
				var imgBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAL4AAABGCAIAAAD917m+AAAACXBIWXMAAAsTAAALEwEAmpwYAAAF5GlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIiB4bWxuczpwaG90b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wOkNyZWF0ZURhdGU9IjIwMTgtMDMtMjhUMTI6MDg6NDQrMDU6MzAiIHhtcDpNb2RpZnlEYXRlPSIyMDE4LTAzLTI4VDEyOjEwOjM1KzA1OjMwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDE4LTAzLTI4VDEyOjEwOjM1KzA1OjMwIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjdlOTlmOWZjLTFhNWItYjA0MC05NDFmLTkzYjlhZTVkNmQyOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0NjRFN0VEOUUxNUYxMUU3ODE5MkFCMUM4RjhBRDU0RCIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjQ2NEU3RUQ5RTE1RjExRTc4MTkyQUIxQzhGOEFENTREIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NDY0RTdFRDZFMTVGMTFFNzgxOTJBQjFDOEY4QUQ1NEQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NDY0RTdFRDdFMTVGMTFFNzgxOTJBQjFDOEY4QUQ1NEQiLz4gPHhtcE1NOkhpc3Rvcnk+IDxyZGY6U2VxPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6N2U5OWY5ZmMtMWE1Yi1iMDQwLTk0MWYtOTNiOWFlNWQ2ZDI4IiBzdEV2dDp3aGVuPSIyMDE4LTAzLTI4VDEyOjEwOjM1KzA1OjMwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDwvcmRmOlNlcT4gPC94bXBNTTpIaXN0b3J5PiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PnX7k+sAAAhHSURBVHic7ZxfbFPXHcd/nWZbsi+xYxxf48RxzUJT7JmwhZCFuqAsaStRt51a9YF50vaUik7TJlRRTVM1rdNUFVVo0tShou2hEhGaNFXQZt3aBlLShAwCLdTYCU2IcQjGDo5JUttSfF/2cMbJz9cOhBOUG8PvIz/cc+7J8e/6fs/vz7kXHtn6z24giHvnO1obQFQqJB1CEJIOIQhJhxCEpEMIQtIhBCHpEIKQdAhBSDqEICQdQhCSDiEISYcQhKRDCELSIQQh6RCCkHQIQUg6hCAkHUIQkg4hCEmHEISkQwhC0iEE+a7WBmhPqN5Ta5IAIHprpieZ0NqciuFhl46sN7zW0sabsU96Itl5De2pIB72gGXTG7Q2oVLR0uu82dT8XEMjb74zPNQ9GcMDDrftbHHW8ebPVtEl7N/s3+P133nM0Wj4wEh4dexZg2jpdRxGE2422x24KesNWDcA4JGksvPIekPQ4QxYbffdwlXAJ1UFHc6gwylXmv/T0uuY9HrcbC0WSqfDqRrvktapekL1npcf2+w2W1gzryhnElN/H40s5Zz2NjQGal1em50NPj42+q9r8RVcQZn5f1Ajc8V/NH753ZFLNr3hyDNB1oMdZ6je8wvfFhtaP33xGDceu+S+eGzfuSH8RT6pis8JAL/q+3Qgk76PF7IctJSOvdjrGHW6oMPJa5xdtS7V+HW6Iqkd3NbW7vaoZmh3e1qddX+9cE4V+2S94WDbk0w0fPAer39HybeUEk1Ph0713nmMT6r6048CXMSM5xoa25x1Q4kp3uORJKaMsgGRGf/6QN9AJn0jl+X9qkUFAM+63Pw4ryirrxvQNmDZiqUDANvlDfzYVyOrzkrIS5XqhmPU6V5raVPFr/d2dmDdcFQ3WwxZb/jzk+1lp7IZTTif4+Nf2PR42amMOp1bWgcAh8Yv5xWFd4bqiy4WK/742OgKbBdHM6/jk6pKO7fclkuo3mPU6VRneW4UdDixbo5Gw9FbMwDwvKeBB4vftz7x1L+Ps+P9m/04qHGfdAf93ROvb/khXwbxudl3vjw7kEn7pKpf+7e2lDgMAGixrudXl1eUt84OcuMPXzzP/eWJ+ASX3a5aF+8PWG1Ypvc35i4fzaRTXZzoMNxmS8BqG8ikG6utpWftpv+nyc97GngnLnN6konuXZ3Mu9iMplC9h/3ceIm/dXaQx8R954beVAqlXkGF12b/6qWf4h4cwnxSFddfXlF+998BFpIi2fmuof5jnbvv6thc0rr+5I2uoX527bz/H1fGuG0tzjpZb0gVFgDgaRStoulprTaiNAtYbpTzpvM57px3yBsAoMO9kZ/iw0y3Vypeyk12uXtXJ/+YUD7E9ogDVhtf4vG5WdV+8bsjl1Z4ITjtOBGfUN3Iv126UPonPckEDkZdTc1Hngke69ztt67HwyLZ+Wh6mjdfrH+UHbShy/84dmWF9gujmXSqkNfJKUrkZood76h14Zv92dUJPqw0NwIAr82OP6VLHN+PidlbqrOpwkJ8blb4KqA4A8O5LWM4M1P2r45Ev1b1uM2WrqbmY527cZWOlRGodQFAwGrjv0NeUVTVwGqiWcDC5dJ0LvvVzRTzJW6zhTvkvKK8f+UbXIn4pKrV98/LqbCWgoWYUg6NX54vFF7duk2V0rnNlj82t3YN9bNm92SMF/Bem13WG3C00ipBZmgmHYdpcX8vmc99MHm1q6mZNXmAj9xMqX56Xtxy3hj8fKmvYCs+jNb9Rku1aoysN9SUc2bLJ1so8OMNJvWmZbBkd4rTPRnrnoztbWjcZLG2OuuMKBzztAYAPrs6wRfPz7/3GA/loF2CzNAsYMmmxRuWLRTKBo7x2VtQnO6Y9QYA6Isvemlv9fqeZIJ/hjMzvIf9+gOZNE8s3GaL6l7+cvP3S0u5ewLfvw73RtWmMM7oMQGr7eC2NllvODR+ed+5oRf/8yE+24KCLJ7/hU2Pc2s1TJAZmnkdnM9ez2UB4PT1a6pM5f0r3wDAdD7HozvLfE9OxXlRs8frd5ikk1NxANgub+hwbzTqdA6ThLdfj4+N8oX72+1PmG8X56qHaGJEsvN98Rizx6jTvbez467FecBqezvQbtTp/DX2D8ZGr2W/9VYXJcix7GLOFMnODyem2DxY5RomyAzNpIPDxFxhAQBOp27gtCY+N8vcRiqX8xY/nupJJrzRMB/c7vaotmfa3Z79uSwv2g+MhHfUupgu2YYhftHirpQW5wz+fODtr7/019iZvt1my1/an77zhHv9W5kIbEYTD9OcdD6ncicfxsZVEtQ2QWZoFrDwAmKLDEcWADh9/Ro7SKKyhWdIB0bCR6NLPrWOpqeZx+K80n8CF7p4pIj1xaQKC7/5oq/sVPG5WXxRjEPhC6WdjLyi/OHMoKqzJ5nAURu0TpAZ2khHtZXMF9mJ+GIpfjp1gx18qyzmoThDOjASfmPw82H0hAgA0vnc4YvnQ6d6Vfl1qrAQOtV7+OJ5Pj6vKEej4dCpXpw5pZcoiO5KJDtfdv6f9H58Blk4WygAwEAm3XXyE/y9jOHEFHuAVTo/3qQArRNkxiMPxv/WzpLfWDZbce/48bR9ODOzVCUPxY9LV7JZcB95QF4wrdx3ipdp+VOPLtbkmifIjIf9BdOKIOhwrpEdZAxJpwL4cV3RYzINLcGQdNY6st6AX/X6dA0kyIwHJNd5gEkVFnAprskLgWUh6VQAa/PfXVDAIgQh6RCCkHQIQUg6hCAkHUIQkg4hCEmHEISkQwhC0iEEIekQgpB0CEFIOoQgJB1CEJIOIQhJhxCEpEMIQtIhBCHpEIKQdAhBSDqEICQdQpD/Ac/ZOj8ydmZZAAAAAElFTkSuQmCC';
				html2canvas(document.getElementById(element_id), {
					onrendered: function (canvas) {
						var data = canvas.toDataURL();
						var patient_name = $(".patient_name_container").find('.pull-left').find('h4').html();
						/*
							var docDefinition = {
								header : "Medeasy",
								footer: "Power by Medeasy",
								background: "MedEasy",
								content: [{
									image: data,
									width: 500,
								}]
							};
							pdfMake.createPdf(docDefinition).download(element_id + "_" + patient_name + ".pdf");
						*/
						var docDefinition = {
							pageSize: 'A4', // LETTER | A4 | A5 | A6 ...
							pageOrientation: 'landscape', // portrait or landscape
							pageMargins: [80, 160, 80, 50], // [←, ↑, →, ↓] or [←→, ↑↓]
							header: {
								columns: [
									{
										image: imgBase64,
										width: 100,
										margin: [80, 60]
									},
									{
										margin: [120, 60, 60, 0],
										text: $scope.current_patient.name,
										alignment: 'center',
									}
								]
							},
							footer: {stack: [{text: 'Powered by MedSign', margin: [80, 0]}]},
							/* content: [
								{
									text: 'Your information',
									style: 'title'
								},
								{
									text: 'Name: Sagar', style: 'data'
								},
								{
									text: 'Email: sagar Email', style: 'data'
								},
								{
									text: 'Your comment',
									style: 'title'
								},
								{
									text: 'comments', style: 'data'
								}
							], 
							styles: {
								title: {
									fontSize: 22,
									bold: true,
									margin: [0, 20]
								},
								data: {
									margin: [0, 5]
								}
							}
							*/
							content: [{
								image: data,
								width: 800,
							}]
						};
						pdfMake.createPdf(docDefinition).download(element_id + "_" + patient_name + ".pdf");
					}
				});
			}

			$scope.resetPphad();
			/* $scope.addMorePphad = function () {
                $scope.patientPreviousHealthAnalyticData.push({healthAnalysis: {name:'', value:'', date:healthAnalysisDefaultDate}, isOpen: false});
                $("#modal_patient_previous_health_analytic").modal('handleUpdate');
                $("#modal_patient_previous_health_analytic .modal-backdrop.in").height($("#modal_patient_previous_health_analytic .modal-backdrop.in").height() + 200);
            } */
			$scope.removeLastPphadObj = function (key) {
                if ($scope.patientPreviousHealthAnalyticData[key].health_analytics_report_id) {
                    $scope.patientPreviousHealthAnalyticData[key].health_analytics_report_status = 9;
                    $scope.pphad_deleted_obj.push($scope.patientPreviousHealthAnalyticData[key]);
                }
                $scope.patientPreviousHealthAnalyticData.splice(key, 1);
            }
			$scope.getAllHealthTest = function(){
				if($rootScope.healthAnalysisTest == undefined || $rootScope.healthAnalysisTest.length == 0){
					var request = {resourceType: 'all'};
					PatientService.getAllHealthTest(request, true, function (response) {
						$rootScope.healthAnalysisTest = response.data;
					});
				}
				var request = {
					appointment_id: $scope.current_appointment_date_obj.appointment_id,
					patient_id: 	$scope.current_patient.user_id,
					clinic_id: 		$rootScope.current_clinic.clinic_id,
					doctor_id: 		($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                    flag: 			1,
					patient_previous_health_analytic: true
				};
                PatientService.getTableGraphDataHealthAnalytics(request, function (response) {
					//remove all the previous entry
					$scope.patientPreviousHealthAnalyticData = [];
					if (response.data) {
						angular.forEach(response.data, function (value, key) {
							var objpha = {healthAnalysis:{}, isOpen:true};
							objpha.health_analytics_report_id = value.health_analytics_report_id;
							if(value.health_analytics_report_date != undefined && value.health_analytics_report_date != ''){
								objpha.healthAnalysis.date = new Date(value.health_analytics_report_date);
								//objpha.healthAnalysis.date = $filter('date')(value.health_analytics_report_date, "dd/MM/y");
							}
							var objRds = JSON.parse(value.health_analytics_report_data);
							objpha.health_analytics_report_status = 1;
							if(objRds != undefined && objRds.length > 0){
								angular.forEach(objRds, function (rdsV, rdsK) {
									if(rdsV.patient_previous_health_analytic != undefined && rdsV.patient_previous_health_analytic == true){
										objpha.healthAnalysis.value = rdsV.value;
										objpha.healthAnalysis.name  = rdsV.name;
										var selectedHADetails = $filter('filter')($rootScope.healthAnalysisTest, {'health_analytics_test_id':rdsV.id},true);
										if(selectedHADetails != undefined && selectedHADetails.length > 0){
											objpha.search = selectedHADetails[0].health_analytics_test_name;
											objpha.selectedHadv = selectedHADetails[0];
											$scope.patientPreviousHealthAnalyticData.push(objpha);
										}
									}
								});
							}
						});
					}
					if($scope.patientPreviousHealthAnalyticData.length > 0){
						$scope.patientPreviousHealthAnalyticData.push({search: ''});
					}else{
						$scope.resetPphad();
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
			}
			$scope.setObjForMppha = function(listArr,objList){
				if(objList.length > 0){
					angular.forEach(objList, function (pphaval, pphakey) {
						
						if(pphaval.healthAnalysis != undefined && pphaval.selectedHadv != undefined && pphaval.healthAnalysis.date != undefined && pphaval.selectedHadv.health_analytics_test_id != undefined){
							var month = pphaval.healthAnalysis.date.getMonth() + 1;
							var day   = pphaval.healthAnalysis.date.getDate();
							var year  = pphaval.healthAnalysis.date.getFullYear();
							var health_analytics_report_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
							var objHa = {  'health_analytics_report_date': health_analytics_report_date,
											'id': pphaval.selectedHadv.health_analytics_test_id,
											'name': pphaval.selectedHadv.health_analytics_test_name,
											'precise_name': pphaval.selectedHadv.health_analytics_test_name_precise,
											'doctor_id': ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
											'value': pphaval.healthAnalysis.value,
											'patient_previous_health_analytic': true 
										   };
							
							if(pphaval.health_analytics_report_id != undefined)
								objHa.health_analytics_report_id = pphaval.health_analytics_report_id;
							
							if(pphaval.health_analytics_report_status != undefined)
								objHa.health_analytics_report_status = pphaval.health_analytics_report_status;
							
							listArr.push(objHa);
						}
					});
				}
			}
			$scope.managePatientPreviousHealthAnalytic = function(form){
				$scope.pphadsubmitted = true;
				if (form.$valid) {
					var pphaddata = [];
					if($scope.patientPreviousHealthAnalyticData.length > 0){
						$scope.setObjForMppha(pphaddata, $scope.patientPreviousHealthAnalyticData);
						if($scope.pphad_deleted_obj.length > 0)
							$scope.setObjForMppha(pphaddata, $scope.pphad_deleted_obj);
					}
					
					if(pphaddata.length > 0){
						var request = {
							appointment_id: $scope.current_appointment_date_obj.appointment_id,
							patient_id: 	$scope.current_patient.user_id,
							clinic_id: 		$rootScope.current_clinic.clinic_id,
							doctor_id: 		($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
							health_analytics_data: JSON.stringify(pphaddata)
						};
						PatientService.managePatientPreviousHealthAnalytic(request, function (response) {
							if (response.status) {
								ngToast.success({
									content: response.message
								});
								$scope.resetPphad();
								$scope.getTableDataHealthAnalytics(true);
								$("#modal_patient_previous_health_analytic").modal("hide");
							} else {
								ngToast.danger({
									content: response.message
								});
							}
						}, function (error) {
							$rootScope.handleError(error);
						});
					}
                }else{
					
				}
			}
			/* Previous patient health analytic code END */        
			$scope.openSelect = function () {
                $("#bgroup_chosen.chosen-container").trigger('mousedown');
            };
            $scope.submitted = '';
            $scope.show = function (event) {
				$scope.$broadcast(event);
            };
            
			$scope.doctor_print_page_setup = {};
			$scope.init = function () {
                $scope.submitted = '';
                $scope.cities = [];
                $scope.search_patient_keyword = '';
			};
            $scope.init();
			
			/* report start */
			$scope.reportTypeList = [];
			$scope.report = {};
            $scope.getReportType = function () {
				PatientService.getReportTypes('', function (response) {
					$scope.reportTypeList = response.data;
				}, function (error) {
					$rootScope.handleError(error);
				});
            }
			$scope.getReportType();
            var report_current_start_date = new Date();
            var year = report_current_start_date.getFullYear();
            var month = report_current_start_date.getMonth();
            var day = report_current_start_date.getDate();
            var report_start_date = new Date(year, month - 12, day);
            var report_end_date = new Date(year, month, day);
            $scope.report_picker = {
                datepickerOptions: {
                    minDate: report_start_date,
                    maxDate: report_end_date,
                },
                open: false
            };
			
			var analytic_report_start_date = new Date(year - 1, month, day);
            var analytic_report_end_date = new Date(year, month, day - 1);
			$scope.analytic_report_picker = {
                datepickerOptions: {
                    minDate: analytic_report_start_date,
                    maxDate: analytic_report_end_date,
                },
                open: false
            };
			
            $scope.cities = [];
            $scope.occupations = [
                {
                    id: 1,
                    text: 'Housewife',
                },
                {
                    id: 2,
                    text: 'Student',
                },
                {
                    id: 3,
                    text: 'Professional- Healthcare/IT/Banking/Service industry',
                },
                {
                    id: 4,
                    text: 'Business',
                },
                {
                    id: 5,
                    text: 'Service- Govt/Pvt/Public sector',
                },
                {
                    id: 6,
                    text: 'Retired',
                },
                {
                    id: 7,
                    text: 'Agriculture',
                },
                {
                    id: 8,
                    text: 'Labour- Construction/Transportation/Manufacturing',
                },
                {
                    id: 9,
                    text: 'Other',
                }
            ];
            /* patient module initial values */
            $scope.testChief = [{}];
            $scope.testObservation = [{}];
            $scope.testDiagnosis = [{}];
            $scope.testNotes = [{}];
            $scope.testLabs = [{}];
            $scope.testKCO = [{}];
            $scope.brandList = [{
                    brand_name: '',
                    isOpen: false,
                    drug_duration: '1',
                    default1: '',
                    default2: '',
                    default3: '',
            }];
            $scope.patientFilter = 3;
            $scope.patientList = [];
            $scope.getCity = function (state_id) {
                CommonService.getCity(state_id, false, function (response) {
                    if (response.status == true) {
                        $scope.cities = response.data;
                    } else {
                        ngToast.danger(response.message);
                    }
                });
            }
            $scope.getState = function (country) {
                if (!$rootScope.states || $rootScope.states.length <= 0) {
                    CommonService.getState(country, false, function (response) {
                        if (response.status == true) {
                            $rootScope.states = response.data;
                        } else {
                            ngToast.danger(response.message);
                        }
                    });
                }
            }
            $scope.getState('');
            $scope.blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            if (!$rootScope.languages || $rootScope.languages.length <= 0) {
                CommonService.getLanguages('', function (response) {
                    if (response.status == true) {
                        $rootScope.languages = response.data;
                    }
                });
            }
            $scope.getFile = function () {
                fileReader.readAsDataUrl($scope.file, $scope)
                        .then(function (result) {
                            $scope.patient.patient_img_temp = result;
                        });
            };
            $scope.getFileReport = function (fileObj, key, file_extension) {
                var is_pdf = 2;
                if (file_extension == 'pdf') {
                    is_pdf = 1;
                }
                fileReader.readAsDataUrl(fileObj, $scope, is_pdf)
                        .then(function (result) {
                            if (is_pdf == 1) {
                            	if($scope.reportObj[key].temp_img != undefined)
                                	$scope.reportObj[key].temp_img = "app/images/pdf_icon.png";
                                if($scope.rxUploadObj[key] != undefined && $scope.rxUploadObj[key].temp_img != undefined)
                                	$scope.rxUploadObj[key].temp_img = "app/images/pdf_icon.png";
                            } else {
                            	if($scope.reportObj[key].temp_img != undefined)
                                	$scope.reportObj[key].temp_img = result;
                                if($scope.rxUploadObj[key] != undefined && $scope.rxUploadObj[key].temp_img != undefined)
                                	$scope.rxUploadObj[key].temp_img = result;
                            }
                            if($scope.reportObj[key].img != undefined)
                            	$scope.reportObj[key].img = fileObj;
                            if($scope.rxUploadObj[key] != undefined && $scope.rxUploadObj[key].img != undefined)
                            	$scope.rxUploadObj[key].img = fileObj;
                            $scope.prescriptionOnchangeFlag();
                        });
            };
            $scope.openPatientFile = function (fileObj) {
                setTimeout(function () {
                    document.getElementById(fileObj).click();
                }, 0);
            }

            /* main patient module start */
            $scope.is_from_template = false;
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
            ];
            $scope.brandIntakeList = [{
                    id: '1',
                    name: 'Before Food',
                }, {
                    id: '2',
                    name: 'After Food',
                }, {
                    id: '3',
                    name: 'Along with Food',
                }, {
                    id: '4',
                    name: 'Empty Stomach',
                }, {
                    id: '5',
                    name: 'As Directed',
                }
            ]; */
            $scope.patient_report_tab = 'app/views/patient/patient_vital.html?'+$rootScope.getVer(2);
            $scope.tab = {};
            $scope.tab.rx = 1;
            $scope.tab.ix = 1;
            $scope.tab.proc = 3;
            $scope.vital = {};
            $scope.vital.pulse = '';
            $scope.vital_bloodpressure_predefine = [
                {
                    id: 1,
                    name: 'Sitting',
                },
                {
                    id: 2,
                    name: 'Standing',
                },
                {
                    id: 3,
                    name: 'Lying',
                }
            ];
            $scope.tempearture_taken = [
                {
                    id: 1,
                    name: 'Axillary (Armpit)'
                },
                {
                    id: 2,
                    name: 'Oral (Mouth)'
                },
                {
                    id: 3,
                    name: 'Tympanic (Ear)'
                },
                {
                    id: 4,
                    name: 'Temporal (Forehead)'
                },
                {
                    id: 5,
                    name: 'Rectal (Anus)'
                },
                {
                    id: 6,
                    name: 'Digital'
                }
            ];
            $scope.vital.teperature_taken_id = 6;
            $scope.procedureObj = [{
                    text: ''
                }];
            $scope.procedure = {};
            $scope.procedure.note = '';
            $scope.appointment_dates = [];
            $scope.patient_report_tab_index = 1;
            $scope.common = {};
            $scope.common.isOpenForEdit = false;
            $scope.common.isClinicalForEdit = false;
            $scope.common.isRXFormOpen = false;
            $scope.common.isProcOpen = false;
            $scope.common.isInvestigationOpen = false;
            $scope.addedBrandList = [];
            $scope.addedProcedureObj = [];
            $scope.addedProcedure = {};
            $scope.addedProcedure.note = '';
            $scope.addedInvestigationObj = [];
            $scope.addedInvestigation = {};
            $scope.addedInvestigation.note = '';
            $scope.reportObj = [{
                    report_name: '',
                    report_type: '',
                    report_date: '',
                    temp_img: '',
                    img: ''
                }];
            $scope.addedReportObj = [];
            $scope.common.next_folloup_date = '';
            $scope.common.drug_instruction = '';
            $scope.common.is_capture_compliance = true;
			var next_appointment_maxdate = new Date();
            var year = next_appointment_maxdate.getFullYear();
            var month = next_appointment_maxdate.getMonth();
            var day = next_appointment_maxdate.getDate();
            var next_appointment_maxdate = new Date(year, month + 6, day)

            var current_date = new Date();
            current_date.setDate(current_date.getDate() + 1)
            //current_date.setHours(00);
            //current_date.setMinutes(00);
            //current_date.setSeconds(00);
            $scope.next_appointment_picker = {
                datepickerOptions: {
                    minDate: current_date,
                    maxDate: next_appointment_maxdate,
                    //startingDay: 1,
                },
				timepickerOptions: {
                    readonlyInput: false,
                    showMeridian: false,
                    minuteStep: 5
                },
				open: false
            };
            $scope.patientTabSetPageNumber = function (index) {
            	if(index == 1) {
                	$rootScope.page_number = $rootScope.VITAL_MODULE;
                	$rootScope.page_name = 'Vitals'; 
                }
                else if(index == 2) {
                	$rootScope.page_number = $rootScope.CLINICAL_NOTES_MODULE;
                	$rootScope.page_name = 'Clinical Notes';
                }
                else if(index == 3) {
                	$rootScope.page_number = $rootScope.RX_MODULE;
                	$rootScope.page_name = 'Rx';
                }
                else if(index == 4) {
                	$rootScope.page_number = $rootScope.INVESTIGATION_MODULE;
                	$rootScope.page_name = 'Investigations';
                }
                else if(index == 5) {
                	$rootScope.page_number = $rootScope.PROC_MODULE;
                	$rootScope.page_name = 'Procedure';
                }
                else if(index == 6) {
                	$rootScope.page_number = $rootScope.REPORT_MODULE;
                	$rootScope.page_name = 'Reports';
                }
                else if(index == 7) {
                	$rootScope.page_number = $rootScope.ANALYTICS_MODULE;
                	$rootScope.page_name = 'Analytics Values';
                }
            }
            $scope.changeAppointmentInvoiceTab = function (tab, e) {
            	if(tab==2) {
	            	$scope.checkPatientDataSaveAlert(e,function(st){
	            		if(st==true) {
			            	$scope.patient_report_main_tab = tab; 
			            	if(tab == 1) {
			            		$scope.getPatientReportDetail();
			            	} else {
			            		$scope.getTaxSetting();
			            		$scope.searchTreatment('', true);
			            		$scope.getInvoiceList();
			            	}
			            }
	            	});
	            } else {
	            	$scope.patient_report_main_tab = tab;
	            	$scope.getPatientReportDetail();
	            }
            }
            /* patient report change code (vital,notes,rx,etc) */
            $scope.changePatientReportTab = function (tab, index, e) {
            	$scope.patientTabSetPageNumber(index);
                if ($scope.patient_report_tab_index == index) {
                    return;
                }
                $scope.checkPatientDataSaveAlert(e,function(st){
                	if(st==true){
		                $rootScope.autoCompleteMinLength = 1;
		                if(index == 3) {
		                	$rootScope.autoCompleteMinLength = 0;
		                }
		                $rootScope.gTrack(tab);
		                $scope.submitted = false;
		                $scope.patient_report_tab = 'app/views/patient/' + tab + '.html?'+$rootScope.getVer(2);
		                $scope.patient_report_tab_index = index;
		                $scope.common.isOpenForEdit = false;
		                $scope.common.isClinicalForEdit = false;
		                $scope.common.isRXFormOpen = false;
		                $scope.common.isProcOpen = false;
		                $scope.common.isInvestigationOpen = false;
		                $scope.getPatientReportDetail();
		                //get data for graph/table
		                if (index == 1) {
		                    if ($scope.current_patient.user_id) {
		                        $scope.getTableData();
		                    }
		                } else if (index == 2) {
		                    if ($scope.current_patient.user_id) {
		                        $scope.getTableDataForNotes();
		                    }
		                }
		            }
                });
            }
            
            $scope.checkPatientDataSaveAlert = function(e,fnCallback){
            	if($rootScope.currentUser.isHidepatientDataSaveAlert != undefined && $rootScope.currentUser.isHidepatientDataSaveAlert){
            		fnCallback(true);
            	} else {
	                if($scope.checkPatientDataForm()) { // If return true then show Alert
	                	$scope.patientDataSaveAlert(fnCallback,e,'');
		            } else {
		                fnCallback(true);
		            }
		        }
            }
            $scope.prescriptionOnchangeFlag = function(){
            	$scope.is_change_prescription = true;
            }
            $scope.checkPatientDataForm = function(){
            	var isShowSaveAlert = false;
            	if($scope.patient_report_tab_index == 1) {
                	if($scope.vital.id == undefined && (
                		($scope.vital.weight != undefined && $scope.vital.weight != "") || 
                		($scope.vital.pulse != undefined && $scope.vital.pulse != "") || 
                		($scope.vital.resp != undefined && $scope.vital.resp != "") || 
                		($scope.vital.spo != undefined && $scope.vital.spo != "") || 
                		($scope.vital.systolic != undefined && $scope.vital.systolic != "") || 
                		($scope.vital.diastolic != undefined && $scope.vital.diastolic != "") || 
                		($scope.vital.temp != undefined && $scope.vital.temp != "")
                		)) {
                		isShowSaveAlert = true;
                	} else if($scope.vital.id != undefined && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 2) {
					if($scope.clinical_notes_reports_id == undefined && (
						($scope.testKCO[0].search != undefined && $scope.testKCO[0].search != "") || 
						($scope.testChief[0].search != undefined && $scope.testChief[0].search != "") || 
						($scope.testObservation[0].search != undefined && $scope.testObservation[0].search != "") || 
						($scope.testDiagnosis[0].search != undefined && $scope.testDiagnosis[0].search != "") || 
						($scope.testNotes[0].search != undefined && $scope.testNotes[0].search != "")
						)) {
						isShowSaveAlert = true;
					} else if($scope.clinical_notes_reports_id != undefined && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 3) {
                	if(($scope.isEditRxData != undefined && !$scope.isEditRxData) && 
                		($scope.brandList[0].similar_brand != undefined && $scope.brandList[0].similar_brand != "" && $scope.brandList[0].drug_name_with_unit != undefined && $scope.brandList[0].drug_name_with_unit != "")
                		){
                		isShowSaveAlert = true;
                	} else if($scope.isEditRxData != undefined && $scope.isEditRxData && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 4) {
                	if($scope.lab_report_id == undefined && $scope.testLabs[0].search != undefined && $scope.testLabs[0].search != ""){
                		isShowSaveAlert = true;
                	} else if($scope.lab_report_id != undefined && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 5) {
                	if($scope.procedure_report_id == undefined && $scope.procedureObj[0].text != undefined && $scope.procedureObj[0].text != "") {
                		isShowSaveAlert = true;
                	} else if($scope.procedure_report_id != undefined && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 6) {
                	/*if($scope.isEditReportData == undefined && 
                		(($scope.reportObj[0].report_name != undefined && $scope.reportObj[0].report_name != "") || ($scope.reportObj[0].report_date != undefined && $scope.reportObj[0].report_date != "") || ($scope.reportObj[0].report_type != undefined && $scope.reportObj[0].report_type != "") || ($scope.reportObj[0].temp_img != undefined && $scope.reportObj[0].temp_img != ""))
                		) {*/
                	if(($scope.reportObj[0].report_name != undefined && $scope.reportObj[0].report_name != "") || ($scope.reportObj[0].report_date != undefined && $scope.reportObj[0].report_date != "") || ($scope.reportObj[0].report_type != undefined && $scope.reportObj[0].report_type != "") || ($scope.reportObj[0].temp_img != undefined && $scope.reportObj[0].temp_img != "")) {
                		isShowSaveAlert = true;
                	}
                } else if($scope.patient_report_tab_index == 7) {
                	angular.forEach($scope.health.healthAnalyticsObj, function(val, key){
                		if($scope.isEditAnalyticData == undefined && val.value != undefined && val.value != ""){
                			isShowSaveAlert = true;
                		} else if($scope.isEditAnalyticData != undefined && $scope.is_change_prescription != undefined && $scope.is_change_prescription == true) {
	                		isShowSaveAlert = true;
	                	}
                	});
                } else if($scope.patient_report_tab_index == 8) {
                	/*if($scope.isEditRxUploadData == undefined && 
                		($scope.rxUploadObj[0].temp_img != undefined && $scope.rxUploadObj[0].temp_img != "")
                		) {*/
                	if(($scope.rxUploadObj[0].temp_img != undefined && $scope.rxUploadObj[0].temp_img != "")
                		) {
                		isShowSaveAlert = true;
                	}
                }
                return isShowSaveAlert;
            }
            $scope.patientDataSaveAlert = function (fnCallback,event,next) {
            	var htmlText = '<div class="common_checbox checkbox_width patient-data-save-check">'
                                    +'<input id="patientDataSaveCheck" type="checkbox">'
                                    +'<label for="patientDataSaveCheck" class="m_bottom_20">Please do not show this message again</label>'
                                +'</div>';
            	SweetAlert.swal(
                    {
                        title: "Please save before leaving this section",
                        text: htmlText,
                        html:true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        cancelButtonText: "Cancel",
                        closeOnConfirm: true,
                    },
                    function (isConfirm) {
                    	var checkEvent = document.getElementById('patientDataSaveCheck');
                    	if(checkEvent.checked){
                    		$localStorage.currentUser.isHidepatientDataSaveAlert = true;
                    	}
                    	if(!isConfirm) {
                    		if(next != undefined && next != '') {
	                    		var splittedUrlArray = next.split("#/");
	                    		if(splittedUrlArray != undefined && splittedUrlArray[1] != undefined) {
	                    			var routeString = splittedUrlArray[1];
	                    			var routeName = routeString.replaceAll('/','.');
	                    			$state.go(routeName);
	                    		}
	                    	} else {
	                    		fnCallback(true);
	                    	}
	                    	$scope.is_change_prescription = false;
                    	} else {
                    		if(fnCallback != '') {
	                    		fnCallback(false);
	                    		event.preventDefault();
	                    		$(".ptDataTabUl li").removeClass("active");
			                	$(".pt-data-tab-"+$scope.patient_report_tab_index).addClass("active");
			                	$(".appointment_title").removeClass("active");
			                	$(".appointment_tab_"+$scope.patient_report_main_tab).addClass("active");
			                }
                    	}
                    }
                );
            }
   //          $(document).click(function(event) {
   //          	if($(event.target).hasClass("sweet-alert") || $(event.target).closest(".sweet-alert").hasClass("sweet-alert")){
            		
   //          	} else {
   //          		$scope.last_click_alert_event = event.target;
   //          	}
			// });
            $scope.loadPatientHealthCalculator = function (objHeathCalc) {
            	$scope.objCurrentCalculator = objHeathCalc;
            	// console.log($scope.objCurrentCalculator);
            	if($scope.objCurrentCalculator != undefined && $scope.objCurrentCalculator.health_analytics_test_validation != undefined && $scope.objCurrentCalculator.health_analytics_test_validation.valueCalcName != undefined){
            		var objCurrentCalculatorView = $scope.objCurrentCalculator.health_analytics_test_validation.valueCalcName;
            	}else{
					var objCurrentCalculatorView = 'modal_scorad_calc';
            	}
            	$scope.objCurrentCalculatorView = 'app/views/patient/calc/' + objCurrentCalculatorView + '.html?'+$rootScope.getVer(2);
            }
            $scope.saveUAS7Data = function (objHeathCalc) {
            	var request = {
					doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    patient_id: $scope.current_patient.user_id,
					appointment_id: $scope.current_appointment_date_obj.appointment_id,
					device_type: 'web',
                    user_id: $rootScope.currentUser.user_id,
                    access_token: $rootScope.currentUser.access_token
				};
				PatientService.saveUAS7Data(request, function (response) {
					if(response.status == true) {
						ngToast.success({
                            content: response.message
                        });
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
            }

            /* get patient list based on filter */
            $scope.currentPage = 0;
            $scope.patient_total_rec = 0;
            $scope.changePatientListTab = function (filterType, isFromLink) {
            	if($scope.isVideoCall || $scope.isCallGoingOn) {
					var result = confirm("You are leaving patient section. This will end teleconsultation.");
					if(result) {
						$scope.endVideoCall();
						$scope.getPatientList(filterType, isFromLink);
					}
				} else {
	            	$scope.getPatientList(filterType, isFromLink);
				}
            }
            $scope.getPatientList = function (filterType, isFromLink) {
                if (filterType) {
                    $scope.patientFilter = filterType;
                    $scope.currentPage = 0;
                    $scope.patientList = [];
                    $scope.patient_total_rec = 0;
                }
                if ($scope.patientList.length < $scope.patient_total_rec || $scope.currentPage == 0) {
                    $scope.currentPage = Number($scope.currentPage) + 1;
                    var request = {
                        filter: $scope.patientFilter,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        page: $scope.currentPage,
                        per_page: 20,
                        doctor_id: $rootScope.current_doctor.user_id
                    };
                    PatientService
                            .getPatientFilterList(request, function (response) {

                                if (response.data.patient_data) {
                                    if (response.data.patient_data.length > 0) {
                                        if (isFromLink) {
                                            $scope.patientList = (response.data.patient_data);
                                        } else {
                                            $scope.patientList = $scope.patientList.concat(response.data.patient_data);
                                        }
                                        $scope.patient_total_rec = response.data.no_of_records;
                                        if ($scope.currentPage == 1 && $scope.patientList.length > 0 && !$rootScope.patient_obj && !$rootScope.current_dashboard_patient_obj) {
                                            $scope.getDoctorPatientDetail($scope.patientList[0]);
                                        } else {
                                            if ($rootScope.patient_obj) {
                                                $scope.getDoctorPatientDetail($rootScope.patient_obj);
                                            } else if ($rootScope.current_dashboard_patient_obj) {
                                                $scope.getDoctorPatientDetail($rootScope.current_dashboard_patient_obj);
                                            }
                                        }
                                        angular
                                                .forEach($scope.patientList, function (value, key) {
                                                    value.name = $filter('trimString')(value.user_first_name + " " + value.user_last_name, '15');
//                                                    value.name = value.user_first_name + " " + value.user_last_name;
                                                });
                                    } else if ($scope.currentPage == 1) {
                                        $scope.current_patient = {};
                                    }
                                } else if ($scope.currentPage == 1) {
                                    $scope.current_patient = {};
                                }
                                $scope.share_record_setting = response.share_setting;
                                if($scope.share_record_setting != undefined) {
                                	$scope.pdf_url_data.with_vitalsign = ($scope.share_record_setting[1] !=undefined && $scope.share_record_setting[1] == "1") ? true : false;
                                	$scope.pdf_url_data.with_clinicnote = ($scope.share_record_setting[2] !=undefined && $scope.share_record_setting[2] == "1") ? true : false;
                                	$scope.pdf_url_data.with_prescription = ($scope.share_record_setting[3] !=undefined && $scope.share_record_setting[3] == "1") ? true : false;
                                	$scope.pdf_url_data.with_patient_lab_orders = ($scope.share_record_setting[4] !=undefined && $scope.share_record_setting[4] == "1") ? true : false;
                                	$scope.pdf_url_data.with_procedure = ($scope.share_record_setting[5] !=undefined && $scope.share_record_setting[5] == "1") ? true : false;
                                	$scope.pdf_url_data.with_only_diagnosis = ($scope.share_record_setting[8] !=undefined && $scope.share_record_setting[8] == "1") ? true : false;
                                	$scope.pdf_url_data.with_generic = ($scope.share_record_setting[9] !=undefined && $scope.share_record_setting[9] == "1") ? true : false;
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.changeCurrentClinic = function (clinic) {
            	if($scope.isVideoCall || $scope.isCallGoingOn) {
					var result = confirm("You are leaving patient section. This will end teleconsultation.");
					if(result) {
						$scope.endVideoCall();
						$rootScope.current_clinic = clinic;
		                $scope.patientList = [];
		                $scope.currentPage = 0;
		                $scope.patient_total_rec = 0;
		                $scope.appointment.clinic_id = clinic.clinic_id;
		                $scope.doctor_print_page_setup = {};
		                $scope.get_pdf_print_user_setting = false;
		                $scope.getPatientList();
		                $scope.getTimeslot();
					}
				} else {
	            	$rootScope.current_clinic = clinic;
	                $scope.patientList = [];
	                $scope.currentPage = 0;
	                $scope.patient_total_rec = 0;
	                $scope.appointment.clinic_id = clinic.clinic_id;
	                $scope.doctor_print_page_setup = {};
	                $scope.get_pdf_print_user_setting = false;
	                $scope.getPatientList();
	                $scope.getTimeslot();
				}
				$scope.changeClinicCalendarUpdate(clinic);
            }
            /* patient detail */
            $scope.changePatientDetails = function (patient_obj,e) {
            	$scope.checkPatientDataSaveAlert(e,function(st){
            		if(st==true){
		            	if($scope.isVideoCall || $scope.isCallGoingOn) {
							var result = confirm("You are leaving patient section. This will end teleconsultation.");
							if(result) {
								$scope.endVideoCall();
								$scope.getDoctorPatientDetail(patient_obj);
							}
						} else {
			            	$scope.getDoctorPatientDetail(patient_obj);
						}
					}
				});
            }
            $scope.getDoctorPatientDetail = function (patient_obj) {
                $rootScope.patient_obj = '';
                $scope.is_from_template = false;
                patient_obj.doctor_id = $rootScope.current_doctor.user_id;
                PatientService
                        .getPatientDetail(patient_obj, function (response) {
                            $scope.health.data = [];
                            $scope.health.subData = [];
                            $scope.health.real_healthAnalyticsObjWithoutValue = [];
                            $scope.health.healthAnalyticsObj = [];
                            $scope.health.healthAnalyticsObjWithoutValue = [];
                            $scope.current_patient = response.data;
                            $scope.pdf_url_data.language_id = $scope.current_patient.patient_pr_lang_id;
                            $scope.current_patient.name = $filter('trimString')($scope.current_patient.user_first_name + " " + $scope.current_patient.user_last_name, '15');
                            if (!$scope.appointment) {
                                $scope.appointment = {};
                            }
                            $scope.current_patient.medicineAllergies = [];
                            if($scope.current_patient.user_details_medicine_allergies != undefined && $scope.current_patient.user_details_medicine_allergies != "") {
                            	var medicine_allergies = EncryptDecrypt.my_decrypt($scope.current_patient.user_details_medicine_allergies);
                            	$scope.current_patient.medicineAllergies = medicine_allergies.split(",");
                            }
                            $scope.appointment.patient_id = $scope.current_patient.user_id;
                            $scope.current_patient.smoking = '';
                            $scope.current_patient.alcohole == '';
                            if ($scope.current_patient.user_details_smoking_habbit != '' && $scope.current_patient.user_details_smoking_habbit != 0) {
                                $scope.current_patient.smoking = SMOKE[$scope.current_patient.user_details_smoking_habbit - 1].name + ', ';
                            }
                            if ($scope.current_patient.user_details_alcohol) {
                                if ($scope.current_patient.user_details_alcohol != '' && $scope.current_patient.user_details_alcohol != '0') {
                                    $scope.current_patient.alcohole = ALCOHOL[$scope.current_patient.user_details_alcohol - 1].name + ' ';
                                }
                            }

							if($scope.current_patient.user_details_height && $scope.current_patient.user_details_weight)
								$scope.current_patient.bmi = $filter('bmi')($scope.current_patient.user_details_height, $scope.current_patient.user_details_weight) + '';
							
                            if ($scope.current_patient.bmi) {
                                $scope.current_patient.bmi = Math.round($scope.current_patient.bmi);
                                $scope.current_patient.bmi += ' BMI, ';
                            }
                            $scope.current_patient.weight = $filter('PoundToKG')($scope.current_patient.user_details_weight);
                            if ($scope.current_patient.weight) {
                                $scope.current_patient.weight += ' kg, ';
                            }
                            if ($scope.current_patient.appointment_type) {
								var appointmentTypeName = $filter('filter')(APPOINTMENT_TYPE, {id: $scope.current_patient.appointment_type});
								appointmentTypeName = (appointmentTypeName != undefined) ? appointmentTypeName[0].name : APPOINTMENT_TYPE[$scope.current_patient.appointment_type - 1].name;
                                $scope.current_patient.appointment_type = appointmentTypeName;
                            }
                            var kco_array_patient = $scope.current_patient.kco.split('],');
                            var kco_appointments = $scope.current_patient.kco_appointment_id.split(',');
                            var kco_doctors_id = $scope.current_patient.kco_doctor_id.split(',');
                            var kco_string = [];
                            if (kco_array_patient) {
                                angular
                                        .forEach(kco_array_patient, function (value, key) {
                                            value = value.replace('[', '');
                                            value = value.replace(']', '');
                                            value = value.replace(/"/g, '');
                                            if (value) {
                                                var temp_kco = {
                                                    appointment_id: kco_appointments[key],
                                                    doctor_id: kco_doctors_id[key],
                                                    kco: value
                                                };
                                                if (kco_doctors_id[key] == $rootScope.currentUser.user_id) {
                                                    temp_kco.is_editable = true;
                                                    if (!($rootScope.checkPermission($rootScope.CLINICAL_NOTES_MODULE, $rootScope.EDIT))) {
                                                        temp_kco.is_editable = false;
                                                    }
                                                } else {
                                                    temp_kco.is_editable = false;
                                                }
                                                kco_string.push(temp_kco);
                                            }

                                        });
                                if (!($rootScope.checkPermission($rootScope.CLINICAL_NOTES_MODULE, $rootScope.VIEW))) {
                                    kco_string = [];
                                }
                            }
                            $scope.current_patient.clinical_notes_reports_kco = kco_string;
                            /* get past all appointment date with that patient */
                            $scope.date_current_page = 1;
                            $scope.getAppointmentDates();
                            $scope.changePatientReportTab('patient_vital', 1);
							
							/* Clear image comparation objects */
							$scope.imageCompareObj = {'img1':{},'img2':{},'doc1':{},'doc2':{}};
							$scope.objReportsCompareSel = [];
							$scope.allCompareObjDisabled = false;
							$scope.isDocumentCompare = false;
                        }, function (error) {
                            $rootScope.handleError(error);
                        })
            }
            $scope.date_current_page = 1;
            $scope.date_total_records = 0;
            $scope.options = {
                color: '#0073b7',
                alwaysVisible: true,
                railColor: '#0073b7',
                railBorderRadius: 0
            };
            $scope.followup = {};
            $scope.followup.is_selected_follow = '';
            $scope.getAppointmentDates = function (flag, isForFollowup) {
                var temp_page = '';
                if (flag == 'next') {
                    var total_pages = Math.ceil($scope.date_total_records / 6);
                    if (total_pages <= $scope.date_current_page) {
                        return;
                    }
                    temp_page = $scope.date_current_page + 1;
                } else if (flag == 'prev') {
                    temp_page = $scope.date_current_page - 1;
                }
                if (flag) {
                    if (temp_page <= 0) {
                        return;
                    } else {
                        $scope.date_current_page = temp_page;
                    }
                }
                var followup_flag = 2;
                if (isForFollowup) {
                    followup_flag = 1;
                }
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    page: $scope.date_current_page,
                    per_page: 6,
                    user_id: $scope.current_patient.user_id,
                    follow_up_flag: followup_flag,
                    doctor_id: $rootScope.current_doctor.user_id
                };
                PatientService
                        .getAppointmentDates(request, function (response) {
                            if (response.data) {
                                var is_found = false;
                                $scope.date_total_records = response.total_count;
                                var days = ($rootScope.currentUser.sub_plan_setting.prescription_update_days != undefined && parseInt($rootScope.currentUser.sub_plan_setting.prescription_update_days) != 0) ? parseInt($rootScope.currentUser.sub_plan_setting.prescription_update_days) : 1;
                                days = days - 1;
                                angular
                                        .forEach(response.data, function (value, key) {
                                            if (value.appointment_date) {
                                                value.appointment_date_obj = new Date(value.appointment_date);
                                                value.appointment_date_obj.setHours(00);
                                                value.appointment_date_obj.setMinutes(00);
                                                value.appointment_date_obj.setSeconds(00);
                                                var current_date = new Date();
                                                current_date.setHours(00);
                                                current_date.setMinutes(00);
                                                current_date.setSeconds(00);
                                                var today_start_time = current_date.getTime();
									            var year = value.appointment_date_obj.getFullYear();
									            var month = value.appointment_date_obj.getMonth();
									            var day = value.appointment_date_obj.getDate();
									            var end_date = new Date(year, month, day + days);
                                                end_date.setHours(23);
                                                end_date.setMinutes(59);
                                                end_date.setSeconds(59);
                                                var today_end_time = end_date.getTime();
                                                if (today_start_time >= value.appointment_date_obj.getTime() && today_end_time >= today_start_time) {
                                                    value.is_editable = true;
                                                } else {
                                                    value.is_editable = false;
                                                }
                                                if (key == 0 && !isForFollowup && !$rootScope.current_dashboard_patient_obj) {
                                                    value.is_active = true;
                                                    if(value.appointment_is_import == 0) {
								                    	$scope.is_appoinment_import_data = false;
								                    } else {
								                    	$scope.is_appoinment_import_data = true;
								                    }
								                    if(value.is_done == 1)
									                	$scope.is_appoinment_done = 1;
									                else
									                	$scope.is_appoinment_done = 0;
									                var appointmentTypeName = $filter('filter')(APPOINTMENT_TYPE, {id: value.appointment_type});
													value.appointment_type_name = (appointmentTypeName != undefined) ? appointmentTypeName[0].name : $scope.current_patient.appointment_type;
                                                    $scope.current_appointment_date_obj = value;
                                                }
                                                if (key == 0 && isForFollowup == 1) {
                                                    $scope.followup.is_selected_follow = value;
                                                }
                                            }
											if ($rootScope.current_dashboard_patient_obj) {
                                                if (value.appointment_id == $rootScope.current_dashboard_patient_obj.appointment_id) {
                                                    is_found = true;
                                                    value.is_active = true;
                                                    if(value.appointment_is_import == 0) {
								                    	$scope.is_appoinment_import_data = false;
								                    } else {
								                    	$scope.is_appoinment_import_data = true;
								                    }
								                    if(value.is_done == 1)
									                	$scope.is_appoinment_done = 1;
									                else
									                	$scope.is_appoinment_done = 0;
                                                    $scope.current_appointment_date_obj = value;
                                                }
                                            }
											//value.is_editable = true;
                                        });
								if ($rootScope.current_dashboard_patient_obj && is_found == false) {
                                    $scope.getAppointmentDates('next');
                                } else {
                                    $rootScope.current_dashboard_patient_obj = '';
                                }
                                if (!isForFollowup) {
                                    $scope.appointment_dates = response.data;
									if ($scope.patient_report_main_tab == 1) {
                                        $scope.getPatientReportDetail();
                                    } else {
                                        $scope.getInvoiceList();
                                    }
                                } else {
                                    $scope.appointment_followup_dates = response.data;
                                    $scope.followup.is_selected_follow = '';
                                }
                            }
                        }, function (error) {
                            $scope.getAppointmentDates();
                        });
            }
            $scope.template = {};
            $scope.template.template_search = '';
            $scope.template.selected_template = '';
            $scope.template.template_name = '';
            /* get  DS template code */
            $scope.getDSTemplates = function (isFromBtn) {
                if (isFromBtn) {
                    $scope.template.template_search = '';
                }
                SettingService
                        .getTemplate($scope.template.template_search, function (response) {
                            $scope.is_from_template = false;
                            $scope.template_list = response.data;
                            angular
                                    .forEach($scope.template_list, function (value, key) {
                                        if (key == 0) {
                                            $scope.template.selected_template = value.template_id;
                                        }
                                        if (value.template_investigation_name) {
                                            value.template_investigation_name = JSON.parse(value.template_investigation_name).toString();
                                        }
                                    });
                        });
            }

            /* get template all data after selection on DS template */
            $scope.getTemplateDetail = function () {
                //first delete all old data
                SweetAlert.swal({
                    title: "Using this template will replace current prescription.",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes!",
                    closeOnConfirm: false},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                $("#modal_dstemplate").modal("hide");
                                return;
                            }
                            $rootScope.app.isLoader = true;
                            var request = {
                                patient_id: $scope.current_patient.user_id,
                                clinic_id: $rootScope.current_clinic.clinic_id,
                                appointment_id: $scope.current_appointment_date_obj.appointment_id
                            };
                            PatientService
                                    .deleteOldData(request, function (response) {

                                        if (response.status) {
                                            $scope.common.next_folloup_date = '';
                                            $scope.common.drug_instruction = '';
                                            $scope.common.is_capture_compliance = true;
                                            $scope.submitted = false;
                                            $scope.lab_test_error = false;
                                            $scope.lab_test_error_invalid = false;

                                            //SweetAlert.swal("Template loaded successfully!");
                                            $scope.getPatientReportDetail();
                                            SettingService
                                                    .getTemplateDetail($scope.template.selected_template, function (response) {
                                                        $("#modal_dstemplate").modal("hide");
                                                        
                                                        $scope.is_from_template = true;
                                                        $scope.template.template_search = '';
                                                        var temp_obj = response.data;
                                                        $scope.template.template_diagnoses = temp_obj.template_diagnosis_name;
                                                        $scope.template.template_name = temp_obj.template_template_name;
                                                        $scope.template.template_id = $scope.template.selected_template;
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
														
                                                        var keys = [];
														var investigation_array = [];
                                                        var testLabs = [];
														if(temp_obj.template_investigation_name != undefined && temp_obj.template_investigation_name.length > 0){
															investigation_array = JSON.parse(temp_obj.template_investigation_name);
	                                                        for (var k in investigation_array) {
	                                                            keys.push(k);
	                                                        }

                                                        	angular
                                                                .forEach(keys, function (key_value, key_key) {
                                                                    var inst = false;
                                                                    if (investigation_array[key_value]) {
                                                                        inst = true;
                                                                    }
                                                                    testLabs.push({
                                                                        search: key_value,
                                                                        lab_instruction: investigation_array[key_value],
                                                                        isOpenInst: inst
                                                                    });
                                                                });
                                                        }
                                                        if (testLabs.length != 0) {
                                                            testLabs.push({
                                                                search: '',
                                                                lab_instruction: '',
                                                                isOpenInst: false
                                                            });
                                                            $scope.testLabs = testLabs;
                                                        }

                                                        var drug_obj = [];
                                                        angular
                                                                .forEach(temp_obj.drug_data, function (value, key) {
                                                                    var custom_array = value.custom_frequency.split('-');
                                                                    var default1 = '';
                                                                    var default2 = '';
                                                                    var default3 = '';
                                                                    var open_default = false;
                                                                    if (custom_array.length == 3) {
                                                                        default1 = custom_array[0];
                                                                        default2 = custom_array[1];
                                                                        default3 = custom_array[2];
                                                                    }
                                                                    if(value.dosage == '' && (value.drug_unit_name == 'Tablets' || value.drug_unit_name == 'IU')) {
												                    	open_default = true;
												                    }
                                                                    var freq_open = false;
                                                                    if (value.frequency_instruction) {
                                                                        freq_open = true;
                                                                    }
                                                                    var intake_open = false;
                                                                    if (value.intake_instruction) {
                                                                        intake_open = true;
                                                                    }
                                                                    var dosage = '';
                                                                    if(value.drug_unit_name == 'As Directed') {
								                                    	dosage = '';
								                                    } else {
								                                    	dosage = value.dosage;
								                                    }
                                                                    drug_obj.push({
                                                                        similar_brand_id: value.drug_id,
                                                                        similar_brand: value.drug_name,
																		drug_name_with_unit: value.drug_name_with_unit,
                                                                        drug_drug_generic_id: value.generic_id.split(','),
                                                                        dosage: dosage,
                                                                        drug_frequency_id: value.frequency,
                                                                        default1: default1,
                                                                        default2: default2,
                                                                        default3: default3,
                                                                        freq_instruction: value.frequency_instruction,
                                                                        drug_intake: value.intake,
                                                                        intake_instruction: value.intake_instruction,
                                                                        drug_duration_value: value.duration_value,
                                                                        drug_instruction: value.diet_instruction,
                                                                        defaultFreqOpen: open_default,
                                                                        isFreqInst: freq_open,
                                                                        isIntakeInst: intake_open,
                                                                        drug_unit_name: value.drug_unit_name,
                                                                        drug_unit_id: value.drug_unit_id,
                                                                        drug_duration: value.duration,
                                                                        isOpenWholeForm: true
                                                                    });
                                                                });
                                                        if (drug_obj.length != 0) {
                                                            $scope.brandList = drug_obj;
															$scope.brandList.push({});
                                                        }
                                                        swal.close();
                                                    });
                                        }
                                    }, function (error) {
                                        $rootScope.handleError(error);
                                    });
                        });
            }
			
			/******* Sunil Code - Video Call *****/
			
			$scope.isMute = false;
			$scope.isVideoCall = false;
			$scope.isVideoMute = false;
			$scope.isCallGoingOn = false;
			$scope.isPatientNotConnected = false;
			$scope.isPatientDisconnected = false;
			
			var uniqueKey;
			
			// var apiKey = "46722342";
			// var sessionId = "2_MX40NjcyMjM0Mn5-MTU4ODc2NTM3NTU0M35na1BJTHlZUEpvcGIrazB3WG9wUjBMcGF-UH4";
			// var token = "T1==cGFydG5lcl9pZD00NjcyMjM0MiZzaWc9MzliMWI0YTQ3NGE1NTA4YjkxN2JhYTMxNTZlNDNjNGM3MjhiNTU4YTpzZXNzaW9uX2lkPTJfTVg0ME5qY3lNak0wTW41LU1UVTRPRGMyTlRNM05UVTBNMzVuYTFCSlRIbFpVRXB2Y0dJcmF6QjNXRzl3VWpCTWNHRi1VSDQmY3JlYXRlX3RpbWU9MTU4ODc2NTM5OCZub25jZT0wLjAwOTA4ODc5MjkzOTk2NTIyOSZyb2xlPXB1Ymxpc2hlciZleHBpcmVfdGltZT0xNTg5MzcwMTk2JmluaXRpYWxfbGF5b3V0X2NsYXNzX2xpc3Q9"
			
			var session;
			var sessionId;
			var token;
			
			$scope.GenerateURL = function(){
				var request = {
					doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    patient_id: $scope.current_patient.user_id,
					appointment_id: $scope.current_appointment_date_obj.appointment_id
				};
				PatientService.generateURL(request, function (response) {
					if(response.status == true)
					{
						// console.log("Video call URL : ", response.patient_url);
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
			}
			
			$scope.initVideoCall = function(){
				generateToken();
			}
			
			function generateToken(){
				var request = {
					doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    patient_id: $scope.current_patient.user_id,
					appointment_id: $scope.current_appointment_date_obj.appointment_id
				};
				PatientService.getVideoCallToken(request, function (response) {
					if(response.status == true)
					{
						// console.log(response);
						$scope.isCallGoingOn = true;
						$scope.isVideoCall = true;
						
						sessionId = response.data.session_id;
						token = response.data.token_id;
						
						initializeSession();
					} else {
						ngToast.danger(response.message);
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
			}
			
			// Handling all of our errors here by alerting them
			function handleError(error) {
			  if (error) {
				alert(error.message);
			  }
			}
			
			function initializeSession() {
	
			  session = OT.initSession($scope.apiKey, sessionId);
				
				 session.on({
				   connectionCreated: function (event) {
					$scope.update_connection_id(session.connection.connectionId);
					if(session.connections.length() == 1)
					{
						// console.log("Patient is yet to start the teleconsultation. Please wait.");
						$scope.isPatientNotConnected = true;
						$scope.isPatientDisconnected = false;
					}						
					else
					{
						$scope.isPatientNotConnected = false;
					    $scope.isPatientDisconnected = false;
					}
				   },
				   connectionDestroyed: function connectionDestroyedHandler(event) {
					 // console.log("Patient disconnected");
					 $scope.isPatientNotConnected = false;
					 $scope.isPatientDisconnected = true;
				   }
				 });
				
			  // Subscribe to a newly created stream
			  session.on('streamCreated', function(event) {
				  session.subscribe(event.stream, 'subscriber', {
					insertMode: 'append',
					width: '100%',
					height: '100%',
					style: {buttonDisplayMode: 'off'}
				  }, handleError);
				});

				session.on("sessionDisconnected", function(event) {
				    // console.log("The session disconnected. " + event.reason);
				    $scope.isPatientNotConnected = false;
					$scope.isPatientDisconnected = false;
					$scope.isCallGoingOn = false;
					$scope.isVideoCall = false;
					$scope.current_appointment_date_obj.call_end_date_time = new Date();
				});

				session.on("signal", function(event) {
					// console.log("Signal data: " + event.data);
					ngToast.danger(event.data);
				});

			  // Create a publisher
			  var publisher = OT.initPublisher('publisher', {
				insertMode: 'append',
				width: '100%',
				height: '100%',
				style: {buttonDisplayMode: 'off'},
				publishAudio:true, 
				publishVideo:true
			  }, handleError);

			  // Connect to the session
			  session.connect(token, function(error) {
				// If the connection is successful, publish to the session
				if (error) {
				  handleError(error);
				} else {
				  session.publish(publisher, handleError);
				}
			  });
			  
			  $("#btnMute").click(function(){
				  $scope.isMute = true;
				  publisher.publishAudio(false); 
			  });
			  $("#btnUnMute").click(function(){
				  $scope.isMute = false;
				  publisher.publishAudio(true); 
			  });
			  
			  $("#btnDisableVideo").click(function(){
				  $scope.isVideoMute = true;
				  publisher.publishVideo(false); 
			  });
			  
			  $("#btnEnableVideo").click(function(){
				  $scope.isVideoMute = false;
				  publisher.publishVideo(true); 
			  });
			}
			
			$scope.update_connection_id = function(connectionId){
				var request = {
					doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    patient_id: $scope.current_patient.user_id,
					appointment_id: $scope.current_appointment_date_obj.appointment_id,
					connection_id: connectionId
				};
				PatientService.updateConnectionId(request, function (response) {
					if(response.status == true)
					{
						
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
			}

			$scope.endVideoCall = function(){
				var request = {
					doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id,
                    patient_id: $scope.current_patient.user_id,
					appointment_id: $scope.current_appointment_date_obj.appointment_id
				};
				PatientService.endVideoCall(request, function (response) {
					if(response.status == true)
					{
						$scope.isPatientNotConnected = false;
						$scope.isPatientDisconnected = false;
						$scope.isCallGoingOn = false;
						$scope.isVideoCall = false;
						$scope.current_appointment_date_obj.call_end_date_time = new Date();
						session.disconnect();
						ngToast.success({
                            content: response.message
                        });
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
			}
									
			$scope.minimizeVideoCall = function(){
				$scope.isVideoCall = false;
			}
			
			$scope.gotoVideoCall = function(){
				$scope.isVideoCall = true;
			}
			
			$scope.$on('$locationChangeStart', function(event, next) {
				if($scope.checkPatientDataForm() && ($rootScope.currentUser.isHidepatientDataSaveAlert == undefined)){ // If return true then show Alert
					$scope.patientDataSaveAlert('',event, next);
					/*var htmlText = '<div class="common_checbox checkbox_width patient-data-save-check">'
	                                    +'<input id="patientDataSaveCheck" type="checkbox">'
	                                    +'<label for="patientDataSaveCheck" class="m_bottom_20">Please do not show this message again</label>'
	                                +'</div>';
	            	SweetAlert.swal(
	                        {
	                            title: "Please save before leaving this section",
	                            text: htmlText,
	                            html:true,
	                            type: "warning",
	                            showCancelButton: true,
	                            confirmButtonColor: "#DD6B55",
	                            confirmButtonText: "Ok",
	                            cancelButtonText: "Cancel",
	                            closeOnConfirm: true,
	                        },
	                        function (isConfirm) {
	                        	var checkEvent = document.getElementById('patientDataSaveCheck');
	                        	if(checkEvent.checked){
	                        		$localStorage.currentUser.isHidepatientDataSaveAlert = true;
	                        	}
	                        	if(!isConfirm) {
	                        		var splittedUrlArray = next.split("#/");
	                        		if(splittedUrlArray != undefined && splittedUrlArray[1] != undefined) {
	                        			var routeString = splittedUrlArray[1];
	                        			var routeName = routeString.replaceAll('/','.');
	                        			$state.go(routeName);
	                        		}
	                        	} else {
	                        		
	                        	}
	                        }
	                );*/
					event.preventDefault();
				}
				if($scope.isVideoCall || $scope.isCallGoingOn)
				{
					var result = confirm("You are leaving patient section. This will end teleconsultation.");
					if(result)
					{
						$scope.endVideoCall();
					}
					else
					{
						event.preventDefault();  
					}
				}
			});
			
			/******* End Code ******/
			
            $scope.rx_setting_data = [];
            $scope.is_notify_allegy_popup = true;
            $scope.getInitAllDataForBrand = function () {
                if ($scope.brandGenericList.length == 0) {
                    SettingService
                            .getGeneric('', function (response) {
                                $scope.brandGenericList = response.data;
                            });
                }
                if ($scope.brandTypeList.length == 0) {
                	var request = {drug_unit_is_display: 1}
                    SettingService
                            .getBrandType(request, function (response) {
                                $scope.brandTypeList = response.data;
                            });
                }
                if ($scope.brandFreqList.length == 0) {
                    SettingService
                            .getBrandFreq('', function (response) {
                                $scope.brandFreqList = response.data;
                            });
                }
                if ($scope.rx_setting_data.length == 0) {
	                var request = {
	                    setting_type: [6,11],
	                    setting_data_type: 1,
	                    doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id
	                };
	                SettingService
                        .getDoctorSetting(request, function (response) {
                        	$scope.apiKey = response.video_api_key;
                            if (response.status) {
                            	var selectedDataObj = $filter('filter')(response.data, {'setting_type':"6"},true);
                            	if(selectedDataObj != undefined && selectedDataObj[0] != undefined && selectedDataObj[0].setting_data != undefined)
                                	$scope.rx_setting_data = JSON.parse(selectedDataObj[0].setting_data);

                                var selectedDataObj = $filter('filter')(response.data, {'setting_type':"11"},true);
                            	if(selectedDataObj != undefined && selectedDataObj[0] != undefined && selectedDataObj[0].setting_data != undefined){
                                	if(selectedDataObj[0].setting_data == "1") {
                                		$scope.is_notify_allegy_popup = true;
                                	} else {
                                		$scope.is_notify_allegy_popup = false;
                                	}
                            	}
                            }
                            if($scope.rx_setting_data.length == 0) {
                            	$scope.rx_setting_data = [{
                                    status: "1"
                                }];
                                $scope.rx_setting_data.push({
                                    status: "2"
                                });
                            }
                        });
                }
            }
            $scope.closeTooltip = function () {

                for (var i = 0; i < $scope.testChief.length; i++) {
                    $("#testChiefRight_1_" + i).tooltip({
                        html: true,
                        title: '<b>Click</b> on the icon12 to add to local catalog.',
                        placement: "left",
                    }).tooltip("close");
                }
                for (var i = 0; i < $scope.testObservation.length; i++) {
                    $("#testChiefRight_2_" + i).tooltip({
                        html: true,
                        title: '<b>Click</b> on the icon12 to add to local catalog.',
                        placement: "left",
                    }).tooltip("close");
                }
                for (var i = 0; i < $scope.testDiagnosis.length; i++) {
                    $("#testChiefRight_3_" + i).tooltip({
                        html: true,
                        title: '<b>Click</b> on the icon12 to add to local catalog.',
                        placement: "left",
                    }).tooltip("close");
                }
                for (var i = 0; i < $scope.testNotes.length; i++) {
                    $("#testChiefRight_4_" + i).tooltip({
                        html: true,
                        title: '<b>Click</b> on the icon12 to add to local catalog.',
                        placement: "left",
                    }).tooltip("close");
                }
            }
            //search notes and all
            $scope.appendNote = function (key, event, testNotes, type) {
            	$scope.prescriptionOnchangeFlag();
                for (var i = 0; i < testNotes.length; i++) {
                    $("#testChiefRight_" + type + "_" + i).tooltip({
                        html: true,
                        title: '<b>Click</b> on the icon12 to add to local catalog.',
                        placement: "left",
                    }).tooltip("close");
                }

				// for (var i = 0; i < $scope.testChief.length; i++) {
				//     $("#testChiefRight_1_" + i).tooltip({
				//         html: true,
				//         title: '<b>Click</b> on the icon12 to add to local catalog.',
				//         placement: "left",
				//     }).tooltip("close");
				// }
				// for (var i = 0; i < $scope.testObservation.length; i++) {
				//     $("#testChiefRight_2_" + i).tooltip({
				//         html: true,
				//         title: '<b>Click</b> on the icon12 to add to local catalog.',
				//         placement: "left",
				//     }).tooltip("close");
				// }

                $("#testChiefRight_" + type + "_" + key).tooltip({
                    html: true,
                    title: '<b>Click</b> on the icon12 to add to local catalog.',
                    placement: "left",
                }).tooltip("open");
					//$(".ui-tooltip").css("margin-left", "-182px");
					//$(".ui-tooltip").css("margin-top", "-52px");
					
				if($scope.focusIndex != undefined && $scope.focusIndex[type] != undefined){
					$scope.focusIndex[type] = Number(key);
				}else{
					$scope.focusIndex = [];
					$scope.focusIndex[type] = Number(key);
				}
                if (type != 12) {
                    if (event.keyCode == 13 && testNotes[testNotes.length - 1].search != '' && testNotes[testNotes.length - 1].search != undefined) {
                        testNotes.push({});
                        $scope.focusIndex[type] = Number(key) + 1;
                        return;
                    } else {
                        if ((testNotes[key].search == undefined || testNotes[key].search.length <= 0) && key != 0) {
                            testNotes.splice(testNotes.length - 1, 1);
                            $scope.focusIndex[type] = Number(key) - 1;
                            return;
                        }
                    }
                }
                if (type == 12) {
                	var labTestLen = $scope.testLabs.length;
                	if($scope.testLabs[labTestLen-1] != undefined && $scope.testLabs[labTestLen-1].search != undefined && $scope.testLabs[labTestLen-1].search != '') {
	                	$scope.testLabs.push({});
	                }
                    if (!testNotes[key].id) {
                        PatientService
                                .getHealthTest(testNotes[key], true, function (response) {
                                    $scope.search_labs = response.data;
                                    var instructions_data = response.instructions_data;
                                });
                    }
                    return;
                }
                if (type == 13) {
                    SettingService
                            .getKCOTestFromDB(testNotes[key], function (response) {
                                $scope.search_kco = response.data;
                            });
                    return;
                }
                testNotes[key].autoID = '';
                testNotes[key].clinical_notes_type = type;
				if(testNotes[key].search != undefined && type == 3){
                    if(testNotes[key].search.length > 2) {
    					SettingService.getClinicalNotesFromDB(testNotes[key], 1, function (response) {
    						$scope.search_notes = response.data;
    					});
                    }
				} else {
                    SettingService.getClinicalNotesFromDB(testNotes[key], 1, function (response) {
                        $scope.search_notes = response.data;
                    });
                }
            }
			
			$scope.appendHealthTestOnPopUp = function (key, event, testNotes, type) {
				if($scope.focusIndex != undefined && $scope.focusIndex[type] != undefined){
					$scope.focusIndex[type] = Number(key) + 1;
				}else{
					$scope.focusIndex = [];
					$scope.focusIndex[type] = Number(key) + 1;
				}
				if(type == 17){
                    if(!testNotes[key].id){
						if(testNotes[key].search != undefined && testNotes[key].search != ''){
							testNotes[key].health_analytics_test_type = 1;
							PatientService.getHealthTest(testNotes[key], false, function (response) {
								$scope.search_labs = response.data;
							});
						}
                    }
                    return;
                }
			}

            $scope.suggestion_brand_result = [];
			$scope.rxCSectionSearch = '';
			$scope.isFetching = false;
            /* search brand in RX tab */
            $scope.searchSimilarBrand = function (key) {
                $scope.suggestion_brand_result = [];
                $scope.brandList[key].drug_strength = '';
                $scope.brandList[key].drug_duration = '1';
                $scope.brandList[key].drug_duration_value = '';
                $scope.brandList[key].drug_intake = '';
                $scope.brandList[key].drug_instruction = '';
                $scope.brandList[key].drug_drug_generic_id = '';
                $scope.brandList[key].drug_unit_id = '';
                $scope.brandList[key].drug_unit_medicine_type = '';
                $scope.brandList[key].drug_unit_value = '';
                $scope.brandList[key].dosage = '';
                $scope.brandList[key].drug_frequency_id = '';
                $scope.brandList[key].drug_unit_name = '';
                $scope.brandList[key].similar_brand_id = '';
                $scope.brandList[key].drug_name_with_unit = '';
				
				$("#addCustomBrandTooltip").tooltip({
                    html: true,
                    position: {
				        my: "left top-50"
				        // at: "right"
				    }
                }).tooltip("open");
				if(($scope.brandList[key] != undefined && $scope.brandList[key].similar_brand != undefined && $scope.brandList[key].similar_brand.length >= 2) || ($scope.drug_generic_id !='' && ($scope.brandList[key] == undefined || $scope.brandList[key].similar_brand == undefined || $scope.brandList[key].similar_brand.length == 0))){
					if($scope.isFetching == false){
						$scope.isFetching = true;
						var request = {
							brand_name: $scope.brandList[key].similar_brand,
							drug_generic_id: $scope.drug_generic_id
						};
						SettingService
							.searchSimilar(request, function (response) {
								$scope.search_similar_brand_result = [];
								if (response.status == true) {
									$scope.search_similar_brand_result = response.data;
									$("#similarbrand" + key).focus();
									//$scope.suggestion_brand_result = response.related_drugs;
									/* if($("#similarbrand"+key)){
										$("ul.ui-autocomplete").last().attr("style", "display:block !important;")
									} */
								}
								$scope.isFetching = false;
							});
					}
				}
				if($scope.brandList[key] != undefined && $scope.brandList[key].similar_brand != undefined && $scope.brandList[key].similar_brand.length >= 1){
					$scope.rxCSectionSearch = $scope.brandList[key].similar_brand;
				}else{
					$scope.rxCSectionSearch = '';
				}
            }
            /* search generic in RX tab */
            $scope.isSearchGeneric = true;
            $scope.searchSimilarGeneric = function (key) {
            	$scope.drug_generic_id = '';
				if($scope.brandList[key] != undefined && $scope.brandList[key].similar_generic != undefined && $scope.brandList[key].similar_generic.length >= 3) {
					$rootScope.autoCompleteMinLength = 0;
					if($scope.isSearchGeneric){
						$scope.isSearchGeneric = false;
						SettingService
							.searchSimilarGeneric($scope.brandList[key].similar_generic, function (response) {
								if (response.status == true) {
									$scope.search_similar_generic_result = response.data;
								}
								$scope.isSearchGeneric = true;
							});
					}
				}
            }
            $scope.addCustomDrug = function (key) {
            	$scope.brandList[key].isOpenWholeForm = true;
                $scope.brandList[key].drug_strength = '';
                $scope.brandList[key].drug_duration = '1';
                $scope.brandList[key].drug_duration_value = '';
                $scope.brandList[key].drug_intake = '';
                $scope.brandList[key].drug_instruction = '';
                $scope.brandList[key].freq_instruction = '';
                $scope.brandList[key].intake_instruction = '';
                $scope.brandList[key].drug_drug_generic_id = '';
                $scope.brandList[key].drug_unit_id = '';
                $scope.brandList[key].drug_unit_medicine_type = '';
                $scope.brandList[key].drug_unit_value = '';
                $scope.brandList[key].dosage = '0';
                $scope.brandList[key].drug_frequency_id = '';
                $scope.brandList[key].drug_unit_name = '';
                $scope.brandList[key].isOpen = true;
                $scope.brandList[key].similar_brand_id = ''
                $scope.brandList[key].is_new_drug = true; //Free enter drug
				$scope.brandList[key].drug_name_with_unit = $scope.brandList[key].similar_brand;
				$scope.changeCustomValue(key);
				//element.autocomplete("destroy");
				$scope.search_similar_brand_result = []; // Clear the last search result
				// if(!isFromRelatedData)
					$scope.brandList.push({});
				$scope.scrollToNextDrugEntry();
				$scope.prescriptionOnchangeFlag();
				$scope.drug_generic_id = '';
				$scope.search_similar_generic_result = []; // Clear the last search result of generic
            }
            $scope.changeBrandType = function (key) {
                angular
                    .forEach($scope.brandTypeList, function (value, innerkey) {
                        if(value.drug_unit_id == $scope.brandList[key].drug_unit_id) {
                            $scope.brandList[key].drug_unit_name = value.drug_unit_name;
                            $scope.brandList[key].drug_unit_medicine_type = value.medicine_type;
                        }
                    });
            }
            /* search diet instruction */
            $scope.isSearchGlobalInstruction = true;
            $scope.diet_instruction_id = '';
            $scope.searchGlobalInstruction = function (type, key, field) {
            	$scope.diet_instruction_id = '';
            	var search_instruction = "";
            	if(type == 1) {
            		search_instruction = $scope.common.drug_instruction;
            	} else {
            		if(field == 1)
            			search_instruction = $scope.brandList[key].freq_instruction;
            		else
            			search_instruction = $scope.brandList[key].intake_instruction;
            	}
				if(search_instruction.length > 2) {
					var request = {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        search: search_instruction,
                        type: type
                    };
					if($scope.isSearchGlobalInstruction){
						$scope.isSearchGlobalInstruction = false;
						PatientService
							.searchGlobalInstruction(request, function (response) {
								if (response.status == true) {
									if(type == 1)
										$scope.search_instruction_result = response.data;
									else
										$scope.rx_instruction_result = response.data;

								}
								$scope.isSearchGlobalInstruction = true;
							});
					}
				}
            }
            $scope.clearGeneric = function (key) {
            	if($scope.brandList[key] != undefined && $scope.brandList[key].similar_generic != undefined)
            		$scope.brandList[key].similar_generic = '';
            		$scope.drug_generic_id = '';
            }
            /* $scope.dynamicPopover = {
                content: 'Hello, World!',
                templateUrl: 'myPopoverTemplate.html',
                title: 'Title'
            }; */
			$scope.scrollToNextDrugEntry = function(){
				if($scope.brandList != undefined){
					//scroll to next DRUG Entry
					var scto = $scope.brandList.length;
					if(scto > 0 && $("#similarbrand"+parseInt(scto-1))){
						$timeout(function () {
							$('html, body').animate({scrollTop: $("#similarbrand"+parseInt(scto-1)).offset().top - 50}, 1000);
						}, 500);
					}
				}
			}
			
            $scope.setClientData = function (item, type, key, element, isFromRelatedData) {

            	if(type == 4 && $scope.patient != undefined && $scope.patient.refby != undefined)
                	$scope.patient.refby = item.user_id;
                if (type == 8) {
                    $scope.testChief[key].search = item.value;
                    $scope.testChief[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testChief.push({});
                    $scope.focusIndex[1] = Number(key) + 1;
                } else if (type == 9) {
                    $scope.testObservation[key].search = item.value;
                    $scope.testObservation[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testObservation.push({});
                    $scope.focusIndex[2] = Number(key) + 1;
                } else if (type == 10) {
                    $scope.testDiagnosis[key].search = item.value;
                    $scope.testDiagnosis[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testDiagnosis.push({});
                    $scope.focusIndex[3] = Number(key) + 1;
                } else if (type == 11) {
                    $scope.testNotes[key].search = item.value;
                    $scope.testNotes[key].autoID = item.clinical_notes_catalog_id;
                    $scope.testNotes.push({});
                    $scope.focusIndex[4] = Number(key) + 1;
                } else if (type == 7) {
                    if (!isFromRelatedData)
                        isFromRelatedData = false;
                    //call related drug data
                    $scope.suggestion_brand_result = [];
					$scope.rxCSectionSearch = '';
                    if (!isFromRelatedData && $scope.currentUser.sub_plan_setting.enabled_competitive_brands != undefined && $scope.currentUser.sub_plan_setting.enabled_competitive_brands == '1') {
                        PatientService
                                .getRelatedDrugData(item.drug_id, function (response) {
                                    $scope.suggestion_brand_result = response.data;
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                    //call detail drug code
                    SettingService
                            .getDrugDetail(item.drug_id, function (response) {
                                if (response.status) {
                                    $scope.brandList[key].isOpenWholeForm = true;
                                    if((response.data[0].drug_unit_name == 'Tablets' || response.data[0].drug_unit_name == 'IU') && response.data[0].drug_drug_unit_value == '')
                                    	$scope.brandList[key].defaultFreqOpen = true;
                                    if (isFromRelatedData) {
                                        $scope.brandList[key].similar_brand = item.drug_name;
										$scope.brandList[key].drug_name_with_unit = item.drug_name_with_unit;
                                    }
                                    $scope.brandList[key].drug_strength = response.data[0].drug_strength;
                                    if (response.data[0].drug_duration != 0) {
                                        $scope.brandList[key].drug_duration = response.data[0].drug_duration;
                                    } else {
                                        $scope.brandList[key].drug_duration = 1;
                                    }
                                    $scope.brandList[key].drug_duration_value = response.data[0].drug_duration_value;
                                    $scope.brandList[key].drug_frequency_value = response.data[0].drug_frequency_value;
                                    if(response.data[0].drug_intake != "0")
                                    	$scope.brandList[key].drug_intake = response.data[0].drug_intake;
                                    $scope.brandList[key].drug_instruction = response.data[0].drug_instruction;
                                    $scope.brandList[key].freq_instruction = '';
                                    $scope.brandList[key].intake_instruction = '';
                                    if (response.data[0].drug_drug_generic_id) {
                                        $scope.brandList[key].drug_drug_generic_id = response.data[0].drug_drug_generic_id.split(',');
                                    }
                                    $scope.brandList[key].drug_unit_id = response.data[0].drug_unit_id;
                                    $scope.brandList[key].drug_unit_medicine_type = response.data[0].drug_unit_medicine_type;
                                    $scope.brandList[key].drug_unit_value = response.data[0].drug_unit_value;
                                    $scope.brandList[key].dosage = response.data[0].drug_unit_value;
                                    $scope.brandList[key].drug_frequency_id = response.data[0].drug_frequency_id;
                                    $scope.brandList[key].drug_unit_name = response.data[0].drug_unit_name;
                                    if(response.data[0].drug_unit_name == 'As Directed') {
                                    	$scope.brandList[key].dosage = '';
                                    } else {
                                    	$scope.brandList[key].dosage = response.data[0].drug_drug_unit_value;
                                    }
                                    $scope.brandList[key].isOpen = true;
                                    $scope.brandList[key].similar_brand_id = item.drug_id;
                                    $scope.brandList[key].similar_brand = response.data[0].drug_name;
									$scope.brandList[key].drug_name_with_unit = response.data[0].drug_name_with_unit;
									$scope.brandList[key].is_show_suggestion_brand_listing = true;
									angular.forEach($scope.brandList, function (objVal, objInnerkey) {
										if(key != objInnerkey)
											$scope.brandList[objInnerkey].is_show_suggestion_brand_listing = false;
									});
									$scope.changeCustomValue(key);
									//element.autocomplete("destroy");
									$scope.search_similar_brand_result = []; // Clear the last search result
									if(!isFromRelatedData)
										$scope.brandList.push({});
									$scope.prescriptionOnchangeFlag();
									$scope.scrollToNextDrugEntry();
									$scope.drug_generic_id = '';
									$scope.search_similar_generic_result = []; // Clear the last search result of generic
                                }
                            });
                } else if (type == 13) {
                    $scope.testKCO[key].search = item.value;
                    $scope.testKCO[key].autoID = item.key;
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
                            	var instructions_data = response.instructions_data;
                                if (response.data) {
                                    $scope.testLabs.splice(key, 1);
                                    $scope.testLabs.splice($scope.testLabs.length-1, 1);
                                    angular
                                            .forEach(response.data, function (value) {
                                                $scope.testLabs.push({
                                                    search: value.health_analytics_test_name,
                                                    id: value.health_analytics_test_id,
                                                    isOpenInst: (instructions_data[value.health_analytics_test_id] != undefined) ? true : false,
                                                    lab_instruction : (instructions_data[value.health_analytics_test_id] != undefined && instructions_data[value.health_analytics_test_id][0] !=undefined) ? instructions_data[value.health_analytics_test_id][0].instruction : '',
                                                });
                                            });
                                    $scope.testLabs.push({
	                                    search: ''
	                                });
                                } else {
                                    $scope.testLabs[key].search = item.health_analytics_test_name;
                                    $scope.testLabs[key].id = item.health_analytics_test_id;
                                    var instructions_data = response.instructions_data;
	                                $scope.investigation_instructions_data = [];
	                            	if(instructions_data[item.health_analytics_test_id] != undefined){
	                            		$scope.investigation_instructions_data = instructions_data[item.health_analytics_test_id];
	                            		$scope.testLabs[key].isOpenInst = (instructions_data[item.health_analytics_test_id] != undefined) ? true : false;
										$scope.testLabs[key].lab_instruction = (instructions_data[item.health_analytics_test_id] != undefined && instructions_data[item.health_analytics_test_id][0] !=undefined) ? instructions_data[item.health_analytics_test_id][0].instruction : '';
	                            	}
                                }
                                
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                    // $scope.focusIndex[type] = Number(key) + 1;
                } else if (type == 15) {
                	$scope.cSearchProc = '';
                	$scope.procCurrentSearchKey = '';
                } else if (type == 16) {
                    $scope.paymentObjList[key].isOpenPaymentDiv = true;
                    $scope.paymentObjList[key].treatment_name = item.pricing_catalog_name;
                    $scope.paymentObjList[key].basic_cost = item.pricing_catalog_cost;
                    $scope.paymentObjList[key].unit = 1;
                    $scope.paymentObjList[key].discount = 0;
                    var current_tax_id_arr = item.tax_id.split(',');
                    var tax_id_object = [];
                    $scope.paymentObjList[key].tax_id = '';
                    angular
                            .forEach(current_tax_id_arr, function (value, innerkey) {
                                tax_id_object.push(value);
                            });
                    $scope.paymentObjList[key].tax_id = tax_id_object;
                    $scope.finalBillingCalculation(key);
                    $scope.paymentObjList.push({
                        isOpenPaymentDiv: false,
                        rupies_type: '2',
                        id: ''
                    });
                } else if (type == 17) {
                    var parent_id = 0;
                    if (item.health_analytics_test_id) {
                        parent_id = item.health_analytics_test_id;
                    }
                    var request = {
                        search: '',
                        health_analytics_test_type: 1,
                        parent_id: parent_id
                    }
                    PatientService
                            .getHealthTest(request, false, function (response) {
                                if (response.data) {
                                    $scope.patientPreviousHealthAnalyticData.splice(key, 1);
                                    angular
                                            .forEach(response.data, function (value) {
												
												if(value.health_analytics_test_validation != undefined && value.health_analytics_test_validation != '')
													value.health_analytics_test_validation = JSON.parse(value.health_analytics_test_validation);
												else
													value.health_analytics_test_validation = [];
												
                                                $scope.patientPreviousHealthAnalyticData.push({
                                                    search: value.health_analytics_test_name,
                                                    selectedHadv: value,
                                                });
                                            });
                                } else {
                                    $scope.patientPreviousHealthAnalyticData[key].search = item.health_analytics_test_name;
                                    $scope.patientPreviousHealthAnalyticData[key].selectedHadv = item;
                                }
                                $scope.patientPreviousHealthAnalyticData.push({
                                    search: ''
                                });
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                    //$scope.focusIndex[type] = Number(key) + 1;
                } else if (type == 18) {
                	$scope.drug_generic_id = item.drug_drug_generic_id;
					var element_key = parseInt($(element).attr("key"));
					$scope.searchSimilarBrand(element_key);
                } else if (type == 19) {
                	$scope.diet_instruction_id = item.id;
                } else if (type == 21) {
                	$scope.patient.caretaker_user_id = item.user_id;
                	$scope.patient.caretaker_first_name = item.user_first_name;
                	$scope.patient.caretaker_last_name = item.user_last_name;
                	$scope.patient.caretaker_mobile = item.user_phone_number;
                	$scope.patient.is_invalid_caretaker_number = false;
                }
            }
            $scope.openPricingInnerDiv = function (key) {
                $scope.paymentObjList[key].isOpenPaymentDiv = true;
                $scope.paymentObjList[key].closeCustomPricing = true;
                $scope.paymentObjList[key].unit = 1;
                $scope.paymentObjList[key].discount = 0;
                $scope.paymentObjList[key].basic_cost = '';
                $scope.finalBillingCalculation(key);
                $scope.paymentObjList.push({
                    isOpenPaymentDiv: false,
                    rupies_type: '1'
                });
            }
            $scope.patient_report_main_tab = 1;
            $scope.finalBillingCalculation = function (key, form) {
                var final_amount = 0;
                if ($scope.paymentObjList[key].unit) {
                    $scope.paymentObjList[key].unit = Number($scope.paymentObjList[key].unit);
                }
                var basic_cost = $scope.paymentObjList[key].basic_cost;
                if (!basic_cost) {
                    $scope.clearAmount(key);
                    return;
                }
                var basic_cost = Number($scope.paymentObjList[key].basic_cost);
                var taxes = $scope.paymentObjList[key].tax_id;
                $scope.paymentObjList[key].tax_ids = '';
                var final_amount = Number(basic_cost);
                if (!isNaN($scope.paymentObjList[key].unit) && $scope.paymentObjList[key].unit) {
                    if (!isNaN(basic_cost)) {
                        final_amount = final_amount * $scope.paymentObjList[key].unit;
                        $scope.paymentObjList[key].tax_value = 0;
                        $scope.paymentObjList[key].tax_value = $scope.paymentObjList[key].tax_value.toFixed(2);
                        if (!isNaN($scope.paymentObjList[key].discount)) {
                            if ($scope.paymentObjList[key].rupies_type == '1' &&
                                    $scope.paymentObjList[key].discount >= 0 &&
                                    $scope.paymentObjList[key].discount <= 100) {
                                $scope.paymentObjList[key].discount_rupies = Number(final_amount) * Number($scope.paymentObjList[key].discount) / 100;
                            } else {
                                $scope.paymentObjList[key].discount_rupies = Number($scope.paymentObjList[key].discount);
                            }
                            if (!isNaN($scope.paymentObjList[key].discount_rupies)) {
                                final_amount -= Number($scope.paymentObjList[key].discount_rupies);
                            }
                            $scope.paymentObjList[key].discount_rupies = $scope.paymentObjList[key].discount_rupies.toFixed(2);
                        } else {
                            $scope.paymentObjList[key].discount_rupies = 0;
                        }
                        var after_discount = Number(final_amount);
                        $scope.paymentObjList[key].tax_value = Number($scope.paymentObjList[key].tax_value);
                        angular
                                .forEach(taxes, function (value, outerKey) {

                                    angular
                                            .forEach($scope.taxes, function (innerValue, innerKey) {
                                                if (innerValue.tax_id == value) {
                                                    var tax_value = Number((after_discount * Number(innerValue.tax_value)) / 100);
                                                    $scope.paymentObjList[key].tax_value += tax_value;
                                                    final_amount += tax_value;
                                                }
                                            });
                                });
                        if (!isNaN(final_amount)) {
                            $scope.paymentObjList[key].final_amount = Number(final_amount).toFixed(2);
                        } else {
                            $scope.paymentObjList[key].final_amount = '';
                        }
                        $scope.calculateMiddleTotalCost();
                    }
                }
            }
			
            /* final payment */
            $scope.addFinalPayment = function (form) {
                $scope.submitted = true;
                $scope.checkAdvancPaynowAmount(form, '');
                if (form.$valid) {
                    /*generate json for payment  */
                    var json_array = [];
                    angular
                            .forEach($scope.paymentObjList, function (value) {

                                if (value.basic_cost) {
                                    var tax_ids = '';
                                    if (value.tax_id) {
                                        tax_ids = value.tax_id.join(',');
                                    }
                                    var id = '';
                                    if (value.id) {
                                        id = value.id;
                                    }
                                    var tax_value = '';
                                    if (value.tax_value) {
                                        tax_value = value.tax_value;
                                    }
                                    var temp_json = {
                                        treatment_name: value.treatment_name,
                                        unit: value.unit,
                                        cost: value.basic_cost,
                                        discount: value.discount,
                                        discount_type: value.rupies_type,
                                        tax_id: tax_ids,
                                        tax_value: tax_value,
                                        amount: value.final_amount,
                                        id: id,
                                        is_delete: 2
                                    }
                                    json_array.push(temp_json);
                                }
                            });
                    if ($scope.removedObj.length > 0) {
                        angular
                                .forEach($scope.removedObj, function (value, key) {
                                    var temp_json = {
                                        treatment_name: value.treatment_name,
                                        unit: value.unit,
                                        cost: value.basic_cost,
                                        discount: value.discount,
                                        discount_type: value.rupies_type,
                                        amount: value.final_amount,
                                        id: value.id,
                                        is_delete: value.is_delete
                                    }
                                    json_array.push(temp_json);
                                });
                        $scope.removedObj = [];
                    }
                    if($scope.final_payment.invoice_date != undefined) {
	                    var month = $scope.final_payment.invoice_date.getMonth() + 1;
	                    var day = $scope.final_payment.invoice_date.getDate();
	                    var year = $scope.final_payment.invoice_date.getFullYear();
	                    var invoice_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                	} else {
                		var invoice_date = "";
                	}
                    var request = {
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        patient_id: $scope.current_patient.user_id,
						doctor_id: ($rootScope.current_doctor != undefined && $rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                        payment_json: JSON.stringify(json_array),
                        total_discount: Number($scope.final_payment.total_discount).toFixed(2),
                        total_tax: $scope.final_payment.total_tax.toFixed(2),
                        grand_total: Number($scope.final_payment.grand_total).toFixed(2),
                        mode_of_payment_id: $scope.final_payment.payment_mode.payment_mode_id,
                        total_payable: Number($scope.final_payment.payable_amount).toFixed(2),
                        advance_amount: $scope.final_payment.from_advance,
                        paid_amount: $scope.final_payment.pay_now,
						user_id: $rootScope.currentUser.user_id,
						billing_id: $scope.invoice_billing_id,
						invoice_date: invoice_date
                    };
					
					if($scope.billing_invoice_no_data != undefined && $scope.billing_invoice_no_data.inv_prefix != undefined && $scope.billing_invoice_no_data.inv_counter != undefined){
						request.invoice_no = $scope.billing_invoice_no_data.inv_prefix+$scope.billing_invoice_no_data.inv_counter;
					}
					
                    PatientService
                            .acceptPayment(request, function (response) {
                                if (response.status) {
									if($scope.appointment_dates != undefined && $scope.appointment_dates.length > 0 && $scope.current_appointment_date_obj != undefined && $scope.current_appointment_date_obj.appointment_id != undefined){
										//var appDtsIndx = $scope.appointment_dates.find(function(item){return item.appointment_id === $scope.current_appointment_date_obj.appointment_id});
										var appDtsIndx = $scope.appointment_dates.findIndex(x=>x.appointment_id===$scope.current_appointment_date_obj.appointment_id);
										$scope.current_appointment_date_obj.appointment_payment_state = '1';
										if($scope.appointment_dates[appDtsIndx] != undefined && $scope.appointment_dates[appDtsIndx].appointment_payment_state != undefined){
											$scope.appointment_dates[appDtsIndx].appointment_payment_state = '1';
										}
									}
									ngToast.success({
                                        content: response.message
                                    });
                                    $scope.getInvoiceList();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.checkRequiredValidation = function (key) {
                if (key == 0 || $scope.paymentObjList.length == 0) {
                    return true;
                }
            }
            $scope.clearAmount = function (key) {
                $scope.paymentObjList[key].final_amount = 0;
                $scope.paymentObjList[key].discount = 0;
                $scope.paymentObjList[key].tax_value = 0;
                $scope.final_payment.total_cost = 0;
                $scope.final_payment.total_discount = 0;
                $scope.final_payment.total_tax = 0;
                $scope.final_payment.grand_total = 0;
                $scope.final_payment.payable_amount = 0;
                $scope.final_payment.from_advance = '';
                $scope.final_payment.pay_now = '';
            }
            $scope.final_payment = {
                total_cost: 0,
                total_discount: 0,
                total_tax: 0,
                grand_total: 0,
                payable_amount: 0,
                invoice_date: new Date(rx_year,rx_month,rx_day)
            };
            $scope.calculateMiddleTotalCost = function () {

                var total_cost = 0;
                var total_discount = 0;
                var total_tax = 0;
                var grand_total = 0;
                angular
                        .forEach($scope.paymentObjList, function (paymentObj, key) {
                            if (paymentObj.basic_cost) {
                                total_cost += (Number(paymentObj.unit) * Number(paymentObj.basic_cost));
                                total_discount += Number(paymentObj.discount_rupies);
                                total_tax += Number(paymentObj.tax_value);
                                if (isNaN(total_cost)) {
                                    total_cost = 0
                                }
                                if (isNaN(total_discount)) {
                                    total_discount = 0
                                }
                                if (isNaN(total_tax)) {
                                    total_tax = 0
                                }
                            }

                        });
                grand_total = Number((total_cost + total_tax) - total_discount).toFixed(2);
                $scope.final_payment.total_cost = total_cost;
                $scope.final_payment.total_discount = total_discount;
                $scope.final_payment.total_tax = total_tax;
                $scope.final_payment.grand_total = grand_total;
                $scope.final_payment.payable_amount = grand_total;
                $scope.calculateModeOfPayment();
            }
            $scope.calculateModeOfPayment = function () {
                if ($scope.final_payment.payment_mode) {
                    var percentage = Number($scope.final_payment.payment_mode.payment_mode_vendor_fee);
                    if (!percentage) {
                        percentage = 0;
                    }
					//if (percentage) {
					$scope.final_payment.payable_amount = Number($scope.final_payment.grand_total);
					var mode_value = Number((Number($scope.final_payment.grand_total) * percentage) / 100).toFixed(2);
					$scope.final_payment.payable_amount = Number($scope.final_payment.payable_amount) + Number(mode_value);
					$scope.final_payment.payable_amount = Math.round($scope.final_payment.payable_amount.toFixed(2));
					//}
                }
                if (!$scope.is_from_billing_detail) {

                    $scope.final_payment.from_advance = '';
                    $scope.final_payment.pay_now = '';
                }
                $scope.is_from_billing_detail = false;
            }
            $scope.checkAdvancPaynowAmount = function (form, type) {
                if (!$scope.final_payment.from_advance) {
                    var advance = 0;
                } else {
                    var advance = Number($scope.final_payment.from_advance);
                }
                if ($scope.final_payment.payable_amount) {
                    if (type == 1) {
                        var remaining = Number($scope.final_payment.payable_amount) - Number($scope.final_payment.from_advance);
                        if (remaining >= 0) {
                            $scope.final_payment.pay_now = Number(remaining);
                        } else {
                            $scope.final_payment.pay_now = 0;
                        }
                    } else if (type == 2) {
                        var remaining = Number($scope.final_payment.payable_amount) - Number($scope.final_payment.pay_now);
                        if (remaining >= 0) {
                            $scope.final_payment.from_advance = Number(remaining);
                        } else {
                            $scope.final_payment.from_advance = 0;
                        }
                    }
                    advance = Number($scope.final_payment.from_advance);
                    var pay_now = Number($scope.final_payment.pay_now);
                    var total = (advance + pay_now).toFixed(2);
                    if (total != $scope.final_payment.payable_amount) {
                        form.from_advance.$setValidity("pattern", false);
                    } else {
                        form.from_advance.$setValidity("pattern", true);
                    }
                }
            }
            /* get payments modes api */
            $scope.getPaymentModes = function () {
                SettingService
                        .getModes('', function (response) {
                            if (response.status) {
                                $scope.payment_modes = response.data;
                                angular
                                        .forEach($scope.payment_modes, function (value) {
                                            value.title = value.payment_mode_name + " (" + value.payment_mode_vendor_fee + " %)"
                                        });
                            } else {
                                $scope.payment_modes = [];
                            }
                        });
            }
            $scope.checkunitValidation = function (event, form, key) {
                if ((event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) || event.ctrlKey) {
                    form.unit.$setValidity("pattern", false);
                    $scope.paymentObjList[key].unit = '';
                    $scope.clearAmount(key);
                    event.preventDefault();
                    return false;
                } else {
                    form.unit.$setValidity("pattern", true);
                    return true;
                }

            }
            $scope.errors = {};
            $scope.checkDiscountValidation = function (form, key) {
                var discount = Number($scope.paymentObjList[key].discount);
                var type = Number($scope.paymentObjList[key].rupies_type);
                var cost = Number($scope.paymentObjList[key].basic_cost);
                if (isNaN(discount)) {
                    form.discount.$setValidity("pattern", false);
                    $scope.errors.discount_error = "Invalid discount";
                    return;
                }
                if (type == 1) {
                    if (discount < 0 || discount > 100) {
                        $scope.errors.discount_error = "Invalid discount";
                        form.discount.$setValidity("pattern", false);
                        return;
                    } else {
                        form.discount.$setValidity("pattern", true);
                    }
                } else {
                    if (discount > cost) {
                        $scope.errors.discount_error = "Should be less than cost";
                        form.discount.$setValidity("pattern", false);
                        return;
                    } else {
                        form.discount.$setValidity("pattern", true);
                    }
                }
            }
			
            $scope.delete_invoice = function (invoiceObj) {
				SweetAlert.swal(
                    {
                        title: "Are you sure want to delete this invoice?",
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
			                    billing_id: invoiceObj.billing_id,
								doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
			                };
			                PatientService
			                    .deleteInvoice(request, function (response) {
			                    	ngToast.success({
			                            content: response.message
			                        });
			                        $scope.getInvoiceList();
			                    });
                        }
                    }
                );
				
			}
            $scope.invoices_list = [];
			$scope.getInvoiceList = function () {
				$rootScope.page_number = $rootScope.INVOICE_MODULE;
                $rootScope.page_name = 'Invoice'; 
                $rootScope.gTrack('invoice');
                var request = {
                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                    patient_id: $scope.current_patient.user_id,
                    clinic_id:  $rootScope.current_clinic.clinic_id,
					doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                };
                PatientService
                    .getInvoiceList(request, function (response) {
                    	$scope.invoices_list = response.data;
                    	$scope.isInvoiceEditMode = false;
                    	if(response.billing_invoice_no_data != undefined){
							$scope.billing_invoice_no_data = response.billing_invoice_no_data;
							//$scope.billing_invoice_no_data.inv_counter = parseInt($scope.billing_invoice_no_data.inv_counter)+1;
						}
                    });
			}
            $scope.getBillingDetail = function (invoiceObj) {
				$scope.isInvoiceEditMode = false;
				$rootScope.page_number = $rootScope.INVOICE_MODULE;
                $rootScope.page_name = 'Invoice'; 
                $rootScope.gTrack('invoice');
                var request = {
                    billing_id: invoiceObj.billing_id,
                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                    patient_id: $scope.current_patient.user_id,
                    clinic_id:  $rootScope.current_clinic.clinic_id,
					doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                };
                $scope.invoice_billing_id = invoiceObj.billing_id;
                $scope.paymentObjList = []; $scope.paymentObjListForDisp = [];
                $scope.removedObj = []; $scope.final_paymentForDisp = [];
				PatientService
                        .getAppointmentBillingDetail(request, function (response) {
							$scope.final_payment = {
								total_cost: 0,
								total_discount: 0,
								total_tax: 0,
								grand_total: 0,
								payable_amount: 0
							};
							$scope.is_invoice_import_data = response.is_import_data;
							var billing_details_unit_total = 0;
							angular
                                    .forEach(response.data, function (value, key) {
										/* START DISCOUNT CALCULATION */
										var bdd = Number(value.billing_detail_discount);
										var billing_detail_discount_rupies = 0;
										if (!isNaN(Number(bdd)) && bdd > 0) {
											if (value.billing_detail_discount_type == '1' && bdd >= 0 && bdd <= 100) {
												billing_detail_discount_rupies = (Number(value.billing_detail_unit) * Number(value.billing_detail_basic_cost)) * Number(bdd) / 100;
											} else {
												billing_detail_discount_rupies = Number(bdd);
											}
											billing_detail_discount_rupies = billing_detail_discount_rupies.toFixed(2);
										} else {
											billing_detail_discount_rupies = 0;
										}
										/* END DISCOUNT CALCULATION */
										
										billing_details_unit_total += (Number(value.billing_detail_unit) * Number(value.billing_detail_basic_cost));
                                        var temp_obj = {
                                            id: value.billing_detail_id,
                                            treatment_name: value.billing_detail_name,
                                            unit: 		Number(value.billing_detail_unit),
                                            basic_cost: Number(value.billing_detail_basic_cost),
                                            discount: 	Number(value.billing_detail_discount),
											billing_detail_discount_rupies: billing_detail_discount_rupies,
                                            rupies_type:(value.billing_detail_discount_type),
                                            tax_id: 	value.billing_detail_tax_id.split(','),
											tax_value:  Number(value.billing_detail_tax),
											final_amount: Number(value.billing_detail_total),
                                            isOpenPaymentDiv: true,
                                            discount_rupies: value.billing_discount
                                        };
                                        $scope.paymentObjList.push(temp_obj);
                                        if (key == 0) {
											$scope.final_payment.billing_created_at = new Date(value.billing_created_at);
											$scope.final_payment.invoice_date = new Date(value.billing_invoice_date);
											$scope.final_payment.invoice_number = 	value.invoice_number;
                                            $scope.final_payment.total_cost = 		Number(value.billing_total_payable);
                                            $scope.final_payment.total_discount = 	Number(value.billing_discount);
                                            $scope.final_payment.billing_tax = 		Number(value.billing_tax);
											$scope.final_payment.total_tax = 		Number(value.billing_tax); 
                                            $scope.final_payment.grand_total = 		Number(value.billing_grand_total);
                                            $scope.final_payment.from_advance = 	Number(value.billing_advance_amount);
                                            $scope.final_payment.pay_now = 			Number(value.billing_paid_amount);
                                            $scope.final_payment.payable_amount = 	Number(value.billing_total_payable);
                                            angular
                                                    .forEach($scope.payment_modes, function (innerValue, innerKey) {
                                                        if (value.billing_payment_mode_id == innerValue.payment_mode_id) {
                                                            $scope.final_payment.payment_mode = innerValue;
                                                        }
                                                    });
                                            $scope.is_from_billing_detail = true;
                                        }
                                    });
									$scope.final_payment.total_cost = billing_details_unit_total;
                            if ($scope.paymentObjList.length == 0) {
                                $scope.paymentObjListForDisp = [];
								$scope.paymentObjList.push({isOpenPaymentDiv: false, rupies_type: '2'});
                                $scope.clearAmount(0);
                                $scope.final_payment.payment_mode = '';
                            } else {
								$scope.paymentObjListForDisp = angular.copy($scope.paymentObjList);
								$scope.final_paymentForDisp = angular.copy($scope.final_payment);
                                $scope.paymentObjList.push({isOpenPaymentDiv: false, rupies_type: '2'});
                            }
							$scope.isInvoiceEditMode = true;
                        }, function (error) {
                            $rootScope.handleError(error);
                        })
            }
			
			$scope.add_invoice = function(){
				$scope.paymentObjListForDisp = [];
				$scope.paymentObjList = [];
				$scope.paymentObjList.push({isOpenPaymentDiv: false, rupies_type: '2'});
                $scope.clearAmount(0);
                $scope.final_payment.payment_mode = '';
				$scope.isInvoiceEditMode = true;
				$scope.invoice_billing_id = '';
				$scope.final_payment = {
					total_cost: 0,
					total_discount: 0,
					total_tax: 0,
					grand_total: 0,
					payable_amount: 0,
					invoice_date: new Date(rx_year,rx_month,rx_day)
				};

			}
			
			$scope.showEditModeInvoice = function(){
				$scope.isInvoiceEditMode = true;
			}

            /* Temparture convert code (F to C & C to F)*/
            $scope.changeFCValue = function (unitType) {
                if ($scope.vital.temp) {
                    $scope.vital.temp = $filter('FCConvert')($scope.vital.temp, unitType);
                }
            }

            /* add vital form code */
            $scope.addVital = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    if (!$scope.vital.weight &&
                            !$scope.vital.systolic &&
                            !$scope.vital.diastolic &&
                            !$scope.vital.pulse &&
                            !$scope.vital.temp &&
                            !$scope.vital.resp
                            ) {
                        ngToast.danger('Please Enter Atleast One Vital Sign');
                        return;
                    }

                    var pressure_type = $scope.vital.standing;
                    var temperature_type = 1;
                    /* if ($scope.vital.standing) {
                     pressure_type = 2;
                     } */
                    $scope.vital.f_temp = $scope.vital.temp;
                    if ($scope.vital.celsuis && $scope.vital.f_temp) {
                        $scope.vital.f_temp = $filter('FCConvert')($scope.vital.temp, 2)
                        temperature_type = 2;
                    }

                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        date: $scope.current_appointment_date_obj.appointment_date,
                        sp2o: $scope.vital.spo,
                        weight: $filter('kgToPound')($scope.vital.weight),
                        blood_pressure_systolic: $scope.vital.systolic,
                        blood_pressure_diastolic: $scope.vital.diastolic,
                        blood_pressure_type: pressure_type,
                        pulse: $scope.vital.pulse,
                        temperature: $scope.vital.f_temp,
                        temperature_type: temperature_type,
                        temperature_taken: $scope.vital.teperature_taken_id,
                        resp: $scope.vital.resp,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                    };
                    request.id = $scope.vital.id;
                    if ($scope.common.isOpenForEdit) {
                        PatientService
                                .editVital(request, function (response) {
                                    $scope.submitted = false;
                                    $scope.is_change_prescription = false;
                                    if (response.status) {
                                        $scope.common.isOpenForEdit = false;
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getPatientReportDetail();
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    } else {
                        PatientService
                                .addNewVital(request, function (response) {
                                    $scope.submitted = false;
                                    $scope.is_change_prescription = false;
                                    if (response.status) {
                                        $scope.vital.id = response.vital_id;
                                        ngToast.success({
                                            content: response.message,
                                            className: '',
                                            dismissOnTimeout: true,
                                            timeout: 5000
                                        });
                                        $scope.getPatientReportDetail();
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }
            }
            $scope.getPatientReportDetail = function (followup_obj, outerKey) {
            	$scope.is_change_prescription = false;
            	$scope.patientTabSetPageNumber($scope.patient_report_tab_index);
                var appoinement_id = $scope.current_appointment_date_obj.appointment_id;
                var appoinement_date = $scope.current_appointment_date_obj.appointment_date;
                if (followup_obj) {
                    appoinement_id = followup_obj.appointment_id;
                    appoinement_date = followup_obj.appointment_date;
                }
                $scope.common.isOpenForEdit = false;
                $scope.common.isClinicalForEdit = false;
                $scope.common.isRXFormOpen = false;
                /* get all prescritpion report */
                var key = $scope.patient_report_tab_index;
                if (outerKey) {
                    key = outerKey;
                }
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    appointment_id: appoinement_id,
                    date: appoinement_date,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    key: key,
                    doctor_id: $rootScope.current_doctor.user_id
                };
                PatientService
                        .getPatientWholeReport(request, function (response) {

                            if (!$scope.is_from_template) {
                                $scope.brandList = [{
                                        brand_name: '',
                                        isOpen: false,
                                        default1: '',
                                        default2: '',
                                        default3: '',
                                    }];
                                $scope.suggestion_brand_result = [];
								$scope.rxCSectionSearch = '';
                                $scope.vital = {};
                                $scope.vital.pulse = '';
                                $scope.vital.teperature_taken_id = 6;
                                $scope.addedBrandList = [];
                                $scope.addedProcedureObj = [];
                                $scope.procedureObj = [{
                                        text: ''
                                    }];
                                $scope.procedure.note = '';
                                $scope.reportObj = [{
                                        report_name: '',
                                        report_type: '',
                                        report_date: '',
                                        temp_img: '',
                                        img: ''
                                    }];
                                $scope.addedProcedure.note = '';
                                $scope.addedInvestigationObj = [];
                                $scope.addedInvestigation.note = '';
                                $scope.testLabs = [{
                                        search: ''
                                    }];
                                $scope.template.lab_instruction = '';
                                $scope.addedReportObj = [];
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
                                $scope.testKCO = [{
                                    }];
                                $scope.chief_images = [];
                                $scope.obs_images = [];
                                $scope.diagnosis_images = [];
                                $scope.notes_images = [];
                                $scope.health.healthAnalyticsObj = [];
                                $scope.health.healthAnalyticsObjWithoutValue = [];
                                $scope.health.id = '';
                            }

                            var resObj = response.data;
                            if (key == 1) {
                                $scope.vital = {};
                                $scope.vital.teperature_taken_id = 6;
                                if (resObj) {
                                    $scope.vital.id = resObj.vital_report_id;
                                    $scope.vital.weight = $filter('PoundToKG')(resObj.vital_report_weight);
                                    $scope.vital.pulse = resObj.vital_report_pulse;
                                    $scope.vital.resp = resObj.vital_report_resp_rate;
                                    $scope.vital.spo = resObj.vital_report_spo2;
                                    $scope.vital.systolic = resObj.vital_report_bloodpressure_systolic;
                                    $scope.vital.diastolic = resObj.vital_report_bloodpressure_diastolic;
                                    $scope.vital.temp = resObj.vital_report_temperature;
                                    $scope.vital.teperature_taken_id = resObj.vital_report_temperature_taken;
                                    if ($scope.vital.teperature_taken_id == 0) {
                                        $scope.vital.teperature_taken_id = 6;
                                    }
                                    $scope.vital.standing = resObj.vital_report_bloodpressure_type;
                                    /* if (resObj.vital_report_bloodpressure_type == 1) {
                                     $scope.vital.standing = false;
                                     } else {
                                     $scope.vital.standing = true;
                                     } */
                                    if (resObj.vital_report_temperature_type == 1) {
                                        $scope.vital.celsuis = false;
                                    } else {
                                        $scope.vital.temp = $filter('FCConvert')($scope.vital.temp, 1)
                                        if (isNaN($scope.vital.temp)) {
                                            $scope.vital.temp = '';
                                        }
                                        $scope.vital.celsuis = true;
                                    }
                                } else {
                                    $scope.vital.standing = true;
                                    $scope.vital.temp = $filter('FCConvert')($scope.vital.temp, 1)
                                    if (isNaN($scope.vital.temp)) {
                                        $scope.vital.temp = '';
                                    }
                                    $scope.vital.celsuis = false;
                                }
                                if ($scope.current_patient.user_id) {
                                    $scope.vital_current_page = 1;
                                    $scope.getTableData();
                                }
                            } else if (key == 2) {
                            	$scope.clinical_notes_reports_id = undefined;
                                if (response.data) {
                                	$scope.clinical_notes_reports_id = response.data.clinical_notes_reports_id;
                                    var kco_data = JSON.parse(response.data.clinical_notes_reports_kco);
                                    $scope.testKCO = [];
                                    angular
                                            .forEach(kco_data, function (value, key) {
                                                $scope.testKCO.push({
                                                    search: value,
                                                    autoID: key + 1
                                                });
                                            });
                                    if ($scope.testKCO.length == 0) {
                                        $scope.testKCO = [{
                                            }];
                                    }

                                    var chied_data = JSON.parse(response.data.clinical_notes_reports_complaints);
                                    $scope.testChief = [];
                                    angular
                                            .forEach(chied_data, function (value, key) {
                                                $scope.testChief.push({
                                                    search: value,
                                                    autoID: key + 1
                                                });
                                            });
                                    if ($scope.testChief.length == 0) {
                                        $scope.testChief = [{
                                            }];
                                    }

                                    var obs_data = JSON.parse(response.data.clinical_notes_reports_observation);
                                    $scope.testObservation = [];
                                    angular
                                            .forEach(obs_data, function (value, key) {
                                                $scope.testObservation.push({
                                                    search: value,
                                                    autoID: key + 1
                                                });
                                            });
                                    if ($scope.testObservation.length == 0) {
                                        $scope.testObservation = [{
                                            }];
                                    }
                                    var diagnosis_data = JSON.parse(response.data.clinical_notes_reports_diagnoses);
                                    $scope.testDiagnosis = [];
                                    angular
                                            .forEach(diagnosis_data, function (value, key) {
                                                $scope.testDiagnosis.push({
                                                    search: value,
                                                    autoID: key + 1
                                                });
                                            });
                                    if ($scope.testDiagnosis.length == 0) {
                                        $scope.testDiagnosis = [{
                                            }];
                                    }

                                    var notes_data = JSON.parse(response.data.clinical_notes_reports_add_notes);
                                    $scope.testNotes = [];
                                    angular
                                            .forEach(notes_data, function (value, key) {
                                                $scope.testNotes.push({
                                                    search: value,
                                                    autoID: key + 1
                                                });
                                            });
                                    if ($scope.testNotes.length == 0) {
                                        $scope.testNotes = [{
                                            }];
                                    }
                                    /* manipulate images array */
                                    var images_array = response.data.images;
                                    $scope.chief_images = [];
                                    $scope.obs_images = [];
                                    $scope.diagnosis_images = [];
                                    $scope.notes_images = [];
                                    angular
                                            .forEach(images_array, function (value, key) {
                                                if (value.clinic_notes_reports_images_type == 1) {
                                                    $scope.chief_images.push(value);
                                                } else if (value.clinic_notes_reports_images_type == 2) {
                                                    $scope.obs_images.push(value);
                                                } else if (value.clinic_notes_reports_images_type == 3) {
                                                    $scope.diagnosis_images.push(value);
                                                } else if (value.clinic_notes_reports_images_type == 4) {
                                                    $scope.notes_images.push(value);
                                                }
                                            });
                                }
                                if ($scope.current_patient.user_id) {
                                    $scope.getTableDataForNotes();
                                }
                            } else if (key == 3) {
                            	if($scope.currentUser.sub_plan_setting.enabled_common_brands != undefined && $scope.currentUser.sub_plan_setting.enabled_common_brands == '1')
                            		$scope.tab.rx = 1;
                            	else
                            		$scope.tab.rx = 2;
                                $scope.getTableDataForRX(true, '', $scope.tab.rx);
                                if (resObj) {
                                    $scope.addedBrandList = resObj;
                                } else {
                                    $scope.addedBrandList = [];
                                    if($scope.is_notify_allegy_popup && $scope.current_appointment_date_obj.is_editable && $scope.current_patient.medicineAllergies != undefined && $scope.current_patient.medicineAllergies.length > 0)
										$scope.AllergyAlert();
                                }
                                $scope.isEditRxData = false;
                                if(response.data != undefined)
                                	$scope.isEditRxData = true;
                                $scope.common.next_folloup_date = '';
                                if (response.next_follow_up) {
                                    $scope.common.next_folloup_date_string = response.next_follow_up;
                                } else {
                                    $scope.common.next_folloup_date_string = '';
                                }
                                $scope.common.is_capture_compliance = true;
                                if (response.is_capture_compliance != undefined && response.is_capture_compliance == false) {
                                    $scope.common.is_capture_compliance = false;
                                }
                                if (response.follow_up_instruction) {
                                    $scope.common.drug_instruction = response.follow_up_instruction;
                                }else{
									$scope.common.drug_instruction = '';
								}
                            } else if (key == 4) {
                                $scope.tab.ix = 1;
                                $scope.getListIXReport();
                                $scope.lab_report_id = undefined;
                                if (response.data) {
                                	$scope.lab_report_id = response.data.lab_report_id;
                                    var keys = [];
                                    $scope.addedInvestigationObj = JSON.parse(response.data.lab_report_test_name);
                                    for (var k in $scope.addedInvestigationObj) {
                                        keys.push(k);
                                    }
                                    $scope.addedInvestigationObj.keys = keys;
//                                    angular
//                                            .forEach($scope.addedInvestigationObj, function (value, key) {
//
//                                                for (var k in value) {
//                                                    keys.push(k);
//                                                }
//                                                value.keys = keys;
//                                            });
                                    $scope.addedInvestigation.note = response.data.lab_report_instruction;
                                    $scope.addedInvestigation.id = response.data.lab_report_id;
                                }
                            } else if (key == 5) {
                                if ($scope.current_patient.user_id) {
                                    $scope.tab.proc = 3;
                                    $scope.getMyProcData();
                                }
                                $scope.procedure_report_id = undefined;
                                $scope.cSearchProc = '';
                				$scope.procCurrentSearchKey = '';
                                if (response.data) {
                                	$scope.procedure_report_id = response.data.procedure_report_id;
                                    $scope.addedProcedureObj = JSON.parse(response.data.procedure_report_procedure_text);
                                    $scope.addedProcedure.note = response.data.procedure_report_note;
                                    $scope.addedProcedure.id = response.data.procedure_report_id;
                                }
                            } else if (key == 6) {
                                if ($scope.current_patient.user_id) {
                                    $scope.getTableDataReports();
                                }
                                $scope.isEditReportData = undefined;
                                if (response.data && response.data.length > 0) {
                                	$scope.isEditReportData = true;
                                    $scope.addedReportObj = response.data;
                                    angular
                                            .forEach($scope.addedReportObj, function (value, key) {
                                                if (value.file_report_date) {
                                                    value.file_report_date = $filter('date')(value.file_report_date, "dd/MM/y")
                                                }
                                            });
                                }
                                $scope.report_upload_pre.edit = false;
                            } else if (key == 7) {
                                if ($scope.current_patient.user_id) {
                                    $scope.getTableDataReports();
                                }
                                $scope.health.real_healthAnalyticsObjWithoutValue = [];
                                $scope.isEditAnalyticData = undefined;
                                if (response.data) {
                                	if(response.data.health_analytics_report_appointment_id != undefined)
                                		$scope.isEditAnalyticData = true;
                                    var analytics_data = JSON.parse(response.data.health_analytics_report_data);
                                    $scope.health.healthAnalyticsObj = analytics_data;
                                    $scope.health.id = response.data.health_analytics_report_id;
                                    angular
                                            .forEach(analytics_data, function (value, key) {
                                                $scope.health.healthAnalyticsObjWithoutValue.push({
                                                    id: value.id,
                                                    name: value.name,
                                                    doctor_id: value.doctor_id,
                                                    precise_name: value.precise_name,
                                                    health_analytics_test_validation: value.health_analytics_test_validation
                                                });
                                                $scope.health.real_healthAnalyticsObjWithoutValue.push({
                                                    id: value.id,
                                                    name: value.name,
                                                    value: value.value,
                                                    doctor_id: value.doctor_id,
                                                    precise_name: value.precise_name,
                                                    health_analytics_test_validation: value.health_analytics_test_validation
                                                });
                                            });
                                }
                            } else if (key == 8) {
                            	$scope.rxUploadObj = [{
					                rx_upload_name: "Rx - " + $scope.current_patient.user_first_name + " " + $scope.current_patient.user_last_name,
					                rx_upload_date: new Date(rx_year,rx_month,rx_day),
					                temp_img: '',
					                img: ''
					            }];
			                	$scope.getUploadedRx();
			                	$scope.report_search.search = '';
			                	$scope.getTableDataReports("12");
			                	$(".report-type-tab").removeClass('active');
			                	$(".report-type-12").addClass('active');
			                	$scope.rx_upload_pre.edit = false;
                            }

                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.removeReportObj = function(key){
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    report_id: $scope.addedReportObj[key].file_report_id,
                };
                SweetAlert.swal({
                    title: "Are you sure want to delete this report?",
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
                            PatientService
                                    .deleteReport(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getPatientReportDetail();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }

            /* change appointment acitve  */
            $scope.changeActiveDate = function (key,appointmentObj,e) {
            	$scope.checkPatientDataSaveAlert(e,function(st){
            		if(st==true) {
		                $scope.submitted = false;
		                $scope.is_from_template = false;
		                angular
		                        .forEach($scope.appointment_dates, function (value) {
		                            value.is_active = false;
		                        });
		                $scope.appointment_dates[key].is_active = true;
		                $scope.current_appointment_date_obj = $scope.appointment_dates[key];

		                if(appointmentObj.appointment_is_import == 0) {
		                	$scope.is_appoinment_import_data = false;
		                } else {
		                	$scope.is_appoinment_import_data = true;
		                }

						var appointmentTypeName = $filter('filter')(APPOINTMENT_TYPE, {id: $scope.current_appointment_date_obj.appointment_type});
						appointmentTypeName = (appointmentTypeName != undefined) ? appointmentTypeName[0].name : $scope.current_patient.appointment_type;
						$scope.current_appointment_date_obj.appointment_type_name = appointmentTypeName;

		                if(appointmentObj.is_done == 1)
		                	$scope.is_appoinment_done = 1;
		                else
		                	$scope.is_appoinment_done = 0;
		                if ($scope.patient_report_main_tab == 1) {
		                    $scope.getPatientReportDetail();
		                } else {
							$scope.getInvoiceList();
		                }
		            }
	            });
            }
            $scope.changeAppointmentStatus = function() {
            	var request = {
            		flag: $scope.is_appoinment_done,
            		appointment_id: $scope.current_appointment_date_obj.appointment_id,
            	}
            	PatientService
                            .updateAppointmentStatus(request, function (response) {
                                var index = $scope.appointment_dates.map(function (item) {
						            return item.appointment_id;
						        }).indexOf(request.appointment_id);
                                $scope.appointment_dates[index].is_done = request.flag;

                                var listIndex = $scope.patientList.map(function (item) {
						            return item.appointment_id;
						        }).indexOf(request.appointment_id);
                                if($scope.patientList[listIndex] != undefined) { 
	                                $scope.patientList[listIndex].is_done = request.flag;
	                                if($scope.patientFilter == 2) {
	                                	$scope.patientList.splice(listIndex, 1);
	                                }
                            	}
                            	if($scope.patientFilter == 2 && request.flag == 0) {
                            		$rootScope.patient_obj = {
                            			appointment_clinic_id: $rootScope.current_clinic.clinic_id,
				                        user_id: $scope.current_patient.user_id,
				                        appointment_id: $scope.current_appointment_date_obj.appointment_id
                            		}
                            		$scope.getPatientList($scope.patientFilter, true);
                            	}
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
            }
            $scope.updateAppointmentStatus = function() {
            	if($scope.is_appoinment_done == 1) {
            		$scope.changeAppointmentStatus();
            	} else {
            		SweetAlert.swal(
                            {
                                title: "Are you sure want to move this appointment in pending list?",
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
                                	$scope.changeAppointmentStatus();
                                } else {
                                	$scope.is_appoinment_done = 1;
                                }
                            }
                        );
            	}
            }
            /* vital form validation */
            $scope.checkValidation = function (type, form) {
                if (type == 1) {
                    if ($scope.vital.pulse) {
                        var pulse = Number($scope.vital.pulse);
                        if (isNaN(pulse)) {
                            $scope.vital.pulse_error = "Invalid pulse";
                            form.pulse.$setValidity("pattern", false);
                            return;
                        }
                        if (pulse < 10 || pulse > 500) {
                            $scope.vital.pulse_error = "Pulse rate cannot be lesser than 10 nor greater than 500";
                            form.pulse.$setValidity("pattern", false);
                        } else {
                            form.pulse.$setValidity("pattern", true);
                        }
                    } else {
                        form.pulse.$setValidity("pattern", true);
                    }
                } else if (type == 2) {
                    if ($scope.vital.resp) {
                        var resp = Number($scope.vital.resp);
                        if (isNaN(resp)) {
                            $scope.vital.resp_error = "Invalid Respiration";
                            form.resp.$setValidity("pattern", false);
                            return;
                        }
                        if (resp < 10 || resp > 70) {
                            $scope.vital.resp_error = "Respiration rate cannot be lesser than 10 nor greater than 70";
                            form.resp.$setValidity("pattern", false);
                        } else {
                            form.resp.$setValidity("pattern", true);
                        }
                    } else {
                        form.resp.$setValidity("pattern", true);
                    }
                } else if (type == 3) {
                    if ($scope.vital.systolic) {
                        var systolic = Number($scope.vital.systolic);
                        if (isNaN(systolic)) {
                            $scope.vital.sys_error = "Invalid Systolic value";
                            form.systolic.$setValidity("pattern", false);
                            return;
                        }
                        if (systolic < 50 || systolic > 300) {
                            $scope.vital.sys_error = "Systolic Blood Pressure cannot be lesser than 50 nor greater than 300";
                            form.systolic.$setValidity("pattern", false);
                        } else {
                            form.systolic.$setValidity("pattern", true);
                        }
                    } else {
                        form.systolic.$setValidity("pattern", true);
                    }
                } else if (type == 4) {
                    if ($scope.vital.diastolic) {
                        var diastolic = Number($scope.vital.diastolic);
                        if (isNaN(diastolic)) {
                            $scope.vital.dis_error = "Invalid Diastolic value";
                            form.diastolic.$setValidity("pattern", false);
                            return;
                        }
                        if (diastolic < 25 || diastolic > 200) {
                            $scope.vital.dis_error = "Diastolic Blood Pressure cannot be lesser than 25 nor greater than 200";
                            form.diastolic.$setValidity("pattern", false);
                        } else {
                            form.diastolic.$setValidity("pattern", true);
                        }
                    } else {
                        form.diastolic.$setValidity("pattern", true);
                    }
                } else if (type == 5) {
                    if ($scope.vital.temp) {
                        var temp = Number($scope.vital.temp);
                        if (isNaN(temp)) {
                            $scope.vital.temp_error = "Invalid Temperature";
                            form.temp.$setValidity("pattern", false);
                            return;
                        }
                        if (!$scope.vital.celsuis) {
                            if (temp < 75.2 || temp > 109.4) {
                                $scope.vital.temp_error = "Temperature cannot be lesser than 75.2 nor greater than 109.4";
                                form.temp.$setValidity("pattern", false);
                            } else {
                                form.temp.$setValidity("pattern", true);
                            }
                        } else {
                            if (temp < 24 || temp > 43) {
                                $scope.vital.temp_error = "Temperature cannot be lesser than 24 nor greater than 43";
                                form.temp.$setValidity("pattern", false);
                            } else {
                                form.temp.$setValidity("pattern", true);
                            }
                        }
                    } else {
                        form.temp.$setValidity("pattern", true);
                    }


                } else if (type == 6) {
                    if ($scope.vital.weight) {
                        var weight = Number($scope.vital.weight);
                        if (isNaN(weight)) {
                            form.weight.$setValidity("pattern", false);
                            $scope.vital.error = "Invalid weight";
                            return;
                        }
                        if (weight <= 0 || weight > 200) {
                            $scope.vital.error = "Weight can not be less then 1 nor greater than 200";
                            form.weight.$setValidity("pattern", false);
                            return;
                        } else {
                            form.weight.$setValidity("pattern", true);
                        }
                    } else {
                        form.weight.$setValidity("pattern", true);
                    }
                } else if (type == 7) {
                    if ($scope.vital.spo) {
                        var spo = Number($scope.vital.spo);
                        if (isNaN(spo)) {
                            form.spo.$setValidity("pattern", false);
                            $scope.vital.spo_error = "Invalid spo";
                            return;
                        }
                        if (spo <= 0 || spo > 100) {
                            $scope.vital.spo_error = "SpO2 cannot be lesser than 1 nor greater than 100.";
                            form.spo.$setValidity("pattern", false);
                            return;
                        } else {
                            form.spo.$setValidity("pattern", true);
                        }
                    } else {
                        form.spo.$setValidity("pattern", true);
                    }
                }
            }

            /* save current data to new template */
            $scope.addNewTemplate = function (form) {
                $scope.submitted = true;
                if ($scope.template.new_template_name) {
                    var request = {
                        device_type: 'web',
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.currentUser.user_id,
                        access_token: $rootScope.currentUser.access_token,
                        template_name: $scope.template.new_template_name,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id
                    };
                    PatientService
                            .saveNewTemplate(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $("#modal_savetemplate").modal("hide");
                                    $rootScope.app.isLoader = false;
                                    $scope.submitted = false;
                                    $scope.template.new_template_name = '';
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            /* add appointment modal code */
            $scope.addAppointmentClose = function () {
                $scope.addAppointmentOpen = false;
            }
            $scope.openAppointment = function () {
                if($scope.appointment.appointment_date == undefined) {
                	$scope.appointment.appointment_date = new Date();
                }
				if($rootScope.current_doctor != undefined && $rootScope.current_doctor.full_name != undefined && $rootScope.current_doctor.full_name != ''){
					$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.current_doctor.full_name;
				}else{
					$scope.appointment.doctor_name = $rootScope.docPrefix + $rootScope.currentUser.user_first_name + " " + $rootScope.currentUser.user_last_name;
				}
				$scope.appointment.appointment_type = "1";
				$scope.appointment.clinic_id = $rootScope.current_clinic.clinic_id;
				if ($scope.appointment && $scope.appointment.duration == undefined)
					$scope.appointment.duration = $rootScope.current_clinic.doctor_clinic_mapping_duration;
                //$scope.appointment.from_time = '';
                $scope.addAppointmentOpen = true;
                $(".outer_div").html('');
            }
            /* refresh dates after adding appointments */
            $scope.$on('refreshDates', function (e) {
                $scope.date_current_page = 1;
                $scope.getAppointmentDates();
                $scope.addAppointmentOpen = false;
            });
            $scope.$on('updatePatientData', function (e,patient_data) {
            	var listIndex = $scope.patientList.map(function (item) {
			            return item.user_id;
			        }).indexOf(patient_data.patient_id);
                if($scope.patientList[listIndex] != undefined) { 
                    $scope.patientList[listIndex].name = patient_data.user_first_name + ' ' + patient_data.user_last_name;
                    $scope.patientList[listIndex].user_name = patient_data.user_first_name + ' ' + patient_data.user_last_name;
                    $scope.patientList[listIndex].user_first_name = patient_data.user_first_name;
                    $scope.patientList[listIndex].user_last_name = patient_data.user_last_name;
                    if(patient_data.user_photo_filepath_thumb != '')
                    	$scope.patientList[listIndex].user_photo_filepath = patient_data.user_photo_filepath_thumb;
                    var pat_obj = {
            			doctor_id: $scope.patientList[listIndex].appointment_doctor_user_id,
                        appointment_clinic_id: $scope.patientList[listIndex].appointment_clinic_id,
                        user_id: $scope.patientList[listIndex].user_id,
                        appointment_id: $scope.patientList[listIndex].appointment_id
                    };
            		$scope.getDoctorPatientDetail(pat_obj);
            	}
            });

            /* report image remove code */
            $scope.removePatientReportImg = function (key) {
                if($scope.reportObj[key] != undefined)
                	$scope.reportObj[key].temp_img = '';
                if($scope.rxUploadObj[key] != undefined)
                	$scope.rxUploadObj[key].temp_img = '';
                $scope.is_change_prescription = false;
            }
            $scope.saveReporObj = function (key) {
                $scope.submitted = true;
                if (!$scope.reportObj[key].report_date ||
                        !$scope.reportObj[key].report_type ||
                        !$scope.reportObj[key].temp_img ||
                        !$scope.reportObj[key].img
                        ) {
                    $scope.reportObj[key].required_error = true;
                } else {
                    $scope.reportObj[key].required_error = false;
                    var month = $scope.reportObj[key].report_date.getMonth() + 1;
                    var day = $scope.reportObj[key].report_date.getDate();
                    var year = $scope.reportObj[key].report_date.getFullYear();
                    $scope.reportObj[key].real_report_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        report_type_id: $scope.reportObj[key].report_type,
                        date: $scope.reportObj[key].real_report_date,
                        report_name: $scope.reportObj[key].report_name,
                        img: $scope.reportObj[key].img
                    };
                    PatientService
                            .addReportToDB(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.reportObj = [{
                                            report_name: '',
                                            report_type: '',
                                            report_date: '',
                                            temp_img: '',
                                            img: ''
                                        }];
                                    $scope.getPatientReportDetail();
                                } else {
                                	ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.resetReportObj = function (key) {
                $scope.reportObj[key].report_name = '';
                $scope.reportObj[key].report_date = '';
                $scope.reportObj[key].report_type = '';
                $scope.reportObj[key].temp_img = '';
                $scope.reportObj[key].img = '';
                $scope.submitted = false;
                $scope.reportObj[key].required_error = false;
            }
            $scope.Model = $scope.Model || {currentImg: ''} || {currentPdf: ''};
            $scope.Model = $scope.Model || {currentImgClinicalNotes: ''};
            $scope.openFullImageModal = function (img_path, type) {
                $scope.Model.currentImg = img_path;
				$scope.Model.currentPdf = '';
				if(type=='file'){
					// $scope.Model.currentPdf = img_path; 
					var pdf_url = btoa(encodeURI($rootScope.app.base_url + 'pdf_preview/web/view_pdf.php?file_url=' + btoa(encodeURI(img_path))));
					$scope.Model.currentPdf = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + pdf_url; 
					$scope.Model.currentImg = '';
				}
                $("#fullscreen_img_modal").modal("show");
            }
            $scope.closeFullImgModal = function () {
                $scope.Model.currentImg = '';
                $scope.Model.currentImgClinicalNotes = '';
                $("#fullscreen_img_modal").modal("hide");
                $("#fullscreen_clinical_notes_modal").modal("hide");
            }
            /* add new report code */
            $scope.addReport = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    var month = $scope.report.report_date.getMonth() + 1;
                    var day = $scope.report.report_date.getDate();
                    var year = $scope.report.report_date.getFullYear();
                    $scope.report.real_report_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        report_type_id: $scope.report.report_type,
                        date: $scope.report.real_report_date,
                        report_name: $scope.report.report_name

                    };
                    PatientService
                            .addReportToDB(request, function (response) {
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.checkFormSubmit = function (e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            }

            /* clinical image preview */
            $scope.getFileClinicalNotes = function (fileObj, type) {

                var kco_array = [];
                var chief_compaints_array = [];
                var observation_array = [];
                var diagnosis_array = [];
                var notes_array = [];
                angular
                        .forEach($scope.testKCO, function (value, key) {
                            if (value.search) {
                                kco_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testChief, function (value, key) {
                            if (value.search) {
                                chief_compaints_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testObservation, function (value, key) {
                            if (value.search) {
                                observation_array.push(value.search);
                            }
                        });
                angular.forEach($scope.testDiagnosis, function (value, key) {
                            if (value.search) {
                                diagnosis_array.push(value.search);
                            }
                        });
                angular.forEach($scope.testNotes, function (value, key) {
                            if (value.search) {
                                notes_array.push(value.search);
                            }
                        });
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    date: $scope.current_appointment_date_obj.appointment_date,
                    kco: JSON.stringify(kco_array),
                    complaints: JSON.stringify(chief_compaints_array),
                    observation: JSON.stringify(observation_array),
                    diagnose: JSON.stringify(diagnosis_array),
                    notes: JSON.stringify(notes_array),
                    image: fileObj,
                    image_type: type

                };
                PatientService
                        .addClinicalNotesPatient(request, function (response) {

                            if (response.status) {

                                // $scope.getPatientReportDetail();
                                // $scope.getTableDataForNotes();
                                ngToast.success({
                                    content: response.message
                                });
                            } else {
                                ngToast.danger(response.message);
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.openImageForDelete = function (imageObj) {
                $scope.Model.currentImgClinicalNotes = imageObj;
                $("#show_clinical_notes_image_modal").modal("show");
            }
            /* delete img code */
            $scope.deleteClinicalImage = function () {
                var img_id = $scope.Model.currentImgClinicalNotes.clinic_notes_reports_images_id;
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    clinic_notes_image_id: img_id
                };
                PatientService
                        .deleteClinicalImage(request, function (response) {
                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                $("#show_clinical_notes_image_modal").modal("hide");
                            }
                            $scope.getPatientReportDetail();
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            /* add clinical notes form */
            $scope.addClinicalNotes = function (form) {

                var kco_array = [];
                var chief_compaints_array = [];
                var observation_array = [];
                var diagnosis_array = [];
                var notes_array = [];
                angular
                        .forEach($scope.testKCO, function (value, key) {
                            if (value.search) {
                                kco_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testChief, function (value, key) {
                            if (value.search) {
                                chief_compaints_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testObservation, function (value, key) {
                            if (value.search) {
                                observation_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testDiagnosis, function (value, key) {
                            if (value.search) {
                                diagnosis_array.push(value.search);
                            }
                        });
                angular
                        .forEach($scope.testNotes, function (value, key) {
                            if (value.search) {
                                notes_array.push(value.search);
                            }
                        });
                if (chief_compaints_array.length <= 0 && diagnosis_array.length <= 0 && notes_array.length <= 0 && observation_array.length <= 0) {
                    ngToast.danger('Please Enter Atleast One');
                    return;
                }

                var request = {
                    patient_id: $scope.current_patient.user_id,
                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    date: $scope.current_appointment_date_obj.appointment_date,
                    kco: JSON.stringify(kco_array),
                    complaints: JSON.stringify(chief_compaints_array),
                    observation: JSON.stringify(observation_array),
                    diagnose: JSON.stringify(diagnosis_array),
                    notes: JSON.stringify(notes_array),
                };
                PatientService
                        .addClinicalNotesPatient(request, function (response) {

                            if (response.status) {
                                ngToast.success({
                                    content: response.message
                                });
                                var pat_obj = {
                                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                                    user_id: $scope.current_patient.user_id,
                                    appointment_clinic_id: $rootScope.current_clinic.clinic_id
                                };
//                                $scope.getDoctorPatientDetail(pat_obj);
                                $scope.getPatientReportDetail();
                                $scope.getTableDataForNotes();
                            } else {
                                ngToast.danger(response.message);
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }

            /* clear clinical notes */
            $scope.removeClinicalNote = function (key, type) {
                if (type == 1) {
                    $scope.testChief[key].search = '';
                } else if (type == 2) {
                    $scope.testObservation[key].search = '';
                } else if (type == 3) {
                    $scope.testDiagnosis[key].search = '';
                } else if (type == 4) {
                    $scope.testNotes[key].search = '';
                } else if (type == 5) {
                    $scope.testKCO[key].search = '';
                }
                if($scope.focusIndex && $scope.focusIndex[type] != undefined)
					$scope.focusIndex[type] = Number(key);
			}
			
            /* add prescription code (RX) */
            $scope.addPrescription = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    $scope.common.next_folloup_date_new = '';
                    if ($scope.common.next_folloup_date) {
                        var month = $scope.common.next_folloup_date.getMonth() + 1; //months from 1-12
                        var day = $scope.common.next_folloup_date.getDate();
                        var year = $scope.common.next_folloup_date.getFullYear();
                        $scope.common.next_folloup_date_new = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    }
                    var drug_request_array = [];
                    SweetAlert.swal(
                            {
                                title: $rootScope.app.appoinment_add_rx_alert,
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
                                    $scope.submitted = false;
									angular.forEach($scope.brandList, function (brandValue, brandKey)
                                    {
                                        if ($scope.brandList[brandKey].similar_brand_id || $scope.brandList[brandKey].is_new_drug == true) {
                                            var custom_value = '';
                                            if ($scope.brandList[brandKey].default1 || $scope.brandList[brandKey].default2 || $scope.brandList[brandKey].default3) {
                                                if (!$scope.brandList[brandKey].default1) {
                                                    $scope.brandList[brandKey].default1 = '0';
                                                }
                                                if (!$scope.brandList[brandKey].default2) {
                                                    $scope.brandList[brandKey].default2 = '0';
                                                }
                                                if (!$scope.brandList[brandKey].default3) {
                                                    $scope.brandList[brandKey].default3 = '0';
                                                }
                                                custom_value = $scope.brandList[brandKey].default1 + '-' + $scope.brandList[brandKey].default2 + '-' + $scope.brandList[brandKey].default3;
                                            }
                                            var generic_json = '';
                                            if (brandValue.drug_drug_generic_id) {
                                                generic_json = brandValue.drug_drug_generic_id.join(',');
                                            }
                                            var id = '';
                                            if (brandValue.id) {
                                                id = brandValue.id;
                                            }
                                            var dosage = brandValue.dosage;
                                            if((brandValue.drug_unit_name=='Tablets' || brandValue.drug_unit_name =='IU') && brandValue.defaultFreqOpen) {
                                            	dosage = '';
                                            } else if(brandValue.drug_unit_name=='As Directed') {
                                            	dosage = '';
                                            }
                                            var is_delete = 2;
                                            var temp_obj = {
                                                drug_id: brandValue.similar_brand_id,
                                                drug_name: brandValue.similar_brand,
												drug_name_with_unit: brandValue.drug_name_with_unit,
                                                generic_id: generic_json,
                                                unit_id: brandValue.drug_unit_id,
                                                unit_value: brandValue.drug_unit_name,
                                                frequency_id: brandValue.drug_frequency_id,
                                                frequency_value: custom_value,
                                                dosage: dosage,
                                                frequency_instruction: brandValue.freq_instruction,
                                                intake: brandValue.drug_intake,
                                                intake_instruction: brandValue.intake_instruction,
                                                duration: brandValue.drug_duration,
                                                duration_value: brandValue.drug_duration_value,
                                                is_delete: is_delete,
                                                id: id
                                            };
                                            drug_request_array.push(temp_obj);
                                        }
                                    });
                                    if (drug_request_array.length == 0) {
                                        ngToast.danger("Please Select atleast one brand name");
                                        return;
                                    }
                                    if ($scope.brand_deleted_obj.length > 0) {
                                        angular
                                                .forEach($scope.brand_deleted_obj, function (deleteObj, deleteKey) {
                                                    var temp_obj = {
                                                        is_delete: 1,
                                                        id: deleteObj.id
                                                    }
                                                    drug_request_array.push(temp_obj);
                                                });
                                        $scope.brand_deleted_obj = [];
                                    }

                                    var drug_request_json = JSON.stringify(drug_request_array);
                                    if (drug_request_array.length > 0) {
                                        $scope.common.isForAdd = false;
                                        var inst = '';
                                        if ($scope.common.drug_instruction) {
                                            inst = $scope.common.drug_instruction;
                                        }
                                        var request = {
                                            drug_request_json: drug_request_json,
                                            patient_id: $scope.current_patient.user_id,
                                            appointment_id: $scope.current_appointment_date_obj.appointment_id,
                                            clinic_id: $rootScope.current_clinic.clinic_id,
                                            date: $scope.current_appointment_date_obj.appointment_date,
                                            next_follow_up: $scope.common.next_folloup_date_new,
                                            is_capture_compliance: $scope.common.is_capture_compliance,
                                            diet_instruction: inst,
                                            diet_instruction_id: $scope.diet_instruction_id
                                        };
                                        PatientService
                                                .addPrescription(request, function (response) {
                                                    $scope.suggestion_brand_result = [];
													$scope.rxCSectionSearch = '';
													$scope.diet_instruction_id = '';
                                                    if (response.status) {
                                                        $scope.common.isRXFormOpen = false;
                                                        $scope.getPatientReportDetail();
                                                        ngToast.success({
                                                            content: response.message
                                                        })
                                                    } else {
                                                        ngToast.danger(response.message);
                                                    }
                                                }, function (error) {
                                                    $rootScope.handleError(error);
                                                });
                                    } else {
                                        ngToast.danger("Please Select Atleast One Brand Name");
                                    }
                                }
                            }
                    );
                }else{
					angular.element("[name='" + form.$name + "']").find('.ng-invalid:visible:first').focus();
				}
            }
            $scope.AllergyAlert = function () {
            	var medicine_name = "<span style='float: left;width: 100%;text-align: left;padding-left: 48px;'>";
            	angular.forEach($scope.current_patient.medicineAllergies, function (allergy, key) {
            		medicine_name += (key+1) + ". " + allergy + "<br>";
            	});
            	medicine_name += "</span>";
            	SweetAlert.swal(
                        {
                            title: "Patient has allergy for the below listed medicines,",
                            text: medicine_name,
                            html:true,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Ok",
                            cancelButtonText: "No",
                            closeOnConfirm: true
                        },
                        function (isConfirm) {

                        }
                );
            }
            /* edit rx form */
            $scope.getEditRXObject = function () {
            	if($scope.is_notify_allegy_popup && $scope.current_appointment_date_obj.is_editable && $scope.current_patient.medicineAllergies != undefined && $scope.current_patient.medicineAllergies.length > 0)
					$scope.AllergyAlert();
                $scope.brandList = [];
                angular
                        .forEach($scope.addedBrandList, function (current_brand_obj, brandInnerKey) {

                            if ($scope.common.next_folloup_date_string) {
                                $scope.common.next_folloup_date = new Date($scope.common.next_folloup_date_string);
                            } else {
                                $scope.common.next_folloup_date = '';
                            }
                            $scope.common.isRXFormOpen = true;
                            var default_array = [];
                            var default1 = '';
                            var default2 = '';
                            var default3 = '';
                            if (current_brand_obj.prescription_frequency_value) {
                                default_array = current_brand_obj.prescription_frequency_value.split('-');
                                if (default_array) {
                                    default1 = default_array[0];
                                    default2 = default_array[1];
                                    default3 = default_array[2];
                                }
                            }
                            var isIntakeInst = false;
                            if (current_brand_obj.prescription_intake_instruction) {
                                isIntakeInst = true;
                            }
                            var freqInst = false;
                            if (current_brand_obj.prescription_frequency_instruction) {
                                freqInst = true;
                            }
                            $scope.brandList.push({
                                isOpenWholeForm: true,
                                similar_brand: current_brand_obj.drug_name,
								drug_name_with_unit: current_brand_obj.drug_name_with_unit,
                                is_edit: true,
                                drug_drug_generic_id: (current_brand_obj.prescription_generic_id != '') ? current_brand_obj.prescription_generic_id.split(',') : [],
                                dosage: current_brand_obj.prescription_dosage,
                                drug_frequency_id: current_brand_obj.prescription_frequency_id,
                                freq_instruction: current_brand_obj.prescription_frequency_instruction,
                                isFreqInst: freqInst,
                                drug_intake: current_brand_obj.prescription_intake,
                                intake_instruction: current_brand_obj.prescription_intake_instruction,
                                isIntakeInst: isIntakeInst,
                                drug_duration_value: current_brand_obj.prescription_duration_value,
                                drug_duration: current_brand_obj.prescription_duration,
                                drug_instruction: current_brand_obj.prescription_diet_instruction,
                                id: current_brand_obj.prescription_id,
                                similar_brand_id: current_brand_obj.prescription_drug_id,
                                drug_unit_id: current_brand_obj.prescription_unit_id,
                                drug_unit_name: current_brand_obj.drug_unit_name,
                                defaultFreqOpen: (current_brand_obj.prescription_dosage == '' && (current_brand_obj.drug_unit_name == 'Tablets' || current_brand_obj.drug_unit_name == 'IU')) ? true : false,
                                default1: default1,
                                default2: default2,
                                default3: default3
                            });
                        });
                $scope.brandList.push({});
            }
            $scope.cancelBtnRX = function () {
            	$scope.is_change_prescription = false;
                $scope.suggestion_brand_result = [];
				$scope.rxCSectionSearch = '';
                $scope.getPatientReportDetail();
                $scope.common.isRXFormOpen = false;
                $scope.common.isForAdd = false;
                if ($scope.common.isRXFormOpen) {
                    $scope.common.isRXFormOpen = false;
                }
                if ($scope.common.isOpenForEdit) {
                    $scope.common.isOpenForEdit = false;
                } else {
                    $scope.vital = {};
                    $scope.vital.pulse = '';
                    $scope.vital.teperature_taken_id = 6;
                }
                $scope.brandList = [{
                        brand_name: '',
                        isOpen: false,
                        default1: '',
                        default2: '',
                        default3: '',
                    },
                ];
                if ($scope.common.isInvestigationOpen) {
                    $scope.common.isInvestigationOpen = false;
                    $scope.testLabs = [{
                            search: ''
                        }];
                    $scope.template.lab_instruction = '';
                }
                if ($scope.common.isProcOpen) {
                    $scope.common.isProcOpen = false;
                    $scope.procedureObj = [{
                            text: ''
                        }];
                    $scope.procedure = {};
                    $scope.procedure.note = '';
                }
                if ($scope.common.isOpenForEdit) {
                    $scope.common.isOpenForEdit = false;
                }

                $scope.testLabs = [{
                    }];
                $scope.template.lab_instruction = '';
                $scope.procedureObj = [{
                        text: ''
                    }];
                $scope.procedure.note = '';
            }
            /* delete RX object */
            $scope.deleteRX = function (id) {
                var request = {
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    id: id,
                    patient_id: $scope.current_patient.user_id
                };
                SweetAlert.swal({
                    title: "Are you sure want to delete this RX?",
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
                            PatientService
                                    .deleteRX(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.brandList = [{
                                                    brand_name: '',
                                                    isOpen: false,
                                                    default1: '',
                                                    default2: '',
                                                    default3: '',
                                                },
                                            ];
                                            $scope.submitted = false;
                                            $scope.getPatientReportDetail();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }
            /* add investigation code */
            $scope.removeLabInvestigationObj = function (key) {
                $scope.testLabs.splice(key, 1);
                $scope.prescriptionOnchangeFlag();
            }
			
            $scope.addInvestigation = function (form) {
                $scope.submitted = true;
                if (!$scope.testLabs[0].search) {
                    $scope.lab_test_error = true;
                } else {
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        date: $scope.current_appointment_date_obj.appointment_date,
                    };
                    var lab_array = [];
                    var temp = {}; var temp_other = {};
                    angular.forEach($scope.testLabs, function (value, key) {
								if (!value.lab_instruction){
									value.lab_instruction = '';
								}
								if (value.search && key != ($scope.testLabs.length - 1)) {
                                    temp[value.search] = value.lab_instruction;
                                } else if(value.search != undefined && value.search != '') {
									temp_other[value.search] = value.lab_instruction;
								}
                            });
                    request.lab_test_name = JSON.stringify(temp);
					request.lab_test_name_other = JSON.stringify(temp_other);
                    if (request.lab_test_name == "{}" && request.lab_test_name_other == "{}") {
                        ngToast.danger("Please Select Valid Test Name");
                        return;
                    }
					
                    $scope.lab_test_error_invalid = false;
                    request.instruction = '';
                    $scope.lab_test_error = false;
                    if ($scope.common.isInvestigationOpen) {
                        request.id = $scope.addedInvestigation.id;
                        PatientService
                                .editInvestigationReport(request, function (response) {

                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.common.isInvestigationOpen = false;
                                        $scope.getPatientReportDetail();
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    } else {
                        PatientService
                                .addInvestigationReport(request, function (response) {

                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.getPatientReportDetail();
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }
            }


            /* procedure module start */
            $scope.addMoreProcedure = function (isAdd, index) {
                if (isAdd) {
                    $scope.procedureObj.push({
                        text: ''
                    });
                } else {
                    $scope.procedureObj.splice(index, 1);
                    $scope.prescriptionOnchangeFlag();
                }
                $scope.cSearchProc = '';
                $scope.procCurrentSearchKey = '';
            }

            /* add new procedure */
            $scope.addProcedure = function (form) {

                $scope.submitted = true;
                if (form.$valid) {
                    $scope.submitted = false;
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        date: $scope.current_appointment_date_obj.appointment_date,
                    };
                    var proc_array = [];
                    angular
                            .forEach($scope.procedureObj, function (value, key) {
                                if (value.text) {
                                    proc_array.push(value.text);
                                }
                            });
                    request.procedure = JSON.stringify(proc_array);
                    request.procedure_note = $scope.procedure.note;
                    if ($scope.common.isProcOpen) {
                        request.id = $scope.addedProcedure.id;
                        PatientService
                                .editProcedureReport(request, function (response) {
                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        })
                                        $scope.common.isProcOpen = false;
                                        $scope.getPatientReportDetail();
                                        $scope.procedureObj = [{
                                                text: ''
                                            }];
                                        $scope.cSearchProc = '';
                						$scope.procCurrentSearchKey = '';
                                        $scope.procedure.note = '';
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    } else {
                        PatientService
                                .addProcedureReport(request, function (response) {
                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.getPatientReportDetail();
                                        $scope.procedureObj = [{
                                                text: ''
                                            }];
                                        $scope.cSearchProc = '';
                						$scope.procCurrentSearchKey = '';
                                        $scope.procedure.note = '';
                                    } else {
                                        ngToast.danger(response.message);
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }
            }
            /**/
            $scope.openEditForm = function (type) {
            	$scope.is_change_prescription = false;
                $scope.common = {};
                $scope.common.isForAdd = false;
                if (type == 1) {
                    $scope.common.isOpenForEdit = true;
                } else if (type == 2) {
                    $scope.common.isClinicalForEdit = true;
                } else if (type == 3) {
                    $scope.common.isRXFormOpen = true;
                    $scope.common.isForAdd = true;
                    $scope.brandList = [{
                            brand_name: '',
                            isOpen: false,
                            default1: '',
                            default2: '',
                            default3: '',
                            drug_duration: '1'
                        },
                    ];
                } else if (type == 4) {
                    $scope.common.isInvestigationOpen = true;
                    $scope.testLabs = [];
                    delete $scope.addedInvestigationObj.keys;
                    for (var k in $scope.addedInvestigationObj) {
                        var isopen = false;
                        if ($scope.addedInvestigationObj[k]) {
                            isopen = true;
                        }
                        $scope.testLabs.push({
                            search: k,
                            lab_instruction: $scope.addedInvestigationObj[k],
                            isOpenInst: isopen
                        });
                    }
                    $scope.testLabs.push({
                        search: ''
                    })
                } else if (type == 5) {
                    $scope.procedureObj = [];
                    angular
                            .forEach($scope.addedProcedureObj, function (value, key) {
                                $scope.procedureObj.push({
                                    text: value
                                })
                            });
                    $scope.procedure.note = $scope.addedProcedure.note;
                    $scope.common.isProcOpen = true;
                }
            }
            $scope.removeVital = function () {
                //call vital api
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    doctor_id: $rootScope.currentUser.user_id,
                    appointment_id: $scope.current_appointment_date_obj.appointment_id
                };
                PatientService.deleteVital(request, function (response) {
                    if (response.status) {
                        $scope.getPatientReportDetail();
                    } else {
                        ngToast.danger(response.message);
                    }
                }, function (error) {

                });
            }

            $scope.trustSrc = function (src) {
                return $sce.trustAsResourceUrl(src);
            }
			
			$scope.safeTrustAsHtml = function (htmlCode) {
                return $sce.trustAsHtml(htmlCode);
            }

            /* health analytics module */
            /* get health analytics  */
            $scope.health = {};
            $scope.health.search = '';
            $scope.health.data = [];
            $scope.health.subData = [];
            $scope.getHealthAnalyticsTest = function (id, isForChild, key) {
            	if($rootScope.currentUser.sub_plan_setting != undefined && $rootScope.currentUser.sub_plan_setting.medsign_speciality_id != undefined) {
            		var medsign_speciality_id = $rootScope.currentUser.sub_plan_setting.medsign_speciality_id;
            	} else {
            		var medsign_speciality_id = '';
            	}
                //$scope.changePatientReportTab('patient_analytics_value', 7)
                if (isForChild && ($scope.health.data[key].is_selected == 1)) {
                    var child_request = {
                        search: '',
                        parent_id: id,
                        medsign_speciality_id: medsign_speciality_id,
                        patient_id: $scope.current_patient.user_id
                    };
                    PatientService.getHealthTestForPatient(child_request, false, function (response) {
                        angular
                                .forEach(response.data, function (subvalue, subkey) {
                                    var subdata_flag = true;
                                    angular
                                            .forEach($scope.health.subData, function (innerSubObj, innerSubKey) {
                                                if (innerSubObj.health_analytics_test_id == subvalue.health_analytics_test_id) {
                                                    subdata_flag = false;
                                                }
                                            });
                                    if (subdata_flag) {
                                        $scope.health.subData.push(subvalue);
                                    }
                                });
                    }, function (error) {
                        $rootScope.handleError(error);
                    });
                } else {
                    if (isForChild && !$scope.health.data[key].isSelected) {
                        var new_sub_array = [];
                        angular
                                .forEach($scope.health.subData, function (subvalue, innerKey) {
                                    if ((subvalue.health_analytics_test_parent_id != $scope.health.data[key].health_analytics_test_id) || subvalue.is_editable == 2) {
                                        new_sub_array.push(subvalue);
                                    }
                                });
                        $scope.health.subData = new_sub_array;
                    } else {
                        var search = $scope.health.search;
                        if (isForChild) {
                            search = '';
                        }

                        var parent_id = 0;
                        if (id) {
                            parent_id = id;
                        }
                        var request = {
                            search: search,
                            parent_id: parent_id,
                            medsign_speciality_id: medsign_speciality_id,
                            patient_id: $scope.current_patient.user_id
                        }
                        PatientService
                                .getHealthTestForPatient(request, false, function (response) {

                                    if (isForChild) {
                                        angular
                                                .forEach(response.data, function (value, key) {
                                                    $scope.health.subData.push(value);
                                                });
                                    } else {
                                        $scope.health.data = response.data;
                                        //$scope.health.subData = [];
                                        var flag = true;
                                        angular
                                                .forEach($scope.health.data, function (healthObj, healthKey) {
                                                    if (healthObj.is_checked == 1 && flag == true) {
//                                                        $scope.getHealthAnalyticsTest(healthObj.health_analytics_test_id, true, healthKey);
                                                        var child_request = {
                                                            search: '',
                                                            parent_id: healthObj.health_analytics_test_id,
                                                            medsign_speciality_id: medsign_speciality_id,
                                                            patient_id: $scope.current_patient.user_id
                                                        };
                                                        PatientService.getHealthTestForPatient(child_request, false, function (response) {
                                                            angular
                                                                    .forEach(response.data, function (subvalue, subkey) {
                                                                        var subdata_flag = true;
                                                                        angular
                                                                                .forEach($scope.health.subData, function (innerSubObj, innerSubKey) {
                                                                                    if (innerSubObj.health_analytics_test_id == subvalue.health_analytics_test_id) {
                                                                                        subdata_flag = false;
                                                                                    }
                                                                                });
                                                                        if (subvalue.is_checked != 1) {
                                                                            subdata_flag = false;
                                                                        }
                                                                        if (subdata_flag) {
                                                                            $scope.health.subData.push(subvalue);
                                                                        }
                                                                    });
                                                        }, function (error) {
                                                            $rootScope.handleError(error);
                                                        });
                                                    }
                                                });
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }

            }

            $scope.removeSubHealthObj = function (key) {
                $scope.health.subData.splice(key, 1);
            }
            $scope.health.healthAnalyticsObj = [];
            $scope.health.healthAnalyticsObjWithoutValue = [];
            $scope.addHealthComponents = function () {
                if ($scope.health.subData.length <= 0) {
                    ngToast.danger('Select Atleast one value');
                    return;
                }
                var flag = '';
                var tempHealth = $scope.health.healthAnalyticsObj;
                $scope.health.healthAnalyticsObj = [];
                $scope.health.healthAnalyticsObjWithoutValue = [];
				//angular
				//        .forEach($scope.health.real_healthAnalyticsObjWithoutValue, function (healthObj, key) {
				//            $scope.health.healthAnalyticsObj.push({
				//                id: healthObj.id,
				//                name: healthObj.name,
				//                value: healthObj.value,
				//            });
				//            $scope.health.healthAnalyticsObjWithoutValue.push({
				//                id: healthObj.id,
				//                name: healthObj.name,
				//            });
				//});
                angular
                        .forEach($scope.health.subData, function (value, key) {

                            flag = true;
                            angular.forEach($scope.health.healthAnalyticsObj, function (innerValue, key) {
                                if (innerValue.id == value.health_analytics_test_id) {
                                    flag = false;
                                }
                            });
                            
                            if (flag) {
                                $scope.health.healthAnalyticsObj.push({
                                    id: value.health_analytics_test_id,
                                    name: value.health_analytics_test_name,
                                    value: '',
                                    doctor_id: value.patient_analytics_doctor_id,
                                    precise_name: value.health_analytics_test_name_precise,
                                    health_analytics_test_validation: JSON.parse(value.health_analytics_test_validation)
                                });
                                $scope.health.healthAnalyticsObjWithoutValue.push({
                                    id: value.health_analytics_test_id,
                                    name: value.health_analytics_test_name,
                                    doctor_id: value.patient_analytics_doctor_id,
                                    precise_name: value.health_analytics_test_name_precise,
                                    health_analytics_test_validation: JSON.parse(value.health_analytics_test_validation)
                                });
                            }
                        });
                /* var request = {
                 patient_id: $scope.current_patient.user_id,
                 appointment_id: $scope.current_appointment_date_obj.appointment_id,
                 date: $scope.current_appointment_date_obj.appointment_date,
                 clinic_id: $rootScope.current_clinic.clinic_id,
                 health_analytics_data: JSON.stringify($scope.health.healthAnalyticsObj),
                 health_analytics_test: JSON.stringify($scope.health.healthAnalyticsObjWithoutValue),
                 health_analytics_id: $scope.health.id
                 };
                 
                 if ($scope.health.id) {
                 PatientService
                 .editAnalyticsValue(request, function (response) {
                 $scope.getTableDataHealthAnalytics(true);
                 if (response.status) {
                 ngToast.success({
                 content: response.message
                 });
                 $scope.getPatientReportDetail();
                 $scope.getTableDataHealthAnalytics(true);
                 }
                 }, function (error) {
                 $rootScope.handleError(error);
                 });
                 
                 } else {
                 PatientService
                 .addAnalyticsValue(request, function (response) {
                 
                 if (response.status) {
                 ngToast.success({
                 content: response.message
                 });
                 $scope.getPatientReportDetail();
                 $scope.getTableDataHealthAnalytics(true);
                 }
                 }, function (error) {
                 $rootScope.handleError(error);
                 });
                 } */
                $("#modal_health_analytics").modal("hide");
            }
            $scope.clearHealthAnalyticsValue = function () {
                angular.forEach($scope.health.healthAnalyticsObj, function (innerValue, key) {
                    innerValue.value = '';
                });
            }
            $scope.checkValidationAnalytics = function (type, form) {
                if (type == 1) {
                    if (form.healthname) {
                        var value = Number(form.healthname);
                        if (isNaN(value)) {
                            form.healthname.$setValidity("pattern", false);
                            return;
                        } else {
                            form.healthname.$setValidity("pattern", true);
                        }
                    } else {
                        form.healthname.$setValidity("pattern", true);
                    }
                }
            };
            $scope.health_analytics_validation = function (validation, form, key, validationErrEid) {
				if(validation == undefined || validation.length ==0)
					return;

                var max 		 = validation.validation.max;
                var min 		 = validation.validation.min;
                var input_number = form.healthname.$viewValue;
                var type 		 = validation.validation.type;
                var gender 		 = $scope.current_patient.user_gender;
                var is_numeric = 1;
				
				var validationKeySelectorId = '#analytics_error_';
				if(validationErrEid != undefined && validationErrEid != '')
				   validationKeySelectorId = '#'+validationErrEid
			   
                if (input_number == "") {
                    form.healthname.$setValidity("pattern", true);
                    return;
                }
                input_number = Number(form.healthname.$viewValue);
                if (type == 'numeric') {
                    if (isNaN(input_number)) {
                        form.healthname.$setValidity("pattern", false);
                        var message = "Invalid Number";
                        var errorElement = angular.element(document.querySelector(validationKeySelectorId + key));
                        errorElement.html(message);
                        return;
                    }
                } else {
                    is_numeric = 2;
                }

                var temp = 0;
                if ($.isArray(validation.validation.gender)) {
                    temp = validation.validation.gender.length;
                } else {
                    temp = Object.keys(validation.validation.gender).length;
                }
                if (is_numeric == 1) {
                    if (gender == '' || temp == 0) {
                        if ((input_number > max || input_number < min)) {
                            form.healthname.$setValidity("pattern", false);
                            var message = "Min is " + min + " Max is " + max;
                            var errorElement = angular.element(document.querySelector(validationKeySelectorId + key));
                            errorElement.html(message);
                            return;
                        } else {
                            form.healthname.$setValidity("pattern", true);
                        }
                    } else if (validation.validation.gender.female.max != undefined && validation.validation.gender.female.min != undefined && gender == 'female') {
                        var fmax = validation.validation.gender.female.max;
                        var fmin = validation.validation.gender.female.min;
                        if (input_number > fmax || input_number < fmin) {
                            form.healthname.$setValidity("pattern", false);
                            var message = "Min is " + fmin + " Max is " + fmax;
                            var errorElement = angular.element(document.querySelector(validationKeySelectorId + key));
                            errorElement.html(message);
                            return;
                        } else {
                            form.healthname.$setValidity("pattern", true);
                        }
                    } else if (validation.validation.gender.male.max != undefined && validation.validation.gender.male.min != undefined && gender != 'female') {
                        var mmax = validation.validation.gender.male.max;
                        var mmin = validation.validation.gender.male.min;
                        if (input_number > mmax || input_number < mmin) {
                            form.healthname.$setValidity("pattern", false);
                            var message = "Min is " + mmin + " Max is " + mmax;
                            var errorElement = angular.element(document.querySelector(validationKeySelectorId + key));
                            errorElement.html(message);
                            return;
                        } else {
                            form.healthname.$setValidity("pattern", true);
                        }
                    } else {
                        form.healthname.$setValidity("pattern", true);
                    }
                }
            };

            /* add health analytics form */
            $scope.addAnalyticsValue = function (form) {
                var is_added_one_value = false;
                $scope.submitted = true;
                /*
                 angular
                 .forEach($scope.health.healthAnalyticsObj, function (value, key) {
                 if (value.value) {
                 is_added_one_value = true;
                 }
                 });
                 if (!is_added_one_value) {
                 ngToast.danger("Please Add Atleast One Value");
                 return;
                 }
                 */
                if (form.$valid) {
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        date: $scope.current_appointment_date_obj.appointment_date,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        health_analytics_data: JSON.stringify($scope.health.healthAnalyticsObj),
                        health_analytics_test: JSON.stringify($scope.health.healthAnalyticsObjWithoutValue),
                        health_analytics_id: $scope.health.id
                    };
                    if ($scope.health.id) {
                        PatientService
                                .editAnalyticsValue(request, function (response) {
                                    $scope.getTableDataHealthAnalytics(true);
                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.getPatientReportDetail();
                                        $scope.getTableDataHealthAnalytics(true);
                                    } else {
                                        ngToast.danger({
                                            content: response.message
                                        });
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    } else {
                        PatientService
                                .addAnalyticsValue(request, function (response) {

                                    if (response.status) {
                                        ngToast.success({
                                            content: response.message
                                        });
                                        $scope.getPatientReportDetail();
                                        $scope.getTableDataHealthAnalytics(true);
                                    } else {
                                        ngToast.danger({
                                            content: response.message
                                        });
                                    }
                                }, function (error) {
                                    $rootScope.handleError(error);
                                });
                    }
                }
            }
            /* health analytics module */
            /* refer modal */
            $scope.fav_search = {};
            $scope.fav_search.search = '';
            $scope.getFavListing = function () {
                var request = {
                    search: $scope.fav_search.search
                };
                SettingService
                        .getFavListingDB(request, function (response) {

                            if (response.status) {
                                $scope.fav_doctors = response.data;
                                if ($scope.fav_doctors) {
                                    $scope.fav_search.selected = $scope.fav_doctors[0].user_id;
                                }
                            }
                        });
            }
            $scope.addReferal = function () {
                if ($scope.fav_search.selected) {
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        other_doctor_id: $scope.fav_search.selected,
                    };
                    PatientService
                            .addReferalDoctor(request, function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    $("#modal_refer").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            /* follow up template start */
            $scope.getFollowupTemplate = function () {
                if ($scope.followup.is_selected_follow) {
                    //call api for copying followup template
                    var request = {
                        new_appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        old_appointment_id: $scope.followup.is_selected_follow.appointment_id,
                        new_date: $scope.current_appointment_date_obj.appointment_date,
                        old_date: $scope.followup.is_selected_follow.appointment_date,
                        patient_id: $scope.current_patient.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id
                    }


                    PatientService
                            .copyFollowUpTemplate(request, function (response) {

                                if (response.status) {
                                    $timeout(function () {
                                        $scope.getPatientReportDetail();
                                    }, 2000);
                                    $("#modal_followuptemplate").modal("hide");
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                } else {
                    ngToast.danger("Please Select Atleast One Followup Template");
                }
            }
            /* table graph data */
            $scope.vital_table_data = [];
            $scope.vital_current_page = 1;
            $scope.vital_filter_flag = 1;
            $scope.vital_head_class = 'th_width_20';
            $scope.changeTableFlag = function (flag) {
                $scope.vital_current_page = 1;
                $scope.vital_filter_flag = flag;
                if ($scope.current_patient.user_id) {
                    $scope.getTableData();
                }
            }

            $scope.fullScreenVital = function (flag, event) {
                $scope.vital_current_page = 1;
                if($(event.target).html() == 'Normal') {
                    $scope.fullScreenPageLimit = 5;   
                    $scope.vital_head_class = 'th_width_20'; 
                } else {
                    $scope.fullScreenPageLimit = 10;
                    $scope.vital_head_class = 'th_width_10';
                }
                $scope.vital_filter_flag = flag;
                if ($scope.current_patient.user_id) {
                    $scope.getTableData();
                }
            }

            $scope.getTableData = function (btn) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.vital_current_page <= 1) {
                            return;
                        }
                        $scope.vital_current_page = Number($scope.vital_current_page) - 1;
                    } else {
                        var total_pages = Math.ceil($scope.total_count / $scope.fullScreenPageLimit);
                        if (total_pages <= $scope.vital_current_page) {
                            return;
                        }
                        $scope.vital_current_page = Number($scope.vital_current_page) + 1;
                    }
                }
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: $scope.vital_filter_flag,
                };
                request.page = $scope.vital_current_page;
                request.per_page = $scope.fullScreenPageLimit;
                PatientService
                        .getTableGraphDataVital(request, function (response) {

                            $scope.total_count = response.total_count;
                            if (response.data) {
                                $scope.vital_table_data = response.data;
                                angular.forEach($scope.vital_table_data, function (value, key) {
                                	$scope.vital_table_data[key].temp_taken = '';
                                	$scope.vital_table_data[key].vital_bloodpressure_taken = '';
                                	if(value.vital_report_temperature_taken != undefined && value.vital_report_temperature_taken != null && value.vital_report_temperature_taken != '') {
                                		var selectedDataObj = $filter('filter')($scope.tempearture_taken, {'id':parseInt(value.vital_report_temperature_taken)},true);
                                		if(selectedDataObj != undefined && selectedDataObj[0] != undefined && selectedDataObj[0].name != undefined)
                                			$scope.vital_table_data[key].temp_taken = selectedDataObj[0].name;
                                	}
                                	if(value.vital_report_bloodpressure_type != undefined && value.vital_report_bloodpressure_type != null && value.vital_report_bloodpressure_type != '') {
                                		var selectedDataObj = $filter('filter')($scope.vital_bloodpressure_predefine, {'id':parseInt(value.vital_report_bloodpressure_type)},true);
                                		if(selectedDataObj != undefined && selectedDataObj[0] != undefined && selectedDataObj[0].name != undefined)
                                			$scope.vital_table_data[key].vital_bloodpressure_taken = selectedDataObj[0].name;
                                	}
                                });
                            } else {
                                $scope.vital_table_data = [];
                            }
                            $scope.renderLineChart($scope.vital_table_data,$scope.active_vital_name);
                            
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.notes_table_data = [];
            $scope.notes_current_page = 1;
            $scope.notes_filter_flag = 1;
            $scope.getTableDataForNotes = function (btn) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.notes_current_page <= 1) {
                            return;
                        }
                        $scope.notes_current_page = Number($scope.notes_current_page) - 1;
                    } else {
                        var total_pages = Math.ceil($scope.notes_total_count / 5);
                        if (total_pages <= $scope.notes_current_page) {
                            return;
                        }
                        $scope.notes_current_page = Number($scope.notes_current_page) + 1;
                    }
                }
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: $scope.notes_filter_flag,
                    report_id: ''
                };
                request.page = $scope.notes_current_page;
                request.per_page = 5;
                PatientService
                        .getTableGraphDataNotes(request, function (response) {
                            $scope.notes_total_count = response.total_count;
                            if (response.data) {
                            	$rootScope.gTrack('c_clinical_notes');
                                $scope.notes_table_data = response.data;
                                if ($scope.notes_table_data.length > 0) {
                                    $scope.getDetailTableNotes($scope.notes_table_data[0].clinical_notes_reports_id);
                                } else {
                                    $scope.detail_notes = {};
                                }
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.detail_notes = {};
            $scope.getDetailTableNotes = function (report_id) {

                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: $scope.notes_filter_flag,
                    report_id: report_id
                };
                PatientService
                        .getTableGraphDataNotes(request, function (response) {

                            if (response.data) {
                                $scope.detail_notes = response.data[0];
                                if ($scope.detail_notes.clinical_notes_reports_complaints) {
                                    $scope.detail_notes.clinical_notes_reports_complaints = JSON.parse($scope.detail_notes.clinical_notes_reports_complaints);
                                }
                                if ($scope.detail_notes.clinical_notes_reports_observation) {
                                    $scope.detail_notes.clinical_notes_reports_observation = JSON.parse($scope.detail_notes.clinical_notes_reports_observation);
                                }
                                if ($scope.detail_notes.clinical_notes_reports_diagnoses) {
                                    $scope.detail_notes.clinical_notes_reports_diagnoses = JSON.parse($scope.detail_notes.clinical_notes_reports_diagnoses);
                                }
                                if ($scope.detail_notes.clinical_notes_reports_add_notes) {
                                    $scope.detail_notes.clinical_notes_reports_add_notes = JSON.parse($scope.detail_notes.clinical_notes_reports_add_notes);
                                }
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.patient_health_data = [];
            $scope.patient_health_analysis_data = [];
            $scope.patient_health_current_page = 1;
            $scope.patient_health_total_records = '';
            $scope.getTableDataHealthAnalytics = function (isFromMenu, btn) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.patient_health_current_page <= 1) {
                            return;
                        }
                        $scope.patient_health_current_page = Number($scope.patient_health_current_page) - 1;
                    } else {
                        var total_pages = Math.ceil($scope.patient_health_total_records / 5);
                        if (total_pages <= $scope.patient_health_current_page) {
                            return;
                        }
                        $scope.patient_health_current_page = Number($scope.patient_health_current_page) + 1;
                    }
                }
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: $scope.notes_filter_flag,
                    page: $scope.patient_health_current_page,
                    per_page: 5
                };
                if (isFromMenu) {
                    PatientService
                            .getPatientHealthAnalytics(request, function (response) {
                                $scope.patient_health_data = response.data;
                                $rootScope.gTrack('c_health_analytics');
                                if($scope.patient_health_data != undefined && $scope.patient_health_data.length > 0)
                                	$scope.active_analytics_id = $scope.patient_health_data[0].patient_analytics_analytics_id;
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                    $scope.patient_health_current_page = 1;
                }

                PatientService.getTableGraphDataHealthAnalytics(request, function (response) {
                			$scope.patient_health_analysis_data = [];
                            if (response.data) {
                                $scope.patient_health_total_records = response.total_count;
                                $scope.patient_health_analysis_data = response.data;
                                angular.forEach($scope.patient_health_analysis_data, function (value, key) {
									if(value.health_analytics_report_data != undefined && value.health_analytics_report_data.length > 0){
										var objJsonParse = [];
										var health_analytics_report_data_list = value.health_analytics_report_data.split('$@@$');
										for(var i=0; i < health_analytics_report_data_list.length; i++){
											objJsonParse = objJsonParse.concat(JSON.parse(health_analytics_report_data_list[i]));
										}
										value.health_analytics_report_data_array = objJsonParse;
									}
								});
								$scope.renderHealthAnalyticsLineChart($scope.patient_health_analysis_data,$scope.active_analytics_id);
                            }
				}, function (error) {
					$rootScope.handleError(error);
				});
            }

            $scope.report_data = [];
            $scope.report_search = {
            	report_type_id: '',
            	search: '',
            	per_page: 10,
            	currentPage: 1,
            	total_page: 0,
            	total_rows: 0,
            	last_rows: 0
            }
            $scope.getTableDataReports = function (rptTypeId) {
				if(rptTypeId != undefined && rptTypeId.length > 0){
					$scope.report_search.report_type_id = rptTypeId;
				} else {
					$scope.report_search.report_type_id = '';
					$(".report-type-tab").removeClass('active');
				}
				$scope.report_search.search = '';
				$scope.searchReports(1);
				$rootScope.gTrack('c_reports');
            }
            $scope.searchReports = function (number) {
            	$scope.report_search.currentPage = number;
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: 1,
                    rptTypeId: $scope.report_search.report_type_id,
                    search: $scope.report_search.search,
                    page: $scope.report_search.currentPage,
                    per_page: $scope.report_search.per_page
                };				
				PatientService.getTableGraphDataReports(request, function (response) {
					if (response.data) {
						$scope.report_data = response.data;
						$scope.report_search.total_rows = response.total_count;
                        $scope.report_search.total_page = Math.ceil(response.total_count/$scope.report_search.per_page);
                        $scope.report_search.last_rows = $scope.report_search.currentPage*$scope.report_search.per_page;
                        if($scope.report_search.last_rows > $scope.report_search.total_rows)
                        	$scope.report_search.last_rows = $scope.report_search.total_rows;
					}
				}, function (error) {
					$rootScope.handleError(error);
				});
            }
            $scope.getNextPrev = function (val) {
				if(val == 'next') {
					if($scope.report_search.currentPage >= $scope.report_search.total_page)
						return false;
					var number = $scope.report_search.currentPage+1;
				}
				if(val == 'prev') {
					if($scope.report_search.currentPage <= 1)
						return false;
					var number = $scope.report_search.currentPage-1;
				}
				$scope.searchReports(number);
			}
            $scope.report_detail = {};
            $scope.openReportDetail = function (report_id,indx) {
				$scope.isNextRptShow = false; $scope.isPreRptShow = false;
				$scope.nxtPreRptObj = {};
				if($scope.report_data != undefined && $scope.report_data.length > 0 && indx != undefined){
					var pre = 0; var nxt = 0;
					if(indx >= 0){ nxt = indx + 1; pre = indx - 1; }
					if(nxt < $scope.report_data.length){
						$scope.nxtPreRptObj.nextRptObj = {'id':$scope.report_data[nxt].file_report_id,'indx':nxt};
						$scope.isNextRptShow = true;
					}
					if(pre >= 0){
						$scope.nxtPreRptObj.preRptObj = {'id':$scope.report_data[pre].file_report_id,'indx':pre};
						$scope.isPreRptShow = true;
					}
				}
				
				var request = {
                    report_id: 	report_id,
                    patient_id: $scope.current_patient.user_id,
                    clinic_id:  $rootScope.current_clinic.clinic_id,
                    report_type_id: $scope.report_data[indx].report_type_id
                }
                PatientService
                        .getReportDetail(request, function (response) {
                            if (response.data) {
                                $scope.report_detail = response.data;
                                $scope.zoomtest = '';
                                $("#modal_report_detail").modal("show");
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.customzoom = {};
            $scope.customzoom.height = '50px';
            $scope.customzoom.width = '50px';
            $scope.customzoom.scale = '1';
            $scope.zoomtest = 'scale(' + $scope.customzoom.scale + ',' + $scope.customzoom.scale + ')';
            $scope.zoomplus = function (point) {
                var new_scale = Number($scope.customzoom.scale) + Number(point);
                if (new_scale < 1) {
                    return;
                }
                $scope.customzoom.scale = Number($scope.customzoom.scale) + Number(point);
                $scope.zoomtest = 'scale(' + $scope.customzoom.scale + ',' + $scope.customzoom.scale + ')';
            }
            $scope.getMyProcData = function () {
                var request = {
                    doctor_id: $rootScope.current_doctor.user_id,
                    device_type: 'web',
                	user_id: $rootScope.currentUser.user_id,
                	access_token: $rootScope.currentUser.access_token
                };
                PatientService
                        .getMyProcData(request, function (response) {
                            $scope.my_proc_data = response.data;
                            $rootScope.gTrack('c_my_procedure');
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.patient_proc_current_page = 1;
            $scope.patient_proc_total_records = '';
            $scope.patient_proc_data = [];
            $scope.getTableProcData = function (isFromMenu, btn) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.patient_proc_current_page <= 1) {
                            return;
                        }
                        $scope.patient_proc_current_page = Number($scope.patient_proc_current_page) - 1;
                    } else {
                        var total_pages = Math.ceil($scope.patient_proc_total_records / 5);
                        if (total_pages <= $scope.patient_proc_current_page) {
                            return;
                        }
                        $scope.patient_proc_current_page = Number($scope.patient_proc_current_page) + 1;
                    }
                }

                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: 1,
                    page: $scope.patient_proc_current_page,
                    per_page: 5,
                    procedure_report_id: ''
                };
                if (isFromMenu) {
                    $scope.patient_proc_total_records = '';
                    $scope.patient_health_current_page = 1;
                }

                PatientService
                        .getTableGraphDataProc(request, function (response) {

                            if (response.data && response.data.length > 0) {
                                $scope.patient_proc_data = response.data;
                                $scope.patient_proc_total_records = response.total_count;
                                $scope.patient_current_proc = $scope.patient_proc_data[0];
                                $scope.patient_current_proc.text_array = JSON.parse($scope.patient_current_proc.procedure_report_procedure_text);
                            } else {
                                $scope.patient_proc_data = [];
                                $scope.patient_current_proc = '';
                            }
                            $rootScope.gTrack('c_past_procedure');
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.changeCurrentProcObj = function (proc_obj) {
                $scope.patient_current_proc = proc_obj;
                $scope.patient_current_proc.text_array = JSON.parse($scope.patient_current_proc.procedure_report_procedure_text);
            }
            $scope.patient_investigation_current_page = 1;
            $scope.patient_investigation_total_records = '';
            $scope.getTableInvestigationData = function (isFromMenu, btn) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.patient_investigation_current_page <= 1) {
                            return;
                        }
                        $scope.patient_investigation_current_page = Number($scope.patient_investigation_current_page) - 1;
                    } else {

                        var total_pages = Math.ceil($scope.patient_investigation_total_records / 6);
                        if (total_pages <= $scope.patient_investigation_current_page) {
                            return;
                        }
                        $scope.patient_investigation_current_page = Number($scope.patient_investigation_current_page) + 1;
                    }
                }

                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: 1,
                    page: $scope.patient_investigation_current_page,
                    per_page: 6,
                    lab_report_id: ''
                };
                if (isFromMenu) {
                    $scope.patient_investigation_current_page = 1;
                }

                PatientService
                        .getTableGraphDataInvestigation(request, function (response) {

                            if (response.data && response.data.length > 0) {
                                $scope.patient_investigation_total_records = response.total_count;
                                $scope.patient_investigation_data = response.data;
                                $scope.patient_investigation_detail = $scope.patient_investigation_data[0];
                                var keys = [];
                                var temp_inv_obj = JSON.parse($scope.patient_investigation_detail.lab_report_test_name);
                                for (var k in temp_inv_obj) {
                                    keys.push(k);
                                }
                                $scope.patient_investigation_detail.test_array = keys.join(',');
                            } else {
                                $scope.patient_investigation_data = [];
                                $scope.patient_investigation_detail = '';
                            }
                            $rootScope.gTrack('c_past_ix');
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.changeInvestigationDateObj = function (obj) {
                $scope.patient_investigation_detail = obj;
                var keys = [];
                var temp_inv_obj = JSON.parse($scope.patient_investigation_detail.lab_report_test_name);
                for (var k in temp_inv_obj) {
                    keys.push(k);
                }
                $scope.patient_investigation_detail.test_array = keys.join(',');
            }
            $scope.patient_rx_current_page = 1;
            $scope.patient_rx_total_records = 1;
            $scope.patient_rx_data = [];
            $scope.patient_rx__detail_data = '';
            $scope.getTableDataForRX = function (isFromMenu, btn, flag) {
                if (btn) {
                    if (btn == "prev") {
                        if ($scope.patient_rx_current_page <= 1) {
                            return;
                        }
                        $scope.patient_rx_current_page = Number($scope.patient_rx_current_page) - 1;
                    } else {
                        var total_pages = Math.ceil($scope.patient_rx_total_records / 6);
                        if (total_pages <= $scope.patient_rx_current_page) {
                            return;
                        }
                        $scope.patient_rx_current_page = Number($scope.patient_rx_current_page) + 1;
                    }
                } else {
                    $scope.patient_rx_current_page = 1;
                    $scope.patient_rx_data = [];
                }
                var per_page = 6;
                if (flag == 1) {
                    per_page = '';
                    $rootScope.gTrack('c_common_brands');
                } else {
                	$rootScope.gTrack('c_past_rx');
                }

                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: flag,
                    page: $scope.patient_rx_current_page,
                    per_page: per_page,
                    date: ''
                };
                if (isFromMenu) {
                    $scope.patient_rx_current_page = 1;
                }
                PatientService
                        .getTableGraphDataRX(request, function (response) {
                            if (response.data) {
                                $scope.patient_rx_total_records = response.total_count;
                                $scope.patient_rx_data = response.data;
								if (flag == 2 && $scope.patient_rx_data && $scope.patient_rx_data.length > 0) {
                                    //get detail of that date prescription
                                    request.date = $scope.patient_rx_data[0].prescription_date;
                                    request.is_all_data = true;
                                    $scope.patient_rx_detail_obj = $scope.patient_rx_data[0];
                                    PatientService
                                            .getTableGraphDataRX(request, function (response) {
                                                $scope.patient_rx__detail_data = response.data;
                                            }, function (error) {
                                                $rootScope.handleError(error);
                                            });
                                }
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.changeRXObj = function (obj) {
                $scope.patient_rx_current_page = 1;
                $scope.patient_rx_detail_obj = obj;
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    flag: 2,
                    page: $scope.patient_rx_current_page,
                    per_page: 6,
                    is_all_data: true,
                    date: $scope.patient_rx_detail_obj.prescription_date
                };
                PatientService
                        .getTableGraphDataRX(request, function (response) {
                            $scope.patient_rx__detail_data = response.data;
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.getTabularDataPatientCompliance = function () {
                var current_date_string = $filter('date')(new Date(), "yyyy-MM-dd");
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    date: current_date_string
                };
                $scope.drug_array = [];
                PatientService
                        .getTableGraphPatientCompliance(request, function (response) {
                        	$rootScope.gTrack('c_patient_compliance');
                            // $scope.reminder_rec_data = response.chart_data;
                            $scope.all_dates = response.chart_data.reminder_record_all_dates;
                            $scope.drug_data = response.chart_data.drug_data;
                            $scope.complianceLineChart($scope.drug_data);
							/*$scope.final_rec_array = [];
                            for (var i = 1; i < 10; i++) {
                                var temp_rec_array = {};
                                angular.forEach($scope.reminder_rec_data, function (rmdVal, rmdKey) {
									if(rmdVal.reminder_record_data != undefined){
										temp_rec_array[rmdKey] = [];
										angular.forEach(rmdVal.reminder_record_data, function (value, key){
											if (value.take_count >= i) {
                                                temp_rec_array[rmdKey].push(value);
                                            } else {
                                                temp_rec_array[rmdKey].push(false);
                                            }
                                            var color_flag = true;
                                            angular.forEach($scope.drug_array, function (innerValue, innerKey) {
												if (innerValue.id == value.drug_id) {
													color_flag = false;
												}
                                            });
                                            if (color_flag) {
                                                if (!value.drug_color_code) {
                                                    value.drug_color_code = "d1d1d1";
                                                }
                                                $scope.drug_array.push({
                                                    id: value.drug_id,
                                                    color: value.drug_color_code,
                                                    name: value.drug_name
                                                })
                                            }
										});
									}
                                });
                                $scope.final_rec_array.push(temp_rec_array);
                            }*/
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            /*  add clinical notes */
            $scope.addClinicalNotesMain = function (key, type) {

                var text = '';
                if (type == 1) {
                    text = $scope.testChief[key].search;
                } else if (type == 2) {
                    text = $scope.testObservation[key].search;
                } else if (type == 3) {
                    text = $scope.testDiagnosis[key].search;
                } else if (type == 4) {
                    text = $scope.testNotes[key].search;
                }
                if (text) {
                    var request = {
                        title: text,
                        clinical_notes_type: type,
                    };
                    SettingService
                            .addClinicalNote(request, function (response) {

                                if (response.status) {
                                    ngToast.success({
                                        content: response.message
                                    });
                                    var inserted_id = response.inserted_id;
                                    if (type == 1) {
                                        $scope.testChief[key].autoID = inserted_id;
                                    } else if (type == 2) {
                                        $scope.testObservation[key].autoID = inserted_id;
                                    } else if (type == 3) {
                                        $scope.testDiagnosis[key].autoID = inserted_id;
                                    } else if (type == 4) {
                                        $scope.testNotes[key].autoID = inserted_id;
                                    }
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.brand_deleted_obj = [];
            $scope.removeLastBrandObj = function (key) {
            	$scope.prescriptionOnchangeFlag();
                if ($scope.brandList[key].id) {
                    $scope.brandList[key].is_delete = 1;
                    $scope.brand_deleted_obj.push($scope.brandList[key]);
                }
                $scope.brandList.splice(key, 1);
				/* setTimeout(function () {
					$('#'+$scope.brandList.length).autocompleteautocomplete("instance");
				}, 100); */
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

            $scope.changeCustomValue = function (key) {
                var freq = $scope.brandList[key].drug_frequency_id;
                $scope.brandList[key].default1 = '0';
                $scope.brandList[key].default2 = '0';
                $scope.brandList[key].default3 = '0';
                if (freq == 1) {
                    $scope.brandList[key].drug_duration = 1;
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '0';
                } else if (freq == 2) {
                    $scope.brandList[key].drug_duration = 1;
                    $scope.brandList[key].default1 = '0';
                    $scope.brandList[key].default2 = '1';
                    $scope.brandList[key].default3 = '0';
                } else if (freq == 3) {
                    $scope.brandList[key].drug_duration = 1;
                    $scope.brandList[key].default1 = '0';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 4) {
                    $scope.brandList[key].drug_duration = 1;
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '0';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 5) {
                    $scope.brandList[key].drug_duration = 1;
                    $scope.brandList[key].default1 = '1';
                    $scope.brandList[key].default2 = '1';
                    $scope.brandList[key].default3 = '1';
                } else if (freq == 11) {
                    $scope.brandList[key].drug_duration = 1;
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

            $scope.clearCustomValues = function(key) {
            	if($scope.brandList[key].drug_unit_name=='Tablets' || $scope.brandList[key].drug_unit_name=='IU') {
	            	$scope.brandList[key].dosage = '0';
	            }
            }
            $scope.isDefaultRequired = function(key) {
            	// if(key==0){
            	// 	console.log($scope.brandList[key]);
            	// }
            	if($scope.brandList[key].default1 == '0' && $scope.brandList[key].default2 == '0' && $scope.brandList[key].default3 == '0') {
            		return "/^[^0]*$/";
            	} else {
            		return "/^[0-9]{1,6}$/";
            	}
            }

            /* c section copy functionality start */
            $scope.copyClinicalNotes = function () {
                if ($scope.detail_notes.clinical_notes_reports_complaints.length > 0 ||
                        $scope.detail_notes.clinical_notes_reports_observation.length > 0 ||
                        $scope.detail_notes.clinical_notes_reports_diagnoses.length > 0 ||
                        $scope.detail_notes.clinical_notes_reports_add_notes.length > 0
                        ) {
                    $scope.testChief = [{
                            search: ''
                        }];
                    $scope.testObservation = [{
                            search: ''
                        }];
                    $scope.testDiagnosis = [{
                            search: ''
                        }];
                    $scope.testNotes = [{
                            search: ''
                        }];
                }
                if ($scope.detail_notes.clinical_notes_reports_complaints.length > 0) {
                    $scope.testChief = [];
                    angular
                            .forEach($scope.detail_notes.clinical_notes_reports_complaints, function (value, key) {
                                $scope.testChief.push({
                                    search: value
                                });
                            });
                }
                if ($scope.detail_notes.clinical_notes_reports_observation.length > 0) {
                    $scope.testObservation = [];
                    angular
                            .forEach($scope.detail_notes.clinical_notes_reports_observation, function (value, key) {
                                $scope.testObservation.push({
                                    search: value
                                });
                            });
                }
                if ($scope.detail_notes.clinical_notes_reports_diagnoses.length > 0) {
                    $scope.testDiagnosis = [];
                    angular
                            .forEach($scope.detail_notes.clinical_notes_reports_diagnoses, function (value, key) {
                                $scope.testDiagnosis.push({
                                    search: value
                                });
                            });
                }
                if ($scope.detail_notes.clinical_notes_reports_add_notes.length > 0) {
                    $scope.testNotes = [];
                    angular
                            .forEach($scope.detail_notes.clinical_notes_reports_add_notes, function (value, key) {
                                $scope.testNotes.push({
                                    search: value
                                });
                            });
                }
            }
			
            $scope.copyInvestigationData = function (type, obj) {
            	$scope.prescriptionOnchangeFlag();
                if (type == 1 && $scope.patient_investigation_detail.lab_report_test_name) {
                    var lab_test_data = JSON.parse($scope.patient_investigation_detail.lab_report_test_name);
                    if (lab_test_data.length > 0) {
                        if (!$scope.testLabs[$scope.testLabs.length - 1].search) {
                            $scope.testLabs.splice($scope.testLabs.length - 1, 1);
                        }
                        angular
                                .forEach(lab_test_data, function (value, key) {
                                    var keys = [];
                                    for (var k in value) {
                                        keys.push(k);
                                    }
                                    value.keys = keys;
                                    value.search = value.keys[0];
                                    value.lab_instruction = value[value.keys[0]];
                                    if (value.lab_instruction) {
                                        value.isOpenInst = true;
                                    }
                                    $scope.testLabs.push(value);
                                });
                    }
                } else if (type == 2) {

                    if (!$scope.testLabs[$scope.testLabs.length - 1].search) {
                        $scope.testLabs.splice($scope.testLabs.length - 1, 1);
                    }
                    var parent_id = 0;
                    if (obj.health_analytics_test_id) {
                        parent_id = obj.health_analytics_test_id;
                    }
                    var request = {
                        search: '',
                        parent_id: parent_id
                    }
                    PatientService
                            .getHealthTest(request, false, function (response) {
                            	var instructions_data = response.instructions_data;
                                if (response.data) {
                                    angular
                                            .forEach(response.data, function (value) {
                                                $scope.testLabs.push({
                                                    search: value.health_analytics_test_name,
                                                    id: value.health_analytics_test_id,
                                                    isOpenInst: (instructions_data[value.health_analytics_test_id] != undefined) ? true : false,
                                        			lab_instruction: (instructions_data[value.health_analytics_test_id] != undefined && instructions_data[value.health_analytics_test_id][0] !=undefined) ? instructions_data[value.health_analytics_test_id][0].instruction : '',
                                                });
                                            });
                                } else {
                                	$scope.investigation_instructions_data = [];
                                	if(instructions_data[obj.health_analytics_test_id] != undefined){
                                		$scope.investigation_instructions_data = instructions_data[obj.health_analytics_test_id];
                                	}
                                    $scope.testLabs.push({
                                        search: obj.health_analytics_test_name,
                                        id: obj.health_analytics_test_id,
                                        isOpenInst: (instructions_data[obj.health_analytics_test_id] != undefined) ? true : false,
                                        lab_instruction: (instructions_data[obj.health_analytics_test_id] != undefined && instructions_data[obj.health_analytics_test_id][0] !=undefined) ? instructions_data[obj.health_analytics_test_id][0].instruction : '',
                                    });
                                }
                                $scope.testLabs.push({
                                    search: ''
                                });
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }

            $scope.search_instructions = function (key) {
            	if($scope.testLabs[key] != undefined && $scope.testLabs[key].lab_instruction.length > 2 && (($scope.testLabs[key].id != undefined && $scope.testLabs[key].id != '') || ($scope.testLabs[key].search != undefined && $scope.testLabs[key].search != ''))) {
	            	var request = {
	            		device_type: 'web',
	                    user_id: $rootScope.currentUser.user_id,
	                    access_token: $rootScope.currentUser.access_token,
	                    search: $scope.testLabs[key].lab_instruction,
	                    health_analytics_test_id: ($scope.testLabs[key].id != undefined) ? $scope.testLabs[key].id : '',
	                    health_analytics_test_name: ($scope.testLabs[key].search != undefined) ? $scope.testLabs[key].search : '',
						doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
	            	}
	            	PatientService
	                    .searchInvestigationInstructions(request, function (response) {
	                    	$scope.investigation_instructions_data = response.data;
	                    }, function (error) {
	                        $rootScope.handleError(error);
	                    });
                }
            }
			
            $scope.copyProcedureData = function () {
                if ($scope.patient_current_proc.text_array) {
                    $scope.procedureObj = [];
                    angular
                            .forEach($scope.patient_current_proc.text_array, function (value, key) {
                                $scope.procedureObj.push({
                                    text: value
                                });
                            });
                }
            }
            $scope.addOldProc = function (procedure) {
            	var key = $scope.procedureObj.length-1;
            	if($scope.procCurrentSearchKey != undefined && $scope.procCurrentSearchKey != ''){
            		$scope.procedureObj[$scope.procCurrentSearchKey].text = procedure;
            	} else if($scope.procedureObj[key] != undefined && $scope.procedureObj[key].text == '') {
            		$scope.procedureObj[key].text = procedure;
            	} else {
	                $scope.procedureObj.push({
	                    text: procedure
	                });
	            }
	            $scope.cSearchProc = '';
	            $scope.procCurrentSearchKey = '';
	            $scope.prescriptionOnchangeFlag();
            }
			
            $scope.getListIXReport = function () {
                $scope.IXList = [];
                var request = {
                };
                PatientService
                        .getHealthTest(request, true, function (response) {
                            $scope.IXList = response.data;
                            $rootScope.gTrack('c_list_of_ix');
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
			
            $scope.addOldRX = function (presObj) {
                if ($scope.current_appointment_date_obj.is_editable && presObj.drug_status != "9") {
                    /*
                     var request = {
                     prescription_id: presObj.prescription_id,
                     patient_id: $scope.current_patient.user_id,
                     appointment_id: $scope.current_appointment_date_obj.appointment_id,
                     clinic_id: $rootScope.current_clinic.clinic_id,
                     date: $scope.current_appointment_date_obj.appointment_date
                     };
                     PatientService
                     .copyRX(request, function (response) {
                     if (response.status) {
                     $scope.getPatientReportDetail();
                     $scope.suggestion_brand_result = [];
                     PatientService
                     .getRelatedDrugData(presObj.prescription_drug_id, function (response) {
                     $scope.suggestion_brand_result = response.data;
                     }, function (error) {
                     $rootScope.handleError(error);
                     });
                     }
                     }, function (error) {
                     $rootScope.handleError(error);
                     });
                     */
                    $scope.prescriptionOnchangeFlag();
                    $scope.brandList.splice($scope.brandList.length - 1, 1);
                    var custom_array = presObj.prescription_frequency_value.split('-');
                    var default1 = '';
                    var default2 = '';
                    var default3 = '';
                    var open_default = false;
                    if (custom_array.length == 3) {
                        default1 = custom_array[0];
                        default2 = custom_array[1];
                        default3 = custom_array[2];
                    }
                    if(presObj.prescription_dosage == '' && (presObj.prescription_unit_value == 'Tablets' || presObj.prescription_unit_value == 'IU')) {
                    	open_default = true;
                    }
                    var freq_open = false;
                    if (presObj.prescription_frequency_instruction) {
                        freq_open = true;
                    }
                    var intake_open = false;
                    if (presObj.prescription_intake_instruction) {
                        intake_open = true;
                    }
					
                    $scope.brandList.push({
                        similar_brand_id: presObj.prescription_drug_id,
                        similar_brand: presObj.drug_name,
						drug_name_with_unit: presObj.drug_name_with_unit,
                        drug_drug_generic_id: presObj.prescription_generic_id.split(','),
                        dosage: presObj.prescription_dosage,
                        drug_frequency_id: presObj.prescription_frequency_id,
                        default1: default1,
                        default2: default2,
                        default3: default3,
                        freq_instruction: presObj.prescription_frequency_instruction,
                        drug_intake: presObj.prescription_intake,
                        intake_instruction: presObj.prescription_intake_instruction,
                        drug_duration_value: presObj.prescription_duration_value,
                        drug_instruction: '',
                        defaultFreqOpen: open_default,
                        isFreqInst: freq_open,
                        isIntakeInst: intake_open,
                        drug_unit_name: presObj.prescription_unit_value,
                        drug_unit_id: presObj.prescription_unit_id,
                        drug_duration: presObj.prescription_duration,
                        is_delete: 2,
                        isOpenWholeForm: true,
                        id: ''
                    });
                    $scope.brandList.push({});
					$scope.scrollToNextDrugEntry();
                }
            }
			
            $scope.openKCODetail = function (tempKCO) {
                if (tempKCO.is_editable) {
                    var flag = false;
                    angular
                            .forEach($scope.appointment_dates, function (value, key) {
                                if (value.appointment_id == tempKCO.appointment_id) {
                                    flag = true;
                                    $scope.changeActiveDate(key,value);
                                    $scope.changePatientReportTab('patient_notes', 2);
                                }
                            });
                    if (!flag) {
                        $scope.getAppointmentDates("next");
                    }
                }
            }

			$scope.openDOBDetail = function (current_patient) {
				/* if(current_patient.user_details_dob.length > 0){
					$scope.current_patient.update_user_details_dob = new Date(current_patient.user_details_dob);
				} */
				if(angular.element("#update_pt_dob")){
					angular.element("#update_pt_dob")[0].value = null;
					delete $scope.current_patient.update_user_details_dob;
				}
				setTimeout(function (){
					$("#modal_update_dob_details").modal("show");
				},100);
            }
			
			$scope.updateCurrentPatientDob = function(){
				if($scope.current_patient && $scope.current_patient.update_user_details_dob){
					var month = $scope.current_patient.update_user_details_dob.getMonth() + 1;
					var day   = $scope.current_patient.update_user_details_dob.getDate();
					var year  = $scope.current_patient.update_user_details_dob.getFullYear();
					$scope.current_patient.updated_user_details_dob = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
					var request = {
                        clinic_id: $rootScope.current_clinic.clinic_id,
						doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
						patient_id: $scope.current_patient.user_id, 
						updated_user_dob: $scope.current_patient.updated_user_details_dob,
						old_dob: $scope.current_patient.user_details_dob,
                    };
					PatientService.updatePatientDob(request, function (response) {
						if (response.status) {
							ngToast.success({
								content: response.message
							});
							$scope.current_patient.user_details_dob = $scope.current_patient.updated_user_details_dob;
							delete $scope.current_patient.update_user_details_dob;
							delete $scope.current_patient.updated_user_details_dob;
							$("#modal_update_dob_details").modal("hide");
						} else {
							ngToast.danger({
								content: response.message
							});
						}
					}, function (error) {
						$rootScope.handleError(error);
					});
				}
			}

            /* open generic details */
            $scope.getGenericDetails = function (generic_id) {
                if (generic_id) {
                    var request = {
                        generic_id: generic_id
                    };
                    PatientService
                            .getGenericDetailsFromDB(request, function (response) {
                                if (response.status) {
                                    $scope.generic_detail = response.data;
                                    $("#modal_generic_details").modal("show");
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            })
                }
            }
            /* get procedure list */
            $scope.getProcedureList = function (key) {
                var search = $scope.procedureObj[key].text;
                $scope.cSearchProc = search;
                $scope.procCurrentSearchKey = key;
                if (search) {
                    PatientService
                            .searchProc(search, function (response) {
                                $scope.search_proclist = response.data;
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }

            /* billing section start */
            $scope.paymentObjList = [{
                    isOpenPaymentDiv: false,
                    rupies_type: '1'
                }];
            $scope.removedObj = [];
            $scope.addNewPaymentObj = function () {
                $scope.paymentObjList.push({isOpenPaymentDiv: false, rupies_type: '1'});
            }
            $scope.closePaymentObj = function (key) {
                $scope.paymentObjList[key].is_delete = 1;
                $scope.removedObj.push($scope.paymentObjList[key]);
                $scope.paymentObjList.splice(key, 1);
                $scope.calculateMiddleTotalCost();
            }
            $scope.searchTreatment = function (key, flag) {
                if (!flag) {
                    var search = $scope.paymentObjList[key].treatment_name;
                    if (search) {
                        var request = {
                            search: search
                        }
                        SettingService.getFeesListDB(request, function (response) {
                            $scope.search_treatment = response.data;
                        });
                    }
                } else {
                    var request = {
                        search: ''
                    }
                    SettingService.getFeesListDB(request, function (response) {
                        $scope.treatment_list = response.data;
                    });
                }
            }
            $scope.copyTreatment = function (obj) {
                $scope.setClientData(obj, 16, $scope.paymentObjList.length - 1, '', '');
            }
            $scope.getTaxSetting = function () {
                SettingService
                        .getTax('', function (response) {
                            if (response.status) {
                                $scope.taxes = response.data;
                            } else {
                                $scope.taxes = [];
                            }
                        });
                $scope.getPaymentModes();
            }
            
			$scope.get_pdf_print_user_setting = false;
			$scope.pdf_url_data = {};
			$scope.pdf_url_data.with_vitalsign = true;
            $scope.pdf_url_data.with_clinicnote = true;
            $scope.pdf_url_data.with_only_diagnosis = true;
            $scope.pdf_url_data.with_generic = true;
            $scope.pdf_url_data.with_patient_lab_orders = true;
            $scope.pdf_url_data.with_prescription = true;
            $scope.pdf_url_data.with_anatomy_diagram = false;
            $scope.pdf_url_data.with_files = true;
            $scope.pdf_url_data.with_treatment = true;
            $scope.pdf_url_data.with_procedure = true;
            $scope.pdf_url_data.with_signature = true;
            $scope.pdf_url_data.tele_consultation = false;
			$scope.pdf_url_data.hide_header_footer = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.hide_header_footer != undefined) ? $scope.doctor_print_page_setup.hide_header_footer : false;
			$scope.pdf_url_data.header_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.header_space != undefined) ? $scope.doctor_print_page_setup.header_space : 3;
		    $scope.pdf_url_data.footer_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.footer_space != undefined) ? $scope.doctor_print_page_setup.footer_space : 2;
		    $scope.pdf_url_data.left_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.left_space != undefined) ? $scope.doctor_print_page_setup.left_space : 1;
		    $scope.pdf_url_data.right_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.right_space != undefined) ? $scope.doctor_print_page_setup.right_space : 1;
            $scope.pdf_url_data.language_id = "1";
        	$scope.print_url_invoice = '';
			$scope.getPrintSetup = function(){
				if($rootScope.current_clinic != undefined && $rootScope.currentUser != undefined){
					var request = {
						doctor_id: $rootScope.currentUser.user_id,
						clinic_id: $rootScope.current_clinic.clinic_id
					};
					PatientService.getPrintSetup(request, function(response){
						if (response.status) {
							if(response.prescription_print_setting_data != undefined && response.prescription_print_setting_data.hide_header_footer != undefined && response.prescription_print_setting_data.header_space != undefined && response.prescription_print_setting_data.footer_space != undefined){
								$scope.doctor_print_page_setup.hide_header_footer = response.prescription_print_setting_data.hide_header_footer;
								$scope.doctor_print_page_setup.header_space 	  = response.prescription_print_setting_data.header_space;
								$scope.doctor_print_page_setup.footer_space 	  = response.prescription_print_setting_data.footer_space;
								
								$scope.pdf_url_data.hide_header_footer  		  = response.prescription_print_setting_data.hide_header_footer;
								$scope.pdf_url_data.header_space 		          = response.prescription_print_setting_data.header_space;
								$scope.pdf_url_data.footer_space 		          = response.prescription_print_setting_data.footer_space;
							}
							if(response.prescription_print_setting_data.left_space != undefined) {
								$scope.doctor_print_page_setup.left_space = response.prescription_print_setting_data.left_space;
								$scope.pdf_url_data.left_space = response.prescription_print_setting_data.left_space;
							}
							if(response.prescription_print_setting_data.right_space != undefined) {
								$scope.doctor_print_page_setup.right_space = response.prescription_print_setting_data.right_space;
								$scope.pdf_url_data.right_space = response.prescription_print_setting_data.right_space;
							}
						} else {
							$scope.pdf_url_data.hide_header_footer = false;
							$scope.pdf_url_data.header_space = 3;
						    $scope.pdf_url_data.footer_space = 2;
						    $scope.pdf_url_data.left_space = 1;
						    $scope.pdf_url_data.right_space = 1;
						}
						$scope.get_pdf_print_user_setting = true;
						setTimeout(function (){ $scope.getPDFUrl(); },100);
					}, function (error) {
						$rootScope.handleError(error);
					});
				}
			}
			
			$scope.changeAppointmentType = function(is_share) {
				SweetAlert.swal(
                    {
                        title: "This will be Tele-consultation prescription.",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        cancelButtonText: "Cancel",
                        closeOnConfirm: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                        	var request = {
                        		device_type: 'web',
		                        user_id: $rootScope.currentUser.user_id,
		                        access_token: $rootScope.currentUser.access_token,
								doctor_id: $rootScope.currentUser.user_id,
								clinic_id: $rootScope.current_clinic.clinic_id,
								appointment_id: $scope.current_appointment_date_obj.appointment_id,
								appointment_type: 5
							};
							PatientService.changeAppointmentType(request, function(response){
								if (response.status) {
									ngToast.success({content: response.message});
									$scope.current_appointment_date_obj.appointment_type = 5;
									var appointmentTypeName = $filter('filter')(APPOINTMENT_TYPE, {id: "5"});
									$scope.current_appointment_date_obj.appointment_type_name = (appointmentTypeName != undefined) ? appointmentTypeName[0].name : $scope.current_patient.appointment_type;
									$scope.getPDFUrl(is_share);
								} else {
									ngToast.danger(response.message);
								}
							}, function (error) {
								$rootScope.handleError(error);
							});
                        } else {
                        	$scope.pdf_url_data.tele_consultation = false;
                        }
                    }
                );
			}
			$scope.getPDFUrl = function(isFromShare) {
				if(($scope.doctor_print_page_setup == undefined || $scope.doctor_print_page_setup.hide_header_footer == undefined  || $scope.doctor_print_page_setup.header_space == undefined || $scope.doctor_print_page_setup.footer_space == undefined) && isFromShare == undefined && $scope.get_pdf_print_user_setting == false) {
					$scope.getPrintSetup();
				}else{
					if(isFromShare == true){
						$scope.pdf_url_data.hide_header_footer = false;
						$rootScope.gTrack('view_prescription_share_pdf');
					} else {
						$rootScope.gTrack('view_prescription_pdf');
						$scope.pdf_url_data.email = '';
						if($scope.current_patient.user_email != undefined)
							$scope.pdf_url_data.email = $scope.current_patient.user_email;
						$scope.pdf_url_data.mobile_no = '';
						if($scope.current_patient.user_phone_number != undefined)
							$scope.pdf_url_data.mobile_no = $scope.current_patient.user_phone_number;
					}
					if($scope.current_appointment_date_obj.appointment_type == 4 || $scope.current_appointment_date_obj.appointment_type == 5)
						$scope.pdf_url_data.tele_consultation = true;
					else
						$scope.pdf_url_data.tele_consultation = false;

					var print_url = $rootScope.app.base_url + 'prints/charting';
					print_url += "?appointment_id=" + $scope.current_appointment_date_obj.appointment_id;
					print_url += "&chart_date=" + $scope.current_appointment_date_obj.appointment_date;
					print_url += "&doctor_id=" + $rootScope.current_doctor.user_id;
					print_url += "&patient_id=" + $scope.current_patient.user_id;
					print_url += "&with_vitalsign=" + $scope.pdf_url_data.with_vitalsign;
					print_url += "&with_clinicnote=" + $scope.pdf_url_data.with_clinicnote;
					print_url += "&with_only_diagnosis=" + $scope.pdf_url_data.with_only_diagnosis;
					print_url += "&with_patient_lab_orders=" + $scope.pdf_url_data.with_patient_lab_orders;
					print_url += "&with_prescription=" + $scope.pdf_url_data.with_prescription;
					print_url += "&with_generic=" + $scope.pdf_url_data.with_generic;
					print_url += "&with_anatomy_diagram=" + $scope.pdf_url_data.with_anatomy_diagram;
					print_url += "&with_files=" + $scope.pdf_url_data.with_files;
					print_url += "&with_treatment=" + $scope.pdf_url_data.with_treatment;
					print_url += "&with_procedure=" + $scope.pdf_url_data.with_procedure;
					print_url += "&with_signature=" + $scope.pdf_url_data.with_signature;
					print_url += "&hide_header_footer=" + $scope.pdf_url_data.hide_header_footer;
					print_url += "&header_space=" + $scope.pdf_url_data.header_space;
					print_url += "&footer_space=" + $scope.pdf_url_data.footer_space;
					print_url += "&left_space=" + $scope.pdf_url_data.left_space;
					print_url += "&right_space=" + $scope.pdf_url_data.right_space;
					print_url += "&language_id=" + $scope.pdf_url_data.language_id;
					print_url += "&time=" + $.now();
					print_url = btoa(encodeURI(print_url));
					if(isFromShare == true){
						$scope.share_with_other_url = $rootScope.app.base_url + "prescription/" + print_url;
					} else {
						$scope.print_share_url = $rootScope.app.base_url + "prescription/" + print_url;
					}
				}
            };
			
			$scope.getPDFUrlForShare = function(is_share) {
				if(is_share != undefined) {
					$scope.pdf_url_data.email = '';
					/*if($scope.current_patient.user_email != undefined)
						$scope.pdf_url_data.email = $scope.current_patient.user_email;*/
					$scope.pdf_url_data.mobile_no = '';
					/*if($scope.current_patient.user_phone_number != undefined)
						$scope.pdf_url_data.mobile_no = $scope.current_patient.user_phone_number;*/
				}
				var request = {
                    setting_type: [7,8],
                    setting_data_type: 2,
                    doctor_id: ($rootScope.currentUser.doctor_id != undefined && $rootScope.currentUser.doctor_id != '') ? $rootScope.currentUser.doctor_id : $rootScope.currentUser.user_id
                };
                SettingService
                    .getDoctorSetting(request, function (response) {
                    	$scope.pdf_url_data.whatsapp_credit = 0;
                    	$scope.pdf_url_data.sms_credit = 0;
                    	$scope.pdf_url_data.is_check_sms_credit = false;
                        if (response.status) {
                        	var searchObj = $filter('filter')(response.data, {'setting_type':'7'},true);
							if(searchObj != undefined && searchObj.length > 0) {
                            	$scope.pdf_url_data.sms_credit = parseInt(searchObj[0].setting_data);
                            	$scope.pdf_url_data.is_check_sms_credit = searchObj[0].is_check_sms_credit;
							}
							var searchObj = $filter('filter')(response.data, {'setting_type':'8'},true);
							if(searchObj != undefined && searchObj.length > 0) {
                            	$scope.pdf_url_data.whatsapp_credit = parseInt(searchObj[0].setting_data);
							}
                        }
                    });
				$scope.getPDFUrl(true);
			}
			
            $scope.getPDFUrlInvoice = function (bill_id) {
            	var billing_id = 0;
            	if(bill_id != undefined){
            		billing_id = bill_id;
            	} else if($scope.invoices_list != undefined && $scope.invoices_list.length > 0) {
            		billing_id = $scope.invoices_list[0].billing_id;
            	}
            	$scope.pdf_url_data.billing_id = billing_id;
            	$scope.is_share_invoice = false;
                $scope.print_url_invoice = $rootScope.app.base_url + 'prints/invoice';
                $scope.print_url_invoice += "?appointment_id=" + $scope.current_appointment_date_obj.appointment_id;
                $scope.print_url_invoice += "&doctor_id=";
				$scope.print_url_invoice += ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id;
                $scope.print_url_invoice += "&patient_id=" + $scope.current_patient.user_id;
                $scope.print_url_invoice += "&billing_id=" + billing_id;
                $scope.print_url_invoice += "&time=" + $.now();
                var print_url_invoice = btoa(encodeURI($scope.print_url_invoice));
				$scope.print_url_invoice = $rootScope.app.base_url + "invoice/" + print_url_invoice;
                $rootScope.gTrack('view_invoice');
            }
            $scope.sharePDFUrlInvoice = function (bill_id) {
            	var billing_id = 0;
            	if(bill_id != undefined) {
            		billing_id = bill_id;
            	} else if($scope.invoices_list != undefined && $scope.invoices_list.length > 0) {
            		billing_id = $scope.invoices_list[0].billing_id;
            	}
            	$scope.pdf_url_data.email = '';
				if($scope.current_patient.user_email != undefined)
					$scope.pdf_url_data.email = $scope.current_patient.user_email;
				$scope.pdf_url_data.mobile_no = '';
				if($scope.current_patient.user_phone_number != undefined)
					$scope.pdf_url_data.mobile_no = $scope.current_patient.user_phone_number;
            	$scope.pdf_url_data.billing_id_arr = [];
            	$scope.pdf_url_data.billing_id = billing_id;
            	$scope.is_share_invoice = true;
                $scope.print_url_invoice = $rootScope.app.base_url + 'prints/invoice';
                $scope.print_url_invoice += "?appointment_id=" + $scope.current_appointment_date_obj.appointment_id;
                $scope.print_url_invoice += "&doctor_id=";
				$scope.print_url_invoice += ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id;
                $scope.print_url_invoice += "&patient_id=" + $scope.current_patient.user_id;
                $scope.print_url_invoice += "&billing_id=" + billing_id;
                $scope.print_url_invoice += "&time=" + $.now();
                var print_url_invoice = btoa(encodeURI($scope.print_url_invoice));
				$scope.print_url_invoice = $rootScope.app.base_url + "invoice/" + print_url_invoice;
                $rootScope.gTrack('view_invoice');
            }
            $scope.clearPrint = function () {
                $scope.submitted = false;
                $scope.print_url = '';
                $scope.pdf_url_data = {};
                $scope.pdf_url_data.with_vitalsign = true;
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_vitalsign = ($scope.share_record_setting[1] !=undefined && $scope.share_record_setting[1] == "1") ? true : false;
                }
                $scope.pdf_url_data.with_clinicnote = true;
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_clinicnote = ($scope.share_record_setting[2] !=undefined && $scope.share_record_setting[2] == "1") ? true : false;
                }
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_only_diagnosis = ($scope.share_record_setting[8] !=undefined && $scope.share_record_setting[8] == "1") ? true : false;
                } else {
	                $scope.pdf_url_data.with_only_diagnosis = false;
                }
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_generic = ($scope.share_record_setting[9] !=undefined && $scope.share_record_setting[9] == "1") ? true : false;
                } else {
	                $scope.pdf_url_data.with_generic = false;
                }
                $scope.pdf_url_data.with_patient_lab_orders = true;
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_patient_lab_orders = ($scope.share_record_setting[4] !=undefined && $scope.share_record_setting[4] == "1") ? true : false;
                }
                $scope.pdf_url_data.with_prescription = true;
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_prescription = ($scope.share_record_setting[3] !=undefined && $scope.share_record_setting[3] == "1") ? true : false;
                }
                $scope.pdf_url_data.with_anatomy_diagram = false;
                $scope.pdf_url_data.with_files = true;
                $scope.pdf_url_data.with_treatment = true;
                $scope.pdf_url_data.with_procedure = true;
                if($scope.share_record_setting != undefined) {
                	$scope.pdf_url_data.with_procedure = ($scope.share_record_setting[5] !=undefined && $scope.share_record_setting[5] == "1") ? true : false;
                }
                $scope.pdf_url_data.with_signature = true;
                $scope.pdf_url_data.hide_header_footer = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.hide_header_footer != undefined) ? $scope.doctor_print_page_setup.hide_header_footer : false;
				$scope.pdf_url_data.header_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.header_space != undefined) ? $scope.doctor_print_page_setup.header_space : 3;
				$scope.pdf_url_data.footer_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.footer_space != undefined) ? $scope.doctor_print_page_setup.footer_space : 2;
				$scope.pdf_url_data.left_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.left_space != undefined) ? $scope.doctor_print_page_setup.left_space : 1;
				$scope.pdf_url_data.right_space = ($scope.doctor_print_page_setup != undefined && $scope.doctor_print_page_setup.right_space != undefined) ? $scope.doctor_print_page_setup.right_space : 1;
				$scope.pdf_url_data.email = '';
				$scope.pdf_url_data.mobile_no = '';
				$scope.pdf_url_data.language_id = ($scope.current_patient != undefined && $scope.current_patient.patient_pr_lang_id != undefined) ? $scope.current_patient.patient_pr_lang_id : "1";
            }
			$scope.savePrintSetup = function(){
				if($scope.pdf_url_data != undefined){
					var request = {
						doctor_id: $rootScope.currentUser.user_id,
						clinic_id: $rootScope.current_clinic.clinic_id,
						pdf_url_data: $scope.pdf_url_data
					};
					PatientService.savePrintSetup(request, function(response){
						if (response.status) {
							if($scope.pdf_url_data.hide_header_footer != undefined)
								$scope.doctor_print_page_setup.hide_header_footer = $scope.pdf_url_data.hide_header_footer;
							if($scope.pdf_url_data.header_space != undefined)
								$scope.doctor_print_page_setup.header_space = $scope.pdf_url_data.header_space;
							if($scope.pdf_url_data.footer_space != undefined)
								$scope.doctor_print_page_setup.footer_space = $scope.pdf_url_data.footer_space;
							if($scope.pdf_url_data.left_space != undefined)
								$scope.doctor_print_page_setup.left_space = $scope.pdf_url_data.left_space;
							if($scope.pdf_url_data.right_space != undefined)
								$scope.doctor_print_page_setup.right_space = $scope.pdf_url_data.right_space;
						}
					}, function (error) {
						$rootScope.handleError(error);
					});
				}
			}
			$scope.submitted_smswhatapp = true;
			$scope.sharePDFSmsWhatApp = function (form, share_type) {
				$scope.submitted_smswhatapp = true;
				$scope.submitted = false;
				if (form.$valid) {
					var request = {
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        chart_date: $scope.current_appointment_date_obj.appointment_date,
                        doctor_id: $rootScope.currentUser.user_id,
                        patient_id: $scope.current_patient.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        with_vitalsign: $scope.pdf_url_data.with_vitalsign,
                        with_clinicnote: $scope.pdf_url_data.with_clinicnote,
                        with_only_diagnosis: $scope.pdf_url_data.with_only_diagnosis,
                        with_patient_lab_orders: $scope.pdf_url_data.with_patient_lab_orders,
                        with_prescription: $scope.pdf_url_data.with_prescription,
                        with_generic: $scope.pdf_url_data.with_generic,
                        with_anatomy_diagram: $scope.pdf_url_data.with_anatomy_diagram,
                        with_files: $scope.pdf_url_data.with_files,
                        with_treatment: $scope.pdf_url_data.with_treatment,
                        with_procedure: $scope.pdf_url_data.with_procedure,
                        with_signature: $scope.pdf_url_data.with_signature,
                        email: ($scope.pdf_url_data.email != undefined) ? $scope.pdf_url_data.email : '',
                        mobile_no: ($scope.pdf_url_data.mobile_no != undefined) ? $scope.pdf_url_data.mobile_no : '',
                        language_id: $scope.pdf_url_data.language_id,
                        share_type: share_type,
                    };
                    PatientService
                            .sharePDFMail(request, function (response) {
                                if (response.status) {
                                    ngToast.success({content: response.message});
                                    $("#modal_share").modal("hide");
                                    $rootScope.gTrack('share_prescription');
                                    $scope.clearPrint();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
				}
			}
            $scope.sharePatientPresValidate = function () {
            	if(($scope.pdf_url_data.email != undefined && $scope.pdf_url_data.email != "") || ($scope.pdf_url_data.mobile_no != undefined && $scope.pdf_url_data.mobile_no != '')) {
            		return false;
            	} else {
            		return true;
            	}
            }
            $scope.sharePDF = function (form, share_type) {
                $scope.submitted = true;
                $scope.submitted_smswhatapp = false;
                if (form.$valid) {
                    var request = {
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        chart_date: $scope.current_appointment_date_obj.appointment_date,
                        doctor_id: $rootScope.currentUser.user_id,
                        patient_id: $scope.current_patient.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        with_vitalsign: $scope.pdf_url_data.with_vitalsign,
                        with_clinicnote: $scope.pdf_url_data.with_clinicnote,
                        with_only_diagnosis: $scope.pdf_url_data.with_only_diagnosis,
                        with_patient_lab_orders: $scope.pdf_url_data.with_patient_lab_orders,
                        with_prescription: $scope.pdf_url_data.with_prescription,
                        with_generic: $scope.pdf_url_data.with_generic,
                        with_anatomy_diagram: $scope.pdf_url_data.with_anatomy_diagram,
                        with_files: $scope.pdf_url_data.with_files,
                        with_treatment: $scope.pdf_url_data.with_treatment,
                        with_procedure: $scope.pdf_url_data.with_procedure,
                        with_signature: $scope.pdf_url_data.with_signature,
                        email: ($scope.pdf_url_data.email != undefined) ? $scope.pdf_url_data.email : '',
                        mobile_no: ($scope.pdf_url_data.mobile_no != undefined) ? $scope.pdf_url_data.mobile_no : '',
                        language_id: $scope.pdf_url_data.language_id,
                        share_type: share_type,
                    };
                    PatientService
                            .sharePDFMail(request, function (response) {
                                if (response.status) {
                                    ngToast.success({content: response.message});
                                    $("#modal_share").modal("hide");
                                    $("#modal_print").modal("hide");
                                    $rootScope.gTrack('share_prescription');
                                    $scope.clearPrint();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.someSelected = function () {
            	var selected = [];
				angular.forEach($scope.pdf_url_data.billing_id_arr, function (value, key) {
                	if(value)
                		selected.push(1);
                });
                if(selected.length > 0)
                	return true;
                else
                	return false;
			}
            $scope.sharePDFInvoice = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                	var billing_id_arr = [];
                	angular.forEach($scope.pdf_url_data.billing_id_arr, function(val, key) {
                		if(val)
                			billing_id_arr.push(key);
                	});
                    var request = {
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        chart_date: $scope.current_appointment_date_obj.appointment_date,
                        doctor_id: ($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
                        patient_id: $scope.current_patient.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        billing_id: billing_id_arr,
                        email: $scope.pdf_url_data.email,
                        mobile_no: $scope.pdf_url_data.mobile_no
                    };
                    PatientService
                            .sharePDFMailInvoice(request, function (response) {
                            	$scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({content: response.message});
                                    $("#modal_print_invoice").modal("hide");
                                    $rootScope.gTrack('share_invoice');
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
			$scope.openEditInvNo = function(){
				$scope.invoice_no_data = {};
				$scope.invoice_no_data.inv_prefix = $scope.billing_invoice_no_data.inv_prefix;
				$scope.invoice_no_data.inv_counter = $scope.billing_invoice_no_data.inv_counter;
				$scope.invoice_no_data.invoice_autoincrement = true;
			}
			
            $scope.invoicenoUpdate = function(form){
				if (form.$valid) {
                    var request = {
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: 		$rootScope.current_clinic.clinic_id,
						doctor_id: 		($rootScope.current_doctor.user_id) ? $rootScope.current_doctor.user_id : $rootScope.currentUser.user_id,
						inv_prefix: 	$scope.invoice_no_data.inv_prefix,
						inv_counter: 	Number($scope.invoice_no_data.inv_counter),
						invoice_autoincrement: $scope.invoice_no_data.invoice_autoincrement
                    };
					PatientService.updateInvoiceNoSetting(request, function (response) {
						if (response.status) {
							ngToast.success({
								content: response.message
							});
							$scope.billing_invoice_no_data.inv_prefix  = $scope.invoice_no_data.inv_prefix;
							$scope.billing_invoice_no_data.inv_counter = Number($scope.invoice_no_data.inv_counter);
							$("#modal_invoiceno_update").modal("hide");
						} else {
							ngToast.danger({
								content: response.message
							});
						}
					}, function (error) {
						$rootScope.handleError(error);
					});
                }else{
					
				}
			}
			
			/* Image Comparation functions */
			$scope.setImageCompareData = function(){
				if($scope.objReportsCompareSel.length >= 2){
					$scope.isImageCompare = false;
					$scope.isDocumentCompare = false;
					$('#modal_patient_reports_image_compare').modal('show');
					var request = {
						report_id: 	[$scope.objReportsCompareSel[0],$scope.objReportsCompareSel[1]],
						patient_id: $scope.current_patient.user_id,
						clinic_id:  $rootScope.current_clinic.clinic_id
					}
					PatientService.getReportDetail(request, function (response) {
						if (response.data != undefined && response.data.length > 0) {
							var imgObj1 = {};
							if(response.data[0] != undefined){
								imgObj1 = {'title'       :response.data[0].file_report_name+' ('+$filter('date')(response.data[0].file_report_date, "dd/MM/y")+')',
										   'path'        :response.data[0].images[0],
										   'images'      :response.data[0].images,
										   'images_thumb':response.data[0].images_thumb,
										   'active_imgs' :0
										  };
							}
							var imgObj2 = {};
							if(response.data[1] != undefined){
								imgObj2 = {'title'       :response.data[1].file_report_name+' ('+$filter('date')(response.data[1].file_report_date, "dd/MM/y")+')',
										   'path'        :response.data[1].images[0],
										   'images'      :response.data[1].images,
										   'images_thumb':response.data[1].images_thumb,
										   'active_imgs' :0
										  };
							}
							
							if(imgObj1.path != undefined && (/\.(png|jpeg|jpg|gif)$/i).test(imgObj1.path) && imgObj2.path != undefined && (/\.(png|jpeg|jpg|gif)$/i).test(imgObj2.path)) {
								$scope.imageCompareObj.img1 = angular.copy(imgObj1);
								$scope.imageCompareObj.img2 = angular.copy(imgObj2);
								$timeout(function () {
									$scope.isImageCompare = true;
									$scope.$broadcast("picsDownloaded");
								}, 300);
							}else if(imgObj1.path != undefined && (/\.(pdf)$/i).test(imgObj1.path) && imgObj2.path != undefined && (/\.(pdf)$/i).test(imgObj2.path)) {
								$scope.imageCompareObj.doc1 = angular.copy(imgObj1);
								$scope.imageCompareObj.doc2 = angular.copy(imgObj2);
								$scope.isDocumentCompare = true;
							}else{
								$scope.imageCompareObj.doc1 = angular.copy(imgObj1);
								$scope.imageCompareObj.doc2 = angular.copy(imgObj2);
								$scope.isDocumentCompare = true;
							}
						}
					}, function (error) {
						$rootScope.handleError(error);
					});
				}
			}
			$scope.permanentDeleteReport = function(reportId){
				var request = {
					report_id : reportId,
				}
				SweetAlert.swal({
                    title: "Are you sure want to delete this report?",
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
                            PatientService
                                    .permanentDeleteReport(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            SweetAlert.swal("Deleted!");
                                            $scope.getTableDataReports();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
			}
			$scope.closeImageCompareObj = function(){
				$scope.$broadcast("removePreImgCompObj");
			}
			$scope.toggleReportsCompareSelection = function(reportId){
				var idx = $scope.objReportsCompareSel.indexOf(reportId);
				if (idx > -1) {
					$scope.objReportsCompareSel.splice(idx, 1);
				} else {
					$scope.objReportsCompareSel.push(reportId);
				}
				if($scope.objReportsCompareSel.length >= 2)
					$scope.allCompareObjDisabled = true;
				else
					$scope.allCompareObjDisabled = false;
			}
			/* Image Comparation functions end */
			
			/*Add Patient previous vitals module start*/
            $scope.getPreviousVital = function () {
            	$scope.previous_vitals = [];
            	$scope.previous_deleted_vital_ids = [];
                var request = {
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                };
                PatientService
                        .getPreviousVital(request, function (response) {
                            if(response.data.length > 0) {
                            	angular.forEach(response.data, function (value, key) {
                            		if(value.vital_report_temperature_type == 2) {
	                            		var temp = $filter('FCConvert')(value.vital_report_temperature, 1)
	                                    if (isNaN(temp)) {
	                                        temp = '';
	                                    }
                                	} else {
                                		var temp = value.vital_report_temperature;
                                	}
                            		$scope.previous_vitals.push({
					                    vital_report_id: value.vital_report_id,
					                    vital_report_appointment_id: value.vital_report_appointment_id,
					                    date: new Date(value.vital_report_date),
					                    weight: $filter('PoundToKG')(value.vital_report_weight),
					                    pulse: value.vital_report_pulse,
					                    resp: value.vital_report_resp_rate,
					                    spo: value.vital_report_spo2,
					                    systolic: value.vital_report_bloodpressure_systolic,
					                    diastolic: value.vital_report_bloodpressure_diastolic,
					                    temp: temp,
					                    celsuis: (value.vital_report_temperature_type == 2) ? true : false,
					                    teperature_taken_id: value.vital_report_temperature_taken,
					                    standing: value.vital_report_bloodpressure_type
					                });
                            	});
                            } else {
                            	$scope.addMorePatientVitals();
                            }
                        }, function (error) {
                            $rootScope.handleError(error);
                        });
            }
            $scope.addMorePatientVitals = function () {
                $scope.previous_vitals.push({
                    vital_report_id: '',
                    vital_report_appointment_id: '',
                    date: '',
                    weight: '',
                    pulse: '',
                    resp: '',
                    spo: '',
                    systolic: '',
                    diastolic: '',
                    temp: '',
                    celsuis: false,
                    teperature_taken_id: 6,
                    standing: 1
                });
            }
            $scope.removePatientVitals = function (index) {
            	if($scope.previous_vitals[index].vital_report_id != undefined && $scope.previous_vitals[index].vital_report_id != '' )
            		$scope.previous_deleted_vital_ids.push($scope.previous_vitals[index].vital_report_id);
                $scope.previous_vitals.splice(index, 1);
            }
            var vitals_report_start_date = new Date(year - 1, month, day);
            var vitals_report_end_date = new Date(year, month, day);
            vitals_report_end_date.setHours(23);
		    vitals_report_end_date.setMinutes(59);
		    vitals_report_end_date.setSeconds(59);
			$scope.vitals_report_picker = {
                datepickerOptions: {
                    minDate: vitals_report_start_date,
                    maxDate: vitals_report_end_date,
                },
                open: false
            };
            /* vital form validation */
            $scope.checkPreviousVitalsValidation = function (type, form, key) {
                if (type == 1) {
                    if ($scope.previous_vitals[key].pulse) {
                        var pulse = Number($scope.previous_vitals[key].pulse);
                        if (isNaN(pulse)) {
                            $scope.previous_vitals[key].pulse_error = "Invalid pulse";
                            form.pulse.$setValidity("pattern", false);
                            return;
                        }
                        if (pulse < 10 || pulse > 500) {
                            $scope.previous_vitals[key].pulse_error = "Pulse rate cannot be lesser than 10 nor greater than 500";
                            form.pulse.$setValidity("pattern", false);
                        } else {
                            form.pulse.$setValidity("pattern", true);
                        }
                    } else {
                        form.pulse.$setValidity("pattern", true);
                    }
                } else if (type == 2) {
                    if ($scope.previous_vitals[key].resp) {
                        var resp = Number($scope.previous_vitals[key].resp);
                        if (isNaN(resp)) {
                            $scope.previous_vitals[key].resp_error = "Invalid Respiration";
                            form.resp.$setValidity("pattern", false);
                            return;
                        }
                        if (resp < 10 || resp > 70) {
                            $scope.previous_vitals[key].resp_error = "Respiration rate cannot be lesser than 10 nor greater than 70";
                            form.resp.$setValidity("pattern", false);
                        } else {
                            form.resp.$setValidity("pattern", true);
                        }
                    } else {
                        form.resp.$setValidity("pattern", true);
                    }
                } else if (type == 3) {
                    if ($scope.previous_vitals[key].systolic) {
                        var systolic = Number($scope.previous_vitals[key].systolic);
                        if (isNaN(systolic)) {
                            $scope.previous_vitals[key].sys_error = "Invalid Systolic value";
                            form.systolic.$setValidity("pattern", false);
                            return;
                        }
                        if (systolic < 50 || systolic > 300) {
                            $scope.previous_vitals[key].sys_error = "Systolic Blood Pressure cannot be lesser than 50 nor greater than 300";
                            form.systolic.$setValidity("pattern", false);
                        } else {
                            form.systolic.$setValidity("pattern", true);
                        }
                    } else {
                        form.systolic.$setValidity("pattern", true);
                    }
                } else if (type == 4) {
                    if ($scope.previous_vitals[key].diastolic) {
                        var diastolic = Number($scope.previous_vitals[key].diastolic);
                        if (isNaN(diastolic)) {
                            $scope.previous_vitals[key].dis_error = "Invalid Diastolic value";
                            form.diastolic.$setValidity("pattern", false);
                            return;
                        }
                        if (diastolic < 25 || diastolic > 200) {
                            $scope.previous_vitals[key].dis_error = "Diastolic Blood Pressure cannot be lesser than 25 nor greater than 200";
                            form.diastolic.$setValidity("pattern", false);
                        } else {
                            form.diastolic.$setValidity("pattern", true);
                        }
                    } else {
                        form.diastolic.$setValidity("pattern", true);
                    }
                } else if (type == 5) {
                    if ($scope.previous_vitals[key].temp) {
                        var temp = Number($scope.previous_vitals[key].temp);
                        if (isNaN(temp)) {
                            $scope.previous_vitals[key].temp_error = "Invalid Temperature";
                            form.temp.$setValidity("pattern", false);
                            return;
                        }
                        if (!$scope.previous_vitals[key].celsuis) {
                            if (temp < 75.2 || temp > 109.4) {
                                $scope.previous_vitals[key].temp_error = "Temperature cannot be lesser than 75.2 nor greater than 109.4";
                                form.temp.$setValidity("pattern", false);
                            } else {
                                form.temp.$setValidity("pattern", true);
                            }
                        } else {
                            if (temp < 24 || temp > 43) {
                                $scope.previous_vitals[key].temp_error = "Temperature cannot be lesser than 24 nor greater than 43";
                                form.temp.$setValidity("pattern", false);
                            } else {
                                form.temp.$setValidity("pattern", true);
                            }
                        }
                    } else {
                        form.temp.$setValidity("pattern", true);
                    }


                } else if (type == 6) {
                    if ($scope.previous_vitals[key].weight) {
                        var weight = Number($scope.previous_vitals[key].weight);
                        if (isNaN(weight)) {
                            form.weight.$setValidity("pattern", false);
                            $scope.previous_vitals[key].error = "Invalid weight";
                            return;
                        }
                        if (weight <= 0 || weight > 200) {
                            $scope.previous_vitals[key].error = "Weight can not be less then 1 nor greater than 200";
                            form.weight.$setValidity("pattern", false);
                            return;
                        } else {
                            form.weight.$setValidity("pattern", true);
                        }
                    } else {
                        form.weight.$setValidity("pattern", true);
                    }
                } else if (type == 7) {
                    if ($scope.previous_vitals[key].spo) {
                        var spo = Number($scope.previous_vitals[key].spo);
                        if (isNaN(spo)) {
                            form.spo.$setValidity("pattern", false);
                            $scope.previous_vitals[key].spo_error = "Invalid spo";
                            return;
                        }
                        if (spo <= 0 || spo > 100) {
                            $scope.previous_vitals[key].spo_error = "SpO2 cannot be lesser than 1 nor greater than 100.";
                            form.spo.$setValidity("pattern", false);
                            return;
                        } else {
                            form.spo.$setValidity("pattern", true);
                        }
                    } else {
                        form.spo.$setValidity("pattern", true);
                    }
                }
            }
            /* Temparture convert code (F to C & C to F)*/
            $scope.changePreFCValue = function (unitType, key) {
                if ($scope.previous_vitals[key].temp) {
                    $scope.previous_vitals[key].temp = $filter('FCConvert')($scope.previous_vitals[key].temp, unitType);
                }
            }
            $scope.addPreviousVital = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                	var vitals_arr = [];
                	var is_error = false;
                	angular.forEach($scope.previous_vitals, function (value, key) {
                		if (!value.weight &&
                            !value.systolic &&
                            !value.diastolic &&
                            !value.pulse &&
                            !value.temp &&
                            !value.resp
                            ) {
	                        ngToast.danger('Please Enter Atleast One Vital Sign');
	                        is_error = true;
	                    }
                		var pressure_type = value.standing;
	                    var temperature_type = 1;
	                    var f_temp = value.temp;
	                    if (value.celsuis && value.temp) {
	                        f_temp = $filter('FCConvert')(value.temp, 2);
	                        temperature_type = 2;
	                    }
	                    var month = value.date.getMonth() + 1;
		                var day = value.date.getDate();
		                var year = value.date.getFullYear();
                		vitals_arr.push({
	                        vital_report_id: value.vital_report_id,
	                        vital_report_appointment_id: value.vital_report_appointment_id,
                			date: year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2'),
	                        sp2o: value.spo,
	                        weight: $filter('kgToPound')(value.weight),
	                        blood_pressure_systolic: value.systolic,
	                        blood_pressure_diastolic: value.diastolic,
	                        blood_pressure_type: pressure_type,
	                        pulse: value.pulse,
	                        temperature: f_temp,
	                        temperature_type: temperature_type,
	                        temperature_taken: value.teperature_taken_id,
	                        resp: value.resp,
                		});
                	});
                	if(is_error)
                		return false;
                    var request = {
                    	vitals_data : vitals_arr,
                    	previous_deleted_vital_ids : $scope.previous_deleted_vital_ids,
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    };
                    PatientService
                            .addPreviousVital(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                	$("#modal_patient_previous_vitals").modal("hide");
                                    ngToast.success({
                                        content: response.message,
                                        className: '',
                                        dismissOnTimeout: true,
                                        timeout: 5000
                                    });
                                    $scope.getPatientReportDetail();
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
			/*Add Patient previous vitals module END*/
			$scope.refer_rx_send_otp_data = {
				otp: ''
			}
			$scope.refer_rx_send_otp = function(){
				var request = {
					patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
				}
				SweetAlert.swal({
                    title: "Are you sure you want to check previous RX details of " + $scope.current_patient.user_name + " ?",
                    text: "To access it system will send OTP to " + $scope.current_patient.user_name + "'s mobile number, you need to add it on next screen to access details.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Continue",
                    closeOnConfirm: true},
                        function (isConfirm) {
                            if (!isConfirm) {
                                $rootScope.app.isLoader = false;
                                return;
                            }
                            PatientService
                                    .referRxSendOTP(request, function (response) {
                                        if (response.status == true) {
                            				$("#modal_patient_refer_rx_verify_otp").modal("show");
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                        } else {
		                                    ngToast.danger(response.message);
		                                }
                                    });
                        });
			}
			$scope.resend_refer_rx_otp = function(){
				var request = {
					is_resend_otp: true,
					patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
				}
                PatientService
                        .referRxSendOTP(request, function (response) {
                            if (response.status == true) {
                                ngToast.success({
                                    content: response.message,
                                    timeout: 5000
                                });
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
			}
			$scope.referRxVerifyOtp = function (form) {
                $scope.submitted = true;
                if (form.$valid) {
                    var request = {
                        otp: $scope.refer_rx_send_otp_data.otp,
                        patient_id: $scope.current_patient.user_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        user_id: $rootScope.currentUser.user_id,
                        doctor_id: $rootScope.current_doctor.user_id,
                        access_token: $rootScope.currentUser.access_token,
                    };
                    PatientService
                            .referRxVerifyOtp(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                	$scope.refer_patient_rx_data(true);
                                } else {
                                    ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.patient_past_rx_data = [];
            $scope.refer_patient_rx_data = function(is_verified_otp) {
            	$scope.patient_past_rx_data = [];
				var request = {
					is_verified_otp: is_verified_otp,
					patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
				}
                PatientService
                        .referPatientRxData(request, function (response) {
                            if (response.status == true) {
                            	if(response.is_otp_required == true) {
                            		$scope.refer_rx_send_otp();
                            	} else {
	                                $scope.patient_past_rx_data = {
	                                	patient_past_rx_data: response.data,
	                                	patient_name : $scope.current_patient.user_name
	                                };
	                                $("#modal_patient_refer_rx_verify_otp").modal("hide");
	                                var modalInstance = $uibModal.open({
					                    animation: true,
					                    templateUrl: 'app/views/patient/modal/view_past_rx_list.html?' + $rootScope.getVer(2),
					                    controller: 'ModalPastRxListCtrl',
					                    size: 'lg',
					                    backdrop: 'static',
					                    keyboard: false,
					                    resolve: {
					                        items: function () {
					                            return $scope.patient_past_rx_data;
					                        }
					                    }
					                });
                            	}
                            } else {
                                ngToast.danger(response.message);
                            }
                        });
			}
			/*Rx Upload*/
            /*$scope.removePatientReportImg = function (key) {
            	if($scope.rxUploadObj[key] != undefined)
                	$scope.rxUploadObj[key].temp_img = '';
            }*/
            $scope.resetRxUploadObj = function (key) {
                $scope.rxUploadObj[key].rx_upload_name = "Rx - " + $scope.current_patient.user_first_name + " " + $scope.current_patient.user_last_name;
                $scope.rxUploadObj[key].rx_upload_date = new Date(rx_year,rx_month,rx_day);
                $scope.rxUploadObj[key].temp_img = '';
                $scope.rxUploadObj[key].img = '';
                $scope.submitted = false;
            }
            $scope.removeRxUploadObj = function(key){
                var request = {
                    rx_upload_id: $scope.uploadedRxObj[key].rx_upload_id,
                };
                SweetAlert.swal({
                    title: "Are you sure want to delete this Rx?",
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
                            PatientService
                                    .deleteRxUploaded(request, function (response) {
                                        if (response.status == true) {
                                            ngToast.success({
                                                content: response.message,
                                                timeout: 5000
                                            });
                                            $scope.getPatientReportDetail();
                                        }
                                        $rootScope.app.isLoader = true;
                                    });
                        });
            }
            $scope.saveRxUploadObj = function (form, key) {
                $scope.submitted = true;
                if (form.$valid) {
                    var month = $scope.rxUploadObj[key].rx_upload_date.getMonth() + 1;
                    var day = $scope.rxUploadObj[key].rx_upload_date.getDate();
                    var year = $scope.rxUploadObj[key].rx_upload_date.getFullYear();
                    $scope.rxUploadObj[key].real_report_date = year + "-" + ('0' + month).slice('-2') + "-" + ('0' + day).slice('-2');
                    var request = {
                        patient_id: $scope.current_patient.user_id,
                        appointment_id: $scope.current_appointment_date_obj.appointment_id,
                        clinic_id: $rootScope.current_clinic.clinic_id,
                        date: $scope.rxUploadObj[key].real_report_date,
                        rx_upload_name: $scope.rxUploadObj[key].rx_upload_name,
                        img: $scope.rxUploadObj[key].img
                    };
                    PatientService
                            .uploadRx(request, function (response) {
                                $scope.submitted = false;
                                if (response.status) {
                                    ngToast.success({
                                        content: response.message,
                                        timeout: 5000
                                    });
                                    $scope.getPatientReportDetail();
                                } else {
                                	ngToast.danger(response.message);
                                }
                            }, function (error) {
                                $rootScope.app.isLoader = false;
                                $rootScope.handleError(error);
                            });
                }
            }
            $scope.uploadedRxObj = [];
            $scope.getUploadedRx = function () {
                var request = {
                    appointment_id: $scope.current_appointment_date_obj.appointment_id,
                    patient_id: $scope.current_patient.user_id,
                    clinic_id: $rootScope.current_clinic.clinic_id,
                    user_id: $rootScope.currentUser.user_id,
                    doctor_id: $rootScope.current_doctor.user_id,
                    access_token: $rootScope.currentUser.access_token,
                };
                $scope.isEditRxUploadData = undefined;
                PatientService
                    .getUploadedRx(request, function (response) {
                        if (response.status) {
                        	$scope.uploadedRxObj = response.data;
                        	if($scope.uploadedRxObj.length > 0)
                        		$scope.isEditRxUploadData = true;
                            angular
                                .forEach($scope.uploadedRxObj, function (value, key) {
                                    if (value.rx_upload_date) {
                                        value.rx_upload_date = $filter('date')(value.rx_upload_date, "dd/MM/y")
                                    }
                                });
                        } else {
                            ngToast.danger(response.message);
                        }
                    }, function (error) {
                        $rootScope.handleError(error);
                    });
            }
            $scope.uploadPreviousRx = function () {
            	$scope.rx_upload_pre.edit = true;
            }
            $scope.uploadPreviousReport = function () {
            	$scope.report_upload_pre.edit = true;
            }
            /*Rx Upload End*/
			/* main patient module end */
        });
/* Preview img code */
angular.module("app.dashboard")
        .directive("ngFileSelectPatient", function () {
            return {
                controller: 'PatientController',
                bindToController: true,
                link: function ($scope, el, attr, ctrl) {
                    el.bind("change", function (e) {
                        var file_obj = (e.srcElement || e.target).files[0];
                        var file_type = file_obj.name;
                        if ((/\.(png|jpeg|jpg|gif)$/i).test(file_type)) {
                            $scope.file = file_obj;
                            $scope.getFile();
                        }
                    })
                }
            }
        });
angular.module("app.dashboard")
        .directive("ngFileSelectPatientReport", function () {
            return {
                link: function ($scope, el, attr, ctrl) {
                    el.bind("change", function (e) {
                        var file_obj = (e.srcElement || e.target).files[0];
                        var file_type = file_obj.name;
                        var file_extension = file_type.split('.').pop();
                        if ((/\.(png|jpeg|jpg|gif|pdf)$/i).test(file_type)) {
                            $scope.file = file_obj;
                            if (attr.key == "clinical_notes") {
                                $scope.getFileClinicalNotes(file_obj, attr.pass);
                            } else {
                                $scope.getFileReport(file_obj, attr.key, file_extension);
                            }
                        }
                    })
                }
            }
        });
/* pagination scroll */
angular.module("app.dashboard")
	.directive('whenScrolled', function () {
		return function (scope, elm, attr) {
			var raw = elm[0];
			elm.bind('scroll', function () {
				if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
					scope.$apply(attr.whenScrolled);
				}
			});
		};
});
angular.module('app.dashboard').controller('ModalPastRxListCtrl', function ($scope, $rootScope, $filter, $uibModalInstance, items, $uibModal, PatientService) {
	$scope.patient_past_rx_data = items.patient_past_rx_data;
	$scope.patient_name = items.patient_name;
	$scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    $rootScope.updateBackDropModalHeight('view_past_rx_list');
    $scope.view_rx_pdf = function(obj) {
    	var request = {
				patient_id: obj.prescription_user_id,
				appointment_id: obj.prescription_appointment_id,
                doctor_id: obj.prescription_doctor_user_id,
                user_id: $rootScope.currentUser.user_id,
                access_token: $rootScope.currentUser.access_token,
			}
    	PatientService
            .getPrescriptionPdf(request, function (response) {
                if (response.status == true) {
                	var items = {pdf_url: response.pdf_url, prescription: obj}
                	var modalInstance = $uibModal.open({
			            animation: true,
			            templateUrl: 'app/views/patient/modal/view_past_rx_pdf.html?' + $rootScope.getVer(2),
			            controller: 'ModalViewRxPdfCtrl',
			            size: 'lg',
			            backdrop: 'static',
			            keyboard: false,
			            resolve: {
			                items: function () {
			                    return items;
			                }
			            }
			        });
                } else {
                    ngToast.danger(response.message);
                }
            });
	}
});
angular.module('app.dashboard').controller('ModalViewRxPdfCtrl', function ($scope, $rootScope, $filter, $uibModalInstance, items) {
	$scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    $scope.prescription = items.prescription;
    var pdf_url = btoa(encodeURI($rootScope.app.base_url + 'pdf_preview/web/view_pdf.php?file_url=' + btoa(encodeURI(items.pdf_url))));
	$scope.prescription_pdf = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + pdf_url;
    $rootScope.updateBackDropModalHeight('view_past_rx_pdf');
});
angular.module("app.dashboard").filter('range', function() {
  return function(input, total) {
    total = parseInt(total);
    for (var i=1; i<=total; i++) {
      input.push(i);
    }
    return input;
  };
});