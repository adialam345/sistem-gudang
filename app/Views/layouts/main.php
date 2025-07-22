<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                    screens: {
                        'xs': '375px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                    },
                },
            },
        }
    </script>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
        }

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
            padding-top: 64px;
            width: 100%;
            min-width: 320px; /* Minimum width to prevent squishing */
            overflow-x: hidden; /* Prevent horizontal scroll */
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
            width: 100%;
            max-width: 100%;
        }

        /* Better table readability */
        table {
            border-spacing: 0;
            width: 100%;
            max-width: 100%;
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
            word-break: break-word;
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
            /* Container adjustments to match banner */
            .table-responsive-container {
                width: 100%;
                margin: 0 auto;
                padding: 0 1rem;
            }

            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
                margin: 0 auto;
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
                width: 100%;
            }
            
            .table-responsive td {
                display: flex !important;
                padding: 0.75rem 1rem !important;
                border-bottom: 1px solid #e5e7eb;
                align-items: center;
                min-height: 48px;
                width: 100%;
                justify-content: space-between !important;
            }
            
            .table-responsive td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                flex: 1;
            }

            .table-responsive td > span,
            .table-responsive td > div,
            .table-responsive td > a,
            .table-responsive td > form,
            .table-responsive td > input,
            .table-responsive td > select {
                text-align: right;
                flex: 1;
            }
            
            .table-responsive td:last-child {
                border-bottom: none;
            }

            /* Special styling for action buttons */
            .table-responsive td[data-label="Aksi"] {
                display: flex !important;
                justify-content: space-between !important;
                padding: 0.75rem 1rem !important;
            }

            .table-responsive td[data-label="Aksi"] > :not(:first-child) {
                display: flex;
                gap: 1rem;
            }

            /* Badge/status styling */
            .table-responsive td .inline-flex {
                justify-content: flex-end;
            }

            /* Match banner padding */
            .px-4.sm\:px-6.py-5 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            /* Match banner container width */
            .bg-white.rounded-lg.shadow-lg {
                width: 100%;
                margin: 0 auto;
            }

            /* Table container adjustments */
            .overflow-x-auto {
                padding: 0 !important;
            }

            /* Ensure consistent spacing */
            .space-y-8 > * {
                margin: 0 auto;
                width: 100%;
            }

            /* Welcome banner width reference */
            .bg-gradient-to-r {
                width: 100%;
                margin: 0 auto;
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

            /* Adjust input sizes on mobile */
            input, select, textarea {
                font-size: 16px !important; /* Prevent zoom on iOS */
            }
        }

        /* Container width adjustments */
        .max-w-7xl {
            width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 640px) {
            .max-w-7xl {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        /* Sticky navbar */
        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 64px;
            background-color: white;
            width: 100%;
        }

        /* Mobile menu adjustments */
        #mobile-menu {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            z-index: 40;
            width: 100%;
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
            margin-top: 1rem;
            width: 100%;
        }
        footer {
            flex-shrink: 0;
        }

        /* Welcome banner adjustments */
        .welcome-banner {
            margin-top: 1rem;
        }

        /* Responsive text adjustments */
        @media (max-width: 640px) {
            html {
                font-size: 14px;
            }
            
            h1 {
                font-size: 1.5rem !important;
            }
            
            h2 {
                font-size: 1.25rem !important;
            }
            
            .text-sm {
                font-size: 0.875rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Top Navigation -->
    <nav class="navbar-fixed shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="flex justify-between items-center h-full">
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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