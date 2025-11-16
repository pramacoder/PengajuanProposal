# 📄 Flow Dokumen Proposal PKM: Dari Upload Hingga Akses Multi-User

## 🎯 Overview

Dokumen ini menjelaskan alur lengkap dokumen proposal PDF dari upload oleh mahasiswa hingga diakses oleh berbagai role pengguna (Dosen, Reviewer, Operator) dalam sistem pengajuan proposal PKM.

---

## 🔄 ALUR LENGKAP DOKUMEN PROPOSAL

### **FASE 1: UPLOAD OLEH MAHASISWA** 📤

#### **1.1. Upload File**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/ajukanproposal`  
**Controller:** `ProposalController@store`

**Proses:**
1. Mahasiswa mengisi form pengajuan proposal
2. Upload file PDF (maksimal 5MB, format PDF only)
3. Validasi file:
   - Format: PDF only
   - Ukuran: Maksimal 5MB
   - File name di-generate otomatis oleh Laravel

**Kode:**
```php
// app/Http/Controllers/ProposalController.php
$proposalFile = $file->store('proposals', 'public');
// File disimpan di: storage/app/public/proposals/[filename].pdf
```

#### **1.2. Penyimpanan Database**
**Tabel:** `dokumens`

**Data yang disimpan:**
- `id_proposal` (Foreign Key)
- `skim` (RE, RSH, K, KI, KC, VGK, PM, PI, AI, GFT)
- `path_file` (Path ke file di storage)
- `tgl_upload` (Timestamp upload)

**Kode:**
```php
$proposal->dokumen()->create([
    'skim' => $request->skim,
    'path_file' => $proposalFile,
    'tgl_upload' => now(),
]);
```

#### **1.3. Status Proposal**
Setelah upload, status proposal menjadi:
- `status` = `submitted`
- `status_validasi` = `pending`

---

### **FASE 2: VALIDASI OLEH DOSEN** ✅

#### **2.1. Akses Dokumen oleh Dosen**
**User:** Dosen Pendamping  
**Lokasi:** `/dosen/validasi_proposal`  
**Controller:** `DosenController@validasiProposal`

**Proses:**
1. Dosen melihat daftar proposal yang perlu divalidasi
2. Klik detail proposal untuk melihat dokumen
3. PDF ditampilkan menggunakan iframe dengan URL:
   ```php
   Storage::url($proposal->dokumen->path_file)
   // Menghasilkan: /storage/proposals/[filename].pdf
   ```

#### **2.2. View PDF untuk Dosen**
**Route:** `/dosen/proposal/{id}/view-pdf`  
**Controller:** `DosenController@viewPdf`

**Fitur:**
- PDF ditampilkan langsung di browser (inline)
- Download button tersedia
- Access control: Hanya dosen yang ditugaskan bisa akses

**Kode:**
```php
// app/Http/Controllers/DosenController.php
public function viewPdf($id)
{
    $proposal = Proposal::where('id_dosen', $dosen->id_dosen)
        ->with('dokumen')
        ->findOrFail($id);
    
    $path = storage_path('app/' . $proposal->dokumen->path_file);
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline'
    ]);
}
```

#### **2.3. Validasi Proposal**
Setelah review, dosen dapat:
- **Set Valid** → Status berubah ke `valid`
- **Set Tidak Valid** → Status berubah ke `tidak_valid`

**Status setelah validasi:**
- Jika `valid` → Proposal siap untuk review
- Jika `tidak_valid` → Proposal ditolak, mahasiswa perlu perbaiki

---

### **FASE 3: ASSIGNMENT OLEH OPERATOR** 👨‍💼

#### **3.1. Operator Assign Reviewer**
**User:** Operator  
**Lokasi:** `/operator/pilih_reviewer`  
**Controller:** `OperatorController@assignReviewer`

**Proses:**
1. Operator melihat proposal yang sudah `valid`
2. Assign reviewer:
   - 1 Reviewer Administratif
   - 2 Reviewer Substantif
3. Status proposal berubah ke `review_administratif`

**Kode:**
```php
// app/Http/Controllers/OperatorController.php
$proposal->update([
    'status' => 'review_administratif',
    'id_reviewer_administratif' => $request->reviewer_administratif,
    'id_reviewer_substantif_1' => $request->reviewer_substantif_1,
    'id_reviewer_substantif_2' => $request->reviewer_substantif_2
]);
```

#### **3.2. Operator Akses Dokumen**
**User:** Operator  
**Lokasi:** `/operator/proposal/{id}`  
**Controller:** `OperatorController@detailProposal`

**Fitur:**
- Operator bisa melihat semua proposal
- PDF ditampilkan dengan iframe
- Download button tersedia
- Monitoring progress review

---

### **FASE 4: REVIEW OLEH REVIEWER** 📝

#### **4.1. Review Administratif**
**User:** Reviewer Administratif  
**Lokasi:** `/reviewer/review-administratif`  
**Controller:** `ReviewerController@detailProposal`

**Proses:**
1. Reviewer melihat daftar proposal yang di-assign
2. Klik detail proposal untuk review
3. PDF ditampilkan di halaman review administratif
4. **Checklist dinamis** ditampilkan berdasarkan skim proposal:
   - **PKM-AI**: Checklist khusus untuk Artikel Ilmiah
   - **PKM-GFT**: Checklist khusus untuk Gagasan Futuristik Tertulis
   - **PKM Pendanaan** (RE, RSH, K, KI, KC, VGK, PM, PI): Checklist khusus untuk 8 bidang pendanaan
5. Checklist dikelompokkan per kategori (Kelengkapan Administratif, Format Penulisan, Sistematika, dll)
6. Reviewer mencentang kesalahan yang ditemukan (minimal 1 harus dipilih)
7. Reviewer menulis catatan review
8. Submit review → Status berubah ke `review_substantif`

**Konfigurasi Checklist:**
- File: `config/review_checklist.php`
- Helper: `ProposalHelper::getReviewChecklist($skim)`
- Backward compatible: Data review lama tetap bisa dibaca dan diedit

**View PDF:**
```blade
<!-- resources/views/reviewer/detail_proposal_administratif.blade.php -->
<iframe 
    src="{{ Storage::url($proposal->dokumen->path_file) }}"
    style="width: 100%; height: 80vh;"
></iframe>
```

#### **4.2. Review Substantif**
**User:** Reviewer Substantif (2 orang)  
**Lokasi:** `/reviewer/review-substantif`  
**Controller:** `ReviewerController@detailProposalSubstantif`

**Proses:**
1. Reviewer substantif melihat proposal yang sudah selesai review administratif
2. PDF ditampilkan di halaman review substantif
3. **Form penilaian dinamis** ditampilkan berdasarkan skim proposal:
   - Kriteria dan bobot berbeda untuk setiap skim (RE, RSH, K, KI, KC, VGK, PM, PI, AI, GFT)
   - Struktur hierarkis: Kriteria utama dengan sub-kriteria (jika ada)
   - Penomoran: Kriteria utama diberi nomor, sub-kriteria diindentasi dengan simbol "└─"
4. Reviewer memberikan skor 0-10 untuk setiap kriteria yang bisa di-score
5. Sistem menghitung nilai otomatis: **Nilai = Bobot × Skor**
6. Total nilai maksimal: 1000 (jika semua skor = 10)
7. Nilai akhir: Total / 10 (contoh: 789 → 78.9)
8. Reviewer menulis catatan substantif (minimal 50 karakter)
9. Submit review → Data disimpan:
   - `skor_per_kriteria` (JSON): Array skor per kriteria
   - `total_nilai` (decimal): Total nilai sebelum konversi (0-1000)
   - `nilai_akhir` (decimal): Nilai akhir setelah konversi (0-100.00)
   - `note_substantif` (text): Catatan review
10. Jika semua review selesai → Status berubah ke `revisi`

**Konfigurasi Kriteria:**
- File: `config/review_substantif_criteria.php`
- Helper: `ProposalHelper::getSubstantifCriteria($skim)`
- Struktur: Kriteria bisa memiliki `sub_kriteria` untuk struktur hierarkis

**Status Flow:**
```
review_administratif → review_substantif → revisi
```

---

### **FASE 5: REVISI OLEH MAHASISWA** 🔄

#### **5.1. Upload Revisi**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal/{id}/revisi`  
**Controller:** `ProposalRevisiController@store`

**Proses:**
1. Mahasiswa melihat catatan review
2. Upload file revisi (jika diperlukan)
3. File revisi disimpan di: `storage/app/public/proposal_revisi/`
4. Status tetap `revisi` atau berubah sesuai keputusan operator

---

### **FASE 6: HASIL FINAL** 🎯

#### **6.1. Penilaian Hasil Final oleh Operator**
**User:** Operator  
**Lokasi:** `/operator/detail-hasil-final/{id}`  
**Controller:** `OperatorController@detailHasilFinal` dan `updateHasilFinal`

**Proses:**
1. Operator membuka halaman detail hasil final
2. **PDF proposal** ditampilkan di bagian atas untuk referensi
3. **Tabel referensi** menampilkan penilaian dari 2 reviewer substantif:
   - Tabel Reviewer Substantif 1 (dengan nama reviewer)
   - Tabel Reviewer Substantif 2 (dengan nama reviewer)
   - Menampilkan: Kriteria, Bobot, Skor, Nilai, Total, dan Nilai Akhir
4. **Form penilaian final** dengan struktur yang sama seperti review substantif:
   - Kriteria dinamis sesuai skim proposal
   - Struktur hierarkis (kriteria utama + sub-kriteria)
   - Operator memberikan skor 0-10 untuk setiap kriteria
   - Perhitungan nilai otomatis: Nilai = Bobot × Skor
   - Nilai akhir = Total Nilai / 10
5. Operator mengisi:
   - **Status Final**: Lolos / Tidak Lolos
   - **Nilai Final**: Terisi otomatis dari penilaian (readonly)
   - **Dana yang Dapat Diberikan**: Input manual oleh operator (opsional)
   - **Catatan Final**: Catatan untuk mahasiswa
6. Submit → Data disimpan:
   - `status_final`: lolos / tidak_lolos
   - `nilai` (decimal): Nilai akhir (0-100.00)
   - `skor_per_kriteria` (JSON): Array skor per kriteria dari operator
   - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
   - `catatan_final` (text): Catatan untuk mahasiswa
7. Status proposal berubah sesuai `status_final`

**Catatan Penting:**
- Review administratif **tidak mempengaruhi** nilai substantif maupun hasil final
- Penilaian operator **independen** dari penilaian reviewer substantif
- Tabel reviewer substantif hanya sebagai **referensi** untuk operator

#### **6.2. Pengumuman Hasil**
**User:** Operator / Dosen  
**Lokasi:** `/operator/hasil_final`  
**Controller:** `OperatorController@hasilFinal`

**Status Final:**
- `lolos` → Proposal disetujui
- `tidak_lolos` → Proposal ditolak

#### **6.3. Akses Hasil oleh Mahasiswa**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal` → Popup "Hasil Final"  
**Fitur:**
- Lihat status final (Lolos/Tidak Lolos)
- Lihat nilai final
- **Lihat dana yang dapat diberikan** (jika ada)
- Lihat catatan final
- Download dokumen proposal
- Download dokumen revisi (jika ada)

---

## 📊 DIAGRAM FLOW DOKUMEN

```
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 1. Upload PDF Proposal                               │  │
│  │    → storage/app/public/proposals/[file].pdf         │  │
│  │    → Database: dokumens.path_file                    │  │
│  │    → Status: submitted                               │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    DOSEN PENDAMPING                         │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 2. Validasi Proposal                                 │  │
│  │    → View PDF: Storage::url(path_file)               │  │
│  │    → Download: Storage::disk('public')->download()   │  │
│  │    → Status: valid / tidak_valid                     │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    OPERATOR                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 3. Assign Reviewer                                   │  │
│  │    → View PDF: Storage::url(path_file)               │  │
│  │    → Assign: reviewer_administratif,                 │  │
│  │              reviewer_substantif_1,                   │  │
│  │              reviewer_substantif_2                    │  │
│  │    → Status: review_administratif                    │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    REVIEWER ADMINISTRATIF                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 4. Review Administratif                              │  │
│  │    → View PDF: iframe dengan Storage::url()          │  │
│  │    → Checklist DINAMIS sesuai skim                   │  │
│  │      (PKM-AI, PKM-GFT, PKM Pendanaan)                │  │
│  │    → Catatan review                                  │  │
│  │    → Status: review_substantif                       │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    REVIEWER SUBSTANTIF (2 orang)            │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 5. Review Substantif                                 │  │
│  │    → View PDF: iframe dengan Storage::url()          │  │
│  │    → Form penilaian DINAMIS sesuai skim              │  │
│  │    → Skor 0-10 per kriteria                          │  │
│  │    → Perhitungan otomatis: Nilai = Bobot × Skor      │  │
│  │    → Nilai akhir = Total / 10                        │  │
│  │    → Catatan substantif (min 50 karakter)            │  │
│  │    → Status: revisi (jika semua selesai)             │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 6. Revisi (jika diperlukan)                          │  │
│  │    → Upload revisi: proposal_revisi/[file].pdf       │  │
│  │    → Status: revisi                                  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    OPERATOR                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 7. Hasil Final                                       │  │
│  │    → View PDF proposal untuk referensi                │  │
│  │    → Lihat penilaian 2 reviewer substantif            │  │
│  │    → Form penilaian final (sama seperti substantif)   │  │
│  │    → Input: Status, Nilai, Dana, Catatan              │  │
│  │    → Status: lolos / tidak_lolos                     │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 8. Lihat Hasil Final                                 │  │
│  │    → Status, Nilai, Dana yang Dapat Diberikan        │  │
│  │    → Catatan final                                   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 ACCESS CONTROL & SECURITY

### **1. Mahasiswa**
- ✅ Bisa akses proposal miliknya sendiri
- ✅ Bisa akses proposal di mana dia anggota tim
- ✅ Bisa download dokumen proposal
- ❌ Tidak bisa akses proposal milik mahasiswa lain

### **2. Dosen Pendamping**
- ✅ Bisa akses proposal yang di-assign kepadanya
- ✅ Bisa view dan download PDF
- ✅ Bisa validasi proposal
- ❌ Tidak bisa akses proposal dosen lain

### **3. Reviewer**
- ✅ Bisa akses proposal yang di-assign kepadanya
- ✅ Bisa view PDF untuk review
- ✅ Bisa submit review
- ❌ Tidak bisa akses proposal yang tidak di-assign

### **4. Operator**
- ✅ Bisa akses semua proposal
- ✅ Bisa view dan download semua PDF
- ✅ Bisa assign reviewer
- ✅ Bisa set hasil final

---

## 📁 STRUKTUR STORAGE

```
storage/app/public/
├── proposals/              # File proposal utama
│   ├── proposal_1.pdf
│   ├── proposal_2.pdf
│   └── ...
├── proposal_revisi/        # File revisi proposal
│   ├── revisi_1.pdf
│   └── ...
└── persetujuan/            # File persetujuan (jika ada)
    └── ...
```

**Symlink:** `public/storage` → `storage/app/public`

**URL Access:**
- Public URL: `/storage/proposals/[filename].pdf`
- Storage Path: `storage/app/public/proposals/[filename].pdf`

---

## 🔄 METODE AKSES PDF

### **1. View PDF (Inline)**
**Menggunakan iframe:**
```blade
<iframe src="{{ Storage::url($proposal->dokumen->path_file) }}"></iframe>
```

**Menggunakan route khusus:**
```php
Route::get('/proposal/{id}/view-pdf', [Controller::class, 'viewPdf']);
// Return: response()->file($path, ['Content-Type' => 'application/pdf'])
```

### **2. Download PDF**
```php
Storage::disk('public')->download($path, $filename);
```

### **3. Direct URL**
```php
Storage::url($proposal->dokumen->path_file);
// Output: /storage/proposals/[filename].pdf
```

---

## 📊 STATUS PROPOSAL & AKSES DOKUMEN

| Status | Mahasiswa | Dosen | Reviewer | Operator |
|--------|-----------|-------|----------|----------|
| `submitted` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download |
| `pending` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download |
| `valid` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download |
| `tidak_valid` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download |
| `review_administratif` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download |
| `review_substantif` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download |
| `revisi` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download |
| `lolos` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download |
| `tidak_lolos` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download |

---

## 📋 SISTEM PENILAIAN DINAMIS

### **1. Review Administratif Dinamis**
- **Checklist disesuaikan dengan skim proposal**
- **Konfigurasi:** `config/review_checklist.php`
- **Helper:** `ProposalHelper::getReviewChecklist($skim)`
- **Struktur:** Kategori → Items (array)
- **Skim yang didukung:**
  - PKM-AI (Artikel Ilmiah)
  - PKM-GFT (Gagasan Futuristik Tertulis)
  - PKM Pendanaan: RE, RSH, K, KI, KC, VGK, PM, PI
- **Backward Compatible:** Data review lama tetap bisa dibaca dan diedit

### **2. Review Substantif Dinamis**
- **Kriteria dan bobot disesuaikan dengan skim proposal**
- **Konfigurasi:** `config/review_substantif_criteria.php`
- **Helper:** `ProposalHelper::getSubstantifCriteria($skim)`
- **Struktur:** Kriteria dengan sub-kriteria (hierarkis)
- **Penilaian:**
  - Skor: 0-10 (dapat desimal, step 0.1)
  - Nilai: Bobot × Skor
  - Total maksimal: 1000 (jika semua skor = 10)
  - Nilai akhir: Total / 10 (0-100.00)
- **Data disimpan:**
  - `skor_per_kriteria` (JSON): Array skor per kriteria
  - `total_nilai` (decimal): Total sebelum konversi (0-1000)
  - `nilai_akhir` (decimal): Nilai akhir setelah konversi (0-100.00)
  - `note_substantif` (text): Catatan review (min 50 karakter)

### **3. Hasil Final oleh Operator**
- **Form penilaian sama seperti review substantif**
- **Referensi:** Menampilkan 2 tabel penilaian dari reviewer substantif
- **Input operator:**
  - Skor 0-10 per kriteria (form dinamis sesuai skim)
  - Status final: Lolos / Tidak Lolos
  - Nilai final: Otomatis dari penilaian (readonly)
  - **Dana yang dapat diberikan:** Input manual (opsional)
  - Catatan final: Catatan untuk mahasiswa
- **Data disimpan:**
  - `status_final`: lolos / tidak_lolos
  - `nilai` (decimal): Nilai akhir (0-100.00)
  - `skor_per_kriteria` (JSON): Array skor dari operator
  - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
  - `catatan_final` (text): Catatan untuk mahasiswa

### **4. Tampilan Hasil Final untuk Mahasiswa**
- **Popup "Hasil Final"** di halaman lihat proposal
- **Menampilkan:**
  - Status final (Lolos/Tidak Lolos)
  - Nilai final
  - **Dana yang dapat diberikan** (jika ada)
  - Catatan final

---

## 🎯 KESIMPULAN

### **Flow Dokumen:**
1. **Upload** → Mahasiswa upload PDF ke `storage/app/public/proposals/`
2. **Validasi** → Dosen view & validasi proposal
3. **Assignment** → Operator assign reviewer
4. **Review Administratif** → Reviewer mengisi checklist dinamis sesuai skim
5. **Review Substantif** → 2 reviewer memberikan skor 0-10 per kriteria (dinamis sesuai skim)
6. **Revisi** → Mahasiswa upload revisi (jika perlu)
7. **Hasil Final** → Operator melakukan penilaian final dengan form dinamis, input dana, dan set status
8. **Akses Hasil** → Mahasiswa melihat hasil final termasuk dana yang dapat diberikan

### **Akses Dokumen:**
- **Satu file PDF** disimpan di storage
- **Semua role** mengakses file yang sama melalui `Storage::url()`
- **Access control** di level controller (authorization)
- **File tidak di-copy** untuk setiap user, hanya path yang di-share

### **Sistem Penilaian:**
- ✅ **Checklist administratif dinamis** berdasarkan skim proposal
- ✅ **Kriteria substantif dinamis** dengan struktur hierarkis
- ✅ **Perhitungan nilai otomatis** untuk review substantif dan hasil final
- ✅ **Dua reviewer substantif** memberikan penilaian terpisah
- ✅ **Operator melakukan penilaian final** dengan referensi dari reviewer
- ✅ **Field dana yang dapat diberikan** untuk tracking anggaran

### **Keamanan:**
- ✅ File disimpan di `storage/app/public/` (public disk)
- ✅ Access control di controller level
- ✅ Authorization check sebelum akses file
- ✅ Validasi format dan ukuran file
- ✅ Validasi skor 0-10 di frontend dan backend
- ✅ Validasi jumlah kriteria sesuai dengan skim

---

**Versi:** 2.0.0  
**Tanggal Update:** 2025-11-13  
**Author:** Pramajaya  
**Perubahan Utama:**
- Sistem penilaian administratif dinamis berdasarkan skim
- Sistem penilaian substantif dinamis dengan scoring 0-10
- Hasil final dengan form penilaian dinamis dan field dana

