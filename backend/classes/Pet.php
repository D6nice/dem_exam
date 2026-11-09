<?php
declare(strict_types=1);

class Pet
{
    public const SPECIES = ['кошка', 'собака', 'грызун', 'птица', 'другое'];

    public static function forUser(int $userId): array
    {
        return db()->fetchAll(
            'SELECT * FROM pets WHERE user_id = ? ORDER BY id',
            [$userId]
        );
    }

    public static function find(int $id): ?array
    {
        return db()->fetchOne('SELECT * FROM pets WHERE id = ?', [$id]);
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM pets WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );
    }

    public static function create(int $userId, string $name, string $species): int
    {
        db()->query(
            'INSERT INTO pets (user_id, name, species) VALUES (?, ?, ?)',
            [$userId, $name, $species]
        );

        return db()->lastInsertId();
    }
}