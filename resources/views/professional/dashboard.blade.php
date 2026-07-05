@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-zinc-100 py-4 pr-4 pl-4 lg:pl-0 h-full min-h-0 grow">
        <x-calendar class="flex-1 min-h-0" :events-url="route('api.professionals.schedule', $professional)" />
    </div>
@endsection