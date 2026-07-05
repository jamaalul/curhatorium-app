@extends('layouts.app')

@section('bodyClass', 'w-screen h-screen bg-red-400 flex flex-row')

@section('content')
    @include('professional.components.sidebar')
    @yield('dashboard-content')
@endsection