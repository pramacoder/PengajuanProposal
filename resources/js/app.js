import './bootstrap';
import Alpine from 'alpinejs'
 
window.Alpine = Alpine
 
Alpine.start()

function parseAngkaIndonesia(value) {
    if (value === null || value === undefined) return '';
    return String(value).replace(/[^\d]/g, '');
}

function formatAngkaIndonesia(value) {
    const digits = parseAngkaIndonesia(value);
    if (!digits) return '';
    return Number(digits).toLocaleString('id-ID');
}

function applyIndonesianNumberFormatting(root = document) {
    const fields = root.querySelectorAll('input.js-format-id-int');
    fields.forEach((field) => {
        field.value = formatAngkaIndonesia(field.value);
        if (field.dataset.idFormatInputBound === '1') return;
        field.dataset.idFormatInputBound = '1';
        field.addEventListener('input', function onInputFormat() {
            this.value = formatAngkaIndonesia(this.value);
        });
    });

    root.querySelectorAll('form').forEach((form) => {
        if (form.dataset.idFormatBound === '1') return;
        form.dataset.idFormatBound = '1';
        form.addEventListener('submit', function onSubmitParseIdFormat() {
            this.querySelectorAll('input.js-format-id-int').forEach((field) => {
                field.value = parseAngkaIndonesia(field.value);
            });
        });
    });
}

function showToast(message, type = 'info', duration = 3000) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    const toast = document.createElement('div');
    toast.className = 'toast toast-custom show';
    toast.style.cssText = 'min-width: 300px; margin-bottom: 10px;';

    const icon = type === 'success'
        ? 'check-circle'
        : type === 'error'
            ? 'times-circle'
            : type === 'warning'
                ? 'exclamation-triangle'
                : 'info-circle';

    const color = type === 'success'
        ? '#28a745'
        : type === 'error'
            ? '#dc3545'
            : type === 'warning'
                ? '#ffc107'
                : '#17a2b8';

    toast.innerHTML = `
        <div class="toast-header">
            <i class="fas fa-${icon} me-2" style="color: ${color}"></i>
            <strong class="me-auto">Notifikasi</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;

    toastContainer.appendChild(toast);
    window.setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, duration);
}

function initSidebarInteractions() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && overlay && sidebarToggle.dataset.bound !== '1') {
        sidebarToggle.dataset.bound = '1';
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay && sidebar && overlay.dataset.bound !== '1') {
        overlay.dataset.bound = '1';
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    document.querySelectorAll('.menu-toggle').forEach((toggle) => {
        if (toggle.dataset.bound === '1') return;
        toggle.dataset.bound = '1';
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            const targetId = toggle.getAttribute('data-target');
            if (!targetId) return;
            const submenu = document.getElementById(targetId);
            if (!submenu) return;
            submenu.classList.toggle('show');
            toggle.classList.toggle('active');
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = submenu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
    });

    document.querySelectorAll('.submenu').forEach((submenu) => {
        const hasActiveChild = submenu.querySelector('.active');
        if (!hasActiveChild) return;
        submenu.classList.add('show');
        const parentToggle = submenu.parentElement?.querySelector('.menu-toggle');
        if (!parentToggle) return;
        parentToggle.classList.add('active');
        const chevron = parentToggle.querySelector('.fa-chevron-down');
        if (chevron) {
            chevron.style.transform = 'rotate(180deg)';
        }
    });
}

window.AppUI = {
    parseAngkaIndonesia,
    formatAngkaIndonesia,
    applyIndonesianNumberFormatting,
    showToast,
    initSidebarInteractions,
};

window.parseAngkaIndonesia = parseAngkaIndonesia;
window.formatAngkaIndonesia = formatAngkaIndonesia;
window.applyIndonesianNumberFormatting = applyIndonesianNumberFormatting;
window.showToast = showToast;