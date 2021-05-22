<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title">Health Analytic</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">
		<div class="row">
			<div class="col-lg-12 btn-m-0">
				<div class="contact-form">
					<div class="parent_list">
						<?php foreach ($analytics_test as $key => $value) { ?>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-check">
										<input name="terms_conditions" type="checkbox" value="<?= $value['health_analytics_test_id']; ?>" class="form-check-input get_analytics">
										<label class="form-check-label" for="terms_conditions"><?= $value['health_analytics_test_name']; ?></label>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="selected_analytic_list child_list" style="display: none;">
						<?php foreach ($patient_analytics as $key => $value) { ?>
							<div class="row analytic_row analytic_id_<?= $value['id']; ?>">
								<textarea style="display: none;" class="analytics_data"><?= json_encode($value) ?></textarea>
								<div class="col-lg-10">
									<label><?= $value['name']; ?></label>
								</div>
								<div class="col-lg-2">
									<a href="javascript:void(0);" class="remove_analytic">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
											<path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
										</svg>
									</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<div class="contact-form">
			<a href="javascript:void(0);" class="btns" data-dismiss="modal">Cancel</a>
			<button type="button" class="btns-hide parent_list" id="params_next">Next</button>
			<button type="button" class="btns-hide child_list" id="add_params" style="display: none;">Done</button>
		</div>
	</div>
</div>