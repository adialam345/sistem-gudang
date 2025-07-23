<?php

namespace App\Controllers;

use App\Helpers\ExportHelper;
use App\Models\BarangModel;
use App\Models\BarangMasukModel;

class BarangMasuk extends BaseController
{
    protected $barangMasukModel;
    protected $barangModel;
    protected $db;

    public function __construct()
    {
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangModel = new BarangModel();
        $this->db = \Config\Database::connect();
    }

    private function generateTransactionNumber()
    {
        $tanggal = date('Y-m-d');
        $prefix = 'BM';
        
        // Get the last transaction number for today
        $lastTransaksi = $this->barangMasukModel
            ->where('DATE(tanggal)', $tanggal)
            ->orderBy('no_transaksi', 'DESC')
            ->first();

        $counter = 1;
        if ($lastTransaksi) {
            // Extract counter from last transaction number (format: BM/YYYYMMDD/XXXX)
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

        $model = new BarangMasukModel();
        
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
            'title' => 'Barang Masuk',
            'barangMasuk' => $model->paginate(10),
            'pager' => $model->pager,
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'time_filter' => $time_filter
        ];

        return view('barang_masuk/index', $data);
    }

    public function create()
    {
        // Get list of barang with current stock info
        $barangList = $this->barangModel->findAll();
        foreach ($barangList as &$barang) {
            // Get current stock
            $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $barang['id'])->first();
            $keluar = $this->db->table('barang_keluar')->selectSum('jumlah')->where('barang_id', $barang['id'])->get()->getRow();
            $barang['stok'] = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
        }

        $data = [
            'title' => 'Tambah Barang Masuk',
            'barang' => $barangList,
            'validation' => \Config\Services::validation()
        ];

        return view('barang_masuk/form', $data);
    }

    public function store()
    {
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
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $barang = $this->barangModel->find($this->request->getPost('barang_id'));
        if (!$barang) {
            return redirect()->back()->withInput()->with('error', 'Barang tidak ditemukan');
        }

        // Generate nomor transaksi: BM/YYYYMMDD/XXXX
        $tanggal = $this->request->getPost('tanggal');
        $lastTransaksi = $this->barangMasukModel
            ->where('DATE(tanggal)', $tanggal)
            ->orderBy('no_transaksi', 'DESC')
            ->first();

        $counter = 1;
        if ($lastTransaksi) {
            // Extract counter from last transaction number
            $parts = explode('/', $lastTransaksi['no_transaksi']);
            $counter = intval(end($parts)) + 1;
        }

        $noTransaksi = sprintf("BM/%s/%04d", date('Ymd', strtotime($tanggal)), $counter);

        $this->db->transStart();

        try {
            // Insert barang masuk
            $data = [
                'tanggal' => $tanggal,
                'no_transaksi' => $noTransaksi,
                'barang_id' => $barang['id'],
                'kode_barang' => $barang['kode'],
                'nama_barang' => $barang['nama'],
                'jumlah' => $this->request->getPost('jumlah'),
                'satuan' => $barang['satuan'],
                'supplier' => $this->request->getPost('supplier'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->barangMasukModel->insert($data)) {
                throw new \Exception('Gagal menambahkan barang masuk');
            }

            $this->db->transCommit();
            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil ditambahkan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan');
        }

        // Get list of barang with current stock info
        $barangList = $this->barangModel->findAll();
        foreach ($barangList as &$barang) {
            // Get current stock
            $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $barang['id'])->first();
            $keluar = $this->db->table('barang_keluar')->selectSum('jumlah')->where('barang_id', $barang['id'])->get()->getRow();
            $barang['stok'] = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
        }

        $data = [
            'title' => 'Edit Barang Masuk',
            'barangMasuk' => $barangMasuk,
            'barang' => $barangList,
            'validation' => \Config\Services::validation()
        ];

        return view('barang_masuk/form', $data);
    }

    public function update($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan');
        }

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
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $barang = $this->barangModel->find($this->request->getPost('barang_id'));
        if (!$barang) {
            return redirect()->back()->withInput()->with('error', 'Barang tidak ditemukan');
        }

        $this->db->transStart();

        try {
            // Update barang masuk
            $data = [
                'tanggal' => $this->request->getPost('tanggal'),
                'barang_id' => $barang['id'],
                'kode_barang' => $barang['kode'],
                'nama_barang' => $barang['nama'],
                'jumlah' => $this->request->getPost('jumlah'),
                'satuan' => $barang['satuan'],
                'supplier' => $this->request->getPost('supplier'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->barangMasukModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate barang masuk');
            }

            $this->db->transCommit();
            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil diupdate');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan');
        }

        // Check if deleting this would make stock negative
        $masuk = $this->barangMasukModel->selectSum('jumlah')
                                       ->where('barang_id', $barangMasuk['barang_id'])
                                       ->where('id !=', $id)
                                       ->first();
        $keluar = $this->db->table('barang_keluar')
                          ->selectSum('jumlah')
                          ->where('barang_id', $barangMasuk['barang_id'])
                          ->get()
                          ->getRow();

        $stokSetelahHapus = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
        if ($stokSetelahHapus < 0) {
            return redirect()->to('/barang-masuk')->with('error', 'Barang masuk tidak dapat dihapus karena akan membuat stok menjadi negatif');
        }

        $this->db->transStart();

        try {
            if (!$this->barangMasukModel->delete($id)) {
                throw new \Exception('Gagal menghapus barang masuk');
            }

            $this->db->transCommit();
            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil dihapus');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/barang-masuk')->with('error', $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $time_filter = $this->request->getGet('time_filter');

        $model = new BarangMasukModel();
        
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
            'barangMasuk' => $model->findAll(),
            'tanggal' => date('d/m/Y')
        ];

        $html = view('barang_masuk/export_pdf', $data);
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('Sistem Gudang');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Barang Masuk');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        $pdf->writeHTML($html);
        
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan-barang-masuk.pdf', 'I');
    }

    public function exportExcel()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $time_filter = $this->request->getGet('time_filter');

        $model = new BarangMasukModel();
        
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

        $barangMasuk = $model->findAll();

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
        $sheet->setCellValue('H1', 'Supplier');
        $sheet->setCellValue('I1', 'Keterangan');
        
        // Data
        $row = 2;
        foreach ($barangMasuk as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['no_transaksi']);
            $sheet->setCellValue('D' . $row, $item['kode_barang']);
            $sheet->setCellValue('E' . $row, $item['nama_barang']);
            $sheet->setCellValue('F' . $row, $item['jumlah']);
            $sheet->setCellValue('G' . $row, $item['satuan']);
            $sheet->setCellValue('H' . $row, $item['supplier']);
            $sheet->setCellValue('I' . $row, $item['keterangan']);
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=laporan-barang-masuk.xlsx');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
} 