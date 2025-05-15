<?php
require_once 'auth_manager.php';
require_once 'functions.php';

initSecureSession();
$vault = new AccessManager($_SESSION['secure_vault']);

processAuthRequests($vault);

$system_message = getSystemMessage();
$is_locked = $vault->isLocked();
$failed_attempts = $_SESSION['secure_vault']['failed_attempts'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault 2.0 | Access Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="py-4">
    <?php include 'header.php'; ?>
    
    <div class="container">
        <?php include 'alerts.php'; ?>
        
        <div class="row">
            <div class="col-lg-6">
                <?php include 'register_form.php'; ?>
            </div>
            <div class="col-lg-6">
                <?php include 'login_form.php'; ?>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <?php include 'activity_log.php'; ?>
            </div>
            <div class="col-md-6">
                <?php include 'user_table.php'; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
