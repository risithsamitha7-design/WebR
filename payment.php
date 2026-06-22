<?php
// Load database config and start session at the very beginning
require_once __DIR__ . '/config.php';

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "You must log in to view the payment page.";
    header("Location: login.php");
    exit();
}

// 2. Ensure user has 'customer' role
if ($_SESSION['user_role'] !== 'customer') {
    $_SESSION['flash_error'] = "Only customer accounts can make payments.";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);

if ($booking_id <= 0) {
    $_SESSION['flash_error'] = "Invalid booking reference.";
    header("Location: dashboard.php");
    exit();
}

// 3. Fetch booking & package details from database
try {
    $stmt = $pdo->prepare("
        SELECT b.*, p.title, p.destination, p.duration, p.image_url 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.id 
        WHERE b.id = :booking_id AND b.user_id = :user_id
    ");
    $stmt->execute([':booking_id' => $booking_id, ':user_id' => $user_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $_SESSION['flash_error'] = "Booking request not found or access denied.";
        header("Location: dashboard.php");
        exit();
    }

    // Enforce awaiting_payment status
    if ($booking['status'] !== 'awaiting_payment') {
        if ($booking['status'] === 'confirmed') {
            $_SESSION['flash_error'] = "This booking has already been paid and confirmed.";
        } else {
            $_SESSION['flash_error'] = "This booking is not ready for payment. Current status: " . ucfirst($booking['status']);
        }
        header("Location: dashboard.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}

// 4. Process payment submission (Mock Card Gateway)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    $cardholder = trim($_POST['cardholder'] ?? '');
    $cardnumber = str_replace(' ', '', trim($_POST['cardnumber'] ?? ''));
    $expiry = trim($_POST['expiry'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    $errors = [];
    if (empty($cardholder)) {
        $errors[] = "Cardholder name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]{3,100}$/", $cardholder)) {
        $errors[] = "Cardholder name must contain only letters and spaces (3-100 characters).";
    }

    if (empty($cardnumber)) {
        $errors[] = "Card number is required.";
    } elseif (!preg_match("/^\d{15,19}$/", $cardnumber)) {
        $errors[] = "Please provide a valid card number (15 to 19 digits).";
    }

    if (empty($expiry)) {
        $errors[] = "Card expiry date is required.";
    } elseif (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $expiry)) {
        $errors[] = "Card expiry must be in MM/YY format.";
    }

    if (empty($cvv)) {
        $errors[] = "Please provide a CVV code.";
    } elseif (!preg_match("/^\d{3,4}$/", $cvv)) {
        $errors[] = "Please provide a valid CVV code (3 or 4 digits).";
    }

    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode("<br>", $errors);
    } else {
        try {
            // Begin database transaction to ensure booking + payment are atomic
            $pdo->beginTransaction();

            $transaction_id = 'GTXN-' . strtoupper(uniqid());

            // Save details to payments table
            $stmt = $pdo->prepare("
                INSERT INTO payments (booking_id, user_id, amount, payment_method, transaction_id, status) 
                VALUES (:booking_id, :user_id, :amount, 'credit_card', :transaction_id, 'completed')
            ");
            $stmt->execute([
                ':booking_id' => $booking_id,
                ':user_id' => $user_id,
                ':amount' => $booking['total_price'],
                ':transaction_id' => $transaction_id
            ]);

            // Update corresponding booking row
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = :booking_id");
            $stmt->execute([':booking_id' => $booking_id]);

            // Commit transaction
            $pdo->commit();

            $_SESSION['flash_success'] = "Payment of Rs " . number_format($booking['total_price'], 2) . " processed successfully! Your booking is now Confirmed. Transaction ID: " . $transaction_id;
            header("Location: dashboard.php?msg=payment_success");
            exit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['flash_error'] = "Could not record payment: " . $e->getMessage();
        }
    }
}

// Load header layout here, after all possible redirect headers have been processed
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row g-4">
        <!-- Booking Overview Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <span class="text-success fw-bold text-uppercase tracking-wider small mb-1">Step 2 of 2</span>
                <h3 class="fw-extrabold text-dark mb-4">Tour Booking Summary</h3>

                <div class="mb-4 text-center">
                    <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 220px; width: 100%; object-fit: cover;">
                </div>

                <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($booking['title']); ?></h4>
                <p class="text-success fw-semibold mb-3"><i class="bi bi-geo-alt-fill me-1"></i><?php echo htmlspecialchars($booking['destination']); ?></p>

                <hr class="text-muted opacity-25">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small fw-semibold"><i class="bi bi-calendar-event me-1"></i>Arrival Date</span>
                    <strong class="text-dark"><?php echo date('M d, Y', strtotime($booking['arrival_date'])); ?></strong>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small fw-semibold"><i class="bi bi-calendar-check me-1"></i>Departure Date</span>
                    <strong class="text-dark"><?php echo date('M d, Y', strtotime($booking['departure_date'])); ?></strong>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small fw-semibold"><i class="bi bi-moon-stars me-1"></i>Nights</span>
                    <strong class="text-dark"><?php 
                        $arr = new DateTime($booking['arrival_date']);
                        $dep = new DateTime($booking['departure_date']);
                        $nights = $arr->diff($dep)->days;
                        echo $nights; 
                    ?></strong>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small fw-semibold"><i class="bi bi-clock me-1"></i>Base Duration</span>
                    <strong class="text-dark"><?php echo htmlspecialchars($booking['duration']); ?></strong>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-secondary small fw-semibold"><i class="bi bi-ticket-perforated me-1"></i>Booking ID</span>
                    <strong class="text-dark">#<?php echo $booking['id']; ?></strong>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: var(--primary-light);">
                    <span class="fw-bold text-success">Total Amount Due</span>
                    <span class="fs-3 fw-extrabold text-success">Rs <?php echo number_format($booking['total_price'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Form Column -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden h-100">
                <div class="card-header text-white p-4" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i>Mock Bank Card Payment</h4>
                    <p class="mb-0 text-white-50 small mt-1">Complete your booking securely using credit/debit card details.</p>
                </div>
                <div class="card-body p-4">
                    
                    <form action="payment.php" method="POST" class="needs-validation" novalidate id="paymentForm">
                        <input type="hidden" name="action" value="process_payment">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cardholder" class="form-label small text-secondary fw-semibold">Cardholder Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person text-secondary"></i></span>
                                    <input type="text" class="form-control bg-light" id="cardholder" name="cardholder" placeholder="John Doe" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="cardnumber" class="form-label small text-secondary fw-semibold">Card Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-credit-card-2-front text-secondary"></i></span>
                                    <input type="text" class="form-control bg-light" id="cardnumber" name="cardnumber" placeholder="4111 2222 3333 4444" minlength="15" maxlength="19" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="expiry" class="form-label small text-secondary fw-semibold">Expiration Date</label>
                                <input type="text" class="form-control bg-light" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cvv" class="form-label small text-secondary fw-semibold">CVV / CVC</label>
                                <input type="text" class="form-control bg-light" id="cvv" name="cvv" placeholder="123" minlength="3" maxlength="4" required>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <a href="dashboard.php" class="btn btn-outline-secondary px-4 py-2.5"><i class="bi bi-x-circle me-1"></i>Pay Later</a>
                            <button type="submit" class="btn btn-primary flex-grow-1 py-2.5"><i class="bi bi-check-circle-fill me-1"></i>Pay Securely (Rs <?php echo number_format($booking['total_price'], 2); ?>)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Basic formatting for expiry MM/YY input
    const expiryInput = document.getElementById("expiry");
    expiryInput.addEventListener("input", function (e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length > 2) {
            value = value.substring(0, 2) + "/" + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Basic formatting for card number spacing
    const cardInput = document.getElementById("cardnumber");
    cardInput.addEventListener("input", function (e) {
        let value = e.target.value.replace(/\D/g, "");
        let formatted = "";
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += " ";
            }
            formatted += value[i];
        }
        e.target.value = formatted;
    });

    // Form validation
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
