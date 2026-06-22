<?php
/**
 * GlobeTrek Adventures - Dynamic Accommodation Options Showcase Page
 */
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM hotels ORDER BY id ASC");
    $hotels = $stmt->fetchAll();
} catch (PDOException $e) {
    $hotels = [];
    $error_msg = "Error fetching accommodations: " . $e->getMessage();
}
?>

<!-- Title & Hero Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); margin-bottom: 2rem; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
    <div class="container">
        <h1 class="fw-extrabold text-white mb-2"><i class="bi bi-building me-2"></i>Accommodation Options</h1>
        <p class="lead text-success-subtle mb-0">Discover our handpicked premium and budget hotel partners across Sri Lanka</p>
    </div>
</div>

<div class="container py-4">
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (empty($hotels)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No accommodations currently available in the catalog.</p>
            </div>
        <?php else: ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <?php 
                        $img = !empty($hotel['image']) ? $hotel['image'] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=600&q=80';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-100 rounded-top shadow-sm object-fit-cover" alt="<?php echo htmlspecialchars($hotel['name']); ?>" style="height: 12rem;" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' fill=\'%23e2e8f0\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'10\' fill=\'%2364748b\'>Image Error</text></svg>';">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-start mb-2"><?php echo htmlspecialchars($hotel['type']); ?></span>
                            <h5 class="card-title fw-bold text-dark"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($hotel['description']); ?></p>
                            <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">From Rs <?php echo number_format($hotel['price_per_night'], 2); ?> / Night</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Booking CTA Banner -->
    <div class="card border-0 rounded-4 mt-5 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);">
        <div class="card-body p-5 text-center">
            <h3 class="fw-bold text-dark mb-3">Looking for Custom Hotel Coordination?</h3>
            <p class="text-muted max-w-2xl mx-auto mb-4">When you book travel packages with us, our staff coordinates hotel bookings that match your preference perfectly. You can also customize your travel plan completely!</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="packages.php" class="btn btn-success px-4 py-2 rounded-pill"><i class="bi bi-compass me-2"></i>Browse Packages</a>
                <a href="dashboard.php" class="btn btn-outline-success px-4 py-2 rounded-pill"><i class="bi bi-person me-2"></i>Custom Itinerary Portal</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
