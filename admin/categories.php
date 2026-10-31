<?php
session_start();
require_once 'config.php';

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

/* ===================== HELPERS ===================== */
function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* ===================== ADD / UPDATE ===================== */
$edit_id = null;
$name = '';
$parent_id = null;

if (isset($_POST['save_category'])) {
    $name = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $slug = slugify($name);

    if (!empty($_POST['edit_id'])) {
        // UPDATE
        $stmt = $pdo->prepare(
            "UPDATE categories SET name = ?, slug = ?, parent_id = ? WHERE id = ?"
        );
        $stmt->execute([$name, $slug, $parent_id, (int)$_POST['edit_id']]);
    } else {
        // INSERT
        $stmt = $pdo->prepare(
            "INSERT INTO categories (name, slug, parent_id) VALUES (?,?,?)"
        );
        $stmt->execute([$name, $slug, $parent_id]);
    }

    header("Location: categories.php");
    exit;
}

/* ===================== EDIT ===================== */
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $row = $stmt->fetch();

    $name = $row['name'];
    $parent_id = $row['parent_id'];
}

/* ===================== DELETE ===================== */
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // check usage
    $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
    $check->execute([$delete_id]);
    $used = $check->fetchColumn();

    // show confirm first
    if ($used > 0 && !isset($_GET['confirm'])) {
        $_SESSION['confirm_delete'] = [
            'id' => $delete_id,
            'count' => $used
        ];
        header("Location: categories.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // ✅ FIRST remove category from articles
        $upd = $pdo->prepare("UPDATE articles SET category_id = NULL WHERE category_id = ?");
        $upd->execute([$delete_id]);

        // ✅ THEN delete category
        $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $del->execute([$delete_id]);

        $pdo->commit();

        $_SESSION['success'] = "Category deleted successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Delete failed: " . $e->getMessage();
    }

    header("Location: categories.php");
    exit;
}




/* ===================== PAGINATION ===================== */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$pages = ceil($total / $limit);

/* ===================== FETCH DATA ===================== */
$stmt = $pdo->prepare("
    SELECT c.id, c.name, c.slug, p.name AS parent
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    ORDER BY c.parent_id IS NULL DESC, c.name
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();

$parents = $pdo->query(
    "SELECT id, name FROM categories WHERE parent_id IS NULL"
)->fetchAll();
require_once 'header.php';
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
                <h3 class="font-weight-bold text-left">Manage Categories</h3>
            </div>
        </div>

        <div class="row">

            <!-- LEFT: ADD / EDIT FORM -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5><?= $edit_id ? 'Edit Category' : 'Add Category'; ?></h5>

                        <form method="post">
                            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_id); ?>">

                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= htmlspecialchars($name); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Parent Category</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">— Main Category —</option>
                                    <?php foreach ($parents as $p): ?>
                                        <option value="<?= $p['id']; ?>"
                                            <?= ($parent_id == $p['id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($p['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" name="save_category" class="btn btn-primary">
                                <?= $edit_id ? 'Update' : 'Add'; ?> Category
                            </button>
                        </form>
                        
                    </div>
                </div>
            </div>

            <!-- RIGHT: LIST -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    
                    <div class="card-body">

                        <h5>Categories List</h5>

<?php if (!empty($_SESSION['confirm_delete'])): 
    $cd = $_SESSION['confirm_delete'];
?>
<div class="alert alert-warning">
    This category is used in <strong><?= $cd['count'] ?></strong> articles.<br>
    Deleting it will remove the category from those articles.<br><br>

    <a href="?delete=<?= $cd['id'] ?>&confirm=1"
       class="btn btn-danger btn-sm">Yes, Delete</a>

    <a href="categories.php"
       class="btn btn-secondary btn-sm">Cancel</a>
</div>
<?php unset($_SESSION['confirm_delete']); endif; ?>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Slug</th>
                                <th width="120">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['name']); ?></td>
                                    <td><?= $cat['parent'] ?: '—'; ?></td>
                                    <td><?= htmlspecialchars($cat['slug']); ?></td>
                                    <td>
                                        <a href="?edit=<?= $cat['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="?delete=<?= $cat['id']; ?>" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Delete this category?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- PAGINATION -->
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $i; ?>">
                                            <?= $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<?php require_once 'footer.php'; ?>
