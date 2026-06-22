<?php
/**
 * GlobeTrek Central Portal - Manage Services View (Staff & Admin Shared)
 * Allows authorized staff and administrators to add/delete hotels, vehicles, and tour guides.
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    echo "<div class='alert alert-danger'>Access Denied.</div>";
    exit();
}

try {
    $hotels_list = $pdo->query("SELECT * FROM hotels ORDER BY id DESC")->fetchAll();
    $vehicles_list = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll();
    $guides_list = $pdo->query("SELECT * FROM tour_guides ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
    $hotels_list = $vehicles_list = $guides_list = [];
}
?>

<div class="row g-4">
    <!-- 1. Hotels Management Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-building text-success me-2"></i>Manage Hotels</h4>
                <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#addHotelCollapse">
                    <i class="bi bi-plus-circle me-1"></i>Add New Hotel
                </button>
            </div>

            <!-- Collapse Form to Add Hotel -->
            <div class="collapse mb-4" id="addHotelCollapse">
                <div class="card card-body bg-light border-0 rounded-3 p-4">
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="action" value="add_hotel">
                        <div class="col-md-6">
                            <label for="hotel_name" class="form-label small fw-bold">Hotel Name *</label>
                            <input type="text" name="name" id="hotel_name" class="form-control" placeholder="e.g. Grand Negombo Beach Resort" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hotel_type" class="form-label small fw-bold">Type/Category *</label>
                            <input type="text" name="type" id="hotel_type" class="form-control" placeholder="e.g. Luxury & Spa" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hotel_price" class="form-label small fw-bold">Price per Night (Rs) *</label>
                            <input type="number" name="price_per_night" id="hotel_price" class="form-control" min="100" placeholder="e.g. 18000" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hotel_image" class="form-label small fw-bold">Hotel Image *</label>
                            <input type="file" name="image" id="hotel_image" class="form-control" accept="image/*" required>
                            <div class="form-text mt-1 text-secondary" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Recommended size: 10KB to 2MB. Allowed: JPG, JPEG, PNG, WEBP, GIF.
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="hotel_desc" class="form-label small fw-bold">Description *</label>
                            <textarea name="description" id="hotel_desc" class="form-control" rows="3" placeholder="Hotel summary details..." required></textarea>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-success rounded-pill px-4">Submit Hotel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price / Night</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($hotels_list)): ?>
                            <tr>
                                <td colspan="5" class="text-muted text-center py-3">No hotels currently in the lookup table.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($hotels_list as $hotel): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($hotel['image'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=80'); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($hotel['name']); ?></strong></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($hotel['type']); ?></span></td>
                                    <td><strong class="text-success">Rs <?php echo number_format($hotel['price_per_night'], 2); ?></strong></td>
                                    <td class="text-end">
                                        <a href="dashboard.php?action=delete_hotel&id=<?php echo $hotel['id']; ?>" class="btn btn-outline-danger btn-sm rounded-circle p-2" onclick="return confirm('Are you sure you want to delete this hotel?');" title="Delete Hotel">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Vehicles Management Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-car-front-fill text-success me-2"></i>Manage Vehicles</h4>
                <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#addVehicleCollapse">
                    <i class="bi bi-plus-circle me-1"></i>Add New Vehicle
                </button>
            </div>

            <!-- Collapse Form to Add Vehicle -->
            <div class="collapse mb-4" id="addVehicleCollapse">
                <div class="card card-body bg-light border-0 rounded-3 p-4">
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="action" value="add_vehicle">
                        <div class="col-md-6">
                            <label for="veh_model" class="form-label small fw-bold">Vehicle Model *</label>
                            <input type="text" name="model" id="veh_model" class="form-control" placeholder="e.g. Toyota Prius / Axio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="veh_type" class="form-label small fw-bold">Category/Type *</label>
                            <input type="text" name="type" id="veh_type" class="form-control" placeholder="e.g. Private Sedan Car" required>
                        </div>
                        <div class="col-md-6">
                            <label for="veh_price" class="form-label small fw-bold">Price per Day (Rs) *</label>
                            <input type="number" name="price_per_day" id="veh_price" class="form-control" min="100" placeholder="e.g. 8500" required>
                        </div>
                        <div class="col-md-6">
                            <label for="veh_image" class="form-label small fw-bold">Vehicle Image *</label>
                            <input type="file" name="image" id="veh_image" class="form-control" accept="image/*" required>
                            <div class="form-text mt-1 text-secondary" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Recommended size: 10KB to 2MB. Allowed: JPG, JPEG, PNG, WEBP, GIF.
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="veh_desc" class="form-label small fw-bold">Description *</label>
                            <textarea name="description" id="veh_desc" class="form-control" rows="3" placeholder="Vehicle features and details..." required></textarea>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-success rounded-pill px-4">Submit Vehicle</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>Price / Day</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vehicles_list)): ?>
                            <tr>
                                <td colspan="5" class="text-muted text-center py-3">No vehicles currently in the lookup table.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vehicles_list as $vehicle): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($vehicle['image'] ?? 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=80'); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($vehicle['model']); ?></strong></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($vehicle['type']); ?></span></td>
                                    <td><strong class="text-success">Rs <?php echo number_format($vehicle['price_per_day'], 2); ?></strong></td>
                                    <td class="text-end">
                                        <a href="dashboard.php?action=delete_vehicle&id=<?php echo $vehicle['id']; ?>" class="btn btn-outline-danger btn-sm rounded-circle p-2" onclick="return confirm('Are you sure you want to delete this vehicle?');" title="Delete Vehicle">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Tour Guides Management Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge-fill text-success me-2"></i>Manage Tour Guides</h4>
                <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#addGuideCollapse">
                    <i class="bi bi-plus-circle me-1"></i>Add New Guide
                </button>
            </div>

            <!-- Collapse Form to Add Guide -->
            <div class="collapse mb-4" id="addGuideCollapse">
                <div class="card card-body bg-light border-0 rounded-3 p-4">
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="action" value="add_guide">
                        <div class="col-md-6">
                            <label for="guide_name" class="form-label small fw-bold">Guide Name *</label>
                            <input type="text" name="name" id="guide_name" class="form-control" placeholder="e.g. Asanka Fernando" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guide_languages" class="form-label small fw-bold">Spoken Languages *</label>
                            <input type="text" name="languages" id="guide_languages" class="form-control" placeholder="e.g. English, German, Russian" required>
                        </div>
                        <div class="col-md-4">
                            <label for="guide_price" class="form-label small fw-bold">Price per Day (Rs) *</label>
                            <input type="number" name="price_per_day" id="guide_price" class="form-control" min="100" placeholder="e.g. 4000" required>
                        </div>
                        <div class="col-md-4">
                            <label for="guide_rating" class="form-label small fw-bold">Rating (0.0 - 5.0) *</label>
                            <input type="number" step="0.05" min="1.0" max="5.0" name="rating" id="guide_rating" class="form-control" placeholder="e.g. 4.90" required>
                        </div>
                        <div class="col-md-4">
                            <label for="guide_image" class="form-label small fw-bold">Guide Image *</label>
                            <input type="file" name="image" id="guide_image" class="form-control" accept="image/*" required>
                            <div class="form-text mt-1 text-secondary" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Recommended size: 10KB to 2MB. Allowed: JPG, JPEG, PNG, WEBP, GIF.
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-success rounded-pill px-4">Submit Guide</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Languages</th>
                            <th>Rating</th>
                            <th>Price / Day</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($guides_list)): ?>
                            <tr>
                                <td colspan="6" class="text-muted text-center py-3">No tour guides currently in the lookup table.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($guides_list as $guide): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($guide['image'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80'); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($guide['name']); ?></strong></td>
                                    <td><span class="small text-secondary"><?php echo htmlspecialchars($guide['languages']); ?></span></td>
                                    <td><span class="text-warning fw-bold"><i class="bi bi-star-fill me-1"></i><?php echo number_format($guide['rating'], 2); ?></span></td>
                                    <td><strong class="text-success">Rs <?php echo number_format($guide['price_per_day'], 2); ?></strong></td>
                                    <td class="text-end">
                                        <a href="dashboard.php?action=delete_guide&id=<?php echo $guide['id']; ?>" class="btn btn-outline-danger btn-sm rounded-circle p-2" onclick="return confirm('Are you sure you want to delete this guide?');" title="Delete Guide">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
