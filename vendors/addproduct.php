<?php
include "header.php";
include "config.php"; // Database connection

// Get vendor ID (Assuming session holds vendor details)
session_start();
if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php"); // Redirect to login if not authenticated
    exit;
}
$vendor_id = $_SESSION['vendor_id'];
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Add Product</h3>
        <h6 class="font-weight-normal mb-0">Select a product type to start adding your product.</h6>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Product Details</h4>
            <form method="POST" action="submit_product.php" enctype="multipart/form-data">
              <input type="hidden" name="vendor_id" value="<?= $vendor_id ?>">

              <div class="form-group">
                <label for="product_type">Product Type</label>
                <select class="form-control" id="product_type" name="product_type" onchange="showProductForm()" required>
                  <option value="">Select Type</option>
                  <option value="aircraft">Aircraft</option>
                  <option value="engine">Engine</option>
                  <option value="part">Part</option>
                </select>
              </div>

              <!-- Dynamic Product Forms -->
              <div id="product-form"></div>

              <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showProductForm() {
  const productType = document.getElementById("product_type").value;
  const productForm = document.getElementById("product-form");

  productForm.innerHTML = ""; // Clear previous form

  if (productType === "aircraft") {
    productForm.innerHTML = `
      <h4 class="mt-4">Aircraft Details</h4>
      <div class="form-group"><label>Model</label><input type="text" class="form-control" name="model" required></div>
      <div class="form-group"><label>Category</label><input type="text" class="form-control" name="category" required></div>
      <div class="form-group"><label>Location</label><input type="text" class="form-control" name="location" required></div>
      <div class="form-group"><label>Aircraft Type</label><input type="text" class="form-control" name="aircraft_type" required></div>
      <div class="form-group"><label>Manufacturer</label><input type="text" class="form-control" name="manufacturer" required></div>
      <div class="form-group"><label>Condition</label><input type="text" class="form-control" name="condition" required></div>
      <div class="form-group"><label>Year</label><input type="number" class="form-control" name="year" required></div>
      <div class="form-group"><label>Total Time Hours</label><input type="number" class="form-control" name="total_time_hours" required></div>
      <div class="form-group"><label>Engine SMH</label><input type="text" class="form-control" name="engine_smh" required></div>
      <div class="form-group"><label>Price</label><input type="number" class="form-control" name="price" required></div>
      <div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="4" required></textarea></div>
      <div class="form-group"><label>Features</label><textarea class="form-control" name="features" rows="4" required></textarea></div>
      <div class="form-group"><label>Warranty</label><textarea class="form-control" name="warranty" rows="4" required></textarea></div>
      <div class="form-group"><label>Product Images</label><input type="file" class="form-control" name="images[]" multiple required></div>
    `;
  } else if (productType === "engine") {
    productForm.innerHTML = `
      <h4 class="mt-4">Engine Details</h4>
      <div class="form-group"><label>Model</label><input type="text" class="form-control" name="model" required></div>
      <div class="form-group"><label>Manufacturer</label><input type="text" class="form-control" name="manufacturer" required></div>
      <div class="form-group"><label>Location</label><input type="text" class="form-control" name="location" required></div>
      <div class="form-group"><label>Engine Type</label><input type="text" class="form-control" name="engine_type" required></div>
      <div class="form-group"><label>Power/Thrust</label><input type="text" class="form-control" name="power_thrust" required></div>
      <div class="form-group"><label>Year</label><input type="number" class="form-control" name="year" required></div>
      <div class="form-group"><label>Total Time Hours</label><input type="number" class="form-control" name="total_time_hours" required></div>
      <div class="form-group"><label>HR</label><input type="number" class="form-control" name="hr" required></div>
      <div class="form-group"><label>Cycles</label><input type="number" class="form-control" name="cycles" required></div>
      <div class="form-group"><label>Condition</label><input type="text" class="form-control" name="condition" required></div>
      <div class="form-group"><label>Price</label><input type="number" class="form-control" name="price" required></div>
      <div class="form-group"><label>Extra Details</label><textarea class="form-control" name="extra_details" rows="4" required></textarea></div>
      <div class="form-group"><label>Warranty</label><textarea class="form-control" name="warranty" rows="4" required></textarea></div>
      <div class="form-group"><label>Product Images</label><input type="file" class="form-control" name="images[]" multiple required></div>
    `;
  } else if (productType === "part") {
    productForm.innerHTML = `
      <h4 class="mt-4">Part Details</h4>
      <div class="form-group"><label>Part Number</label><input type="text" class="form-control" name="part_number" required></div>
      <div class="form-group"><label>Type</label><input type="text" class="form-control" name="type" required></div>
      <div class="form-group"><label>Condition</label><input type="text" class="form-control" name="condition" required></div>
      <div class="form-group"><label>Region</label><input type="text" class="form-control" name="region" required></div>
      <div class="form-group"><label>Price</label><input type="number" class="form-control" name="price" required></div>
      <div class="form-group"><label>Tagged with EASA Form 1</label><input type="text" class="form-control" name="tagged_with_easa_form_1" required></div>
      <div class="form-group"><label>Extra Details</label><textarea class="form-control" name="extra_details" rows="4" required></textarea></div>
      <div class="form-group"><label>Warranty</label><textarea class="form-control" name="warranty" rows="4" required></textarea></div>
      <div class="form-group"><label>Product Images</label><input type="file" class="form-control" name="images[]" multiple required></div>
    `;
  }
}
</script>

<?php include "footer.php"; ?>
