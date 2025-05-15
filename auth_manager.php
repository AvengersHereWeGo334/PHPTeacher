<?php
class AccessManager {
    private $storage;
    private $max_attempts = 5;
    
    public function __construct(&$container) {
        $this->storage = &$container;
    }
    
    public function registerUser($username, $password) {
        if ($this->isLocked()) return false;

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
        if ($this->isLocked()) return false;

        foreach ($this->storage['user_profiles'] as &$profile) {
            if ($profile['username'] === $username && $profile['password'] === $password) {
                $profile['last_login'] = date('Y-m-d H:i:s');
                $this->storage['failed_attempts'] = 0;
                $this->logEvent("Successful login: $username");
                return true;
            }
        }

        $this->storage['failed_attempts']++;
        $this->logEvent("Failed login: $username");
        return false;
    }
    
    // ... (other methods from previous example)
}
?>
