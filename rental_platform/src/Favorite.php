<?php

class Favorite
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(int $userId, int $rentalId): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO favorites (user_id, rental_id)
            VALUES (:user_id, :rental_id)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);
    }

    public function remove(int $userId, int $rentalId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM favorites
            WHERE user_id = :user_id AND rental_id = :rental_id
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);
    }

    public function isFavorite(int $userId, int $rentalId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM favorites
            WHERE user_id = :user_id AND rental_id = :rental_id
        ");

        $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function findUserFavorites(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.*
            FROM rentals r
            JOIN favorites f ON f.rental_id = r.id
            WHERE f.user_id = :user_id
        ");

        $stmt->execute([
            'user_id' => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
