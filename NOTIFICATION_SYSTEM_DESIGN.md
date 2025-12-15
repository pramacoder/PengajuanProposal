# 📢 Desain Sistem Notifikasi untuk Mahasiswa

## 🎯 Overview

Dokumen ini menjelaskan desain sistem notifikasi komprehensif untuk mahasiswa dalam sistem pengajuan proposal PKM. Sistem notifikasi dirancang untuk memberikan informasi real-time kepada mahasiswa tentang status proposal mereka dari awal pendaftaran hingga hasil final.

---

## 📋 Daftar Notifikasi Berdasarkan Fase

### **FASE 0: RUANG KONTROL (Pendaftaran Dibuka)**

#### **1. Pendaftaran Proposal Dibuka**
- **Trigger:** Operator membuka ruang kontrol (status_pendaftaran = 'terbuka')
- **Type:** `success`
- **Title:** "Pendaftaran Proposal PKM Dibuka"
- **Message:** 
  ```
  Pendaftaran proposal PKM untuk tahun ajaran {tahun_ajaran} telah dibuka. 
  Periode: {tanggal_mulai} - {tanggal_selesai}. 
  Segera ajukan proposal Anda!
  ```
- **Target:** Semua mahasiswa aktif
- **Action:** Link ke halaman ajukan proposal
- **UI:** Badge hijau dengan icon calendar

---

### **FASE 1: UPLOAD PROPOSAL**

#### **2. Proposal Berhasil Dikirim**
- **Trigger:** Mahasiswa berhasil upload proposal
- **Type:** `success`
- **Title:** "Proposal Berhasil Dikirim"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' (Skim: {skim}) telah berhasil dikirim 
  dan sedang menunggu validasi dari dosen pendamping.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:** Badge hijau dengan icon check-circle

---

### **FASE 2: VALIDASI DOSEN PENDAMPING**

#### **3. Proposal Divalidasi - Siap Review**
- **Trigger:** Dosen set status = 'valid'
- **Type:** `success`
- **Title:** "Proposal Divalidasi - Siap Review"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' telah divalidasi oleh dosen pendamping 
  dan siap untuk proses review.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:** Badge hijau dengan icon check-circle

#### **4. Proposal Tidak Valid - Perlu Perbaikan** ⚠️
- **Trigger:** Dosen set status = 'tidak_valid'
- **Type:** `danger`
- **Title:** "Proposal Tidak Valid - Perlu Perbaikan"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' tidak dapat divalidasi oleh dosen pendamping. 
  Catatan: {catatan_dosen}
  Silakan perbaiki proposal Anda dan kirim ulang.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form revisi proposal
- **UI:** 
  - Badge merah dengan icon exclamation-triangle
  - Border merah di kiri notifikasi
  - Highlight dengan background merah muda (#fee)
  - Pesan catatan ditampilkan dengan jelas

---

### **FASE 3: REVIEW ADMINISTRATIF**

#### **5. Review Proposal Dimulai**
- **Trigger:** Operator assign reviewer administratif
- **Type:** `info`
- **Title:** "Review Proposal Dimulai"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' telah ditugaskan kepada reviewer 
  dan proses review administratif telah dimulai.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:** Badge biru dengan icon info-circle

#### **6. Review Administratif Selesai - Lolos**
- **Trigger:** Reviewer administratif submit review (lolos)
- **Type:** `success`
- **Title:** "Review Administratif Selesai - Lolos"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' telah lolos review administratif 
  dan akan dilanjutkan ke review substantif.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:** Badge hijau dengan icon check-circle

#### **7. Review Administratif - Tidak Lolos** ⚠️
- **Trigger:** Reviewer administratif submit review (tidak lolos)
- **Type:** `danger`
- **Title:** "Review Administratif - Tidak Lolos"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' tidak lolos review administratif. 
  Catatan reviewer: {catatan_reviewer}
  Silakan perbaiki proposal Anda sesuai catatan reviewer.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form revisi proposal
- **UI:**
  - Badge merah dengan icon times-circle
  - Border merah di kiri notifikasi
  - Highlight dengan background merah muda (#fee)
  - Catatan reviewer ditampilkan dengan jelas

---

### **FASE 4: REVIEW SUBSTANTIF**

#### **8. Review Substantif Selesai - Perlu Revisi**
- **Trigger:** Kedua reviewer substantif selesai review
- **Type:** `warning`
- **Title:** "Review Substantif Selesai - Perlu Revisi"
- **Message:**
  ```
  Review substantif untuk proposal Anda '{judul_proposal}' telah selesai. 
  Nilai Reviewer 1: {nilai1}, Nilai Reviewer 2: {nilai2}
  Proposal Anda memerlukan revisi. Silakan periksa catatan reviewer 
  dan upload file revisi.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form upload revisi
- **UI:** Badge kuning dengan icon exclamation-triangle

---

### **FASE 5: HASIL SEMI FINAL**

#### **9. Lolos Semi Final - Upload Revisi Akhir**
- **Trigger:** Operator set hasil semi final = 'lolos_tingkat_universitas'
- **Type:** `success`
- **Title:** "Lolos Semi Final - Upload Revisi Akhir"
- **Message:**
  ```
  Selamat! Proposal Anda '{judul_proposal}' telah lolos penilaian semi final 
  tingkat universitas. Nilai: {nilai}
  Dana yang dapat diberikan: Rp {dana}
  Silakan upload revisi akhir proposal Anda.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form upload revisi akhir
- **UI:** Badge hijau dengan icon check-circle dan icon upload

#### **10. Tidak Lolos Semi Final** ⚠️
- **Trigger:** Operator set hasil semi final = 'tidak_lolos_tingkat_universitas'
- **Type:** `danger`
- **Title:** "Tidak Lolos Semi Final"
- **Message:**
  ```
  Mohon maaf, proposal Anda '{judul_proposal}' tidak lolos penilaian semi final 
  tingkat universitas. Nilai: {nilai}
  Catatan: {catatan_operator}
  Terima kasih atas partisipasi Anda.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:**
  - Badge merah dengan icon times-circle
  - Border merah di kiri notifikasi
  - Highlight dengan background merah muda (#fee)
  - Pesan empati dan profesional
  - Catatan operator ditampilkan dengan jelas

---

### **FASE 6: REVISI AKHIR**

#### **11. Revisi Akhir Divalidasi**
- **Trigger:** Dosen universitas set validasi = 'valid'
- **Type:** `success`
- **Title:** "Revisi Akhir Divalidasi"
- **Message:**
  ```
  Revisi akhir proposal Anda '{judul_proposal}' telah divalidasi oleh dosen 
  pendamping universitas dan siap untuk penilaian final oleh Pimpinan PT.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail proposal
- **UI:** Badge hijau dengan icon check-circle

#### **12. Revisi Akhir Tidak Valid** ⚠️
- **Trigger:** Dosen universitas set validasi = 'tidak_valid'
- **Type:** `danger`
- **Title:** "Revisi Akhir Tidak Valid"
- **Message:**
  ```
  Revisi akhir proposal Anda '{judul_proposal}' tidak dapat divalidasi oleh 
  dosen pendamping universitas. Catatan: {catatan_dosen}
  Silakan perbaiki dan upload ulang revisi akhir.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form upload revisi akhir
- **UI:**
  - Badge merah dengan icon exclamation-triangle
  - Border merah di kiri notifikasi
  - Highlight dengan background merah muda (#fee)
  - Catatan dosen ditampilkan dengan jelas

---

### **FASE 7: HASIL FINAL**

#### **13. Lolos PIMNAS dan Mendapat Pendanaan** 🎉
- **Trigger:** Pimpinan PT set status_pimnas = 'lolos' dan status_pendanaan = 'lolos'
- **Type:** `success`
- **Title:** "Selamat! Proposal Lolos PIMNAS dan Mendapat Pendanaan"
- **Message:**
  ```
  Selamat! Proposal Anda '{judul_proposal}' telah lolos PIMNAS dan mendapatkan pendanaan. 
  Nilai: {nilai}
  Dana yang didapatkan: Rp {dana_yang_didapatkan}
  Catatan: {catatan_pimpinan_pt}
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail hasil final
- **UI:**
  - Badge hijau dengan icon trophy dan icon money-bill
  - Highlight dengan background hijau muda (#efe)
  - Animasi confetti (opsional)

#### **14. Lolos PIMNAS (Tidak Mendapat Pendanaan)**
- **Trigger:** Pimpinan PT set status_pimnas = 'lolos' dan status_pendanaan = 'tidak_lolos'
- **Type:** `success`
- **Title:** "Selamat! Proposal Lolos PIMNAS"
- **Message:**
  ```
  Selamat! Proposal Anda '{judul_proposal}' telah lolos PIMNAS, 
  namun tidak mendapatkan pendanaan. Nilai: {nilai}
  Catatan: {catatan_pimpinan_pt}
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail hasil final
- **UI:** Badge hijau dengan icon trophy

#### **15. Mendapat Pendanaan (Tidak Lolos PIMNAS)**
- **Trigger:** Pimpinan PT set status_pimnas = 'tidak_lolos' dan status_pendanaan = 'lolos'
- **Type:** `warning`
- **Title:** "Proposal Mendapat Pendanaan"
- **Message:**
  ```
  Proposal Anda '{judul_proposal}' tidak lolos PIMNAS, namun mendapatkan pendanaan. 
  Nilai: {nilai}
  Dana yang didapatkan: Rp {dana_yang_didapatkan}
  Catatan: {catatan_pimpinan_pt}
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail hasil final
- **UI:** Badge kuning dengan icon money-bill

#### **16. Tidak Lolos Final** ⚠️
- **Trigger:** Pimpinan PT set status_pimnas = 'tidak_lolos' dan status_pendanaan = 'tidak_lolos'
- **Type:** `danger`
- **Title:** "Proposal Tidak Lolos Final"
- **Message:**
  ```
  Mohon maaf, proposal Anda '{judul_proposal}' tidak lolos PIMNAS 
  dan tidak mendapatkan pendanaan. Nilai: {nilai}
  Catatan: {catatan_pimpinan_pt}
  Terima kasih atas partisipasi Anda.
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke detail hasil final
- **UI:**
  - Badge merah dengan icon times-circle
  - Border merah di kiri notifikasi
  - Highlight dengan background merah muda (#fee)
  - Pesan empati dan profesional
  - Catatan pimpinan PT ditampilkan dengan jelas

---

### **FASE 8: DEADLINE REMINDER**

#### **17. Pengingat Deadline**
- **Trigger:** Sistem check deadline (1 hari, 3 hari, 7 hari sebelum deadline)
- **Type:** `info` (7 hari), `warning` (3 hari), `danger` (1 hari atau hari ini)
- **Title:** "Pengingat Deadline"
- **Message:**
  ```
  Pengingat: Deadline upload revisi proposal '{judul_proposal}' 
  adalah {tanggal_deadline} ({days_left} hari lagi)
  ```
- **Target:** Semua anggota tim proposal
- **Action:** Link ke form upload revisi/revisi akhir
- **UI:**
  - Badge sesuai urgency (biru/kuning/merah)
  - Icon clock dengan animasi pulse jika urgent

---

## 🎨 Desain UI Notifikasi Negatif (Penolakan)

### **Prinsip Desain:**
1. **Empati:** Gunakan bahasa yang sopan dan empati
2. **Klaritas:** Jelaskan alasan penolakan dengan jelas
3. **Aksi:** Berikan langkah selanjutnya yang jelas
4. **Visual:** Gunakan warna dan icon yang sesuai

### **Styling CSS untuk Notifikasi Negatif:**

```css
/* Notifikasi Negatif */
.notification-item.negative {
    background-color: #fee;
    border-left: 4px solid #dc3545;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
}

.notification-item.negative .notification-icon-small {
    background-color: #dc3545;
    color: white;
}

.notification-item.negative .notification-title {
    color: #dc3545;
    font-weight: 600;
}

.notification-item.negative .notification-message {
    color: #721c24;
}

.notification-item.negative .catatan-box {
    background-color: #fff;
    border: 1px solid #dc3545;
    border-radius: 4px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-style: italic;
}

.notification-item.negative .action-button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    margin-top: 0.5rem;
    cursor: pointer;
}

.notification-item.negative .action-button:hover {
    background-color: #c82333;
}
```

### **Struktur HTML Notifikasi Negatif:**

```html
<div class="notification-item negative unread" data-id="{id}">
    <div class="notification-content">
        <div class="notification-icon-small danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="notification-text">
            <div class="notification-title">
                {title}
            </div>
            <div class="notification-message">
                {message}
            </div>
            @if(catatan)
            <div class="catatan-box">
                <strong>Catatan:</strong> {catatan}
            </div>
            @endif
            <div class="notification-time">
                {time_ago}
            </div>
            <button class="action-button" onclick="handleAction('{action}')">
                {action_text}
            </button>
        </div>
    </div>
</div>
```

---

## 🔄 Flow Notifikasi

```
RUANG KONTROL DIBUKA
    ↓
PROPOSAL UPLOADED
    ↓
VALIDASI DOSEN
    ├─ VALID → REVIEW ADMINISTRATIF
    └─ TIDAK VALID → REVISI PROPOSAL
        ↓
REVIEW ADMINISTRATIF
    ├─ LOLOS → REVIEW SUBSTANTIF
    └─ TIDAK LOLOS → REVISI PROPOSAL
        ↓
REVIEW SUBSTANTIF
    ↓
PERLU REVISI → UPLOAD REVISI
    ↓
HASIL SEMI FINAL
    ├─ LOLOS → UPLOAD REVISI AKHIR
    └─ TIDAK LOLOS → END
        ↓
VALIDASI AKHIR DOSEN
    ├─ VALID → HASIL FINAL
    └─ TIDAK VALID → REVISI AKHIR ULANG
        ↓
HASIL FINAL
    ├─ LOLOS PIMNAS + PENDANAAN → END
    ├─ LOLOS PIMNAS → END
    ├─ PENDANAAN → END
    └─ TIDAK LOLOS → END
```

---

## 📱 Implementasi

### **1. Service Layer**
- File: `app/Services/NotificationService.php`
- Method-method untuk setiap fase notifikasi
- Helper untuk format pesan

### **2. Controller Integration**
- Trigger notifikasi di setiap controller yang mengubah status proposal
- Contoh: `DosenController@validasiProposal`, `OperatorController@updateHasilSemiFinal`, dll

### **3. View Component**
- Component untuk menampilkan notifikasi dengan styling khusus untuk notifikasi negatif
- File: `resources/views/components/notification-item.blade.php`

### **4. JavaScript Handler**
- Handler untuk action button di notifikasi
- Auto-refresh notifikasi setiap 30 detik
- Mark as read ketika notifikasi diklik

---

## ✅ Checklist Implementasi

- [x] Service method untuk semua fase notifikasi
- [ ] Integrasi di controller (validasi dosen, review, hasil semi final, hasil final)
- [ ] UI component untuk notifikasi negatif
- [ ] Update view notifikasi di layout mahasiswa
- [ ] JavaScript handler untuk action button
- [ ] Test notifikasi untuk semua skenario
- [ ] Dokumentasi untuk developer

---

## 🎯 Best Practices

1. **Konsistensi:** Gunakan format pesan yang konsisten
2. **Timeliness:** Kirim notifikasi segera setelah event terjadi
3. **Clarity:** Pesan harus jelas dan mudah dipahami
4. **Empathy:** Untuk notifikasi negatif, gunakan bahasa yang empati
5. **Actionable:** Setiap notifikasi harus memiliki action yang jelas
6. **Non-intrusive:** Notifikasi tidak mengganggu workflow user

---

**Versi:** 1.0.0  
**Tanggal:** 2025-11-17  
**Author:** Pramajaya

