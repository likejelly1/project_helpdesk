<div class="card boom" style="display: none">
  <div class="card-header">
    <h4>Form Service Request</h4>
  </div>

  <!-- Service Request Open -->
  <div class="card-body">
    <div class="row">
      <div class="form-group col-12 col-md-6 col-lg-6">
        <label>Request</label>
        <textarea class="summernote form-control" name="request" style="height: 170px;"><?= $trx_sr['rsr_request']; ?></textarea>
      </div>

      <div class="form-group col-12 col-md-6 col-lg-6">
        <label>Reason</label>
        <textarea class="summernote form-control" name="reason" style="height: 170px;"><?= $trx_sr['rsr_reason'] ?></textarea>
      </div>
    </div>

    <div class="row">
        <div class="form-group col-12 col-md-12 col-lg-12" id="sr_category">
        </div>
    </div>

    <?php echo view('svc/table_priority'); ?>

    <div class="row">
      <div class="col-6 col-md-6 col-lg-6">
        <div class="form-group">
          <label>Urgency</label>
          <select class="form-control select2" name="urgency">
            <option value="L">Low</option>
            <option value="M">Medium</option>
            <option value="H">High</option>
          </select>
        </div>
      </div>

      <div class="col-6 col-md-6 col-lg-6">
        <div class="form-group">
          <label>Impact</label>
          <select class="form-control select2" name="impact">
            <option value="L">Low</option>
            <option value="M">Medium</option>
            <option value="H">High</option>
          </select>
        </div>
      </div>
    </div>
                    

  </div>
  <!-- Service Request Close -->
  <!-- </form> -->

</div>

<script type="text/javascript">
  $(document).on('change', 'input[name="svc"]', function() {
    var svc = $(this).val();
    $.ajax({
        url: "<?= base_url('SR/loadDSVC'); ?>",
        cache: false,
        method: "POST",
        data: {svc: svc},
        success: function(result){
          if(result) {
            $('#sr_form #sr_category').html(result);
            $('#sr_form .boom').show();
          }
        }
    });
  });
</script>

