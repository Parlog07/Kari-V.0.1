<?php
require_once "../config/autoload.php";
session_start();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'traveler';

    try {
        if (empty($fullName) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }

        $db = new Database();
        $pdo = $db->getConnection();

        $userModel = new User($pdo);
        $userModel->register([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        $success = "Account created successfully. You can now login.";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="POST">
    <label>Full name</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role</label><br>
    <select name="role">
    <option value="traveler">Traveler</option>
    <option value="host">Host</option>
    </select>
    <br><br>

    <button type="submit">Register</button>
</form>

<br>
<a href="login.php">Already have an account? Login</a>

</body>
</html>
