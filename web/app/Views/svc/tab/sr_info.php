<div class="form-group row">
  	<label class="col-12 col-md-3 col-lg-3 col-form-label">No. SR</label>
    <label class="col-12 col-md-9 col-lg-9 col-form-label"><li><?= $trx_sr["no_SR"]; ?></li></label>

    <label class="col-12 col-md-3 col-lg-3 col-form-label">Type Service</label>
    <label class="col-12 col-md-9 col-lg-9 col-form-label"><li><?= $trx_sr["svc_name"]; ?></li></label>

    <label class="col-12 col-md-3 col-lg-3 col-form-label">Request</label>
    <label class="col-12 col-md-9 col-lg-9 col-form-label"><li><?= $trx_sr["request"]; ?></li></label>

    <label class="col-12 col-md-3 col-lg-3 col-form-label">Reason</label>
    <label class="col-12 col-md-9 col-lg-9 col-form-label"><li><?= $trx_sr["reason"]; ?></li></label>

    <label class="col-12 col-md-3 col-lg-3 col-form-label">Service Date</label>
    <label class="col-12 col-md-9 col-lg-9 col-form-label"><li><?= $trx_sr["created_at"]; ?></li></label>
</div>

<div class="row">
    <div class="col-sm-12 form-group">
	    <label class="col-form-label">Detail Service</label>
	    <ul>
	      <?php foreach ($trx_sr['detail_sr'] as $key => $value) { ?>
	      <li class="form-group" style="margin-bottom: unset;"><label class="col-form-label"><?php echo $value['name_DSVC']; ?> <?php if($value['desc_DSVC']) echo ("(".$value['desc_DSVC'].")") ?></label></li>
	      <?php } ?>
	    </ul>
    </div>
</div>

<link rel="stylesheet" href="<?= site_url("public/stisla/css/chocolat.css"); ?>" type="text/css" media="screen" >
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12 form-group">
        <form method="POST" action="<?= base_url('SR/addAttach'); ?>" novalidate="" enctype="multipart/form-data">
            <?php $nik = $this->session->get('nik'); ?>
            
            <label>Attachment</label>&nbsp;&nbsp;
            <?php if($trx_sr['nik'] == $nik || $trx_sr['pic'] == $nik) { ?>
            <button type="button" class="btn btn-info add-attach" style="font-size: 10px; padding: 0px 15px;"><i class="fa fa-plus"></i></button>&nbsp;&nbsp;
            <label class="lbl-attach" style="display: none;"><span class="qty-attach">3</span> file(s) selected.</label>
            <input type="hidden" name="request_id" value="<?php echo $trx_sr['request_id']; ?>">
            <input type="hidden" name="no_SR" value="<?php echo $trx_sr['no_SR']; ?>">
            <input type="file" name="attachment[]" multiple="" style="display:none">&nbsp;&nbsp;
            <button type="submit" class="btn btn-primary submit-attach lbl-attach" style="font-size: 10px; padding: 0px 15px; display: none;">Submit</button>
            <?php } ?>
        </form>

        <div class="chocolat-parent">
            <?php if($trx_sr['attachment']) { foreach ($trx_sr['attachment'] as $key => $value) { ?>
            <a class="chocolat-image" href="<?= site_url("public/stisla/attachment/").$value; ?>" target="_blank" title="caption image 1">
                <img width="100" src="<?= site_url("public/stisla/attachment/").$value; ?>" />
            </a>
            <?php } } else echo "-"; ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= site_url("public/stisla/js/chocolat.js"); ?>"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) { 
    Chocolat(document.querySelectorAll('.chocolat-parent .chocolat-image'))
});

<?php if($trx_sr['nik'] == $nik || $trx_sr['pic'] == $nik) { ?>
$(document).ready(function() {
    $('.add-attach').on('click', function() {
        $('input[name="attachment[]"]').trigger('click');
    });

    $('input[name="attachment[]"]').change(function(){
        var files = $(this)[0].files;
        if(files.length != 0) { 
            $('.qty-attach').html(files.length);
            $('.lbl-attach').fadeIn();
        } else {
            $('.lbl-attach').fadeOut();
        }
    });
});
<?php } ?>
</script>
