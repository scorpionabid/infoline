<?php $__env->startSection('tab-content'); ?>
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
                           <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <tr>
                               <td><?php echo e($sector->region->name); ?></td>
                               <td><?php echo e($sector->name); ?></td>
                               <td><?php echo e($sector->phone); ?></td>
                               <td><?php echo e($sector->schools_count); ?></td>
                               <td>
                                   <?php if($sector->admin): ?>
                                       <?php echo e($sector->admin->name); ?>

                                   <?php else: ?>
                                       <span class="badge bg-warning">Admin təyin edilməyib</span>
                                   <?php endif; ?>
                               </td>
                               <<td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editSector(<?php echo e($sector->id); ?>)"
                                                type="button">
                                            <i class="fas fa-edit"></i>
                                        </button>
        
        <!-- Yeni admin təyinat düyməsi -->
                                        <button class="btn btn-sm btn-outline-warning assign-admin-btn" 
                                            data-sector-id="<?php echo e($sector->id); ?>"
                                            <i class="fas fa-user-plus"></i>
                                        </button>

                                        <?php if($sector->schools_count == 0): ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteSector(<?php echo e($sector->id); ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                           </tr>
                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pages.settings.personal.modals.sector-admin-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('pages.settings.personal.modals.sector-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('pages.settings.personal.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/sectors/index.blade.php ENDPATH**/ ?>