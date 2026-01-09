<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";

$db = new Database();
$pdo = $db->getConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Admin Dashboard</h1>
                <p class="text-slate-600">Platform management and analytics</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                            <i class="fas fa-users text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Users</p>
                            <p class="text-2xl font-bold text-slate-900">0</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="users.php" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-home text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Rentals</p>
                            <p class="text-2xl font-bold text-slate-900">0</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="rentals.php" class="text-blue-700 hover:text-blue-900 text-sm font-medium">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Bookings</p>
                            <p class="text-2xl font-bold text-slate-900">0</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="bookings.php" class="text-amber-700 hover:text-amber-900 text-sm font-medium">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-coral-100 flex items-center justify-center mr-4">
                            <i class="fas fa-dollar-sign text-coral-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Platform Revenue</p>
                            <p class="text-2xl font-bold text-slate-900">$0</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="bookings.php" class="text-coral-700 hover:text-coral-900 text-sm font-medium">
                            View report <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Management Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Users Management -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 hover:shadow-md transition duration-150">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user-cog text-emerald-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">User Management</h2>
                    </div>
                    <p class="text-slate-600 mb-4">Manage user accounts, roles, and permissions.</p>
                    <div class="space-y-2">
                        <a href="users.php" 
                           class="block w-full text-left px-4 py-2.5 border border-emerald-200 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 rounded-lg transition duration-150">
                            <i class="fas fa-list mr-2"></i> View All Users
                        </a>
                        <a href="users.php?action=add" 
                           class="block w-full text-left px-4 py-2.5 border border-stone-200 text-slate-700 hover:bg-stone-50 hover:border-stone-300 rounded-lg transition duration-150">
                            <i class="fas fa-user-plus mr-2"></i> Add New User
                        </a>
                    </div>
                </div>

                <!-- Rentals Management -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 hover:shadow-md transition duration-150">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-home text-blue-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Rental Management</h2>
                    </div>
                    <p class="text-slate-600 mb-4">Manage property listings and verify hosts.</p>
                    <div class="space-y-2">
                        <a href="rentals.php" 
                           class="block w-full text-left px-4 py-2.5 border border-blue-200 text-blue-700 hover:bg-blue-50 hover:border-blue-300 rounded-lg transition duration-150">
                            <i class="fas fa-list mr-2"></i> View All Rentals
                        </a>
                        <a href="rentals.php?status=pending" 
                           class="block w-full text-left px-4 py-2.5 border border-stone-200 text-slate-700 hover:bg-stone-50 hover:border-stone-300 rounded-lg transition duration-150">
                            <i class="fas fa-clock mr-2"></i> Pending Approvals
                        </a>
                    </div>
                </div>

                <!-- Bookings Management -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 hover:shadow-md transition duration-150">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-alt text-amber-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Booking Management</h2>
                    </div>
                    <p class="text-slate-600 mb-4">Monitor and manage all platform bookings.</p>
                    <div class="space-y-2">
                        <a href="bookings.php" 
                           class="block w-full text-left px-4 py-2.5 border border-amber-200 text-amber-700 hover:bg-amber-50 hover:border-amber-300 rounded-lg transition duration-150">
                            <i class="fas fa-list mr-2"></i> View All Bookings
                        </a>
                        <a href="bookings.php?status=confirmed" 
                           class="block w-full text-left px-4 py-2.5 border border-stone-200 text-slate-700 hover:bg-stone-50 hover:border-stone-300 rounded-lg transition duration-150">
                            <i class="fas fa-check-circle mr-2"></i> Confirmed Bookings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-stone-50 to-stone-100 rounded-xl p-6 border border-stone-200 mb-8">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="../index.php" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-external-link-alt text-emerald-600 mr-2"></i>
                        <span>View Public Site</span>
                    </a>
                    <a href="#" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        <span>View Analytics</span>
                    </a>
                    <a href="#" 
                       class="flex items-center justify-center p-4 bg-white rounded-lg border border-stone-300 hover:bg-stone-50 hover:border-stone-400 transition duration-150">
                        <i class="fas fa-cog text-slate-600 mr-2"></i>
                        <span>Admin Settings</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-900">Recent Activity</h2>
                    <span class="text-sm text-slate-500">Last 24 hours</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-stone-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user-plus text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">New user registration</p>
                            <p class="text-xs text-slate-500">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-stone-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-home text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">New rental listed</p>
                            <p class="text-xs text-slate-500">15 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-stone-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-check text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">New booking created</p>
                            <p class="text-xs text-slate-500">1 hour ago</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="#" class="text-emerald-700 hover:text-emerald-900 font-medium text-sm">
                        View full activity log <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>