<?php
/**
 * GlobeTrek Central Portal - Sales & Analytics Reports View (Admin Only)
 * Renders structured package performance, customer activity audit trail, and payment summaries.
 * Optimized for screen viewing and print formats.
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

try {
    // Confirmed revenue
    $revenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status='confirmed'")->fetchColumn() ?? 0.0;
    // Total bookings count
    $bookings_count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

    // Fetch Package Sales Statistics
    $package_sales = $pdo->query("
        SELECT p.id, p.title, p.destination, p.price,
               COUNT(b.id) AS bookings_count,
               SUM(CASE WHEN b.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
               COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.total_price ELSE 0 END), 0) AS confirmed_revenue
        FROM packages p
        LEFT JOIN bookings b ON p.id = b.package_id
        GROUP BY p.id, p.title, p.destination, p.price
        ORDER BY confirmed_revenue DESC, bookings_count DESC
    ")->fetchAll();

    // Fetch Customer Booking Metrics
    $customer_metrics = $pdo->query("
        SELECT u.id, u.name, u.email, u.contact_number,
               COUNT(b.id) AS bookings_count,
               COALESCE(SUM(pm.amount), 0) AS total_paid
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id
        LEFT JOIN payments pm ON b.id = pm.booking_id AND pm.status = 'completed'
        WHERE u.role = 'customer'
        GROUP BY u.id, u.name, u.email, u.contact_number
        ORDER BY total_paid DESC, bookings_count DESC
    ")->fetchAll();

    // Fetch Payment Methods Summary
    $payment_summary = $pdo->query("
        SELECT payment_method, COUNT(*) AS count, SUM(amount) AS total_amount
        FROM payments
        WHERE status = 'completed'
        GROUP BY payment_method
    ")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Admin query error: " . $e->getMessage() . "</div>";
    $package_sales = $customer_metrics = $payment_summary = [];
    $revenue = $bookings_count = 0;
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 no-print">
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-bar-chart-line-fill text-success me-2"></i>Sales & Customer Reports
        </h4>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Print-Only Header -->
    <div class="d-none d-print-block mb-4 text-center">
        <h2 class="fw-bold text-success mb-1">GlobeTrek Adventures</h2>
        <p class="text-secondary small mb-0">Lewis Place, Negombo, Sri Lanka | Phone: +94 31 222 3456</p>
        <h4 class="fw-bold text-dark mt-4 border-bottom pb-2">Sales, Bookings & Customer Analytics Report</h4>
        <div class="text-muted small mt-2">Generated on: <?php echo date('F d, Y - h:i A'); ?></div>
    </div>

    <!-- Summary Cards for Quick Glance -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 border rounded-3 bg-light-subtle">
                <span class="text-secondary small fw-semibold text-uppercase">Total Revenue</span>
                <h4 class="fw-bold text-success mt-1 mb-0">Rs <?php echo number_format($revenue, 2); ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 bg-light-subtle">
                <span class="text-secondary small fw-semibold text-uppercase">Total Bookings</span>
                <h4 class="fw-bold text-dark mt-1 mb-0"><?php echo $bookings_count; ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 bg-light-subtle">
                <span class="text-secondary small fw-semibold text-uppercase">Active Customers</span>
                <h4 class="fw-bold text-info mt-1 mb-0"><?php echo count($customer_metrics); ?></h4>
            </div>
        </div>
    </div>

    <!-- Report Section 1: Package Performance -->
    <div class="mb-5">
        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-boxes text-success me-2"></i>Package Sales Performance
        </h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Package Title</th>
                        <th>Destination</th>
                        <th>Package Price</th>
                        <th class="text-center">Total Bookings</th>
                        <th class="text-center">Confirmed Sales</th>
                        <th class="text-end">Confirmed Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($package_sales)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No package data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($package_sales as $ps): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ps['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($ps['destination']); ?></td>
                                <td>Rs <?php echo number_format($ps['price'], 2); ?></td>
                                    <td class="text-center"><?php echo $ps['bookings_count']; ?></td>
                                    <td class="text-center"><?php echo $ps['confirmed_count']; ?></td>
                                    <td class="text-end fw-bold text-success">Rs <?php echo number_format($ps['confirmed_revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Section 2: Customer Activity & Value -->
    <div class="mb-5">
        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-people-fill text-success me-2"></i>Customer Activity & Payments Summary
        </h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Contact Number</th>
                        <th class="text-center">Total Bookings</th>
                        <th class="text-end">Total Payments Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customer_metrics)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No customer data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customer_metrics as $cm): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cm['name']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($cm['email']); ?></code></td>
                                <td><?php echo htmlspecialchars($cm['contact_number'] ?: 'N/A'); ?></td>
                                <td class="text-center"><?php echo $cm['bookings_count']; ?></td>
                                <td class="text-end fw-bold text-success">Rs <?php echo number_format($cm['total_paid'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Section 3: Revenue by Payment Method -->
    <div>
        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-credit-card-2-front-fill text-success me-2"></i>Payments Collected by Channel
        </h5>
        <div class="table-responsive" style="max-width: 600px;">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Payment Method</th>
                        <th class="text-center">Transactions</th>
                        <th class="text-end">Total Amount Collected</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payment_summary)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No payments recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $total_collected = 0;
                        foreach ($payment_summary as $pay): 
                            $total_collected += $pay['total_amount'];
                        ?>
                            <tr>
                                <td class="text-capitalize"><?php echo htmlspecialchars($pay['payment_method']); ?></td>
                                <td class="text-center"><?php echo $pay['count']; ?></td>
                                <td class="text-end fw-bold text-success">Rs <?php echo number_format($pay['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-secondary fw-bold">
                            <td>Total Collected</td>
                            <td class="text-center">-</td>
                            <td class="text-end text-success">Rs <?php echo number_format($total_collected, 2); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
