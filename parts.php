<?php include 'track_page.php'; ?>

<?php

session_start();
include "config.php"; // Database connectionfooter

// Get the 'name' parameter from URL
$nameFilter = $_GET['name'] ?? '';

$perPage = 18;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

$parts = [];
$totalParts = 0;

if ($nameFilter) {
    $nameFilter = trim(preg_replace('/\s+/', ' ', $nameFilter));

    if (preg_match('/^(.*?)\s*\((.*?)\)$/', $nameFilter, $matches)) {
        $partName = trim($matches[1]);
        $partNumber = trim($matches[2]);

        // Get total count
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM parts 
            WHERE status = 'approved' 
            AND (part_name LIKE :partName OR part_number LIKE :partNumber)
        ");
        $countStmt->execute([
            ':partName' => "%$partName%",
            ':partNumber' => "%$partNumber%"
        ]);
        $totalParts = $countStmt->fetchColumn();

        // Paginated fetch (using named parameters only)
        $stmt = $pdo->prepare("
            SELECT * 
            FROM parts 
            WHERE status = 'approved' 
            AND (part_name LIKE :partName OR part_number LIKE :partNumber) 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':partName', "%$partName%", PDO::PARAM_STR);
        $stmt->bindValue(':partNumber', "%$partNumber%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

    } else {
        // Search only by name
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM parts 
            WHERE status = 'approved' 
            AND (part_name LIKE :name OR part_number LIKE :name)
        ");
        $countStmt->execute([':name' => "%$nameFilter%"]);
        $totalParts = $countStmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT * 
            FROM parts 
            WHERE status = 'approved' 
            AND (part_name LIKE :name OR part_number LIKE :name)
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':name', "%$nameFilter%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

} else {
    // No name filter
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM parts WHERE status = 'approved'");
    $countStmt->execute();
    $totalParts = $countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT * 
        FROM parts 
        WHERE status = 'approved' 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($totalParts / $perPage);

// Get condition-wise count (always)
$query = "SELECT `condition`, COUNT(*) as total FROM parts WHERE status = 'approved' GROUP BY `condition`";
$stmt = $pdo->prepare($query);
$stmt->execute();
$conditionCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct locations
$query = "SELECT DISTINCT region FROM parts WHERE region IS NOT NULL ORDER BY region ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert result to associative array
$counts = [];
foreach ($conditionCounts as $row) {
    $counts[$row['condition']] = $row['total'];
}
function safe($val) {
    return htmlspecialchars($val ?? "", ENT_QUOTES, 'UTF-8');
}

// CONDITIONS
$conditionCounts = $pdo->query("
    SELECT `condition`, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY `condition`
    ORDER BY `condition`
")->fetchAll(PDO::FETCH_ASSOC);

// PART TYPE
$typeCounts = $pdo->query("
    SELECT `type`, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY `type`
    ORDER BY `type`
")->fetchAll(PDO::FETCH_ASSOC);

// MANUFACTURER
$manufacturerCounts = $pdo->query("
    SELECT manufacturer, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY manufacturer
    ORDER BY manufacturer
")->fetchAll(PDO::FETCH_ASSOC);

// OWNED TYPE
$ownedTypeCounts = $pdo->query("
    SELECT owned_type, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY owned_type
    ORDER BY owned_type
")->fetchAll(PDO::FETCH_ASSOC);

// MATERIAL
$materialCounts = $pdo->query("
    SELECT material, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY material
    ORDER BY material
")->fetchAll(PDO::FETCH_ASSOC);

// COMPATIBLE AIRCRAFT
$compatibleAircraftCounts = $pdo->query("
    SELECT compatible_aircraft, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY compatible_aircraft
    ORDER BY compatible_aircraft
")->fetchAll(PDO::FETCH_ASSOC);

// TAGGED WITH EASA FORM 1
$tagCounts = $pdo->query("
    SELECT tagged_with_easa_form_1, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY tagged_with_easa_form_1
    ORDER BY tagged_with_easa_form_1
")->fetchAll(PDO::FETCH_ASSOC);

// LOCATIONS (REGION)
$locationCounts = $pdo->query("
    SELECT country, COUNT(*) AS total
    FROM parts
    WHERE status='approved'
    GROUP BY country
    ORDER BY country
")->fetchAll(PDO::FETCH_ASSOC);

// DEALERS
$dealerCounts = $pdo->query("
    SELECT v.vendor_id, v.business_name, COUNT(*) AS total
    FROM parts p
    JOIN vendors v ON v.vendor_id = p.vendor_id
    WHERE p.status='approved'
    GROUP BY v.vendor_id, v.business_name
    ORDER BY v.business_name
")->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <!-- Meta Tags for Parts Listing Page -->
<!-- Responsive and Compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!-- SEO Meta -->
<title>Parts</title>
<meta name="description" content="Browse top-quality aircraft parts at Flying411. Shop engines, pistons, accessories, and more from trusted aviation suppliers. Filter by condition, manufacturer, and location.">
<meta name="keywords" content="aircraft parts, aircraft components, aviation accessories, piston parts, factory new parts, overhauled parts, serviceable aviation parts, surplus aircraft parts, buy aircraft components">
<meta name="author" content="Flying411">

<!-- Open Graph Meta (for Social Sharing) -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="Buy Aircraft Parts – Engines, Pistons & Accessories | Flying411">
<meta property="og:description" content="Find premium aircraft parts on Flying411. Explore listings for new, overhauled, and serviceable components. Filter by condition and location.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://flying411.com//assets/corporate/img/Hangar-2-4-White-Final-1.png"> <!-- Replace with actual part image -->
<meta property="og:url" content="https://flying411.com/parts-listing">

<!-- Twitter Card Meta -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Aircraft Parts for Sale | Engine, Piston & More | Flying411">
<meta name="twitter:description" content="Shop aircraft parts from global sellers at Flying411. Quality assured. Factory New, Overhauled, and Serviceable available.">
<meta name="twitter:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png"> <!-- Replace with actual part listing image -->


 <!-- Apple Touch Icon (iPhones, iPads) -->
<link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Android / Chrome -->
<link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Safari Pinned Tab (macOS) -->
<link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">


  <!-- Fonts START -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,500,500|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,500,500,900&amp;subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,500,500,900&amp;subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page -->  
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <link href="/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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

     .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 95%;
        max-width: 1430px;
            margin: auto;
            padding: 20px 0;
        }

        /* Filter Section */
        .filter-section {
            width: 300px;
            /*background-color: #fff;*/
            /*border: 1px solid #ddd;*/
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
       font-family: "EB Garamond", serif;
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
      width: 31% !important;
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
      font-weight: 500 !important;
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
      width: 35% !important;
      height: 100%!important;
      object-fit: cover !important;
    }
    .header .header-navigation ul li a{
        color:white !important;
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
      background-color: lightgray !important;
      color: #black !important;
      border: 1px solid black !important;

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
.headers {
            position: relative;
            width: 100%;
            height: 80vh;
            background: url('/assets/pages/img/products/updated\ aircraft.jpg') center center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .headers::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .headers h1, .header p {
            position: relative;
            z-index: 2;
        }

        .headers h1 {
            font-size: 4.5rem;
            font-weight: ;
            font-family: "EB Garamond", serif;
            margin-top: 100px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .headers p {
            font-size: 1.5rem;
            color:white !important;
            z-index: 2;
            margin: 10px 0 20px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
        }
.search-bar {
            display: flex;
            justify-content: center;
            gap: 10px;
            width: 41%;
            max-width: 900px;
            position: relative;
            z-index: 2;
        }

        .search-bar select, .search-bar input, .search-bar button {
            padding: 15px;
            font-size: 1.5rem;
            border: none;
            border-radius: 5px;
        }

        .search-bar select {
            flex: 1;
            border: 1px solid white;
            color:#fff;
            border-radius: 10px;
            min-width: 150px;
            appearance: none;
            background: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"%3E%3Cpath fill="%23ccc" d="M2 0L0 2h4zm0 5L0 3h4z"/%3E%3C/svg%3E') no-repeat right 10px center;
            background-size: 12px;
        }
        .search-bar select:focus {
    background-color: #333; /* Keep the background color consistent */
    color: #fff; /* Ensure text color remains white */
    outline: none; /* Optional: Remove default outline */
}

/* Optional: Style the options within the dropdown */
.search-bar select option {
    background-color: #333; /* Set option background color */
    color: #fff; /* Set option text color */
}

/* Optional: If you want the hover effect on options */
.search-bar select option:hover {
    background-color: #555; /* Darker background when hovering over options */
}
        .search-bar input {
            flex: 2;
            min-width: 200px;
            color:black;
        }

        .search-bar button {
            background-color: #e30b17;
            color: white;
            cursor: pointer;
            min-width: 120px;
        }

        .search-bar button:hover {
            background-color: #c00914;
        }
 @media (max-width: 1024px) {
.header .header-navigation ul li a{
        color:black !important;
    }
 }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .header p {
                font-size: 1rem;
            }

            .search-bar {
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }
        }
.filter-section {
    width: 280px;
    /*border: 1px solid #ddd;*/
    padding: 15px;
    /*background: #fff;*/
    /*font-family: Arial, sans-serif;*/
}

.filter-item h4 {
    font-size: 14px;
    /*font-weight: bold;*/
    cursor: pointer;
    padding: 10px;
    /*background: #f1f1f1;*/
    margin: 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    
}
@media (max-width: 768px) {
            .filter-section {
                width: 100% !important;
                margin-bottom: 20px;
            }

            .right-section {
                width: 100%;
                margin: 0;
            }

          
            .listing-cards {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
        }
.arrow {
    font-size: 14px;
    color: #333;
}

.filter-content {
    display: flex;
    flex-direction:column;
    overflow-y: scroll;
    max-height: 184px;
    padding: 10px;
    /*max-height: 200px;*/
    overflow-y: auto;
    /*background: #f9f9f9;*/
     white-space: nowrap; 
  position: relative;
   /*scrollbar-width: thin;*/
  /*scrollbar-color: #bbb #f1f1f1;*/
}
.filter-content::-webkit-scrollbar {
  width: 6px;
}

.filter-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.filter-content::-webkit-scrollbar-thumb {
  background: #bbb;
  border-radius: 10px;
}

.filter-content::-webkit-scrollbar-thumb:hover {
  background: #999;
}
.filter-content a {
    display: block;
    padding: 5px;
    font-size: 14px;
    font-weight: 500;
    color: #757575;
}


.submit-btn {
    width: 100%;
    padding: 10px;
    background: #008cba;
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 10px;
    display: none;
}

.selected-filters {
    background: #f8f8f8;
    padding: 23px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
}

.slides {
    display: flex;
    transition: transform 0.5s ease; /* Smooth transition for sliding */
}

.slides img {
    width: 100%;  /* Each image takes full width of the container */
    object-fit: cover; /* Ensures the image scales without distortion */
}
/* Style for the dropdown list */
.dropdown-list1 {
       position: absolute;
    background: white;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ccc;
    border-radius: 5px;
    z-index: 1000;
    margin-top: 50px;
    display: none;
    color: black;
    text-align: left;
}

.dropdown-item1 {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
    transition: background 0.2s ease-in-out;
}

.dropdown-item1:hover {
    background: #f1f1f1;
}


.tooltip-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
    margin-bottom:50px;
}

.tooltip {
    position: absolute;
    background-color:#f1f1f1;
    color: #000;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1;
    /*bottom: 100%;*/
   /*bottom: 100%;*/
    left: 50%;
    top: 25px;
    transform: translateX(-50%);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease-in-out, visibility 0.2s;
}

.tooltip-container:hover .tooltip {
    opacity: 1;
    z-index: 1;
    visibility: visible;
}
.site-logo img{
    border-radius: 50% !important;
    margin-right: 110px !important;
}
.header .top-cart-block{
    margin-left: 70px !important;
}
.header .top-cart-block a:hover{
    background-color: white!important;
    color: black !important; 
    border: 1px solid black !important;
}
@media (min-width: 1200px) {
    .header .container {
        max-width: 1350px !important;
    }
}
@media (min-width: 1025px) {
    .header .container {
        max-width: 1350px !important;
    }
    .header .top-cart-block {
    margin-left: 5px !important;
}
.site-logo img {
    border-radius: 50% !important;
    margin-right: 28px !important;
}

}  
@media (max-width: 1024px) {
    .ecommerce .header-navigation > ul > li > a{
     color:white !important;
 }
  .site-logo img{
     margin-right:0px !important;
 } 
 .header .top-cart-block{
         margin-left: 0px !important;
 }
 .header .container, .header .container-md, .header .container-sm{
     max-width:800px;
 }
}


.search-wrapper {
    position: relative;
    display: inline-block;
  }
  
.search-wrapper .serchicon{
      position: absolute;
    top: 14px;
    left: 12px;
  }
.search-wrapper .serchicon img{
    width: 30px;
    height: 28px;
}
  .search-input {
    
    padding: 16px 15px 15px 48px !important;
    width: 550px;
    border-radius: 30px !important;
    border: none;
    font-size: 17px;
    font-weight: 400;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    outline: none;
    background: white;
    color: #142238;
  }

 .type-tag {
    font-size: 12px;
    padding: 1px 6px 5px 6px;
    border-radius: 20px !important;
    margin-right: 8px;
    color: #fff;
    text-transform: capitalize;
}

/* Client-specified colors */
.tag-aircraft { background-color: #065f46; }  /* Dark green */
.tag-engine   { background-color: #1e40af; }  /* Dark blue */
.tag-part     { background-color: #991b1b; }  /* Dark red */
.tag-service  { background-color: #9d174d; }  /* Dark pink/purple */
.tag-default  { background-color: #1e3a8a; } 
.part-info {
    background-color: #991b1b; /* dark red */
}/* Default blue fallback */
  .suggestion-item {
    display: flex;
      max-height: 200px  !important; 
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #334155;
    cursor: pointer;
  }

  .suggestion-item:hover {
    background-color: #1e293b;
  }
 #suggestionList {
  max-height: 200px  !important; /* or use any height you prefer like 20rem */
  overflow-y: auto;
  overflow-x: hidden;
  position: absolute;
  width: 100%;
  border-radius: 30px  !important;
   background-color: #1e293b;
    scrollbar-width: thin;               /* Firefox */
  scrollbar-color: #999 transparent;
}
#suggestionList::-webkit-scrollbar {
  width: 6px; /* Thin scrollbar */
}

#suggestionList::-webkit-scrollbar-track {
  background: transparent;
}

#suggestionList::-webkit-scrollbar-thumb {
  background-color: #999;
  border-radius: 4px;
}
 /* Responsive adjustments */
@media (max-width: 1024px) {
  .search-input {
    width: 500px  !important;
    padding: 16px 15px 15px 48px;
    font-size: 15px;
  }
}

@media (max-width: 768px) {
  .search-input {
     width: 60vw  !important;
    padding: 16px 15px 15px 48px !important;
    font-size: 14px !important;
  }
  #suggestionList{
      width:100%;
  }
    .ecommerce .header-navigation > ul > li > a{
     color:black !important;
 }
}

@media (max-width: 480px) {
      .ecommerce .header-navigation > ul > li > a{
     color:black !important;
 }
/* Style for each image inside the slide */
.slides img {
    /*width: 100% !important;  */
    height: auto !important;
    object-fit: cover !important;
}
  .search-input {
    width: 90vw !important;
     padding: 16px 15px 15px 48px !important;
    font-size: 14px;
  }
  .search-wrapper .serchicon{
      top:14px !important;
  }
#suggestionList{
    width:100%; 
    position: relative !important;
}
  #suggestionBox li {
    font-size: 14px;
    flex-direction: column !important;
    align-items: flex-start !important;
  }

  #suggestionBox li .type-tag {
    margin-top: 6px !important;
    margin-left: 0 !important;
  }

  .search-wrapper {
    margin-top: 5vh !important;
  }
}

.car-card {
    position: relative;
}

.sold-ribbon {
    position: absolute;
    top: -13px;
    right: -12px;
    width: 165px;
    z-index: 10;
    opacity: 0.9;
    transform: rotate(1deg);
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
.parts-page-wrap{
     margin-top:50px; 
 display:flex;
 gap:30px;
 align-items:flex-start;
}
.filter-sidebar{
   width:280px; 
   background:#fff;
   border:1px solid #eee;
   padding:20px; 
   border-radius:14px; 
}

 @media (max-width: 600px) {
.parts-page-wrap{
    gap: 0px;
}
.filter-sidebar{
    width: 100%;
    margin-bottom: 30px;
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
  <div class="header" style="position: absolute;width: 100%;">
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
            <li><a href="/services">Services</a></li>
    <li><a href="/contacts">CONTACT US</a></li>
       <li style="display:none;">
           <?php if (isset($_SESSION['user_id'])): ?>
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
    <button class="mobile-menu-btn" id="mobileMenuBtn">
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
   <div class="headers">
    <h1>WELCOME TO AIRCRAFT PARTS</h1>
    <p style="font-size: 15px; color: white;">Discover Certified Aircraft Parts with Advanced Search Options</p>
    <div class="search-bar">
        <!--<input type="text" name="part_number" id="part_number" placeholder="Search by Part Number" oninput="showDropdown()">-->
        <!--<button type="button" onclick="fetchByParts()">Search</button>-->
        <div class="search-wrapper relative mx-auto w-full max-w-xl">
      
      <span class="serchicon">
      <img src="assets/corporate/img/search.png">    
      </span>
      <input type="text" id="searchInput" class="search-input w-full border rounded" placeholder="" autocomplete="off">
      <div id="suggestionBox" class="absolute left-0 right-0 bg-blue-900 text-white rounded-xl shadow-lg mt-2 z-50 hidden max-h-80 overflow-y-auto">
        <ul id="suggestionList" class="space-y-2"></ul>
      </div>
</div>
    </div>
    <!--<div id="suggestions" class="dropdown-list1"></div>-->
</div>


  
<div class="parts-page-wrap container" style="">

    <!-- LEFT: FILTER SIDEBAR -->
 <aside class="filter-sidebar" style="">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:15px;">
        <h3 style="margin:0;">Parts</h3>
        <button class="clear-filters-btn"
                style="border:none; background:none; padding:5px 10px; font-size:12px; color:#d00; cursor:pointer;"
                onclick="resetFilters()">
            Clear Filters
        </button>
    </div>

    <div id="selected-filters" style="display:none; margin-bottom:10px;"></div>


    <!-- CONDITIONS -->
<div class="filter-item">
    <h4 onclick="toggleFilter('condition_filter', this)">
        Condition <span class="arrow"><img src="assets/pages/img/icons/down-arrow-golden.640deada.svg"></span>
    </h4>

    <div id="condition_filter" class="filter-content" style="display:none;">
        <?php foreach ($conditionCounts as $row): ?>
            <label class="filter-checkbox" 
                   style="display:flex;align-items:center;margin-bottom:8px;cursor:pointer;">
                
                <input type="checkbox"
                       class="filter-input"
                       data-filter-type="condition"
                       value="<?= safe($row['condition']) ?>"
                       style="width:18px;height:18px;margin-right:10px;">
                
                <?= safe($row['condition']) ?> (<?= (int)$row['total'] ?>)
            </label>
        <?php endforeach; ?>
    </div>
</div>


          <!-- PRICE -->
    <div class="filter-item">
        <h4 onclick="toggleFilter('price_range_filter', this)">
            Price <span class="arrow"><img src="assets/pages/img/icons/down-arrow-golden.640deada.svg" ></span>
        </h4>

        <div id="price_range_filter" class="filter-content" style="display:none;">
            <div style="display:flex; gap:10px; margin-top:10px;">
                <input type="number" id="price_min" placeholder="Min"
                       style="width:50%; padding:8px; border-radius:6px; border:1px solid #ccc;">
                <input type="number" id="price_max" placeholder="Max"
                       style="width:50%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>
        </div>
    </div>


    <!-- COMPATIBLE AIRCRAFT -->
    <div class="filter-item">
        <h4 onclick="toggleFilter('compatible_aircraft_filter', this)">
            Compatible Aircraft <span class="arrow"><img src="assets/pages/img/icons/down-arrow-golden.640deada.svg" ></span>
        </h4>

        <div id="compatible_aircraft_filter" class="filter-content" style="display:none;">
            <?php foreach ($compatibleAircraftCounts as $row): ?>
                <label class="filter-checkbox" style="display:flex;align-items:center;margin-bottom:8px;cursor:pointer;">
                    <input type="checkbox"
                           class="filter-input"
                           data-filter-type="compatible_aircraft"
                           value="<?= safe($row['compatible_aircraft']) ?>"
                           style="width:18px;height:18px;margin-right:10px;">
                    <?= safe($row['compatible_aircraft']) ?> (<?= (int)$row['total'] ?>)
                </label>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- LOCATION -->
    <div class="filter-item">
        <h4 onclick="toggleFilter('location_filter', this)">
            Location <span class="arrow"><img src="assets/pages/img/icons/down-arrow-golden.640deada.svg" ></span>
        </h4>

        <div id="location_filter" class="filter-content" style="display:none; max-height:200px; overflow-y:auto;">
            <?php foreach ($locationCounts as $row): ?>
                <label class="filter-checkbox" style="display:flex;align-items:center;margin-bottom:8px;cursor:pointer;">
                    <input type="checkbox"
                           class="filter-input"
                           data-filter-type="location"
                           value="<?= safe($row['country']) ?>"
                           style="width:18px;height:18px;margin-right:10px;">
                    <?= safe($row['country']) ?> (<?= (int)$row['total'] ?>)
                </label>
            <?php endforeach; ?>
        </div>
    </div>

</aside>


    <!-- RIGHT: RESULTS -->
    <div class="right-section">
      <div class="listing-cards">
        <?php if (!empty($parts)): ?>
            <?php foreach ($parts as $part): ?>
                          <?php
$part_slug = strtolower($part['part_name']);
$part_slug = preg_replace('/[^a-z0-9]+/', '_', $part_slug);
$part_slug = trim($part_slug, '_');
 // Slug example: Engine_Filter
?>


                 <?php
                // Fetch all images for this part
               $imgQuery = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'part' ORDER BY sort_order ASC");

                $imgQuery->execute([$part['part_id']]);
                $images = $imgQuery->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="car-card">
                    <?php if (!empty($part['is_sold']) && $part['is_sold'] == 1): ?>
                        <img class="sold-ribbon" src="/assets/pages/img/icons/sold.png" alt="Sold">
                    <?php endif; ?>

            <div class="slider" data-current-slide="0">
              <span class="new-tag" style="font-size: 10px !important;">NEW</span>
              <!-- Two icons: Heart + List -->
              <div class="favorite-icons">
                <img src="/assets/pages/img/icons/ribbon.png" width="25px" alt="">
              </div>
               <a href='/parts/<?= $part_slug ?>/<?= $part['part_id'] ?>';>
              <div class="slides">
                   <?php if (!empty($images)): ?>
                                    <?php foreach ($images as $image): ?>
                                        <img src="vendors/<?= htmlspecialchars($image['image_url']); ?>" alt="Part Image">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Default Image If No Images Found -->
                                    <img src="/assets/pages/img/products/default-part.png" alt="Default Image">
                                <?php endif; ?>
              </div>
              </a>
              <div class="arrows">
                <span class="arrow-left"><img src="/assets/pages/img/icons/left-chevron.png" width="17px" alt=""></span>
                <span class="arrow-right"><img src="/assets/pages/img/icons/right-arrow-angle.png" width="17px" alt=""></span>
              </div>
              <div class="slider-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
              </div>
            </div>
            <div class="car-info" style="text-align: left; font-size: 16px;">
              <h2 style="margin:20px 0px 30px 15px; font-size: 23px !important;letter-spacing: 1px;  color: #000; font-weight: 500;">
               <?= htmlspecialchars($part['part_name']); ?>
              </h2>
              <p><img src="/assets/pages/img/icons/license-plate.png" alt="">&nbsp; Part No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= htmlspecialchars($part['part_number']); ?></span></p>
              <p><img src="/assets/pages/img/icons/settings.png" alt="">&nbsp;Condition &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= htmlspecialchars($part['condition']); ?></span></p>
              <p><img src="/assets/pages/img/icons/shopping.png" alt="">&nbsp;Tag &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span><?= htmlspecialchars($part['tagged_with_easa_form_1']); ?></span></p>
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
        <?php else: ?>
            <p>No parts available.</p>
        <?php endif; ?>
        
        
        <div class="pagination">
    <?php
    if ($totalPages > 1):

        $queryStr = '';
        if (!empty($_GET['name'])) {
            $queryStr .= '&name=' . urlencode($_GET['name']);
        }

        // Prev Button
        if ($currentPage > 1):
            echo '<a href="?page=' . ($currentPage - 1) . $queryStr . '">&laquo; Prev</a>';
        endif;

        $range = 2; // pages around current
        $ellipsisShown = false;

        for ($i = 1; $i <= $totalPages; $i++) {
            if (
                $i == 1 || 
                $i == $totalPages || 
                ($i >= $currentPage - $range && $i <= $currentPage + $range)
            ) {
                $activeClass = ($i == $currentPage) ? 'active' : '';
                echo '<a class="' . $activeClass . '" href="?page=' . $i . $queryStr . '">' . $i . '</a>';
                $ellipsisShown = false;
            } else {
                if (!$ellipsisShown) {
                    echo '<span class="dots">...</span>';
                    $ellipsisShown = true;
                }
            }
        }

        // Next Button
        if ($currentPage < $totalPages):
            echo '<a href="?page=' . ($currentPage + 1) . $queryStr . '">Next &raquo;</a>';
        endif;

    endif;
    ?>
</div>

    </div>

        <!-- keep your existing pagination markup if you want for initial page load -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $queryStr = '';
                if (!empty($_GET['name'])) {
                    $queryStr .= '&name=' . urlencode($_GET['name']);
                }

                if ($currentPage > 1):
                    echo '<a href="?page=' . ($currentPage - 1) . $queryStr . '">&laquo; Prev</a>';
                endif;

                $range = 2;
                $ellipsisShown = false;

                for ($i = 1; $i <= $totalPages; $i++) {
                    if (
                        $i == 1 ||
                        $i == $totalPages ||
                        ($i >= $currentPage - $range && $i <= $currentPage + $range)
                    ) {
                        $activeClass = ($i == $currentPage) ? 'active' : '';
                        echo '<a class="' . $activeClass . '" href="?page=' . $i . $queryStr . '">' . $i . '</a>';
                        $ellipsisShown = false;
                    } else {
                        if (!$ellipsisShown) {
                            echo '<span class="dots">...</span>';
                            $ellipsisShown = true;
                        }
                    }
                }

                if ($currentPage < $totalPages):
                    echo '<a href="?page=' . ($currentPage + 1) . $queryStr . '">Next &raquo;</a>';
                endif;
                ?>
            </div>
        <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/jquery.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-migrate.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/corporate/scripts/back-to-top.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
  <!-- END CORE PLUGINS -->

  <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
  <script src="assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
  <script src="assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script>
  <!-- slider for products -->
  <script src='assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
  <script src="assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
  <!-- Quantity -->

  <script src="assets/corporate/scripts/layout.js" type="text/javascript"></script>
  <script src="assets/pages/scripts/bs-carousel.js" type="text/javascript"></script>
  <script type="text/javascript">
    jQuery(document).ready(function () {
      Layout.init();
      Layout.initOWL();
      Layout.initImageZoom();
      Layout.initTouchspin();
      Layout.initTwitter();
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
        function resetFilters() {
            alert("Filters have been reset.");
            // Add logic to reset filters
        }
    
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }
    </script>
  <script>
// =======================================
// GLOBAL STATE
// =======================================

const selectedFilters = {
    condition: new Set(),
    type: new Set(),
    manufacturer: new Set(),
    owned_type: new Set(),
    material: new Set(),
    compatible_aircraft: new Set(),
    tag: new Set(),
    location: new Set(),
    dealer: new Set(),
};

const rangeFilters = {
    price_min: null,
    price_max: null,
};

let searchKeyword = ""; // For top search bar


// =======================================
// HELPERS
// =======================================

function slugify(text) {
    return (text || "")
        .toString()
        .trim()
        .toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w\-]+/g, "");
}

function toggleFilter(id, el) {
    const box = document.getElementById(id);
    const parent = el.parentElement;

    parent.classList.toggle("open");

    if (box.style.display === "block") {
        box.style.display = "none";
    } else {
        box.style.display = "block";
    }
}


// =======================================
// UPDATE FILTERS
// =======================================

function updateFilters(type, value) {
    const set = selectedFilters[type];
    if (!set) return;

    if (set.has(value)) set.delete(value);
    else set.add(value);

    updateFilterTags();
    fetchParts();
}

function updateRangeFilter(field, val) {
    rangeFilters[field] = val === "" ? null : val;
    updateFilterTags();
    fetchParts();
}


// =======================================
// SELECTED FILTER TAGS
// =======================================

function updateFilterTags() {
    let box = document.getElementById("selected-filters");
    box.innerHTML = "";
    let hasFilters = false;

    // multi filters
    for (const [type, set] of Object.entries(selectedFilters)) {
        set.forEach(v => {
            hasFilters = true;
            box.innerHTML += `
                <span class="filter-tag">
                    ${v}
                    <span class="remove-tag" onclick="removeFilter('${type}','${v}')">×</span>
                </span>
            `;
        });
    }

    // range filters
    for (const [k, v] of Object.entries(rangeFilters)) {
        if (v !== null) {
            hasFilters = true;
            box.innerHTML += `
                <span class="filter-tag">
                    ${k.replace("_", " ")}: ${v}
                    <span class="remove-tag" onclick="removeRange('${k}')">×</span>
                </span>
            `;
        }
    }

    box.style.display = hasFilters ? "block" : "none";

    highlightFilterLinks();
}

function removeFilter(type, value) {
    selectedFilters[type].delete(value);

    // ALSO uncheck checkbox
    document.querySelectorAll(`.filter-input[data-filter-type="${type}"]`).forEach(el => {
        if (el.value === value) el.checked = false;
    });

    updateFilterTags();
    fetchParts();
}

function removeRange(field) {
    rangeFilters[field] = null;
    const input = document.getElementById(field);
    if (input) input.value = "";
    updateFilterTags();
    fetchParts();
}

function highlightFilterLinks() {
    document.querySelectorAll(".filter-link").forEach(link => {
        const t = link.dataset.filterType;
        const v = link.dataset.filterValue;
        if (selectedFilters[t]?.has(v)) link.classList.add("active-filter");
        else link.classList.remove("active-filter");
    });
}


// =======================================
// RESET ALL FILTERS
// =======================================

function resetFilters() {
    // Clear all filter sets
    for (const k in selectedFilters) selectedFilters[k].clear();
    for (const k in rangeFilters) rangeFilters[k] = null;

    // Uncheck all checkboxes
    document.querySelectorAll(".filter-input").forEach(el => el.checked = false);

    // Clear price range inputs
    document.querySelectorAll("input.range-input").forEach(el => el.value = "");

    searchKeyword = "";

    updateFilterTags();
    fetchParts();
}


// =======================================
// FETCH PARTS (AJAX)
// =======================================

function fetchParts() {
    const fd = new FormData();

    // Multi filters
    for (const [type, set] of Object.entries(selectedFilters)) {
        set.forEach(v => fd.append(type + "s[]", v));
    }

    // Range filters
    for (const [k, v] of Object.entries(rangeFilters)) {
        if (v !== null) fd.append(k, v);
    }

    // Search keyword
    fd.append("search", searchKeyword);

    fetch("filter_parts.php", {
        method: "POST",
        body: fd
    })
    .then(r => r.json())
    .then(parts => {
        const box = document.querySelector(".listing-cards");
        box.innerHTML = "";

        if (!parts.length) {
            box.innerHTML = "<p>No parts available.</p>";
            return;
        }

        parts.forEach(p => {
            const imgs = p.images ? p.images.split(",") : [];
            const imgHTML = imgs.length
                ? imgs.map(i => `<img src="vendors/${i}" alt="">`).join("")
                : `<img src="/assets/pages/img/products/default-part.png" alt="">`;

            const slug = slugify(p.part_name);

            box.innerHTML += `
                <div class="car-card">
                    ${p.is_sold == 1 ? '<img class="sold-ribbon" src="/assets/pages/img/icons/sold.png" alt="Sold">' : ''}

                    <div class="slider" data-current-slide="0">
                        <span class="new-tag" style="font-size: 10px !important;">NEW</span>

                        <div class="favorite-icons">
                            <img src="/assets/pages/img/icons/ribbon.png" width="25px" alt="">
                        </div>

                        <a href="/parts/${slug}/${p.part_id}">
                            <div class="slides">${imgHTML}</div>
                        </a>

                        <div class="arrows">
                            <span class="arrow-left">
                                <img src="/assets/pages/img/icons/left-chevron.png" width="17px" alt="">
                            </span>
                            <span class="arrow-right">
                                <img src="/assets/pages/img/icons/right-arrow-angle.png" width="17px" alt="">
                            </span>
                        </div>

                        <div class="slider-dots">
                            ${
                                p.images
                                    ? p.images.split(",").map(() => `<span class="dot"></span>`).join("")
                                    : `<span class="dot active"></span>`
                            }
                        </div>
                    </div>

                    <div class="car-info" style="text-align:left; font-size:16px;">
                        <h2 style="
                            margin:20px 0px 30px 15px;
                            font-size:23px !important;
                            letter-spacing:1px;
                            color:#000;
                            font-weight:500;">
                            ${p.part_name}
                        </h2>

                        <p>
                            <img src="/assets/pages/img/icons/license-plate.png" alt="">
                            &nbsp;Part No
                            <span style="padding-left:40px;">${p.part_number || ""}</span>
                        </p>

                        <p>
                            <img src="/assets/pages/img/icons/settings.png" alt="">
                            &nbsp;Condition
                            <span style="padding-left:35px;">${p.condition || ""}</span>
                        </p>

                        <p>
                            <img src="/assets/pages/img/icons/shopping.png" alt="">
                            &nbsp;Tag
                            <span style="padding-left:68px;">${p.tagged_with_easa_form_1 || ""}</span>
                        </p>

                        <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;text-transform: capitalize">
                        ${p.price_label === 'call'
                            ? 'Call For Price'
                            : `${p.currency}${parseFloat(p.price).toFixed(2)}`
                        }
                    </h2>


                        <div class="button-group">
                            <button onclick="window.location.href='/parts/${slug}/${p.part_id}'">
                                More Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        initSliders();
    })
    .catch(err => console.log("Fetch error:", err));
}


// =======================================
// SLIDER INITIALIZER
// =======================================

function initSliders() {
    document.querySelectorAll(".slider").forEach(slider => {
        let slides = slider.querySelectorAll(".slides img");
        let left = slider.querySelector(".arrow-left");
        let right = slider.querySelector(".arrow-right");

        let index = 0;

        function show() {
            slides.forEach((s, i) => s.style.display = i === index ? "block" : "none");
        }

        if (!slides.length) return;

        show();

        left.onclick = () => { index = (index - 1 + slides.length) % slides.length; show(); };
        right.onclick = () => { index = (index + 1) % slides.length; show(); };
    });
}


// =======================================
// ON PAGE LOAD
// =======================================
document.addEventListener("DOMContentLoaded", () => {

    // === 1. Parse URL search param if exists ===
    const params = new URLSearchParams(window.location.search);
    if (params.has("name") && !searchKeyword) {
        searchKeyword = params.get("name"); // get the search term from URL
    }

    // === 2. Attach checkbox listeners ===
    document.querySelectorAll(".filter-input").forEach(inp => {
        inp.addEventListener("change", () => {
            updateFilters(inp.dataset.filterType, inp.value);
        });
    });

    // === 3. Attach filter-link listeners ===
    document.querySelectorAll(".filter-link").forEach(a => {
        a.addEventListener("click", e => {
            e.preventDefault();
            updateFilters(a.dataset.filterType, a.dataset.filterValue);
        });
    });

    // === 4. Price range listeners ===
    document.querySelectorAll(".range-input").forEach(inp => {
        inp.addEventListener("input", () => updateRangeFilter(inp.id, inp.value));
    });


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

        // Function to update the slide position
        function updateSlide() {
            const slideWidth = slider.clientWidth;
            if (totalSlides > 1) {
                slidesContainer.style.transition = 'transform 0.5s ease'; // Enable smooth transition for multiple slides
                slidesContainer.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
            } else {
                slidesContainer.style.transition = 'none'; // No transition if there's only 1 image
            }

            // Update dots to reflect the active slide
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        // Left and Right arrow event listeners
        leftArrow.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlide();
        });

        rightArrow.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlide();
        });

        // Dots click event listener
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateSlide();
            });
        });

        // Ensure slides have the correct width on page load and window resize
        window.addEventListener('resize', updateSlide);
        updateSlide(); // Initial update
    });
</script>

       <script>
        function searchParts() {
            const country = document.getElementById('country').value;
            const keyword = document.getElementById('keyword').value;
            const location = document.getElementById('location').value;
            
            alert(`Searching for parts in ${country || 'all countries'} with keyword '${keyword}' in '${location || 'all locations'}'.`);
        }
    </script>
    
    <script>
        function initSliders() {
    document.querySelectorAll(".slider").forEach((slider) => {
        let slides = slider.querySelectorAll(".slides img");
        let dots = slider.querySelectorAll(".slider-dots .dot");
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? "block" : "none";
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });
        }

        slider.querySelector(".arrow-left").addEventListener("click", () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        });

        slider.querySelector(".arrow-right").addEventListener("click", () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        });

        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        showSlide(currentSlide);
    });
}

    </script>
    
<script>
   document.addEventListener("DOMContentLoaded", function () {
    let partInput = document.getElementById("part_number");
    let searchBar = document.querySelector(".search-bar");

   if (!partInput || !searchBar) return;

    // Create and append dropdown
    let dropdown = document.createElement("div");
    dropdown.classList.add("dropdown-list1");
    searchBar.appendChild(dropdown);

    partInput.addEventListener("input", function () {
        let query = partInput.value.trim();
        
        if (query.length < 2) {
            dropdown.innerHTML = "";
            dropdown.style.display = "none";
            return;
        }

        let formData = new FormData();
        formData.append("part_number", query);

        fetch("fetch_parts_by_region.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            dropdown.innerHTML = "";
            if (data.length === 0) {
                dropdown.style.display = "none";
                return;
            }

            dropdown.style.display = "block";

            data.forEach(part => {
                let item = document.createElement("div");
                item.classList.add("dropdown-item1");
                item.textContent = `${part.part_number} - ${part.part_name}`;
                item.dataset.partId = part.part_id;
                item.dataset.partName = part.part_name;

                item.addEventListener("click", function () {
                    partInput.value = part.part_number;
                    dropdown.style.display = "none";
                    fetchByParts(part.part_number); // Ensure fetchByParts function exists
                });

                dropdown.appendChild(item);
            });
        })
        .catch(error => console.error("Error fetching parts:", error));
    });

    // Hide dropdown when clicking outside the search bar
    document.addEventListener("click", function (event) {
        if (!searchBar.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
});


function fetchByParts(partNumber) {
    let formData = new FormData();
    if (partNumber) formData.append("part_number", partNumber);

    fetch("fetch_parts_by_region.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            let listingCards = document.querySelector(".listing-cards");
            listingCards.innerHTML = "";

            if (data.length === 0) {
                listingCards.innerHTML = "<p>No parts available.</p>";
                return;
            }

            data.forEach((part) => {
                let imagesHTML = part.images.length
                    ? part.images.map((img) => `<img src="vendors/${img}" alt="Part Image">`).join("")
                    : `<img src="/assets/pages/img/products/default-part.png" alt="Default Image">`;

                let partHTML = `
                <div class="car-card">
                 ${part.is_sold == 1 ? '<img class="sold-ribbon" src="/assets/pages/img/icons/sold.png" alt="Sold">' : ''}
                    <div class="slider" data-current-slide="0">
                        <span class="new-tag" style="font-size: 10px !important;">NEW</span>
                        <div class="favorite-icons">
                            <img src="/assets/pages/img/icons/ribbon.png" width="25px" alt="">
                        </div>
                        <a href='/parts_details.php?part_id=${part.part_id}'>
                            <div class="slides">${imagesHTML}</div>
                        </a>
                        <div class="arrows">
                            <span class="arrow-left"><img src="/assets/pages/img/icons/left-chevron.png" width="17px" alt=""></span>
                            <span class="arrow-right"><img src="/assets/pages/img/icons/right-arrow-angle.png" width="17px" alt=""></span>
                        </div>
                        <div class="slider-dots">
                            ${part.images.map(() => `<span class="dot"></span>`).join("")}
                        </div>
                    </div>
                    <div class="car-info" style="text-align: left; font-size: 16px;">
                        <h2 style="margin:20px 0px 30px 15px; font-size: 23px !important; letter-spacing: 1px; color: #000; font-weight: 500;">
                            ${part.part_name}
                        </h2>
                        <p><img src="/assets/pages/img/icons/license-plate.png" alt="">&nbsp;Part No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;${part.part_number}</p>
                        <p><img src="/assets/pages/img/icons/settings.png" alt="">&nbsp;Condition &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;${part.condition}</p>
                        <p><img src="/assets/pages/img/icons/shopping.png" alt="">&nbsp;Tag &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${part.tagged_with_easa_form_1}</p>
                        <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500; text-transform: capitalize">
                            ${part.price_label === 'call'
                                ? 'Call For Price'
                                : `${part.currency}${parseFloat(part.price).toFixed(2)}`
                            }
                        </h2>

                        <div class="button-group">
                            <button onclick="window.location.href='parts_details.php?part_id=${part.part_id}';">More Details</button>
                        </div>
                    </div>
                </div>`;
                listingCards.innerHTML += partHTML;
            });

            initSliders();
        })
        .catch((error) => console.error("Error fetching parts:", error));
}


    </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const labels = document.querySelectorAll(".tooltip-container");

    labels.forEach(label => {
        label.addEventListener("mouseenter", function () {
            const tooltip = this.querySelector(".tooltip");
            tooltip.innerText = this.getAttribute("data-description");
        });
    });
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".clear-filters-btn").addEventListener("click", function () {
        // Clear all filters (assuming they are stored in the URL parameters)
        const url = new URL(window.location.href);
        url.search = ""; // Remove all query parameters
        window.location.href = "/parts"; // Redirect to the parts page
    });
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

    // ✅ Add universal "Search all for..." suggestion
    // suggestions += `
    //   <li class="suggestion-item search-all" data-url="/aircraft?model=${encodeURIComponent(query)}">
    //     <div class="flex space-x-2">
    //       <span class="type-tag tag-all bg-blue-600 text-white px-2 py-1 rounded text-xs">Search</span>
    //       <span>Search all for "<strong>${query}</strong>"</span>
    //     </div>
    //   </li>
    // `;

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
</html>