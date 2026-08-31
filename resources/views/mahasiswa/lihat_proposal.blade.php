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
    
    .empty-state .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        min-width: 200px;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .empty-state .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .empty-state .btn i {
        margin-right: 8px;
        font-size: 1.1em;
    }
    
    .empty-state-button-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin-top: 1rem;
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
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Lihat Proposal', 'active' => true],
    ]" />

    <!-- Header -->
    <x-page-header 
        title="Data Proposal PKM" 
        description="Selamat datang, {{ $user->nama_mhs }}! Berikut adalah daftar proposal PKM yang telah Anda ajukan." />

    <!-- Proposal Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="proposalCards">
        @if($proposals->count() > 0)
            @foreach($proposals as $proposal)
                <x-ui.card class="flex flex-col h-full hover:shadow-lg transition-shadow duration-300">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-br from-navy-700 to-navy-900 text-white p-5 rounded-t-xl relative">
                        <div class="flex justify-between items-start mb-3">
                            <x-ui.badge variant="{{ $proposal->status_final === 'revisi' ? 'warning' : ($proposal->status_final === 'lolos' ? 'success' : ($proposal->status_final === 'tidak_lolos' ? 'danger' : 'info')) }}">
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
                            </x-ui.badge>
                        </div>
                        <h3 class="text-lg font-bold mb-2 leading-snug line-clamp-2">
                            {{ $proposal->judul }}
                        </h3>
                        <div class="text-sm text-navy-100 flex flex-wrap gap-y-1">
                            <div class="w-full">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Diajukan: {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}
                            </div>
                            @php
                                $userRole = null;
                                if ($proposal->id_mahasiswa == $user->id_mahasiswa) {
                                    $userRole = 'Pengaju';
                                } else {
                                    $userTeamMember = $proposal->semuaAnggotaTim->where('nim', $user->nim)->first();
                                    if ($userTeamMember) {
                                        $userRole = $userTeamMember->is_ketua ? 'Ketua' : 'Anggota';
                                    }
                                }
                            @endphp
                            
                            @if($userRole)
                                <div class="w-full mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white/20 text-white">
                                        <i class="fas fa-user me-1"></i>{{ $userRole }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div class="space-y-3 mb-5">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Skim</span>
                                <span class="text-sm text-slate-800">{{ $proposal->skim }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Ketua Tim</span>
                                <span class="text-sm text-slate-800">
                                    @php $ketua = $proposal->ketuaTim; @endphp
                                    {{ $ketua ? $ketua->nama_mhs : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Dosen Pendamping</span>
                                <span class="text-sm text-slate-800 text-right line-clamp-1 max-w-[150px]">{{ $proposal->dosen_pembimbing ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Dana Belmawa</span>
                                <span class="text-sm font-medium text-navy-600">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Dana Univ</span>
                                <span class="text-sm font-medium text-green-600">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Jumlah Anggota</span>
                                <span class="text-sm text-slate-800">@formatId($proposal->semuaAnggotaTim->count()) orang</span>
                            </div>
                            
                            @if($proposal->proposalRevisi->count() > 0)
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-sm font-semibold text-slate-500">Status Revisi</span>
                                <div class="text-right">
                                    @php
                                        $revisiTerbaru = $proposal->proposalRevisi->sortByDesc('tanggal_submit')->first();
                                        switch($proposal->status) {
                                            case 'revisi': $sT = 'Menunggu Revisi'; $sC = 'text-amber-600'; break;
                                            case 'revisi_submitted': $sT = 'Menunggu Hasil Final'; $sC = 'text-blue-600'; break;
                                            case 'lolos': case 'tidak_lolos': $sT = 'Selesai'; $sC = 'text-green-600'; break;
                                            default: $sT = 'Belum Direvisi'; $sC = 'text-slate-500'; break;
                                        }
                                    @endphp
                                    <span class="text-sm font-medium {{ $sC }}">{{ $sT }}</span><br>
                                    <span class="text-xs text-slate-400">{{ $revisiTerbaru->tanggal_submit ? $revisiTerbaru->tanggal_submit->format('d/m/Y H:i') : 'N/A' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2 mt-auto pt-4 border-t border-slate-100 bg-slate-50 -mx-5 px-5 -mb-5 pb-5 rounded-b-xl">
                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="inline-flex items-center justify-center rounded-md bg-navy-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-navy-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-600 transition-colors flex-1">
                                <i class="fas fa-eye me-1"></i>Detail
                            </a>
                            
                            @if($proposal->dokumen)
                            <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors flex-1">
                                <i class="fas fa-download me-1"></i>Unduh
                            </a>
                            @endif
                            
                            <button onclick="showReviewModal('administrative', '{{ $proposal->id_proposal }}')" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm ring-1 ring-inset ring-blue-300 hover:bg-blue-50 transition-colors flex-1">
                                <i class="fas fa-clipboard-check me-1"></i>Review
                            </button>
                            
                            @if($proposal->status === 'revisi' && $proposal->status_validasi !== 'pending')
                            <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="inline-flex items-center justify-center rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-amber-400 transition-colors w-full mt-2">
                                <i class="fas fa-edit me-1"></i>Revisi
                            </a>
                            @elseif($proposal->status === 'revisi' && $proposal->status_validasi === 'pending')
                            <button class="inline-flex items-center justify-center rounded-md bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 w-full mt-2 cursor-not-allowed opacity-70" disabled>
                                <i class="fas fa-clock me-1"></i>Menunggu Validasi Dosen
                            </button>
                            @elseif($proposal->status === 'revisi_akhir')
                            <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="inline-flex items-center justify-center rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-amber-400 transition-colors w-full mt-2">
                                <i class="fas fa-edit me-1"></i>Revisi Akhir
                            </a>
                            @elseif($proposal->status === 'revisi_submitted' || $proposal->status === 'validasi_akhir_dosen_univ')
                            <span class="inline-flex items-center justify-center rounded-md bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 w-full mt-2">
                                <i class="fas fa-clock me-1"></i>Menunggu Validasi
                            </span>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="text-center py-16 px-4">
                    <i class="fas fa-file-alt text-6xl text-slate-200 mb-6 block"></i>
                    <h4 class="text-xl font-bold text-slate-700 mb-3">Belum Ada Proposal</h4>
                    <p class="text-slate-500 mb-8 max-w-md mx-auto">Anda belum mengajukan proposal PKM. Mulai dengan mengajukan proposal baru untuk berpartisipasi dalam program PKM.</p>
                    
                    @if(\App\Helpers\RuangKontrolHelper::isPendaftaranActive())
                        <div class="flex justify-center">
                            <a href="{{ route('mahasiswa.proposal.create') }}" class="inline-flex items-center justify-center rounded-md bg-navy-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-navy-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-600 transition-colors">
                                <i class="fas fa-plus me-2"></i>Ajukan Proposal Pertama
                            </a>
                        </div>
                    @else
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-md max-w-lg mx-auto text-left">
                            <div class="flex items-center text-amber-800 font-bold mb-1">
                                <i class="fas fa-lock me-2"></i>
                                Sistem Pendaftaran Ditutup
                            </div>
                            <p class="text-sm text-amber-700 mb-0">Saat ini sistem pendaftaran proposal PKM sedang ditutup oleh operator.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Action Sidebar -->
<div class="fixed top-0 right-0 h-screen w-80 bg-white shadow-[-4px_0_15px_rgba(0,0,0,0.1)] z-[1001] transition-transform duration-300 transform translate-x-full overflow-y-auto" id="actionSidebar">
    <div class="bg-gradient-to-br from-navy-700 to-navy-900 text-white p-6 text-center">
        <h4 class="text-xl font-bold m-0"><i class="fas fa-tasks me-2"></i>Menu Aksi</h4>
    </div>
    <div class="p-6">
        <ul class="space-y-3">
            <li>
                <a href="#" onclick="showReviewModal('administrative', '{{ $proposals->first()->id_proposal ?? "" }}')" class="flex items-center p-4 bg-slate-50 rounded-xl text-slate-700 font-medium hover:bg-slate-100 hover:text-navy-700 hover:-translate-y-0.5 hover:shadow-md border-l-4 border-transparent hover:border-navy-600 transition-all duration-300">
                    <i class="fas fa-clipboard-check text-navy-600 text-xl w-8"></i>
                    <span>Hasil Review Administratif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('substantive', '{{ $proposals->first()->id_proposal ?? "" }}')" class="flex items-center p-4 bg-slate-50 rounded-xl text-slate-700 font-medium hover:bg-slate-100 hover:text-navy-700 hover:-translate-y-0.5 hover:shadow-md border-l-4 border-transparent hover:border-navy-600 transition-all duration-300">
                    <i class="fas fa-search text-navy-600 text-xl w-8"></i>
                    <span>Hasil Review Substantif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('final', '{{ $proposals->first()->id_proposal ?? "" }}')" class="flex items-center p-4 bg-slate-50 rounded-xl text-slate-700 font-medium hover:bg-slate-100 hover:text-navy-700 hover:-translate-y-0.5 hover:shadow-md border-l-4 border-transparent hover:border-navy-600 transition-all duration-300">
                    <i class="fas fa-trophy text-navy-600 text-xl w-8"></i>
                    <span>Hasil Final</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Modal Overlay -->
<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden items-center justify-center transition-opacity opacity-0" id="modalOverlay">
    <div class="bg-white rounded-2xl w-[90%] max-w-2xl max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="bg-navy-900 px-6 py-4 flex justify-between items-center rounded-t-2xl border-b border-navy-800">
            <h5 class="text-white text-lg font-bold flex items-center gap-2 m-0" id="modalTitle">Hasil Review</h5>
            <button class="bg-white/10 hover:bg-white/20 text-white/70 hover:text-white w-8 h-8 rounded-lg flex items-center justify-center transition-colors" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto bg-slate-50 flex-grow" id="modalBody">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Action Toggle Button -->
<button class="fixed right-6 bottom-6 w-14 h-14 bg-navy-600 hover:bg-navy-700 text-white rounded-full text-2xl shadow-lg hover:shadow-xl transition-all duration-300 z-[1000] hover:scale-110 flex items-center justify-center focus:outline-none" id="actionToggleBtn" onclick="toggleActionSidebar()">
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
            actionSidebar.classList.toggle('translate-x-full');
            actionSidebar.classList.toggle('translate-x-0');
            
            if (actionSidebar.classList.contains('translate-x-0')) {
                toggleBtn.style.right = '340px';
            } else {
                toggleBtn.style.right = '24px'; // 1.5rem for right-6
            }
        }
    }

    function showReviewModal(type, proposalId) {
        const modalOverlay = document.getElementById('modalOverlay');
        const modalContent = document.getElementById('modalContent');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        // Show loading
        modalBody.innerHTML = '<div class="text-center py-10"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-slate-200 border-t-navy-600 mb-4"></div><p class="text-slate-500 font-medium">Memuat data...</p></div>';
        
        modalOverlay.classList.remove('hidden');
        modalOverlay.classList.add('flex');
        
        // Trigger reflow for transition
        void modalOverlay.offsetWidth;
        
        modalOverlay.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
        
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
        const modalContent = document.getElementById('modalContent');
        
        modalOverlay.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modalOverlay.classList.remove('flex');
            modalOverlay.classList.add('hidden');
        }, 300);
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
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-list text-6xl text-slate-300 mb-4 block"></i>
                            <h6 class="text-lg font-bold text-slate-700 mb-2">Belum ada review administratif</h6>
                            <p class="text-slate-500">Review administratif akan muncul di sini setelah proposal direview oleh reviewer.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex items-center text-red-700 font-bold mb-1">
                            <i class="fas fa-exclamation-triangle me-2"></i> Error
                        </div>
                        <p class="text-sm text-red-600 mb-0">Terjadi kesalahan saat memuat data review administratif.</p>
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
                        <div class="text-center py-12">
                            <i class="fas fa-user-check text-6xl text-slate-300 mb-4 block"></i>
                            <h6 class="text-lg font-bold text-slate-700 mb-2">Belum ada review substantif</h6>
                            <p class="text-slate-500">Review substantif akan muncul di sini setelah proposal lolos review administratif.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading substantive review:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex items-center text-red-700 font-bold mb-1">
                            <i class="fas fa-exclamation-triangle me-2"></i> Error
                        </div>
                        <p class="text-sm text-red-600 mb-0">Terjadi kesalahan saat memuat data review substantif.<br><small>${error.message}</small></p>
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
                    document.getElementById('modalBody').innerHTML = generateFinalReviewHTML(
                        data.data, 
                        data.proposal_info, 
                        data.dosen_universitas,
                        data.hasil_semi_final
                    );
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
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <h6 class="text-navy-700 font-bold mb-4 flex items-center gap-2 pb-2 border-b border-slate-100"><i class="fas fa-file-alt text-navy-500"></i>Informasi Proposal</h6>
                    <div class="space-y-3">
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Judul:</span><span class="text-slate-800 font-medium">${proposalInfo.judul}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Skim:</span><span class="text-slate-800">${proposalInfo.skim}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 inline-block mr-2">Status:</span><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${proposalInfo.status}</span></div>
                    </div>
                </div>
        `;
        
        if (reviews.length > 0) {
            const review = reviews[0];
            html += `
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-navy-600 text-white px-5 py-3 font-semibold flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i>
                        Review Administratif
                        ${review.reviewer ? ` <span class="font-normal text-navy-100 text-sm ml-auto">(${review.reviewer.nama_reviewer})</span>` : ''}
                    </div>
                    <div class="p-5">
            `;
            
            if (review.checklist && review.checklist.length > 0) {
                html += '<div class="mb-5"><strong class="text-slate-700 block mb-3">Kesalahan Administratif yang Ditemukan:</strong><ul class="space-y-3">';
                
                review.checklist.forEach((item) => {
                    html += `
                        <li class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-100">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center mt-0.5">
                                <i class="fas fa-times text-xs"></i>
                            </span>
                            <div>
                                <strong class="text-slate-800 text-sm">${item}</strong>
                                <div class="text-xs text-red-600 mt-1 font-medium">Status: Perlu Perbaikan</div>
                            </div>
                        </li>
                    `;
                });
                html += '</ul></div>';
            } else {
                html += `
                    <div class="mb-5">
                        <strong class="text-slate-700 block mb-3">Checklist Administratif:</strong>
                        <div class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-xl border border-green-100 text-green-700">
                            <i class="fas fa-check-circle text-4xl mb-3 text-green-500"></i>
                            <span class="font-medium">Semua kriteria telah memenuhi standar</span>
                        </div>
                    </div>
                `;
            }
            
            if (review.note_administratif) {
                html += `
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <div class="flex items-center text-blue-800 font-bold mb-2">
                            <i class="fas fa-edit me-2"></i> Catatan
                        </div>
                        <p class="text-sm text-blue-900 mb-0 leading-relaxed">${review.note_administratif}</p>
                    </div>
                `;
            }
            
            html += '</div></div>';
        } else {
            html += `
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
                    <i class="fas fa-clipboard-list text-5xl text-slate-300 mb-4 block"></i>
                    <h6 class="text-lg font-bold text-slate-700 mb-2">Belum ada review administratif</h6>
                    <p class="text-slate-500">Review administratif akan muncul di sini setelah proposal direview oleh reviewer.</p>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    function generateSubstantiveReviewHTML(reviews, proposalInfo) {
        console.log('Generating substantive review HTML with:', { reviews, proposalInfo });
        
        let html = `
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <h6 class="text-navy-700 font-bold mb-4 flex items-center gap-2 pb-2 border-b border-slate-100"><i class="fas fa-file-alt text-navy-500"></i>Informasi Proposal</h6>
                    <div class="space-y-3">
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Judul:</span><span class="text-slate-800 font-medium">${proposalInfo.judul}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Skim:</span><span class="text-slate-800">${proposalInfo.skim}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 inline-block mr-2">Status:</span><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${proposalInfo.status}</span></div>
                    </div>
                </div>
        `;
        
        if (reviews && reviews.length > 0) {
            console.log(`Found ${reviews.length} substantive reviews`);
            
            reviews.forEach((review, index) => {
                console.log(`Processing review ${index + 1}:`, review);
                
                html += `
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-cyan-700 text-white px-5 py-3 font-semibold flex items-center gap-2">
                            <i class="fas fa-user text-cyan-200"></i>
                            Reviewer Substantif ${index + 1}
                            ${review.reviewer ? ` <span class="font-normal text-cyan-100 text-sm ml-auto">(${review.reviewer.nama_reviewer})</span>` : ''}
                        </div>
                        <div class="p-5">
                            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
                                <div class="flex items-center text-amber-800 font-bold mb-2">
                                    <i class="fas fa-edit me-2"></i> Catatan
                                </div>
                                <p class="text-sm text-amber-900 mb-0 leading-relaxed whitespace-pre-wrap">${review.note_substantif || 'Tidak ada catatan khusus.'}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            console.log('No substantive reviews found');
            html += `
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
                    <i class="fas fa-user-check text-5xl text-slate-300 mb-4 block"></i>
                    <h6 class="text-lg font-bold text-slate-700 mb-2">Belum ada review substantif</h6>
                    <p class="text-slate-500">Review substantif akan muncul di sini setelah proposal lolos review administratif.</p>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    function generateFinalReviewHTML(finalResult, proposalInfo, dosenUniversitas = null, hasilSemiFinal = null) {
        // Tentukan status berdasarkan hasil final dari Pimpinan PT
        let statusClass = 'bg-slate-100 text-slate-800 border-slate-200';
        let statusText = 'BELUM DINILAI';
        let statusIcon = 'clock';
        let statusHeaderBg = 'bg-slate-600';
        
        if (finalResult) {
            // Status PIMNAS
            const statusPimnas = finalResult.status_pimnas;
            const statusPendanaan = finalResult.status_pendanaan;
            
            if (statusPimnas === 'lolos' && statusPendanaan === 'lolos') {
                statusClass = 'bg-green-50 text-green-800 border-green-200';
                statusText = 'LOLOS PIMNAS & PENDANAAN';
                statusIcon = 'trophy';
                statusHeaderBg = 'bg-green-600';
            } else if (statusPimnas === 'lolos' && statusPendanaan === 'tidak_lolos') {
                statusClass = 'bg-amber-50 text-amber-800 border-amber-200';
                statusText = 'LOLOS PIMNAS (TIDAK PENDANAAN)';
                statusIcon = 'trophy';
                statusHeaderBg = 'bg-amber-500';
            } else if (statusPimnas === 'tidak_lolos' && statusPendanaan === 'lolos') {
                statusClass = 'bg-blue-50 text-blue-800 border-blue-200';
                statusText = 'TIDAK LOLOS PIMNAS (LOLOS PENDANAAN)';
                statusIcon = 'money-bill-wave';
                statusHeaderBg = 'bg-blue-600';
            } else {
                statusClass = 'bg-red-50 text-red-800 border-red-200';
                statusText = 'TIDAK LOLOS';
                statusIcon = 'times-circle';
                statusHeaderBg = 'bg-red-600';
            }
        }
        
        let html = `
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <h6 class="text-navy-700 font-bold mb-4 flex items-center gap-2 pb-2 border-b border-slate-100"><i class="fas fa-file-alt text-navy-500"></i>Informasi Proposal</h6>
                    <div class="space-y-3">
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Judul:</span><span class="text-slate-800 font-medium">${proposalInfo.judul}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 block mb-1">Skim:</span><span class="text-slate-800">${proposalInfo.skim}</span></div>
                        <div><span class="text-sm font-semibold text-slate-500 inline-block mr-2">Status:</span><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${proposalInfo.status}</span></div>
                    </div>
                </div>
        `;
                
        if (dosenUniversitas) {
            html += `
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden border-l-4 border-l-indigo-500">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-5 py-3 font-semibold flex items-center gap-2">
                        <i class="fas fa-user-tie"></i>
                        Dosen Pendamping Universitas
                    </div>
                    <div class="p-5 space-y-2">
                        <div><span class="text-sm font-semibold text-slate-500 inline-block w-20">Nama:</span><span class="text-slate-800">${dosenUniversitas.nama_dosen}</span></div>
                        ${dosenUniversitas.no_hp_dosen ? `<div><span class="text-sm font-semibold text-slate-500 inline-block w-20">No. HP:</span><span class="text-slate-800">${dosenUniversitas.no_hp_dosen}</span></div>` : ''}
                        ${dosenUniversitas.email_dosen ? `<div><span class="text-sm font-semibold text-slate-500 inline-block w-20">Email:</span><span class="text-slate-800">${dosenUniversitas.email_dosen}</span></div>` : ''}
                    </div>
                </div>
                ` : ''}
                
                ${hasilSemiFinal ? `
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-cyan-600 text-white px-5 py-3 font-semibold flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i>
                        Hasil Semi Final
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <span class="text-sm font-semibold text-slate-500 mr-2">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${hasilSemiFinal.status_final === 'lolos_tingkat_universitas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${hasilSemiFinal.status_final === 'lolos_tingkat_universitas' ? 'Lolos Tingkat Universitas' : 'Tidak Lolos Tingkat Universitas'}
                            </span>
                        </div>
                        <div><span class="text-sm font-semibold text-slate-500 mr-2">Nilai:</span><span class="font-bold text-slate-800">${hasilSemiFinal.nilai ? parseFloat(hasilSemiFinal.nilai).toFixed(2) : 'N/A'}</span></div>
                        ${hasilSemiFinal.catatan_final ? `
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 mt-2">
                            <span class="text-sm font-semibold text-slate-500 block mb-1">Catatan:</span>
                            <p class="text-sm text-slate-700 whitespace-pre-wrap">${hasilSemiFinal.catatan_final}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="${statusHeaderBg} text-white px-5 py-3 font-semibold flex items-center gap-2">
                        <i class="fas fa-${statusIcon}"></i>
                        Hasil Final (Pimpinan PT)
                    </div>
                    <div class="p-5">
                        ${finalResult ? `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div class="${finalResult.status_pimnas === 'lolos' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'} border rounded-lg p-4 flex items-center gap-3">
                                <i class="fas fa-${finalResult.status_pimnas === 'lolos' ? 'trophy text-green-600' : 'times-circle text-red-600'} text-2xl"></i>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Status PIMNAS</div>
                                    <div class="font-bold text-lg">${finalResult.status_pimnas === 'lolos' ? 'LOLOS' : 'TIDAK LOLOS'}</div>
                                </div>
                            </div>
                            
                            <div class="${finalResult.status_pendanaan === 'lolos' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'} border rounded-lg p-4 flex items-center gap-3">
                                <i class="fas fa-${finalResult.status_pendanaan === 'lolos' ? 'money-bill-wave text-green-600' : 'times-circle text-red-600'} text-2xl"></i>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Status Pendanaan</div>
                                    <div class="font-bold text-lg">${finalResult.status_pendanaan === 'lolos' ? 'LOLOS' : 'TIDAK LOLOS'}</div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 flex items-center gap-3">
                                <i class="fas fa-star text-blue-600 text-2xl"></i>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Nilai</div>
                                    <div class="font-bold text-lg">${finalResult.nilai ? parseFloat(finalResult.nilai).toFixed(2) : 'N/A'}</div>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 text-slate-800 rounded-lg p-4 flex items-center gap-3">
                                <i class="fas fa-calendar-alt text-slate-500 text-2xl"></i>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Tanggal Penilaian</div>
                                    <div class="font-bold">${new Date(finalResult.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                                </div>
                            </div>
                        </div>
                        
                        ${finalResult.dana_didapatkan_belmawa > 0 || finalResult.dana_didapatkan_operator > 0 ? `
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-5">
                            <h6 class="font-bold text-emerald-800 mb-3 flex items-center gap-2"><i class="fas fa-money-bill-wave"></i> Rincian Dana Didapatkan</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-md border border-emerald-100 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-500 mb-1">Dana Belmawa</div>
                                    <div class="text-lg font-bold text-emerald-700">Rp ${formatRupiah(finalResult.dana_didapatkan_belmawa || 0)}</div>
                                </div>
                                <div class="bg-white p-3 rounded-md border border-emerald-100 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-500 mb-1">Dana Universitas</div>
                                    <div class="text-lg font-bold text-emerald-700">Rp ${formatRupiah(finalResult.dana_didapatkan_operator || 0)}</div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${finalResult.catatan_final ? `
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
                            <h6 class="font-bold text-amber-800 mb-2 flex items-center gap-2"><i class="fas fa-comment"></i> Catatan Final:</h6>
                            <p class="text-sm text-amber-900 mb-0 leading-relaxed whitespace-pre-wrap">${finalResult.catatan_final}</p>
                        </div>
                        ` : ''}
                        ` : `
                        <div class="text-center py-10">
                            <i class="fas fa-clock text-6xl text-slate-300 mb-4 block"></i>
                            <h6 class="text-lg font-bold text-slate-700 mb-2">Belum ada hasil final</h6>
                            <p class="text-slate-500">Hasil final akan muncul di sini setelah Pimpinan PT melakukan penilaian.</p>
                        </div>
                        `}
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

    // Format Rupiah helper function
    function formatRupiah(value) {
        if (!value) return '0';
        const num = parseFloat(value);
        return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

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