<?php
/**
 * GlobeTrek Adventures - Dynamic Travel Guides Showcase Page
 */
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM tour_guides ORDER BY id ASC");
    $guides = $stmt->fetchAll();
} catch (PDOException $e) {
    $guides = [];
    $error_msg = "Error fetching tour guides: " . $e->getMessage();
}
?>

<!-- Title & Hero Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); margin-bottom: 2rem; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
    <div class="container">
        <h1 class="fw-extrabold text-white mb-2"><i class="bi bi-person-badge me-2"></i>Our Tour Guides</h1>
        <p class="lead text-success-subtle mb-0">Certified, multi-lingual local experts to guide your journey</p>
    </div>
</div>

<div class="container py-4">
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (empty($guides)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No tour guides currently listed in the catalog.</p>
            </div>
        <?php else: ?>
            <?php foreach ($guides as $guide): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <?php 
                        $img = !empty($guide['image']) ? $guide['image'] : 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=600&q=80';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-100 rounded-top shadow-sm object-fit-cover" alt="<?php echo htmlspecialchars($guide['name']); ?>" style="height: 14rem;" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' fill=\'%23e2e8f0\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'10\' fill=\'%2364748b\'>Image Error</text></svg>';">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Certified Guide</span>
                                <span class="text-warning fw-bold small"><i class="bi bi-star-fill me-1"></i><?php echo number_format($guide['rating'], 2); ?></span>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-1"><?php echo htmlspecialchars($guide['name']); ?></h5>
                            <p class="small text-secondary mb-3"><i class="bi bi-translate text-success me-1"></i>Spoken: <strong><?php echo htmlspecialchars($guide['languages']); ?></strong></p>
                            <p class="card-text text-muted flex-grow-1 small">Friendly, highly rated, and knowledgeable guide specializing in cultural, eco-tourism, and historical sites in Sri Lanka.</p>
                            <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">From Rs <?php echo number_format($guide['price_per_day'], 2); ?> / Day</span>
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
