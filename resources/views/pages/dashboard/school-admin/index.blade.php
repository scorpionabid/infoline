{{-- resources/views/pages/dashboard/school-admin/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- Statistika paneli --}}
    @include('pages.dashboard.school-admin.components.stats-panel')

    {{-- Xəbərdarlıqlar --}}
    @include('pages.dashboard.school-admin.components.notifications')

    {{-- Əsas content --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Məlumat Daxiletmə</h5>
                    
                    {{-- Filter və axtarış --}}
                    @include('pages.dashboard.school-admin.partials.filters')
                </div>

                <div class="card-body">
                    {{-- Kateqoriya tabları --}}
                    @include('pages.dashboard.school-admin.components.category-tabs')

                    {{-- Məlumat forması --}}
                    @include('pages.dashboard.school-admin.components.data-entry-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/school-admin/data-entry.js') }}"></script>
@endpush