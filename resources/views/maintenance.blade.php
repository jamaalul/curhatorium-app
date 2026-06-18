@extends('layouts.app')

@section('title', 'Maintenance')

@section('head')
    <style>
        body {
            font-family: sans-serif;
            background-color: #f3f4f6;
            color: #4b5563;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 2.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.125rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1>Maintenance</h1>
        <p>{{ config('maintenance.message') }}</p>
        @if(config('maintenance.estimated_downtime'))
            <p>Estimated downtime: {{ config('maintenance.estimated_downtime') }}</p>
        @endif
    </div>
@endsection