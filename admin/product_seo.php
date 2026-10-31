<?php
session_start();
include "config.php"; // DB connection

// Fetch products based on category selection (initially empty)
$products = [];
$category = isset($_POST['category']) ? $_POST['category'] : ''; // For storing selected category

// Fetch products based on selected category
if ($category == 'aircraft') {
    $stmt = $pdo->query("SELECT aircraft_id, model FROM aircrafts");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($category == 'engine') {
    $stmt = $pdo->query("SELECT engine_id, model FROM engines");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($category == 'parts') {
    $stmt = $pdo->query("SELECT part_id, part_name FROM parts");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include "header.php" ?>
<head>
    <style>
    .form-check .form-check-label{
        margin-left:4px !important;
    }
    </style>
</head>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-9 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">SEO Data for Product</h3>
                        <h6 class="font-weight-normal mb-0">Fill in the details below to add SEO data for product.</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">SEO Data for Product</h4>
                        <form method="POST" action="submit_product_seo.php" enctype="multipart/form-data">
    <!-- Category Select -->
    <div class="form-group">
        <label>Category</label>
        <select class="form-control" name="category" id="category" onchange="loadProducts()" required>
            <option value="">Select Category</option>
            <option value="aircraft" <?= $category == 'aircraft' ? 'selected' : ''; ?>>Aircraft</option>
            <option value="engine" <?= $category == 'engine' ? 'selected' : ''; ?>>Engine</option>
            <option value="parts" <?= $category == 'parts' ? 'selected' : ''; ?>>Parts</option>
        </select>
    </div>

    <!-- Product Select (dynamically populated based on category) -->
    <div class="form-group" id="product_div" style="display: <?= !empty($products) ? 'block' : 'none'; ?>;">
        <label>Select Product</label>
        <select class="form-control" name="product_id" id="product_id" required onchange="setHiddenProductId()">
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['aircraft_id'] ?? $product['engine_id'] ?? $product['part_id']; ?>">
                    <?= $product['model'] ?? $product['part_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Hidden input for product_id -->
    <input type="hidden" name="product_id" id="hidden_product_id">

    <!-- SEO Data Fields -->
    <div class="form-group">
        <label>Meta Title</label>
        <input type="text" class="form-control" name="meta_title" required>
    </div>

    <div class="form-group">
        <label>Meta Keywords (Comma Separated)</label>
        <textarea class="form-control" name="meta_keywords" rows="4" required></textarea>
    </div>

    <div class="form-group">
        <label>Meta Description</label>
        <textarea class="form-control" name="meta_description" rows="4" required></textarea>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn" style="background-color:#4747a1;color:white;">Submit SEO Data</button>
</form>




                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// JavaScript function to dynamically load products based on the selected category
function loadProducts() {
    var category = document.getElementById("category").value;
    var productDiv = document.getElementById("product_div");

    // Show the product dropdown if a category is selected
    if (category) {
        productDiv.style.display = "block";

        // Send AJAX request to fetch products based on category
        $.ajax({
            type: 'POST',
            url: 'get_products.php',
            data: { category: category },
            success: function(response) {
                var products = JSON.parse(response);
                var productSelect = document.getElementById("product_id");
                productSelect.innerHTML = '<option value="">Select Product</option>'; // Reset options

                // Populate the product dropdown
                products.forEach(function(product) {
                    var option = document.createElement("option");
                    option.value = product.id;
                    option.textContent = product.name;
                    productSelect.appendChild(option);
                });
            }
        });
    } else {
        productDiv.style.display = "none"; // Hide the product dropdown if no category is selected
    }
}
</script>

<script>
// JavaScript function to set the hidden product_id field when a product is selected
function setHiddenProductId() {
    var productSelect = document.getElementById("product_id");
    var hiddenProductId = document.getElementById("hidden_product_id");
    
    // Set the hidden field value to the selected product_id
    hiddenProductId.value = productSelect.value;
}
</script>





<?php include "footer.php"; ?>
   
