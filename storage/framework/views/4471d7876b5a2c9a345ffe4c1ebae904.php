<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo e(request()->is('*/regions*') ? 'active' : ''); ?>" 
               href="<?php echo e(route('settings.personal.regions.index')); ?>">
                <i class="fas fa-globe"></i> Regionlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e(request()->is('*/sectors*') ? 'active' : ''); ?>" 
               href="<?php echo e(route('settings.personal.sectors.index')); ?>">
                <i class="fas fa-building"></i> Sektorlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e(request()->is('*/schools*') ? 'active' : ''); ?>" 
               href="<?php echo e(route('settings.personal.schools.index')); ?>">
                <i class="fas fa-school"></i> Məktəblər
            </a>
        </li>
    </ul>

    
    <div class="tab-content">
        <?php echo $__env->yieldContent('tab-content'); ?>
    </div>
</div>

<?php echo $__env->make('pages.settings.personal.modals.region-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/index.blade.php ENDPATH**/ ?>