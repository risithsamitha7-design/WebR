<?php
/**
 * GlobeTrek Central Portal - Manage Users View (Admin Only)
 * Allows Administrators to view user roles, add new staff/admins, and observe security constraints.
 */

// Ensure configuration is active
require_once __DIR__ . '/../../config.php';

try {
    // Fetch all users sorted by ID
    $users_list = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading users: " . $e->getMessage() . "</div>";
    $users_list = [];
}
?>

<!-- Add Staff/Admin Account Form -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-plus-fill text-success me-2"></i>Create Staff or Admin Account</h5>
    <form action="dashboard.php?tab=users" method="POST" class="row g-3 needs-validation" novalidate>
        <input type="hidden" name="action" value="add_staff_admin">
        
        <div class="col-md-6 col-lg-3">
            <label for="new_usr_name" class="form-label small fw-bold text-muted mb-1">Full Name</label>
            <input type="text" id="new_usr_name" name="name" class="form-control form-control-sm rounded-3" required placeholder="E.g. Samantha Perera">
        </div>
        
        <div class="col-md-6 col-lg-3">
            <label for="new_usr_email" class="form-label small fw-bold text-muted mb-1">Email Address</label>
            <input type="email" id="new_usr_email" name="email" class="form-control form-control-sm rounded-3" required placeholder="email@globetrek.com">
        </div>
        
        <div class="col-md-6 col-lg-2">
            <label for="new_usr_contact" class="form-label small fw-bold text-muted mb-1">Contact Number</label>
            <input type="text" id="new_usr_contact" name="contact" class="form-control form-control-sm rounded-3" required placeholder="+94 77 123 4567">
        </div>
        
        <div class="col-md-6 col-lg-2">
            <label for="new_usr_password" class="form-label small fw-bold text-muted mb-1">Password</label>
            <input type="password" id="new_usr_password" name="password" class="form-control form-control-sm rounded-3" required placeholder="Min 6 characters">
        </div>
        
        <div class="col-md-6 col-lg-2">
            <label for="new_usr_role" class="form-label small fw-bold text-muted mb-1">Assigned Role</label>
            <select id="new_usr_role" name="role" class="form-select form-select-sm rounded-3" required>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        
        <div class="col-12 text-end mt-3">
            <button type="submit" class="btn btn-success btn-sm px-4 rounded-pill shadow-sm"><i class="bi bi-plus-circle me-1"></i> Create Account</button>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
    <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-people-fill text-success me-2"></i>Manage User Accounts & Roles</h4>
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Current Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users_list as $usr): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($usr['name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($usr['email']); ?></code></td>
                        <td><?php echo htmlspecialchars($usr['contact_number'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge text-capitalize <?php 
                                echo ($usr['role'] === 'admin') ? 'bg-danger-subtle text-danger' : (($usr['role'] === 'staff') ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'); 
                            ?>">
                                <?php echo $usr['role']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($usr['id'] === $user_id): ?>
                                <span class="text-muted small">You (Self)</span>
                            <?php else: ?>
                                <a href="dashboard.php?action=delete_user&target_user_id=<?php echo $usr['id']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill" onclick="return confirm('Are you sure you want to permanently delete the user account \'<?php echo htmlspecialchars(addslashes($usr['name'])); ?>\'? This action cannot be undone.');">
                                    <i class="bi bi-trash me-1"></i>Delete Account
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

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
