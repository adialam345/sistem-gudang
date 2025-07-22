<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Gudang' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        warehouse: {
                            50: '#FFF9E5',
                            100: '#FFF2CC',
                            200: '#FFE699',
                            300: '#FFD966',
                            400: '#FFC933',
                            500: '#FFB800',
                            600: '#CC9200',
                            700: '#996D00',
                            800: '#664800',
                            900: '#332400',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #FFB800;
            border-radius: 6px;
            border: 3px solid #f1f1f1;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #CC9200;
        }

        /* Increased text contrast and readability */
        body {
            font-size: 16px;
            line-height: 1.6;
            color: #1a1a1a;
        }
        
        /* Better focus indicators */
        *:focus {
            outline: 3px solid #FFB800 !important;
            outline-offset: 2px !important;
        }

        /* Improved button contrast */
        .btn-primary {
            background-color: #FFB800 !important;
            color: #1a1a1a !important;
            font-weight: 600 !important;
        }
        .btn-primary:hover {
            background-color: #CC9200 !important;
        }

        /* Enhanced form controls */
        input, select, textarea {
            font-size: 16px !important;
            padding: 0.75rem 1rem !important;
        }

        /* Better table readability */
        table {
            border-spacing: 0;
            width: 100%;
        }
        th {
            background-color: #FFF9E5 !important;
            color: #1a1a1a !important;
            font-weight: 600 !important;
            text-transform: none !important;
            font-size: 16px !important;
            padding: 1rem !important;
        }
        td {
            padding: 1rem !important;
            font-size: 16px !important;
        }
        tr:nth-child(even) {
            background-color: #FFF9E5;
        }

        /* Ensure text contrast on yellow backgrounds */
        [class*="bg-warehouse-"] {
            color: #1a1a1a !important;
        }
        
        /* Exception for primary buttons */
        .bg-warehouse-500, 
        .bg-warehouse-600,
        .hover\:bg-warehouse-500:hover,
        .hover\:bg-warehouse-600:hover {
            color: #1a1a1a !important;
        }

        /* Fix navigation text colors */
        .text-warehouse-500 {
            color: #996D00 !important;
        }
        .hover\:text-warehouse-500:hover {
            color: #996D00 !important;
        }
        .border-warehouse-500 {
            border-color: #996D00 !important;
        }

        /* Responsive table */
        @media (max-width: 640px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table-responsive thead {
                display: none;
            }
            
            .table-responsive tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                background-color: white;
            }
            
            .table-responsive td {
                display: flex;
                padding: 0.75rem !important;
                border-bottom: 1px solid #e5e7eb;
                text-align: right;
            }
            
            .table-responsive td::before {
                content: attr(data-label);
                font-weight: 600;
                margin-right: auto;
                text-align: left;
            }
            
            .table-responsive td:last-child {
                border-bottom: none;
            }
        }

        /* Responsive forms */
        @media (max-width: 640px) {
            .form-responsive {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            
            .form-responsive > div {
                width: 100%;
            }
        }

        /* Sticky footer */
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-warehouse-500">SisGudang</span>
                    </div>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="<?= site_url('dashboard') ?>" 
                           class="<?= current_url() == site_url('dashboard') ? 'border-warehouse-500 text-warehouse-500' : 'border-transparent text-gray-700' ?> hover:text-warehouse-500 inline-flex items-center px-2 pt-1 border-b-4 text-base font-medium transition-colors duration-200">
                            Dashboard
                        </a>
                        <a href="<?= site_url('barang') ?>"
                           class="<?= current_url() == site_url('barang') ? 'border-warehouse-500 text-warehouse-500' : 'border-transparent text-gray-700' ?> hover:text-warehouse-500 inline-flex items-center px-2 pt-1 border-b-4 text-base font-medium transition-colors duration-200">
                            Barang
                        </a>
                        <a href="<?= site_url('barang-masuk') ?>"
                           class="<?= current_url() == site_url('barang-masuk') ? 'border-warehouse-500 text-warehouse-500' : 'border-transparent text-gray-700' ?> hover:text-warehouse-500 inline-flex items-center px-2 pt-1 border-b-4 text-base font-medium transition-colors duration-200">
                            Barang Masuk
                        </a>
                        <a href="<?= site_url('barang-keluar') ?>"
                           class="<?= current_url() == site_url('barang-keluar') ? 'border-warehouse-500 text-warehouse-500' : 'border-transparent text-gray-700' ?> hover:text-warehouse-500 inline-flex items-center px-2 pt-1 border-b-4 text-base font-medium transition-colors duration-200">
                            Barang Keluar
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700 text-base"><?= session()->get('user_name') ?></span>
                            <a href="<?= site_url('logout') ?>" 
                               class="btn-primary px-4 py-2 rounded-lg text-base transition-colors duration-200">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" 
                            class="inline-flex items-center justify-center p-3 rounded-lg text-gray-700 hover:text-warehouse-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-warehouse-500 transition-colors duration-200"
                            onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden sm:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="<?= site_url('dashboard') ?>" 
                   class="<?= current_url() == site_url('dashboard') ? 'bg-warehouse-50 text-warehouse-500' : 'text-gray-700' ?> block pl-3 pr-4 py-4 text-base font-medium hover:bg-warehouse-50 hover:text-warehouse-500 transition-colors duration-200">
                    Dashboard
                </a>
                <a href="<?= site_url('barang') ?>"
                   class="<?= current_url() == site_url('barang') ? 'bg-warehouse-50 text-warehouse-500' : 'text-gray-700' ?> block pl-3 pr-4 py-4 text-base font-medium hover:bg-warehouse-50 hover:text-warehouse-500 transition-colors duration-200">
                    Barang
                </a>
                <a href="<?= site_url('barang-masuk') ?>"
                   class="<?= current_url() == site_url('barang-masuk') ? 'bg-warehouse-50 text-warehouse-500' : 'text-gray-700' ?> block pl-3 pr-4 py-4 text-base font-medium hover:bg-warehouse-50 hover:text-warehouse-500 transition-colors duration-200">
                    Barang Masuk
                </a>
                <a href="<?= site_url('barang-keluar') ?>"
                   class="<?= current_url() == site_url('barang-keluar') ? 'bg-warehouse-50 text-warehouse-500' : 'text-gray-700' ?> block pl-3 pr-4 py-4 text-base font-medium hover:bg-warehouse-50 hover:text-warehouse-500 transition-colors duration-200">
                    Barang Keluar
                </a>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4 py-4">
                        <div class="flex-shrink-0">
                            <span class="text-gray-700 text-base"><?= session()->get('user_name') ?></span>
                        </div>
                    </div>
                    <div class="mt-3 px-2">
                        <a href="<?= site_url('logout') ?>"
                           class="btn-primary block px-4 py-2 rounded-lg text-center text-base transition-colors duration-200">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-700 text-base">&copy; <?= date('Y') ?> Sistem Gudang. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 