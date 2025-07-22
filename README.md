# Sistem Manajemen Gudang (CodeIgniter 4)

## Instalasi
1. `composer install`
2. Copy `.env.example` ke `.env`, atur koneksi DB.
3. Jalankan migrasi: `php spark migrate`
4. Jalankan seeder: 
   - `php spark db:seed UserSeeder`
   - `php spark db:seed BarangSeeder`
5. Jalankan server: `php spark serve`
6. Login: user `admin`, password `admin123`

## Fitur
- Login, dashboard, CRUD barang, barang masuk/keluar, laporan, audit, role, dsb.

## Catatan
- Semua akses dashboard & modul harus login.
- Role admin bisa kelola user, staff hanya modul gudang.
