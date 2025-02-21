<?php $__env->startSection('title', 'Region Redaktə Et'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Region Redaktə Et</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('settings.personal.regions.index')); ?>" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="regionForm" data-region-id="<?php echo e($region->id); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div id="regionFormErrors"></div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Region Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo e($region->name); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Region Kodu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo e($region->code); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($region->phone); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Təsvir</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo e($region->description); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Yadda Saxla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Region Admin Modal -->
    <div class="modal fade" id="regionAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Region Admini Təyin Et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="regionAdminForm">
                        <?php echo csrf_field(); ?>
                        <div id="adminFormErrors"></div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="admin_phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="send_credentials" name="send_credentials">
                                <label class="form-check-label" for="send_credentials">
                                    Giriş məlumatlarını email ilə göndər
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                    <button type="submit" class="btn btn-primary" form="regionAdminForm">Təyin Et</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script src="<?php echo e(asset('js/settings/region.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/regions/edit.blade.php ENDPATH**/ ?>