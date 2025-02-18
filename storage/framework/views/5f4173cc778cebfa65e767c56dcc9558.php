<!DOCTYPE html>
<html lang="az">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <title><?php echo e(config('app.name', 'İnfoLine')); ?></title>

   <!-- Vendor CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
   <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
   <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
   <?php if(auth()->guard()->check()): ?>
       <?php echo $__env->make('partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   <?php endif; ?>

   <div class="container-fluid mt-3">
       <?php echo $__env->yieldContent('content'); ?>
   </div>

   <!-- Core JS -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

   <!-- Global Config -->
   <script>
       window.appConfig = {
           baseUrl: '<?php echo e(url('/')); ?>',
           csrfToken: '<?php echo e(csrf_token()); ?>'
       };

       $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': window.appConfig.csrfToken
           }
       });
   </script>

   <!-- App JS -->
   <script src="<?php echo e(asset('js/app.js')); ?>"></script>
   
   <!-- Settings JS -->
   <script src="<?php echo e(asset('js/settings/table.js')); ?>"></script>
   <script src="<?php echo e(asset('js/settings/regions.js')); ?>"></script>
   <script src="<?php echo e(asset('js/settings/sector.js')); ?>"></script>
   
   <?php echo $__env->yieldPushContent('scripts'); ?>

   <!-- Flash Messages -->
   <?php if(session('success') || session('error')): ?>
       <script>
           Swal.fire({
               icon: '<?php echo e(session('success') ? 'success' : 'error'); ?>',
               title: '<?php echo e(session('success') ? 'Uğurlu!' : 'Xəta!'); ?>',
               text: '<?php echo e(session('success') ?? session('error')); ?>',
               timer: 3000
           });
       </script>
   <?php endif; ?>
</body>
</html><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/layouts/app.blade.php ENDPATH**/ ?>