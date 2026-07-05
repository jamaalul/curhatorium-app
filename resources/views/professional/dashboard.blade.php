@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-zinc-100 p-4 h-full grow min-h-0">
        <x-calendar class="flex-1 min-h-0" :events-url="route('api.professionals.schedule', $professional)" />
    </div>
@endsection