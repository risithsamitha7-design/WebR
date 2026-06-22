<?php
/**
 * GlobeTrek Adventures - Customize Plan Page
 * Allows authenticated customers to request custom travel itineraries.
 */
require_once __DIR__ . '/includes/header.php';

// Auth Guard
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "Please log in to access the Custom Travel Planner.";
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'] ?? '';
if ($user_role !== 'customer') {
    $_SESSION['flash_error'] = "Only customers can request customized plans.";
    header("Location: dashboard.php");
    exit();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back to Dashboard Link -->
            <div class="mb-4">
                <a href="dashboard.php" class="text-success text-decoration-none fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to My Bookings
                </a>
            </div>

            <!-- Customization Form Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%);">
                    <h3 class="fw-bold mb-1"><i class="bi bi-sliders2 me-2"></i>Design Your Dream Getaway</h3>
                    <p class="mb-0 text-success-subtle text-opacity-75">Tell us your preferences and we'll craft a custom itinerary for you.</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="dashboard.php" method="POST" class="row g-4">
                        <input type="hidden" name="action" value="submit_custom_plan">

                        <!-- Destination -->
                        <div class="col-md-6">
                            <label for="destination" class="form-label text-secondary fw-semibold">Where do you want to go?</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt-fill text-success"></i></span>
                                <input type="text" class="form-control bg-light border-start-0" id="destination" name="destination" placeholder="e.g. Negombo, Ella, Kandy" required>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="col-md-6">
                            <label for="duration" class="form-label text-secondary fw-semibold">Duration</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock-fill text-success"></i></span>
                                <input type="text" class="form-control bg-light border-start-0" id="duration" name="duration" placeholder="e.g. 5 Days / 4 Nights" required>
                            </div>
                        </div>

                        <!-- Travel Date -->
                        <div class="col-md-6">
                            <label for="travel_date" class="form-label text-secondary fw-semibold">Expected Travel Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event-fill text-success"></i></span>
                                <input type="date" class="form-control bg-light border-start-0" id="travel_date" name="travel_date" required>
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="col-md-6">
                            <label for="budget" class="form-label text-secondary fw-semibold">Estimated Budget (Rs)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-cash-stack text-success"></i></span>
                                <input type="number" class="form-control bg-light border-start-0" id="budget" name="budget" placeholder="e.g. 100000" min="1000" required>
                            </div>
                        </div>



                        <!-- Special Requirements -->
                        <div class="col-12">
                            <label for="special_requirements" class="form-label text-secondary fw-semibold">Special Requirements & Interests</label>
                            <textarea class="form-control bg-light" id="special_requirements" name="special_requirements" rows="4" placeholder="Detail any activities, dietary needs, places of interest, or custom requirements..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-2"></i>Submit Plan Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
