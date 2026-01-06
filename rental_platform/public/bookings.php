<?php
require_once "../config/autoload.php";
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);

// Handle cancel action
if (isset($_GET['cancel'])) {
    $bookingId = (int) $_GET['cancel'];
    $bookingModel->cancelByUser($bookingId, $_SESSION['user_id']);
    header("Location: bookings.php");
    exit;
}

// Fetch user bookings
$bookings = $bookingModel->findByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
</head>
<body>

<h2>My Bookings</h2>

<?php if (empty($bookings)): ?>
    <p>You have no bookings.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Rental</th>
            <th>City</th>
            <th>Dates</th>
            <th>Total price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['title']); ?></td>
                <td><?php echo htmlspecialchars($booking['city']); ?></td>
                <td>
                    <?php echo $booking['start_date']; ?>
                    →
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
<?php endif; ?>

<br>
<a href="index.php">Back to rentals</a>

</body>
</html>
