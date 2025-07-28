<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg shadow-lg">
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
    <div class="px-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total SKU -->
        <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105">
            <div class="p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
    <div class="px-6">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="sm:flex sm:items-center sm:justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                        <!-- Filter Tipe Transaksi -->
                        <div class="relative inline-block text-left">
                            <select name="tipe" 
                                    onchange="window.location.href='<?= site_url('dashboard') ?>?tipe='+this.value+'<?= $search ? '&search='.$search : '' ?><?= $tanggal_awal ? '&tanggal_awal='.$tanggal_awal : '' ?><?= $tanggal_akhir ? '&tanggal_akhir='.$tanggal_akhir : '' ?>'"
                                    class="block w-56 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm rounded-md bg-white shadow-sm appearance-none cursor-pointer">
                                <option value="" class="py-2">Semua Transaksi</option>
                                <option value="masuk" <?= isset($_GET['tipe']) && $_GET['tipe'] == 'masuk' ? 'selected' : '' ?> class="py-2">Barang Masuk</option>
                                <option value="keluar" <?= isset($_GET['tipe']) && $_GET['tipe'] == 'keluar' ? 'selected' : '' ?> class="py-2">Barang Keluar</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <a href="<?= site_url('dashboard/export-aktivitas-excel' . (isset($_GET['search']) || isset($_GET['tanggal_awal']) || isset($_GET['tipe']) ? '?' . http_build_query($_GET) : '')) ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </a>
                        <a href="<?= site_url('dashboard/export-aktivitas-pdf' . (isset($_GET['search']) || isset($_GET['tanggal_awal']) || isset($_GET['tipe']) ? '?' . http_build_query($_GET) : '')) ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>

                <!-- Search and Date Filter Form -->
                <form method="get" class="mb-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Pencarian</label>
                            <div class="mt-1">
                                <input type="text" name="search" id="search" 
                                       value="<?= $search ?>"
                                       placeholder="Cari kode/nama barang..."
                                       class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <div class="mt-1">
                                <input type="date" name="tanggal_awal" id="tanggal_awal" 
                                       value="<?= $tanggal_awal ?>"
                                       onchange="this.form.submit()"
                                       class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <div class="mt-1">
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" 
                                       value="<?= $tanggal_akhir ?>"
                                       onchange="this.form.submit()"
                                       class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Menyimpan nilai tipe jika ada -->
                    <?php if (isset($_GET['tipe'])): ?>
                    <input type="hidden" name="tipe" value="<?= $_GET['tipe'] ?>">
                    <?php endif; ?>
                </form>

                <!-- Table -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-yellow-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($aktivitasTerbaru as $aktivitas): ?>
                                        <tr class="hover:bg-yellow-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('d/m/Y', strtotime($aktivitas['tanggal'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= $aktivitas['no_transaksi'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= $aktivitas['kode_barang'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= $aktivitas['nama_barang'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Rp <?= number_format($aktivitas['harga'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= number_format($aktivitas['jumlah']) ?> <?= $aktivitas['satuan'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <?php if ($aktivitas['tipe'] == 'masuk'): ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Masuk
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Keluar
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 