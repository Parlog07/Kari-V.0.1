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
                (host_id, title, description, city, address, price_per_night, max_guests, image_path)
                VALUES
                (:host_id, :title, :description, :city, :address, :price, :max_guests, :image_path)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'host_id'     => $data['host_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'city'        => $data['city'],
            'address'     => $data['address'],
            'price'       => $data['price_per_night'],
            'max_guests'  => $data['max_guests'],
            'image_path'  => $data['image_path']
        ]);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM rentals
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $rental = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rental ?: null;
    }

    public function findByHost(int $hostId): array
    {
        $sql = "SELECT *
                FROM rentals
                WHERE host_id = :host_id
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['host_id' => $hostId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            'title'      => $data['title'],
            'description'=> $data['description'],
            'city'       => $data['city'],
            'address'    => $data['address'],
            'price'      => $data['price_per_night'],
            'max_guests' => $data['max_guests'],
            'id'         => $rentalId,
            'host_id'    => $hostId
        ]);
    }

    public function delete(int $rentalId, int $hostId): bool
    {
        $sql = "DELETE FROM rentals
                WHERE id = :id AND host_id = :host_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id'      => $rentalId,
            'host_id' => $hostId
        ]);
    }

    public function findAllActive(): array
    {
        $sql = "SELECT *
                FROM rentals
                WHERE is_active = 1
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(array $filters, int $limit = 6, int $offset = 0): array
    {
        $sql = "SELECT r.*
                FROM rentals r
                WHERE r.is_active = 1";

        $params = [];

        if (!empty($filters['city'])) {
            $sql .= " AND r.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND r.price_per_night >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND r.price_per_night <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND r.id NOT IN (
                SELECT b.rental_id
                FROM bookings b
                WHERE b.status = 'confirmed'
                  AND b.start_date < :end_date
                  AND b.end_date > :start_date
            )";

            $params['start_date'] = $filters['start_date'];
            $params['end_date']   = $filters['end_date'];
        }

        $sql .= " ORDER BY r.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearchResults(array $filters): int
    {
        $sql = "SELECT COUNT(*)
                FROM rentals r
                WHERE r.is_active = 1";

        $params = [];

        if (!empty($filters['city'])) {
            $sql .= " AND r.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND r.price_per_night >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND r.price_per_night <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND r.id NOT IN (
                SELECT b.rental_id
                FROM bookings b
                WHERE b.status = 'confirmed'
                  AND b.start_date < :end_date
                  AND b.end_date > :start_date
            )";

            $params['start_date'] = $filters['start_date'];
            $params['end_date']   = $filters['end_date'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
