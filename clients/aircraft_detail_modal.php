<?php
require_once "config.php";
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE aircraft_id = ?");
$stmt->execute([$id]);
$ac = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ac) exit("<p>Aircraft not found.</p>");
?>

<h3><?= htmlspecialchars($ac['model']) ?> (<?= htmlspecialchars($ac['year']) ?>)</h3>
<p><strong>Manufacturer:</strong> <?= htmlspecialchars($ac['manufacturer']) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($ac['category']) ?></p>
<p><strong>Type:</strong> <?= htmlspecialchars($ac['aircraft_type']) ?></p>
<p><strong>Location:</strong> <?= htmlspecialchars($ac['city'] . ', ' . $ac['country']) ?></p>
<p><strong>Price:</strong> $<?= number_format($ac['price']) ?></p>
<p><strong>Registration No:</strong> <?= htmlspecialchars($ac['registration_number']) ?></p>
<p><strong>Serial No:</strong> <?= htmlspecialchars($ac['serial_number']) ?></p>
<p><strong>Total Time:</strong> <?= number_format($ac['total_time_hours']) ?> hrs</p>
<p><strong>Engine 1:</strong> <?= htmlspecialchars($ac['engine1_status']) ?> (<?= htmlspecialchars($ac['engine1_hours']) ?> hrs)</p>
<p><strong>Engine 2:</strong> <?= htmlspecialchars($ac['engine2_status']) ?> (<?= htmlspecialchars($ac['engine2_hours']) ?> hrs)</p>
<p><strong>Prop 1:</strong> <?= htmlspecialchars($ac['prop1_status']) ?> (<?= htmlspecialchars($ac['prop1_hours']) ?> hrs)</p>
<p><strong>Prop 2:</strong> <?= htmlspecialchars($ac['prop2_status']) ?> (<?= htmlspecialchars($ac['prop2_hours']) ?> hrs)</p>
<p><strong>Features:</strong><br><?= nl2br(htmlspecialchars($ac['features'])) ?></p>
<p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($ac['description'])) ?></p>
<p><strong>Warranty:</strong> <?= htmlspecialchars($ac['warranty']) ?></p>
<p><strong>Status:</strong> <?= htmlspecialchars($ac['status']) ?></p>
