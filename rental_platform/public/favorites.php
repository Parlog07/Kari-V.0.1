<?php
require_once "../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$favoriteModel = new Favorite($pdo);
$favorites = $favoriteModel->findUserFavorites($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<body>

<h2>My Favorites</h2>

<?php if (empty($favorites)): ?>
    <p>No favorites yet.</p>
<?php else: ?>
    <?php foreach ($favorites as $rental): ?>
        <div>
            <h3><?php echo htmlspecialchars($rental['title']); ?></h3>
            <p><?php echo htmlspecialchars($rental['city']); ?></p>
            <p><?php echo htmlspecialchars($rental['price_per_night']); ?></p>
            <a href="rental_detail.php?id=<?php echo $rental['id']; ?>">View</a>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
