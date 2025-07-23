<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-warehouse-400 to-warehouse-500 rounded-lg shadow-lg">
        <div class="px-6 py-8 sm:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Barang Masuk</h1>
                    <p class="mt-2 text-gray-900">Kelola transaksi barang masuk dengan mudah dan terstruktur</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-24 h-24 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 3H4a2 2 0 00-2 2v14a2 2 0 002 2h16a2 2 0 002-2V5a2 2 0 00-2-2zM12 9v6m-3-3h6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M5 15h.01M5 18h.01M8 18h.01M11 18h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div class="flex justify-end">
        <a href="<?= site_url('barang-masuk/create') ?>" 
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-warehouse-500 shadow-lg hover:bg-warehouse-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500 transition-all duration-200 hover:scale-105">
            <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Barang Masuk
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-4 sm:px-6 py-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <form id="searchForm" action="<?= site_url('barang-masuk') ?>" method="get">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 form-responsive">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                        <div class="mt-1">
                            <input type="text" name="search" id="searchInput" value="<?= esc($search) ?>" 
                                   placeholder="Kode, nama, atau no transaksi" 
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-lg" 
                                   autocomplete="off">
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                        <div class="mt-1">
                            <input type="date" name="tanggal_awal" id="tanggal_awal" value="<?= esc($tanggal_awal ?? '') ?>" 
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <div class="mt-1">
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="<?= esc($tanggal_akhir ?? '') ?>" 
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-warehouse-500 hover:bg-warehouse-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500 transition-colors duration-200">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Daftar Barang Masuk</h2>
                <div class="flex space-x-2">
                    <a href="<?= site_url('barang-masuk/export-excel') . '?' . http_build_query($_GET) ?>" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </a>
                    <a href="<?= site_url('barang-masuk/export-pdf') . '?' . http_build_query($_GET) ?>" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-responsive">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 sm:px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($barangMasuk)) : ?>
                            <tr>
                                <td colspan="8" class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-4 text-gray-500">Belum ada data</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($barangMasuk as $item) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Tanggal">
                                        <?= date('d/m/Y', strtotime($item['tanggal'])) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="No Transaksi">
                                        <?= esc($item['no_transaksi']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Kode Barang">
                                        <?= esc($item['kode_barang']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Nama Barang">
                                        <?= esc($item['nama_barang']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap" data-label="Jumlah">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <?= number_format($item['jumlah']) ?> <?= esc($item['satuan']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Supplier">
                                        <?= esc($item['supplier']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-label="Keterangan">
                                        <?= esc($item['keterangan']) ?>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="Aksi">
                                        <a href="<?= site_url('barang-masuk/edit/' . $item['id']) ?>" 
                                           class="text-warehouse-600 hover:text-warehouse-900 mr-3">Edit</a>
                                        <form action="<?= site_url('barang-masuk/delete/' . $item['id']) ?>" method="post" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <?= $pager->links() ?>
            </div>
        </div>
    </div>
</div>

<script>
// Live search: submit form on input (only for search field)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchForm.submit();
        }, 400); // debounce
    });
});
</script>

<?= $this->endSection() ?> 