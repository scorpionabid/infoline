<?php $__env->startSection('title', 'İstifadəçilər'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">İstifadəçilər</h4>
            </div>
        </div>
    </div>

    <!-- Filters and actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <a href="<?php echo e(route('settings.personal.users.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Yeni İstifadəçi
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <input type="text" class="form-control w-auto me-2" id="searchInput" placeholder="Axtar...">
                                <select class="form-select w-auto" id="userTypeFilter">
                                    <option value="">Bütün tiplər</option>
                                    <?php $__currentLoopData = $user_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>Email</th>
                                    <th>Tip</th>
                                    <th>Region</th>
                                    <th>Sektor</th>
                                    <th>Məktəb</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($user->full_name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td><?php echo e($user_types[$user->user_type->value] ?? $user->user_type->value); ?></td>
                                    <td><?php echo e($user->region->name ?? '-'); ?></td>
                                    <td><?php echo e($user->sector->name ?? '-'); ?></td>
                                    <td><?php echo e($user->school->name ?? '-'); ?></td>
                                    <td>
                                        <?php if($user->status === 'active'): ?>
                                            <span class="badge bg-success">Aktiv</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Deaktiv</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="<?php echo e(route('settings.personal.users.edit', $user->id)); ?>" class="dropdown-item">
                                                    <i class="fas fa-edit text-primary"></i> Redaktə et
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item toggle-status" 
                                                   data-id="<?php echo e($user->id); ?>" 
                                                   data-status="<?php echo e($user->status); ?>">
                                                    <?php if($user->status === 'active'): ?>
                                                        <i class="fas fa-ban text-danger"></i> Deaktiv et
                                                    <?php else: ?>
                                                        <i class="fas fa-check text-success"></i> Aktiv et
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">İstifadəçi tapılmadı</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($users->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link href="<?php echo e(asset('assets/libs/select2/select2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
<script src="<?php echo e(asset('assets/libs/select2/select2.min.js')); ?>"></script>
<script>
$(document).ready(function() {
    // Select2 initialization
    $('.form-select').select2();

    // Search functionality
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // User type filter
    $('#userTypeFilter').on('change', function() {
        var value = $(this).val().toLowerCase();
        if (value) {
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).find("td:eq(2)").text().toLowerCase().indexOf(value) > -1)
            });
        } else {
            $("table tbody tr").show();
        }
    });

    // Status toggle
    $('.toggle-status').on('click', function() {
        var userId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        var $button = $(this);

        $.ajax({
            url: '/settings/personal/users/' + userId + '/toggle-status',
            type: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                status: newStatus
            },
            success: function(response) {
                // Reload page to show updated status
                location.reload();
            },
            error: function(xhr) {
                alert('Xəta baş verdi!');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/users/index.blade.php ENDPATH**/ ?>