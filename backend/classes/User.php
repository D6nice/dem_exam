<?php
declare(strict_types=1);

class User
{
    public static function findByLogin(string $login): ?array
    {
        return db()->fetchOne('SELECT * FROM users WHERE login = ?', [$login]);
    }

    public static function findById(int $id): ?array
    {
        return db()->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function loginExists(string $login): bool
    {
        return db()->fetchOne('SELECT id FROM users WHERE login = ?', [$login]) !== null;
    }

    public static function create(array $data): int
    {
        db()->query(
            'INSERT INTO users (login, password_hash, full_name, phone, email, role)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['login'],
                $data['password_hash'],
                $data['full_name'],
                $data['phone'],
                $data['email'],
                'client',
            ]
        );

        return db()->lastInsertId();
    }

    public static function ensureAdmin(string $login, string $password): void
    {
        $existing = self::findByLogin($login);

        if ($existing !== null) {
            if (($existing['role'] ?? '') !== 'admin') {
                db()->query('UPDATE users SET role = ? WHERE id = ?', ['admin', $existing['id']]);
            }
            return;
        }

        db()->query(
            'INSERT INTO users (login, password_hash, full_name, phone, email, role)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $login,
                password_hash($password, PASSWORD_DEFAULT),
                'Администратор клиники',
                '',
                '',
                'admin',
            ]
        );
    }
}