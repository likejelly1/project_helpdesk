<div class="row">
  <div class="col-sm-6 col-md-6 col-lg-6">

    <div class="card">
      <div class="card-header">
        <h4>Export</h4>
      </div>	

      <div class="card-body">
        <form method="POST" action="<?= base_url('Export/trx'); ?>" class="needs-validation" novalidate="">
          <div class="form-group">
            <label>Start Date</label>
            <input type="date" class="form-control" name="start" required="">
          </div>

          <div class="form-group">
            <label>End Date</label>
            <input type="date" class="form-control" name="end" required="">
          </div>

          <div class="form-group text-right">
            <button class="btn btn-primary btn-lg">
              Export
            </button>
          </div>
        </form>
            
      </div>
    </div>

  </div>
</div>