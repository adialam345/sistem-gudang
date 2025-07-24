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
        $barang = $this->barangModel->find($barangId);
        return $barang ? (int)$barang['stok'] : 0;
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
        $data = [
            'title' => 'Tambah Barang Keluar',
            'barang' => $this->barangModel->findAll(),
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

            // Update stok di tabel barang
            $this->barangModel->syncStok($data['barang_id']);

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

            // Update stok di tabel barang
            $this->barangModel->syncStok($data['barang_id']);
            if ($data['barang_id'] != $oldData['barang_id']) {
                $this->barangModel->syncStok($oldData['barang_id']);
            }

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

            // Update stok di tabel barang
            $this->barangModel->syncStok($barangKeluar['barang_id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus barang keluar');
            }

            return redirect()->to('/barang-keluar')->with('success', 'Barang keluar berhasil dihapus');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/barang-keluar')->with('error', 'Gagal menghapus barang keluar: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $query = $this->barangKeluarModel;

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

        $barangKeluar = $query->findAll();
        
        $html = view('barang_keluar/export_pdf', [
            'barangKeluar' => $barangKeluar,
            'tanggal' => date('d/m/Y'),
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir
        ]);

        ExportHelper::exportToPdf($html, 'barang_keluar_' . date('Ymd'));
    }

    public function exportExcel()
    {
        $search = $this->request->getGet('search');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $query = $this->barangKeluarModel;

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

        $barangKeluar = $query->findAll();
        
        $headers = ['Tanggal', 'No Transaksi', 'Kode Barang', 'Nama Barang', 'Jumlah', 'Satuan', 'Penerima', 'Keterangan'];
        
        $data = array_map(function($item) {
            return [
                date('d/m/Y', strtotime($item['tanggal'])),
                $item['no_transaksi'],
                $item['kode_barang'],
                $item['nama_barang'],
                $item['jumlah'],
                $item['satuan'],
                $item['penerima'],
                $item['keterangan']
            ];
        }, $barangKeluar);

        ExportHelper::exportToExcel($data, $headers, 'barang_keluar_' . date('Ymd'));
    }
} 