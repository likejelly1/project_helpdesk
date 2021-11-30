<div class="row">
  	<div class="col-12 col-md-12 col-lg-12">
  	<table class="table table table-striped">
  	<thead class="thead-light">
      <tr>
        <th scope="col">Detail Servis</th>
        <th scope="col" style="text-align: center;">Qty</th>
        <?php foreach ($approve_sr as $key => $value) { ?>
            <th scope="col" style="text-align: center;"><?= $value['name']; ?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($trx_sr['detail_sr'] as $key => $value) { ?>
      <tr>  
        <td>
            <b><i><?= $value['name_DSVC'] ?></i></b>
            <?php if(trim($value['desc_DSVC'])) echo "<br/>".$value['desc_DSVC']; ?>
        </td>

        <td style="text-align: center;"><?php echo $value['qty']; ?></td>

        <?php foreach ($approve_sr as $key2 => $value2) { ?>
            <td style="text-align: center;" data-toggle="tooltip" data-placement="top" title="<?= $value['approve'][$value2['nik']]['ket'] ?>">
                <?php if($value['approve'][$value2['nik']]) { $icon = ""; ?>
                  <?php $status =  $value['approve'][$value2['nik']]['status'];
                        if($status == 1)
                          $icon = "check";
                        elseif ($status == "-")
                          $icon = "times";
                        elseif ($status == ".")
                          $icon = "hand-paper";
                ?>
                        <i class="fas fa-<?= $icon ?>"></i>
                <?php } ?>
            </td>
        <?php } ?>
      </tr>
      <?php } ?>
    </tbody>
  	</table>
  	</div>
</div>
