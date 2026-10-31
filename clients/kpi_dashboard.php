<?php
session_start();
include "header.php";
include "config.php"; 

if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch this vendor's aircraft listings
$stmt = $pdo->prepare("
    SELECT * FROM aircrafts 
    WHERE vendor_id = :vendor_id 
    AND deleted_at IS NULL
");
$stmt->execute(['vendor_id' => $vendor_id]);
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare array for aircraft images
$aircraftImages = [];

// Loop through each aircraft and fetch its images
foreach ($aircrafts as $aircraft) {
    $aircraft_id = $aircraft['aircraft_id'];

    // Fetch aircraft images for this aircraft
    $imgStmt = $pdo->prepare("
        SELECT image_url 
        FROM product_images 
        WHERE product_id = :aircraft_id 
        AND product_type = 'aircraft' 
        ORDER BY sort_order ASC
    ");
    $imgStmt->execute(['aircraft_id' => $aircraft_id]);
    $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

    $aircraftImages[$aircraft_id] = $images;
}

// Optional: aircraft stats function (if needed elsewhere)
function fetchStats($pdo, $model) {
    $s = $pdo->prepare("
        SELECT 
            AVG(price) AS avg_price, 
            MIN(price) AS min_price, 
            MAX(price) AS max_price,
            AVG(year) AS avg_year, 
            MIN(year) AS min_year, 
            MAX(year) AS max_year,
            AVG(total_time_hours) AS avg_hours, 
            MIN(total_time_hours) AS min_hours, 
            MAX(total_time_hours) AS max_hours
        FROM aircrafts
        WHERE model = :model
        AND deleted_at IS NULL
    ");
    $s->execute(['model' => $model]);
    return $s->fetch(PDO::FETCH_ASSOC);
}
?>
<head>
    <style>
        /* ===== Aircraft Listing Layout ===== */
.aircraft-listing{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:1.25rem;
    padding:1rem 0;
}

/* ===== Card Container ===== */
.car-card{
    position:relative;
    background:#fff;
    border-radius:.75rem;
    box-shadow:0 4px 14px rgba(0,0,0,.08);
    overflow:hidden;
    transition:transform .25s ease;
}
.car-card:hover{transform:translateY(-4px);}

/* ===== Slider Wrapper ===== */
.slider{
    position:relative;
    width:100%;
    height:220px;          /* adjust for your design */
    overflow:hidden;
}
.slider .slides{
    display:flex;          /* enables translateX animation */
    height:100%;
    transition:transform .45s ease-in-out;
}
.slider .slides img{
    flex:0 0 100%;
    width:100%;
    height:100%;
    object-fit:cover;
}

/* ===== Overlay Elements ===== */
.new-tag{
    position:absolute;
    top:.75rem;
    left:.75rem;
    background:#e60023;
    color:#fff;
    letter-spacing:.6px;
    font-weight:600;
    padding:.2rem .5rem;
    border-radius:.25rem;
    z-index:2;
}
.favorite-icons{
    position:absolute;
    top:.75rem;
    right:.75rem;
    z-index:2;
}
.favorite-icons img{filter:drop-shadow(0 1px 2px rgba(0,0,0,.25));}

/* ===== Navigation Arrows ===== */
.arrows{
    position:absolute;
    top:50%;
    left:0;
    right:0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 .75rem;
    transform:translateY(-50%);
    z-index:2;
}
.arrow-left,
.arrow-right{
    width:34px;
    height:34px;
    display:flex;
    justify-content:center;
    align-items:center;
    background:rgba(255,255,255,.85);
    border-radius:50%;
    cursor:pointer;
    backdrop-filter:blur(2px);
    transition:background .2s;
}
.arrow-left:hover,
.arrow-right:hover{background:#fff;}
.arrow-left img,
.arrow-right img{pointer-events:none;}

/* ===== Slider Dots ===== */
.slider-dots{
    position:absolute;
    bottom:.65rem;
    left:50%;
    transform:translateX(-50%);
    display:flex;
    gap:.4rem;
    z-index:2;
}
.slider-dots .dot{
    width:8px;
    height:8px;
    border-radius:50%;
    background:rgba(255,255,255,.55);
    cursor:pointer;
    transition:background .25s;
}
.slider-dots .dot.active,
.slider-dots .dot:hover{background:#ffffff;}

/* ===== Card Info Section ===== */
.car-info{
    padding:1rem .75rem 1.25rem;
    border-top:1px solid #f1f1f1;
}
.car-info h2{
    margin:0 0 .75rem;
    font-size:1.1rem;
    font-weight:600;
    color:#000;
}
.button-group{
    display:flex;
    gap:.5rem;
}
.button-group button{
    flex:1;
    padding:.45rem .75rem;
    font-size:.8rem;
    font-weight:500;
    background:#004aad;
    color:#fff;
    border:none;
    border-radius:.35rem;
    cursor:pointer;
    transition:background .25s;
}
.button-group button:hover{background:#0061d5;}

    </style>
</head>

<div class="main-panel" style="margin:40px;">
    <div class="aircraft-listing">
        <?php foreach ($aircrafts as $aircraft): ?>
            <?php
                $slug = strtolower(str_replace([' ', '/'], '-', $aircraft['model']));
                $images = $aircraftImages[$aircraft['aircraft_id']] ?? [];
            ?>
            <div class="car-card">
                <div class="slider" data-current-slide="0">
                    <span class="new-tag" style="font-size: 10px !important;">NEW</span>
                    <div class="favorite-icons">
                        <img src="/assets/pages/img/icons/ribbon.png" width="25px" alt="">
                    </div>
                    <a href="/<?= $slug ?>/<?= $aircraft['aircraft_id'] ?>">
                        <div class="slides">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                    <?php
                                        $vendorPath = 'vendors/' . $img;
                                        $clientPath = $img; // if already relative path like 'uploads/aircraft1.jpg'
                                        $finalPath = '';

                                        if (file_exists($vendorPath)) {
                                            $finalPath = '/' . $vendorPath;
                                        } elseif (file_exists($clientPath)) {
                                            $finalPath = '/' . $clientPath;
                                        } else {
                                            $finalPath = '/assets/pages/img/products/default-aircraft.jpg';
                                        }
                                    ?>
                                    <img src="<?= $finalPath ?>" alt="Aircraft Image">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="/assets/pages/img/products/default-aircraft.jpg" alt="Default Aircraft Image">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="arrows">
                        <span class="arrow-left">
                            <img src="/assets/pages/img/icons/left-chevron.png" width="17px" alt="">
                        </span>
                        <span class="arrow-right">
                            <img src="/assets/pages/img/icons/right-arrow-angle.png" width="17px" alt="">
                        </span>
                    </div>
                    <div class="slider-dots">
                        <?php
                            $dotCount = max(1, count($images));
                            for ($i = 0; $i < $dotCount; $i++): ?>
                            <span class="dot <?= $i === 0 ? 'active' : '' ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="car-info" style="text-align: left; font-size: 16px;">
                    <?php if ($aircraft['price_label'] === 'call'): ?>
                        <h2 style="margin-top:50px;margin-left: 15px; font-size: 22px !important; color: #000; font-weight: 500;">
                            Call for Price
                        </h2>
                    <?php else: ?>
                        <h2 style="margin-top:40px;margin-left: 15px; font-size: 30px !important; color: #000; font-weight: 500;">
                            $<?= number_format($aircraft['price']) ?>
                        </h2>
                    <?php endif; ?>

                    <div class="button-group">
                        <a href="/clients/kpi_details.php?id=<?= $aircraft['aircraft_id']?>">
                        <button onclick="">Comparison</button>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include "footer.php"; ?>

 <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();    
            Layout.initOWL();
            Layout.initImageZoom();
            Layout.initTouchspin();
            Layout.initTwitter();
        });
    </script>

    <script>
        document.getElementById("sidebarToggle").onclick = function() {
            var sidebar = document.querySelector(".sidebar-menu");
            sidebar.classList.toggle("toggle-show");
        }
    </script>
    <!-- END PAGE LEVEL JAVASCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Page Loaded");
        });
    </script>
   <script>
    function resetFilters() {
        alert("Filters have been reset.");
        // Add logic to reset filters
    }

    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "block";
        }
    }
</script>
     <!-- END PAGE LEVEL JAVASCRIPTS -->
    <script>
       document.querySelectorAll('.car-card .slider').forEach((slider) => {
          const slidesContainer = slider.querySelector('.slides');
          const leftArrow = slider.querySelector('.arrow-left');
          const rightArrow = slider.querySelector('.arrow-right');
          const dots = slider.querySelectorAll('.dot');
          const slides = slidesContainer.children;
          const totalSlides = slides.length;
          let currentSlide = 0;
      
          function updateSlide() {
              // Calculate the correct width of a single slide
              const slideWidth = slider.clientWidth;
              slidesContainer.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
              
              // Update dots to reflect the active slide
              dots.forEach((dot, index) => {
                  dot.classList.toggle('active', index === currentSlide);
              });
          }
      
          leftArrow.addEventListener('click', () => {
              currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
              updateSlide();
          });
      
          rightArrow.addEventListener('click', () => {
              currentSlide = (currentSlide + 1) % totalSlides;
              updateSlide();
          });
      
          dots.forEach((dot, index) => {
              dot.addEventListener('click', () => {
                  currentSlide = index;
                  updateSlide();
              });
          });
      
          // Ensure slides have the correct width on page load
          window.addEventListener('resize', updateSlide);
          updateSlide(); // Initial update
      });
      
      </script>
      <script>
        function initSliders() {
    document.querySelectorAll(".slider").forEach((slider) => {
        let slides = slider.querySelectorAll(".slides img");
        let dots = slider.querySelectorAll(".slider-dots .dot");
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? "block" : "none";
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });
        }

        slider.querySelector(".arrow-left").addEventListener("click", () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        });

        slider.querySelector(".arrow-right").addEventListener("click", () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        });

        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        showSlide(currentSlide);
    });
}

    </script>

