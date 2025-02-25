<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3">İstifadəçi Düzəliş</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('settings.users.update', $user)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ad</label>
                        <input type="text" name="first_name" class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('first_name', $user->first_name)); ?>" required>
                        <?php $__errorArgs = ['first_name'];
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
                    
                    <div class="col-md-6">
                        <label class="form-label">Soyad</label>
                        <input type="text" name="last_name" class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('last_name', $user->last_name)); ?>" required>
                        <?php $__errorArgs = ['last_name'];
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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('email', $user->email)); ?>" required>
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
                    
                    <div class="col-md-6">
                        <label class="form-label">İstifadəçi adı</label>
                        <input type="text" name="username" class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('username', $user->username)); ?>" required>
                        <?php $__errorArgs = ['username'];
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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Şifrə (Dəyişmək üçün doldurun)</label>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['password'];
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
                    
                    <div class="col-md-6">
                        <label class="form-label">UTIS Kodu</label>
                        <input type="text" name="utis_code" class="form-control <?php $__errorArgs = ['utis_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('utis_code', $user->utis_code)); ?>" required>
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
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">İstifadəçi Tipi</label>
                        <select name="user_type" class="form-select <?php $__errorArgs = ['user_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                onchange="handleUserTypeChange(this.value)" 
                                <?php echo e($user->isSuperAdmin() ? 'disabled' : 'required'); ?>>
                            <option value="">Seçin</option>
                            <option value="sectoradmin" <?php if(old('user_type', $user->user_type) == 'sectoradmin'): echo 'selected'; endif; ?>>
                                Sektor Admin
                            </option>
                            <option value="schooladmin" <?php if(old('user_type', $user->user_type) == 'schooladmin'): echo 'selected'; endif; ?>>
                                Məktəb Admin
                            </option>
                        </select>
                        <?php $__errorArgs = ['user_type'];
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
                    
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select name="region_id" id="region_id" 
                                class="form-select <?php $__errorArgs = ['region_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                onchange="loadSectors(this.value)">
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($region->id); ?>" 
                                        <?php if(old('region_id', $user->region_id) == $region->id): echo 'selected'; endif; ?>>
                                    <?php echo e($region->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['region_id'];
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

                <div class="row mb-3">
                    <div class="col-md-6" id="sector_container">
                        <label class="form-label">Sektor</label>
                        <select name="sector_id" id="sector_id" 
                                class="form-select <?php $__errorArgs = ['sector_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                onchange="loadSchools(this.value)">
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sector->id); ?>" 
                                        <?php if(old('sector_id', $user->sector_id) == $sector->id): echo 'selected'; endif; ?>>
                                    <?php echo e($sector->name); ?>

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
                    
                    <div class="col-md-6" id="school_container">
                        <label class="form-label">Məktəb</label>
                        <select name="school_id" id="school_id" 
                                class="form-select <?php $__errorArgs = ['school_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seçin</option>
                            <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($school->id); ?>" 
                                        <?php if(old('school_id', $user->school_id) == $school->id): echo 'selected'; endif; ?>>
                                    <?php echo e($school->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['school_id'];
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

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Rollar</label>
                        <div class="row">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" 
                                               value="<?php echo e($role->id); ?>"
                                               class="form-check-input <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               <?php if(in_array($role->id, old('roles', $user->roles->pluck('id')->toArray()))): echo 'checked'; endif; ?>>
                                        <label class="form-check-label"><?php echo e($role->name); ?></label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Yadda Saxla
                        </button>
                        <a href="<?php echo e(route('settings.users.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Ləğv Et
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function handleUserTypeChange(type) {
    const sectorContainer = document.getElementById('sector_container');
    const schoolContainer = document.getElementById('school_container');
    const regionSelect = document.getElementById('region_id');
    const sectorSelect = document.getElementById('sector_id');
    const schoolSelect = document.getElementById('school_id');

    if (type === 'sectoradmin') {
        sectorContainer.style.display = 'block';
        schoolContainer.style.display = 'none';
        schoolSelect.value = '';
        regionSelect.required = true;
        sectorSelect.required = false;
        schoolSelect.required = false;
    } else if (type === 'schooladmin') {
        sectorContainer.style.display = 'block';
        schoolContainer.style.display = 'block';
        regionSelect.required = true;
        sectorSelect.required = true;
        schoolSelect.required = true;
    } else {
        sectorContainer.style.display = 'none';
        schoolContainer.style.display = 'none';
        regionSelect.required = false;
        sectorSelect.required = false;
        schoolSelect.required = false;
    }
}

function loadSectors(regionId) {
    if (!regionId) return;

    fetch(`/api/v1/regions/${regionId}/sectors`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sector_id');
            select.innerHTML = '<option value="">Seçin</option>';
            
            data.data.forEach(sector => {
                const option = document.createElement('option');
                option.value = sector.id;
                option.textContent = sector.name;
                if (sector.id == '<?php echo e(old('sector_id', $user->sector_id)); ?>') {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            // Məktəbləri yükləyək əgər seçilmiş sektor varsa
            if (select.value) {
                loadSchools(select.value);
            }
        });
}

function loadSchools(sectorId) {
    if (!sectorId) return;

    fetch(`/api/v1/sectors/${sectorId}/schools`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('school_id');
            select.innerHTML = '<option value="">Seçin</option>';
            
            data.data.forEach(school => {
                const option = document.createElement('option');
                option.value = school.id;
                option.textContent = school.name;
                if (school.id == '<?php echo e(old('school_id', $user->school_id)); ?>') {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        });
}

// Initialize form based on selected type
handleUserTypeChange(document.querySelector('select[name="user_type"]').value);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/users/edit.blade.php ENDPATH**/ ?>