<?php
/**
 * GlobeTrek Central Portal - Customer Bookings View
 * Renders the logged-in customer's booking records, payment statuses, and actions
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

// Fetch bookings for this customer with payment details
try {
    $stmt = $pdo->prepare("
        SELECT b.*, p.title, p.destination, p.duration, p.image_url, pm.status AS payment_status, pm.transaction_id, pm.payment_method 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.id 
        LEFT JOIN payments pm ON b.id = pm.booking_id AND pm.status = 'completed'
        WHERE b.user_id = :user_id 
        ORDER BY b.id DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $my_bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading bookings: " . $e->getMessage() . "</div>";
    $my_bookings = [];
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-card-list me-2 text-success"></i>Your Booking Records</h4>
    
    <?php if (empty($my_bookings)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted display-4"></i>
            <h5 class="text-dark mt-3">No Active Bookings</h5>
            <p class="text-muted">You have not requested any travel packages yet. Explore our packages and start your journey!</p>
            <a href="packages.php" class="btn btn-primary mt-2"><i class="bi bi-search me-1"></i> Browse Tour Packages</a>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4 w-100">
            <?php foreach ($my_bookings as $booking): ?>
                <div class="booking-card bg-white border rounded-3 shadow-sm p-4" style="border: 1px solid rgba(0,0,0,0.08) !important;">
                    <!-- Row 1: Header (Section A) -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Booking #<?php echo $booking['id']; ?></h5>
                        </div>
                        <div>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold text-capitalize">
                                    Awaiting Staff Review
                                </span>
                            <?php elseif ($booking['status'] === 'awaiting_payment'): ?>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold text-capitalize">
                                    Approved / Awaiting Payment
                                </span>
                            <?php elseif ($booking['status'] === 'cancelled'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold text-capitalize">
                                    Cancelled
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold text-capitalize">
                                    Confirmed
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Row 2: Details Grid -->
                    <div class="row g-4">
                        <!-- Package Details & Image -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex gap-3">
                                <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <strong class="d-block text-dark fs-6 mb-1 text-wrap"><?php echo htmlspecialchars($booking['title']); ?></strong>
                                    <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($booking['destination']); ?></div>
                                    <div class="small text-secondary">
                                        <i class="bi bi-moon-stars text-warning me-1"></i><strong>Duration:</strong> <?php echo htmlspecialchars($booking['duration']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline & Specifications -->
                        <div class="col-md-6 col-lg-3">
                            <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Preferences & Timeline</h6>
                            <div class="meta-info-container text-secondary" style="font-size: 0.85rem; opacity: 0.85;">
                                <div class="mb-1">
                                    <i class="bi bi-calendar-event text-warning me-1"></i><strong>Arr:</strong> <?php echo date('M d, Y', strtotime($booking['arrival_date'])); ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-calendar-event text-danger me-1"></i><strong>Dep:</strong> <?php echo date('M d, Y', strtotime($booking['departure_date'])); ?>
                                </div>

                                <?php if (($booking['guide_requirement'] ?? 0)): ?>
                                    <div class="mb-1 text-info"><i class="bi bi-person-badge-fill me-1"></i>Guide Requested</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Booking Size & Details -->
                        <div class="col-md-6 col-lg-3">
                            <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Travel Group & Details</h6>
                            <div class="meta-info-container text-secondary" style="font-size: 0.85rem; opacity: 0.85;">
                                <div class="mb-1">
                                    <i class="bi bi-people text-success me-1"></i>Guests: <strong><?php echo $booking['num_adults']; ?> Ad</strong><?php 
                                    if ($booking['num_children_older'] > 0) {
                                        echo ", " . $booking['num_children_older'] . " Ch (6-11)";
                                    }
                                    if ($booking['num_children_younger'] > 0) {
                                        echo ", " . $booking['num_children_younger'] . " Ch (<5)";
                                    }
                                    ?>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-door-closed text-primary me-1"></i>Rooms: <strong><?php echo $booking['num_rooms']; ?></strong>
                                </div>
                                

                                
                                <?php if (!empty($booking['special_requests'])): ?>
                                    <div class="font-monospace mt-2 pt-1 border-top" style="font-size: 0.75rem;" title="<?php echo htmlspecialchars($booking['special_requests']); ?>">
                                        <i class="bi bi-person-gear me-1"></i>Req: <?php echo htmlspecialchars(mb_strimwidth($booking['special_requests'], 0, 40, '...')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Financial Summary & Actions -->
                        <div class="col-md-6 col-lg-2 d-flex flex-column justify-content-between align-items-md-end text-md-end">
                            <div>
                                <h6 class="fw-bold text-secondary mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Financials</h6>
                                <div class="fs-4 fw-bold text-success mb-2">Rs <?php echo number_format($booking['total_price'], 2); ?></div>
                                <div class="mb-3">
                                    <?php if ($booking['payment_status'] === 'completed'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="bi bi-patch-check-fill me-1"></i>Paid
                                        </span>
                                        <div class="text-muted mt-1 small font-monospace" style="font-size: 0.7rem;"><?php echo htmlspecialchars($booking['transaction_id']); ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="bi bi-x-circle-fill me-1"></i>Unpaid
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="w-100">
                                <?php if ($booking['status'] === 'awaiting_payment' && $booking['payment_status'] !== 'completed'): ?>
                                    <a href="payment.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-success btn-sm w-100 py-2 fw-bold mb-2 text-white" style="font-size: 0.85rem; background-color: var(--primary-color); border-color: var(--primary-color);">
                                        <i class="bi bi-credit-card-2-front me-1"></i>Pay Now (Card Payment)
                                    </a>
                                <?php endif; ?>
                                <?php if ($booking['status'] === 'pending' && $booking['payment_status'] !== 'completed'): ?>
                                    <a href="dashboard.php?action=cancel_booking&booking_id=<?php echo $booking['id']; ?>" class="btn btn-outline-danger btn-sm w-100 py-2 fw-bold" style="font-size: 0.85rem;" onclick="return confirm('Are you sure you want to cancel this booking request?');">
                                        <i class="bi bi-x-circle me-1"></i>Cancel Request
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
