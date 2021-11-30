<?php /* ?><div class="form-group">
  <label class="form-label"><?= $svc ?></label>
  <input type="hidden" name="svc" value="<?= $svc ?>">
  <div class="selectgroup selectgroup-pills">
  	<?php foreach ($ms_dsvc as $key => $value): ?>
    <label class="selectgroup-item">
      <input type="checkbox" label="<?= $value['name']; ?>" name="dsvc[]" value="<?= $value['id']; ?>" class="selectgroup-input">
      <span class="selectgroup-button"><?= $value['name']; ?></span>
    </label>
    <?php endforeach ?>
  </div>
</div>

<div class="row" id="dsvc_text">
</div>

<script type="text/javascript">
	$('#sr_form input[name="dsvc[]"]').on('change', function() {
		var id = $(this).val();
		var label = $(this).attr("label");
		var svc = "<?= $svc ?>";
		if($(this).is(':checked') && (svc == "SVC03" || svc == "SVC04")) {
			html = $('#dsvc_text').html();
			html += '<div class="form-group col-12 col-md-6 col-lg-6 '+id+'_text"><label>'+label+'</label><input type="text" class="form-control" name="'+id+'"></div>';
			$('#dsvc_text').html(html);
		} else {
			$('#sr_form #dsvc_text .'+id+'_text').remove();
		}
	});
</script>
<?php */ ?>

<div class="row">
  <input type="hidden" name="svc" value="<?= $ms_dsvc[0]['id_SVC']; ?>">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="form-group add-dlt-detail">
      <label>Service Detail&nbsp;&nbsp;<button class="btn btn-primary btn-xs add-detail" type="button"><i class="fa fa-plus"></i></button></label>
      <div class="row">
      	<div class="col-4 col-md-4 col-lg-4"><small>Service Detail Name</small></div>
      	<div class="col-2 col-md-2 col-lg-2"><small>Qty</small></div>
      	<div class="col-5 col-md-5 col-lg-5"><small>Service Description</small></div>
      </div>
                  
      <div class="row" style="margin-top: 10px;"> 	
          <div class="col-4 col-md-4 col-lg-4">
              <select class="form-control" name="id_DSVC[]">
              	<?php  foreach ($ms_dsvc as $key => $value) { ?>
              		<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
              	<?php } ?>
              </select>
          </div>

          <div class="col-2 col-md-2 col-lg-2">
          	  <input class="form-control" type="number" name="qty[]" value="0" min="0">
          </div>

          <div class="col-5 col-md-5 col-lg-5">
              <input type="text" name="desc_DSVC[]" class="form-control">
          </div>
  	  </div>

    </div>
  </div>
</div>

<script type="text/javascript">
	$('.add-dlt-detail').on('click', '.add-detail', function() {
      var temp = '<div class="row newrow" style="margin-top: 10px;">';
      temp += '<div class="col-4 col-md-4 col-lg-4"><select class="form-control" name="id_DSVC[]">';
      <?php foreach ($ms_dsvc as $key => $value) { ?>
        temp += '<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>';
      <?php } ?>
      temp += '</select></div>';
      temp += '<div class="col-2 col-md-2 col-lg-2"><input type="number" name="qty[]" class="form-control" value="0" min="0"></div>';
      temp += '<div class="col-5 col-md-5 col-lg-5"><input type="text" name="desc_DSVC[]" class="form-control"></div>';
      temp += '<div class="col-1 col-md-1 col-lg-1"><button class="btn btn-danger btn-xs delete-detail" type="button"><i class="fa fa-minus"></i></button></div>';
      temp += '</div>';
      
        $('.add-dlt-detail').append(temp);                          
    });

    $('.add-dlt-detail').on('click', '.delete-detail', function() {
      $(this).parents(".newrow").remove();
  	});
</script>

