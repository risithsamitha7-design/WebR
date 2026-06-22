<?php
// Load header layout which automatically starts configuration & session
require_once __DIR__ . '/includes/header.php';
?>

<!-- Scroll Snap Container -->
<main class="snap-container">
    
    <!-- Section 1: Hero Banner -->
    <section id="hero" class="scroll-section hero-section text-center">
        <div class="container py-5">
            <span class="hero-subtitle mb-2 d-inline-block">Welcome to Negombo's Premier Tour Agency</span>
            <h1 class="display-3 fw-extrabold mb-3 text-white">Explore Sri Lanka's Raw Natural Wonder</h1>
            <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 700px;">
                Book tailor-made tours to beautiful sandy beaches, historic colonial sites, scenic highland mountains, and thrilling wildlife reserves.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="packages.php" class="btn btn-primary btn-lg px-4 py-3"><i class="bi bi-compass me-2"></i>Browse Packages</a>
                <a href="contact.php" class="btn btn-outline-light btn-lg px-4 py-3"><i class="bi bi-envelope me-2"></i>Contact Us</a>
            </div>
        </div>
        
        <!-- Animated Mouse Scroll Indicator -->
        <div class="mouse-scroll-indicator">
            <div class="mouse-body">
                <div class="mouse-wheel"></div>
            </div>
            <span>Scroll Down</span>
        </div>
    </section>

    <!-- Section 2: Interactive Destinations Showcase -->
    <section id="destinations" class="scroll-section bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-success fw-bold text-uppercase tracking-wider small">Where to Go?</span>
                <h2 class="display-5 fw-bold text-dark mt-1">Top Destinations in Sri Lanka</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">
                    From the cultural triangle to the misty highlands, explore Sri Lanka's most iconic hotspots.
                </p>
            </div>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 justify-content-center">
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="uploads/sigiriya.png" alt="Sigiriya" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3 text-center">
                            <h5 class="fw-bold mb-1">Sigiriya</h5>
                            <p class="small text-muted mb-0">Ancient Lion Rock Fortress</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="uploads/ella.png" alt="Ella" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3 text-center">
                            <h5 class="fw-bold mb-1">Ella</h5>
                            <p class="small text-muted mb-0">Highland Train & Tea Country</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="uploads/yala.png" alt="Yala" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3 text-center">
                            <h5 class="fw-bold mb-1">Yala</h5>
                            <p class="small text-muted mb-0">Wildlife & Leopard Safari</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="uploads/beach.png" alt="Mirissa" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-3 text-center">
                            <h5 class="fw-bold mb-1">Mirissa</h5>
                            <p class="small text-muted mb-0">Southern Coast Beach & Whales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Call to Action & Explore Packages -->
    <section id="explore" class="scroll-section" style="background: var(--body-bg);">
        <div class="container py-5 text-center">
            <div class="p-5 rounded-5 bg-white shadow-sm border border-light mx-auto" style="max-width: 800px;">
                <span class="text-success fw-bold text-uppercase tracking-wider small">Tailor-Made Adventures</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3">Ready to Start Your Journey?</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">
                    We design personalized itineraries, coordinate luxury transport, organize star-class hotels, and provide professional tour guiding. Select your favorite catalog packages or send us a custom request.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="packages.php" class="btn btn-primary btn-lg px-4"><i class="bi bi-compass-fill me-2"></i>Explore Package Catalog</a>
                    <a href="about.php" class="btn btn-outline-secondary btn-lg px-4"><i class="bi bi-info-circle me-2"></i>Read About Us</a>
                    <a href="contact.php" class="btn btn-outline-success btn-lg px-4"><i class="bi bi-chat-left-text me-2"></i>Send Custom Inquiry</a>
                </div>
            </div>
        </div>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
