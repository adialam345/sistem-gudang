<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;

class Dashboard extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $barangModel = new BarangModel();
        $barangMasukModel = new BarangMasukModel();
        $barangKeluarModel = new BarangKeluarModel();

        // Total SKU (jumlah jenis barang)
        $totalSKU = $barangModel->countAll();

        // Total stok (jumlah seluruh barang)
        $totalMasuk = $barangMasukModel->selectSum('jumlah')->first()['jumlah'] ?? 0;
        $totalKeluar = $barangKeluarModel->selectSum('jumlah')->first()['jumlah'] ?? 0;
        $totalStok = $totalMasuk - $totalKeluar;

        // Transaksi hari ini
        $today = date('Y-m-d');
        $masukHariIni = $barangMasukModel->where('tanggal', $today)
                                        ->selectSum('jumlah')
                                        ->first()['jumlah'] ?? 0;
        $keluarHariIni = $barangKeluarModel->where('tanggal', $today)
                                          ->selectSum('jumlah')
                                          ->first()['jumlah'] ?? 0;

        // Aktivitas terbaru (gabungan barang masuk dan keluar)
        $aktivitasMasuk = $barangMasukModel->select('tanggal, no_transaksi, barang_id, kode_barang, nama_barang, jumlah, satuan, "masuk" as tipe')
                                          ->orderBy('tanggal', 'DESC')
                                          ->limit(5)
                                          ->find();

        $aktivitasKeluar = $barangKeluarModel->select('tanggal, no_transaksi, barang_id, kode_barang, nama_barang, jumlah, satuan, "keluar" as tipe')
                                            ->orderBy('tanggal', 'DESC')
                                            ->limit(5)
                                            ->find();

        // Gabungkan dan urutkan aktivitas
        $aktivitasTerbaru = array_merge($aktivitasMasuk, $aktivitasKeluar);
        usort($aktivitasTerbaru, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });
        $aktivitasTerbaru = array_slice($aktivitasTerbaru, 0, 5);

        $data = [
            'title' => 'Dashboard',
            'totalSKU' => $totalSKU,
            'totalStok' => $totalStok,
            'masukHariIni' => $masukHariIni,
            'keluarHariIni' => $keluarHariIni,
            'aktivitasTerbaru' => $aktivitasTerbaru
        ];

        return view('dashboard/index', $data);
    }
} 