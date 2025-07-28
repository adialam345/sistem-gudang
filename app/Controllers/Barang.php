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
        $selectedCategory = $this->request->getGet('kategori');
        $sort = $this->request->getGet('sort');

        $db = \Config\Database::connect();
        $builder = $db->table('barang b');
        $builder->select('b.*, COALESCE(k.nama, "-") as kategori')
                ->join('kategori k', 'k.id = b.kategori_id', 'left');

        // Filter pencarian
        if ($search) {
            $builder->groupStart()
                    ->like('b.kode', $search)
                    ->orLike('b.nama', $search)
                    ->orLike('b.deskripsi', $search)
                    ->orLike('k.nama', $search)
                    ->groupEnd();
        }

        // Filter kategori
        if ($selectedCategory) {
            $builder->where('b.kategori_id', $selectedCategory);
        }

        // Pengurutan
        if ($sort) {
            switch ($sort) {
                case 'nama_asc':
                    $builder->orderBy('b.nama', 'ASC');
                    break;
                case 'nama_desc':
                    $builder->orderBy('b.nama', 'DESC');
                    break;
                case 'stok_asc':
                    $builder->orderBy('b.stok', 'ASC');
                    break;
                case 'stok_desc':
                    $builder->orderBy('b.stok', 'DESC');
                    break;
                default:
                    $builder->orderBy('b.nama', 'ASC');
            }
        } else {
            $builder->orderBy('b.nama', 'ASC');
        }

        // Pagination
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;
        $total = $builder->countAllResults(false);
        $items = $builder->get($perPage, ($page - 1) * $perPage)->getResultArray();

        // Create Pager
        $pager = service('pager');
        $pager->setPath('barang');
        $pager->makeLinks($page, $perPage, $total);

        $data = [
            'title' => 'Data Barang',
            'barang' => $items,
            'pager' => $pager,
            'search' => $search,
            'selectedCategory' => $selectedCategory,
            'sort' => $sort,
            'categories' => $this->db->table('kategori')->get()->getResultArray()
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
                        'harga' => $barang['harga'],
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
                        'harga' => $barang['harga'],
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

    public function adjustStock()
    {
        $rules = [
            'barang_id' => 'required|numeric',
            'adjustment_type' => 'required|in_list[add,subtract]',
            'adjustment_amount' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                           ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        $barangId = $this->request->getPost('barang_id');
        $adjustmentType = $this->request->getPost('adjustment_type');
        $adjustmentAmount = (int)$this->request->getPost('adjustment_amount');
        $keterangan = $this->request->getPost('keterangan');

        $barang = $this->barangModel->find($barangId);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
        }

        $this->db->transStart();

        try {
            $tanggal = date('Y-m-d');
            $stokLama = $barang['stok'];
            
            if ($adjustmentType === 'add') {
                // Stok bertambah -> catat di barang masuk
                $noTransaksi = sprintf("BM/%s/ADJ%03d", date('Ymd'), rand(1, 999));
                $data = [
                    'tanggal' => $tanggal,
                    'no_transaksi' => $noTransaksi,
                    'barang_id' => $barang['id'],
                    'kode_barang' => $barang['kode'],
                    'nama_barang' => $barang['nama'],
                    'jumlah' => $adjustmentAmount,
                    'satuan' => $barang['satuan'],
                    'harga' => $barang['harga'],
                    'supplier' => 'Penyesuaian Manual',
                    'keterangan' => $keterangan
                ];
                
                if (!$this->barangMasukModel->insert($data)) {
                    throw new \Exception('Gagal mencatat barang masuk');
                }

                $stokBaru = $stokLama + $adjustmentAmount;
            } else {
                // Stok berkurang -> catat di barang keluar
                if ($adjustmentAmount > $stokLama) {
                    return redirect()->back()->with('error', 'Jumlah pengurangan melebihi stok yang tersedia');
                }

                $noTransaksi = sprintf("BK/%s/ADJ%03d", date('Ymd'), rand(1, 999));
                $data = [
                    'tanggal' => $tanggal,
                    'no_transaksi' => $noTransaksi,
                    'barang_id' => $barang['id'],
                    'kode_barang' => $barang['kode'],
                    'nama_barang' => $barang['nama'],
                    'jumlah' => $adjustmentAmount,
                    'satuan' => $barang['satuan'],
                    'harga' => $barang['harga'],
                    'tujuan' => 'Penyesuaian Manual',
                    'keterangan' => $keterangan
                ];
                
                if (!$this->barangKeluarModel->insert($data)) {
                    throw new \Exception('Gagal mencatat barang keluar');
                }

                $stokBaru = $stokLama - $adjustmentAmount;
            }

            // Update stok di tabel barang
            if (!$this->barangModel->update($barangId, ['stok' => $stokBaru])) {
                throw new \Exception('Gagal mengupdate stok barang');
            }
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->back()->with('success', 'Stok berhasil disesuaikan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error adjusting stock: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }
} 