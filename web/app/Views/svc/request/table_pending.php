<div class="card">
  <div class="card-header">
    <h4>Pending Service Request</h4>
  </div>	

  <div class="card-body p-0">
  	<div class="table-responsive">
      <table class="table table-striped" id="pending_table">
        <tr>
          <th>No</th>
          <th>Request</th>
          <th>Reason</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php foreach ($request_sr as $key => $value) { 
       	  	$created_at = strtotime($value['created_at']);
			$created_at = date('d-m-Y', $created_at);
       	  ?>
        <tr>
       	  	<td><?= $key + 1; ?></td>
            <td><?= $value['request']; ?></td>
            <td><?= $value['reason']; ?></td>
            <td><div class="badge"><?= $created_at ?></div></td>
            <td><?php if($value['status'] == '0') { ?>
                  <div class="badge badge-secondary"><?php echo "To be Reviewed" ?></div>
                <?php } elseif ($value['status'] == 'x' ) { ?>
                  <div class="badge badge-danger"><?php echo "Rejected" ?></div>
                <?php } ?>
            </td>
            <td><?php if($value['status'] == '0') { ?>
                  <a href="#" class="btn btn-danger cancel_btn" request_id="<?= $value['id']; ?>">Cancel</a>
                <?php } elseif ($value['status'] == 'x' ) { ?>

                <?php } ?>
            </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#pending_table').on('click', '.cancel_btn', function() {
    var id = $(this).attr("request_id");
    var tr = $(this).parents("tr");
    var r = confirm("Are you sure want to delete this request?");
    if(r == true) 
    {
	    $.ajax({
	        url: "<?= base_url('SR/pending_request_cancel'); ?>",
	        cache: false,
	        method: "POST",
	        data: {id: id},
	        success: function(result){
	            tr.remove();
	        }
	    });
    }
  });
</script>