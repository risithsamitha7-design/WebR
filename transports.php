<?php
/**
 * GlobeTrek Adventures - Transportation Services Showcase Page
 */
require_once __DIR__ . '/includes/header.php';
?>

<!-- Title & Hero Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); margin-bottom: 2rem; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
    <div class="container">
        <h1 class="fw-extrabold text-white mb-2"><i class="bi bi-car-front me-2"></i>Transportation Services</h1>
        <p class="lead text-success-subtle mb-0">Safe, reliable, and comfortable island-wide travel options</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Transport 1 -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="Airport Pickup Car" style="height: 220px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-start mb-2">Private Car & SUV</span>
                    <h5 class="card-title fw-bold text-dark">Airport Transfers & City Cab</h5>
                    <p class="card-text text-muted flex-grow-1">Guaranteed direct private airport transfer from Bandaranaike International Airport (CMB) in Negombo to anywhere on the island. Professional English-speaking drivers.</p>
                    <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold">From Rs 8,500 / Trip</span>
                        <a href="contact.php" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-envelope me-1"></i>Book</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transport 2 -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="4x4 Safari Jeep" style="height: 220px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill align-self-start mb-2">Wild Safari</span>
                    <h5 class="card-title fw-bold text-dark">4x4 Yala Safari Jeeps</h5>
                    <p class="card-text text-muted flex-grow-1">Rugged, open-top 4x4 vehicles custom built for wildlife photography. Accompanied by experienced local naturalists to spot leopards and elephants.</p>
                    <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold">From Rs 12,000 / Session</span>
                        <a href="contact.php" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-envelope me-1"></i>Book</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transport 3 -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1474487548417-781cb71495f3?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="Scenic Train Ride" style="height: 220px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill align-self-start mb-2">Scenic Train Ticket Coordination</span>
                    <h5 class="card-title fw-bold text-dark">Ella & Kandy Observation Rail</h5>
                    <p class="card-text text-muted flex-grow-1">Skip the queues and let us secure VIP observation deck train tickets for the world-famous colonial rail journey through Sri Lanka's tea country.</p>
                    <div class="mt-3 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold">From Rs 3,500 / Seat</span>
                        <a href="contact.php" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-envelope me-1"></i>Book</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
