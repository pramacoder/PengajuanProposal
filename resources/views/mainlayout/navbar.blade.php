<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <!-- Hamburger Menu -->
        <button class="btn btn-link text-white d-lg-none me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <a class="navbar-brand" href="#">
            <i class="fas fa-graduation-cap"></i>
            Pengajuan Proposal
        </a>
        
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
            <!-- Notifications -->
            <div class="nav-item dropdown me-3 position-relative">
                <a class="nav-link dropdown-toggle d-flex align-items-center position-relative" href="#" data-bs-toggle="dropdown" id="notificationDropdown">
                    <div class="notification-icon-wrapper">
                        <img src="{{ asset('ion_notifcations.svg') }}" alt="Notifikasi" class="notification-icon" width="24" height="24">
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px; max-height: 500px; overflow-y: auto;">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-bell me-2 text-primary"></i>
                            Notifikasi Sistem
                        </h6>
                        <button class="btn btn-sm btn-outline-primary" id="markAllRead">
                            Tandai Semua Dibaca
                        </button>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div id="notificationList">
                        <!-- Notifications will be populated here -->
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="text-center p-2">
                        <a href="#" class="text-decoration-none" id="viewAllNotifications">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- User Profile -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-2">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <span class="fw-bold">
                            {{ \App\Helpers\UserHelper::getCurrentUserName() }}
                        </span>
                    </div>
                </a>
                <x-user-profile-dropdown
                    headerTitle="Profil Pengguna"
                    menuClass="dropdown-menu dropdown-menu-end user-profile-dropdown"
                    menuStyle="min-width: 280px;"
                    menuItemClass="dropdown-item profile-menu-item"
                    infoTextClass="user-info"
                    logoutFormId="logout-form-main"
                />
            </div>
        </div>
    </div>
</nav>
