<?php
class AccessManager {
    private $storage;
    public $max_attempts = 5;
    
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
            $this->logEvent("New registration: $username");
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
        
        // Keep only the last 50 log entries
        if (count($this->storage['activity_log']) > 50) {
            array_shift($this->storage['activity_log']);
        }
    }
    
    public function getUserProfiles() {
        return $this->storage['user_profiles'];
    }
    
    public function getActivityLog() {
        return array_slice($this->storage['activity_log'], -10);
    }
    
    public function clearLogs() {
        $this->storage['activity_log'] = [];
        return true;
    }
}
?>
