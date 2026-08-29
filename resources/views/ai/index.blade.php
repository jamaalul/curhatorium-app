@extends('layouts.app')

@section('title', 'MentAI - Mental Support Chatbot | Curhatorium')

@section('head')
    @include('ai.partials._styles')
    <style>
        /* ── Index-only: welcome screen content ── */
        .mentai-floating-card-index {
            padding: 36px 36px 28px;
            overflow-y: auto;
            justify-content: center;
        }
        .mentai-content-wrapper {
            display: flex; width: 700.242px; max-width: 100%;
            flex-direction: column; align-items: center;
            gap: 128px; margin-top: auto; margin-bottom: 0;
        }
        .mentai-middle-bottom-group {
            display: flex; flex-direction: column;
            align-items: flex-start; gap: 80px;
            align-self: stretch; width: 100%;
        }
        .mentai-top-header {
            display: flex; flex-direction: column;
            align-items: center; text-align: center;
            width: 100%; max-width: 520px;
        }
        .mentai-mascot-circle {
            width: 76px; height: 76px; border-radius: 9999px;
            background-color: #FAFAFA; border: 1px solid #F4F4F5;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .mentai-mascot-circle img, .mentai-mascot-circle svg { width: 38px; height: 38px; object-fit: contain; }
        .mentai-title {
            font-family: 'Bricolage Grotesque', sans-serif !important;
            font-weight: 700; font-size: 32px; line-height: 38px;
            letter-spacing: -0.02em; color: #1F2937; margin: 18px 0 8px 0;
        }
        .mentai-subtitle { font-size: 14.5px; line-height: 24px; color: #71717A; margin: 0; max-width: 460px; }
        .mentai-prompts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; width: 100%; box-sizing: border-box; }
        .mentai-card-teal {
            background-color: #00BBA7; border-radius: 16px; padding: 6px 6px 10px;
            display: flex; flex-direction: column; justify-content: space-between;
            gap: 8px; cursor: pointer; transition: background-color .2s ease;
            text-decoration: none; box-sizing: border-box;
        }
        .mentai-card-teal:hover { background-color: #009689; }
        .mentai-card-neutral {
            background-color: #F4F4F5; border: 1px solid #E4E4E7;
            border-radius: 16px; padding: 6px 6px 10px;
            display: flex; flex-direction: column; justify-content: space-between;
            gap: 8px; cursor: pointer; transition: background-color .2s ease, border-color .2s ease;
            text-decoration: none; box-sizing: border-box;
        }
        .mentai-card-neutral:hover { background-color: #E4E4E7; border-color: #D4D4D8; }
        .mentai-card-inner-white {
            background-color: #FFFFFF; border-radius: 12px; padding: 12px 14px;
            min-height: 94px; display: flex; flex-direction: column; justify-content: flex-start; box-sizing: border-box;
        }
        .mentai-input-wrap { width: 100%; max-width: 700px; display: flex; flex-direction: column; gap: 10px; box-sizing: border-box; }

        @media (max-height: 900px) { .mentai-content-wrapper { gap: 96px; } .mentai-middle-bottom-group { gap: 64px; } }
        @media (max-height: 750px) {
            .mentai-content-wrapper { gap: 64px; } .mentai-middle-bottom-group { gap: 40px; }
            .mentai-floating-card-index { padding: 28px 28px 20px; }
            .mentai-mascot-circle { width: 64px; height: 64px; }
            .mentai-mascot-circle img, .mentai-mascot-circle svg { width: 32px; height: 32px; }
            .mentai-title { font-size: 28px; line-height: 34px; margin: 14px 0 6px 0; }
        }
        @media (max-height: 620px) {
            .mentai-content-wrapper { gap: 32px; } .mentai-middle-bottom-group { gap: 20px; }
            .mentai-floating-card-index { padding: 16px; }
        }
        @media (max-width: 768px) {
            .mentai-page-wrap {
                background-color: #FFFFFF !important;
                height: 100vh;
                height: 100dvh;
            }
            .mentai-main-stage {
                padding: 0 !important;
                background-color: #FFFFFF !important;
                height: 100vh;
                height: 100dvh;
                display: flex;
                flex-direction: column;
            }
            .mentai-floating-card-index {
                border: none !important;
                border-radius: 0 !important;
                padding: 16px 16px 20px !important;
                background-color: #FFFFFF !important;
                height: 100% !important;
                max-height: 100% !important;
                box-shadow: none !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                box-sizing: border-box !important;
                overflow-y: auto !important;
            }
            .mentai-mobile-top-bar {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                padding: 0;
                margin-bottom: 0;
            }
            .mentai-content-wrapper {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 120px !important;
                margin-top: auto !important;
                margin-bottom: 0 !important;
                padding: 0 !important;
            }
            .mentai-top-header {
                padding-top: 0;
                max-width: 100%;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .mentai-mascot-circle {
                width: 80px !important;
                height: 80px !important;
                background-color: #FAFAFA !important;
                border: 1px solid #F4F4F5 !important;
                border-radius: 9999px !important;
            }
            .mentai-mascot-circle img, .mentai-mascot-circle svg {
                width: 40px !important;
                height: 40px !important;
            }
            .mentai-title {
                font-family: 'Bricolage Grotesque', sans-serif !important;
                font-size: 24px !important;
                line-height: 32px !important;
                font-weight: 600 !important;
                letter-spacing: -0.01em !important;
                color: #1F2937 !important;
                margin: 16px 0 8px 0 !important;
            }
            .mentai-subtitle {
                font-family: 'DM Sans', sans-serif !important;
                font-size: 14px !important;
                line-height: 24px !important;
                color: #71717A !important;
                max-width: 340px !important;
                margin: 0 auto !important;
            }
            .mentai-middle-bottom-group {
                display: flex !important;
                flex-direction: column !important;
                gap: 80px !important;
                width: 100% !important;
            }
            .mentai-prompts-grid {
                display: flex !important;
                flex-direction: row !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                gap: 16px !important;
                width: calc(100% + 32px) !important;
                margin: 0 -16px !important;
                padding: 4px 16px !important;
                box-sizing: border-box !important;
            }
            .mentai-prompts-grid::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            .mentai-card-teal,
            .mentai-card-neutral {
                min-width: 220px !important;
                max-width: 224px !important;
                width: 62vw !important;
                flex-shrink: 0 !important;
                box-sizing: border-box !important;
                padding: 6px 6px 12px !important;
                border-radius: 14px !important;
                box-shadow: 0px 2px 12px 0px rgba(0, 0, 0, 0.05) !important;
                gap: 12px !important;
            }
            .mentai-card-inner-white {
                min-height: 86px !important;
                padding: 12px !important;
                border-radius: 10px !important;
            }
            .mentai-card-inner-white p {
                font-family: 'DM Sans', sans-serif !important;
                font-size: 14px !important;
                line-height: 22px !important;
                color: #09090B !important;
            }
            .mentai-input-wrap {
                width: 100% !important;
            }
        }

        /* ── Mobile Height Breakpoints (aturan bertingkat seperti desktop) ── */
        @media (max-width: 768px) and (max-height: 850px) {
            .mentai-content-wrapper { gap: 92px !important; }
            .mentai-middle-bottom-group { gap: 48px !important; }
        }
        @media (max-width: 768px) and (max-height: 750px) {
            .mentai-content-wrapper { gap: 58px !important; }
            .mentai-middle-bottom-group { gap: 32px !important; }
            .mentai-mascot-circle { width: 68px !important; height: 68px !important; }
            .mentai-mascot-circle img, .mentai-mascot-circle svg { width: 34px !important; height: 34px !important; }
            .mentai-title { font-size: 22px !important; line-height: 28px !important; margin: 12px 0 6px 0 !important; }
            .mentai-subtitle { font-size: 13px !important; line-height: 20px !important; }
        }
        @media (max-width: 768px) and (max-height: 650px) {
            .mentai-content-wrapper { gap: 32px !important; }
            .mentai-middle-bottom-group { gap: 20px !important; }
            .mentai-mascot-circle { width: 56px !important; height: 56px !important; }
            .mentai-mascot-circle img, .mentai-mascot-circle svg { width: 28px !important; height: 28px !important; }
            .mentai-title { font-size: 20px !important; line-height: 26px !important; margin: 8px 0 4px 0 !important; }
        }
    </style>
@endsection

@section('content')
    <script>
        var __mentaiInitialConversations = {{ Js::from($initialConversations) }};
    </script>

    @include('ai.partials._scripts-shared')

    <div class="mentai-page-wrap" x-data="mentaiIndex()" x-init="initChat()">

        @include('ai.partials._sidebar', ['spaNav' => false])

        {{-- ── Main Stage ── --}}
        <main class="mentai-main-stage">
            <div class="mentai-floating-card mentai-floating-card-index mentai-scrollbar">
                
                {{-- Mobile top bar for index (Figma #1231:2040) --}}
                <div class="mentai-mobile-top-bar md:hidden">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="mentai-sidebar-action-btn"
                            style="padding:4px;border-radius:8px;background:transparent;border:none;cursor:pointer;color:#09090B;display:flex;align-items:center;justify-content:center;"
                            title="Buka menu riwayat">
                        <svg style="width:24px;height:24px;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.646 10V6c0-3.333-1.333-4.667-4.667-4.667H5.98C2.646 1.333 1.313 2.667 1.313 6v4c0 3.333 1.333 4.667 4.666 4.667h3.98C13.313 14.667 14.646 13.333 14.646 10z" stroke="#09090B" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.313 1.333v13.334" stroke="#09090B" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m9.98 6.293-1.706 1.707 1.706 1.707" stroke="#09090B" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="mentai-content-wrapper">

                    {{-- Welcome Header (Figma #1231:1989) --}}
                    <div class="mentai-top-header">
                        <div class="mentai-mascot-circle">
                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" />
                        </div>
                        <h1 class="mentai-title">Halo, Aku MentAI</h1>
                        <p class="mentai-subtitle">
                            Teman cerita 24/7 yang siap mendengarkanmu tanpa menghakimi.<br>
                            Ada yang ingin kamu sampaikan hari ini?
                        </p>
                    </div>

                    {{-- Prompt Cards + Input (Figma #1231:1997) --}}
                    <div class="mentai-middle-bottom-group">

                        {{-- 3 Starter Cards (Figma #1257:2324) --}}
                        <div class="mentai-prompts-grid">

                            <div @click="selectStarter('Cerita apa aja, aku di sini buat dengerin 🫰🏼')" class="mentai-card-teal">
                                <div class="mentai-card-inner-white" style="gap:8px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                        <div style="display:flex;align-items:center;gap:5px;">
                                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" style="width:16px;height:16px;object-fit:contain;" />
                                            <span style="font-size:13px;font-weight:600;color:#00BBA7;">MentAI</span>
                                        </div>
                                        <span style="background-color:#00BBA7;color:#FFFFFF;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;">Temen curhat</span>
                                    </div>
                                    <p style="font-size:13.5px;line-height:20px;font-weight:400;color:#1E1E1E;margin:0;">
                                        Cerita apa aja, aku di sini buat dengerin 🫰🏼
                                    </p>
                                </div>
                                <div style="padding:2px 10px 0;">
                                    <span style="color:#FFFFFF;font-size:11.5px;font-weight:500;letter-spacing:.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                            <div @click="selectStarter('Saya merasa sedikit cemas dan butuh teman bicara.')" class="mentai-card-neutral">
                                <div class="mentai-card-inner-white">
                                    <p style="font-size:13.5px;line-height:20px;font-weight:400;color:#1E1E1E;margin:0;">
                                        Saya merasa sedikit cemas dan butuh teman bicara.
                                    </p>
                                </div>
                                <div style="padding:2px 10px 0;">
                                    <span style="color:#A1A1AA;font-size:11.5px;font-weight:500;letter-spacing:.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                            <div @click="selectStarter('Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?')" class="mentai-card-neutral">
                                <div class="mentai-card-inner-white">
                                    <p style="font-size:13.5px;line-height:20px;font-weight:400;color:#1E1E1E;margin:0;">
                                        Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?
                                    </p>
                                </div>
                                <div style="padding:2px 10px 0;">
                                    <span style="color:#A1A1AA;font-size:11.5px;font-weight:500;letter-spacing:.02em;">Prompt disarankan</span>
                                </div>
                            </div>

                        </div>

                        {{-- Input Box --}}
                        <div class="mentai-input-wrap">
                            @include('ai.partials._input-box', ['useMentaiClass' => true])
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
