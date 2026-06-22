<?php
/**
 * GlobeTrek Central Portal - Customer Inquiries & Queries View (Staff & Admin Shared)
 * Renders support requests submitted by visitors with responsive query status updates
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

try {
    // Fetch all visitor queries
    $queries_list = $pdo->query("SELECT * FROM queries ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading queries: " . $e->getMessage() . "</div>";
    $queries_list = [];
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-envelope-paper-heart-fill text-success me-2"></i>Customer Inquiries & Queries</h4>
    <?php if (empty($queries_list)): ?>
        <p class="text-muted text-center py-4">No customer queries logged yet.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($queries_list as $q): ?>
                <div class="col-12">
                    <div class="border rounded-3 p-4 <?php echo ($q['status'] === 'unread') ? 'bg-light border-success border-start border-4' : 'bg-white'; ?>">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <span class="badge text-capitalize <?php echo ($q['status'] === 'unread') ? 'bg-danger' : 'bg-secondary'; ?>">
                                <?php echo $q['status']; ?>
                            </span>
                            <span class="text-muted small"><i class="bi bi-clock me-1"></i><?php echo date('M d, Y - h:i A', strtotime($q['created_at'])); ?></span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($q['subject']); ?></h5>
                        <p class="small text-muted mb-3">
                            From: <strong><?php echo htmlspecialchars($q['name']); ?></strong> (<code><?php echo htmlspecialchars($q['email']); ?></code>)
                        </p>
                        <div class="bg-white border rounded p-3 text-secondary mb-3 small font-monospace" style="white-space: pre-wrap;"><?php echo htmlspecialchars($q['message']); ?></div>
                        
                        <?php if ($q['status'] === 'unread'): ?>
                            <a href="dashboard.php?action=mark_query_replied&query_id=<?php echo $q['id']; ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-envelope-check me-1"></i> Mark as Replied
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
