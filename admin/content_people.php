<?php
session_start();
require_once 'config.php';

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

/* ===================== HELPERS ===================== */
function sanitize($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/* ===================== ADD / UPDATE ===================== */
$edit_id = null;
$name = '';
$qualification = '';
$role = '';
$bio = '';
$profile_image = '';

if (isset($_POST['save_person'])) {
    $name = trim($_POST['name']);
    $qualification = trim($_POST['qualification']);
    $role = $_POST['role'];
    $bio = trim($_POST['bio']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], __DIR__ . "/uploads/" . $profile_image);
    }

    if (!empty($_POST['edit_id'])) {
        // UPDATE
        if ($profile_image) {
            $stmt = $pdo->prepare("UPDATE content_people SET name=?, qualification=?, role=?, bio=?, profile_image=? WHERE id=?");
            $stmt->execute([$name, $qualification, $role, $bio, $profile_image, (int)$_POST['edit_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE content_people SET name=?, qualification=?, role=?, bio=? WHERE id=?");
            $stmt->execute([$name, $qualification, $role, $bio, (int)$_POST['edit_id']]);
        }
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO content_people (name, qualification, role, bio, profile_image) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $qualification, $role, $bio, $profile_image]);
    }

    header("Location: content_people.php");
    exit;
}

/* ===================== EDIT ===================== */
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM content_people WHERE id=?");
    $stmt->execute([$edit_id]);
    $person = $stmt->fetch();

    $name = $person['name'];
    $qualification = $person['qualification'];
    $role = $person['role'];
    $bio = $person['bio'];
    $profile_image = $person['profile_image'];
}

/* ===================== DELETE ===================== */
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // Check usage in articles
    $check = $pdo->prepare("
        SELECT COUNT(*) 
        FROM articles 
        WHERE author_id = ? OR fact_checker_id = ?
    ");
    $check->execute([$delete_id, $delete_id]);
    $used = $check->fetchColumn();

    // Ask confirm first
    if ($used > 0 && !isset($_GET['confirm'])) {
        $_SESSION['confirm_delete_person'] = [
            'id' => $delete_id,
            'count' => $used
        ];
        header("Location: content_people.php");
        exit;
    }

    // ✅ Safety: set NULL manually (even if FK already ON DELETE SET NULL)
    $nullStmt = $pdo->prepare("
        UPDATE articles 
        SET author_id = NULL, fact_checker_id = NULL
        WHERE author_id = ? OR fact_checker_id = ?
    ");
    $nullStmt->execute([$delete_id, $delete_id]);

    // Delete image file
    $stmt = $pdo->prepare("SELECT profile_image FROM content_people WHERE id=?");
    $stmt->execute([$delete_id]);
    $old_image = $stmt->fetchColumn();

    if ($old_image && file_exists(__DIR__ . "/uploads/" . $old_image)) {
        unlink(__DIR__ . "/uploads/" . $old_image);
    }

    // Delete person
    $stmt = $pdo->prepare("DELETE FROM content_people WHERE id=?");
    $stmt->execute([$delete_id]);

    $_SESSION['success'] = "Profile deleted successfully.";
    header("Location: content_people.php");
    exit;
}

/* ===================== FETCH ALL ===================== */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total = $pdo->query("SELECT COUNT(*) FROM content_people")->fetchColumn();
$pages = ceil($total / $limit);

$stmt = $pdo->prepare("SELECT * FROM content_people ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$people = $stmt->fetchAll();
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
                <h3 class="font-weight-bold text-left">Manage Writers & Fact Checkers</h3>
            </div>
        </div>

        <div class="row">

            <!-- LEFT: FORM -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5><?= $edit_id ? 'Edit Person' : 'Add Person'; ?></h5>

                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="edit_id" value="<?= sanitize($edit_id); ?>">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= sanitize($name); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Qualification</label>
                                <input type="text" name="qualification" class="form-control" value="<?= sanitize($qualification); ?>">
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">— Select Role —</option>
                                    <option value="writer" <?= ($role=='writer')?'selected':''; ?>>Writer</option>
                                    <option value="fact_checker" <?= ($role=='fact_checker')?'selected':''; ?>>Fact Checker</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Short Bio</label>
                                <textarea name="bio" class="form-control" rows="3"><?= sanitize($bio); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Profile Picture</label>
                                <?php if(!empty($profile_image)): ?>
                                    <div>
                                        <img src="uploads/<?= sanitize($profile_image); ?>" width="100" style="margin-bottom:10px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="profile_image" class="form-control">
                            </div>

                            <button type="submit" name="save_person" class="btn btn-primary">
                                <?= $edit_id ? 'Update' : 'Add'; ?> Person
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT: LIST -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>People List</h5>
<?php if (!empty($_SESSION['confirm_delete_person'])): ?>
<?php $c = $_SESSION['confirm_delete_person']; ?>
<div class="alert alert-warning">
    This person is used in <?= $c['count'] ?> articles.<br>
    Delete anyway?

    <a href="?delete=<?= $c['id'] ?>&confirm=1" class="btn btn-danger btn-sm">
        Yes Delete
    </a>

    <a href="content_people.php" class="btn btn-secondary btn-sm">
        Cancel
    </a>
</div>
<?php unset($_SESSION['confirm_delete_person']); ?>
<?php endif; ?>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Qualification</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($people as $person): ?>
                                    <tr>
                                        <td>
                                            <?php if(!empty($person['profile_image'])): ?>
                                                <img src="uploads/<?= sanitize($person['profile_image']); ?>" width="50" class="rounded-circle">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= sanitize($person['name']); ?></td>
                                        <td><?= ucfirst(sanitize($person['role'])); ?></td>
                                        <td><?= sanitize($person['qualification']); ?></td>
                                        <td>
                                            <a href="?edit=<?= $person['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                            <a href="?delete=<?= $person['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this person?')">Delete</a>
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#viewModal<?= $person['id']; ?>">View</button>
                                        </td>
                                    </tr>

                                    <!-- VIEW MODAL -->
                                    <div class="modal fade" id="viewModal<?= $person['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?= $person['id']; ?>" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?= $person['id']; ?>">Person Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                            <?php if(!empty($person['profile_image'])): ?>
                                                <p><img src="uploads/<?= sanitize($person['profile_image']); ?>" width="150" class="rounded mb-2"></p>
                                            <?php endif; ?>
                                            <p><strong>Name:</strong> <?= sanitize($person['name']); ?></p>
                                            <p><strong>Role:</strong> <?= ucfirst(sanitize($person['role'])); ?></p>
                                            <p><strong>Qualification:</strong> <?= sanitize($person['qualification']); ?></p>
                                            <p><strong>Bio:</strong><br><?= nl2br(sanitize($person['bio'])); ?></p>
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- PAGINATION -->
                        <nav>
                            <ul class="pagination">
                                <?php for($i=1; $i<=$pages; $i++): ?>
                                    <li class="page-item <?= ($i==$page)?'active':''; ?>">
                                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
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
