<?php include 'track_page.php'; ?> 

<?php

require 'config.php'; // Include database connection

// Fetch all approved aircraft
$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$aircrafts = $stmt->fetchAll();

$stmt->execute();
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch aircraft images
$aircraftImages = [];
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_type = 'aircraft' ORDER BY sort_order ASC ");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($images as $image) {
    $aircraftImages[$image['product_id']][] = $image['image_url'];
}

// Fetch all approved engines
$stmt = $pdo->prepare("SELECT * FROM engines WHERE status = 'approved' ORDER BY created_at DESC  LIMIT 3");
$stmt->execute();
$engines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch engine images
$engineImages = [];
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_type = 'engine' ORDER BY sort_order ASC ");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($images as $image) {
    $engineImages[$image['product_id']][] = $image['image_url'];
}

$stmt = $pdo->prepare("SELECT * FROM parts WHERE status = 'approved' ORDER BY created_at DESC  LIMIT 3");
$stmt->execute();
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch part images
$partImages = [];
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_type = 'part' ORDER BY sort_order ASC");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($images as $image) {
    $partImages[$image['product_id']][] = $image['image_url'];
}



$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>






<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>flying411</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!-- SEO Meta -->
<meta name="description" content="Flying411 is a global aviation marketplace for aircraft sales, engines, and aircraft parts. Find listings for aircraft for sale, aviation services, and expert insights in one place.">
<meta name="keywords" content="aircraft for sale, aviation marketplace, buy aircraft, sell aircraft, aircraft engines, aviation parts, aviation maintenance, used aircraft, private jets, airplanes for sale, aircraft trading platform">
<meta name="author" content="Flying411">

<!-- Open Graph Meta (for Social Sharing) -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="New & Used Aircraft, Engines, and Aviation Parts for Sale">
<meta property="og:description" content="Discover premium aircraft, engines, and aviation parts on Flying411. Trusted platform for aviation professionals and aircraft traders.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png"> <!-- Replace with your actual image URL -->
<meta property="og:url" content="https://flying411.com">

<!-- Apple Touch Icon (iPhones, iPads) -->
<link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Android / Chrome -->
<link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Safari Pinned Tab (macOS) -->
<link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">

<!-- Web Manifest (optional for PWA support) -->
<!--<link rel="manifest" href="/site.webmanifest">-->


  <!-- Fonts START -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


   
  <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  
   
  <!-- Page level plugin styles START -->
  <!--<link href="assets/pages/css/animate.css" rel="stylesheet">-->
  <!--<link href="assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">-->
  <!--<link href="assets/plugins/owl.carousel/assets/owl.carousel.css" rel="stylesheet">-->
  <!-- Page level plugin styles END -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Theme styles START -->
  <!--<link href="assets/pages/css/components.css" rel="stylesheet">-->
  <!--<link href="assets/pages/css/slider.css" rel="stylesheet">-->
  <link href="assets/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="assets/corporate/css/style.css" rel="stylesheet">
  <link href="assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="assets/pages/css/home.css" rel="stylesheet" type="text/css">
  <!--<link href="assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">-->
  <!--<link href="assets/corporate/css/custom.css" rel="stylesheet">-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9EWL5C594"></script>

     
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9EWL5C594');
</script>
 <style>
       @media (max-width: 768px) {
  .header {
    display: none !important;
  }
}
/* Hide mobile navbar on desktop */
.mobile-header {
  display: none;
}

/* MOBILE ONLY */
@media (max-width: 768px) {

  .mobile-header {
    display: block;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 9999;
  }

  .mobile-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    background: #f5f6f7;
  }

  .mobile-menu-btn {
    background: none;
    border: none;
    font-size: 22px;
    color: #1b3a57;
  }

  .mobile-brand {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    color: #1b3a57;
  }

  .mobile-brand img {
    height: 50px;
    border-radius: 50% !important;
    
  }

  .mobile-login {
    background: #dbe5ec;
    color: #1b3a57;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
  }

  /* MENU PANEL */
  .mobile-menu {
    background: #f7f8fa;
    margin: 14px;
    border-radius: 14px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease;
  }

  .mobile-menu.open {
    max-height: 500px;
  }

  .mobile-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .mobile-menu ul li {
    border-bottom: 1px solid #e1e1e1;
  }

  .mobile-menu ul li:last-child {
    border-bottom: none;
  }

  .mobile-menu ul li a {
    display: block;
    padding: 14px;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    color: #1b3a57;
    text-decoration: none;
  }

  .mobile-cta {
    background: #4f86a8;
    color: #fff !important;
    margin: 14px;
    border-radius: 6px;
    font-weight: 700;
  }
}
 </style>
 
 <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8016514328655422"
     crossorigin="anonymous"></script>
 
</head> 
<!-- Head END -->

<!-- Body BEGIN -->

<body class="ecommerce">
  <div class="page-video margin-bottom-35">
    <div class="header" style="background-color: transparent !important;position: absolute; top:0px; width: 100%; border:none; color: white;">
      <div class="container-fluid headlogo" style="display: flex; justify-content: space-between; align-items: center;border: none; color: white !important;">
        <a class="site-logo" href="/"><img src="assets/corporate/img/Hangar-2-4-White-Final-1.png"
            alt="=Flying411"></a>
  
        <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>
  
  
  
        <!-- BEGIN NAVIGATION -->
       <div class="header-navigation">
        <ul>
             <li><a href="/">HOME</a></li>
    <li><a href="/parts">PARTS</a></li>
    <li><a href="/engine">ENGINES</a></li>
    <li><a href="/aircraft">AIRCRAFT</a></li>
            <li><a href="/services">Services</a></li>
    <li><a href="/contacts">CONTACT US</a></li>
       <li style="display:none;"><?php if (isset($_SESSION['user_id'])): ?>
    <!-- Show Dashboard link if user is logged in -->
    <a href="/client_dashboard" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">Dashboard</a>
  <?php else: ?>
    <!-- Show Login button if user is not logged in -->
    <a href="/login" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">LOGIN / REGISTER</a>
  <?php endif; ?></li>
    
  
          
          <!-- BEGIN TOP SEARCH -->
       
          <!-- END TOP SEARCH -->
        </ul>
      </div>
  
<div class="top-cart-block">
 <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Show Dashboard link if user is logged in -->
    <a href="/client_dashboard" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">Dashboard</a>
  <?php else: ?>
    <!-- Show Login button if user is not logged in -->
    <a href="/login" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">LOGIN / REGISTER</a>
  <?php endif; ?>
</div>
        <!--END CART -->
      </div>
    </div>
    <div class="mobile-header">
  <div class="mobile-topbar">
    <button class="mobile-menu-btn" id="mobileMenuBtn">
      <img src="assets/corporate/img/menu-bar.png" alt="menu-bar">
    </button>

    <div class="mobile-brand">
      <img  src="assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Logo">
      <span>FLYING411</span>
    </div>

    <a href="/login" class="mobile-login">LOGIN / REGISTER</a>
  </div>

  <!-- MOBILE MENU PANEL -->
  <div class="mobile-menu" id="mobileMenu">
    <ul>
      <li><a href="/">HOME</a></li>
      <li><a href="/parts">PARTS</a></li>
      <li><a href="/engine">ENGINES</a></li>
      <li><a href="/aircraft">AIRCRAFT</a></li>
      <li><a href="/services">SERVICES</a></li>
      <li><a href="/contacts" class="mobile-cta">CONTACT US</a></li>
    </ul>
  </div>
</div>
    <div class="video-container">
  <!-- Video Background -->
  <video autoplay muted loop playsinline class="video-bg">
    <source src="The Ultramodern Gulfstream Fleet (1).mp4" type="video/mp4">
  </video>

  <!-- Centered Text Content -->
  <div class="video-overlay text-center">
    <div class="search-wrapper relative mx-auto w-full max-w-xl">
      
      <span class="serchicon">
      <img src="assets/corporate/img/search.png" alt="search_icon">    
      </span>
      <input type="text" id="searchInput" class="search-input w-full border rounded" placeholder="" autocomplete="off">
      <div id="suggestionBox" class="absolute left-0 right-0 bg-blue-900 text-white rounded-xl shadow-lg mt-2 z-50 hidden max-h-80 overflow-y-auto">
        <ul id="suggestionList" class="space-y-2"></ul>
      </div>
    </div>
  </div>
</div>
  </div>
  <!-- END SLIDER -->





 <div class="featured-items">

    <h1>Featured Aircraft</h1>
    <div class="cards-container" id="aircraft-list">
        <?php foreach (array_slice($aircrafts, 0, 3) as $aircraft): ?>
        <?php
// Create a URL-safe slug from aircraft model
$slug = strtolower(str_replace([' ', '/'], '-', $aircraft['model']));

?>

            <div class="car-card">
                <?php if (!empty($aircraft['is_sold']) && $aircraft['is_sold'] == 1): ?> <img class="sold-ribbon" src="/assets/pages/img/icons/sold.png" alt="Sold"> <?php endif; ?>
                <div class="slider" data-current-slide="0">
                    
                    <span class="new-tag" style="font-size: 10px !important;">NEW</span>
                    <div class="favorite-icons">
                        <img src="assets/pages/img/icons/ribbon.png" width="25px" alt="icons">
                    </div>
                   <a href="/<?= urlencode($slug) ?>/<?= $aircraft['aircraft_id'] ?>">
                        <div class="slides">
                            <?php if (!empty($aircraftImages[$aircraft['aircraft_id']])): ?>
                                <?php foreach ($aircraftImages[$aircraft['aircraft_id']] as $image): ?>
                                    <?php
                                        $vendorPath = 'vendors/' . $image;
                                        $clientPath = 'clients/' . $image;
                                        $finalPath = file_exists($vendorPath) ? $vendorPath : (file_exists($clientPath) ? $clientPath : '');
                                    ?>
                                    <img src="<?= !empty($finalPath) ? '/' . $finalPath : '/assets/pages/img/products/default-aircraft.jpg' ?>" alt="Aircraft Image">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="/assets/pages/img/products/default-aircraft.jpg" alt="Default Aircraft Image">
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="arrows">
                        <span class="arrow-left"><img src="assets/pages/img/icons/left-chevron.png" alt="left-chevron" width="17px" alt=""></span>
                        <span class="arrow-right"><img src="assets/pages/img/icons/right-arrow-angle.png" alt="right-arrow-angle" width="17px" alt=""></span>
                    </div>
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
                <div class="car-info" style="text-align: left; font-size: 16px;">
                    <h2 style="margin:20px 0px 30px 15px; font-size: 23px !important; letter-spacing: 1.5px; color: #000; font-weight: 500;">
                        <?= htmlspecialchars($aircraft['model']) ?>
                    </h2>
                    <p><img src="assets/pages/img/icons/calendar (2).png" alt="clender">&nbsp;Year<span style="padding-left: 93px;"> <?= htmlspecialchars($aircraft['year']) ?></span></p>
                    <p><img src="assets/pages/img/icons/time-management (1).png" alt="time-management">&nbsp;Total Time <span style="padding-left: 40px;"> <?= htmlspecialchars($aircraft['total_time_hours']) ?> hours</span></p>
                    <p><img src="assets/pages/img/icons/engines.png" alt="engines">&nbsp;Engine <span style="padding-left: 70px;"><?= htmlspecialchars($aircraft['engine1_hours']) ?>&nbsp;(<?= htmlspecialchars($aircraft['engine1_status'])?>)</span></p>
                    <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;">
                                <?php if ($aircraft['price_label'] === 'call'): ?>

                                <h2 style="margin-top:50px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;text-transform: capitalize;">
                                     Call For Price
                                </h2>
                            
                            <?php elseif ($aircraft['price_label'] === 'make_offer'): ?>
                            
                                <h2 style="margin-top:50px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;text-transform: capitalize;">
                                    MAKE OFFER
                                </h2>
                            
                            <?php else: ?>
                            
                                <?= htmlspecialchars($aircraft['currency']) ?>
                                <?= number_format($aircraft['price'], 2) ?>
                            
                            <?php endif; ?>

                            </h2>
                    <p><img src="assets/pages/img/icons/location.png" alt="location">&nbsp;<?= htmlspecialchars($aircraft['location']) ?></p>
                     <div class="button-group">
                        <button onclick="window.location.href='/<?= $slug ?>/<?= $aircraft['aircraft_id'] ?>';">More Details</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="load-more" style="text-align: center !important; display:flex; justify-content: center;">
        <button class="load-more-btn" onclick="window.location.href='/aircraft'">View All</button>
    </div>
</div>
  
  
  
  
 <div class="featured-items">
    <h1>Featured Engines</h1>
    <div class="cards-container" id="engine-list">
        <?php foreach (array_slice($engines, 0, 3) as $engine): ?>
          <?php
$engine_slug = str_replace(' ', '_', $engine['model']); // Create slug like "Rotax_912"
?>

            <div class="car-card">
                <div class="slider" data-current-slide="0">
                    <span class="new-tag" style="font-size: 10px !important;">NEW</span>
                    <div class="favorite-icons">
                        <img src="assets/pages/img/icons/ribbon.png" width="25px" alt="ribbon">
                    </div>
                    <a href="/engines/<?= $engine_slug ?>/<?= $engine['engine_id'] ?>">
                        <div class="slides">
                            <?php if (!empty($engineImages[$engine['engine_id']])): ?>
                                <?php foreach ($engineImages[$engine['engine_id']] as $image): ?>
                                    <img src="vendors/<?= $image ?>" alt="Engine Image">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="/assets/pages/img/products/default-engine.png" alt="Default Engine Image">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="arrows">
                        <span class="arrow-left"><img src="assets/pages/img/icons/left-chevron.png" alt="left-chevron" width="17px" alt=""></span>
                        <span class="arrow-right"><img src="assets/pages/img/icons/right-arrow-angle.png" alt="right-arrow-angle" width="17px" alt=""></span>
                    </div>
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
                <div class="car-info" style="text-align: left; font-size: 16px;">
                    <h2 style="margin: 20px 0px 30px 15px; font-size: 23px !important;letter-spacing: 1.5px; color: #000; font-weight: 500;">
                        <?= htmlspecialchars($engine['model']) ?>
                    </h2>
                    <p><img src="assets/pages/img/icons/calendar (2).png" alt="calendar">&nbsp;Year<span style="padding-left: 110px;"><?= htmlspecialchars($engine['year']) ?></span></p>
                    <p><img src="assets/pages/img/icons/time-management (1).png" alt="time-management">&nbsp;Total Time<span style="padding-left: 60px;"><?= htmlspecialchars($engine['total_time_hours']) ?> hours</span></p>
                    <p><img src="assets/pages/img/icons/time-management (1).png" alt="time-management">&nbsp;HR  <span style="padding-left: 107px;"><?= htmlspecialchars($engine['hr']) ?> hours</span></p>
                    <p><img src="assets/pages/img/icons/time-management (1).png" alt="total_time_hours">&nbsp;CR <span style="padding-left: 109px;"><?= htmlspecialchars($engine['cycles']) ?> cycles</span></p>
                    <p><img src="assets/pages/img/icons/settings.png" alt="Condition">&nbsp;Condition <span style="padding-left: 60px;"><?= htmlspecialchars($engine['condition']) ?></span></p>
                    
                    <?php if ($engine['price_label'] === 'call'): ?>
                                
                                    <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;text-transform: capitalize;">
                                        Call For Price
                                    </h2>
                                
                                <?php else: ?>
                                
                                    <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;">
                                        <?= htmlspecialchars($engine['currency']) ?>
                                        <?= number_format($engine['price'], 2) ?>
                                    </h2>
                                
                                <?php endif; ?>
                     <div class="button-group">
    <button onclick="window.location.href='/engines/<?= $engine_slug ?>/<?= $engine['engine_id'] ?>';">More Details</button>
</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="load-more" style="text-align: center !important; display:flex; justify-content: center;justify-content:center;">
               <button class="load-more-btn" onclick="window.location.href='/engine'">View All</button>
    </div>
</div>


 
    <div class="featured-items">
        <h1>Featured Parts</h1>
    <div class="cards-container" id="part-list">
        <?php foreach (array_slice($parts, 0, 3) as $part): ?>
          <?php
$part_slug = strtolower($part['part_name']);
$part_slug = preg_replace('/[^a-z0-9]+/', '_', $part_slug);
$part_slug = trim($part_slug, '_');
 // Slug example: Engine_Filter
?>
            <div class="car-card">
                <div class="slider" data-current-slide="0">
                    <span class="new-tag" style="font-size: 10px !important;">NEW</span>
                    <div class="favorite-icons">
                        <img src="assets/pages/img/icons/ribbon.png" width="25px" alt="ribbon">
                    </div>
                   <a href='/parts/<?= $part_slug ?>/<?= $part['part_id'] ?>';>
                        <div class="slides">
                            <?php if (!empty($partImages[$part['part_id']])): ?>
                                <?php foreach ($partImages[$part['part_id']] as $image): ?>
                                    <img src="vendors/<?= $image ?>" alt="Part Image">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="/assets/pages/img/products/default-part.png" alt="Default Part Image">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="arrows">
                        <span class="arrow-left"><img src="assets/pages/img/icons/left-chevron.png" alt="left-chevron" width="17px" alt=""></span>
                        <span class="arrow-right"><img src="assets/pages/img/icons/right-arrow-angle.png" alt="right-arrow-angle" width="17px" alt=""></span>
                    </div>
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
                <div class="car-info" style="text-align: left; font-size: 16px;">
                    <h2 style="margin:20px 0px 30px 15px; font-size: 23px !important;letter-spacing: 1.5px; color: #000; font-weight: 500;">
                        <?= htmlspecialchars($part['part_name']) ?>
                    </h2>
                    <p><img src="assets/pages/img/icons/license-plate.png" alt="license-plate">&nbsp;Part Number <span style="padding-left: 60px;"> <?= htmlspecialchars($part['part_number']) ?> </span></p>
                    <p><img src="assets/pages/img/icons/settings.png" alt="settings">&nbsp;Condition <span style="padding-left: 79px;"> <?= htmlspecialchars($part['condition']) ?> </span></p>
                    <p><img src="assets/pages/img/icons/shopping.png" alt="shopping">&nbsp;Tag <span style="padding-left: 130px;"> <?= htmlspecialchars($part['tagged_with_easa_form_1']) ?> </span></p>
                    
                                <?php if ($part['price_label'] === 'call'): ?>
                                
                                    <h2 style="margin-top:50px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;text-transform: capitalize;">
                                        Call For Price
                                    </h2>
                                
                                <?php else: ?>
                                
                                    <h2 style="margin-top:50px;margin-left:15px;font-size:30px;color:#000;font-weight:500;">
                                        <?= htmlspecialchars($part['currency']) ?>
                                        <?= number_format($part['price'], 2) ?>
                                    </h2>
                                
                                <?php endif; ?>

                      <div class="button-group">
    <button onclick="window.location.href='/parts/<?= $part_slug ?>/<?= $part['part_id'] ?>';">More Details</button>
</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="load-more" style="text-align: center !important; display:flex; justify-content: center;">
      <button class="load-more-btn" onclick="window.location.href='/parts'">View All</button>
    </div>
</div>


  </div>



 <div class="container my-5">
  <h2 class="text-center mb-4" style="font-size: 40px;">Our Latest Blogs</h2>

  <!-- Multi-Item Blog Carousel -->
 <div id="blogCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <!-- JS will insert slides here -->
  </div>

  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#blogCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#blogCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
 <button class="load-more-btn blogbtn"  onclick="window.location.href='/blogs'">View All Blogs</button>
</div>




  <!-- BEGIN PRE-FOOTER -->
 <div class="pre-footer" style="margin-top:50px">
    <div class="container">
      <div class="row">
        <!-- BEGIN BOTTOM ABOUT BLOCK -->
        <div class="col-md-3 col-sm-6 pre-footer-col">
          <h2>About us</h2>
          <p>Welcome to Flying411 your trusted platform for buying, selling, and exploring aircraft.
             With a passion for aviation and a commitment to excellence, we connect aircraft buyers and sellers from around the world. Whether you're looking to purchase a private jet, sell your aircraft, or explore aviation services, we provide a seamless and secure experience tailored to your needs.</p>
        </div>
        <!-- END BOTTOM ABOUT BLOCK -->
        <!-- BEGIN BOTTOM INFO BLOCK -->
        <div class="col-md-3 col-sm-6 pre-footer-col first-col">
          <h2>Quick Links</h2>
          <ul class="list-unstyled">
            <li> <a href="/">Home</a></li>
            <li> <a href="/services">Services</a></li>
            <li> <a href="/contacts">Contact Us</a></li>
            <li> <a href="/blogs">Articles</a></li>
            <li> <a href="/privacy-policy">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="col-md-3 col-sm-6 pre-footer-col">
          <h2>Products</h2>
          <ul class="list-unstyled">
            <li><a href="/aircraft">Aircrafts</a></li>
            <li><a href="/engine">Engines</a></li>
            <li><a href="/parts">Parts</a></li>
          </ul>
        </div>
        <div class="col-md-3 col-sm-6 pre-footer-col">
          <h2>Our Contacts</h2>
          <address class="margin-bottom-40">
            Phone: +1 (904) 994-6224<br>
            Cory@Flying411.com<br>
            <a href="https://flying411.com/">Flying411.com</a>
          </address>
        </div>
        <!-- END BOTTOM CONTACTS -->
      </div>
      <hr>
    </div>
  </div>


  <!-- BEGIN fast view of a product -->
 
  <!-- END fast view of a product -->

  <!-- Load javascripts at bottom, this will reduce page load time -->
  <!-- BEGIN CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
  <!--[if lt IE 9]>
    <script src="assets/plugins/respond.min.js"></script>  
    <![endif]-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/jquery.min.js" type="text/javascript"></script>
  <!--<script src="assets/plugins/jquery-migrate.min.js" type="text/javascript"></script>-->
  <!--<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>-->
  <!--<script src="assets/corporate/scripts/back-to-top.js" type="text/javascript"></script>-->
  <!--<script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>-->
  <!-- END CORE PLUGINS -->

  <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
  <!--<script src="assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
  <!--<script src="assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script>-->
  <!-- slider for products -->
  <!--<script src='assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
  <!--<script src="assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>-->
  <!-- Quantity -->

  <script src="assets/corporate/scripts/layout.js" type="text/javascript"></script>
  <script src="assets/pages/scripts/bs-carousel.js" type="text/javascript"></script>
  <script type="text/javascript">
    jQuery(document).ready(function () {
      Layout.init();
    //   Layout.initOWL();
    //   Layout.initImageZoom();
    //   Layout.initTouchspin();
    //   Layout.initTwitter();
    });
  </script>
  <!-- END PAGE LEVEL JAVASCRIPTS -->
  <script>
  document.querySelectorAll('.car-card .slider').forEach((slider) => {
          const slidesContainer = slider.querySelector('.slides');
          const leftArrow = slider.querySelector('.arrow-left');
          const rightArrow = slider.querySelector('.arrow-right');
          const dots = slider.querySelectorAll('.dot');
          const slides = slidesContainer.children;
          const totalSlides = slides.length;
          let currentSlide = 0;
      
          function updateSlide() {
              // Calculate the correct width of a single slide
              const slideWidth = slider.clientWidth;
              slidesContainer.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
              
              // Update dots to reflect the active slide
              dots.forEach((dot, index) => {
                  dot.classList.toggle('active', index === currentSlide);
              });
          }
      
          leftArrow.addEventListener('click', () => {
              currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
              updateSlide();
          });
      
          rightArrow.addEventListener('click', () => {
              currentSlide = (currentSlide + 1) % totalSlides;
              updateSlide();
          });
      
          dots.forEach((dot, index) => {
              dot.addEventListener('click', () => {
                  currentSlide = index;
                  updateSlide();
              });
          });
      
          // Ensure slides have the correct width on page load
          window.addEventListener('resize', updateSlide);
          updateSlide(); // Initial update
      });

   
   </script>
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const carouselInner = document.querySelector(".carousel-inner");

  // This should be generated by PHP and injected as JSON
  const blogData = <?php echo json_encode($articles); ?>;

  function createCard(article) {
    return `
      <div class="col-md-4 mb-4">
        <div class="card blog-card h-100">
          <img src="admin/uploads/${article.image}" class="card-img-top" style="width:100%;height:250px;object-fit:cover;" alt="${article.title}">
          <div class="card-body d-flex flex-column">
            <h5 class="blog-card-title">${article.title}</h5>
            <a href="/blog/${encodeURIComponent(article.slug)}/${article.id}"
              class="btn btn-sm mt-auto"
              style="background-color: #fff; color: black; border: 1px solid #666; padding: 8px 50px; font-size: 12px; font-weight: 400; border-radius: 4px;">
              Read More
            </a>
          </div>
        </div>
      </div>
    `;
  }

  function updateCarousel() {
    const width = window.innerWidth;
    let itemsPerSlide = 3;
    if (width < 576) itemsPerSlide = 1;
    else if (width < 993) itemsPerSlide = 3;

    carouselInner.innerHTML = ""; // Clear existing

    for (let i = 0; i < blogData.length; i += itemsPerSlide) {
      const isActive = i === 0 ? 'active' : '';
      let slideContent = '<div class="row">';
      for (let j = i; j < i + itemsPerSlide && j < blogData.length; j++) {
        slideContent += createCard(blogData[j]);
      }
      slideContent += '</div>';

      const item = document.createElement("div");
      item.className = `carousel-item ${isActive}`;
      item.innerHTML = slideContent;
      carouselInner.appendChild(item);
    }
  }

  updateCarousel();
  window.addEventListener("resize", updateCarousel);
});
</script>

<script>
$(document).ready(function() {
  function toggleLoginButton() {
    if ($(window).width() <= 768) {
      $("li:last-child").show();
    } else {
      $("li:last-child").hide();
    }
  }

  toggleLoginButton();
  $(window).resize(toggleLoginButton);
});
</script>

 <script>
$(document).ready(function() {
    function toggleLoginButton() {
        if ($(window).width() <= 768) { // Mobile screen (adjust breakpoint as needed)
            $("li:last-child").show(); // Show login button on mobile
        } else {
            $("li:last-child").hide(); // Hide login button on larger screens
        }
    }

    // Run on page load
    toggleLoginButton();

    // Run on window resize
    $(window).resize(function() {
        toggleLoginButton();
    });
});


 </script>
<script>
  const searchInput = document.getElementById('searchInput');
  const suggestionBox = document.getElementById('suggestionBox');
  const suggestionList = document.getElementById('suggestionList');

  const placeholders = ["Aircraft", "Parts", "Engines", "Services"];
  let placeholderIndex = 0;

  function rotatePlaceholder() {
    searchInput.setAttribute("placeholder", `Search for ${placeholders[placeholderIndex]}`);
    placeholderIndex = (placeholderIndex + 1) % placeholders.length;
  }
  rotatePlaceholder();
  setInterval(rotatePlaceholder, 2500);

  // Handle input event
  searchInput.addEventListener('input', async () => {
    const query = searchInput.value.trim();
    if (!query) {
      suggestionBox.classList.add('hidden');
      suggestionList.innerHTML = '';
      return;
    }

    const response = await fetch(`search_suggestions.php?q=${encodeURIComponent(query)}`);
    const results = await response.json();

    let suggestions = '';


    if (results.length === 0) {
      suggestions += '<li class="text-gray-300 px-4 py-2">No results found</li>';
    } else {
      suggestions += results.map(item => {
        let tagClass = '';
            switch (item.type) {
              case 'aircraft': tagClass = 'tag-aircraft'; break;
              case 'engine': tagClass = 'tag-engine'; break;
              case 'part': tagClass = 'tag-part'; break;
              case 'service': tagClass = 'tag-service'; break;
              case 'part-info': tagClass = 'part-info'; break;
              default: tagClass = 'type-tag'; // fallback
            }


        // Handle URL fallback if not returned from PHP (optional, here PHP provides URL only for aircraft)
               // Handle URL fallback if not returned from PHP (optional, here PHP provides URL only for aircraft)
        let url = item.url;
        if (!url) {
          if (item.type === 'engine') {
            url = `/engine?model=${encodeURIComponent(item.name)}`;
          } else if (item.type === 'part') {
            url = `/parts?name=${encodeURIComponent(item.name)}`;
          } else {
            url = '#';
          }
        }


        return `
          <li class="suggestion-item" data-url="${url}">
            <div class="flex space-x-2">
              <span class="type-tag ${tagClass} text-white px-2 py-1 rounded text-xs">${item.type}</span>
              <span>${item.name}</span>
            </div>
          </li>
        `;
      }).join('');
    }

    suggestionList.innerHTML = suggestions;
    suggestionBox.classList.remove('hidden');
  });

  // ✅ Handle click on suggestions
  suggestionList.addEventListener('click', (e) => {
    const target = e.target.closest('li');
    if (!target) return;

    const url = target.getAttribute('data-url');
    if (url && url !== '#') {
      window.location.href = url;
    }
  });

  // ✅ Hide suggestions on outside click
  document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
      suggestionBox.classList.add('hidden');
    }
  });

  // ✅ Enter key triggers universal search
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();

      const query = searchInput.value.trim();
      if (!query) return;

      suggestionBox.classList.add('hidden');

      // Default: universal aircraft model search
      window.location.href = `/aircraft?model=${encodeURIComponent(query)}`;
    }
  });
</script>

<script>
document.getElementById("mobileMenuBtn").addEventListener("click", function () {
  document.getElementById("mobileMenu").classList.toggle("open");
});
</script>
<script>
let startTime = Date.now();

window.addEventListener("pagehide", function () {
    let timeSpent = Math.round((Date.now() - startTime) / 1000);

    console.log("⏱ Time:", timeSpent, "Token:", window.PAGE_TOKEN);

    navigator.sendBeacon(
        "/time_update",
        JSON.stringify({
            time: timeSpent,
            token: window.PAGE_TOKEN
        })
    );
});
</script>




 
</body>
<!-- END BODY -->

</html><!-- CI/CD Test -->
