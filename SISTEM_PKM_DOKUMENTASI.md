# Dokumentasi Sistem PKM Proposal — Cara Kerja Lengkap

> **Versi:** 2025/2026 · **Framework:** Laravel 12 · **Database:** Supabase PostgreSQL · **Storage:** Supabase Storage (S3)

---

## 1. Gambaran Umum

Sistem PKM Proposal adalah platform web untuk mengelola pengajuan proposal Program Kreativitas Mahasiswa (PKM). Sistem mengotomasi seluruh alur dari pendaftaran, review bertahap, seleksi, hingga penetapan hasil final oleh Pimpinan PT.

```
Mahasiswa → Dosen Pembimbing → Operator → Reviewer → Operator (Semi-Final) → Dosen Univ → Pimpinan PT
 (submit)      (validasi 1)    (assign)    (review)       (seleksi)           (validasi)  (hasil final)
```

**Terdapat 4 Fase utama** yang dikendalikan oleh Ruang Kontrol:

| Fase | Nama | Cakupan Proses |
|---|---|---|
| **Fase 1** | Pengajuan Proposal (`pendaftaran`) | Submit, validasi dosen, input pendanaan |
| **Fase 2** | Review (`review`) | Assign reviewer, review administratif & substantif pertama |
| **Fase 3** | Revisi & Seleksi (`perbaikan`) | Revisi mahasiswa, validasi 2 dosen, reviewer seleksi, semi-final, validasi dosen univ |
| **Fase 4** | Penilaian Akhir (`penilaian_akhir`) | Revisi akhir mahasiswa, penilaian final Pimpinan PT |

**Tugas di luar fase** (dapat dilakukan kapan saja):
- Manajemen CRUD akun oleh Operator / Pimpinan PT
- Pembuatan & pengelolaan Form Penilaian dinamis
- Kelola Laporan SIMBELMAWA & Generate Laporan

---

## 2. Arsitektur Teknis

### Stack

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | Supabase PostgreSQL |
| Frontend | Blade + Vanilla CSS + Vanilla JS |
| File Storage | Supabase Storage (S3-compatible) / Local disk fallback |
| Build Tool | Vite |
| Notifikasi | Database Channel (tabel `notifications`) |

### Struktur Direktori Kunci

```
app/
  Http/
    Controllers/
      AuthController.php
      DosenController.php              — Validasi 1 & 2 dosen pembimbing
      DosenPendampingController.php    — Validasi akhir dosen universitas
      FormPenilaianController.php      — CRUD form penilaian dinamis
      NotificationController.php       — Notifikasi semua role
      PimpinanPTController.php         — Hasil final, manajemen akun
      ProfileController.php
      SimbelmawaReportController.php   — Laporan SIMBELMAWA
      Mahasiswa/                       — Controller khusus mahasiswa
      Operator/
        AccountManagementController.php  — CRUD akun
        DashboardController.php
        HasilController.php              — Semi-final & keputusan lolos
        ReviewerAssignmentController.php — Assign reviewer
        RuangKontrolController.php       — Kelola fase & jadwal
      Reviewer/                        — Controller review admin & substantif
    Middleware/
      CheckActivePhase.php             — Cek fase aktif
  Models/                              — 16 model Eloquent (basis: tabel users tunggal)
  Helpers/
    NumberFormatHelper.php    — Format angka (titik sebagai pemisah ribuan)
    ProposalHelper.php        — Konfigurasi kriteria penilaian per skim
    RuangKontrolHelper.php    — Pengecekan fase ruang kontrol
    StorageHelper.php         — Abstraksi file storage (Supabase/Local)
    TahunAjaranHelper.php     — Tahun ajaran aktif
    UserHelper.php            — Utilitas terkait user
routes/web.php                — Semua route berbasis role
resources/views/              — Blade templates per role
```

---

## 3. Model Data

### 3.1 Tabel `users` — Unified Table Semua Role

Semua pengguna disimpan dalam **satu tabel** dengan kolom `role` sebagai pembeda.

```
users
├── id
├── identifier    — NIM (mahasiswa) | NIDN (dosen) | NIP (reviewer/operator/pimpinan_pt)
├── name
├── email
├── phone
├── password
├── role          — mahasiswa | dosen | reviewer | operator | pimpinan_pt
├── is_active     — boolean
└── metadata      — JSONB, data spesifik per role:
    ├── [mahasiswa]  prodi_id, prodi_name, fakultas_id, fakultas_name,
    │                team_id, is_ketua, id_dosen_pembimbing
    └── [dosen]      nuptk, gelar_depan, gelar_belakang, bidang_keahlian
```

**Aturan penting:**
- Dosen pembimbing **maksimal menampung 10 mahasiswa** bimbingan.
- Dosen Universitas yang di-assign pada Semi-Final **tidak boleh** merupakan dosen pembimbing asli mahasiswa tersebut.
- Akun dibuat oleh Operator; **registrasi publik dinonaktifkan**.

### 3.2 Tabel `proposals` — Inti Sistem

```
proposals
├── id_proposal          (PK)
├── judul_proposal
├── skim                 — RE | RSH | PM | PI | KC | K | KI | VGK | AI | GFT
├── status               — (lihat State Machine di bawah)
├── status_validasi      — pending | valid | tidak_valid     (Validasi 1 Dosen)
├── status_validasi_2    — pending | valid | tidak_valid     (Validasi 2 Dosen)
├── tahun_ajaran
│
├── dana_diajukan_belmawa    — Dana yang diminta ke Belmawa (Kemendiktisaintek)
│                              Range: Rp 0 – Rp 8.000.000
├── dana_diajukan_operator   — Dana yang diminta ke Universitas
│                              Range: Rp 0 – Rp 2.000.000
│
├── id_mahasiswa             — FK → users (ketua tim)
├── id_dosen                 — FK → users (dosen pembimbing)
├── id_dosen_pendamping_universitas  — FK → users (diisi saat semi-final lolos)
├── team_id                  — UUID untuk mengidentifikasi anggota tim
│
├── id_reviewer_administratif        — FK → users (Fase 2)
├── id_reviewer_substantif_1         — FK → users (Fase 2, reviewer berbeda)
├── id_reviewer_substantif_2         — FK → users (Fase 2, reviewer berbeda)
├── id_reviewer_substantif_seleksi_1 — FK → users (Fase 3)
└── id_reviewer_substantif_seleksi_2 — FK → users (Fase 3)
```

**Catatan pendanaan:** Mahasiswa mengajukan **dua** nominal dana secara terpisah saat submit:
- `dana_diajukan_belmawa`: untuk Kemendiktisaintek, **Rp 0 – Rp 8.000.000**
- `dana_diajukan_operator`: untuk Universitas, **Rp 0 – Rp 2.000.000**

Batas min/max setiap dana dikonfigurasi per-tahun-ajaran di `ruang_kontrols`.

### 3.3 Tabel Penilaian

| Tabel | Kegunaan |
|---|---|
| `nilai_administratifs` | Checklist & catatan review administratif (Fase 2) |
| `nilai_substantifs` | Catatan/skor review substantif; `jenis_review`: `pertama` (Fase 2) / `seleksi` (Fase 3) |
| `hasil_semi_finals` | Keputusan operator + nilai + data dosen universitas (Fase 3) |
| `hasil_finals` | Penilaian final Pimpinan PT: status PIMNAS, pendanaan, dana yang ditetapkan (Fase 4) |
| `proposal_revisi` | File revisi mahasiswa; `jenis_revisi`: `revisi_biasa` / `revisi_akhir` |

**Perbedaan Review Pertama vs Seleksi:**

| Aspek | Review Pertama (`jenis_review = 'pertama'`) | Review Seleksi (`jenis_review = 'seleksi'`) |
|---|---|---|
| Penilaian | Catatan saja | Catatan + skor per kriteria (0-10) |
| Reviewer | `id_reviewer_substantif_1/2` | `id_reviewer_substantif_seleksi_1/2` |
| Tujuan | Identifikasi kelemahan proposal | Seleksi untuk ke tingkat nasional |

### 3.4 Tabel `ruang_kontrols` — Pengatur Fase

```
ruang_kontrols
├── id_ruang_kontrol
├── tahun_ajaran
├── is_active
├── nama_history
│
├── status_pendaftaran      — terbuka | tertutup
├── tanggal_pendaftaran_mulai / tanggal_pendaftaran_selesai
│
├── status_review           — terbuka | tertutup
├── tanggal_review_mulai / tanggal_review_selesai
│
├── status_perbaikan        — terbuka | tertutup
├── tanggal_perbaikan_mulai / tanggal_perbaikan_selesai
│
├── status_penilaian_akhir  — terbuka | tertutup
├── tanggal_penilaian_akhir_mulai / tanggal_penilaian_akhir_selesai
│
├── dana_min_operator / dana_max_operator   — Batas dana universitas
└── dana_min_belmawa / dana_max_belmawa     — Batas dana Belmawa
```

**Hanya satu fase yang bisa `terbuka` pada satu waktu.** Fase dikontrol manual oleh Operator.

### 3.5 Tabel `form_penilaian` — Form Dinamis

```
form_penilaian
├── id
├── nama_form
├── jenis_form   — administratif | substantif
├── skim         — nullable (null = berlaku untuk semua skim)
├── config       — JSONB (struktur kriteria, bobot, checklist)
├── is_active
├── tahun_ajaran
└── created_by   — FK → users (operator yang membuat)
```

### 3.6 Tabel `simbelmawa_reports` — Laporan SIMBELMAWA

```
simbelmawa_reports
├── id
├── tahun_ajaran
├── id_ruang_kontrol
├── jumlah_proposal_tervalidasi_pimpinan_pt
├── jumlah_proposal_dapat_pendanaan
├── total_dana_pendanaan
├── jumlah_proposal_lolos_pimnas
├── judul_proposal_lolos_pimnas   — JSON array
├── jumlah_prestasi
├── prestasi                      — JSON array
└── created_by                    — FK → users
```

---

## 4. Alur Lengkap Sistem (27 Langkah)

### Pra-Sistem: Setup oleh Operator / Pimpinan PT *(Di luar fase, kapan saja)*

**[Langkah 1]** Operator atau Pimpinan PT membuka **Ruang Kontrol** dan mengatur:
- Jadwal setiap fase (tanggal mulai & selesai)
- Batas dana yang dapat diajukan (min/max untuk Belmawa & Universitas)
- Mengaktifkan Fase 1 (`status_pendaftaran = 'terbuka'`)

**[Langkah 2]** Operator membuat **Form Penilaian Dinamis** untuk digunakan reviewer (administratif & substantif) melalui menu CRUD Form Penilaian.

---

### FASE 1 — Pengajuan Proposal (`pendaftaran`)

**[Langkah 3 — Mahasiswa]** Mahasiswa mencari akunnya melalui halaman `/credentials/search` dengan memasukkan NIM. Sistem mengirimkan OTP ke email Google-nya. Mahasiswa mengikuti alur verifikasi dan membuat password baru.

**[Langkah 4 — Mahasiswa]** Setelah login, mahasiswa melihat dashboard yang menampilkan **jadwal pengajuan proposal** (status fase 1 terbuka/tertutup).

**[Langkah 5 — Mahasiswa]** Saat Fase 1 terbuka, mahasiswa mengajukan proposal:
- Isi judul, skim, data tim (ketua + maks 4 anggota)
- Upload file proposal PDF
- Input **dua nominal dana** secara terpisah:
  - Dana Belmawa: Rp 0 – Rp 8.000.000
  - Dana Universitas: Rp 0 – Rp 2.000.000
- Submit → `status: submitted`, `status_validasi: pending`
- Notifikasi dikirim ke dosen pembimbing

**[Langkah 6 — Dosen Pembimbing]** Dosen menerima notifikasi dan melakukan validasi pertama:
- Lihat PDF proposal mahasiswa
- Pilih **Valid** atau **Tidak Valid** (dengan catatan)
- Satu dosen **maksimal membimbing 10 mahasiswa**
- Jika Tidak Valid → proposal dikembalikan ke mahasiswa untuk diajukan ulang
- Jika Valid → `status_validasi: valid`

**[Langkah 7 — Dosen Pembimbing]** Setelah proposal valid, proposal otomatis masuk ke antrian operator. Dosen dapat memantau status proposal mahasiswanya.

---

### FASE 2 — Review (`review`)

**[Langkah 8 — Operator]** Operator membuka menu **Assign Reviewer** dan menugaskan 3 reviewer per proposal:
- **1 Reviewer Administratif** → `id_reviewer_administratif`
- **2 Reviewer Substantif** → `id_reviewer_substantif_1` & `id_reviewer_substantif_2`
  - *Catatan: Kedua reviewer substantif tidak boleh sama satu sama lain.*
- Status berubah → `review_administratif`
- Notifikasi dikirim ke ketiga reviewer

**[Langkah 9 — Reviewer]** Reviewer menerima notifikasi dan melihat tugasnya di **beranda**. Reviewer melakukan review sesuai tugasnya:
- **Reviewer Administratif:** Mengisi checklist dinamis per skim + catatan → simpan ke `nilai_administratifs`. Status → `review_substantif`
- **Reviewer Substantif (1 & 2):** Membaca PDF proposal, menulis **catatan substantif** (hanya catatan, tanpa skor) → simpan ke `nilai_substantifs` dengan `jenis_review = 'pertama'`

**[Langkah 10 — Sistem]** Setelah kedua reviewer substantif submit, hasil review terkirim ke mahasiswa dan dosen pembimbing. Status proposal → `revisi`.

---

### FASE 3 — Revisi & Seleksi (`perbaikan`)

**[Langkah 11 — Mahasiswa]** Mahasiswa melihat **catatan reviewer** pada halaman detail proposal.

**[Langkah 12 — Dosen Pembimbing]** Dosen dapat melihat catatan reviewer pada detail proposal yang dibimbingnya.

**[Langkah 13 — Mahasiswa]** Mahasiswa melakukan **revisi** terhadap proposalnya (upload file PDF revisi). File disimpan di `proposal_revisi` dengan `jenis_revisi = 'revisi_biasa'`. Mahasiswa meminta validasi ulang ke dosen pembimbing.

**[Langkah 14 — Dosen Pembimbing]** Dosen melakukan **Validasi Kedua** melalui menu sidebar baru (**Validasi 2**). Cara validasi sama seperti validasi pertama (lihat/tolak/setujui PDF revisi).
- Jika Tidak Valid → dikembalikan ke mahasiswa
- Jika Valid → `status_validasi_2: valid`

**[Langkah 15 — Dosen Pembimbing]** Setelah valid 2, proposal masuk ke antrian operator untuk assign reviewer seleksi.

**[Langkah 16 — Operator]** Operator melakukan **assign reviewer seleksi**:
- **2 Reviewer Substantif Seleksi** → `id_reviewer_substantif_seleksi_1` & `id_reviewer_substantif_seleksi_2`
- Status → `review_substantif_seleksi`

**[Langkah 17 — Reviewer]** Reviewer membuka menu sidebar baru: **Selective Review**. Di sini reviewer dapat melihat:
- Detail hasil review sebelumnya (kesalahan administratif & catatan substantif pertama)
- Reviewer menilai menggunakan **form substantif** (kriteria per skim, skor 0-10 per kriteria)
- Submit → simpan ke `nilai_substantifs` dengan `jenis_review = 'seleksi'`

**[Langkah 18 — Sistem]** Setelah kedua reviewer seleksi submit, skor & catatan tersimpan dan masuk ke antrian operator (menu semi-final).

**[Langkah 19 — Operator]** Operator membuka **menu Semi-Final** dan dapat melihat:
- Seluruh nilai dari semua reviewer (administratif, substantif pertama, substantif seleksi)
- Catatan dari setiap tahap review
- Membuat keputusan: **Lolos / Tidak Lolos** tingkat universitas

**[Langkah 20 — Operator]** Jika **Tidak Lolos**:
- Status proposal → `tidak_lolos_tingkat_universitas`
- Proposal selesai; mahasiswa & dosen dapat melihat status akhir

**[Langkah 21 — Operator]** Jika **Lolos**:
- Operator menentukan **Dosen Universitas** (harus dosen lain, bukan dosen pembimbing asli)
- Status proposal → `revisi_akhir`
- Data tersimpan ke `hasil_semi_finals`

**[Langkah 22 — Dosen Pembimbing]** Dosen menerima notifikasi bahwa mahasiswanya lolos dan diperintahkan untuk membimbing mahasiswa mempersiapkan proposal ke tingkat nasional.

**[Langkah 23 — Mahasiswa]** Mahasiswa melihat pada detail proposal bahwa proposalnya **lolos tingkat universitas**. Mahasiswa melihat kontak Dosen Universitas yang ditugaskan, kemudian **mengumpulkan proposal akhir** (upload revisi akhir). File disimpan dengan `jenis_revisi = 'revisi_akhir'`. Status → `validasi_akhir_dosen_univ`.

**[Langkah 24a — Dosen Universitas]** Dosen Universitas menerima notifikasi dan melakukan **validasi akhir**:
- Jika **Tidak Valid** → status kembali ke `revisi_akhir`, mahasiswa upload ulang
- Jika **Valid** → status → `pimpinan_pt`

---

### FASE 4 — Penilaian Akhir (`penilaian_akhir`)

**[Langkah 24b — Mahasiswa]** Mahasiswa dapat melihat status terbaru pada halaman detail proposal (apakah lolos validasi dosen universitas atau dikembalikan).

**[Langkah 25 — Sistem]** Proposal yang lolos validasi dosen universitas masuk ke dashboard Pimpinan PT (`status = 'pimpinan_pt'`).

**[Langkah 26 — Pimpinan PT]** Pimpinan PT membuka menu **Hasil Final** dan mengisi:
- **Form Substantif Final** (penilaian dengan kriteria sesuai skim)
- **Keputusan Pendanaan** — Pimpinan PT dapat mengubah nominal yang diajukan mahasiswa:
  - Dana dari Belmawa (Kemendiktisaintek)
  - Dana dari Universitas
- **Status PIMNAS**: Lolos / Tidak Lolos
- **Catatan Final**
- Submit → simpan ke `hasil_finals`. Status proposal berubah ke status final.
- Notifikasi terkirim lengkap ke mahasiswa dan dosen pembimbing.

**[Langkah 27 — Operator / Pimpinan PT]** *(Di luar fase, kapan saja)*
- Pimpinan PT mengelola **Laporan SIMBELMAWA** (CRUD data laporan ke Dikti)
- **Generate Laporan** (statistik proposal, pendanaan, PIMNAS, prestasi)
- Melihat data keseluruhan proposal universitas

---

## 5. State Machine Status Proposal

```
submitted
   ↓ [Dosen Pembimbing: validasi 1]
valid / tidak_valid (kembali ke mahasiswa jika tidak valid)
   ↓ [Operator: assign reviewer]
review_administratif
   ↓ [Reviewer Administratif: submit checklist]
review_substantif
   ↓ [Kedua Reviewer Substantif: submit catatan]
revisi
   ↓ [Mahasiswa upload revisi → Dosen: validasi 2]
validasi_2_valid / tidak_valid (kembali ke mahasiswa jika tidak valid)
   ↓ [Operator: assign reviewer seleksi]
review_substantif_seleksi
   ↓ [Kedua Reviewer Seleksi: submit skor]
hasil_semi_final (antrian operator)
   ↓ [Operator: keputusan semi-final]
tidak_lolos_tingkat_universitas (END)
   ──atau──
   ↓ [Lolos: assign Dosen Univ]
revisi_akhir
   ↓ [Mahasiswa: upload revisi akhir]
validasi_akhir_dosen_univ
   ↓ [Dosen Universitas: validasi akhir]
revisi_akhir (jika tidak valid, kembali loop)
   ──atau──
   ↓ [Valid]
pimpinan_pt
   ↓ [Pimpinan PT: penilaian final]
lolos_pimnas_pendanaan /
lolos_pimnas_tidak_pendanaan /
tidak_lolos_pimnas_lolos_pendanaan /
tidak_lolos (END)
```

---

## 6. Mekanisme Kontrol Akses

### 6.1 Middleware Auth + Role

```php
// Contoh grup route reviewer
Route::middleware(['auth', 'role:reviewer'])->group(function () { ... });

// Operator DAN Pimpinan PT berbagi beberapa menu
Route::middleware(['auth', 'role:operator,pimpinan_pt'])->group(function () { ... });
```

### 6.2 Middleware `check.phase`

Route terikat fase hanya dapat diakses saat fase yang sesuai `terbuka`:

```php
// Submit proposal hanya saat Fase 1 terbuka
Route::post('/mahasiswa/proposal/store', ...)->middleware('check.phase:pendaftaran');

// Upload revisi hanya saat Fase 3 terbuka
Route::post('/mahasiswa/proposal/{id}/revisi', ...)->middleware('check.phase:perbaikan');
```

`CheckActivePhase` memeriksa `ruang_kontrols.status_{fase} = 'terbuka'` untuk tahun ajaran aktif. Jika fase tertutup, redirect dengan pesan error.

---

## 7. Pengelolaan File

### Lokasi Penyimpanan

| Jenis File | Path |
|---|---|
| Proposal utama (PDF) | `proposals/` |
| Revisi biasa | `proposals/revisi_biasa/` |
| Revisi akhir | `proposals/revisi_akhir/` |
| File review dari Dosen Univ | `proposals/review_akhir/` |

Semua file diakses melalui `StorageHelper` yang secara otomatis memilih antara Supabase Storage atau Local Disk.

### Akses File

```php
// Route dengan auth guard untuk serve file
Route::get('/file/serve', function (Request $request) {
    $path = $request->query('path');
    return StorageHelper::response($path, basename($path));
})->middleware('auth');
```

File **tidak dapat** diakses langsung via URL publik — harus melalui route `/file/serve?path=...`.

---

## 8. Notifikasi

Sistem menggunakan **Laravel Database Notifications** (tabel `notifications`).

| Event | Penerima |
|---|---|
| Proposal di-submit | Dosen Pembimbing |
| Proposal valid/tidak valid (validasi 1) | Mahasiswa (semua anggota tim) |
| Reviewer di-assign | Reviewer |
| Review selesai (administratif + substantif pertama) | Mahasiswa + Dosen Pembimbing |
| Proposal lolos semi-final | Mahasiswa + Dosen Pembimbing |
| Proposal tidak lolos semi-final | Mahasiswa + Dosen Pembimbing |
| Validasi dosen universitas selesai | Mahasiswa |
| Hasil final Pimpinan PT | Mahasiswa + Dosen Pembimbing |

`NotificationController` menangani GET notifikasi dan *mark-as-read*, tersedia untuk semua role.

---

## 9. Sistem Penilaian

### Review Administratif
- Checklist dinamis berdasarkan skim (dikonfigurasi di `ProposalHelper`)
- Reviewer mencentang **item yang bermasalah** beserta catatan
- Data: `nilai_administratifs.checklist` (JSON)

### Review Substantif
- Kriteria dan bobot dikonfigurasi di `ProposalHelper::getSubstantifCriteria($skim)`
- Skor: **0-10** per kriteria (dapat desimal, step 0.1)
- Rumus nilai akhir:
  ```
  Total Nilai = Σ (Bobot × Skor) semua kriteria  → range: 0 – 1.000
  Nilai Akhir = Total Nilai / 10                  → range: 0,00 – 100,00
  ```
- Data: `nilai_substantifs.skor_per_kriteria` (JSONB), `total_nilai`, `nilai_akhir`

### Form Penilaian Dinamis (CRUD)
- Operator membuat form via menu CRUD Form Penilaian (di luar fase, kapan saja)
- Form bisa berbeda per skim dan per tahun ajaran
- Digunakan oleh: Reviewer Seleksi, Operator (semi-final), dan Pimpinan PT (final)

---

## 10. Manajemen Akun

**Semua pembuatan akun dilakukan oleh Operator** (registrasi publik dinonaktifkan).

| Role | Dibuat oleh | Cara Aktivasi |
|---|---|---|
| Mahasiswa | Operator | Cari akun di `/credentials/search` dengan NIM → OTP ke email |
| Dosen | Operator | Cari akun dengan NIDN/NUPTK → OTP ke email |
| Reviewer | Operator | Cari akun dengan NIP → OTP ke email |
| Operator | Pimpinan PT | Dibuat langsung oleh Pimpinan PT |
| Pimpinan PT | — | Dibuat via seeder/manual |

Via `/operator/akun` (Operator) atau `/pimpinan-pt/akun` (Pimpinan PT):
- Buat / edit / nonaktifkan / hapus akun
- Pimpinan PT memiliki akses CRUD untuk **semua jenis akun** termasuk operator

---

## 11. Konfigurasi Penting (.env)

```env
APP_NAME=PengajuanProposal
APP_URL=http://localhost

# Database (Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=...
DB_DATABASE=...

# Storage (Supabase S3-compatible)
FILESYSTEM_DISK=supabase
SUPABASE_URL=...
SUPABASE_KEY=...
SUPABASE_STORAGE_BUCKET=proposals
SUPABASE_STORAGE_ENDPOINT=...
SUPABASE_STORAGE_REGION=...

# Email (untuk OTP aktivasi akun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Sistem Pengajuan Proposal PKM"
```

---

## 12. Hak Akses per Role

| Fitur / Aksi | Mahasiswa | Dosen | Reviewer | Operator | Pimpinan PT |
|---|:---:|:---:|:---:|:---:|:---:|
| Aktivasi akun mandiri (OTP) | ✅ | ✅ | ✅ | — | — |
| Lihat jadwal & dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |
| Submit proposal (Fase 1) | ✅ | | | | |
| Input 2 nominal dana (Belmawa + Univ) | ✅ | | | | |
| Validasi 1 proposal | | ✅ | | | |
| Assign reviewer (Fase 2) | | | | ✅ | |
| Review administratif (Fase 2) | | | ✅ | | |
| Review substantif pertama (Fase 2) | | | ✅ | | |
| Upload revisi (Fase 3) | ✅ | | | | |
| Validasi 2 proposal (Fase 3) | | ✅ | | | |
| Assign reviewer seleksi (Fase 3) | | | | ✅ | |
| Review seleksi / substantif (Fase 3) | | | ✅ | | |
| Semi-final & assign Dosen Univ (Fase 3) | | | | ✅ | |
| Validasi akhir (Dosen Univ, Fase 3) | | ✅ Dosen Univ | | | |
| Upload revisi akhir (Fase 4) | ✅ | | | | |
| Penilaian & penetapan hasil final (Fase 4) | | | | | ✅ |
| Ruang Kontrol (jadwal & dana) | | | | ✅ | |
| CRUD Form Penilaian | | | | ✅ | ✅ |
| Laporan SIMBELMAWA & Generate Laporan | | | | ✅ | ✅ |
| CRUD Manajemen Akun | | | | ✅ (terbatas) | ✅ (penuh) |
