<?php

namespace App\Controllers;

use App\Helpers\ExportHelper;
use App\Models\BarangModel;
use App\Models\BarangMasukModel;

class Barang extends BaseController
{
    protected $barangModel;
    protected $barangMasukModel;
    protected $db;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->barangMasukModel = new BarangMasukModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $kategori = $this->request->getGet('kategori');
        $sort = $this->request->getGet('sort');

        $query = $this->barangModel;

        if ($search) {
            $query->groupStart()
                  ->like('kode', $search)
                  ->orLike('nama', $search)
                  ->groupEnd();
        }

        if ($kategori) {
            $query->where('kategori_id', $kategori);
        }

        if ($sort) {
            switch ($sort) {
                case 'nama_asc':
                    $query->orderBy('nama', 'ASC');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama', 'DESC');
                    break;
                default:
                    $query->orderBy('kode', 'ASC');
            }
        } else {
            $query->orderBy('kode', 'ASC');
        }

        $barang = $query->paginate(10);
        $categories = $this->barangModel->getCategories();

        // Add category names and stock info to barang data
        foreach ($barang as &$item) {
            // Get category name
            foreach ($categories as $cat) {
                if ($cat['id'] == $item['kategori_id']) {
                    $item['kategori'] = $cat['nama'];
                    break;
                }
            }

            // Get current stock
            $masuk = $this->barangMasukModel->selectSum('jumlah')->where('barang_id', $item['id'])->first();
            $keluar = $this->db->table('barang_keluar')->selectSum('jumlah')->where('barang_id', $item['id'])->get()->getRow();
            $item['stok'] = ($masuk['jumlah'] ?? 0) - ($keluar->jumlah ?? 0);
        }

        $data = [
            'title' => 'Daftar Barang',
            'barang' => $barang,
            'pager' => $query->pager,
            'search' => $search,
            'selectedCategory' => $kategori,
            'sort' => $sort,
            'categories' => $categories
        ];

        return view('barang/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Barang',
            'categories' => $this->barangModel->getCategories(),
            'validation' => \Config\Services::validation()
        ];

        return view('barang/form', $data);
    }

    public function store()
    {
        $rules = [
            'kode' => [
                'rules' => 'required|is_unique[barang.kode]',
                'errors' => [
                    'required' => 'Kode barang harus diisi',
                    'is_unique' => 'Kode barang sudah digunakan'
                ]
            ],
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama barang harus diisi'
                ]
            ],
            'kategori_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus dipilih'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'satuan' => $this->request->getPost('satuan'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        if (!$this->barangModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan barang');
        }

        return redirect()->to('/barang')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Barang',
            'barang' => $barang,
            'categories' => $this->barangModel->getCategories(),
            'validation' => \Config\Services::validation()
        ];

        return view('barang/form', $data);
    }

    public function update($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan');
        }

        $rules = [
            'kode' => [
                'rules' => 'required|is_unique[barang.kode,id,' . $id . ']',
                'errors' => [
                    'required' => 'Kode barang harus diisi',
                    'is_unique' => 'Kode barang sudah digunakan'
                ]
            ],
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama barang harus diisi'
                ]
            ],
            'kategori_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus dipilih'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'satuan' => $this->request->getPost('satuan'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        if (!$this->barangModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate barang');
        }

        return redirect()->to('/barang')->with('success', 'Barang berhasil diupdate');
    }

    public function delete($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan');
        }

        // Check if barang has transactions
        $hasTransactions = $this->barangMasukModel->where('barang_id', $id)->countAllResults() > 0 ||
                          $this->db->table('barang_keluar')->where('barang_id', $id)->countAllResults() > 0;

        if ($hasTransactions) {
            return redirect()->to('/barang')->with('error', 'Barang tidak dapat dihapus karena memiliki transaksi');
        }

        if (!$this->barangModel->delete($id)) {
            return redirect()->to('/barang')->with('error', 'Gagal menghapus barang');
        }

        return redirect()->to('/barang')->with('success', 'Barang berhasil dihapus');
    }

    public function exportPdf()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('barang b');
        $builder->select('b.*, COALESCE(k.nama, "-") as kategori')
                ->join('kategori k', 'k.id = b.kategori_id', 'left');
        $barang = $builder->get()->getResultArray();
        
        $html = view('barang/export_pdf', [
            'barang' => $barang,
            'tanggal' => date('d/m/Y')
        ]);

        ExportHelper::exportToPdf($html, 'daftar_barang_' . date('Ymd'));
    }

    public function exportExcel()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('barang b');
        $builder->select('b.*, COALESCE(k.nama, "-") as kategori')
                ->join('kategori k', 'k.id = b.kategori_id', 'left');
        $barang = $builder->get()->getResultArray();
        
        $headers = ['Kode', 'Nama', 'Kategori', 'Satuan', 'Stok', 'Deskripsi'];
        
        $data = array_map(function($item) {
            return [
                $item['kode'],
                $item['nama'],
                $item['kategori'],
                $item['satuan'],
                $item['stok'],
                $item['deskripsi']
            ];
        }, $barang);

        ExportHelper::exportToExcel($data, $headers, 'daftar_barang_' . date('Ymd'));
    }
} 