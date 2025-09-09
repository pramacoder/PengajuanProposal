# Sistem Pengajuan Proposal PKM

## Deskripsi Sistem

sistem pengajuan proposal PKM (Program Kreativitas Mahasiswa) yang dibangun menggunakan Laravel 10. Sistem ini mengelola seluruh alur kerja proposal PKM dari pengajuan oleh mahasiswa hingga pengumuman hasil final, dengan dukungan multi-role user (Mahasiswa, Dosen, Reviewer, dan Operator).

## Arsitektur Sistem

### Role User

-   **Mahasiswa**: Pengajuan proposal, monitoring status, lihat hasil
-   **Dosen**: Validasi proposal, penilaian administratif
-   **Reviewer**: Review substantif proposal
-   **Operator**: Manajemen sistem, penentuan reviewer, monitoring

### Teknologi

-   **Backend**: Laravel 10, PHP 8.0+
-   **Frontend**: Blade Templates, Bootstrap 5, Tailwind CSS
-   **Database**: MySQL
-   **Authentication**: Multi-guard Laravel
-   **File Storage**: Laravel Storage

## Alur Lengkap Proposal PKM

### 1. **Awal - Get Started & Authentication**

```
User → Get Started → Pilih Role → Login/Register → Dashboard
```

**Fitur:**

-   Halaman "Get Started" dengan pilihan role
-   Login sesuai role yang dipilih
-   Registrasi user baru dengan validasi lengkap
-   Multi-guard authentication untuk setiap role

### 2. **Pengajuan Proposal oleh Mahasiswa**
#### Form Pengajuan (`/mahasiswa/ajukanproposal`)
**Field Wajib:**
-   Judul proposal (10-200 karakter)
-   Skim PKM (RE, RSH, KC, PM, PI, K, KI, VGK, AI, GFT)
-   Dana diajukan (Rp 1.000.000 - Rp 15.000.000)
-   Dosen pendamping
-   File proposal (PDF, max 5MB)

#### Struktur Tim (Minimal 3, Maksimal 5 orang)

**Wajib (3 orang):**

-   Ketua tim (semua field lengkap)
-   Anggota 1 (semua field lengkap)
-   Anggota 2 (semua field lengkap)

**Opsional (2 orang):**

-   Anggota 3 (jika diisi, semua field lengkap)
-   Anggota 4 (jika diisi, semua field lengkap)

**Field setiap anggota:**

-   Nama lengkap
-   NIM (minimal 8 digit)
-   Program studi
-   Fakultas
-   Email (format valid)
-   No. HP (minimal 10 digit)

#### Fitur Auto-fill

-   Auto-fill data ketua tim berdasarkan NIM
-   Auto-fill data anggota tim berdasarkan NIM
-   Sinkronisasi real-time antar field
-   Validasi duplikasi NIM dalam tim
-   Peringatan jika anggota sudah terdaftar di proposal lain

#### Validasi Form

-   Validasi field wajib real-time
-   Validasi format data (NIM, email, no HP)
-   Validasi logika bisnis (ketua ≠ anggota)
-   Validasi file (format dan ukuran)
-   Error message spesifik di bawah setiap field

### 3. **Validasi oleh Dosen**

#### Dashboard Dosen (`/dosen/dashboard`)

**Fitur:**

-   Daftar proposal yang perlu divalidasi
-   Filter berdasarkan status validasi
-   Detail proposal lengkap dengan dokumen

#### Proses Validasi (`/dosen/validasi_proposal`)

**Status Validasi:**

-   `pending` - Belum divalidasi
-   `valid` - Proposal valid
-   `tidak_valid` - Proposal ditolak

**Aksi Validasi:**

-   Review dokumen proposal
-   Validasi kelengkapan data
-   Set status validasi
-   Tambah catatan jika diperlukan

### 4. **Review oleh Reviewer**
#### Dashboard Reviewer (`/reviewer/dashboard`)
**Fitur:**

-   Daftar proposal yang perlu direview
-   Filter berdasarkan jenis review
-   Progress review per proposal

#### Review Administratif (`/reviewer/review_administratif`)
**Proses:**
1. Pilih checklist kesalahan administratif
2. Tulis catatan detail review
3. Submit review administratif
4. Status proposal berubah ke `review_substantif`

**Data yang Disimpan:**
-   Checklist kesalahan (JSON array)
-   Catatan administratif (text)
-   ID proposal dan reviewer
-   Timestamp

#### Review Substantif (`/reviewer/review_substantif`)
**Proses:**

1. Review kualitas konten proposal
2. Tulis catatan substantif
3. Submit review substantif
4. Jika semua review selesai, status berubah ke `review_completed`

**Data yang Disimpan:**
-   Catatan substantif (text)
-   ID proposal dan reviewer
-   Timestamp

### 5. **Manajemen oleh Operator**
#### Dashboard Operator (`/operator/dashboard`)
**Ringkasan:**
-   Total semua proposal PKM
-   Jumlah PKM-8 Bidang (memerlukan dana)
-   Jumlah PKM Insentif
**Monitoring per Skim:**
-   RE, RSH, KC, PM, PI, K, KI, VGK (PKM-8 Bidang)
-   AI, GFT (PKM Insentif)

**Status per Skim:**
-   Jumlah total
-   Sudah valid
-   Belum valid
-   Tolak valid
-   Sedang review
-   Selesai review

#### Pilih Reviewer (`/operator/pilih_reviewer`)
**Fitur:**

-   Daftar proposal yang perlu reviewer
-   Search reviewer berdasarkan nama/NIP
-   Assign reviewer ke proposal
-   Monitoring progress review

#### Ruang Kontrol (`/operator/ruang_kontrol`)
**Fitur:**
-   Overview semua proposal
-   Filter berdasarkan status
-   Monitoring progress dari validasi hingga review
-   Export data untuk analisis

### 6. **Hasil Final**
#### Pengumuman Hasil (`/dosen/hasil_final`)
**Status Final:**
-   `lolos` - Proposal disetujui
-   `tidak_lolos` - Proposal ditolak
**Data hasil:**
-   Nilai administratif (dari review administratif)
-   Nilai substantif (dari review substantif)
-   Catatan reviewer
-   Status final
#### Dashboard Mahasiswa - Lihat Hasil
**Fitur:**
-   Status proposal terkini
-   Detail hasil review
-   Download dokumen
-   Notifikasi status perubahan
### 7. **Sistem Notifikasi**
#### Fitur Notifikasi
-   **Real-time**: Polling setiap 30 detik
-   **Role-based**: Notifikasi sesuai user type
-   **Interactive**: Action button untuk setiap notifikasi
-   **Visual**: Badge counter, warna sesuai tipe
#### Tipe Notifikasi
-   **Success** (Hijau): Proposal disetujui, status berhasil
-   **Warning** (Kuning): Perlu revisi, perhatian khusus
-   **Info** (Biru): Informasi umum, status berubah
-   **Danger** (Merah): Proposal ditolak, error
#### Notifikasi per Role
-   **Mahasiswa**: Status proposal, revisi, hasil review
-   **Dosen**: Proposal baru yang perlu validasi
-   **Reviewer**: Proposal yang perlu direview
-   **Operator**: Proposal pending yang perlu diproses
## Struktur Database
### Tabel Utama
```sql
-- Users dan Authentication
users (id, name, email, password, user_type)
mahasiswas (id, nim, nama, email, no_hp, fakultas, prodi)
dosens (id, nidn, nama, email, no_hp)
reviewers (id, nip, nama, email, no_hp)
pts (id, nip, nama, email, no_hp)

-- Proposal dan Tim
proposals (id, judul, skim, dana_diajukan, status, status_validasi, tanggal_pengajuan)
teams (id, id_proposal, ketua_nim, anggota1_nim, anggota2_nim, anggota3_nim, anggota4_nim)

-- Dokumen dan Review
dokumens (id, id_proposal, file_proposal, file_persetujuan)
nilai_administratifs (id, id_proposal, id_reviewer, checklist, note_administratif)
nilai_substantifs (id, id_proposal, id_reviewer, note_substantif)
hasil_finals (id, id_proposal, status_final, catatan)

-- Master Data
fakultas (id, nama_fakultas)
prodis (id, nama_prodi, id_fakultas)
notifications (id, user_id, type, title, message, data, read_at)
```

### Relasi Database

```
Proposal → Team (1:1)
Proposal → Dokumen (1:1)
Proposal → NilaiAdministratif (1:many)
Proposal → NilaiSubstantif (1:many)
Proposal → HasilFinal (1:1)
User → Notifications (1:many)
```

## Status Proposal

### Flow Status Lengkap

```
1. submitted → Proposal baru diajukan
2. pending → Menunggu validasi dosen
3. valid → Proposal divalidasi dosen
4. tidak_valid → Proposal ditolak validasi
5. review_administratif → Sedang review administratif
6. review_substantif → Sedang review substantif
7. review_completed → Semua review selesai
8. lolos → Proposal disetujui final
9. tidak_lolos → Proposal ditolak final
```

## Fitur Utama

### 1. **Auto-fill Data Mahasiswa**

-   Input NIM → Auto-fill semua field terkait
-   Real-time validation
-   Pencegahan duplikasi data
-   Sinkronisasi antar field

### 2. **PDF Viewer Terintegrasi**

-   Tampilan PDF langsung di browser
-   Tab untuk proposal dan persetujuan
-   Loading animation
-   Responsive design

### 3. **Dashboard Real-time**

-   Update status otomatis
-   Progress tracking
-   Filter dan search
-   Export data

### 4. **Sistem Review Terstruktur**

-   Review administratif (checklist + catatan)
-   Review substantif (catatan)
-   Progress monitoring
-   Quality control

### 5. **Notifikasi Smart**

-   Role-based notifications
-   Action buttons
-   Real-time updates
-   Visual feedback

## Instalasi dan Setup



### Data Sample
Seeder menyediakan data sample untuk testing:

-   Mahasiswa: 50 user
-   Dosen: 20 user
-   Reviewer: 15 user
-   Operator: 5 user
-   Proposal: 31 sample
-   Fakultas dan Prodi: 10 fakultas, 30 prodi

## Penggunaan

### 1. **Mahasiswa**

1. Login dengan NIM dan password
2. Ajukan proposal baru
3. Monitor status proposal
4. Lihat hasil final

### 2. **Dosen**

1. Login dengan NIDN dan password
2. Validasi proposal mahasiswa
3. Lihat hasil review
4. Monitor progress

### 3. **Reviewer**

1. Login dengan NIP dan password
2. Review proposal yang diassign
3. Submit review administratif dan substantif
4. Monitor progress review

### 4. **Operator**

1. Login dengan NIP dan password
2. Monitor semua proposal
3. Assign reviewer
4. Kelola sistem

## Keamanan

### Authentication & Authorization

-   Multi-guard authentication
-   Role-based access control
-   CSRF protection
-   Session management

### File Security

-   Validasi tipe file (PDF only)
-   Validasi ukuran file (max 5MB)
-   Nama file diacak
-   Storage terpisah

### Data Validation

-   Input sanitization
-   SQL injection prevention
-   XSS protection
-   Rate limiting

## Troubleshooting

### Common Issues

1. **File upload gagal**

    - Cek permission folder storage
    - Pastikan disk space mencukupi
    - Cek php.ini upload_max_filesize

2. **PDF tidak tampil**

    - Pastikan PDF.js library terload
    - Cek CORS policy
    - Periksa path file storage

3. **Database error**
    - Cek koneksi database
    - Jalankan `php artisan migrate:fresh --seed`
    - Periksa log Laravel


## Roadmap

### Fitur Mendatang

-   Email notifications
-   Mobile app
-   API untuk integrasi eksternal
-   Dashboard analytics
-   Report generation

### Enhancement

-   Multi-language support
-   Advanced search
-   Bulk operations
-   Audit trail
-   Backup automation

## Support

Untuk bantuan teknis dan dukungan:

-   Email: support@phiprosal.com
-   Documentation: https://docs.phiprosal.com
-   Issues: https://github.com/phiprosal/issues

## License

This project is licensed under the MIT License.

---

**PHIPROSAL v1.0.0** - Sistem Pengajuan Proposal PKM Terintegrasi  
**Dibuat oleh:** Pramajaya  
**Tanggal:** {{ date('Y-m-d') }}  
**Status:** Production on Proses
