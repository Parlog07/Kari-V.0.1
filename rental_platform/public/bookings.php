<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$bookingModel = new Booking($pdo);
$mailer = new Mailer();

if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("
        SELECT b.*, r.title
        FROM bookings b
        JOIN rentals r ON r.id = b.rental_id
        WHERE b.id = :id AND b.user_id = :user_id
    ");
    $stmt->execute([
        'id' => $_GET['cancel'],
        'user_id' => $_SESSION['user_id']
    ]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $bookingModel->cancelByUser($booking['id'], $_SESSION['user_id']);
        $mailer->sendBookingCancellation(
            $_SESSION['email'],
            $_SESSION['full_name'],
            $booking['title'],
            $booking['start_date'],
            $booking['end_date']
        );
    }

    header("Location: bookings.php");
    exit;
}

$bookings = $bookingModel->findByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">My Bookings</h1>
                <p class="text-slate-600">Manage your upcoming and past reservations</p>
            </div>

            <!-- Bookings Table -->
            <?php if (empty($bookings)): ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-stone-100">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                        <i class="fas fa-calendar-alt text-3xl text-stone-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-700 mb-2">No bookings yet</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">Start exploring amazing properties for your next adventure.</p>
                    <a href="index.php" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-150">
                        <i class="fas fa-search mr-2"></i> Browse Rentals
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-stone-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Rental</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Dates</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Action</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-stone-50 transition duration-150">
                                    <!-- Rental -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900"><?= htmlspecialchars($b['title']) ?></div>
                                    </td>
                                    
                                    <!-- Dates -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-center">
                                                <div class="font-medium text-slate-900"><?= date('M j', strtotime($b['start_date'])) ?></div>
                                                <div class="text-xs text-slate-500"><?= date('Y', strtotime($b['start_date'])) ?></div>
                                            </div>
                                            <div class="mx-3">
                                                <i class="fas fa-arrow-right text-emerald-500"></i>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-medium text-slate-900"><?= date('M j', strtotime($b['end_date'])) ?></div>
                                                <div class="text-xs text-slate-500"><?= date('Y', strtotime($b['end_date'])) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Total -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-bold text-emerald-700">$<?= $b['total_price'] ?></span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    
                                    <!-- Action -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($b['status'] === 'confirmed'): ?>
                                            <a href="?cancel=<?= $b['id'] ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400 rounded-lg text-sm font-medium transition duration-150"
                                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                <i class="fas fa-times mr-1"></i> Cancel
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-sm">No actions</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Receipt -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="receipt.php?id=<?= $b['id'] ?>" 
                                           class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150">
                                            <i class="fas fa-file-pdf mr-1 text-red-500"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-check text-emerald-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Upcoming Bookings</p>
                                <p class="text-2xl font-bold text-slate-900">
                                    <?php 
                                    $upcoming = array_filter($bookings, function($b) {
                                        return $b['status'] === 'confirmed' && strtotime($b['start_date']) > time();
                                    });
                                    echo count($upcoming);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                <i class="fas fa-history text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Total Bookings</p>
                                <p class="text-2xl font-bold text-slate-900"><?= count($bookings) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                                <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Total Spent</p>
                                <p class="text-2xl font-bold text-emerald-700">
                                    $<?= array_sum(array_column($bookings, 'total_price')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>