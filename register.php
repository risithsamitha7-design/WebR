<?php
require_once __DIR__ . '/config.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$name = '';
$email = '';
$contact = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and read input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $contact = trim($_POST['contact'] ?? '');

    // Server-side validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if (empty($contact)) {
        $errors[] = "Contact number is required.";
    }

    // Check if email already exists in DB
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "A user with this email address already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Insert user into database
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, contact_number) VALUES (:name, :email, :password, 'customer', :contact)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed_password,
                ':contact' => $contact
            ]);

            // Set flash success message and redirect
            $_SESSION['flash_success'] = "Registration Successful! You can now log in using your credentials.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Failed to register user. Please try again. " . $e->getMessage();
        }
    }
}

// Render Header
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="mb-1"><i class="bi bi-person-plus-fill me-2"></i>Join GlobeTrek</h2>
            <p class="mb-0 text-white-50 small">Create your account and explore Negombo and beyond</p>
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

            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="John Doe">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="john@example.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required placeholder="+94 77 123 4567">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Min 6 characters">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2.5 mb-3">
                    <i class="bi bi-check-circle me-1"></i> Register Account
                </button>
                
                <div class="text-center text-muted small">
                    Already have an account? <a href="login.php" class="text-success fw-bold text-decoration-none">Login here</a>
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
