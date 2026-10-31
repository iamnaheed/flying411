<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php'; // Admin header file

// Default date range (Last 30 days)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total sales
$query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE order_date BETWEEN :start_date AND :end_date";
$stmt = $pdo->prepare($query);
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$total_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

// Get total vendor earnings
$query = "SELECT SUM(amount) AS total_earnings FROM vendor_withdrawals WHERE status='approved' AND approval_date BETWEEN :start_date AND :end_date";
$stmt = $pdo->prepare($query);
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$total_earnings = $stmt->fetch(PDO::FETCH_ASSOC)['total_earnings'] ?? 0;

// Get pending withdrawals
$query = "SELECT COUNT(*) AS pending_withdrawals FROM vendor_withdrawals WHERE status='pending'";
$stmt = $pdo->query($query);
$pending_withdrawals = $stmt->fetch(PDO::FETCH_ASSOC)['pending_withdrawals'] ?? 0;

// Get top-selling products
$query = "
    SELECT p.product_name, SUM(o.quantity) AS total_sold 
    FROM order_items o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.order_date BETWEEN :start_date AND :end_date
    GROUP BY o.product_id
    ORDER BY total_sold DESC
    LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top-earning vendors
$query = "
    SELECT v.business_name, SUM(o.total_price) AS vendor_earnings 
    FROM orders o
    JOIN vendors v ON o.vendor_id = v.vendor_id
    WHERE o.order_date BETWEEN :start_date AND :end_date
    GROUP BY o.vendor_id
    ORDER BY vendor_earnings DESC
    LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$top_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="mt-4">Admin Reports</h4>
            
            <!-- Date Filter -->
            <form method="GET" class="mb-4">
                <div class="form-row">
                    <div class="col">
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col">
                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Report Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Sales</h5>
                            <h3>$<?= number_format($total_sales, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Vendor Earnings</h5>
                            <h3>$<?= number_format($total_earnings, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Pending Withdrawals</h5>
                            <h3><?= $pending_withdrawals; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top-Selling Products -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    Top-Selling Products
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Total Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['product_name']); ?></td>
                                    <td><?= $product['total_sold']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($top_products)): ?>
                                <tr><td colspan="2" class="text-center">No sales data available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top-Earning Vendors -->
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    Top-Earning Vendors
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Vendor Name</th>
                                <th>Total Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_vendors as $vendor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                                    <td>$<?= number_format($vendor['vendor_earnings'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($top_vendors)): ?>
                                <tr><td colspan="2" class="text-center">No vendor earnings available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
