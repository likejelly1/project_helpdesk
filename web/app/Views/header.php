<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Helpdesk</title>
  <link rel="shortcut icon" type="image/jpg" href="<?= site_url("public/stisla/img/MHA_fav.ico") ?>"/>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- Template CSS -->
  <link rel="stylesheet" href="<?= site_url("public/stisla/css/style.css"); ?>">
  <link rel="stylesheet" href="<?= site_url("public/stisla/css/components.css"); ?>">

  <!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"> -->

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= site_url("public/stisla/css/select2.min.css"); ?>" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg" id="burger"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <!-- Notification Comment 
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notifications
                <div class="float-right">
                  <a href="#">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-icon bg-primary text-white">
                    <i class="fas fa-code"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Template update is available now!
                    <div class="time text-primary">2 Min Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="far fa-user"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>You</b> and <b>Dedik Sugiharto</b> are now friends
                    <div class="time">10 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-success text-white">
                    <i class="fas fa-check"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Kusnaedi</b> has moved task <b>Fix bug header</b> to <b>Done</b>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Low disk space. Let's clean it!
                    <div class="time">17 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Welcome to Stisla template!
                    <div class="time">Yesterday</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li> -->

          <?php $this->session = \Config\Services::session();
                $nik = $this->session->get('nik');
                $name = $this->session->get('name');
                $it_staff = $this->session->has('it_staff');
                $site = $this->session->get('site');
                $arr_name = explode(" ", $name);
                $first_name = $arr_name[0]; ?>
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="<?= site_url("public/stisla/img/avatar/avatar-1.png"); ?>" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, <?= $first_name ?></div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <!-- <div class="dropdown-title">Logged in 5 min ago</div> -->
              <a href="<?= base_url('profile'); ?>" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <a href="<?= base_url('profile/changePassword'); ?>" target="_blank" class="dropdown-item has-icon">
                <i class="fas fa-lock"></i> Change Password
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?= base_url('logout'); ?>" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand" style="height: 85px;">
            <a href="<?= base_url() ?>"><img src="<?= site_url("public/stisla/img/MHA.png") ?>" alt="logo" width="75" class=""></a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?= base_url() ?>"> 
              <img src="<?= site_url("public/stisla/img/MHA.png") ?>" alt="logo" width="40"></a>
          </div>
          <ul class="sidebar-menu">
              <?php if(isApproveArr() || isPendingApprove()) { ?>
              <li class="menu-header">Approver - Service Request</li>
              <li><a class="nav-link" href="<?= base_url('Approve'); ?>"><i class="fas fa-user-check"></i> <span>Approve SR</span></a></li>   
              <?php } ?>

              <?php if($it_staff) { //IT Staff ?>
              <li class="menu-header">IT Staff - Service Request</li>
              <?php if(isHO()) { ?>
              <li><a class="nav-link" href="<?= base_url('SR/dashboard'); ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>  
              <?php } ?>

              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-file"></i><span>User's Request</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="<?= base_url('SR/process_request'); ?>">Process Request</a></li>
                </ul>
              </li>

              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-file-invoice"></i><span>Service Request</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="<?= base_url('SR/my_service'); ?>">My Service</a></li>
                  <?php if(isHO()) { ?>
                  <li><a class="nav-link" href="<?= base_url('SR/all'); ?>">All Service Request</a></li>
                  <?php } ?>
                </ul>
              </li>

              <?php if(is_PIC_RO()) { ?>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-file-prescription"></i>  <span>Request Order</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="<?= base_url('RO/my_ro'); ?>">My RO</a></li>
                  <?php /* if(isHO()) { ?>
                  <li><a class="nav-link" href="<?= base_url('ro/all'); ?>">All Request Order</a></li>
                  <?php } */ ?>
                </ul>
              </li>
              <?php } } ?>

              <?php if(in_array($nik, nik_exportSR())) { ?>
                <li><a class="nav-link" href="<?= base_url('Export'); ?>"><i class="far fa-file-excel"></i> <span>Export</span></a></li> 
              <?php } ?>

              <li class="menu-header">Service Request</li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-paste"></i><span>Request</span></a>
                <ul class="dropdown-menu">
                  <li><a id="ServiceModalButton" class="nav-link" href="#">New Request</a></li>
                  <li><a class="nav-link" href="<?= base_url('SR/pending_request'); ?>">Pending Request</a></li>
                  <li><a class="nav-link" href="<?= base_url('SR_user/my_request'); ?>">My Request</a></li>
                </ul>
              </li>            
            </ul>
        </aside>
      </div>

      <div class="main-content">
        <section class="section">


        