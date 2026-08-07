@extends('layouts.dashboard')

@section('title', $ebook->title . ' | Ebook Curhatorium')

@section('bodyClass', 'pt-16 w-full bg-[#F4F4F5]')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($ebook->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Geist:wght@100..900&family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F4F4F5 !important;
        }

        /* Ebook Hero Container (Mobile Stacked, Desktop 50-50 Side-by-Side) */
        .ebook-hero-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 24px !important;
            width: 100% !important;
        }

        .ebook-hero-left {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .ebook-cover-banner {
            width: 100% !important;
            max-width: 576px !important;
            height: 100% !important;
            min-height: 400px !important;
            position: relative !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            background: #FFFFFF !important;
            border: 1px solid #E4E4E7 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 24px !important;
            box-sizing: border-box !important;
        }

        .ebook-hero-right {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            gap: 20px !important;
            height: auto !important;
            min-height: 0 !important;
            width: 100% !important;
        }

        .ebook-title-heading {
            color: #111827 !important;
            font-size: 28px !important;
            font-weight: 600 !important;
            font-family: 'Bricolage Grotesque', sans-serif !important;
            line-height: 36px !important;
            letter-spacing: -0.015em !important;
            margin: 0 !important;
        }

        /* Comments Pagination Wrapper (Centered on Mobile, Space-Between on Desktop) */
        .comments-pagination-wrapper {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            gap: 16px !important;
            width: 100% !important;
            padding-top: 16px !important;
            box-sizing: border-box !important;
        }

        /* Desktop View Specs (>= 768px) */
        @media (min-width: 768px) {
            .ebook-hero-container {
                flex-direction: row !important;
                gap: 36px !important;
                align-items: stretch !important;
                justify-content: space-between !important;
            }
            .ebook-hero-left {
                flex: 1 1 48% !important;
                min-width: 0 !important;
                max-width: 576px !important;
                align-self: stretch !important;
                height: 100% !important;
            }
            .ebook-cover-banner {
                min-height: 576px !important;
                height: 100% !important;
            }
            .ebook-hero-right {
                flex: 1 1 48% !important;
                min-width: 0 !important;
                max-width: 588px !important;
                min-height: 576px !important;
                justify-content: space-between !important;
            }
            .ebook-title-heading {
                font-size: 40px !important;
                line-height: 48px !important;
            }
            .comments-pagination-wrapper {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                text-align: left !important;
            }
        }

        /* Card Ebook Aspect Ratio 3:4 */
        .ebook-card-banner {
            width: 100% !important;
            aspect-ratio: 3 / 4 !important;
            position: relative !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #F4F4F5 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        /* Related Ebooks Grid: 4 cols Desktop, 2 cols Tablet/Mobile */
        .related-ebooks-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
        }

        @media (min-width: 1024px) {
            .related-ebooks-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 24px !important;
            }
        }
    </style>
@endsection

@php
    $coverUrl = null;
    if ($ebook->cover_image) {
        if (str_starts_with($ebook->cover_image, 'http://') || str_starts_with($ebook->cover_image, 'https://')) {
            $coverUrl = $ebook->cover_image;
        } else {
            $coverUrl = \Illuminate\Support\Facades\Storage::url($ebook->cover_image);
        }
    }
@endphp

@section('dashboard-content')
    <!-- Main Max-Width Wrapper (1200px) -->
    <div class="w-full bg-[#F4F4F5] min-h-screen py-6 sm:py-10" style="background-color: #F4F4F5; position: relative;">
        
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-10 sm:gap-14" style="max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 16px; padding-right: 16px; display: flex; flex-direction: column; gap: 40px;">
            
            <!-- Hero Detail Section Partial -->
            @include('ebooks.partials.hero')

            <!-- Reviews & Comments Section Partial -->
            @include('ebooks.partials.reviews')

            <!-- Related Ebooks Grid Section Partial -->
            @include('ebooks.partials.related')

        </div>

    </div>
@endsection
