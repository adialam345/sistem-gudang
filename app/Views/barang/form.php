<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold text-gray-900"><?= isset($barang) ? 'Edit Barang' : 'Tambah Barang' ?></h1>
        <div class="mt-4 sm:mt-0">
            <a href="<?= site_url('barang') ?>" 
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
            <form action="<?= site_url('barang/' . (isset($barang) ? 'update/' . $barang['id'] : 'store')) ?>" method="post" class="space-y-6">
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
                        <label for="kode" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                        <div class="mt-1">
                            <input type="text" name="kode" id="kode" 
                                   value="<?= isset($barang) ? esc($barang['kode']) : old('kode') ?>"
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('kode')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('kode') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <div class="mt-1">
                            <input type="text" name="nama" id="nama" 
                                   value="<?= isset($barang) ? esc($barang['nama']) : old('nama') ?>"
                                   class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('nama')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('nama') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <div class="mt-1">
                            <select id="kategori_id" name="kategori_id" 
                                    class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= (isset($barang) && $barang['kategori_id'] == $category['id']) || old('kategori_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('kategori_id')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('kategori_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan</label>
                        <div class="mt-1">
                            <select id="satuan" name="satuan" 
                                    class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    required>
                                <option value="">Pilih Satuan</option>
                                <option value="pcs" <?= (isset($barang) && $barang['satuan'] == 'pcs') || old('satuan') == 'pcs' ? 'selected' : '' ?>>Pcs</option>
                                <option value="box" <?= (isset($barang) && $barang['satuan'] == 'box') || old('satuan') == 'box' ? 'selected' : '' ?>>Box</option>
                                <option value="kg" <?= (isset($barang) && $barang['satuan'] == 'kg') || old('satuan') == 'kg' ? 'selected' : '' ?>>Kg</option>
                                <option value="liter" <?= (isset($barang) && $barang['satuan'] == 'liter') || old('satuan') == 'liter' ? 'selected' : '' ?>>Liter</option>
                                <option value="meter" <?= (isset($barang) && $barang['satuan'] == 'meter') || old('satuan') == 'meter' ? 'selected' : '' ?>>Meter</option>
                                <option value="roll" <?= (isset($barang) && $barang['satuan'] == 'roll') || old('satuan') == 'roll' ? 'selected' : '' ?>>Roll</option>
                                <option value="lembar" <?= (isset($barang) && $barang['satuan'] == 'lembar') || old('satuan') == 'lembar' ? 'selected' : '' ?>>Lembar</option>
                                <option value="batang" <?= (isset($barang) && $barang['satuan'] == 'batang') || old('satuan') == 'batang' ? 'selected' : '' ?>>Batang</option>
                                <option value="sak" <?= (isset($barang) && $barang['satuan'] == 'sak') || old('satuan') == 'sak' ? 'selected' : '' ?>>Sak</option>
                                <option value="dus" <?= (isset($barang) && $barang['satuan'] == 'dus') || old('satuan') == 'dus' ? 'selected' : '' ?>>Dus</option>
                            </select>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('satuan')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('satuan') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
                        <div class="mt-1">
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp &nbsp;</span>
                                </div>
                                <input type="number" 
                                       name="harga" 
                                       id="harga" 
                                       min="0"
                                       step="1"
                                       value="<?= isset($barang) && isset($barang['harga']) ? esc($barang['harga']) : (old('harga') ?? '0') ?>"
                                       class="pl-10 focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required
                                       style="padding-left: 3rem !important;">
                            </div>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('harga')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('harga') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <div class="mt-1">
                            <textarea id="deskripsi" name="deskripsi" rows="3" 
                                      class="shadow-sm focus:ring-warehouse-500 focus:border-warehouse-500 block w-full sm:text-sm border-gray-300 rounded-md"><?= isset($barang) ? esc($barang['deskripsi']) : old('deskripsi') ?></textarea>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('deskripsi')) : ?>
                            <p class="mt-2 text-sm text-red-600"><?= $validation->getError('deskripsi') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="reset" 
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warehouse-500">
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <?= isset($barang) ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 