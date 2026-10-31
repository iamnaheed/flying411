<?php
session_start();
require "config.php";
require "header.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ===============================
   DATE FILTER
================================ */
$range = $_GET['range'] ?? '7days';

$dateWhere = "created_at >= NOW() - INTERVAL 7 DAY";

if ($range === 'today') {
    $dateWhere = "created_at >= CURDATE()";
} elseif ($range === '30days') {
    $dateWhere = "created_at >= NOW() - INTERVAL 30 DAY";
}

/* ===============================
   ANALYTICS TABLES
================================ */
$tables = [
    'aircraft_analytics',
    'engine_analytics',
    'parts_analytics',
    'service_analytics'
];

/* ===============================
   TOTAL PRODUCT ANALYTICS
================================ */
$totalProductViews = $totalClicks = $callClicks = $emailClicks = $websiteClicks = 0;

foreach ($tables as $table) {
    $stmt = $pdo->query("
        SELECT
            SUM(event_type='view')     AS views,
            SUM(event_type!='view')    AS clicks,
            SUM(event_type='call')     AS calls,
            SUM(event_type='email')    AS emails,
            SUM(event_type='website')  AS websites
        FROM {$table}
        WHERE {$dateWhere}
    ");

    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalProductViews += (int)$r['views'];
    $totalClicks       += (int)$r['clicks'];
    $callClicks        += (int)$r['calls'];
    $emailClicks       += (int)$r['emails'];
    $websiteClicks     += (int)$r['websites'];
}

/* ===============================
   VISITORS (FILTERED)
================================ */
$stmt = $pdo->query("
    SELECT 
        COUNT(*) AS page_views,
        COUNT(DISTINCT session_id) AS unique_visitors
    FROM (
        SELECT session_id FROM aircraft_analytics WHERE {$dateWhere}
        UNION ALL
        SELECT session_id FROM engine_analytics WHERE {$dateWhere}
        UNION ALL
        SELECT session_id FROM parts_analytics WHERE {$dateWhere}
        UNION ALL
        SELECT session_id FROM service_analytics WHERE {$dateWhere}
    ) t
");

$visitor = $stmt->fetch(PDO::FETCH_ASSOC);
$totalVisitors  = (int)$visitor['page_views'];
$uniqueVisitors = (int)$visitor['unique_visitors'];
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
/* =========================
   ROOT COLORS (SAFE FALLBACK)
========================= */

.sidebar .nav:not(.sub-menu) > .nav-item.active{
    background-color: #ededed !important;
}
:root{
  --text:#0f172a;
  --muted:#6b7280;
  --border:#e6ebf2;
}

/* =========================
   SOFT CARD BASE
========================= */
.soft-card{
  background: linear-gradient(180deg,#ffffff 0%,#fbfcff 100%);
  border:1px solid var(--border);
  border-radius:18px;
  box-shadow:0 4px 14px rgba(15,23,42,.06);
  transition:.25s ease;
}

.soft-card:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 24px rgba(15,23,42,.10);
}

/* =========================
   KPI CARD (FIXED)
========================= */
.kpi-card{
  position:relative;
  padding:20px 64px 20px 20px; /* 🔥 RIGHT SPACE FOR ICON */
  min-height:120px;
  overflow:hidden;
}

/* Decorative glow */
.kpi-card::after{
  content:"";
  position:absolute;
  right:-40px;
  bottom:-60px;
  width:180px;
  height:180px;
  background:radial-gradient(circle,rgba(79,70,229,.10),transparent 70%);
}

/* KPI TEXT */
.kpi-title{
  font-size:13px;
  font-weight:600;
  color:var(--muted);
  margin-bottom:4px;
}

.kpi-value{
  font-size:30px;
  font-weight:800;
  letter-spacing:-.4px;
  color:var(--text);
  line-height:1.2;
}

.kpi-delta{
  font-size:13px;
  font-weight:600;
  margin-top:6px;
}

/* KPI ICON – PERFECT ALIGN */
.kpi-icon{
  position:absolute;
  top:18px;
  right:18px;
  width:46px;
  height:46px;
  border-radius:14px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:20px;
  background:linear-gradient(135deg,#eef2ff,#e0e7ff);
  color:#4f46e5;
  box-shadow:inset 0 0 0 1px rgba(79,70,229,.15);
}

/* =========================
   SECTION / CHART HEADERS
========================= */
.section-title{
  font-weight:700;
  font-size:17px;
  color:var(--text);
}

.chart-card{
  padding:20px;
}

.chart-title{
  font-weight:700;
  font-size:16px;
  color:var(--text);
}

.chart-sub{
  font-size:13px;
  color:var(--muted);
}

/* =========================
   LIST ROWS (AIRCRAFT)
========================= */
.list-row{
  display:flex;
  align-items:center;
  gap:14px;
  padding:12px 0;
}

.circle{
  width:40px;
  height:40px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(135deg,#eef2ff,#e0e7ff);
  color:#4f46e5;
  flex-shrink:0;
}

/* =========================
   PROGRESS BAR
========================= */
.progress-bar-slim{
  height:8px;
  background:#eef2f7;
  border-radius:999px;
  overflow:hidden;
}

.progress-bar-slim span{
  display:block;
  height:100%;
  border-radius:999px;
}

/* =========================
   DONUT LEGEND (FIXED ALIGN)
========================= */
.legend-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:10px 0;
  border-bottom:1px solid var(--border);
  font-size:14px;
}

.legend-row:last-child{
  border-bottom:0;
}

.legend-left{
  display:flex;
  align-items:center;
  gap:10px;
}

.legend-dot{
  width:10px;
  height:10px;
  border-radius:50%;
}

/* =========================
   MINI MARKET CARDS
========================= */
.mini{
  padding:16px;
  border-radius:14px;
  background:linear-gradient(180deg,#ffffff,#f9fbff);
}

/* =========================
   BUTTON POLISH
========================= */
.btn-primary{
  box-shadow:0 6px 16px rgba(79,70,229,.25);
}

.btn-ghost{
  background:#fff;
}

/* =========================
   RESPONSIVE FIX
========================= */
@media(max-width:768px){
  .kpi-card{
    padding-right:20px;
  }
  .kpi-icon{
    position:static;
    margin-top:10px;
  }
}

.soft-card{
    padding:12px;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


</head>
<body>

 <div class="container-fluid p-4 p-lg-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">Admin KPI <span class="text-muted">Dashboard</span></h2>
      <div class="text-muted">Analytics & marketplace overview</div>
    </div>

    <div class="btn-group">
      <a href="?range=today"  class="btn btn-sm <?= $range=='today'?'btn-primary':'btn-outline-primary' ?>">Today</a>
      <a href="?range=7days"  class="btn btn-sm <?= $range=='7days'?'btn-primary':'btn-outline-primary' ?>">7 Days</a>
      <a href="?range=30days" class="btn btn-sm <?= $range=='30days'?'btn-primary':'btn-outline-primary' ?>">30 Days</a>
    </div>
  </div>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="soft-card kpi-card">
        <div class="kpi-title">Total Website Visitors</div>
        <div class="kpi-value"><?= number_format($totalVisitors) ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="soft-card kpi-card">
        <div class="kpi-title">Unique Visitors</div>
        <div class="kpi-value"><?= number_format($uniqueVisitors) ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="soft-card kpi-card">
        <div class="kpi-title">Product Views</div>
        <div class="kpi-value"><?= number_format($totalProductViews) ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="soft-card kpi-card">
        <div class="kpi-title">Total Interactions</div>
        <div class="kpi-value"><?= number_format($totalClicks) ?></div>
      </div>
    </div>
  </div>

  <!-- CHARTS -->
  <div class="row g-4">
    <div class="col-lg-6"><canvas id="chartVisitors"></canvas></div>
    <div class="col-lg-6"><canvas id="chartInteractions"></canvas></div>
    <div class="col-lg-4">
    <div class="soft-card chart-card">
      <div class="chart-title">Aircraft Analytics</div>
      <canvas id="chartAircraft"></canvas>
    </div>
  </div>

  <!-- ENGINES -->
  <div class="col-lg-4">
    <div class="soft-card chart-card">
      <div class="chart-title">Engines Analytics</div>
      <canvas id="chartEngines"></canvas>
    </div>
  </div>

  <!-- PARTS -->
  <div class="col-lg-4">
    <div class="soft-card chart-card">
      <div class="chart-title">Parts Analytics</div>
      <canvas id="chartParts"></canvas>
    </div>
  </div>
  </div>
  <div class="row g-4 mt-4">

  <!-- TOP AIRCRAFT -->
  <div class="col-lg-4">
    <div class="soft-card">
      <h5 class="fw-bold mb-3">Top Aircraft</h5>
      <div id="topAircraft"></div>
    </div>
  </div>

  <!-- TOP ENGINES -->
  <div class="col-lg-4">
    <div class="soft-card">
      <h5 class="fw-bold mb-3">Top Engines</h5>
      <div id="topEngines"></div>
    </div>
  </div>

  <!-- TOP PARTS -->
  <div class="col-lg-4">
    <div class="soft-card">
      <h5 class="fw-bold mb-3">Top Parts</h5>
      <div id="topParts"></div>
    </div>
  </div>

</div>

</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const range = new URLSearchParams(window.location.search).get('range') || '7days';
let charts = {};

/* ===== COLOR CONSTANTS ===== */
const COLORS = {
  views: 'rgba(59, 130, 246, 0.9)',      // Blue
  viewsBg: 'rgba(59, 130, 246, 0.25)',

  clicks: 'rgba(16, 185, 129, 0.9)',     // Green
  clicksBg: 'rgba(16, 185, 129, 0.25)',

  calls: '#22c55e',
  emails: '#6366f1',
  website: '#f59e0b'
};

/* ===== LOAD ANALYTICS ===== */
async function loadAnalyticsGraphs() {
  const res = await fetch(`ga-report.php?range=${range}`);
  const data = await res.json();

  /* ================= VISITORS (VIEWS vs CLICKS) ================= */
  charts.visitors?.destroy();
  charts.visitors = new Chart(chartVisitors, {
    type: 'line',
    data: {
      labels: data.visitors.labels,
      datasets: [
        {
          label: 'Views',
          data: data.visitors.views,
          borderColor: COLORS.views,
          backgroundColor: COLORS.viewsBg,
          fill: true,
          tension: 0.4
        },
        {
          label: 'Clicks',
          data: data.visitors.clicks,
          borderColor: COLORS.clicks,
          backgroundColor: COLORS.clicksBg,
          fill: true,
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  /* ================= INTERACTIONS BAR ================= */
  charts.interactions?.destroy();
  charts.interactions = new Chart(chartInteractions, {
    type: 'bar',
    data: {
      labels: ['Calls', 'Emails', 'Website'],
      datasets: [{
        label: 'Interactions',
        data: data.interactions,
        backgroundColor: [
          COLORS.calls,
          COLORS.emails,
          COLORS.website
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      }
    }
  });

  /* ================= DOUGHNUT HELPER ================= */
  function renderDoughnut(chartRef, canvas, values) {
    chartRef?.destroy();
    return new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: ['Views', 'Clicks'],
        datasets: [{
          data: values,
          backgroundColor: [
            COLORS.views,
            COLORS.clicks
          ],
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }

  /* ================= DOUGHNUT CHARTS ================= */
 charts.aircraft?.destroy();
charts.aircraft = new Chart(chartAircraft, {
  type: 'doughnut',
  data: {
    labels: data.aircraft.labels,
    datasets: [{
      data: data.aircraft.datasets[0].data,
      backgroundColor: [COLORS.views, COLORS.clicks]
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

charts.engines?.destroy();
charts.engines = new Chart(chartEngines, {
  type: 'doughnut',
  data: {
    labels: data.engines.labels,
    datasets: [{
      data: data.engines.datasets[0].data,
      backgroundColor: [COLORS.views, COLORS.clicks]
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

charts.parts?.destroy();
charts.parts = new Chart(chartParts, {
  type: 'doughnut',
  data: {
    labels: data.parts.labels,
    datasets: [{
      data: data.parts.datasets[0].data,
      backgroundColor: [COLORS.views, COLORS.clicks]
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

}

/* ===== INIT ===== */
loadAnalyticsGraphs();
</script>


<script>
function renderTopItems(containerId, items, label) {
  const el = document.getElementById(containerId);
  if (!el) return;

  if (!Array.isArray(items) || items.length === 0) {
    el.innerHTML = '<div class="text-muted">No data</div>';
    return;
  }

  el.innerHTML = items.map((item, index) => `
    <a href="${item.slug}" class="text-decoration-none text-dark" target="_blank" >
      <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded hover-shadow">
        <div>
          <div class="fw-bold">${label}: ${item.name} (#${item.item_id})</div>
          <div class="small text-muted">
            👁 ${item.views ?? 0} Views &nbsp;|&nbsp;
            🖱 ${item.clicks ?? 0} Clicks &nbsp;|&nbsp;
            ✉️ ${item.emails ?? 0} Emails
          </div>
        </div>
        <div class="d-flex align-items-center">
          <span class="badge bg-primary me-2">${index + 1}</span>
         <span class="text-primary fs-5 bi bi-box-arrow-up-right"></span>
        </div>
      </div>
    </a>
  `).join('');
}

/* ===== LOAD DATA ===== */
async function loadAnalyticsGraphs() {
  const range = new URLSearchParams(window.location.search).get('range') || '7days';
  const res = await fetch(`ga-report.php?range=${range}`);
  const data = await res.json();

  renderTopItems('topAircraft', data.top_aircraft, 'Aircraft');
  renderTopItems('topEngines',  data.top_engines,  'Engine');
  renderTopItems('topParts',    data.top_parts,    'Part');
}

loadAnalyticsGraphs();

</script>



<?php include "footer.php"; ?>
</body>
</html>