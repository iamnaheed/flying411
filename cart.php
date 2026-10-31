<?php
// Include database configuration
require 'config.php';
session_start();

// Get type from URL safely
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Define valid product types and their corresponding ID column names
$valid_types = [
    'aircrafts' => 'aircraft_id',
    'engines' => 'engine_id',
    'parts' => 'part_id'
];

// Validate the product type
if (!array_key_exists($type, $valid_types)) {
    die("Invalid product type.");
}

// Determine the correct ID column and fetch the ID dynamically
$id_column = $valid_types[$type];
$id = isset($_GET[$id_column]) ? (int)$_GET[$id_column] : 0;
if ($id === 0) {
    die("Invalid product ID.");
}

try {
    // Fetch product details
    $stmt = $pdo->prepare("SELECT * FROM $type WHERE $id_column = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }

    // Map URL type to database product_type for images
    $product_types_map = [
        'aircrafts' => 'aircraft',
        'engines' => 'engine',
        'parts' => 'part'
    ];
    $db_product_type = $product_types_map[$type] ?? $type;

    // Fetch product images
    $image_stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = :id AND product_type = :type");
    $image_stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $image_stmt->bindParam(':type', $db_product_type, PDO::PARAM_STR);
    $image_stmt->execute();
    $images = $image_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch product reviews
    $review_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = :id AND product_type = :type");
    $review_stmt->execute(['id' => $id, 'type' => $type]);
    $reviewData = $review_stmt->fetch(PDO::FETCH_ASSOC);

    $avgRating = round($reviewData['avg_rating'] ?? 0, 1);
    $totalReviews = $reviewData['total_reviews'] ?? 0;

    // Fetch individual reviews (latest 5)
    $reviews_stmt = $pdo->prepare("SELECT rating, review_text, created_at FROM reviews WHERE product_id = :id AND product_type = :type ORDER BY created_at DESC LIMIT 5");
    $reviews_stmt->execute(['id' => $id, 'type' => $type]);
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="utf-8">
<title>Shopping Cart | Flying411</title>

<!-- Responsive and Compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!-- SEO Meta -->
<meta name="description" content="View and manage your selected aircraft, parts, and services in your shopping cart at Flying411, the global aviation marketplace.">
<meta name="keywords" content="Flying411, shopping cart, aircraft cart, aviation parts cart, services cart">
<meta name="author" content="Flying411">

<!-- Open Graph Meta for social sharing -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="Shopping Cart | Flying411">
<meta property="og:description" content="View and manage your selected aircraft, parts, and services in your shopping cart at Flying411.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<meta property="og:url" content="https://flying411.com/cart.php">

<!-- Favicon and Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">
<link rel="shortcut icon" href="favicon.ico">

  <!-- Fonts START -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <!-- Fonts END -->
<!-- Global styles START -->          
  <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <link href="assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">
  <link href="assets/plugins/owl.carousel/assets/owl.carousel.css" rel="stylesheet">
  <link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
  <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"><!-- for slider-range -->
  <link href="assets/plugins/rateit/src/rateit.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin styles END -->

  <!-- Theme styles START -->
  <link href="assets/pages/css/components.css" rel="stylesheet">
  <link href="assets/corporate/css/style.css" rel="stylesheet">
  <link href="assets/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">
  <link href="assets/corporate/css/custom.css" rel="stylesheet">
  <link
  href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
  rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
  integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
  crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .container {
           display: flex;
           flex-wrap: wrap;
           justify-content: space-between;
           width: 85%;
           margin: auto;
           padding: 0px 20px 20px 20px;
       }

       /* Filter Section */
       .filter-section {
           width: 300px;
           background-color: #fff;
           border: 1px solid #ddd;
           border-radius: 8px;
           padding: 15px;
           /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
       }

       .filter-section h3 {
           font-size: 25px;
           margin: 0 0 20px;
           color: #333;
           /* font-weight: bold; */
       }

       .reset-icon {
           float: right;
           font-size: 16px;
           color: #666;
           cursor: pointer;
           text-decoration: none;
       }

       .filter-item {
           margin-bottom: 15px;
       }

       .filter-item label {
           display: flex;
           align-items: center;
           margin-bottom: 10px;
           font-size: 14px;
           color: #555;
           cursor: pointer;
       }

       .filter-item input[type="checkbox"] {
           margin-right: 10px;
       }

       .filter-item .dropdown-box {
           display: flex;
           align-items: center;
           justify-content: space-between;
           padding: 10px;
           font-size: 14px;
           border: 1px solid #ddd;
           border-radius: 4px;
           cursor: pointer;
           background-color: #fff;
       }

       .filter-item .dropdown-box:hover {
           background-color: #f4f4f4;
       }

       .expandable-section {
           display: none;
           border-top: 1px solid #ddd;
           margin-top: 10px;
           padding-top: 10px;
       }

       /* Right Section */
       .right-section {
           flex: 1;
           margin-left: 20px;
       }

       .search-bar {
       display: flex;
       flex-direction: column;
       gap: 10px;
       padding: 10px;
       background-color: #f9f9f9;
       border: 1px solid #ddd;
       border-radius: 8px;
       max-width: 100%;
       margin-bottom:30px;
   }
   .search-container {
       display: flex;
       align-items: center;
       gap: 10px;
   }
   .search-container input[type="text"] {
       flex: 1;
       padding: 8px;
       font-size: 16px;
       border: 1px solid #ccc;
       border-radius: 4px;
   }
   .search-container button {
       padding: 8px 16px;
       font-size: 16px;
       background-color: #333;
       color: #fff;
       border: none;
       border-radius: 4px;
       cursor: pointer;
   }
   .search-container button:hover {
       background-color: #333;
   }
   .advanced-options {
       display: flex;
       justify-content: space-between;
       font-size: 14px;
   }
   .advanced-options label {
       display: flex;
       align-items: center;
       gap: 5px;
   }

       .listing-cards {
           display: flex;
           flex-wrap: wrap;
           width:100%;
           row-gap: 20px;
           column-gap: 20px;
               }

      
       /* Responsive */
       @media (max-width: 768px) {
           .filter-section {
               width: 100%;
               margin-bottom: 20px;
           }

           .right-section {
               width: 100%;
               margin: 0;
           }

           .search-bar {
               flex-direction: column;
           }

           .search-bar input, .search-bar button {
               width: 100%;
               margin-bottom: 10px;
               border-radius: 4px;
           }

           .listing-cards {
               grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
           }
       }
     .header .mobi-toggler{
   margin:0px !important;
}  
@media (max-width: 768px) {
   .ecommerce .header .mobi-toggler{
       margin:0px !important;
   }
}

   /* Ensure consistent image size */
   .card-img-top {
     width: 100%;
     height: 200px;
     object-fit: cover;
     /* Ensures images are cropped proportionally */
   }

   /* Adjust card content layout */
   .card-title {
     font-size: 1.4rem;
     font-weight: bold;
   }

   .card-text {
     font-size: 12px;
   }

   /* Customize Add to Cart button */
   .btn-primary {
     background-color: blue;
     color: white;
     font-size: 12px;
     font-weight: bold;
     padding: 8px 20px;
     border-radius: 5px;
     /* Aircraft-inspired blue */
     border: none;
   }

   .btn-primary:hover {
     background-color: #003780;
   }


   .blog-card img {
     border-radius: 10px;
     max-height: 200px;
     object-fit: cover;
   }

   .blog-card {
     transition: transform 0.3s ease-in-out;
   }

   .blog-card:hover {
     transform: scale(1.05);
   }

   .carousel-inner {
     padding: 10px 0;
   }

   .carousel-control-prev-icon,
   .carousel-control-next-icon {
     background-color: rgba(0, 0, 0, 0.6);
     border-radius: 50%;
   }

   .blog-card-title {
     font-weight: bold;
     font-size: 22px;
     color: black;
   }

   .blog-card-desc {
     color: #666;
     font-size: 13px;
   }

   .featured-items {
     text-align: center;
     padding: 20px;
   }

   .featured-items h1 {
     margin-bottom: 20px;
     font-size: 50px;
     font-weight: 400;
   }

   .items-container {
     display: flex;
     flex-wrap: wrap;
     justify-content: center;
     gap: 20px;
   }

   .item-card {
     width: 300px;
     position: relative;
     border: 1px solid #ddd;
     border-radius: 5px;
     overflow: hidden;
     box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
     text-align: left;
   }

   .item-card img {
     width: 100%;
     height: auto;
   }

   .item-card .item-info {
     padding: 15px;
   }

   .item-card .price {

     font-size: 18px;
     margin: 10px 0;
   }

   .item-card .model {
     color: gray;
     font-size: 14px;
   }

   .item-card .add-to-cart {
     background: #007bff;
     color: white;
     text-align: center;
     padding: 10px;
     cursor: pointer;
     text-decoration: none;
     display: block;
     border-radius: 5px;
   }

   .item-card .add-to-cart:hover {
     background: #0056b3;
   }
 
   @media (max-width: 768px) {
     .item-card {
       width: 100%;
     }
   }

   /* General Styling for the Card */
   .item-card {
     border: 1px solid #ddd;
     border-radius: 8px;
     overflow: hidden;
     width: 300px;
     margin: 10px;
     box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
     text-align: center;
   }

   .image-container {
     position: relative;
     width: 100%;
   }

   .image-container img {
     width: 100%;
     height: 300px;
     border-bottom: 1px solid #ddd;
   }

   /* Bookmark Icon Styling */
   .bookmark-icon {
     position: absolute;
     top: 0px;
     right: 0px;
     font-size: 20px;
     color: lightslategrey;
     background: rgba(255, 255, 255, 0.8);
     padding: 8px 9px 16px 21px;
     border-radius: 0px 0px 0px 35px;
     cursor: pointer;
     transition: color 0.3s ease;
   }

   .bookmark-icon:hover {
     color: #007bff;
   }

   /* Item Info Styling */
   .item-info {
     padding: 15px;
     text-align: left;
   }

   .item-info h3 {
     font-size: 15px;
     color: black;
     font-weight: bold;
     text-align: left;
     font-family: "Roboto", sans-serif;
     margin-bottom: 10px;
   }

   .item-info .price {
     color: #555;
     font-weight: bold;
     margin: 5px 0;
     font-size: 14px;
   }

   .item-info .model {
     color: #555;
     font-size: 14px;
     font-weight: bold;
   }
   @media (max-width: 560px) {
     .item-card {
       width: 100% !important;
     }
   }
   .header-navigation ul li a{
     color: white !important;
   }
   .header-navigation ul li a:hover{
     background-color: transparent;
     color: #555;
   }
   body {
     background: #f5f5f5;
   }

   .cards-container {
     display: flex !important;
     flex-wrap: wrap !important;
     gap: 1.5rem !important;
     max-width: 1200px !important;
     margin: 0 auto !important;
   }
   
   /* Card */
   .car-card {
     background: #fff !important;
     border-radius: 8px !important;
     overflow: hidden !important;
     border: 1px solid #DDDDDD !important;
     display: flex !important;
     flex-direction: column !important;
     width: 32% !important;
   }

   /* Slider Container */
   .slider {
     position: relative !important;
     width: 100% !important;
     /* Fixed height so images are consistent */
     height: 250px !important;
     overflow: hidden !important;
   }

   /* "NEW" TAG at top-left */
  .new-tag {
     position: absolute !important;
     top: 10px !important;
     left: 10px !important;
     background: #fff !important;
     color: #444 !important;
     padding: 1rem 1.5rem !important;
     border-radius: 4px !important;
     font-size: 0.75rem !important;
     text-transform: uppercase !important;
     font-weight: 700 !important;
     box-shadow: 0 1px 2px rgba(0,0,0,0.15) !important;
     z-index: 2 !important;
   }

   /* Slider Images */
   .slides {
     display: flex !important;
     width: 300% !important; /* If 3 slides total, can be 300% */
     transition: transform 0.4s ease !important;
 height: 100% !important
 ;  }
   .slides img {
     width: 100% !important;
     height: 100% !important;
     object-fit: cover !important;
   }
   .header .header-navigation ul li a{
       color:black !important;
   }
   /* Left/Right Arrows */
   .arrows {
     position: absolute !important;
     top: 50% !important;
     width: 100% !important;
     display: flex !important;
     justify-content: space-between !important;
     transform: translateY(-50%) !important;
     opacity: 0 !important;
     transition: opacity 0.3s ease !important;
     z-index: 2 !important;
   padding: 0 0.5rem !important; /* Some horizontal breathing room */
   }
   .arrows span {
     background-color: #fff !important;
     color: #444 !important;
     border: 1px solid #444 !important;
     width: 32px !important;
     height: 32px !important;
     border-radius: 50% !important;
     display: inline-flex !important;
     align-items: center !important;
     justify-content: center !important;
     cursor: pointer !important;
     user-select: none !important;
     transition: background-color 0.3s, color 0.3s !important;
   }
   .arrows span:hover {
     background-color: #444 !important;
     color: #fff !important;
   }

   /* Favorite + List Icons on the top-right */
   .favorite-icons {
     position: absolute !important;
     top: 0px !important;
     right: 0px !important;
     display: flex !important;
     gap: 0.4rem !important;
     z-index: 2 !important;
     opacity: 0 !important;
     transition: opacity 0.3s ease !important;
     background-color: #fff !important;
     padding: 12px 5px 20px 15px;
     border-radius: 0px 0px 0px 40px !important;
   }
 
   .favorite-icons i:hover {
     background-color: #444 !important;
     color: #fff !important;
   }

   /* Slider Dots at bottom-center */
   .slider-dots {
     position: absolute !important;
     bottom: 10px !important;
     width: 100% !important;
     display: flex;
     justify-content: center !important;
     gap: 0.5rem !important;
     z-index: 2 !important;
     opacity: 0 !important;
     transition: opacity 0.3s ease;
   }
   .dot {
     width: 8px !important;
     height: 8px !important;
     background: #ccc !important;
     border-radius: 50% !important;
     cursor: pointer !important;
     transition: background-color 0.3s !important;
   }
   .dot.active {
     background: #444 !important;
   }

   /* Show arrows, favorite icons, and dots on hover */
   .slider:hover .arrows,
   .slider:hover .favorite-icons,
   .slider:hover .slider-dots {
     opacity: 1 !important;
   }

   /* Card info */
   .car-info {
     padding: 1rem !important;
   }
   .car-info h3 {
     /* margin-bottom: 0.5rem !important; */
     font-size: 1.2rem !important;
     color: #444 !important;
   }
   .car-info p {
     margin: 15px 10px  !important;
     color: #000 !important;
     font-size: 18px;
   }
   .car-info p strong{
     padding-right: 10px;
   }
   .button-group {
     margin-top: 1rem !important;
     display: flex !important;
     gap: 1rem !important;
   }
   .button-group button {
     /* flex: 1; */
     border: none !important;
     width: 100%; 
     padding: 1.2rem !important;
     cursor: pointer;
     margin-top: 15px;
     margin-bottom: 10px;
     border-radius: 4px !important;
     font-weight: bold !important;
     transition: background 0.3s ease, color 0.3s ease !important;
   }
   .button-group button:nth-child(1) {
     background-color: #fff !important;
     color: #444 !important;
     border: 1px solid #444 !important;
   }
   .button-group button:nth-child(1):hover {
     background-color: #444 !important;
     color: #fff !important;

   }
   @media (max-width: 1400px) {
     .car-card {
       width: 48% !important;
     }
     .cards-container{
      justify-content: center !important;
     }
   }
   @media (max-width: 990px) {
     .car-card {
       width: 100% !important;
     }
     .cards-container{
      justify-content: center !important;
     }
   }
   /* Responsive Tweaks */
   @media (max-width: 768px) {
     .arrows span {
       width: 28px;
       height: 28px;
     }
     .favorite-icons i {
       padding: 0.3rem;
     }
     .car-info h3 {
       font-size: 1rem;
     }
     .car-card{
       width: 100% !important;
     }
   }
   .load-more {
     margin: 30px 0px;
     width: 100% !important;
 text-align: center !important;
}

.load-more-btn {
 display: inline-block;
 padding: 10px 20px;
 font-size: 16px;
 font-weight: 500;
 color: #444; /* Dark gray text */
 background-color: #fff; /* White background */
 border: 1px solid #DDDDDD; /* Border matching text color */
 border-radius: 4px;
 cursor: pointer;
 transition: all 0.3s ease;
}

.load-more-btn:hover {
 background-color: #444;
 color: #fff;
}
.car-info img{
 width:25px;
}
.filter-item select{
   width: 100%;
   padding: 11px;
   border: 1px solid gray;
}
.car-info p{
 color:#757575 !important;
}
.product-page-content {
  width: 82%;
  margin: 20px auto; /* Center content */
}

.tab-content {
  background-color: #fff; /* Ensures visibility */
  padding: 20px;
  border: 1px solid #ddd;
  border-top: none;
}

.nav-tabs > li > a {
  background-color: #f8f8f8;
  border-radius: 5px 5px 0 0;
  padding: 10px;
  font-weight: bold;
}

.nav-tabs > li.active > a {
  background-color: #fff !important;
  border: 1px solid #ddd;
  border-bottom: none;
  color:black;
}
.nav-tabs{
    border-color: black !important;
}
.product-page-content{
    padding: 0px !important;
}
.ecommerce .nav-tabs > li > a, .ecommerce .nav-tabs > li > a:hover, .ecommerce .nav-tabs > li > a:focus{
    padding: 15px 26px 15px;
}
   </style>
</head> 
<!-- Head END -->

<!-- Body BEGIN -->
<body class="ecommerce">
  <!-- BEGIN TOP BAR -->
  <div class="header" style="box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;">
    <div class="container headlogo" style="display: flex; justify-content: space-between; align-items: center;">
      <a class="site-logo" style="padding: 0px; margin:0px;" href="/"><img src="/assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Metronic Shop UI"></a>

      <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>

      

      <!-- BEGIN NAVIGATION -->
      <div class="header-navigation">
        <ul>
             <li><a href="/">HOME</a></li>
    <li><a href="/parts">PARTS</a></li>
    <li><a href="/engine">ENGINES</a></li>
    <li><a href="/aircraft">AIRCRAFT</a></li>
    <li><a href="/contacts">CONTACT US</a></li>
          
          <!-- BEGIN TOP SEARCH -->
       
          <!-- END TOP SEARCH -->
        </ul>
      </div>
  
<div class="top-cart-block">
  <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Show Dashboard link if user is logged in -->
    <a href="/client_dashboard" style="padding: 8px 20px; color: white; background:linear-gradient(to right ,#555,rgb(146, 146, 146));font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">Dashboard</a>
  <?php else: ?>
    <!-- Show Login button if user is not logged in -->
    <a href="/login" style="padding: 8px 20px; color: white; background:linear-gradient(to right ,#555,rgb(146, 146, 146));font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">LOGIN / REGISTER</a>
  <?php endif; ?>
</div>
      <!-- END NAVIGATION -->
       <!-- BEGIN CART -->
     
      <!--END CART -->
    </div>
  </div>
<div class="top-bars" style="margin:40px auto; width:82%" style="">
    <a href="javascript:void(0)" onclick="window.history.back()" style="font-size:15px;color:black">Back to Details Page</a>
    </div>
 <div class="main">
      <div class="container">
        <!-- BEGIN SIDEBAR & CONTENT -->
        <div class="row margin-bottom-40">
          <!-- BEGIN SIDEBAR -->
          <!-- END SIDEBAR -->

          <!-- BEGIN CONTENT -->
   <div class="col-md-12 col-sm-12">
    <div class="product-page">
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <div class="product-main-image">
                    <img src="<?= !empty($images) ? 'vendors/' . htmlspecialchars($images[0]) : '/assets/pages/img/products/default.jpg' ?>" 
                         alt="Product Image" class="img-responsive">
                </div>
                <?php if (!empty($images)) : ?>
                    <div class="product-other-images">
                        <?php foreach ($images as $img) : ?>
                            <a href="vendors/<?= htmlspecialchars($img) ?>" class="fancybox-button" rel="photos-lib">
                                <img src="vendors/<?= htmlspecialchars($img) ?>" alt="Product Image">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 col-sm-6">
                <h1><?= htmlspecialchars($product['model'] ?? $product['part_name'] ?? 'Unknown Product') ?></h1>
                <div class="price-availability-block clearfix">
                    <div class="price">
                        <strong style="color:black;">$ <?= isset($product['price']) ? number_format($product['price']) : 'N/A' ?></strong>
                    </div>
                    <!--<div class="availability">-->
                    <!--    Availability: <strong>In Stock</strong>-->
                    <!--</div>-->
                </div>
                <div class="description">
                    <p><?= htmlspecialchars($product['description'] ?? $product['extra_details'] ?? 'No description available.') ?></p>
                </div>
                <div class="product-page-cart my-5" style="display:flex; align-items:center;">
                    <div class="" style="border: none;
    background: #edeff1 !important;
    font: 300 23px 'Open Sans', sans-serif;
    color: #647484;
    height: 38px;
    width: 50px;
    text-align: center;
    padding: 5px; margin-right:20px">
                        <input id="product-quantity" type="text" value="1" readonly class="form-control input-sm" style="border:none;">
                    </div>
                    <a href="/checkout" style="text-decoration: none;">
                        <button class="btn btn-secondary" style="color:#7f7e81" type="button">Add to cart</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



          <!-- END CONTENT -->
        </div>
      </div>
      
  <div class="product-page-content">
    <ul id="myTab" class="nav nav-tabs">
        <li><a href="#Description" data-toggle="tab">Description</a></li>
        <li class="active"><a href="#Information" data-toggle="tab">Information</a></li>
        <!--<li ><a href="#Reviews" data-toggle="tab">Reviews (<?= count($reviews) ?>)</a></li>-->
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade" id="Description">
            <p><?= ($type === 'aircrafts') ? $product['description'] : $product['extra_details']; ?></p>
        </div>
        <div class="tab-pane  active" id="Information">
            <table class="datasheet">
                <tr>
                    <th colspan="2">Additional features</th>
                </tr>
                <?php
                $columns = [];
                if ($type === 'aircrafts') {
                    $columns = ['model',  'location', 'aircraft_type', 'manufacturer', 'condition', 'year', 'total_time_hours', 'engine_smh', 'engine_smh_hours', 'price', 'warranty'];
                } elseif ($type === 'engines') {
                    $columns = ['model', 'manufacturer', 'location', 'engine_type', 'power_thrust', 'year', 'total_time_hours', 'hr', 'cycles', 'condition', 'price', 'warranty'];
                } elseif ($type === 'parts') {
                    $columns = ['part_name', 'part_number',  'condition', 'region', 'price', 'tagged_with_easa_form_1', 'warranty'];
                }
                foreach ($columns as $col) {
                    echo "<tr><td class='datasheet-features-type'>" . ucfirst(str_replace('_', ' ', $col)) . "</td><td>" . $product[$col] . "</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

    </div>

    <!-- END BRANDS -->

    <div class="pre-footer">
      <div class="container">
        <div class="row">
          <!-- BEGIN BOTTOM ABOUT BLOCK -->
          <div class="col-md-3 col-sm-6 pre-footer-col">
            <h2>About us</h2>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam sit nonummy nibh euismod tincidunt ut
              laoreet dolore magna aliquarm erat sit volutpat. Nostrud exerci tation ullamcorper suscipit lobortis nisl
              aliquip commodo consequat. </p>
            <p>Duis autem vel eum iriure dolor vulputate velit esse molestie at dolore.</p>
          </div>
          <!-- END BOTTOM ABOUT BLOCK -->
          <!-- BEGIN BOTTOM INFO BLOCK -->
          <div class="col-md-3 col-sm-6 pre-footer-col">
            <h2>Information</h2>
            <ul class="list-unstyled">
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Delivery Information</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Customer Service</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Order Tracking</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Shipping &amp; Returns</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="contacts.php">Contact Us</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Careers</a></li>
              <li><i class="fa fa-angle-right"></i> <a href="javascript:;">Payment Methods</a></li>
              <li> <a href="/privacy-policy">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="col-md-3 col-sm-6 pre-footer-col">
            <h2>Our Contacts</h2>
            <address class="margin-bottom-40">
              Phone: +1 (904) 994-6224<br>
              Cory@Hangar-24.com<br>
              <a href="https://hangar-24.com/">hangar-24.com</a>
            </address>
          </div>
          <!-- END BOTTOM CONTACTS -->
        </div>
        <hr>
      </div>
    </div>


    <!-- END FOOTER -->
<script src="assets/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>      
    <script src="assets/corporate/scripts/back-to-top.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <script src="assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
    <script src="assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
    <script src='assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
    <script src="assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script><!-- Quantity -->
    <script src="assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="assets/plugins/rateit/src/jquery.rateit.js" type="text/javascript"></script>

    <script src="assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initTwitter();
            Layout.initImageZoom();
            Layout.initTouchspin();
            Layout.initUniform();
        });
    </script>
    <!-- END PAGE LEVEL JAVASCRIPTS -->
<script>
$(document).ready(function() {
    $("#reviewForm").submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: 'submit_review.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert("Review submitted successfully!");
                    location.reload(); // Refresh the page to show the new review
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
</script>

</body>
<!-- END BODY -->
</html>