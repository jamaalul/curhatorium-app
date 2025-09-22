<section class="w-full h-fit px-4 py-12 md:px-8 bg-stone-200">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-800 mb-8">
            Artikel Terbaru
        </h2>
        <div id="articles-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Article cards will be injected here by JavaScript --}}
        </div>
        <div id="articles-pagination" class="flex justify-center items-center mt-8 space-x-2">
            {{-- Pagination controls will be injected here --}}
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const articlesGrid = document.getElementById('articles-grid');
    const articlesPagination = document.getElementById('articles-pagination');

    if (!articlesGrid || !articlesPagination) {
        console.error("Article grid or pagination element not found.");
        return;
    }

    function escapeHtml(text) {
        const map = {
            '&': '&',
            '<': '<',
            '>': '>',
            '"': '"',
            "'": '&#039;'
        };
        return String(text || '').replace(/[&<>"']/g, m => map[m]);
    }

    function renderArticles(articles) {
        if (!articles || articles.length === 0) {
            articlesGrid.innerHTML = '<p class="text-center text-slate-500 col-span-full">Tidak ada artikel untuk ditampilkan.</p>';
            return;
        }
        articlesGrid.innerHTML = articles.map(article => `
            <div class="bg-white rounded-md shadow-md overflow-hidden transform hover:-translate-y-1 transition-transform duration-300 cursor-pointer" onclick="window.location.href='/articles/${encodeURIComponent(article.slug)}'">
                <img src="${encodeURI('/storage/' + (article.image || 'images/default-article.jpg'))}" alt="${escapeHtml(article.title)}" class="w-full h-40 object-cover">
                <div class="p-4">
                    <span class="inline-block bg-teal-100 text-teal-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mb-2">${escapeHtml(article.category || 'Umum')}</span>
                    <h3 class="text-lg font-bold text-slate-800 mb-2 truncate">${escapeHtml(article.title)}</h3>
                    <p class="text-sm text-slate-600">${escapeHtml(article.created_at_formatted || '')}</p>
                </div>
            </div>
        `).join('');
    }

    function renderPagination(meta) {
        const { current_page, last_page } = meta;
        let paginationHtml = '';

        if (last_page > 1) {
            // Previous button
            paginationHtml += `<button data-page="${current_page - 1}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white rounded-md shadow-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${current_page === 1 ? 'disabled' : ''}>Prev</button>`;

            // Page numbers
            for (let p = 1; p <= last_page; p++) {
                if (p === current_page) {
                    paginationHtml += `<button class="px-4 py-2 text-sm font-medium text-white bg-[#48A6A6] rounded-md shadow-sm">${p}</button>`;
                } else {
                    paginationHtml += `<button data-page="${p}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white rounded-md shadow-sm hover:bg-slate-50">${p}</button>`;
                }
            }

            // Next button
            paginationHtml += `<button data-page="${current_page + 1}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white rounded-md shadow-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${current_page === last_page ? 'disabled' : ''}>Next</button>`;
        }

        articlesPagination.innerHTML = paginationHtml;
    }

    async function fetchArticles(page = 1) {
        try {
            const response = await fetch(`/api/articles?page=${page}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            renderArticles(result.data);
            renderPagination(result.meta);
        } catch (error) {
            console.error("Failed to fetch articles:", error);
            articlesGrid.innerHTML = '<p class="text-center text-red-500 col-span-full">Gagal memuat artikel. Silakan coba lagi nanti.</p>';
        }
    }

    articlesPagination.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON' && e.target.dataset.page) {
            const page = parseInt(e.target.dataset.page, 10);
            if (!isNaN(page)) {
                fetchArticles(page);
            }
        }
    });

    // Initial fetch
    fetchArticles(1);
});
</script>