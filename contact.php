<?php
/**
 * GlobeTrek Adventures - Contact Us Page
 */
require_once __DIR__ . '/config.php';

$q_name = '';
$q_email = '';
$q_subject = '';
$q_message = '';

// Process customer contact queries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_query') {
    $q_name = trim($_POST['name'] ?? '');
    $q_email = trim($_POST['email'] ?? '');
    $q_subject = trim($_POST['subject'] ?? '');
    $q_message = trim($_POST['message'] ?? '');

    $q_errors = [];
    if (empty($q_name)) {
        $q_errors[] = "Please provide your name.";
    } elseif (strlen($q_name) < 2 || strlen($q_name) > 100) {
        $q_errors[] = "Name must be between 2 and 100 characters.";
    }

    if (empty($q_email)) {
        $q_errors[] = "Please provide your email.";
    } elseif (!filter_var($q_email, FILTER_VALIDATE_EMAIL)) {
        $q_errors[] = "Please provide a valid email address.";
    }

    if (empty($q_subject)) {
        $q_errors[] = "Subject line is required.";
    } elseif (strlen($q_subject) < 3 || strlen($q_subject) > 150) {
        $q_errors[] = "Subject must be between 3 and 150 characters.";
    }

    if (empty($q_message)) {
        $q_errors[] = "Message content is required.";
    } elseif (strlen($q_message) < 10 || strlen($q_message) > 2000) {
        $q_errors[] = "Message must be between 10 and 2000 characters.";
    }

    if (empty($q_errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO queries (name, email, subject, message, status) VALUES (:name, :email, :subject, :message, 'unread')");
            $stmt->execute([
                ':name' => $q_name,
                ':email' => $q_email,
                ':subject' => $q_subject,
                ':message' => $q_message
            ]);
            $_SESSION['flash_success'] = "Thank you! Your message has been sent successfully. GlobeTrek staff will respond to you soon.";
            header("Location: contact.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Oops! We encountered an error sending your message: " . $e->getMessage();
        }
    } else {
        $_SESSION['flash_error'] = implode("<br>", $q_errors);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Contact & Query Submission Section -->
<section class="container py-5 mt-5">
    <div class="row g-5">
        <div class="col-lg-5">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%); color: white;">
                <h3 class="fw-bold mb-3 text-white">Get in Touch</h3>
                <p class="text-white-50 small mb-4">Have specific tour requirements? Drop us a query and our staff will reply within 24 hours.</p>
                
                <div class="d-flex gap-3 mb-4">
                    <i class="bi bi-geo-alt-fill fs-4 text-success-subtle"></i>
                    <div>
                        <h6 class="mb-0 text-white-50">Head Office</h6>
                        <p class="mb-0 small text-white">128 Lewis Place, Negombo, Sri Lanka</p>
                    </div>
                </div>
                
                <div class="d-flex gap-3 mb-4">
                    <i class="bi bi-envelope-open-fill fs-4 text-success-subtle"></i>
                    <div>
                        <h6 class="mb-0 text-white-50">Email Support</h6>
                        <p class="mb-0 small text-white">info@globetrekadventures.com</p>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <i class="bi bi-phone-fill fs-4 text-success-subtle"></i>
                    <div>
                        <h6 class="mb-0 text-white-50">Direct Hotline</h6>
                        <p class="mb-0 small text-white">+94 31 222 3456</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card border-0 p-4 shadow-sm rounded-4 bg-white">
                <h3 class="fw-bold text-dark mb-4">Submit a Query</h3>
                <form action="contact.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="submit_query">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small text-secondary fw-semibold">Your Name</label>
                            <input type="text" class="form-control py-2.5" id="name" name="name" required minlength="2" maxlength="100" placeholder="John Doe" value="<?php echo htmlspecialchars($q_name); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small text-secondary fw-semibold">Your Email</label>
                            <input type="email" class="form-control py-2.5" id="email" name="email" required placeholder="john@example.com" value="<?php echo htmlspecialchars($q_email); ?>">
                        </div>
                    </div>
                    
                    <div class="my-3">
                        <label for="subject" class="form-label small text-secondary fw-semibold">Subject</label>
                        <input type="text" class="form-control py-2.5" id="subject" name="subject" required minlength="3" maxlength="150" placeholder="e.g. Customized Safari Tour Package" value="<?php echo htmlspecialchars($q_subject); ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label small text-secondary fw-semibold">Message Description (Min 10 characters)</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required minlength="10" maxlength="2000" placeholder="Describe your travel needs..."><?php echo htmlspecialchars($q_message); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-4 py-2.5">
                        <i class="bi bi-send me-1"></i> Send Inquiry
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Script to activate Bootstrap form validation -->
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
