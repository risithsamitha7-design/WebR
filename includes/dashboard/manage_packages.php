<?php
/**
 * GlobeTrek Central Portal - Manage Packages View (Staff & Admin Shared)
 * Provides travel catalog management, including travel details edit modals and new catalog entry forms
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

try {
    // Fetch packages
    $packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading packages: " . $e->getMessage() . "</div>";
    $packages = [];
}
?>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-success"></i>Travel Packages Catalog</h4>
        <a href="add_package.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Package
        </a>
    </div>

    <div class="table-responsive">
        <table class="table custom-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Destination</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                <strong class="text-dark"><?php echo htmlspecialchars($pkg['title']); ?></strong>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($pkg['destination']); ?></td>
                        <td><?php echo htmlspecialchars($pkg['duration']); ?></td>
                        <td><strong class="text-success">Rs <?php echo number_format($pkg['price'], 2); ?></strong></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="edit_package.php?id=<?php echo $pkg['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="dashboard.php?action=delete_package&package_id=<?php echo $pkg['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this package? This will delete all connected bookings.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
