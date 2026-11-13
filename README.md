# Aplikasi Kasir & Antrean Cuci Mobil & Cuci Helm

Aplikasi web kasir dan pemantauan antrean pencucian kendaraan (mobil) dan helm secara terintegrasi. Dibangun menggunakan framework **CodeIgniter 4**, database **MySQL/MariaDB**, dan antarmuka responsif **Bootstrap 5**.

Aplikasi ini dirancang untuk mempermudah operasional kasir dalam mencatat transaksi, menghitung biaya otomatis, memantau alur status pengerjaan, mencetak struk/nota kasir (thermal 58/80 mm & A5), mengelola master data, serta menyediakan laporan pendapatan dan jejak audit (*audit log*).

---

## Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Kebutuhan Sistem (Prerequisites)](#kebutuhan-sistem-prerequisites)
- [Panduan Instalasi & Menjalankan Aplikasi](#panduan-instalasi--menjalankan-aplikasi)
- [Akun Pengguna Bawaan (Default)](#akun-pengguna-bawaan-default)
- [Panduan Penggunaan Fitur](#panduan-penggunaan-fitur)
  - [Peran Kasir / Operator](#1-peran-kasir--operator)
  - [Peran Administrator](#2-peran-administrator)
- [Aturan Bisnis Penting](#aturan-bisnis-penting)
- [Pengujian Otomatis (Testing)](#pengujian-otomatis-testing)
- [Struktur Direktori Proyek](#struktur-direktori-proyek)
- [Troubleshooting & Tanya Jawab](#troubleshooting--tanya-jawab)

---

## Fitur Utama

1. **Manajemen Transaksi Cuci:**
   - Input pelanggan fleksibel: pelanggan tetap terdaftar atau pelanggan umum (*walk-in*).
   - Pemilihan layanan dinamis: mendukung multi-layanan (mobil dan helm sekaligus dalam satu nota).
   - Snapshot harga historis: harga dan nama layanan disalin ke item transaksi sehingga histori lama tidak berubah jika tarif master diperbarui.
   - Penomoran nota otomatis dan unik per hari: `{PREFIX}-YYYYMMDD-0001` (misal: `CW-20261113-0001`).
2. **Kalkulasi Biaya Cerdas:**
   - Subtotal per item, diskon (nominal Rp atau persentase %), dan total bersih.
   - Validasi ketat di sisi server via library `PriceCalculator`.
3. **Pembayaran Fleksibel (3 Metode):**
   - **Tunai (Cash):** Otomatis menghitung kembalian uang. Nominal bayar kurang otomatis ditolak.
   - **Transfer Bank & QRIS:** Nominal dicatat pas sesuai total tagihan tanpa kembalian.
4. **Alur Status Pengerjaan Terpantau:**
   - `Antri` $\rightarrow$ `Dikerjakan` $\rightarrow$ `Selesai` $\rightarrow$ `Diambil`.
   - Syarat status `Diambil`: Transaksi **wajib berstatus Lunas**.
   - Setiap perubahan status dicatat otomatis ke log riwayat pengerjaan (`status_logs`).
5. **Cetak Nota Kasir:**
   - Format cetak khusus printer kasir thermal (58 mm / 80 mm) maupun kertas A5.
   - Otomatis mencatat jumlah cetak ulang nota (*print count*).
6. **Master Pelanggan & Riwayat Transaksi:**
   - Pencarian cepat pelanggan lama berdasarkan nama atau nomor HP.
   - Halaman detail pelanggan menampilkan riwayat seluruh pencucian yang pernah dilakukan.
7. **Modul Administrator:**
   - **Master Layanan:** Tambah, edit, aktifkan/nonaktifkan layanan cuci mobil & helm.
   - **Kelola Pengguna:** CRUD akun Admin & Kasir, reset password, dan status aktif.
   - **Pengaturan Usaha:** Nama bengkel/usaha, alamat, telepon, teks footer nota, dan prefiks nota.
   - **Laporan Pendapatan:** Filter tanggal, kategori, kasir, metode bayar, statistik layanan terlaris, serta **Ekspor ke format CSV / Excel**.
   - **Audit Log:** Jejak audit aktivitas penting (perubahan harga tarif, pembatalan nota, login gagal berulang).

---

## Teknologi yang Digunakan

- **Backend:** PHP 8.1+ (diuji pada PHP 8.4)
- **Framework:** CodeIgniter 4 (versi 4.7+)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5.3.3 & Bootstrap Icons 1.11.3 (via CDN)
- **Testing:** PHPUnit 10

---

## Kebutuhan Sistem (Prerequisites)

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terpasang:
1. **PHP 8.1 atau lebih baru** dengan ekstensi berikut aktif di `php.ini`:
   - `intl`
   - `mbstring`
   - `mysqli`
   - `openssl`
   - `curl`
   - `gd`
2. **MySQL / MariaDB** (dapat menggunakan XAMPP, Laragon, atau MySQL standalone).
3. **Composer** (opsional bila folder `vendor/` sudah ada).

---

## Panduan Instalasi & Menjalankan Aplikasi

### 1. Masuk ke Folder Proyek
Buka terminal (PowerShell atau Command Prompt) dan arahkan ke direktori proyek CodeIgniter:
```bash
cd d:\cuci-cuci\cuci
```

### 2. Konfigurasi File Lingkungan (.env)
File `.env` sudah dikonfigurasi secara default untuk database lokal `cuci_db`. Jika perlu menyesuaikan kredensial database Anda, buka file `.env` di folder `cuci/` dan periksa bagian berikut:
```ini
database.default.hostname = localhost
database.default.database = cuci_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port     = 3306

app.baseURL     = 'http://localhost:8080/'
app.appTimezone = 'Asia/Jakarta'
```

### 3. Migrasi Database
Jalankan perintah spark untuk membuat seluruh tabel:
```bash
php spark migrate
```
*Tabel yang dibuat: `users`, `customers`, `services`, `transactions`, `transaction_items`, `status_logs`, `audit_logs`, dan `settings`.*

### 4. Eksekusi Data Awal (Seeder)
Jalankan seeder untuk mengisi akun Admin, Kasir, tarif layanan awal, identitas usaha, dan data contoh:
```bash
php spark db:seed InitialSeeder
```

### 5. Jalankan Web Server
Jalankan server pengembangan bawaan CodeIgniter:
```bash
php spark serve --port 8080
```

### 6. Buka di Browser
Akses aplikasi melalui peramban:
👉 **[http://localhost:8080](http://localhost:8080)**

---

## Akun Pengguna Bawaan (Default)

| Peran (Role) | Username | Password | Deskripsi Hak Akses |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses penuh seluruh sistem: Dashboard, Transaksi, Pelanggan, Layanan, Laporan & Ekspor, Pengguna, Pengaturan Usaha, dan Audit Log. |
| **Kasir / Operator** | `kasir` | `kasir123` | Akses operasional harian: Dashboard, Input Transaksi Baru, Daftar Transaksi, Penerimaan Pembayaran, Ubah Status Pengerjaan, Cetak Nota, dan Pelanggan. |

*(Disarankan untuk mengganti password default melalui menu Pengguna setelah instalasi di lingkungan produksi).*

---

## Panduan Penggunaan Fitur

### 1. Peran Kasir / Operator

#### A. Membuat Transaksi Baru (`/transactions/new`)
1. Klik tombol **Transaksi Baru** di Dashboard atau menu atas.
2. **Pilih Pelanggan:**
   - Jika pelanggan lama: Pilih dari dropdown "Pilih Pelanggan Terdaftar" (nama dan catatan plat akan terisi otomatis).
   - Jika pelanggan baru: Ketik nama pelanggan di kolom "Nama Pelanggan".
3. **Plat Nomor / Keterangan Item:** Masukkan nomor plat kendaraan (misal: `B 1234 CD - Avanza`) atau merek helm.
4. **Pilih Layanan Cuci:**
   - Pilih jenis layanan dari dropdown (Cuci Mobil / Cuci Helm).
   - Masukkan kuantitas (*Qty*).
   - Klik **Tambah Baris Layanan** jika ingin mencuci lebih dari satu layanan dalam satu transaksi.
5. **Potongan Diskon (Opsional):** Pilih jenis diskon (Rupiah atau %) dan masukkan nilai potongan bila ada promo.
6. **Opsi Pembayaran Langsung:**
   - Centang *"Langsung Bayar Sekarang"* jika pelanggan membayar di awal.
   - Pilih metode (Tunai, Transfer, QRIS) dan masukkan nominal uang yang diterima.
   - Bila pembayaran dilakukan belakangan saat pengambilan, biarkan tidak dicentang.
7. Klik **Simpan Transaksi**. Transaksi akan otomatis berstatus **Antri**.

#### B. Memperbarui Status Pengerjaan (`/transactions/{id}`)
Buka detail transaksi, lalu gunakan tombol aksi di kartu kontrol status:
1. **Mulai Kerjakan:** Mengubah status dari `Antri` menjadi `Dikerjakan`.
2. **Tandai Selesai Dicuci:** Mengubah status dari `Dikerjakan` menjadi `Selesai`.
3. **Ambil & Selesaikan:** Mengubah status dari `Selesai` menjadi `Diambil`.
   - *Catatan:* Tombol ini hanya dapat diklik jika transaksi sudah **Lunas**. Jika belum lunas, kasir harus mengklik tombol **Terima Pembayaran** terlebih dahulu.

#### C. Menerima Pembayaran (`/transactions/{id}`)
1. Klik tombol **Terima Pembayaran**.
2. Pilih metode bayar:
   - **Tunai:** Masukkan uang yang diterima, sistem otomatis menghitung kembalian.
   - **Transfer / QRIS:** Sistem otomatis mengisi nominal pas sesuai tagihan.
3. Klik **Konfirmasi Pembayaran**. Status transaksi berubah menjadi **Lunas**.

#### D. Mencetak Nota Transaksi
- Klik tombol **Cetak Nota** di halaman daftar transaksi atau detail transaksi.
- Jendela cetak akan terbuka secara otomatis dalam format nota kasir struk siap print (`Ctrl + P`).

---

### 2. Peran Administrator

Selain hak akses operasional kasir, Administrator memiliki fitur manajemen khusus:

#### A. Kelola Layanan (`/services`)
- Menambah jenis layanan baru (Kategori: Mobil atau Helm) beserta tarif harga.
- Mengubah tarif layanan cuci (setiap perubahan harga otomatis tercatat ke Audit Log).
- Mengaktifkan atau menonaktifkan status layanan (layanan nonaktif tidak akan muncul di form kasir namun riwayat lama tetap terjaga).

#### B. Laporan & Ekspor Data (`/reports`)
- Memantau total omzet pendapatan bersih pada rentang tanggal tertentu.
- Memfilter laporan berdasarkan kategori (Mobil/Helm), metode pembayaran, dan kasir yang bertugas.
- Melihat daftar **Layanan Terlaris** (*best seller*).
- Mengunduh rekap laporan dalam format **CSV / Excel** melalui tombol **Ekspor ke CSV / Excel**.

#### C. Kelola Pengguna (`/users`)
- Menambah akun kasir atau administrator baru.
- Mengubah profil dan mereset password pengguna.
- Menonaktifkan akun pengguna yang sudah tidak bertugas.

#### D. Pengaturan Usaha (`/settings`)
- Mengatur nama bisnis cuci, alamat, nomor telepon/WhatsApp, dan teks footer nota (pesan terima kasih / garansi).
- Mengatur prefiks nota (misal: `CW` untuk menghasilkan nomor nota `CW-20261113-0001`).

#### E. Audit Log (`/audit-logs`)
- Memeriksa catatan aktivitas sistem: perubahan tarif layanan, pembatalan transaksi, pembukaan status final, penambahan user, dan percobaan login gagal yang berulang.

---

## Aturan Bisnis Penting

Aplikasi ini menerapkan aturan validasi ketat sesuai standar POS:
1. **Aturan Status Diambil:** Status pengerjaan `Diambil` hanya dapat dipilih jika status pembayaran sudah `Lunas`.
2. **Aturan Status Mundur:** Alur pengerjaan bergerak maju (`Antri` $\rightarrow$ `Dikerjakan` $\rightarrow$ `Selesai` $\rightarrow$ `Diambil`). Aksi memundurkan status hanya dapat dilakukan oleh akun berhak akses Administrator.
3. **Pembatalan Transaksi:**
   - Transaksi hanya dapat dibatalkan jika masih berstatus `Antri` atau `Dikerjakan`.
   - Transaksi yang sudah berstatus `Lunas` hanya dapat dibatalkan oleh Administrator.
   - Setiap pembatalan transaksi wajib menyertakan alasan pembatalan dan otomatis tercatat ke Audit Log.
4. **Perhitungan Pembayaran:**
   - Pembayaran tunai dengan nominal kurang dari total tagihan akan otomatis ditolak.
   - Metode Transfer Bank dan QRIS mewajibkan nominal pembayaran sama persis dengan total tagihan.

---

## Pengujian Otomatis (Testing)

Aplikasi telah dilengkapi dengan pengujian unit (*Unit Test*) dan logika bisnis (*Business Logic Test*) menggunakan PHPUnit:

Untuk menjalankan pengujian:
```bash
cd d:\cuci-cuci\cuci
vendor/bin/phpunit
```
*Hasil pengujian mencakup 25 test dan 66 assertions dengan status kelulusan 100% (`OK`).*

---

## Struktur Direktori Proyek

```text
cuci/
├── app/
│   ├── Config/              # Konfigurasi aplikasi, database, rute, filter
│   ├── Controllers/         # Auth, Dashboard, Transactions, Customers, Services, Reports, Users, Settings, AuditLogs
│   ├── Database/
│   │   ├── Migrations/      # Skema 8 tabel database
│   │   └── Seeds/           # InitialSeeder (data awal admin, kasir, layanan, settings)
│   ├── Filters/             # AuthFilter (login check) & RoleFilter (admin only check)
│   ├── Helpers/             # app_helper.php (format rupiah, badge status, badge kategori)
│   ├── Libraries/           # PriceCalculator (hitung biaya & bayar) & AuditLogger (jejak audit)
│   ├── Models/              # 8 Model CodeIgniter 4
│   └── Views/
│       ├── layouts/         # main.php (Layout utama Bootstrap 5)
│       ├── partials/        # navbar.php, flash.php
│       ├── auth/            # login.php
│       ├── dashboard/       # index.php
│       ├── transactions/    # index.php, new.php, show.php, print.php
│       ├── customers/       # index.php, new.php, edit.php, show.php
│       ├── services/        # index.php
│       ├── users/           # index.php
│       ├── settings/        # index.php
│       ├── reports/         # index.php
│       └── audit_logs/      # index.php
├── public/                  # Aset publik & index.php
├── tests/                   # Pengujian unit & fungsional
├── .env                     # File konfigurasi environment lokal
├── spark                    # CLI tool CodeIgniter 4
└── composer.json            # Dependensi proyek
```

---

## Troubleshooting & Tanya Jawab

**Q: Database tidak bisa terhubung (*DatabaseException*)?**
> Pastikan service MySQL/MariaDB (misalnya XAMPP) sudah dalam status *Running* di port 3306 dan database `cuci_db` sudah dibuat (`CREATE DATABASE cuci_db;`).

**Q: Port 8080 sudah digunakan aplikasi lain?**
> Anda dapat menjalankan server spark pada port alternatif, misalnya port 8081:
> ```bash
> php spark serve --port 8081
> ```
> Lalu buka `http://localhost:8081` di browser.

**Q: Terdapat angka peringatan merah di sidebar file explorer VS Code / Antigravity IDE?**
> Angka merah tersebut adalah peringatan *static analysis* dari ekstensi PHP Intelephense yang belum mengenali variabel inject runtime pada file view template CodeIgniter. File konfigurasi `.vscode/settings.json` sudah disediakan untuk menonaktifkan peringatan palsu tersebut. Cukup lakukan **Reload Window** (`Ctrl + Shift + P` $\rightarrow$ `Developer: Reload Window`).

---

## Lisensi
Aplikasi ini dikembangkan untuk kebutuhan internal usaha cuci mobil dan helm.
Dilisensikan di bawah lisensi MIT.
