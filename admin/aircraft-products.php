<?php
session_start();

include "header.php";
include "config.php";

/* DEBUG (remove in production) */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ===============================
   FILTERS
================================ */
$search = trim($_GET['search'] ?? '');
$range  = $_GET['range'] ?? '7days';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = (int)($_GET['limit'] ?? 25);

$allowedLimits = [10, 25, 50, 100, 500, 1000];
if (!in_array($limit, $allowedLimits)) {
    $limit = 25;
}

$offset = ($page - 1) * $limit;

/* ===============================
   DATE RANGE
================================ */
if ($range === 'today') {
    $fromDate = date('Y-m-d 00:00:00');
} elseif ($range === '30days') {
    $fromDate = date('Y-m-d H:i:s', strtotime('-30 days'));
} else {
    $fromDate = date('Y-m-d H:i:s', strtotime('-7 days'));
}

/* ===============================
   WHERE CLAUSE (REUSED SAFELY)
================================ */
$where = [];
$whereParams = [];

if ($search !== '') {
    $where[] = "a.model LIKE :search";
    $whereParams[':search'] = "%$search%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ===============================
   KPI QUERY (FIXED, SAFE)
================================ */
$kpiParams = array_merge(
    [':from_date' => $fromDate],
    $whereParams
);

$stmt = $pdo->prepare("
SELECT
    SUM(ae.views)    AS views,
    SUM(ae.calls)    AS calls,
    SUM(ae.emails)   AS emails,
    SUM(ae.websites) AS websites
FROM aircrafts a
LEFT JOIN (
    SELECT 
        aircraft_id,
        SUM(event_type='view')    AS views,
        SUM(event_type='call')    AS calls,
        SUM(event_type='email')   AS emails,
        SUM(event_type='website') AS websites
    FROM aircraft_analytics
    WHERE created_at >= :from_date
    GROUP BY aircraft_id
) ae ON ae.aircraft_id = a.aircraft_id
$whereSQL
");

$stmt->execute(array_merge([':from_date'=>$fromDate], $whereParams));
$kpi = array_map('intval', $stmt->fetch(PDO::FETCH_ASSOC) ?: []);


/* Safety fallback */
$kpi = array_map(fn($v) => (int)$v, $kpi ?: []);

/* ===============================
   TOTAL ROWS (UNCHANGED LOGIC)
================================ */
$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM aircrafts a
$whereSQL
");
$stmt->execute($whereParams);
$totalRows = (int)$stmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $limit));


/* ===============================
   LISTING QUERY (UNCHANGED)
================================ */
$stmt = $pdo->prepare("
SELECT
    a.aircraft_id,
    a.model,
    'active' AS status,
    img.image_url,

    COALESCE(ae.views,0)    AS views,
    COALESCE(ae.calls,0)    AS calls,
    COALESCE(ae.emails,0)   AS emails,
    COALESCE(ae.websites,0) AS websites

FROM aircrafts a

LEFT JOIN (
    SELECT 
        aircraft_id,
        SUM(event_type='view')    AS views,
        SUM(event_type='call')    AS calls,
        SUM(event_type='email')   AS emails,
        SUM(event_type='website') AS websites
    FROM aircraft_analytics
    WHERE created_at >= :from_date
    GROUP BY aircraft_id
) ae ON ae.aircraft_id = a.aircraft_id

LEFT JOIN (
    SELECT product_id, MIN(image_url) AS image_url
    FROM product_images
    WHERE product_type='aircraft'
    GROUP BY product_id
) img ON img.product_id = a.aircraft_id

$whereSQL
ORDER BY views DESC
LIMIT $limit OFFSET $offset
");

$stmt->execute(array_merge([':from_date'=>$fromDate], $whereParams));



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>KPI Dashboard</title>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

   <!--Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

   <!--Tailwind (utility classes) -->
  <!--<script src="https://cdn.tailwindcss.com"></script>-->

   <!--Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

   <!--Chart.js -->
  <!--<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>-->

  <!-- GA tag (tracking only) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-V5JJXEWQ6S"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-V5JJXEWQ6S'); // your measurement ID
  </script>

 <style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:Inter,system-ui,sans-serif;
}

body{
  background:#f5f7fb;
  color:#0f172a;
}

.dashboard{
  max-width:1400px;
  margin:auto;
}

/* KPI CARDS */
.kpi-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:20px;
  margin-bottom:30px;
}

.kpi-card{
  background:#ffffff;
  border:1px solid #e2e8f0;
  border-radius:18px;
  padding:22px;
  display:flex;
  justify-content:space-between;
  box-shadow:0 6px 20px rgba(15,23,42,.06);
}

.kpi-card span{color:#64748b}
.kpi-card h2{margin:6px 0}
.kpi-card small{color:#22c55e;font-weight:600}

.icon{
  width:48px;height:48px;
  border-radius:14px;
  display:grid;
  place-items:center;
  font-size:20px;
}

.cyan{background:#e0f2fe;color:#0284c7}
.green{background:#dcfce7;color:#16a34a}
.yellow{background:#fef9c3;color:#ca8a04}
.purple{background:#ede9fe;color:#7c3aed}

/* TABLE */
.table-card{
  background:#ffffff;
  border:1px solid #e2e8f0;
  border-radius:20px;
  padding:24px;
  box-shadow:0 6px 24px rgba(15,23,42,.05);
}

.table-header p{color:#64748b;margin-top:4px}

.table-wrap{overflow-x:auto;margin-top:20px}

table{
  width:100%;
  border-collapse:collapse;
  min-width:1000px;
}

thead th{
  text-align:left;
  color:#64748b;
  font-size:13px;
  padding-bottom:12px;
}

tbody tr{
  border-top:1px solid #e2e8f0;
}

tbody td{
  padding:16px 0;
  vertical-align:middle;
}

.product{
  display:flex;
  align-items:center;
  gap:12px;
}

.prod-icon{
  width:40px;height:40px;
  border-radius:12px;
  background:#e0f2fe;
  color:#0284c7;
  display:grid;
  place-items:center;
}

.status{
  padding:6px 14px;
  border-radius:999px;
  font-size:13px;
  font-weight:600;
}

.status.active{
  background:#dcfce7;
  color:#16a34a;
}

.action{
  color:#64748b;
  cursor:pointer;
}

.action:hover{color:#0ea5e9}

.cyan-text{color:#0284c7}
.green-text{color:#16a34a}
.yellow-text{color:#ca8a04}
.purple-text{color:#7c3aed}

/* RESPONSIVE */
@media(max-width:768px){
  .dashboard{padding:10px}
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 24px;
  flex-wrap: wrap;
}

.pagination a, .pagination span {
  padding: 8px 14px;
  border-radius: 8px;
  background: #f1f3f5;
  color: #333;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.25s ease;
}

.pagination a:hover {
  background: #0d6efd;
  color: #fff;
}

.pagination a.active {
  background: #0d6efd;
  color: #fff;
  font-weight: 600;
}

.pagination span {
  background: transparent;
  color: #666;
  cursor: default;
}
.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

/* Left title section */
.table-header h2,
.table-header p {
  margin: 0;
}

/* Right side (search + filter) */
.table-header .header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Search box */
.table-header .search {
  position: relative;
}

.table-header .search i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
  font-size: 14px;
}

.table-header .search input {
  padding: 8px 12px 8px 34px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 14px;
  width: 200px;
}

/* Select dropdown */
.table-header .select {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 14px;
  background: #fff;
  cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
  .table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .table-header .header-right {
    width: 100%;
    justify-content: flex-start;
  }

  .table-header .search input {
    width: 100%;
  }

  .table-header .select {
    width: 100%;
  }
}
.prod-icon {
  width: 42px;
  height: 42px;
  border-radius: 6px;
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f3f3f3;
  margin-right: 8px;
}

.prod-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product {
  display: flex;
  align-items: center;
  gap: 10px;
}

</style>

</head>
<body>

  <div class="container-fluid p-4 p-lg-5">

<div class="dashboard">

  <!-- KPI CARDS -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div>
        <span>Total Views</span>
       <h2><?= number_format($kpi['views'] ?? 0) ?></h2>
      </div>
      <div class="icon cyan"><i class="bi bi-eye"></i></div>
    </div>

    <div class="kpi-card">
      <div>
        <span>Call Clicks</span>
       <h2><?= number_format($kpi['calls'] ?? 0) ?></h2>
      </div>
      <div class="icon green"><i class="bi bi-telephone"></i></div>
    </div>

    <div class="kpi-card">
      <div>
        <span>Email Clicks</span>
        <h2><?= number_format($kpi['emails'] ?? 0) ?></h2>
      </div>
      <div class="icon yellow"><i class="bi bi-envelope"></i></div>
    </div>

    <div class="kpi-card">
      <div>
        <span>Website Clicks</span>
        <h2><?= number_format($kpi['websites'] ?? 0) ?></h2>
      </div>
      <div class="icon purple"><i class="bi bi-globe"></i></div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-header">
      <h2>All Aircraft Products</h2>
      <p>Click any product to view detailed analytics</p>
     <form method="GET" class="header-right">
  <div class="search">
    <i class="bi bi-search"></i>
    <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search...">
  </div>

  <select name="range" class="select" onchange="this.form.submit()">
    <option value="today" <?= $range=='today'?'selected':'' ?>>Today</option>
    <option value="7days" <?= $range=='7days'?'selected':'' ?>>Last 7 Days</option>
    <option value="30days" <?= $range=='30days'?'selected':'' ?>>Last 30 Days</option>
  </select>

  <select name="limit" class="select" onchange="this.form.submit()">
    <?php foreach ([10,25,50,100,500,1000] as $size): ?>
      <option value="<?= $size ?>" <?= $limit==$size?'selected':'' ?>>
        <?= $size ?> rows
      </option>
    <?php endforeach; ?>
  </select>

  <input type="hidden" name="page" value="1">
</form>



    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>PRODUCT NAME</th>
            <th>VIEWS</th>
            <th>CALL CLICKS</th>
            <th>EMAIL CLICKS</th>
            <th>WEBSITE CLICKS</th>
            <th>STATUS</th>
            <th></th>
          </tr>
        </thead>

       <tbody>
<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
  <td class="product">
  <span class="prod-icon">
    <?php 
$imagePath = '';
if (!empty($row['image_url'])) {
    // Check vendors folder
    $vendorImage = "../vendors/" . $row['image_url'];
    $clientImage = "../clients/" . $row['image_url'];

    if (file_exists($vendorImage)) {
        $imagePath = $vendorImage;
    } elseif (file_exists($clientImage)) {
        $imagePath = $clientImage;
    }
}

if (!empty($imagePath)):
?>
    <img 
        src="<?= htmlspecialchars($imagePath) ?>" 
        alt="<?= htmlspecialchars($row['model']) ?>" 
        class="aircraft-thumb"
    >
<?php else: ?>
    <i class="bi bi-airplane"></i>
<?php endif; ?>

  </span>

  <?= htmlspecialchars($row['model']) ?>
</td>


  <td><i class="bi bi-eye cyan-text"></i> <?= number_format($row['views']) ?></td>
  <td><i class="bi bi-telephone green-text"></i> <?= number_format($row['calls']) ?></td>
  <td><i class="bi bi-envelope yellow-text"></i> <?= number_format($row['emails']) ?></td>
  <td><i class="bi bi-globe purple-text"></i> <?= number_format($row['websites']) ?></td>

  <td>
    <span class="status <?= $row['status'] === 'active' ? 'active' : 'inactive' ?>">
      <?= ucfirst($row['status']) ?>
    </span>
  </td>

  <td>
    <a href="https://flying411.com/<?= $row['model'] ?>/<?= (int)$row['aircraft_id'] ?>/ " target="_blank">
      <i class="bi bi-box-arrow-up-right action"></i>
    </a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>


      </table>
    </div>
   <div class="pagination">
<?php
$adj = 1;

if ($page > 1) {
    echo '<a href="?page='.($page-1).
         '&limit='.$limit.
         '&search='.urlencode($search).
         '&range='.$range.'">Prev</a>';
}

for ($i=1; $i<=$totalPages; $i++) {
    if ($i==1 || $i==$totalPages || ($i >= $page-$adj && $i <= $page+$adj)) {
        echo '<a href="?page='.$i.
             '&limit='.$limit.
             '&search='.urlencode($search).
             '&range='.$range.'" '.($i==$page?'class="active"':'').'>'.$i.'</a>';
    } elseif ($i==2 || $i==$totalPages-1) {
        echo '<span>...</span>';
    }
}

if ($page < $totalPages) {
    echo '<a href="?page='.($page+1).
         '&limit='.$limit.
         '&search='.urlencode($search).
         '&range='.$range.'">Next</a>';
}
?>
</div>


  </div>

</div>


  </div><!-- /container -->




<?php include "footer.php"; ?>
</body>
</html>