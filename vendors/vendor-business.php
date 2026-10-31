<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Business Details</h4>
                        <form method="POST" action="update_business_profile.php" enctype="multipart/form-data">
                            <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($vendor['vendor_id']); ?>">
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="business_name">Business Name</label>
                                    <input type="text" class="form-control" id="business_name" name="business_name" value="<?= htmlspecialchars($vendor['business_name'] ?? ''); ?>" >
                                </div>
                               <div class="form-group col-md-6">
                                <label for="business_phone">Business Phone</label>
                                <div class="input-group">
                            <input type="tel" class="form-control" id="business_phone" name="business_phone"
                                   value="<?= htmlspecialchars($vendor['business_phone'] ?? ''); ?>">
                            <input type="hidden" id="business_phone_code" name="business_phone_code"
                                   value="<?= htmlspecialchars('+' .($vendor['business_phone_code'] ?? '')); ?>">
                        </div>
                        <small class="text-danger" id="phone_error" style="display: none;">Please enter a valid phone number.</small>
                                
                            </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="business_email">Business Email</label>
                                    <input type="email" class="form-control" id="business_email" name="business_email" value="<?= htmlspecialchars($vendor['business_email'] ?? ''); ?>" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="logo">Business Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="website_url">Website</label>
                                <input type="text" class="form-control" id="website_url" name="website_url"
                                       placeholder="yourcompany.com"
                                       value="<?= htmlspecialchars($vendor['website_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="business_address">Address</label>
                                <textarea class="form-control" id="business_address" name="business_address" rows="3"><?= htmlspecialchars($vendor['business_address'] ?? ''); ?></textarea>
                            </div>

                            <h4 class="mt-4">Payment Information</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="bank_name">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= htmlspecialchars($vendor['bank_name'] ?? ''); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="account_number">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" name="account_number" value="<?= htmlspecialchars($vendor['account_number'] ?? ''); ?>" >
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="account_holder">Account Holder Name</label>
                                    <input type="text" class="form-control" id="account_holder" name="account_holder" value="<?= htmlspecialchars($vendor['account_holder'] ?? ''); ?>" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="iban">IBAN</label>
                                    <input type="text" class="form-control" id="iban" name="iban" value="<?= htmlspecialchars($vendor['iban'] ?? ''); ?>" >
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="swift_code">SWIFT Code</label>
                                <input type="text" class="form-control" id="swift_code" name="swift_code" value="<?= htmlspecialchars($vendor['swift_code'] ?? ''); ?>">
                            </div>


                            <button type="submit" class="btn" style="background-color:#4747A1;color:white;">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <?php if (!empty($vendor['logo'])): ?>
                            <img src="<?= htmlspecialchars($vendor['logo']); ?>" class="rounded-circle mb-3" alt="Logo" width="100">
                        <?php else: ?>
                            <img src="uploads/images/default-profile.png" class="rounded-circle mb-3" alt="Logo" width="100">
                        <?php endif; ?>
                        <h4 class="font-weight-bold"> <?= htmlspecialchars($vendor['business_name'] ?? 'Vendor'); ?> </h4>
                        <p class="text-muted">Vendor</p>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Store Performance</h5>
                        <p>Total Sales: <strong>$0.00</strong></p>
                        <p>Total Products: <strong>0</strong></p>
                        <p>Total Orders: <strong>0</strong></p>
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
    function initializePhoneInput(inputId, hiddenInputId) {
        var input = document.querySelector(inputId);
        var hiddenInput = document.querySelector(hiddenInputId);

        // Ensure intlTelInput is initialized only once
        if (!input.classList.contains("iti-initialized")) {
            var iti = window.intlTelInput(input, {
                separateDialCode: true,
                preferredCountries: ["us", "gb", "in", "pk"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });

            // Set the phone number with the country code from the database
            var phoneNumber = input.value.trim();
            var countryCode = hiddenInput.value.trim();

            if (phoneNumber && countryCode) {
                iti.setNumber(countryCode + phoneNumber);
            } else if (countryCode) {
                iti.setCountry(countryCode.replace("+", ""));
            }

            // Update hidden input when country changes
            input.addEventListener("countrychange", function () {
                var selectedCountryData = iti.getSelectedCountryData();
                hiddenInput.value = "+" + selectedCountryData.dialCode;
            });
        }
    }

    // Initialize for Business Phone
    initializePhoneInput("#business_phone", "#business_phone_code");

    // Initialize for Contact Phone
    initializePhoneInput("#contact_phone", "#contact_phone_code");
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
