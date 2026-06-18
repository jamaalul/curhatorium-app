@extends('layouts.app')

@section('title', 'Artikel')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/main/agenda.css') }}?v={{ filemtime(public_path('css/main/agenda.css')) }}" />
@endsection

@section('content')
  @include('components.navbar')
  <section class="agenda-section">
    <h2>Artikel</h2>
    <div class="articles-grid"></div>
    <div class="pagination"></div>
  </section>
  @include('components.footer')
@endsection

@section('scripts')
    <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}" defer></script>
@endsection
