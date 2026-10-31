<?php

include "header.php";
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
  header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch vendor details
$query = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
$query->execute([$vendor_id]);
$vendor = $query->fetch(PDO::FETCH_ASSOC);
?>


<head>
    <!-- Include intl-tel-input CSS & JS -->
<style>
    /* Default input style */
.form-control {
    border: 1px solid #ccc !important;
    transition: border-color 0.3s ease-in-out !important;
}

/* If input is empty and focused, show red border */
.form-control:focus:required:invalid {
    border: 2px solid red !important;
}

/* If input is filled, reset border */
.form-control:valid {
    border: 1px solid #ccc !important;
}

.btn-primary{
    background-color: #4747A1!important;
    color: white !important;
}
.card {
    border-radius: 14px;
}

.card h4 {
    font-weight: 700;
}

.btn {
    min-width: 160px;
}

</style>
</head>
<div class="main-panel">
    <div class="content-wrapper">
    
  <div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start flex-wrap flex-lg-nowrap">
      
      <!-- LEFT SIDE: Welcome Text -->
      <div class="mb-3 mb-lg-0 me-lg-4">
        <h3 class="font-weight-bold">Profile Management</h3>
        <h6 class="font-weight-normal mb-0">
          Update your profile and payment details.
           <span class="text-primary"><?= count($notifications); ?> unread notifications!</span>
        </h6>
      </div>

      <!-- RIGHT SIDE: Listing Buttons -->
      <div class="pages-links w-100 w-lg-auto overflow-auto">
        <div class="d-flex flex-nowrap gap-2" style="justify-content:end;">
          <a href="addaircraft.php" class="btn btn-primary">List An Aircraft</a>
          <a href="addparts.php" class="btn btn-primary">List A Part</a>
          <a href="addengines.php" class="btn btn-primary">List An Engine</a>
          <a href="add_services.php" class="btn btn-primary">List A Service</a>
          <a href="manage_products.php" class="btn btn-primary">My Listings</a>
        </div>
      </div>

    </div>
  </div>
</div>

        <div class="container-fluid">
    <div class="row justify-content-center">

        <!-- LEFT: USER INFO -->
        <div class="col-12 col-lg-6 mb-4">

            <div class="card h-100">
                <div class="card-body">

                    <h4 class="mb-4">User Login Information</h4>

                    <form method="POST" action="update_profile.php" enctype="multipart/form-data">

                        <input type="hidden" name="vendor_id"
                               value="<?= $vendor['vendor_id'] ?>">

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">
                                <label>User Name</label>
                                <input type="text"
                                       name="contact_name"
                                       class="form-control"
                                       value="<?= htmlspecialchars($vendor['contact_name']) ?>">
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email"
                                       name="contact_email"
                                       class="form-control"
                                       value="<?= htmlspecialchars($vendor['contact_email']) ?>">
                            </div>

                            <div class="col-12 col-md-6 mb-3">
    <label>Phone</label>

    <input type="tel"
           id="contact_phone"
           name="contact_phone"
           class="form-control"
           value="<?= htmlspecialchars($vendor['contact_phone']) ?>">

    <input type="hidden"
           id="contact_phone_code"
           name="contact_phone_code"
           value="<?= htmlspecialchars($vendor['contact_phone_code']) ?>">
</div>


                            <div class="col-12 col-md-6 mb-3">
                                <label>Profile Picture</label>
                                <input type="file"
                                       name="profile_picture"
                                       class="form-control">
                            </div>

                        </div>

                        <button type="submit"
                                class="btn btn-primary mt-3">
                            Update Profile
                        </button>

                    </form>

                </div>
            </div>

        </div>

        <!-- RIGHT: CHANGE PASSWORD -->
        <div class="col-12 col-lg-6 mb-4">

    <div class="card h-100">
        <div class="card-body">

            <h4 class="mb-4">Change Password</h4>

            <!-- ERROR / SUCCESS MESSAGE HERE -->

            <form method="POST" action="update_password.php">

                <div class="row">

                    <div class="col-12 mb-3">
                        <label>Current Password</label>
                        <input type="password"
                               name="current_password"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 mb-3">
                        <label>New Password</label>
                        <input type="password"
                               name="new_password"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Confirm Password</label>
                        <input type="password"
                               name="confirm_password"
                               class="form-control"
                               required>
                    </div>

                </div>
                    <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
                <button type="submit" class="btn btn-warning mt-2">
                    Update Password
                </button>
              
            </form>
           
        </div>
    </div>

</div>


    </div>
</div>

    </div>
    
   
</div>


<!-- Include intlTelInput CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    var input = document.querySelector("#contact_phone");
    var hidden = document.querySelector("#contact_phone_code");

    var iti = window.intlTelInput(input, {
        separateDialCode: true,
        preferredCountries: ["pk", "in", "ae", "us", "gb"],
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
    });

    // Load number from DB
    if (hidden.value && input.value) {
        iti.setNumber(hidden.value + input.value);
    }

    // Update country code
    input.addEventListener("countrychange", function () {
        var countryData = iti.getSelectedCountryData();
        hidden.value = "+" + countryData.dialCode;
    });

});
</script>





<script>
    document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".form-control[required]");

    inputs.forEach(input => {
        input.addEventListener("focusout", function () {
            if (!input.value.trim()) {
                input.style.border = "2px solid red";
            } else {
                input.style.border = "1px solid #ccc";
            }
        });

        input.addEventListener("input", function () {
            if (input.value.trim()) {
                input.style.border = "1px solid #ccc";
            }
        });
    });
});

</script>
<?php include "footer.php"; ?>
