@extends('layouts.app')

@section('title', 'Pusher Test')

@section('bodyClass', 'bg-gray-50 text-gray-800')

@section('head')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endsection

@section('content')
    <div class="max-w-xl mx-auto p-4">
        <h1 class="text-2xl font-bold text-[#48A6A6] mb-2">PUSHER</h1>
    </div>
@endsection