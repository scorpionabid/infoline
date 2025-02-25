<?php $__env->startSection('title', 'Məktəb Redaktəsi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <!-- Məktəb Məlumatları -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Məlumatları</h6>
                    <a href="<?php echo e(route('settings.personal.schools.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
                <div class="card-body">
                    <form id="editSchoolForm" method="POST" action="<?php echo e(route('settings.personal.schools.update', $school)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="row">
                            <!-- Əsas Məlumatlar -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Əsas Məlumatlar</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Məktəbin Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" name="name" value="<?php echo e(old('name', $school->name)); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="utis_code" class="form-label">UTİS Kodu <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['utis_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="utis_code" name="utis_code" value="<?php echo e(old('utis_code', $school->utis_code)); ?>" required>
                                    <?php $__errorArgs = ['utis_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Məktəb Tipi <span class="text-danger">*</span></label>
                                    <select class="form-control <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="type" name="type" required>
                                        <option value="">Seçin</option>
                                        <?php $__currentLoopData = $schoolTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type); ?>" <?php echo e(old('type', $school->type) == $type ? 'selected' : ''); ?>>
                                                <?php echo e($type); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="sector_id" class="form-label">Sektor <span class="text-danger">*</span></label>
                                    <select class="form-control <?php $__errorArgs = ['sector_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="sector_id" name="sector_id" required>
                                        <option value="">Seçin</option>
                                        <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sector->id); ?>" <?php echo e(old('sector_id', $school->sector_id) == $sector->id ? 'selected' : ''); ?>>
                                                <?php echo e($sector->region->name); ?> - <?php echo e($sector->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['sector_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Əlaqə Məlumatları -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Əlaqə Məlumatları</h5>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefon</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="phone" name="phone" value="<?php echo e(old('phone', $school->phone)); ?>">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" name="email" value="<?php echo e(old('email', $school->email)); ?>">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Vebsayt</label>
                                    <input type="url" class="form-control <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="website" name="website" value="<?php echo e(old('website', $school->website)); ?>">
                                    <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Ünvan</label>
                                    <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="address" name="address" rows="3"><?php echo e(old('address', $school->address)); ?></textarea>
                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Əlavə Məlumatlar -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3">Əlavə Məlumatlar</h5>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Təsvir</label>
                                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="description" name="description" rows="3"><?php echo e(old('description', $school->description)); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="status" name="status" value="1" 
                                               <?php echo e(old('status', $school->status) ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="status">Aktiv</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Yadda Saxla
                            </button>
                            <a href="<?php echo e(route('settings.personal.schools.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Ləğv Et
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sağ Panel -->
        <div class="col-md-4">
            <!-- Məktəb Statistikası -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Statistikası</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">Məlumat Tamamlanması 
                            <span class="float-right"><?php echo e($dataCompletion['percentage']); ?>%</span>
                        </h4>
                        <div class="progress">
                            <div class="progress-bar bg-<?php echo e($dataCompletion['percentage'] < 50 ? 'danger' : ($dataCompletion['percentage'] < 80 ? 'warning' : 'success')); ?>" 
                                 role="progressbar" style="width: <?php echo e($dataCompletion['percentage']); ?>%"></div>
                        </div>

                        <!-- Kateqoriyalar üzrə tamamlanma -->
                        <?php $__currentLoopData = $dataCompletion['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <h6 class="small font-weight-bold"><?php echo e($categoryName); ?>

                                    <span class="float-right"><?php echo e($stats['percentage']); ?>%</span>
                                </h6>
                                <div class="progress">
                                    <div class="progress-bar bg-info" 
                                         role="progressbar" 
                                         style="width: <?php echo e($stats['percentage']); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="text-center">
                        <a href="<?php echo e(route('settings.personal.schools.show.data', $school)); ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-database"></i> Məlumatları İdarə Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Məktəb Administratoru -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Administratoru</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createAdminModal">
                        <i class="fas fa-plus"></i> Yeni Admin
                    </button>
                </div>
                <div class="card-body">
                    <?php if($school->admin): ?>
                        <div class="text-center mb-3">
                            <img src="<?php echo e($school->admin->avatar_url ?? asset('images/default-avatar.png')); ?>" 
                                 alt="<?php echo e($school->admin->first_name); ?> <?php echo e($school->admin->last_name); ?>" 
                                 class="img-profile rounded-circle" 
                                 style="width: 100px; height: 100px;">
                        </div>
                        <h5 class="text-center mb-3"><?php echo e($school->admin->first_name); ?> <?php echo e($school->admin->last_name); ?></h5>
                        <p class="text-center mb-2">
                            <i class="fas fa-envelope"></i> <?php echo e($school->admin->email); ?>

                        </p>
                        <p class="text-center">
                            <i class="fas fa-phone"></i> <?php echo e($school->admin->phone ?? 'Təyin edilməyib'); ?>

                        </p>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeAdmin()">
                                <i class="fas fa-user-minus"></i> Adminı Sil
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="mb-3">Bu məktəbə hələ administrator təyin edilməyib.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assignAdminModal">
                                <i class="fas fa-user-plus"></i> Admin Təyin Et
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Administrator Təyin Etmə Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Administrator Təyin Et</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="admin_id">Administrator Seçin</label>
                    <select class="form-control" id="admin_id" name="admin_id">
                        <option value="">Seçin...</option>
                        <?php $__currentLoopData = $availableAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($admin->id); ?>"><?php echo e($admin->first_name); ?> <?php echo e($admin->last_name); ?> (<?php echo e($admin->email); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" onclick="assignAdmin()">Təyin Et</button>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Administrator Yaratma Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Administrator</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createAdminForm">
                    <div class="form-group">
                        <label for="first_name">Ad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">İstifadəçi adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Şifrə <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="utis_code">UTİS Kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="utis_code" name="utis_code" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" onclick="createAdmin()">Yarat</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function createAdmin() {
    const form = document.getElementById('createAdminForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    axios.post('<?php echo e(route("settings.personal.schools.admins.create")); ?>', data)
        .then(response => {
            if (response.data.success) {
                toastr.success(response.data.message);
                $('#createAdminModal').modal('hide');
                // Yeni admin yaradıldıqdan sonra siyahını yeniləyirik
                loadAvailableAdmins();
            }
        })
        .catch(error => {
            if (error.response.data.errors) {
                Object.values(error.response.data.errors).forEach(error => {
                    toastr.error(error[0]);
                });
            } else {
                toastr.error(error.response.data.message || 'Xəta baş verdi');
            }
        });
}

function assignAdmin() {
    const adminId = document.getElementById('admin_id').value;
    if (!adminId) {
        toastr.error('Administrator seçilməlidir');
        return;
    }

    axios.post('<?php echo e(route("settings.personal.schools.assign-admin", $school)); ?>', { admin_id: adminId })
        .then(response => {
            if (response.data.success) {
                toastr.success(response.data.message);
                $('#assignAdminModal').modal('hide');
                // Səhifəni yeniləyirik
                window.location.reload();
            }
        })
        .catch(error => {
            toastr.error(error.response.data.message || 'Xəta baş verdi');
        });
}

function removeAdmin() {
    if (!confirm('Administratoru silmək istədiyinizə əminsiniz?')) {
        return;
    }

    axios.delete('<?php echo e(route("settings.personal.schools.remove-admin", $school)); ?>')
        .then(response => {
            if (response.data.success) {
                toastr.success(response.data.message);
                // Səhifəni yeniləyirik
                window.location.reload();
            }
        })
        .catch(error => {
            toastr.error(error.response.data.message || 'Xəta baş verdi');
        });
}

function loadAvailableAdmins() {
    axios.get('<?php echo e(route("settings.personal.schools.admins.available")); ?>')
        .then(response => {
            const select = document.getElementById('admin_id');
            select.innerHTML = '<option value="">Seçin...</option>';
            
            response.data.forEach(admin => {
                const option = document.createElement('option');
                option.value = admin.id;
                option.textContent = `${admin.first_name} ${admin.last_name} (${admin.email})`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            toastr.error('Administratorlar yüklənərkən xəta baş verdi');
        });
}
</script>
<?php $__env->stopPush(); ?>        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Administrator Təyin Et</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="assignAdminForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="admin_id">Administrator</label>
                        <select class="form-control" id="admin_id" name="admin_id" required>
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $availableAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($admin->id); ?>"><?php echo e($admin->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Təyin Et</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Form submit
    $('#editSchoolForm').submit(function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });

    // Select2 inteqrasiyası
    $('#sector_id, #type, #admin_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Telefon nömrəsi formatı
    $('#phone').inputmask('+\\9\\94 (99) 999-99-99');

    // Administrator təyin etmə
    $('#assignAdminForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "<?php echo e(route('settings.personal.schools.assign-admin', $school)); ?>",
            type: 'POST',
            data: {
                admin_id: $('#admin_id').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                $('#assignAdminModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/edit.blade.php ENDPATH**/ ?>