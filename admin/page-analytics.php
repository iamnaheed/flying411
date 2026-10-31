<?php
session_start();
require_once "config.php";
include "header.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ======================
   SAFE INPUTS
====================== */
$search = trim($_GET['search'] ?? '');
$range  = $_GET['range'] ?? '7days';
$page   = max(1, (int)($_GET['page'] ?? 1));

$allowedLimits = [10,25,50,100,500,1000];
$limit = (isset($_GET['limit']) && in_array((int)$_GET['limit'], $allowedLimits))
    ? (int)$_GET['limit']
    : 10;

$offset = ($page - 1) * $limit;

/* ======================
   DATE RANGE (FIXED & ACCURATE)
====================== */
$toDate = date('Y-m-d H:i:s');

switch ($range) {
    case 'today':
        $fromDate = date('Y-m-d 00:00:00');
        break;

    case '30days':
        $fromDate = date('Y-m-d H:i:s', strtotime('-30 days'));
        break;

    case '7days':
    default:
        $fromDate = date('Y-m-d H:i:s', strtotime('-7 days'));
        break;
}

/* ======================
   WHERE CONDITIONS
====================== */
$where = [];
$params = [];

$where[] = "created_at BETWEEN :from AND :to";
$params[':from'] = $fromDate;
$params[':to']   = $toDate;

if ($search !== '') {
    $where[] = "page_url LIKE :search";
    $params[':search'] = "%{$search}%";
}

$whereSQL = "WHERE " . implode(" AND ", $where);

/* ======================
   KPI: TOTAL UNIQUE PAGES
====================== */
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT page_url)
    FROM page_analytics
    $whereSQL
");
$stmt->execute($params);
$totalPages = (int)$stmt->fetchColumn();

/* ======================
   KPI: AVG VISITORS / PAGE
====================== */
$stmt = $pdo->prepare("
    SELECT 
        IFNULL(
            ROUND(
                COUNT(DISTINCT session_id) / NULLIF(COUNT(DISTINCT page_url),0)
            ),0
        )
    FROM page_analytics
    $whereSQL
");
$stmt->execute($params);
$avgVisitors = (int)$stmt->fetchColumn();

/* ======================
   KPI: AVG TIME SPENT
====================== */
$stmt = $pdo->prepare("
    SELECT IFNULL(AVG(time_spent),0)
    FROM page_analytics
    $whereSQL
");
$stmt->execute($params);
$avgSec  = (int)$stmt->fetchColumn();
$avgTime = floor($avgSec / 60) . "m " . ($avgSec % 60) . "s";

/* ======================
   KPI: EXIT RATE (CORRECT LOGIC)
====================== */
$stmt = $pdo->prepare("
    SELECT 
        IFNULL(ROUND(SUM(is_exit) / COUNT(*) * 100),0)
    FROM (
        SELECT session_id,
               CASE WHEN COUNT(*) = 1 THEN 1 ELSE 0 END AS is_exit
        FROM page_analytics
        $whereSQL
        GROUP BY session_id
    ) t
");
$stmt->execute($params);
$exitRate = (int)$stmt->fetchColumn();

/* ======================
   PAGINATION COUNT
====================== */
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT page_url)
    FROM page_analytics
    $whereSQL
");
$stmt->execute($params);
$totalRows = (int)$stmt->fetchColumn();
$totalPagesNum = max(1, ceil($totalRows / $limit));

/* ======================
   PAGE LIST (FINAL & ACCURATE)
====================== */
$stmt = $pdo->prepare("
    SELECT
        page_url,
        COUNT(DISTINCT session_id) AS visits,
        COUNT(DISTINCT user_ip)    AS unique_visitors,
        ROUND(AVG(time_spent))     AS avg_time,
        MAX(created_at)            AS last_visit
    FROM page_analytics
    $whereSQL
    GROUP BY page_url
    ORDER BY visits DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

/* ======================
   HELPER
====================== */
function timeAgo($date){
    if (!$date) return '-';
    $seconds = time() - strtotime($date);

    if ($seconds < 60) return "Just now";
    if ($seconds < 3600) return floor($seconds/60)." min ago";
    if ($seconds < 86400) return floor($seconds/3600)." hr ago";
    return floor($seconds/86400)." days ago";
}
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

/* =====================
   LAYOUT
===================== */
.dashboard{
  padding:28px;
  max-width:1400px;
  margin:auto;
}

/* =====================
   HEADER
===================== */
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:20px;
}

.topbar h1{
  color:#0ea5e9;
  font-size:28px;
}

.topbar p{
  color:#64748b;
}

.top-actions{
  display:flex;
  align-items:center;
  gap:14px;
}

.search-box{
  background:#ffffff;
  border:1px solid #e2e8f0;
  padding:10px 14px;
  border-radius:12px;
  display:flex;
  align-items:center;
  gap:8px;
}

.search-box i{
  color:#94a3b8;
}

.search-box input{
  background:none;
  border:none;
  color:#0f172a;
  outline:none;
}

.date-btn{
  background:#ffffff;
  border:1px solid #e2e8f0;
  color:#0f172a;
  padding:10px 14px;
  border-radius:12px;
  cursor:pointer;
}

.notification{
  font-size:20px;
  color:#475569;
}

.avatar{
  width:36px;
  height:36px;
  border-radius:50%;
  background:linear-gradient(135deg,#38bdf8,#6366f1);
  display:grid;
  place-items:center;
  color:#ffffff;
  font-weight:700;
}

/* =====================
   KPI GRID
===================== */
.kpi-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:20px;
  margin:30px 0;
}

.kpi-card{
  background:#ffffff;
  border:1px solid #e2e8f0;
  border-radius:18px;
  padding:22px;
  display:flex;
  align-items:center;
  gap:18px;
  box-shadow:0 6px 20px rgba(15,23,42,.06);
}

.kpi-card span{
  color:#64748b;
  font-size:14px;
}

.kpi-card h3{
  font-size:28px;
  margin-top:4px;
  color:#0f172a;
}

/* KPI ICONS */
.icon{
  width:50px;
  height:50px;
  border-radius:14px;
  display:grid;
  place-items:center;
  font-size:22px;
}

.cyan{
  background:#e0f2fe;
  color:#0284c7;
}

.green{
  background:#dcfce7;
  color:#16a34a;
}

.yellow{
  background:#fef9c3;
  color:#ca8a04;
}

.purple{
  background:#ede9fe;
  color:#7c3aed;
}

/* =====================
   TABLE CARD
===================== */
.table-card{
  background:#ffffff;
  border:1px solid #e2e8f0;
  border-radius:20px;
  padding:24px;
  box-shadow:0 6px 24px rgba(15,23,42,.05);
}

.table-header h2{
  font-size:20px;
  color:#0f172a;
}

.table-header p{
  color:#64748b;
  margin-top:4px;
}

/* TABLE */
.table-wrap{
  overflow-x:auto;
  margin-top:20px;
  display: flex;
 flex-direction: column-reverse;
}

table{
  width:100%;
  border-collapse:collapse;
  min-width:900px;
}

thead th{
  text-align:left;
  color:#64748b;
  font-size:13px;
  padding-bottom:12px;
}

tbody tr{
  border-top:1px solid #e2e8f0;
  transition:background .15s ease;
}

tbody tr:hover{
  background:#f8fafc;
}

tbody td{
  padding:14px 0;
  color:#0f172a;
}

/* PAGE URL BADGE */
.page-badge{
  background:#e0f2fe;
  padding:6px 10px;
  border-radius:8px;
  color:#0284c7;
  font-weight:600;
}

/* TYPE PILLS */
.pill{
  padding:6px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:600;
}

.home{
  background:#e0f2fe;
  color:#0284c7;
}

.category{
  background:#dcfce7;
  color:#16a34a;
}

/* ACTION ICON */
tbody i{
  cursor:pointer;
  color:#64748b;
  transition:color .15s ease;
}

tbody i:hover{
  color:#0ea5e9;
}

/* =====================
   RESPONSIVE
===================== */
@media(max-width:768px){
  .topbar{
    flex-direction:column;
    align-items:flex-start;
  }
}
.header-right{
  display:flex;
  align-items:center;
  gap:14px;
}

.search{
  background:#fff;
  border:1px solid var(--border);
  border-radius:12px;
  padding:10px 14px;
  display:flex;
  align-items:center;
  gap:8px;
}

.search input{
  border:none;
  outline:none;
}

.select{
  background:#fff;
  border:1px solid var(--border);
  padding:10px 14px;
  border-radius:12px;
  cursor:pointer;
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

</style>

</head>
<body>

  <div class="container-fluid p-4 p-lg-5" style="max-width:85%"> 

  <header class="topbar">
    <div>
      <h1>Page Analytics</h1>
      <p>Track every page — static + dynamic</p>
    </div>
    <div class="header-right">
  <form method="GET" class="search">
    <i class="bi bi-search"></i>
    <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search...">
    <input type="hidden" name="range" value="<?= $range ?>">
    <input type="hidden" name="limit" value="<?= $limit ?>">
  </form>

  <form method="GET">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="limit" value="<?= $limit ?>">
    <select name="range" class="select" onchange="this.form.submit()">
      <option value="today" <?= $range=='today'?'selected':'' ?>>Today</option>
      <option value="7days" <?= $range=='7days'?'selected':'' ?>>Last 7 Days</option>
      <option value="30days" <?= $range=='30days'?'selected':'' ?>>Last 30 Days</option>
    </select>
  </form>

  <!-- PAGE SIZE (NEW, SAME STYLE) -->
  <form method="GET">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="range" value="<?= $range ?>">
    <select name="limit" class="select" onchange="this.form.submit()">
      <?php foreach ([10,25,50,100,500,1000] as $l): ?>
        <option value="<?= $l ?>" <?= $limit==$l?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>


  </header>

  <!-- KPI CARDS -->
  <section class="kpi-grid">
    <div class="kpi-card">
      <div class="icon cyan"><i class="bi bi-eye"></i></div>
      <div>
        <span>Total Pages</span>
       <h3><?= $totalPages ?></h3>

      </div>
    </div>

    <div class="kpi-card">
      <div class="icon green"><i class="bi bi-people"></i></div>
      <div>
        <span>Avg. Visitors/Page</span>
       <h3><?= number_format($avgVisitors) ?></h3>

      </div>
    </div>

    <div class="kpi-card">
      <div class="icon yellow"><i class="bi bi-clock"></i></div>
      <div>
        <span>Avg. Time on Page</span>
        <h3><?= $avgTime ?></h3>

      </div>
    </div>

    <div class="kpi-card">
      <div class="icon purple"><i class="bi bi-box-arrow-up-right"></i></div>
      <div>
        <span>Exit Rate</span>
       <h3><?= $exitRate ?>%</h3>

      </div>
    </div>
  </section>

  <!-- TABLE -->
  <section class="table-card">
    <div class="table-header">
      <h2>All Pages</h2>
      <p>Click any page to view detailed KPIs</p>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Page URL</th>
            <th>Type</th>
            <th>Total Visits</th>
            <th>Unique Visitors</th>
            <th>Avg Time</th>
            <th>Last Visit</th>
            <th></th>
          </tr>
        </thead>

       <tbody>
<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
    $avgSeconds = (int)$row['avg_time'];
    $m = floor($avgSeconds / 60);
    $s = $avgSeconds % 60;
?>
<tr>
  <td><span class="page-badge"><?= htmlspecialchars($row['page_url']) ?></span></td>
  <td><span class="pill category">Dynamic</span></td>
  <td><?= number_format($row['visits']) ?></td>
  <td><?= number_format($row['unique_visitors']) ?></td>
  <td><?= "{$m}m {$s}s" ?></td>
  <td><?= timeAgo($row['last_visit']) ?></td>
  <td>
    <a href="<?= htmlspecialchars($row['page_url']) ?>"
       target="_blank"
       class="text-dark"
       title="Open page in new tab">
      <i class="bi bi-box-arrow-up-right"></i>
    </a>
  </td>
</tr>
<?php endwhile; ?>

</tbody>

    </div>
  <div class="pagination">
<?php
$query = '&search='.urlencode($search)
       . '&range='.$range
       . '&limit='.$limit;

if ($page > 1) {
    echo '<a href="?page='.($page-1).$query.'">Prev</a>';
}

for ($i = 1; $i <= $totalPagesNum; $i++) {
    if ($i == 1 || $i == $totalPagesNum || ($i >= $page-1 && $i <= $page+1)) {
        echo '<a href="?page='.$i.$query.'" '.($i==$page?'class="active"':'').'>'.$i.'</a>';
    } elseif ($i == 2 || $i == $totalPagesNum-1) {
        echo '<span>...</span>';
    }
}

if ($page < $totalPagesNum) {
    echo '<a href="?page='.($page+1).$query.'">Next</a>';
}
?>
</div>



  </section>
</div>


  </div>
  <!-- /container -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let start = Date.now();
window.addEventListener("beforeunload", function () {
  navigator.sendBeacon(
    "update_time.php",
    "time=" + Math.round((Date.now() - start) / 1000)
  );
});
</script>




<?php include "footer.php"; ?>
</body>
</html>