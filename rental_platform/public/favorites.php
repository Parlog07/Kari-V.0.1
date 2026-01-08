<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

if (!isset($_SESSION['user_id'])) {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$favoriteModel = new Favorite($pdo);
$favorites = $favoriteModel->findUserFavorites($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">My Favorites</h1>
                        <p class="text-slate-600">Your saved properties for future stays</p>
                    </div>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-coral-100 to-orange-100 flex items-center justify-center mr-3">
                            <i class="fas fa-heart text-coral-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Saved Properties</p>
                            <p class="text-2xl font-bold text-slate-900"><?= count($favorites) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Favorites Grid -->
            <?php if (empty($favorites)): ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-stone-100">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-r from-coral-50 to-orange-50 mb-4">
                        <i class="far fa-heart text-3xl text-coral-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-700 mb-2">No favorites yet</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">Start saving properties you love by clicking the heart icon on any rental.</p>
                    <a href="index.php" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-150">
                        <i class="fas fa-search mr-2"></i> Explore Rentals
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($favorites as $rental): ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 border border-stone-100 group">
                            <div class="relative">
                                <div class="h-48 bg-gradient-to-r from-emerald-100 to-teal-100 flex items-center justify-center">
                                    <i class="fas fa-home text-6xl text-emerald-300"></i>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <a href="rental_detail.php?id=<?php echo $rental['id']; ?>&favorite=remove" 
                                       class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-sm hover:shadow transition duration-150"
                                       title="Remove from favorites">
                                        <i class="fas fa-heart text-coral-500"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-slate-900 truncate group-hover:text-emerald-700 transition duration-150">
                                        <?php echo htmlspecialchars($rental['title']); ?>
                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                        $<?php echo htmlspecialchars($rental['price_per_night']); ?>/night
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-slate-600 mb-4">
                                    <i class="fas fa-map-marker-alt text-coral-500 mr-2"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($rental['city']); ?></span>
                                </div>
                                
                                <p class="text-slate-600 mb-4 line-clamp-2 text-sm">
                                    <?php 
                                    $desc = htmlspecialchars($rental['description'] ?? '');
                                    echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                    ?>
                                </p>
                                
                                <div class="flex justify-between items-center pt-4 border-t border-stone-100">
                                    <a href="rental_detail.php?id=<?php echo $rental['id']; ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-sm hover:shadow transition duration-150">
                                        View Details
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                    <div class="text-sm text-slate-500">
                                        <i class="far fa-calendar mr-1"></i> Available
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Favorites Stats -->
                <div class="mt-12 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Your favorites list</h3>
                            <p class="text-slate-700">You have <?= count($favorites) ?> saved propert<?= count($favorites) === 1 ? 'y' : 'ies' ?></p>
                        </div>
                        <div class="flex space-x-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-700">
                                    $<?php 
                                    $avgPrice = array_sum(array_column($favorites, 'price_per_night')) / max(count($favorites), 1);
                                    echo number_format($avgPrice, 0);
                                    ?>
                                </div>
                                <div class="text-sm text-slate-600">Avg. price</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-700">
                                    <?php
                                    $uniqueCities = array_unique(array_column($favorites, 'city'));
                                    echo count($uniqueCities);
                                    ?>
                                </div>
                                <div class="text-sm text-slate-600">Cities</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>