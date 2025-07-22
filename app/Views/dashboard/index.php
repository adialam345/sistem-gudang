<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-warehouse-400 to-warehouse-500 rounded-lg shadow-lg">
        <div class="px-6 py-8 sm:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Selamat Datang di SisGudang</h1>
                    <p class="mt-2 text-gray-900">Sistem Informasi Pergudangan yang Memudahkan Pengelolaan Stok Anda</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-24 h-24 text-gray-900" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total SKU -->
        <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-5 flex-1">
                        <div class="text-sm font-medium text-gray-500">Total SKU</div>
                        <div class="mt-1 flex items-baseline flex-wrap">
                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900"><?= number_format($totalSKU) ?></div>
                            <div class="ml-2 text-sm font-medium text-gray-500">Jenis Barang</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stok -->
        <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-5 flex-1">
                        <div class="text-sm font-medium text-gray-500">Total Stok</div>
                        <div class="mt-1 flex items-baseline flex-wrap">
                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900"><?= number_format($totalStok) ?></div>
                            <div class="ml-2 text-sm font-medium text-gray-500">Unit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Masuk Hari Ini -->
        <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-indigo-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-5 flex-1">
                        <div class="text-sm font-medium text-gray-500">Barang Masuk Hari Ini</div>
                        <div class="mt-1 flex items-baseline flex-wrap">
                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900"><?= number_format($masukHariIni) ?></div>
                            <div class="ml-2 text-sm font-medium text-gray-500">Unit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Keluar Hari Ini -->
        <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-5 flex-1">
                        <div class="text-sm font-medium text-gray-500">Barang Keluar Hari Ini</div>
                        <div class="mt-1 flex items-baseline flex-wrap">
                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900"><?= number_format($keluarHariIni) ?></div>
                            <div class="ml-2 text-sm font-medium text-gray-500">Unit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow-lg mt-8">
        <div class="px-4 sm:px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
                <h2 class="text-xl font-semibold text-gray-900">Aktivitas Terbaru</h2>
                <span class="ml-2 px-3 py-1 text-xs font-medium bg-warehouse-100 text-warehouse-800 rounded-full">Live</span>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-responsive">
                    <thead>
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($aktivitasTerbaru)) : ?>
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-4 text-gray-500">Belum ada aktivitas</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($aktivitasTerbaru as $aktivitas) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Tanggal">
                                        <?= date('d/m/Y', strtotime($aktivitas['tanggal'])) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="No Transaksi">
                                        <?= esc($aktivitas['no_transaksi']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Kode">
                                        <?= esc($aktivitas['kode_barang']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Nama Barang">
                                        <?= esc($aktivitas['nama_barang']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Jumlah">
                                        <?= number_format($aktivitas['jumlah']) ?> <?= esc($aktivitas['satuan']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap" data-label="Status">
                                        <?php if ($aktivitas['tipe'] === 'masuk') : ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Masuk
                                            </span>
                                        <?php else : ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Keluar
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 