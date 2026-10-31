<?php
// Include the config.php file for database connection
include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect form data
    $page_name = $_POST['page_name'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $og_title = $_POST['og_title'] ?? null;
    $og_description = $_POST['og_description'] ?? null;
    
    // Handle file upload for Open Graph image
    $og_image = null;
    if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] == 0) {
        $upload_dir = 'uploads/og_images/';
        $og_image = $upload_dir . basename($_FILES['og_image']['name']);
        
        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['og_image']['tmp_name'], $og_image)) {
            echo "Failed to upload Open Graph image.";
            exit;
        }
    }

    $noindex = $_POST['noindex'] == '1' ? 1 : 0;
    $nofollow = $_POST['nofollow'] == '1' ? 1 : 0;
    $seo_status = $_POST['seo_status'];
    $meta_robots = $_POST['meta_robots'];

    // Convert meta keywords into a comma-separated string
    $meta_keywords = implode(',', array_map('trim', explode(',', $meta_keywords)));

    try {
        // Prepare SQL query to insert the data into the database
        $sql = "INSERT INTO seo_meta (page_name, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, 
                 noindex, nofollow, seo_status, meta_robots)
                VALUES (:page_name, :meta_title, :meta_description, :meta_keywords, :og_title, :og_description, :og_image, 
                         :noindex, :nofollow, :seo_status, :meta_robots)";

        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':page_name', $page_name);
        $stmt->bindParam(':meta_title', $meta_title);
        $stmt->bindParam(':meta_description', $meta_description);
        $stmt->bindParam(':meta_keywords', $meta_keywords);
        $stmt->bindParam(':og_title', $og_title);
        $stmt->bindParam(':og_description', $og_description);
        $stmt->bindParam(':og_image', $og_image);
        $stmt->bindParam(':noindex', $noindex, PDO::PARAM_INT);
        $stmt->bindParam(':nofollow', $nofollow, PDO::PARAM_INT);
        $stmt->bindParam(':seo_status', $seo_status);
        $stmt->bindParam(':meta_robots', $meta_robots);

        // Execute the statement
        $stmt->execute();

        // Redirect after successful insertion
        header('Location: seo_success.php'); // Redirect to a success page
        exit;

    } catch (PDOException $e) {
        // If there's an error, display the message
        echo "Error: " . $e->getMessage();
    }
}
?>
