# PTSP Backend API

## 📌 Deskripsi
Sistem API Backend PTSP (Pelayanan Terpadu Satu Pintu) ini dirancang untuk membantu institusi pendidikan dalam mengelola administrasi sekolah secara digital. API ini menyediakan berbagai layanan untuk pengelolaan surat, keuangan, sarana prasarana, dan data pendidikan.

## 🛠 Teknologi yang Digunakan
- **Backend:** PHP Slim Framework + Twig
- **Database:** MySQL
- **Autentikasi:** Firebase Authentication
- **PDF Generator:** mPDF
- **Spreadsheet Generator:** PhpSpreadsheet

## ⚡ Fitur Utama
- **Dashboard:** Statistik dan monitoring sistem secara real-time
- **Surat Masuk:** Pengelolaan surat yang masuk ke institusi
- **Surat Keluar:** Pengelolaan surat yang keluar dari institusi
- **Riwayat Surat:** Tracking dan arsip surat masuk dan keluar
- **Tenaga Pendidikan:** Manajemen data guru dan staff
- **Keuangan:** Pengelolaan data keuangan sekolah
- **Sarana Prasarana:** Inventarisasi dan monitoring fasilitas sekolah
- **Penerimaan Siswa Baru (PSB):** Sistem pendaftaran siswa baru online
- **Buku Tamu:** Pencatatan tamu yang berkunjung ke sekolah
- Ekspor laporan ke Excel (PhpSpreadsheet) dan PDF (mPDF)

## 🚀 Instalasi
### 1. Clone Repository
```sh
git clone https://github.com/NextDep/PTSP-Backend.git
cd ptsp-backend
```

### 2. Instalasi Backend
```sh
composer install
```
Buat file `.env` berdasarkan `.env.example` dan sesuaikan konfigurasi database.
```sh
cp .env.example .env
```

### 3. Menjalankan Backend
```
composer run serve
```

## 🔗 Domain
- DEVELOPMENT : 
- PRODUCTION  : 

---

## 📝 Kontributor
- **Cecep Rokani** (https://github.com/ceceprokani)

## 📄 Lisensi
Project ini dilisensikan di bawah [MIT License](LICENSE).