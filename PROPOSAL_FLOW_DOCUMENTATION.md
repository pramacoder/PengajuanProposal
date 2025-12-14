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

#### **5.1. Upload Revisi (Revisi Pertama)**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal/{id}/revisi`  
**Controller:** `ProposalRevisiController@store`

**Proses:**
1. Mahasiswa melihat catatan review dari reviewer substantif
2. Upload file revisi pertama (jika diperlukan)
3. File revisi disimpan di: `storage/app/public/proposal_revisi/`
4. File name format: `revisi_[timestamp]_[original_name].pdf`
5. Status proposal tetap `revisi` atau berubah sesuai keputusan operator
6. **Revisi pertama ini digunakan untuk penilaian Hasil Semi Final**

**Catatan:**
- Mahasiswa dapat melakukan beberapa kali revisi (revisi 1, revisi 2, dll)
- Semua file revisi disimpan di tabel `proposal_revisi`
- Revisi terakhir (berdasarkan `tanggal_submit`) yang digunakan untuk penilaian

---

### **FASE 6: HASIL SEMI FINAL OLEH OPERATOR** 🎯

#### **6.1. Penilaian Hasil Semi Final oleh Operator**
**User:** Operator  
**Lokasi:** `/operator/detail-hasil-semi-final/{id}`  
**Controller:** `OperatorController@detailHasilSemiFinal` dan `updateHasilSemiFinal`

**Proses:**
1. Operator membuka halaman detail hasil semi final
2. **PDF proposal** ditampilkan di bagian atas untuk referensi:
   - **Prioritas**: Menampilkan revisi terakhir dari `proposal_revisi` (jika ada)
   - Jika tidak ada revisi, tampilkan dokumen original
3. **Tabel referensi** menampilkan penilaian dari 2 reviewer substantif:
   - Tabel Reviewer Substantif 1 (dengan nama reviewer)
   - Tabel Reviewer Substantif 2 (dengan nama reviewer)
   - Menampilkan: Kriteria, Bobot, Skor, Nilai, Total, dan Nilai Akhir
4. **Form penilaian semi final** dengan struktur yang sama seperti review substantif:
   - Kriteria dinamis sesuai skim proposal
   - Struktur hierarkis (kriteria utama + sub-kriteria)
   - Operator memberikan skor 0-10 untuk setiap kriteria
   - Perhitungan nilai otomatis: Nilai = Bobot × Skor
   - Nilai akhir = Total Nilai / 10
5. Operator mengisi:
   - **Status Final**: Lolos Tingkat Universitas / Tidak Lolos Tingkat Universitas
   - **Nilai Final**: Terisi otomatis dari penilaian (readonly)
   - **Dana yang Dapat Diberikan**: Input manual oleh operator (opsional)
   - **Catatan Final**: Catatan untuk mahasiswa (minimal 50 karakter jika diisi)
   - **Dosen Pendamping Universitas**: Dipilih jika status "Lolos Tingkat Universitas"
6. Submit → Data disimpan:
   - `status_final`: lolos_tingkat_universitas / tidak_lolos_tingkat_universitas
   - `nilai` (decimal): Nilai akhir (0-100.00)
   - `skor_per_kriteria` (JSON): Array skor per kriteria dari operator
   - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
   - `catatan_final` (text): Catatan untuk mahasiswa
   - `id_dosen_pendamping_universitas`: ID dosen yang ditugaskan (jika lolos)
   - `id_pt`: ID operator yang melakukan penilaian
7. Status proposal berubah:
   - Jika `lolos_tingkat_universitas` → Status menjadi `revisi_akhir`
   - Jika `tidak_lolos_tingkat_universitas` → Status menjadi `tidak_lolos`

**Catatan Penting:**
- Review administratif **tidak mempengaruhi** nilai substantif maupun hasil semi final
- Penilaian operator **independen** dari penilaian reviewer substantif
- Tabel reviewer substantif hanya sebagai **referensi** untuk operator
- Data disimpan di tabel `hasil_semi_finals`
- **Revisi yang digunakan**: Revisi terakhir dari `proposal_revisi` (revisi pertama, revisi kedua, dll)

---

### **FASE 7: REVISI AKHIR OLEH MAHASISWA** 🔄

#### **7.1. Upload Revisi Akhir**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal/{id}/revisi-akhir`  
**Controller:** `ProposalController@showRevisiAkhirForm` dan `submitRevisiAkhir`

**Proses:**
1. Mahasiswa melihat notifikasi bahwa proposal perlu revisi akhir
2. Status proposal: `revisi_akhir`
3. Upload file revisi akhir (maksimal 5MB, format PDF only)
4. File revisi akhir disimpan di: `storage/app/public/proposals/revisi_akhir/`
5. File name format: `revisi_akhir_[timestamp]_[original_name].pdf`
6. Submit → Status proposal berubah ke `validasi_akhir_dosen_univ`

**Perbedaan dengan Revisi Biasa (Revisi Pertama):**
- **Revisi Biasa (Revisi Pertama)**: 
  - Disimpan di `proposal_revisi/`
  - Status `revisi`
  - Digunakan untuk penilaian **Hasil Semi Final** oleh Operator
- **Revisi Akhir**: 
  - Disimpan di `proposals/revisi_akhir/`
  - Status `revisi_akhir` → `validasi_akhir_dosen_univ`
  - Digunakan untuk penilaian **Hasil Final** oleh Pimpinan PT
- Revisi akhir **diprioritaskan** saat view PDF oleh Dosen Universitas dan Pimpinan PT

**Kode:**
```php
// app/Http/Controllers/ProposalController.php
$filePath = $revisiFile->storeAs('proposals/revisi_akhir', $fileName, 'public');
$proposal->update([
    'status' => 'validasi_akhir_dosen_univ',
    'status_final' => 'validasi_akhir_dosen_univ'
]);
```

---

### **FASE 8: VALIDASI AKHIR OLEH DOSEN UNIVERSITAS** ✅

#### **8.1. Validasi Akhir Proposal**
**User:** Dosen Pendamping Universitas  
**Lokasi:** `/dosen/universitas/validasi-akhir`  
**Controller:** `DosenController@validasiAkhirProposal` dan `detailValidasiAkhir`

**Proses:**
1. Dosen Universitas melihat daftar proposal yang perlu divalidasi akhir
2. Status proposal: `validasi_akhir_dosen_univ`
3. Klik detail proposal untuk melihat dokumen
4. **PDF ditampilkan dengan prioritas:**
   - Jika ada **revisi akhir** → Tampilkan revisi akhir
   - Jika tidak ada → Tampilkan dokumen original
5. Dosen Universitas melakukan validasi:
   - **Set Valid** → Status berubah ke `pimpinan_pt`
   - **Set Tidak Valid** → Status berubah ke `revisi_akhir` (kembali ke mahasiswa)
6. Jika ditolak, dosen dapat upload file review (opsional PDF)

**View PDF:**
```php
// app/Http/Controllers/DosenController.php
$proposal = Proposal::with(['proposalRevisi' => function($query) {
    $query->where('path_file', 'like', '%revisi_akhir%')
          ->orderBy('tanggal_submit', 'desc');
}])->findOrFail($id);

// Prioritas: Revisi akhir jika ada, jika tidak ambil dokumen original
$revisiAkhir = $proposal->proposalRevisi->first();
```

**Status setelah validasi:**
- Jika `valid` → Proposal masuk ke Pimpinan PT untuk penilaian final
- Jika `tidak_valid` → Proposal kembali ke mahasiswa untuk revisi akhir ulang

---

### **FASE 9: HASIL FINAL OLEH PIMPINAN PT** 🏆

#### **9.1. Penilaian Hasil Final oleh Pimpinan PT**
**User:** Pimpinan PT  
**Lokasi:** `/pimpinan-pt/detail-hasil-final/{id}`  
**Controller:** `PimpinanPTController@detailHasilFinal` dan `updateHasilFinal`

**Proses:**
1. Pimpinan PT melihat daftar proposal yang perlu dinilai final
2. Status proposal: `pimpinan_pt`
3. Klik detail proposal untuk melihat dokumen
4. **PDF ditampilkan dengan prioritas:**
   - Jika ada **revisi akhir** (dari `proposals/revisi_akhir/`) → Tampilkan revisi akhir
   - Jika tidak ada revisi akhir, cek **revisi terakhir** dari `proposal_revisi/` → Tampilkan revisi terakhir
   - Jika tidak ada revisi sama sekali → Tampilkan dokumen original
5. **Tabel referensi** menampilkan penilaian dari 2 reviewer substantif (sama seperti operator)
6. **Form penilaian final** dengan struktur yang sama seperti review substantif:
   - Kriteria dinamis sesuai skim proposal
   - Struktur hierarkis (kriteria utama + sub-kriteria)
   - Pimpinan PT memberikan skor 0-10 untuk setiap kriteria
   - Perhitungan nilai otomatis: Nilai = Bobot × Skor
   - Nilai akhir = Total Nilai / 10
7. Pimpinan PT mengisi:
   - **Status PIMNAS**: Lolos / Tidak Lolos
   - **Status Pendanaan**: Lolos / Tidak Lolos
   - **Dana yang Didapatkan**: 
     - Jika `status_pendanaan` = `lolos` → Input 0-15,000,000 (wajib)
     - Jika `status_pendanaan` = `tidak_lolos` → Otomatis 0 (tidak bisa diubah)
   - **Nilai Final**: Terisi otomatis dari penilaian (readonly)
   - **Catatan Final**: Catatan untuk mahasiswa
8. Submit → Data disimpan:
   - `status_pimnas`: lolos / tidak_lolos
   - `status_pendanaan`: lolos / tidak_lolos
   - `dana_yang_didapatkan` (decimal): 0-15,000,000 (jika lolos pendanaan), 0 (jika tidak)
   - `nilai` (decimal): Nilai akhir (0-100.00)
   - `skor_per_kriteria` (JSON): Array skor per kriteria dari Pimpinan PT
   - `catatan_final` (text): Catatan untuk mahasiswa
   - `id_pimpinan_pt`: ID Pimpinan PT yang melakukan penilaian
9. Status proposal berubah:
   - Jika `status_pimnas` = `lolos` dan `status_pendanaan` = `lolos` → `lolos_pimnas_pendanaan`
   - Jika `status_pimnas` = `lolos` dan `status_pendanaan` = `tidak_lolos` → `lolos_pimnas_tidak_pendanaan`
   - Jika `status_pimnas` = `tidak_lolos` dan `status_pendanaan` = `lolos` → `tidak_lolos_pimnas_lolos_pendanaan`
   - Jika keduanya `tidak_lolos` → `tidak_lolos`

**Catatan Penting:**
- Penilaian Pimpinan PT **independen** dari penilaian operator dan reviewer
- Tabel reviewer substantif hanya sebagai **referensi**
- Data disimpan di tabel `hasil_finals`
- Field `dana_yang_didapatkan` memiliki validasi khusus berdasarkan `status_pendanaan`

**Validasi:**
```php
// app/Http/Controllers/PimpinanPTController.php
'dana_yang_didapatkan' => 'required_if:status_pendanaan,lolos|nullable|numeric|min:0|max:15000000'
```

#### **9.2. Manajemen Akun oleh Pimpinan PT**
**User:** Pimpinan PT  
**Lokasi:** `/pimpinan-pt/akun`  
**Controller:** `PimpinanPTController@manageAccounts`

**Fitur:**
- Pimpinan PT dapat mengelola semua jenis user:
  - Mahasiswa
  - Dosen
  - Reviewer
  - Operator
  - Pimpinan PT
- Fitur CRUD lengkap untuk semua user types
- Bulk delete untuk mahasiswa

---

### **FASE 10: PENGUMUMAN HASIL FINAL** 📢

#### **10.1. Akses Hasil oleh Mahasiswa**
**User:** Mahasiswa  
**Lokasi:** `/mahasiswa/proposal` → Popup "Hasil Final"  
**Fitur:**
- Lihat status final:
  - Status PIMNAS (Lolos/Tidak Lolos)
  - Status Pendanaan (Lolos/Tidak Lolos)
- Lihat nilai final
- **Lihat dana yang didapatkan** (jika status pendanaan = lolos)
- Lihat catatan final
- Download dokumen proposal
- Download dokumen revisi (jika ada)
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
│  │ 7. Hasil Semi Final                                  │  │
│  │    → View PDF proposal untuk referensi                │  │
│  │    → Lihat penilaian 2 reviewer substantif            │  │
│  │    → Form penilaian final (sama seperti substantif) │  │
│  │    → Input: Status, Nilai, Dana, Catatan, Dosen Univ│  │
│  │    → Status: lolos_tingkat_universitas / tidak_lolos │  │
│  │    → Jika lolos → Status: revisi_akhir              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 8. Revisi Akhir                                      │  │
│  │    → Upload revisi akhir:                            │  │
│  │      proposals/revisi_akhir/[file].pdf              │  │
│  │    → Status: validasi_akhir_dosen_univ              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    DOSEN UNIVERSITAS                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 9. Validasi Akhir                                    │  │
│  │    → View PDF: Prioritas revisi akhir jika ada        │  │
│  │    → Validasi: valid / tidak_valid                    │  │
│  │    → Jika valid → Status: pimpinan_pt               │  │
│  │    → Jika tidak valid → Status: revisi_akhir        │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    PIMPINAN PT                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 10. Hasil Final                                      │  │
│  │     → View PDF: Prioritas revisi akhir jika ada       │  │
│  │     → Lihat penilaian 2 reviewer substantif           │  │
│  │     → Form penilaian final (sama seperti substantif)  │  │
│  │     → Input: Status PIMNAS, Status Pendanaan,         │  │
│  │              Dana (0-15M jika lolos), Nilai, Catatan  │  │
│  │     → Status: lolos_pimnas_pendanaan /                │  │
│  │              lolos_pimnas_tidak_pendanaan /           │  │
│  │              tidak_lolos_pimnas_lolos_pendanaan /     │  │
│  │              tidak_lolos                              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    MAHASISWA                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ 11. Lihat Hasil Final                                │  │
│  │     → Status PIMNAS, Status Pendanaan                │  │
│  │     → Nilai final                                    │  │
│  │     → Dana yang didapatkan (jika lolos pendanaan)    │  │
│  │     → Catatan final                                  │  │
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
- ✅ Bisa set hasil semi final
- ✅ Bisa assign Dosen Pendamping Universitas

### **5. Dosen Pendamping Universitas**
- ✅ Bisa akses proposal yang di-assign kepadanya untuk validasi akhir
- ✅ Bisa view PDF dengan prioritas revisi akhir jika ada
- ✅ Bisa validasi akhir proposal
- ✅ Bisa upload file review (opsional)
- ❌ Tidak bisa akses proposal yang tidak di-assign

### **6. Pimpinan PT**
- ✅ Bisa akses proposal dengan status `pimpinan_pt` dan proposal yang sudah dinilai
- ✅ Bisa view PDF dengan prioritas revisi akhir jika ada
- ✅ Bisa melakukan penilaian final dengan status PIMNAS dan pendanaan
- ✅ Bisa mengelola semua jenis user (Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT)
- ✅ Bisa mengakses semua menu operator
- ❌ Tidak bisa menghapus akun sendiri

---

## 📁 STRUKTUR STORAGE

```
storage/app/public/
├── proposals/              # File proposal utama
│   ├── proposal_1.pdf
│   ├── proposal_2.pdf
│   ├── revisi_akhir/      # File revisi akhir (prioritas tinggi)
│   │   ├── revisi_akhir_[timestamp]_[name].pdf
│   │   └── ...
│   └── review_akhir/       # File review dari dosen universitas (opsional)
│       └── ...
├── proposal_revisi/        # File revisi proposal (revisi biasa)
│   ├── revisi_1.pdf
│   └── ...
└── persetujuan/            # File persetujuan (jika ada)
    └── ...
```

**Prioritas View PDF:**
1. **Revisi Akhir** (`proposals/revisi_akhir/`) - Diprioritaskan oleh Dosen Universitas dan Pimpinan PT
2. **Dokumen Original** (`proposals/`) - Fallback jika tidak ada revisi akhir
3. **Revisi Biasa** (`proposal_revisi/`) - Untuk referensi historis

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

| Status | Mahasiswa | Dosen Pendamping | Dosen Universitas | Reviewer | Operator | Pimpinan PT |
|--------|-----------|------------------|-------------------|----------|----------|-------------|
| `submitted` | ✅ View/Download | ✅ View/Download | ❌ | ❌ | ✅ View/Download | ❌ |
| `pending` | ✅ View/Download | ✅ View/Download | ❌ | ❌ | ✅ View/Download | ❌ |
| `valid` | ✅ View/Download | ✅ View/Download | ❌ | ❌ | ✅ View/Download | ❌ |
| `tidak_valid` | ✅ View/Download | ✅ View/Download | ❌ | ❌ | ✅ View/Download | ❌ |
| `review_administratif` | ✅ View/Download | ✅ View/Download | ❌ | ✅ (Assigned) | ✅ View/Download | ❌ |
| `review_substantif` | ✅ View/Download | ✅ View/Download | ❌ | ✅ (Assigned) | ✅ View/Download | ❌ |
| `revisi` | ✅ View/Download | ✅ View/Download | ❌ | ✅ (Assigned) | ✅ View/Download | ❌ |
| `revisi_akhir` | ✅ View/Download | ✅ View/Download | ❌ | ❌ | ✅ View/Download | ❌ |
| `validasi_akhir_dosen_univ` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ❌ | ✅ View/Download | ❌ |
| `pimpinan_pt` | ✅ View/Download | ✅ View/Download | ✅ (Assigned) | ❌ | ✅ View/Download | ✅ View/Download |
| `lolos_pimnas_pendanaan` | ✅ View/Download | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `lolos_pimnas_tidak_pendanaan` | ✅ View/Download | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `tidak_lolos_pimnas_lolos_pendanaan` | ✅ View/Download | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |
| `tidak_lolos` | ✅ View/Download | ✅ View/Download | ✅ View/Download | ❌ | ✅ View/Download | ✅ View/Download |

**Catatan:**
- **Prioritas PDF**: Dosen Universitas dan Pimpinan PT melihat **revisi akhir** jika ada, jika tidak baru melihat dokumen original
- **Dosen Pendamping**: Dosen yang melakukan validasi awal proposal
- **Dosen Universitas**: Dosen yang melakukan validasi akhir sebelum masuk ke Pimpinan PT

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

### **3. Hasil Semi Final oleh Operator**
- **Form penilaian sama seperti review substantif**
- **Referensi:** Menampilkan 2 tabel penilaian dari reviewer substantif
- **Input operator:**
  - Skor 0-10 per kriteria (form dinamis sesuai skim)
  - Status final: Lolos Tingkat Universitas / Tidak Lolos
  - Nilai final: Otomatis dari penilaian (readonly)
  - **Dana yang dapat diberikan:** Input manual (opsional)
  - **Dosen Pendamping Universitas:** Dipilih jika lolos tingkat universitas
  - Catatan final: Catatan untuk mahasiswa (minimal 50 karakter jika diisi)
- **Data disimpan di tabel `hasil_semi_finals`:**
  - `status_final`: lolos_tingkat_universitas / tidak_lolos
  - `nilai` (decimal): Nilai akhir (0-100.00)
  - `skor_per_kriteria` (JSON): Array skor dari operator
  - `dana_yang_dapat_diberikan` (decimal): Dana yang dapat diberikan
  - `catatan_final` (text): Catatan untuk mahasiswa
  - `id_pt`: ID operator yang melakukan penilaian

### **4. Hasil Final oleh Pimpinan PT**
- **Form penilaian sama seperti review substantif**
- **Referensi:** Menampilkan 2 tabel penilaian dari reviewer substantif
- **Input Pimpinan PT:**
  - Skor 0-10 per kriteria (form dinamis sesuai skim)
  - **Status PIMNAS:** Lolos / Tidak Lolos
  - **Status Pendanaan:** Lolos / Tidak Lolos
  - **Dana yang Didapatkan:**
    - Jika `status_pendanaan` = `lolos` → Input 0-15,000,000 (wajib, editable)
    - Jika `status_pendanaan` = `tidak_lolos` → Otomatis 0 (tidak bisa diubah)
  - Nilai final: Otomatis dari penilaian (readonly)
  - Catatan final: Catatan untuk mahasiswa
- **Data disimpan di tabel `hasil_finals`:**
  - `status_pimnas`: lolos / tidak_lolos
  - `status_pendanaan`: lolos / tidak_lolos
  - `dana_yang_didapatkan` (decimal): 0-15,000,000 (jika lolos pendanaan), 0 (jika tidak)
  - `nilai` (decimal): Nilai akhir (0-100.00)
  - `skor_per_kriteria` (JSON): Array skor dari Pimpinan PT
  - `catatan_final` (text): Catatan untuk mahasiswa
  - `id_pimpinan_pt`: ID Pimpinan PT yang melakukan penilaian
- **Status proposal final:**
  - `lolos_pimnas_pendanaan`: Lolos PIMNAS dan Lolos Pendanaan
  - `lolos_pimnas_tidak_pendanaan`: Lolos PIMNAS tapi Tidak Lolos Pendanaan
  - `tidak_lolos_pimnas_lolos_pendanaan`: Tidak Lolos PIMNAS tapi Lolos Pendanaan
  - `tidak_lolos`: Tidak Lolos keduanya

### **5. Tampilan Hasil Final untuk Mahasiswa**
- **Popup "Hasil Final"** di halaman lihat proposal
- **Menampilkan:**
  - Status PIMNAS (Lolos/Tidak Lolos)
  - Status Pendanaan (Lolos/Tidak Lolos)
  - Nilai final
  - **Dana yang didapatkan** (jika status pendanaan = lolos, range 0-15,000,000)
  - Catatan final

---

## 🎯 KESIMPULAN

### **Flow Dokumen:**
1. **Upload** → Mahasiswa upload PDF ke `storage/app/public/proposals/`
2. **Validasi** → Dosen Pendamping view & validasi proposal
3. **Assignment** → Operator assign reviewer
4. **Review Administratif** → Reviewer mengisi checklist dinamis sesuai skim
5. **Review Substantif** → 2 reviewer memberikan skor 0-10 per kriteria (dinamis sesuai skim)
6. **Revisi** → Mahasiswa upload revisi (jika perlu)
7. **Hasil Semi Final** → Operator melakukan penilaian semi final dengan form dinamis, input dana, dan assign Dosen Universitas
8. **Revisi Akhir** → Mahasiswa upload revisi akhir (jika lolos tingkat universitas)
9. **Validasi Akhir** → Dosen Universitas validasi akhir proposal, prioritas view revisi akhir jika ada
10. **Hasil Final** → Pimpinan PT melakukan penilaian final dengan form dinamis, input status PIMNAS, status pendanaan, dan dana (0-15M jika lolos pendanaan)
11. **Akses Hasil** → Mahasiswa melihat hasil final termasuk status PIMNAS, status pendanaan, dan dana yang didapatkan

### **Akses Dokumen:**
- **Satu file PDF** disimpan di storage
- **Semua role** mengakses file yang sama melalui `Storage::url()`
- **Access control** di level controller (authorization)
- **File tidak di-copy** untuk setiap user, hanya path yang di-share

### **Sistem Penilaian:**
- ✅ **Checklist administratif dinamis** berdasarkan skim proposal
- ✅ **Kriteria substantif dinamis** dengan struktur hierarkis
- ✅ **Perhitungan nilai otomatis** untuk review substantif, hasil semi final, dan hasil final
- ✅ **Dua reviewer substantif** memberikan penilaian terpisah
- ✅ **Operator melakukan penilaian semi final** dengan referensi dari reviewer
- ✅ **Dosen Universitas melakukan validasi akhir** dengan prioritas view revisi akhir
- ✅ **Pimpinan PT melakukan penilaian final** dengan status PIMNAS dan pendanaan terpisah
- ✅ **Field dana yang dapat diberikan** (operator) dan **dana yang didapatkan** (Pimpinan PT, 0-15M jika lolos pendanaan)

### **Keamanan:**
- ✅ File disimpan di `storage/app/public/` (public disk)
- ✅ Access control di controller level
- ✅ Authorization check sebelum akses file
- ✅ Validasi format dan ukuran file
- ✅ Validasi skor 0-10 di frontend dan backend
- ✅ Validasi jumlah kriteria sesuai dengan skim

---

**Versi:** 3.0.0  
**Tanggal Update:** 2025-11-17  
**Author:** Pramajaya  
**Perubahan Utama:**
- Sistem penilaian administratif dinamis berdasarkan skim
- Sistem penilaian substantif dinamis dengan scoring 0-10
- Hasil semi final oleh Operator dengan assign Dosen Universitas
- Revisi akhir oleh Mahasiswa (terpisah dari revisi biasa)
- Validasi akhir oleh Dosen Universitas dengan prioritas view revisi akhir
- Hasil final oleh Pimpinan PT dengan status PIMNAS dan pendanaan terpisah
- Field dana yang didapatkan (0-15M) dengan validasi berdasarkan status pendanaan
- Manajemen akun oleh Pimpinan PT untuk semua user types

