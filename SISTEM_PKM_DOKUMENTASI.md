# Dokumentasi Sistem PKM Proposal — Cara Kerja Lengkap

> **Versi:** 2025/2026 · **Framework:** Laravel 11 · **Database:** PostgreSQL · **Storage:** Local / Supabase

---

## 1. Gambaran Umum

Sistem PKM Proposal adalah platform web untuk mengelola pengajuan proposal Program Kreativitas Mahasiswa (PKM) di lingkungan universitas. Sistem mengotomasi seluruh alur dari pendaftaran proposal oleh mahasiswa hingga penetapan hasil final oleh Pimpinan PT.

```
Mahasiswa → Dosen Pendamping → Operator → Reviewer → Operator → Dosen Univ → Pimpinan PT
  (submit)     (validasi)     (assign)   (review)   (semi-final) (validasi)   (hasil final)
```

---

## 2. Arsitektur Teknis

### Stack
| Komponen | Teknologi |
|---|---|
| Framework | Laravel 11 (PHP 8.2+) |
| Database | PostgreSQL |
| Frontend | Blade + Vanilla CSS + Vanilla JS |
| File Storage | Local disk (fallback) / Supabase Storage |
| Notifikasi Realtime | Firebase Firestore (opsional) |
| Build Tool | Vite |

### Struktur Direktori Kunci
```
app/
  Http/
    Controllers/       — 17 controller (1 per role/fitur)
    Middleware/        — CheckActivePhase, CheckUserType
  Models/              — 16 model Eloquent
  Helpers/
    ProposalHelper.php — Konfigurasi kriteria penilaian per skim
    StorageHelper.php  — Abstraksi file storage
    TahunAjaranHelper.php — Tahun ajaran aktif
routes/web.php         — 296 baris, semua route berbasis role
resources/views/       — Blade templates per role
```

---

## 3. Model Data

### 3.1 Tabel `users` — Satu Tabel untuk Semua Role

Seluruh pengguna disimpan dalam **satu tabel** dengan kolom `role` untuk membedakan jenis pengguna.

```
users
├── id
├── identifier    — NIM (mahasiswa), NIDN (dosen), NIP (reviewer/operator/pimpinan)
├── name
├── email
├── password
├── role          — mahasiswa | dosen | reviewer | operator | pimpinan_pt
├── is_active     — boolean (operator bisa nonaktifkan akun)
└── metadata      — JSONB — data spesifik per role:
    ├── [mahasiswa]  prodi_id, fakultas_id, team_id, is_ketua, id_dosen_pembimbing
    └── [dosen]      nuptk, gelar_depan, gelar_belakang
```

**Mengapa satu tabel?** Menyederhanakan query relasi, otentikasi, dan manajemen akun oleh operator.

### 3.2 Tabel `proposals` — Inti Sistem

```
proposals
├── id_proposal        (PK)
├── judul_proposal
├── skim               — RE, RSH, PM, PI, KC, K, KI, VGK, AI, GFT
├── status             — (lihat Status Flow di bawah)
├── status_validasi    — pending | valid | tidak_valid
├── status_final       — Didanai / Tidak Didanai / dll
├── tahun_ajaran
│
├── id_mahasiswa       — FK → users (ketua tim)
├── id_dosen           — FK → users (dosen pembimbing)
├── id_dosen_pendamping_universitas  — FK → users (dosen univ, diisi di Fase 3)
│
├── id_reviewer_administratif        — FK → users
├── id_reviewer_substantif_1         — FK → users (Fase 2)
├── id_reviewer_substantif_2         — FK → users (Fase 2)
├── id_reviewer_substantif_seleksi_1 — FK → users (Fase 3)
└── id_reviewer_substantif_seleksi_2 — FK → users (Fase 3)
```

**Anggota tim** tidak disimpan di `proposals`, melainkan diidentifikasi dari kolom `metadata->team_id` di tabel `users`.

### 3.3 Tabel Penilaian

| Tabel | Kegunaan |
|---|---|
| `nilai_administratifs` | Skor review administratif (Fase 2) |
| `nilai_substantifs` | Skor review substantif (Fase 2 & 3), kolom `jenis_review`: `pertama` / `seleksi` |
| `hasil_semi_finals` | Hasil semi-final tingkat universitas (Fase 3) |
| `hasil_finals` | Hasil final dari Pimpinan PT (Fase 4) |
| `proposal_revisis` | File revisi yang diupload mahasiswa |

### 3.4 Tabel `ruang_kontrols` — Pengatur Fase

```
ruang_kontrols
├── id_ruang_kontrol
├── tahun_ajaran
├── is_active
├── status_pendaftaran    — terbuka | tertutup
├── status_review         — terbuka | tertutup
├── status_perbaikan      — terbuka | tertutup
├── status_penilaian_akhir — terbuka | tertutup
├── tanggal_*_mulai / *_selesai  — Range tanggal tiap fase
└── dana_min/max_operator/belmawa — Batas dana proposal
```

**Hanya satu fase yang bisa `terbuka` pada satu waktu** (mutual exclusive). Fase dikontrol manual oleh operator via "Ruang Kontrol".

---

## 4. Alur 4 Fase Sistem

### FASE 1 — Pengajuan Proposal (`pendaftaran`)

```
Mahasiswa
 1. Isi form proposal (judul, skim, anggota tim, data dosen pembimbing)
 2. Upload file proposal PDF
 3. Submit → status: submitted

Ketua Tim: role=mahasiswa, metadata.is_ketua=true
Anggota  : role=mahasiswa, metadata.team_id = sama dengan ketua
```

**Route kunci:** `POST /mahasiswa/proposal/store` → `ProposalController@store`  
**Middleware:** `check.phase:pendaftaran` — blokir jika fase tidak aktif

---

### FASE 2 — Review (`review`)

```
Operator
 1. Buka halaman "Pilih Reviewer"
 2. Assign 1 reviewer administratif + 2 reviewer substantif ke setiap proposal
    → proposal.id_reviewer_administratif = X
    → proposal.id_reviewer_substantif_1 = Y
    → proposal.id_reviewer_substantif_2 = Z
    → proposal.status = review_administratif

Reviewer Administratif
 3. Review kelengkapan berkas
 4. Submit penilaian → NilaiAdministratif record dibuat
    → proposal.status = review_substantif

Reviewer Substantif (1 & 2)
 5. Buka halaman detail proposal (lihat PDF, baca keterangan)
 6. Isi skor per kriteria (sesuai skim) via form interaktif
 7. Submit → NilaiSubstantif record (jenis_review='pertama')
    → Setelah KEDUA reviewer submit: proposal.status = hasil_semi_final
```

**Kriteria penilaian** dikonfigurasi di `ProposalHelper::getSubstantifCriteria($skim)` — berbeda untuk setiap skim PKM.

---

### FASE 3 — Revisi & Seleksi (`perbaikan`)

```
Mahasiswa
 1. Upload revisi proposal → ProposalRevisi record
    → proposal.status = sudah_revisi (opsional, tergantung alur)

Operator
 2. Buka "Hasil Semi Final" — lihat skor rata-rata dari 2 reviewer
 3. Tetapkan hasil: Lolos / Tidak Lolos Tingkat Universitas
 4. Jika Lolos: assign Dosen Pendamping Universitas
    → hasil_semi_finals record dibuat
    → proposal.id_dosen_pendamping_universitas = X
    → proposal.status = review_substantif_seleksi

 5. Assign 2 Reviewer Seleksi
    → proposal.id_reviewer_substantif_seleksi_1 = A
    → proposal.id_reviewer_substantif_seleksi_2 = B

Reviewer Seleksi (1 & 2)
 6. Akses halaman: /reviewer/proposal/{id}/detail-substantif?seleksi=1
    (parameter ?seleksi=1 mengaktifkan mode seleksi)
 7. Isi skor penilaian tingkat universitas
 8. Submit → NilaiSubstantif record (jenis_review='seleksi')
    → Setelah KEDUA reviewer seleksi submit:
       proposal.status = validasi_akhir_dosen

Dosen Pendamping Universitas
 9. Akses halaman validasi akhir
10. Validasi proposal → proposal.status = validasi_akhir_final
```

**Catatan penting:** URL parameter `?seleksi=1` membedakan antara reviewer substantif biasa dan reviewer seleksi, baik di controller maupun di blade template.

---

### FASE 4 — Penilaian Akhir (`penilaian_akhir`)

```
Mahasiswa
 1. Upload "Revisi Akhir" proposal
    Route: POST /mahasiswa/proposal/{id}/revisi-akhir
    → middleware check.phase:penilaian_akhir

Pimpinan PT
 2. Dashboard menampilkan proposal yang lolos seleksi
 3. Tetapkan hasil final:
    - status_pimnas: lolos / tidak_lolos
    - status_pendanaan: didanai / tidak_didanai
    - dana_yang_didapatkan: nominal dana
    → hasil_finals record dibuat
    → proposal.status = hasil_final

Operator (opsional)
 4. Juga bisa akses hasil final via /operator/hasil-final
 5. Set dana_yang_dapat_diberikan (anggaran dari universitas)
```

---

## 5. Mekanisme Kontrol Akses

### 5.1 Middleware Auth + Role

Setiap grup route dilindungi dua lapis middleware:

```php
// Contoh untuk reviewer
Route::middleware(['auth', 'role:reviewer'])->group(function () {
    ...
});

// Operator DAN Pimpinan PT berbagi beberapa route
Route::middleware(['auth', 'role:operator,pimpinan_pt'])->group(function () {
    ...
});
```

### 5.2 Middleware `check.phase`

Banyak route punya middleware tambahan yang memastikan aksi hanya bisa dilakukan di fase yang tepat:

```php
// Hanya bisa submit proposal saat fase pendaftaran terbuka
Route::post('/mahasiswa/proposal/store', ...)->middleware('check.phase:pendaftaran');

// Hanya bisa review saat fase review terbuka
Route::post('/reviewer/.../submit-review-substantif', ...)->middleware('check.phase:review');
```

**`CheckActivePhase`** middleware cek `ruang_kontrols.status_{fase} = 'terbuka'` untuk tahun ajaran aktif. Jika fase tertutup, redirect dengan pesan error.

### 5.3 Otorisasi Level Controller

Untuk halaman review substantif seleksi, controller `ReviewerController@detailProposalSubstantif` lakukan pengecekan tambahan:

```php
$isSeleksiMode = request()->get('seleksi') == '1';

if ($isSeleksiMode) {
    // Cek di kolom reviewer SELEKSI
    $isSubstantif = $proposal->id_reviewer_substantif_seleksi_1 == $reviewer->id
                 || $proposal->id_reviewer_substantif_seleksi_2 == $reviewer->id;
} else {
    // Cek di kolom reviewer BIASA
    $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id
                 || $proposal->id_reviewer_substantif_2 == $reviewer->id;
}

if (!$isSubstantif) abort(403);
```

---

## 6. Status Proposal (State Machine)

```
submitted
   ↓ [dosen pendamping validasi]
valid / tidak_valid
   ↓ [operator assign reviewer]
review_administratif
   ↓ [reviewer admin submit]
review_substantif
   ↓ [kedua reviewer submit]
hasil_semi_final
   ↓ [operator tetapkan lolos + assign reviewer seleksi]
review_substantif_seleksi
   ↓ [kedua reviewer seleksi submit]
validasi_akhir_dosen
   ↓ [dosen universitas validasi]
validasi_akhir_final
   ↓ [pimpinan PT tetapkan hasil]
hasil_final
```

---

## 7. Pengelolaan File

### Lokasi Penyimpanan
- **Proposal PDF:** `storage/app/public/proposals/`
- **Revisi:** `storage/app/public/revisi/`
- **Review Dosen:** `storage/app/public/review_dosen/`

### Akses File
```php
// Route dengan auth guard untuk serve file
Route::get('/file/serve', function (Request $request) {
    $path = $request->query('path');
    return StorageHelper::response($path, basename($path));
})->middleware('auth');
```

File tidak bisa diakses langsung via URL publik — harus melalui route `/file/serve?path=...` yang mengecek autentikasi.

---

## 8. Notifikasi

Sistem mendukung dua channel notifikasi:

| Channel | Kondisi |
|---|---|
| **Database** (`notifications` table) | Selalu aktif |
| **Firebase Firestore** (realtime) | Aktif jika package `google/cloud-firestore` terinstall dan kredensial Firebase dikonfigurasi |

**`NotificationController`** menangani GET notifikasi dan mark-as-read, tersedia untuk semua role.

---

## 9. Form Penilaian (Kriteria Review)

Kriteria penilaian per skim dikonfigurasi di `ProposalHelper::getSubstantifCriteria($skim)`. Setiap skim (RE, RSH, PM, PI, dst.) punya set kriteria berbeda, di mana setiap kriteria memiliki:
- `nama` — Label kriteria
- `bobot` — Bobot persentase
- `max_skor` — Skor maksimum per kriteria

Skor disimpan di `nilai_substantifs.skor_per_kriteria` dalam format JSONB:
```json
{"1": 8, "2": 7, "3": 9, "4": 8, "5": 7}
```

Operator bisa membuat/edit form penilaian via CRUD di `FormPenilaian`.

---

## 10. Manajemen Akun

**Semua pembuatan akun dilakukan oleh Operator** (registrasi publik dinonaktifkan).

Via `/operator/akun`, operator bisa:
- Buat / edit / hapus akun: mahasiswa, dosen, reviewer, operator
- Bulk delete mahasiswa

**Pimpinan PT** juga punya akses manajemen akun via `/pimpinan-pt/akun`.

Reset password tersedia via `/forgot-password` (OTP ke email).

---

## 11. Laporan SIMBELMAWA

Operator bisa buat laporan format SIMBELMAWA (Sistem Informasi Belmawa Dikti) via:
```
GET  /operator/laporan-simbelmawa
POST /operator/laporan-simbelmawa
PUT  /operator/laporan-simbelmawa/{id}
```

---

## 12. Konfigurasi Penting (.env)

```env
DB_CONNECTION=pgsql
DB_HOST=...
DB_DATABASE=...

# Firebase (opsional, untuk notifikasi realtime)
FIREBASE_CREDENTIALS=...

# Storage
FILESYSTEM_DISK=public   # atau 'supabase'
SUPABASE_URL=...
SUPABASE_KEY=...
```

---

## 13. Ringkasan Hak Akses per Role

| Halaman / Aksi | Mahasiswa | Dosen | Reviewer | Operator | Pimpinan PT |
|---|:---:|:---:|:---:|:---:|:---:|
| Submit proposal | ✅ Fase 1 | | | | |
| Validasi proposal | | ✅ | | | |
| Assign reviewer | | | | ✅ Fase 2 | |
| Review administratif | | | ✅ Fase 2 | | |
| Review substantif | | | ✅ Fase 2 | | |
| Upload revisi | ✅ Fase 3 | | | | |
| Hasil semi final | | | | ✅ Fase 3 | |
| Assign reviewer seleksi | | | | ✅ Fase 3 | |
| Review seleksi | | | ✅ Fase 3 | | |
| Validasi akhir (univ) | | ✅ Dosen Univ | | | |
| Upload revisi akhir | ✅ Fase 4 | | | | |
| Hasil final | | | | ✅ Fase 4 | ✅ Fase 4 |
| Manajemen akun | | | | ✅ | ✅ |
| Ruang Kontrol | | | | ✅ | |
| Form penilaian | | | | ✅ | |
| Laporan SIMBELMAWA | | | | ✅ | |

