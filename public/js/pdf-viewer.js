/**
 * PDF Viewer Enhancement Script
 * Provides better PDF viewing experience with iframe
 */

class PDFViewer {
    constructor(containerId, pdfUrl) {
        this.container = document.getElementById(containerId);
        this.pdfUrl = pdfUrl;
        this.isFullscreen = false;
        this.init();
    }

    init() {
        this.createViewer();
        this.bindEvents();
        this.setupResponsive();
    }

    createViewer() {
        // Clear existing content
        this.container.innerHTML = '';
        
        // Create iframe
        this.iframe = document.createElement('iframe');
        this.iframe.src = this.pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH';
        this.iframe.style.cssText = 'width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 8px;';
        this.iframe.frameBorder = '0';
        this.iframe.allowFullscreen = true;
        this.iframe.title = 'PDF Viewer';
        
        // Add loading indicator
        this.iframe.style.opacity = '0.7';
        this.iframe.style.transition = 'opacity 0.3s ease';
        
        this.container.appendChild(this.iframe);
        
        // Handle load event
        this.iframe.addEventListener('load', () => {
            this.iframe.style.opacity = '1';
            this.hideLoadingIndicator();
        });
        
        // Handle error event
        this.iframe.addEventListener('error', () => {
            this.showError();
        });
    }

    bindEvents() {
        // Fullscreen toggle
        if (window.toggleFullscreen) {
            window.toggleFullscreen = () => this.toggleFullscreen();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isFullscreen) {
                this.exitFullscreen();
            }
        });
    }

    setupResponsive() {
        const resizeObserver = new ResizeObserver(() => {
            this.adjustSize();
        });
        
        resizeObserver.observe(this.container);
        
        // Initial size adjustment
        this.adjustSize();
    }

    adjustSize() {
        const containerWidth = this.container.offsetWidth;
        
        if (containerWidth < 768) {
            this.iframe.style.height = '400px';
        } else if (containerWidth < 1200) {
            this.iframe.style.height = '500px';
        } else {
            this.iframe.style.height = '600px';
        }
    }

    toggleFullscreen() {
        if (!this.isFullscreen) {
            this.enterFullscreen();
        } else {
            this.exitFullscreen();
        }
    }

    enterFullscreen() {
        this.container.classList.add('fullscreen');
        this.isFullscreen = true;
        
        // Adjust iframe for fullscreen
        this.iframe.style.height = '100vh';
        this.iframe.style.borderRadius = '0';
        
        // Update button text
        const fullscreenBtn = document.querySelector('button[onclick="toggleFullscreen()"]');
        if (fullscreenBtn) {
            fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i>Exit Fullscreen';
        }
    }

    exitFullscreen() {
        this.container.classList.remove('fullscreen');
        this.isFullscreen = false;
        
        // Restore iframe size
        this.adjustSize();
        this.iframe.style.borderRadius = '8px';
        
        // Update button text
        const fullscreenBtn = document.querySelector('button[onclick="toggleFullscreen()"]');
        if (fullscreenBtn) {
            fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i>Fullscreen';
        }
    }

    showLoadingIndicator() {
        if (!this.loadingIndicator) {
            this.loadingIndicator = document.createElement('div');
            this.loadingIndicator.className = 'pdf-loading';
            this.loadingIndicator.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat dokumen PDF...</p>
                </div>
            `;
            this.container.appendChild(this.loadingIndicator);
        }
    }

    hideLoadingIndicator() {
        if (this.loadingIndicator) {
            this.loadingIndicator.remove();
            this.loadingIndicator = null;
        }
    }

    showError() {
        this.iframe.style.display = 'none';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'pdf-error';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle fa-3x"></i>
            <h5 class="text-warning">Gagal memuat PDF</h5>
            <p class="text-muted">Silakan gunakan tombol download untuk melihat dokumen</p>
            <button class="btn btn-outline-primary mt-2" onclick="location.reload()">
                <i class="fas fa-redo me-1"></i>Coba Lagi
            </button>
        `;
        
        this.container.appendChild(errorDiv);
    }

    // Public method to refresh viewer
    refresh() {
        this.createViewer();
    }

    // Public method to change PDF URL
    changePDF(newUrl) {
        this.pdfUrl = newUrl;
        this.iframe.src = newUrl + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH';
    }
}

// Initialize PDF viewers when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Find all PDF containers and initialize them
    const pdfContainers = document.querySelectorAll('.pdf-container');
    
    pdfContainers.forEach(container => {
        const iframe = container.querySelector('iframe');
        if (iframe && iframe.src) {
            const pdfUrl = iframe.src.split('#')[0]; // Remove hash parameters
            const viewer = new PDFViewer(container.id, pdfUrl);
            
            // Store viewer instance for external access
            container.pdfViewer = viewer;
        }
    });
});

// Global functions for backward compatibility
window.toggleFullscreen = function() {
    const container = document.querySelector('.pdf-container');
    if (container && container.pdfViewer) {
        container.pdfViewer.toggleFullscreen();
    }
};

window.refreshPDFViewer = function() {
    const container = document.querySelector('.pdf-container');
    if (container && container.pdfViewer) {
        container.pdfViewer.refresh();
    }
};
