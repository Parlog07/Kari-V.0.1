<?php
require_once "../config/autoload.php";
include "../views/navbar.php";

$error = null;

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        $userModel = new User($pdo);
        $user = $userModel->login($email, $password);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        header("Location: index.php");
        exit;

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
    <title>Login | Kari</title>
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
                <h2 class="mt-4 text-3xl font-bold text-slate-900">Welcome back</h2>
                <p class="mt-2 text-slate-600">Sign in to your account</p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-bold text-red-800">Login Error</h3>
                            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-stone-100">
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-envelope text-emerald-500 mr-2"></i> Email Address
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-slate-700">
                                <i class="fas fa-lock text-emerald-500 mr-2"></i> Password
                            </label>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-800 transition">Forgot password?</a>
                        </div>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition placeholder-slate-400"
                               placeholder="••••••••">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" 
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-stone-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-slate-700">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition duration-150 flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-stone-200">
                    <p class="text-center text-slate-600">
                        Don't have an account?
                        <a href="register.php" class="font-medium text-emerald-600 hover:text-emerald-800 transition ml-1">
                            Sign up now
                        </a>
                    </p>
                </div>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-stone-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-slate-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" 
                                class="w-full inline-flex justify-center py-2.5 px-4 border border-stone-300 rounded-lg shadow-sm bg-white text-sm font-medium text-slate-700 hover:bg-stone-50 transition">
                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                        </button>
                        <button type="button" 
                                class="w-full inline-flex justify-center py-2.5 px-4 border border-stone-300 rounded-lg shadow-sm bg-white text-sm font-medium text-slate-700 hover:bg-stone-50 transition">
                            <i class="fab fa-facebook text-blue-500 mr-2"></i> Facebook
                        </button>
                    </div>
                </div>
            </div>

            <!-- Guest Access Note -->
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-500">
                    Want to browse first?
                    <a href="index.php" class="text-emerald-600 hover:text-emerald-800 font-medium">
                        Explore rentals as guest
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>