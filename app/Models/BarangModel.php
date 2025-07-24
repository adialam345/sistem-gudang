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
    protected $allowedFields = ['kode', 'nama', 'kategori_id', 'satuan', 'deskripsi', 'stok'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getCategories()
    {
        return $this->db->table('kategori')->get()->getResultArray();
    }

    public function hitungStokDariTransaksi($id)
    {
        // Hitung total barang masuk
        $masuk = $this->db->table('barang_masuk')
                         ->selectSum('jumlah')
                         ->where('barang_id', $id)
                         ->get()
                         ->getRow()
                         ->jumlah ?? 0;

        // Hitung total barang keluar
        $keluar = $this->db->table('barang_keluar')
                          ->selectSum('jumlah')
                          ->where('barang_id', $id)
                          ->get()
                          ->getRow()
                          ->jumlah ?? 0;

        return (int)$masuk - (int)$keluar;
    }

    public function syncStok($id)
    {
        $stok = $this->hitungStokDariTransaksi($id);
        return $this->update($id, ['stok' => $stok]);
    }

    // Override find method untuk auto sync
    public function find($id = null)
    {
        $barang = parent::find($id);
        if ($barang) {
            $stokTransaksi = $this->hitungStokDariTransaksi($barang['id']);
            if ((int)$barang['stok'] !== $stokTransaksi) {
                $this->syncStok($barang['id']);
                $barang['stok'] = $stokTransaksi;
            }
        }
        return $barang;
    }

    // Override findAll method untuk auto sync
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $barangList = parent::findAll($limit, $offset);
        foreach ($barangList as &$barang) {
            $stokTransaksi = $this->hitungStokDariTransaksi($barang['id']);
            if ((int)$barang['stok'] !== $stokTransaksi) {
                $this->syncStok($barang['id']);
                $barang['stok'] = $stokTransaksi;
            }
        }
        return $barangList;
    }
} 