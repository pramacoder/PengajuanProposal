# 📋 Panduan Implementasi Notifikasi

## ✅ Yang Sudah Dilakukan

1. ✅ **NotificationService diperluas** dengan method untuk semua fase proposal
2. ✅ **Dokumentasi desain** notifikasi lengkap (`NOTIFICATION_SYSTEM_DESIGN.md`)
3. ✅ **ProposalController** - Notifikasi proposal uploaded

## 🔧 Yang Perlu Dilakukan

### **1. DosenController - Validasi Proposal**

**File:** `app/Http/Controllers/DosenController.php`

**Method:** `validasiProposal()` atau method yang mengubah status validasi

**Tambahkan setelah update status:**

```php
use App\Services\NotificationService;

// Setelah update proposal status
$notificationService = new NotificationService();
$notificationService->notifyValidasiDosen(
    $proposal,
    $request->status_validasi, // 'valid' atau 'tidak_valid'
    $request->catatan ?? null
);
```

---

### **2. OperatorController - Assign Reviewer**

**File:** `app/Http/Controllers/OperatorController.php`

**Method:** `assignReviewer()` atau method yang assign reviewer

**Tambahkan setelah assign reviewer:**

```php
use App\Services\NotificationService;

// Setelah assign reviewer
$notificationService = new NotificationService();
$notificationService->notifyReviewerAssigned($proposal);
```

---

### **3. ReviewerController - Review Administratif Selesai**

**File:** `app/Http/Controllers/ReviewerController.php`

**Method:** Method yang submit review administratif

**Tambahkan setelah submit review:**

```php
use App\Services\NotificationService;

// Setelah submit review administratif
$notificationService = new NotificationService();
$lolos = $nilaiAdministratif->status === 'lolos'; // Sesuaikan dengan logika Anda
$notificationService->notifyReviewAdministratifSelesai(
    $proposal,
    $lolos,
    $nilaiAdministratif->catatan ?? null
);
```

---

### **4. ReviewerController - Review Substantif Selesai**

**File:** `app/Http/Controllers/ReviewerController.php`

**Method:** Method yang submit review substantif

**Tambahkan setelah kedua reviewer selesai:**

```php
use App\Services\NotificationService;

// Setelah kedua reviewer substantif selesai
$notificationService = new NotificationService();

// Ambil nilai dari kedua reviewer
$nilai1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
$nilai2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();

$notificationService->notifyReviewSubstantifSelesai(
    $proposal,
    [
        'reviewer1' => $nilai1->nilai_akhir ?? null,
        'reviewer2' => $nilai2->nilai_akhir ?? null
    ]
);
```

---

### **5. OperatorController - Hasil Semi Final**

**File:** `app/Http/Controllers/OperatorController.php`

**Method:** `updateHasilSemiFinal()`

**Tambahkan setelah save hasil semi final:**

```php
use App\Services\NotificationService;

// Setelah save hasil semi final
$notificationService = new NotificationService();
$notificationService->notifyHasilSemiFinal(
    $proposal,
    $request->status_final, // 'lolos_tingkat_universitas' atau 'tidak_lolos_tingkat_universitas'
    $hasilSemiFinal->nilai ?? null,
    $request->catatan_final ?? null,
    $request->dana_yang_dapat_diberikan ?? null
);
```

---

### **6. DosenController - Validasi Akhir**

**File:** `app/Http/Controllers/DosenController.php`

**Method:** Method yang validasi akhir proposal

**Tambahkan setelah validasi akhir:**

```php
use App\Services\NotificationService;

// Setelah validasi akhir
$notificationService = new NotificationService();
$notificationService->notifyValidasiAkhirDosen(
    $proposal,
    $request->status_validasi, // 'valid' atau 'tidak_valid'
    $request->catatan ?? null
);
```

---

### **7. PimpinanPTController - Hasil Final**

**File:** `app/Http/Controllers/PimpinanPTController.php`

**Method:** `updateHasilFinal()`

**Tambahkan setelah save hasil final:**

```php
use App\Services\NotificationService;

// Setelah save hasil final
$notificationService = new NotificationService();
$notificationService->notifyHasilFinalLengkap(
    $proposal,
    $request->status_pimnas, // 'lolos' atau 'tidak_lolos'
    $request->status_pendanaan, // 'lolos' atau 'tidak_lolos'
    $hasilFinal->nilai ?? 0,
    $request->dana_yang_didapatkan ?? 0,
    $request->catatan_final ?? null
);
```

---

### **8. OperatorController - Ruang Kontrol Dibuka**

**File:** `app/Http/Controllers/OperatorController.php`

**Method:** `ruangKontrol()` atau method yang update status ruang kontrol

**Tambahkan setelah update status ruang kontrol menjadi 'terbuka':**

```php
use App\Services\NotificationService;

// Setelah update status_pendaftaran menjadi 'terbuka'
if ($ruangKontrol->status_pendaftaran === 'terbuka' && $oldStatus !== 'terbuka') {
    $notificationService = new NotificationService();
    $notificationService->notifyRuangKontrolDibuka($ruangKontrol);
}
```

---

## 🎨 Update UI Notifikasi

### **1. Update View Notifikasi**

**File:** `resources/views/mainlayout/app.blade.php` atau file layout mahasiswa

**Tambahkan styling untuk notifikasi negatif:**

```css
/* Tambahkan di section styles */
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
```

### **2. Update JavaScript Render Notifikasi**

**File:** `resources/views/mainlayout/app.blade.php`

**Update method `renderNotifications()` untuk menampilkan notifikasi negatif:**

```javascript
renderNotifications() {
    // ... existing code ...
    
    container.innerHTML = this.notifications.map(notification => {
        const isNegative = notification.data?.is_negative || false;
        const hasCatatan = notification.data?.catatan;
        
        return `
            <div class="notification-item ${notification.unread ? 'unread' : ''} ${isNegative ? 'negative' : ''}" data-id="${notification.id}">
                <div class="notification-content">
                    <div class="notification-icon-small ${notification.type}">
                        <i class="fas fa-${this.getIconForType(notification.type)}"></i>
                    </div>
                    <div class="notification-text">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        ${hasCatatan ? `
                            <div class="catatan-box">
                                <strong>Catatan:</strong> ${notification.data.catatan}
                            </div>
                        ` : ''}
                        <div class="notification-time">${notification.time}</div>
                        ${notification.data?.action ? `
                            <button class="btn btn-sm btn-${isNegative ? 'danger' : 'primary'}" 
                                    onclick="window.location.href='${this.getActionUrl(notification.data.action, notification.data.proposal_id)}'">
                                ${this.getActionText(notification.data.action)}
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // ... rest of code ...
}

getActionUrl(action, proposalId) {
    const routes = {
        'view_proposal': `/mahasiswa/proposal/${proposalId}`,
        'revisi_proposal': `/mahasiswa/proposal/${proposalId}/revisi`,
        'upload_revisi': `/mahasiswa/proposal/${proposalId}/revisi`,
        'upload_revisi_akhir': `/mahasiswa/proposal/${proposalId}/revisi-akhir`,
        'view_hasil_final': `/mahasiswa/proposal/${proposalId}`
    };
    return routes[action] || '#';
}

getActionText(action) {
    const texts = {
        'view_proposal': 'Lihat Proposal',
        'revisi_proposal': 'Revisi Proposal',
        'upload_revisi': 'Upload Revisi',
        'upload_revisi_akhir': 'Upload Revisi Akhir',
        'view_hasil_final': 'Lihat Hasil Final'
    };
    return texts[action] || 'Lihat Detail';
}
```

---

## 🧪 Testing

### **Test Case untuk Setiap Notifikasi:**

1. **Upload Proposal**
   - Upload proposal baru
   - Cek notifikasi muncul di dashboard mahasiswa

2. **Validasi Dosen (Valid)**
   - Dosen set valid
   - Cek notifikasi success muncul

3. **Validasi Dosen (Tidak Valid)**
   - Dosen set tidak valid dengan catatan
   - Cek notifikasi danger muncul dengan catatan
   - Cek styling negatif diterapkan

4. **Review Administratif**
   - Reviewer submit review
   - Cek notifikasi muncul sesuai hasil

5. **Review Substantif**
   - Kedua reviewer submit
   - Cek notifikasi dengan nilai muncul

6. **Hasil Semi Final**
   - Operator set hasil semi final
   - Cek notifikasi muncul dengan detail lengkap

7. **Hasil Final**
   - Pimpinan PT set hasil final
   - Cek notifikasi muncul dengan status PIMNAS dan pendanaan

8. **Ruang Kontrol**
   - Operator buka ruang kontrol
   - Cek semua mahasiswa mendapat notifikasi

---

## 📝 Catatan Penting

1. **Error Handling:** Selalu wrap notifikasi dalam try-catch agar tidak mengganggu flow utama
2. **Performance:** Notifikasi dikirim secara async jika memungkinkan
3. **Testing:** Test semua skenario termasuk edge cases
4. **Logging:** Log semua notifikasi yang dikirim untuk debugging

---

**Versi:** 1.0.0  
**Tanggal:** 2025-11-17

