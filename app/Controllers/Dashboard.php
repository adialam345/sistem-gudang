<?php

namespace App\Controllers;

use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;
use App\Models\BarangModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $time_filter = $this->request->getGet('time_filter');
        
        $barangMasukModel = new BarangMasukModel();
        $barangKeluarModel = new BarangKeluarModel();
        $barangModel = new BarangModel();
        
        // Calculate statistics
        $totalSKU = $barangModel->countAllResults();

        // Calculate real stock based on transactions
        $totalMasuk = $barangMasukModel->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalKeluar = $barangKeluarModel->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalStok = $totalMasuk - $totalKeluar;
        
        // Calculate today's transactions
        $today = date('Y-m-d');
        $masukHariIni = $barangMasukModel->where('DATE(tanggal)', $today)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $keluarHariIni = $barangKeluarModel->where('DATE(tanggal)', $today)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        
        // Get filtered activities
        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $startDate = date('Y-m-d', strtotime("-$days days"));
            $endDate = date('Y-m-d');
            
            $barangMasukModel->where('tanggal >=', $startDate)
                            ->where('tanggal <=', $endDate);
            
            $barangKeluarModel->where('tanggal >=', $startDate)
                             ->where('tanggal <=', $endDate);
        }
        
        $aktivitasTerbaru = array_merge(
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'masuk'
                ];
            }, $barangMasukModel->findAll()),
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'keluar'
                ];
            }, $barangKeluarModel->findAll())
        );
        
        usort($aktivitasTerbaru, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });
        
        $data = [
            'title' => 'Dashboard',
            'totalSKU' => $totalSKU,
            'totalStok' => $totalStok,
            'masukHariIni' => $masukHariIni,
            'keluarHariIni' => $keluarHariIni,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'time_filter' => $time_filter,
            'search' => $this->request->getGet('search'),
            'tanggal_awal' => $this->request->getGet('tanggal_awal'),
            'tanggal_akhir' => $this->request->getGet('tanggal_akhir')
        ];
        
        return view('dashboard/index', $data);
    }

    public function exportPdf()
    {
        $time_filter = $this->request->getGet('time_filter');
        
        $barangMasukModel = new BarangMasukModel();
        $barangKeluarModel = new BarangKeluarModel();
        
        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $startDate = date('Y-m-d', strtotime("-$days days"));
            $endDate = date('Y-m-d');
            
            $barangMasukModel->where('tanggal >=', $startDate)
                            ->where('tanggal <=', $endDate);
            
            $barangKeluarModel->where('tanggal >=', $startDate)
                             ->where('tanggal <=', $endDate);
        }
        
        $aktivitas = array_merge(
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'masuk'
                ];
            }, $barangMasukModel->findAll()),
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'keluar'
                ];
            }, $barangKeluarModel->findAll())
        );
        
        usort($aktivitas, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });
        
        $data = [
            'aktivitas' => $aktivitas,
            'tanggal' => date('d/m/Y')
        ];
        
        $html = view('dashboard/export_pdf', $data);
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('Sistem Gudang');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Aktivitas');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        $pdf->writeHTML($html);
        
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan-aktivitas.pdf', 'I');
    }

    public function exportExcel()
    {
        $time_filter = $this->request->getGet('time_filter');
        
        $barangMasukModel = new BarangMasukModel();
        $barangKeluarModel = new BarangKeluarModel();
        
        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $startDate = date('Y-m-d', strtotime("-$days days"));
            $endDate = date('Y-m-d');
            
            $barangMasukModel->where('tanggal >=', $startDate)
                            ->where('tanggal <=', $endDate);
            
            $barangKeluarModel->where('tanggal >=', $startDate)
                             ->where('tanggal <=', $endDate);
        }
        
        $aktivitas = array_merge(
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'masuk'
                ];
            }, $barangMasukModel->findAll()),
            array_map(function($item) {
                return [
                    'tanggal' => $item['tanggal'],
                    'no_transaksi' => $item['no_transaksi'],
                    'kode_barang' => $item['kode_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'tipe' => 'keluar'
                ];
            }, $barangKeluarModel->findAll())
        );
        
        usort($aktivitas, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'No Transaksi');
        $sheet->setCellValue('D1', 'Kode Barang');
        $sheet->setCellValue('E1', 'Nama Barang');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Satuan');
        $sheet->setCellValue('H1', 'Tipe');
        
        // Data
        $row = 2;
        foreach ($aktivitas as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['no_transaksi']);
            $sheet->setCellValue('D' . $row, $item['kode_barang']);
            $sheet->setCellValue('E' . $row, $item['nama_barang']);
            $sheet->setCellValue('F' . $row, $item['jumlah']);
            $sheet->setCellValue('G' . $row, $item['satuan']);
            $sheet->setCellValue('H' . $row, ucfirst($item['tipe']));
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=laporan-aktivitas.xlsx');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
} 