<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        warehouse: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#ca8a04',
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12',
                        },
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .input-transition {
            transition: all 0.2s ease-in-out;
        }
        .input-transition:focus {
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-warehouse-50 to-white min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8 animate-fadeIn">
            <div class="text-center">
                <!-- Warehouse Icon -->
                <div class="mx-auto w-20 h-20 bg-warehouse-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-warehouse-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Sistem Gudang</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Silakan login untuk melanjutkan
                </p>
            </div>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-100 rounded-lg p-4 animate-fadeIn">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <?= esc(session()->getFlashdata('error')) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="post" action="<?= site_url('login') ?>">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username / Email</label>
                        <input id="username" name="username" type="text" required 
                               class="input-transition appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-warehouse-500 focus:border-warehouse-500 sm:text-sm" 
                               placeholder="Masukkan username atau email">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required 
                                   class="input-transition appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-warehouse-500 focus:border-warehouse-500 sm:text-sm" 
                                   placeholder="Masukkan password">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-warehouse-400 to-warehouse-500 hover:from-warehouse-500 hover:to-warehouse-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500 transform transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-warehouse-200 group-hover:text-warehouse-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Login
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="mt-4 text-center text-sm text-gray-500 animate-fadeIn" style="animation-delay: 0.2s">
            &copy; <?= date('Y') ?> Sistem Gudang. All rights reserved.
        </div>
    </div>
</body>
</html> 