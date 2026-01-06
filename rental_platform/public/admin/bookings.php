<?php
require_once "../../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);

if (isset($_GET['cancel'])) {
    $bookingModel->cancelByAdmin((int)$_GET['cancel']);
    header("Location: bookings.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT b.*, u.full_name, r.title
    FROM bookings b
    JOIN users u ON u.id = b.user_id
    JOIN rentals r ON r.id = b.rental_id
    ORDER BY b.created_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Bookings</title>
</head>
<body>

<h2>All Bookings</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>User</th>
        <th>Rental</th>
        <th>Dates</th>
        <th>Total price</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($bookings as $booking): ?>
        <tr>
            <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['title']); ?></td>
            <td>
                <?php echo $booking['start_date']; ?> →
                <?php echo $booking['end_date']; ?>
            </td>
            <td><?php echo $booking['total_price']; ?></td>
            <td><?php echo $booking['status']; ?></td>
            <td>
                <?php if ($booking['status'] === 'confirmed'): ?>
                    <a href="?cancel=<?php echo $booking['id']; ?>"
                       onclick="return confirm('Cancel this booking?');">
                        Cancel
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="../index.php">Back to site</a>

</body>
</html>
