@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-white lg:bg-zinc-100 p-0 lg:py-4 lg:pr-4 h-full min-h-0 grow">
        <x-calendar class="flex-1 min-h-0" :events-url="route('api.professionals.schedule', $professional)" />
    </div>
@endsection