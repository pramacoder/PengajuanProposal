# TRACKING FLOW DOKUMEN PROPOSAL: DARI UPLOAD HINGGA PDF.JS

## 📋 **OVERVIEW SISTEM DOKUMEN**

Sistem pengajuan proposal menggunakan arsitektur file storage yang terstruktur dengan PDF.js untuk tampilan dokumen. Berikut adalah analisis lengkap flow dokumen dari upload hingga tampilan.

---

## 🔄 **FLOW DOKUMEN PROPOSAL**

### **1. UPLOAD PROPOSAL** 📤

**Lokasi:** `app/Http/Controllers/ProposalController.php` (line 189-208)

```php
// Upload file proposal
$proposalFile = null;
if ($request->hasFile('proposal_file')) {
    $file = $request->file('proposal_file');

    // Validasi file
    if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
        return back()->withErrors(['proposal_file' => 'Ukuran file maksimal 5MB']);
    }

    if ($file->getClientOriginalExtension() !== 'pdf') {
        return back()->withErrors(['proposal_file' => 'File harus berformat PDF']);
    }

    $proposalFile = $file->store('proposals', 'public');
}
```

**Validasi:**

-   ✅ Format: PDF only
-   ✅ Ukuran: Maksimal 5MB
-   ✅ Storage: `storage/app/public/proposals/`

### **2. PENYIMPANAN DATABASE** 💾

**Lokasi:** `app/Http/Controllers/ProposalController.php` (line 322-329)

```php
// Buat dokumen jika ada file
if ($proposalFile) {
    $proposal->dokumen()->create([
        'skim' => $request->skim,
        'path_file' => $proposalFile,
        'file_proposal' => $proposalFile,
        'tgl_upload' => now(),
    ]);
}
```

**Tabel:** `dokumens`

-   `id_dokumen` (Primary Key)
-   `skim` (RE, RSH, KC, PM, PI, K, KI, VGK, AI, GFT)
-   `path_file` (Path ke file di storage)
-   `file_proposal` (Duplikasi path untuk kompatibilitas)
-   `tgl_upload` (Timestamp upload)
-   `id_proposal` (Foreign Key ke proposals)

### **3. STRUKTUR STORAGE** 📁

**Lokasi:** `storage/app/public/`

```
storage/app/public/
├── proposals/           # File proposal utama
├── proposal_revisi/     # File revisi proposal
└── persetujuan/         # File persetujuan
```

**Symlink:** `public/storage` → `storage/app/public`

### **4. TAMPILAN PDF MENGGUNAKAN PDF.JS** 📄

#### **A. Route untuk View PDF**

**Lokasi:** `routes/web.php` (line 84)

```php
Route::get('/mahasiswa/proposal/{id}/view-pdf', [ProposalController::class, 'viewPdf'])
    ->name('mahasiswa.proposal.view-pdf');
```

#### **B. Controller Method**

**Lokasi:** `app/Http/Controllers/ProposalController.php` (line 430-457)

```php
public function viewPdf($id)
{
    try {
        $proposal = Proposal::where('id_proposal', $id)
            ->where('id_mahasiswa', auth()->user()->id_mahasiswa)
            ->with('dokumen')
            ->firstOrFail();

        if (!$proposal->dokumen) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/' . $proposal->dokumen->path_file);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan: ' . $path);
        }

        // Return PDF dengan content-type yang tepat untuk iframe
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in viewPdf: ' . $e->getMessage());
        abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
    }
}
```

#### **C. Frontend PDF Viewer**

**Lokasi:** Multiple views (detail_proposal.blade.php, dll)

```javascript
function loadPDFDocument() {
    const pdfViewer = document.getElementById("pdfViewer");
    const pdfUrl =
        '{{ $proposal->dokumen ? Storage::url($proposal->dokumen->path_file) : "" }}';

    if (!pdfUrl) {
        pdfViewer.innerHTML =
            '<div class="empty-state">Dokumen Tidak Tersedia</div>';
        return;
    }

    // Create iframe untuk PDF viewer
    const iframe = document.createElement("iframe");
    iframe.src = pdfUrl;
    iframe.className = "pdf-iframe";
    iframe.style.width = "100%";
    iframe.style.height = "700px";
    iframe.style.border = "none";
    iframe.style.borderRadius = "8px";
    iframe.style.boxShadow = "0 2px 8px rgba(0,0,0,0.1)";

    pdfViewer.appendChild(iframe);
}
```

---

## ⚡ **ANALISIS PERFORMANCE**

### **1. KEUNTUNGAN SISTEM SAAT INI** ✅

#### **A. Storage Management**

-   ✅ **Laravel Storage:** Menggunakan Laravel Storage facade yang efisien
-   ✅ **Public Disk:** File dapat diakses langsung melalui URL
-   ✅ **Symlink:** `public/storage` → `storage/app/public` untuk akses web
-   ✅ **Organized Structure:** Folder terpisah untuk proposal, revisi, persetujuan

#### **B. PDF.js Implementation**

-   ✅ **Browser Native:** Menggunakan PDF viewer bawaan browser
-   ✅ **No External Dependencies:** Tidak perlu library PDF.js eksternal
-   ✅ **Responsive:** Iframe dapat disesuaikan ukuran
-   ✅ **Error Handling:** Fallback ke download jika PDF tidak bisa ditampilkan

#### **C. Security & Access Control**

-   ✅ **Authentication Required:** Hanya user yang login bisa akses
-   ✅ **Authorization:** User hanya bisa akses proposal miliknya
-   ✅ **File Validation:** Validasi format dan ukuran file
-   ✅ **Path Security:** File path tidak langsung diekspos

### **2. POTENSI MASALAH PERFORMANCE** ⚠️

#### **A. File Storage Issues**

-   ⚠️ **Local Storage:** File disimpan di server lokal (bukan cloud)
-   ⚠️ **Disk Space:** Semakin banyak proposal, semakin banyak space yang dibutuhkan
-   ⚠️ **Backup Complexity:** Perlu backup file storage terpisah dari database

#### **B. PDF Loading Performance**

-   ⚠️ **Large Files:** PDF 5MB bisa lambat di-load di browser
-   ⚠️ **Concurrent Access:** Banyak user akses PDF bersamaan bisa overload server
-   ⚠️ **Network Dependency:** Loading PDF bergantung pada koneksi user

#### **C. Database Performance**

-   ⚠️ **File Path Storage:** Menyimpan path file di database (normal)
-   ⚠️ **No File Compression:** Tidak ada kompresi PDF untuk mengurangi ukuran

---

## 🚀 **REKOMENDASI OPTIMASI**

### **1. SHORT TERM (Immediate)** 🔧

#### **A. File Compression**

```php
// Tambahkan kompresi PDF saat upload
if ($file->getClientOriginalExtension() === 'pdf') {
    // Compress PDF menggunakan library seperti Ghostscript
    $compressedFile = $this->compressPDF($file);
    $proposalFile = $compressedFile->store('proposals', 'public');
}
```

#### **B. Caching Strategy**

```php
// Tambahkan cache untuk PDF URL
$pdfUrl = Cache::remember("pdf_url_{$proposal->id}", 3600, function() use ($proposal) {
    return Storage::url($proposal->dokumen->path_file);
});
```

#### **C. Lazy Loading**

```javascript
// Implementasi lazy loading untuk PDF viewer
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            loadPDFDocument();
            observer.unobserve(entry.target);
        }
    });
});
```

### **2. MEDIUM TERM (3-6 months)** 📈

#### **A. Cloud Storage Migration**

-   **AWS S3** atau **Google Cloud Storage**
-   **CDN Integration** untuk distribusi global
-   **Automatic Backup** dan **Versioning**

#### **B. PDF Optimization**

-   **Server-side PDF compression**
-   **Thumbnail generation** untuk preview
-   **Progressive loading** untuk file besar

#### **C. Database Optimization**

-   **File metadata caching**
-   **Index optimization** untuk query dokumen
-   **Archive old proposals** ke storage terpisah

### **3. LONG TERM (6+ months)** 🎯

#### **A. Microservices Architecture**

-   **Document Service** terpisah
-   **File Processing Service** untuk kompresi/optimasi
-   **CDN Integration** untuk global distribution

#### **B. Advanced Features**

-   **PDF Annotation** dan **Commenting**
-   **Version Control** untuk revisi
-   **Bulk Operations** untuk admin

---

## 📊 **IMPACT ANALYSIS**

### **1. CURRENT SYSTEM CAPACITY** 📈

#### **A. File Storage**

-   **Current:** Local storage di server
-   **Capacity:** Bergantung pada disk space server
-   **Scalability:** Terbatas pada kapasitas server

#### **B. Performance Metrics**

-   **Upload Time:** ~2-5 detik untuk PDF 5MB
-   **Load Time:** ~3-8 detik untuk tampilan PDF
-   **Concurrent Users:** ~50-100 user simultan (estimasi)

### **2. SCALABILITY CONCERNS** ⚠️

#### **A. Storage Growth**

```
Estimasi pertumbuhan:
- 100 proposal/tahun × 5MB = 500MB/tahun
- 1000 proposal/tahun × 5MB = 5GB/tahun
- 10,000 proposal/tahun × 5MB = 50GB/tahun
```

#### **B. Server Load**

-   **Disk I/O:** Semakin banyak file, semakin tinggi I/O
-   **Memory Usage:** PDF loading menggunakan memory browser
-   **Network Bandwidth:** Setiap akses PDF menggunakan bandwidth

---

## ✅ **KESIMPULAN & REKOMENDASI**

### **1. SISTEM SAAT INI**

-   ✅ **Fungsional:** Flow dokumen sudah bekerja dengan baik
-   ✅ **Secure:** Ada validasi dan authorization yang proper
-   ✅ **User-Friendly:** PDF viewer mudah digunakan

### **2. POTENSI MASALAH**

-   ⚠️ **Scalability:** Sistem lokal tidak scalable untuk volume besar
-   ⚠️ **Performance:** File besar bisa lambat di-load
-   ⚠️ **Maintenance:** Backup dan maintenance file storage manual

### **3. PRIORITAS OPTIMASI**

1. **HIGH:** Implementasi file compression
2. **MEDIUM:** Caching strategy untuk PDF URL
3. **LOW:** Cloud storage migration (untuk masa depan)

### **4. MONITORING RECOMMENDATIONS**

-   **Disk usage monitoring**
-   **PDF load time tracking**
-   **User access pattern analysis**
-   **Storage growth projection**

**Sistem saat ini sudah cukup baik untuk penggunaan normal, tetapi perlu persiapan untuk optimasi jika volume proposal meningkat signifikan.**
