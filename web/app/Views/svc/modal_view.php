<div class="row">
	<?php
	$tab = [ "sr_info" 		=> array("fas fa-file-invoice", "SR Info"),
			     "action_log"	=> array("fab fa-stack-overflow", "Action Log")];

	if(isset($approve_sr) && $approve_sr) {
		$tab["approve_status"] = array("fas fa-user-check", "Approve Status");
	}; 

  if(is_PIC_RO() && $trx_sr['svc'] == "SVC03") {
    $tab["request_order"] = array("fas fa-file-prescription", "Request Order");
  }

  ?>

	<div class="col-12 col-md-12 col-lg-12 col-xl-12">
		<section class="section">
			<div class="card">
              <div class="card-header">
                <h4>Service Request Details</h4>
              </div>
              <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                  <?php $i = 0; foreach ($tab as $key => $value) { ?>
                  <li class="nav-item">
                    <a class="nav-link <?php if($i == 0) echo "active"; ?>" data-toggle="tab" href="#<?= $key ?>" role="tab" aria-selected="true"><i class="<?= $value[0]; ?>"></i> <?= $value[1]; ?></a>
                  </li>
                  <?php $i = 1; } ?>
                </ul>

                <div class="tab-content tab-bordered">
                	<?php $i = 0; foreach ($tab as $key => $value) { ?>
                	  <div class="tab-pane fade <?php if($i == 0) echo "show active"; ?>" id="<?= $key ?>" role="tabpanel">
	                    <?= view('svc/tab/'.$key); ?>
	                  </div>
                	<?php $i = 1; } ?>
                </div>
              </div>
            </div>
		</section>
	</div>

	<div class="col-12 col-md-12 col-lg-12 col-xl-12">
		<?= view('svc/modal_left'); ?>
	</div>
</div>
