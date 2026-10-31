<?php include 'track_page.php'; ?>
<?php
session_start();

// 1. CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. COLLECT ERROR MESSAGES (global + per field)
$global_error = $_SESSION['register_error'] ?? '';
$errors       = $_SESSION['errors'] ?? [];
unset($_SESSION['register_error'], $_SESSION['errors']);

// 3. OLD FORM DATA (sticky values)
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// 4. WHICH STEP TO SHOW
$form_step = $_SESSION['form_step'] ?? 1;
unset($_SESSION['form_step']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="utf-8">
<title>Register - Flying411</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<meta name="description" content="Register at Flying411, the global aviation marketplace for aircraft, engines, and parts. Join our platform to list, buy, or sell aviation products.">
<meta name="keywords" content="Flying411, register, aviation marketplace, aircraft for sale, aircraft parts, aviation services">
<meta name="author" content="Flying411">

<!-- Open Graph / Social Meta -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="Register - Flying411">
<meta property="og:description" content="Join Flying411 today to buy, sell, or list aircraft, engines, and parts in the global aviation marketplace.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">
<meta property="og:url" content="https://flying411.com/register">

<!-- Favicon and Apple Icons -->
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

<!-- Web Manifest (optional for PWA support) -->
<!--<link rel="manifest" href="/site.webmanifest">-->


  <!-- Fonts START -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9EWL5C594"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9EWL5C594');
</script>
  <style>
  .error-alert {
    background: #ffe5e5;
    border-left: 4px solid #d40000;
    padding: 10px 14px;
    border-radius: 6px;
    color: #7a0000;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.field-error {
    color: #d40000;
    font-size: 12px;
    margin-top: 4px;
}
.input-error {
    border-color: #d40000 !important;
}
  
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
       .header .container{
    left: 100px;
    margin-bottom: 100px;
    color: white;
    position: absolute;
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
   .header .container{
       left:0px !important;
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

    @media (max-width: 1200px) {
    .site-logo img{
     margin-right:0px !important;
 } 
}
  @media (max-width: 900px) {
  .header .container{
      left:24px;
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
.form-container{
    border-radius:30px !important;
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





.main {
     background: url('/assets/pages/img/products/updated\ aircraft.jpg') center center/cover no-repeat;
              display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
}
   .form-container {
       margin-top: 120px;
       margin-bottom:100px;
            background: rgba(255, 255, 255, 0.8);
            max-width: 1000px;
            width: 100%;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 30px;
            word-spacing: 6px;
            font-weight: 600;
        }
         .form-container h3{
               text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
         }
            .form-container form label{
                font-size:16px;
            }
.stepper {
    display:flex;
    justify-content:center;
    align-items:center;
    margin-bottom:30px;
}
.step {
    text-align:center;
    width:140px;
    color:#aaa;
}
.step .circle {
    width:38px;
    height:38px;
    border-radius:50%;
    border:2px solid #ccc;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
}
.step.active .circle {
    border-color:#2843a7;
    color:#2843a7;
}
.step.completed .circle {
    background:#2843a7;
    color:#fff;
    border-color:#2843a7;
}
.step .label {
    display:block;
    margin-top:6px;
    font-size:13px;
}
.line {
    width:120px;
    height:2px;
    background:#ddd;
}
.form-step {
    display:none;
}
.form-step.active {
    display:block;
}
label {
    font-weight:600;
    display:block;
    margin-top:15px;
}
input, select {
    width:100%;
    padding:10px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:5px;
}
.form-row {
    display:flex;
    gap:20px;
}
.form-row > div {
    flex:1;
}
.checkbox-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:15px;
    margin-top:15px;
}
.actions {
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}
button {
    padding:12px 28px;
    border:none;
    border-radius:5px;
    background:#2843a7;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}
button.secondary {
    background:#ccc;
    color:#333;
}
/* intl-tel-input width fix */
.iti {
    display: inline-block !important;
    width: 100% !important;
    margin-top: 6px;
    vertical-align: top;
}

/* make input fill the iti container */
.iti input {
    width: 100% !important;
}

/* when used inside flex row */
.form-row .iti {
    flex: 0 0 48%;
}
.site-logo img{
    border-radius: 50%;
}
   </style>
</head> 
<!-- Head END -->

<!-- Body BEGIN -->
<body class="ecommerce">
  <!-- BEGIN TOP BAR -->
  <div class="header" style="">
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

<div class="main">
<div class="form-container">

<h2><span style="color:#2843a7;"> Flying411.Com </span>  Marketplace Full <br> Profile Registration</h2>
<h3> <span style="color:#2843a7;"> 3 Steps </span> to Your New Business Horizons</h3>

<div class="stepper">
  <div class="step active"><span class="circle">1</span><span class="label" style="color:#000;">User Information</span></div>
  <div class="line"></div>
  <div class="step"><span class="circle">2</span><span class="label" style="color:#000;">Business Information</span></div>
  <div class="line"></div>
  <div class="step"><span class="circle">3</span><span class="label" style="color:#000;">Company Information</span></div>
</div>

<form id="registerForm" enctype="multipart/form-data" novalidate>

<!-- STEP 1 -->
<div id="step-1" class="form-step active">
    <div class="form-row">
    <div>
    <label>Username</label>
    <input type="text"
       name="contact_name"
       id="contact_name"
       placeholder="Username (no spaces)"
       autocomplete="off"
       oninput="this.value = this.value.replace(/\s/g,'')">

    </div>

    <div>
    <label>User Email</label>
    <input type="email" name="contact_email" id="contact_email" >
    </div>
</div>
    <div class="form-row">
        <div>
            <label>User Phone</label>
            <input type="tel" id="user_phone">
            <input type="hidden" name="contact_phone_code" id="user_phone_code">
            <input type="hidden" name="contact_phone" id="user_phone_number">
        </div>
        <div>
            <label>Password</label>
            <input 
    type="password" 
    name="password" 
    id="password"
    required
    minlength="8"
    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
    title="Password must be at least 8 characters and include uppercase, lowercase, number, and special character">

        </div>
    </div>

    <label>Profile Picture</label>
    <input type="file" name="profile_picture">

    <div class="actions">
        <div></div>
        <button type="button" onclick="nextStep()">Save & Continue</button>
    </div>
</div>

<!-- STEP 2 -->
<div id="step-2" class="form-step">
    <label>Business Name</label>
    <input type="text" name="business_name" >

    <div class="form-row">
        <div>
            <label>Business Email</label>
            <input type="email" name="business_email" id="business_email" >
        </div>
        <div>
            <label>Business Phone</label>
            <input type="tel" id="business_phone" required>
            <input type="hidden" name="business_phone_code" id="business_phone_code">
            <input type="hidden" name="business_phone" id="business_phone_number">

        </div>
    </div>

    <label>Website</label>
    <input type="url" name="website_url">

    <div class="actions">
        <button type="button" class="secondary" onclick="prevStep()">Back</button>
        <button type="button" onclick="nextStep()">Save & Continue</button>
    </div>
</div>

<!-- STEP 3 -->
<div id="step-3" class="form-step">

    <label>Company Logo</label>
    <input type="file" name="logo">

    <label>Select Services</label>
    <div class="checkbox-grid">
        <?php
        $services = [
            "Aircraft for Sale","Engine Sales","Maintenance Shop","Flight Instructor",
            "Parts House","Avionics Shop","Local Mechanics","Flight Schools"
        ];
        foreach($services as $s): ?>
            <label style="display:flex; justify-content:center;"><input style="margin-right:12px; width:20px;" type="checkbox" name="selected_services[]" value="<?= $s ?>"> <?= $s ?></label>
        <?php endforeach; ?>
    </div>

    <label>Do you own an aircraft?</label>
    <label style="width:3%;display:flex; justify-content:center;"><input type="radio" name="owns_aircraft" value="yes" onclick="toggleAircraft(true)"> Yes</label>
    <label style="width:3%;display:flex; justify-content:center;"><input type="radio" name="owns_aircraft" value="no" onclick="toggleAircraft(false)"> No</label>

    <div id="aircraftBox" style="display:none;">
        <label>Aircraft Type</label>
        <input type="text" name="aircraft_type">
    </div>

    <label>Looking to buy or sell aircraft?</label>
    <label style="width:3%;display:flex; justify-content:center;"><input type="radio" name="looking_to_buy_or_sell" value="yes"> Yes</label>
    <label style="width:3%;display:flex; justify-content:center;"><input type="radio" name="looking_to_buy_or_sell" value="no">  No</label>

    <label style="display:flex; justify-content:left;"><input style="width:3%;margin-right:12px;" type="checkbox" name="marketing_consent" value="1" required>
        I agree to receive marketing communications
    </label>
       <div style="margin-top:15px;" class="<?php echo !empty($errors['recaptcha']) ? 'input-error' : ''; ?>">
                    <div class="g-recaptcha" data-sitekey="6Lco9CQsAAAAAMkEPMSoLcW762_TEDmm_KH8Qh1t"></div>
                </div>
                <?php if (!empty($errors['recaptcha'])): ?>
                    <div class="field-error"><?= htmlentities($errors['recaptcha']) ?></div>
                <?php endif; ?>
    <div class="actions">
        <button type="button" class="secondary" onclick="prevStep()">Back</button>
        <button type="button" onclick="submitRegistration()">Submit Registration</button>

    </div>
</div>

</form>
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initTwitter();
        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

<script>
/* ================= GLOBAL ================= */
let currentStep = 1;

/* ================= STEP CONTROL ================= */
function showStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    const target = document.getElementById('step-' + step);
    if (target) target.classList.add('active');
    currentStep = step;
}

function nextStep() {
    if (currentStep < 3) showStep(currentStep + 1);
}

function prevStep() {
    if (currentStep > 1) showStep(currentStep - 1);
}

function toggleAircraft(show) {
    document.getElementById('aircraftBox').style.display = show ? 'block' : 'none';
}

/* ================= PHONE INPUT ================= */
function initPhone(inputId, codeId, numberId) {
    const input = document.getElementById(inputId);
    const code = document.getElementById(codeId);
    const number = document.getElementById(numberId);

    if (!input) return;

    const iti = window.intlTelInput(input, {
        separateDialCode: true,
        preferredCountries: ["us", "gb", "pk", "ae"],
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    function update() {
        code.value = "+" + iti.getSelectedCountryData().dialCode;
        number.value = input.value;
    }

    input.addEventListener("input", update);
    input.addEventListener("countrychange", update);
    update();
}

/* ================= ERROR HELPERS ================= */
function clearErrors() {
    document.querySelectorAll('.field-error, .form-error').forEach(e => e.remove());
}

function showError(element, message) {
    let container = element.closest('div') || element.parentNode;

    let error = container.querySelector('.field-error');
    if (!error) {
        error = document.createElement('div');
        error.className = 'field-error';
        error.style.color = '#e63946';
        error.style.fontSize = '13px';
        error.style.marginTop = '6px';
        container.appendChild(error);
    }
    error.innerText = message;
}

/* ================= FRONTEND VALIDATION ================= */
function validateFrontend() {
    clearErrors();
    let valid = true;
    let firstStep = null;

    /* USERNAME */
    const username = document.getElementById('contact_name');
    if (username.value.trim() === '') {
        showError(username, 'Username is required');
        valid = false; firstStep ??= 1;
    } else if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
        showError(username, 'Only letters, numbers and underscore allowed');
        valid = false; firstStep ??= 1;
    }

    /* CONTACT EMAIL */
    const contactEmail = document.getElementById('contact_email');
    if (contactEmail.value.trim() === '') {
        showError(contactEmail, 'Contact email is required');
        valid = false; firstStep ??= 1;
    } else if (!/^\S+@\S+\.\S+$/.test(contactEmail.value)) {
        showError(contactEmail, 'Enter a valid email address');
        valid = false; firstStep ??= 1;
    }

    /* PASSWORD */
    const password = document.getElementById('password');
    if (password.value === '') {
        showError(password, 'Password is required');
        valid = false; firstStep ??= 1;
    } else if (password.value.length < 8) {
        showError(password, 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character');
        valid = false; firstStep ??= 1;
    } else if (!/[A-Z]/.test(password.value)) {
        showError(password, 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character');
        valid = false; firstStep ??= 1;
    } else if (!/[a-z]/.test(password.value)) {
        showError(password, 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character');
        valid = false; firstStep ??= 1;
    } else if (!/[0-9]/.test(password.value)) {
        showError(password, 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character');
        valid = false; firstStep ??= 1;
    } else if (!/[@$!%*?&]/.test(password.value)) {
        showError(password, 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character');
        valid = false; firstStep ??= 1;
    }

    /* BUSINESS EMAIL */
    const businessEmail = document.getElementById('business_email');
    if (businessEmail.value.trim() === '') {
        showError(businessEmail, 'Business email is required');
        valid = false; firstStep ??= 2;
    } else if (!/^\S+@\S+\.\S+$/.test(businessEmail.value)) {
        showError(businessEmail, 'Enter a valid business email');
        valid = false; firstStep ??= 2;
    }

    /* SERVICES */
    const services = document.querySelectorAll('input[name="selected_services[]"]:checked');
    if (services.length === 0) {
        showError(document.querySelector('.checkbox-grid'), 'Select at least one service');
        valid = false; firstStep ??= 3;
    }

    /* RECAPTCHA */
    if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === '') {
        showError(document.querySelector('.g-recaptcha'), 'Please verify reCAPTCHA');
        valid = false; firstStep ??= 3;
    }

    if (!valid && firstStep) {
        showStep(firstStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    return valid;
}

/* ================= SUBMIT ================= */
function submitRegistration() {

    if (!validateFrontend()) return;

    clearErrors();

    const form = document.getElementById('registerForm');
    const formData = new FormData(form);

    fetch('register_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch {
            alert('Server error');
            return;
        }

        if (!data.success) {

            const top = document.createElement('div');
            top.className = 'form-error';
            top.style.background = '#ffe6e6';
            top.style.color = '#b00020';
            top.style.padding = '12px';
            top.style.marginBottom = '15px';
            top.innerText = data.message || 'Submission failed';
            form.prepend(top);

            let step = data.step || 1;

            if (data.errors) {
                Object.keys(data.errors).forEach(name => {
                    const el = document.querySelector(`[name="${name}"]`);
                    if (el) showError(el, data.errors[name]);
                });
            }

            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.reset();
            }

            showStep(step);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        window.location.href = data.redirect;
    })
    .catch(() => alert('Network error'));
}

/* ================= INIT ================= */
document.addEventListener("DOMContentLoaded", function () {
    showStep(1);
    initPhone("user_phone", "user_phone_code", "user_phone_number");
    initPhone("business_phone", "business_phone_code", "business_phone_number");
});
</script>


<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
<!-- END BODY -->
</html>