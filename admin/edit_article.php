<?php
session_start();
require_once 'config.php';
require_once 'header.php';

// ---------- Validate Article ID ----------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid article ID!'); window.location.href='articles_list.php';</script>";
    exit;
}

$id = (int)$_GET['id'];

// ---------- Fetch Article ----------
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "<script>alert('Article not found!'); window.location.href='articles_list.php';</script>";
    exit;
}

// ---------- Fetch Categories ----------
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- Fetch Content Authors / Fact Checkers ----------
$authorStmt = $pdo->query("SELECT id, name, role FROM content_people ORDER BY name ASC");
$authors = $authorStmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- Handle Form Submission ----------
if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = $_POST['content'];
    $keywords = trim($_POST['tags']);
    $meta_description = trim($_POST['meta_description']);
    $publish_date = $_POST['publish_date'] ?? date('Y-m-d H:i:s');
    $category_id = $_POST['category_id'] ?? null;
    $author_id = $_POST['author_id'] ?? null;

    // Clean slug
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug));
    $slug = preg_replace('/-+/', '-', $slug);

    // ---------- Handle Featured Image Upload ----------
    $image_name = $article['image']; // keep old image
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $image_name = 'article-' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/uploads/" . $image_name);
        } else {
            echo "<script>alert('Invalid image format!');</script>";
        }
    }

    // ---------- Update Database ----------
    $updateStmt = $pdo->prepare("
        UPDATE articles SET 
            title = ?, 
            content = ?, 
            image = ?, 
            keywords = ?, 
            meta_description = ?, 
            slug = ?, 
            publish_date = ?, 
            category_id = ?, 
            author_id = ? 
        WHERE id = ?
    ");
    $updateStmt->execute([
        $title, $content, $image_name, $keywords, $meta_description, $slug, $publish_date, $category_id, $author_id, $id
    ]);

    echo "<script>alert('Article updated successfully!'); window.location.href='articles_list.php';</script>";
    exit;
}
?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
</head>

<div class="container content-wrapper" style="max-width:1200px;">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h3 class="mt-4 mb-4">Edit Article</h3>

            <form method="post" enctype="multipart/form-data">
                <!-- Title -->
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" class="form-control" required>
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($article['slug']) ?>" class="form-control" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Author -->
                <div class="form-group">
                    <label>Author / Fact Checker</label>
                    <select name="author_id" class="form-control" required>
                        <option value="">-- Select Author --</option>
                        <?php foreach ($authors as $auth): ?>
                            <option value="<?= $auth['id'] ?>" <?= ($article['author_id'] == $auth['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($auth['name']) ?> (<?= ucfirst($auth['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" id="editor" class="form-control" rows="10"><?= htmlspecialchars($article['content']) ?></textarea>
                </div>

                <!-- Featured Image -->
                <div class="form-group">
                    <label>Featured Image</label>
                    <?php if ($article['image']): ?>
                        <div class="mb-2">
                            <img src="uploads/<?= htmlspecialchars($article['image']) ?>" width="150">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                </div>

                <!-- Publish Date -->
                <div class="form-group">
                    <label>Publish Date</label>
                    <input type="datetime-local" name="publish_date" class="form-control" 
                        value="<?= date('Y-m-d\TH:i', strtotime($article['publish_date'])) ?>">
                </div>

                <!-- Tags -->
                <div class="form-group">
                    <label>Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($article['keywords']) ?>">
                </div>

                <!-- Meta Description -->
                <div class="form-group">
                    <label>Meta Description (SEO)</label>
                    <textarea name="meta_description" class="form-control" rows="2"><?= htmlspecialchars($article['meta_description']) ?></textarea>
                </div>

                <!-- Buttons -->
                <button type="submit" name="submit" class="btn btn-primary">Update Article</button>
                <a href="articles_list.php" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    let editorInstance;
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => { editorInstance = editor; })
        .catch(error => { console.error(error); });

    document.querySelector("form").addEventListener("submit", function (e) {
        const content = editorInstance.getData().trim();
        if (content === '') {
            alert("Please enter content before submitting.");
            e.preventDefault();
        }
    });
</script>

<?php require_once 'footer.php'; ?>
