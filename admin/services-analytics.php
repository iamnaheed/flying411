<?php
session_start();
include "header.php";
require_once "config.php";
if(!isset($_SESSION['admin_id'])) exit;

$search = trim($_GET['search'] ?? '');
$range  = $_GET['range'] ?? '7days';
$page = max(1, (int)($_GET['page'] ?? 1));

$allowedLimits = [10, 25, 100, 500, 1000];
$limit = (int)($_GET['limit'] ?? 10);
$limit = in_array($limit, $allowedLimits) ? $limit : 10;

$offset = ($page - 1) * $limit;


$where=[]; $params=[];

if($search){
  $where[]="s.business_name LIKE :s";
  $params[':s']="%$search%";
}

if($range=='today')
  $where[]="DATE(a.created_at)=CURDATE()";
elseif($range=='30days')
  $where[]="a.created_at>=NOW()-INTERVAL 30 DAY";
else
  $where[]="a.created_at>=NOW()-INTERVAL 7 DAY";

$whereSQL=$where?"WHERE ".implode(" AND ",$where):"";

/* KPIs */
$kpi=$pdo->prepare("
SELECT
SUM(a.event_type='view') views,
SUM(a.event_type='call') calls,
SUM(a.event_type='email') emails,
SUM(a.event_type='website') websites
FROM service_analytics a
JOIN services s ON s.id=a.service_id
$whereSQL
");
$kpi->execute($params);
$kpi=$kpi->fetch();

/* Total rows */
$stmt=$pdo->prepare("
SELECT COUNT(DISTINCT s.id)
FROM services s
LEFT JOIN service_analytics a ON a.service_id=s.id
$whereSQL
");
$stmt->execute($params);
$totalPages=ceil($stmt->fetchColumn()/$limit);

/* Table */
$stmt = $pdo->prepare("
SELECT
  s.id,
  s.business_name,
  s.photos,
  SUM(a.event_type='view')    AS views,
  SUM(a.event_type='call')    AS calls,
  SUM(a.event_type='email')   AS emails,
  SUM(a.event_type='website') AS websites
FROM services s
LEFT JOIN service_analytics a ON a.service_id = s.id
$whereSQL
GROUP BY s.id
ORDER BY views DESC
LIMIT $limit OFFSET $offset
");
$stmt->execute($params);

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

.pagination a {
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
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
}

/* Mobile friendly */
@media (max-width: 576px) {
  .pagination a {
    padding: 6px 10px;
    font-size: 13px;
  }
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
    <h2>All Aircraft Services</h2>
    <p>Click any service to view detailed analytics</p>
    <form method="GET" class="header-right">
  <div class="search">
    <i class="bi bi-search"></i>
    <input name="search"
           value="<?= htmlspecialchars($search) ?>"
           placeholder="Search service...">
  </div>

  <select name="range" class="select" onchange="this.form.submit()">
    <option value="today" <?= $range=='today'?'selected':'' ?>>Today</option>
    <option value="7days" <?= $range=='7days'?'selected':'' ?>>Last 7 Days</option>
    <option value="30days" <?= $range=='30days'?'selected':'' ?>>Last 30 Days</option>
  </select>

  <!-- Rows per page -->
  <select name="limit" class="select" onchange="this.form.submit()">
    <?php foreach ([10,25,100,500,1000] as $l): ?>
      <option value="<?= $l ?>" <?= $limit==$l?'selected':'' ?>>
        <?= $l ?> rows
      </option>
    <?php endforeach; ?>
  </select>

  <!-- Reset page when filters change -->
  <input type="hidden" name="page" value="1">
</form>


  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>SERVICE NAME</th>
          <th>VIEWS</th>
          <th>CALL CLICKS</th>
          <th>EMAIL CLICKS</th>
          <th>WEBSITE CLICKS</th>
          <th>STATUS</th>
          <th></th>
        </tr>
      </thead>

     <tbody>
<?php while($r=$stmt->fetch()): ?>
<?php
$serviceImage = null;

if (!empty($r['photos'])) {
    $photos = json_decode($r['photos'], true);

    if (is_array($photos) && !empty($photos[0])) {
        // Adjust path if needed
        $serviceImage = '../vendors/uploads/' . $photos[0];
    }
}
?>
<tr>
<td class="product">
  <span class="prod-icon">
    <?php if ($serviceImage): ?>
      <img 
        src="<?= htmlspecialchars($serviceImage) ?>" 
        alt="<?= htmlspecialchars($r['business_name']) ?>" 
        loading="lazy"
      >
    <?php else: ?>
      <i class="fa-solid fa-plane-up"></i>
    <?php endif; ?>
  </span>

  <?= htmlspecialchars($r['business_name']) ?>
</td>



<td><i class="bi bi-eye"></i> <?= (int)$r['views'] ?></td>
<td><i class="bi bi-telephone"></i> <?= (int)$r['calls'] ?></td>
<td><i class="bi bi-envelope"></i> <?= (int)$r['emails'] ?></td>
<td><i class="bi bi-globe"></i> <?= (int)$r['websites'] ?></td>

<td>
<a href="https://flying411.com/services/<?= $r['business_name']?>/<?= $r['id'] ?>" target="_blank">
<i class="bi bi-box-arrow-up-right"></i>
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>

    </table>
    <?php
if ($totalPages > 1):
?>
<div class="pagination">

<?php
$adj = 1;

/* Prev */
if ($page > 1):
?>
<a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&range=<?= $range ?>&limit=<?= $limit ?>">
  Prev
</a>
<?php endif; ?>

<?php
for ($i = 1; $i <= $totalPages; $i++) {

    if (
        $i == 1 ||
        $i == $totalPages ||
        ($i >= $page - $adj && $i <= $page + $adj)
    ) {
        ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&range=<?= $range ?>&limit=<?= $limit ?>"
           class="<?= $i == $page ? 'active' : '' ?>">
           <?= $i ?>
        </a>
        <?php
    }
    elseif ($i == 2 && $page > ($adj + 2)) {
        echo '<span class="dots">...</span>';
    }
    elseif ($i == $totalPages - 1 && $page < ($totalPages - $adj - 1)) {
        echo '<span class="dots">...</span>';
    }
}
?>

<?php
/* Next */
if ($page < $totalPages):
?>
<a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&range=<?= $range ?>&limit=<?= $limit ?>">
  Next
</a>
<?php endif; ?>

</div>
<?php endif; ?>

  </div>
</div>




</div>


  </div><!-- /container -->




<?php include "footer.php"; ?>
</body>
</html>