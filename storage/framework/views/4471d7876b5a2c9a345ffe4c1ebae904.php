<?php $__env->startSection('title', 'Şəxsi İdarəetmə'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Şəxsi İdarəetmə</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Regionlar -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?php echo e($regions_count); ?></span></h4>
                            <p class="text-muted mb-0">Regionlar</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-primary align-self-center">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="fas fa-map-marker-alt font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('settings.personal.regions.index')); ?>" class="btn btn-primary btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- Sektorlar -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?php echo e($sectors_count); ?></span></h4>
                            <p class="text-muted mb-0">Sektorlar</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-success align-self-center">
                            <span class="avatar-title rounded-circle bg-success">
                                <i class="fas fa-sitemap font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('settings.personal.sectors.index')); ?>" class="btn btn-success btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- Məktəblər -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?php echo e($schools_count); ?></span></h4>
                            <p class="text-muted mb-0">Məktəblər</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-info align-self-center">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="fas fa-school font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('settings.personal.schools.index')); ?>" class="btn btn-info btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- İstifadəçilər -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?php echo e($users_count); ?></span></h4>
                            <p class="text-muted mb-0">İstifadəçilər</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-warning align-self-center">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="fas fa-users font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('settings.personal.users.index')); ?>" class="btn btn-warning btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Fəaliyyətlər -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Son Fəaliyyətlər</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tarix</th>
                                    <th>İstifadəçi</th>
                                    <th>Əməliyyat</th>
                                    <th>Detallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($activity->created_at->format('d.m.Y H:i')); ?></td>
                                    <td><?php echo e($activity->causer->full_name ?? '-'); ?></td>
                                    <td><?php echo e($activity->description); ?></td>
                                    <td><?php echo e($activity->properties['details'] ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Son fəaliyyət tapılmadı</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link href="<?php echo e(asset('assets/libs/counter/counter.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
<script src="<?php echo e(asset('assets/libs/counter/counter.min.js')); ?>"></script>
<script>
    $("[data-plugin='counterup']").each(function(index, el) {
        $(this).counterUp({
            delay: 10,
            time: 1000
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/index.blade.php ENDPATH**/ ?>