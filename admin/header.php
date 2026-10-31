<?php
session_start();
// ob_start();
include "config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];


$admin_id = $_SESSION['admin_id'];

// Get admin details
$stmt = $pdo->prepare("SELECT status, role FROM admins WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$admin_role = $admin['role'] ?? 'Regular Admin'; // default fallback
$admin_status = $admin['status']; // default fallback

// Get admin ID from session
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title> User Dashboard</title>
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
          color:black !important;
      }
        .sidebar .nav-item:hover .nav-link span,i{
    color: black !important;
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
      .sidebar .nav-item:hover{
          color:black !important;
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
            
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
            <img src="<?= $profile_picture; ?>" alt="profile" class="profile-img" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="admin-manage.php">
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
     <?php if ($admin_role === 'Super Admin' && $admin_status === 'active'): ?>
    <!-- Admin Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard</span>
        </a>
    </li>

    <!-- User Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-manage.php">
            <i class="icon-head menu-icon"></i>
            <span class="menu-title">Manage Admins</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-vendors.php">
            <i class="ti-user menu-icon"></i>
            <span class="menu-title">Manage Vendors</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-clients.php">
            <i class="ti-user menu-icon"></i>
            <span class="menu-title">Manage Clients</span>
        </a>
    </li>

<li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-client-management" aria-expanded="false" aria-controls="admin-client-management">
            <i class="ti-list menu-icon"></i>
            <span class="menu-title">Client Product Listing</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-client-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="add_client_aircraft_list.php">Add Client Aircrafts</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-vendor-management" aria-expanded="false" aria-controls="admin-vendor-management">
            <i class="ti-list menu-icon"></i>
            <span class="menu-title">Vendor Product Listing</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-vendor-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="add_vendor_aircraft_list.php">Add Vendor Aircrafts</a></li>
                 <li class="nav-item"><a class="nav-link" href="add_vendor_engine_list.php">Add Vendor Engines</a></li>
                  <li class="nav-item"><a class="nav-link" href="add_vendor_part_list.php">Add Vendor Parts</a></li>
            </ul>
        </div>
    </li>
    <!-- Product & Listing Management -->
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-product-management" aria-expanded="false" aria-controls="admin-product-management">
            <i class="icon-tag menu-icon"></i>
            <span class="menu-title">Manage Product </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-product-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="admin-aircrafts.php">Manage Aircrafts</a></li>
                <li class="nav-item"><a class="nav-link" href="admin-engines.php">Manage Engines</a></li>
                <li class="nav-item"><a class="nav-link" href="admin-parts.php">Manage Parts</a></li>
                <li class="nav-item"><a class="nav-link" href="admin-services.php">Manage Services</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
    <a class="nav-link" data-toggle="collapse" href="#admin-bulk-upload" aria-expanded="false" aria-controls="admin-bulk-upload">
        <i class="ti-upload menu-icon"></i>
        <span class="menu-title">Bulk Upload Services</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="admin-bulk-upload">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="bulk_upload_flight_school.php">Upload Services</a></li>
            <li class="nav-item"><a class="nav-link" href="transfer_ownership.php">Transfer Ownership</a></li>
        </ul>
    </div>
</li>

   <li class="nav-item">
    <a class="nav-link" data-toggle="collapse" href="#admin-article-management"
       aria-expanded="false" aria-controls="admin-article-management">
        <i class="icon-tag menu-icon"></i>
        <span class="menu-title">Article Management</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="admin-article-management">
        <ul class="nav flex-column sub-menu">

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link" href="categories.php">Categories</a>
            </li>

            <!-- Writers / Fact Checkers -->
            <li class="nav-item">
                <a class="nav-link" href="content_people.php">
                    Writers & Fact Checkers
                </a>
            </li>

            <!-- Articles -->
            <li class="nav-item">
                <a class="nav-link" href="add_article.php">Add Article</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="articles_list.php">List of Articles</a>
            </li>

        </ul>
    </div>
</li>
     <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-category-management" aria-expanded="false" aria-controls="admin-category-management">
            <i class="icon-layers menu-icon"></i>
            <span class="menu-title">Manage Categories </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-category-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="aircrafts_categories.php">Aircrafts Category</a></li>
                <li class="nav-item"><a class="nav-link" href="engines_categories.php">Engines Category</a></li>
                <li class="nav-item"><a class="nav-link" href="parts_categoies.php">Parts Category</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management" aria-expanded="false" aria-controls="admin-seo-management">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Pages SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="seo_pages.php">Add  Pages SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="seo_success.php">All Pages SEO</a></li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management1" aria-expanded="false" aria-controls="admin-seo-management1">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Products SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="product_seo.php">Add  Products SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All products SEO</a></li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management2" aria-expanded="false" aria-controls="admin-seo-management2">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Blog SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management2">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="blog_seo.php">Add  Blog SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All Blogs SEO</a></li>
                </ul>
            </div>
        </li>

    <!-- Orders & Payments Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-orders.php">
            <i class="ti-bag menu-icon"></i>
            <span class="menu-title">Manage Orders</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-payments.php">
            <i class="ti-wallet menu-icon"></i>
            <span class="menu-title">Manage Pan & Escrow</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-withdrawals.php">
            <i class="ti-export menu-icon"></i>
            <span class="menu-title">Manage Withdrawals</span>
        </a>
    </li>

    <!-- Shipping Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-shipping.php">
            <i class="ti-truck menu-icon"></i>
            <span class="menu-title">Manage Shipments</span>
        </a>
    </li>
        

    <!-- Discount & Promotions -->
    <li class="nav-item">
        <a class="nav-link" href="admin-discounts.php">
            <i class="icon-tag menu-icon"></i>
            <span class="menu-title">Manage Discounts</span>
        </a>
    </li>

    <!-- Reports & Logs -->
    <li class="nav-item">
        <a class="nav-link" href="admin-reports.php">
            <i class="ti-bar-chart menu-icon"></i>
            <span class="menu-title">Transaction Reports</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-notifications.php">
            <i class="icon-bell menu-icon"></i>
            <span class="menu-title">Email & Notifications</span>
        </a>
    </li>
    

<li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#AeroMetrics-category-management" aria-expanded="false" aria-controls="AeroMetrics-category-management">
            <i class="ti-bar-chart menu-icon"></i>
            <span class="menu-title">AeroMetrics </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="AeroMetrics-category-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="admin-kpi.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="page-analytics.php">Page Analytics </a></li>
                <li class="nav-item"><a class="nav-link" href="products-analytics.php">Products Analytics</a></li>
                <li class="nav-item"><a class="nav-link" href="aircraft-products.php">Aircraft Products </a></li>
                <li class="nav-item"><a class="nav-link" href="part-products.php">Part Products </a></li>
                <li class="nav-item"><a class="nav-link" href="engine-products.php">Engine Products </a></li>
                <li class="nav-item"><a class="nav-link" href="services-analytics.php">Services Analytics</a></li>
            </ul>
        </div>
    </li>

</ul>
<?php endif; ?>

 <?php if ($admin_role === 'SEO Admin' && $admin_status === 'active'): ?>
   <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management" aria-expanded="false" aria-controls="admin-seo-management">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Pages SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="seo_pages.php">Add  Pages SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="seo_success.php">All Pages SEO</a></li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management1" aria-expanded="false" aria-controls="admin-seo-management1">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Products SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="product_seo.php">Add  Products SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All products SEO</a></li>
                </ul>
            </div>
        </li>
          <li class="nav-item">
    <a class="nav-link" data-toggle="collapse" href="#admin-article-management"
       aria-expanded="false" aria-controls="admin-article-management">
        <i class="icon-tag menu-icon"></i>
        <span class="menu-title">Article Management</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="admin-article-management">
        <ul class="nav flex-column sub-menu">

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link" href="categories.php">Categories</a>
            </li>

            <!-- Writers / Fact Checkers -->
            <li class="nav-item">
                <a class="nav-link" href="content_people.php">
                    Writers & Fact Checkers
                </a>
            </li>

            <!-- Articles -->
            <li class="nav-item">
                <a class="nav-link" href="add_article.php">Add Article</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="articles_list.php">List of Articles</a>
            </li>

        </ul>
    </div>
</li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management2" aria-expanded="false" aria-controls="admin-seo-management2">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Blog SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management2">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="blog_seo.php">Add  Blog SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All Blogs SEO</a></li>
                </ul>
            </div>
        </li>
        
 <?php endif; ?>
 
  <?php if ($admin_role === 'Regular Admin' && $admin_status === 'active'): ?>
  
   <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard</span>
        </a>
    </li>

    <!-- User Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-manage.php">
            <i class="icon-head menu-icon"></i>
            <span class="menu-title">Manage Admins</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-vendors.php">
            <i class="ti-user menu-icon"></i>
            <span class="menu-title">Manage Vendors</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-clients.php">
            <i class="ti-user menu-icon"></i>
            <span class="menu-title">Manage Clients</span>
        </a>
    </li>

<li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-client-management" aria-expanded="false" aria-controls="admin-client-management">
            <i class="ti-list menu-icon"></i>
            <span class="menu-title">Client Product Listing</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-client-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="add_client_aircraft_list.php">Add Client Aircrafts</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-vendor-management" aria-expanded="false" aria-controls="admin-vendor-management">
            <i class="ti-list menu-icon"></i>
            <span class="menu-title">Vendor Product Listing</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-vendor-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="add_vendor_aircraft_list.php">Add Vendor Aircrafts</a></li>
                 <li class="nav-item"><a class="nav-link" href="add_vendor_engine_list.php">Add Vendor Engines</a></li>
                  <li class="nav-item"><a class="nav-link" href="add_vendor_part_list.php">Add Vendor Parts</a></li>
            </ul>
        </div>
    </li>
    <!-- Product & Listing Management -->
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-product-management" aria-expanded="false" aria-controls="admin-product-management">
            <i class="icon-tag menu-icon"></i>
            <span class="menu-title">Manage Product </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-product-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="admin-aircrafts.php">Manage Aircrafts</a></li>
                <li class="nav-item"><a class="nav-link" href="admin-engines.php">Manage Engines</a></li>
                <li class="nav-item"><a class="nav-link" href="admin-parts.php">Manage Parts</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
    <a class="nav-link" data-toggle="collapse" href="#admin-article-management"
       aria-expanded="false" aria-controls="admin-article-management">
        <i class="icon-tag menu-icon"></i>
        <span class="menu-title">Article Management</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="admin-article-management">
        <ul class="nav flex-column sub-menu">

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link" href="categories.php">Categories</a>
            </li>

            <!-- Writers / Fact Checkers -->
            <li class="nav-item">
                <a class="nav-link" href="content_people.php">
                    Writers & Fact Checkers
                </a>
            </li>

            <!-- Articles -->
            <li class="nav-item">
                <a class="nav-link" href="add_article.php">Add Article</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="articles_list.php">List of Articles</a>
            </li>

        </ul>
    </div>
</li>
     <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#admin-category-management" aria-expanded="false" aria-controls="admin-category-management">
            <i class="icon-layers menu-icon"></i>
            <span class="menu-title">Manage Categories </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="admin-category-management">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="aircrafts_categories.php">Aircrafts Category</a></li>
                <li class="nav-item"><a class="nav-link" href="engines_categories.php">Engines Category</a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management" aria-expanded="false" aria-controls="admin-seo-management">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Pages SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="seo_pages.php">Add  Pages SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="seo_success.php">All Pages SEO</a></li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management1" aria-expanded="false" aria-controls="admin-seo-management1">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Products SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="product_seo.php">Add  Products SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All products SEO</a></li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin-seo-management2" aria-expanded="false" aria-controls="admin-seo-management2">
                <i class="ti-stats-up menu-icon"></i>
                <span class="menu-title">Blog SEO</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-seo-management2">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="blog_seo.php">Add  Blog SEO</a></li>
                     <li class="nav-item"><a class="nav-link" href="product_success_seo.php">All Blogs SEO</a></li>
                </ul>
            </div>
        </li>

    <!-- Orders & Payments Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-orders.php">
            <i class="ti-bag menu-icon"></i>
            <span class="menu-title">Manage Orders</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin-payments.php">
            <i class="ti-wallet menu-icon"></i>
            <span class="menu-title">Manage Pan & Escrow</span>
        </a>
    </li>
   

    <!-- Shipping Management -->
    <li class="nav-item">
        <a class="nav-link" href="admin-shipping.php">
            <i class="ti-truck menu-icon"></i>
            <span class="menu-title">Manage Shipments</span>
        </a>
    </li>
        

   

  
    <li class="nav-item">
        <a class="nav-link" href="admin-notifications.php">
            <i class="icon-bell menu-icon"></i>
            <span class="menu-title">Email & Notifications</span>
        </a>
    </li>
  <?php endif; ?>
</nav>


