<!-- Footer -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="d-flex align-items-center text-white">
                    <i class="bi bi-compass-fill me-2 text-success"></i> GlobeTrek Adventures
                </h5>
                <p class="small text-muted">
                    Based in Negombo, Sri Lanka, GlobeTrek Adventures specializes in crafting bespoke eco-tourism, wildlife safaris, and cultural tours. Discover the natural beauty of the pearl of the Indian Ocean.
                </p>
                <div class="d-flex gap-3 fs-5 mt-3">
                    <a href="#" class="text-white-50"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white">Our Top Destinations</h5>
                <ul class="list-unstyled text-muted small">
                    <li>Negombo Beach & Lagoons</li>
                    <li>Sigiriya Cultural Fortress</li>
                    <li>Ella Highlands & Ravana</li>
                    <li>Yala National Park Safari</li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white">Contact Info</h5>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill me-2 text-success"></i> 
                        128 Lewis Place, Kudapaduwa, Negombo, Sri Lanka
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone-fill me-2 text-success"></i> 
                        +94 31 222 3456
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill me-2 text-success"></i> 
                        info@globetrekadventures.com
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small">
                &copy; <?php echo date('Y'); ?> GlobeTrek Adventures. All Rights Reserved.
            </div>
            <div class="col-md-6 text-center text-md-end small">
                Designed for Tourism & Hospitality Management.
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    const cssRules = `
        /* Hide toggle button when strict-wireframe is active */
        body.strict-wireframe #wireframe-toggle-btn {
            display: none !important;
        }

        /* Exclude header, nav, and footer from grayscale overlays */
        body.strict-wireframe nav,
        body.strict-wireframe header,
        body.strict-wireframe footer {
            position: relative !important;
            z-index: 10005 !important;
        }

        /* Color & Asset Neutralization Layer CSS Override */
        body.strict-wireframe :not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            filter: grayscale(100%) contrast(150%) brightness(100%) !important;
            box-shadow: none !important;
            text-shadow: none !important;
            transition: none !important;
        }

        /* Neutralize background colors outside header/nav/footer */
        body.strict-wireframe div:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe section:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe article:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe aside:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe main:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .card:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .modal-content:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .alert:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            background-color: #ffffff !important;
            border-color: #7e7576 !important;
        }

        body.strict-wireframe .card:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .stat-card:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .search-container:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .dashboard-sidebar:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .dropdown-menu:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            background-color: #eeeeee !important;
        }

        body.strict-wireframe table:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe th:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe td:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            border: 1px solid #7e7576 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        body.strict-wireframe th:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            background-color: #eeeeee !important;
        }

        body.strict-wireframe input:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe select:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe textarea:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe button:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .btn:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            background-color: #ffffff !important;
            border: 1px solid #7e7576 !important;
            color: #000000 !important;
        }

        /* Strict Image Override: draw a sharp diagonal X without layout movement */
        body.strict-wireframe img:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe video:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .package-img-container:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .hero-section:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            content: "" !important;
            background: 
                linear-gradient(to top right, transparent calc(50% - 1.5px), #7e7576 calc(50% - 1.5px), #7e7576 calc(50% + 1.5px), transparent calc(50% + 1.5px)),
                linear-gradient(to bottom right, transparent calc(50% - 1.5px), #7e7576 calc(50% - 1.5px), #7e7576 calc(50% + 1.5px), transparent calc(50% + 1.5px)) !important;
            background-color: #eeeeee !important;
            border: 1px solid #7e7576 !important;
            opacity: 1 !important;
        }

        /* Icons override: change color without altering layout dimensions */
        body.strict-wireframe i[class^="bi-"]:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe i[class*=" bi-"]:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe .bi:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *),
        body.strict-wireframe svg:not(header):not(nav):not(footer):not(header *):not(nav *):not(footer *) {
            color: #7e7576 !important;
            border-color: #7e7576 !important;
        }
    `;

    // Function to activate wireframe mode
    function activateWireframe() {
        document.body.classList.add('strict-wireframe');
        
        // 1. Inject Style Tag
        if (!document.getElementById('wireframe-style-tag')) {
            const styleTag = document.createElement('style');
            styleTag.id = 'wireframe-style-tag';
            styleTag.textContent = cssRules;
            document.head.appendChild(styleTag);
        }

        // 2. Inject Overlay 1 (Color & Asset Neutralization)
        if (!document.getElementById('wireframe-backdrop-overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'wireframe-backdrop-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 10000; pointer-events: none; backdrop-filter: grayscale(100%) contrast(150%) brightness(100%); background: rgba(255,255,255,0.2);';
            document.body.appendChild(overlay);
        }
    }

    // Function to deactivate wireframe mode
    function deactivateWireframe() {
        document.body.classList.remove('strict-wireframe');
        
        const overlay = document.getElementById('wireframe-backdrop-overlay');
        if (overlay) overlay.remove();

        const styleTag = document.getElementById('wireframe-style-tag');
        if (styleTag) styleTag.remove();

        const toggleBtn = document.getElementById('wireframe-toggle-btn');
        if (toggleBtn) {
            toggleBtn.style.display = 'flex';
        }
    }

    // Initialize logic on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        // Create the floating action toggle button dynamically
        let toggleBtn = document.getElementById('wireframe-toggle-btn');
        if (!toggleBtn) {
            toggleBtn = document.createElement('button');
            toggleBtn.id = 'wireframe-toggle-btn';
            toggleBtn.className = 'btn btn-dark shadow-lg d-flex align-items-center justify-content-center';
            toggleBtn.style.cssText = 'position: fixed; bottom: 25px; left: 25px; width: 50px; height: 50px; border-radius: 50%; z-index: 9999; border: 2px solid #ffffff; transition: all 0.3s ease;';
            toggleBtn.title = 'Toggle Wireframe Mode';
            toggleBtn.innerHTML = '<i class="bi bi-bounding-box" style="font-size: 1.25rem;"></i>';
            document.body.appendChild(toggleBtn);
        }

        // Click event listener
        toggleBtn.addEventListener('click', function() {
            activateWireframe();
            localStorage.setItem('strict-wireframe', 'active');
        });

        // Restore if active in localStorage
        if (localStorage.getItem('strict-wireframe') === 'active') {
            activateWireframe();
        }

        // Global Keydown Listener for Escape key
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' || event.key === 'Esc') {
                deactivateWireframe();
                localStorage.setItem('strict-wireframe', 'inactive');
            }
        });
    });

    // Run immediately if DOM is already parsed to prevent color flash
    if (localStorage.getItem('strict-wireframe') === 'active') {
        activateWireframe();
    }
})();
</script>

<!-- Bootstrap 5 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
