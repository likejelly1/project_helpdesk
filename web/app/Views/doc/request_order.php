<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Helpdesk</title>
  <link rel="shortcut icon" type="image/jpg" href="<?= site_url("public/stisla/img/MHA_fav.ico") ?>"/>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- Template CSS -->
  <link rel="stylesheet" href="<?= site_url("public/stisla/css/style.css"); ?>">
  <link rel="stylesheet" href="<?= site_url("public/stisla/css/components.css"); ?>">

  <!-- Signature -->
  <link href="<?= site_url("public/stisla/css/jquery.signaturepad.css"); ?>" rel="stylesheet" />

  <!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"> -->

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

  <!-- Select2 -->
  <link rel="stylesheet" href='<?= site_url("public/stisla/css/select2.min.css"); ?>' />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<style type="text/css">
  @media print{@page {size: landscape}}
  @media print {
}
  table td {
    padding: 3.5px;
    font-size: 13px;
  }

  .bitsmaller td {
    font-size: 12px;
    padding: 7px;
  }

  body {
    margin: 0 0 0 0;
  }

  .smallsign {
  transform: scale(0.5, 0.5);
  -ms-transform: scale(0.5, 0.5); /* IE 9 */
  -webkit-transform: scale(0.5, 0.5); /* Safari and Chrome */
  -o-transform: scale(0.5, 0.5); /* Opera */
  -moz-transform: scale(0.5, 0.5); /* Firefox */
  }
</style>

<body>
  <table border="1" style="width: 100%">
    <tr>
      <td rowspan="4" style="width: 10%"><img alt="image" <img src='<?= site_url("public/stisla/img/MHA.png") ?>' style="width: 100%; padding: 10px;"></td>
      <td class="text-center" rowspan="2" style="width: 50%; font-weight: bold; color: blue">PT. MANDIRI HERINDO ADIPERKASA</td>
      <td style="width: 15%">No. Formulir</td>
      <td style="width: 25%">Form 01/Pros-MHA-SMI-LOG-01</td>
    </tr>

    <tr>
      <td>Revisi</td>
      <td>00</td>
    </tr>

    <tr>
      <td class="text-center" rowspan="2" style="font-weight: bold;">REQUEST ORDER FORM</td>
      <td>Berlaku</td>
      <td>01 Januari 2016</td>
    </tr>

    <tr>
      <td>Halaman</td>
      <td>1 dari 1</td>
    </tr>
  </table>
  <br>
  <table border="1" style="width: 100%">
    <tr>
      <td style="width: 30%; font-weight: bold">No. RO</td>
      <td style="width: 70%"><?php echo $detail_ro[0]['no_RO']; ?></td>
    </tr>

    <tr>
      <td style="font-weight: bold">Tanggal RO</td>
      <td><?php $date = str_replace('-', '/', $detail_ro[0]['ro_created']);
        echo date('d/m/Y', strtotime($date)); ?></td>
    </tr>

    <tr>
      <td style="font-weight: bold">Bagian / Divisi</td>
      <td>IT / <i><?php echo $detail_sr['no_SR']; ?></i></td>
    </tr>

    <tr>
      <td style="font-weight: bold">Type RO</td>
      <td>Normal &nbsp;<input type="checkbox" checked=""> &nbsp;&nbsp;&nbsp;Urgent &nbsp;<input type="checkbox"></td>
    </tr>
  </table>
  <br>
  <table class="bitsmaller" border="1" style="width: 100%">
    <tr>
      <td class="text-center" rowspan="2" style="width: 5%; font-weight: bold">No.</td>
      <td class="text-center" rowspan="2" style="width: 10%; font-weight: bold">Code No.</td>
      <td class="text-center" rowspan="2" style="width: 20%; font-weight: bold">Description</td>
      <td class="text-center" colspan="2" style="width: 14%; font-weight: bold">Specification</td>
      <td class="text-center" rowspan="2" style="width: 7%; font-weight: bold">Jumlah</td>
      <td class="text-center" rowspan="2" style="width: 7%; font-weight: bold">Satuan</td>
      <td class="text-center" rowspan="2" style="width: 7%; font-weight: bold">Stock</td>
      <td class="text-center" rowspan="2" style="width: 30%; font-weight: bold">Keterangan</td>
    </tr>

    <tr>
      <td class="text-center" style="width: 7%; font-weight: bold">Barang</td>
      <td class="text-center" style="width: 7%; font-weight: bold">Jasa</td>
    </tr>

    <?php foreach ($detail_ro as $key => $value) { ?>
    <tr>
      <td class="text-center"><?php echo $key+1; ?></td>
      <td></td>
      <td><?php echo $value['desc_DSVC']; ?></td>
      <td class="text-center"><?php if($value['id_DSVC'] != "0313") echo "<input type='checkbox' checked>"; ?></td>
      <td class="text-center"><?php if($value['id_DSVC'] == "0313") echo "<input type='checkbox' checked>"; ?></td>
      <td class="text-center"><?php if($value['qty'] == 0) echo "-"; else echo $value['qty']; ?></td>
      <td class="text-center">PCS</td>
      <td class="text-center"></td>
      <td><?php echo $value['ket_RO']; ?></td>
    </tr> 
    <?php } ?>
  </table>
  <br>
  <table border="1" style="width: 100%;">
    <tr>
      <td class="text-center" style="width: 33.33%; border-bottom: 0">Disiapkan oleh,</td>
      <td class="text-center" style="width: 33.34%">Diperiksa oleh,</td>
      <td class="text-center" style="width: 33.33%">Diketahui oleh,</td>
    </tr>

    <tr style="height: 125px;">
      <td class="prepared_sig text-center" style="width: 33.33%;"><canvas class="pad" width="200" height="125"></canvas></div></td>
      <td class="checked_sig text-center" style="width: 33.33%;"><canvas class="pad" width="200" height="125"></canvas></td>
      <td style="width: 33.33%;"></td>
    </tr>

    <tr>
      <td>Nama : <?php echo $prepared_by['name']; ?></td>
      <td>Nama : <?php echo $checked_by['name']; ?></td>
      <td>Nama : Bpk. Alexander</td>
    </tr>

    <tr>
      <td>Tanggal : <?php echo date('d/m/Y', strtotime($date)); ?></td>
      <td>Tanggal : <?php echo date('d/m/Y', strtotime($date)); ?></td>
      <td>Tanggal : <?php echo date('d/m/Y', strtotime($date)); ?></td>
    </tr>
  </table>
</body>


<footer>
  <!-- General JS Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src='<?= site_url("public/stisla/js/stisla.js"); ?>'></script>

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src='<?= site_url("public/stisla/js/scripts.js"); ?>'></script>
  <script src='<?= site_url("public/stisla/js/custom.js"); ?>'></script>
  <!-- Page Specific JS File -->

  <!-- Signature -->
  <script src="<?= site_url("public/stisla/js/numeric-1.2.6.min.js"); ?>"></script>
  <script src="<?= site_url("public/stisla/js/bezier.js"); ?>"></script>
  <script src="<?= site_url("public/stisla/js/jquery.signaturepad.js"); ?>"></script>

  <script type="text/javascript">
    $('.prepared_sig').signaturePad({
      drawOnly:true, 
      drawBezierCurves:true, 
      variableStrokeWidth:true, 
      lineTop:false
    });

    $('.checked_sig').signaturePad({
      drawOnly:true, 
      drawBezierCurves:true, 
      variableStrokeWidth:true, 
      lineTop:false
    });

    <?php if($prepared_by['signature']) { ?>
      $('.prepared_sig').signaturePad({
        displayOnly:true
      }).regenerate(<?php echo $prepared_by['signature']; ?>);
    <?php } ?>

     <?php if($checked_by['signature']) { ?>
      $('.checked_sig').signaturePad({
        displayOnly:true
      }).regenerate(<?php echo $checked_by['signature']; ?>);
    <?php } ?>
  </script>
</footer>