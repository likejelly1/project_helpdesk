<style type="text/css">
  .csize {
    font-size: 50px;
    color: rgb(0, 188, 212);
    cursor: pointer;
    padding: .25rem;
  }
  .csize:hover {
    color : rgb(63, 81, 181);
  }
</style>

<form action="<?= base_url('SR_user/complete'); ?>" method="POST">
  <div style="text-align: center; margin-top: 2.5rem">
    <i class="csize far fa-angry fa-lg" v="1"></i>
    <i class="csize far fa-frown fa-lg" v="2"></i>
    <i class="csize far fa-meh fa-lg" v="3"></i>
    <i class="csize far fa-smile fa-lg" v="4"></i>
    <i class="csize far fa-grin-stars fa-lg" v="5" style="color: rgb(63, 81, 181)"></i>
    <input type="hidden" name="no_sr" value="<?php echo $no_sr ?>">
    <input type="hidden" name="score" value="5">
  </div>

  <div class="form-group" style="margin-top: 2.5rem">
    <label>Write your review here</label>
    <textarea class="form-control" style="height: 90px;" name="review"></textarea>
  </div>

  <div class="form-group text-right">
    <button class="btn btn-primary btn-lg" type="submit">Submit</button>
  </div>
</form>

<script type="text/javascript">
  $(document).ready(function() {
    $('.modal-body').on('click', '.csize', function() {
      $('.csize').attr("style", "");
      $(this).attr("style", "color: rgb(63, 81, 181)");
      v = $(this).attr("v");
      $('input[name="score"]').val(v);
    }); 
  });
</script>