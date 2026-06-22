<?php
require_once __DIR__ . '/config.php';

// Enforce strict authentication guards
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle POST request for General Info or Password updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_general') {
            // Sanitize inputs
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $contact_number = trim($_POST['contact_number'] ?? '');
            
            if (empty($name) || empty($email) || empty($contact_number)) {
                $error_msg = 'All general information fields are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = 'Please enter a valid email address.';
            } else {
                try {
                    // Check if email already exists for another user
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $user_id]);
                    if ($stmt->fetch()) {
                        $error_msg = 'This email address is already registered to another account.';
                    } else {
                        // Parameterized PDO UPDATE statement
                        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, contact_number = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $contact_number, $user_id]);
                        
                        // Update active session variables
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        
                        $success_msg = 'Your profile details have been updated successfully.';
                    }
                } catch (PDOException $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        } elseif ($_POST['action'] === 'update_password') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error_msg = 'All password fields are required.';
            } elseif ($new_password !== $confirm_password) {
                $error_msg = 'The new password and confirmation password do not match.';
            } else {
                try {
                    // Cryptographic verification: fetch current hashed password
                    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user_db = $stmt->fetch();
                    
                    if ($user_db && password_verify($current_password, $user_db['password'])) {
                        // Encrypt new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        // Parameterized PDO UPDATE statement
                        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $stmt->execute([$hashed_password, $user_id]);
                        
                        $success_msg = 'Password changed successfully.';
                    } else {
                        $error_msg = 'Your current password is incorrect.';
                    }
                } catch (PDOException $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Fetch up-to-date user data
try {
    $stmt = $pdo->prepare("SELECT name, email, contact_number, role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Session user not found
        header("Location: logout.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database access error: " . $e->getMessage());
}

// Determine role badge representation
$role_badge = '';
$role_badge_class = '';
if ($user['role'] === 'admin') {
    $role_badge = 'System Administrator';
    $role_badge_class = 'bg-rose-100 text-rose-800 border-rose-300';
} elseif ($user['role'] === 'staff') {
    $role_badge = 'Staff Member';
    $role_badge_class = 'bg-amber-100 text-amber-800 border-amber-300';
} else {
    $role_badge = 'Customer';
    $role_badge_class = 'bg-emerald-100 text-emerald-800 border-emerald-300';
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Tailwind CSS Script & Config (preflight: false to preserve header/footer bootstrap layout) -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        }
    }
</script>

<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb / Header Title -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Personal Profile</h1>
                <p class="mt-2 text-sm text-slate-500">Manage your account information and security credentials.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border <?php echo $role_badge_class; ?>">
                    <span class="w-2.5 h-2.5 rounded-full mr-2 <?php echo $user['role'] === 'admin' ? 'bg-rose-600' : ($user['role'] === 'staff' ? 'bg-amber-600' : 'bg-emerald-600'); ?>"></span>
                    <?php echo htmlspecialchars($role_badge); ?>
                </span>
            </div>
        </div>

        <!-- Custom Viewport Alerts (Tailwind styled) -->
        <?php if (!empty($success_msg)): ?>
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-emerald-800"><?php echo htmlspecialchars($success_msg); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-sm font-medium text-rose-800"><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Navigation panel/Info -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm sticky top-24">
                    <div class="flex flex-col items-center text-center">
                        <!-- Initials Circle -->
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-600 to-emerald-800 text-white flex items-center justify-content-center text-2xl font-bold mb-4 shadow-md">
                            <?php 
                            $words = explode(" ", $user['name']);
                            $initials = "";
                            foreach ($words as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                            }
                            echo htmlspecialchars(substr($initials, 0, 2));
                            ?>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="text-sm text-slate-500 mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <div class="w-full border-t border-slate-100 my-4"></div>
                        
                        <div class="text-left w-full">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Account Details</span>
                            <div class="mt-2 space-y-2 text-sm text-slate-600">
                                <p class="flex items-center gap-2">
                                    <i class="bi bi-telephone text-slate-400"></i>
                                    <span><?php echo htmlspecialchars($user['contact_number']); ?></span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <i class="bi bi-shield-check text-slate-400"></i>
                                    <span class="capitalize"><?php echo htmlspecialchars($user['role']); ?> Access</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forms Container -->
            <div class="md:col-span-2 space-y-8">
                <!-- Component A: General Info Form -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-person-gear text-emerald-600"></i>
                            General Information
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Update your personal identification information here.</p>
                    </div>
                    <form action="profile.php" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="action" value="update_general">
                        
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" 
                                   required 
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                   placeholder="Full Name">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       required 
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                       placeholder="email@example.com">
                            </div>
                            <div>
                                <label for="contact_number" class="block text-sm font-semibold text-slate-700 mb-2">Contact Number</label>
                                <input type="text" id="contact_number" name="contact_number" 
                                       value="<?php echo htmlspecialchars($user['contact_number']); ?>" 
                                       required 
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                       placeholder="+94 77 123 4567">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition duration-150">
                                <i class="bi bi-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Component B: Security Credentials Form -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-shield-lock text-emerald-600"></i>
                            Security Credentials
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ensure your account stays secure by changing your password periodically.</p>
                    </div>
                    <form action="profile.php" method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="action" value="update_password">

                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   required 
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                   placeholder="••••••••">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       required 
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                       placeholder="••••••••">
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       required 
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none text-sm text-slate-800 transition bg-white"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition duration-150">
                                <i class="bi bi-key me-2"></i> Update Password
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
