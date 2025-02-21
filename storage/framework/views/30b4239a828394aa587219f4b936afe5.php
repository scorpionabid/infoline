<?php $__env->startSection('title', 'Məktəblər'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Başlıq -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Məktəblər</h1>
        <a href="<?php echo e(route('settings.personal.schools.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Məktəb
        </a>
    </div>

    <!-- Filtrlər -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrlər</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="region" class="form-label">Region</label>
                    <select name="region" id="region" class="form-control">
                        <option value="">Bütün regionlar</option>
                        <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($region->id); ?>" <?php echo e(request('region') == $region->id ? 'selected' : ''); ?>>
                                <?php echo e($region->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sector" class="form-label">Sektor</label>
                    <select name="sector" id="sector" class="form-control">
                        <option value="">Bütün sektorlar</option>
                        <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sector->id); ?>" <?php echo e(request('sector') == $sector->id ? 'selected' : ''); ?>>
                                <?php echo e($sector->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Məktəb Tipi</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">Bütün tiplər</option>
                        <?php $__currentLoopData = $schoolTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type); ?>" <?php echo e(request('type') == $type ? 'selected' : ''); ?>>
                                <?php echo e($type); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Bütün</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Aktiv</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Deaktiv</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="search" class="form-label">Axtar</label>
                    <input type="text" name="search" id="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Ad, UTİS kodu, email...">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrlə
                    </button>
                    <a href="<?php echo e(route('settings.personal.schools.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Sıfırla
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Məktəblər Cədvəli -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="schoolsTable">
                    <thead>
                        <tr>
                            <th>UTİS Kodu</th>
                            <th>Ad</th>
                            <th>Region</th>
                            <th>Sektor</th>
                            <th>Tip</th>
                            <th>Admin</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($school->utis_code); ?></td>
                                <td><?php echo e($school->name); ?></td>
                                <td><?php echo e($school->sector->region->name); ?></td>
                                <td><?php echo e($school->sector->name); ?></td>
                                <td><?php echo e($school->type); ?></td>
                                <td>
                                    <?php if($school->admin): ?>
                                        <?php echo e($school->admin->name); ?>

                                    <?php else: ?>
                                        <span class="badge badge-warning">Təyin edilməyib</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input status-switch"
                                               id="status_<?php echo e($school->id); ?>"
                                               data-id="<?php echo e($school->id); ?>"
                                               <?php echo e($school->status ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="status_<?php echo e($school->id); ?>"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('settings.personal.schools.edit', $school)); ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-school"
                                                data-id="<?php echo e($school->id); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <a href="<?php echo e(route('settings.personal.schools.data', $school)); ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-database"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">Məktəb tapılmadı</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Səhifələmə -->
            <div class="mt-3">
                <?php echo e($schools->withQueryString()->links()); ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Status dəyişdirmə
    $('.status-switch').change(function() {
        const schoolId = $(this).data('id');
        const status = $(this).prop('checked');
        
        $.ajax({
            url: `/settings/personal/schools/${schoolId}/status`,
            type: 'POST',
            data: {
                status: status,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                toastr.error('Xəta baş verdi');
                $(this).prop('checked', !status);
            }
        });
    });

    // Məktəb silmə
    $('.delete-school').click(function() {
        const schoolId = $(this).data('id');
        
        Swal.fire({
            title: 'Əminsiniz?',
            text: "Bu məktəbi silmək istədiyinizə əminsiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/settings/personal/schools/${schoolId}`,
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
                    }
                });
            }
        });
    });

    // Region dəyişdikdə sektorları yenilə
    $('#region').change(function() {
        const regionId = $(this).val();
        const sectorSelect = $('#sector');
        
        sectorSelect.empty().append('<option value="">Bütün sektorlar</option>');
        
        if (regionId) {
            $.get(`/api/regions/${regionId}/sectors`, function(sectors) {
                sectors.forEach(function(sector) {
                    sectorSelect.append(new Option(sector.name, sector.id));
                });
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/index.blade.php ENDPATH**/ ?>