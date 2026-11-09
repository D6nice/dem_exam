<?php
declare(strict_types=1);

class Review
{
    public static function forApplication(int $applicationId): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM reviews WHERE application_id = ?',
            [$applicationId]
        );
    }

    public static function create(int $applicationId, int $userId, string $text): int
    {
        db()->query(
            'INSERT INTO reviews (application_id, user_id, text) VALUES (?, ?, ?)',
            [$applicationId, $userId, $text]
        );

        return db()->lastInsertId();
    }
}