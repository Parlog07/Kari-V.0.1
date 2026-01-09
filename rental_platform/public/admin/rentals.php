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
    SELECT r.id, r.title, r.city, r.is_active, u.full_name, r.price_per_night
    FROM rentals r
    JOIN users u ON u.id = r.host_id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Management | Admin Dashboard</title>
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
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Rental Management</h1>
                        <p class="text-slate-600">Manage and moderate all property listings</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white rounded-lg shadow-sm p-4 border border-stone-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-700"><?= count($rentals) ?></div>
                                <div class="text-sm text-slate-500">Total Rentals</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900">All Rentals</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search rentals..." 
                                   class="pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                        </div>
                        <select class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Disabled</option>
                            <option>Pending Review</option>
                        </select>
                    </div>
                </div>

                <!-- Rentals Table -->
                <?php if (empty($rentals)): ?>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                            <i class="fas fa-home text-3xl text-stone-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No rentals found</h3>
                        <p class="text-slate-600 max-w-md mx-auto">There are no rental properties in the database yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Property</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">City</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Host</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php foreach ($rentals as $r): ?>
                                <tr class="hover:bg-stone-50 transition duration-150">
                                    <!-- Property Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-home text-emerald-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900"><?= htmlspecialchars($r['title']) ?></div>
                                                <div class="text-sm text-slate-500">ID: <?= $r['id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- City -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-coral-500 mr-2"></i>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['city']) ?></span>
                                        </div>
                                    </td>
                                    
                                    <!-- Host -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <span class="text-xs font-bold text-blue-700">
                                                    <?= strtoupper(substr($r['full_name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['full_name']) ?></span>
                                        </div>
                                    </td>
                                    
                                    <!-- Price -->
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-emerald-700">$<?= $r['price_per_night'] ?></span>
                                        <span class="text-sm text-slate-500">/night</span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <?php if ($r['is_active']): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                                Disabled
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="?toggle=<?= $r['id'] ?>" 
                                               onclick="return confirm('Are you sure you want to <?= $r['is_active'] ? 'disable' : 'activate' ?> \"<?= htmlspecialchars($r['title']) ?>\"?');"
                                               class="inline-flex items-center px-3 py-1 <?= $r['is_active'] ? 'border border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400' : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-400' ?> rounded-lg text-sm font-medium transition duration-150">
                                                <?php if ($r['is_active']): ?>
                                                    <i class="fas fa-ban mr-1"></i> Disable
                                                <?php else: ?>
                                                    <i class="fas fa-check-circle mr-1"></i> Activate
                                                <?php endif; ?>
                                            </a>
                                            <a href="../rental_detail.php?id=<?= $r['id'] ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <a href="#" 
                                               class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150">
                                                <i class="fas fa-edit mr-1"></i> Edit
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

            <!-- Rental Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                            <i class="fas fa-home text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Active Rentals</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($rentals, fn($r) => $r['is_active'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                            <i class="fas fa-home text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Disabled Rentals</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($rentals, fn($r) => !$r['is_active'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Active Hosts</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_unique(array_column(array_filter($rentals, fn($r) => $r['is_active']), 'full_name'))) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                            <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Avg. Price</p>
                            <p class="text-2xl font-bold text-slate-900">
                                $<?= count($rentals) > 0 ? number_format(array_sum(array_column($rentals, 'price_per_night')) / count($rentals), 2) : '0.00' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-stone-50 to-stone-100 rounded-xl p-6 border border-stone-200">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-amber-600 mr-2"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="#" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-file-export text-emerald-600 mr-2"></i>
                        <span>Export Rentals</span>
                    </a>
                    <a href="#" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                        <span>View Analytics</span>
                    </a>
                    <a href="#" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-envelope text-coral-600 mr-2"></i>
                        <span>Contact All Hosts</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>