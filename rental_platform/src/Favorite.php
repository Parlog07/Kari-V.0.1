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
        $sql = "INSERT INTO favorites (user_id, rental_id)
                VALUES (:user_id, :rental_id)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);
    }
    public function remove(int $userId, int $rentalId): bool
    {
        $sql = "DELETE FROM favorites
                WHERE user_id = :user_id AND rental_id = :rental_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);
    }
    public function isFavorite(int $userId, int $rentalId): bool
    {
        $sql = "SELECT 1 FROM favorites
                WHERE user_id = :user_id AND rental_id = :rental_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'rental_id' => $rentalId
        ]);

        return (bool) $stmt->fetchColumn();
    }
    public function getUserFavorites(int $userId): array
    {
        $sql = "SELECT r.*
                FROM favorites f
                JOIN rentals r ON f.rental_id = r.id
                WHERE f.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
