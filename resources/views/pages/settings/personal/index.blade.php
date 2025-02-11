@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('*/regions*') ? 'active' : '' }}" 
               href="{{ route('settings.personal.regions.index') }}">
                <i class="fas fa-globe"></i> Regionlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('*/sectors*') ? 'active' : '' }}" 
               href="{{ route('settings.personal.sectors.index') }}">
                <i class="fas fa-building"></i> Sektorlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('*/schools*') ? 'active' : '' }}" 
               href="{{ route('settings.personal.schools.index') }}">
                <i class="fas fa-school"></i> Məktəblər
            </a>
        </li>
    </ul>

    {{-- Dynamic Content --}}
    <div class="tab-content">
        @yield('tab-content')
    </div>
</div>

@include('pages.settings.personal.modals.region-modal')
@endsection