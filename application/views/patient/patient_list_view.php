<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title"><?=$this->patient_auth->get_patient_name();?></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	</div>
	<form name="change_patient_frm" id="change_patient_frm" method="post" onsubmit="return false;">
		<div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<?php 
					foreach ($patients as $key => $value) { ?>
						<div class="relative">
				            <div class="form-check form-check-inline static">
				                <span class="radio-inline">
				                    <input type="radio" id="user_<?= $value->user_id; ?>" name="patient_id" value="<?= $value->user_id; ?>" <?= ($value->user_id==$this->patient_auth->get_user_id()) ? 'checked' : ''; ?> class="form-check-input">
				                    <label class="form-check-label" for="user_<?= $value->user_id; ?>"><?= $value->user_first_name . ' ' . $value->user_last_name; ?><?=isset($value->mapping_relation) ? '' : ' (Self)'; ?></label>
				                </span>
				            </div>
				        </div>
			    	<?php } ?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="contact-form">
				<a href="javascript:void(0);" class="btns" data-dismiss="modal">Cancel</a>
				<button type="button" class="btns-hide" id="change_patient_btn">Change Patient</button>
			</div>
		</div>
	</form>
</div>