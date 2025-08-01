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
        $sort_date = $this->request->getGet('sort_date') ?? 'desc';

        $db = \Config\Database::connect();
        $builder = $db->table('barang_masuk bm');
        $builder->select('bm.*, b.nama as nama_barang, b.kode as kode_barang')
                ->join('barang b', 'b.id = bm.barang_id');

        // Filter pencarian
        if ($search) {
            $builder->groupStart()
                    ->like('bm.no_transaksi', $search)
                    ->orLike('b.kode', $search)
                    ->orLike('b.nama', $search)
                    ->orLike('bm.supplier', $search)
                    ->groupEnd();
        }

        // Filter tanggal
        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(bm.tanggal) >=', $tanggal_awal)
                   ->where('DATE(bm.tanggal) <=', $tanggal_akhir);
        }

        // Pengurutan
        $builder->orderBy('bm.tanggal', $sort_date)
                ->orderBy('bm.no_transaksi', $sort_date);

        // Pagination
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;
        $total = $builder->countAllResults(false);
        $items = $builder->get($perPage, ($page - 1) * $perPage)->getResultArray();

        // Create Pager
        $pager = service('pager');
        $pager->setPath('barang-masuk');
        $pager->makeLinks($page, $perPage, $total);

        $data = [
            'title' => 'Barang Masuk',
            'barangMasuk' => $items,
            'pager' => $pager,
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'sort_date' => $sort_date
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
            ],
            'supplier' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus diisi'
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

        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'no_transaksi' => $this->generateTransactionNumber(),
            'barang_id' => $barang['id'],
            'kode_barang' => $barang['kode'],
            'nama_barang' => $barang['nama'],
            'jumlah' => $this->request->getPost('jumlah'),
            'satuan' => $barang['satuan'],
            'harga' => $barang['harga'],
            'supplier' => $this->request->getPost('supplier'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if (!$this->barangMasukModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan barang masuk');
        }

        // Update stok barang
        $this->barangModel->update($barang['id'], [
            'stok' => $barang['stok'] + $data['jumlah']
        ]);

        return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil ditambahkan');
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
            return redirect()->to('/barang-masuk')->with('error', 'Barang masuk tidak ditemukan');
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
            ],
            'supplier' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus diisi'
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

        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'barang_id' => $barang['id'],
            'kode_barang' => $barang['kode'],
            'nama_barang' => $barang['nama'],
            'jumlah' => $this->request->getPost('jumlah'),
            'satuan' => $barang['satuan'],
            'harga' => $barang['harga'],
            'supplier' => $this->request->getPost('supplier'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        // Update stok barang
        $selisihStok = $data['jumlah'] - $barangMasuk['jumlah'];
        $stokBaru = $barang['stok'] + $selisihStok;

        if (!$this->barangMasukModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate barang masuk');
        }

        $this->barangModel->update($barang['id'], ['stok' => $stokBaru]);

        return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil diupdate');
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