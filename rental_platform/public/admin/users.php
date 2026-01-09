<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";

$db = new Database();
$pdo = $db->getConnection();

if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("
        UPDATE users
        SET is_active = NOT is_active
        WHERE id = :id
    ");
    $stmt->execute(['id' => $_GET['toggle']]);
    header("Location: users.php");
    exit;
}

$users = $pdo->query("
    SELECT id, full_name, email, role, is_active
    FROM users
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Admin Dashboard</title>
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
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">User Management</h1>
                        <p class="text-slate-600">Manage user accounts, roles, and permissions</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white rounded-lg shadow-sm p-4 border border-stone-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-700"><?= count($users) ?></div>
                                <div class="text-sm text-slate-500">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-stone-100 mb-8">
                <div class="px-6 py-4 border-b border-stone-200 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                            <i class="fas fa-users text-emerald-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">All Users</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search users..." 
                                   class="pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                        </div>
                        <button class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-sm hover:shadow transition duration-150">
                            <i class="fas fa-user-plus mr-2"></i> Add User
                        </button>
                    </div>
                </div>

                <?php if (empty($users)): ?>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-stone-100 mb-4">
                            <i class="fas fa-users text-3xl text-stone-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-2">No users found</h3>
                        <p class="text-slate-600 max-w-md mx-auto">There are no users in the database yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-stone-50 transition duration-150">
                                    <!-- User Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-100 to-teal-100 flex items-center justify-center font-bold text-emerald-700">
                                                    <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-slate-900"><?= htmlspecialchars($u['full_name']) ?></div>
                                                <div class="text-sm text-slate-500">ID: <?= $u['id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Email -->
                                    <td class="px-6 py-4">
                                        <div class="text-slate-800"><?= htmlspecialchars($u['email']) ?></div>
                                    </td>
                                    
                                    <!-- Role -->
                                    <td class="px-6 py-4">
                                        <?php
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'host' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'traveler' => 'bg-emerald-100 text-emerald-800 border-emerald-200'
                                        ];
                                        $color = $roleColors[$u['role']] ?? 'bg-stone-100 text-stone-800 border-stone-200';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border <?= $color ?>">
                                            <i class="fas fa-user-tag mr-1" style="font-size: 10px;"></i>
                                            <?= ucfirst($u['role']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <?php if ($u['is_active']): ?>
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
                                            <a href="?toggle=<?= $u['id'] ?>" 
                                               onclick="return confirm('Are you sure you want to <?= $u['is_active'] ? 'disable' : 'activate' ?> <?= htmlspecialchars($u['full_name']) ?>?');"
                                               class="inline-flex items-center px-3 py-1 <?= $u['is_active'] ? 'border border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400' : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-400' ?> rounded-lg text-sm font-medium transition duration-150">
                                                <?php if ($u['is_active']): ?>
                                                    <i class="fas fa-ban mr-1"></i> Disable
                                                <?php else: ?>
                                                    <i class="fas fa-check-circle mr-1"></i> Activate
                                                <?php endif; ?>
                                            </a>
                                            <a href="#" 
                                               class="inline-flex items-center px-3 py-1 border border-stone-300 text-slate-700 hover:bg-stone-50 hover:border-stone-400 rounded-lg text-sm font-medium transition duration-150">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
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

            <!-- User Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Active Users</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($users, fn($u) => $u['is_active'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-home text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Hosts</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($users, fn($u) => $u['role'] === 'host')) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                            <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Admins</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                            <i class="fas fa-suitcase-rolling text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Travelers</p>
                            <p class="text-2xl font-bold text-slate-900">
                                <?= count(array_filter($users, fn($u) => $u['role'] === 'traveler')) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bg-gradient-to-r from-stone-50 to-stone-100 rounded-xl p-6 border border-stone-200">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center">
                    <i class="fas fa-cogs text-slate-600 mr-2"></i> Bulk Actions
                </h3>
                <div class="flex items-center space-x-4">
                    <select class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option>Select action...</option>
                        <option>Activate selected</option>
                        <option>Disable selected</option>
                        <option>Change role</option>
                        <option>Delete selected</option>
                    </select>
                    <button class="px-4 py-2 border border-stone-300 bg-white text-slate-700 hover:bg-stone-50 rounded-lg transition">
                        Apply to selected
                    </button>
                    <button class="px-4 py-2 border border-stone-300 bg-white text-slate-700 hover:bg-stone-50 rounded-lg transition">
                        Export to CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add row selection functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('a') && !e.target.closest('button')) {
                        this.classList.toggle('bg-emerald-50');
                    }
                });
            });
        });
    </script>
</body>
</html>