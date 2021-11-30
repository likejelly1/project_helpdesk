<?= view('svc/approve/table_detail_approve'); ?>

<?php $this->session = \Config\Services::session();
      $ss['site'] = $this->session->get('site');
      $ss['nik'] = $this->session->get('nik'); 

      $showApprove = 0;
      foreach ($trx_sr['detail_sr'] as $key => $value) {	
      	if($value['status'] == $ss['nik']) {
      		$showApprove = 1; break;
      	}
      }
?>

<?php if($showApprove == 1) { ?>
<form method="POST" id="approve_form" action="<?php echo base_url('Approve/subtmitApprove'); ?>">
<div class="card-header" >
	<h4>Approve</h4>
</div>
<ul class="list-group">
	<?php foreach ($trx_sr['detail_sr'] as $key => $value): ?>
	  <?php if($value['status'] == $ss['nik']) { 
	  		$dsr_status = $value['approve'][$ss['nik']]['status'];
	  		$dsr_ket = $value['approve'][$ss['nik']]['ket'];
	  ?>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	  	<div>
		    <b><i><?= $value['name_DSVC']; ?></i></b>
			<br/><?= $value['desc_DSVC']; ?>
			<br/><b><i><?= "Qty : ".$value['qty']; ?></i></b>
			<br/><br/><textarea name="ket[<?= $value['id']; ?>]" class="form-control" placeholder="Reason of Reject or On Hold" style="width: 20rem; height: 45px;" required=""><?= $dsr_ket; ?></textarea>
		</div>

		<input type="hidden" name="no_SR" value="<?= $trx_sr['no_SR'] ?>">

	  	
		<div>
		    <div class="form-group">
                <label class="selectgroup-item" style="width: 7rem">
                  <input type="radio" name="dsr[<?= $value['id']; ?>]" value="1" class="selectgroup-input" <?php if($dsr_status == "1") echo "checked"; ?>>
                  <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-thumbs-up"></i>&nbsp; Approve</span>
                </label><br/>
                <label class="selectgroup-item" style="width: 7rem">
                  <input type="radio" name="dsr[<?= $value['id']; ?>]" value="-" class="selectgroup-input" <?php if($dsr_status == "-") echo "checked"; ?>>
                  <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-thumbs-down"></i>&nbsp; Reject</span>
                </label><br/>
                <label class="selectgroup-item" style="width: 7rem">
                  <input type="radio" name="dsr[<?= $value['id']; ?>]" value="." class="selectgroup-input"  <?php if($dsr_status == "." || $dsr_status == "0") echo "checked"; ?>>
                  <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-hand-paper"></i></i>&nbsp; On Hold</span>
                </label>
           	</div>
	    </div>
	  </li>
	<?php } endforeach ?>
</ul>
<div class="card-footer text-right">
	<button class="btn btn-primary mr-1 btn-app-sbt" type="submit">Submit</button>
</div>
</form>
<?php } ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#approve_form').on('click', '.selectgroup-input', function() {
			var state = $(this).val();
			var textarea = $(this).parents("li").find("textarea");
			textarea.val("");

			if(state == 1) {
				textarea.fadeOut("fast");
				textarea.prop('required',false);	
			} else {
				textarea.fadeIn("fast");
				textarea.prop('required',true);
			}
		});
	});
</script>
