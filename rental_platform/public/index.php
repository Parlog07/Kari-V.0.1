<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

$db = new Database();
$pdo = $db->getConnection();
$rentalModel = new Rental($pdo);

$filters = [
    'city'       => $_GET['city'] ?? null,
    'min_price'  => $_GET['min_price'] ?? null,
    'max_price'  => $_GET['max_price'] ?? null,
    'start_date' => $_GET['start_date'] ?? null,
    'end_date'   => $_GET['end_date'] ?? null,
];

$limit = 6;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$rentals = $rentalModel->search($filters, $limit, $offset);
$totalResults = $rentalModel->countSearchResults($filters);
$totalPages = (int) ceil($totalResults / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rentals | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <!-- Hero / Search Section -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-stone-200">
            <div class="max-w-7xl mx-auto px-8 py-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Find your perfect stay</h1>
                <p class="text-lg text-slate-600 mb-8">Discover unique accommodations for your next adventure</p>
                
                <!-- Search Form -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Destination</label>
                            <input type="text" name="city" placeholder="Any city"
                                   value="<?php echo htmlspecialchars($_GET['city'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Check-in</label>
                            <input type="date" name="start_date"
                                   value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Check-out</label>
                            <input type="date" name="end_date"
                                   value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Min Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500">$</span>
                                <input type="number" name="min_price" placeholder="0"
                                       value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>"
                                       class="w-full pl-8 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Max Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500">$</span>
                                <input type="number" name="max_price" placeholder="1000"
                                       value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>"
                                       class="w-full pl-8 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                            </div>
                        </div>
                        
                        <div class="lg:col-span-5 mt-2">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-150 flex items-center gap-2">
                                <i class="fas fa-search"></i> Search Rentals
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-slate-900">Available Rentals</h2>
                    <p class="text-slate-600">
                        <span class="font-semibold text-emerald-700"><?php echo $totalResults; ?></span> properties found
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <!-- Rentals Grid -->
            <?php if (empty($rentals)): ?>
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                        <i class="fas fa-home text-3xl text-stone-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-700 mb-2">No rentals found</h3>
                    <p class="text-slate-600 max-w-md mx-auto">Try adjusting your search filters to find more properties.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <?php foreach ($rentals as $rental): ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 border border-stone-100">
                            <div class="h-48 bg-gradient-to-r from-emerald-100 to-teal-100 flex items-center justify-center">
                                <i class="fas fa-home text-6xl text-emerald-300"></i>
                            </div>
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-slate-900 truncate"><?php echo htmlspecialchars($rental['title']); ?></h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                        $<?php echo htmlspecialchars($rental['price_per_night']); ?>/night
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-slate-600 mb-3">
                                    <i class="fas fa-map-marker-alt text-coral-500 mr-2"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($rental['city']); ?></span>
                                </div>
                                
                                <p class="text-slate-600 mb-4 line-clamp-2"><?php echo htmlspecialchars(substr($rental['description'], 0, 120)); ?>...</p>
                                
                                <div class="flex justify-between items-center">
                                    <a href="rental_detail.php?id=<?php echo $rental['id']; ?>" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-sm hover:shadow transition duration-150">
                                        View Details
                                    </a>
                                    <button class="p-2 text-slate-400 hover:text-coral-500 transition">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center items-center space-x-2 py-8 border-t border-stone-200">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>"
                           class="px-4 py-2 border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 hover:border-stone-400 transition">
                            <i class="fas fa-chevron-left mr-2"></i> Previous
                        </a>
                    <?php endif; ?>

                    <div class="flex items-center space-x-1">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-medium rounded-lg shadow-sm">
                                    <?php echo $i; ?>
                                </span>
                            <?php else: ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                                   class="px-4 py-2 border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 hover:border-stone-400 transition">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>"
                           class="px-4 py-2 border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 hover:border-stone-400 transition">
                            Next <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>