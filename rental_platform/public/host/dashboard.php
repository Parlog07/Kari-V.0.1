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
$rentals = $rentalModel->findByHost($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Dashboard | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Host Dashboard</h1>
                        <p class="text-slate-600">Manage your rental properties and bookings</p>
                    </div>
                    <a href="add_rental.php" 
                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-150">
                        <i class="fas fa-plus-circle mr-2"></i> Add New Rental
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                            <i class="fas fa-home text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Rentals</p>
                            <p class="text-2xl font-bold text-slate-900"><?= count($rentals) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Active Bookings</p>
                            <p class="text-2xl font-bold text-slate-900">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                            <i class="fas fa-star text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Avg. Rating</p>
                            <p class="text-2xl font-bold text-slate-900">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-coral-100 flex items-center justify-center mr-4">
                            <i class="fas fa-dollar-sign text-coral-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Revenue</p>
                            <p class="text-2xl font-bold text-slate-900">$0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rentals Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-stone-100">
                <div class="px-6 py-4 border-b border-stone-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-slate-900">My Rentals</h2>
                    <span class="text-sm text-slate-500">
                        Showing <?= count($rentals) ?> propert<?= count($rentals) === 1 ? 'y' : 'ies' ?>
                    </span>
                </div>
                
                <?php if (empty($rentals)): ?>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                            <i class="fas fa-home text-3xl text-stone-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No rentals yet</h3>
                        <p class="text-slate-600 max-w-md mx-auto mb-6">Start earning by listing your first property.</p>
                        <a href="add_rental.php" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-150">
                            <i class="fas fa-plus-circle mr-2"></i> Create Your First Rental
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">City</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Price / Night</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php foreach ($rentals as $rental): ?>
                                <tr class="hover:bg-stone-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900"><?php echo htmlspecialchars($rental['title']); ?></div>
                                        <div class="text-xs text-slate-500 mt-1">ID: <?php echo $rental['id']; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-coral-500 mr-2"></i>
                                            <span class="text-slate-800"><?php echo htmlspecialchars($rental['city']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-emerald-700">$<?php echo htmlspecialchars($rental['price_per_night']); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <a href="edit_rental.php?id=<?php echo $rental['id']; ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-400 rounded-lg text-sm font-medium transition duration-150"
                                               title="Edit">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="delete_rental.php?id=<?php echo $rental['id']; ?>"
                                               onclick="return confirm('Are you sure you want to delete this rental? This action cannot be undone.');"
                                               class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400 rounded-lg text-sm font-medium transition duration-150"
                                               title="Delete">
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </a>
                                            <a href="../rental_detail.php?id=<?php echo $rental['id']; ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150"
                                               title="View">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center">
                        <i class="fas fa-chart-line text-emerald-600 mr-2"></i> Performance
                    </h3>
                    <p class="text-slate-600 text-sm mb-4">Track your rental performance and earnings.</p>
                    <a href="#" class="text-emerald-700 hover:text-emerald-900 font-medium text-sm">
                        View analytics <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-gradient-to-r from-stone-50 to-stone-100 rounded-xl p-6 border border-stone-200">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center">
                        <i class="fas fa-calendar-alt text-coral-600 mr-2"></i> Bookings
                    </h3>
                    <p class="text-slate-600 text-sm mb-4">Manage upcoming and past bookings.</p>
                    <a href="#" class="text-coral-700 hover:text-coral-900 font-medium text-sm">
                        View bookings <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-100">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i> Settings
                    </h3>
                    <p class="text-slate-600 text-sm mb-4">Update your host profile and preferences.</p>
                    <a href="#" class="text-blue-700 hover:text-blue-900 font-medium text-sm">
                        Account settings <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-8 pt-6 border-t border-stone-200 text-center text-sm text-slate-500">
                <p>Need help? Contact our <a href="#" class="text-emerald-600 hover:text-emerald-800">host support team</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>