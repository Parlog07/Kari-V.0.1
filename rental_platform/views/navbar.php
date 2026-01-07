<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="sticky top-0 z-50 w-full bg-white border-b border-stone-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/php/Kari-V.0.1/rental_platform/public/index.php" class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent hover:from-emerald-700 hover:to-teal-600 transition duration-150">Kari</a>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-1">
                <a href="/php/Kari-V.0.1/rental_platform/public/index.php" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-emerald-600 hover:bg-stone-50 rounded-lg transition duration-150">Rentals</a>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <a href="/php/Kari-V.0.1/rental_platform/public/bookings.php" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-emerald-600 hover:bg-stone-50 rounded-lg transition duration-150">My Bookings</a>
                    <a href="/php/Kari-V.0.1/rental_platform/public/favorites.php" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-emerald-600 hover:bg-stone-50 rounded-lg transition duration-150">Favorites</a>

                    <?php if ($_SESSION['role'] === 'host'): ?>
                        <a href="/php/Kari-V.0.1/rental_platform/public/host/dashboard.php" class="px-4 py-2 text-sm font-medium text-emerald-700 border border-emerald-200 hover:text-emerald-800 hover:bg-emerald-50 hover:border-emerald-300 rounded-lg transition duration-150">Host Dashboard</a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="/php/Kari-V.0.1/rental_platform/public/admin/dashboard.php" class="px-4 py-2 text-sm font-medium text-rose-700 border border-rose-200 hover:text-rose-800 hover:bg-rose-50 hover:border-rose-300 rounded-lg transition duration-150">Admin Dashboard</a>
                    <?php endif; ?>

                    <a href="/php/Kari-V.0.1/rental_platform/public/logout.php" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-coral-500 to-orange-500 hover:from-coral-600 hover:to-orange-600 rounded-lg shadow-sm hover:shadow transition duration-150">Logout</a>

                <?php else: ?>

                    <a href="/php/Kari-V.0.1/rental_platform/public/login.php" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-emerald-600 hover:bg-stone-50 rounded-lg transition duration-150">Login</a>
                    <a href="/php/Kari-V.0.1/rental_platform/public/register.php" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 rounded-lg shadow-sm hover:shadow transition duration-150">Sign Up</a>

                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>