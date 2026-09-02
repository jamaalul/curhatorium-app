{{-- Shared CSS for MentAI Chat (Animations & Markdown Prose) --}}
<style>
    .font-bricolage { font-family: 'Bricolage Grotesque', sans-serif !important; }
    .font-dm, .font-dmsans { font-family: 'DM Sans', sans-serif !important; }

    /* ── Hide scrollbars while keeping scroll functionality ── */
    .scrollbar-none::-webkit-scrollbar,
    .mentai-scrollbar::-webkit-scrollbar,
    .custom-scrollbar::-webkit-scrollbar,
    textarea::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
    .scrollbar-none, .mentai-scrollbar, .custom-scrollbar, textarea {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    /* ── Eased smooth feather gradient for input top edge ── */
    .mentai-smooth-top-fade {
        position: absolute;
        top: -36px;
        left: 0;
        right: 0;
        height: 36px;
        background: linear-gradient(
            to top,
            rgba(255, 255, 255, 1) 0%,
            rgba(255, 255, 255, 0.92) 18%,
            rgba(255, 255, 255, 0.78) 35%,
            rgba(255, 255, 255, 0.58) 52%,
            rgba(255, 255, 255, 0.36) 68%,
            rgba(255, 255, 255, 0.18) 82%,
            rgba(255, 255, 255, 0.05) 92%,
            rgba(255, 255, 255, 0) 100%
        );
        pointer-events: none;
    }

    /* ── Thinking dot pulse animation ── */
    @keyframes mentaiPulse {
        0%, 100% { transform: scale(0.75); opacity: 0.35; }
        50% { transform: scale(1.15); opacity: 1; }
    }
    .mentai-thinking-dot {
        width: 6px;
        height: 6px;
        background-color: #00BBA7;
        border-radius: 9999px;
        display: inline-block;
        animation: mentaiPulse 1.2s ease-in-out infinite;
    }

    /* ── Live typing blinking cursor ── */
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

    /* ── MentAI Markdown Prose Rendering ── */
    .mentai-prose {
        font-family: 'DM Sans', sans-serif !important;
        font-size: 15px;
        line-height: 1.7;
        color: #27272A;
        word-break: break-word;
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
        background-color: #F4F4F5;
        color: #0F766E;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 13.5px;
        font-family: monospace;
    }
    .mentai-prose pre {
        background-color: #18181B;
        color: #F4F4F5;
        border-radius: 12px;
        padding: 14px 16px;
        margin: 14px 0;
        overflow-x: auto;
    }
    .mentai-prose pre code { background-color: transparent; color: inherit; padding: 0; }
    .mentai-prose h1, .mentai-prose h2, .mentai-prose h3, .mentai-prose h4, .mentai-prose h5, .mentai-prose h6 {
        font-family: 'Bricolage Grotesque', sans-serif !important;
        font-weight: 700;
        color: #18181B;
        margin: 18px 0 10px;
    }
</style>
