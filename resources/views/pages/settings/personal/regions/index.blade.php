@extends('layouts.app')

@section('title', 'Regionlar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Regionlar</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" id="createRegionBtn">
                            <i class="fas fa-plus"></i> Yeni Region
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="regions-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Region</th>
                                <th>Kod</th>
                                <th>Sektor Sayı</th>
                                <th>Məktəb Sayı</th>
                                <th>Admin</th>
                                <th>Əməliyyatlar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.settings.personal.regions.create')

<!-- Region Admin Modal -->
<div class="modal fade" id="regionAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Region Admini Təyin Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="regionAdminForm">
                    @csrf
                    <div id="adminFormErrors"></div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="admin_phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="send_credentials" name="send_credentials" checked>
                            <label class="form-check-label" for="send_credentials">
                                Giriş məlumatlarını email ilə göndər
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                <button type="submit" class="btn btn-primary" form="regionAdminForm">Təyin Et</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('js/settings/region.js') }}"></script>

<script>
$(function() {
    const table = $('#regions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('settings.personal.regions.data') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'code', name: 'code'},
            {data: 'sectors_count', name: 'sectors_count'},
            {data: 'schools_count', name: 'schools_count'},
            {data: 'admin', name: 'admin'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        language: {
            url: "{{ asset('assets/libs/datatables/az.json') }}"
        }
    });

    // Region yaradıldıqdan və ya yeniləndikdən sonra cədvəli yeniləyirik
    document.addEventListener('region:saved', function() {
        table.ajax.reload();
    });
});
</script>
@endpush