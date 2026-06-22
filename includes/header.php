<?php
// Include configuration file to ensure DB connection and session start
require_once __DIR__ . '/../config.php';

// Get current filename to handle active navbar links
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlobeTrek Adventures - Negombo Travel Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css?v=1.2" rel="stylesheet">
    <style>
        /* Header Horizontal Inline Flex Styles */
        .navbar-nav .nav-link {
            display: inline-flex !important;
            align-items: center;
            gap: 0.4rem;
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            white-space: nowrap !important;
        }
        /* Custom Caret override for Services dropdown */
        .navbar .dropdown-toggle::after {
            display: none !important;
        }
        /* Smooth Dropdown Hover Animation */
        @media (min-width: 992px) {
            .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
                display: block;
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            .navbar-nav .nav-item.dropdown .dropdown-menu {
                display: block;
                opacity: 0;
                visibility: hidden;
                transform: translateY(8px);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }
        /* Profile Pill & Initials Avatar */
        .profile-pill {
            background-color: #f8fafc;
            border: 1px solid rgba(45, 138, 78, 0.12);
            transition: all 0.2s ease;
        }
        .profile-pill:hover {
            border-color: rgba(45, 138, 78, 0.3);
            background-color: #f1f5f9;
        }
        .avatar-circle {
            background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%);
            box-shadow: 0 2px 4px rgba(45, 138, 78, 0.2);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-compass-fill me-2 fs-3"></i>
            <span>GlobeTrek Adventures</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door"></i><span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'packages.php') ? 'active' : ''; ?>" href="packages.php">
                        <i class="bi bi-compass"></i><span>Packages</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['accommodations.php', 'transport.php', 'guides.php']) ? 'active' : ''; ?>" href="#" id="navbarServicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill"></i><span>Services</span><i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem; opacity: 0.7;"></i>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3 mt-2" aria-labelledby="navbarServicesDropdown">
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="accommodations.php"><i class="bi bi-building text-success"></i>Accommodations</a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="transport.php"><i class="bi bi-car-front text-success"></i>Transportation</a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="guides.php"><i class="bi bi-person-badge text-success"></i>Travel Guides</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'about.php') ? 'active' : ''; ?>" href="about.php">
                        <i class="bi bi-info-circle"></i><span>About Us</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                        <i class="bi bi-envelope"></i><span>Contact Us</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="block text-decoration-none">
                        <div class="profile-pill flex-shrink-0 d-flex align-items-center rounded-pill px-3 py-1.5 gap-2">
                            <div class="avatar-circle flex-shrink-0 text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; aspect-ratio: 1/1; font-size: 0.85rem;">
                                <?php 
                                $words = explode(" ", $_SESSION['user_name']);
                                $initials = "";
                                foreach ($words as $w) {
                                    $initials .= strtoupper(substr($w, 0, 1));
                                }
                                echo htmlspecialchars(substr($initials, 0, 2));
                                ?>
                            </div>
                            <div class="d-flex flex-column lh-sm text-start" style="line-height: 1.2;">
                                <span class="text-muted small" style="font-size: 0.7rem;">Welcome,</span>
                                <span class="fw-bold text-dark small text-nowrap" style="font-size: 0.85rem; white-space: nowrap;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-capitalize ms-1" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <?php echo htmlspecialchars($_SESSION['user_role']); ?>
                            </span>
                        </div>
                    </a>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold <?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a href="register.php" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-semibold <?php echo ($current_page === 'register.php') ? 'active' : ''; ?>">
                        <i class="bi bi-person-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- System Alerts / Notifications Container -->
<div class="container mt-4 mb-2">
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?php echo $_SESSION['flash_success']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div><?php echo $_SESSION['flash_error']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
</div>
