<?php $__env->startSection('tab-content'); ?>
<div class="row">
   <div class="col-12 mb-4">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Sektorlar</h5>
               <div>
                   <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#sectorAdminModal">
                       <i class="fas fa-user-plus"></i> Sektor admini əlavə et
                   </button>
                   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectorModal">
                       <i class="fas fa-plus"></i> Sektor əlavə et
                   </button>
               </div>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Region</th>
                               <th>Sektor adı</th>
                               <th>Telefon</th>
                               <th>Məktəb sayı</th>
                               <th>Admin</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <tr>
                               <td><?php echo e($sector->region->name); ?></td>
                               <td><?php echo e($sector->name); ?></td>
                               <td><?php echo e($sector->phone); ?></td>
                               <td><?php echo e($sector->schools_count); ?></td>
                               <!-- Admin hissəsinə əlavələr -->
                                <td>
                                    <?php if($sector->admin): ?>
                                        <div class="d-flex align-items-center">
                                           <?php echo e($sector->admin->full_name); ?>

                                            <span class="badge bg-success ms-2">Aktiv</span>
                                        </div>
                                        <small class="text-muted"><?php echo e($sector->admin->email); ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Admin təyin edilməyib</span>
                                    <?php endif; ?>
                                </td>
                               <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editSector(<?php echo e($sector->id); ?>)"
                                                type="button">
                                            <i class="fas fa-edit"></i>
                                        </button>
        
        <!-- Yeni admin təyinat düyməsi -->
                                        <button class="btn btn-sm btn-outline-warning assign-admin-btn" 
                                            data-sector-id="<?php echo e($sector->id); ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#sectorAdminModal">
                                            <i class="fas fa-user-plus"></i>
                                        </button>

                                        <?php if($sector->schools_count == 0): ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteSector(<?php echo e($sector->id); ?>)">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pages.settings.personal.modals.sector-admin-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('pages.settings.personal.modals.sector-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('pages.settings.personal.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/sectors/index.blade.php ENDPATH**/ ?>