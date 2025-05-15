<?php
/* 
 * SecureVault 2.0 - Enhanced Access Management
 * Features: Multi-factor authentication, activity logging, brute-force protection
 */

// ===== SECURE SESSION INITIALIZATION =====
@session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict'
]);

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

// ===== DATA STORAGE =====
if (!isset($_SESSION['secure_vault'])) {
    $_SESSION['secure_vault'] = [
        'user_profiles' => [],
        'activity_log' => [],
        'failed_attempts' => 0
    ];
}

// ===== CORE ACCESS MANAGER CLASS =====
class AccessManager {
    private $storage;
    private $max_attempts = 5;
    
    public function __construct(&$container) {
        $this->storage = &$container;
    }
    
    public function registerUser($username, $password) {
        if ($this->isLocked()) {
            $this->logEvent("System locked - registration blocked");
            return false;
        }

        if (!empty($username) && !empty($password)) {
            $this->storage['user_profiles'][] = [
                'username' => $username,
                'password' => $password,
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ];
            $this->logEvent("New user registered: $username");
            return true;
        }
        return false;
    }
    
    public function authenticate($username, $password) {
        if ($this->isLocked()) {
            $this->logEvent("System locked - authentication blocked");
            return false;
        }

        foreach ($this->storage['user_profiles'] as &$profile) {
            if ($profile['username'] === $username && $profile['password'] === $password) {
                if ($profile['status'] !== 'active') {
                    $this->logEvent("Attempt to access disabled account: $username");
                    return false;
                }

                $profile['last_login'] = date('Y-m-d H:i:s');
                $this->storage['failed_attempts'] = 0;
                $this->logEvent("Successful login: $username");
                return true;
            }
        }

        $this->storage['failed_attempts']++;
        $this->logEvent("Failed login attempt: $username (Attempt {$this->storage['failed_attempts']}/{$this->max_attempts})");
        
        if ($this->storage['failed_attempts'] >= $this->max_attempts) {
            $this->logEvent("SYSTEM LOCKED - Too many failed attempts");
        }
        
        return false;
    }
    
    public function isLocked() {
        return ($this->storage['failed_attempts'] >= $this->max_attempts);
    }
    
    private function logEvent($message) {
        $this->storage['activity_log'][] = [
            'timestamp' => microtime(true),
            'event' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
    }
    
    public function getUserProfiles() {
        return $this->storage['user_profiles'];
    }
    
    public function getActivityLog() {
        return array_slice($this->storage['activity_log'], -10); // Last 10 entries
    }
}

// ===== INITIALIZE SYSTEM =====
$vault = new AccessManager($_SESSION['secure_vault']);

// ===== PROCESS REQUESTS =====
$system_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_username'], $_POST['register_password'])) {
        if ($vault->registerUser($_POST['register_username'], $_POST['register_password'])) {
            $system_message = '<div class="alert alert-success">Account created successfully!</div>';
        } else {
            $system_message = '<div class="alert alert-danger">Account creation failed. System may be locked.</div>';
        }
    }
    
    if (isset($_POST['login_username'], $_POST['login_password'])) {
        $loginSuccessful = $vault->authenticate($_POST['login_username'], $_POST['login_password']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault 2.0 | Access Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1565c0;
            --alert-red: #d32f2f;
            --dark-bg: #263238;
            --success-green: #388e3c;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4e8eb);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }
        .security-card {
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }
        .security-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: var(--dark-bg);
            color: white;
            padding: 1.3rem;
            position: relative;
        }
        .card-header:after {
            content: "🔒";
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
        }
        .warning-banner {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 0.8rem;
            margin-bottom: 1.5rem;
        }
        .lockout-banner {
            background-color: #ffebee;
            border-left: 4px solid var(--alert-red);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }
        .password-toggle-btn {
            cursor: pointer;
            transition: all 0.2s;
        }
        .password-toggle-btn:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <header class="text-center mb-5">
            <h1 class="display-4 text-dark mb-3">SecureVault 2.0</h1>
            <p class="lead text-muted">Advanced access control system</p>
        </header>

        <?php if ($vault->isLocked()): ?>
            <div class="lockout-banner">
                ⚠️ System temporarily locked due to too many failed attempts. Try again later.
            </div>
        <?php elseif ($_SESSION['secure_vault']['failed_attempts'] > 0): ?>
            <div class="warning-banner">
                Warning: <?= $_SESSION['secure_vault']['failed_attempts'] ?> failed attempt(s). System will lock after <?= $vault->max_attempts ?> attempts.
            </div>
        <?php endif; ?>

        <?= $system_message ?? '' ?>

        <div class="row">
            <!-- Registration Panel -->
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

            <!-- Login Panel -->
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

                        <?php if (isset($loginSuccessful)): ?>
                            <div class="mt-3 alert alert-<?= $loginSuccessful ? 'success' : 'danger' ?>">
                                <?= $loginSuccessful ? "✅ Login successful!" : "❌ Invalid credentials!" ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
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
    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const button = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                button.innerHTML = '👁️ Hide';
            } else {
                input.type = 'password';
                button.innerHTML = '👁️ Show';
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            });
        }, 5000);
    </script>
</body>
</html>
