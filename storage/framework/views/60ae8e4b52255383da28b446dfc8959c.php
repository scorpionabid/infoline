<?php $__env->startSection('title', 'Cədvəl'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-5">
    <!-- Bildirişlər -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Başlıq -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 text-gray-800">Cədvəl Ayarları</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-2"></i>Yeni Kateqoriya
        </button>
    </div>

    <div class="row">
        <!-- Kateqoriyalar -->
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kateqoriyalar</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center
                              <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('settings.table.index', ['category' => $category->id])); ?>" 
                               class="text-decoration-none <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'text-white' : 'text-dark'); ?>">
                                <?php echo e($category->name); ?>

                                <span class="badge <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'bg-white text-primary' : 'bg-secondary'); ?> rounded-pill ms-2">
                                    <?php echo e($category->columns_count); ?>

                                </span>
                            </a>
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-sm <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'btn-light' : 'btn-outline-primary'); ?>"
                                        data-action="edit-category"
                                        data-category-id="<?php echo e($category->id); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'btn-light' : 'btn-outline-danger'); ?>"
                                        data-action="delete-category"
                                        data-category-id="<?php echo e($category->id); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item text-muted text-center">
                            Kateqoriya tapılmadı
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sütunlar -->
        <div class="col-md-9">
            <div class="card shadow h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <?php if($selectedCategory): ?>
                            <?php echo e($selectedCategory->name); ?> - Sütunlar
                        <?php else: ?>
                            Sütunlar
                        <?php endif; ?>
                    </h5>
                    <?php if($selectedCategory): ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                            <i class="fas fa-plus me-2"></i>Yeni Sütun
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($selectedCategory): ?>
                        <?php if($columns->isNotEmpty()): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ad</th>
                                            <th>Tip</th>
                                            <th>Son Tarix</th>
                                            <th>Limit</th>
                                            <th>Status</th>
                                            <th>Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($column->name); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo e($column->data_type); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($column->end_date): ?>
                                                        <?php echo e($column->end_date->format('d.m.Y')); ?>

                                                    <?php else: ?>
                                                        <span class="text-muted">--</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($column->input_limit): ?>
                                                        <?php echo e($column->input_limit); ?>

                                                    <?php else: ?>
                                                        <span class="text-muted">--</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($column->end_date && $column->end_date->isPast()): ?>
                                                        <span class="badge bg-danger">Bitmişdir</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Aktivdir</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" 
                                                            data-action="edit-column"
                                                            data-column-id="<?php echo e($column->id); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                            data-action="delete-column"
                                                            data-column-id="<?php echo e($column->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p>Bu kateqoriyada hələ heç bir sütun yoxdur.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-arrow-left fa-2x mb-3"></i>
                            <p>Zəhmət olmasa, kateqoriya seçin.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modallar -->
<?php echo $__env->make('partials.category-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partials.column-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/settings/table.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/table/index.blade.php ENDPATH**/ ?>