<?php echo view('table_resource'); ?>

<div class="card">
  <div class="card-header">
    <h4>My Request</h4>
  </div>	

  <div class="card-body">
    <div class="row">
       <div class="form-group float-right col-12 col-md-4 col-lg-4">
          <select class="form-control filter_select" name="svc">
            <option value="-">All Service Type</option>
            <?php $ms_svc = svc(); foreach ($svc as $key => $value): ?>
              <option value="<?= $value ?>"><?= $ms_svc[$value] ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <div class="form-group float-right col-12 col-md-4 col-lg-4">
          <select class="form-control filter_select" name="priority">
            <option value="-">All Priority</option>
            <?php foreach ($ms_priority as $key => $value): ?>
              <option value="<?= $value['id'] ?>"><?= $value['description'] ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <div class="form-group float-right col-12 col-md-4 col-lg-4">
          <select class="form-control filter_select" name="status">         
            <option value="-">All Status</option>          
            <?php $status = status(); foreach ($status as $key => $value) { ?>
              <option value="<?= $key ?>"><?= $value ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

  	<div class="table-responsive">
      <table class="table table-striped" id="all_sr">
        <thead>
          <tr>
          <th>No. SR</th>
          <th>SVC</th>
          <th>Priority</th>
          <th>Expected Resolution</th>
          <th>PIC</th>
          <th>Status</th>
          <th>Score</th>
          <th>Action</th>
        </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    var tableSR = $('#all_sr').DataTable({
        "order": [[ 0, "DESC" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
        "searching": { "regex": true },
        "lengthMenu": [[5, 10, 30], ["5", "10", "30"]],
          "ajax": {
              "url": "<?= base_url("SR_user/ajaxMyRequest"); ?>",
              "type" : "POST",
          },
          "columnDefs": [

          { "name": "no_SR", "targets" : 0},
          { "name": "svc","targets": 1 },
          { "name": "priority","targets": 2 },
          { "name": "expected_resolutionDate","targets": 3 },
          { "name": "pic", "targets": 4},
          { "name": "status", "targets": 5},
          { "name": "score", "targets": 6},
          { "orderable": false, "targets": 7}

          ]
    });

    $('.card-body').on('change', '.filter_select', function() {
      var name = $(this).attr("name");
      var value = $(this).val();

      $.ajax({
        url: "<?= base_url('SR/ajaxFilterSR'); ?>",
        cache: false,
        method: "POST",
        data: {name: name, value : value},
        success: function(result){
          tableSR.ajax.reload();
        }
      });
    });
  });

  /*
  $('#all_sr').on('click', '.sr_view', function() {
    var no_sr = $(this).attr("no_sr");  

    $.ajax({
        url: "<?php //echo base_url('SR/loadSVC_view'); ?>",
        cache: false,
        method: "POST",
        data: {no_sr: no_sr},
        success: function(result) {
          $('#_Modal .modal-body').html(result)
        }
    });
  }); */

  $('#all_sr').on('click', '.sr_check', function() {
    var no_sr = $(this).attr("no_sr");  

    $.ajax({
        url: "<?php echo base_url('SR_user/loadComplete'); ?>",
        cache: false,
        method: "POST",
        data: {no_sr: no_sr},
        success: function(result) {
          $('#_normalModal .modal-body').html(result)
        }
    });
  });
</script>

