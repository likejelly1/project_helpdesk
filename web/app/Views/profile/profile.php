<link rel="stylesheet" type="text/css" href="https://getstisla.com/dist/modules/bootstrap-social/bootstrap-social.css">

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="card">
  <div class="card-body">
    <h2 class="section-title">Hi, <?php echo explode(" ", $karyawan['name'])[0]; ?>!</h2>
    <p class="section-lead">
      Change information about yourself on this page.
    </p>

    <div class="row mt-sm-4">
      <div class="col-12 col-md-12 col-lg-5">
        <div class="card profile-widget">
          <div class="profile-widget-header">
            <img alt="image" src="<?= site_url("public/stisla/img/avatar/avatar-1.png"); ?>" class="rounded-circle profile-widget-picture">
            <div class="profile-widget-items">
              <div class="profile-widget-item">
                <div class="profile-widget-item-label">Service</div>
                <div class="profile-widget-item-value"><?php echo $karyawan['t_service']; ?></div>
              </div>

              <div class="profile-widget-item">
                <div class="profile-widget-item-label">Review</div>
                <div class="profile-widget-item-value"><?php echo number_format($karyawan['t_score'], 1, '.', ''); ?></div>
              </div>
            </div>
          </div>
          <div class="profile-widget-description">
            <div class="profile-widget-name"><?php echo ucwords(strtolower($karyawan['name'])); ?> <div class="text-muted d-inline font-weight-normal"><div class="slash"></div> <?php echo ucwords(strtolower($karyawan['position'])); ?> <div class="slash"></div> <?php echo $karyawan['site'] ?></div></div>
            <?php echo $karyawan['bio']; ?>
          </div>
          <?php /* ?><div class="card-footer text-center">
            <div class="font-weight-bold mb-2">Follow Ujang On</div>
            <a href="#" class="btn btn-social-icon btn-facebook mr-1">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="btn btn-social-icon btn-twitter mr-1">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="btn btn-social-icon btn-github mr-1">
              <i class="fab fa-github"></i>
            </a>
            <a href="#" class="btn btn-social-icon btn-instagram">
              <i class="fab fa-instagram"></i>
            </a>
          </div><?php */ ?>
        </div>
      </div>
      <div class="col-12 col-md-12 col-lg-7">
        <div class="card">
          <form action="<?= base_url('profile'); ?>" method="POST" class="needs-validation" novalidate="">
            <div class="card-header">
              <h4>Edit Profile</h4>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="form-group col-md-7 col-12">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $karyawan['name'] ?>" required="">
                    <div class="invalid-feedback">
                      Please fill in your name
                    </div>
                  </div>

                  <div class="form-group col-md-5 col-12"  data-toggle="tooltip" data-placement="top" title="Follow telegram bot first at @Helpd3sk_bot and click /start. Then input your telegram username here.">
                    <label>Telegram Username</label>
                    <input type="text" name="tg_username" class="form-control" value="<?php echo $karyawan['tg_username'] ?>">
                  </div>
                  
                </div>
                <div class="row">
                  <div class="form-group col-md-7 col-12">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $karyawan['email'] ?>" required="">
                    <div class="invalid-feedback">
                      Please fill in the email
                    </div>
                  </div>
                  <div class="form-group col-md-5 col-12">
                    <label>Phone</label>
                    <input type="tel" name="telp" class="form-control" value="<?php echo $karyawan['telp'] ?>">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-6 col-6">
                  <?php echo view('profile/signature'); ?>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-12">
                    <label>Bio</label>
                    <textarea class="form-control summernote-simple" name="bio"><?php echo $karyawan['bio'] ?></textarea>
                  </div>
                </div>
            </div>
            <div class="card-footer text-right">
              <button class="btn btn-primary btn-sbt" type="submit">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>  
