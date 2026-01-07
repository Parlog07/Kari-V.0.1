<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";

$db = new Database();
$pdo = $db->getConnection();
?>

<!DOCTYPE html>
<html>
<body>

<h1>Admin Dashboard</h1>

<ul>
    <li><a href="users.php">Manage users</a></li>
    <li><a href="rentals.php">Manage rentals</a></li>
</ul>

<a href="../index.php">Back to site</a>

</body>
</html>
