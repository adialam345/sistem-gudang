<?php

namespace App\Controllers;

use App\Helpers\ExportHelper;
use App\Models\BarangModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;

class Barang extends BaseController
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

    public function addPriceColumn()
    {
        try {
            // Check if column exists
            $fields = $this->db->getFieldData('barang');
            $hargaExists = false;
            foreach ($fields as $field) {
                if ($field->name === 'harga') {
                    $hargaExists = true;
                    break;
                }
            }

            if (!$hargaExists) {
                // Add column if it doesn't exist
                $sql = "ALTER TABLE barang ADD COLUMN harga DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER deskripsi";
                $this->db->query($sql);
                
                // Update existing prices
                $updates = [
                    ['kode' => 'BRG001', 'harga' => 75000.00],
                    ['kode' => 'BRG002', 'harga' => 85000.00],
                    ['kode' => 'BRG003', 'harga' => 450000.00],
                    ['kode' => 'BRG004', 'harga' => 25000.00],
                    ['kode' => 'BRG005', 'harga' => 185000.00]
                ];

                foreach ($updates as $update) {
                    $this->db->table('barang')
                            ->where('kode', $update['kode'])
                            ->update(['harga' => $update['harga']]);
                }
                
                return redirect()->to('/barang')->with('success', 'Kolom harga berhasil ditambahkan');
            }
            
            return redirect()->to('/barang')->with('info', 'Kolom harga sudah ada');
        } catch (\Exception $e) {
            return redirect()->to('/barang')->with('error', 'Gagal menambahkan kolom harga: ' . $e->getMessage());
        }
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
                case 'stok_asc':
                    $query->orderBy('stok', 'ASC');
                    break;
                case 'stok_desc':
                    $query->orderBy('stok', 'DESC');
                    break;
                default:
                    $query->orderBy('nama', 'ASC');
            }
        } else {
            $query->orderBy('nama', 'ASC');
        }

        $categories = $this->barangModel->getCategories();
        $barang = $query->paginate(10);

        // Get category names and stock info
        foreach ($barang as &$item) {
            // Get category name
            foreach ($categories as $cat) {
                if ($cat['id'] == $item['kategori_id']) {
                    $item['kategori'] = $cat['nama'];
                    break;
                }
            }
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
            ],
            'harga' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga harus diisi',
                    'numeric' => 'Harga harus berupa angka',
                    'greater_than_equal_to' => 'Harga tidak boleh negatif'
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
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga' => $this->request->getPost('harga')
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

        // Set default harga if not exists
        if (!isset($barang['harga'])) {
            $barang['harga'] = '0';
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
            ],
            'harga' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Harga harus diisi',
                    'numeric' => 'Harga harus berupa angka',
                    'greater_than_equal_to' => 'Harga tidak boleh negatif'
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
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga' => $this->request->getPost('harga')
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

    public function updateStokLangsung()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $barangId = $this->request->getPost('barang_id');
        $stokBaru = (int)$this->request->getPost('stok');
        $stokLama = (int)$this->request->getPost('old_stok');

        if (!$barangId || !is_numeric($stokBaru) || $stokBaru < 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid']);
        }

        $barang = $this->barangModel->find($barangId);
        if (!$barang) {
            return $this->response->setJSON(['success' => false, 'message' => 'Barang tidak ditemukan']);
        }

        $this->db->transStart();

        try {
            // Hitung selisih stok
            $selisih = $stokBaru - $stokLama;

            if ($selisih != 0) {
                $tanggal = date('Y-m-d');
                
                if ($selisih > 0) {
                    // Stok bertambah -> catat di barang masuk
                    $noTransaksi = sprintf("BM/%s/ADJ%03d", date('Ymd'), rand(1, 999));
                    $data = [
                        'tanggal' => $tanggal,
                        'no_transaksi' => $noTransaksi,
                        'barang_id' => $barang['id'],
                        'kode_barang' => $barang['kode'],
                        'nama_barang' => $barang['nama'],
                        'jumlah' => abs($selisih),
                        'satuan' => $barang['satuan'],
                        'supplier' => 'Penyesuaian stok',
                        'keterangan' => 'Penyesuaian stok manual'
                    ];
                    
                    if (!$this->barangMasukModel->insert($data)) {
                        throw new \Exception('Gagal mencatat barang masuk');
                    }
                } else {
                    // Stok berkurang -> catat di barang keluar
                    $noTransaksi = sprintf("BK/%s/ADJ%03d", date('Ymd'), rand(1, 999));
                    $data = [
                        'tanggal' => $tanggal,
                        'no_transaksi' => $noTransaksi,
                        'barang_id' => $barang['id'],
                        'kode_barang' => $barang['kode'],
                        'nama_barang' => $barang['nama'],
                        'jumlah' => abs($selisih),
                        'satuan' => $barang['satuan'],
                        'tujuan' => 'Penyesuaian stok',
                        'keterangan' => 'Penyesuaian stok manual'
                    ];
                    
                    if (!$this->barangKeluarModel->insert($data)) {
                        throw new \Exception('Gagal mencatat barang keluar');
                    }
                }
            }

            // Update stok di tabel barang
            $this->barangModel->update($barangId, ['stok' => $stokBaru]);
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Stok berhasil diupdate',
                'stok' => number_format($stokBaru)
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating stock: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal mengupdate stok: ' . $e->getMessage()
            ]);
        }
    }

    public function syncStok($id = null)
    {
        try {
            if ($id) {
                // Sync satu barang
                $barang = $this->barangModel->find($id); // Ini akan otomatis sync
                if (!$barang) {
                    throw new \Exception('Barang tidak ditemukan');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Stok berhasil disinkronkan',
                    'stok' => number_format($barang['stok'])
                ]);
            } else {
                // Sync semua barang
                $this->barangModel->findAll(); // Ini akan otomatis sync semua
                return redirect()->back()->with('success', 'Semua stok berhasil disinkronkan');
            }
        } catch (\Exception $e) {
            if ($id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyinkronkan stok: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()->with('error', 'Gagal menyinkronkan stok: ' . $e->getMessage());
            }
        }
    }
} 