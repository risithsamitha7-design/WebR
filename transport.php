<?php
/**
 * GlobeTrek Adventures - Dynamic Transportation Options Showcase Page
 */
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id ASC");
    $vehicles = $stmt->fetchAll();
} catch (PDOException $e) {
    $vehicles = [];
    $error_msg = "Error fetching vehicles: " . $e->getMessage();
}
?>

<!-- Title & Hero Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); margin-bottom: 2rem; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
    <div class="container">
        <h1 class="fw-extrabold text-white mb-2"><i class="bi bi-car-front me-2"></i>Transportation Services</h1>
        <p class="lead text-success-subtle mb-0">Safe, reliable, and comfortable island-wide travel options</p>
    </div>
</div>

<div class="container py-4">
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (empty($vehicles)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No vehicles currently available in the catalog.</p>
            </div>
        <?php else: ?>
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <?php 
                        $img = !empty($vehicle['image']) ? $vehicle['image'] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-100 rounded-top shadow-sm object-fit-cover" alt="<?php echo htmlspecialchars($vehicle['model']); ?>" style="height: 12rem;" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' fill=\'%23e2e8f0\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'10\' fill=\'%2364748b\'>Image Error</text></svg>';">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-start mb-2"><?php echo htmlspecialchars($vehicle['type']); ?></span>
                            <h5 class="card-title fw-bold text-dark"><?php echo htmlspecialchars($vehicle['model']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($vehicle['description']); ?></p>
                            <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">From Rs <?php echo number_format($vehicle['price_per_day'], 2); ?> / Day</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
