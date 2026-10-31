<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Ensure vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch support messages
$query = $pdo->prepare("
    SELECT message_id, message, sender, status, created_at
    FROM support_messages
    WHERE vendor_id = ?
    ORDER BY created_at DESC
");
$query->execute([$vendor_id]);
$messages = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="font-weight-bold">Support Messages</h3>
    <h6 class="font-weight-normal mb-4">Send messages to support and track responses.</h6>

    <!-- Send Message Form -->
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Send a New Message</h4>
        <form method="POST" action="send_message.php">
          <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Describe your issue" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>

    <!-- Messages Table -->
    <div class="card mt-4">
      <div class="card-body">
        <h4 class="card-title">Your Messages</h4>
        <table class="table">
          <thead>
            <tr>
              <th>Message</th>
              <th>Sender</th>
              <th>Status</th>
              <th>Sent At</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($messages) > 0): ?>
              <?php foreach ($messages as $message): ?>
                <tr>
                  <td><?= htmlspecialchars($message['message']); ?></td>
                  <td>
                    <?= $message['sender'] === 'vendor' ? '<span class="text-primary">You</span>' : '<span class="text-success">Support</span>'; ?>
                  </td>
                  <td>
                    <span class="<?= $message['status'] === 'read' ? 'text-success' : 'text-warning'; ?>">
                      <?= ucfirst($message['status']); ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($message['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No messages found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php include "footer.php"; ?>
