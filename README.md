# GNF Music Store

Aplikasi web **toko alat musik** dengan **PHP Native (mysqli)** dan tampilan **Bootstrap 5** (default, sederhana). Dibuat untuk tugas **Pemrograman Web 1**, mengikuti pola pada modul: `config.php` + session, dan router `index.php` dengan `switch ($page)`.

## Fitur

**Pelanggan**
- Beranda, daftar produk, pencarian + filter kategori + urutkan, detail produk.
- Keranjang belanja (session) & checkout.
- Register, login, profil, dan daftar pesanan.
- Mode terang/gelap (tombol di navbar).

**Alur Transaksi**
1. Pelanggan checkout → pesanan dibuat dengan status **"Menunggu Konfirmasi"** (stok belum dipotong).
2. **Admin** memverifikasi pembayaran di menu *Pesanan*:
   - **Konfirmasi** → status jadi **"Lunas/Selesai"**, stok otomatis dikurangi (apa pun metode bayarnya).
   - **Batalkan** → status jadi **"Dibatalkan"**.
3. Setiap pesanan punya **struk/nota** yang bisa di-**Print**, atau diunduh sebagai **PDF, XLSX, dan CSV**.

**Admin**
- Login karyawan, dashboard ringkas.
- CRUD Produk, kelola Pesanan (konfirmasi/batal), daftar Pelanggan, CRUD Karyawan.

## Cara Menjalankan (XAMPP)
1. Letakkan folder ini di `htdocs/PemWeb`.
2. Jalankan **Apache** dan **MySQL** dari XAMPP.
3. Buka phpMyAdmin → buat database `db_toko_musik` → **Import** `database/db_toko_musik.sql`.
4. Buka `http://localhost/PemWeb/`.

> Sudah pernah meng-import versi lama? Cukup import ulang `database/db_toko_musik.sql`, atau jalankan SQL berikut sekali saja:
> ```sql
> ALTER TABLE transaksi ADD COLUMN id_pesanan INT DEFAULT NULL;
> CREATE TABLE pesanan (
>   id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
>   id_user INT NOT NULL, tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
>   metode_bayar VARCHAR(30) NOT NULL, total_bayar INT NOT NULL,
>   status VARCHAR(20) DEFAULT 'menunggu', id_karyawan INT DEFAULT NULL
> );
> ```

## Akun Demo
| Peran | Login | Password |
|-------|-------|----------|
| Pelanggan | `user@gmail.com` | `user123` |
| Admin | `nazif` | `nazif123` |

## Struktur File
```
PemWeb/
├── config.php              # koneksi DB + session
├── index.php               # beranda
├── shop.php product.php    # daftar & detail produk
├── cart.php checkout.php    # keranjang & checkout
├── struk.php export.php     # nota + unduh (PDF/XLSX/CSV)
├── login/register/account/logout.php
├── includes/               # functions, header, footer, product_card, exporters
├── admin/                  # panel admin (index router + pages/)
├── assets/                 # css, js, placeholder.php (gambar SVG), images/
└── database/db_toko_musik.sql
```

## Catatan
- Query memakai *prepared statement*; output di-escape dengan `htmlspecialchars`.
- Gambar produk memakai SVG otomatis (`assets/placeholder.php`) bila file foto belum ada. Untuk memakai foto asli, simpan gambar di `assets/images/` sesuai kolom `gambar_url` (lihat `tools/image_prompts.md`).
- Ekspor PDF/XLSX/CSV dibuat tanpa library eksternal (lihat `includes/exporters.php`).
