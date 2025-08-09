@extends('mainlayout.app')

@section('title', 'Data Proposal PKM')

@section('styles')
<style>
    .proposal-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    
    .proposal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-review {
        background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
        color: #0c5460;
        border: 1px solid #b8daff;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .proposal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.5rem;
        position: relative;
    }
    
    .proposal-title {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    .proposal-meta {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .proposal-body {
        padding: 1.5rem;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
        font-size: 0.9rem;
    }
    
    .info-value {
        color: #333;
        font-size: 0.9rem;
    }
    
    .action-buttons {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-action {
        font-size: 0.85rem;
        padding: 0.4rem 1rem;
        margin: 0 0.25rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
    }
    
    .pdf-viewer {
        width: 100%;
        height: 600px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .document-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .document-tabs .nav-link {
        color: var(--primary-color);
        border-color: transparent;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        margin-right: 0.25rem;
    }
    
    .document-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-color: var(--primary-color);
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #dee2e6;
    }
    
    .empty-state h4 {
        margin-bottom: 1rem;
        color: #495057;
    }
    
    .empty-state p {
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    /* Action Sidebar Button */
    .action-toggle-btn {
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 12px 8px;
        border-radius: 8px 0 0 8px;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        z-index: 1002;
        transition: all 0.3s ease;
        font-weight: bold;
        letter-spacing: 1px;
    }
    
    .action-toggle-btn:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #5a1f1f 100%);
        color: white;
        transform: translateY(-50%) translateX(-2px);
    }
    
    .action-toggle-btn.shifted {
        right: 320px;
    }
    
    /* Proposal Stats */
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Loading Animation */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 12px;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-alt me-2"></i>
                DATA PROPOSAL PKM
            </h2>
            <p class="text-muted mb-0">PKM adalah program yang diselenggarakan oleh Kementerian Riset, Teknologi, dan Pendidikan Tinggi untuk mendorong peserta didik program penelitian SKU untuk mengaplikasikan kompetensi di bidang pengabdian masyarakat.</p>
        </div>
        <div class="text-end">
            <img src="https://via.placeholder.com/80x80?text=Logo" alt="Logo" class="rounded">
            <div class="mt-2">
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajukan Proposal Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Total Proposal</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Sedang Direview</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Menunggu Validasi</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Disetujui</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Cards -->
    <div class="row" id="proposalCards">
        <!-- Sample Proposal Card 1 -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="proposal-card">
                <div class="proposal-header">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="status-badge status-review">Sedang Direview</span>
                        <small>ID: PKM-001</small>
                    </div>
                    <div class="proposal-title">
                        Sistem Informasi Manajemen Perpustakaan Berbasis Web untuk Meningkatkan Efisiensi Layanan Perpustakaan
                    </div>
                    <div class="proposal-meta">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Diajukan: 15 Januari 2025
                    </div>
                </div>
                
                <div class="proposal-body">
                    <div class="info-row">
                        <span class="info-label">Skim</span>
                        <span class="info-value">PKM-KC</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ketua Tim</span>
                        <span class="info-value">Ahmad Fauzi</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dosen Pembimbing</span>
                        <span class="info-value">Dr. Sari Widyastuti, M.Kom</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dana Diajukan</span>
                        <span class="info-value">Rp 15.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Validasi</span>
                        <span class="info-value">
                            <span class="status-badge status-approved">Valid</span>
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn btn-primary btn-action btn-sm" onclick="viewProposal(1)">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </button>
                    <button class="btn btn-outline-secondary btn-action btn-sm" onclick="downloadProposal(1)">
                        <i class="fas fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>

        <!-- Sample Proposal Card 2 -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="proposal-card">
                <div class="proposal-header">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="status-badge status-pending">Menunggu Validasi</span>
                        <small>ID: PKM-002</small>
                    </div>
                    <div class="proposal-title">
                        Aplikasi Mobile untuk Monitoring Kesehatan Mental Mahasiswa
                    </div>
                    <div class="proposal-meta">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Diajukan: 20 Januari 2025
                    </div>
                </div>
                
                <div class="proposal-body">
                    <div class="info-row">
                        <span class="info-label">Skim</span>
                        <span class="info-value">PKM-KC</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ketua Tim</span>
                        <span class="info-value">Siti Nurhaliza</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dosen Pembimbing</span>
                        <span class="info-value">Prof. Dr. Budi Santoso, M.T</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dana Diajukan</span>
                        <span class="info-value">Rp 12.500.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Validasi</span>
                        <span class="info-value">
                            <span class="status-badge status-pending">Pending</span>
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn btn-primary btn-action btn-sm" onclick="viewProposal(2)">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </button>
                    <button class="btn btn-outline-secondary btn-action btn-sm" onclick="downloadProposal(2)">
                        <i class="fas fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>

        <!-- Sample Proposal Card 3 -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="proposal-card">
                <div class="proposal-header">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="status-badge status-approved">Disetujui</span>
                        <small>ID: PKM-003</small>
                    </div>
                    <div class="proposal-title">
                        Pengembangan Aplikasi E-Learning Berbasis Gamification untuk Meningkatkan Motivasi Belajar
                    </div>
                    <div class="proposal-meta">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Diajukan: 10 Januari 2025
                    </div>
                </div>
                
                <div class="proposal-body">
                    <div class="info-row">
                        <span class="info-label">Skim</span>
                        <span class="info-value">PKM-PI</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ketua Tim</span>
                        <span class="info-value">Budi Prasetyo</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dosen Pembimbing</span>
                        <span class="info-value">Dr. Rina Marlina, M.Kom</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dana Diajukan</span>
                        <span class="info-value">Rp 10.000.000</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Validasi</span>
                        <span class="info-value">
                            <span class="status-badge status-approved">Valid</span>
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn btn-primary btn-action btn-sm" onclick="viewProposal(3)">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </button>
                    <button class="btn btn-outline-secondary btn-action btn-sm" onclick="downloadProposal(3)">
                        <i class="fas fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Viewer Section -->
    <div class="document-section" id="documentSection" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>
                <i class="fas fa-file-pdf me-2"></i>
                Dokumen Proposal: <span id="documentTitle">Sistem Informasi Manajemen Perpustakaan</span>
            </h4>
            <button class="btn btn-outline-secondary btn-sm" onclick="hideDocumentViewer()">
                <i class="fas fa-times me-1"></i>Tutup
            </button>
        </div>
        
        <!-- Document Tabs -->
        <ul class="nav nav-tabs document-tabs mb-3" id="documentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="proposal-tab" data-bs-toggle="tab" data-bs-target="#proposal-pane" type="button" role="tab">
                    <i class="fas fa-file-alt me-1"></i>Proposal
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="persetujuan-tab" data-bs-toggle="tab" data-bs-target="#persetujuan-pane" type="button" role="tab">
                    <i class="fas fa-file-signature me-1"></i>Persetujuan
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="documentTabContent">
            <div class="tab-pane fade show active" id="proposal-pane" role="tabpanel">
                <div class="position-relative">
                    <iframe id="proposalPDF" class="pdf-viewer" src="about:blank"></iframe>
                    <div class="loading-overlay" id="proposalLoading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="persetujuan-pane" role="tabpanel">
                <div class="position-relative">
                    <iframe id="persetujuanPDF" class="pdf-viewer" src="about:blank"></iframe>
                    <div class="loading-overlay" id="persetujuanLoading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Toggle Button -->
<button class="btn action-toggle-btn" id="actionToggleBtn" onclick="toggleActionSidebar()">
    AKSI
</button>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Sample proposal data
    const proposalData = {
        1: {
            id: 'PKM-001',
            title: 'Sistem Informasi Manajemen Perpustakaan Berbasis Web untuk Meningkatkan Efisiensi Layanan Perpustakaan',
            skim: 'PKM-KC',
            ketua: 'Ahmad Fauzi',
            nim: '2021110001',
            dosen: 'Dr. Sari Widyastuti, M.Kom',
            dana: 'Rp 15.000.000',
            status: 'Sedang Direview',
            tanggal: '15 Januari 2025',
            proposal_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            persetujuan_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'
        },
        2: {
            id: 'PKM-002',
            title: 'Aplikasi Mobile untuk Monitoring Kesehatan Mental Mahasiswa',
            skim: 'PKM-KC',
            ketua: 'Siti Nurhaliza',
            nim: '2021110002',
            dosen: 'Prof. Dr. Budi Santoso, M.T',
            dana: 'Rp 12.500.000',
            status: 'Menunggu Validasi',
            tanggal: '20 Januari 2025',
            proposal_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            persetujuan_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'
        },
        3: {
            id: 'PKM-003',
            title: 'Pengembangan Aplikasi E-Learning Berbasis Gamification untuk Meningkatkan Motivasi Belajar',
            skim: 'PKM-PI',
            ketua: 'Budi Prasetyo',
            nim: '2021110003',
            dosen: 'Dr. Rina Marlina, M.Kom',
            dana: 'Rp 10.000.000',
            status: 'Disetujui',
            tanggal: '10 Januari 2025',
            proposal_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            persetujuan_url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'
        }
    };

    // View proposal detail
    function viewProposal(proposalId) {
        const proposal = proposalData[proposalId];
        if (!proposal) return;

        // Update document title
        document.getElementById('documentTitle').textContent = proposal.title;
        
        // Show loading
        document.getElementById('proposalLoading').style.display = 'flex';
        document.getElementById('persetujuanLoading').style.display = 'flex';
        
        // Load PDF documents
        const proposalPDF = document.getElementById('proposalPDF');
        const persetujuanPDF = document.getElementById('persetujuanPDF');
        
        proposalPDF.onload = function() {
            document.getElementById('proposalLoading').style.display = 'none';
        };
        
        persetujuanPDF.onload = function() {
            document.getElementById('persetujuanLoading').style.display = 'none';
        };
        
        proposalPDF.src = proposal.proposal_url;
        persetujuanPDF.src = proposal.persetujuan_url;
        
        // Show document section
        document.getElementById('documentSection').style.display = 'block';
        
        // Scroll to document section
        document.getElementById('documentSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });

        // Show action sidebar if not already shown
        if (!document.getElementById('actionSidebar').classList.contains('show')) {
            toggleActionSidebar();
        }

        // Show success message
        showToast(`Membuka detail proposal ${proposal.id}`, 'success');
    }

    // Hide document viewer
    function hideDocumentViewer() {
        document.getElementById('documentSection').style.display = 'none';
        
        // Clear PDF sources
        document.getElementById('proposalPDF').src = 'about:blank';
        document.getElementById('persetujuanPDF').src = 'about:blank';
        
        // Hide action sidebar
        if (document.getElementById('actionSidebar').classList.contains('show')) {
            toggleActionSidebar();
        }
    }

    // Download proposal
    function downloadProposal(proposalId) {
        const proposal = proposalData[proposalId];
        if (!proposal) return;

        // Create a temporary download link
        const link = document.createElement('a');
        link.href = proposal.proposal_url;
        link.download = `Proposal_${proposal.id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Show success message
        showToast(`Proposal ${proposal.id} berhasil didownload`, 'success');
    }

    // Toggle action sidebar
    function toggleActionSidebar() {
        const actionSidebar = document.getElementById('actionSidebar');
        const toggleBtn = document.getElementById('actionToggleBtn');
        
        actionSidebar.classList.toggle('show');
        toggleBtn.classList.toggle('shifted');
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Proposal index page loaded');
        
        // Check if there are any proposals
        const proposalCards = document.querySelectorAll('.proposal-card');
        if (proposalCards.length === 0) {
            // Show empty state if needed
            console.log('No proposals found');
        }

        // Initialize PDF.js
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    });

    // Handle responsive design
    window.addEventListener('resize', function() {
        // Adjust layout for mobile devices
        if (window.innerWidth < 768) {
            const actionSidebar = document.getElementById('actionSidebar');
            const toggleBtn = document.getElementById('actionToggleBtn');
            
            if (actionSidebar.classList.contains('show')) {
                // Hide action sidebar on mobile when resizing
                actionSidebar.classList.remove('show');
                toggleBtn.classList.remove('shifted');
            }
        }
    });

    // Enhanced PDF viewer functions
    function loadPDFDocument(url, containerId) {
        const container = document.getElementById(containerId);
        
        if (typeof pdfjsLib !== 'undefined') {
            // Use PDF.js for better PDF rendering
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                // Clear previous content
                container.innerHTML = '';
                
                // Create canvas for each page
                for (let pageNum = 1; pageNum <= Math.min(pdf.numPages, 5); pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        const scale = 1.5;
                        const viewport = page.getViewport({ scale: scale });
                        
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.width = '100%';
                        canvas.style.marginBottom = '10px';
                        canvas.style.border = '1px solid #ddd';
                        
                        container.appendChild(canvas);
                        
                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        
                        page.render(renderContext);
                    });
                }
            }).catch(function(error) {
                // Fallback to iframe if PDF.js fails
                const iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '100%';
                iframe.style.height = '600px';
                iframe.style.border = '1px solid #ddd';
                
                container.innerHTML = '';
                container.appendChild(iframe);
            });
        } else {
            // Fallback to iframe
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '600px';
            iframe.style.border = '1px solid #ddd';
            
            container.innerHTML = '';
            container.appendChild(iframe);
        }
    }
</script>
@endsection