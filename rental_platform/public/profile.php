<?php
require_once "../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();
$userModel = new User($pdo);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel->updateProfile($_SESSION['user_id'], [
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone'])
    ]);

    $success = "Profile updated successfully";
}

// Get current user data
$user = $userModel->findByEmail($_SESSION['email'] ?? '');
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>

<h2>My Profile</h2>

<?php if (!empty($success)): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Full name</label><br>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br><br>

    <label>Phone</label><br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"><br><br>

    <button type="submit">Update</button>
</form>

<br>
<a href="logout.php">Logout</a>

</body>
</html>
