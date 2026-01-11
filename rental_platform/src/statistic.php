<?php

class Statistics
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getTotalUsers(): int
    {
        return (int)$this->pdo
            ->query("SELECT COUNT() FROM users")
            ->fetchColumn();
    }
    public function getTotalRentals(): int
    {
        return (int)$this->pdo
            ->query("SELECT COUNT() FROM rentals")
            ->fetchColumn();
    }
    public function getTotalBookings(): int
    {
        return (int)$this->pdo
            ->query("SELECT COUNT(*) FROM bookings")
            ->fetchColumn();
    }
    public function getTotalRevenue(): float
    {
        return (float)$this->pdo
            ->query("
                SELECT COALESCE(SUM(total_price), 0)
                FROM bookings
                WHERE status = 'confirmed'
            ")
            ->fetchColumn();
    }
    public function getTopRentals(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.title, SUM(b.total_price) AS revenue
            FROM bookings b
            JOIN rentals r ON r.id = b.rental_id
            WHERE b.status = 'confirmed'
            GROUP BY r.id
            ORDER BY revenue DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}