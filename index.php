<?php
require_once 'auth_manager.php';
require_once 'functions.php';

initSecureSession();
$vault = new AccessManager($_SESSION['secure_vault']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_username'], $_POST['register_password'])) {
        $registration_result = $vault->registerUser($_POST['register_username'], $_POST['register_password']);
        $_SESSION['system_message'] = $registration_result ? 
            '<div class="alert alert-success">Account created successfully!</div>' : 
            '<div class="alert alert-danger">Account creation failed. System may be locked.</div>';
    }
    
    if (isset($_POST['login_username'], $_POST['login_password'])) {
        $login_result = $vault->authenticate($_POST['login_username'], $_POST['login_password']);
        $_SESSION['login_result'] = $login_result;
    }
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$system_message = $_SESSION['system_message'] ?? '';
$login_result = $_SESSION['login_result'] ?? null;
unset($_SESSION['system_message'], $_SESSION['login_result']);

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
    <header class="text-center mb-5">
        <h1 class="display-4 text-dark mb-3">SecureVault 2.0</h1>
        <p class="lead text-muted">Advanced access control system</p>
    </header>

    <div class="container">
        <?php if ($is_locked): ?>
            <div class="lockout-banner">
                ⚠️ System temporarily locked due to too many failed attempts. Try again later.
            </div>
        <?php elseif ($failed_attempts > 0): ?>
            <div class="warning-banner">
                Warning: <?= $failed_attempts ?> failed attempt(s). System will lock after <?= $vault->max_attempts ?> attempts.
            </div>
        <?php endif; ?>

        <?= $system_message ?>

        <?php if (isset($login_result)): ?>
            <div class="mt-3 alert alert-<?= $login_result ? 'success' : 'danger' ?>">
                <?= $login_result ? "✅ Login successful!" : "❌ Invalid credentials!" ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6">
                <div class="security-card">
                    <div class="card-header">
                        <h3 class="mb-0">Create New Account</h3>
                    </div>
                    <div class="p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="register_username" class="form-control" required 
                                       placeholder="Choose your username" minlength="3">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="register_password" id="regPassword" class="form-control" required
                                           placeholder="Minimum 6 characters" minlength="6">
                                    <button type="button" class="btn btn-outline-secondary password-toggle-btn"
                                            onclick="togglePassword('regPassword')">
                                        👁️ Show
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Register Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="security-card">
                    <div class="card-header">
                        <h3 class="mb-0">User Login</h3>
                    </div>
                    <div class="p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="login_username" class="form-control" required
                                       placeholder="Enter your username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="login_password" id="loginPassword" class="form-control" required
                                           placeholder="Enter your password">
                                    <button type="button" class="btn btn-outline-secondary password-toggle-btn"
                                            onclick="togglePassword('loginPassword')">
                                        👁️ Show
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="security-card">
                    <div class="card-header">
                        <h3 class="mb-0">Activity Log</h3>
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($vault->getActivityLog() as $log): ?>
                                <li class="list-group-item small">
                                    <span class="text-muted">[<?= date('H:i:s', (int)$log['timestamp']) ?>]</span>
                                    <?= htmlspecialchars($log['event']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="security-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">User Accounts</h3>
                        <span class="badge bg-light text-dark">
                            <?= count($vault->getUserProfiles()) ?> registered
                        </span>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Created</th>
                                        <th>Last Active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vault->getUserProfiles() as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(substr($user['username'], 0, 3)) ?>***</td>
                                            <td><?= $user['created_at'] ?></td>
                                            <td><?= $user['last_login'] ?? 'Never' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 text-center text-muted small">
        <p>SecureVault 2.0 | <?= date('Y') ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
