<?php
require_once "../../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);
$mailer = new Mailer();

if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("
        SELECT b.*, u.email, u.full_name, r.title
        FROM bookings b
        JOIN users u ON u.id = b.user_id
        JOIN rentals r ON r.id = b.rental_id
        WHERE b.id = :id
    ");
    $stmt->execute(['id' => $_GET['cancel']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $bookingModel->cancelByAdmin($booking['id']);
        $mailer->sendBookingCancellation(
            $booking['email'],
            $booking['full_name'],
            $booking['title'],
            $booking['start_date'],
            $booking['end_date']
        );
    }

    header("Location: bookings.php");
    exit;
}

$stmt = $pdo->query("
    SELECT b.*, u.full_name, r.title
    FROM bookings b
    JOIN users u ON u.id = b.user_id
    JOIN rentals r ON r.id = b.rental_id
");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<body>

<h2>Admin Bookings</h2>

<table border="1">
<tr>
    <th>User</th>
    <th>Rental</th>
    <th>Dates</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($bookings as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['full_name']) ?></td>
    <td><?= htmlspecialchars($b['title']) ?></td>
    <td><?= $b['start_date'] ?> → <?= $b['end_date'] ?></td>
    <td><?= $b['status'] ?></td>
    <td>
        <?php if ($b['status'] === 'confirmed'): ?>
            <a href="?cancel=<?= $b['id'] ?>">Cancel</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
