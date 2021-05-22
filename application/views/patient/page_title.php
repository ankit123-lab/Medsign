<div class="row">
    <div class="col-lg-12 btn-m-0">
        <h1 class="page-title"><?= $title; ?>
			<span class="change_patient" style="cursor:pointer;float:right;font-size:small;margin-top:5px;padding:5px;border:1px solid #48ada6;"><?= (strlen($this->patient_auth->get_patient_name()) > 13) ? substr($this->patient_auth->get_patient_name(), 0, 13).'..' : $this->patient_auth->get_patient_name(); ?></span>
        </h1>
    </div>
</div>