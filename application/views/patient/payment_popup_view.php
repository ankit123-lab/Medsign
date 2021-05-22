<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="paymentModalLabel">Payment of UAS7 Diary</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	</div>
	<form name="create_payment_order" id="create_payment_order" method="post" onsubmit="return false;">
		<input type="hidden" name="utility_name" id="sub_amount" value="<?= $utility_name; ?>">
		<input type="hidden" name="is_apply_igst" id="is_apply_igst" value="<?= $is_apply_igst; ?>">
		<div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<?php if(!empty($errors)) { ?>
					<center>
						<div class="alert alert-danger">
							<?= $errors; ?>
						</div>
					</center>
					<?php } else { ?>
					<table class="payment-table" style="width: 100%;">
						<tbody>
							<tr>
								<td align="right">Amount</td>
								<td align="right"><?= number_format($utility_price,2); ?></td>
							</tr>
							<tr>
								<td align="right">GST(<?= number_format($gst_pecent,2,'.',''); ?>%)</td>
								<td align="right"><?= number_format($gst_amount,2,'.',''); ?></td>
							</tr>
							<tr>
								<td align="right">Total</td>
								<td align="right"><?= number_format($total_price,2,'.',''); ?></td>
							</tr>
						</tbody>
					</table>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="contact-form">
				<a href="javascript:void(0);" class="btns" data-dismiss="modal">Cancel</a>
			<?php if(empty($errors)) { ?>
				<button type="button" class="btns-hide" id="pay_now_btn">Pay Now</button>
			<?php } ?>
			</div>
		</div>
	</form>
</div>