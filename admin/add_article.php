<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php';

// ---------- Fetch Categories ----------
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// ---------- Fetch Authors & Fact Checkers ----------
$authors = $pdo->prepare("SELECT id, name FROM content_people WHERE role='writer' ORDER BY name");
$authors->execute();
$authors = $authors->fetchAll();

$fact_checkers = $pdo->prepare("SELECT id, name FROM content_people WHERE role='fact_checker' ORDER BY name");
$fact_checkers->execute();
$fact_checkers = $fact_checkers->fetchAll();

// ---------- Handle CKEditor Inline Image Upload (AJAX) ----------
if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(["error" => ["message" => "Invalid file type. Allowed: jpg, jpeg, png, gif, webp"]]);
        exit;
    }

    $newName = 'editor-' . time() . '.' . $ext;
    $uploadPath = __DIR__ . '/uploads/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode([
            "uploaded" => 1,
            "fileName" => $newName,
            "url" => "uploads/" . $newName
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => ["message" => "Failed to move uploaded file."]]);
    }
    exit;
}

// ---------- Handle Article Form Submit ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = $_POST['content'];
    $keywords = trim($_POST['tags']);
    $meta_description = trim($_POST['meta_description']);
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $author_id = !empty($_POST['author_id']) ? $_POST['author_id'] : null;
    $fact_checker_id = !empty($_POST['fact_checker_id']) ? $_POST['fact_checker_id'] : null;

    $publish_date = !empty($_POST['publish_date'])
        ? date('Y-m-d H:i:s', strtotime($_POST['publish_date']))
        : date('Y-m-d H:i:s');
    $created_at = date('Y-m-d H:i:s');

    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug));
    $slug = preg_replace('/-+/', '-', $slug);

    // Featured Image
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed_exts)) {
            $image_name = 'featured-' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/uploads/" . $image_name);
        } else {
            echo "<script>alert('Invalid featured image format.');</script>";
        }
    }

    // Insert Article
    $stmt = $pdo->prepare("INSERT INTO articles 
        (title, content, image, keywords, meta_description, slug, publish_date, created_at, category_id, author_id, fact_checker_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        $title, $content, $image_name, $keywords, $meta_description, $slug, $publish_date, $created_at,
        $category_id, $author_id, $fact_checker_id
    ]);

    if ($success) {
        echo "<script>alert('Article added successfully!'); window.location.href='articles_list.php';</script>";
    } else {
        echo "<script>alert('Something went wrong while saving the article.');</script>";
    }
}
?>



<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- CKEditor 5 (Free) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            height:250px;
        }
    </style>
</head>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold text-left">Add Article</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Slug</label>
        <input type="text" name="slug" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Category</label>
        <select name="category_id" class="form-control">
            <option value="">— Select Category —</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Author (Writer)</label>
        <select name="author_id" class="form-control">
            <option value="">— Select Author —</option>
            <?php foreach($authors as $a): ?>
                <option value="<?= $a['id']; ?>"><?= htmlspecialchars($a['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Fact Checker</label>
        <select name="fact_checker_id" class="form-control">
            <option value="">— Select Fact Checker —</option>
            <?php foreach($fact_checkers as $f): ?>
                <option value="<?= $f['id']; ?>"><?= htmlspecialchars($f['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Content</label>
        <textarea name="content" id="editor" class="form-control" rows="25"></textarea>
    </div>

    <div class="form-group">
        <label>Upload Featured Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="form-group">
        <label>Publish Date</label>
        <input type="datetime-local" name="publish_date" class="form-control">
    </div>

    <div class="form-group">
        <label for="tags">Tags</label>
        <input type="text" name="tags" class="form-control" placeholder="e.g. aviation, repair, news">
    </div>

    <div class="form-group">
        <label for="meta_description">Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="2"></textarea>
    </div>

    <button type="submit" name="submit" class="btn" style="background-color:#4747A1;color:white;">Add Article</button>
</form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        ckfinder: {
            uploadUrl: 'upload_article_image.php' // our upload handler (Step 2)
        }
    })
    .then(editor => {
        window.editorInstance = editor;
    })
    .catch(error => {
        console.error(error);
    });

document.querySelector("form").addEventListener("submit", function (e) {
    const content = editorInstance.getData().trim();
    if (content === '') {
        alert("Please enter content before submitting.");
        e.preventDefault();
    }
});
</script>

<?php require_once 'footer.php'; ?>
