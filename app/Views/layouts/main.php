<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?? 'AminsGudang' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#E6E7F0',
                            100: '#C4C6DC',
                            200: '#9EA2C7',
                            300: '#777DB1',
                            400: '#4F569B',
                            500: '#09186C', // Main Blue
                            600: '#081661',
                            700: '#061356',
                            800: '#040F4B',
                            900: '#020C40',
                        },
                        secondary: {
                            50: '#FFF8E5',
                            100: '#FEEFC3',
                            200: '#FDE59D',
                            300: '#FCDB77',
                            400: '#FBD152',
                            500: '#FAAA00', // Main Yellow
                            600: '#E69D00',
                            700: '#CC8B00',
                            800: '#B37A00',
                            900: '#996800',
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
            background: #09186C;
            border-radius: 6px;
            border: 3px solid #f1f1f1;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #061356;
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
            outline: 3px solid #FAAA00 !important;
            outline-offset: 2px !important;
        }

        /* Improved button contrast */
        .btn-primary {
            background-color: #09186C !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
        }
        .btn-primary:hover {
            background-color: #061356 !important;
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
            background-color: #E6E7F0 !important;
            color: #09186C !important;
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
            background-color: #F8F9FF;
        }

        /* Ensure text contrast on yellow backgrounds */
        [class*="bg-primary-"] {
            color: #1a1a1a !important;
        }
        
        /* Exception for primary buttons */
        .bg-primary-500, 
        .bg-primary-600,
        .hover\:bg-primary-500:hover,
        .hover\:bg-primary-600:hover {
            color: #1a1a1a !important;
        }

        /* Fix navigation text colors */
        .text-primary-500 {
            color: #09186C !important;
        }
        .hover\:text-primary-500:hover {
            color: #09186C !important;
        }
        .border-primary-500 {
            border-color: #09186C !important;
        }

        /* Responsive table */
        @media (max-width: 640px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: hidden;
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
                width: 100%;
            }
            
            .table-responsive td {
                display: flex !important;
                padding: 0.75rem 1.5rem !important;
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
                flex: none;
                margin-right: 1rem;
                width: 100px;
                min-width: 100px;
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
                padding: 0.75rem 1.5rem !important;
            }

            .table-responsive td[data-label="Aksi"] > :not(:first-child) {
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
            }

            /* Badge/status styling */
            .table-responsive td .inline-flex {
                justify-content: flex-end;
                width: auto;
            }

            /* Container width adjustments */
            .max-w-7xl {
                width: 100%;
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            /* Table container adjustments */
            .overflow-x-auto {
                margin: 0;
                width: 100%;
            }

            /* Ensure table takes full width */
            .table-responsive tbody {
                display: block;
                width: 100%;
            }

            /* Add proper padding to containers */
            .px-6 {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }

            /* Statistics cards container */
            .grid {
                padding: 0 !important;
            }

            /* Activity list container */
            .bg-white.rounded-lg.shadow-lg {
                margin: 0;
            }

            /* Welcome banner adjustments */
            .bg-gradient-to-r {
                margin: 0;
            }

            /* Adjust spacing for better mobile view */
            .space-y-8 > * + * {
                margin-top: 2rem;
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
    <nav class="navbar-fixed shadow-lg border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="flex justify-between items-center h-full">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold">
                            <span class="text-primary-500">Amins</span><span class="text-secondary-500">Gudang</span>
                        </span>
                    </div>
                    <!-- Navigation Menu -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="<?= site_url('dashboard') ?>" 
                           class="<?= current_url() == site_url('dashboard') ? 'text-secondary-500 after:w-full after:bg-secondary-500' : 'text-gray-700' ?> group relative inline-flex items-center px-2 pt-1 text-base font-medium transition-all duration-200 hover:bg-gradient-to-r hover:from-primary-500 hover:to-secondary-500 hover:bg-clip-text hover:text-transparent after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all after:duration-300 after:ease-out hover:after:w-full hover:after:bg-gradient-to-r hover:after:from-primary-500 hover:after:to-secondary-500">
                            Dashboard
                        </a>
                        <a href="<?= site_url('barang') ?>"
                           class="<?= current_url() == site_url('barang') ? 'text-secondary-500 after:w-full after:bg-secondary-500' : 'text-gray-700' ?> group relative inline-flex items-center px-2 pt-1 text-base font-medium transition-all duration-200 hover:bg-gradient-to-r hover:from-primary-500 hover:to-secondary-500 hover:bg-clip-text hover:text-transparent after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all after:duration-300 after:ease-out hover:after:w-full hover:after:bg-gradient-to-r hover:after:from-primary-500 hover:after:to-secondary-500">
                            Barang
                        </a>
                        <a href="<?= site_url('barang-masuk') ?>"
                           class="<?= current_url() == site_url('barang-masuk') ? 'text-secondary-500 after:w-full after:bg-secondary-500' : 'text-gray-700' ?> group relative inline-flex items-center px-2 pt-1 text-base font-medium transition-all duration-200 hover:bg-gradient-to-r hover:from-primary-500 hover:to-secondary-500 hover:bg-clip-text hover:text-transparent after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all after:duration-300 after:ease-out hover:after:w-full hover:after:bg-gradient-to-r hover:after:from-primary-500 hover:after:to-secondary-500">
                            Barang Masuk
                        </a>
                        <a href="<?= site_url('barang-keluar') ?>"
                           class="<?= current_url() == site_url('barang-keluar') ? 'text-secondary-500 after:w-full after:bg-secondary-500' : 'text-gray-700' ?> group relative inline-flex items-center px-2 pt-1 text-base font-medium transition-all duration-200 hover:bg-gradient-to-r hover:from-primary-500 hover:to-secondary-500 hover:bg-clip-text hover:text-transparent after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all after:duration-300 after:ease-out hover:after:w-full hover:after:bg-gradient-to-r hover:after:from-primary-500 hover:after:to-secondary-500">
                            Barang Keluar
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative flex items-center space-x-4">
                        <!-- User Profile -->
                        <div class="flex items-center space-x-3">
                            <!-- Profile Vector -->
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center transform transition-transform duration-200 hover:scale-110">
                                <svg class="h-6 w-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <!-- User Info -->
                            <div class="flex flex-col">
                                <span class="text-m font-semibold text-primary-600"><?= session()->get('name') ?></span>
                                <span class="text-xs font-medium text-secondary-500"><?= ucfirst(session()->get('role')) ?></span>
                            </div>
                        </div>
                        <div class="h-6 w-px bg-gray-200"></div>
                        <!-- Logout Button with Icon -->
                        <a href="<?= site_url('logout') ?>" 
                           class="group inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-1.5 transition-transform duration-200 group-hover:rotate-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-white font-semibold">Logout</span>
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" 
                            class="inline-flex items-center justify-center p-3 rounded-lg text-gray-700 hover:text-primary-500 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition-all duration-200 hover:shadow-md"
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
                   class="<?= current_url() == site_url('dashboard') ? 'bg-gradient-to-r from-primary-500 to-secondary-500 text-white' : 'text-gray-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-500' ?> block pl-3 pr-4 py-4 text-base font-medium transition-all duration-200">
                    Dashboard
                </a>
                <a href="<?= site_url('barang') ?>"
                   class="<?= current_url() == site_url('barang') ? 'bg-gradient-to-r from-primary-500 to-secondary-500 text-white' : 'text-gray-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-500' ?> block pl-3 pr-4 py-4 text-base font-medium transition-all duration-200">
                    Barang
                </a>
                <a href="<?= site_url('barang-masuk') ?>"
                   class="<?= current_url() == site_url('barang-masuk') ? 'bg-gradient-to-r from-primary-500 to-secondary-500 text-white' : 'text-gray-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-500' ?> block pl-3 pr-4 py-4 text-base font-medium transition-all duration-200">
                    Barang Masuk
                </a>
                <a href="<?= site_url('barang-keluar') ?>"
                   class="<?= current_url() == site_url('barang-keluar') ? 'bg-gradient-to-r from-primary-500 to-secondary-500 text-white' : 'text-gray-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-500' ?> block pl-3 pr-4 py-4 text-base font-medium transition-all duration-200">
                    Barang Keluar
                </a>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4 py-4">
                        <!-- User Profile Mobile -->
                        <div class="flex items-center space-x-3">
                            <!-- Profile Vector Mobile -->
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <!-- User Info Mobile -->
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900"><?= session()->get('username') ?></span>
                                <span class="text-xs font-medium text-primary-600"><?= ucfirst(session()->get('role')) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 px-2">
                        <a href="<?= site_url('logout') ?>"
                           class="group inline-flex items-center justify-center w-full px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-lg">
                            <svg class="w-5 h-5 mr-1.5 transition-transform duration-200 group-hover:rotate-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-white font-semibold">Logout</span>
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
            <p class="text-center text-gray-700 text-base">&copy; <?= date('Y') ?> AminsGudang. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 