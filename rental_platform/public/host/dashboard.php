<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Must be host
if ($_SESSION['role'] !== 'host') {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$rentalModel = new Rental($pdo);
$rentals = $rentalModel->findByHost($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Host Dashboard</title>
</head>
<body>

<h2>My Rentals</h2>

<a href="add_rental.php">Add new rental</a>
<br><br>

<?php if (empty($rentals)): ?>
    <p>You have no rentals yet.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Title</th>
            <th>City</th>
            <th>Price / night</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($rentals as $rental): ?>
            <tr>
                <td><?php echo htmlspecialchars($rental['title']); ?></td>
                <td><?php echo htmlspecialchars($rental['city']); ?></td>
                <td><?php echo htmlspecialchars($rental['price_per_night']); ?></td>
                <td>
                    <a href="edit_rental.php?id=<?php echo $rental['id']; ?>">Edit</a> |
                    <a href="delete_rental.php?id=<?php echo $rental['id']; ?>"
                       onclick="return confirm('Delete this rental?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br>
<a href="../logout.php">Logout</a>

</body>
</html>
