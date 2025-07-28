ALTER TABLE barang ADD COLUMN harga DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER deskripsi;

UPDATE barang SET harga = 75000.00 WHERE kode = 'BRG001';
UPDATE barang SET harga = 85000.00 WHERE kode = 'BRG002';
UPDATE barang SET harga = 450000.00 WHERE kode = 'BRG003';
UPDATE barang SET harga = 25000.00 WHERE kode = 'BRG004';
UPDATE barang SET harga = 185000.00 WHERE kode = 'BRG005'; 

-- Update harga di tabel barang_masuk berdasarkan harga di tabel barang
UPDATE barang_masuk bm
JOIN barang b ON bm.barang_id = b.id
SET bm.harga = b.harga
WHERE bm.harga = 0;

-- Update harga di tabel barang_keluar berdasarkan harga di tabel barang
UPDATE barang_keluar bk
JOIN barang b ON bk.barang_id = b.id
SET bk.harga = b.harga
WHERE bk.harga = 0; 