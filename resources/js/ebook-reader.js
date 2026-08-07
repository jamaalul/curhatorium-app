export default function ebookReader() {
    // Keep PDF.js objects outside Alpine's reactive system.
    // Alpine wraps data in Proxies, which breaks PDF.js private class fields.
    let pdfDoc = null;
    let activeRenderTask = null;

    return {
        currentPage: 1,
        numPages: 0,
        scale: 1.25,
        loading: true,
        pdfUrl: null,
        progressUrl: null,
        refreshUrlEndpoint: null,
        saveTimeout: null,
        refreshInterval: null,
        error: null,
        pageInput: 1,

        init() {
            this.pdfUrl = this.$el.dataset.pdfUrl;
            this.currentPage = parseInt(this.$el.dataset.startPage) || 1;
            this.pageInput = this.currentPage;
            this.progressUrl = this.$el.dataset.progressUrl;
            this.refreshUrlEndpoint = this.$el.dataset.refreshUrl;

            if (typeof window.pdfjsLib === 'undefined') {
                this.error = 'Pustaka PDF.js belum tersedia. Silakan muat ulang halaman.';
                this.loading = false;
                return;
            }

            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            this.loadDocument();

            if (this.refreshUrlEndpoint) {
                this.refreshInterval = setInterval(() => {
                    this.refreshUrl();
                }, 12 * 60 * 1000);
            }

            window.addEventListener('keydown', (e) => {
                // Prevent browser download/print shortcuts
                if ((e.ctrlKey || e.metaKey) && ['s', 'p', 'u'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
            });

            window.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.saveProgressBeacon();
                }
            });
        },

        async loadDocument() {
            this.loading = true;
            this.error = null;

            if (!this.pdfUrl) {
                this.error = 'URL dokumen PDF tidak ditemukan.';
                this.loading = false;
                return;
            }

            try {
                const loadingTask = window.pdfjsLib.getDocument({
                    url: this.pdfUrl,
                    cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                    cMapPacked: true,
                });

                pdfDoc = await loadingTask.promise;
                this.numPages = pdfDoc.numPages;
                this.loading = false;

                if (this.currentPage > this.numPages) {
                    this.currentPage = this.numPages;
                }
                if (this.currentPage < 1) {
                    this.currentPage = 1;
                }
                this.pageInput = this.currentPage;

                this.$nextTick(() => {
                    this.renderPage(this.currentPage);
                });
            } catch (err) {
                console.error('Error loading PDF:', err);
                this.error = 'Gagal memuat dokumen PDF. Pastikan file valid dan server dapat diakses.';
                this.loading = false;
            }
        },

        async renderPage(num) {
            if (!pdfDoc) return;

            // Cancel any active render task before starting a new one
            if (activeRenderTask) {
                try {
                    activeRenderTask.cancel();
                } catch (e) {
                    // Ignore cancel errors
                }
                activeRenderTask = null;
            }

            this.currentPage = num;
            this.pageInput = num;

            try {
                const page = await pdfDoc.getPage(num);
                const viewport = page.getViewport({ scale: this.scale });

                const canvas = this.$refs.canvas;
                if (!canvas) return;

                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                };

                activeRenderTask = page.render(renderContext);
                await activeRenderTask.promise;
                activeRenderTask = null;

                this.debounceSaveProgress();
            } catch (err) {
                if (err && err.name !== 'RenderingCancelledException') {
                    console.error('Error rendering page:', err);
                }
            }
        },

        nextPage() {
            if (this.currentPage >= this.numPages || this.loading) return;
            this.renderPage(this.currentPage + 1);
        },

        prevPage() {
            if (this.currentPage <= 1 || this.loading) return;
            this.renderPage(this.currentPage - 1);
        },

        goToPage() {
            let page = parseInt(this.pageInput);
            if (isNaN(page)) page = this.currentPage;
            if (page < 1) page = 1;
            if (page > this.numPages) page = this.numPages;
            this.renderPage(page);
        },

        zoomIn() {
            if (this.scale >= 3 || this.loading) return;
            this.scale = Math.min(3, Math.round((this.scale + 0.25) * 100) / 100);
            this.renderPage(this.currentPage);
        },

        zoomOut() {
            if (this.scale <= 0.5 || this.loading) return;
            this.scale = Math.max(0.5, Math.round((this.scale - 0.25) * 100) / 100);
            this.renderPage(this.currentPage);
        },

        updateZoom() {
            this.scale = Math.min(3, Math.max(0.5, this.scale));
            this.renderPage(this.currentPage);
        },

        fitWidth() {
            if (!pdfDoc || !this.$refs.container) return;
            pdfDoc.getPage(this.currentPage).then((page) => {
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                const containerWidth = this.$refs.container.clientWidth - 64; // accounting for padding
                if (containerWidth > 0 && unscaledViewport.width > 0) {
                    this.scale = Math.min(2.5, Math.max(0.6, Math.round((containerWidth / unscaledViewport.width) * 100) / 100));
                    this.renderPage(this.currentPage);
                }
            });
        },

        async refreshUrl() {
            try {
                if (!this.refreshUrlEndpoint) return;

                const response = await fetch(this.refreshUrlEndpoint);
                const data = await response.json();
                if (data.url) {
                    this.pdfUrl = data.url;
                }
            } catch (err) {
                console.error('Failed to refresh URL', err);
            }
        },

        debounceSaveProgress() {
            if (!this.progressUrl) return;

            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }

            this.saveTimeout = setTimeout(() => {
                this.saveProgressFetch();
            }, 800);
        },

        saveProgressFetch() {
            if (!this.progressUrl) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) return;

            fetch(this.progressUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
                body: JSON.stringify({ page: this.currentPage }),
            }).catch(console.error);
        },

        saveProgressBeacon() {
            if (!this.progressUrl) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) return;

            fetch(this.progressUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
                body: JSON.stringify({ page: this.currentPage }),
                keepalive: true,
            }).catch(console.error);
        },
    };
}
