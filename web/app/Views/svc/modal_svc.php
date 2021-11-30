<div class="row">
	<div class="col-12 col-md-12 col-lg-4">
		<?= view('svc/modal_left'); ?>
	</div>

	<div class="col-12 col-md-12 col-lg-8">
		<section class="section">
		<form id="sr_form" method="POST" action="<?= base_url('SR/editSR'); ?>">
			<input type="hidden" name="no_SR" value="<?= $trx_sr['no_SR'] ?>">
			<input type="hidden" name="request_date" value="<?= $trx_sr["rsr_created_at"]; ?>">
			<div class="card">
	 			<div class="card-header">
	    			<h4><?= $trx_sr['svc_name'] ?></h4>
	  			</div>

	  			<!-- Service Request Open -->
		  		<div class="card-body">
		    		<div class="row">
				      <div class="form-group col-12 col-md-6 col-lg-6">
				        <label>Request</label>
				        <input type="hidden" name="request[old]" value="<?= $trx_sr['request']; ?>">
				        <textarea class="summernote form-control" name="request[new]" style="height: 170px;"><?= $trx_sr['request']; ?></textarea>
				      </div>

				      <div class="form-group col-12 col-md-6 col-lg-6">
				        <label>Reason</label>
				        <input type="hidden" name="reason[old]" value="<?= $trx_sr['reason']; ?>">
				        <textarea class="summernote form-control" name="reason[new]" style="height: 170px;"><?= $trx_sr['reason'] ?></textarea>
				      </div>
		    		</div>

		    		<div class="row">
				      <div class="col-12 col-md-12 col-lg-12">
	                    <div class="form-group add-dlt-detail">
	                      <label>Service Detail&nbsp;&nbsp;<button class="btn btn-primary btn-xs add-detail" type="button"><i class="fa fa-plus"></i></button></label>
	                      <div class="row">
	                      	<div class="col-4 col-md-4 col-lg-4"><small>Service Detail Name</small></div>
	                      	<div class="col-2 col-md-2 col-lg-2"><small>Qty</small></div>
	                      	<div class="col-5 col-md-5 col-lg-5"><small>Service Description</small></div>
	                      </div>
	                      <?php foreach ($trx_sr['detail_sr'] as $key => $d_value): ?>                      
	                      <div class="row" style="margin-top: 10px;"> 	
		                      <div class="col-4 col-md-4 col-lg-4">
		                      	  <input type="hidden" name="id_DSVC[<?= $d_value['id'] ?>][old]" value="<?= $d_value['id_DSVC'] ?>">
			                      <select class="form-control" name="id_DSVC[<?= $d_value['id'] ?>][new]">
			                      	<?php foreach ($trx_sr['dsvc'] as $key => $value) { ?>
			                      		<option value="<?= $value['id'] ?>"<?php if($d_value['id_DSVC'] == $value['id']) echo "selected"; ?>><?= $value['name'] ?></option>
			                      	<?php } ?>
			                      </select>
		                      </div>

		                      <div class="col-2 col-md-2 col-lg-2">
		                      	  <input type="hidden" name="qty[<?= $d_value['id'] ?>][old]" value="<?= $d_value['qty'] ?>">
		                      	  <input class="form-control" type="number" name="qty[<?= $d_value['id'] ?>][new]" value="<?= $d_value['qty'] ?>" min="0">
		                      </div>

		                      <div class="col-5 col-md-5 col-lg-5">
								  <input type="hidden" name="desc_DSVC[<?= $d_value['id'] ?>][old]" class="form-control" value="<?= $d_value['desc_DSVC'] ?>">
			                      <input type="text" name="desc_DSVC[<?= $d_value['id'] ?>][new]" class="form-control" value="<?= $d_value['desc_DSVC'] ?>">
		                      </div>
	                  	  </div>
	                  	  <?php endforeach ?>

	                    </div>
				      </div>
		    		</div>

		    		<div class="form-group">	
		    			<label>Priority</label>
		    			<?php echo view('svc/table_priority'); ?>
		    		</div>

		    		<div class="row">
		    		<?php $lmh = array("L" => "Low", "M" => "Medium", "H" => "High"); ?>
				      <div class="col-6 col-md-6 col-lg-6">
				      	<input type="hidden" name="urgency[old]" value="<?= $trx_sr['urgency']; ?>">
				        <div class="form-group">
				          <label>Urgency</label>
				          <select class="form-control select2" name="urgency[new]">
				            <?php foreach ($lmh as $key => $value): ?>
				            	<option value="<?= $key ?>" <?php if($trx_sr['urgency'] == $key) echo "selected" ?>><?= $value ?></option>
				            <?php endforeach ?>
				          </select>
				        </div>
				      </div>

				      <div class="col-6 col-md-6 col-lg-6">
				      	<input type="hidden" name="impact[old]" value="<?= $trx_sr['impact']; ?>">
				        <div class="form-group">
				          <label>Impact</label>
				          <select class="form-control select2" name="impact[new]">
				            <?php foreach ($lmh as $key => $value): ?>
				            	<option value="<?= $key ?>" <?php if($trx_sr['impact'] == $key) echo "selected" ?>><?= $value ?></option>
				            <?php endforeach ?>
				          </select>
				        </div>
				      </div>
				    </div>

				    <div class="row">
				    	<div class="col-12 col-md-12 col-lg-12">
					    	<div class="form-group">
		                      <label>Supported By</label>
		                      <input type="hidden" name="supported_by[old]" value='<?= $trx_sr['supported_by']; ?>'>
		                      <select class="form-control select2 supported_by" multiple="multiple" name="supported_by_new[]">
		                      	<?php if($trx_sr['supported_by']) { 
		                      	$supported_by = json_decode($trx_sr['supported_by']); 
		                      	foreach ($supported_by as $key => $value) { ?>
		                      		<option value="<?= $key ?>-<?= $value ?>" selected=""><?= $value ?></option>
		                      	<?php } } ?>	                      	
		                      </select>
		                    </div>
				    	</div>
				    </div>

				    <?php /* ?>
				    <div class="row">
				  	  <div class="form-group col-12">
						  <label>Attachment - <i>Image Only</i> (Optional)</label>
						  <input class="form-control" type="file" name="attachment[]" multiple="">
					  </div>
				  	</div>
				  	<?php */ ?>
				    
		    	</div>
		    </div>
		</form>
		</section>
	</div>
	
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary btn-submit">Save changes</button>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		$(document).on('click', '.btn-submit' ,function() {
			$('#sr_form').submit();
		});

		$('.add-dlt-detail').on('click', '.add-detail', function() {
			var temp = '<div class="row newrow" style="margin-top: 10px;">';
			temp += '<div class="col-4 col-md-4 col-lg-4"><select class="form-control" name="newdsvc[]">';
			<?php foreach ($trx_sr['dsvc'] as $key => $value) { ?>
				temp += '<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>';
			<?php } ?>
			temp += '</select></div>';
			temp += '<div class="col-2 col-md-2 col-lg-2"><input type="number" name="newqty_dsvc[]" class="form-control" value="0" min="0"></div>';
			temp += '<div class="col-5 col-md-5 col-lg-5"><input type="text" name="newdesc_dsvc[]" class="form-control"></div>';
			temp += '<div class="col-1 col-md-1 col-lg-1"><button class="btn btn-danger btn-xs delete-detail" type="button"><i class="fa fa-minus"></i></button></div>';
			temp += '</div>';
	    
	    	$('.add-dlt-detail').append(temp);		                      
		});

		$('.add-dlt-detail').on('click', '.delete-detail', function() {
			$(this).parents(".newrow").remove();
		});

	    $('.supported_by').select2({
           minimumInputLength: 2,
           allowClear: true,
           placeholder: '   Supported By',
           ajax: {
              dataType: 'json',
              url: "<?php echo base_url('Karyawan/getKaryawan'); ?>",
              delay: 800,
              data: function(params) {
                return {
                  q: params.term, // search term
                }
              },
              processResults: function (data, page) {
              return {
                results: data
              };
            },
          }
	     });

	});
</script>