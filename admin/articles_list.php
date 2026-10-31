<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php';

// Get search filters
$filter_title = isset($_GET['title']) ? trim($_GET['title']) : '';
$filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;


// SQL query with filters
$sql = "SELECT * FROM articles WHERE 1=1";
$params = [];

if (!empty($filter_title)) {
    $sql .= " AND title LIKE :title";
    $params['title'] = "%$filter_title%";
}
if (!empty($filter_date)) {
    $sql .= " AND DATE(publish_date) = :date";
    $params['date'] = $filter_date;
}

$sql .= " ORDER BY publish_date DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total records count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE 1=1" . (!empty($filter_title) ? " AND title LIKE :title" : "") . (!empty($filter_date) ? " AND DATE(publish_date) = :date" : ""));
$countStmt->execute($params);
$total_articles = $countStmt->fetchColumn();
$total_pages = ceil($total_articles / $limit);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <h3>Manage Articles</h3>

        <!-- Filters -->
        <form method="GET" class="mb-3">
            <input type="text" name="title" placeholder="Article Title" value="<?= htmlspecialchars($filter_title) ?>" class="form-control mb-2">
            <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Responsive Table Wrapper -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Content</th>
                        <th>Meta Description</th>
                        <th>Keywords</th>
                        <th>Publish Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)) : ?>
                        <tr>
                            <td colspan="7" class="text-center">No articles found.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($articles as $article) : ?>
                            <tr>
                               
                                <td>
                                    <?php if (!empty($article['image'])) : ?>
                                        <img src="uploads/<?= htmlspecialchars($article['image']); ?>" alt="Image" style="width:130px;height:90px; border-radius:0px;">
                                    <?php else : ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                 <td><?= htmlspecialchars($article['title']); ?></td>
                                 <td><?= htmlspecialchars($article['slug']); ?></td>
                                <td><?= substr(htmlspecialchars($article['content']), 0, 50) ?>...</td>
                                <td><?= htmlspecialchars($article['meta_description']); ?></td>
                                <td><?= htmlspecialchars($article['keywords']); ?></td>
                                <td><?= date("d M Y, H:i", strtotime($article['publish_date'])); ?></td>
                                <td>
                                     <a href="edit_article.php?id=<?= $article['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <!--<a href="edit_article.php?id=<?= $article['id']; ?>" class="btn btn-warning btn-sm">Edit</a>-->
                                    <a href="delete_article.php?id=<?= $article['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- End of Table Responsive -->

        <!-- Pagination -->
        <nav>
           <ul class="pagination" style="margin-top:50px; text-align:center;">

    <!-- Previous -->
    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?title=<?= urlencode($filter_title) ?>&date=<?= urlencode($filter_date) ?>&page=<?= $page - 1 ?>">Previous</a>
    </li>

    <?php
    $adjacents = 2; // how many pages to show on each side of current
    $start = max(2, $page - $adjacents);
    $end = min($total_pages - 1, $page + $adjacents);
    ?>

    <!-- Page 1 -->
    <li class="page-item <?= ($page == 1) ? 'active' : '' ?>">
        <a class="page-link" href="?title=<?= urlencode($filter_title) ?>&date=<?= urlencode($filter_date) ?>&page=1">1</a>
    </li>

    <!-- Left dots -->
    <?php if ($start > 2): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
    <?php endif; ?>

    <!-- Middle pages -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?title=<?= urlencode($filter_title) ?>&date=<?= urlencode($filter_date) ?>&page=<?= $i ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>

    <!-- Right dots -->
    <?php if ($end < $total_pages - 1): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
    <?php endif; ?>

    <!-- Last page -->
    <?php if ($total_pages > 1): ?>
        <li class="page-item <?= ($page == $total_pages) ? 'active' : '' ?>">
            <a class="page-link" href="?title=<?= urlencode($filter_title) ?>&date=<?= urlencode($filter_date) ?>&page=<?= $total_pages ?>"><?= $total_pages ?></a>
        </li>
    <?php endif; ?>

    <!-- Next -->
    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?title=<?= urlencode($filter_title) ?>&date=<?= urlencode($filter_date) ?>&page=<?= $page + 1 ?>">Next</a>
    </li>

</ul>


        </nav>
    </div>
</div>


<?php require_once 'footer.php'; ?>
