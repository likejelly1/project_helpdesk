<div class="card">
  <div class="card-header">
    <h4>User's Request</h4>
  </div>	

  <div class="card-body">
  	<div class="table-responsive">
      <table class="table table-striped" id="process_table">
        <thead>
          <th>No</th>
          <th>Requester</th>
          <th>Site</th>
          <th>Request</th>
          <th>Reason</th>
          <th>Date</th>
          <th>Action</th>
        </thead>
        <?php foreach ($request_sr as $key => $value) { 
       	  	$created_at = strtotime($value['created_at']);
			      $created_at = date('d-m-Y', $created_at);
       	  ?>
        <tr>
       	  	<td><?= $key + 1; ?></td>
            <td><?= $value['name']; ?></td>
            <td><?= $value['site']; ?></td>
            <td><?= $value['request']; ?></td>
            <td><?= $value['reason'] ?></td>
            <td><div class="badge"><?= $created_at ?></div></td>
            <td>
              <div style="display: flex;">
                <?php if(in_array($value['site'], $handling_site)) { ?>
                  <a href="#" class="btn btn-info create_btn" request_id="<?= $value['id']; ?>" data-toggle="modal" data-target="#_Modal"><i class="fa fa-plus"></i></a>&nbsp;
                  <a href="#" class="btn btn-danger reject_btn" request_id="<?= $value['id']; ?>"><i class="fa fa-times"></i></a>
                <?php } ?>
              </div>
            </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<?php echo view('table_resource'); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $('#process_table').DataTable({
      "lengthMenu": [[5, 10, 30], ["5", "10", "30"]]
    });

    $('#process_table').on('click', '.create_btn', function() {
      var id = $(this).attr("request_id");  

      $.ajax({
          url: "<?php echo base_url('SR/loadSVC'); ?>",
          cache: false,
          method: "POST",
          data: {id: id},
          success: function(result) {
            $('#_Modal .modal-body').html(result)
          }
      });
    });

    $('#process_table').on('click', '.reject_btn', function() {
      var id = $(this).attr("request_id");  
      var r = confirm("Are you sure want to reject this request?");

      if (r == true) {
        $.ajax({
          url: "<?php echo base_url('SR/rejectSVC'); ?>",
          cache: false,
          method: "POST",
          data: {id: id},
          success: function(result) {
            location.reload();
          }
        });
      }
    });

  });
</script>