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
                                                onclick="editSector({{ $sector->id }})"
                                                type="button">
                                            <i class="fas fa-edit"></i>
                                        </button>
        
        <!-- Yeni admin təyinat düyməsi -->
                                        <button class="btn btn-sm btn-outline-warning assign-admin-btn" 
                                            data-sector-id="{{ $sector->id }}"
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
@include('pages.settings.personal.modals.sector-modal')
@push('scripts')
<script>
$(document).ready(function() {
    // Admin təyin etmə düyməsinə click handler
    $(".assign-admin-btn").on("click", function() {
        const sectorId = $(this).data("sector-id");
        const modal = $("#sectorAdminModal");
        const form = modal.find("form");
        
        // Form action və sector_id-ni təyin et
        form.attr('action', form.attr('action').replace(':id', sectorId));
        form.find('#sectorIdInput').val(sectorId);
        
        // Modalı göstər
        modal.modal('show');
    });

    // Form submit handler
    $("#sectorAdminForm").on("submit", function(e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("#sectorAdminModal").modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Uğurlu!',
                    text: response.message || 'Sektor admini uğurla təyin edildi'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Xəta baş verdi';
                Swal.fire({
                    icon: 'error',
                    title: 'Xəta!',
                    text: errorMessage
                });

                // Xətanı console-da göstər
                console.error("Sektor admin təyinatı xətası:", xhr);
            }
        });
    });

    // Modal bağlandıqda formu sıfırla
    $("#sectorAdminModal").on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });
});
</script>
@endpush