<?php include 'track_page.php'; ?>
 <?php
require 'config.php';


/* ================== PAGINATION SETTINGS ================== */
$perPage = 6; // Show 5 or 6 articles per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

/* ================== FETCH CATEGORIES ================== */
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

/* ================== FETCH CONTENT PEOPLE ================== */
$peopleStmt = $pdo->query("SELECT * FROM content_people ORDER BY name ASC");
$people = $peopleStmt->fetchAll(PDO::FETCH_ASSOC);
$authors = array_filter($people, fn($p) => $p['role'] === 'writer');
$factCheckers = array_filter($people, fn($p) => $p['role'] === 'fact_checker');

/* ================== TOTAL ARTICLES COUNT ================== */
$totalStmt = $pdo->query("SELECT COUNT(*) FROM articles");
$totalArticles = $totalStmt->fetchColumn();
$totalPages = ceil($totalArticles / $perPage);

/* ================== FETCH PAGINATED ARTICLES ================== */
$articleStmt = $pdo->prepare("
    SELECT a.*, 
           c.name AS category_name, 
           au.name AS author_name,
           fc.name AS fact_checker_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN content_people au ON a.author_id = au.id
    LEFT JOIN content_people fc ON a.fact_checker_id = fc.id
    ORDER BY a.publish_date DESC
    LIMIT :limit OFFSET :offset
");
$articleStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$articleStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$articleStmt->execute();
$articles = $articleStmt->fetchAll(PDO::FETCH_ASSOC);

/* ================== FEATURED ARTICLE ================== */
$featured = null;
foreach ($articles as $a) {
    if (!empty($a['is_featured'])) {
        $featured = $a;
        break;
    }
}

/* ================== AUTHOR SPOTLIGHT ================== */
$spotlight = null;
foreach ($authors as $au) {
    if (!empty($au['managed'] ?? false)) {
        $spotlight = $au;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
<title><?= htmlspecialchars($seoTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
<link rel="canonical" href="<?= $canonicalUrl ?>">

<!-- Responsive and Compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<meta name="keywords" content="aviation blog, aircraft news, flight tips, aviation stories, pilot blog, flying411 articles, aircraft maintenance, aviation technology, flight safety">
<meta name="author" content="Flying411">

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Fonts START -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page -->  
  <!-- Fonts END -->

  <!-- Global styles START -->          

  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
   <link href="assets/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="assets/corporate/css/style.css" rel="stylesheet">
  <link href="assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="assets/pages/css/home.css" rel="stylesheet" type="text/css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9EWL5C594"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9EWL5C594');
</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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
            --primary-gold: #ededed;
            --primary-gold-hover: #2843a7;
            --dark-blue: #2843a7;
            --text-main: #334155;
            --text-light: #64748b;
            --bg-body: #f8fafc;
            --white: #ffffff;
            --transition: all 0.3s ease;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
a, a:focus, a:hover, a:active{
    color: #000;
}
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "EB Garamond", serif !important;
            
        }

        body {
            background-color:#ededed;
            color: var(--text-main);
            line-height: 1.6;
        }

        /* --- HERO SECTION --- */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                        url('https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&q=80&w=2000') center/cover no-repeat;
            height: 550px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            padding: 0 20px;
        }

        .hero-content h1 {
            
            font-family: "EB Garamond", serif !important;
            font-size: 3.5rem;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 35px;
            font-weight: 300;
            opacity: 0.9;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            cursor: pointer;
            border: none;
        }

        .btn-gold { background: var(--primary-gold); color: #000; }
        .btn-gold:hover { color:white; background: var(--primary-gold-hover); transform: translateY(-2px); }
        .btn-dark { background: var(--dark-blue); color: white; }
        .btn-dark:hover { background: #0d1321; transform: translateY(-2px); }

        /* --- CATEGORY NAVIGATION --- */
        .featured-header {
            max-width: 1200px;
            margin: -40px auto 40px;
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 10;
        }

        .featured-header h3 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-light);
            margin-bottom: 20px;
            text-align: left;
        }

        .nav-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pill {
            padding: 10px 20px;
            border-radius: 50px;
            background: #f1f5f9;
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pill i { font-size: 1rem; }
        .pill:hover { background: #e2e8f0; }
        .pill.active {
            background: var(--dark-blue);
            color: white;
        }

        /* --- MAIN LAYOUT --- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 80px;
            display: grid;
            grid-template-columns: 8fr 3.5fr;
            gap: 40px;
        }

        /* --- ARTICLE CARDS --- */
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .view-all {
            font-size: 0.9rem;
            color: var(--primary-gold);
            text-decoration: none;
            font-weight: 600;
        }

        .featured-article {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .featured-article img {
            width: 45%;
            object-fit: cover;
        }

        .featured-content {
            padding: 40px;
            width: 55%;
        }

        .category-tag {
            background: #2843a7;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
        }

        .featured-content h2 {
            font-size: 2rem;
            margin: 15px 0;
            line-height: 1.2;
        }

        .featured-content p {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .meta {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 20px;
            display: block;
        }

        .read-more {
            color: var(--dark-blue);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        /* Article Grid */
        .article-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .small-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .small-card:hover { transform: translateY(-5px); }
        .small-card img { width: 100%; height: 200px; object-fit: cover; margin-bottom:13px; }
        .small-card-content { padding: 20px; }
        .small-card-content h4 { font-size:17px; margin-bottom: 15px; min-height: 3rem; }
        .card-content h4 { font-size:17px;}
        .card-content p { font-size:16px; margin-bottom: 15px; min-height: 3rem; }

        /* --- SIDEBAR --- */
        .sidebar-widget {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .sidebar-widget h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-gold);
            padding-left: 15px;
        }

        .pop-article {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
          
        }

        .pop-article img { width: 80px; height: 60px; border-radius: 6px; object-fit: cover; }
        .pop-article h5 { font-size: 0.9rem; line-height: 1.3;   color: #2843a7;}
        .pop-article span { font-size: 0.75rem; color: var(--text-light); }

        .author-card { text-align: center; }
        .author-img { width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 15px; border: 3px solid #f1f5f9; }
        .managed-badge {
            background: var(--dark-blue);
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* --- PAGINATION --- */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 50px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--white);
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: 1px solid #e2e8f0;
            cursor: pointer;
        }

        .page-btn.active { background: var(--dark-blue); color: white; border-color: var(--dark-blue); }
        .page-btn:hover:not(.active) { background: #f1f5f9; }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .container {
    display: flex;
    flex-direction: column-reverse;
    gap:0px;
}

            .hero-content h1 { font-size: 2.5rem; }
        }

        @media (max-width: 600px) {
            .featured-article { flex-direction: column; }
            .featured-article img, .featured-content { width: 100%; }
            .article-grid { grid-template-columns: 1fr; }
        }
        /* Hide mobile navbar on desktop */
.mobile-header {
  display: none;
}

/* MOBILE ONLY */
@media (max-width: 768px) {


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
    color: white;
    font-size: 18px !important;
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
.small-card a{
    color: #000;
}
    </style>
</head>
<body>
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
 

<!-- ================== HERO SECTION ================== -->
<section class="hero">
    <div class="hero-content">
        <h1>Aviation Insights &<br>Expert Guides</h1>
        <p>Your premium resource for aircraft ownership, maintenance, and industry expertise, written by veteran pilots and technicians.</p>
        <div class="btn-group">
            <button class="btn btn-gold" onclick="scrollToContent()">Browse by Category</button>
            <button class="btn btn-dark" onclick="filterArticles('All')">Latest Articles</button>
        </div>
    </div>
</section>

<!-- ================== CATEGORY FILTERS ================== -->
<div class="featured-header">
    <h3>Filter by Department</h3>
    <div class="nav-pills" id="categoryFilters">
        <div class="pill active" data-cat="All">
         All Articles
        </div>

        <?php foreach ($categories as $cat): ?>
            <?php if ($cat['parent_id'] === null): ?>
                <div class="pill" data-cat="<?= htmlspecialchars($cat['name']) ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>


<div class="container" id="main-content">
    <main>
        <div id="dynamic-section-title">
            <div class="section-title">
                <h2 id="currentCategoryTitle">All Aviation Articles</h2>
                <a href="#" class="view-all">View All Articles →</a>
            </div>
        </div>

        <div id="featured-article-container">
        <?php if ($featured): ?>
            <article class="featured-article">
                <img src="/admin/uploads/<?= htmlspecialchars($featured['image']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
                <div class="featured-content">
                    <span class="category-tag"><?= htmlspecialchars($featured['category_name']) ?></span>
                    <h2><?= htmlspecialchars($featured['title']) ?></h2>
                    <span class="meta">
                        By <?= htmlspecialchars($featured['author_name']) ?> • 
                        <?= date("M d, Y", strtotime($featured['publish_date'])) ?>
                    </span>
                    <p><?= substr(strip_tags($featured['content']), 0, 180) ?>...</p>
                    <a href="/blog/<?= htmlspecialchars($featured['slug']) ?>/<?= $featured['id'] ?>" class="read-more">Read More →</a>
                </div>
            </article>
        <?php endif; ?>
        </div>

        <div class="article-grid" id="article-grid-container">
            <?php foreach ($articles as $art): ?>
                <article class="small-card" data-cat="<?= htmlspecialchars($art['category_name']) ?>">
                    <a href="/blog/<?= htmlspecialchars($art['slug']) ?>/<?= $art['id'] ?>">
                    <img src="/admin/uploads/<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['title']) ?>">
                    </a>
                    <div class="card-content">
                        <span class="category-tag"><?= htmlspecialchars($art['category_name']) ?></span>
                        <a href="/blog/<?= htmlspecialchars($art['slug']) ?>/<?= $art['id'] ?>">
                        <h4><?= htmlspecialchars($art['title']) ?></h4>
                        </a>
                        <span class="meta">
                            By <?= htmlspecialchars($art['author_name']) ?> / Fact Checker: <?= htmlspecialchars($art['fact_checker_name']) ?> • 
                            <?= date("M d, Y", strtotime($art['publish_date'])) ?>
                        </span>
                        <p><?= substr(strip_tags($art['content']), 0, 120) ?>...</p>
                        <a href="/blog/<?= htmlspecialchars($art['slug']) ?>/<?= $art['id'] ?>" class="read-more">Read More →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- ================== PAGINATION ================== -->
        <!-- ================== PAGINATION ================== -->
<div class="pagination">
<?php
$adjacents = 1; // Number of pages to show on each side of current page
if ($totalPages > 1) {

    // Previous button
    if($page > 1){
        echo '<a href="?page='.($page-1).'">&laquo; Prev</a>';
    }

    // First page
    if($page > 2 + $adjacents){
        echo '<a href="?page=1">1</a>';
        if($page > 3 + $adjacents) echo '<span>...</span>';
    }

    // Pages around current
    $start = ($page - $adjacents) > 1 ? $page - $adjacents : 1;
    $end = ($page + $adjacents) < $totalPages ? $page + $adjacents : $totalPages;
    for($i = $start; $i <= $end; $i++){
        echo '<a href="?page='.$i.'" class="'.($i==$page?'active':'').'">'.$i.'</a>';
    }

    // Last page
    if($page < $totalPages - $adjacents - 1){
        if($page < $totalPages - $adjacents - 2) echo '<span>...</span>';
        echo '<a href="?page='.$totalPages.'">Last</a>';
    }

    // Next button
    if($page < $totalPages){
        echo '<a href="?page='.($page+1).'">Next &raquo;</a>';
    }
}
?>
</div>





    </main>

    <aside>
        <!-- Popular Articles -->
        <div class="sidebar-widget">
            <h3>Popular Articles</h3>
            <div id="popular-articles">
                <?php foreach (array_slice($articles, 0, 6) as $pop): ?>
                    <div class="pop-article">
                        <img src="/admin/uploads/<?= htmlspecialchars($pop['image']) ?>" alt="<?= htmlspecialchars($pop['title']) ?>">
                        <div>
                             <a href="/blog/<?= htmlspecialchars($pop['slug']) ?>/<?= $pop['id'] ?>">
                            <h5><?= htmlspecialchars($pop['title']) ?></h5>
                            </a>
                            <span><?= date("M d, Y", strtotime($pop['publish_date'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    
    </aside>
</div>
<div class="pre-footer" style="margin-top:50px">
    <div class="container" style="max-width:1400px;display:flex;">
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
    </div>
  </div>
<script>
const pills = document.querySelectorAll('.pill');
const cards = document.querySelectorAll('.small-card');
pills.forEach(pill => {
    pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const cat = pill.dataset.cat;
        cards.forEach(card => {
            card.style.display = cat === 'All' || card.dataset.cat === cat ? 'block' : 'none';
        });
    });
});
</script>
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