<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;

class BarangKeluar extends BaseController
{
    protected $barangModel;
    protected $barangMasukModel;
    protected $barangKeluarModel;
    protected $db;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $query = $this->barangKeluarModel;

        if ($search) {
            $query->groupStart()
                  ->like('kode_barang', $search)
                  ->orLike('nama_barang', $search)
                  ->groupEnd();
        }

        if ($tanggal_awal && $tanggal_akhir) {
            $query->where('tanggal >=', $tanggal_awal)
                  ->where('tanggal <=', $tanggal_akhir);
        }

        $query->orderBy('tanggal', 'DESC')
              ->orderBy('no_transaksi', 'DESC');

        $data = [
            'title' => 'Barang Keluar',
            'barangKeluar' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ];

        return view('barang_keluar/index', $data);
    }

    public function create()
    {
        // Get list of barang with current stock info
        $barangList = $this->barangModel->findAll();
        foreach ($barangList as &$item) {
            // Get current stock
            $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $item['id'])->first();
            $keluar = $this->db->table('barang_keluar')->selectSum('jumlah')->where('barang_id', $item['id'])->get()->getRow();
            $item['stok'] = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
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

        // Hitung stok saat ini
        $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $barang['id'])->first();
        $keluar = $this->db->table('barang_keluar')->selectSum('jumlah')->where('barang_id', $barang['id'])->get()->getRow();
        $stokSaatIni = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);

        $jumlah = $this->request->getPost('jumlah');
        if ($jumlah > $stokSaatIni) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak mencukupi');
        }

        // Generate nomor transaksi: BK/YYYYMMDD/XXXX
        $tanggal = $this->request->getPost('tanggal');
        $lastTransaksi = $this->barangKeluarModel
            ->where('DATE(tanggal)', $tanggal)
            ->orderBy('no_transaksi', 'DESC')
            ->first();

        $counter = 1;
        if ($lastTransaksi) {
            // Extract counter from last transaction number
            $parts = explode('/', $lastTransaksi['no_transaksi']);
            $counter = intval(end($parts)) + 1;
        }

        $noTransaksi = sprintf("BK/%s/%04d", date('Ymd', strtotime($tanggal)), $counter);

        $this->db->transStart();

        try {
            $data = [
                'tanggal' => $tanggal,
                'no_transaksi' => $noTransaksi,
                'barang_id' => $barang['id'],
                'kode_barang' => $barang['kode'],
                'nama_barang' => $barang['nama'],
                'jumlah' => $jumlah,
                'satuan' => $barang['satuan'],
                'tujuan' => $this->request->getPost('tujuan'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->barangKeluarModel->insert($data)) {
                throw new \Exception('Gagal menambahkan barang keluar');
            }

            $this->db->transCommit();
            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil ditambahkan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
            // Get current stock
            $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $item['id'])->first();
            $keluar = $this->db->table('barang_keluar')
                              ->selectSum('jumlah')
                              ->where('barang_id', $item['id'])
                              ->where('id !=', $id) // Exclude current transaction
                              ->get()
                              ->getRow();
            $item['stok'] = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
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
        $barangKeluar = $this->barangKeluarModel->find($id);
        if (!$barangKeluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan');
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

        // Hitung stok saat ini (tidak termasuk transaksi yang sedang diedit)
        $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $barang['id'])->first();
        $keluar = $this->db->table('barang_keluar')
                          ->selectSum('jumlah')
                          ->where('barang_id', $barang['id'])
                          ->where('id !=', $id)
                          ->get()
                          ->getRow();
        $stokSaatIni = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);

        $jumlah = $this->request->getPost('jumlah');
        if ($jumlah > $stokSaatIni) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak mencukupi');
        }

        $this->db->transStart();

        try {
            $data = [
                'tanggal' => $this->request->getPost('tanggal'),
                'barang_id' => $barang['id'],
                'kode_barang' => $barang['kode'],
                'nama_barang' => $barang['nama'],
                'jumlah' => $jumlah,
                'satuan' => $barang['satuan'],
                'tujuan' => $this->request->getPost('tujuan'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (!$this->barangKeluarModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate barang keluar');
            }

            $this->db->transCommit();
            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil diupdate');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
            // Kembalikan stok barang
            $barang = $this->barangModel->find($barangKeluar['barang_id']);
            $this->barangModel->update($barang['id'], [
                'stok' => $barang['stok'] + $barangKeluar['jumlah']
            ]);

            // Hapus barang keluar
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

    private function generateNoTransaksi()
    {
        $tanggal = date('Ymd');
        $lastNo = $this->barangKeluarModel->where('DATE(tanggal)', date('Y-m-d'))
                                         ->orderBy('no_transaksi', 'DESC')
                                         ->first();

        if ($lastNo) {
            $lastNo = substr($lastNo['no_transaksi'], -4);
            $nextNo = str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNo = '0001';
        }

        return 'BK' . $tanggal . $nextNo;
    }
} 