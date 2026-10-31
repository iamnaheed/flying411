<?php
session_start();
include "config.php"; // DB connection


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
    <!--<div class="row">-->
    <!--  <div class="col-md-12 grid-margin">-->
    <!--    <h3 class="font-weight-bold">Add Aircraft</h3>-->
    <!--    <h6 class="font-weight-normal mb-0">Fill in the details below to add a new aircraft.</h6>-->
    <!--  </div>-->
    <!--</div>-->
       <div class="row mb-3">
  <div class="col-md-12 grid-margin">
    <div class="row">
      <div class="col-12 col-xl-9 mb-4 mb-xl-0">
        <h3 class="font-weight-bold">SEO Data for Page</h3>
        <h6 class="font-weight-normal mb-0">
        Fill in the details below to add SEO data for Page. 
        </h6>
      </div>
      <!-- Right side content with buttons in one row -->
    </div>
  </div>
</div>
  <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">SEO Data for Page</h4>
                <form method="POST" action="submit_seo_data.php" enctype="multipart/form-data">
    <!-- Page Name -->
    <div class="form-group">
        <label>Page Name</label>
        <select class="form-control" name="page_name" required>
            <option value="home">Home</option>
            <option value="aircraft">Aircraft</option>
            <option value="engine">Engine</option>
            <option value="parts">Parts</option>
            <option value="blog">Blog</option>
            <option value="contact">Contact</option>
            <option value="services">Services</option>
        </select>
    </div>

    <!-- Meta Title -->
    <div class="form-group">
        <label>Meta Title</label>
        <input type="text" class="form-control" name="meta_title" required>
    </div>

    <!-- Meta Description -->
    <div class="form-group">
        <label>Meta Description</label>
        <textarea class="form-control" name="meta_description" rows="4" required></textarea>
    </div>

    <!-- Meta Keywords (Comma-Separated) -->
    <div class="form-group">
        <label>Meta Keywords (Comma Separated)</label>
        <textarea class="form-control" name="meta_keywords" rows="4" required></textarea>
    </div>

    <!-- Open Graph Title -->
    <div class="form-group">
        <label>Open Graph Title</label>
        <input type="text" class="form-control" name="og_title">
    </div>

    <!-- Open Graph Description -->
    <div class="form-group">
        <label>Open Graph Description</label>
        <textarea class="form-control" name="og_description" rows="4"></textarea>
    </div>

    <!-- Open Graph Image (File Upload) -->
    <div class="form-group">
        <label>Open Graph Image (Upload)</label>
        <input type="file" class="form-control" name="og_image" accept="image/*">
    </div>


    <!-- Noindex -->
    <div class="form-group">
        <label>Noindex</label>
        <select class="form-control" name="noindex">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <!-- Nofollow -->
    <div class="form-group">
        <label>Nofollow</label>
        <select class="form-control" name="nofollow">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <!-- SEO Status -->
    <div class="form-group">
        <label>SEO Status</label>
        <select class="form-control" name="seo_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
        </select>
    </div>

    <!-- Meta Robots -->
    <div class="form-group">
        <label>Meta Robots</label>
        <input type="text" class="form-control" name="meta_robots" placeholder="e.g., noindex, nofollow">
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



<?php include "footer.php"; ?>
   
