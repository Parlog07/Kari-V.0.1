<?php
require_once "../config/autoload.php";
session_start();

if (!isset($_GET['id'])) {
    die("Rental not found");
}

$db = new Database();
$pdo = $db->getConnection();

$rentalModel = new Rental($pdo);
$rental = $rentalModel->findById((int)$_GET['id']);

if (!$rental) {
    die("Rental not found");
}

$favoriteModel = new Favorite($pdo);

if (isset($_GET['favorite']) && isset($_SESSION['user_id'])) {
    if ($_GET['favorite'] === 'add') {
        $favoriteModel->add($_SESSION['user_id'], $rental['id']);
    }
    if ($_GET['favorite'] === 'remove') {
        $favoriteModel->remove($_SESSION['user_id'], $rental['id']);
    }
    header("Location: rental_detail.php?id=" . $rental['id']);
    exit;
}

$isFavorite = isset($_SESSION['user_id'])
    ? $favoriteModel->isFavorite($_SESSION['user_id'], $rental['id'])
    : false;

$bookingError = null;
$bookingSuccess = null;

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SESSION['user_id']) &&
    $_SESSION['role'] === 'traveler'
) {
    try {
        $bookingModel = new Booking($pdo);

        $bookingModel->create([
            'rental_id' => $rental['id'],
            'user_id' => $_SESSION['user_id'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'price_per_night' => $rental['price_per_night']
        ]);

        $mailer = new Mailer();
        $mailer->sendBookingConfirmation(
            $_SESSION['email'],
            $_SESSION['full_name'],
            $rental['title'],
            $_POST['start_date'],
            $_POST['end_date'],
            $rental['price_per_night']
        );

        $bookingSuccess = "Booking confirmed";
    } catch (Exception $e) {
        $bookingError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($rental['title']); ?></title>
</head>
<body>

<h1><?php echo htmlspecialchars($rental['title']); ?></h1>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($isFavorite): ?>
        <a href="?id=<?php echo $rental['id']; ?>&favorite=remove">Remove from favorites</a>
    <?php else: ?>
        <a href="?id=<?php echo $rental['id']; ?>&favorite=add">Add to favorites</a>
    <?php endif; ?>
<?php endif; ?>

<p><strong>City:</strong> <?php echo htmlspecialchars($rental['city']); ?></p>
<p><strong>Address:</strong> <?php echo htmlspecialchars($rental['address']); ?></p>
<p><strong>Price per night:</strong> <?php echo htmlspecialchars($rental['price_per_night']); ?></p>

<p><?php echo nl2br(htmlspecialchars($rental['description'])); ?></p>

<?php if (!empty($rental['image_path'])): ?>
    <img src="<?php echo htmlspecialchars($rental['image_path']); ?>" width="350">
<?php endif; ?>

<hr>

<?php if ($bookingError): ?>
    <p style="color:red;"><?php echo htmlspecialchars($bookingError); ?></p>
<?php endif; ?>

<?php if ($bookingSuccess): ?>
    <p style="color:green;"><?php echo htmlspecialchars($bookingSuccess); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'traveler'): ?>
    <h3>Book this rental</h3>

    <form method="POST">
        <label>Start date</label><br>
        <input type="date" name="start_date" required><br><br>

        <label>End date</label><br>
        <input type="date" name="end_date" required><br><br>

        <button type="submit">Book now</button>
    </form>
<?php endif; ?>

<br>
<a href="index.php">Back to rentals</a>

</body>
</html>
