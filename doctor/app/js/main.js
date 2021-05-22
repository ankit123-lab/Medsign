function setFullScreenMode(id,objE){
	if($.fullscreen.isNativelySupported()){
		if ($.fullscreen.isFullScreen()){
			if($('#btnFullScreen') && $('#btnFullScreen').hasClass('exitfullscreen')){
				if($('#btnFullScreen')){
					$('#btnFullScreen').removeClass('exitfullscreen').addClass('requestfullscreen');
				}
				$.fullscreen.exit();
				setTimeout(function(){
					$('#'+id).fullscreen(); 
					if(id == 'vitals-monitoring-fullscreen-mode') {
						$('#vitals-monitoring-fullscreen-mode').find('.btnfc').html('Full Screen');
						$(".vital-full-screen").click();
					}
				}, 500);
			}else{
				$.fullscreen.exit();
			}
		}else{
			$('#'+id).fullscreen();
		}
	}
}

function removeFullScreenMode(id){
	if($.fullscreen.isNativelySupported()){
		if ($.fullscreen.isFullScreen()){
			$.fullscreen.exit();
		}
	}
}

$(document).ready(function(){
	$(document).bind('fscreenchange', function(e, state, elem){
		if($.fullscreen.isFullScreen()){
			if($('#patient-compliance-fullscreen-mode')){
				$('#patient-compliance-fullscreen-mode').find('.btnfc').html('Normal');
			}
			if($('#vitals-monitoring-fullscreen-mode')){
				$('#vitals-monitoring-fullscreen-mode').find('.btnfc').html('Normal');
				$('#vitals-monitoring-fullscreen-mode').find('.print_icon_images').removeClass('hide');
			}
			if($('#patient-analytics-fullscreen-mode')){
				$('#patient-analytics-fullscreen-mode').find('.btnfc').html('Normal');
			}
			if($('#reports-healthanalytics-fullscreen-mode')){
				$('#reports-healthanalytics-fullscreen-mode').find('.btnfc').html('Normal');
			}
		}else{
			if($('#patient-compliance-fullscreen-mode')){
				$('#patient-compliance-fullscreen-mode').find('.btnfc').html('Full Screen');
			}
			if($('#vitals-monitoring-fullscreen-mode')){
				$('#vitals-monitoring-fullscreen-mode').find('.print_icon_images').addClass('hide');
				$(".vital-full-screen").click();
				$('#vitals-monitoring-fullscreen-mode').find('.btnfc').html('Full Screen');
			}
			if($('#patient-analytics-fullscreen-mode')){
				$('#patient-analytics-fullscreen-mode').find('.btnfc').html('Full Screen');
			}
			if($('#reports-healthanalytics-fullscreen-mode')){
				$('#reports-healthanalytics-fullscreen-mode').find('.btnfc').html('Full Screen');
			}
			if($('#btnFullScreen')){
				$('#btnFullScreen').removeClass('exitfullscreen').addClass('requestfullscreen');
			}
		}
	});
});