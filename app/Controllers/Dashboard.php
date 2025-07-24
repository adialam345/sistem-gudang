<?php

namespace App\Controllers;

use App\Helpers\ExportHelper;

class Dashboard extends BaseController
{
    public function index()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $barangModel = new \App\Models\BarangModel();
        $barangMasukModel = new \App\Models\BarangMasukModel();
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Calculate total stock from transactions
        $totalMasuk = $barangMasukModel->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalKeluar = $barangKeluarModel->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalStok = $totalMasuk - $totalKeluar;

        $data = [
            'totalSKU' => $barangModel->countAllResults(),
            'totalStok' => $totalStok,
            'masukHariIni' => $barangMasukModel->where('DATE(tanggal)', date('Y-m-d'))->selectSum('jumlah')->get()->getRow()->jumlah ?? 0,
            'keluarHariIni' => $barangKeluarModel->where('DATE(tanggal)', date('Y-m-d'))->selectSum('jumlah')->get()->getRow()->jumlah ?? 0,
            'aktivitasTerbaru' => $this->getAktivitasTerbaru($search, $tanggal_awal, $tanggal_akhir),
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ];

        return view('dashboard/index', $data);
    }

    private function getAktivitasTerbaru($search = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $db = \Config\Database::connect();
        $tipe = $this->request->getGet('tipe');

        // Gabungkan query barang masuk dan keluar dengan UNION
        $sql = "";
        
        // Jika tipe tidak diset atau 'masuk', tambahkan query barang masuk
        if (!$tipe || $tipe === 'masuk') {
            $sql .= "SELECT 'masuk' as tipe, tanggal, no_transaksi, kode_barang, nama_barang, jumlah, satuan FROM barang_masuk ";
            
            if ($search) {
                $sql .= "WHERE (kode_barang LIKE '%{$search}%' OR nama_barang LIKE '%{$search}%' OR no_transaksi LIKE '%{$search}%') ";
            }
            if ($tanggal_awal && $tanggal_akhir) {
                $sql .= ($search ? "AND" : "WHERE") . " (tanggal >= '{$tanggal_awal}' AND tanggal <= '{$tanggal_akhir}') ";
            }
        }

        // Jika tipe tidak diset atau 'keluar', tambahkan query barang keluar
        if (!$tipe || $tipe === 'keluar') {
            if ($sql) {
                $sql .= "UNION ALL ";
            }
            $sql .= "SELECT 'keluar' as tipe, tanggal, no_transaksi, kode_barang, nama_barang, jumlah, satuan FROM barang_keluar ";

            if ($search) {
                $sql .= "WHERE (kode_barang LIKE '%{$search}%' OR nama_barang LIKE '%{$search}%' OR no_transaksi LIKE '%{$search}%') ";
            }
            if ($tanggal_awal && $tanggal_akhir) {
                $sql .= ($search ? "AND" : "WHERE") . " (tanggal >= '{$tanggal_awal}' AND tanggal <= '{$tanggal_akhir}') ";
            }
        }

        // Urutkan berdasarkan tanggal terbaru
        $sql .= "ORDER BY tanggal DESC, no_transaksi DESC LIMIT 10";

        return $db->query($sql)->getResultArray();
    }

    public function exportAktivitasPdf()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $aktivitas = $this->getAktivitasTerbaru($search, $tanggal_awal, $tanggal_akhir);
        
        $html = view('dashboard/export_pdf', [
            'aktivitas' => $aktivitas,
            'tanggal' => date('d/m/Y'),
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ]);

        ExportHelper::exportToPdf($html, 'aktivitas_terbaru_' . date('Ymd'));
    }

    public function exportAktivitasExcel()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $aktivitas = $this->getAktivitasTerbaru($search, $tanggal_awal, $tanggal_akhir);
        
        $headers = ['Tanggal', 'No Transaksi', 'Kode Barang', 'Nama Barang', 'Jumlah', 'Satuan', 'Tipe'];
        
        $data = array_map(function($item) {
            return [
                date('d/m/Y', strtotime($item['tanggal'])),
                $item['no_transaksi'],
                $item['kode_barang'],
                $item['nama_barang'],
                $item['jumlah'],
                $item['satuan'],
                ucfirst($item['tipe'])
            ];
        }, $aktivitas);

        ExportHelper::exportToExcel($data, $headers, 'aktivitas_terbaru_' . date('Ymd'));
    }
} 