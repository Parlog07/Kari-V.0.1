<?php

class Booking
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function checkAvailability(int $rentalId, string $startDate, string $endDate): bool {
        $sql = "SELECT COUNT(*) FROM bookings
                WHERE rental_id = :rental_id
                AND status = 'confirmed'
                AND (
                        start_date < :end_date
                    AND end_date > :start_date
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'rental_id' => $rentalId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $stmt->fetchColumn() == 0;
    }
    public function create(array $data): bool
    {
        if (!$this->checkAvailability(
            $data['rental_id'],
            $data['start_date'],
            $data['end_date']
        )) {
            throw new Exception("Rental not available for selected dates");
        }

        $days = (strtotime($data['end_date']) - strtotime($data['start_date'])) / 86400;
        $totalPrice = $days * $data['price_per_night'];

        $sql = "INSERT INTO bookings
                (rental_id, user_id, start_date, end_date, total_price)
                VALUES
                (:rental_id, :user_id, :start_date, :end_date, :total_price)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'rental_id' => $data['rental_id'],
            'user_id' => $data['user_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_price' => $totalPrice
        ]);
    }
    public function cancel(int $bookingId, array $user): bool
    {
        if ($user['role'] === 'admin') {
            $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $bookingId]);
        }

        $sql = "UPDATE bookings
                SET status = 'cancelled'
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $bookingId,
            'user_id' => $user['id']
        ]);
    }
    public function findUserBookings(int $userId): array
    {
        $sql = "SELECT b.*, r.title, r.city
                FROM bookings b
                JOIN rentals r ON b.rental_id = r.id
                WHERE b.user_id = :user_id
                ORDER BY b.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
