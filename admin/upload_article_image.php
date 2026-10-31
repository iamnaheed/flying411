<?php
require_once 'config.php'; // Database connection

// ---------- Handle CKEditor Inline Image Upload (AJAX) ----------
if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode([
            "error" => ["message" => "Invalid file type. Allowed: jpg, jpeg, png, gif, webp"]
        ]);
        exit;
    }

    $newName = 'editor-' . time() . '.' . $ext;
    $uploadPath = __DIR__ . '/uploads/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode([
            "uploaded" => 1,
            "fileName" => $newName,
            "url" => "/admin/uploads/" . $newName // ✅ correct for both frontend & admin
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "error" => ["message" => "Failed to move uploaded file."]
        ]);
    }
    exit; // Important: stop further execution for AJAX upload
}
?>
