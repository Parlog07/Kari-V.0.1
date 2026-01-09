<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";


$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);
$mailer = new Mailer();

if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("
        SELECT b.*, u.email, u.full_name, r.title
        FROM bookings b
        JOIN users u ON u.id = b.user_id
        JOIN rentals r ON r.id = b.rental_id
        WHERE b.id = :id
    ");
    $stmt->execute(['id' => $_GET['cancel']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $bookingModel->cancelByAdmin($booking['id']);
        $mailer->sendBookingCancellation(
            $booking['email'],
            $booking['full_name'],
            $booking['title'],
            $booking['start_date'],
            $booking['end_date']
        );
    }

    header("Location: bookings.php");
    exit;
}

$stmt = $pdo->query("
    SELECT b.*, u.full_name, r.title
    FROM bookings b
    JOIN users u ON u.id = b.user_id
    JOIN rentals r ON r.id = b.rental_id
");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management | Admin Dashboard</title>
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
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Booking Management</h1>
                        <p class="text-slate-600">Monitor and manage all platform bookings</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white rounded-lg shadow-sm p-4 border border-stone-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-700"><?= count($bookings) ?></div>
                                <div class="text-sm text-slate-500">Total Bookings</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900">All Bookings</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search bookings..." 
                                   class="pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                        </div>
                        <select class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                            <option>All Status</option>
                            <option>Confirmed</option>
                            <option>Cancelled</option>
                            <option>Completed</option>
                            <option>Pending</option>
                        </select>
                        <input type="date" 
                               class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <!-- Bookings Table -->
                <?php if (empty($bookings)): ?>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                            <i class="fas fa-calendar-alt text-3xl text-stone-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No bookings found</h3>
                        <p class="text-slate-600 max-w-md mx-auto">There are no bookings in the database yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Rental</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Dates</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Total Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-stone-50 transition duration-150">
                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                                <span class="font-bold text-emerald-700">
                                                    <?= strtoupper(substr($b['full_name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900"><?= htmlspecialchars($b['full_name']) ?></div>
                                                <div class="text-sm text-slate-500">ID: <?= $b['user_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Rental -->
                                    <td class="px-6 py-4">
                                        <div class="text-slate-800 font-medium"><?= htmlspecialchars($b['title']) ?></div>
                                    </td>
                                    
                                    <!-- Dates -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="text-center mr-4">
                                                <div class="font-medium text-slate-900"><?= date('M j', strtotime($b['start_date'])) ?></div>
                                                <div class="text-xs text-slate-500">Check-in</div>
                                            </div>
                                            <i class="fas fa-arrow-right text-emerald-500 mx-2"></i>
                                            <div class="text-center ml-4">
                                                <div class="font-medium text-slate-900"><?= date('M j', strtotime($b['end_date'])) ?></div>
                                                <div class="text-xs text-slate-500">Check-out</div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            <?= date('Y', strtotime($b['start_date'])) ?>
                                        </div>
                                    </td>
                                    
                                    <!-- Total Price -->
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-emerald-700">$<?= $b['total_price'] ?></span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <?php
                                        $statusColors = [
                                            'confirmed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $color = $statusColors[$b['status']] ?? 'bg-stone-100 text-stone-800';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $color ?>">
                                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                            <?= ucfirst($b['status']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <?php if ($b['status'] === 'confirmed'): ?>
                                                <a href="?cancel=<?= $b['id'] ?>" 
                                                   onclick="return confirm('Are you sure you want to cancel this booking? This will notify the user.');"
                                                   class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400 rounded-lg text-sm font-medium transition duration-150">
                                                    <i class="fas fa-times mr-1"></i> Cancel
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-sm">No actions</span>
                                            <?php endif; ?>
                                            <a href="#" 
                                               class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150">
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

            <!-- Booking Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Confirmed</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed')) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Cancelled</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled')) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Completed</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($bookings, fn($b) => $b['status'] === 'completed')) ?>
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
                            <p class="text-sm text-slate-500">Total Revenue</p>
                            <p class="text-2xl font-bold text-slate-900">
                                $<?= array_sum(array_column($bookings, 'total_price')) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart Placeholder -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 mb-8">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Booking Revenue Overview</h3>
                <div class="h-64 bg-gradient-to-r from-stone-50 to-stone-100 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-stone-400 mb-4"></i>
                        <p class="text-slate-600">Revenue chart would appear here</p>
                        <p class="text-sm text-slate-500 mt-2">Monthly booking revenue and trends</p>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-2">Export Booking Data</h3>
                        <p class="text-slate-700">Download booking reports for analysis</p>
                    </div>
                    <div class="flex space-x-4">
                        <button class="px-4 py-2 bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                            <i class="fas fa-file-csv mr-2"></i> CSV Export
                        </button>
                        <button class="px-4 py-2 bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                            <i class="fas fa-file-pdf mr-2"></i> PDF Report
                        </button>
                        <button class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white rounded-lg shadow-sm hover:shadow transition">
                            <i class="fas fa-chart-bar mr-2"></i> Analytics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>