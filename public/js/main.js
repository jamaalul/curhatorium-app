// Quote Section (safe)
(() => {
    const quoteEl = document.querySelector(".quote-text");
    if (!quoteEl) return;
    fetch("/quote/today")
        .then((response) => {
            if (!response.ok) {
                throw new Error("Failed to fetch quote");
            }
            return response.json();
        })
        .then((data) => {
            quoteEl.textContent = `"${data.quote}"`;
        })
        .catch((error) => {
            quoteEl.textContent = "Failed to load quote.";
            console.error("Error fetching quote:", error);
        });
})();

// Features Section

(() => {
    const mentalTest = document.getElementById("to-mental-test");
    const shareTalk = document.getElementById("to-share-talk");
    const chatbot = document.getElementById("to-chatbot");
    const missions = document.getElementById("to-missions");
    const sgd = document.getElementById("to-sgd");
    const deepCards = document.getElementById("to-deep-cards");

    mentalTest && mentalTest.addEventListener("click", function () {
        window.location.href = "/mental-health-test";
    });
    shareTalk && shareTalk.addEventListener("click", function () {
        window.location.href = "/share-and-talk";
    });
    chatbot && chatbot.addEventListener("click", function () {
        window.location.href = "/mental-support-chatbot";
    });
    missions && missions.addEventListener("click", function () {
        window.location.href = "/missions-of-the-day";
    });
    sgd && sgd.addEventListener("click", function () {
        window.location.href = "/support-group-discussion";
    });
    deepCards && deepCards.addEventListener("click", function () {
        window.location.href = "/deep-cards";
    });
})();

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
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
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
