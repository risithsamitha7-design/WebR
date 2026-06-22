<?php
/**
 * GlobeTrek Adventures - Booking Form Page
 * Displays tour package details and handles client booking requests
 */

require_once __DIR__ . '/config.php';

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "You must log in to book a tour.";
    header("Location: login.php");
    exit();
}

// 2. Ensure user has 'customer' role
if ($_SESSION['user_role'] !== 'customer') {
    $_SESSION['flash_error'] = "Only customer accounts can make bookings.";
    header("Location: index.php");
    exit();
}

$package_id = intval($_GET['package_id'] ?? 0);

// 3. Fetch package details
try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id");
    $stmt->execute([':id' => $package_id]);
    $pkg = $stmt->fetch();

    if (!$pkg) {
        $_SESSION['flash_error'] = "Travel package not found.";
        header("Location: packages.php");
        exit();
    }

    // Fetch hotels, vehicles, and guides for select options
    $hotels_opt = $pdo->query("SELECT id, name, price_per_night FROM hotels ORDER BY name ASC")->fetchAll();
    $vehicles_opt = $pdo->query("SELECT id, model, price_per_day FROM vehicles ORDER BY model ASC")->fetchAll();
    $guides_opt = $pdo->query("SELECT id, name, price_per_day FROM tour_guides ORDER BY name ASC")->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Initialize form variables
$arrival_date = '';
$departure_date = '';
$num_adults = 1;
$num_children_older = 0;
$num_children_younger = 0;
$num_rooms = 1;
$guide_required = 0;
$assigned_hotel_id = null;
$assigned_vehicle_id = null;
$assigned_guide_id = null;
$special_requests = '';
$contact_name = $_SESSION['user_name'] ?? '';
$contact_email = $_SESSION['user_email'] ?? '';
$contact_phone = '';
$contact_country = '';

// 4. Handle form submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arrival_date = trim($_POST['arrival_date'] ?? '');
    $departure_date = trim($_POST['departure_date'] ?? '');
    $num_adults = intval($_POST['num_adults'] ?? 1);
    $num_children_older = intval($_POST['num_children_older'] ?? 0);
    $num_children_younger = intval($_POST['num_children_younger'] ?? 0);
    $num_rooms = intval($_POST['num_rooms'] ?? 1);
    $guide_required = isset($_POST['guide_required']) ? 1 : 0;
    $guide_requirement = $guide_required;
    $special_requests = trim($_POST['special_requests'] ?? '');
    
    $assigned_hotel_id = !empty($_POST['assigned_hotel_id']) ? intval($_POST['assigned_hotel_id']) : null;
    $assigned_vehicle_id = !empty($_POST['assigned_vehicle_id']) ? intval($_POST['assigned_vehicle_id']) : null;
    $assigned_guide_id = !empty($_POST['assigned_guide_id']) ? intval($_POST['assigned_guide_id']) : null;
    
    $contact_name = trim($_POST['contact_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $contact_country = trim($_POST['contact_country'] ?? '');

    $errors = [];
    
    // 1. Arrival Date Validation
    if (empty($arrival_date)) {
        $errors[] = "Date of Arrival is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $arrival_date)) {
        $errors[] = "Arrival date must be in YYYY-MM-DD format.";
    } else {
        $today = date('Y-m-d');
        if ($arrival_date <= $today) {
            $errors[] = "Arrival date must be in the future (starting from tomorrow).";
        }
    }

    // 2. Departure Date Validation
    if (empty($departure_date)) {
        $errors[] = "Date of Departure is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $departure_date)) {
        $errors[] = "Departure date must be in YYYY-MM-DD format.";
    } else {
        if (!empty($arrival_date) && $departure_date <= $arrival_date) {
            $errors[] = "Departure date must be after the arrival date.";
        }
    }

    // 3. Guest Counts Validation
    if ($num_adults < 1) {
        $errors[] = "There must be at least 1 adult traveler.";
    } elseif ($num_adults > 30) {
        $errors[] = "Online bookings are limited to 30 adults. Please contact us for larger bookings.";
    }

    if ($num_children_older < 0 || $num_children_younger < 0) {
        $errors[] = "Children count cannot be negative.";
    }

    // 4. Rooms Count Validation
    if ($num_rooms < 1) {
        $errors[] = "At least 1 room must be requested.";
    }

    // 6. Contact Details Validation
    if (empty($contact_name)) {
        $errors[] = "Contact Name is required.";
    } elseif (strlen($contact_name) < 2 || strlen($contact_name) > 150) {
        $errors[] = "Contact Name must be between 2 and 150 characters.";
    }

    if (empty($contact_email)) {
        $errors[] = "Contact Email is required.";
    } elseif (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid contact email address.";
    }

    if (empty($contact_phone)) {
        $errors[] = "Phone / WhatsApp number is required.";
    } elseif (strlen($contact_phone) < 5 || strlen($contact_phone) > 50) {
        $errors[] = "Phone number must be between 5 and 50 characters.";
    }

    if (empty($contact_country)) {
        $errors[] = "Country is required.";
    } elseif (strlen($contact_country) < 2 || strlen($contact_country) > 100) {
        $errors[] = "Country name must be between 2 and 100 characters.";
    }

    // 7. Special Requests Validation
    if (!empty($special_requests) && strlen($special_requests) > 1000) {
        $errors[] = "Special requests details must not exceed 1000 characters.";
    }

    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode("<br>", $errors);
    } else {
        // Calculate total price: Adults pay full, older children pay 50%, plus selected hotel/vehicle/guide per-day rates
        $base_price = floatval($pkg['price']) * ($num_adults + 0.5 * $num_children_older);
        
        // Calculate nights/days
        $arr_dt = new DateTime($arrival_date);
        $dep_dt = new DateTime($departure_date);
        $nights = $arr_dt->diff($dep_dt)->days;
        $days = $nights > 0 ? $nights : 1;

        $hotel_cost = 0;
        if ($assigned_hotel_id) {
            $stmt_h = $pdo->prepare("SELECT price_per_night FROM hotels WHERE id = ?");
            $stmt_h->execute([$assigned_hotel_id]);
            $hotel_cost = floatval($stmt_h->fetchColumn()) * $num_rooms * $days;
        }

        $vehicle_cost = 0;
        if ($assigned_vehicle_id) {
            $stmt_v = $pdo->prepare("SELECT price_per_day FROM vehicles WHERE id = ?");
            $stmt_v->execute([$assigned_vehicle_id]);
            $vehicle_cost = floatval($stmt_v->fetchColumn()) * $days;
        }

        $guide_cost = 0;
        if ($assigned_guide_id) {
            $stmt_g = $pdo->prepare("SELECT price_per_day FROM tour_guides WHERE id = ?");
            $stmt_g->execute([$assigned_guide_id]);
            $guide_cost = floatval($stmt_g->fetchColumn()) * $days;
        }

        $total_price = $base_price + $hotel_cost + $vehicle_cost + $guide_cost;

        try {
            $stmt = $pdo->prepare("
                INSERT INTO bookings (
                    user_id, package_id, arrival_date, departure_date, num_adults, 
                    num_children_older, num_children_younger, num_rooms, 
                    guide_required, guide_requirement,
                    special_requests, contact_name, contact_email, contact_phone, 
                    contact_country, status, total_price,
                    assigned_hotel_id, assigned_vehicle_id, assigned_guide_id
                ) VALUES (
                    :user_id, :package_id, :arrival_date, :departure_date, :num_adults, 
                    :num_children_older, :num_children_younger, :num_rooms, 
                    :guide_required, :guide_requirement,
                    :special_requests, :contact_name, :contact_email, :contact_phone, 
                    :contact_country, 'pending', :total_price,
                    :assigned_hotel_id, :assigned_vehicle_id, :assigned_guide_id
                )
            ");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':package_id' => $package_id,
                ':arrival_date' => $arrival_date,
                ':departure_date' => $departure_date,
                ':num_adults' => $num_adults,
                ':num_children_older' => $num_children_older,
                ':num_children_younger' => $num_children_younger,
                ':num_rooms' => $num_rooms,
                ':guide_required' => $guide_required,
                ':guide_requirement' => $guide_requirement,
                ':special_requests' => $special_requests,
                ':contact_name' => $contact_name,
                ':contact_email' => $contact_email,
                ':contact_phone' => $contact_phone,
                ':contact_country' => $contact_country,
                ':total_price' => $total_price,
                ':assigned_hotel_id' => $assigned_hotel_id,
                ':assigned_vehicle_id' => $assigned_vehicle_id,
                ':assigned_guide_id' => $assigned_guide_id
            ]);

            $_SESSION['flash_success'] = "Your booking request has been submitted successfully and is awaiting staff review!";
            header("Location: dashboard.php?msg=booking_submitted");
            exit();
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Failed to submit booking: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%);">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-airplane-fill me-2"></i>Book Your Adventure</h4>
                </div>
                
                <div class="card-body p-4">
                    <!-- Package Info Preview Card -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                        <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        <div>
                            <h6 class="mb-1 text-dark fw-bold"><?php echo htmlspecialchars($pkg['title']); ?></h6>
                            <p class="mb-0 text-success fw-semibold small"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($pkg['destination']); ?></p>
                            <p class="mb-0 text-muted small"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($pkg['duration']); ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column bg-light p-3 rounded mb-4 border border-light gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small fw-semibold"><i class="bi bi-person me-1"></i>Min Price per Person (Adult)</span>
                            <span class="fw-bold text-success">Rs <?php echo number_format($pkg['price'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="text-secondary small fw-bold">Total Estimated Price</span>
                            <span class="fs-4 fw-extrabold text-success">Rs <span id="calculated_total"><?php echo number_format($pkg['price'], 2); ?></span></span>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form action="book.php?package_id=<?php echo $package_id; ?>" method="POST">
                        
                        <!-- 1. Tour Details Section -->
                        <h5 class="fw-bold text-success mb-3 border-bottom pb-2"><i class="bi bi-compass me-2"></i>Tour Details</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="arrival_date" class="form-label small text-secondary fw-semibold">Date of Arrival *</label>
                                <input type="date" class="form-control py-2" id="arrival_date" name="arrival_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" value="<?php echo htmlspecialchars($arrival_date); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="departure_date" class="form-label small text-secondary fw-semibold">Date of Departure *</label>
                                <input type="date" class="form-control py-2" id="departure_date" name="departure_date" required min="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" value="<?php echo htmlspecialchars($departure_date); ?>">
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-3 rounded-3">
                            <label class="form-label small text-secondary fw-bold mb-2"><i class="bi bi-people me-1"></i>Number of Guests</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="num_adults" class="form-label small text-muted">Adults *</label>
                                    <input type="number" class="form-control py-1.5" id="num_adults" name="num_adults" value="<?php echo htmlspecialchars($num_adults); ?>" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="num_children_older" class="form-label small text-muted">Children (06 - 11 Years)</label>
                                    <input type="number" class="form-control py-1.5" id="num_children_older" name="num_children_older" value="<?php echo htmlspecialchars($num_children_older); ?>" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="num_children_younger" class="form-label small text-muted">Children (below 05 Years)</label>
                                    <input type="number" class="form-control py-1.5" id="num_children_younger" name="num_children_younger" value="<?php echo htmlspecialchars($num_children_younger); ?>" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_rooms" class="form-label small text-secondary fw-semibold">Number of Rooms Required *</label>
                                <input type="number" class="form-control py-2" id="num_rooms" name="num_rooms" value="<?php echo htmlspecialchars($num_rooms); ?>" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_hotel_id" class="form-label small text-secondary fw-semibold">Select Accommodation Preference</label>
                                <select class="form-select py-2 price-modifier" id="assigned_hotel_id" name="assigned_hotel_id">
                                    <option value="" data-price="0" <?php echo empty($assigned_hotel_id) ? 'selected' : ''; ?>>None</option>
                                    <?php foreach ($hotels_opt as $h): ?>
                                        <option value="<?php echo $h['id']; ?>" data-price="<?php echo $h['price_per_night']; ?>" <?php echo ($assigned_hotel_id == $h['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($h['name']); ?> (+Rs <?php echo number_format($h['price_per_night']); ?>/night)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assigned_vehicle_id" class="form-label small text-secondary fw-semibold">Select Transportation Preference</label>
                                <select class="form-select py-2 price-modifier" id="assigned_vehicle_id" name="assigned_vehicle_id">
                                    <option value="" data-price="0" <?php echo empty($assigned_vehicle_id) ? 'selected' : ''; ?>>None</option>
                                    <?php foreach ($vehicles_opt as $v): ?>
                                        <option value="<?php echo $v['id']; ?>" data-price="<?php echo $v['price_per_day']; ?>" <?php echo ($assigned_vehicle_id == $v['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($v['model']); ?> (+Rs <?php echo number_format($v['price_per_day']); ?>/day)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_guide_id" class="form-label small text-secondary fw-semibold">Select Travel Guide Preference</label>
                                <select class="form-select py-2 price-modifier" id="assigned_guide_id" name="assigned_guide_id">
                                    <option value="" data-price="0" <?php echo empty($assigned_guide_id) ? 'selected' : ''; ?>>None</option>
                                    <?php foreach ($guides_opt as $g): ?>
                                        <option value="<?php echo $g['id']; ?>" data-price="<?php echo $g['price_per_day']; ?>" <?php echo ($assigned_guide_id == $g['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g['name']); ?> (+Rs <?php echo number_format($g['price_per_day']); ?>/day)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center pt-1">
                                <div class="form-check form-switch">
                                    <input class="form-check-input text-success" type="checkbox" id="guide_required" name="guide_required" value="1" <?php echo !empty($guide_required) ? 'checked' : ''; ?>>
                                    <label class="form-check-label small text-secondary fw-semibold ms-2" for="guide_required">Do you require a dedicated Travel Guide?</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="special_requests" class="form-label small text-secondary fw-semibold">Special Requests</label>
                            <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Describe special requests, custom tour arrangements, or dietary needs..."><?php echo htmlspecialchars($special_requests); ?></textarea>
                        </div>

                        <!-- 2. Personal Information Section -->
                        <h5 class="fw-bold text-success mb-3 border-bottom pb-2 pt-2"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label small text-secondary fw-semibold">Name *</label>
                                <input type="text" class="form-control py-2" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($contact_name); ?>" required placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label small text-secondary fw-semibold">Email *</label>
                                <input type="email" class="form-control py-2" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($contact_email); ?>" required placeholder="e.g. john@example.com">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label small text-secondary fw-semibold">Phone / WhatsApp *</label>
                                <input type="text" class="form-control py-2" id="contact_phone" name="contact_phone" required placeholder="e.g. +94 77 123 4567" value="<?php echo htmlspecialchars($contact_phone); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_country" class="form-label small text-secondary fw-semibold">Country *</label>
                                <input type="text" class="form-control py-2" id="contact_country" name="contact_country" required placeholder="e.g. Sri Lanka" value="<?php echo htmlspecialchars($contact_country); ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <a href="packages.php" class="btn btn-outline-secondary w-100 w-md-50 py-2.5">Cancel</a>
                            <button type="submit" class="btn btn-primary w-100 w-md-50 py-2.5">
                                <i class="bi bi-cart-check-fill me-1"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePrice = <?php echo floatval($pkg['price']); ?>;
    const numAdultsInput = document.getElementById('num_adults');
    const numChildrenOlderInput = document.getElementById('num_children_older');
    const numRoomsInput = document.getElementById('num_rooms');
    const arrivalDateInput = document.getElementById('arrival_date');
    const departureDateInput = document.getElementById('departure_date');
    
    const hotelSelect = document.getElementById('assigned_hotel_id');
    const vehicleSelect = document.getElementById('assigned_vehicle_id');
    const guideSelect = document.getElementById('assigned_guide_id');
    const calculatedTotalSpan = document.getElementById('calculated_total');

    function updatePrice() {
        const adults = parseInt(numAdultsInput.value) || 1;
        const childrenOlder = parseInt(numChildrenOlderInput.value) || 0;
        const rooms = parseInt(numRoomsInput.value) || 1;
        
        let days = 1;
        if (arrivalDateInput.value && departureDateInput.value) {
            const arr = new Date(arrivalDateInput.value);
            const dep = new Date(departureDateInput.value);
            const diffTime = Math.abs(dep - arr);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays > 0) days = diffDays;
        }

        // Base tour price (Adults full, children 50%)
        let total = basePrice * (adults + (0.5 * childrenOlder));
        
        // Add Hotel price (per room per night)
        if (hotelSelect) {
            const selectedHotelOpt = hotelSelect.options[hotelSelect.selectedIndex];
            const hotelPrice = parseFloat(selectedHotelOpt.getAttribute('data-price')) || 0;
            total += hotelPrice * rooms * days;
        }

        // Add Vehicle price (per day)
        if (vehicleSelect) {
            const selectedVehicleOpt = vehicleSelect.options[vehicleSelect.selectedIndex];
            const vehiclePrice = parseFloat(selectedVehicleOpt.getAttribute('data-price')) || 0;
            total += vehiclePrice * days;
        }

        // Add Guide price (per day)
        if (guideSelect) {
            const selectedGuideOpt = guideSelect.options[guideSelect.selectedIndex];
            const guidePrice = parseFloat(selectedGuideOpt.getAttribute('data-price')) || 0;
            total += guidePrice * days;
        }

        calculatedTotalSpan.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    numAdultsInput.addEventListener('input', updatePrice);
    numChildrenOlderInput.addEventListener('input', updatePrice);
    numRoomsInput.addEventListener('input', updatePrice);
    arrivalDateInput.addEventListener('change', updatePrice);
    departureDateInput.addEventListener('change', updatePrice);
    
    if (hotelSelect) hotelSelect.addEventListener('change', updatePrice);
    if (vehicleSelect) vehicleSelect.addEventListener('change', updatePrice);
    if (guideSelect) guideSelect.addEventListener('change', updatePrice);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
