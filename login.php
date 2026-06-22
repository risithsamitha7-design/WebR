<?php
require_once __DIR__ . '/config.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic server-side validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        try {
            // Retrieve user details
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password matches, write session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];

                $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['name']) . "! You are logged in as a " . htmlspecialchars($user['role']) . ".";
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Render Header
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="mb-1"><i class="bi bi-box-arrow-in-right me-2"></i>Welcome Back</h2>
            <p class="mb-0 text-white-50 small">Log in to manage bookings and packages</p>
        </div>
        <div class="auth-body">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 shadow-sm" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="john@example.com">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2.5 mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                </button>
                
                <div class="text-center text-muted small mb-4">
                    Don't have an account? <a href="register.php" class="text-success fw-bold text-decoration-none">Register here</a>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Bootstrap client-side form validation script -->
<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
