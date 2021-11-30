<?php if(!isset($trx_sr['no_SR'])) { ?>
<section class="section">
  <div class="card">
  <div class="card-header">
    <h4>Service Request Type</h4>
  </div>

  <!-- Service Request Open -->
  <div class="card-body">
    <div class="row">
    <div class="form-group col-12 col-md-12 col-lg-12">
      <div class="custom-switches-stacked mt-2">
        <style type="text/css">
          .custom-switch {
            padding-left: 0rem;
          }
        </style>
        <?php foreach ($ms_svc as $key => $value): ?>
        <label class="custom-switch">
          <input type="radio" name="svc" value="<?= $value['id']; ?>" class="custom-switch-input">
          <span class="custom-switch-indicator"></span>
          <span class="custom-switch-description"><?= $value['name']; ?></span>
        </label>
        <?php endforeach ?>
      </div>
    </div>
    </div>
  </div>
</section>
<?php } ?>


<section class="section">

  <!-- Service Request Open -->
  <div class="card">
    <div class="card-header">
      <h4>Basic Information</h4>
    </div>
    <div class="card-body">
      <ul class="nav nav-tabs" id="myTab2" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home" aria-selected="true"><i class="far fa-id-card"></i> User's Data</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#profile2" role="tab" aria-controls="profile" aria-selected="false"><i class="far fa-file-alt"></i> Request Info</a>
        </li>
        <?php if(isset($trx_sr["pic_name"])) { ?>
        <li class="nav-item">
          <a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact2" role="tab" aria-controls="contact" aria-selected="false"><i class="fas fa-id-card"></i> PIC's Data</a>
        </li>
        <?php } ?>
      </ul>
      <div class="tab-content tab-bordered" id="myTab3Content">

        <div class="tab-pane fade show active" id="home2" role="tabpanel" aria-labelledby="home-tab2">
          <div class="form-group row">
            <label class="col-md-3 col-lg-4 col-form-label">Name</label>
            <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["kr_name"]; ?></li></label>

            <label class="col-md-3 col-lg-4 col-form-label">Dept</label>
            <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["kr_organizational"]; ?></li></label>

            <label class="col-md-3 col-lg-4 col-form-label">Position</label>
            <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["kr_position"]; ?></li></label>

            <?php $wa = substr($trx_sr["kr_telp"], "1"); $wa = "62".$wa; ?>
            <label class="col-md-3 col-lg-4 col-form-label">Telp</label>
            <label class="col-md-9 col-lg-8 col-form-label"><li><a href="https://api.whatsapp.com/send/?phone=<?= $wa ?>" target="_blank"> <?= $trx_sr["kr_telp"]; ?></a></li></label> 

            <label class="col-md-3 col-lg-4 col-form-label">Site</label>
            <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["kr_site"]; ?></li></label>
          </div>
        </div>

        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab2">
          <div class="form-group">
              <label>Request</label>
              <p style="margin-left: 5px;"><?= $trx_sr["rsr_request"]; ?></p>

              <label>Reason</label>
              <p style="margin-left: 5px;"><?= $trx_sr["rsr_reason"]; ?></p>

              <label>Request Date</label>
              <p style="margin-left: 5px;"><?= $trx_sr["rsr_created_at"]; ?></p>

              <?php if(!isset($trx_sr['no_SR'])) { ?>
              <link rel="stylesheet" href="<?php echo site_url("public/stisla/css/chocolat.css"); ?>" type="text/css" media="screen" >
              <label>Attachment</label>
              <div class="chocolat-parent">
                  <?php if($trx_sr['attachment']) { foreach ($trx_sr['attachment'] as $key => $value) { ?>
                  <a class="chocolat-image" href="<?= site_url("public/stisla/attachment/").$value; ?>" target="_blank" title="caption image 1">
                      <img width="50" src="<?= site_url("public/stisla/attachment/").$value; ?>" />
                  </a>
                  <?php } } else echo "-"; ?>
              </div>
              <script type="text/javascript" src="<?php echo site_url("public/stisla/js/chocolat.js"); ?>"></script>
              <script type="text/javascript">
                  document.addEventListener("DOMContentLoaded", function(event) { 
                  Chocolat(document.querySelectorAll('.chocolat-parent .chocolat-image'))
              });
              </script>
              <?php } ?>
          </div>
        </div>

        <?php if(isset($trx_sr["pic_name"])) { ?>
        <div class="tab-pane fade" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
            <div class="row form-group custom-form">
              <label class="col-md-3 col-lg-4 col-form-label">Name</label>
              <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["pic_name"]; ?></li></label>

              <label class="col-md-3 col-lg-4 col-form-label">Position</label>
              <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["pic_position"]; ?></li></label>

              <?php $wa = substr($trx_sr["pic_telp"], "1"); $wa = "62".$wa; ?>
              <label class="col-md-3 col-lg-4 col-form-label">Telp</label>
              <label class="col-md-9 col-lg-8 col-form-label"><li><a href="https://api.whatsapp.com/send/?phone=<?= $wa ?>" target="_blank"><?= $trx_sr["pic_telp"]; ?></a></li></label>

              <label class="col-md-3 col-lg-4 col-form-label">Site</label>
              <label class="col-md-9 col-lg-8 col-form-label"><li><?= $trx_sr["pic_site"]; ?></li></label>
            </div>

            <?php if($trx_sr["supported_by"]) { 
                  $temp_support = json_decode($trx_sr["supported_by"]); 
            ?>
              <div class="row">
                <div class="col-sm-12 form-group">
                <label class="col-form-label">Supported By</label>
                <ul>
                  <?php foreach ($temp_support as $key => $value) { ?>
                  <li class="form-group" style="margin-bottom: unset;"><label class="col-form-label" nik=<?php echo $key ?>><?php echo $value ?></label></li>
                  <?php } ?>
                </ul>
                </div>
              </div>
            <?php }  ?>
        </div>
        <?php } ?>

      </div>
    </div>
  </div>
  

  <!-- Service Request Close -->
</section>
