<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid profile');
}

$stmt = $pdo->prepare("
    SELECT 
        name,
        role,
        qualification,
        bio,
        profile_image
    FROM content_people
    WHERE id = :id
    LIMIT 1
");

$stmt->execute(['id' => $id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die('Profile not found');
}

$image = !empty($profile['profile_image'])
    ? htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8')
    : 'assets/pages/img/default-user.jfif';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debra Sullivan, Ph.D. | Healthline Profile</title>
    
  <!-- Open Graph Meta (for Social Sharing) -->
<meta property="og:site_name" content="Flying411">
<meta property="og:title" content="<?= htmlspecialchars($seoTitle) ?>">
<meta property="og:description" content="<?= htmlspecialchars($seoDescription) ?>">
<meta property="og:url" content="<?= $canonicalUrl ?>">

<meta property="og:type" content="article">
<meta property="og:image" content="https://flying411.com/assets/corporate/img/Hangar-2-4-White-Final-1.png">




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
  <!--<link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
  <!-- Global styles END --> 
   
  <link href="/assets/corporate/css/style.css" rel="stylesheet">
  <link href="/assets/corporate/css/style-responsive.css" rel="stylesheet">
  <!--<link href="/assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">-->
  <!--<link href="/assets/corporate/css/custom.css" rel="stylesheet">-->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9EWL5C594"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9EWL5C594');
</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    *{
        font-family: "EB Garamond", serif !important;
    }
    .header-navigation > ul > li > a{
        font-size:18px !important ;}
    .headers {
            position: relative;
            width: 100%;
            height: 80vh;
            background: url('/assets/pages/img/blogs.png') center center/cover no-repeat;
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
            border-radius: 5px;
        }

        .search-bar select {
            flex: 1;
            border: 1px solid white;
            color:#fff;
            border-radius: 10px;
            max-width: 470px;
            appearance: none;
            background: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"%3E%3Cpath fill="%23ccc" d="M2 0L0 2h4zm0 5L0 3h4z"/%3E%3C/svg%3E') no-repeat right 10px center;
            background-size: 12px;
        }
        .search-bar select:focus {
    background-color: #333; /* Keep the background color consistent */
    color: #fff; /* Ensure text color remains white */
    outline: none; /* Optional: Remove default outline */
}
        :root {
            --healthline-black: #000000;
            --healthline-teal: #007f8c;
            --text-dark: #222222;
            --text-light: #555555;
            --bg-circle: #c5e4e7;
        }

        /* --- MAIN PROFILE SECTION --- */
        .profile-wrapper {
            max-width: 800px;
            margin: 80px auto;
            text-align: center;
            padding: 0 20px;
        }

        /* The circular image effect */
        .profile-image-container {
            width: 180px;
            height: 180px;
            background-color: var(--bg-circle);
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .profile-image-container img {
            width: 100%;
            height: auto;
            /* Using a grayscale filter to match the original image aesthetic */
            filter: grayscale(100%);
        }

        .role-label {
            color: var(--healthline-teal);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 25px;
            display: block;
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2b2b2b;
        }

        .bio-text {
            text-align: left;
            font-size: 16px;
            color: #444444;
            max-width: 700px;
            margin: 0 auto;
        }

.headlogo{
display: flex; justify-content: space-between; align-items: center;}
        /* --- RESPONSIVE --- */
        @media (max-width: 900px) {
            nav { display: none; } /* Hide complex nav on mobile for this demo */
            h1 { font-size: 28px; }
            header { padding: 12px 20px; }
        }
.mobile-header {
  display: none;
}

/* MOBILE ONLY */
@media (max-width: 768px) {
.headlogo{
    display: none;
}
nav{
    padding: 0px !important;
}
.btn-group>.btn-group:not(:last-child)>.btn, .btn-group>.btn.dropdown-toggle-split:first-child, .btn-group>.btn:not(:last-child):not(.dropdown-toggle) ,.btn-group>.btn-group:not(:first-child), .btn-group>:not(.btn-check:first-child)+.btn{
    border-radius:20px;
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
  .btn-group {
      display: flex;
      flex-direction: column;
  }
}
.header-navigation > ul > li > a{
    color: #000;
}
.site-logo img{
    border-radius:50% ;
}
.header .container{
    top: 30px;
}
.header-navigation > ul > li > a:hover{
    background-color: transparent;
}

.pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 20px;
}
.pagination a, .pagination span {
    padding: 6px 12px;
    border: 1px solid #ddd;
    color: #4747A1;
    text-decoration: none;
    border-radius: 4px;
}
.pagination a.active {
    background-color: #4747A1;
    color: #fff;
    font-weight: bold;
}
.pagination span {
    padding: 6px 8px;
    color: #999;
    border: none;
}
.pagination a:hover:not(.active) {
    background-color: #f1f1f1;
}
    </style>
</head>
<body>

    <div class="header" style="max-width:80%; margin:auto;">
    <div class="container headlogo" style="">
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

  <main class="profile-wrapper">
        
    <div class="profile-image-container">
        <img src="admin/uploads/<?= $image ?>" alt="<?= htmlspecialchars($profile['name']) ?>">
    </div>
   


    <span class="role-label">
        <?= htmlspecialchars($profile['role']) ?>
    </span>

    <h1><?= htmlspecialchars($profile['name']) ?></h1>
 <a href="javascript:history.back()" 
   style="color:#2843a7; text-decoration:none; font-weight:500; display:inline-flex; align-items:center; gap:6px; margin-bottom:15px;">
    
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2843a7" viewBox="0 0 16 16">
        <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.146 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
    </svg>

    Back to Blog
</a>
    <div class="bio-text">
        <h1>Qualification</h1>
        <p>
            <?= nl2br(htmlspecialchars($profile['qualification'])) ?>
        </p>
    </div>
    
    <div class="bio-text">
        <h1>Bio</h1>
        <p>
            <?= nl2br(htmlspecialchars($profile['bio'])) ?>
        </p>
    </div>

</main>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const clearFilterBtn = document.querySelector(".filterbtn");

    if (!clearFilterBtn) return; // Prevent error if button doesn't exist

    clearFilterBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default link behavior
        location.reload(); // Refresh the page
    });
});
</script>


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
</html>