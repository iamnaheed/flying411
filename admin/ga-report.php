<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require 'config.php';

$range = $_GET['range'] ?? '7days';
$days = 7;
$dateWhere = "created_at >= NOW() - INTERVAL 7 DAY";

if ($range === 'today') {
    $days = 1;
    $dateWhere = "created_at >= CURDATE()";
} elseif ($range === '30days') {
    $days = 30;
    $dateWhere = "created_at >= NOW() - INTERVAL 30 DAY";
}

$tables = [
    'aircraft_analytics',
    'engine_analytics',
    'parts_analytics'
];

/* ================= VISITORS TIMELINE ================= */
$labels = [];
$views  = [];
$clicks = [];

for ($i = $days - 1; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('M d', strtotime($day));

    $dayViews = 0;
    $dayClicks = 0;

    foreach ($tables as $t) {
        $dayViews += (int)$pdo->query("
            SELECT COUNT(*) FROM $t
            WHERE event_type = 'view'
            AND DATE(created_at) = '$day'
        ")->fetchColumn();

        $dayClicks += (int)$pdo->query("
            SELECT COUNT(*) FROM $t
            WHERE event_type != 'view'
            AND DATE(created_at) = '$day'
        ")->fetchColumn();
    }

    $views[]  = $dayViews;
    $clicks[] = $dayClicks;
}

/* ================= INTERACTIONS ================= */
$interactions = ['call'=>0,'email'=>0,'website'=>0];

foreach ($tables as $t) {
    $r = $pdo->query("
        SELECT
            SUM(event_type='call'),
            SUM(event_type='email'),
            SUM(event_type='website')
        FROM $t
        WHERE $dateWhere
    ")->fetch(PDO::FETCH_NUM);

    $interactions['call']    += (int)$r[0];
    $interactions['email']  += (int)$r[1];
    $interactions['website']+= (int)$r[2];
}

/* ================= CATEGORY DATA ================= */
function categoryData($pdo, $table, $dateWhere) {
    return [
        'labels' => ['Views','Clicks'],
        'datasets' => [[
            'data' => [
                (int)$pdo->query("SELECT COUNT(*) FROM $table WHERE event_type='view' AND $dateWhere")->fetchColumn(),
                (int)$pdo->query("SELECT COUNT(*) FROM $table WHERE event_type!='view' AND $dateWhere")->fetchColumn()
            ]
        ]]
    ];
}
function topItemsWithSlug($pdo, $analyticsTable, $idField, $mainTable, $nameField, $type) {
    $sql = "
        SELECT
            a.$idField AS item_id,
            m.$nameField AS name,
            " . ($type == 'aircraft' ? "CONCAT('https://flying411.com/', LOWER(REPLACE(m.$nameField,' ', '-')),'/',a.$idField)" :
                  ($type == 'engine' ? "CONCAT('https://flying411.com/engines/', LOWER(REPLACE(m.$nameField,' ', '-')),'/',a.$idField)" :
                  "CONCAT('https://flying411.com/parts/', LOWER(REPLACE(m.$nameField,' ', '-')),'/',a.$idField)")
                 ) . " AS slug,
            SUM(CASE WHEN a.event_type='view' THEN 1 ELSE 0 END) AS views,
            SUM(CASE WHEN a.event_type!='view' THEN 1 ELSE 0 END) AS clicks,
            SUM(CASE WHEN a.event_type='email' THEN 1 ELSE 0 END) AS emails
        FROM $analyticsTable a
        JOIN $mainTable m ON a.$idField = m.$idField
        WHERE a.$idField IS NOT NULL
        GROUP BY a.$idField, m.$nameField
        ORDER BY views DESC
        LIMIT 3
    ";

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Usage
$topAircraft = topItemsWithSlug($pdo, 'aircraft_analytics', 'aircraft_id', 'aircrafts', 'model', 'aircraft');
$topEngines  = topItemsWithSlug($pdo, 'engine_analytics',   'engine_id',   'engines',   'model', 'engine');
$topParts    = topItemsWithSlug($pdo, 'parts_analytics',    'part_id',     'parts',     'part_name', 'part');


echo json_encode([
    'visitors' => [
        'labels' => $labels,
        'views'  => $views,
        'clicks' => $clicks
    ],
    'interactions' => array_values($interactions),

    'aircraft' => categoryData($pdo,'aircraft_analytics',$dateWhere),
    'engines'  => categoryData($pdo,'engine_analytics',$dateWhere),
    'parts'    => categoryData($pdo,'parts_analytics',$dateWhere),

    /* ===== TOP 3 ===== */
    'top_aircraft' => $topAircraft,
    'top_engines'  => $topEngines,
    'top_parts'    => $topParts
]);



