@extends('layouts.app')

@section('content')
    @include('components.navbar')
    @yield('dashboard-content')
    @include('components.footer')
@endsection