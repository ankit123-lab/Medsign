angular.module("app.dashboard")
        .directive("ngAnatomicalDiagrams", function ($rootScope) {
            return {
                controller: function($scope, PatientService, $filter, $timeout, angularLoad, ngToast) {
                       $scope.diagram_category = [];
                       $scope.diagram_sub_category = [];
                       $scope.diagram_list = [];
                       $scope.isShowViewMore = true;
                       $scope.isShowDiagramsDetail = false;
                       $scope.isShowImageDiagrams = false;
                       $scope.isShowVideo = false;
                       $scope.isShowPdf = false;
                       $scope.anatomical_diagrams_video_url = '';
                       $scope.anatomical_diagrams_file_path = '';
                       $scope.diagram_search = {
                            category: '',
                            sub_category: '',
                            search: ''
                       };
                       $scope.diagrams_current_page = 1;
                       $scope.diagrams_per_page = 5;
                       $scope.clearSearch = function() {
                          $scope.diagram_search = {
                            category: '',
                            sub_category: '',
                            search: ''
                          };
                          $scope.diagram_sub_category = [];
                          $scope.diagrams_current_page = 1;
                          $scope.diagram_list = [];
                          $scope.getDaigramsList();
                       }
                       $scope.backToDiagrams = function() {
                          $scope.isShowDiagramsDetail = false;
                          $scope.anatomical_diagrams_video_url = '';
                          $scope.unSetDiagramVideoUrls(0);
                       }
                       $scope.viewDiagrams = function(tab) {
                          $scope.anatomical_diagrams_video_url = '';
                          $scope.isShowImageDiagrams = false;
                          $scope.show_loadWPaintLib = false;
                          $scope.isShowVideo = false;
                          $scope.isShowPdf = false;
                          $scope.show_loadWPaintLib = false;
                          if(tab == 1) {
                            $scope.unSetDiagramVideoUrls(0);
                            $scope.isShowImageDiagrams = true;
                          } else if(tab == 2) {
                            $scope.unSetDiagramVideoUrls(0);
                            $scope.isShowPdf = true;
                            $scope.document_share_data = {
                            	status: false,
                            	anatomical_diagrams_id: $scope.diagrams_detail.anatomical_diagrams_id,
                            	document_url: $scope.diagrams_detail.anatomical_diagrams_file_path,
                            	type: 2
                            }
                            $scope.getDocumentFromShare();
                          } else if(tab == 3) {
                            $scope.isShowVideo = true;
                            $scope.anatomical_diagrams_video_url = $scope.diagrams_detail.anatomical_diagrams_video_url;
                            $scope.document_share_data = {
                            	status: false,
                            	anatomical_diagrams_id: $scope.diagrams_detail.anatomical_diagrams_id,
                            	document_url: $scope.anatomical_diagrams_video_url,
                            	type: 1
                            }
                            $scope.getDocumentFromShare();
                          }
                       }
                       $scope.getDocumentFromShare = function() {
                       		var request = {
                   				user_id: $rootScope.currentUser.user_id,
		                        access_token: $rootScope.currentUser.access_token,
								patient_id : $scope.current_patient.user_id,
								doctor_id : $rootScope.current_doctor.user_id,
								appointment_id : $scope.current_appointment_date_obj.appointment_id,
								document_type : $scope.document_share_data.type,
								document_id: $scope.document_share_data.anatomical_diagrams_id
							};
                       		PatientService.getDocumentFromShare(request, function (response) {
								if(response.status) {
									$scope.document_share_data.status = true;
								} else {
									$scope.document_share_data.status = false;
								}
							}, function (error) {
								$rootScope.handleError(error);
							});
                       }
                       $scope.diagramsDetails = function(diagrams_id) {
                          var selectedDiagramObj = $filter('filter')($scope.diagram_list, {'anatomical_diagrams_id':diagrams_id},true);
                          $scope.diagrams_detail = selectedDiagramObj[0];
                          if($scope.diagrams_detail.anatomical_diagrams_file_path != null) {
                          	var pdf_url = btoa(encodeURI($rootScope.app.base_url + 'pdf_preview/web/view_pdf.php?file_url=' + btoa(encodeURI($scope.diagrams_detail.anatomical_diagrams_file_path))));
							$scope.anatomical_diagrams_file_path = $rootScope.app.base_url + "pdf_preview/web/pdf_preview.php?charting_url=" + pdf_url; 
                          }
                          $scope.isShowImageDiagrams = false;
                          $scope.show_loadWPaintLib = false;
                          $scope.isShowVideo = false;
                          $scope.isShowPdf = false;
                          $scope.isShowDiagramsDetail = true;
                          $scope.anatomical_diagrams_video_url = '';
                          if($scope.diagrams_detail.is_show_image) {
                            $scope.isShowImageDiagrams = true;
                          } else if($scope.diagrams_detail.is_show_video) {
                            $scope.isShowVideo = true;
                            $scope.anatomical_diagrams_video_url = $scope.diagrams_detail.anatomical_diagrams_video_url;
                            $scope.document_share_data = {
                            	status: false,
                            	anatomical_diagrams_id: $scope.diagrams_detail.anatomical_diagrams_id,
                            	document_url: $scope.anatomical_diagrams_video_url,
                            	type: 1
                            }
                            $scope.getDocumentFromShare();
                          } else if($scope.diagrams_detail.is_show_pdf) {
                            $scope.isShowPdf = true;
                            $scope.document_share_data = {
                            	status: false,
                            	anatomical_diagrams_id: $scope.diagrams_detail.anatomical_diagrams_id,
                            	document_url: $scope.diagrams_detail.anatomical_diagrams_file_path,
                            	type: 2
                            }
                            $scope.getDocumentFromShare();
                          }
                          setTimeout( function () {
                            var modal_height = $('#modal_diagrams .modal-content').height();
                            var backdrop_height = $('#modal_diagrams .modal-backdrop').height();
                            if($('#modal_diagrams') && modal_height > backdrop_height)
                              $('#modal_diagrams .modal-backdrop').height(modal_height + 300);
                          },100);
                       }
                       $scope.addToPrescription = function(type) {
                       		var request = {
                   				user_id: $rootScope.currentUser.user_id,
		                        access_token: $rootScope.currentUser.access_token,
								patient_id : $scope.current_patient.user_id,
								doctor_id : $rootScope.current_doctor.user_id,
								appointment_id : $scope.current_appointment_date_obj.appointment_id,
								document_type : type,
								document_url: $scope.document_share_data.document_url,
								document_id: $scope.document_share_data.anatomical_diagrams_id,
								document_is_add: $scope.document_share_data.status
							};
                       		PatientService.daigramsAddToPrescription(request, function (response) {
								ngToast.success({
									content: response.message
								});
							}, function (error) {
								$rootScope.handleError(error);
							});
                       }
                       $scope.viewMore = function() {
                          $scope.diagrams_current_page++;
                          $scope.getDaigramsList();
                          setTimeout( function () {
                            var modal_height = $('#modal_diagrams .modal-content').height();
                            var backdrop_height = $('#modal_diagrams .modal-backdrop').height();
                            if($('#modal_diagrams') && modal_height > backdrop_height)
                              $('#modal_diagrams .modal-backdrop').height(modal_height + 300);
                          },100);
                       }
                       $scope.getDiagramCategoryWise = function(cat_id) {
                          $scope.diagram_search = {
                            category: [cat_id],
                            sub_category: '',
                            search: ''
                          };
                          $scope.diagram_sub_category = [];
                          $scope.diagrams_current_page = 1;
                          $scope.diagram_list = [];
                          $scope.getDaigramsList();
                          $scope.getDiagramSubCategory();
                       }
						$scope.getDaigramsSearch = function() {
							$scope.diagrams_current_page = 1;
							$scope.diagram_list = [];
							$scope.getDaigramsList();
						}
						$scope.getDaigramsList = function() {
							var request = $scope.diagram_search;
							request.page = $scope.diagrams_current_page;
							request.per_page = $scope.diagrams_per_page;
							PatientService.getDaigramsData(request, function (response) {
								angular.forEach(response.data, function (value, key) {
								  $scope.diagram_list.push(value);
								});
								if($scope.diagram_list.length == response.totals) {
								  $scope.isShowViewMore = false;
								} else {
								  $scope.isShowViewMore = true;
								}
							}, function (error) {
								$rootScope.handleError(error);
							});
						}
                        $scope.unSetDiagramVideoUrls= function(val){
                          if(val == 1) {
                            $scope.isShowDiagramsDetail = false;
                            $scope.anatomical_diagrams_video_url = '';
                          }
                          $timeout(function(){ $('#modal_diagrams').find("iframe.video_iframe").attr("src", ""); });
                        }
						$scope.getDiagramCategory = function() {
							PatientService.getDiagramCategory('', function (response) {
								$scope.diagram_category = response.data;
							}, function (error) {
								$rootScope.handleError(error);
							});
						}
						$scope.getDiagramCategory();
						$scope.getDiagramSubCategory = function() {
							if($scope.diagram_search.category.length == 0) {
								$scope.diagram_sub_category = [];
								return false;
							}
							var request = {category_id: $scope.diagram_search.category}
							PatientService.getDiagramSubCategory(request, function (response) {
								$scope.diagram_sub_category = response.data;
							}, function (error) {
								$rootScope.handleError(error);
							});
						}
						
						$scope.fillcollor_black = function() {
							$.extend($.fn.wPaint.defaults, {
							  fillStyle:   '#000000', // starting fill style
							});
						}
						$scope.setPaintLibNow = function(reAssign){
							var images = [];
							$.fn.wPaint.menus.main = {
								  img: '/plugins/main/img/icons-menu-main.png',
								  items: {
								    undo: {
								      icon: 'generic',
								      title: 'Undo',
								      index: 0,
								      callback: function () { this.undo(); }
								    },
								    redo: {
								      icon: 'generic',
								      title: 'Redo',
								      index: 1,
								      callback: function () { this.redo(); }
								    },
								    clear: {
								      icon: 'generic',
								      title: 'Clear',
								      index: 2,
								      callback: function () { this.clear(); }
								    },
								    pencil: {
								      icon: 'activate',
								      title: 'Pencil',
								      index: 6,
								      callback: function () { this.setMode('pencil'); }
								    },
								    eraser: {
								      icon: 'activate',
								      title: 'Eraser',
								      index: 8,
								      callback: function () { this.setMode('eraser'); }
								    },more: {
								        icon: 'activate',
								        title: 'More',
								        index: 15,
								        callback: function () {  }
								    },
								    bucket: {
								      icon: 'activate',
								      title: 'Bucket',
								      index: 9,
								      callback: function () { this.setMode('bucket'); }
								    },
								    fillStyle: {
								      icon: 'colorPicker',
								      title: 'Fill Color',
								      index: 10,
								      callback: function (color) { this.setFillStyle(color); }
								    },
								    lineWidth: {
								    	icon: 'select',
								    	title: 'Stroke Width',
								    	range: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
								    	value: 5,
								    	callback: function (width) { this.setLineWidth(width); }
								    },
								    strokeStyle: {
								        icon: 'colorPicker',
								        title: 'Stroke Color',
								        callback: function (color) { this.setStrokeStyle(color); }
								    },rectangle: {
								        icon: 'activate',
								        title: 'Rectangle',
								        index: 3,
								        callback: function () { this.setMode('rectangle'); }
								      },
								      ellipse: {
								        icon: 'activate',
								        title: 'Ellipse',
								        index: 4,
								        callback: function () { this.setMode('ellipse'); }
								      },
								      line: {
								        icon: 'activate',
								        title: 'Line',
								        index: 5,
								        callback: function () { this.setMode('line'); }
								      },
								    save: {
									    icon: 'generic',
									    title: 'Save Image',
									    img: 'plugins/file/img/icons-menu-main-file.png',
									    index: 0,
									      callback: function () {
									        this.options.saveImg.apply(this, [this.getImage()]);
									      }
									    }
								}
							};
							$.fn.wPaint.menus.main.items.text = {
							    icon: 'menu',
							    after: 'pencil',
							    title: 'Text',
							    index: 7,
							    callback: function () { this.setMode('text'); }
							};
							// init wPaint
							// extend defaults
							$.extend($.fn.wPaint.defaults, {
								mode:        'pencil',  // set mode
							  	lineWidth:   '5',       // starting line width
							  	fillStyle:   'rgba(100, 100, 100, 0)', // starting fill style
							  	strokeStyle: '#FFFF00'  // start stroke style
							});
							$.extend($.fn.wPaint.defaults, {
							  fontSize       : '18',    // current font size for text input
							  fontFamily     : 'Arial', // active font family for text input
							  fontBold       : false,   // text input bold enable/disable
							  fontItalic     : false,   // text input italic enable/disable
							  fontUnderline  : false    // text input italic enable/disable
							});
							
							$scope.wPaint = $('#wPaint').wPaint({
								menuOrientation: 'horizontal',
								menuOffsetLeft: -12,
								menuOffsetTop: -50,
								saveImg: function(image){
									var _this = this;
									var request = {
										patient_id : $scope.current_patient.user_id,
										doctor_id : $rootScope.current_doctor.user_id,
										clinic_id : $rootScope.current_clinic.clinic_id,
										appointment_date : $scope.current_appointment_date_obj.appointment_date,
										appointment_id : $scope.current_appointment_date_obj.appointment_id,
										diagrams_title : $scope.diagrams_detail.anatomical_diagrams_title,
									};
									PatientService.upload_drs_art_imgs(image,request,function (response){
										_this._displayStatus('Image saved successfully');
										//images.push(resp.img);
									}, function (error) {
										$rootScope.handleError(error);
									});
								},
								loadImgBg: function() {
									this._showFileModal('bg', images);
								},
								loadImgFg: function() {
									this._showFileModal('fg', images);
								},
								path: (reAssign != undefined && reAssign == true) ? '' : 'app/plugins/wPaint-2.5.0/',
								theme: 'standard classic',
								autoScaleImage:  true, // auto scale images to size of canvas (fg and bg)
								autoCenterImage: true, // auto center images (fg and bg, default is left/top corner)
								bg: $rootScope.app.uploadsPath+'/anatomical_diagram/'+$scope.diagrams_detail.anatomical_diagrams_image_name
							});
							
							$timeout(function(){
								$('.wPaint-menu-icon-name-loadBg').css('display', 'none'); 
								$('.wPaint-menu').css('width','auto');
								$('.wPaint-menu-holder').css('text-align', 'left');
								$(".wPaint-menu-icon-name-rectangle").addClass('hide');
								$(".wPaint-menu-icon-name-ellipse").addClass('hide');
								$(".wPaint-menu-icon-name-line").addClass('hide');
								$(".wPaint-menu-icon-name-bucket").addClass('hide');
								$(".wPaint-menu-icon-name-fillStyle").addClass('hide');
								$(".wPaint-menu-icon-name-lineWidth").addClass('hide');
								$(".wPaint-menu-icon-name-strokeStyle").addClass('hide');
								$(".wColorPicker-palette-simple").css({"margin-left":"9px"});
							}, 10);
						}
						
					    $scope.show_loadWPaintLib = false;
					    $scope.loadWPaintLib = function() {
							if(jQuery().wPaint == undefined){
								angularLoad.loadScript('app/plugins/wPaint-2.5.0/wPaint.min.js?' + $rootScope.getVer(2)).then(function() {
									angularLoad.loadCSS('app/plugins/wPaint-2.5.0/lib/wColorPicker.min.css?' + $rootScope.getVer(2));
									angularLoad.loadCSS('app/plugins/wPaint-2.5.0/wPaint.min.css?' + $rootScope.getVer(2));
									angularLoad.loadScript('app/plugins/wPaint-2.5.0/lib/wColorPicker.min.js?' + $rootScope.getVer(2));
									angularLoad.loadScript('app/plugins/wPaint-2.5.0/plugins/main/wPaint.menu.main.min.js?' + $rootScope.getVer(2)).then(function(){
										angularLoad.loadScript('app/plugins/wPaint-2.5.0/plugins/text/wPaint.menu.text.min.js?' + $rootScope.getVer(2)).then(function(){
											angularLoad.loadScript('app/plugins/wPaint-2.5.0/plugins/shapes/wPaint.menu.main.shapes.min.js?' + $rootScope.getVer(2)).then(function(){
												angularLoad.loadScript('app/plugins/wPaint-2.5.0/plugins/file/wPaint.menu.main.file.min.js?' + $rootScope.getVer(2)).then(function(){
													$scope.setPaintLibNow(false);
												});
											});
										});
									});
									$scope.show_loadWPaintLib = true;
									$scope.isShowImageDiagrams = false;
								}).catch(function() {
									console.log('Doctor art lib error. Please contact support.');
								});
							}else{
								if($scope.wPaint == undefined){
									$('#wPaint').unbind("wPaint");
									$('#wPaint').empty();
									$timeout(function(){
										$scope.setPaintLibNow(true);
									},500);
								}else{
									$('#wPaint').wPaint('clear'); 
									$('#wPaint').wPaint('bg',$rootScope.app.uploadsPath+'/anatomical_diagram/'+$scope.diagrams_detail.anatomical_diagrams_image_name);
									$(".wPaint-menu-icon-name-rectangle").addClass('hide');
									$(".wPaint-menu-icon-name-ellipse").addClass('hide');
									$(".wPaint-menu-icon-name-line").addClass('hide');
									$(".wPaint-menu-icon-name-bucket").addClass('hide');
									$(".wPaint-menu-icon-name-fillStyle").addClass('hide');
									$(".wPaint-menu-icon-name-lineWidth").addClass('hide');
									$(".wPaint-menu-icon-name-strokeStyle").addClass('hide');
									$(".wColorPicker-palette-simple").css({"margin-left":"9px"});
								}
								$scope.show_loadWPaintLib = true;
								$scope.isShowImageDiagrams = false;
							}
						}
                },
                templateUrl: 'app/views/patient/diagrams_modal.html?' + $rootScope.getVer(2)
            }
        });