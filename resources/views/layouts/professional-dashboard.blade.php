@extends('layouts.app')

@section('bodyClass', 'w-screen h-screen bg-zinc-100 flex flex-col lg:flex-row overflow-hidden')

@section('content')
    @include('professional.components.sidebar')
    @yield('dashboard-content')
@endsection