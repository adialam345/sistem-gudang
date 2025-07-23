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

        // Base queries
        $barangMasukQuery = $db->table('barang_masuk')
            ->select("'masuk' as tipe, tanggal, no_transaksi, kode_barang, nama_barang, jumlah, satuan");
        
        $barangKeluarQuery = $db->table('barang_keluar')
            ->select("'keluar' as tipe, tanggal, no_transaksi, kode_barang, nama_barang, jumlah, satuan");

        // Apply search filter if provided
        if ($search) {
            $barangMasukQuery->groupStart()
                ->like('kode_barang', $search)
                ->orLike('nama_barang', $search)
                ->orLike('no_transaksi', $search)
                ->groupEnd();

            $barangKeluarQuery->groupStart()
                ->like('kode_barang', $search)
                ->orLike('nama_barang', $search)
                ->orLike('no_transaksi', $search)
                ->groupEnd();
        }

        // Apply date range filter if provided
        if ($tanggal_awal && $tanggal_akhir) {
            $barangMasukQuery->where('tanggal >=', $tanggal_awal)
                ->where('tanggal <=', $tanggal_akhir);

            $barangKeluarQuery->where('tanggal >=', $tanggal_awal)
                ->where('tanggal <=', $tanggal_akhir);
        }

        // Get results
        $barangMasuk = $barangMasukQuery->get()->getResultArray();
        $barangKeluar = $barangKeluarQuery->get()->getResultArray();

        // Combine and sort the results
        $aktivitas = array_merge($barangMasuk, $barangKeluar);
        usort($aktivitas, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        // Get only the latest 10 records
        return array_slice($aktivitas, 0, 10);
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