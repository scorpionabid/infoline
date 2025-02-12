<?php $__env->startSection('tab-content'); ?>
<div class="row">
   <div class="col-12 mb-4">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Regionlar</h5>
               <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#regionModal">
                   <i class="fas fa-plus"></i> Region əlavə et
               </button>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Region adı</th>
                               <th>Telefon</th>
                               <th>Sektor sayı</th>
                               <th>Məktəb sayı</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($region->name); ?></td>
                                <td><?php echo e($region->phone ?? 'Qeyd edilməyib'); ?></td>
                                <td><?php echo e($region->sectors_count); ?></td>
                                <td><?php echo e($region->schools_count); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary btn-edit-region" 
                                                data-id="<?php echo e($region->id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if($region->sectors_count == 0): ?>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-region" 
                                                data-id="<?php echo e($region->id); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>

<!-- Region Modal -->
<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Global config
    const regionConfig = {
        urls: {
            edit: "<?php echo e(route('settings.personal.regions.edit', ':id')); ?>",
            update: "<?php echo e(route('settings.personal.regions.update', ':id')); ?>",
            delete: "<?php echo e(route('settings.personal.regions.destroy', ':id')); ?>"
        }
    };
</script>
<script src="<?php echo e(asset('js/settings/regions.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("pages.settings.personal.index", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/regions/index.blade.php ENDPATH**/ ?>