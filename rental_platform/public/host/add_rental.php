<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Rental | Host Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-4xl mx-auto px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Add New Rental</h1>
                        <p class="text-slate-600">List your property and start earning</p>
                    </div>
                    <a href="dashboard.php" 
                       class="inline-flex items-center px-4 py-2 border border-stone-300 text-slate-700 hover:bg-stone-50 rounded-lg transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-bold text-red-800">Error</h3>
                            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-bold text-emerald-800">Success!</h3>
                            <p class="text-emerald-700"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-4">
                        <a href="dashboard.php" 
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                            <i class="fas fa-home mr-2"></i> Go to Dashboard
                        </a>
                        <a href="add_rental.php" 
                           class="inline-flex items-center px-4 py-2 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i> Add Another
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-stone-100">
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-6 pb-3 border-b border-stone-200">
                            <i class="fas fa-info-circle text-emerald-500 mr-2"></i> Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required
                                       class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                                       placeholder="Beautiful apartment in city center">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" required
                                       class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                                       placeholder="New York">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address" required
                                   class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                                   placeholder="123 Main Street, Apt 4B">
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" required rows="4"
                                      class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                                      placeholder="Describe your property's features, amenities, and unique qualities..."></textarea>
                        </div>
                    </div>

                    <!-- Pricing & Capacity -->
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-6 pb-3 border-b border-stone-200">
                            <i class="fas fa-dollar-sign text-emerald-500 mr-2"></i> Pricing & Capacity
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Price per night ($) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-slate-500">$</span>
                                    <input type="number" step="0.01" name="price_per_night" required
                                           class="w-full pl-8 pr-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                                           placeholder="99.99">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Maximum guests <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_guests" required
                                       class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                                       placeholder="4">
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-6 pb-3 border-b border-stone-200">
                            <i class="fas fa-image text-emerald-500 mr-2"></i> Property Images
                        </h3>
                        <div class="border-2 border-dashed border-stone-300 rounded-xl p-8 text-center hover:border-emerald-400 transition duration-150">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-stone-100 mb-4">
                                <i class="fas fa-cloud-upload-alt text-2xl text-stone-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-900 mb-2">Upload property image</h4>
                            <p class="text-slate-600 mb-4">JPG, PNG or GIF (max 5MB)</p>
                            <input type="file" name="image" accept="image/*" 
                                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                            <p class="text-xs text-slate-500 mt-4">Main image will be displayed on listing page</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-6 border-t border-stone-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-slate-600">
                                <span class="text-red-500">*</span> Required fields
                            </div>
                            <div class="flex space-x-4">
                                <button type="reset" 
                                        class="px-6 py-3 border border-stone-300 text-slate-700 hover:bg-stone-50 rounded-lg font-medium transition duration-150">
                                    <i class="fas fa-redo mr-2"></i> Reset
                                </button>
                                <button type="submit" 
                                        class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition duration-150">
                                    <i class="fas fa-plus-circle mr-2"></i> Add Rental
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tips -->
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                <h3 class="font-bold text-slate-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i> Tips for a great listing
                </h3>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-500 mr-2 mt-1"></i>
                        Use high-quality photos that showcase your space
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-500 mr-2 mt-1"></i>
                        Be accurate with pricing and guest capacity
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-500 mr-2 mt-1"></i>
                        Write a detailed description highlighting unique features
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-500 mr-2 mt-1"></i>
                        Set clear check-in/out times and house rules
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>