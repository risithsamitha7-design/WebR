<?php
/**
 * GlobeTrek Adventures - Dedicated Packages Catalog Page
 * Allows visitors and customers to search, filter, and book travel packages
 */

// Load the header layout (handles database configuration & session)
require_once __DIR__ . '/includes/header.php';

// Fetch packages with search filters
$search_dest = trim($_GET['destination'] ?? '');
$search_min_price = trim($_GET['min_price'] ?? '');
$search_max_price = trim($_GET['max_price'] ?? '');
$sort_by = trim($_GET['sort_by'] ?? 'newest');

$sql = "SELECT * FROM packages WHERE 1=1";
$params = [];

if (!empty($search_dest)) {
    $sql .= " AND (destination LIKE :dest OR title LIKE :title OR description LIKE :desc)";
    $params[':dest'] = '%' . $search_dest . '%';
    $params[':title'] = '%' . $search_dest . '%';
    $params[':desc'] = '%' . $search_dest . '%';
}

if (!empty($search_min_price) && is_numeric($search_min_price)) {
    $sql .= " AND price >= :min_price";
    $params[':min_price'] = (float)$search_min_price;
}

if (!empty($search_max_price) && is_numeric($search_max_price)) {
    $sql .= " AND price <= :max_price";
    $params[':max_price'] = (float)$search_max_price;
}

if ($sort_by === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort_by === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $packages = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading packages: " . $e->getMessage() . "</div>";
    $packages = [];
}
?>

<!-- Title & Hero Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); margin-bottom: 2rem; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
    <div class="container">
        <h1 class="fw-extrabold text-white mb-2"><i class="bi bi-compass-fill me-2"></i>Travel Packages Catalog</h1>
        <p class="lead text-success-subtle mb-0">Search and book your perfect Sri Lankan getaway</p>
    </div>
</div>

<!-- Search Container -->
<div class="container mb-5">
    <div class="search-container bg-white p-4 shadow-sm rounded-4">
        <form action="packages.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="destination" class="form-label text-secondary fw-semibold small">Destination or Key Terms</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control bg-light" id="destination" name="destination" placeholder="e.g. Ella, Sigiriya..." value="<?php echo htmlspecialchars($search_dest); ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <label for="min_price" class="form-label text-secondary fw-semibold small">Min Price (Rs)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-currency-dollar"></i></span>
                    <input type="number" class="form-control bg-light" id="min_price" name="min_price" placeholder="Min" min="0" step="1000" value="<?php echo htmlspecialchars($search_min_price); ?>">
                </div>
            </div>

            <div class="col-md-2">
                <label for="max_price" class="form-label text-secondary fw-semibold small">Max Price (Rs)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-currency-dollar"></i></span>
                    <input type="number" class="form-control bg-light" id="max_price" name="max_price" placeholder="Max" min="0" step="1000" value="<?php echo htmlspecialchars($search_max_price); ?>">
                </div>
            </div>

            <div class="col-md-3">
                <label for="sort_by" class="form-label text-secondary fw-semibold small">Sort By</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-sort-down"></i></span>
                    <select class="form-select bg-light" id="sort_by" name="sort_by">
                        <option value="newest" <?php echo ($sort_by === 'newest') ? 'selected' : ''; ?>>Newest First</option>
                        <option value="price_asc" <?php echo ($sort_by === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo ($sort_by === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <?php if (!empty($search_dest) || !empty($search_min_price) || !empty($search_max_price) || $sort_by !== 'newest'): ?>
                        <a href="packages.php" class="btn btn-outline-secondary py-2.5 px-3" title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tour Packages Grid Section -->
<section id="packages-list" class="container py-4">
    <?php if (empty($packages)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm border border-light">
            <i class="bi bi-search-heart text-muted display-1"></i>
            <h4 class="text-dark mt-3">No Tours Found</h4>
            <p class="text-muted">We couldn't find any tour packages matching your filter. Try adjusting your search keywords or price limit.</p>
            <a href="packages.php" class="btn btn-outline-success mt-2">Reset Filters</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($packages as $pkg): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <div class="package-img-container">
                            <img src="<?php echo htmlspecialchars($pkg['image_url'] ?? 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80'); ?>" class="package-img" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                            <span class="package-tag"><i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($pkg['duration']); ?></span>
                            <span class="package-price-tag">Rs <?php echo number_format($pkg['price'], 2); ?></span>
                        </div>
                        <div class="package-body">
                            <div class="package-dest">
                                <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($pkg['destination']); ?>
                            </div>
                            <h4 class="package-title"><?php echo htmlspecialchars($pkg['title']); ?></h4>
                            <p class="package-desc"><?php echo htmlspecialchars(substr($pkg['description'], 0, 140)) . (strlen($pkg['description']) > 140 ? '...' : ''); ?></p>
                            
                            <!-- Integrated Baseline Logistics Showcase -->
                            <div class="bg-light p-2.5 rounded-3 mb-3 border border-light-subtle" style="font-size: 0.8rem;">
                                <div class="text-secondary fw-bold mb-1.5" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;"><i class="bi bi-gift-fill text-success me-1"></i>Baseline Inclusions:</div>
                                <div class="d-flex flex-wrap gap-1.5">
                                    <span class="badge bg-white text-secondary border d-flex align-items-center"><i class="bi bi-building text-success me-1"></i>Hotel Opts</span>
                                    <span class="badge bg-white text-secondary border d-flex align-items-center"><i class="bi bi-car-front text-success me-1"></i>Private Transfer</span>
                                    <span class="badge bg-white text-secondary border d-flex align-items-center"><i class="bi bi-map text-success me-1"></i>Expert Guide</span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="book.php?package_id=<?php echo $pkg['id']; ?>" class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-event me-2"></i>Book This Tour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
// Load the footer layout
require_once __DIR__ . '/includes/footer.php';
?>
