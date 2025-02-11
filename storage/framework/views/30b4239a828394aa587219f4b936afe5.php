<?php $__env->startSection('tab-content'); ?>

<div class="alert alert-info mb-4">
   <h5><i class="fas fa-info-circle"></i> Excel ilə import qaydaları:</h5>
   <ul class="mb-0">
       <li>Şablonu yükləyin və düzgün doldurun</li>
       <li>Bütün məcburi xanaları doldurun (Məktəb adı, Sektor, UTIS kod)</li>
       <li>Şablonda olan formatı pozmayın</li>
       <li>Təkrarlanan məlumatlar qəbul edilmir</li>
   </ul>
</div>

<div class="row mb-4">
   <div class="col-md-6">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Məktəblər</h5>
               <div>
                   <a href="<?php echo e(route('settings.personal.schools.template')); ?>" class="btn btn-sm btn-outline-primary">
                       <i class="fas fa-download"></i> Excel şablonu
                   </a>
                   <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                       <i class="fas fa-upload"></i> Excel import
                   </button>
                   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal">
                       <i class="fas fa-plus"></i> Məktəb əlavə et
                   </button>
               </div>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>UTIS kod</th>
                               <th>Məktəb adı</th>
                               <th>Sektor</th>
                               <th>Telefon</th>
                               <th>Email</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <tr>
                               <td><?php echo e($school->utis_code); ?></td>
                               <td><?php echo e($school->name); ?></td>
                               <td><?php echo e($school->sector->name); ?></td>
                               <td><?php echo e($school->phone); ?></td>
                               <td><?php echo e($school->email); ?></td>
                               <td>
                                   <div class="btn-group">
                                       <button class="btn btn-sm btn-outline-primary" onclick="editSchool(<?php echo e($school->id); ?>)">
                                           <i class="fas fa-edit"></i>
                                       </button>
                                       <?php if(!$school->hasAdmins()): ?>
                                       <button class="btn btn-sm btn-outline-danger" onclick="deleteSchool(<?php echo e($school->id); ?>)">
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

   <div class="col-md-6">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Məktəb Adminləri</h5>
               <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#schoolAdminModal">
                   <i class="fas fa-user-plus"></i> Admin əlavə et
               </button>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Ad Soyad</th>
                               <th>Email</th>
                               <th>UTIS kod</th>
                               <th>Məktəb</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php $__currentLoopData = $schoolAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <tr>
                               <td><?php echo e($admin->name); ?></td>
                               <td><?php echo e($admin->email); ?></td>
                               <td><?php echo e($admin->utis_code); ?></td>
                               <td><?php echo e($admin->school->name); ?></td>
                               <td>
                                   <button class="btn btn-sm btn-outline-primary" onclick="editAdmin(<?php echo e($admin->id); ?>)">
                                       <i class="fas fa-edit"></i>
                                   </button>
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
<?php echo $__env->make('pages.settings.personal.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/index.blade.php ENDPATH**/ ?>