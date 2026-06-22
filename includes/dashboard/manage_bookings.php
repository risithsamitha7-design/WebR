<?php
/**
 * GlobeTrek Central Portal - Manage Bookings View (Staff & Admin Shared)
 * Lists all client travel bookings, payment statuses, and allows coordination of hotel/transport logistics
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

try {
    // Fetch Bookings with User, Package, and Payment details
    $bookings_list = $pdo->query("
        SELECT b.id, b.user_id, b.package_id, b.arrival_date, b.departure_date, b.num_adults, 
               b.num_children_older, b.num_children_younger, b.num_rooms, 
               b.guide_requirement,
               b.special_requests, b.contact_name, b.contact_email, b.contact_phone, 
               b.contact_country, b.status, b.total_price,
               p.title AS pkg_title, u.name AS user_name, u.email AS user_email, 
               pm.status AS payment_status, pm.transaction_id, pm.payment_method 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.id 
        JOIN users u ON b.user_id = u.id 
        LEFT JOIN payments pm ON b.id = pm.booking_id AND pm.status = 'completed'
        ORDER BY b.id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
    $bookings_list = [];
}
?>

<style>
    /* 1. Comfortable Cell Spacing */
    .custom-table th, 
    .custom-table td {
        padding: 1.25rem 1rem !important;
    }
    
    /* 2. Smaller Metadata Layout Style */
    .meta-info-container {
        font-size: 0.85rem;
        opacity: 0.85;
        line-height: 1.4;
    }
    
    /* 3. High-Contrast Pill Badges */
    .badge-pill-status {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 50rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
    }
    .badge-pill-status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }
    .badge-pill-status-confirmed {
        background-color: #d1fae5;
        color: #065f46;
        border-color: #a7f3d0;
    }
    .badge-pill-status-awaiting_payment {
        background-color: #dbeafe;
        color: #1e40af;
        border-color: #bfdbfe;
    }
    .badge-pill-status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }
</style>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-calendar2-check-fill text-success me-2"></i>Manage Bookings</h4>
    <?php if (empty($bookings_list)): ?>
        <p class="text-muted text-center py-4">No booking requests submitted yet.</p>
    <?php else: ?>
        <div class="d-flex flex-column gap-4 w-100">
            <?php foreach ($bookings_list as $bk): ?>
                <div class="booking-card bg-white border rounded-3 shadow-sm p-4" style="border: 1px solid rgba(0,0,0,0.08) !important;">
                    <!-- Row 1: Header (Section A) -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Booking #<?php echo $bk['id']; ?></h5>
                        </div>
                        <div>
                            <span class="badge-pill-status badge-pill-status-<?php echo $bk['status']; ?> text-capitalize">
                                <?php echo str_replace('_', ' ', $bk['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Row 2: Details Grid -->
                    <div class="row g-4">
                        <!-- Customer Details (Section B) -->
                        <div class="col-md-6 col-lg-3">
                            <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Customer Profile</h6>
                            <strong class="d-block text-dark fs-6 mb-1"><?php echo htmlspecialchars($bk['contact_name']); ?></strong>
                            <div class="meta-info-container text-secondary">
                                <div class="text-truncate mb-1" title="<?php echo htmlspecialchars($bk['contact_email']); ?>">
                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($bk['contact_email']); ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($bk['contact_phone']); ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-globe me-1"></i><?php echo htmlspecialchars($bk['contact_country']); ?>
                                </div>
                                <div class="mt-2 pt-1 border-top text-muted" style="font-size: 0.8rem;">
                                    Acc: <?php echo htmlspecialchars($bk['user_name']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Package & Timeline (Section C) -->
                        <div class="col-md-6 col-lg-3">
                            <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Package & Timeline</h6>
                            <strong class="d-block text-dark fs-6 mb-1 text-wrap"><?php echo htmlspecialchars($bk['pkg_title']); ?></strong>
                            <div class="meta-info-container text-secondary">
                                <div class="mb-1">
                                    <i class="bi bi-people text-success me-1"></i>Guests: <strong><?php echo $bk['num_adults']; ?> Ad</strong><?php 
                                    if ($bk['num_children_older'] > 0) {
                                        echo ", " . $bk['num_children_older'] . " Ch (6-11)";
                                    }
                                    if ($bk['num_children_younger'] > 0) {
                                        echo ", " . $bk['num_children_younger'] . " Ch (<5)";
                                    }
                                    ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-door-closed text-primary me-1"></i>Rooms: <strong><?php echo $bk['num_rooms']; ?></strong>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-calendar-event text-warning me-1"></i><strong>Arr:</strong> <?php echo date('M d, Y', strtotime($bk['arrival_date'])); ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-calendar-event text-danger me-1"></i><strong>Dep:</strong> <?php echo date('M d, Y', strtotime($bk['departure_date'])); ?>
                                </div>
                                <div class="mt-2 text-muted" style="font-size: 0.8rem;">
                                    <i class="bi bi-moon-stars text-warning me-1"></i>
                                    <strong><?php 
                                        $arr = new DateTime($bk['arrival_date']);
                                        $dep = new DateTime($bk['departure_date']);
                                        $nights = $arr->diff($dep)->days;
                                        echo $nights . ($nights === 1 ? ' night' : ' nights');
                                    ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coordination Form (Section D) -->
                        <div class="col-md-6 col-lg-3">
                            <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Coordination</h6>
                            
                            <?php if ($bk['status'] === 'pending'): ?>
                                <form action="dashboard.php" method="POST" class="d-flex flex-column gap-2">
                                    <input type="hidden" name="action" value="update_booking_status">
                                    <input type="hidden" name="booking_id" value="<?php echo $bk['id']; ?>">
                                    <input type="hidden" name="status" value="awaiting_payment">
                                    
                                    <button type="submit" class="btn btn-success btn-sm w-100 py-2 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-check-circle me-1"></i>Approve for Payment</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">No action required.</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Financial Summary & Cancellation Actions (Section E) -->
                        <div class="col-md-6 col-lg-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Financials</h6>
                                <div class="fs-4 fw-bold text-success mb-2">Rs <?php echo number_format($bk['total_price'], 2); ?></div>
                                <div class="mb-3">
                                    <?php if ($bk['payment_status'] === 'completed'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="bi bi-patch-check-fill me-1"></i>Paid
                                        </span>
                                        <div class="text-muted mt-1 small font-monospace"><?php echo htmlspecialchars($bk['transaction_id']); ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="bi bi-x-circle-fill me-1"></i>Unpaid
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div>
                                <?php if ($bk['status'] !== 'cancelled' && $bk['status'] !== 'confirmed'): ?>
                                    <form action="dashboard.php" method="POST">
                                        <input type="hidden" name="action" value="update_booking_status">
                                        <input type="hidden" name="booking_id" value="<?php echo $bk['id']; ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-x-circle me-1"></i>Cancel Booking</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
