<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg shadow-lg">
        <div class="px-6 py-8 sm:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Data Barang</h1>
                    <p class="mt-2 text-gray-900">Kelola data barang dengan mudah dan terstruktur</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-24 h-24 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end">
        <?php if (session()->get('role') === 'admin'): ?>
        <a href="<?= site_url('barang/create') ?>" 
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-yellow-500 shadow-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 hover:scale-105">
            <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Barang
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter dan pencarian -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-4 sm:px-6 py-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <form id="searchForm" action="<?= current_url() ?>" method="get">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 form-responsive">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                        <div class="mt-1">
                            <input type="text" name="search" id="searchInput" value="<?= esc($search) ?>" 
                                   placeholder="Kode, nama, atau deskripsi" 
                                   class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-lg" 
                                   autocomplete="off">
                        </div>
                    </div>
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <div class="mt-1">
                            <select name="kategori" 
                                    onchange="window.location.href='<?= site_url('barang') ?>?kategori='+this.value+'<?= $search ? '&search='.$search : '' ?><?= $sort ? '&sort='.$sort : '' ?>'"
                                    class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                    <?= $category['nama'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700">Urutkan</label>
                        <div class="mt-1">
                            <select name="sort" 
                                    onchange="window.location.href='<?= site_url('barang') ?>?sort='+this.value+'<?= $search ? '&search='.$search : '' ?><?= $selectedCategory ? '&kategori='.$selectedCategory : '' ?>'"
                                    class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                                <option value="">Urutkan</option>
                                <option value="nama_asc" <?= $sort == 'nama_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                                <option value="nama_desc" <?= $sort == 'nama_desc' ? 'selected' : '' ?>>Nama (Z-A)</option>
                                <option value="stok_asc" <?= $sort == 'stok_asc' ? 'selected' : '' ?>>Stok (Rendah-Tinggi)</option>
                                <option value="stok_desc" <?= $sort == 'stok_desc' ? 'selected' : '' ?>>Stok (Tinggi-Rendah)</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
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

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-4 sm:px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Barang</h2>
                <div class="flex space-x-2">
                    <a href="<?= site_url('barang/export-excel') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </a>
                    <a href="<?= site_url('barang/export-pdf') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
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
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <?php if (session()->get('role') === 'admin'): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($barang)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-4 text-gray-500">Belum ada data</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($barang as $item): ?>
                        <tr class="hover:bg-yellow-50">
                            <td class="px-6 py-4 text-sm text-gray-900" data-label="Kode">
                                <?= esc($item['kode']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900" data-label="Nama">
                                <?= esc($item['nama']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" data-label="Kategori">
                                <?= esc($item['kategori'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" data-label="Satuan">
                                <?= esc($item['satuan']) ?>
                            </td>
                            <td class="px-6 py-4" data-label="Stok">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['stok'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= number_format($item['stok']) ?> <?= esc($item['satuan']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" data-label="Harga">
                                Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" data-label="Deskripsi">
                                <?= esc($item['deskripsi'] ?: '-') ?>
                            </td>
                            <?php if (session()->get('role') === 'admin'): ?>
                            <td class="px-6 py-4" data-label="Aksi">
                                <div class="flex items-center space-x-2">
                                    <a href="<?= site_url('barang/edit/' . $item['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="<?= site_url('barang/delete/' . $item['id']) ?>" method="post" class="inline" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
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