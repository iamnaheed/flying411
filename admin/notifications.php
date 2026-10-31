<?php
session_start();
include "config.php"; // Ensure database connection is included
include "header.php"; // Include navigation/header

// Ensure the vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch notifications for the logged-in vendor
$query = $pdo->prepare("
    SELECT 
        notification_id,
        message,
        type,
        created_at,
        is_read
    FROM notifications
    WHERE vendor_id = ?
    ORDER BY created_at DESC
");
$query->execute([$vendor_id]);
$notifications = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Notifications</h3>
                <h6 class="font-weight-normal mb-0">Stay updated with the latest alerts and updates.</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Notifications</h4>
                        <div class="list-group">
                            <?php if (!empty($notifications)): ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <a href="mark_notification_read.php?id=<?= htmlspecialchars($notification['notification_id']); ?>" 
                                       class="list-group-item list-group-item-action <?= $notification['is_read'] ? 'list-group-item-light' : 'list-group-item-primary'; ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?= ucfirst(htmlspecialchars($notification['type'])); ?> Notification</h5>
                                            <small><?= date("M d, Y H:i", strtotime($notification['created_at'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($notification['message'])); ?></p>
                                        <small class="<?= $notification['is_read'] ? 'text-muted' : 'text-bold'; ?>">
                                            <?= $notification['is_read'] ? 'Read' : 'Unread'; ?>
                                        </small>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">No notifications found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<?php include "footer.php"; ?>
