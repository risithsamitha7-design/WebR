<?php
/**
 * GlobeTrek Central Portal - Manage Custom Itineraries View (Staff & Admin Shared)
 * Lists all customized travel plans requested by customers, allows status updates.
 */
require_once __DIR__ . '/../../config.php';

try {
    $custom_plans_list = $pdo->query("
        SELECT c.*, u.name AS user_name, u.email AS user_email, u.contact_number
        FROM custom_itineraries c
        JOIN users u ON c.user_id = u.id
        ORDER BY c.id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
    $custom_plans_list = [];
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-sliders2 text-success me-2"></i>Custom Travel Plan Requests</h4>
    
    <?php if (empty($custom_plans_list)): ?>
        <p class="text-muted text-center py-4">No customized travel plan requests submitted yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Destination / Duration</th>
                        <th>Travel Date & Budget</th>
                        <th>Preferences</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($custom_plans_list as $plan): ?>
                        <tr>
                            <td>#<?php echo $plan['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($plan['user_name']); ?></strong>
                                <div class="text-muted small"><?php echo htmlspecialchars($plan['user_email']); ?></div>
                                <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($plan['contact_number']); ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($plan['destination']); ?></strong>
                                <div class="text-secondary small mt-1"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($plan['duration']); ?></div>
                            </td>
                            <td>
                                <div class="small"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($plan['travel_date'])); ?></div>
                                <div class="text-success fw-bold small">Rs <?php echo number_format($plan['budget'], 2); ?></div>
                            </td>
                            <td>
                                <?php if (!empty($plan['special_requirements'])): ?>
                                    <div class="text-muted mt-1 small" style="font-size: 0.75rem; border-top: 1px dotted rgba(0,0,0,0.1); padding-top: 2px;" title="<?php echo htmlspecialchars($plan['special_requirements']); ?>">
                                        <strong>Req:</strong> <?php echo htmlspecialchars(mb_strimwidth($plan['special_requirements'], 0, 35, '...')); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plan['status'] === 'pending'): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                        Pending Review
                                    </span>
                                <?php elseif ($plan['status'] === 'reviewed'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                        Approved
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-1.5 rounded-pill small fw-bold">
                                        Cancelled
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php if ($plan['status'] !== 'reviewed'): ?>
                                        <form action="dashboard.php" method="POST">
                                            <input type="hidden" name="action" value="update_custom_plan_status">
                                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                            <input type="hidden" name="status" value="reviewed">
                                            <button type="submit" class="btn btn-success btn-sm w-100 py-1" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>Approve</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($plan['status'] !== 'contacted'): ?>
                                        <form action="dashboard.php" method="POST">
                                            <input type="hidden" name="action" value="update_custom_plan_status">
                                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                            <input type="hidden" name="status" value="contacted">
                                            <button type="submit" class="btn btn-danger btn-sm w-100 py-1" style="font-size: 0.75rem;"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
