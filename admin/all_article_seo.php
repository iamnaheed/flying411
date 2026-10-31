<?php 
session_start();
include "config.php";

// Fetch All Articles with SEO-related data
$sql = "
    SELECT 
        id,
        title,
        keywords,
        meta_description,
        publish_date,
        created_at
    FROM articles
    ORDER BY id DESC
";
$stmt = $pdo->query($sql);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "header.php"; // your layout header
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-3">
      <div class="col-md-12">
        <h3 class="font-weight-bold">All Articles with SEO Info</h3>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title">Articles List</h4>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Keywords</th>
                <th>Meta Description</th>
                <th>Publish Date</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($articles)): ?>
                <?php foreach ($articles as $article): ?>
                  <tr>
                    <td><?= htmlspecialchars($article['id']) ?></td>
                    <td><?= htmlspecialchars($article['title']) ?></td>
                    <td><?= htmlspecialchars($article['keywords']) ?></td>
                    <td><?= htmlspecialchars($article['meta_description']) ?></td>
                    <td><?= htmlspecialchars($article['publish_date']) ?></td>
                    <td><?= htmlspecialchars($article['created_at']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">No articles found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>