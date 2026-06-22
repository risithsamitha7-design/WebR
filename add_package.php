<?php
/**
 * GlobeTrek Adventures - Add Travel Package Form Page
 * Admin and Staff form to create a new tour package catalog entry
 */

require_once __DIR__ . '/includes/header.php';

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "You must log in to access this page.";
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// 2. Ensure user has staff or admin role
if ($user_role !== 'staff' && $user_role !== 'admin') {
    $_SESSION['flash_error'] = "Only staff or admin accounts can add packages.";
    header("Location: dashboard.php");
    exit();
}

// 3. Process form submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $price = floatval($_POST['price'] ?? 0.0);
    $duration = trim($_POST['duration'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($image_url)) {
        $image_url = 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80';
    }

    $errors = [];
    if (empty($title)) {
        $errors[] = "Package title is required.";
    } elseif (strlen($title) < 3 || strlen($title) > 150) {
        $errors[] = "Title must be between 3 and 150 characters.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    } elseif (strlen($description) < 10 || strlen($description) > 2000) {
        $errors[] = "Description must be between 10 and 2000 characters.";
    }

    if (empty($destination)) {
        $errors[] = "Destination is required.";
    } elseif (strlen($destination) < 3 || strlen($destination) > 100) {
        $errors[] = "Destination must be between 3 and 100 characters.";
    }

    if ($price <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    if (empty($duration)) {
        $errors[] = "Duration is required.";
    } elseif (strlen($duration) < 2 || strlen($duration) > 50) {
        $errors[] = "Duration description must be between 2 and 50 characters.";
    } elseif (stripos($duration, '1 day') !== false || preg_match('/^\s*1\s*day/i', $duration)) {
        $errors[] = "Only tours of 2 days or more are allowed. 1-day tours are not supported.";
    }

    // Handle Image Upload or URL
    $final_image_url = '';

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        $file_size = $_FILES['image_file']['size'];
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

        // Validate image dimensions
        $img_info = getimagesize($file_tmp);
        if ($img_info === false) {
            $errors[] = "Uploaded file is not a valid image.";
        } else {
            $width = $img_info[0];
            $height = $img_info[1];
            
            if ($width < 400) {
                $errors[] = "Image width is too small ({$width}px). Minimum required width is 400px.";
            } elseif ($width > 2560) {
                $errors[] = "Image width is too large ({$width}px). Maximum allowed width is 2560px.";
            }
            
            if ($height < 300) {
                $errors[] = "Image height is too small ({$height}px). Minimum required height is 300px.";
            } elseif ($height > 2048) {
                $errors[] = "Image height is too large ({$height}px). Maximum allowed height is 2048px.";
            }
        }

        if (empty($errors)) {
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_file_name = uniqid('pkg_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $dest_path)) {
                $final_image_url = 'uploads/' . $new_file_name;
            } else {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    } elseif (!empty($image_url)) {
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            $errors[] = "Please provide a valid image URL.";
        } else {
            $final_image_url = $image_url;
        }
    } else {
        $final_image_url = 'uploads/sigiriya.png';
    }

    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode("<br>", $errors);
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO packages (title, description, destination, price, duration, image_url) VALUES (:title, :description, :destination, :price, :duration, :image_url)");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':destination' => $destination,
                ':price' => $price,
                ':duration' => $duration,
                ':image_url' => $final_image_url
            ]);
            $_SESSION['flash_success'] = "New travel package '$title' added successfully!";
            header("Location: dashboard.php?tab=packages");
            exit();
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Error adding package: " . $e->getMessage();
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #2d8a4e 0%, #1b5e20 100%);">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Travel Package</h4>
                </div>
                
                <div class="card-body p-4">
                    <form action="add_package.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Package Title</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g. Negombo Canal Sightseeing" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Duration</label>
                            <input type="text" class="form-control" name="duration" placeholder="e.g. 1 Day, 3 Days / 2 Nights" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Destination</label>
                            <input type="text" class="form-control" name="destination" placeholder="e.g. Negombo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Price (Rs)</label>
                            <input type="number" class="form-control" name="price" step="0.01" min="1" placeholder="e.g. 15000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Package Image</label>
                            <div class="card bg-light border-0 p-3 rounded-3 mb-2">
                                <div class="row g-2">
                                    <div class="col-md-6 border-end">
                                        <label class="form-label small text-muted"><i class="bi bi-link-45deg me-1"></i>Option A: Enter Image URL</label>
                                        <input type="url" class="form-control bg-white" name="image_url" placeholder="https://images.unsplash.com/... (optional)">
                                    </div>
                                    <div class="col-md-6 ps-md-3">
                                        <label class="form-label small text-muted"><i class="bi bi-upload me-1"></i>Option B: Upload from PC</label>
                                        <input type="file" class="form-control bg-white" name="image_file" accept="image/*">
                                    </div>
                                </div>
                                <div class="form-text mt-2 small text-secondary" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i> Uploading a file takes priority. Allowed: JPG, JPEG, PNG, WEBP, GIF (10KB to 2MB, width 400-2560px, height 300-2048px).
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Description</label>
                            <textarea class="form-control" name="description" rows="5" placeholder="Enter sights, inclusions..." required></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="dashboard.php?tab=packages" class="btn btn-outline-secondary w-50 py-2.5">Cancel</a>
                            <button type="submit" class="btn btn-primary w-50 py-2.5">Add Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
