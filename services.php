<?php include 'track_page.php'; ?>

<?php
require 'config.php';
?>
<html lang="en">
<head>
  <meta charset="utf-8">
 <title>Services</title>

<!-- Responsive and Compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!-- SEO Meta -->
<meta name="description" content="Explore top-rated aviation services on Flying411. From certified flight instructors to avionics shops and maintenance, find everything you need for your aircraft.">
<meta name="keywords" content="aviation services, flight instructors, flight schools, engine shops, avionics, maintenance shops, local aircraft mechanics, Flying411 services, aviation support">
<meta name="author" content="Flying411">

<!-- Open Graph Meta (for Social Sharing) -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="Find Aviation Services - Flight Instructors, Maintenance, and More | Flying411">
<meta property="og:description" content="Discover trusted aviation service providers including flight schools, instructors, and aircraft maintenance on Flying411.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png"> <!-- Replace with actual image -->
<meta property="og:url" content="https://flying411.com/services.php">


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
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page -->  
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
            margin: auto;
            padding: 20px 0;
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
       font-family: "EB Garamond", serif!important;
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
      color: black !important;
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
  /*    display: flex !important;*/
      /*width: 300% !important; */
  /*    transition: transform 0.4s ease !important;*/
  /*height: 100% !important*/
  ;  }
    .slides img {
      width: 33.56% !important;
      height: 100% !important;
      object-fit: cover !important;
    }
    .header .header-navigation ul li a{
        color:white !important;
             font-family: "EB Garamond", serif !important;
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
 @media (max-width: 1024px) {
 .header .header-navigation ul li a{
        color:black !important;
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
            background: url('/assets/pages/img/products/services.jpg') center center/cover no-repeat;
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
            width: 100%;
            max-width: 900px;
            position: relative;
            z-index: 2;
        }

        .search-bar select, .search-bar input, .search-bar button {
            padding: 15px;
            font-size: 1.5rem;
            border: none;
            border-radius: 30px !important;
            
        }

        .search-bar select {
            flex: 1;
    border: 1px solid white;
    color: black;
    border-radius: 31px;
    max-width: 522px;
    appearance: none;
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
                width: 80%;
            }
        }
.filter-section {
    width: 250px;
    border: 1px solid #ddd;
    padding: 15px;
    background: #fff;
   
}

.filter-item h4 {
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    padding: 10px;
    background: #f1f1f1;
    margin: 0;
    border: 1px solid #ddd;
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
    display: none;
    padding: 10px;
    /*max-height: 200px;*/
    overflow-y: auto;
    border: 1px solid #ddd;
    background: #fff;
}

.filter-content label {
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
.selected-filters span:hover{
    cursor: pointer;
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
.breed-list {
            flex: 1;
            min-width: 250px;
            padding-right: 20px;
        }
        .breed-list h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .breed-list ul {
            list-style: none;
            padding: 0;
        }
        .breed-list li {
            font-size: 18px;
            margin-bottom: 15px;
        }
                .breed-list li a{
                    color: #424d5c;
                    width: 60%;
                    text-align: center;
                }
        .map-container {
            flex: 2;
            min-width: 300px;
        }
        iframe {
            width: 100%;
            height: 350px;
            border: none;
        }
        .upload-container {
            margin-top: 20px;
            text-align: center;
            width: 100%;
        }
        @media (max-width: 768px) {
            .container {
                align-items: center;
            }
            .breed-list, .map-container {
                width: 100%;
                text-align: center;
            }
            .search-bar select{
                margin:auto;
                width:90%;
            }
        }
         .filter-bar {
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .filter-bar select, .filter-bar input, .filter-bar button {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
                width: 40%;
    padding: 14px
        }
        .service-grid {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            column-gap: 22px;
            row-gap: 22px;
            width: 100%;
        }
        .service-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            font-size: 14px;
            width:30% !important;
        }
        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .service-card .content {
            padding: 15px;
        }
        .service-card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .rating {
            color: gold;
        }
        .contact-info {
            font-size: 14px;
            margin: 10px 0;
        }
        .inquiry-btn {
            display: block;
            text-align: center;
            background: white;
            color: black;
            padding: 8px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            border:1px solid black;
        }
         .inquiry-btn:hover{
             background-color: lightgray;
             color: white;
         }
         
         .suggestions-box {
    position: absolute;
    background: white;
   
    max-width: 300px;
    z-index: 1000;
    color: black;
    top: 53px;
    width: 29%;
}
.suggestion-item {
    padding: 13px;
    font-size: 14px;
    cursor: pointer;
}
.suggestion-item:hover {
    background: #f0f0f0;
}


 .map-container {
            width: 100%;
            height: 500px;
            position: relative;
        }
        #map {
            width: 100%;
            height: 80%;
        }
        .search-container {
            margin-bottom: 10px;
        }
        
        
        
        @media (min-width: 1200px) {
    .container, .container-lg, .container-md, .container-sm {
        max-width: 1250px !important;
    }
}
@media (min-width: 1025px) {
    .container, .container-lg, .container-md, .container-sm {
        max-width: 1250px !important;
    }
    .header .top-cart-block {
    margin-left: 5px !important;
}
.site-logo img {
    border-radius: 50% !important;
    margin-right: 28px !important;
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
                width: 80%;
            }
        }
        
         @media (max-width: 560px) {
     
    .header-navigation ul li a{
      color: black !important;
    }
    .header-navigation ul li a:hover{
      background-color: transparent;
      color: #555;
    }
    .search-container input{
        width:190px !important;
    }
    }
    .headers {
            position: relative;
            width: 100%;
            height: 80vh;
            /*background: url('/assets/pages/img/products/aircraft.jpg') center center/cover no-repeat;*/
            background: url('/assets/pages/img/products/aircraft1.jpg') center center/cover no-repeat;
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
            width: 100%;
            max-width: 900px;
            position: relative;
            z-index: 2;
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
      height: auto !important;
      /*height: 250px !important;*/
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

   /* Container for the slides */
.slides {
    display: flex !important;
    transition: transform 0.4s ease !important;
    height: 100% !important;
}

/* Style for each image inside the slide */
.slides img {
    width: 100% !important;  /* Each image takes full width */
    height: 320px !important;
    /*object-fit: cover !important;*/
}

    .header .header-navigation ul li a{
        color:white !important;
             font-family: "EB Garamond", serif !important;
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
 @media (max-width: 1024px) {
 .header .header-navigation ul li a{
        color:black !important;
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
  display: flex;
  justify-content: space-between;
  text-align: right;
}

.slider {
    position: relative;
    overflow: hidden;
    width: 100%;
}

.slides {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slides img {
    min-width: 100%; /* Ensures each image takes up full slider width */
    /*object-fit: cover;*/
}




/* Fix existing bubble look */
.service-filters {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    /*gap: 10px;*/
}

.service-filters li {
    position: relative;
}

.service-bubble {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
    font-size: 14px;
}

.service-bubble:hover,
.service-bubble.active {
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}


.dropdown-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 35px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 10px;
    z-index: 99;
}




.dropdown-container:hover .dropdown-menu {
    display: flex;
    width: 60%;
    flex-wrap: wrap;
    padding: 20px;
}



 #pagination {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 5px;
    }

    #pagination button {
        padding: 14px 18px;
        border: none;
        border-radius: 5px;
        background-color: #fff;
        color: #333;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: bold;
    }

    #pagination button:hover {
        background-color: #007bff;
        color: white;
        transform: scale(1.05);
    }

    #pagination button.active {
        background-color: #007bff;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        transform: scale(1.1);
    }
    .dropdown-menu li{
        display: block !important;
    }
    
    .dropdown-menu .service-bubble1{
    border: 1px solid #ddd !important;
    padding: 10px 20px;
    widows: 100%;
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

    </style>
</head>
<!-- Head END -->

<!-- Body BEGIN -->
<body class="ecommerce">
  <!-- BEGIN TOP BAR -->

  <!-- END TOP BAR -->

  <!-- BEGIN HEADER -->
  <div class="header" style="position: absolute; width:100%">
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
    <?php
session_start(); // Start the session to check login status
?>
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

    <a href="/login"  class="mobile-login">LOGIN / REGISTER</a>
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
    <h1>WELCOME TO AVIATION SERVICES</h1>
    <p style="font-size: 25px; color: white;">Find Certified Flight Instructors, Schools, and More</p>
   <div class="search-bar">
    <!-- Service Type Filter (Dynamically Loaded) -->
  <select name="service_type" id="service_type">
    <option value="">All Service Types</option>
    <option data-service="Flight Instructor">Flight Instructor</option>
    <option data-service="flight_school">Flight School</option>
    <option data-service="Engine Shop">Engine Shop</option>
    <option data-service="Avionics Shop">Avionics Shop</option>
    <option data-service="Maintenance Shop">Maintenance Shop</option>
    <option data-service="Local Mechanic">Local Mechanic</option>
</select>

</div>

    </div>
</div>



<div class="container" style="margin-top:50px;">
    <div class="breed-list">
       
        <h2>Popular Services </h2>
      <ul class="service-filters">

    <li><a href="#" class="service-bubble" data-service="Flight Instructor">Flight Instructor</a></li> <br>

    <!-- Flight School With Dropdown -->
    <li class="dropdown-container">
        <a href="#" class="service-bubble dropdown-toggle">
            Flight School
        </a>

        <ul class="dropdown-menu">
            <li><a href="#" style="Width:100%;" class="service-bubble sub-filter" data-subservice="141">Part 141</a></li>
            <li><a href="#" style="Width:100%;" class="service-bubble sub-filter" data-subservice="61">Part 61</a></li>
            <li><a href="#" style="Width:100%;" class="service-bubble sub-filter" data-subservice="141,61">Both (141 & 61)</a></li>
        </ul>
    </li>

    <br>

    <li><a href="#" class="service-bubble" data-service="Engine Shop">Engine Shop</a></li><br>
    <li><a href="#" class="service-bubble" data-service="Avionics Shop">Avionics Shop</a></li><br>
    <li><a href="#" class="service-bubble" data-service="Maintenance Shop">Maintenance Shop</a></li><br>
    <li><a href="#" class="service-bubble" data-service="Local Mechanic">Local Mechanic</a></li>

</ul>


    </div>
    
    <div class="map-container">
        <div class="search-container">
        <input id="searchBox" type="text" placeholder="Search for a location" style="width: 300px; padding: 10px;border-radius:20px;border:1px solid lightgray">
        <button onclick="searchLocation()" style="padding:8px 12px; background-color:#e30b17; color:white; border:none;border-radius:20px;">Search</button>
        <a href="" class="filterbtn" style="background-color:white; padding:8px 12px; color:black; border:1px solid black; float:right; border-radius:4px;">Clear Filters</a>
    </div>
        <div id="map" style="border-radius:20px;"></div>
    </div>
</div>
 <div class="container" style="margin-top: 10px;border-radius:20px;">
    <div id="serviceGrid" class="service-grid" style="border-radius:20px;">
        <!-- Cards will be injected here dynamically -->
    </div>

    <!-- Pagination controls -->
    <div id="pagination" style="margin-top: 20px; text-align: center;"></div>
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

    <!-- BEGIN fast view of a product -->
    <div id="product-pop-up" style="display: none; width: 700px;">
            <div class="product-page product-pop-up">
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-3">
                  <div class="product-main-image">
                    <img src="/assets/pages/img/products/product1.jpg" alt="Cool green dress with red bell" class="img-responsive">
                  </div>
                  <div class="product-other-images">
                    <a href="javascript:;" class="active"><img alt="Berry Lace Dress" src="/assets/pages/img/products/product2.jpg"></a>
                    <a href="javascript:;"><img alt="Berry Lace Dress" src="/assets/pages/img/products/air1.jpg"></a>
                    <a href="javascript:;"><img alt="Berry Lace Dress" src="/assets/pages/img/products/air3.jpg"></a>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-9">
                  <h2>Lorem ipsum dolor sit amet.</h2>
                  <div class="price-availability-block clearfix">
                    <div class="price">
                      <strong><span>Aircraft</span>Details</strong>
                      
                    </div>
                  </div>
                  <div class="description">
                    <p>Lorem ipsum dolor ut sit ame dolore  adipiscing elit, sed nonumy nibh sed euismod laoreet dolore magna aliquarm erat volutpat Nostrud duis molestie at dolore.</p>
                  </div>
                  <div class="product-page-cart">
                    <div class="product-quantity">
                        <input id="product-quantity" type="text" value="1" readonly name="product-quantity" class="form-control input-sm">
                    </div>
                    <button class="btn btn-primary" type="submit">Chat with Dealer</button>
                    <a href="#" class="btn btn-default">More details</a>
                  </div>
                </div>

                <div class="sticker sticker-sale"></div>
              </div>
            </div>
    </div>
    
    <!-- END fast view of a product -->

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
    <script src="/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <script src="/assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
    <script src="/assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
    <script src='/assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
    <script src="/assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script><!-- Quantity -->

    <script src="/assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/bs-carousel.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initImageZoom();
            Layout.initTouchspin();
            Layout.initTwitter();
        });
    </script>



  <script>
function toggleFilter(filterId, element) {
    let filterContent = document.getElementById(filterId);
    let arrow = element.querySelector('.arrow');
    
    if (filterContent.style.display === "none" || filterContent.style.display === "") {
        filterContent.style.display = "block";
        arrow.innerHTML = "&#9652;";
    } else {
        filterContent.style.display = "none";
        arrow.innerHTML = "&#9662;";
    }
}


</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const clearBtn = document.querySelector(".clear-filters-btn");

    if (!clearBtn) {
        console.warn("Clear Filters button not found on this page.");
        return;
    }

    clearBtn.addEventListener("click", function () {
        // Clear all filters (assuming they are stored in the URL parameters)
        const url = new URL(window.location.href);
        url.search = ""; // Remove all query parameters
        window.location.href = "/services"; // Redirect to the parts page
    });
});
</script>





<script>
let map;
let markers = []; // store markers
let servicesData = [];
let currentPage = 1;
const servicesPerPage = 20;

// Initialize Google Map
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 39.8283, lng: -98.5795 },
        zoom: 4
    });
}

// Fetch all services
async function fetchServices() {
    try {
        const response = await fetch("fetch_services.php");
        const services = await response.json();

        if (services.error) {
            document.getElementById("serviceGrid").innerHTML = `<p>Error: ${services.error}</p>`;
            return;
        }

        servicesData = services;
        currentPage = 1;
        renderPage();
    } catch (error) {
        console.error("Error fetching services:", error);
    }
}

// Render page
function renderPage() {
    const serviceGrid = document.getElementById("serviceGrid");
    serviceGrid.innerHTML = "";

    const startIndex = (currentPage - 1) * servicesPerPage;
    const endIndex = startIndex + servicesPerPage;
    const pageServices = servicesData.slice(startIndex, endIndex);

    if (pageServices.length === 0) {
        serviceGrid.innerHTML = "<p>No services available.</p>";
        clearMarkers();
        return;
    }

    pageServices.forEach(service => {
        let photos = "default-service.jpg";
        try {
            let parsedPhotos = JSON.parse(service.photos);
            if (Array.isArray(parsedPhotos) && parsedPhotos.length > 0) {
                photos = parsedPhotos[0];
            }
        } catch (e) {}

        serviceGrid.innerHTML += `
<div class="car-card">

    <a href="services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}">
        <div class="slides">
            <img src="vendors/uploads/${photos}" alt="Service Image"
                 onerror="this.onerror=null; this.src='/vendors/uploads/services/default-service.jpg';">
        </div>
    </a>

    <div class="car-info" style="text-align: left; font-size: 16px;">
 <div>
     
        <!-- 2. Service Type -->
        ${service.service_type ? `
        <p style="display:flex;justify-content:space-between;">
           
          <span style="width:120px; text-align:left;">Flight School</span>
            <span style="padding-left: 20px;">${service.business_name}</span>
        </p>` : ''}


         <!-- 3. Part -->
        ${service.school_type ? `
        <p>
           
            &nbsp;Part
            <span style="padding-left: 15px;">${service.school_type}</span>
        </p>` : ''}
        <!-- 3. Instruction Rate -->
        ${service.instruction_rate ? `
        <p>
           
            &nbsp;Instruction Rate
            <span style="padding-left: 15px;">$${service.instruction_rate}</span>
        </p>` : ''}

        <!-- 4. Ground Rate -->
        ${service.ground_rate ? `
        <p>
          
            &nbsp;Ground Rate
            <span style="padding-left: 35px;">$${service.ground_rate}</span>
        </p>` : ''}

        <!-- 5. Hourly Rate -->
        ${service.hourly_rate ? `
        <p>
          
            &nbsp;Hourly Rate
            <span style="padding-left: 45px;">$${service.hourly_rate}</span>
        </p>` : ''}

       
        ${service.country ? `
        <p>
            <span>
            &nbsp;Location
            </span>
            <span style="padding-left: 55px;">${service.country}</span>
        </p>` : ''}
 </div>
        <div class="button-group">
            <button onclick="window.location.href='services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}';">
                More Details
            </button>
        </div>

    </div>
</div>
`;
    });

    // Add markers only for current page
    addMarkersForPage(pageServices);

    renderPagination();
}

// Clear old markers
function clearMarkers() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
}

// Add markers for current page
function addMarkersForPage(pageServices) {
    clearMarkers();
    const geocoder = new google.maps.Geocoder();
    let delay = 0;

    pageServices.forEach(service => {
        if (service.country && service.country.trim() !== "") {
            setTimeout(() => {
                geocoder.geocode({ address: service.country }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: service.service_name || "Service Location"
                        });
                        markers.push(marker);
                    }
                });
            }, delay);

            delay += 250; // avoid hitting Google API limit
        }
    });
}

// Render pagination
function renderPagination() {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    const totalPages = Math.ceil(servicesData.length / servicesPerPage);

    if (totalPages <= 1) return;

    function createButton(text, page, disabled = false) {
        const btn = document.createElement("button");
        btn.textContent = text;
        btn.disabled = disabled;
        btn.addEventListener("click", () => {
            currentPage = page;
            renderPage();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
        if (disabled) btn.style.cursor = "default";
        if (page === currentPage) btn.style.fontWeight = "bold";
        return btn;
    }

    pagination.appendChild(createButton("«", currentPage - 1, currentPage === 1));

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        pagination.appendChild(createButton(i, i));
    }

    pagination.appendChild(createButton("»", currentPage + 1, currentPage === totalPages));
}

// Initialize everything
window.onload = function() {
    initMap();
    fetchServices();
};
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const serviceLinks = document.querySelectorAll(".service-bubble");
    const subLinks = document.querySelectorAll(".sub-filter");
    const serviceGrid = document.getElementById("serviceGrid");
    const serviceTypeSelect = document.getElementById("service_type");
    const pagination = document.getElementById("pagination");
    const servicesPerPage = 20;

    let map;
    let markers = [];
    let servicesData = [];
    let currentPage = 1;

    // Initialize map
    async function initMap() {
        const defaultLocation = { lat: 39.8283, lng: -98.5795 };
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 4
        });
        fetchAndAddAllServices();
    }

    async function fetchAndAddAllServices() {
        try {
            const res = await fetch("fetch_services.php");
            servicesData = await res.json();
            currentPage = 1;
            renderPage();
        } catch (e) { console.error(e); }
    }
function fetchFilteredServices(serviceType = "", subservice = "", location = "") {
    const postData = {};
    if (serviceType) postData["services"] = JSON.stringify([serviceType]);
    if (subservice) postData["subservice"] = subservice;
    if (location) postData["location"] = location;

    fetch("fetch_checkbox_services.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(postData)
    })
    .then(res => res.json())
    .then(services => {
        servicesData = services;
        currentPage = 1;
        renderPage();
    })
    .catch(err => console.error(err));
} // <-- Correct ending

    // Handle main service clicks
    serviceLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const type = link.dataset.service;
            if (!link.classList.contains("sub-filter")) {
                fetchFilteredServices(type);
            }
        });
    });

    // Handle Flight School sub-filter clicks
    subLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const sub = link.dataset.subservice;
            fetchFilteredServices("flight_school", sub);
        });
    });

    // Dropdown filter
    serviceTypeSelect.addEventListener("change", function () {
        const type = this.options[this.selectedIndex].getAttribute("data-service") || "";
        if (type) fetchFilteredServices(type);
        else fetchAndAddAllServices();
    });

    window.initMap = initMap;

    // Render grid & map
    function renderPage() {
        serviceGrid.innerHTML = "";
        clearMarkers();
        const startIndex = (currentPage - 1) * servicesPerPage;
        const pageServices = servicesData.slice(startIndex, startIndex + servicesPerPage);

        if (!pageServices.length) {
            serviceGrid.innerHTML = "<p>No services found.</p>";
            return;
        }

        const geocoder = new google.maps.Geocoder();

        pageServices.forEach(service => {
            let photos = "default-service.jpg";
            try {
                const parsed = JSON.parse(service.photos);
                if (Array.isArray(parsed) && parsed[0]) photos = parsed[0];
            } catch(e){}

            serviceGrid.innerHTML += `
<div class="car-card">

    <a href="services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}">
        <div class="slides">
            <img src="vendors/uploads/${photos}" alt="Service Image"
                 onerror="this.onerror=null; this.src='/vendors/uploads/services/default-service.jpg';">
        </div>
    </a>

    <div class="car-info" style="text-align: left; font-size: 16px;">
 <div>
      
        <!-- 2. Service Type -->
        ${service.service_type ? `
        <p style="display:flex;justify-content:space-between;">
         
            <span style="width:120px;text-align:left;">Flight School</span>
            <span style="padding-left: 20px;">${service.business_name}</span>
        </p>` : ''}

  <!-- 3. Part -->
        ${service.school_type ? `
        <p>
           
            &nbsp;Part
            <span style="padding-left: 15px;">${service.school_type}</span>
        </p>` : ''}
        <!-- 3. Instruction Rate -->
        ${service.instruction_rate ? `
        <p>
            
            &nbsp;Instruction Rate
            <span style="padding-left: 15px;">$${service.instruction_rate}</span>
        </p>` : ''}

        <!-- 4. Ground Rate -->
        ${service.ground_rate ? `
        <p>
            
            &nbsp;Ground Rate
            <span style="padding-left: 35px;">$${service.ground_rate}</span>
        </p>` : ''}

        <!-- 5. Hourly Rate -->
        ${service.hourly_rate ? `
        <p>
           
            &nbsp;Hourly Rate
            <span style="padding-left: 45px;">$${service.hourly_rate}</span>
        </p>` : ''}

       
        <!-- Location -->
        ${service.country ? `
        <p>
            <span>
            
            &nbsp;Location
            </span>
            <span style="padding-left: 55px;">${service.country}</span>
        </p>` : ''}
 </div>
        <div class="button-group">
            <button onclick="window.location.href='services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}';">
                More Details
            </button>
        </div>

    </div>
</div>
`;

            // Add marker
            if (service.country) {
                geocoder.geocode({address: service.country}, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: service.business_name
                        });
                        const infoWindow = new google.maps.InfoWindow({
                            content: `<div><strong>${service.business_name}</strong></div>`
                        });
                        marker.addListener("click", () => infoWindow.open(map, marker));
                        markers.push(marker);
                    }
                });
            }
        });

        renderPagination();
    }

    function renderPagination() {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    const totalPages = Math.ceil(servicesData.length / servicesPerPage);

    if (totalPages <= 1) return;

    function createButton(text, page, disabled = false) {
        const btn = document.createElement("button");
        btn.textContent = text;
        btn.disabled = disabled;
        btn.addEventListener("click", () => {
            currentPage = page;
            renderPage();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
        if (disabled) btn.style.cursor = "default";
        if (page === currentPage) btn.style.fontWeight = "bold";
        return btn;
    }

    pagination.appendChild(createButton("«", currentPage - 1, currentPage === 1));

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        pagination.appendChild(createButton(i, i));
    }

    pagination.appendChild(createButton("»", currentPage + 1, currentPage === totalPages));
}

    function clearMarkers() {
        markers.forEach(m => m.setMap(null));
        markers = [];
    }
});

    </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const serviceLinks = document.querySelectorAll(".breed-list ul li a");
    const serviceGrid = document.getElementById("serviceGrid");
    const serviceTypeSelect = document.getElementById("service_type");
    const pagination = document.getElementById("pagination");
    const servicesPerPage = 20;

    let map;
    let markers = [];
    let servicesData = []; // All fetched services
    let currentPage = 1;

    // Initialize Google Map
    async function initMap() {
        const defaultLocation = { lat: 39.8283, lng: -98.5795 }; // Center of USA
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 4
        });

        // Fetch all services initially
        fetchAndAddAllServices();
    }

    // Fetch all services without filter
    async function fetchAndAddAllServices() {
        try {
            const response = await fetch("fetch_services.php");
            const services = await response.json();
            servicesData = services;
            currentPage = 1;
            renderPage();
        } catch (error) {
            console.error("Error fetching services:", error);
        }
    }

    // Fetch filtered services
  function fetchFilteredServices(serviceType = "", location = "") {
    const postData = {};
    if (serviceType) postData["services"] = JSON.stringify([serviceType]);
    if (location) postData["location"] = location;

    fetch("fetch_checkbox_services.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(postData)
    })
    .then(response => response.json())
    .then(services => {
        servicesData = services;
        currentPage = 1;
        renderPage();
    })
    .catch(error => console.error("Error fetching services:", error));
}


    // Dropdown filter
    serviceTypeSelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const type = selectedOption.getAttribute("data-service") || "";
        if (type) fetchFilteredServices(type);
        else fetchAndAddAllServices();
    });

    window.initMap = initMap;



    // Render grid and map for current page
    function renderPage() {
        serviceGrid.innerHTML = "";
        clearMarkers();

        const startIndex = (currentPage - 1) * servicesPerPage;
        const endIndex = startIndex + servicesPerPage;
        const pageServices = servicesData.slice(startIndex, endIndex);

        if (!pageServices.length) {
            serviceGrid.innerHTML = "<p>No services found.</p>";
            return;
        }

        const geocoder = new google.maps.Geocoder();

        pageServices.forEach(service => {
            let photos = "default-service.jpg";
            try {
                const parsedPhotos = JSON.parse(service.photos);
                if (Array.isArray(parsedPhotos) && parsedPhotos.length > 0 && parsedPhotos[0] !== "") {
                    photos = parsedPhotos[0];
                }
            } catch (e) { console.error(e); }

            serviceGrid.innerHTML += `
<div class="car-card">

    <a href="services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}">
        <div class="slides">
            <img src="vendors/uploads/${photos}" alt="Service Image"
                 onerror="this.onerror=null; this.src='/vendors/uploads/services/default-service.jpg';">
        </div>
    </a>

    <div class="car-info" style="text-align: left; font-size: 16px;">
 <div>
       

        <!-- 2. Service Type -->
        ${service.service_type ? `
        <p style="display:flex;justify-content:space-between;">
           
            <span style="width:120px;text-align:left;">Flight School</span>
            <span style="padding-left: 20px;">${service.business_name}</span>
        </p>` : ''}

  <!-- 3. Part -->
        ${service.school_type ? `
        <p>
           
            &nbsp;Part
            <span style="padding-left: 15px;">${service.school_type}</span>
        </p>` : ''}
        <!-- 3. Instruction Rate -->
        ${service.instruction_rate ? `
        <p>
           
            &nbsp;Instruction Rate
            <span style="padding-left: 15px;">$${service.instruction_rate}</span>
        </p>` : ''}

        <!-- 4. Ground Rate -->
        ${service.ground_rate ? `
        <p>
          
            &nbsp;Ground Rate
            <span style="padding-left: 35px;">$${service.ground_rate}</span>
        </p>` : ''}

        <!-- 5. Hourly Rate -->
        ${service.hourly_rate ? `
        <p>
          
            &nbsp;Hourly Rate
            <span style="padding-left: 45px;">$${service.hourly_rate}</span>
        </p>` : ''}

       
        ${service.country ? `
        <p>
            <span>
            
            &nbsp;Location
            </span>
            <span style="padding-left: 55px;">${service.country}</span>
        </p>` : ''}
 </div>
        <div class="button-group">
            <button onclick="window.location.href='services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}';">
                More Details
            </button>
        </div>

    </div>
</div>
`;
            // Add marker for current page service
            if (service.country) {
                geocoder.geocode({ address: service.country }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: service.business_name || "Service Location"
                        });

                        const infoWindow = new google.maps.InfoWindow({
                            content: `<div style="font-weight:bold;">${service.service_name || "Service Location"}</div>`
                        });

                        marker.addListener("click", () => {
                            infoWindow.open(map, marker);
                            // Filter current servicesData by country
                            const filteredServices = servicesData.filter(s => s.country === service.country);
                            currentPage = 1; // reset to first page
                            renderPageWithData(filteredServices);
                        });


                        markers.push(marker);
                    } else {
                        console.error("Geocode failed: " + status);
                    }
                });
            }
        });

        renderPagination();
    }

function renderPageWithData(data) {
    const serviceGrid = document.getElementById("serviceGrid");
    serviceGrid.innerHTML = "";

    clearMarkers();

    const startIndex = (currentPage - 1) * servicesPerPage;
    const endIndex = startIndex + servicesPerPage;
    const pageServices = data.slice(startIndex, endIndex);

    if (!pageServices.length) {
        serviceGrid.innerHTML = "<p>No services found.</p>";
        return;
    }

    const geocoder = new google.maps.Geocoder();

    pageServices.forEach(service => {
        let photos = "default-service.jpg";
        try {
            const parsedPhotos = JSON.parse(service.photos);
            if (Array.isArray(parsedPhotos) && parsedPhotos.length > 0 && parsedPhotos[0] !== "") {
                photos = parsedPhotos[0];
            }
        } catch (e) {}

        serviceGrid.innerHTML += `
<div class="car-card">

    <a href="services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}">
        <div class="slides">
            <img src="vendors/uploads/${photos}" alt="Service Image"
                 onerror="this.onerror=null; this.src='/vendors/uploads/services/default-service.jpg';">
        </div>
    </a>

    <div class="car-info" style="text-align: left; font-size: 16px;">
 <div>
       
        <!-- 2. Service Type -->
        ${service.service_type ? `
        <p style="display:flex;justify-content:space-between;">
           
           <span style="width:120px;text-align:left;">Flight School</span>
            <span style="padding-left: 20px;">${service.business_name}</span>
        </p>` : ''}
  <!-- 3. Part -->
        ${service.school_type ? `
        <p>
           
            &nbsp;Part
            <span style="padding-left: 15px;">${service.school_type}</span>
        </p>` : ''}
        <!-- 3. Instruction Rate -->
        ${service.instruction_rate ? `
        <p>
           
            &nbsp;Instruction Rate
            <span style="padding-left: 15px;">$${service.instruction_rate}</span>
        </p>` : ''}

        <!-- 4. Ground Rate -->
        ${service.ground_rate ? `
        <p>
          
            &nbsp;Ground Rate
            <span style="padding-left: 35px;">$${service.ground_rate}</span>
        </p>` : ''}

        <!-- 5. Hourly Rate -->
        ${service.hourly_rate ? `
        <p>
          
            &nbsp;Hourly Rate
            <span style="padding-left: 45px;">$${service.hourly_rate}</span>
        </p>` : ''}

       
        ${service.country ? `
        <p>
            <span>
            
            &nbsp;Location
            </span>
            <span style="padding-left: 55px;">${service.country}</span>
        </p>` : ''}
 </div>
        <div class="button-group">
            <button onclick="window.location.href='services/${service.business_name.toLowerCase().replace(/ /g,'-')}/${service.id}';">
                More Details
            </button>
        </div>

    </div>
</div>
`;

        // Add markers for current page
        if (service.country) {
            geocoder.geocode({ address: service.country }, (results, status) => {
                if (status === "OK" && results[0]) {
                    const marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title: service.service_name || "Service Location"
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div style="font-weight:bold;">${service.service_name || "Service Location"}</div>`
                    });

                    marker.addListener("click", () => {
                        infoWindow.open(map, marker);
                        renderPageWithData(data); // click again
                    });

                    markers.push(marker);
                }
            });
        }
    });

    // Render pagination for filtered data
    renderPaginationWithData(data);
}

    // Pagination rendering
    function renderPagination() {
        pagination.innerHTML = "";
        const totalPages = Math.ceil(servicesData.length / servicesPerPage);
        if (totalPages <= 1) return;

        const createButton = (text, page) => {
            const btn = document.createElement("button");
            btn.textContent = text;
            if (page === currentPage) btn.classList.add("active");
            btn.disabled = page < 1 || page > totalPages;
            btn.addEventListener("click", () => {
                currentPage = page;
                renderPage();
                window.scrollTo({ top: 0, behavior: "smooth" });
            });
            return btn;
        };

        pagination.appendChild(createButton("«", currentPage - 1));

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        startPage = Math.max(1, endPage - 4);

        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(createButton(i, i));
        }

        pagination.appendChild(createButton("»", currentPage + 1));
    }

    // Clear map markers
    function clearMarkers() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
    }

    // Filter clicks
    serviceLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const type = this.dataset.service;
            fetchFilteredServices(type);
        });
    });

    // Dropdown filter
    serviceTypeSelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const type = selectedOption.getAttribute("data-service") || "";
        if (type) fetchFilteredServices(type);
        else fetchAndAddAllServices();
    });

    // window.initMap = initMap;
});
</script>

 

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmlHqr1dCDcSciN-_94-i3jUg5P-48j60&libraries=places&callback=initMap"></script>
    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const clearFilterBtn = document.querySelector(".filterbtn");

    clearFilterBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default link behavior
        location.reload(); // Refresh the page
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