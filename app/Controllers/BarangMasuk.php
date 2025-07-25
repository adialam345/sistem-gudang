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

        $query = $this->barangMasukModel;

        if ($search) {
            $query->groupStart()
                  ->like('no_transaksi', $search)
                  ->orLike('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->groupEnd();
        }

        if ($tanggal_awal && $tanggal_akhir) {
            $query->where('tanggal >=', $tanggal_awal)
                  ->where('tanggal <=', $tanggal_akhir);
        } elseif ($tanggal_awal) {
            $query->where('tanggal >=', $tanggal_awal);
        } elseif ($tanggal_akhir) {
            $query->where('tanggal <=', $tanggal_akhir);
        }

        $data = [
            'title' => 'Barang Masuk',
            'barangMasuk' => $query->orderBy('tanggal', 'DESC')
                                  ->orderBy('no_transaksi', 'DESC')
                                  ->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ];

        return view('barang_masuk/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Barang Masuk',
            'barang' => $this->barangModel->findAll(),
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

            // Update stok di tabel barang
            $this->barangModel->syncStok($barang['id']);

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

        $data = [
            'title' => 'Edit Barang Masuk',
            'barangMasuk' => $barangMasuk,
            'barang' => $this->barangModel->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('barang_masuk/form', $data);
    }

    public function update($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $barang = $this->barangModel->find($this->request->getPost('barang_id'));
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
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

            // Update stok di tabel barang
            $this->barangModel->syncStok($barang['id']);
            if ($barang['id'] != $barangMasuk['barang_id']) {
                $this->barangModel->syncStok($barangMasuk['barang_id']);
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

        $this->db->transStart();

        try {
            // Delete barang masuk record
            if (!$this->barangMasukModel->delete($id)) {
                throw new \Exception('Gagal menghapus barang masuk');
            }

            // Update stok di tabel barang
            $this->barangModel->syncStok($barangMasuk['barang_id']);

            $this->db->transComplete();
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

        $query = $this->barangMasukModel;

        if ($search) {
            $query->groupStart()
                  ->like('no_transaksi', $search)
                  ->orLike('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->groupEnd();
        }

        if ($tanggal_awal && $tanggal_akhir) {
            $query->where('DATE(tanggal) >=', $tanggal_awal)
                  ->where('DATE(tanggal) <=', $tanggal_akhir);
        }

        $barangMasuk = $query->findAll();
        
        $html = view('barang_masuk/export_pdf', [
            'barangMasuk' => $barangMasuk,
            'tanggal' => date('d/m/Y'),
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ]);

        ExportHelper::exportToPdf($html, 'barang_masuk_' . date('Ymd'));
    }

    public function exportExcel()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $query = $this->barangMasukModel;

        if ($search) {
            $query->groupStart()
                  ->like('no_transaksi', $search)
                  ->orLike('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->groupEnd();
        }

        if ($tanggal_awal && $tanggal_akhir) {
            $query->where('DATE(tanggal) >=', $tanggal_awal)
                  ->where('DATE(tanggal) <=', $tanggal_akhir);
        }

        $barangMasuk = $query->findAll();
        
        $headers = ['Tanggal', 'No Transaksi', 'Kode Barang', 'Nama Barang', 'Jumlah', 'Satuan', 'Supplier', 'Keterangan'];
        
        $data = array_map(function($item) {
            return [
                date('d/m/Y', strtotime($item['tanggal'])),
                $item['no_transaksi'],
                $item['kode_barang'],
                $item['nama_barang'],
                $item['jumlah'],
                $item['satuan'],
                $item['supplier'],
                $item['keterangan']
            ];
        }, $barangMasuk);

        ExportHelper::exportToExcel($data, $headers, 'barang_masuk_' . date('Ymd'));
    }
} 