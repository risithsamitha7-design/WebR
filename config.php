<?php
// GlobeTrek Adventures Configuration File

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'globetrek_db';

try {
    // Connect to database using PDO
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Log the error securely to system log
    error_log("Database Connection Failed: " . $e->getMessage());
    
    // Display an elegant, user-friendly, and secure maintenance message
    die('<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - GlobeTrek Adventures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-card { max-width: 500px; padding: 2.5rem; background: white; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; border: 1px solid rgba(0,0,0,0.05); }
        .icon-circle { width: 80px; height: 80px; border-radius: 50%; background-color: #fef2f2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 2.5rem; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h3 class="fw-bold text-dark mb-2">Database Connection Failed</h3>
        <p class="text-secondary mb-4">We are currently experiencing database server connectivity difficulties. Our technical staff has been notified. Please try reloading the page in a few minutes.</p>
        <button onclick="window.location.reload();" class="btn btn-primary px-4 py-2 rounded-pill"><i class="bi bi-arrow-clockwise me-1"></i>Retry Loading</button>
    </div>
</body>
</html>');
}
?>
