<?php
declare(strict_types=1);

class Auth
{
    private static ?Auth $instance = null;

    public static function instance(): Auth
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        return true;
    }

    public function attemptAdmin(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function id(): ?int
    {
        return $this->check() ? (int)$_SESSION['user_id'] : null;
    }

    public function user(): ?array
    {
        $id = $this->id();
        return $id === null ? null : User::findById($id);
    }

    public function isAdmin(): bool
    {
        $user = $this->user();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }
}