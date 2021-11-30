<?php $this->session = \Config\Services::session();
      $nik = $this->session->get('nik');
      $supported_by = (array)json_decode($trx_sr['supported_by']); 
      $insertComment = ($trx_sr['status'] != "R" && $trx_sr['status'] != "1" && (isHO() || $trx_sr['pic'] == $nik || ($supported_by && isset($supported_by[$nik])))); ?>

<form id="sr_actionForm" method="POST" action="<?= base_url('SR/actionSR'); ?>">
<div class="row">
  <div class="col-3">
    <div class="list-group" id="list-tab" role="tablist">
      <?php $i = 0; foreach ($trx_sr['detail_sr'] as $key => $value) { ?>
      <a class="list-group-item list-group-item-action <?php if($i == 0) echo "active"; ?>" data-toggle="list" href="#tmp<?= $value['id'] ?>" role="tab">
      	<b><i><?= $value['name_DSVC']; ?></i></b>
      	<?php if(trim($value['desc_DSVC'])) echo '<br/>'.$value['desc_DSVC']; ?>
        <br/><b><i>Qty : <?= $value['qty'] ?></i></b>
      </a>
  	  <?php $i = 1; } ?>
    </div>
  </div>
  <div class="col-9">
    <div class="tab-content">

    <?php $i = 0; foreach ($trx_sr['detail_sr'] as $key => $value) { ?>
      <div class="tab-pane fade <?php if($i == 0) echo "show active"; ?>" id="tmp<?= $value['id'] ?>" role="tabpanel">
      	<?php if($value['action']) { ?>
        <ul class="list-unstyled list-unstyled-border">
        	<?php foreach ($value['action'] as $key2 => $value2) { ?>
	        <li class="media">
	          <img class="mr-3 rounded-circle" width="50" src="<?= site_url("public/stisla/img/avatar/avatar-1.png"); ?>" alt="avatar">
	          <div class="media-body">
	            <div class="float-right" style="font-size: 11px;"><?= $value2['log_date']; ?></div>
	            <div class="media-title"><?= ucwords(strtolower($value2['name'])); ?></div>
	            <?php if($nik == $value2['created_by'] && $trx_sr['status'] != "R" && $trx_sr['status'] != "1") { ?>
                        <textarea style="display: none" class="form-control" name="oaction[old][<?= $value2['id'] ?>]"><?= $value2['action_plan'] ?></textarea>
                        <textarea type="text" class="form-control" name="oaction[new][<?= $value2['id'] ?>]" style="height: 85px;"><?= $value2['action_plan'] ?></textarea>
                    <?php } else { ?>
                      <div class="text-small text-muted" style="white-space: pre-wrap; max-height: 85px; overflow-y: auto;"><?= $value2['action_plan'] ?></div>
              <?php } ?>
	          </div>
	        </li>
	    	<?php } ?>
      	</ul>

      	<?php } else { ?>
      		<div class="hero align-items-center">
              <div class="hero-inner text-center">
                <h3><i>No Action Yet.</i></h3>
                <p class="lead">Please check again later !</p>
              </div>
            </div>
      	<?php } ?>

        <?php if($insertComment) { ?>
          <textarea class="summernote form-control" name="action[<?= $value['id'] ?>]" style="height: 85px;" placeholder="Write a comment ..."></textarea>
          <input type="hidden" name="solved" value="0">
          <input type="hidden" name="no_SR" value="<?= $trx_sr['no_SR']; ?>">
        <?php } ?>
      </div>

    <?php $i = 1; } ?>

    </div>
  </div>
</div>

<!-- Munculkan Comment Action Log bila status bukan Resolved, Jika PIC nya sesuai dengan yang login / Support pada SR tersebut -->
<?php if($insertComment) { ?>
    <div class="modal-footer">
    <button type="button" class="btn btn-primary btn-submit-cmt">Save changes</button>
    <?php if($nik == $trx_sr['pic'] && $trx_sr['svc'] != "SVC03") { ?>
        <input type="hidden" name="sr" value="<?php echo $trx_sr['no_SR']; ?>">
        <button type="button" class="btn btn-success btn-done">Solved</button>
    <?php } ?>
    </div>
<?php } ?>
</form>

<script type="text/javascript">
  $(document).ready(function() {
    $(document).on('click', '.btn-submit-cmt' ,function() {
      $('#sr_actionForm').submit();
    });

    <?php if($this->session->get("nik") == $trx_sr['pic'] && $trx_sr['svc'] != "SVC03") { ?>
    $(document).on('click', '.btn-done' ,function() {
      var r = confirm("Are you sure all the action plan is right and all the problem has been solved?");
      if (r == true) {
        $('input[name="solved"]').val("1");
        $('#sr_actionForm').submit();
      }
    });
    <?php } ?>
  });
</script>




