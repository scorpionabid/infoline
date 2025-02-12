@extends('pages.settings.personal.index')

@section('tab-content')
<div class="row">
   <div class="col-12 mb-4">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Sektorlar</h5>
               <div>
                   <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#sectorAdminModal">
                       <i class="fas fa-user-plus"></i> Sektor admini əlavə et
                   </button>
                   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectorModal">
                       <i class="fas fa-plus"></i> Sektor əlavə et
                   </button>
               </div>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Region</th>
                               <th>Sektor adı</th>
                               <th>Telefon</th>
                               <th>Məktəb sayı</th>
                               <th>Admin</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($sectors as $sector)
                           <tr>
                               <td>{{ $sector->region->name }}</td>
                               <td>{{ $sector->name }}</td>
                               <td>{{ $sector->phone }}</td>
                               <td>{{ $sector->schools_count }}</td>
                               <td>
                                   @if($sector->admin)
                                       {{ $sector->admin->name }}
                                   @else
                                       <span class="badge bg-warning">Admin təyin edilməyib</span>
                                   @endif
                               </td>
                               <<td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editSector({{ $sector->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
        
        <!-- Yeni admin təyinat düyməsi -->
                                        <button class="btn btn-sm btn-outline-warning btn-assign-sector-admin" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#sectorAdminModal"
                                                data-sector-id="{{ $sector->id }}">
                                            <i class="fas fa-user-plus"></i>
                                        </button>

                                        @if($sector->schools_count == 0)
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteSector({{ $sector->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                           </tr>
                           @endforeach
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>
@endsection

@include('pages.settings.personal.modals.sector-admin-modal')

@push('scripts')
<script>
    // Admin təyinatı üçün JavaScript
    $(document).ready(function() {
        $(".btn-assign-sector-admin").on("click", function() {
            const sectorId = $(this).data("sector-id");
            $("#sectorAdminModal form").attr(
                'action', 
                "{{ route('settings.personal.sectors.admin', ':id') }}".replace(':id', sectorId)
            );
        });

        $("#sectorAdminModal form").on("submit", function(e) {
            e.preventDefault();
            const form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $("#sectorAdminModal").modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Uğurlu!',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Xəta!',
                        text: xhr.responseJSON.message
                    });
                }
            });
        });
    });
</script>
@endpush