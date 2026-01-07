<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['role'] !== 'host') {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$rentalModel = new Rental($pdo);

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $price = $_POST['price_per_night'] ?? '';
    $maxGuests = $_POST['max_guests'] ?? '';

    if (
        empty($title) ||
        empty($description) ||
        empty($city) ||
        empty($address) ||
        empty($price) ||
        empty($maxGuests)
    ) {
        $error = "All fields are required";
    } else {

        $imagePath = null;

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "../uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = "uploads/" . $fileName;
            }
        }

        $rentalModel->create([
            'host_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => $description,
            'city' => $city,
            'address' => $address,
            'price_per_night' => $price,
            'max_guests' => $maxGuests,
            'image_path' => $imagePath
        ]);

        $success = "Rental added successfully";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Rental</title>
</head>
<body>

<h2>Add New Rental</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Title</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>City</label><br>
    <input type="text" name="city" required><br><br>

    <label>Address</label><br>
    <input type="text" name="address" required><br><br>

    <label>Price per night</label><br>
    <input type="number" step="0.01" name="price_per_night" required><br><br>

    <label>Max guests</label><br>
    <input type="number" name="max_guests" required><br><br>

    <label>Image</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Add Rental</button>
</form>

<br>
<a href="dashboard.php">Back to dashboard</a>

</body>
</html>
