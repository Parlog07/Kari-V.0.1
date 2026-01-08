<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($rental['title']); ?> | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto px-8 py-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <a href="index.php" class="text-emerald-600 hover:text-emerald-800 transition duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Rentals
                </a>
            </div>

            <!-- Property Header -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2"><?php echo htmlspecialchars($rental['title']); ?></h1>
                    <div class="flex items-center space-x-6 text-slate-600">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-coral-500 mr-2"></i>
                            <span class="font-medium"><?php echo htmlspecialchars($rental['city']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-home text-emerald-500 mr-2"></i>
                            <span><?php echo htmlspecialchars($rental['address']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-2xl font-bold text-emerald-700 mb-2">
                        $<?php echo htmlspecialchars($rental['price_per_night']); ?> <span class="text-lg font-normal text-slate-600">/ night</span>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mt-2">
                            <?php if ($isFavorite): ?>
                                <a href="?id=<?php echo $rental['id']; ?>&favorite=remove" 
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-coral-500 to-orange-500 hover:from-coral-600 hover:to-orange-600 text-white rounded-lg shadow-sm hover:shadow transition duration-150">
                                    <i class="fas fa-heart mr-2"></i> Remove from Favorites
                                </a>
                            <?php else: ?>
                                <a href="?id=<?php echo $rental['id']; ?>&favorite=add" 
                                   class="inline-flex items-center px-4 py-2 border border-coral-300 bg-white text-coral-600 hover:bg-coral-50 hover:border-coral-400 rounded-lg shadow-sm hover:shadow transition duration-150">
                                    <i class="far fa-heart mr-2"></i> Add to Favorites
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Property Image -->
            <div class="mb-8">
                <?php if (!empty($rental['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($rental['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($rental['title']); ?>"
                         class="w-full h-96 object-cover rounded-2xl shadow-lg">
                <?php else: ?>
                    <div class="w-full h-96 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-2xl shadow-lg flex items-center justify-center">
                        <i class="fas fa-home text-8xl text-emerald-300"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Description -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-stone-100">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">About this property</h2>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                            <?php echo nl2br(htmlspecialchars($rental['description'])); ?>
                        </p>
                    </div>

                    <!-- Booking Form / Messages -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                        <?php if ($bookingError): ?>
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                                    <div>
                                        <h3 class="font-bold text-red-800">Booking Error</h3>
                                        <p class="text-red-700"><?php echo htmlspecialchars($bookingError); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($bookingSuccess): ?>
                            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                                    <div>
                                        <h3 class="font-bold text-emerald-800">Booking Confirmed!</h3>
                                        <p class="text-emerald-700"><?php echo htmlspecialchars($bookingSuccess); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'traveler'): ?>
                            <h3 class="text-2xl font-bold text-slate-900 mb-6">Book this rental</h3>
                            <form method="POST" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">
                                            <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i> Check-in Date
                                        </label>
                                        <input type="date" name="start_date" required
                                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">
                                            <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i> Check-out Date
                                        </label>
                                        <input type="date" name="end_date" required
                                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-stone-50 rounded-lg border border-stone-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-slate-700">Price per night:</span>
                                        <span class="font-bold text-emerald-700">$<?php echo htmlspecialchars($rental['price_per_night']); ?></span>
                                    </div>
                                    <p class="text-sm text-slate-500">Total price will be calculated based on selected dates</p>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition duration-150 flex items-center justify-center">
                                    <i class="fas fa-check-circle mr-2"></i> Book Now
                                </button>
                            </form>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <div class="text-center p-8 bg-gradient-to-r from-stone-50 to-stone-100 rounded-xl border border-stone-200">
                                <i class="fas fa-lock text-4xl text-slate-400 mb-4"></i>
                                <h3 class="text-xl font-bold text-slate-800 mb-2">Sign in to book this rental</h3>
                                <p class="text-slate-600 mb-6">Create an account or log in to make a reservation</p>
                                <div class="flex justify-center space-x-4">
                                    <a href="login.php" 
                                       class="px-6 py-2 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                                        Login
                                    </a>
                                    <a href="register.php" 
                                       class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white rounded-lg shadow-sm hover:shadow transition">
                                        Sign Up
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Property Details -->
                <div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 sticky top-24">
                        <h3 class="text-xl font-bold text-slate-900 mb-6 pb-4 border-b border-stone-200">Property Details</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-city text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">City</p>
                                    <p class="font-medium text-slate-800"><?php echo htmlspecialchars($rental['city']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-map-pin text-coral-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Address</p>
                                    <p class="font-medium text-slate-800"><?php echo htmlspecialchars($rental['address']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-dollar-sign text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Price per night</p>
                                    <p class="font-bold text-emerald-700 text-lg">$<?php echo htmlspecialchars($rental['price_per_night']); ?></p>
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t border-stone-200">
                                <h4 class="font-bold text-slate-900 mb-3">Need help?</h4>
                                <p class="text-sm text-slate-600 mb-4">Contact our support team for any questions about this property.</p>
                                <button class="w-full py-2 border border-stone-300 text-slate-700 hover:bg-stone-50 rounded-lg transition">
                                    <i class="fas fa-headset mr-2"></i> Contact Support
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>