# CLOUD STORAGE IMPLEMENTATION: MASA DEPAN SISTEM PROPOSAL

## 🚀 **OVERVIEW CLOUD STORAGE**

Cloud storage adalah solusi terbaik untuk sistem proposal PDF yang scalable dan reliable. Berikut adalah panduan lengkap implementasi cloud storage.

---

## ☁️ **PILIHAN CLOUD STORAGE PROVIDER**

### **1. AWS S3 (Amazon Web Services)** 🏆

-   **Market leader** dengan fitur terlengkap
-   **Global CDN** dengan CloudFront
-   **Pricing:** Pay-as-you-use
-   **Reliability:** 99.999999999% (11 9's)

### **2. Google Cloud Storage** 🔍

-   **Integration** dengan Google Workspace
-   **AI/ML capabilities** untuk analisis dokumen
-   **Pricing:** Competitive dengan AWS
-   **Reliability:** 99.95% uptime

### **3. Azure Blob Storage** 💙

-   **Microsoft ecosystem** integration
-   **Enterprise features** yang kuat
-   **Pricing:** Similar dengan AWS
-   **Reliability:** 99.9% uptime

---

## 🔧 **IMPLEMENTASI AWS S3**

### **1. Setup AWS S3** ⚙️

#### **A. Install AWS SDK**

```bash
composer require aws/aws-sdk-php
```

#### **B. Environment Configuration**

```env
# .env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=pengajuan-proposal-unud
AWS_URL=https://pengajuan-proposal-unud.s3.ap-southeast-1.amazonaws.com
```

#### **C. Config Filesystem**

```php
// config/filesystems.php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
    ],
],
```

### **2. Migration Database** 📝

```php
// Migration: add_cloud_fields_to_dokumens_table.php
Schema::table('dokumens', function (Blueprint $table) {
    $table->string('cloud_url')->nullable()->after('path_file');
    $table->string('cloud_key')->nullable()->after('cloud_url');
    $table->string('cloud_provider')->default('s3')->after('cloud_key');
    $table->boolean('is_cloud_stored')->default(false)->after('cloud_provider');
    $table->timestamp('cloud_uploaded_at')->nullable()->after('is_cloud_stored');
});
```

### **3. Controller Implementation** 🎮

```php
// app/Http/Controllers/ProposalController.php
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

class ProposalController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // ... existing proposal creation logic ...

            // Upload file to S3
            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');

                // Validasi file
                if ($file->getSize() > 5 * 1024 * 1024) {
                    return back()->withErrors(['proposal_file' => 'Ukuran file maksimal 5MB']);
                }

                if ($file->getClientOriginalExtension() !== 'pdf') {
                    return back()->withErrors(['proposal_file' => 'File harus berformat PDF']);
                }

                // Generate unique filename
                $fileName = 'proposals/' . $proposal->id_proposal . '_' . time() . '.pdf';

                // Upload to S3
                $cloudPath = Storage::disk('s3')->putFileAs(
                    'proposals',
                    $file,
                    $fileName,
                    'public' // Make file publicly accessible
                );

                // Get S3 URL
                $cloudUrl = Storage::disk('s3')->url($cloudPath);

                // Save to database
                $proposal->dokumen()->create([
                    'skim' => $request->skim,
                    'path_file' => $cloudPath, // S3 key
                    'file_proposal' => $cloudPath,
                    'cloud_url' => $cloudUrl, // Public URL
                    'cloud_key' => $cloudPath,
                    'cloud_provider' => 's3',
                    'is_cloud_stored' => true,
                    'cloud_uploaded_at' => now(),
                    'tgl_upload' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollback();

            // Delete from S3 if database save fails
            if (isset($cloudPath)) {
                Storage::disk('s3')->delete($cloudPath);
            }

            return back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

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

            // Check if file is stored in cloud
            if ($proposal->dokumen->is_cloud_stored) {
                // Redirect to S3 URL (direct access)
                return redirect($proposal->dokumen->cloud_url);
            } else {
                // Fallback to local storage
                $path = storage_path('app/' . $proposal->dokumen->path_file);

                if (!file_exists($path)) {
                    abort(404, 'File tidak ditemukan.');
                }

                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in viewPdf: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }
}
```

### **4. Frontend Implementation** 🖥️

```javascript
// Load PDF dari cloud storage
function loadPDFDocument() {
    const pdfViewer = document.getElementById('pdfViewer');

    // Check if file is cloud stored
    const isCloudStored = {{ $proposal->dokumen->is_cloud_stored ? 'true' : 'false' }};

    let pdfUrl;
    if (isCloudStored) {
        // Direct S3 URL (faster, cached by CDN)
        pdfUrl = '{{ $proposal->dokumen->cloud_url }}';
    } else {
        // Fallback to local storage
        pdfUrl = '{{ route("mahasiswa.proposal.view-pdf", $proposal->id_proposal) }}';
    }

    if (!pdfUrl) {
        pdfViewer.innerHTML = '<div class="empty-state">Dokumen Tidak Tersedia</div>';
        return;
    }

    // Create iframe untuk PDF viewer
    const iframe = document.createElement('iframe');
    iframe.src = pdfUrl;
    iframe.className = 'pdf-iframe';
    iframe.style.width = '100%';
    iframe.style.height = '700px';
    iframe.style.border = 'none';
    iframe.style.borderRadius = '8px';
    iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';

    pdfViewer.appendChild(iframe);
}
```

---

## 🌐 **CDN INTEGRATION**

### **1. AWS CloudFront Setup** ⚡

#### **A. CloudFront Distribution**

```yaml
# CloudFront Configuration
Distribution:
    Origins:
        - DomainName: pengajuan-proposal-unud.s3.ap-southeast-1.amazonaws.com
          OriginPath: /proposals
          S3OriginConfig:
              OriginAccessIdentity: origin-access-identity/cloudfront/ABC123
    DefaultCacheBehavior:
        TargetOriginId: S3-pengajuan-proposal-unud
        ViewerProtocolPolicy: redirect-to-https
        CachePolicyId: 4135ea2d-6df8-44a3-9df3-4b5a84be39ad # CachingOptimized
        Compress: true
    PriceClass: PriceClass_100 # Asia Pacific only
```

#### **B. Environment Update**

```env
# .env
AWS_CLOUDFRONT_URL=https://d1234567890.cloudfront.net
```

#### **C. Controller Update**

```php
// Use CloudFront URL instead of direct S3 URL
public function getCloudUrl($cloudPath)
{
    $cloudFrontUrl = env('AWS_CLOUDFRONT_URL');
    return $cloudFrontUrl . '/' . $cloudPath;
}

// In store method
$cloudUrl = $this->getCloudUrl($cloudPath);
```

### **2. CDN Benefits** 🚀

-   **Global distribution** - File tersedia di seluruh dunia
-   **Faster loading** - File di-cache di edge locations
-   **Reduced server load** - Tidak perlu serve file dari server
-   **Better user experience** - Loading time lebih cepat

---

## 🔄 **MIGRATION STRATEGY**

### **1. Gradual Migration** 📈

#### **A. Phase 1: Dual Storage**

```php
// Upload to both local and cloud
public function store(Request $request)
{
    // ... existing logic ...

    if ($request->hasFile('proposal_file')) {
        $file = $request->file('proposal_file');

        // Upload to local storage (existing)
        $localPath = $file->store('proposals', 'public');

        // Upload to S3 (new)
        $cloudPath = Storage::disk('s3')->putFileAs(
            'proposals',
            $file,
            $fileName,
            'public'
        );

        // Save both paths
        $proposal->dokumen()->create([
            'path_file' => $localPath, // Local fallback
            'cloud_url' => Storage::disk('s3')->url($cloudPath),
            'cloud_key' => $cloudPath,
            'is_cloud_stored' => true,
            'cloud_provider' => 's3',
        ]);
    }
}
```

#### **B. Phase 2: Migrate Existing Files**

```php
// Command: Migrate existing files to S3
php artisan make:command MigrateFilesToS3

// app/Console/Commands/MigrateFilesToS3.php
class MigrateFilesToS3 extends Command
{
    protected $signature = 'migrate:files-to-s3';
    protected $description = 'Migrate existing files to S3';

    public function handle()
    {
        $dokumens = Dokumen::where('is_cloud_stored', false)->get();

        $this->info("Migrating {$dokumens->count()} files to S3...");

        foreach ($dokumens as $dokumen) {
            $localPath = storage_path('app/public/' . $dokumen->path_file);

            if (file_exists($localPath)) {
                // Upload to S3
                $cloudPath = 'proposals/' . $dokumen->id_proposal . '_' . time() . '.pdf';

                Storage::disk('s3')->put($cloudPath, file_get_contents($localPath), 'public');

                // Update database
                $dokumen->update([
                    'cloud_url' => Storage::disk('s3')->url($cloudPath),
                    'cloud_key' => $cloudPath,
                    'is_cloud_stored' => true,
                    'cloud_provider' => 's3',
                    'cloud_uploaded_at' => now(),
                ]);

                $this->info("Migrated: {$dokumen->path_file}");
            }
        }

        $this->info('Migration completed!');
    }
}
```

#### **C. Phase 3: Cloud-Only**

```php
// Remove local storage, use only cloud
public function store(Request $request)
{
    // ... existing logic ...

    if ($request->hasFile('proposal_file')) {
        $file = $request->file('proposal_file');

        // Upload only to S3
        $cloudPath = Storage::disk('s3')->putFileAs(
            'proposals',
            $file,
            $fileName,
            'public'
        );

        // Save only cloud path
        $proposal->dokumen()->create([
            'path_file' => $cloudPath, // S3 key
            'cloud_url' => Storage::disk('s3')->url($cloudPath),
            'cloud_key' => $cloudPath,
            'is_cloud_stored' => true,
            'cloud_provider' => 's3',
        ]);
    }
}
```

---

## 💰 **COST ANALYSIS**

### **1. AWS S3 Pricing** 💵

#### **A. Storage Costs**

```
Standard Storage (ap-southeast-1):
- First 50 TB: $0.025 per GB/month
- 1000 PDF files × 5MB = 5GB = $0.125/month
- 10,000 PDF files × 5MB = 50GB = $1.25/month
```

#### **B. Request Costs**

```
PUT/POST requests: $0.0004 per 1,000 requests
GET requests: $0.0004 per 1,000 requests
- 1000 uploads/month = $0.0004
- 10,000 views/month = $0.004
```

#### **C. Data Transfer**

```
Data transfer out (first 1GB free):
- 1GB/month = Free
- 10GB/month = $0.90
- 100GB/month = $9.00
```

### **2. CloudFront Pricing** ⚡

```
Data transfer out:
- First 1TB: $0.085 per GB
- 10GB/month = $0.85
- 100GB/month = $8.50
```

### **3. Total Monthly Cost** 📊

```
Small scale (1000 files):
- S3 Storage: $0.125
- S3 Requests: $0.004
- CloudFront: $0.85
- Total: ~$1.00/month

Medium scale (10,000 files):
- S3 Storage: $1.25
- S3 Requests: $0.04
- CloudFront: $8.50
- Total: ~$10.00/month

Large scale (100,000 files):
- S3 Storage: $12.50
- S3 Requests: $0.40
- CloudFront: $85.00
- Total: ~$100.00/month
```

---

## 🔒 **SECURITY & ACCESS CONTROL**

### **1. S3 Bucket Policies** 🛡️

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowPublicReadProposals",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::pengajuan-proposal-unud/proposals/*"
        },
        {
            "Sid": "RestrictUploads",
            "Effect": "Deny",
            "Principal": "*",
            "Action": "s3:PutObject",
            "Resource": "arn:aws:s3:::pengajuan-proposal-unud/proposals/*",
            "Condition": {
                "StringNotEquals": {
                    "aws:Referer": "https://pengajuan-proposal.unud.ac.id"
                }
            }
        }
    ]
}
```

### **2. Signed URLs** 🔐

```php
// Generate signed URL for private access
public function getSignedUrl($cloudKey, $expiration = 3600)
{
    return Storage::disk('s3')->temporaryUrl(
        $cloudKey,
        now()->addSeconds($expiration)
    );
}

// Use signed URL for sensitive documents
public function viewPrivatePdf($id)
{
    $proposal = Proposal::findOrFail($id);

    if ($proposal->dokumen->is_cloud_stored) {
        $signedUrl = $this->getSignedUrl($proposal->dokumen->cloud_key);
        return redirect($signedUrl);
    }

    // Fallback to local storage
    return $this->viewPdf($id);
}
```

---

## 📊 **MONITORING & ANALYTICS**

### **1. CloudWatch Metrics** 📈

```php
// Track file operations
use Aws\CloudWatch\CloudWatchClient;

class FileMetrics
{
    public function trackFileUpload($fileSize, $fileType)
    {
        $cloudWatch = new CloudWatchClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest'
        ]);

        $cloudWatch->putMetricData([
            'Namespace' => 'PengajuanProposal/Files',
            'MetricData' => [
                [
                    'MetricName' => 'FileUploads',
                    'Value' => 1,
                    'Unit' => 'Count'
                ],
                [
                    'MetricName' => 'FileSize',
                    'Value' => $fileSize,
                    'Unit' => 'Bytes'
                ]
            ]
        ]);
    }
}
```

### **2. Usage Analytics** 📊

```php
// Track file access patterns
class FileAnalytics
{
    public function trackFileAccess($proposalId, $userId, $fileType)
    {
        DB::table('file_access_logs')->insert([
            'proposal_id' => $proposalId,
            'user_id' => $userId,
            'file_type' => $fileType,
            'accessed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

---

## 🚀 **BENEFITS CLOUD STORAGE**

### **1. Performance** ⚡

-   **Global CDN** - File tersedia di seluruh dunia
-   **Faster loading** - Cached di edge locations
-   **Reduced server load** - Server tidak perlu serve file
-   **Better user experience** - Loading time lebih cepat

### **2. Scalability** 📈

-   **Unlimited storage** - Tidak ada batasan disk space
-   **Auto-scaling** - Otomatis menangani traffic spike
-   **Global distribution** - File tersedia di seluruh dunia
-   **High availability** - 99.999999999% uptime

### **3. Reliability** 🛡️

-   **Automatic backup** - File di-backup otomatis
-   **Versioning** - Bisa simpan multiple versi file
-   **Disaster recovery** - File tersimpan di multiple locations
-   **Data durability** - 99.999999999% durability

### **4. Cost Efficiency** 💰

-   **Pay-as-you-use** - Hanya bayar yang digunakan
-   **No upfront costs** - Tidak perlu beli hardware
-   **Reduced maintenance** - Tidak perlu maintain server storage
-   **Predictable costs** - Biaya bisa diprediksi

---

## 📋 **IMPLEMENTATION ROADMAP**

### **Phase 1: Setup & Testing (Week 1-2)**

1. Setup AWS S3 bucket
2. Install AWS SDK
3. Configure environment
4. Test file upload/download

### **Phase 2: Dual Storage (Week 3-4)**

1. Implement dual storage (local + cloud)
2. Update controllers
3. Test with new uploads
4. Monitor performance

### **Phase 3: Migration (Week 5-6)**

1. Create migration command
2. Migrate existing files
3. Update frontend
4. Test all functionality

### **Phase 4: Cloud-Only (Week 7-8)**

1. Remove local storage
2. Implement CDN
3. Add monitoring
4. Go live

---

## ✅ **KESIMPULAN**

### **Cloud Storage adalah solusi terbaik untuk masa depan karena:**

1. **Scalability** - Tidak ada batasan storage
2. **Performance** - Global CDN untuk loading cepat
3. **Reliability** - 99.999999999% uptime
4. **Cost Efficiency** - Pay-as-you-use model
5. **Security** - Enterprise-grade security
6. **Maintenance** - Tidak perlu maintain server storage

### **Rekomendasi:**

-   **Start dengan AWS S3** untuk reliability dan fitur lengkap
-   **Implementasi gradual** untuk meminimalisir risiko
-   **Gunakan CDN** untuk performance optimal
-   **Monitor costs** untuk optimasi biaya

**Cloud storage akan membuat sistem proposal PDF lebih scalable, reliable, dan cost-effective!** 🚀
