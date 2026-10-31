<?php

session_start();
ob_start();
include "config.php";
$vendor_id = $_SESSION['vendor_id']; // Get vendor ID from session

// Fetch vendor profile picture
$query = $pdo->prepare("
    SELECT profile_picture 
    FROM vendors 
    WHERE vendor_id = ?
");
$query->execute([$vendor_id]);
$vendor = $query->fetch(PDO::FETCH_ASSOC);

// Set profile picture (default if not available)
$profile_picture = !empty($vendor['profile_picture']) ?   htmlspecialchars($vendor['profile_picture']) : 'images/default_profile.png';

// Fetch vendor notifications
$notifications_query = $pdo->prepare("
    SELECT 
        message, 
        DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') AS formatted_date 
    FROM notifications 
    WHERE vendor_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$notifications_query->execute([$vendor_id]);
$notifications = $notifications_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title> User Dashboard</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <!-- <link rel="shortcut icon" href="images/favicon.png" /> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
      .content-wrapper{
          background-color:#ededed;
      }
      .sidebar .nav .nav-item.active > .nav-link{
          background-color:#ededed !important;
      }
     .sidebar .nav .nav-item.active > .nav-link i, .sidebar .nav .nav-item.active > .nav-link .menu-title, .sidebar .nav .nav-item.active > .nav-link .menu-arrow{
          color:black !important;
      }
      .sidebar .nav:not(.sub-menu) > .nav-item > .nav-link:hover{
          background-color:#ededed !important;
          
          color:black !important;
      }
      .sidebar .nav .nav-item.active > .nav-link i, .sidebar .nav .nav-item.active > .nav-link .menu-title, .sidebar .nav .nav-item.active > .nav-link .menu-arrow:hover{
          background-color:#ededed !important;
          color:black !important;
          
      }
      .sidebar .nav.sub-menu{
           background-color:#ededed !important;
          color:black !important;
      }
      .sidebar .nav:not(.sub-menu) > .nav-item > .nav-link[aria-expanded="true"]{
            background-color:#ededed !important;
          color:black !important;
      }
      .sidebar .nav.sub-menu .nav-item .nav-link{
          color: black !important;
      }
          .sidebar .nav.sub-menu .nav-item .nav-link:hover{
          color:black !important;
      }
       .sidebar .nav-item:hover .nav-link span,i{
    color: black !important;
}
  </style>

</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="../assets/corporate/img/Hangar-2-4-White-Final-1.png" class="mr-2" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-info">
                    <i class="ti-user mx-0"></i>
                  </div>
                </div>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0">Notifications</h6>
                <div class="dropdown-divider"></div>
                <?php if (!empty($notifications)): ?>
                  <?php foreach ($notifications as $notification): ?>
                    <a class="dropdown-item preview-item">
                      <div class="preview-item-content">
                        <h6 class="preview-subject font-weight-normal"><?= htmlspecialchars($notification['message']); ?></h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          <?= htmlspecialchars($notification['formatted_date']); ?>
                        </p>
                      </div>
                    </a>
                    <div class="dropdown-divider"></div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <a class="dropdown-item preview-item">
                    <div class="preview-item-content">
                      <h6 class="preview-subject font-weight-normal">No new notifications</h6>
                      <p class="font-weight-light small-text mb-0 text-muted">
                        You are all caught up!
                      </p>
                    </div>
                  </a>
                <?php endif; ?>
                <h6 class="p-3 mb-0 text-center">View all</h6>
              </div>
              </a>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
            <img src="<?= $profile_picture; ?>" alt="profile" class="profile-img" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="vendor-profile.php">
                <i class="ti-user text-primary"></i>
                Profile
              </a>
              <a class="dropdown-item" href="logout.php">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="icon-grid menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <!-- Profile -->
      <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#profile-management" aria-expanded="false" aria-controls="profile-management">
        <i class="icon-head menu-icon"></i>
        <span class="menu-title">My Profile</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="profile-management">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="vendor-profile.php">Login profile</a></li>
          <li class="nav-item"><a class="nav-link" href="vendor-business.php">Business profile</a></li>
        </ul>
      </div>
    </li>

      <!-- Listing Management -->
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#listing-management" aria-expanded="false" aria-controls="listing-management">
          <i class="icon-tag menu-icon"></i>
          <span class="menu-title">Manage Listings</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="listing-management">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="addaircraft.php">List an Aircraft</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_products.php">My Listings</a></li>
          </ul>
        </div>
      </li>

      <!-- Orders -->
      <li class="nav-item">
        <a class="nav-link" href="orders-management.php">
          <i class="icon-bag menu-icon"></i>
          <span class="menu-title">Orders</span>
        </a>
      </li>

      <!-- Payments -->
      <li class="nav-item">
        <a class="nav-link" href="manage_payments.php">
          <i class="icon-stack menu-icon"></i>
          <span class="menu-title">Payments</span>
        </a>
      </li>

      <!-- Support -->
      <li class="nav-item">
        <a class="nav-link" href="messages-support.php">
          <i class="icon-mail menu-icon"></i>
          <span class="menu-title">Messages & Support</span>
        </a>
      </li>

      <!-- Notifications -->
      <li class="nav-item">
        <a class="nav-link" href="notifications.php">
          <i class="icon-bell menu-icon"></i>
          <span class="menu-title">Notifications</span>
        </a>
      </li>
      <li class="nav-item">
  <a class="nav-link" href="kpi_dashboard.php">
    <i class="icon-pie-graph menu-icon"></i>
    <span class="menu-title">KPI Dashboard</span>
  </a>
</li>

    </ul>
</nav>
