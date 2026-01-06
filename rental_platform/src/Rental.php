<?php

class Rental
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO rentals 
                (host_id, title, description, city, address, price_per_night, max_guests)
                VALUES 
                (:host_id, :title, :description, :city, :address, :price, :max_guests)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'host_id' => $data['host_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'city' => $data['city'],
            'address' => $data['address'],
            'price' => $data['price_per_night'],
            'max_guests' => $data['max_guests']
        ]);
    }
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM rentals WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $rental = $stmt->fetch(PDO::FETCH_ASSOC);

        return $rental ?: null;
    }
    public function update(int $rentalId, int $hostId, array $data): bool
    {
        $sql = "UPDATE rentals SET
                    title = :title,
                    description = :description,
                    city = :city,
                    address = :address,
                    price_per_night = :price,
                    max_guests = :max_guests
                WHERE id = :id AND host_id = :host_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'city' => $data['city'],
            'address' => $data['address'],
            'price' => $data['price_per_night'],
            'max_guests' => $data['max_guests'],
            'id' => $rentalId,
            'host_id' => $hostId
        ]);
    }
    public function delete(int $rentalId, int $hostId): bool
    {
        $sql = "DELETE FROM rentals WHERE id = :id AND host_id = :host_id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $rentalId,
            'host_id' => $hostId
        ]);
    }
    public function findByHost(int $hostId): array
    {
        $sql = "SELECT *
                FROM rentals
                WHERE host_id = :host_id
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'host_id' => $hostId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}