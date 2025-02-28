<?php $__env->startSection('title', 'Cədvəl Ayarları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-5">
    <?php echo $__env->make('partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Başlıq -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 text-gray-800">Cədvəl Ayarları</h4>
            <p class="text-muted mb-0">Məlumat toplama cədvəllərinin idarə edilməsi</p>
        </div>
        <div class="d-flex gap-2">
            <?php if($selectedCategory): ?>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cloneCategoryModal">
                    <i class="fas fa-copy me-2"></i>Kateqoriyanı Kopyala
                </button>
            <?php endif; ?>
            <button class="btn btn-primary add-category" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fas fa-plus me-2"></i>Yeni Kateqoriya
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Kateqoriyalar -->
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kateqoriyalar</h5>
                    <span class="badge bg-light text-primary"><?php echo e($categories->count()); ?></span>
                </div>
                <div class="list-group list-group-flush" id="categoriesList">
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo e(route('settings.table.index', ['category' => $category->id])); ?>" 
                                   class="text-decoration-none text-dark flex-grow-1 <?php echo e($selectedCategory && $selectedCategory->id === $category->id ? 'fw-bold' : ''); ?>">
                                    <?php echo e($category->name); ?>

                                    <span class="badge bg-secondary rounded-pill ms-2"><?php echo e($category->columns_count); ?></span>
                                </a>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch ms-2">
                                        <input class="form-check-input category-status" type="checkbox" 
                                               data-category-id="<?php echo e($category->id); ?>"
                                               <?php echo e($category->status ? 'checked' : ''); ?>>
                                    </div>
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item edit-category" data-category-id="<?php echo e($category->id); ?>" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                    <i class="fas fa-edit me-2"></i>Redaktə et
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item delete-category" data-category-id="<?php echo e($category->id); ?>">
                                                    <i class="fas fa-trash me-2"></i>Sil
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php if($category->description): ?>
                                <small class="text-muted d-block mt-1"><?php echo e($category->description); ?></small>
                            <?php endif; ?>
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sütunlar</h5>
                    <?php if($selectedCategory): ?>
                        <div>
                            <span class="badge bg-light text-primary me-2"><?php echo e($columns->count()); ?></span>
                            <button class="btn btn-light btn-sm add-column" data-category-id="<?php echo e($selectedCategory->id); ?>" data-bs-toggle="modal" data-bs-target="#columnModal">
                                <i class="fas fa-plus me-1"></i>Yeni Sütun
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(!$selectedCategory): ?>
                        <div class="alert alert-info text-center">
                            Sütunları görmək üçün soldakı siyahıdan bir kateqoriya seçin.
                        </div>
                    <?php elseif($columns->isEmpty()): ?>
                        <div class="alert alert-info text-center">
                            Bu kateqoriyada hələ heç bir sütun yoxdur.
                            <button class="btn btn-primary btn-sm ms-2 add-column" data-category-id="<?php echo e($selectedCategory->id); ?>" data-bs-toggle="modal" data-bs-target="#columnModal">
                                <i class="fas fa-plus me-1"></i>Sütun əlavə et
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Sütun</th>
                                        <th style="width: 120px;">Növ</th>
                                        <th style="width: 150px;">Son tarix</th>
                                        <th style="width: 100px;">Limit</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 120px;">Əməliyyatlar</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable">
                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr data-column-id="<?php echo e($column->id); ?>">
                                            <td>
                                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo e($column->name); ?></div>
                                                <?php if($column->description): ?>
                                                    <small class="text-muted"><?php echo e($column->description); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($column->type); ?></span>
                                                <?php if($column->required): ?>
                                                    <span class="badge bg-danger ms-1">Məcburi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if($column->end_date): ?>
                                                        <span class="me-2 <?php echo e($column->isExpired() ? 'text-danger' : 'text-success'); ?>">
                                                            <?php echo e($column->end_date->format('d.m.Y')); ?>

                                                        </span>
                                                        <button class="btn btn-sm btn-outline-secondary set-deadline" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deadlineModal"
                                                                data-column-id="<?php echo e($column->id); ?>">
                                                            <i class="fas fa-clock"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted">--</span>
                                                        <button class="btn btn-sm btn-outline-secondary ms-2 set-deadline" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deadlineModal"
                                                                data-column-id="<?php echo e($column->id); ?>">
                                                            <i class="fas fa-clock"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if($column->input_limit): ?>
                                                        <span class="me-2"><?php echo e($column->input_limit); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted me-2">∞</span>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateLimitModal"
                                                            data-column-id="<?php echo e($column->id); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input column-status" type="checkbox" 
                                                           data-column-id="<?php echo e($column->id); ?>"
                                                           <?php echo e($column->is_active ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <?php echo e($column->is_active ? 'Aktiv' : 'Deaktiv'); ?>

                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning edit-column" data-column-id="<?php echo e($column->id); ?>" data-bs-toggle="modal" data-bs-target="#columnModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-column" data-column-id="<?php echo e($column->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.settings.table.modals.category-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partials.settings.table.modals.column', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partials.settings.table.modals.deadline', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('partials.settings.table.modals.limit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/settings/table/table-utils.js')); ?>"></script>
<script src="<?php echo e(asset('js/settings/table/category-operations.js')); ?>"></script>
<script src="<?php echo e(asset('js/settings/table/column-operations.js')); ?>"></script>
<script src="<?php echo e(asset('js/settings/table/deadline-operations.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="<?php echo e(asset('js/settings/table/table-init.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/table/table.blade.php ENDPATH**/ ?>