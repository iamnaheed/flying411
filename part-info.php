<?php include 'track_page.php'; ?>
<?php
require 'config.php';

// --- Input Validation ---
$part_number = $_GET['part_number'] ?? '';
if (!$part_number) {
    header("HTTP/1.0 400 Bad Request");
    exit("Invalid Part Number.");
}

$part = null;
$images = [];

// --- Fetch part details from parts_master ---
try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM parts_master
        WHERE part_number = :part_number
          AND status = 'active'
        LIMIT 1
    ");
    $stmt->execute(['part_number' => $part_number]);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$part) {
        header("HTTP/1.0 404 Not Found");
        exit("Part not found.");
    }

    // --- Images ---
    if (!empty($part['image_src'])) {
        // Split by semicolon
        $images = array_map('trim', explode(';', $part['image_src']));
        // Keep only valid URLs
        $images = array_filter($images, function($url){
            return preg_match('/^https?:\/\//i', $url);
        });
    }

    if (empty($images)) {
        $images = ["https://flying411.com/assets/pages/img/products/default.png"];
    }

    // Main image
    $mainImage = $images[0];

    // SEO URL and short description
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $part['part_name']), '-'));
    $seoUrl = "https://flying411.com/parts/$slug/" . urlencode($part['part_number']);
    $shortDesc = !empty($part['description'])
        ? htmlspecialchars(substr(strip_tags($part['description']), 0, 160))
        : "Aircraft part details, specifications, documents, and availability.";

} catch (PDOException $e) {
    error_log("Database error (part_number $part_number): " . $e->getMessage());
    header("HTTP/1.0 500 Internal Server Error");
    exit("Something went wrong, please try again later.");
}

// --- Alternate Parts (same part_name, different part_number) ---
$alternateParts = [];
try {
    $stmt = $pdo->prepare("
        SELECT part_number
        FROM parts_master
        WHERE part_number != :part_number
          AND part_name = (SELECT part_name FROM parts_master WHERE part_number = :part_number LIMIT 1)
          AND status = 'active'
        LIMIT 5
    ");
    $stmt->execute(['part_number' => $part_number]);
    $alternateParts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching alternate parts: ' . $e->getMessage());
}

// --- Related Parts (same part_type) ---
$relatedParts = [];

if (!empty($part['part_type'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT part_number, part_name, image_src
            FROM parts_master
            WHERE part_number != :part_number
              AND status = 'active'
              AND part_type = :part_type
            ORDER BY part_name ASC
        ");
        $stmt->execute([
            'part_number' => $part_number,
            'part_type' => $part['part_type']
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Deduplicate by part_number
        $uniqueParts = [];
        foreach ($rows as $r) {
            if (!isset($uniqueParts[$r['part_number']])) {
                // Prepare main image
                if (!empty($r['image_src'])) {
                    $imgs = array_map('trim', explode(';', $r['image_src']));
                    $r['main_image'] = $imgs[0];
                } else {
                    $r['main_image'] = "https://flying411.com/assets/pages/img/products/default.jpeg";
                }
                $uniqueParts[$r['part_number']] = $r;
            }
        }

        // Keep only the first 3 related parts
        $relatedParts = array_slice(array_values($uniqueParts), 0, 3);

    } catch (PDOException $e) {
        error_log('Error fetching related parts: ' . $e->getMessage());
    }
}

?>

<!-- =================== HTML OUTPUT =================== -->


<!-- ========================== -->
<!-- PART-INFO PAGE HTML -->
<!-- ========================== -->






<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($part['part_name']) ?> | Aircraft Part Details - Flying411</title>

    <link rel="canonical" href="<?= $seoUrl ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <!-- SEO Description -->
    <meta name="description" content="<?= $shortDesc ?>">

    <!-- SEO Keywords -->
    <meta name="keywords" content="
        aircraft parts,
        <?= htmlspecialchars($part['part_name']) ?>,
        <?= htmlspecialchars($part['part_number']) ?>,
        <?= htmlspecialchars($part['type']) ?>,
        <?= htmlspecialchars($part['condition']) ?>,
        <?= htmlspecialchars($part['region']) ?> aerospace parts,
        aviation spare parts,
        aircraft component marketplace
    ">

    <meta name="author" content="Flying411">

    <!-- Open Graph -->
    <meta property="og:site_name" content="Flying411">
    <meta property="og:title" content="<?= htmlspecialchars($part['part_name']) ?> - Aircraft Part Details">
    <meta property="og:description" content="<?= $shortDesc ?>">
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?= $seoUrl ?>">
    <meta property="og:image" content="<?= $mainImage ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($part['part_name']) ?> - Aircraft Part Details">
    <meta name="twitter:description" content="<?= $shortDesc ?>">
    <meta name="twitter:image" content="<?= $mainImage ?>">

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
    <link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">
    
<!-- Apple Touch Icon (iPhones, iPads) -->
<link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Android / Chrome -->
<link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Safari Pinned Tab (macOS) -->
<link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">


  <!-- Fonts START -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <!--<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">-->
  <!--<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <!--<link href="assets/pages/css/animate.css" rel="stylesheet">-->
  <!--<link href="assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">-->
  <!--<link href="assets/plugins/owl.carousel/assets/owl.carousel.css" rel="stylesheet">-->
  <!-- Page level plugin styles END -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <!--<link href="/assets/pages/css/animate.css" rel="stylesheet">-->
  <!--<link href="/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">-->
  <!--<link href="/assets/plugins/owl.carousel//assets/owl.carousel.css" rel="stylesheet">-->
  <!-- Page level plugin styles END -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Theme styles START -->
  <link href="/assets/pages/css/components.css" rel="stylesheet">
  <link href="/assets/pages/css/slider.css" rel="stylesheet">
  <link href="/assets/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="/assets/corporate/css/style.css" rel="stylesheet">
  <link href="/assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="/assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">
  <link href="/assets/corporate/css/custom.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
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
*{
  margin: 0;
 
   font-family: "EB Garamond", serif !important;
}
h1,h2,h3,h4,h5,h6{

   font-family: "EB Garamond", serif  !important;
}

         .top-bars {
            display: flex;
            justify-content: space-between;
            /* padding: 10px 20px; */
            color: black;
            width:80%;
            font-size: 16px;
            margin:auto;

        }
        .top-bars a {
            color: black;
            text-decoration: none;
            font-size:16px;
        }
        .main {
            /*width:86%;*/
            margin: auto;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            /* padding: 0px 20 ; */
        }
        .containers {
            width: 60%;
             background-color: #ffffff; 
            border-radius: 20px !important;
             padding: 20px; 
           box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }
        .sideheader {
            width:100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sideheader h1 {
            margin: 0;
            font-size: 24px;
            color:black;
              font-family: "EB Garamond", serif !important;
        }
        .sideheader .price {
            font-size: 24px;
            font-weight: bold;
            color: black;
        }
        .image-section {
            /* width:60%; */
            margin-top: 10px;
        }
        #main-image {
            width: 100%;
            height:550px !important;
            border-radius: 8px;
        }
        .thumbnail-row {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            margin: 10px auto;
            justify-content:center;
        }
        .thumbnail-row img {
            width: 100px;
            height: 80px;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .thumbnail-row img:hover {
            transform: scale(1.1);
        }
        .dropdown {
            margin-top: 20px;
        }
        .dropdown h3 {
            margin: 10px 0;
        }
        .sidebar {
            width: 35%;
            background-color: #fff;
            border-radius: 20px !important;
            padding: 20px;
            
                        box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }
        .sidebar button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border:1px solid #666;
            background-color: white;
            color: black;
            cursor: pointer;
            border-radius: 8px !important;
            font-size: 20px;
            
        }
        .sidebar button:hover {
            background-color: lightgray;
            color:black;
            border: 1px solid black;

        }
        .sidebar .details-table {
            width: 100%;
            border-collapse: collapse;
            color:black;
        }
       
        .sidebar .details-table th,
        .sidebar .details-table td {
            border-bottom: 1px solid #ddd;
            padding: 18px  5px;
            letter-spacing: 1px;
            text-align: left;
            /*background-color: #e7e9eb;*/
        }
                .sidebar .details-table th{
                    font-weight: 500;
                }
.details-section {
  width: 100%;
  margin-top: 20px;
  font-size: 16px;
  color: #333;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 10px;
  border-bottom: 1px solid #ddd;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: bold;
  text-transform: uppercase;
}

.detail-value {
  font-size: 16px;
  color: #555;
}

.expandable {
  cursor: pointer;
}

.expand-icon {
  font-weight: bold;
  color: #888;
  font-size: 18px;
}
.header .mobi-toggler{
    margin:0px !important;
}


/* Add custom styles to the chat window */
#chatWindow {
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

#chatMessages {
    padding: 10px;
    max-height: 280px;
    overflow-y: auto;
}

#chatInput {
    width: 100%;
    padding: 11px;
    border-radius: 5px;
}

button {
    cursor: pointer;
    padding: 10px;
    border-radius: 5px;
}

.sidebar .details-table tr:nth-child(odd) {
  background-color: #ffffff; /* White color for odd rows */
}

.sidebar .details-table tr:nth-child(even) {
  background-color: #e7e9eb; /* Light gray for even rows */
}
.image-section {
    position: relative;
}

.main-image-wrapper {
    position: relative;
}

#main-image {
    width: 100%;
    height: 400px !important;
    display: block;
}

.arrows {
    position: absolute;
    top: 50%;
    width: 100%;
    display: none;
    justify-content: space-between;
    transform: translateY(-50%);
}

.arrow-left, .arrow-right {
    background-color: black;
    color: #fff;
    font-size: 24px;
    padding: 10px;
    cursor: pointer;
    border-radius: 30px;
}

.thumbnail-row {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.thumbnail-row img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    cursor: pointer;
}

.image-section:hover .arrows {
    display: flex;
}
  /* Left Section (Table) */
        .left-section {
            flex: 1;
            /*min-width: 300px;*/
            /*background: #f9f9f9;*/
            padding: 10px;
            border-radius: 20px !important;
            font-size: 16px;
            border: 1px solid #ddd;
            background-color: white;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }

        /* Right Section (Description) */
        .right-section {
            flex: 1;
            min-width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 20px !important;
            border: 1px solid #ddd;
           box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }

        .right-section h2 {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
        }

     .left-section  .details-table {
            width: 100%;
            border-collapse: collapse;
            color:black;
        }
       
        .left-section  .details-table th ,td {
            border-bottom: 1px solid #ddd;
            padding: 17px;
            letter-spacing: 1px;
            text-align: left;
            /*background-color: #e7e9eb;*/
        }
         .left-section  .details-table th{
                    font-weight: 500;
                }
                
                .left-section .details-table tr:nth-child(odd) {
  background-color: #ffffff; /* White color for odd rows */
}

 .left-section .details-table tr:nth-child(even) {
  background-color: #e7e9eb; /* Light gray for even rows */
}
.popup-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: #fff;
    padding: 20px;
    width: 70%;
    max-width: 800px;
    border-radius: 8px;
    position: relative;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
}
 .site-logo img{
        border-radius: 50% !important;
        margin-right: 110px !important;
    }
    .header .top-cart-block a:hover{
    background-color: white!important;
    color: black !important; 
    border: 1px solid black !important;
}
.header{
    margin-top:20px !important;
}
#document-list{
    display:none;
}
@media (max-width: 1200px) {
    .container, .container-lg, .container-md, .container-sm {
        max-width: 990px !important;
    }
   
     .site-logo img{
        margin-right:0px !important;
   }
   .ecommerce .sidebar {
    font: 10px !important; 
}
}

.call-label {
    font-weight: 400;
}

.obo-label {
    font-weight: 400 !important;
    /*padding-left:5px;*/
}

.starting-bid-label {
    font-weight: 400 !important;
    /*padding-right:5px;*/
}
@media (max-width: 960px) {
    .main {
        width: 95%;
        padding: 10px;
    }
    .container{
        width: 95%;
        
    }
}
@media (max-width: 768px) {
    .top-bars {
        flex-direction: column;
        text-align: center;
    }
    .main {
        flex-direction: column;
        width: 95%;
        padding: 10px;
    }
    .container{
        width: 95%;
        
    }
    .containers, .sidebar {
        width: 100%;
        margin-bottom: 20px;
    }
    .sideheader h1, .sideheader .price {
        font-size: 20px;
    }
    .thumbnail-row img {
        width: 80px;
        height: 60px;
    }
    .sidebar button {
        font-size: 18px;
    }
    .details-section {
        font-size: 14px;
    }
    .detail-label {
        font-size: 14px;
    }
    .detail-value {
        font-size: 14px;
    }
    .ecommerce .header .mobi-toggler{
        margin:0px !important;
    }
}
@media (max-width: 600px) {

#main-image {
    width: 100%;
    height: 300px !important;
    display: block;
}
}















/* Container Layout */
.main {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin: 20px auto;
  padding: 0px;
}

/* Sidebar + main content */
.containers {
  min-width: 300px;
}

.sidebar {
  flex: 1;
  min-width: 280px;
  /*background: #f9f9f9;*/
  padding: 20px;
  /*border-radius: 12px;*/
  /*box-shadow: 0 4px 10px rgba(0,0,0,0.08);*/
}

/* Sideheader */
.sideheader h1 {
  margin-bottom: 10px;
}

/* Price style */
.price {
  font-size: 20px;
  color: #222;
}

/* IMAGE SECTION */
.image-section {
  margin-top: 20px;
}

.main-image-wrapper {
  position: relative;
  max-width: 100%;
  border-radius: 12px;
  overflow: hidden;
}

#main-image {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: contain;  /* Prevents stretching */
  display: block;
  margin: 0 auto;
}

/* Arrows */
.arrows {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 15px;
  transform: translateY(-50%);
}

.arrows span {
  background: rgba(0,0,0,0.5);
  color: #fff;
  padding: 8px 12px;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.3s;
}
.arrows span:hover {
  background: rgba(0,0,0,0.8);
}

/* Thumbnails */
.thumbnail-row {
  display: flex;
  gap: 10px;
  margin-top: 12px;
  overflow-x: auto;
  padding-bottom: 5px;
}

.thumbnail-row img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: 0.3s;
}

.thumbnail-row img:hover {
  border: 2px solid #007BFF;
}

/* Details Table */
/*.details-table {*/
/*  width: 100%;*/
/*  margin-top: 20px;*/
/*  border-collapse: separate;*/
/*  border-spacing: 0 8px;*/
/*}*/

/*.details-table th {*/
/*  text-align: left;*/
/*  font-weight: 600;*/
/*  padding: 8px 10px;*/
/*  background: #f0f0f0;*/
/*  border-radius: 6px 0 0 6px;*/
/*}*/

/*.details-table td {*/
/*  padding: 8px 10px;*/
/*  background: #fff;*/
/*  border-radius: 0 6px 6px 0;*/
/*  box-shadow: 0 2px 5px rgba(0,0,0,0.05);*/
/*}*/

/* Responsive */
.col-md-6{
    padding:0px;
}

@media (max-width: 1200px) {
   .ecommerce .sidebar {
    font-size: 12px !important; 
}
}
.header-navigation ul li a:hover{
    background: none !important;
}
 
 @media (max-width: 768px) {
  .main {
    flex-direction: column;
  }
  .sidebar {
    order: -1; /* Move sidebar above images if needed */
  }
  #main-image {
    max-height: 350px;
  }
}


.btn {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    /* --- Modal Overlay --- */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }


/* Modal layout styling */
.modal-content {
  max-width: 600px;
  margin: 0 auto;
  background: #fff;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.modal-content h3 {
  text-align: center;
  margin-bottom: 20px;
}

.form-row {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.form-row input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

select, textarea, button {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

button {
  background-color: #007BFF;
  color: #fff;
  cursor: pointer;
  border: none;
  transition: background 0.3s;
}

button:hover {
  background-color: #0056b3;
}

/* Popup message styling */
.popup-message {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #4CAF50;
  color: white;
  padding: 20px 30px;
  border-radius: 8px;
  font-size: 18px;
  z-index: 9999;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Responsive for mobile */
@media (max-width: 600px) {
  .form-row {
    flex-direction: column;
  }
}
    
    
    /* --- Product & Related Images --- */
.related-card img,
.alt-card img {
  height: 104px;              /* Fixed height for uniform cards */
  object-fit: cover;          /* Ensures image fills the box neatly */
  object-position: center;    /* Centers the image */
  border-radius: 8px;         /* Smooth corners */
  transition: transform 0.3s ease;
}

.related-card:hover img,
.alt-card:hover img {
  transform: scale(1.03);     /* Slight zoom on hover for a modern effect */
}

.related-card,
.alt-card {
  border-radius: 10px;
  background: #fff;
  /*box-shadow: 0 2px 6px rgba(0,0,0,0.1);*/
  padding: 10px;
  transition: all 0.3s ease;
}

.related-card:hover,
.alt-card:hover {
  transform: translateY(-3px);
  /*box-shadow: 0 4px 12px rgba(0,0,0,0.15);*/
}

/* --- Responsive tweaks --- */
@media (max-width: 768px) {
  .related-card img,
  .alt-card img {
    height: 140px;           /* Slightly smaller on mobile */
  }
}

.details-table tr th{
    width: 70% !important;
}
.aircraft-bubbles {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.aircraft-bubble {
    background-color: #ededed;   /* light blue */
    color: #3e4d5c;              /* blue text */
    padding: 6px 12px;
    border-radius: 999px;        /* pill shape */
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
}


  
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

.main{
    margin-top:100px ;
}
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
</head>
<!-- Head END -->

<!-- Body BEGIN -->
<body class="ecommerce">
  <!-- BEGIN TOP BAR -->

  <!-- END TOP BAR -->

  <!-- BEGIN HEADER -->
  <div class="header" >
    <div class="container headlogo" style="display: flex; justify-content: space-between; align-items: center;flex-direction:row;">
      <a class="site-logo" style="padding: 0px; margin:0px;" href="/"><img src="/assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Metronic Shop UI"></a>

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
       <li style="display:none;"> <?php if (isset($_SESSION['user_id'])): ?>
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
      <!-- END NAVIGATION -->
       <!-- BEGIN CART -->
     
      <!--END CART -->
    </div>
  </div>
   <div class="mobile-header">
  <div class="mobile-topbar">
    <button class="mobile-menu-btn" id="mobileMenuBtn" style="width:auto;">
      <img src="/assets/corporate/img/menu-bar.png" alt="menu-bar">
    </button>

    <div class="mobile-brand">
      <img  src="/assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Logo">
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
    <!-- Header END -->
    </div>

    <!--<div class="top-bars" style="margin-top:50px; margin-bottom:20px;">-->
    <!--<a href="javascript:void(0)" onclick="window.history.back()">Back to Search</a>-->
    <!--</div>-->


<div class="main container">
    <div class="containers">
        <div class="sideheader">
            <h1 style="padding: 10px 0px; font-size:25px; font-weight:500; color:black;">
                <?= htmlspecialchars($part['part_name']) ?>
            </h1>
        </div>

        <div class="image-section">
            <div class="main-image-wrapper">
               <img src="<?= htmlspecialchars($mainImage) ?>" alt="Product Image" id="main-image">
                <div class="arrows">
                    <span class="arrow-left" onclick="changeImage(-1)">&#8592;</span>
                    <span class="arrow-right" onclick="changeImage(1)">&#8594;</span>
                </div>
            </div>
            <div class="thumbnail-row">
                <?php foreach ($images as $img) : ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="Thumbnail" onclick="changeMainImage('<?= htmlspecialchars($img) ?>')">
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="sidebar">
        <a href="/parts" style="text-decoration: none;">
            <button style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Search Inventory</button>
        </a>
         <a href="#" style="text-decoration: none;">
        <button style="padding: 10px 20px; font-size: 16px; cursor: pointer;" id="openQuote">Request for Quote</button>
    </a>

        <h1 style="font-size:25px;margin-top:12px; font-weight:500; color:black;">Technical Specifications</h1>
        <table class="details-table">
            <tr><th>Part Number</th><td><?= htmlspecialchars($part['part_number']) ?></td></tr>
            <tr><th>Manufacturer</th><td><?= htmlspecialchars($part['manufacturer']) ?></td></tr>
           <tr><th>ECCN</th><td><?= htmlspecialchars($part['eccn']) ?></td></tr>
                        <?php
            // WEIGHT ROW
            if (!empty($part['packaged_weight'])) {
                echo '<tr><th>Packaged Weight</th><td>' . htmlspecialchars($part['packaged_weight']) . '</td></tr>';
            } elseif (!empty($part['weight'])) {
                echo '<tr><th>Weight</th><td>' . htmlspecialchars($part['weight']) . '</td></tr>';
            }
            
            // DIMENSIONS ROW
            if (!empty($part['packaged_dimensions'])) {
                echo '<tr><th>Packaged Dimensions</th><td>' . htmlspecialchars($part['packaged_dimensions']) . '</td></tr>';
            } elseif (!empty($part['dimensions'])) {
                echo '<tr><th>Dimensions</th><td>' . htmlspecialchars($part['dimensions']) . '</td></tr>';
            }
            ?>
            <tr><th>Alternate Part Numbers</th><td> <?= !empty($alternateParts) ? htmlspecialchars(implode(', ', array_column($alternateParts, 'part_number'))) : 'No alternate part numbers found.' ?></td></tr>
        </table>
    </div>
</div>

<div class="container" style="display: flex; flex-wrap: wrap; gap: 20px; padding: 35px 0px;">
    <div class="right-section">
        <h2 style="font-size:25px; font-weight:500;">Description</h2>
       <?php if (!empty($part['description'])): ?>
            <p><?= nl2br(htmlspecialchars($part['description'])) ?></p>
        <?php elseif (!empty($part['notes'])): ?>
            <p><?= nl2br(htmlspecialchars($part['notes'])) ?></p>
        <?php endif; ?>


        <section class="container mt-4 alt-section">
            <div class="row align-items-start mb-2">
                <div class="col-md-6">
                    <?php
                        $aircraftList = array_map('trim', explode(';', $part['compatible_aircraft']));
                        ?>

                    <h4 class="fw-semibold mb-2">Applicable Aircraft</h4>
                    <div class="aircraft-bubbles">
                        <?php foreach ($aircraftList as $aircraft): ?>
                            <?php if (!empty($aircraft)): ?>
                                <span class="aircraft-bubble">
                                    <?= htmlspecialchars($aircraft) ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>

<!-- Related Parts -->
<div class="container" style="display: flex; flex-wrap: wrap; gap: 20px; padding: 15px 0px;">
    <div class="right-section">
        <section class="container mt-5">
            <h4 class="fw-semibold mb-3">Related Parts</h4>
            <div class="row g-3">
                <?php if (!empty($relatedParts)) : ?>
                    <?php foreach ($relatedParts as $r) : ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="related-card h-100">
                                <img src="<?= htmlspecialchars($r['main_image']) ?>" alt="<?= htmlspecialchars($r['part_name']) ?>">
                                <h6><?= htmlspecialchars($r['part_name']) ?></h6>
                                <a href="/parts/<?= urlencode($r['part_number']) ?>/<?= strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $r['part_name']), '-')) ?>">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No related parts found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
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
 
   


 <div class="modal" id="quoteModal">
  <div class="modal-content">
    <span class="close-modal" id="closeModal">&times;</span>
    <h3>Request for Quote</h3>

    <form id="quoteForm">
      <div class="form-row">
        <input type="text" name="contact_name" placeholder="Contact Name" required>
        <input type="text" name="business_name" placeholder="Business Name" required>
      </div>

      <div class="form-row">
        <input type="tel" name="phone_number" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email Address" required>
      </div>

      <label for="condition">Select Condition Required <span style="color:red;">*</span></label>
      <select id="condition" name="condition" required>
        <option value="">Select Condition</option>
        <option value="New">New</option>
        <option value="Overhauled">Overhauled</option>
        <option value="Serviceable">Serviceable</option>
        <option value="As Removed">As Removed</option>
      </select>

      <label for="tag_requirement">Select Tag Requirement <span style="color:red;">*</span></label>
      <select id="tag_requirement" name="tag_requirement" required>
        <option value="">Select Tag Requirement</option>
        <option value="None">None</option>
        <option value="8130">8130</option>
        <option value="EASA Form 1">EASA Form 1</option>
        <option value="81.30 + EASA Form 1">81.30 + EASA Form 1</option>
        <option value="SV (Serviceable Tag)">SV (Serviceable Tag)</option>
      </select>

      <textarea name="message" rows="4" placeholder="Additional Message (optional)"></textarea>

      <button type="submit">Submit Request</button>
    </form>
  </div>
</div>


<!-- Popup confirmation message -->
<div id="popupMessage" class="popup-message">Your quote request has been sent successfully!</div>




    <!-- Load javascripts at bottom, this will reduce page load time -->
    <!-- BEGIN CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
    <!--[if lt IE 9]>
    <script src="/assets/plugins/respond.min.js"></script>  
    <![endif]-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>      
    <script src="/assets/corporate/scripts/back-to-top.js" type="text/javascript"></script>
    <!--<script src="/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>-->
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <!--<script src="/assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
    <!--<script src="/assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
    <!--<script src='/assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
    <!--<script src="/assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script><!-- Quantity -->

    <script src="/assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <!--<script src="/assets/pages/scripts/bs-carousel.js" type="text/javascript"></script>-->
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            // Layout.initOWL();
            // Layout.initImageZoom();
            // Layout.initTouchspin();
            // Layout.initTwitter();
        });
    </script>

   <script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar-menu");

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("toggle-show");
        });
    }
});
</script>

    <!-- END PAGE LEVEL JAVASCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Page Loaded");
        });
    </script>
    <script>
        $(document).ready(function() {
    // Toggle the visibility of the details when an expandable row is clicked
    $(".expandable").click(function() {
        // Find the next sibling div that contains the details
        var $details = $(this).next(); 

        // Find the corresponding expand icon within the clicked row
        var $icon = $(this).find(".expand-icon");

        // Toggle the visibility of the details div (show or hide)
        $details.slideToggle();

        // Change the icon text to "-" when the details are shown, and "+" when hidden
        if ($details.is(":visible")) {
            $icon.text("-");  // Change to '-' when expanded
        } else {
            $icon.text("+");  // Change to '+' when collapsed
        }
    });
});

    </script>
    <script>
        // Open the chat window
function openChatWindow() {
    document.getElementById('chatWindow').style.display = 'block';
}

// Close the chat window
function closeChatWindow() {
    document.getElementById('chatWindow').style.display = 'none';
}

// Send a message and update the chat window
function sendMessage() {
    var message = document.getElementById('chatInput').value;
    if (message.trim() !== "") {
        var chatMessages = document.getElementById('chatMessages');
        
        // Create a new message element
        var messageDiv = document.createElement('div');
        messageDiv.style.marginBottom = '10px';
        messageDiv.style.textAlign = 'right';
        messageDiv.innerHTML = "<div style='display: inline-block; padding: 10px; background-color: #e5e5e5; border-radius: 10px; max-width: 70%;'>" + message + "</div>";
        
        // Append the new message
        chatMessages.appendChild(messageDiv);
        
        // Clear the input field
        document.getElementById('chatInput').value = "";

        // Scroll to the bottom of the chat window
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Simulate a response from the vendor after a short delay
        setTimeout(function() {
            var responseDiv = document.createElement('div');
            responseDiv.style.marginBottom = '10px';
            responseDiv.style.textAlign = 'left';
            responseDiv.innerHTML = "<div style='display: inline-block; padding: 10px; background-color: #f1f1f1; border-radius: 10px; max-width: 70%;'>" + "Thank you for your question, we'll get back to you shortly." + "</div>";

            // Append the vendor's response
            chatMessages.appendChild(responseDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 1000);
    }
}

    </script>
 <script>
    // Correct image path array from PHP (already full HTTPS URLs)
    let images = <?php echo json_encode($images); ?>;
    let currentIndex = 0;

    // Change main image when clicking a thumbnail
    function changeMainImage(src) {
        document.getElementById('main-image').src = src;
        currentIndex = images.indexOf(src);
    }

    // Change image on next/prev button click
    function changeImage(direction) {
        currentIndex += direction;

        // Wrap around if out of bounds
        if (currentIndex < 0) currentIndex = images.length - 1;
        if (currentIndex >= images.length) currentIndex = 0;

        document.getElementById('main-image').src = images[currentIndex];
    }
</script>


<script>
function showDocumentPopup(url) {
    let viewer = document.getElementById("document-viewer");
    viewer.innerHTML = ""; // Clear previous content

    // Ensure the full path is used with the domain
    // Check if the URL is relative and prepend the domain if needed
    if (!url.startsWith("http://") && !url.startsWith("https://")) {
        url = "https://flying411.com/" + url; // Replace `yourdomain.com` with your actual domain
    }

    let fileExtension = url.split('.').pop().toLowerCase();
    let supportedImages = ["jpg", "jpeg", "png", "gif"];
    
    if (fileExtension === "pdf") {
        viewer.innerHTML = `<iframe src="${url}" width="100%" height="500px" frameborder="0"></iframe>`;
    } else if (supportedImages.includes(fileExtension)) {
        viewer.innerHTML = `<img src="${url}" style="max-width: 100%; height: auto;" />`;
    } else if (["doc", "docx"].includes(fileExtension)) {
        // Encode the URL for Google Docs Viewer to handle spaces and special characters
        viewer.innerHTML = `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true" width="100%" height="500px"></iframe>`;
    } else {
        viewer.innerHTML = `<p>Preview not available for this file type. <a href="${url}" target="_blank">Open manually</a>.</p>`;
    }

    document.getElementById("document-popup").style.display = "flex";
}

function closeDocumentPopup() {
    document.getElementById("document-popup").style.display = "none";
    document.getElementById("document-viewer").innerHTML = ""; // Clear content
}
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
    $('#view-documents-toggle').on('click', function () {
        $('#document-list').slideToggle();

        $('.doc-row').each(function () {
            const isFlex = $(this).css('display') === 'flex';

            if (isFlex) {
                // Remove flex styles
                $(this).css({
                    'display': '',
                    'flex-direction': ''
                });
            } else {
                // Apply flex styles
                $(this).css({
                    'display': 'flex',
                    'flex-direction': 'column'
                });
            }
        });
    });
</script>
<script>
  const modal = document.getElementById('quoteModal');
  document.getElementById('openQuote').onclick = () => modal.style.display = 'flex';
  document.getElementById('closeModal').onclick = () => modal.style.display = 'none';
  window.onclick = e => { if(e.target === modal) modal.style.display = 'none'; };
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const quoteForm = document.getElementById("quoteForm");
  const popup = document.getElementById("popupMessage");

  if (!quoteForm || !popup) return;

  quoteForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("/flying411/send_quote.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        popup.textContent = "Your quote request has been sent successfully!";
        popup.style.background = "#4CAF50";
        quoteForm.reset();

        setTimeout(() => {
          const modal = document.getElementById('quoteModal');
          if (modal) modal.style.display = 'none';
        }, 2000);

      } else {
        popup.textContent = "Failed to send request.";
        popup.style.background = "#f44336";
      }

      popup.style.display = "block";
      setTimeout(() => popup.style.display = "none", 3000);
    })
    .catch(err => {
      console.log(err);
      popup.textContent = "Error sending request.";
      popup.style.background = "#f44336";
      popup.style.display = "block";
    });
  });
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
</html>