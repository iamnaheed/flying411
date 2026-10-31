<?php 
session_start();
include "config.php";

// Get the SEO ID from the query string
if (isset($_GET['seo_id'])) {
    $seo_id = (int)$_GET['seo_id'];

    // Fetch the existing SEO data for the given ID
    $stmt = $pdo->prepare("SELECT * FROM seo_meta WHERE seo_id = ?");
    $stmt->execute([$seo_id]);
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no SEO data is found, redirect to the SEO page list
    if (!$seo) {
        header("Location: seo_success.php");
        exit();
    }
}

// Handle the form submission to update SEO data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_seo'])) {
    // Sanitize and collect form data
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $og_title = $_POST['og_title'];
    $og_description = $_POST['og_description'];
    $og_image = $_POST['og_image'];  // Assume image URL is passed
    $noindex = $_POST['noindex'] == '1' ? 1 : 0;
    $nofollow = $_POST['nofollow'] == '1' ? 1 : 0;
    $seo_status = $_POST['seo_status'];
    $meta_robots = $_POST['meta_robots'];

    // Update the SEO data in the database
    $updateStmt = $pdo->prepare("UPDATE seo_meta SET 
        meta_title = ?, meta_description = ?, meta_keywords = ?, og_title = ?, og_description = ?, og_image = ?
       , noindex = ?, 
        nofollow = ?, seo_status = ?, meta_robots = ? 
        WHERE seo_id = ?");
    
    $updateStmt->execute([$meta_title, $meta_description, $meta_keywords, $og_title, $og_description, $og_image, $noindex, 
                          $nofollow, $seo_status, $meta_robots, $seo_id]);

    $_SESSION['success_message'] = "SEO data updated successfully!";
    header("Location: seo_success.php");
    exit();
}

include "header.php"; // Include header for the layout
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit SEO Data</h3>
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
                        <form method="POST" action="">
                            <!-- Meta Title -->
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($seo['meta_title']); ?>" required>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-group">
                                <label>Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="4" required><?= htmlspecialchars($seo['meta_description']); ?></textarea>
                            </div>

                            <!-- Meta Keywords -->
                            <div class="form-group">
                                <label>Meta Keywords</label>
                                <textarea class="form-control" name="meta_keywords" rows="4" required><?= htmlspecialchars($seo['meta_keywords']); ?></textarea>
                            </div>

                            <!-- Open Graph Title -->
                            <div class="form-group">
                                <label>Open Graph Title</label>
                                <input type="text" class="form-control" name="og_title" value="<?= htmlspecialchars($seo['og_title']); ?>">
                            </div>

                            <!-- Open Graph Description -->
                            <div class="form-group">
                                <label>Open Graph Description</label>
                                <textarea class="form-control" name="og_description" rows="4"><?= htmlspecialchars($seo['og_description']); ?></textarea>
                            </div>

                            <!-- Open Graph Image -->
                            <div class="form-group">
                                <label>Open Graph Image</label>
                                <input type="text" class="form-control" name="og_image" value="<?= htmlspecialchars($seo['og_image']); ?>">
                            </div>

                            

                            <!-- Noindex -->
                            <div class="form-group">
                                <label>Noindex</label>
                                <select name="noindex" class="form-control">
                                    <option value="1" <?= ($seo['noindex'] == 1) ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?= ($seo['noindex'] == 0) ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <!-- Nofollow -->
                            <div class="form-group">
                                <label>Nofollow</label>
                                <select name="nofollow" class="form-control">
                                    <option value="1" <?= ($seo['nofollow'] == 1) ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?= ($seo['nofollow'] == 0) ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <!-- SEO Status -->
                            <div class="form-group">
                                <label>SEO Status</label>
                                <select name="seo_status" class="form-control">
                                    <option value="active" <?= ($seo['seo_status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?= ($seo['seo_status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="pending" <?= ($seo['seo_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>

                            <!-- Meta Robots -->
                            <div class="form-group">
                                <label>Meta Robots</label>
                                <input type="text" class="form-control" name="meta_robots" value="<?= htmlspecialchars($seo['meta_robots']); ?>">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" name="update_seo" class="btn btn-primary">Update SEO Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<?php include "footer.php"; ?> 
