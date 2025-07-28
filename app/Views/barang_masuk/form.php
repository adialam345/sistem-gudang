<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold text-gray-900"><?= isset($barangMasuk) ? 'Edit Barang Masuk' : 'Tambah Barang Masuk' ?></h1>
        <div class="mt-4 sm:mt-0">
            <a href="<?= site_url('barang-masuk') ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="<?= site_url('barang-masuk/' . (isset($barangMasuk) ? 'update/' . $barangMasuk['id'] : 'store')) ?>" method="post" class="space-y-6">
                <?= csrf_field() ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <?= session()->getFlashdata('error') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <div class="mt-1">
                            <input type="date" name="tanggal" id="tanggal" 
                                   value="<?= isset($barangMasuk) ? $barangMasuk['tanggal'] : old('tanggal', date('Y-m-d')) ?>"
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('tanggal')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('tanggal') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="barang_id" class="block text-sm font-medium text-gray-700">Barang</label>
                        <div class="mt-1">
                            <select id="barang_id" name="barang_id" 
                                    class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($barang as $item) : ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-kode="<?= esc($item['kode']) ?>"
                                            data-satuan="<?= esc($item['satuan']) ?>"
                                            data-stok="<?= $item['stok'] ?>"
                                            data-harga="<?= $item['harga'] ?>"
                                            <?= (isset($barangMasuk) && $barangMasuk['barang_id'] == $item['id']) || old('barang_id') == $item['id'] ? 'selected' : '' ?>>
                                        <?= esc($item['kode'] . ' - ' . $item['nama'] . ' (Stok: ' . number_format($item['stok']) . ' ' . $item['satuan'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('barang_id')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('barang_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <div class="mt-1">
                            <input type="number" name="jumlah" id="jumlah" 
                                   value="<?= isset($barangMasuk) ? esc($barangMasuk['jumlah']) : old('jumlah') ?>"
                                   class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('jumlah')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('jumlah') ?></p>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="harga" id="harga" value="<?= isset($barangMasuk) ? esc($barangMasuk['harga']) : old('harga', 0) ?>">

                    <div class="sm:col-span-3">
                        <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <div class="mt-1">
                            <input type="text" name="supplier" id="supplier" 
                                   value="<?= isset($barangMasuk) ? esc($barangMasuk['supplier']) : old('supplier') ?>"
                                   class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('supplier')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('supplier') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <div class="mt-1">
                            <textarea id="keterangan" name="keterangan" rows="3" 
                                      class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md"><?= isset($barangMasuk) ? esc($barangMasuk['keterangan']) : old('keterangan') ?></textarea>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('keterangan')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('keterangan') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="reset" 
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <?= isset($barangMasuk) ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tom Select for barang dropdown
    const tomSelect = new TomSelect('#barang_id', {
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
        placeholder: 'Cari atau pilih barang...',
        onChange: function() {
            updateBarangInfo();
        }
    });

    // Satuan and price update
    const barangSelect = document.getElementById('barang_id');
    const hargaInput = document.getElementById('harga');
    
    function updateBarangInfo() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            hargaInput.value = selectedOption.dataset.harga || 0;
        }
    }
    
    // Initial update
    updateBarangInfo();
});
</script>
<?= $this->endSection() ?> 