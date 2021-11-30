<link href="<?= site_url("public/stisla/css/jquery.signaturepad.css"); ?>" rel="stylesheet" />
<div class="sigPad" id="smoothed-variableStrokeWidth" style="width:204px;">
  <ul class="sigNav" style="display: block;">
  <li class="drawIt"><a href="#draw-it" class="current">Sign Here</a></li>
  <li class="clearButton" style="display: list-item;"><a href="#clear">Clear</a></li>
  </ul>
  <div class="sig sigWrapper current" style="height: auto; display: block;">
  <div class="typed" style="display: none;"></div>
    <canvas class="pad" width="200" height="125"></canvas>
    <input type="hidden" name="signature" class="output" value="">
  </div>
</div>

<script src="<?= site_url("public/stisla/js/numeric-1.2.6.min.js"); ?>"></script>
<script src="<?= site_url("public/stisla/js/bezier.js"); ?>"></script>
<script src="<?= site_url("public/stisla/js/jquery.signaturepad.js"); ?>"></script>
<script type="text/javascript">
 $(document).ready(function() {
  $('.sigPad').signaturePad({
      drawOnly:true, 
      drawBezierCurves:true, 
      variableStrokeWidth:true, 
      lineTop:false
    });

    <?php if($karyawan['signature']) { ?>
      $('.sigPad').signaturePad({
        displayOnly:true
      }).regenerate(<?php echo $karyawan['signature']; ?>);
    <?php } ?>
  });
</script>
<script src="<?= site_url("public/stisla/js/json2.js"); ?>"></script>