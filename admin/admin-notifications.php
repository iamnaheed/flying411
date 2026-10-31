<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php'; // Admin header file

// Mark notification as read
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?")->execute([$notification_id]);
}

// Mark all as read
if (isset($_POST['mark_all_read'])) {
    $pdo->query("UPDATE notifications SET is_read = 1");
}

// Filter by type and status
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query notifications
$query = "SELECT * FROM notifications WHERE 1=1";
$params = [];

if ($type_filter) {
    $query .= " AND type = ?";
    $params[] = $type_filter;
}
if ($status_filter === 'unread') {
    $query .= " AND is_read = 0";
} elseif ($status_filter === 'read') {
    $query .= " AND is_read = 1";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="mt-4">Admin Notifications</h4>

            <!-- Filters -->
            <form method="GET" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="order" <?= $type_filter === 'order' ? 'selected' : ''; ?>>Order</option>
                            <option value="payment" <?= $type_filter === 'payment' ? 'selected' : ''; ?>>Payment</option>
                            <option value="general" <?= $type_filter === 'general' ? 'selected' : ''; ?>>General</option>
                            <option value="alert" <?= $type_filter === 'alert' ? 'selected' : ''; ?>>Alert</option>
                        </select>
                    </div>
                    <div class="col">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="unread" <?= $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                            <option value="read" <?= $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Mark All as Read -->
            <form method="POST">
                <button type="submit" name="mark_all_read" class="btn btn-warning mb-3">Mark All as Read</button>
            </form>

            <!-- Notifications Table -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    Notifications
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td><?= htmlspecialchars($notification['message']); ?></td>
                                    <td>
                                        <span class="badge badge-<?= $notification['type'] === 'alert' ? 'danger' : 'primary'; ?>">
                                            <?= ucfirst($notification['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $notification['is_read'] ? 'success' : 'warning'; ?>">
                                            <?= $notification['is_read'] ? 'Read' : 'Unread'; ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($notification['created_at'])); ?></td>
                                    <td>
                                        <?php if (!$notification['is_read']): ?>
                                            <form method="POST">
                                                <input type="hidden" name="notification_id" value="<?= $notification['notification_id']; ?>">
                                                <button type="submit" name="mark_read" class="btn btn-sm btn-success">Mark as Read</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Read</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($notifications)): ?>
                                <tr><td colspan="5" class="text-center">No notifications available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
