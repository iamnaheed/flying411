<?php
session_start();

?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <title>Secure Checkout | Flying411 Aviation Marketplace</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- SEO Meta -->
  <meta name="description" content="Complete your purchase securely on Flying411. Trusted global aviation marketplace for aircraft parts, engines, and aviation services.">
  <meta name="keywords" content="Flying411 checkout, aviation marketplace checkout, aircraft parts purchase, aircraft engine checkout, aviation services payment">
  <meta name="author" content="Flying411">
 

  <!-- Open Graph / Social -->
  <meta property="og:site_name" content="Flying411">
  <meta property="og:title" content="Secure Checkout | Flying411">
  <meta property="og:description" content="Finalize your order securely on Flying411, the global aviation marketplace for aircraft, engines, and parts.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.flying411.com/checkout">
  <meta property="og:image" content="https://www.flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">

  <!-- Icons -->
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
  <link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
  <link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#0b3c5d">
  




  <!-- Fonts START -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"> 
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <link href="/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <link href="/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">
  <link href="/assets/plugins/owl.carousel/assets/owl.carousel.css" rel="stylesheet">
  <link href="/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin styles END -->

  <!-- Theme styles START -->
  <link href="/assets/pages/css/components.css" rel="stylesheet">
  <link href="/assets/corporate/css/style.css" rel="stylesheet">
  <link href="/assets/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="/assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="/assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">
  <link href="/assets/corporate/css/custom.css" rel="stylesheet">
  <!-- Theme styles END -->
<!-- Theme styles END -->
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
         .top-bars {
            display: flex;
            justify-content: space-between;
            /* padding: 10px 20px; */
            color: black;
            width:80%;
            font-size: 16px;
            margin:auto;


        }
        .header{
    margin-top:20px !important;
}
        .top-bars a {
            color: black;
            text-decoration: none;
            font-size:16px;
        }
        .main {
            width:80%;
            margin: auto;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            /* padding: 0px 20 ; */
        }
        .containers {
            width: 60%;
            /* background-color: #f4f4f4; */
            border-radius: 8px;
            /* padding: 20px; */
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
            height:400px;
            border-radius: 8px;
        }
        .thumbnail-row {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            margin-top: 10px;
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
            border-radius: 8px;
            padding: 20px;
            /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
        }
        .sidebar button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border:1px solid #666;
            background-color: white;
            color: black;
            cursor: pointer;
            border-radius: 4px;
            font-size: 20px;
        }
        .sidebar button:hover {
            background-color: #666;
            color:white;
        }
        .sidebar .details-table {
            width: 100%;
            border-collapse: collapse;
            color:black;
        }
        .sidebar .details-table th,
        .sidebar .details-table td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            letter-spacing: 1px;
            text-align: left;
        }
        .map {
            margin-top: 20px;
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
@media (max-width: 768px) {
    .top-bars {
        flex-direction: column;
        text-align: center;
    }
    .main {
        flex-direction: column;
        width: 100%;
        padding: 10px;
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

    </style>
</head>
<!-- Head END -->

<!-- Body BEGIN -->
<body class="ecommerce">
  <!-- BEGIN TOP BAR -->

  <!-- END TOP BAR -->

  <!-- BEGIN HEADER -->
  <div class="header">
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
       <li style="display:none;"> <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Show Dashboard link if user is logged in -->
    <a href="/client_dashboard" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">Dashboard</a>
  <?php else: ?>
    <!-- Show Login button if user is not logged in -->
    <a href="/login" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">LogIn / Register</a>
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
    <a href="/login" style="padding: 8px 20px; color: black; background-color:lightgray;font-weight: bold;font-size: 15px;border-radius: 5px !important; text-decoration: none;">LogIn / Register</a>
  <?php endif; ?>
</div>
      <!-- END NAVIGATION -->
       <!-- BEGIN CART -->
     
      <!--END CART -->
    </div>
  </div>
    <!-- Header END -->
    </div>

  <div class="top-bars" style="margin:40px auto; width:75%" style="">
    <a href="javascript:void(0)" onclick="window.history.back()" style="font-size:15px;color:black">&#129120 Back to Previous Page</a>
    </div>

   <div class="main">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li class="active">Checkout</li>
    </ul>

    <!-- BEGIN SIDEBAR & CONTENT -->
    <div class="row margin-bottom-40">
      <!-- BEGIN CONTENT -->
      <div class="col-md-12 col-sm-12">
        <h1>Checkout</h1>
        <!-- BEGIN CHECKOUT PAGE -->
        <div class="panel-group checkout-page accordion scrollable" id="checkout-page">

          <!-- BEGIN CHECKOUT OPTIONS -->
          <div id="checkout" class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">
                <a data-toggle="collapse" data-parent="#checkout-page" href="#checkout-content" class="accordion-toggle">
                  Step 1: Account Options
                </a>
              </h2>
            </div>
            <div id="checkout-content" class="panel-collapse collapse in">
              <div class="panel-body row">
                <div class="col-md-6 col-sm-6">
                  <h3>New Vendor or Customer</h3>
                  <p>Choose an option to proceed:</p>
                  <div class="radio-list">
                    <label>
                      <input type="radio" name="account" value="register"> Register as Vendor or Customer
                    </label>
                  </div>
                  <p>By creating an account, you can track orders, manage your products, and receive updates.</p>
                  <button class="btn btn-secondory" style="padding:10px 18px;" type="submit" data-toggle="collapse" data-parent="#checkout-page" data-target="#payment-address-content">Continue</button>
                </div>
                <div class="col-md-6 col-sm-6">
                  <h3>Returning User</h3>
                  <p>I am a returning vendor or customer.</p>
                  <form role="form" action="#">
                    <div class="form-group">
                      <label for="email-login">Email</label>
                      <input type="email" id="email-login" class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="password-login">Password</label>
                      <input type="password" id="password-login" class="form-control">
                    </div>
                    <a href="javascript:;">Forgot Password?</a>
                    <div class="padding-top-20">
                      <button class="btn btn-secondory" style="padding:10px 18px;" type="submit">Login</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- END CHECKOUT OPTIONS -->

          <!-- BEGIN ACCOUNT & BILLING DETAILS -->
          <div id="payment-address" class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">
                <a data-toggle="collapse" data-parent="#checkout-page" href="#payment-address-content" class="accordion-toggle">
                  Step 2: Account & Billing Details
                </a>
              </h2>
            </div>
            <div id="payment-address-content" class="panel-collapse collapse">
              <div class="panel-body row">
                <div class="col-md-6 col-sm-6">
                  <h3>Personal Details</h3>
                  <div class="form-group">
                    <label for="firstname">First Name <span class="require">*</span></label>
                    <input type="text" id="firstname" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="lastname">Last Name <span class="require">*</span></label>
                    <input type="text" id="lastname" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="email">Email <span class="require">*</span></label>
                    <input type="email" id="email" class="form-control">
                  </div>
                 
                  
                   <div class="form-group">
                    <label for="telephone">Telephone <span class="require">*</span></label>
                    <input type="text" id="telephone" class="form-control">
                  </div>
                </div>
                <div class="col-md-6 col-sm-6">
                  <h3>Billing Address</h3>
                  <div class="form-group">
                    <label for="company">Company Name</label>
                    <input type="text" id="company" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="address1">Address <span class="require">*</span></label>
                    <input type="text" id="address1" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="city">City <span class="require">*</span></label>
                    <input type="text" id="city" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="state">State <span class="require">*</span></label>
                    <input type="text" id="state" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="country">Country <span class="require">*</span></label>
                    <select id="country" class="form-control">
                      <option value="">--- Select Country ---</option>
                      <option value="1">United States</option>
                      <option value="2">Canada</option>
                      <option value="3">United Kingdom</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <button class="btn btn-secondory  pull-right" style="padding:10px 18px;" type="submit" data-toggle="collapse" data-parent="#checkout-page" data-target="#shipping-address-content">Continue</button>
                </div>
              </div>
            </div>
          </div>
          <!-- END ACCOUNT & BILLING DETAILS -->

          <!-- BEGIN CONFIRMATION -->
          <div id="confirm" class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">
                <a data-toggle="collapse" data-parent="#checkout-page" href="#confirm-content" class="accordion-toggle">
                  Step 3: Confirm Order
                </a>
              </h2>
            </div>
            <div id="confirm-content" class="panel-collapse collapse">
              <div class="panel-body row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Item</th>
                          <th>Description</th>
                          <th>Vendor</th>
                          <th>Price</th>
                          <th>Quantity</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>Aircraft Engine</td>
                          <td>High-performance engine</td>
                          <td>Vendor A</td>
                          <td>$50,000</td>
                          <td>1</td>
                          <td>$50,000</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <button class="btn btn-secondory pull-right" style="padding:10px 18px;" type="submit">Confirm Order</button>
                </div>
              </div>
            </div>
          </div>
          <!-- END CONFIRMATION -->

        </div>
        <!-- END CHECKOUT PAGE -->
      </div>
      <!-- END CONTENT -->
    </div>
    <!-- END SIDEBAR & CONTENT -->
  </div>
</div>

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

    <script src="assets/corporate/scripts/layout.js" type="text/javascript"></script>
    <script src="assets/pages/scripts/checkout.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initTwitter();
            Layout.initImageZoom();
            Layout.initTouchspin();
            Layout.initUniform();
            Checkout.init();
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
</body>
<!-- END BODY -->
</html>