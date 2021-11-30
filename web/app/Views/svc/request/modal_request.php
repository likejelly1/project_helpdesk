<div class="row">
	<div class="col-12 col-md-12 col-lg-4">
		<?= view('svc/modal_left'); ?>
	</div>

	<div class="col-12 col-md-12 col-lg-8">
		<section class="section">
		<form id="sr_form" method="POST" action="<?= base_url('SR/submitSR'); ?>">
			<input type="hidden" name="request_id" value="<?= $trx_sr['request_id']; ?>">
			<input type="hidden" name="nik" value="<?= $trx_sr['kr_nik']; ?>">
			<input type="hidden" name="site" value="<?= $trx_sr["kr_site"]; ?>">
			<input type="hidden" name="request_date" value="<?= $trx_sr["rsr_created_at"]; ?>">
			<input type="hidden" name="organizational_id" value="<?= $trx_sr["organizational_id"]; ?>">
			<?php echo view('svc/svc'); ?>
		</form>
		</section>
	</div>
	
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary btn-submit">Submit</button>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$(document).on('click', '.btn-submit' ,function() {
			$('#sr_form').submit();
		});
	});
</script>