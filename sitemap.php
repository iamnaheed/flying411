<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO(
    "mysql:host=localhost;dbname=u682556162_flying411;charset=utf8mb4",
    "u682556162_flying411",
    "Flying@$2&411",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$base_url = "https://flying411.com";

// Directory to save sitemaps
$dir = __DIR__;

// =====================
// Helper to generate sitemap XML
// =====================
function generate_sitemap($urls, $file_path) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
    $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    foreach ($urls as $url) {
        $urlNode = $xml->addChild('url');
        $urlNode->addChild('loc', htmlspecialchars($url['loc'], ENT_XML1));
        $urlNode->addChild('lastmod', $url['lastmod']);
        $urlNode->addChild('changefreq', $url['changefreq']);
        $urlNode->addChild('priority', $url['priority']);
    }

    $xml->asXML($file_path);
}

// =====================
// 1. Static pages
// =====================
$static_pages = [
    '/', '/parts', '/engines', '/services', '/aircraft', '/contacts', 
    '/login', '/register', '/blogs', '/privacy-policy'
];

$static_urls = [];
foreach ($static_pages as $page) {
    $static_urls[] = [
        'loc' => $base_url . $page,
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.8'
    ];
}
generate_sitemap($static_urls, "$dir/sitemap-static.xml");

// =====================
// 2. Aircrafts
// =====================
$aircraft_urls = [];
foreach ($pdo->query("SELECT aircraft_id, model FROM aircrafts WHERE deleted_at IS NULL") as $row) {
    $aircraft_urls[] = [
        'loc' => "$base_url/". $row['model'] ."/". $row['aircraft_id'],
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.7'
    ];
}
generate_sitemap($aircraft_urls, "$dir/sitemap-aircrafts.xml");

// =====================
// 3. Parts
// =====================


$limit = 20000; // URLs per sitemap
$offset = 0;
$fileIndex = 1;
$total = $pdo->query("
    SELECT COUNT(*) 
    FROM parts_master 
    WHERE status = 1
")->fetchColumn();


while ($offset < $total) {

    $infoparts_urls = [];

    $sql = "
        SELECT part_number, part_name, updated_at
        FROM parts_master
        WHERE status = 1
        ORDER BY id ASC
        LIMIT $limit OFFSET $offset
    ";

    foreach ($pdo->query($sql) as $row) {

        // SEO slug
        $slug = strtolower(trim($row['part_name']));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $infoparts_urls[] = [
            'loc' => $base_url . "/parts/" . urlencode($row['part_number']) . "/" . $slug,
            'lastmod' => date('Y-m-d', strtotime($row['updated_at'] ?? 'now')),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
    }

    // Save sitemap file
    $filename = "$dir/sitemap-infoparts-$fileIndex.xml";
    generate_sitemap($infoparts_urls, $filename);

    echo "✅ Generated: sitemap-infoparts-$fileIndex.xml<br>";

    $offset += $limit;
    $fileIndex++;
}


$parts_urls = [];
foreach ($pdo->query("SELECT part_id, part_name FROM parts") as $row) {
    $slug = strtolower(trim($row['part_name']));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim($slug, '-');

    $parts_urls[] = [
        'loc' => "$base_url/parts/" . urlencode($slug) . "/" . $row['part_id'],
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.7'
    ];
}
generate_sitemap($parts_urls, "$dir/sitemap-parts.xml");

// =====================
// 4. Engines
// =====================
$engines_urls = [];
foreach ($pdo->query("SELECT engine_id, model FROM engines WHERE deleted_at IS NULL") as $row) {
    $engines_urls[] = [
        'loc' => "$base_url/engines/" . urlencode($row['model']) . "/" . $row['engine_id'],
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.7'
    ];
}
generate_sitemap($engines_urls, "$dir/sitemap-engines.xml");

// =====================
// 5. Blogs
// =====================
$blogs_urls = [];
foreach ($pdo->query("SELECT id, slug, publish_date FROM articles WHERE publish_date <= CURDATE()") as $row) {
    $blogs_urls[] = [
        'loc' => "$base_url/blog/" . urlencode($row['slug']) . "/" . $row['id'],
        'lastmod' => date('Y-m-d', strtotime($row['publish_date'])),
        'changefreq' => 'monthly',
        'priority' => '0.8'
    ];
}
generate_sitemap($blogs_urls, "$dir/sitemap-blogs.xml");

// =====================
// 6. Services
// =====================
$services_urls = [];
foreach ($pdo->query("SELECT id, business_name FROM services") as $row) {
    $services_urls[] = [
        'loc' => "$base_url/services/" . urlencode($row['business_name']) . "/" . $row['id'],
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.7'
    ];
}
generate_sitemap($services_urls, "$dir/sitemap-services.xml");

// =====================
// 7. Master Sitemap (index)
// =====================

$master_urls = [
    "$base_url/sitemap-static.xml",
    "$base_url/sitemap-aircrafts.xml",
    "$base_url/sitemap-parts.xml",
    "$base_url/sitemap-engines.xml",
    "$base_url/sitemap-blogs.xml",
    "$base_url/sitemap-services.xml",
];

// =====================
// Auto include ALL infoparts sitemap files
// sitemap-infoparts-1.xml, 2, 3, ...
// =====================
$infoparts_files = glob($dir . "/sitemap-infoparts-*.xml");

// Sort properly (1,2,3,10 not 1,10,2)
if ($infoparts_files !== false) {
    natsort($infoparts_files);

    foreach ($infoparts_files as $file) {
        $master_urls[] = $base_url . "/" . basename($file);
    }
}

// =====================
// Create sitemap index XML
// =====================
$xml = new SimpleXMLElement(
    '<?xml version="1.0" encoding="UTF-8"?><sitemapindex/>'
);
$xml->addAttribute(
    'xmlns',
    'http://www.sitemaps.org/schemas/sitemap/0.9'
);

foreach ($master_urls as $url) {
    $sitemap = $xml->addChild('sitemap');
    $sitemap->addChild('loc', htmlspecialchars($url, ENT_XML1));
    $sitemap->addChild('lastmod', date('Y-m-d'));
}

// =====================
// Save master sitemap
// =====================
$xml->asXML($dir . "/sitemap.xml");

echo "✅ All sitemaps generated successfully!";

?>