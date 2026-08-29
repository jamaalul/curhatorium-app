{{-- Shared CSS for MentAI pages (index + show) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">
<style>
    .font-bricolage { font-family: 'Bricolage Grotesque', sans-serif !important; }
    .font-dmsans   { font-family: 'DM Sans', sans-serif !important; }

    /* Hide scrollbars while keeping scroll functionality */
    .mentai-scrollbar::-webkit-scrollbar,
    .custom-scrollbar::-webkit-scrollbar,
    textarea::-webkit-scrollbar,
    *::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
    .mentai-scrollbar, .custom-scrollbar, textarea, * {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    /* ── Page wrapper ── */
    .mentai-page-wrap {
        display: flex; width: 100vw; height: 100vh;
        overflow: hidden; background-color: #F4F4F5;
        font-family: 'DM Sans', sans-serif; color: #1E1E1E;
    }

    /* ── Main stage (right of sidebar) ── */
    .mentai-main-stage {
        flex: 1; height: 100vh; overflow: hidden;
        background-color: #F4F4F5;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 24px 32px; box-sizing: border-box;
    }

    /* ── Floating white card ── */
    .mentai-floating-card {
        width: 100%; height: 100%;
        background-color: #FFFFFF; border: 1px solid #E4E4E7;
        border-radius: 24px;
        display: flex; flex-direction: column; align-items: center;
        box-sizing: border-box; overflow: hidden;
        position: relative;
    }

    /* ── Sidebar ── */
    .mentai-sidebar {
        width: 260px; min-width: 260px; max-width: 260px;
        background-color: #F4F4F5;
        border-right: 1px solid #E4E4E7;
        height: 100vh;
        display: flex; flex-direction: column;
        box-sizing: border-box;
        user-select: none;
        transition: width .25s ease, min-width .25s ease, max-width .25s ease, transform .25s ease;
        z-index: 40;
        overflow: hidden;
    }
    .mentai-sidebar.is-collapsed { width: 58px; min-width: 58px; max-width: 58px; }

    .mentai-sidebar-action-btn {
        width: 30px; height: 30px;
        border-radius: 8px;
        background-color: #FFFFFF;
        border: none;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #09090B;
        transition: all .2s ease;
    }
    .mentai-sidebar-action-btn:hover { color: #00BBA7 !important; }

    /* ── Input box (shared) ── */
    .mentai-input-outer-box {
        background-color: #CCFBF1; border: 1px solid #96F7E4;
        border-radius: 18px; padding: 6px 6px 8px;
        display: flex; flex-direction: column; gap: 6px; box-sizing: border-box;
    }
    .mentai-input-inner-form {
        background-color: #FFFFFF; border-radius: 12px; padding: 12px 14px;
        display: flex; align-items: center; justify-content: space-between; gap: 12px; box-sizing: border-box;
    }
    .mentai-textarea {
        width: 100%; background: transparent !important; border: none !important;
        outline: none !important; box-shadow: none !important; resize: none !important;
        padding: 0 !important; margin: 0 !important;
        font-family: 'DM Sans', sans-serif !important; font-size: 15px !important;
        font-weight: 400 !important; color: #09090B !important; line-height: 24px !important;
        min-height: 24px; max-height: 120px;
    }
    .mentai-textarea::placeholder { color: #A1A1AA !important; font-weight: 400 !important; }
    .mentai-send-btn {
        width: 36px; height: 36px; min-width: 36px; border-radius: 9999px; border: none;
        display: flex; align-items: center; justify-content: center;
        background-color: #00BBA7; color: #FFFFFF; cursor: pointer; transition: all .2s ease; padding: 0;
    }
    .mentai-send-btn:hover:not(:disabled) { background-color: #009e8d; }
    .mentai-send-btn.is-stop { background-color: #00BBA7 !important; color: #FFFFFF !important; cursor: pointer !important; }
    .mentai-send-btn.is-stop:hover { background-color: #009e8d !important; }
    .mentai-send-btn:disabled { background-color: #E4E4E7; color: #A1A1AA; cursor: not-allowed; transform: none; }

    /* ── Thinking and cursor animations ── */
    @keyframes mentaiPulse {
        0%, 100% { transform: scale(0.75); opacity: 0.35; }
        50% { transform: scale(1.15); opacity: 1; }
    }
    .mentai-thinking-dot {
        width: 6px; height: 6px;
        background-color: #00BBA7;
        border-radius: 9999px;
        display: inline-block;
        animation: mentaiPulse 1.2s ease-in-out infinite;
    }
    @keyframes mentaiCursorBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    .mentai-cursor {
        display: inline-block;
        width: 2px;
        height: 15px;
        background-color: #00BBA7;
        vertical-align: -2px;
        margin-left: 3px;
        animation: mentaiCursorBlink 0.8s infinite;
    }

    /* ── MentAI Markdown prose styling ── */
    .mentai-prose {
        font-family: 'DM Sans', sans-serif !important;
        font-size: 15px;
        line-height: 1.8;
        color: #27272A;
    }
    .mentai-prose p { margin: 0 0 14px 0; }
    .mentai-prose p:last-child { margin-bottom: 0; }
    .mentai-prose strong, .mentai-prose b { font-weight: 600; color: #18181B; }
    .mentai-prose ul, .mentai-prose ol { margin: 8px 0 16px 20px; padding: 0; }
    .mentai-prose ul { list-style-type: disc; }
    .mentai-prose ul li::marker { color: #00BBA7; }
    .mentai-prose ol { list-style-type: decimal; }
    .mentai-prose ol li::marker { color: #00BBA7; font-weight: 600; }
    .mentai-prose li { margin-bottom: 8px; line-height: 1.7; }
    .mentai-prose blockquote {
        margin: 14px 0;
        padding: 12px 18px;
        background: #F0FDFA;
        border-left: 3.5px solid #00BBA7;
        border-radius: 0 12px 12px 0;
        color: #0F766E;
        font-style: italic;
        font-size: 14.5px;
        line-height: 1.7;
    }
    .mentai-prose blockquote p {
        margin: 0 !important;
        color: #0F766E !important;
        font-style: italic;
    }
    .mentai-prose code {
        background-color: #F4F4F5; color: #0F766E;
        padding: 2px 6px; border-radius: 5px; font-size: 13.5px; font-family: monospace;
    }
    .mentai-prose pre {
        background-color: #18181B; color: #F4F4F5;
        border-radius: 12px; padding: 14px 16px; margin: 14px 0; overflow-x: auto;
    }
    .mentai-prose pre code { background-color: transparent; color: inherit; padding: 0; }
    .mentai-prose h1, .mentai-prose h2, .mentai-prose h3 {
        font-family: 'Bricolage Grotesque', sans-serif !important;
        font-weight: 700; color: #18181B; margin: 18px 0 10px;
    }

    /* ── Action bar below AI response ── */
    .mentai-msg-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        opacity: 0.85;
        transition: opacity .2s ease;
    }
    .mentai-action-icon-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 6px;
        background: transparent;
        border: none;
        color: #71717A;
        font-size: 12px;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: all .2s ease;
    }
    .mentai-action-icon-btn:hover {
        background: #F4F4F5;
        color: #18181B;
    }
    .mentai-action-icon-btn.is-copied {
        color: #00BBA7 !important;
        background: #F0FDFA;
    }

    /* ── Search Popup Modal (Figma #1231:2616) ── */
    .mentai-search-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 16px;
        padding-top: max(40px, calc(50vh - 170px));
        background-color: rgba(9, 9, 11, 0.45);
    }
    .mentai-search-modal-card {
        width: 100% !important;
        max-width: 622px !important;
        height: auto !important;
        background-color: #F4F4F5 !important;
        border-radius: 12px !important;
        padding: 16px !important;
        display: flex;
        flex-direction: column;
        gap: 20px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(228, 228, 231, 0.8) !important;
        border: 1px solid #E4E4E7 !important;
        box-sizing: border-box !important;
    }
    .mentai-search-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1px solid #E4E4E7;
        gap: 12px;
    }
    .mentai-search-modal-input-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    .mentai-search-modal-input {
        width: 100%;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #18181B !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        line-height: 28px !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .mentai-search-modal-input::placeholder {
        color: #71717A !important;
        font-weight: 500 !important;
    }
    .mentai-search-modal-close-btn {
        padding: 4px;
        border-radius: 6px;
        background: transparent;
        border: none;
        color: #71717A;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s ease;
    }
    .mentai-search-modal-close-btn:hover {
        color: #18181B;
        background-color: rgba(228, 228, 231, 0.7);
    }
    .mentai-search-modal-caption {
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        line-height: 16px;
        font-weight: 400;
        color: #71717A;
    }
    .mentai-search-modal-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: all .15s ease;
        box-sizing: border-box;
        width: 100%;
        background-color: transparent;
    }
    .mentai-search-modal-item:hover,
    .mentai-search-modal-item.is-selected {
        background-color: #FFFFFF !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .mentai-search-modal-item-icon {
        width: 24px;
        height: 24px;
        min-width: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #71717A;
        transition: color .15s ease;
    }
    .mentai-search-modal-item:hover .mentai-search-modal-item-icon,
    .mentai-search-modal-item.is-selected .mentai-search-modal-item-icon {
        color: #00BBA7 !important;
    }
    .mentai-search-modal-item-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        line-height: 28px;
        font-weight: 500;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        transition: color .15s ease;
    }
    .mentai-search-modal-item:hover .mentai-search-modal-item-title,
    .mentai-search-modal-item.is-selected .mentai-search-modal-item-title {
        color: #09090B !important;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .mentai-page-wrap { background-color: #FFFFFF; }
        .mentai-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 60;
            background-color: #FFFFFF !important;
            border-right: 1px solid #E4E4E7;
        }
        .mentai-sidebar .mentai-sidebar-action-btn {
            background-color: #F4F4F5 !important;
        }
        .mentai-sidebar a.group:hover,
        .mentai-sidebar a.group.bg-white {
            background-color: #F4F4F5 !important;
        }
        .mentai-main-stage { padding: 0; background-color: #FFFFFF; height: 100vh; height: 100dvh; }
        .mentai-floating-card { border: none; border-radius: 0; max-height: 100vh; height: 100vh; height: 100dvh; }
        .mentai-card-topbar { border-radius: 0; border-bottom: 1px solid #E4E4E7; padding: 12px 16px; }

        /* ── Fullscreen Search Modal on Mobile (White Background 100vw & 100vh) ── */
        .mentai-search-modal-backdrop {
            padding: 0 !important;
            padding-top: 0 !important;
            align-items: stretch !important;
            justify-content: stretch !important;
            background-color: #FFFFFF !important;
            width: 100vw !important;
            height: 100vh !important;
            height: 100dvh !important;
        }
        .mentai-search-modal-card {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100% !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            border-radius: 0 !important;
            border: none !important;
            box-shadow: none !important;
            padding: 16px 20px 24px !important;
            box-sizing: border-box !important;
            background-color: #FFFFFF !important;
            overflow-y: auto !important;
        }
        .mentai-search-modal-item:hover,
        .mentai-search-modal-item.is-selected {
            background-color: #F4F4F5 !important;
        }
    }
</style>
