<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'];
    $meta_title = $_POST['meta_title'];
    $meta_keywords = $_POST['meta_keywords'];
    $meta_description = $_POST['meta_description'];

    $stmt = $pdo->prepare("UPDATE articles SET title = ?, keywords = ?, meta_description = ? WHERE id = ?");
    $stmt->execute([$meta_title, $meta_keywords, $meta_description, $article_id]);

    echo "<script>alert('SEO Data Updated'); window.location.href='all_article_seo.php';</script>";
}
?>
