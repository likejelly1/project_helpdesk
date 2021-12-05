<link rel="stylesheet" src="<?= site_url("public/stisla/css/dropzone.min.css"); ?>" />

<div class="card">
  <div class="card-header">
    <h4>Form Service Request</h4>
  </div>	

	
  <div class="card-body">
	  <div class="ticket-form">
		<form method="POST" action="<?= base_url('SR/request_sap'); ?>" class="needs-validation" novalidate="" enctype="multipart/form-data">
		<div class='row'>
			<div class="form-group col-12 col-md-6 col-lg-6">
				<!-- <a class="btn btn-info btn-sm dropdown-toggle" href="#" id="dropdown01" type='button' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Functional Label </a>
				<div class="dropdown-menu" aria-labelledby="dropdown01" >
					<a class="dropdown-item" href="#">MM</a>
					<a class="dropdown-item" href="#">PM</a>
					<a class="dropdown-item" href="#">FICO</a>
					<a class="dropdown-item" href="#">HR</a>
				</div>
	 -->
				<select name='modul'>
					
					<option  selected>Pilih Modul</option>
					<option  value="MM">MM</option>
					<option  value="PM">PM</option>
					<option  value="FICO">FICO</option>
					<option  value="HR">HR</option>
				</select>

			</div>
		</div>
		<div class="row">
			  <div class="form-group col-12 col-md-6 col-lg-6">
			  	<label>Request</label>
			    <textarea class="summernote form-control" name="request" placeholder="Type your request here" style="height: 210px;" required=""></textarea>
			  </div>

			  <div class="form-group col-12 col-md-6 col-lg-6">
			  	<label>Reason</label>
			    <textarea class="summernote form-control" name="reason" placeholder="Type your reason here" style="height: 210px;" required=""></textarea>
			  </div>
		  </div>

		  <div class="row">
		  	  <div class="form-group col-12">
				  <label>Attachment - <i>Image Only</i> (Optional)</label>
				  <input class="form-control" type="file" name="attachment[]" multiple="">
			  </div>
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

<script type="text/javascript" src="<?= site_url("public/stisla/js/dropzone.min.js"); ?>"></script>