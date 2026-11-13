# AGENTS.md — Aplikasi Cuci Mobil & Cuci Helm

Instruksi untuk AI coding agent yang bekerja di repository ini. Baca bagian ini sebelum mengubah kode.

## 1. Ringkasan Proyek

Aplikasi web admin-kasir untuk mencatat transaksi cuci mobil dan cuci helm: input transaksi berdasarkan jenis layanan, kelola status pengerjaan, hitung biaya otomatis, catat pembayaran, dan cetak nota. Peran pengguna: **Admin** dan **Kasir**.

Dokumen acuan (urutan prioritas bila ada konflik):

1. `PRD.md` — spesifikasi produk (versi 1.1)
2. `STYLE_GUIDE_UI.md` — panduan tampilan
3. `AGENTS.md` — file ini

## 2. Tech Stack

- PHP 8.1+ dan **CodeIgniter 4** (MVC bawaan CI4)
- MySQL / MariaDB
- **Bootstrap 5** (tampilan bawaan) + Bootstrap Icons
- JavaScript vanilla (atau jQuery bila sudah ada) untuk form dinamis
- Tanpa framework frontend tambahan (React/Vue) kecuali diminta

## 3. Perintah Penting

```bash
composer install                 # pasang dependensi
cp env .env                      # salin konfigurasi, lalu isi database & baseURL
php spark migrate                # jalankan migration
php spark db:seed InitialSeeder  # admin default, contoh layanan, pengaturan usaha awal
php spark serve                  # server lokal (http://localhost:8080)
php spark make:migration NamaMigration
php spark make:model NamaModel
php spark make:controller NamaController
vendor/bin/phpunit               # jalankan test
```

Jangan mengubah file di `system/` dan `vendor/`. Jangan commit `.env`.

## 4. Struktur Direktori

| Lokasi | Isi |
|---|---|
| `app/Controllers` | `Auth`, `Dashboard`, `Transactions`, `Customers`, `Services`, `Reports`, `Users`, `Settings`, `AuditLogs` |
| `app/Models` | `UserModel`, `CustomerModel`, `ServiceModel`, `TransactionModel`, `TransactionItemModel`, `StatusLogModel`, `AuditLogModel`, `SettingModel` |
| `app/Views` | `layouts/main.php`, `partials/`, satu folder per modul |
| `app/Filters` | `AuthFilter`, `RoleFilter` |
| `app/Libraries` | `PriceCalculator` (hitung biaya), `AuditLogger` (catat audit) |
| `app/Helpers` | format rupiah dan badge status |
| `app/Database/Migrations`, `Seeds` | skema dan data awal |
| `public/assets` | CSS/JS/gambar milik aplikasi |

Taruh logika bisnis di Model atau Library, **bukan** di View. Controller dibuat tipis.

## 5. Konvensi Kode

- Ikuti **PSR-12**; indentasi 4 spasi.
- Kelas `PascalCase`, method dan variabel `camelCase`, nama tabel dan kolom `snake_case` (tabel bentuk jamak, mis. `transactions`).
- Nama file kelas sama dengan nama kelas.
- Teks UI dan label dalam **Bahasa Indonesia**; nama kode dalam bahasa Inggris.
- Gunakan type hint dan return type bila memungkinkan.
- Gunakan aturan validasi CI4 untuk semua input form.
- Jangan meninggalkan kode mati, `var_dump`, atau `dd()`.

## 6. Aturan Bisnis (Wajib Dipatuhi)

1. **Hitung biaya:** subtotal item = harga × qty; total kotor = jumlah subtotal; **total = total kotor − diskon**; kembalian (tunai) = uang diterima − total.
2. Perhitungan wajib dilakukan ulang **di server** lewat `PriceCalculator`; jangan percaya angka dari browser.
3. `transaction_items` menyimpan **salinan** `price` dan `service_name` saat transaksi; jangan membaca ulang harga dari master.
4. Diskon (rupiah atau persen) tidak boleh melebihi total kotor; total tidak boleh negatif.
5. Alur status: `antri → dikerjakan → selesai → diambil`; `dibatalkan` hanya dari `antri` atau `dikerjakan`. Mundur hanya oleh Admin.
6. Status `diambil` hanya boleh bila `payment_status = lunas`.
7. Metode pembayaran: `tunai`, `transfer`, `qris` (dicatat manual, tanpa payment gateway). Kembalian hanya untuk `tunai`; `transfer` dan `qris` nominal harus sama dengan total.
8. Pembayaran kurang dari total ditolak (DP/pembayaran sebagian ada di Fase 2, jangan dibuat).
9. Setiap perubahan status wajib menulis baris ke `status_logs`.
10. Pembatalan wajib mengisi `cancel_reason`. Membatalkan transaksi yang sudah `lunas` hanya oleh Admin.
11. Transaksi `diambil` / `dibatalkan` tidak dapat diedit (kecuali Admin, dan dicatat di audit log).
12. Nomor transaksi `CW-YYYYMMDD-0001` (prefiks dari `settings.invoice_prefix`), unik dan berurutan per hari.
13. Transaksi tidak dihapus permanen; gunakan status `dibatalkan` atau soft delete.
14. Layanan nonaktif disembunyikan dari form transaksi, riwayatnya tetap.
15. Pendapatan di laporan = jumlah `total` transaksi berstatus `diambil` pada rentang tanggal terpilih; transaksi batal tidak dihitung.
16. Pelanggan bersifat opsional: transaksi boleh memakai `customer_id` atau hanya `customer_name` (walk-in). No. HP pelanggan unik bila diisi.

## 7. Audit Log

Catat lewat `AuditLogger` untuk: perubahan harga layanan, pembuatan/perubahan/penonaktifan pengguna, reset password, pembatalan transaksi, pembukaan kembali transaksi final, dan login gagal berulang. Format: `user_id`, `action`, `entity`, `entity_id`, `detail`, `created_at`. Jangan menyimpan password atau data rahasia di kolom `detail`.

## 8. Database

- Semua perubahan skema lewat **migration**; jangan ubah tabel manual.
- Tabel: `users`, `customers`, `services`, `transactions`, `transaction_items`, `status_logs`, `audit_logs`, `settings` (key-value: `business_name`, `address`, `phone`, `receipt_footer`, `invoice_prefix`).
- Gunakan kunci asing dan indeks pada `transaction_id`, `service_id`, `customer_id`, `invoice_no`, `status`, `payment_method`, `created_by`, `created_at`.
- Uang disimpan sebagai `DECIMAL(12,0)` (rupiah tanpa pecahan).
- Simpan header transaksi + item + log status dalam satu transaksi database (`$db->transStart()` / `transComplete()`).
- Seeder menyediakan satu akun Admin default (wajib diganti passwordnya di produksi), contoh layanan mobil/helm, dan pengaturan usaha awal.

## 9. Keamanan

- Password memakai `password_hash()` dan `password_verify()`.
- CSRF aktif; setiap form memakai `csrf_field()`.
- Semua output di view dibungkus `esc()`.
- Akses dikontrol Filter: `AuthFilter` (harus login) dan `RoleFilter` (Admin saja untuk Layanan, Pengguna, Pengaturan, Laporan, Audit Log).
- Batasi percobaan login berulang dan akhiri sesi yang tidak aktif.
- Gunakan Model / Query Builder dengan parameter binding; jangan menyusun SQL dari string input.
- Jangan menaruh rahasia (password DB, key) di kode atau repository.

## 10. Tampilan (UI)

Ikuti `STYLE_GUIDE_UI.md`. Ringkasnya:

- Semua halaman memakai `layouts/main.php` dengan `extend` / `section`.
- Pakai komponen dan utility class **Bootstrap 5 bawaan**; hindari CSS inline dan kode hex.
- Badge status: Antri = `secondary`, Dikerjakan = `warning`, Selesai = `info`, Diambil = `success`, Dibatalkan = `danger`. Gunakan helper, jangan tulis ulang di tiap view.
- Angka uang berformat `Rp 1.250.000` dan rata kanan.
- Pesan sukses/error lewat flash data dan komponen alert.
- Nota cetak: halaman terpisah tanpa navbar, identitas usaha diambil dari `settings`.

## 11. Rute

Definisikan di `app/Config/Routes.php` dengan grup dan filter:

- Publik: `/login`
- Terautentikasi (Admin dan Kasir): `/dashboard`, `/transactions` (daftar, `new`, `create`, detail, `status`, `pay`, `cancel`, `print`), `/customers`
- Admin saja: `/services`, `/users`, `/settings`, `/reports`, `/audit-logs`

## 12. Pengujian

- Unit test `PriceCalculator`: subtotal, diskon rupiah dan persen, batas diskon, kembalian tunai, validasi transfer/qris.
- Test aturan transisi status dan syarat `diambil` harus `lunas`.
- Test pembatasan akses per peran dan pembatalan transaksi lunas oleh Kasir (harus ditolak).
- Test penulisan `status_logs` dan `audit_logs`.
- Jalankan `vendor/bin/phpunit` dan pastikan lulus sebelum menyatakan pekerjaan selesai.

## 13. Cara Kerja Agent

- Baca `PRD.md` dan `STYLE_GUIDE_UI.md` sebelum membuat fitur baru.
- Kerjakan **satu fitur per langkah** sesuai tahap di PRD; jangan menambah fitur di luar MVP (DP/pembayaran sebagian, tutup shift, WhatsApp, payment gateway, membership, penjadwalan online, stok bahan).
- Bila requirement ambigu atau bertentangan, **tanyakan dulu**; jangan berasumsi.
- Ubah seminimal mungkin: jangan merefaktor atau mengganti nama file yang tidak terkait tugas.
- Setelah selesai, jelaskan file apa yang berubah dan cara mengujinya.
- Jangan menjalankan perintah destruktif (`migrate:refresh`, `DROP`, hapus data) tanpa persetujuan.

## 14. Definisi Selesai

- Fitur sesuai aturan bisnis bagian 6 dan kriteria penerimaan di `PRD.md`.
- Validasi, `esc()`, filter akses, dan audit log (bila relevan) sudah terpasang.
- Tampilan konsisten dengan `STYLE_GUIDE_UI.md` dan responsif di lebar HP.
- Migration dapat dijalankan dari nol tanpa error.
- Test relevan sudah ditulis dan lulus.
