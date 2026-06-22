<?php
/**
 * GlobeTrek Central Portal - Customer Custom Itineraries View
 * Lists all customized travel plans requested by the active customer
 */
require_once __DIR__ . '/../../config.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM custom_itineraries WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute([':user_id' => $user_id]);
    $custom_plans = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
    $custom_plans = [];
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-range-fill text-success me-2"></i>My Customized Travel Plans</h4>
        <a href="customize_plan.php" class="btn btn-success btn-sm rounded-pill"><i class="bi bi-plus-circle me-1"></i>Request New Plan</a>
    </div>

    <?php if (empty($custom_plans)): ?>
        <p class="text-muted text-center py-4">You have not requested any custom travel plans yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Travel Date</th>
                        <th>Budget</th>
                        <th>Preferences</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($custom_plans as $plan): ?>
                        <tr>
                            <td>#<?php echo $plan['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($plan['destination']); ?></strong></td>
                            <td><?php echo htmlspecialchars($plan['duration']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($plan['travel_date'])); ?></td>
                            <td><strong class="text-success">Rs <?php echo number_format($plan['budget'], 2); ?></strong></td>
                            <td>
                                <?php if (!empty($plan['special_requirements'])): ?>
                                    <div class="text-muted mt-1 small" title="<?php echo htmlspecialchars($plan['special_requirements']); ?>" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <strong>Req:</strong> <?php echo htmlspecialchars($plan['special_requirements']); ?>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
