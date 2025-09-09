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
    
    .status-valid {
        background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
        color: #0c5460;
        border: 1px solid #b8daff;
    }
    
    .status-revisi {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .review-section {
        margin-bottom: 2rem;
    }
    
    .review-section .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .review-section .card-header {
        border-bottom: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    
    .review-section .card-body {
        padding: 1.5rem;
    }
    
    .proposal-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .proposal-info h6 {
        color: var(--primary-color);
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .proposal-info p {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .proposal-info .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
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

    /* Team Member Badge */
    .team-member-badge {
        display: inline-block;
        background: #e9ecef;
        color: #495057;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        margin: 0.125rem;
    }

    .team-member-badge.ketua {
        background: #007bff;
        color: white;
    }

    /* Header Styles */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .header-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .header-title {
        color: var(--primary-color);
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Action Sidebar - Mahasiswa Specific */
    .mahasiswa-action-sidebar {
        position: fixed;
        right: -320px;
        top: 0;
        width: 320px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .mahasiswa-action-sidebar.show {
        right: 0;
    }

    .mahasiswa-sidebar-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .mahasiswa-sidebar-header h4 {
        margin: 0;
        font-weight: 600;
    }

    .mahasiswa-sidebar-content {
        padding: 1.5rem;
    }

    .mahasiswa-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mahasiswa-sidebar-menu li {
        margin-bottom: 0.5rem;
    }

    .mahasiswa-sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .mahasiswa-sidebar-menu a:hover {
        background: #e9ecef;
        border-left-color: var(--primary-color);
        transform: translateX(5px);
    }

    .mahasiswa-sidebar-menu i {
        margin-right: 1rem;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1003;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        background: #8B0000;
        color: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .modal-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.3);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .review-section {
        margin-bottom: 1.5rem;
    }

    .review-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .review-icon {
        width: 24px;
        height: 24px;
        background: #007bff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: white;
        font-size: 0.8rem;
    }

    .review-title {
        color: #007bff;
        font-weight: 600;
        margin: 0;
    }

    .error-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1rem 0;
    }

    .error-list li {
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 6px;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .note-section {
        display: flex;
        align-items: flex-start;
        background: #fff3cd;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #ffc107;
    }

    .note-icon {
        color: #ffc107;
        margin-right: 0.75rem;
        margin-top: 0.125rem;
    }

    .note-text {
        color: #856404;
        margin: 0;
        line-height: 1.5;
    }

    .substantive-review {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .reviewer-avatar {
        width: 20px;
        height: 20px;
        background: #007bff;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    .reviewer-name {
        color: #007bff;
        font-weight: 600;
        margin: 0;
    }

    .review-note {
        background: #fff3cd;
        padding: 0.75rem;
        border-radius: 6px;
        border-left: 4px solid #ffc107;
    }

    .review-note-text {
        color: #856404;
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    /* Action Toggle Button */
    .mahasiswa-action-toggle-btn {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .mahasiswa-action-toggle-btn:hover {
        background: var(--primary-dark);
        transform: scale(1.1);
    }

    .mahasiswa-action-toggle-btn.shifted {
        right: 340px;
    }

    /* Card Actions */
    .card-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .card-actions .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }

    /* Review Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.3s;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-body {
        padding: 2rem;
    }

    /* Review Section Styles */
    .review-section {
        margin-bottom: 1rem;
    }

    .proposal-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        border-left: 4px solid #007bff;
    }

    .proposal-info h6 {
        color: #007bff;
        margin-bottom: 0.5rem;
    }

    .proposal-info p {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    /* Card Styles */
    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
        font-weight: 600;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Alert Styles */
    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem;
    }

    .alert-info {
        background: #e3f2fd;
        color: #0d47a1;
    }

    .alert-warning {
        background: #fff3e0;
        color: #e65100;
    }

    .alert-success {
        background: #e8f5e8;
        color: #1b5e20;
    }

    .alert-danger {
        background: #ffebee;
        color: #c62828;
    }

    /* Badge Styles */
    .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
    }

    /* List Styles */
    .list-unstyled li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .list-unstyled li:last-child {
        border-bottom: none;
    }

    /* Loading Spinner */
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Action Sidebar Styles */
    .mahasiswa-action-sidebar {
        position: fixed;
        right: -300px;
        top: 0;
        width: 300px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        transition: right 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
    }

    .mahasiswa-action-sidebar.show {
        right: 0;
    }

    .mahasiswa-sidebar-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .mahasiswa-sidebar-header h4 {
        margin: 0;
        font-size: 1.2rem;
    }

    .mahasiswa-sidebar-content {
        padding: 1rem;
    }

    .mahasiswa-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mahasiswa-sidebar-menu li {
        margin-bottom: 0.5rem;
    }

    .mahasiswa-sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .mahasiswa-sidebar-menu a:hover {
        background: #e9ecef;
        border-left-color: #007bff;
        transform: translateX(5px);
    }

    .mahasiswa-sidebar-menu i {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
        color: #007bff;
    }

    /* Action Toggle Button */
    .mahasiswa-action-toggle-btn {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
        z-index: 999;
    }

    .mahasiswa-action-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    /* Toast Styles */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    }

    .toast-success {
        background: #28a745;
        color: white;
    }

    .toast-error {
        background: #dc3545;
        color: white;
    }

    .toast-info {
        background: #17a2b8;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <x-page-header 
        title="Data Proposal PKM" 
        subtitle="UNIVERSITAS UDAYANA"
        description="Selamat datang, {{ $user->nama_mhs }}! Berikut adalah daftar proposal PKM yang telah Anda ajukan." />
    

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">{{ $totalProposals }}</div>
                    <div class="stat-label">Total Proposal</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">{{ $underReview }}</div>
                    <div class="stat-label">Sedang Direview</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">{{ $pendingValidation }}</div>
                    <div class="stat-label">Menunggu Validasi</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-item">
                    <div class="stat-number">{{ $approved }}</div>
                    <div class="stat-label">Disetujui</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Cards -->
    <div class="row" id="proposalCards">
        @if($proposals->count() > 0)
            @foreach($proposals as $proposal)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="proposal-card">
                        <div class="proposal-header">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="status-badge status-{{ $proposal->status_final === 'revisi' ? 'revisi' : ($proposal->status_final === 'lolos' ? 'approved' : ($proposal->status_final === 'tidak_lolos' ? 'rejected' : strtolower($proposal->status_validasi))) }}">
                                    @if($proposal->status_final === 'revisi')
                                        Perlu Revisi
                                    @elseif($proposal->status_final === 'lolos')
                                        Lolos
                                    @elseif($proposal->status_final === 'tidak_lolos')
                                        Tidak Lolos
                                    @elseif($proposal->status === 'revisi_submitted')
                                        Menunggu Review Revisi
                                    @elseif($proposal->status_validasi == 'pending')
                                        Menunggu Validasi
                                    @elseif($proposal->status_validasi == 'valid')
                                        @if($proposal->status_final == 'lolos')
                                            Lolos Final
                                        @elseif($proposal->status_final == 'tidak_lolos')
                                            Tidak Lolos Final
                                        @elseif($proposal->status_final == 'sedang_review')
                                            Sedang Direview
                                        @else
                                            Valid (Menunggu Review)
                                        @endif
                                    @elseif($proposal->status_validasi == 'tidak_valid')
                                        Ditolak
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $proposal->status_validasi)) }}
                                    @endif
                                </span>
                                <small>ID: PKM-{{ str_pad($proposal->id_proposal, 3, '0', STR_PAD_LEFT) }}</small>
                            </div>
                            <div class="proposal-title">
                            {{ $proposal->judul_proposal }}
                            </div>
                            <div class="proposal-meta">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Diajukan: {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}
                                
                                @php
                                    $userRole = null;
                                    if ($proposal->id_mahasiswa == $user->id_mahasiswa) {
                                        $userRole = 'Pengaju';
                                    } else {
                                        $userTeamMember = $proposal->teams->where('nim', $user->nim)->first();
                                        if ($userTeamMember) {
                                            $userRole = ucfirst($userTeamMember->role);
                                        }
                                    }
                                @endphp
                                
                                @if($userRole)
                                    <br>
                                    <span class="badge bg-info me-1">
                                        <i class="fas fa-user me-1"></i>{{ $userRole }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="proposal-body">
                            <div class="info-row">
                                <span class="info-label">Skim</span>
                                <span class="info-value">{{ $proposal->skim }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ketua Tim</span>
                                <span class="info-value">
                                    @php
                                        $ketua = $proposal->teams->where('role', 'ketua')->first();
                                    @endphp
                                    {{ $ketua ? $ketua->nama : 'N/A' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Dosen Pembimbing</span>
                                <span class="info-value">{{ $proposal->dosen_pembimbing ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Dana Diajukan</span>
                                <span class="info-value">Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Jumlah Anggota</span>
                                <span class="info-value">{{ $proposal->teams->count() }} orang</span>
                            </div>
                            @if($proposal->proposalRevisi->count() > 0)
                            <div class="info-row">
                                <span class="info-label">Status Revisi</span>
                                <span class="info-value">
                                    @php
                                        $revisiTerbaru = $proposal->proposalRevisi->sortByDesc('tanggal_submit')->first();
                                        
                                        // Status berdasarkan proposal status
                                        switch($proposal->status) {
                                            case 'revisi':
                                                $statusText = 'Menunggu Revisi';
                                                $statusClass = 'warning';
                                                break;
                                            case 'revisi_submitted':
                                                $statusText = 'Sudah Direvisi';
                                                $statusClass = 'info';
                                                break;
                                            case 'lolos':
                                            case 'tidak_lolos':
                                                $statusText = 'Selesai';
                                                $statusClass = 'success';
                                                break;
                                            default:
                                                $statusText = 'Belum Direvisi';
                                                $statusClass = 'secondary';
                                                break;
                                        }
                                    @endphp
                                    <span class="text-{{ $statusClass }}">{{ $statusText }}</span>
                                    <br><small class="text-muted">{{ $revisiTerbaru->tanggal_submit ? $revisiTerbaru->tanggal_submit->format('d/m/Y H:i') : 'N/A' }}</small>
                                </span>
                            </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Status Validasi</span>
                                <span class="info-value">
                                    @if($proposal->status_final === 'revisi')
                                        <span class="text-warning">Perlu Revisi</span>
                                    @elseif($proposal->status_validasi == 'pending')
                                        <span class="text-warning">Menunggu Validasi</span>
                                    @elseif($proposal->status_validasi == 'valid')
                                        @if($proposal->status_final == 'lolos')
                                            <span class="text-success">Lolos Final</span>
                                        @elseif($proposal->status_final == 'tidak_lolos')
                                            <span class="text-danger">Tidak Lolos Final</span>
                                        @elseif($proposal->status_final == 'sedang_review')
                                            <span class="text-info">Sedang Direview</span>
                                        @else
                                            <span class="text-success">Valid (Menunggu Review)</span>
                                        @endif
                                    @elseif($proposal->status_validasi == 'tidak_valid')
                                        <span class="text-danger">Ditolak</span>
                                    @else
                                        <span class="text-secondary">{{ ucfirst(str_replace('_', ' ', $proposal->status_validasi)) }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                            @if($proposal->dokumen)
                            <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                            @endif
                            <button class="btn btn-outline-info btn-sm" onclick="showReviewModal('administrative', '{{ $proposal->id_proposal }}')">
                                <i class="fas fa-clipboard-check me-1"></i>Review
                            </button>
                            @if($proposal->status_final === 'revisi')
                            <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Revisi
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h4>Belum Ada Proposal</h4>
                    <p>Anda belum mengajukan proposal PKM. Mulai dengan mengajukan proposal baru untuk berpartisipasi dalam program PKM.</p>
                    @if(\App\Helpers\RuangKontrolHelper::isPendaftaranActive())
                        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Ajukan Proposal Pertama
                        </a>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Sistem Pendaftaran Ditutup</strong><br>
                            Saat ini sistem pendaftaran proposal PKM sedang ditutup oleh operator.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Action Sidebar -->
<div class="mahasiswa-action-sidebar" id="actionSidebar">
    <div class="mahasiswa-sidebar-header">
        <h4><i class="fas fa-tasks me-2"></i>Menu Aksi</h4>
    </div>
    <div class="mahasiswa-sidebar-content">
        <ul class="mahasiswa-sidebar-menu">
            <li>
                <a href="#" onclick="showReviewModal('administrative', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Hasil Review Administratif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('substantive', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-search"></i>
                    <span>Hasil Review Substantif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('final', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-trophy"></i>
                    <span>Hasil Final</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 id="modalTitle">Hasil Review</h5>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Action Toggle Button -->
<button class="mahasiswa-action-toggle-btn" id="actionToggleBtn" onclick="toggleActionSidebar()">
    <i class="fas fa-bars"></i>
</button>
@endsection

@section('scripts')
<script>
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Proposal index page loaded');
        
        // Check if there are any proposals
        const proposalCards = document.querySelectorAll('.proposal-card');
        if (proposalCards.length === 0) {
            console.log('No proposals found');
        }

        // Show success message if exists
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    // Handle responsive design
    window.addEventListener('resize', function() {
        // Adjust layout for mobile devices
        if (window.innerWidth < 768) {
            const actionSidebar = document.getElementById('actionSidebar');
            const toggleBtn = document.getElementById('actionToggleBtn');
            
            if (actionSidebar && actionSidebar.classList.contains('show')) {
                // Hide action sidebar on mobile when resizing
                actionSidebar.classList.remove('show');
                toggleBtn.classList.remove('shifted');
            }
        }
    });

    // Sidebar and Modal Functions
    function toggleActionSidebar() {
        const actionSidebar = document.getElementById('actionSidebar');
        const toggleBtn = document.getElementById('actionToggleBtn');
        
        if (actionSidebar) {
            actionSidebar.classList.toggle('show');
            toggleBtn.classList.toggle('shifted');
        }
    }

    function showReviewModal(type, proposalId) {
        const modalOverlay = document.getElementById('modalOverlay');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        // Show loading
        modalBody.innerHTML = '<div class="text-center"><div class="spinner"></div><p>Memuat data...</p></div>';
        modalOverlay.classList.add('show');
        
        // Set modal title based on type
        switch(type) {
            case 'administrative':
                modalTitle.textContent = 'Hasil Review Administratif';
                loadAdministrativeReview(proposalId);
                break;
            case 'substantive':
                modalTitle.textContent = 'Hasil Review Substantif';
                loadSubstantiveReview(proposalId);
                break;
            case 'final':
                modalTitle.textContent = 'Hasil Final';
                loadFinalReview(proposalId);
                break;
        }
    }

    function closeModal() {
        const modalOverlay = document.getElementById('modalOverlay');
        modalOverlay.classList.remove('show');
    }

    function loadAdministrativeReview(proposalId) {
        if (!proposalId) {
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted">Tidak ada data review administratif.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/administrative`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    document.getElementById('modalBody').innerHTML = generateAdministrativeReviewHTML(data.data, data.proposal_info);
                } else {
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada review administratif</h6>
                            <p class="text-muted">Review administratif akan muncul di sini setelah proposal direview oleh reviewer.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> Terjadi kesalahan saat memuat data review administratif.
                    </div>
                `;
            });
    }

    function loadSubstantiveReview(proposalId) {
        console.log('Loading substantive review for proposal:', proposalId);
        
        if (!proposalId) {
            console.log('No proposal ID provided');
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted">Tidak ada data review substantif.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/substantive`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                
                if (data.success && data.data && data.data.length > 0) {
                    console.log(`Found ${data.data.length} substantive reviews`);
                    document.getElementById('modalBody').innerHTML = generateSubstantiveReviewHTML(data.data, data.proposal_info);
                } else {
                    console.log('No substantive reviews found or empty data');
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada review substantif</h6>
                            <p class="text-muted">Review substantif akan muncul di sini setelah proposal lolos review administratif.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading substantive review:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> Terjadi kesalahan saat memuat data review substantif.
                        <br><small>Error: ${error.message}</small>
                    </div>
                `;
            });
    }

    function loadFinalReview(proposalId) {
        if (!proposalId) {
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted">Tidak ada data hasil final.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/final`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('modalBody').innerHTML = generateFinalReviewHTML(data.data, data.proposal_info);
                } else {
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada hasil final</h6>
                            <p class="text-muted">Hasil final akan muncul di sini setelah semua review selesai.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> Terjadi kesalahan saat memuat data hasil final.
                    </div>
                `;
            });
    }

    function generateAdministrativeReviewHTML(reviews, proposalInfo) {
        let html = `
            <div class="review-section">
                <div class="proposal-info mb-3">
                    <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                    <p><strong>Judul:</strong> ${proposalInfo.judul}</p>
                    <p><strong>Skim:</strong> ${proposalInfo.skim}</p>
                    <p><strong>Status:</strong> <span class="badge bg-info">${proposalInfo.status}</span></p>
                </div>
        `;
        
        // Karena sekarang hanya ada 1 review administratif terbaru
        if (reviews.length > 0) {
            const review = reviews[0];
            html += `
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-clipboard-check me-2"></i>
                        <strong>Review Administratif</strong>
                        ${review.reviewer ? ` - ${review.reviewer.nama_reviewer}` : ''}
                    </div>
                    <div class="card-body">
            `;
            
            if (review.checklist && review.checklist.length > 0) {
                html += '<div class="mb-3"><strong>Kesalahan Administratif yang Ditemukan:</strong><ul class="list-unstyled mt-2">';
                
                // Display selected errors
                review.checklist.forEach((item) => {
                    html += `
                        <li class="mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-3">
                                    <i class="fas fa-times"></i>
                                </span>
                                <div>
                                    <strong>${item}</strong>
                                    <br>
                                    <small class="text-muted">Status: Perlu Perbaikan</small>
                                </div>
                            </div>
                        </li>
                    `;
                });
                html += '</ul></div>';
            } else {
                html += `
                    <div class="mb-3">
                        <strong>Checklist Administratif:</strong>
                        <div class="text-center text-success mt-2">
                            <i class="fas fa-check-circle me-2"></i>
                            Semua kriteria telah memenuhi standar
                        </div>
                    </div>
                `;
            }
            
            if (review.note_administratif) {
                html += `
                    <div class="alert alert-info">
                        <i class="fas fa-edit me-2"></i>
                        <strong>Catatan:</strong><br>
                        ${review.note_administratif}
                    </div>
                `;
            }
            
            html += '</div></div>';
        } else {
            html += `
                <div class="card mb-3">
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <h6>Belum ada review administratif</h6>
                        <p>Review administratif akan muncul di sini setelah proposal direview oleh reviewer.</p>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    function generateSubstantiveReviewHTML(reviews, proposalInfo) {
        console.log('Generating substantive review HTML with:', { reviews, proposalInfo });
        
        let html = `
            <div class="review-section">
                <div class="proposal-info mb-3">
                    <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                    <p><strong>Judul:</strong> ${proposalInfo.judul}</p>
                    <p><strong>Skim:</strong> ${proposalInfo.skim}</p>
                    <p><strong>Status:</strong> <span class="badge bg-info">${proposalInfo.status}</span></p>
                </div>
        `;
        
        if (reviews && reviews.length > 0) {
            console.log(`Found ${reviews.length} substantive reviews`);
            
            reviews.forEach((review, index) => {
                console.log(`Processing review ${index + 1}:`, review);
                
                html += `
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-user me-2"></i>
                            <strong>Reviewer Substantif ${index + 1}</strong>
                            ${review.reviewer ? ` - ${review.reviewer.nama_reviewer}` : ''}
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-edit me-2"></i>
                                <strong>Catatan:</strong><br>
                                ${review.note_substantif || 'Tidak ada catatan khusus.'}
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            console.log('No substantive reviews found');
            html += `
                <div class="card mb-3">
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-user-check fa-3x mb-3"></i>
                        <h6>Belum ada review substantif</h6>
                        <p>Review substantif akan muncul di sini setelah proposal lolos review administratif.</p>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    function generateFinalReviewHTML(finalResult, proposalInfo) {
        const statusClass = finalResult.status_final === 'lolos' ? 'success' : 'danger';
        const statusText = finalResult.status_final === 'lolos' ? 'LOLOS' : 'TIDAK LOLOS';
        const statusIcon = finalResult.status_final === 'lolos' ? 'trophy' : 'times-circle';
        
        return `
            <div class="review-section">
                <div class="proposal-info mb-3">
                    <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                    <p><strong>Judul:</strong> ${proposalInfo.judul}</p>
                    <p><strong>Skim:</strong> ${proposalInfo.skim}</p>
                    <p><strong>Status:</strong> <span class="badge bg-info">${proposalInfo.status}</span></p>
                </div>
                
                <div class="card">
                    <div class="card-header bg-${statusClass} text-white">
                        <i class="fas fa-${statusIcon} me-2"></i>
                        <strong>Hasil Final</strong>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-${statusClass}">
                            <h5 class="alert-heading">
                                <i class="fas fa-${statusIcon} me-2"></i>
                                Status: ${statusText}
                            </h5>
                            <p class="mb-0">
                                ${finalResult.catatan_final || 'Tidak ada catatan final.'}
                            </p>
                        </div>
                        
                        ${finalResult.pt ? `
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modalOverlay = document.getElementById('modalOverlay');
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Hide toast after 3 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
</script>
@endsection