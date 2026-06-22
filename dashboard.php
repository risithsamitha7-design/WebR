<?php
/**
 * GlobeTrek Central Portal - Main Dashboard Router
 * Handles session validation, includes the actions processor, and routes active tab requests
 * to modular sub-views under includes/dashboard/ based on user roles (customer, staff, admin)
 */

// 1. Load the Action Controller first (handles POST/GET updates and redirects before output rendering)
require_once __DIR__ . '/includes/dashboard/actions.php';

// At this stage, the session is verified, and any dashboard actions have finished executing.
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$active_tab = $_GET['tab'] ?? '';

// Determine and validate the default active tab for each user role
if ($user_role === 'customer') {
    if (empty($active_tab) || !in_array($active_tab, ['bookings', 'custom_plans'])) {
        $active_tab = 'bookings';
    }
} elseif ($user_role === 'staff') {
    if (empty($active_tab) || !in_array($active_tab, ['packages', 'bookings', 'queries', 'custom_plans', 'services'])) {
        $active_tab = 'packages';
    }
} elseif ($user_role === 'admin') {
    if (empty($active_tab) || !in_array($active_tab, ['overview', 'bookings', 'packages', 'users', 'queries', 'reports', 'custom_plans', 'services'])) {
        $active_tab = 'overview';
    }
}

// 2. Load the general header template
require_once __DIR__ . '/includes/header.php';

// Fetch unread queries count for sidebar badge counters (for admin & staff)
$unread_queries = 0;
if ($user_role === 'admin' || $user_role === 'staff') {
    try {
        $unread_queries = $pdo->query("SELECT COUNT(*) FROM queries WHERE status='unread'")->fetchColumn();
    } catch (PDOException $e) {
        $unread_queries = 0;
    }
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Welcoming Alert Banner Card -->
        <div class="col-12 mb-4">
            <div class="card border-0 p-4 text-white shadow-sm rounded-4" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%);">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="text-success-subtle small fw-bold text-uppercase">GlobeTrek Central Portal</span>
                        <h2 class="mb-0 text-white font-extrabold">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                    </div>
                    <div class="bg-white bg-opacity-10 px-3 py-2 rounded-pill border border-white border-opacity-10 text-capitalize">
                        <i class="bi bi-shield-lock me-1"></i> Role: <?php echo htmlspecialchars($user_role); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- ROLE-BASED DASHBOARD ROUTING                                    -->
        <!-- ============================================================== -->

        <!-- CUSTOMER VIEW -->
        <?php if ($user_role === 'customer'): ?>
            <div class="col-lg-3 mb-4">
                <div class="dashboard-sidebar">
                    <h5 class="fw-bold mb-3">Navigation</h5>
                    <a href="dashboard.php?tab=bookings" class="dashboard-nav-item <?php echo ($active_tab === 'bookings') ? 'active' : ''; ?>">
                        <i class="bi bi-journal-check"></i> My Bookings
                    </a>
                    <a href="customize_plan.php" class="dashboard-nav-item">
                        <i class="bi bi-sliders2"></i> Customize Travel Plan
                    </a>
                    <a href="dashboard.php?tab=custom_plans" class="dashboard-nav-item <?php echo ($active_tab === 'custom_plans') ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-range"></i> Custom Plan Requests
                    </a>
                </div>
            </div>
            
            <div class="col-lg-9">
                <?php 
                if ($active_tab === 'bookings') {
                    include __DIR__ . '/includes/dashboard/customer_bookings.php';
                } elseif ($active_tab === 'custom_plans') {
                    include __DIR__ . '/includes/dashboard/customer_custom_plans.php';
                }
                ?>
            </div>

        <!-- STAFF VIEW -->
        <?php elseif ($user_role === 'staff'): ?>
            <div class="col-lg-3 mb-4">
                <div class="dashboard-sidebar">
                    <h5 class="fw-bold mb-3">Navigation</h5>
                    <a href="dashboard.php?tab=packages" class="dashboard-nav-item <?php echo ($active_tab === 'packages') ? 'active' : ''; ?>">
                        <i class="bi bi-boxes"></i> Package Management
                    </a>
                    <a href="dashboard.php?tab=bookings" class="dashboard-nav-item <?php echo ($active_tab === 'bookings') ? 'active' : ''; ?>">
                        <i class="bi bi-calendar2-check"></i> Manage Bookings
                    </a>
                     <a href="dashboard.php?tab=queries" class="dashboard-nav-item <?php echo ($active_tab === 'queries') ? 'active' : ''; ?>">
                        <i class="bi bi-envelope-paper-heart"></i> Customer Queries 
                        <?php if ($unread_queries > 0): ?>
                            <span class="badge bg-danger rounded-pill float-end small mt-0.5"><?php echo $unread_queries; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="dashboard.php?tab=services" class="dashboard-nav-item <?php echo ($active_tab === 'services') ? 'active' : ''; ?>">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Manage Services
                    </a>
                    <a href="dashboard.php?tab=custom_plans" class="dashboard-nav-item <?php echo ($active_tab === 'custom_plans') ? 'active' : ''; ?>">
                        <i class="bi bi-sliders2"></i> Custom Plan Requests
                    </a>
                </div>
            </div>

            <div class="col-lg-9">
                <?php 
                if ($active_tab === 'packages') {
                    include __DIR__ . '/includes/dashboard/manage_packages.php';
                } elseif ($active_tab === 'bookings') {
                    include __DIR__ . '/includes/dashboard/manage_bookings.php';
                } elseif ($active_tab === 'queries') {
                    include __DIR__ . '/includes/dashboard/manage_queries.php';
                } elseif ($active_tab === 'services') {
                    include __DIR__ . '/includes/dashboard/manage_services.php';
                } elseif ($active_tab === 'custom_plans') {
                    include __DIR__ . '/includes/dashboard/manage_custom_plans.php';
                }
                ?>
            </div>

        <!-- ADMINISTRATOR VIEW -->
        <?php elseif ($user_role === 'admin'): ?>
            <div class="col-lg-3 mb-4">
                <div class="dashboard-sidebar">
                    <h5 class="fw-bold mb-3">Admin Panel</h5>
                    <a href="dashboard.php?tab=overview" class="dashboard-nav-item <?php echo ($active_tab === 'overview') ? 'active' : ''; ?>">
                        <i class="bi bi-graph-up-arrow"></i> Overview & Stats
                    </a>
                    <a href="dashboard.php?tab=bookings" class="dashboard-nav-item <?php echo ($active_tab === 'bookings') ? 'active' : ''; ?>">
                        <i class="bi bi-calendar2-check"></i> Manage Bookings
                    </a>
                    <a href="dashboard.php?tab=packages" class="dashboard-nav-item <?php echo ($active_tab === 'packages') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i> Manage Packages
                    </a>
                    <a href="dashboard.php?tab=users" class="dashboard-nav-item <?php echo ($active_tab === 'users') ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i> Staff & User Roles
                    </a>
                    <a href="dashboard.php?tab=queries" class="dashboard-nav-item <?php echo ($active_tab === 'queries') ? 'active' : ''; ?>">
                        <i class="bi bi-envelope-paper-heart"></i> Customer Queries 
                        <?php if ($unread_queries > 0): ?>
                            <span class="badge bg-danger rounded-pill float-end small mt-0.5"><?php echo $unread_queries; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="dashboard.php?tab=services" class="dashboard-nav-item <?php echo ($active_tab === 'services') ? 'active' : ''; ?>">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Manage Services
                    </a>
                    <a href="dashboard.php?tab=reports" class="dashboard-nav-item <?php echo ($active_tab === 'reports') ? 'active' : ''; ?>">
                        <i class="bi bi-bar-chart-line-fill"></i> Reports & Analytics
                    </a>
                    <a href="dashboard.php?tab=custom_plans" class="dashboard-nav-item <?php echo ($active_tab === 'custom_plans') ? 'active' : ''; ?>">
                        <i class="bi bi-sliders2"></i> Custom Plan Requests
                    </a>
                </div>
            </div>

            <div class="col-lg-9">
                <?php
                if ($active_tab === 'overview') {
                    include __DIR__ . '/includes/dashboard/admin_overview.php';
                } elseif ($active_tab === 'bookings') {
                    include __DIR__ . '/includes/dashboard/manage_bookings.php';
                } elseif ($active_tab === 'packages') {
                    include __DIR__ . '/includes/dashboard/manage_packages.php';
                } elseif ($active_tab === 'users') {
                    include __DIR__ . '/includes/dashboard/manage_users.php';
                } elseif ($active_tab === 'queries') {
                    include __DIR__ . '/includes/dashboard/manage_queries.php';
                } elseif ($active_tab === 'services') {
                    include __DIR__ . '/includes/dashboard/manage_services.php';
                } elseif ($active_tab === 'reports') {
                    include __DIR__ . '/includes/dashboard/reports.php';
                } elseif ($active_tab === 'custom_plans') {
                    include __DIR__ . '/includes/dashboard/manage_custom_plans.php';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// 3. Load the footer layout
require_once __DIR__ . '/includes/footer.php';
?>
