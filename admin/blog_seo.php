<?php 
session_start();
include "config.php"; // DB connection

// Fetch all articles
$stmt = $pdo->query("SELECT id, title FROM articles ORDER BY id DESC");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h3 class="font-weight-bold">SEO Data for Article</h3>
                <h6 class="font-weight-normal mb-0">Select an article and add/update SEO fields.</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="submit_article_seo.php">

                            <!-- Article Dropdown -->
                            <div class="form-group">
                                <label>Select Article</label>
                                <select class="form-control" name="article_id" required>
                                    <option value="">-- Select Article --</option>
                                    <?php foreach ($articles as $article): ?>
                                        <option value="<?= $article['id']; ?>"><?= htmlspecialchars($article['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- SEO Fields -->
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" required>
                            </div>

                            <div class="form-group">
                                <label>Meta Keywords (comma-separated)</label>
                                <textarea class="form-control" name="meta_keywords" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="4" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit SEO Data</button>
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
   
