<?php

namespace App\Controllers;

use App\Helpers\ExportHelper;
use App\Models\BarangModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;

class BarangKeluar extends BaseController
{
    protected $barangKeluarModel;
    protected $barangModel;
    protected $barangMasukModel;
    protected $db;

    public function __construct()
    {
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->barangModel = new BarangModel();
        $this->barangMasukModel = new BarangMasukModel();
        $this->db = \Config\Database::connect();
    }

    private function getCurrentStock($barangId)
    {
        $totalMasuk = $this->barangMasukModel->where('barang_id', $barangId)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalKeluar = $this->barangKeluarModel->where('barang_id', $barangId)->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        return $totalMasuk - $totalKeluar;
    }

    private function generateTransactionNumber()
    {
        $tanggal = date('Y-m-d');
        $prefix = 'BK';
        
        // Get the last transaction number for today
        $lastTransaksi = $this->barangKeluarModel
            ->where('DATE(tanggal)', $tanggal)
            ->orderBy('no_transaksi', 'DESC')
            ->first();

        $counter = 1;
        if ($lastTransaksi) {
            // Extract counter from last transaction number (format: BK/YYYYMMDD/XXXX)
            $parts = explode('/', $lastTransaksi['no_transaksi']);
            if (count($parts) === 3) {
                $counter = (int)$parts[2] + 1;
            }
        }

        // Generate new transaction number
        return sprintf("%s/%s/%04d", $prefix, date('Ymd'), $counter);
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $time_filter = $this->request->getGet('time_filter');

        $model = new BarangKeluarModel();
        
        if ($search) {
            $model->groupStart()
                  ->like('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->orLike('no_transaksi', $search)
                  ->groupEnd();
        }

        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $model->where('tanggal >=', date('Y-m-d', strtotime("-$days days")))
                  ->where('tanggal <=', date('Y-m-d'));
        } else if ($tanggal_awal && $tanggal_akhir) {
            $model->where('tanggal >=', $tanggal_awal)
                  ->where('tanggal <=', $tanggal_akhir);
        }

        $data = [
            'title' => 'Barang Keluar',
            'barangKeluar' => $model->paginate(10),
            'pager' => $model->pager,
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'time_filter' => $time_filter
        ];

        return view('barang_keluar/index', $data);
    }

    public function create()
    {
        // Get list of barang with current stock info
        $barangList = $this->barangModel->findAll();
        foreach ($barangList as &$item) {
            $item['stok'] = $this->getCurrentStock($item['id']);
        }

        $data = [
            'title' => 'Tambah Barang Keluar',
            'barang' => $barangList,
            'validation' => \Config\Services::validation()
        ];

        return view('barang_keluar/form', $data);
    }

    public function store()
    {
        // Validate required fields
        $rules = [
            'tanggal' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal harus diisi'
                ]
            ],
            'barang_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Barang harus dipilih'
                ]
            ],
            'jumlah' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah harus diisi',
                    'numeric' => 'Jumlah harus berupa angka',
                    'greater_than' => 'Jumlah harus lebih dari 0'
                ]
            ],
            'tujuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tujuan harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator)
                           ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

            $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'no_transaksi' => $this->generateTransactionNumber(),
            'barang_id' => $this->request->getPost('barang_id'),
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah' => $this->request->getPost('jumlah'),
            'satuan' => $this->request->getPost('satuan'),
                'tujuan' => $this->request->getPost('tujuan'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check if barang exists
            $barang = $this->barangModel->find($data['barang_id']);
            if (!$barang) {
                throw new \Exception('Barang tidak ditemukan');
            }

            // Check if stock is sufficient
            $currentStock = $this->getCurrentStock($data['barang_id']);
            if ($currentStock < $data['jumlah']) {
                throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $currentStock);
            }

            // Insert barang keluar record
            if (!$this->barangKeluarModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data: ' . implode(', ', $this->barangKeluarModel->errors()));
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil ditambahkan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambah barang keluar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $barangKeluar = $this->barangKeluarModel->find($id);
        if (!$barangKeluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan');
        }

        // Get list of barang with current stock info
        $barangList = $this->barangModel->findAll();
        foreach ($barangList as &$item) {
            // Add back current transaction amount to get available stock
            $item['stok'] = $this->getCurrentStock($item['id']);
            if ($item['id'] == $barangKeluar['barang_id']) {
                $item['stok'] += $barangKeluar['jumlah'];
            }
        }

        $data = [
            'title' => 'Edit Barang Keluar',
            'barangKeluar' => $barangKeluar,
            'barang' => $barangList,
            'validation' => \Config\Services::validation()
        ];

        return view('barang_keluar/form', $data);
    }

    public function update($id)
    {
        $oldData = $this->barangKeluarModel->find($id);
        if (!$oldData) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

            $data = [
                'tanggal' => $this->request->getPost('tanggal'),
            'barang_id' => $this->request->getPost('barang_id'),
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah' => $this->request->getPost('jumlah'),
            'satuan' => $this->request->getPost('satuan'),
                'tujuan' => $this->request->getPost('tujuan'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check if stock is sufficient
            $currentStock = $this->getCurrentStock($data['barang_id']);
            if ($data['barang_id'] == $oldData['barang_id']) {
                $currentStock += $oldData['jumlah']; // Add back the old amount
            }
            
            if ($currentStock < $data['jumlah']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $currentStock);
            }

            // Update barang keluar record
            $this->barangKeluarModel->update($id, $data);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal mengupdate barang keluar');
            }

            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil diupdate');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal mengupdate barang keluar: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $barangKeluar = $this->barangKeluarModel->find($id);
        if (!$barangKeluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan');
        }

        $this->db->transStart();

        try {
            // Delete barang keluar record
            $this->barangKeluarModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus barang keluar');
            }

            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil dihapus');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $time_filter = $this->request->getGet('time_filter');

        $model = new BarangKeluarModel();
        
        if ($search) {
            $model->groupStart()
                  ->like('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->orLike('no_transaksi', $search)
                  ->groupEnd();
        }

        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $model->where('tanggal >=', date('Y-m-d', strtotime("-$days days")))
                  ->where('tanggal <=', date('Y-m-d'));
        } else if ($tanggal_awal && $tanggal_akhir) {
            $model->where('tanggal >=', $tanggal_awal)
                  ->where('tanggal <=', $tanggal_akhir);
        }

        $data = [
            'barangKeluar' => $model->findAll(),
            'tanggal' => date('d/m/Y')
        ];

        $html = view('barang_keluar/export_pdf', $data);
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('Sistem Gudang');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Barang Keluar');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        $pdf->writeHTML($html);
        
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan-barang-keluar.pdf', 'I');
    }

    public function exportExcel()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $time_filter = $this->request->getGet('time_filter');

        $model = new BarangKeluarModel();
        
        if ($search) {
            $model->groupStart()
                  ->like('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->orLike('no_transaksi', $search)
                  ->groupEnd();
        }

        if ($time_filter && $time_filter !== 'all') {
            $days = (int)$time_filter;
            $model->where('tanggal >=', date('Y-m-d', strtotime("-$days days")))
                  ->where('tanggal <=', date('Y-m-d'));
        } else if ($tanggal_awal && $tanggal_akhir) {
            $model->where('tanggal >=', $tanggal_awal)
                  ->where('tanggal <=', $tanggal_akhir);
        }

        $barangKeluar = $model->findAll();

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
        $sheet->setCellValue('H1', 'Tujuan');
        $sheet->setCellValue('I1', 'Keterangan');
        
        // Data
        $row = 2;
        foreach ($barangKeluar as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['no_transaksi']);
            $sheet->setCellValue('D' . $row, $item['kode_barang']);
            $sheet->setCellValue('E' . $row, $item['nama_barang']);
            $sheet->setCellValue('F' . $row, $item['jumlah']);
            $sheet->setCellValue('G' . $row, $item['satuan']);
            $sheet->setCellValue('H' . $row, $item['tujuan']);
            $sheet->setCellValue('I' . $row, $item['keterangan']);
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=laporan-barang-keluar.xlsx');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
} 