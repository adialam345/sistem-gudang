<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['kode', 'nama', 'kategori_id', 'satuan', 'deskripsi'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getCategories()
    {
        return [
            ['id' => 1, 'nama' => 'Bahan Baku'],
            ['id' => 2, 'nama' => 'Barang Jadi'],
            ['id' => 3, 'nama' => 'Sparepart'],
            ['id' => 4, 'nama' => 'Peralatan'],
            ['id' => 5, 'nama' => 'Perlengkapan'],
            ['id' => 6, 'nama' => 'Lain-lain']
        ];
    }

    public function getCurrentStock($id)
    {
        $db = \Config\Database::connect();
        $totalMasuk = $db->table('barang_masuk')->where('barang_id', $id)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalKeluar = $db->table('barang_keluar')->where('barang_id', $id)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        return $totalMasuk - $totalKeluar;
    }
} 