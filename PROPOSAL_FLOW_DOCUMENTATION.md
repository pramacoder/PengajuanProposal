# 📄 Flow Dokumen Proposal PKM: Dari Upload Hingga Akses Multi-User

## 🎯 Overview

Dokumen ini menjelaskan alur lengkap dokumen proposal PDF dari upload oleh mahasiswa hingga diakses oleh berbagai role pengguna (Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT) dalam sistem pengajuan proposal PKM.

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
2. **PDF proposal** ditampilkan di bagian atas untuk referensi:
   - **Prioritas PDF**: Jika ada revisi akhir, tampilkan revisi akhir; jika tidak, tampilkan dokumen original
   - **Revisi akhir**: File yang diupload mahasiswa setelah hasil final operator (status: `revisi_akhir`)
   - **Dokumen original**: File proposal pertama kali diupload
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
   - **Catatan Final**: Catatan untuk mahasiswa (minimal 50 karakter jika diisi)
6. Submit → Data disimpan:
   - `status_final`: lolos / tidak_lolos
   - `nilai` (decimal): Nilai akhir (0-100.00)
   - `skor_per_kriteria` (JSON): Array skor per kriteria dari operator
   - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
   - `catatan_final` (text): Catatan untuk mahasiswa
   - `id_pt` (foreign key): ID PT yang melakukan penilaian
7. Status proposal berubah sesuai `status_final`:
   - Jika `lolos` → Status: `revisi_akhir` (mahasiswa perlu upload revisi akhir)
   - Jika `tidak_lolos` → Status: `tidak_lolos`

**Catatan Penting:**
- Review administratif **tidak mempengaruhi** nilai substantif maupun hasil final
- Penilaian operator **independen** dari penilaian reviewer substantif
- Tabel reviewer substantif hanya sebagai **referensi** untuk operator
- **Validasi**: `catatan_final` minimal 50 karakter jika diisi
- **PDF Revisi**: Jika ada revisi akhir, sistem akan menampilkan revisi akhir terlebih dahulu

#### **6.2. Pengumuman Hasil**
**User:** Operator / Dosen  
**Lokasi:** `/operator/hasil_final`  
**Controller:** `OperatorController@hasilFinal`

**Status Final:**
- `lolos` → Proposal disetujui
- `tidak_lolos` → Proposal ditolak

#### **6.3. Revisi Akhir oleh Mahasiswa** (Jika Lolos dari Operator)
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal/{id}/revisi-akhir`  
**Controller:** `ProposalController@showRevisiAkhirForm` dan `submitRevisiAkhir`

**Proses:**
1. Mahasiswa melihat hasil final dari operator (status: `lolos`)
2. Status proposal berubah ke `revisi_akhir`
3. Mahasiswa upload file revisi akhir:
   - File disimpan di: `storage/app/public/proposals/revisi_akhir/`
   - Format: PDF (maksimal 5MB)
   - Nama file: `revisi_akhir_[timestamp]_[original_name].pdf`
4. Submit revisi akhir → Status berubah ke `validasi_akhir_dosen_univ`
5. Data disimpan di tabel `proposal_revisi`:
   - `id_proposal` (Foreign Key)
   - `path_file` (Path ke file revisi akhir)
   - `nama_file` (Nama file revisi akhir)
   - `tanggal_submit` (Timestamp upload)

**Catatan:**
- Revisi akhir hanya bisa diupload jika status proposal adalah `revisi_akhir`
- Setelah upload, proposal akan divalidasi oleh dosen pendamping universitas

#### **6.4. Validasi Akhir oleh Dosen Universitas**
**User:** Dosen Pendamping Universitas  
**Lokasi:** `/dosen/validasi-akhir`  
**Controller:** `DosenController@validasiAkhirProposal` dan `detailValidasiAkhir`

**Proses:**
1. Dosen melihat daftar proposal yang perlu divalidasi akhir (status: `validasi_akhir_dosen_univ`)
2. Klik detail proposal untuk melihat dokumen revisi akhir
3. **PDF revisi akhir** ditampilkan menggunakan iframe:
   - Prioritas: Tampilkan revisi akhir jika ada
   - Jika tidak ada revisi akhir, tampilkan dokumen original
4. Dosen melakukan validasi:
   - **Set Valid** → Status berubah ke `pimpinan_pt` (siap untuk penilaian Pimpinan PT)
   - **Set Tidak Valid** → Status kembali ke `revisi_akhir` (mahasiswa perlu perbaiki lagi)
5. View PDF Revisi Akhir:
   - Route: `/dosen/revisi-akhir/{id}/view-pdf`
   - Controller: `DosenController@viewPdfRevisiAkhir`
   - PDF ditampilkan inline di browser

**Catatan:**
- Validasi akhir dilakukan oleh dosen pendamping universitas
- Jika valid, proposal akan dinilai oleh Pimpinan PT
- Jika tidak valid, mahasiswa perlu upload revisi lagi

#### **6.5. Penilaian Hasil Final oleh Pimpinan PT**
**User:** Pimpinan PT  
**Lokasi:** `/pimpinan-pt/dashboard` dan `/pimpinan-pt/detail-hasil-final/{id}`  
**Controller:** `PimpinanPTController@dashboard`, `detailHasilFinal`, dan `updateHasilFinal`

**Proses:**
1. Pimpinan PT melihat dashboard proposal yang perlu dinilai (status: `pimpinan_pt`)
2. Klik detail proposal untuk melihat hasil final
3. **PDF revisi akhir** ditampilkan di bagian atas:
   - Prioritas: Tampilkan revisi akhir jika ada
   - Jika tidak ada revisi akhir, tampilkan dokumen original
   - Route: `/pimpinan-pt/proposal/{id}/view-pdf`
4. **Tabel referensi** menampilkan penilaian dari 2 reviewer substantif (sama seperti operator)
5. **Form penilaian final** dengan struktur yang sama seperti review substantif:
   - Kriteria dinamis sesuai skim proposal
   - Struktur hierarkis (kriteria utama + sub-kriteria)
   - Pimpinan PT memberikan skor 0-10 untuk setiap kriteria
   - Perhitungan nilai otomatis: Nilai = Bobot × Skor
   - Nilai akhir = Total Nilai / 10
6. Pimpinan PT mengisi:
   - **Status PIMNAS**: Lolos / Tidak Lolos
   - **Status Pendanaan**: Lolos / Tidak Lolos
   - **Dana yang Didapatkan**: 
     - Jika status pendanaan "tidak lolos" → Otomatis 0, field disabled
     - Jika status pendanaan "lolos" → Input manual, maksimal Rp 15.000.000
   - **Catatan Final**: Catatan untuk mahasiswa (opsional)
   - **Nilai Final**: Terisi otomatis dari penilaian (readonly)
7. Submit → Data disimpan:
   - `status_pimnas`: lolos / tidak_lolos
   - `status_pendanaan`: lolos / tidak_lolos
   - `dana_yang_didapatkan` (decimal): Dana yang didapatkan (0 jika tidak lolos, max 15.000.000 jika lolos)
   - `nilai` (decimal): Nilai akhir (0-100.00)
   - `skor_per_kriteria` (JSON): Array skor per kriteria dari Pimpinan PT
   - `catatan_final` (text): Catatan untuk mahasiswa
   - `id_pimpinan_pt` (foreign key): ID PT yang melakukan penilaian
8. Status proposal berubah sesuai kombinasi status:
   - Jika `status_pimnas` = `lolos` dan `status_pendanaan` = `lolos` → Status: `lolos_pimnas_pendanaan`
   - Jika `status_pimnas` = `lolos` dan `status_pendanaan` = `tidak_lolos` → Status: `lolos_pimnas_tidak_pendanaan`
   - Jika `status_pimnas` = `tidak_lolos` dan `status_pendanaan` = `lolos` → Status: `tidak_lolos_pimnas_lolos_pendanaan`
   - Jika keduanya `tidak_lolos` → Status: `tidak_lolos`

**Catatan Penting:**
- Penilaian Pimpinan PT **independen** dari penilaian operator
- **Validasi dana**: Jika status pendanaan "tidak lolos", dana otomatis 0 dan tidak bisa diubah
- **Validasi dana**: Jika status pendanaan "lolos", dana maksimal Rp 15.000.000
- **PDF Revisi**: Sistem akan menampilkan revisi akhir terlebih dahulu jika ada

#### **6.6. Manajemen Akun oleh Pimpinan PT**
**User:** Pimpinan PT  
**Lokasi:** `/pimpinan-pt/akun`  
**Controller:** `PimpinanPTController@manageAccounts`, `storeAccount`, `updateAccount`, `deleteAccount`

**Fitur:**
- ✅ Mengelola semua jenis user: Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT
- ✅ Create, Update, Delete akun
- ✅ Bulk delete untuk mahasiswa
- ✅ Filter dan pencarian akun
- ✅ Sama seperti manajemen akun operator

#### **6.7. Akses Hasil oleh Mahasiswa**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal` → Popup "Hasil Final"  
**Fitur:**
- Lihat status final dari Pimpinan PT:
  - Status PIMNAS (Lolos/Tidak Lolos)
  - Status Pendanaan (Lolos/Tidak Lolos)
- Lihat nilai final
- **Lihat dana yang didapatkan** (jika status pendanaan "lolos")
- Lihat catatan final
- Download dokumen proposal
- Download dokumen revisi akhir (jika ada)

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
│  │    → View PDF (prioritas: revisi akhir > original)   │  │
│  │    → Lihat penilaian 2 reviewer substantif            │  │
│  │    → Form penilaian final (sama seperti substantif)   │  │
│  │    → Input: Status, Nilai, Dana, Catatan              │  │
│  │    → Status: lolos → revisi_akhir                    │  │
│  │    → Status: tidak_lolos → tidak_lolos              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 8. Revisi Akhir (jika lolos)                         │  │
│  │    → Upload revisi akhir:                            │  │
│  │      proposals/revisi_akhir/[file].pdf              │  │
│  │    → Status: validasi_akhir_dosen_univ             │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    DOSEN UNIVERSITAS                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 9. Validasi Akhir                                    │  │
│  │    → View PDF revisi akhir                           │  │
│  │    → Valid → Status: pimpinan_pt                    │  │
│  │    → Tidak Valid → Status: revisi_akhir              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    PIMPINAN PT                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 10. Hasil Final Pimpinan PT                          │  │
│  │     → View PDF revisi akhir (prioritas)              │  │
│  │     → Lihat penilaian 2 reviewer substantif           │  │
│  │     → Form penilaian final                            │  │
│  │     → Input: Status PIMNAS, Status Pendanaan,         │  │
│  │              Dana (max 15.000.000), Catatan          │  │
│  │     → Status: lolos_pimnas_pendanaan /               │  │
│  │              tidak_lolos / dll                       │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 11. Lihat Hasil Final                                │  │
│  │     → Status PIMNAS, Status Pendanaan                │  │
│  │     → Nilai final                                    │  │
│  │     → Dana yang didapatkan (jika lolos)              │  │
│  │     → Catatan final                                   │  │
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
- ✅ Bisa melihat revisi akhir jika ada
- ✅ Bisa mengelola semua jenis akun (Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT)

### **5. Pimpinan PT**
- ✅ Bisa akses proposal yang sudah divalidasi dosen universitas (status: `pimpinan_pt`)
- ✅ Bisa view dan download PDF (prioritas: revisi akhir > original)
- ✅ Bisa set hasil final (status PIMNAS, status pendanaan, dana)
- ✅ Bisa mengelola semua jenis akun (Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT)
- ❌ Tidak bisa akses proposal yang belum divalidasi dosen universitas

---

## 📁 STRUKTUR STORAGE

```
storage/app/public/
├── proposals/              # File proposal utama
│   ├── proposal_1.pdf
│   ├── proposal_2.pdf
│   ├── revisi_akhir/      # File revisi akhir (setelah hasil final operator)
│   │   ├── revisi_akhir_1234567890_proposal_1.pdf
│   │   └── ...
│   └── ...
├── proposal_revisi/        # File revisi proposal (setelah review substantif)
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

| Status | Mahasiswa | Dosen | Reviewer | Operator | Pimpinan PT |
|--------|-----------|-------|----------|----------|-------------|
| `submitted` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ❌ |
| `pending` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ❌ |
| `valid` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ❌ |
| `tidak_valid` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ❌ |
| `review_administratif` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download | ❌ |
| `review_substantif` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download | ❌ |
| `revisi` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ✅ View/Download | ❌ |
| `revisi_akhir` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ❌ |
| `validasi_akhir_dosen_univ` | ✅ View/Download | ✅ (Assigned) | ❌ | ✅ View/Download | ❌ |
| `pimpinan_pt` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `lolos` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `tidak_lolos` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `lolos_pimnas_pendanaan` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `lolos_pimnas_tidak_pendanaan` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `tidak_lolos_pimnas_lolos_pendanaan` | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |

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
- **PDF Prioritas:** Tampilkan revisi akhir jika ada, jika tidak tampilkan dokumen original
- **Input operator:**
  - Skor 0-10 per kriteria (form dinamis sesuai skim)
  - Status final: Lolos / Tidak Lolos
  - Nilai final: Otomatis dari penilaian (readonly)
  - **Dana yang dapat diberikan:** Input manual (opsional)
  - Catatan final: Catatan untuk mahasiswa (minimal 50 karakter jika diisi)
- **Data disimpan:**
  - `status_final`: lolos / tidak_lolos
  - `nilai` (decimal): Nilai akhir (0-100.00)
  - `skor_per_kriteria` (JSON): Array skor dari operator
  - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
  - `catatan_final` (text): Catatan untuk mahasiswa
  - `id_pt` (foreign key): ID PT yang melakukan penilaian
- **Status setelah penilaian:**
  - Jika `lolos` → Status proposal: `revisi_akhir` (mahasiswa perlu upload revisi akhir)
  - Jika `tidak_lolos` → Status proposal: `tidak_lolos`

### **4. Revisi Akhir oleh Mahasiswa**
- **Trigger:** Proposal lolos dari penilaian operator (status: `revisi_akhir`)
- **Upload file revisi akhir:**
  - Lokasi: `storage/app/public/proposals/revisi_akhir/`
  - Format: PDF (maksimal 5MB)
  - Nama file: `revisi_akhir_[timestamp]_[original_name].pdf`
- **Data disimpan:**
  - Tabel: `proposal_revisi`
  - `path_file`: Path ke file revisi akhir
  - `nama_file`: Nama file revisi akhir
  - `tanggal_submit`: Timestamp upload
- **Status setelah upload:** `validasi_akhir_dosen_univ`

### **5. Validasi Akhir oleh Dosen Universitas**
- **User:** Dosen Pendamping Universitas
- **Proses:**
  - View PDF revisi akhir (prioritas: revisi akhir > original)
  - Validasi proposal revisi akhir
  - Jika valid → Status: `pimpinan_pt`
  - Jika tidak valid → Status: `revisi_akhir` (mahasiswa perlu perbaiki lagi)

### **6. Hasil Final oleh Pimpinan PT**
- **Form penilaian sama seperti review substantif**
- **Referensi:** Menampilkan 2 tabel penilaian dari reviewer substantif
- **PDF Prioritas:** Tampilkan revisi akhir jika ada, jika tidak tampilkan dokumen original
- **Input Pimpinan PT:**
  - Skor 0-10 per kriteria (form dinamis sesuai skim)
  - **Status PIMNAS**: Lolos / Tidak Lolos
  - **Status Pendanaan**: Lolos / Tidak Lolos
  - **Dana yang Didapatkan**: 
    - Jika status pendanaan "tidak lolos" → Otomatis 0, field disabled
    - Jika status pendanaan "lolos" → Input manual, maksimal Rp 15.000.000
  - Catatan final: Catatan untuk mahasiswa (opsional)
  - Nilai final: Otomatis dari penilaian (readonly)
- **Data disimpan:**
  - `status_pimnas`: lolos / tidak_lolos
  - `status_pendanaan`: lolos / tidak_lolos
  - `dana_yang_didapatkan` (decimal): Dana yang didapatkan (0 jika tidak lolos, max 15.000.000 jika lolos)
  - `nilai` (decimal): Nilai akhir (0-100.00)
  - `skor_per_kriteria` (JSON): Array skor dari Pimpinan PT
  - `catatan_final` (text): Catatan untuk mahasiswa
  - `id_pimpinan_pt` (foreign key): ID PT yang melakukan penilaian
- **Status setelah penilaian:**
  - Kombinasi status menghasilkan status final yang lebih spesifik:
    - `lolos_pimnas_pendanaan`: Lolos PIMNAS dan Pendanaan
    - `lolos_pimnas_tidak_pendanaan`: Lolos PIMNAS tapi tidak pendanaan
    - `tidak_lolos_pimnas_lolos_pendanaan`: Tidak lolos PIMNAS tapi lolos pendanaan
    - `tidak_lolos`: Tidak lolos keduanya

### **7. Tampilan Hasil Final untuk Mahasiswa**
- **Popup "Hasil Final"** di halaman lihat proposal
- **Menampilkan:**
  - Status PIMNAS (Lolos/Tidak Lolos)
  - Status Pendanaan (Lolos/Tidak Lolos)
  - Nilai final
  - **Dana yang didapatkan** (jika status pendanaan "lolos")
  - Catatan final
  - Download dokumen proposal
  - Download dokumen revisi akhir (jika ada)

---

## 🎯 KESIMPULAN

### **Flow Dokumen:**
1. **Upload** → Mahasiswa upload PDF ke `storage/app/public/proposals/`
2. **Validasi** → Dosen view & validasi proposal
3. **Assignment** → Operator assign reviewer
4. **Review Administratif** → Reviewer mengisi checklist dinamis sesuai skim
5. **Review Substantif** → 2 reviewer memberikan skor 0-10 per kriteria (dinamis sesuai skim)
6. **Revisi** → Mahasiswa upload revisi (jika perlu)
7. **Hasil Final Operator** → Operator melakukan penilaian final dengan form dinamis, input dana, dan set status
8. **Revisi Akhir** → Jika lolos, mahasiswa upload revisi akhir ke `storage/app/public/proposals/revisi_akhir/`
9. **Validasi Akhir** → Dosen universitas validasi revisi akhir
10. **Hasil Final Pimpinan PT** → Pimpinan PT melakukan penilaian final (status PIMNAS, status pendanaan, dana max 15.000.000)
11. **Akses Hasil** → Mahasiswa melihat hasil final termasuk status PIMNAS, status pendanaan, dan dana yang didapatkan

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
- ✅ **Field dana yang dapat diberikan** untuk tracking anggaran (operator)
- ✅ **Pimpinan PT melakukan penilaian final** dengan status PIMNAS dan status pendanaan terpisah
- ✅ **Field dana yang didapatkan** dengan validasi: 0 jika tidak lolos, max 15.000.000 jika lolos
- ✅ **Revisi akhir** setelah penilaian operator (jika lolos)
- ✅ **Validasi akhir** oleh dosen universitas sebelum penilaian Pimpinan PT

### **Keamanan:**
- ✅ File disimpan di `storage/app/public/` (public disk)
- ✅ Access control di controller level
- ✅ Authorization check sebelum akses file
- ✅ Validasi format dan ukuran file
- ✅ Validasi skor 0-10 di frontend dan backend
- ✅ Validasi jumlah kriteria sesuai dengan skim
- ✅ Validasi catatan final minimal 50 karakter jika diisi
- ✅ Validasi dana yang didapatkan: 0 jika tidak lolos, max 15.000.000 jika lolos
- ✅ Prioritas PDF: Revisi akhir ditampilkan terlebih dahulu jika ada

---

**Versi:** 3.0.0  
**Tanggal Update:** 2025-11-17  
**Author:** Pramajaya  
**Perubahan Utama:**
- Sistem penilaian administratif dinamis berdasarkan skim
- Sistem penilaian substantif dinamis dengan scoring 0-10
- Hasil final operator dengan form penilaian dinamis dan field dana
- **Revisi akhir** oleh mahasiswa setelah penilaian operator (jika lolos)
- **Validasi akhir** oleh dosen universitas
- **Hasil final Pimpinan PT** dengan status PIMNAS dan status pendanaan terpisah
- **Field dana yang didapatkan** dengan validasi: 0 jika tidak lolos, max 15.000.000 jika lolos
- **Manajemen akun** oleh Pimpinan PT untuk semua jenis user
- **Prioritas PDF**: Revisi akhir ditampilkan terlebih dahulu jika ada
- **Validasi catatan final**: Minimal 50 karakter jika diisi

