<?php $__env->startSection('title', 'Sektor İdarəetməsi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard.index')); ?>">Panel</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('settings.index')); ?>">Tənzimləmələr</a></li>
                        <li class="breadcrumb-item active">Sektor İdarəetməsi</li>
                    </ol>
                </div>
                <h4 class="page-title">Sektor İdarəetməsi</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Ümumi Sektorlar</h5>
                        <i class="fas fa-building text-muted"></i>
                    </div>
                    <h2 class="mb-2"><?php echo e($totalSectors); ?></h2>
                    <p class="text-muted mb-0">Bütün regionlar üzrə</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Aktiv Adminlər</h5>
                        <i class="fas fa-user-shield text-muted"></i>
                    </div>
                    <h2 class="mb-2"><?php echo e($activeAdmins); ?></h2>
                    <p class="text-muted mb-0">Sektor adminləri</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Məktəblər</h5>
                        <i class="fas fa-school text-muted"></i>
                    </div>
                    <h2 class="mb-2"><?php echo e($totalSchools); ?></h2>
                    <p class="text-muted mb-0">Bütün sektorlar üzrə</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sektorlar</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-secondary" id="exportExcel">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectorModal">
                            <i class="fas fa-plus me-1"></i> Yeni Sektor
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select select2" id="regionFilter">
                                <option value="">Region seçin</option>
                                <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($region->id); ?>"><?php echo e($region->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select select2" id="adminFilter">
                                <option value="">Admin statusu</option>
                                <option value="with_admin">Adminli</option>
                                <option value="without_admin">Adminsiz</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-centered w-100 dt-responsive nowrap" id="sectors-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Sektor</th>
                                    <th>Region</th>
                                    <th>Məktəb Sayı</th>
                                    <th>Admin</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Sector Modal -->
<div class="modal fade" id="sectorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectorModalLabel">Yeni Sektor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sectorForm">
                <div class="modal-body">
                    <input type="hidden" id="sectorId">
                    <div class="mb-3">
                        <label class="form-label">Region</label>
                        <select class="form-select select2" name="region_id" id="region_id" required>
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($region->id); ?>"><?php echo e($region->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sektor adı</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" class="form-control" name="phone" id="phone" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary" id="saveSector">
                        <span class="spinner-border spinner-border-sm d-none me-1"></span>
                        Yadda saxla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin Assignment Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Təyin Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adminForm">
                <div class="modal-body">
                    <input type="hidden" id="adminSectorId">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="full_name" id="admin_name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="admin_email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" class="form-control" name="phone" id="admin_phone" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="sendCredentials" checked>
                        <label class="form-check-label">Giriş məlumatlarını email ilə göndər</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary" id="saveAdmin">
                        <span class="spinner-border spinner-border-sm d-none me-1"></span>
                        Təyin et
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link href="<?php echo e(asset('assets/libs/datatables/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('assets/libs/datatables/responsive.bootstrap5.min.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('assets/libs/select2/select2.min.css')); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/libs/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/datatables/dataTables.bootstrap5.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/datatables/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/select2/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/settings/sector.js')); ?>"></script>
<script>
    // DataTable konfiqurasiyası
    $('#sectors-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("settings.personal.sectors.data")); ?>',
            data: function(d) {
                d.region = $('#regionFilter').val();
                d.admin_status = $('#adminFilter').val();
            }
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'region.name', name: 'region.name'},
            {data: 'schools_count', name: 'schools_count'},
            {
                data: 'admin',
                name: 'admin.full_name',
                render: function(data) {
                    if (data) {
                        return `<span class="badge bg-success">${data.full_name}</span>`;
                    }
                    return '<span class="badge bg-warning">Təyin edilməyib</span>';
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    return data ? 
                        '<span class="badge bg-success">Aktiv</span>' : 
                        '<span class="badge bg-danger">Deaktiv</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let buttons = `
                        <a href="/settings/personal/sectors/${data.id}/edit" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-sector" data-id="${data.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    
                    if (!data.admin) {
                        buttons += `
                            <button class="btn btn-sm btn-success assign-admin-btn" data-id="${data.id}">
                                <i class="fas fa-user-shield"></i>
                            </button>
                        `;
                    }
                    
                    return buttons;
                }
            }
        ],
        order: [[0, 'asc']],
        language: {
            url: '/assets/libs/datatables/i18n/az.json'
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/sectors/index.blade.php ENDPATH**/ ?>