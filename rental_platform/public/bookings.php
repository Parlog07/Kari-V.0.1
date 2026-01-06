<?php
require_once "../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);
$mailer = new Mailer();

if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("
        SELECT b.*, r.title
        FROM bookings b
        JOIN rentals r ON r.id = b.rental_id
        WHERE b.id = :id AND b.user_id = :user_id
    ");
    $stmt->execute([
        'id' => $_GET['cancel'],
        'user_id' => $_SESSION['user_id']
    ]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $bookingModel->cancelByUser($booking['id'], $_SESSION['user_id']);
        $mailer->sendBookingCancellation(
            $_SESSION['email'],
            $_SESSION['full_name'],
            $booking['title'],
            $booking['start_date'],
            $booking['end_date']
        );
    }

    header("Location: bookings.php");
    exit;
}

$bookings = $bookingModel->findByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<body>

<h2>My Bookings</h2>

<table border="1">
    <tr>
        <th>Rental</th>
        <th>Dates</th>
        <th>Total</th>
        <th>Status</th>
        <th>Action</th>
        <th>Receipt</th>
    </tr>

<?php foreach ($bookings as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['title']) ?></td>
    <td><?= $b['start_date'] ?> → <?= $b['end_date'] ?></td>
    <td><?= $b['total_price'] ?></td>
    <td><?= $b['status'] ?></td>
    <td>
        <?php if ($b['status'] === 'confirmed'): ?>
            <a href="?cancel=<?= $b['id'] ?>">Cancel</a>
        <?php endif; ?>
    </td>
    <td>
        <a href="receipt.php?id=<?= $b['id'] ?>">PDF</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
