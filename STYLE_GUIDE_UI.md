# Panduan Style UI — Aplikasi Cuci Mobil & Cuci Helm

**Framework:** CodeIgniter 4 · **UI:** Bootstrap 5 (tampilan bawaan, tanpa tema kustom berat) · **Pasangan dokumen:** PRD Aplikasi Cuci Mobil & Cuci Helm

## 1. Prinsip Desain

- **Bawaan Bootstrap dulu.** Gunakan komponen dan utility class Bootstrap 5 apa adanya; CSS kustom hanya bila benar-benar perlu.
- **Cepat dipakai kasir.** Tombol besar, label jelas, alur sedikit klik.
- **Status terbaca sekilas.** Warna badge konsisten di seluruh halaman.
- **Responsif.** Harus nyaman di laptop kasir maupun layar HP.
- **Konsisten.** Satu pola untuk satu jenis halaman (daftar, form, detail).

## 2. Setup Bootstrap 5 di CI4

Simpan aset di `public/assets/` (atau pakai CDN saat pengembangan). Semua halaman memakai satu layout `app/Views/layouts/main.php`.

```php
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Cuci Mobil & Helm') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <?= $this->include('partials/navbar') ?>
  <main class="container py-4">
    <?= $this->include('partials/flash') ?>
    <?= $this->renderSection('content') ?>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
```

Setiap view halaman memakai `extend('layouts/main')` dan `section('content')`.

## 3. Warna

Pakai palet bawaan Bootstrap 5 lewat class kontekstual (`primary`, `success`, dst.), jangan menulis kode hex sendiri di view.

| Peran | Class Bootstrap | Penggunaan |
|---|---|---|
| Primary | `primary` (biru bawaan) | Navbar, tombol aksi utama (Simpan, Transaksi Baru) |
| Success | `success` | Lunas, Diambil, notifikasi berhasil |
| Warning | `warning` | Dikerjakan, peringatan |
| Info | `info` | Selesai, informasi |
| Danger | `danger` | Dibatalkan, hapus, error |
| Secondary | `secondary` | Antri, aksi sekunder |
| Latar halaman | `bg-light` | Body |
| Permukaan | `bg-white` / card | Konten utama |

### Badge status transaksi

| Status | Badge |
|---|---|
| Antri | `badge text-bg-secondary` |
| Dikerjakan | `badge text-bg-warning` |
| Selesai | `badge text-bg-info` |
| Diambil | `badge text-bg-success` |
| Dibatalkan | `badge text-bg-danger` |

Badge kategori layanan: Mobil memakai `text-bg-primary`, Helm memakai `text-bg-dark`.

Badge pembayaran: Belum = `text-bg-light border`, Lunas = `text-bg-success`.

Buat satu helper (mis. `status_badge($status)` di `app/Helpers`) agar pemetaan ini tidak diulang di tiap view.

## 4. Tipografi

- Font: **system font stack bawaan Bootstrap** (tanpa font eksternal).
- Judul halaman `h1` dengan class `h3`, judul seksi `h5`, teks isi ukuran dasar 16px.
- Angka uang tampil dengan format `Rp 1.250.000`, rata kanan, pakai class `text-end` dan `fw-semibold` untuk total.
- Teks bantuan memakai `form-text` atau `text-muted small`.

## 5. Layout & Spasi

- Konten dibungkus `container` (lebar penuh di HP, terpusat di desktop).
- Jarak antar blok memakai utility `mb-3` / `mb-4`; padding card memakai bawaan `card-body`.
- Grid: gunakan `row g-3` dan `col-md-*`. Form transaksi dua kolom: `col-lg-8` (form) dan `col-lg-4` (ringkasan biaya).
- Navbar atas `navbar navbar-expand-lg navbar-dark bg-primary` berisi: Dashboard, Transaksi, Layanan*, Laporan*, Pengguna*, dan menu pengguna/Logout (*hanya Admin).

## 6. Komponen

**Tombol**
- Aksi utama: `btn btn-primary`; sekunder: `btn btn-outline-secondary`; hapus: `btn btn-outline-danger`.
- Satu halaman hanya satu tombol primary yang dominan.
- Tombol di tabel memakai `btn-sm` dengan ikon Bootstrap Icons (mis. `bi-eye`, `bi-pencil`).

**Form**
- Gunakan `form-label`, `form-control`, `form-select`; error validasi dengan `is-invalid` + `invalid-feedback`.
- Field wajib diberi tanda `*` pada label.
- Input uang memakai `input-group` dengan prefiks `Rp`.

**Tabel**
- `table table-hover align-middle` di dalam `div.table-responsive`; header memakai `table-light`.
- Kolom angka rata kanan; kolom aksi di paling kanan.
- Pagination bawaan `pagination` Bootstrap (sesuaikan lewat view pager CI4).

**Card**
- Dashboard: kartu statistik `card shadow-sm` berisi angka besar (`display-6`) dan label kecil.
- Detail transaksi: card terpisah untuk Rincian Item, Ringkasan Biaya, dan Riwayat Status.

**Alert / Notifikasi**
- Pesan flash CI4 ditampilkan lewat `alert alert-success|danger|warning alert-dismissible`.

**Modal**
- Dipakai untuk tambah/ubah layanan dan konfirmasi hapus/batal; tombol konfirmasi berwarna sesuai risiko.

**Ikon**
- Bootstrap Icons, ukuran mengikuti teks, selalu disertai teks pada aksi penting.

## 7. Pola Halaman

| Halaman | Pola |
|---|---|
| Login | Card sempit di tengah (`col-md-4 mx-auto`), logo/nama aplikasi di atas form |
| Dashboard | Baris 4 kartu statistik, lalu tabel "Transaksi Terbaru" |
| Daftar | Judul + tombol primary di kanan, card filter, tabel, pagination |
| Form (buat/ubah) | Card form, tombol Simpan (primary) dan Batal (outline) di bawah |
| Detail | Header berisi no. transaksi + badge status, lalu card-card rincian dan tombol aksi |
| Nota cetak | Halaman terpisah tanpa navbar, `@media print`, lebar struk 58/80 mm atau A5 |

## 8. Responsif

- Breakpoint bawaan Bootstrap: kolom ringkasan turun ke bawah form di bawah `lg`.
- Tabel lebar dibungkus `table-responsive`; bila perlu sembunyikan kolom sekunder dengan `d-none d-md-table-cell`.
- Target sentuh tombol minimal setinggi default Bootstrap (jangan terlalu kecil di HP).

## 9. Aksesibilitas

- Setiap input punya `label` yang terhubung (`for`/`id`).
- Jangan menyampaikan status hanya lewat warna; badge selalu berisi teks status.
- Kontras teks mengikuti kombinasi `text-bg-*` bawaan Bootstrap.
- Modal dan dropdown memakai atribut `aria-*` sesuai dokumentasi Bootstrap.

## 10. Do & Don't

| Lakukan | Hindari |
|---|---|
| Pakai utility class Bootstrap | Menulis CSS inline atau kode hex di view |
| Satu layout utama dan partial bersama | Menyalin navbar/footer ke tiap halaman |
| Memakai helper untuk badge dan format rupiah | Mengulang `if/else` status di banyak view |
| Escape output dengan `esc()` | Mencetak input pengguna mentah |
| Menaruh CSS kustom kecil di `public/assets/css/app.css` | Menimpa file inti Bootstrap |

## 11. Checklist Review UI

- Semua halaman memakai `layouts/main` dan tampil benar di lebar HP.
- Badge status mengikuti tabel di bagian 3.
- Angka uang berformat rupiah dan rata kanan.
- Pesan sukses/error muncul lewat alert.
- Tombol hapus/batal selalu meminta konfirmasi.
