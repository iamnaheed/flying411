<?php 
// Start session and include necessary files
session_start();
include "config.php";

// Fetch all SEO meta data
$stmt = $pdo->query("SELECT * FROM seo_meta ORDER BY last_updated DESC");
$seo_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_seo'])) {
    $seo_id = (int)$_POST['seo_id'];

    // Prepare SQL statement to delete the SEO data
    $deleteStmt = $pdo->prepare("DELETE FROM seo_meta WHERE seo_id = ?");
    $deleteStmt->execute([$seo_id]);

    $_SESSION['success_message'] = "SEO data deleted successfully!";
    header("Location: seo_success.php"); // Redirect after deleting
    exit();
}

// Handle Edit Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_seo'])) {
    $seo_id = (int)$_POST['seo_id'];
    header("Location: edit-seo.php?seo_id=" . $seo_id); // Redirect to edit page with SEO ID
    exit();
}

include "header.php"; // Include header for the layout
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Manage SEO Pages</h3>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">SEO Pages List</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>SEO ID</th>
                                        <th>Page Name</th>
                                        <th>Meta Title</th>
                                        <th>Meta Description</th>
                                        <th>Meta Keywords</th>
                                        <th>SEO Status</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seo_data as $seo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($seo['seo_id']); ?></td>
                                            <td><?= htmlspecialchars($seo['page_name']); ?></td>
                                            <td><?= htmlspecialchars($seo['meta_title']); ?></td>
                                            <td><?= htmlspecialchars($seo['meta_description']); ?></td>
                                            <td><?= htmlspecialchars($seo['meta_keywords']); ?></td>
                                            <td><?= ucfirst($seo['seo_status']); ?></td>
                                            <td><?= htmlspecialchars($seo['last_updated']); ?></td>
                                            <td>
                                                <form method="POST" action="">
                                                    <!-- Edit Button -->
                                                    <input type="hidden" name="seo_id" value="<?= $seo['seo_id']; ?>">
                                                    <button type="submit" name="edit_seo" class="btn btn-warning btn-sm">Edit</button>
                                                </form>
                                                <form method="POST" action="" class="mt-2">
                                                    <!-- Delete Button -->
                                                    <input type="hidden" name="seo_id" value="<?= $seo['seo_id']; ?>">
                                                    <button type="submit" name="delete_seo" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<?php include "footer.php"; ?>