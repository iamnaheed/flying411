<?php
session_start();
require_once "config.php";
require_once "header.php";

if (!isset($_SESSION['vendor_id'])) exit("Unauthorized access");

// ──► Get aircraft ID
$id = (int)($_GET['id'] ?? 0);
if (!$id) exit("Invalid ID");

// ──► Fetch main aircraft
$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE aircraft_id = ?");
$stmt->execute([$id]);
$ac = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ac) exit("Aircraft not found");

// ──► Model statistics
function fetchStats(PDO $pdo, string $model): array {
    $sql = "SELECT
              COUNT(*) AS total_listings,
              AVG(price) AS avg_price,
              MIN(price) AS min_price,
              MAX(price) AS max_price,
              AVG(year) AS avg_year,
              AVG(total_time_hours) AS avg_hours,
              AVG(engine1_hours) AS avg_engine1,
              AVG(engine2_hours) AS avg_engine2
            FROM aircrafts
            WHERE model = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$model]);
    return $q->fetch(PDO::FETCH_ASSOC) ?: [];
}
$stats = fetchStats($pdo, $ac['model']);

// ──► Similar aircrafts
$similarSQL = "SELECT * FROM aircrafts 
               WHERE (model = :model OR model LIKE :similar)
                 AND aircraft_id != :id
               ORDER BY ABS(price - :price) ASC
               LIMIT 8";
$sim = $pdo->prepare($similarSQL);
$sim->execute([
    ':model'   => $ac['model'],
    ':similar' => '%' . substr($ac['model'], 0, 6) . '%',
    ':id'      => $ac['aircraft_id'],
    ':price'   => $ac['price']
]);
$similar_aircrafts = $sim->fetchAll(PDO::FETCH_ASSOC);

// ──► Metrics
$metrics = [
  'price' => ['label' => 'Price (USD)', 'value' => $ac['price'], 'avg' => $stats['avg_price']],
  'year' => ['label' => 'Year', 'value' => $ac['year'], 'avg' => $stats['avg_year']],
  'total_time_hours' => ['label' => 'Total Hours', 'value' => $ac['total_time_hours'], 'avg' => $stats['avg_hours']],
  'engine1_hours' => ['label' => 'Engine 1 Hours', 'value' => $ac['engine1_hours'], 'avg' => $stats['avg_engine1']],
  'engine2_hours' => ['label' => 'Engine 2 Hours', 'value' => $ac['engine2_hours'], 'avg' => $stats['avg_engine2']]
];

// ──► Helper: Percent difference
function percentDiff($value, $avg) {
    if (!$avg || !$value) return 0;
    return round((($value - $avg) / $avg) * 100, 1);
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="aircraft-header">
      <div class="header-left">
        <h2><?= htmlspecialchars($ac['model']) ?> <small>(<?= htmlspecialchars($ac['manufacturer']) ?>)</small></h2>
        <p class="muted"><?= htmlspecialchars($ac['category']) ?> • <?= htmlspecialchars($ac['aircraft_type']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($ac['city']) ?>, <?= htmlspecialchars($ac['state']) ?>, <?= htmlspecialchars($ac['country']) ?></p>
        <p><strong>Price:</strong> $<?= number_format($ac['price']) ?> <span class="price-label">(<?= htmlspecialchars($ac['price_label']) ?>)</span></p>
      </div>
      <div class="header-right">
        <?php if (!empty($ac['thumbnail'])): ?>
          <img src="<?= htmlspecialchars($ac['thumbnail']) ?>" alt="<?= htmlspecialchars($ac['model']) ?>">
        <?php endif; ?>
      </div>
    </div>

    <hr>

    <div class="kpi-container">
      <?php foreach ($metrics as $key => $m): 
        $diff = percentDiff($m['value'], $m['avg']);
        $color = $diff < 0 ? '#00b894' : ($diff > 0 ? '#d63031' : '#636e72');
      ?>
        <div class="kpi-block">
          <h4><?= $m['label'] ?></h4>
          <p class="kpi-value">$<?= number_format($m['value']) ?></p>
          <p class="avg">Avg: <?= number_format($m['avg']) ?></p>
          <p class="diff" style="color:<?= $color ?>"> 
            <?= $diff == 0 ? "Equal to Average" : ($diff < 0 ? abs($diff) . "% below avg" : "+$diff% above avg") ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>

    <hr>

    <div class="info-section">
      <h3>Aircraft Details</h3>
      <div class="info-grid">
        <div><strong>Registration #:</strong> <?= htmlspecialchars($ac['registration_number']) ?></div>
        <div><strong>Serial #:</strong> <?= htmlspecialchars($ac['serial_number']) ?></div>
        <div><strong>Status:</strong> <?= htmlspecialchars($ac['status']) ?></div>
        <div><strong>Warranty:</strong> <?= htmlspecialchars($ac['warranty']) ?></div>
        <div><strong>Engine 1:</strong> <?= htmlspecialchars($ac['engine1_status']) ?> (<?= number_format($ac['engine1_hours']) ?> hrs)</div>
        <div><strong>Engine 2:</strong> <?= htmlspecialchars($ac['engine2_status']) ?> (<?= number_format($ac['engine2_hours']) ?> hrs)</div>
        <div><strong>Prop 1:</strong> <?= htmlspecialchars($ac['prop1_status']) ?> (<?= number_format($ac['prop1_hours']) ?> hrs)</div>
        <div><strong>Prop 2:</strong> <?= htmlspecialchars($ac['prop2_status']) ?> (<?= number_format($ac['prop2_hours']) ?> hrs)</div>
      </div>
    </div>

    <hr>

    <?php if ($similar_aircrafts): ?>
  <h3>Similar Aircrafts</h3>
  <div class="similar-grid">
    <?php foreach ($similar_aircrafts as $s): ?>
      <div class="similar-card" style="padding: 1rem; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h4><?= htmlspecialchars($s['model']) ?> <small>(<?= htmlspecialchars($s['manufacturer']) ?>)</small></h4>
        <div class="info-grid">
          <div><strong>Registration #:</strong> <?= htmlspecialchars($s['registration_number']) ?></div>
          <div><strong>Serial #:</strong> <?= htmlspecialchars($s['serial_number']) ?></div>
          <div><strong>Status:</strong> <?= htmlspecialchars($s['status']) ?></div>
          <div><strong>Warranty:</strong> <?= htmlspecialchars($s['warranty']) ?></div>
          <div><strong>Price:</strong> $<?= number_format($s['price']) ?> (<?= htmlspecialchars($s['price_label']) ?>)</div>
          <div><strong>Year:</strong> <?= htmlspecialchars($s['year']) ?></div>
          <div><strong>Total Hours:</strong> <?= number_format($s['total_time_hours']) ?></div>
          <div><strong>Engine 1:</strong> <?= htmlspecialchars($s['engine1_status']) ?> (<?= number_format($s['engine1_hours']) ?> hrs)</div>
          <div><strong>Engine 2:</strong> <?= htmlspecialchars($s['engine2_status']) ?> (<?= number_format($s['engine2_hours']) ?> hrs)</div>
          <div><strong>Prop 1:</strong> <?= htmlspecialchars($s['prop1_status']) ?> (<?= number_format($s['prop1_hours']) ?> hrs)</div>
          <div><strong>Prop 2:</strong> <?= htmlspecialchars($s['prop2_status']) ?> (<?= number_format($s['prop2_hours']) ?> hrs)</div>
          <div><strong>Category:</strong> <?= htmlspecialchars($s['category']) ?></div>
          <div><strong>Aircraft Type:</strong> <?= htmlspecialchars($s['aircraft_type']) ?></div>
          <div><strong>Location:</strong> <?= htmlspecialchars($s['city']) ?>, <?= htmlspecialchars($s['state']) ?>, <?= htmlspecialchars($s['country']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p>No similar aircrafts found.</p>
<?php endif; ?>

  </div>
</div>

<?php include "footer.php"; ?>

<!-- ✅ CSS Styling -->
<style>
.main-panel { background: #f9fafc; }
.content-wrapper { padding: 2rem; }
.aircraft-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 1.5rem;
}
.header-left h2 { margin-bottom: .3rem; }
.header-left small { font-size: .8rem; color: #888; }
.header-right img { max-width: 300px; border-radius: 10px; object-fit: cover; }
.muted { color: #777; }

.kpi-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  margin: 2rem 0;
}
.kpi-block {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  padding: 1rem;
  text-align: center;
}
.kpi-block h4 { margin-bottom: .5rem; color: #555; }
.kpi-value { font-size: 1.4rem; font-weight: 600; color: #222; }
.avg { font-size: .9rem; color: #666; }
.diff { font-size: .9rem; margin-top: .3rem; }

.info-section { margin-top: 2rem; }
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: .8rem;
  margin-top: 1rem;
}
.similar-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-top: 1.5rem;
}
.similar-card {
  border-radius: 10px;
  background: #fff;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: transform .2s;
}
.similar-card:hover { transform: translateY(-5px); }
.similar-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
}
.similar-card .details {
  padding: .8rem 1rem;
  text-align: center;
}
.btn-view {
  display: inline-block;
  margin-top: .4rem;
  padding: .4rem .8rem;
  background: #007bff;
  color: #fff;
  border-radius: .4rem;
  text-decoration: none;
}
.btn-view:hover { background: #0056b3; }
.price-label { color: #777; font-size: .85rem; margin-left: .5rem; }
</style>
