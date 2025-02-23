<?php $__env->startSection('title', 'Məktəblər'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <h5 class="navbar-brand mb-0">Məktəblər</h5>
            <div class="ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i>Yeni Məktəb
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('settings.personal.schools.index')); ?>" method="GET" id="filter-form">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Region</label>
                            <select class="form-select" name="region_id" onchange="this.form.submit()">
                                <option value="">Bütün Regionlar</option>
                                <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($region->id); ?>" <?php echo e(request('region_id') == $region->id ? 'selected' : ''); ?>>
                                        <?php echo e($region->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sektor</label>
                            <select class="form-select" name="sector_id" onchange="this.form.submit()">
                                <option value="">Bütün Sektorlar</option>
                                <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sector->id); ?>" <?php echo e(request('sector_id') == $sector->id ? 'selected' : ''); ?>>
                                        <?php echo e($sector->name); ?> (<?php echo e($sector->region->name); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schools Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select-all">
                                    </div>
                                </th>
                                <th>Məktəb Adı</th>
                                <th>UTİS Kodu</th>
                                <th>Region</th>
                                <th>Sektor</th>
                                <th>Telefon</th>
                                <th>Admin</th>
                                <th class="text-center">Admin sayı</th>
                                <th class="text-center">Tamamlanma</th>
                                <th class="text-center" style="width: 100px">Əməliyyatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input school-checkbox" value="<?php echo e($school->id); ?>">
                                        </div>
                                    </td>
                                    <td><?php echo e($school->name); ?></td>
                                    <td><?php echo e($school->utis_code); ?></td>
                                    <td><?php echo e($school->sector->region->name ?? '-'); ?></td>
                                    <td><?php echo e($school->sector->name ?? '-'); ?></td>
                                    <td><?php echo e($school->phone ?? '-'); ?></td>
                                    <td><?php echo e($school->admin?->full_name ?? '-'); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo e($school->admins->count()); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress w-75 me-2">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo e($school->data_completion_percentage); ?>%">
                                                </div>
                                            </div>
                                            <small><?php echo e($school->data_completion_percentage); ?>%</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $__env->make('pages.settings.personal.schools.partials.actions', ['school' => $school], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Məktəb tapılmadı</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <?php echo e($schools->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Yeni Məktəb</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="schoolForm" action="<?php echo e(route('settings.personal.schools.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Məktəb adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="utis_code" class="form-label">UTİS kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="utis_code" name="utis_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Məktəb tipi <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $schoolTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sector_id" class="form-label">Sektor <span class="text-danger">*</span></label>
                        <select class="form-select" id="sector_id" name="sector_id" required>
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sector->id); ?>"><?php echo e($sector->name); ?> (<?php echo e($sector->region->name); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Ünvan</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="submit" class="btn btn-primary" form="schoolForm">Yadda saxla</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Admin Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1" aria-labelledby="assignAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAdminModalLabel">Admin təyin et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignAdminForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">Admin seçin <span class="text-danger">*</span></label>
                        <select class="form-select" id="admin_id" name="admin_id" required>
                            <option value="">Seçin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="submit" class="btn btn-primary" form="assignAdminForm">Təyin et</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .table th { font-weight: 600; }
    .progress { height: 5px; }
    .form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?php echo e(asset('js/settings/school.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/index.blade.php ENDPATH**/ ?>