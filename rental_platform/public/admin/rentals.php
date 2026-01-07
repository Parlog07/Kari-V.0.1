<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";

$db = new Database();
$pdo = $db->getConnection();

if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("
        UPDATE rentals
        SET is_active = NOT is_active
        WHERE id = :id
    ");
    $stmt->execute(['id' => $_GET['toggle']]);
    header("Location: rentals.php");
    exit;
}

$rentals = $pdo->query("
    SELECT r.id, r.title, r.city, r.is_active, u.full_name
    FROM rentals r
    JOIN users u ON u.id = r.host_id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<body>

<h2>Rentals</h2>

<table border="1">
<tr>
    <th>Title</th>
    <th>City</th>
    <th>Host</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($rentals as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['title']) ?></td>
    <td><?= htmlspecialchars($r['city']) ?></td>
    <td><?= htmlspecialchars($r['full_name']) ?></td>
    <td><?= $r['is_active'] ? 'Active' : 'Disabled' ?></td>
    <td>
        <a href="?toggle=<?= $r['id'] ?>">
            <?= $r['is_active'] ? 'Disable' : 'Activate' ?>
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
