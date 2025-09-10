<head>
  <style>
    .agenda-section {
        max-width: 1200px;
        margin: 60px auto;
        padding: 0 35px;
    }

    .agenda-section h2 {
        font-size: 2em;
        font-weight: bold;
        color: #000;
        margin-bottom: 30px;
        text-align: center;
    }

    .agenda-card {
        display: flex;
        align-items: center;
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 10px;
        margin-bottom: 20px;
        gap: 20px;
        transition: transform 0.2s ease;
    }

    .agenda-card:hover {
        transform: translateY(-8px) scale(1.02);
        z-index: 2;
    }

    .agenda-card .icon-box {
        min-width: 90px;
        height: 90px;
        border-radius: 12px;
        flex-shrink: 0;
    }

    .agenda-card.teal .icon-box {
        background-color: #93cdd3;
    }

    .agenda-card.yellow .icon-box {
        background-color: #fcd53f;
    }

    .agenda-text {
        flex: 1;
    }

    .agenda-text small {
        font-size: 0.9em;
        color: #777;
        display: block;
        margin-bottom: 5px;
    }

    .agenda-text strong {
        font-size: 1em;
        display: block;
        color: #000;
        margin-bottom: 6px;
    }

    .agenda-text span {
        font-size: 0.85em;
        color: #777;
    }

    /* Articles Grid */
    .articles-grid {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }


    .article-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        align-items: stretch;
        gap: 12px;
        padding: 10px;
        cursor: pointer;
    }

    .article-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.12); }

    .article-thumb {
        flex: 0 0 160px;
        width: 160px;
        height: 120px; /* 4:3 */
        border-radius: 10px;
        overflow: hidden;
        background: #f3f4f6;
    }

    .article-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .article-content { padding: 2px 4px; flex: 1; display: flex; flex-direction: column; justify-content: center; }

    .article-title {
        font-size: 1.25rem;
        color: #0f172a; /* slate-900 */
        margin: 0 0 6px;
        line-height: 1.35;
        font-weight: 700;
    }

    .article-meta { display: flex; align-items: center; gap: 10px; font-size: 0.84rem; color: #6b7280; }

    @media (max-width: 640px) {
        .article-thumb { flex-basis: 120px; width: 120px; height: 90px; }
    }

    .category-chip {
        background: #e8f6f6; /* soft teal */
        color: #256e6e; /* teal text */
        border: 1px solid #c7e7e8; /* light teal border */
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
    }

    .article-meta .dot {
        color: #9ca3af; /* gray-400 */
    }

    .pagination {
        margin-top: 24px;
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .pagination button {
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb; /* gray-200 */
        background: #fff;
        color: #374151; /* gray-700 */
        cursor: pointer;
    }

    .pagination button:hover {
        background: #f9fafb; /* gray-50 */
    }

    .pagination .active {
        background: #8ecbcf;
        color: #fff;
        border-color: #8ecbcf;
    }

    @media (max-width: 768px) {
        .agenda-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .agenda-card .icon-box {
            width: 100%;
            height: 60px;
        }

        .agenda-section h2 {
            font-size: 1.3em;
        }
    }

    @media (max-width: 640px) {
        .agenda-section {
            padding: 0 16px;
        }
    }
  </style>
</head>
<body>
  <section class="agenda-section">
    <h2>Artikel</h2>
      <div class="articles-grid"></div>
      <div class="pagination"></div>
  </section>
  <script>
    // Agendas Section (safe)
    (() => {
        const agendaSection = document.querySelector(".agenda-section");
        if (!agendaSection) return;
        fetch("/agenda/pending")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                let i = 1;
                data.forEach((item) => {
                    const agendaCard = document.createElement("div");
                    agendaCard.className = "agenda-card";
                    agendaCard.classList.add(i % 2 === 0 ? "teal" : "yellow");
                    const iconBox = document.createElement("div");
                    iconBox.className = "icon-box";
                    const agendaText = document.createElement("div");
                    agendaText.className = "agenda-text";
                    const small = document.createElement("small");
                    small.textContent = item.type;
                    const strong = document.createElement("strong");
                    strong.textContent = item.title;
                    const br = document.createElement("br");
                    const span = document.createElement("span");
                    span.textContent = item.description;
                    const dateTime = document.createElement("span");
                    dateTime.textContent = item.event_date + " " + item.event_time;

                    agendaText.appendChild(small);
                    agendaText.appendChild(strong);
                    agendaText.appendChild(span);
                    agendaText.appendChild(br);
                    agendaText.appendChild(dateTime);
                    agendaCard.appendChild(iconBox);
                    agendaCard.appendChild(agendaText);

                    agendaSection.appendChild(agendaCard);
                    i += 1;
                });
            })
            .catch((error) => {
                console.error("Fetch error:", error);
            });
    })();

    // Articles page: no-refresh pagination moved from articles.js
    (() => {
        const grid = document.querySelector('.articles-grid');
        const pagination = document.querySelector('.pagination');
        const heading = document.querySelector('.agenda-section h2');
        if (!grid || !pagination || !heading) return;

        function escapeHtml(text) {
            const map = { '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' };
            return String(text || '').replace(/[&<>"']/g, m => map[m]);
        }

        function renderArticles(articles) {
            grid.innerHTML = articles.map(a => `
                <article class="article-card" onclick="window.location.href='/articles/${encodeURIComponent(a.slug)}'">
                  <div class="article-thumb">
                    <img class="article-thumb-img" src="${encodeURI('/storage/' + (a.image || ''))}" alt="${escapeHtml(a.title)}" />
                  </div>
                  <div class="article-content">
                    <h3 class="article-title">${escapeHtml(a.title)}</h3>
                    <div class="article-meta">
                      <span class="category-chip">${escapeHtml(a.category || 'Umum')}</span>
                      <span class="date">${escapeHtml(a.created_at_formatted || '')}</span>
                    </div>
                  </div>
                </article>
            `).join('');
        }

        function renderPagination(meta) {
            const buttons = [];
            const { current_page, last_page } = meta;
            if (current_page > 1) {
                buttons.push(`<button data-page="${current_page - 1}" class="page-prev">Prev</button>`);
            }
            for (let p = 1; p <= last_page; p++) {
                buttons.push(`<button data-page="${p}" class="page-num ${p === current_page ? 'active' : ''}">${p}</button>`);
            }
            if (current_page < last_page) {
                buttons.push(`<button data-page="${current_page + 1}" class="page-next">Next</button>`);
            }
            pagination.innerHTML = buttons.join('');
        }

        async function fetchPage(page = 1) {
            const url = new URL(window.location.origin + '/api/articles');
            url.searchParams.set('page', String(page));
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed to load articles');
            return res.json();
        }

        pagination.addEventListener('click', (e) => {
            const target = e.target;
            if (!(target instanceof Element)) return;
            const page = target.getAttribute('data-page');
            if (!page) return;
            e.preventDefault();
            navigate(Number(page));
        });

        async function navigate(page) {
            try {
                const { data, meta } = await fetchPage(page);
                renderArticles(data);
                renderPagination(meta);
                history.replaceState(null, '', `?page=${meta.current_page}`);
            } catch (err) {
                console.error(err);
            }
        }

        const params = new URLSearchParams(window.location.search);
        const startPage = Number(params.get('page') || '1');
        navigate(startPage);
    })();
  </script>
</body>