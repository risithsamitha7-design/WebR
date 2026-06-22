<?php
/**
 * GlobeTrek Central Portal - Admin Dashboard Overview & Reports
 * Renders high-level key performance metrics (KPIs) and operational stats with Date Filtering.
 * Supports native browser printing with optimized corporate document layouts.
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

// Parse and validate date filters
$start_date = trim($_GET['start_date'] ?? '');
$end_date = trim($_GET['end_date'] ?? '');

$date_query = "";
$date_params = [];

if (!empty($start_date) && !empty($end_date)) {
    $date_query = " AND DATE(b.created_at) BETWEEN :start_date AND :end_date";
    $date_params[':start_date'] = $start_date;
    $date_params[':end_date'] = $end_date;
}

try {
    // 1. Fetch Customers Count
    $user_params = [];
    $user_query = "SELECT COUNT(*) FROM users WHERE role = 'customer'";
    if (!empty($start_date) && !empty($end_date)) {
        $user_query .= " AND DATE(created_at) BETWEEN :start_date AND :end_date";
        $user_params[':start_date'] = $start_date;
        $user_params[':end_date'] = $end_date;
    }
    $users_stmt = $pdo->prepare($user_query);
    $users_stmt->execute($user_params);
    $users_count = $users_stmt->fetchColumn();

    // 2. Fetch Total Bookings
    $bookings_query = "SELECT COUNT(*) FROM bookings b WHERE 1=1" . $date_query;
    $bookings_stmt = $pdo->prepare($bookings_query);
    $bookings_stmt->execute($date_params);
    $bookings_count = $bookings_stmt->fetchColumn();

    // 3. Fetch Successful Bookings (Confirmed/Awaiting Payment)
    $success_query = "SELECT COUNT(*) FROM bookings b WHERE (b.status = 'confirmed' OR b.status = 'awaiting_payment')" . $date_query;
    $success_stmt = $pdo->prepare($success_query);
    $success_stmt->execute($date_params);
    $success_bookings_count = $success_stmt->fetchColumn();

    // 4. Fetch Cancelled Bookings
    $cancelled_query = "SELECT COUNT(*) FROM bookings b WHERE b.status = 'cancelled'" . $date_query;
    $cancelled_stmt = $pdo->prepare($cancelled_query);
    $cancelled_stmt->execute($date_params);
    $cancelled_bookings_count = $cancelled_stmt->fetchColumn();

    // 5. Fetch Confirmed Revenue
    $revenue_query = "SELECT SUM(b.total_price) FROM bookings b WHERE b.status = 'confirmed'" . $date_query;
    $revenue_stmt = $pdo->prepare($revenue_query);
    $revenue_stmt->execute($date_params);
    $revenue = $revenue_stmt->fetchColumn() ?? 0.0;

    // 6. Fetch Unread Queries
    $unread_queries = $pdo->query("SELECT COUNT(*) FROM queries WHERE status='unread'")->fetchColumn();

    // 7. Fetch Top Performing Packages
    $top_packages_query = "SELECT p.id, p.title, p.destination, COUNT(b.id) AS booking_count, SUM(CASE WHEN b.status='confirmed' THEN b.total_price ELSE 0 END) as revenue
        FROM packages p
        LEFT JOIN bookings b ON p.id = b.package_id " . (empty($date_query) ? "" : " AND DATE(b.created_at) BETWEEN :start_date AND :end_date") . "
        GROUP BY p.id, p.title, p.destination
        ORDER BY booking_count DESC LIMIT 5";
    $top_packages_stmt = $pdo->prepare($top_packages_query);
    $top_packages_stmt->execute($date_params);
    $top_packages = $top_packages_stmt->fetchAll();

    // 8. Fetch Detailed Transactions (Individual Reports)
    $transactions_query = "SELECT b.id, b.contact_name, p.title AS package_title, b.total_price, b.status, b.created_at
        FROM bookings b
        JOIN packages p ON b.package_id = p.id
        WHERE 1=1 " . $date_query . "
        ORDER BY b.id DESC";
    $transactions_stmt = $pdo->prepare($transactions_query);
    $transactions_stmt->execute($date_params);
    $transactions = $transactions_stmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Admin query error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $users_count = $bookings_count = $success_bookings_count = $cancelled_bookings_count = $revenue = $unread_queries = 0;
    $top_packages = $transactions = [];
}
?>

<!-- Printing Stylesheets Override Block -->
<style>
    @media print {
        /* Hide layout sidebars, buttons, form filters, header navbars, mouse indicators, welcome alerts */
        .navbar,
        .dashboard-sidebar,
        .no-print,
        .alert,
        .btn,
        form,
        header,
        footer,
        .mouse-scroll-indicator,
        .profile-pill {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .container, .card, main {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        /* Formal printable header block */
        .print-header-block {
            display: block !important;
            text-align: center;
            border-bottom: 3px double #1b5e20;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .print-header-block h1 {
            color: #1b5e20 !important;
            font-size: 26pt !important;
            font-weight: 800;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .print-header-block p {
            margin: 0;
            font-size: 10pt;
            color: #4a5568;
        }

        /* Table styling for neat alignment on paper */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 15px !important;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #1a202c !important;
            padding: 8px 10px !important;
            text-align: left;
        }

        th {
            background-color: #f7fafc !important;
            color: #000000 !important;
            font-weight: bold;
        }
    }
</style>

<!-- Dynamic Corporate Print Header Block -->
<div class="d-none print-header-block">
    <h1>GlobeTrek Adventures</h1>
    <p>128 Lewis Place, Negombo, Sri Lanka | Info: info@globetrekadventures.com</p>
    <h3 class="fw-bold mt-4" style="font-size: 16pt;">Official Analytical Business Report</h3>
    <p class="mt-2 text-secondary">
        <?php if (!empty($start_date) && !empty($end_date)): ?>
            Reporting Range: <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?>
        <?php else: ?>
            Reporting Range: Cumulative Analytics (All-Time)
        <?php endif; ?>
    </p>
    <p class="text-secondary small">Generated on: <?php echo date('F d, Y - h:i A'); ?></p>
</div>

<!-- Date Filter Form Console -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4 no-print">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-funnel-fill text-success me-2"></i>Filter Report Timeline</h5>
    <form action="dashboard.php" method="GET" class="row g-3 align-items-end">
        <input type="hidden" name="tab" value="overview">
        <div class="col-md-4">
            <label for="start_date" class="form-label small text-secondary fw-semibold">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label small text-secondary fw-semibold">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-success w-100 py-2 rounded-pill"><i class="bi bi-filter me-1"></i>Apply Filters</button>
            <?php if (!empty($start_date) || !empty($end_date)): ?>
                <a href="dashboard.php?tab=overview" class="btn btn-outline-secondary py-2 px-3 rounded-pill" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="text-secondary small fw-bold">Confirmed Revenue</span>
            <h3 class="stat-number text-success">Rs <?php echo number_format($revenue, 2); ?></h3>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #0dcaf0;">
            <span class="text-secondary small fw-bold">Total Bookings</span>
            <h3 class="stat-number"><?php echo $bookings_count; ?></h3>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #28a745;">
            <span class="text-secondary small fw-bold">Successful Bookings</span>
            <h3 class="stat-number text-success"><?php echo $success_bookings_count; ?></h3>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #dc3545;">
            <span class="text-secondary small fw-bold">Cancelled / Rejected</span>
            <h3 class="stat-number text-danger"><?php echo $cancelled_bookings_count; ?></h3>
        </div>
    </div>
</div>

<!-- Additional KPI Row (Customers & Queries) -->
<div class="row g-4 mb-4 no-print">
    <div class="col-md-6">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <span class="text-secondary small fw-bold">Registered Customers (Within Range)</span>
            <h3 class="stat-number"><?php echo $users_count; ?></h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card" style="border-left-color: #17a2b8;">
            <span class="text-secondary small fw-bold">Unread Queries (Awaiting Staff)</span>
            <h3 class="stat-number"><?php echo $unread_queries; ?></h3>
        </div>
    </div>
</div>

<!-- Top Packages Breakdown -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Performing Packages</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Package Title</th>
                    <th>Destination</th>
                    <th class="text-center">Bookings Count</th>
                    <th class="text-end">Confirmed Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($top_packages)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No packages recorded within this time window.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($top_packages as $tp): ?>
                        <tr>
                            <td><strong class="text-dark"><?php echo htmlspecialchars($tp['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($tp['destination']); ?></td>
                            <td class="text-center"><?php echo $tp['booking_count']; ?></td>
                            <td class="text-end fw-bold text-success">Rs <?php echo number_format($tp['revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Transactions Table -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-bar-graph-fill text-success me-2"></i>Analytical Reports</h5>
        <button onclick="window.print();" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm no-print">
            <i class="bi bi-printer me-1"></i> Generate PDF Report
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Transaction / Booking ID</th>
                    <th>Customer Name</th>
                    <th>Selected Package</th>
                    <th>Revenue Amount</th>
                    <th>Status</th>
                    <th>Date Logged</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No transactions logged within this timeframe.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td>#TX-<?php echo str_pad($tx['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td><strong><?php echo htmlspecialchars($tx['contact_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($tx['package_title']); ?></td>
                            <td class="fw-bold text-success">Rs <?php echo number_format($tx['total_price'], 2); ?></td>
                            <td>
                                <span class="badge text-capitalize <?php 
                                    echo ($tx['status'] === 'confirmed') ? 'bg-success' : 
                                         (($tx['status'] === 'awaiting_payment') ? 'bg-info text-dark' : 
                                         (($tx['status'] === 'cancelled') ? 'bg-danger' : 'bg-secondary')); 
                                ?>">
                                    <?php echo $tx['status']; ?>
                                </span>
                            </td>
                            <td><span class="text-secondary small"><?php echo date('Y-m-d', strtotime($tx['created_at'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
