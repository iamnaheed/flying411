<?php include 'track_page.php'; ?>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <head>
  <meta charset="utf-8">

  <title>Contact Flying411 | Aviation Marketplace Support & Inquiries</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <!-- SEO Meta -->
  <meta name="description" content="Contact Flying411 for support, inquiries, or partnership opportunities. Reach our aviation marketplace team for aircraft, engines, and parts listings assistance.">
  <meta name="keywords" content="Contact Flying411, aviation marketplace support, aircraft sales help, aircraft parts inquiries, aviation services contact">
  <meta name="author" content="Flying411">

  <!-- Open Graph / Social Sharing -->
  <meta property="og:site_name" content="Flying411">
  <meta property="og:title" content="Contact Flying411 | Aviation Marketplace Support">
  <meta property="og:description" content="Get in touch with Flying411 for aircraft sales, engines, parts listings, and aviation marketplace support.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.flying411.com/contact">
  <meta property="og:image" content="https://www.flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">

  <!-- Apple Touch Icon -->
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

  <!-- Android / Chrome Icons -->
  <link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
  <link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

  <!-- Safari Pinned Tab -->
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
    font-family: "EB Garamond", serif !important;
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
     .sidebar{
      width: 100%;
     }
      .cards{
       margin-top:20px;
   }
   .suppot_form{
       margin-top: 20px;
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
.sidebar{
  margin-top: 50px;
}
/* Ensure the sidebar and forms are displayed in a single row with space between them */
.row {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.sidebar, .col-md-4 {
  flex: 1 1 30%; /* Each section takes up 30% of the row */
}



.from{
    width:100%;
}
/* Add some padding to the forms */
.forms-row {
  display: flex;
  justify-content:space-evenly;
}

.form-col {
  width: 100%; /* Each form takes 48% of the width of the parent */
  margin-bottom: 20px;
}

.form-col form {
  display: flex;
  flex-direction: column;
}

.form-col .form-group {
  margin-bottom: 15px;
}

button {
  cursor: pointer;
}

button[type="submit"] {
  padding: 12px 25px;
  background-color: #333;
  color: white;
  border: none;
  border-radius: 5px;
}

button[type="submit"]:hover {
  background-color: #555;
}

/* Optional: Style for the margin around the main section */
.margin-bottom-40 {
  margin-bottom: 40px;
}

.margin-top-10 {
  margin-top: 10px;
}
.sidebar, .col-md-4 {
    flex: 0 1 27% !important;
}


@media (max-width: 768px) {
  .sidebar, .col-md-4 {
    flex: 1 1 100%; /* On smaller screens, each section takes full width */
    margin-bottom: 20px;
  }
  .sidebar, .col-md-4 {
    flex: 0 1 100% !important;
}

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
@media (min-width: 1025px) {
    .container, .container-lg, .container-md, .container-sm {
        max-width: 1150px !important;
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
     color:black !important;
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
  <div class="header" style="">
    <div class="container headlogo" style="display: flex; justify-content: space-between; align-items: center;">
      <a class="site-logo" style="padding: 0px; margin:0px;" href="/"><img src="/assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Metronic Shop UI"></a>

      <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>

      

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
  <div class="container">
    <!-- BEGIN SIDEBAR & CONTENT -->
    <div class="row margin-bottom-40 margin-top-10" style="width:100%">
      
      <!-- BEGIN SIDEBAR -->
      <div class="sidebar col-md-4 col-sm-12 mb-4">
        <h2 style="font-size: 30px;">Our Contacts</h2>
        <address>
          <p title="Phone">
            <img src="assets/pages/img/icons/phone-call.png" alt="" width="25px">&nbsp;&nbsp;&nbsp;
            <a href="tel:+19049946224">+1 (904) 994-6224</a>
          </p>
          <br>
          <p style="padding-bottom:20px;" title="email">
            <img src="assets/pages/img/icons/mail.png" alt="" width="25px">&nbsp;&nbsp;&nbsp;
            <a href="mailto:Cory@Hangar-24.com">Cory@Hangar-24.com</a>
          </p>
        </address>
        <ul class="social-icons margin-bottom-10">
          <li><a href="javascript:;" data-original-title="facebook" class="facebook"></a></li>
          <li><a href="javascript:;" data-original-title="github" class="github"></a></li>
          <li><a href="javascript:;" data-original-title="Google Plus" class="googleplus"></a></li>
          <li><a href="javascript:;" data-original-title="linkedin" class="linkedin"></a></li>
          <li><a href="javascript:;" data-original-title="rss" class="rss"></a></li>
        </ul>
      </div>
      <!-- END SIDEBAR -->

      <!-- BEGIN CONTENT (Support Form) -->
      <div class="col-md-4 col-sm-12 mb-4" style="margin-top:40px; box-shadow: rgba(67, 71, 85, 0.27) 0px 0px 0.25em, rgba(90, 125, 188, 0.05) 0px 0.25em 1em; padding:20px 40px;">
        <h2 style="font-size: 30px;">Support Form</h2>
        <div class="support-ticket">
          <form>
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" rows="5" id="description" required></textarea>
            </div>
            <div class="mb-3">
              <label for="priority" class="form-label">Priority</label>
              <select class="form-control" id="priority" required>
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </div>
             <button type="submit" class="btn btn-dark" style="padding: 5px 15px; background-color:black; color:#fff; font-size:18px; border: 1px solid #ddd; border-radius: 5px;">Submit</button>
          </form>
        </div>
      </div>
      <!-- END CONTENT -->

      <!-- BEGIN CONTACT FORM -->
      <div class="col-md-3 col-sm-12 mb-4"  style="margin-top:40px; box-shadow: rgba(67, 71, 85, 0.27) 0px 0px 0.25em, rgba(90, 125, 188, 0.05) 0px 0.25em 1em; padding:20px 40px;">
                <h2 style="font-size: 30px;">Contact Form</h2>
        <!-- BEGIN FORM -->
        <div class="forms-row">
          <div class="form-col">
           <form action="#" class="default-form" role="form">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email <span class="require">*</span></label>
              <input type="email" class="form-control" id="email" required>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea class="form-control" rows="3" cols="" id="message" required></textarea>
            </div>
            <div class="padding-top-20">
              <button type="submit" class="btn btn-dark" style="padding: 5px 15px; background-color:black; color:#fff; font-size:18px; border: 1px solid #ddd; border-radius: 5px;">Submit</button>
            </div>
          </form>
          </div>
        </div>
        <!-- END FORM -->
      </div>
      <!-- END CONTACT FORM -->
    </div>
    <!-- END SIDEBAR & CONTENT -->
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
    <script src='/assets/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
    <script src="/assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script><!-- Quantity -->
    <script src="/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
    <script src="/assets/plugins/gmaps/gmaps.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/contact-us.js" type="text/javascript"></script>

    <script src="/assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initTwitter();
            Layout.initImageZoom();
            Layout.initTouchspin();
            Layout.initUniform();
            ContactUs.init();
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
    <!-- END PAGE LEVEL JAVASCRIPTS -->
    
    
    
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