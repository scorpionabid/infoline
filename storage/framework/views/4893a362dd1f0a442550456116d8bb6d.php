<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Regionlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['regionCount']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Məlumat Filtirləri</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Sektor</label>
                        <select name="sector_id" class="form-control">
                            <option value="">Bütün sektorlar</option>
                            <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sector->id); ?>" <?php echo e(request('sector_id') == $sector->id ? 'selected' : ''); ?>>
                                    <?php echo e($sector->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Kateqoriya</label>
                        <select name="category_id" class="form-control">
                            <option value="">Bütün kateqoriyalar</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e($category->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Bütün</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Gözləyir</option>
                            <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Tamamlanıb</option>
                            <option value="expired" <?php echo e(request('status') == 'expired' ? 'selected' : ''); ?>>Vaxtı keçib</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrlə
                            </button>
                            
                            <button type="submit" name="export" value="1" class="btn btn-success">
                                <i class="fas fa-download"></i> Export
                            </button>

                            <a href="<?php echo e(route('dashboard.super-admin')); ?>" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Sıfırla
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Məktəb</th>
                            <th>Sektor</th>
                            <th>Kateqoriya</th>
                            <th>Son yenilənmə</th>
                            <th>Status</th>
                            <th>Tamamlanma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $schoolData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($data->school->name); ?></td>
                                <td><?php echo e($data->school->sector->name); ?></td>
                                <td><?php echo e($data->category->name); ?></td>
                                <td><?php echo e($data->updated_at->format('d.m.Y H:i')); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($data->getStatusColor()); ?>">
                                        <?php echo e($data->getStatusText()); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: <?php echo e($data->completion_percentage); ?>%">
                                            <?php echo e($data->completion_percentage); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">Məlumat tapılmadı</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php echo e($schoolData->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // DataTables initialization
    $('#dataTable').DataTable({
        "pageLength": 15,
        "ordering": true,
        "info": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Azerbaijan.json"
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/dashboard/super-admin.blade.php ENDPATH**/ ?>