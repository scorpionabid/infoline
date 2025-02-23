{{-- resources/views/pages/dashboard/sector-admin/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- Statistika paneli --}}
    @include('pages.dashboard.sector-admin.components.stats-panel')

    {{-- Kritik vəziyyətlər --}}
    @if($criticalSchools->count() > 0 || count($upcomingDeadlines) > 0)
        <div class="row mb-4">
            <div class="col-12">
                @include('pages.dashboard.sector-admin.components.alerts')
            </div>
        </div>
    @endif

    {{-- Məktəblər cədvəli --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Məktəblər</h5>
                    <div class="d-flex gap-2">
                        @include('pages.dashboard.sector-admin.partials.school-filters')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                            <i class="fas fa-plus"></i> Yeni məktəb
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('pages.dashboard.sector-admin.components.schools-table')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modallar --}}
@include('pages.dashboard.sector-admin.modals.add-school')
@include('pages.dashboard.sector-admin.modals.assign-admin')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sector-admin/dashboard.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/sector-admin/dashboard.js') }}"></script>
@endsection