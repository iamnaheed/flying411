<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="utf-8">
<title>Frequently Asked Questions (FAQ) | Flying411 Aviation Marketplace</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- SEO Meta -->
<meta name="description" content="Find answers to frequently asked questions about Flying411, including aircraft listings, parts, engines, seller inquiries, payments, and marketplace policies.">
<meta name="keywords" content="Flying411 FAQ, aviation marketplace help, aircraft sales questions, aircraft parts FAQ, aviation listings support">
<meta name="author" content="Flying411">

<!-- Open Graph (Social Sharing) -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="Flying411 FAQ – Answers to Common Aviation Marketplace Questions">
<meta property="og:description" content="Get quick answers to common questions about buying and selling aircraft, engines, and parts on Flying411.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://www.flying411.com/faq">
<meta property="og:image" content="https://www.flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">

<!-- Favicon / Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">

  <!-- Fonts START -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <link href="/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <link href="/assets/pages/css/animate.css" rel="stylesheet">
  <link href="/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">
  <link href="/assets/plugins/owl.carousel//assets/owl.carousel.css" rel="stylesheet">
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
  <link
  href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
  rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
  integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
  crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9EWL5C594"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9EWL5C594');
</script>
  <style>
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

    <div class="main">
      <div class="container" style="margin-top: 30px;">
        <!-- BEGIN SIDEBAR & CONTENT -->
        <div class="row margin-bottom-40">
          <!-- BEGIN SIDEBAR -->
          <!-- END SIDEBAR -->

          <!-- BEGIN CONTENT -->
          <div class="col-md-9 col-sm-9 col-lg-12">
            <h1>Frequently Asked Questions</h1>
            <div class="faq-page">
                            <div class="panel panel-default">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_1">
                                     1. Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_1" class="panel-collapse collapse  in">
                                  <div class="panel-body">
                                     Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-default">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_2">
                                     2. Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_2" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch   et.
                                     Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-success">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_3">
                                     3. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_3" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch   et.
                                     Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-warning">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_4">
                                     4. Wolf moon officia aute, non cupidatat skateboard dolor brunch ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_4" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-danger">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_5">
                                     5. Leggings occaecat craft beer farm-to-table, raw denim aesthetic ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_5" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-default">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_6">
                                     6. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_6" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et
                                  </div>
                               </div>
                            </div>
                            <div class="panel panel-default">
                               <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#accordion1_7">
                                     7. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft ?
                                     </a>
                                  </h4>
                               </div>
                               <div id="accordion1_7" class="panel-collapse collapse">
                                  <div class="panel-body">
                                     3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et
                                  </div>
                               </div>
                            </div>
            </div>
          </div>
          <!-- END CONTENT -->
        </div>
        <!-- END SIDEBAR & CONTENT -->
      </div>
    </div>

    <!-- BEGIN BRANDS -->
     <!-- BEGIN BRANDS -->
     <div class="brands">
      <div class="container">
            <div class="owl-carousel owl-carousel6-brands">
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/download.png" alt="canon" title="canon"></a>
              
              
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/images (3).png" alt="zara" title="zara"></a>
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/images.png" alt="canon" title="canon"></a>
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/download.png" alt="esprit" title="esprit"></a>
              
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/images (3).png" alt="next" title="next"></a>
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/images.png" alt="puma" title="puma"></a>
              <a href="shop-product-list.php"><img src="/assets/pages/img/brands/download.png" alt="zara" title="zara"></a>
            </div>
        </div>
    </div>
    <!-- END BRANDS -->

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
        <div class="col-md-3 col-sm-6 pre-footer-col">
          <h2>Quick Links</h2>
          <ul class="list-unstyled">
            <li></i> <a href="/">Home</a></li>
            <li></i> <a href="/aircraft">Aircrafts</a></li>
            <li> <a href="/parts">Parts</a></li>
            <li> <a href="/engine">Engines</a></li>
            <li> <a href="/services">Services</a></li>
            <li> <a href="/blogs">Articles</a></li>
            <li> <a href="/contacts">Contact Us</a></li>
          </ul>
        </div>
        <div class="col-md-3 col-sm-6 pre-footer-col">
          <h2>Information</h2>
          <ul class="list-unstyled">
            <li> <a href="javascript:;">Delivery Information</a></li>
            <li><a href="javascript:;">Customer Service</a></li>
            <li> <a href="javascript:;">Order Tracking</a></li>
            <li> <a href="javascript:;">Shipping &amp; Returns</a></li>
            <li> <a href="/contacts">Contact Us</a></li>
            <li> <a href="/blogs">Articles</a></li>
            <li></i> <a href="javascript:;">Payment Methods</a></li>
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
  
    <!-- END FOOTER -->

    <!-- Load javascripts at bottom, this will reduce page load time -->
    <!-- BEGIN CORE PLUGINS(REQUIRED FOR ALL PAGES) -->
    <!--[if lt IE 9]>
    <script src="/assets/plugins/respond.min.js"></script>  
    <![endif]-->  
    <script src="/assets/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>      
    <script src="/assets/corporate/scripts/back-to-top.js" type="text/javascript"></script>
    <script src="/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <script src="/assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
    <script src="/assets/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->

    <script src="/assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initTwitter();
        });
    </script>
    <!-- END PAGE LEVEL JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>