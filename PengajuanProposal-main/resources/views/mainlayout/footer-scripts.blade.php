<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    function applyIndonesianNumberFormatting() {
        if (window.AppUI?.applyIndonesianNumberFormatting) {
            window.AppUI.applyIndonesianNumberFormatting(document);
        }
    }

    // Show Review Modal
    function showReviewModal(type) {
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        const title = document.getElementById('reviewModalTitle');
        const body = document.getElementById('reviewModalBody');
        
        if (type === 'administratif') {
            title.innerHTML = '<i class="fas fa-clipboard-check me-2"></i>Hasil Review Administratif';
            body.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Hasil Review Kesalahan Administratif</strong>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            Kesalahan yang Ditemukan:
                        </h6>
                        <ol class="mb-3">
                            <li>Margin tidak sesuai dengan ketentuan (harus 4-4-3-3 cm)</li>
                            <li>Penulisan judul tidak sesuai format yang ditentukan</li>
                            <li>Font yang digunakan tidak sesuai standar (harus Times New Roman)</li>
                            <li>Spasi antar paragraf tidak konsisten</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="fas fa-edit text-info me-2"></i>
                            <strong>Catatan:</strong><br>
                            Berikan revisi sesuai kesalahan administratif yang ditemukan agar proposal memungkinkan untuk lolos ke tahap review substantif. Semangat!!
                        </div>
                    </div>
                </div>
            `;
        } else {
            title.innerHTML = '<i class="fas fa-user-check me-2"></i>Hasil Review Subtantif';
            body.innerHTML = `
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user me-2"></i>
                        <strong>Reviewer 1 - Dr. Sari Widyastuti, M.Kom</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-edit text-warning me-2"></i>
                            <strong>Catatan:</strong><br>
                            Isi proposal sudah baik secara substantif, akan tetapi ada beberapa penggunaan AI yang masih terdeteksi dalam penulisan. Mohon untuk menulis ulang bagian tersebut dengan bahasa yang lebih natural.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nilai: 85/100</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="badge bg-warning">Perlu Revisi</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user me-2"></i>
                        <strong>Reviewer 2 - Prof. Dr. Budi Santoso, M.T</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-edit text-warning me-2"></i>
                            <strong>Catatan:</strong><br>
                            Proposal memiliki inovasi yang menarik dan metodologi yang jelas. Namun perlu perbaikan pada bagian analisis data dan kesimpulan.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nilai: 90/100</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="badge bg-success">Disetujui</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        modal.show();
    }

    // Action Sidebar Toggle (for lihat proposal page)
    function toggleActionSidebar() {
        const actionSidebar = document.getElementById('actionSidebar');
        actionSidebar.classList.toggle('show');
    }

    // Show Toast Notification
    function showToast(message, type = 'info', duration = 3000) {
        if (window.AppUI?.showToast) {
            window.AppUI.showToast(message, type, duration);
        }
    }

    // Notification System
    class NotificationSystem {
        constructor() {
            this.notifications = [];
            this.unreadCount = 0;
            this.init();
        }

        init() {
            this.loadNotifications();
            this.setupEventListeners();
            this.startPolling();
        }

        setupEventListeners() {
            try {
                // Mark all as read
                const markAllReadBtn = document.getElementById('markAllRead');
                if (markAllReadBtn) {
                    markAllReadBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.markAllAsRead();
                    });
                }

                // View all notifications
                const viewAllBtn = document.getElementById('viewAllNotifications');
                if (viewAllBtn) {
                    viewAllBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.showAllNotifications();
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    const dropdown = document.querySelector('.notification-dropdown');
                    const toggle = document.getElementById('notificationDropdown');
                    
                    if (!dropdown?.contains(e.target) && !toggle?.contains(e.target)) {
                        const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                        if (bsDropdown) {
                            bsDropdown.hide();
                        }
                    }
                });
            } catch (error) {
                console.error('Error setting up notification event listeners:', error);
            }
        }

        async loadNotifications() {
            try {
                // Deteksi user type dan gunakan route yang sesuai
                let notificationRoute = '';
                
                // Cek apakah ada route yang sesuai dengan user type
                if (window.location.pathname.includes('/dosen/')) {
                    notificationRoute = '{{ route("dosen.notifications.get") }}';
                } else if (window.location.pathname.includes('/mahasiswa/')) {
                    notificationRoute = '{{ route("mahasiswa.notifications.get") }}';
                } else if (window.location.pathname.includes('/reviewer/')) {
                    notificationRoute = '{{ route("reviewer.notifications.get") }}';
                } else if (window.location.pathname.includes('/operator/')) {
                    notificationRoute = '{{ route("operator.notifications.get") }}';
                } else {
                    // Fallback jika tidak ada route yang cocok
                    console.warn('No notification route found for current path');
                    this.loadSampleNotifications();
                    return;
                }
                
                const response = await fetch(notificationRoute, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    // Format notifications untuk memastikan semua field ada
                    this.notifications = (data.notifications || []).map(notif => ({
                        ...notif,
                        unread: notif.unread !== undefined ? notif.unread : !notif.read_at,
                        data: notif.data || {},
                        proposal_id: notif.proposal_id || (notif.data?.proposal_id || null)
                    }));
                    this.updateNotificationCount();
                    this.renderNotifications();
                } else {
                    console.error('Failed to load notifications');
                    // Fallback to sample notifications
                    this.loadSampleNotifications();
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
                // Fallback to sample notifications
                this.loadSampleNotifications();
            }
        }

        loadSampleNotifications() {
            // Fallback notifications when API is not available
            this.notifications = [
                {
                    id: 1,
                    type: 'success',
                    title: 'Proposal Disetujui',
                    message: 'Proposal PKM Anda telah disetujui dan lolos ke tahap review substantif.',
                    time: '2 jam yang lalu',
                    unread: true,
                    actions: ['Lihat Detail', 'Download']
                },
                {
                    id: 2,
                    type: 'warning',
                    title: 'Perlu Revisi',
                    message: 'Proposal PKM Anda memerlukan revisi pada bagian metodologi penelitian.',
                    time: '1 hari yang lalu',
                    unread: true,
                    actions: ['Lihat Detail', 'Revisi']
                },
                {
                    id: 3,
                    type: 'info',
                    title: 'Status Berubah',
                    message: 'Status proposal Anda telah berubah dari "Draft" menjadi "Under Review".',
                    time: '3 hari yang lalu',
                    unread: false,
                    actions: ['Lihat Detail']
                },
                {
                    id: 4,
                    type: 'danger',
                    title: 'Proposal Ditolak',
                    message: 'Mohon maaf, proposal PKM Anda tidak dapat diproses karena tidak memenuhi syarat administratif.',
                    time: '1 minggu yang lalu',
                    unread: false,
                    actions: ['Lihat Detail', 'Ajukan Ulang']
                }
            ];

            this.updateNotificationCount();
            this.renderNotifications();
        }

        renderNotifications() {
            try {
                const container = document.getElementById('notificationList');
                if (!container) {
                    console.warn('Notification container not found');
                    return;
                }

                if (this.notifications.length === 0) {
                    container.innerHTML = `
                        <div class="empty-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = this.notifications.map(notification => {
                    // Cek apakah notifikasi negatif (penolakan)
                    const isNegative = notification.data?.is_negative || false;
                    const hasCatatan = notification.data?.catatan || notification.data?.catatan_final || notification.data?.catatan_operator || notification.data?.catatan_dosen;
                    const proposalId = notification.data?.proposal_id || notification.proposal_id;
                    const action = notification.data?.action;
                    
                    return `
                        <div class="notification-item ${notification.unread ? 'unread' : ''} ${isNegative ? 'negative' : ''}" data-id="${notification.id}">
                            <div class="notification-content">
                                <div class="notification-icon-small ${notification.type}">
                                    <i class="fas fa-${this.getIconForType(notification.type)}"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                                    <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                                    ${hasCatatan ? `
                                        <div class="catatan-box">
                                            <strong>Catatan:</strong> ${this.escapeHtml(hasCatatan)}
                                        </div>
                                    ` : ''}
                                    <div class="notification-time">${notification.time || notification.created_at || ''}</div>
                                    ${action && proposalId ? `
                                        <button class="action-button" 
                                                onclick="event.stopPropagation(); notificationSystem.handleAction(${notification.id}, '${action}', ${proposalId})">
                                            ${this.getActionText(action)}
                                        </button>
                                    ` : notification.actions && notification.actions.length > 0 ? `
                                        <div class="notification-actions">
                                            ${notification.actions.map(action => `
                                                <button class="btn btn-sm btn-outline-${isNegative ? 'danger' : this.getButtonStyle(notification.type)}" 
                                                        onclick="event.stopPropagation(); notificationSystem.handleAction(${notification.id}, '${action}')">
                                                    ${action}
                                                </button>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                // Add click event to mark as read
                container.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const id = parseInt(item.dataset.id);
                        this.markAsRead(id);
                    });
                });
            } catch (error) {
                console.error('Error rendering notifications:', error);
                // Fallback to simple display
                const container = document.getElementById('notificationList');
                if (container) {
                    container.innerHTML = `
                        <div class="empty-notifications">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Gagal memuat notifikasi</p>
                        </div>
                    `;
                }
            }
        }

        getIconForType(type) {
            const icons = {
                success: 'check-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle',
                danger: 'times-circle'
            };
            return icons[type] || 'info-circle';
        }

        getButtonStyle(type) {
            const styles = {
                success: 'success',
                warning: 'warning',
                info: 'primary',
                danger: 'danger'
            };
            return styles[type] || 'primary';
        }

        updateNotificationCount() {
            try {
                this.unreadCount = this.notifications.filter(n => n.unread).length;
                const badge = document.getElementById('notificationCount');
                
                if (badge) {
                    if (this.unreadCount > 0) {
                        badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error updating notification count:', error);
            }
        }

        async markAsRead(id) {
            try {
                // Deteksi user type dan gunakan route yang sesuai
                let markReadRoute = '';
                
                if (window.location.pathname.includes('/dosen/')) {
                    markReadRoute = '{{ route("dosen.notifications.mark-read") }}';
                } else if (window.location.pathname.includes('/mahasiswa/')) {
                    markReadRoute = '{{ route("mahasiswa.notifications.mark-read") }}';
                } else if (window.location.pathname.includes('/reviewer/')) {
                    markReadRoute = '{{ route("reviewer.notifications.mark-read") }}';
                } else if (window.location.pathname.includes('/operator/')) {
                    markReadRoute = '{{ route("operator.notifications.mark-read") }}';
                } else {
                    console.warn('No mark-read route found for current path');
                    return;
                }
                
                const response = await fetch(markReadRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ notification_id: id }),
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification && notification.unread) {
                        notification.unread = false;
                        this.updateNotificationCount();
                        this.renderNotifications();
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                // Fallback to local update
                const notification = this.notifications.find(n => n.id === id);
                if (notification && notification.unread) {
                    notification.unread = false;
                    this.updateNotificationCount();
                    this.renderNotifications();
                }
            }
        }

        async markAllAsRead() {
            try {
                // Deteksi user type dan gunakan route yang sesuai
                let markAllReadRoute = '';
                
                if (window.location.pathname.includes('/dosen/')) {
                    markAllReadRoute = '{{ route("dosen.notifications.mark-all-read") }}';
                } else if (window.location.pathname.includes('/mahasiswa/')) {
                    markAllReadRoute = '{{ route("mahasiswa.notifications.mark-all-read") }}';
                } else if (window.location.pathname.includes('/reviewer/')) {
                    markAllReadRoute = '{{ route("reviewer.notifications.mark-all-read") }}';
                } else if (window.location.pathname.includes('/operator/')) {
                    markAllReadRoute = '{{ route("operator.notifications.mark-all-read") }}';
                } else {
                    console.warn('No mark-all-read route found for current path');
                    return;
                }
                
                const response = await fetch(markAllReadRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    this.notifications.forEach(n => n.unread = false);
                    this.updateNotificationCount();
                    this.renderNotifications();
                    
                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
                    }
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
                // Fallback to local update
                this.notifications.forEach(n => n.unread = false);
                this.updateNotificationCount();
                this.renderNotifications();
                
                if (typeof showToast === 'function') {
                    showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
                }
            }
        }

        handleAction(id, action, proposalId = null) {
            try {
                const notification = this.notifications.find(n => n.id === id);
                if (!notification) {
                    console.warn(`Notification with id ${id} not found`);
                    return;
                }

                // Jika ada proposalId, gunakan action URL
                if (proposalId && action) {
                    const url = this.getActionUrl(action, proposalId);
                    if (url && url !== '#') {
                        window.location.href = url;
                        return;
                    }
                }

                // Handle different actions (fallback untuk action lama)
                switch (action) {
                    case 'Lihat Detail':
                    case 'view_proposal':
                        if (proposalId) {
                            window.location.href = this.getActionUrl('view_proposal', proposalId);
                        } else {
                            this.showNotificationDetail(notification);
                        }
                        break;
                    case 'Download':
                        this.downloadDocument(notification);
                        break;
                    case 'Revisi':
                    case 'revisi_proposal':
                    case 'upload_revisi':
                        if (proposalId) {
                            window.location.href = this.getActionUrl('upload_revisi', proposalId);
                        } else {
                            this.openRevisionForm(notification);
                        }
                        break;
                    case 'upload_revisi_akhir':
                        if (proposalId) {
                            window.location.href = this.getActionUrl('upload_revisi_akhir', proposalId);
                        } else {
                            this.openRevisionForm(notification);
                        }
                        break;
                    case 'view_hasil_final':
                        if (proposalId) {
                            window.location.href = this.getActionUrl('view_hasil_final', proposalId);
                        } else {
                            this.showNotificationDetail(notification);
                        }
                        break;
                    case 'Ajukan Ulang':
                        this.resubmitProposal(notification);
                        break;
                    default:
                        console.log(`Action: ${action} for notification ${id}`);
                }
            } catch (error) {
                console.error('Error handling notification action:', error);
            }
        }

        getActionUrl(action, proposalId) {
            const baseUrl = window.location.origin;
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

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        showNotificationDetail(notification) {
            // Show modal or navigate to detail page
            if (typeof showToast === 'function') {
                showToast(`Membuka detail: ${notification.title}`, 'info');
            }
        }

        downloadDocument(notification) {
            if (typeof showToast === 'function') {
                showToast('Mengunduh dokumen...', 'info');
            }
        }

        openRevisionForm(notification) {
            if (typeof showToast === 'function') {
                showToast('Membuka form revisi...', 'info');
            }
        }

        resubmitProposal(notification) {
            if (typeof showToast === 'function') {
                showToast('Membuka form pengajuan ulang...', 'info');
            }
        }

        startPolling() {
            try {
                // Poll for new notifications every 30 seconds
                this.pollingInterval = setInterval(() => {
                    this.checkForNewNotifications();
                }, 30000);
            } catch (error) {
                console.error('Error starting notification polling:', error);
            }
        }

        checkForNewNotifications() {
            try {
                // Simulate checking for new notifications
                // In real implementation, this would make an AJAX call to the server
                const hasNewNotifications = Math.random() > 0.8; // 20% chance of new notification
                
                if (hasNewNotifications) {
                    this.addNewNotification();
                }
            } catch (error) {
                console.error('Error checking for new notifications:', error);
            }
        }

        addNewNotification() {
            try {
                const newNotification = {
                    id: Date.now(),
                    type: 'info',
                    title: 'Notifikasi Baru',
                    message: 'Ada pembaruan status proposal yang perlu Anda periksa.',
                    time: 'Baru saja',
                    unread: true,
                    actions: ['Lihat Detail']
                };

                this.notifications.unshift(newNotification);
                this.updateNotificationCount();
                this.renderNotifications();

                // Show toast notification
                if (typeof showToast === 'function') {
                    showToast('Anda memiliki notifikasi baru', 'info');
                }
            } catch (error) {
                console.error('Error adding new notification:', error);
            }
        }

        showAllNotifications() {
            // Navigate to notifications page or show all in modal
            if (typeof showToast === 'function') {
                showToast('Membuka halaman semua notifikasi', 'info');
            }
        }

        // Cleanup function
        cleanup() {
            try {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
                }
            } catch (error) {
                console.error('Error cleaning up notification system:', error);
            }
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Layout loaded successfully');
        window.AppUI?.initSidebarInteractions?.();
        applyIndonesianNumberFormatting();
        
        if (window.initGlobalFormLoading) {
            window.initGlobalFormLoading();
        }
        
        // Initialize notification system only once
        if (!window.notificationSystem) {
            try {
                window.notificationSystem = new NotificationSystem();
                console.log('Notification system initialized successfully');
            } catch (error) {
                console.error('Failed to initialize notification system:', error);
            }
        } else {
            console.log('Notification system already exists');
        }
    });

    // Cleanup notification system when page unloads
    window.addEventListener('beforeunload', function() {
        if (window.notificationSystem) {
            window.notificationSystem.cleanup();
        }
    });

    // Load custom PDF viewer script
    const pdfViewerScript = document.createElement('script');
    pdfViewerScript.src = '/js/pdf-viewer.js';
    pdfViewerScript.onload = function() {
        console.log('PDF Viewer script loaded successfully');
    };
    pdfViewerScript.onerror = function() {
        console.error('Failed to load PDF Viewer script');
    };
    document.head.appendChild(pdfViewerScript);
</script>
