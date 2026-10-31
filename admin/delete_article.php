<?php
require 'config.php'; // Include database connection

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = intval($_GET['id']);

    try {
        // Fetch the article to delete image (if exists)
        $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $image = $stmt->fetchColumn(); // Get the image filename
        $stmt = null; // Close statement

        // Delete the article
        $delete_stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        if ($delete_stmt->execute([$article_id])) {
            // Delete image if exists
            if (!empty($image) && file_exists($image)) {
                unlink($image); // Remove the image from the server
            }
            header("Location: articles_list.php?msg=Article deleted successfully");
            exit;
        } else {
            header("Location: articles_list.php?error=Failed to delete article");
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: articles_list.php?error=Invalid article ID");
    exit;
}
?>
