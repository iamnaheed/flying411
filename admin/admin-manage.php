<?php

// session_start();
include __DIR__ . "/config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle admin deletion
if (isset($_GET['delete_admin'])) {
    $admin_id = $_GET['delete_admin'];

    if ($admin_id == $_SESSION['admin_id']) {
        $_SESSION['error_message'] = "You cannot delete your own account!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $_SESSION['success_message'] = "Admin deleted successfully!";
    }
    header("Location: admin-manage.php");
    exit();
}

// Handle status toggle (Active <--> Inactive)
if (isset($_GET['toggle_status'])) {
    $admin_id = $_GET['toggle_status'];
    $stmt = $pdo->prepare("SELECT status FROM admins WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();

    if ($admin) {
        $new_status = ($admin['status'] == 'active') ? 'inactive' : 'active';
        $update = $pdo->prepare("UPDATE admins SET status = ? WHERE admin_id = ?");
        $update->execute([$new_status, $admin_id]);
        $_SESSION['success_message'] = "Admin status updated!";
    }
    header("Location: admin-manage.php");
    exit();
}

// Handle admin creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = $_POST['status'];
     $role = $_POST['role'] ?? 'Regular Admin';

    $profile_picture = "default.jpg"; 
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $profile_picture = time() . "_" . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_dir . $profile_picture);
    }

    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, status, profile_picture, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $password, $status, $profile_picture, $role]);
    $_SESSION['success_message'] = "Admin added successfully!";
    header("Location: admin-manage.php");
    exit();
}

// Handle admin update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $admin_id = $_POST['admin_id'];
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $role = $_POST['role'] ?? 'Regular Admin';


    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $new_profile_picture = time() . "_" . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_dir . $new_profile_picture);

        $stmt = $pdo->prepare("UPDATE admins SET profile_picture = ? WHERE admin_id = ?");
        $stmt->execute([$new_profile_picture, $admin_id]);
    }

      if ($password) {
        $stmt = $pdo->prepare("UPDATE admins SET email = ?, password = ?, role = ? WHERE admin_id = ?");
        $stmt->execute([$email, $password, $role, $admin_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE admins SET email = ?, role = ? WHERE admin_id = ?");
        $stmt->execute([$email, $role, $admin_id]);
    }


    $_SESSION['success_message'] = "Admin details updated!";
    header("Location: admin-manage.php");
    exit();
}

// Fetch all admins
$query = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC");
$admins = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch admin details for editing
$edit_admin = null;
if (isset($_GET['edit_admin'])) {
    $edit_admin_id = $_GET['edit_admin'];
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->execute([$edit_admin_id]);
    $edit_admin = $stmt->fetch(PDO::FETCH_ASSOC);
}

include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Admins</h3>
        <h6 class="font-weight-normal mb-0">Add, edit, delete, or manage admin accounts.</h6>
      </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="row">
      <!-- Left Side: Add/Edit Admin Form -->
      <div class="col-md-4" style="margin-bottom:12px;">
        <div class="card shadow-sm">
         <div class="card-body">
  <h4 class="card-title"><?= $edit_admin ? 'Edit Admin' : 'Add New Admin' ?></h4>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="admin_id" value="<?= $edit_admin['admin_id'] ?? ''; ?>">

    <div class="form-group">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= $edit_admin['name'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= $edit_admin['email'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
      <label>Password <?= $edit_admin ? '(Leave blank to keep current password)' : '' ?></label>
      <input type="password" name="password" class="form-control">
    </div>

    <div class="form-group">
      <label>Profile Picture</label>
      <input type="file" name="profile_picture" class="form-control">
      <?php if ($edit_admin && !empty($edit_admin['profile_picture'])): ?>
        <img src="uploads/<?= htmlspecialchars($edit_admin['profile_picture']); ?>" width="50" height="50">
      <?php endif; ?>
    </div>

    <!-- ✅ Admin Role Dropdown -->
    <div class="form-group">
      <label>Admin Role</label>
      <select name="role" class="form-control" required>
        <option value="">-- Select Role --</option>
        <option value="Super Admin" <?= isset($edit_admin['role']) && $edit_admin['role'] === 'Super Admin' ? 'selected' : '' ?>>Super Admin</option>
        <option value="Regular Admin" <?= isset($edit_admin['role']) && $edit_admin['role'] === 'Regular Admin' ? 'selected' : '' ?>>Regular Admin</option>
        <option value="SEO Admin" <?= isset($edit_admin['role']) && $edit_admin['role'] === 'SEO Admin' ? 'selected' : '' ?>>SEO Admin</option>
      </select>
    </div>

    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="active" <?= isset($edit_admin['status']) && $edit_admin['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?= isset($edit_admin['status']) && $edit_admin['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" name="<?= $edit_admin ? 'update_admin' : 'add_admin' ?>" class="btn btn-primary">
      <?= $edit_admin ? 'Update Admin' : 'Add Admin' ?>
    </button>

    <?php if ($edit_admin): ?>
      <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancel</a>
    <?php endif; ?>
  </form>
</div>

        </div>
      </div>

      <!-- Right Side: Admins List Table -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Admins List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($admins as $admin): ?>
                    <tr>
                      <td><img src="uploads/<?= htmlspecialchars($admin['profile_picture']); ?>" class="rounded-circle" width="40" height="40"></td>
                      <td><?= htmlspecialchars($admin['name']); ?></td>
                      <td><?= htmlspecialchars($admin['email']); ?></td>
                       <td><?= htmlspecialchars($admin['role']); ?></td>
                      <td>
                        <a href="?toggle_status=<?= $admin['admin_id']; ?>" class="btn btn-sm <?= $admin['status'] === 'active' ? 'btn-success' : 'btn-warning'; ?>">
                          <?= ucfirst($admin['status']); ?>
                        </a>
                      </td>
                      <td>
                        <a href="?edit_admin=<?= $admin['admin_id']; ?>" class="btn btn-info btn-sm">Edit</a>
                        <a href="?delete_admin=<?= $admin['admin_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
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
