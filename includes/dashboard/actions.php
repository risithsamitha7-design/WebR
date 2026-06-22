<?php
/**
 * GlobeTrek Central Portal - Actions Controller
 * Handles database transaction logic for all roles (customer, staff, admin)
 */

// Ensure configuration and database connection are loaded
require_once __DIR__ . '/../../config.php';

// Ensure the user session is active
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "You must log in to view your dashboard.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Extract the action parameters
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!empty($action)) {
    // ---------------------------------------------------------
    // CUSTOMER ACTIONS
    // ---------------------------------------------------------
    
    // Customer canceling booking (only if status is pending and unpaid)
    if ($user_role === 'customer' && $action === 'cancel_booking') {
        $booking_id = intval($_GET['booking_id'] ?? 0);
        try {
            // Check if paid
            $pay_check = $pdo->prepare("SELECT id FROM payments WHERE booking_id = :booking_id AND status = 'completed'");
            $pay_check->execute([':booking_id' => $booking_id]);
            if ($pay_check->rowCount() > 0) {
                $_SESSION['flash_error'] = "You cannot cancel this booking. It has already been paid.";
            } else {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status = 'pending'");
                $stmt->execute([':id' => $booking_id, ':user_id' => $user_id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['flash_success'] = "Booking cancelled successfully.";
                } else {
                    $_SESSION['flash_error'] = "You cannot cancel this booking. It might already be confirmed or cancelled.";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
        }
        header("Location: dashboard.php");
        exit();
    }

    // Customer submitting customized plan request
    if ($user_role === 'customer' && $action === 'submit_custom_plan') {
        $destination = trim($_POST['destination'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $travel_date = trim($_POST['travel_date'] ?? '');
        $budget = floatval($_POST['budget'] ?? 0.0);
        $special_requirements = trim($_POST['special_requirements'] ?? '');

        if (empty($destination) || empty($duration) || empty($travel_date) || $budget <= 0) {
            $_SESSION['flash_error'] = "Please fill in all required fields and enter a valid budget.";
            header("Location: customize_plan.php");
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO custom_itineraries (user_id, destination, duration, travel_date, budget, special_requirements, status) VALUES (:user_id, :destination, :duration, :travel_date, :budget, :special_requirements, 'pending')");
            $stmt->execute([
                ':user_id' => $user_id,
                ':destination' => $destination,
                ':duration' => $duration,
                ':travel_date' => $travel_date,
                ':budget' => $budget,
                ':special_requirements' => $special_requirements
            ]);
            $_SESSION['flash_success'] = "Your customized itinerary request for $destination has been submitted successfully!";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=custom_plans");
        exit();
    }

    // STAFF & ADMIN ACTIONS
    // ---------------------------------------------------------
    
    // Staff/Admin - Add Travel Package
    if (($user_role === 'staff' || $user_role === 'admin') && $action === 'add_package') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $destination = trim($_POST['destination'] ?? '');
        $price = floatval($_POST['price'] ?? 0.0);
        $duration = trim($_POST['duration'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        if (empty($image_url)) {
            $image_url = 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80';
        }

        if (empty($title) || empty($description) || empty($destination) || $price <= 0 || empty($duration)) {
            $_SESSION['flash_error'] = "Please fill in all fields with valid information. Price must be positive.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO packages (title, description, destination, price, duration, image_url) VALUES (:title, :description, :destination, :price, :duration, :image_url)");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':destination' => $destination,
                    ':price' => $price,
                    ':duration' => $duration,
                    ':image_url' => $image_url
                ]);
                $_SESSION['flash_success'] = "New travel package '$title' added successfully!";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error adding package: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=packages");
        exit();
    }

    // Staff/Admin - Edit Travel Package
    if (($user_role === 'staff' || $user_role === 'admin') && $action === 'edit_package') {
        $package_id = intval($_POST['package_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $destination = trim($_POST['destination'] ?? '');
        $price = floatval($_POST['price'] ?? 0.0);
        $duration = trim($_POST['duration'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        if (empty($title) || empty($description) || empty($destination) || $price <= 0 || empty($duration)) {
            $_SESSION['flash_error'] = "All package fields are required and pricing must be positive.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE packages SET title = :title, description = :description, destination = :destination, price = :price, duration = :duration, image_url = :image_url WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':destination' => $destination,
                    ':price' => $price,
                    ':duration' => $duration,
                    ':image_url' => $image_url,
                    ':id' => $package_id
                ]);
                $_SESSION['flash_success'] = "Travel package '$title' updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error updating package: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=packages");
        exit();
    }

    // Staff/Admin - Delete Travel Package
    if (($user_role === 'staff' || $user_role === 'admin') && $action === 'delete_package') {
        $package_id = intval($_GET['package_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = :id");
            $stmt->execute([':id' => $package_id]);
            $_SESSION['flash_success'] = "Travel package deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error deleting package. (Check if it has linked bookings): " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=packages");
        exit();
    }

    // Staff/Admin - Update Booking Status
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'update_booking_status') {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        // Staff/Admin approval transitions to 'awaiting_payment', not 'confirmed'
        if ($status === 'confirmed') {
            $status = 'awaiting_payment';
        }

        if (in_array($status, ['pending', 'awaiting_payment', 'confirmed', 'cancelled'])) {
            try {
                if ($status === 'awaiting_payment') {
                    $stmt = $pdo->prepare("UPDATE bookings SET status = 'awaiting_payment' WHERE id = ?");
                    $stmt->execute([$booking_id]);
                    $_SESSION['flash_success'] = "Booking ID #$booking_id approved for payment.";
                } else {
                    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
                    $stmt->execute([':status' => $status, ':id' => $booking_id]);
                    $_SESSION['flash_success'] = "Booking ID #$booking_id status updated to '$status'.";
                }
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error updating booking: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=bookings");
        exit();
    }

    // Staff/Admin - Update Custom Travel Plan Status
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'update_custom_plan_status') {
        $plan_id = intval($_POST['plan_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if (in_array($status, ['pending', 'reviewed', 'contacted'])) {
            try {
                $stmt = $pdo->prepare("UPDATE custom_itineraries SET status = :status WHERE id = :id");
                $stmt->execute([':status' => $status, ':id' => $plan_id]);
                $_SESSION['flash_success'] = "Custom plan ID #$plan_id status updated to '$status'.";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error updating custom plan status: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=custom_plans");
        exit();
    }

    

    // ---------------------------------------------------------
    // ADMIN ONLY ACTIONS
    // ---------------------------------------------------------
    
    // Admin Only - Promote/Demote User Role
    if ($user_role === 'admin' && $action === 'change_role') {
        $target_user_id = intval($_POST['target_user_id'] ?? 0);
        $new_role = trim($_POST['new_role'] ?? '');

        if ($target_user_id === $user_id) {
            $_SESSION['flash_error'] = "You cannot modify your own Administrator role.";
        } elseif (in_array($new_role, ['customer', 'staff', 'admin'])) {
            try {
                // Fetch target user current role
                $fetch_stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
                $fetch_stmt->execute([':id' => $target_user_id]);
                $target_user = $fetch_stmt->fetch();

                if (!$target_user) {
                    $_SESSION['flash_error'] = "Target user not found.";
                } elseif ($target_user['role'] === 'admin' || $target_user['role'] === 'staff') {
                    $_SESSION['flash_error'] = "You cannot change the role of an Administrator or Staff member after creation.";
                } elseif ($target_user['role'] === 'customer' && ($new_role === 'admin' || $new_role === 'staff')) {
                    $_SESSION['flash_error'] = "You cannot promote a customer to Administrator or Staff.";
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
                    $stmt->execute([':role' => $new_role, ':id' => $target_user_id]);
                    $_SESSION['flash_success'] = "User role modified successfully.";
                }
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error updating role: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=users");
        exit();
    }

    // Admin Only - Add Staff/Admin User
    if ($user_role === 'admin' && $action === 'add_staff_admin') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $contact = trim($_POST['contact'] ?? '');
        $role = trim($_POST['role'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($contact) || !in_array($role, ['staff', 'admin'])) {
            $_SESSION['flash_error'] = "Please fill in all fields correctly. Role must be Staff or Admin.";
        } elseif (strlen($password) < 6) {
            $_SESSION['flash_error'] = "Password must be at least 6 characters long.";
        } else {
            try {
                // Check if email already exists
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $check_stmt->execute([':email' => $email]);
                if ($check_stmt->rowCount() > 0) {
                    $_SESSION['flash_error'] = "A user with this email address already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, contact_number) VALUES (:name, :email, :password, :role, :contact)");
                    $stmt->execute([
                        ':name' => $name,
                        ':email' => $email,
                        ':password' => $hashed_password,
                        ':role' => $role,
                        ':contact' => $contact
                    ]);
                    $_SESSION['flash_success'] = "New " . ucfirst($role) . " user '$name' added successfully!";
                }
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error adding user: " . $e->getMessage();
            }
        }
    }

    // Admin Only - Delete User Account
    if ($user_role === 'admin' && $action === 'delete_user') {
        $target_user_id = intval($_GET['target_user_id'] ?? $_POST['target_user_id'] ?? 0);
        if ($target_user_id === $user_id) {
            $_SESSION['flash_error'] = "You cannot delete your own Administrator account.";
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $target_user_id]);
                $_SESSION['flash_success'] = "User account has been deleted successfully.";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error deleting user account: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=users");
        exit();
    }

    // Staff/Admin - Mark Inquiry Query as Replied
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'mark_query_replied') {
        $query_id = intval($_GET['query_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE queries SET status = 'replied' WHERE id = :id");
            $stmt->execute([':id' => $query_id]);
            $_SESSION['flash_success'] = "Query has been marked as Replied.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error updating query status: " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=queries");
        exit();
    }

    // Staff/Admin - Add Hotel
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'add_hotel') {
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $price = floatval($_POST['price_per_night'] ?? 0.0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if (empty($name) || empty($type) || $price <= 0 || empty($description)) {
            $errors[] = "All hotel text fields are required, and pricing must be positive.";
        }

        // Handle Image File Upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_ext, $allowed_exts)) {
                $errors[] = "Invalid image file type. Allowed: jpg, jpeg, png, gif, webp.";
            }
            if ($file_size < 10 * 1024) {
                $errors[] = "Image file size is too small. Minimum size required is 10KB.";
            } elseif ($file_size > 2 * 1024 * 1024) {
                $errors[] = "Image file size is too large. Maximum size allowed is 2MB.";
            }

            if (empty($errors)) {
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_file_name = uniqid('hotel_', true) . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $image_path = 'uploads/' . $new_file_name;
                } else {
                    $errors[] = "Failed to save uploaded file.";
                }
            }
        } else {
            $errors[] = "A valid hotel image upload is required.";
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode("<br>", $errors);
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO hotels (name, type, description, price_per_night, image) VALUES (:name, :type, :description, :price, :image)");
                $stmt->execute([
                    ':name' => $name,
                    ':type' => $type,
                    ':description' => $description,
                    ':price' => $price,
                    ':image' => $image_path
                ]);
                $_SESSION['flash_success'] = "Hotel '$name' added successfully!";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error adding hotel: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }

    // Staff/Admin - Delete Hotel
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'delete_hotel') {
        $id = intval($_GET['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = "Hotel deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error deleting hotel. It might be linked to active bookings: " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }

    // Staff/Admin - Add Vehicle
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'add_vehicle') {
        $model = trim($_POST['model'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $price = floatval($_POST['price_per_day'] ?? 0.0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if (empty($model) || empty($type) || $price <= 0 || empty($description)) {
            $errors[] = "All vehicle text fields are required, and pricing must be positive.";
        }

        // Handle Image File Upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_ext, $allowed_exts)) {
                $errors[] = "Invalid image file type. Allowed: jpg, jpeg, png, gif, webp.";
            }
            if ($file_size < 10 * 1024) {
                $errors[] = "Image file size is too small. Minimum size required is 10KB.";
            } elseif ($file_size > 2 * 1024 * 1024) {
                $errors[] = "Image file size is too large. Maximum size allowed is 2MB.";
            }

            if (empty($errors)) {
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_file_name = uniqid('veh_', true) . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $image_path = 'uploads/' . $new_file_name;
                } else {
                    $errors[] = "Failed to save uploaded file.";
                }
            }
        } else {
            $errors[] = "A valid vehicle image upload is required.";
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode("<br>", $errors);
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO vehicles (model, type, description, price_per_day, image) VALUES (:model, :type, :description, :price, :image)");
                $stmt->execute([
                    ':model' => $model,
                    ':type' => $type,
                    ':description' => $description,
                    ':price' => $price,
                    ':image' => $image_path
                ]);
                $_SESSION['flash_success'] = "Vehicle model '$model' added successfully!";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error adding vehicle: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }

    // Staff/Admin - Delete Vehicle
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'delete_vehicle') {
        $id = intval($_GET['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = "Vehicle record deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error deleting vehicle. It might be linked to active bookings: " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }

    // Staff/Admin - Add Guide
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'add_guide') {
        $name = trim($_POST['name'] ?? '');
        $languages = trim($_POST['languages'] ?? '');
        $price = floatval($_POST['price_per_day'] ?? 0.0);
        $rating = floatval($_POST['rating'] ?? 5.0);

        $errors = [];
        if (empty($name) || empty($languages) || $price <= 0 || $rating < 1.0 || $rating > 5.0) {
            $errors[] = "All guide text fields are required, pricing must be positive, and rating must be between 1.0 and 5.0.";
        }

        // Handle Image File Upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_ext, $allowed_exts)) {
                $errors[] = "Invalid image file type. Allowed: jpg, jpeg, png, gif, webp.";
            }
            if ($file_size < 10 * 1024) {
                $errors[] = "Image file size is too small. Minimum size required is 10KB.";
            } elseif ($file_size > 2 * 1024 * 1024) {
                $errors[] = "Image file size is too large. Maximum size allowed is 2MB.";
            }

            if (empty($errors)) {
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_file_name = uniqid('guide_', true) . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $image_path = 'uploads/' . $new_file_name;
                } else {
                    $errors[] = "Failed to save uploaded file.";
                }
            }
        } else {
            $errors[] = "A valid guide image upload is required.";
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode("<br>", $errors);
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO tour_guides (name, languages, rating, price_per_day, image) VALUES (:name, :languages, :rating, :price, :image)");
                $stmt->execute([
                    ':name' => $name,
                    ':languages' => $languages,
                    ':rating' => $rating,
                    ':price' => $price,
                    ':image' => $image_path
                ]);
                $_SESSION['flash_success'] = "Tour guide '$name' added successfully!";
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = "Error adding tour guide: " . $e->getMessage();
            }
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }

    // Staff/Admin - Delete Guide
    if (($user_role === 'admin' || $user_role === 'staff') && $action === 'delete_guide') {
        $id = intval($_GET['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM tour_guides WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = "Tour guide record deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error deleting guide. It might be linked to active bookings: " . $e->getMessage();
        }
        header("Location: dashboard.php?tab=services");
        exit();
    }
}
