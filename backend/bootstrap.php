<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

date_default_timezone_set(APP_TIMEZONE);

spl_autoload_register(static function (string $class): void {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function old(string $key, string $default = ''): string
{
    return (string)($_SESSION['old'][$key] ?? $default);
}

function keep_old(array $fields): void
{
    foreach ($fields as $field) {
        $_SESSION['old'][$field] = $_POST[$field] ?? '';
    }
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function db(): Database
{
    return Database::instance();
}

function auth(): Auth
{
    return Auth::instance();
}

User::ensureAdmin(ADMIN_LOGIN, ADMIN_PASSWORD);