<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $publish_date = !empty($_POST['publish_date']) ? $_POST['publish_date'] : NULL;
    $imagePath = NULL;

    // Slug generation (SEO-friendly URL)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Insert Article into Database
    try {
        $sql = "INSERT INTO articles (title, content, image, slug, publish_date) VALUES (:title, :content, :image, :slug, :publish_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':image' => $imagePath,
            ':slug' => $slug,
            ':publish_date' => $publish_date
        ]);

        $_SESSION['message'] = "Article added successfully!";
        header("Location: add_article.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
