<div class="btn-group">
    <a href="<?php echo e(route('settings.personal.schools.edit', $school)); ?>" class="btn btn-sm btn-primary" title="Redaktə et">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger delete-school" data-id="<?php echo e($school->id); ?>" title="Sil">
        <i class="fas fa-trash"></i>
    </button>
    <button type="button" class="btn btn-sm btn-info assign-admin" data-id="<?php echo e($school->id); ?>" data-bs-toggle="modal" data-bs-target="#assignAdminModal" title="Admin təyin et">
        <i class="fas fa-user-shield"></i>
    </button>
    <a href="<?php echo e(route('settings.personal.schools.show', $school)); ?>" class="btn btn-sm btn-success" title="Ətraflı bax">
        <i class="fas fa-eye"></i>
    </a>
</div>
<?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/partials/actions.blade.php ENDPATH**/ ?>