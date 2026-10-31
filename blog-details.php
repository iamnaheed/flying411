<?php include 'track_page.php'; ?>
<?php
require 'config.php';

$id   = $_GET['id'] ?? null;
$slug = $_GET['slug'] ?? null;

if (!$id || !$slug || !is_numeric($id)) {
    header("HTTP/1.0 404 Not Found");
    exit('Blog not found');
}

/* ===========================
   FETCH BLOG + CATEGORY + PEOPLE
=========================== */
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        c.name AS category_name,
        au.id AS author_id,
        au.name AS author_name,
        au.profile_image AS author_image,
        au.bio AS author_bio,
        fc.id AS fact_id,
        fc.name AS fact_name,
        fc.profile_image AS fact_image
    FROM articles a
    LEFT JOIN categories c ON c.id = a.category_id
    LEFT JOIN content_people au ON au.id = a.author_id
    LEFT JOIN content_people fc ON fc.id = a.fact_checker_id
    WHERE a.id = ? AND a.slug = ?
    LIMIT 1
");
$stmt->execute([$id, $slug]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    header("HTTP/1.0 404 Not Found");
    exit('Blog not found');
}

/* ===========================
   RELATED ARTICLES
=========================== */
$relatedStmt = $pdo->prepare("
    SELECT id, title, slug, image
    FROM articles
    WHERE category_id = ? AND id != ?
    ORDER BY publish_date DESC
    LIMIT 3
");
$relatedStmt->execute([$blog['category_id'], $blog['id']]);
$relatedBlogs = $relatedStmt->fetchAll();

/* ===========================
   SIDEBAR IMAGE (LATEST BLOG IMAGE)
=========================== */
$sideImgStmt = $pdo->query("
    SELECT image FROM articles
    WHERE image IS NOT NULL
    ORDER BY publish_date DESC
    LIMIT 1
");
$sidebarImage = $sideImgStmt->fetchColumn();

/* ===========================
   DATE FORMATTING
=========================== */
$publishedDate = date('M d, Y', strtotime($blog['publish_date']));
$updatedDate   = date('F d, Y', strtotime($blog['created_at']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    
    <!-- Title & Meta -->
    <title><?= $blogTitle ?></title>
    <meta name="description" content="<?= $blogDescription ?>">
    <meta name="keywords" content="<?= $blogKeywords ?>">
    <meta name="author" content="Flying411">
    <link rel="canonical" href="<?= $canonicalUrl ?>">

    <!-- Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <!-- Open Graph -->
    <meta property="og:site_name" content="Flying411">
    <meta property="og:url" content="<?= $canonicalUrl ?>">
    <meta property="og:title" content="<?= $blogTitle ?>">
    <meta property="og:description" content="<?= $blogDescription ?>">
    <meta property="og:image" content="<?= $ogImage ?>">

    <meta property="og:type" content="article">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

    <!-- Android / Chrome -->
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png">

    <!-- Safari Pinned Tab -->
    <link rel="mask-icon" href="/assets/corporate/img/Hangar-2-4-White-Final-1.png" color="#5bbad5">



  <!-- Fonts START -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page -->  
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <!--<link href="/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">-->
  <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
   
  <link href="/assets/corporate/css/style.css" rel="stylesheet">
  <link href="/assets/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="/assets/corporate/css/themes/red.css" rel="stylesheet" id="style-color">
  <link href="/assets/corporate/css/custom.css" rel="stylesheet">
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
            --primary-gold: #2843a7;
            --dark-blue: #2843a7;
            --text-main: #1e293b;
            --text-light: #64748b;
            --bg-body: #ededed;
            --bg-alt: #f8fafc;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
              font-family: "EB Garamond", serif !important;
        }

        body {
            font-family: "EB Garamond", serif !important;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.8;
        }

        /* --- NAVIGATION --- */
        nav {
            padding: 20px;
        }

        .back-link {
            text-decoration: none;
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
            transition: 0.3s;
        }

        .back-link:hover { color: var(--primary-gold); }

        /* --- ARTICLE HEADER --- */
        .article-header {
            max-width: 850px;
            margin: 110px auto 40px;
            text-align: center;
            padding: 0 20px;
        }

        .category-badge {
            background: var(--primary-gold);
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 1.1rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .article-header h1 {
          font-family: "EB Garamond", serif !important;
            font-size: 3.2rem;
            line-height: 1.1;
            margin: 25px 0;
                    }

        .article-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .author-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        /* --- FEATURED IMAGE --- */
        .featured-image-container {
            max-width: 1100px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        .featured-image-container img {
            width: 100%;
            height: 550px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        /* --- MAIN CONTENT LAYOUT --- */
        .article-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 8fr 3.5fr;
            gap: 60px;
            padding: 0 20px 100px;
        }

        .article-body {
            font-size: 1.15rem;
            color: #334155;
        }

        .article-body h2 {
            font-family: "EB Garamond", serif !important;
            font-size: 2rem;
            margin: 40px 0 20px;
            color: var(--dark-blue);
        }

        .article-body p{
            margin-bottom: 25px;
            font-size: 18px;
        }
        .article-body th,td,ul,li,a{
            font-size: 16px;
        }
                .article-body table ,th,td{
                    border:1px solid black;
                }
        .article-body img{
            width:100% ;
        }
        .expert-quote {
            border-left: 4px solid var(--primary-gold);
            padding: 20px 40px;
            background: var(--bg-alt);
            font-style: italic;
            font-font-family: "EB Garamond", serif !important;
            font-size: 1.4rem;
            margin: 40px 0;
            color: var(--dark-blue);
        }

        .content-image {
            width: 100%;
            border-radius: 8px;
            margin: 30px 0;
        }

        /* --- SIDEBAR --- */
        .sidebar-sticky {
            position: sticky;
            top: 40px;
        }

        .sidebar-box {
            background:white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .sidebar-box h4 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #000;
            border-bottom: 2px solid var(--primary-gold);
            display: inline-block;
            padding-bottom: 5px;
        }

        .social-share {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .share-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .share-fb { background: #1877f2; }
        .share-tw { background: #1da1f2; }
        .share-li { background: #0077b5; }

        .share-btn:hover { opacity: 0.9; transform: translateX(5px); }

        /* --- RELATED POSTS --- */
        .related-section {
            background: var(--bg-alt);
            padding: 80px 20px;
            border-top: 1px solid var(--border);
        }

        .related-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .related-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }

        .related-card:hover { transform: translateY(-5px); }
        .related-card img { width: 100%; height: 180px; object-fit: cover; }
        .related-card div { padding: 20px; }
        .related-card h5 { font-size: 1rem; color: var(--dark-blue); }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .article-container { grid-template-columns: 1fr; }
            .article-header h1 { font-size: 2.5rem; }
            .featured-image-container img { height: 350px; }
            .sidebar-sticky { position: static; }
            .related-grid { grid-template-columns: 1fr 1fr; }
        }
        
        .mobile-header {
  display: none;
}

.backlink{
 color:#2843a7; text-decoration:none; font-weight:500; display:inline-flex; align-items:center; gap:6px;  margin-left:170px;   
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
  nav{
      padding: 0px !important;
  }
  .backlink{
      margin-left:20px;
  }
  .article-body{
      padding: 10px;
  }
  .pre-footer .container .first-col{
      padding: 16px !important;
  }
}
.header-navigation > ul > li > a{
    color: #000;
    font-size:18px !important;
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

    <nav>
        <div class="header" style="position: absolute; width:97%">
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
    </nav>

    <header class="article-header">
    <span class="category-badge"><?= htmlspecialchars($blog['category_name']) ?></span>

    <h1><?= htmlspecialchars($blog['title']) ?></h1>
</header>


  <div class="featured-image-container">
    <img src="/admin/uploads/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
</div>
    <a href="/blogs" class="backlink" style="">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2843a7" viewBox="0 0 16 16">
        <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
    </svg>
    Back to Blog List
</a>


    <div class="article-container">
        <main class="article-body">
        <?= $blog['content'] ?>
    </main>

        <aside>
    <div class="sidebar-sticky">

        <!-- ABOUT AUTHOR -->
        <?php if (!empty($blog['author_id']) || !empty($blog['fact_id'])): ?>
<div class="sidebar-box">
    <h4>About The Expert</h4>

    <!-- PROFILE ROW (SAME AS HEADER STYLE) -->
    <div style="display:flex; gap:12px; align-items:flex-start;">

        <?php if (!empty($blog['fact_image'])): ?>
            <img 
                src="/admin/uploads/<?= htmlspecialchars($blog['fact_image']) ?>" 
                alt="<?= htmlspecialchars($blog['fact_name'] ?? 'Expert') ?>"
                style="width:60px; height:60px; border-radius:50%; object-fit:cover;"
            >
        <?php endif; ?>

        <div style="font-size:0.9rem; line-height:1.4;">

            <?php if (!empty($blog['fact_id'])): ?>
                <a href="/profile?id=<?= $blog['fact_id'] ?>" style="font-weight:700; display:block; color:#000;">
                    This article reviewed by <?= htmlspecialchars($blog['fact_name']) ?>
                </a>
            <?php endif; ?>

            <?php if (!empty($blog['author_id'])): ?>
                <a href="/profile?id=<?= $blog['author_id'] ?>" style="display:block; margin-top:4px; color:#000;">
                    Written by <?= htmlspecialchars($blog['author_name']) ?>
                </a>
            <?php endif; ?>

            <span style="display:block; margin-top:4px; opacity:0.7; font-size:12px;">
                Updated on <?= date('F d, Y', strtotime($blog['created_at'])) ?>
            </span>

        </div>
    </div>
</div>
<?php endif; ?>




        <!-- SUBSCRIBE -->
        <div class="sidebar-box" style="background:white; color: #000; font-size:18px;">
                    <h4 style="color: #000;font-size:1.3rem;color:#000;">Join The Fleet</h4>
                    <p style="font-size: 1.5rem; margin-bottom: 15px; opacity: 0.8;">Get expert maintenance tips and buying guides delivered to your inbox weekly.</p>
                    <input type="email" placeholder="Your email address" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #000; margin-bottom: 10px;">
                    <button style="width: 100%; padding: 10px; background: var(--primary-gold); color: white; border: none; border-radius: 4px; font-weight: 700; cursor: pointer;">SUBSCRIBE</button>
                </div>

    </div>
</aside>
</div>

    </div>

    <section class="related-section">
    <div class="related-content">
        <h2>Keep Reading</h2>

        <div class="related-grid">
            <?php foreach ($relatedBlogs as $r): ?>
            <a href="/blog/<?= $r['slug'] ?>/<?= $r['id'] ?>" class="related-card">
                <img src="/admin/uploads/<?= htmlspecialchars($r['image']) ?>">
                <div>
                    <h5><?= htmlspecialchars($r['title']) ?></h5>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>


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