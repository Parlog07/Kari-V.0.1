<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'traveler';

    try {
        if (empty($fullName) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }

        $db = new Database();
        $pdo = $db->getConnection();

        $userModel = new User($pdo);
        $userModel->register([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        $success = "Account created successfully. You can now login.";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Kari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-10">
                <a href="index.php" class="inline-block">
                    <span class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Kari</span>
                </a>
                <h2 class="mt-4 text-3xl font-bold text-slate-900">Create your account</h2>
                <p class="mt-2 text-slate-600">Join our community of travelers and hosts</p>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-bold text-red-800">Registration Error</h3>
                            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-bold text-emerald-800">Registration Successful!</h3>
                            <p class="text-emerald-700"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="login.php" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-medium rounded-lg shadow-sm hover:shadow transition">
                            <i class="fas fa-sign-in-alt mr-2"></i> Go to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-stone-100">
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-user text-emerald-500 mr-2"></i> Full Name
                        </label>
                        <input type="text" name="full_name" required
                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                               placeholder="John Doe">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-envelope text-emerald-500 mr-2"></i> Email Address
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-lock text-emerald-500 mr-2"></i> Password
                        </label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                               placeholder="••••••••">
                        <p class="mt-2 text-xs text-slate-500">Must be at least 8 characters long</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-user-tag text-emerald-500 mr-2"></i> I want to join as
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <input type="radio" id="traveler" name="role" value="traveler" checked 
                                       class="sr-only peer">
                                <label for="traveler" 
                                       class="flex flex-col items-center p-4 border-2 border-stone-300 rounded-xl cursor-pointer hover:border-emerald-400 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition duration-150">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-suitcase-rolling text-emerald-600"></i>
                                    </div>
                                    <span class="font-medium text-slate-800">Traveler</span>
                                    <span class="text-xs text-slate-500 mt-1">Book stays</span>
                                </label>
                            </div>
                            
                            <div class="relative">
                                <input type="radio" id="host" name="role" value="host"
                                       class="sr-only peer">
                                <label for="host" 
                                       class="flex flex-col items-center p-4 border-2 border-stone-300 rounded-xl cursor-pointer hover:border-emerald-400 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition duration-150">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-home text-teal-600"></i>
                                    </div>
                                    <span class="font-medium text-slate-800">Host</span>
                                    <span class="text-xs text-slate-500 mt-1">List properties</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" required
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-stone-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-slate-700">
                            I agree to the
                            <a href="#" class="text-emerald-600 hover:text-emerald-800 font-medium">Terms of Service</a>
                            and
                            <a href="#" class="text-emerald-600 hover:text-emerald-800 font-medium">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition duration-150 flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-stone-200 text-center">
                    <p class="text-slate-600">
                        Already have an account?
                        <a href="login.php" class="font-medium text-emerald-600 hover:text-emerald-800 transition ml-1">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="mt-8 grid grid-cols-2 gap-4 text-center">
                <div class="bg-white p-4 rounded-xl border border-stone-100">
                    <i class="fas fa-shield-alt text-emerald-500 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-slate-800">Secure platform</p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-stone-100">
                    <i class="fas fa-headset text-emerald-500 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-slate-800">24/7 Support</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>