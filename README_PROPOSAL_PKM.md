# Sistem Proposal PKM - PHIPROSAL

## Deskripsi

Sistem Proposal PKM (Program Kreativitas Mahasiswa) adalah aplikasi web yang memungkinkan mahasiswa untuk mengajukan proposal PKM secara online. Sistem ini dibangun menggunakan Laravel dan menyediakan antarmuka yang user-friendly untuk pengelolaan proposal.

## Fitur Utama

### 1. Layout Konsisten

-   **Navbar**: Tombol hamburger, icon profile dan nama user, icon notifikasi, dan logout
-   **Sidebar**: Menu "PROPOSAL" dengan submenu PKK Ormawa (non-active) dan PKM
-   **Responsive Design**: Mendukung tampilan mobile dan desktop

### 2. Pengajuan Proposal PKM

-   Form pengajuan proposal yang lengkap
-   Upload file proposal dan persetujuan dosen (PDF)
-   Validasi form real-time
-   Drag & drop file upload
-   Progress bar untuk upload

### 3. Lihat Proposal

-   Daftar proposal yang telah diajukan
-   Status proposal (Menunggu Validasi, Sedang Direview, Disetujui, Ditolak)
-   Detail proposal lengkap
-   Download file proposal
-   Sidebar "AKSI" untuk melihat hasil review

### 4. Review System

-   Modal popup untuk hasil review administratif
-   Modal popup untuk hasil review substantif
-   Detail feedback dari reviewer

### 5. PDF Viewer

-   Integrasi PDF.js untuk menampilkan dokumen
-   Tab untuk proposal dan persetujuan
-   Loading animation saat membuka PDF

## Struktur File

### Views

```
resources/views/
├── mainlayout/
│   └── app.blade.php          # Layout utama
├── mahasiswa/
│   ├── ajukanproposal.blade.php    # Form pengajuan proposal
│   └── proposal_list.blade.php     # Daftar proposal
└── auth/
    └── login.blade.php        # Halaman login
```

### Controllers

```
app/Http/Controllers/
├── AuthController.php         # Controller autentikasi
└── ProposalController.php     # Controller proposal PKM
```

### Models

```
app/Models/
├── Proposal.php              # Model proposal
├── Dokumen.php               # Model dokumen
├── Mahasiswa.php             # Model mahasiswa
└── User.php                  # Model user
```

### Routes

```
routes/web.php                # Definisi routing
```

## Instalasi dan Setup

### 1. Prerequisites

-   PHP 8.0 atau lebih tinggi
-   Composer
-   Laravel 10
-   Database (MySQL/PostgreSQL)
-   Web server (Apache/Nginx)

### 2. Instalasi

```bash
# Clone repository
git clone <repository-url>
cd PengajuanProposal3

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengajuan_proposal
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Run seeders (opsional)
php artisan db:seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve
```

### 3. Konfigurasi Storage

Pastikan folder storage dapat diakses untuk upload file:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Penggunaan

### 1. Login sebagai Mahasiswa

-   Akses `/login`
-   Masuk dengan kredensial mahasiswa
-   Akan diarahkan ke dashboard mahasiswa

### 2. Ajukan Proposal

-   Klik menu "Ajukan Proposal" di sidebar
-   Isi form pengajuan proposal
-   Upload file proposal dan persetujuan dosen
-   Klik "Ajukan Proposal"

### 3. Lihat Proposal

-   Klik menu "Lihat Proposal" di sidebar
-   Lihat daftar proposal yang telah diajukan
-   Klik "Lihat Detail" untuk melihat detail proposal
-   Klik tombol "AKSI" untuk melihat hasil review

### 4. Download File

-   Di halaman detail proposal, klik "Download"
-   File akan diunduh ke komputer

## Validasi Form

### Pengajuan Proposal

-   Judul: Wajib diisi, maksimal 255 karakter
-   Skim: Wajib dipilih dari dropdown
-   Dana: Wajib diisi, maksimal Rp 15.000.000
-   Dosen Pembimbing: Wajib diisi
-   Data Ketua Tim: Semua field wajib diisi
-   File: Wajib upload, format PDF, maksimal 5MB

### Validasi Anggota Tim

-   Jika nama diisi, NIM juga harus diisi
-   Jika NIM diisi, nama juga harus diisi
-   Email harus valid
-   Nomor HP minimal 10 digit

## Status Proposal

1. **Pending**: Proposal baru diajukan
2. **Review**: Sedang direview oleh reviewer
3. **Approved**: Proposal disetujui
4. **Rejected**: Proposal ditolak

## Review System

### Review Administratif

-   Validasi format dokumen
-   Kelengkapan data
-   Kesesuaian dengan template

### Review Substantif

-   Kualitas konten proposal
-   Metodologi penelitian
-   Inovasi dan kreativitas
-   Kelayakan implementasi

## Keamanan

### File Upload

-   Validasi tipe file (PDF only)
-   Validasi ukuran file (max 5MB)
-   Penyimpanan di folder terpisah
-   Nama file diacak untuk keamanan

### Authentication

-   Middleware auth untuk semua route
-   Validasi user type (mahasiswa/dosen/reviewer)
-   Session management

### Authorization

-   Mahasiswa hanya bisa akses proposal miliknya
-   Validasi ownership sebelum download/edit/delete

## Customization

### Styling

-   CSS variables untuk warna tema
-   Responsive design dengan Bootstrap 5
-   Custom animations dan transitions
-   Dark mode support (opsional)

### Configuration

-   File size limits di config
-   Allowed file types
-   Email templates
-   Notification settings

## Troubleshooting

### Common Issues

1. **File upload gagal**

    - Periksa permission folder storage
    - Pastikan disk space mencukupi
    - Cek konfigurasi upload_max_filesize di php.ini

2. **PDF tidak tampil**

    - Pastikan PDF.js library terload
    - Cek CORS policy untuk file PDF
    - Periksa path file di storage

3. **Layout tidak responsive**
    - Pastikan Bootstrap CSS terload
    - Cek viewport meta tag
    - Test di berbagai ukuran layar

### Debug Mode

```bash
# Enable debug mode
APP_DEBUG=true

# Check logs
tail -f storage/logs/laravel.log
```

## Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License.

## Support

Untuk bantuan dan dukungan teknis, silakan hubungi:

-   Email: support@phiprosal.com
-   Documentation: https://docs.phiprosal.com
-   Issues: https://github.com/phiprosal/issues
