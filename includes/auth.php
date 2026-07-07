<?php
/*
    Author: Tuong Nguyen Pham
    Student ID: 110192780
    COMP 3340 - Web Development
    Couse Project
    HTML5, CSS, JS, PHP, MySQL
*/
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/config.php';

    // Create a session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    /*
    1. Session state
    */
    function current_user_id(): ?int {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    function is_logged_in(): bool {
        return current_user_id() !== null;
    }

    function is_admin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    function is_client(): bool {
        return is_logged_in() && current_user_role() === 'client';
    }

    function current_user_role(): string {
        return isset($_SESSION['role']) ? $_SESSION['role'] : 'client';
    }

    function current_user_email(): string {
        return isset($_SESSION['email']) ? $_SESSION['email'] : '';
    }

    
    /*
    2. Page guards
    */
    function require_login(string $redirectTo = '/client/login.php'): void {
        if (!is_logged_in()) {
            header('Location: ' . $redirectTo . '?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    function require_admin(): void {
        require_login();
        if (!is_admin()) {
            http_response_code(403);
            exit("Access denied. You must be an admin to access this page.");
        }
    }

    function redirect_if_logged_in(string $redirectTo = '../index.php'): void {
        if (is_logged_in()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }


    /*
    3. Login and logout
    */
    function login_user(array $userData): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['role'] = $userData['role'];
    }

    function logout_user(): void {
        $_SESSION = [];
        session_destroy();
        
        // clear other cookies
        setcookie('dinespot_theme', '', time() - 3600, '/');
    }


    /*
    4. Password management
    */
    function hash_password(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    function verify_password(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /*
    5. Database functions
    */
    function get_user_by_email(string $email): ?array {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    function get_user_by_id(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    function create_user(array $userData): bool {
        $email = $userData['email'];
        $password = $userData['password'];
        $full_name = $userData['full_name'];
        $role = $userData['role'];
        $status = $userData['status'];
        $phone = $userData['phone'];

        if (email_exists($email)) {
            return false;
        }
        
        $password_hash = hash_password($password);
        $stmt = db()->prepare('INSERT INTO users (email, password_hash, full_name, role, status, phone) VALUES (:email, :password_hash, :full_name, :role, :status, :phone)');
        $stmt->execute(['email' => $email, 'password_hash' => $password_hash, 'full_name' => $full_name, 'role' => $role, 'status' => $status, 'phone' => $phone]);
        return $stmt->rowCount() > 0;
    }

    function update_user(int $id, array $userData): bool {
        $email = $userData['email'];
        $full_name = $userData['full_name'];
        $status = $userData['status'];
        $phone = $userData['phone'];
        $stmt = db()->prepare('UPDATE users SET email = :email, full_name = :full_name, status = :status, phone = :phone WHERE id = :id');
        $stmt->execute(['email' => $email, 'full_name' => $full_name, 'status' => $status, 'phone' => $phone, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }

    function update_user_status(int $id, string $status): bool {
        $stmt = db()->prepare('UPDATE users SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }

    function update_user_password(int $id, string $password): bool {
        $stmt = db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'password_hash' => hash_password($password),
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    function delete_user(int $id): bool {
        $stmt = db()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    function get_all_users(): array {
        $stmt = db()->prepare('SELECT * FROM users');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function email_exists(string $email): bool {
        $user = get_user_by_email($email);
        return $user ? true : false;
    }

    function logged_in_user(): ?array {
        $id = current_user_id();
        if (!$id) {
            return null;
        }

        return get_user_by_id($id);
    }

    function is_account_active(int $id): bool {
        $stmt = db()->prepare('SELECT status FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ? $user['status'] === 'active' : false;
    }

    function authenticate(string $email, string $password): array {
        $user = get_user_by_email($email);
        if (!$user || !verify_password($password, $user['password_hash'])) {
            return ['error' => 'Invalid email or password.'];
        }
        if (!is_account_active((int) $user['id'])) {
            return ['error' => 'Your account has been suspended. Please contact support at ' . SITE_SUPPORT_EMAIL . ' during support hours (' . SITE_SUPPORT_HOURS . ').'];
        }
        return ['user' => $user];
    }



    


