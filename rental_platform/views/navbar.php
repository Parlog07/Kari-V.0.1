<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-left">
        <a href="/php/Kari-V.0.1/rental_platform/public/index.php" class="logo">Kari</a>
    </div>

    <div class="nav-right">
        <a href="/php/Kari-V.0.1/rental_platform/public/index.php">Rentals</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="/php/Kari-V.0.1/rental_platform/public/bookings.php">My bookings</a>
            <a href="/php/Kari-V.0.1/rental_platform/public/favorites.php">Favorites</a>

            <?php if ($_SESSION['role'] === 'host'): ?>
                <a href="/php/Kari-V.0.1/rental_platform/public/host/dashboard.php">Host</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/php/Kari-V.0.1/rental_platform/public/admin/dashboard.php">Admin</a>
            <?php endif; ?>

            <a href="/php/Kari-V.0.1/rental_platform/public/logout.php" class="btn">Logout</a>

        <?php else: ?>

            <a href="/php/Kari-V.0.1/rental_platform/public/login.php">Login</a>
            <a href="/php/Kari-V.0.1/rental_platform/public/register.php" class="btn">Sign up</a>

        <?php endif; ?>
    </div>
</nav>
