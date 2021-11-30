<?php /* echo "<pre>";
print_r($trx_sr['detail_sr']);
echo "</pre>";
 */ ?>

<form id="ro_form" method="POST" action="<?= base_url('RO/newRO'); ?>">
<div class="row">
  	<div class="col-12 col-md-12 col-lg-12">
      <input type="hidden" name="no_SR" value="<?php echo $trx_sr['no_SR']; ?>">
    	<table class="table table table-striped">
    	<thead class="thead-light">
        <tr>
          <th></th>
          <th scope="col">Detail Servis</th>
          <th scope="col" style="text-align: center;">Qty</th>
          <th scope="col" style="text-align: center;">No. RO</th>
          <?php $create_ro = 0; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trx_sr['detail_sr'] as $key => $value) { ?>
        <tr>
          <td><?php if($value['status'] == 1 && !$value['no_RO']) { 
                    $create_ro = 1; ?>
                <input type="checkbox" name="id_detail_sr[]" value="<?= $value['id']; ?>">
              <?php } ?></td>
          <td>
              <b><i><?= $value['name_DSVC'] ?></i></b>
              <?php if(trim($value['desc_DSVC'])) echo "<br/>".$value['desc_DSVC']; ?>
          </td>

          <td style="text-align: center;"><?php if($value['qty'] == "0") echo "-"; else echo $value['qty']; ?></td>
          <td style="text-align: center;"><?php if(!$value['no_RO']) echo "-"; 
                                                else { 
                                                  echo $value['no_RO'].'&nbsp;&nbsp;<a href="#" class="btn btn-warning ro-print" no_RO="'.$value['no_RO'].'"><i class="fas fa-print"></i></a>'; 
                                                } 
                                          ?></td>
        </tr>
        <?php } ?>
      </tbody>
    	</table>
  	</div>
</div>

<ul class="list-group">
  <?php foreach ($trx_sr['detail_sr'] as $key => $value): ?>
    <li class="list-group-item justify-content-between align-items-center">
      <div class="row">
        <div class="col-sm-3">
          <b><i><?= $value['name_DSVC']; ?></i></b>
          <br/><?= $value['desc_DSVC']; ?>
          <br/><b><i><?= "Qty : ".$value['qty']; ?></i></b>
        </div>

        <div class="col-sm-9">
          <textarea name="ket[<?= $value['id']; ?>]" class="form-control" placeholder="Description" style="width: 100%; height: 100%;"><?= $value['ket_RO']; ?></textarea>
        </div>
      </div>
    </li>
  <?php endforeach ?>
</ul>


<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Make New RO / Save Changes</button>
</div>

</form>

<script type="text/javascript">
  $('#ro_form').on('click', '.ro-print', function() {
      no_RO = $(this).attr("no_RO");
      $.ajax({
         type: "POST",
         url: "<?php  echo base_url() ?>/RO/printRO",
         data: { no_RO : no_RO},
         success: function(result) {
          var popupWin = window.open('', '_blank', '');
          popupWin.document.open();
          popupWin.document.write('<html><body onload="window.print()">'+result+'</html>');
          popupWin.document.close();
         }
      });
   });
</script>
