<?php
session_start();
include "header.php";
include "config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ===============================
   FILTERS
================================ */
$range = $_GET['range'] ?? '7days';

switch ($range) {
    case 'today':
        $dateSQL = ">= CURDATE()";
        break;
    case '30days':
        $dateSQL = ">= NOW() - INTERVAL 30 DAY";
        break;
    default:
        $dateSQL = ">= NOW() - INTERVAL 7 DAY";
}

/* ===============================
   TOTAL KPIs (ALL PRODUCTS)
================================ */
$totalViews  = 0;
$totalClicks = 0;

$analyticsTables = [
    'aircraft_analytics',
    'engine_analytics',
    'parts_analytics',
    'service_analytics'
];

foreach ($analyticsTables as $table) {
    $stmt = $pdo->query("
        SELECT
            SUM(event_type = 'view') AS views,
            SUM(event_type IN ('call','email','website')) AS clicks
        FROM {$table}
        WHERE created_at {$dateSQL}
    ");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalViews  += (int)($row['views'] ?? 0);
    $totalClicks += (int)($row['clicks'] ?? 0);
}

$conversionRate = $totalViews > 0
    ? round(($totalClicks / $totalViews) * 100, 2)
    : 0;

/* ===============================
   CATEGORY STATS FUNCTION
================================ */
function categoryStats(
    PDO $pdo,
    string $analyticsTable,
    string $analyticsFK,   // aircraft_id / engine_id / part_id / service_id
    string $mainTable,
    string $mainPK,        // aircraft_id / engine_id / part_id / id
    string $nameCol,
    string $dateSQL
) {

    // TOTAL VIEWS & CLICKS
    $stats = $pdo->query("
        SELECT
            SUM(event_type = 'view') AS views,
            SUM(event_type IN ('call','email','website')) AS clicks
        FROM {$analyticsTable}
        WHERE created_at {$dateSQL}
    ")->fetch(PDO::FETCH_ASSOC);

    // MOST VIEWED
    $topViewed = $pdo->query("
        SELECT m.{$nameCol}
        FROM {$analyticsTable} a
        JOIN {$mainTable} m ON m.{$mainPK} = a.{$analyticsFK}
        WHERE a.event_type = 'view'
          AND a.created_at {$dateSQL}
        GROUP BY m.{$mainPK}
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ")->fetchColumn();

    // MOST CONVERTED
    $topConverted = $pdo->query("
        SELECT m.{$nameCol}
        FROM {$analyticsTable} a
        JOIN {$mainTable} m ON m.{$mainPK} = a.{$analyticsFK}
        WHERE a.event_type IN ('call','email','website')
          AND a.created_at {$dateSQL}
        GROUP BY m.{$mainPK}
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ")->fetchColumn();

    return [
        'views' => (int)($stats['views'] ?? 0),
        'clicks' => (int)($stats['clicks'] ?? 0),
        'top_viewed' => $topViewed ?: '—',
        'top_converted' => $topConverted ?: '—'
    ];
}

/* ===============================
   CATEGORY DATA
================================ */
$aircraft = categoryStats(
    $pdo,
    'aircraft_analytics',
    'aircraft_id',
    'aircrafts',
    'aircraft_id',
    'model',
    $dateSQL
);

$engine = categoryStats(
    $pdo,
    'engine_analytics',
    'engine_id',
    'engines',
    'engine_id',
    'model',
    $dateSQL
);

$part = categoryStats(
    $pdo,
    'parts_analytics',
    'part_id',
    'parts',
    'part_id',
    'part_number',
    $dateSQL
);

$service = categoryStats(
    $pdo,
    'service_analytics',
    'service_id',
    'services',
    'id',                 // ✅ FIX IS HERE
    'service_type',
    $dateSQL
);


/* ===============================
   TOP CATEGORY
================================ */
$topCategory = [
    'Aircraft' => $aircraft['views'],
    'Engines'  => $engine['views'],
    'Parts'    => $part['views'],
    'Services' => $service['views']
];

arsort($topCategory);
$topCategoryName = array_key_first($topCategory);
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
:root{
  --bg:#f4f7fb;
  --card:#ffffff;
  --border:#e5eaf2;
  --text:#0f172a;
  --muted:#64748b;
  --primary:#0ea5e9;
}

*{box-sizing:border-box;margin:0;padding:0;font-family:Inter,system-ui,sans-serif}

body{
  background:var(--bg);
  color:var(--text);
}

/* =========================
   LAYOUT
========================= */
.wrapper{
  max-width:1400px;
  margin:auto;
  padding:28px;
}

/* =========================
   HEADER
========================= */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:20px;
}

.header h1{
  color:var(--primary);
  font-size:28px;
}

.header p{
  color:var(--muted);
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

.avatar{
  width:36px;height:36px;
  border-radius:50%;
  background:linear-gradient(135deg,#38bdf8,#6366f1);
  color:#fff;
  display:grid;
  place-items:center;
  font-weight:700;
}

/* =========================
   KPI CARDS
========================= */
.kpi-grid{
  margin:32px 0;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:20px;
}

.kpi{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:18px;
  padding:22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 6px 20px rgba(15,23,42,.06);
}

.kpi h3{
  font-size:28px;
  margin-top:6px;
}

.kpi span{
  color:var(--muted);
  font-size:14px;
}

.kpi small{
  color:#16a34a;
  font-weight:600;
}

.kpi-icon{
  width:48px;
  height:48px;
  border-radius:14px;
  display:grid;
  place-items:center;
  font-size:22px;
}

.blue{background:#e0f2fe;color:#0284c7}
.green{background:#dcfce7;color:#16a34a}
.orange{background:#fef3c7;color:#ca8a04}
.purple{background:#ede9fe;color:#7c3aed}

/* =========================
   CATEGORY GRID
========================= */
.category-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(420px,1fr));
  gap:24px;
}

.category{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:20px;
  padding:24px;
  box-shadow:0 6px 24px rgba(15,23,42,.05);
}

.category-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.category-header h2{
  font-size:20px;
}

.category-header p{
  color:var(--muted);
  font-size:14px;
}

.stats{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:16px;
  margin:24px 0;
}

.stat-box{
  background:#f8fafc;
  border-radius:14px;
  padding:16px;
}

.stat-box span{
  color:var(--muted);
  font-size:13px;
}

.stat-box h3{
  margin-top:6px;
}

.meta{
  display:flex;
  justify-content:space-between;
  margin-top:12px;
  font-size:14px;
}

.meta span{
  color:var(--muted);
}

.meta b{
  color:var(--text);
}

/* =========================
   RESPONSIVE
========================= */
@media(max-width:768px){
  .header{flex-direction:column;align-items:flex-start}
  .category-grid{grid-template-columns:1fr}
}

</style>

</head>
<body>

  <div class="container-fluid p-4 p-lg-5">

 <div class="header">
    <div>
      <h1>Products Analytics</h1>
      <p>Track performance across all product categories</p>
    </div>

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
</form>

  </div>

  <div class="kpi-grid">
  <div class="kpi">
    <div>
      <span>Total Product Views</span>
      <h3><?= number_format($totalViews) ?></h3>
    </div>
    <div class="kpi-icon blue"><i class="bi bi-eye"></i></div>
  </div>

  <div class="kpi">
    <div>
      <span>Total Clicks</span>
      <h3><?= number_format($totalClicks) ?></h3>
    </div>
    <div class="kpi-icon green"><i class="bi bi-cursor"></i></div>
  </div>

  <div class="kpi">
    <div>
      <span>Conversion Rate</span>
      <h3><?= $conversionRate ?>%</h3>
    </div>
    <div class="kpi-icon orange"><i class="bi bi-graph-up"></i></div>
  </div>

  <div class="kpi">
    <div>
      <span>Top Category</span>
      <h3><?= $topCategoryName ?></h3>
    </div>
    <div class="kpi-icon purple"><i class="bi bi-star"></i></div>
  </div>
</div>

<div class="category-grid">

<?php
$categories = [
    'Aircraft' => $aircraft,
    'Engines'  => $engine,
    'Parts'    => $part,
    'Services' => $service
];

foreach ($categories as $title => $data):
?>
  <div class="category">
    <div class="category-header">
      <div>
        <h2><?= $title ?></h2>
        <p>Category Analytics</p>
      </div>
      ➜
    </div>

    <div class="stats">
      <div class="stat-box">
        <span>Total Views</span>
        <h3><?= number_format($data['views']) ?></h3>
      </div>
      <div class="stat-box">
        <span>Total Clicks</span>
        <h3><?= number_format($data['clicks']) ?></h3>
      </div>
    </div>

    <div class="meta">
      <span>Most Viewed</span>
      <b><?= htmlspecialchars($data['top_viewed']) ?></b>
    </div>

    <div class="meta">
      <span>Most Converted</span>
      <b><?= htmlspecialchars($data['top_converted']) ?></b>
    </div>
  </div>
<?php endforeach; ?>

</div>



  </div><!-- /container -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let sessionsLine, sourcesDoughnut;

/* helpers */
const fmtNumber = n => Number(n || 0).toLocaleString();

/* main loader */
async function loadGA(){
  try{
    const res  = await fetch('ga-report.php');
    const data = await res.json();

    /* ======================
       KPI CARDS
    ====================== */
    document.getElementById('kpi-needs').textContent    = fmtNumber(data.cards.totalVisitors);
    document.getElementById('kpi-overdue').textContent  = fmtNumber(data.cards.uniqueVisitors);
    document.getElementById('kpi-sessions').textContent = fmtNumber(data.cards.pageViews);
    document.getElementById('kpi-emails').textContent   = fmtNumber(data.cards.productViews);

    /* ======================
       LINE CHART – VISITORS
    ====================== */
    if(sessionsLine) sessionsLine.destroy();
    sessionsLine = new Chart(
      document.getElementById('lineSessions'),
      {
        type:'line',
        data:{
          labels:data.timeseries.labels,
          datasets:[{
            label:'Visitors',
            data:data.timeseries.values,
            tension:.35,
            fill:true,
            borderWidth:2,
            borderColor:'#4f46e5',
            backgroundColor:'rgba(79,70,229,.12)'
          }]
        },
        options:{
          responsive:true,
          plugins:{ legend:{display:false} },
          scales:{ y:{ beginAtZero:true } }
        }
      }
    );

    /* ======================
       DOUGHNUT – INTERACTIONS
    ====================== */
    if(sourcesDoughnut) sourcesDoughnut.destroy();
    sourcesDoughnut = new Chart(
      document.getElementById('doughnutSources'),
      {
        type:'doughnut',
        data:{
          labels:data.interactions.labels,
          datasets:[{
            data:data.interactions.values,
            backgroundColor:[
              '#22c55e',
              '#60a5fa',
              '#f59e0b'
            ]
          }]
        },
        options:{
          cutout:'70%',
          plugins:{
            legend:{ display:false }
          }
        }
      }
    );

    /* ======================
       TOP PAGES
    ====================== */
    const wrap = document.getElementById('topPagesWrap');
    wrap.innerHTML = '';

    data.topPages.forEach(p=>{
      const row = document.createElement('div');
      row.className =
        'd-flex justify-content-between align-items-center py-2 border-bottom';
      row.innerHTML = `
        <div class="text-truncate" style="max-width:70%">
          <i class="bi bi-link-45deg me-1 text-primary"></i>
          <span class="fw-semibold">${p.path}</span>
        </div>
        <div class="text-muted small">${fmtNumber(p.views)} views</div>
      `;
      wrap.appendChild(row);
    });

  }catch(err){
    console.error('Dashboard load error:', err);
  }
}

loadGA();
</script>




<?php include "footer.php"; ?>
</body>
</html>