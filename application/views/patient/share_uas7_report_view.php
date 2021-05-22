<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="shareUAS7ReportModalLabel">Share UAS7 Report</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	</div>
	<form name="share_uas7_report_frm" id="share_uas7_report_frm" method="post" onsubmit="return false;">
		<div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<center class="element-hide">
						<div class="share-error alert alert-danger">
							
						</div>
					</center>
				</div>
				<div class="col-lg-12 contact-form">
					<table class="share-uas7-table" style="width: 100%;">
						<tbody>
							<tr>
								<td align="right" width="25%"><b>Name:</b></td>
								<td align="left" width="70%"><input class="name form-control" type="text" name="share_doctor_name" id="share_doctor_name" value=""></td>
							</tr>
							<tr>
								<td align="right" width="25%"><b>Email:</b></td>
								<td align="left" width="70%"><input class="name form-control" type="text" name="share_doctor_email" value=""></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="contact-form">
				<a href="javascript:void(0);" class="btns" data-dismiss="modal">Cancel</a>
				<button type="button" class="btns" id="share_uas7_report_btn">Share</button>
			</div>
		</div>
	</form>
</div>