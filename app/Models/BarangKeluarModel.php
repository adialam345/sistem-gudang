<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $table = 'barang_keluar';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tanggal', 'no_transaksi', 'barang_id', 'kode_barang', 'nama_barang', 'jumlah', 'satuan', 'harga', 'tujuan', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'tanggal' => 'required',
        'no_transaksi' => 'required|is_unique[barang_keluar.no_transaksi,id,{id}]',
        'barang_id' => 'required|numeric',
        'kode_barang' => 'required',
        'nama_barang' => 'required',
        'jumlah' => 'required|numeric|greater_than[0]',
        'satuan' => 'required',
        'tujuan' => 'required'
    ];

    protected $validationMessages = [
        'no_transaksi' => [
            'is_unique' => 'Nomor transaksi sudah digunakan'
        ],
        'jumlah' => [
            'greater_than' => 'Jumlah harus lebih dari 0'
        ]
    ];

    protected $skipValidation = false;

    public function insertWithValidation($data)
    {
        try {
            if (!$this->validate($data)) {
                $errors = $this->errors();
                throw new \Exception(implode(', ', $errors));
            }

            return $this->insert($data);
        } catch (\Exception $e) {
            throw new \Exception('Gagal mencatat barang keluar: ' . $e->getMessage());
        }
    }
} 