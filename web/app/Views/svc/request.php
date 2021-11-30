<div class="card">
  <div class="card-header">
    <h4>Form Service Request</h4>
  </div>	

  <div class="card-body">
	  <div class="ticket-form">
		<form method="POST" action="<?= base_url('SR/request'); ?>" class="needs-validation" novalidate="">
		  <div class="form-group">
		    <textarea class="summernote form-control" name="add_desc" placeholder="Type your request here" style="height: 210px;" required=""></textarea>
		  </div>
		  <div class="form-group text-right">
		    <button class="btn btn-primary btn-lg">
		      Submit
		    </button>
		  </div>
		</form>
	   </div>
   </div>
</div>