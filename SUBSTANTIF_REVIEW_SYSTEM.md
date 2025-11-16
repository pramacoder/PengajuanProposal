# Sistem Penilaian Substantif PKM Dinamis

## 📋 Overview

Sistem penilaian substantif PKM telah diubah dari text-only menjadi sistem penilaian terstruktur dengan kriteria dinamis sesuai skim proposal. Setiap reviewer substantif dapat memberikan skor 0-10 untuk setiap kriteria, dan sistem akan menghitung nilai akhir secara otomatis.

---

## 🎯 Fitur Utama

### **1. Penilaian Substantif Dinamis**
- Kriteria penilaian disesuaikan dengan skim proposal
- Setiap kriteria memiliki bobot yang berbeda
- Skor input: 0-10 (dapat menggunakan desimal, step 0.1)
- Perhitungan otomatis: Nilai = Bobot × Skor

### **2. Dua Reviewer Substantif**
- Setiap proposal memiliki 2 reviewer substantif
- Masing-masing reviewer memberikan penilaian terpisah
- Data penilaian disimpan terpisah per reviewer

### **3. Hasil Final dengan Referensi**
- Operator dapat melihat penilaian dari 2 reviewer substantif sebagai referensi
- Operator melakukan penilaian sendiri dengan form yang sama
- Nilai final dihitung otomatis dari penilaian operator

### **4. Perhitungan Nilai**
- **Total Nilai**: Sum dari (Bobot × Skor) untuk semua kriteria (0-1000)
- **Nilai Akhir**: Total Nilai / 10 (0-100.00)
- **Konversi**: 789 → 78.9 (dibagi 10)

---

## 📊 Struktur Data

### **Tabel: nilai_substantifs**

**Field Baru:**
- `skor_per_kriteria` (JSON): Array skor per kriteria
  ```json
  {
    "0": 8.5,
    "1": 7.0,
    "2": 9.0,
    ...
  }
  ```
- `total_nilai` (decimal 6,2): Total nilai sebelum dikonversi (0-1000)
- `nilai_akhir` (decimal 5,2): Nilai akhir setelah dikonversi (0-100.00)

**Field Existing:**
- `note_substantif` (text): Catatan review (tetap ada)

### **Tabel: hasil_finals**

**Field Baru:**
- `skor_per_kriteria` (JSON): Array skor per kriteria dari operator

**Field Existing:**
- `nilai` (decimal 5,2): Nilai final (0-100.00)

---

## 🔄 Flow Penilaian

### **1. Review Substantif oleh Reviewer**

```
Reviewer Substantif 1/2
    ↓
Buka halaman review substantif
    ↓
Lihat kriteria penilaian sesuai skim
    ↓
Input skor 0-10 untuk setiap kriteria
    ↓
Sistem hitung nilai otomatis (real-time)
    ↓
Tulis catatan review
    ↓
Submit → Data tersimpan di nilai_substantifs
```

**Data yang disimpan:**
- `skor_per_kriteria`: Array skor per kriteria
- `total_nilai`: Total nilai (0-1000)
- `nilai_akhir`: Nilai akhir (0-100.00)
- `note_substantif`: Catatan review

### **2. Hasil Final oleh Operator**

```
Operator
    ↓
Buka halaman detail hasil final
    ↓
Lihat 2 tabel penilaian substantif (referensi)
    ↓
Lihat form penilaian hasil final
    ↓
Input skor 0-10 untuk setiap kriteria
    ↓
Sistem hitung nilai otomatis (real-time)
    ↓
Pilih status final (lolos/tidak_lolos)
    ↓
Tulis catatan final
    ↓
Submit → Data tersimpan di hasil_finals
```

**Data yang disimpan:**
- `skor_per_kriteria`: Array skor per kriteria
- `nilai`: Nilai final (0-100.00) - otomatis dari penilaian
- `status_final`: lolos / tidak_lolos
- `catatan_final`: Catatan untuk mahasiswa

---

## 📁 File yang Dibuat/Dimodifikasi

### **File Baru:**
1. `config/review_substantif_criteria.php` - Config kriteria penilaian per skim
2. `database/migrations/2025_01_22_000001_add_scoring_fields_to_nilai_substantifs_table.php`
3. `database/migrations/2025_01_22_000002_add_scoring_fields_to_hasil_finals_table.php`
4. `SUBSTANTIF_REVIEW_SYSTEM.md` - Dokumentasi ini

### **File yang Dimodifikasi:**
1. `app/Helpers/ProposalHelper.php`
   - Method `getSubstantifCriteria($skim)`: Ambil kriteria berdasarkan skim
   - Method `calculateSubstantifScore($criteria, $skorPerKriteria)`: Hitung nilai

2. `app/Http/Controllers/ReviewerController.php`
   - Method `detailProposalSubstantif()`: Pass kriteria ke view
   - Method `submitReviewSubstantif()`: Handle form penilaian baru

3. `app/Http/Controllers/OperatorController.php`
   - Method `detailHasilFinal()`: Pass kriteria dan nilai substantif ke view
   - Method `updateHasilFinal()`: Handle form penilaian hasil final

4. `app/Models/NilaiSubstantif.php`
   - Tambah field: `skor_per_kriteria`, `total_nilai`, `nilai_akhir`
   - Tambah casts untuk JSON dan decimal

5. `app/Models/HasilFinal.php`
   - Tambah field: `skor_per_kriteria`
   - Tambah cast untuk JSON

6. `resources/views/reviewer/detail_proposal_substantif.blade.php`
   - Form penilaian dinamis dengan tabel kriteria
   - JavaScript untuk perhitungan real-time

7. `resources/views/operator/detail_hasil_final.blade.php`
   - Tampilkan 2 tabel penilaian substantif (referensi)
   - Form penilaian hasil final dengan kriteria dinamis
   - JavaScript untuk perhitungan real-time

---

## 📊 Mapping Kriteria per Skim

### **PKM-RE & PKM-RSH** (9 kriteria)
1. Kreativitas - Gagasan (15%)
2. Kreativitas - Penyajian rumusan masalah (15%)
3. Kreativitas - Perbandingan dengan riset terdahulu (10%)
4. Kesesuaian dan Kemutakhiran Metode Riset (15%)
5. Potensi Program - Kontribusi Perkembangan Ilmu dan Teknologi (10%)
6. Potensi Program - Sintesis Telaah Literatur (15%)
7. Potensi Program - Kemanfaatan (10%)
8. Penjadwalan Kegiatan dan Personalia (5%)
9. Penyusunan Anggaran Biaya (5%)

### **PKM-PM & PKM-PI** (7 kriteria)
1. Kreativitas - Perumusan Masalah / Identifikasi Permasalahan (10%)
2. Kreativitas - Ketepatan Solusi (20%)
3. Ketepatan Masyarakat Mitra / Mitra Program (15%)
4. Potensi Program - Potensi Nilai Tambah (25%)
5. Potensi Program - Potensi Keberlanjutan (20%)
6. Penjadwalan Kegiatan dan Personalia (5%)
7. Penyusunan Anggaran Biaya (5%)

### **PKM-KC** (7 kriteria)
1. Kreativitas - Gagasan (20%)
2. Kreativitas - Kemutakhiran iptek (20%)
3. Kesesuaian Tahap Pelaksanaan (15%)
4. Potensi Program - Kontribusi produk luaran (25%)
5. Potensi Program - Potensi Publikasi (10%)
6. Penjadwalan Kegiatan dan Personalia (5%)
7. Penyusunan Anggaran Biaya (5%)

### **PKM-K** (7 kriteria)
1. Kreativitas - Gagasan Usaha (15%)
2. Kreativitas - Keunggulan Produk (20%)
3. Rancangan Usaha (20%)
4. Potensi Program - Potensi Pelaksanaan dan Profit (20%)
5. Potensi Program - Potensi Keberlanjutan Usaha (15%)
6. Penjadwalan Kegiatan dan Personalia (5%)
7. Penyusunan Anggaran Biaya (5%)

### **PKM-KI** (7 kriteria)
1. Kreativitas - Urgensi Permasalahan (15%)
2. Kreativitas - Kreativitas Gagasan Solusi (25%)
3. Kesesuaian Tahap Pelaksanaan (15%)
4. Potensi Produk (10%)
5. Ketepatan Iptek, Standar, Regulasi (25%)
6. Penjadwalan Kegiatan dan Personalia (5%)
7. Penyusunan Anggaran Biaya (5%)

### **PKM-VGK** (8 kriteria)
1. Kreativitas - Kreativitas Gagasan (20%)
2. Kreativitas - Kreativitas Komunikasi Video (20%)
3. Kesesuaian Tahap Pelaksanaan (15%)
4. Potensi Program - Kontribusi Gagasan (15%)
5. Potensi Program - Potensi Efektivitas Video (15%)
6. Sumber Informasi (5%)
7. Penjadwalan Kegiatan dan Personalia (5%)
8. Penyusunan Anggaran Biaya (5%)

### **PKM-AI** (7 kriteria)
1. JUDUL: Kesesuaian isi dan judul (5%)
2. ABSTRAK/ABSTRACT (10%)
3. PENDAHULUAN (15%)
4. METODE (25%)
5. HASIL DAN PEMBAHASAN (30%)
6. KESIMPULAN (10%)
7. DAFTAR PUSTAKA (5%)

### **PKM-GFT** (13 kriteria)
1-3. Format Makalah (10% total)
4-6. Gagasan (35% total)
7-10. Tahapan solusi (30% total)
11-12. Sumber informasi (15% total)
13. Kesimpulan (10%)

---

## 🧮 Rumus Perhitungan

### **Per Kriteria:**
```
Nilai = Bobot × Skor
```

**Contoh:**
- Kriteria: "Kreativitas - Gagasan"
- Bobot: 15%
- Skor: 8.5
- Nilai: 15 × 8.5 = 127.5

### **Total Nilai:**
```
Total Nilai = Σ (Bobot × Skor) untuk semua kriteria
```

**Range:** 0 - 1000 (jika semua skor = 10)

### **Nilai Akhir:**
```
Nilai Akhir = Total Nilai / 10
```

**Range:** 0.00 - 100.00

**Contoh:**
- Total Nilai: 789
- Nilai Akhir: 789 / 10 = 78.90

---

## 🔍 Contoh Perhitungan

### **Contoh: PKM-PM (7 kriteria)**

| No | Kriteria | Bobot | Skor | Nilai |
|----|----------|-------|------|-------|
| 1 | Perumusan Masalah | 10 | 3 | 30 |
| 2 | Ketepatan Solusi | 20 | 5 | 100 |
| 3 | Ketepatan Masyarakat Mitra | 15 | 5 | 75 |
| 4 | Potensi Nilai Tambah | 25 | 5 | 125 |
| 5 | Potensi Keberlanjutan | 20 | 6 | 120 |
| 6 | Penjadwalan | 5 | 7 | 35 |
| 7 | Anggaran | 5 | 6 | 30 |
| **Total** | | **100** | | **515** |
| **Nilai Akhir** | | | | **51.50** |

**Perhitungan:**
- Total Nilai: 515
- Nilai Akhir: 515 / 10 = 51.50

---

## 🎨 UI Features

### **Form Penilaian Reviewer:**
- Tabel dengan kolom: No, Kriteria, Bobot, Skor (input), Nilai (auto-calculate)
- Perhitungan real-time saat input skor
- Total nilai dan nilai akhir ditampilkan di footer tabel
- Validasi: semua skor harus diisi (0-10)

### **Halaman Hasil Final:**
- **2 Tabel Referensi**: Menampilkan penilaian dari Reviewer 1 dan Reviewer 2
- **Form Penilaian Operator**: Form yang sama dengan reviewer untuk input penilaian final
- **Nilai Final**: Auto-calculate dan auto-fill ke input field (readonly)

---

## ✅ Validasi

### **Review Substantif:**
- ✅ Semua skor harus diisi (0-10)
- ✅ Catatan minimal 50 karakter
- ✅ Skor harus numeric, min 0, max 10

### **Hasil Final:**
- ✅ Semua skor harus diisi (0-10)
- ✅ Status final harus dipilih (lolos/tidak_lolos)
- ✅ Nilai final auto-calculate dari penilaian
- ✅ Jumlah skor harus sesuai dengan jumlah kriteria

---

## 🔄 Backward Compatibility

### **Data Review Lama:**
- Data review substantif lama (hanya text) tetap bisa dibaca
- Field `note_substantif` tetap ada dan tetap digunakan
- Jika `skor_per_kriteria` kosong/null, form akan kosong (user bisa input baru)

### **Migration:**
- Field baru ditambahkan dengan `nullable()`
- Tidak ada data yang hilang
- Data lama tetap bisa diakses

---

## 🧪 Testing Checklist

1. ✅ Test dengan proposal PKM-RE → kriteria harus sesuai
2. ✅ Test dengan proposal PKM-PM → kriteria harus sesuai
3. ✅ Test dengan proposal PKM-AI → kriteria harus sesuai
4. ✅ Test input skor 0-10 → nilai harus terhitung otomatis
5. ✅ Test submit review substantif → data harus tersimpan
6. ✅ Test 2 reviewer substantif → data harus terpisah
7. ✅ Test hasil final → 2 tabel referensi harus tampil
8. ✅ Test penilaian hasil final → nilai harus auto-calculate
9. ✅ Test edit review existing → data harus ter-load
10. ✅ Test validasi form → error harus muncul jika tidak valid

---

## 📌 Catatan Penting

1. **Skor Range**: 0-10 (dapat desimal, step 0.1)
2. **Bobot Total**: Selalu 100% untuk semua skim
3. **Total Maksimal**: 1000 (jika semua skor = 10)
4. **Nilai Akhir**: Selalu dibagi 10 untuk konversi ke 0-100
5. **Config Cache**: Setelah mengubah config, jalankan `php artisan config:clear`

---

## 🚀 Deployment Checklist

- [ ] Backup database
- [ ] Jalankan migration: `php artisan migrate`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Test dengan proposal berbagai skim
- [ ] Test dengan 2 reviewer substantif
- [ ] Test hasil final dengan referensi
- [ ] Monitor error logs

---

**Versi:** 1.0.0  
**Tanggal:** 2025-01-XX  
**Author:** Pramajaya

