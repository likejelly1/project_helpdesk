<?php echo view('table_resource'); ?>

<div class="card">
  <div class="card-header">
    <h4>All Service Request</h4>
  </div>	

  <div class="card-body">
    <div class="row">
       <div class="form-group float-right col-12 col-md-6 col-lg-3">
          <select class="form-control filter_select" name="svc">
            <option value="-">All Service Type</option>
            <?php foreach ($ms_svc as $key => $value): ?>
              <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <div class="form-group float-right col-12 col-md-6 col-lg-3">
          <select class="form-control filter_select" name="site">
            <option value="-">All Site</option>
            <?php $site = site(); foreach ($site as $key => $value): ?>
              <option value="<?= $key ?>"><?= $value ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <div class="form-group float-right col-12 col-md-6 col-lg-3">
          <select class="form-control filter_select" name="priority">
            <option value="-">All Priority</option>
            <?php $site = site(); foreach ($ms_priority as $key => $value): ?>
              <option value="<?= $value['id'] ?>"><?= $value['description'] ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <div class="form-group float-right col-12 col-md-6 col-lg-3">
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
          <th>RQST</th>
          <th>SVC</th>
          <th>Site</th>
          <th>Priority</th>
          <th>Expected Resolution</th>
          <th>PIC</th>
          <th>Status</th>
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
        "lengthMenu": [[5, 10, 30, 50, 100], ["5", "10", "30", "50", "100"]],
          "ajax": {
              "url": "<?= base_url("SR/ajaxSRAll"); ?>",
              "type" : "POST",
          },
          "columnDefs": [

          { "name": "no_SR", "targets" : 0, "className" : "tooltip_sr"},
          { "name": "requester_name", "targets" : 1, "className" : "tooltip_requester"},
          { "name": "svc","targets": 2 },
          { "name": "site","targets": 3 },
          { "name": "priority","targets": 4 },
          { "name": "expected_resolutionDate","targets": 5 },
          { "name": "pic", "targets": 6, "className" : "tooltip_pic"},
          { "name": "status", "targets": 7},
          { "orderable": false, "targets": 8}

          ],
          "fnDrawCallback": function (oSettings) {
                $('#all_sr tbody .tooltip_sr').each(function () {
                    var sTitle;
                    var tr_custom = $(this).parent();
                    var nTds = $('td', tr_custom);
                    var s0 = $(nTds[0]).children().attr("text_tool");

                    sTitle = s0;

                    this.setAttribute('rel', 'tooltip');
                    this.setAttribute('title', sTitle);
                    $(this).tooltip({
                        html: true
                    });
                });

                $('#all_sr tbody .tooltip_requester').each(function () {
                    var sTitle;
                    var tr_custom = $(this).parent();
                    var nTds = $('td', tr_custom);
                    var s1 = $(nTds[1]).children().attr("text_tool");

                    sTitle = s1;

                    this.setAttribute('rel', 'tooltip');
                    this.setAttribute('title', sTitle);
                    $(this).tooltip({
                        html: true
                    });
                });

                $('#all_sr tbody .tooltip_pic').each(function () {
                    var sTitle;
                    var tr_custom = $(this).parent();
                    var nTds = $('td', tr_custom);
                    var s6 = $(nTds[6]).children().attr("text_tool");

                    sTitle = s6;

                    this.setAttribute('rel', 'tooltip');
                    this.setAttribute('title', sTitle);
                    $(this).tooltip({
                        html: true
                    });
                });
            }
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

  /*$('#all_sr').on('click', '.sr_view', function() {
    var no_sr = $(this).attr("no_sr");  

    $.ajax({
        url: "<?php // echo base_url('SR/loadSVC_view'); ?>",
        cache: false,
        method: "POST",
        data: {no_sr: no_sr},
        success: function(result) {
          $('#_Modal .modal-body').html(result)
        }
    });
  }); */

  $('#all_sr').on('click', '.sr_process', function() {
    var no_sr = $(this).attr("no_sr");  

    $.ajax({
        url: "<?php echo base_url('SR/loadSVC_edit'); ?>",
        cache: false,
        method: "POST",
        data: {no_sr: no_sr},
        success: function(result) {
          $('#_Modal .modal-body').html(result)
        }
    });
  });

  $('#all_sr').on('click', '.sr_action', function() {
    var no_sr = $(this).attr("no_sr");  

    $.ajax({
        url: "<?php echo base_url('SR/loadSVC_action'); ?>",
        cache: false,
        method: "POST",
        data: {no_sr: no_sr},
        success: function(result) {
          $('#_Modal .modal-body').html(result)
        }
    });
  });
</script>

