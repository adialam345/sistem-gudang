<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold text-gray-900"><?= isset($barangKeluar) ? 'Edit Barang Keluar' : 'Tambah Barang Keluar' ?></h1>
        <div class="mt-4 sm:mt-0">
            <a href="<?= site_url('barang-keluar') ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="<?= site_url('barang-keluar/' . (isset($barangKeluar) ? 'update/' . $barangKeluar['id'] : 'store')) ?>" method="post" class="space-y-6">
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
                                   value="<?= isset($barangKeluar) ? $barangKeluar['tanggal'] : old('tanggal', date('Y-m-d')) ?>"
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
                                    class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    required>
                                <option value="">Pilih Barang</option>
                                <?php 
                                $hasStock = false;
                                foreach ($barang as $item) : 
                                    if ($item['stok'] > 0) :
                                        $hasStock = true;
                                ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-kode="<?= esc($item['kode']) ?>"
                                            data-satuan="<?= esc($item['satuan']) ?>"
                                            data-stok="<?= $item['stok'] ?>"
                                            <?= (isset($barangKeluar) && $barangKeluar['barang_id'] == $item['id']) || old('barang_id') == $item['id'] ? 'selected' : '' ?>>
                                        <?= esc($item['kode'] . ' - ' . $item['nama'] . ' (Stok: ' . number_format($item['stok']) . ' ' . $item['satuan'] . ')') ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                
                                if (!$hasStock) :
                                ?>
                                    <option value="" disabled>Tidak ada barang dengan stok tersedia</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('barang_id')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('barang_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="jumlah" id="jumlah" 
                                   value="<?= isset($barangKeluar) ? $barangKeluar['jumlah'] : old('jumlah') ?>"
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   min="1" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span id="satuan-addon" class="text-gray-500 sm:text-sm"></span>
                            </div>
                        </div>
                        <p id="stok-info" class="mt-2 text-sm text-gray-500"></p>
                        <?php if (isset($validation) && $validation->hasError('jumlah')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('jumlah') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan</label>
                        <div class="mt-1">
                            <input type="text" name="tujuan" id="tujuan" 
                                   value="<?= isset($barangKeluar) ? esc($barangKeluar['tujuan']) : old('tujuan') ?>"
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('tujuan')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('tujuan') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <div class="mt-1">
                            <textarea id="keterangan" name="keterangan" rows="3" 
                                      class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md"><?= isset($barangKeluar) ? esc($barangKeluar['keterangan']) : old('keterangan') ?></textarea>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('keterangan')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('keterangan') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="reset" 
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500">
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-warehouse-400 hover:bg-warehouse-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500">
                        <?= isset($barangKeluar) ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangSelect = document.getElementById('barang_id');
    const jumlahInput = document.getElementById('jumlah');
    const satuanAddon = document.getElementById('satuan-addon');
    const stokInfo = document.getElementById('stok-info');

    function updateSatuanAndStok() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        satuanAddon.textContent = selectedOption.dataset.satuan || '';

        if (selectedOption.value) {
            const stok = parseInt(selectedOption.dataset.stok);
            stokInfo.textContent = `Stok tersedia: ${stok.toLocaleString()} ${selectedOption.dataset.satuan}`;
            jumlahInput.max = stok;
        } else {
            stokInfo.textContent = '';
            jumlahInput.removeAttribute('max');
        }
    }

    barangSelect.addEventListener('change', updateSatuanAndStok);
    updateSatuanAndStok();

    // Validasi jumlah tidak melebihi stok
    jumlahInput.addEventListener('input', function() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        if (selectedOption.value) {
            const stok = parseInt(selectedOption.dataset.stok);
            if (parseInt(this.value) > stok) {
                this.value = stok;
            }
        }
    });
});
</script>

<?= $this->endSection() ?> 