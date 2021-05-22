<?php $this->load->view('patient/header_layout'); ?>
<script src="<?= ASSETS_PATH ?>web/js/kit.fontawesome.b155a71cdd.js?<?= WEB_VERSION; ?>" crossorigin="anonymous"></script>
<script src="<?= ASSETS_PATH ?>web/js/opentok.min.js?<?= WEB_VERSION; ?>"></script>
<style>
#videos {
    width: 100%;
    height: 100%;
    margin-left: auto;
    margin-right: auto;
}

#subscriber {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 10;
}

#publisher {
    position: absolute;
    width: 150px;
    height: 150px;
    bottom: 10px;
    left: 10px;
    z-index: 100;
    border: 3px solid white;
    border-radius: 3px;
}
</style>
<body data-spy="scroll" data-target=".header" data-offset="50">
    <!-- Page loader -->
    <div id="preloader"></div>
    <!-- header section start -->
    <?php
    $this->load->view('patient/header');
    ?>
    <section class="ptb-90 main-section" style="position: relative;">
		<div id="dvVideo">
			<div id="videos" class="text-center">
                <label id="lblDoctorMsg">Doctor is yet to start the teleconsultation.<br/><strong>Please wait...</strong></label>
                <label id="lblDoctorMsg1">Doctor disconnected from the video call.</label>
				<div id="subscriber"></div>
				<div id="publisher"></div>
			</div>
			<div id="buttonsWrap" style="z-index: 100;position: absolute;bottom: 15px;left: 45%;">
			   <button type="button" id="btnMute" style="height: 40px;width: 45px;background-color: #d8faf6;border: 1px solid #30aca4;"><i class="fas fa-microphone fa-lg"></i></button>
			   <button type="button" id="btnUnMute" ng-show="isMute" style="height: 40px;width: 45px;background-color: #30aca5;border: none;"> <i class="fas fa-microphone-slash fa-lg"></i></button>
			   <button type="button" id="btnDisableVideo" ng-show="!isVideoMute" style="height: 40px;width: 45px;background-color: #d8faf6;border: 1px solid #30aca4;"><i class="fa fa-video-camera fa-lg"></i></button>
			   <button type="button" id="btnEnableVideo" ng-show="isVideoMute" style="height: 40px;width: 45px;background-color: #30aca5;border: none;"><i class="fa fa-video-slash fa-lg"></i></button>
			   <button type="button" id="btnEndCall" style="height: 40px;width: 45px;background-color: #30aca5;border: none;"><i class="fa fa-phone-slash fa-lg"></i></button>
			</div>
		</div>
		<div id="dvCallEnd" style="display: block;text-align: center;margin-top: 10%;">
			<label style="font-size: 16px;">Video call ended</label>
		</div>
    </section>
    <input type="hidden" name="user_doctor_id" id="user_doctor_id" value="<?= $doctor_id ?>">
    <input type="hidden" name="user_patient_id" id="user_patient_id" value="<?= $patient_id ?>">
    <input type="hidden" name="user_appointment_id" id="user_appointment_id" value="<?= $appointment_id ?>">
    <input type="hidden" name="apiKey" id="apiKey" value="<?= $apiKey ?>">
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
